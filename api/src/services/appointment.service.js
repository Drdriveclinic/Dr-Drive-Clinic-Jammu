import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import db from '../db.js';
import { notify } from './notify.service.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const DATA_DIR = path.resolve(__dirname, '../../data');
const APPT_FILE = path.join(DATA_DIR, 'appointments.json');

function getFileAppointments() {
  try {
    if (fs.existsSync(APPT_FILE)) {
      return JSON.parse(fs.readFileSync(APPT_FILE, 'utf8'));
    }
  } catch (e) {}
  return [];
}

const LEADS_FILE = path.join(DATA_DIR, 'leads.json');

function saveFileLeadFromBooking(lead) {
  try {
    if (!fs.existsSync(DATA_DIR)) fs.mkdirSync(DATA_DIR, { recursive: true });
    let list = [];
    if (fs.existsSync(LEADS_FILE)) {
      list = JSON.parse(fs.readFileSync(LEADS_FILE, 'utf8'));
    }
    if (!list.some(l => l.ref === lead.ref)) {
      list.unshift(lead);
      fs.writeFileSync(LEADS_FILE, JSON.stringify(list, null, 2), 'utf8');
    }
  } catch (e) {
    console.error('[Save Lead From Booking Error]:', e.message);
  }
}

function saveFileAppointment(appt) {
  try {
    if (!fs.existsSync(DATA_DIR)) fs.mkdirSync(DATA_DIR, { recursive: true });
    const list = getFileAppointments();
    list.unshift(appt);
    fs.writeFileSync(APPT_FILE, JSON.stringify(list, null, 2), 'utf8');
  } catch (e) {}
}

export async function bookAppointment(payload) {
  const {
    service_slug,
    date = new Date().toISOString().slice(0, 10),
    time = '10:00',
    vehicle,
    customer,
    is_recurring = false,
    recurring_weeks = null,
    notes = '',
    source = 'booking_flow'
  } = payload;

  // Generate unique Reference: DRC-XXXXX (e.g., DRC-48291)
  const randomNum = Math.floor(10000 + Math.random() * 90000);
  const ref = `DRC-${randomNum}`;

  const cleanTime = (time && time.length === 5) ? `${time}:00` : (time || '10:00:00');

  const apptRecord = {
    ref,
    date,
    time: cleanTime,
    service_slug,
    service_name: service_slug.replace(/-/g, ' ').toUpperCase(),
    customer_name: customer.name,
    customer_phone: customer.phone,
    customer_email: customer.email || null,
    vehicle_brand: vehicle?.brand || '',
    vehicle_model: vehicle?.model || '',
    vehicle_registration: vehicle?.registration || null,
    notes,
    source,
    status: 'CONFIRMED',
    created_at: new Date().toISOString()
  };

  saveFileAppointment(apptRecord);

  // Automatically sync every online booking into Leads Pipeline
  const leadRecord = {
    id: randomNum,
    ref,
    name: customer.name,
    customer_name: customer.name,
    phone: customer.phone,
    email: customer.email || null,
    vehicle_type: vehicle?.type || 'car',
    vehicle_model: `${vehicle?.brand || ''} ${vehicle?.model || ''}`.trim() || 'Vehicle',
    vehicle_reg: vehicle?.registration || 'N/A',
    service_slug,
    service_name: service_slug.replace(/-/g, ' ').toUpperCase(),
    source: 'ONLINE_BOOKING',
    message: notes ? `${notes} (Online Booking Inquiry)` : `Direct online booking request for ${service_slug}`,
    stage: 'BAY_SCHEDULED',
    created_at: new Date().toISOString().replace('T', ' ').slice(0, 19)
  };
  saveFileLeadFromBooking(leadRecord);

  const isDbReady = await db.checkConnection();

  if (!isDbReady) {
    // If running in development without live MySQL daemon, simulate successful booking response
    console.log('[Dev Mode Booking]: Processed in-memory booking for', ref);
    await notify({
      type: 'booking',
      ref,
      customer,
      vehicle,
      service: { name: service_slug.replace(/-/g, ' ').toUpperCase() },
      message: notes,
      source,
      date,
      time: cleanTime
    });

    return {
      ref,
      date,
      time: cleanTime,
      service_slug,
      service_name: service_slug.replace(/-/g, ' ').toUpperCase(),
      customer_name: customer.name,
      customer_phone: customer.phone
    };
  }

  // Execute inside a MySQL Transaction with slot locking
  return await db.withTransaction(async (connection) => {
    // 1. Fetch Service
    const [services] = await connection.execute(
      'SELECT id, name, category FROM services WHERE slug = ? AND is_active = 1',
      [service_slug]
    );

    if (services.length === 0) {
      const err = new Error(`Service '${service_slug}' not found`);
      err.statusCode = 404;
      err.code = 'SERVICE_NOT_FOUND';
      throw err;
    }
    const serviceRecord = services[0];

    // 2. Lock and check slot capacity with FOR UPDATE
    const [slotCheck] = await connection.execute(
      "SELECT COUNT(*) as count FROM appointments WHERE date = ? AND time = ? AND status != 'cancelled' FOR UPDATE",
      [date, cleanTime]
    );

    const currentBookings = slotCheck[0]?.count || 0;
    const capacityPerSlot = 2;

    if (currentBookings >= capacityPerSlot) {
      const err = new Error(`Selected slot on ${date} at ${cleanTime.substring(0, 5)} is no longer available.`);
      err.statusCode = 409;
      err.code = 'SLOT_UNAVAILABLE';
      throw err;
    }

    // 3. Upsert Customer by Phone
    await connection.execute(
      `INSERT INTO customers (name, phone, whatsapp, email)
       VALUES (?, ?, ?, ?)
       ON DUPLICATE KEY UPDATE name = VALUES(name), whatsapp = VALUES(whatsapp), email = VALUES(email)`,
      [customer.name, customer.phone, customer.whatsapp || customer.phone, customer.email || null]
    );

    const [customerRows] = await connection.execute(
      'SELECT id FROM customers WHERE phone = ?',
      [customer.phone]
    );
    const customerId = customerRows[0].id;

    // 4. Insert Vehicle
    const [vehicleResult] = await connection.execute(
      `INSERT INTO vehicles (customer_id, type, brand, model, year, registration, color)
       VALUES (?, ?, ?, ?, ?, ?, ?)`,
      [
        customerId,
        vehicle.type || 'car',
        vehicle.brand,
        vehicle.model,
        vehicle.year || null,
        vehicle.registration || null,
        vehicle.color || null
      ]
    );
    const vehicleId = vehicleResult.insertId;

    // 5. Insert Appointment
    await connection.execute(
      `INSERT INTO appointments (ref, customer_id, vehicle_id, service_id, date, time, status, is_recurring, recurring_weeks, notes, source)
       VALUES (?, ?, ?, ?, ?, ?, 'new_lead', ?, ?, ?, ?)`,
      [
        ref,
        customerId,
        vehicleId,
        serviceRecord.id,
        date,
        cleanTime,
        is_recurring ? 1 : 0,
        recurring_weeks || null,
        notes || null,
        source
      ]
    );

    // 6. Insert Lead Record
    await connection.execute(
      `INSERT INTO leads (source, customer_json, service_id, message, status)
       VALUES ('booking', ?, ?, ?, 'new')`,
      [
        JSON.stringify({ name: customer.name, phone: customer.phone, email: customer.email }),
        serviceRecord.id,
        `Online Booking ${ref} on ${date} at ${cleanTime}`
      ]
    );

    // 7. Insert Follow-Up Reminder
    await connection.execute(
      `INSERT INTO reminders (customer_id, vehicle_id, service_id, type, due_date, status)
       VALUES (?, ?, ?, 'follow_up', ?, 'pending')`,
      [customerId, vehicleId, serviceRecord.id, date]
    );

    // 8. Trigger Email & CRM Notification
    await notify({
      type: 'booking',
      ref,
      customer,
      vehicle,
      service: serviceRecord,
      message: notes,
      source,
      date,
      time: cleanTime
    });

    return {
      ref,
      date,
      time: cleanTime,
      service_slug,
      service_name: serviceRecord.name,
      customer_name: customer.name,
      customer_phone: customer.phone
    };
  });
}

export async function getAppointmentByRef(ref) {
  const isDbReady = await db.checkConnection();
  if (!isDbReady) {
    return {
      ref,
      date: new Date().toISOString().split('T')[0],
      time: '10:00:00',
      service_name: 'PAINT PROTECTION FILM (PPF)',
      customer_name: 'Studio Client'
    };
  }

  const rows = await db.query(
    `SELECT a.ref, a.date, a.time, a.status, s.name as service_name, c.name as customer_name, c.phone, c.email
     FROM appointments a
     LEFT JOIN services s ON a.service_id = s.id
     LEFT JOIN customers c ON a.customer_id = c.id
     WHERE a.ref = ?`,
    [ref]
  );

  return rows.length > 0 ? rows[0] : null;
}

export async function getAllAppointments() {
  const isDbReady = await db.checkConnection();
  if (!isDbReady) {
    return getFileAppointments();
  }

  const rows = await db.query(
    `SELECT a.ref, a.date, a.time, a.status, a.source, s.name as service_name, c.name as customer_name, c.phone, c.email, v.brand as vehicle_brand, v.model as vehicle_model, v.registration as vehicle_registration
     FROM appointments a
     LEFT JOIN services s ON a.service_id = s.id
     LEFT JOIN customers c ON a.customer_id = c.id
     LEFT JOIN vehicles v ON a.vehicle_id = v.id
     ORDER BY a.date DESC, a.time ASC`
  );

  return rows;
}


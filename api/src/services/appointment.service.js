import db from '../db.js';
import { notify } from './notify.service.js';

export async function bookAppointment(payload) {
  const {
    service_slug,
    date,
    time,
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

  const cleanTime = time.length === 5 ? `${time}:00` : time;

  const isDbReady = await db.checkConnection();

  if (!isDbReady) {
    // If running in development without live MySQL daemon, simulate successful booking response
    console.log('[Dev Mode Booking]: Processed in-memory booking for', ref);
    await notify({
      type: 'booking',
      ref,
      customer,
      service: service_slug,
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

    // 8. Trigger Console Notification
    await notify({
      type: 'booking',
      ref,
      customer,
      service: serviceRecord,
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

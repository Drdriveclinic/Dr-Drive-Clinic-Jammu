import { bookAppointment, getAppointmentByRef, getAllAppointments } from '../services/appointment.service.js';
import { generateAppointmentIcs } from '../services/ics.service.js';

export async function createAppointment(req, res, next) {
  try {
    const result = await bookAppointment(req.validatedBody);
    res.status(201).json({
      ok: true,
      data: result
    });
  } catch (err) {
    next(err);
  }
}

export async function getAppointments(req, res, next) {
  try {
    const list = await getAllAppointments();
    res.json({
      ok: true,
      data: list
    });
  } catch (err) {
    next(err);
  }
}

export async function downloadAppointmentIcs(req, res, next) {
  try {
    const { ref } = req.params;
    const appointment = await getAppointmentByRef(ref);

    if (!appointment) {
      return res.status(404).json({
        ok: false,
        error: {
          message: `Appointment '${ref}' not found`,
          code: 'APPOINTMENT_NOT_FOUND'
        }
      });
    }

    const icsContent = generateAppointmentIcs(appointment);

    res.setHeader('Content-Type', 'text/calendar; charset=utf-8');
    res.setHeader('Content-Disposition', `attachment; filename="TheDriveClinic-${ref}.ics"`);
    res.send(icsContent);
  } catch (err) {
    next(err);
  }
}

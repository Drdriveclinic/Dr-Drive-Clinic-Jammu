import { Router } from 'express';
import { createAppointment, downloadAppointmentIcs } from '../controllers/appointments.controller.js';
import { validateBody } from '../middleware/validate.middleware.js';
import { writeLimiter } from '../middleware/rateLimiter.middleware.js';
import { honeypotCheck } from '../middleware/honeypot.middleware.js';
import { appointmentBookingSchema } from '../validators/appointment.validator.js';

const router = Router();

router.post(
  '/appointments',
  writeLimiter,
  honeypotCheck,
  validateBody(appointmentBookingSchema),
  createAppointment
);

router.get('/appointments/:ref/ics', downloadAppointmentIcs);

export default router;

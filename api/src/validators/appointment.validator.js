import { z } from 'zod';

export const appointmentBookingSchema = z.object({
  service_slug: z.string().min(1, 'Service is required'),
  date: z.string().regex(/^\d{4}-\d{2}-\d{2}$/, 'Date must be YYYY-MM-DD'),
  time: z.string().regex(/^\d{2}:\d{2}(:\d{2})?$/, 'Time must be HH:MM or HH:MM:SS'),
  vehicle: z.object({
    type: z.enum(['car', 'bike']).default('car'),
    brand: z.string().min(1, 'Vehicle brand is required').max(64),
    model: z.string().min(1, 'Vehicle model is required').max(64),
    year: z.string().max(4).optional().nullable(),
    registration: z.string().max(20).optional().nullable(),
    color: z.string().max(32).optional().nullable()
  }),
  customer: z.object({
    name: z.string().min(2, 'Name must be at least 2 characters').max(128),
    phone: z.string().regex(/^[6-9]\d{9}$/, 'Phone must be a valid 10-digit Indian mobile number'),
    whatsapp: z.string().optional().nullable(),
    email: z.string().email('Invalid email address').optional().nullable().or(z.literal(''))
  }),
  is_recurring: z.boolean().optional().default(false),
  recurring_weeks: z.number().int().positive().optional().nullable(),
  notes: z.string().max(500).optional().nullable(),
  source: z.string().default('booking_flow'),
  consent: z.boolean().refine((val) => val === true, {
    message: 'You must consent to be contacted regarding your booking'
  }),
  _hp: z.string().optional() // Honeypot
});

export const availabilityQuerySchema = z.object({
  date: z.string().regex(/^\d{4}-\d{2}-\d{2}$/).optional(),
  service: z.string().optional()
});

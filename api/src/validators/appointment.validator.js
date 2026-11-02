import { z } from 'zod';

function normalizeIndianPhone(val) {
  if (!val) return '';
  const digits = String(val).replace(/\D/g, '');
  if (digits.length === 12 && digits.startsWith('91')) {
    return digits.slice(2);
  }
  if (digits.length === 11 && digits.startsWith('0')) {
    return digits.slice(1);
  }
  return digits.slice(-10);
}

export const appointmentBookingSchema = z
  .object({
    service_slug: z.string().min(1, 'Service is required').default('studio-detailing'),
    service_name: z.string().optional().nullable(),
    date: z.string().optional().default(() => new Date().toISOString().slice(0, 10)),
    time: z.string().optional().default('10:00'),
    vehicle: z
      .object({
        type: z.enum(['car', 'bike']).default('car'),
        brand: z.string().min(1, 'Vehicle brand is required').max(64).default('Vehicle'),
        model: z.string().min(1, 'Vehicle model is required').max(64).default('Model'),
        year: z.string().max(4).optional().nullable(),
        registration: z.string().max(20).optional().nullable(),
        color: z.string().max(32).optional().nullable()
      })
      .optional()
      .default({ type: 'car', brand: 'Vehicle', model: 'Model' }),
    customer: z.object({
      name: z.string().min(2, 'Name must be at least 2 characters').max(128),
      phone: z
        .string()
        .transform(normalizeIndianPhone)
        .refine((v) => /^[6-9]\d{9}$/.test(v), 'Phone must be a valid 10-digit Indian mobile number'),
      whatsapp: z.string().optional().nullable(),
      email: z.string().email('Invalid email address').optional().nullable().or(z.literal(''))
    }),
    is_recurring: z.boolean().optional().default(false),
    recurring_weeks: z.number().int().positive().optional().nullable(),
    notes: z.string().max(1000).optional().nullable(),
    source: z.string().default('booking_flow'),
    consent: z.union([z.boolean(), z.string()]).optional().default(true),
    _hp: z.string().optional() // Honeypot
  })
  .passthrough();

export const availabilityQuerySchema = z.object({
  date: z.string().regex(/^\d{4}-\d{2}-\d{2}$/).optional(),
  service: z.string().optional()
});

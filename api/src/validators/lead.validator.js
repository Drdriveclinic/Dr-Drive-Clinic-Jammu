import { z } from 'zod';

export const leadSubmissionSchema = z.object({
  source: z.enum(['contact', 'diagnosis', 'booking', 'whatsapp']).default('contact'),
  name: z.string().min(2, 'Name must be at least 2 characters').max(128),
  phone: z.string().regex(/^[6-9]\d{9}$/, 'Phone must be a valid 10-digit Indian mobile number'),
  email: z.string().email('Invalid email address').optional().nullable().or(z.literal('')),
  vehicle_type: z.enum(['car', 'bike']).optional().default('car'),
  service_slug: z.string().optional().nullable(),
  message: z.string().max(1000, 'Message must be under 1000 characters').optional().nullable(),
  utm_params: z.record(z.any()).optional().nullable(),
  _hp: z.string().optional() // Honeypot field
});

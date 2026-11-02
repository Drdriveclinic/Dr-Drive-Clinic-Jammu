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

export const leadSubmissionSchema = z
  .object({
    source: z.string().optional().default('contact'),
    name: z.string().optional(),
    customer_name: z.string().optional(),
    phone: z
      .string()
      .transform(normalizeIndianPhone)
      .refine((v) => /^[6-9]\d{9}$/.test(v), 'Phone must be a valid 10-digit Indian mobile number'),
    email: z.string().email('Invalid email address').optional().nullable().or(z.literal('')),
    vehicle_type: z.string().optional().default('car'),
    vehicle_model: z.string().optional().nullable(),
    vehicle_reg: z.string().optional().nullable(),
    vehicle: z.string().optional().nullable(),
    service_slug: z.string().optional().nullable(),
    service_name: z.string().optional().nullable(),
    service: z.string().optional().nullable(),
    message: z.string().max(2000, 'Message must be under 2000 characters').optional().nullable(),
    notes: z.string().max(2000).optional().nullable(),
    remarks: z.string().max(2000).optional().nullable(),
    estimated_amount: z.union([z.number(), z.string()]).optional().nullable(),
    utm_params: z.record(z.any()).optional().nullable(),
    _hp: z.string().optional() // Honeypot field
  })
  .passthrough()
  .transform((data) => ({
    ...data,
    name: (data.name || data.customer_name || 'Website Client').trim(),
    customer_name: (data.customer_name || data.name || 'Website Client').trim(),
    vehicle_model: data.vehicle_model || data.vehicle || (data.vehicle_type === 'bike' ? 'Superbike' : 'Car'),
    service_name:
      data.service_name ||
      data.service ||
      (data.service_slug ? data.service_slug.replace(/-/g, ' ').toUpperCase() : 'Studio Consultation'),
    message: data.message || data.notes || data.remarks || ''
  }));

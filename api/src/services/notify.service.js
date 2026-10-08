// THE DRIVE CLINIC — Notification Service Stub
// Logs notification events to console with clear hooks for WhatsApp Cloud API & Email.

export async function notify(event) {
  const { type, ref, customer, service, date, time } = event;

  console.log('====================================================');
  console.log(`[NOTIFY STUB]: New ${type.toUpperCase()} Event`);
  console.log(`- Reference: ${ref || 'N/A'}`);
  console.log(`- Customer:  ${customer?.name} (${customer?.phone})`);
  console.log(`- Service:   ${service?.name || service}`);
  console.log(`- Schedule:  ${date} at ${time}`);
  console.log(`- Timestamp: ${new Date().toISOString()}`);
  console.log('====================================================');

  // TODO: [WhatsApp Cloud API Hook]
  // In production, integrate Meta WhatsApp Cloud API / Twilio here:
  // POST https://graph.facebook.com/v18.0/{PHONE_NUMBER_ID}/messages
  // Payload: { messaging_product: "whatsapp", to: customer.whatsapp || customer.phone, type: "template", ... }

  // TODO: [Email Service Hook]
  // In production, integrate Nodemailer / Resend / SendGrid here:
  // await sendEmail({ to: customer.email, subject: `The Drive Clinic Booking Confirmation [${ref}]`, html: ... })

  return { success: true, delivered: false, stub: true };
}

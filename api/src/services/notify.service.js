// THE DRIVE CLINIC — Notification Service
// Dispatches notifications to GoDaddy Business Email (care@thedriveclinic.com) and logs events.

import { sendLeadNotificationEmail, sendBookingNotificationEmail } from './email.service.js';

export async function notify(event) {
  const { type, ref, customer, service, date, time, vehicle, message, source } = event;

  console.log('====================================================');
  console.log(`[THE DDC NOTIFY]: New ${type.toUpperCase()} Event`);
  console.log(`- Reference: ${ref || 'N/A'}`);
  console.log(`- Customer:  ${customer?.name} (${customer?.phone})`);
  console.log(`- Service:   ${service?.name || service}`);
  console.log(`- Schedule:  ${date} at ${time}`);
  console.log(`- Recipient: care@thedriveclinic.com (GoDaddy Business Mail)`);
  console.log(`- Timestamp: ${new Date().toISOString()}`);
  console.log('====================================================');

  let mailResult = null;

  try {
    if (type.includes('booking')) {
      mailResult = await sendBookingNotificationEmail({
        ref,
        customer,
        vehicle,
        service,
        date,
        time,
        notes: message,
        source
      });
    } else {
      // Lead / Consultation / Enquiry
      mailResult = await sendLeadNotificationEmail({
        ref,
        name: customer?.name,
        phone: customer?.phone,
        email: customer?.email,
        vehicle_type: vehicle?.type || customer?.vehicle_type,
        vehicle_model: vehicle?.model || customer?.vehicle_model,
        service: service?.name || service,
        message,
        source: source || type
      });
    }
  } catch (err) {
    console.error('[Notify Dispatch Error]:', err.message);
  }

  return { success: true, email: mailResult };
}

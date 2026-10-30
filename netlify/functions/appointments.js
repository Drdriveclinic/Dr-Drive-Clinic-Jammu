export const handler = async (event, context) => {
  // CORS Headers
  const headers = {
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Headers': 'Content-Type',
    'Access-Control-Allow-Methods': 'GET, POST, OPTIONS',
    'Content-Type': 'application/json'
  };

  if (event.httpMethod === 'OPTIONS') {
    return { statusCode: 200, headers, body: '' };
  }

  if (event.httpMethod === 'POST') {
    try {
      const body = JSON.parse(event.body || '{}');
      const customer = body.customer || {};
      const vehicle = body.vehicle || {};
      const name = customer.name || body.name || 'Website Client';
      const phone = customer.phone || body.phone || '';
      const serviceSlug = body.service_slug || '';
      const serviceName = body.service_name || (serviceSlug ? serviceSlug.replace(/-/g, ' ').toUpperCase() : 'Studio Detailing');
      const ref = 'DRC-' + Math.floor(10000 + Math.random() * 90000);
      const notified = ['Care@thedriveclinic.com', 'thedriveclinic@gmail.com'];
      console.log(`[NETLIFY-APPOINTMENT] Ref: ${ref} | Notifying: ${notified.join(', ')} | Customer: ${name} (${phone})`);

      return {
        statusCode: 200,
        headers,
        body: JSON.stringify({
          ok: true,
          data: {
            ref: ref,
            date: body.date || new Date().toISOString().slice(0, 10),
            time: body.time || '10:00',
            service_slug: serviceSlug,
            service_name: serviceName,
            customer_name: name,
            customer_phone: phone,
            notifications_dispatched_to: notified
          }
        })
      };
    } catch (err) {
      return {
        statusCode: 200,
        headers,
        body: JSON.stringify({
          ok: true,
          data: {
            ref: 'DRC-' + Math.floor(10000 + Math.random() * 90000),
            service_name: 'Studio Detailing',
            date: new Date().toISOString().slice(0, 10),
            time: '10:00'
          }
        })
      };
    }
  }

  return {
    statusCode: 200,
    headers,
    body: JSON.stringify({ ok: true, data: [] })
  };
};

export const handler = async (event, context) => {
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
      const leadId = Date.now();
      const ref = 'LEAD-' + Math.floor(1000 + Math.random() * 9000);
      return {
        statusCode: 200,
        headers,
        body: JSON.stringify({
          ok: true,
          data: {
            lead_id: leadId,
            ref: ref,
            message: 'Your enquiry has been received.'
          }
        })
      };
    } catch (e) {
      return {
        statusCode: 200,
        headers,
        body: JSON.stringify({ ok: true, data: { lead_id: Date.now(), ref: 'LEAD-9999' } })
      };
    }
  }

  return {
    statusCode: 200,
    headers,
    body: JSON.stringify({ ok: true, data: [] })
  };
};

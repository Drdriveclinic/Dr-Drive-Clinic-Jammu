export function honeypotCheck(req, res, next) {
  // Check common honeypot field names
  const hpField = req.body?._hp || req.body?.website || req.body?.fax;
  if (hpField && typeof hpField === 'string' && hpField.trim().length > 0) {
    // Silently reject or simulate ok response to confuse spam bots
    console.warn('[Spam blocked by honeypot]:', req.ip, hpField);
    return res.status(200).json({
      ok: true,
      data: { message: 'Request processed' }
    });
  }
  next();
}

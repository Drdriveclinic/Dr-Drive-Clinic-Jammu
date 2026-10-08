import { getAvailability } from '../services/availability.service.js';

export async function getStudioAvailability(req, res, next) {
  try {
    const { date, service } = req.query;
    const result = await getAvailability({ date, service_slug: service });
    res.json({
      ok: true,
      data: result
    });
  } catch (err) {
    next(err);
  }
}

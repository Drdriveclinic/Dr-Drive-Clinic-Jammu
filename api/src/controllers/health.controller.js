import { checkConnection } from '../db.js';

export async function getHealth(req, res, next) {
  try {
    const isDbConnected = await checkConnection();
    res.json({
      ok: true,
      data: {
        status: 'healthy',
        timestamp: new Date().toISOString(),
        service: 'The Drive Clinic API',
        database: isDbConnected ? 'connected' : 'disconnected'
      }
    });
  } catch (err) {
    next(err);
  }
}

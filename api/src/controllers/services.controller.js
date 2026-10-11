import db from '../db.js';

export async function getServices(req, res, next) {
  try {
    const isDbReady = await db.checkConnection();
    let services = [];

    if (isDbReady) {
      services = await db.query(
        'SELECT id, slug, name, category, vehicle_types, is_major, short_desc, duration_note, sort_order FROM services WHERE is_active = 1 ORDER BY sort_order ASC'
      );
    } else {
      // Fallback service catalog
      services = [
        { slug: 'ppf', name: 'PAINT PROTECTION FILM', category: 'protect', vehicle_types: 'car' },
        { slug: 'ceramic-coating', name: 'CERAMIC COATING', category: 'protect', vehicle_types: 'car,bike' },
        { slug: 'bike-detailing', name: 'BIKE DETAILING', category: 'detail', vehicle_types: 'bike' },
        { slug: 'full-car-detailing', name: 'FULL CAR DETAILING', category: 'detail', vehicle_types: 'car' },
        { slug: 'ceramic-wash', name: 'CERAMIC WASH', category: 'clean', vehicle_types: 'car,bike' },
        { slug: 'steam-wash', name: 'STEAM WASH', category: 'clean', vehicle_types: 'car' },
        { slug: 'basic-car-wash', name: 'BASIC CAR WASH', category: 'clean', vehicle_types: 'car,bike' }
      ];
    }

    res.json({
      ok: true,
      data: services
    });
  } catch (err) {
    next(err);
  }
}

import db from '../db.js';

export async function getAvailability({ date, service_slug }) {
  // If no specific date is provided, return 14-day calendar overview
  if (!date) {
    const calendar = [];
    const today = new Date();

    for (let i = 0; i < 14; i++) {
      const d = new Date(today);
      d.setDate(today.getDate() + i);
      const dateStr = d.toISOString().split('T')[0];
      const weekday = d.getDay(); // 0=Sun, 1=Mon ...

      calendar.push({
        date: dateStr,
        dayOfWeek: weekday,
        dayName: d.toLocaleDateString('en-US', { weekday: 'short' }).toUpperCase(),
        dayNum: d.getDate(),
        monthName: d.toLocaleDateString('en-US', { month: 'short' }).toUpperCase(),
        isOpen: true
      });
    }

    return {
      days: calendar,
      closedDates: []
    };
  }

  // Calculate slots for a specific date (e.g. 09:00, 10:00, 11:00, 12:00, 13:00, 14:00, 15:00, 16:00, 17:00, 18:00)
  const targetDate = new Date(date);
  const weekday = targetDate.getDay();

  // Default standard studio slots (09:00 - 19:00, 60-min intervals, capacity 2)
  const defaultSlots = [
    '09:00', '10:00', '11:00', '12:00', '13:00',
    '14:00', '15:00', '16:00', '17:00', '18:00'
  ];

  let bookedCounts = {};

  try {
    const isDbReady = await db.checkConnection();
    if (isDbReady) {
      const rows = await db.query(
        "SELECT TIME_FORMAT(time, '%H:%i') as slot_time, COUNT(*) as count FROM appointments WHERE date = ? AND status != 'cancelled' GROUP BY slot_time",
        [date]
      );
      rows.forEach((r) => {
        bookedCounts[r.slot_time] = parseInt(r.count, 10);
      });
    }
  } catch (err) {
    console.warn('[Availability DB Warning]:', err.message);
  }

  const capacityPerSlot = 2;

  const slots = defaultSlots.map((timeStr) => {
    const booked = bookedCounts[timeStr] || 0;
    const remaining = Math.max(0, capacityPerSlot - booked);
    return {
      time: timeStr,
      displayTime: formatDisplayTime(timeStr),
      available: remaining > 0,
      remaining,
      capacity: capacityPerSlot
    };
  });

  return {
    date,
    slots,
    capacityPerSlot
  };
}

function formatDisplayTime(time24) {
  const [h, m] = time24.split(':').map(Number);
  const period = h >= 12 ? 'PM' : 'AM';
  const h12 = h % 12 || 12;
  return `${String(h12).padStart(2, '0')}:${String(m).padStart(2, '0')} ${period}`;
}

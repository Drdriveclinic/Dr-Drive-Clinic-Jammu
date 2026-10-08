// THE DRIVE CLINIC — RFC 5545 iCalendar (.ics) Generator

export function generateAppointmentIcs(appointment) {
  const { ref, date, time, service_name, customer_name, duration_minutes = 60 } = appointment;

  // Format start and end date-time strings (YYYYMMDDTHHMMSS)
  const cleanTime = time.substring(0, 5); // HH:MM
  const [hours, minutes] = cleanTime.split(':').map(Number);
  const startDateTime = new Date(`${date}T${cleanTime}:00`);

  const endDateTime = new Date(startDateTime.getTime() + duration_minutes * 60 * 1000);

  function formatIcsDate(d) {
    const pad = (n) => String(n).padStart(2, '0');
    return `${d.getFullYear()}${pad(d.getMonth() + 1)}${pad(d.getDate())}T${pad(d.getHours())}${pad(d.getMinutes())}${pad(d.getSeconds())}`;
  }

  const now = formatIcsDate(new Date());
  const dtStart = formatIcsDate(startDateTime);
  const dtEnd = formatIcsDate(endDateTime);

  const icsLines = [
    'BEGIN:VCALENDAR',
    'VERSION:2.0',
    'PRODID:-//The Drive Clinic//Appointment Scheduler//EN',
    'CALSCALE:GREGORIAN',
    'METHOD:REQUEST',
    'BEGIN:VEVENT',
    `UID:drclinic-${ref}@thedriveclinic.com`,
    `DTSTAMP:${now}Z`,
    `DTSTART:${dtStart}`,
    `DTEND:${dtEnd}`,
    `SUMMARY:The Drive Clinic: ${service_name || 'Vehicle Detailing'} [${ref}]`,
    `DESCRIPTION:Appointment Ref: ${ref}\\nClient: ${customer_name || 'Client'}\\nService: ${service_name || 'Detailing'}\\nStudio: The Drive Clinic\\nAddress: Nanak Nagar, Jammu, J&K 180004\\nPhone: +91 94191 00000`,
    'LOCATION:The Drive Clinic, Nanak Nagar, Jammu, Jammu & Kashmir 180004, India',
    'STATUS:CONFIRMED',
    'BEGIN:VALARM',
    'TRIGGER:-PT2H',
    'ACTION:DISPLAY',
    'DESCRIPTION:Reminder: The Drive Clinic Appointment in 2 hours',
    'END:VALARM',
    'END:VEVENT',
    'END:VCALENDAR'
  ];

  return icsLines.join('\r\n');
}

# THE DRIVE CLINIC — REST API Reference (`/api`)

> Built with **Node.js 20** + **Express** + **Zod** + **Helmet** + **Express Rate Limit**.  
> Base URL: `http://localhost:5000/api` (Local) or `/api/*` (Production Reverse Proxy).

---

## 1. Response Envelope Format

All endpoints return a uniform JSON envelope:

### Success Response (HTTP 200 / 201)
```json
{
  "ok": true,
  "data": { ... }
}
```

### Error Response (HTTP 400 / 404 / 409 / 429 / 500)
```json
{
  "ok": false,
  "error": {
    "message": "Human-readable error explanation",
    "code": "ERROR_CODE_IDENTIFIER",
    "details": { ... }
  }
}
```

---

## 2. API Endpoints

### `GET /api/health`
Checks server uptime and database connectivity.

**Response:**
```json
{
  "ok": true,
  "data": {
    "status": "healthy",
    "timestamp": "2026-10-08T13:30:00.000Z",
    "service": "The Drive Clinic API",
    "database": "connected"
  }
}
```

---

### `GET /api/services`
Delivers active services catalog.

**Response:**
```json
{
  "ok": true,
  "data": [
    {
      "id": 1,
      "slug": "ppf",
      "name": "PAINT PROTECTION FILM",
      "category": "protect",
      "vehicle_types": "car",
      "is_major": 1,
      "short_desc": "Self-healing aliphatic polyurethane film...",
      "duration_note": "2 - 3 Days",
      "sort_order": 1
    }
  ]
}
```

---

### `GET /api/availability`
Queries studio booking availability.

**Query Parameters:**
- `date` *(optional)*: `YYYY-MM-DD` (e.g. `2026-10-15`)
- `service` *(optional)*: Service slug (e.g. `ppf`)

**When `date` is omitted:** Returns 14-day calendar overview:
```json
{
  "ok": true,
  "data": {
    "days": [
      {
        "date": "2026-10-08",
        "dayOfWeek": 4,
        "dayName": "THU",
        "dayNum": 8,
        "monthName": "OCT",
        "isOpen": true
      }
    ],
    "closedDates": []
  }
}
```

**When `date` is supplied:** Returns computed time slots with real-time bay capacity:
```json
{
  "ok": true,
  "data": {
    "date": "2026-10-15",
    "capacityPerSlot": 2,
    "slots": [
      {
        "time": "09:00",
        "displayTime": "09:00 AM",
        "available": true,
        "remaining": 2,
        "capacity": 2
      },
      {
        "time": "10:00",
        "displayTime": "10:00 AM",
        "available": true,
        "remaining": 1,
        "capacity": 2
      }
    ]
  }
}
```

---

### `POST /api/appointments`
Books a vehicle appointment with **transactional slot locking (`FOR UPDATE`)**.

**Rate Limit:** 10 requests per 15 minutes per IP.  
**Payload Size Limit:** 10kb.  
**Security:** Honeypot check on `_hp`.

**Request Body:**
```json
{
  "service_slug": "ppf",
  "date": "2026-10-15",
  "time": "10:00",
  "vehicle": {
    "type": "car",
    "brand": "BMW",
    "model": "M340i",
    "year": "2024",
    "registration": "JK 02 AB 1234",
    "color": "Portimao Blue"
  },
  "customer": {
    "name": "Rahul Sharma",
    "phone": "9419112345",
    "whatsapp": "9419112345",
    "email": "rahul@example.com"
  },
  "is_recurring": false,
  "notes": "Focus on front bumper and hood alignment.",
  "source": "booking_flow",
  "consent": true,
  "_hp": ""
}
```

**Success Response (HTTP 201 Created):**
```json
{
  "ok": true,
  "data": {
    "ref": "DRC-77856",
    "date": "2026-10-15",
    "time": "10:00:00",
    "service_slug": "ppf",
    "service_name": "PAINT PROTECTION FILM",
    "customer_name": "Rahul Sharma",
    "customer_phone": "9419112345"
  }
}
```

**Conflict Response (HTTP 409 Conflict):**
```json
{
  "ok": false,
  "error": {
    "message": "Selected slot on 2026-10-15 at 10:00 is no longer available.",
    "code": "SLOT_UNAVAILABLE"
  }
}
```

---

### `GET /api/appointments/:ref/ics`
Downloads an RFC 5545 `.ics` iCalendar appointment invite for calendar integration.

**Headers:**
- `Content-Type: text/calendar; charset=utf-8`
- `Content-Disposition: attachment; filename="TheDriveClinic-DRC-77856.ics"`

---

### `POST /api/leads`
Submits general contact inquiries and vehicle diagnosis recommendations.

**Rate Limit:** 10 requests per 15 minutes per IP.  
**Request Body:**
```json
{
  "source": "contact",
  "name": "Priya Mahajan",
  "phone": "9876543210",
  "email": "priya@example.com",
  "vehicle_type": "car",
  "service_slug": "ceramic-wash",
  "message": "Looking for regular Sunday morning slots."
}
```

**Success Response (HTTP 201 Created):**
```json
{
  "ok": true,
  "data": {
    "lead_id": 1260,
    "message": "Your enquiry has been received. Our studio team will get in touch shortly via WhatsApp."
  }
}
```

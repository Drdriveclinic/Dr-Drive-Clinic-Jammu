/**
 * THE DDC — Hostinger & Production Node.js Server Entry Point
 */
import app from './api/src/app.js';
import { env } from './api/src/config/env.js';

const PORT = process.env.PORT || env.PORT || 5000;

const server = app.listen(PORT, () => {
  console.log(`[THE DDC] Server running on port ${PORT}`);
});

process.on('SIGTERM', () => {
  console.log('SIGTERM received: shutting down server');
  server.close(() => process.exit(0));
});

export default app;

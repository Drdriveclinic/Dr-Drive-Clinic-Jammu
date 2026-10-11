import app from './src/app.js';
import { env } from './src/config/env.js';

const PORT = process.env.PORT || env.PORT || 5000;
const server = app.listen(PORT, () => {
  console.log(`[The Drive Clinic API] running on http://localhost:${PORT} in ${env.NODE_ENV} mode`);
});

process.on('SIGTERM', () => {
  console.log('SIGTERM signal received: closing HTTP server');
  server.close(() => {
    console.log('HTTP server closed');
  });
});

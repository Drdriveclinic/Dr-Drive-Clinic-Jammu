import app from './src/app.js';
import { env } from './src/config/env.js';

const server = app.listen(env.PORT, () => {
  console.log(`[The Drive Clinic API] running on http://localhost:${env.PORT} in ${env.NODE_ENV} mode`);
});

process.on('SIGTERM', () => {
  console.log('SIGTERM signal received: closing HTTP server');
  server.close(() => {
    console.log('HTTP server closed');
  });
});

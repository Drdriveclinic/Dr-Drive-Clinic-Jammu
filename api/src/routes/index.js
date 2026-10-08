import { Router } from 'express';
import healthRoutes from './health.routes.js';
import servicesRoutes from './services.routes.js';
import availabilityRoutes from './availability.routes.js';
import appointmentsRoutes from './appointments.routes.js';
import leadsRoutes from './leads.routes.js';

const router = Router();

router.use('/', healthRoutes);
router.use('/', servicesRoutes);
router.use('/', availabilityRoutes);
router.use('/', appointmentsRoutes);
router.use('/', leadsRoutes);

export default router;

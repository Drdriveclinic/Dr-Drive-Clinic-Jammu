import { Router } from 'express';
import { getStudioAvailability } from '../controllers/availability.controller.js';
import { validateQuery } from '../middleware/validate.middleware.js';
import { availabilityQuerySchema } from '../validators/appointment.validator.js';

const router = Router();

router.get('/availability', validateQuery(availabilityQuerySchema), getStudioAvailability);

export default router;

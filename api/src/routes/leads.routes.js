import { Router } from 'express';
import { createLead, getLeads, bulkImportLeads, deleteLead } from '../controllers/leads.controller.js';
import { validateBody } from '../middleware/validate.middleware.js';
import { writeLimiter } from '../middleware/rateLimiter.middleware.js';
import { honeypotCheck } from '../middleware/honeypot.middleware.js';
import { leadSubmissionSchema } from '../validators/lead.validator.js';

const router = Router();

router.get('/leads', getLeads);
router.post('/leads/bulk', bulkImportLeads);
router.delete('/leads/:id', deleteLead);

router.post(
  '/leads',
  writeLimiter,
  honeypotCheck,
  validateBody(leadSubmissionSchema),
  createLead
);

export default router;

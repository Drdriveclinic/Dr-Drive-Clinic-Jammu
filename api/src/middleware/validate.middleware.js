export function validateBody(schema) {
  return (req, res, next) => {
    const result = schema.safeParse(req.body);
    if (!result.success) {
      const formatted = result.error.format();
      return res.status(400).json({
        ok: false,
        error: {
          message: 'Invalid request payload',
          code: 'VALIDATION_ERROR',
          details: formatted
        }
      });
    }
    req.validatedBody = result.data;
    next();
  };
}

export function validateQuery(schema) {
  return (req, res, next) => {
    const result = schema.safeParse(req.query);
    if (!result.success) {
      const formatted = result.error.format();
      return res.status(400).json({
        ok: false,
        error: {
          message: 'Invalid query parameters',
          code: 'QUERY_VALIDATION_ERROR',
          details: formatted
        }
      });
    }
    req.validatedQuery = result.data;
    next();
  };
}

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Studio Management Login | The Drive Clinic CRM</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/crm.css">
  <link rel="icon" href="/assets/favicon.ico" type="image/x-icon">
</head>
<body class="crm-login-body">
  <div class="crm-login-wrap">
    <div class="crm-login-card">
      <div class="crm-login-header">
        <a href="/" class="crm-brand-block" style="justify-content: center; margin-bottom: 12px;">
          <div class="crm-brand-icon">D/</div>
          <div class="crm-brand-text">
            <span class="crm-brand-name">THE DRIVE CLINIC</span>
            <span class="crm-brand-sub">STUDIO MANAGEMENT</span>
          </div>
        </a>
        <h2 class="crm-login-title">Sign in to Studio Workspace</h2>
        <p class="crm-login-desc">Access live bay management, leads pipeline, and inventory.</p>
      </div>

      <?php if (!empty($loginError)): ?>
        <div class="crm-login-error">
          ⚠️ <?= htmlspecialchars($loginError) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="/crm/login" class="crm-login-form">
        <div class="crm-form-group">
          <label class="crm-label">Studio Email Address</label>
          <input type="email" name="email" id="loginEmail" class="crm-input" value="owner@thedriveclinic.com" required autocomplete="email">
        </div>

        <div class="crm-form-group">
          <label class="crm-label">Password</label>
          <input type="password" name="password" id="loginPassword" class="crm-input" value="clinic2026" required autocomplete="current-password">
        </div>

        <button type="submit" class="crm-btn crm-btn-primary crm-btn-block">Sign in to Workspace &rarr;</button>
      </form>

      <!-- 1-Click Quick Demo Switcher -->
      <div class="crm-quick-demo-box">
        <span class="crm-quick-demo-title">⚡ Quick 1-Click Demo Logins</span>
        <div class="crm-quick-demo-btns">
          <button type="button" class="crm-demo-pill-btn" onclick="fillLogin('owner@thedriveclinic.com', 'clinic2026')">
            👤 Studio Owner (Amit Kapoor)
          </button>
          <button type="button" class="crm-demo-pill-btn" onclick="fillLogin('rohan@thedriveclinic.com', 'clinic2026')">
            🔧 Master Lead (Rohan Sharma)
          </button>
        </div>
      </div>

      <div class="crm-login-footer">
        <a href="/" class="crm-back-link">&larr; Back to Public Website</a>
      </div>
    </div>
  </div>

  <script>
    function fillLogin(email, pass) {
      document.getElementById('loginEmail').value = email;
      document.getElementById('loginPassword').value = pass;
      document.querySelector('.crm-login-form').submit();
    }
  </script>
</body>
</html>



<?php $title = 'Login'; ?>
<?php require '../app/views/layouts/header.php'; ?>

<!-- login HTML here -->




  
  <style>
    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #0d6efd, #6610f2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', sans-serif;
    }

    .login-card {
      width: 100%;
      max-width: 420px;
      border-radius: 16px;
      box-shadow: 0 15px 40px rgba(0,0,0,0.2);
      animation: fadeIn 0.6s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .brand {
      font-weight: 700;
      font-size: 1.5rem;
      color: #0d6efd;
    }

    .form-control {
      height: 48px;
      border-radius: 10px;
    }

    .btn-login {
      height: 48px;
      border-radius: 10px;
      font-weight: 600;
    }

    .toggle-password {
      cursor: pointer;
    }
  </style>


<div class="card login-card">
  <div class="card-body p-4">

    <div class="text-center mb-4">
      <div class="brand">CRM-HRMS</div>
      <small class="text-muted">Login to your account</small>
    </div>

    <?php if (!empty($error ?? null)): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">

      <div class="mb-3">
        <label class="form-label">Email address</label>
        <input type="email" name="email" class="form-control" required placeholder="admin@example.com">
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <div class="input-group">
          <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
          <span class="input-group-text toggle-password" onclick="togglePassword()">
            <i class="bi bi-eye" id="eyeIcon"></i>
          </span>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100 btn-login">
        <i class="bi bi-box-arrow-in-right"></i> Login
      </button>

    </form>

    <div class="text-center mt-3 text-muted">
      © <?= date('Y') ?> CRM-HRMS
    </div>

  </div>
</div>


<script>
    function togglePassword() {
  const pwd = document.getElementById('password');
  const icon = document.getElementById('eyeIcon');
  if (pwd.type === 'password') {
    pwd.type = 'text';
    icon.classList.replace('bi-eye','bi-eye-slash');
  } else {
    pwd.type = 'password';
    icon.classList.replace('bi-eye-slash','bi-eye');
  }
}
</script>


<?php require '../app/views/layouts/footer.php'; ?>

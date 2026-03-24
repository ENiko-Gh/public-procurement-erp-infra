<?php
session_start();

// Si ya está logueado, redirigir
if (isset($_SESSION['user_id'])) {
  header('Location: dashboard.php');
  exit;
}

$error = isset($_GET['error']) ? htmlspecialchars(urldecode($_GET['error'])) : '';
$msg   = isset($_GET['msg']) ? htmlspecialchars(urldecode($_GET['msg'])) : '';
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión - Compras Públicas</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

  <div class="login-wrapper">
    <div class="login-card">

      <div class="login-header text-center">
        <div class="logo-icon mb-2">
          <i class="fa fa-building-columns fa-2x"></i>
        </div>
        <h4 class="fw-bold mb-1">Compras Públicas</h4>
        <p class="small opacity-75 mb-0">Sistema de Gestión Institucional</p>
      </div>

      <div class="login-body">

        <?php if ($error): ?>
          <div class="alert alert-danger py-2 small">
            <i class="fa fa-triangle-exclamation me-1"></i>
            <?php echo $error; ?>
          </div>
        <?php endif; ?>

        <?php if ($msg): ?>
          <div class="alert alert-success py-2 small">
            <i class="fa fa-circle-check me-1"></i>
            <?php echo $msg; ?>
          </div>
        <?php endif; ?>

        <form action="../controllers/auth_controller.php" method="POST">
          <input type="hidden" name="action" value="login">

          <div class="mb-3">
            <label class="form-label">Correo Institucional</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="fa fa-envelope"></i>
              </span>
              <input type="email"
                name="email"
                class="form-control"
                placeholder="usuario@institucion.gob.ec"
                required autofocus>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label">Contraseña</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="fa fa-lock"></i>
              </span>
              <input type="password"
                name="password"
                id="password"
                class="form-control"
                placeholder="••••••••"
                required>

              <button type="button"
                class="btn btn-outline-secondary"
                onclick="togglePass()">
                <i class="fa fa-eye" id="eyeIcon"></i>
              </button>
            </div>
          </div>

          <button type="submit"
            class="btn btn-primary w-100 py-2 fw-bold">
            <i class="fa fa-right-to-bracket me-2"></i>
            Iniciar Sesión
          </button>
        </form>

        <hr class="my-4">

        <div class="small text-muted text-center">
          <p class="fw-bold mb-2">Credenciales de prueba</p>

          <div class="row g-2">
            <div class="col-4">
              <div class="bg-light rounded p-2">
                <span class="badge bg-danger d-block mb-1">Admin</span>
                admin123
              </div>
            </div>

            <div class="col-4">
              <div class="bg-light rounded p-2">
                <span class="badge bg-warning text-dark d-block mb-1">Dev</span>
                dev123
              </div>
            </div>

            <div class="col-4">
              <div class="bg-light rounded p-2">
                <span class="badge bg-success d-block mb-1">Super</span>
                super123
              </div>
            </div>
          </div>
        </div>

        <div class="text-center mt-3">
          <a href="../index.php" class="text-muted small">
            <i class="fa fa-arrow-left me-1"></i>
            Volver al inicio
          </a>
        </div>

      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

  <script>
    function togglePass() {
      const p = document.getElementById('password');
      const i = document.getElementById('eyeIcon');

      if (p.type === 'password') {
        p.type = 'text';
        i.className = 'fa fa-eye-slash';
      } else {
        p.type = 'password';
        i.className = 'fa fa-eye';
      }
    }
  </script>

</body>

</html>
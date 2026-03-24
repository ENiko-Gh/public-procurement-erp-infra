<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Compras Públicas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="index-hero">
  <!-- NAVBAR TOP -->
  <nav class="navbar navbar-dark">
    <div class="container">
      <span class="navbar-brand fw-bold fs-5">
        <i class="fa fa-building-columns me-2"></i>ComprasPúblicas Ecuador
      </span>
      <a href="views/login.php" class="btn btn-outline-light btn-sm">
        <i class="fa fa-right-to-bracket me-1"></i>Iniciar Sesión
      </a>
    </div>
  </nav>

  <!-- HERO CONTENT -->
  <div class="hero-content">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center mb-5">
          <div class="mb-4">
            <i class="fa fa-building-columns" style="font-size:5rem; opacity:0.9;"></i>
          </div>
          <h1 class="display-4 fw-bold mb-3">Sistema de Gestión de<br>Compras Públicas</h1>
          <p class="lead mb-4 opacity-85">
            Plataforma integral para la administración transparente y eficiente de procesos de contratación pública.
            Gestione instituciones, proveedores, procesos, contratos y pagos desde un solo lugar.
          </p>
          <a href="views/login.php" class="btn btn-warning btn-lg fw-bold px-5 py-3 shadow">
            <i class="fa fa-right-to-bracket me-2"></i>Ingresar al Sistema
          </a>
        </div>
      </div>

      <!-- FEATURES -->
      <div class="row g-4 mt-2">
        <div class="col-md-4">
          <div class="feature-card text-center">
            <i class="fa fa-landmark fa-2x mb-3"></i>
            <h5 class="fw-bold">Instituciones Públicas</h5>
            <p class="small opacity-85 mb-0">Registro y gestión de las instituciones del Estado que realizan procesos de adquisición.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-card text-center">
            <i class="fa fa-file-contract fa-2x mb-3"></i>
            <h5 class="fw-bold">Procesos de Compra</h5>
            <p class="small opacity-85 mb-0">Control completo del ciclo de vida de cada proceso: planificación, publicación, evaluación y adjudicación.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-card text-center">
            <i class="fa fa-money-bill-trend-up fa-2x mb-3"></i>
            <h5 class="fw-bold">Trazabilidad Financiera</h5>
            <p class="small opacity-85 mb-0">Seguimiento de contratos y pagos con total transparencia y trazabilidad del gasto público.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-card text-center">
            <i class="fa fa-truck fa-2x mb-3"></i>
            <h5 class="fw-bold">Registro de Proveedores</h5>
            <p class="small opacity-85 mb-0">Directorio de proveedores habilitados para participar en procesos de contratación pública.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-card text-center">
            <i class="fa fa-shield-halved fa-2x mb-3"></i>
            <h5 class="fw-bold">Control de Acceso</h5>
            <p class="small opacity-85 mb-0">Roles diferenciados: Administrador, Desarrollador y Supervisor para garantizar la seguridad.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-card text-center">
            <i class="fa fa-code fa-2x mb-3"></i>
            <h5 class="fw-bold">API REST / Microservicios</h5>
            <p class="small opacity-85 mb-0">Arquitectura de microservicios con endpoints REST para integración con otros sistemas.</p>
          </div>
        </div>
      </div>

      <!-- CREDENCIALES DE PRUEBA -->
      <div class="row justify-content-center mt-5">
        <div class="col-lg-8">
          <div class="feature-card">
            <h6 class="fw-bold mb-3 text-center"><i class="fa fa-key me-2"></i>Credenciales de Acceso de Prueba</h6>
            <div class="row text-center g-3">
              <div class="col-md-4">
                <div class="bg-white bg-opacity-10 rounded p-3">
                  <div class="badge bg-danger mb-2">Administrador</div>
                  <div class="small"><strong>admin@compras.gob.ec</strong><br>admin123</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="bg-white bg-opacity-10 rounded p-3">
                  <div class="badge bg-warning text-dark mb-2">Desarrollador</div>
                  <div class="small"><strong>dev@compras.gob.ec</strong><br>dev123</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="bg-white bg-opacity-10 rounded p-3">
                  <div class="badge bg-success mb-2">Supervisor</div>
                  <div class="small"><strong>super@compras.gob.ec</strong><br>super123</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <footer class="text-center text-white py-3 opacity-75">
    <small>
      <i class="fa fa-building-columns me-1"></i>
      Sistema de Gestión de Compras Públicas &copy; <?php echo date('Y'); ?> &nbsp;|&nbsp;
      Desarrollado bajo normativa SERCOP &nbsp;|&nbsp;
      <i class="fa fa-code me-1"></i>API REST + MySQL
    </small>
  </footer>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>

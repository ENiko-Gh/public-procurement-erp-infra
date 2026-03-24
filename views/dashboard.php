<?php
$pageTitle = 'Dashboard - Compras Públicas';
require_once '../includes/header.php';
require_once '../config/database.php';

$pdo = getDB();

$stats = [
    'instituciones' => $pdo->query("SELECT COUNT(*) FROM institucion_publica")->fetchColumn(),
    'proveedores'   => $pdo->query("SELECT COUNT(*) FROM proveedor")->fetchColumn(),
    'procesos'      => $pdo->query("SELECT COUNT(*) FROM proceso_compra")->fetchColumn(),
    'contratos'     => $pdo->query("SELECT COUNT(*) FROM contrato")->fetchColumn(),
    'pagos'         => $pdo->query("SELECT COUNT(*) FROM pago")->fetchColumn(),
    'total_pagado'  => $pdo->query("SELECT COALESCE(SUM(monto_pagado),0) FROM pago")->fetchColumn(),
    'total_contratado' => $pdo->query("SELECT COALESCE(SUM(monto_contratado),0) FROM contrato")->fetchColumn(),
];

$ultimosProcesos = $pdo->query("
    SELECT pc.*, ip.nombre AS institucion 
    FROM proceso_compra pc 
    JOIN institucion_publica ip ON pc.id_institucion = ip.id_institucion 
    ORDER BY pc.created_at DESC LIMIT 5
")->fetchAll();

$estadosProcesos = $pdo->query("
    SELECT estado, COUNT(*) as total FROM proceso_compra GROUP BY estado
")->fetchAll();
?>

<div class="page-hero">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2><i class="fa fa-gauge me-2"></i>Panel de Control</h2>
      <p>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong> — <?php echo date('l, d \d\e F \d\e Y'); ?></p>
    </div>
    <div class="text-end d-none d-md-block">
      <span class="badge bg-white text-primary fs-6 px-3 py-2">
        <i class="fa fa-shield-halved me-1"></i><?php echo ucfirst($_SESSION['user_rol']); ?>
      </span>
    </div>
  </div>
</div>

<!-- STATS -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="card stat-card p-3">
      <div class="stat-number"><?php echo $stats['instituciones']; ?></div>
      <div class="stat-label"><i class="fa fa-landmark me-1"></i>Instituciones</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card stat-card p-3" style="border-color:#16a34a">
      <div class="stat-number" style="color:#16a34a"><?php echo $stats['proveedores']; ?></div>
      <div class="stat-label"><i class="fa fa-truck me-1"></i>Proveedores</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card stat-card p-3" style="border-color:#f59e0b">
      <div class="stat-number" style="color:#f59e0b"><?php echo $stats['procesos']; ?></div>
      <div class="stat-label"><i class="fa fa-file-contract me-1"></i>Procesos</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card stat-card p-3" style="border-color:#7c3aed">
      <div class="stat-number" style="color:#7c3aed"><?php echo $stats['contratos']; ?></div>
      <div class="stat-label"><i class="fa fa-handshake me-1"></i>Contratos</div>
    </div>
  </div>
</div>

<!-- FINANCIERO -->
<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="card p-3">
      <div class="d-flex align-items-center">
        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
          <i class="fa fa-handshake fa-2x text-primary"></i>
        </div>
        <div>
          <div class="text-muted small">Total Contratado</div>
          <div class="fw-bold fs-4 text-primary">$<?php echo number_format($stats['total_contratado'], 2, '.', ','); ?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card p-3">
      <div class="d-flex align-items-center">
        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
          <i class="fa fa-money-bill-wave fa-2x text-success"></i>
        </div>
        <div>
          <div class="text-muted small">Total Pagado</div>
          <div class="fw-bold fs-4 text-success">$<?php echo number_format($stats['total_pagado'], 2, '.', ','); ?></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- ÚLTIMOS PROCESOS -->
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
        <span><i class="fa fa-clock-rotate-left me-2"></i>Últimos Procesos de Compra</span>
        <a href="procesos.php" class="btn btn-sm btn-outline-light">Ver todos</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead><tr>
              <th>Tipo</th><th>Institución</th><th>Fecha Inicio</th><th>Estado</th>
            </tr></thead>
            <tbody>
            <?php foreach ($ultimosProcesos as $p): 
              $badgeClass = match($p['estado']) {
                'adjudicado' => 'bg-success',
                'publicado' => 'bg-primary',
                'en_evaluacion' => 'bg-warning text-dark',
                'cancelado','desierto' => 'bg-danger',
                default => 'bg-secondary'
              };
            ?>
            <tr>
              <td><?php echo htmlspecialchars($p['tipo_proceso']); ?></td>
              <td><?php echo htmlspecialchars($p['institucion']); ?></td>
              <td><?php echo date('d/m/Y', strtotime($p['fecha_inicio'])); ?></td>
              <td><span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst(str_replace('_',' ',$p['estado'])); ?></span></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- ESTADOS PROCESOS -->
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header bg-secondary text-white py-3">
        <i class="fa fa-chart-pie me-2"></i>Estado de Procesos
      </div>
      <div class="card-body">
        <?php 
        $colors = ['planificacion'=>'secondary','publicado'=>'primary','en_evaluacion'=>'warning','adjudicado'=>'success','desierto'=>'dark','cancelado'=>'danger'];
        foreach ($estadosProcesos as $e): 
          $c = $colors[$e['estado']] ?? 'secondary';
        ?>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <span class="badge bg-<?php echo $c; ?> me-2">&nbsp;</span>
            <?php echo ucfirst(str_replace('_',' ',$e['estado'])); ?>
          </div>
          <strong><?php echo $e['total']; ?></strong>
        </div>
        <?php endforeach; ?>
        <hr>
        <div class="text-center mt-3">
          <a href="procesos.php" class="btn btn-primary btn-sm">
            <i class="fa fa-file-contract me-1"></i>Gestionar Procesos
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ACCESOS RÁPIDOS -->
<div class="row g-3 mt-2">
  <div class="col-12"><h6 class="text-muted fw-bold"><i class="fa fa-bolt me-1"></i>Accesos Rápidos</h6></div>
  <?php $menus = [
    ['instituciones.php','fa-landmark','Instituciones','primary'],
    ['proveedores.php','fa-truck','Proveedores','success'],
    ['procesos.php','fa-file-contract','Procesos','warning'],
    ['contratos.php','fa-handshake','Contratos','info'],
    ['pagos.php','fa-money-bill','Pagos','secondary'],
    ['microservicio.php','fa-code','API REST','dark'],
  ]; ?>
  <?php foreach ($menus as $m): ?>
  <div class="col-6 col-md-2">
    <a href="<?php echo $m[0]; ?>" class="card text-center p-3 text-decoration-none text-<?php echo $m[3]; ?> hover-shadow">
      <i class="fa <?php echo $m[1]; ?> fa-2x mb-2"></i>
      <div class="small fw-bold"><?php echo $m[2]; ?></div>
    </a>
  </div>
  <?php endforeach; ?>
</div>

<?php require_once '../includes/footer.php'; ?>

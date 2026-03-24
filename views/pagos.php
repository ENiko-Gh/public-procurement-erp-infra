<?php
$pageTitle = 'Pagos';
require_once '../includes/header.php';
require_once '../config/database.php';

$pdo = getDB();
$pagos = $pdo->query("
    SELECT pg.*, c.objeto_contrato, c.monto_contratado,
           p.razon_social AS proveedor, ip.nombre AS institucion
    FROM pago pg
    JOIN contrato c ON pg.id_contrato = c.id_contrato
    JOIN proveedor p ON c.id_proveedor = p.id_proveedor
    JOIN proceso_compra pc ON c.id_proceso = pc.id_proceso
    JOIN institucion_publica ip ON pc.id_institucion = ip.id_institucion
    ORDER BY pg.fecha_pago DESC
")->fetchAll();
$contratos = $pdo->query("SELECT c.id_contrato, p.razon_social, c.monto_contratado FROM contrato c JOIN proveedor p ON c.id_proveedor=p.id_proveedor ORDER BY c.id_contrato")->fetchAll();
$totalPagado = array_sum(array_column($pagos, 'monto_pagado'));
?>

<div class="page-hero">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2><i class="fa fa-money-bill me-2"></i>Registro de Pagos</h2>
      <p>Trazabilidad financiera de los pagos realizados a proveedores</p>
    </div>
    <?php if (canEdit()): ?>
    <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#modalCrear">
      <i class="fa fa-plus me-1"></i>Nuevo Pago
    </button>
    <?php endif; ?>
  </div>
</div>

<!-- RESUMEN -->
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card p-3 text-center">
      <div class="text-muted small mb-1">Total Pagos Registrados</div>
      <div class="fw-bold fs-3 text-primary"><?php echo count($pagos); ?></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3 text-center">
      <div class="text-muted small mb-1">Monto Total Pagado</div>
      <div class="fw-bold fs-4 text-success">$<?php echo number_format($totalPagado, 2, '.', ','); ?></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3 text-center">
      <div class="text-muted small mb-1">Contratos con Pagos</div>
      <div class="fw-bold fs-3 text-info"><?php echo count(array_unique(array_column($pagos, 'id_contrato'))); ?></div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead><tr>
          <th>#</th><th>Institución</th><th>Proveedor</th><th>Fecha Pago</th>
          <th>Monto</th><th>Comprobante</th><th>Descripción</th>
          <?php if (canEdit()): ?><th class="text-center">Acciones</th><?php endif; ?>
        </tr></thead>
        <tbody>
        <?php if (empty($pagos)): ?>
          <tr><td colspan="8" class="text-center py-4 text-muted"><i class="fa fa-inbox fa-2x d-block mb-2"></i>No hay pagos registrados</td></tr>
        <?php else: ?>
        <?php foreach ($pagos as $pg): ?>
        <tr>
          <td><?php echo $pg['id_pago']; ?></td>
          <td class="text-truncate" style="max-width:130px"><?php echo htmlspecialchars($pg['institucion']); ?></td>
          <td><?php echo htmlspecialchars($pg['proveedor']); ?></td>
          <td><?php echo date('d/m/Y', strtotime($pg['fecha_pago'])); ?></td>
          <td class="fw-bold text-success">$<?php echo number_format($pg['monto_pagado'],2,'.',','); ?></td>
          <td><code><?php echo htmlspecialchars($pg['comprobante'] ?? '—'); ?></code></td>
          <td class="text-truncate" style="max-width:200px"><?php echo htmlspecialchars($pg['descripcion'] ?? '—'); ?></td>
          <?php if (canEdit()): ?>
          <td class="text-center action-btns">
            <button class="btn btn-sm btn-outline-primary" onclick='openEditModal(<?php echo json_encode($pg); ?>)'>
              <i class="fa fa-pen"></i>
            </button>
            <?php if (isAdmin()): ?>
            <a href="../controllers/pago_controller.php?action=eliminar&id=<?php echo $pg['id_pago']; ?>"
               class="btn btn-sm btn-outline-danger btn-delete"><i class="fa fa-trash"></i></a>
            <?php endif; ?>
          </td>
          <?php endif; ?>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer text-muted small">Total pagado: <strong class="text-success">$<?php echo number_format($totalPagado,2,'.',','); ?></strong></div>
</div>

<?php if (canEdit()): ?>
<!-- MODAL CREAR -->
<div class="modal fade" id="modalCrear" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title"><i class="fa fa-plus me-2"></i>Registrar Nuevo Pago</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <form action="../controllers/pago_controller.php" method="POST">
      <input type="hidden" name="action" value="crear">
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Contrato *</label>
          <select name="id_contrato" class="form-select" required>
            <option value="">-- Seleccione contrato --</option>
            <?php foreach ($contratos as $c): ?>
            <option value="<?php echo $c['id_contrato']; ?>">Contrato #<?php echo $c['id_contrato']; ?> - <?php echo htmlspecialchars($c['razon_social']); ?> ($<?php echo number_format($c['monto_contratado'],2,'.',','); ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="row g-2">
          <div class="col-md-6">
            <label class="form-label">Fecha de Pago *</label>
            <input type="date" name="fecha_pago" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Monto Pagado ($) *</label>
            <input type="number" name="monto_pagado" class="form-control" required step="0.01" min="0.01">
          </div>
        </div>
        <div class="mt-2">
          <label class="form-label">Comprobante</label>
          <input type="text" name="comprobante" class="form-control" placeholder="CMP-2024-XXX">
        </div>
        <div class="mt-2">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" class="form-control" rows="2" placeholder="Descripción del pago..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i>Guardar</button>
      </div>
    </form>
  </div></div>
</div>

<!-- MODAL EDITAR -->
<div class="modal fade" id="modalEditar" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title"><i class="fa fa-pen me-2"></i>Editar Pago</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <form action="../controllers/pago_controller.php" method="POST">
      <input type="hidden" name="action" value="actualizar">
      <input type="hidden" name="id_pago" id="e_id">
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Contrato *</label>
          <select name="id_contrato" id="e_contrato" class="form-select" required>
            <?php foreach ($contratos as $c): ?>
            <option value="<?php echo $c['id_contrato']; ?>">Contrato #<?php echo $c['id_contrato']; ?> - <?php echo htmlspecialchars($c['razon_social']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="row g-2">
          <div class="col-md-6">
            <label class="form-label">Fecha de Pago *</label>
            <input type="date" name="fecha_pago" id="e_fecha" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Monto ($) *</label>
            <input type="number" name="monto_pagado" id="e_monto" class="form-control" required step="0.01">
          </div>
        </div>
        <div class="mt-2">
          <label class="form-label">Comprobante</label>
          <input type="text" name="comprobante" id="e_comp" class="form-control">
        </div>
        <div class="mt-2">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" id="e_desc" class="form-control" rows="2"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i>Actualizar</button>
      </div>
    </form>
  </div></div>
</div>

<script>
function openEditModal(data) {
    document.getElementById('e_id').value = data.id_pago;
    document.getElementById('e_contrato').value = data.id_contrato;
    document.getElementById('e_fecha').value = data.fecha_pago;
    document.getElementById('e_monto').value = data.monto_pagado;
    document.getElementById('e_comp').value = data.comprobante || '';
    document.getElementById('e_desc').value = data.descripcion || '';
    new bootstrap.Modal(document.getElementById('modalEditar')).show();
}
</script>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>

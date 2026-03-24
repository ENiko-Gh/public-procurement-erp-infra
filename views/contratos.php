<?php
$pageTitle = 'Contratos';
require_once '../includes/header.php';
require_once '../config/database.php';

$pdo = getDB();
$contratos = $pdo->query("
    SELECT c.*, pc.tipo_proceso, ip.nombre AS institucion, p.razon_social AS proveedor
    FROM contrato c
    JOIN proceso_compra pc ON c.id_proceso = pc.id_proceso
    JOIN institucion_publica ip ON pc.id_institucion = ip.id_institucion
    JOIN proveedor p ON c.id_proveedor = p.id_proveedor
    ORDER BY c.fecha_adjudicacion DESC
")->fetchAll();
$procesos   = $pdo->query("SELECT id_proceso, tipo_proceso, fecha_inicio FROM proceso_compra ORDER BY fecha_inicio DESC")->fetchAll();
$proveedores= $pdo->query("SELECT id_proveedor, razon_social FROM proveedor ORDER BY razon_social")->fetchAll();
$estadosC   = ['vigente','finalizado','rescindido','suspendido'];
?>

<div class="page-hero">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2><i class="fa fa-handshake me-2"></i>Contratos Adjudicados</h2>
      <p>Gestión de contratos resultantes de los procesos de compra pública</p>
    </div>
    <?php if (canEdit()): ?>
    <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#modalCrear">
      <i class="fa fa-plus me-1"></i>Nuevo Contrato
    </button>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead><tr>
          <th>#</th><th>Proceso</th><th>Institución</th><th>Proveedor</th>
          <th>Fecha Adj.</th><th>Monto</th><th>Estado</th>
          <?php if (canEdit()): ?><th class="text-center">Acciones</th><?php endif; ?>
        </tr></thead>
        <tbody>
        <?php if (empty($contratos)): ?>
          <tr><td colspan="8" class="text-center py-4 text-muted"><i class="fa fa-inbox fa-2x d-block mb-2"></i>No hay contratos registrados</td></tr>
        <?php else: ?>
        <?php foreach ($contratos as $c):
          $bc = match($c['estado']) {
            'vigente'=>'bg-success','finalizado'=>'bg-secondary','rescindido'=>'bg-danger','suspendido'=>'bg-warning text-dark', default=>'bg-secondary'
          };
        ?>
        <tr>
          <td><?php echo $c['id_contrato']; ?></td>
          <td><?php echo htmlspecialchars($c['tipo_proceso']); ?></td>
          <td class="text-truncate" style="max-width:150px"><?php echo htmlspecialchars($c['institucion']); ?></td>
          <td><?php echo htmlspecialchars($c['proveedor']); ?></td>
          <td><?php echo date('d/m/Y', strtotime($c['fecha_adjudicacion'])); ?></td>
          <td class="fw-bold text-success">$<?php echo number_format($c['monto_contratado'],2,'.',','); ?></td>
          <td><span class="badge <?php echo $bc; ?>"><?php echo ucfirst($c['estado']); ?></span></td>
          <?php if (canEdit()): ?>
          <td class="text-center action-btns">
            <button class="btn btn-sm btn-outline-primary" onclick='openEditModal(<?php echo json_encode($c); ?>)'>
              <i class="fa fa-pen"></i>
            </button>
            <?php if (isAdmin()): ?>
            <a href="../controllers/contrato_controller.php?action=eliminar&id=<?php echo $c['id_contrato']; ?>"
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
  <div class="card-footer text-muted small">Total: <strong><?php echo count($contratos); ?></strong> contratos</div>
</div>

<?php if (canEdit()): ?>
<!-- MODAL CREAR -->
<div class="modal fade" id="modalCrear" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title"><i class="fa fa-plus me-2"></i>Nuevo Contrato</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <form action="../controllers/contrato_controller.php" method="POST">
      <input type="hidden" name="action" value="crear">
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Proceso de Compra *</label>
            <select name="id_proceso" class="form-select" required>
              <option value="">-- Seleccione proceso --</option>
              <?php foreach ($procesos as $pr): ?>
              <option value="<?php echo $pr['id_proceso']; ?>"><?php echo htmlspecialchars($pr['tipo_proceso'].' ('.$pr['fecha_inicio'].')'); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Proveedor *</label>
            <select name="id_proveedor" class="form-select" required>
              <option value="">-- Seleccione proveedor --</option>
              <?php foreach ($proveedores as $pv): ?>
              <option value="<?php echo $pv['id_proveedor']; ?>"><?php echo htmlspecialchars($pv['razon_social']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Fecha de Adjudicación *</label>
            <input type="date" name="fecha_adjudicacion" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Monto Contratado ($) *</label>
            <input type="number" name="monto_contratado" class="form-control" required step="0.01" min="0.01">
          </div>
          <div class="col-md-4">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select">
              <?php foreach ($estadosC as $e): ?>
              <option value="<?php echo $e; ?>"><?php echo ucfirst($e); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Objeto del Contrato</label>
            <textarea name="objeto_contrato" class="form-control" rows="2" placeholder="Descripción del objeto contractual..."></textarea>
          </div>
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
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title"><i class="fa fa-pen me-2"></i>Editar Contrato</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <form action="../controllers/contrato_controller.php" method="POST">
      <input type="hidden" name="action" value="actualizar">
      <input type="hidden" name="id_contrato" id="e_id">
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Proceso de Compra *</label>
            <select name="id_proceso" id="e_proceso" class="form-select" required>
              <?php foreach ($procesos as $pr): ?>
              <option value="<?php echo $pr['id_proceso']; ?>"><?php echo htmlspecialchars($pr['tipo_proceso'].' ('.$pr['fecha_inicio'].')'); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Proveedor *</label>
            <select name="id_proveedor" id="e_prov" class="form-select" required>
              <?php foreach ($proveedores as $pv): ?>
              <option value="<?php echo $pv['id_proveedor']; ?>"><?php echo htmlspecialchars($pv['razon_social']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Fecha Adjudicación *</label>
            <input type="date" name="fecha_adjudicacion" id="e_fecha" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Monto Contratado ($) *</label>
            <input type="number" name="monto_contratado" id="e_monto" class="form-control" required step="0.01">
          </div>
          <div class="col-md-4">
            <label class="form-label">Estado</label>
            <select name="estado" id="e_estado" class="form-select">
              <?php foreach ($estadosC as $e): ?>
              <option value="<?php echo $e; ?>"><?php echo ucfirst($e); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Objeto del Contrato</label>
            <textarea name="objeto_contrato" id="e_objeto" class="form-control" rows="2"></textarea>
          </div>
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
    document.getElementById('e_id').value = data.id_contrato;
    document.getElementById('e_proceso').value = data.id_proceso;
    document.getElementById('e_prov').value = data.id_proveedor;
    document.getElementById('e_fecha').value = data.fecha_adjudicacion;
    document.getElementById('e_monto').value = data.monto_contratado;
    document.getElementById('e_estado').value = data.estado;
    document.getElementById('e_objeto').value = data.objeto_contrato || '';
    new bootstrap.Modal(document.getElementById('modalEditar')).show();
}
</script>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>

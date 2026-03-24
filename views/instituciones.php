<?php
$pageTitle = 'Instituciones Públicas';
require_once '../includes/header.php';
require_once '../config/database.php';

$pdo = getDB();
$instituciones = $pdo->query("SELECT * FROM institucion_publica ORDER BY nombre")->fetchAll();
?>

<div class="page-hero">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2><i class="fa fa-landmark me-2"></i>Instituciones Públicas</h2>
      <p>Gestión del catálogo de instituciones que realizan compras públicas</p>
    </div>
    <?php if (canEdit()): ?>
    <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#modalCrear">
      <i class="fa fa-plus me-1"></i>Nueva Institución
    </button>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead><tr>
          <th>#</th><th>Nombre</th><th>Teléfono</th><th>Correo</th><th>Dirección</th>
          <?php if (canEdit()): ?><th class="text-center">Acciones</th><?php endif; ?>
        </tr></thead>
        <tbody>
        <?php if (empty($instituciones)): ?>
          <tr><td colspan="6" class="text-center py-4 text-muted"><i class="fa fa-inbox fa-2x d-block mb-2"></i>No hay instituciones registradas</td></tr>
        <?php else: ?>
        <?php foreach ($instituciones as $i): ?>
        <tr>
          <td><?php echo $i['id_institucion']; ?></td>
          <td class="fw-semibold"><?php echo htmlspecialchars($i['nombre']); ?></td>
          <td><?php echo htmlspecialchars($i['telefono'] ?? '—'); ?></td>
          <td><?php echo htmlspecialchars($i['correo'] ?? '—'); ?></td>
          <td class="text-truncate" style="max-width:200px"><?php echo htmlspecialchars($i['direccion'] ?? '—'); ?></td>
          <?php if (canEdit()): ?>
          <td class="text-center action-btns">
            <button class="btn btn-sm btn-outline-primary" 
              onclick='openEditModal("modalEditar", <?php echo json_encode($i); ?>)'>
              <i class="fa fa-pen"></i>
            </button>
            <?php if (isAdmin()): ?>
            <a href="../controllers/institucion_controller.php?action=eliminar&id=<?php echo $i['id_institucion']; ?>"
               class="btn btn-sm btn-outline-danger btn-delete">
              <i class="fa fa-trash"></i>
            </a>
            <?php endif; ?>
          </td>
          <?php endif; ?>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer text-muted small">
    Total: <strong><?php echo count($instituciones); ?></strong> instituciones registradas
  </div>
</div>

<?php if (canEdit()): ?>
<!-- MODAL CREAR -->
<div class="modal fade" id="modalCrear" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title"><i class="fa fa-plus me-2"></i>Nueva Institución</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <form action="../controllers/institucion_controller.php" method="POST">
      <input type="hidden" name="action" value="crear">
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nombre de la Institución *</label>
          <input type="text" name="nombre" class="form-control" required placeholder="Ministerio de...">
        </div>
        <div class="row g-2">
          <div class="col-md-6">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" class="form-control" placeholder="02-XXXXXXX">
          </div>
          <div class="col-md-6">
            <label class="form-label">Correo</label>
            <input type="email" name="correo" class="form-control" placeholder="info@inst.gob.ec">
          </div>
        </div>
        <div class="mt-2">
          <label class="form-label">Dirección</label>
          <input type="text" name="direccion" class="form-control" placeholder="Av. ...">
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
      <h5 class="modal-title"><i class="fa fa-pen me-2"></i>Editar Institución</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <form action="../controllers/institucion_controller.php" method="POST">
      <input type="hidden" name="action" value="actualizar">
      <input type="hidden" name="id_institucion" id="edit_id">
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nombre de la Institución *</label>
          <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
        </div>
        <div class="row g-2">
          <div class="col-md-6">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" id="edit_telefono" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Correo</label>
            <input type="email" name="correo" id="edit_correo" class="form-control">
          </div>
        </div>
        <div class="mt-2">
          <label class="form-label">Dirección</label>
          <input type="text" name="direccion" id="edit_direccion" class="form-control">
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
function openEditModal(modalId, data) {
    document.getElementById('edit_id').value = data.id_institucion;
    document.getElementById('edit_nombre').value = data.nombre;
    document.getElementById('edit_telefono').value = data.telefono || '';
    document.getElementById('edit_correo').value = data.correo || '';
    document.getElementById('edit_direccion').value = data.direccion || '';
    new bootstrap.Modal(document.getElementById(modalId)).show();
}
</script>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>

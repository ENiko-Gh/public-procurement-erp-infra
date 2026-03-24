<?php
$pageTitle = 'Proveedores';
require_once '../includes/header.php';
require_once '../config/database.php';

$pdo = getDB();
$proveedores = $pdo->query("SELECT * FROM proveedor ORDER BY razon_social")->fetchAll();
?>

<div class="page-hero">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2><i class="fa fa-truck me-2"></i>Proveedores</h2>
      <p>Registro de proveedores habilitados para procesos de contratación pública</p>
    </div>
    <?php if (canEdit()): ?>
      <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#modalCrear">
        <i class="fa fa-plus me-1"></i>Nuevo Proveedor
      </button>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Razón Social</th>
            <th>N° Identificación</th>
            <th>Teléfono</th>
            <th>Correo</th>
            <?php if (canEdit()): ?><th class="text-center">Acciones</th><?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($proveedores)): ?>
            <tr>
              <td colspan="6" class="text-center py-4 text-muted"><i class="fa fa-inbox fa-2x d-block mb-2"></i>No hay proveedores registrados</td>
            </tr>
          <?php else: ?>
            <?php foreach ($proveedores as $p): ?>
              <tr>
                <td><?php echo $p['id_proveedor']; ?></td>
                <td class="fw-semibold"><?php echo htmlspecialchars($p['razon_social']); ?></td>
                <td><code><?php echo htmlspecialchars($p['numero_identificacion']); ?></code></td>
                <td><?php echo htmlspecialchars($p['telefono'] ?? '—'); ?></td>
                <td><?php echo htmlspecialchars($p['correo'] ?? '—'); ?></td>
                <?php if (canEdit()): ?>
                  <td class="text-center action-btns">
                    <button class="btn btn-sm btn-outline-primary"
                      onclick='openEditModal(<?php echo json_encode($p); ?>)'>
                      <i class="fa fa-pen"></i>
                    </button>
                    <?php if (isAdmin()): ?>
                      <a href="../controllers/proveedor_controller.php?action=eliminar&id=<?php echo $p['id_proveedor']; ?>"
                        class="btn btn-sm btn-outline-danger btn-delete">
                        <i class="fa fa-trash"></i>
                      </a>
                    <?php endif; ?>
                  </td>
                <?php endif; ?>
              </tr>
          <?php endforeach;
          endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer text-muted small">Total: <strong><?php echo count($proveedores); ?></strong> proveedores</div>
</div>

<?php if (canEdit()): ?>
  <!-- MODAL CREAR -->
  <div class="modal fade" id="modalCrear" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-plus me-2"></i>Nuevo Proveedor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="../controllers/proveedor_controller.php" method="POST">
          <input type="hidden" name="action" value="crear">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Razón Social *</label>
              <input type="text" name="razon_social" class="form-control" required placeholder="Empresa S.A.">
            </div>
            <div class="mb-3">
              <label class="form-label">Número de Identificación (RUC) *</label>
              <input type="text" name="numero_identificacion" class="form-control" required placeholder="1790000000001" maxlength="20">
            </div>
            <div class="row g-2">
              <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" placeholder="02-XXXXXXX">
              </div>
              <div class="col-md-6">
                <label class="form-label">Correo</label>
                <input type="email" name="correo" class="form-control" placeholder="info@empresa.com">
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
      </div>
    </div>
  </div>

  <!-- MODAL EDITAR -->
  <div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-pen me-2"></i>Editar Proveedor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="../controllers/proveedor_controller.php" method="POST">
          <input type="hidden" name="action" value="actualizar">
          <input type="hidden" name="id_proveedor" id="e_id">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Razón Social *</label>
              <input type="text" name="razon_social" id="e_razon" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Número de Identificación (RUC) *</label>
              <input type="text" name="numero_identificacion" id="e_numid" class="form-control" required>
            </div>
            <div class="row g-2">
              <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" id="e_telefono" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label">Correo</label>
                <input type="email" name="correo" id="e_correo" class="form-control">
              </div>
            </div>
            <div class="mt-2">
              <label class="form-label">Dirección</label>
              <input type="text" name="direccion" id="e_direccion" class="form-control">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i>Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script>
    function openEditModal(data) {
      document.getElementById('e_id').value = data.id_proveedor;
      document.getElementById('e_razon').value = data.razon_social;
      document.getElementById('e_numid').value = data.numero_identificacion;
      document.getElementById('e_telefono').value = data.telefono || '';
      document.getElementById('e_correo').value = data.correo || '';
      document.getElementById('e_direccion').value = data.direccion || '';
      new bootstrap.Modal(document.getElementById('modalEditar')).show();
    }
  </script>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
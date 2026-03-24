<?php
$pageTitle = 'Gestión de Usuarios';
require_once '../includes/header.php';
require_once '../includes/session.php';
requireRole('administrador');
require_once '../config/database.php';

$pdo = getDB();
$usuarios = $pdo->query("SELECT id_usuario, nombre, email, rol, activo, created_at FROM usuarios ORDER BY nombre")->fetchAll();
$roles = ['administrador','desarrollador','supervisor'];
?>

<div class="page-hero">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2><i class="fa fa-users me-2"></i>Gestión de Usuarios</h2>
      <p>Administración de usuarios y roles del sistema</p>
    </div>
    <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#modalCrear">
      <i class="fa fa-plus me-1"></i>Nuevo Usuario
    </button>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead><tr>
          <th>#</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th><th>Creado</th>
          <th class="text-center">Acciones</th>
        </tr></thead>
        <tbody>
        <?php foreach ($usuarios as $u):
          $rolBadge = match($u['rol']) {
            'administrador'=>'bg-danger','desarrollador'=>'bg-warning text-dark', default=>'bg-success'
          };
        ?>
        <tr>
          <td><?php echo $u['id_usuario']; ?></td>
          <td class="fw-semibold"><i class="fa fa-circle-user me-1 text-muted"></i><?php echo htmlspecialchars($u['nombre']); ?></td>
          <td><?php echo htmlspecialchars($u['email']); ?></td>
          <td><span class="badge <?php echo $rolBadge; ?>"><?php echo ucfirst($u['rol']); ?></span></td>
          <td>
            <?php if ($u['activo']): ?>
              <span class="badge bg-success">Activo</span>
            <?php else: ?>
              <span class="badge bg-danger">Inactivo</span>
            <?php endif; ?>
          </td>
          <td><?php echo date('d/m/Y', strtotime($u['created_at'])); ?></td>
          <td class="text-center action-btns">
            <button class="btn btn-sm btn-outline-primary" onclick='openEditModal(<?php echo json_encode($u); ?>)'>
              <i class="fa fa-pen"></i>
            </button>
            <?php if ($u['id_usuario'] != $_SESSION['user_id']): ?>
            <a href="../controllers/usuario_controller.php?action=eliminar&id=<?php echo $u['id_usuario']; ?>"
               class="btn btn-sm btn-outline-danger btn-delete"><i class="fa fa-trash"></i></a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer text-muted small">Total: <strong><?php echo count($usuarios); ?></strong> usuarios</div>
</div>

<!-- MODAL CREAR -->
<div class="modal fade" id="modalCrear" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title"><i class="fa fa-user-plus me-2"></i>Nuevo Usuario</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <form action="../controllers/usuario_controller.php" method="POST">
      <input type="hidden" name="action" value="crear">
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nombre Completo *</label>
          <input type="text" name="nombre" class="form-control" required placeholder="Juan Pérez">
        </div>
        <div class="mb-3">
          <label class="form-label">Correo Electrónico *</label>
          <input type="email" name="email" class="form-control" required placeholder="usuario@inst.gob.ec">
        </div>
        <div class="mb-3">
          <label class="form-label">Contraseña * <small class="text-muted">(mínimo 6 caracteres)</small></label>
          <input type="password" name="password" class="form-control" required minlength="6">
        </div>
        <div class="mb-3">
          <label class="form-label">Rol</label>
          <select name="rol" class="form-select">
            <?php foreach ($roles as $r): ?>
            <option value="<?php echo $r; ?>"><?php echo ucfirst($r); ?></option>
            <?php endforeach; ?>
          </select>
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
      <h5 class="modal-title"><i class="fa fa-pen me-2"></i>Editar Usuario</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <form action="../controllers/usuario_controller.php" method="POST">
      <input type="hidden" name="action" value="actualizar">
      <input type="hidden" name="id_usuario" id="e_id">
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nombre Completo *</label>
          <input type="text" name="nombre" id="e_nombre" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Correo Electrónico *</label>
          <input type="email" name="email" id="e_email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Nueva Contraseña <small class="text-muted">(dejar vacío para no cambiar)</small></label>
          <input type="password" name="password" class="form-control" minlength="6">
        </div>
        <div class="row g-2">
          <div class="col-md-6">
            <label class="form-label">Rol</label>
            <select name="rol" id="e_rol" class="form-select">
              <?php foreach ($roles as $r): ?>
              <option value="<?php echo $r; ?>"><?php echo ucfirst($r); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Estado</label>
            <select name="activo" id="e_activo" class="form-select">
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
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
    document.getElementById('e_id').value = data.id_usuario;
    document.getElementById('e_nombre').value = data.nombre;
    document.getElementById('e_email').value = data.email;
    document.getElementById('e_rol').value = data.rol;
    document.getElementById('e_activo').value = data.activo;
    new bootstrap.Modal(document.getElementById('modalEditar')).show();
}
</script>

<?php require_once '../includes/footer.php'; ?>

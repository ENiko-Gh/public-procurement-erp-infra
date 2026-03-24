<?php
require_once '../includes/session.php';
requireLogin();
require_once '../config/database.php';
require_once '../includes/header.php';

$pdo = getDB();

/* ==========================
   CONSULTAS
========================== */

$procesos = $pdo->query("
    SELECT pc.*, ip.nombre AS institucion 
    FROM proceso_compra pc 
    JOIN institucion_publica ip 
        ON pc.id_institucion = ip.id_institucion 
    ORDER BY pc.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$instituciones = $pdo->query("
    SELECT id_institucion, nombre 
    FROM institucion_publica 
    ORDER BY nombre
")->fetchAll(PDO::FETCH_ASSOC);

$estadoOptions = [
  'planificacion',
  'publicado',
  'en_evaluacion',
  'adjudicado',
  'desierto',
  'cancelado'
];

$tiposProceso = [
  'Licitación',
  'Subasta Inversa Electrónica',
  'Cotización',
  'Menor Cuantía',
  'Ínfima Cuantía',
  'Contratación Directa',
  'Régimen Especial'
];
?>

<div class="container mt-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3><i class="fa fa-file-contract me-2"></i>Procesos de Compra</h3>

    <?php if (canEdit()): ?>
      <button class="btn btn-warning"
        data-bs-toggle="modal"
        data-bs-target="#modalCrear">
        <i class="fa fa-plus me-1"></i>Nuevo Proceso
      </button>
    <?php endif; ?>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">

        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Tipo</th>
              <th>Institución</th>
              <th>Fecha</th>
              <th>Presupuesto</th>
              <th>Estado</th>
              <?php if (canEdit()): ?>
                <th class="text-center">Acciones</th>
              <?php endif; ?>
            </tr>
          </thead>

          <tbody>

            <?php if (empty($procesos)): ?>
              <tr>
                <td colspan="<?php echo canEdit() ? 7 : 6; ?>" class="text-center p-4 text-muted">
                  No hay procesos registrados
                </td>
              </tr>
            <?php else: ?>

              <?php foreach ($procesos as $p):

                $badgeClass = match ($p['estado']) {
                  'adjudicado' => 'bg-success',
                  'publicado' => 'bg-primary',
                  'en_evaluacion' => 'bg-warning text-dark',
                  'cancelado', 'desierto' => 'bg-danger',
                  default => 'bg-secondary'
                };
              ?>

                <tr>
                  <td><?= $p['id_proceso']; ?></td>
                  <td><?= htmlspecialchars($p['tipo_proceso']); ?></td>
                  <td><?= htmlspecialchars($p['institucion']); ?></td>
                  <td><?= date('d/m/Y', strtotime($p['fecha_inicio'])); ?></td>
                  <td>
                    <?= $p['presupuesto_referencial']
                      ? '$' . number_format($p['presupuesto_referencial'], 2)
                      : '—'; ?>
                  </td>
                  <td>
                    <span class="badge <?= $badgeClass; ?>">
                      <?= ucfirst(str_replace('_', ' ', $p['estado'])); ?>
                    </span>
                  </td>

                  <?php if (canEdit()): ?>
                    <td class="text-center">

                      <button class="btn btn-sm btn-outline-primary"
                        onclick='openEditModal(<?= json_encode($p, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                        <i class="fa fa-pen"></i>
                      </button>

                      <?php if (isAdmin()): ?>
                        <a href="../controllers/proceso_controller.php?action=eliminar&id=<?= $p['id_proceso']; ?>"
                          class="btn btn-sm btn-outline-danger"
                          onclick="return confirm('¿Eliminar proceso?')">
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
  </div>
</div>

<?php if (canEdit()): ?>

  <!-- =========================
     MODAL CREAR
========================= -->
  <div class="modal fade" id="modalCrear">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <form action="../controllers/proceso_controller.php" method="POST">
          <input type="hidden" name="action" value="crear">

          <div class="modal-header">
            <h5 class="modal-title">Nuevo Proceso</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <div class="row g-3">

              <div class="col-md-6">
                <label>Institución</label>
                <select name="id_institucion" class="form-select" required>
                  <?php foreach ($instituciones as $inst): ?>
                    <option value="<?= $inst['id_institucion']; ?>">
                      <?= htmlspecialchars($inst['nombre']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-6">
                <label>Tipo</label>
                <select name="tipo_proceso" class="form-select" required>
                  <?php foreach ($tiposProceso as $t): ?>
                    <option value="<?= $t; ?>"><?= $t; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-4">
                <label>Fecha</label>
                <input type="date" name="fecha_inicio" class="form-control" required>
              </div>

              <div class="col-md-4">
                <label>Estado</label>
                <select name="estado" class="form-select">
                  <?php foreach ($estadoOptions as $e): ?>
                    <option value="<?= $e; ?>">
                      <?= ucfirst(str_replace('_', ' ', $e)); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-4">
                <label>Presupuesto</label>
                <input type="number" step="0.01" name="presupuesto_referencial" class="form-control">
              </div>

              <div class="col-12">
                <label>Descripción</label>
                <textarea name="descripcion" class="form-control"></textarea>
              </div>

            </div>
          </div>

          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-primary">Guardar</button>
          </div>

        </form>
      </div>
    </div>
  </div>

  <!-- =========================
     MODAL EDITAR
========================= -->
  <div class="modal fade" id="modalEditar">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <form action="../controllers/proceso_controller.php" method="POST">
          <input type="hidden" name="action" value="actualizar">
          <input type="hidden" name="id_proceso" id="e_id">

          <div class="modal-header">
            <h5 class="modal-title">Editar Proceso</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <div class="row g-3">

              <div class="col-md-6">
                <label>Institución</label>
                <select name="id_institucion" id="e_inst" class="form-select">
                  <?php foreach ($instituciones as $inst): ?>
                    <option value="<?= $inst['id_institucion']; ?>">
                      <?= htmlspecialchars($inst['nombre']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-6">
                <label>Tipo</label>
                <select name="tipo_proceso" id="e_tipo" class="form-select">
                  <?php foreach ($tiposProceso as $t): ?>
                    <option value="<?= $t; ?>"><?= $t; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-4">
                <label>Fecha</label>
                <input type="date" name="fecha_inicio" id="e_fecha" class="form-control">
              </div>

              <div class="col-md-4">
                <label>Estado</label>
                <select name="estado" id="e_estado" class="form-select">
                  <?php foreach ($estadoOptions as $e): ?>
                    <option value="<?= $e; ?>">
                      <?= ucfirst(str_replace('_', ' ', $e)); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-4">
                <label>Presupuesto</label>
                <input type="number" step="0.01"
                  name="presupuesto_referencial"
                  id="e_presup"
                  class="form-control">
              </div>

              <div class="col-12">
                <label>Descripción</label>
                <textarea name="descripcion"
                  id="e_desc"
                  class="form-control"></textarea>
              </div>

            </div>
          </div>

          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-primary">Actualizar</button>
          </div>

        </form>
      </div>
    </div>
  </div>

  <script>
    function openEditModal(data) {
      document.getElementById('e_id').value = data.id_proceso;
      document.getElementById('e_inst').value = data.id_institucion;
      document.getElementById('e_tipo').value = data.tipo_proceso;
      document.getElementById('e_fecha').value = data.fecha_inicio;
      document.getElementById('e_estado').value = data.estado;
      document.getElementById('e_presup').value = data.presupuesto_referencial ?? '';
      document.getElementById('e_desc').value = data.descripcion ?? '';

      new bootstrap.Modal(document.getElementById('modalEditar')).show();
    }
  </script>

<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
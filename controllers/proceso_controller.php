<?php
// controllers/proceso_controller.php
session_start();
require_once '../config/database.php';
require_once '../includes/session.php';
requireLogin();

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$pdo = getDB();
$redirect = '../views/procesos.php';

switch ($action) {
    case 'crear':
        if (!canEdit()) { header("Location: $redirect?error=Sin+permisos"); exit; }
        $id_inst   = intval($_POST['id_institucion'] ?? 0);
        $tipo      = trim($_POST['tipo_proceso'] ?? '');
        $fecha     = trim($_POST['fecha_inicio'] ?? '');
        $estado    = trim($_POST['estado'] ?? 'planificacion');
        $desc      = trim($_POST['descripcion'] ?? '');
        $presup    = floatval($_POST['presupuesto_referencial'] ?? 0);
        if (!$id_inst || empty($tipo) || empty($fecha)) {
            header("Location: $redirect?error=Institución+tipo+y+fecha+son+obligatorios"); exit;
        }
        try {
            $stmt = $pdo->prepare("INSERT INTO proceso_compra (id_institucion, tipo_proceso, fecha_inicio, estado, descripcion, presupuesto_referencial) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$id_inst, $tipo, $fecha, $estado, $desc, $presup ?: null]);
            header("Location: $redirect?success=Proceso+de+compra+creado+exitosamente"); exit;
        } catch (PDOException $e) {
            header("Location: $redirect?error=" . urlencode($e->getMessage())); exit;
        }
        break;

    case 'actualizar':
        if (!canEdit()) { header("Location: $redirect?error=Sin+permisos"); exit; }
        $id        = intval($_POST['id_proceso'] ?? 0);
        $id_inst   = intval($_POST['id_institucion'] ?? 0);
        $tipo      = trim($_POST['tipo_proceso'] ?? '');
        $fecha     = trim($_POST['fecha_inicio'] ?? '');
        $estado    = trim($_POST['estado'] ?? '');
        $desc      = trim($_POST['descripcion'] ?? '');
        $presup    = floatval($_POST['presupuesto_referencial'] ?? 0);
        if (!$id || !$id_inst) { header("Location: $redirect?error=Datos+inválidos"); exit; }
        try {
            $stmt = $pdo->prepare("UPDATE proceso_compra SET id_institucion=?, tipo_proceso=?, fecha_inicio=?, estado=?, descripcion=?, presupuesto_referencial=? WHERE id_proceso=?");
            $stmt->execute([$id_inst, $tipo, $fecha, $estado, $desc, $presup ?: null, $id]);
            header("Location: $redirect?success=Proceso+actualizado+exitosamente"); exit;
        } catch (PDOException $e) {
            header("Location: $redirect?error=" . urlencode($e->getMessage())); exit;
        }
        break;

    case 'eliminar':
        if (!isAdmin()) { header("Location: $redirect?error=Solo+administradores+pueden+eliminar"); exit; }
        $id = intval($_GET['id'] ?? 0);
        try {
            $pdo->prepare("DELETE FROM proceso_compra WHERE id_proceso=?")->execute([$id]);
            header("Location: $redirect?success=Proceso+eliminado+exitosamente"); exit;
        } catch (PDOException $e) {
            header("Location: $redirect?error=No+se+puede+eliminar:+tiene+contratos+asociados"); exit;
        }
        break;

    default:
        header("Location: $redirect"); exit;
}

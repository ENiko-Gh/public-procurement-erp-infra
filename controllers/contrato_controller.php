<?php
// controllers/contrato_controller.php
session_start();
require_once '../config/database.php';
require_once '../includes/session.php';
requireLogin();

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$pdo = getDB();
$redirect = '../views/contratos.php';

switch ($action) {
    case 'crear':
        if (!canEdit()) { header("Location: $redirect?error=Sin+permisos"); exit; }
        $id_proceso  = intval($_POST['id_proceso'] ?? 0);
        $id_prov     = intval($_POST['id_proveedor'] ?? 0);
        $fecha_adj   = trim($_POST['fecha_adjudicacion'] ?? '');
        $monto       = floatval($_POST['monto_contratado'] ?? 0);
        $estado      = trim($_POST['estado'] ?? 'vigente');
        $objeto      = trim($_POST['objeto_contrato'] ?? '');
        if (!$id_proceso || !$id_prov || empty($fecha_adj) || $monto <= 0) {
            header("Location: $redirect?error=Todos+los+campos+obligatorios+deben+completarse"); exit;
        }
        try {
            $stmt = $pdo->prepare("INSERT INTO contrato (id_proceso, id_proveedor, fecha_adjudicacion, monto_contratado, estado, objeto_contrato) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$id_proceso, $id_prov, $fecha_adj, $monto, $estado, $objeto]);
            header("Location: $redirect?success=Contrato+creado+exitosamente"); exit;
        } catch (PDOException $e) {
            header("Location: $redirect?error=" . urlencode($e->getMessage())); exit;
        }
        break;

    case 'actualizar':
        if (!canEdit()) { header("Location: $redirect?error=Sin+permisos"); exit; }
        $id          = intval($_POST['id_contrato'] ?? 0);
        $id_proceso  = intval($_POST['id_proceso'] ?? 0);
        $id_prov     = intval($_POST['id_proveedor'] ?? 0);
        $fecha_adj   = trim($_POST['fecha_adjudicacion'] ?? '');
        $monto       = floatval($_POST['monto_contratado'] ?? 0);
        $estado      = trim($_POST['estado'] ?? '');
        $objeto      = trim($_POST['objeto_contrato'] ?? '');
        if (!$id) { header("Location: $redirect?error=ID+inválido"); exit; }
        try {
            $stmt = $pdo->prepare("UPDATE contrato SET id_proceso=?, id_proveedor=?, fecha_adjudicacion=?, monto_contratado=?, estado=?, objeto_contrato=? WHERE id_contrato=?");
            $stmt->execute([$id_proceso, $id_prov, $fecha_adj, $monto, $estado, $objeto, $id]);
            header("Location: $redirect?success=Contrato+actualizado+exitosamente"); exit;
        } catch (PDOException $e) {
            header("Location: $redirect?error=" . urlencode($e->getMessage())); exit;
        }
        break;

    case 'eliminar':
        if (!isAdmin()) { header("Location: $redirect?error=Solo+administradores+pueden+eliminar"); exit; }
        $id = intval($_GET['id'] ?? 0);
        try {
            $pdo->prepare("DELETE FROM contrato WHERE id_contrato=?")->execute([$id]);
            header("Location: $redirect?success=Contrato+eliminado"); exit;
        } catch (PDOException $e) {
            header("Location: $redirect?error=No+se+puede+eliminar:+tiene+pagos+asociados"); exit;
        }
        break;

    default:
        header("Location: $redirect"); exit;
}

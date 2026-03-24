<?php
// controllers/pago_controller.php
session_start();
require_once '../config/database.php';
require_once '../includes/session.php';
requireLogin();

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$pdo = getDB();
$redirect = '../views/pagos.php';

switch ($action) {
    case 'crear':
        if (!canEdit()) { header("Location: $redirect?error=Sin+permisos"); exit; }
        $id_contrato  = intval($_POST['id_contrato'] ?? 0);
        $fecha_pago   = trim($_POST['fecha_pago'] ?? '');
        $monto        = floatval($_POST['monto_pagado'] ?? 0);
        $descripcion  = trim($_POST['descripcion'] ?? '');
        $comprobante  = trim($_POST['comprobante'] ?? '');
        if (!$id_contrato || empty($fecha_pago) || $monto <= 0) {
            header("Location: $redirect?error=Contrato+fecha+y+monto+son+obligatorios"); exit;
        }
        try {
            $stmt = $pdo->prepare("INSERT INTO pago (id_contrato, fecha_pago, monto_pagado, descripcion, comprobante) VALUES (?,?,?,?,?)");
            $stmt->execute([$id_contrato, $fecha_pago, $monto, $descripcion, $comprobante]);
            header("Location: $redirect?success=Pago+registrado+exitosamente"); exit;
        } catch (PDOException $e) {
            header("Location: $redirect?error=" . urlencode($e->getMessage())); exit;
        }
        break;

    case 'actualizar':
        if (!canEdit()) { header("Location: $redirect?error=Sin+permisos"); exit; }
        $id           = intval($_POST['id_pago'] ?? 0);
        $id_contrato  = intval($_POST['id_contrato'] ?? 0);
        $fecha_pago   = trim($_POST['fecha_pago'] ?? '');
        $monto        = floatval($_POST['monto_pagado'] ?? 0);
        $descripcion  = trim($_POST['descripcion'] ?? '');
        $comprobante  = trim($_POST['comprobante'] ?? '');
        if (!$id) { header("Location: $redirect?error=ID+inválido"); exit; }
        try {
            $stmt = $pdo->prepare("UPDATE pago SET id_contrato=?, fecha_pago=?, monto_pagado=?, descripcion=?, comprobante=? WHERE id_pago=?");
            $stmt->execute([$id_contrato, $fecha_pago, $monto, $descripcion, $comprobante, $id]);
            header("Location: $redirect?success=Pago+actualizado+exitosamente"); exit;
        } catch (PDOException $e) {
            header("Location: $redirect?error=" . urlencode($e->getMessage())); exit;
        }
        break;

    case 'eliminar':
        if (!isAdmin()) { header("Location: $redirect?error=Solo+administradores+pueden+eliminar"); exit; }
        $id = intval($_GET['id'] ?? 0);
        try {
            $pdo->prepare("DELETE FROM pago WHERE id_pago=?")->execute([$id]);
            header("Location: $redirect?success=Pago+eliminado"); exit;
        } catch (PDOException $e) {
            header("Location: $redirect?error=" . urlencode($e->getMessage())); exit;
        }
        break;

    default:
        header("Location: $redirect"); exit;
}

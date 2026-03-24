<?php
// controllers/proveedor_controller.php
session_start();
require_once '../config/database.php';
require_once '../includes/session.php';
requireLogin();

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$pdo = getDB();
$redirect = '../views/proveedores.php';

switch ($action) {
    case 'crear':
        if (!canEdit()) { header("Location: $redirect?error=Sin+permisos"); exit; }
        $razon    = trim($_POST['razon_social'] ?? '');
        $num_id   = trim($_POST['numero_identificacion'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $correo   = trim($_POST['correo'] ?? '');
        $direccion= trim($_POST['direccion'] ?? '');
        if (empty($razon) || empty($num_id)) {
            header("Location: $redirect?error=Razón+social+y+número+de+identificación+son+obligatorios"); exit;
        }
        try {
            $stmt = $pdo->prepare("INSERT INTO proveedor (razon_social, numero_identificacion, telefono, correo, direccion) VALUES (?,?,?,?,?)");
            $stmt->execute([$razon, $num_id, $telefono, $correo, $direccion]);
            header("Location: $redirect?success=Proveedor+creado+exitosamente"); exit;
        } catch (PDOException $e) {
            $msg = str_contains($e->getMessage(), 'Duplicate') ? 'El número de identificación ya existe' : $e->getMessage();
            header("Location: $redirect?error=" . urlencode($msg)); exit;
        }
        break;

    case 'actualizar':
        if (!canEdit()) { header("Location: $redirect?error=Sin+permisos"); exit; }
        $id       = intval($_POST['id_proveedor'] ?? 0);
        $razon    = trim($_POST['razon_social'] ?? '');
        $num_id   = trim($_POST['numero_identificacion'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $correo   = trim($_POST['correo'] ?? '');
        $direccion= trim($_POST['direccion'] ?? '');
        if (!$id || empty($razon)) { header("Location: $redirect?error=Datos+inválidos"); exit; }
        try {
            $stmt = $pdo->prepare("UPDATE proveedor SET razon_social=?, numero_identificacion=?, telefono=?, correo=?, direccion=? WHERE id_proveedor=?");
            $stmt->execute([$razon, $num_id, $telefono, $correo, $direccion, $id]);
            header("Location: $redirect?success=Proveedor+actualizado+exitosamente"); exit;
        } catch (PDOException $e) {
            header("Location: $redirect?error=" . urlencode($e->getMessage())); exit;
        }
        break;

    case 'eliminar':
        if (!isAdmin()) { header("Location: $redirect?error=Solo+administradores+pueden+eliminar"); exit; }
        $id = intval($_GET['id'] ?? 0);
        try {
            $pdo->prepare("DELETE FROM proveedor WHERE id_proveedor=?")->execute([$id]);
            header("Location: $redirect?success=Proveedor+eliminado"); exit;
        } catch (PDOException $e) {
            header("Location: $redirect?error=No+se+puede+eliminar:+tiene+contratos+asociados"); exit;
        }
        break;

    default:
        header("Location: $redirect"); exit;
}

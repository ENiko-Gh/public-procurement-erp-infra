<?php
// controllers/institucion_controller.php
session_start();
require_once '../config/database.php';
require_once '../includes/session.php';
requireLogin();

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$pdo = getDB();
$redirect = '../views/instituciones.php';

switch ($action) {
    case 'crear':
        if (!canEdit()) die('Sin permisos');
        $nombre    = trim($_POST['nombre'] ?? '');
        $telefono  = trim($_POST['telefono'] ?? '');
        $correo    = trim($_POST['correo'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        if (empty($nombre)) {
            header("Location: $redirect?error=El+nombre+es+obligatorio"); exit;
        }
        try {
            $stmt = $pdo->prepare("INSERT INTO institucion_publica (nombre, telefono, correo, direccion) VALUES (?,?,?,?)");
            $stmt->execute([$nombre, $telefono, $correo, $direccion]);
            header("Location: $redirect?success=Institución+creada+exitosamente"); exit;
        } catch (PDOException $e) {
            header("Location: $redirect?error=Error+al+crear:+" . urlencode($e->getMessage())); exit;
        }
        break;

    case 'actualizar':
        if (!canEdit()) die('Sin permisos');
        $id        = intval($_POST['id_institucion'] ?? 0);
        $nombre    = trim($_POST['nombre'] ?? '');
        $telefono  = trim($_POST['telefono'] ?? '');
        $correo    = trim($_POST['correo'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        if (!$id || empty($nombre)) {
            header("Location: $redirect?error=Datos+inválidos"); exit;
        }
        try {
            $stmt = $pdo->prepare("UPDATE institucion_publica SET nombre=?, telefono=?, correo=?, direccion=? WHERE id_institucion=?");
            $stmt->execute([$nombre, $telefono, $correo, $direccion, $id]);
            header("Location: $redirect?success=Institución+actualizada+exitosamente"); exit;
        } catch (PDOException $e) {
            header("Location: $redirect?error=Error+al+actualizar:+" . urlencode($e->getMessage())); exit;
        }
        break;

    case 'eliminar':
        if (!isAdmin()) {
            header("Location: $redirect?error=Solo+el+Administrador+puede+eliminar"); exit;
        }
        $id = intval($_GET['id'] ?? 0);
        if (!$id) { header("Location: $redirect?error=ID+inválido"); exit; }
        try {
            $stmt = $pdo->prepare("DELETE FROM institucion_publica WHERE id_institucion=?");
            $stmt->execute([$id]);
            header("Location: $redirect?success=Institución+eliminada+exitosamente"); exit;
        } catch (PDOException $e) {
            header("Location: $redirect?error=No+se+puede+eliminar:+tiene+procesos+asociados"); exit;
        }
        break;

    default:
        header("Location: $redirect"); exit;
}

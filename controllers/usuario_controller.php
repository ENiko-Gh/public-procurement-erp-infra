<?php
// controllers/usuario_controller.php
session_start();
require_once '../config/database.php';
require_once '../includes/session.php';
requireRole('administrador');

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$pdo = getDB();
$redirect = '../views/usuarios.php';

switch ($action) {
    case 'crear':
        $nombre   = trim($_POST['nombre'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $rol      = trim($_POST['rol'] ?? 'supervisor');
        if (empty($nombre) || empty($email) || empty($password)) {
            header("Location: $redirect?error=Nombre+email+y+contraseña+son+obligatorios"); exit;
        }
        if (strlen($password) < 6) {
            header("Location: $redirect?error=La+contraseña+debe+tener+al+menos+6+caracteres"); exit;
        }
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?,?,?,?)");
            $stmt->execute([$nombre, $email, $hash, $rol]);
            header("Location: $redirect?success=Usuario+creado+exitosamente"); exit;
        } catch (PDOException $e) {
            $msg = str_contains($e->getMessage(), 'Duplicate') ? 'El email ya está en uso' : $e->getMessage();
            header("Location: $redirect?error=" . urlencode($msg)); exit;
        }
        break;

    case 'actualizar':
        $id       = intval($_POST['id_usuario'] ?? 0);
        $nombre   = trim($_POST['nombre'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $rol      = trim($_POST['rol'] ?? '');
        $activo   = intval($_POST['activo'] ?? 1);
        $password = $_POST['password'] ?? '';
        if (!$id || empty($nombre)) { header("Location: $redirect?error=Datos+inválidos"); exit; }
        try {
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, email=?, password=?, rol=?, activo=? WHERE id_usuario=?");
                $stmt->execute([$nombre, $email, $hash, $rol, $activo, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, email=?, rol=?, activo=? WHERE id_usuario=?");
                $stmt->execute([$nombre, $email, $rol, $activo, $id]);
            }
            header("Location: $redirect?success=Usuario+actualizado+exitosamente"); exit;
        } catch (PDOException $e) {
            header("Location: $redirect?error=" . urlencode($e->getMessage())); exit;
        }
        break;

    case 'eliminar':
        $id = intval($_GET['id'] ?? 0);
        if ($id == $_SESSION['user_id']) {
            header("Location: $redirect?error=No+puedes+eliminarte+a+ti+mismo"); exit;
        }
        try {
            $pdo->prepare("DELETE FROM usuarios WHERE id_usuario=?")->execute([$id]);
            header("Location: $redirect?success=Usuario+eliminado"); exit;
        } catch (PDOException $e) {
            header("Location: $redirect?error=" . urlencode($e->getMessage())); exit;
        }
        break;

    default:
        header("Location: $redirect"); exit;
}

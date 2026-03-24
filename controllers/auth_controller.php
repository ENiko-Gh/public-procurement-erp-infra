<?php
// controllers/auth_controller.php
session_start();
require_once '../config/database.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'login') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        header('Location: ../views/login.php?error=Ingrese+email+y+contraseña');
        exit;
    }

    $pdo  = getDB();
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']  = $user['id_usuario'];
        $_SESSION['user_name']= $user['nombre'];
        $_SESSION['user_rol'] = $user['rol'];
        $_SESSION['user_email']=$user['email'];
        header('Location: ../views/dashboard.php');
        exit;
    } else {
        header('Location: ../views/login.php?error=Credenciales+incorrectas');
        exit;
    }
}

if ($action === 'logout') {
    session_destroy();
    header('Location: ../views/login.php?msg=Sesión+cerrada+correctamente');
    exit;
}

header('Location: ../views/login.php');
exit;

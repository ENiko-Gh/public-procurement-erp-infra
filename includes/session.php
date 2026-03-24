<?php
// includes/session.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin()
{
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . getBaseUrl() . '/views/login.php?error=Debe+iniciar+sesión');
        exit;
    }
}

function requireRole($roles)
{
    requireLogin();
    if (!in_array($_SESSION['user_rol'], (array)$roles)) {
        die('<div style="padding:40px;text-align:center;font-family:Arial"><h2>Acceso Denegado</h2><p>No tienes permisos para esta acción.</p><a href="javascript:history.back()">Volver</a></div>');
    }
}

function isAdmin()
{
    return ($_SESSION['user_rol'] ?? '') === 'administrador';
}

function canEdit()
{
    return in_array($_SESSION['user_rol'] ?? '', ['administrador', 'desarrollador']);
}

function getBaseUrl()
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = dirname(dirname($_SERVER['SCRIPT_NAME']));
    return rtrim($protocol . '://' . $host . $script, '/');
}

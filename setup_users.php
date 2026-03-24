<?php
// setup_users.php - Ejecutar UNA VEZ para crear los hashes de contraseñas reales
// Acceder via: http://localhost/compras_publicas/setup_users.php
require_once 'config/database.php';

$users = [
    ['Admin Principal', 'admin@compras.gob.ec', 'admin123', 'administrador'],
    ['Desarrollador TI', 'dev@compras.gob.ec', 'dev123', 'desarrollador'],
    ['Supervisor Control', 'super@compras.gob.ec', 'super123', 'supervisor'],
];

$pdo = getDB();

// Deshabilitar restricciones FK para poder truncar sin orden
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$pdo->exec("TRUNCATE TABLE pago");
$pdo->exec("TRUNCATE TABLE contrato");
$pdo->exec("TRUNCATE TABLE proceso_proveedor");
$pdo->exec("TRUNCATE TABLE proceso_compra");
$pdo->exec("TRUNCATE TABLE institucion_publica");
$pdo->exec("TRUNCATE TABLE proveedor");
$pdo->exec("TRUNCATE TABLE usuarios");
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

foreach ($users as $u) {
    $hash = password_hash($u[2], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?,?,?,?)");
    $stmt->execute([$u[0], $u[1], $hash, $u[3]]);
    echo "&#x2705; Usuario creado: {$u[1]} / {$u[2]} (rol: {$u[3]})<br>";
}

echo "<br><strong>&#x2705; Usuarios creados exitosamente. Ahora puedes iniciar sesión.</strong><br>";
echo "<br><a href='views/login.php'>Ir al Login</a>";

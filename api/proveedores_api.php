<?php
// api/proveedores_api.php - Microservicio REST para Proveedores
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); exit;
}

// Simple API Key auth
$apiKey = $_SERVER['HTTP_AUTHORIZATION'] ?? $_GET['api_key'] ?? '';
$validKey = 'COMPRAS-API-KEY-2024-ESPE';
if ($apiKey !== $validKey && $apiKey !== 'Bearer ' . $validKey) {
    http_response_code(401);
    echo json_encode(['status'=>'error','message'=>'API Key inválida. Incluye: Authorization: COMPRAS-API-KEY-2024-ESPE']);
    exit;
}

require_once '../config/database.php';
$pdo = getDB();

$method = $_SERVER['REQUEST_METHOD'];
$id = intval($_GET['id'] ?? 0);

// GET /api/proveedores_api.php → lista
// GET /api/proveedores_api.php?id=X → uno
// POST → crear
// PUT /api/proveedores_api.php?id=X → actualizar
// DELETE /api/proveedores_api.php?id=X → eliminar

try {
    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM proveedor WHERE id_proveedor=?");
                $stmt->execute([$id]);
                $row = $stmt->fetch();
                if ($row) {
                    echo json_encode(['status'=>'success','data'=>$row]);
                } else {
                    http_response_code(404);
                    echo json_encode(['status'=>'error','message'=>'Proveedor no encontrado']);
                }
            } else {
                $q = $_GET['q'] ?? '';
                if ($q) {
                    $stmt = $pdo->prepare("SELECT * FROM proveedor WHERE razon_social LIKE ? OR numero_identificacion LIKE ? ORDER BY razon_social");
                    $stmt->execute(["%$q%", "%$q%"]);
                } else {
                    $stmt = $pdo->query("SELECT * FROM proveedor ORDER BY razon_social");
                }
                $rows = $stmt->fetchAll();
                echo json_encode(['status'=>'success','total'=>count($rows),'data'=>$rows]);
            }
            break;

        case 'POST':
            $body = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $razon   = trim($body['razon_social'] ?? '');
            $num_id  = trim($body['numero_identificacion'] ?? '');
            $telefono= trim($body['telefono'] ?? '');
            $correo  = trim($body['correo'] ?? '');
            $dir     = trim($body['direccion'] ?? '');
            if (empty($razon) || empty($num_id)) {
                http_response_code(400);
                echo json_encode(['status'=>'error','message'=>'razon_social y numero_identificacion son requeridos']);
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO proveedor (razon_social, numero_identificacion, telefono, correo, direccion) VALUES (?,?,?,?,?)");
            $stmt->execute([$razon, $num_id, $telefono, $correo, $dir]);
            $newId = $pdo->lastInsertId();
            http_response_code(201);
            echo json_encode(['status'=>'success','message'=>'Proveedor creado','id'=>$newId]);
            break;

        case 'PUT':
            if (!$id) { http_response_code(400); echo json_encode(['status'=>'error','message'=>'Se requiere ?id=X']); exit; }
            $body = json_decode(file_get_contents('php://input'), true) ?? [];
            $razon   = trim($body['razon_social'] ?? '');
            $num_id  = trim($body['numero_identificacion'] ?? '');
            $telefono= trim($body['telefono'] ?? '');
            $correo  = trim($body['correo'] ?? '');
            $dir     = trim($body['direccion'] ?? '');
            if (empty($razon)) { http_response_code(400); echo json_encode(['status'=>'error','message'=>'razon_social requerido']); exit; }
            $stmt = $pdo->prepare("UPDATE proveedor SET razon_social=?, numero_identificacion=?, telefono=?, correo=?, direccion=? WHERE id_proveedor=?");
            $stmt->execute([$razon, $num_id, $telefono, $correo, $dir, $id]);
            echo json_encode(['status'=>'success','message'=>'Proveedor actualizado','rows'=>$stmt->rowCount()]);
            break;

        case 'DELETE':
            if (!$id) { http_response_code(400); echo json_encode(['status'=>'error','message'=>'Se requiere ?id=X']); exit; }
            $stmt = $pdo->prepare("DELETE FROM proveedor WHERE id_proveedor=?");
            $stmt->execute([$id]);
            if ($stmt->rowCount()) {
                echo json_encode(['status'=>'success','message'=>'Proveedor eliminado']);
            } else {
                http_response_code(404);
                echo json_encode(['status'=>'error','message'=>'Proveedor no encontrado']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['status'=>'error','message'=>'Método no permitido']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    $msg = str_contains($e->getMessage(),'Duplicate') ? 'El número de identificación ya existe' : 'Error de base de datos';
    echo json_encode(['status'=>'error','message'=>$msg]);
}

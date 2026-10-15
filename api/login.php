<?php
/**
 * API Endpoint: Inicio de Sesión
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controllers/AuthController.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Método no permitido.', null, 405);
}

try {
    $correo = $_POST['correo'] ?? '';
    $password = $_POST['password'] ?? '';

    $auth = new AuthController();
    $usuario = $auth->login($correo, $password);

    jsonResponse(true, "¡Bienvenido, {$usuario['nombre_completo']}!", $usuario);
} catch (Exception $e) {
    jsonResponse(false, $e->getMessage(), null, 401);
}

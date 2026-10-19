<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controllers/UsuarioController.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
 jsonResponse(false, 'Método no permitido. Solo se acepta POST.', null, 405);
}

try {
 $controller = new UsuarioController();

 $datos = [
 'nombre_completo' => $_POST['nombre_completo'] ?? '',
 'correo' => $_POST['correo'] ?? '',
 'password' => $_POST['password'] ?? '',
 'rol' => $_POST['rol'] ?? 'operador'
 ];

 $resultado = $controller->crear($datos);

 jsonResponse(true, 'Usuario registrado exitosamente', $resultado, 201);
} catch (Exception $e) {
 jsonResponse(false, $e->getMessage(), null, 400);
}

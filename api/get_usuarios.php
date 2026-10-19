<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controllers/UsuarioController.php';

header('Content-Type: application/json; charset=utf-8');

try {
 $controller = new UsuarioController();
 $usuarios = $controller->listar();

 jsonResponse(true, 'Usuarios obtenidos correctamente', $usuarios);
} catch (Exception $e) {
 jsonResponse(false, $e->getMessage(), null, 400);
}

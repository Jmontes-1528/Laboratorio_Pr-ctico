<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controllers/UsuarioController.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
 jsonResponse(false, 'Método no permitido. Solo se acepta POST.', null, 405);
}

try {
 $controller = new UsuarioController();

 $idUsuario = isset($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : 0;
 if ($idUsuario <= 0) {
 throw new Exception("ID de usuario no proporcionado.");
 }

 $controller->toggleEstado($idUsuario);

 jsonResponse(true, 'Estado del usuario modificado con éxito');
} catch (Exception $e) {
 jsonResponse(false, $e->getMessage(), null, 400);
}

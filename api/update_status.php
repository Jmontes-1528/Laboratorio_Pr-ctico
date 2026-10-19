<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controllers/DocumentoController.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
 jsonResponse(false, 'Método no permitido. Solo se acepta POST.', null, 405);
}

try {
 $controller = new DocumentoController();

 $idDocumento = isset($_POST['id_documento']) ? (int)$_POST['id_documento'] : 0;
 $nuevoEstatus = isset($_POST['id_estatus']) ? (int)$_POST['id_estatus'] : 0;
 $observacion = $_POST['observacion'] ?? '';

 if ($idDocumento <= 0 || $nuevoEstatus <= 0) {
 throw new Exception("Parámetros incompletos para actualizar el estatus.");
 }

 $controller->actualizarEstatus($idDocumento, $nuevoEstatus, $observacion);

 jsonResponse(true, 'Estatus actualizado correctamente');
} catch (Exception $e) {
 jsonResponse(false, $e->getMessage(), null, 400);
}

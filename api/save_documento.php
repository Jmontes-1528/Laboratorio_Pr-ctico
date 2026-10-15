<?php
/**
 * API Endpoint: Registrar un nuevo documento
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controllers/DocumentoController.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Método no permitido. Solo se acepta POST.', null, 405);
}

try {
    $controller = new DocumentoController();

    $datos = [
        'no_oficio'     => $_POST['no_oficio'] ?? '',
        'fecha'         => $_POST['fecha'] ?? '',
        'asunto'        => $_POST['asunto'] ?? '',
        'remitente'     => $_POST['remitente'] ?? '',
        'destinatario'  => $_POST['destinatario'] ?? '',
        'estatus'       => $_POST['estatus'] ?? 1,
        'id_tipo'       => $_POST['id_tipo'] ?? 1,
        'observaciones' => $_POST['observaciones'] ?? ''
    ];

    $archivo = $_FILES['archivo_pdf'] ?? ($_FILES['archivo_camara'] ?? null);

    $resultado = $controller->registrar($datos, $archivo);

    jsonResponse(true, 'Documento registrado y digitalizado con éxito', $resultado, 201);
} catch (Exception $e) {
    jsonResponse(false, $e->getMessage(), null, 400);
}

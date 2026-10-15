<?php
/**
 * API Endpoint: Obtener listado de documentos con filtros y paginación
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controllers/DocumentoController.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $controller = new DocumentoController();

    $filtros = [
        'q'          => $_GET['q'] ?? '',
        'estatus'    => $_GET['estatus'] ?? '',
        'desde'      => $_GET['desde'] ?? '',
        'hasta'      => $_GET['hasta'] ?? '',
        'pagina'     => isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1,
        'por_pagina' => isset($_GET['por_pagina']) ? (int)$_GET['por_pagina'] : 10
    ];

    $resultado = $controller->listar($filtros);

    jsonResponse(true, 'Documentos recuperados exitosamente', $resultado);
} catch (Exception $e) {
    jsonResponse(false, $e->getMessage(), null, 400);
}

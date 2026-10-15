<?php
/**
 * API Endpoint: Obtener catálogos para selects y autocompletados
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controllers/DocumentoController.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $controller = new DocumentoController();
    $catalogos = $controller->getCatalogos();

    jsonResponse(true, 'Catálogos cargados exitosamente', $catalogos);
} catch (Exception $e) {
    jsonResponse(false, $e->getMessage(), null, 400);
}

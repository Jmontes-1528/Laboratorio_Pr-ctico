<?php
/**
 * API Endpoint: Obtener métricas y datos para reportes y dashboard
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controllers/ReporteController.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $controller = new ReporteController();

    $filtros = [
        'desde' => $_GET['desde'] ?? '',
        'hasta' => $_GET['hasta'] ?? ''
    ];

    $metricas = $controller->obtenerMetricas($filtros);

    jsonResponse(true, 'Métricas generadas exitosamente', $metricas);
} catch (Exception $e) {
    jsonResponse(false, $e->getMessage(), null, 400);
}

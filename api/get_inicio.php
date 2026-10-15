<?php
/**
 * API Endpoint: Datos para la pantalla de Inicio / Dashboard
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $db = Database::getConnection();

    // 1. Contadores rápidos de estatus
    $stmtKpis = $db->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN id_estatus = 1 THEN 1 ELSE 0 END) as recibidos,
            SUM(CASE WHEN id_estatus = 2 THEN 1 ELSE 0 END) as en_proceso,
            SUM(CASE WHEN id_estatus = 3 THEN 1 ELSE 0 END) as atendidos
        FROM documentos
    ");
    $kpis = $stmtKpis->fetch();

    // 2. Últimos 5 documentos registrados
    $stmtDocs = $db->query("
        SELECT * FROM v_documentos_completos 
        ORDER BY fecha_recepcion DESC, id_documento DESC 
        LIMIT 5
    ");
    $ultimosDocs = $stmtDocs->fetchAll();

    // 3. Actividad reciente en bitácora (últimos 5 seguimientos)
    $stmtAct = $db->query("
        SELECT 
            h.id_historial,
            h.fecha_cambio,
            h.observacion,
            d.no_oficio,
            d.asunto,
            e.nombre as estatus_nuevo,
            e.color_badge,
            COALESCE(u.nombre_completo, 'Sistema') as usuario_nombre
        FROM historial_seguimiento h
        INNER JOIN documentos d ON h.id_documento = d.id_documento
        INNER JOIN estatus_documento e ON h.id_estatus_nuevo = e.id_estatus
        LEFT JOIN usuarios u ON h.id_usuario = u.id_usuario
        ORDER BY h.fecha_cambio DESC
        LIMIT 5
    ");
    $actividad = $stmtAct->fetchAll();

    jsonResponse(true, 'Datos de inicio obtenidos con éxito', [
        'kpis'               => $kpis,
        'ultimos_documentos' => $ultimosDocs,
        'actividad_reciente' => $actividad
    ]);
} catch (Exception $e) {
    jsonResponse(false, $e->getMessage(), null, 400);
}

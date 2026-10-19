<?php

date_default_timezone_set('America/Mexico_City');

if (session_status() === PHP_SESSION_NONE) {
 session_start();
}

define('APP_NAME', 'Sistema de Gestión de Documentos - UNACH');
define('APP_VERSION', '1.0.0');

define('ROOT_PATH', realpath(__DIR__ . '/..') . '/');

define('UPLOAD_DIR', ROOT_PATH . 'uploads/documentos/');

if (!is_dir(UPLOAD_DIR)) {
 mkdir(UPLOAD_DIR, 0777, true);
}

define('MAX_FILE_SIZE_BYTES', 15 * 1024 * 1024);
define('ALLOWED_MIME_TYPES', [
 'application/pdf',
 'image/jpeg',
 'image/png',
 'image/webp'
]);

define('SQL_FROM_DOCUMENTOS_COMPLETOS', "
 (SELECT
 d.id_documento,
 d.no_oficio,
 d.fecha_documento,
 d.fecha_recepcion,
 d.asunto,
 d.id_tipo,
 COALESCE(td.nombre, 'General') AS tipo_documento,
 d.id_remitente,
 r.nombre_remitente AS remitente,
 r.institucion_dependencia AS remitente_institucion,
 d.id_destinatario,
 des.nombre_destinatario AS destinatario,
 des.area_departamento AS destinatario_area,
 e.id_estatus,
 e.nombre AS estatus,
 e.color_badge AS estatus_color,
 d.id_usuario_registro,
 COALESCE(u.nombre_completo, 'Sistema') AS registrado_por,
 d.observaciones,
 a.id_archivo,
 a.ruta_archivo AS archivo_pdf,
 a.nombre_original AS archivo_nombre_original,
 d.creado_en,
 d.actualizado_en
 FROM documentos d
 INNER JOIN estatus_documento e ON d.id_estatus = e.id_estatus
 LEFT JOIN tipos_documento td ON d.id_tipo = td.id_tipo
 INNER JOIN remitentes r ON d.id_remitente = r.id_remitente
 INNER JOIN destinatarios des ON d.id_destinatario = des.id_destinatario
 LEFT JOIN usuarios u ON d.id_usuario_registro = u.id_usuario
 LEFT JOIN archivos_documento a ON d.id_documento = a.id_documento AND a.es_principal = 1) AS v_documentos_completos
");

function jsonResponse(bool $success, string $message, $data = null, int $statusCode = 200): void {
 header('Content-Type: application/json; charset=utf-8');
 http_response_code($statusCode);
 echo json_encode([
 'success' => $success,
 'message' => $message,
 'data' => $data,
 'timestamp' => date('c')
 ], JSON_UNESCAPED_UNICODE);
 exit;
}

function cleanInput(?string $data): string {
 if ($data === null) {
 return '';
 }
 return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

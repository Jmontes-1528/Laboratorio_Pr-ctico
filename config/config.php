<?php
/**
 * ====================================================================
 * Configuración General del Sistema
 * Universidad Autónoma de Chiapas (UNACH)
 * ====================================================================
 */

// Zona horaria
date_default_timezone_set('America/Mexico_City');

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de rutas
define('APP_NAME', 'Sistema de Gestión de Documentos - UNACH');
define('APP_VERSION', '1.0.0');

// Directorio raíz del proyecto en el servidor
define('ROOT_PATH', realpath(__DIR__ . '/..') . '/');

// Directorio de almacenamiento de PDFs
define('UPLOAD_DIR', ROOT_PATH . 'uploads/documentos/');

// Asegurar que la carpeta de uploads exista y tenga permisos
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

// Límites de subida
define('MAX_FILE_SIZE_BYTES', 15 * 1024 * 1024); // 15 Megabytes
define('ALLOWED_MIME_TYPES', [
    'application/pdf',
    'image/jpeg',
    'image/png',
    'image/webp'
]);

/**
 * Función para responder de manera estándar en JSON para peticiones AJAX
 */
function jsonResponse(bool $success, string $message, $data = null, int $statusCode = 200): void {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($statusCode);
    echo json_encode([
        'success'   => $success,
        'message'   => $message,
        'data'      => $data,
        'timestamp' => date('c')
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Limpieza y sanitización básica de entradas de texto
 */
function cleanInput(?string $data): string {
    if ($data === null) {
        return '';
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

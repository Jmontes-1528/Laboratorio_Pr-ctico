<?php
/**
 * ====================================================================
 * Configuración de Conexión a Base de Datos (PDO)
 * Universidad Autónoma de Chiapas (UNACH)
 * ====================================================================
 * Compatible con XAMPP por defecto:
 * Host: localhost | Puerto: 3306 | Usuario: root | Password: "" (vacío)
 * Base de datos: gestion_documentos
 */

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            // Permite variables de entorno (ideal para despliegue en la nube) o valores locales de XAMPP
            $host     = getenv('DB_HOST')     ?: '127.0.0.1';
            $port     = getenv('DB_PORT')     ?: '3306';
            $dbname   = getenv('DB_NAME')     ?: 'gestion_documentos';
            $username = getenv('DB_USER')     ?: 'root';
            $password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];

            try {
                self::$instance = new PDO($dsn, $username, $password, $options);
            } catch (PDOException $e) {
                // Registrar error en log sin exponer credenciales al cliente
                error_log("Error de conexión a BD: " . $e->getMessage());
                
                // Si es petición AJAX/JSON responder en JSON
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json; charset=utf-8');
                    http_response_code(500);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'No se pudo conectar a la base de datos de XAMPP. Verifica que MySQL esté activo en el panel de control.'
                    ]);
                    exit;
                }

                die("<h3>Error de conexión a la Base de Datos</h3><p>Asegúrate de que MySQL esté iniciado en XAMPP y que hayas importado la base de datos <code>gestion_documentos</code>.</p><p>Detalle: " . htmlspecialchars($e->getMessage()) . "</p>");
            }
        }

        return self::$instance;
    }
}

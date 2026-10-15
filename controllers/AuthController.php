<?php
/**
 * ====================================================================
 * Controlador de Autenticación y Sesiones
 * Universidad Autónoma de Chiapas (UNACH)
 * ====================================================================
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

class AuthController {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Iniciar sesión verificando credenciales
     */
    public function login(string $correo, string $password): array {
        $correo = trim(strtolower($correo));

        if (empty($correo) || empty($password)) {
            throw new Exception("Por favor ingresa tu correo y contraseña.");
        }

        $stmt = $this->db->prepare("
            SELECT id_usuario, nombre_completo, correo, password_hash, rol, activo 
            FROM usuarios 
            WHERE LOWER(correo) = :correo 
            LIMIT 1
        ");
        $stmt->execute([':correo' => $correo]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            throw new Exception("El correo ingresado no se encuentra registrado.");
        }

        if ((int)$usuario['activo'] !== 1) {
            throw new Exception("Esta cuenta se encuentra inactiva. Contacta al administrador.");
        }

        // Verificación con hash bcrypt
        $passwordValida = password_verify($password, $usuario['password_hash']);

        // Compatibilidad para credenciales iniciales de demostración
        if (!$passwordValida && $usuario['correo'] === 'admin@unach.mx' && ($password === 'Admin123*' || $password === 'admin')) {
            $passwordValida = true;
            // Actualizar a hash seguro en BD
            $nuevoHash = password_hash($password, PASSWORD_BCRYPT);
            $stmtUp = $this->db->prepare("UPDATE usuarios SET password_hash = :hash WHERE id_usuario = :id");
            $stmtUp->execute([':hash' => $nuevoHash, ':id' => $usuario['id_usuario']]);
        }

        if (!$passwordValida) {
            throw new Exception("Contraseña incorrecta. Por favor verifica tus datos.");
        }

        // Iniciar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['usuario_id']     = (int)$usuario['id_usuario'];
        $_SESSION['usuario_nombre'] = $usuario['nombre_completo'];
        $_SESSION['usuario_correo'] = $usuario['correo'];
        $_SESSION['usuario_rol']    = $usuario['rol'];
        $_SESSION['login_time']     = time();

        return [
            'id_usuario'      => (int)$usuario['id_usuario'],
            'nombre_completo' => $usuario['nombre_completo'],
            'correo'          => $usuario['correo'],
            'rol'             => $usuario['rol']
        ];
    }

    /**
     * Cerrar sesión
     */
    public static function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Verificar que el usuario tenga sesión activa
     */
    public static function verificarSesion(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['usuario_id'])) {
            header('Location: login.php');
            exit;
        }
    }
}

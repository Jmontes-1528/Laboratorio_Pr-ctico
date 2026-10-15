<?php
/**
 * ====================================================================
 * Controlador de Usuarios
 * Universidad Autónoma de Chiapas (UNACH)
 * ====================================================================
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

class UsuarioController {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Listar todos los usuarios
     */
    public function listar(): array {
        $stmt = $this->db->query("
            SELECT id_usuario, nombre_completo, correo, rol, activo, creado_en
            FROM usuarios
            ORDER BY id_usuario ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Crear un nuevo usuario en ventanilla o administrativo
     */
    public function crear(array $datos): array {
        $nombre = cleanInput($datos['nombre_completo'] ?? '');
        $correo = filter_var($datos['correo'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $datos['password'] ?? '';
        $rol = in_array($datos['rol'] ?? '', ['admin', 'operador']) ? $datos['rol'] : 'operador';

        if (empty($nombre)) {
            throw new Exception("El nombre completo del usuario es obligatorio.");
        }
        if (!$correo) {
            throw new Exception("El correo electrónico proporcionado no es válido.");
        }
        if (strlen($password) < 4) {
            throw new Exception("La contraseña debe contener al menos 4 caracteres.");
        }

        // Verificar si el correo ya está registrado
        $stmtCheck = $this->db->prepare("SELECT id_usuario FROM usuarios WHERE correo = :correo LIMIT 1");
        $stmtCheck->execute([':correo' => $correo]);
        if ($stmtCheck->fetch()) {
            throw new Exception("Ya existe un usuario registrado con el correo '{$correo}'.");
        }

        // Cifrado de contraseña con BCRYPT
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->db->prepare("
            INSERT INTO usuarios (nombre_completo, correo, password_hash, rol, activo)
            VALUES (:nom, :correo, :hash, :rol, 1)
        ");
        $stmt->execute([
            ':nom'    => $nombre,
            ':correo' => $correo,
            ':hash'   => $hash,
            ':rol'    => $rol
        ]);

        return [
            'id_usuario'      => (int)$this->db->lastInsertId(),
            'nombre_completo' => $nombre,
            'correo'          => $correo,
            'rol'             => $rol
        ];
    }

    /**
     * Alternar estado de activación (Activo / Inactivo)
     */
    public function toggleEstado(int $idUsuario): bool {
        if ($idUsuario === 1) {
            throw new Exception("El usuario Administrador principal no puede ser desactivado.");
        }

        $stmt = $this->db->prepare("SELECT activo FROM usuarios WHERE id_usuario = :id LIMIT 1");
        $stmt->execute([':id' => $idUsuario]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            throw new Exception("El usuario no existe.");
        }

        $nuevoEstado = $usuario['activo'] ? 0 : 1;

        $stmtUpdate = $this->db->prepare("UPDATE usuarios SET activo = :est WHERE id_usuario = :id");
        $stmtUpdate->execute([
            ':est' => $nuevoEstado,
            ':id'  => $idUsuario
        ]);

        return true;
    }
}

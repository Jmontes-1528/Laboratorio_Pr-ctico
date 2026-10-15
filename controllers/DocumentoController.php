<?php
/**
 * ====================================================================
 * Controlador de Documentos
 * Universidad Autónoma de Chiapas (UNACH)
 * ====================================================================
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

class DocumentoController {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Obtiene los catálogos necesarios para los formularios y filtros
     */
    public function getCatalogos(): array {
        $estatus = $this->db->query("SELECT id_estatus, nombre, color_badge FROM estatus_documento ORDER BY orden ASC")->fetchAll();
        $tipos = $this->db->query("SELECT id_tipo, nombre FROM tipos_documento ORDER BY nombre ASC")->fetchAll();
        $remitentes = $this->db->query("SELECT id_remitente, nombre_remitente, institucion_dependencia FROM remitentes ORDER BY nombre_remitente ASC")->fetchAll();
        $destinatarios = $this->db->query("SELECT id_destinatario, nombre_destinatario, area_departamento FROM destinatarios ORDER BY nombre_destinatario ASC")->fetchAll();

        return [
            'estatus'       => $estatus,
            'tipos'         => $tipos,
            'remitentes'    => $remitentes,
            'destinatarios' => $destinatarios
        ];
    }

    /**
     * Listado con filtros combinados y paginación
     */
    public function listar(array $filtros = []): array {
        $where = [];
        $params = [];

        // 1. Filtro de búsqueda general (No. de Oficio, Asunto, Remitente o Destinatario)
        if (!empty($filtros['q'])) {
            $term = '%' . trim($filtros['q']) . '%';
            $where[] = "(no_oficio LIKE :q1 OR asunto LIKE :q2 OR remitente LIKE :q3 OR remitente_institucion LIKE :q4 OR destinatario LIKE :q5 OR destinatario_area LIKE :q6)";
            $params[':q1'] = $term;
            $params[':q2'] = $term;
            $params[':q3'] = $term;
            $params[':q4'] = $term;
            $params[':q5'] = $term;
            $params[':q6'] = $term;
        }

        // 2. Filtro por estatus
        if (!empty($filtros['estatus']) && $filtros['estatus'] !== 'todos') {
            if (is_numeric($filtros['estatus'])) {
                $where[] = "id_estatus = :id_estatus";
                $params[':id_estatus'] = (int)$filtros['estatus'];
            } else {
                $where[] = "estatus = :nom_estatus";
                $params[':nom_estatus'] = trim($filtros['estatus']);
            }
        }

        // 3. Filtro por rango de fechas (Desde / Hasta)
        if (!empty($filtros['desde'])) {
            $where[] = "fecha_recepcion >= :desde";
            $params[':desde'] = $filtros['desde'];
        }

        if (!empty($filtros['hasta'])) {
            $where[] = "fecha_recepcion <= :hasta";
            $params[':hasta'] = $filtros['hasta'];
        }

        $sqlWhere = count($where) > 0 ? " WHERE " . implode(" AND ", $where) : "";

        // Contar total para paginación
        $countStmt = $this->db->prepare("SELECT COUNT(*) as total FROM v_documentos_completos" . $sqlWhere);
        $countStmt->execute($params);
        $totalRegistros = (int)$countStmt->fetchColumn();

        // Paginación
        $pagina = isset($filtros['pagina']) && (int)$filtros['pagina'] > 0 ? (int)$filtros['pagina'] : 1;
        $porPagina = isset($filtros['por_pagina']) && (int)$filtros['por_pagina'] > 0 ? (int)$filtros['por_pagina'] : 10;
        $offset = ($pagina - 1) * $porPagina;

        // Consulta de datos ordenados cronológicamente
        $sql = "SELECT * FROM v_documentos_completos" . $sqlWhere . " ORDER BY fecha_recepcion DESC, id_documento DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $registros = $stmt->fetchAll();

        return [
            'total'       => $totalRegistros,
            'pagina'      => $pagina,
            'por_pagina'  => $porPagina,
            'total_paginas' => ceil($totalRegistros / $porPagina),
            'documentos'  => $registros
        ];
    }

    /**
     * Obtener un solo documento por ID con su historial y archivo
     */
    public function obtenerPorId(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM v_documentos_completos WHERE id_documento = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $documento = $stmt->fetch();

        if (!$documento) {
            return null;
        }

        // Obtener historial de seguimiento
        $stmtHist = $this->db->prepare("
            SELECT h.*, e.nombre as estatus_nombre, e.color_badge, u.nombre_completo as usuario_nombre
            FROM historial_seguimiento h
            INNER JOIN estatus_documento e ON h.id_estatus_nuevo = e.id_estatus
            LEFT JOIN usuarios u ON h.id_usuario = u.id_usuario
            WHERE h.id_documento = :id
            ORDER BY h.fecha_cambio ASC
        ");
        $stmtHist->execute([':id' => $id]);
        $documento['historial'] = $stmtHist->fetchAll();

        return $documento;
    }

    /**
     * Registrar un nuevo documento
     */
    public function registrar(array $datos, ?array $archivo = null): array {
        // Validaciones básicas requeridas por el proyecto
        if (empty($datos['no_oficio'])) {
            throw new Exception("El número o folio de oficio es obligatorio.");
        }
        if (empty($datos['fecha'])) {
            throw new Exception("La fecha de recepción es obligatoria.");
        }
        if (empty($datos['asunto'])) {
            throw new Exception("El asunto del documento es obligatorio.");
        }
        if (empty($datos['remitente'])) {
            throw new Exception("El remitente es obligatorio.");
        }
        if (empty($datos['destinatario'])) {
            throw new Exception("El destinatario es obligatorio.");
        }

        $this->db->beginTransaction();

        try {
            // 1. Resolver o crear Remitente
            $idRemitente = $this->resolverRemitente($datos['remitente']);

            // 2. Resolver o crear Destinatario
            $idDestinatario = $this->resolverDestinatario($datos['destinatario']);

            // 3. Resolver Estatus (Por defecto 1 = Recibido)
            $idEstatus = !empty($datos['estatus']) ? (int)$datos['estatus'] : 1;

            // 4. Tipo de documento opcional
            $idTipo = !empty($datos['id_tipo']) ? (int)$datos['id_tipo'] : 1;

            // 5. Insertar documento
            $stmt = $this->db->prepare("
                INSERT INTO documentos 
                (no_oficio, fecha_documento, fecha_recepcion, asunto, id_tipo, id_remitente, id_destinatario, id_estatus, id_usuario_registro, observaciones)
                VALUES
                (:no_oficio, :fecha_doc, :fecha_rec, :asunto, :id_tipo, :id_rem, :id_des, :id_estatus, :id_usr, :obs)
            ");

            $stmt->execute([
                ':no_oficio'   => trim($datos['no_oficio']),
                ':fecha_doc'   => $datos['fecha'],
                ':fecha_rec'   => $datos['fecha'],
                ':asunto'      => trim($datos['asunto']),
                ':id_tipo'     => $idTipo,
                ':id_rem'      => $idRemitente,
                ':id_des'      => $idDestinatario,
                ':id_estatus'  => $idEstatus,
                ':id_usr'      => !empty($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 1,
                ':obs'         => !empty($datos['observaciones']) ? trim($datos['observaciones']) : null
            ]);

            $idDocumento = (int)$this->db->lastInsertId();

            // 6. Procesar archivo PDF / foto digitalizada
            $rutaArchivo = 'uploads/documentos/demo_001.pdf'; // Fallback por defecto si no se adjunta
            $nombreOriginal = 'Documento_Recepcion.pdf';

            if ($archivo && isset($archivo['tmp_name']) && is_uploaded_file($archivo['tmp_name'])) {
                $infoArchivo = $this->guardarArchivoDigitalizado($archivo, $datos['no_oficio']);
                $rutaArchivo = $infoArchivo['ruta_relativa'];
                $nombreOriginal = $infoArchivo['nombre_original'];

                $stmtArch = $this->db->prepare("
                    INSERT INTO archivos_documento 
                    (id_documento, nombre_original, nombre_servidor, ruta_archivo, tipo_mime, tamano_bytes, es_principal)
                    VALUES 
                    (:id_doc, :nom_orig, :nom_serv, :ruta, :mime, :tamano, 1)
                ");
                $stmtArch->execute([
                    ':id_doc'    => $idDocumento,
                    ':nom_orig'  => $nombreOriginal,
                    ':nom_serv'  => $infoArchivo['nombre_servidor'],
                    ':ruta'      => $rutaArchivo,
                    ':mime'      => $infoArchivo['tipo_mime'],
                    ':tamano'    => $infoArchivo['tamano_bytes']
                ]);
            } else {
                // Registrar referencia al archivo digitalizado básico
                $stmtArch = $this->db->prepare("
                    INSERT INTO archivos_documento 
                    (id_documento, nombre_original, nombre_servidor, ruta_archivo, tipo_mime, tamano_bytes, es_principal)
                    VALUES 
                    (:id_doc, :nom_orig, 'demo_001.pdf', :ruta, 'application/pdf', 145200, 1)
                ");
                $stmtArch->execute([
                    ':id_doc'   => $idDocumento,
                    ':nom_orig' => "Oficio_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $datos['no_oficio']) . ".pdf",
                    ':ruta'     => $rutaArchivo
                ]);
            }

            // 7. Insertar en bitácora/historial de seguimiento
            $stmtHist = $this->db->prepare("
                INSERT INTO historial_seguimiento 
                (id_documento, id_estatus_anterior, id_estatus_nuevo, observacion, id_usuario)
                VALUES 
                (:id_doc, NULL, :id_est, 'Documento registrado en sistema', :id_usr)
            ");
            $stmtHist->execute([
                ':id_doc' => $idDocumento,
                ':id_est' => $idEstatus,
                ':id_usr' => !empty($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 1
            ]);

            $this->db->commit();

            return [
                'id_documento' => $idDocumento,
                'no_oficio'    => $datos['no_oficio'],
                'archivo'      => $rutaArchivo
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Actualiza el estatus de un documento con trazabilidad
     */
    public function actualizarEstatus(int $idDocumento, int $nuevoEstatus, string $observacion = ''): bool {
        $stmtDoc = $this->db->prepare("SELECT id_estatus FROM documentos WHERE id_documento = :id LIMIT 1");
        $stmtDoc->execute([':id' => $idDocumento]);
        $doc = $stmtDoc->fetch();

        if (!$doc) {
            throw new Exception("El documento con ID {$idDocumento} no existe.");
        }

        $estatusAnterior = (int)$doc['id_estatus'];

        if ($estatusAnterior === $nuevoEstatus) {
            return true; // No hay cambio
        }

        $this->db->beginTransaction();

        try {
            // Actualizar documento
            $stmtUpdate = $this->db->prepare("UPDATE documentos SET id_estatus = :nuevo_estatus WHERE id_documento = :id");
            $stmtUpdate->execute([
                ':nuevo_estatus' => $nuevoEstatus,
                ':id'            => $idDocumento
            ]);

            // Registrar en historial
            $stmtHist = $this->db->prepare("
                INSERT INTO historial_seguimiento 
                (id_documento, id_estatus_anterior, id_estatus_nuevo, observacion, id_usuario)
                VALUES 
                (:id_doc, :ant, :nuevo, :obs, :usr)
            ");
            $stmtHist->execute([
                ':id_doc' => $idDocumento,
                ':ant'    => $estatusAnterior,
                ':nuevo'  => $nuevoEstatus,
                ':obs'    => !empty($observacion) ? trim($observacion) : 'Cambio de estatus administrativo',
                ':usr'    => !empty($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 1
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Resuelve el ID del remitente o lo inserta si es texto libre
     */
    private function resolverRemitente(string $valor): int {
        if (is_numeric($valor)) {
            return (int)$valor;
        }

        $stmt = $this->db->prepare("SELECT id_remitente FROM remitentes WHERE nombre_remitente = :nombre LIMIT 1");
        $stmt->execute([':nombre' => trim($valor)]);
        $id = $stmt->fetchColumn();

        if ($id) {
            return (int)$id;
        }

        // Si no existe, crear registro en catálogo automáticamente
        $insert = $this->db->prepare("INSERT INTO remitentes (nombre_remitente, institucion_dependencia) VALUES (:nombre, 'General')");
        $insert->execute([':nombre' => trim($valor)]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Resuelve el ID del destinatario o lo inserta si es texto libre
     */
    private function resolverDestinatario(string $valor): int {
        if (is_numeric($valor)) {
            return (int)$valor;
        }

        $stmt = $this->db->prepare("SELECT id_destinatario FROM destinatarios WHERE nombre_destinatario = :nombre LIMIT 1");
        $stmt->execute([':nombre' => trim($valor)]);
        $id = $stmt->fetchColumn();

        if ($id) {
            return (int)$id;
        }

        // Si no existe, crear registro en catálogo automáticamente
        $insert = $this->db->prepare("INSERT INTO destinatarios (nombre_destinatario, area_departamento) VALUES (:nombre, 'Área Administrativa')");
        $insert->execute([':nombre' => trim($valor)]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Guarda el archivo digitalizado (PDF o imagen de cámara convertida o guardada)
     */
    private function guardarArchivoDigitalizado(array $archivo, string $noOficio): array {
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error al subir el archivo digitalizado (Código: {$archivo['error']}).");
        }

        if ($archivo['size'] > MAX_FILE_SIZE_BYTES) {
            throw new Exception("El archivo supera el tamaño máximo permitido de 15MB.");
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($archivo['tmp_name']);

        if (!in_array($mime, ALLOWED_MIME_TYPES)) {
            throw new Exception("Tipo de archivo no válido ({$mime}). Solo se aceptan PDFs e imágenes digitales.");
        }

        $ext = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        if (empty($ext)) {
            $ext = ($mime === 'application/pdf') ? 'pdf' : 'jpg';
        }

        // Nombre de archivo seguro
        $prefijo = preg_replace('/[^a-zA-Z0-9_-]/', '_', $noOficio);
        $nombreServidor = 'doc_' . $prefijo . '_' . uniqid() . '.' . strtolower($ext);
        $destino = UPLOAD_DIR . $nombreServidor;

        if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
            throw new Exception("No se pudo mover el archivo digitalizado a la carpeta de almacenamiento.");
        }

        return [
            'nombre_original' => $archivo['name'],
            'nombre_servidor' => $nombreServidor,
            'ruta_relativa'   => 'uploads/documentos/' . $nombreServidor,
            'tipo_mime'       => $mime,
            'tamano_bytes'    => $archivo['size']
        ];
    }
}

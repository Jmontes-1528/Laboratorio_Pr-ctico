-- ====================================================================
-- SISTEMA DE GESTIÓN DE DOCUMENTOS DE UNA OFICINA
-- Universidad Autónoma de Chiapas (UNACH) - ETDA Campus IV
-- Base de Datos Normalizada (3FN) con Índices y Vistas
-- Compatible con MySQL / MariaDB (XAMPP)
-- ====================================================================

CREATE DATABASE IF NOT EXISTS `gestion_documentos` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `gestion_documentos`;

-- Desactivar temporalmente revisión de llaves foráneas para creación limpia
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------------------
-- 1. TABLA: estatus_documento (Catálogo de estados)
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `estatus_documento`;
CREATE TABLE `estatus_documento` (
    `id_estatus` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(50) NOT NULL UNIQUE,
    `color_badge` VARCHAR(30) NOT NULL DEFAULT 'secondary',
    `descripcion` VARCHAR(150) NULL,
    `orden` TINYINT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 2. TABLA: tipos_documento (Catálogo: Oficio, Circular, etc.)
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `tipos_documento`;
CREATE TABLE `tipos_documento` (
    `id_tipo` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(60) NOT NULL UNIQUE,
    `descripcion` VARCHAR(200) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 3. TABLA: remitentes (Personas / Dependencias emisoras)
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `remitentes`;
CREATE TABLE `remitentes` (
    `id_remitente` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre_remitente` VARCHAR(150) NOT NULL,
    `institucion_dependencia` VARCHAR(150) NULL,
    `cargo` VARCHAR(100) NULL,
    `telefono` VARCHAR(30) NULL,
    `correo` VARCHAR(100) NULL,
    `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_remitente_nombre` (`nombre_remitente`),
    INDEX `idx_remitente_inst` (`institucion_dependencia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 4. TABLA: destinatarios (Áreas / Personas receptoras)
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `destinatarios`;
CREATE TABLE `destinatarios` (
    `id_destinatario` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre_destinatario` VARCHAR(150) NOT NULL,
    `area_departamento` VARCHAR(150) NOT NULL,
    `cargo` VARCHAR(100) NULL,
    `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_destinatario_nombre` (`nombre_destinatario`),
    INDEX `idx_destinatario_area` (`area_departamento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 5. TABLA: usuarios (Administradores y operadores del sistema)
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
    `id_usuario` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre_completo` VARCHAR(120) NOT NULL,
    `correo` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `rol` ENUM('admin', 'operador', 'consulta') NOT NULL DEFAULT 'operador',
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 6. TABLA: documentos (Entidad Principal)
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `documentos`;
CREATE TABLE `documentos` (
    `id_documento` INT AUTO_INCREMENT PRIMARY KEY,
    `no_oficio` VARCHAR(60) NOT NULL,
    `fecha_documento` DATE NULL,
    `fecha_recepcion` DATE NOT NULL,
    `asunto` TEXT NOT NULL,
    `id_tipo` INT NULL,
    `id_remitente` INT NOT NULL,
    `id_destinatario` INT NOT NULL,
    `id_estatus` INT NOT NULL DEFAULT 1,
    `id_usuario_registro` INT NULL,
    `observaciones` TEXT NULL,
    `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `actualizado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Restricciones e Índices para optimizar búsquedas y filtros
    INDEX `idx_doc_no_oficio` (`no_oficio`),
    INDEX `idx_doc_fecha_recepcion` (`fecha_recepcion`),
    INDEX `idx_doc_estatus` (`id_estatus`),
    INDEX `idx_doc_remitente` (`id_remitente`),
    INDEX `idx_doc_destinatario` (`id_destinatario`),
    
    -- Llaves Foráneas
    CONSTRAINT `fk_doc_tipo` 
        FOREIGN KEY (`id_tipo`) REFERENCES `tipos_documento` (`id_tipo`) 
        ON UPDATE CASCADE ON DELETE SET NULL,
        
    CONSTRAINT `fk_doc_remitente` 
        FOREIGN KEY (`id_remitente`) REFERENCES `remitentes` (`id_remitente`) 
        ON UPDATE CASCADE ON DELETE RESTRICT,
        
    CONSTRAINT `fk_doc_destinatario` 
        FOREIGN KEY (`id_destinatario`) REFERENCES `destinatarios` (`id_destinatario`) 
        ON UPDATE CASCADE ON DELETE RESTRICT,
        
    CONSTRAINT `fk_doc_estatus` 
        FOREIGN KEY (`id_estatus`) REFERENCES `estatus_documento` (`id_estatus`) 
        ON UPDATE CASCADE ON DELETE RESTRICT,
        
    CONSTRAINT `fk_doc_usuario` 
        FOREIGN KEY (`id_usuario_registro`) REFERENCES `usuarios` (`id_usuario`) 
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 7. TABLA: archivos_documento (Digitalización PDF e imágenes)
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `archivos_documento`;
CREATE TABLE `archivos_documento` (
    `id_archivo` INT AUTO_INCREMENT PRIMARY KEY,
    `id_documento` INT NOT NULL,
    `nombre_original` VARCHAR(255) NOT NULL,
    `nombre_servidor` VARCHAR(255) NOT NULL,
    `ruta_archivo` VARCHAR(255) NOT NULL,
    `tipo_mime` VARCHAR(100) NOT NULL DEFAULT 'application/pdf',
    `tamano_bytes` INT UNSIGNED NOT NULL DEFAULT 0,
    `es_principal` TINYINT(1) NOT NULL DEFAULT 1,
    `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX `idx_archivo_doc` (`id_documento`),
    
    CONSTRAINT `fk_archivo_doc` 
        FOREIGN KEY (`id_documento`) REFERENCES `documentos` (`id_documento`) 
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 8. TABLA: historial_seguimiento (Trazabilidad y auditoría)
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `historial_seguimiento`;
CREATE TABLE `historial_seguimiento` (
    `id_historial` INT AUTO_INCREMENT PRIMARY KEY,
    `id_documento` INT NOT NULL,
    `id_estatus_anterior` INT NULL,
    `id_estatus_nuevo` INT NOT NULL,
    `observacion` TEXT NULL,
    `id_usuario` INT NULL,
    `fecha_cambio` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX `idx_hist_doc` (`id_documento`),
    
    CONSTRAINT `fk_hist_doc` 
        FOREIGN KEY (`id_documento`) REFERENCES `documentos` (`id_documento`) 
        ON UPDATE CASCADE ON DELETE CASCADE,
        
    CONSTRAINT `fk_hist_estatus_ant` 
        FOREIGN KEY (`id_estatus_anterior`) REFERENCES `estatus_documento` (`id_estatus`) 
        ON UPDATE CASCADE ON DELETE SET NULL,
        
    CONSTRAINT `fk_hist_estatus_nuevo` 
        FOREIGN KEY (`id_estatus_nuevo`) REFERENCES `estatus_documento` (`id_estatus`) 
        ON UPDATE CASCADE ON DELETE RESTRICT,
        
    CONSTRAINT `fk_hist_usuario` 
        FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) 
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------------------
-- 9. VISTA: v_documentos_completos (Optimiza las consultas de PHP)
-- --------------------------------------------------------------------
CREATE OR REPLACE VIEW `v_documentos_completos` AS
SELECT 
    d.`id_documento`,
    d.`no_oficio`,
    d.`fecha_documento`,
    d.`fecha_recepcion`,
    d.`asunto`,
    d.`id_tipo`,
    COALESCE(td.`nombre`, 'General') AS `tipo_documento`,
    d.`id_remitente`,
    r.`nombre_remitente` AS `remitente`,
    r.`institucion_dependencia` AS `remitente_institucion`,
    d.`id_destinatario`,
    des.`nombre_destinatario` AS `destinatario`,
    des.`area_departamento` AS `destinatario_area`,
    e.`id_estatus`,
    e.`nombre` AS `estatus`,
    e.`color_badge` AS `estatus_color`,
    d.`id_usuario_registro`,
    COALESCE(u.`nombre_completo`, 'Sistema') AS `registrado_por`,
    d.`observaciones`,
    a.`id_archivo`,
    a.`ruta_archivo` AS `archivo_pdf`,
    a.`nombre_original` AS `archivo_nombre_original`,
    d.`creado_en`,
    d.`actualizado_en`
FROM `documentos` d
INNER JOIN `estatus_documento` e ON d.`id_estatus` = e.`id_estatus`
LEFT JOIN `tipos_documento` td ON d.`id_tipo` = td.`id_tipo`
INNER JOIN `remitentes` r ON d.`id_remitente` = r.`id_remitente`
INNER JOIN `destinatarios` des ON d.`id_destinatario` = des.`id_destinatario`
LEFT JOIN `usuarios` u ON d.`id_usuario_registro` = u.`id_usuario`
LEFT JOIN `archivos_documento` a ON d.`id_documento` = a.`id_documento` AND a.`es_principal` = 1;

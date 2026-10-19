SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `estatus_documento`;
CREATE TABLE `estatus_documento` (
 `id_estatus` INT AUTO_INCREMENT PRIMARY KEY,
 `nombre` VARCHAR(50) NOT NULL UNIQUE,
 `color_badge` VARCHAR(30) NOT NULL DEFAULT 'secondary',
 `descripcion` VARCHAR(150) NULL,
 `orden` TINYINT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tipos_documento`;
CREATE TABLE `tipos_documento` (
 `id_tipo` INT AUTO_INCREMENT PRIMARY KEY,
 `nombre` VARCHAR(60) NOT NULL UNIQUE,
 `descripcion` VARCHAR(200) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

 INDEX `idx_doc_no_oficio` (`no_oficio`),
 INDEX `idx_doc_fecha_recepcion` (`fecha_recepcion`),
 INDEX `idx_doc_estatus` (`id_estatus`),
 INDEX `idx_doc_remitente` (`id_remitente`),
 INDEX `idx_doc_destinatario` (`id_destinatario`),

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

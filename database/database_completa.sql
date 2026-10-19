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

SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM `historial_seguimiento`;
DELETE FROM `archivos_documento`;
DELETE FROM `documentos`;
DELETE FROM `usuarios`;
DELETE FROM `destinatarios`;
DELETE FROM `remitentes`;
DELETE FROM `tipos_documento`;
DELETE FROM `estatus_documento`;
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO `estatus_documento` (`id_estatus`, `nombre`, `color_badge`, `descripcion`, `orden`) VALUES
(1, 'Recibido', 'success', 'Documento ingresado y registrado en la oficina', 1),
(2, 'En proceso', 'warning', 'Documento turnado o en trámite de atención', 2),
(3, 'Atendido', 'info', 'Documento contestado, resuelto o concluido', 3);

INSERT INTO `tipos_documento` (`id_tipo`, `nombre`, `descripcion`) VALUES
(1, 'Oficio', 'Comunicaciones oficiales administrativas'),
(2, 'Circular', 'Avisos o disposiciones emitidas a varias áreas'),
(3, 'Solicitud', 'Peticiones de recursos, permisos o servicios'),
(4, 'Invitación', 'Convocatorias a eventos institucionales'),
(5, 'Reporte', 'Informes de actividades y avances técnicos'),
(6, 'Comunicado', 'Avisos de interés general');

INSERT INTO `remitentes` (`id_remitente`, `nombre_remitente`, `institucion_dependencia`, `cargo`) VALUES
(1, 'Dirección Administrativa', 'UNACH Campus IV', 'Director Administrativo'),
(2, 'Secretaría General', 'UNACH', 'Secretario General'),
(3, 'Departamento de Sistemas', 'ETDA Campus IV', 'Jefe de Sistemas'),
(4, 'Institución Externa', 'Sector Productivo / Empresa Aliada', 'Representante Legal'),
(5, 'C. Municipal de Tapachula', 'H. Ayuntamiento de Tapachula', 'Secretario Particular');

INSERT INTO `destinatarios` (`id_destinatario`, `nombre_destinatario`, `area_departamento`, `cargo`) VALUES
(1, 'Coordinación Académica', 'Escuela de Tecnologías Digitales Aplicadas', 'Coordinador Académico'),
(2, 'Dirección ETDA', 'Escuela de Tecnologías Digitales Aplicadas', 'Director'),
(3, 'Coordinación de Investigación', 'UNACH Campus IV', 'Coordinador de Investigación'),
(4, 'Rectoría', 'Rectoría UNACH', 'Rector');

INSERT INTO `usuarios` (`id_usuario`, `nombre_completo`, `correo`, `password_hash`, `rol`, `activo`) VALUES
(1, 'Administrador del Sistema', 'admin@unach.mx', '$2y$10$wT3F1b6u98P1Q6bY0h2XheN7hA1d7yBsk7qB2vE6Pms9eN3Oa6jUe', 'admin', 1),
(2, 'Recepcionista de Oficina', 'ventanilla@unach.mx', '$2y$10$wT3F1b6u98P1Q6bY0h2XheN7hA1d7yBsk7qB2vE6Pms9eN3Oa6jUe', 'operador', 1);

INSERT INTO `documentos` (`id_documento`, `no_oficio`, `fecha_documento`, `fecha_recepcion`, `asunto`, `id_tipo`, `id_remitente`, `id_destinatario`, `id_estatus`, `id_usuario_registro`, `observaciones`) VALUES
(1, '001/2026', '2026-10-05', '2026-10-05', 'Solicitud de materiales', 3, 1, 1, 1, 1, 'Solicitud para equipo de cómputo y papelería para laboratorios'),
(2, '002/2026', '2026-10-04', '2026-10-04', 'Invitación a evento', 4, 2, 2, 2, 1, 'Inauguración del ciclo de conferencias tecnológicas'),
(3, '003/2026', '2026-10-03', '2026-10-03', 'Reporte de actividades', 5, 3, 2, 3, 1, 'Informe trimestral de soporte y mantenimiento'),
(4, '004/2026', '2026-10-02', '2026-10-02', 'Solicitud de apoyo', 3, 4, 3, 1, 1, 'Convenio para residencias profesionales y servicio social'),
(5, '005/2026', '2026-10-01', '2026-10-01', 'Oficio de colaboración', 1, 5, 4, 2, 1, 'Propuesta de vinculación comunitaria');

INSERT INTO `archivos_documento` (`id_archivo`, `id_documento`, `nombre_original`, `nombre_servidor`, `ruta_archivo`, `tipo_mime`, `tamano_bytes`, `es_principal`) VALUES
(1, 1, 'Oficio_001_2026.pdf', 'doc_670d9a10_001.pdf', 'uploads/documentos/demo_001.pdf', 'application/pdf', 145200, 1),
(2, 2, 'Oficio_002_2026.pdf', 'doc_670d9a20_002.pdf', 'uploads/documentos/demo_002.pdf', 'application/pdf', 189400, 1),
(3, 3, 'Oficio_003_2026.pdf', 'doc_670d9a30_003.pdf', 'uploads/documentos/demo_003.pdf', 'application/pdf', 256100, 1),
(4, 4, 'Oficio_004_2026.pdf', 'doc_670d9a40_004.pdf', 'uploads/documentos/demo_004.pdf', 'application/pdf', 128900, 1),
(5, 5, 'Oficio_005_2026.pdf', 'doc_670d9a50_005.pdf', 'uploads/documentos/demo_005.pdf', 'application/pdf', 214500, 1);

INSERT INTO `historial_seguimiento` (`id_historial`, `id_documento`, `id_estatus_anterior`, `id_estatus_nuevo`, `observacion`, `id_usuario`, `fecha_cambio`) VALUES
(1, 1, NULL, 1, 'Documento recibido y registrado en ventanilla', 1, '2026-10-05 09:15:00'),
(2, 2, NULL, 1, 'Documento recibido en recepción', 1, '2026-10-04 10:30:00'),
(3, 2, 1, 2, 'Turnado a Dirección ETDA para confirmación de asistencia', 1, '2026-10-04 14:00:00'),
(4, 3, NULL, 1, 'Recepción de reporte técnico', 1, '2026-10-03 08:45:00'),
(5, 3, 1, 2, 'Revisión técnica de indicadores', 1, '2026-10-03 11:20:00'),
(6, 3, 2, 3, 'Reporte validado y archivado conforme', 1, '2026-10-03 16:30:00'),
(7, 4, NULL, 1, 'Ingreso de solicitud externa', 1, '2026-10-02 12:10:00'),
(8, 5, NULL, 1, 'Recepción de oficio municipal', 1, '2026-10-01 09:00:00'),
(9, 5, 1, 2, 'Enviado a revisión jurídica en Rectoría', 1, '2026-10-01 13:40:00');

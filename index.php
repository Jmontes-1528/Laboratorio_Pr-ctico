<?php
/**
 * ====================================================================
 * SISTEMA DE GESTIÓN DE DOCUMENTOS DE UNA OFICINA
 * Universidad Autónoma de Chiapas (UNACH)
 * Escuela de Tecnologías Digitales Aplicadas (ETDA) - Campus IV
 * ====================================================================
 */
require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Sistema de Gestión de Documentos · UNACH</title>

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.min.css">

    <!-- Estilos Personalizados UNACH (Mobile-First) -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="app-wrapper">

    <!-- SIDEBAR ESCRITORIO -->
    <?php include_once __DIR__ . '/views/components/sidebar.php'; ?>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">

        <!-- NAVBAR SUPERIOR -->
        <?php include_once __DIR__ . '/views/components/navbar.php'; ?>

        <div class="page-container" id="listado_documentos">

            <!-- CABECERA DEL PANEL -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                <div>
                    <h3 class="fw-bold text-primary mb-1" style="color: var(--unach-blue-primary) !important;">
                        Listado de Documentos
                    </h3>
                    <p class="text-muted small mb-0">
                        Administración, control de oficios y seguimiento de trámites
                    </p>
                </div>
                <button type="button" class="btn btn-primary px-3 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoDocumento" style="background-color: var(--unach-blue-secondary); border-color: var(--unach-blue-secondary);">
                    <i class="bi bi-plus-lg me-1"></i> + Nuevo Documento
                </button>
            </div>

            <!-- BÚSQUEDA Y FILTROS HORIZONTALES (MOCKUP OFICIAL PÁG. 4) -->
            <div class="search-filter-box" id="search_filter_box">
                <!-- Buscador de texto general -->
                <div class="input-group search-input-group mb-3">
                    <span class="input-group-text bg-white text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control" id="input_buscar" placeholder="Buscar por no. de oficio, asunto, remitente o destinatario...">
                    <button class="btn btn-primary" type="button" id="btn_buscar" style="background-color: var(--unach-blue-secondary);">
                        Buscar
                    </button>
                </div>

                <!-- Filtros combinados de Estatus y Fechas -->
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-3">
                        <label for="filtro_estatus" class="form-label small text-muted mb-1 fw-bold">Estatus</label>
                        <select class="form-select form-select-sm" id="filtro_estatus">
                            <option value="todos" selected>Todos</option>
                            <option value="Recibido">Recibido</option>
                            <option value="En proceso">En proceso</option>
                            <option value="Atendido">Atendido</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-3">
                        <label for="filtro_desde" class="form-label small text-muted mb-1 fw-bold">Desde</label>
                        <input type="date" class="form-control form-control-sm" id="filtro_desde">
                    </div>

                    <div class="col-6 col-md-3">
                        <label for="filtro_hasta" class="form-label small text-muted mb-1 fw-bold">Hasta</label>
                        <input type="date" class="form-control form-control-sm" id="filtro_hasta">
                    </div>

                    <div class="col-12 col-md-3 d-flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm flex-grow-1 fw-semibold" id="btn_filtrar" style="background-color: var(--unach-blue-secondary);">
                            <i class="bi bi-funnel-fill me-1"></i> Filtrar
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_limpiar_filtros" title="Limpiar Filtros">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- CONTENEDOR DE TABLA (ESCRITORIO / TABLETA) -->
            <div class="card-custom d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-documents align-middle">
                        <thead>
                            <tr>
                                <th>No. Oficio</th>
                                <th>Fecha</th>
                                <th>Asunto</th>
                                <th>Remitente</th>
                                <th>Destinatario</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla_documentos_body">
                            <!-- Los registros se cargan dinámicamente mediante jQuery AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CONTENEDOR DE TARJETAS (MÓVIL) -->
            <div class="d-md-none" id="lista_documentos_movil">
                <!-- Se llena dinámicamente con tarjetas compactas en pantallas pequeñas -->
            </div>

            <!-- PIE DE TABLA: CONTADOR Y PAGINACIÓN (MOCKUP PÁG. 4) -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-3 px-2">
                <div class="text-muted small" id="info_paginacion">
                    Mostrando 1 a 5 de 5 registros
                </div>
                <nav aria-label="Navegación de documentos">
                    <ul class="pagination pagination-sm mb-0" id="controles_paginacion">
                        <!-- Botones de paginación generados por JS -->
                    </ul>
                </nav>
            </div>

        </div><!-- /.page-container -->

    </main><!-- /.main-content -->

</div><!-- /.app-wrapper -->

<!-- MODALES DEL SISTEMA -->
<?php include_once __DIR__ . '/views/components/modal_nuevo.php'; ?>
<?php include_once __DIR__ . '/views/components/modal_pdf.php'; ?>
<?php include_once __DIR__ . '/views/components/modal_status.php'; ?>

<!-- BARRA DE NAVEGACIÓN INFERIOR PARA MÓVILES -->
<?php include_once __DIR__ . '/views/components/bottom_nav.php'; ?>

<!-- jQuery 3.7.1 -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap 5.3.3 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.all.min.js"></script>

<!-- Lógica de Aplicación Frontend -->
<script src="assets/js/app.js"></script>

</body>
</html>

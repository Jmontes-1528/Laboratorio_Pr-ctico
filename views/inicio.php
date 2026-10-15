<!-- SECCIÓN: INICIO / DASHBOARD PRINCIPAL -->
<div class="view-section" id="seccion_inicio">

    <!-- HERO BANNER DE BIENVENIDA -->
    <div class="card border-0 text-white shadow-sm rounded-4 mb-4 overflow-hidden" 
         style="background: linear-gradient(135deg, var(--unach-blue-primary) 0%, var(--unach-blue-secondary) 70%, #1b4d89 100%);">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-warning text-dark fw-bold mb-2 px-3 py-1 text-uppercase" style="letter-spacing: 0.5px;">
                        <i class="bi bi-mortarboard-fill me-1"></i> UNACH · ETDA Campus IV
                    </span>
                    <h2 class="fw-bold mb-2 text-white">Sistema de Gestión de Documentos</h2>
                    <p class="text-white-50 mb-4" style="max-width: 650px;">
                        Plataforma oficial para la digitalización, control de folios, organización y seguimiento oportuno de oficios y comunicados en ventanilla.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-warning text-dark fw-bold px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoDocumento">
                            <i class="bi bi-plus-circle-fill me-1"></i> Registrar Documento
                        </button>
                        <a href="#documentos" class="btn btn-outline-light px-3 py-2 fw-semibold nav-view-link" data-view="documentos">
                            <i class="bi bi-folder2-open me-1"></i> Ver Todos los Documentos
                        </a>
                        <a href="#reportes" class="btn btn-outline-light px-3 py-2 fw-semibold nav-view-link" data-view="reportes">
                            <i class="bi bi-bar-chart-line me-1"></i> Estadísticas
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-flex justify-content-center">
                    <div class="text-center p-3 rounded-4" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(5px); border: 1px solid rgba(255, 255, 255, 0.2);">
                        <i class="bi bi-file-earmark-check-fill text-warning display-4 mb-2 d-block"></i>
                        <h6 class="fw-bold text-white mb-1">Ventanilla Digital Activa</h6>
                        <small class="text-white-50 d-block" id="inicio_fecha_actual">Tapachula, Chiapas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TARJETAS DE ACCESO RÁPIDO POR ESTATUS -->
    <div class="row g-3 mb-4">
        <!-- Recibidos -->
        <div class="col-6 col-lg-3">
            <div class="card card-custom h-100 p-3 border-start border-success border-4 cursor-pointer hover-card btn-filtro-inicio" 
                 data-estatus="Recibido" title="Clic para ver documentos Recibidos">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Recibidos</div>
                        <h3 class="fw-bold mb-0 text-success" id="inicio_kpi_recibidos">0</h3>
                        <small class="text-muted">En espera de turno</small>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-circle">
                        <i class="bi bi-inbox-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- En Proceso -->
        <div class="col-6 col-lg-3">
            <div class="card card-custom h-100 p-3 border-start border-warning border-4 cursor-pointer hover-card btn-filtro-inicio" 
                 data-estatus="En proceso" title="Clic para ver documentos En proceso">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">En Proceso</div>
                        <h3 class="fw-bold mb-0 text-warning" id="inicio_kpi_proceso">0</h3>
                        <small class="text-muted">En trámite de atención</small>
                    </div>
                    <div class="bg-warning-subtle text-warning p-3 rounded-circle">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Atendidos -->
        <div class="col-6 col-lg-3">
            <div class="card card-custom h-100 p-3 border-start border-info border-4 cursor-pointer hover-card btn-filtro-inicio" 
                 data-estatus="Atendido" title="Clic para ver documentos Atendidos">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Atendidos</div>
                        <h3 class="fw-bold mb-0 text-info" id="inicio_kpi_atendidos">0</h3>
                        <small class="text-muted">Concluidos / Archivados</small>
                    </div>
                    <div class="bg-info-subtle text-info p-3 rounded-circle">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total de Documentos -->
        <div class="col-6 col-lg-3">
            <div class="card card-custom h-100 p-3 border-start border-primary border-4 cursor-pointer hover-card btn-filtro-inicio" 
                 data-estatus="todos" title="Clic para ver todo el archivo">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Total Archivo</div>
                        <h3 class="fw-bold mb-0 text-primary" id="inicio_kpi_total">0</h3>
                        <small class="text-muted">Documentos en sistema</small>
                    </div>
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                        <i class="bi bi-archive-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTENIDO EN DOS COLUMNAS -->
    <div class="row g-4">
        <!-- Columna Izquierda: Últimos 5 Documentos -->
        <div class="col-12 col-lg-8">
            <div class="card-custom h-100">
                <div class="card-custom-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-primary mb-0">
                            <i class="bi bi-clock-fill text-warning me-2"></i> Recepciones Recientes en Ventanilla
                        </h5>
                        <small class="text-muted">Últimos documentos ingresados al sistema</small>
                    </div>
                    <a href="#documentos" class="btn btn-sm btn-outline-primary nav-view-link" data-view="documentos">
                        Ver todos <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-custom-body p-0">
                    <div class="list-group list-group-flush" id="inicio_lista_ultimos_docs">
                        <!-- Se llena dinámicamente -->
                        <div class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                            Cargando recepciones recientes...
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Acciones Rápidas y Bitácora -->
        <div class="col-12 col-lg-4">
            <!-- Acciones Rápidas -->
            <div class="card-custom mb-4">
                <div class="card-custom-header">
                    <h5 class="fw-bold"><i class="bi bi-lightning-charge-fill text-warning me-2"></i> Acciones Directas</h5>
                </div>
                <div class="card-custom-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary text-start d-flex align-items-center gap-2 py-2" data-bs-toggle="modal" data-bs-target="#modalNuevoDocumento" onclick="setTimeout(() => $('#btn_camara').click(), 400);">
                            <i class="bi bi-camera-fill text-primary fs-5"></i>
                            <div>
                                <strong class="d-block">Tomar Fotografía</strong>
                                <small class="text-muted">Digitalizar oficio desde la cámara</small>
                            </div>
                        </button>
                        <button type="button" class="btn btn-outline-primary text-start d-flex align-items-center gap-2 py-2" data-bs-toggle="modal" data-bs-target="#modalNuevoDocumento" onclick="setTimeout(() => $('#btn_archivo_pdf').click(), 400);">
                            <i class="bi bi-cloud-arrow-up-fill text-success fs-5"></i>
                            <div>
                                <strong class="d-block">Cargar Archivo PDF</strong>
                                <small class="text-muted">Subir documento escaneado</small>
                            </div>
                        </button>
                        <a href="#reportes" class="btn btn-outline-primary text-start d-flex align-items-center gap-2 py-2 nav-view-link" data-view="reportes">
                            <i class="bi bi-file-earmark-bar-graph-fill text-info fs-5"></i>
                            <div>
                                <strong class="d-block">Reporte Ejecutivo</strong>
                                <small class="text-muted">Consultar e imprimir resumen</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bitácora de Seguimiento Reciente -->
            <div class="card-custom">
                <div class="card-custom-header">
                    <h5 class="fw-bold"><i class="bi bi-journal-text text-primary me-2"></i> Actividad Reciente</h5>
                </div>
                <div class="card-custom-body p-3">
                    <div class="timeline-activity" id="inicio_lista_actividad">
                        <!-- Se llena dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</div><!-- /#seccion_inicio -->

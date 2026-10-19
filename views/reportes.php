<div class="view-section d-none" id="seccion_reportes">

 <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
 <div>
 <h3 class="fw-bold text-primary mb-1" style="color: var(--unach-blue-primary) !important;">
 <i class="bi bi-bar-chart-line-fill text-warning me-2"></i> Reportes y Métricas de Oficina
 </h3>
 <p class="text-muted small mb-0">
 Indicadores de gestión documental, tiempos de respuesta y volumen de oficios
 </p>
 </div>
 <div class="d-flex gap-2">
 <button type="button" class="btn btn-outline-primary" id="btn_imprimir_reporte" onclick="window.print();">
 <i class="bi bi-printer-fill me-1"></i> Imprimir Reporte
 </button>
 <button type="button" class="btn btn-primary" id="btn_recargar_reportes" style="background-color: var(--unach-blue-secondary);">
 <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
 </button>
 </div>
 </div>

 <div class="card-custom mb-4 p-3 bg-white">
 <div class="row g-2 align-items-end">
 <div class="col-12 col-md-4">
 <label for="reporte_desde" class="form-label small text-muted mb-1 fw-bold">Periodo Desde</label>
 <input type="date" class="form-control form-control-sm" id="reporte_desde">
 </div>
 <div class="col-12 col-md-4">
 <label for="reporte_hasta" class="form-label small text-muted mb-1 fw-bold">Periodo Hasta</label>
 <input type="date" class="form-control form-control-sm" id="reporte_hasta">
 </div>
 <div class="col-12 col-md-4 d-flex gap-2">
 <button type="button" class="btn btn-primary btn-sm flex-grow-1 fw-semibold" id="btn_filtrar_reporte" style="background-color: var(--unach-blue-secondary);">
 <i class="bi bi-funnel-fill me-1"></i> Filtrar Periodo
 </button>
 <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_limpiar_reporte" title="Restablecer Fechas">
 <i class="bi bi-arrow-counterclockwise"></i>
 </button>
 </div>
 </div>
 </div>

 <div class="row g-3 mb-4" id="contenedor_kpis">
 <div class="col-6 col-lg-3">
 <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-primary border-4">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <div class="text-muted small fw-bold text-uppercase">Total Recibidos</div>
 <h2 class="fw-bold mb-0 text-primary" id="kpi_total">0</h2>
 </div>
 <div class="bg-primary-subtle text-primary p-3 rounded-circle">
 <i class="bi bi-folder2-open fs-4"></i>
 </div>
 </div>
 </div>
 </div>

 <div class="col-6 col-lg-3">
 <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-success border-4">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <div class="text-muted small fw-bold text-uppercase">Recibidos</div>
 <h2 class="fw-bold mb-0 text-success" id="kpi_recibidos">0</h2>
 </div>
 <div class="bg-success-subtle text-success p-3 rounded-circle">
 <i class="bi bi-inbox-fill fs-4"></i>
 </div>
 </div>
 </div>
 </div>

 <div class="col-6 col-lg-3">
 <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-warning border-4">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <div class="text-muted small fw-bold text-uppercase">En Proceso</div>
 <h2 class="fw-bold mb-0 text-warning" id="kpi_proceso">0</h2>
 </div>
 <div class="bg-warning-subtle text-warning p-3 rounded-circle">
 <i class="bi bi-clock-history fs-4"></i>
 </div>
 </div>
 </div>
 </div>

 <div class="col-6 col-lg-3">
 <div class="card border-0 shadow-sm rounded-3 p-3 bg-white border-start border-info border-4">
 <div class="d-flex justify-content-between align-items-center">
 <div>
 <div class="text-muted small fw-bold text-uppercase">Atendidos</div>
 <h2 class="fw-bold mb-0 text-info" id="kpi_atendidos">0</h2>
 </div>
 <div class="bg-info-subtle text-info p-3 rounded-circle">
 <i class="bi bi-check2-circle fs-4"></i>
 </div>
 </div>
 </div>
 </div>
 </div>

 <div class="row g-4 mb-4">

 <div class="col-12 col-lg-5">
 <div class="card-custom h-100">
 <div class="card-custom-header">
 <h5 class="fw-bold"><i class="bi bi-pie-chart-fill text-primary me-2"></i> Distribución de Estatus</h5>
 </div>
 <div class="card-custom-body d-flex flex-column align-items-center justify-content-center" style="min-height: 280px;">
 <div style="position: relative; width: 100%; max-width: 260px;">
 <canvas id="chartEstatus"></canvas>
 </div>
 <div class="text-center mt-3 small text-muted">
 Tasa de resolución efectiva: <strong class="text-primary" id="kpi_tasa_resolucion">0%</strong>
 </div>
 </div>
 </div>
 </div>

 <div class="col-12 col-lg-7">
 <div class="card-custom h-100">
 <div class="card-custom-header">
 <h5 class="fw-bold"><i class="bi bi-bar-chart-fill text-primary me-2"></i> Documentos por Clasificación</h5>
 </div>
 <div class="card-custom-body" style="min-height: 280px;">
 <canvas id="chartTipos"></canvas>
 </div>
 </div>
 </div>
 </div>

 <div class="card-custom">
 <div class="card-custom-header d-flex justify-content-between align-items-center">
 <h5 class="fw-bold"><i class="bi bi-card-checklist text-primary me-2"></i> Detalle de Trámites del Periodo</h5>
 <small class="text-muted">Últimos documentos registrados</small>
 </div>
 <div class="table-responsive">
 <table class="table table-hover table-striped align-middle mb-0">
 <thead class="table-light">
 <tr>
 <th>No. Oficio</th>
 <th>Fecha</th>
 <th>Tipo</th>
 <th>Asunto</th>
 <th>Remitente</th>
 <th>Destinatario</th>
 <th>Estatus</th>
 </tr>
 </thead>
 <tbody id="tabla_reportes_body">

 </tbody>
 </table>
 </div>
 </div>
</div>

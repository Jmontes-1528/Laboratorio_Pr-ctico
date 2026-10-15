<!-- SIDEBAR ESCRITORIO (ESTILO UNACH) -->
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo-icon">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div class="sidebar-brand-text">
            <h5>UNACH</h5>
            <small>Gestión de Documentos</small>
        </div>
    </div>
    <ul class="sidebar-nav">
        <li class="sidebar-nav-item">
            <a href="index.php" class="sidebar-nav-link">
                <i class="bi bi-house-door-fill"></i>
                <span>Inicio</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="index.php" class="sidebar-nav-link active">
                <i class="bi bi-folder-fill"></i>
                <span>Documentos</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="#" class="sidebar-nav-link" data-bs-toggle="modal" data-bs-target="#modalNuevoDocumento">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Nuevo Documento</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="#search_filter_box" class="sidebar-nav-link" onclick="$('#input_buscar').focus();">
                <i class="bi bi-search"></i>
                <span>Buscar</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="#" class="sidebar-nav-link text-white-50" onclick="Swal.fire('Módulo Informativo', 'Reportes de estadística y desempeño en desarrollo.', 'info');">
                <i class="bi bi-bar-chart-line-fill"></i>
                <span>Reportes</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="#" class="sidebar-nav-link text-white-50" onclick="Swal.fire('Módulo Informativo', 'Gestión de usuarios y permisos administrativos.', 'info');">
                <i class="bi bi-people-fill"></i>
                <span>Usuarios</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="#" class="sidebar-nav-link text-white-50" onclick="Swal.fire('Configuración', 'Parámetros del sistema y dependencias.', 'info');">
                <i class="bi bi-gear-fill"></i>
                <span>Configuración</span>
            </a>
        </li>
    </ul>
    <div class="p-3 border-top border-secondary text-white-50 small text-center">
        ETDA · Campus IV Tapachula<br>
        <span class="text-warning">Versión 1.0</span>
    </div>
</aside>

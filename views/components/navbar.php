<!-- NAVBAR SUPERIOR -->
<header class="top-navbar">
    <div class="d-flex align-items-center gap-2">
        <!-- Botón menú hamburguesa para móviles -->
        <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
            <i class="bi bi-list fs-5"></i>
        </button>
        <div class="d-flex align-items-center gap-2">
            <span class="d-lg-none fw-bold text-primary">
                <i class="bi bi-file-earmark-text-fill text-warning me-1"></i> Gestión Documental
            </span>
            <span class="d-none d-lg-inline text-muted small">
                Escuela de Tecnologías Digitales Aplicadas (ETDA) · Campus IV
            </span>
        </div>
    </div>

    <!-- Perfil de usuario -->
    <div class="dropdown">
        <div class="user-badge" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="user-avatar-circle">
                <i class="bi bi-person-fill"></i>
            </div>
            <span class="d-none d-sm-inline">Usuario</span>
            <i class="bi bi-chevron-down small text-muted"></i>
        </div>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
            <li><h6 class="dropdown-header">Administrador</h6></li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-shield-check me-2"></i> Seguridad</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
        </ul>
    </div>
</header>

<!-- OFFCANVAS MÓVIL (MENÚ LATERAL EN PANTALLAS PEQUEÑAS) -->
<div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
    <div class="offcanvas-header border-bottom border-secondary">
        <h5 class="offcanvas-title" id="mobileSidebarLabel">
            <i class="bi bi-mortarboard-fill text-warning me-2"></i> UNACH ETDA
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <ul class="sidebar-nav">
            <li class="sidebar-nav-item">
                <a href="index.php" class="sidebar-nav-link active">
                    <i class="bi bi-house-door-fill"></i>
                    <span>Inicio</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="index.php" class="sidebar-nav-link">
                    <i class="bi bi-folder-fill"></i>
                    <span>Documentos</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#" class="sidebar-nav-link" data-bs-dismiss="offcanvas" data-bs-toggle="modal" data-bs-target="#modalNuevoDocumento">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Nuevo Documento</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#search_filter_box" class="sidebar-nav-link" data-bs-dismiss="offcanvas">
                    <i class="bi bi-search"></i>
                    <span>Buscar</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<header class="top-navbar">
 <div class="d-flex align-items-center gap-2">

 <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
 <i class="bi bi-list fs-5"></i>
 </button>
 <div class="d-flex align-items-center gap-2">
 <span class="d-lg-none fw-bold text-primary">
 <i class="bi bi-file-earmark-text-fill text-warning me-1"></i> Gestión Documental
 </span>
 <span class="d-none d-lg-inline text-muted small">
 Escuela de Tecnologías Digitales Aplicadas (ETDA) · Campus IV Tapachula
 </span>
 </div>
 </div>

 <div class="dropdown">
 <div class="user-badge" data-bs-toggle="dropdown" aria-expanded="false">
 <div class="user-avatar-circle">
 <i class="bi bi-person-fill"></i>
 </div>
 <span class="d-none d-sm-inline">
 <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?>
 </span>
 <i class="bi bi-chevron-down small text-muted"></i>
 </div>
 <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
 <li>
 <h6 class="dropdown-header text-truncate" style="max-width: 200px;">
 <i class="bi bi-shield-lock text-primary me-1"></i>
 <?php echo htmlspecialchars(ucfirst($_SESSION['usuario_rol'] ?? 'Operador')); ?>
 </h6>
 </li>
 <li><a class="dropdown-item nav-view-link" href="#usuarios" data-view="usuarios"><i class="bi bi-people me-2"></i> Gestión Usuarios</a></li>
 <li><a class="dropdown-item nav-view-link" href="#reportes" data-view="reportes"><i class="bi bi-bar-chart me-2"></i> Ver Reportes</a></li>
 <li><hr class="dropdown-divider"></li>
 <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a></li>
 </ul>
 </div>
</header>

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
 <a href="#documentos" class="sidebar-nav-link nav-view-link active" data-view="documentos" data-bs-dismiss="offcanvas">
 <i class="bi bi-house-door-fill"></i>
 <span>Inicio</span>
 </a>
 </li>
 <li class="sidebar-nav-item">
 <a href="#documentos" class="sidebar-nav-link nav-view-link" data-view="documentos" data-bs-dismiss="offcanvas">
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
 <a href="#reportes" class="sidebar-nav-link nav-view-link" data-view="reportes" data-bs-dismiss="offcanvas">
 <i class="bi bi-bar-chart-line-fill"></i>
 <span>Reportes</span>
 </a>
 </li>
 <li class="sidebar-nav-item">
 <a href="#usuarios" class="sidebar-nav-link nav-view-link" data-view="usuarios" data-bs-dismiss="offcanvas">
 <i class="bi bi-people-fill"></i>
 <span>Usuarios</span>
 </a>
 </li>
 </ul>
 </div>
</div>

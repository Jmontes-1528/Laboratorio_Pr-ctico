<!-- BARRA DE NAVEGACIÓN INFERIOR (MÓVIL - MOCKUP OFICIAL) -->
<nav class="bottom-nav">
    <a href="index.php" class="bottom-nav-item active" data-target="inicio">
        <i class="bi bi-house-door-fill"></i>
        <span>Inicio</span>
    </a>
    <a href="#listado_documentos" class="bottom-nav-item" data-target="documentos">
        <i class="bi bi-folder-fill"></i>
        <span>Documentos</span>
    </a>
    <a href="#" class="bottom-nav-item" data-target="nuevo" data-bs-toggle="modal" data-bs-target="#modalNuevoDocumento">
        <div class="bottom-nav-center">
            <i class="bi bi-plus-lg"></i>
        </div>
        <span>Nuevo</span>
    </a>
    <a href="#search_filter_box" class="bottom-nav-item" data-target="buscar">
        <i class="bi bi-search"></i>
        <span>Buscar</span>
    </a>
    <a href="#" class="bottom-nav-item" data-target="perfil" onclick="Swal.fire('Perfil de Usuario', 'Sesión iniciada como Operador/Administrador.', 'info');">
        <i class="bi bi-person-circle"></i>
        <span>Perfil</span>
    </a>
</nav>

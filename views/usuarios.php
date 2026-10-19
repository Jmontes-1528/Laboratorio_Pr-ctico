<div class="view-section d-none" id="seccion_usuarios">
 <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
 <div>
 <h3 class="fw-bold text-primary mb-1" style="color: var(--unach-blue-primary) !important;">
 <i class="bi bi-people-fill text-warning me-2"></i> Personal y Usuarios del Sistema
 </h3>
 <p class="text-muted small mb-0">
 Control de operadores de ventanilla y administradores con acceso al registro de documentos
 </p>
 </div>
 <button type="button" class="btn btn-primary px-3 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario" style="background-color: var(--unach-blue-secondary);">
 <i class="bi bi-person-plus-fill me-1"></i> + Nuevo Usuario
 </button>
 </div>

 <div class="card-custom">
 <div class="card-custom-header">
 <h5 class="fw-bold">Usuarios Registrados</h5>
 <button class="btn btn-sm btn-outline-secondary" id="btn_recargar_usuarios">
 <i class="bi bi-arrow-clockwise"></i> Recargar
 </button>
 </div>
 <div class="table-responsive">
 <table class="table table-hover align-middle mb-0">
 <thead class="table-light">
 <tr>
 <th>#</th>
 <th>Nombre Completo</th>
 <th>Correo Institucional</th>
 <th>Rol</th>
 <th>Estado</th>
 <th>Fecha de Registro</th>
 <th>Acciones</th>
 </tr>
 </thead>
 <tbody id="tabla_usuarios_body">

 </tbody>
 </table>
 </div>
 </div>
</div>

<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-labelledby="modalNuevoUsuarioLabel" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered">
 <div class="modal-content border-0 shadow">
 <div class="modal-header text-white" style="background-color: var(--unach-blue-primary);">
 <h5 class="modal-title fw-bold" id="modalNuevoUsuarioLabel">
 <i class="bi bi-person-plus-fill text-warning me-2"></i> Registrar Personal de Oficina
 </h5>
 <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
 </div>
 <div class="modal-body p-4">
 <form id="form_nuevo_usuario">
 <div class="mb-3">
 <label for="usr_nombre" class="form-label fw-bold">Nombre Completo <span class="text-danger">*</span></label>
 <input type="text" class="form-control" id="usr_nombre" name="nombre_completo" placeholder="Ej: Lic. Carlos López" required>
 </div>

 <div class="mb-3">
 <label for="usr_correo" class="form-label fw-bold">Correo Electrónico <span class="text-danger">*</span></label>
 <input type="email" class="form-control" id="usr_correo" name="correo" placeholder="ejemplo@unach.mx" required>
 </div>

 <div class="mb-3">
 <label for="usr_rol" class="form-label fw-bold">Rol en el Sistema <span class="text-danger">*</span></label>
 <select class="form-select" id="usr_rol" name="rol" required>
 <option value="operador" selected>Operador de Ventanilla (Registro y consulta)</option>
 <option value="admin">Administrador (Control total)</option>
 </select>
 </div>

 <div class="mb-3">
 <label for="usr_password" class="form-label fw-bold">Contraseña de Acceso <span class="text-danger">*</span></label>
 <input type="password" class="form-control" id="usr_password" name="password" placeholder="Mínimo 4 caracteres" required>
 <div class="form-text">La contraseña se almacena con cifrado seguro BCRYPT.</div>
 </div>

 <div class="mt-4">
 <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" id="btn_guardar_usuario" style="background-color: var(--unach-blue-secondary);">
 <i class="bi bi-check-circle-fill me-1"></i> Guardar Usuario
 </button>
 </div>
 </form>
 </div>
 </div>
 </div>
</div>

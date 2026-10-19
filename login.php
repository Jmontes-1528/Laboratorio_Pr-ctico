<?php

require_once __DIR__ . '/config/config.php';

if (!empty($_SESSION['usuario_id'])) {
 header('Location: index.php');
 exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
 <title>Iniciar Sesión · Sistema de Gestión Documental UNACH</title>

 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.min.css">

 <style>
 :root {
 --unach-blue-primary: #0a2540;
 --unach-blue-secondary: #0d3b66;
 --unach-gold: #c8963e;
 --unach-bg-light: #f4f6f9;
 }

 body {
 font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
 background: linear-gradient(135deg, #07192d 0%, #0d3b66 60%, #15457a 100%);
 min-height: 100vh;
 display: flex;
 align-items: center;
 justify-content: center;
 padding: 1.5rem;
 margin: 0;
 }

 .login-card {
 background: #ffffff;
 border-radius: 18px;
 box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
 max-width: 440px;
 width: 100%;
 overflow: hidden;
 border: 1px solid rgba(255, 255, 255, 0.2);
 }

 .login-header {
 background: linear-gradient(180deg, var(--unach-blue-primary) 0%, var(--unach-blue-secondary) 100%);
 color: #ffffff;
 padding: 2.25rem 1.75rem 1.75rem;
 text-align: center;
 position: relative;
 }

 .login-logo-circle {
 width: 68px;
 height: 68px;
 background: rgba(255, 255, 255, 0.12);
 border: 2px solid var(--unach-gold);
 border-radius: 50%;
 display: inline-flex;
 align-items: center;
 justify-content: center;
 font-size: 2rem;
 color: var(--unach-gold);
 margin-bottom: 0.85rem;
 box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
 }

 .login-header h4 {
 font-weight: 800;
 margin-bottom: 0.25rem;
 letter-spacing: 0.5px;
 }

 .login-header small {
 color: rgba(255, 255, 255, 0.8);
 font-size: 0.82rem;
 display: block;
 }

 .login-body {
 padding: 2rem 1.75rem 1.75rem;
 }

 .form-floating > .form-control:focus ~ label {
 color: var(--unach-blue-secondary);
 }

 .btn-login {
 background-color: var(--unach-blue-secondary);
 border-color: var(--unach-blue-secondary);
 font-weight: 700;
 padding: 0.75rem 1.5rem;
 border-radius: 10px;
 transition: all 0.25s ease;
 }

 .btn-login:hover {
 background-color: #082846;
 border-color: #082846;
 transform: translateY(-1px);
 box-shadow: 0 4px 12px rgba(13, 59, 102, 0.3);
 }

 .demo-credentials-box {
 background-color: #f8fafc;
 border: 1px dashed #cbd5e1;
 border-radius: 10px;
 padding: 0.85rem 1rem;
 margin-top: 1.5rem;
 font-size: 0.82rem;
 }

 .cursor-pointer {
 cursor: pointer;
 }
 </style>
</head>
<body>

<div class="login-card">

 <div class="login-header">
 <div class="login-logo-circle">
 <i class="bi bi-mortarboard-fill"></i>
 </div>
 <h4>UNACH · ETDA</h4>
 <small class="text-warning fw-semibold">Escuela de Tecnologías Digitales Aplicadas</small>
 <small class="text-white-50 mt-1">Sistema de Gestión de Documentos de Oficina</small>
 </div>

 <div class="login-body">
 <h5 class="fw-bold text-dark mb-1 text-center" style="color: var(--unach-blue-primary) !important;">
 Iniciar Sesión
 </h5>
 <p class="text-muted small text-center mb-4">Ingresa tus credenciales para acceder a la ventanilla</p>

 <form id="form_login">

 <div class="form-floating mb-3">
 <input type="email" class="form-control" id="correo" name="correo" placeholder="correo@unach.mx" required autofocus value="admin@unach.mx">
 <label for="correo"><i class="bi bi-envelope me-1"></i> Correo Electrónico</label>
 </div>

 <div class="input-group mb-3">
 <div class="form-floating flex-grow-1">
 <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required value="Admin123*">
 <label for="password"><i class="bi bi-lock me-1"></i> Contraseña</label>
 </div>
 <button class="btn btn-outline-secondary" type="button" id="btn_toggle_password" title="Ver contraseña">
 <i class="bi bi-eye"></i>
 </button>
 </div>

 <button type="submit" class="btn btn-primary btn-login w-100 text-white mb-2" id="btn_submit_login">
 <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar al Sistema
 </button>
 </form>

 <div class="demo-credentials-box text-muted">
 <div class="d-flex justify-content-between align-items-center mb-1">
 <strong class="text-primary"><i class="bi bi-key-fill text-warning me-1"></i> Acceso de Demostración:</strong>
 <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none fw-semibold" id="btn_rellenar_demo">
 Autocompletar
 </button>
 </div>
 <div><strong>Usuario:</strong> <code>admin@unach.mx</code></div>
 <div><strong>Clave:</strong> <code>Admin123*</code></div>
 </div>
 </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.all.min.js"></script>

<script>
$(document).ready(function () {
 // Mostrar / ocultar contraseña
 $('#btn_toggle_password').on('click', function () {
 const $pass = $('#password');
 const $icon = $(this).find('i');
 if ($pass.attr('type') === 'password') {
 $pass.attr('type', 'text');
 $icon.removeClass('bi-eye').addClass('bi-eye-slash');
 } else {
 $pass.attr('type', 'password');
 $icon.removeClass('bi-eye-slash').addClass('bi-eye');
 }
 });

 // Rellenar credenciales demo
 $('#btn_rellenar_demo').on('click', function () {
 $('#correo').val('admin@unach.mx');
 $('#password').val('Admin123*');
 });

 // Envío del formulario con AJAX
 $('#form_login').on('submit', function (e) {
 e.preventDefault();

 const $btn = $('#btn_submit_login');
 const originalHtml = $btn.html();
 $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Verificando...');

 $.ajax({
 url: 'api/login.php',
 type: 'POST',
 data: $(this).serialize(),
 dataType: 'json',
 success: function (res) {
 if (res.success) {
 Swal.fire({
 icon: 'success',
 title: '¡Acceso Correcto!',
 text: res.message,
 timer: 1300,
 showConfirmButton: false
 }).then(() => {
 window.location.href = 'index.php';
 });
 } else {
 $btn.prop('disabled', false).html(originalHtml);
 Swal.fire({
 icon: 'error',
 title: 'Error de Acceso',
 text: res.message || 'Credenciales inválidas.',
 confirmButtonColor: '#0d3b66'
 });
 }
 },
 error: function (xhr) {
 $btn.prop('disabled', false).html(originalHtml);
 let msg = 'Ocurrió un error al verificar las credenciales.';
 if (xhr.responseJSON && xhr.responseJSON.message) {
 msg = xhr.responseJSON.message;
 }
 Swal.fire({
 icon: 'error',
 title: 'Acceso Denegado',
 text: msg,
 confirmButtonColor: '#0d3b66'
 });
 }
 });
 });
});
</script>

</body>
</html>

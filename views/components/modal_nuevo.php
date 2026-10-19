<div class="modal fade" id="modalNuevoDocumento" tabindex="-1" aria-labelledby="modalNuevoDocumentoLabel" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered modal-lg">
 <div class="modal-content border-0 shadow">
 <div class="modal-header bg-primary text-white" style="background-color: var(--unach-blue-primary) !important;">
 <h5 class="modal-title fw-bold" id="modalNuevoDocumentoLabel">
 <i class="bi bi-file-earmark-plus-fill text-warning me-2"></i> Registrar Documento
 </h5>
 <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
 </div>
 <div class="modal-body p-4">
 <form id="form_nuevo_documento" enctype="multipart/form-data">
 <div class="row g-3">

 <div class="col-md-6">
 <label for="no_oficio" class="form-label fw-bold">
 No. de Oficio <span class="text-danger">*</span>
 </label>
 <input type="text" class="form-control" id="no_oficio" name="no_oficio" placeholder="Ej: 123/2026" required>
 </div>

 <div class="col-md-6">
 <label for="fecha" class="form-label fw-bold">
 Fecha de recepción <span class="text-danger">*</span>
 </label>
 <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
 </div>

 <div class="col-12">
 <label for="asunto" class="form-label fw-bold">
 Asunto <span class="text-danger">*</span>
 </label>
 <input type="text" class="form-control" id="asunto" name="asunto" placeholder="Descripción breve del asunto" required>
 </div>

 <div class="col-md-6">
 <label for="remitente" class="form-label fw-bold">
 Remitente <span class="text-danger">*</span>
 </label>
 <input type="text" class="form-control" id="remitente" name="remitente" list="datalist_remitentes" placeholder="Nombre o dependencia emisora" required>
 <datalist id="datalist_remitentes"></datalist>
 </div>

 <div class="col-md-6">
 <label for="destinatario" class="form-label fw-bold">
 Destinatario <span class="text-danger">*</span>
 </label>
 <input type="text" class="form-control" id="destinatario" name="destinatario" list="datalist_destinatarios" placeholder="Nombre o área destinataria" required>
 <datalist id="datalist_destinatarios"></datalist>
 </div>

 <input type="hidden" id="select_estatus" name="estatus" value="1">

 <div class="col-md-12">
 <label for="select_tipo" class="form-label fw-bold">
 Tipo de Documento
 </label>
 <select class="form-select" id="select_tipo" name="id_tipo">
 <option value="1" selected>Oficio</option>
 <option value="2">Circular</option>
 <option value="3">Solicitud</option>
 <option value="4">Invitación</option>
 <option value="5">Reporte</option>
 <option value="6">Comunicado</option>
 </select>
 </div>

 <div class="col-12">
 <label for="observaciones" class="form-label fw-semibold text-muted small">
 Observaciones / Notas internas (opcional)
 </label>
 <textarea class="form-control" id="observaciones" name="observaciones" rows="2" placeholder="Detalles de turno, comentarios de seguimiento..."></textarea>
 </div>

 <div class="col-12 mt-3">
 <label class="form-label fw-bold mb-1">
 Documento digitalizado (foto o PDF)
 </label>
 <small class="text-muted d-block mb-2">
 Puedes tomar una fotografía con tu celular o adjuntar el archivo PDF escaneado.
 </small>

 <input type="file" id="input_archivo_camara" name="archivo_camara" accept="image/*" capture="environment" style="display: none;">
 <input type="file" id="input_archivo_pdf" name="archivo_pdf" accept="application/pdf,image/*" style="display: none;">

 <div class="upload-options-container">
 <div class="upload-card-btn" id="btn_camara">
 <i class="bi bi-camera-fill"></i>
 <span>Tomar foto<br><small class="text-muted">del documento</small></span>
 </div>
 <div class="upload-card-btn" id="btn_archivo_pdf">
 <i class="bi bi-cloud-arrow-up-fill"></i>
 <span>Seleccionar<br><small class="text-muted">archivo PDF o imagen</small></span>
 </div>
 </div>

 <div class="file-preview-card" id="file_preview_card">
 <div class="file-preview-info">
 <i class="bi bi-file-earmark-pdf-fill"></i>
 <div>
 <div class="fw-bold small text-truncate" id="preview_filename" style="max-width: 250px;">documento.pdf</div>
 <div class="text-muted" style="font-size: 0.75rem;" id="preview_filesize">0 KB</div>
 </div>
 </div>
 <button type="button" class="btn btn-sm btn-outline-danger" id="btn_remover_archivo" title="Quitar archivo">
 <i class="bi bi-trash"></i>
 </button>
 </div>
 </div>

 <div class="col-12 mt-4">
 <button type="submit" class="btn btn-primary w-100 py-2 fs-5 fw-bold" id="btn_guardar_documento" style="background-color: var(--unach-blue-secondary); border-color: var(--unach-blue-secondary);">
 <i class="bi bi-check-circle-fill me-2"></i> Guardar Documento
 </button>
 </div>
 </div>
 </form>
 </div>
 </div>
 </div>
</div>

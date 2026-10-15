<!-- MODAL PARA CAPTURA CON CÁMARA (WEBCAM / MÓVIL) -->
<div class="modal fade" id="modalCapturaCamara" tabindex="-1" aria-labelledby="modalCapturaCamaraLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-camera-fill text-warning fs-4"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalCapturaCamaraLabel">Digitalizar con Cámara</h5>
                        <small class="text-white-50">Toma una fotografía clara del oficio o documento</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="btn_cerrar_camara"></button>
            </div>
            <div class="modal-body p-3 bg-black text-center">
                <!-- Visor en vivo de la cámara -->
                <div class="camera-stream-container position-relative" style="min-height: 320px; max-height: 450px; overflow: hidden; border-radius: 8px;">
                    <video id="video_camara" autoplay playsinline style="width: 100%; height: 100%; max-height: 450px; object-fit: contain; background: #111;"></video>
                    
                    <!-- Previsualización de la foto capturada -->
                    <img id="img_capturada" style="display: none; width: 100%; max-height: 450px; object-fit: contain; background: #111;" alt="Fotografía capturada">
                    
                    <!-- Canvas oculto para procesar los píxeles -->
                    <canvas id="canvas_camara" style="display: none;"></canvas>
                </div>
            </div>
            <div class="modal-footer bg-light d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn_cancelar_camara">
                    <i class="bi bi-x-lg me-1"></i> Cancelar
                </button>
                
                <div class="d-flex gap-2">
                    <!-- Botón para disparar foto -->
                    <button type="button" class="btn btn-primary px-4 fw-bold" id="btn_disparar_foto" style="background-color: var(--unach-blue-secondary);">
                        <i class="bi bi-camera me-1"></i> Tomar Fotografía
                    </button>
                    
                    <!-- Botón para repetir foto (oculto al inicio) -->
                    <button type="button" class="btn btn-outline-warning text-dark fw-semibold d-none" id="btn_repetir_foto">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Repetir
                    </button>
                    
                    <!-- Botón para confirmar y convertir a PDF (oculto al inicio) -->
                    <button type="button" class="btn btn-success fw-bold px-4 d-none" id="btn_confirmar_pdf">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Guardar como PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

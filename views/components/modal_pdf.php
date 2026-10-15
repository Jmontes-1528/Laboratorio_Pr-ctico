<!-- MODAL VISOR DE DOCUMENTO PDF -->
<div class="modal fade" id="modalVisorPdf" tabindex="-1" aria-labelledby="modal_pdf_titulo" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-pdf-fill text-danger fs-4"></i>
                    <h5 class="modal-title fw-bold" id="modal_pdf_titulo">Visor de Documento</h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="#" id="btn_descargar_modal_pdf" class="btn btn-sm btn-outline-light" download>
                        <i class="bi bi-download me-1"></i> Descargar PDF
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 bg-secondary" style="min-height: 500px;">
                <iframe id="iframe_pdf_viewer" class="pdf-viewer-frame" src="about:blank"></iframe>
            </div>
        </div>
    </div>
</div>

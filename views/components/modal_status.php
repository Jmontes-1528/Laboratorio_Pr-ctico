<div class="modal fade" id="modalCambiarEstatus" tabindex="-1" aria-labelledby="modalCambiarEstatusLabel" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered">
 <div class="modal-content border-0 shadow">
 <div class="modal-header bg-light">
 <h5 class="modal-title fw-bold" id="modalCambiarEstatusLabel">
 <i class="bi bi-arrow-repeat text-primary me-2"></i> Actualizar Estatus de Documento
 </h5>
 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
 </div>
 <div class="modal-body p-4">
 <input type="hidden" id="modal_estatus_id_doc">

 <div class="alert alert-info py-2 mb-3">
 <i class="bi bi-info-circle me-1"></i> Documento: <strong id="modal_estatus_oficio"></strong>
 </div>

 <div class="mb-3">
 <label for="modal_nuevo_estatus" class="form-label fw-bold">Nuevo Estatus:</label>
 <select class="form-select" id="modal_nuevo_estatus">
 <option value="1">Recibido</option>
 <option value="2">En proceso</option>
 <option value="3">Atendido</option>
 </select>
 </div>

 <div class="mb-3">
 <label for="modal_estatus_obs" class="form-label fw-bold">Observación / Nota de seguimiento:</label>
 <textarea class="form-control" id="modal_estatus_obs" rows="3" placeholder="Ej: Se turnó al área académica para revisión de contenidos..."></textarea>
 </div>
 </div>
 <div class="modal-footer bg-light">
 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
 <button type="button" class="btn btn-primary" id="btn_confirmar_cambio_estatus">Guardar Cambio</button>
 </div>
 </div>
 </div>
</div>

/**
 * ====================================================================
 * APLICACIÓN CLIENTE - JQUERY / FRONTEND
 * Universidad Autónoma de Chiapas (UNACH) - ETDA
 * ====================================================================
 */

$(document).ready(function () {
    // Estado global de la aplicación
    let estadoApp = {
        paginaActual: 1,
        porPagina: 5,
        totalRegistros: 0,
        totalPaginas: 1,
        filtros: {
            q: '',
            estatus: 'todos',
            desde: '',
            hasta: ''
        }
    };

    // Inicializar
    inicializarApp();

    function inicializarApp() {
        cargarCatalogos();
        cargarDocumentos();
        configurarEventos();
    }

    // ----------------------------------------------------------------
    // 1. CARGA DE CATÁLOGOS DINÁMICOS
    // ----------------------------------------------------------------
    function cargarCatalogos() {
        $.ajax({
            url: 'api/get_catalogos.php',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.success && res.data) {
                    poblarSelects(res.data);
                }
            },
            error: function (err) {
                console.warn('Error al cargar catálogos desde el servidor:', err);
            }
        });
    }

    function poblarSelects(data) {
        // Llenar select de estatus en formulario de registro
        const $selectEstatus = $('#select_estatus');
        if ($selectEstatus.length && data.estatus) {
            $selectEstatus.empty();
            data.estatus.forEach(item => {
                $selectEstatus.append(`<option value="${item.id_estatus}">${item.nombre}</option>`);
            });
        }

        // Llenar select de tipos de documento
        const $selectTipo = $('#select_tipo');
        if ($selectTipo.length && data.tipos) {
            $selectTipo.empty();
            data.tipos.forEach(item => {
                $selectTipo.append(`<option value="${item.id_tipo}">${item.nombre}</option>`);
            });
        }

        // Llenar datalists para autocompletar remitente y destinatario
        if (data.remitentes) {
            const $dlRem = $('#datalist_remitentes').empty();
            data.remitentes.forEach(r => {
                $dlRem.append(`<option value="${r.nombre_remitente}">`);
            });
        }

        if (data.destinatarios) {
            const $dlDes = $('#datalist_destinatarios').empty();
            data.destinatarios.forEach(d => {
                $dlDes.append(`<option value="${d.nombre_destinatario}">`);
            });
        }
    }

    // ----------------------------------------------------------------
    // 2. CARGA Y FILTRADO DE DOCUMENTOS CON AJAX
    // ----------------------------------------------------------------
    function cargarDocumentos() {
        const $tbody = $('#tabla_documentos_body');
        const $mobileContainer = $('#lista_documentos_movil');

        $tbody.html(`
            <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Cargando documentos...
                </td>
            </tr>
        `);

        $mobileContainer.html(`
            <div class="text-center py-4 text-muted">
                <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                Cargando...
            </div>
        `);

        const params = {
            q: estadoApp.filtros.q,
            estatus: estadoApp.filtros.estatus,
            desde: estadoApp.filtros.desde,
            hasta: estadoApp.filtros.hasta,
            pagina: estadoApp.paginaActual,
            por_pagina: estadoApp.porPagina
        };

        $.ajax({
            url: 'api/get_documentos.php',
            type: 'GET',
            data: params,
            dataType: 'json',
            success: function (res) {
                if (res.success && res.data) {
                    renderizarTabla(res.data.documentos);
                    renderizarMovil(res.data.documentos);
                    actualizarPaginacion(res.data);
                } else {
                    mostrarErrorTabla('No se pudieron recuperar los registros.');
                }
            },
            error: function () {
                mostrarErrorTabla('Error de conexión con el servidor.');
            }
        });
    }

    // ----------------------------------------------------------------
    // 3. RENDERIZACIÓN DE TABLA ESCRITORIO (MOCKUP PÁG. 4)
    // ----------------------------------------------------------------
    function renderizarTabla(docs) {
        const $tbody = $('#tabla_documentos_body').empty();

        if (!docs || docs.length === 0) {
            $tbody.html(`
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-1"></i>
                        No se encontraron documentos con los filtros aplicados.
                    </td>
                </tr>
            `);
            return;
        }

        docs.forEach(doc => {
            const badgeClass = obtenerClaseBadge(doc.estatus);
            const fechaFormateada = formatearFecha(doc.fecha_recepcion);
            const rutaPdf = doc.archivo_pdf ? doc.archivo_pdf : 'uploads/documentos/demo_001.pdf';

            const fila = `
                <tr>
                    <td><strong>${doc.no_oficio}</strong></td>
                    <td>${fechaFormateada}</td>
                    <td>
                        <span class="d-inline-block text-truncate" style="max-width: 220px;" title="${doc.asunto}">
                            ${doc.asunto}
                        </span>
                    </td>
                    <td>
                        <span class="d-inline-block text-truncate" style="max-width: 160px;" title="${doc.remitente}">
                            ${doc.remitente}
                        </span>
                    </td>
                    <td>
                        <span class="d-inline-block text-truncate" style="max-width: 160px;" title="${doc.destinatario}">
                            ${doc.destinatario}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge ${badgeClass}">${doc.estatus}</span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <button type="button" class="btn-action-icon btn-ver-pdf" 
                                    data-pdf="${rutaPdf}" 
                                    data-oficio="${doc.no_oficio}" 
                                    title="Consultar PDF">
                                <i class="bi bi-eye"></i>
                            </button>
                            <a href="${rutaPdf}" download="${doc.no_oficio}.pdf" class="btn-action-icon" title="Descargar PDF">
                                <i class="bi bi-download"></i>
                            </a>
                            <button type="button" class="btn-action-icon btn-cambiar-estatus text-primary" 
                                    data-id="${doc.id_documento}" 
                                    data-oficio="${doc.no_oficio}" 
                                    data-estatus="${doc.id_estatus}" 
                                    title="Actualizar Estatus">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            $tbody.append(fila);
        });
    }

    // ----------------------------------------------------------------
    // 4. RENDERIZACIÓN EN MÓVIL (TARJETAS COMPACTAS)
    // ----------------------------------------------------------------
    function renderizarMovil(docs) {
        const $container = $('#lista_documentos_movil').empty();

        if (!docs || docs.length === 0) {
            $container.html(`
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-1"></i>
                    No hay documentos disponibles.
                </div>
            `);
            return;
        }

        docs.forEach(doc => {
            const badgeClass = obtenerClaseBadge(doc.estatus);
            const fecha = formatearFecha(doc.fecha_recepcion);
            const rutaPdf = doc.archivo_pdf ? doc.archivo_pdf : 'uploads/documentos/demo_001.pdf';

            const tarjeta = `
                <div class="mobile-doc-card">
                    <div class="mobile-doc-card-header">
                        <div>
                            <span class="fw-bold text-primary">${doc.no_oficio}</span>
                            <div class="text-muted small">${fecha}</div>
                        </div>
                        <span class="status-badge ${badgeClass}">${doc.estatus}</span>
                    </div>
                    <div class="mobile-doc-card-body mb-2">
                        <strong>Asunto:</strong> ${doc.asunto}<br>
                        <strong>De:</strong> ${doc.remitente}<br>
                        <strong>Para:</strong> ${doc.destinatario}
                    </div>
                    <div class="mobile-doc-meta align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-ver-pdf" 
                                data-pdf="${rutaPdf}" 
                                data-oficio="${doc.no_oficio}">
                            <i class="bi bi-eye me-1"></i> Ver PDF
                        </button>
                        <a href="${rutaPdf}" download="${doc.no_oficio}.pdf" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-download me-1"></i> Descargar
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-info btn-cambiar-estatus" 
                                data-id="${doc.id_documento}" 
                                data-oficio="${doc.no_oficio}" 
                                data-estatus="${doc.id_estatus}">
                            <i class="bi bi-arrow-repeat"></i> Estatus
                        </button>
                    </div>
                </div>
            `;
            $container.append(tarjeta);
        });
    }

    // ----------------------------------------------------------------
    // 5. PAGINACIÓN Y METADATOS
    // ----------------------------------------------------------------
    function actualizarPaginacion(data) {
        estadoApp.totalRegistros = data.total;
        estadoApp.totalPaginas = data.total_paginas || 1;

        const inicio = data.total > 0 ? (data.pagina - 1) * data.por_pagina + 1 : 0;
        const fin = Math.min(data.pagina * data.por_pagina, data.total);

        $('#info_paginacion').text(`Mostrando ${inicio} a ${fin} de ${data.total} registros`);

        const $paginacion = $('#controles_paginacion').empty();

        // Botón Anterior
        const deshabAnt = data.pagina <= 1 ? 'disabled' : '';
        $paginacion.append(`
            <li class="page-item ${deshabAnt}">
                <a class="page-link btn-pagina" href="#" data-pag="${data.pagina - 1}">‹</a>
            </li>
        `);

        // Números de página
        for (let i = 1; i <= estadoApp.totalPaginas; i++) {
            const activa = i === data.pagina ? 'active' : '';
            $paginacion.append(`
                <li class="page-item ${activa}">
                    <a class="page-link btn-pagina" href="#" data-pag="${i}">${i}</a>
                </li>
            `);
        }

        // Botón Siguiente
        const deshabSig = data.pagina >= estadoApp.totalPaginas ? 'disabled' : '';
        $paginacion.append(`
            <li class="page-item ${deshabSig}">
                <a class="page-link btn-pagina" href="#" data-pag="${data.pagina + 1}">›</a>
            </li>
        `);
    }

    // ----------------------------------------------------------------
    // 6. EVENTOS DE BÚSQUEDA Y FILTRADO
    // ----------------------------------------------------------------
    function configurarEventos() {
        // Búsqueda rápida por texto (Enter o Clic en Buscar)
        $('#btn_buscar').on('click', function () {
            estadoApp.filtros.q = $('#input_buscar').val().trim();
            estadoApp.paginaActual = 1;
            cargarDocumentos();
        });

        $('#input_buscar').on('keypress', function (e) {
            if (e.which === 13) {
                $('#btn_buscar').click();
            }
        });

        // Botón Filtrar
        $('#btn_filtrar').on('click', function () {
            estadoApp.filtros.estatus = $('#filtro_estatus').val();
            estadoApp.filtros.desde = $('#filtro_desde').val();
            estadoApp.filtros.hasta = $('#filtro_hasta').val();
            estadoApp.paginaActual = 1;
            cargarDocumentos();
        });

        // Limpiar Filtros
        $('#btn_limpiar_filtros').on('click', function () {
            $('#input_buscar').val('');
            $('#filtro_estatus').val('todos');
            $('#filtro_desde').val('');
            $('#filtro_hasta').val('');

            estadoApp.filtros = {
                q: '',
                estatus: 'todos',
                desde: '',
                hasta: ''
            };
            estadoApp.paginaActual = 1;
            cargarDocumentos();
        });

        // Clic en paginación
        $(document).on('click', '.btn-pagina', function (e) {
            e.preventDefault();
            const pag = parseInt($(this).data('pag'));
            if (pag >= 1 && pag <= estadoApp.totalPaginas && pag !== estadoApp.paginaActual) {
                estadoApp.paginaActual = pag;
                cargarDocumentos();
            }
        });

        // ------------------------------------------------------------
        // 7. DIGITALIZACIÓN: CÁMARA Y SELECCIÓN DE PDF
        // ------------------------------------------------------------
        $('#btn_camara').on('click', function () {
            $('#input_archivo_camara').click();
        });

        $('#btn_archivo_pdf').on('click', function () {
            $('#input_archivo_pdf').click();
        });

        // Manejar archivo seleccionado de cámara o explorador
        $('#input_archivo_camara, #input_archivo_pdf').on('change', function () {
            const input = this;
            if (input.files && input.files[0]) {
                const file = input.files[0];
                $('#preview_filename').text(file.name);
                $('#preview_filesize').text(formatearTamano(file.size));
                $('#file_preview_card').css('display', 'flex');

                // Si se seleccionó en uno, limpiar el otro
                if (input.id === 'input_archivo_camara') {
                    $('#input_archivo_pdf').val('');
                    $('#btn_camara').addClass('active');
                    $('#btn_archivo_pdf').removeClass('active');
                } else {
                    $('#input_archivo_camara').val('');
                    $('#btn_archivo_pdf').addClass('active');
                    $('#btn_camara').removeClass('active');
                }
            }
        });

        // Quitar archivo seleccionado
        $('#btn_remover_archivo').on('click', function () {
            $('#input_archivo_camara').val('');
            $('#input_archivo_pdf').val('');
            $('#file_preview_card').hide();
            $('#btn_camara').removeClass('active');
            $('#btn_archivo_pdf').removeClass('active');
        });

        // ------------------------------------------------------------
        // 8. ENVÍO DE FORMULARIO DE REGISTRO
        // ------------------------------------------------------------
        $('#form_nuevo_documento').on('submit', function (e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);

            const $btnSubmit = $('#btn_guardar_documento');
            const textoOriginal = $btnSubmit.html();
            $btnSubmit.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');

            $.ajax({
                url: 'api/save_documento.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (res) {
                    $btnSubmit.prop('disabled', false).html(textoOriginal);
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Registrado con éxito!',
                            text: `El documento ${res.data.no_oficio} ha sido digitalizado y guardado.`,
                            confirmButtonColor: '#0d3b66'
                        });

                        // Limpiar formulario
                        form.reset();
                        $('#btn_remover_archivo').click();

                        // Cerrar modal si estuviera abierto
                        const modalEl = document.getElementById('modalNuevoDocumento');
                        if (modalEl) {
                            const modalInstance = bootstrap.Modal.getInstance(modalEl);
                            if (modalInstance) modalInstance.hide();
                        }

                        // Recargar lista
                        cargarDocumentos();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al registrar',
                            text: res.message || 'Verifica los campos obligatorios.',
                            confirmButtonColor: '#0d3b66'
                        });
                    }
                },
                error: function (xhr) {
                    $btnSubmit.prop('disabled', false).html(textoOriginal);
                    let msg = 'Ocurrió un error al procesar el documento.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error en el servidor',
                        text: msg,
                        confirmButtonColor: '#0d3b66'
                    });
                }
            });
        });

        // ------------------------------------------------------------
        // 9. VISOR DE PDF EN MODAL
        // ------------------------------------------------------------
        $(document).on('click', '.btn-ver-pdf', function () {
            const pdfUrl = $(this).data('pdf');
            const noOficio = $(this).data('oficio');

            $('#modal_pdf_titulo').text(`Visor de Documento: ${noOficio}`);
            $('#iframe_pdf_viewer').attr('src', pdfUrl);
            $('#btn_descargar_modal_pdf').attr('href', pdfUrl).attr('download', `${noOficio}.pdf`);

            const modal = new bootstrap.Modal(document.getElementById('modalVisorPdf'));
            modal.show();
        });

        // Limpiar visor al cerrar modal
        $('#modalVisorPdf').on('hidden.bs.modal', function () {
            $('#iframe_pdf_viewer').attr('src', 'about:blank');
        });

        // ------------------------------------------------------------
        // 10. CAMBIO RÁPIDO DE ESTATUS CON SEGUIMIENTO
        // ------------------------------------------------------------
        $(document).on('click', '.btn-cambiar-estatus', function () {
            const idDoc = $(this).data('id');
            const noOficio = $(this).data('oficio');
            const estatusActual = $(this).data('estatus');

            $('#modal_estatus_id_doc').val(idDoc);
            $('#modal_estatus_oficio').text(noOficio);
            $('#modal_nuevo_estatus').val(estatusActual);
            $('#modal_estatus_obs').val('');

            const modal = new bootstrap.Modal(document.getElementById('modalCambiarEstatus'));
            modal.show();
        });

        $('#btn_confirmar_cambio_estatus').on('click', function () {
            const idDoc = $('#modal_estatus_id_doc').val();
            const nuevoEstatus = $('#modal_nuevo_estatus').val();
            const obs = $('#modal_estatus_obs').val();

            const $btn = $(this);
            $btn.prop('disabled', true).text('Actualizando...');

            $.ajax({
                url: 'api/update_status.php',
                type: 'POST',
                data: {
                    id_documento: idDoc,
                    id_estatus: nuevoEstatus,
                    observacion: obs
                },
                dataType: 'json',
                success: function (res) {
                    $btn.prop('disabled', false).text('Guardar Cambio');
                    if (res.success) {
                        const modalEl = document.getElementById('modalCambiarEstatus');
                        bootstrap.Modal.getInstance(modalEl).hide();

                        Swal.fire({
                            icon: 'success',
                            title: 'Estatus Actualizado',
                            text: 'El estado del documento se actualizó en la bitácora.',
                            timer: 1600,
                            showConfirmButton: false
                        });

                        cargarDocumentos();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'No se pudo actualizar',
                            text: res.message
                        });
                    }
                },
                error: function () {
                    $btn.prop('disabled', false).text('Guardar Cambio');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de servidor',
                        text: 'No se pudo comunicar con la base de datos.'
                    });
                }
            });
        });

        // ------------------------------------------------------------
        // 11. NAVEGACIÓN EN MÓVIL (BOTTOM NAV)
        // ------------------------------------------------------------
        $('.bottom-nav-item').on('click', function (e) {
            const destino = $(this).data('target');
            if (!destino) return;

            e.preventDefault();
            $('.bottom-nav-item').removeClass('active');
            $(this).addClass('active');

            if (destino === 'nuevo') {
                // Abrir modal o llevar al formulario
                const modalNuevo = new bootstrap.Modal(document.getElementById('modalNuevoDocumento'));
                modalNuevo.show();
            } else if (destino === 'buscar') {
                $('html, body').animate({
                    scrollTop: $('#search_filter_box').offset().top - 70
                }, 400);
                $('#input_buscar').focus();
            } else if (destino === 'documentos' || destino === 'inicio') {
                $('html, body').animate({
                    scrollTop: 0
                }, 300);
            }
        });
    }

    // ----------------------------------------------------------------
    // UTILIDADES
    // ----------------------------------------------------------------
    function obtenerClaseBadge(estatus) {
        if (!estatus) return 'status-recibido';
        const str = estatus.toLowerCase();
        if (str.includes('proceso')) return 'status-en-proceso';
        if (str.includes('atendido')) return 'status-atendido';
        return 'status-recibido';
    }

    function formatearFecha(fechaStr) {
        if (!fechaStr) return '';
        const partes = fechaStr.split('-');
        if (partes.length === 3) {
            return `${partes[2]}/${partes[1]}/${partes[0]}`;
        }
        return fechaStr;
    }

    function formatearTamano(bytes) {
        if (!bytes || bytes === 0) return '0 B';
        const k = 1024;
        const dm = 1;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    function mostrarErrorTabla(mensaje) {
        $('#tabla_documentos_body').html(`
            <tr>
                <td colspan="7" class="text-center py-4 text-danger">
                    <i class="bi bi-exclamation-triangle fs-3 d-block mb-1"></i>
                    ${mensaje}
                </td>
            </tr>
        `);
    }
});

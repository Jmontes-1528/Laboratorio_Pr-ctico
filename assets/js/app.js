/**
 * ====================================================================
 * APLICACIÓN CLIENTE - JQUERY / FRONTEND
 * Universidad Autónoma de Chiapas (UNACH) - ETDA
 * ====================================================================
 */

$(document).ready(function () {
    // Instancias de gráficos Chart.js
    let chartEstatusInstance = null;
    let chartTiposInstance = null;

    // Estado global de la aplicación
    let estadoApp = {
        vistaActual: 'inicio',
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
        cargarInicio();
        configurarEventos();
        configurarNavegacionVistas();
    }

    // ----------------------------------------------------------------
    // NAVEGACIÓN ENTRE VISTAS (INICIO, DOCUMENTOS, REPORTES, USUARIOS)
    // ----------------------------------------------------------------
    function configurarNavegacionVistas() {
        $(document).on('click', '.nav-view-link', function (e) {
            const vista = $(this).data('view');
            if (vista) {
                e.preventDefault();
                cambiarVista(vista);
            }
        });

        // Revisar hash en la URL (ej: #inicio, #documentos, #reportes o #usuarios)
        const hash = window.location.hash.replace('#', '');
        if (hash === 'documentos' || hash === 'reportes' || hash === 'usuarios') {
            cambiarVista(hash);
        } else {
            cambiarVista('inicio');
        }
    }

    function cambiarVista(vista) {
        estadoApp.vistaActual = vista;

        // Ocultar todas las secciones
        $('.view-section').addClass('d-none');

        // Actualizar estados activos en sidebar y bottom nav
        $('.nav-view-link').removeClass('active');
        $(`.nav-view-link[data-view="${vista}"]`).addClass('active');

        // Mostrar sección correspondiente y cargar sus datos
        if (vista === 'inicio') {
            $('#seccion_inicio').removeClass('d-none');
            cargarInicio();
        } else if (vista === 'documentos') {
            $('#seccion_documentos').removeClass('d-none');
            cargarDocumentos();
        } else if (vista === 'reportes') {
            $('#seccion_reportes').removeClass('d-none');
            cargarReportes();
        } else if (vista === 'usuarios') {
            $('#seccion_usuarios').removeClass('d-none');
            cargarUsuarios();
        }

        // Desplazar suavemente hacia arriba
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // ----------------------------------------------------------------
    // 0. CARGA DE DATOS PARA LA VISTA DE INICIO / DASHBOARD
    // ----------------------------------------------------------------
    function cargarInicio() {
        const opcionesFecha = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const fechaActual = new Date().toLocaleDateString('es-MX', opcionesFecha);
        $('#inicio_fecha_actual').text(fechaActual.charAt(0).toUpperCase() + fechaActual.slice(1));

        $.ajax({
            url: 'api/get_inicio.php',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.success && res.data) {
                    const d = res.data;

                    // 1. Tarjetas KPI
                    $('#inicio_kpi_total').text(d.kpis.total || 0);
                    $('#inicio_kpi_recibidos').text(d.kpis.recibidos || 0);
                    $('#inicio_kpi_proceso').text(d.kpis.en_proceso || 0);
                    $('#inicio_kpi_atendidos').text(d.kpis.atendidos || 0);

                    // 2. Últimas Recepciones en Ventanilla
                    const $listaDocs = $('#inicio_lista_ultimos_docs').empty();
                    if (!d.ultimos_documentos || d.ultimos_documentos.length === 0) {
                        $listaDocs.html('<div class="p-3 text-center text-muted">No hay documentos registrados aún.</div>');
                    } else {
                        d.ultimos_documentos.forEach(doc => {
                            const badge = obtenerClaseBadge(doc.estatus);
                            const fecha = formatearFecha(doc.fecha_recepcion);
                            const rutaPdf = doc.archivo_pdf ? doc.archivo_pdf : 'uploads/documentos/demo_001.pdf';

                            $listaDocs.append(`
                                <div class="list-group-item p-3 border-0 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <strong class="text-primary">${doc.no_oficio}</strong>
                                            <span class="badge bg-secondary-subtle text-dark">${doc.tipo_documento}</span>
                                        </div>
                                        <span class="status-badge ${badge}">${doc.estatus}</span>
                                    </div>
                                    <div class="fw-semibold text-dark text-truncate mb-1" style="max-width: 90%;" title="${doc.asunto}">
                                        ${doc.asunto}
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center text-muted small">
                                        <div class="text-truncate" style="max-width: 70%;">
                                            <i class="bi bi-person me-1"></i> ${doc.remitente} &rarr; ${doc.destinatario}
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="d-none d-sm-inline"><i class="bi bi-calendar3 me-1"></i> ${fecha}</span>
                                            <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 btn-ver-pdf" 
                                                    data-pdf="${rutaPdf}" 
                                                    data-oficio="${doc.no_oficio}">
                                                <i class="bi bi-eye"></i> Ver
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `);
                        });
                    }

                    // 3. Bitácora de Actividad
                    const $listaAct = $('#inicio_lista_actividad').empty();
                    if (!d.actividad_reciente || d.actividad_reciente.length === 0) {
                        $listaAct.html('<div class="text-muted small text-center py-2">Sin actividad reciente.</div>');
                    } else {
                        d.actividad_reciente.forEach(act => {
                            const badge = obtenerClaseBadge(act.estatus_nuevo);
                            const fechaHora = act.fecha_cambio ? act.fecha_cambio.substring(5, 16) : '';
                            $listaAct.append(`
                                <div class="timeline-item">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="text-primary">${act.no_oficio}</strong>
                                        <span class="badge ${badge} p-1" style="font-size: 0.7rem;">${act.estatus_nuevo}</span>
                                    </div>
                                    <div class="text-muted small mb-1">${act.observacion || 'Actualización de estatus'}</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">
                                        <i class="bi bi-clock me-1"></i> ${fechaHora} por ${act.usuario_nombre}
                                    </div>
                                </div>
                            `);
                        });
                    }
                }
            },
            error: function () {
                console.warn('No se pudieron recuperar las métricas de inicio.');
            }
        });
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
        // Llenar select de estatus si existiera en la vista
        const $selectEstatus = $('#select_estatus');
        if ($selectEstatus.length && $selectEstatus.is('select') && data.estatus) {
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
    // 3. RENDERIZACIÓN DE TABLA ESCRITORIO
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
    // 4. RENDERIZACIÓN EN MÓVIL
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

        const deshabAnt = data.pagina <= 1 ? 'disabled' : '';
        $paginacion.append(`
            <li class="page-item ${deshabAnt}">
                <a class="page-link btn-pagina" href="#" data-pag="${data.pagina - 1}">‹</a>
            </li>
        `);

        for (let i = 1; i <= estadoApp.totalPaginas; i++) {
            const activa = i === data.pagina ? 'active' : '';
            $paginacion.append(`
                <li class="page-item ${activa}">
                    <a class="page-link btn-pagina" href="#" data-pag="${i}">${i}</a>
                </li>
            `);
        }

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
        // Clic en tarjetas de resumen de Inicio (filtra y va directo a Documentos)
        $(document).on('click', '.btn-filtro-inicio', function () {
            const estatus = $(this).data('estatus');
            cambiarVista('documentos');
            if (estatus) {
                $('#filtro_estatus').val(estatus);
                $('#btn_filtrar').click();
            }
        });

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

        $('#btn_filtrar').on('click', function () {
            estadoApp.filtros.estatus = $('#filtro_estatus').val();
            estadoApp.filtros.desde = $('#filtro_desde').val();
            estadoApp.filtros.hasta = $('#filtro_hasta').val();
            estadoApp.paginaActual = 1;
            cargarDocumentos();
        });

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

        $(document).on('click', '.btn-pagina', function (e) {
            e.preventDefault();
            const pag = parseInt($(this).data('pag'));
            if (pag >= 1 && pag <= estadoApp.totalPaginas && pag !== estadoApp.paginaActual) {
                estadoApp.paginaActual = pag;
                cargarDocumentos();
            }
        });

        // ------------------------------------------------------------
        // 7. DIGITALIZACIÓN CON CÁMARA Y CONVERSIÓN A PDF
        // ------------------------------------------------------------
        let streamCamaraActivo = null;
        let fotoCapturadaDataUrl = null;

        // Abrir Cámara
        $('#btn_camara').on('click', function () {
            // Intentar abrir cámara en vivo si el navegador lo soporta
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                abrirModalCamara();
            } else {
                // Dispositivos sin soporte de getUserMedia directo: abrir app nativa de cámara
                $('#input_archivo_camara').click();
            }
        });

        function abrirModalCamara() {
            const video = document.getElementById('video_camara');
            fotoCapturadaDataUrl = null;

            // Restablecer interfaz de la cámara
            $('#video_camara').show();
            $('#img_capturada').hide();
            $('#btn_disparar_foto').removeClass('d-none');
            $('#btn_repetir_foto').addClass('d-none');
            $('#btn_confirmar_pdf').addClass('d-none');

            const modalCamara = new bootstrap.Modal(document.getElementById('modalCapturaCamara'));
            modalCamara.show();

            // Solicitar acceso a la cámara (prioriza la trasera en móviles)
            navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: { ideal: 'environment' },
                    width: { ideal: 1920 },
                    height: { ideal: 1080 }
                },
                audio: false
            }).then(function (stream) {
                streamCamaraActivo = stream;
                video.srcObject = stream;
                video.play();
            }).catch(function (err) {
                console.warn("No se pudo iniciar stream de cámara directa:", err);
                modalCamara.hide();
                // Fallback automático al input nativo de cámara
                $('#input_archivo_camara').click();
            });
        }

        function detenerStreamCamara() {
            if (streamCamaraActivo) {
                streamCamaraActivo.getTracks().forEach(track => track.stop());
                streamCamaraActivo = null;
            }
            const video = document.getElementById('video_camara');
            if (video) {
                video.srcObject = null;
            }
        }

        // Al cerrar o cancelar el modal de cámara
        $('#modalCapturaCamara').on('hidden.bs.modal', function () {
            detenerStreamCamara();
        });

        // Disparar fotografía
        $('#btn_disparar_foto').on('click', function () {
            const video = document.getElementById('video_camara');
            const canvas = document.getElementById('canvas_camara');

            if (!video.videoWidth) {
                Swal.fire('Atención', 'La cámara aún está iniciando. Espera un segundo.', 'warning');
                return;
            }

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            fotoCapturadaDataUrl = canvas.toDataURL('image/jpeg', 0.92);

            // Mostrar la imagen capturada en lugar del video
            $('#img_capturada').attr('src', fotoCapturadaDataUrl).show();
            $('#video_camara').hide();

            // Alternar botones
            $('#btn_disparar_foto').addClass('d-none');
            $('#btn_repetir_foto').removeClass('d-none');
            $('#btn_confirmar_pdf').removeClass('d-none');
        });

        // Repetir fotografía
        $('#btn_repetir_foto').on('click', function () {
            $('#img_capturada').hide();
            $('#video_camara').show();
            $('#btn_disparar_foto').removeClass('d-none');
            $('#btn_repetir_foto').addClass('d-none');
            $('#btn_confirmar_pdf').addClass('d-none');
            fotoCapturadaDataUrl = null;
        });

        // Confirmar foto y convertir automáticamente a PDF
        $('#btn_confirmar_pdf').on('click', function () {
            if (!fotoCapturadaDataUrl) return;

            const $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Generando PDF...');

            const noOficio = $('#no_oficio').val().trim() || 'Documento';
            const nombreArchivo = 'Foto_' + noOficio.replace(/[^a-zA-Z0-9_-]/g, '_') + '_' + Date.now() + '.pdf';

            convertirDataUrlAPdf(fotoCapturadaDataUrl, nombreArchivo, function (pdfFile) {
                $btn.prop('disabled', false).html('<i class="bi bi-file-earmark-pdf-fill me-1"></i> Guardar como PDF');

                // Asignar el PDF generado al input de archivo
                asignarArchivoAInput($('#input_archivo_pdf'), pdfFile);
                $('#input_archivo_camara').val('');

                // Mostrar en la tarjeta de previsualización
                $('#preview_filename').text(pdfFile.name);
                $('#preview_filesize').text(formatearTamano(pdfFile.size) + ' (PDF generado de foto)');
                $('#file_preview_card').css('display', 'flex');

                $('#btn_camara').addClass('active');
                $('#btn_archivo_pdf').removeClass('active');

                // Cerrar modal de cámara y detener stream
                const modalEl = document.getElementById('modalCapturaCamara');
                bootstrap.Modal.getInstance(modalEl).hide();

                Swal.fire({
                    icon: 'success',
                    title: '¡Fotografía Digitalizada!',
                    text: 'La fotografía se convirtió en documento PDF listo para guardar.',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        });

        // Botón seleccionar archivo PDF o imagen desde el explorador
        $('#btn_archivo_pdf').on('click', function () {
            $('#input_archivo_pdf').click();
        });

        // Manejar cuando se selecciona un archivo de forma manual (cámara nativa o explorador)
        $('#input_archivo_camara, #input_archivo_pdf').on('change', function () {
            const input = this;
            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Si seleccionaron una imagen directamente, convertirla a PDF con jsPDF
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const nombrePdf = file.name.replace(/\.[^/.]+$/, "") + ".pdf";
                        convertirDataUrlAPdf(e.target.result, nombrePdf, function (pdfFile) {
                            asignarArchivoAInput($('#input_archivo_pdf'), pdfFile);
                            $('#input_archivo_camara').val('');

                            $('#preview_filename').text(pdfFile.name);
                            $('#preview_filesize').text(formatearTamano(pdfFile.size) + ' (Convertido a PDF)');
                            $('#file_preview_card').css('display', 'flex');

                            $('#btn_camara').addClass('active');
                            $('#btn_archivo_pdf').removeClass('active');
                        });
                    };
                    reader.readAsDataURL(file);
                } else {
                    // Es un PDF directo
                    $('#preview_filename').text(file.name);
                    $('#preview_filesize').text(formatearTamano(file.size));
                    $('#file_preview_card').css('display', 'flex');

                    $('#input_archivo_camara').val('');
                    $('#btn_archivo_pdf').addClass('active');
                    $('#btn_camara').removeClass('active');
                }
            }
        });

        $('#btn_remover_archivo').on('click', function () {
            $('#input_archivo_camara').val('');
            $('#input_archivo_pdf').val('');
            $('#file_preview_card').hide();
            $('#btn_camara').removeClass('active');
            $('#btn_archivo_pdf').removeClass('active');
            fotoCapturadaDataUrl = null;
        });

        // ------------------------------------------------------------
        // HELPER: CONVERSIÓN DE IMAGEN A PDF ESTÁNDAR (A4) CON JSPDF
        // ------------------------------------------------------------
        function convertirDataUrlAPdf(dataUrl, nombreArchivoBase, callback) {
            const img = new Image();
            img.onload = function () {
                if (!window.jspdf || !window.jspdf.jsPDF) {
                    console.error("jsPDF no está cargado.");
                    return;
                }

                const { jsPDF } = window.jspdf;
                const esHorizontal = img.width > img.height;
                const doc = new jsPDF({
                    orientation: esHorizontal ? 'landscape' : 'portrait',
                    unit: 'mm',
                    format: 'a4'
                });

                const pageW = doc.internal.pageSize.getWidth();
                const pageH = doc.internal.pageSize.getHeight();
                const margin = 10;
                const maxW = pageW - (margin * 2);
                const maxH = pageH - (margin * 2);

                const scale = Math.min(maxW / img.width, maxH / img.height);
                const finalW = img.width * scale;
                const finalH = img.height * scale;
                const posX = (pageW - finalW) / 2;
                const posY = (pageH - finalH) / 2;

                doc.addImage(dataUrl, 'JPEG', posX, posY, finalW, finalH, undefined, 'FAST');
                const pdfBlob = doc.output('blob');
                const pdfFile = new File([pdfBlob], nombreArchivoBase, { type: 'application/pdf' });
                callback(pdfFile);
            };
            img.src = dataUrl;
        }

        function asignarArchivoAInput($input, file) {
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            $input[0].files = dataTransfer.files;
        }

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

                        form.reset();
                        $('#btn_remover_archivo').click();

                        const modalEl = document.getElementById('modalNuevoDocumento');
                        if (modalEl) {
                            const modalInstance = bootstrap.Modal.getInstance(modalEl);
                            if (modalInstance) modalInstance.hide();
                        }

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

        $('#modalVisorPdf').on('hidden.bs.modal', function () {
            $('#iframe_pdf_viewer').attr('src', 'about:blank');
        });

        // ------------------------------------------------------------
        // 10. CAMBIO RÁPIDO DE ESTATUS
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
        // 11. NAVEGACIÓN EN MÓVIL
        // ------------------------------------------------------------
        $('.bottom-nav-item').on('click', function (e) {
            const destino = $(this).data('target');
            if (!destino) return;

            if (destino === 'nuevo') {
                e.preventDefault();
                const modalNuevo = new bootstrap.Modal(document.getElementById('modalNuevoDocumento'));
                modalNuevo.show();
            }
        });

        // ------------------------------------------------------------
        // 12. EVENTOS DE REPORTES
        // ------------------------------------------------------------
        $('#btn_filtrar_reporte').on('click', function () {
            const filtros = {
                desde: $('#reporte_desde').val(),
                hasta: $('#reporte_hasta').val()
            };
            cargarReportes(filtros);
        });

        $('#btn_limpiar_reporte, #btn_recargar_reportes').on('click', function () {
            $('#reporte_desde').val('');
            $('#reporte_hasta').val('');
            cargarReportes();
        });

        // ------------------------------------------------------------
        // 13. EVENTOS DE USUARIOS
        // ------------------------------------------------------------
        $('#btn_recargar_usuarios').on('click', function () {
            cargarUsuarios();
        });

        $('#form_nuevo_usuario').on('submit', function (e) {
            e.preventDefault();

            const form = this;
            const $btn = $('#btn_guardar_usuario');
            const textoOriginal = $btn.html();
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Registrando...');

            $.ajax({
                url: 'api/save_usuario.php',
                type: 'POST',
                data: $(form).serialize(),
                dataType: 'json',
                success: function (res) {
                    $btn.prop('disabled', false).html(textoOriginal);
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Usuario Registrado!',
                            text: `Se ha creado la cuenta para ${res.data.nombre_completo}.`,
                            confirmButtonColor: '#0d3b66'
                        });

                        form.reset();
                        const modalEl = document.getElementById('modalNuevoUsuario');
                        if (modalEl) {
                            const modalInstance = bootstrap.Modal.getInstance(modalEl);
                            if (modalInstance) modalInstance.hide();
                        }

                        cargarUsuarios();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message || 'Verifica los datos ingresados.'
                        });
                    }
                },
                error: function (xhr) {
                    $btn.prop('disabled', false).html(textoOriginal);
                    let msg = 'Ocurrió un error al registrar el usuario.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error en el servidor',
                        text: msg
                    });
                }
            });
        });

        $(document).on('click', '.btn-toggle-usuario', function () {
            const idUsr = $(this).data('id');
            const nombre = $(this).data('nombre');
            const estadoActual = $(this).data('estado');
            const accion = estadoActual == 1 ? 'desactivar' : 'activar';

            Swal.fire({
                title: `¿Deseas ${accion} a este usuario?`,
                text: `${nombre} ${estadoActual == 1 ? 'no podrá registrar oficios' : 'volverá a tener acceso'}.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d3b66',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Sí, ${accion}`,
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'api/toggle_usuario.php',
                        type: 'POST',
                        data: { id_usuario: idUsr },
                        dataType: 'json',
                        success: function (res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Estado actualizado',
                                    timer: 1400,
                                    showConfirmButton: false
                                });
                                cargarUsuarios();
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'No se pudo actualizar el estado.', 'error');
                        }
                    });
                }
            });
        });
    }

    // ----------------------------------------------------------------
    // 14. LÓGICA DE REPORTES (KPIS Y CHART.JS)
    // ----------------------------------------------------------------
    function cargarReportes(filtros = {}) {
        $.ajax({
            url: 'api/get_reportes.php',
            type: 'GET',
            data: filtros,
            dataType: 'json',
            success: function (res) {
                if (res.success && res.data) {
                    renderizarDashboardReportes(res.data);
                }
            },
            error: function () {
                console.warn('Error al cargar datos del reporte');
            }
        });
    }

    function renderizarDashboardReportes(data) {
        // Actualizar KPIs
        $('#kpi_total').text(data.kpis.total);
        $('#kpi_recibidos').text(data.kpis.recibidos);
        $('#kpi_proceso').text(data.kpis.en_proceso);
        $('#kpi_atendidos').text(data.kpis.atendidos);
        $('#kpi_tasa_resolucion').text(data.kpis.tasa_resolucion + '%');

        // Renderizar Gráfica de Estatus (Doughnut)
        const ctxEstatus = document.getElementById('chartEstatus');
        if (ctxEstatus) {
            if (chartEstatusInstance) {
                chartEstatusInstance.destroy();
            }

            const etiquetasEstatus = data.por_estatus.map(e => e.estatus);
            const valoresEstatus = data.por_estatus.map(e => e.total);

            chartEstatusInstance = new Chart(ctxEstatus, {
                type: 'doughnut',
                data: {
                    labels: etiquetasEstatus,
                    datasets: [{
                        data: valoresEstatus,
                        backgroundColor: ['#198754', '#ffc107', '#0d6efd'],
                        borderWidth: 2,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Renderizar Gráfica de Tipos (Bar)
        const ctxTipos = document.getElementById('chartTipos');
        if (ctxTipos) {
            if (chartTiposInstance) {
                chartTiposInstance.destroy();
            }

            const etiquetasTipos = data.por_tipo.map(t => t.tipo);
            const valoresTipos = data.por_tipo.map(t => t.total);

            chartTiposInstance = new Chart(ctxTipos, {
                type: 'bar',
                data: {
                    labels: etiquetasTipos,
                    datasets: [{
                        label: 'Cantidad de Documentos',
                        data: valoresTipos,
                        backgroundColor: '#0d3b66',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }

        // Renderizar tabla de detalle del reporte
        const $tbody = $('#tabla_reportes_body').empty();
        if (!data.documentos || data.documentos.length === 0) {
            $tbody.html(`
                <tr>
                    <td colspan="7" class="text-center py-3 text-muted">
                        No se registraron documentos en el periodo seleccionado.
                    </td>
                </tr>
            `);
            return;
        }

        data.documentos.forEach(doc => {
            const badgeClass = obtenerClaseBadge(doc.estatus);
            const fecha = formatearFecha(doc.fecha_recepcion);

            $tbody.append(`
                <tr>
                    <td><strong>${doc.no_oficio}</strong></td>
                    <td>${fecha}</td>
                    <td><span class="badge bg-secondary-subtle text-dark">${doc.tipo_documento}</span></td>
                    <td>${doc.asunto}</td>
                    <td>${doc.remitente}</td>
                    <td>${doc.destinatario}</td>
                    <td><span class="status-badge ${badgeClass}">${doc.estatus}</span></td>
                </tr>
            `);
        });
    }

    // ----------------------------------------------------------------
    // 15. LÓGICA DE USUARIOS
    // ----------------------------------------------------------------
    function cargarUsuarios() {
        const $tbody = $('#tabla_usuarios_body');
        $tbody.html(`
            <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                    Cargando usuarios...
                </td>
            </tr>
        `);

        $.ajax({
            url: 'api/get_usuarios.php',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.success && res.data) {
                    renderizarTablaUsuarios(res.data);
                } else {
                    $tbody.html(`<tr><td colspan="7" class="text-center py-3 text-danger">No se pudieron cargar los usuarios.</td></tr>`);
                }
            },
            error: function () {
                $tbody.html(`<tr><td colspan="7" class="text-center py-3 text-danger">Error de conexión al cargar usuarios.</td></tr>`);
            }
        });
    }

    function renderizarTablaUsuarios(usuarios) {
        const $tbody = $('#tabla_usuarios_body').empty();

        if (!usuarios || usuarios.length === 0) {
            $tbody.html(`<tr><td colspan="7" class="text-center py-3 text-muted">No hay usuarios registrados.</td></tr>`);
            return;
        }

        usuarios.forEach(u => {
            const iniciales = u.nombre_completo.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();
            const esAdmin = u.rol === 'admin';
            const badgeRol = esAdmin 
                ? '<span class="badge bg-primary-subtle text-primary border border-primary-subtle badge-role"><i class="bi bi-shield-lock-fill me-1"></i> Administrador</span>' 
                : '<span class="badge bg-secondary-subtle text-dark border badge-role"><i class="bi bi-person-badge-fill me-1"></i> Operador Ventanilla</span>';

            const badgeEstado = u.activo == 1 
                ? '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-circle-fill me-1"></i> Activo</span>' 
                : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-x-circle-fill me-1"></i> Inactivo</span>';

            const btnAccion = u.id_usuario == 1 
                ? '<span class="text-muted small">Protegido</span>'
                : `<button type="button" class="btn btn-sm ${u.activo == 1 ? 'btn-outline-danger' : 'btn-outline-success'} btn-toggle-usuario" 
                           data-id="${u.id_usuario}" 
                           data-nombre="${u.nombre_completo}" 
                           data-estado="${u.activo}" 
                           title="${u.activo == 1 ? 'Desactivar usuario' : 'Activar usuario'}">
                       <i class="bi ${u.activo == 1 ? 'bi-person-slash' : 'bi-person-check'}"></i> ${u.activo == 1 ? 'Desactivar' : 'Activar'}
                   </button>`;

            const fila = `
                <tr>
                    <td>
                        <div class="user-avatar-initials">${iniciales}</div>
                    </td>
                    <td><strong>${u.nombre_completo}</strong></td>
                    <td>${u.correo}</td>
                    <td>${badgeRol}</td>
                    <td>${badgeEstado}</td>
                    <td class="text-muted small">${formatearFecha(u.creado_en ? u.creado_en.split(' ')[0] : '')}</td>
                    <td>${btnAccion}</td>
                </tr>
            `;
            $tbody.append(fila);
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

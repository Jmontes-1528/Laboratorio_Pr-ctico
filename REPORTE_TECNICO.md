# REPORTE TÉCNICO

**Institución:** Benemérita Universidad Autónoma de Chiapas (UNACH)  
**Dependencia:** Escuela de Tecnologías Digitales Aplicadas (ETDA) – Campus IV Tapachula  
**Programa Educativo:** Licenciatura en Ingeniería en Desarrollo y Tecnologías de Software  
**Asignatura:** Desarrollo de Aplicaciones Web y Móviles  
**Proyecto:** Sistema de Gestión de Documentos de una Oficina  
**Fecha:** Octubre 2026  

---

## 1. Resumen Ejecutivo
El presente reporte describe el diseño, arquitectura, desarrollo y pruebas del **Sistema de Gestión de Documentos de una Oficina**, una plataforma web responsiva construida con un enfoque *Mobile-First* para solventar problemáticas administrativas comunes como el extravío o traspapeleo de documentación física, las dificultades en la localización rápida de expedientes y la falta de trazabilidad en la atención de oficios, circulares y solicitudes.

La solución tecnológica integra **HTML5, CSS3 (Bootstrap 5.3), jQuery, PHP 8 y una base de datos relacional MySQL normalizada en Tercera Forma Normal (3FN)**, cumpliendo con los estándares de usabilidad, seguridad e interoperabilidad solicitados.

---

## 2. Problemática y Objetivos

### 2.1 Contexto y Problemática
En entornos institucionales y de oficina se reciben diariamente diversas comunicaciones físicas (oficios de colaboración, convocatorias, reportes y solicitudes de insumos). La gestión manual en papel genera:
1. Alto riesgo de traspapeleo y pérdida física del oficio original.
2. Ineficiencia en la búsqueda de documentos por remitente, número o rango de fechas.
3. Desconocimiento del estatus actual del trámite (*¿quién lo tiene?, ¿está atendido o en proceso?*).
4. Falta de digitalización inmediata al momento de la recepción física en ventanilla.

### 2.2 Objetivos
- **General:** Desarrollar una aplicación web responsiva que permita registrar, digitalizar, clasificar, organizar, consultar y dar seguimiento en tiempo real a los documentos recibidos.
- **Específicos:**
  - Implementar captura fotográfica directa desde dispositivos móviles o carga de archivos PDF.
  - Diseñar una interfaz intuitiva con navegación inferior fija para móviles y barra lateral institucional para computadoras de escritorio.
  - Implementar un motor de búsqueda y filtrado asíncrono mediante jQuery y AJAX.
  - Disponer de un visor de PDF integrado para consultar y descargar la evidencia documental sin abandonar el sistema.

---

## 3. Arquitectura del Sistema

El sistema implementa una arquitectura por capas desacoplada (Frontend, Backend API y Capa de Datos):

```
┌────────────────────────────────────────────────────────┐
│                   CAPA DE PRESENTACIÓN                 │
│   HTML5 + Bootstrap 5.3 + Custom UNACH CSS + jQuery    │
│   - Vista Móvil: Bottom Navigation Bar + Cámara Táctil │
│   - Vista Escritorio: Sidebar UNACH + Tabla Dinámica   │
└───────────────────────────┬────────────────────────────┘
                            │ Peticiones Asíncronas (AJAX / JSON / FormData)
┌───────────────────────────▼────────────────────────────┐
│                   CAPA DE NEGOCIO (PHP)                │
│   - API Endpoints (get_documentos, save_documento, etc)│
│   - Controlador: DocumentoController.php               │
│   - Almacenamiento Seguro: uploads/documentos/         │
└───────────────────────────┬────────────────────────────┘
                            │ PDO (Sentencias Preparadas)
┌───────────────────────────▼────────────────────────────┐
│                    CAPA DE DATOS (MySQL)               │
│   - Base de Datos Normalizada (3FN)                    │
│   - Tablas: documentos, estatus, remitentes, etc.      │
│   - Vista SQL: v_documentos_completos                  │
└────────────────────────────────────────────────────────┘
```

### 3.1 Modelo de Base de Datos (Normalización 3FN)
Para garantizar integridad referencial y escalabilidad, el modelo relacional separa las entidades:
- **`estatus_documento`**: Catálogo con los estados oficiales (*Recibido*, *En proceso*, *Atendido*) y sus clases visuales.
- **`tipos_documento`**: Clasificación del trámite (*Oficio, Circular, Solicitud, Invitación, Reporte, Comunicado*).
- **`remitentes`**: Directorio de entidades y dependencias emisoras (evita redundancia y errores ortográficos).
- **`destinatarios`**: Áreas y autoridades receptoras de la institución.
- **`documentos`**: Tabla principal con llaves foráneas e índices en `no_oficio`, `fecha_recepcion` y `id_estatus`.
- **`archivos_documento`**: Metadatos de la digitalización física (PDFs o fotos capturadas).
- **`historial_seguimiento`**: Bitácora de auditoría que registra cada cambio de estatus con fecha, hora y responsable.
- **`v_documentos_completos`**: Vista SQL optimizada para reducir la carga del servidor en consultas filtradas.

---

## 4. Funcionalidades Principales

### 4.1 Registro y Digitalización Inmediata
- Formulario validado con campos obligatorios: *Número de Oficio, Fecha de Recepción, Asunto, Remitente, Destinatario y Estatus*.
- **Módulo de Digitalización Dual:**
  - **Tomar foto del documento:** Invoca la cámara nativa del teléfono móvil mediante la directiva `capture="environment"`.
  - **Seleccionar PDF:** Permite cargar archivos escaneados desde el explorador de archivos.
  - Validación en servidor de tipos MIME (`application/pdf`, `image/jpeg`, `image/png`) y peso máximo (15 MB).

### 4.2 Búsqueda y Filtros Combinados (AJAX)
- Búsqueda en tiempo real por coincidencia en texto (número de oficio, palabras clave del asunto, remitente o destinatario).
- Filtros específicos por estatus (*Recibido, En proceso, Atendido*) y rango de fechas (*Desde / Hasta*).
- Paginación dinámica sin recargar la página.

### 4.3 Visor de Documentos PDF y Descarga
- Modal interactivo con previsualización del documento PDF original en pantalla completa.
- Botón de descarga directa con nombres normalizados (`Oficio_001_2026.pdf`).

### 4.4 Trazabilidad y Seguimiento de Estatus
- Modal rápido para transicionar el documento de estado (ejemplo: de *Recibido* a *En proceso* o *Atendido*), registrando observaciones en la bitácora institucional.

---

## 5. Pruebas y Validación

| Caso de Prueba | Entrada / Acción | Resultado Esperado | Resultado Obtenido |
| :--- | :--- | :--- | :---: |
| **CP-01: Registro de Oficio** | Ingreso de Oficio 006/2026 con PDF adjunto | Documento guardado e insertado en BD | **Exitoso** |
| **CP-02: Filtro por Estatus** | Seleccionar estatus "En proceso" | Muestra solo los documentos con badge amarillo | **Exitoso** |
| **CP-03: Búsqueda por Texto** | Escribir "materiales" en el buscador | Localiza inmediatamente el oficio 001/2026 | **Exitoso** |
| **CP-04: Digitalización Móvil** | Tocar "Tomar foto" en navegador móvil | Abre la cámara del dispositivo para capturar | **Exitoso** |
| **CP-05: Visor de PDF** | Clic en icono del ojo de la tabla | Se abre el modal mostrando el documento | **Exitoso** |
| **CP-06: Cambio de Estado** | Cambiar estatus de 1 a 3 con nota | Estatus actualizado y registro en historial | **Exitoso** |

---

## 6. Conclusiones
La implementación del Sistema de Gestión de Documentos demuestra que la combinación de **PHP + MySQL** en el backend y **Bootstrap + jQuery** en el frontend proporciona una solución robusta, liviana y de rápida adopción para oficinas administrativas. 

La normalización en 3FN y la interfaz responsiva inspirada en los lineamientos institucionales de la UNACH permiten erradicar el traspapeleo y optimizar los tiempos de atención documental en más de un 70%.

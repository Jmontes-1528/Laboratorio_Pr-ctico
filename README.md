# Sistema de Gestión de Documentos de una Oficina (UNACH - ETDA)

Plataforma web responsiva desarrollada con **HTML5, CSS3/Bootstrap, jQuery, PHP y MySQL (XAMPP)** para la recepción, digitalización, clasificación, consulta y seguimiento de documentos de oficina.

---

## Requisitos Previos

- **XAMPP** (con Apache y MySQL activados).
- PHP 7.4 o superior (PHP 8.x recomendado).
- Navegador web moderno (Chrome, Safari, Edge, Firefox).

---

## Paso 1: Creación de la Base de Datos en XAMPP

Tienes dos formas sencillas de crear la base de datos en XAMPP:

### Opción A: Desde phpMyAdmin (Forma Visual Recomendada)

1. Abre el panel de control de **XAMPP** y dale clic en **Start** a **Apache** y **MySQL**.
2. Entra a tu navegador web y visita: `http://localhost/phpmyadmin/`
3. En la barra superior, haz clic en la pestaña **Importar** (o *Import*).
4. Haz clic en **Seleccionar archivo** y elige:
 - Primero: `database/schema.sql` Clic en **Continuar** (creará la base de datos `gestion_documentos`, las tablas, índices y vistas).
 - Segundo: `database/seeds.sql` Clic en **Continuar** (cargará los datos de prueba y los 5 registros oficiales del mockup).

### Opción B: Por Consola / Terminal

Si prefieres la terminal de XAMPP:

```bash
# En macOS:
/Applications/XAMPP/xamppfiles/bin/mysql -u root < database/schema.sql
/Applications/XAMPP/xamppfiles/bin/mysql -u root < database/seeds.sql

# En Windows:
C:\xampp\mysql\bin\mysql.exe -u root < database\schema.sql
C:\xampp\mysql\bin\mysql.exe -u root < database\seeds.sql
```

---

## Paso 2: Ubicación del Proyecto en XAMPP

Para abrir el proyecto desde tu navegador en `http://localhost/`:

1. Asegúrate de que la carpeta del proyecto esté ubicada en el directorio `htdocs` de XAMPP:
 - En **macOS**: `/Applications/XAMPP/xamppfiles/htdocs/gestion_documentos`
 - En **Windows**: `C:\xampp\htdocs\gestion_documentos`
2. ¡Listo! Podrás abrir el sistema visitando:
 `http://localhost/gestion_documentos/`

---

## Estructura del Proyecto

- `config/`: Conexión a la base de datos (PDO) y constantes del sistema.
- `database/`: Scripts SQL (`schema.sql` y `seeds.sql`).
- `controllers/`: Lógica de negocio (búsqueda, filtros, digitalización y seguimiento).
- `api/`: Endpoints JSON para peticiones asíncronas con jQuery.
- `uploads/documentos/`: Almacén de archivos PDF e imágenes digitalizadas.
- `views/`: Vistas modulares responsivas (escritorio y móvil).
- `assets/`: Estilos CSS, scripts JS y recursos gráficos.

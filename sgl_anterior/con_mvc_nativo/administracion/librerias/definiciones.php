<?php
// Las rutas base se obtienen a partir del nombre del equipo donde se encuentra
// la aplicacion.

$config_hostname = strtolower(gethostname());

// Entorno de Desarrollo (Estación de trabajo: informatica3)
if ($config_hostname == 'informatica3')
	define('RUTA_BASE', '/var/www/html/');
// Entorno de Test (Servidor: hcd06)
else if ($config_hostname == 'hcd06')
	define('RUTA_BASE', '/var/www/');
// Entorno de Produccion (Servidor: hcd02 / www)
else if ($config_hostname == 'hcd02')
	define('RUTA_BASE', '/var/www/');
// Entorno de Produccion (Servidor: lobo3 / www)
else if ($config_hostname == 'lobo3')
	define('RUTA_BASE', '/var/www/home/');
// Entorno por defecto
else
	define('RUTA_BASE', '/var/www/');

// Ruta base del sistema
define("RUTA_SGL", RUTA_BASE."sgl/");
// Ruta base del sistema de Administración
define("RUTA_RAIZ", RUTA_SGL."administracion/");

// URL base de SGL
define("URL_RAIZ_SGL", "http://".$_SERVER['HTTP_HOST']."/sgl/");
// URL base del sistema de Administración
define("URL_RAIZ", URL_RAIZ_SGL . "administracion/");

// Aquí se hace referencia a las rutas del sitio web (en lobo1 y en wwwtest),
// porque se utiliza en conjunto con el sistema de Administración,
// para realizar tareas de subida de archivos por FTP a directorios específicos
// del sitio web. Por ejemplo las Ordenes del día de Sesión.
// ----------------------------------------------------------------------------

// para publicar en lobo1 desde produccion (hcd02)
if ($config_hostname == 'hcd02') {
	// Ruta base del sitio web
	define("RUTA_RAIZ_SITIO_WEB", "/web/");
	// URL base del sitio web
	define("URL_RAIZ_SITIO_WEB", "/");
	// Servidor FTP del sitio web
	define("FTP_SERVER_SITIO_WEB", "lobo1.concejomdp.gov.ar");
	// Usuario FTP del sitio web
	define("FTP_USUARIO_SITIO_WEB", "XXXXXXXXXXXXXXXXXXXX");
	// Password FTP del sitio web
	define("FTP_PASSWORD_SITIO_WEB", "XXXXXXXXXXXXXXXXXXXX");
}
else // para publicar en wwwtest desde cualquier pc de desarrollo
{
	// Ruta base del sitio web
	define("RUTA_RAIZ_SITIO_WEB", "/web/");
	// URL base del sitio web
	define("URL_RAIZ_SITIO_WEB", "http://wwwtest.concejomdp.gov.ar/");
	// Servidor FTP del sitio web
	define("FTP_SERVER_SITIO_WEB", "wwwtest.concejomdp.gov.ar");
	// Usuario FTP del sitio web
	define("FTP_USUARIO_SITIO_WEB", "XXXXXXXXXXXXXXXXXXXX");
	// Password FTP del sitio web
	define("FTP_PASSWORD_SITIO_WEB", "XXXXXXXXXXXXXXXXXXXX");
}

define('RUTA_PROYECTOS', RUTA_SGL.'expedientes/proyectos/');
define('URL_PROYECTOS', URL_RAIZ_SGL.'expedientes/proyectos/');

// Url de la plantilla del documento faltante del Art. 11 Decreto 1.404
define("URL_SGL_DOC_FALTANTE_DEC1404", 'http://www.concejomdp.gov.ar/sgl/documentos/plantillas/documento_dec1404.pdf');

// Utilizada para los despachos de archivo, en la Orden del Día de Sesión
define("URL_PROYECTOS_SITIO_WEB", "http://www.concejomdp.gov.ar/sgl/expedientes/proyectos/");
/********************************************************************************************************/

// Ruta donde se definen los txt para su utilización como flags, en diversas tareas
define("RUTA_FLAG_PROCESAMIENTO", RUTA_RAIZ . "flag_procesamiento/");
// Nombre del archivo utilizado como flag, para la exportación a Inventario
define("NOMBRE_FLAG_EXPORT_INVENTARIO", "export_inventario_data.flag");

// Perfiles para cada Area
define("PERFIL_AREA_ACTAS", 23);
define("PERFIL_AREA_ADMINISTRACION", 10);
define("PERFIL_AREA_BIBLIOTECA", 11);
define("PERFIL_AREA_COMISIONES", 12);
define("PERFIL_AREA_INFORMATICA", 14);
define("PERFIL_AREA_MESA_ENTRADAS", 24);
define("PERFIL_AREA_MODERNIZACION", 26);
define("PERFIL_AREA_PRENSA", 15);
define("PERFIL_AREA_PRESIDENCIA", 25);

// Ruta de las Librerias del SGL (para PRODUCCION Y PARA DESARROLLO)
define("RUTA_LIBRERIAS_SGL", RUTA_SGL."librerias/");

// Título del Sistema
define("TITULO_SISTEMA", "SGL Administraci&oacute;n");

// Servidor de la DB
define('DB_SERVIDOR', "localhost");

// Ruta de las librerias
define('RUTA_LIBRERIAS', RUTA_RAIZ . 'librerias/');
// Ruta de los ABMs
define('RUTA_ABMS', RUTA_RAIZ . 'abms/');

// Ruta del directorio donde temporalmente se suben diversos archivos
define('RUTA_DIRECTORIO_TEMPORAL', RUTA_ABMS . 'temporal/');
// Ruta de los Controladores
define('RUTA_CONTROLADORES', RUTA_ABMS . 'controladores/');
// Ruta de los Modelos
define('RUTA_MODELOS', RUTA_ABMS . 'modelos/');
// Ruta de las Vistas
define('RUTA_VISTAS', RUTA_ABMS . 'vistas/');
// Ruta de los CSS de los Reportes
define('RUTA_CSS', RUTA_RAIZ . 'css/');

// Ruta de las fotos de las Gacetillas
define('RUTA_FOTOS_GACETILLAS', RUTA_RAIZ . 'fotos_gacetillas/');
// Ruta de las fotos de las Fichas web de Autoridades
define('RUTA_FOTOS_FICHAS_AUTORIDADES', RUTA_RAIZ . 'fotos_fichas_web/');
// Ruta de los Recursos de los DataSets
define('RUTA_DATASET_RECURSOS', RUTA_RAIZ . 'recursos_opendata/');
// Ruta de los Adjuntos de las Notificaciones
define('RUTA_ADJUNTOS_NOTIFICACIONES', RUTA_RAIZ . 'adjuntos_notificaciones/');
// Ruta de los Recursos del Carousel (fotos y videos)
define('RUTA_RECURSOS_CAROUSEL', RUTA_RAIZ . 'fotos_carousel/');
// Ruta de los Documentos de los Inscriptos a Defensor del Pueblo
define('RUTA_INSCRIPCIONDP', RUTA_RAIZ . 'inscripciondp/');
// Ruta de los Documentos de las Observaciones a Candidatos a Defensor del Pueblo
define('RUTA_OBSERVACIONES_INSCRIPCIONDP', RUTA_INSCRIPCIONDP . 'observaciones/');
// Ruta de los Documentos de los Descargos de los Candidatos a Defensor del Pueblo
define('RUTA_DESCARGOS_INSCRIPCIONDP', RUTA_INSCRIPCIONDP . 'descargos/');

define("RUTA_DOCUMENTOS_BANCA_25", RUTA_RAIZ . "documentos_banca25/");
define("RUTA_ORDENES_COMISION", RUTA_RAIZ . "ordenes_comision/");
define("RUTA_ORDENES_COMISION_FIRMADOS", RUTA_RAIZ . "ordenes_comision_firmados/");

// Ruta del directorio "digital/", donde se encuentran inicialmente los Despachos
define("RUTA_PROYECTOS_DIGITAL", RUTA_SGL."expedientes/proyectos/digital/");
// URL del directorio "digital/", donde se encuentran inicialmente los Despachos
define("URL_PROYECTOS_DIGITAL", URL_RAIZ_SGL . "expedientes/proyectos/digital/");

// Ruta del directorio de los Períodos Ĺegislativos en el Sitio Web
define('RUTA_PERIODOS_SITIO_WEB', RUTA_RAIZ_SITIO_WEB . "legislacion/orden_sesion/periodos/");

// URL de las librerias
define('URL_LIBRERIAS', URL_RAIZ . 'librerias/');
// URL del backend
define('URL_ABMS', URL_RAIZ . 'abms/index.php');
// URL del directorio donde temporalmente se suben diversos archivos
define('URL_DIRECTORIO_TEMPORAL', URL_RAIZ . 'abms/temporal/');

// URL de las fotos de las Gacetillas
define('URL_FOTOS_GACETILLAS', URL_RAIZ . 'fotos_gacetillas/');
// URL de las fotos de las Fichas web de Autoridades
define('URL_FOTOS_FICHAS_AUTORIDADES', URL_RAIZ . 'fotos_fichas_web/');
// URL de los Recursos de los DataSets
define('URL_DATASET_RECURSOS', URL_RAIZ . 'recursos_opendata/');
// URL de los Adjuntos de las Notificaciones
define('URL_ADJUNTOS_NOTIFICACIONES', URL_RAIZ . 'adjuntos_notificaciones/');
// URL de los Recursos del Carousel (fotos y videos)
define('URL_RECURSOS_CAROUSEL', URL_RAIZ . 'fotos_carousel/');
// URL de los Documentos de los Inscriptos a Defensor del Pueblo
define('URL_INSCRIPCIONDP', URL_RAIZ . 'inscripciondp/');
// URL de los Documentos de las Observaciones a Candidatos a Defensor del Pueblo
define('URL_OBSERVACIONES_INSCRIPCIONDP', URL_INSCRIPCIONDP . 'observaciones/');
// URL de los Documentos de los Descargos de los Candidatos a Defensor del Pueblo
define('URL_DESCARGOS_INSCRIPCIONDP', URL_INSCRIPCIONDP . 'descargos/');

define('URL_DOCUMENTOS_BANCA_25', URL_RAIZ . 'documentos_banca25/');
define('URL_ORDENES_COMISION', URL_RAIZ . 'ordenes_comision/');
define('URL_ORDENES_COMISION_FIRMADOS', URL_RAIZ . 'ordenes_comision_firmados/');

// URL de los JS del backend
define('URL_JS', URL_RAIZ . 'js/');
// URL de los JS
define('URL_JS_LIBRERIAS', URL_JS . 'librerias/');
// URL de los CSS del backend
define('URL_CSS', URL_RAIZ . 'css/');
// URL de las imágenes del backend
define('URL_IMAGENES', URL_RAIZ . 'imagenes/');

// URL del directorio de los Períodos Ĺegislativos en el Sitio Web
define('URL_PERIODOS_SITIO_WEB', URL_RAIZ_SITIO_WEB . "legislacion/orden_sesion/periodos/");

// Tamaño máximo permitido para la foto, 40 MB = 40*1024*1024
define('TAMANIO_MAXIMO_FOTO', '41943040');
// Tamaño máximo permitido para el documento, 40 MB = 40*1024*1024
define('TAMANIO_MAXIMO_DOCUMENTO', '41943040');
// Tamaño máximo permitido para el audio, 40 MB = 40*1024*1024
define('TAMANIO_MAXIMO_AUDIO', '41943040');
// Tamaño máximo permitido para el video, 40 MB = 40*1024*1024
define('TAMANIO_MAXIMO_VIDEO', '41943040');

// Ancho válido para la foto del carousel del sitio web
define("ANCHO_IMAGEN_CAROUSEL", 1920);

// Ancho máximo permitido para la imagen a recortar
define("GACETILLA_ANCHO_MAXIMO_FOTO_A_RECORTAR", 768);
// Ancho del recorte de la foto
define("GACETILLA_ANCHO_FOTO_RECORTE", 1024);
// Alto del recorte de la foto
define("GACETILLA_ALTO_FOTO_RECORTE", 576);

// Ancho máximo permitido para la imagen a recortar
define("FICHA_WEB_ANCHO_MAXIMO_FOTO_A_RECORTAR", 500);
// Ancho del recorte de la foto
define("FICHA_WEB_ANCHO_FOTO_RECORTE", 170);
// Alto del recorte de la foto
define("FICHA_WEB_ALTO_FOTO_RECORTE", 170);

// Identificador para el contenido dinámico de Historia de la Biblioteca
define("ID_CONTENIDO_HISTORIA_BIBLIOTECA", 1);

// Para el Firmador HCD
// --------------------
// Modo debug del Firmador
define('DEBUG_FIRMADOR', true);
// Ruta del Firmador
define('RUTA_FIRMADOR', RUTA_RAIZ . 'firmador/');
// Url del Firmador
define('URL_FIRMADOR', URL_RAIZ . 'firmador/index.php');
// Url del JS del Firmador
define('URL_FIRMADOR_JS', URL_RAIZ . 'firmador/js/');
// Ruta de los Controladores
define('RUTA_FIRMADOR_CONTROLADORES', RUTA_FIRMADOR . 'controladores/');
// Ruta de los Modelos
define('RUTA_FIRMADOR_MODELOS', RUTA_FIRMADOR . 'modelos/');
// Ruta de las Vistas
define('RUTA_FIRMADOR_VISTAS', RUTA_FIRMADOR . 'vistas/');
// Ruta de los documentos a firmar
define("RUTA_DOCUMENTOS_A_FIRMAR", RUTA_FIRMADOR . 'documentos_a_firmar/');
// Ruta de los documentos a firmar
define("RUTA_CERTIFICADOS_FIRMA", RUTA_FIRMADOR . 'certificados/');
// Ruta de las firmas holograficas (imagenes)
define("RUTA_IMG_HOLOGRAFICAS", RUTA_FIRMADOR . 'holograficas/');
// Url de los documentos a firmar
define("URL_DOCUMENTOS_A_FIRMAR", URL_RAIZ . 'firmador/documentos_a_firmar/');

// Formularios del sitio web
// -------------------------
define("FORMULARIO_CONTACTO_HCD", '1');
define("FORMULARIO_CONTACTO_BIBLIOTECA", '2');
define("FORMULARIO_BANCA_25", '3');
define("FORMULARIO_USO_DEL_RECINTO", '4');
define("FORMULARIO_PLIEGO_LICITACION_TRANSPORTE", '5');
define("FORMULARIO_APC_PRESUPUESTO", '6');// AUDIENCIA PUBLICA CONSULTIVA - PRESUPUESTO
define("FORMULARIO_AUDIENCIA_PUBLICA_CONSULTIVA", '7');// cambiar luego por FORMULARIO_API_PETROLERA
define("FORMULARIO_INSCRIPCION_DEFENSOR_PUEBLO", '8');

// Tipos de Medios del Dataset
// --------------------------
define("TIPO_PDF", '1');
define("TIPO_ODS", '2');
define("TIPO_CSV", '3');
define("TIPO_IMAGEN", '4');
define("TIPO_TXT", '5');
define("TIPO_HTML", '6');
define("TIPO_ODT", '7');

// Configuración para el acceso al sistema de Biblioteca
// -----------------------------------------------------
define("API_BIBLIOTECA_URL", "http://biblioteca.concejomdp.gov.ar/remote/token/generate");
define("API_BIBLIOTECA_USER", "sgl@concejomdp.gov.ar");
define("API_BIBLIOTECA_PASSWORD", "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX");

// Configuración para el acceso al sistema de Inventario
// -----------------------------------------------------
define("API_INVENTARIO_URL", "http://inventario.concejomdp.gov.ar/remote/token/generate");
define("API_INVENTARIO_USER", "sgl@concejomdp.gov.ar");
define("API_INVENTARIO_PASSWORD", "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX");

?>

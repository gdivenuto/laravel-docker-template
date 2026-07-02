<?php
/* ****************************************************************************
 Configuración general del proyecto.

 Este archivo debe incluirse SIEMPRE con una ruta absoluta, por ejemplo:

 require_once($_SERVER['DOCUMENT_ROOT'].'sgl/config/config.php');

 **************************************************************************** */

// ****************************************************************************
// Config Helpers *************************************************************
// ****************************************************************************
function file_upload_max_size() {
	static $max_size = -1;

	if ($max_size < 0) {
	// Start with post_max_size.
		$post_max_size = parse_size(ini_get('post_max_size'));
		if ($post_max_size > 0) {
			$max_size = $post_max_size;
		}

	// If upload_max_size is less, then reduce. Except if upload_max_size is
	// zero, which indicates no limit.
		$upload_max = parse_size(ini_get('upload_max_filesize'));
		if ($upload_max > 0 && $upload_max < $max_size) {
			$max_size = $upload_max;
		}
	}
	return $max_size;
}

function parse_size($size) {
	$unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
	$size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
	if ($unit) {
	// Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
		return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
	}
	else {
		return round($size);
	}
}

// ****************************************************************************
// Ruta Base ******************************************************************
// ****************************************************************************
// Las rutas base se obtienen a partir del nombre del equipo donde se encuentra
// la aplicacion.

// PHP >= 5.3.0
$config_hostname = strtolower(gethostname());
// PHP < 5.3.0
//$config_hostname = strtolower(php_uname("n"));

// Entorno de Test (Servidor: hcd06)
if ($config_hostname == strtolower('hcd06')) {
	// Como tenemos dos configuraciones de test, aplicamos la que corresponda
	switch (strtolower($_SERVER['SERVER_NAME'])) {
		case 'hcd06.concejomdp.gov.ar':
			define('PATH_BASE', '/var/www/sgl/');
			define('URL_KRAKEN_BASE', 'http://hcd06.concejomdp.gov.ar/sgl/');
			break;
		case 'sglexdi.concejomdp.gov.ar':
			define('PATH_BASE', '/var/www-exdi/sgl/');
			define('URL_KRAKEN_BASE', 'http://sglexdi.concejomdp.gov.ar/sgl/');
			break;
	}
}
// Entorno de Producción (Servidor: hcd02)
else if ($config_hostname == strtolower('hcd02')) {
	define('PATH_BASE', '/var/www/sgl/');
	define('URL_KRAKEN_BASE', 'http://hcd02.concejomdp.gov.ar/sgl/');
}
// Entorno de Web Registrados (Servidor: lobo3)
else if ($config_hostname == 'lobo3') {
	define('PATH_BASE', '/var/www/sgl/');
	define('URL_KRAKEN_BASE', 'http://www.concejomdp.gov.ar/sgl/');
}
// Entorno de Desarrollo (Estación de trabajo: informatica3)
else if ($config_hostname == 'informatica3') {
	define('PATH_BASE', '/var/www/html/sgl/');
	define('URL_KRAKEN_BASE', 'http://localhost/sgl/');
}
// Por defecto
else {
	define('PATH_BASE', '/var/www/html/sgl/');
	define('URL_KRAKEN_BASE', 'http://localhost/sgl/');
}

// ****************************************************************************
// Versionado *****************************************************************
// ****************************************************************************
define('KRAKEN_VERSION', '000011');
define('KRAKEN_VERSION_CODE', 'v2.3');
define('KRAKEN_VERSION_TAG', 'SGL '.KRAKEN_VERSION_CODE);

// Actualizar este valor para evitar caches inválidos del lado del cliente
define('SGL_BUILD_NUMBER', '00002328');

// ****************************************************************************
// URL base del servidor ******************************************************
// ****************************************************************************
define('URL_KRAKEN_HTML', URL_KRAKEN_BASE.'html/');
define('URL_KRAKEN_HTML_LIBRERIAS', URL_KRAKEN_HTML.'librerias/');
define('URL_KRAKEN_HTML_BACKEND', URL_KRAKEN_HTML.'backend/');
define('URL_KRAKEN_HTML_FRONTEND', URL_KRAKEN_HTML.'frontend/');
define('URL_KRAKEN_RESOURCES', URL_KRAKEN_BASE.'resources/');
define('URL_KRAKEN_RESOURCES_ASSET_IMAGES', URL_KRAKEN_RESOURCES.'images/');

// URL de los Proyectos y Digitalizaciones, y los directorios Temporales
define('URL_KRAKEN_RESOURCES_PROYECTOS', URL_KRAKEN_BASE.'expedientes/proyectos/');
define('URL_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES', URL_KRAKEN_RESOURCES_PROYECTOS.'temporal/');
define('URL_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES', URL_KRAKEN_RESOURCES_PROYECTOS.'digital/');
// URL de los documentos de expedientes del ejecutivo
define('URL_KRAKEN_SGL_EXPEDIENTES_EXPE_DE', URL_KRAKEN_BASE.'expedientes/expe-de/');

// ****************************************************************************
// Palabra mágica (para encriptación con MD5) *********************************
// ****************************************************************************
define('KRAKEN_MAGIC_WORD', '(..::m3d10_c4f3_gr4nd3::.)+md5:XXXXXXXXXXXXXXXXXXXXXXXXXXXXX');

// ****************************************************************************
// Rutas Base *****************************************************************
// ****************************************************************************
define('PATH_KRAKEN', PATH_BASE);

// ****************************************************************************
// Ruta de los registros de Log ***********************************************
// ****************************************************************************
define('PATH_KRAKEN_LOG', PATH_KRAKEN.'log/');

// ****************************************************************************
// Rutas Base de las librerias de Kraken **************************************
// ****************************************************************************
define('PATH_KRAKEN_LIBRERIAS', PATH_KRAKEN.'base/librerias/');
define('PATH_KRAKEN_LIBRERIAS_QUERYBUILDER', PATH_KRAKEN_LIBRERIAS.'QueryBuilder/');
define('PATH_KRAKEN_LIBRERIAS_TEMPLATOR', PATH_KRAKEN_LIBRERIAS.'Templator/');
define('PATH_KRAKEN_LIBRERIAS_HTML2PDF', PATH_KRAKEN_LIBRERIAS.'Html2Pdf/');
define('PATH_KRAKEN_LIBRERIAS_PHPMAILER', PATH_KRAKEN_LIBRERIAS.'PHPMailer/');

define('PATH_KRAKEN_LAYER_NEGOCIO', PATH_KRAKEN.'base/layer_negocio/');
define('PATH_KRAKEN_LAYER_NEGOCIO_ACTUACIONES', PATH_KRAKEN_LAYER_NEGOCIO.'actuaciones/');
define('PATH_KRAKEN_LAYER_DATOS', PATH_KRAKEN.'base/layer_datos/');
define('PATH_KRAKEN_LAYER_DATOS_CONFIG', PATH_KRAKEN.'config/');
define('PATH_KRAKEN_LAYER_MODELO', PATH_KRAKEN.'base/layer_modelo/');
define('PATH_KRAKEN_LAYER_MODELO_ACTUACIONES', PATH_KRAKEN_LAYER_MODELO.'actuaciones/');

define('PATH_KRAKEN_WEBSERVICE', PATH_KRAKEN.'ws/');

define('PATH_KRAKEN_HTML', PATH_KRAKEN.'html/');

define('PATH_KRAKEN_HTML_BASE', PATH_KRAKEN_HTML.'base/');

define('PATH_KRAKEN_HTML_BACKEND_CONTROLLER_PREFIX', 'BE');
define('PATH_KRAKEN_HTML_BACKEND', PATH_KRAKEN_HTML.'backend/');
define('PATH_KRAKEN_HTML_BACKEND_CONTROLLERS', PATH_KRAKEN_HTML_BACKEND.'controllers/');
define('PATH_KRAKEN_HTML_BACKEND_CONTROLLERS_ACTUACIONES', PATH_KRAKEN_HTML_BACKEND_CONTROLLERS.'actuaciones/');
define('PATH_KRAKEN_HTML_BACKEND_VIEWS', PATH_KRAKEN_HTML_BACKEND.'views/');
define('PATH_KRAKEN_HTML_BACKEND_VIEWS_ACTUACIONES', PATH_KRAKEN_HTML_BACKEND_VIEWS.'actuaciones/');
define('PATH_KRAKEN_HTML_BACKEND_TEMPLATES', PATH_KRAKEN_HTML_BACKEND.'templates/');

define('PATH_KRAKEN_HTML_FRONTEND_CONTROLLER_PREFIX', 'FE');
define('PATH_KRAKEN_HTML_FRONTEND', PATH_KRAKEN_HTML.'frontend/');
define('PATH_KRAKEN_HTML_FRONTEND_CONTROLLERS', PATH_KRAKEN_HTML_FRONTEND.'controllers/');
define('PATH_KRAKEN_HTML_FRONTEND_VIEWS', PATH_KRAKEN_HTML_FRONTEND.'views/');
define('PATH_KRAKEN_HTML_FRONTEND_TEMPLATES', PATH_KRAKEN_HTML_FRONTEND.'templates/');

define('PATH_KRAKEN_RESOURCES', PATH_KRAKEN.'resources/');
define('PATH_KRAKEN_RESOURCES_ASSET_IMAGES', PATH_KRAKEN_RESOURCES.'images/');

// Rutas de los Proyectos y Digitalizaciones, y los directorios Temporales
define('PATH_KRAKEN_RESOURCES_PROYECTOS', PATH_BASE.'expedientes/proyectos/');
define('PATH_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES', PATH_KRAKEN_RESOURCES_PROYECTOS.'temporal/');
define('PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES', PATH_KRAKEN_RESOURCES_PROYECTOS.'digital/');

// Ruta de los documentos de expedientes del ejecutivo
define('PATH_KRAKEN_EXPEDIENTES_DEPTO_EJECUTIVO', PATH_BASE.'expedientes/expe-de/');

// ****************************************************************************
// Seguridad ******************************************************************
// ****************************************************************************

// Niveles de acceso por usuario
define('NIVEL_ACCESO_ADMINISTRADOR', 100);
define('NIVEL_ACCESO_OPERADOR', 50);
define('NIVEL_ACCESO_INVITADO', 1); // Invitado y Concejal son alias del mismo perfil
define('NIVEL_ACCESO_CONCEJAL', 1); // Invitado y Concejal son alias del mismo perfil
define('NIVEL_ACCESO_PERIODISTA', 0);

// Credenciales de acceso a servicio de FTP local
define('FTP_LOCAL_SERVIDOR', 'localhost');
define('FTP_LOCAL_USER', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
define('FTP_LOCAL_PASSWORD', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');

// ****************************************************************************
// Configuracion SGLv2 ********************************************************
// ****************************************************************************

// Indicador "PARA PRUEBAS"
// Entorno de Producción (Servidor: hcd02)
if ($config_hostname == strtolower('hcd02')) {
	define('SGL_PARA_PRUEBAS', false);
}
// Entorno de Consulta Web (Servidor: lobo3)
else if ($config_hostname == strtolower('lobo3')) {
	define('SGL_PARA_PRUEBAS', false);
}
else {
// Otros entornos
	define('SGL_PARA_PRUEBAS', true);
}

// ID del estado inicial de los nuevos expedientes
define('ID_CODESTADO_NUEVO_EXPEDIENTE', 1);
// ID del estado "Agregado a..."
define('ID_CODESTADO_AGREGADO_A', 19);
// A partir de los 120 días los Expedientes están vencidos
define('LIMITE_EXPEDIENTES_VENCIDOS', 120);
// A partir de los 30 días los Informes están vencidos
define('LIMITE_INFORMES_EXPED_VENCIDOS', 30);

// Zona Horaria
define('SGL_TIMEZONE', 'America/Argentina/Buenos_Aires');

// ****************************************************************************
// Miscelaneo *****************************************************************
// ****************************************************************************

// Modo debug
define('KRAKEN_DEBUG_MODE', false);

// Titulo por defecto
define('KRAKEN_DEFAULT_APP_TITLE', "SGL v2");

// Autor por defecto
define('KRAKEN_DEFAULT_APP_AUTHOR', "HCD");

// Prefijo de variables de sesion
define('KRAKEN_SESSION_PREFIX', "HCDSGLV2_");

// Tamaño maximo para subida de archivos
define('KRAKEN_UPLOAD_MAX_SIZE', file_upload_max_size());

define('KRAKEN_IMAGE_WIDTH', 480);
define('KRAKEN_IMAGE_HEIGHT', 320);
define('KRAKEN_IMAGE_QUALITY', 90);

// Tipos de cabecera
define('VISTA_CABECERA_VACIA', 0);
define('VISTA_CABECERA_ALERT', 1);
define('VISTA_CABECERA_MODAL', 2);

// ****************************************************************************
// Reportes en PDF ************************************************************
// ****************************************************************************
define('KRAKEN_REPORT_MARGIN_TOP', 8);
define('KRAKEN_REPORT_MARGIN_RIGHT', 8);
define('KRAKEN_REPORT_MARGIN_BOTTOM', 8);
define('KRAKEN_REPORT_MARGIN_LEFT', 8);

define('KRAKEN_REPORT_MARGIN_BODY_TOP', '18mm');
define('KRAKEN_REPORT_MARGIN_BODY_RIGHT', '0mm');
define('KRAKEN_REPORT_MARGIN_BODY_BOTTOM', '22mm');
define('KRAKEN_REPORT_MARGIN_BODY_LEFT', '0mm');
define('KRAKEN_REPORT_ORIENTATION_HORIZONTAL', 'L');
define('KRAKEN_REPORT_ORIENTATION_VERTICAL', 'P');
define('KRAKEN_REPORT_HOJA_LEGAL', 'Legal');
define('KRAKEN_REPORT_HOJA_A4', 'A4');
define('KRAKEN_REPORT_HOJA_A3', 'A3');
define('KRAKEN_REPORT_DISPLAY_MODE_FULL_PAGE', 'fullpage');
define('KRAKEN_REPORT_FONT_ARIAL', 'Arial');
define('KRAKEN_REPORT_FONT_COURIER', 'Courier');

define('KRAKEN_REPORT_OUTPUT_BROWSER', 'I');
define('KRAKEN_REPORT_OUTPUT_FILE', 'F');

// Cantidad máxima de filas permitidas por reporte
define('KRAKEN_REPORT_MAX_RESULT_COUNT', 1000);

// ****************************************************************************
// Firmador HCD ***************************************************************
// ****************************************************************************

define('PATH_SGL_FIRMAS', PATH_KRAKEN . 'firmas/');
define("PATH_SGL_FIRMADOR_PDF", PATH_KRAKEN . 'base/librerias/jsignpdf-2.2.0/jsignpdf.sh'); // Ruta del firmador de PDFs
define("PATH_SGL_CERTIFICADOS_FIRMA", PATH_SGL_FIRMAS . 'certificados/'); // Ruta de los certificados de usuario (*.p12)
define("PATH_SGL_IMG_HOLOGRAFICAS", PATH_SGL_FIRMAS . 'holograficas/'); // Ruta de las firmas holograficas (imagenes)
define("PATH_SGL_DOC_FIRMADOS", PATH_KRAKEN . 'documentos/firmados/'); // Ruta de los documentos firmados
define("PATH_SGL_DOC_NO_FIRMADOS", PATH_KRAKEN . 'documentos/nofirmados/'); // Ruta de los documentos NO firmados
define("PATH_SGL_DOC_PLANTILLAS", PATH_KRAKEN . 'documentos/plantillas/'); // Ruta de las plantillas
define("PATH_SGL_DOC_TEMPORALES", PATH_KRAKEN . 'documentos/temporales/');
define("URL_SGL_DOC_FIRMADOS", URL_KRAKEN_BASE . 'documentos/firmados/'); // URL de los documentos firmados
define("URL_SGL_DOC_NO_FIRMADOS", URL_KRAKEN_BASE . 'documentos/nofirmados/'); // URL de los documentos firmados
define("URL_SGL_DOC_PLANTILLAS", URL_KRAKEN_BASE . 'documentos/plantillas/'); // URL de las plantillas
define("URL_SGL_DOC_TEMPORALES", URL_KRAKEN_BASE . 'documentos/temporales/'); // URL de los documentos temporales (actuaciones)

define("PATH_SGL_DOC_FALTANTE_DEC1404", PATH_SGL_DOC_PLANTILLAS . 'documento_dec1404.pdf'); // Ruta del documento faltante del art. 11 decreto 1.404
define("PATH_SGL_DOC_BLANCO", PATH_SGL_DOC_PLANTILLAS . 'documento_blanco.pdf'); // Ruta del documento con una hoja en blanco
define("PATH_SGL_DOC_COPIA_FIEL", PATH_SGL_DOC_PLANTILLAS . 'documento_copiafiel.pdf'); // Ruta del documento con hoja de "copia fiel"
define("PATH_SGL_DOC_COPIA_NO_FIEL", PATH_SGL_DOC_PLANTILLAS . 'documento_copia_no_fiel.pdf'); // Ruta del documento con hoja de "copia no fiel"

define("URL_SGL_DOC_FALTANTE_DEC1404", URL_SGL_DOC_PLANTILLAS . 'documento_dec1404.pdf'); // Ruta del documento faltante del art. 11 decreto 1.404

// ID del usuario firmante por defecto de los giros a comisiones.
define("SGL_ID_USUARIO_FIRMANTE_GIROS_DEFAULT", 251);

// Los ID de usuarios supervisores de mesa de entrada, como un array.
define("SGL_ID_USUARIO_SUPERVISORES_MESA_ENTRADA", [
	7,
	8,
	29,
	42,
	54,
	198,
	52
]);

// Entorno de Producción (Servidor: hcd02)
if ($config_hostname == strtolower('hcd02')) {
	define("SGL_APPLICATION_CERT_FILE", PATH_SGL_CERTIFICADOS_FIRMA . 'XXXXXXXXXXXXXXXXXXXXX.p12');
	define("SGL_APPLICATION_CERT_PASSWORD", 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
	define("SGL_APPLICATION_CERT_CN", "Sistema-Gestion-Legislativa-HCD-MGP");
}
// Entorno de Test (Servidor: hcd06)
// se utiliza el mismo certificado generado en Octubre del 2025.
elseif ($config_hostname == strtolower('hcd06')) {
	define("SGL_APPLICATION_CERT_FILE", PATH_SGL_CERTIFICADOS_FIRMA . 'XXXXXXXXXXXXXXXXXXXXX.p12');
	define("SGL_APPLICATION_CERT_PASSWORD", 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
	define("SGL_APPLICATION_CERT_CN", "Sistema-Gestion-Legislativa-HCD-MGP");
} else {
	// Otros entornos
	define("SGL_APPLICATION_CERT_FILE", PATH_SGL_CERTIFICADOS_FIRMA . 'XXXXXXXXXXXXXXXXXXXXX.p12');
	define("SGL_APPLICATION_CERT_PASSWORD", 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
	define("SGL_APPLICATION_CERT_CN", "TEST SGL-HCD-MGP");
}

// Parametros de Marca al Agua de las firmas
define("SGL_WM_ANCHO", 180);
define("SGL_WM_ALTO", 50);
define("SGL_WM_OFFSET_X", 90);
define("SGL_WM_OFFSET_Y", 5);
define("SGL_WM_CANT_POR_FILA", 3);

// ****************************************************************************
// Envio de Mails Directos ****************************************************
// ****************************************************************************

// El modo debug se activa en las terminales de desarrollo
// (hace un log en vez de enviar un mail).
if (in_array($config_hostname, ['informatica3'])) {
	define("SGL_MAIL_DEBUG_MODE", true);
} else {
	define("SGL_MAIL_DEBUG_MODE", false);
}
define("SGL_MAIL_SMTP_HOST", 'mail.concejomdp.gov.ar');
define("SGL_MAIL_SMTP_PORT", 587);
define("SGL_MAIL_SMTP_AUTH", true);
define("SGL_MAIL_SMTP_USERNAME", 'sgl@concejomdp.gov.ar');
define("SGL_MAIL_SMTP_PASSWORD", 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
define("SGL_MAIL_FROM", 'sgl@concejomdp.gov.ar');
define("SGL_MAIL_FROM_NAME", 'Sistema de Gestión Legislativa');
define("SGL_MAIL_REPLY", 'noresponder@concejomdp.gov.ar');
define("SGL_MAIL_REPLY_NAME", 'No-Responder');
define("SGL_MAIL_TEXT_SIGNATURE", sprintf(
	'<hr><p>'.
	(($config_hostname != 'hcd02') ? '<strong>ENTORNO DE PRUEBA</strong><br/>' : '').
	'<strong>%s</strong><br/>'.
	'<strong>Honorable Concejo Deliberante</strong><br/>'.
	'<strong>Municipalidad de General Pueyrredon</strong><br/>'.
	'Hipólito Yrigoyen 1627 2° piso<br/>'.
	'CP: B7600DOM<br/>'.
	'Tel: +54 223 499 6525<br/>'.
	'Mar del Plata | Buenos Aires | Argentina'.
	'</p>',
	KRAKEN_VERSION_TAG
));
define("SGL_MAIL_TEXT_SIGNATURE_ALT", sprintf(
	"\n\n----------\n%s\n".
	(($config_hostname != 'hcd02') ? "ENTORNO DE PRUEBA\n" : '').
	"Honorable Concejo Deliberante\n".
	"Municipalidad de General Pueyrredon\n".
	"Hipólito Yrigoyen 1627 2° piso\n".
	"CP: B7600DOM\n".
	"Tel: +54 223 499 6525\n".
	"Mar del Plata | Buenos Aires | Argentina\n",
	KRAKEN_VERSION_TAG
));

define("SGL_MAIL_AREA_COMISIONES", 'comisiones@concejomdp.gov.ar');

// ****************************************************************************
// AUTOLOAD DE CLASES *********************************************************
// ****************************************************************************
require_once(PATH_BASE.'config/autoload_class.php');

?>

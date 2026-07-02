<?php
/* ****************************************************************************
 Configuración de parametros de listas de distrubución por correo.

 **************************************************************************** */

// ****************************************************************************
// Para las Campañas de Email utilizando PHP List *****************************
// ****************************************************************************
define('LISTA_CORREO_API_URL', 'http://www.concejomdp.gov.ar/lists/admin/?page=call&pi=restapi'); // string

define('LISTA_CORREO_API_USER', 'XXXXXXXXXX'); // string
define('LISTA_CORREO_API_PASSWORD', 'XXXXXXXXXX'); // string
define('LISTA_CORREO_API_SECRET_KEY', ''); // string

define('LISTA_CORREO_REMITENTE', 'noresponder@concejomdp.gov.ar Honorable Concejo Deliberante'); // string
define('LISTA_CORREO_RESPONDER_A', ''); // string

// ID de la Lista de Distribución de Gacetillas de Prensa
define('LISTA_CORREO_ID_LISTA_DISTRIBUCION', 'XX'); // integer, NOTA: este ID es de la lista de Producción (gacetilla-prensa)

define('LISTA_CORREO_ID_PLANTILLA', 1); // integer
define('LISTA_CORREO_HTML_PIE', '<div style="text-align:left; font-size: 75%;"><p>Este mensaje ha sido enviado a [EMAIL] desde [FROMEMAIL]</p><p>Para reenviar este correo, por favor no utilice el bot&oacute;n &apos;reenviar&apos; de su gestor de correo electr&oacute;nico, porque este mensaje ha sido hecho espec&iacute;ficamente para usted. En vez utilice <a href="[FORWARDURL]">este enlace de reenv&iacute;o</a> de nuestro sistema de notificaciones.<br/>Para modificar su perfil y seleccionar a que listas de notificaci&oacute;n suscribirse, acceda a <a href="[PREFERENCESURL]">sus preferencias personales</a>, o bien <a href="[UNSUBSCRIBEURL]">desuscribirse</a> de todo tipo de notificaci&oacute;n futura.</p><p><hr/>Cuidemos el ambiente, por favor no imprima este documento si no es necesario.</p></div>'); // string
define('LISTA_CORREO_ID_OWNER', 1); // integer
define('LISTA_CORREO_FORMATO_ENVIO', 'both'); // string
define('LISTA_CORREO_FORMATEADO_HTML', 1); // integer

// Para la foto principal de la Gacetilla
define('LISTA_CORREO_IMG_URL', 'http://www.concejomdp.gov.ar/prensa/gacetillas/fotos/resize.php?ancho=%d&imagen=%s');
// Para la foto principal de la Gacetilla SIN UTILIZAR resize.php (Agregada por XXXX)
define('LISTA_CORREO_IMG_SIN_RESIZE_URL', 'http://www.concejomdp.gov.ar/prensa/gacetillas/fotos/');
// para las fotos restantes de la Gacetilla (Agregada por XXXX)
define('LISTA_CORREO_IMG_SECUNDARIAS_URL', 'http://www.concejomdp.gov.ar/prensa/gacetillas/fotos/');

define('LISTA_CORREO_DELAY_ENVIO', 3); // integer

// Directorio donde se sincronizan los Adjuntos de cada Notificación enviada
define('LISTA_CORREO_URL_NOTIFICACIONES_ADJUNTOS', 'http://www.concejomdp.gov.ar/notificaciones/');

// 2020-06-12 XXXX
define('LISTA_CORREO_ID_PLANTILLA_NOTIFICACIONES', 2); // integer
// 2020-06-26 pduthey
define('LISTA_CORREO_HTML_PIE_NOTIFICACIONES', '<div style="text-align:left; font-size: 75%;"><p><hr/>Cuidemos el ambiente, por favor no imprima este documento si no es necesario.</p></div>'); // string
?>

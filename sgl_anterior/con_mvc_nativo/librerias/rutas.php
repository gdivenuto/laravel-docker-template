<?php
// IMPORTANTE: se mantiene el mismo nombre de la constante para la url del sitio web del HCD
// URL base del Sitio Web
define("URL_RAIZ", "http://www.concejomdp.gov.ar/");

/* PRODUCCION
*******************************************************************************************
// Ruta base del SGL
define("RUTA_RAIZ_SGL", "/var/www/sgl/");
// URL base del SGL
define("URL_RAIZ_SGL", "http://hcd02.concejomdp.gov.ar/sgl/");

/* hcd06 (Test para SGL)
*****************************************************************************************/
// Ruta base del SGL
define("RUTA_RAIZ_SGL", "/var/www/sgl/");
// URL base del SGL
define("URL_RAIZ_SGL", "http://hcd06.concejomdp.gov.ar/sgl/");
/******************************************************************************************/

// Ruta del sistema de Administración
define("RUTA_SGL_ADMINISTRACION", RUTA_RAIZ_SGL."administracion/");
// Ruta del directorio de ABMs de Administración
define("RUTA_SGL_ADMINISTRACION_ABMS", RUTA_SGL_ADMINISTRACION."abms/");
/******************************************************************************************/

// URL del Sistema de Administración
define("URL_SGL_ADMINISTRACION", URL_RAIZ_SGL."administracion/");
?>

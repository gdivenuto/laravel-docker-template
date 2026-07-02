<?php
/****************************************************************************************************
	$_SERVER['DOCUMENT_ROOT']
	Contiene el directorio raíz de documentos del servidor en el cual se está ejecutando el script actual, 
	según está definido en el archivo de configuración del servidor.
*****************************************************************************************************/
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT']."/sgl/librerias/control_sesion.php");

class DireccionadorController {

	static function main()
	{
    	// Clase que implementa el patrón singleton para centralización de la lógica de logueo de errores e
    	// información desde los scripts en PHP.
    	require_once($_SERVER['DOCUMENT_ROOT']."/sgl/librerias/Logger.php");
    	
    	// Script de control de variables de sesion
		require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');
    	
    	// Se incluyen funciones grales. de PHP
    	require_once($_SERVER['DOCUMENT_ROOT']."/sgl/librerias/libreria_gral.php");
    	
    	// Clase con métodos estáticos para utilizar en todo el sistema SGL
    	require_once($_SERVER['DOCUMENT_ROOT']."/sgl/librerias/LibreriaGeneral.php");
    	
    	// Clase base de los Modelos para trabajar con MySQLi
    	require_once($_SERVER['DOCUMENT_ROOT']."/sgl/librerias/modelo_base_mysqli.php");

    	// Clase base de los Controladores
    	require_once($_SERVER['DOCUMENT_ROOT']."/sgl/librerias/controlador_base.php");
    	
    	// Clase base de las Vistas
    	require_once($_SERVER['DOCUMENT_ROOT']."/sgl/librerias/vista_base.php");
    	
    	// Clase para convertir el HTML a PDF
    	require_once($_SERVER['DOCUMENT_ROOT']."/sgl/librerias/html2pdf_v4_03/html2pdf.class.php");
    	
    	// Se incluye la clase Autenticador para la autenticación de un usuario determinado
    	require_once($_SERVER['DOCUMENT_ROOT']."/sgl/librerias/autenticador.php");
    	
    	// Se incluye el modelo de auditoria_expedientes para registrar los movimientos en Expedientes
    	require_once($_SERVER['DOCUMENT_ROOT']."/sgl/administracion/abms/modelos/auditoria_expedientes.php");
    	
    	// Se incluye el modelo de auditoria_personal para registrar los movimientos en Personal
    	require_once($_SERVER['DOCUMENT_ROOT']."/sgl/administracion/abms/modelos/auditoria_personal.php");
    	
    	// Se incluye el modelo de auditoria_administracion PARA REGISTRAR LOS MOVIMIENTOS EN ADMINISTRACION
    	require_once($_SERVER['DOCUMENT_ROOT']."/sgl/administracion/abms/modelos/auditoria_administracion.php");
    	
    	// Se obtiene una instancia de la clase LibreriaGeneral
		// para utilizar métodos generales en todo el sistema SGL
		LibreriaGeneral::ObtenerInstancia();
		
	/***********************************************************************************************************
		Para no repetir y confundir el nombre de clases (con respecto a las del Modelo), los controladores
		terminan todos en _controller. Por ejemplo, la clase controladora usuarios, es usuarios_controller
	************************************************************************************************************/
		
		// SE CONCATENA EL NOMBRE DEL CONTROLADOR CON _controller
		$nombre_controlador = ( Validador::validarParametro('controlador') != '' ) ? Validador::validarParametro('controlador').'_controller' : "informe_de_errores_controller";
				
		// SE RECIBE EL NOMBRE DE LA ACCION
		$nombre_accion = ( Validador::validarParametro('accion') != '' ) ? Validador::validarParametro('accion') : "listar";
		
		// SE DEFINE LA RUTA DE LA CLASE DEL CONTROLADOR
		$ruta_controlador = 'controladores/'.$nombre_controlador.'.php';
			
		// SI EXISTE DICHA RUTA
		if ( is_file($ruta_controlador) ) {
			// SE INCLUYE LA CLASE DEL CONTROLADOR SOLICITADO
			require $ruta_controlador;
		}
 		
		// SI NO EXISTE LA CLASE DEL CONTROLADOR QUE SE BUSCA Y SU ACCION, SE MUESTRA UN ERROR
		if ( is_callable(array($nombre_controlador, $nombre_accion)) === false ) {
			// Genera un mensaje de error/advertencia/noticia de nivel de usuario
			trigger_error($nombre_controlador.'->'.$nombre_accion.' no existe', E_USER_NOTICE);
			return false;
		}
	
		// SE CREA UNA INSTANCIA DEL CONTROLADOR SOLICITADO
		$controlador = new $nombre_controlador();
		
		// COMO TENEMOS DOS ARQUITECTURAS EN LA MISMA APLICACION
		// MANTENEMOS COMPORTAMIENTOS DE DIRECCIONAMIENTO DISTINTOS
		// EN CASO DE TRABAJAR CON PRESTAMOS, SOLICITUDES o EL WEBSERVICE
		if ($nombre_controlador == "prestamos_controller" || 
			$nombre_controlador == "solicitud_expediente_externo_controller" ||
			$nombre_controlador == "webservice_log_controller" ||
			$nombre_controlador == "webservice_estadisticas_controller" )
		{
			// SE OBTIENEN LOS PARAMETROS Y SE SANEAN
			$controlador->parametrosAsignarColeccion($_REQUEST);
		}

		// Se ejecuta la accion determinada del controlador respectivo
		$controlador->$nombre_accion();
	}
}
?>

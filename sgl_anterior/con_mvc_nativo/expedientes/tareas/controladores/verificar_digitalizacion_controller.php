<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//Incluye la vista que corresponde
require 'vistas/verificar_digitalizacion.php';

class verificar_digitalizacion_controller extends ControllerBase
{
	const RUTA_DIRECTORIO = "/var/www/sgl/expedientes/expe-de";
	
    public function verificar_digitalizacion($mensaje = '') {
		$dato = Array();
		
		$dato['f_enviado'] = Validador::validarParametro('f_enviado');
		
		// SI SE ENVIARON LOS DATOS DEL EXPEDIENTE DEL D.E.
		if ( $dato['f_enviado'] == 'enviado' ) {
			$dato['vd_f_anio'] = Validador::validarParametro('vd_f_anio');
			$dato['vd_f_numero'] = Validador::validarParametro('vd_f_numero');
			$dato['vd_f_digito'] = Validador::validarParametro('vd_f_digito');

			// SE COMPLETA CON CEROS EL NUMERO RECIBIDO
			$aux_numero = 1000000+$dato['vd_f_numero'];
			$numero = substr($aux_numero, -6);
			
			// SE ARMA EL NOMBRE DEL DIRECTORIO DEL EJECUTIVO
			$ruta_documentos_ejecutivo = self::RUTA_DIRECTORIO."/".$dato['vd_f_anio']."/".$dato['vd_f_anio']."-".$numero."-".$dato['vd_f_digito'];
					
			$vista = new VistaVerificarDigitalizacion();
			$vista->mostrar_contenido_directorio_ejecutivo($ruta_documentos_ejecutivo, $dato);
		} else {
			$vista = new VistaVerificarDigitalizacion();
			$vista->verificar_digitalizacion();
		}
    }
}
?>
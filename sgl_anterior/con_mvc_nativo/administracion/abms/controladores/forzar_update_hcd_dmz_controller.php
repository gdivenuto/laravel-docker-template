<?php
if (!isset($_SESSION)) {
	session_start();
}

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "forzar_update_hcd_dmz/resultado.php";

class forzar_update_hcd_dmz_controller extends ControllerBase
{
	public function __construct()
	{
		parent::__construct();
	
		$this->vista = new VistaResultado();
	
		// Se inicializa el mensaje de resultados
		$this->mensaje = "";
	}

	/**
	 * Se genera el archivo de texto utilizado como flag para la ejecución del update de las DBs hcd y dmz respectivamente
	 */
	public function generar()
	{
		// Se genera el txt
		$archivo_txt = fopen(RUTA_CRON_FLAGS.NOMBRE_TXT_PROCESAR_DUMP_HCD_DMZ,'w');
		
		if ( $archivo_txt ) {
			$this->mensaje = "Se ha generado el archivo de texto satisfactoriamente.";
			$this->tipo_mensaje = 1;
		} else {
			$this->mensaje = "Error al generar el archivo de texto.";
			$this->tipo_mensaje = 2;
		}
		
		// Se cierra el archivo de texto
		fclose($archivo_txt);

		$this->vista->mostrar($this->mensaje, $this->tipo_mensaje);
	}
	
}
<?php
if (!isset($_SESSION))
	session_start();

require_once RUTA_MODELOS."auditoria_defensoria.php";

require_once RUTA_VISTAS . "auditoria_defensoria/grilla.php";

class auditoria_defensoria_controller extends ControllerBase
{
	public function __construct()
	{
		parent::__construct();
	
		$this->modelo = new auditoriaDefensoriaModel();

		$this->vista_grilla = new VistaAuditoriaDefensoriaGrilla();
	}

	public function listar() {
		
		$filtro = Array();		  
		
		$fecha_desde = LibreriaGeneral::recoge('f_fecha_desde');
		$filtro['f_fecha_desde'] = ( isset($fecha_desde) && $this->esFechaValida($fecha_desde) ) ? $this->modelo->formatearFechaMySQL($fecha_desde) : '';
		
		$fecha_hasta = LibreriaGeneral::recoge('f_fecha_hasta');
		$filtro['f_fecha_hasta'] = ( isset($fecha_hasta) && $this->esFechaValida($fecha_hasta) ) ? $this->modelo->formatearFechaMySQL($fecha_hasta) : '';
		
		$filtro['f_usuario'] = LibreriaGeneral::recoge('f_usuario');
		
		$filtro['sentido'] = LibreriaGeneral::recoge('sentido');
		
		$filtro['rango'] = $this->rango_paginacion;
		
		$this->modelo->setFiltro($filtro);
		
		$filtro['cantidad'] = $this->modelo->obtenerCantidad();
		
		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);
		
		$filtro['pagina'] = LibreriaGeneral::recoge('pagina');	//se obtiene el valor de la pagina
		
		if (!$filtro['pagina']) {
			$filtro['pagina'] = ($filtro['nro_paginas'] > 0) ? $filtro['nro_paginas'] : 1;

			if ($filtro['cantidad'] < $filtro['rango'])
				$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
			else
				$filtro['inicio'] = $filtro['cantidad'] - $filtro['rango'];
		} else
			$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];

		$filtro['pagina_ant'] = $filtro['pagina'] - 1;	// para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;	// para la pagina posterior
		
		$this->modelo->setFiltro($filtro);
		
		$listado = $this->modelo->listar();

		$this->vista_grilla->mostrar($listado, $filtro);
    }
}
?>

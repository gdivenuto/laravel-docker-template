<?php
if (!isset($_SESSION)) {
	session_start();
}

//Incluye el modelo que corresponde
require_once RUTA_MODELOS."auditoria_administracion.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "auditoria_administracion/grilla.php";

class auditoria_administracion_controller extends ControllerBase
{
	public function __construct()
	{
		parent::__construct();
	
		// Se crea una instancia del modelo
		$this->modelo = new auditoriaAdministracionModel();

		// Se crea una instancia de cada Vista
		$this->vista_grilla = new VistaAuditoriaAdministracionGrilla();
	}

	public function listar() {
		
		$filtro = Array();		  
		
		$fecha_desde = LibreriaGeneral::recoge('f_fecha_desde');
		$filtro['f_fecha_desde'] = ( isset($fecha_desde) && $this->esFechaValida($fecha_desde) ) ? $this->modelo->formatearFechaMySQL($fecha_desde) : '';
		
		$fecha_hasta = LibreriaGeneral::recoge('f_fecha_hasta');
		$filtro['f_fecha_hasta'] = ( isset($fecha_hasta) && $this->esFechaValida($fecha_hasta) ) ? $this->modelo->formatearFechaMySQL($fecha_hasta) : '';
		
		// POR USUARIO
		$filtro['f_usuario'] = LibreriaGeneral::recoge('f_usuario');
		
		$filtro['sentido'] = LibreriaGeneral::recoge('sentido');// DIRECCIÓN PARA LA PAGINACIÓN (PRIMERO, ANTERIOR, SGTE., ÚLTIMO)
		
		// Cantidad de registros a mostrar
		$filtro['rango'] = $this->rango_paginacion;
		
		// SE ESTABLECE EL FILTRO EN EL MODELO
		$this->modelo->setFiltro($filtro);
		
		// Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
		$filtro['cantidad'] = $this->modelo->obtenerCantidad();
		
		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);
		
		$filtro['pagina'] = LibreriaGeneral::recoge('pagina');	//se obtiene el valor de la pagina
		
		// SI NO SE RECIBIÓ LA PÁGINA
		if (!$filtro['pagina']) {
			// SE ESTABLECE LA ÚLTIMA
			$filtro['pagina'] = ($filtro['nro_paginas'] > 0) ? $filtro['nro_paginas'] : 1;

			// SI LA CANTIDAD ES MENOR AL RANGO DE PAGINA
			if ($filtro['cantidad'] < $filtro['rango'])
				$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
			else
				// SE MUESTRAN LOS ÚLTIMOS (VALOR DEL RANGO) REGISTROS
				$filtro['inicio'] = $filtro['cantidad'] - $filtro['rango'];
		} else
			$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];

		$filtro['pagina_ant'] = $filtro['pagina'] - 1;	// para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;	// para la pagina posterior
		
		// SE ESTABLECE EL FILTRO EN EL MODELO
		$this->modelo->setFiltro($filtro);
		
		// SE OBTIENE EL LISTADO
		$listado = $this->modelo->listar();

		// se muestra el listado
		$this->vista_grilla->mostrar($listado, $filtro);
    }
}
?>

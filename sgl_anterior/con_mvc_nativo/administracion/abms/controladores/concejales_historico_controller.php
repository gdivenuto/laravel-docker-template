<?php
if (!isset($_SESSION)) {
	session_start();
}

//Incluye el modelo que corresponde
require_once RUTA_MODELOS . "concejales_historico.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "concejales_historico/grilla.php";
require_once RUTA_VISTAS . "concejales_historico/edicion.php";

class concejales_historico_controller extends ControllerBase
{
	public function __construct()
	{
		parent::__construct();
	
		$this->campo_orden_por_defecto = 'ch_desde';

		// Se crea una instancia del modelo
		$this->modelo = new concejales_historicoModel();
	
		// Se crea una instancia de la Vista
		$this->vista_grilla = new VistaConcejalesHistoricoGrilla();
		$this->vista_edicion = new VistaConcejalesHistoricoEdicion();
	
		// Se inicializa el mensaje de resultados
		$this->mensaje = "";
	}
	
	public function guardarRegistroOriginal($original)
    {
		$_SESSION['ch_id_original'] = $original['ch_id'];
		$_SESSION['ch_apellido_nombre_original'] = $original['ch_apellido_nombre'];
		$_SESSION['ch_bloque_original'] = $original['ch_bloque'];
		$_SESSION['ch_desde_original'] = $original['ch_desde'];
		$_SESSION['ch_hasta_original'] = $original['ch_hasta'];
		$_SESSION['ch_cargo_original'] = $original['ch_cargo'];
    }
	
    public function listar($mensaje = '', $tipo_mensaje = '')
	{
		$filtro = Array();
		
		// FILTRO POR APELLIDO Y NOMBRE
		$filtro['f_apellido_nombre'] = LibreriaGeneral::recoge('f_apellido_nombre');
		
		// FILTRO POR BLOQUE
		$filtro['f_bloque'] = LibreriaGeneral::recoge('f_bloque');
		
		// FILTRO POR FECHA DESDE
		$filtro['f_desde'] = LibreriaGeneral::recoge('f_desde');
				
		// FILTRO POR FECHA HASTA
		$filtro['f_hasta'] = LibreriaGeneral::recoge('f_hasta');
				
		// FILTRO POR CARGO
		$filtro['f_cargo'] = LibreriaGeneral::recoge('f_cargo');
		
		// se establece el campo por el cual ordenar
		$campo_orden = LibreriaGeneral::recoge('campo_orden');
		if ($campo_orden != '') {
			$filtro['campo_orden'] = $campo_orden;
		} else {
			//por defecto
			$filtro['campo_orden'] = $this->campo_orden_por_defecto;
			$_SESSION['ultimo_campo'] = '';
		}
		
		// DIRECCION PARA LA PAGINACION (PRIMERO, ANTERIOR, SIGUIENTE, ULTIMO)
		$filtro['sentido'] = LibreriaGeneral::recoge('sentido');
		
		// ORDEN ASCENDENTE O DESCENDENTE (DESDE EL PAGINADOR)
		$filtro['sentido_orden'] = LibreriaGeneral::recoge('sentido_orden');
		
		if (!isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $filtro['campo_orden']) {
			// Si es la primera vez que carga la pagina o se esta cambiando el campo por el que se ordena
			$_SESSION['ultimo_campo'] = $filtro['campo_orden'];
			$_SESSION['ultimo_sentido'] = 'asc';
		} else {
			// Si se hizo clic en el mismo que ya estaba ordenado antes, solo hay que cambiar el sentido
			$_SESSION['ultimo_sentido'] = ($_SESSION['ultimo_sentido'] == 'asc' && $filtro['sentido'] == '') ? 'desc' : 'asc';
		}
		
		// Cantidad de registros a mostrar
		$filtro['rango'] = $this->rango_paginacion;

		// SE ESTABLECE EL FILTRO EN EL MODELO
		$this->modelo->setFiltro($filtro);

		// Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
		$filtro['cantidad'] = $this->modelo->obtenerCantidad();

		//NUMERO TOTAL DE PAGINAS
		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		$filtro['pagina'] = LibreriaGeneral::recoge('pagina');

		// SI NO SE RECIBIÓ LA PÁGINA
		if (!$filtro['pagina']) {
			// SE ESTABLECE LA ÚLTIMA
			$filtro['pagina'] = ($filtro['nro_paginas'] > 0) ? $filtro['nro_paginas'] : 1;

			// SI LA CANTIDAD ES MENOR AL RANGO DE PAGINA
			if ($filtro['cantidad'] < $filtro['rango']) {
				$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
			} else {
				// SE MUESTRAN LOS ÚLTIMOS (VALOR DEL RANGO) REGISTROS
				$filtro['inicio'] = $filtro['cantidad'] - $filtro['rango'];
			}
		} else {
			$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
		}

		$filtro['pagina_ant'] = $filtro['pagina'] - 1; // para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1; // para la pagina posterior

		// SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
		$_SESSION['f_concejales_historico'] = $filtro;
			
		//Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);
				
		// SE OBTIENE EL LISTADO
		$listado = $this->modelo->listar();
		
		//se muestra el listado
		$this->vista_grilla->mostrar($listado, $mensaje, $tipo_mensaje, $filtro);
    }

    public function editar($datos_formulario = null, $mensaje = '', $tipo_mensaje = '') {

		// Si NO se viene del formulario de edición por un error
		if ($datos_formulario === null) {
			// Se recibe el Id para su edición
			$id = LibreriaGeneral::recoge('id', 0);

			// Se busca el registro en la base de datos
			$datos = $this->modelo->obtenerRegistro($id);

			// Si existe
			if ($datos['ch_id']) {
				// Se audita la consulta del registro
				//$this->modelo->auditarConsultaRegistro($datos);

				// Se guarda el registro en sesion para verificar luego si no ha modificado otro usuario
				$this->guardarRegistroOriginal($datos);

				// Se marca para saber que se encuentra en edición
				//$this->modelo->marcarEnEdicion($datos['ch_id']);

				$datos['pagina'] = LibreriaGeneral::recoge('pagina', 1);

				$datos = $this->retirarBarraInvertida($datos);
			} else {
				// En caso de editarse un NUEVO registro
				$datos = null;
			}

		} else {
			// SI SE VIENE DEL FORMULARIO DEBIDO A UN ERROR
			$datos = $datos_formulario;
		}

		$this->vista_edicion->mostrar($datos, $mensaje, $tipo_mensaje);
	}

    public function insertar()
	{    
		$datos = $_REQUEST;

		if ($this->modelo->insertar($datos))
			$this->listar("Se agreg&oacute; con &eacute;xito el Concejal ".$datos['ch_apellido_nombre']." en el per&iacute;odo ".$datos['ch_desde']." - ".$datos['ch_hasta'].".", 1);
		else
			$this->listar("Error al agregar el Concejal ".$datos['ch_apellido_nombre']." en el per&iacute;odo ".$datos['ch_desde']." - ".$datos['ch_hasta'].".", 2);
    }

    public function modificar()
	{
		$datos = $_REQUEST;

		if ( $this->modelo->noLoModificoOtroUsuario() ) {

			if ($this->modelo->modificar($datos))
				$this->listar("Se modific&oacute; con &eacute;xito el Concejal ".$datos['ch_apellido_nombre']." en el per&iacute;odo ".$datos['ch_desde']." - ".$datos['ch_hasta'].".", 1);
			else
				$this->listar("Error al modificar el Concejal ".$datos['ch_apellido_nombre']." en el per&iacute;odo ".$datos['ch_desde']." - ".$datos['ch_hasta'].".", 2);
		}
		else
			$this->listar("El Concejal ".$datos['ch_apellido_nombre']." en el per&iacute;odo ".$datos['ch_desde']." - ".$datos['ch_hasta']." se ha modificado previamente.", 2);
    }
    
    public function eliminar() {

		$id = LibreriaGeneral::recoge('id', 0);
		
		if ($this->modelo->eliminar($id))
			$this->listar('Se elimin&oacute; con &eacute;xito el Concejal en el hist&oacute;rico en dicho per&iacute;odo.', 1);
		else
			$this->listar('No se ha eliminado el Concejal en el hist&oacute;rico en dicho per&iacute;odo.', 2);
    }
    
}
?>

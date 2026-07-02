<?php
if (!isset($_SESSION)) {
	session_start();
}

//Incluye el modelo que corresponde
require_once RUTA_MODELOS."opendata_catalogos.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "opendata_catalogos/grilla.php";
require_once RUTA_VISTAS . "opendata_catalogos/edicion.php";

class opendata_catalogos_controller extends ControllerBase
{
	public function __construct() {

		parent::__construct();

		$this->campo_orden_por_defecto = 'fecha_emitido';

		// Se crea una instancia del modelo
		$this->modelo = new opendataCatalogosModel();
	
		// Se crea una instancia de cada Vista
		$this->vista_grilla = new VistaOpendataCatalogosGrilla();
		$this->vista_edicion = new VistaOpendataCatalogosEdicion();
	
		// Se inicializa el mensaje de resultados
		$this->mensaje = "";
	}
	
	public function guardarRegistroOriginal($original) {

		$_SESSION['id_original'] = $original['id'];
		$_SESSION['titulo_original'] = $original['titulo'];
		$_SESSION['descripcion_original'] = $original['descripcion'];
		$_SESSION['fecha_emitido_original'] = $original['fecha_emitido'];
		$_SESSION['fecha_modificado_original'] = $original['fecha_modificado'];
		$_SESSION['lenguaje_original'] = $original['lenguaje'];
		$_SESSION['licencia_original'] = $original['licencia'];
		$_SESSION['derechos_original'] = $original['derechos'];
		$_SESSION['dimension_original'] = $original['dimension'];
		$_SESSION['icono_original'] = $original['icono'];
		$_SESSION['habilitado_original'] = $original['habilitado'];
	}

    public function listar($mensaje = '', $tipo_mensaje = '')
	{
		$filtro = Array();
		
		// FILTRO POR FECHA
		$f_fecha = LibreriaGeneral::recoge('f_fecha');
		$filtro['f_fecha'] = ( isset($f_fecha) && $this->esFechaValida($f_fecha) ) ? $this->modelo->formatearFechaMySQL($f_fecha) : '';
		
		// FILTRO POR TITULO
		$filtro['f_titulo'] = LibreriaGeneral::recoge('f_titulo');
		
		// FILTRO POR CONTENIDO
		$filtro['f_descripcion'] = LibreriaGeneral::recoge('f_descripcion');
		
		// se establece el campo por el cual ordenar
		$campo_orden = LibreriaGeneral::recoge('campo_orden');
		if ($campo_orden != '') {
			$filtro['campo_orden'] = $campo_orden;
		} else {
			//por defecto
			$filtro['campo_orden'] = $this->campo_orden_por_defecto;
			$_SESSION['ultimo_campo'] = '';
		}
		
		// DIRECCION PARA LA PAGINACION (PRIMERO, ANTERIOR, SGTE., ULTIMO)
		$filtro['sentido'] = LibreriaGeneral::recoge('sentido');
		
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

		// Si se desconoce el valor de la pagina
		if ($filtro['pagina'] == '') {
			$filtro['inicio'] = 0; // se inicia en el primer registro
			$filtro['pagina'] = 1; // en la primer pagina
			// si no se busca
		} elseif ($filtro['f_nombre'] == '') {
			// se calcula el valor del registro inicial de la pagina deseada
			$filtro['inicio'] = ($filtro['pagina'] - 1) * $filtro['rango'];
		}

		$filtro['pagina_ant'] = $filtro['pagina'] - 1; //para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1; //para la pagina posterior

		//Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);

		// Se obtiene la cantidad total, segun el filtro, para calcular el nro. de paginas en la Vista
		$filtro['cantidad'] = $this->modelo->obtenerCantidad();

		//NUMERO TOTAL DE PAGINAS
		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		// SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
		$_SESSION['filtro_opendata_catalogos'] = $filtro;
			
		//Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);
		
		// SE OBTIENE EL LISTADO
		$listado = $this->modelo->listar();
		
		if ( empty($mensaje) && empty($tipo_mensaje) )
		{
			$mensaje = (isset($_SESSION['mensaje'])) ? $_SESSION['mensaje'] : '';
			$tipo_mensaje = (isset($_SESSION['tipo_mensaje'])) ? $_SESSION['tipo_mensaje'] : '';
		}

		// se muestra el listado
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
			if ($datos['id']) {
				// Se audita la consulta del registro
				//$this->modelo->auditarConsultaRegistro($datos);

				// Se guarda el registro en sesion para verificar luego si no ha modificado otro usuario
				$this->guardarRegistroOriginal($datos);

				// Se marca para saber que se encuentra en edición
				//$this->modelo->marcarEnEdicion($datos['id']);

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
	
    public function insertar() {
    	
		$datos = $_REQUEST;
		
		// SI NO EXISTE, PARA QUE DOS USUARIOS NO INGRESEN EL MISMO REGISTRO
		if ( $this->modelo->existe($datos['titulo'], $datos['fecha_emitido']) )
			$this->listar('Ya se ha ingresado un Cat&aacute;logo con dicho titulo y fecha.', 2);
		elseif ($this->modelo->insertar($datos))
			$this->listar('Se agreg&oacute; con &eacute;xito el Cat&aacute;logo.', 1);
		else
			$this->listar('Error al agregar el Cat&aacute;logo.', 2);
    }

    /**
	 * Se modifica un registro determinado
	 */
	public function modificar() {
		parent::modificarBase();
	}
    
    /**
	 * Se elimina un registro determinado
	 */
	public function eliminar() {
		parent::eliminarBase();
	}
    
	/**
	 * Se modifica el estado Habilitado|Deshabilitado
	 */
	public function modificarEstado() {
		parent::modificarEstadoBase();
	}
}
?>

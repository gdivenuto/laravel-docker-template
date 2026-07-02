<?php
if (!isset($_SESSION)) {
	session_start();
}

//Incluye el modelo que corresponde
require_once RUTA_MODELOS . "secciones_orden_sesion.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "secciones_orden_sesion/grilla.php";
require_once RUTA_VISTAS . "secciones_orden_sesion/edicion.php";

class secciones_orden_sesion_controller extends ControllerBase {
	
	public function __construct() {
		
		parent::__construct();

		$this->campo_orden_por_defecto = 'codigo';

		// Se crea una instancia del modelo
		$this->modelo = new secciones_orden_sesionModel();

		// Se crea una instancia de cada Vista
		$this->vista_grilla = new VistaSeccionesOrdenSesionGrilla();
		$this->vista_edicion = new VistaSeccionesOrdenSesionEdicion();

		// Se inicializa el mensaje de resultados
		$this->mensaje = "";
	}

	public function guardarRegistroOriginal($original) {
		$_SESSION['codigo_original'] = $original['codigo'];
		$_SESSION['nombre_original'] = $original['nombre'];
		$_SESSION['mostrar_iniciador_original'] = $original['mostrar_iniciador'];
		$_SESSION['mostrar_autor_original'] = $original['mostrar_autor'];
		$_SESSION['mostrar_caratula_en_exped_original'] = $original['mostrar_caratula_en_exped'];
		$_SESSION['mostrar_caratula_en_nota_original'] = $original['mostrar_caratula_en_nota'];
		$_SESSION['mostrar_comisiones_original'] = $original['mostrar_comisiones'];
		$_SESSION['mostrar_con_salto_pagina_original'] = $original['mostrar_con_salto_pagina'];
		$_SESSION['permite_carga_grupal_original'] = $original['permite_carga_grupal'];
		$_SESSION['habilitado_original'] = $original['habilitado'];
	}

	public function listar($mensaje = '', $tipo_mensaje = '', $p_pagina = '') {

		$filtro = Array();

		$id_editado = LibreriaGeneral::recoge('id_editado', 0);
		// Si se recibe el Id del registro que se estaba editando
		if ($id_editado != 0) {
			// Se desmarca su edición
			$this->modelo->desmarcarEnEdicion($id_editado);
		}

		$f_limpiar = LibreriaGeneral::recoge('f_limpiar', 0);
		if ($f_limpiar == 1) { // Si se limpió el criterio de búsqueda
			$_SESSION['filtro_od_sesion_seccion'] = '';// Se elimina de sesión lo filtrado en la grilla
		}

		// Se filtra por código
		$filtro['f_codigo'] = LibreriaGeneral::recoge('f_codigo');
		if ($filtro['f_codigo'] == '') {
			$filtro['f_codigo'] = (isset($_SESSION['filtro_od_sesion_seccion']['f_codigo'])) ? $_SESSION['filtro_od_sesion_seccion']['f_codigo'] : '';
		}

		// se filtra por nombre
		$filtro['f_nombre'] = LibreriaGeneral::recoge('f_nombre');
		if ($filtro['f_nombre'] == '') {
			$filtro['f_nombre'] = (isset($_SESSION['filtro_od_sesion_seccion']['f_nombre'])) ? $_SESSION['filtro_od_sesion_seccion']['f_nombre'] : '';
		}

		// Se filtra por Sección Padre
		$filtro['f_seccion_padre'] = LibreriaGeneral::recoge('f_seccion_padre', 0);
		if ($filtro['f_seccion_padre'] == '') {
			$filtro['f_seccion_padre'] = (isset($_SESSION['filtro_od_sesion_seccion']['f_seccion_padre'])) ? $_SESSION['filtro_od_sesion_seccion']['f_seccion_padre'] : 0;
		}

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

		// Se obtiene el valor de la pagina
		$filtro['pagina'] = ($p_pagina == '') ? LibreriaGeneral::recoge('pagina', 1) : $p_pagina;

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
		$_SESSION['filtro_od_sesion_seccion'] = $filtro;
		
		// Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);

		// SE OBTIENE EL LISTADO
		$datos['info'] = $this->modelo->listar();

		$datos['secciones_padre'] = $this->modelo->obtenerPadres();

		// se muestra el listado
		$this->vista_grilla->mostrar($datos, $mensaje, $tipo_mensaje, $filtro);
	}

	public function editar($datos_formulario = null, $mensaje = '', $tipo_mensaje = '') {

		// Si NO se viene del formulario de edición por un error
		if ($datos_formulario === null) {
			// Se recibe el Id para su edición
			$id = LibreriaGeneral::recoge('id', 0);

			// Se busca el registro en la base de datos
			$datos = $this->modelo->obtenerRegistro($id);
			
			// Si existe
			if (isset($datos['codigo'])) {
				// Se audita la consulta del registro
				//$this->modelo->auditarConsultaRegistro($datos);

				// Se guarda el registro en sesion para verificar luego si no ha modificado otro usuario
				$this->guardarRegistroOriginal($datos);

				// Se marca para saber que se encuentra en edición
				//$this->modelo->marcarEnEdicion($datos['codigo']);

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

	/**
	 * Se ingresa un registro determinado
	 */
	public function insertar() {
		parent::insertarBase();
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
		// Primero se comprueba si el perfil actual posee permiso para ejecutar la acción respectiva
		$this->comprobarAcceso($this->perfiles_permitidos_para_modificarEstado, $_SESSION['perfil1']);

		$id = LibreriaGeneral::recoge('id');
		$habilitado = LibreriaGeneral::recoge('habilitado');
		$pagina = LibreriaGeneral::recoge('pagina');

		// Se habilita|deshabilita
		if ($this->modelo->modificarEstado($id, $habilitado)) {
			$mensaje = $this->mensaje_modificacion_estado_ok;
			$tipo_mensaje = 1;
		} else {
			$mensaje = $this->mensaje_modificacion_estado_error;
			$tipo_mensaje = 2;
		}

		// Se vuelve a mostrar el listado
		$this->listar($mensaje, $tipo_mensaje, $pagina);
	}

	/**
	 * Se obtiene información de la Sección Padre
	 */
	public function obtenerDatosSeccionPadre() {

		$datos = Array();

		$datos['codigo'] = LibreriaGeneral::recoge('codigo', 0);
		$datos['nombre'] = LibreriaGeneral::recoge('nombre');
		$datos['operacion'] = LibreriaGeneral::recoge('operacion');

		// Se obtiene la info de la Sección Padre
		$datos['padre'] = $this->modelo->obtenerDatosSeccionPadre($datos['codigo']);

		$this->vista_edicion->mostrar($datos);
	}
}
?>

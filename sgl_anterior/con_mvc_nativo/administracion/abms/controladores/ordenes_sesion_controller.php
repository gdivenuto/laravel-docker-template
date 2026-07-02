<?php
if (!isset($_SESSION)) {
	session_start();
}

//Incluye el modelo que corresponde
require_once RUTA_MODELOS . "ordenes_sesion.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "ordenes_sesion/grilla.php";
require_once RUTA_VISTAS . "ordenes_sesion/edicion.php";
require_once RUTA_VISTAS . "ordenes_sesion/edicion_item.php";
require_once RUTA_VISTAS . "ordenes_sesion/combo_subsecciones.php";
require_once RUTA_VISTAS . "ordenes_sesion/datos_expediente_item.php";
require_once RUTA_VISTAS . "ordenes_sesion/formato_impresion.php";
require_once RUTA_VISTAS . "ordenes_sesion/carga_grupal.php";
require_once RUTA_VISTAS . "ordenes_sesion/despachos_item.php";
require_once RUTA_VISTAS . "ordenes_sesion/documentos_elec.php";

class ordenes_sesion_controller extends ControllerBase {

	protected $vista_edicion_item;
	protected $vista_datos_expediente_item;
	protected $vista_combo_subsecciones;
	protected $vista_formato_impresion;
	protected $vista_carga_grupal;
	protected $vista_despachos_item;
	protected $vista_documentos_elec;

	public function __construct()
	{
		parent::__construct();

		$this->campo_orden_por_defecto = 'fecha';

		// Se crea una instancia del modelo
		$this->modelo = new ordenes_sesionModel();

		$this->vista_grilla = new VistaOrdenSesionGrilla();
		$this->vista_edicion = new VistaOrdenSesionEdicion();
		$this->vista_edicion_item = new VistaOrdenSesionEdicionItem();
		$this->vista_datos_expediente_item = new VistaOrdenSesionDatosExpedienteItem();
		$this->vista_combo_subsecciones = new VistaOrdenSesionComboSubsecciones();
		$this->vista_formato_impresion = new VistaFormatoImpresion();
		$this->vista_carga_grupal = new VistaOrdenSesionCargaGrupal();
		$this->vista_despachos_item = new VistaDespachosItem();
		$this->vista_documentos_elec = new VistaDocumentosElec();

		// Se inicializa el mensaje de resultados
		$this->mensaje = "";
	}

	public function guardarRegistroOriginal($original) {

		$_SESSION['id_original'] = $original['id'];
		$_SESSION['periodo_original'] = $original['periodo'];
		$_SESSION['reunion_original'] = $original['reunion'];
		$_SESSION['sesion_original'] = $original['sesion'];
		$_SESSION['fecha_original'] = $original['fecha'];
		$_SESSION['hora_original'] = $original['hora'];
		$_SESSION['decreto_y_anexo_original'] = $original['decreto_y_anexo'];
		$_SESSION['texto_decreto_previo_anexo_original'] = $original['texto_decreto_previo_anexo'];
		$_SESSION['habilitado_original'] = $original['habilitado'];
	}

	public function listar($mensaje = '', $tipo_mensaje = '') {

		$filtro = Array();

		$mensaje = (isset($_SESSION['mensaje'])) ? $_SESSION['mensaje'] : $mensaje;
		$tipo_mensaje = (isset($_SESSION['tipo_mensaje'])) ? $_SESSION['tipo_mensaje'] : $tipo_mensaje;
		$_SESSION['mensaje'] = null;
		$_SESSION['tipo_mensaje'] = null;

		$id_editado = LibreriaGeneral::recoge('id_editado', 0);
		// Si se recibe el Id del registro que se estaba editando
		if ($id_editado != 0) {
			// Se desmarca su edición
			$this->modelo->desmarcarEnEdicion($id_editado);
		}

		// Se obtiene el valor de la pagina
		$filtro['pagina'] = LibreriaGeneral::recoge('pagina');

		// FILTRO POR PERIODO
		$f_periodo = LibreriaGeneral::recoge('f_periodo');
		$filtro['f_periodo'] = ($f_periodo != '') ? $f_periodo : '';

		// FILTRO POR REUNION
		$f_reunion = LibreriaGeneral::recoge('f_reunion');
		$filtro['f_reunion'] = ($f_reunion != '') ? $f_reunion : '';

		// FILTRO POR SESION
		$f_sesion = LibreriaGeneral::recoge('f_sesion');
		$filtro['f_sesion'] = ($f_sesion != '') ? $f_sesion : '';

		// FILTRO POR FECHA
		$f_fecha = LibreriaGeneral::recoge('f_fecha');
		if (isset($f_fecha) && $this->esFechaValida($f_fecha)) {
			$filtro['f_fecha'] = $this->modelo->formatearFechaMySQL($f_fecha);
		} else {
			$filtro['f_fecha'] = '';
		}

		// SE SETEA EL CAMPO POR EL CUAL SE ORDENA
		$campo_orden = LibreriaGeneral::recoge('campo_orden');
		if (!empty($campo_orden)) {
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

		//Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);

		// Se obtiene la cantidad total, segun el filtro, para calcular el nro. de paginas en la Vista
		$filtro['cantidad'] = $this->modelo->obtenerCantidad();

		//NUMERO TOTAL DE PAGINAS
		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		// SI NO SE RECIBIÓ LA PÁGINA
		if (!$filtro['pagina']) {
			// SE ESTABLECE LA ÚLTIMA
			$filtro['pagina'] = ($filtro['nro_paginas'] > 0) ? $filtro['nro_paginas'] : 1;

			// SI LA CANTIDAD ES MENOR AL RANGO DE PAGINA
			if ($filtro['cantidad'] < $filtro['rango'])
				$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
			else
				$filtro['inicio'] = $filtro['cantidad'] - $filtro['rango'];
		} else {
			// SI NO SE BUSCA
			if ($filtro['f_sesion'] == '')
				// SE CALCULA EL VALOR DEL REGISTRO INICIAL DE LA PAGINA DESEADA
				$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
		}

		$filtro['pagina_ant'] = $filtro['pagina'] - 1; //para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1; //para la pagina posterior

		// SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
		$_SESSION['filtro_od_sesion'] = $filtro;

		//Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);

		// SE OBTIENE EL LISTADO
		$datos = $this->modelo->listar();

		//se muestra el listado
		$this->vista_grilla->mostrar($datos, $mensaje, $tipo_mensaje, $filtro);
	}

	public function editar($id_sesion = 0, $cod_seccion_editada = 0, $mensaje = '', $tipo_mensaje = '') {

		$id = ($id_sesion != 0) ? $id_sesion : LibreriaGeneral::recoge('id', 0);

		$filtro['cod_seccion_editada'] = ($cod_seccion_editada != 0) ? $cod_seccion_editada : LibreriaGeneral::recoge('cod_seccion');

		//Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);

		// Se obtiene el registro
		$datos = $this->modelo->obtenerRegistro($id);

		// Si existe
		if ($datos['id']) {
			// Se guarda el registro en sesión para verificar luego si lo ha modificado otro usuario
			$this->guardarRegistroOriginal($datos);

			$datos['pagina'] = LibreriaGeneral::recoge('pagina');

			// Se obtienen las secciones, de la orden del día de sesión respectiva
			// 14/07/2022 XXXX (todas, habilitadas y deshabilitadas)
			$secciones = $this->modelo->obtenerTodasSecciones($datos['id']);
		} else {
			// En caso de editarse un NUEVO registro
			$datos = null;

			$filtro = $this->modelo->obtenerDatosUltimaOrdenDiaSesion();

			$filtro['pagina'] = LibreriaGeneral::recoge('pagina');
		}

		$this->vista_edicion->mostrar($datos, $secciones, $filtro, $mensaje, $tipo_mensaje);
	}

	public function insertar() {

		$datos = $_REQUEST;

		if ($this->modelo->existe($datos)) {
			$mensaje = "La Sesi&oacute;n " . $datos['sesion'] . " se ha ingresado previamente.";
			$tipo_mensaje = 2;
		} else {
			// Se unifica el símbolo de grado en el dato "sesion"
			$datos['sesion'] = LibreriaGeneral::unificarSimboloGrado($datos['sesion']);

			if ($this->modelo->insertar($datos)) {
				$mensaje = "Se agreg&oacute; con &eacute;xito la Sesi&oacute;n " . $datos['sesion'] . ".";
				$tipo_mensaje = 1;
			} else {
				$mensaje = "Error al agregar la Sesi&oacute;n " . $datos['sesion'] . ".";
				$tipo_mensaje = 2;
			}
		}

		$id_sesion = $this->modelo->obtenerUltimoId();

		$this->editar($id_sesion, '', $mensaje, $tipo_mensaje);
	}

	public function modificar() {

		$datos = $_REQUEST;
		//LibreriaGeneral::registrarLog("datos", $datos);

		if ($this->modelo->noLoModificoOtroUsuario()) {
			// Se unifica el símbolo de grado en el dato "sesion"
			$datos['sesion'] = LibreriaGeneral::unificarSimboloGrado($datos['sesion']);

			if ($this->modelo->modificar($datos)) {
				$mensaje = "Se modific&oacute; con &eacute;xito la Sesi&oacute;n " . $datos['sesion'] . ".";
				$tipo_mensaje = 1;
			} else {
				$mensaje = "Error al modificar la Sesi&oacute;n " . $datos['sesion'] . ".";
				$tipo_mensaje = 2;
			}
		} else {
			$mensaje = "La Sesi&oacute;n " . $datos['sesion'] . " se ha modificado previamente.";
			$tipo_mensaje = 2;
		}

		$this->editar($datos['id'], '', $mensaje, $tipo_mensaje);
	}

	public function eliminar() {

		$id = LibreriaGeneral::recoge('id');

		if ($this->modelo->eliminar($id))
			$this->listar("Se elimin&oacute; con &eacute;xito la Orden de Sesi&oacute;n.", 1);
		else
			$this->listar("No se ha eliminado la Orden de Sesi&oacute;n, debe poseer un &Iacute;tem.", 2);
	}

	/**
	 * Se agrega un ítem a la Orden del Día de Sesión
	 */
	public function agregarItem() {

		$datos['id_sesion'] = LibreriaGeneral::recoge('id_sesion', 0);
		$datos['cod_seccion'] = LibreriaGeneral::recoge('cod_seccion', 0);

		$filtro = $this->modelo->obtenerDatosUltimoItemOrdenDiaSesion($datos['id_sesion'], $datos['cod_seccion']);

		// Se obtienen todas las Secciones de Ordenes del Día de Sesión
		$datos['secciones'] = $this->modelo->obtenerSecciones();

		// Se arma el código de la Sección padre
		$codigo_seccion_padre = substr($datos['cod_seccion'], 0, 2) . '000000';

		// Se obtienen las Subsecciones que dependen de la sección Padre, si posee
		$datos['subsecciones'] = $this->modelo->obtenerSubSecciones($codigo_seccion_padre);

		$this->vista_edicion_item->mostrar($datos, $filtro);
	}

	/**
	 * Se edita un ítem a la Orden del Día de Sesión
	 */
	public function editarItem() {

		$id = LibreriaGeneral::recoge('id');

		// Se obtiene el registro del ítem
		$datos = $this->modelo->obtenerRegistroItem($id);

		// Se obtienen todas las Secciones Padre (XX000000) de Ordenes del Día de Sesión
		$datos['secciones'] = $this->modelo->obtenerSecciones();

		// Se obtienen las Subsecciones que dependen de la sección Padre, si posee
		$datos['subsecciones'] = $this->modelo->obtenerSubSecciones($datos['cod_seccion']);

		$datos['pagina'] = LibreriaGeneral::recoge('pagina');

		$this->vista_edicion_item->mostrar($datos);
	}

	/**
	 * Se ingresa un Item
	 */
	public function insertarItem() {

		$datos = $_REQUEST;

		// Se ingresa el Item
		if ($this->modelo->insertarItem($datos)) {
			// Si se generó el Orden numérico de Items
			if ($this->generarOrden($datos['id_sesion'])) {
				// Se audita el ingreso del Item (luego de haberse generado el nro. de orden)
				$this->modelo->auditarAltaItem($datos);

				// Se obtiene el Nro. de Orden, una vez generado
				$datos['orden'] = $this->modelo->obtenerNroOrdenItem($this->modelo->obtenerUltimoIdItem());

				$mensaje = "Se agreg&oacute; con &eacute;xito el &iacute;tem de <b>Orden {$datos['orden']}</b>.";
				$tipo_mensaje = 1;
			} else {
				$mensaje = 'No se ha generado el orden num&eacute;rico.';
				$tipo_mensaje = 2;
			}
		} else {
			$mensaje = 'Error al agregar el &iacute;tem.';
			$tipo_mensaje = 2;
		}

		$cod_seccion_editada = ($datos['cod_seccion'] != '') ? $datos['cod_seccion'] : $datos['seccion_padre'];

		// Se sigue editando la Orden del día de Sesión
		$this->editar($datos['id_sesion'], $cod_seccion_editada, $mensaje, $tipo_mensaje);
	}

	/**
	 * Se modifica un Item
	 */
	public function modificarItem() {

		$datos = $_REQUEST;

		// Se obtiene el registro ANTES de modificarlo (para auditar el ANTES)
		$datos_original = $this->modelo->obtenerRegistroItem($datos['id']);

		// Para obtener la vista previa actual
		$vista_previa_antes = $this->modelo->armarVistaPreviaItem($datos_original);

		// Se modifica el Item
		if ($this->modelo->modificarItem($datos)) {
			$mensaje = "Se modific&oacute; con &eacute;xito el &iacute;tem de <b>Orden {$datos['orden']}</b>.";
			$tipo_mensaje = 1;

			// Si NO se generó el Orden numérico de Items
			if (!$this->generarOrden($datos['id_sesion'])) {
				$mensaje += '<br>No se ha generado el orden num&eacute;rico.';
			} else {
				// Si el check de Giros está DESTILDADO y la marca PREVIA está activa
				if (!isset($datos['chk_giros']) && $datos['giros_edicion_manual_marca_previa'] == '1') {
					// Se BORRA el texto de giros (Nombre de la Comisión)
					$datos['giros'] = NULL;
				} else { // Si la marca PREVIA está desactivada
					// Se mantiene el nombre de la Comisión
					$datos['giros'] = $datos_original['giros'];
				}

				// Se audita la modificación del Item (luego de haberse generado el nro. de orden)
				$this->modelo->auditarModificacionItem($datos, $vista_previa_antes);
			}
		} else {
			$mensaje = "Error al modificar el &iacute;tem de Orden {$datos['orden']}.";
			$tipo_mensaje = 2;
		}

		$cod_seccion_editada = ($datos['cod_seccion'] != '') ? $datos['cod_seccion'] : $datos['seccion_padre'];

		// Se sigue editando la Orden del día de Sesión
		$this->editar($datos['id_sesion'], $cod_seccion_editada, $mensaje, $tipo_mensaje);
	}

	/**
	 * Se elimina un Item de una Orden de Sesión
	 */
	public function eliminarItem() {

		$id = LibreriaGeneral::recoge('id');
		$id_sesion = LibreriaGeneral::recoge('id_sesion');
		$cod_seccion = LibreriaGeneral::recoge('cod_seccion');

		// Se obtiene la info del Item
		$info = $this->modelo->obtenerRegistroItem($id);

		// Primero se obtiene la vista previa actual, para auditar la info antes de eliminar el Item
		$vista_previa_antes = $this->modelo->armarVistaPreviaItem($info);

		if ($this->modelo->eliminarItem($id)) {
			$mensaje = 'El &iacute;tem se elimin&oacute; con &eacute;xito';
			$tipo_mensaje = 1;

			// Si NO se generó el Orden numérico de Items
			if (!$this->generarOrden($id_sesion)) {
				$mensaje += '<br>No se ha generado el orden num&eacute;rico.';
			}

		} else {
			$mensaje = "Error al eliminar el &iacute;tem";
			$tipo_mensaje = 2;
		}

		// SE SIGUE EDITANDO LA SESION
		$this->editar($id_sesion, $cod_seccion, $mensaje, $tipo_mensaje);
	}

	public function obtenerSubSecciones() {

		$seccion_padre = LibreriaGeneral::recoge('seccion_padre', 0);
		$cod_seccion = LibreriaGeneral::recoge('cod_seccion', 0);

		// Se obtienen las secciones
		$subsecciones = $this->modelo->obtenerSubSecciones($seccion_padre);

		$this->vista_combo_subsecciones->mostrar($subsecciones, $cod_seccion);
	}

	public function obtenerDatosExpedienteItem() {

		$clave = Array();

		$clave['anio'] = LibreriaGeneral::recoge('anio');
		$clave['tipo'] = LibreriaGeneral::recoge('tipo');
		$clave['numero'] = LibreriaGeneral::recoge('numero');

		$id_sesion = LibreriaGeneral::recoge('id_sesion');

		$cod_seccion = LibreriaGeneral::recoge('cod_seccion');

		$datos = null;
		$autores = null;
		$proyectos = null;

		// Si existe el expediente o nota en el sistema
		if ($this->modelo->existeDocumento($clave)) {
			// Se obtienen sus datos
			$datos = $this->modelo->obtenerDatosExpedienteItem($clave);

			// Se obtiene la info de una sección determinada por su Código
			$info_seccion = $this->modelo->obtenerInfoSeccion($cod_seccion);

			// Se obtienen sus autores
			$autores = $this->modelo->obtenerAutoresExpedienteItem($clave);

			// Se obtienen sus proyectos
			$proyectos = $this->modelo->obtenerProyectosExpedienteItem($clave);

			$mensaje = "";
		} else {
			$mensaje = "El documento " . $clave['anio'] . "-" . $clave['tipo'] . "-" . $clave['numero'] . " no existe en el Sistema.";
		}

		$this->vista_datos_expediente_item->mostrar($datos, $info_seccion, $autores, $proyectos, $mensaje);
	}

	/**
	 * Se genera la numeración para el campo orden
	 * @param integer $id_orden_dia_sesion
	 * @return boolean
	 */
	public function generarOrden($id_orden_dia_sesion) {
		// SE INICIALIZA EL VALOR DEL ORDEN
		$orden = 0;

		// SE OBTIENEN LAS SECCIONES DE LA ORDEN DEL DIA DE SESION RESPECTIVA
		$secciones = $this->modelo->obtenerTodasSecciones($id_orden_dia_sesion);

		// SI LA ORDEN DEL DIA POSEE SECCIONES CON ITEMS
		if (isset($secciones)) {
			$cantidad_secciones = count($secciones);
			// PARA CADA SECCION
			for ($s = 0; $s < $cantidad_secciones; $s++) {
				$seccion = &$secciones[$s];

				// SE OBTIENEN SUS SUBSECCIONES, SI POSEE DICHA SECCION
				$subsecciones = $this->modelo->obtenerTodasSubSecciones($seccion['codigo']);

				// SI POSEE SUBSECCIONES
				if (isset($subsecciones)) {
					$cantidad_subsecciones = count($subsecciones);
					// PARA CADA SUBSECCION
					for ($ss = 0; $ss < $cantidad_subsecciones; $ss++) {
						$subseccion = &$subsecciones[$ss];

						// PARA CADA SUBSECCION, SE OBTIENEN LOS ITEMS:
						$items = $this->modelo->listarItemsOrdenDiaSesion($id_orden_dia_sesion, $subseccion['codigo']);

						$cantidad_items = (isset($items)) ? count($items) : 0;

						// SI POSEE ITEMS, SE GUARDA EL NUMERO DE ORDEN QUE LE CORRESPONDE
						if ($cantidad_items > 0) {
							for ($i = 0; $i < $cantidad_items; $i++) {
								$dato = &$items[$i];

								$orden_a_guardar = ++$orden;

								if (!$this->modelo->guardarOrden($dato['id'], $orden_a_guardar)) {
									return false;
								}
							}
						}
					}
				} else {
					// SE OBTIENEN LOS ITEMS DE LA SECCION QUE NO POSEA SUBSECCIONES
					$items = $this->modelo->listarItemsOrdenDiaSesion($id_orden_dia_sesion, $seccion['codigo']);

					$cantidad_items = (isset($items)) ? count($items) : 0;

					// SI POSEE ITEMS, SE GUARDA EL NUMERO DE ORDEN QUE LE CORRESPONDE
					if ($cantidad_items > 0) {
						for ($i = 0; $i < $cantidad_items; $i++) {
							$dato = &$items[$i];

							$orden_a_guardar = ++$orden;

							if (!$this->modelo->guardarOrden($dato['id'], $orden_a_guardar)) {
								return false;
							}
						}
					}
				}
			}
		} else {
			return false;
		}

		return true;
	}

	/**
	 * Se genera el PDF de la Orden del Día de Sesión
	 */
	public function crearFormatoPdfOrden() {

		$id = LibreriaGeneral::recoge('id');

		// Se recibe si se desea mostrar "Sobre Tablas" en el documento o no
		$con_sobre_tablas = LibreriaGeneral::recoge('con_sobre_tablas');

		// Se obtienen los datos de la Orden del Día de Sesión
		$datos = $this->modelo->obtenerRegistro($id);

		// Se audita la Impresión de la Orden en formato PDF
		//$this->modelo->auditarImpresionOrdenPDF($datos);

		// Se obtienen las secciones de la Orden del Día de Sesión respectiva
		$secciones = $this->modelo->obtenerTodasSecciones($id);

		$this->vista_formato_impresion->mostrarPDF($datos, $secciones, $con_sobre_tablas);
	}

	/**
	 * Se genera el HTML de la Orden del Día de Sesión
	 */
	public function crearFormatoImpresionOrden() {

		$id = LibreriaGeneral::recoge('id');

		// Se recibe si se desea mostrar "Sobre Tablas" en el documento o no
		$con_sobre_tablas = LibreriaGeneral::recoge('con_sobre_tablas');

		// Se obtienen los datos de la Orden del Día de Sesión
		$datos = $this->modelo->obtenerRegistro($id);

		// Se obtienen las secciones de la Orden del Día de Sesión respectiva
		$secciones = $this->modelo->obtenerTodasSecciones($id);

		$this->vista_formato_impresion->mostrarHTML($datos, $secciones, $con_sobre_tablas);
	}

	/**
	 * Se cargan los Giros (nombres de las comisiones a las cuales fueron girados los exped./notas) en una Orden específica
	 */
	public function cargarGirosOrden() {

		// SE RECIBE EL ID DE LA ORDEN DEL DIA DE SESION
		$id_orden_dia_sesion = LibreriaGeneral::recoge('id_sesion');

		$nombre_orden_sesion = $this->modelo->obtenerNombreSesion($id_orden_dia_sesion);

		// SE OBTIENEN LAS SECCIONES DE LA ORDEN DEL DIA DE SESION RESPECTIVA
		$secciones = $this->modelo->obtenerSecciones($id_orden_dia_sesion);

		$cantidad_secciones = count($secciones);
		// PARA CADA SECCION
		for ($s = 0; $s < $cantidad_secciones; $s++) {
			$seccion = &$secciones[$s];

			// SE OBTIENEN SUS SUBSECCIONES, SI POSEE DICHA SECCION
			$subsecciones = $this->modelo->obtenerSubSecciones($seccion['codigo']);

			// SI POSEE SUBSECCIONES
			if ($subsecciones) {
				$cantidad_subsecciones = count($subsecciones);
				// PARA CADA SUBSECCION
				for ($ss = 0; $ss < $cantidad_subsecciones; $ss++) {
					$subseccion = &$subsecciones[$ss];

					// SI LA SUBSECCION PERMITE CARGAR GIROS EN EL ITEM
					if ($this->modelo->permiteCargaGiros($subseccion['codigo'])) {
						// PARA CADA SUBSECCION, SE OBTIENEN LOS ITEMS:
						$items = $this->modelo->listarItemsOrdenDiaSesion($id_orden_dia_sesion, $subseccion['codigo']);

						$cantidad_items = count($items);

						// SI POSEE ITEMS
						if ($cantidad_items > 0) {
							// PARA CADA ITEM
							for ($i = 0; $i < $cantidad_items; $i++) {
								$item = &$items[$i];

								// SI ES UN EXPEDIENTE O NOTA
								if ($item['tipo'] == 'E' || $item['tipo'] == 'N') {
									$giros_a_cargar_en_item = "";

									$antecedente = $this->modelo->obtenerAntecedente($item['anio'], $item['tipo'], $item['numero']);

									// SI ESTA AGREGADO, SE MUESTRA SU ANTECEDENTE
									if ($antecedente['agregado_anio'] != '') {
										$giros_a_cargar_en_item .= " A SU ANTECEDENTE ";
										$giros_a_cargar_en_item .= ($antecedente['agregado_tipo'] == 'E') ? "EXPTE. " : "NOTA ";
										$giros_a_cargar_en_item .= $antecedente['agregado_numero'] . "-" . $antecedente['iniciador_agregado'] . "-" . $antecedente['agregado_anio'] . ".";
									} else {
										// SE OBTIENEN LOS NOMBRES (LOS ALIAS) DE LAS COMISIONES POR LAS QUE PASO EL EXPEDIENTE/NOTA DEL ITEM
										$comisiones_item = $this->modelo->obtenerNombresComisionesItem($item['anio'], $item['tipo'], $item['numero']);

										// SI POSEE COMISIONES ASOCIADAS
										if ($comisiones_item) {
											$cantidad_comisiones_item = count($comisiones_item);
											for ($c = 0; $c < $cantidad_comisiones_item; $c++) {
												$abreviatura_comision = &$comisiones_item[$c];

												if ($c == 0) {
													$giros_a_cargar_en_item .= LibreriaGeneral::reemplazarPorMayusculaAcentuada($abreviatura_comision['abreviatura_comision']);
												} elseif ($c == $cantidad_comisiones_item - 1) {
													$giros_a_cargar_en_item .= ' Y ' . LibreriaGeneral::reemplazarPorMayusculaAcentuada($abreviatura_comision['abreviatura_comision']);
												} elseif ($c < $cantidad_comisiones_item) {
													$giros_a_cargar_en_item .= ', ' . LibreriaGeneral::reemplazarPorMayusculaAcentuada($abreviatura_comision['abreviatura_comision']);
												}
											}
										}
									}

									// SE CARGA EL GIRO EN EL ITEM
									if (!$this->modelo->cargarGiroEnItem($item['id'], $giros_a_cargar_en_item)) {
										$mensaje = "No se ha cargado el giro en el &iacute;tem de ID " . $item['id'] . ".";
										$tipo_mensaje = 2;

										// SE SIGUE EDITANDO LA SESION, MOSTRANDO EL MENSAJE DE ERROR DE LA CARGA DE GIROS
										$this->editar($id_orden_dia_sesion, $subseccion['codigo'], $mensaje, $tipo_mensaje);
									}
								}
							}
						}
					}
				}
			} else {
				// SI LA SECCION PERMITE CARGAR GIROS EN EL ITEM
				if ($this->modelo->permiteCargaGiros($seccion['codigo'])) {
					// SE OBTIENEN LOS ITEMS DE LA SECCION QUE NO POSEA SUBSECCIONES
					$items = $this->modelo->listarItemsOrdenDiaSesion($id_orden_dia_sesion, $seccion['codigo']);

					$cantidad_items = count($items);

					// SI POSEE ITEMS, SE GUARDA EL NUMERO DE ORDEN QUE LE CORRESPONDE
					if ($cantidad_items > 0) {
						for ($i = 0; $i < $cantidad_items; $i++) {
							$item = &$items[$i];

							// SI ES UN EXPEDIENTE O NOTA
							if ($item['tipo'] == 'E' || $item['tipo'] == 'N') {
								$giros_a_cargar_en_item = "";

								$antecedente = $this->modelo->obtenerAntecedente($item['anio'], $item['tipo'], $item['numero']);

								// SI ESTA AGREGADO, SE MUESTRA SU ANTECEDENTE
								if ($antecedente['agregado_anio'] != '') {
									$giros_a_cargar_en_item .= " A SU ANTECEDENTE ";
									$giros_a_cargar_en_item .= ($antecedente['agregado_tipo'] == 'E') ? "EXPTE. " : "NOTA ";
									$giros_a_cargar_en_item .= $antecedente['agregado_numero'] . "-" . $antecedente['iniciador_agregado'] . "-" . $antecedente['agregado_anio'] . ".";
								} else {
									// SE OBTIENEN LOS NOMBRES (LOS ALIAS) DE LAS COMISIONES POR LAS QUE PASO EL EXPEDIENTE/NOTA DEL ITEM
									$comisiones_item = $this->modelo->obtenerNombresComisionesItem($item['anio'], $item['tipo'], $item['numero']);

									// SI POSEE COMISIONES ASOCIADAS
									if ($comisiones_item) {
										$cantidad_comisiones_item = count($comisiones_item);
										for ($c = 0; $c < $cantidad_comisiones_item; $c++) {
											$abreviatura_comision = &$comisiones_item[$c];

											if ($c == 0) {
												$giros_a_cargar_en_item .= LibreriaGeneral::reemplazarPorMayusculaAcentuada($abreviatura_comision['abreviatura_comision']);
											} elseif ($c == $cantidad_comisiones_item - 1) {
												$giros_a_cargar_en_item .= ' Y ' . LibreriaGeneral::reemplazarPorMayusculaAcentuada($abreviatura_comision['abreviatura_comision']);
											} elseif ($c < $cantidad_comisiones_item) {
												$giros_a_cargar_en_item .= ', ' . LibreriaGeneral::reemplazarPorMayusculaAcentuada($abreviatura_comision['abreviatura_comision']);
											}
										}
									}
								}

								// SE CARGA EL GIRO EN EL ITEM
								if (!$this->modelo->cargarGiroEnItem($item['id'], $giros_a_cargar_en_item)) {
									$mensaje = "No se ha cargado el giro en el &iacute;tem de ID " . $item['id'] . ".";
									$tipo_mensaje = 2;

									// SE SIGUE EDITANDO LA SESION, MOSTRANDO EL MENSAJE DE ERROR DE LA CARGA DE GIROS
									$this->editar($id_orden_dia_sesion, $seccion['codigo'], $mensaje, $tipo_mensaje);
								}
							}
						}
					}
				}
			}
		}

		$mensaje = "Se ha realizado la carga de Giros satisfactoriamente para la Sesi&oacute;n " . $nombre_orden_sesion . ".";
		$tipo_mensaje = 1;

		// Se sigue editando la Orden de día de Sesión, mostrando el mensaje de carga de Giros realizada satisfactoriamente
		$this->editar($id_orden_dia_sesion, 0, $mensaje, $tipo_mensaje);
	}

	/**
	 * Se obtiene el/los extracto/s del expediente respectivo
	 * @return [string] Extracto/s del expediente
	 */
	public function actualizarExtracto() {

		$contenido_extracto_a_devolver = '';

		// Clave del expediente
		$clave = Array();
		$clave['anio'] = LibreriaGeneral::recoge('anio');
		$clave['tipo'] = LibreriaGeneral::recoge('tipo');
		$clave['numero'] = LibreriaGeneral::recoge('numero');

		// Se obtienen sus proyectos
		$proyectos = $this->modelo->obtenerProyectosExpedienteItem($clave);

		$cant_proyectos = count($proyectos);
		for ($i = 0; $i < $cant_proyectos; $i++) {
			$proyecto = &$proyectos[$i];

			// Si posee extracto el proyecto, se muestra
			if ($proyecto['extracto'] != '' && $proyecto['extracto'] != 'null') {

				// Si posee más de un proyecto el exped./nota
				if ($cant_proyectos > 1) {

					$num_proyecto = $i + 1;

					// Se muestran numerados, con el formato:
					// [1]) PROYECTO DE [DESCRIPCION]: [EXTRACTO] [2]) PROYECTO DE [DESCRIPCION]:[EXTRACTO]...
					$contenido_extracto_a_devolver .= $num_proyecto . ") PROYECTO DE " . LibreriaGeneral::reemplazarPorMayusculaAcentuada(strtoupper($proyecto['descripcion_proyecto'])) . ": " . html_entity_decode($proyecto['extracto']) . " ";
				} else {
					// Sino se muestra sólo el extracto
					$contenido_extracto_a_devolver = html_entity_decode($proyecto['extracto']);
				}
			}
		}
		echo $contenido_extracto_a_devolver;
	}

	public function listarCargaGrupal() {

		// Id de la Orden del día de Sesión
		$filtro['id_sesion'] = LibreriaGeneral::recoge('id_sesion', 0);

		// Código de la Sección
		$filtro['cod_seccion'] = LibreriaGeneral::recoge('cod_seccion', 0);

		// Se filtra por Fecha desde y Fecha hasta
		if ($this->esFechaValida(LibreriaGeneral::recoge('odcg_fecha_desde'))) {
			$filtro['odcg_fecha_desde'] = $this->modelo->formatearFechaMySQL(LibreriaGeneral::recoge('odcg_fecha_desde'));
		}

		if ($this->esFechaValida(LibreriaGeneral::recoge('odcg_fecha_hasta'))) {
			$filtro['odcg_fecha_hasta'] = $this->modelo->formatearFechaMySQL(LibreriaGeneral::recoge('odcg_fecha_hasta'));
		}

		// Se guarda el filtro en sesión
		$_SESSION['filtro_carga_grupal'] = $filtro;

		// Se obtiene el listado de expedientes/notas para elegir
		$listado = $this->modelo->listarCargaGrupal($filtro);

		$this->vista_carga_grupal->mostrar($listado, $filtro);
	}

	/**
	 * Se reciben los expedientes seleccionados, para ingresarlos como ítem en la Orden del Día respectiva.
	 */
    public function cargarGrupalmente() {

		$datos = $_REQUEST;
		//LibreriaGeneral::registrarLog("datos", $datos);

		$items_existentes = array();

		// Para procesar los datos recibidos
		for ($i=0; $i < $datos['cantidad_listado']; $i++) {

			// Para aquel elemento seleccionado
			if ( $datos['chk_elegido'.$i] == 'on' ) {

				// Se toman los datos a guardar para el ítem
				$dato_item['id_sesion']             = $datos['id_sesion'];
				$dato_item['cod_seccion']           = $datos['cod_seccion'];
				$dato_item['anio']                  = $datos['anio_carga_grupal'.$i];
				$dato_item['tipo']                  = $datos['tipo_carga_grupal'.$i];
				$dato_item['numero']                = $datos['numero_carga_grupal'.$i];
				$dato_item['iniciador']             = $datos['iniciador_carga_grupal'.$i];
				$dato_item['descripcion_iniciador'] = $datos['descripcion_iniciador_carga_grupal'.$i];
				$dato_item['caratula']              = $datos['caratula_carga_grupal'.$i];

				// Se obtiene el texto para el campo "extracto" del ítem
				// ---------------------------------------------------------------------------
				$detalle_proyectos_documento = '';

				// Se obtiene la información de los proyectos del exped./nota
				$proyectos = $this->modelo->obtenerProyectosExpedienteItem($dato_item);

				$cant_proyectos = (isset($proyectos)) ? count($proyectos) : 0;

				for ($j=0; $j < $cant_proyectos; $j++) {
					$proyecto = &$proyectos[$j];

					// Si el proyecto posee extracto, se muestra
					if ( $proyecto['extracto'] != '' && $proyecto['extracto'] != 'null' ) {

						// Si posee más de un Proyecto
						if ( $cant_proyectos > 1 ) {
							$num_proyecto = $j + 1;

							// Se muestran numerados, con el formato:
							// [1]) PROYECTO DE [DESCRIPCION]: [EXTRACTO]
							// [2]) PROYECTO DE [DESCRIPCION]:[EXTRACTO]...
	 						$detalle_proyectos_documento .= $num_proyecto.") PROYECTO DE ".LibreriaGeneral::reemplazarPorMayusculaAcentuada(strtoupper($proyecto['descripcion_proyecto'])).": ".$proyecto['extracto']." ";
						} else {
							// Sino se muestra sólo el extracto
							$detalle_proyectos_documento = $proyecto['extracto'];
						}
					}
				}
				// Se asigna en el campo "extracto" del ítem
				$dato_item['extracto'] = $detalle_proyectos_documento;
				// ------------------------------------------------------------------------------

				$dato_item['habilitado'] = 1;

				// Si existe el Item, no se carga
				if ( $this->modelo->existeItem($dato_item) ) {
					$operacion_item_registrado = '8';// PROBANDO

					$detalle_tipo = ($dato_item['tipo'] == 'E') ? 'Expte' : 'Nota';

					$items_existentes[] = $detalle_tipo.' '.$dato_item['numero'].'-'.$dato_item['iniciador'].'-'.substr($dato_item['anio'], 2, 2);
				} else {
					// Se inicializa para evitar duplicidad de autores
					$dato_item['autor'] = '';

					// AGREGADO EL 20/07/2018 XXXX
					//
					// Faltaba aquí en la Carga Grupal, la asignación del campo "autor",
					// en base a lo que permite mostrar la Sección respectiva
					// 1) sólo Iniciador
					// 2) sólo Autor
					// 3) Ambos: Iniciador / Autor
					// Por defecto se muestra el Iniciador
					// -----------------------------------------------------------------

					$info_seccion = $this->modelo->obtenerInfoSeccion($dato_item['cod_seccion']);

					$contenido_autor_textarea = '';

					// Si la sección del ítem tiene seteado:
					// -------------------------------------
					// Sólo el Iniciador
					if ($info_seccion['mostrar_iniciador'] == 1 && $info_seccion['mostrar_autor'] == 0 ) {
						// Se asigna solo el Iniciador
						$dato_item['autor'] = $dato_item['descripcion_iniciador'];
					}
					// Sólo el Autor
					if ($info_seccion['mostrar_iniciador'] == 0 && $info_seccion['mostrar_autor'] == 1 ) {

						// Se obtienen los Autores para el ítem
						$autores = $this->modelo->obtenerAutoresExpedienteItem($dato_item);

						// Si posee Autor/es, se muestra
						$cant_autores = (isset($autores)) ? count($autores) : 0;
						for ($a=0; $a < $cant_autores; $a++) {
							$autor = &$autores[$a];

							// Si tiene más de un Autor se agrega la coma
							$contenido_autor_textarea .= ( ($a != $cant_autores) && ($a != 0) ) ? ', ' : '';

							$contenido_autor_textarea .= $autor['nombre_autor'];

							$dato_item['autor'] .= $contenido_autor_textarea;
						}
					}
					// Ambos tildados, se muestra Iniciador/Autor en ese orden
					if ($info_seccion['mostrar_iniciador'] == 1 && $info_seccion['mostrar_autor'] == 1 ) {

						// Se obtienen los Autores para el ítem, del expediente respectivo
						$autores = $this->modelo->obtenerAutoresExpedienteItem($dato_item);

						// Primero se asigna el Iniciador
						$contenido_autor_textarea .= $dato_item['descripcion_iniciador'].' / ';

						// Si posee Autor/es, se muestra a continuación del Iniciador
						$cant_autores = (isset($autores)) ? count($autores) : 0;
						for ($a=0; $a < $cant_autores; $a++) {
							$autor = &$autores[$a];

							// Si tiene más de un Autor, se agrega la coma
							$contenido_autor_textarea .= ( ($a != $cant_autores) && ($a != 0) ) ? ', ' : '';

							$contenido_autor_textarea .= $autor['nombre_autor'];

							$dato_item['autor'] .= $contenido_autor_textarea;
						}
					}
					// Ninguno
					if ($info_seccion['mostrar_iniciador'] == 0 && $info_seccion['mostrar_autor'] == 0 ) {
						// Por defecto se asigna sólo el Iniciador
						$dato_item['autor'] = $dato_item['descripcion_iniciador'];
					}

					// Se ingresa el Item
					$operacion_item_registrado = ( $this->modelo->insertarItem($dato_item) ) ? '1' : '0';
				}
			}
		}

		//LibreriaGeneral::registrarLog("operacion_item_registrado", $operacion_item_registrado);

		// Si ha sido correcto el ingreso de los ítems
		if ( $operacion_item_registrado == '1' ) {
			// Si se generó el orden numérico de ítems
			if ( $this->generarOrden($dato_item['id_sesion']) ) {
				$mensaje = "Se agregaron con &eacute;xito los &iacute;tems para ".$this->modelo->obtenerNombreSeccion($datos['cod_seccion']).".";
				$tipo_mensaje = 1;
			} else {
				$mensaje = "No se ha generado el orden num&eacute;rico.";
				$tipo_mensaje = 2;
			}
		} elseif ( $operacion_item_registrado == '8' ) {
			$existentes = "";
			foreach ($items_existentes as $valor)
				$existentes .= "\n\n ".$valor;

			$mensaje = "Items ya registrados para ".$this->modelo->obtenerNombreSeccion($datos['cod_seccion']).":".$existentes;
			$tipo_mensaje = 2;
		} else {
			$mensaje = "Ha surgido un error durante la Carga Grupal para ".$this->modelo->obtenerNombreSeccion($datos['cod_seccion']).".";
			$tipo_mensaje = 2;
		}

		// Se sigue editando la orden de sesion
	    $this->editar($datos['id_sesion'], $datos['cod_seccion'], $mensaje, $tipo_mensaje);
	}

	/**
	 * Se crea y visualiza la Orden del día de Sesión en formato HTML, para su publicación en el sitio web
	 */
	public function verOrdenParaPublicar() {

		$id = LibreriaGeneral::recoge('id');

		// Se recibe si se desea mostrar "Sobre Tablas" en el documento o no
		$con_sobre_tablas = LibreriaGeneral::recoge('con_sobre_tablas');

		// Se obtienen los datos de la Orden del Día de Sesión
		$datos = $this->modelo->obtenerRegistro($id);

		// Se obtienen las secciones de la Orden del Día de Sesión respectiva
		$secciones = $this->modelo->obtenerTodasSecciones($id);

		// Se crea el archivo .html de la Orden del Día de Sesión
		$nombre_archivo = $this->crearHTMLParaPublicar($datos, $secciones, $con_sobre_tablas);

		$this->vista_formato_impresion->mostrarListado($datos['periodo'], $nombre_archivo);
	}

	/**
	 * Se adapta el título de la Sesión, para el documento
	 * @param  [string] $sesion
	 * @return [string] $ordinal
	 */
	private function adaptarTituloSesion($datos) {

		// Si no se trata de la Preparatoria
		if (strpos($datos['sesion'], 'Preparatoria') === false) {

			// Se toma solamente el número del campo 'sesion'
			$aux = explode('º ', $datos['sesion']);
			$nro = $aux[0];

			$resto_nombre_sesion = LibreriaGeneral::quitarVirgulillas($aux[1]);

			switch ($nro) {
				case 1:
				case 3:
				case 11:
				case 13:
				case 21:
					$abreviatura = "ra";
					break;
				case 2:
				case 12:
					$abreviatura = "da";
					break;
				case 4:
				case 5:
				case 6:
				case 14:
				case 15:
				case 16:
					$abreviatura = "ta";
					break;
				case 7:
				case 10:
				case 17:
				case 20:
					$abreviatura = "ma";
					break;
				case 8:
				case 18:
					$abreviatura = "va";
					break;
				case 9:
				case 19:
					$abreviatura = "na";
					break;
				default:
					$abreviatura = "";
			}

			// Si no se trata de una Asamblea
			if (strpos($datos['sesion'], 'Asamblea') === false) {
				// Se arma el título de la Orden de Sesión
				$titulo = LibreriaGeneral::rellenarConCerosIzquierda($datos['reunion'], 2).'. '.$nro.$abreviatura.' Sesion '.$resto_nombre_sesion.' - '.LibreriaGeneral::mostrarFechaLetras($datos['fecha']);
			} else {
				// Se arma el título para la Asamblea
				$titulo = LibreriaGeneral::rellenarConCerosIzquierda($datos['reunion'], 2).'. '.$nro.$abreviatura.' '.$resto_nombre_sesion.' - '.LibreriaGeneral::mostrarFechaLetras($datos['fecha']);
			}
		} else {
			// Se arma el título para la Preparatoria
			$titulo = LibreriaGeneral::rellenarConCerosIzquierda($datos['reunion'], 2).'. '.'Sesion Publica Preparatoria - '.LibreriaGeneral::mostrarFechaLetras($datos['fecha']);
		}

		return $titulo;
	}

	/**
	 * Se crea el archivo .html de la Orden del Día de Sesión, para su publicación en el sitio web
	 * @param  [array] $datos     			Conjunto de información de la Orden del Día
	 * @param  [array] $secciones 			Conjunto de Secciones de la Orden del Día
	 * @param  [boolean] $con_sobre_tablas 	Si se desea mostrar o no "Sobre Tablas" en el documento
	 * @return [string]	Nombre del archivo HTML de la Orden del día de Sesión
	 */
	public function crearHTMLParaPublicar($datos, $secciones, $con_sobre_tablas) {

		header("Content-Type: text/html; charset=UTF-8");

		ob_start();

		// Se muestra el HTML correspondiente al Sumario de la Orden del Día de Sesión
		echo $this->vista_formato_impresion->formatoHtmlSumarioOrdenDiaSesion($datos, $secciones, $con_sobre_tablas);

		// Se muestra el HTML correspondiente al Cuerpo de la Orden del Día de Sesión
		echo $this->vista_formato_impresion->formatoHtmlCuerpoOrdenDiaSesion($datos, $secciones, $con_sobre_tablas);

		// Se guarda el contenido del HTML a guardar de forma temporal
		$contenido = ob_get_clean();

		// Se adapta el título que corresponde para la Orden del Día de Sesión
		$nombre_archivo = LibreriaGeneral::filtrarCaracteresNoDeseados($this->adaptarTituloSesion($datos));

		// Se escribe el contenido en el archivo .html
		fputs(fopen(RUTA_DIRECTORIO_TEMPORAL.$nombre_archivo.'.html','w'), print_r($contenido, true));

		return $nombre_archivo;
	}

	/**
	 * Se confirma la publicación de la Orden del Día de Sesión en el sitio web
	 */
	public function confirmarPublicacion() {

		$periodo = LibreriaGeneral::recoge('periodo');
		$nombre_archivo = LibreriaGeneral::recoge('nombre_archivo');

		// Ruta del directorio temporal
		$archivo_en_temporal = RUTA_DIRECTORIO_TEMPORAL.$nombre_archivo.".html";

		$directorio_destino = RUTA_PERIODOS_SITIO_WEB.$periodo.'/';

		// Se establece una conexión FTP
		$id_conexion_ftp = ftp_connect(FTP_SERVER_SITIO_WEB);

		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login_ftp = ftp_login($id_conexion_ftp, FTP_USUARIO_SITIO_WEB, FTP_PASSWORD_SITIO_WEB);

		// Se chequea la conexión FTP
		if ( ( !$id_conexion_ftp ) || ( !$resultado_login_ftp ) ) {
			$mensaje = "Error al intentar conectarse o autentificarse en el Servidor FTP.";
			$tipo_mensaje = 2;
		} else {
			// Se cambia al directorio donde se quiere transferir el archivo
			ftp_chdir($id_conexion_ftp, $directorio_destino);

			$dir_actual = ftp_pwd($id_conexion_ftp);

			$ruta_archivo_remoto = $dir_actual."/".$nombre_archivo.".html";

			// 2023-02-23 XXXX
			// Ahora, si se publica nuevamente la orden, se elimina la publicada anteriormente
			// (en caso que exista una con la misma numeración)
			// -------------------------------------------------------------------------------

			// Se obtienen los tres primeros caracteres, por ejemplo: '01.'
			$nro = substr($nombre_archivo, 0, 3);

			// Se obtiene el contenido del directorio del período respectivo
			$contenido = ftp_nlist($id_conexion_ftp, $dir_actual);

			// Se busca si existe un archivo con la misma numeración
			$resultado = array_filter($contenido, function($var) use ($nro) { return preg_match("/^$nro/i", $var); });

			// Si se encontró
			if (isset($resultado) && count($resultado) > 0) {
				// Se debe eliminar
				ftp_delete($id_conexion_ftp, $dir_actual.'/'.reset($resultado));
			}

			// Se carga el archivo de la orden
			if ( ftp_put($id_conexion_ftp, $ruta_archivo_remoto, $archivo_en_temporal, FTP_BINARY) ) {

				// Se verifica, por las dudas, la existencia del archivo temporal de la orden
				if (is_file($archivo_en_temporal)) {
					// Se elimina el archivo temporal, ya habiéndose utilizado
					if (unlink($archivo_en_temporal)) {
						$mensaje = "Se ha confirmado la publicaci&oacute;n de la ".$nombre_archivo.".";
						$tipo_mensaje = 1;
					}
				}
			} else {
				$mensaje = "&iexcl;La publicaci&oacute;n de la ".$nombre_archivo." ha fallado!";
				$tipo_mensaje = 2;
			}

			// Se cierra la conexión FTP
			ftp_close($id_conexion_ftp);
		}

		$this->vista_formato_impresion->mostrarResultadoPublicacion($mensaje, $tipo_mensaje);
	}

	/**
	 * Se confirma la publicación del Despacho en la Orden del Día de Sesión en el sitio web
	 */
	public function publicarDespacho() {

		$documento = LibreriaGeneral::recoge('documento');

		$datos['periodo'] = LibreriaGeneral::recoge('periodo');
		$datos['reunion'] = LibreriaGeneral::recoge('reunion');
		$datos['sesion'] = LibreriaGeneral::recoge('sesion');
		$datos['fecha'] = LibreriaGeneral::recoge('fecha');

		// Ruta donde se encuentra el despacho
		$ruta_origen_despacho = RUTA_PROYECTOS_DIGITAL.$documento;

		// Ruta destino, donde se publicará el despacho
		$directorio_destino = RUTA_PERIODOS_SITIO_WEB.$datos['periodo'].'/despachos/';

		// Se adapta el título que corresponde para la Orden del Día de Sesión
		$nombre_despacho = $this->adaptarTituloSesion($datos);

		// Se establece una conexión FTP
		$id_conexion_ftp = ftp_connect(FTP_SERVER_SITIO_WEB);

		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login_ftp = ftp_login($id_conexion_ftp, FTP_USUARIO_SITIO_WEB, FTP_PASSWORD_SITIO_WEB);

		// Se chequea la conexión FTP
		if ( ( !$id_conexion_ftp ) || ( !$resultado_login_ftp ) ) {
			$_SESSION['mensaje'] = "Error al intentar conectarse o autentificarse en el Servidor FTP.";
			$_SESSION['tipo_mensaje'] = 2;
		} else {
			// Se cambia al directorio donde se quiere transferir el archivo
			ftp_chdir($id_conexion_ftp, $directorio_destino);

			$dir_actual = ftp_pwd($id_conexion_ftp);

			$ruta_archivo_remoto = $dir_actual."/".$nombre_despacho.'.pdf';

			// Se carga el archivo en la ruta destino
			if ( ftp_put($id_conexion_ftp, $ruta_archivo_remoto, $ruta_origen_despacho, FTP_BINARY) ) {

				// Se verifica, por las dudas, la existencia del archivo temporal del despacho
				if (is_file($ruta_origen_despacho)) {

					// Si se elimina el archivo, ya habiéndose utilizado
					if (unlink($ruta_origen_despacho)) {
						$_SESSION['mensaje'] = "Se ha confirmado la publicaci&oacute;n del despacho.";
						$_SESSION['tipo_mensaje'] = 1;
					}
				}
			} else {
				$_SESSION['mensaje'] = "La publicaci&oacute;n del despacho ha fallado!";
				$_SESSION['tipo_mensaje'] = 2;
			}

			// Se cierra la conexión FTP
			ftp_close($id_conexion_ftp);
		}

		// Se direcciona a la grilla
		header('Location: '.URL_ABMS.'?controlador=ordenes_sesion&accion=listar');
		exit;
	}

	// Gestión de los despachos de un item de la orden del día de sesión
	// -----------------------------------------------------------------

	/**
	 * Se obtienen los Documentos del Exped. Electrónico
	 * para utilizarlos en la edición del item de la orden del día de sesión
	 * @return html
	 */
	public function obtenerDocumentosExpedElec()
	{
		$documentos_elec = $this->modelo->obtenerDocumentosExpedElec(
			LibreriaGeneral::recoge('anio'),
			LibreriaGeneral::recoge('tipo'),
			LibreriaGeneral::recoge('numero')
		);
		//LibreriaGeneral::registrarLog("documentos_elec", $documentos_elec);

		$this->vista_documentos_elec->mostrar($documentos_elec);
	}

	/**
	 * Se asigna un Despacho a un item
	 * @return html
	 */
	public function asignarDespacho()
	{
		$id_item = LibreriaGeneral::recoge('id_item');
		$orden_actuacion = LibreriaGeneral::recoge('orden_actuacion');
		$detalle = LibreriaGeneral::recoge('detalle');

		// Si el item no tiene asignado el despacho con dicho Orden
		if ( ! $this->modelo->existeDespachoItem($id_item, $orden_actuacion) )
			$this->modelo->asignarDespacho($id_item, $orden_actuacion, $detalle);

		$despachos = $this->modelo->obtenerDespachosItem($id_item);

		$this->vista_despachos_item->mostrar($despachos);
	}

	/**
	 * Se actualiza el texto del Detalle del Despacho
	 * @return html
	 */
	public function actualizarDetalleDespacho()
	{
		$id_item = LibreriaGeneral::recoge('id_item');
		$orden_actuacion = LibreriaGeneral::recoge('orden_actuacion');
		$detalle = LibreriaGeneral::recoge('detalle');

		if ( $this->modelo->actualizarDetalleDespacho($id_item, $orden_actuacion, $detalle) )
		{
			$despachos = $this->modelo->obtenerDespachosItem($id_item);

			$this->vista_despachos_item->mostrar($despachos);
		}
	}

	/**
	 * Se elimina un Despacho del item
	 * @return html
	 */
	public function eliminarDespacho()
	{
		$id_item = LibreriaGeneral::recoge('id_item');
		$orden_actuacion = LibreriaGeneral::recoge('orden_actuacion');

		if ( $this->modelo->eliminarDespacho($id_item, $orden_actuacion) )
		{
			$despachos = $this->modelo->obtenerDespachosItem($id_item);

			$this->vista_despachos_item->mostrar($despachos);
		}
	}

}
?>

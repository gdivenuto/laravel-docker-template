<?php
if (!isset($_SESSION)) {
	session_start();
}

//Incluye el modelo que corresponde
require_once RUTA_MODELOS . "participaciones_hcd.php";
require_once RUTA_MODELOS . "participaciones_dmz.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "participaciones/grilla_expe_en_participacion.php";
require_once RUTA_VISTAS . "participaciones/grilla_participaciones.php";
require_once RUTA_VISTAS . "participaciones/pdf_incorporadas.php";
require_once RUTA_VISTAS . "participaciones/pdf_ficha.php";

class participaciones_controller extends ControllerBase {
	private $modelo_participaciones_hcd;
	private $modelo_participaciones_dmz;

	private $perfiles_permitidos_para_listar_participaciones;

	public function __construct() {

		parent::__construct();

		$this->campo_orden_por_defecto = 'fecha_inicio';

		// Se crea una instancia del modelo
		$this->modelo_participaciones_hcd = new participacionesHcdModel();
		$this->modelo_participaciones_dmz = new participacionesDmzModel();

		// Se crea una instancia de la Vista
		$this->vista_grilla_expe_en_participacion = new VistaExpeEnParticipacionGrilla();
		$this->vista_grilla = new VistaParticipacionesGrilla();
		$this->vista_pdf = new VistaParticipacionesPDF();
		$this->vista_pdf_ficha = new VistaParticipacionesFichaPDF();

		$this->perfiles_permitidos_para_listar_participaciones = array(1, 2);
	}

	public function listar($mensaje = '', $tipo_mensaje = '', $p_pagina = '') {

		// Primero se comprueba si el perfil actual posee permiso para ejecutar la acción respectiva
		//$this->comprobarAcceso($this->perfiles_permitidos_para_listar, $_SESSION['perfil1']);

		$filtro = Array();

		// Se obtiene el valor de la pagina
		$filtro['pagina'] = ($p_pagina == '') ? LibreriaGeneral::recoge('pagina', 1) : $p_pagina;

		// se establece el valor a buscar en el modelo
		//$filtro['valor_buscado'] = LibreriaGeneral::recoge('valor_buscado');

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

		// Se establece el filtro en el modelo
		$this->modelo_participaciones_hcd->setFiltro($filtro);

		// Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
		$filtro['cantidad'] = $this->modelo_participaciones_hcd->obtenerCantidad();

		// NUMERO TOTAL DE PAGINAS
		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		//Se establece el filtro en el modelo
		$this->modelo_participaciones_hcd->setFiltro($filtro);

		$listado = $this->modelo_participaciones_hcd->listar();

		$this->vista_grilla_expe_en_participacion->mostrar($listado, $mensaje, $tipo_mensaje, $filtro);
	}

	/**
	 * Se obtienen las participaciones de un expediente determinado
	 */
	public function listarParticipaciones($mensaje = '', $tipo_mensaje = '') {

		// Primero se comprueba si el perfil actual posee permiso para ejecutar la acción respectiva
		//$this->comprobarAcceso($this->perfiles_permitidos_para_listar_participaciones, $_SESSION['perfil1']);

		// Se recibe la clave del expediente
		$anio = LibreriaGeneral::recoge('anio');
		$tipo = LibreriaGeneral::recoge('tipo');
		$numero = LibreriaGeneral::recoge('numero');
		$cuerpo = LibreriaGeneral::recoge('cuerpo');
		$alcance = LibreriaGeneral::recoge('alcance');

		$fecha_desde_a_verificar = LibreriaGeneral::recoge('f_fecha_desde');
		$fecha_hasta_a_verificar = LibreriaGeneral::recoge('f_fecha_hasta');

		if (isset($fecha_desde_a_verificar) && $this->esFechaValida($fecha_desde_a_verificar)) {
			$f_fecha_desde = $this->modelo_participaciones_dmz->formatearFechaMySQL($fecha_desde_a_verificar);
		} else {
			$f_fecha_desde = '';
		}

		if (isset($fecha_hasta_a_verificar) && $this->esFechaValida($fecha_hasta_a_verificar)) {
			$f_fecha_hasta = $this->modelo_participaciones_dmz->formatearFechaMySQL($fecha_hasta_a_verificar);
		} else {
			$f_fecha_hasta = '';
		}

		// Se obtienen las Participaciones
		$listado = $this->modelo_participaciones_dmz->listarParticipaciones(
			$anio, $tipo, $numero, $cuerpo, $alcance, $f_fecha_desde, $f_fecha_hasta);

		$this->vista_grilla->mostrar($listado, $anio, $tipo, $numero, $cuerpo, $alcance, $mensaje, $tipo_mensaje, $f_fecha_desde, $f_fecha_hasta);
	}

	/**
	 * Se aprueba una Participacion determinada
	 */
	public function modificarEstadoAprobacion() {

		// Se recibe una Participación para
		$anio = LibreriaGeneral::recoge('anio');
		$tipo = LibreriaGeneral::recoge('tipo');
		$numero = LibreriaGeneral::recoge('numero');
		$cuerpo = LibreriaGeneral::recoge('cuerpo');
		$alcance = LibreriaGeneral::recoge('alcance');
		$numero_participacion = LibreriaGeneral::recoge('numero_participacion');
		$estado = LibreriaGeneral::recoge('estado');

		$fecha_desde_a_verificar = LibreriaGeneral::recoge('f_fecha_desde');
		$fecha_hasta_a_verificar = LibreriaGeneral::recoge('f_fecha_hasta');

		if (isset($fecha_desde_a_verificar) && $this->esFechaValida($fecha_desde_a_verificar)) {
			$filtro['f_fecha_desde'] = $this->modelo_participaciones_dmz->formatearFechaMySQL($fecha_desde_a_verificar);
		}

		if (isset($fecha_hasta_a_verificar) && $this->esFechaValida($fecha_hasta_a_verificar)) {
			$filtro['f_fecha_hasta'] = $this->modelo_participaciones_dmz->formatearFechaMySQL($fecha_hasta_a_verificar);
		}

		// Se modifica el estado de Aprobación en "hcd"
		if ($this->modelo_participaciones_hcd->modificarEstadoAprobacion($anio, $tipo, $numero, $cuerpo, $alcance, $numero_participacion, $estado)) {

			$mensaje = "El estado de incorporaci&oacute;n  de la Participaci&oacute;n se modific&oacute; con &eacute;xito.";
			$tipo_mensaje = 1;
		} else {
			$mensaje = "Error al intentar modificar el estado de incorporaci&oacute;n de la Participaci&oacute;n.";
			$tipo_mensaje = 2;
		}

		// Se obtienen las Participaciones
		$listado = $this->modelo_participaciones_dmz->listarParticipaciones($anio, $tipo, $numero, $cuerpo, $alcance, $filtro);

		$this->vista_grilla->mostrar($listado, $anio, $tipo, $numero, $cuerpo, $alcance, $mensaje, $tipo_mensaje, $filtro);
	}

	public function retirarExpeParticipaciones() {

		// Se recibe la clave del expediente
		$anio = LibreriaGeneral::recoge('anio');
		$tipo = LibreriaGeneral::recoge('tipo');
		$numero = LibreriaGeneral::recoge('numero');
		$cuerpo = LibreriaGeneral::recoge('cuerpo');
		$alcance = LibreriaGeneral::recoge('alcance');

		// Se retira el Expediente de las Participaciones
		if ($this->modelo_participaciones_hcd->retirarExpeParticipaciones($anio, $tipo, $numero, $cuerpo, $alcance)) {

			$mensaje = "El Expediente se ha retirado de las Participaciones con &eacute;xito.";
			$tipo_mensaje = 1;
		} else {
			$mensaje = "Error al intentar retirar el expediente de las Participaciones.";
			$tipo_mensaje = 2;
		}

		$listado = $this->modelo_participaciones_hcd->listar();

		$this->vista_grilla_expe_en_participacion->mostrar($listado, $mensaje, $tipo_mensaje);
	}

	public function eliminarParticipacion() {

		// Se recibe la clave del expediente
		$anio = LibreriaGeneral::recoge('anio');
		$tipo = LibreriaGeneral::recoge('tipo');
		$numero = LibreriaGeneral::recoge('numero');
		$cuerpo = LibreriaGeneral::recoge('cuerpo');
		$alcance = LibreriaGeneral::recoge('alcance');
		$numero_participacion = LibreriaGeneral::recoge('numero_participacion');

		// Primero se elimina la Participación de un Expediente en la DB dmz
		if ($this->modelo_participaciones_dmz->eliminarParticipacion($anio, $tipo, $numero, $cuerpo, $alcance, $numero_participacion)) {

			// Luego se elimina la Participación de un Expediente en la DB hcd
			if ($this->modelo_participaciones_hcd->eliminarParticipacion($anio, $tipo, $numero, $cuerpo, $alcance, $numero_participacion)) {

				$mensaje = "La participaci&oacute;n se ha eliminado con &eacute;xito.";
				$tipo_mensaje = 1;
			} else {
				$mensaje = "Error al intentar eliminar la Participaci&oacute;n.";
				$tipo_mensaje = 2;
			}
		} else {
			$mensaje = "Error al intentar eliminar la Participaci&oacute;n.";
			$tipo_mensaje = 2;
		}

		// Se obtienen las Participaciones
		$listado = $this->modelo_participaciones_dmz->listarParticipaciones($anio, $tipo, $numero, $cuerpo, $alcance);

		$this->vista_grilla->mostrar($listado, $anio, $tipo, $numero, $cuerpo, $alcance, $mensaje, $tipo_mensaje);
	}

	public function generarInforme() {

		// Se recibe la clave del expediente
		$anio = LibreriaGeneral::recoge('anio');
		$tipo = LibreriaGeneral::recoge('tipo');
		$numero = LibreriaGeneral::recoge('numero');
		$cuerpo = LibreriaGeneral::recoge('cuerpo');
		$alcance = LibreriaGeneral::recoge('alcance');
		$estado = LibreriaGeneral::recoge('estado');

		// Se obtienen las Participaciones
		$listado = $this->modelo_participaciones_hcd->listarParticipaciones($anio, $tipo, $numero, $cuerpo, $alcance, $estado);
		//LibreriaGeneral::registrarLog("listado", $listado);

		$this->vista_pdf->mostrar($listado);
	}

	public function generarFicha() {

		// Se recibe la clave del expediente
		$anio = LibreriaGeneral::recoge('anio');
		$tipo = LibreriaGeneral::recoge('tipo');
		$numero = LibreriaGeneral::recoge('numero');
		$cuerpo = LibreriaGeneral::recoge('cuerpo');
		$alcance = LibreriaGeneral::recoge('alcance');
		$numero_participacion = LibreriaGeneral::recoge('numero_participacion');

		// Se obtienen las Participaciones
		$registro = $this->modelo_participaciones_hcd->obtenerParticipacion($anio, $tipo, $numero, $cuerpo, $alcance, $numero_participacion);
		//LibreriaGeneral::registrarLog("registro", $registro);

		$this->vista_pdf_ficha->mostrar($registro);
	}
}
?>

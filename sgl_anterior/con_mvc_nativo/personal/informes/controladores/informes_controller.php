<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//Incluye el modelo que corresponde
require 'modelos/informes.php';
require '../abms/modelos/personal.php';

//Incluye la vista que corresponde
require 'vistas/informes.php';

class informes_controller extends ControllerBase
{
    const RUTA_DIRECTORIO_TEMPORAL = "/var/www/sgl/personal/informes/temporal/";

    /* PRODUCCION */
	const RUTA_DIRECTORIO_DESTINO = "/web/institucional/personal/";
	const FTP_SERVER              = 'lobo1.concejomdp.gov.ar';
	const USUARIO                 = 'informaticasgl';
	const PASSWORD                = '12sgl34hcd';
    /* DESARROLLO *
	const RUTA_DIRECTORIO_DESTINO = "/var/www/demo_hcd_2020/institucional/personal/";
	const FTP_SERVER              = 'localhost';
	const USUARIO                 = 'expe';
	const PASSWORD                = '123456';
	/**/

	private $id_conexion;
	private $resultado_login;
	private $listadoConcejales                   = null;
	private $listadoAreas                        = null;
	private $listadoCargos                       = null;
	private $listadoBloques                      = null;
	private $listadoPersonalAdministrativoBloque = null;

    public function __construct()
    {
    	parent::__construct();

    	// Se crea una instancia del modelo de Informes
    	$this->modelo = new informesModel();

    	// Se crea una instancia de la Vista de Informes
    	$this->vista = new VistaInformes();
    }

    public function listar()
    {
		$filtro = Array();

		$filtro['i_enviado'] = Validador::validarParametro('i_enviado');

		// SE FILTRA HASTA UNA FECHA DETERMINADA
		if ( $this->esFechaValida(Validador::validarParametro('i_a_la_fecha')) )
			$filtro['i_a_la_fecha'] = $this->modelo->formatearFechaMySQL(Validador::validarParametro('i_a_la_fecha'));

		// SE FILTRA POR UNA FECHA DE BAJA DETERMINADA
		if ( $this->esFechaValida(Validador::validarParametro('i_por_fecha_de_baja')) )
			$filtro['i_por_fecha_de_baja'] = $this->modelo->formatearFechaMySQL(Validador::validarParametro('i_por_fecha_de_baja'));

		// SE FILTRA POR Fecha desde Y Fecha hasta
		if ( $this->esFechaValida(Validador::validarParametro('i_fecha_desde')) )
			$filtro['i_fecha_desde'] = $this->modelo->formatearFechaMySQL(Validador::validarParametro('i_fecha_desde'));

		if ( $this->esFechaValida(Validador::validarParametro('i_fecha_hasta')) )
			$filtro['i_fecha_hasta'] = $this->modelo->formatearFechaMySQL(Validador::validarParametro('i_fecha_hasta'));

		// SI ESTA HABILITADO LA FECHA HASTA O EL RANGO DE FECHAS
		$filtro['i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha'] = Validador::validarParametro('i_habilitar_fecha_hasta_o_fecha_baja_o_rango_de_fecha');

		$filtro['i_area'] = Validador::validarParametro('i_area');
		$filtro['i_cargo'] = Validador::validarParametro('i_cargo');
		$filtro['i_concejal'] = Validador::validarParametro('i_concejal');

		$filtro['i_orden'] = Validador::validarParametro('i_orden');	// ORDENA POR EL CAMPO ESPECIFICADO

		//$filtro['i_solo_activos'] = Validador::validarParametro('i_solo_activos');// SI SE MUESTRAN SOLO LOS ACTIVOS O NO

		$filtro['i_rango'] = 15;	//cantidad de registros a mostrar
		$filtro['i_pagina'] = Validador::validarParametro('i_pagina');	//se obtiene el valor de la pagina

		// si no se sabe el valor de la pagina
		if ( $filtro['i_pagina'] == '' ) {
			$filtro['i_inicio'] = 0;	//se inicia en el primer registro
			$filtro['i_pagina'] = 1;	//con la primer pagina
		} else
			// si se conoce se calcula el valor del registro inicial de dicha pagina
			$filtro['i_inicio'] = ($filtro['i_pagina'] * $filtro['i_rango']) - $filtro['i_rango'];

		$filtro['i_pagina_ant'] = $filtro['i_pagina'] - 1;	//para la pagina anterior
		$filtro['i_pagina_sgte'] = $filtro['i_pagina'] + 1;	//para la pagina posterior

		// Se le pide al modelo todas las Areas
		$this->listadoAreas = $this->modelo->obtenerAreas();

		// Se le pide al modelo todos las Cargos
		$this->listadoCargos = $this->modelo->obtenerCargos();

		// Se le pide al modelo todos los Concejales SEGUN LA FECHA ELEGIDA
		$this->listadoConcejales = $this->modelo->obtenerConcejalesConsultaGeneral($filtro);

		if ( $filtro['i_enviado'] == 'enviado' ) {
			// SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
			$_SESSION['filtro_informes'] = $filtro;

			//fputs(fopen("filtro_listar.txt", 'w'), print_r($filtro, true));

			$this->modelo->setFiltro($filtro);

			// Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
			$todos = $this->modelo->listar();

			$filtro['i_cantidad'] = count($todos);

			$filtro['i_nro_paginas'] = 1;
			if ( $filtro['i_cantidad'] > 5 )
				// NUMERO TOTAL DE PAGINAS (DE 5 expedientes CADA UNA)
				$filtro['i_nro_paginas'] = ceil($filtro['i_cantidad'] / $filtro['i_rango']);

			$this->modelo->setFiltro($filtro);

			$listado = $this->modelo->listar(1);
		}

		// se muestra el listado
		$this->vista->listar($listado, $this->listadoConcejales, $this->listadoAreas, $this->listadoCargos, $filtro);
    }

    public function crear_formato_impresion()
    {
		$filtro = Array();

		$filtro['i_enviado'] = Validador::validarParametro('i_enviado');

		// SE FILTRA HASTA UNA FECHA DETERMINADA
		if ( $this->esFechaValida(Validador::validarParametro('i_a_la_fecha')) )
			$filtro['i_a_la_fecha'] = $this->modelo->formatearFechaMySQL(Validador::validarParametro('i_a_la_fecha'));

		// SE FILTRA POR UNA FECHA DE BAJA DETERMINADA
		if ( $this->esFechaValida(Validador::validarParametro('i_por_fecha_de_baja')) )
			$filtro['i_por_fecha_de_baja'] = $this->modelo->formatearFechaMySQL(Validador::validarParametro('i_por_fecha_de_baja'));

		// SE FILTRA POR Fecha desde Y Fecha hasta
		if ( $this->esFechaValida(Validador::validarParametro('i_fecha_desde')) )
			$filtro['i_fecha_desde'] = $this->modelo->formatearFechaMySQL(Validador::validarParametro('i_fecha_desde'));

		if ( $this->esFechaValida(Validador::validarParametro('i_fecha_hasta')) )
			$filtro['i_fecha_hasta'] = $this->modelo->formatearFechaMySQL(Validador::validarParametro('i_fecha_hasta'));

		$filtro['i_area'] = Validador::validarParametro('i_area');
		$filtro['i_cargo'] = Validador::validarParametro('i_cargo');
		$filtro['i_concejal'] = Validador::validarParametro('i_concejal');

		//$filtro['i_solo_activos'] = Validador::validarParametro('i_solo_activos');	// SI SE MUESTRAN SOLO LOS ACTIVOS O NO

		$filtro['i_orden'] = Validador::validarParametro('i_orden');	// ORDENA POR EL CAMPO ESPECIFICADO

		$this->modelo->setFiltro($filtro);

		$listado = $this->modelo->listar();

		// SE MUESTRA EL LISTADO PARA IMPRESION
		$this->vista->crear_formato_impresion($listado, $filtro);
    }

	public function pedirNombreConcejalModal()
	{
	    $id_combo = Validador::validarParametro('id_combo');

	    $this->listadoConcejales = $this->modelo->obtenerConcejales();

	    $this->vista->pedirNombreConcejalModal($this->listadoConcejales, $id_combo);
	}

	public function pedirNombreAreaModal()
	{
	    $this->listadoAreas = $this->modelo->obtenerAreas();

	    $this->vista->pedirNombreAreaModal($this->listadoAreas);
	}

	public function pedirNombreBloqueModal()
	{
	    $bloques = $this->modelo->obtenerPorTipo('B');

	    $this->vista->pedirNombreAreaModal($bloques);
	}

	public function pedirNombreAreaParaLiquidacionesModal()
	{
	    $this->listadoAreas = $this->modelo->obtenerAreasParaLiquidaciones();

	    $this->vista->pedirNombreAreaParaLiquidacionesModal($this->listadoAreas);
	}

	public function pedirNombreCargoModal()
	{
		$this->listadoCargos = $this->modelo->obtenerCargos();

	    $this->vista->pedirNombreCargoModal($this->listadoCargos);
	}

	public function pedirNombreRetiraModal()
	{
	    $id_area = Validador::validarParametro('id_area');

		$tipo_area = substr($id_area, 0, 2);

		if ( $tipo_area == '02' )
			$listado = $this->modelo->obtenerPersonalAdministrativoBloque($id_area);
	    else
			$listado = $this->modelo->obtenerPersonalAdministrativoParaLiquidaciones($id_area);

	    $this->vista->pedirNombreRetiraModal($listado);
	}

	public function refrescarComboCargos()
	{
		$id_area = Validador::validarParametro('id_area');

		$tipo_area = $this->modelo->obtenerTipoArea($id_area);

		$this->listadoCargos = $this->modelo->obtenerCargos($tipo_area);

	    $this->vista->comboCargos($this->listadoCargos);
	}

	public function refrescarComboConcejales()
	{
		$id_bloque    = Validador::validarParametro('i_bloque');
		$cod_concejal = Validador::validarParametro('i_concejal');

		// 03/10/2019 XXXX
		$this->listadoConcejales = $this->modelo->obtenerConcejalesPorBloque_ConsultaGral($id_bloque);
		//$this->listadoConcejales = $this->modelo->obtenerConcejales($id_bloque);

	    $this->vista->comboConcejales($this->listadoConcejales, $cod_concejal);
	}

	public function refrescarComboRetira()
	{
		$id_area = Validador::validarParametro('id_area');
		$cod_concejal = Validador::validarParametro('i_concejal');
		$cod_retira = Validador::validarParametro('i_retira');

		$tipo_area = substr($id_area, 0, 2);

		// SI EL AREA ES DE PLANTA POLITICA
		if ( $tipo_area == '02' )
			$listado = $this->modelo->obtenerPersonalAdministrativoBloque($id_area, $cod_concejal);
		else // O DE PLANTA PERMANENTE
			$listado = $this->modelo->obtenerPersonalAdministrativoParaLiquidaciones($id_area);

		// Se carga el combo de Personal dependiente que pueda retirar el listado de liquidaciones
	    $this->vista->comboRetira($listado, $cod_retira, $id_area);
	}

	/**
	 * Se obtienen los Concejales ó los Agentes en un Área, Mes y Año determinados
	 */
	public function refrescarIntegrantesAreaParaLiquidaciones()
	{
		$i_mes = Validador::validarParametro('i_mes');
		$i_anio = Validador::validarParametro('i_anio');
		$id_area = Validador::validarParametro('id_area');

		$tipo_area = substr($id_area, 0, 2);

		// SI EL AREA ES DE PLANTA POLITICA
		if ( $tipo_area == '02' )
			$listado = $this->modelo->obtenerConcejales($id_area, $i_mes, $i_anio);
		else // O DE PLANTA PERMANENTE
			$listado = $this->modelo->obtenerPersonalAdministrativoParaLiquidaciones($id_area);

	    $this->vista->mostrarPersonalParaLiquidaciones($listado, $id_area, $i_mes, $i_anio);
	}

	/**
	 * Se refresca el listado de Concejales de un Bloque, Mes y Año determinados
	 */
	public function refrescarConcejalesParaCertificado()
	{
		$i_mes = Validador::validarParametro('i_mes');
		$i_anio = Validador::validarParametro('i_anio');
		$id_bloque = Validador::validarParametro('i_bloque');

		$this->listadoConcejales = $this->modelo->obtenerConcejales($id_bloque, $i_mes, $i_anio);

	    $this->vista->listadoConcejalesParaElegir($this->listadoConcejales);
	}

	/**
	 * Se refresca el listado de Concejales de un Bloque determinado en una fecha específica
	 */
	public function refrescarConcejalesParaUnaFecha()
	{
		$i_a_la_fecha = Validador::validarParametro('i_a_la_fecha');
		$id_bloque    = Validador::validarParametro('i_bloque');

		$this->listadoConcejales = $this->modelo->obtenerConcejalesParaUnaFecha($id_bloque, $i_a_la_fecha);

		$this->vista->listadoConcejalesParaElegir($this->listadoConcejales);
	}

    public function listarPersonalBloques()
    {
		$filtro = Array();

		$filtro['i_enviado'] = Validador::validarParametro('i_enviado');

		//SE FILTRA HASTA UNA FECHA DETERMINADA
		if ( $this->esFechaValida(Validador::validarParametro('i_a_la_fecha')) )
			$filtro['i_a_la_fecha'] = $this->modelo->formatearFechaMySQL(Validador::validarParametro('i_a_la_fecha'));

		$filtro['i_solo_activos'] = Validador::validarParametro('i_solo_activos');	// SI SE MUESTRAN SOLO LOS ACTIVOS O NO

		// 16/02/2018
		// COMENTADO POR EL MOMENTO,
		// SE PENSABA QUE SE MOSTRABAN LOS CARGOS EN EL SITIO WEB
		// POR ELLO SE AGREGÓ LA OPCIÓN DE MOSTRARLOS O NO
		// UTILIZANDO UN CHECKBOX, SÓLO PARA PLANTA POLÍTICA
		// Para mostrar o no los Cargos
		//$filtro['i_ver_cargos'] = Validador::validarParametro('i_ver_cargos');

		$filtro['i_orden'] = Validador::validarParametro('i_orden');	// ORDENA POR EL CAMPO ESPECIFICADO

		$filtro['i_rango'] = 15;	//cantidad de registros a mostrar
		$filtro['i_pagina'] = Validador::validarParametro('i_pagina');	//se obtiene el valor de la pagina

		if ( $filtro['i_pagina'] == '' ) {
			//si no se sabe el valor de la pagina
			$filtro['i_inicio'] = 0;	//se inicia en el primer registro
			$filtro['i_pagina'] = 1;	//con la primer pagina
		} else
			//si se conoce se calcula el valor del registro inicial de dicha pagina
			$filtro['i_inicio'] = ($filtro['i_pagina'] * $filtro['i_rango']) - $filtro['i_rango'];

		$filtro['i_pagina_ant'] = $filtro['i_pagina'] - 1;	//para la pagina anterior
		$filtro['i_pagina_sgte'] = $filtro['i_pagina'] + 1;	//para la pagina posterior

		if ( $filtro['i_enviado'] == 'enviado' ) {
			// SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
			$_SESSION['filtro_informes_personal_bloques_politicos'] = $filtro;

			$this->modelo->setFiltro($filtro);

			// Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
			$todos = $this->modelo->obtenerPersonal('02');
			$filtro['i_cantidad'] = count($todos);

			$filtro['i_nro_paginas'] = 1;
			if ( $filtro['i_cantidad'] > 5 )
				// NUMERO TOTAL DE PAGINAS (DE 5 expedientes CADA UNA)
				$filtro['i_nro_paginas'] = ceil($filtro['i_cantidad'] / $filtro['i_rango']);

			$this->modelo->setFiltro($filtro);

			$listado = $this->modelo->obtenerPersonal('02', 1);
		}

		$bloques = $this->modelo->obtenerBloques();

		//$this->vista->listarPersonalBloque_y_Planta('bloques', $listado, $filtro);
		// 2019-08-09 XXXX
		$this->vista->listarPersonalBloques($listado, $filtro);
	}

    public function crearFormatoHTMLPersonalBloquesPoliticos()
    {
		$filtro = Array();

		$filtro['i_enviado'] = Validador::validarParametro('i_enviado');

		//SE FILTRA POR Fecha
		if ( $this->esFechaValida(Validador::validarParametro('i_a_la_fecha')) )
			$filtro['i_a_la_fecha'] = $this->modelo->formatearFechaMySQL(Validador::validarParametro('i_a_la_fecha'));

		$filtro['i_solo_activos'] = Validador::validarParametro('i_solo_activos');	// SI SE MUESTRAN SOLO LOS ACTIVOS O NO

		// 16/02/2018
		// COMENTADO POR EL MOMENTO,
		// SE PENSABA QUE SE MOSTRABAN LOS CARGOS EN EL SITIO WEB
		// POR ELLO SE AGREGÓ LA OPCIÓN DE MOSTRARLOS O NO
		// UTILIZANDO UN CHECKBOX, SÓLO PARA PLANTA POLÍTICA
		// Para mostrar o no los Cargos
		//$filtro['i_ver_cargos'] = Validador::validarParametro('i_ver_cargos');

		$filtro['i_orden'] = Validador::validarParametro('i_orden');	// ORDENA POR EL CAMPO ESPECIFICADO

		$this->modelo->setFiltro($filtro);

		$bloques = $this->modelo->obtenerPorTipo('B', 0, 1);

		$concejales_sin_bloque = $this->modelo->obtenerConcejalesSinBloque();

		// SE MUESTRA EL LISTADO DE PERSONAL DE BLOQUES PARA IMPRESION
		$this->vista->crearFormatoHTMLPersonalBloquesPoliticos($bloques, $concejales_sin_bloque, $filtro);
    }

    /**
     * NUEVO 17/12/2020 XXXX
     * Se listan sólo los Activos a una fecha determinada, con su Cargo respectivo
     * @return [array] $listado
     */
    public function listarParaSitioMGP()
    {
    	$filtro = Array();

		$filtro['i_enviado'] = Validador::validarParametro('i_enviado');

		//SE FILTRA POR Fecha
		if ( $this->esFechaValida(Validador::validarParametro('i_a_la_fecha')) )
			$filtro['i_a_la_fecha'] = $this->modelo->formatearFechaMySQL(Validador::validarParametro('i_a_la_fecha'));

		$filtro['i_rango'] = 15;	//cantidad de registros a mostrar
		$filtro['i_pagina'] = Validador::validarParametro('i_pagina');	//se obtiene el valor de la pagina

		// Si no se sabe el valor de la pagina
		if ( $filtro['i_pagina'] == '' ) {
			$filtro['i_inicio'] = 0;	//se inicia en el primer registro
			$filtro['i_pagina'] = 1;	//con la primer pagina
		} else {
			// si se conoce se calcula el valor del registro inicial de dicha pagina
			$filtro['i_inicio'] = ($filtro['i_pagina'] * $filtro['i_rango']) - $filtro['i_rango'];
		}

		$filtro['i_pagina_ant'] = $filtro['i_pagina'] - 1;	//para la pagina anterior
		$filtro['i_pagina_sgte'] = $filtro['i_pagina'] + 1;	//para la pagina posterior

		if ( $filtro['i_enviado'] == 'enviado' ) {
			// SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
			$_SESSION['filtro_informes_para_sitio_mgp'] = $filtro;

			$this->modelo->setFiltro($filtro);

			$listado = $this->modelo->listarParaSitioMGP();

			// Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
			$filtro['i_cantidad'] = count($listado);

			$filtro['i_nro_paginas'] = 1;
			if ( $filtro['i_cantidad'] > 5 )
				// NUMERO TOTAL DE PAGINAS (DE 5 expedientes CADA UNA)
				$filtro['i_nro_paginas'] = ceil($filtro['i_cantidad'] / $filtro['i_rango']);

			$this->modelo->setFiltro($filtro);

			$listado = $this->modelo->listarParaSitioMGP();
		} else {
			$listado = null;
		}

		//fputs(fopen("listado_listarParaSitioMGP.txt", 'w'), print_r($listado, true));

		$this->vista->listarParaSitioMGP($listado, $filtro);
    }

	/**
	 * NUEVO 17/12/2020 XXXX
	 * Se crea el Formato de Impresión, de sólo los Activos a una fecha determinada, con su Cargo respectivo
	 */
	public function crearFormatoImpresionParaSitioMGP()
    {
		$filtro = Array();

		// Se recibe la Fecha
		if ( $this->esFechaValida(Validador::validarParametro('i_a_la_fecha')) )
			$filtro['i_a_la_fecha'] = $this->modelo->formatearFechaMySQL(Validador::validarParametro('i_a_la_fecha'));

		$this->modelo->setFiltro($filtro);

		// Se obtienen sólo los Activos a una fecha determinada, con su Cargo respectivo
		$listado = $this->modelo->listarParaSitioMGP();

		//fputs(fopen("listado_crearFormatoImpresionParaSitioMGP.txt", 'w'), print_r($listado, true));

		$this->vista->crearFormatoImpresionParaSitioMGP($listado);
	}

    public function listarPersonalPlantaPermanente()
    {
		$filtro = Array();

		$filtro['i_enviado'] = Validador::validarParametro('i_enviado');

		//SE FILTRA POR Fecha
		if ( $this->esFechaValida(Validador::validarParametro('i_a_la_fecha')) )
			$filtro['i_a_la_fecha'] = $this->modelo->formatearFechaMySQL(Validador::validarParametro('i_a_la_fecha'));

		$filtro['i_solo_activos'] = Validador::validarParametro('i_solo_activos');	// SI SE MUESTRAN SOLO LOS ACTIVOS O NO

		$filtro['i_perteneciente_a'] = Validador::validarParametro('i_perteneciente_a');	// SI PERTENECE AL HCD O A DEFENSORIA DEL PUEBLO

		$filtro['i_rango'] = 15;	//cantidad de registros a mostrar
		$filtro['i_pagina'] = Validador::validarParametro('i_pagina');	//se obtiene el valor de la pagina

		// Si no se sabe el valor de la pagina
		if ( $filtro['i_pagina'] == '' ) {
			$filtro['i_inicio'] = 0;	//se inicia en el primer registro
			$filtro['i_pagina'] = 1;	//con la primer pagina
		} else {
			// si se conoce se calcula el valor del registro inicial de dicha pagina
			$filtro['i_inicio'] = ($filtro['i_pagina'] * $filtro['i_rango']) - $filtro['i_rango'];
		}

		$filtro['i_pagina_ant'] = $filtro['i_pagina'] - 1;	//para la pagina anterior
		$filtro['i_pagina_sgte'] = $filtro['i_pagina'] + 1;	//para la pagina posterior

		if ( $filtro['i_enviado'] == 'enviado' ) {
			// SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
			$_SESSION['filtro_informes_personal_planta_permanente'] = $filtro;

			$this->modelo->setFiltro($filtro);

			if ( $filtro['i_perteneciente_a'] == '1' )
				$todos = $this->modelo->obtenerPersonal('01');
			else
				$todos = $this->modelo->obtenerPersonalDefensoria();

			//Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
			$filtro['i_cantidad'] = count($todos);

			$filtro['i_nro_paginas'] = 1;
			if ( $filtro['i_cantidad'] > 5 )
				// NUMERO TOTAL DE PAGINAS (DE 5 expedientes CADA UNA)
				$filtro['i_nro_paginas'] = ceil($filtro['i_cantidad'] / $filtro['i_rango']);

			$this->modelo->setFiltro($filtro);

			if ( $filtro['i_perteneciente_a'] == '1' )
				$listado = $this->modelo->obtenerPersonal('01', 1);
			else
				$listado = $this->modelo->obtenerPersonalDefensoria(1);
		}

		//$this->vista->listarPersonalBloque_y_Planta('planta_permanente', $listado, $filtro);
		// 2019-08-09 XXXX
		$this->vista->listarPersonalPlantaPermanente($listado, $filtro);
	}

    public function crearFormatoHTMLPersonalPlantaPermanente()
    {
		$filtro = Array();

		$filtro['i_enviado'] = Validador::validarParametro('i_enviado');

		//SE FILTRA POR Fecha
		if ( $this->esFechaValida(Validador::validarParametro('i_a_la_fecha')) )
			$filtro['i_a_la_fecha'] = $this->modelo->formatearFechaMySQL(Validador::validarParametro('i_a_la_fecha'));

		$filtro['i_solo_activos'] = Validador::validarParametro('i_solo_activos');	// SI SE MUESTRAN SOLO LOS ACTIVOS O NO

		$filtro['i_orden'] = Validador::validarParametro('i_orden');	// ORDENA POR EL CAMPO ESPECIFICADO

		$filtro['i_perteneciente_a'] = Validador::validarParametro('i_perteneciente_a');	// SI PERTENECE AL HCD O A DEFENSORIA DEL PUEBLO

		$this->modelo->setFiltro($filtro);

		if ( $filtro['i_perteneciente_a'] == '1' )
			$areas_planta = $this->modelo->obtenerPorTipo('P', 0, 1);
		else
			$areas_planta = $this->modelo->obtenerPorTipo('P', 1, 1);

		// SE MUESTRA EL LISTADO DE PERSONAL DE PLANTA PERMANENTE PARA IMPRESION
		$this->vista->crearFormatoHTMLPersonalPlantaPermanente($areas_planta, $filtro);
    }

    public function listarParaLiquidaciones()
    {
		// Sólo los Bloques y las Direcciones
		$this->listado_bloques_y_direcciones = $this->modelo->obtenerAreasParaLiquidaciones();

		$this->vista->listarParaLiquidaciones($this->listado_bloques_y_direcciones);
	}

	public function crearFormatoImpresionListadoParaLiquidaciones()
    {
		$filtro = Array();

		$filtro['i_enviado'] = Validador::validarParametro('i_enviado');

		$filtro['i_mes'] = Validador::validarParametro('i_mes');
		$filtro['i_anio'] = Validador::validarParametro('i_anio');

		// HABER, AGUINALDO O ADICIONAL
		$filtro['i_opcion_haberes_aguinaldo_adicional'] = Validador::validarParametro('i_opcion_haberes_aguinaldo_adicional');

		// BLOQUE POLITICO O AREA ADMINISTRATIVA DE PLANTA PERMANENTE
		$filtro['i_area'] = Validador::validarParametro('i_area');

		// Concejales elegidos
		$filtro['i_concejales'] = $_REQUEST["i_concejales"];
		// Se ordenan los Concejales por su Legajo
		sort($filtro['i_concejales']);
		// Se convierte el array de Concejales en una cadena de legajos separados por una coma
		$legajos_concejales = implode(',', $filtro['i_concejales']);

		// Se obtienen TODOS los Activos en el mes y año respectivos
		$listado_activos_en_el_mes = $this->modelo->listarParaLiquidaciones($filtro['i_mes'], $filtro['i_anio'], $filtro['i_area'], $legajos_concejales);

		// QUIEN RETIRA EL LISTADO
		$filtro['i_retira'] = Validador::validarParametro('i_retira');

		// FORMATO DE PAGINA A4 O LEGAL (OFICIO)
		$filtro['i_opcion_formato_pagina'] = Validador::validarParametro('i_opcion_formato_pagina');

		if ( $filtro['i_area'] )
			// NOMBRE DEL BLOQUE
			$filtro['nombre_area'] = $this->modelo->obtenerNombreBloque($filtro['i_area']);

		if ( $filtro['i_retira'] )
			// NOMBRE DEL QUE RETIRA EL LISTADO DE LIQUIDACION
			$filtro['datos_retira'] = $this->modelo->obtenerInformacionLegajo($filtro['i_retira']);

		// SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
		$_SESSION['filtro_listado_para_liquidaciones'] = $filtro;

		$this->vista->crearFormatoImpresionListadoParaLiquidaciones($filtro, $listado_activos_en_el_mes);
	}

    public function listarParaCertificado()
    {
		//Se le pide al modelo todos los Bloques
		$bloques = $this->modelo->obtenerBloques();

		$this->vista->listarParaCertificado($bloques);
	}

	public function crearFormatoImpresionListadoParaCertificado()
    {
		$filtro = Array();

		$filtro['i_enviado'] 			   = Validador::validarParametro('i_enviado');
		$filtro['i_mes'] 				   = Validador::validarParametro('i_mes');
		$filtro['i_anio'] 				   = Validador::validarParametro('i_anio');
		$filtro['i_bloque'] 			   = Validador::validarParametro('i_bloque');
		$filtro['i_opcion_formato_pagina'] = Validador::validarParametro('i_opcion_formato_pagina');// FORMATO DE PAGINA A4 O LEGAL (OFICIO)

		// Concejales elegidos
		$filtro['i_concejales'] = $_REQUEST["i_concejales"];
		// Se ordenan los Concejales por su Legajo
		sort($filtro['i_concejales']);

		// Se convierte el array de Concejales en una cadena de legajos separados por una coma
		$legajos_concejales = implode(',', $filtro['i_concejales']);

		// 09/11/2017 XXXX, XXXX
		// Se reemplazó "listarParaLiquidaciones" por "listarParaCertificado"

		// Se obtienen los legajos Activos en el mes, año y bloque respectivos
		//$listado_activos_en_el_mes = $this->modelo->listarParaLiquidaciones($filtro['i_mes'], $filtro['i_anio'], $filtro['i_bloque'], $legajos_concejales);
		$listado_activos_en_el_mes = $this->modelo->listarParaCertificado($filtro['i_mes'], $filtro['i_anio'], $filtro['i_bloque'], $legajos_concejales);

		// Nombre del Bloque
		$filtro['nombre_bloque'] = ( $filtro['i_bloque'] ) ? $this->modelo->obtenerNombreBloque($filtro['i_bloque']) : '';

		// Se guardan en sesión los parámetros de búsqueda para no perder una referencia anterior
		$_SESSION['filtro_listado_para_certificados'] = $filtro;

		$this->vista->crearFormatoImpresionListadoParaCertificado($filtro, $listado_activos_en_el_mes);
	}

    public function listarPorConcejal()
    {
		//Se le pide al modelo todos los Bloques
		$bloques = $this->modelo->obtenerBloques();

		$this->vista->listarPorConcejal($bloques);
	}

	/**
	 * Se genera el listado del Personal dependiente de los Concejales, en una fecha específica
	 */
	public function crearFormatoImpresionListadoPorConcejal()
    {
		$filtro = Array();

		$filtro['i_enviado'] = Validador::validarParametro('i_enviado');

		// HASTA LA FECHA ESPECIFICADA
		if ( $this->esFechaValida(Validador::validarParametro('i_a_la_fecha')) )
			$filtro['i_a_la_fecha'] = $this->modelo->formatearFechaMySQL(Validador::validarParametro('i_a_la_fecha'));

		$filtro['i_bloque'] = Validador::validarParametro('i_bloque');

		$filtro['i_concejales'] = $_REQUEST["i_concejales"];
		// SE ORDENAN LOS CONCEJALES POR LEGAJO
		sort($filtro['i_concejales']);

		if ( $filtro['i_bloque'] )
			$filtro['nombre_bloque'] = $this->modelo->obtenerNombreBloque($filtro['i_bloque']);

		// FORMATO DE PAGINA A4 O LEGAL (OFICIO)
		$filtro['i_opcion_formato_pagina'] = Validador::validarParametro('i_opcion_formato_pagina');

		// SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
		$_SESSION['filtro_listado_por_concejal_interno'] = $filtro;

		$this->vista->crearFormatoImpresionListadoPorConcejal($filtro);
	}

	public function refrescarDependientesParaReasignacion()
	{
		$i_concejal = Validador::validarParametro('i_concejal');
		$i_listado = Validador::validarParametro('i_listado');

		$this->listadoPersonalAdministrativoBloque = $this->modelo->obtenerPersonalAdministrativoBloque(0, $i_concejal);

	    if ( $i_listado == 'origen' )
			$this->vista->listadoDependientesParaReasignacion($this->listadoPersonalAdministrativoBloque);
		else
			$this->vista->listadoDependientes($this->listadoPersonalAdministrativoBloque);
	}

	/**
	 * Se asignan los legajos dependientes de un Concejal a otro, siempre y cuando estén activos
	 */
	public function reasignarDependientes()
	{
		//Se le pide al modelo todos los Concejales activos
		$this->listadoConcejales = $this->modelo->obtenerConcejalesActivos();

		$this->vista->reasignarDependientes($this->listadoConcejales);
	}

	public function guardarReasignacionDependientes()
	{
		$datos = Array();

		$datos['i_enviado'] 		   = Validador::validarParametro('i_enviado');
		$datos['i_concejal_origen']    = Validador::validarParametro('i_concejal_origen');
		$datos['i_concejal_destino']   = Validador::validarParametro('i_concejal_destino');
		$datos['i_fecha_reasignacion'] = $this->modelo->formatearFechaMySQL(Validador::validarParametro('i_fecha_reasignacion'));
		$datos['i_dependientes'] 	   = $_REQUEST["i_dependientes"];

		// SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
		$_SESSION['filtro_reasignacion_dependientes'] = $datos;

		// PARA CADA DEPENDIENTE
		$cantidad_dependientes = count($datos['i_dependientes']);
		for ($d=0; $d < $cantidad_dependientes; $d++) {
			$legajo_dependiente = &$datos['i_dependientes'][$d];

			// SE OBTIENEN LOS DATOS DEL CARGO ACTUAL DEL DEPENDIENTE
			$datos['i_datos_cargo_actual'] = $this->modelo->obtenerCargoActual($legajo_dependiente);

			// *** 11/09/2017, XXXX
	    	// Se obtiene el Bloque del Concejal destino
			$id_bloque = $this->modelo->obtenerIdUltimaArea($datos['i_concejal_destino']);
			// En caso que los dependientes pasen a un Concejal de un BLOQUE DIFERENTE
			// se les asigna dicho bloque al asesor antes de su reasignación
			$this->modelo->verificarPertenenciaBloque($legajo_dependiente, $datos['i_fecha_reasignacion'], $id_bloque);

			// Se reasigna al Concejal destino
			$this->modelo->guardarReasignacion($datos);
		}

		//Se le pide al modelo todos los Concejales
		$this->listadoConcejales = $this->modelo->obtenerConcejalesActivos();

		$this->vista->reasignarDependientes($this->listadoConcejales, "Se ha reasignado el personal satisfactoriamente.", 1);
	}

	public function confirmarPublicacion()
	{
		$nombre_archivo = Validador::validarParametro('nombre_archivo');

		// ARCHIVO EN /personal/informes/temporal/
		$archivo_en_temporal = self::RUTA_DIRECTORIO_TEMPORAL.$nombre_archivo.".html";

		// DIRECTORIO /institucional/personal/
		$directorio_destino = self::RUTA_DIRECTORIO_DESTINO;

		// SE ESTABLECE UNA CONEXION FTP
		$this->id_conexion = ftp_connect(self::FTP_SERVER);

		// SE ESTABLECE EL INICIO DE SESION FTP CON USUARIO Y PASSWORD
		$this->resultado_login = ftp_login($this->id_conexion, self::USUARIO, self::PASSWORD);

		// SE CHEQUEA LA CONEXION FTP
		if ( ( !$this->id_conexion ) || ( !$this->resultado_login ) ) {
			$mensaje = "Error al intentar conectarse o autentificarse en el Servidor FTP.";
			$tipo_mensaje = 2;
		} else {
			// SE CAMBIA AL DIRECTORIO DONDE SE QUIERE TRANSFERIR EL ARCHIVO, ( /var/www/sgl/personal/web_personal/ )
			ftp_chdir($this->id_conexion, $directorio_destino);

			$dir_actual = ftp_pwd($this->id_conexion);

			$ruta_archivo_remoto = $dir_actual."/".$nombre_archivo.".html";

			// SE CARGA EL ARCHIVO EN /web_personal
			if ( ftp_put($this->id_conexion, $ruta_archivo_remoto, $archivo_en_temporal, FTP_BINARY) ) {

				$mensaje = "Se ha confirmado la publicaci&oacute;n del listado de ".$nombre_archivo.".";
				$tipo_mensaje = 1;

				// 13/02/2026 XXXX
				// Se retira de mantenimiento, la sección de Planta Política del sitio web
				if ($nombre_archivo === 'ppolitica') {
					if ( ! $this->modelo->activarSeccionWebPlantaPolitica()) {
						$mensaje = "No se ha podido retirar de mantenimiento la secci&oacute;n de Planta Pol&iacute;tica en el sitio web";
						$tipo_mensaje = 2;
					}
				}
			} else {
				$mensaje = "&iexcl;La publicaci&oacute;n del listado de ".$nombre_archivo." ha fallado!";
				$tipo_mensaje = 2;
			}

			//SE CIERRA LA SECUENCIA FTP
			ftp_close($this->id_conexion);
		}

		$_SESSION['mensaje'] = $mensaje;
		$_SESSION['tipo_mensaje'] = $tipo_mensaje;
		$_SESSION['nombre_archivo'] = $nombre_archivo;

		// SE VUELVE AL LISTADO
		header ("Location: ../index.php");
	}
}
?>

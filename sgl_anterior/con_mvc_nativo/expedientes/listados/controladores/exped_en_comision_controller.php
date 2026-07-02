<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

// Se incluye el modelo que corresponde
require '../consultas/modelos/consulta_gral.php';
require 'modelos/exped_en_comision.php';
require '../abms/modelos/informes.php';

// Se incluye la vista que corresponde
require 'vistas/exped_en_comision.php';

class exped_en_comision_controller extends ControllerBase
{
    private $listadoIniciadores = null;
    private $listadoAutores = null;
    private $listadoComisiones = null;
    private $listadoCategorias = null;
    private $listadoTemas = null;
    private $listadoEstados = null;

	// VECTOR CON EL RANGO DE DIAS
	private $vector_rango_de_dias = Array();

    const RUTA_DIRECTORIO_PROYECTOS = "/var/www/sgl/expedientes/proyectos/";

	//SE LE DA EL FORMATO dia/mes/anio completo
	public function formatearFecha($fecha) {

	    if ($fecha) {
			if ($fecha != '0000-00-00') {
				$fec_partes = explode("-",$fecha);
				$fecha_a_ver = $fec_partes[2].'/'.$fec_partes[1].'/'.$fec_partes[0];
				return $fecha_a_ver;
			} else
				return '';
	    } else
			return '';
	}

    private function cargarCombos() {
		//Se crea una instancia del modelo
		$modelo = new expedEnComisionModel();

		//Se le pide al modelo todos los iniciadores
		$this->listadoIniciadores = $modelo->obtenerIniciadores();

		//Se le pide al modelo todos los autores
		$this->listadoAutores = $modelo->obtenerAutores();

		//Se le pide al modelo todas las comisiones
		$this->listadoComisiones = $modelo->obtenerComisiones();

		//Se le pide al modelo todos las categorias
		$this->listadoCategorias = $modelo->obtenerCategorias();

		//Se le pide al modelo todos los temas
		$this->listadoTemas = $modelo->obtenerTemas();

		//Se le pide al modelo todos los estados
		$this->listadoEstados = $modelo->obtenerEstados();
	}

	// NUEVO 01/03/2013
	public function mostrarModalComisionesActivas() {
		// Se crea una instancia del modelo
		$modelo = new expedEnComisionModel();

		// Se le pide al modelo todas las comisiones activas
		$listado_comisiones_activas = $modelo->obtenerComisiones(1);

		$vista = new VistaExpedEnComision();
		$vista->mostrarModalComisionesActivas($listado_comisiones_activas);
	}

    public function listar($mensaje = '') {
		// Se crea una instancia del modelo
		$modelo = new expedEnComisionModel();

		$filtro = Array();

		// PARA VISUALIZAR EL CRITERIO DE BUSQUEDA PARA EL LISTADO DETERMINADO
		$filtro['l_tipo_listado'] = Validador::validarParametro('l_tipo_listado');

		$filtro['l_enviado'] = Validador::validarParametro('l_enviado');

		// SE FILTRA POR Fecha desde Y Fecha hasta
		if ( $this->esFechaValida(Validador::validarParametro('l_fecha_desde')) )
		{
			$filtro['l_fecha_desde'] = $modelo->formatearFechaMySQL(Validador::validarParametro('l_fecha_desde'));
		}
		if ( $this->esFechaValida(Validador::validarParametro('l_fecha_hasta')) )
		{
			$filtro['l_fecha_hasta'] = $modelo->formatearFechaMySQL(Validador::validarParametro('l_fecha_hasta'));
		}

		// SE FILTRA POR Fecha de la Comision
		if ( $this->esFechaValida(Validador::validarParametro('l_fecha_comision')) )
		{
			$filtro['l_fecha_comision'] = $modelo->formatearFechaMySQL(Validador::validarParametro('l_fecha_comision'));
		}

		// 30/03/2012 FECHA DE LISTADO
		if ( Validador::validarParametro('l_fecha_de_listado') )
		{
			$filtro['l_fecha_de_listado'] = Validador::validarParametro('l_fecha_de_listado');
		}

		// SI SE MUESTRAN LOS VENCIDOS O TODOS
		$filtro['l_vencidos'] = Validador::validarParametro('l_vencidos');

		// SI SE MUESTRAN SOLO LOS Habilitados O NO
		$filtro['l_solo_habilitado'] = Validador::validarParametro('l_solo_habilitado');

		// SE FILTRA POR Comision
		$filtro['l_comision'] = Validador::validarParametro('l_comision');
		$comision = explode("-", Validador::validarParametro('l_comision'));
		$filtro['l_comision_tipo'] = $comision[0];
		$filtro['l_comision_codigo'] = $comision[1];

		// SE FILTRA POR Estado
		$filtro['l_estado'] = Validador::validarParametro('l_estado');

		// SE FILTRA POR LA Observacion del Estado
		$filtro['l_observacion_estado'] = Validador::validarParametro('l_observacion_estado');

		$filtro['l_rango'] = 10;	//cantidad de registros a mostrar
		$filtro['l_pagina'] = Validador::validarParametro('l_pagina');	//se obtiene el valor de la pagina
		$filtro['se_pagina'] = Validador::validarParametro('se_pagina'); // SI SE LLEGA PAGINANDO O NO

		// si no se sabe el valor de la pagina
		if ( $filtro['l_pagina'] == '' )
		{
			$filtro['l_inicio'] = 0;	//se inicia en el primer registro
			$filtro['l_pagina'] = 1;	//con la primer pagina
		}
		else
		{	//si se conoce se calcula el valor del registro inicial de dicha pagina
			$filtro['l_inicio'] = ($filtro['l_pagina'] * $filtro['l_rango']) - $filtro['l_rango'];
		}
		$filtro['l_pagina_ant'] = $filtro['l_pagina'] - 1;	//para la pagina anterior
		$filtro['l_pagina_sgte'] = $filtro['l_pagina'] + 1;	//para la pagina posterior

		// Se cargan los combos para la busqueda en la vista
		$this->cargarCombos();

		if ( $filtro['l_enviado'] == 'enviado' )
		{
			// 04/03/2013
			// SI SE LISTA EXPEDIENTES EN COMISION, Y NO SE FILTRA POR ESTADO NI POR COMISION DESDE EL COMBO
			if ( ( $filtro['l_tipo_listado'] == 'exped_en_comision' )  && ( $filtro['l_estado'] == '' OR $filtro['l_estado'] == '0' ) && ( $filtro['l_comision'] == '' OR $filtro['l_comision'] == '0' ) )
			{
				// SON LAS COMISIONES QUE SE DESEAN MOSTRAR EN EL RESULTADO DE LA BUSQUEDA
				if ( $filtro['se_pagina'] == 'si' )
				{
					$l_comisiones_modal_serializado = Validador::validarParametro('l_comisiones_modal');

					// SON LAS COMISIONES QUE SE DESEAN MOSTRAR EN EL RESULTADO DE LA BUSQUEDA
					$filtro['l_comisiones_modal'] = deserializarColeccion($l_comisiones_modal_serializado);
				}
				else
				{
					$filtro['l_comisiones_modal'] = $_POST['l_comisiones_modal'];
				}
			}
			else
			{
				$filtro['l_comisiones_modal'] = '';
			}

			// 04/01/2012: SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
			$_SESSION['filtro_LISTADOS'] = $filtro;

			$modelo->setFiltro($filtro);

			//21/05/2013
			if ( $filtro['l_tipo_listado'] == "exped_en_prestamo" )
			{
				$listado = $modelo->listar_exped_en_prestamo();
			}
			else
			{
				$listado = $modelo->listar();
			}

			// AGREGADO 02/08/2012: SI HAY EXPEDIENTES
			if ( $listado != '' )
			{
				// Y SI SE UTILIZA 'EXPEDIENTE EN COMISION'
				if ( $filtro['l_tipo_listado'] == 'exped_en_comision' )
				{
					// SE CALCULAN LOS DIAS SOLAMENTE SI SE FILTRA POR COMISION
					// 20/05/2013	Ó CON ESTADO 3, 16 ó 79
					if ( $filtro['l_estado'] == '3' || $filtro['l_estado'] == '16' || $filtro['l_estado'] == '79' || $filtro['l_estado'] == '' || $filtro['l_estado'] == '0' )
					{
						$cantidad = count($listado);
						// POR CADA REGISTRO SE OBTIENEN LOS DIAS DEL EXPEDIENTE EN COMISION
						for ($i=0; $i < $cantidad; $i++)
						{
							$registro = &$listado[$i];

							// SE OBTIENE EL ULTIMO GIRO
							$ultimo_giro = $modelo->obtenerComisionFicha($registro['anio'], $registro['tipo'], $registro['numero'], $registro['cuerpo'], $registro['alcance']);

							// 20/05/2013 SI POSEE GIRO SIN FECHA DE SALIDA
							if ( $ultimo_giro[0]['fecha_giro'] != '' )
							{
								// SE AGREGA A CADA REGISTRO LOS DIAS EN COMISION
								$listado[$i]['dias'] = $this->calcularDiasEnComision($this->formatearFecha($ultimo_giro[0]['fecha_giro']), $filtro['l_fecha_de_listado'], $registro['anio'], $registro['tipo'], $registro['numero'], $registro['cuerpo'], $registro['alcance'], $ultimo_giro[0]['orden_giro']);
							}
						}

						// 25/06/2012 SI SE DESEAN LISTAR SÓLO LOS VENCIDOS
						if ( $filtro['l_vencidos'] == 1 )
						{
							$pos = 0;
							for ($i=0; $i < $cantidad; $i++)
							{
								// SE FILTRAN LOS QUE SUPEREN 120 DIAS
								if ( $listado[$i]['dias'] > 120 )
								{
									$vencidos[$pos] = $listado[$i];
									$pos++;
								}
							}
							if ( $vencidos[0]['dias'] != '' )
							{
								$listado = $vencidos;
							}
							else
							{
								$listado = null;
							}
						}
					}
				}
			}

			// SE GUARDA LA CANTIDAD TOTAL DE EXPEDIENTES DEVUELTOS
			if ( $this->filtro['l_tipo_listado'] != "exped_en_comision" || $this->filtro['l_tipo_listado'] != "informes" )
			{
				$filtro['l_cantidad'] = $_SESSION['total'];
			}
			else
			{
				$filtro['l_cantidad'] = count($listado);
			}

			$filtro['l_nro_paginas'] = 1;
			if ( $filtro['l_cantidad'] > 5 )
			{
				// NUMERO TOTAL DE PAGINAS (DE 5 expedientes CADA UNA)
				$filtro['l_nro_paginas'] = ceil($filtro['l_cantidad'] / $filtro['l_rango']);
			}
		}

		$vista = new VistaExpedEnComision();

		// 11/04/2013	SE INDEPENDIZA CADA LISTADO EN UNA VISTA DIFERENTE
		switch ($filtro['l_tipo_listado'])
		{
			case 'exped_en_comision':
				$vista->listar_expedientes_en_comision($listado, $this->listadoIniciadores, $this->listadoAutores, $this->listadoComisiones, $this->listadoCategorias, $this->listadoTemas, $this->listadoEstados, $this->listadoGiros, $mensaje, $filtro);
				break;
			case 'orden_del_dia':
				$vista->listar_orden_del_dia($listado, $this->listadoIniciadores, $this->listadoAutores, $this->listadoComisiones, $this->listadoCategorias, $this->listadoTemas, $this->listadoEstados, $this->listadoGiros, $mensaje, $filtro);
				break;
			case 'detalle_giros':
				$vista->listar_detalle_giros($listado, $this->listadoIniciadores, $this->listadoAutores, $this->listadoComisiones, $this->listadoCategorias, $this->listadoTemas, $this->listadoEstados, $this->listadoGiros, $mensaje, $filtro);
				break;
			case 'asuntos_entrados':
				$vista->listar_asuntos_entrados($listado, $mensaje, $filtro);
				break;
			case 'expurgo':
				$vista->listar_expurgo($listado, $this->listadoEstados, $mensaje, $filtro);
				break;
			case 'exped_en_prestamo':
				$vista->listar_expedientes_en_prestamo($listado, $this->listadoEstados, $mensaje, $filtro);
				break;
		}
	}

    // SE OBTIENE EL LISTADO COMPLETO PARA GENERAR EL FORMATO DE IMPRESION Y EL DOCUMENTO DE TEXTO
    public function armar_listado_completo() {
		$modelo = new expedEnComisionModel();

		// SE ARMA EL FILTRO PARA LA VISTA
		$filtro = Array();

		// PARA GENERAR EL PDF SEGUN EL LISTADO DETERMINADO
		$filtro['l_tipo_listado'] = Validador::validarParametro('l_tipo_listado');

		// PARA UTILIZAR LA VISTA CORRESPONDIENTE, EL FORMATO DE IMPRESION O EL DOCUMENTO DE TEXTO
		$formato = Validador::validarParametro('formato');

		// SI SE FILTRA POR Comision
		$comision = explode("-", Validador::validarParametro('l_comision'));
		$filtro['l_comision_tipo'] = $comision[0];
		$filtro['l_comision_codigo'] = $comision[1];

		// SE FILTRA POR Estado
		$filtro['l_estado'] = Validador::validarParametro('l_estado');

		// SE FILTRA POR LA Observacion del Estado
		$filtro['l_observacion_estado'] = Validador::validarParametro('l_observacion_estado');

		//SE FILTRA POR Fecha desde Y Fecha hasta
		if ( $this->esFechaValida(Validador::validarParametro('l_fecha_desde')) )
		{
			$filtro['l_fecha_desde'] = $modelo->formatearFechaMySQL(Validador::validarParametro('l_fecha_desde'));
		}
		if ( $this->esFechaValida(Validador::validarParametro('l_fecha_hasta')) )
		{
			$filtro['l_fecha_hasta'] = $modelo->formatearFechaMySQL(Validador::validarParametro('l_fecha_hasta'));
		}

		// SE FILTRA POR Fecha de la Comision
		if ( $this->esFechaValida(Validador::validarParametro('l_fecha_comision')) )
		{
			$filtro['l_fecha_comision'] = $modelo->formatearFechaMySQL(Validador::validarParametro('l_fecha_comision'));
		}

		// 03/05/2012 FECHA DE LISTADO
		if ( Validador::validarParametro('l_fecha_de_listado') )
		{
			$filtro['l_fecha_de_listado'] = Validador::validarParametro('l_fecha_de_listado');
		}

		// SI SE MUESTRAN LOS VENCIDOS O TODOS
		$filtro['l_vencidos'] = Validador::validarParametro('l_vencidos');

		// 04/03/2013
		// 20/10/2015	CAMBIO EN LAS CONDICIONES, SE QUITÓ
		// SI SE LISTA EXPEDIENTES EN COMISION, Y NO SE FILTRA POR ESTADO NI POR COMISION DESDE EL COMBO
		if ( ( $filtro['l_tipo_listado'] == 'exped_en_comision' )  &&
			 ( $filtro['l_estado'] == '' OR $filtro['l_estado'] == '0' ) &&
			 ( $filtro['l_comision_tipo'] == '0' && $filtro['l_comision_codigo'] == '' )
		   )
		{
			$l_comisiones_modal_serializado = Validador::validarParametro('l_comisiones_modal');

			// SON LAS COMISIONES QUE SE DESEAN MOSTRAR EN EL RESULTADO DE LA BUSQUEDA
			$filtro['l_comisiones_modal'] = deserializarColeccion($l_comisiones_modal_serializado);
		}
		else
		{
			$filtro['l_comisiones_modal'] = '';
		}

		// SE ESTABLECE EL FILTRO PARA LA QUERY DEL MODELO
		$modelo->setFiltro($filtro);

		// SE OBTIENE EL LISTADO COMPLETO EN EL MODELO
		//21/05/2013
		if ( $filtro['l_tipo_listado'] == "exped_en_prestamo" )
		{
			$listado = $modelo->armar_listado_para_reporte_exped_en_prestamo();
		}
		else
		{
			$listado = $modelo->armar_listado_para_reporte();
		}

		// 09/05/2012	SI SE DESEAN CONOCER LOS DIAS EN COMISION DE LOS EXPEDIENTES/NOTAS
		if ( $filtro['l_tipo_listado'] == 'exped_en_comision' )
		{
			// 17/05/2013	SE CALCULAN LOS DIAS SOLAMENTE SI SE LISTA POR COMISION
			// 20/05/2013	ó CON ESTADO 3, 16 ó 79
			if ( $filtro['l_estado'] == '' OR $filtro['l_estado'] == '0' OR $filtro['l_estado'] == '3' OR $filtro['l_estado'] == '16' OR $filtro['l_estado'] == '79' )
			{
				$cantidad = count($listado);
				// POR CADA REGISTRO
				for ($i=0; $i < $cantidad; $i++)
				{
					$registro = &$listado[$i];

					// SE OBTIENE EL ULTIMO GIRO
					$ultimo_giro = $modelo->obtenerComisionFicha($registro['anio'], $registro['tipo'], $registro['numero'], $registro['cuerpo'], $registro['alcance']);

					// 20/05/2013 SI POSEE GIRO
					if ( $ultimo_giro[0]['fecha_giro'] != '' )
					{
						// SE AGREGA A CADA REGISTRO LOS DIAS EN COMISION
						$listado[$i]['dias'] = $this->calcularDiasEnComision($this->formatearFecha($ultimo_giro[0]['fecha_giro']), $filtro['l_fecha_de_listado'], $registro['anio'], $registro['tipo'], $registro['numero'], $registro['cuerpo'], $registro['alcance'], $ultimo_giro[0]['orden_giro']);

					// 20/04/2015, SE TOMA EL GIRO, LA FECHA DEL GIRO Y LA DESCRIPCION DE LA COMISION DEL EXPED./NOTA
						$listado[$i]['orden_giro'] = $ultimo_giro[0]['orden_giro'];
						$listado[$i]['fecha_giro'] = $ultimo_giro[0]['fecha_giro'];
						$listado[$i]['descripcion_comision'] = $ultimo_giro[0]['comision'];
					}
				}

				// SI SE DESEAN LISTAR SÓLO LOS VENCIDOS
				if ( $filtro['l_vencidos'] == 1 )
				{
					$pos = 0;
					for ($i=0; $i < $cantidad; $i++)
					{
						// SE FILTRAN LOS QUE SUPEREN 120 DIAS
						if ( $listado[$i]['dias'] > 120 )
						{
							$vencidos[$pos] = $listado[$i];
							$pos++;
						}
					}
					if ( $vencidos[0]['dias'] != '' )
					{
						$listado = $vencidos;
					}
				}
			}
		}

		$vista = new VistaExpedEnComision();

		if ( $formato == "impresion" )
		{
			switch ($filtro['l_tipo_listado'])
			{
				case 'exped_en_comision':
					$vista->generar_formato_de_impresion_exped_en_comision($listado, $filtro);
					break;
				case 'orden_del_dia':
					$vista->generar_formato_de_impresion_orden_del_dia_en_comision($listado, $filtro);
					break;
				case 'detalle_giros':
					$vista->generar_formato_de_impresion_detalle_de_giros($listado, $filtro);
					break;
				case 'asuntos_entrados':
					$vista->generar_formato_de_impresion_asuntos_entrados($listado, $filtro);
					break;
				case 'expurgo':
					$vista->generar_formato_de_impresion_expurgo($listado, $filtro);
					break;
				case 'exped_en_prestamo':
					$vista->generar_formato_de_impresion_exped_en_prestamo($listado, $filtro);
					break;
			}
		}
		elseif ($formato == "texto")
		{
			switch ($filtro['l_tipo_listado'])
			{
				case 'exped_en_comision':
					$vista->procesar_texto_exped_en_comision($listado, $filtro);
					break;
				case 'orden_del_dia':
					$vista->procesar_texto_orden_del_dia($listado, $filtro);
					break;
				case 'detalle_giros':
					$vista->procesar_texto_detalle_giros($listado, $filtro);
					break;
				case 'asuntos_entrados':
					$vista->procesar_texto_asuntos_entrados($listado, $filtro);
					break;
				case 'expurgo':
					$vista->procesar_texto_expurgo($listado, $filtro);
					break;
				case 'exped_en_prestamo':
					$vista->procesar_texto_exped_en_prestamo($listado, $filtro);
					break;
			}
		}
		elseif ($formato == "csv")
		{
			switch ($filtro['l_tipo_listado'])
			{
				case 'expurgo':
					$vista->procesarTextoExpurgoFormatoCSV($listado, $filtro);
					break;
			}
		}
    }

    public function refrescarComboComisiones() {
		$habilitado = Validador::validarParametro('habilitado');
		$comision = Validador::validarParametro('comision');

		$modelo = new expedEnComisionModel();
		$listadoComisiones = $modelo->obtenerComisiones($habilitado);

		$vista = new VistaExpedEnComision();
		$vista->comboComisiones($listadoComisiones, $comision);
    }

    public function refrescarComboEstados() {
		$habilitado = Validador::validarParametro('habilitado');
		$estado = Validador::validarParametro('estado');

		$modelo = new expedEnComisionModel();
		$listadoEstados = $modelo->obtenerEstados($habilitado);

		$vista = new VistaExpedEnComision();
		$vista->comboEstados($listadoEstados, $estado);
    }

    // $inicio_rango : FECHA DEL ULTIMO GIRO
    // $fin_rango 	 : FECHA INGRESADA EN EL BUSCADOR DEL LISTADO
    public function calcularDiasEnComision($inicio_rango, $fin_rango, $anio, $tipo, $numero, $cuerpo, $alcance, $orden_giro) {

		$clave = Array();
		$clave['anio'] = $anio;
		$clave['tipo'] = $tipo;
		$clave['numero'] = $numero;
		$clave['cuerpo'] = $cuerpo;
		$clave['alcance'] = $alcance;
		$clave['orden_giro'] = $orden_giro;

		//Se crea una instancia del modelo de Informes
		$modelo = new informesModel();

		// SE OBTIENEN TODOS LOS INFORMES DEL GIRO
		$informes = $modelo->listar($clave);

		// SI POSEE INFORMES
		if ( $informes[0]['anio'] )
		{
			$fechaEntrada = explode("/", $inicio_rango);
			$fechaSalida = explode("/", $fin_rango);

			// SE CARGA EL VECTOR CON EL RANGO DE FECHAS
			$this->cargarVectorRangoFechas($fechaEntrada, $fechaSalida, $this->meses($fechaEntrada));

			// POR CADA INFORME
			$cantidad = count($informes);
			for ( $i=0; $i < $cantidad; $i++ )
			{
				// SE CARGAN LOS CEROS EN EL VECTOR DE RANGO DE FECHAS
				$this->cargarCeros($informes[$i]['fecha_pedido_informe'], $informes[$i]['fecha_vuelta_informe']);
			}

			// SE SUMAN LOS DIAS DONDE NO ESTE PEDIDO NINGÚN INFORME
			$dias = $this->sumarDias();
		}
		else
		{
			$dias = $this->obtenerDiferenciaFechasEnDias($fin_rango, $inicio_rango);
		}

		return $dias;
	}

	/**
	 * Devuelve la diferencia en días entre la fecha de listado y la fecha en comisión
	 * @param string $fecha_listado
	 * @param string $fecha_en_comision
	 * @return integer $dias_diferencia
	 */
	public function obtenerDiferenciaFechasEnDias($fecha_listado, $fecha_en_comision) {

		// SE DIVIDE LA FECHA DE FIN DEL RANGO
		$partes_fecha_fin = explode("/", $fecha_listado);
		$anio_fin = $partes_fecha_fin[2];
		$mes_fin = $partes_fecha_fin[1];
		$dia_fin = $partes_fecha_fin[0];

		// SE DIVIDE LA FECHA DE INICIO DEL RANGO
		$partes_fecha_inicio = explode("/", $fecha_en_comision);
		$anio_inicio = $partes_fecha_inicio[2];
		$mes_inicio = $partes_fecha_inicio[1];
		$dia_inicio = $partes_fecha_inicio[0];

		// SE CALCULA EL TIMESTAMP DE LAS DOS FECHAS
		$timestamp_fin = mktime( 0, 0, 0, $mes_fin, $dia_fin, $anio_fin);
		$timestamp_inicio = mktime(0, 0, 0, $mes_inicio, $dia_inicio, $anio_inicio);

		// SE RESTA A UNA FECHA LA OTRA
		$segundos_diferencia = $timestamp_fin - $timestamp_inicio;

		// SE CONVIERTEN LOS SEGUNDOS EN DIAS
		$dias_diferencia = $segundos_diferencia / (60 * 60 * 24);

		// SE OBTIENE EL VALOR ABSOLUTO DE LOS DIAS (SE QUITA UN POSIBLE SIGNO NEGATIVO)
		$dias_diferencia = abs($dias_diferencia);

		// SE QUITAN LOS DECIMALES A LOS DIAS DE DIFERENCIA, EN CASO DE EXISTIR
		$dias_diferencia = floor($dias_diferencia);

		if ( $dias_diferencia < 0 )
		{
			$dias_diferencia = 0;
		}

		return $dias_diferencia;
	}

	// DEVUELVE 29 SI ES BISIESTO, SINO 28
	public function anioBisiesto($anio) {
		// Un año es bisiesto si es divisible entre 4, excepto aquellos divisibles entre 100 pero no entre 400.
		if ( ( $anio%4 == 0 && $anio%100 != 0 ) || $anio%400 == 0 )
		{
			return 29;
		}
		else
		{
			return 28;
		}
	}

	// DEVUELVE LA CANTIDAD DE DIAS DEL MES RESPECTIVO A LA FECHA
	public function meses($fecha) {
		// SI EL MES ES FEBRERO
		if ( $fecha[1] == 2 )
		{
			return $this->anioBisiesto($fecha[2]);
		}
		elseif ( $fecha[1] == 1 || $fecha[1] == 3 || $fecha[1] == 5 || $fecha[1] == 7 || $fecha[1] == 8 || $fecha[1] == 10 || $fecha[1] == 12 )
		{
			return 31;
		}
		else
		{
			return 30;
		}
	}

	// SE CARGA UN VECTOR DE FECHAS CON UNOS
	public function cargarVectorRangoFechas($fechaIn, $fechaOut, $cantidad_dias_del_mes) {

		// $cantidad_dias_del_mes: CANTIDAD DE DIAS DEL MES DE LA $fechaIn
		// $fechaIn[0] Y $fechaOut[0] ES EL DIA
		// $fechaIn[1] Y $fechaOut[1] ES EL MES
		// $fechaIn[2] Y $fechaOut[2] ES EL AÑO

		//  SE CONCATENA EL AÑO, MES Y DIA DE LA FECHA HASTA PARA COMPARAR
		$fecha_hasta = $fechaOut[2].$fechaOut[1].$fechaOut[0];

		$i = 0;// POSICION DEL VECTOR

		while( true )
		{
			// PARA COMPLETAR EL DÍA SI NO LO ESTÁ Y ES MENOR A DIEZ
			if ( $fechaIn[0] < 10 )
			{
				$fechaIn[0] = substr('0'.$fechaIn[0], -2);
			}
			// PARA COMPLETAR EL MES SI NO LO ESTÁ Y ES MENOR A DIEZ
			if ( $fechaIn[1] < 10 )
			{
				$fechaIn[1] = substr('0'.$fechaIn[1], -2);
			}

			// SE CONCATENA EL AÑO, MES Y DIA DE LA FECHA DESDE PARA COMPARAR
			$fecha_desde = $fechaIn[2].$fechaIn[1].$fechaIn[0];

			// SI SE LLEGO AL FINAL DEL RANGO, TERMINA DE CARGAR EL VECTOR
			if ( $fecha_desde > $fecha_hasta )
			{
				break;
			}

			// SE INICIALIZA EL VECTOR EN 1 CON CADA DIA DEL RANGO, CON FORMATO DE FECHA yyyy-mm-dd PARA COMPARAR
			$this->vector_rango_de_dias[$i]['fecha'] = $fechaIn[2]."-".$fechaIn[1]."-".$fechaIn[0];
			$this->vector_rango_de_dias[$i]['valor'] = 1;

			// SI NO SE LLEGÓ AL ULTIMO DIA DEL MES (28, 29, 30 o 31)
			if ( $fechaIn[0] < $cantidad_dias_del_mes)
			{
				$fechaIn[0]++;// SE INCREMENTA EL DIA
			}
			else // SI ES EL ULTIMO DIA
			{
				$fechaIn[0] = 1;// COMIENZA EN EL DIA 1
				$fechaIn[1]++;// SE PASA AL MES SIGUIENTE

				// SI EL MES ES MAYOR A DICIEMBRE, COMIENZA EL SIGUIENTE AÑO
				if ( $fechaIn[1] > 12 )
				{
					$fechaIn[2]++;// SE PASA AL AÑO SIGUIENTE
					$fechaIn[1] = 1; // MES 1, ENERO
				}

				// CANTIDAD DE DIAS DEL MES SIGUIENTE
				$cantidad_dias_del_mes = $this->meses($fechaIn);
			}

			$i++;// SIGUIENTE POSICION
		}
	}

	// SE CARGA CON CEROS LOS DIAS DEL PERIODO QUE ESTA PEDIDO CADA INFORME DEL GIRO
	public function cargarCeros($fecha_pedido, $fecha_vuelta) {

		$cantidad = count($this->vector_rango_de_dias);
		$con_informe = false;

		// CASO ESPECIAL: SI LA fecha_pedido ES MENOR A LA FECHA DE ENTRADA DEL GIRO
		if ( $fecha_pedido < $this->vector_rango_de_dias[0]['fecha'] )
		{
			// fecha_pedido = FECHA DE INICIO DEL RANGO
			$fecha_pedido = $this->vector_rango_de_dias[0]['fecha'];
		}

		// SE RECORRE EL VECTOR
		for ( $i=0; $i < $cantidad; $i++ )
		{
			// SI CONCUERDA LA fecha_pedido CON LA FECHA DEL VECTOR
			if ( $this->vector_rango_de_dias[$i]['fecha'] == $fecha_pedido )
			{
				// SE EMPIEZA A CARGAR CON CERO
				$this->vector_rango_de_dias[$i]['valor'] = 0;
				$con_informe = true;
			}

			// SI YA SE EMPEZÓ A CARGAR CEROS
			if ( $con_informe )
			{
				if ( $fecha_vuelta == null )
				{
					// SE SIGUE CARGANDO CON CEROS HASTA EL FINAL DEL VECTOR
					$this->vector_rango_de_dias[$i]['valor'] = 0;
				}
				else
				{
					// SI NO SE LLEGÓ A LA FECHA DE VUELTA
					if ( $this->vector_rango_de_dias[$i]['fecha'] != $fecha_vuelta )
					{
						// SE SIGUE CARGANDO CON CEROS
						$this->vector_rango_de_dias[$i]['valor'] = 0;
					}
					else
					{
						// SE CARGA EL ULTIMO CERO PORQUE LLEGÓ A LA FECHA DE VUELTA
						$this->vector_rango_de_dias[$i]['valor'] = 0;
						// SE ESTABLECE QUE PASÓ EL PERIODO DEL INFORME
						$con_informe = false;
					}
				}
			}
		}
	}

	// SE OBTIENE LA SUMA DE LOS DIAS DONDE NO ESTE PEDIDO NINGÚN INFORME (CON VALOR 1)
	public function sumarDias() {

		$suma = 0;
		$cantidad = count($this->vector_rango_de_dias);
		for ( $i=0; $i < $cantidad; $i++ )
		{
			if ( $this->vector_rango_de_dias[$i]['valor'] == 1 )
			{
				$suma++;
			}
		}
		// 24/04/2013
		// SE VACIA EL VECTOR LUEGO DE REALIZAR LA SUMA
		$this->vector_rango_de_dias = null;

		return $suma;
	}

	/**
	 * Se obtienen los Informes de una Comisión determinada, en un rango de fechas específico
	 */
    public function listarInformes() {
		//Se crea una instancia del modelo
		$modelo = new expedEnComisionModel();

		$filtro = Array();

		$filtro['l_enviado'] = Validador::validarParametro('l_enviado');
		$filtro['l_tipo_listado'] = Validador::validarParametro('l_tipo_listado');

		// SE FILTRA POR Fecha desde Y Fecha hasta
		if ( $this->esFechaValida(Validador::validarParametro('l_fecha_desde')) )
			$filtro['l_fecha_desde'] = $modelo->formatearFechaMySQL(Validador::validarParametro('l_fecha_desde'));

		if ( $this->esFechaValida(Validador::validarParametro('l_fecha_hasta')) )
			$filtro['l_fecha_hasta'] = $modelo->formatearFechaMySQL(Validador::validarParametro('l_fecha_hasta'));

		// FECHA DE LISTADO
		if ( Validador::validarParametro('l_fecha_de_listado') )
			$filtro['l_fecha_de_listado'] = Validador::validarParametro('l_fecha_de_listado');

		$filtro['l_vencidos'] = Validador::validarParametro('l_vencidos');	// SI SE MUESTRAN LOS VENCIDOS O TODOS

		$filtro['l_solo_habilitado'] = Validador::validarParametro('l_solo_habilitado');	//SI SE MUESTRAN SOLO LOS Habilitados O NO

		// SE FILTRA POR Comision
		$filtro['l_comision'] = Validador::validarParametro('l_comision');
		$comision = explode("-", Validador::validarParametro('l_comision'));
		$filtro['l_comision_tipo']   = $comision[0];
		$filtro['l_comision_codigo'] = $comision[1];

		$filtro['l_rango'] = 5;	//cantidad de registros a mostrar
		$filtro['l_pagina'] = Validador::validarParametro('l_pagina');	//se obtiene el valor de la pagina

		if ( $filtro['l_pagina'] == '' ) {	//si no se sabe el valor de la pagina
			$filtro['l_inicio'] = 0;	//se inicia en el primer registro
			$filtro['l_pagina'] = 1;	//con la primer pagina
		} else
			//si se conoce se calcula el valor del registro inicial de dicha pagina
			$filtro['l_inicio'] = ($filtro['l_pagina'] * $filtro['l_rango']) - $filtro['l_rango'];

		$filtro['l_pagina_ant']  = $filtro['l_pagina'] - 1;	//para la pagina anterior
		$filtro['l_pagina_sgte'] = $filtro['l_pagina'] + 1;	//para la pagina posterior

		if ( $filtro['l_enviado'] == 'enviado' ) {
			// SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
			$_SESSION['filtro_LISTADOS'] = $filtro;

			$modelo->setFiltro($filtro);

			// Se obtienen los Informes
			$listado = $modelo->listarInformes();

			// SI SE OBTUVIERON DATOS
			if ( $listado ) {
				$cantidad = count($listado);
				// SE CALCULA LA DIFERENCIA EN DIAS DESDE LA FECHA DE PEDIDO Y LA FECHA DE LISTADO
				for ($i=0; $i < $cantidad; $i++) {
					$registro = &$listado[$i];

					// SE AGREGA A CADA REGISTRO LOS DIAS QUE ESTA PEDIDO EL INFORME
					$listado[$i]['dias'] = $this->obtenerDiferenciaFechasEnDias($filtro['l_fecha_de_listado'], $this->formatearFecha($registro['fecha_pedido_informe']));
				}

				// SI SE DESEAN LISTAR SÓLO LOS VENCIDOS
				if ( $filtro['l_vencidos'] == 1 ) {
					$pos = 0;
					for ($i=0; $i < $cantidad; $i++) {
						// SE FILTRAN LOS QUE SUPEREN 30 DIAS
						if ( $listado[$i]['dias'] > 30 ) {
							$vencidos[$pos] = $listado[$i];
							$pos++;
						}
					}

					// Si existe por lo menos un informe vencido
					if ( $vencidos[0]['dias'] != '' ) {
						// Se toman solamente los informes vencidos
						$listado = $vencidos;
						// Se asigna la nueva cantidad de informes resultantes
						$filtro['l_cantidad'] = count($listado);
					}
				} else
					// Se toma la cantidad calculada previamente
					$filtro['l_cantidad'] = $_SESSION['total'];

				$filtro['l_nro_paginas'] = 1;
				if ( $filtro['l_cantidad'] > 5 )
					// NUMERO TOTAL DE PAGINAS (DE 5 informes CADA UNA)
					$filtro['l_nro_paginas'] = ceil($filtro['l_cantidad'] / $filtro['l_rango']);
			}
		}

		// Se le pide al modelo todas las comisiones
		$this->listadoComisiones = $modelo->obtenerComisiones();

		$vista = new VistaExpedEnComision();
		$vista->listarInformes($listado, $this->listadoComisiones, $filtro);
    }

    // XXXX: Modificado el 19/10/2018
    // Se utiliza un método propio del Modelo
    //
    // SE OBTIENE EL LISTADO COMPLETO PARA GENERAR EL FORMATO DE IMPRESION Y EL DOCUMENTO DE TEXTO PARA LOS INFORMES
    public function armar_listado_completo_listadoInformes() {
		$modelo = new expedEnComisionModel();

		// SE ARMA EL FILTRO PARA LA VISTA
		$filtro = Array();

		// PARA UTILIZAR LA VISTA CORRESPONDIENTE, EL FORMATO DE IMPRESION O EL DOCUMENTO DE TEXTO
		$formato = Validador::validarParametro('formato');

		//SE FILTRA POR Fecha desde Y Fecha hasta
		if ( $this->esFechaValida(Validador::validarParametro('l_fecha_desde')) )
			$filtro['l_fecha_desde'] = $modelo->formatearFechaMySQL(Validador::validarParametro('l_fecha_desde'));

		if ( $this->esFechaValida(Validador::validarParametro('l_fecha_hasta')) )
			$filtro['l_fecha_hasta'] = $modelo->formatearFechaMySQL(Validador::validarParametro('l_fecha_hasta'));

		// FECHA DE LISTADO
		if ( Validador::validarParametro('l_fecha_de_listado') )
			$filtro['l_fecha_de_listado'] = Validador::validarParametro('l_fecha_de_listado');

		$filtro['l_vencidos'] = Validador::validarParametro('l_vencidos');	// SI SE MUESTRAN LOS VENCIDOS O TODOS

		// SE FILTRA POR Comision
		$filtro['l_comision'] = Validador::validarParametro('l_comision');
		$comision = explode("-", Validador::validarParametro('l_comision'));
		$filtro['l_comision_tipo'] = $comision[0];
		$filtro['l_comision_codigo'] = $comision[1];

		// SE ESTABLECE EL FILTRO PARA LA QUERY DEL MODELO
		$modelo->setFiltro($filtro);

		// SE OBTIENE EL LISTADO COMPLETO EN EL MODELO
		$listado = $modelo->armarListadoInformesReporte();// armar_listado_para_reporte

		// SI SE OBTUVIERON DATOS
		if ( $listado ) {
			$cantidad = count($listado);
			// SE CALCULA LA DIFERENCIA EN DIAS DESDE LA FECHA DE PEDIDO Y LA FECHA DE LISTADO
			for ($i=0; $i < $cantidad; $i++) {
				$registro = &$listado[$i];
				// SE AGREGA A CADA REGISTRO LOS DIAS QUE ESTA PEDIDO EL INFORME
				$listado[$i]['dias'] = $this->obtenerDiferenciaFechasEnDias($filtro['l_fecha_de_listado'], $this->formatearFecha($registro['fecha_pedido_informe']));
			}

			// SI SE DESEAN LISTAR SÓLO LOS VENCIDOS
			if ( $filtro['l_vencidos'] == 1 ) {
				$pos = 0;
				for ($i=0; $i < $cantidad; $i++) {
					// SE FILTRAN LOS QUE SUPEREN 30 DIAS
					if ( $listado[$i]['dias'] > 30 ) {
						$vencidos[$pos] = $listado[$i];
						$pos++;
					}
				}
				if ( $vencidos[0]['dias'] != '' )
					$listado = $vencidos;
			}
		}

		$vista = new VistaExpedEnComision();

		if ( $formato == "impresion" )
			$vista->generar_formato_de_impresion_informes($listado, $filtro);
		elseif ($formato == "texto")
			$vista->procesar_texto_informes($listado, $filtro);
    }

    // XXXX: Realizado el 15/11/2011
    // XXXX: Agregado aquí el 27/04/2015, para mostrar en el listado de Expedientes en Comisión
    // XXXX: Optimizado el 19/01/2017
    public function verificarEstadoDoc($expediente)
    {
		$anio_corto = substr($expediente['anio'], -2);
		$tipo = $expediente['tipo'];
		$aux_numero = 100000+$expediente['numero'];
		$numero = substr($aux_numero, -5);

		$nombre_codificado = $anio_corto.$tipo.$numero;

		$directorio_remoto = self::RUTA_DIRECTORIO_PROYECTOS.$expediente['anio']."/".$nombre_codificado."/";

		$estado_doc = 3;// ESTADO 'SIN CARGAR' por defecto

		if ( file_exists("/var/www/sgl/expedientes/proyectos/temporal/".$nombre_codificado.".doc") )// Si se encuentra el documento en el directorio '/temporal'
		    $estado_doc = 1;// ESTADO 'PARA CARGAR'
		else if ( file_exists($directorio_remoto.'original.doc') )// Si ya existe como 'original.doc'
		    $estado_doc = 2;// ESTADO 'CARGADO'

		return $estado_doc;
    }

	// 27/04/2015
    public function listar_expedientes_sin_cargar()
    {
		$vista = new VistaExpedEnComision();
		$vista->listar_expedientes_sin_cargar();
    }

    // 27/04/2015, SE OBTIENE EL LISTADO DE EXPEDIENTES SIN CARGAR, PARA GENERAR EL FORMATO DE IMPRESION Y DE DOCUMENTO DE TEXTO
    public function armar_listado_exped_sin_cargar()
	{
		$modelo = new expedEnComisionModel();

		// SE ARMA EL FILTRO PARA LA VISTA
		$filtro = Array();

		// PARA UTILIZAR LA VISTA CORRESPONDIENTE, EL FORMATO DE IMPRESION O EL DOCUMENTO DE TEXTO
		$formato = Validador::validarParametro('formato');

		//SE FILTRA POR Fecha desde Y Fecha hasta
		if ($this->esFechaValida(Validador::validarParametro('l_fecha_desde')))
		{
			$filtro['l_fecha_desde'] = $modelo->formatearFechaMySQL(Validador::validarParametro('l_fecha_desde'));
		}
		if ($this->esFechaValida(Validador::validarParametro('l_fecha_hasta')))
		{
			$filtro['l_fecha_hasta'] = $modelo->formatearFechaMySQL(Validador::validarParametro('l_fecha_hasta'));
		}

		// SE ESTABLECE EL FILTRO PARA LA QUERY DEL MODELO
		$modelo->setFiltro($filtro);

		// SE OBTIENE EL LISTADO COMPLETO EN EL MODELO
		$listado = $modelo->armar_listado_exped_sin_cargar();

		$cantidad = count($listado);
		$pos = 0;
		for ($i=0; $i < $cantidad; $i++)
		{
			// SE VERIFICA EL ESTADO DEL PROYECTO (ARCHIVO original.doc) EN EL EXPEDIENTE
			//1 = PARA CARGAR, 2 = CARGADO, 3 = SIN CARGAR
			$estado_doc = $this->verificarEstadoDoc($listado[$i]);

			// SI ESTA SIN CARGAR EL DOCUMENTO
			if ( $estado_doc == 3 )
			{
				$listado_sin_cargar[$pos] = $listado[$i];
				$pos++;
			}
		}

		// SI SE ENCONTRO POR LO MENOS UN DOCUMENTO SIN CARGAR
		if ( $listado_sin_cargar[0]['anio'] != '' )
		{
			// SE REDEFINE EL LISTADO DE RESULTADOS
			$listado = $listado_sin_cargar;
		}
		else
		{
			// SI NO SE ENCONTRO NINGUNO SIN CARGAR, NO HAY NADA QUE MOSTRAR
			$listado = null;
		}

		$vista = new VistaExpedEnComision();

		if ( $formato == "impresion" )
		{
			$vista->generar_formato_de_impresion_expedientes_sin_cargar($listado, $filtro);
		}
		elseif ( $formato == "texto" )
		{
			$vista->procesar_texto_expedientes_sin_cargar($listado, $filtro);
		}
    }

	/**
	 * 2019/02/13
	 * Se obtiene el listado de expedientes sin digitalizar
	 */
    public function listar_expedientes_sin_digitalizar() {
		$vista = new VistaExpedEnComision();
		$vista->listar_expedientes_sin_digitalizar();
    }

    /**
	 * 13/02/2019
	 * SE OBTIENE EL LISTADO DE EXPEDIENTES SIN digitalizar, PARA GENERAR EL FORMATO DE IMPRESION Y DE DOCUMENTO DE TEXTO
	 */
    public function armar_listado_exped_sin_digitalizar() {

		$modelo = new expedEnComisionModel();

		// SE ARMA EL FILTRO PARA LA VISTA
		$filtro = Array();

		// PARA UTILIZAR LA VISTA CORRESPONDIENTE, EL FORMATO DE IMPRESION O EL DOCUMENTO DE TEXTO
		$formato = Validador::validarParametro('formato');

		//SE FILTRA POR Fecha desde Y Fecha hasta
		if ($this->esFechaValida(Validador::validarParametro('l_fecha_desde')))
		{
			$filtro['l_fecha_desde'] = $modelo->formatearFechaMySQL(Validador::validarParametro('l_fecha_desde'));
		}
		if ($this->esFechaValida(Validador::validarParametro('l_fecha_hasta')))
		{
			$filtro['l_fecha_hasta'] = $modelo->formatearFechaMySQL(Validador::validarParametro('l_fecha_hasta'));
		}

		// SE ESTABLECE EL FILTRO PARA LA QUERY DEL MODELO
		$modelo->setFiltro($filtro);

		// SE OBTIENE EL LISTADO COMPLETO EN EL MODELO
		$listado = $modelo->armar_listado_exped_sin_digitalizar();

		$cantidad = count($listado);
		$pos = 0;
		for ($i=0; $i < $cantidad; $i++) {
			// Si NO está digitalizado
			if ( $this->verificarEstadoDigitalizacion($listado[$i]) != 2 ) {

				$listado_sin_digitalizar[$pos] = $listado[$i];
				$pos++;
			}
		}

		// Si se encontró por lo menos uno
		if ( $listado_sin_digitalizar[0]['anio'] != '' )
		{
			// Se asignan sólo aquellos sin digitalizar
			$listado = $listado_sin_digitalizar;
		}
		else
		{
			// SI NO SE ENCONTRO NINGUNO SIN digitalizar, NO HAY NADA QUE MOSTRAR
			$listado = null;
		}

		$vista = new VistaExpedEnComision();

		if ( $formato == "impresion" )
		{
			$vista->generar_formato_de_impresion_expedientes_sin_digitalizar($listado, $filtro);
		}
		elseif ( $formato == "texto" )
		{
			$vista->procesar_texto_expedientes_sin_digitalizar($listado, $filtro);
		}
    }
}
?>

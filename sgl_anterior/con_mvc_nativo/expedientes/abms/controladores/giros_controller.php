<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

// Incluye el modelo que corresponde
require 'modelos/giros.php';
require 'modelos/estados.php';
require '../tareas/modelos/carga_giros.php';
require 'modelos/informes.php';

// Incluye la vista que corresponde
require 'vistas/giros.php';

class giros_controller extends ControllerBase
{
	private $clave = Array();
	
	// VECTOR CON EL RANGO DE DIAS
	private $vector_rango_de_dias = Array();
		
	//SE LE DA EL FORMATO dia/mes/anio completo
	public function formatearFecha($fecha)
	{
	    if ($fecha)
	    {
			if ($fecha != '0000-00-00')
			{
				$fec_partes = explode("-",$fecha);
				$fecha_a_ver = $fec_partes[2].'/'.$fec_partes[1].'/'.$fec_partes[0]; 
				return $fecha_a_ver;
			}
			else
			{
				return '';
			}	
	    }
	    else
	    {
			return '';
	    }	
	}
	
	public function guardarRegistroOriginal($original)
	{
		$_SESSION['anio_original'] = $original[0]['anio'];
		$_SESSION['tipo_original'] = $original[0]['tipo'];
		$_SESSION['numero_original'] = $original[0]['numero'];
		$_SESSION['cuerpo_original'] = $original[0]['cuerpo'];
		$_SESSION['alcance_original'] = $original[0]['alcance'];
		$_SESSION['orden_giro_original'] = $original[0]['orden_giro'];
		$_SESSION['comision_tipo_original'] = $original[0]['comision_tipo'];
		$_SESSION['comision_codigo_original'] = $original[0]['comision_codigo'];
		$_SESSION['fecha_entrada_giro_original'] = $original[0]['fecha_entrada_giro'];
		$_SESSION['fecha_salida_giro_original'] = $original[0]['fecha_salida_giro'];
		$_SESSION['dictamen_giro_original'] = $original[0]['dictamen_giro'];
		$_SESSION['observaciones_giro_original'] = $original[0]['observaciones_giro'];
		$_SESSION['id_usuario_original'] = $original[0]['id_usuario'];
		//fputs(fopen('SESSIONRegistroOriginalExpedC.txt','w'),print_r($_SESSION,true));
	}
	
	public function listar($mensaje = '', $tipo_mensaje = '', $clave = '')
	{	
		//Se crea una instancia del modelo
		$modelo = new girosModel();
		
		$filtro = Array();
		
		if ( !empty($clave) )
		{
			$filtro['anio'] = $clave['anio'];
			$filtro['tipo'] = $clave['tipo'];
			$filtro['numero'] = $clave['numero'];
			$filtro['cuerpo'] = $clave['cuerpo'];
			$filtro['alcance'] = $clave['alcance'];
			$filtro['sentido'] = 'anterior';// DIRECCIÓN PARA LA PAGINACIÓN (PRIMERO, ANTERIOR, SGTE., ÚLTIMO)
		}
		else
		{
			$filtro['anio'] = Validador::validarParametro('anio');
			$filtro['tipo'] = Validador::validarParametro('tipo');
			$filtro['numero'] = Validador::validarParametro('numero');
			$filtro['cuerpo'] = Validador::validarParametro('cuerpo');
			$filtro['alcance'] = Validador::validarParametro('alcance');
			$filtro['sentido'] = Validador::validarParametro('sentido');// DIRECCIÓN PARA LA PAGINACIÓN (PRIMERO, ANTERIOR, SGTE., ÚLTIMO)
			$filtro['por_teclado'] = Validador::validarParametro('por_teclado');// SI SE LLEGÓ MEDIANTE EL TECLADO AL RECORRER EL LISTADO
		}
		
		//se establece el campo por el cual ordenar
		if ( !empty($_GET['campo_orden']) )
		{
			$filtro['campo_orden'] = Validador::validarParametro('campo_orden');
		}
		else
		{
			$filtro['campo_orden'] = 'anio';//por defecto
			$_SESSION['ultimo_campo'] = '';
		}
		
		if ( !isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $filtro['campo_orden'] )
		{
			// Si es la primera vez que carga la pagina
			// o se esta cambiando el campo por el que se ordena
			$_SESSION['ultimo_campo'] = $filtro['campo_orden'];
			$_SESSION['ultimo_sentido'] = 'asc';
		}
		else
		{
			// Si se hizo clic en el mismo que ya estaba ordenado antes
			// Solo hay que cambiar el sentido:
			if ($_SESSION['ultimo_sentido'] == 'asc') {
				$_SESSION['ultimo_sentido'] = 'desc';
			} else {
				$_SESSION['ultimo_sentido'] = 'asc';
			}
		}
		
		$filtro['rango'] = 10;	//cantidad de registros a mostrar
		$filtro['pagina'] = Validador::validarParametro('pagina');	//se obtiene el valor de la pagina
					
		if ( !$filtro['pagina'] )
		{	//si no se sabe el valor de la pagina
			$filtro['inicio'] = 0;	//se inicia en el primer registro
			$filtro['pagina'] = 1;	//con la primer pagina 
		}
		else
		{	//si se conoce se calcula el valor del registro inicial de dicha pagina 
			$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
		} 
		$filtro['pagina_ant'] = $filtro['pagina'] - 1;	//para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;	//para la pagina posterior

		// Se establece el filtro en el modelo
		$modelo->setFiltro($filtro);		
		//Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
		$filtro['cantidad'] = $modelo->obtenerCantidad();
		
		// NUMERO TOTAL DE PAGINAS 
		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);
		
		// Se establece el filtro en el modelo
		$modelo->setFiltro($filtro);
		
		// Se le pide al modelo todos los items
		$listado = $modelo->listar();
		//fputs(fopen('listado_Giros.txt','w'),print_r($listado,true));
		
		/********************************
			NUEVO 09/08/2012
		/*******************************/
		$cantidad = count($listado);
		// POR CADA GIRO
		for ($i=0; $i < $cantidad; $i++)
		{
			$registro = &$listado[$i];
			
			$fecha_actual = date("d/m/Y");
			
			//fputs(fopen("registro_".$i."_giros.txt", 'w'), print_r($registro, true));
			
			// 03/10/2012 CORREGIDA LA CONDICION PARA LAS FECHAS
			// SI POSEE FECHA DE ENTRADA Y NO FECHA DE SALIDA, SE OBTIENEN LOS DIAS DEL EXPEDIENTE EN COMISION
			if ( $registro['fecha_entrada_giro'] > '0000-00-00' && ( $registro['fecha_salida_giro'] == '' || $registro['fecha_salida_giro'] == '0000-00-00' ) )
			{
				// SE AGREGA A CADA REGISTRO LOS DIAS EN COMISION
				$listado[$i]['dias'] = $this->calcularDiasEnComision($this->formatearFecha($registro['fecha_entrada_giro']), $fecha_actual, $registro['anio'], $registro['tipo'], $registro['numero'], $registro['cuerpo'], $registro['alcance'], $registro['orden_giro']);
			}
		}
		//fputs(fopen('listado_con_dias_Giros.txt','w'),print_r($listado,true));
		/**/
		
 		//Se crea una instancia de la vista
		$vista = new VistaGiros();
		//se muestra el listado
		$vista->listar($listado, $mensaje, $tipo_mensaje, $filtro);
	}
	
	public function editar($clave = '')
	{	
		$filtro = Array();
		if ( !empty($clave) )
		{	
			$filtro['anio'] = $clave['anio'];
			$filtro['tipo'] = $clave['tipo'];
			$filtro['numero'] = $clave['numero'];
			$filtro['cuerpo'] = $clave['cuerpo'];
			$filtro['alcance'] = $clave['alcance'];
		}else{
			$filtro['anio'] = Validador::validarParametro('anio');
			$filtro['tipo'] = Validador::validarParametro('tipo');
			$filtro['numero'] = Validador::validarParametro('numero');
			$filtro['cuerpo'] = Validador::validarParametro('cuerpo');
			$filtro['alcance'] = Validador::validarParametro('alcance');
			$filtro['orden_giro'] = Validador::validarParametro('orden_giro');
		}	
	
		//se establece el campo por el cual ordenar
		$campo_orden = Validador::validarParametro('campo_orden');
		if ($campo_orden != ''){
			$filtro['campo_orden'] = $campo_orden;
		}else{
			//por defecto
			$filtro['campo_orden'] = 'anio';
			$_SESSION['ultimo_campo'] = '';
		}
		
		if (!isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $filtro['campo_orden']) {
			// Si es la primera vez que carga la pagina
			// o se esta cambiando el campo por el que se ordena
			$_SESSION['ultimo_campo'] = $filtro['campo_orden'];
			$_SESSION['ultimo_sentido'] = 'asc';
		} else {
			// Si se hizo clic en el mismo que ya estaba ordenado antes
			// Solo hay que cambiar el sentido:
			if ($_SESSION['ultimo_sentido'] == 'asc') {
				$_SESSION['ultimo_sentido'] = 'desc';
			} else {
				$_SESSION['ultimo_sentido'] = 'asc';
			}
		}
			
		$filtro['rango'] = 10;	//cantidad de registros a mostrar
		$filtro['pagina'] = Validador::validarParametro('pagina');	//se obtiene el valor de la pagina
					
		if ( !$filtro['pagina'] )
		{	//si no se sabe el valor de la pagina
			$filtro['inicio'] = 0;	//se inicia en el primer registro
			$filtro['pagina'] = 1;	//con la primer pagina 
		}
		else
		{	//si se conoce se calcula el valor del registro inicial de dicha pagina 
			$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
		} 
		$filtro['pagina_ant'] = $filtro['pagina'] - 1;		//para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;		//para la pagina posterior
				
		//Se crea una instancia del modelo
		$modelo = new girosModel();
		//Se establece el filtro en el modelo
		$modelo->setFiltro($filtro);
		//Se le pide al modelo todos los items
		$listado = $modelo->listar();
		
		// SE GUARDA EL REGISTRO EN SESION PARA VERIFICAR 
		// LUEGO SI LO HA MODIFICADO OTRO USUARIO
		$this->guardarRegistroOriginal($listado);
		
		//Se crea una instancia de la "vista"
		$vista = new VistaGiros();
		$vista->editar($listado, $filtro);
	}
	
	public function agregar()
	{			
		$filtro = Array();
		$filtro['anio'] = Validador::validarParametro('anio');
		$filtro['tipo'] = Validador::validarParametro('tipo');
		$filtro['numero'] = Validador::validarParametro('numero');
		$filtro['cuerpo'] = Validador::validarParametro('cuerpo');
		$filtro['alcance'] = Validador::validarParametro('alcance');
		/**
		//se establece el campo por el cual ordenar
		if ($_GET['campo_orden'] != ''){
			$filtro['campo_orden'] = Validador::validarParametro('campo_orden');
		}else{
			//por defecto
			$filtro['campo_orden'] = 'anio';
		}
		$filtro['rango'] = 10;	//cantidad de registros a mostrar
		$filtro['pagina'] = 1;
		
		//se calcula el valor del registro inicial de dicha pagina 
		$filtro['inicio'] = 0;
		
		$filtro['pagina_ant'] = $filtro['pagina'] - 1;		//para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;		//para la pagina posterior
		/**/
		//Se crea una instancia del modelo
		$modelo = new girosModel();
		
		//SE OBTIENE EL ULTIMO ORDEN INGRESADO
		$filtro['ultimoOrden'] = $modelo->obtenerUltimoOrden($filtro);
		
		//Se crea una instancia de la Vista
		$vista = new VistaGiros();
		
		//se muestra el formulario
		$vista->editar(null, $filtro);
	}
	
	public function insertar()
	{
		$post = $_REQUEST;
		if ( $post['numero'] == '' ){ $post['numero'] = 0; }
		if ( $post['cuerpo'] == '' ){ $post['cuerpo'] = 0; }
		if ( $post['alcance'] == '' ){ $post['alcance'] = 0; }
		
		$this->clave['anio'] = $post['anio'];
		$this->clave['tipo'] = $post['tipo'];
		$this->clave['numero'] = $post['numero'];
		$this->clave['cuerpo'] = $post['cuerpo'];
		$this->clave['alcance'] = $post['alcance'];
					  
		//Se crea una instancia del modelo
		$modelo = new girosModel();	
			
		//PARA QUE DOS USUARIOS NO INGRESEN EL MISMO REGISTRO	
		if ( !$modelo->existe($this->clave) )
		{
			if ( $modelo->insertar($post) )
			{
				$mensaje = 'El Giro se agreg&oacute; con &eacute;xito.';
				$tipo_mensaje = 1;
				
				// 13/04/2012
				// SI ES EL PRIMER GIRO INGRESADO AL EXPEDIENTE
				if ( $post['orden_giro'] == 1 )
				{
					// 24/01/2012: SE AGREGA EL ESTADO 3 (Girado a comision) AL EXPEDIENTE
					$modeloEstado = new estadosModel();
					$ordenEstado = $modeloEstado->obtenerUltimoOrden($post);// SE OBTIENE EL ULTIMO ORDEN DEL Estado PARA EL EXPEDIENTE DETERMINADO
					$nuevo_orden_estado = $ordenEstado + 1;
					
					// SE ESTABLECE LA FECHA PARA EL ESTADO 3
					if ( $post['fecha_entrada_giro'] != '' )
					{
						$fecha_a_guardar = $modelo->formatearFechaMySQL($post['fecha_entrada_giro']);
					}
					else
					{
						$fecha_a_guardar = date("Y-m-d");
					}
					//Se crea una instancia del modelo cargarGirosModel
					$modeloCargarGiros = new cargarGirosModel();
					// SE REGISTRA EL ESTADO 3 (GIRADO A COMISION) PARA EL EXPEDIENTE
					$modeloCargarGiros->registrar_estado_girado_a_comision($post, $fecha_a_guardar, $nuevo_orden_estado);
				}
			}
			else
			{
				$mensaje = 'Error al agregar el Giro.';
				$tipo_mensaje = 2;
			}
			$this->listar($mensaje, $tipo_mensaje, $post);
		}
		else
		{
			$mensaje = 'El registro se ha ingresado previamente.';
			$tipo_mensaje = 2;
			$this->listar($mensaje, $tipo_mensaje);
		}
			
	}
	
	public function modificar()
	{	
		$post = $_REQUEST;
		//fputs(fopen('postModificarGiro.txt','w'),print_r($post, true));
		
		//Se crea una instancia del modelo
		$modelo = new girosModel();
		
		// 	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO PREVIAMENTE
		if ( $modelo->verificarRegistroEntero() )
		{	
			if ( $modelo->modificar($post) )
			{
				$mensaje = 'El Giro se modific&oacute; con &eacute;xito.';
				$tipo_mensaje = 1;
			}
			else
			{
				$mensaje = 'Error al modificar el Giro.';
				$tipo_mensaje = 2;
			}
		}
		else
		{
			$mensaje = 'El registro se ha modificado previamente.';
			$tipo_mensaje = 2;
		}
		$this->listar($mensaje, $tipo_mensaje, $post);
	}
	
	public function eliminar()
	{	
		$this->clave['anio'] = Validador::validarParametro('anio');
		$this->clave['tipo'] = Validador::validarParametro('tipo');
		$this->clave['numero'] = Validador::validarParametro('numero');
		$this->clave['cuerpo'] = Validador::validarParametro('cuerpo');
		$this->clave['alcance'] = Validador::validarParametro('alcance');
		$this->clave['fecha_entrada_giro'] = Validador::validarParametro('fecha_entrada_giro');
		$this->clave['orden_giro'] = Validador::validarParametro('orden_giro');
		
		//Se crea una instancia del modelo
		$modelo = new girosModel();
 		
 		if ( $modelo->existeInformePendiente($this->clave) )
			$this->listar("El Giro posee un Informe pendiente.", 2, $this->clave);
		else {
			if ( $modelo->eliminar($this->clave) )
				$this->listar("El Giro se elimin&oacute; con &eacute;xito.", 1, $this->clave);
			else
				$this->listar("Error al eliminar el Giro.", 2, $this->clave);
		}
	}
	
    public function calcularDiasEnComision($inicio_rango, $fin_rango, $anio, $tipo, $numero, $cuerpo, $alcance, $orden_giro)
	{
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
		//fputs(fopen("informes_calcularDiasEnComision_Giro_".$anio."_".$tipo."_".$numero.".txt",'w'),print_r($informes, true));
		
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
			
			//fputs(fopen("vector_rango_de_dias_con_ceros_calcularDiasEnComision.txt", 'w'), print_r($this->vector_rango_de_dias, true));
			
			// SE SUMAN LOS DIAS DONDE NO ESTE PEDIDO NINGÚN INFORME
			$dias = $this->sumarDias();
		}
		else
		{
			$dias = $this->obtenerDiferenciaFechasEnDias($fin_rango, $inicio_rango);
		}
		
		return $dias;
	}
	
	public function obtenerDiferenciaFechasEnDias($fecha_listado, $fecha_en_comision) 
	{
		//fputs(fopen("fecha_listado__obtenerDiferenciaFechasEnDias.txt", 'w'), print_r($fecha_listado, true));
		//fputs(fopen("fecha_en_comision__obtenerDiferenciaFechasEnDias.txt", 'w'), print_r($fecha_en_comision, true));
		
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
	
	// SE CARGA UN VECTOR DE FECHAS CON UNOS
	public function cargarVectorRangoFechas($fechaIn, $fechaOut, $cantidad_dias_del_mes)
	{
		//fputs(fopen("fechaIn_cargarVectorRangoFechas.txt", 'w'), print_r($fechaIn, true));
		//fputs(fopen("fechaOut_cargarVectorRangoFechas.txt", 'w'), print_r($fechaOut, true));
		//fputs(fopen("cantidad_dias_del_mes_cargarVectorRangoFechas.txt", 'w'), print_r($cantidad_dias_del_mes, true));
		
		// $cantidad_dias_del_mes: CANTIDAD DE DIAS DEL MES DE LA $fechaIn
		// $fechaIn[0] Y $fechaOut[0] ES EL DIA
		// $fechaIn[1] Y $fechaOut[1] ES EL MES
		// $fechaIn[2] Y $fechaOut[2] ES EL AÑO
		
		//fputs(fopen("cantidad_dias_del_mes_inicial_cargarVectorRangoFechas.txt", 'w'), print_r($cantidad_dias_del_mes, true));
		
		//  SE CONCATENA EL AÑO, MES Y DIA DE LA FECHA HASTA PARA COMPARAR
		$fecha_hasta = $fechaOut[2].$fechaOut[1].$fechaOut[0];
		//fputs(fopen("fecha_hasta_cargarVectorRangoFechas.txt", 'w'), print_r($fecha_hasta, true));

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
					//fputs(fopen("fechaIn_MAYOR_DIC_cargarVectorRangoFechas".$i.".txt", 'w'), print_r($fechaIn, true));
					$fechaIn[2]++;// SE PASA AL AÑO SIGUIENTE
					$fechaIn[1] = 1; // MES 1, ENERO
				}
				
				// CANTIDAD DE DIAS DEL MES SIGUIENTE
				$cantidad_dias_del_mes = $this->meses($fechaIn);
			}

			$i++;// SIGUIENTE POSICION
		}

		//fputs(fopen("vector_rango_de_dias_cargarVectorRangoFechas.txt", 'w'), print_r($this->vector_rango_de_dias, true));
	}
	 
	// SE CARGA CON CEROS LOS DIAS DEL PERIODO QUE ESTA PEDIDO CADA INFORME DEL GIRO
	public function cargarCeros($fecha_pedido, $fecha_vuelta)
	{
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
	public function sumarDias()
	{
		$suma = 0;
		$cantidad = count($this->vector_rango_de_dias);
		for ( $i=0; $i < $cantidad; $i++ )
		{
			if ( $this->vector_rango_de_dias[$i]['valor'] == 1 )
			{
				$suma++;
			}
		} 
		/* 24/04/2013 */
		// SE ELIMINA EL VECTOR LUEGO DE REALIZAR LA SUMA
		$this->vector_rango_de_dias = null;
		
		return $suma;
	}
	
	// DEVUELVE 29 SI ES BISIESTO, SINO 28
	public function anioBisiesto($anio)
	{
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
	public function meses($fecha)
	{
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
	
}
?>

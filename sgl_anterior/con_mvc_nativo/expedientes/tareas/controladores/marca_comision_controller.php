<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//Incluye el modelo que corresponde
require 'modelos/marca_comision.php';
require '../abms/modelos/informes.php';

//Incluye la vista que corresponde
require 'vistas/marca_comision.php';
require 'vistas/cargar_proyecto.php';

class marca_comision_controller extends ControllerBase
{
	private $filtro = Array();
	private $comisiones_a_marcar = Array();

	// VECTOR CON EL RANGO DE DIAS
	private $vector_rango_de_dias = Array();

	public function esFechaValida($fecha)
	{
	    if ( $fecha != null )
	    {
		    $fec_partes = explode("/",$fecha);
		    $mes   = $fec_partes[1];
		    $dia   = $fec_partes[0];
		    $anio  = $fec_partes[2];
		    return checkdate( $mes, $dia, $anio );
	    }
	    else
	    {
		    return false;
	    }
	}

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
			}else{
				return '';
			}
	    }else{
			return '';
	    }
	}

	public function listar($mensaje = '')
	{
	    //Se crea una instancia del modelo
	    $modelo = new marcaComisionModel();

	    $this->filtro['enviado'] = Validador::validarParametro('enviado');

	    //SE FILTRA POR Fecha desde, Fecha hasta Y Fecha de Listado
	    if ( $this->esFechaValida(Validador::validarParametro('mstmc_fecha_desde')) )
	    {
		    $this->filtro['mstmc_fecha_desde'] = $modelo->formatearFechaMySQL(Validador::validarParametro('mstmc_fecha_desde'));
	    }
	    if ( $this->esFechaValida(Validador::validarParametro('mstmc_fecha_hasta')) )
	    {
		    $this->filtro['mstmc_fecha_hasta'] = $modelo->formatearFechaMySQL(Validador::validarParametro('mstmc_fecha_hasta'));
	    }
	    if ( $this->esFechaValida(Validador::validarParametro('mstmc_fecha_de_listado')) )
	    {
		    $this->filtro['mstmc_fecha_de_listado'] = Validador::validarParametro('mstmc_fecha_de_listado');
	    }
	    //SE FILTRA POR Comision
	    $comision = explode("-", Validador::validarParametro('mstmc_comision'));
	    $this->filtro['mstmc_comision_tipo'] = $comision[0];
	    $this->filtro['mstmc_comision_codigo'] = $comision[1];

	    $modelo->setFiltro($this->filtro);

	    // SE OBTIENE EL LISTADO DE EXPEDIENTES/NOTAS EN COMISION
	    $listado = $modelo->listar();

		// 09/05/2012	PARA AGREGAR AL LISTADO LOS DIAS EN COMISION DE CADA REGISTRO
		$cantidad = count($listado);
		// POR CADA REGISTRO
		for ($i=0; $i < $cantidad; $i++)
		{
			$registro = &$listado[$i];

			// SE OBTIENE EL ULTIMO GIRO
			$ultimo_giro = $modelo->obtenerUltimoGiro($registro['anio'], $registro['tipo'], $registro['numero'], $registro['cuerpo'], $registro['alcance']);

			// SI POSEE FECHA DE ENTRADA Y NO FECHA DE SALIDA, SE OBTIENEN LOS DIAS DEL EXPEDIENTE EN COMISION
			if ( $ultimo_giro[0]['fecha_entrada_giro'] > '0000-00-00' && ( $ultimo_giro[0]['fecha_salida_giro'] == '' || $ultimo_giro[0]['fecha_salida_giro'] == '0000-00-00' ) )
			{
				// SE AGREGA A CADA REGISTRO LOS DIAS EN COMISION
				$listado[$i]['dias'] = $this->calcularDiasEnComision($this->formatearFecha($ultimo_giro[0]['fecha_entrada_giro']), $this->filtro['mstmc_fecha_de_listado'], $registro['anio'], $registro['tipo'], $registro['numero'], $registro['cuerpo'], $registro['alcance'], $ultimo_giro[0]['orden_giro']);
			}
		}

	    //Se le pide al modelo todas las comisiones
	    $listadoComisiones = $modelo->obtenerComisiones();

	    $vista = new VistaMarcaComision();
	    //se muestra el listado
	    $vista->listar($listado, $listadoComisiones, $this->filtro);
	}

	public function guardar()
	{
	    //Se crea una instancia del modelo
	    $modelo = new marcaComisionModel();

		$clave_expediente = Array('anio' => Validador::validarParametro('mc_f_anio'),
								  'tipo' => Validador::validarParametro('mc_f_tipo'),
								  'numero' => Validador::validarParametro('mc_f_numero'),
								  'cuerpo' => Validador::validarParametro('mc_f_cuerpo'),
								  'alcance' => Validador::validarParametro('mc_f_alcance')
								 );

	    // SE RECORRE EL LISTADO
	    for ( $i = 0; $i < Validador::validarParametro('cantidad'); $i++ )
	    {
	    	// SE ACTUALIZARAN SOLO AQUELLOS QUE HAYAN CAMBIADO SU marca_comision
			if (Validador::validarParametro('marca_modificada'.$i) == 'true')
			{
				$partes_clave_expediente = explode('-', Validador::validarParametro('clave_expediente'.$i));

				$this->comisiones_a_marcar['anio'] = $partes_clave_expediente[0];
				$this->comisiones_a_marcar['tipo'] = $partes_clave_expediente[1];
				$this->comisiones_a_marcar['numero'] = $partes_clave_expediente[2];
				$this->comisiones_a_marcar['cuerpo'] = $partes_clave_expediente[3];
				$this->comisiones_a_marcar['alcance'] = $partes_clave_expediente[4];

				$this->comisiones_a_marcar['marca_comision'] = Validador::validarParametro('i_tipo_marca'.$i);

				if ( !$modelo->marcarComisiones($this->comisiones_a_marcar) )
				{
					$mensaje = "No se han guardado los cambios.";
					$tipo_mensaje = 2;
					break;
				}
			}
	    }

	    if ( $tipo_mensaje != 2 )
	    {
			$mensaje = "Las Comisiones se han marcado satisfactoriamente.";
			$tipo_mensaje = 1;
	    }

	    // 03/04/2019 XXXX
	    $_SESSION['mensaje']      = $mensaje;
		$_SESSION['tipo_mensaje'] = $tipo_mensaje;
		// Vuelve en el lugar de la grilla del expediente que se estaba visualizando (el del buscador)
		$this->volverAlInicio($clave_expediente);
	}

	public function limpiar()
	{
	    //Se crea una instancia del modelo
	    $modelo = new marcaComisionModel();

		$this->filtro['enviado'] = Validador::validarParametro('enviado');

	    //SE FILTRA POR Fecha desde, Fecha hasta Y Fecha de Listado
	    if ( $this->esFechaValida(Validador::validarParametro('mstmc_fecha_desde')) )
	    {
		    $this->filtro['mstmc_fecha_desde'] = $modelo->formatearFechaMySQL(Validador::validarParametro('mstmc_fecha_desde'));
	    }
	    if ( $this->esFechaValida(Validador::validarParametro('mstmc_fecha_hasta')) )
	    {
		    $this->filtro['mstmc_fecha_hasta'] = $modelo->formatearFechaMySQL(Validador::validarParametro('mstmc_fecha_hasta'));
	    }
	    if ( $this->esFechaValida(Validador::validarParametro('mstmc_fecha_de_listado')) )
	    {
		    $this->filtro['mstmc_fecha_de_listado'] = Validador::validarParametro('mstmc_fecha_de_listado');//$modelo->formatearFechaMySQL(
	    }

	    //SE FILTRA POR Comision
	    $comision = explode("-", Validador::validarParametro('mstmc_comision'));
	    $this->filtro['mstmc_comision_tipo'] = $comision[0];
	    $this->filtro['mstmc_comision_codigo'] = $comision[1];

	    $modelo->setFiltro($this->filtro);

	    if ( $modelo->limpiar() )
	    {
			$mensaje = "Se han limpiado las marcas satisfactoriamente.";
			$tipo_mensaje = 1;
		}
		else
		{
			$mensaje = "No se han limpiado las marcas.";
			$tipo_mensaje = 2;
		}

		// SE OBTIENE EL LISTADO MODIFICADO PARA MOSTRAR
	    $listado = $modelo->listar();

		// 09/05/2012	PARA AGREGAR AL LISTADO LOS DIAS EN COMISION DE CADA REGISTRO
		$cantidad = count($listado);
		// POR CADA REGISTRO
		for ($i=0; $i < $cantidad; $i++)
		{
			$registro = &$listado[$i];

			// SE OBTIENE EL ULTIMO GIRO
			$ultimo_giro = $modelo->obtenerUltimoGiro($registro['anio'], $registro['tipo'], $registro['numero'], $registro['cuerpo'], $registro['alcance']);

			// SI POSEE FECHA DE ENTRADA Y NO FECHA DE SALIDA, SE OBTIENEN LOS DIAS DEL EXPEDIENTE EN COMISION
			if ( $ultimo_giro[0]['fecha_entrada_giro'] > '0000-00-00' && ( $ultimo_giro[0]['fecha_salida_giro'] == '' || $ultimo_giro[0]['fecha_salida_giro'] == '0000-00-00' ) )
			{
				// SE AGREGA A CADA REGISTRO LOS DIAS EN COMISION
				$listado[$i]['dias'] = $this->calcularDiasEnComision($this->formatearFecha($ultimo_giro[0]['fecha_entrada_giro']), $this->filtro['mstmc_fecha_de_listado'], $registro['anio'], $registro['tipo'], $registro['numero'], $registro['cuerpo'], $registro['alcance'], $ultimo_giro[0]['orden_giro']);
			}
		}

	    // Se le pide al modelo todas las comisiones
	    $listadoComisiones = $modelo->obtenerComisiones();

	    $vista = new VistaMarcaComision();
	    //se muestra el listado
	    $vista->listar($listado, $listadoComisiones, $this->filtro);
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

		// SI POSEE INFORMES
		if ( $informes[0]['anio'] )
		{
			$fechaEntrada = explode("/", $inicio_rango);
			$fechaSalida = explode("/", $fin_rango);

			// SE CARGA UN VECTOR CON EL RANGO DE FECHAS
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

	public function obtenerDiferenciaFechasEnDias($fecha_listado, $fecha_en_comision)
	{
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

	// SE CARGA UN VECTOR DE FECHAS CON UNOS
	public function cargarVectorRangoFechas($fechaIn, $fechaOut, $longitud)
	{
		// $longitud: CANTIDAD DE DIAS DEL MES DE LA $fechaIn
		// $fechaIn[0] Y $fechaOut[0] ES EL DIA
		// $fechaIn[1] Y $fechaOut[1] ES EL MES
		// $fechaIn[2] Y $fechaOut[2] ES EL AÑO

		$i = 0;// POSICION DEL VECTOR
		/**/
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

			// SE INICIALIZA EL VECTOR EN 1 CON CADA DIA DEL RANGO, CON FORMATO DE FECHA yyyy-mm-dd PARA COMPARAR
			$this->vector_rango_de_dias[$i]['fecha'] = $fechaIn[2]."-".$fechaIn[1]."-".$fechaIn[0];
			$this->vector_rango_de_dias[$i]['valor'] = 1;

			// SI SE LLEGO AL FINAL DEL RANGO, TERMINA DE CARGAR EL VECTOR
			if ($fechaIn[0] == $fechaOut[0] && $fechaIn[1] == $fechaOut[1] && $fechaIn[2] == $fechaOut[2])
			{
				break;
			}

			// SI NO SE LLEGÓ AL ULTIMO DIA DEL MES (28, 29, 30 o 31)
			if ( $fechaIn[0] < $longitud)
			{
				$fechaIn[0]++;// SE INCREMENTA EL DIA
			}
			else // SI ES EL ULTIMO DIA
			{
				$fechaIn[0] = 0;
				$fechaIn[0]++;// COMIENZA EN EL DIA 1
				$fechaIn[1]++;// SE PASA AL MES SIGUIENTE
				$longitud = $this->meses($fechaIn);// CANTIDAD DE DIAS DEL MES SIGUIENTE

				// SI EL MES ES MAYOR A DICIEMBRE, COMIENZA EL SIGUIENTE AÑO
				if ( $fechaIn[1] > 12 )
				{
					$fechaIn[2]++;// AÑO SIGUIENTE
					$fechaIn[1] = 1; // MES 1, ENERO
				}
			}

			$i++;// SIGUIENTE POSICION
		}

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
		/* 22/07/2013 */
		// SE ELIMINA EL VECTOR LUEGO DE REALIZAR LA SUMA
		$this->vector_rango_de_dias = null;

		return $suma;
	}

	/**
	 * Para volver al expediente que se estaba visualizando (el del buscador)
	 */
	public function volverAlInicio($clave_expediente) {
		// URL para volver al expediente que se estaba visualizando (el del buscador)
		$url = "index.php?anio=".$clave_expediente['anio']."&tipo=".$clave_expediente['tipo']."&numero=".$clave_expediente['numero']."&cuerpo=".$clave_expediente['cuerpo']."&alcance=".$clave_expediente['alcance']."&sentido=anterior";
	?>
		<script type="text/javascript">
			location.href = '<?php echo $url; ?>';
		</script>
	<?php
	}

}
?>

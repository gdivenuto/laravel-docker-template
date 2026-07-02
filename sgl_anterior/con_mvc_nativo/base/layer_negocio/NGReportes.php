<?php
/**
 * Capa de negocio de Reportes.
 */

class NGReportes extends NGBaseClass {

	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************

	// Vector con el rango de días, para el cálculo de días en comisión
	protected $vector_rango_de_dias = Array();

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * NGReportes: Obtiene una coleccion de instancias de tipo Expediente en base a diferentes criterios de selección, asociados a la búsqueda avanzada de expedientes.
	 * @param  array|string     $pfecha_entrada_expe   [description]
	 * @param  array|string     $pfecha_promulga       [description]
	 * @param  array|string     $pfecha_sancion        [description]
	 * @param  integer     $pid_codcategoria      [description]
	 * @param  string     $piniciador_tipo       [description]
	 * @param  string     $piniciador_codigo     [description]
	 * @param  string     $pcaratula             [description]
	 * @param  integer     $pid_codtema           [description]
	 * @param  string     $pautor_tipo           [description]
	 * @param  string     $pautor_codigo         [description]
	 * @param  bool     $ptratamiento_comision [description]
	 * @param  string     $pcomision_codigo      [description]
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerExpedientesAvanzado(
		// Parametros
		$pfecha_entrada_expe = null,
		$pfecha_promulga = null,
		$pfecha_sancion = null,
		$pid_codcategoria = null,
		$piniciador_tipo = null,
		$piniciador_codigo = null,
		$pcaratula = null,
		$pid_codtema = null,
		$pautor_tipo = null,
		$pautor_codigo = null,
		$ptratamiento_comision = null, /* boolean */
		$pcomision_codigo = null,
		$pid_codestado = null,
		$pInstanciasCompletas = false,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {

		DB::getInstanceDBReportes()->conectar();

		// La búsqueda por fechas es mutuamente excluyente, pero alguna debe existir.
		// (Comparo por el caso válido e invierto la condicion)
		if (!(!is_null($pfecha_entrada_expe) xor !is_null($pfecha_promulga) xor !is_null($pfecha_sancion))) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesAvanzado: %s", get_class($this), "Los filtros por fecha son par&aacute;metros mutuamente excluyentes, y al menos uno es obligatorio."));
		}

		// Comision y código de estado son mutuamente excluyente.
		// Puede existir al menos uno, o ambos nulos.
		if (!is_null($pcomision_codigo) && !is_null($pid_codestado)) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesAvanzado: %s", get_class($this), "Los filtros por comisi&oacute;n y estado son par&aacute;metros mutuamente excluyentes."));
		}

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBReportes()->obtenerExpedientesAvanzado(
				$pfecha_entrada_expe, $pfecha_promulga, $pfecha_sancion,
				$pid_codcategoria, $piniciador_tipo, $piniciador_codigo, $pcaratula,
				$pid_codtema, $pautor_tipo, $pautor_codigo, $ptratamiento_comision,
				$pcomision_codigo, $pid_codestado,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesAvanzado: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Expediente
		$resultado = $this->arrayResultToInstance($filas, 'Expediente');

		// Obtengo el resto de la información si se solicita la instancia completa
		if ($pInstanciasCompletas) {
			foreach ($resultado as $exp) {
				$exp = NG::expedientes()->completarInstanciaExpediente($exp);

				// 21/09/2020 XXXX
				// Sólo los Perfiles 1 y 2 pueden ver los extractos cuando el Tema es el 36 (OFICIO JUDICIAL)
				// Si el perfil es 3 o 4 los extractos NO se muestran (se limpian)
				// --------------------------------------------------------------------------------------------
				if (isset($_SESSION['perfil2']) && ($_SESSION['perfil2'] == 3 || $_SESSION['perfil2'] == 4)) {
					$tiene_tema_36 = 0;
					// Se recorren los Temas del expediente/nota
					foreach ($exp->getTemas() as $item) {
						// Si posee el Tema 36
						if ($item->id_codtema == 36) {
							$tiene_tema_36 = 1; // Se marca que lo tiene
							break;
						}
					}
					// Si posee un Tema 36
					if ($tiene_tema_36) {
						// Se "limpian" los Extractos de cada Proyecto del expediente
						foreach ($exp->getProyectos() as $item) {
							$item->extracto = '';
						}
					}
				}
			}
		}

		DB::getInstanceDBReportes()->desconectar();

		return $resultado;
	}

	/**
	 * NGReportes: Obtiene la cantidad de instancias correspondientes a la clase Expediente en base a diferentes criterios de selección, asociados a la búsqueda avanzada de expedientes.
	 * @param  array|string     $pfecha_entrada_expe   [description]
	 * @param  array|string     $pfecha_promulga       [description]
	 * @param  array|string     $pfecha_sancion        [description]
	 * @param  integer     $pid_codcategoria      [description]
	 * @param  string     $piniciador_tipo       [description]
	 * @param  string     $piniciador_codigo     [description]
	 * @param  string     $pcaratula             [description]
	 * @param  integer     $pid_codtema           [description]
	 * @param  string     $pautor_tipo           [description]
	 * @param  string     $pautor_codigo         [description]
	 * @param  bool     $ptratamiento_comision [description]
	 * @param  string     $pcomision_codigo      [description]
	 * @return integer
	 */
	public function obtenerExpedientesAvanzadoCantidad(
		// Parametros
		$pfecha_entrada_expe = null,
		$pfecha_promulga = null,
		$pfecha_sancion = null,
		$pid_codcategoria = null,
		$piniciador_tipo = null,
		$piniciador_codigo = null,
		$pcaratula = null,
		$pid_codtema = null,
		$pautor_tipo = null,
		$pautor_codigo = null,
		$ptratamiento_comision = null, /* boolean */
		$pcomision_codigo = null,
		$pid_codestado = null) {
		DB::getInstanceDBReportes()->conectar();

		// La búsqueda por fechas es mutuamente excluyente, pero alguna debe existir.
		// (Comparo por el caso válido e invierto la condicion)
		if (!(!is_null($pfecha_entrada_expe) xor !is_null($pfecha_promulga) xor !is_null($pfecha_sancion))) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesAvanzadoCantidad: %s", get_class($this), "Los filtros por fecha son par&aacute;metros mutuamente excluyentes, y al menos uno es obligatorio."));
		}

		// Comision y código de estado son mutuamente excluyente.
		// Puede existir a menos uno, o ambos nulos.
		if (!is_null($pcomision_codigo) && !is_null($pid_codestado)) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesAvanzadoCantidad: %s", get_class($this), "Los filtros por comisi&oacute;n y estado son par&aacute;metros mutuamente excluyentes."));
		}

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBReportes()->obtenerExpedientesAvanzadoCantidad(
				$pfecha_entrada_expe, $pfecha_promulga, $pfecha_sancion,
				$pid_codcategoria, $piniciador_tipo, $piniciador_codigo, $pcaratula,
				$pid_codtema, $pautor_tipo, $pautor_codigo, $ptratamiento_comision,
				$pcomision_codigo, $pid_codestado);
		} catch (Exception $e) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesAvanzadoCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBReportes()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGReportes: Obtiene una coleccion de elementos tipo Expediente en base a un determinado Antecedente.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio_a
	 * @param  string (PK) tipo_a
	 * @param  float (PK) numero_a
	 * @param  string (PK) digito_a
	 * @param  integer (PK) cuerpo_a
	 * @param  integer (PK) alcance_a
	 * @param  integer (PK) cuerpoalcance_a
	 * @param  integer (PK) anexoalcance_a
	 * @param  integer (PK) cuerpoanexoalcance_a
	 * @param  integer (PK) anexo_a
	 * @param  integer (PK) cuerpoanexo_a
	 * @param  boolean pInstanciasCompletas Devuelve las instancias de expediente completas en vez de solo la cabecera.
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<Antecedente>
	 */
	public function obtenerExpedientesPorAntecedente(
		// Parametros
		$panio_a = null,
		$ptipo_a = null,
		$pnumero_a = null,
		$pdigito_a = null,
		$pcuerpo_a = null,
		$palcance_a = null,
		$pcuerpoalcance_a = null,
		$panexoalcance_a = null,
		$pcuerpoanexoalcance_a = null,
		$panexo_a = null,
		$pcuerpoanexo_a = null,
		$pInstanciasCompletas = false,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		DB::getInstanceDBReportes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBReportes()->obtenerExpedientesPorAntecedente(
				$panio_a, $ptipo_a, $pnumero_a, $pdigito_a, $pcuerpo_a, $palcance_a,
				$pcuerpoalcance_a, $panexoalcance_a, $pcuerpoanexoalcance_a, $panexo_a, $pcuerpoanexo_a,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesPorAntecedente: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Expediente
		$resultado = $this->arrayResultToInstance($filas, 'Expediente');

		// Obtengo el resto de la información si se solicita la instancia completa
		if ($pInstanciasCompletas) {
			foreach ($resultado as $exp) {
				$exp = NG::expedientes()->completarInstanciaExpediente($exp);

				// 21/09/2020 XXXX
				// Sólo los Perfiles 1 y 2 pueden ver los extractos cuando el Tema es el 36 (OFICIO JUDICIAL)
				// Si el perfil es 3 o 4 los extractos NO se muestran (se limpian)
				// --------------------------------------------------------------------------------------------
				if (isset($_SESSION['perfil2']) && ($_SESSION['perfil2'] == 3 || $_SESSION['perfil2'] == 4)) {
					$tiene_tema_36 = 0;
					// Se recorren los Temas del expediente/nota
					foreach ($exp->getTemas() as $item) {
						// Si posee el Tema 36
						if ($item->id_codtema == 36) {
							$tiene_tema_36 = 1; // Se marca que lo tiene
							break;
						}
					}
					// Si posee un Tema 36
					if ($tiene_tema_36) {
						// Se "limpian" los Extractos de cada Proyecto del expediente
						foreach ($exp->getProyectos() as $item) {
							$item->extracto = '';
						}
					}
				}
			}
		}

		DB::getInstanceDBReportes()->desconectar();

		return $resultado;
	}

	/**
	 * NGReportes: Obtiene la cantidad de elementos de un filtro a obtenerExpedientesPorAntecedente()
	 *
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) anio_a
	 * @param  string (PK) tipo_a
	 * @param  float (PK) numero_a
	 * @param  string (PK) digito_a
	 * @param  integer (PK) cuerpo_a
	 * @param  integer (PK) alcance_a
	 * @param  integer (PK) cuerpoalcance_a
	 * @param  integer (PK) anexoalcance_a
	 * @param  integer (PK) cuerpoanexoalcance_a
	 * @param  integer (PK) anexo_a
	 * @param  integer (PK) cuerpoanexo_a
	 * @return integer
	 */
	public function obtenerExpedientesPorAntecedenteCantidad(
		// Parametros
		$panio_a = null,
		$ptipo_a = null,
		$pnumero_a = null,
		$pdigito_a = null,
		$pcuerpo_a = null,
		$palcance_a = null,
		$pcuerpoalcance_a = null,
		$panexoalcance_a = null,
		$pcuerpoanexoalcance_a = null,
		$panexo_a = null,
		$pcuerpoanexo_a = null) {
		DB::getInstanceDBReportes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBReportes()->obtenerExpedientesPorAntecedenteCantidad(
				$panio_a, $ptipo_a, $pnumero_a, $pdigito_a, $pcuerpo_a, $palcance_a,
				$pcuerpoalcance_a, $panexoalcance_a, $pcuerpoanexoalcance_a, $panexo_a, $pcuerpoanexo_a);
		} catch (Exception $e) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesPorAntecedenteCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBReportes()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * Se calcula el número de días que permanece en comisión, salteando los días que se encuentra prestado
	 * @param  string  $inicio_rango Fecha de entrada del último Giro
	 * @param  string  $fin_rango    Fecha del listado ingresada en el criterio de búsqueda
	 * @param  integer $anio
	 * @param  string  $tipo
	 * @param  integer $numero
	 * @param  integer $cuerpo
	 * @param  integer $alcance
	 * @param  integer $orden_giro   Orden del Giro
	 * @return integer $dias       	 Número de días en comisión
	 */
	public function calcularDiasEnComision(
		$inicio_rango,
		$fin_rango,
		$anio,
		$tipo,
		$numero,
		$cuerpo,
		$alcance,
		$orden_giro) {
		// Se obtienen todos los informes del giro
		$informes = NG::expedientes()->obtenerInformes(
			$anio,
			$tipo,
			$numero,
			$cuerpo,
			$alcance,
			$orden_giro);

		// Si NO posee Informes
		if (is_null($informes))
		// Se obtiene la diferencia en días, entre la fecha de entrada del último giro y la fecha elegida en el listado
		{
			$dias = $this->obtenerDiferenciaFechasEnDias($fin_rango, $inicio_rango);
		} else {
			$fechaEntrada = explode("/", $inicio_rango);
			$fechaSalida = explode("/", $fin_rango);

			// Se carga el vector con el rango de fechas
			$this->cargarVectorRangoFechas($fechaEntrada, $fechaSalida, $this->meses($fechaEntrada));

			// Por cada informe
			foreach ($informes as $informe)
			// Se cargan los ceros en el vector de rango de fechas
			{
				$this->cargarCeros($informe->fecha_pedido_informe, $informe->fecha_vuelta_informe);
			}

			// Se suman los días donde NO esté pedido ningún informe
			$dias = $this->sumarDias();
		}

		return $dias;
	}

	/**
	 * Devuelve 29 si es bisiesto, sino 28
	 * @param  integer $anio Año para corroborar si es bisiesto o no
	 * @return integer Ultimo día de Febrero de dicho año
	 */
	public function anioBisiesto($anio) {
		// Un año es bisiesto si es divisible entre 4, excepto aquellos divisibles entre 100 pero no entre 400.
		return (($anio % 4 == 0 && $anio % 100 != 0) || $anio % 400 == 0) ? 29 : 28;
	}

	/**
	 * Devuelve la cantidad de días del mes respectivo a la fecha
	 * @param  string $fecha [description]
	 * @return [type]        [description]
	 */
	public function meses($fecha) {
		// Si el mes es Febrero
		if ($fecha[1] == 2) {
			return $this->anioBisiesto($fecha[2]);
		} elseif ($fecha[1] == 1 || $fecha[1] == 3 || $fecha[1] == 5 || $fecha[1] == 7 || $fecha[1] == 8 || $fecha[1] == 10 || $fecha[1] == 12) {
			return 31;
		} else {
			return 30;
		}

	}

	/**
	 * 07/12/2021 XXXX, se agregaron verificaciones con isset
	 *
	 * Se carga un vector de fechas con 1's (unos)
	 * @param  string  $fechaDesde            	Fecha de entrada del último Giro
	 * @param  string  $fechaHasta            	Fecha de Listado
	 * @param  integer $cantidad_dias_del_mes 	Cantidad de días del mes de la $fechaDesde
	 */
	public function cargarVectorRangoFechas($fechaDesde, $fechaHasta, $cantidad_dias_del_mes) {

		//  Si tiene valor, se concatena el año, mes y día de la fecha Hasta para comparar
		if (isset($fechaHasta[0]) && isset($fechaHasta[1]) && isset($fechaHasta[2])) {
			$fecha_hasta = $fechaHasta[2] . $fechaHasta[1] . $fechaHasta[0];
		} else {
			$fecha_hasta = null;
		}

		if (!is_null($fecha_hasta)) {
			$dia_fecha_desde = $fechaDesde[0];
			$mes_fecha_desde = $fechaDesde[1];
			$anio_fecha_desde = $fechaDesde[2];

			$i = 0; // Posición del vector
			while (true) {
				// Para completar el mes si no lo está y es menor a diez
				if ($dia_fecha_desde < 10) {
					$dia_fecha_desde = substr('0' . $dia_fecha_desde, -2);
				}

				// Para completar el mes si no lo está y es menor a diez
				if ($mes_fecha_desde < 10) {
					$mes_fecha_desde = substr('0' . $mes_fecha_desde, -2);
				}

				// Se concatena el año, mes y día de la fecha desde, para compararla con la fecha tope
				$fecha_desde = $anio_fecha_desde . $mes_fecha_desde . $dia_fecha_desde;

				// Si la fecha hasta está seteada y se llegó al final del rango, termina de cargar el vector
				if (isset($fecha_hasta) && $fecha_desde > $fecha_hasta) {
					break;
				}

				// Se inicializa el vector en 1 con cada día del rango, con formato de fecha yyyy-mm-dd para comparar
				$this->vector_rango_de_dias[$i]['fecha'] = $anio_fecha_desde . "-" . $mes_fecha_desde . "-" . $dia_fecha_desde;
				$this->vector_rango_de_dias[$i]['valor'] = 1;

				// Si no se llegó al último día del mes (28, 29, 30 o 31)
				if ($dia_fecha_desde < $cantidad_dias_del_mes) {
					$dia_fecha_desde++;
				}
				// Se incrementa el día
				else {
					// Si es el último día
					$dia_fecha_desde = 1; // Comienza en el día 1
					$mes_fecha_desde++; // Se pasa al mes siguiente

					// Si el mes es mayor a Diciembre, comienza el siguiente año
					if ($mes_fecha_desde > 12) {
						$anio_fecha_desde++; // Se pasa al año siguiente
						$mes_fecha_desde = 1; // Mes 1, Enero
					}

					// Cantidad de días del mes siguiente
					$cantidad_dias_del_mes = $this->meses($fechaDesde);
				}

				$i++; // Siguiente posición
			}
		}
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
		$timestamp_fin = mktime(0, 0, 0, $mes_fin, $dia_fin, $anio_fin);
		$timestamp_inicio = mktime(0, 0, 0, $mes_inicio, $dia_inicio, $anio_inicio);

		// SE RESTA A UNA FECHA LA OTRA
		$segundos_diferencia = $timestamp_fin - $timestamp_inicio;

		// SE CONVIERTEN LOS SEGUNDOS EN DIAS
		$dias_diferencia = $segundos_diferencia / (60 * 60 * 24);

		// SE OBTIENE EL VALOR ABSOLUTO DE LOS DIAS (SE QUITA UN POSIBLE SIGNO NEGATIVO)
		$dias_diferencia = abs($dias_diferencia);

		// SE QUITAN LOS DECIMALES A LOS DIAS DE DIFERENCIA, EN CASO DE EXISTIR
		$dias_diferencia = floor($dias_diferencia);

		if ($dias_diferencia < 0) {
			$dias_diferencia = 0;
		}

		return $dias_diferencia;
	}

	/**
	 * 07/12/2021 XXXX, se agregaron verificaciones con isset
	 *
	 * Se carga con ceros los días del período que está pedido cada informe del giro
	 * @param  string $fecha_pedido Fecha del pedido del informe
	 * @param  string $fecha_vuelta Fecha que se devuelve el informe
	 */
	public function cargarCeros($fecha_pedido, $fecha_vuelta) {
		$cantidad = (isset($this->vector_rango_de_dias)) ? count($this->vector_rango_de_dias) : 0;
		$con_informe = false;

		if (isset($this->vector_rango_de_dias[0])) {
			// CASO ESPECIAL: SI LA fecha_pedido ES MENOR A LA FECHA DE ENTRADA DEL GIRO
			if ($fecha_pedido < $this->vector_rango_de_dias[0]['fecha']) {
				// fecha_pedido = FECHA DE INICIO DEL RANGO
				$fecha_pedido = $this->vector_rango_de_dias[0]['fecha'];
			}

			// Se recorre el vector
			for ($i = 0; $i < $cantidad; $i++) {
				// Si concuerda la fecha_pedido con la fecha del vector
				if ($this->vector_rango_de_dias[$i]['fecha'] == $fecha_pedido) {
					// Se empieza a cargar con cero
					$this->vector_rango_de_dias[$i]['valor'] = 0;
					$con_informe = true;
				}

				// Si ya se empezó a cargar ceros
				if ($con_informe) {
					if ($fecha_vuelta == null) {
						// Se sigue cargando con ceros hasta el final del vector
						$this->vector_rango_de_dias[$i]['valor'] = 0;
					} else {
						// Si no se llegó a la fecha de vuelta
						if ($this->vector_rango_de_dias[$i]['fecha'] != $fecha_vuelta) {
							// Se sigue cargando con ceros
							$this->vector_rango_de_dias[$i]['valor'] = 0;
						} else {
							// Se carga el último cero porque llegó a la fecha de vuelta
							$this->vector_rango_de_dias[$i]['valor'] = 0;
							// Se establece que pasó el período del informe
							$con_informe = false;
						}
					}
				}
			}
		}
	}

	/**
	 * 07/12/2021 XXXX, se agregaron verificaciones con isset
	 *
	 * Se obtiene la suma de los días donde no esté pedido ningún informe (CON VALOR 1)
	 */
	public function sumarDias() {
		$suma = 0;
		$cantidad = (isset($this->vector_rango_de_dias)) ? count($this->vector_rango_de_dias) : 0;
		for ($i = 0; $i < $cantidad; $i++) {
			if ($this->vector_rango_de_dias[$i]['valor'] == 1) {
				$suma++;
			}
		}

		// Se vacía el vector luego de realizar la suma
		$this->vector_rango_de_dias = null;

		return $suma;
	}

    /**
	 * NGReportes: Obtiene una coleccion de instancias de tipo Expediente en base a diferentes criterios de selección,
	 * en una Comisión determinada.
	 * @param  string     $pfecha_desde
	 * @param  string     $pfecha_hasta
	 * @param  string     $pfecha_comision
	 * @param  string     $pcomision_codigo
	 * @param  integer    $pid_codestado
	 * @param  bool       $ptratamiento_comision
	 * @param  integer 	  $pvencidos				Sólo vencidos, cantidad de días en Comisión > 120 días
	 * @param  array|null $pOrdenColumnas 			Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer 	  $pLimiteCantidad 			Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer    $pLimiteOffset 			Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerExpedientesEnComision(
		// Parametros
		$pfecha_listado = null,
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pfecha_comision = null,
		$pcomision_codigo = null,
		$pid_codestado = null,
		$pcomisiones_elegidas_en_modal = null,
		$ptratamiento_comision = null,
		$pInstanciasCompletas = false,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		DB::getInstanceDBReportes()->conectar();

		// La búsqueda por rango de fechas debe existir.
		if (is_null($pfecha_desde) && is_null($pfecha_hasta)) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesEnComision: %s", get_class($this), "Los filtros por fecha Desde y Hasta son obligatorios."));
		}

		// Comisión y código de estado son mutuamente excluyente.
		// Puede existir al menos uno, o ambos nulos.
		if (!is_null($pcomision_codigo) && !is_null($pid_codestado)) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesEnComision: %s", get_class($this), "Los filtros por Comisi&oacute;n y Estado son par&aacute;metros mutuamente excluyentes."));
		}

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBReportes()->obtenerExpedientesEnComision(
				$pfecha_desde,
				$pfecha_hasta,
				$pfecha_comision,
				$pcomision_codigo,
				$pid_codestado,
				$pcomisiones_elegidas_en_modal,
				$ptratamiento_comision,
				$pOrdenColumnas,
				$pLimiteCantidad,
				$pLimiteOffset
			);
		} catch (Exception $e) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesEnComision: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Expediente
		$resultado = $this->arrayResultToInstance($filas, 'Expediente');

		// Obtengo el resto de la información si se solicita la instancia completa
		if ($pInstanciasCompletas) {
			foreach ($resultado as $exp) {
				$exp = NG::expedientes()->completarInstanciaExpediente($exp);

				// 21/09/2020 XXXX
				// Sólo los Perfiles 1 y 2 pueden ver los extractos cuando el Tema es el 36 (OFICIO JUDICIAL)
				// Si el perfil es 3 o 4 los extractos NO se muestran (se limpian)
				// --------------------------------------------------------------------------------------------
				if (isset($_SESSION['perfil2']) && ($_SESSION['perfil2'] == 3 || $_SESSION['perfil2'] == 4)) {
					$tiene_tema_36 = 0;
					// Se recorren los Temas del expediente/nota
					foreach ($exp->getTemas() as $item) {
						// Si posee el Tema 36
						if ($item->id_codtema == 36) {
							$tiene_tema_36 = 1; // Se marca que lo tiene
							break;
						}
					}
					// Si posee un Tema 36
					if ($tiene_tema_36) {
						// Se "limpian" los Extractos de cada Proyecto del expediente
						foreach ($exp->getProyectos() as $item) {
							$item->extracto = '';
						}
					}
				}
			}
		}

		// Si no se busca por Estado
		if (empty($pid_codestado)) {
			// Por cada expediente obtenido
			foreach ($resultado as $exp) {
				// Se obtiene el último giro del expediente
				$ultimo_giro = NG::expedientes()->obtenerUltimoGiro(
					// Parametros
					$exp->anio,
					$exp->tipo,
					$exp->numero,
					$exp->cuerpo,
					$exp->alcance,
					// Control de consulta
					array('anio desc', 'tipo desc', 'numero desc', 'cuerpo desc', 'alcance desc', 'orden_giro desc')
				);

				// Si posee por lo menos un Giro
				if (!is_null($ultimo_giro)) {
					$fecha_entrada_ultimo_giro = $this->formatearFecha($ultimo_giro->fecha_entrada_giro);
					$fecha_del_listado = $this->formatearFecha($pfecha_listado);

					// Se calcula el número de días en comisión
					$exp->ro_cantidad_dias_en_comision = $this->calcularDiasEnComision(
						$fecha_entrada_ultimo_giro,
						$fecha_del_listado,
						$exp->anio,
						$exp->tipo,
						$exp->numero,
						$exp->cuerpo,
						$exp->alcance,
						$ultimo_giro->orden_giro);
				} else {
					$exp->ro_cantidad_dias_en_comision = -1;
				}

			}
		}

		DB::getInstanceDBReportes()->desconectar();

		return $resultado;
	}

	/**
	 * NGReportes: Obtiene la cantidad de instancias correspondientes a la clase Expediente en base a diferentes criterios de selección,
	 * en una Comisión determinada.
	 * @param  string     $pfecha_desde
	 * @param  string     $pfecha_hasta
	 * @param  string     $pfecha_listado
	 * @param  string     $pfecha_comision
	 * @param  string     $pcomision_codigo
	 * @param  integer    $pid_codestado
	 * @param  bool       $ptratamiento_comision
	 * @return integer
	 */
	public function obtenerExpedientesEnComisionCantidad(
		// Parametros
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pfecha_comision = null,
		$pcomision_codigo = null,
		$pid_codestado = null,
		$pcomisiones_elegidas_en_modal = null,
		$ptratamiento_comision = null) {
		DB::getInstanceDBReportes()->conectar();

		// La búsqueda por rango de fechas debe existir.
		if (is_null($pfecha_desde) && is_null($pfecha_hasta)) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesEnComisionCantidad: %s", get_class($this), "Los filtros por fecha Desde y Hasta son obligatorios."));
		}

		// Comisión y código de estado son mutuamente excluyentes.
		// Puede existir a menos uno, o ambos nulos.
		if (!is_null($pcomision_codigo) && !is_null($pid_codestado)) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesEnComisionCantidad: %s", get_class($this), "Los filtros por Comisi&oacute;n y Estado son par&aacute;metros mutuamente excluyentes."));
		}

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBReportes()->obtenerExpedientesEnComisionCantidad(
				$pfecha_desde,
				$pfecha_hasta,
				$pfecha_comision,
				$pcomision_codigo,
				$pid_codestado,
				$pcomisiones_elegidas_en_modal,
				$ptratamiento_comision); //, $pvencidos
		} catch (Exception $e) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesEnComisionCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBReportes()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * 17/07/2020: XXXX
	 * NGReportes: Obtiene una coleccion de instancias de tipo Expediente VENCIDOS,
	 * en base a diferentes criterios de selección,
	 * en una Comisión determinada.
	 * @param  [type]     $pfecha_listado                [description]
	 * @param  [type]     $pfecha_desde                  [description]
	 * @param  [type]     $pfecha_hasta                  [description]
	 * @param  [type]     $pfecha_comision               [description]
	 * @param  [type]     $pcomision_codigo              [description]
	 * @param  [type]     $pid_codestado                 [description]
	 * @param  [type]     $pcomisiones_elegidas_en_modal [description]
	 * @param  [type]     $ptratamiento_comision         [description]
	 * @param  boolean    $pInstanciasCompletas          [description]
	 * @param  array|null $pOrdenColumnas                [description]
	 * @return [type]                                    [description]
	 */
	public function obtenerExpedientesEnComisionVencidos(
		// Parametros
		$pfecha_listado = null,
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pfecha_comision = null,
		$pcomision_codigo = null,
		$pid_codestado = null,
		$pcomisiones_elegidas_en_modal = null,
		$ptratamiento_comision = null,
		$pInstanciasCompletas = false,
		// Control de consulta
		array $pOrdenColumnas = null) {
		// Se obtienen todos los expedientes en una comisión determinada, sin utilizar el límite
		$expedientes = $this->obtenerExpedientesEnComision(
			// Parametros
			$pfecha_listado,
			$pfecha_desde,
			$pfecha_hasta,
			$pfecha_comision,
			$pcomision_codigo,
			$pid_codestado,
			$pcomisiones_elegidas_en_modal,
			$ptratamiento_comision,
			$pInstanciasCompletas,
			$pOrdenColumnas,
			null,
			null
		);

		$vencidos = Array();
		foreach ($expedientes as $exp)
		// Nos quedamos con los que superen los 120 días
		{
			if ($exp->ro_cantidad_dias_en_comision > LIMITE_EXPEDIENTES_VENCIDOS) {
				$vencidos[] = $exp;
			}
		}

		return $vencidos;
	}

	/**
	 * NGReportes: Obtiene una coleccion de instancias de tipo Expediente en base a diferentes criterios de selección,
	 * en una Comisión determinada.
	 * @param  string     $pfecha_desde
	 * @param  string     $pfecha_hasta
	 * @param  string     $pcomision_codigo
	 * @param  array|null $pOrdenColumnas 			Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer 	  $pLimiteCantidad 			Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer    $pLimiteOffset 			Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerOrdenesDelDia(
		// Parametros
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pcomision_codigo = null,
		$pInstanciasCompletas = false,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		DB::getInstanceDBReportes()->conectar();

		// La búsqueda por rango de fechas debe existir.
		if (is_null($pfecha_desde) && is_null($pfecha_hasta)) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerOrdenesDelDia: %s", get_class($this), "Los filtros por fecha Desde y Hasta son obligatorios."));
		}

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBReportes()->obtenerOrdenesDelDia(
				$pfecha_desde,
				$pfecha_hasta,
				$pcomision_codigo,
				$pOrdenColumnas,
				$pLimiteCantidad,
				$pLimiteOffset); //, $pvencidos,
		} catch (Exception $e) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerOrdenesDelDia: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Expediente
		$resultado = $this->arrayResultToInstance($filas, 'Expediente');

		// Obtengo el resto de la información si se solicita la instancia completa
		if ($pInstanciasCompletas) {
			foreach ($resultado as $exp) {
				$exp = NG::expedientes()->completarInstanciaExpediente($exp);

				// 21/09/2020 XXXX
				// Sólo los Perfiles 1 y 2 pueden ver los extractos cuando el Tema es el 36 (OFICIO JUDICIAL)
				// Si el perfil es 3 o 4 los extractos NO se muestran (se limpian)
				// --------------------------------------------------------------------------------------------
				if (isset($_SESSION['perfil2']) && ($_SESSION['perfil2'] == 3 || $_SESSION['perfil2'] == 4)) {
					$tiene_tema_36 = 0;
					// Se recorren los Temas del expediente/nota
					foreach ($exp->getTemas() as $item) {
						// Si posee el Tema 36
						if ($item->id_codtema == 36) {
							$tiene_tema_36 = 1; // Se marca que lo tiene
							break;
						}
					}
					// Si posee un Tema 36
					if ($tiene_tema_36) {
						// Se "limpian" los Extractos de cada Proyecto del expediente
						foreach ($exp->getProyectos() as $item) {
							$item->extracto = '';
						}
					}
				}
			}
		}

		DB::getInstanceDBReportes()->desconectar();

		return $resultado;
	}

	/**
	 * NGReportes: Obtiene la cantidad de instancias correspondientes a la clase Expediente en base a diferentes criterios de selección,
	 * en una Comisión determinada.
	 * @param  string     $pfecha_desde
	 * @param  string     $pfecha_hasta
	 * @param  string     $pcomision_codigo
	 * @return integer
	 */
	public function obtenerOrdenesDelDiaCantidad(
		// Parametros
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pcomision_codigo = null) {
		DB::getInstanceDBReportes()->conectar();

		// La búsqueda por rango de fechas debe existir.
		if (is_null($pfecha_desde) && is_null($pfecha_hasta)) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerOrdenesDelDiaCantidad: %s", get_class($this), "Los filtros por fecha Desde y Hasta son obligatorios."));
		}

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBReportes()->obtenerOrdenesDelDiaCantidad(
				$pfecha_desde, $pfecha_hasta, $pcomision_codigo);
		} catch (Exception $e) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerOrdenesDelDiaCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBReportes()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGReportes: Obtiene una coleccion de instancias de tipo Expediente en base a diferentes criterios de selección,
	 * en una Comisión determinada.
	 * @param  string     $pfecha_desde
	 * @param  string     $pfecha_hasta
	 * @param  string     $pcomision_codigo
	 * @param  array|null $pOrdenColumnas 	Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer 	  $pLimiteCantidad 	Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer    $pLimiteOffset 	Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerInformes(
		// Parametros
		$pfecha_listado = null,
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pcomision_codigo = null,
		$psolo_vencidos = false,
		$pInstanciasCompletas = false,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		DB::getInstanceDBReportes()->conectar();

		// La búsqueda por rango de fechas debe existir.
		if (is_null($pfecha_listado) && is_null($pfecha_desde) && is_null($pfecha_hasta)) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerInformes: %s", get_class($this), "Los filtros por fecha Informe, Desde y Hasta son obligatorios."));
		}

		// Comisión es obligatorio
		// if ( is_null($pcomision_codigo) ) {
		// 	DB::getInstanceDBReportes()->desconectar();
		// 	throw new Exception(sprintf("Error en %s.obtenerInformes: %s", get_class($this), "El filtro por Comisi&oacute;n es obligatorio."));
		// }

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBReportes()->obtenerInformes(
				$pfecha_listado,
				$pfecha_desde,
				$pfecha_hasta,
				$pcomision_codigo,
				$psolo_vencidos,
				$pOrdenColumnas,
				$pLimiteCantidad,
				$pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerInformes: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una colección de elementos tipo Informe
		$resultado = $this->arrayResultToInstance($filas, 'Informe');

		// Obtengo el resto de la información si se solicita la instancia completa
		if ($pInstanciasCompletas) {
			foreach ($resultado as $informe) {
				$informe = NG::expedientes()->completarInstanciaInforme($informe);
			}
		}

		DB::getInstanceDBReportes()->desconectar();

		return $resultado;
	}

	/**
	 * NGReportes: Obtiene la cantidad de instancias correspondientes a la clase Expediente en base a diferentes criterios de selección,
	 * en una Comisión determinada.
	 * @param  string     $pfecha_desde
	 * @param  string     $pfecha_hasta
	 * @param  string     $pcomision_codigo
	 * @return integer
	 */
	public function obtenerInformesCantidad(
		// Parametros
		$pfecha_listado = null,
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pcomision_codigo = null,
		$psolo_vencidos = false) {
		DB::getInstanceDBReportes()->conectar();

		// La búsqueda por rango de fechas debe existir.
		if (is_null($pfecha_listado) && is_null($pfecha_desde) && is_null($pfecha_hasta)) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerInformesCantidad: %s", get_class($this), "Los filtros por fecha Informe, Desde y Hasta son obligatorios."));
		}

		// Comisión es obligatorio
		// if ( is_null($pcomision_codigo) ) {
		// 	DB::getInstanceDBReportes()->desconectar();
		// 	throw new Exception(sprintf("Error en %s.obtenerInformes: %s", get_class($this), "El filtro por Comisi&oacute;n es obligatorio."));
		// }

		try {
			// Obtengo los datos desd e la capa de datos
			$cantidad_resultados = DB::getInstanceDBReportes()->obtenerInformesCantidad(
				$pfecha_listado,
				$pfecha_desde,
				$pfecha_hasta,
				$pcomision_codigo,
				$psolo_vencidos
			);
		} catch (Exception $e) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerInformesCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBReportes()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGReportes: Obtiene una coleccion de instancias de tipo Expediente en base a diferentes criterios de selección,
	 * en una Comisión determinada.
	 * @param  string     $pfecha_desde
	 * @param  string     $pfecha_hasta
	 * @param  integer    $pid_codestado
	 * @param  string     $pobservaciones_estado
	 * @param  array|null $pOrdenColumnas 	Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer 	  $pLimiteCantidad 	Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer    $pLimiteOffset 	Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerExpedientesEnPrestamo(
		// Parametros
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pid_codestado = null,
		$pobservaciones_estado = null,
		$pInstanciasCompletas = false,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		DB::getInstanceDBReportes()->conectar();

		// La búsqueda por rango de fechas debe existir.
		if (is_null($pfecha_desde) && is_null($pfecha_hasta)) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesEnPrestamo: %s", get_class($this), "Los filtros por fecha Desde y Hasta son obligatorios."));
		}

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBReportes()->obtenerExpedientesEnPrestamo(
				$pfecha_desde,
				$pfecha_hasta,
				$pid_codestado,
				$pobservaciones_estado,
				$pOrdenColumnas,
				$pLimiteCantidad,
				$pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesEnPrestamo: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Expediente
		$resultado = $this->arrayResultToInstance($filas, 'Expediente');

		// Obtengo el resto de la información si se solicita la instancia completa
		if ($pInstanciasCompletas) {
			foreach ($resultado as $exp) {
				$exp = NG::expedientes()->completarInstanciaExpediente($exp);

				// 21/09/2020 XXXX
				// Sólo los Perfiles 1 y 2 pueden ver los extractos cuando el Tema es el 36 (OFICIO JUDICIAL)
				// Si el perfil es 3 o 4 los extractos NO se muestran (se limpian)
				// --------------------------------------------------------------------------------------------
				if (isset($_SESSION['perfil2']) && ($_SESSION['perfil2'] == 3 || $_SESSION['perfil2'] == 4)) {
					$tiene_tema_36 = 0;
					// Se recorren los Temas del expediente/nota
					foreach ($exp->getTemas() as $item) {
						// Si posee el Tema 36
						if ($item->id_codtema == 36) {
							$tiene_tema_36 = 1; // Se marca que lo tiene
							break;
						}
					}
					// Si posee un Tema 36
					if ($tiene_tema_36) {
						// Se "limpian" los Extractos de cada Proyecto del expediente
						foreach ($exp->getProyectos() as $item) {
							$item->extracto = '';
						}
					}
				}
			}
		}

		DB::getInstanceDBReportes()->desconectar();

		return $resultado;
	}

	/**
	 * NGReportes: Obtiene la cantidad de instancias correspondientes a la clase Expediente en base a diferentes criterios de selección,
	 * en una Comisión determinada.
	 * @param  string     $pfecha_desde
	 * @param  string     $pfecha_hasta
	 * @param  integer    $pid_codestado
	 * @param  string     $pobservaciones_estado
	 * @return integer
	 */
	public function obtenerExpedientesEnPrestamoCantidad(
		// Parametros
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pid_codestado = null,
		$pobservaciones_estado = null) {
		DB::getInstanceDBReportes()->conectar();

		// La búsqueda por rango de fechas debe existir.
		if (is_null($pfecha_desde) && is_null($pfecha_hasta)) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesEnPrestamoCantidad: %s", get_class($this), "Los filtros por fecha Desde y Hasta son obligatorios."));
		}

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBReportes()->obtenerExpedientesEnPrestamoCantidad(
				$pfecha_desde,
				$pfecha_hasta,
				$pid_codestado,
				$pobservaciones_estado);
		} catch (Exception $e) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesEnPrestamoCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBReportes()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGReportes: Obtiene una coleccion de instancias de tipo Expediente en base a un rango de Fechas determinado.
	 * @param  string     $pfecha_desde
	 * @param  string     $pfecha_hasta
	 * @return array
	 */
	public function obtenerExpedientesSoloPorRangoFechas(
		// Parametros
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pInstanciasCompletas = false) {
		DB::getInstanceDBReportes()->conectar();

		// La búsqueda por rango de fechas debe existir.
		if (is_null($pfecha_desde) && is_null($pfecha_hasta)) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesSoloPorRangoFechas: %s", get_class($this), "Los filtros por fecha Desde y Hasta son obligatorios."));
		}

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBReportes()->obtenerExpedientesSoloPorRangoFechas(
				$pfecha_desde,
				$pfecha_hasta);
		} catch (Exception $e) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesSoloPorRangoFechas: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Expediente
		$resultado = $this->arrayResultToInstance($filas, 'Expediente');

		// Obtengo el resto de la información si se solicita la instancia completa
		if ($pInstanciasCompletas) {
			foreach ($resultado as $exp) {
				$exp = NG::expedientes()->completarInstanciaExpediente($exp);

				// 21/09/2020 XXXX
				// Sólo los Perfiles 1 y 2 pueden ver los extractos cuando el Tema es el 36 (OFICIO JUDICIAL)
				// Si el perfil es 3 o 4 los extractos NO se muestran (se limpian)
				// --------------------------------------------------------------------------------------------
				if (isset($_SESSION['perfil2']) && ($_SESSION['perfil2'] == 3 || $_SESSION['perfil2'] == 4)) {
					$tiene_tema_36 = 0;
					// Se recorren los Temas del expediente/nota
					foreach ($exp->getTemas() as $item) {
						// Si posee el Tema 36
						if ($item->id_codtema == 36) {
							$tiene_tema_36 = 1; // Se marca que lo tiene
							break;
						}
					}
					// Si posee un Tema 36
					if ($tiene_tema_36) {
						// Se "limpian" los Extractos de cada Proyecto del expediente
						foreach ($exp->getProyectos() as $item) {
							$item->extracto = '';
						}
					}
				}
				// ------------------------------------
			}
		}

		DB::getInstanceDBReportes()->desconectar();

		return $resultado;
	}

	/**
	 * NGReportes: Obtiene la cantidad de instancias correspondientes a la clase Expediente en base a un rango de Fechas determinado.
	 * @param  string     $pfecha_desde
	 * @param  string     $pfecha_hasta
	 * @return integer
	 */
	public function obtenerExpedientesSoloPorRangoFechasCantidad(
		// Parametros
		$pfecha_desde = null,
		$pfecha_hasta = null) {
		DB::getInstanceDBReportes()->conectar();

		// La búsqueda por rango de fechas debe existir.
		if (is_null($pfecha_desde) && is_null($pfecha_hasta)) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesSoloPorRangoFechasCantidad: %s", get_class($this), "Los filtros por fecha Desde y Hasta son obligatorios."));
		}

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBReportes()->obtenerExpedientesSoloPorRangoFechasCantidad(
				$pfecha_desde,
				$pfecha_hasta);
		} catch (Exception $e) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesSoloPorRangoFechasCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBReportes()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGReportes: Obtiene una coleccion de instancias de tipo Expediente en base a diferentes criterios de selección
	 * @param  string     $pfecha_desde
	 * @param  string     $pfecha_hasta
	 * @param  string     $pfecha_comision
	 * @param  string     $pcomision_codigo
	 * @param  integer    $pid_codestado
	 * @param  bool       $ptratamiento_comision
	 * @param  integer 	  $pvencidos				Sólo vencidos, cantidad de días en Comisión > 120 días
	 * @param  array|null $pOrdenColumnas 			Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer 	  $pLimiteCantidad 			Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer    $pLimiteOffset 			Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerSoloExpedientes(
		// Parametros
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pfecha_comision = null,
		$pcomision_codigo = null,
		$pid_codestado = null,
		$pcomisiones_elegidas_en_modal = null,
		$ptratamiento_comision = null,
		$pInstanciasCompletas = false,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		DB::getInstanceDBReportes()->conectar();

		// La búsqueda por rango de fechas debe existir.
		if (is_null($pfecha_desde) && is_null($pfecha_hasta)) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerSoloExpedientes: %s", get_class($this), "Los filtros por fecha Desde y Hasta son obligatorios."));
		}

		// Comisión y código de estado son mutuamente excluyente.
		// Puede existir al menos uno, o ambos nulos.
		if (!is_null($pcomision_codigo) && !is_null($pid_codestado)) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerSoloExpedientes: %s", get_class($this), "Los filtros por Comisi&oacute;n y Estado son par&aacute;metros mutuamente excluyentes."));
		}

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBReportes()->obtenerSoloExpedientes(
				$pfecha_desde,
				$pfecha_hasta,
				$pfecha_comision,
				$pcomision_codigo,
				$pid_codestado,
				$pcomisiones_elegidas_en_modal,
				$ptratamiento_comision,
				$pOrdenColumnas,
				$pLimiteCantidad,
				$pLimiteOffset); //, $pvencidos,
		} catch (Exception $e) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerSoloExpedientes: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Expediente
		$resultado = $this->arrayResultToInstance($filas, 'Expediente');

		// Obtengo el resto de la información si se solicita la instancia completa
		if ($pInstanciasCompletas) {
			foreach ($resultado as $exp) {
				$exp = NG::expedientes()->completarInstanciaExpediente($exp);

				// 21/09/2020 XXXX
				// Sólo los Perfiles 1 y 2 pueden ver los extractos cuando el Tema es el 36 (OFICIO JUDICIAL)
				// Si el perfil es 3 o 4 los extractos NO se muestran (se limpian)
				// --------------------------------------------------------------------------------------------
				if (isset($_SESSION['perfil2']) && ($_SESSION['perfil2'] == 3 || $_SESSION['perfil2'] == 4)) {
					$tiene_tema_36 = 0;
					// Se recorren los Temas del expediente/nota
					foreach ($exp->getTemas() as $item) {
						// Si posee el Tema 36
						if ($item->id_codtema == 36) {
							$tiene_tema_36 = 1; // Se marca que lo tiene
							break;
						}
					}
					// Si posee un Tema 36
					if ($tiene_tema_36) {
						// Se "limpian" los Extractos de cada Proyecto del expediente
						foreach ($exp->getProyectos() as $item) {
							$item->extracto = '';
						}
					}
				}
			}
		}

		DB::getInstanceDBReportes()->desconectar();

		return $resultado;
	}

	/**
	 * NGReportes: Obtiene la cantidad de instancias correspondientes a la clase Expediente en base a diferentes criterios de selección,
	 * en una Comisión determinada.
	 * @param  string     $pfecha_desde
	 * @param  string     $pfecha_hasta
	 * @param  string     $pfecha_listado
	 * @param  string     $pfecha_comision
	 * @param  string     $pcomision_codigo
	 * @param  integer    $pid_codestado
	 * @param  bool       $ptratamiento_comision
	 * @return integer
	 */
	public function obtenerCantidadSoloExpedientes(
		// Parametros
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pfecha_comision = null,
		$pcomision_codigo = null,
		$pid_codestado = null,
		$pcomisiones_elegidas_en_modal = null,
		$ptratamiento_comision = null) {
		DB::getInstanceDBReportes()->conectar();

		// La búsqueda por rango de fechas debe existir.
		if (is_null($pfecha_desde) && is_null($pfecha_hasta)) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerCantidadSoloExpedientes: %s", get_class($this), "Los filtros por fecha Desde y Hasta son obligatorios."));
		}

		// Comisión y código de estado son mutuamente excluyentes.
		// Puede existir a menos uno, o ambos nulos.
		if (!is_null($pcomision_codigo) && !is_null($pid_codestado)) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerCantidadSoloExpedientes: %s", get_class($this), "Los filtros por Comisi&oacute;n y Estado son par&aacute;metros mutuamente excluyentes."));
		}

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBReportes()->obtenerCantidadSoloExpedientes(
				$pfecha_desde,
				$pfecha_hasta,
				$pfecha_comision,
				$pcomision_codigo,
				$pid_codestado,
				$pcomisiones_elegidas_en_modal,
				$ptratamiento_comision); //, $pvencidos
		} catch (Exception $e) {
			DB::getInstanceDBReportes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerCantidadSoloExpedientes: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBReportes()->desconectar();

		return $cantidad_resultados;
	}
}

<?php
/* ****************************************************************************
 Capa de acceso a datos (persistencia).
**************************************************************************** */
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/config/path_config.php');

require_once(PATH_SGL_LAYER_DATOS_PRESTAMOS.'db_config.php');

/**
 * Esta clase implementa el acceso a datos utilizando MySQLi con los drivers básicos
 * de PHP, llamados MySQL Client Library, el cual es menos performante y no permite
 * el uso de funciones mejoradas. Funciona por defecto en PHP 5.3.x
 *
 * Se implementa de esta manera porque no se puede actualizar (por ahora) el driver
 * de PHP para MySQL en el entorno de producción.
 *
 * @author XXXX, XXXX
 *
 */
class db_prestamos {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	// Referencia a la conexion mediante las funciones de mysqli
	private $mysqli_conn;

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct()
	{
	}

	/**
	 * Conecta al motor de persistencia.
	 * @throws RuntimeException
	 */
	public function conectar()
	{
		$this->mysqli_conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

		// Cambiamos el juego de caracteres, para que acepte caracteres especiales.
		$this->mysqli_conn->set_charset("utf8");

		/* comprobar la conexión */
		if (mysqli_connect_errno()) {
			throw new RuntimeException("Error en layer_datos:" . mysqli_connect_error());
		}
	}

	/**
	 * Desconecta del motor de persistencia.
	 */
	public function desconectar()
	{
		/* cerrar la conexión */
		$this->mysqli_conn->close();
	}

	/**
	 * Transforma un array a utf8 de manera recursiva
	 * @param var $dat Array a convertir a utf8.
	 * @return array El mismo array enviado como parametro, con la codificacion de caracteres modificada.
	 */
	private function ArrayUTF8EncodeRecursive($dat)
	{
		if (is_string($dat)) {
			if (mb_detect_encoding($dat, 'UTF-8', true))
				return $dat;
			else
				return utf8_encode($dat);
		}

		if (is_object($dat)) {
			$ovs= get_object_vars($dat);
			$new=$dat;
			foreach ($ovs as $k =>$v)    {
				$new->$k=$this->ArrayUTF8EncodeRecursive($new->$k);
			}
			return $new;
		}

		if (!is_array($dat)) return $dat;
		$ret = array();
		foreach($dat as $i=>$d) $ret[$i] = $this->ArrayUTF8EncodeRecursive($d);
		return $ret;
	}

	/**
	 * Transforma un array de nombre de columnas en una cláusula ORDER BY para un query de MySQL.
	 * En caso de no indicar columnas o un array vacío, la cláusula es una cadena vacía.
	 * @param array $columnas Array con el conjunto de nombres de columnas.
	 * @param string $sentido Sentido del orden (ORDEN_ASCENDENTE o ORDEN_DESCENDENTE)
	 * @return string Cadena correspondiente a la claúsula ORDER BY
	 */
	private function GenerarOrderBy(array $columnas = null, $sentido = ORDEN_ASCENDENTE)
	{
		// Por cada columna, vamos armando un string que genera la clausula
		// ORDER BY del query
		$clausula = "";

		// Si no hay columnas, no hay clausula
		if ($columnas != null)
		{
			if (count($columnas) > 0)
				$clausula = " ORDER BY ".implode(",", $columnas)." ".$sentido;
		}

		return $clausula;
	}

	/**
	 * Genera la cláusula LIMIT a partir de la cantidad y el corrimiento.
	 * En caso de no indicar cantidad y el corrimiento, la cláusula es una cadena vacía.
	 * @param string $limiteCantidad Cantidad de elementos a mostrar (equivalente a LIMIT cantidad).
	 * @param string $limiteOffset Corrimiento del elemento inicial a mostrar (equivalente a LIMIT corrimiento, cantidad).
	 * @return string Cadena correspondiente a la claúsula LIMIT
	 */
	private function GenerarLimit($limiteCantidad = null, $limiteOffset = null)
	{
		// Vamos armando un string que genera la clausula LIMIT del query
		$clausula = "";

		// si la cantidad es nula, no hago nada
		if ($limiteCantidad != null)
		{
			$clausula = " LIMIT ".$limiteCantidad;

			// si el offset es nulo, no agrego "el corrimiento" (XXXX de mierda, me gusta el ingles).
			if ($limiteOffset != null)
				$clausula = " LIMIT ".$limiteOffset.", ".$limiteCantidad;
		}

		return $clausula;
	}

	/**
	 * Obtiene una array de filas correspondientes a préstamos en base a diferentes criterios de selección.
	 * @param integer $pAnio Año del expediente.
	 * @param string $pTipo Tipo de expediente.
	 * @param integer $pNumero Número del expediente.
	 * @param integer $pCuerpo Cuerpo del expediente.
	 * @param integer $pAlcance Alcance del expediente.
	 * @param string $pDigito Dígito del expediente. Para expedientes externos.
	 * @param integer $pCuerpoalcance Cuerpo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexoalcance Anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexoalcance Cuerpo del anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexo Anexo del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexo Cuerpo del anexo del expediente. Para expedientes externos.
	 * @param string $pFecha_solicitud_desde Fecha de solicitud del expediente; funciona como filtro para obtener conjunto con $pFecha_solicitud_hasta.
	 * @param string $pFecha_solicitud_hasta Fecha de solicitud del expediente; funciona como filtro para obtener conjunto con $pFecha_solicitud_desde.
	 * @param string $pSolicitante_tipo Tipo del solicitante.
	 * @param string $pSolicitante_codigo Código del solicitante.
	 * @param array $pEstados Arreglo de estados para filtro. Conjunto de estados a devolver. Si se indica null o un array vacío, no filtra por estado.
	 * @param array $pOrdenColumnas Array de nombres de atributos para ordernar la colección resultado.
	 * @param string $pOrdenSentido Criterio de ordenamiento de la colección resultado (ORDEN_ASCENDENTE, ORDEN_DESCENDENTE).
	 * @param integer $pLimiteCantidad Limite de cantidad de resultados a devolver (utilizado normalmente para paginación).
	 * @param integer $pLimiteOffset Offset de resultados a devolver (utilizado normalmente para paginación).
	 * @return array Array asociativo de los préstamos seleccionados.
	 * @throws RuntimeException
	 */
	public function ObtenerPrestamos($pAnio, $pTipo, $pNumero, $pCuerpo = null, $pAlcance  = null, $pDigito = null,
			$pCuerpoalcance = null, $pAnexoalcance = null, $pCuerpoanexoalcance = null, $pAnexo = null,	$pCuerpoanexo = null,
			$pFecha_solicitud_desde = null, $pFecha_solicitud_hasta = null,
			$pSolicitante_tipo = null, $pSolicitante_codigo = null,
			array $pEstados = null,
			array $pOrdenColumnas = null, $pOrdenSentido = ORDEN_ASCENDENTE,
			$pLimiteCantidad = null, $pLimiteOffset = null)
	{
		$query = "SELECT 	EP.anio,
							EP.tipo,
							EP.numero,
							EP.cuerpo,
							EP.alcance,
							EP.digito,
							EP.cuerpoalcance,
							EP.anexoalcance,
							EP.cuerpoanexoalcance,
							EP.anexo,
							EP.cuerpoanexo,
							EP.fecha_solicitud,
							EP.fecha_prestado,
							EP.fecha_devuelto,
							EP.fecha_anulado,
							EP.solicitante_tipo,
							EP.solicitante_codigo,
					        ELUGAR.descripcion_grp as solicitante_nombre,
							EP.libro_numero,
							EP.libro_folio,
							EP.estado,
							EP.observaciones_prestamo,
							EP.id_usuario
					FROM 	hcd.expe_prestamos EP
					LEFT JOIN hcd.expe_lugares ELUGAR ON
						(ELUGAR.tipo_grp = EP.solicitante_tipo AND ELUGAR.codigo_grp = EP.solicitante_codigo)
					WHERE 	EP.activo = 1
					AND 	EP.anio = IFNULL(?, EP.anio)
					AND		EP.tipo = IFNULL(?, EP.tipo)
					AND		EP.numero = IFNULL(?, EP.numero)
					AND		EP.cuerpo = IFNULL(?, EP.cuerpo)
					AND		EP.alcance = IFNULL(?, EP.alcance)
					AND    	EP.digito = IFNULL(?, EP.digito)
					AND    	EP.cuerpoalcance = IFNULL(?, EP.cuerpoalcance)
					AND     EP.anexoalcance = IFNULL(?, EP.anexoalcance)
					AND 	EP.cuerpoanexoalcance = IFNULL(?, EP.cuerpoanexoalcance)
					AND    	EP.anexo = IFNULL(?, EP.anexo)
					AND    	EP.cuerpoanexo = IFNULL(?, EP.cuerpoanexo)
					AND    	EP.fecha_solicitud BETWEEN IFNULL(?, '1000-01-01') AND IFNULL(?, '9999-12-31')
					AND		EP.solicitante_tipo = IFNULL(?, EP.solicitante_tipo)
					AND		EP.solicitante_codigo = IFNULL(?, EP.solicitante_codigo) ";

		// Armo el conjunto de estados posibles
		// Si el conjunto es nulo o no tiene elementos, NO APLICO el filtro por estado
		$criterioEstados = "";
		if ($pEstados != null)
		{
			if (count($pEstados) > 0)
			{
				// Agrego las comillas simples a cada estado que recibo como parametro
				$conjuntoEstados = array();
				foreach ($pEstados as $e)
					$conjuntoEstados[] = "'".trim($e)."'";
				$criterioEstados = " AND EP.estado IN (".implode(",", $conjuntoEstados).")";
			}
			$query = $query . $criterioEstados;
		}

		// Agrego el ORDER BY, si es necesario
		$query = $query . $this->GenerarOrderBy($pOrdenColumnas, $pOrdenSentido);

		// Agrego el LIMIT, si es necesario
		$query = $query . $this->GenerarLimit($pLimiteCantidad, $pLimiteOffset);

		//Logger::GetInstance()->Log('query_despues_limit_ObtenerPrestamos', $query);
		/*
		print("<pre>$query</pre>");
		print("<pre>$pFecha_solicitud_desde || $pFecha_solicitud_hasta</pre>");
		*/

		// preparo y ejecuto la consulta
		$filas = array();
		if ($sentencia = $this->mysqli_conn->prepare($query))
		{
			$sentencia->bind_param('isiiisiiiiissss', $pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance, $pDigito,
					$pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo,	$pCuerpoanexo,
					$pFecha_solicitud_desde, $pFecha_solicitud_hasta,
					$pSolicitante_tipo, $pSolicitante_codigo);

			// ejecutar la sentencia
			$sentencia->execute();

			// vincular las variables de resultados
			$sentencia->bind_result($col_anio,
					$col_tipo,
					$col_numero,
					$col_cuerpo,
					$col_alcance,
					$col_digito,
					$col_cuerpoalcance,
					$col_anexoalcance,
					$col_cuerpoanexoalcance,
					$col_anexo,
					$col_cuerpoanexo,
					$col_fecha_solicitud,
					$col_fecha_prestado,
					$col_fecha_devuelto,
					$col_fecha_anulado,
					$col_solicitante_tipo,
					$col_solicitante_codigo,
					$col_solicitante_nombre,
					$col_libro_numero,
					$col_libro_folio,
					$col_estado,
					$col_observaciones_prestamo,
					$col_id_usuario);

	 		// obtener los valores
			while ($sentencia->fetch())
			{
				$filas[] = array(
					'anio' => $col_anio,
					'tipo' => $col_tipo,
					'numero' => $col_numero,
					'cuerpo' => $col_cuerpo,
					'alcance' => $col_alcance,
					'digito' => $col_digito,
					'cuerpoalcance' => $col_cuerpoalcance,
					'anexoalcance' => $col_anexoalcance,
					'cuerpoanexoalcance' => $col_cuerpoanexoalcance,
					'anexo' => $col_anexo,
					'cuerpoanexo' => $col_cuerpoanexo,
					'fecha_solicitud' => $col_fecha_solicitud,
					'fecha_prestado' => $col_fecha_prestado,
					'fecha_devuelto' => $col_fecha_devuelto,
					'fecha_anulado' => $col_fecha_anulado,
					'solicitante_tipo' => $col_solicitante_tipo,
					'solicitante_codigo' => $col_solicitante_codigo,
					'solicitante_nombre' => $col_solicitante_nombre,
					'libro_numero' => $col_libro_numero,
					'libro_folio' => $col_libro_folio,
					'estado' => $col_estado,
					'observaciones_prestamo' => $col_observaciones_prestamo,
					'id_usuario' => $col_id_usuario
				);
			}

			// cerrar la sentencia
			$sentencia->close();
		}
		else
		{
			//Lanzo una excepcion
			throw new RuntimeException("Falló la ejecución de db_prestamos.ObtenerPrestamos: (" . $this->mysqli_conn->errno . ") " . $this->mysqli_conn->error);
		}

		// Convierto en caso de caracteres extraños.
		return $this->ArrayUTF8EncodeRecursive($filas);
	}

	/**
	 * Obtiene la cantidad de resultados para una consulta de préstamos en base a diferentes criterios de selección. En esencia
	 * ejecuta la misma consulta que ObtenerPrestamos, pero en vez de devolver el conjunto de datos, devuelve la cantidad de resultados.
	 * Se utiliza en paginación de resultados.
	 * @param integer $pAnio Año del expediente.
	 * @param string $pTipo Tipo de expediente.
	 * @param integer $pNumero Número del expediente.
	 * @param integer $pCuerpo Cuerpo del expediente.
	 * @param integer $pAlcance Alcance del expediente.
	 * @param string $pDigito Dígito del expediente. Para expedientes externos.
	 * @param integer $pCuerpoalcance Cuerpo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexoalcance Anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexoalcance Cuerpo del anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexo Anexo del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexo Cuerpo del anexo del expediente. Para expedientes externos.
	 * @param string $pFecha_solicitud_desde Fecha de solicitud del expediente; funciona como filtro para obtener conjunto con $pFecha_solicitud_hasta.
	 * @param string $pFecha_solicitud_hasta Fecha de solicitud del expediente; funciona como filtro para obtener conjunto con $pFecha_solicitud_desde.
	 * @param string $pSolicitante_tipo Tipo del solicitante.
	 * @param string $pSolicitante_codigo Código del solicitante.
	 * @param array $pEstados Arreglo de estados para filtro. Conjunto de estados a devolver. Si se indica null o un array vacío, no filtra por estado.
	 * @return integer Cantidad de resultados para una determinada consulta de préstamos.
	 * @throws RuntimeException
	 */
	public function ObtenerPrestamosCantidadResultados($pAnio, $pTipo, $pNumero, $pCuerpo = null, $pAlcance  = null, $pDigito = null,
			$pCuerpoalcance = null, $pAnexoalcance = null, $pCuerpoanexoalcance = null, $pAnexo = null,	$pCuerpoanexo = null,
			$pFecha_solicitud_desde = null, $pFecha_solicitud_hasta = null,
			$pSolicitante_tipo = null, $pSolicitante_codigo = null,
			array $pEstados = null)
	{
		$query = "SELECT 	count(*) as cantidad
					FROM 	hcd.expe_prestamos EP
					WHERE 	EP.activo = 1
					AND 	EP.anio = IFNULL(?, EP.anio)
					AND		EP.tipo = IFNULL(?, EP.tipo)
					AND		EP.numero = IFNULL(?, EP.numero)
					AND		EP.cuerpo = IFNULL(?, EP.cuerpo)
					AND		EP.alcance = IFNULL(?, EP.alcance)
					AND    	EP.digito = IFNULL(?, EP.digito)
					AND    	EP.cuerpoalcance = IFNULL(?, EP.cuerpoalcance)
					AND     EP.anexoalcance = IFNULL(?, EP.anexoalcance)
					AND 	EP.cuerpoanexoalcance = IFNULL(?, EP.cuerpoanexoalcance)
					AND    	EP.anexo = IFNULL(?, EP.anexo)
					AND    	EP.cuerpoanexo = IFNULL(?, EP.cuerpoanexo)
					AND    	EP.fecha_solicitud BETWEEN IFNULL(?, '1000-01-01') AND IFNULL(?, '9999-12-31')
					AND		EP.solicitante_tipo = IFNULL(?, EP.solicitante_tipo)
					AND		EP.solicitante_codigo = IFNULL(?, EP.solicitante_codigo)";

		// Armo el conjunto de estados posibles
		// Si el conjunto es nulo o no tiene elementos, NO APLICO el filtro por estado
		$criterioEstados = "";
		if ($pEstados != null)
		{
			if (count($pEstados) > 0)
			{
				// Agrego las comillas simples a cada estado que recibo como parametro
				$conjuntoEstados = array();
				foreach ($pEstados as $e)
					$conjuntoEstados[] = "'".trim($e)."'";
				$criterioEstados = " AND EP.estado IN (".implode(",", $conjuntoEstados).")";
			}
			$query = $query . $criterioEstados;
		}

		// preparo y ejecuto la consulta
		if ($sentencia = $this->mysqli_conn->prepare($query)) {

			$sentencia->bind_param('isiiisiiiiissss', $pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance, $pDigito,
					$pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo,	$pCuerpoanexo,
					$pFecha_solicitud_desde, $pFecha_solicitud_hasta,
					$pSolicitante_tipo, $pSolicitante_codigo);

			// ejecutar la sentencia
			$sentencia->execute();

			// vincular las variables de resultados
			$sentencia->bind_result($col_cantidad);

	 		// obtener los valores
			$cantidad = 0;
			if ($sentencia->fetch()) {
				$cantidad = $col_cantidad;
			}

			// cerrar la sentencia
			$sentencia->close();
		}
		else
		{
			//Lanzo una excepcion
			throw new RuntimeException("Falló la ejecución de db_prestamos.ObtenerPrestamosCantidadResultados: (" . $this->mysqli_conn->errno . ") " . $this->mysqli_conn->error);
		}

		// Convierto en caso de caracteres extraños.
		return $cantidad;
	}

	/**
	 * Guarda un préstamo. Si no existe en la base de datos, crea uno nuevo.
	 * Si ya existe, lo actualiza (no se modifican claves primarias).
	 * @param integer $pAnio Año del expediente.
	 * @param string $pTipo Tipo de expediente.
	 * @param integer $pNumero Número del expediente.
	 * @param integer $pCuerpo Cuerpo del expediente.
	 * @param integer $pAlcance Alcance del expediente.
	 * @param string $pDigito Dígito del expediente. Para expedientes externos.
	 * @param integer $pCuerpoalcance Cuerpo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexoalcance Anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexoalcance Cuerpo del anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexo Anexo del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexo Cuerpo del anexo del expediente. Para expedientes externos.
	 * @param string $pFecha_solicitud Fecha de solicitud del préstamo.
	 * @param string $pFecha_prestado Fecha de prestado del préstamo.
	 * @param string $pFecha_devuelto Fecha de devolución del préstamo.
	 * @param string $pFecha_anulado Fecha de anulación del préstamo.
	 * @param string $pSolicitante_tipo Tipo del solicitante.
	 * @param string $pSolicitante_codigo Código del solicitante.
	 * @param integer $pLibro_numero Número de registro en libro de préstamos.
	 * @param integer $pLibro_folio Folio de registro en libro de préstamos.
	 * @param string $pEstado Estado del préstamo.
	 * @param string $pObservaciones_prestamo Observaciones del préstamo.
	 * @param integer $pId_usuario Identificador de usuario de ultima modificación.
	 * @throws RuntimeException
	 */
	public function GuardarPrestamo($pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance,
			$pDigito, $pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo, $pCuerpoanexo,
			$pFecha_solicitud, $pFecha_prestado, $pFecha_devuelto, $pFecha_anulado,
			$pSolicitante_tipo, $pSolicitante_codigo, $pLibro_numero, $pLibro_folio,
			$pEstado, $pObservaciones_prestamo, $pId_usuario)
	{
		// Primero verifico si el prestamo existe, para determinar un insert o un update
		$prestamo_existente = $this->ObtenerPrestamos($pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance,
			$pDigito, $pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo, $pCuerpoanexo,
			$pFecha_solicitud, $pFecha_solicitud); // Repito la fecha de solicitud para obtener exactamante un prestamo

		$query = "";

		if (count($prestamo_existente) == 0) // No existe, INSERT
		{
			// Primero se indican los campos NO CLAVE para luego poder utilizar un unico "bind_params"
			// tanto en el INSERT como en el UPDATE.
			$query = "INSERT INTO hcd.expe_prestamos
						( /*** campos NO CLAVE ***/
						fecha_prestado, fecha_devuelto, fecha_anulado,
						solicitante_tipo, solicitante_codigo,
						libro_numero, libro_folio, estado, observaciones_prestamo, id_usuario,
						  /*** campos CLAVE ***/
						anio, tipo, numero, cuerpo, alcance,
						digito,	cuerpoalcance, anexoalcance, cuerpoanexoalcance, anexo, cuerpoanexo,
						fecha_solicitud)
					VALUES
						(?,	?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		}
		else if (count($prestamo_existente) == 1) // Existe, UPDATE
		{
			// Primero se indican los campos NO CLAVE para luego poder utilizar un unico "bind_params"
			// tanto en el INSERT como en el UPDATE.
			// Además, no permito modificar la clave primaria.
			$query = "UPDATE hcd.expe_prestamos
					SET
					/*** campos NO CLAVE ***/
						fecha_prestado = ?,
						fecha_devuelto = ?,
						fecha_anulado = ?,
						solicitante_tipo = ?,
						solicitante_codigo = ?,
						libro_numero = ?,
						libro_folio = ?,
						estado = ?,
						observaciones_prestamo = ?,
						id_usuario = ?
					/*** campos CLAVE ***/
					WHERE 	anio = ? AND tipo = ? AND numero = ? AND cuerpo = ? AND alcance = ?
					AND 	digito = ? AND cuerpoalcance = ? AND anexoalcance = ? AND cuerpoanexoalcance = ? AND anexo = ? AND cuerpoanexo = ? AND fecha_solicitud = ?";
		}
		else // Mas de un resultado, ERROR!!!
		{
			//Lanzo una excepcion
			throw new RuntimeException("Falló la ejecución de db_prestamos.GuardarPrestamo: Clave duplicada? (" . $this->mysqli_conn->errno . ") " . $this->mysqli_conn->error);
		}

		// print("<pre>$query</pre>");

		if ($sentencia = $this->mysqli_conn->prepare($query)) {

			// Si las fechas son cadenas vacias, las transformo a nulos
			$pFecha_solicitud = (empty($pFecha_solicitud)) ? null : $pFecha_solicitud;
			$pFecha_prestado = (empty($pFecha_prestado)) ? null : $pFecha_prestado;
			$pFecha_devuelto = (empty($pFecha_devuelto)) ? null : $pFecha_devuelto;
			$pFecha_anulado = (empty($pFecha_anulado)) ? null : $pFecha_anulado;

			$sentencia->bind_param('sssssiissiisiiisiiiiis',
					/*** campos NO CLAVE ***/
					$pFecha_prestado, $pFecha_devuelto, $pFecha_anulado,
					$pSolicitante_tipo, $pSolicitante_codigo, $pLibro_numero, $pLibro_folio,
					$pEstado, $pObservaciones_prestamo, $pId_usuario,
					/*** campos CLAVE ***/
					$pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance,
					$pDigito, $pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo, $pCuerpoanexo,
					$pFecha_solicitud);

			// ejecutar la sentencia
			$sentencia->execute();

			// Verifico errores (debe ser antes de cerrar la sentencia).
			$error_msg = $this->mysqli_conn->error;
			$error_nro = $this->mysqli_conn->errno;

			// cerrar la sentencia
			$sentencia->close();

			// Si hubo errores, los muestro
			if ($error_msg != '')
				throw new RuntimeException("Falló la ejecución de db_prestamos.GuardarPrestamo: ($error_nro) $error_msg");
		}
		else
		{
			//Lanzo una excepcion
			throw new RuntimeException("Falló la ejecución de db_prestamos.GuardarPrestamo: (" . $this->mysqli_conn->errno . ") " . $this->mysqli_conn->error);
		}
	}

	/**
	 * Obtiene una array de filas correspondientes a solicitudes de expedientes externos en base a diferentes criterios de selección.
	 * @param integer $pAnio Año del expediente.
	 * @param string $pTipo Tipo de expediente.
	 * @param integer $pNumero Número del expediente.
	 * @param integer $pCuerpo Cuerpo del expediente.
	 * @param integer $pAlcance Alcance del expediente.
	 * @param string $pDigito Dígito del expediente. Para expedientes externos.
	 * @param integer $pCuerpoalcance Cuerpo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexoalcance Anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexoalcance Cuerpo del anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexo Anexo del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexo Cuerpo del anexo del expediente. Para expedientes externos.
	 * @param string $pFecha_solicitud_hcd_desde Fecha de solicitud del expediente desde el HCD; funciona como filtro para obtener conjunto con $pFecha_solicitud_hcd_hasta.
	 * @param string $pFecha_solicitud_hcd_hasta Fecha de solicitud del expediente desde el HCD; funciona como filtro para obtener conjunto con $pFecha_solicitud_hcd_desde.
	 * @param array $pEstados Arreglo de estados para filtro. Conjunto de estados a devolver. Si se indica null o un array vacío, no filtra por estado.
	 * @param array $pOrdenColumnas Array de nombres de atributos para ordernar la colección resultado.
	 * @param string $pOrdenSentido Criterio de ordenamiento de la colección resultado (ORDEN_ASCENDENTE, ORDEN_DESCENDENTE).
	 * @param integer $pLimiteCantidad Limite de cantidad de resultados a devolver (utilizado normalmente para paginación).
	 * @param integer $pLimiteOffset Offset de resultados a devolver (utilizado normalmente para paginación).
	 * @return array Array asociativo de los préstamos seleccionados.
	 * @throws RuntimeException
	 */
	public function ObtenerSolicitudesExpedientesExternos($pAnio, $pTipo, $pNumero, $pCuerpo = null, $pAlcance  = null, $pDigito = null,
			$pCuerpoalcance = null, $pAnexoalcance = null, $pCuerpoanexoalcance = null, $pAnexo = null,	$pCuerpoanexo = null,
			$pFecha_solicitud_hcd_desde = null, $pFecha_solicitud_hcd_hasta = null,
			array $pEstados = null,
			array $pOrdenColumnas = null, $pOrdenSentido = ORDEN_ASCENDENTE,
			$pLimiteCantidad = null, $pLimiteOffset = null)
	{
		$query = "SELECT 	EE.anio,
							EE.tipo,
							EE.numero,
							EE.cuerpo,
							EE.alcance,
							EE.digito,
							EE.cuerpoalcance,
							EE.anexoalcance,
							EE.cuerpoanexoalcance,
							EE.anexo,
							EE.cuerpoanexo,
							EE.fecha_solicitud_hcd,
							EE.fecha_solicitud_ee,
							EE.fecha_ingresado_ee,
							EE.fecha_devuelto_ee,
							EE.fecha_anulado_ee,
							EE.estado,
							EE.observaciones,
							EE.id_usuario
					FROM 	hcd.expe_expedientes_externos EE
					WHERE 	EE.activo = 1
					AND 	EE.anio = IFNULL(?, EE.anio)
					AND		EE.tipo = IFNULL(?, EE.tipo)
					AND		EE.numero = IFNULL(?, EE.numero)
					AND		EE.cuerpo = IFNULL(?, EE.cuerpo)
					AND		EE.alcance = IFNULL(?, EE.alcance)
					AND    	EE.digito = IFNULL(?, EE.digito)
					AND    	EE.cuerpoalcance = IFNULL(?, EE.cuerpoalcance)
					AND     EE.anexoalcance = IFNULL(?, EE.anexoalcance)
					AND 	EE.cuerpoanexoalcance = IFNULL(?, EE.cuerpoanexoalcance)
					AND    	EE.anexo = IFNULL(?, EE.anexo)
					AND    	EE.cuerpoanexo = IFNULL(?, EE.cuerpoanexo)
					AND    	EE.fecha_solicitud_hcd BETWEEN IFNULL(?, '1000-01-01') AND IFNULL(?, '9999-12-31')";

		// Armo el conjunto de estados posibles
		// Si el conjunto es nulo o no tiene elementos, NO APLICO el filtro por estado
		$criterioEstados = "";
		if ($pEstados != null)
		{
			if (count($pEstados) > 0)
			{
				// Agrego las comillas simples a cada estado que recibo como parametro
				$conjuntoEstados = array();
				foreach ($pEstados as $e)
					$conjuntoEstados[] = "'".trim($e)."'";
				$criterioEstados = " AND EE.estado IN (".implode(",", $conjuntoEstados).")";
			}
			$query = $query . $criterioEstados;
		}

		// Agrego el ORDER BY, si es necesario
		$query = $query . $this->GenerarOrderBy($pOrdenColumnas, $pOrdenSentido);

		// Agrego el LIMIT, si es necesario
		$query = $query . $this->GenerarLimit($pLimiteCantidad, $pLimiteOffset);

		// preparo y ejecuto la consulta
		$filas = array();
		if ($sentencia = $this->mysqli_conn->prepare($query)) {

			$sentencia->bind_param('isiiisiiiiiss', $pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance, $pDigito,
					$pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo,	$pCuerpoanexo,
					$pFecha_solicitud_hcd_desde, $pFecha_solicitud_hcd_hasta);

			// ejecutar la sentencia
			$sentencia->execute();

			// vincular las variables de resultados
			$sentencia->bind_result($col_anio,
					$col_tipo,
					$col_numero,
					$col_cuerpo,
					$col_alcance,
					$col_digito,
					$col_cuerpoalcance,
					$col_anexoalcance,
					$col_cuerpoanexoalcance,
					$col_anexo,
					$col_cuerpoanexo,
					$col_fecha_solicitud_hcd,
					$col_fecha_solicitud_ee,
					$col_fecha_ingresado_ee,
					$col_fecha_devuelto_ee,
					$col_fecha_anulado_ee,
					$col_estado,
					$col_observaciones,
					$col_id_usuario);

			// obtener los valores
			while ($sentencia->fetch()) {
				$filas[] = array(
						'anio' => $col_anio,
						'tipo' => $col_tipo,
						'numero' => $col_numero,
						'cuerpo' => $col_cuerpo,
						'alcance' => $col_alcance,
						'digito' => $col_digito,
						'cuerpoalcance' => $col_cuerpoalcance,
						'anexoalcance' => $col_anexoalcance,
						'cuerpoanexoalcance' => $col_cuerpoanexoalcance,
						'anexo' => $col_anexo,
						'cuerpoanexo' => $col_cuerpoanexo,
						'fecha_solicitud_hcd' => $col_fecha_solicitud_hcd,
						'fecha_solicitud_ee' => $col_fecha_solicitud_ee,
						'fecha_ingresado_ee' => $col_fecha_ingresado_ee,
						'fecha_devuelto_ee' => $col_fecha_devuelto_ee,
						'fecha_anulado_ee' => $col_fecha_anulado_ee,
						'estado' => $col_estado,
						'observaciones' => $col_observaciones,
						'id_usuario' => $col_id_usuario
				);
			}

			// cerrar la sentencia
			$sentencia->close();
		}
		else
		{
			//Lanzo una excepcion
			throw new RuntimeException("Falló la ejecución de db_prestamos.ObtenerSolicitudesExpedientesExternos: (" . $this->mysqli_conn->errno . ") " . $this->mysqli_conn->error);
		}

		// Convierto en caso de caracteres extraños.
		return $this->ArrayUTF8EncodeRecursive($filas);
	}

	/**
	 * Obtiene la cantidad de resultados para una consulta de solicitudes de expedientes externos en base a diferentes criterios de selección.
	 * En esencia ejecuta la misma consulta que ObtenerSolicitudesExpedientesExternos, pero en vez de devolver el conjunto de datos, devuelve
	 * la cantidad de resultados.
	 * Se utiliza en paginación de resultados.
	 * @param integer $pAnio Año del expediente.
	 * @param string $pTipo Tipo de expediente.
	 * @param integer $pNumero Número del expediente.
	 * @param integer $pCuerpo Cuerpo del expediente.
	 * @param integer $pAlcance Alcance del expediente.
	 * @param string $pDigito Dígito del expediente. Para expedientes externos.
	 * @param integer $pCuerpoalcance Cuerpo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexoalcance Anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexoalcance Cuerpo del anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexo Anexo del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexo Cuerpo del anexo del expediente. Para expedientes externos.
	 * @param string $pFecha_solicitud_hcd_desde Fecha de solicitud del expediente desde el HCD; funciona como filtro para obtener conjunto con $pFecha_solicitud_hcd_hasta.
	 * @param string $pFecha_solicitud_hcd_hasta Fecha de solicitud del expediente desde el HCD; funciona como filtro para obtener conjunto con $pFecha_solicitud_hcd_desde.
	 * @param array $pEstados Arreglo de estados para filtro. Conjunto de estados a devolver. Si se indica null o un array vacío, no filtra por estado.
	 * @return integer Cantidad de resultados para una determinada consulta de solicitudes de expedientes externos.
	 * @throws RuntimeException
	 */
	public function ObtenerSolicitudesExpedientesExternosCantidadResultados($pAnio, $pTipo, $pNumero, $pCuerpo = null, $pAlcance  = null, $pDigito = null,
			$pCuerpoalcance = null, $pAnexoalcance = null, $pCuerpoanexoalcance = null, $pAnexo = null,	$pCuerpoanexo = null,
			$pFecha_solicitud_hcd_desde = null, $pFecha_solicitud_hcd_hasta = null,
			array $pEstados = null)
	{
		$query = "SELECT 	count(*) as cantidad
					FROM 	hcd.expe_expedientes_externos EE
					WHERE 	EE.activo = 1
					AND 	EE.anio = IFNULL(?, EE.anio)
					AND		EE.tipo = IFNULL(?, EE.tipo)
					AND		EE.numero = IFNULL(?, EE.numero)
					AND		EE.cuerpo = IFNULL(?, EE.cuerpo)
					AND		EE.alcance = IFNULL(?, EE.alcance)
					AND    	EE.digito = IFNULL(?, EE.digito)
					AND    	EE.cuerpoalcance = IFNULL(?, EE.cuerpoalcance)
					AND     EE.anexoalcance = IFNULL(?, EE.anexoalcance)
					AND 	EE.cuerpoanexoalcance = IFNULL(?, EE.cuerpoanexoalcance)
					AND    	EE.anexo = IFNULL(?, EE.anexo)
					AND    	EE.cuerpoanexo = IFNULL(?, EE.cuerpoanexo)
					AND    	EE.fecha_solicitud_hcd BETWEEN IFNULL(?, '1000-01-01') AND IFNULL(?, '9999-12-31')";

		// Armo el conjunto de estados posibles
		// Si el conjunto es nulo o no tiene elementos, NO APLICO el filtro por estado
		$criterioEstados = "";
		if ($pEstados != null)
		{
			if (count($pEstados) > 0)
			{
				// Agrego las comillas simples a cada estado que recibo como parametro
				$conjuntoEstados = array();
				foreach ($pEstados as $e)
					$conjuntoEstados[] = "'".trim($e)."'";
				$criterioEstados = " AND EE.estado IN (".implode(",", $conjuntoEstados).")";
			}
			$query = $query . $criterioEstados;
		}

		if ($sentencia = $this->mysqli_conn->prepare($query)) {

			$sentencia->bind_param('isiiisiiiiiss', $pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance, $pDigito,
					$pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo,	$pCuerpoanexo,
					$pFecha_solicitud_hcd_desde, $pFecha_solicitud_hcd_hasta);

			// ejecutar la sentencia
			$sentencia->execute();

			// vincular las variables de resultados
			$sentencia->bind_result($col_cantidad);

			// obtener los valores
			$cantidad = 0;
			if ($sentencia->fetch()) {
				$cantidad = $col_cantidad;
			}

			// cerrar la sentencia
			$sentencia->close();
		}
		else
		{
			//Lanzo una excepcion
			throw new RuntimeException("Falló la ejecución de db_prestamos.ObtenerSolicitudesExpedientesExternosCantidadResultados: (" . $this->mysqli_conn->errno . ") " . $this->mysqli_conn->error);
		}

		return $cantidad;
	}

	/**
	 * Guarda una solicitud de expediente externo. Si no existe en la base de datos, crea una nueva.
	 * Si ya existe, la actualiza (no se modifican claves primarias).
	 * @param integer $pAnio Año del expediente.
	 * @param string $pTipo Tipo de expediente.
	 * @param integer $pNumero Número del expediente.
	 * @param integer $pCuerpo Cuerpo del expediente.
	 * @param integer $pAlcance Alcance del expediente.
	 * @param string $pDigito Dígito del expediente. Para expedientes externos.
	 * @param integer $pCuerpoalcance Cuerpo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexoalcance Anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexoalcance Cuerpo del anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexo Anexo del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexo Cuerpo del anexo del expediente. Para expedientes externos.
	 * @param string $pFecha_solicitud_hcd Fecha de solicitud del préstamo al HCD.
	 * @param string $pFecha_solicitud_ee Fecha de solicitud del préstamo al ente externo.
	 * @param string $pFecha_ingresado_ee Fecha de ingresado del préstamo desde el ente externo.
	 * @param string $pFecha_devuelto_ee Fecha de devolución del préstamo al ente externo.
	 * @param string $pFecha_anulado_ee Fecha de anulación del préstamo al ente externo.
	 * @param string $pEstado Estado del préstamo.
	 * @param string $pObservaciones Observaciones del préstamo.
	 * @param integer $pId_usuario Identificador de usuario de ultima modificación.
	 * @throws RuntimeException
	 */
	public function GuardarSolicitudExpedienteExterno($pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance,
			$pDigito, $pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo, $pCuerpoanexo,
			$pFecha_solicitud_hcd, $pFecha_solicitud_ee, $pFecha_ingresado_ee, $pFecha_devuelto_ee, $pFecha_anulado_ee,
			$pEstado, $pObservaciones, $pId_usuario)
	{
		// Primero verifico si la solicitud existe, para determinar un insert o un update
		$solicitud_existente = $this->ObtenerSolicitudesExpedientesExternos($pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance,
				$pDigito, $pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo, $pCuerpoanexo,
				$pFecha_solicitud_hcd, $pFecha_solicitud_hcd); // Repito la fecha de solicitud para obtener exactamante una solicitud

		$query = "";

		if (count($solicitud_existente) == 0) // No existe, INSERT
		{
			// Primero se indican los campos NO CLAVE para luego poder utilizar un unico "bind_params"
			// tanto en el INSERT como en el UPDATE.
			$query = "INSERT INTO hcd.expe_expedientes_externos
						( /*** campos NO CLAVE ***/
						fecha_solicitud_ee, fecha_ingresado_ee, fecha_devuelto_ee, fecha_anulado_ee,
						estado, observaciones, id_usuario,
						  /*** campos CLAVE ***/
						anio, tipo, numero, cuerpo, alcance,
						digito,	cuerpoalcance, anexoalcance, cuerpoanexoalcance, anexo, cuerpoanexo,
						fecha_solicitud_hcd)
					VALUES
						(?,	?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		}
		else if (count($solicitud_existente) == 1) // Existe, UPDATE
		{
			// Primero se indican los campos NO CLAVE para luego poder utilizar un unico "bind_params"
			// tanto en el INSERT como en el UPDATE.
			// Además, no permito modificar la clave primaria.
			$query = "UPDATE hcd.expe_expedientes_externos
					SET
					/*** campos NO CLAVE ***/
						fecha_solicitud_ee = ?,
						fecha_ingresado_ee = ?,
						fecha_devuelto_ee = ?,
						fecha_anulado_ee = ?,
						estado = ?,
						observaciones = ?,
						id_usuario = ?
					/*** campos CLAVE ***/
					WHERE 	anio = ? AND tipo = ? AND numero = ? AND cuerpo = ? AND alcance = ?
					AND 	digito = ? AND cuerpoalcance = ? AND anexoalcance = ? AND cuerpoanexoalcance = ? AND anexo = ? AND cuerpoanexo = ? AND fecha_solicitud_hcd = ?";
		}
		else // Mas de un resultado, ERROR!!!
		{
			//Lanzo una excepcion
			throw new RuntimeException("Falló la ejecución de db_prestamos.GuardarSolicitudExpedienteExterno: Clave duplicada? (" . $this->mysqli_conn->errno . ") " . $this->mysqli_conn->error);
		}

		// print("<pre>$query</pre>");

		if ($sentencia = $this->mysqli_conn->prepare($query)) {

			// Si las fechas son cadenas vacias, las transformo a nulos
			$pFecha_solicitud_hcd = (empty($pFecha_solicitud_hcd)) ? null : $pFecha_solicitud_hcd;
			$pFecha_solicitud_ee = (empty($pFecha_solicitud_ee)) ? null : $pFecha_solicitud_ee;
			$pFecha_ingresado_ee = (empty($pFecha_ingresado_ee)) ? null : $pFecha_ingresado_ee;
			$pFecha_devuelto_ee = (empty($pFecha_devuelto_ee)) ? null : $pFecha_devuelto_ee;
			$pFecha_anulado_ee = (empty($pFecha_anulado_ee)) ? null : $pFecha_anulado_ee;

			$sentencia->bind_param('ssssssiisiiisiiiiis',
					/*** campos NO CLAVE ***/
					$pFecha_solicitud_ee, $pFecha_ingresado_ee, $pFecha_devuelto_ee, $pFecha_anulado_ee,
					$pEstado, $pObservaciones, $pId_usuario,
					/*** campos CLAVE ***/
					$pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance,
					$pDigito, $pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo, $pCuerpoanexo,
					$pFecha_solicitud_hcd);

			// ejecutar la sentencia
			$sentencia->execute();

			// Verifico errores (debe ser antes de cerrar la sentencia).
			$error_msg = $this->mysqli_conn->error;
			$error_nro = $this->mysqli_conn->errno;

			// cerrar la sentencia
			$sentencia->close();

			// Si hubo errores, los muestro
			if ($error_msg != '')
				throw new RuntimeException("Falló la ejecución de db_prestamos.GuardarSolicitudExpedienteExterno: ($error_nro) $error_msg");
		}
		else
		{
			//Lanzo una excepcion
			throw new RuntimeException("Falló la ejecución de db_prestamos.GuardarSolicitudExpedienteExterno: (" . $this->mysqli_conn->errno . ") " . $this->mysqli_conn->error);
		}
	}

	/**
	 * Elimina un Préstamo de forma lógica
	 * @param integer $pAnio Año del expediente.
	 * @param string $pTipo Tipo de expediente.
	 * @param integer $pNumero Número del expediente.
	 * @param integer $pCuerpo Cuerpo del expediente.
	 * @param integer $pAlcance Alcance del expediente.
	 * @param string $pDigito Dígito del expediente. Para expedientes externos.
	 * @param integer $pCuerpoalcance Cuerpo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexoalcance Anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexoalcance Cuerpo del anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexo Anexo del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexo Cuerpo del anexo del expediente. Para expedientes externos.
	 * @param string $pFecha_solicitud Fecha de solicitud del préstamo.
	 * @throws RuntimeException
	 */
	public function EliminarPrestamo($pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance,
			$pDigito, $pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo, $pCuerpoanexo,
			$pFecha_solicitud)
	{

		$query = "UPDATE hcd.expe_prestamos
				  SET activo = 0
				  WHERE anio = ? AND tipo = ? AND numero = ? AND cuerpo = ? AND alcance = ?
				  AND 	digito = ? AND cuerpoalcance = ? AND anexoalcance = ? AND cuerpoanexoalcance = ?
				  AND anexo = ? AND cuerpoanexo = ? AND fecha_solicitud = ?";

		// print("<pre>$query</pre>");

		if ($sentencia = $this->mysqli_conn->prepare($query))
		{

			$sentencia->bind_param('isiiisiiiiis',
					$pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance,
					$pDigito, $pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo, $pCuerpoanexo,
					$pFecha_solicitud);

			// ejecutar la sentencia
			$sentencia->execute();

			// Verifico errores (debe ser antes de cerrar la sentencia).
			$error_msg = $this->mysqli_conn->error;
			$error_nro = $this->mysqli_conn->errno;

			// cerrar la sentencia
			$sentencia->close();

			// Si hubo errores, los muestro
			if ($error_msg != '')
				throw new RuntimeException("Falló la ejecución de db_prestamos.EliminarPrestamo: ($error_nro) $error_msg");
		}
		else
		{
			//Lanzo una excepcion
			throw new RuntimeException("Falló la ejecución de db_prestamos.EliminarPrestamo: (" . $this->mysqli_conn->errno . ") " . $this->mysqli_conn->error);
		}
	}

	/**
	 * Elimina una Solicitud de forma lógica
	 * @param integer $pAnio Año del expediente.
	 * @param string $pTipo Tipo de expediente.
	 * @param integer $pNumero Número del expediente.
	 * @param integer $pCuerpo Cuerpo del expediente.
	 * @param integer $pAlcance Alcance del expediente.
	 * @param string $pDigito Dígito del expediente. Para expedientes externos.
	 * @param integer $pCuerpoalcance Cuerpo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexoalcance Anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexoalcance Cuerpo del anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexo Anexo del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexo Cuerpo del anexo del expediente. Para expedientes externos.
	 * @param string $pFecha_solicitud_hcd Fecha de solicitud de la solicitud.
	 * @throws RuntimeException
	 */
	public function EliminarSolicitud($pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance,
			$pDigito, $pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo, $pCuerpoanexo,
			$pFecha_solicitud_hcd)
	{

		$query = "UPDATE hcd.expe_expedientes_externos
				  SET activo = 0
				  WHERE anio = ? AND tipo = ? AND numero = ? AND cuerpo = ? AND alcance = ?
				  AND 	digito = ? AND cuerpoalcance = ? AND anexoalcance = ? AND cuerpoanexoalcance = ?
				  AND anexo = ? AND cuerpoanexo = ? AND fecha_solicitud_hcd = ?";

		// print("<pre>$query</pre>");

		if ($sentencia = $this->mysqli_conn->prepare($query))
		{

			$sentencia->bind_param('isiiisiiiiis',
					$pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance,
					$pDigito, $pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo, $pCuerpoanexo,
					$pFecha_solicitud_hcd);

			// ejecutar la sentencia
			$sentencia->execute();

			// Verifico errores (debe ser antes de cerrar la sentencia).
			$error_msg = $this->mysqli_conn->error;
			$error_nro = $this->mysqli_conn->errno;

			// cerrar la sentencia
			$sentencia->close();

			// Si hubo errores, los muestro
			if ($error_msg != '')
				throw new RuntimeException("Falló la ejecución de db_prestamos.EliminarSolicitud: ($error_nro) $error_msg");
		}
		else
		{
			//Lanzo una excepcion
			throw new RuntimeException("Falló la ejecución de db_prestamos.EliminarSolicitud: (" . $this->mysqli_conn->errno . ") " . $this->mysqli_conn->error);
		}
	}
}
?>

<?php
/* ****************************************************************************
 Capa de acceso a datos (persistencia).
**************************************************************************** */

require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/config/path_config.php');

require_once(PATH_SGL_LAYER_DATOS_PRESTAMOS.'db_config.php');

/**
 * Esta clase implementa el acceso a datos utilizando MySQLi con los drivers nuevos
 * nativos de PHP, llamados MySQL Native Driver, el cual es mas performante y permite
 * el uso de un código mas limpio. Requiere PHP 5.3.x
 *
 * http://php.net/manual/es/mysqlnd.overview.php
 *
 * IMPORTANTE: ver notas de instalación del driver mysqlnd en la documentación.
 * 			apt-get update
 * 			apt-get install php5-mysqlnd
 * 			service apache2 restart
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
	 */
	private function ArrayUTF8EncodeRecursive($dat)
	{
		if (is_string($dat)) {
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
					WHERE 	EP.anio = IFNULL(?, EP.anio)
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
		$criterioEstados = "";
		if ($pEstados != null)
		{
			if (count($pEstados) > 0)
			{
				// Agrego las comillas simples a cada estado que recibo como parametro
				$conjuntoEstados = array();
				foreach ($pEstados as $e)
					$conjuntoEstados[] = "'".trim($e)."'";
			}
			$criterioEstados = " AND EP.estado IN (".implode(",", $conjuntoEstados).")";
			$query = $query . $criterioEstados;
		}

		// Agrego el ORDER BY, si es necesario
		$query = $query . $this->GenerarOrderBy($pOrdenColumnas, $pOrdenSentido);

		// Agrego el LIMIT, si es necesario
		$query = $query . $this->GenerarLimit($pLimiteCantidad, $pLimiteOffset);

		/*
		print("<pre>$query</pre>");
		print("<pre>$pFecha_solicitud_desde || $pFecha_solicitud_hasta</pre>");
		*/

		// preparo y ejecuto la consulta
		$filas = array();
		if ($sentencia = $this->mysqli_conn->prepare($query)) {

			$sentencia->bind_param('isiiisiiiiissss', $pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance, $pDigito,
					$pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo,	$pCuerpoanexo,
					$pFecha_solicitud_desde, $pFecha_solicitud_hasta,
					$pSolicitante_tipo, $pSolicitante_codigo);

			// ejecutar la sentencia
			$sentencia->execute();

			// obtengo el resultado de la sentencia
			$resultado = $sentencia->get_result();

			// convierto el resultado en un array asociativo, de una pasada (todos los resultados).
			$filas = $resultado->fetch_all(MYSQLI_ASSOC);

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
}
?>

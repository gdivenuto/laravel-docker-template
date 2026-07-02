<?php
/**
 * Capa de negocio de giros pendientes a comisiones.
 *
 * @author XXXX
 *
 */

class NGGirosPendientes extends NGBaseClass {

	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************

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
	 * NGGirosPendientes: Obtiene una coleccion de elementos tipo GiroPendiente en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-10-31 10:21:36
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) id_pendiente
	 * @param  string giros_pendientes
	 * @param  string estado
	 * @param  string fecha_hora_entrada
	 * @param  string fecha_hora_salida
	 * @param  integer id_usuario_firmante
	 * @param  integer id_usuario_solicitante
	 * @param  string observaciones
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<GiroPendiente>
	 */
	public function obtenerGirosPendientes(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pid_pendiente = null,
		$pgiros_pendientes = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
		$pid_usuario_firmante = null,
		$pid_usuario_solicitante = null,
		$pobservaciones = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null)
	{
		DB::getInstanceDBGirosPendientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBGirosPendientes()->obtenerGirosPendientes($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pid_pendiente, $pgiros_pendientes, $pestado, $pfecha_hora_entrada, $pfecha_hora_salida, $pid_usuario_firmante, $pid_usuario_solicitante, $pobservaciones,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBGirosPendientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerGirosPendientes: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo GiroPendiente
		$resultado = $this->arrayResultToInstance($filas, 'GiroPendiente');

		DB::getInstanceDBGirosPendientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGGirosPendientes: Determina la cantidad de elementos tipo GiroPendiente obtenidos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-10-31 10:21:36
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) id_pendiente
	 * @param  string giros_pendientes
	 * @param  string estado
	 * @param  string fecha_hora_entrada
	 * @param  string fecha_hora_salida
	 * @param  integer id_usuario_firmante
	 * @param  integer id_usuario_solicitante
	 * @param  string observaciones
	 * @return int
	 */
	public function obtenerGirosPendientesCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pid_pendiente = null,
		$pgiros_pendientes = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
		$pid_usuario_firmante = null,
		$pid_usuario_solicitante = null,
		$pobservaciones = null)
	{
		DB::getInstanceDBGirosPendientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBGirosPendientes()->obtenerGirosPendientesCantidad($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pid_pendiente, $pgiros_pendientes, $pestado, $pfecha_hora_entrada, $pfecha_hora_salida, $pid_usuario_firmante, $pid_usuario_solicitante, $pobservaciones);
		} catch (Exception $e) {
			DB::getInstanceDBGirosPendientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerGirosPendientesCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBGirosPendientes()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGGirosPendientes: Obtiene una instancia de tipo GiroPendiente en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.7 beta @ 2022-10-31 10:21:36
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) id_pendiente
	 * @return GiroPendiente Instancia de GiroPendiente buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerGiroPendiente(
		// Parametros
		$panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pid_pendiente)
	{
		if (is_null($panio) || is_null($ptipo) || is_null($pnumero) || is_null($pcuerpo) || is_null($palcance) || is_null($pid_pendiente))
			throw new Exception(sprintf("Error en %s.obtenerGiroPendiente: los campos clave no pueden ser nulos.", get_class($this)));

		$resultado = $this->obtenerGirosPendientes($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pid_pendiente);

		if (count($resultado) == 0)
			return null;
		else if (count($resultado) == 1)
			return $resultado[0];
		else
			throw new Exception(sprintf("Error en %s.obtenerGiroPendiente: se encontr&oacute; m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
	}

	/**
	 * Guarda una instancia de tipo GiroPendiente. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.97.7 beta @ 2022-10-31 10:21:36
	 * @param  GiroPendiente $pGiroPendiente 	Instancia a guardar.
	 * @param  boolean $pRecargar 		Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return GiroPendiente               Instancia guardada.
	 */
	public function guardarGiroPendiente(GiroPendiente $pGiroPendiente, $pRecargar = true)
	{
		if (is_null($pGiroPendiente))
			throw new Exception(sprintf("Error en %s.guardarGiroPendiente: la instancia a guardar no puede ser nula.",get_class($this)));

		DB::getInstanceDBGirosPendientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBGirosPendientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$id = DB::getInstanceDBGirosPendientes()->guardarGiroPendiente(
				$pGiroPendiente->anio,
				$pGiroPendiente->tipo,
				$pGiroPendiente->numero,
				$pGiroPendiente->cuerpo,
				$pGiroPendiente->alcance,
				$pGiroPendiente->id_pendiente,
				$pGiroPendiente->giros_pendientes,
				$pGiroPendiente->estado,
				$pGiroPendiente->fecha_hora_entrada,
				$pGiroPendiente->fecha_hora_salida,
				$pGiroPendiente->id_usuario_firmante,
				$pGiroPendiente->id_usuario_solicitante,
				$pGiroPendiente->observaciones);

			DB::getInstanceDBGirosPendientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBGirosPendientes()->cancelarTransaccion();
			DB::getInstanceDBGirosPendientes()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarGiroPendiente: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$resultado = $this->obtenerGiroPendiente($pGiroPendiente->anio, $pGiroPendiente->tipo, $pGiroPendiente->numero, $pGiroPendiente->cuerpo, $pGiroPendiente->alcance, $pGiroPendiente->id_pendiente);
		}
		else
			$resultado = $pGiroPendiente;

		DB::getInstanceDBGirosPendientes()->desconectar();

		if (is_null($resultado))
			throw new Exception(sprintf("Error grave en %s.guardarGiroPendiente: no se encuentra el contenido actualizado.",get_class($this)));

		return $resultado;
	}

	/**
	 * NGGirosPendientes: Elimina un conjunto de GirosPendientes en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-10-31 10:21:36
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) id_pendiente
	 * @param  string giros_pendientes
	 * @param  string estado
	 * @param  string fecha_hora_entrada
	 * @param  string fecha_hora_salida
	 * @param  integer id_usuario_firmante
	 * @param  integer id_usuario_solicitante
	 * @param  string observaciones
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarGirosPendientes(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pid_pendiente = null,
		$pgiros_pendientes = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
		$pid_usuario_firmante = null,
		$pid_usuario_solicitante = null,
		$pobservaciones = null)
	{
		DB::getInstanceDBGirosPendientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBGirosPendientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBGirosPendientes()->eliminarGirosPendientes($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pid_pendiente, $pgiros_pendientes, $pestado, $pfecha_hora_entrada, $pfecha_hora_salida, $pid_usuario_firmante, $pid_usuario_solicitante, $pobservaciones);

			//Logger::get()->Log("resultado_eliminarGirosPendientes_".date("Ymd_His"), $resultado);

			DB::getInstanceDBGirosPendientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBGirosPendientes()->cancelarTransaccion();
			DB::getInstanceDBGirosPendientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarGirosPendientes: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBGirosPendientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGGirosPendientes: Elimina una instancia de tipo GiroPendiente en base a su identificador.
	 * GenerateClass 0.97.7 beta @ 2022-10-31 10:21:36
	 * @param  GiroPendiente $pGiroPendiente 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarGiroPendiente(GiroPendiente $pGiroPendiente)
	{
		if (is_null($pGiroPendiente))
			throw new Exception(sprintf("Error en %s.eliminarGiroPendiente: la instancia a eliminar no puede ser nula.",get_class($this)));

		DB::getInstanceDBGirosPendientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBGirosPendientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarGirosPendientes($pGiroPendiente->anio, $pGiroPendiente->tipo, $pGiroPendiente->numero, $pGiroPendiente->cuerpo, $pGiroPendiente->alcance, $pGiroPendiente->id_pendiente);

			if ($resultado > 1)
				throw new Exception(sprintf("Error en %s.eliminarGiroPendiente: se quiso eliminar m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));

			DB::getInstanceDBGirosPendientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBGirosPendientes()->cancelarTransaccion();
			DB::getInstanceDBGirosPendientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarGiroPendiente: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBGirosPendientes()->desconectar();

		return ($resultado == 1);
	}

	/**
	 * [obtenerGiroPendienteIdSiguiente description]
	 * @param  GiroPendiente $pGiroPendiente [description]
	 * @return [type]                        [description]
	 */
	public function obtenerGiroPendienteIdSiguiente(GiroPendiente $pGiroPendiente)
	{
		DB::getInstanceDBGirosPendientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$nuevo_id_pendiente = DB::getInstanceDBGirosPendientes()->obtenerGiroPendienteIdSiguiente(
				$pGiroPendiente->anio,
				$pGiroPendiente->tipo,
				$pGiroPendiente->numero,
				$pGiroPendiente->cuerpo,
				$pGiroPendiente->alcance
			);
		} catch (Exception $e) {
			DB::getInstanceDBGirosPendientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerGiroPendienteIdSiguiente: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBGirosPendientes()->desconectar();

		return $nuevo_id_pendiente;
	}

	/**
	 * Agrega un lote de giros pendientes a un expediente.
	 * @param  GiroPendiente $pGiroPendiente [description]
	 * @return [type]                        [description]
	 */
	public function agregarGiroPendiente(GiroPendiente $pGiroPendiente)
	{
		// En una única transacción, obtengo el nuevo ID y lo guardo en la DB.
		DB::getInstanceDBGirosPendientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBGirosPendientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Piso id de firma y fecha/hora
			$fecha_hora = date('Y-m-d H:i:s');
			$pGiroPendiente->id_pendiente = $this->obtenerGiroPendienteIdSiguiente($pGiroPendiente);
			$pGiroPendiente->fecha_hora_entrada = $fecha_hora;
			$pGiroPendiente->fecha_hora_salida = ($pGiroPendiente->estado == 'pendiente')
				? null
				: $fecha_hora;

			// Guardo
			$pGiroPendiente = $this->guardarGiroPendiente($pGiroPendiente, true);

			// Ejecuto transaccion
			DB::getInstanceDBGirosPendientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBGirosPendientes()->cancelarTransaccion();
			DB::getInstanceDBGirosPendientes()->desconectar();
			throw new Exception(sprintf("Error en %s.agregarGiroPendiente: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBGirosPendientes()->desconectar();

		// Devolvemos el expediente electronico actualizado
		return $pGiroPendiente;
	}

	/**
	 * Obtiene los giros pendientes que esten aún sin confirmar, donde el
	 * usuario indicado como parámetro es 'solicitante' o 'firmante'.
	 * El usuario puede ser un único "id_usuario" o un array de "id_usuario",
	 * o "null" para traer todos los giros pendientes (supervisores).
	 * @param  array|Usuario $pUsuario        [description]
	 * @param  array|null    $pOrdenColumnas  [description]
	 * @param  [type]        $pLimiteCantidad [description]
	 * @param  [type]        $pLimiteOffset   [description]
	 * @return [type]                         [description]
	 */
	public function obtenerGirosPendientesParaUsuario(
		// Parametros
		$pUsuario,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null)
	{
		// Validacion dura de parametros variables
		if ( !(is_null($pUsuario) || get_class($pUsuario) == "Usuario" || is_array($pUsuario)) )
			throw new Exception(sprintf("Error en %s.obtenerGirosPendientesParaUsuario: el usuario solamente puede ser nulo, tipo 'Usuario' o un array.", get_class($this)));

		DB::getInstanceDBGirosPendientes()->conectar();

		try {
			// Preparo el parametro de usuarios (puede ser un array o una instancia de Usuario/null)
			$filtro_usuario = (is_array($pUsuario))
				? $pUsuario
				: ((is_null($pUsuario)) ? null : $pUsuario->id_usuario);

			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBGirosPendientes()->obtenerGirosPendientesUsuario(// PK: $panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pid_pendiente
				null, null, null, null, null, null,
				// Resto
				null,        // $pgiros_pendientes
				'pendiente', // $pestado
				null,        // $pfecha_hora_entrada
				null,        // $pfecha_hora_salida
				$filtro_usuario, // $pid_usuario_firmante
				$filtro_usuario, // $pid_usuario_solicitante
				null,        // $pobservaciones
				// Control de consulta
				$pOrdenColumnas, // La firma pendiente mas vieja primero
				$pLimiteCantidad, // cantidad de registros (paginación)
				$pLimiteOffset
			);
		} catch (Exception $e) {
			DB::getInstanceDBGirosPendientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerGirosPendientesParaUsuario: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo GiroPendiente
		$resultado = $this->arrayResultToInstance($filas, 'GiroPendiente');

		DB::getInstanceDBGirosPendientes()->desconectar();

		return $resultado;
	}

	/**
	 * Obtiene los giros pendientes que esten aún sin confirmar, donde el
	 * usuario indicado como parámetro es 'solicitante' o 'firmante'.
	 * El usuario puede ser un único "id_usuario" o un array de "id_usuario",
	 * o "null" para traer todos los giros pendientes (supervisores).
	 * @param  array|Usuario $pUsuario [description]
	 * @return [type]                  [description]
	 */
	public function obtenerGirosPendientesParaUsuarioCantidad($pUsuario)
	{
		// Validacion dura de parametros variables
		if ( !(is_null($pUsuario) || get_class($pUsuario) == "Usuario" || is_array($pUsuario)) )
			throw new Exception(sprintf("Error en %s.obtenerGirosPendientesParaUsuarioCantidad: el usuario solamente puede ser nulo, tipo 'Usuario' o un array.", get_class($this)));

		DB::getInstanceDBGirosPendientes()->conectar();

		try {
			// Preparo el parametro de usuarios (puede ser un array o una instancia de Usuario/null)
			$filtro_usuario = (is_array($pUsuario))
				? $pUsuario
				: ((is_null($pUsuario)) ? null : $pUsuario->id_usuario);

			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBGirosPendientes()->obtenerGirosPendientesUsuarioCantidad(
				// PK: $panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pid_pendiente
				null, null, null, null, null, null,
				// Resto
				null,        // $pgiros_pendientes
				'pendiente', // $pestado
				null,        // $pfecha_hora_entrada
				null,        // $pfecha_hora_salida
				$filtro_usuario, // $pid_usuario_firmante
				$filtro_usuario, // $pid_usuario_solicitante
				null        // $pobservaciones
			);
		} catch (Exception $e) {
			DB::getInstanceDBGirosPendientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerGirosPendientesParaUsuarioCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBGirosPendientes()->desconectar();

		return $cantidad_resultados;
	}

}
?>

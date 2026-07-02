<?php
/**
 * Capa de negocio de Transacciones de Actuación.
 *
 * @author XXXX, XXXX
 *
 */
class NGTransacActuaciones extends NGBaseClass {

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

	// ------------------------------------------------------------------------
	// ---- TransacActuacion --------------------------------------------------
	// ------------------------------------------------------------------------

	/**
	 * NGActuaciones: Obtiene una coleccion de elementos tipo TransacActuacion en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-08-10 13:50:42
	 * @param  integer (PK) id_transaccion
	 * @param  integer (PK) id_paso
	 * @param  string tipo_actuacion
	 * @param  string fecha_hora
	 * @param  integer id_usuario
	 * @param  string data
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<TransacActuacion>
	 */
	public function obtenerTransacActuaciones(
		// Parametros
		$pid_transaccion = null,
		$pid_paso = null,
		$ptipo_actuacion = null,
		$pfecha_hora = null,
		$pid_usuario = null,
		$pdata = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null)
	{
		DB::getInstanceDBTransacActuaciones()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBTransacActuaciones()->obtenerTransacActuaciones($pid_transaccion, $pid_paso, $ptipo_actuacion, $pfecha_hora, $pid_usuario, $pdata,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBTransacActuaciones()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerTransacActuaciones: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo TransacActuacion
		$resultado = $this->arrayResultToInstance($filas, 'TransacActuacion');

		DB::getInstanceDBTransacActuaciones()->desconectar();

		return $resultado;
	}

	/**
	 * NGActuaciones: Determina la cantidad de elementos tipo TransacActuacion obtenidos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-08-10 13:50:42
	 * @param  integer (PK) id_transaccion
	 * @param  integer (PK) id_paso
	 * @param  string tipo_actuacion
	 * @param  string fecha_hora
	 * @param  integer id_usuario
	 * @param  string data
	 * @return int
	 */
	public function obtenerTransacActuacionesCantidad(
		// Parametros
		$pid_transaccion = null,
		$pid_paso = null,
		$ptipo_actuacion = null,
		$pfecha_hora = null,
		$pid_usuario = null,
		$pdata = null)
	{
		DB::getInstanceDBTransacActuaciones()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBTransacActuaciones()->obtenerTransacActuacionesCantidad($pid_transaccion, $pid_paso, $ptipo_actuacion, $pfecha_hora, $pid_usuario, $pdata);
		} catch (Exception $e) {
			DB::getInstanceDBTransacActuaciones()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerTransacActuacionesCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBTransacActuaciones()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGActuaciones: Obtiene una instancia de tipo TransacActuacion en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.7 beta @ 2022-08-10 13:50:42
	 * @param  integer (PK) id_transaccion
	 * @param  integer (PK) id_paso
	 * @return TransacActuacion Instancia de TransacActuacion buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerTransacActuacion(
		// Parametros
		$pid_transaccion, $pid_paso)
	{
		if (is_null($pid_transaccion) || is_null($pid_paso))
			throw new Exception(sprintf("Error en %s.obtenerTransacActuacion: los campos clave no pueden ser nulos.", get_class($this)));

		$resultado = $this->obtenerTransacActuaciones($pid_transaccion, $pid_paso);

		if (count($resultado) == 0)
			return null;
		else if (count($resultado) == 1)
			return $resultado[0];
		else
			throw new Exception(sprintf("Error en %s.obtenerTransacActuacion: se encontr&oacute; m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
	}

	/**
	 * Guarda una instancia de tipo TransacActuacion. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.97.7 beta @ 2022-08-10 13:50:42
	 * @param  TransacActuacion $pTransacActuacion 	Instancia a guardar.
	 * @param  boolean $pRecargar 		Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return TransacActuacion               Instancia guardada.
	 */
	public function guardarTransacActuacion(TransacActuacion $pTransacActuacion, $pRecargar = true)
	{
		if (is_null($pTransacActuacion))
			throw new Exception(sprintf("Error en %s.guardarTransacActuacion: la instancia a guardar no puede ser nula.",get_class($this)));

		DB::getInstanceDBTransacActuaciones()->conectar(false); // AutoCommit: false
		DB::getInstanceDBTransacActuaciones()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$id = DB::getInstanceDBTransacActuaciones()->guardarTransacActuacion(
				$pTransacActuacion->id_transaccion,
				$pTransacActuacion->id_paso,
				$pTransacActuacion->tipo_actuacion,
				$pTransacActuacion->fecha_hora,
				$pTransacActuacion->id_usuario,
				$pTransacActuacion->data);

			DB::getInstanceDBTransacActuaciones()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBTransacActuaciones()->cancelarTransaccion();
			DB::getInstanceDBTransacActuaciones()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarTransacActuacion: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$resultado = $this->obtenerTransacActuacion($pTransacActuacion->id_transaccion, $pTransacActuacion->id_paso);
		}
		else
			$resultado = $pTransacActuacion;

		DB::getInstanceDBTransacActuaciones()->desconectar();

		if (is_null($resultado))
			throw new Exception(sprintf("Error grave en %s.guardarTransacActuacion: no se encuentra el contenido actualizado.",get_class($this)));

		return $resultado;
	}

	/**
	 * NGActuaciones: Elimina un conjunto de TransacActuaciones en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-08-10 13:50:42
	 * @param  integer (PK) id_transaccion
	 * @param  integer (PK) id_paso
	 * @param  string tipo_actuacion
	 * @param  string fecha_hora
	 * @param  integer id_usuario
	 * @param  string data
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarTransacActuaciones(
		// Parametros
		$pid_transaccion = null,
		$pid_paso = null,
		$ptipo_actuacion = null,
		$pfecha_hora = null,
		$pid_usuario = null,
		$pdata = null)
	{
		DB::getInstanceDBTransacActuaciones()->conectar(false); // AutoCommit: false
		DB::getInstanceDBTransacActuaciones()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBTransacActuaciones()->eliminarTransacActuaciones($pid_transaccion, $pid_paso, $ptipo_actuacion, $pfecha_hora, $pid_usuario, $pdata);

			DB::getInstanceDBTransacActuaciones()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBTransacActuaciones()->cancelarTransaccion();
			DB::getInstanceDBTransacActuaciones()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarTransacActuaciones: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBTransacActuaciones()->desconectar();

		return $resultado;
	}

	/**
	 * NGActuaciones: Elimina una instancia de tipo TransacActuacion en base a su identificador.
	 * GenerateClass 0.97.7 beta @ 2022-08-10 13:50:42
	 * @param  TransacActuacion $pTransacActuacion 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarTransacActuacion(TransacActuacion $pTransacActuacion)
	{
		if (is_null($pTransacActuacion))
			throw new Exception(sprintf("Error en %s.eliminarTransacActuacion: la instancia a eliminar no puede ser nula.",get_class($this)));

		DB::getInstanceDBTransacActuaciones()->conectar(false); // AutoCommit: false
		DB::getInstanceDBTransacActuaciones()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarTransacActuaciones($pTransacActuacion->id_transaccion, $pTransacActuacion->id_paso);

			if ($resultado > 1)
				throw new Exception(sprintf("Error en %s.eliminarTransacActuacion: se quiso eliminar m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));

			DB::getInstanceDBTransacActuaciones()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBTransacActuaciones()->cancelarTransaccion();
			DB::getInstanceDBTransacActuaciones()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarTransacActuacion: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBTransacActuaciones()->desconectar();

		return ($resultado == 1);
	}

	/**
	 * [obtenerTransacActuacionesNuevoIdTransaccion description]
	 * @return [type] [description]
	 */
	public function obtenerTransacActuacionesNuevoIdTransaccion()
	{
		DB::getInstanceDBTransacActuaciones()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$nuevo_id_transaccion = DB::getInstanceDBTransacActuaciones()->obtenerTransacActuacionesNuevoIdTransaccion();
		} catch (Exception $e) {
			DB::getInstanceDBTransacActuaciones()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerTransacActuacionesNuevoIdTransaccion: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBTransacActuaciones()->desconectar();

		return $nuevo_id_transaccion;
	}

	/**
	 * [generarTransacParaActuacion description]
	 * @param  Actuacion $pActuacion  [description]
	 * @param  integer   $pid_usuario [description]
	 * @return [type]                 [description]
	 */
	public function generarTransacParaActuacion (Actuacion $pActuacion, $pid_usuario = 0)
	{
		// En una única transacción, obtengo el nuevo ID y lo guardo en la DB.

		DB::getInstanceDBTransacActuaciones()->conectar(false); // AutoCommit: false
		DB::getInstanceDBTransacActuaciones()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$pActuacion->id_transaccion = $this->obtenerTransacActuacionesNuevoIdTransaccion();

			// Genero todos los pasos de la transaccion
			for ($i = 0; $i < count($pActuacion->pasos); $i++) {

				// Creo una nueva instancia de TransacActuacion y la persisto
				$this->guardarTransacActuacion(
					new TransacActuacion(
						$pActuacion->id_transaccion,
						$i,
						$pActuacion->obtenerTipoDeClaseActuacion(),
						'CURRENT_TIMESTAMP',
						$pid_usuario,
						null
					)
				);

				// Propago el id de transaccion a todos los pasos
				$pActuacion->getPaso($i)->id_transaccion = $pActuacion->id_transaccion;
			}

			DB::getInstanceDBTransacActuaciones()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBTransacActuaciones()->cancelarTransaccion();
			DB::getInstanceDBTransacActuaciones()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarTransacActuaciones: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBTransacActuaciones()->desconectar();

		// Devolvemos la actuacion actualizada
		return $pActuacion;
	}
}

?>

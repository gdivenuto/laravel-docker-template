<?php
/**
 * Capa de negocio de PasoActuaciones.
 * Actualmente la capa funciona simil a un patrón 'factory' contra las
 * capas de negocio específicas de cada tipo de PasoActuacion.
 *
 * @author XXXX, XXXX
 *
 */
class NGPasoActuaciones extends NGBaseClass {

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
	// ---- Asignar Datos a Paso Actuacion ------------------------------------
	// ------------------------------------------------------------------------

	/**
	 * Proxy para delegar la lógica de obtencion de datos para que un paso determinado
	 * disponga de todo lo necesario para generar su vista, por ejemplo, una consulta
	 * a la BD con los posibles firmantes.
	 * @param  Actuacion     $actuacion [description]
	 * @param  PasoActuacion $paso   [description]
	 * @param  Usuario       $pUsuario  [description]
	 */
	public function asignarDatosAPasoActuacion(Actuacion $actuacion, PasoActuacion $paso, Usuario $pUsuario)
	{
		$capa_negocio = sprintf('NG%s', get_class($paso));
		$capa_alias = sprintf('getInstanceNG%s', get_class($paso));
		if (class_exists($capa_negocio))
			NG::$capa_alias()->asignarDatosAPasoActuacion($actuacion, $paso, $pUsuario);
	}

	// ------------------------------------------------------------------------
	// ---- Procesar Paso Actuacion -------------------------------------------
	// ------------------------------------------------------------------------

	/**
	 * Proxy que se encarga de tomar un paso en particular y ejecutar su funcion
	 * de validacion y procesamiento (guardar transaccion) en base a los parametros
	 * recopilados.
	 * @param  Actuacion     $actuacion [description]
	 * @param  PasoActuacion $paso   [description]
	 * @param  Usuario       $pUsuario  [description]
	 * @param  Array         $params Parámetros del paso, por referencia (esto permite modificar los parametros desde el procesamiento del paso).
	 * @return Array                 Array de errores detectados; si es '[]', no hay errores.
	 */
	public function procesarPasoActuacion(Actuacion $actuacion, PasoActuacion $paso, Usuario $pUsuario, &$params)
	{
		$capa_negocio = sprintf('NG%s', get_class($paso));
		$capa_alias = sprintf('getInstanceNG%s', get_class($paso));
		$resultado = (class_exists($capa_negocio))
			? NG::$capa_alias()->procesarPasoActuacion($actuacion, $paso, $pUsuario, $params)
			: [];

		// Si el resultado del procesamiento es correcto, entonces guardo en la
		// transaccion los datos recopilados
		if (count($resultado) == 0) {
			$trans_actuacion = NG::transacActuaciones()->obtenerTransacActuacion($paso->id_transaccion, $paso->id_paso);
			// Si la trasanccion es nula, algo se rompio
			if (is_null($trans_actuacion))
				return [sprintf('Transacción inexistente. Id Transaccion: %d, Id Paso: %d', $paso->id_transaccion, $paso->id_paso)];
			$trans_actuacion->fecha_hora = date('Y-m-d H:i:s');
			$trans_actuacion->data = json_encode($params);
			NG::transacActuaciones()->guardarTransacActuacion($trans_actuacion);
		}

		return $resultado;
	}
}
?>

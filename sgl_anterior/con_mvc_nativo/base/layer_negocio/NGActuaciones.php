<?php
/**
 * Capa de negocio de Actuaciones.
 * Actualmente la capa funciona simil a un patrón 'factory' contra las
 * capas de negocio específicas de cada tipo de Actuacion.
 *
 * @author XXXX, XXXX
 *
 */
class NGActuaciones extends NGBaseClass {

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
	// ---- Actuaciones -------------------------------------------------------
	// ------------------------------------------------------------------------

	/**
	 * Devuelve un nombre de clase a partir de un tipo de actuacion (utilizado generalmente
	 * como identificador de acción de controlador).
	 * @param  [type] $ptipo [description]
	 * @return [type]        [description]
	 */
	public function obtenerClaseDeTipoActuacion($ptipo_actuacion)
	{
		$aux = str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($ptipo_actuacion))));
		return sprintf('Actuacion%s', $aux);
	}

	/**
	 * Devuelve una nueva instancia de un tipo de actuacion dado en base a un parametro
	 * (que generalmente será la acción de un controlador). Ademas genera las transacciones
	 * que almacenaran los datos obtenidos a lo largo de la actuacion.
	 * @param  [type] $ptipo_actuacion [description]
	 * @param  [type] $pparametros     [description]
	 * @param  [type] $pid_usuario     [description]
	 * @return [type]                  [description]
	 */
	public function nuevaActuacion($ptipo_actuacion, $pparametros, $pid_usuario)
	{
		$clase_actuacion = $this->obtenerClaseDeTipoActuacion($ptipo_actuacion);

		if (!class_exists($clase_actuacion))
			throw new Exception(sprintf("Error en %s.nuevaActuacion: la clase '%s' no existe.", get_class($this), $clase_actuacion));

		// Creo la actuación, generando en la DB la transaccion de todos los pasos
		$actuacion = NG::transacActuaciones()->generarTransacParaActuacion(new $clase_actuacion(), $pid_usuario);

		// Asocio los parametros a la actuacion.
		$actuacion->parametros = $pparametros;

		return $actuacion;
	}

	// ------------------------------------------------------------------------
	// ---- Inicialización de Actuaciones -------------------------------------
	// ------------------------------------------------------------------------

	/**
	 * Proxy que se encarga de tomar una actuacion y ejecutar su funcion de
	 * inicialización.
	 * @param  Actuacion    $actuacion Actuacion a procesar
	 * @param  Usuario|null $pUsuario  Usuario asociado a la ejecución de la actuacion.
	 * @return Array                Lista de errores encontrados durante el procesamiento.
	 */
	public function inicializarActuacion(Actuacion $actuacion, Usuario $pUsuario = null)
	{
		$capa_negocio = sprintf('NG%s', get_class($actuacion));
		$capa_alias = sprintf('getInstanceNG%s', get_class($actuacion));
		return (class_exists($capa_negocio))
			? NG::$capa_alias()->inicializarActuacion($actuacion, $pUsuario)
			: [];
	}

	// ------------------------------------------------------------------------
	// ---- Verificación de Actuaciones ---------------------------------------
	// ------------------------------------------------------------------------

	/**
	 * Proxy que se encarga de tomar una actuacion y ejecutar su funcion de
	 * verificación.
	 * @param  Actuacion    $actuacion Actuacion a verificar.
	 * @param  Usuario|null $pUsuario  Usuario asociado a la verificación de la actuacion.
	 * @return Array                   Lista de errores encontrados durante la verificación.
	 */
	public function verificarActuacion(Actuacion $actuacion, Usuario $pUsuario = null)
	{
		$capa_negocio = sprintf('NG%s', get_class($actuacion));
		$capa_alias = sprintf('getInstanceNG%s', get_class($actuacion));
		return (class_exists($capa_negocio))
			? NG::$capa_alias()->verificarActuacion($actuacion, $pUsuario)
			: [];
	}

	// ------------------------------------------------------------------------
	// ---- Procesamiento de Actuaciones --------------------------------------
	// ------------------------------------------------------------------------

	/**
	 * Proxy que se encarga de tomar una actuacion y ejecutar su funcion de validacion
	 * y procesamiento en base a las transacciones generadas durante el wizard.
	 * @param  Actuacion    $actuacion Actuacion a procesar
	 * @param  Usuario|null $pUsuario  Usuario asociado a la ejecución de la actuacion.
	 * @return Array                Lista de errores encontrados durante el procesamiento.
	 */
	public function procesarActuacion(Actuacion $actuacion, Usuario $pUsuario = null)
	{
		$capa_negocio = sprintf('NG%s', get_class($actuacion));
		$capa_alias = sprintf('getInstanceNG%s', get_class($actuacion));
		if (class_exists($capa_negocio)) {
			$errores = NG::$capa_alias()->procesarActuacion($actuacion, $pUsuario);

			// Si no hay errores, realizo la auditoria obteniendo el detalle de la actuacion.
			if (count($errores) == 0) {
				try {
					NG::auditorias()->auditarActuacion($actuacion, $pUsuario);
				} catch (Exception $e) {
					$errores[] = sprintf('Error al realizar la auditoria de la actuación: %s', $e->getMessage());
				}
			}
		} else {
			// Si no existe la capa de negocio para la actuacion, no hay errores...
			$errores = [];
		}

		return $errores;
	}
}
?>

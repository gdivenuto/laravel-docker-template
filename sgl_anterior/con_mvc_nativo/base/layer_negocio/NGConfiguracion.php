<?php
/**
 * Capa de negocio de Configuracion para Kraken.
 *
 * @author XXXX
 *
 */
class NGConfiguracion extends NGBaseClass {

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
	 * NGConfiguracion: Obtiene una coleccion de elementos tipo Parametro en base a diferentes criterios de selección.
	 * @param  string (PK) parametro
	 * @param  integer val_int
	 * @param  string val_string
	 * @param  string val_datetime
	 * @param  string val_text
	 * @param  float val_double
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<Parametro>
	 */
	public function obtenerParametros(
		// Parametros
		$pparametro = null,
		$pval_int = null,
		$pval_string = null,
		$pval_datetime = null,
		$pval_text = null,
		$pval_double = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null)
	{
		DB::getInstanceDBConfiguracion()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBConfiguracion()->obtenerParametros($pparametro, $pval_int, $pval_string, $pval_datetime, $pval_text, $pval_double,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBConfiguracion()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerParametros: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Parametro
		$resultado = $this->arrayResultToInstance($filas, 'Parametro');

		DB::getInstanceDBConfiguracion()->desconectar();

		return $resultado;
	}

	/**
	 * NGConfiguracion: Determina la cantidad de elementos tipo Parametro obtenidos en base a diferentes criterios de selección.
	 * @param  string (PK) parametro
	 * @param  integer val_int
	 * @param  string val_string
	 * @param  string val_datetime
	 * @param  string val_text
	 * @param  float val_double
	 * @return int
	 */
	public function obtenerParametrosCantidad(
		// Parametros
		$pparametro = null,
		$pval_int = null,
		$pval_string = null,
		$pval_datetime = null,
		$pval_text = null,
		$pval_double = null)
	{
		DB::getInstanceDBConfiguracion()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBConfiguracion()->obtenerParametrosCantidad($pparametro, $pval_int, $pval_string, $pval_datetime, $pval_text, $pval_double);
		} catch (Exception $e) {
			DB::getInstanceDBConfiguracion()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerParametrosCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBConfiguracion()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGConfiguracion: Obtiene una instancia de tipo Parametro en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * @param  string (PK) parametro
	 * @return Parametro Instancia de Parametro buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerParametro(
		// Parametros
		$pparametro)
	{
		if (is_null($pparametro))
			throw new Exception(sprintf("Error en %s.obtenerParametro: los campos clave no pueden ser nulos.", get_class($this)));

		$resultado = $this->obtenerParametros($pparametro);

		if (count($resultado) == 0)
			return null;
		else if (count($resultado) == 1)
			return $resultado[0];
		else
			throw new Exception(sprintf("Error en %s.obtenerParametro: se encontr&oacute; m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
	}

	/**
	 * NGConfiguracion: Guarda una instancia de tipo Parametro. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * @param  Parametro $pParametro 	Instancia a guardar.
	 * @param  boolean $pRecargar 		Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return Parametro               Instancia guardada.
	 */
	public function guardarParametro(Parametro $pParametro, $pRecargar = true)
	{
		if (is_null($pParametro))
			throw new Exception(sprintf("Error en %s.guardarParametro: la instancia a guardar no puede ser nula.",get_class($this)));

		DB::getInstanceDBConfiguracion()->conectar(false); // AutoCommit: false
		DB::getInstanceDBConfiguracion()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$id = DB::getInstanceDBConfiguracion()->guardarParametro(
				$pParametro->parametro,
				$pParametro->val_int,
				$pParametro->val_string,
				$pParametro->val_datetime,
				$pParametro->val_text,
				$pParametro->val_double);

			DB::getInstanceDBConfiguracion()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBConfiguracion()->cancelarTransaccion();
			DB::getInstanceDBConfiguracion()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarParametro: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$resultado = $this->obtenerParametro($pParametro->parametro);
		}
		else
			$resultado = $pParametro;

		DB::getInstanceDBConfiguracion()->desconectar();

		if (is_null($resultado))
			throw new Exception(sprintf("Error grave en %s.guardarParametro: no se encuentra el contenido actualizado.",get_class($this)));

		return $resultado;
	}

	/**
	 * NGConfiguracion: Elimina una instancia de la clase Parametro en la base de datos.
	 * @param  integer $pparametro
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarParametros($pparametro)
	{
		DB::getInstanceDBConfiguracion()->conectar(false); // AutoCommit: false
		DB::getInstanceDBConfiguracion()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBConfiguracion()->eliminarParametros($pparametro, $pval_int, $pval_string, $pval_datetime, $pval_text, $pval_double);

			DB::getInstanceDBConfiguracion()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBConfiguracion()->cancelarTransaccion();
			DB::getInstanceDBConfiguracion()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarParametros: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBConfiguracion()->desconectar();

		return $resultado;
	}

	/**
	 * NGConfiguracion: Elimina una instancia de tipo Parametro en base a su identificador.
	 * GenerateClass 0.97.5 beta @ 2016-09-20 10:56:46
	 * @param  Parametro $pParametro 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarParametro(Parametro $pParametro)
	{
		if (is_null($pParametro))
			throw new Exception(sprintf("Error en %s.eliminarParametro: la instancia a eliminar no puede ser nula.",get_class($this)));

		DB::getInstanceDBConfiguracion()->conectar(false); // AutoCommit: false
		DB::getInstanceDBConfiguracion()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarParametros($pParametro->parametro);

			if ($resultado > 1)
				throw new Exception(sprintf("Error en %s.eliminarParametro: se quiso eliminar m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));

			DB::getInstanceDBConfiguracion()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBConfiguracion()->cancelarTransaccion();
			DB::getInstanceDBConfiguracion()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarParametro: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBConfiguracion()->desconectar();

		return ($resultado == 1);
	}

	/**
	 * NGConfiguracion: Obtiene el titulo de la aplicacion.
	 * @return [type] [description]
	 */
	public function obtenerTituloAplicacion()
	{
		$parametro = $this->obtenerParametro('app_title');

		if (is_null($parametro))
			return KRAKEN_DEFAULT_APP_TITLE;
		else
			return $parametro->val_string;
	}

	/**
	 * NGConfiguracion: Obtiene el autor de la aplicacion.
	 * @return [type] [description]
	 */
	public function obtenerAutorAplicacion()
	{
		$parametro = $this->obtenerParametro('app_author');

		if (is_null($parametro))
			return KRAKEN_DEFAULT_APP_AUTHOR;
		else
			return $parametro->val_string;
	}
}
?>

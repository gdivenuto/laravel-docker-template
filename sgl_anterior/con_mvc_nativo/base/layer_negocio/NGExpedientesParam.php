<?php
/**
 * Capa de negocio de tablas paramétricas de Expedientes.
 *
 * @author XXXX
 *
 */

class NGExpedientesParam extends NGBaseClass {

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

	// ************************************************************************
	// Codcategorias
	//
	// 07/01/2022 XXXX: se retira el campo codigo_categoria
	// ************************************************************************

	/**
	 * NGExpedientesParam: Obtiene una coleccion de elementos tipo Codcategoria en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:49
	 *
	 * 07/01/2022 XXXX, se retiró pcodigo_categoria
	 *
	 * @param  integer (PK) id_codcategoria
	 * @param  string descripcion_categoria
	 * @param  string vigencia_desde_categoria
	 * @param  string vigencia_hasta_categoria
	 * @param  string habilitado_categoria
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<Codcategoria>
	 */
	public function obtenerCodcategorias(
		// Parametros
		$pid_codcategoria = null,
		$pdescripcion_categoria = null,
		$pvigencia_desde_categoria = null,
		$pvigencia_hasta_categoria = null,
		$phabilitado_categoria = null,
		$pid_usuario = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null)
	{
		DB::getInstanceDBExpedientesParam()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBExpedientesParam()->obtenerCodcategorias(
				$pid_codcategoria,
				$pdescripcion_categoria,
				$pvigencia_desde_categoria,
				$pvigencia_hasta_categoria,
				$phabilitado_categoria,
				$pid_usuario,
				$pOrdenColumnas,
				$pLimiteCantidad,
				$pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerCodcategorias: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Codcategoria
		$resultado = $this->arrayResultToInstance($filas, 'Codcategoria');

		DB::getInstanceDBExpedientesParam()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientesParam: Determina la cantidad de elementos tipo Codcategoria obtenidos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:49
	 *
	 * 07/01/2022 XXXX, se retiró pcodigo_categoria
	 *
	 * @param  integer (PK) id_codcategoria
	 * @param  string descripcion_categoria
	 * @param  string vigencia_desde_categoria
	 * @param  string vigencia_hasta_categoria
	 * @param  string habilitado_categoria
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerCodcategoriasCantidad(
		// Parametros
		$pid_codcategoria = null,
		$pdescripcion_categoria = null,
		$pvigencia_desde_categoria = null,
		$pvigencia_hasta_categoria = null,
		$phabilitado_categoria = null,
		$pid_usuario = null)
	{
		DB::getInstanceDBExpedientesParam()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBExpedientesParam()->obtenerCodcategoriasCantidad(
				$pid_codcategoria,
				$pdescripcion_categoria,
				$pvigencia_desde_categoria,
				$pvigencia_hasta_categoria,
				$phabilitado_categoria,
				$pid_usuario);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerCodcategoriasCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesParam()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGExpedientesParam: Obtiene una instancia de tipo Codcategoria en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:49
	 * @param  integer (PK) id_codcategoria
	 * @return Codcategoria Instancia de Codcategoria buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerCodcategoria(
		// Parametros
		$pid_codcategoria)
	{
		if (is_null($pid_codcategoria))
			throw new Exception(sprintf("Error en %s.obtenerCodcategoria: los campos clave no pueden ser nulos.", get_class($this)));

		$resultado = $this->obtenerCodcategorias($pid_codcategoria);

		if (count($resultado) == 0)
			return null;
		else if (count($resultado) == 1)
			return $resultado[0];
		else
			throw new Exception(sprintf("Error en %s.obtenerCodcategoria: se encontr&oacute; m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
	}

	/**
	 * Guarda una instancia de tipo Codcategoria. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:49
	 *
	 * 07/01/2022 XXXX, se retiró $pCodcategoria->codigo_categoria
	 *
	 * @param  Codcategoria $pCodcategoria 	Instancia a guardar.
	 * @param  boolean $pRecargar 		Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return Codcategoria               Instancia guardada.
	 */
	public function guardarCodcategoria(Codcategoria $pCodcategoria, $pRecargar = true)
	{
		if (is_null($pCodcategoria))
			throw new Exception(sprintf("Error en %s.guardarCodcategoria: la instancia a guardar no puede ser nula.",get_class($this)));

		DB::getInstanceDBExpedientesParam()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientesParam()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$audit_operacion = (is_null($this->obtenerCodestado($pCodcategoria->id_codcategoria))) ? Auditoria::OP_ALTA : Auditoria::OP_MODIFICA;

			$id = DB::getInstanceDBExpedientesParam()->guardarCodcategoria(
				$pCodcategoria->id_codcategoria,
				$pCodcategoria->descripcion_categoria,
				$pCodcategoria->vigencia_desde_categoria,
				$pCodcategoria->vigencia_hasta_categoria,
				$pCodcategoria->habilitado_categoria,
				$pCodcategoria->id_usuario);

			// Auditoria
			NG::auditorias()->auditarComoExpediente(
				null, null, $audit_operacion, 'hcd.expe_codcategoria',  null, null,
				sprintf('Se ha guardado la codificadora de categor&iacute;a %s, Id: %d',
						$pCodcategoria->descripcion_categoria,
						$pCodcategoria->id_codcategoria)
			);

			DB::getInstanceDBExpedientesParam()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->cancelarTransaccion();
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarCodcategoria: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$pCodcategoria->id_codcategoria = $id; // Actualizo con el valor autogenerado.
			$resultado = $this->obtenerCodcategoria($pCodcategoria->id_codcategoria);
		}
		else
			$resultado = $pCodcategoria;

		DB::getInstanceDBExpedientesParam()->desconectar();

		if (is_null($resultado))
			throw new Exception(sprintf("Error grave en %s.guardarCodcategoria: no se encuentra el contenido actualizado.",get_class($this)));

		return $resultado;
	}

	/**
	 * NGExpedientesParam: Elimina un conjunto de Codcategorias en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:49
	 *
	 * 07/01/2022 XXXX, se retiró $pcodigo_categoria
	 *
	 * @param  integer (PK) id_codcategoria
	 * @param  string descripcion_categoria
	 * @param  string vigencia_desde_categoria
	 * @param  string vigencia_hasta_categoria
	 * @param  string habilitado_categoria
	 * @param  integer id_usuario
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarCodcategorias(
		// Parametros
		$pid_codcategoria = null,
		$pdescripcion_categoria = null,
		$pvigencia_desde_categoria = null,
		$pvigencia_hasta_categoria = null,
		$phabilitado_categoria = null,
		$pid_usuario = null)
	{
		DB::getInstanceDBExpedientesParam()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientesParam()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBExpedientesParam()->eliminarCodcategorias(
				$pid_codcategoria,
				$pdescripcion_categoria,
				$pvigencia_desde_categoria,
				$pvigencia_hasta_categoria,
				$phabilitado_categoria,
				$pid_usuario);

			// Auditoria (si es que se elimino algo, tengo que auditar)
			if ($resultado > 0) {
				NG::auditorias()->auditarComoExpediente(
					null, null, Auditoria::OP_BAJA, 'hcd.expe_codcategoria', null, null,
					NG::auditorias()->generarMensajeEliminacion($resultado, array($pid_codcategoria))
				);
			}

			DB::getInstanceDBExpedientesParam()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->cancelarTransaccion();
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarCodcategorias: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesParam()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientesParam: Elimina una instancia de tipo Codcategoria en base a su identificador.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:49
	 * @param  Codcategoria $pCodcategoria 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarCodcategoria(Codcategoria $pCodcategoria)
	{
		if (is_null($pCodcategoria))
			throw new Exception(sprintf("Error en %s.eliminarCodcategoria: la instancia a eliminar no puede ser nula.",get_class($this)));

		DB::getInstanceDBExpedientesParam()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientesParam()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarCodcategorias($pCodcategoria->id_codcategoria);

			if ($resultado > 1)
				throw new Exception(sprintf("Error en %s.eliminarCodcategoria: se quiso eliminar m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));

			DB::getInstanceDBExpedientesParam()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->cancelarTransaccion();
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarCodcategoria: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesParam()->desconectar();

		return ($resultado == 1);
	}

	// ************************************************************************
	// Codestado
	//
	// 07/01/2022 XXXX, se retiró el parámetro $pcodigo_estado
	// ************************************************************************

	/**
	 * NGExpedientesParam: Obtiene una coleccion de elementos tipo Codestado en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:57
	 * @param  integer (PK) id_codestado
	 * @param  string nombre_estado
	 * @param  string vigencia_desde_codestado
	 * @param  string vigencia_hasta_codestado
	 * @param  string observaciones_codestado
	 * @param  string habilitado_codestado
	 * @param  integer id_usuario
	 * @param  bool tratamiento_comision
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<Codestado>
	 */
	public function obtenerCodestados(
		// Parametros
		$pid_codestado = null,
		$pnombre_estado = null,
		$pvigencia_desde_codestado = null,
		$pvigencia_hasta_codestado = null,
		$pobservaciones_codestado = null,
		$phabilitado_codestado = null,
		$pid_usuario = null,
		$ptratamiento_comision = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null)
	{
		DB::getInstanceDBExpedientesParam()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBExpedientesParam()->obtenerCodestados($pid_codestado, $pnombre_estado, $pvigencia_desde_codestado, $pvigencia_hasta_codestado, $pobservaciones_codestado, $phabilitado_codestado, $pid_usuario, $ptratamiento_comision,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerCodestados: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Codestado
		$resultado = $this->arrayResultToInstance($filas, 'Codestado');

		DB::getInstanceDBExpedientesParam()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientesParam: Determina la cantidad de elementos tipo Codestado obtenidos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:57
	 * @param  integer (PK) id_codestado
	 * @param  string nombre_estado
	 * @param  string vigencia_desde_codestado
	 * @param  string vigencia_hasta_codestado
	 * @param  string observaciones_codestado
	 * @param  string habilitado_codestado
	 * @param  integer id_usuario
	 * @param  bool tratamiento_comision
	 * @return int
	 */
	public function obtenerCodestadosCantidad(
		// Parametros
		$pid_codestado = null,
		$pnombre_estado = null,
		$pvigencia_desde_codestado = null,
		$pvigencia_hasta_codestado = null,
		$pobservaciones_codestado = null,
		$phabilitado_codestado = null,
		$pid_usuario = null,
		$ptratamiento_comision = null)
	{
		DB::getInstanceDBExpedientesParam()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBExpedientesParam()->obtenerCodestadosCantidad($pid_codestado, $pnombre_estado, $pvigencia_desde_codestado, $pvigencia_hasta_codestado, $pobservaciones_codestado, $phabilitado_codestado, $pid_usuario, $ptratamiento_comision);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerCodestadosCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesParam()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGExpedientesParam: Obtiene una instancia de tipo Codestado en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:57
	 * @param  integer (PK) id_codestado
	 * @return Codestado Instancia de Codestado buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerCodestado(
		// Parametros
		$pid_codestado)
	{
		if (is_null($pid_codestado))
			throw new Exception(sprintf("Error en %s.obtenerCodestado: los campos clave no pueden ser nulos.", get_class($this)));

		$resultado = $this->obtenerCodestados($pid_codestado);

		if (count($resultado) == 0)
			return null;
		else if (count($resultado) == 1)
			return $resultado[0];
		else
			throw new Exception(sprintf("Error en %s.obtenerCodestado: se encontr&oacute; m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
	}

	/**
	 * Guarda una instancia de tipo Codestado. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:57
	 * @param  Codestado $pCodestado 	Instancia a guardar.
	 * @param  boolean $pRecargar 		Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return Codestado               Instancia guardada.
	 */
	public function guardarCodestado(Codestado $pCodestado, $pRecargar = true)
	{
		if (is_null($pCodestado))
			throw new Exception(sprintf("Error en %s.guardarCodestado: la instancia a guardar no puede ser nula.",get_class($this)));

		DB::getInstanceDBExpedientesParam()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientesParam()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$audit_operacion = (is_null($this->obtenerCodestado($pCodestado->id_codestado))) ? Auditoria::OP_ALTA : Auditoria::OP_MODIFICA;

			$id = DB::getInstanceDBExpedientesParam()->guardarCodestado(
				$pCodestado->id_codestado,
				$pCodestado->nombre_estado,
				$pCodestado->vigencia_desde_codestado,
				$pCodestado->vigencia_hasta_codestado,
				$pCodestado->observaciones_codestado,
				$pCodestado->habilitado_codestado,
				$pCodestado->id_usuario,
				$pCodestado->tratamiento_comision);

			// Auditoria
			NG::auditorias()->auditarComoExpediente(
				null, null, $audit_operacion, 'hcd.expe_codestados',  null, null,
				sprintf('Se ha guardado la codificadora de estado %s, Id: %d', $pCodestado->nombre_estado, $pCodestado->id_codestado)
			);

			DB::getInstanceDBExpedientesParam()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->cancelarTransaccion();
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarCodestado: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$pCodestado->id_codestado = $id; // Actualizo con el valor autogenerado.
			$resultado = $this->obtenerCodestado($pCodestado->id_codestado);
		}
		else
			$resultado = $pCodestado;

		DB::getInstanceDBExpedientesParam()->desconectar();

		if (is_null($resultado))
			throw new Exception(sprintf("Error grave en %s.guardarCodestado: no se encuentra el contenido actualizado.",get_class($this)));

		return $resultado;
	}

	/**
	 * NGExpedientesParam: Elimina un conjunto de Codestados en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:57
	 * @param  integer (PK) id_codestado
	 * @param  string nombre_estado
	 * @param  string vigencia_desde_codestado
	 * @param  string vigencia_hasta_codestado
	 * @param  string observaciones_codestado
	 * @param  string habilitado_codestado
	 * @param  integer id_usuario
	 * @param  bool tratamiento_comision
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarCodestados(
		// Parametros
		$pid_codestado = null,
		$pnombre_estado = null,
		$pvigencia_desde_codestado = null,
		$pvigencia_hasta_codestado = null,
		$pobservaciones_codestado = null,
		$phabilitado_codestado = null,
		$pid_usuario = null,
		$ptratamiento_comision = null)
	{
		DB::getInstanceDBExpedientesParam()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientesParam()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBExpedientesParam()->eliminarCodestados($pid_codestado, $pnombre_estado, $pvigencia_desde_codestado, $pvigencia_hasta_codestado, $pobservaciones_codestado, $phabilitado_codestado, $pid_usuario, $ptratamiento_comision);

			// Auditoria (si es que se elimino algo, tengo que auditar)
			if ($resultado > 0) {
				NG::auditorias()->auditarComoExpediente(
					null, null, Auditoria::OP_BAJA, 'hcd.expe_codestados', null, null,
					NG::auditorias()->generarMensajeEliminacion($resultado, array($pid_codestado))
				);
			}

			DB::getInstanceDBExpedientesParam()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->cancelarTransaccion();
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarCodestados: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesParam()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientesParam: Elimina una instancia de tipo Codestado en base a su identificador.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:57
	 * @param  Codestado $pCodestado 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarCodestado(Codestado $pCodestado)
	{
		if (is_null($pCodestado))
			throw new Exception(sprintf("Error en %s.eliminarCodestado: la instancia a eliminar no puede ser nula.",get_class($this)));

		DB::getInstanceDBExpedientesParam()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientesParam()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarCodestados($pCodestado->id_codestado);

			if ($resultado > 1)
				throw new Exception(sprintf("Error en %s.eliminarCodestado: se quiso eliminar m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));

			DB::getInstanceDBExpedientesParam()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->cancelarTransaccion();
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarCodestado: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesParam()->desconectar();

		return ($resultado == 1);
	}

	// ************************************************************************
	// Codtema
	//
	// 07/01/2022 XXXX, se retiró el parámetro "pcodigo_tema"
	// ************************************************************************

	/**
	 * NGExpedientesParam: Obtiene una coleccion de elementos tipo Codtema en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:05
	 * @param  integer (PK) id_codtema
	 * @param  string descripcion_tema
	 * @param  string vigencia_desde_tema
	 * @param  string vigencia_hasta_tema
	 * @param  string habilitado_tema
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<Codtema>
	 */
	public function obtenerCodtemas(
		// Parametros
		$pid_codtema = null,
		$pdescripcion_tema = null,
		$pvigencia_desde_tema = null,
		$pvigencia_hasta_tema = null,
		$phabilitado_tema = null,
		$pid_usuario = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null)
	{
		DB::getInstanceDBExpedientesParam()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBExpedientesParam()->obtenerCodtemas($pid_codtema, $pdescripcion_tema, $pvigencia_desde_tema, $pvigencia_hasta_tema, $phabilitado_tema, $pid_usuario,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerCodtemas: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Codtema
		$resultado = $this->arrayResultToInstance($filas, 'Codtema');

		DB::getInstanceDBExpedientesParam()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientesParam: Determina la cantidad de elementos tipo Codtema obtenidos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:05
	 * @param  integer (PK) id_codtema
	 * @param  string descripcion_tema
	 * @param  string vigencia_desde_tema
	 * @param  string vigencia_hasta_tema
	 * @param  string habilitado_tema
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerCodtemasCantidad(
		// Parametros
		$pid_codtema = null,
		$pdescripcion_tema = null,
		$pvigencia_desde_tema = null,
		$pvigencia_hasta_tema = null,
		$phabilitado_tema = null,
		$pid_usuario = null)
	{
		DB::getInstanceDBExpedientesParam()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBExpedientesParam()->obtenerCodtemasCantidad($pid_codtema, $pdescripcion_tema, $pvigencia_desde_tema, $pvigencia_hasta_tema, $phabilitado_tema, $pid_usuario);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerCodtemasCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesParam()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGExpedientesParam: Obtiene una instancia de tipo Codtema en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:05
	 * @param  integer (PK) id_codtema
	 * @return Codtema Instancia de Codtema buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerCodtema(
		// Parametros
		$pid_codtema)
	{
		if (is_null($pid_codtema))
			throw new Exception(sprintf("Error en %s.obtenerCodtema: los campos clave no pueden ser nulos.", get_class($this)));

		$resultado = $this->obtenerCodtemas($pid_codtema);

		if (count($resultado) == 0)
			return null;
		else if (count($resultado) == 1)
			return $resultado[0];
		else
			throw new Exception(sprintf("Error en %s.obtenerCodtema: se encontr&oacute; m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
	}

	/**
	 * Guarda una instancia de tipo Codtema. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:05
	 * @param  Codtema $pCodtema 	Instancia a guardar.
	 * @param  boolean $pRecargar 		Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return Codtema               Instancia guardada.
	 */
	public function guardarCodtema(Codtema $pCodtema, $pRecargar = true)
	{
		if (is_null($pCodtema))
			throw new Exception(sprintf("Error en %s.guardarCodtema: la instancia a guardar no puede ser nula.",get_class($this)));

		DB::getInstanceDBExpedientesParam()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientesParam()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$audit_operacion = (is_null($this->obtenerCodtema($pCodtema->id_codtema))) ? Auditoria::OP_ALTA : Auditoria::OP_MODIFICA;

			$id = DB::getInstanceDBExpedientesParam()->guardarCodtema(
				$pCodtema->id_codtema,
				$pCodtema->descripcion_tema,
				$pCodtema->vigencia_desde_tema,
				$pCodtema->vigencia_hasta_tema,
				$pCodtema->habilitado_tema,
				$pCodtema->id_usuario);

			// Auditoria
			NG::auditorias()->auditarComoExpediente(
				null, null, $audit_operacion, 'hcd.expe_codtemas',  null, null,
				sprintf('Se ha guardado la codificadora de temas %s, Id: %d', $pCodtema->descripcion_tema, $pCodtema->id_codtema)
			);

			DB::getInstanceDBExpedientesParam()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->cancelarTransaccion();
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarCodtema: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$pCodtema->id_codtema = $id; // Actualizo con el valor autogenerado.
			$resultado = $this->obtenerCodtema($pCodtema->id_codtema);
		}
		else
			$resultado = $pCodtema;

		DB::getInstanceDBExpedientesParam()->desconectar();

		if (is_null($resultado))
			throw new Exception(sprintf("Error grave en %s.guardarCodtema: no se encuentra el contenido actualizado.",get_class($this)));

		return $resultado;
	}

	/**
	 * NGExpedientesParam: Elimina un conjunto de Codtemas en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:05
	 * @param  integer (PK) id_codtema
	 * @param  string descripcion_tema
	 * @param  string vigencia_desde_tema
	 * @param  string vigencia_hasta_tema
	 * @param  string habilitado_tema
	 * @param  integer id_usuario
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarCodtemas(
		// Parametros
		$pid_codtema = null,
		$pdescripcion_tema = null,
		$pvigencia_desde_tema = null,
		$pvigencia_hasta_tema = null,
		$phabilitado_tema = null,
		$pid_usuario = null)
	{
		DB::getInstanceDBExpedientesParam()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientesParam()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBExpedientesParam()->eliminarCodtemas($pid_codtema, $pdescripcion_tema, $pvigencia_desde_tema, $pvigencia_hasta_tema, $phabilitado_tema, $pid_usuario);

			// Auditoria (si es que se elimino algo, tengo que auditar)
			if ($resultado > 0) {
				NG::auditorias()->auditarComoExpediente(
					null, null, Auditoria::OP_BAJA, 'hcd.expe_codtemas', null, null,
					NG::auditorias()->generarMensajeEliminacion($resultado, array($pid_codtema))
				);
			}

			DB::getInstanceDBExpedientesParam()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->cancelarTransaccion();
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarCodtemas: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesParam()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientesParam: Elimina una instancia de tipo Codtema en base a su identificador.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:05
	 * @param  Codtema $pCodtema 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarCodtema(Codtema $pCodtema)
	{
		if (is_null($pCodtema))
			throw new Exception(sprintf("Error en %s.eliminarCodtema: la instancia a eliminar no puede ser nula.",get_class($this)));

		DB::getInstanceDBExpedientesParam()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientesParam()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarCodtemas($pCodtema->id_codtema);

			if ($resultado > 1)
				throw new Exception(sprintf("Error en %s.eliminarCodtema: se quiso eliminar m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));

			DB::getInstanceDBExpedientesParam()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->cancelarTransaccion();
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarCodtema: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesParam()->desconectar();

		return ($resultado == 1);
	}

	// ************************************************************************
	// Lugares
	// ************************************************************************

	/**
	 * NGExpedientesParam: Obtiene una coleccion de elementos tipo Lugar en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:11
	 * @param  string (PK) tipo_grp
	 * @param  string (PK) codigo_grp
	 * @param  string descripcion_grp
	 * @param  string abreviatura_grp
	 * @param  string bloque_tipo
	 * @param  string bloque_codigo
	 * @param  string observaciones_grp
	 * @param  string vigente_Desde_grp
	 * @param  string vigente_Hasta_grp
	 * @param  string habilitado_grp
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<Lugar>
	 */
	public function obtenerLugares(
		// Parametros
		$ptipo_grp = null,
		$pcodigo_grp = null,
		$pdescripcion_grp = null,
		$pabreviatura_grp = null,
		$pbloque_tipo = null,
		$pbloque_codigo = null,
		$pobservaciones_grp = null,
		$pvigente_Desde_grp = null,
		$pvigente_Hasta_grp = null,
		$phabilitado_grp = null,
		$pid_usuario = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null)
	{
		DB::getInstanceDBExpedientesParam()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBExpedientesParam()->obtenerLugares($ptipo_grp, $pcodigo_grp, $pdescripcion_grp, $pabreviatura_grp, $pbloque_tipo, $pbloque_codigo, $pobservaciones_grp, $pvigente_Desde_grp, $pvigente_Hasta_grp, $phabilitado_grp, $pid_usuario,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerLugares: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Lugar
		$resultado = $this->arrayResultToInstance($filas, 'Lugar');

		DB::getInstanceDBExpedientesParam()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientesParam: Determina la cantidad de elementos tipo Lugar obtenidos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:11
	 * @param  string (PK) tipo_grp
	 * @param  string (PK) codigo_grp
	 * @param  string descripcion_grp
	 * @param  string abreviatura_grp
	 * @param  string bloque_tipo
	 * @param  string bloque_codigo
	 * @param  string observaciones_grp
	 * @param  string vigente_Desde_grp
	 * @param  string vigente_Hasta_grp
	 * @param  string habilitado_grp
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerLugaresCantidad(
		// Parametros
		$ptipo_grp = null,
		$pcodigo_grp = null,
		$pdescripcion_grp = null,
		$pabreviatura_grp = null,
		$pbloque_tipo = null,
		$pbloque_codigo = null,
		$pobservaciones_grp = null,
		$pvigente_Desde_grp = null,
		$pvigente_Hasta_grp = null,
		$phabilitado_grp = null,
		$pid_usuario = null)
	{
		DB::getInstanceDBExpedientesParam()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBExpedientesParam()->obtenerLugaresCantidad($ptipo_grp, $pcodigo_grp, $pdescripcion_grp, $pabreviatura_grp, $pbloque_tipo, $pbloque_codigo, $pobservaciones_grp, $pvigente_Desde_grp, $pvigente_Hasta_grp, $phabilitado_grp, $pid_usuario);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerLugaresCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesParam()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGExpedientesParam: Obtiene una instancia de tipo Lugar en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:11
	 * @param  string (PK) tipo_grp
	 * @param  string (PK) codigo_grp
	 * @return Lugar Instancia de Lugar buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerLugar(
		// Parametros
		$ptipo_grp, $pcodigo_grp)
	{
		if (is_null($ptipo_grp) || is_null($pcodigo_grp))
			throw new Exception(sprintf("Error en %s.obtenerLugar: los campos clave no pueden ser nulos.", get_class($this)));

		$resultado = $this->obtenerLugares($ptipo_grp, $pcodigo_grp);

		if (count($resultado) == 0)
			return null;
		else if (count($resultado) == 1)
			return $resultado[0];
		else
			throw new Exception(sprintf("Error en %s.obtenerLugar: se encontr&oacute; m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
	}

	/**
	 * Guarda una instancia de tipo Lugar. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:11
	 * @param  Lugar $pLugar 	Instancia a guardar.
	 * @param  boolean $pRecargar 		Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return Lugar               Instancia guardada.
	 */
	public function guardarLugar(Lugar $pLugar, $pRecargar = true)
	{
		if (is_null($pLugar))
			throw new Exception(sprintf("Error en %s.guardarLugar: la instancia a guardar no puede ser nula.",get_class($this)));

		DB::getInstanceDBExpedientesParam()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientesParam()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$audit_operacion = (is_null($this->obtenerLugar($pLugar->tipo_grp, $pLugar->codigo_grp))) ? Auditoria::OP_ALTA : Auditoria::OP_MODIFICA;

			$id = DB::getInstanceDBExpedientesParam()->guardarLugar(
				$pLugar->tipo_grp,
				$pLugar->codigo_grp,
				$pLugar->descripcion_grp,
				$pLugar->abreviatura_grp,
				$pLugar->bloque_tipo,
				$pLugar->bloque_codigo,
				$pLugar->observaciones_grp,
				$pLugar->vigente_Desde_grp,
				$pLugar->vigente_Hasta_grp,
				$pLugar->habilitado_grp,
				$pLugar->id_usuario);

			// Auditoria
			NG::auditorias()->auditarComoExpediente(
				null, null, $audit_operacion, 'hcd.expe_lugares',  null, null,
				sprintf('Se ha guardado el lugar %s, tipo: %s, c&oacute;digo: %s', $pLugar->descripcion_grp, $pLugar->tipo_grp, $pLugar->codigo_grp)
			);

			DB::getInstanceDBExpedientesParam()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->cancelarTransaccion();
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarLugar: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$resultado = $this->obtenerLugar($pLugar->tipo_grp, $pLugar->codigo_grp);
		}
		else
			$resultado = $pLugar;

		DB::getInstanceDBExpedientesParam()->desconectar();

		if (is_null($resultado))
			throw new Exception(sprintf("Error grave en %s.guardarLugar: no se encuentra el contenido actualizado.",get_class($this)));

		return $resultado;
	}

	/**
	 * NGExpedientesParam: Elimina un conjunto de Lugares en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:11
	 * @param  string (PK) tipo_grp
	 * @param  string (PK) codigo_grp
	 * @param  string descripcion_grp
	 * @param  string abreviatura_grp
	 * @param  string bloque_tipo
	 * @param  string bloque_codigo
	 * @param  string observaciones_grp
	 * @param  string vigente_Desde_grp
	 * @param  string vigente_Hasta_grp
	 * @param  string habilitado_grp
	 * @param  integer id_usuario
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarLugares(
		// Parametros
		$ptipo_grp = null,
		$pcodigo_grp = null,
		$pdescripcion_grp = null,
		$pabreviatura_grp = null,
		$pbloque_tipo = null,
		$pbloque_codigo = null,
		$pobservaciones_grp = null,
		$pvigente_Desde_grp = null,
		$pvigente_Hasta_grp = null,
		$phabilitado_grp = null,
		$pid_usuario = null)
	{
		DB::getInstanceDBExpedientesParam()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientesParam()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBExpedientesParam()->eliminarLugares($ptipo_grp, $pcodigo_grp, $pdescripcion_grp, $pabreviatura_grp, $pbloque_tipo, $pbloque_codigo, $pobservaciones_grp, $pvigente_Desde_grp, $pvigente_Hasta_grp, $phabilitado_grp, $pid_usuario);

			// Auditoria (si es que se elimino algo, tengo que auditar)
			if ($resultado > 0) {
				NG::auditorias()->auditarComoExpediente(
					null, null, Auditoria::OP_BAJA, 'hcd.expe_lugares', null, null,
					NG::auditorias()->generarMensajeEliminacion($resultado, array($ptipo_grp, $pcodigo_grp))
				);
			}

			DB::getInstanceDBExpedientesParam()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->cancelarTransaccion();
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarLugares: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesParam()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientesParam: Elimina una instancia de tipo Lugar en base a su identificador.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:11
	 * @param  Lugar $pLugar 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarLugar(Lugar $pLugar)
	{
		if (is_null($pLugar))
			throw new Exception(sprintf("Error en %s.eliminarLugar: la instancia a eliminar no puede ser nula.",get_class($this)));

		DB::getInstanceDBExpedientesParam()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientesParam()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarLugares($pLugar->tipo_grp, $pLugar->codigo_grp);

			if ($resultado > 1)
				throw new Exception(sprintf("Error en %s.eliminarLugar: se quiso eliminar m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));

			DB::getInstanceDBExpedientesParam()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->cancelarTransaccion();
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarLugar: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesParam()->desconectar();

		return ($resultado == 1);
	}

	// ************************************************************************
	// Codproyecto
	//
	// 07/01/2022 XXXX, se retiró el parámetro "pcodigo_proyecto"
	// ************************************************************************

	/**
	 * NGExpedientesParam: Obtiene una coleccion de elementos tipo Codproyecto en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:57
	 * @param  integer (PK) id_codproyecto
	 * @param  string descripcion_proyecto
	 * @param  string vigencia_desde_codproy
	 * @param  string vigencia_hasta_codproy
	 * @param  string habilitado_codproy
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<Codproyecto>
	 */
	public function obtenerCodproyectos(
		// Parametros
		$pid_codproyecto = null,
		$pdescripcion_proyecto = null,
		$pvigencia_desde_codproy = null,
		$pvigencia_hasta_codproy = null,
		$phabilitado_codproy = null,
		$pid_usuario = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null)
	{
		DB::getInstanceDBExpedientesParam()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBExpedientesParam()->obtenerCodproyectos($pid_codproyecto, $pdescripcion_proyecto, $pvigencia_desde_codproy, $pvigencia_hasta_codproy, $phabilitado_codproy, $pid_usuario,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerCodproyectos: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Codproyecto
		$resultado = $this->arrayResultToInstance($filas, 'Codproyecto');

		DB::getInstanceDBExpedientesParam()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientesParam: Determina la cantidad de elementos tipo Codproyecto obtenidos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:05
	 * @param  integer (PK) id_codproyecto
	 * @param  string descripcion_proyecto
	 * @param  string vigencia_desde_codproy
	 * @param  string vigencia_hasta_codproy
	 * @param  string habilitado_codproy
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerCodproyectosCantidad(
		// Parametros
		$pid_codproyecto = null,
		$pdescripcion_proyecto = null,
		$pvigencia_desde_codproy = null,
		$pvigencia_hasta_codproy = null,
		$phabilitado_codproy = null,
		$pid_usuario = null)
	{
		DB::getInstanceDBExpedientesParam()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBExpedientesParam()->obtenerCodproyectosCantidad($pid_codproyecto, $pdescripcion_proyecto, $pvigencia_desde_codproy, $pvigencia_hasta_codproy, $phabilitado_codproy, $pid_usuario);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerCodproyectosCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesParam()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGExpedientesParam: Obtiene una instancia de tipo Codproyecto en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:57
	 * @param  integer (PK) id_codproyecto
	 * @return Codproyecto Instancia de Codproyecto buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerCodproyecto(
		// Parametros
		$pid_codproyecto)
	{
		if (is_null($pid_codproyecto))
			throw new Exception(sprintf("Error en %s.obtenerCodproyecto: los campos clave no pueden ser nulos.", get_class($this)));

		$resultado = $this->obtenerCodproyectos($pid_codproyecto);

		if (count($resultado) == 0)
			return null;
		else if (count($resultado) == 1)
			return $resultado[0];
		else
			throw new Exception(sprintf("Error en %s.obtenerCodproyecto: se encontr&oacute; m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
	}

	/**
	 * Guarda una instancia de tipo Codproyecto. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:05
	 * @param  Codproyecto $pCodproyecto 	Instancia a guardar.
	 * @param  boolean $pRecargar 		Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return Codtema               Instancia guardada.
	 */
	public function guardarCodproyecto(Codproyecto $pCodproyecto, $pRecargar = true)
	{
		if (is_null($pCodproyecto))
			throw new Exception(sprintf("Error en %s.guardarCodproyecto: la instancia a guardar no puede ser nula.",get_class($this)));

		DB::getInstanceDBExpedientesParam()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientesParam()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$audit_operacion = (is_null($this->obtenerCodproyecto($pCodproyecto->id_codproyecto))) ? Auditoria::OP_ALTA : Auditoria::OP_MODIFICA;

			$id = DB::getInstanceDBExpedientesParam()->guardarCodproyecto(
				$pCodproyecto->id_codproyecto,
				$pCodproyecto->descripcion_proyecto,
				$pCodproyecto->vigencia_desde_codproy,
				$pCodproyecto->vigencia_hasta_codproy,
				$pCodproyecto->habilitado_codproy,
				$pCodproyecto->id_usuario);

			// Auditoria
			NG::auditorias()->auditarComoExpediente(
				null, null, $audit_operacion, 'hcd.expe_codproyectos',  null, null,
				sprintf('Se ha guardado la codificadora de proyectos %s, Id: %d', $pCodproyecto->descripcion_proyecto, $pCodproyecto->id_codproyecto)
			);

			DB::getInstanceDBExpedientesParam()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->cancelarTransaccion();
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarCodproyecto: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$pCodproyecto->id_codproyecto = $id; // Actualizo con el valor autogenerado.
			$resultado = $this->obtenerCodproyecto($pCodproyecto->id_codproyecto);
		}
		else
			$resultado = $pCodproyecto;

		DB::getInstanceDBExpedientesParam()->desconectar();

		if (is_null($resultado))
			throw new Exception(sprintf("Error grave en %s.guardarCodproyecto: no se encuentra el contenido actualizado.",get_class($this)));

		return $resultado;
	}

	/**
	 * NGExpedientesParam: Elimina un conjunto de Codproyectos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:05
	 * @param  integer (PK) id_codproyecto
	 * @param  string descripcion_proyecto
	 * @param  string vigencia_desde_codproy
	 * @param  string vigencia_hasta_codproy
	 * @param  string habilitado_codproy
	 * @param  integer id_usuario
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarCodproyectos(
		// Parametros
		$pid_codproyecto = null,
		$pdescripcion_proyecto = null,
		$pvigencia_desde_codproy = null,
		$pvigencia_hasta_codproy = null,
		$phabilitado_codproy = null,
		$pid_usuario = null)
	{
		DB::getInstanceDBExpedientesParam()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientesParam()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBExpedientesParam()->eliminarCodproyectos($pid_codproyecto, $pdescripcion_proyecto, $pvigencia_desde_codproy, $pvigencia_hasta_codproy, $phabilitado_codproy, $pid_usuario);

			// Auditoria (si es que se elimino algo, tengo que auditar)
			if ($resultado > 0) {
				NG::auditorias()->auditarComoExpediente(
					null, null, Auditoria::OP_BAJA, 'hcd.expe_codproyectos', null, null,
					NG::auditorias()->generarMensajeEliminacion($resultado, array($pid_codproyecto))
				);
			}

			DB::getInstanceDBExpedientesParam()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->cancelarTransaccion();
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarCodproyectos: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesParam()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientesParam: Elimina una instancia de tipo Codproyecto en base a su identificador.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:05
	 * @param  Codproyecto $pCodproyecto 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarCodproyecto(Codproyecto $pCodproyecto)
	{
		if (is_null($pCodproyecto))
			throw new Exception(sprintf("Error en %s.eliminarCodproyecto: la instancia a eliminar no puede ser nula.",get_class($this)));

		DB::getInstanceDBExpedientesParam()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientesParam()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarCodproyectos($pCodproyecto->id_codproyecto);

			if ($resultado > 1)
				throw new Exception(sprintf("Error en %s.eliminarCodproyecto: se quiso eliminar m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));

			DB::getInstanceDBExpedientesParam()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->cancelarTransaccion();
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarCodproyecto: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesParam()->desconectar();

		return ($resultado == 1);
	}

	// ************************************************************************
	// Datos de comisiones
	//
	// ************************************************************************
	/**
	 * NGExpedientesParam: Obtiene el mail de notificación para una determinada comision.
	 * En caso de no haber relatora asociada o no haber un mail válido, se toma el mail
	 * del área de comisiones (constante de configuracion).
	 * @param  [type] $pci_codigo [description]
	 * @return [type]             [description]
	 */
	public function obtenerMailComision($pci_codigo = null)
	{
		DB::getInstanceDBExpedientesParam()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$mail_comision = DB::getInstanceDBExpedientesParam()->obtenerMailComision($pci_codigo);

			// Como debemos obtener un mail para notificar, si la comisión no figura como comision interna, entonces el mail es el del area de comisiones
			if (is_null($mail_comision))
				$mail_comision = SGL_MAIL_AREA_COMISIONES;

		} catch (Exception $e) {
			DB::getInstanceDBExpedientesParam()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerMailComision: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesParam()->desconectar();

		return $mail_comision;
	}
}
?>

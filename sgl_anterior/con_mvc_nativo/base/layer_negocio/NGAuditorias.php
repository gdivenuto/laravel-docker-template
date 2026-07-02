<?php
/**
 * Capa de negocio de Auditorías.
 */

class NGAuditorias extends NGBaseClass {

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
	 * Obtiene de la sesion actual, el usuario validado (o null si no hay sesion iniciada).
	 * @return Usuario Usuario actual validado, o null si no hay sesion iniciada.
	 */
	private function obtenerUsuarioActual() {
		if (SessionController::get()->existe('USUARIO')) {
			return SessionController::get()->obtenerSerializado('USUARIO', new Usuario());
		} else {
			return null;
		}

	}

	/**
	 * NGAuditoria: Obtiene una coleccion de elementos tipo Auditoria en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2018-03-15 09:49:48
	 * @param  integer (PK) id_log
	 * @param  string fecha_hora_log
	 * @param  integer id_usuario
	 * @param  string operacion
	 * @param  string tabla
	 * @param  integer anio_log
	 * @param  string tipo_log
	 * @param  float numero_log
	 * @param  string digito_log
	 * @param  integer cuerpo_log
	 * @param  integer alcance_log
	 * @param  integer cuerpoalcance_log
	 * @param  integer anexoalcance_log
	 * @param  integer cuerpoanexoalcance_log
	 * @param  integer anexo_log
	 * @param  integer cuerpoanexo_log
	 * @param  string fecha_log
	 * @param  float orden_log
	 * @param  string netusername
	 * @param  string netpcname
	 * @param  string observaciones_log
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<Auditoria>
	 */
	public function obtenerAuditorias(
		// Parametros
		$pid_log = null,
		$pfecha_hora_log = null,
		$pid_usuario = null,
		$poperacion = null,
		$ptabla = null,
		$panio_log = null,
		$ptipo_log = null,
		$pnumero_log = null,
		$pdigito_log = null,
		$pcuerpo_log = null,
		$palcance_log = null,
		$pcuerpoalcance_log = null,
		$panexoalcance_log = null,
		$pcuerpoanexoalcance_log = null,
		$panexo_log = null,
		$pcuerpoanexo_log = null,
		$pfecha_log = null,
		$porden_log = null,
		$pnetusername = null,
		$pnetpcname = null,
		$pobservaciones_log = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		DB::getInstanceDBAuditorias()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBAuditorias()->obtenerAuditorias($pid_log, $pfecha_hora_log, $pid_usuario, $poperacion, $ptabla, $panio_log, $ptipo_log, $pnumero_log, $pdigito_log, $pcuerpo_log, $palcance_log, $pcuerpoalcance_log, $panexoalcance_log, $pcuerpoanexoalcance_log, $panexo_log, $pcuerpoanexo_log, $pfecha_log, $porden_log, $pnetusername, $pnetpcname, $pobservaciones_log,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBAuditorias()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerAuditorias: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Auditoria
		$resultado = $this->arrayResultToInstance($filas, 'Auditoria');

		DB::getInstanceDBAuditorias()->desconectar();

		return $resultado;
	}

	/**
	 * NGAuditoria: Determina la cantidad de elementos tipo Auditoria obtenidos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2018-03-15 09:49:48
	 * @param  integer (PK) id_log
	 * @param  string fecha_hora_log
	 * @param  integer id_usuario
	 * @param  string operacion
	 * @param  string tabla
	 * @param  integer anio_log
	 * @param  string tipo_log
	 * @param  float numero_log
	 * @param  string digito_log
	 * @param  integer cuerpo_log
	 * @param  integer alcance_log
	 * @param  integer cuerpoalcance_log
	 * @param  integer anexoalcance_log
	 * @param  integer cuerpoanexoalcance_log
	 * @param  integer anexo_log
	 * @param  integer cuerpoanexo_log
	 * @param  string fecha_log
	 * @param  float orden_log
	 * @param  string netusername
	 * @param  string netpcname
	 * @param  string observaciones_log
	 * @return int
	 */
	public function obtenerAuditoriasCantidad(
		// Parametros
		$pid_log = null,
		$pfecha_hora_log = null,
		$pid_usuario = null,
		$poperacion = null,
		$ptabla = null,
		$panio_log = null,
		$ptipo_log = null,
		$pnumero_log = null,
		$pdigito_log = null,
		$pcuerpo_log = null,
		$palcance_log = null,
		$pcuerpoalcance_log = null,
		$panexoalcance_log = null,
		$pcuerpoanexoalcance_log = null,
		$panexo_log = null,
		$pcuerpoanexo_log = null,
		$pfecha_log = null,
		$porden_log = null,
		$pnetusername = null,
		$pnetpcname = null,
		$pobservaciones_log = null) {
		DB::getInstanceDBAuditorias()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBAuditorias()->obtenerAuditoriasCantidad($pid_log, $pfecha_hora_log, $pid_usuario, $poperacion, $ptabla, $panio_log, $ptipo_log, $pnumero_log, $pdigito_log, $pcuerpo_log, $palcance_log, $pcuerpoalcance_log, $panexoalcance_log, $pcuerpoanexoalcance_log, $panexo_log, $pcuerpoanexo_log, $pfecha_log, $porden_log, $pnetusername, $pnetpcname, $pobservaciones_log);
		} catch (Exception $e) {
			DB::getInstanceDBAuditorias()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerAuditoriasCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBAuditorias()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGAuditoria: Obtiene una instancia de tipo Auditoria en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.7 beta @ 2018-03-15 09:49:48
	 * @param  integer (PK) id_log
	 * @return Auditoria Instancia de Auditoria buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerAuditoria(
		// Parametros
		$pid_log) {
		if (is_null($pid_log)) {
			throw new Exception(sprintf("Error en %s.obtenerAuditoria: los campos clave no pueden ser nulos.", get_class($this)));
		}

		$resultado = $this->obtenerAuditorias($pid_log);

		if (count($resultado) == 0) {
			return null;
		} else if (count($resultado) == 1) {
			return $resultado[0];
		} else {
			throw new Exception(sprintf("Error en %s.obtenerAuditoria: se encontr&oacute; m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
		}

	}

	/**
	 * Guarda una instancia de tipo Auditoria. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.97.7 beta @ 2018-03-15 09:49:48
	 * @param  Auditoria $pAuditoria 	Instancia a guardar.
	 * @param  boolean $pRecargar 		Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return Auditoria               Instancia guardada.
	 */
	public function guardarAuditoria(Auditoria $pAuditoria, $pRecargar = true) {
		if (is_null($pAuditoria)) {
			throw new Exception(sprintf("Error en %s.guardarAuditoria: la instancia a guardar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBAuditorias()->conectar(false); // AutoCommit: false
		DB::getInstanceDBAuditorias()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$id = DB::getInstanceDBAuditorias()->guardarAuditoria(
				$pAuditoria->id_log,
				$pAuditoria->fecha_hora_log,
				$pAuditoria->id_usuario,
				$pAuditoria->operacion,
				$pAuditoria->tabla,
				$pAuditoria->anio_log,
				$pAuditoria->tipo_log,
				$pAuditoria->numero_log,
				$pAuditoria->digito_log,
				$pAuditoria->cuerpo_log,
				$pAuditoria->alcance_log,
				$pAuditoria->cuerpoalcance_log,
				$pAuditoria->anexoalcance_log,
				$pAuditoria->cuerpoanexoalcance_log,
				$pAuditoria->anexo_log,
				$pAuditoria->cuerpoanexo_log,
				$pAuditoria->fecha_log,
				$pAuditoria->orden_log,
				$pAuditoria->netusername,
				$pAuditoria->netpcname,
				$pAuditoria->observaciones_log);

			DB::getInstanceDBAuditorias()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBAuditorias()->cancelarTransaccion();
			DB::getInstanceDBAuditorias()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarAuditoria: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$pAuditoria->id_log = $id; // Actualizo con el valor autogenerado.
			$resultado = $this->obtenerAuditoria($pAuditoria->id_log);
		} else {
			$resultado = $pAuditoria;
		}

		DB::getInstanceDBAuditorias()->desconectar();

		if (is_null($resultado)) {
			throw new Exception(sprintf("Error grave en %s.guardarAuditoria: no se encuentra el contenido actualizado.", get_class($this)));
		}

		return $resultado;
	}

	/**
	 * NGAuditoria: Elimina un conjunto de Auditorias en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2018-03-15 09:49:48
	 * @param  integer (PK) id_log
	 * @param  string fecha_hora_log
	 * @param  integer id_usuario
	 * @param  string operacion
	 * @param  string tabla
	 * @param  integer anio_log
	 * @param  string tipo_log
	 * @param  float numero_log
	 * @param  string digito_log
	 * @param  integer cuerpo_log
	 * @param  integer alcance_log
	 * @param  integer cuerpoalcance_log
	 * @param  integer anexoalcance_log
	 * @param  integer cuerpoanexoalcance_log
	 * @param  integer anexo_log
	 * @param  integer cuerpoanexo_log
	 * @param  string fecha_log
	 * @param  float orden_log
	 * @param  string netusername
	 * @param  string netpcname
	 * @param  string observaciones_log
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarAuditorias(
		// Parametros
		$pid_log = null,
		$pfecha_hora_log = null,
		$pid_usuario = null,
		$poperacion = null,
		$ptabla = null,
		$panio_log = null,
		$ptipo_log = null,
		$pnumero_log = null,
		$pdigito_log = null,
		$pcuerpo_log = null,
		$palcance_log = null,
		$pcuerpoalcance_log = null,
		$panexoalcance_log = null,
		$pcuerpoanexoalcance_log = null,
		$panexo_log = null,
		$pcuerpoanexo_log = null,
		$pfecha_log = null,
		$porden_log = null,
		$pnetusername = null,
		$pnetpcname = null,
		$pobservaciones_log = null) {
		DB::getInstanceDBAuditorias()->conectar(false); // AutoCommit: false
		DB::getInstanceDBAuditorias()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBAuditorias()->eliminarAuditorias($pid_log, $pfecha_hora_log, $pid_usuario, $poperacion, $ptabla, $panio_log, $ptipo_log, $pnumero_log, $pdigito_log, $pcuerpo_log, $palcance_log, $pcuerpoalcance_log, $panexoalcance_log, $pcuerpoanexoalcance_log, $panexo_log, $pcuerpoanexo_log, $pfecha_log, $porden_log, $pnetusername, $pnetpcname, $pobservaciones_log);

			DB::getInstanceDBAuditorias()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBAuditorias()->cancelarTransaccion();
			DB::getInstanceDBAuditorias()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarAuditorias: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBAuditorias()->desconectar();

		return $resultado;
	}

	/**
	 * NGAuditoria: Elimina una instancia de tipo Auditoria en base a su identificador.
	 * GenerateClass 0.97.7 beta @ 2018-03-15 09:49:48
	 * @param  Auditoria $pAuditoria 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarAuditoria(Auditoria $pAuditoria) {
		if (is_null($pAuditoria)) {
			throw new Exception(sprintf("Error en %s.eliminarAuditoria: la instancia a eliminar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBAuditorias()->conectar(false); // AutoCommit: false
		DB::getInstanceDBAuditorias()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarAuditorias($pAuditoria->id_log);

			if ($resultado > 1) {
				throw new Exception(sprintf("Error en %s.eliminarAuditoria: se quiso eliminar m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
			}

			DB::getInstanceDBAuditorias()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBAuditorias()->cancelarTransaccion();
			DB::getInstanceDBAuditorias()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarAuditoria: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBAuditorias()->desconectar();

		return ($resultado == 1);
	}

	/**
	 * NGAuditoria: graba una entrada de auditoria como si fuera un expediente, tomando los campos "clave"
	 * de $pRef y asignandolos a la instancia de la auditoria a insertar (si es que estos existen).
	 * @param  [Mixed] $pRef         Instancia a interpretar como expediente auditable, o conjunto de parametros de identificación del expediente. Puede ser una instancia de objeto, array o null.
	 * @param  [type] $pid_usuario        [description]
	 * @param  [type] $poperacion         [description]
	 * @param  [type] $ptabla             [description]
	 * @param  [type] $pfecha_log         [description]
	 * @param  [type] $porden_log         [description]
	 * @param  [type] $pobservaciones_log [description]
	 * @return Auditoria                     Auditoria generada.
	 */
	public function auditarComoExpediente(
		$pRef,
		$pid_usuario,
		$poperacion,
		$ptabla,
		$pfecha_log,
		$porden_log,
		$pobservaciones_log) {
		$auditoria = new Auditoria(null, null); // fuerzo el id_log y timestamp a null para que se calculen solos al insertarse

		if (!is_null($pRef)) {

			if (is_array($pRef)) {
				$propiedades = $pRef;
			} else if (is_object($pRef))
			// Obtengo las propiedades... esto es una porqueria, porque en realidad
			// el método 'obtenerPropiedades' tendria que estar desacoplado de ClaseBase...
			// pero funciona. Mas adelante habra que programar un helper para reflection...
			{
				$propiedades = $auditoria->obtenerPropiedades($pRef);
			} else {
				throw new Exception(sprintf("Error en %s.auditarComoExpediente: transacci&oacute;n no finalizada, causa: %s", get_class($this), 'La referencia debe ser una instancia de objeto, un array o null.'));
			}

			$auditoria->anio_log = (array_key_exists('anio', $propiedades)) ? $propiedades['anio'] : 0;
			$auditoria->tipo_log = (array_key_exists('tipo', $propiedades)) ? $propiedades['tipo'] : 'C'; // valor fijo
			$auditoria->numero_log = (array_key_exists('numero', $propiedades)) ? $propiedades['numero'] : 0;
			$auditoria->cuerpo_log = (array_key_exists('cuerpo', $propiedades)) ? $propiedades['cuerpo'] : 0;
			$auditoria->alcance_log = (array_key_exists('alcance', $propiedades)) ? $propiedades['alcance'] : 0;
			$auditoria->digito_log = (array_key_exists('digito', $propiedades)) ? $propiedades['digito'] : null;
			$auditoria->cuerpoalcance_log = (array_key_exists('cuerpoalcance', $propiedades)) ? $propiedades['cuerpoalcance'] : null;
			$auditoria->anexoalcance_log = (array_key_exists('anexoalcance', $propiedades)) ? $propiedades['anexoalcance'] : null;
			$auditoria->cuerpoanexoalcance_log = (array_key_exists('cuerpoanexoalcance', $propiedades)) ? $propiedades['cuerpoanexoalcance'] : null;
			$auditoria->anexo_log = (array_key_exists('anexo', $propiedades)) ? $propiedades['anexo'] : null;
			$auditoria->cuerpoanexo_log = (array_key_exists('cuerpoanexo', $propiedades)) ? $propiedades['cuerpoanexo'] : null;
		} else {
			$auditoria->anio_log = 0;
			$auditoria->tipo_log = 'C';
			$auditoria->numero_log = 0;
			$auditoria->cuerpo_log = 0;
			$auditoria->alcance_log = 0;
		}

		// Deteccion automatica de usuario...
		if (is_null($pid_usuario))
		// Verifico si hay un usuario por sesion (autenticado): cuando estoy trabajando con algun UI web
		{
			$usuario = $this->obtenerUsuarioActual();
		} else
		// Me han forzado un usuario por parametro...
		{
			$usuario = NG::seguridad()->obtenerUsuario($pid_usuario);
		}

		if (is_null($usuario)) {
			throw new Exception(sprintf("Error en %s.auditarComoExpediente: transacci&oacute;n no finalizada, causa: %s", get_class($this), 'No se ha encontrado un usuario autenticado en sesi&oacute;n, o el usuario especificado no existe.'));
		}

		// Resto de atributos 'automaticos'
		$auditoria->id_usuario = $usuario->id_usuario;
		$auditoria->netusername = $usuario->codigo_usuario;
		$auditoria->netpcname = gethostbyaddr($_SERVER['REMOTE_ADDR']);

		// Atributos pasados como parametro
		$auditoria->operacion = $poperacion;
		$auditoria->tabla = $ptabla;
		$auditoria->fecha_log = $pfecha_log;
		$auditoria->orden_log = $porden_log;
		$auditoria->observaciones_log = $pobservaciones_log;

		return $this->guardarAuditoria($auditoria, true); // guardo, recargo y devuelvo como resultado
	}

	public function generarMensajeEliminacion($cantidad, array $parametros) {
		$dummy = array();

		foreach ($parametros as $key => $value) {
			$dummy[] = (is_null($value)) ? '?' : $value;
		}

		return sprintf('Se han eliminado %d ocurrencia(s) con criterio %s', $cantidad, implode(', ', $dummy));
	}

	/**
	 * NGAuditoria: graba una entrada de auditoria al procesar una Actuación,
	 * tomando los campos "clave" de $pActuacion y asignandolos a la instancia de la auditoria a insertar
	 * (si es que estos existen).
	 * @param  Actuacion $pActuacion Instancia de Actuacion a auditar.
	 * @param  Usuario   $pUsuario   Funcionario publico que procesa la Actuacion.
	 * @return [type]                [description]
	 */
	public function auditarActuacion(Actuacion $pActuacion, Usuario $pUsuario)
	{
		// Validaciones
		if (is_null($pActuacion))
			throw new Exception('Error en %s.auditarActuacion: transacci&oacute;n no finalizada, causa: actuación nula.');

		if (is_null($pUsuario))
			throw new Exception('Error en %s.auditarActuacion: transacci&oacute;n no finalizada, causa: No se ha encontrado un usuario autenticado en sesi&oacute;n, o el usuario especificado no existe.');

		// fuerzo el id_log y timestamp a null para que se calculen solos al insertarse
		$auditoria = new Auditoria(null, null);
		$auditoria->anio_log = (array_key_exists('anio', $pActuacion->parametros)) ? $pActuacion->parametros['anio'] : 0;
		$auditoria->tipo_log = (array_key_exists('tipo', $pActuacion->parametros)) ? $pActuacion->parametros['tipo'] : 'C'; // valor fijo
		$auditoria->numero_log = (array_key_exists('numero', $pActuacion->parametros)) ? $pActuacion->parametros['numero'] : 0;
		$auditoria->cuerpo_log = (array_key_exists('cuerpo', $pActuacion->parametros)) ? $pActuacion->parametros['cuerpo'] : 0;
		$auditoria->alcance_log = (array_key_exists('alcance', $pActuacion->parametros)) ? $pActuacion->parametros['alcance'] : 0;
		$auditoria->orden_log = (array_key_exists('orden', $pActuacion->parametros)) ? $pActuacion->parametros['orden'] : null;

		// Resto de atributos 'automaticos'
		$auditoria->id_usuario = $pUsuario->id_usuario;
		$auditoria->netusername = $pUsuario->codigo_usuario;
		$auditoria->netpcname = gethostbyaddr($_SERVER['REMOTE_ADDR']);

		// Atributos pasados como parametro
		$auditoria->operacion = Auditoria::OP_PROCESA;
		$auditoria->tabla = 'hcd.expe_expedientes_elec';
		$auditoria->fecha_log = date('Y-m-d H:i:s');
		$auditoria->observaciones_log = $pActuacion->obtenerDetalleAuditoria();

		// guardo, recargo y devuelvo como resultado
		return $this->guardarAuditoria($auditoria, true);
	}
}
?>

<?php

/**
 * Capa de negocio de Firmas de Expediente Electronico.
 *
 * @author XXXX, XXXX
 *
 */
class NGFirmasExpedienteElec extends NGBaseClass {

	/**
	 * NGFirmasExpedienteElec: Obtiene una coleccion de elementos tipo FirmaExpedienteElec en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-09-28 12:37:51
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden
	 * @param  integer (PK) id_firma
	 * @param  integer id_usuario
	 * @param  integer id_usuario_solicitante
	 * @param  string estado
	 * @param  string fecha_hora_entrada
	 * @param  string fecha_hora_salida
	 * @param  string usuario_cargo
	 * @param  string usuario_dependencia
	 * @param  string observaciones
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<FirmaExpedienteElec>
	 */
	public function obtenerFirmasExpedienteElec(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden = null,
		$pid_firma = null,
		$pid_usuario = null,
		$pid_usuario_solicitante = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
		$pusuario_cargo = null,
		$pusuario_dependencia = null,
		$pobservaciones = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null)
	{
		DB::getInstanceDBFirmasExpedienteElec()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBFirmasExpedienteElec()->obtenerFirmasExpedienteElec($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden, $pid_firma, $pid_usuario, $pid_usuario_solicitante, $pestado, $pfecha_hora_entrada, $pfecha_hora_salida, $pusuario_cargo, $pusuario_dependencia, $pobservaciones,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBFirmasExpedienteElec()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerFirmasExpedienteElec: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo FirmaExpedienteElec
		$resultado = $this->arrayResultToInstance($filas, 'FirmaExpedienteElec');

		DB::getInstanceDBFirmasExpedienteElec()->desconectar();

		// Se actualizan los días pendientes de firma
		foreach($resultado as $f)
			$f->calcularDiasPendiente();

		return $resultado;
	}

	/**
	 * NGFirmasExpedienteElec: Determina la cantidad de elementos tipo FirmaExpedienteElec obtenidos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-09-28 12:37:51
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden
	 * @param  integer (PK) id_firma
	 * @param  integer id_usuario
	 * @param  integer id_usuario_solicitante
	 * @param  string estado
	 * @param  string fecha_hora_entrada
	 * @param  string fecha_hora_salida
	 * @param  string usuario_cargo
	 * @param  string usuario_dependencia
	 * @param  string observaciones
	 * @return int
	 */
	public function obtenerFirmasExpedienteElecCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden = null,
		$pid_firma = null,
		$pid_usuario = null,
		$pid_usuario_solicitante = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
		$pusuario_cargo = null,
		$pusuario_dependencia = null,
		$pobservaciones = null)
	{
		DB::getInstanceDBFirmasExpedienteElec()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBFirmasExpedienteElec()->obtenerFirmasExpedienteElecCantidad($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden, $pid_firma, $pid_usuario, $pid_usuario_solicitante, $pestado, $pfecha_hora_entrada, $pfecha_hora_salida, $pusuario_cargo, $pusuario_dependencia, $pobservaciones);
		} catch (Exception $e) {
			DB::getInstanceDBFirmasExpedienteElec()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerFirmasExpedienteElecCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBFirmasExpedienteElec()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGFirmasExpedienteElec: Obtiene una instancia de tipo FirmaExpedienteElec en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.7 beta @ 2022-09-28 12:37:51
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden
	 * @param  integer (PK) id_firma
	 * @return FirmaExpedienteElec Instancia de FirmaExpedienteElec buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerFirmaExpedienteElec(
		// Parametros
		$panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden, $pid_firma)
	{
		if (is_null($panio) || is_null($ptipo) || is_null($pnumero) || is_null($pcuerpo) || is_null($palcance) || is_null($porden) || is_null($pid_firma))
			throw new Exception(sprintf("Error en %s.obtenerFirmaExpedienteElec: los campos clave no pueden ser nulos.", get_class($this)));

		$resultado = $this->obtenerFirmasExpedienteElec($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden, $pid_firma);

		if (count($resultado) == 0)
			return null;
		else if (count($resultado) == 1)
			return $resultado[0];
		else
			throw new Exception(sprintf("Error en %s.obtenerFirmaExpedienteElec: se encontr&oacute; m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
	}

	/**
	 * Guarda una instancia de tipo FirmaExpedienteElec. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.97.7 beta @ 2022-09-28 12:37:51
	 * @param  FirmaExpedienteElec $pFirmaExpedienteElec 	Instancia a guardar.
	 * @param  boolean $pRecargar 		Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return FirmaExpedienteElec               Instancia guardada.
	 */
	public function guardarFirmaExpedienteElec(FirmaExpedienteElec $pFirmaExpedienteElec, $pRecargar = true)
	{
		if (is_null($pFirmaExpedienteElec))
			throw new Exception(sprintf("Error en %s.guardarFirmaExpedienteElec: la instancia a guardar no puede ser nula.",get_class($this)));

		DB::getInstanceDBFirmasExpedienteElec()->conectar(false); // AutoCommit: false
		DB::getInstanceDBFirmasExpedienteElec()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$id = DB::getInstanceDBFirmasExpedienteElec()->guardarFirmaExpedienteElec(
				$pFirmaExpedienteElec->anio,
				$pFirmaExpedienteElec->tipo,
				$pFirmaExpedienteElec->numero,
				$pFirmaExpedienteElec->cuerpo,
				$pFirmaExpedienteElec->alcance,
				$pFirmaExpedienteElec->orden,
				$pFirmaExpedienteElec->id_firma,
				$pFirmaExpedienteElec->id_usuario,
				$pFirmaExpedienteElec->id_usuario_solicitante,
				$pFirmaExpedienteElec->estado,
				$pFirmaExpedienteElec->fecha_hora_entrada,
				$pFirmaExpedienteElec->fecha_hora_salida,
				$pFirmaExpedienteElec->usuario_cargo,
				$pFirmaExpedienteElec->usuario_dependencia,
				$pFirmaExpedienteElec->observaciones);

			DB::getInstanceDBFirmasExpedienteElec()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBFirmasExpedienteElec()->cancelarTransaccion();
			DB::getInstanceDBFirmasExpedienteElec()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarFirmaExpedienteElec: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$resultado = $this->obtenerFirmaExpedienteElec($pFirmaExpedienteElec->anio, $pFirmaExpedienteElec->tipo, $pFirmaExpedienteElec->numero, $pFirmaExpedienteElec->cuerpo, $pFirmaExpedienteElec->alcance, $pFirmaExpedienteElec->orden, $pFirmaExpedienteElec->id_firma);
		}
		else
			$resultado = $pFirmaExpedienteElec;

		DB::getInstanceDBFirmasExpedienteElec()->desconectar();

		if (is_null($resultado))
			throw new Exception(sprintf("Error grave en %s.guardarFirmaExpedienteElec: no se encuentra el contenido actualizado.",get_class($this)));

		return $resultado;
	}

	/**
	 * NGFirmasExpedienteElec: Elimina un conjunto de FirmasExpedienteElec en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-09-28 12:37:51
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden
	 * @param  integer (PK) id_firma
	 * @param  integer id_usuario
	 * @param  integer id_usuario_solicitante
	 * @param  string estado
	 * @param  string fecha_hora_entrada
	 * @param  string fecha_hora_salida
	 * @param  string usuario_cargo
	 * @param  string usuario_dependencia
	 * @param  string observaciones
	 * @return integer Cantidad de entidades afectadas.
	 */
	private function eliminarFirmasExpedienteElec(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden = null,
		$pid_firma = null,
		$pid_usuario = null,
		$pid_usuario_solicitante = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
		$pusuario_cargo = null,
		$pusuario_dependencia = null,
		$pobservaciones = null)
	{
		DB::getInstanceDBFirmasExpedienteElec()->conectar(false); // AutoCommit: false
		DB::getInstanceDBFirmasExpedienteElec()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBFirmasExpedienteElec()->eliminarFirmasExpedienteElec($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden, $pid_firma, $pid_usuario, $pid_usuario_solicitante, $pestado, $pfecha_hora_entrada, $pfecha_hora_salida, $pusuario_cargo, $pusuario_dependencia, $pobservaciones);

			DB::getInstanceDBFirmasExpedienteElec()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBFirmasExpedienteElec()->cancelarTransaccion();
			DB::getInstanceDBFirmasExpedienteElec()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarFirmasExpedienteElec: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBFirmasExpedienteElec()->desconectar();

		return $resultado;
	}

	/**
	 * NGFirmasExpedienteElec: Elimina una instancia de tipo FirmaExpedienteElec en base a su identificador.
	 * GenerateClass 0.97.7 beta @ 2022-09-28 12:37:51
	 * @param  FirmaExpedienteElec $pFirmaExpedienteElec 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	private function eliminarFirmaExpedienteElec(FirmaExpedienteElec $pFirmaExpedienteElec)
	{
		if (is_null($pFirmaExpedienteElec))
			throw new Exception(sprintf("Error en %s.eliminarFirmaExpedienteElec: la instancia a eliminar no puede ser nula.",get_class($this)));

		DB::getInstanceDBFirmasExpedienteElec()->conectar(false); // AutoCommit: false
		DB::getInstanceDBFirmasExpedienteElec()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarFirmasExpedienteElec($pFirmaExpedienteElec->anio, $pFirmaExpedienteElec->tipo, $pFirmaExpedienteElec->numero, $pFirmaExpedienteElec->cuerpo, $pFirmaExpedienteElec->alcance, $pFirmaExpedienteElec->orden, $pFirmaExpedienteElec->id_firma);

			if ($resultado > 1)
				throw new Exception(sprintf("Error en %s.eliminarFirmaExpedienteElec: se quiso eliminar m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));

			DB::getInstanceDBFirmasExpedienteElec()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBFirmasExpedienteElec()->cancelarTransaccion();
			DB::getInstanceDBFirmasExpedienteElec()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarFirmaExpedienteElec: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBFirmasExpedienteElec()->desconectar();

		return ($resultado == 1);
	}

	/**
	 * [obtenerFirmaExpedienteElecIdSiguiente description]
	 * @param  FirmaExpedienteElec $pFirmaExpedienteElec [description]
	 * @return [type]                                    [description]
	 */
	public function obtenerFirmaExpedienteElecIdSiguiente(FirmaExpedienteElec $pFirmaExpedienteElec)
	{
		DB::getInstanceDBFirmasExpedienteElec()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$nuevo_id_firma = DB::getInstanceDBFirmasExpedienteElec()->obtenerFirmaExpedienteElecIdSiguiente(
				$pFirmaExpedienteElec->anio,
				$pFirmaExpedienteElec->tipo,
				$pFirmaExpedienteElec->numero,
				$pFirmaExpedienteElec->cuerpo,
				$pFirmaExpedienteElec->alcance,
				$pFirmaExpedienteElec->orden
			);
		} catch (Exception $e) {
			DB::getInstanceDBFirmasExpedienteElec()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerFirmaExpedienteElecIdSiguiente: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBFirmasExpedienteElec()->desconectar();

		return $nuevo_id_firma;
	}

	/**
	 * Agrega una firma única a un documento del expediente electronico.
	 * @param  FirmaExpedienteElec $pFirmaExpedienteElec [description]
	 * @return [type]                                    [description]
	 */
	public function agregarFirmaExpedienteElec(FirmaExpedienteElec $pFirmaExpedienteElec)
	{
		// En una única transacción, obtengo el nuevo ID y lo guardo en la DB.
		DB::getInstanceDBFirmasExpedienteElec()->conectar(false); // AutoCommit: false
		DB::getInstanceDBFirmasExpedienteElec()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Piso id de firma y fecha/hora
			$fecha_hora = date('Y-m-d H:i:s');
			$pFirmaExpedienteElec->id_firma = $this->obtenerFirmaExpedienteElecIdSiguiente($pFirmaExpedienteElec);
			$pFirmaExpedienteElec->fecha_hora_entrada = $fecha_hora;
			$pFirmaExpedienteElec->fecha_hora_salida = ($pFirmaExpedienteElec->estado == 'pendiente')
				? null
				: $fecha_hora;

			// Guardo
			$pFirmaExpedienteElec = $this->guardarFirmaExpedienteElec($pFirmaExpedienteElec, true);

			// Ejecuto transaccion
			DB::getInstanceDBFirmasExpedienteElec()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBFirmasExpedienteElec()->cancelarTransaccion();
			DB::getInstanceDBFirmasExpedienteElec()->desconectar();
			throw new Exception(sprintf("Error en %s.agregarFirma: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBFirmasExpedienteElec()->desconectar();

		// Devolvemos el expediente electronico actualizado
		return $pFirmaExpedienteElec;
	}

	/**
	 * [agregarFirmasDocumentoElectronico description]
	 * @param  ExpedienteElec $pExpedienteElec      [description]
	 * @param  Usuario        $pusuario_solicitante [description]
	 * @param  array          $firmantes            [description]
	 * @param  string         $pestado              [description]
	 * @param  string         $pobservaciones       [description]
	 * @return [type]                               [description]
	 */
	public function agregarFirmasDocumentoElectronico(
		ExpedienteElec $pExpedienteElec,
		Usuario $pusuario_solicitante,
		$pfirmantes = [],
		$pestado = 'pendiente',
		$pobservaciones = ''
	) {
		if (is_null($pExpedienteElec))
			throw new Exception(sprintf("Error en %s.agregarFirmasDocumentoElectronico: la instancia a del expediente electrónico no puede ser nula.",get_class($this)));

		if (is_null($pusuario_solicitante))
			throw new Exception(sprintf("Error en %s.agregarFirmasDocumentoElectronico: el usuario solicitante no puede ser nulo.",get_class($this)));

		if (count($pfirmantes) == 0)
			throw new Exception(sprintf("Error en %s.agregarFirmasDocumentoElectronico: debe especificar al menos un usuario firmante.",get_class($this)));

		$lista_id_usuario = array_map(function ($u) {
			return $u->id_usuario;
		}, $pfirmantes);

		$usuarios_firma_count = NG::seguridad()->obtenerUsuariosFirmantesCantidad(
			$lista_id_usuario, // $pid_usuario
			null, // $pcodigo_usuario
			null, // $pnombre_usuario
			null, // $piniciales_usuario
			null, // $ppassword_usuario
			true, // $phabilitado_usuario
			null, // $pconfirma_giros
			null, // $pobservaciones_usuario
			null  // $pu_legajo
		);

		if (count($pfirmantes) != $usuarios_firma_count)
			throw new Exception(sprintf("Error en %s.agregarFirmasDocumentoElectronico: algunos usuarios firmantes no tienen capacidad de firmar electrónicamente un documento.",get_class($this)));

		if ($pobservaciones == '')
			$pobservaciones = sprintf('En relación a entrada de expediente electrónico: %d-%s-%d cpo %d alc %d, orden %d', $pExpedienteElec->anio, $pExpedienteElec->tipo, $pExpedienteElec->numero, $pExpedienteElec->cuerpo, $pExpedienteElec->alcance, $pExpedienteElec->orden);

		foreach ($pfirmantes as $f) {
			$this->agregarFirmaExpedienteElec(
				new FirmaExpedienteElec(
					$pExpedienteElec->anio,
					$pExpedienteElec->tipo,
					$pExpedienteElec->numero,
					$pExpedienteElec->cuerpo,
					$pExpedienteElec->alcance,
					$pExpedienteElec->orden,
					null, // id_firma calculado automaticamente
					$f->id_usuario,                    // signatario
					$pusuario_solicitante->id_usuario, // solicitante
					$pestado,
					null, // fecha_hora_entrada automatica segun estado
					null, // fecha_hora_salida automatica segun estado
					'', // $pusuario_cargo = '',
					'', // $pusuario_dependencia = '',
					$pobservaciones
				)
			);
		}
	}

	/**
	 * Obtiene las firmas pendientes para un determinado usuario.
	 * @param  array|Usuario $pUsuario        [description]
	 * @param  array|null    $pOrdenColumnas  [description]
	 * @param  [type]  $pLimiteCantidad [description]
	 * @param  [type]  $pLimiteOffset   [description]
	 * @return [type]                   [description]
	 */
	public function obtenerFirmasPendientesUsuario(
		$pUsuario,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null
	) {
		// Validacion dura de parametros variables
		if ( !(is_null($pUsuario) || get_class($pUsuario) == "Usuario" || is_array($pUsuario)) )
			throw new Exception(sprintf("Error en %s.obtenerFirmasPendientesUsuario: el usuario solamente puede ser nulo, tipo 'Usuario' o un array.", get_class($this)));

		$orden = (is_null($pOrdenColumnas))
			? ['T.`fecha_hora_entrada` DESC']
			: $pOrdenColumnas;

		return $this->obtenerFirmasExpedienteElec(
			// Parametros
			null, // $panio
			null, // $ptipo
			null, // $pnumero
			null, // $pcuerpo
			null, // $palcance
			null, // $porden
			null, // $pid_firma
			$pUsuario->id_usuario, // $pid_usuario
			null, // $pid_usuario_solicitante
			'pendiente', // $pestado
			null, // $pfecha_hora_entrada
			null, // $pfecha_hora_salida
			null, // $pusuario_cargo
			null, // $pusuario_dependencia
			null, // $pobservaciones
			// Control de consulta
			$orden,
			$pLimiteCantidad,
			$pLimiteOffset
		);
	}

	/**
	 * Obtiene la cantidad de firmas pendientes para un determinado usuario.
	* @param  array|Usuario $pUsuario  [description]
	 * @return [type]                   [description]
	 */
	public function obtenerFirmasPendientesUsuarioCantidad($pUsuario) {

		// Validacion dura de parametros variables
		if ( !(is_null($pUsuario) || get_class($pUsuario) == "Usuario" || is_array($pUsuario)) )
			throw new Exception(sprintf("Error en %s.obtenerFirmasPendientesUsuarioCantidad: el usuario solamente puede ser nulo, tipo 'Usuario' o un array.", get_class($this)));

		return $this->obtenerFirmasExpedienteElecCantidad(
			// Parametros
			null, // $panio
			null, // $ptipo
			null, // $pnumero
			null, // $pcuerpo
			null, // $palcance
			null, // $porden
			null, // $pid_firma
			$pUsuario->id_usuario, // $pid_usuario
			null, // $pid_usuario_solicitante
			'pendiente', // $pestado
			null, // $pfecha_hora_entrada
			null, // $pfecha_hora_salida
			null, // $pusuario_cargo
			null, // $pusuario_dependencia
			null // $pobservaciones
		);
	}

	/**
	 * 2023-05-10 XXXX
	 * Obtiene todas las firmas pendientes, cuyo solicitante es un Supervisor.
	 * @param  array|Usuario $pUsuario        [description]
	 * @param  array|null    $pOrdenColumnas  [description]
	 * @param  [type]  $pLimiteCantidad [description]
	 * @param  [type]  $pLimiteOffset   [description]
	 * @return [type]                   [description]
	 */
	public function obtenerFirmasPendientesParaSupervisores(
		$pUsuario,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null
	) {
		// Validacion dura de parametros variables
		if ( !(is_null($pUsuario) || get_class($pUsuario) == "Usuario" || is_array($pUsuario)) )
			throw new Exception(sprintf("Error en %s.obtenerFirmasPendientesParaSupervisores: el usuario solamente puede ser nulo, tipo 'Usuario' o un array.", get_class($this)));

		$orden = (is_null($pOrdenColumnas))
			? ['T.`fecha_hora_entrada` DESC']
			: $pOrdenColumnas;

		return $this->obtenerFirmasExpedienteElec(
			// Parametros
			null, // $panio
			null, // $ptipo
			null, // $pnumero
			null, // $pcuerpo
			null, // $palcance
			null, // $porden
			null, // $pid_firma
			null, // $pid_usuario
			null, // 2023-05-18 $pUsuario->id_usuario, // $pid_usuario_solicitante
			'pendiente', // $pestado
			null, // $pfecha_hora_entrada
			null, // $pfecha_hora_salida
			null, // $pusuario_cargo
			null, // $pusuario_dependencia
			null, // $pobservaciones
			// Control de consulta
			$orden,
			$pLimiteCantidad,
			$pLimiteOffset
		);
	}

	/**
	 * 2023-05-10 XXXX
	 * Obtiene la cantidad de firmas pendientes, cuyo solicitante es un Supervisor.
	 * @param  array|Usuario $pUsuario  [description]
	 * @return [type]                   [description]
	 */
	public function obtenerFirmasPendientesParaSupervisoresCantidad($pUsuario) {

		// Validacion dura de parametros variables
		if ( !(is_null($pUsuario) || get_class($pUsuario) == "Usuario" || is_array($pUsuario)) )
			throw new Exception(sprintf("Error en %s.obtenerFirmasPendientesParaSupervisoresCantidad: el usuario solamente puede ser nulo, tipo 'Usuario' o un array.", get_class($this)));

		return $this->obtenerFirmasExpedienteElecCantidad(
			// Parametros
			null, // $panio
			null, // $ptipo
			null, // $pnumero
			null, // $pcuerpo
			null, // $palcance
			null, // $porden
			null, // $pid_firma
			null, // $pid_usuario
			null, // 2023-05-18 $pUsuario->id_usuario, // $pid_usuario_solicitante
			'pendiente', // $pestado
			null, // $pfecha_hora_entrada
			null, // $pfecha_hora_salida
			null, // $pusuario_cargo
			null, // $pusuario_dependencia
			null // $pobservaciones
		);
	}

}
?>

<?php

/**
 * Capa de negocio de Expedientes Electronicos.
 *
 * NOTA: los metodos de creacion/edicion/eliminacion no son disponibles por fuera
 * de la clase, por cuestiones de seguridad y para prevenir la edicion de los
 * expedientes electronicos.
 *
 * @author XXXX, XXXX
 *
 */
class NGExpedientesElec extends NGBaseClass {

	/**
	 * NGExpedientesElec: Obtiene una coleccion de elementos tipo ExpedienteElec en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-09-16 09:33:42
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden
	 * @param  string tipo_actuacion
	 * @param  string detalle
	 * @param  string documento
	 * @param  string documento_hash
	 * @param  string texto_original
	 * @param  bool dec1404
	 * @param  bool embebido
	 * @param  bool es_caratula
	 * @param  string fecha_hora
	 * @param  integer id_usuario
	 * @param  string observaciones
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<ExpedienteElec>
	 */
	public function obtenerExpedientesElec(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden = null,
		$ptipo_actuacion = null,
		$pdetalle = null,
		$pdocumento = null,
		$pdocumento_hash = null,
		$ptexto_original = null,
		$pdec1404 = null,
		$pembebido = null,
		$pes_caratula = null,
		$pfecha_hora = null,
		$pid_usuario = null,
		$pobservaciones = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null)
	{
		DB::getInstanceDBExpedientesElec()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBExpedientesElec()->obtenerExpedientesElec($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden, $ptipo_actuacion, $pdetalle, $pdocumento, $pdocumento_hash, $ptexto_original, $pdec1404, $pembebido, $pes_caratula, $pfecha_hora, $pid_usuario, $pobservaciones,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientesElec()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesElec: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo ExpedienteElec
		$resultado = $this->arrayResultToInstance($filas, 'ExpedienteElec');

		DB::getInstanceDBExpedientesElec()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientesElec: Determina la cantidad de elementos tipo ExpedienteElec obtenidos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-09-16 09:33:42
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden
	 * @param  string tipo_actuacion
	 * @param  string detalle
	 * @param  string documento
	 * @param  string documento_hash
	 * @param  string texto_original
	 * @param  bool dec1404
	 * @param  bool embebido
	 * @param  bool es_caratula
	 * @param  string fecha_hora
	 * @param  integer id_usuario
	 * @param  string observaciones
	 * @return int
	 */
	public function obtenerExpedientesElecCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden = null,
		$ptipo_actuacion = null,
		$pdetalle = null,
		$pdocumento = null,
		$pdocumento_hash = null,
		$ptexto_original = null,
		$pdec1404 = null,
		$pembebido = null,
		$pes_caratula = null,
		$pfecha_hora = null,
		$pid_usuario = null,
		$pobservaciones = null)
	{
		DB::getInstanceDBExpedientesElec()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBExpedientesElec()->obtenerExpedientesElecCantidad($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden, $ptipo_actuacion, $pdetalle, $pdocumento, $pdocumento_hash, $ptexto_original, $pdec1404, $pembebido, $pes_caratula, $pfecha_hora, $pid_usuario, $pobservaciones);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientesElec()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesElecCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesElec()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGExpedientesElec: Obtiene una instancia de tipo ExpedienteElec en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.7 beta @ 2022-09-16 09:33:42
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden
	 * @return ExpedienteElec Instancia de ExpedienteElec buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerExpedienteElec(
		// Parametros
		$panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden)
	{
		if (is_null($panio) || is_null($ptipo) || is_null($pnumero) || is_null($pcuerpo) || is_null($palcance) || is_null($porden))
			throw new Exception(sprintf("Error en %s.obtenerExpedienteElec: los campos clave no pueden ser nulos.", get_class($this)));

		$resultado = $this->obtenerExpedientesElec($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden);

		if (count($resultado) == 0)
			return null;
		else if (count($resultado) == 1)
			return $resultado[0];
		else
			throw new Exception(sprintf("Error en %s.obtenerExpedienteElec: se encontr&oacute; m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
	}

	/**
	 * Se obtiene la url de documento asociado al Expediente y número de Orden
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden
	 * @return string
	 */
	public function obtenerExpedElecUrl($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden)
	{
		$url = '';

		// Busco el documento del expediente electrónico
		$expe_elec = $this->obtenerExpedienteElec($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden);

		if ($expe_elec) {
			// Ahora verifico la existencia del documento. Casos:
			// 1. Si el doc no esta alcazado por el decreto 1404, lo muestro.
			// 2. Si esta alcanzado por el dec 1404 pero el documento existe en disco,
			//    lo muestro porque estoy en el entorno 'interno' del HCD.
			// 3. Si esta alcanzado por el dec 1404 pero el documento NO existe,
			//    muestro la plantilla (estoy en la version publica).
			$documento = PATH_KRAKEN_RESOURCES_PROYECTOS . $expe_elec->documento;
			if (! $expe_elec->dec1404) {
				if (file_exists($documento)) {
					// Caso 1
					$url = URL_KRAKEN_RESOURCES_PROYECTOS.$expe_elec->documento;
				} else {
					// Caso Especial: ERROR... el documento deberia existir y no esta...
					throw new Exception('El documento solicitado no existe! [1]');
				}
			} else {
				$url = (file_exists($documento))
					? URL_KRAKEN_RESOURCES_PROYECTOS.$expe_elec->documento // Caso 2
					: URL_SGL_DOC_FALTANTE_DEC1404;                        // Caso 3
			}

			// Agrego un random al final, para evitar caches
			$url .= sprintf('?v=%s', rand());
		} else {
			// Caso Especial: ERROR... los parametros pasados al controlador
			// son de un documento que no existe en la DB
			throw new Exception('El documento solicitado no existe! [2]');
		}

		return $url;
	}

	/**
	 * Guarda una instancia de tipo ExpedienteElec. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.97.7 beta @ 2022-09-16 09:33:42
	 * @param  ExpedienteElec $pExpedienteElec 	Instancia a guardar.
	 * @param  boolean $pRecargar 		Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return ExpedienteElec               Instancia guardada.
	 */
	protected function guardarExpedienteElec(ExpedienteElec $pExpedienteElec, $pRecargar = true)
	{
		if (is_null($pExpedienteElec))
			throw new Exception(sprintf("Error en %s.guardarExpedienteElec: la instancia a guardar no puede ser nula.",get_class($this)));

		DB::getInstanceDBExpedientesElec()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientesElec()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$id = DB::getInstanceDBExpedientesElec()->guardarExpedienteElec(
				$pExpedienteElec->anio,
				$pExpedienteElec->tipo,
				$pExpedienteElec->numero,
				$pExpedienteElec->cuerpo,
				$pExpedienteElec->alcance,
				$pExpedienteElec->orden,
				$pExpedienteElec->tipo_actuacion,
				$pExpedienteElec->detalle,
				$pExpedienteElec->documento,
				$pExpedienteElec->documento_hash,
				$pExpedienteElec->texto_original,
				$pExpedienteElec->dec1404,
				$pExpedienteElec->embebido,
				$pExpedienteElec->es_caratula,
				$pExpedienteElec->fecha_hora,
				$pExpedienteElec->id_usuario,
				$pExpedienteElec->observaciones);

			DB::getInstanceDBExpedientesElec()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientesElec()->cancelarTransaccion();
			DB::getInstanceDBExpedientesElec()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarExpedienteElec: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$resultado = $this->obtenerExpedienteElec($pExpedienteElec->anio, $pExpedienteElec->tipo, $pExpedienteElec->numero, $pExpedienteElec->cuerpo, $pExpedienteElec->alcance, $pExpedienteElec->orden);
		}
		else
			$resultado = $pExpedienteElec;

		DB::getInstanceDBExpedientesElec()->desconectar();

		if (is_null($resultado))
			throw new Exception(sprintf("Error grave en %s.guardarExpedienteElec: no se encuentra el contenido actualizado.",get_class($this)));

		return $resultado;
	}

	/**
	 * NGExpedientesElec: Elimina un conjunto de ExpedientesElec en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-09-16 09:33:42
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden
	 * @param  string tipo_actuacion
	 * @param  string detalle
	 * @param  string documento
	 * @param  string documento_hash
	 * @param  string texto_original
	 * @param  bool dec1404
	 * @param  bool embebido
	 * @param  string fecha_hora
	 * @param  integer id_usuario
	 * @param  string observaciones
	 * @return integer Cantidad de entidades afectadas.
	 */
	protected function eliminarExpedientesElec(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden = null,
		$ptipo_actuacion = null,
		$pdetalle = null,
		$pdocumento = null,
		$pdocumento_hash = null,
		$ptexto_original = null,
		$pdec1404 = null,
		$pembebido = null,
		$pes_caratula = null,
		$pfecha_hora = null,
		$pid_usuario = null,
		$pobservaciones = null)
	{
		DB::getInstanceDBExpedientesElec()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientesElec()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBExpedientesElec()->eliminarExpedientesElec($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden, $ptipo_actuacion, $pdetalle, $pdocumento, $pdocumento_hash, $ptexto_original, $pdec1404, $pembebido, $pes_caratula, $pfecha_hora, $pid_usuario, $pobservaciones);

			DB::getInstanceDBExpedientesElec()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientesElec()->cancelarTransaccion();
			DB::getInstanceDBExpedientesElec()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarExpedientesElec: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesElec()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientesElec: Elimina una instancia de tipo ExpedienteElec en base a su identificador.
	 * GenerateClass 0.97.7 beta @ 2022-09-16 09:33:42
	 * @param  ExpedienteElec $pExpedienteElec 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	protected function eliminarExpedienteElec(ExpedienteElec $pExpedienteElec)
	{
		if (is_null($pExpedienteElec))
			throw new Exception(sprintf("Error en %s.eliminarExpedienteElec: la instancia a eliminar no puede ser nula.",get_class($this)));

		DB::getInstanceDBExpedientesElec()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientesElec()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarExpedientesElec($pExpedienteElec->anio, $pExpedienteElec->tipo, $pExpedienteElec->numero, $pExpedienteElec->cuerpo, $pExpedienteElec->alcance, $pExpedienteElec->orden);

			if ($resultado > 1)
				throw new Exception(sprintf("Error en %s.eliminarExpedienteElec: se quiso eliminar m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));

			DB::getInstanceDBExpedientesElec()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientesElec()->cancelarTransaccion();
			DB::getInstanceDBExpedientesElec()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarExpedienteElec: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesElec()->desconectar();

		return ($resultado == 1);
	}

	/**
	 * [obtenerTransacActuacionesNuevoIdTransaccion description]
	 * @param  ExpedienteElec $pExpedienteElec [description]
	 * @return [type]                          [description]
	 */
	public function obtenerExpedienteElecOrdenSiguiente(ExpedienteElec $pExpedienteElec)
	{
		DB::getInstanceDBExpedientesElec()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$nuevo_orden = DB::getInstanceDBExpedientesElec()->obtenerExpedienteElecOrdenSiguiente(
				$pExpedienteElec->anio,
				$pExpedienteElec->tipo,
				$pExpedienteElec->numero,
				$pExpedienteElec->cuerpo,
				$pExpedienteElec->alcance
			);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientesElec()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedienteElecOrdenSiguiente: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesElec()->desconectar();

		return $nuevo_orden;
	}

	/**
	 * Dado una instancia de ExpedienteElec, si es una caratula, busca todos sus
	 * 'hermanos' y les deshabilita la marca de caratula.
	 * @param  ExpedienteElec $pExpedienteElec [description]
	 * @return [type]                          [description]
	 */
	public function desasignarOtrasCaratulas(ExpedienteElec $pExpedienteElec) {
		// Si el documento es caratula, actualizo el estado de la caratula anteriores
		if ($pExpedienteElec->es_caratula) {
			$caratulas = $this->obtenerExpedientesElec(
				$pExpedienteElec->anio,
				$pExpedienteElec->tipo,
				$pExpedienteElec->numero,
				$pExpedienteElec->cuerpo,
				$pExpedienteElec->alcance,
				null, // el orden se desestima porque quiero 'buscar' en todos los documentos
				null, null, null, null, null, null, null,
				true  // $pes_caratula
			);

			// Debería ser siempre una única caratula, peeeeero...
			foreach ($caratulas as $c) {
				// La 'caratula' actual se deja intacta
				if ($c->orden != $pExpedienteElec->orden) {
					$c->es_caratula = false;
					$this->guardarExpedienteElec($c, false); // guardo sin recargar para que sea mas rapido
				}
			}
		}
	}

	/**
	 * Crea una nueva entrada de Expediente Electronico, y devuelve la instancia
	 * actualizada.
	 * @param  ExpedienteElec $pExpedienteElec [description]
	 * @return [type]                          [description]
	 */
	public function agregarDocumentoElectronico(ExpedienteElec $pExpedienteElec)
	{
		// En una única transacción, obtengo el nuevo ID y lo guardo en la DB.
		DB::getInstanceDBExpedientesElec()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientesElec()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Si el documento es caratula, actualizo el estado de la caratula anteriores
			$this->desasignarOtrasCaratulas($pExpedienteElec);

			// Piso orden y fecha/hora
			$pExpedienteElec->orden = $this->obtenerExpedienteElecOrdenSiguiente($pExpedienteElec);
			$pExpedienteElec->fecha_hora = date('Y-m-d H:i:s');

			// Guardo
			$pExpedienteElec = $this->guardarExpedienteElec($pExpedienteElec, true);

			// Ejecuto transaccion
			DB::getInstanceDBExpedientesElec()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientesElec()->cancelarTransaccion();
			DB::getInstanceDBExpedientesElec()->desconectar();
			throw new Exception(sprintf("Error en %s.agregarDocumentoElectronico: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesElec()->desconectar();

		// Devolvemos el expediente electronico actualizado
		return $pExpedienteElec;
	}

	/**
	 * Crea una nueva entrada de Expediente Electronico a partir de un documento pendiente
	 * y devuelve la instancia actualizada.
	 * @param  ExpedienteElecPend $pExpedienteElecPend [description]
	 * @return [type]                          [description]
	 */
	public function agregarDocumentoElectronicoDesdePendiente(ExpedienteElecPend $pExpedienteElecPend)
	{
		// En una única transacción, obtengo el nuevo ID y lo guardo en la DB.
		DB::getInstanceDBExpedientesElec()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientesElec()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Creo un ExpedienteElec a partir de un ExpedienteElecPend
			$expe_elec = new ExpedienteElec(
				$pExpedienteElecPend->anio,
				$pExpedienteElecPend->tipo,
				$pExpedienteElecPend->numero,
				$pExpedienteElecPend->cuerpo,
				$pExpedienteElecPend->alcance,
				null, // $porden -> se calcula despues
				$pExpedienteElecPend->tipo_actuacion,
				$pExpedienteElecPend->detalle,
				$pExpedienteElecPend->documento,
				$pExpedienteElecPend->documento_hash,
				$pExpedienteElecPend->texto_original,
				$pExpedienteElecPend->dec1404,
				$pExpedienteElecPend->embebido,
				$pExpedienteElecPend->es_caratula,
				'CURRENT_TIMESTAMP',
				$pExpedienteElecPend->id_usuario,
				$pExpedienteElecPend->observaciones
			);
			$expe_elec->orden = $this->obtenerExpedienteElecOrdenSiguiente($expe_elec);

			// Renombro el archivo
			$doc_info = pathinfo($expe_elec->documento);
			$doc_filename_nuevo = preg_replace('/^pendiente_/i', '', $doc_info['filename']);
			$nuevo_doc = sprintf('%s/%s.%s', $doc_info['dirname'], $doc_filename_nuevo, $doc_info['extension']);

			FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);
			FTPHelper::get()->moveFile(
				PATH_KRAKEN_RESOURCES_PROYECTOS.$expe_elec->documento,
				PATH_KRAKEN_RESOURCES_PROYECTOS.$nuevo_doc,
				0644
			);
			FTPHelper::get()->disconnect();

			$expe_elec->documento = $nuevo_doc;

			// Actualizo el expediente electronico
			$expe_elec = $this->guardarExpedienteElec($expe_elec, true);

			// Si el documento es caratula, actualizo el estado de la caratula anteriores
			$this->desasignarOtrasCaratulas($expe_elec);

			// Ejecuto transaccion
			DB::getInstanceDBExpedientesElec()->guardarTransaccion();

			// ------ Copio los firmantes del doc pendiente al final -------
			// Obtengo las firmas
			$firmas = NG::firmasExpedienteElecPend()->obtenerFirmasExpedienteElecPend(
				$pExpedienteElecPend->anio,
				$pExpedienteElecPend->tipo,
				$pExpedienteElecPend->numero,
				$pExpedienteElecPend->cuerpo,
				$pExpedienteElecPend->alcance,
				$pExpedienteElecPend->orden
			);
			// quito el primer elemento de las firmas
			$firma_solicitante = array_shift($firmas);

			// El primer firmante es el solicitante, y se considera aprobado
			NG::firmasExpedienteElec()->agregarFirmasDocumentoElectronico(
				$expe_elec,
				NG::seguridad()->obtenerUsuario($firma_solicitante->id_usuario),
				[NG::seguridad()->obtenerUsuario($firma_solicitante->id_usuario)],
				'firmado'
			);

			// Armo una lista con el resto de los firmantes (quite el primero con
			// el array_shift), que estaran 'pendientes'
			if (count($firmas) > 0) {
				$otros_firmantes = array_map(function ($fp) {
					return $fp->id_usuario;
				}, $firmas);

				NG::firmasExpedienteElec()->agregarFirmasDocumentoElectronico(
					$expe_elec,
					NG::seguridad()->obtenerUsuario($firma_solicitante->id_usuario),
					NG::seguridad()->obtenerUsuarios($otros_firmantes),
					'pendiente'
				);
			}

		} catch (Exception $e) {
			DB::getInstanceDBExpedientesElec()->cancelarTransaccion();
			DB::getInstanceDBExpedientesElec()->desconectar();
			throw new Exception(sprintf("Error en %s.agregarDocumentoElectronico: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientesElec()->desconectar();

		// Devolvemos el expediente electronico actualizado
		return $expe_elec;
	}

	/**
	 * Obtiene la ultima entrada de un expediente electronico, o null si no posee
	 * ninguna.
	 * @param  [type] $panio    [description]
	 * @param  [type] $ptipo    [description]
	 * @param  [type] $pnumero  [description]
	 * @param  [type] $pcuerpo  [description]
	 * @param  [type] $palcance [description]
	 * @return [type]           [description]
	 */
	public function obtenerExpedienteElecUltimo(
		// Parametros
		$panio, $ptipo, $pnumero, $pcuerpo, $palcance)
	{
		if (is_null($panio) || is_null($ptipo) || is_null($pnumero) || is_null($pcuerpo) || is_null($palcance))
			throw new Exception(sprintf("Error en %s.obtenerExpedienteElecUltimo: los campos clave no pueden ser nulos.", get_class($this)));

		$resultado = NG::expedientesElec()->obtenerExpedientesElec(
			$panio, $ptipo, $pnumero, $pcuerpo, $palcance,
			null, null, null, null, null, null, null, null, null, null, null, null,
			['T.`orden` DESC'], 1
		);

		if (count($resultado) == 0)
			return null;
		else if (count($resultado) == 1)
			return $resultado[0];
		else
			throw new Exception(sprintf("Error en %s.obtenerExpedienteElecUltimo: se encontr&oacute; m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
	}

	/**
	 * Realiza el cambio de alcance al documento del expediente electronico
	 * segun el art. 11 decreto 1404
	 * @param  ExpedienteElec $pExpedienteElec Entrada del expediente electronico afectada
	 * @param  Boolean        $pAlcanzaDec1404 True si es alcanzado, False en caso contrario.
	 * @return [type]                          [description]
	 */
	public function alcanzarDec1404(ExpedienteElec $pExpedienteElec, $pAlcanzaDec1404)
	{
		// Solamente hago el cambio si el alcance actual y el nuevo difieren
		if ($pExpedienteElec->dec1404 != $pAlcanzaDec1404) {
			// Actualizo el nombre del documento
			$doc_info = pathinfo($pExpedienteElec->documento);

			$doc_filename_nuevo = ($pAlcanzaDec1404)
				? $doc_info['filename'] . '_dec1404'
				: preg_replace('/_dec1404$/i', '', $doc_info['filename']);

			$nuevo_doc = sprintf('%s/%s.%s',
				$doc_info['dirname'],
				$doc_filename_nuevo,
				$doc_info['extension']
			);

			FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);
			FTPHelper::get()->moveFile(
				PATH_KRAKEN_RESOURCES_PROYECTOS.$pExpedienteElec->documento,
				PATH_KRAKEN_RESOURCES_PROYECTOS.$nuevo_doc,
				0644
			);
			FTPHelper::get()->disconnect();

			// Actualizo la instancia del expediente
			$pExpedienteElec->dec1404 = $pAlcanzaDec1404;
			$pExpedienteElec->documento = $nuevo_doc;
			$pExpedienteElec = $this->guardarExpedienteElec($pExpedienteElec);
		}

		return $pExpedienteElec;
	}

	/**
	 * Realiza el cambio de las observaciones al documento del expediente electronico
	 * @param  ExpedienteElec $pExpedienteElec Entrada del expediente electronico afectada
	 * @param  Boolean        $pObservaciones  Observaciones del documento del expediente electrónico
	 * @return [type]                          [description]
	 */
	public function editarObservaciones(ExpedienteElec $pExpedienteElec, $pObservaciones)
	{
		// Actualizo la instancia del expediente
		$pExpedienteElec->observaciones = $pObservaciones;
		$pExpedienteElec = $this->guardarExpedienteElec($pExpedienteElec);

		return $pExpedienteElec;
	}

	/**
	 * A partir de un expediente, comprime toda la documentación digital, agrega un checksum
	 * y devuelve la referencia al archivo generado.
	 * @param  [type] $panio    [description]
	 * @param  [type] $ptipo    [description]
	 * @param  [type] $pnumero  [description]
	 * @param  [type] $pcuerpo  [description]
	 * @param  [type] $palcance [description]
	 * @return string           Ruta completa del archivo generado.
	 */
	public function generarExpedienteElecZip(
		// Parametros
		$panio, $ptipo, $pnumero, $pcuerpo, $palcance)
	{
		// Obtengo la documentación electrónica
		$lista_ee = NG::expedientesElec()->obtenerExpedientesElec(
			$panio,	$ptipo,	$pnumero, $pcuerpo, $palcance,
			null, null, null, null, null, null, null, null, null, null, null, null,
			['orden asc']
		);

		// Si no tengo documentación, no devuelvo nada
		if (count($lista_ee) == 0) return '';

		// Genero la cabecera del archivo de checksum
		$timestamp = DateTimeHelper::get()->timestampInstance();
		$hash_data = sprintf("# %s - Sumas de Verificación\n# Documento: %d-%s-%05d cpo. %d alc. %d\n# %s\n\n",
			KRAKEN_VERSION_TAG,
			$panio,	$ptipo,	$pnumero, $pcuerpo, $palcance,
			$timestamp->format('Y-m-d H:i:s.u')
		);

		// Nombre del contenedor .zip
		$zip_filename = sprintf('%s%d%s%05d%02d%02d_%s.zip',
			PATH_SGL_DOC_TEMPORALES,
			$panio,	$ptipo,	$pnumero, $pcuerpo, $palcance,
			$timestamp->format('YmdHisu')
		);

		// Creo el zip y agrego todo el contenido
		$zip = new ZipArchive();
		if (! $zip->open($zip_filename, ZipArchive::CREATE))
			throw new Exception(sprintf('Error en %s.descargarExpedienteElecZip: no se puede abrir el archivo zip.', get_class($this)));

		$error_add = [];

		foreach ($lista_ee as $ee) {

			// Si el documento digital no existe, asumo que fue alcanzado por
			// el decreto 1404 y estamos en la versión pública del SGL, por lo
			// cual se reemplaza por una plantilla vacía.
			if (file_exists(PATH_KRAKEN_RESOURCES_PROYECTOS.$ee->documento)) {
				$doc = PATH_KRAKEN_RESOURCES_PROYECTOS.$ee->documento;
				$doc_hash = $ee->documento_hash;
			} else {
				$doc = PATH_SGL_DOC_FALTANTE_DEC1404;
				$doc_hash = hash_file('sha256', $doc);
			}

			// El $doc puede cambiar, pero mantengo el nombre original dentro del zip
			if (! $zip->addFile($doc, sprintf('%04d_%s', $ee->orden, basename($ee->documento))))
				$error_add[] = sprintf("No se puede agregar el archivo '%s' al contenedor.", $doc);

			$hash_data .= sprintf("SHA256(%04d_%s) = %s\n",
				$ee->orden,
				basename($ee->documento),
				$doc_hash
			);
		}

		// Genero el checksum
		$zip->addFromString('checksum.txt', $hash_data);

		// Creo el archivo
		$zip->close();

		// Verifico errores
		if (! file_exists($zip_filename))
			throw new Exception(sprintf('Error en %s.descargarExpedienteElecZip: no se encuentra el contenedor zip: %s', $zip_filename, get_class($this)));

		if (count($error_add) > 0)
			throw new Exception(sprintf('Error en %s.descargarExpedienteElecZip: se han producido los siguientes errores al generar el contenedor zip: %s', get_class($this), join(', ', $error_add)));

		// Devuelvo resultados
		return $zip_filename;
	}

	/**
	 * Esta funcion se encarga de tomar un archivo pdf, verificar si tiene embebidos,
	 * y de forma recursiva ir agregandolos al array de documentos pasado como parametro.
	 *
	 * @param  string $ee_doc      Documento con posibles embebidos
	 * @param  array  &$documentos Listado que se irá completando, de documentos a unificar y firmar
	 *
	 * @return array  &$documentos Listado final, de documentos a unificar y firmar
	 */
	private function agregarEmbebidosRecursivos($ee_doc, &$documentos) {

		// Agrego el documento al array de documentos
		$documentos[] = sprintf("'%s'", $ee_doc);

		// Si tiene embebidos, continua la recursión.
		$embebidos = PDFExtractor::get()->getAttachments($ee_doc);

		if (count($embebidos['pdf']) > 0) {
			foreach ($embebidos['pdf'] as $emb) {
				$emb_tmp_file = PDFExtractor::get()->extractFile($ee_doc, $emb['id']);
				$this->agregarEmbebidosRecursivos($emb_tmp_file, $documentos);
			}
		}
		/**/
		// ---- Proceso similar para los que NO son pdf --------------
		if (count($embebidos['other']) > 0) {

			// Por cada embebido que no es un pdf
			foreach ($embebidos['other'] as $emb) {
				// Se lo extrae
				$emb_tmp_file_no_pdf = PDFExtractor::get()->extractFile($ee_doc, $emb['id']);
				Logger::get()->Log("emb_tmp_file_no_pdf", $emb_tmp_file_no_pdf);

				// Se lo convierte a PDF
                $archivo_convertido = $this->convertirArchivoAPdf($emb_tmp_file_no_pdf);
                //Logger::get()->Log("archivo_convertido", $archivo_convertido);

				// Ya convertido se lo agrega al listado de documentos
				$this->agregarEmbebidosRecursivos($archivo_convertido, $documentos);
			}
		}
		/**/
	}

	/**
	 * Convierte un archivo, de texto o imagen, a PDF.
	 * Se utiliza el comando 'lowriter'
	 * Texto: doc, docx, dot, dotx, odt
	 * Imagen: jpg, jpeg, png, gif
	 *
	 * @param  string $archivo_a_convertir
	 * @return string $archivo_convertido
	 */
	private function convertirArchivoAPdf($archivo_a_convertir) {

		// Se obtiene información de la ruta del archivo extraído
        $partes_ruta = pathinfo($archivo_a_convertir);

        $cmd = '';

        // En base a su extensión
        switch ($partes_ruta['extension']) {
            case 'doc':
            case 'docx':
            case 'dot':
            case 'dotx':
            case 'odt':
            	// Ya que la conversión mantiene su nombre, se arma el nombre con la extensión pdf
		        $archivo_convertido = str_replace(
		        	'.'.$partes_ruta['extension'],
		        	'.pdf',
		        	$archivo_a_convertir
		        );
                // Se convierte a pdf con el comando 'lowriter'
                $cmd = sprintf('cd %s && lowriter --convert-to pdf %s',
                	PATH_SGL_DOC_TEMPORALES,
                	$archivo_a_convertir
                );
                break;
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                $archivo_convertido = PATH_SGL_DOC_TEMPORALES.$partes_ruta['filename'].'.pdf';

                // Se convierte a pdf con el comando 'convert'
                // Debe instalarse:
                // sudo apt install graphicsmagick-imagemagick-compat
                // --------------------------------------------------
                $cmd = sprintf('cd %s && convert %s +compress %s',
                	PATH_SGL_DOC_TEMPORALES,
                	$archivo_a_convertir,
                	$archivo_convertido
            	);
            	break;
            case 'xls':
            case 'xlsx':
            case 'ods':
            	$archivo_convertido = PATH_SGL_DOC_TEMPORALES.$partes_ruta['filename'].'.pdf';

                // Se convierte a pdf con el comando 'unoconv'
                // Debe instalarse:
                // sudo apt install unoconv
                // ------------------------
                $cmd = sprintf('cd %s && unoconv -f pdf -o %s -e "PageOrientation=Landscape" %s',
                	PATH_SGL_DOC_TEMPORALES,
                	$archivo_convertido,
                	$archivo_a_convertir
            	);
            	break;
            case 'dat':
            	$archivo_convertido = PATH_SGL_DOC_TEMPORALES.$partes_ruta['filename'].'.pdf';
            	//$archivo_convertido = PATH_SGL_DOC_TEMPORALES.'embebido_'.uniqid().'_'.random_int(1000, 9999).'.pdf';

                // Se renombra el archivo .dat a .pdf
                $cmd = sprintf('mv "%s" "%s"',
                	$archivo_a_convertir,
                	$archivo_convertido
            	);
                break;
        }

        // ---- Fix para evitar que los acentos salgan rotos ------------------
        $locale = 'es_AR.UTF-8';
        setlocale(LC_ALL, $locale);
        putenv('LC_ALL='.$locale);
        // --------------------------------------------------------------------

        // Si se ha definido un comando
        if ($cmd != '') {
        	//Logger::get()->Log("cmd", $cmd);

        	// Se ejecuta el comando y se guarda su salida
        	$output = shell_exec("( $cmd ) 2>&1");
        	//Logger::get()->Log("output", $output);
        }

        return $archivo_convertido;
	}

	/**
	 * A partir de un expediente, concatena toda la documentación digital en un
	 * único archivo PDF, lo firma y devuelve la referencia al archivo generado.
	 *
	 * @param  int  $panio    Año de un Exped. Electrónico
	 * @param  char $ptipo    Tipo de un Exped. Electrónico
	 * @param  int  $pnumero  Número de un Exped. Electrónico
	 * @param  int  $pcuerpo  Cuerpo de un Exped. Electrónico
	 * @param  int  $palcance Alcance de un Exped. Electrónico
	 *
	 * @return string $pdf_filename Ruta del documento final en pdf firmado
	 */
	public function generarExpedienteElecPdf(
		// Parametros
		$panio, $ptipo, $pnumero, $pcuerpo, $palcance)
	{
		// Obtengo la documentación electrónica
		$lista_ee = NG::expedientesElec()->obtenerExpedientesElec(
			$panio,	$ptipo,	$pnumero, $pcuerpo, $palcance,
			null, null, null, null, null, null, null, null, null, null, null, null,
			['orden asc']
		);

		// Si no tengo documentación, no devuelvo nada
		if (count($lista_ee) == 0) return '';

		$timestamp = DateTimeHelper::get()->timestampInstance();

		// Nombre del contenedor temporal .pdf (no firmado)
		$pdf_temp_filename = sprintf('%stemp_%d%s%05d%02d%02d_%s.pdf',
			PATH_SGL_DOC_TEMPORALES,
			$panio,	$ptipo,	$pnumero, $pcuerpo, $palcance,
			$timestamp->format('YmdHisu')
		);

		// Tomo todos los archivos ordenados por 'orden' y preparo la lista
		// de documentos a concatenar.
		$documentos = [];
		$pos_caratula = -1;
		foreach ($lista_ee as $ee) {
			// Encuentro la caratula
			if ($ee->es_caratula)
				$pos_caratula = count($documentos);

			// Si el documento digital no existe, asumo que fue alcanzado por
			// el decreto 1404 y estamos en la versión pública del SGL, por lo
			// cual se reemplaza por una plantilla vacía.
			$ee_doc = PATH_KRAKEN_RESOURCES_PROYECTOS.$ee->documento;
			if (file_exists($ee_doc)) {
				// Los elementos se agregan de forma recursiva para resolver los
				// embebidos que poseen embebidos que poseen embebidos y asi...
				$this->agregarEmbebidosRecursivos($ee_doc, $documentos);
			} else
				$documentos[] = sprintf("'%s'", PATH_SGL_DOC_FALTANTE_DEC1404);
		}

		// Si posee carátula, la pasamos al frente del listado de documentos
		if ($pos_caratula >= 0) {
			// Nos quedamos con el documento de la caratula
			$doc_caratula = $documentos[$pos_caratula];

			// Se elimina del vector de documentos
			unset($documentos[$pos_caratula]);

			// Se agrega primero
			array_unshift($documentos, $doc_caratula);
		}

		// Anexo una hoja de copia fiel (para la firma digital)
		$documentos[] = PATH_SGL_DOC_COPIA_FIEL;

		Logger::get()->Log("documentos", $documentos);

		// Se define el comando para unir las digitalizaciones, utilizando el comando gs (ghostscript)
		$cmd = sprintf("gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile='%s' %s",
			$pdf_temp_filename,
			join(' ', $documentos)
		);

		// Se ejecuta el comando
		$cmd_result = shell_exec($cmd);

		// Verifico errores
		if (! file_exists($pdf_temp_filename))
			throw new Exception(sprintf('Error en %s.generarExpedienteElecPdf: no se encuentra el contenedor pdf temporal: %s', $pdf_temp_filename, get_class($this)));

		// ---- Firmo el documento con certificado de aplicación y su propia marca al agua.

		// Nombre del contenedor final .pdf (firmado)
		$pdf_filename = sprintf('%s%d%s%05d%02d%02d_%s.pdf',
			PATH_SGL_DOC_TEMPORALES,
			$panio,	$ptipo,	$pnumero, $pcuerpo, $palcance,
			$timestamp->format('YmdHisu')
		);

		// Firmo el documento
		$errores = NG::firmas()->firmarPDF($pdf_temp_filename, $pdf_filename, null);
		if (count($errores) > 0)
			throw new Exception(sprintf('Error en %s.generarExpedienteElecPdf: se han producido errores al firmar el contenedor pdf: %s', join(', ', $errores), get_class($this)));

		unlink($pdf_temp_filename);

		// Devuelvo resultados
		return $pdf_filename;
	}

	/**
	 * A partir de un expediente, concatena toda la documentación digital en un
	 * único archivo PDF, lo firma y devuelve la referencia al archivo generado.
	 * Aquí los documentos alcanzados por el decreto 1404 se reemplazan por una plantilla vacía.
	 *
	 * @param  int  $panio    Año de un Exped. Electrónico
	 * @param  char $ptipo    Tipo de un Exped. Electrónico
	 * @param  int  $pnumero  Número de un Exped. Electrónico
	 * @param  int  $pcuerpo  Cuerpo de un Exped. Electrónico
	 * @param  int  $palcance Alcance de un Exped. Electrónico
	 *
	 * @return string $pdf_filename Ruta del documento final en pdf firmado
	 */
	public function generarExpedientePublicoElecPdf(
		// Parametros
		$panio, $ptipo, $pnumero, $pcuerpo, $palcance)
	{
		// Obtengo la documentación electrónica
		$lista_ee = NG::expedientesElec()->obtenerExpedientesElec(
			$panio,	$ptipo,	$pnumero, $pcuerpo, $palcance,
			null, null, null, null, null, null, null, null, null, null, null, null,
			['orden asc']
		);

		// Si no tengo documentación, no devuelvo nada
		if (count($lista_ee) == 0) return '';

		$timestamp = DateTimeHelper::get()->timestampInstance();

		// Nombre del contenedor temporal .pdf (no firmado)
		$pdf_temp_filename = sprintf('%stemp_%d%s%05d%02d%02d_%s.pdf',
			PATH_SGL_DOC_TEMPORALES,
			$panio,	$ptipo,	$pnumero, $pcuerpo, $palcance,
			$timestamp->format('YmdHisu')
		);

		// Tomo todos los archivos ordenados por 'orden' y preparo la lista
		// de documentos a concatenar.
		$documentos = [];
		$pos_caratula = -1;
		foreach ($lista_ee as $ee) {
			// Encuentro la caratula
			if ($ee->es_caratula)
				$pos_caratula = count($documentos);

			$ee_doc = PATH_KRAKEN_RESOURCES_PROYECTOS.$ee->documento;
			if (file_exists($ee_doc)) {
				// Si el documento NO fue alcanzado por el decreto 1404
				if (strpos($ee->documento, "_dec1404.pdf") === false) {
					// Los elementos se agregan de forma recursiva para resolver los
					// embebidos que poseen embebidos que poseen embebidos y asi...
					$this->agregarEmbebidosRecursivos($ee_doc, $documentos);
				} else {
					// Si fue alcanzado, se reemplaza por una plantilla vacía
					$documentos[] = sprintf("'%s'", PATH_SGL_DOC_FALTANTE_DEC1404);
				}
			}
		}

		// Si posee carátula, la pasamos al frente del listado de documentos
		if ($pos_caratula >= 0) {
			// Nos quedamos con el documento de la caratula
			$doc_caratula = $documentos[$pos_caratula];

			// Se elimina del vector de documentos
			unset($documentos[$pos_caratula]);

			// Se agrega primero
			array_unshift($documentos, $doc_caratula);
		}

		// Anexo una hoja de copia fiel (para la firma digital)
		$documentos[] = PATH_SGL_DOC_COPIA_FIEL;

		Logger::get()->Log("documentos", $documentos);

		// Se define el comando para unir las digitalizaciones, utilizando el comando gs (ghostscript)
		$cmd = sprintf("gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile='%s' %s",
			$pdf_temp_filename,
			join(' ', $documentos)
		);

		// Se ejecuta el comando
		$cmd_result = shell_exec($cmd);

		// Verifico errores
		if (! file_exists($pdf_temp_filename))
			throw new Exception(sprintf('Error en %s.generarExpedienteElecPdf: no se encuentra el contenedor pdf temporal: %s', $pdf_temp_filename, get_class($this)));

		// ---- Firmo el documento con certificado de aplicación y su propia marca al agua.

		// Nombre del contenedor final .pdf (firmado)
		$pdf_filename = sprintf('%s%d%s%05d%02d%02d_%s.pdf',
			PATH_SGL_DOC_TEMPORALES,
			$panio,	$ptipo,	$pnumero, $pcuerpo, $palcance,
			$timestamp->format('YmdHisu')
		);

		// Firmo el documento
		$errores = NG::firmas()->firmarPDF($pdf_temp_filename, $pdf_filename, null);
		if (count($errores) > 0)
			throw new Exception(sprintf('Error en %s.generarExpedienteElecPdf: se han producido errores al firmar el contenedor pdf: %s', join(', ', $errores), get_class($this)));

		unlink($pdf_temp_filename);

		// Devuelvo resultados
		return $pdf_filename;
	}

	/**
	 * Obtiene una instancia de tipo Expediente en base a un expediente electrónico.
	 * @param  ExpedienteElec $pExpedienteElec [description]
	 * @return [type]                          [description]
	 */
	public function obtenerExpedienteDeExpedienteElec(ExpedienteElec $pExpedienteElec)
	{
		return NG::expedientes()->obtenerExpediente(
			$pExpedienteElec->anio,
			$pExpedienteElec->tipo,
			$pExpedienteElec->numero,
			$pExpedienteElec->cuerpo,
			$pExpedienteElec->alcance
		);
	}

	/**
	 * Actualiza un documento del expediente electrónico. Generalmente se utiliza
	 * cuando se suman firmas a un documento del expediente electronico.
	 * @param  ExpedienteElec $pExpedienteElec [description]
	 * @param  [type]         $pdocumento      [description]
	 * @return [type]                          [description]
	 */
	public function actualizarDocumentoExpedienteElec(ExpedienteElec $pExpedienteElec, $pdocumento)
	{
		// Verifico que el archivo exista con su ruta completa
		if (!file_exists($pdocumento))
			throw new Exception(sprintf('Error en %s.actualizarDocumentoExpedienteElec: no se encuentra el archivo "%s".', $pdocumento, get_class($this)));

		// Extraigo la ruta base del archivo
		$pdocumento_ref = preg_replace(sprintf('|^%s|',PATH_KRAKEN_RESOURCES_PROYECTOS), '', $pdocumento);

		// Actualizo
		$pExpedienteElec->documento = $pdocumento_ref;
		$pExpedienteElec->documento_hash = hash_file('sha256', $pdocumento);
		$pExpedienteElec = $this->guardarExpedienteElec($pExpedienteElec);

		return $pExpedienteElec;
	}
}
?>

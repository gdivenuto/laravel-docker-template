<?php
/**
 * Capa de negocio de Expedientes.
 *
 * @author XXXX
 *
 */

// Estados de Proyecto del Expediente
define('ESTADO_PROYECTO_CARGADO', 'C');
define('ESTADO_PROYECTO_PARA_CARGAR', 'PC');
define('ESTADO_PROYECTO_SIN_CARGAR', 'SC');
define('ESTADO_PROYECTO_CARGADO_PARA_CARGAR', 'C_PC');

// Estados de la Digitalización del Expediente
// Se siguen utilizando para aquellos Expedientes NO Electrónicos (anteriores al 2023)
define('ESTADO_DIGITALIZACION_CARGADA', 'DC');
define('ESTADO_DIGITALIZACION_PARA_CARGAR', 'DPC');
define('ESTADO_DIGITALIZACION_SIN_CARGAR', 'DSC');
define('ESTADO_DIGITALIZACION_CARGADA_Y_PARA_CARGAR', 'DC_PC');

// 09/02/2023 XXXX
// Se utiliza para Exped. Electrónicos (a partir del 2023)
// Estados del Expediente Electrónico
define('ESTADO_EXPED_ELECTRONICO_SIN_DOCUMENTOS', 'EESD');
define('ESTADO_EXPED_ELECTRONICO_CON_DOCUMENTOS', 'EECD');

// Descripción de las marcas de un expediente en una comisión determinada
define('NOMBRE_MARCA_SM', 'Sin marca');
define('NOMBRE_MARCA_PT', 'Para tratar');
define('NOMBRE_MARCA_PSC', 'Para su conocimiento');
define('NOMBRE_MARCA_PA', 'Para archivo');
define('NOMBRE_MARCA_PP', 'Para pr&oacute;rroga');
define('NOMBRE_MARCA_PC', 'Para convalidar');

class NGExpedientes extends NGBaseClass {

	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	private $nombre_marca;

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct() {
		parent::__construct();

		$this->nombre_marca = '';
	}

	// ************************************************************************
	// Antecedentes
	// ************************************************************************

	/**
	 * NGExpedientes: Obtiene una coleccion de elementos tipo Antecedente en base a diferentes criterios de selección.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) anio_a
	 * @param  string (PK) tipo_a
	 * @param  float (PK) numero_a
	 * @param  string (PK) digito_a
	 * @param  integer (PK) cuerpo_a
	 * @param  integer (PK) alcance_a
	 * @param  integer (PK) cuerpoalcance_a
	 * @param  integer (PK) anexoalcance_a
	 * @param  integer (PK) cuerpoanexoalcance_a
	 * @param  integer (PK) anexo_a
	 * @param  integer (PK) cuerpoanexo_a
	 * @param  string observaciones_antecedentes
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<Antecedente>
	 */
	public function obtenerAntecedentes(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$panio_a = null,
		$ptipo_a = null,
		$pnumero_a = null,
		$pdigito_a = null,
		$pcuerpo_a = null,
		$palcance_a = null,
		$pcuerpoalcance_a = null,
		$panexoalcance_a = null,
		$pcuerpoanexoalcance_a = null,
		$panexo_a = null,
		$pcuerpoanexo_a = null,
		$pobservaciones_antecedentes = null,
		$pid_usuario = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null,
		array $pAgrupacionColumnas = null) {
		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBExpedientes()->obtenerAntecedentes($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $panio_a, $ptipo_a, $pnumero_a, $pdigito_a, $pcuerpo_a, $palcance_a, $pcuerpoalcance_a, $panexoalcance_a, $pcuerpoanexoalcance_a, $panexo_a, $pcuerpoanexo_a, $pobservaciones_antecedentes, $pid_usuario,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset, $pAgrupacionColumnas);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerAntecedentes: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Antecedente
		$resultado = $this->arrayResultToInstance($filas, 'Antecedente');

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Determina la cantidad de elementos tipo Antecedente obtenidos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) anio_a
	 * @param  string (PK) tipo_a
	 * @param  float (PK) numero_a
	 * @param  string (PK) digito_a
	 * @param  integer (PK) cuerpo_a
	 * @param  integer (PK) alcance_a
	 * @param  integer (PK) cuerpoalcance_a
	 * @param  integer (PK) anexoalcance_a
	 * @param  integer (PK) cuerpoanexoalcance_a
	 * @param  integer (PK) anexo_a
	 * @param  integer (PK) cuerpoanexo_a
	 * @param  string observaciones_antecedentes
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerAntecedentesCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$panio_a = null,
		$ptipo_a = null,
		$pnumero_a = null,
		$pdigito_a = null,
		$pcuerpo_a = null,
		$palcance_a = null,
		$pcuerpoalcance_a = null,
		$panexoalcance_a = null,
		$pcuerpoanexoalcance_a = null,
		$panexo_a = null,
		$pcuerpoanexo_a = null,
		$pobservaciones_antecedentes = null,
		$pid_usuario = null,
		$pAgrupacionColumnas = null) {
		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBExpedientes()->obtenerAntecedentesCantidad($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $panio_a, $ptipo_a, $pnumero_a, $pdigito_a, $pcuerpo_a, $palcance_a, $pcuerpoalcance_a, $panexoalcance_a, $pcuerpoanexoalcance_a, $panexo_a, $pcuerpoanexo_a, $pobservaciones_antecedentes, $pid_usuario, $pAgrupacionColumnas);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerAntecedentesCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGExpedientes: Obtiene una instancia de tipo Antecedente en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) anio_a
	 * @param  string (PK) tipo_a
	 * @param  float (PK) numero_a
	 * @param  string (PK) digito_a
	 * @param  integer (PK) cuerpo_a
	 * @param  integer (PK) alcance_a
	 * @param  integer (PK) cuerpoalcance_a
	 * @param  integer (PK) anexoalcance_a
	 * @param  integer (PK) cuerpoanexoalcance_a
	 * @param  integer (PK) anexo_a
	 * @param  integer (PK) cuerpoanexo_a
	 * @return Antecedente Instancia de Antecedente buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerAntecedente(
		// Parametros
		$panio, $ptipo, $pnumero, $pcuerpo, $palcance, $panio_a, $ptipo_a, $pnumero_a, $pdigito_a, $pcuerpo_a, $palcance_a, $pcuerpoalcance_a, $panexoalcance_a, $pcuerpoanexoalcance_a, $panexo_a, $pcuerpoanexo_a) {
		if (is_null($panio) || is_null($ptipo) || is_null($pnumero) || is_null($pcuerpo) || is_null($palcance) || is_null($panio_a) || is_null($ptipo_a) || is_null($pnumero_a) || is_null($pdigito_a) || is_null($pcuerpo_a) || is_null($palcance_a) || is_null($pcuerpoalcance_a) || is_null($panexoalcance_a) || is_null($pcuerpoanexoalcance_a) || is_null($panexo_a) || is_null($pcuerpoanexo_a)) {
			throw new Exception(sprintf("Error en %s.obtenerAntecedente: los campos clave no pueden ser nulos.", get_class($this)));
		}

		$resultado = $this->obtenerAntecedentes($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $panio_a, $ptipo_a, $pnumero_a, $pdigito_a, $pcuerpo_a, $palcance_a, $pcuerpoalcance_a, $panexoalcance_a, $pcuerpoanexoalcance_a, $panexo_a, $pcuerpoanexo_a);

		if (count($resultado) == 0) {
			return null;
		} else if (count($resultado) == 1) {
			return $resultado[0];
		} else {
			throw new Exception(sprintf("Error en %s.obtenerAntecedente: se encontr&oacute; m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
		}

	}

	/**
	 * Guarda una instancia de tipo Antecedente. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  Antecedente $pAntecedente 	Instancia a guardar.
	 * @param  boolean $pRecargar 		Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return Antecedente               Instancia guardada.
	 */
	public function guardarAntecedente(Antecedente $pAntecedente, $pRecargar = true) {
		if (is_null($pAntecedente)) {
			throw new Exception(sprintf("Error en %s.guardarAntecedente: la instancia a guardar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$audit_operacion = (is_null($this->obtenerAntecedente($pAntecedente->anio, $pAntecedente->tipo, $pAntecedente->numero, $pAntecedente->cuerpo, $pAntecedente->alcance, $pAntecedente->anio_a, $pAntecedente->tipo_a, $pAntecedente->numero_a, $pAntecedente->digito_a, $pAntecedente->cuerpo_a, $pAntecedente->alcance_a, $pAntecedente->cuerpoalcance_a, $pAntecedente->anexoalcance_a, $pAntecedente->cuerpoanexoalcance_a, $pAntecedente->anexo_a, $pAntecedente->cuerpoanexo_a))) ? Auditoria::OP_ALTA : Auditoria::OP_MODIFICA;

			$id = DB::getInstanceDBExpedientes()->guardarAntecedente(
				$pAntecedente->anio,
				$pAntecedente->tipo,
				$pAntecedente->numero,
				$pAntecedente->cuerpo,
				$pAntecedente->alcance,
				$pAntecedente->anio_a,
				$pAntecedente->tipo_a,
				$pAntecedente->numero_a,
				$pAntecedente->digito_a,
				$pAntecedente->cuerpo_a,
				$pAntecedente->alcance_a,
				$pAntecedente->cuerpoalcance_a,
				$pAntecedente->anexoalcance_a,
				$pAntecedente->cuerpoanexoalcance_a,
				$pAntecedente->anexo_a,
				$pAntecedente->cuerpoanexo_a,
				$pAntecedente->observaciones_antecedentes,
				$pAntecedente->id_usuario);

			// Auditoria
			NG::auditorias()->auditarComoExpediente(
				$pAntecedente, null, $audit_operacion, 'hcd.expe_antecedentes', null, null,
				sprintf('Se ha guardado el antecedente %d-%s-%d-%s-%d-%d-%d-%d-%d-%d-%d del expediente %d-%s-%d-%d-%d',
					$pAntecedente->anio_a, $pAntecedente->tipo_a, $pAntecedente->numero_a, $pAntecedente->digito_a, $pAntecedente->cuerpo_a, $pAntecedente->alcance_a, $pAntecedente->cuerpoalcance_a, $pAntecedente->anexoalcance_a, $pAntecedente->cuerpoanexoalcance_a, $pAntecedente->anexo_a, $pAntecedente->cuerpoanexo_a,
					$pAntecedente->anio, $pAntecedente->tipo, $pAntecedente->numero, $pAntecedente->cuerpo, $pAntecedente->alcance)
			);

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarAntecedente: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$resultado = $this->obtenerAntecedente($pAntecedente->anio, $pAntecedente->tipo, $pAntecedente->numero, $pAntecedente->cuerpo, $pAntecedente->alcance, $pAntecedente->anio_a, $pAntecedente->tipo_a, $pAntecedente->numero_a, $pAntecedente->digito_a, $pAntecedente->cuerpo_a, $pAntecedente->alcance_a, $pAntecedente->cuerpoalcance_a, $pAntecedente->anexoalcance_a, $pAntecedente->cuerpoanexoalcance_a, $pAntecedente->anexo_a, $pAntecedente->cuerpoanexo_a);
		} else {
			$resultado = $pAntecedente;
		}

		DB::getInstanceDBExpedientes()->desconectar();

		if (is_null($resultado)) {
			throw new Exception(sprintf("Error grave en %s.guardarAntecedente: no se encuentra el contenido actualizado.", get_class($this)));
		}

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina un conjunto de Antecedentes en base a diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-02 12:06:49
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) anio_a
	 * @param  string (PK) tipo_a
	 * @param  float (PK) numero_a
	 * @param  string (PK) digito_a
	 * @param  integer (PK) cuerpo_a
	 * @param  integer (PK) alcance_a
	 * @param  integer (PK) cuerpoalcance_a
	 * @param  integer (PK) anexoalcance_a
	 * @param  integer (PK) cuerpoanexoalcance_a
	 * @param  integer (PK) anexo_a
	 * @param  integer (PK) cuerpoanexo_a
	 * @param  string observaciones_antecedentes
	 * @param  integer id_usuario
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarAntecedentes(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$panio_a = null,
		$ptipo_a = null,
		$pnumero_a = null,
		$pdigito_a = null,
		$pcuerpo_a = null,
		$palcance_a = null,
		$pcuerpoalcance_a = null,
		$panexoalcance_a = null,
		$pcuerpoanexoalcance_a = null,
		$panexo_a = null,
		$pcuerpoanexo_a = null,
		$pobservaciones_antecedentes = null,
		$pid_usuario = null) {
		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBExpedientes()->eliminarAntecedentes($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $panio_a, $ptipo_a, $pnumero_a, $pdigito_a, $pcuerpo_a, $palcance_a, $pcuerpoalcance_a, $panexoalcance_a, $pcuerpoanexoalcance_a, $panexo_a, $pcuerpoanexo_a, $pobservaciones_antecedentes, $pid_usuario);

			// Auditoria (si es que se elimino algo, tengo que auditar)
			if ($resultado > 0) {
				$dummy = new Antecedente($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $panio_a, $ptipo_a, $pnumero_a, $pdigito_a, $pcuerpo_a, $palcance_a, $pcuerpoalcance_a, $panexoalcance_a, $pcuerpoanexoalcance_a, $panexo_a, $pcuerpoanexo_a, $pobservaciones_antecedentes, $pid_usuario);

				// 07/02/2022 XXXX
				if (is_null($panio_a) && is_null($ptipo_a)) {
					$audit_observaciones = "AUTOMATICO, al eliminar un Expediente.";
				} else {
					$audit_observaciones = null;//NG::auditorias()->generarMensajeEliminacion($resultado, array($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $panio_a, $ptipo_a, $pnumero_a, $pdigito_a, $pcuerpo_a, $palcance_a, $pcuerpoalcance_a, $panexoalcance_a, $pcuerpoanexoalcance_a, $panexo_a, $pcuerpoanexo_a, $pobservaciones_antecedentes, $pid_usuario));
				}

				NG::auditorias()->auditarComoExpediente(
					$dummy, null, Auditoria::OP_BAJA, 'hcd.expe_antecedentes', null, null, $audit_observaciones);
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarAntecedentes: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina una instancia de tipo Antecedente en base a su identificador.
	 * GenerateClass 0.97.5 beta @ 2016-09-02 12:06:49
	 * @param  Antecedente $pAntecedente 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarAntecedente(Antecedente $pAntecedente) {
		if (is_null($pAntecedente)) {
			throw new Exception(sprintf("Error en %s.eliminarAntecedente: la instancia a eliminar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarAntecedentes($pAntecedente->anio, $pAntecedente->tipo, $pAntecedente->numero, $pAntecedente->cuerpo, $pAntecedente->alcance, $pAntecedente->anio_a, $pAntecedente->tipo_a, $pAntecedente->numero_a, $pAntecedente->digito_a, $pAntecedente->cuerpo_a, $pAntecedente->alcance_a, $pAntecedente->cuerpoalcance_a, $pAntecedente->anexoalcance_a, $pAntecedente->cuerpoanexoalcance_a, $pAntecedente->anexo_a, $pAntecedente->cuerpoanexo_a);

			if ($resultado > 1) {
				throw new Exception(sprintf("Error en %s.eliminarAntecedente: se quiso eliminar m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarAntecedente: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return ($resultado == 1);
	}

	// ************************************************************************
	// Autores
	// ************************************************************************

	/**
	 * NGExpedientes: Obtiene una coleccion de elementos tipo Autor en base a diferentes criterios de selección.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string (PK) autor_tipo
	 * @param  string (PK) autor_codigo
	 * @param  string autor_bloque_tipo
	 * @param  string autor_bloque_codigo
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<Autor>
	 */
	public function obtenerAutores(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pautor_tipo = null,
		$pautor_codigo = null,
		$pautor_bloque_tipo = null,
		$pautor_bloque_codigo = null,
		$pid_usuario = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBExpedientes()->obtenerAutores($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pautor_tipo, $pautor_codigo, $pautor_bloque_tipo, $pautor_bloque_codigo, $pid_usuario,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerAutores: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Autor
		$resultado = $this->arrayResultToInstance($filas, 'Autor');

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Determina la cantidad de elementos tipo Autor obtenidos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string (PK) autor_tipo
	 * @param  string (PK) autor_codigo
	 * @param  string autor_bloque_tipo
	 * @param  string autor_bloque_codigo
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerAutoresCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pautor_tipo = null,
		$pautor_codigo = null,
		$pautor_bloque_tipo = null,
		$pautor_bloque_codigo = null,
		$pid_usuario = null) {
		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBExpedientes()->obtenerAutoresCantidad($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pautor_tipo, $pautor_codigo, $pautor_bloque_tipo, $pautor_bloque_codigo, $pid_usuario);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerAutoresCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGExpedientes: Obtiene una instancia de tipo Autor en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string (PK) autor_tipo
	 * @param  string (PK) autor_codigo
	 * @return Autor Instancia de Autor buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerAutor(
		// Parametros
		$panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pautor_tipo, $pautor_codigo) {
		if (is_null($panio) || is_null($ptipo) || is_null($pnumero) || is_null($pcuerpo) || is_null($palcance) || is_null($pautor_tipo) || is_null($pautor_codigo)) {
			throw new Exception(sprintf("Error en %s.obtenerAutor: los campos clave no pueden ser nulos.", get_class($this)));
		}

		$resultado = $this->obtenerAutores($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pautor_tipo, $pautor_codigo);

		if (count($resultado) == 0) {
			return null;
		} else if (count($resultado) == 1) {
			return $resultado[0];
		} else {
			throw new Exception(sprintf("Error en %s.obtenerAutor: se encontr&oacute; m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
		}

	}

	/**
	 * Guarda una instancia de tipo Autor. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  Autor $pAutor 	Instancia a guardar.
	 * @param  boolean $pRecargar 		Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return Autor               Instancia guardada.
	 */
	public function guardarAutor(Autor $pAutor, $pRecargar = true) {
		if (is_null($pAutor)) {
			throw new Exception(sprintf("Error en %s.guardarAutor: la instancia a guardar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$audit_operacion = (is_null($this->obtenerAutor($pAutor->anio, $pAutor->tipo, $pAutor->numero, $pAutor->cuerpo, $pAutor->alcance, $pAutor->autor_tipo, $pAutor->autor_codigo))) ? Auditoria::OP_ALTA : Auditoria::OP_MODIFICA;

			//Logger::get()->Log("pAutor", $pAutor);
			$id = DB::getInstanceDBExpedientes()->guardarAutor(
				$pAutor->anio,
				$pAutor->tipo,
				$pAutor->numero,
				$pAutor->cuerpo,
				$pAutor->alcance,
				$pAutor->autor_tipo,
				$pAutor->autor_codigo,
				$pAutor->autor_bloque_tipo,
				$pAutor->autor_bloque_codigo,
				$pAutor->id_usuario);

			// Auditoria
			NG::auditorias()->auditarComoExpediente(
				$pAutor, null, $audit_operacion, 'hcd.expe_autores', null, null,
				sprintf('Se ha guardado el autor %d-%s-%d-%d-%d, tipo: %s, c&oacute;digo: %s', $pAutor->anio, $pAutor->tipo, $pAutor->numero, $pAutor->cuerpo, $pAutor->alcance, $pAutor->autor_tipo, $pAutor->autor_codigo)
			);

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarAutor: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$resultado = $this->obtenerAutor($pAutor->anio, $pAutor->tipo, $pAutor->numero, $pAutor->cuerpo, $pAutor->alcance, $pAutor->autor_tipo, $pAutor->autor_codigo);
		} else {
			$resultado = $pAutor;
		}

		DB::getInstanceDBExpedientes()->desconectar();

		if (is_null($resultado)) {
			throw new Exception(sprintf("Error grave en %s.guardarAutor: no se encuentra el contenido actualizado.", get_class($this)));
		}

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina un conjunto de Autores en base a diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-02 13:55:07
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string (PK) autor_tipo
	 * @param  string (PK) autor_codigo
	 * @param  string autor_bloque_tipo
	 * @param  string autor_bloque_codigo
	 * @param  integer id_usuario
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarAutores(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pautor_tipo = null,
		$pautor_codigo = null,
		$pautor_bloque_tipo = null,
		$pautor_bloque_codigo = null,
		$pid_usuario = null) {
		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBExpedientes()->eliminarAutores($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pautor_tipo, $pautor_codigo, $pautor_bloque_tipo, $pautor_bloque_codigo, $pid_usuario);

			// Auditoria (si es que se elimino algo, tengo que auditar)
			if ($resultado > 0) {
				$dummy = new Autor($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pautor_tipo, $pautor_codigo, $pautor_bloque_tipo, $pautor_bloque_codigo, $pid_usuario);

				// 07/02/2022 XXXX
				$audit_observaciones = null;
				// Si no se recibe el Id de la Codificadora del Tema
				if (is_null($pautor_tipo) && is_null($pautor_codigo)) {
					$audit_observaciones = "AUTOMATICO, al eliminar un Expediente.";
				}

				NG::auditorias()->auditarComoExpediente(
					$dummy, null, Auditoria::OP_BAJA, 'hcd.expe_autores', null, null, $audit_observaciones);
				//NG::auditorias()->generarMensajeEliminacion($resultado, array($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pautor_tipo, $pautor_codigo, $pautor_bloque_tipo, $pautor_bloque_codigo, $pid_usuario))
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarAutores: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina una instancia de tipo Autor en base a su identificador.
	 * GenerateClass 0.97.5 beta @ 2016-09-02 13:55:07
	 * @param  Autor $pAutor 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarAutor(Autor $pAutor) {
		if (is_null($pAutor)) {
			throw new Exception(sprintf("Error en %s.eliminarAutor: la instancia a eliminar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarAutores($pAutor->anio, $pAutor->tipo, $pAutor->numero, $pAutor->cuerpo, $pAutor->alcance, $pAutor->autor_tipo, $pAutor->autor_codigo);

			if ($resultado > 1) {
				throw new Exception(sprintf("Error en %s.eliminarAutor: se quiso eliminar m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarAutor: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return ($resultado == 1);
	}

	// ************************************************************************
	// Estados
	// ************************************************************************

	/**
	 * NGExpedientes: Obtiene una coleccion de elementos tipo Estado en base a diferentes criterios de selección.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string (PK) fecha_estado
	 * @param  float (PK) orden_estado
	 * @param  integer id_codestado
	 * @param  string observaciones_estado
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<Estado>
	 */
	public function obtenerEstados(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pfecha_estado = null,
		$porden_estado = null,
		$pid_codestado = null,
		$pobservaciones_estado = null,
		$pid_usuario = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBExpedientes()->obtenerEstados($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pfecha_estado, $porden_estado, $pid_codestado, $pobservaciones_estado, $pid_usuario,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerEstados: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Estado
		$resultado = $this->arrayResultToInstance($filas, 'Estado');

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Determina la cantidad de elementos tipo Estado obtenidos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string (PK) fecha_estado
	 * @param  float (PK) orden_estado
	 * @param  integer id_codestado
	 * @param  string observaciones_estado
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerEstadosCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pfecha_estado = null,
		$porden_estado = null,
		$pid_codestado = null,
		$pobservaciones_estado = null,
		$pid_usuario = null) {
		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBExpedientes()->obtenerEstadosCantidad($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pfecha_estado, $porden_estado, $pid_codestado, $pobservaciones_estado, $pid_usuario);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerEstadosCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * DBExpedientes: a partir de una determinada clave de expediente, retorna el número de orden siguiente del último estado insertado.
	 * En caso de que no existan estados para un determinado año y tipo, devuelve cero.
	 * @param  [type] $panio         [description]
	 * @param  [type] $ptipo         [description]
	 * @param  [type] $pnumero       [description]
	 * @param  [type] $pcuerpo       [description]
	 * @param  [type] $palcance      [description]
	 * @param  [type] $pfecha_estado [description]
	 * @return int                [description]
	 */
	public function obtenerNumeroSiguienteEstado(
		// Parametros
		$panio,
		$ptipo,
		$pnumero,
		$pcuerpo,
		$palcance,
		$pfecha_estado) {

		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBExpedientes()->obtenerNumeroUltimoEstado($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pfecha_estado);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerNumeroSiguienteEstado: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado + 1;
	}

	/**
	 * NGExpedientes: Obtiene una instancia de tipo Estado en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string (PK) fecha_estado
	 * @param  float (PK) orden_estado
	 * @return Estado Instancia de Estado buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerEstado(
		// Parametros
		$panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pfecha_estado, $porden_estado) {
		if (is_null($panio) || is_null($ptipo) || is_null($pnumero) || is_null($pcuerpo) || is_null($palcance) || is_null($pfecha_estado) || is_null($porden_estado)) {
			throw new Exception(sprintf("Error en %s.obtenerEstado: los campos clave no pueden ser nulos.", get_class($this)));
		}

		$resultado = $this->obtenerEstados($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pfecha_estado, $porden_estado);

		if (count($resultado) == 0) {
			return null;
		} else if (count($resultado) == 1) {
			return $resultado[0];
		} else {
			throw new Exception(sprintf("Error en %s.obtenerEstado: se encontr&oacute; m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
		}

	}

	/**
	 * Guarda una instancia de tipo Estado. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  Estado $pEstado 	Instancia a guardar.
	 * @param  boolean $pRecargar 		Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return Estado               Instancia guardada.
	 */
	public function guardarEstado(Estado $pEstado, $pRecargar = true) {
		if (is_null($pEstado)) {
			throw new Exception(sprintf("Error en %s.guardarEstado: la instancia a guardar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// 04/02/2022 XXXX, se modifica la información a auditar

			$estado_actual = $this->obtenerEstado($pEstado->anio, $pEstado->tipo, $pEstado->numero, $pEstado->cuerpo, $pEstado->alcance, $pEstado->fecha_estado, $pEstado->orden_estado);

			// Si no existe
			if (is_null($estado_actual)) {
				// Se define la operación a auditar, ante el Alta del Estado
				$audit_operacion = Auditoria::OP_ALTA;

				$audit_observaciones = "Estado Id: ".$pEstado->id_codestado;
				$audit_observaciones .= (is_null($pEstado->observaciones_estado)) ? "" : "<br>Observaciones: ".$pEstado->observaciones_estado;
			} else {

				// Se define la operación a auditar, ante la Modificación del Estado
				$audit_operacion = Auditoria::OP_MODIFICA;

				$audit_observaciones = "Estado<br>";
				$audit_observaciones .= "ANTERIOR<br>";
				$audit_observaciones .= "Id: ".$estado_actual->id_codestado;
				$audit_observaciones .= (is_null($pEstado->observaciones_estado)) ? "" : "<br>Observaciones: ".$estado_actual->observaciones_estado;
				$audit_observaciones .= "<br>ACTUAL<br>";
				$audit_observaciones .= "Id: ".$pEstado->id_codestado;
				$audit_observaciones .= (is_null($pEstado->observaciones_estado)) ? "" : "<br>Observaciones: ".$pEstado->observaciones_estado;
			}

			//$audit_operacion = (is_null($this->obtenerEstado($pEstado->anio, $pEstado->tipo, $pEstado->numero, $pEstado->cuerpo, $pEstado->alcance, $pEstado->fecha_estado, $pEstado->orden_estado))) ? Auditoria::OP_ALTA : Auditoria::OP_MODIFICA;

			$id = DB::getInstanceDBExpedientes()->guardarEstado(
				$pEstado->anio,
				$pEstado->tipo,
				$pEstado->numero,
				$pEstado->cuerpo,
				$pEstado->alcance,
				$pEstado->fecha_estado,
				$pEstado->orden_estado,
				$pEstado->id_codestado,
				$pEstado->observaciones_estado,
				$pEstado->id_usuario);

			// Auditoria
			NG::auditorias()->auditarComoExpediente(
				$pEstado, null, $audit_operacion, 'hcd.expe_estados',
				$pEstado->fecha_estado, $pEstado->orden_estado, $audit_observaciones);

			//sprintf('Se ha guardado el estado %d-%s-%d-%d-%d, fecha: %s, orden: %s', $pEstado->anio, $pEstado->tipo, $pEstado->numero, $pEstado->cuerpo, $pEstado->alcance, $pEstado->fecha_estado, $pEstado->orden_estado)

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarEstado: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$resultado = $this->obtenerEstado($pEstado->anio, $pEstado->tipo, $pEstado->numero, $pEstado->cuerpo, $pEstado->alcance, $pEstado->fecha_estado, $pEstado->orden_estado);
		} else {
			$resultado = $pEstado;
		}

		DB::getInstanceDBExpedientes()->desconectar();

		if (is_null($resultado)) {
			throw new Exception(sprintf("Error grave en %s.guardarEstado: no se encuentra el contenido actualizado.", get_class($this)));
		}

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina un conjunto de Estados en base a diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-02 13:55:07
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string (PK) fecha_estado
	 * @param  float (PK) orden_estado
	 * @param  integer id_codestado
	 * @param  string observaciones_estado
	 * @param  integer id_usuario
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarEstados(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pfecha_estado = null,
		$porden_estado = null,
		$pid_codestado = null,
		$pobservaciones_estado = null,
		$pid_usuario = null) {
		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// 07/02/2022 XXXX, se modifica la información al auditar la eliminación
			$audit_observaciones = null;
			if (is_null($pfecha_estado) && is_null($porden_estado)) {
				$audit_observaciones = "AUTOMATICO, al eliminar un Expediente.";
			} else {
				$estado_a_eliminar = $this->obtenerEstado($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pfecha_estado, $porden_estado);

				$audit_observaciones = "Estado Id: ".$estado_a_eliminar->id_codestado;
			}

			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBExpedientes()->eliminarEstados($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pfecha_estado, $porden_estado, $pid_codestado, $pobservaciones_estado, $pid_usuario);

			// Auditoria (si es que se elimino algo, tengo que auditar)
			if ($resultado > 0) {
				$dummy = new Estado($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pfecha_estado, $porden_estado, $pid_codestado, $pobservaciones_estado, $pid_usuario);

				NG::auditorias()->auditarComoExpediente(
					$dummy, null, Auditoria::OP_BAJA, 'hcd.expe_estados',
					$pfecha_estado, $porden_estado, $audit_observaciones);

			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarEstados: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina una instancia de tipo Estado en base a su identificador.
	 * GenerateClass 0.97.5 beta @ 2016-09-02 13:55:07
	 * @param  Estado $pEstado 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarEstado(Estado $pEstado) {
		if (is_null($pEstado)) {
			throw new Exception(sprintf("Error en %s.eliminarEstado: la instancia a eliminar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarEstados($pEstado->anio, $pEstado->tipo, $pEstado->numero, $pEstado->cuerpo, $pEstado->alcance, $pEstado->fecha_estado, $pEstado->orden_estado);

			if ($resultado > 1) {
				throw new Exception(sprintf("Error en %s.eliminarEstado: se quiso eliminar m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarEstado: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return ($resultado == 1);
	}

	// ************************************************************************
	// Expedientes
	// ************************************************************************

	/**
	 * NGExpedientes: toma una instancia de Expediente y completa sus colecciones de instancias asociadas (conjunto de instancias asociadas).
	 * @param  Expediente $expediente Expediente del cual se desea completar sus colecciones internas.
	 * @return Expediente                 Expediente del cual se desea completar sus colecciones internas.
	 */
	public function completarInstanciaExpediente(Expediente $expediente) {

		if (is_null($expediente)) {
			throw new Exception(sprintf("Error en %s.completarInstanciaExpediente: %s", get_class($this), "La instancia del expediente a completar no puede ser nula."));
		}

		$expediente->getAntecedentes()->fillFromArray($this->obtenerAntecedentes($expediente->anio, $expediente->tipo, $expediente->numero, $expediente->cuerpo, $expediente->alcance));
		$expediente->getAutores()->fillFromArray($this->obtenerAutores($expediente->anio, $expediente->tipo, $expediente->numero, $expediente->cuerpo, $expediente->alcance));
		$expediente->getEstados()->fillFromArray($this->obtenerEstados($expediente->anio, $expediente->tipo, $expediente->numero, $expediente->cuerpo, $expediente->alcance, null, null, null, null, null,
			array('anio', 'tipo', 'numero', 'cuerpo', 'alcance', 'fecha_estado', 'orden_estado')));
		$expediente->getGiros()->fillFromArray($this->obtenerGiros($expediente->anio, $expediente->tipo, $expediente->numero, $expediente->cuerpo, $expediente->alcance));
		$expediente->getProyectos()->fillFromArray($this->obtenerProyectos($expediente->anio, $expediente->tipo, $expediente->numero, $expediente->cuerpo, $expediente->alcance));
		$expediente->getSanciones()->fillFromArray($this->obtenerSanciones($expediente->anio, $expediente->tipo, $expediente->numero, $expediente->cuerpo, $expediente->alcance));
		$expediente->getTemas()->fillFromArray($this->obtenerTemas($expediente->anio, $expediente->tipo, $expediente->numero, $expediente->cuerpo, $expediente->alcance));
		$expediente->getInformes()->fillFromArray($this->obtenerInformes($expediente->anio, $expediente->tipo, $expediente->numero, $expediente->cuerpo, $expediente->alcance));

		return $expediente;
	}

	/**
	 * NGExpedientes: toma una instancia de Expediente y completa sus colecciones de instancias asociadas (conjunto de instancias asociadas).
	 * @param  Expediente $expediente Expediente del cual se desea completar sus colecciones internas.
	 * @return Expediente                 Expediente del cual se desea completar sus colecciones internas.
	 */
	public function completarInstanciaSoloConProyectos(Expediente $expediente) {
		if (is_null($expediente)) {
			throw new Exception(sprintf("Error en %s.completarInstanciaExpediente: %s", get_class($this), "La instancia del expediente a completar no puede ser nula."));
		}

		$expediente->getProyectos()->fillFromArray($this->obtenerProyectos($expediente->anio, $expediente->tipo, $expediente->numero, $expediente->cuerpo, $expediente->alcance));

		return $expediente;
	}

	/**
	 * NGExpedientes: Obtiene una coleccion de elementos tipo Expediente en base a diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:27
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string iniciador_tipo
	 * @param  string iniciador_codigo
	 * @param  string iniciador_bloque_tipo
	 * @param  string iniciador_bloque_codigo
	 * @param  integer agregado_anio
	 * @param  string agregado_tipo
	 * @param  float agregado_numero
	 * @param  integer agregado_cuerpo
	 * @param  integer agregado_alcance
	 * @param  integer id_codcategoria
	 * @param  mixed fecha_entrada_expe Si el parametro es una fecha, busca una coincidencia exacta. Si es un array (de dos elementos), busca elem_1 <= fecha_entrada_expe <= elem_2.
	 * @param  string caratula
	 * @param  string observaciones_expe
	 * @param  integer marca_comision
	 * @param  integer id_usuario
	 * @param  boolean pInstanciasCompletas Devuelve las instancias de expediente completas en vez de solo la cabecera.
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<Expediente>
	 */
	public function obtenerExpedientes(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$piniciador_tipo = null,
		$piniciador_codigo = null,
		$piniciador_bloque_tipo = null,
		$piniciador_bloque_codigo = null,
		$pagregado_anio = null,
		$pagregado_tipo = null,
		$pagregado_numero = null,
		$pagregado_cuerpo = null,
		$pagregado_alcance = null,
		$pid_codcategoria = null,
		$pfecha_entrada_expe = null,
		$pcaratula = null,
		$pobservaciones_expe = null,
		$pmarca_comision = null,
		$pid_usuario = null,
		$pInstanciasCompletas = false,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBExpedientes()->obtenerExpedientes($panio, $ptipo, $pnumero, $pcuerpo, $palcance,
				$piniciador_tipo, $piniciador_codigo, $piniciador_bloque_tipo, $piniciador_bloque_codigo,
				$pagregado_anio, $pagregado_tipo, $pagregado_numero, $pagregado_cuerpo, $pagregado_alcance,
				$pid_codcategoria, $pfecha_entrada_expe, $pcaratula, $pobservaciones_expe, $pmarca_comision, $pid_usuario,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientes: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Expediente
		$resultado = $this->arrayResultToInstance($filas, 'Expediente');

		// Obtengo el resto de la información si se solicita la instancia completa
		if ($pInstanciasCompletas) {
			foreach ($resultado as $exp) {
				$exp = $this->completarInstanciaExpediente($exp);
			}
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Obtiene una coleccion de elementos tipo Expediente en base a diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:27
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string 	pCriterio	Operador lógico con el cual realizar la comparación. Pueden utilizarse las constantes IGUAL_A, DISTINTO_A, MAYOR_A, MAYOR_IGUAL_A, MENOR_A, MENOR_IGUAL_A.
	 * @param  boolean pInstanciasCompletas Devuelve las instancias de expediente completas en vez de solo la cabecera.
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array<Expediente>
	 */
	public function obtenerExpedientesPagina(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pCriterio = IGUAL_A,
		$pInstanciasCompletas = false,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBExpedientes()->obtenerExpedientesPagina($panio, $ptipo, $pnumero, $pcuerpo, $palcance,
				$pCriterio, $pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesPagina: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Expediente
		$resultado = $this->arrayResultToInstance($filas, 'Expediente');

		// Obtengo el resto de la información si se solicita la instancia completa
		if ($pInstanciasCompletas) {
			foreach ($resultado as $exp) {
				$exp = $this->completarInstanciaExpediente($exp);
			}
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Obtiene una instancia del ultimo expediente ingresado.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * @param  boolean $pInstanciasCompletas Obtiene la instancia completa del expediente
	 * @return Expediente Instancia de Expediente buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerExpedienteUltimo($pInstanciasCompletas = false) {
		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::expedientes()->obtenerExpedienteUltimo();
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedienteUltimo: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Expediente
		$resultado = $this->arrayResultToInstance($filas, 'Expediente');

		// Obtengo el resto de la información si se solicita la instancia completa
		if ($pInstanciasCompletas) {
			foreach ($resultado as $exp) {
				$exp = $this->completarInstanciaExpediente($exp);
			}
		}

		DB::getInstanceDBExpedientes()->desconectar();

		if (count($resultado) == 0) {
			return null;
		} else if (count($resultado) == 1) {
			$e = $resultado[0];
			return $e;
		} else {
			throw new Exception(sprintf("Error en %s.obtenerExpedienteUltimo: se encontr&oacute; m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
		}

	}

	/**
	 * NGExpedientes: Determina la cantidad de elementos tipo Expediente obtenidos en base a diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:27
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string iniciador_tipo
	 * @param  string iniciador_codigo
	 * @param  string iniciador_bloque_tipo
	 * @param  string iniciador_bloque_codigo
	 * @param  integer agregado_anio
	 * @param  string agregado_tipo
	 * @param  float agregado_numero
	 * @param  integer agregado_cuerpo
	 * @param  integer agregado_alcance
	 * @param  integer id_codcategoria
	 * @param  mixed fecha_entrada_expe Si el parametro es una fecha, busca una coincidencia exacta. Si es un array (de dos elementos), busca elem_1 <= fecha_entrada_expe <= elem_2.
	 * @param  string caratula
	 * @param  string observaciones_expe
	 * @param  integer marca_comision
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerExpedientesCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$piniciador_tipo = null,
		$piniciador_codigo = null,
		$piniciador_bloque_tipo = null,
		$piniciador_bloque_codigo = null,
		$pagregado_anio = null,
		$pagregado_tipo = null,
		$pagregado_numero = null,
		$pagregado_cuerpo = null,
		$pagregado_alcance = null,
		$pid_codcategoria = null,
		$pfecha_entrada_expe = null,
		$pcaratula = null,
		$pobservaciones_expe = null,
		$pmarca_comision = null,
		$pid_usuario = null) {
		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBExpedientes()->obtenerExpedientesCantidad($panio, $ptipo, $pnumero, $pcuerpo, $palcance,
				$piniciador_tipo, $piniciador_codigo, $piniciador_bloque_tipo, $piniciador_bloque_codigo, $pagregado_anio, $pagregado_tipo, $pagregado_numero, $pagregado_cuerpo, $pagregado_alcance,
				$pid_codcategoria, $pfecha_entrada_expe, $pcaratula, $pobservaciones_expe, $pmarca_comision, $pid_usuario);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGExpedientes: Obtiene una instancia de tipo Expediente en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:27
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  boolean $pInstanciasCompletas Obtiene la instancia completa del expediente
	 * @return Expediente Instancia de Expediente buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerExpediente(
		// Parametros
		$panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pInstanciasCompletas = false) {
		if (is_null($panio) || is_null($ptipo) || is_null($pnumero) || is_null($pcuerpo) || is_null($palcance)) {
			throw new Exception(sprintf("Error en %s.obtenerExpediente: los campos clave no pueden ser nulos.", get_class($this)));
		}

		$resultado = $this->obtenerExpedientes($panio, $ptipo, $pnumero, $pcuerpo, $palcance,
			null, null, null, null, null, null, null, null, null, null, null, null, null, null, null,
			$pInstanciasCompletas);

		if (count($resultado) == 0) {
			return null;
		} else if (count($resultado) == 1) {
			$e = $resultado[0];
			return $e;
		} else {
			throw new Exception(sprintf("Error en %s.obtenerExpediente: se encontr&oacute; m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
		}

	}

	/**
	 * NGExpedientes: a partir de un determinado anio y tipo de expediente, retorna el número siguiente al último expediente que fue agregado.
	 * @param  int 		$panio [description]
	 * @param  string 	$ptipo [description]
	 * @return int    Números de último expediente a ser insertado para un año/tipo.
	 */
	public function obtenerNumeroSiguienteExpediente($panio, $ptipo) {
		if (is_null($panio) || is_null($ptipo)) {
			throw new Exception(sprintf("Error en %s.obtenerNumeroSiguienteExpediente: a&ntilde;o y tipo no pueden ser nulos.", get_class($this)));
		}

		DB::expedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$resultados = DB::expedientes()->obtenerNumeroUltimoExpediente($panio, $ptipo);
		} catch (Exception $e) {
			DB::expedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerNumeroSiguienteExpediente: %s", get_class($this), $e->getMessage()));
		}

		DB::expedientes()->desconectar();

		if (count($resultados) == 0) {
			switch ($ptipo) {
			case 'E':return 1001;
			case 'N':return 1;
			case 'R':return 1;
			default:
				throw new Exception(sprintf("Error en %s.obtenerNumeroSiguienteExpediente: tipo de expediente inválido.", get_class($this)));
			}
		} else if (count($resultados) == 1) {
			return $resultados[0]['ultimo_numero'] + 1;
		} else {
			throw new Exception(sprintf("Error en %s.obtenerNumeroSiguienteExpediente: se encontr&oacute; m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
		}

	}

	/**
	 * Obtiene el conjunto de archivos de proyecto que estan disponibles "para cargar".
	 * @return array   $archivos 	Array asociativo con los archivos del expediente. Cada elemento del array posee "tipo", "archivo", "ruta_completa", "url" y "expediente"
	 */
	public function obtenerArchivosACargar() {
		$archivos = array();

		// Obtengo los archivos del directorio
		$dirContent = scandir(PATH_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES);

		// Expresion regular para considerar solamente aquellos archivos que cumplen con la nomenclatura.
		$archivosPermitidos = preg_grep('/^[0-9]{2,2}(E|N|R)[0-9]{5,5}\.(doc|docx|odt)$/', $dirContent);

		$expData = array();
		foreach ($archivosPermitidos as $a) {
			// Obtengo el expediente asociado
			preg_match('/^([0-9]{2,2})(E|N|R)([0-9]{5,5})\.(doc|docx|odt)$/', $a, $expData);
			$e_anio = ($expData[1] >= 83) ? $expData[1] + 1900 : $expData[1] + 2000;
			$e_tipo = $expData[2];
			$e_numero = $expData[3];

			// Como puede haber mas de un expediente para año/tipo/numero, me quedo con el primero que encuentro
			$expedientes = $this->obtenerExpedientes($e_anio, $e_tipo, $e_numero);
			$exp = (count($expedientes) > 0) ? $expedientes[0] : null;

			if ($exp != null) {
				$archivos[] = array(
					'tipo' => 'temporal',
					'archivo' => $a,
					'ruta_completa' => PATH_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES . $a,
					'url' => URL_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES . $a,
					'expediente' => $exp);
			}
		}

		return $archivos;
	}

	/**
	 * Busca un nombre de archivo para multiples extensiones
	 * @param  string $fileName  Nombre del archivo, SIN incluir el punto ni la extension.
	 * @param  array $extension Arreglo de extensiones a buscar, SIN el punto.
	 * @return string            Nombre del archivo encontrado, o cadena vacia si no existe.
	 */
	private function fileExistsExtension($fileName, $extensiones) {
		$resultado = '';

		foreach ($extensiones as $key => $value) {
			if (file_exists($fileName . '.' . $value)) {
				$resultado = $fileName . '.' . $value;
			}
		}

		return $resultado;
	}

	/**
	 * Busca el archivo original de un determinado expediente y devuelve su nombre.
	 * El archivo original puede ser .doc, .docx o .odt. Si no lo encuentra, devuelve una cadena vacia.
	 * @param  [type] $pAnio   [description]
	 * @param  [type] $pTipo   [description]
	 * @param  [type] $pNumero [description]
	 * @return [type]          [description]
	 */
	private function buscarDocumentoOriginal($pAnio, $pTipo, $pNumero) {
		$archivo = sprintf('%s%s/%s/original',
			PATH_KRAKEN_RESOURCES_PROYECTOS,
			$pAnio,
			sprintf("%02d%s%05d", $pAnio % 100, $pTipo, $pNumero));

		return $this->fileExistsExtension($archivo, array('doc', 'docx', 'odt'));
	}

	/**
	 * Sube un archivo temporal como proyecto, renombrandolo en base a un expediente.
	 * @param  Expediente $pExpediente     Expediente al cual asignar el archivo temporal
	 * @param  array    &$filesReference   Referencia al objeto de formulario que contiene la información del archivo.
	 * @param  string   $fileFormFieldId   Dentro del $filesReference, el identificador del archivo que se desea subir (nombre del campo del formulario, si aplica)
	 * @return Expediente                  Expediente al cual se asignó el archivo temporal
	 */
	public function subirArchivoTemporal(Expediente $pExpediente, &$filesReference, $fileFormFieldId) {
		// Intento guardar el documento
		if ($filesReference[$fileFormFieldId]['error'] != UPLOAD_ERR_NO_FILE) {
			$fileHelper = new FileHelper($filesReference, PATH_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES);
			$fileHelper->mimeTypePorExtension = array(
				'doc' => 'application/msword',
				'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
				'odt' => 'application/vnd.oasis.opendocument.text');

			try {
				$extensionArchivo = pathinfo($filesReference[$fileFormFieldId]['name'], PATHINFO_EXTENSION);

				// Si NO es una extensión permitida
				if (!in_array($extensionArchivo, array("doc", "docx", "odt"))) {
					return null;
				}

				// Se genera el nombre con el que se va a guardar dicho temporal, AATNNNNN con su extensión
				$nuevoNombre = sprintf('%02d%s%05d.%s',
					($pExpediente->anio % 100),
					strtoupper($pExpediente->tipo),
					$pExpediente->numero,
					$extensionArchivo
				);

				// SE UTILIZARÁ AL CAMBIAR DE VERSIÓN EL SERVIDOR !!!! (PARA PODER INSTALAR LA LIBRERIA PARA LA CONVERSIÓN)
				// Se convierte a ".doc" antes de subirlo
				//$convertido_to_doc = $this->convertirToDoc('doc', $nuevoNombre);

				// Se sube el archivo.
				// Se pasa como primer parametro $fileFormFieldId: el nombre del elemento dentro del array de archivos (el nombre del input)
				$archivo = $fileHelper->subirArchivoComo(
					$fileFormFieldId,
					$nuevoNombre,
					'localhost',
					FTP_LOCAL_USER,
					FTP_LOCAL_PASSWORD,
					0664
				);

				// EL usuario de bloque no tiene permiso para auditar !!!!!!!
				// REVISAR la Auditoría para hacerla con un usuario válido
				// ----------------------------------------------------------------
				// // Auditoria
				// NG::auditorias()->auditarComoExpediente(
				// 	$pExpediente,
				// 	null,
				// 	Auditoria::OP_ARCHIVO_SUBIDO,
				// 	sprintf('%s%s', PATH_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES, $nuevoNombre),
				// 	null, null,
				// 	sprintf('Se ha subido el archivo \'%s\' como proyecto temporal.', $nuevoNombre)
				// );

				return $nuevoNombre;

			} catch (Exception $exUpload) {
				// Quito el identificador del fileHelper
				$mensajeErrorSubirArchivo = str_replace('FileHelper.subirArchivoComo: ', '', $exUpload->getMessage());
				throw new Exception('No se pudo cargar el documento. Causa: ' . $mensajeErrorSubirArchivo);
			}
		} else {
			return null;
		}
	}

	/**
	 * Mueve un conjunto de archivos temporales de documentos de proyecto a la carpeta correspondiente.
	 * Crea los directorios destino si estos no existiesen.
	 * @param  array  $archivos      Array con los nombres de archivo a mover (temporales).
	 * @param  boolean $forzarDestino
	 * Si es TRUE y el archivo 'original.doc' existiese en el destino, renombra el existente y lo reemplaza con el nuevo.
	 * Si es FALSE, marca como error si 'original.doc' ya existiese.
	 * @return array                 Status de resultado de movimiento.
	 */
	public function moverArchivosTemporales($archivos, $forzarDestino = false) {
		$resultado = array();
		$archivosAuditables = array();

		$permisosDirectorio = 0775; // sudo find . -type d -exec chmod 775 {} +
		$permisosArchivo = 0664; // sudo find . -type f -exec chmod 664 {} +

		// Expresion regular para considerar solamente aquellos archivos que cumplen con la nomenclatura.
		$archivosPermitidos = preg_grep('/^[0-9]{2,2}(E|N|R)[0-9]{5,5}\.(doc|docx|odt)$/', $archivos);

		foreach ($archivosPermitidos as $a) {
			// Obtengo el expediente asociado
			preg_match('/^([0-9]{2,2})(E|N|R)([0-9]{5,5})\.(doc|docx|odt)$/', $a, $expData);
			$e_anio = ($expData[1] >= 83) ? $expData[1] + 1900 : $expData[1] + 2000;
			$e_tipo = $expData[2];
			$e_numero = $expData[3];
			$e_ext_temp = $expData[4]; // extension del archivo temporal
			$nombreCodificado = sprintf("%02d%s%05d", $e_anio % 100, $e_tipo, $e_numero);
			$directorioDestino = PATH_KRAKEN_RESOURCES_PROYECTOS . $e_anio . '/' . $nombreCodificado;
			$archivoDestino = $directorioDestino . '/original.' . $e_ext_temp; // mantengo la extension original del temporal

			// Verifico la existencia del archivo
			$archivoOrigen = PATH_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES . $a;
			if (!file_exists($archivoOrigen)) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => 'El documento no existe.');
				continue; // Salteo esta vuelta del foreach y continuo con el archivo siguiente.
			}

			// Verifico la existencia del expediente
			$expedientes = $this->obtenerExpedientes($e_anio, $e_tipo, $e_numero);
			if (count($expedientes) == 0) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => sprintf('El expediente %d-%s-%d no existe.', $e_anio, $e_tipo, $e_numero));
				continue; // Salteo esta vuelta del foreach y continuo con el archivo siguiente.
			}

			// Verifico que no exista el destino (dependiendo del flag $forzarDestino)
			$archivoOriginal = $this->buscarDocumentoOriginal($e_anio, $e_tipo, $e_numero);
			if ((!$forzarDestino) && $archivoOriginal != '') {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'WARNING',
					'mensaje' => sprintf('El documento original ya existe para el expediente %d-%s-%d.', $e_anio, $e_tipo, $e_numero));
				continue; // Salteo esta vuelta del foreach y continuo con el archivo siguiente.
			}

			// Si no existe el directorio destino, lo creo (recursivamente)
			if (!file_exists($directorioDestino)) {
				try {
					// 15/07/2020 XXXX
					FTPHelper::get()->crearDirectorioProyecto($e_anio . '/' . $nombreCodificado, $permisosDirectorio);
				} catch (Exception $ex) {
					$resultado[] = array(
						'archivo' => $a,
						'estado' => 'ERROR',
						'mensaje' => $ex->getMessage());
					continue; // Salteo esta vuelta del foreach y continuo con el archivo siguiente.
				}
			} else if (!is_dir($directorioDestino)) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => 'El directorio destino existe, pero es un archivo.');
				continue; // Salteo esta vuelta del foreach y continuo con el archivo siguiente.
			}

			// Si el destino ya existe, lo muevo, es decir, creo un backup (dependiendo del flag $forzarDestino)
			$flagMovido = false;
			if ($archivoOriginal != '' && $forzarDestino) {

				$archivoDestinoBackupNombre = pathinfo($archivoOriginal, PATHINFO_BASENAME);
				$archivoDestinoBackupNombre = str_replace('original', 'original_' . date('Ymd_His'), $archivoDestinoBackupNombre);
				$archivoDestinoBackup = $directorioDestino . '/' . $archivoDestinoBackupNombre;

				try {
					FTPHelper::get()->moveFileTemporal($archivoOriginal, $archivoDestinoBackup, $permisosArchivo);
					$flagMovido = true;
				} catch (Exception $ex) {
					$resultado[] = array(
						'archivo' => $a,
						'estado' => 'ERROR',
						'mensaje' => 'No se pudo renombrar el documento original existente.');
					continue; // Salteo esta vuelta del foreach y continuo con el archivo siguiente.
				}
			}

			try {
				//FTPHelper::get()->moveFileTemporal($archivoOrigen, $archivoDestino, $permisosArchivo);
				// 18/08/2020 XXXX
				// Se carga el archivo temporal en el directorio del expediente respectivo
				$this->moverTemporalToDirectorioFinal($archivoOrigen, $archivoDestino, $e_anio, $e_tipo, $e_numero);

			} catch (Exception $ex) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => 'No se pudo mover el documento al directorio destino.');
				continue; // Salteo esta vuelta del foreach y continuo con el archivo siguiente.
			}

			$mensajeFinal = sprintf('El documento %s fue asignado con &eacute;xito al expediente %d-%s-%d.', $a, $e_anio, $e_tipo, $e_numero);
			$archivosAuditables[] = sprintf('%s -> %s', basename($archivoOrigen), $archivoDestino);
			if ($flagMovido) {
				$mensajeFinal = sprintf('%s Se conserva el proyecto anterior como "%s".', $mensajeFinal, $archivoDestinoBackupNombre);
				$archivosAuditables[] = sprintf('%s -> %s [backup]', basename($archivoOriginal), $archivoDestinoBackup);
			}

			$resultado[] = array(
				'archivo' => $a,
				'estado' => 'OK',
				'mensaje' => $mensajeFinal);
		}

		return $resultado;
	}

	/**
	 * HECHO
	 * Se carga el archivo temporal en el directorio del expediente respectivo
	 * @param  [type]  $origen   	Ruta del archivo temporal
	 * @param  [type]  $destino  	Ruta destino para el archivo
	 * @param  [type]  $e_anio   	Año del expediente
	 * @param  [type]  $e_tipo   	Tipo del expediente
	 * @param  [type]  $e_numero   	Número del expediente
	 */
	public function moverTemporalToDirectorioFinal($origen, $destino, $e_anio, $e_tipo, $e_numero) {

		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');
		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		if (is_null($id_conexion) || is_null($resultado_login)) {
			throw new Exception('No conectado al servicio de manipulación de archivos.');
		}

		// Se carga el archivo origen (el temporal) en el directorio destino
		if (!ftp_put($id_conexion, $destino, $origen, FTP_BINARY)) {
			throw new Exception('No se puede cargar el archivo.');
		} else {
			// Se elimina el temporal del directorio "proyectos/temporal/" del expediente respectivo
			if (file_exists($origen)) {
				if (ftp_delete($id_conexion, $origen)) {

					// Como puede haber mas de un expediente para año/tipo/numero, me quedo con el primero que encuentro
					$expedientes = $this->obtenerExpedientes($e_anio, $e_tipo, $e_numero);
					$exp = (count($expedientes) > 0) ? $expedientes[0] : null;

					// Se audita la carga del documento
					NG::auditorias()->auditarComoExpediente(
						$exp,
						null,
						Auditoria::OP_ARCHIVO_MOVIDO,
						$destino,
						null, null,
						sprintf('Se ha cargado el documento.')
					);
				}
			}
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);
	}

	/**
	 * Obtiene un array asociativo con los archivos 'de proyecto' correspondientes a un determinado expediente.
	 * @param  Expediente $pExpediente Expediente del cual se desean obtener los archivos de proyecto.
	 * @return array                  Array asociativo con los archivos del expediente.
	 * Cada elemento del array posee "tipo", "archivo", "ruta_completa", "url" y "expediente"
	 */
	public function obtenerArchivosProyecto(Expediente $pExpediente) {
		if (is_null($pExpediente)) {
			throw new Exception(sprintf("Error en %s.obtenerArchivosProyecto: la instancia a verificar no puede ser nula.", get_class($this)));
		}

		$archivos = array();

		// Nombre codificado del directorio o archivo de proyecto.
		$nombreCodificado = sprintf("%02d%s%05d", $pExpediente->anio % 100, $pExpediente->tipo, $pExpediente->numero);

		// Ruta del directorio remoto determinado por la clave del expediente respectivo
		$directorioRemoto = PATH_KRAKEN_RESOURCES_PROYECTOS . $pExpediente->anio . "/" . $nombreCodificado . "/";

		// Verifico la existencia del proyecto temporal (puedo tener varios con distintas extensiones)
		$archivosTemporales = preg_grep('/' . $nombreCodificado . '\.(doc|docx|odt)$/', scandir(PATH_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES));
		foreach ($archivosTemporales as $temporal) {
			$archivos[] = array(
				'tipo' => 'temporal',
				'archivo' => pathinfo($temporal, PATHINFO_BASENAME),
				'ruta_completa' => $temporal,
				// 01/07/2020 XXXX: se toma la fecha del documento
				'fecha' => date("d/m/Y", filemtime(PATH_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES . $temporal)),
				'url' => URL_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES . pathinfo($temporal, PATHINFO_BASENAME),
				'expediente' => $pExpediente);
		}

		// Verifico la existencia de los proyectos cargados.
		if (is_dir($directorioRemoto)) {
			// Se 'escanea' el directorio (se obtiene un array con los archivos que contiene)
			$dirContent = scandir($directorioRemoto);

			foreach ($dirContent as $key => $value) {
				// Si contiene por lo menos un archivo
				// 07/02/2023 XXXX: además que no sea el directorio 'electronico' ni 'reservados'
				if ( ! ( ($value == '.') or ($value == '..') or ($value == 'electronico') or ($value == 'reservados') ) )
				{
					// 04/11/2019 XXXX
					// Si el archivo es un .pdf, NO se considera Cargado
					// porque es una Digitalización, no un Proyecto.

					// Si no es un .pdf
					if ($value != $nombreCodificado . '.pdf') {
						$archivos[] = array(
							'tipo' => 'proyecto',
							'archivo' => $value,
							'ruta_completa' => $directorioRemoto . $value,
							// 01/07/2020 XXXX: se toma la fecha del documento
							'fecha' => date("d/m/Y", filemtime($directorioRemoto . $value)),
							'url' => URL_KRAKEN_RESOURCES_PROYECTOS . $pExpediente->anio . "/" . $nombreCodificado . "/" . $value,
							'expediente' => $pExpediente);
					}
				}
			}
		}

		// 01/07/2020 XXXX
		if (count($archivos) > 0) {
			// Se ordena el listado de archivos, por fecha de forma descendente
			foreach ($archivos as $key => $row) {
				$aux[$key] = substr($row['fecha'], 6, 4) . substr($row['fecha'], 3, 2) . substr($row['fecha'], 0, 2);
			}
			array_multisort($aux, SORT_DESC, $archivos);
		}

		return $archivos;
	}

	/**
	 * Determina el estado del proyecto de un expediente determinado, a partir de la existencia del archivo
	 * de proyecto correspondiente. Actualiza el atributo de estadoProyecto del expediente.
	 * @param  Expediente $pExpediente Expediente a verificar
	 * @return Expediente              Instancia modificada (la misma pasada como parametro)
	 */
	public function determinarEstadoProyecto(Expediente $pExpediente) {

		$urlProyectoTemporal = '';

		if (is_null($pExpediente)) {
			throw new Exception(sprintf("Error en %s.determinarEstadoProyecto: la instancia a verificar no puede ser nula.", get_class($this)));
		}

		// Supongo el estado sin cargar por defecto
		$estadoProyecto = ESTADO_PROYECTO_SIN_CARGAR;
		$urlProyecto = '';

		// Nombre codificado del directorio o archivo de proyecto.
		$nombreCodificado = sprintf("%02d%s%05d", $pExpediente->anio % 100, $pExpediente->tipo, $pExpediente->numero);

		// Obtengo los archivos del proyecto
		$archivos = $this->obtenerArchivosProyecto($pExpediente);

		// Verifico la existencia del proyecto "original"
		foreach ($archivos as $key => $value) {
			if ($value['tipo'] == 'proyecto') {

				if ($value['archivo'] == 'original.doc' ||
					$value['archivo'] == 'original.docx' ||
					$value['archivo'] == 'original.odt') {

					// Se define el estado "CARGADO"
					$estadoProyecto = ESTADO_PROYECTO_CARGADO;

					// URL del documento "original"
					$urlProyecto = URL_KRAKEN_RESOURCES_PROYECTOS . $pExpediente->anio . "/" . $nombreCodificado . "/" . $value['archivo'];
					// Si posee un original ya deja de buscar
					break;
				}
			}
		}

		if ($urlProyecto == '') {
			// Verifico la existencia del proyecto cargado.
			foreach ($archivos as $key => $value) {
				if ($value['tipo'] == 'proyecto') {

					if ($value['archivo'] == 'deforma.doc' ||
						$value['archivo'] == 'deforma.docx' ||
						$value['archivo'] == 'deforma.odt') {

						// Se define el estado "CARGADO"
						$estadoProyecto = ESTADO_PROYECTO_CARGADO;

						// URL del documento "deforma"
						$urlProyecto = URL_KRAKEN_RESOURCES_PROYECTOS . $pExpediente->anio . "/" . $nombreCodificado . "/" . $value['archivo'];
						// Si posee un deforma ya deja de buscar
						break;
					}
				}
			}
		}

		if ($urlProyecto == '') {
			// Verifico la existencia del proyecto cargado.
			foreach ($archivos as $key => $value) {
				if ($value['tipo'] == 'proyecto') {
					// Se define el estado "CARGADO"
					$estadoProyecto = ESTADO_PROYECTO_CARGADO;
					// De no tener un original o un deforma, se indica que posee otros documentos
					$urlProyecto = "sin_original_y_deforma";
				}
			}
		}

		// Verifico la existencia del proyecto temporal.
		// El estado "PARA CARGAR" es preponderante sobre el resto de estados.
		foreach ($archivos as $key => $value) {
			// Si es Temporal
			if ($value['tipo'] == 'temporal') {
				// 13/07/2020 XXXX
				// Si se encuentra CARGADO
				if ($estadoProyecto == ESTADO_PROYECTO_CARGADO) {
					// Se define como "Cargado | Para cargar"
					$estadoProyecto = ESTADO_PROYECTO_CARGADO_PARA_CARGAR;
				} else {
					// Sino se define solo "Para Cargar"
					$estadoProyecto = ESTADO_PROYECTO_PARA_CARGAR;
				}

				// La url es la del temporal
				$urlProyectoTemporal = URL_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES . $value['archivo'];
			}
		}

		// Actualizo la instancia del expediente.
		$pExpediente->estado_proyecto = $estadoProyecto;
		$pExpediente->url_proyecto = $urlProyecto;
		$pExpediente->url_proyecto_temporal = $urlProyectoTemporal;

		return $pExpediente;
	}

	/**
	 * Obtiene un array asociativo con los archivos 'de digitalización' correspondientes a un determinado expediente.
	 * @param  Expediente $pExpediente Expediente del cual se desean obtener los archivos de digitalización.
	 * @return array                  Array asociativo con los archivos del expediente. Cada elemento del array posee "tipo", "archivo", "ruta_completa", "url" y "expediente"
	 */
	public function obtenerArchivosDigitalizacion(Expediente $pExpediente) {
		if (is_null($pExpediente)) {
			throw new Exception(sprintf("Error en %s.obtenerArchivosDigitalizacion: la instancia a verificar no puede ser nula.", get_class($this)));
		}

		$archivos = array();

		// Nombre codificado del directorio o archivo de la digitalización
		$nombreCodificado = sprintf("%02d%s%05d", $pExpediente->anio % 100, $pExpediente->tipo, $pExpediente->numero);

		// Ruta del directorio remoto determinado por la clave del expediente respectivo
		$directorioRemoto = PATH_KRAKEN_RESOURCES_PROYECTOS . $pExpediente->anio . "/" . $nombreCodificado . "/";

		// Se verifica si encuentra la digitalización en el directorio temporal "digital/"
		// -------------------------------------------------------------------------------
		// con el nombre codificado = AAAATNNNNN
		$archivoTemporal = $this->fileExistsExtension(PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES . $nombreCodificado, array('pdf', 'PDF'));
		// con el nombre codificado + la letra "a" = AAAATNNNNNa
		$con_letra_a = $this->fileExistsExtension(PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES . $nombreCodificado . 'a', array('pdf', 'PDF'));
		// con el nombre codificado + la letra "A" = AAAATNNNNNA
		$con_letra_A = $this->fileExistsExtension(PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES . $nombreCodificado . 'A', array('pdf', 'PDF'));

		if ($archivoTemporal != '') {
			$archivos[] = array(
				'tipo' => 'temporal',
				'archivo' => $nombreCodificado . '.' . pathinfo($archivoTemporal, PATHINFO_EXTENSION),
				'ruta_completa' => $archivoTemporal,
				'url' => URL_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES . $nombreCodificado . '.' . pathinfo($archivoTemporal, PATHINFO_EXTENSION),
				'expediente' => $pExpediente);
		} else {
			// Si existe el temporal con la letra "a"
			if ($con_letra_a != '') {
				$archivos[] = array(
					'tipo' => 'temporal',
					'archivo' => $nombreCodificado . 'a.' . pathinfo($con_letra_a, PATHINFO_EXTENSION),
					'ruta_completa' => $con_letra_a,
					'url' => URL_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES . $nombreCodificado . 'a.' . pathinfo($con_letra_a, PATHINFO_EXTENSION),
					'expediente' => $pExpediente);
			}

			// Si existe el temporal con la letra "A"
			if ($con_letra_A != '') {
				$archivos[] = array(
					'tipo' => 'temporal',
					'archivo' => $nombreCodificado . 'A.' . pathinfo($con_letra_A, PATHINFO_EXTENSION),
					'ruta_completa' => $con_letra_A,
					'url' => URL_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES . $nombreCodificado . 'A.' . pathinfo($con_letra_A, PATHINFO_EXTENSION),
					'expediente' => $pExpediente);
			}
		}

		// Se verifica la existencia del directorio respectivo a la digitalización
		if (is_dir($directorioRemoto)) {
			// Si se encuentra la digitalización Cargada en el directorio correspondiente
			if (file_exists($directorioRemoto . $nombreCodificado . ".pdf")) {

				$archivos[] = array(
					'tipo' => 'digitalizada',
					'archivo' => $nombreCodificado . ".pdf",
					'ruta_completa' => $directorioRemoto . $nombreCodificado . ".pdf",
					'fecha' => date("d/m/Y", filemtime($directorioRemoto . $nombreCodificado . ".pdf")),
					'url' => URL_KRAKEN_RESOURCES_PROYECTOS . $pExpediente->anio . "/" . $nombreCodificado . "/" . $nombreCodificado . ".pdf",
					'expediente' => $pExpediente);
			}
		}

		return $archivos;
	}

	/**
	 * Determina el estado de la Digitalización de un expediente determinado, a partir de la existencia del archivo
	 * de la Digitalización correspondiente. Actualiza el atributo "estado_digitalizacion" del expediente.
	 * @param  Expediente $pExpediente Expediente a verificar
	 * @return Expediente              Instancia modificada (la misma pasada como parametro)
	 */
	public function determinarEstadoDigitalizacion(Expediente $pExpediente) {

		if (is_null($pExpediente)) {
			throw new Exception(sprintf("Error en %s.determinarEstadoDigitalizacion: la instancia a verificar no puede ser nula.", get_class($this)));
		}

		$urlDigitalizacion = '';

		// Nombre codificado para la digitalización
		$nombreCodificado = sprintf("%02d%s%05d", $pExpediente->anio % 100, $pExpediente->tipo, $pExpediente->numero);

		// Ruta del directorio remoto determinado por la clave del expediente respectivo
		$directorioRemoto = PATH_KRAKEN_RESOURCES_PROYECTOS . $pExpediente->anio . "/" . $nombreCodificado . "/";

		// 09/02/2023 XXXX
		// Si el expediente es anterior al año 2023
		if ($pExpediente->anio < 2023 )
		{
			// Por defecto se supone el estado "Sin cargar"
			$estadoDigitalizacion = ESTADO_DIGITALIZACION_SIN_CARGAR;

			// Se obtienen los archivos de la digitalizacion
			$archivos = $this->obtenerArchivosDigitalizacion($pExpediente);

			// Primero se verifica la existencia de una digitalizacion temporal
			foreach ($archivos as $key => $value) {
				// Si se encuentra
				if ($value['tipo'] == 'temporal') {
					// Estado 'Para Cargar'
					$estadoDigitalizacion = ESTADO_DIGITALIZACION_PARA_CARGAR;
					// URL de la digitalizacion temporal
					$urlDigitalizacion = $value['url'];
					// Se sale del ciclo de verificación
					break;
				}
			}

			// Si se encuentra la digitalización Cargada
			if (file_exists($directorioRemoto . $nombreCodificado . ".pdf"))
			{
				// Si previamente se verificó que hay un temporal para cargar
				if ($estadoDigitalizacion == ESTADO_DIGITALIZACION_PARA_CARGAR)
				{
					// Se establece el estado "Cargada o Completa y Para Cargar" (para la Vista Previa del Expediente)
					$estadoDigitalizacion = ESTADO_DIGITALIZACION_CARGADA_Y_PARA_CARGAR;
				} else {
					// Se
					$estadoDigitalizacion = ESTADO_DIGITALIZACION_CARGADA;
					$urlDigitalizacion = URL_KRAKEN_RESOURCES_PROYECTOS . $pExpediente->anio . '/' . $nombreCodificado . '.pdf';
				}
			}
		}
		else // Si el expediente es electrónico (a partir del año 2023)
		{
			// Por defecto, se considera que no tiene documentación electrónica
			$estadoDigitalizacion = ESTADO_EXPED_ELECTRONICO_SIN_DOCUMENTOS;

			// Se verifica si existe el directorio
			if (is_dir($directorioRemoto.'electronico'))
			{
				// Se escanea el directorio
			    $carpeta = scandir($directorioRemoto.'electronico');
			    // Si posee documentos
			    if ( isset($carpeta) && count($carpeta) > 2)
			        $estadoDigitalizacion = ESTADO_EXPED_ELECTRONICO_CON_DOCUMENTOS;
			}
		}

		// Actualizo la instancia del expediente.
		$pExpediente->estado_digitalizacion = $estadoDigitalizacion;
		$pExpediente->url_digitalizacion = $urlDigitalizacion;

		return $pExpediente;
	}

	/**
	 * Obtiene un array asociativo con los archivos Reservados correspondientes a un determinado expediente.
	 * @param  Expediente $pExpediente Expediente del cual se desean obtener los documentos reservados.
	 * @return array                   Array asociativo con los documentos reservados del expediente. Cada elemento del array posee "tipo", "archivo", "ruta_completa", "url" y "expediente"
	 */
	public function obtenerArchivosReservados(Expediente $pExpediente) {
		if (is_null($pExpediente)) {
			throw new Exception(sprintf("Error en %s.obtenerArchivosReservados: la instancia a verificar no puede ser nula.", get_class($this)));
		}

		$archivos = array();

		// Nombre codificado del directorio del expediente respectivo
		$nombreCodificado = sprintf("%02d%s%05d", $pExpediente->anio % 100, $pExpediente->tipo, $pExpediente->numero);

		// Ruta del directorio remoto de Reservados, determinado por la clave del expediente respectivo
		$directorioRemoto = PATH_KRAKEN_RESOURCES_PROYECTOS . $pExpediente->anio . "/" . $nombreCodificado . "/reservados/";

		// Verifico la existencia del directorio para sus Reservados
		if (is_dir($directorioRemoto)) {
			// Se 'escanea' el directorio (se obtiene un array con los archivos que contiene)
			$dirContent = scandir($directorioRemoto);

			foreach ($dirContent as $key => $value) {
				// Si contiene por lo menos un archivo
				if (!(($value == '.') or ($value == '..'))) {
					// Si no es un auxiliar (utilizado en la sobreescritura)
					if (strpos($value, 'temp_') === false) {
						$archivos[] = array(
							'tipo' => 'reservado',
							'archivo' => $value,
							'ruta_completa' => $directorioRemoto . $value,
							'fecha' => date("d/m/Y", filemtime($directorioRemoto . $value)),
							'url' => URL_KRAKEN_RESOURCES_PROYECTOS . $pExpediente->anio . "/" . $nombreCodificado . "/reservados/" . $value,
							'expediente' => $pExpediente);
					}
				}
			}
		}

		if (count($archivos) > 0) {
			// Se ordena el listado de archivos, por fecha de forma descendente
			foreach ($archivos as $key => $row) {
				$aux[$key] = substr($row['fecha'], 6, 4) . substr($row['fecha'], 3, 2) . substr($row['fecha'], 0, 2);
			}
			array_multisort($aux, SORT_DESC, $archivos);
		}

		return $archivos;
	}

	/**
	 * Guarda una instancia de tipo Expediente. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:27
	 * @param  Expediente $pExpediente 	Instancia a guardar.
	 * @param  boolean $pRecargar 		Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return Expediente               Instancia guardada.
	 */
	public function guardarExpediente(Expediente $pExpediente, $pRecargar = true) {
		if (is_null($pExpediente)) {
			throw new Exception(sprintf("Error en %s.guardarExpediente: la instancia a guardar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$audit_operacion = (is_null($this->obtenerExpediente($pExpediente->anio, $pExpediente->tipo, $pExpediente->numero, $pExpediente->cuerpo, $pExpediente->alcance))) ? Auditoria::OP_ALTA : Auditoria::OP_MODIFICA;

			$id = DB::getInstanceDBExpedientes()->guardarExpediente(
				$pExpediente->anio,
				$pExpediente->tipo,
				$pExpediente->numero,
				$pExpediente->cuerpo,
				$pExpediente->alcance,
				$pExpediente->iniciador_tipo,
				$pExpediente->iniciador_codigo,
				$pExpediente->iniciador_bloque_tipo,
				$pExpediente->iniciador_bloque_codigo,
				$pExpediente->agregado_anio,
				$pExpediente->agregado_tipo,
				$pExpediente->agregado_numero,
				$pExpediente->agregado_cuerpo,
				$pExpediente->agregado_alcance,
				$pExpediente->id_codcategoria,
				$pExpediente->fecha_entrada_expe,
				$pExpediente->caratula,
				$pExpediente->observaciones_expe,
				$pExpediente->marca_comision,
				$pExpediente->digi_completa,
				$pExpediente->id_usuario);

			// Auditoria
			NG::auditorias()->auditarComoExpediente(
				$pExpediente, null, $audit_operacion, 'hcd.expe_expedientes',
				$pExpediente->fecha_entrada_expe, null,
				sprintf('Se ha guardado el expediente %d-%s-%d-%d-%d', $pExpediente->anio, $pExpediente->tipo, $pExpediente->numero, $pExpediente->cuerpo, $pExpediente->alcance)
			);

			// Por las dudas, actualizo las claves de las coleciones hijas para que apunten al mismo expediente.
			// Esto se hace porque al crear un nuevo expediente, las colecciones asociadas pueden NO TENER seteadas
			// las claves primarias (referencias al expediente padre).
			$pExpediente->reasignarIdEnColecciones();

			// Actualizo los antecedentes
			foreach ($pExpediente->getAntecedentes() as $item) {
				if (($item->getInstanceState() == IS_MODIFIED) || ($item->getInstanceState() == IS_ADDED)) {
					$this->guardarAntecedente($item);
				}
			}

			foreach ($pExpediente->getAntecedentes()->getDeletedItems() as $item) {
				NG::expedientes()->eliminarAntecedente($item);
			}

			//Logger::get()->Log("expe_autores", $pExpediente->getAutores());
			// Actualizo los autores
			foreach ($pExpediente->getAutores() as $item) {
				if (($item->getInstanceState() == IS_MODIFIED) || ($item->getInstanceState() == IS_ADDED)) {
					$this->guardarAutor($item);
				}
			}

			foreach ($pExpediente->getAutores()->getDeletedItems() as $item) {
				NG::expedientes()->eliminarAutor($item);
			}

			// Actualizo los estados
			foreach ($pExpediente->getEstados() as $item) {
				if (($item->getInstanceState() == IS_MODIFIED) || ($item->getInstanceState() == IS_ADDED)) {
					$this->guardarEstado($item);
				}
			}

			foreach ($pExpediente->getEstados()->getDeletedItems() as $item) {
				NG::expedientes()->eliminarEstado($item);
			}

			// Actualizo los giros
			foreach ($pExpediente->getGiros() as $item) {
				if (($item->getInstanceState() == IS_MODIFIED) || ($item->getInstanceState() == IS_ADDED)) {
					$this->guardarGiro($item);
				}
			}

			foreach ($pExpediente->getGiros()->getDeletedItems() as $item) {
				NG::expedientes()->eliminarGiro($item);
			}

			// Actualizo los proyectos
			foreach ($pExpediente->getProyectos() as $item) {
				if (($item->getInstanceState() == IS_MODIFIED) || ($item->getInstanceState() == IS_ADDED)) {
					$this->guardarProyecto($item);
				}
			}

			foreach ($pExpediente->getProyectos()->getDeletedItems() as $item) {
				NG::expedientes()->eliminarProyecto($item);
			}

			// Actualizo los sanciones
			foreach ($pExpediente->getSanciones() as $item) {
				if (($item->getInstanceState() == IS_MODIFIED) || ($item->getInstanceState() == IS_ADDED)) {
					$this->guardarSancion($item);
				}
			}

			foreach ($pExpediente->getSanciones()->getDeletedItems() as $item) {
				NG::expedientes()->eliminarSancion($item);
			}

			// Actualizo los temas
			foreach ($pExpediente->getTemas() as $item) {
				if (($item->getInstanceState() == IS_MODIFIED) || ($item->getInstanceState() == IS_ADDED)) {
					$this->guardarTema($item);
				}
			}

			foreach ($pExpediente->getTemas()->getDeletedItems() as $item) {
				NG::expedientes()->eliminarTema($item);
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarExpediente: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			// Por defecto, recargo la instancia completa
			$resultado = $this->obtenerExpediente($pExpediente->anio, $pExpediente->tipo, $pExpediente->numero,
				$pExpediente->cuerpo, $pExpediente->alcance, true);
		} else {
			$resultado = $pExpediente;
		}

		DB::getInstanceDBExpedientes()->desconectar();

		if (is_null($resultado)) {
			throw new Exception(sprintf("Error grave en %s.guardarExpediente: no se encuentra el contenido actualizado.", get_class($this)));
		}

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina un conjunto de Expedientes en base a diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-02 13:55:07
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string iniciador_tipo
	 * @param  string iniciador_codigo
	 * @param  string iniciador_bloque_tipo
	 * @param  string iniciador_bloque_codigo
	 * @param  integer agregado_anio
	 * @param  string agregado_tipo
	 * @param  float agregado_numero
	 * @param  integer agregado_cuerpo
	 * @param  integer agregado_alcance
	 * @param  integer id_codcategoria
	 * @param  string fecha_entrada_expe
	 * @param  string caratula
	 * @param  string observaciones_expe
	 * @param  integer marca_comision
	 * @param  integer id_usuario
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarExpedientes(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$piniciador_tipo = null,
		$piniciador_codigo = null,
		$piniciador_bloque_tipo = null,
		$piniciador_bloque_codigo = null,
		$pagregado_anio = null,
		$pagregado_tipo = null,
		$pagregado_numero = null,
		$pagregado_cuerpo = null,
		$pagregado_alcance = null,
		$pid_codcategoria = null,
		$pfecha_entrada_expe = null,
		$pcaratula = null,
		$pobservaciones_expe = null,
		$pmarca_comision = null,
		$pid_usuario = null) {
		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Antes se obtiene el expediente, para auditar su eliminación
			$dummy = $this->obtenerExpediente($panio, $ptipo, $pnumero, $pcuerpo, $palcance);

			// Se elimina el expediente
			$resultado = DB::getInstanceDBExpedientes()->eliminarExpedientes($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $piniciador_tipo, $piniciador_codigo, $piniciador_bloque_tipo, $piniciador_bloque_codigo, $pagregado_anio, $pagregado_tipo, $pagregado_numero, $pagregado_cuerpo, $pagregado_alcance, $pid_codcategoria, $pfecha_entrada_expe, $pcaratula, $pobservaciones_expe, $pmarca_comision, $pid_usuario);

			// Si es que se elimino algo, se audita
			if ($resultado > 0) {
				//$au_observ = NG::auditorias()->generarMensajeEliminacion(
					// $resultado,
					// array($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $piniciador_tipo, $piniciador_codigo, $piniciador_bloque_tipo, $piniciador_bloque_codigo, $pagregado_anio, $pagregado_tipo, $pagregado_numero, $pagregado_cuerpo, $pagregado_alcance, $pid_codcategoria, $pfecha_entrada_expe, $pcaratula, $pobservaciones_expe, $pmarca_comision, $pid_usuario));

				$au_observ  = 'Se ha eliminado el '.$dummy->anio.'-'.$dummy->tipo.'-'.$dummy->numero.'-'.$dummy->cuerpo.'-'.$dummy->alcance;

				if ($dummy->iniciador_tipo != '' && $dummy->iniciador_codigo != '')
					$au_observ .= ' | Iniciador: '.$dummy->iniciador_tipo.'-'.$dummy->iniciador_codigo;

				if ($dummy->iniciador_bloque_tipo != '' && $dummy->iniciador_bloque_codigo != '')
					$au_observ .= ' Bloque: '.$dummy->iniciador_bloque_tipo.'-'.$dummy->iniciador_bloque_codigo;

				if ($dummy->agregado_anio != '')
					$au_observ .= ' | Agregado a: '.$dummy->agregado_anio.'-'.$dummy->agregado_tipo.'-'.$dummy->agregado_numero.'-'.$dummy->agregado_cuerpo.'-'.$dummy->agregado_alcance;

				if ($dummy->id_codcategoria && $dummy->ro_descripcion_categoria != '')
					$au_observ .= ' | Categoria: '.$dummy->id_codcategoria."-".trim($dummy->ro_descripcion_categoria);

				$au_observ .= ' | Fecha entrada: '.$dummy->fecha_entrada_expe;
				$au_observ .= ' | Caratula: '.$dummy->caratula;

				if ($dummy->observaciones_expe != '')
					$au_observ .= ' | Observaciones: '.$dummy->observaciones_expe;

				if ($dummy->marca_comision != '')
					$au_observ .= ' | Marca: '.$dummy->marca_comision;

				$au_observ .=  ' | Por el Usuario '.$dummy->id_usuario."-".trim($dummy->ro_codigo_usuario)."-".$dummy->ro_nombre_usuario;

				NG::auditorias()->auditarComoExpediente(
					$dummy, null, Auditoria::OP_BAJA, 'hcd.expe_expedientes', null, null, $au_observ);
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarExpedientes: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina una instancia de tipo Expediente en base a su identificador.
	 * GenerateClass 0.97.5 beta @ 2016-09-02 13:55:07
	 * @param  Expediente $pExpediente Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarExpediente(Expediente $pExpediente) {

		if (is_null($pExpediente)) {
			throw new Exception(sprintf("Error en %s.eliminarExpediente: la instancia a eliminar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		$resultado = 0;

		try {
			// Se eliminan sus Antecedentes
			$this->eliminarAntecedentes($pExpediente->anio, $pExpediente->tipo, $pExpediente->numero, $pExpediente->cuerpo, $pExpediente->alcance);

			// Se eliminan sus Estados
			$this->eliminarEstados($pExpediente->anio, $pExpediente->tipo, $pExpediente->numero, $pExpediente->cuerpo, $pExpediente->alcance);

			// Se eliminan sus Sanciones
			$this->eliminarSanciones($pExpediente->anio, $pExpediente->tipo, $pExpediente->numero, $pExpediente->cuerpo, $pExpediente->alcance);

			// Se eliminan sus Informes
			$this->eliminarInformes($pExpediente->anio, $pExpediente->tipo, $pExpediente->numero, $pExpediente->cuerpo, $pExpediente->alcance);

			// Se eliminan sus Giros
			$this->eliminarGiros($pExpediente->anio, $pExpediente->tipo, $pExpediente->numero, $pExpediente->cuerpo, $pExpediente->alcance);

			// Se eliminan sus Proyectos
			$this->eliminarProyectos($pExpediente->anio, $pExpediente->tipo, $pExpediente->numero, $pExpediente->cuerpo, $pExpediente->alcance);

			// Se eliminan sus Temas
			$this->eliminarTemas($pExpediente->anio, $pExpediente->tipo, $pExpediente->numero, $pExpediente->cuerpo, $pExpediente->alcance);

			// Se eliminan sus Autores
			$this->eliminarAutores($pExpediente->anio, $pExpediente->tipo, $pExpediente->numero, $pExpediente->cuerpo, $pExpediente->alcance);

			// Se eliminan sus Participaciones
			$this->eliminarParticipaciones($pExpediente->anio, $pExpediente->tipo, $pExpediente->numero, $pExpediente->cuerpo, $pExpediente->alcance);

			// 2026-03-19 XXXX
			// Se eliminan las referencias a los:
			// Giros Pendientes
			// Documentos electrónicos (Exped. Electrónico)
			// Revisiones
			// Firmas (pendientes o no)
			// Asociados al Expediente o Nota
			// ---------------------------------------------------------------

			NGGirosPendientes::eliminarGirosPendientes($pExpediente->anio, $pExpediente->tipo, $pExpediente->numero, $pExpediente->cuerpo, $pExpediente->alcance);

			$this->eliminarFirmasExpedienteElecPend($pExpediente->anio, $pExpediente->tipo, $pExpediente->numero, $pExpediente->cuerpo, $pExpediente->alcance);

			$this->eliminarFirmasExpedienteElec($pExpediente->anio, $pExpediente->tipo, $pExpediente->numero, $pExpediente->cuerpo, $pExpediente->alcance);

			$this->eliminarRevExpedienteElecPend($pExpediente->anio, $pExpediente->tipo, $pExpediente->numero, $pExpediente->cuerpo, $pExpediente->alcance);

			$this->eliminarExpedientesElecPend($pExpediente->anio, $pExpediente->tipo, $pExpediente->numero, $pExpediente->cuerpo, $pExpediente->alcance);

			$this->eliminarExpedientesElec($pExpediente->anio, $pExpediente->tipo, $pExpediente->numero, $pExpediente->cuerpo, $pExpediente->alcance);

			// ---------------------------------------------------------------

			// Se elimina el Expediente respectivo
			$resultado = $this->eliminarExpedientes($pExpediente->anio, $pExpediente->tipo, $pExpediente->numero, $pExpediente->cuerpo, $pExpediente->alcance);

			if ($resultado > 1) {
				throw new Exception(sprintf("Error en %s.eliminarExpediente: se quiso eliminar m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarExpediente: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return ($resultado == 1);
	}

	// ************************************************************************
	// Giros
	// ************************************************************************

	/**
	 * NGExpedientes: Obtiene una coleccion de elementos tipo Giro en base a diferentes criterios de selección.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_giro
	 * @param  string comision_tipo
	 * @param  string comision_codigo
	 * @param  string fecha_entrada_giro
	 * @param  string fecha_salida_giro
	 * @param  string dictamen_giro
	 * @param  string observaciones_giro
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<Giro>
	 */
	public function obtenerGiros(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_giro = null,
		$pcomision_tipo = null,
		$pcomision_codigo = null,
		$pfecha_entrada_giro = null,
		$pfecha_salida_giro = null,
		$pdictamen_giro = null,
		$pobservaciones_giro = null,
		$pid_usuario = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBExpedientes()->obtenerGiros($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_giro, $pcomision_tipo, $pcomision_codigo, $pfecha_entrada_giro, $pfecha_salida_giro, $pdictamen_giro, $pobservaciones_giro, $pid_usuario,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerGiros: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Giro
		$resultado = $this->arrayResultToInstance($filas, 'Giro');

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Determina la cantidad de elementos tipo Giro obtenidos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_giro
	 * @param  string comision_tipo
	 * @param  string comision_codigo
	 * @param  string fecha_entrada_giro
	 * @param  string fecha_salida_giro
	 * @param  string dictamen_giro
	 * @param  string observaciones_giro
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerGirosCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_giro = null,
		$pcomision_tipo = null,
		$pcomision_codigo = null,
		$pfecha_entrada_giro = null,
		$pfecha_salida_giro = null,
		$pdictamen_giro = null,
		$pobservaciones_giro = null,
		$pid_usuario = null) {
		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBExpedientes()->obtenerGirosCantidad($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_giro, $pcomision_tipo, $pcomision_codigo, $pfecha_entrada_giro, $pfecha_salida_giro, $pdictamen_giro, $pobservaciones_giro, $pid_usuario);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerGirosCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGExpedientes: a partir de un determinado clave de expediente, retorna el número de orden siguiente del último giro insertado.
	 * @param  [type] $panio    [description]
	 * @param  [type] $ptipo    [description]
	 * @param  [type] $pnumero  [description]
	 * @param  [type] $pcuerpo  [description]
	 * @param  [type] $palcance [description]
	 * @return int           [description]
	 */
	public function obtenerNumeroSiguienteGiro(
		// Parametros
		$panio,
		$ptipo,
		$pnumero,
		$pcuerpo,
		$palcance) {

		DB::getInstanceDBExpedientes()->conectar();

		try {
			// En caso de que no existan giros para un determinado expediente, devuelve cero
			$resultado = DB::getInstanceDBExpedientes()->obtenerNumeroUltimoGiro($panio, $ptipo, $pnumero, $pcuerpo, $palcance);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerNumeroSiguienteGiro: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado + 1;// se incrementa en uno para que sea el siguiente (en caso que sea el primero será orden 1)
	}

	/**
	 * NGExpedientes: Obtiene una instancia de tipo Giro en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_giro
	 * @return Giro Instancia de Giro buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerGiro(
		// Parametros
		$panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_giro) {
		if (is_null($panio) || is_null($ptipo) || is_null($pnumero) || is_null($pcuerpo) || is_null($palcance) || is_null($porden_giro)) {
			throw new Exception(sprintf("Error en %s.obtenerGiro: los campos clave no pueden ser nulos.", get_class($this)));
		}

		$resultado = $this->obtenerGiros($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_giro);

		if (count($resultado) == 0) {
			return null;
		} else if (count($resultado) == 1) {
			return $resultado[0];
		} else {
			throw new Exception(sprintf("Error en %s.obtenerGiro: se encontr&oacute; m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
		}

	}

	/**
	 * 22/08/2022 XXXX
	 * Se obtiene el Giro anterior a uno determinado por su clave
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_giro 	Al cual se busca su anterior
	 * @return Giro Instancia de Giro buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerGiroAnterior($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_giro)
	{
		$encontrado = false;
		do {
			$porden_giro--; // Se decrementa

			// Se obtiene el Giro con el orden decrementado
			$giro = $this->obtenerGiro($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_giro);

			if (! is_null($giro)) // si existe
			{
				$encontrado = true;
				return $giro; // lo retorna
			}
		} while ($encontrado === false && $porden_giro > 0);

		return null;// Si no lo ha encontrado, no existe
	}

	/**
	 * 22/08/2022 XXXX
	 * Se obtiene el Giro siguiente a uno determinado por su clave
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_giro 	Al cual se busca su siguiente
	 * @return Giro Instancia de Giro buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerGiroSiguiente($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_giro)
	{
		// Se obtiene el último Orden, para utilizarlo para detener la búsqueda
		$ultimo_giro = $this->obtenerUltimoOrdenGiro($panio, $ptipo, $pnumero, $pcuerpo, $palcance);

		$encontrado = false;
		do {
			$porden_giro++; // Se incrementa

			// Se obtiene el Giro con el orden incrementado
			$giro = $this->obtenerGiro($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_giro);

			if (! is_null($giro)) // si existe
			{
				$encontrado = true;
				return $giro; // lo retorna
			}
		} while ($encontrado === false && $porden_giro <= $ultimo_giro);

		return null;// Si no lo ha encontrado, no existe
	}

	/**
	 * 22/08/2022 XXXX
	 * Se obtiene el Ultimo Giro de un expediente determinado, NO se evalua si posee fecha de inicio y salida
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_giro
	 * @return Giro Instancia de Giro buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerUltimoOrdenGiro(
		// Parametros
		$panio,
		$ptipo,
		$pnumero,
		$pcuerpo,
		$palcance) {

		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBExpedientes()->obtenerUltimoOrdenGiro(
				$panio, $ptipo, $pnumero, $pcuerpo, $palcance);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerUltimoOrdenGiro: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * Guarda una instancia de tipo Giro. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  Giro $pGiro 	Instancia a guardar.
	 * @param  boolean $pRecargar Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return Giro               Instancia guardada.
	 */
	public function guardarGiro(Giro $pGiro, $pRecargar = true, $para_ppc = 0, $fecha_para_estado_automatico = null)
	{
		if (is_null($pGiro)) {
			throw new Exception(sprintf("Error en %s.guardarGiro: la instancia a guardar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// 01/02/2022 XXXX
			// Para auditar el alta/modificación del Giro como en la versión anterior del sistema

			$audit_fecha_entrada_giro = ($pGiro->fecha_entrada_giro != 'null') ? ' - Fecha Entrada: '.$pGiro->fecha_entrada_giro : '';

			$audit_dictamen_giro = ($pGiro->dictamen_giro != 'null') ? ' - Dictamen: '.$pGiro->dictamen_giro : '';

			// Se obtiene el Giro (ante una modificación)
			$giro_actual = $this->obtenerGiro($pGiro->anio, $pGiro->tipo, $pGiro->numero, $pGiro->cuerpo, $pGiro->alcance, $pGiro->orden_giro);

			// Si no existe
			if (is_null($giro_actual)) {
				// Se define la operación a auditar, ante el Alta del Giro
				$audit_operacion = Auditoria::OP_ALTA;

				$audit_observaciones = "Comisi&oacute;n: ".$pGiro->comision_tipo." ".$pGiro->comision_codigo.$audit_fecha_entrada_giro.$audit_dictamen_giro;
			} else {
				// Se define la operación a auditar, ante la Modificación del Giro
				$audit_operacion = Auditoria::OP_MODIFICA;

				$audit_observaciones = "Comisi&oacute;n: ".$giro_actual->comision_tipo." ".$giro_actual->comision_codigo.$audit_fecha_entrada_giro.$audit_dictamen_giro;
			}

			$id = DB::getInstanceDBExpedientes()->guardarGiro(
				$pGiro->anio,
				$pGiro->tipo,
				$pGiro->numero,
				$pGiro->cuerpo,
				$pGiro->alcance,
				$pGiro->orden_giro,
				$pGiro->comision_tipo,
				$pGiro->comision_codigo,
				$pGiro->fecha_entrada_giro,
				$pGiro->fecha_salida_giro,
				$pGiro->dictamen_giro,
				$pGiro->observaciones_giro,
				$pGiro->id_usuario);

			// Auditoria
			NG::auditorias()->auditarComoExpediente($pGiro, null, $audit_operacion, 'hcd.expe_giros', null, null, $audit_observaciones);

			// --- Se guarda automáticamente el Estado 3 "Girado a Comisión" ----------------------------------
			// Si es el 1er giro del expediente
			if ($pGiro->orden_giro == '1') {

				// Se obtiene el siguiente número de orden del estado
				// No se conoce la fecha del estado,
				// sólo con la clave del expediente basta para obtener el siguiente número de orden del estado
				$nro_siguiente_orden_estado = $this->obtenerNumeroSiguienteEstado(
					$pGiro->anio,
					$pGiro->tipo,
					$pGiro->numero,
					$pGiro->cuerpo,
					$pGiro->alcance,
					null);

				// 28/07/2021 XXXX
				// Se define la Observación del Estado 3
				// sólo si es una Nota y se considera para PPC
				$msg_observaciones_estado = ($pGiro->tipo == 'N' && $para_ppc == 1) ? 'AUTOMATICO - Considerar PPC' : 'AUTOMATICO';

				// Ahora puede definirse la fecha al Estado 3, pero no a la 1er comisión
				// antes se utilizaba la fecha del Giro creado
				$fecha_estado = ($pGiro->fecha_entrada_giro == null)
					? $fecha_para_estado_automatico
					: $pGiro->fecha_entrada_giro;

				// Se crea una instancia de Estado, utilizando la información del Giro
				$estado = new Estado(
					$pGiro->anio,
					$pGiro->tipo,
					$pGiro->numero,
					$pGiro->cuerpo,
					$pGiro->alcance,
					$fecha_estado, // fecha_estado
					$nro_siguiente_orden_estado, // orden_estado
					3, // id_codestado = Girado a Comisión
					$msg_observaciones_estado, // observaciones_estado
					$pGiro->id_usuario
				);

				// Se ingresa automáticamente el Estado 3 "Girado a Comisión", para el expediente respectivo
				// (se audita internamente el ingreso de dicho Estado)
				$estado_ingresado = $this->guardarEstado($estado, true);
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarGiro: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$resultado = $this->obtenerGiro($pGiro->anio, $pGiro->tipo, $pGiro->numero, $pGiro->cuerpo, $pGiro->alcance, $pGiro->orden_giro);
		} else {
			$resultado = $pGiro;
		}

		DB::getInstanceDBExpedientes()->desconectar();

		if (is_null($resultado)) {
			throw new Exception(sprintf("Error grave en %s.guardarGiro: no se encuentra el contenido actualizado.", get_class($this)));
		}

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina un conjunto de Giros en base a diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-02 13:55:07
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_giro
	 * @param  string comision_tipo
	 * @param  string comision_codigo
	 * @param  string fecha_entrada_giro
	 * @param  string fecha_salida_giro
	 * @param  string dictamen_giro
	 * @param  string observaciones_giro
	 * @param  integer id_usuario
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarGiros(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_giro = null,
		$pcomision_tipo = null,
		$pcomision_codigo = null,
		$pfecha_entrada_giro = null,
		$pfecha_salida_giro = null,
		$pdictamen_giro = null,
		$pobservaciones_giro = null,
		$pid_usuario = null) {
		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// 01/02/2022 XXXX, se modifica la Observación al auditar la Baja del Giro
			$audit_orden_log = null;
			$audit_observaciones = null;
			if (is_null($porden_giro)) {
				$audit_observaciones = "AUTOMATICO, al eliminar un Expediente.";
			} else {
				// Se obtiene la info del Giro a eliminar
				$giro = $this->obtenerGiro($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_giro);

				$audit_orden_log = $porden_giro;
				$audit_observaciones = "Comisi&oacute;n: ".$giro->comision_tipo."-".$giro->comision_codigo;
			}

			// Se elimina el Giro desde la capa de datos
			$resultado = DB::getInstanceDBExpedientes()->eliminarGiros($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_giro, $pcomision_tipo, $pcomision_codigo, $pfecha_entrada_giro, $pfecha_salida_giro, $pdictamen_giro, $pobservaciones_giro, $pid_usuario);

			// Auditoria
			if ($resultado > 0) {

				$dummy = new Giro($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_giro, $pcomision_tipo, $pcomision_codigo, $pfecha_entrada_giro, $pfecha_salida_giro, $pdictamen_giro, $pobservaciones_giro, $pid_usuario);

				NG::auditorias()->auditarComoExpediente(
					$dummy, null, Auditoria::OP_BAJA, 'hcd.expe_giros',
					null, $audit_orden_log, $audit_observaciones);
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarGiros: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina una instancia de tipo Giro en base a su identificador.
	 * GenerateClass 0.97.5 beta @ 2016-09-02 13:55:07
	 * @param  Giro $pGiro 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarGiro(Giro $pGiro) {
		if (is_null($pGiro)) {
			throw new Exception(sprintf("Error en %s.eliminarGiro: la instancia a eliminar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarGiros($pGiro->anio, $pGiro->tipo, $pGiro->numero, $pGiro->cuerpo, $pGiro->alcance, $pGiro->orden_giro);

			if ($resultado > 1) {
				throw new Exception(sprintf("Error en %s.eliminarGiro: se quiso eliminar m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarGiro: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return ($resultado == 1);
	}

	/**
	 * NGExpedientes: Se obtiene el último Giro de un Expediente determinado
	 * @param  integer    $panio            Año del Expediente
	 * @param  string     $ptipo            Tipo del Expediente
	 * @param  integer    $pnumero          Número del Expediente
	 * @param  integer    $pcuerpo          Cuerpo del Expediente
	 * @param  integer    $palcance         Alcance del Expediente
	 * @param  array|null $pOrdenColumnas 	Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @return Giro                    		Instancia del último Giro encontrado
	 */
	public function obtenerUltimoGiro(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		// Control de consulta
		array $pOrdenColumnas = null) {
		DB::getInstanceDBExpedientes()->conectar();

		if (is_null($panio) && is_null($ptipo) && is_null($pnumero) && is_null($pcuerpo) && is_null($palcance)) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerUltimoGiro: %s", get_class($this), "La clave del expediente es obligatoria."));
		}

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::expedientes()->obtenerUltimoGiro(
				$panio,
				$ptipo,
				$pnumero,
				$pcuerpo,
				$palcance,
				$pOrdenColumnas);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerUltimoGiro: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una colección de elementos de tipo Giro
		$resultado = $this->arrayResultToInstance($filas, 'Giro');

		DB::getInstanceDBExpedientes()->desconectar();

		if (count($resultado) == 0) // Si no se encontraron resultados
		{
			return null;
		} else if (count($resultado) == 1) // Si se encontró un Giro
		{
			return $resultado[0]; // Devuelve el Giro encontrado
		} else {
			throw new Exception(sprintf("Error en %s.obtenerUltimoGiro: se encontr&oacute; m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
		}

	}

	/**
	 * NGExpedientes: Se obtiene el último Informe de un Giro determinado
	 * @param  integer    $panio            Año del Expediente
	 * @param  string     $ptipo            Tipo del Expediente
	 * @param  integer    $pnumero          Número del Expediente
	 * @param  integer    $pcuerpo          Cuerpo del Expediente
	 * @param  integer    $palcance         Alcance del Expediente
	 * @param  integer    $porden_giro		Orden del Giro
	 * @return Informe                    	Instancia del último Informe encontrado
	 */
	public function obtenerUltimoInforme(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_giro = null) {
		DB::getInstanceDBExpedientes()->conectar();

		if (is_null($panio) && is_null($ptipo) && is_null($pnumero) && is_null($pcuerpo) && is_null($palcance) && is_null($porden_giro)) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerUltimoInforme: %s", get_class($this), "La clave del Giro es obligatoria."));
		}

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::expedientes()->obtenerUltimoInforme(
				$panio,
				$ptipo,
				$pnumero,
				$pcuerpo,
				$palcance,
				$porden_giro);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerUltimoInforme: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una colección de elementos de tipo Informe
		$resultado = $this->arrayResultToInstance($filas, 'Informe');

		DB::getInstanceDBExpedientes()->desconectar();

		if (count($resultado) == 0) // Si no se encontraron resultados
		{
			return null;
		} else if (count($resultado) == 1) // Si se encontró un Informe
		{
			return $resultado[0]; // Lo devuelve
		} else {
			throw new Exception(sprintf("Error en %s.obtenerUltimoInforme: se encontr&oacute; m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
		}

	}

	/**
	 * Se cargan los Giros a un Expediente determinado
	 * @param  [type] $pExpediente            Expediente al cual se le cargan los Giros
	 * @param  [type] $comisiones             Giros a cargar
	 * @param  [type] $observaciones_comision Observaciones de cada Giro
	 * @param  [type] $pUsuario               Usuario actual
	 * @param  [type] $pPpc 				  Si se considera para PPC o no (cuando es una Nota)
	 * @return [type]
	 */
	public function cargarGiros($pExpediente, $comisiones, $observaciones_comision, $pUsuario, $pPpc = null)
	{
		$errores = [];
		$cant_comisiones = (isset($comisiones)) ? count($comisiones) : 0;

		try {
			// Por cada giro a comisión
			for ($i=0; $i < $cant_comisiones; $i++)
			{
				// Se obtiene el siguiente orden de giro del Expediente/Nota respectivo/a
				// (en caso de ser el primero, internamente ya devuelve orden 1)
				$siguiente_orden_giro = $this->obtenerNumeroSiguienteGiro(
					$pExpediente->anio,
					$pExpediente->tipo,
					$pExpediente->numero,
					$pExpediente->cuerpo,
					$pExpediente->alcance
				);

				// Si es una Nota
				if ($pExpediente->tipo == 'N') {
					// Sólo si es el primer Giro se define su fecha de entrada
					$fecha_entrada_giro = ($siguiente_orden_giro == 1) ? date("Y-m-d") : null;
				}
				// Si es un Expediente
				else {
					$fecha_entrada_giro = null; // Por defecto NO se le asigna fecha aún

					// Si es el primer Giro
					if ($siguiente_orden_giro == 1) {
						// Se verifica previamente si se encuentra en PPC (Programa de Participación Ciudadana)
						// -----------------------------------------------------------------------------------
						// Obtenemos los estados del Expediente
						$estados = $this->obtenerEstados(
							$pExpediente->anio,
							$pExpediente->tipo,
							$pExpediente->numero,
							$pExpediente->cuerpo,
							$pExpediente->alcance);

						// Su cantidad
						$cant_estados = (isset($estados)) ? count($estados) : 0;

						// Si posee estados
						if ($cant_estados > 0) {
							// Nos quedamos con el último Estado (el actual)
							$ultimo_estado = $estados[count($estados) - 1];

							// Si el Estado actual es 90 (Expediente en PPC), no se le asigna fecha aún
							$fecha_entrada_giro = ($ultimo_estado->id_codestado == 90) ? null : date("Y-m-d");
						}
					}
				}

				// Se prepara una instancia de Giro con la info recibida
				$giro = new Giro(
					$pExpediente->anio,
					$pExpediente->tipo,
					$pExpediente->numero,
					$pExpediente->cuerpo,
					$pExpediente->alcance,
					$siguiente_orden_giro,
					'C', // comision_tipo
					$comisiones[$i], // comision_codigo
					$fecha_entrada_giro,
					null, // fecha_salida_giro
					null, // dictamen_giro
					$observaciones_comision[$i],
					$pUsuario->id_usuario
				);

				// Se define la marca, para modificar la Observación del Estado, del 1er Giro
				// Sólo si es una Nota y se considera para PPC
				$para_ppc = ( ($pExpediente->tipo == 'N') && (!is_null($pPpc)) && ($pPpc == 1) ) ? '1' : '0';

				// Sólo se utiliza esta fecha si es el primer giro del expediente
				// (antes se utilizaba la fecha del Giro creado)
				$fecha_para_estado_automatico = date("Y-m-d");

				// Se guarda el Giro
				// (se registra el estado 3 "Girado a comisión" dentro del método guardarGiro)
				$giro = $this->guardarGiro($giro, true, $para_ppc, $fecha_para_estado_automatico);
			}

		} catch (Exception $e) {
			$errores = [sprintf('Error al cargar el Giro: %s', $e->getMessage())];
		}

		return $errores;
	}

	// ************************************************************************
	// Proyectos
	// ************************************************************************

	/**
	 * NGExpedientes: Obtiene una coleccion de elementos tipo Proyecto en base a diferentes criterios de selección.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_proyecto
	 * @param  integer id_codproyecto
	 * @param  string extracto
	 * @param  string observaciones_proyecto
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<Proyecto>
	 */
	public function obtenerProyectos(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_proyecto = null,
		$pid_codproyecto = null,
		$pextracto = null,
		$pobservaciones_proyecto = null,
		$pid_usuario = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBExpedientes()->obtenerProyectos($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_proyecto, $pid_codproyecto, $pextracto, $pobservaciones_proyecto, $pid_usuario,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerProyectos: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Proyecto
		$resultado = $this->arrayResultToInstance($filas, 'Proyecto');

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Determina la cantidad de elementos tipo Proyecto obtenidos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_proyecto
	 * @param  integer id_codproyecto
	 * @param  string extracto
	 * @param  string observaciones_proyecto
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerProyectosCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_proyecto = null,
		$pid_codproyecto = null,
		$pextracto = null,
		$pobservaciones_proyecto = null,
		$pid_usuario = null) {
		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBExpedientes()->obtenerProyectosCantidad($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_proyecto, $pid_codproyecto, $pextracto, $pobservaciones_proyecto, $pid_usuario);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerProyectosCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * DBExpedientes: a partir de un determinado clave de expediente, retorna el número de orden siguiente del último proyecto insertado.
	 * En caso de que no existan proyectos para un determinado año y tipo, devuelve cero.
	 * @param  [type] $panio    [description]
	 * @param  [type] $ptipo    [description]
	 * @param  [type] $pnumero  [description]
	 * @param  [type] $pcuerpo  [description]
	 * @param  [type] $palcance [description]
	 * @return int           [description]
	 */
	public function obtenerNumeroSiguienteProyecto(
		// Parametros
		$panio,
		$ptipo,
		$pnumero,
		$pcuerpo,
		$palcance) {

		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBExpedientes()->obtenerNumeroUltimoProyecto($panio, $ptipo, $pnumero, $pcuerpo, $palcance);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerNumeroSiguienteProyecto: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado + 1;
	}

	/**
	 * NGExpedientes: Obtiene una instancia de tipo Proyecto en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_proyecto
	 * @return Proyecto Instancia de Proyecto buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerProyecto(
		// Parametros
		$panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_proyecto) {
		if (is_null($panio) || is_null($ptipo) || is_null($pnumero) || is_null($pcuerpo) || is_null($palcance) || is_null($porden_proyecto)) {
			throw new Exception(sprintf("Error en %s.obtenerProyecto: los campos clave no pueden ser nulos.", get_class($this)));
		}

		$resultado = $this->obtenerProyectos($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_proyecto);

		if (count($resultado) == 0) {
			return null;
		} else if (count($resultado) == 1) {
			return $resultado[0];
		} else {
			throw new Exception(sprintf("Error en %s.obtenerProyecto: se encontr&oacute; m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
		}

	}

	/**
	 * Guarda una instancia de tipo Proyecto. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  Proyecto $pProyecto 	Instancia a guardar.
	 * @param  boolean $pRecargar 		Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return Proyecto               Instancia guardada.
	 */
	public function guardarProyecto(Proyecto $pProyecto, $pRecargar = true) {
		if (is_null($pProyecto)) {
			throw new Exception(sprintf("Error en %s.guardarProyecto: la instancia a guardar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// 03/02/2022 XXXX, se obtiene la Descripción del Proyecto y se modifica la observación en la Auditoría

			// Se obtiene el Proyecto con su info actual (ante una modificación)
			$proyecto_actual = $this->obtenerProyecto($pProyecto->anio, $pProyecto->tipo, $pProyecto->numero, $pProyecto->cuerpo, $pProyecto->alcance, $pProyecto->orden_proyecto);

			// Se obtiene la Codificadora del Proyecto, en base a su Identificador, para utilizar su Descripción
			$codificadora_proyecto = NG::expedientesParam()->obtenerCodproyecto($pProyecto->id_codproyecto);

			// Si no existe
			if (is_null($proyecto_actual)) {
				// Se define la operación a auditar, ante el Alta del Proyecto
				$audit_operacion = Auditoria::OP_ALTA;

				$audit_observaciones = "Proyecto ".$pProyecto->orden_proyecto.": ".$codificadora_proyecto->descripcion_proyecto;
				$audit_observaciones .= "<br>Extracto: ".$pProyecto->extracto;
			} else {

				// Se define la operación a auditar, ante la Modificación del Proyecto
				$audit_operacion = Auditoria::OP_MODIFICA;

				$audit_observaciones = "Proyecto ".$pProyecto->orden_proyecto.": ".$codificadora_proyecto->descripcion_proyecto;
				$audit_observaciones .= "<br>Extracto anterior: ".$proyecto_actual->extracto;
				$audit_observaciones .= "<br>Extracto actual: ".$pProyecto->extracto;
			}

			//$audit_operacion = (is_null($this->obtenerProyecto($pProyecto->anio, $pProyecto->tipo, $pProyecto->numero, $pProyecto->cuerpo, $pProyecto->alcance, $pProyecto->orden_proyecto))) ? Auditoria::OP_ALTA : Auditoria::OP_MODIFICA;

			$id = DB::getInstanceDBExpedientes()->guardarProyecto(
				$pProyecto->anio,
				$pProyecto->tipo,
				$pProyecto->numero,
				$pProyecto->cuerpo,
				$pProyecto->alcance,
				$pProyecto->orden_proyecto,
				$pProyecto->id_codproyecto,
				$pProyecto->extracto,
				$pProyecto->observaciones_proyecto,
				$pProyecto->id_usuario);

			// Auditoria
			NG::auditorias()->auditarComoExpediente(
				$pProyecto, null, $audit_operacion, 'hcd.expe_proyectos', null, $pProyecto->orden_proyecto, $audit_observaciones);

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarProyecto: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$resultado = $this->obtenerProyecto($pProyecto->anio, $pProyecto->tipo, $pProyecto->numero, $pProyecto->cuerpo, $pProyecto->alcance, $pProyecto->orden_proyecto);
		} else {
			$resultado = $pProyecto;
		}

		DB::getInstanceDBExpedientes()->desconectar();

		if (is_null($resultado)) {
			throw new Exception(sprintf("Error grave en %s.guardarProyecto: no se encuentra el contenido actualizado.", get_class($this)));
		}

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina un conjunto de Proyectos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-02 13:55:07
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_proyecto
	 * @param  integer id_codproyecto
	 * @param  string extracto
	 * @param  string observaciones_proyecto
	 * @param  integer id_usuario
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarProyectos(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_proyecto = null,
		$pid_codproyecto = null,
		$pextracto = null,
		$pobservaciones_proyecto = null,
		$pid_usuario = null) {
		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// 07/02/2022 XXXX, se modifica la Observación al auditar la Baja del Proyecto
			$audit_orden_log = null;
			$audit_observaciones = null;
			if (is_null($porden_proyecto)) {
				$audit_observaciones = "AUTOMATICO, al eliminar un Expediente.";
			} else {
				// Se obtiene la info del Proyecto a eliminar
				$proyecto = $this->obtenerProyecto($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_proyecto);

				// Se obtiene la Codificadora del Proyecto, en base a su Identificador
				// para utilizar su Descripción
				$codificadora_proyecto = NG::expedientesParam()->obtenerCodproyecto($proyecto->id_codproyecto);

				$audit_orden_log = $proyecto->orden_proyecto;

				$audit_observaciones = "Proyecto ".$porden_proyecto.": ".$codificadora_proyecto->descripcion_proyecto;
				$audit_observaciones .= "<br>Extracto: ".$proyecto->extracto;
			}

			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBExpedientes()->eliminarProyectos($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_proyecto, $pid_codproyecto, $pextracto, $pobservaciones_proyecto, $pid_usuario);

			// Auditoria (si es que se elimino algo, tengo que auditar)
			if ($resultado > 0) {
				$dummy = new Proyecto($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_proyecto, $pid_codproyecto, $pextracto, $pobservaciones_proyecto, $pid_usuario);

				NG::auditorias()->auditarComoExpediente(
					$dummy, null, Auditoria::OP_BAJA, 'hcd.expe_proyectos',
					null, $audit_orden_log, $audit_observaciones);

				//NG::auditorias()->generarMensajeEliminacion($resultado, array($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_proyecto, $pid_codproyecto, $pextracto, $pobservaciones_proyecto, $pid_usuario))
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarProyectos: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina una instancia de tipo Proyecto en base a su identificador.
	 * GenerateClass 0.97.5 beta @ 2016-09-02 13:55:07
	 * @param  Proyecto $pProyecto 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarProyecto(Proyecto $pProyecto) {
		if (is_null($pProyecto)) {
			throw new Exception(sprintf("Error en %s.eliminarProyecto: la instancia a eliminar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarProyectos($pProyecto->anio, $pProyecto->tipo, $pProyecto->numero, $pProyecto->cuerpo, $pProyecto->alcance, $pProyecto->orden_proyecto);

			if ($resultado > 1) {
				throw new Exception(sprintf("Error en %s.eliminarProyecto: se quiso eliminar m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarProyecto: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		// Aunque elimine el registro, $resultado SIEMPRE es 0, VER CON GABY !!!!!!!!!!!!!!!!!!!!!!!!!!

		return ($resultado === 1);
	}

	// 19/02/2021 XXXX
	// ************************************************************************
	// Participaciones
	// ************************************************************************

	/**
	 * NGExpedientes:  Se verifica si el expediente se encuentra habilitado para su participación ciudadana
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @return boolean
	 */
	public function estaHabilitadoParticipacion($panio, $ptipo, $pnumero, $pcuerpo, $palcance) {

		if (is_null($panio) || is_null($ptipo) || is_null($pnumero) || is_null($pcuerpo) || is_null($palcance)) {
			throw new Exception(sprintf("Error en %s.estaHabilitadoParticipacion: los campos clave no pueden ser nulos.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBExpedientes()->estaHabilitadoParticipacion(
				$panio, $ptipo, $pnumero, $pcuerpo, $palcance);

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.estaHabilitadoParticipacion: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return (isset($resultado[0]['anio']) && $resultado[0]['anio'] != '');
	}

	/**
	 * NGExpedientes: Obtiene una coleccion de elementos tipo Participacion en base a diferentes criterios de selección.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) numero_participacion
	 * @param  string texto
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<Participacion>
	 */
	public function obtenerParticipaciones(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pnumero_participacion = null,
		$texto = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {

		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBExpedientes()->obtenerParticipaciones($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pnumero_participacion, $texto,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerParticipaciones: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Participacion
		$resultado = $this->arrayResultToInstance($filas, 'Participacion');

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Determina la cantidad de elementos tipo Participacion obtenidos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) numero_participacion
	 * @param  string texto
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerParticipacionesCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pnumero_participacion = null,
		$ptexto = null) {
		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBExpedientes()->obtenerParticipacionesCantidad($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pnumero_participacion, $ptexto);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerParticipacionesCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * DBExpedientes: a partir de un determinado clave de expediente, retorna el número de orden siguiente de la última participacion insertada.
	 * En caso de que no existan participaciones para un determinado año y tipo, devuelve cero.
	 * @param  [type] $panio    [description]
	 * @param  [type] $ptipo    [description]
	 * @param  [type] $pnumero  [description]
	 * @param  [type] $pcuerpo  [description]
	 * @param  [type] $palcance [description]
	 * @return int           [description]
	 */
	public function obtenerNumeroSiguienteParticipacion(
		// Parametros
		$panio,
		$ptipo,
		$pnumero,
		$pcuerpo,
		$palcance) {

		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBExpedientes()->obtenerNumeroUltimoParticipacion($panio, $ptipo, $pnumero, $pcuerpo, $palcance);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerNumeroSiguienteParticipacion: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado + 1;
	}

	/**
	 * NGExpedientes: Obtiene una instancia de tipo Participacion en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) numero_participacion
	 * @return Participacion Instancia de Participacion buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerParticipacion(
		// Parametros
		$panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pnumero_participacion) {
		if (is_null($panio) || is_null($ptipo) || is_null($pnumero) || is_null($pcuerpo) || is_null($palcance) || is_null($pnumero_participacion)) {
			throw new Exception(sprintf("Error en %s.obtenerParticipacion: los campos clave no pueden ser nulos.", get_class($this)));
		}

		$resultado = $this->obtenerParticipaciones($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pnumero_participacion);

		if (count($resultado) == 0) {
			return null;
		} else if (count($resultado) == 1) {
			return $resultado[0];
		} else {
			throw new Exception(sprintf("Error en %s.obtenerParticipacion: se encontr&oacute; m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
		}
	}

	/**
	 * NGExpedientes: Elimina un conjunto de Participaciones en base a diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-02 13:55:07
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) numero_participacion
	 * @param  string texto
	 * @param  integer id_usuario
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarParticipaciones(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pnumero_participacion = null,
		$ptexto = null) {
		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBExpedientes()->eliminarParticipaciones($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pnumero_participacion, $ptexto);

			// Auditoria (si es que se elimino algo, tengo que auditar)
			if ($resultado > 0) {
				$dummy = new Participacion($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pnumero_participacion, $ptexto);

				// 07/02/2022 XXXX
				if (is_null($pnumero_participacion)) {
					$audit_observaciones = "AUTOMATICO, al eliminar un Expediente.";
				} else {
					$audit_observaciones = NG::auditorias()->generarMensajeEliminacion($resultado, array($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pnumero_participacion, $ptexto));
				}

				NG::auditorias()->auditarComoExpediente(
					$dummy, null, Auditoria::OP_BAJA, 'hcd.expe_participaciones', null, null, $audit_observaciones);
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarParticipaciones: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina una instancia de tipo Participacion en base a su identificador.
	 * GenerateClass 0.97.5 beta @ 2016-09-02 13:55:07
	 * @param  Participacion $pParticipacion 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarParticipacion(Participacion $pParticipacion) {
		if (is_null($pParticipacion)) {
			throw new Exception(sprintf("Error en %s.eliminarParticipacion: la instancia a eliminar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarParticipaciones($pParticipacion->anio, $pParticipacion->tipo, $pParticipacion->numero, $pParticipacion->cuerpo, $pParticipacion->alcance, $pParticipacion->numero_participacion);

			if ($resultado > 1) {
				throw new Exception(sprintf("Error en %s.eliminarParticipacion: se quiso eliminar m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarParticipacion: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return ($resultado === 1);
	}

	/**
	 * NGExpedientes:  Se verifica si el expediente ya posee la Propuesta (pdf) para su participación ciudadana
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @return boolean
	 */
	public function poseePropuestaParticipacion($panio, $ptipo, $pnumero) {

		if (is_null($panio) || is_null($ptipo) || is_null($pnumero)) {
			throw new Exception(sprintf("Error en %s.poseePropuestaParticipacion: los campos clave no pueden ser nulos.", get_class($this)));
		}

		try {
			// Nombre codificado del directorio del expediente respectivo
			$nombre_codificado = sprintf("%02d%s%05d", $panio % 100, $ptipo, $pnumero);

			// Ruta del directorio del expediente, determinado por la clave del expediente respectivo
			$ruta_directorio_expediente = PATH_KRAKEN_RESOURCES_PROYECTOS . $panio . "/" . $nombre_codificado . "/";

			// URL del directorio del expediente, determinado por la clave del expediente respectivo
			$url_directorio_expediente = URL_KRAKEN_RESOURCES_PROYECTOS . $panio . "/" . $nombre_codificado . "/";

		} catch (Exception $e) {
			throw new Exception(sprintf("Error en %s.poseePropuestaParticipacion: %s", get_class($this), $e->getMessage()));
		}

		// Si existe la propuesta (pdf), retorna la URL, sino valor nulo
		return (is_file($ruta_directorio_expediente . 'participacion.pdf')) ? $url_directorio_expediente . 'participacion.pdf' : null;
	}

	// ************************************************************************
	// 2026-03-19 XXXX
	// Eliminaciones de Exped. Electrónico, Revisiones y Firmas
	// Asociados a un Expediente o Nota determinado
	// ************************************************************************

	/**
	 * NGExpedientes: Elimina un conjunto de Firmas Pendientes de un Expediente Elec.
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarFirmasExpedienteElecPend(
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null) {

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = DB::getInstanceDBExpedientes()->eliminarFirmasExpedienteElecPend($panio, $ptipo, $pnumero, $pcuerpo, $palcance);

			//Logger::get()->Log("resultado_eliminarFirmasExpedienteElecPend_".date("Ymd_His"), $resultado);

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarFirmasExpedienteElecPend: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina un conjunto de Firmas de un Expediente Elec.
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarFirmasExpedienteElec(
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null) {

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = DB::getInstanceDBExpedientes()->eliminarFirmasExpedienteElec($panio, $ptipo, $pnumero, $pcuerpo, $palcance);

			//Logger::get()->Log("resultado_eliminarFirmasExpedienteElec_".date("Ymd_His"), $resultado);

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarFirmasExpedienteElec: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina un conjunto de Revisiones Pendientes de un Expediente Elec.
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarRevExpedienteElecPend(
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null) {

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = DB::getInstanceDBExpedientes()->eliminarRevExpedienteElecPend($panio, $ptipo, $pnumero, $pcuerpo, $palcance);

			//Logger::get()->Log("resultado_eliminarRevExpedienteElecPend_".date("Ymd_His"), $resultado);

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarRevExpedienteElecPend: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina un Expediente Elec. Pendiente
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarExpedientesElecPend(
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null) {

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = DB::getInstanceDBExpedientes()->eliminarExpedientesElecPend($panio, $ptipo, $pnumero, $pcuerpo, $palcance);

			//Logger::get()->Log("resultado_eliminarExpedientesElecPend_".date("Ymd_His"), $resultado);

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarExpedientesElecPend: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina un Expediente Elec.
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarExpedientesElec(
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null) {

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = DB::getInstanceDBExpedientes()->eliminarExpedientesElec($panio, $ptipo, $pnumero, $pcuerpo, $palcance);

			//Logger::get()->Log("resultado_eliminarExpedientesElec_".date("Ymd_His"), $resultado);

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarExpedientesElec: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	// ************************************************************************
	// Sanciones
	// ************************************************************************

	/**
	 * NGExpedientes: Obtiene una coleccion de elementos tipo Sancion en base a diferentes criterios de selección.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_proyecto
	 * @param  string (PK) fecha_sancion
	 * @param  string numero_sancion
	 * @param  string fecha_promulga
	 * @param  string numero_promulga
	 * @param  string decreto_promulga
	 * @param  string fecha_veto
	 * @param  string decreto_veto
	 * @param  string decreto_presidencia
	 * @param  string fecha_remision_de_comunicacion
	 * @param  string fecha_1er_vto_comunicacion
	 * @param  string fecha_2do_vto_comunicacion
	 * @param  string fecha_rta_comunicacion
	 * @param  string observaciones_sancion
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<Sancion>
	 */
	public function obtenerSanciones(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_proyecto = null,
		$pfecha_sancion = null,
		$pnumero_sancion = null,
		$pfecha_promulga = null,
		$pnumero_promulga = null,
		$pdecreto_promulga = null,
		$pfecha_veto = null,
		$pdecreto_veto = null,
		$pdecreto_presidencia = null,
		$pfecha_remision_de_comunicacion = null,
		$pfecha_1er_vto_comunicacion = null,
		$pfecha_2do_vto_comunicacion = null,
		$pfecha_rta_comunicacion = null,
		$pobservaciones_sancion = null,
		$pid_usuario = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBExpedientes()->obtenerSanciones($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_proyecto, $pfecha_sancion, $pnumero_sancion, $pfecha_promulga, $pnumero_promulga, $pdecreto_promulga, $pfecha_veto, $pdecreto_veto, $pdecreto_presidencia, $pfecha_remision_de_comunicacion, $pfecha_1er_vto_comunicacion, $pfecha_2do_vto_comunicacion, $pfecha_rta_comunicacion, $pobservaciones_sancion, $pid_usuario,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerSanciones: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Sancion
		$resultado = $this->arrayResultToInstance($filas, 'Sancion');

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Determina la cantidad de elementos tipo Sancion obtenidos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_proyecto
	 * @param  string (PK) fecha_sancion
	 * @param  string numero_sancion
	 * @param  string fecha_promulga
	 * @param  string numero_promulga
	 * @param  string decreto_promulga
	 * @param  string fecha_veto
	 * @param  string decreto_veto
	 * @param  string decreto_presidencia
	 * @param  string fecha_remision_de_comunicacion
	 * @param  string fecha_1er_vto_comunicacion
	 * @param  string fecha_2do_vto_comunicacion
	 * @param  string fecha_rta_comunicacion
	 * @param  string observaciones_sancion
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerSancionesCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_proyecto = null,
		$pfecha_sancion = null,
		$pnumero_sancion = null,
		$pfecha_promulga = null,
		$pnumero_promulga = null,
		$pdecreto_promulga = null,
		$pfecha_veto = null,
		$pdecreto_veto = null,
		$pdecreto_presidencia = null,
		$pfecha_remision_de_comunicacion = null,
		$pfecha_1er_vto_comunicacion = null,
		$pfecha_2do_vto_comunicacion = null,
		$pfecha_rta_comunicacion = null,
		$pobservaciones_sancion = null,
		$pid_usuario = null) {
		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBExpedientes()->obtenerSancionesCantidad($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_proyecto, $pfecha_sancion, $pnumero_sancion, $pfecha_promulga, $pnumero_promulga, $pdecreto_promulga, $pfecha_veto, $pdecreto_veto, $pdecreto_presidencia, $pfecha_remision_de_comunicacion, $pfecha_1er_vto_comunicacion, $pfecha_2do_vto_comunicacion, $pfecha_rta_comunicacion, $pobservaciones_sancion, $pid_usuario);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerSancionesCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGExpedientes: Obtiene una instancia de tipo Sancion en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_proyecto
	 * @param  string (PK) fecha_sancion
	 * @return Sancion Instancia de Sancion buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerSancion(
		// Parametros
		$panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_proyecto, $pfecha_sancion) {
		if (is_null($panio) || is_null($ptipo) || is_null($pnumero) || is_null($pcuerpo) || is_null($palcance) || is_null($porden_proyecto) || is_null($pfecha_sancion)) {
			throw new Exception(sprintf("Error en %s.obtenerSancion: los campos clave no pueden ser nulos.", get_class($this)));
		}

		$resultado = $this->obtenerSanciones($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_proyecto, $pfecha_sancion);

		if (count($resultado) == 0) {
			return null;
		} else if (count($resultado) == 1) {
			return $resultado[0];
		} else {
			throw new Exception(sprintf("Error en %s.obtenerSancion: se encontr&oacute; m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
		}

	}

	/**
	 * Guarda una instancia de tipo Sancion. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  Sancion $pSancion 	Instancia a guardar.
	 * @param  boolean $pRecargar 	Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return Sancion              Instancia guardada.
	 */
	public function guardarSancion(Sancion $pSancion, $pRecargar = true) {
		if (is_null($pSancion)) {
			throw new Exception(sprintf("Error en %s.guardarSancion: la instancia a guardar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// 03/02/2022 XXXX, para optimizar la auditoría del alta/modificación de la Sanción

			// Se obtiene la Sanción con su info actual (ante una modificación)
			$sancion_actual = $this->obtenerSancion($pSancion->anio, $pSancion->tipo, $pSancion->numero, $pSancion->cuerpo, $pSancion->alcance, $pSancion->orden_proyecto, $pSancion->fecha_sancion);

			// Si no existe
			if (is_null($sancion_actual)) {
				// Se define la operación a auditar, ante el Alta de la Sanción
				$audit_operacion = Auditoria::OP_ALTA;

				$audit_observaciones  = "Nro Sanción: ".$pSancion->numero_sancion;
				$audit_observaciones .= (! is_null($pSancion->fecha_promulga)) ? "<br>Fecha Promulgación: ".$pSancion->fecha_promulga : "";
				$audit_observaciones .= (! is_null($pSancion->numero_promulga)) ? "<br>Nro Promulgación: ".$pSancion->numero_promulga : "";
				$audit_observaciones .= (! is_null($pSancion->decreto_promulga)) ? "<br>Decreto Promulgación: ".$pSancion->decreto_promulga : "";
				$audit_observaciones .= (! is_null($pSancion->fecha_veto)) ? "<br>Fecha Veto: ".$pSancion->fecha_veto : "";
				$audit_observaciones .= (! is_null($pSancion->decreto_veto)) ? "<br>Decreto Veto: ".$pSancion->decreto_veto : "";
				$audit_observaciones .= (! is_null($pSancion->observaciones_sancion)) ? "<br>Observaciones: ".$pSancion->observaciones_sancion : "";
			} else {

				// Se define la operación a auditar, ante la Modificación de la Sanción
				$audit_operacion = Auditoria::OP_MODIFICA;

				$audit_observaciones  = "Nro Sanción: ".$pSancion->numero_sancion;
				$audit_observaciones .= "<br> ANTES:";
				$audit_observaciones .= (! is_null($sancion_actual->fecha_promulga)) ? "<br>Fecha Promulgación: ".$sancion_actual->fecha_promulga : "";
				$audit_observaciones .= (! is_null($sancion_actual->numero_promulga)) ? "<br>Nro Promulgación: ".$sancion_actual->numero_promulga : "";
				$audit_observaciones .= (! is_null($sancion_actual->decreto_promulga)) ? "<br>Decreto Promulgación: ".$sancion_actual->decreto_promulga : "";
				$audit_observaciones .= (! is_null($sancion_actual->fecha_veto)) ? "<br>Fecha Veto: ".$sancion_actual->fecha_veto : "";
				$audit_observaciones .= (! is_null($sancion_actual->decreto_veto)) ? "<br>Decreto Veto: ".$sancion_actual->decreto_veto : "";
				$audit_observaciones .= (! is_null($sancion_actual->observaciones_sancion)) ? "<br>Observaciones: ".$sancion_actual->observaciones_sancion : "";
				$audit_observaciones .= "<br> AHORA:";
				$audit_observaciones .= (! is_null($pSancion->fecha_promulga)) ? "<br>Fecha Promulgación: ".$pSancion->fecha_promulga : "";
				$audit_observaciones .= (! is_null($pSancion->numero_promulga)) ? "<br>Nro Promulgación: ".$pSancion->numero_promulga : "";
				$audit_observaciones .= (! is_null($pSancion->decreto_promulga)) ? "<br>Decreto Promulgación: ".$pSancion->decreto_promulga : "";
				$audit_observaciones .= (! is_null($pSancion->fecha_veto)) ? "<br>Fecha Veto: ".$pSancion->fecha_veto : "";
				$audit_observaciones .= (! is_null($pSancion->decreto_veto)) ? "<br>Decreto Veto: ".$pSancion->decreto_veto : "";
				$audit_observaciones .= (! is_null($pSancion->observaciones_sancion)) ? "<br>Observaciones: ".$pSancion->observaciones_sancion : "";
			}

			$id = DB::getInstanceDBExpedientes()->guardarSancion(
				$pSancion->anio,
				$pSancion->tipo,
				$pSancion->numero,
				$pSancion->cuerpo,
				$pSancion->alcance,
				$pSancion->orden_proyecto,
				$pSancion->fecha_sancion,
				$pSancion->numero_sancion,
				$pSancion->fecha_promulga,
				$pSancion->numero_promulga,
				$pSancion->decreto_promulga,
				$pSancion->fecha_veto,
				$pSancion->decreto_veto,
				$pSancion->decreto_presidencia,
				$pSancion->fecha_remision_de_comunicacion,
				$pSancion->fecha_1er_vto_comunicacion,
				$pSancion->fecha_2do_vto_comunicacion,
				$pSancion->fecha_rta_comunicacion,
				$pSancion->observaciones_sancion,
				$pSancion->id_usuario);

			// Auditoria
			NG::auditorias()->auditarComoExpediente(
				$pSancion, null, $audit_operacion, 'hcd.expe_sanciones', $pSancion->fecha_sancion, $pSancion->orden_proyecto, $audit_observaciones);

			// Si se ingresó una Fecha de Promulgación
			if ($pSancion->fecha_promulga != '') {

				// 2026-03-04 XXXX
				// Se corrige: se usaba $pSancion->orden_proyecto, en vez del siguiente número de Orden para dicho expediente.
				// --------------------------------------------------------------------------
				// Se obtiene el siguiente número de Orden de estado del expediente
				$siguiente_orden = $this->obtenerNumeroSiguienteEstado(
					$pSancion->anio,
					$pSancion->tipo,
					$pSancion->numero,
					$pSancion->cuerpo,
					$pSancion->alcance,
					null // desconoce la fecha del último estado registrado
				);

				// Se crea una instancia de Estado, utilizando la información de la Sanción
				$estado = new Estado(
					$pSancion->anio,
					$pSancion->tipo,
					$pSancion->numero,
					$pSancion->cuerpo,
					$pSancion->alcance,
					$pSancion->fecha_promulga, // fecha_estado
					$siguiente_orden, // orden_estado
					8, // id_codestado = Promulgado
					'AUTOMATICO', // observaciones_estado
					$pSancion->id_usuario
				);

				// Se ingresa el Estado 8 = Promulgado para el expediente respectivo
				$estado_ingresado = $this->guardarEstado($estado, true);
				// Se audita internamente el ingreso de dicho Estado !!
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarSancion: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$resultado = $this->obtenerSancion($pSancion->anio, $pSancion->tipo, $pSancion->numero, $pSancion->cuerpo, $pSancion->alcance, $pSancion->orden_proyecto, $pSancion->fecha_sancion);
		} else {
			$resultado = $pSancion;
		}

		DB::getInstanceDBExpedientes()->desconectar();

		if (is_null($resultado)) {
			throw new Exception(sprintf("Error grave en %s.guardarSancion: no se encuentra el contenido actualizado.", get_class($this)));
		}

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina un conjunto de Sanciones en base a diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-02 13:55:07
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_proyecto
	 * @param  string (PK) fecha_sancion
	 * @param  string numero_sancion
	 * @param  string fecha_promulga
	 * @param  string numero_promulga
	 * @param  string decreto_promulga
	 * @param  string fecha_veto
	 * @param  string decreto_veto
	 * @param  string decreto_presidencia
	 * @param  string fecha_remision_de_comunicacion
	 * @param  string fecha_1er_vto_comunicacion
	 * @param  string fecha_2do_vto_comunicacion
	 * @param  string fecha_rta_comunicacion
	 * @param  string observaciones_sancion
	 * @param  integer id_usuario
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarSanciones(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_proyecto = null,
		$pfecha_sancion = null,
		$pnumero_sancion = null,
		$pfecha_promulga = null,
		$pnumero_promulga = null,
		$pdecreto_promulga = null,
		$pfecha_veto = null,
		$pdecreto_veto = null,
		$pdecreto_presidencia = null,
		$pfecha_remision_de_comunicacion = null,
		$pfecha_1er_vto_comunicacion = null,
		$pfecha_2do_vto_comunicacion = null,
		$pfecha_rta_comunicacion = null,
		$pobservaciones_sancion = null,
		$pid_usuario = null) {
		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// 03/02/2022 XXXX, se modifica la Observación al auditar la Baja de la Sanción
			$audit_observaciones = null;
			if (is_null($porden_proyecto) && is_null($pfecha_sancion)) {
				$audit_observaciones = "AUTOMATICO, al eliminar un Expediente.";
			} else {
				// Se obtiene la info de la Sanción a eliminar
				$sancion = $this->obtenerSancion($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_proyecto, $pfecha_sancion);

				$audit_observaciones = "Sanción Nro.: ".$sancion->numero_sancion;

				$audit_observaciones .= (! is_null($sancion->fecha_promulga)) ? "<br>Fecha Promulgación: ".$sancion->fecha_promulga : "";
				$audit_observaciones .= (! is_null($sancion->numero_promulga)) ? "<br>Nro Promulgación: ".$sancion->numero_promulga : "";
				$audit_observaciones .= (! is_null($sancion->decreto_promulga)) ? "<br>Decreto Promulgación: ".$sancion->decreto_promulga : "";
				$audit_observaciones .= (! is_null($sancion->fecha_veto)) ? "<br>Fecha Veto: ".$sancion->fecha_veto : "";
				$audit_observaciones .= (! is_null($sancion->decreto_veto)) ? "<br>Decreto Veto: ".$sancion->decreto_veto : "";
				$audit_observaciones .= (! is_null($sancion->observaciones_sancion)) ? "<br>Observaciones: ".$sancion->observaciones_sancion : "";
			}

			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBExpedientes()->eliminarSanciones($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_proyecto, $pfecha_sancion, $pnumero_sancion, $pfecha_promulga, $pnumero_promulga, $pdecreto_promulga, $pfecha_veto, $pdecreto_veto, $pdecreto_presidencia, $pfecha_remision_de_comunicacion, $pfecha_1er_vto_comunicacion, $pfecha_2do_vto_comunicacion, $pfecha_rta_comunicacion, $pobservaciones_sancion, $pid_usuario);

			// Auditoria (si es que se elimino algo, tengo que auditar)
			if ($resultado > 0) {
				$dummy = new Sancion($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_proyecto, $pfecha_sancion, $pnumero_sancion, $pfecha_promulga, $pnumero_promulga, $pdecreto_promulga, $pfecha_veto, $pdecreto_veto, $pdecreto_presidencia, $pfecha_remision_de_comunicacion, $pfecha_1er_vto_comunicacion, $pfecha_2do_vto_comunicacion, $pfecha_rta_comunicacion, $pobservaciones_sancion, $pid_usuario);

				NG::auditorias()->auditarComoExpediente(
					$dummy, null, Auditoria::OP_BAJA, 'hcd.expe_sanciones',
					$pfecha_sancion, $porden_proyecto, $audit_observaciones);

				//NG::auditorias()->generarMensajeEliminacion($resultado, array($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_proyecto, $pfecha_sancion, $pnumero_sancion, $pfecha_promulga, $pnumero_promulga, $pdecreto_promulga, $pfecha_veto, $pdecreto_veto, $pdecreto_presidencia, $pfecha_remision_de_comunicacion, $pfecha_1er_vto_comunicacion, $pfecha_2do_vto_comunicacion, $pfecha_rta_comunicacion, $pobservaciones_sancion, $pid_usuario))
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarSanciones: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina una instancia de tipo Sancion en base a su identificador.
	 * GenerateClass 0.97.5 beta @ 2016-09-02 13:55:07
	 * @param  Sancion $pSancion 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarSancion(Sancion $pSancion) {
		if (is_null($pSancion)) {
			throw new Exception(sprintf("Error en %s.eliminarSancion: la instancia a eliminar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarSanciones($pSancion->anio, $pSancion->tipo, $pSancion->numero, $pSancion->cuerpo, $pSancion->alcance, $pSancion->orden_proyecto, $pSancion->fecha_sancion);

			if ($resultado > 1) {
				throw new Exception(sprintf("Error en %s.eliminarSancion: se quiso eliminar m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarSancion: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return ($resultado == 1);
	}

	// ************************************************************************
	// Temas
	// ************************************************************************

	/**
	 * NGExpedientes: Obtiene una coleccion de elementos tipo Tema en base a diferentes criterios de selección.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) id_codtema
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<Tema>
	 */
	public function obtenerTemas(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pid_codtema = null,
		$pid_usuario = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBExpedientes()->obtenerTemas($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pid_codtema, $pid_usuario,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerTemas: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Tema
		$resultado = $this->arrayResultToInstance($filas, 'Tema');

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Determina la cantidad de elementos tipo Tema obtenidos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) id_codtema
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerTemasCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pid_codtema = null,
		$pid_usuario = null) {
		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBExpedientes()->obtenerTemasCantidad($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pid_codtema, $pid_usuario);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerTemasCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGExpedientes: Obtiene una instancia de tipo Tema en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) id_codtema
	 * @return Tema Instancia de Tema buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerTema(
		// Parametros
		$panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pid_codtema) {
		if (is_null($panio) || is_null($ptipo) || is_null($pnumero) || is_null($pcuerpo) || is_null($palcance) || is_null($pid_codtema)) {
			throw new Exception(sprintf("Error en %s.obtenerTema: los campos clave no pueden ser nulos.", get_class($this)));
		}

		$resultado = $this->obtenerTemas($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pid_codtema);

		if (count($resultado) == 0) {
			return null;
		} else if (count($resultado) == 1) {
			return $resultado[0];
		} else {
			throw new Exception(sprintf("Error en %s.obtenerTema: se encontr&oacute; m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
		}

	}

	/**
	 * Guarda una instancia de tipo Tema. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  Tema $pTema 	Instancia a guardar.
	 * @param  boolean $pRecargar 		Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return Tema               Instancia guardada.
	 */
	public function guardarTema(Tema $pTema, $pRecargar = true) {
		if (is_null($pTema)) {
			throw new Exception(sprintf("Error en %s.guardarTema: la instancia a guardar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$audit_operacion = (is_null($this->obtenerTema($pTema->anio, $pTema->tipo, $pTema->numero, $pTema->cuerpo, $pTema->alcance, $pTema->id_codtema))) ? Auditoria::OP_ALTA : Auditoria::OP_MODIFICA;

			$id = DB::getInstanceDBExpedientes()->guardarTema(
				$pTema->anio,
				$pTema->tipo,
				$pTema->numero,
				$pTema->cuerpo,
				$pTema->alcance,
				$pTema->id_codtema,
				$pTema->id_usuario);

			// Auditoria
			NG::auditorias()->auditarComoExpediente(
				$pTema, null, $audit_operacion, 'hcd.expe_temas', null, null,
				sprintf('Se ha guardado el tema %d-%s-%d-%d-%d, id: %d', $pTema->anio, $pTema->tipo, $pTema->numero, $pTema->cuerpo, $pTema->alcance, $pTema->id_codtema)
			);

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarTema: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$resultado = $this->obtenerTema($pTema->anio, $pTema->tipo, $pTema->numero, $pTema->cuerpo, $pTema->alcance, $pTema->id_codtema);
		} else {
			$resultado = $pTema;
		}

		DB::getInstanceDBExpedientes()->desconectar();

		if (is_null($resultado)) {
			throw new Exception(sprintf("Error grave en %s.guardarTema: no se encuentra el contenido actualizado.", get_class($this)));
		}

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina un conjunto de Temas en base a diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-02 13:55:07
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) id_codtema
	 * @param  integer id_usuario
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarTemas(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pid_codtema = null,
		$pid_usuario = null) {
		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBExpedientes()->eliminarTemas($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pid_codtema, $pid_usuario);

			// Auditoria (si es que se elimino algo, tengo que auditar)
			if ($resultado > 0) {
				$dummy = new Tema($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pid_codtema, $pid_usuario);

				// 07/02/2022 XXXX
				// Si se recibe o no, el Id de la Codificadora del Tema
				$audit_observaciones = (is_null($pid_codtema)) ? "AUTOMATICO, al eliminar un Expediente." : null;

				NG::auditorias()->auditarComoExpediente(
					$dummy, null, Auditoria::OP_BAJA, 'hcd.expe_temas', null, null, $audit_observaciones);

				//NG::auditorias()->generarMensajeEliminacion($resultado, array($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pid_codtema, $pid_usuario))
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarTemas: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina una instancia de tipo Tema en base a su identificador.
	 * GenerateClass 0.97.5 beta @ 2016-09-02 13:55:07
	 * @param  Tema $pTema 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarTema(Tema $pTema) {
		if (is_null($pTema)) {
			throw new Exception(sprintf("Error en %s.eliminarTema: la instancia a eliminar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarTemas($pTema->anio, $pTema->tipo, $pTema->numero, $pTema->cuerpo, $pTema->alcance, $pTema->id_codtema);

			if ($resultado > 1) {
				throw new Exception(sprintf("Error en %s.eliminarTema: se quiso eliminar m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarTema: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return ($resultado == 1);
	}

	// ************************************************************************
	// Informes
	// ************************************************************************

	/**
	 * NGExpedientes: toma una instancia de Informe y completa sus colecciones de instancias asociadas (conjunto de instancias asociadas).
	 * @param  Informe $informe Informe del cual se desea completar sus colecciones internas.
	 * @return Informe                 Informe del cual se desea completar sus colecciones internas.
	 */
	public function completarInstanciaInforme(Informe $informe) {

		if (is_null($informe)) {
			throw new Exception(sprintf("Error en %s.completarInstanciaInforme: %s", get_class($this), "La instancia del informe a completar no puede ser nula."));
		}

		$informe->getProyectos()->fillFromArray($this->obtenerProyectos($informe->anio, $informe->tipo, $informe->numero, $informe->cuerpo, $informe->alcance));

		return $informe;
	}

	/**
	 * NGExpedientes: Obtiene una coleccion de elementos tipo Informe en base a diferentes criterios de selección.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_giro
	 * @param  float (PK) orden_informe
	 * @param  string fecha_pedido_informe
	 * @param  string fecha_vuelta_informe
	 * @param  string detalle_informe
	 * @param  string observaciones_informe
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<Informe>
	 */
	public function obtenerInformes(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_giro = null,
		$porden_informe = null,
		$pfecha_pedido_informe = null,
		$pfecha_vuelta_informe = null,
		$pdetalle_informe = null,
		$pobservaciones_informe = null,
		$pid_usuario = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBExpedientes()->obtenerInformes(
				$panio, $ptipo, $pnumero, $pcuerpo, $palcance,
				$porden_giro, $porden_informe, $pfecha_pedido_informe, $pfecha_vuelta_informe,
				$pdetalle_informe, $pobservaciones_informe, $pid_usuario,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerInformes: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		// Se transforma y devuelve el array de resultados en una coleccion de elementos tipo Informe
		return $this->arrayResultToInstance($filas, 'Informe');
	}

	/**
	 * NGExpedientes: Determina la cantidad de elementos tipo Informe obtenidos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string  (PK) tipo
	 * @param  float   (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float   (PK) orden_giro
	 * @param  float   (PK) orden_informe
	 * @param  string  		fecha_pedido_informe
	 * @param  string  		fecha_vuelta_informe
	 * @param  string  		detalle_informe
	 * @param  string  		observaciones_informe
	 * @param  integer 		id_usuario
	 * @return int
	 */
	public function obtenerInformesCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_giro = null,
		$porden_informe = null,
		$pfecha_pedido_informe = null,
		$pfecha_vuelta_informe = null,
		$pdetalle_informe = null,
		$pobservaciones_informe = null,
		$pid_usuario = null) {
		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBExpedientes()->obtenerInformesCantidad($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_giro, $porden_informe, $pfecha_pedido_informe, $pfecha_vuelta_informe, $pdetalle_informe, $pobservaciones_informe, $pid_usuario);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerInformesCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * DBExpedientes: a partir de una determinada clave de expediente, retorna el número de orden siguiente del último informe insertado.
	 * En caso de que no existan informes para un determinado año y tipo, devuelve cero.
	 * @param  [type] $panio    [description]
	 * @param  [type] $ptipo    [description]
	 * @param  [type] $pnumero  [description]
	 * @param  [type] $pcuerpo  [description]
	 * @param  [type] $palcance [description]
	 * @return int           [description]
	 */
	public function obtenerNumeroSiguienteInforme(
		// Parametros
		$panio,
		$ptipo,
		$pnumero,
		$pcuerpo,
		$palcance) {

		DB::getInstanceDBExpedientes()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBExpedientes()->obtenerNumeroUltimoInforme($panio, $ptipo, $pnumero, $pcuerpo, $palcance);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerNumeroUltimoInforme: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado + 1;

	}

	/**
	 * NGExpedientes: Obtiene una instancia de tipo Informe en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_giro
	 * @param  float (PK) orden_informe
	 * @return Informe Instancia de Informe buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerInforme(
		// Parametros
		$panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_giro, $porden_informe) {
		if (is_null($panio) || is_null($ptipo) || is_null($pnumero) || is_null($pcuerpo) || is_null($palcance) || is_null($porden_giro) || is_null($porden_informe)) {
			throw new Exception(sprintf("Error en %s.obtenerInforme: los campos clave no pueden ser nulos.", get_class($this)));
		}

		$resultado = $this->obtenerInformes($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_giro, $porden_informe);

		if (count($resultado) == 0) {
			return null;
		} else if (count($resultado) == 1) {
			return $resultado[0];
		} else {
			throw new Exception(sprintf("Error en %s.obtenerInforme: se encontr&oacute; m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
		}

	}

	/**
	 * Guarda una instancia de tipo Informe. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.97.3 beta @ 2016-08-29 10:29:16
	 * @param  Informe $pInforme 	Instancia a guardar.
	 * @param  boolean $pRecargar 	Recargar la clase después de ser guardada, para actualizar su estado.
	 * @return Informe              Instancia guardada.
	 */
	public function guardarInforme(Informe $pInforme, $pRecargar = true) {
		if (is_null($pInforme)) {
			throw new Exception(sprintf("Error en %s.guardarInforme: la instancia a guardar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$audit_operacion = (is_null($this->obtenerInforme($pInforme->anio, $pInforme->tipo, $pInforme->numero, $pInforme->cuerpo, $pInforme->alcance, $pInforme->orden_giro, $pInforme->orden_informe))) ? Auditoria::OP_ALTA : Auditoria::OP_MODIFICA;

			$id = DB::getInstanceDBExpedientes()->guardarInforme(
				$pInforme->anio,
				$pInforme->tipo,
				$pInforme->numero,
				$pInforme->cuerpo,
				$pInforme->alcance,
				$pInforme->orden_giro,
				$pInforme->orden_informe,
				$pInforme->fecha_pedido_informe,
				$pInforme->fecha_vuelta_informe,
				$pInforme->detalle_informe,
				$pInforme->observaciones_informe,
				$pInforme->id_usuario);

			// Auditoria
			NG::auditorias()->auditarComoExpediente(
				$pInforme, null, $audit_operacion, 'hcd.expe_informes',
				$pInforme->fecha_pedido_informe,
				$pInforme->orden_giro,
				sprintf('Se ha guardado el informe %d-%s-%d-%d-%d <br>Orden giro: %d <br>Orden informe: %d',
					$pInforme->anio, $pInforme->tipo, $pInforme->numero, $pInforme->cuerpo, $pInforme->alcance,
					$pInforme->orden_giro,
					$pInforme->orden_informe)
			);

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarInforme: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$resultado = $this->obtenerInforme($pInforme->anio, $pInforme->tipo, $pInforme->numero, $pInforme->cuerpo, $pInforme->alcance, $pInforme->orden_giro, $pInforme->orden_informe);
		} else {
			$resultado = $pInforme;
		}

		DB::getInstanceDBExpedientes()->desconectar();

		if (is_null($resultado)) {
			throw new Exception(sprintf("Error grave en %s.guardarInforme: no se encuentra el contenido actualizado.", get_class($this)));
		}

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina un conjunto de Informes en base a diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-02 13:55:07
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_giro
	 * @param  float (PK) orden_informe
	 * @param  string fecha_pedido_informe
	 * @param  string fecha_vuelta_informe
	 * @param  string detalle_informe
	 * @param  string observaciones_informe
	 * @param  integer id_usuario
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarInformes(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_giro = null,
		$porden_informe = null,
		$pfecha_pedido_informe = null,
		$pfecha_vuelta_informe = null,
		$pdetalle_informe = null,
		$pobservaciones_informe = null,
		$pid_usuario = null) {
		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// 08/02/2022 XXXX, se modifica la Observación al auditar la Baja
			$audit_orden_log = null;
			$audit_observaciones = null;

			if (is_null($porden_giro) && is_null($porden_informe)) {
				$audit_observaciones = "AUTOMATICO, al eliminar un Expediente.";
			} else {
				// Se obtiene la info del Informe a eliminar
				$informe = $this->obtenerInforme($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_giro, $porden_informe);

				$audit_orden_log = $porden_giro;

				$audit_observaciones = sprintf("Orden giro: %d<br>Orden informe: %d", $informe->orden_giro, $informe->orden_informe);
			}

			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBExpedientes()->eliminarInformes($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_giro, $porden_informe, $pfecha_pedido_informe, $pfecha_vuelta_informe, $pdetalle_informe, $pobservaciones_informe, $pid_usuario);

			// Auditoria (si es que se elimino algo, tengo que auditar)
			if ($resultado > 0) {

				$dummy = new Informe($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $porden_giro, $porden_informe, $pfecha_pedido_informe, $pfecha_vuelta_informe, $pdetalle_informe, $pobservaciones_informe, $pid_usuario);

				NG::auditorias()->auditarComoExpediente(
					$dummy, null, Auditoria::OP_BAJA, 'hcd.expe_informes',
					null, $audit_orden_log, $audit_observaciones);
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarInformes: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * NGExpedientes: Elimina una instancia de tipo Informe en base a su identificador.
	 * GenerateClass 0.97.5 beta @ 2016-09-02 13:55:07
	 * @param  Informe $pInforme 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarInforme(Informe $pInforme) {
		if (is_null($pInforme)) {
			throw new Exception(sprintf("Error en %s.eliminarInforme: la instancia a eliminar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarInformes($pInforme->anio, $pInforme->tipo, $pInforme->numero, $pInforme->cuerpo, $pInforme->alcance, $pInforme->orden_giro, $pInforme->orden_informe);

			if ($resultado > 1) {
				throw new Exception(sprintf("Error en %s.eliminarInforme: se quiso eliminar m&aacute;s de una ocurrencia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
			}

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarInforme: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return ($resultado == 1);
	}

	/**
	 * Obtiene el conjunto de archivos de proyecto que estan disponibles "para cargar".
	 * @return array   $archivos 	Array asociativo con los archivos del expediente. Cada elemento del array posee "tipo", "archivo", "ruta_completa", "url" y "expediente"
	 */
	public function obtenerDocumentosACargarDE($info, $ruta_documentos_ejecutivo_para_cargar) {
		$archivos = array();

		// Obtengo los archivos del directorio
		$contenido_directorio = scandir($ruta_documentos_ejecutivo_para_cargar);

		// Para cada archivo encontrado en dicho directorio
		foreach ($contenido_directorio as $a) {
			// Sólo se toman los archivos que contiene
			if ($a != "." && $a != "..") {
				// Obtengo el expediente asociado
				$e_anio = $info['f_anio'];
				$e_tipo = $info['f_tipo'];
				$e_numero = $info['f_numero'];

				$archivos[] = array(
					'archivo' => $a,
					'ruta_completa' => $ruta_documentos_ejecutivo_para_cargar . $a,
				);
			}
		}

		return $archivos;
	}

	/**
	 * Se obtienen los expedientes de una Comisión en un rango de fechas determinado
	 * @param  [string] $pfecha_desde       Fecha desde
	 * @param  [string] $pfecha_hasta       Fecha hasta
	 * @param  [string] $pcomision_codigo 	Código de la comisión
	 * @return [array]  $resultado        	Colección de elementos tipo Expediente
	 */
	public function obtenerExpedientesParaMarcarComision(
		// Parametros
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pcomision_codigo = null) {
		DB::getInstanceDBExpedientes()->conectar();

		// La búsqueda por rango de fechas debe existir.
		if (is_null($pfecha_desde) && is_null($pfecha_hasta) && !is_null($pcomision_codigo)) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesParaMarcarComision: %s", get_class($this), "Los filtros por fecha Desde, fecha Hasta y Comisi&oacute;n, son obligatorios."));
		}

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBExpedientes()->obtenerExpedientesParaMarcarComision(
				$pfecha_desde,
				$pfecha_hasta,
				$pcomision_codigo
			);
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesParaMarcarComision: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Expediente
		$resultado = $this->arrayResultToInstance($filas, 'Expediente');

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * Se obtiene el nombre de la marca en comisión
	 * @param  [integer] $pmarca_comision Número de marca asignada
	 * @return [string]                   Descripción de dicha marca
	 */
	private function obtenerNombreMarcaEnComision($pmarca_comision) {
		switch ($pmarca_comision) {
		case 0:
			$this->nombre_marca = NOMBRE_MARCA_SM;
			break;
		case 1:
			$this->nombre_marca = NOMBRE_MARCA_PT;
			break;
		case 2:
			$this->nombre_marca = NOMBRE_MARCA_PSC;
			break;
		case 3:
			$this->nombre_marca = NOMBRE_MARCA_PA;
			break;
		case 4:
			$this->nombre_marca = NOMBRE_MARCA_PP;
			break;
		case 5:
			$this->nombre_marca = NOMBRE_MARCA_PC;
			break;
		}

		return $this->nombre_marca;
	}

	/**
	 * Se guarda la Marca de un expediente respectivo
	 * @param  Expediente 	 $pExpediente     Instancia de un expediente
	 * @param  [integer]     $pmarca_comision valor numérico de la marca elegida
	 * @param  [integer]     $pid_usuario     Identificador del usuario
	 * @return [type]                      [description]
	 */
	public function marcarComision(Expediente $pExpediente, $pmarca_comision, $pid_usuario) {
		if (is_null($pExpediente)) {
			throw new Exception(sprintf("Error en %s.marcarComision: %s", get_class($this), "La instancia del expediente a marcar comisi&oacute; no puede ser nula."));
		}

		// Si NO se ha recibido la Marca del expediente
		if (is_null($pmarca_comision)) {
			throw new Exception(sprintf("Error en %s.marcarComision: no se ha recibido la marca del expediente a guardar.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		$pExpediente->marca_comision = $pmarca_comision;
		$pExpediente->id_usuario = $pid_usuario;

		try {
			// Se registra la marca asignada al expediente respectivo
			$resultado = $this->guardarExpediente($pExpediente);

			// Se obtiene el nombre de la marca en comisión
			$nombre_segun_marca = $this->obtenerNombreMarcaEnComision($pmarca_comision);

			// Auditoria
			NG::auditorias()->auditarComoExpediente(
				$pExpediente, null, Auditoria::OP_MARCA, 'hcd.expe_expedientes', null, null,
				sprintf('Se ha marcado el expediente %d-%s-%d-%d-%d como \'%s\'', $pExpediente->anio, $pExpediente->tipo, $pExpediente->numero, $pExpediente->cuerpo, $pExpediente->alcance, $nombre_segun_marca)
			);

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.marcarComision: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * Se limpian las Marcas de los expedientes en una Comisión y un período de fechas respectivos
	 * @param  [string] $pfecha_desde     Fecha desde
	 * @param  [string] $pfecha_hasta     Fecha hasta
	 * @param  [string] $pcomision_codigo Código de la Comisión
	 */
	public function limpiarMarcas($pfecha_desde, $pfecha_hasta, $pcomision_codigo) {
		// Si NO se ha recibido la fecha desde y hasta
		if (is_null($pfecha_desde) && is_null($pfecha_hasta)) {
			throw new Exception(sprintf("Error en %s.limpiarMarcas: no se ha recibido la fecha desde y hasta.", get_class($this)));
		}

		// Si NO se ha recibido la Comisión
		if (is_null($pcomision_codigo)) {
			throw new Exception(sprintf("Error en %s.limpiarMarcas: no se ha recibido la comisi&oacute;n.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			DB::getInstanceDBExpedientes()->limpiarMarcas($pfecha_desde, $pfecha_hasta, $pcomision_codigo);

			DB::getInstanceDBExpedientes()->guardarTransaccion();
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.limpiarMarcas: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();
	}

	// DE ACA PARA ABAJO SE PODRÍA PASAR A UNA NUEVA Capa de Negocio NGCargaDocumentos.php
	// PARA REDUCIR LA Capa de Negocio actual
	// -----------------------------------------------------------------------------------

	/**
	 * Obtiene el conjunto de digitalizaciones que están disponibles "para cargar".
	 * @return array   $archivos 	Array asociativo con las digitalizaciones del expediente.
	 * Cada elemento del array posee "tipo", "archivo", "ruta_completa", "url" y "expediente"
	 */
	public function obtenerDigitalizacionesACargar() {
		$archivos = array();

		// Obtengo los archivos del directorio
		$dirContent = scandir(PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES);

		// Expresion regular para considerar solamente aquellos archivos que cumplen con la nomenclatura.
		$archivosPermitidos = preg_grep('/^[0-9]{2,2}(e|E|n|N|r|R)[0-9]{5,5}([aA]{0,1})\.(pdf|PDF)$/', $dirContent);

		$expData = array();
		foreach ($archivosPermitidos as $a) {
			// Obtengo el expediente asociado
			preg_match('/^([0-9]{2,2})(e|E|n|N|r|R)([0-9]{5,5})([aA]{0,1})\.(pdf|PDF)$/', $a, $expData);
			$e_anio = ($expData[1] >= 83) ? $expData[1] + 1900 : $expData[1] + 2000;
			$e_tipo = $expData[2];
			$e_numero = $expData[3];

			// Como puede haber mas de un expediente para año/tipo/numero, me quedo con el primero que encuentro
			$expedientes = $this->obtenerExpedientes($e_anio, $e_tipo, $e_numero);

			$exp = (count($expedientes) > 0) ? $expedientes[0] : null;

			// 30/10/2020 XXXX
			// Si existe el Expediente
			if ($exp != null) {
				$archivos[] = array(
					'tipo' => 'temporal',
					'archivo' => $a,
					'ruta_completa' => PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES . $a,
					'url' => URL_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES . $a,
					'expediente' => $exp);
			}
		}

		return $archivos;
	}

	/**
	 * Se carga un conjunto de Digitalizaciones en las carpetas correspondiente a cada expediente.
	 * Crea los directorios destino si estos no existiesen.
	 * @param  array  $archivos      Array con los nombres de archivo a mover.
	 * @return array                 Status de resultado de movimiento.
	 */
	public function cargarDigitalizaciones($archivos) {

		$resultado = array();
		$archivosAuditables = array();

		// Impersonalizo las acciones contra el FileSystem utilizando una conexion FTP local,
		// evitando de esta manera que el usuario de Apache sea quien crea archivos y directorios.
		FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		$permisosDirectorio = 0775; // sudo find . -type d -exec chmod 775 {} +
		$permisosArchivo = 0664; // sudo find . -type f -exec chmod 664 {} +

		// Expresion regular para considerar solamente aquellos archivos que cumplen con la nomenclatura.
		$archivosPermitidos = preg_grep('/^[0-9]{2,2}(e|E|n|N|r|R)[0-9]{5,5}([aA]{0,1})\.(pdf|PDF)$/', $archivos);

		foreach ($archivosPermitidos as $a) {
			// Obtengo el expediente asociado
			preg_match('/^([0-9]{2,2})(e|E|n|N|r|R)([0-9]{5,5})([aA]{0,1})\.(pdf|PDF)$/', $a, $expData);

			$e_anio = ($expData[1] >= 83) ? $expData[1] + 1900 : $expData[1] + 2000;
			$e_tipo = mb_strtoupper($expData[2]);
			$e_numero = $expData[3];

			$nombreCodificado = sprintf("%02d%s%05d", $e_anio % 100, $e_tipo, $e_numero);
			$directorioDestino = PATH_KRAKEN_RESOURCES_PROYECTOS . $e_anio . '/' . $nombreCodificado;
			$archivoDestino = $directorioDestino . '/' . $nombreCodificado . '.pdf';

			// Verifico la existencia del archivo
			$archivoOrigen = PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES . $a;
			if (!file_exists($archivoOrigen)) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => 'El documento no existe.');
				continue; // Salteo esta vuelta del foreach y continuo con el archivo siguiente.
			}

			// Verifico la existencia del expediente
			$expedientes = $this->obtenerExpedientes($e_anio, $e_tipo, $e_numero);
			if (count($expedientes) == 0) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => sprintf('El expediente %d-%s-%d no existe.', $e_anio, $e_tipo, $e_numero));
				continue; // Salteo esta vuelta del foreach y continuo con el archivo siguiente.
			}

			// Verifico que no exista el destino (dependiendo del flag $forzarDestino)
			$archivoExistente = $this->buscarDocumentoDigitalizado($e_anio, $e_tipo, $e_numero);

			// Si ya existe
			if ($archivoExistente != '') {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'WARNING',
					'mensaje' => sprintf('La digitalizacion ya existe para el expediente %d-%s-%d.', $e_anio, $e_tipo, $e_numero));
				continue; // Salteo esta vuelta del foreach y continuo con el archivo siguiente.
			}

			// Si no existe el directorio destino, lo creo (recursivamente)
			if (!file_exists($directorioDestino)) {
				try {
					FTPHelper::get()->mkDirProyecto($e_anio . '/' . $nombreCodificado, $permisosDirectorio);
				} catch (Exception $ex) {
					$resultado[] = array(
						'archivo' => $a,
						'estado' => 'ERROR',
						'mensaje' => $ex->getMessage());
					continue; // Salteo esta vuelta del foreach y continuo con el archivo siguiente.
				}
			} else if (!is_dir($directorioDestino)) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => 'El directorio destino existe, pero es un archivo.');
				continue; // Salteo esta vuelta del foreach y continuo con el archivo siguiente.
			}

			// Intento mover el archivo
			try {
				// Se mueve el archivo de la digitalización sin otorgarle permisos
				$this->moverDigitalizacion($archivoOrigen, $archivoDestino, $e_anio, $e_tipo, $e_numero);

				$mensajeFinal = sprintf('La digitalizacion %s fue asignada con &eacute;xito al expediente %d-%s-%d.', $a, $e_anio, $e_tipo, $e_numero);
				$archivosAuditables[] = sprintf('%s -> %s', basename($archivoOrigen), $archivoDestino);

				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'OK',
					'mensaje' => $mensajeFinal);

			} catch (Exception $ex) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => 'No se pudo mover la digitalizacion al directorio destino.');
				continue; // Salteo esta vuelta del foreach y continuo con el archivo siguiente.
			}
		}

		FTPHelper::get()->disconnect(); // Cierro la conexion al ftp

		return $resultado;
	}

	/**
	 * Se mueve el archivo de la digitalización sin otorgarle permisos
	 * @param  [type] $origen  [description]
	 * @param  [type] $destino [description]
	 * @return [type]          [description]
	 */
	private function moverDigitalizacion($origen, $destino, $e_anio, $e_tipo, $e_numero) {

		$conexion = FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		if (is_null($conexion)) {
			throw new Exception('No conectado al servicio de manipulación de archivos.');
		}

		// Se carga el archivo origen (el temporal) en el directorio destino
		if (!ftp_put($conexion, $destino, $origen, FTP_BINARY)) {
			throw new Exception('No se puede mover el archivo.');
		} else {
			// Se elimina el temporal del expediente respectivo
			if (file_exists($origen)) {

				if (ftp_delete($conexion, $origen)) {
					// Como puede haber mas de un expediente para año/tipo/numero, me quedo con el primero que encuentro
					$expedientes = $this->obtenerExpedientes($e_anio, $e_tipo, $e_numero);
					$exp = (count($expedientes) > 0) ? $expedientes[0] : null;

					// Se audita la eliminación del documento original
					NG::auditorias()->auditarComoExpediente(
						$exp,
						null,
						Auditoria::OP_ARCHIVO_MOVIDO,
						$destino,
						null, null,
						sprintf('Se ha cargado la digitalizacion al directorio final')
					);
				}
			}
		}
	}

	/**
	 * Busca la digitalización de un determinado expediente y devuelve su nombre.
	 * La digitalización sólo debe ser .pdf. Si no la encuentra, devuelve una cadena vacia.
	 * Se la busca como .../proyectos/AAAA/AAENNNNN/AAENNNNN.pdf
	 * @param  [type] $pAnio   [description]
	 * @param  [type] $pTipo   [description]
	 * @param  [type] $pNumero [description]
	 * @return [type]          [description]
	 */
	private function buscarDocumentoDigitalizado($pAnio, $pTipo, $pNumero) {
		$archivo = sprintf('%s%s/%s/%s',
			PATH_KRAKEN_RESOURCES_PROYECTOS,
			$pAnio,
			sprintf("%02d%s%05d", $pAnio % 100, $pTipo, $pNumero),
			sprintf("%02d%s%05d", $pAnio % 100, $pTipo, $pNumero)
		);

		return $this->fileExistsExtension($archivo, array('pdf'));
	}

	/**
	 * Busca la digitalización Reservada de un determinado expediente y devuelve su nombre.
	 * La digitalización sólo debe ser .pdf. Si no la encuentra, devuelve una cadena vacia.
	 * Se la busca como .../proyectos/AAAA/AAENNNNN/reservados/AAENNNNN.pdf
	 * @param  [type] $pAnio   [description]
	 * @param  [type] $pTipo   [description]
	 * @param  [type] $pNumero [description]
	 * @return [type]          [description]
	 */
	private function buscarDocumentoDigitalizadoReservado($pAnio, $pTipo, $pNumero) {
		$archivo = sprintf('%s%s/%s/reservados/%s',
			PATH_KRAKEN_RESOURCES_PROYECTOS,
			$pAnio,
			sprintf("%02d%s%05d", $pAnio % 100, $pTipo, $pNumero),
			sprintf("%02d%s%05d", $pAnio % 100, $pTipo, $pNumero)
		);

		return $this->fileExistsExtension($archivo, array('pdf'));
	}

	/**
	 * Se agrega, es decir, se une la digitalización a la existente al final
	 * @param  [string] $digitalizacion_a_agregar 	Nombre del archivo de la digitalización a agregar a la existente
	 */
	public function agregardigitalizacion($digitalizacion_a_agregar) {

		$resultado = array();

		// Directorio "digital/"
		$directorio_desde = PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES;

		// Se abre el directorio "proyectos/digital/" para tomar la digitalización a agregar
		$directorio_abierto = opendir($directorio_desde);

		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');

		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			$resultado[] = array(
				'archivo' => $digitalizacion_a_agregar,
				'estado' => 'ERROR',
				'mensaje' => 'Error al intentar conectarse o autentificarse en el Servidor FTP.');
		} else {
			// Se toma solo el nombre codificado del archivo, AAENNNNN
			// se convierte a mayúsculas para evitar AAtNNNNN (13/09/2019 XXXX)
			$nombre_codificado = mb_strtoupper(substr($digitalizacion_a_agregar, 0, 8));

			// Se toman los dos primeros dígitos, correspondientes al año
			$anio_corto = substr($digitalizacion_a_agregar, 0, 2);
			// Se completa el año
			$anio_completo = ($anio_corto >= 83) ? $anio_corto + 1900 : $anio_corto + 2000;

			// Directorio donde se va a cargar la unión "proyectos/AAAA/AAENNNNN/"
			$directorio_destino = PATH_KRAKEN_RESOURCES_PROYECTOS . $anio_completo . '/' . $nombre_codificado;

			// Se cambia a ese directorio
			ftp_chdir($id_conexion, $directorio_destino);

			// Se toma el nombre del directorio actual: "proyectos/AAAA/AAENNNNN/"
			$dir_actual = ftp_pwd($id_conexion);

			// Ruta de la digitalización EXISTENTE (proyectos/AAAA/AAENNNNN/AAENNNNN.pdf)
			$ruta_digitalizacion_existente = $dir_actual . "/" . $nombre_codificado . ".pdf";

			// Ruta de la digitalización A AGREGAR al existente (proyectos/digital/AAENNNNN.pdf)
			// aquí su nombre puede contener la letra 'a' o 'A'
			$ruta_digitalizacion_a_agregar = $directorio_desde . $digitalizacion_a_agregar;

			// Ruta de la digitalización DESCARGADA para unirle la agregada
			$ruta_digitalizacion_actual_descargada = $directorio_desde . $nombre_codificado . '_actual.pdf';

			// Ruta del resultado FINAL de la unión
			$ruta_digitalizacion_final = $directorio_desde . $nombre_codificado . '_final.pdf';

			// 1ro:
			// Se descarga la digitalización actual y se guarda en "proyectos/digital/"
			// con el nombre AAENNNNN_actual.pdf
			if (ftp_get($id_conexion, $ruta_digitalizacion_actual_descargada, $ruta_digitalizacion_existente, FTP_BINARY)) {
				// 2do:
				// Se unen las digitalizaciones
				$union_pdfs = $this->unirDigitalizaciones(
					$ruta_digitalizacion_actual_descargada,
					$ruta_digitalizacion_a_agregar,
					$ruta_digitalizacion_final
				);

				// Si se ha generado la unión de las digitalizaciones
				if (!is_null($union_pdfs)) {

					// 16/02/2022 XXXX
					// Se debe capturar el error al intentar eliminar cada archivo por FTP
					// (por ejemplo, surge un error al intentar eliminarlo, si éste se encuentra abierto)

					// Se elimina la digitalización EXISTENTE para cargar la unión de ambas digitalizaciones
					// (en caso de surgir un error al intentar eliminar el archivo)
					if (! ftp_delete($id_conexion, $ruta_digitalizacion_existente)) {
						$resultado[] = array(
							'archivo' => $nombre_codificado.".pdf",
							'estado' => 'ERROR',
							'mensaje' => "Ha fallado la eliminaci&oacute;n de la digitalizaci&oacute;n existente ".$nombre_codificado.".pdf (verifique si se encuentra abierto el archivo en alg&uacute;n equipo, de ser as&iacute; debe cerrarse).");
					} else {
						// Se carga la digitalización NUEVA (con la extensión en minúscula)
						if (ftp_put($id_conexion, $dir_actual . "/" . $nombre_codificado . ".pdf", $union_pdfs, FTP_BINARY)) {

							// Se elimina la digitalización que fue agregada, del directorio
							// "proyectos/digital/AAENNNNN.pdf"
							// (en caso de surgir un error al intentar eliminar el archivo)
							if (! ftp_delete($id_conexion, $ruta_digitalizacion_a_agregar)) {
								$resultado[] = array(
									'archivo' => $digitalizacion_a_agregar,
									'estado' => 'ERROR',
									'mensaje' => "Ha fallado la eliminaci&oacute;n de la digitalizaci&oacute;n a agregar, en proyectos/digital/".$digitalizacion_a_agregar." (verifique si se encuentra abierto el archivo en alg&uacute;n equipo, de ser as&iacute; debe cerrarse).");
							} else {
								// Se elimina la copia que se utilizó para unir con la digitalización agregada
								// la "AAENNNNN_actual.pdf"
								// (en caso de surgir un error al intentar eliminar el archivo)
								if (! ftp_delete($id_conexion, $ruta_digitalizacion_actual_descargada)) {
									$resultado[] = array(
										'archivo' => $nombre_codificado."_actual.pdf",
										'estado' => 'ERROR',
										'mensaje' => "Ha fallado la eliminaci&oacute;n de la copia, que se utiliz&oacute; para unir con la digitalizaci&oacute;n agregada, ".$nombre_codificado."_actual.pdf");
								} else {
									// Se elimina el resultado final
									// la "AAENNNNN_final.pdf"
									// (en caso de surgir un error al intentar eliminar el archivo)
									if (! ftp_delete($id_conexion, $ruta_digitalizacion_final)) {
										$resultado[] = array(
											'archivo' => $nombre_codificado."_final.pdf",
											'estado' => 'ERROR',
											'mensaje' => "Ha fallado la eliminaci&oacute;n del resultado final, ".$nombre_codificado."_final.pdf");
									} else {
										$resultado[] = array(
											'archivo' => $digitalizacion_a_agregar,
											'estado' => 'OK',
											'mensaje' => "Se ha agregado la digitalizaci&oacute;n satisfactoriamente a la existente!");

										$exp = $this->obtenerExpedienteDesdeNombreCodificado($nombre_codificado);

										// Se audita la unión de las digitalizaciones
										NG::auditorias()->auditarComoExpediente(
											$exp,
											null,
											Auditoria::OP_ARCHIVO_AGREGADO,
											PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES,
											null, null,
											sprintf('Se ha agregado la digitalizacion %s', $digitalizacion_a_agregar)
										);
									}
								}
							}
						} else {
							$resultado[] = array(
								'archivo' => $digitalizacion_a_agregar,
								'estado' => 'ERROR',
								'mensaje' => "La uni&oacute;n de las digitalizaciones ha fallado!");
						}
					}
				} else {
					$resultado[] = array(
						'archivo' => $digitalizacion_a_agregar,
						'estado' => 'ERROR',
						'mensaje' => "La uni&oacute;n de las digitalizaciones ha fallado!");
				}
			} else {
				$resultado[] = array(
					'archivo' => $digitalizacion_a_agregar,
					'estado' => 'ERROR',
					'mensaje' => "La descarga de la copia en " . $ruta_digitalizacion_actual_descargada . " ha fallado!");
			}
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		// Se cierra el directorio "digital/"
		closedir($directorio_abierto);

		return $resultado;
	}

	/**
	 * Se unen las digitalizaciones
	 * @param  [string] $digi_existente     Digitalización existente
	 * @param  [string] $digi_a_agregar     Digitalización a agregar a la existente
	 * @param  [string] $archivo_salida 	Nombre codificado, en formato AAENNNNN
	 * @return [string]                     Nombre del archivo de la unificación, o null
	 */
	public function unirDigitalizaciones($digi_existente, $digi_a_agregar, $archivo_salida) {

		// lista de archivos a unir
		$archivos = Array($digi_existente, $digi_a_agregar);

		if (count($archivos) > 1) {
			// Se define el comando para unir las digitalizaciones, utilizando el comando gs (ghostscript)
			$comando = sprintf("gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile='%s' %s",
				$archivo_salida,
				join(array_map(function ($v) {return sprintf("'%s'", $v);}, $archivos), " ")
			);

			// Se ejecuta el comando
			$resultado = shell_exec($comando);

			// Devuelve el nombre del archivo con la unión de las digitalizaciones, o null
			return (is_file($archivo_salida)) ? $archivo_salida : null;
		} else {
			return null;
		}

	}

	/**
	 * Se convierte un documento al formato de la extensión suministrada
	 * @param  string $extension_salida      	Extensión al que se desea convertir el documento, por defecto'.doc'
	 * @param  string $documento_a_convertir 	Documento que se desea convertir
	 * @return string                        	Documento convertido o NULL
	 */
	public function convertirToDoc($extension_salida = 'doc', $documento_a_convertir) {

		// Se define el comando para convertir el documento al formato respectivo
		$comando = sprintf("lowriter --convert-to %s '%s'", $extension_salida, $documento_a_convertir);

		// Se ejecuta el comando
		$resultado = shell_exec($comando);

		if ($resultado != null) {
			// Se obtiene sólo el nombre del documento convertido
			$solo_nombre = pathinfo($documento_a_convertir, PATHINFO_BASENAME);

			// Se le agrega la extensión suministrada
			$documento_salida = $solo_nombre . '.' . $extension_salida;
		}

		// Devuelve el nombre del archivo convertido, o null
		return (is_file($documento_salida)) ? $documento_salida : null;
	}

	/**
	 * Se sobreescribe la digitalización existente
	 * @param  [string] $digitalizacion_a_sobreescribir Nombre del archivo de la digitalización a sobreescribir
	 */
	public function sobreescribirdigitalizacion($digitalizacion_a_sobreescribir) {

		$resultado = array();

		// Directorio "digital/"
		$directorio_desde = PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES;

		// Se abre el directorio "proyectos/digital/" para tomar la digitalización a agregar
		$directorio_abierto = opendir($directorio_desde);

		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');

		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			$resultado[] = array(
				'archivo' => $digitalizacion_a_sobreescribir,
				'estado' => 'ERROR',
				'mensaje' => 'Error al intentar conectarse o autentificarse en el Servidor FTP.');
		} else {
			// Se toma solo el nombre codificado del archivo, AAENNNNN
			// se convierte a mayúsculas para evitar AAtNNNNN (13/09/2019 XXXX)
			$nombre_codificado = mb_strtoupper(substr($digitalizacion_a_sobreescribir, 0, 8));

			// Se toman los dos primeros dígitos, correspondientes al año
			$anio_corto = substr($digitalizacion_a_sobreescribir, 0, 2);
			// Se completa el año
			$anio_completo = ($anio_corto >= 83) ? $anio_corto + 1900 : $anio_corto + 2000;

			// Directorio donde se va a sobreescribir "proyectos/AAAA/AAENNNNN/"
			$directorio_destino = PATH_KRAKEN_RESOURCES_PROYECTOS . $anio_completo . '/' . $nombre_codificado;

			// Se cambia a ese directorio
			ftp_chdir($id_conexion, $directorio_destino);

			// Se toma el nombre del directorio actual: "proyectos/AAAA/AAENNNNN/"
			$dir_actual = ftp_pwd($id_conexion);

			// Ruta de la digitalización EXISTENTE (proyectos/AAAA/AAENNNNN/AAENNNNN.pdf)
			$ruta_digitalizacion_existente = $dir_actual . "/" . $nombre_codificado . '.pdf';

			// Ruta de la digitalización a sobreescribir (proyectos/digital/AAENNNNN.pdf)
			// (puede poseer la letra del Tipo en minúscula o mayúscula)
			$ruta_digitalizacion_nueva = $directorio_desde . $digitalizacion_a_sobreescribir;

			try {
				// Se elimina la digitalización existente, si existe, para cargar la nueva
				if (file_exists($ruta_digitalizacion_existente)) {
					// en caso de surgir un error al intentar eliminar el archivo
					if (! ftp_delete($id_conexion, $ruta_digitalizacion_existente)) {
						$resultado[] = array(
							'archivo' => $nombre_codificado.".pdf",
							'estado' => 'ERROR',
							'mensaje' => "Ha fallado la eliminaci&oacute;n de la digitalizaci&oacute;n existente ".$nombre_codificado.".pdf (verifique si se encuentra abierto el archivo en alg&uacute;n equipo, de ser as&iacute; debe cerrarse).");
					} else {
						// Se carga la nueva digitalización (ya con la extensión en minúscula)
						if (ftp_put($id_conexion, $ruta_digitalizacion_existente, $ruta_digitalizacion_nueva, FTP_BINARY)) {

							// Se elimina la digitalización temporal del directorio "proyectos/digital/" del expediente
							if (file_exists($ruta_digitalizacion_nueva)) {
								// en caso de surgir un error al intentar eliminar el archivo
								if (! ftp_delete($id_conexion, $ruta_digitalizacion_nueva)) {
									$resultado[] = array(
										'archivo' => $nombre_codificado.".pdf",
										'estado' => 'ERROR',
										'mensaje' => "Ha fallado la eliminaci&oacute;n de la nueva digitalizaci&oacute;n, en proyectos/digital/".$digitalizacion_a_sobreescribir." (verifique si se encuentra abierto el archivo en alg&uacute;n equipo, de ser as&iacute; debe cerrarse).");
								} else {
									$resultado[] = array(
										'archivo' => $nombre_codificado . '.pdf',
										'estado' => 'OK',
										'mensaje' => "Se ha sobreescrito la digitalizaci&oacute;n satisfactoriamente!");

									$exp = $this->obtenerExpedienteDesdeNombreCodificado($nombre_codificado);

									// Se audita la sobreescritura de la digitalización
									NG::auditorias()->auditarComoExpediente(
										$exp,
										null,
										Auditoria::OP_ARCHIVO_SOBREESCRITO,
										$digitalizacion_a_sobreescribir,
										null, null,
										sprintf('Se ha sobreescrito la digitalizacion %s', $digitalizacion_a_sobreescribir)
									);
								}
							}
						}
					}
				}
			} catch (Exception $ex) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => 'No se pudo sobreescribir la digitalizacion existente.');
			}
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		// Se cierra el directorio "digital/"
		closedir($directorio_abierto);

		return $resultado;
	}

	/**
	 * Se elimina el documento original, de un expediente determinado
	 * @param  [integer] $pAnio    Año del expediente
	 * @param  [string]  $pTipo    Tipo del expediente
	 * @param  [integer] $pNumero  Número del expediente
	 * @param  [string]  $pArchivo Nombre del archivo del original
	 * @return [boolean]           True|false
	 */
	public function eliminarDocumentoOriginal($pAnio, $pTipo, $pNumero, $pArchivo) {
		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');
		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			return false;
		} else {
			// Se arma el nombre codificado
			$nombre_codificado = sprintf("%02d%s%05d", $pAnio % 100, $pTipo, $pNumero);

			// Directorio del expediente respectivo
			$directorio_proyectos = PATH_KRAKEN_RESOURCES_PROYECTOS . $pAnio . '/' . $nombre_codificado;

			// Se cambia a ese directorio
			ftp_chdir($id_conexion, $directorio_proyectos);
			// Se toma el nombre del directorio actual: "proyectos/AAAA/AATNNNNN/"
			$dir_actual = ftp_pwd($id_conexion);

			// Ruta del documento original (proyectos/AAAA/AATNNNNN/original.doc|.docx|.odt)
			// también puede eliminarse un original que haya sido renombrado como: original_yyyymmdd_hhiiss
			$ruta_documento_a_eliminar = $dir_actual . "/" . $pArchivo;

			// Si existe el documento original
			if (file_exists($ruta_documento_a_eliminar)) {
				// Se elimina
				if (ftp_delete($id_conexion, $ruta_documento_a_eliminar)) {

					// Como puede haber mas de un expediente para año/tipo/numero, me quedo con el primero que encuentro
					$expedientes = $this->obtenerExpedientes($pAnio, $pTipo, $pNumero);
					$exp = (count($expedientes) > 0) ? $expedientes[0] : null;

					// Se audita la eliminación del documento original
					NG::auditorias()->auditarComoExpediente(
						$exp,
						null,
						Auditoria::OP_ARCHIVO_ELIMINADO,
						$directorio_proyectos,
						null, null,
						sprintf('Se ha eliminado el documento original %s', $pArchivo)
					);
				}
			}
		}
		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		return true;
	}

	/**
	 * Se elimina una digitalización temporal, de un expediente determinado
	 * @param  [string]  $pArchivo Nombre de la digitalización temporal
	 * @return [boolean]           True|false
	 */
	public function eliminarDigitalizacionTemporal($pArchivo) {
		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');
		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			return false;
		} else {
			// Se cambia a ese directorio
			ftp_chdir($id_conexion, PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES);
			// Se toma el nombre del directorio actual: "proyectos/digital/"
			$dir_actual = ftp_pwd($id_conexion);

			// Ruta de la digitalización temporal (proyectos/digital/AATNNNNN.pdf|AATNNNNNa.pdf|AATNNNNNA.pdf)
			$ruta_documento_a_eliminar = $dir_actual . "/" . $pArchivo;

			// Si existe el documento original
			if (file_exists($ruta_documento_a_eliminar)) {
				// Se elimina
				if (ftp_delete($id_conexion, $ruta_documento_a_eliminar)) {

					$exp = $this->obtenerExpedienteDesdeNombreCodificado($pArchivo);

					// Se audita la eliminación del documento original
					NG::auditorias()->auditarComoExpediente(
						$exp,
						null,
						Auditoria::OP_ARCHIVO_ELIMINADO,
						PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES,
						null, null,
						sprintf('Se ha eliminado la digitalizacion temporal %s', $pArchivo)
					);
				}
			}
		}
		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		return true;
	}

	/**
	 * Se elimina un documento temporal, de un expediente determinado
	 * @param  [string]  $pArchivo Nombre del documento temporal
	 * @return [boolean]           True|false
	 */
	public function eliminarDocumentoTemporal($pArchivo) {
		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');
		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			return false;
		} else {
			// Se cambia a ese directorio
			ftp_chdir($id_conexion, PATH_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES);
			// Se toma el nombre del directorio actual: "proyectos/temporal/"
			$dir_actual = ftp_pwd($id_conexion);

			// Ruta del documento temporal (proyectos/temporal/AATNNNNN.doc)
			$ruta_documento_a_eliminar = $dir_actual . "/" . $pArchivo;

			// Si existe el documento original
			if (file_exists($ruta_documento_a_eliminar)) {
				// Se elimina
				if (ftp_delete($id_conexion, $ruta_documento_a_eliminar)) {

					$exp = $this->obtenerExpedienteDesdeNombreCodificado($pArchivo);

					// Se audita la eliminación del documento original
					NG::auditorias()->auditarComoExpediente(
						$exp,
						null,
						Auditoria::OP_ARCHIVO_ELIMINADO,
						PATH_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES,
						null, null,
						sprintf('Se ha eliminado el documento temporal %s', $pArchivo)
					);
				}
			}
		}
		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		return true;
	}

	/**
	 * Se elimina la digitalización, de un expediente determinado
	 * @param  [integer] $pAnio    Año del expediente
	 * @param  [string]  $pTipo    Tipo del expediente
	 * @param  [integer] $pNumero  Número del expediente
	 * @param  [string]  $pArchivo Nombre del archivo de digitalizacion
	 * @return [boolean]           True|false
	 */
	public function eliminarDigitalizacion($pAnio, $pTipo, $pNumero, $pArchivo) {
		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');
		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			return false;
		} else {
			// Se arma el nombre codificado
			$nombre_codificado = sprintf("%02d%s%05d", $pAnio % 100, $pTipo, $pNumero);

			// Directorio del expediente respectivo
			$directorio_proyectos = PATH_KRAKEN_RESOURCES_PROYECTOS . $pAnio . '/' . $nombre_codificado;

			// Se cambia a ese directorio
			ftp_chdir($id_conexion, $directorio_proyectos);
			// Se toma el nombre del directorio actual: "proyectos/AAAA/AATNNNNN/"
			$dir_actual = ftp_pwd($id_conexion);

			// Ruta de la digitalización
			$ruta_documento_a_eliminar = $dir_actual . "/" . $pArchivo;

			// Si existe la digitalización
			if (file_exists($ruta_documento_a_eliminar)) {
				// Se elimina
				if (ftp_delete($id_conexion, $ruta_documento_a_eliminar)) {

					// Como puede haber mas de un expediente para año/tipo/numero, me quedo con el primero que encuentro
					$expedientes = $this->obtenerExpedientes($pAnio, $pTipo, $pNumero);
					$exp = (count($expedientes) > 0) ? $expedientes[0] : null;

					// Se audita la eliminación de la digitalización
					NG::auditorias()->auditarComoExpediente(
						$exp,
						null,
						Auditoria::OP_ARCHIVO_ELIMINADO,
						$directorio_proyectos,
						null, null,
						sprintf('Se ha eliminado la digitalizacion %s', $pArchivo)
					);
				}
			}
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		return true;
	}

	/**
	 * [moverDocumentoPublicoToReservado description]
	 * @param  [type] $pAnio    [description]
	 * @param  [type] $pTipo    [description]
	 * @param  [type] $pNumero  [description]
	 * @param  [type] $pArchivo [description]
	 * @return [type]           [description]
	 */
	public function moverDocumentoPublicoToReservado($pAnio, $pTipo, $pNumero, $pArchivo) {
		$resultado = array();

		$permisosDirectorio = 0775;

		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');
		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			return false;
		} else {
			// Se arma el nombre codificado
			$nombre_codificado = sprintf("%02d%s%05d", $pAnio % 100, $pTipo, $pNumero);

			// Directorio de los documentos Reservados del expediente respectivo
			$directorio_del_expediente = PATH_KRAKEN_RESOURCES_PROYECTOS . $pAnio . '/' . $nombre_codificado;

			// Directorio de los documentos Reservados del expediente respectivo
			$directorio_reservados_del_expediente = $directorio_del_expediente . '/reservados';

			// Si no existe el directorio destino
			if (!file_exists($directorio_reservados_del_expediente)) {
				try {
					// Se crea
					$this->crearDirectorioParaExpediente(
						$id_conexion,
						$pAnio . '/' . $nombre_codificado . '/reservados',
						$permisosDirectorio);

				} catch (Exception $ex) {
					$resultado[] = array(
						'archivo' => $a,
						'estado' => 'ERROR',
						'mensaje' => $ex->getMessage());
				}
			} else if (!is_dir($directorio_reservados_del_expediente)) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => 'El directorio destino existe, pero es un archivo.');
			}

			// Si existe el documento reservado
			if (file_exists($directorio_del_expediente . '/' . $pArchivo)) {
				// Se mueve
				if (ftp_rename(
					$id_conexion,
					$directorio_del_expediente . '/' . $pArchivo,
					$directorio_reservados_del_expediente . '/' . $pArchivo)) {

					// Como puede haber mas de un expediente para año/tipo/numero, me quedo con el primero que encuentro
					$expedientes = $this->obtenerExpedientes($pAnio, $pTipo, $pNumero);
					$exp = (count($expedientes) > 0) ? $expedientes[0] : null;

					// Se audita el movimiento del documento al directorio de reservados
					NG::auditorias()->auditarComoExpediente(
						$exp,
						null,
						Auditoria::OP_ARCHIVO_MOVIDO,
						$directorio_reservados_del_expediente,
						null, null,
						sprintf('Se ha movido el documento %s al directorio de reservados', $pArchivo)
					);
				}
			}
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		return true;
	}

	/**
	 * [moverDocumentoReservadoToPublico description]
	 * @param  [type] $pAnio    [description]
	 * @param  [type] $pTipo    [description]
	 * @param  [type] $pNumero  [description]
	 * @param  [type] $pArchivo [description]
	 * @return [type]           [description]
	 */
	public function moverDocumentoReservadoToPublico($pAnio, $pTipo, $pNumero, $pArchivo) {
		$resultado = array();

		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');
		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			return false;
		} else {
			// Se arma el nombre codificado
			$nombre_codificado = sprintf("%02d%s%05d", $pAnio % 100, $pTipo, $pNumero);

			// Directorio de los documentos Reservados del expediente respectivo
			$directorio_del_expediente = PATH_KRAKEN_RESOURCES_PROYECTOS . $pAnio . '/' . $nombre_codificado;

			// Directorio de los documentos Reservados del expediente respectivo
			$directorio_reservados_del_expediente = $directorio_del_expediente . '/reservados';

			// Si existe el documento reservado
			if (file_exists($directorio_reservados_del_expediente . '/' . $pArchivo)) {
				// Se mueve
				if (ftp_rename(
					$id_conexion,
					$directorio_reservados_del_expediente . '/' . $pArchivo,
					$directorio_del_expediente . '/' . $pArchivo)) {

					// Como puede haber mas de un expediente para año/tipo/numero, me quedo con el primero que encuentro
					$expedientes = $this->obtenerExpedientes($pAnio, $pTipo, $pNumero);
					$exp = (count($expedientes) > 0) ? $expedientes[0] : null;

					// Se audita el movimiento del documento al directorio de reservados
					NG::auditorias()->auditarComoExpediente(
						$exp,
						null,
						Auditoria::OP_ARCHIVO_MOVIDO,
						$directorio_del_expediente,
						null, null,
						sprintf('Se ha movido el documento %s al directorio %s', $pArchivo, $nombre_codificado)
					);
				}
			}
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		return true;
	}

	/**
	 * Se elimina un documento reservado, de un expediente determinado
	 * @param  [integer] $pAnio    Año del expediente
	 * @param  [string]  $pTipo    Tipo del expediente
	 * @param  [integer] $pNumero  Número del expediente
	 * @param  [string]  $pArchivo Nombre del documento reservado
	 * @return [boolean]           True|false
	 */
	public function eliminarReservado($pAnio, $pTipo, $pNumero, $pArchivo) {
		$resultado = array();

		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');
		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			return false;
		} else {
			// Se arma el nombre codificado
			$nombre_codificado = sprintf("%02d%s%05d", $pAnio % 100, $pTipo, $pNumero);

			// Directorio de los documentos Reservados del expediente respectivo
			$directorio_reservados = PATH_KRAKEN_RESOURCES_PROYECTOS . $pAnio . '/' . $nombre_codificado . '/reservados';

			// Se cambia a ese directorio
			ftp_chdir($id_conexion, $directorio_reservados);
			// Se toma el nombre del directorio actual: "proyectos/AAAA/AATNNNNN/"
			$dir_actual = ftp_pwd($id_conexion);

			// Ruta del documento reservado
			$ruta_documento_a_eliminar = $dir_actual . "/" . $pArchivo;

			// Si existe el documento reservado
			if (file_exists($ruta_documento_a_eliminar)) {
				// Se elimina
				if (ftp_delete($id_conexion, $ruta_documento_a_eliminar)) {

					// Como puede haber mas de un expediente para año/tipo/numero, me quedo con el primero que encuentro
					$expedientes = $this->obtenerExpedientes($pAnio, $pTipo, $pNumero);
					$exp = (count($expedientes) > 0) ? $expedientes[0] : null;

					// Se audita la eliminación del documento reservado
					NG::auditorias()->auditarComoExpediente(
						$exp,
						null,
						Auditoria::OP_ARCHIVO_ELIMINADO,
						$directorio_reservados,
						null, null,
						sprintf('Se ha eliminado el documento reservado %s', $pArchivo)
					);
				}
			}
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		return true;
	}

	/**
	 * Se sube un documento como reservado
	 * @param  Expediente $pExpediente     Expediente al cual asignar el documento reservado
	 * @param  array    &$filesReference   Referencia al objeto de formulario que contiene la información del archivo.
	 * @return Expediente                  Expediente al cual se asignó el documento reservado
	 */
	public function subirDocumentoReservado(Expediente $pExpediente, &$filesReference) {

		// Si no se recibió un error
		if ($filesReference['f_archivo_reservado']['error'] != UPLOAD_ERR_NO_FILE) {
			try {
				$permisosDirectorio = 0775;
				// Nombre codificado del directorio del expediente respectivo
				$nombre_codificado = sprintf("%02d%s%05d", $pExpediente->anio % 100, $pExpediente->tipo, $pExpediente->numero);

				// Ruta del directorio remoto de Reservados, determinado por la clave del expediente respectivo
				$directorio_reservados = PATH_KRAKEN_RESOURCES_PROYECTOS . $pExpediente->anio . "/" . $nombre_codificado . "/reservados/";

				// Nombre del documento reservado
				$nombre_reservado = $filesReference['f_archivo_reservado']['name'];

				// Se establece una conexión FTP
				$id_conexion = ftp_connect('localhost');

				// Se establece el inicio de sesión FTP con usuario y password
				$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

				// Si no existe el directorio destino
				if (!file_exists($directorio_reservados)) {
					try {
						// Se crea
						$this->crearDirectorioParaExpediente(
							$id_conexion,
							$pExpediente->anio . '/' . $nombre_codificado . '/reservados',
							$permisosDirectorio);

					} catch (Exception $ex) {
						$resultado[] = array(
							'archivo' => $a,
							'estado' => 'ERROR',
							'mensaje' => $ex->getMessage());
					}
				} else if (!is_dir($directorio_reservados)) {
					$resultado[] = array(
						'archivo' => $a,
						'estado' => 'ERROR',
						'mensaje' => 'El directorio destino existe, pero es un archivo.');
				}

				// Se cambia al directorio donde se quiere subir el archivo
				ftp_chdir($id_conexion, $directorio_reservados);

				$dir_actual = ftp_pwd($id_conexion);

				$archivoOrigen = $filesReference['f_archivo_reservado']['tmp_name'];
				$archivoDestino = sprintf('%s%s', $directorio_reservados, $nombre_reservado);

				if (ftp_put($id_conexion, $archivoDestino, $archivoOrigen, FTP_BINARY)) {
					// Auditoria
					NG::auditorias()->auditarComoExpediente(
						$pExpediente,
						null,
						Auditoria::OP_ARCHIVO_SUBIDO,
						sprintf('%s%s', $directorio_reservados, $nombre_reservado), // en vez de tabla, utilizamos la ruta donde se guarda el archivo
						null, null,
						sprintf('Se ha subido el archivo \'%s\' como Reservado.', $nombre_reservado)
					);
				}

				ftp_close($id_conexion);

			} catch (Exception $ex) {
				throw new Exception('No se pudo cargar el documento. Causa: ' . $ex->getMessage());
			}
		}

		return $pExpediente;
	}

	/**
	 * Se sube un documento público
	 * @param  Expediente $pExpediente     Expediente al cual asignar el documento público
	 * @param  array    &$filesReference   Referencia al objeto de formulario que contiene la información del archivo.
	 * @return Expediente                  Expediente al cual se asignó el documento público
	 */
	public function subirDocumentoPublico(Expediente $pExpediente, &$filesReference) {

		// Si no se recibió un error
		if ($filesReference['f_archivo_publico']['error'] != UPLOAD_ERR_NO_FILE) {
			try {
				// Nombre codificado del directorio del expediente respectivo
				$nombre_codificado = sprintf("%02d%s%05d", $pExpediente->anio % 100, $pExpediente->tipo, $pExpediente->numero);

				// Ruta del directorio remoto de públicos, determinado por la clave del expediente respectivo
				$directorio_expediente = PATH_KRAKEN_RESOURCES_PROYECTOS . $pExpediente->anio . "/" . $nombre_codificado . "/";

				// Nombre del documento público
				$nombre_publico = $filesReference['f_archivo_publico']['name'];

				// Se establece una conexión FTP
				$id_conexion = ftp_connect('localhost');

				// Se establece el inicio de sesión FTP con usuario y password
				$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

				// Se cambia al directorio donde se quiere subir el archivo
				ftp_chdir($id_conexion, $directorio_expediente);

				$dir_actual = ftp_pwd($id_conexion);

				$archivoOrigen = $filesReference['f_archivo_publico']['tmp_name'];
				$archivoDestino = sprintf('%s%s', $directorio_expediente, $nombre_publico);

				if (ftp_put($id_conexion, $archivoDestino, $archivoOrigen, FTP_BINARY)) {
					// Auditoria
					NG::auditorias()->auditarComoExpediente(
						$pExpediente,
						null,
						Auditoria::OP_ARCHIVO_SUBIDO,
						sprintf('%s%s', $directorio_expediente, $nombre_publico), // en vez de tabla, utilizamos la ruta donde se guarda el archivo
						null, null,
						sprintf('Se ha subido el archivo \'%s\' como P&uacute;blico.', $nombre_publico)
					);
				}

				ftp_close($id_conexion);

			} catch (Exception $exUpload) {
				throw new Exception('No se pudo cargar el documento. Causa: ' . $exUpload->getMessage());
			}
		}

		return $pExpediente;
	}

	/**
	 * Se sube un documento como auxiliar, en el mismo directorio del expediente respectivo
	 * @param  Expediente $pExpediente     Expediente al cual asignar el documento
	 * @param  array    &$filesReference   Referencia al objeto de formulario que contiene la información del archivo.
	 * @return Expediente                  Expediente al cual se asignó el documento
	 */
	public function subirDocumentoAuxiliar(Expediente $pExpediente, &$filesReference) {

		// Si no se recibió un error
		if ($filesReference['f_archivo_publico']['error'] != UPLOAD_ERR_NO_FILE) {
			try {
				// Directorio "temporal/"
				$directorio_temporal = PATH_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES;

				// Nombre del documento auxiliar
				$nombre_auxiliar = "temp_" . $filesReference['f_archivo_publico']['name'];

				// Se establece una conexión FTP
				$id_conexion = ftp_connect('localhost');

				// Se establece el inicio de sesión FTP con usuario y password
				$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

				// Se cambia al directorio donde se quiere subir el archivo
				ftp_chdir($id_conexion, $directorio_temporal);

				$dir_actual = ftp_pwd($id_conexion);

				$archivoOrigen = $filesReference['f_archivo_publico']['tmp_name'];
				$archivoDestino = sprintf('%s%s', $directorio_temporal, $nombre_auxiliar);

				ftp_put($id_conexion, $archivoDestino, $archivoOrigen, FTP_BINARY);

				ftp_close($id_conexion);

			} catch (Exception $exUpload) {
				throw new Exception('No se pudo cargar el documento. Causa: ' . $exUpload->getMessage());
			}
		}

		return $pExpediente;
	}

	/**
	 * Se sube un documento reservado como auxiliar
	 * @param  Expediente $pExpediente     Expediente al cual asignar el documento reservado
	 * @param  array    &$filesReference   Referencia al objeto de formulario que contiene la información del archivo.
	 * @return Expediente                  Expediente al cual se asignó el documento reservado
	 */
	public function subirDocumentoReservadoAuxiliar(Expediente $pExpediente, &$filesReference) {

		// Si no se recibió un error
		if ($filesReference['f_archivo_reservado']['error'] != UPLOAD_ERR_NO_FILE) {
			try {
				// Directorio "temporal/"
				$directorio_temporal = PATH_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES;

				// Nombre del documento auxiliar
				$nombre_auxiliar = "temp_" . $filesReference['f_archivo_reservado']['name'];

				// Se establece una conexión FTP
				$id_conexion = ftp_connect('localhost');

				// Se establece el inicio de sesión FTP con usuario y password
				$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

				// Se cambia al directorio donde se quiere subir el archivo
				ftp_chdir($id_conexion, $directorio_temporal);

				$dir_actual = ftp_pwd($id_conexion);

				$archivoOrigen = $filesReference['f_archivo_reservado']['tmp_name'];
				$archivoDestino = sprintf('%s%s', $directorio_temporal, $nombre_auxiliar);

				ftp_put($id_conexion, $archivoDestino, $archivoOrigen, FTP_BINARY);

				ftp_close($id_conexion);

			} catch (Exception $exUpload) {
				throw new Exception('No se pudo cargar el documento. Causa: ' . $exUpload->getMessage());
			}
		}

		return $pExpediente;
	}

	/**
	 * Se sobreescribe el Documento Público
	 * @param  integer $f_anio              Año del expediente
	 * @param  string  $f_tipo              Tipo del expediente
	 * @param  integer $f_numero           	Número del expediente
	 * @param  string  $documento_existente Nombre del documento
	 * @return boolean
	 */
	public function sobreescribirDocPublico($f_anio, $f_tipo, $f_numero, $documento_existente) {

		$resultado = array();

		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');
		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			$resultado[] = array(
				'archivo' => $documento_existente,
				'estado' => 'ERROR',
				'mensaje' => 'Error al intentar conectarse o autentificarse en el Servidor FTP.');
		} else {
			// Se arma el nombre codificado
			$nombre_codificado = sprintf("%02d%s%05d", $f_anio % 100, $f_tipo, $f_numero);
			// Ruta del directorio del expediente
			$directorio_del_expediente = PATH_KRAKEN_RESOURCES_PROYECTOS . $f_anio . "/" . $nombre_codificado;
			// Directorio "temporal/"
			$directorio_temporal = PATH_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES;

			// Se cambia a ese directorio
			ftp_chdir($id_conexion, $directorio_del_expediente);
			// Se toma el nombre del directorio actual: "proyectos/AAAA/AATNNNNN/"
			$dir_actual = ftp_pwd($id_conexion);

			// Ruta del documento EXISTENTE = proyectos/AAAA/AATNNNNN/documento_a_pisar
			$ruta_documento_existente = $dir_actual . "/" . $documento_existente;

			// Ruta del documento a usar para sobreescribir
			$ruta_documento_nuevo = $directorio_temporal . '/temp_' . $documento_existente;

			try {
				// Si existe el documento a sobreescribir
				if (file_exists($ruta_documento_existente)) {

					// Se elimina el documento existente
					if (! ftp_delete($id_conexion, $ruta_documento_existente)) {
						$resultado[] = array(
							'archivo' => $documento_existente,
							'estado' => 'ERROR',
							'mensaje' => 'No se pudo eliminar el documento p&uacute;blico existente (verifique que no se encuentre abierto en alg&uacute;n equipo).');
					} else {
						// Se carga el nuevo documento
						if (ftp_put($id_conexion, $ruta_documento_existente, $ruta_documento_nuevo, FTP_BINARY)) {

							// Se elimina el documento temporal del directorio "proyectos/temporal/"
							if (file_exists($ruta_documento_nuevo)) {
								if (! ftp_delete($id_conexion, $ruta_documento_nuevo)) {
									$resultado[] = array(
										'archivo' => $documento_existente,
										'estado' => 'ERROR',
										'mensaje' => 'No se pudo eliminar el documento p&uacute;blico temporal, utilizado para la sobreescritura, verifique que no se encuentre abierto en alg&uacute;n equipo.');
								} else {
									// Como puede haber más de un expediente para año/tipo/numero, nos quedamos con el primero que encuentro
									$expedientes = $this->obtenerExpedientes($f_anio, $f_tipo, $f_numero);
									$exp = (count($expedientes) > 0) ? $expedientes[0] : null;

									// Se audita el movimiento del documento al directorio de reservados
									NG::auditorias()->auditarComoExpediente(
										$exp,
										null,
										Auditoria::OP_ARCHIVO_SOBREESCRITO,
										$directorio_del_expediente,
										null, null,
										sprintf('Se ha sobreescrito el documento %s al directorio %s', $documento_existente, $nombre_codificado)
									);

									$resultado[] = array(
										'archivo' => $documento_existente,
										'estado' => 'OK',
										'mensaje' => "Se ha sobreescrito el documento satisfactoriamente!");
								}
							}
						}
					}
				} else {
					$resultado[] = array(
						'archivo' => $a,
						'estado' => 'ERROR',
						'mensaje' => 'No se pudo sobreescribir el documento existente.');
				}
			} catch (Exception $ex) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => 'No se pudo sobreescribir el documento existente.');
			}
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		// Se elimina el temporal que quedó
		$this->eliminarDocumentoAuxiliar($f_anio, $f_tipo, $f_numero, $documento_existente);

		return $resultado;
	}

	/**
	 * Se sobreescribe el Documento Reservado
	 * @param  integer $f_anio              Año del expediente
	 * @param  string  $f_tipo              Tipo del expediente
	 * @param  integer $f_numero           	Número del expediente
	 * @param  string  $documento_existente Nombre del documento
	 * @return boolean
	 */
	public function sobreescribirDocReservado($f_anio, $f_tipo, $f_numero, $documento_existente) {

		$resultado = array();

		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');
		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			$resultado[] = array(
				'archivo' => $documento_existente,
				'estado' => 'ERROR',
				'mensaje' => 'Error al intentar conectarse o autentificarse en el Servidor FTP.');
		} else {
			// Se arma el nombre codificado
			$nombre_codificado = sprintf("%02d%s%05d", $f_anio % 100, $f_tipo, $f_numero);
			// Ruta del directorio del expediente
			$directorio_del_expediente = PATH_KRAKEN_RESOURCES_PROYECTOS . $f_anio . "/" . $nombre_codificado;
			// Directorio "temporal/"
			$directorio_temporal = PATH_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES;

			// Se cambia al directorio de Reservados del expediente
			ftp_chdir($id_conexion, $directorio_del_expediente . "/reservados");
			// Se toma el nombre del directorio actual: "proyectos/AAAA/AATNNNNN/"
			$dir_actual = ftp_pwd($id_conexion);

			// Ruta del documento EXISTENTE = proyectos/AAAA/AATNNNNN/documento_a_pisar
			$ruta_documento_existente = $dir_actual . "/" . $documento_existente;

			// Ruta del documento a usar para sobreescribir
			$ruta_documento_nuevo = $directorio_temporal . '/temp_' . $documento_existente;

			try {
				// Si existe el documento reservado a sobreescribir
				if (file_exists($ruta_documento_existente)) {

					// Se elimina el documento reservado existente
					if (! ftp_delete($id_conexion, $ruta_documento_existente)) {
						$resultado[] = array(
							'archivo' => $documento_existente,
							'estado' => 'ERROR',
							'mensaje' => 'No se pudo eliminar el documento reservado existente (verifique que no se encuentre abierto en alg&uacute;n equipo).');
					} else {
						// Se carga el nuevo documento
						if (ftp_put($id_conexion, $ruta_documento_existente, $ruta_documento_nuevo, FTP_BINARY)) {

							// Se elimina el documento temporal del directorio "proyectos/temporal/"
							if (file_exists($ruta_documento_nuevo)) {
								if (! ftp_delete($id_conexion, $ruta_documento_nuevo)) {
									$resultado[] = array(
										'archivo' => $documento_existente,
										'estado' => 'ERROR',
										'mensaje' => 'No se pudo eliminar el documento reservado temporal, utilizado para la sobreescritura, verifique que no se encuentre abierto en alg&uacute;n equipo.');
								} else {
									// Como puede haber mas de un expediente para año/tipo/numero, me quedo con el primero que encuentro
									$expedientes = $this->obtenerExpedientes($f_anio, $f_tipo, $f_numero);
									$exp = (count($expedientes) > 0) ? $expedientes[0] : null;

									// Se audita la sobreescritura del documento reservado al directorio de reservados respectivo
									NG::auditorias()->auditarComoExpediente(
										$exp,
										null,
										Auditoria::OP_ARCHIVO_SOBREESCRITO,
										$directorio_del_expediente,
										null, null,
										sprintf('Se ha sobreescrito el documento reservado %s al directorio %s', $documento_existente, $nombre_codificado)
									);

									$resultado[] = array(
										'archivo' => $documento_existente,
										'estado' => 'OK',
										'mensaje' => "Se ha sobreescrito el documento reservado satisfactoriamente!");
								}
							}
						}
					}
				} else {
					$resultado[] = array(
						'archivo' => $a,
						'estado' => 'ERROR',
						'mensaje' => 'No se pudo sobreescribir el documento reservado existente.');
				}

			} catch (Exception $ex) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => 'No se pudo sobreescribir el documento reservado existente.');
			}
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		// Se elimina el temporal que quedó
		$this->eliminarDocumentoAuxiliar($f_anio, $f_tipo, $f_numero, $documento_existente);

		return $resultado;
	}

	/**
	 * Se elimina un documento auxiliar, utilizado para una sobreescritura previa
	 * @param  [integer] $pAnio    Año del expediente
	 * @param  [string]  $pTipo    Tipo del expediente
	 * @param  [integer] $pNumero  Número del expediente
	 * @param  [string]  $pArchivo Nombre del documento auxiliar
	 * @return [boolean]           True|false
	 */
	public function eliminarDocumentoAuxiliar($pAnio, $pTipo, $pNumero, $pArchivo) {
		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');
		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			return false;
		} else {
			// Directorio "temporal/"
			$directorio_temporal = PATH_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES;

			// Se cambia a ese directorio
			ftp_chdir($id_conexion, $directorio_temporal);

			// Se toma el nombre del directorio actual: "proyectos/temporal/"
			$dir_actual = ftp_pwd($id_conexion);

			// Ruta del auxiliar
			$ruta_documento_a_eliminar = $dir_actual . "/temp_" . $pArchivo;

			// Si existe el documento temporal
			if (file_exists($ruta_documento_a_eliminar)) {
				// Se elimina
				if (! ftp_delete($id_conexion, $ruta_documento_a_eliminar)) {
					return false;
				}
			}
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		return true;
	}

	/**
	 * Se agrega, es decir, se une un documento al existente al final
	 * @param  integer $f_anio              Año del expediente
	 * @param  string  $f_tipo              Tipo del expediente
	 * @param  integer $f_numero           	Número del expediente
	 * @param  string  $documento_a_agregar Nombre del archivo a agregar al existente
	 */
	public function agregarDocumentoPublicoPDF($f_anio, $f_tipo, $f_numero, $documento_a_agregar) {

		$resultado = array();

		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');

		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			$resultado[] = array(
				'archivo' => $documento_a_agregar,
				'estado' => 'ERROR',
				'mensaje' => 'Error al intentar conectarse o autentificarse en el Servidor FTP.');
		} else {
			// Se arma el nombre codificado
			$nombre_codificado = sprintf("%02d%s%05d", $f_anio % 100, $f_tipo, $f_numero);
			// Ruta del directorio del expediente
			$directorio_del_expediente = PATH_KRAKEN_RESOURCES_PROYECTOS . $f_anio . "/" . $nombre_codificado;

			// Directorio "digital/"
			$directorio_desde = PATH_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES;

			// Se abre el directorio "proyectos/digital/" para tomar la digitalización a agregar
			$directorio_abierto = opendir($directorio_desde);

			// Se cambia a ese directorio
			ftp_chdir($id_conexion, $directorio_del_expediente);

			// Se toma el nombre del directorio actual: "proyectos/AAAA/AATNNNNN/"
			$dir_actual = ftp_pwd($id_conexion);

			// Ruta del documento EXISTENTE (proyectos/AAAA/AATNNNNN/eldocumento.pdf)
			$ruta_documento_existente = $dir_actual . "/" . $documento_a_agregar;

			// Ruta del documento A AGREGAR al existente (proyectos/AAAA/AATNNNNN/temp_eldocumento.pdf)
			$ruta_documento_a_agregar = $directorio_desde . "temp_" . $documento_a_agregar;

			// Ruta del documento DESCARGADO (a la que se le une lo nuevo)
			$ruta_documento_actual_descargado = $directorio_desde . $documento_a_agregar . '_actual.pdf';

			// Ruta del resultado FINAL de la unión
			$ruta_documento_final = $directorio_desde . $documento_a_agregar . '_final.pdf';

			// Se descarga el documento actual
			if (ftp_get($id_conexion, $ruta_documento_actual_descargado, $ruta_documento_existente, FTP_BINARY)) {
				// Se unen los documentos
				$union_pdfs = $this->unirDigitalizaciones(
					$ruta_documento_actual_descargado,
					$ruta_documento_a_agregar,
					$ruta_documento_final
				);

				// Si se ha generado la unión de los documentos
				if (!is_null($union_pdfs)) {

					// Se elimina el documento EXISTENTE para cargar la unión de ambos documentos
					if (! ftp_delete($id_conexion, $ruta_documento_existente)) {
						$resultado[] = array(
							'archivo' => $documento_a_agregar,
							'estado' => 'ERROR',
							'mensaje' => "No se pudo eliminar el documento p&uacute;blico existente, verifique que no se encuentre abierto en alg&uacute;n equipo.");
					} else {
						// Se carga el documento NUEVO
						if (ftp_put($id_conexion, $dir_actual . "/" . $documento_a_agregar, $union_pdfs, FTP_BINARY)) {

							// Se elimina el documento que fue agregado
							if (! ftp_delete($id_conexion, $ruta_documento_a_agregar)) {
								$resultado[] = array(
									'archivo' => $documento_a_agregar,
									'estado' => 'ERROR',
									'mensaje' => "No se pudo eliminar el documento a agregar, verifique que no se encuentre abierto en alg&uacute;n equipo.");
							} else {
								// Se elimina la copia que se utilizó para unir con el documento agregado
								if (! ftp_delete($id_conexion, $ruta_documento_actual_descargado)) {
									$resultado[] = array(
										'archivo' => $documento_a_agregar,
										'estado' => 'ERROR',
										'mensaje' => "No se pudo eliminar la copia del documento.");
								} else {
									// Se elimina el resultado final (el temporal creado)
									if (! ftp_delete($id_conexion, $ruta_documento_final)) {
										$resultado[] = array(
											'archivo' => $documento_a_agregar,
											'estado' => 'ERROR',
											'mensaje' => "No se pudo eliminar el documento final.");
									} else {

										$resultado[] = array(
											'archivo' => $documento_a_agregar,
											'estado' => 'OK',
											'mensaje' => "Se ha agregado el documento satisfactoriamente al existente!");

										// Como puede haber mas de un expediente para año/tipo/numero, me quedo con el primero que encuentro
										$expedientes = $this->obtenerExpedientes($f_anio, $f_tipo, $f_numero);
										$exp = (count($expedientes) > 0) ? $expedientes[0] : null;

										// Se audita la unión de los documentos
										NG::auditorias()->auditarComoExpediente(
											$exp,
											null,
											Auditoria::OP_ARCHIVO_AGREGADO,
											$directorio_del_expediente,
											null, null,
											sprintf('Se ha agregado el documento %s', $documento_a_agregar)
										);
									}
								}
							}
						} else {
							$resultado[] = array(
								'archivo' => $documento_a_agregar,
								'estado' => 'ERROR',
								'mensaje' => "La uni&oacute;n de los documentos ha fallado!");
						}
					}
				} else {
					$resultado[] = array(
						'archivo' => $documento_a_agregar,
						'estado' => 'ERROR',
						'mensaje' => "La uni&oacute;n de los documentos ha fallado!");
				}
			} else {
				$resultado[] = array(
					'archivo' => $documento_a_agregar,
					'estado' => 'ERROR',
					'mensaje' => "La descarga de la copia en " . $ruta_documento_actual_descargado . " ha fallado!");
			}
			// Se cierra el directorio codificado del expediente
			closedir($directorio_abierto);
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		return $resultado;
	}

	/**
	 * Se agrega, es decir, se une un documento reservado al existente al final
	 * @param  integer $f_anio              Año del expediente
	 * @param  string  $f_tipo              Tipo del expediente
	 * @param  integer $f_numero           	Número del expediente
	 * @param  string  $documento_a_agregar Nombre del archivo a agregar al existente
	 */
	public function agregarDocumentoReservadoPDF($f_anio, $f_tipo, $f_numero, $documento_a_agregar) {

		$resultado = array();

		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');

		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			$resultado[] = array(
				'archivo' => $documento_a_agregar,
				'estado' => 'ERROR',
				'mensaje' => 'Error al intentar conectarse o autentificarse en el Servidor FTP.');
		} else {
			// Se arma el nombre codificado
			$nombre_codificado = sprintf("%02d%s%05d", $f_anio % 100, $f_tipo, $f_numero);
			// Ruta del directorio del expediente
			$directorio_del_expediente = PATH_KRAKEN_RESOURCES_PROYECTOS . $f_anio . "/" . $nombre_codificado;

			// Directorio "temporal/"
			$directorio_desde = PATH_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES;

			// Se abre el directorio "proyectos/temporal/" para tomar la digitalización a agregar
			$directorio_abierto = opendir($directorio_desde);

			// Se cambia a ese directorio
			ftp_chdir($id_conexion, $directorio_del_expediente . '/reservados');

			// Se toma el nombre del directorio actual: "proyectos/AAAA/AAENNNNN/reservados"
			$dir_actual = ftp_pwd($id_conexion);

			// Ruta del documento EXISTENTE (proyectos/AAAA/AAENNNNN/reservados/eldocumento.pdf)
			$ruta_documento_existente = $dir_actual . "/" . $documento_a_agregar;

			// Ruta del documento A AGREGAR al existente (proyectos/AAAA/AAENNNNN/temp_eldocumento.pdf)
			$ruta_documento_a_agregar = $directorio_desde . "temp_" . $documento_a_agregar;

			// Ruta del documento DESCARGADO (a la que se le une lo nuevo)
			$ruta_documento_actual_descargado = $directorio_desde . $documento_a_agregar . '_actual.pdf';

			// Ruta del resultado FINAL de la unión
			$ruta_documento_final = $directorio_desde . $documento_a_agregar . '_final.pdf';

			// Se descarga el documento actual
			if (ftp_get($id_conexion, $ruta_documento_actual_descargado, $ruta_documento_existente, FTP_BINARY)) {
				// Se unen los documentos
				$union_pdfs = $this->unirDigitalizaciones(
					$ruta_documento_actual_descargado,
					$ruta_documento_a_agregar,
					$ruta_documento_final
				);

				// Si se ha generado la unión de los documentos
				if (!is_null($union_pdfs)) {

					// Se elimina el documento EXISTENTE para cargar la unión de ambos documentos
					if (! ftp_delete($id_conexion, $ruta_documento_existente)) {
						$resultado[] = array(
							'archivo' => $documento_a_agregar,
							'estado' => 'ERROR',
							'mensaje' => "No se pudo eliminar el documento reservado existente, verifique que no se encuentre abierto en alg&uacute;n equipo.");
					} else {
						// Se carga el documento NUEVO
						if (ftp_put($id_conexion, $dir_actual . "/" . $documento_a_agregar, $union_pdfs, FTP_BINARY)) {

							// Se elimina el documento que fue agregado
							if (! ftp_delete($id_conexion, $ruta_documento_a_agregar)) {
								$resultado[] = array(
									'archivo' => $documento_a_agregar,
									'estado' => 'ERROR',
									'mensaje' => "No se pudo eliminar el documento reservado que fue agregado, verifique que no se encuentre abierto en alg&uacute;n equipo.");
							} else {
								// Se elimina la copia que se utilizó para unir con el documento agregado
								if (! ftp_delete($id_conexion, $ruta_documento_actual_descargado)) {
									$resultado[] = array(
										'archivo' => $documento_a_agregar,
										'estado' => 'ERROR',
										'mensaje' => "No se pudo eliminar el documento reservado que fue agregado, verifique que no se encuentre abierto en alg&uacute;n equipo.");
								} else {
									// Se elimina el resultado final (el temporal creado)
									if (! ftp_delete($id_conexion, $ruta_documento_final)) {
										$resultado[] = array(
											'archivo' => $documento_a_agregar,
											'estado' => 'ERROR',
											'mensaje' => "No se pudo eliminar el documento reservado final, que fue creado.");
									} else {
										$resultado[] = array(
											'archivo' => $documento_a_agregar,
											'estado' => 'OK',
											'mensaje' => "Se ha agregado el documento reservado satisfactoriamente al existente!");

										// Como puede haber mas de un expediente para año/tipo/numero, me quedo con el primero que encuentro
										$expedientes = $this->obtenerExpedientes($f_anio, $f_tipo, $f_numero);
										$exp = (count($expedientes) > 0) ? $expedientes[0] : null;

										// Se audita la unión de los documentos
										NG::auditorias()->auditarComoExpediente(
											$exp,
											null,
											Auditoria::OP_ARCHIVO_AGREGADO,
											$directorio_del_expediente,
											null, null,
											sprintf('Se ha agregado el documento reservado %s al existente', $documento_a_agregar)
										);
									}
								}
							}
						} else {
							$resultado[] = array(
								'archivo' => $documento_a_agregar,
								'estado' => 'ERROR',
								'mensaje' => "La uni&oacute;n de los documentos ha fallado!");
						}
					}
				} else {
					$resultado[] = array(
						'archivo' => $documento_a_agregar,
						'estado' => 'ERROR',
						'mensaje' => "La uni&oacute;n de los documentos ha fallado!");
				}
			} else {
				$resultado[] = array(
					'archivo' => $documento_a_agregar,
					'estado' => 'ERROR',
					'mensaje' => "La descarga de la copia en " . $ruta_documento_actual_descargado . " ha fallado!");
			}
			// Se cierra el directorio codificado del expediente
			closedir($directorio_abierto);
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		return $resultado;
	}

	/**
	 * Obtiene el conjunto de digitalizaciones Reservadas que están disponibles "para cargar".
	 * @return array   $archivos 	Array asociativo con las digitalizaciones del expediente.
	 * Cada elemento del array posee "tipo", "archivo", "ruta_completa", "url" y "expediente"
	 */
	public function obtenerDigitalizacionesReservadasACargar() {
		$archivos = array();

		// Obtengo los archivos del directorio
		$dirContent = scandir(PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES);

		// Expresion regular para considerar solamente aquellos archivos que cumplen con la nomenclatura.
		$archivosPermitidos = preg_grep('/^[0-9]{2,2}(e|E|n|N)[0-9]{5,5}([dD]{1,1})\.(pdf|PDF)$/', $dirContent);

		$expData = array();
		foreach ($archivosPermitidos as $a) {
			// Obtengo el expediente asociado
			preg_match('/^([0-9]{2,2})(e|E|n|N)([0-9]{5,5})([dD]{1,1})\.(pdf|PDF)$/', $a, $expData);
			$e_anio = ($expData[1] >= 83) ? $expData[1] + 1900 : $expData[1] + 2000;
			$e_tipo = $expData[2];
			$e_numero = $expData[3];

			// Como puede haber mas de un expediente para año/tipo/numero, me quedo con el primero que encuentro
			$expedientes = $this->obtenerExpedientes($e_anio, $e_tipo, $e_numero);
			$exp = (count($expedientes) > 0) ? $expedientes[0] : null;

			if ($exp != null) {
				$archivos[] = array(
					'tipo' => 'temporal',
					'archivo' => $a,
					'ruta_completa' => PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES . $a,
					'url' => URL_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES . $a,
					'expediente' => $exp);
			}
		}

		return $archivos;
	}

	/**
	 * Se carga un conjunto de Digitalizaciones en las carpetas correspondiente a cada expediente.
	 * Crea los directorios destino si estos no existiesen.
	 * @param  array  $archivos      Array con los nombres de archivo a mover.
	 * @return array                 Status de resultado de movimiento.
	 */
	public function cargarDigitalizacionesReservadas($archivos) {

		$resultado = array();
		$archivosAuditables = array();

		// Impersonalizo las acciones contra el FileSystem utilizando una conexion FTP local,
		// evitando de esta manera que el usuario de Apache sea quien crea archivos y directorios.
		FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		$permisosDirectorio = 0775; // sudo find . -type d -exec chmod 775 {} +
		$permisosArchivo = 0664; // sudo find . -type f -exec chmod 664 {} +

		// Expresion regular para considerar solamente aquellos archivos que cumplen con la nomenclatura.
		$archivosPermitidos = preg_grep('/^[0-9]{2,2}(e|E|n|N)[0-9]{5,5}([dD]{1,1})\.(pdf|PDF)$/', $archivos);

		foreach ($archivosPermitidos as $a) {
			// Obtengo el expediente asociado
			preg_match('/^([0-9]{2,2})(e|E|n|N)([0-9]{5,5})([dD]{1,1})\.(pdf|PDF)$/', $a, $expData);

			$e_anio = ($expData[1] >= 83) ? $expData[1] + 1900 : $expData[1] + 2000;
			$e_tipo = mb_strtoupper($expData[2]);
			$e_numero = $expData[3];

			$nombreCodificado = sprintf("%02d%s%05d", $e_anio % 100, $e_tipo, $e_numero);
			$directorioDestino = PATH_KRAKEN_RESOURCES_PROYECTOS . $e_anio . '/' . $nombreCodificado . '/reservados';
			$archivoDestino = $directorioDestino . '/' . $nombreCodificado . '.pdf';

			// Verifico la existencia del archivo
			$archivoOrigen = PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES . $a;
			if (!file_exists($archivoOrigen)) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => 'El documento no existe.');
				continue; // Salteo esta vuelta del foreach y continuo con el archivo siguiente.
			}

			// Verifico la existencia del expediente
			$expedientes = $this->obtenerExpedientes($e_anio, $e_tipo, $e_numero);
			if (count($expedientes) == 0) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => sprintf('El expediente %d-%s-%d no existe.', $e_anio, $e_tipo, $e_numero));
				continue; // Salteo esta vuelta del foreach y continuo con el archivo siguiente.
			}

			// Verifico que no exista el destino (dependiendo del flag $forzarDestino)
			$archivoExistente = $this->buscarDocumentoDigitalizadoReservado($e_anio, $e_tipo, $e_numero);

			// Si ya existe
			if ($archivoExistente != '') {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'WARNING',
					'mensaje' => sprintf('La digitalizacion reservada ya existe para el expediente %d-%s-%d.', $e_anio, $e_tipo, $e_numero));
				continue; // Salteo esta vuelta del foreach y continuo con el archivo siguiente.
			}

			// Si no existe el directorio destino, lo creo (recursivamente)
			if (!file_exists($directorioDestino)) {
				try {
					FTPHelper::get()->mkDirProyecto($e_anio . '/' . $nombreCodificado . '/reservados', $permisosDirectorio);
				} catch (Exception $ex) {
					$resultado[] = array(
						'archivo' => $a,
						'estado' => 'ERROR',
						'mensaje' => $ex->getMessage());
					continue; // Salteo esta vuelta del foreach y continuo con el archivo siguiente.
				}
			} else if (!is_dir($directorioDestino)) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => 'El directorio destino existe, pero es un archivo.');
				continue; // Salteo esta vuelta del foreach y continuo con el archivo siguiente.
			}

			// Intento mover el archivo
			try {
				// Se mueve el archivo de la digitalización sin otorgarle permisos
				$this->moverDigitalizacion($archivoOrigen, $archivoDestino, $e_anio, $e_tipo, $e_numero);

				$mensajeFinal = sprintf('La digitalizacion reservada %s fue asignada con &eacute;xito al expediente %d-%s-%d.', $a, $e_anio, $e_tipo, $e_numero);
				$archivosAuditables[] = sprintf('%s -> %s', basename($archivoOrigen), $archivoDestino);

				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'OK',
					'mensaje' => $mensajeFinal);

			} catch (Exception $ex) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => 'No se pudo mover la digitalizacion reservada al directorio destino.');
				continue; // Salteo esta vuelta del foreach y continuo con el archivo siguiente.
			}
		}

		FTPHelper::get()->disconnect(); // Cierro la conexion al ftp

		return $resultado;
	}

	/**
	 * Crea un directorio para contener documentos de un expediente determinado.
	 * @param  string  $id_conexion			Identificador de la conexión FTP
	 * @param  string  $nombreDirectorio 	Nombre del directorio a crear dentro de PATH_KRAKEN_RESOURCES_PROYECTOS (sin barra final)
	 * @param  integer $permisosDirectorio 	Permisos de acceso al directorio (chmod)
	 */
	public function crearDirectorioParaExpediente($id_conexion, $nombreDirectorio, $permisosDirectorio = 0777) {

		if (!@ftp_chdir($id_conexion, PATH_KRAKEN_RESOURCES_PROYECTOS)) {
			throw new Exception('No existe el directorio base de proyectos.');
		}

		$directorios = explode('/', $nombreDirectorio); // ej: 2020/20N00050/reservados

		foreach ($directorios as $dir) {
			// Si no existe el directorio, lo creo.
			if (!@ftp_chdir($id_conexion, $dir)) {
				if (!ftp_mkdir($id_conexion, $dir)) {
					throw new Exception('No se puede crear el directorio ' . $dir);
				} else {
					ftp_chdir($id_conexion, $dir);
				}
			}
		}
	}

	/**
	 * Se sobreescribe la digitalización reservada existente
	 * @param  [string] $digitalizacion_a_sobreescribir Nombre del archivo de la digitalización a sobreescribir
	 */
	public function sobreescribirDigitalizacionReservada($digitalizacion_a_sobreescribir) {

		$resultado = array();

		$permisosDirectorio = 0775; // sudo find . -type d -exec chmod 775 {} +

		// Directorio "digital/"
		$directorio_desde = PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES;

		// Se abre el directorio "proyectos/digital/" para tomar la digitalización a agregar
		$directorio_abierto = opendir($directorio_desde);

		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');

		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			$resultado[] = array(
				'archivo' => $digitalizacion_a_sobreescribir,
				'estado' => 'ERROR',
				'mensaje' => 'Error al intentar conectarse o autentificarse en el Servidor FTP.');
		} else {
			// Se toma solo el nombre codificado del archivo, AAENNNNN
			// se convierte a mayúsculas para evitar AAtNNNNN (13/09/2019 XXXX)
			$nombre_codificado = mb_strtoupper(substr($digitalizacion_a_sobreescribir, 0, 8));

			// Se toman los dos primeros dígitos, correspondientes al año
			$anio_corto = substr($digitalizacion_a_sobreescribir, 0, 2);
			// Se completa el año
			$anio_completo = ($anio_corto >= 83) ? $anio_corto + 1900 : $anio_corto + 2000;

			// Directorio donde se va a sobreescribir "proyectos/AAAA/AAENNNNN/reservados"
			$directorio_destino = PATH_KRAKEN_RESOURCES_PROYECTOS . $anio_completo . '/' . $nombre_codificado . '/reservados';

			// Si no existe el directorio destino
			if (!file_exists($directorio_destino)) {
				try {
					// Se crea
					$this->crearDirectorioParaExpediente(
						$id_conexion,
						$anio_completo . '/' . $nombre_codificado . '/reservados',
						$permisosDirectorio);

				} catch (Exception $ex) {
					$resultado[] = array(
						'archivo' => $a,
						'estado' => 'ERROR',
						'mensaje' => $ex->getMessage());
				}
			} else if (!is_dir($directorio_destino)) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => 'El directorio destino existe, pero es un archivo.');
			}

			// Se cambia a ese directorio
			ftp_chdir($id_conexion, $directorio_destino);

			// Se toma el nombre del directorio actual: "proyectos/AAAA/AAENNNNN/reservados"
			$dir_actual = ftp_pwd($id_conexion);

			// Ruta de la digitalización EXISTENTE (proyectos/AAAA/AAENNNNN/reservados/AAENNNNN.pdf)
			$ruta_digitalizacion_existente = $dir_actual . "/" . $nombre_codificado . '.pdf';

			// Ruta de la digitalización a sobreescribir (proyectos/digital/AAENNNNN.pdf)
			// (puede poseer la letra del Tipo en minúscula o mayúscula)
			$ruta_digitalizacion_nueva = $directorio_desde . $digitalizacion_a_sobreescribir;

			try {
				// Se elimina la digitalización existente para cargar la nueva
				if (file_exists($ruta_digitalizacion_existente)) {
					if (! ftp_delete($id_conexion, $ruta_digitalizacion_existente)) {
						$resultado[] = array(
							'archivo' => $digitalizacion_a_sobreescribir,
							'estado' => 'ERROR',
							'mensaje' => "No se pudo eliminar el documento reservado existente, verifique que no se encuentre abierto en alg&uacute;n equipo.");
					} else {
						// Se carga la nueva digitalización (ya con la extensión en minúscula)
						if (ftp_put($id_conexion, $ruta_digitalizacion_existente, $ruta_digitalizacion_nueva, FTP_BINARY)) {

							// Se elimina la digitalización temporal del directorio "proyectos/digital/" del expediente
							if (file_exists($ruta_digitalizacion_nueva)) {
								if (! ftp_delete($id_conexion, $ruta_digitalizacion_nueva)) {
									$resultado[] = array(
										'archivo' => $digitalizacion_a_sobreescribir,
										'estado' => 'ERROR',
										'mensaje' => "No se pudo eliminar el documento temporal, utilizado para la sobreescritura.");
								} else {
									$resultado[] = array(
										'archivo' => $nombre_codificado . '.pdf',
										'estado' => 'OK',
										'mensaje' => "Se ha sobreescrito la digitalizaci&oacute;n reservada satisfactoriamente!");

									$exp = $this->obtenerExpedienteDesdeNombreCodificado($nombre_codificado);

									// Se audita la sobreescritura de la digitalización
									NG::auditorias()->auditarComoExpediente(
										$exp,
										null,
										Auditoria::OP_ARCHIVO_SOBREESCRITO,
										$digitalizacion_a_sobreescribir,
										null, null,
										sprintf('Se ha sobreescrito la digitalizacion reservada %s', $digitalizacion_a_sobreescribir)
									);
								}
							}
						}
					}
				}
			} catch (Exception $ex) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => 'No se pudo sobreescribir la digitalizacion reservada existente.');
			}
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		// Se cierra el directorio "digital/"
		closedir($directorio_abierto);

		return $resultado;
	}

	/**
	 * Se agrega, es decir, se une la digitalización a la existente al final
	 * @param  string $digitalizacion_a_agregar 	Nombre del archivo de la digitalización a agregar a la existente
	 */
	public function agregarDigitalizacionReservada($digitalizacion_a_agregar) {

		$resultado = array();

		$permisosDirectorio = 0775; // sudo find . -type d -exec chmod 775 {} +

		// Directorio "digital/"
		$directorio_desde = PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES;

		// Se abre el directorio "proyectos/digital/" para tomar la digitalización a agregar
		$directorio_abierto = opendir($directorio_desde);

		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');

		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			$resultado[] = array(
				'archivo' => $digitalizacion_a_agregar,
				'estado' => 'ERROR',
				'mensaje' => 'Error al intentar conectarse o autentificarse en el Servidor FTP.');
		} else {
			// Se toma solo el nombre codificado del archivo, AAENNNNN
			// se convierte a mayúsculas para evitar AAtNNNNN
			$nombre_codificado = mb_strtoupper(substr($digitalizacion_a_agregar, 0, 8));

			// Se toman los dos primeros dígitos, correspondientes al año
			$anio_corto = substr($digitalizacion_a_agregar, 0, 2);
			// Se completa el año
			$anio_completo = ($anio_corto >= 83) ? $anio_corto + 1900 : $anio_corto + 2000;

			// Directorio donde se va a cargar la unión "proyectos/AAAA/AAENNNNN/"
			$directorio_destino = PATH_KRAKEN_RESOURCES_PROYECTOS . $anio_completo . '/' . $nombre_codificado . '/reservados';

			// Si no existe el directorio destino
			if (!file_exists($directorio_destino)) {
				try {
					// Se crea
					$this->crearDirectorioParaExpediente(
						$id_conexion,
						$anio_completo . '/' . $nombre_codificado . '/reservados',
						$permisosDirectorio);

				} catch (Exception $ex) {
					$resultado[] = array(
						'archivo' => $a,
						'estado' => 'ERROR',
						'mensaje' => $ex->getMessage());
				}
			} else if (!is_dir($directorio_destino)) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => 'El directorio destino existe, pero es un archivo.');
			}

			// Se cambia a ese directorio
			ftp_chdir($id_conexion, $directorio_destino);

			// Se toma el nombre del directorio actual: "proyectos/AAAA/AATNNNNN/reservados/"
			$dir_actual = ftp_pwd($id_conexion);

			// Ruta de la digitalización EXISTENTE (proyectos/AAAA/AATNNNNN/reservados/AATNNNNN.pdf)
			$ruta_digitalizacion_existente = $dir_actual . "/" . $nombre_codificado . ".pdf";

			// Ruta de la digitalización A AGREGAR al existente (proyectos/digital/AATNNNNN.pdf)
			// aquí su nombre puede contener la letra 'a' o 'A'
			$ruta_digitalizacion_a_agregar = $directorio_desde . $digitalizacion_a_agregar;

			// Ruta de la digitalización DESCARGADA para unirle la agregada
			$ruta_digitalizacion_actual_descargada = $directorio_desde . $nombre_codificado . '_actual.pdf';

			// Ruta del resultado FINAL de la unión
			$ruta_digitalizacion_final = $directorio_desde . $nombre_codificado . '_final.pdf';

			// 1ro:
			// Se descarga la digitalización actual y se guarda en "proyectos/digital/"
			// con el nombre AATNNNNN_actual.pdf
			if (ftp_get($id_conexion, $ruta_digitalizacion_actual_descargada, $ruta_digitalizacion_existente, FTP_BINARY)) {
				// 2do:
				// Se unen las digitalizaciones
				$union_pdfs = $this->unirDigitalizaciones(
					$ruta_digitalizacion_actual_descargada,
					$ruta_digitalizacion_a_agregar,
					$ruta_digitalizacion_final
				);

				// Si se ha generado la unión de las digitalizaciones
				if (!is_null($union_pdfs)) {

					// Se elimina la digitalización EXISTENTE para cargar la unión de ambas digitalizaciones
					if (! ftp_delete($id_conexion, $ruta_digitalizacion_existente)) {
						$resultado[] = array(
							'archivo' => $digitalizacion_a_agregar,
							'estado' => 'ERROR',
							'mensaje' => "No se pudo eliminar el documento reservado existente, verifique que no se encuentre abierto en alg&uacute;n equipo.");
					} else {
						// Se carga la digitalización NUEVA (con la extensión en minúscula)
						if (ftp_put($id_conexion, $dir_actual . "/" . $nombre_codificado . ".pdf", $union_pdfs, FTP_BINARY)) {

							// Se elimina la digitalización que fue agregada
							// del directorio "proyectos/digital/AATNNNNN.pdf"
							if (! ftp_delete($id_conexion, $ruta_digitalizacion_a_agregar)) {
								$resultado[] = array(
									'archivo' => $digitalizacion_a_agregar,
									'estado' => 'ERROR',
									'mensaje' => "No se pudo eliminar el documento reservado que fue agregado.");
							} else {
								// Se elimina la copia que se utilizó para unir con la digitalización agregada
								// la "AATNNNNN_actual.pdf"
								if (! ftp_delete($id_conexion, $ruta_digitalizacion_actual_descargada)) {
									$resultado[] = array(
										'archivo' => $digitalizacion_a_agregar,
										'estado' => 'ERROR',
										'mensaje' => "No se pudo eliminar la copia que se utiliz&oacute; para unir con la digitalizaci&oacute;n agregada.");
								} else {
									// Se elimina el resultado final
									// la "AATNNNNN_final.pdf"
									if (! ftp_delete($id_conexion, $ruta_digitalizacion_final)) {
										$resultado[] = array(
											'archivo' => $digitalizacion_a_agregar,
											'estado' => 'ERROR',
											'mensaje' => "No se pudo eliminar el resultado final.");
									} else {
										$resultado[] = array(
											'archivo' => $digitalizacion_a_agregar,
											'estado' => 'OK',
											'mensaje' => "Se ha agregado la digitalizaci&oacute;n satisfactoriamente a la existente!");

										$exp = $this->obtenerExpedienteDesdeNombreCodificado($nombre_codificado);

										// Se audita la unión de las digitalizaciones
										NG::auditorias()->auditarComoExpediente(
											$exp,
											null,
											Auditoria::OP_ARCHIVO_AGREGADO,
											PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES,
											null, null,
											sprintf('Se ha agregado la digitalizacion %s', $digitalizacion_a_agregar)
										);
									}
								}
							}
						} else {
							$resultado[] = array(
								'archivo' => $digitalizacion_a_agregar,
								'estado' => 'ERROR',
								'mensaje' => "La uni&oacute;n de las digitalizaciones ha fallado!");
						}
					}
				} else {
					$resultado[] = array(
						'archivo' => $digitalizacion_a_agregar,
						'estado' => 'ERROR',
						'mensaje' => "La uni&oacute;n de las digitalizaciones ha fallado!");
				}
			} else {
				$resultado[] = array(
					'archivo' => $digitalizacion_a_agregar,
					'estado' => 'ERROR',
					'mensaje' => "La descarga de la copia en " . $ruta_digitalizacion_actual_descargada . " ha fallado!");
			}
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		// Se cierra el directorio "digital/"
		closedir($directorio_abierto);

		return $resultado;
	}

	/**
	 * Se elimina una digitalización reservada temporal, de un expediente determinado
	 * @param  [string]  $pArchivo Nombre de la digitalización temporal
	 * @return [boolean]           True|false
	 */
	public function eliminarDigitalizacionReservadaTemporal($pArchivo) {
		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');
		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			return false;
		} else {
			// Se cambia a ese directorio
			ftp_chdir($id_conexion, PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES);
			// Se toma el nombre del directorio actual: "proyectos/digital/"
			$dir_actual = ftp_pwd($id_conexion);

			// Ruta de la digitalización temporal (proyectos/digital/AATNNNNN.pdf|AATNNNNNa.pdf|AATNNNNNA.pdf)
			$ruta_documento_a_eliminar = $dir_actual . "/" . $pArchivo;

			// Si existe el documento original
			if (file_exists($ruta_documento_a_eliminar)) {
				// Se elimina
				if (ftp_delete($id_conexion, $ruta_documento_a_eliminar)) {

					$exp = $this->obtenerExpedienteDesdeNombreCodificado($pArchivo);

					// Se audita la eliminación del documento original
					NG::auditorias()->auditarComoExpediente(
						$exp,
						null,
						Auditoria::OP_ARCHIVO_ELIMINADO,
						PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES,
						null, null,
						sprintf('Se ha eliminado la digitalizacion reservada temporal %s', $pArchivo)
					);
				}
			}
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		return true;
	}

	/**
	 * Se sobreescribe un Documento Público que se desea mover desde los reservados
	 * @param  integer $f_anio              Año del expediente
	 * @param  string  $f_tipo              Tipo del expediente
	 * @param  integer $f_numero           	Número del expediente
	 * @param  string  $documento_existente Nombre del documento
	 * @return boolean
	 */
	public function sobreescribirDocPublicoEnMovimiento($f_anio, $f_tipo, $f_numero, $documento_existente) {

		$resultado = array();

		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');
		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			$resultado[] = array(
				'archivo' => $documento_existente,
				'estado' => 'ERROR',
				'mensaje' => 'Error al intentar conectarse o autentificarse en el Servidor FTP.');
		} else {
			// Se arma el nombre codificado
			$nombre_codificado = sprintf("%02d%s%05d", $f_anio % 100, $f_tipo, $f_numero);
			// Ruta del directorio del expediente
			$directorio_del_expediente = PATH_KRAKEN_RESOURCES_PROYECTOS . $f_anio . "/" . $nombre_codificado;

			try {
				// Si existe el documento reservado a mover
				if (file_exists($directorio_del_expediente . '/reservados/' . $documento_existente)) {
					// Se mueve
					if (ftp_rename(
						$id_conexion,
						$directorio_del_expediente . '/reservados/' . $documento_existente,
						$directorio_del_expediente . '/' . $documento_existente)) {

						// Como puede haber mas de un expediente para año/tipo/numero, me quedo con el primero que encuentro
						$expedientes = $this->obtenerExpedientes($f_anio, $f_tipo, $f_numero);
						$exp = (count($expedientes) > 0) ? $expedientes[0] : null;

						// Se audita el movimiento del documento al directorio de reservados
						NG::auditorias()->auditarComoExpediente(
							$exp,
							null,
							Auditoria::OP_ARCHIVO_MOVIDO,
							$directorio_del_expediente,
							null, null,
							sprintf('Se ha sobreescrito el documento %s al directorio %s', $documento_existente, $nombre_codificado)
						);
					}
				}

				$resultado[] = array(
					'archivo' => $documento_existente,
					'estado' => 'OK',
					'mensaje' => "Se ha sobreescrito el documento satisfactoriamente!");

			} catch (Exception $ex) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => 'No se pudo sobreescribir el documento existente.');
			}
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		return $resultado;
	}

	/**
	 * Se sobreescribe un Documento Reservado que se desea mover desde los Públicos
	 * @param  integer $f_anio              Año del expediente
	 * @param  string  $f_tipo              Tipo del expediente
	 * @param  integer $f_numero           	Número del expediente
	 * @param  string  $documento_existente Nombre del documento
	 * @return boolean
	 */
	public function sobreescribirDocReservadoEnMovimiento($f_anio, $f_tipo, $f_numero, $documento_existente) {

		$resultado = array();

		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');
		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			$resultado[] = array(
				'archivo' => $documento_existente,
				'estado' => 'ERROR',
				'mensaje' => 'Error al intentar conectarse o autentificarse en el Servidor FTP.');
		} else {
			// Se arma el nombre codificado
			$nombre_codificado = sprintf("%02d%s%05d", $f_anio % 100, $f_tipo, $f_numero);
			// Ruta del directorio del expediente
			$directorio_del_expediente = PATH_KRAKEN_RESOURCES_PROYECTOS . $f_anio . "/" . $nombre_codificado;

			try {
				// Si existe el documento público a mover
				if (file_exists($directorio_del_expediente . '/' . $documento_existente)) {
					// Se mueve
					if (ftp_rename(
						$id_conexion,
						$directorio_del_expediente . '/' . $documento_existente,
						$directorio_del_expediente . '/reservados/' . $documento_existente)) {

						// Como puede haber mas de un expediente para año/tipo/numero, me quedo con el primero que encuentro
						$expedientes = $this->obtenerExpedientes($f_anio, $f_tipo, $f_numero);
						$exp = (count($expedientes) > 0) ? $expedientes[0] : null;

						// Se audita el movimiento del documento al directorio de reservados
						NG::auditorias()->auditarComoExpediente(
							$exp,
							null,
							Auditoria::OP_ARCHIVO_MOVIDO,
							$directorio_del_expediente,
							null, null,
							sprintf('Se ha sobreescrito el documento reservado %s al directorio %s', $documento_existente, $nombre_codificado)
						);
					}
				}

				$resultado[] = array(
					'archivo' => $documento_existente,
					'estado' => 'OK',
					'mensaje' => "Se ha sobreescrito el documento satisfactoriamente!");

			} catch (Exception $ex) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => 'No se pudo sobreescribir el documento reservado existente.');
			}
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		return $resultado;
	}

	/**
	 * Se agrega, es decir, se une un documento al existente al final, durante un movimiento
	 * @param  integer $f_anio              Año del expediente
	 * @param  string  $f_tipo              Tipo del expediente
	 * @param  integer $f_numero           	Número del expediente
	 * @param  string  $documento_a_agregar Nombre del archivo a agregar al existente
	 */
	public function agregarDocumentoPublicoPdfEnMovimiento($f_anio, $f_tipo, $f_numero, $documento_a_agregar) {

		$resultado = array();

		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');

		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			$resultado[] = array(
				'archivo' => $documento_a_agregar,
				'estado' => 'ERROR',
				'mensaje' => 'Error al intentar conectarse o autentificarse en el Servidor FTP.');
		} else {
			// Se arma el nombre codificado
			$nombre_codificado = sprintf("%02d%s%05d", $f_anio % 100, $f_tipo, $f_numero);
			// Ruta del directorio del expediente
			$directorio_del_expediente = PATH_KRAKEN_RESOURCES_PROYECTOS . $f_anio . "/" . $nombre_codificado;

			// Directorio "temporal/"
			$directorio_desde = PATH_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES;

			// Se abre el directorio "proyectos/temporal/" para tomar la digitalización a agregar
			$directorio_abierto = opendir($directorio_desde);

			// Se cambia a ese directorio
			ftp_chdir($id_conexion, $directorio_del_expediente);

			// Se toma el nombre del directorio actual: "proyectos/AAAA/AATNNNNN/"
			$dir_actual = ftp_pwd($id_conexion);

			// Ruta del documento EXISTENTE (proyectos/AAAA/AATNNNNN/eldocumento.pdf)
			$ruta_documento_existente = $dir_actual . "/" . $documento_a_agregar;

			// Ruta del documento A AGREGAR al existente (proyectos/AAAA/AATNNNNN/reservados/eldocumento.pdf)
			$ruta_documento_a_agregar = $dir_actual . "/reservados/" . $documento_a_agregar;

			// Ruta del documento DESCARGADO (a la que se le une lo nuevo)
			$ruta_documento_actual_descargado = $directorio_desde . $documento_a_agregar . '_actual.pdf';

			// Ruta del resultado FINAL de la unión
			$ruta_documento_final = $directorio_desde . $documento_a_agregar . '_final.pdf';

			// Se descarga el documento actual
			if (ftp_get($id_conexion, $ruta_documento_actual_descargado, $ruta_documento_existente, FTP_BINARY)) {

				// Se unen los documentos
				$union_pdfs = $this->unirDigitalizaciones(
					$ruta_documento_actual_descargado,
					$ruta_documento_a_agregar,
					$ruta_documento_final
				);

				// Si se ha generado la unión de los documentos
				if (!is_null($union_pdfs)) {

					// Se elimina el documento EXISTENTE para cargar la unión de ambos documentos
					if (! ftp_delete($id_conexion, $ruta_documento_existente)) {
						$resultado[] = array(
							'archivo' => $documento_a_agregar,
							'estado' => 'ERROR',
							'mensaje' => 'No se pudo eliminar el documento p&uacute;blico existente, verifique que no se encuentre abierto en alg&uacute;n equipo.');
					} else {
						// Se carga el documento NUEVO
						if (ftp_put($id_conexion, $dir_actual . "/" . $documento_a_agregar, $union_pdfs, FTP_BINARY)) {

							// Se elimina el documento que fue agregado
							if (! ftp_delete($id_conexion, $ruta_documento_a_agregar)) {
								$resultado[] = array(
									'archivo' => $documento_a_agregar,
									'estado' => 'ERROR',
									'mensaje' => 'No se pudo eliminar el documento que fue agregado.');
							} else {
								// Se elimina la copia que se utilizó para unir con el documento agregado
								if (! ftp_delete($id_conexion, $ruta_documento_actual_descargado)) {
									$resultado[] = array(
										'archivo' => $documento_a_agregar,
										'estado' => 'ERROR',
										'mensaje' => 'No se pudo eliminar la copia que se utilizó para unir con el documento agregado.');
								} else {
									// Se elimina el resultado final (el temporal creado)
									if (! ftp_delete($id_conexion, $ruta_documento_final)) {
										$resultado[] = array(
											'archivo' => $documento_a_agregar,
											'estado' => 'ERROR',
											'mensaje' => 'No se pudo eliminar el documento final (el temporal creado).');
									} else {
										$resultado[] = array(
											'archivo' => $documento_a_agregar,
											'estado' => 'OK',
											'mensaje' => "Se ha agregado el documento satisfactoriamente al existente!");

										// Como puede haber mas de un expediente para año/tipo/numero, me quedo con el primero que encuentro
										$expedientes = $this->obtenerExpedientes($f_anio, $f_tipo, $f_numero);
										$exp = (count($expedientes) > 0) ? $expedientes[0] : null;

										// Se audita la unión de los documentos
										NG::auditorias()->auditarComoExpediente(
											$exp,
											null,
											Auditoria::OP_ARCHIVO_AGREGADO,
											$directorio_del_expediente,
											null, null,
											sprintf('Se ha agregado el documento %s', $documento_a_agregar)
										);
									}
								}
							}
						} else {
							$resultado[] = array(
								'archivo' => $documento_a_agregar,
								'estado' => 'ERROR',
								'mensaje' => "La uni&oacute;n de los documentos ha fallado!");
						}
					}
				} else {
					$resultado[] = array(
						'archivo' => $documento_a_agregar,
						'estado' => 'ERROR',
						'mensaje' => "La uni&oacute;n de los documentos ha fallado!");
				}
			} else {
				$resultado[] = array(
					'archivo' => $documento_a_agregar,
					'estado' => 'ERROR',
					'mensaje' => "La descarga de la copia en " . $ruta_documento_actual_descargado . " ha fallado!");
			}
			// Se cierra el directorio codificado del expediente
			closedir($directorio_abierto);
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		return $resultado;
	}

	/**
	 * Se agrega, es decir, se une un documento al Reservado existente al final, durante un movimiento
	 * @param  integer $f_anio              Año del expediente
	 * @param  string  $f_tipo              Tipo del expediente
	 * @param  integer $f_numero           	Número del expediente
	 * @param  string  $documento_a_agregar Nombre del archivo a agregar al existente
	 */
	public function agregarDocumentoReservadoPdfEnMovimiento($f_anio, $f_tipo, $f_numero, $documento_a_agregar) {

		$resultado = array();

		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');

		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			$resultado[] = array(
				'archivo' => $documento_a_agregar,
				'estado' => 'ERROR',
				'mensaje' => 'Error al intentar conectarse o autentificarse en el Servidor FTP.');
		} else {
			// Se arma el nombre codificado
			$nombre_codificado = sprintf("%02d%s%05d", $f_anio % 100, $f_tipo, $f_numero);
			// Ruta del directorio del expediente
			$directorio_del_expediente = PATH_KRAKEN_RESOURCES_PROYECTOS . $f_anio . "/" . $nombre_codificado;

			// Directorio "temporal/"
			$directorio_desde = PATH_KRAKEN_RESOURCES_PROYECTOS_TEMPORALES;

			// Se abre el directorio "proyectos/temporal/" para tomar la digitalización a agregar
			$directorio_abierto = opendir($directorio_desde);

			// Se cambia a ese directorio
			ftp_chdir($id_conexion, $directorio_del_expediente);

			// Se toma el nombre del directorio actual: "proyectos/AAAA/AAENNNNN/"
			$dir_actual = ftp_pwd($id_conexion);

			// Ruta del documento EXISTENTE (proyectos/AAAA/AAENNNNN/reservados/eldocumento.pdf)
			$ruta_documento_existente = $dir_actual . "/reservados/" . $documento_a_agregar;

			// Ruta del documento A AGREGAR al existente (proyectos/AAAA/AAENNNNN/reservados/eldocumento.pdf)
			$ruta_documento_a_agregar = $dir_actual . "/" . $documento_a_agregar;

			// Ruta del documento DESCARGADO (a la que se le une lo nuevo)
			$ruta_documento_actual_descargado = $directorio_desde . $documento_a_agregar . '_actual.pdf';

			// Ruta del resultado FINAL de la unión
			$ruta_documento_final = $directorio_desde . $documento_a_agregar . '_final.pdf';

			// Se descarga el documento actual
			if (ftp_get($id_conexion, $ruta_documento_actual_descargado, $ruta_documento_existente, FTP_BINARY)) {
				// Se unen los documentos
				$union_pdfs = $this->unirDigitalizaciones(
					$ruta_documento_actual_descargado,
					$ruta_documento_a_agregar,
					$ruta_documento_final
				);

				// Si se ha generado la unión de los documentos
				if (!is_null($union_pdfs)) {

					// Se elimina el documento EXISTENTE para cargar la unión de ambos documentos
					if (! ftp_delete($id_conexion, $ruta_documento_existente)) {
						$resultado[] = array(
							'archivo' => $documento_a_agregar,
							'estado' => 'ERROR',
							'mensaje' => 'No se pudo eliminar el documento reservado existente, verifique que no se encuentre abierto en alg&uacute;n equipo.');
					} else {
						// Se carga el documento NUEVO
						if (ftp_put($id_conexion, $dir_actual."/reservados/".$documento_a_agregar, $union_pdfs, FTP_BINARY)) {

							// Se elimina el documento que fue agregado
							if (! ftp_delete($id_conexion, $ruta_documento_a_agregar)) {
								$resultado[] = array(
									'archivo' => $documento_a_agregar,
									'estado' => 'ERROR',
									'mensaje' => 'No se pudo eliminar el documento que fue agregado.');
							} else {
								// Se elimina la copia que se utilizó para unir con el documento agregado
								if (! ftp_delete($id_conexion, $ruta_documento_actual_descargado)) {
									$resultado[] = array(
										'archivo' => $documento_a_agregar,
										'estado' => 'ERROR',
										'mensaje' => 'No se pudo eliminar la copia que se utilizó para unir con el documento agregado.');
								} else {
									// Se elimina el resultado final (el temporal creado)
									if (! ftp_delete($id_conexion, $ruta_documento_final)) {
										$resultado[] = array(
											'archivo' => $documento_a_agregar,
											'estado' => 'ERROR',
											'mensaje' => 'No se pudo eliminar el documento final (el temporal creado).');
									} else {
										$resultado[] = array(
											'archivo' => $documento_a_agregar,
											'estado' => 'OK',
											'mensaje' => "Se ha agregado el documento satisfactoriamente al existente!");

										// Como puede haber mas de un expediente para año/tipo/numero, me quedo con el primero que encuentro
										$expedientes = $this->obtenerExpedientes($f_anio, $f_tipo, $f_numero);
										$exp = (count($expedientes) > 0) ? $expedientes[0] : null;

										// Se audita la unión de los documentos
										NG::auditorias()->auditarComoExpediente(
											$exp,
											null,
											Auditoria::OP_ARCHIVO_AGREGADO,
											$directorio_del_expediente,
											null, null,
											sprintf('Se ha agregado el documento %s', $documento_a_agregar)
										);
									}
								}
							}
						} else {
							$resultado[] = array(
								'archivo' => $documento_a_agregar,
								'estado' => 'ERROR',
								'mensaje' => "La uni&oacute;n de los documentos ha fallado!");
						}
					}
				} else {
					$resultado[] = array(
						'archivo' => $documento_a_agregar,
						'estado' => 'ERROR',
						'mensaje' => "La uni&oacute;n de los documentos ha fallado!");
				}
			} else {
				$resultado[] = array(
					'archivo' => $documento_a_agregar,
					'estado' => 'ERROR',
					'mensaje' => "La descarga de la copia en " . $ruta_documento_actual_descargado . " ha fallado!");
			}
			// Se cierra el directorio codificado del expediente
			closedir($directorio_abierto);
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		return $resultado;
	}

	/**
	 * Se obtiene el Expediente a partir del Nombre Codificado
	 * @param  string 		$nombre_codificado  Nombre codificado AATNNNNN
	 * @return Expediente   $exp 				Instancia del Expediente respectivo
	 */
	public function obtenerExpedienteDesdeNombreCodificado($nombre_codificado) {

		$anio_corto = substr($nombre_codificado, 0, 2);
		$expe_anio = ($anio_corto >= 83) ? $anio_corto + 1900 : $anio_corto + 2000;
		$expe_tipo = substr($nombre_codificado, 2, 1);
		$expe_numero = (int) substr($nombre_codificado, 3, 5);

		// Como puede haber mas de un expediente para año/tipo/numero, me quedo con el primero que encuentro
		$expedientes = $this->obtenerExpedientes($expe_anio, $expe_tipo, $expe_numero);
		$exp = (count($expedientes) > 0) ? $expedientes[0] : null;

		return $exp;
	}

	/**
	 * Se sube una digitalización como auxiliar
	 * @param  Expediente $pExpediente     Expediente al cual asignar la digitalización
	 * @param  array    &$filesReference   Referencia al objeto de formulario que contiene la información del archivo.
	 * @return Expediente                  Expediente al cual se asignó la digitalización
	 */
	public function subirDigitalizacionAuxiliar(Expediente $pExpediente, &$filesReference) {

		// Si no se recibió un error
		if ($filesReference['f_digitalizacion']['error'] != UPLOAD_ERR_NO_FILE) {
			try {
				// Se arma el nombre codificado
				$nombre_codificado = sprintf("%02d%s%05d", $pExpediente->anio % 100, $pExpediente->tipo, $pExpediente->numero);

				// Directorio "digital/"
				$directorio_temporal = PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES;

				// Nombre del documento auxiliar
				$nombre_auxiliar = "temp_" . $nombre_codificado . ".pdf";

				// Se establece una conexión FTP
				$id_conexion = ftp_connect('localhost');

				// Se establece el inicio de sesión FTP con usuario y password
				$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

				// Se cambia al directorio donde se quiere subir el archivo
				ftp_chdir($id_conexion, $directorio_temporal);

				$dir_actual = ftp_pwd($id_conexion);

				$archivoOrigen = $filesReference['f_digitalizacion']['tmp_name'];
				$archivoDestino = sprintf('%s%s', $directorio_temporal, $nombre_auxiliar);

				ftp_put($id_conexion, $archivoDestino, $archivoOrigen, FTP_BINARY);

				ftp_close($id_conexion);

			} catch (Exception $exUpload) {
				throw new Exception('No se pudo cargar el documento auxiliar. Causa: ' . $exUpload->getMessage());
			}
		}

		return $pExpediente;
	}

	/**
	 * Se sube una digitalización directamente al directorio del expediente
	 * @param  Expediente $pExpediente       Expediente al cual asignar el documento público
	 * @param  array      &$filesReference   Referencia al objeto de formulario que contiene la información del archivo.
	 * @return Expediente                    Expediente al cual se asignó el documento público
	 */
	public function subirDigitalizacionDirectamente(Expediente $pExpediente, &$filesReference) {
		// Si no se recibió un error
		if ($filesReference['f_digitalizacion']['error'] != UPLOAD_ERR_NO_FILE) {
			try {
				// Nombre codificado del directorio del expediente respectivo
				$nombre_codificado = sprintf("%02d%s%05d", $pExpediente->anio % 100, $pExpediente->tipo, $pExpediente->numero);

				// Ruta del directorio remoto de públicos, determinado por la clave del expediente respectivo
				$directorio_expediente = PATH_KRAKEN_RESOURCES_PROYECTOS . $pExpediente->anio . "/" . $nombre_codificado . "/";

				// Nombre de la digitalizacion
				$nombre_digitalizacion = $nombre_codificado . '.pdf';

				// Se establece una conexión FTP
				$id_conexion = ftp_connect('localhost');

				// Se establece el inicio de sesión FTP con usuario y password
				$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

				// Se cambia al directorio donde se quiere subir el archivo
				ftp_chdir($id_conexion, $directorio_expediente);

				$dir_actual = ftp_pwd($id_conexion);

				$archivoOrigen = $filesReference['f_digitalizacion']['tmp_name'];
				$archivoDestino = sprintf('%s%s', $directorio_expediente, $nombre_digitalizacion);

				if (ftp_put($id_conexion, $archivoDestino, $archivoOrigen, FTP_BINARY)) {
					// Auditoria
					NG::auditorias()->auditarComoExpediente(
						$pExpediente,
						null,
						Auditoria::OP_ARCHIVO_SUBIDO,
						sprintf('%s%s', $directorio_expediente, $nombre_digitalizacion), // en vez de tabla, utilizamos la ruta donde se guarda el archivo
						null, null,
						sprintf('Se ha subido la digitalizaci&oacute;n %s', $nombre_digitalizacion)
					);
				}

				ftp_close($id_conexion);

			} catch (Exception $ex) {
				throw new Exception('No se pudo subir la digitalizaci&oacute;n. Causa: ' . $ex->getMessage());
			}
		}

		return $pExpediente;
	}

	/**
	 * Se sube una digitalización reservada directamente al directorio del expediente
	 * @param  Expediente $pExpediente       Expediente al cual asignar el documento público
	 * @param  array      &$filesReference   Referencia al objeto de formulario que contiene la información del archivo.
	 * @return Expediente                    Expediente al cual se asignó el documento público
	 */
	public function subirDigitalizacionReservadaDirectamente(Expediente $pExpediente, &$filesReference) {
		// Si no se recibió un error
		if ($filesReference['f_digitalizacion']['error'] != UPLOAD_ERR_NO_FILE) {
			try {
				$permisosDirectorio = 0775;
				// Nombre codificado del directorio del expediente respectivo
				$nombre_codificado = sprintf("%02d%s%05d", $pExpediente->anio % 100, $pExpediente->tipo, $pExpediente->numero);

				// Ruta del directorio remoto de reservados del expediente respectivo
				$directorio_reservados = PATH_KRAKEN_RESOURCES_PROYECTOS . $pExpediente->anio . "/" . $nombre_codificado . "/reservados/";

				// Nombre de la digitalizacion
				$nombre_digitalizacion = $nombre_codificado . '.pdf';

				// Se establece una conexión FTP
				$id_conexion = ftp_connect('localhost');

				// Se establece el inicio de sesión FTP con usuario y password
				$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

				// Si no existe el directorio destino
				if (!file_exists($directorio_reservados)) {
					try {
						// Se crea
						$this->crearDirectorioParaExpediente(
							$id_conexion,
							$pExpediente->anio . '/' . $nombre_codificado . '/reservados',
							$permisosDirectorio);

					} catch (Exception $ex) {
						$resultado[] = array(
							'archivo' => $a,
							'estado' => 'ERROR',
							'mensaje' => $ex->getMessage());
					}
				} else if (!is_dir($directorio_reservados)) {
					$resultado[] = array(
						'archivo' => $a,
						'estado' => 'ERROR',
						'mensaje' => 'El directorio destino existe, pero es un archivo.');
				}

				// Se cambia al directorio donde se quiere subir el archivo
				ftp_chdir($id_conexion, $directorio_reservados);

				$dir_actual = ftp_pwd($id_conexion);

				$archivoOrigen = $filesReference['f_digitalizacion']['tmp_name'];
				$archivoDestino = sprintf('%s%s', $directorio_reservados, $nombre_digitalizacion);

				if (ftp_put($id_conexion, $archivoDestino, $archivoOrigen, FTP_BINARY)) {
					// Auditoria
					NG::auditorias()->auditarComoExpediente(
						$pExpediente,
						null,
						Auditoria::OP_ARCHIVO_SUBIDO,
						sprintf('%s%s', $directorio_reservados, $nombre_digitalizacion), // en vez de tabla, utilizamos la ruta donde se guarda el archivo
						null, null,
						sprintf('Se ha subido la digitalizaci&oacute;n %s', $nombre_digitalizacion)
					);
				}

				ftp_close($id_conexion);

			} catch (Exception $ex) {
				throw new Exception('No se pudo subir la digitalizaci&oacute;n. Causa: ' . $ex->getMessage());
			}
		}

		return $pExpediente;
	}

	/**
	 * Se sobreescribe la Digitalización directamente
	 * @param  integer $f_anio              Año del expediente
	 * @param  string  $f_tipo              Tipo del expediente
	 * @param  integer $f_numero           	Número del expediente
	 * @param  string  $documento_existente Nombre del documento
	 * @return boolean
	 */
	public function sobreescribirDigitalizacionDirectamente($f_anio, $f_tipo, $f_numero, $documento_existente, $es_reservada) {

		$resultado = array();

		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');
		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			$resultado[] = array(
				'archivo' => $documento_existente,
				'estado' => 'ERROR',
				'mensaje' => 'Error al intentar conectarse o autentificarse en el Servidor FTP.');
		} else {
			// Directorio "digital/"
			$directorio_desde = PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES;
			// Se arma el nombre codificado
			$nombre_codificado = sprintf("%02d%s%05d", $f_anio % 100, $f_tipo, $f_numero);
			// Ruta del directorio del expediente
			$directorio_del_expediente = PATH_KRAKEN_RESOURCES_PROYECTOS . $f_anio . "/" . $nombre_codificado;

			$directorio_destino = ($es_reservada == 1) ? $directorio_del_expediente . '/reservados' : $directorio_del_expediente;

			try {
				// Si existe el documento auxiliar
				if (file_exists($directorio_desde . '/temp_' . $documento_existente)) {
					// Se mueve
					if (ftp_rename(
						$id_conexion,
						$directorio_desde . '/temp_' . $documento_existente,
						$directorio_destino . '/' . $documento_existente)) {

						// Como puede haber mas de un expediente para año/tipo/numero, me quedo con el primero que encuentro
						$expedientes = $this->obtenerExpedientes($f_anio, $f_tipo, $f_numero);
						$exp = (count($expedientes) > 0) ? $expedientes[0] : null;

						// Se audita el movimiento del documento al directorio final del expediente respectivo
						NG::auditorias()->auditarComoExpediente(
							$exp,
							null,
							Auditoria::OP_ARCHIVO_MOVIDO,
							$directorio_del_expediente,
							null, null,
							sprintf('Se ha sobreescrito el documento %s al directorio %s', $documento_existente, $nombre_codificado)
						);
					}
				}

				$resultado[] = array(
					'archivo' => $documento_existente,
					'estado' => 'OK',
					'mensaje' => "Se ha sobreescrito el documento satisfactoriamente!");

			} catch (Exception $ex) {
				$resultado[] = array(
					'archivo' => $a,
					'estado' => 'ERROR',
					'mensaje' => 'No se pudo sobreescribir el documento existente.');
			}
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		return $resultado;
	}

	/**
	 * Se agrega, es decir, se une un documento al existente al final
	 * @param  string  $digitalizacion_a_agregar Nombre del archivo a agregar al existente
	 */
	public function agregarDigitalizacionDirectamente($digitalizacion_a_agregar, $es_reservada) {

		$resultado = array();

		// Directorio "digital/"
		$directorio_desde = PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES;

		// Se abre el directorio "proyectos/digital/" para tomar la digitalización a agregar
		$directorio_abierto = opendir($directorio_desde);

		// Se establece una conexión FTP
		$id_conexion = ftp_connect('localhost');

		// Se establece el inicio de sesión FTP con Usuario y Password
		$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

		// Se chequea la conexión
		if ((!$id_conexion) || (!$resultado_login)) {
			$resultado[] = array(
				'archivo' => $digitalizacion_a_agregar,
				'estado' => 'ERROR',
				'mensaje' => 'Error al intentar conectarse o autentificarse en el Servidor FTP.');
		} else {
			// Se toma solo el nombre codificado del archivo, AATNNNNN
			// se convierte a mayúsculas para evitar AAtNNNNN
			$nombre_codificado = mb_strtoupper(substr($digitalizacion_a_agregar, 0, 8));

			// Se toman los dos primeros dígitos, correspondientes al año
			$anio_corto = substr($digitalizacion_a_agregar, 0, 2);
			// Se completa el año
			$anio_completo = ($anio_corto >= 83) ? $anio_corto + 1900 : $anio_corto + 2000;

			// Directorio donde se va a cargar la unión "proyectos/AAAA/AATNNNNN/"
			$directorio_del_expediente = PATH_KRAKEN_RESOURCES_PROYECTOS . $anio_completo . '/' . $nombre_codificado;

			$directorio_destino = ($es_reservada == 1) ? $directorio_del_expediente . '/reservados' : $directorio_del_expediente;

			// Se cambia a ese directorio
			ftp_chdir($id_conexion, $directorio_destino);

			// Se toma el nombre del directorio actual: "proyectos/AAAA/AATNNNNN/"
			$dir_actual = ftp_pwd($id_conexion);

			// Ruta de la digitalización EXISTENTE (proyectos/AAAA/AATNNNNN/AATNNNNN.pdf)
			$ruta_digitalizacion_existente = $dir_actual . "/" . $nombre_codificado . ".pdf";

			// Ruta de la digitalización A AGREGAR al existente (proyectos/digital/temp_AATNNNNN.pdf)
			// aquí su nombre contiene el prefijo 'temp_'
			$ruta_digitalizacion_a_agregar = $directorio_desde . "temp_" . $digitalizacion_a_agregar;

			// Ruta de la digitalización DESCARGADA para unirle la agregada
			$ruta_digitalizacion_actual_descargada = $directorio_desde . $nombre_codificado . '_actual.pdf';

			// Ruta del resultado FINAL de la unión
			$ruta_digitalizacion_final = $directorio_desde . $nombre_codificado . '_final.pdf';

			// 1ro:
			// Se descarga la digitalización actual y se guarda en "proyectos/digital/"
			// con el nombre AATNNNNN_actual.pdf
			if (ftp_get($id_conexion, $ruta_digitalizacion_actual_descargada, $ruta_digitalizacion_existente, FTP_BINARY)) {
				// 2do:
				// Se unen las digitalizaciones
				$union_pdfs = $this->unirDigitalizaciones(
					$ruta_digitalizacion_actual_descargada,
					$ruta_digitalizacion_a_agregar,
					$ruta_digitalizacion_final
				);

				// Si se ha generado la unión de las digitalizaciones
				if (!is_null($union_pdfs)) {

					// Se elimina la digitalización EXISTENTE para cargar la unión de ambas digitalizaciones
					if (! ftp_delete($id_conexion, $ruta_digitalizacion_existente)) {
						$resultado[] = array(
							'archivo' => $digitalizacion_a_agregar,
							'estado' => 'ERROR',
							'mensaje' => 'No se pudo eliminar el documento existente, verifique que no se encuentre abierto en alg&uacute;n equipo.');
					} else {
						// Se carga la digitalización NUEVA (con la extensión en minúscula)
						if (ftp_put($id_conexion, $dir_actual . "/" . $nombre_codificado . ".pdf", $union_pdfs, FTP_BINARY)) {

							// Se elimina la digitalización que fue agregada
							// del directorio "proyectos/digital/temp_AATNNNNN.pdf"
							if (! ftp_delete($id_conexion, $ruta_digitalizacion_a_agregar)) {
								$resultado[] = array(
									'archivo' => $digitalizacion_a_agregar,
									'estado' => 'ERROR',
									'mensaje' => 'No se pudo eliminar la digitalización que fue agregada.');
							} else {
								// Se elimina la copia que se utilizó para unir con la digitalización agregada
								// la "AATNNNNN_actual.pdf"
								if (! ftp_delete($id_conexion, $ruta_digitalizacion_actual_descargada)) {
									$resultado[] = array(
										'archivo' => $digitalizacion_a_agregar,
										'estado' => 'ERROR',
										'mensaje' => 'No se pudo eliminar la copia que se utilizó para unir con la digitalización agregada.');
								} else {
									// Se elimina el resultado final, la "AATNNNNN_final.pdf"
									if (! ftp_delete($id_conexion, $ruta_digitalizacion_final)) {
										$resultado[] = array(
											'archivo' => $digitalizacion_a_agregar,
											'estado' => 'ERROR',
											'mensaje' => 'No se pudo eliminar la copia que se utilizó para unir con la digitalización agregada.');
									} else {
										$resultado[] = array(
											'archivo' => $digitalizacion_a_agregar,
											'estado' => 'OK',
											'mensaje' => "Se ha agregado la digitalizaci&oacute;n satisfactoriamente a la existente!");

										$exp = $this->obtenerExpedienteDesdeNombreCodificado($nombre_codificado);

										// Se audita la unión de las digitalizaciones
										NG::auditorias()->auditarComoExpediente(
											$exp,
											null,
											Auditoria::OP_ARCHIVO_AGREGADO,
											PATH_KRAKEN_RESOURCES_DIGITALIZACIONES_TEMPORALES,
											null, null,
											sprintf('Se ha agregado la digitalizacion %s', $digitalizacion_a_agregar)
										);
									}
								}
							}
						} else {
							$resultado[] = array(
								'archivo' => $digitalizacion_a_agregar,
								'estado' => 'ERROR',
								'mensaje' => "La uni&oacute;n de las digitalizaciones ha fallado!");
						}
					}
				} else {
					$resultado[] = array(
						'archivo' => $digitalizacion_a_agregar,
						'estado' => 'ERROR',
						'mensaje' => "La uni&oacute;n de las digitalizaciones ha fallado!");
				}
			} else {
				$resultado[] = array(
					'archivo' => $digitalizacion_a_agregar,
					'estado' => 'ERROR',
					'mensaje' => "La descarga de la copia en " . $ruta_digitalizacion_actual_descargada . " ha fallado!");
			}
		}

		// Se cierra la conexión FTP
		ftp_close($id_conexion);

		// Se cierra el directorio "digital/"
		closedir($directorio_abierto);

		return $resultado;
	}

	/**
	 * 24/02/2021 XXXX
	 * Se sube una propuesta (pdf) de participacion al directorio del expediente respectivo
	 * @param  Expediente $pExpediente       Expediente al cual asignar la propuesta
	 * @param  array      &$filesReference   Referencia al objeto de formulario que contiene la información del archivo.
	 * @return Expediente                    Expediente al cual se asignó la propuesta
	 */
	public function subirPropuestaParticipacion(Expediente $pExpediente, &$filesReference) {
		// Si no se recibió un error
		if ($filesReference['f_propuesta']['error'] != UPLOAD_ERR_NO_FILE) {
			try {
				// Nombre codificado del directorio del expediente respectivo
				$nombre_codificado = sprintf("%02d%s%05d", $pExpediente->anio % 100, $pExpediente->tipo, $pExpediente->numero);

				// Ruta del directorio remoto de públicos, determinado por la clave del expediente respectivo
				$directorio_expediente = PATH_KRAKEN_RESOURCES_PROYECTOS . $pExpediente->anio . "/" . $nombre_codificado . "/";

				// Nombre de la propuesta
				$nombre_propuesta = 'participacion.pdf';

				// Se establece una conexión FTP
				$id_conexion = ftp_connect('localhost');

				// Se establece el inicio de sesión FTP con usuario y password
				$resultado_login = ftp_login($id_conexion, FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);

				// Se cambia al directorio donde se quiere subir el archivo
				ftp_chdir($id_conexion, $directorio_expediente);

				$dir_actual = ftp_pwd($id_conexion);

				$archivoOrigen = $filesReference['f_propuesta']['tmp_name'];
				$archivoDestino = sprintf('%s%s', $directorio_expediente, $nombre_propuesta);

				if (ftp_put($id_conexion, $archivoDestino, $archivoOrigen, FTP_BINARY)) {
					// Auditoria
					NG::auditorias()->auditarComoExpediente(
						$pExpediente,
						null,
						Auditoria::OP_ARCHIVO_SUBIDO,
						sprintf('%s%s', $directorio_expediente, $nombre_propuesta), // en vez de tabla, utilizamos la ruta donde se guarda el archivo
						null, null,
						sprintf('Se ha subido la propuesta %s', $nombre_propuesta)
					);
				}

				ftp_close($id_conexion);

			} catch (Exception $ex) {
				throw new Exception('No se pudo subir la propuesta. Causa: ' . $ex->getMessage());
			}
		}

		return $pExpediente;
	}

	/**
	 * Se habilita un Expediente para la Participación Ciudadana
	 * @param  ExpedienteEnParticipacion $pExpedienteEnParticipacion [description]
	 * @return [type]                                                [description]
	 */
	public function habilitarExpedienteAParticipar(ExpedienteEnParticipacion $pExpedienteEnParticipacion) {

		if (is_null($pExpedienteEnParticipacion)) {
			throw new Exception(sprintf("Error en %s.habilitarExpedienteAParticipar: la instancia a guardar no puede ser nula.", get_class($this)));
		}

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {

			$id = DB::getInstanceDBExpedientes()->habilitarExpedienteAParticipar(
				$pExpedienteEnParticipacion->anio,
				$pExpedienteEnParticipacion->tipo,
				$pExpedienteEnParticipacion->numero,
				$pExpedienteEnParticipacion->cuerpo,
				$pExpedienteEnParticipacion->alcance,
				$pExpedienteEnParticipacion->fecha_inicio,
				$pExpedienteEnParticipacion->fecha_fin,
				$pExpedienteEnParticipacion->extracto,
				$pExpedienteEnParticipacion->id_usuario);

			// Auditoria
			NG::auditorias()->auditarComoExpediente(
				$pExpedienteEnParticipacion, null, Auditoria::OP_ALTA, 'hcd.expe_en_participacion', null, null,
				sprintf('Se ha habilitado el expediente %d-%s-%d-%d-%d para su participacion', $pExpedienteEnParticipacion->anio, $pExpedienteEnParticipacion->tipo, $pExpedienteEnParticipacion->numero, $pExpedienteEnParticipacion->cuerpo, $pExpedienteEnParticipacion->alcance)
			);

			// Se obtiene el siguiente número de orden del estado
			// No se conoce la fecha del estado,
			// sólo con la clave del expediente basta para obtener el siguiente número de orden del estado
			$nro_siguiente_orden_estado = $this->obtenerNumeroSiguienteEstado(
				$pExpedienteEnParticipacion->anio,
				$pExpedienteEnParticipacion->tipo,
				$pExpedienteEnParticipacion->numero,
				$pExpedienteEnParticipacion->cuerpo,
				$pExpedienteEnParticipacion->alcance,
				null);

			// Se crea una instancia de Estado, utilizando la información de ExpedienteEnParticipacion
			$estado = new Estado(
				$pExpedienteEnParticipacion->anio,
				$pExpedienteEnParticipacion->tipo,
				$pExpedienteEnParticipacion->numero,
				$pExpedienteEnParticipacion->cuerpo,
				$pExpedienteEnParticipacion->alcance,
				$pExpedienteEnParticipacion->fecha_inicio, // fecha_estado
				$nro_siguiente_orden_estado, // orden_estado
				90, // id_codestado = PARTICIPACIÓN CIUDADANA
				'AUTOMATICO', // observaciones_estado
				$pExpedienteEnParticipacion->id_usuario
			);

			// Se ingresa automáticamente el Estado 90 "PARTICIPACIÓN CIUDADANA", para el expediente respectivo
			// (se audita internamente el ingreso de dicho Estado)
			$estado_ingresado = $this->guardarEstado($estado, true);

			DB::getInstanceDBExpedientes()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.habilitarExpedienteAParticipar: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		$resultado = $pExpedienteEnParticipacion;

		DB::getInstanceDBExpedientes()->desconectar();

		if (is_null($resultado)) {
			throw new Exception(sprintf("Error grave en %s.habilitarExpedienteAParticipar: no se encuentra el contenido actualizado.", get_class($this)));
		}

		return $resultado;
	}

	/**
	 * Se obtienen los expedientes con estado 90 (en PPC) cuya fecha haya superado los 30 días.
	 * @return [type] [description]
	 */
	public function obtenerExpedientesEnPpcVencidos() {

		DB::getInstanceDBExpedientes()->conectar(false); // AutoCommit: false
		DB::getInstanceDBExpedientes()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = DB::getInstanceDBExpedientes()->obtenerExpedientesEnPpcVencidos();

			DB::getInstanceDBExpedientes()->guardarTransaccion();
		} catch (Exception $e) {
			DB::getInstanceDBExpedientes()->cancelarTransaccion();
			DB::getInstanceDBExpedientes()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerExpedientesEnPpcVencidos: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBExpedientes()->desconectar();

		return $resultado;
	}

	/**
	 * Se arma el contenido de la Etiqueta del expediente
	 * @param  [type] $e [description]
	 * @return [type]    [description]
	 */
	private function armarContenidoEtiqueta($e)
	{
		if ($e->tipo == 'E') $nombre_segun_tipo = 'Expediente';
		if ($e->tipo == 'N') $nombre_segun_tipo = 'Nota';
		if ($e->tipo == 'R') $nombre_segun_tipo = 'Recomendaci&oacute;n';

		$contenido = '';

		$contenido .= '<style>';
		$contenido .= '.etiqueta_general {';
		$contenido .= '    width: 370px;';
		$contenido .= '    min-height: 376px;';
		$contenido .= '    padding: 5px;';
		$contenido .= '    font-family: Arial;';
		$contenido .= '    font-size: 12px;';
		$contenido .= '    color: #000 !important;';
		$contenido .= '    border: solid 1px #000;';
		$contenido .= '}';
		$contenido .= '.etiqueta_fila {';
		$contenido .= '    padding-bottom: 7px; ';
		$contenido .= '}';
		$contenido .= '.etiqueta_fila_antecedente {';
		$contenido .= '    padding: 2px;';
		$contenido .= '    font-size: 10px;';
		$contenido .= '    border-bottom: 1px solid #000;';
		$contenido .= '}';
		$contenido .= '.etiqueta_subrayado {';
		$contenido .= '    text-decoration: underline;';
		$contenido .= '    color: #000 !important;';
		$contenido .= '    border-bottom: 0 !important;';
		$contenido .= '}';
		$contenido .= '</style>';

		$contenido .= '<br><br><br>';
		$contenido .= '<div class="etiqueta_general">';
		$contenido .= '<div class="etiqueta_fila">';
		$contenido .= '<span class="etiqueta_subrayado">'.$nombre_segun_tipo.' N&deg;</span>';
		$contenido .= '<strong>'.sprintf('%d-%s-%d', $e->numero, $e->iniciador_codigo, $e->anio).'</strong>';

		if ($e->cuerpo > 0 || $e->alcance > 0) {
			$contenido .= '&nbsp;&nbsp;<span class="etiqueta_subrayado">Cpo.</span>&nbsp;<strong>'.$e->cuerpo.'</strong>&nbsp;';
			$contenido .= '&nbsp;<span class="etiqueta_subrayado">Alc.</span>&nbsp;<strong>'.$e->alcance.'</strong>&nbsp;';

			$espacio = ($e->tipo == 'N') ? '50px' : '10px';
			// Se abrevia para que quepen en la misma fila
			$contenido .= '<span class="etiqueta_subrayado" style="margin-left:'.$espacio.'">F. Ingreso</span>';
		} else {
			$espacio = ($e->tipo == 'N') ? '120px' : '70px';
			$contenido .= '<span class="etiqueta_subrayado" style="margin-left:'.$espacio.'">Fecha Ingreso</span>';
		}
		$contenido .= '<strong>'.Validator::get()->convertirAFechaVista($e->fecha_entrada_expe).'</strong>';
		$contenido .= '</div>';

		$contenido .= '<div class="etiqueta_fila"><strong>'.$e->caratula.'</strong></div>';


		$contenido .= '<div class="etiqueta_fila"><span class="etiqueta_subrayado">Iniciador</span>:&nbsp;&nbsp;&nbsp;'.$e->iniciador_codigo.'&nbsp;'.$e->ro_iniciador_descripcion_grp.'</div>';

		$contenido .= '<div class="etiqueta_fila"><span class="etiqueta_subrayado">Autores</span>:&nbsp;&nbsp;&nbsp;';
		foreach ($e->autores as $a) {
			$separador_autores = (count($e->autores) > 1)
				? '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
				: '';
			$contenido .= $a->autor_codigo.'&nbsp;'.$a->ro_descripcion_grp.$separador_autores;
		}
		$contenido .= '</div>';

		$contenido .= '<div class="etiqueta_fila"><span class="etiqueta_subrayado">Categor&iacute;a</span>:&nbsp;'.$e->id_codcategoria.'&nbsp;'.$e->ro_descripcion_categoria.'</div>';

		// Por cada Proyecto
		foreach ($e->proyectos as $p) {
			$contenido .= '<div class="etiqueta_fila"><span class="etiqueta_subrayado">Proyecto</span>&nbsp;<strong>'.$p->orden_proyecto.'&nbsp;'.$p->ro_descripcion_proyecto.'</strong></div>';
			$contenido .= '<div class="etiqueta_fila" style="text-align: justify;"><strong>'.$p->extracto.'</strong></div>';
		}

		$contenido .= '<br><div class="etiqueta_fila"><span class="etiqueta_subrayado">Antecedentes</span></div>';

		$antecedentes_base = [];
		$antecedentes_gde = [];
		foreach ($e->antecedentes as $ant) {
			if ($ant->tipo_a == 'G')
				$antecedentes_gde[] = $ant;
			else
				$antecedentes_base[] = $ant;
		}

		if ( count($antecedentes_base) > 0 ) {

			$contenido .= '<div class="etiqueta_fila etiqueta_fila_antecedente">';
				$contenido .= '<span>N&uacute;mero|</span>';
				$contenido .= '<span>Tipo|</span>';
				$contenido .= '<span>A&ntilde;o&nbsp;&nbsp;&nbsp;|</span>';
				$contenido .= '<span>Dig.|</span>';
				$contenido .= '<span>Cpo.|</span>';
				$contenido .= '<span>Alc.|</span>';
				$contenido .= '<span>Cpo.Alc.|</span>';
				$contenido .= '<span>An.Alc.|</span>';
				$contenido .= '<span>Cpo.An.Alc.|</span>';
				$contenido .= '<span>An.|</span>';
				$contenido .= '<span>Cpo.An.</span>';
			$contenido .= '</div>';

			foreach ($antecedentes_base as $ant) {
				$contenido .= '<div class="etiqueta_fila etiqueta_fila_antecedente">';
				$contenido .= '<span>'.str_pad($ant->numero_a, 5, "0", STR_PAD_LEFT).'&nbsp;&nbsp;&nbsp;|</span>';
				$contenido .= '<span>'.$ant->tipo_a.'&nbsp;&nbsp;&nbsp;&nbsp;|</span>';
				$contenido .= '<span>'.$ant->anio_a.' |</span>';
				$contenido .= '<span>'.$ant->digito_a.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</span>';
				$contenido .= '<span>'.$ant->cuerpo_a.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</span>';
				$contenido .= '<span>'.$ant->alcance_a.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</span>';
				$contenido .= '<span>'.$ant->cuerpoalcance_a.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</span>';
				$contenido .= '<span>'.$ant->anexoalcance_a.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</span>';
				$contenido .= '<span>'.$ant->cuerpoanexoalcance_a.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</span>';
				$contenido .= '<span>'.$ant->anexo_a.'&nbsp;&nbsp;&nbsp;|</span>';
				$contenido .= '<span>'.$ant->cuerpoanexo_a.'</span>';
				$contenido .= '</div>';
			}
			$contenido .= '<br>';
		}

		if ( count($antecedentes_gde) > 0) {
			$contenido .= '<div class="etiqueta_fila etiqueta_fila_antecedente">';
				$contenido .= '<span>GDE</span>';
			$contenido .= '</div>';

			foreach ($antecedentes_gde as $ant) {
				$contenido .= '<div class="etiqueta_fila etiqueta_fila_antecedente">';
				$contenido .= '<span>'.$ant->observaciones_antecedentes.'</span>';
				$contenido .= '</div>';
			}
		}


		$contenido .= '<br><div><span class="etiqueta_subrayado">Observaciones</span>:&nbsp;'.$e->observaciones_expe.'</div>';
		$contenido .= '</div>';

		return $contenido;
	}

	/**
	 * [generarArchivoCaratula description]
	 * @param  [type] $pExpediente [description]
	 * @param  [type] $pUsuario    [description]
	 * @return [type]              [description]
	 */
	public function generarArchivoCaratula($pExpediente, $pUsuario)
	{
		$contenido = $this->armarContenidoEtiqueta($pExpediente);

		$nombre_archivo_nuevo = sprintf('%scaratula_%s_%s.pdf',
			PATH_SGL_DOC_TEMPORALES,
			DateTimeHelper::get()->timestampStr('YmdHisu'),
			sha1($contenido)
		);

		PDFComposer::get()->setOptions(['title' => 'Carátula']);

		// Se genera el pdf
		$errores = PDFComposer::get()->generarPDF($contenido, $nombre_archivo_nuevo);

		// Si hay error, lanzo excepcion
		if (count($errores) > 0)
			throw new Exception(sprintf("Error en %s.generarArchivoCaratula: %s", get_class($this), implode('; ', $errores)));

		// Devuelvo el nombre del archivo generado (ruta completa)
		return PDFComposer::get()->getLastOutput();
	}

	/**
	 * Toma una caratula ya generada en formato pdf, se firma y se agrega al expediente.
	 * @param  [type] $archivoCaratula [description]
	 * @param  [type] $pExpediente     [description]
	 * @param  [type] $pUsuario        [description]
	 * @return [type]                  [description]
	 */
	public function agregarCaratula($archivoCaratula, $pExpediente, $pUsuario)
	{
		if (!file_exists($archivoCaratula))
			$errores[] = 'Error al generar el archivo pdf de la carátula.';

		// Determino la ruta del archivo de salida
		$archivo_firmado = NG::actuacionBase()->obtenerArchivoSalida($pExpediente, false, false);
		if ($archivo_firmado == '') return ['No se pudo obtener el nombre del archivo de salida.'];

		// Firmo el documento
		$errores = NG::actuacionBase()->firmarPDF($archivoCaratula, $archivo_firmado, $pUsuario);
		if (count($errores) > 0) return $errores;

		// Extraigo la ruta base del archivo
		$archivo_ref = preg_replace(sprintf('|^%s|',PATH_KRAKEN_RESOURCES_PROYECTOS), '', $archivo_firmado);

		// Agrego una entrada al expediente electrónico
		// (el orden y la fecha/hora se recalculan)
		$expe_elec = NG::expedientesElec()->agregarDocumentoElectronico(
			new ExpedienteElec(
				$pExpediente->anio,
				$pExpediente->tipo,
				$pExpediente->numero,
				$pExpediente->cuerpo,
				$pExpediente->alcance,
				null,
				'expediente_alta', // Simulamos un tipo de actuación
				'Carátula',
				$archivo_ref,
				($archivo_firmado != '') ? hash_file('sha256', $archivo_firmado) : '',
				'', // Sin referencia al texto original
				false, // No lo alcanza el Decreto 1404
				false, // No es archivo con embebidos
				true, // es carátula
				null,
				$pUsuario->id_usuario,
				'' // Sin observaciones
			)
		);

		// Agrego la 'firma inicial' del usuario que anexa el documento
		NG::firmasExpedienteElec()->agregarFirmasDocumentoElectronico($expe_elec, $pUsuario, [$pUsuario], 'firmado');

		// Retorna el nuevo documento del expediente electrónico
		return $expe_elec;
	}

	/**
	 * Verifica si un expediente está Agregado a otro expediente
	 * @param  Expediente $pExpediente [description]
	 * @return [type]                  [description]
	 */
	public function estaAgregadoA(Expediente $pExpediente)
	{
		if (is_null($pExpediente->anio) || is_null($pExpediente->tipo) || is_null($pExpediente->numero) || is_null($pExpediente->cuerpo) || is_null($pExpediente->alcance)) {
			throw new Exception(sprintf(
				"Error en %s.estaAgregadoA: los campos clave no pueden ser nulos.",
				get_class($this)
			));
		}

		DB::getInstanceDBExpedientes()->conectar();

		$resultado = DB::getInstanceDBExpedientes()->estaAgregadoA(
			$pExpediente->anio,
			$pExpediente->tipo,
			$pExpediente->numero,
			$pExpediente->cuerpo,
			$pExpediente->alcance
		);

		DB::getInstanceDBExpedientes()->desconectar();

		return (count($resultado) > 0);
	}
}
?>

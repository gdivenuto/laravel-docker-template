<?php
/**
 * Capa de negocio para funcionalidad de ActuacionExpedienteSubirZip.
 *
 * @author XXXX, XXXX
 *
 */
class NGActuacionExpedienteSubirZip extends NGActuacionBase {

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
	// ---- Inicialización de Actuaciones -------------------------------------
	// ------------------------------------------------------------------------

	/**
	 * Tomar una actuacion y ejecuta su funcion de inicialización.
	 * @param  Actuacion    $actuacion Actuacion a procesar
	 * @param  Usuario|null $pUsuario  Usuario asociado a la ejecución de la actuacion.
	 * @return Array                Lista de errores encontrados durante el procesamiento.
	 */
	public function inicializarActuacion(Actuacion $actuacion, Usuario $pUsuario = null)
	{
		// Es expediente Agregado?
		$errores = $this->esExpedienteAgregado($actuacion);
		if (count($errores) > 0)
			return $errores;
		else
			return $this->guardarUltimoOrdenExpedienteElec($actuacion, $pUsuario);
	}

	// ------------------------------------------------------------------------
	// ---- Verificación de Actuaciones ---------------------------------------
	// ------------------------------------------------------------------------

	/**
	 * Tomar una actuacion y ejecuta su funcion de verificación.
	 * @param  Actuacion    $actuacion Actuacion a verificar.
	 * @param  Usuario|null $pUsuario  Usuario asociado a la verificación de la actuacion.
	 * @return Array                   Lista de errores encontrados durante la verificación.
	 */
	public function verificarActuacion(Actuacion $actuacion, Usuario $pUsuario = null)
	{
		// Verifico que no hayan habido cambios en el expediente electronico
		// mientras se estaba realizando esta actuación.
		if ($this->huboCambiosEnExpedienteElec($actuacion))
			return [sprintf("El expediente electrónico '%s-%s-%s cpo. %s alc. %s' ha sufrido modificaciones mientras se estaba llevando a cabo esta actuación. Por favor, verifique.", $actuacion->parametros['anio'], $actuacion->parametros['tipo'], $actuacion->parametros['numero'], $actuacion->parametros['cuerpo'], $actuacion->parametros['alcance'])];

		return [];
	}

	// ------------------------------------------------------------------------
	// ---- Procesamiento de Actuaciones --------------------------------------
	// ------------------------------------------------------------------------

	/**
	 * Toma una actuacion y ejecuta su funcion de validacion y procesamiento en base
	 * a las transacciones generadas durante el wizard.
	 * @param  Actuacion    $actuacion Actuacion a procesar
	 * @param  Usuario|null $pUsuario  Usuario asociado a la ejecución de la actuacion.
	 * @return Array                Lista de errores encontrados durante el procesamiento.
	 */
	public function procesarActuacion(Actuacion $actuacion, Usuario $pUsuario = null)
	{
		// Extra: obtengo el expediente de trabajo
		$expediente = NG::expedientes()->obtenerExpediente(
			$actuacion->parametros['anio'],
			$actuacion->parametros['tipo'],
			$actuacion->parametros['numero'],
			$actuacion->parametros['cuerpo'],
			$actuacion->parametros['alcance']
		);
		if (is_null($expediente))
			return [sprintf("El expediente '%s-%s-%s cpo. %s alc. %s' no existe.", $actuacion->parametros['anio'], $actuacion->parametros['tipo'], $actuacion->parametros['numero'], $actuacion->parametros['cuerpo'], $actuacion->parametros['alcance'])];

		// Obtengo las transacciones de los pasos de la actuacion
		$trans = NG::transacActuaciones()->obtenerTransacActuaciones($actuacion->id_transaccion);

		// Debo tener la misma cantidad de transacciones que de pasos
		if (count($trans) != count($actuacion->pasos))
			return [sprintf('Error en transacción %s: la cantidad de pasos difiere.', $actuacion->id_transaccion)];

		// Paso 0: Subir Archivo (lote de archivos)
		$data = json_decode($trans[0]->data);
		$lote_archivos_subidos = $data->lote_archivos_subidos;

		// Paso 1: Confirmar Firma de Lote
		$data = json_decode($trans[1]->data);
		$firma_confirmada = $data->f_op_confirmacion == 1;
		$conservar_lote_original = $actuacion->pasos[1]->opciones['conservar_lote_original'];

		// Paso 2: Selección de Revisores
		$data = json_decode($trans[2]->data);
		$requiere_revision = $data->f_requiere_revision == 1;
		$revisores = $data->f_revisores;

		// Paso 3: Selección de Firmantes
		$data = json_decode($trans[3]->data);
		$firmantes = $data->f_firmantes;

		// Paso 4: Observaciones
		$data = json_decode($trans[4]->data);
		$observaciones = $data->f_texto;

		// ---- Verifico que todo este bien -----------------------------------
		if (!$firma_confirmada) return ['Firma de lote de documentos no aceptada.'];
		if (count($lote_archivos_subidos) == 0) return ['No hay archivos en el lote de documentos para firma.'];
		foreach ($lote_archivos_subidos as $archivo_subido)
			if (! file_exists($archivo_subido))
				return [sprintf('El documento para firma "%s" no existe.', basename($archivo_subido))];

		// ---- Procesamiento del lote ----------------------------------------
		foreach ($lote_archivos_subidos as $archivo_subido) {
			// Determino la ruta del archivo de salida
			// Los documentos agregados por lote se consideran alcanzados por el Dec 1404
			$archivo_firmado = $this->obtenerArchivoSalida($expediente, $requiere_revision, true);
			if ($archivo_firmado == '') return ['No se pudo obtener el nombre del archivo de salida.'];

			// Firmo el documento
			$errores = $this->firmarPDF($archivo_subido, $archivo_firmado, $pUsuario);
			if (count($errores) > 0) return $errores;

			// Genero el detalle del documento electronico
			$detalle_texto = pathinfo($archivo_subido, PATHINFO_FILENAME);

			// Si requiere revision, lo envio a revisar; sino lo agrego directamente
			if ($requiere_revision) {
				// Agrego una entrada a documentos pendientes de revision
				try {
					$expe_elec_pend = $this->agregarAExpedienteElectronicoPendiente(
						$expediente,
						$actuacion,
						$pUsuario,
						$archivo_firmado,
						null,  // El texto original es nulo en archivos subidos
						true,  // Los agregados por lote son siempre alcanzados
						false, // No es caratula
						$detalle_texto,
						$observaciones,
						$revisores,
						$firmantes
					);
				} catch (Exception $e) {
					return [sprintf('Error al agregar entrada pendiente de revisión al expediente electrónico: %s.', $e->getMessage())];
				}

				if (is_null($expe_elec_pend)) return ['Error al agregar entrada pendiente de revisión al expediente electrónico: entrada nula.'];

			} else {
				// Agrego una entrada al expediente electronico
				try {
					$expe_elec = $this->agregarAExpedienteElectronico(
						$expediente,
						$actuacion,
						$pUsuario,
						$archivo_firmado,
						null,  // El texto original es nulo en archivos subidos
						true,  // Los agregados por lote son siempre alcanzados
						false, // No es caratula
						$detalle_texto,
						$observaciones,
						$firmantes
					);
				} catch (Exception $e) {
					return [sprintf('Error al agregar entrada al expediente electrónico: %s.', $e->getMessage())];
				}

				if (is_null($expe_elec)) return ['Error al agregar entrada al expediente electrónico: entrada nula.'];
			}

			// Elimino el original
			if (!$conservar_lote_original) {
				FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);
				FTPHelper::get()->delete($archivo_subido);
				FTPHelper::get()->disconnect();
			}

			// Completo la info para auditoria, si aplica
			$actuacion->info_auditoria = [
				'Revision' => ($requiere_revision) ? 'si' : 'no',
				'Orden' => ($requiere_revision) ? $expe_elec_pend->orden : $expe_elec->orden,
				'Detalle' => $detalle_texto,
				'Firma confirmada' => ($firma_confirmada) ? 'si' : 'no',
				'Archivo firmado' => $archivo_firmado,
				'Observaciones' => $observaciones
			];
		}

		return [];
	}

}
?>

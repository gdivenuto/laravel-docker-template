<?php
/**
 * Capa de negocio para funcionalidad de ActuacionExpeElecCaratular.
 *
 * @author XXXX, XXXX
 *
 */
class NGActuacionExpeElecCaratular extends NGActuacionBase {

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
		// Extra: obtengo el expediente de trabajo
		$expediente = NG::expedientes()->obtenerExpediente(
			$actuacion->parametros['anio'],
			$actuacion->parametros['tipo'],
			$actuacion->parametros['numero'],
			$actuacion->parametros['cuerpo'],
			$actuacion->parametros['alcance'],
			true // Instancias completas
		);
		if (is_null($expediente))
			return [sprintf("El expediente '%s-%s-%s cpo. %s alc. %s' no existe.", $actuacion->parametros['anio'], $actuacion->parametros['tipo'], $actuacion->parametros['numero'], $actuacion->parametros['cuerpo'], $actuacion->parametros['alcance'])];

		// Es expediente Agregado?
		$errores = $this->esExpedienteAgregado($actuacion, $expediente);
		if (count($errores) > 0)
			return $errores;

		// Con el expediente "en mano", genero el documento de la caratula (firmado por el usuario actual)
		try {
			$archivo_generado = NG::expedientes()->generarArchivoCaratula($expediente, $pUsuario);
		} catch (Exception $e) {
			return [sprintf("Error en %s.inicializarActuacion: %s", get_class($this), $e->getMessage())];
		}
		if (!file_exists($archivo_generado))
			$errores[] = 'Error al generar el archivo pdf de la carátula.';

		// Actualizo el parámetro del paso 0: Confirmar Firma
		// Como la ruta viene completa, saco el nombre de archivo y le agrego el URL de documentos temporales
		$actuacion->getPaso(0)->opciones['documento_a_firmar']['ruta_documento_a_firmar'] = basename($archivo_generado);

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

		// Paso 0: Confirmar Firma
		$data = json_decode($trans[0]->data);
		$firma_confirmada = $data->f_op_confirmacion == 1;
		$conservar_original = $actuacion->pasos[0]->opciones['conservar_documento_original'];

		// Paso 1: Selección de Revisores
		$data = json_decode($trans[1]->data);
		$requiere_revision = $data->f_requiere_revision == 1;
		$revisores = $data->f_revisores;

		// Paso 2: Observaciones
		$data = json_decode($trans[2]->data);
		$observaciones = $data->f_texto;

		// Verifico que todo este bien
		$archivo_para_firma = PATH_SGL_DOC_TEMPORALES.$actuacion->getPaso(0)->opciones['documento_a_firmar']['ruta_documento_a_firmar'];
		if (!file_exists($archivo_para_firma)) return ['No se encuentra el archivo a firmar.'];
		if (!$firma_confirmada) return ['Firma de documento no aceptada.'];

		// Determino la ruta del archivo de salida
		try {
			$archivo_firmado = $this->obtenerArchivoSalida(
				$expediente,         // Expediente a caratular
				$requiere_revision,  // Requiere revision?
				false                // La caratula nunca es alcanzada por el art 11 dec 1404
			);
			if ($archivo_firmado == '') return ['No se pudo obtener el nombre del archivo de salida.'];
		} catch (Exception $e) {
			return [sprintf('No se pudo obtener el nombre del archivo de salida: %s.', $e->getMessage())];
		}

		// Firmo el documento
		$errores = $this->firmarPDF($archivo_para_firma, $archivo_firmado, $pUsuario);
		if (count($errores) > 0) return $errores;

		// Si requiere revision, lo envio a revisar; sino lo agrego directamente
		if ($requiere_revision) {
			// Agrego una entrada a documentos pendientes de revision
			try {
				$expe_elec_pend = $this->agregarAExpedienteElectronicoPendiente(
					$expediente,
					$actuacion,
					$pUsuario,
					$archivo_firmado,
					'',           // sin $texto_original
					false,        // no lo $alcanza_dec1404,
					true, 		  // Es caratula!
					'Carátula',   // $detalle_texto,
					$observaciones,
					$revisores,
					[]            // $firmantes
				);
			} catch (Exception $e) {
				return [sprintf('Error al agregar entrada pendiente de revisión al expediente electrónico: %s.', $e->getMessage())];
			}

			if (is_null($expe_elec_pend)) return ['Error al agregar entrada pendiente de revisión al expediente electrónico: entrada nula.'];

		} else {
			// Agrego una entrada al expediente electronico con sus firmas asociadas
			try {
				// Agrego una entrada al expediente electronico
				$expe_elec = $this->agregarAExpedienteElectronico(
					$expediente,
					$actuacion,
					$pUsuario,
					$archivo_firmado,
					'',           // sin $texto_original
					false,        // no lo $alcanza_dec1404,
					true,         // Es caratula!
					'Carátula',   // $detalle_texto,
					$observaciones,
					[]            // $firmantes
				);
			} catch (Exception $e) {
				return [sprintf('Error al agregar entrada al expediente electrónico: %s.', $e->getMessage())];
			}

			if (is_null($expe_elec)) return ['Error al agregar entrada al expediente electrónico: entrada nula.'];
		}

		// Elimino el original
		if (!$conservar_original) {
			FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);
			FTPHelper::get()->delete($archivo_para_firma);
			FTPHelper::get()->disconnect();
		}

		// Completo la info para auditoria, si aplica
		$actuacion->info_auditoria = [
			'Revision' => ($requiere_revision) ? 'si' : 'no',
			'Orden' => ($requiere_revision) ? $expe_elec_pend->orden : $expe_elec->orden,
			'Detalle' => 'Carátula',
			'Firma confirmada' => ($firma_confirmada) ? 'si' : 'no',
			'Archivo firmado' => $archivo_firmado,
			'Observaciones' => $observaciones
		];

		return [];
	}
}
?>

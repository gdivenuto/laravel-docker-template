<?php
/**
 * Capa de negocio para funcionalidad de ActuacionExpedienteComponerPdf.
 *
 * @author XXXX, XXXX
 *
 */
class NGActuacionExpedienteComponerPdf extends NGActuacionBase {

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

		// Paso 0: Seleccionar plantilla
		// (No hay datos relevantes de este paso para procesar la actuacion
		// poque ya fueron utilizados durante el wizard)
		//$data = json_decode($trans[0]->data);

		// Paso 1: Editar texto
		$data = json_decode($trans[1]->data);
		$detalle_texto = $data->f_titulo;
		$texto_original = $data->f_texto;
		$archivo_generado = $data->archivo_generado;

		// Paso 2: Confirmar Firma
		$data = json_decode($trans[2]->data);
		$firma_confirmada = $data->f_op_confirmacion == 1;
		$conservar_original = $actuacion->pasos[2]->opciones['conservar_documento_original'];

		// Paso 3: Selección de Revisores
		$data = json_decode($trans[3]->data);
		$requiere_revision = $data->f_requiere_revision == 1;
		$revisores = $data->f_revisores;

		// Paso 4: Selección de Firmantes
		$data = json_decode($trans[4]->data);
		$firmantes = $data->f_firmantes;

		// Paso 5: Alcanzado por Dec. 1404
		$data = json_decode($trans[5]->data);
		$alcanza_dec1404 = $data->f_op_decreto == 1;

		// Paso 6: Observaciones
		$data = json_decode($trans[6]->data);
		$observaciones = $data->f_texto;

		// Verifico que todo este bien
		if (!file_exists($archivo_generado)) return ['No se encuentra el archivo a firmar.'];
		if (!$firma_confirmada) return ['Firma de documento no aceptada.'];

		// Determino la ruta del archivo de salida
		$archivo_firmado = $this->obtenerArchivoSalida($expediente, $requiere_revision, $alcanza_dec1404);
		if ($archivo_firmado == '') return ['No se pudo obtener el nombre del archivo de salida.'];

		// Firmo el documento
		$errores = $this->firmarPDF($archivo_generado, $archivo_firmado, $pUsuario);
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
					$texto_original,
					$alcanza_dec1404,
					false, // no es carátula
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
			// Agrego una entrada al expediente electronico con sus firmas asociadas
			try {
				// Agrego una entrada al expediente electronico
				$expe_elec = $this->agregarAExpedienteElectronico(
					$expediente,
					$actuacion,
					$pUsuario,
					$archivo_firmado,
					$texto_original,
					$alcanza_dec1404,
					false, // no es caratula
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
		if (!$conservar_original) {
			FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);
			FTPHelper::get()->delete($archivo_generado);
			FTPHelper::get()->disconnect();
		}

		// Completo la info para auditoria, si aplica
		$actuacion->info_auditoria = [
			'Revision' => ($requiere_revision) ? 'si' : 'no',
			'Orden' => ($requiere_revision) ? $expe_elec_pend->orden : $expe_elec->orden,
			'Detalle' => $detalle_texto,
			'Firma confirmada' => ($firma_confirmada) ? 'si' : 'no',
			'Alcanza dec1404' => ($alcanza_dec1404) ? 'si' : 'no',
			'Archivo firmado' => $archivo_firmado,
			'Observaciones' => $observaciones
		];

		return [];
	}

}
?>

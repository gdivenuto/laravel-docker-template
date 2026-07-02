<?php
/**
 * Capa de negocio base para funcionalidad de PasoActuacionSeleccionPlantillaTexto.
 *
 * @author XXXX
 *
 */
class NGPasoActuacionSeleccionPlantillaTexto extends NGPasoActuacionBase {

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
	// ---- Asignacion de datos de Pasos --------------------------------------
	// ------------------------------------------------------------------------
	/**
	 * Delega la lógica de obtencion de datos para que un paso determinado
	 * disponga de todo lo necesario para generar su vista, por ejemplo, una consulta
	 * a la BD con los posibles firmantes.
	 * @param  Actuacion     $actuacion [description]
	 * @param  PasoActuacion $paso   [description]
	 * @param  Usuario       $pUsuario  [description]
	 */
	public function asignarDatosAPasoActuacion(Actuacion $actuacion, PasoActuacion $paso, Usuario $pUsuario)
	{
		// Levanto todas las plantillas (*.xml) definidas en el directorio
		// utilizado como repositorio.
		$paso->datos['plantillas'] = [];
		$filtro_destino = $paso->opciones['destino_plantillas'];

		foreach (scandir($paso->opciones['path_plantillas']) as $p) {
			if (preg_match('/\.xml$/i', basename($p))) {
				$xml_data = new SimpleXMLElement(file_get_contents($paso->opciones['path_plantillas'].$p));
				if (property_exists($xml_data, 'config')) {
					$pconf = json_decode($xml_data->config);

					//filtro por destino
					if (($filtro_destino == 'todos') ||
						($pconf->plantilla->destino == 'todos') ||
						($filtro_destino == $pconf->plantilla->destino)
					) {
						$paso->datos['plantillas'][] = [
							'plantilla' => preg_replace('/\.xml$/i', '', $p),
							'categoria' => $pconf->plantilla->categoria,
							'nombre' => $pconf->plantilla->nombre,
							'descripcion' => $pconf->plantilla->descripcion
						];
					}
				}
			}
		}

		usort($paso->datos['plantillas'], function($a, $b) {
			// Comparo 'categoria+nombre' para listar.
			return strcmp($a['categoria'].$a['nombre'], $b['categoria'].$b['nombre']);
		});
	}

	// ------------------------------------------------------------------------
	// ---- Procesamiento de Pasos --------------------------------------------
	// ------------------------------------------------------------------------

	/**
	 * Toma un paso en particular y ejecutar su funcion de validacion y
	 * procesamiento (guardar transaccion) en base a los parametros recopilados.
	 * @param  Actuacion     $actuacion [description]
	 * @param  PasoActuacion $paso   [description]
	 * @param  Usuario       $pUsuario  [description]
	 * @param  Array         $params Parámetros del paso, por referencia (esto permite modificar los parametros desde el procesamiento del paso).
	 * @return Array                 Array de errores detectados; si es '[]', no hay errores.
	 */
	public function procesarPasoActuacion(Actuacion $actuacion, PasoActuacion $paso, Usuario $pUsuario, &$params)
	{
		$ops = $paso->opciones;

		$errores = [];

		// Verifico campos 'estaticos' del paso
		$errores = $this->verificarExistenciaParametros(['f_plantilla'], $params);
		if (count($errores) > 0) return $errores;

		// Si no tengo una plantilla seleccionada, no tengo nada mas que hacer
		if ($params['f_plantilla'] == '')
			return [];

		// Verifico si existe la plantilla
		$archivo_plantilla = sprintf('%s%s.xml', $ops['path_plantillas'], $params['f_plantilla']);
		if (!file_exists($archivo_plantilla))
			return [sprintf('El archivo de plantilla "%s" no existe.', basename($archivo_plantilla))];


		// Proceso la plantilla y guardo el texto resultante
		try {
			// Agrego como campos predefinidos de la plantilla.
			$campos_predefinidos = [
				'pred_expediente_etiqueta' => $actuacion->generarTextoInformativo(),
				'pred_expediente_etiqueta_extendida' => $actuacion->generarTextoInformativo(true),
				'pred_usuario_nombre' => $pUsuario->nombre_usuario,
				'pred_usuario_legajo' => $pUsuario->u_legajo,
				'pred_usuario_iniciales' => $pUsuario->iniciales_usuario
			];

			$templator = new Templator($archivo_plantilla, $campos_predefinidos);

			// Verifico el resto de los campos, serverside
			$errores = $templator->validarCampos($params);
			if (count($errores) > 0)
				return $errores;

			// Funcionalidad dependiendo del tipo de destino
			switch ($paso->opciones['destino_texto']['destino']) {
				case 'ninguno':
					break;

				case 'paso_editor_texto':
					// ---- Obtengo la transacción 'destino' definida en el paso.
					$transac = NG::transacActuaciones()->obtenerTransacActuacion(
						$paso->id_transaccion,
						$paso->opciones['destino_texto']['id_paso']
					);

					$data = json_decode($transac->data, true); // Datos de la transaccion

					// Modifico el campo 'titulo' y 'texto' definido en el paso.
					if ($paso->opciones['destino_texto']['parametro_titulo'] != '')
						$data[$paso->opciones['destino_texto']['parametro_titulo']] = $templator->plantilla_config->plantilla->nombre;

					if ($paso->opciones['destino_texto']['parametro_texto'] != '')
						$data[$paso->opciones['destino_texto']['parametro_texto']] = $templator->generarTexto($params);

					// ---- Guardo la transaccion
					$transac->data = json_encode($data);
					$transac = NG::transacActuaciones()->guardarTransacActuacion($transac);

					break;
			}

		} catch (Exception $e) {
			return [sprintf('Error al procesar plantilla "%s": %s.', basename($archivo_plantilla), $e->getMessage())];
		}

		/*
		// Verifico las expresiones regulares de los campos
		$errores = $this->verificarExistenciaParametros(['f_op_decreto'], $params);
		if (count($errores) > 0) return $errores;

		if (!in_array($params['f_op_decreto'], [0, 1]))
			$errores[] = 'Debe definir si el documento es alcanzado por el Art. 11 Decreto 1404.';
		*/
		return $errores;
	}
}
?>

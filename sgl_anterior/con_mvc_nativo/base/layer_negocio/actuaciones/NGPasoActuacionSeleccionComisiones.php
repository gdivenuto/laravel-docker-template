<?php
/**
 * Capa de negocio base para funcionalidad de PasoActuacionSeleccionComisiones.
 *
 * @author XXXX
 *
 */
class NGPasoActuacionSeleccionComisiones extends NGPasoActuacionBase {

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
		// Determino si muestro o no el campo de "Para considerar Participación Ciudadana"
		// Esta verificación se hace cando la actuación tiene como parámetro un Expediente/Nota.
		// Por eso verificamos todos los paramétros y no solo el campo tipo.
		if (array_key_exists('anio', $actuacion->parametros) &&
			array_key_exists('tipo', $actuacion->parametros) &&
			array_key_exists('numero', $actuacion->parametros) &&
			array_key_exists('cuerpo', $actuacion->parametros) &&
			array_key_exists('alcance', $actuacion->parametros))

			$paso->datos['ocultar_ppc'] = $actuacion->parametros['tipo'] != 'N';
		else
			$paso->datos['ocultar_ppc'] = true;

		// Se obtienen las Comisiones (Lugares de tipo C y habilitadas)
		$data = NG::expedientesParam()->obtenerLugares(
			'C',
			null, null, null, null, null, null, null, null,
			'1',
			null,
			array('descripcion_grp'),
			null,
			null);

		$comisiones = [];

		// Nos quedamos solamente con el Código y la Descripción, el Tipo siempre es 'C'
		foreach ($data as $d)
			$comisiones[] = [
				'codigo_grp' => $d->codigo_grp,
				'descripcion_grp' => trim($d->descripcion_grp)
			];

		$paso->datos['comisiones'] = $comisiones;
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

		// Verifico la existencia obligatoria de los parametros
		$errores = $this->verificarExistenciaParametros(['f_comisiones', 'f_observaciones'], $params);
		if (count($errores) > 0) return $errores;

		// Fix para el campo ppc: como es un checkbox, si no está tildado no llega
		if (array_key_exists('f_ppc', $params)) {
			if (! in_array($params['f_ppc'], [0, 1]) )
				return ["Parámetro f_ppc inválido"];
		} else {
			$params['f_ppc'] = 0;
		}

		// Obtengo las comisiones (con un fix para comisiones vacías)
		if (!is_array($params['f_comisiones']))
			$params['f_comisiones'] = []; // actualizo los parámetros del paso (que llevará a actualizar la transacción)

		$comisiones = $params['f_comisiones'];

		// Obtengo las observaciones (con un fix para observaciones vacías)
		if (!is_array($params['f_observaciones']))
			$params['f_observaciones'] = []; // actualizo los parámetros del paso (que llevará a actualizar la transacción)

		$observaciones = $params['f_observaciones'];

		if (count($comisiones) < count($observaciones))
			return ["Inconsistencia entre cantidad de observaciones y comisiones."];

	    // Verifico cantidad mínima de comisiones
	    if (count($comisiones) < $ops['cantidad_minima']) {
	        $errores[] = sprintf(
	        	'Debe elegir al menos %s %s.',
	        	$ops['cantidad_minima'],
	        	(count($comisiones) == 1) ? 'comisión' : 'comisiones');
	    }
		if (count($errores) > 0) return $errores;

		// Verifico si necesito generar la providencia de los giros
	    if ($ops['generar_providencia_pdf']['generar_providencia'])
	    {
	    	if (!is_dir($ops['generar_providencia_pdf']['ruta_archivo'])) {
	    		$errores[] = sprintf("La ruta de salida '%s' no existe", $ops['generar_providencia_pdf']['ruta_archivo']);
	    	} else {
	    		// ---- Generación del contenido de la providencia
	    		$contenido = $this->generarContenidoActaGiroComisiones(
	    			$actuacion,
	    			$comisiones,
	    			$ops['generar_providencia_pdf']['tipo_providencia']
	    		);

	    		switch ($ops['generar_providencia_pdf']['tipo_providencia']) {
	    			case 'confirmar_giros':
	    				$titulo = 'Giros a comisiones para %s-%s-%s cpo %s alc %s';
	    				break;

	    			case 'convalidar_giros':
	    				$titulo = 'Convalidar giros para %s-%s-%s cpo %s alc %s';
	    				break;

	    			default:
	    				$titulo = '%s-%s-%s cpo %s alc %s';
	    				break;
	    		}

	    		// ---- Generación de la providencia
				$nombre_archivo_nuevo = sprintf('%s%d_%d_%s_%s.pdf',
					$ops['generar_providencia_pdf']['ruta_archivo'],
					$paso->id_transaccion,
					$paso->id_paso,
					DateTimeHelper::get()->timestampStr('YmdHisu'),
					sha1($contenido)
				);
	    		PDFComposer::get()->setOptions([
	    			'title' => sprintf($titulo,
	    				$actuacion->parametros['anio'],
	    				$actuacion->parametros['tipo'],
	    				$actuacion->parametros['numero'],
	    				$actuacion->parametros['cuerpo'],
	    				$actuacion->parametros['alcance']
	    			)
	    		]);
				$errores = PDFComposer::get()->generarPDF($contenido, $nombre_archivo_nuevo);

				if (count($errores) == 0) {
					$archivo_salida = PDFComposer::get()->getLastOutput();
					if (!file_exists($archivo_salida))
						$errores[] = 'Error al generar el archivo pdf.';

					// Guardo el archivo generado en la transaccion
					$params['archivo_generado'] = $archivo_salida;
				}
	    	}
	    }

		return [];
	}

	/**
	 * Se genera el contenido del Acta de Giro a Comisiones
	 * @param  [array] $actuacion  Información de la actuación
	 * @param  [array] $comisiones Código de las comisiones recibidas
	 * @param  string $tipo_providencia Tipo de providencia a generar: 'confirmar_giros' o 'convalidar_giros'
	 * @return [type]                   [description]
	 */
	private function generarContenidoActaGiroComisiones($actuacion, $comisiones, $tipo_providencia = 'confirmar_giros')
	{
		$nombre_tipo = ($actuacion->parametros['tipo'] == 'E') ? "EXPEDIENTE" : "NOTA";

		$fecha = DateTimeHelper::mostrarFechaLetras(date("Y-m-d"));

		foreach($comisiones as $v)
			$data[] = NG::expedientesParam()->obtenerLugar('C', $v);

		$nombres = '<ol>';
		foreach($data as $v) $nombres .= '<li>&nbsp;&nbsp;&nbsp;&nbsp;'.$v->descripcion_grp.'</li>';
		$nombres .= '</ol>';

		// Dependiendo del tipo de providencia, cambio el texto:
		switch ($tipo_providencia) {
			case 'confirmar_giros':
				$texto_providencia = "Atento a lo establecido en el art&iacute;culo 30&deg; del Reglamento Interno, g&iacute;rese el presente a las comisiones de";
				break;

			case 'convalidar_giros':
				$texto_providencia = "Atento a lo establecido en el art&iacute;culo 30&deg; del Reglamento Interno, se giran las presentes actuaciones para su convalidaci&oacute;n";
				break;
		}

		return <<<HTML
			<div style="width:100%;border:1px solid #000;margin-top: 15px;padding:5px;text-align: center">
				MESA DE ENTRADAS HONORABLE CONCEJO DELIBERANTE
				<br>
				{$nombre_tipo}&nbsp;N&deg;&nbsp;&nbsp;{$actuacion->parametros['numero']}&nbsp;&nbsp;&nbsp;&nbsp;A&Ntilde;O&nbsp;&nbsp;{$actuacion->parametros['anio']}
			</div>
			<p>Mar del plata, {$fecha}</p>
			<p style="padding-left: 20px;">{$texto_providencia}:<br><br>{$nombres}</p>
HTML;
	}
}
?>

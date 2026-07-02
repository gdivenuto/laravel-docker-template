<?php
if (!isset($_SESSION)) {
	session_start();
}
class VistaOrdenSesionDatosExpedienteItem extends VistaBase {

	private $controlador;

	public function __construct() {

		parent::__construct();

		$this->controlador = 'ordenes_sesion';

		// Se crea una instancia del modelo
		$this->modelo = new ordenes_sesionModel();
	}

	public function mostrar($datos, $info_seccion, $autores, $proyectos, $mensaje) {

		//LibreriaGeneral::registrarLog("datos_expediente_item", $datos);
		//LibreriaGeneral::registrarLog("autores", $autores);

		$contenido_autor_textarea = '';

		// Si se edita el ítem, se muestra el valor que ya tiene registrado en su campo "autor" de la DB.
		if ( isset($datos['autor']) && $datos['autor'] != '' ) {
			$contenido_autor_textarea = $datos['autor'];
		} else {
			// Si la sección del ítem tiene seteado:
			// -------------------------------------
			// Sólo el Iniciador
			if ($info_seccion['mostrar_iniciador'] == 1 && $info_seccion['mostrar_autor'] == 0 ) {
				// Se muestra el Iniciador
				$contenido_autor_textarea .= $datos['descripcion_iniciador'];
			// Sólo el Autor
			} elseif ($info_seccion['mostrar_iniciador'] == 0 && $info_seccion['mostrar_autor'] == 1 ) {
				// Si posee Autor/es, se muestra
				$cant_autores = count($autores);
				for ($i=0; $i < $cant_autores; $i++) {
					$autor = &$autores[$i];
					
					// Si tiene más de un Autor se agrega la coma
					$contenido_autor_textarea .= ( ($i != $cant_autores) && ($i != 0) ) ? ', ' : '';

					$contenido_autor_textarea .= $autor['nombre_autor'];
				}
			}
			// Ambos tildados
			elseif ($info_seccion['mostrar_iniciador'] == 1 && $info_seccion['mostrar_autor'] == 1 ) {
				// Se muestra Iniciador/Autor en ese orden
				$contenido_autor_textarea .= $datos['descripcion_iniciador'].' / ';

				// Si posee Autor/es, se muestra a continuación del Iniciador
				$cant_autores = count($autores);
				for ($i=0; $i < $cant_autores; $i++) {
					$autor = &$autores[$i];
					
					// Si tiene más de un Autor se agrega la coma
					$contenido_autor_textarea .= ( ($i != $cant_autores) && ($i != 0) ) ? ', ' : '';

					$contenido_autor_textarea .= $autor['nombre_autor'];
				}
			}
			// Ninguno
			else
				// Por defecto se muestra el Iniciador
				$contenido_autor_textarea .= $datos['descripcion_iniciador'];
		}

		$contenido_extracto_textarea = '';

		// SI SE EDITA EL ITEM, SE MUESTRA EL EXTRACTO
		if ( isset($datos['extracto']) && $datos['extracto'] != '' ) {
			$contenido_extracto_textarea = $datos['extracto'];
		} else {
			$cant_proyectos = count($proyectos);
			for ($i=0; $i < $cant_proyectos; $i++) {
				$proyecto = &$proyectos[$i];
				
				// SI POSEE EXTRACTO EL PROYECTO, SE MUESTRA
				if ( $proyecto['extracto'] != '' && $proyecto['extracto'] != 'null' ) {
					// SI POSEE MÁS DE UN PROYECTO EL EXPED./NOTA
					if ( $cant_proyectos > 1 ) {
						$num_proyecto = $i + 1;
						// SE MUESTRAN NUMERADOS, CON EL FORMATO: 
						// [1]) PROYECTO DE [DESCRIPCION]: [EXTRACTO] [2]) PROYECTO DE [DESCRIPCION]:[EXTRACTO]...
						$contenido_extracto_textarea .= $num_proyecto.") PROYECTO DE ".$this->reemplazarPorMayusculaAcentuada(strtoupper($proyecto['descripcion_proyecto'])).": ".$proyecto['extracto']." ";
					} else {
						// SINO SE MUESTRA SÓLO EL EXTRACTO
						$contenido_extracto_textarea = $proyecto['extracto'];
					}
				}
			}
		}
		?>
		<div class="form-group row mt-1">
			<label for="iniciador_codigo" class="col-sm-2 control-label small text-left text-md-right pt-1">
				Iniciador
			</label>
			<div class="col-sm-1">
				<input  type="text" id="iniciador_codigo" name="iniciador_codigo"
						value="<?=$datos['iniciador_codigo'];?>"
						class="form-control form-control-sm p-1 text-muted" readonly="true">
			</div>
			<div class="col-sm-7">
				<input  type="text" id="descripcion_iniciador" name="descripcion_iniciador" 
						value="<?= $datos['descripcion_iniciador']; ?>"
						class="form-control form-control-sm text-muted" width="400" readonly="true" />
			</div>
			<div class="col-sm-2 p-1">
				<!-- Para Copiar el Iniciador en el textarea de Iniciador/Autor -->
				<button type="button" id="btCopiarIniciador" class="btn btn-info btn-sm w-100" 
						title="Copiar el Iniciador debajo">
					<i class="fas fa-check-circle"></i>&nbsp;Copiar
				</button>
			</div>
		</div>

		<div class="form-group row mt-1">
			<label for="codigo_autor" class="col-sm-2 control-label small text-left text-md-right pt-1">
				Autor
			</label>
			<div class="col-sm-1">
				<input  type="text" id="codigo_autor" name="codigo_autor"
						value="<?=$autores[0]['codigo_autor'];?>"
						class="form-control form-control-sm p-1 text-muted" readonly="true">
			</div>
			<div class="col-sm-7">
				<input  type="text" id="descripcion_autor" name="descripcion_autor" 
						value="<?= $autores[0]['nombre_autor']; ?>"
						class="form-control form-control-sm text-muted" width="400" readonly="true" />
			</div>
			<div class="col-sm-2 p-1">
				<!-- Para Copiar el Autor en el textarea de Iniciador/Autor -->
				<button type="button" id="btCopiarAutor" class="btn btn-info btn-sm w-100" 
						title="Copiar el Autor debajo">
					<i class="fas fa-check-circle"></i>&nbsp;Copiar
				</button>
			</div>
		</div>

		<div class="form-group row mt-1">
			<label for="autor" class="col-sm-2 control-label small text-left text-md-right pt-1">
				Iniciador/Autor:
			</label>
			<div class="col-sm-8">
				<textarea id="autor" name="autor" class="form-control form-control-sm"
						  rows="4" aria-label="Iniciador/Autor"><?=$contenido_autor_textarea;?></textarea>
			</div>
		</div>

		<div class="form-group row mt-1">
			<label for="caratula" class="col-sm-2 control-label small text-left text-md-right pt-1">
				Car&aacute;tula
			</label>
			<div class="col-sm-8">
				<input  type="text" id="caratula" name="caratula"
						value="<?=(isset($datos['caratula'])) ? $this->reemplazarComillaDoble($datos['caratula']) : '';?>"
						class="form-control form-control-sm" >
			</div>
		</div>

		<div class="form-group row mt-1">
			<label for="extracto" class="col-sm-2 control-label small text-left text-md-right pt-1">
				Extracto:
			</label>
			<div class="col-sm-8">
				<textarea id="extracto" name="extracto" class="form-control form-control-sm" rows="4" 
						  aria-label="Extracto"><?= $contenido_extracto_textarea; ?></textarea>
			</div>
			<div class="col-sm-2 p-1">
				<!-- Para Copiar el Autor en el textarea de Iniciador/Autor -->
				<button type="button" id="btActualizarExtracto" class="btn btn-info btn-sm w-100" 
						title="Actualizar Extracto">
					<i class="fas fa-check-circle"></i>&nbsp;Actualizar
				</button>
			</div>
		</div>

		<script>
			var puede_registrarse_item = '<?=(isset($datos)) ? 1 : 0;?>';
		</script>

		<script src="<?=URL_JS;?>ordenes_sesion/datos_expediente_item.js"></script>
				
	<?php }
}

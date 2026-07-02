<?php
// Clave del expediente, nota o recomendación
$f_anio = $this->vista->data['f_anio'];
$f_tipo = $this->vista->data['f_tipo'];
$f_numero = $this->vista->data['f_numero'];
$f_cuerpo = $this->vista->data['f_cuerpo'];
$f_alcance = $this->vista->data['f_alcance'];

if ($f_tipo == 'E') {
	$titulo_segun_tipo = 'el Expediente ';
} elseif ($f_tipo == 'N') {
	$titulo_segun_tipo = 'la Nota ';
} else {
	$titulo_segun_tipo = 'la Recomendaci&oacute;n ';
}

$this->generarModalDialog();
?>
<div class="row">
	<!-- Contenedora del formulario y texto informativo -->
	<div class="col-md-9 responsive">

		<div class="row borde-inferior">
		    <div class="col-md-12">
		    	<h3>Carga del documento para <?php echo $titulo_segun_tipo; ?></h3>
		    </div>
		</div>

		<form id="form_upload_temporal" name="form_upload_temporal" action="index.php?c=cargaproyectos&a=uploadtemporal" method="POST" class="form-horizontal" enctype="multipart/form-data" role="form">
			<!-- Cabecera del expediente|nota|recomendación, de sólo lectura -->
			<div class="row borde-inferior">
			    <div class="form-group col-md-12" >
			        <div class="col-md-12">
			            <div class="form-inline tamanio-texto-small">
			                <div class="form-group form-group-inline">
			                    <label for="f_anio">A&ntilde;o:</label>
			                    <input type="text" id="f_anio" name="f_anio" class="form-control input-sm form-control-width-small" value="<?php echo $f_anio; ?>" readonly="true">
			                </div>
			                <div class="form-group form-group-inline">
			                    <label for="f_tipo">Tipo:</label>
			                    <input type="text" id="f_tipo" name="f_tipo" class="form-control input-sm form-control-width-extra-small" value="<?php echo $f_tipo; ?>" readonly="true">
			                </div>
			                <div class="form-group form-group-inline">
			                    <label for="f_numero">N&uacute;mero:</label>
			                    <input type="text" id="f_numero" name="f_numero" class="form-control input-sm form-control-width-small" value="<?php echo $f_numero; ?>" readonly="true">
			                </div>
			                <div class="form-group form-group-inline">
			                    <label for="f_cuerpo">Cuerpo:</label>
			                    <input type="text" id="f_cuerpo" name="f_cuerpo" class="form-control input-sm form-control-width-extra-small" value="<?php echo $f_cuerpo; ?>" readonly="true">
			                </div>
			                <div class="form-group form-group-inline">
			                    <label for="f_alcance">Alcance:</label>
			                    <input type="text" id="f_alcance" name="f_alcance" class="form-control input-sm form-control-width-extra-small" value="<?php echo $f_alcance; ?>" readonly="true">
			                </div>
			            </div>
			        </div>
			    </div>
			</div>
			<!-- Texto informativo + botones -->
			<div class="row" style="height:380px">
		    	<div class="col-sm-11 col-md-11 col-sm-offset-1 col-md-offset-1">
		        	<div class="form-group">
				    	<p class="help-block">
				    		<br>
				    		Desde aqu&iacute; podr&aacute; subir un documento para <strong><?php echo $titulo_segun_tipo . $f_anio . '-' . $f_tipo . '-' . $f_numero . '-' . $f_cuerpo . '-' . $f_alcance; ?></strong>.
				    		<br>Para ello utilice el bot&oacute;n <strong>Examinar</strong>, seleccione el documento de su PC, y finalmente haga click en el bot&oacute;n <strong>Abrir</strong> para subirlo.
							<br><br>
							Podr&aacute; visualizar en la solapa Expedientes, en color amarillo <span class="resaltado-advertencia">&nbsp;"P"&nbsp;</span>, el estado del documento del proyecto (Para Cargar);
							esto significa que dicho documento quedar&aacute; pendiente de carga, para que el &aacute;rea de Mesa de Entrada del Honorable Concejo Deliberante pueda finalizar el proceso.
				    		<br><br>
				    		<span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;
				    		<strong>Se permiten solamente los documentos con extensi&oacute;n .doc, .docx y .odt.</strong>
				    		<br>
				    	</p>
				    </div>
		        	<div class="form-group">
		                <div class="col-md-7">
		                	<!-- Aquí se muestra el nombre del documento seleccionado -->
		                	<span id="contenedor_nombre_documento" class="help-block"></span>

		                    <!-- Se oculta el input 'file' con el que se carga el documento -->
							<input type="file" id="f_archivo_temporal" name="f_archivo_temporal" value="" accept=".doc,.docx,.odt" style="visibility:hidden" />
							<!-- Botón para buscar y seleccionar el documento -->
							<button id="btn_examinar" class="btn btn-primary btn-sm" type="button">
								<span class="glyphicon glyphicon-search"></span>&nbsp;Examinar
							</button>

		                    <!-- Botón para volver al listado de Expedientes -->
		                    <button id="btn_cancelar" class="btn btn-primary btn-sm" type="button">
		                    	<span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Volver a Expedientes
		                    </button>
						</div>
		            </div>
		    	</div>
			</div>
		</form>
	</div>
	<?php $this->incluirPlantilla('expedientes/expedientes_vista_previa_expediente.php');?>
</div>
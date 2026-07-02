<?php
// Información del expediente que posee como Antecedente el expediente del D.E. respectivo
$info = $this->vista->data['info'];

if ($info['f_tipo'] == 'E')
    $titulo_segun_tipo = 'el Expediente ';
elseif ($info['f_tipo'] == 'N')
    $titulo_segun_tipo = 'la Nota ';
else
    $titulo_segun_tipo = 'la Recomendaci&oacute;n ';

// Documentos del expediente del D.E.
$documentos_depto_ejecutivo = $this->vista->data['documentos_depto_ejecutivo'];

//Logger::get()->Log("documentos_depto_ejecutivo", $documentos_depto_ejecutivo, false);
//Logger::get()->Log("info", $info, false);

// Extensiones que NO se permiten cargar
$extensiones_no_permitidas = array(".exe", ".pif", ".inf");

// Nombre codificado AATNNNNN, para el directorio de proyectos del expediente respectivo
$nombre_codificado_proyectos = sprintf("%02d%s%05d", $info['f_anio'] % 100, $info['f_tipo'], $info['f_numero']);

// Ruta del directorio destino, /proyectos/AAAA/AATNNNNN/
$ruta_directorio_destino = PATH_KRAKEN_RESOURCES_PROYECTOS.$info['f_anio']."/".$nombre_codificado_proyectos."/";

$this->generarModalDialog();
?>
<div class="row">
    <!-- Contenedora del formulario y texto informativo -->
    <div class="col-md-12 responsive">

        <div class="row">
            <div class="col-md-12">
                <h3>Carga de documentos para <?php echo $titulo_segun_tipo; ?> <strong><?php echo $info['f_anio'].'-'.$info['f_tipo'].'-'.$info['f_numero'].'-'.$info['f_cuerpo'].'-'.$info['f_alcance']; ?></strong></h3>
            </div>
        </div>

        <form id="form_upload_documentos_ejecutivo" name="form_upload_documentos_ejecutivo" action="index.php?c=antecedentes&a=uploaddocumentosexpeddeptoejecutivo" method="POST" class="form-horizontal" enctype="multipart/form-data" role="form">
            
            <input type="hidden" id="f_anio" name="f_anio" value="<?php echo $info['f_anio']; ?>">
            <input type="hidden" id="f_tipo" name="f_tipo" value="<?php echo $info['f_tipo']; ?>">
            <input type="hidden" id="f_numero" name="f_numero" value="<?php echo $info['f_numero']; ?>">
            <input type="hidden" id="f_cuerpo" name="f_cuerpo" value="<?php echo $info['f_cuerpo']; ?>">
            <input type="hidden" id="f_alcance" name="f_alcance" value="<?php echo $info['f_alcance']; ?>">

            <input type="hidden" id="f_anio_a" name="f_anio_a" value="<?php echo $info['f_anio_a']; ?>">
            <input type="hidden" id="f_tipo_a" name="f_tipo_a" value="<?php echo $info['f_tipo_a']; ?>">
            <input type="hidden" id="f_numero_a" name="f_numero_a" value="<?php echo $info['f_numero_a']; ?>">
            <input type="hidden" id="f_digito_a" name="f_digito_a" value="<?php echo $info['f_digito_a']; ?>">

            <div class="row">
                <div class="col-md-8" >
                    <p class="help-block">
                        <br>
                        <span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;
                        Desde aqu&iacute; podr&aacute; subir los documentos, una vez seleccionados, utilice el bot&oacute;n <strong>Cargar</strong>, para subirlos al directorio correspondiente al expediente.
                        <br><br>
                    </p>
                </div>
            </div>
            <!-- Listado de documentos a cargar -->
            <div class="row">
                <div class="col-md-8 responsive">
                    <?php
                    // Para cada archivo encontrado en dicho directorio
                    foreach ($documentos_depto_ejecutivo as $doc) {
                        // Se muestran solamente los archivos que contenga y cuya extensión sea permitida
                        if ( is_file($doc['ruta_completa']) && ( ! in_array(strtolower(substr($doc['archivo'], -4)), $extensiones_no_permitidas) ) ) {
                    ?>
                            <div class="row">
                                <div class="col-xs-4 col-md-2 col-md-offset-1"><?php echo $doc['archivo']; ?></div>
                                <div class="col-xs-1 col-md-1">
                                    <input type="checkbox" name="documento_de[]" value="<?php echo $doc['archivo']; ?>" class="chk_documento_DE" style="margin-top: 0" />
                                </div>
                                <div class="col-xs-6 col-md-7">
                                    <?php
                                    // Se verifica si ya existe el documento
                                    if ( file_exists($ruta_directorio_destino.$doc['archivo']) )
                                        echo '<span class="text-info">(El documento ya existe, si desea <strong>reemplazarlo</strong> marque la casilla)</span>';
                                    ?>
                                </div>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-7 col-md-offset-2">
                    <!-- Botón para enviar el formulario para cargar los documentos elegidos -->
                    <button id="btn_cargar" class="btn btn-primary btn-sm" type="button"><span class="glyphicon glyphicon-open"></span>&nbsp;Cargar</button>
                    <!-- Botón para volver al listado de Antecedentes -->
                    <button id="btn_cancelar" class="btn btn-default btn-sm" type="button"><span class="glyphicon glyphicon-remove"></span>&nbsp;Cancelar</button>
                </div>
                <br><br><br><br>
            </div>
        </form>
    </div>
</div>
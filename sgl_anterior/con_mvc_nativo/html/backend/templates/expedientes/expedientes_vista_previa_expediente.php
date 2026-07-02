<?php
/**
 * Este script esta diseñado para ser incluído desde las páginas que requieran una vista previa de un expediente.
 */
?>
<!-- Vista previa Flotante del expediente -->
<div class="col-md-3 contenedor-vista-previa">
    <div class="panel panel-default sin_margin_bottom">
        <div class="panel-heading">
            <h3 class="panel-title"><strong>Informaci&oacute;n del Expediente</strong></h3>
            <h3 id="prev_expediente" class="panel-title">&nbsp;</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <strong>Proyecto: <span id="prev_estado_proyecto"></span></strong>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <strong>Digitalizaci&oacute;n: <span id="prev_estado_digitalizacion"></span></strong>
                </div>
            </div>
          	<div class="row">
              	<div class="col-md-12">
                  	<strong><span id="prev_caratula"></span></strong>
              	</div>
          	</div>
          	<div class="row">
              	<div class="col-sm-3 col-md-12"><strong>Inicia</strong>:</div>
                <div class="col-sm-9 col-md-12">
                    <span id="prev_iniciador"></span>
                </div>
            </div>
            <div class="row">
              	<div class="col-sm-3 col-md-12"><strong>Categor&iacute;a:</strong></div>
                <div class="col-sm-9 col-md-12">
                    <span id="prev_categoria"></span>
                </div>
            </div>
          	<div class="row">
              	<div id="prev_titulo_autores" class="col-sm-3 col-md-12"></div>
                <div id="contenedor_prev_autores" class="col-sm-9 col-md-12">
                    <!-- Aquí se muestra/n el autor/los autores -->
                </div>
            </div>
            <div class="row">
              	<div id="prev_titulo_temas" class="col-sm-3 col-md-12"></div>
                <div id="contenedor_prev_temas" class="col-sm-9 col-md-12">
                    <!-- Aquí se muestra/n el tema/los temas -->
                </div>
            </div>
            <div class="row">
              	<div class="col-sm-3 col-md-12"><strong>Estado:</strong></div>
                <div class="col-sm-9 col-md-12">
                    <span id="prev_estado"></span>
                </div>
            </div>
            <div class="row">
              	<div class="col-sm-3 col-md-12"><strong>Comisi&oacute;n:</strong></div>
                <div class="col-sm-9 col-md-12">
                    <span id="prev_comision"></span>
                </div>
            </div>
          	<div class="row">
              	<div class="col-md-12">
                  	<strong>Proyecto N&deg; <span id="prev_proyecto_orden"></span></strong>
              </div>
          	</div>
            <div class="row">
              	<div id="prev_contenedor_textarea_proyectos" class="col-md-11">
                    <textarea id="prev_proyecto_extracto" class="form-control" rows="3" readonly></textarea>
              	</div>
                <div id="prev_contenedor_botones_proyectos" class="col-md-1 sin_padding_izq">
                    <a id="btn_prev_proyecto_anterior" href="#" title="Ver proyecto anterior"><span class="glyphicon glyphicon-arrow-up"></span></a>
                    <br><br>
                    <a id="btn_prev_proyecto_siguiente" href="#" title="Ver proyecto siguiente"><span class="glyphicon glyphicon-arrow-down"></span></a>
                </div>
            </div>
            <div class="row">
              	<div class="col-md-12"><strong>Observaciones</strong></div>
          	</div>
          	<div class="row">
              	<div class="col-md-12">
                  	<textarea id="prev_observaciones_expe" class="form-control" rows="3" readonly></textarea>
              	</div>
          	</div>
            <!-- <div class="row"> 
                <div id="prev_documento" class="col-md-12"></div>
            </div> -->
            <div class="row"> 
                <div class="col-md-12">
                    <strong>Modificado por:</strong>&nbsp;<span id="prev_usuario"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

// Configuracion de rutas
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/config/path_config.php');

// Clases del modelo
require_once(PATH_SGL_LAYER_MODELO_PRESTAMOS.'SolicitudExpedienteExterno.php');

class VistaSolicitudExpedienteExterno extends VistaBase
{
    private $controlador;
    private $formulario;
    private $formulario_cambio_estado;
	
    public function __construct()
    {
		$this->controlador = 'solicitud_expediente_externo';
		$this->formulario = 'formEdicionSolicitud';
		$this->formulario_cambio_estado = 'formCambioEstado';
    }

    /**
     * Muestra el paginador de Solicitudes
     * @param array $parametros_vista
     */
    public function mostrarPaginadorSolicitudes($parametros_vista = '')
    {
        ?>
       	<div class="p_borde_superior"></div>
       	<!-- Paginador de préstamos -->
       	<div class="prestamo_paginador">
    		<?php
    		// Si la página no es la primera
    		if ($parametros_vista['pagina'] != 1)
    		{
    		?>
    			<!-- Primer página -->
    			<a id="btPrimero" title="Primer p&aacute;gina" href="javascript:paginarSolicitudes(1, 'primero');">
    				<img id="imgPrimero" src="imagenes/barra/b_firstpage.png" width="14" height="14" align="top" />
    			</a>
    		<?php 
    		}
    		else
    		{
    		?>
    			<a id="btPrimero" href="#">
    				<img id="imgPrimero" src="imagenes/barra/bd_firstpage.png" width="14" height="14" align="top" />
    			</a>
    		<?php 
    		}
    			
    		// Si la página anterior está definida
    		if ($parametros_vista['pagina_ant'] != 0)
    		{
    		?>
    			<!-- Página anterior -->
    			<a id="btAnterior" title="P&aacute;gina anterior" href="javascript:paginarSolicitudes(<?php echo $parametros_vista['pagina_ant']; ?>, 'anterior');">
    				<img id="imgAnterior" src="imagenes/barra/b_prevpage.png" width="14" height="14" align="top" />
    			</a>
    		<?php 
    		}
    		else
    		{
    		?>
    			<a id="btAnterior" href="#">
    				<img id="imgAnterior" src="imagenes/barra/bd_prevpage.png" width="14" height="14" align="top" />
    			</a>
    		<?php 
    		}
    		
    		// Descripción del paginador
    		echo " P&aacute;gina ";
    		?>
    		<select id="cmb_nro_pagina" name="cmb_nro_pagina">
    			<?php
    			// Se muestran una opción por cada número de página a elegir
    			for ($i=1; $i <= $parametros_vista['nro_paginas']; $i++)
    			{
    			?>
    				<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
    			<?php
    			}
    			?>
    			
    		</select>		
    		<?php
    		echo " de ".$parametros_vista['nro_paginas'];
    		
    		// Si la página no es la última
    		if ( $parametros_vista['pagina'] != $parametros_vista['nro_paginas'] )
    		{
    		?>
    			<!-- Página siguiente -->
    			<a id="btSiguiente" title="P&aacute;gina siguiente" href="javascript:paginarSolicitudes(<?php echo $parametros_vista['pagina_sgte']; ?>, 'siguiente');">
    				<img id="imgSiguiente" src="imagenes/barra/b_nextpage.png" width="14" height="14" align="top" />
    			</a>
    		<?php 
    		}
    		else
    		{
    		?>
    			<a id="btSiguiente" href="#">
    				<img id="imgSiguiente" src="imagenes/barra/bd_nextpage.png" width="14" height="14" align="top" />
    			</a>
    		<?php 
    		}
    				
    		// Si la página no es la última
    		if ($parametros_vista['pagina'] != $parametros_vista['nro_paginas'])
    		{
    		?>
    			<!-- Última página -->
    			<a id="btUltimo" title="&Uacute;ltima p&aacute;gina" href="javascript:paginarSolicitudes(<?php echo $parametros_vista['nro_paginas']; ?>, 'ultimo');">
    				<img id="imgUltimo" src="imagenes/barra/b_lastpage.png" width="14" height="14" align="top" />
    			</a>
    		<?php 
    		}
    		else
    		{
    		?>
    			<a id="btUltimo" href="#">
    				<img id="imgUltimo" src="imagenes/barra/bd_lastpage.png" width="14" height="14" align="top" />
    			</a>
    		<?php 
    		}
    		?>
    	</div>
    	<script>
    		$('cmb_nro_pagina').value = '<?php echo ($parametros_vista['pagina']) ? $parametros_vista['pagina'] : 1; ?>';
    
    		$('cmb_nro_pagina').addEvent('change', function()
    		{
    			// Se pagina el listado
    			paginarSolicitudes($('cmb_nro_pagina').value, '');
    		});
    	</script>
        <?php	
    }

    /**
     * Se listan las Solicitudes
     * @param Array $listado, colección de instancias de Solicitudes
     * @param ng_prestamos $ng_prestamos
     * @param Array $parametros_vista
     */
    public function listar($listado = '',  ng_prestamos $ng_prestamos, $parametros_vista = '')
    {
    	// MENSAJE DEL RESULTADO DE LA OPERACION REALIZADA
    	$this->mostrarCartelResultado($parametros_vista['mensaje'], $parametros_vista['tipo_mensaje']);
    	 
    	// Cantidad de préstamos obtenidos
    	$cantidad = count($listado);

    	// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN EDITAR
    	$puede_editar = ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2) ? true : false;
    	 
    	// SÓLO USUARIOS DE PERFIL 1 PUEDEN ELIMINAR
    	$puede_eliminar = ($_SESSION['perfil2'] == 1) ? true : false;
    	?>
        <script>
            $('item_archivos_menu_gral').setStyle('display','none');
            $('item_consultas_menu_gral').setStyle('display','none');
            $('item_listados_menu_gral').setStyle('display','none');
            $('item_tareas_menu_gral').setStyle('display','none');
            
    	    $("capaFondo").setStyle('visibility','hidden');
    		$("capaVentana").setStyle('visibility','hidden');
    	</script>
    		
        <!-- Div de fondo negro al utilizar una ventana modal-->
        <div id="fade" class="overlay"></div>
        <!-- Div para la ventana modal -->
    	<div id="light" class="modal"></div>
    		
    	<!-- Nombre del controlador -->
        <input type="hidden" id="controlador" name="controlador" value="<?php echo $this->controlador; ?>" />
        <!-- Cantidad de préstamos -->
        <input type="hidden" id="cantidad" value="<?php echo count($listado); ?>" />
        <!-- Valor de la página -->
        <input type="hidden" id="pagina" value="<?php echo ($parametros_vista['pagina']) ? $parametros_vista['pagina'] : 1; ?>" />
        <!-- Número total de páginas -->
        <input type="hidden" id="nro_paginas" value="<?php echo $parametros_vista['nro_paginas']; ?>" />
       	<!-- Nombre por defecto del campo por el cual se ordena -->
       	<input type="hidden" name="campo_orden" id="campo_orden" value="<?php echo $parametros_vista['campo_orden']; ?>" />
            	
       	<div class="p_borde_superior"></div>
        	    
       	<!-- Barra de navegación superior -->
        <div class="p_barra_navegacion">
        	
        	<!-- TITULO DE LA GRILLA -->
    		<div class="prestamo_titulo_grilla solicitud_ee_color_titulo">SOLICITUDES A ENTES EXTERNOS</div>
    		
    		<!-- BOTON VOLVER -->
        	<div class="p_boton_edicion" style="float:right">
    			<a href="index.php" title="Volver al listado de Expedientes">
    				<img src="imagenes/barra/volver.jpeg" width="15" height="15" />&nbsp;Volver
    			</a>
    		</div>
    		<?php
    		// Si hay solicitudes que listar en el reporte
    		if ($cantidad > 0) {
    		?>
	    		<div class="p_buscador_margen_datos" style="float:right"></div>
	    	    <!-- BOTON GENERAR REPORTE -->
	    	   	<div class="p_boton_edicion" style="width:130px;float:right">
	    			<a id="btGenerarReporte" title="Generar reporte de Solicitudes a Entes Externos" style="width:130px;">
	    				<img src="imagenes/pdf.jpg" width="15" height="15" />&nbsp;Generar reporte
	    			</a>
	    		</div>
	    	<?php
			}
			?>
			
			<?php /* NO UTILIZADO POR EL MOMENTO, SE GENERAN AUTOMÁTICAMENTE AL INGRESAR UN PRESTAMO QUE NO POSEA YA UNA SOLICITUD * ?>
			<div class="p_buscador_margen_datos" style="float:right"></div>
			<!-- BOTON NUEVO (POR EL MOMENTO INHABILITADO, SE GENERA AUTOMÀTICAMENTE AL INGRESAR UN PRESTAMO DE TIPO 'D') -->
        	<div class="p_boton_edicion" style="float:right;">
        		<a href="#" style="color:silver;" title="Agregar Solicitud"><!-- javascript:refrescar('abms/index.php?controlador=<?php //echo $this->controlador; ?>&accion=agregar', 'contenidoAjaxPrincipal'); -->
        			<img src="imagenes/barra/add_gris_16x16.gif" width="16" height="16" align="top" />&nbsp;Nuevo
        		</a>
        	</div>
        	<?php /**/ ?>
        </div>
        	
        <div class="p_borde_superior"></div>
        	
       	<!-- Buscador por clave del expediente -->
        <div class="p_buscador">
        	<div class="p_buscador_margen_datos"></div>
            <div class="p_buscador_dato p_buscador_texto">
        	    A&ntilde;o:&nbsp;<input type="text" name="f_anio" id="f_anio" value="<?php echo ($parametros_vista['anio']) ? $parametros_vista['anio'] : ''; ?>" onKeyPress="return soloEnteros(event);" onKeyUp="javascript:respetar_anio(this);" size="2" maxlength="4" />
            </div>
            <div class="p_buscador_margen_datos"></div>
            <div class="p_buscador_dato p_buscador_texto">
        	    Tipo:&nbsp;
                <select name="f_tipo" id="f_tipo" class="p_buscador_dato_tipo">
                    <option value="D">D</option>
                    <option value="O">O</option>
                </select>
        	</div>
        	<div class="p_buscador_margen_datos"></div>
        	<div class="p_buscador_dato p_buscador_texto">
        	    N&uacute;mero:&nbsp;<input type="text" name="f_numero" id="f_numero" value="<?php echo ($parametros_vista['numero'] != '') ? $parametros_vista['numero'] : ''; ?>" onKeyPress="return soloEnteros(event);" size="3" maxlength="5" />
        	</div>
        	<div class="p_buscador_margen_datos"></div>
        	<div class="p_buscador_dato p_buscador_texto">
        	    Cuerpo:&nbsp;<input type="text" name="f_cuerpo" id="f_cuerpo" value="<?php echo ($parametros_vista['cuerpo'] != '') ? $parametros_vista['cuerpo'] : ''; ?>" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" />
        	</div>
        	<div class="p_buscador_margen_datos"></div>
        	<div class="p_buscador_dato p_buscador_texto">
        	    Alcance:&nbsp;<input type="text" name="f_alcance" id="f_alcance" value="<?php echo ($parametros_vista['alcance'] != '') ? $parametros_vista['alcance'] : ''; ?>" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" />
        	</div>
        	<div class="p_buscador_margen_datos"></div>
        	<div class="p_buscador_dato p_buscador_texto">
        		D&iacute;gito:&nbsp;<input type="text" name="f_digito" id="f_digito" value="<?php echo ($parametros_vista['digito'] != '') ? $parametros_vista['digito'] : ''; ?>" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" />
        	</div>
        	<div class="p_buscador_margen_datos"></div>
        	<div class="p_buscador_dato p_buscador_texto">
        	    Cpo Alc.:&nbsp;<input type="text" name="f_cuerpoalcance" id="f_cuerpoalcance" value="<?php echo ($parametros_vista['cuerpoalcance'] != '') ? $parametros_vista['cuerpoalcance'] : ''; ?>" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" />
        	</div>
        	<div class="p_buscador_margen_datos"></div>
        	<div class="p_buscador_dato p_buscador_texto">
        	    An. Alc.:&nbsp;<input type="text" name="f_anexoalcance" id="f_anexoalcance" value="<?php echo ($parametros_vista['anexoalcance'] != '') ? $parametros_vista['anexoalcance'] : ''; ?>" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" />
        	</div>
        	<div class="p_buscador_margen_datos"></div>
        	<div class="p_buscador_dato p_buscador_texto">
        	    Cpo. An. Alc.:&nbsp;<input type="text" name="f_cuerpoanexoalcance" id="f_cuerpoanexoalcance" value="<?php echo ($parametros_vista['cuerpoanexoalcance'] != '') ? $parametros_vista['cuerpoanexoalcance'] : ''; ?>" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" />
        	</div>
        	<div class="p_buscador_margen_datos"></div>
        	<div class="p_buscador_dato p_buscador_texto">
        	    An.:&nbsp;<input type="text" name="f_anexo" id="f_anexo" value="<?php echo ($parametros_vista['anexo'] != '') ? $parametros_vista['anexo'] : ''; ?>" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" />
        	</div>
        	<div class="p_buscador_margen_datos"></div>
        	<div class="p_buscador_dato p_buscador_texto">
        	    Cpo. An.:&nbsp;<input type="text" name="f_cuerpoanexo" id="f_cuerpoanexo" value="<?php echo ($parametros_vista['cuerpoanexo'] != '') ? $parametros_vista['cuerpoanexo'] : ''; ?>" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" />
        	</div>
        	<div class="p_buscador_margen_datos"></div>
        	<div class="p_boton_edicion">
        		<a title="Buscar" href="javascript:listarPorExpediente();">
        			<img src="imagenes/zoom_16x16.gif" width="16" height="16" align="top" />&nbsp;Buscar
        		</a>
        	</div>
       	</div>
        	
       	<div class="p_borde_superior"></div>
        	
        <!-- Buscador extendido -->
       	<div class="p_buscador">
       		<div class="p_buscador_margen_datos"></div>
    	   	<div class="p_buscador_dato p_buscador_texto">
    	   		Estados:&nbsp;&nbsp;&nbsp;
    	   		&nbsp;<input type="checkbox" id="f_estado_solicitado_hcd" name="f_estado_solicitado_hcd" value="<?php echo SolicitudExpedienteExterno::E_SOLICITADO_HCD; ?>">&nbsp;Solicitado al HCD
    	   		&nbsp;<input type="checkbox" id="f_estado_solicitado_ee" name="f_estado_solicitado_ee" value="<?php echo SolicitudExpedienteExterno::E_SOLICITADO_EE; ?>">&nbsp;Solicitado al E.E.
    	   		&nbsp;<input type="checkbox" id="f_estado_ingresado_ee" name="f_estado_ingresado_ee" value="<?php echo SolicitudExpedienteExterno::E_INGRESADO_EE; ?>">&nbsp;Ingresado del E.E.
    	   		&nbsp;<input type="checkbox" id="f_estado_devuelto_ee" name="f_estado_devuelto_ee" value="<?php echo SolicitudExpedienteExterno::E_DEVUELTO_EE; ?>">&nbsp;Devuelto al E.E.
    	   		&nbsp;<input type="checkbox" id="f_estado_anulado_ee" name="f_estado_anulado_ee" value="<?php echo SolicitudExpedienteExterno::E_ANULADO_EE; ?>">&nbsp;Anulado
    	   	</div>
    	</div>
    	<div class="p_buscador">
    		<div class="p_buscador_margen_datos"></div>
    	   	<div class="p_buscador_dato p_buscador_texto">
    	   		Fecha desde:&nbsp;
    			<input type="text" name="f_fecha_desde" id="f_fecha_desde" value="<?php echo ($parametros_vista['fecha_desde']) ? $parametros_vista['fecha_desde'] : date("d/m/").( date("Y")-2 ); ?>" size="8" maxlength="10" onkeyup="mascara(this, '/', patron, true);" />
    			&nbsp;<input type="image" id="img_f_fecha_desde" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la Fecha Desde" width="16" height="16">
    			&nbsp;&nbsp;
    			Fecha hasta:&nbsp;
    			<input type="text" name="f_fecha_hasta" id="f_fecha_hasta" value="<?php echo ($parametros_vista['fecha_hasta']) ? $parametros_vista['fecha_hasta'] : ''; ?>" size="8" maxlength="10" onkeyup="mascara(this, '/', patron, true);" />
    			&nbsp;<input type="image" id="img_f_fecha_hasta" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la Fecha Hasta" width="16" height="16">
    		</div>
    		<div class="p_buscador_margen_datos" style="width:22px;"></div>
    	    <div class="p_boton_edicion">
    	    	<a href="javascript:listarPorCriterioBusqueda();" title="Filtrar">
    	    		<img src="imagenes/zoom_16x16.gif" width="16" height="16" align="top" />&nbsp;Filtrar
    	    	</a>
    	    </div>
    	    <div class="p_buscador_margen_datos"></div>
    	    <div class="p_boton_edicion">
    			<a href="javascript:limpiarCriterioBusqueda();" title="Limpiar criterio de b&uacute;squeda">
    				<img src="imagenes/limpiar.png" width="15" height="15" />&nbsp;Limpiar
    			</a>
    		</div>
    	</div>
    		
        <div class="p_borde_superior"></div>
        	
       	<div class="p_borde_superior_sin_linea"></div>
        	    
        <div class="contenedor_listado contenedor_listado_prestamos">
    	   	<?php
    	   	// Si hay préstamos para mostrar
    		if ( $cantidad > 0)
    		{
    		?>	
	    		<table class="e_tabla_texto">
	    			<thead class="e_tabla_titulos">
	    				<?php
                        if ( $puede_eliminar ) // Sólo el perfil 1 puede eliminar
                            echo '<th class="orden_link" width="32" colspan="2">&nbsp;</th>';
                        elseif ( $puede_editar ) // Sólo los perfiles 1 y 2 pueden modificar
                            echo '<th class="orden_link" width="16">&nbsp;</th>';
                        ?>
	    				<th class="orden_link">Expediente</th>
	    				<th class="orden_link">Fecha<br>Solicitud HCD</th>
	    				<th class="orden_link">Fecha<br>Solicitud E.E.</th>
	    				<th class="orden_link">Fecha<br>Ingreso HCD</th>
	    				<th class="orden_link">Fecha<br>Devoluci&oacute;n E.E.</th>
	    				<th class="orden_link">Fecha<br>Anulado</th>
	    				<th class="orden_link">Estado</th>
	    				<th class="orden_link">Observaciones</th>
	    			</thead>
	    			<tbody id="e_cuerpo_scrolleable" class="e_cuerpo_scrolleable">
	    				<?php
	    				$i = 0;
	    				foreach ($listado as $solicitud_ee) {
	    					// Se obtienen los estados siguientes que puede tomar la Solicitud
	    					$estados_siguientes = $ng_prestamos->ObtenerEstadosSiguientesExpedienteExterno($solicitud_ee);

                            // Verificamos si existe por lo menos un Préstamo PRESTADO, para la Solicitud
                            $existe_prestamo_prestado = $ng_prestamos->ExistePrestamoPrestadoParaSolicitud($solicitud_ee);

                            // Verificamos si existe por lo menos un Préstamo pendiente (Solicitado y NO Prestado aún)
                            $existe_prestamo_pendiente = $ng_prestamos->ExistePrestamoPendienteParaSolicitud($solicitud_ee);
	    				?>
	    					<tr onmouseover="javascript:this.setStyle('background-color', '#EBEFF9');" onmouseout="javascript:this.setStyle('background-color', '#ffffff');"> 
		    					<?php
	    					    // Si tiene permiso para Editar
								if ($puede_editar) {
								?>
									<td style="width:22px">
										<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarObservaciones&parametros_serializados=<?php echo serializarColeccion($parametros_vista); ?>&data=<?php echo $solicitud_ee->Serializar(); ?>', 'capaVentana');" title="Editar Solicitud" >
											&nbsp;<img src="imagenes/b_edit.png" width="14" height="14" align="center" />
										</a>
									</td>
								<?php 
								}

                                /* REEMPLAZADO en la línea 389, por empty($estados_siguientes) *
                                // No se permite eliminar si ya se prestó el expediente
                                if ( !(
                                        in_array(SolicitudExpedienteExterno::E_DEVUELTO_EE, $estados_siguientes) || 
                                        in_array(SolicitudExpedienteExterno::E_ANULADO_EE, $estados_siguientes)
                                      ))
                                {
                                /**/

								// Si tiene permiso para Eliminar
								if ($puede_eliminar) {
                                    // Si ya se encuentra cerrada la Solicitud
                                    if (empty($estados_siguientes)) {
                                        // Si existe por lo menos un préstamo PRESTADO para la solicitud
                                        if ($existe_prestamo_prestado) {
                                ?>
                                            <td style="width:22px" title="No se puede eliminar, el expediente se encuentra prestado.">
                                                &nbsp;<img src="imagenes/b_drop_gris.png" width="14" height="14" align="center" />
                                            </td>
                                <?php   } else { 
                                            if ($existe_prestamo_pendiente) {
                                ?>
                                                <td style="width:22px">
                                                    <a href="javascript:if(confirm('¿Desea eliminar la Solicitud <?php echo $solicitud_ee->anio.'-'.$solicitud_ee->tipo.'-'.$solicitud_ee->numero; ?> definitivamente?')){refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=eliminar&data=<?php echo $solicitud_ee->Serializar(); ?>&parametros_serializados=<?php echo serializarColeccion($parametros_vista); ?>', 'contenidoAjaxPrincipal');};" title="Eliminar Solicitud">
                                                        &nbsp;<img src="imagenes/b_drop.png" width="14" height="14" align="center" />
                                                    </a>
                                                </td>
                                <?php       } else { ?>
                                                <td style="width:22px">
                                                    <a href="javascript:if(confirm('¿Desea eliminar la Solicitud <?php echo $solicitud_ee->anio.'-'.$solicitud_ee->tipo.'-'.$solicitud_ee->numero; ?> definitivamente?')){refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=eliminar&data=<?php echo $solicitud_ee->Serializar(); ?>&parametros_serializados=<?php echo serializarColeccion($parametros_vista); ?>', 'contenidoAjaxPrincipal');};" title="Eliminar Solicitud">
                                                        &nbsp;<img src="imagenes/b_drop.png" width="14" height="14" align="center" />
                                                    </a>
                                                </td>
                                <?php       }
                                        }
    								} else {
                                ?>
                                        <td style="width:22px" title="No se puede eliminar, el expediente se encuentra prestado o pendiente de pr&eacute;stamo.">
                                            &nbsp;<img src="imagenes/b_drop_gris.png" width="14" height="14" align="center" />
                                        </td>
                                <?php
                                    }
                                }
								?>

                                <!--  Descripción de la clave del expediente -->
								<td class="prestamo_celda_listado_general" style="width:180px;">
	    							<?php
	    							// Muestra la descripcion de la clave del expediente
	    							echo $solicitud_ee->ToStringDescription();
	    							?>
	    							&nbsp;
									<a style="padding:3px;height:16px;background-color:#fff;display:inline;" href="javascript:refrescar('abms/index.php?controlador=prestamos&accion=listarPrestamosPorExpediente&anio=<?php echo $solicitud_ee->anio; ?>&tipo=<?php echo $solicitud_ee->tipo; ?>&numero=<?php echo $solicitud_ee->numero; ?>', 'contenidoAjaxPrincipal');" title="Ver Pr&eacute;stamos" >
										<img src="imagenes/print_etiq_16x16.gif" width="14" height="14" >
									</a>
                                    <?php
                                    /**
                                    // Muestra una advertencia en caso que NO exista el Préstamo respectivo a la Solicitud
                                    // Si la solicitud esta cerrada, siempre devuelve verdadero (supone que ya finalizo y tiene un prestamo asociado)
                                    $ignorar_solicitud_cerrada = $solicitud_ee->estado == SolicitudExpedienteExterno::E_DEVUELTO_EE || $solicitud_ee->estado == SolicitudExpedienteExterno::E_ANULADO_EE;
                                    //if ( ! $existe_prestamo_prestado) <-- se agrego la variable $ignorar_solicitud_cerrada en la comparacion
                                    if ( !($ignorar_solicitud_cerrada || $existe_prestamo_prestado) ) {
                                    ?>
                                        <a style="width:16px;height:16px;background-color:#fff;display:inline" href="#" title="Pr&eacute;stamo inexistente" >
                                            <img src="imagenes/barra/advertencia.png" width="18" height="18" />
                                        </a>
                                    <?php 
                                    }
                                    /**/
                                    ?>
	    						</td>
                                <!-- Fecha de Solicitud en el HCD -->
	    						<td class="prestamo_celda_listado_general" style="width:80px;text-align:center;">
	    							<?php echo $this->verificarFecha($solicitud_ee, SolicitudExpedienteExterno::E_SOLICITADO_HCD); ?>
	    						</td>
                                <!-- Fecha de Solicitud al Ente Externo -->
	    						<td class="prestamo_celda_listado_general" style="width:100px;text-align:center;">
	    							<?php
	    							// SI PUEDE EDITAR Y SU SIGUIENTE ESTADO ES 'SOLICITADO_EE'
	    							if ( $puede_editar && in_array(SolicitudExpedienteExterno::E_SOLICITADO_EE, $estados_siguientes) ) {
	    							?>
	    								<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=cambiarEstado&parametros_serializados=<?php echo serializarColeccion($parametros_vista); ?>&data=<?php echo $solicitud_ee->Serializar(); ?>&estado_nuevo=<?php echo SolicitudExpedienteExterno::E_SOLICITADO_EE; ?>&generar_nueva_solicitud=0', 'capaVentana');">
	    									<img src="imagenes/b_edit.png" width="14" height="14" >&nbsp;<?php echo "Solicitar al E.E."; ?>
	    								</a>
	    							<?php
	    							} else
	    								echo $this->verificarFecha($solicitud_ee, SolicitudExpedienteExterno::E_SOLICITADO_EE);
	    							?>
	    						</td>
                                <!-- Fecha de Ingreso al HCD -->
	    						<td class="prestamo_celda_listado_general" style="width:100px;text-align:center;">
	    							<?php
	    							// SI PUEDE EDITAR Y SU SIGUIENTE ESTADO ES 'INGRESADO_EE'
	    							if ( $puede_editar && in_array(SolicitudExpedienteExterno::E_INGRESADO_EE, $estados_siguientes) ) {
	    							?>
	    								<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=cambiarEstado&parametros_serializados=<?php echo serializarColeccion($parametros_vista); ?>&data=<?php echo $solicitud_ee->Serializar(); ?>&estado_nuevo=<?php echo SolicitudExpedienteExterno::E_INGRESADO_EE; ?>&generar_nueva_solicitud=0', 'capaVentana');">
	    									<img src="imagenes/b_edit.png" width="14" height="14" >&nbsp;<?php echo "Ingresar al HCD"; ?>
	    								</a>
	    							<?php
	    							} else
	    								echo $this->verificarFecha($solicitud_ee, SolicitudExpedienteExterno::E_INGRESADO_EE);
	    							?>
	    						</td>
                                <!-- Fecha de Devolución del Ente Externo -->
	    						<td class="prestamo_celda_listado_general" style="width:100px;text-align:center;">
	    							<?php
                                    // SI PUEDE EDITAR Y SU SIGUIENTE ESTADO ES 'DEVUELTO'
	    							if ( $puede_editar && in_array(SolicitudExpedienteExterno::E_DEVUELTO_EE, $estados_siguientes) ) {
                                        if ($existe_prestamo_pendiente) {
                                    ?>
                                            <a href="javascript:if(confirm('Existen Préstamos pendientes \n¿Desea realizar la devolución? Se generará una nueva Solicitud para los pedidos del expediente <?php echo $solicitud_ee->anio.'-'.$solicitud_ee->tipo.'-'.$solicitud_ee->numero; ?>')){ refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=cambiarEstado&parametros_serializados=<?php echo serializarColeccion($parametros_vista); ?>&data=<?php echo $solicitud_ee->Serializar(); ?>&estado_nuevo=<?php echo SolicitudExpedienteExterno::E_DEVUELTO_EE; ?>&generar_nueva_solicitud=1', 'capaVentana'); };" title="Devolver Solicitud al Ente Externo">
                                                <img src="imagenes/b_edit.png" width="14" height="14" >&nbsp;<?php echo "Devolver"; ?>
                                            </a>
                                    <?php
                                        } else {
	    							?>
    	    								<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=cambiarEstado&parametros_serializados=<?php echo serializarColeccion($parametros_vista); ?>&data=<?php echo $solicitud_ee->Serializar(); ?>&estado_nuevo=<?php echo SolicitudExpedienteExterno::E_DEVUELTO_EE; ?>&generar_nueva_solicitud=0', 'capaVentana');" title="Devolver Solicitud al Ente Externo">
    	    									<img src="imagenes/b_edit.png" width="14" height="14" >&nbsp;<?php echo "Devolver"; ?>
    	    								</a>
	    							<?php
                                        }
    	    						} else
    	    							echo $this->verificarFecha($solicitud_ee, SolicitudExpedienteExterno::E_DEVUELTO_EE);
                                    ?>
	    						</td>
                                <!-- Fecha de Anulación -->
	    						<td class="prestamo_celda_listado_general" style="width:100px;text-align:center;">
	    							<?php
	    							// SI PUEDE EDITAR Y SU SIGUIENTE ESTADO ES 'ANULADO'
	    							if ( $puede_editar && in_array(SolicitudExpedienteExterno::E_ANULADO_EE, $estados_siguientes) ) {
                                        // Si hay expedientes pendientes de préstamo
                                        if ($existe_prestamo_pendiente) {
                                            echo '---'; // NO se permite Anular aún
                                        } else {
	    							?>	
    	    								<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=cambiarEstado&parametros_serializados=<?php echo serializarColeccion($parametros_vista); ?>&data=<?php echo $solicitud_ee->Serializar(); ?>&estado_nuevo=<?php echo SolicitudExpedienteExterno::E_ANULADO_EE; ?>&generar_nueva_solicitud=0', 'capaVentana');">
    	    									<img src="imagenes/b_edit.png" width="14" height="14" >&nbsp;<?php echo "Anular"; ?>
    	    								</a>
	    							<?php
                                        }
	    							} else
	    								echo $this->verificarFecha($solicitud_ee, SolicitudExpedienteExterno::E_ANULADO_EE);
	    							?>
	    						</td>
	    						<td class="prestamo_celda_listado_general" style="width:155px;<?php echo $this->solicitudEstadoToColor($solicitud_ee); ?>">
	    							<?php echo ($solicitud_ee->estado != '') ? $solicitud_ee->EstadoToString() : '&nbsp;' ; ?>
	    						</td>
	    						<td class="prestamo_celda_listado_general">
	    							<?php echo ($solicitud_ee->observaciones != '') ? $solicitud_ee->observaciones : '&nbsp;' ;//$solicitud_ee->ObtenerResumenObservacion() ?>
	    						</td>
	    					</tr>
	    				<?php
	    					$i++;
	    				}
	    				?>
		    		</tbody>
		    	</table>
    		<?php
    		}
    		else
    			echo "<h3>No se han encontrado resultados.</h3>";	
    		?>
    	</div>
        	
        <?php
    	// Si hay préstamos se muestra el paginador
    	if ( $cantidad > 0 && $parametros_vista['nro_paginas'] > 1 ) {
    		echo $this->mostrarPaginadorSolicitudes($parametros_vista);
    	}
    	?>
        <script>
       	   	// Se resalta el ítem PRÉSTAMOS del menú principal
        	$('item_prestamos_menu_gral').setStyle('background-color','#263D7C');
        
            $('f_tipo').value = '<?php echo ($parametros_vista['tipo']) ? $parametros_vista['tipo'] : 'D'; ?>';

    		// Al editar el número del expediente
    		$('f_numero').addEvent('keydown', function(event) {
    			// Si se presiona la tecla Enter y se ha ingresado un número
    			if(event.key == 'Enter' && $('f_numero').value != '' )
					// Se buscan las Solicitudes para dicho expediente
    				listarPorExpediente();
    		});

    		function listarPorExpediente() {
    			var mensaje = "";
    			var error = false;
    			
    			if($('f_anio').value == '') {
    				mensaje += "No ha ingresado el A"+'\u00f1'+"o del expediente.";
    				error = true;
    			}

    		    if($('f_numero').value == '') {
    				mensaje += "<br> No ha ingresado el N"+'\u00fa'+"mero del expediente.";
    				error = true;
    			}
    				
    			if (error)
    			    alert(mensaje);
    			else
    				//alert('abms/index.php?controlador='+$('controlador').value+'&accion=listarPorExpediente&anio='+$('f_anio').value+'&tipo=D&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&digito='+$('f_digito').value+'&cuerpoalcance='+$('f_cuerpoalcance').value+'&anexoalcance='+$('f_anexoalcance').value+'&cuerpoanexoalcance='+$('f_cuerpoanexoalcance').value+'&anexo='+$('f_anexo').value+'&cuerpoanexo='+$('f_cuerpoanexo').value);
        			refrescar('abms/index.php?controlador='+$('controlador').value+'&accion=listarPorExpediente&anio='+$('f_anio').value+'&tipo=D&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&digito='+$('f_digito').value+'&cuerpoalcance='+$('f_cuerpoalcance').value+'&anexoalcance='+$('f_anexoalcance').value+'&cuerpoanexoalcance='+$('f_cuerpoanexoalcance').value+'&anexo='+$('f_anexo').value+'&cuerpoanexo='+$('f_cuerpoanexo').value+'', 'contenidoAjaxPrincipal');
    		}
    
            // CALENDARIO PARA LA FECHA DESDE DEL CRITERIO DE BUSQUEDA
    		var calendario_fecha_desde = new Zapatec.Calendar.setup({
    			inputField: "f_fecha_desde",
    			ifFormat: "%d/%m/%Y",
    			button: "img_f_fecha_desde",
    			showsTime: false
    		});
    			
    		// CALENDARIO PARA LA FECHA HASTA DEL CRITERIO DE BUSQUEDA
    		var calendario_fecha_hasta = new Zapatec.Calendar.setup({
    			inputField: "f_fecha_hasta",
    			ifFormat: "%d/%m/%Y",
    			button: "img_f_fecha_hasta",
    			showsTime: false
    		});
    			
    		$('f_estado_solicitado_hcd').checked = <?php echo ($parametros_vista['estado_solicitado_hcd'] != '') ? 1 : 0; ?>;
			$('f_estado_solicitado_ee').checked = <?php echo ($parametros_vista['estado_solicitado_ee'] != '') ? 1 : 0; ?>;
    		$('f_estado_ingresado_ee').checked = <?php echo ($parametros_vista['estado_ingresado_ee'] != '') ? 1 : 0; ?>;
    		$('f_estado_devuelto_ee').checked = <?php echo ($parametros_vista['estado_devuelto_ee'] != '') ? 1 : 0; ?>;
    		$('f_estado_anulado_ee').checked = <?php echo ($parametros_vista['estado_anulado_ee'] != '') ? 1 : 0; ?>;
    	
    		function listarPorCriterioBusqueda() {
    			var mensaje = "";
    			var error = false;
    			
    			if( $('f_estado_solicitado_hcd').checked === false && 
    				$('f_estado_solicitado_ee').checked === false && 
    				$('f_estado_ingresado_ee').checked === false &&
    				$('f_estado_devuelto_ee').checked === false &&
    				$('f_estado_anulado_ee').checked === false && 
    				$('f_fecha_desde').value == '' && 
    				$('f_fecha_hasta').value == '' )
    			{
    				mensaje += "Debe utilizar por lo menos un criterio de b"+'\u00fa'+"squeda.";
    				error = true;
    			}
    
    			if (error)
    			    alert(mensaje);
    		    else {	
    				valor_estado_solicitado_hcd = ($('f_estado_solicitado_hcd').checked) ? $('f_estado_solicitado_hcd').value : '';
    				valor_estado_solicitado_ee = ($('f_estado_solicitado_ee').checked) ? $('f_estado_solicitado_ee').value : '';
    				valor_estado_ingresado_ee = ($('f_estado_ingresado_ee').checked) ? $('f_estado_ingresado_ee').value : '';
    				valor_estado_devuelto_ee = ($('f_estado_devuelto_ee').checked) ? $('f_estado_devuelto_ee').value : '';
    				valor_estado_anulado_ee = ($('f_estado_anulado_ee').checked) ? $('f_estado_anulado_ee').value : '';

    				//alert('abms/index.php?controlador='+$('controlador').value+'&accion=listarPorCriterioBusqueda&estado_solicitado_hcd='+valor_estado_solicitado_hcd+'&estado_solicitado_ee='+valor_estado_solicitado_ee+'&estado_ingresado_ee='+valor_estado_ingresado_ee+'&estado_devuelto_ee='+valor_estado_devuelto_ee+'&estado_anulado_ee='+valor_estado_anulado_ee+'&fecha_desde='+$('f_fecha_desde').value+'&fecha_hasta='+$('f_fecha_hasta').value);
    		    	refrescar('abms/index.php?controlador='+$('controlador').value+'&accion=listarPorCriterioBusqueda&estado_solicitado_hcd='+valor_estado_solicitado_hcd+'&estado_solicitado_ee='+valor_estado_solicitado_ee+'&estado_ingresado_ee='+valor_estado_ingresado_ee+'&estado_devuelto_ee='+valor_estado_devuelto_ee+'&estado_anulado_ee='+valor_estado_anulado_ee+'&fecha_desde='+$('f_fecha_desde').value+'&fecha_hasta='+$('f_fecha_hasta').value+'', 'contenidoAjaxPrincipal');
    		    }
    		}
    	
    		function paginarSolicitudes(pagina, sentido)
    		{
    			// Si se buscó por un expediente particular
    			if( $('f_anio').value != '' )
    				refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarPorExpediente&pagina='+pagina+'&sentido='+sentido+'&anio='+$('f_anio').value+'&tipo=D&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&digito='+$('f_digito').value+'&cuerpoalcance='+$('f_cuerpoalcance').value+'&anexoalcance='+$('f_anexoalcance').value+'&cuerpoanexoalcance='+$('f_cuerpoanexoalcance').value+'&anexo='+$('f_anexo').value+'&cuerpoanexo='+$('f_cuerpoanexo').value+'', 'contenidoAjaxPrincipal');
    			// Si se buscó por algún criterio de búsqueda
    			else if( $('f_estado_solicitado_hcd').checked == 1 || 
        				 $('f_estado_solicitado_ee').checked == 1 ||
        				 $('f_estado_ingresado_ee').checked == 1 ||
        				 $('f_estado_devuelto_ee').checked == 1 ||
        				 $('f_estado_anulado_ee').checked == 1 ||
        				 $('f_fecha_hasta').value != '' )
    			{
    				valor_estado_solicitado_hcd = ($('f_estado_solicitado_hcd').checked) ? $('f_estado_solicitado_hcd').value : '';
    				valor_estado_solicitado_ee = ($('f_estado_solicitado_ee').checked) ? $('f_estado_solicitado_ee').value : '';
    				valor_estado_ingresado_ee = ($('f_estado_ingresado_ee').checked) ? $('f_estado_ingresado_ee').value : '';
    				valor_estado_devuelto_ee = ($('f_estado_devuelto_ee').checked) ? $('f_estado_devuelto_ee').value : '';
    				valor_estado_anulado_ee = ($('f_estado_anulado_ee').checked) ? $('f_estado_anulado_ee').value : '';
    
    				refrescar('abms/index.php?controlador='+$('controlador').value+'&accion=listarPorCriterioBusqueda&pagina='+pagina+'&sentido='+sentido+'&estado_solicitado_hcd='+valor_estado_solicitado_hcd+'&estado_solicitado_ee='+valor_estado_solicitado_ee+'&estado_ingresado_ee='+valor_estado_ingresado_ee+'&estado_devuelto_ee='+valor_estado_devuelto_ee+'&estado_anulado_ee='+valor_estado_anulado_ee+'&fecha_desde='+$('f_fecha_desde').value+'&fecha_hasta='+$('f_fecha_hasta').value+'', 'contenidoAjaxPrincipal');
    			} else
    				// Sino se lista el siguiente grupo de préstamos según el sentido elegido
    				refrescar('abms/index.php?controlador='+$('controlador').value+'&accion=listar&pagina='+pagina+'&sentido='+sentido+'', 'contenidoAjaxPrincipal');
    		}
    	
    		function limpiarCriterioBusqueda() {
    			refrescar('abms/index.php?controlador='+$('controlador').value+'&accion=listar', 'contenidoAjaxPrincipal');
    		}
    		
    		// Si hay registros           
            if ( $('cantidad').value > 0 )
            {
	    		$('btGenerarReporte').addEvent('click', function() {
	    			// Si se buscó por un expediente particular
	    			if( $('f_anio').value != '' )
	    				$('btGenerarReporte').setProperty('href', 'abms/index.php?controlador='+$('controlador').value+'&accion=generarReporte&anio='+$('f_anio').value+'&tipo=D&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&digito='+$('f_digito').value+'&cuerpoalcance='+$('f_cuerpoalcance').value+'&anexoalcance='+$('f_anexoalcance').value+'&cuerpoanexoalcance='+$('f_cuerpoanexoalcance').value+'&anexo='+$('f_anexo').value+'&cuerpoanexo='+$('f_cuerpoanexo').value+'');
	    			// Si se buscó por algún criterio de búsqueda
	    			else if( $('f_estado_solicitado_hcd').checked == 1 || 
	        				 $('f_estado_solicitado_ee').checked == 1 ||
	        				 $('f_estado_ingresado_ee').checked == 1 ||
	        				 $('f_estado_devuelto_ee').checked == 1 ||
	        				 $('f_estado_anulado_ee').checked == 1 ||
	        				 $('f_fecha_hasta').value != '' )
	    			{
	    				valor_estado_solicitado_hcd = ($('f_estado_solicitado_hcd').checked) ? $('f_estado_solicitado_hcd').value : '';
	    				valor_estado_solicitado_ee = ($('f_estado_solicitado_ee').checked) ? $('f_estado_solicitado_ee').value : '';
	    				valor_estado_ingresado_ee = ($('f_estado_ingresado_ee').checked) ? $('f_estado_ingresado_ee').value : '';
	    				valor_estado_devuelto_ee = ($('f_estado_devuelto_ee').checked) ? $('f_estado_devuelto_ee').value : '';
	    				valor_estado_anulado_ee = ($('f_estado_anulado_ee').checked) ? $('f_estado_anulado_ee').value : '';
	    
	    				//alert('abms/index.php?controlador='+$('controlador').value+'&accion=generarReporte&estado_solicitado_hcd='+valor_estado_solicitado_hcd+'&estado_solicitado_ee='+valor_estado_solicitado_ee+'&estado_ingresado_ee='+valor_estado_ingresado_ee+'&estado_devuelto_ee='+valor_estado_devuelto_ee+'&estado_anulado_ee='+valor_estado_anulado_ee+'&fecha_desde='+$('f_fecha_desde').value+'&fecha_hasta='+$('f_fecha_hasta').value+'');
	    				$('btGenerarReporte').setProperty('href', 'abms/index.php?controlador='+$('controlador').value+'&accion=generarReporte&estado_solicitado_hcd='+valor_estado_solicitado_hcd+'&estado_solicitado_ee='+valor_estado_solicitado_ee+'&estado_ingresado_ee='+valor_estado_ingresado_ee+'&estado_devuelto_ee='+valor_estado_devuelto_ee+'&estado_anulado_ee='+valor_estado_anulado_ee+'&fecha_desde='+$('f_fecha_desde').value+'&fecha_hasta='+$('f_fecha_hasta').value+'');
	    			} else
	    				// Sino se lista el siguiente grupo de préstamos según el sentido elegido
	    				$('btGenerarReporte').setProperty('href', 'abms/index.php?controlador='+$('controlador').value+'&accion=generarReporte');
	    		});
    		}
    	</script>
    <?php	
	}

	/**
	 * Se genera el reporte en formato PDF para guardarlo y/o imprimirlo
	 * (los métodos utilizados aquí están definidos en el archivo html2pdf.class.php)
	 * @param Array $solicitudes, colección de instancias de Solicitudes
	 * @param Array $parametros_vista
	 */
	public function generarReporte($solicitudes = '', $parametros_vista = '')
	{
		//guardarEnTxt("solicitudes_en_generarReporte_solicitudes_Vista", $solicitudes);
		
		ob_start();
	?>
		<style type="text/css">
			table {
				width: 100%;
				padding: 0;
				border-collapse: collapse;
			}
			.pdf_tabla_titulos {
				padding:2px 0 2px 0;
				text-align:center;
			}
			.pdf_tabla_titulos th {
				font-family: Arial;
				font-size: 11px;
				font-weight: 500;
				color: #fff;
				background-color: #004E65;
				border: 1px solid #BEBEBE;
				padding: 3px;
			}
			.pdf_cuerpo_scrolleable {
				overflow: auto;
				padding-right: 15px;
				background-color: #fff;
			}
			.pdf_cuerpo_scrolleable tr {
				height: 21px;
				font-family: Arial;
				font-size: 11px;
			}
			.pdf_cuerpo_scrolleable td {
				height: 21px;
				padding: 0 3px 0 3px;
				border: 1px solid #BEBEBE;
			}
			.pdf_valor_celda_alineado_izquierda {
				text-align: left;
			}
			.pdf_valor_celda_alineado_derecha {
				text-align: right;
			}
			.pdf_valor_celda_centrado {
				text-align: center;
			}
		</style>
		<page backtop="31mm" backbottom="7mm" backleft="5mm" backright="1mm">
			<page_header>
				<table>
					<tr>
						<td style="width:10%;" rowspan="5">
							<img src="../imagenes/escudo_cuatro_colores.gif" width="80" height="100" align="center" >
						</td>
						<td style="width:90%;">&nbsp;</td>
					</tr>
					<tr>
						<td class="pdf_valor_celda_alineado_izquierda" style="width:90%;">Municipalidad de General Pueyrredon</td>
					</tr>
					<tr>
						<td class="pdf_valor_celda_alineado_izquierda" style="width:90%;">Honorable Concejo Deliberante</td>
					</tr>
					<tr>
						<td class="pdf_valor_celda_alineado_izquierda" style="width:90%;">Sistema de Expedientes - <b>Solicitudes de Expedientes Externos</b></td>
					</tr>
					<tr>
						<td class="pdf_valor_celda_alineado_izquierda" style="width:90%;">&nbsp;</td>
					</tr>
				</table>
			</page_header>
			<page_footer>
				<table style="border:solid 1px black;">
					<tr>
						<td class="pdf_valor_celda_alineado_izquierda" style="width:50%">Fecha: <?php echo date("d/m/Y"); ?></td>
						<td class="pdf_valor_celda_alineado_derecha" style="width:50%">P&aacute;gina [[page_cu]] de [[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<table>
				<thead class="pdf_tabla_titulos">
					<tr>
						<th>Expediente</th>
    	    			<th>Fecha<br>Solicitud HCD</th>
    	    			<th>Fecha<br>Solicitud E.E.</th>
    	    			<th>Fecha<br>Ingreso HCD</th>
    	    			<th>Fecha<br>Devoluci&oacute;n E.E.</th>
    	    			<th>Fecha<br>Anulado</th>
    	    			<th>Estado</th>
    	    			<th>Observaciones</th>
					</tr>
				</thead>
				<tbody class="pdf_cuerpo_scrolleable">		
					<?php
					foreach ($solicitudes as $solicitud_ee)
					{
					?>
						<tr class="pdf_tabla_titulos">
							<td class="pdf_valor_celda_centrado" style="width:150px;">
								<?php echo $solicitud_ee->ToStringDescription(); ?>
							</td>
							<td class="pdf_valor_celda_centrado" style="width:70px;">
								<?php echo $this->verificarFecha($solicitud_ee, SolicitudExpedienteExterno::E_SOLICITADO_HCD); ?>
							</td>
							<td class="pdf_valor_celda_centrado" style="width:70px;">
								<?php echo $this->verificarFecha($solicitud_ee, SolicitudExpedienteExterno::E_SOLICITADO_EE); ?>
							</td>
							<td class="pdf_valor_celda_centrado" style="width:70px;">
								<?php echo $this->verificarFecha($solicitud_ee, SolicitudExpedienteExterno::E_INGRESADO_EE); ?>
							</td>
							<td class="pdf_valor_celda_centrado" style="width:70px;">
								<?php echo $this->verificarFecha($solicitud_ee, SolicitudExpedienteExterno::E_DEVUELTO_EE); ?>
							</td>
							<td class="pdf_valor_celda_centrado" style="width:70px;">
								<?php echo $this->verificarFecha($solicitud_ee, SolicitudExpedienteExterno::E_ANULADO_EE); ?>
							</td>
							<td class="pdf_valor_celda_alineado_izquierda" style="width:120px;">
								<?php echo ($solicitud_ee->estado != '') ? $solicitud_ee->EstadoToString() : '&nbsp;' ; ?>
							</td>
							<td class="pdf_valor_celda_alineado_izquierda" style="width:520px;">
								<?php echo ($solicitud_ee->observaciones != '') ? $solicitud_ee->observaciones : '&nbsp;' ;//ObtenerResumenObservacion() ?>
							</td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</page>
	<?php
		$content = ob_get_clean();
		try
		{
			// Se configura la página
			$html2pdf = new HTML2PDF('L','LEGAL','es', array(mL, mT, mR, mB));
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->setDefaultFont('Arial');
			
			// Se realiza la conversión HTML => PDF
			$html2pdf->WriteHTML($content);
			
			// Se envía el documento al navegador, 
			// para que se visualice la ventana para Abrir o Guardar dicho documento
			$html2pdf->Output('listado_solicitudes_expediente_externo.pdf', 'D');
		}
		catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
		}	
	}
	
	/**
	 * Devuelve el color de fondo y texto según el estado actual de la Solicitud
	 * @param SolicitudExpedienteExterno $solicitud_ee
	 * @return string $color_fondo_y_texto
	 */
	public function solicitudEstadoToColor(SolicitudExpedienteExterno $solicitud_ee)
	{
		$color_fondo_y_texto = "";
		switch ($solicitud_ee->estado)
		{
			case SolicitudExpedienteExterno::E_SOLICITADO_HCD:
				$color_fondo_y_texto = "background-color: #FCF8E3;color: #C09853;";// AMARILLO PASTEL
				break;
			case SolicitudExpedienteExterno::E_SOLICITADO_EE:
				$color_fondo_y_texto = "background-color: #FCF8E3;color: #C09853;";// AMARILLO PASTEL
				break;
			case SolicitudExpedienteExterno::E_INGRESADO_EE:
				$color_fondo_y_texto = "background-color: #F2DEDE;color: #B94A48;";// ROJO PASTEL
				break;
			case SolicitudExpedienteExterno::E_DEVUELTO_EE:
				$color_fondo_y_texto = "background-color: #DFF0D8;color: #468847;";// VERDE PASTEL
				break;
			case SolicitudExpedienteExterno::E_ANULADO_EE:
				$color_fondo_y_texto = "background-color: #D9D9D9;color: #3A3A3A;";// GRIS PASTEL
				break;
		}
	
		return $color_fondo_y_texto;
	}
	
	/**
	 * Verifica la fecha para un estado determinado de un Préstamo
	 * @param SolicitudExpedienteExterno $solicitud_ee
	 * @param string $estado
	 * @return string Devuelve la fecha en formato dd/mm/aaaa, ó la expresión "---" en caso de ser nula.
	 */
	public function verificarFecha(SolicitudExpedienteExterno $solicitud_ee, $estado)
	{
		if ($solicitud_ee->ObtenerFechaSegunEstado($estado) === null)
		{
			return "---";
		}
		else
		{
			return $this->formatearFecha(substr($solicitud_ee->ObtenerFechaSegunEstado($estado), 0, 10));
		}
	}

	/**
	 * Devuelve el nombre del estado respectivo
	 * @param string $estado, estado a asignar al Préstamo
	 */
	public function mostrarEstado($estado)
	{
		$nombre = "";
		switch($estado)
		{
			case SolicitudExpedienteExterno::E_SOLICITADO_EE:
				$nombre = "Solicitado al E.E.";
				break;
			case SolicitudExpedienteExterno::E_INGRESADO_EE:
				$nombre = "Ingresado al HCD";
				break;
			case SolicitudExpedienteExterno::E_DEVUELTO_EE:
				$nombre = "Devuelto al E.E.";
				break;
			case SolicitudExpedienteExterno::E_ANULADO_EE:
				$nombre = "Anulado";
				break;
			default:
				$nombre = "Solicitado al HCD";
				break;
		}
		 
		return $nombre;
	}

	/**
	 * Formulario para editar la fecha y datos respectivos para cambiar el estado determinado de una Solicitud
	 * @param SolicitudExpedienteExterno $solicitud_ee
	 * @param array $parametros_vista
	 */
	public function editarCambioEstado($solicitud_ee, $parametros_vista)
	{
	?>
    	<script type="text/javascript">
    		$("capaFondo").setStyle('visibility','visible');
    		$("capaVentana").setStyle('visibility','visible');
    	</script>
	    	
    	<div id="precarga_principal" style="display:none"></div>
    	
    	<div id="contenedoraCambioEstado" class="prestamo_edicion_texto">
    		
			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>
			
    		<form action="abms/index.php" method="POST" name="<?php echo $this->formulario_cambio_estado; ?>" id="<?php echo $this->formulario_cambio_estado; ?>">
    		    	
		    	<input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
		    	
		    	<input type="hidden" name="accion" id="accion" value="guardarCambioEstado" />
		    	
		    	<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
		    	
		    	<input type="hidden" name="parametros_serializados" id="parametros_serializados" value="<?php echo $parametros_vista['parametros_serializados']; ?>" />
		    	
                <!-- AGREGADA EL 03/08/2017 -->
                <input type="hidden" name="generar_nueva_solicitud" id="generar_nueva_solicitud" value="<?php echo (isset($parametros_vista['generar_nueva_solicitud']) && $parametros_vista['generar_nueva_solicitud'] != '') ? $parametros_vista['generar_nueva_solicitud'] : 0; ?>" />
                
				<!-- CLAVE DE LA SOLICITUD -->
				<input type="hidden" name="anio" id="anio" value="<?php echo $solicitud_ee->anio; ?>" />
				<input type="hidden" name="tipo" id="tipo" value="<?php echo $solicitud_ee->tipo; ?>" />
				<input type="hidden" name="numero" id="numero" value="<?php echo $solicitud_ee->numero; ?>" />
				<input type="hidden" name="cuerpo" id="cuerpo" value="<?php echo $solicitud_ee->cuerpo; ?>" />
				<input type="hidden" name="alcance" id="alcance" value="<?php echo $solicitud_ee->alcance; ?>" />
				<input type="hidden" name="digito" id="digito" value="<?php echo $solicitud_ee->digito; ?>" />
				<input type="hidden" name="cuerpoalcance" id="cuerpoalcance" value="<?php echo $solicitud_ee->cuerpoalcance; ?>" />
				<input type="hidden" name="anexoalcance" id="anexoalcance" value="<?php echo $solicitud_ee->anexoalcance; ?>" />
				<input type="hidden" name="cuerpoanexoalcance" id="cuerpoanexoalcance" value="<?php echo $solicitud_ee->cuerpoanexoalcance; ?>" />
				<input type="hidden" name="anexo" id="anexo" value="<?php echo $solicitud_ee->anexo; ?>" />
				<input type="hidden" name="cuerpoanexo" id="cuerpoanexo" value="<?php echo $solicitud_ee->cuerpoanexo; ?>" />
				<input type="hidden" name="fecha_solicitud_hcd" id="fecha_solicitud_hcd" value="<?php echo $solicitud_ee->fecha_solicitud_hcd; ?>" />
				
				<!-- ESTADO A ASIGNARLE A LA SOLICITUD -->
				<input type="hidden" name="estado_nuevo" id="estado_nuevo" value="<?php echo $parametros_vista['estado_nuevo']; ?>" />
				
				<div id="dragger_cambio_estado" class="degradado">Cambio a estado <?php echo $this->mostrarEstado($parametros_vista['estado_nuevo']); ?></div>
				<div class="prestamo_cambio_estado_seccion_fecha">
					Fecha:&nbsp;
					<input type="text" name="solo_fecha_estado" id="solo_fecha_estado" value="<?php echo date("d/m/Y"); ?>" size="8" maxlength="10" onkeyup="mascara(this, '/', patron, true);" />
					&nbsp;<input type="image" id="img_solo_fecha_estado" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la Fecha" width="16" height="16">
					&nbsp;&nbsp;
					Hora:&nbsp;
					<input type="text" name="solo_hora_estado" id="solo_hora_estado" value="<?php echo date("H:i:s"); ?>" size="6" maxlength="8" onkeyup="mascara(this, ':', patron_hora, true);" />
				
					<input type="hidden" name="fecha_estado" id="fecha_estado" value="" />
				</div>
				<div class="prestamo_cambio_estado_seccion_observaciones">
					Observaciones:<br>
					<textarea name="observaciones" id="observaciones"><?php echo $solicitud_ee->observaciones; ?></textarea>
				</div>
				<div class="prestamo_cambio_estado_seccion_botones">
					<div class="prestamo_cambio_estado_margen_lateral_botones"></div>
					<div class="p_boton_edicion">
						<a href="javascript:registrarCambioEstado();">
							<img src="imagenes/barra/ok_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Guardar
						</a>
					</div>
					<div class="prestamo_cambio_estado_margen_medio_botones"></div>
					<div class="p_boton_edicion">
						<a href="javascript:cerrarModalNueva();">
							<img src="imagenes/barra/error_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Cancelar
						</a>
					</div>
				</div>
			</form>
		</div>
		
		<script type="text/javascript">
		
			// Evalua el valor de ciertos datos, y envía los datos al controlador
			function registrarCambioEstado()
			{
				var mensaje = "";
				var error = false;
			
				if ( $('solo_fecha_estado').value == '' )
				{
					error = true;
					mensaje += "Debe ingresar una Fecha.";
				}

				if ( $('solo_hora_estado').value == '' )
				{
					error = true;
					mensaje += "\n Debe ingresar un Horario.";
				}
				
				if ( error )
					alert(mensaje);
				else {
					// Se unen la fecha y horario del cambio de estado para su guardado en la DB
					$('fecha_estado').value = formatearConGuion($('solo_fecha_estado').value)+" "+$('solo_hora_estado').value;

					// Se envían los datos al método respectivo del controlador
					enviarForm('<?php echo $this->formulario_cambio_estado; ?>', 'abms', 'contenidoAjaxPrincipal');
				}
			}

			var menuDrag = new Drag.Move($('contenedoraCambioEstado'), {
			   handle: $('dragger_cambio_estado')
			  }
			);
			
			// CALENDARIO PARA LA FECHA DEL ESTADO DE LA SOLICITUD
			var calendario = new Zapatec.Calendar.setup(
			{
				inputField: "solo_fecha_estado",
				ifFormat: "%d/%m/%Y",
				button: "img_solo_fecha_estado",
				showsTime: false
			});
		</script>
    <?php	
    }

    /**
     * Formulario para editar la observación de una Solicitud
     * @param Prestamo $prestamo
     * @param array $parametros_vista
     */
    public function editarObservaciones($solicitud_ee, $parametros_vista)
    {
    ?>
        <script type="text/javascript">
        	$("capaFondo").setStyle('visibility','visible');
        	$("capaVentana").setStyle('visibility','visible');
        </script>
        	
        <div id="precarga_principal" style="display:none"></div>
        	
        <div id="contenedoraEdicionObservaciones" class="prestamo_edicion_texto">
            		
		<div id="fade" class="overlay"></div>
       		<div id="light" class="modal"></div>
        			
           	<form action="abms/index.php" method="POST" name="formEdicionObservaciones" id="formEdicionObservaciones">
           		    	
       		   	<input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
        		    	
       		   	<input type="hidden" name="accion" id="accion" value="guardarObservaciones" />
        		    
       		    <input type="hidden" name="perfil" id="perfil" value="<?php echo $_SESSION['perfil2']; ?>" />
        		    	
       		   	<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
        		   	
       		   	<input type="hidden" name="parametros_serializados" id="parametros_serializados" value="<?php echo $parametros_vista['parametros_serializados']; ?>" />
        		    
        	    <!-- Estado actual del Préstamo -->
        	    <input type="hidden" name="estado" id="estado" value="<?php echo $solicitud_ee->estado; ?>" />
        		    
        		<!-- Clave del Préstamo -->
        		<input type="hidden" name="anio" id="anio" value="<?php echo $solicitud_ee->anio; ?>" />
        		<input type="hidden" name="tipo" id="tipo" value="<?php echo $solicitud_ee->tipo; ?>" />
        		<input type="hidden" name="numero" id="numero" value="<?php echo $solicitud_ee->numero; ?>" />
        		<input type="hidden" name="cuerpo" id="cuerpo" value="<?php echo $solicitud_ee->cuerpo; ?>" />
        		<input type="hidden" name="alcance" id="alcance" value="<?php echo $solicitud_ee->alcance; ?>" />
        		<input type="hidden" name="digito" id="digito" value="<?php echo $solicitud_ee->digito; ?>" />
        		<input type="hidden" name="cuerpoalcance" id="cuerpoalcance" value="<?php echo $solicitud_ee->cuerpoalcance; ?>" />
        		<input type="hidden" name="anexoalcance" id="anexoalcance" value="<?php echo $solicitud_ee->anexoalcance; ?>" />
        		<input type="hidden" name="cuerpoanexoalcance" id="cuerpoanexoalcance" value="<?php echo $solicitud_ee->cuerpoanexoalcance; ?>" />
        		<input type="hidden" name="anexo" id="anexo" value="<?php echo $solicitud_ee->anexo; ?>" />
        		<input type="hidden" name="cuerpoanexo" id="cuerpoanexo" value="<?php echo $solicitud_ee->cuerpoanexo; ?>" />
        		<input type="hidden" name="fecha_solicitud_hcd" id="fecha_solicitud_hcd" value="<?php echo $solicitud_ee->fecha_solicitud_hcd; ?>" />
        				
        		<div id="dragger_edicion_observaciones" class="degradado">
        			Solicitud <?php echo $solicitud_ee->anio.'-'.$solicitud_ee->tipo.'-'.$solicitud_ee->numero; ?>
        		</div>
        			
        		<div class="prestamo_cambio_estado_seccion_observaciones">
        			Observaciones:<br>
        			<textarea name="observaciones" id="observaciones"><?php echo $solicitud_ee->observaciones; ?></textarea>
        		</div>
        		<div class="prestamo_cambio_estado_seccion_botones">
        			<div class="prestamo_cambio_estado_margen_lateral_botones"></div>
        			<div class="p_boton_edicion">
        				<a href="javascript:guardarModificaciones();">
        					<img src="imagenes/barra/ok_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Guardar
        				</a>
        			</div>
        			<div class="prestamo_cambio_estado_margen_medio_botones"></div>
        			<div class="p_boton_edicion">
        				<a href="javascript:cerrarModalNueva();">
        					<img src="imagenes/barra/error_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Cancelar
        				</a>
        			</div>
        		</div>
        	</form>
       	</div>
       	<script type="text/javascript">
        	
       		// Evalua el valor de ciertos datos, y envía los datos al controlador
       		function guardarModificaciones()
       		{
       			// Se envían los datos al método respectivo del controlador
       			enviarForm('formEdicionObservaciones', 'abms', 'contenidoAjaxPrincipal');
       		}
       
       		var menuDrag = new Drag.Move($('contenedoraEdicionObservaciones'), {
       		   handle: $('dragger_edicion_observaciones')
       		});
       	</script>
	<?php
    }
        
}

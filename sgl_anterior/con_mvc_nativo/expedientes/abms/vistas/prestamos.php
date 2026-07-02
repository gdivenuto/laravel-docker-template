<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

// Configuracion de rutas
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/config/path_config.php');

// Clases del modelo
require_once(PATH_SGL_LAYER_MODELO_PRESTAMOS.'Prestamo.php');

class VistaPrestamos extends VistaBase
{
	private $controlador;
    private $formulario;

    public function __construct()
    {
		$this->controlador = 'prestamos';
		$this->formulario = 'formEdicionPrestamo';
    }

    /**
     * Muestra el paginador de préstamos
     * @param array $parametros_vista
     */
    public function mostrarPaginadorPrestamos($parametros_vista = '')
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
				<a id="btPrimero" title="Primer p&aacute;gina" href="javascript:paginarPrestamo(1, 'primero');">
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
				<a id="btAnterior" title="P&aacute;gina anterior" href="javascript:paginarPrestamo(<?php echo $parametros_vista['pagina_ant']; ?>, 'anterior');">
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
				<a id="btSiguiente" title="P&aacute;gina siguiente" href="javascript:paginarPrestamo(<?php echo $parametros_vista['pagina_sgte']; ?>, 'siguiente');">
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
				<a id="btUltimo" title="&Uacute;ltima p&aacute;gina" href="javascript:paginarPrestamo(<?php echo $parametros_vista['nro_paginas']; ?>, 'ultimo');">
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
				paginarPrestamo($('cmb_nro_pagina').value, '');
			});
		</script>
    	<?php
    }

    /**
     * Se listan los préstamos, pertenecientes tanto al HCD como a Entes Externos
     * @param Array $listado_prestamos, colección de instancias de Préstamos
     * @param ng_prestamos $ng_prestamos
     * @param Array $solicitantes
     * @param Array $parametros_vista
     */
    public function mostrarGrillaGeneral($listado_prestamos = '',  ng_prestamos $ng_prestamos, $solicitantes = null, $parametros_vista = '')
    {
    	// MENSAJE DEL RESULTADO DE LA OPERACION REALIZADA
    	$this->mostrarCartelResultado($parametros_vista['mensaje'], $parametros_vista['tipo_mensaje']);

    	// Cantidad de préstamos obtenidos
    	$cantidad = count($listado_prestamos);

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
    	<input type="hidden" id="cantidad" value="<?php echo $cantidad; ?>" />
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
    		<div class="prestamo_titulo_grilla">PR&Eacute;STAMOS</div>

    		<!-- BOTON VOLVER -->
    		<div class="p_boton_edicion" style="float:right">
				<a href="index.php" title="Volver al listado de Expedientes">
					<img src="imagenes/barra/volver.jpeg" width="15" height="15" />&nbsp;Volver
				</a>
			</div>
			<?php
    	    // Si hay préstamos que listar en el reporte
    	    if ($cantidad > 0) {
    	    ?>
				<div class="p_buscador_margen_datos" style="float:right"></div>
				<!-- BOTON GENERAR REPORTE -->
				<div class="p_boton_edicion" style="width:130px;float:right">
					<a id="btGenerarReporte" title="Generar reporte" style="width:130px;">
						<img src="imagenes/pdf.jpg" width="15" height="15" />&nbsp;Generar reporte
					</a>
				</div>
			<?php
    	    }
			?>
			<div class="p_buscador_margen_datos" style="float:right"></div>
			<!-- BOTON NUEVO -->
    		<div class="p_boton_edicion" style="float:right">
    			<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=agregar&grilla_origen=grilla_general', 'contenidoAjaxPrincipal');" title="Agregar Pr&eacute;stamo">
    				<img src="imagenes/barra/add_16x16.gif" width="16" height="16" align="top" />&nbsp;Nuevo
    			</a>
    		</div>
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
    				<option value="0">Todos</option>
    				<option value="E">E</option>
    				<option value="N">N</option>
    				<option value="R">R</option>
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
    			<a href="javascript:buscarPrestamosPorExpediente();" title="Buscar Expediente">
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
	   			&nbsp;<input type="checkbox" id="f_estado_solicitado" name="f_estado_solicitado" value="<?php echo Prestamo::E_SOLICITADO; ?>">&nbsp;Solicitado
	   			&nbsp;<input type="checkbox" id="f_estado_prestado" name="f_estado_prestado" value="<?php echo Prestamo::E_PRESTADO; ?>">&nbsp;Prestado
	   			&nbsp;<input type="checkbox" id="f_estado_devuelto" name="f_estado_devuelto" value="<?php echo Prestamo::E_DEVUELTO; ?>">&nbsp;Devuelto
	   			&nbsp;<input type="checkbox" id="f_estado_anulado" name="f_estado_anulado" value="<?php echo Prestamo::E_ANULADO; ?>">&nbsp;Anulado
	   		</div>
	   	</div>
	   	<div class="p_buscador">
	   		<div class="p_buscador_margen_datos"></div>
	   		<div class="p_buscador_dato p_buscador_texto">
	   			Solicitante:&nbsp;
	   			<?php echo $this->mostrarComboSolicitantes($solicitantes); ?>
				&nbsp;&nbsp;
				Fecha desde:&nbsp;
				<input type="text" name="f_fecha_desde" id="f_fecha_desde" value="<?php echo ($parametros_vista['fecha_desde']) ? $parametros_vista['fecha_desde'] : '';//date("d/m/").( date("Y")-2 ) ?>" size="8" maxlength="10" onkeyup="mascara(this, '/', patron, true);" />
				&nbsp;<input type="image" id="img_f_fecha_desde" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la Fecha Desde" width="16" height="16">
				&nbsp;&nbsp;
				Fecha hasta:&nbsp;
				<input type="text" name="f_fecha_hasta" id="f_fecha_hasta" value="<?php echo ($parametros_vista['fecha_hasta']) ? $parametros_vista['fecha_hasta'] : ''; ?>" size="8" maxlength="10" onkeyup="mascara(this, '/', patron, true);" />
				&nbsp;<input type="image" id="img_f_fecha_hasta" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la Fecha Hasta" width="16" height="16">
			</div>
			<div class="p_buscador_margen_datos" style="width:22px;"></div>
	    	<div class="p_boton_edicion">
	    		<a href="javascript:buscarConCriterio();" title="Filtrar por Estado, Solicitante y/o Rango de fechas">
	    			<img src="imagenes/zoom_16x16.gif" width="16" height="16" align="top" />&nbsp;Filtrar
	    		</a>
	    	</div>
	    	<div class="p_buscador_margen_datos"></div>
	    	<div class="p_boton_edicion">
				<a href="javascript:limpiarCriterioBusquedaPrestamos();" title="Limpiar criterio de b&uacute;squeda">
					<img src="imagenes/limpiar.png" width="15" height="15" />&nbsp;Limpiar
				</a>
			</div>
		</div>

    	<div class="p_borde_superior"></div>
    	<div class="p_borde_superior_sin_linea"></div>

   	    <div class="contenedor_listado contenedor_listado_prestamos">
    	   	<?php
    	   	// Si hay préstamos para mostrar
    		if ( $cantidad > 0) {
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
	    				<th class="orden_link">Solicitante</th>
	    				<th class="orden_link">Fecha<br>Solicitud</th>
	    				<th class="orden_link">Fecha<br>Pr&eacute;stamo</th>
	    				<th class="orden_link">Fecha<br>Devoluci&oacute;n</th>
	    				<th class="orden_link">Fecha<br>Anulado</th>
	    				<th class="orden_link">Estado</th>
	    				<th class="orden_link">Nro.</th>
	    				<th class="orden_link">Folio</th>
	    				<th class="orden_link">Observaciones</th>
	    			</thead>
	    			<tbody id="e_cuerpo_scrolleable" class="e_cuerpo_scrolleable">
	    				<?php
	    				$i = 0;
	    				foreach ($listado_prestamos as $prestamo) {
	    					// SE OBTIENEN LOS ESTADOS SIGUIENTES QUE PUEDE TOMAR EL PRESTAMO
	    					$estados_siguientes = $ng_prestamos->ObtenerEstadosSiguientes($prestamo);
	    				?>
	    					<tr onmouseover="javascript:this.setStyle('background-color', '#EBEFF9');" onmouseout="javascript:this.setStyle('background-color', '#ffffff');">
		    					<?php
	    					    // Si tiene permiso para Editar
								if ($puede_editar) {
								?>
									<td style="width:22px">
										<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarObservaciones&grilla_origen=grilla_general&parametros_serializados=<?php echo serializarColeccion($parametros_vista); ?>&data=<?php echo $prestamo->Serializar(); ?>', 'capaVentana');" title="Editar Pr&eacute;stamo" >
											&nbsp;<img src="imagenes/b_edit.png" width="14" height="14" align="center" />
										</a>
									</td>
								<?php
								}
								// Si tiene permiso para Eliminar
								if ($puede_eliminar) {
									// Si aún NO se prestó o ya se encuentra cerrado (Devuelto o Anulado)
									if ( in_array(Prestamo::E_PRESTADO, $estados_siguientes) || empty($estados_siguientes)) {
								?>
										<td style="width:22px">
											<a href="javascript:if(confirm('¿Desea eliminar el Préstamo <?php echo $prestamo->anio.'-'.$prestamo->tipo.'-'.$prestamo->numero; ?> definitivamente?')){refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=eliminar&grilla_origen=grilla_general&data=<?php echo $prestamo->Serializar(); ?>&parametros_serializados=<?php echo serializarColeccion($parametros_vista); ?>', 'contenidoAjaxPrincipal');};" title="Eliminar Pr&eacute;stamo">
												&nbsp;<img src="imagenes/b_drop.png" width="14" height="14" align="center" />
											</a>
										</td>
								<?php
									} else {
								?>
										<td style="width:22px" title="No se puede eliminar, no se encuentra cerrado el Pr&eacute;stamo (Devuelto o Anulado).">
	                                        &nbsp;<img src="imagenes/b_drop_gris.png" width="14" height="14" align="center" />
	                                    </td>
								<?php
									}
								}
								?>
								<td class="prestamo_celda_listado_general" style="width:180px;">
	    							<?php
	    							// Muestra la descripcion de la clave del expediente
	    							echo $prestamo->ToStringDescription();

	    							// XXXX 27/07/2017 SE AGREGÓ EL TIPO O = Otro
    								// Si el préstamo es de un expediente externo (D = D.E., O = Otro Ente)
    								if ($prestamo->tipo == 'D' || $prestamo->tipo == 'O') {
    								?>
    									&nbsp;
    									<a style="padding:3px;height:16px;background-color:#fff;display:inline;" href="javascript:refrescar('abms/index.php?controlador=solicitud_expediente_externo&accion=listarPorExpediente&anio=<?php echo $prestamo->anio; ?>&tipo=<?php echo $prestamo->tipo; ?>&numero=<?php echo $prestamo->numero; ?>', 'contenidoAjaxPrincipal');" title="Ver Solicitud" >
    										<img src="imagenes/print_etiq_16x16.gif" width="14" height="14" >
    									</a>
    								<?php
    								}
    								/**
    								// Muestra una advertencia en caso que se requiera generar una solicitud
    								if ( $ng_prestamos->RequiereSolicitudExpedienteExterno($prestamo) ) {
    								?>
    									<a style="width:16px;height:16px;background-color:#fff;display:inline" href="javascript:if (confirm('¿Desea generar la solicitud <?php echo $prestamo->anio.'-'.$prestamo->tipo.'-'.$prestamo->numero; ?> al Ente Externo ?')) {refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=generarSolicitudEE&data=<?php echo $prestamo->Serializar(); ?>&pagina=<?php echo $parametros_vista['pagina']; ?>', 'contenidoAjaxPrincipal'); };" title="Generar solicitud de pr&eacute;stamo de un expediente al Ente Externo" >
    								    	<img src="imagenes/barra/advertencia.png" width="18" height="18" />
    								    </a>
    								<?php
    								}
    								/**/
    								?>
	    						</td>
	    						<td class="prestamo_celda_listado_general" style="width:230px">
	    							<?php echo ($prestamo->solicitante_nombre != '') ? $prestamo->solicitante_nombre : '&nbsp;' ; ?>
	    						</td>
	    						<td class="prestamo_celda_listado_general" style="width:100px;text-align:center;">
	    							<?php echo $this->verificarFecha($prestamo, Prestamo::E_SOLICITADO); ?>
	    						</td>
	    						<td class="prestamo_celda_listado_general" style="width:100px;text-align: center;">
	    							<?php
	    							// SI PUEDE EDITAR Y SU SIGUIENTE ESTADO ES 'PRESTADO'
	    							if ( $puede_editar && in_array(Prestamo::E_PRESTADO, $estados_siguientes) ) {
	    							?>
	    								<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=cambiarEstado&grilla_origen=grilla_general&parametros_serializados=<?php echo serializarColeccion($parametros_vista); ?>&data=<?php echo $prestamo->Serializar(); ?>&estado_nuevo=<?php echo Prestamo::E_PRESTADO; ?>', 'capaVentana');">
	    									<img src="imagenes/b_edit.png" width="14" height="14" >&nbsp;<?php echo "Prestar"; ?>
	    								</a>
	    							<?php
	    							}
	    							else
	    								echo $this->verificarFecha($prestamo, Prestamo::E_PRESTADO);
	    							?>
	    						</td>
	    						<td class="prestamo_celda_listado_general" style="width:100px;text-align: center;">
	    							<?php
	    							// SI PUEDE EDITAR Y SU SIGUIENTE ESTADO ES 'DEVUELTO'
	    							if ( $puede_editar && in_array(Prestamo::E_DEVUELTO, $estados_siguientes) ) {
	    							?>
	    								<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=cambiarEstado&grilla_origen=grilla_general&parametros_serializados=<?php echo serializarColeccion($parametros_vista); ?>&data=<?php echo $prestamo->Serializar(); ?>&estado_nuevo=<?php echo Prestamo::E_DEVUELTO; ?>', 'capaVentana');">
	    									<img src="imagenes/b_edit.png" width="14" height="14" >&nbsp;<?php echo "Devuelto"; ?>
	    								</a>
	    							<?php
	    							}
	    							else
	    								echo $this->verificarFecha($prestamo, Prestamo::E_DEVUELTO);
	    							?>
	    						</td>
	    						<td class="prestamo_celda_listado_general" style="width:100px;text-align:center;">
	    							<?php
	    							// SI PUEDE EDITAR Y SU SIGUIENTE ESTADO ES 'ANULADO'
	    							if ( $puede_editar && in_array(Prestamo::E_ANULADO, $estados_siguientes) ) {
	    							?>
	    								<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=cambiarEstado&grilla_origen=grilla_general&parametros_serializados=<?php echo serializarColeccion($parametros_vista); ?>&data=<?php echo $prestamo->Serializar(); ?>&estado_nuevo=<?php echo Prestamo::E_ANULADO; ?>', 'capaVentana');">
	    									<img src="imagenes/b_edit.png" width="14" height="14" >&nbsp;<?php echo "Anular"; ?>
	    								</a>
	    							<?php
	    							}
	    							else
	    								echo $this->verificarFecha($prestamo, Prestamo::E_ANULADO);
	    							?>
	    						</td>
	    						<td class="prestamo_celda_listado_general" style="width:120px;<?php echo $this->prestamoEstadoToColor($prestamo); ?>">
	    							<?php echo ($prestamo->estado != '') ? $prestamo->EstadoToString() : '&nbsp;' ; ?>
	    						</td>
	    						<td class="prestamo_celda_listado_general" style="width:30px;text-align:right;">
	    							<?php echo ($prestamo->libro_numero != '') ? ($prestamo->libro_numero) : '&nbsp;' ; ?>
	    						</td>
	    						<td class="prestamo_celda_listado_general" style="width:30px;text-align:right;">
	    							<?php echo ($prestamo->libro_folio != '') ? ($prestamo->libro_folio) : '&nbsp;' ; ?>
	    						</td>
	    						<td class="prestamo_celda_listado_general" style="width:200px">
	    							<?php echo ($prestamo->observaciones_prestamo != '') ? $prestamo->observaciones_prestamo : '&nbsp;' ;//$prestamo->ObtenerResumenObservacion() ?>
	    						</td>
	    					</tr>
	    				<?php
	    					$i++;
	    				}
	    				?>
		    		</tbody>
		    	</table>
    		<?php
    		} else
    			echo "<h3>No se han encontrado resultados.</h3>";
    		?>
    	</div>

    	<?php
		// Si hay préstamos se muestra el Paginador
		if ( $cantidad > 0 && $parametros_vista['nro_paginas'] > 1 ) {
			echo $this->mostrarPaginadorPrestamos($parametros_vista);
		}
		?>

    	<script>
    		// Se resalta el ítem PRÉSTAMOS del menú principal
    		$('item_prestamos_menu_gral').setStyle('background-color','#263D7C');

			$('f_tipo').value = '<?php echo ($parametros_vista['tipo'] != '0') ? $parametros_vista['tipo'] : 0; ?>';

			$('solicitante').value = '<?php echo ($parametros_vista['solicitante'] != '0') ? $parametros_vista['solicitante'] : 0; ?>';

			// CALENDARIO PARA LA FECHA DESDE DEL CRITERIO DE BUSQUEDA
			var calendario_fecha_desde = new Zapatec.Calendar.setup(
			{
				inputField: "f_fecha_desde",
				ifFormat: "%d/%m/%Y",
				button: "img_f_fecha_desde",
				showsTime: false
			});

			// CALENDARIO PARA LA FECHA HASTA DEL CRITERIO DE BUSQUEDA
			var calendario_fecha_hasta = new Zapatec.Calendar.setup(
			{
				inputField: "f_fecha_hasta",
				ifFormat: "%d/%m/%Y",
				button: "img_f_fecha_hasta",
				showsTime: false
			});

			$('f_estado_solicitado').checked = <?php echo ($parametros_vista['estado_solicitado'] != '') ? 1 : 0; ?>;
			$('f_estado_prestado').checked = <?php echo ($parametros_vista['estado_prestado'] != '') ? 1 : 0; ?>;
			$('f_estado_devuelto').checked = <?php echo ($parametros_vista['estado_devuelto'] != '') ? 1 : 0; ?>;
			$('f_estado_anulado').checked = <?php echo ($parametros_vista['estado_anulado'] != '') ? 1 : 0; ?>;

			function buscarPrestamosPorExpediente() {
				var mensaje = "";
				var error = false;

				if($('f_anio').value == '') {
					mensaje += "No ha ingresado el A"+'\u00f1'+"o del expediente.";
					error = true;
				}

				if($('f_tipo').value == '0') {
					mensaje += "<br> No ha seleccionado el Tipo del expediente.";
					error = true;
				}

				if($('f_numero').value == '') {
					mensaje += "<br> No ha ingresado el N"+'\u00fa'+"mero del expediente.";
					error = true;
				}

				if (error)
				    alert(mensaje);
			    else
			    	refrescar('abms/index.php?controlador='+$('controlador').value+'&accion=listarPrestamosPorExpediente&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&digito='+$('f_digito').value+'&cuerpoalcance='+$('f_cuerpoalcance').value+'&anexoalcance='+$('f_anexoalcance').value+'&cuerpoanexoalcance='+$('f_cuerpoanexoalcance').value+'&anexo='+$('f_anexo').value+'&cuerpoanexo='+$('f_cuerpoanexo').value+'', 'contenidoAjaxPrincipal');
			}

			function buscarConCriterio() {
				var mensaje = "";
				var error = false;

				if( $('f_estado_solicitado').checked === false &&
					$('f_estado_prestado').checked === false &&
					$('f_estado_devuelto').checked === false &&
					$('f_estado_anulado').checked === false &&
					$('solicitante').value == '0' &&
					$('f_fecha_desde').value == '' &&
					$('f_fecha_hasta').value == '' )
				{
					mensaje += "Debe utilizar por lo menos un criterio de b"+'\u00fa'+"squeda.";
					error = true;
				}

				if (error)
				    alert(mensaje);
			    else {
					valor_estado_solicitado = ($('f_estado_solicitado').checked) ? $('f_estado_solicitado').value : '';
					valor_estado_prestado = ($('f_estado_prestado').checked) ? $('f_estado_prestado').value : '';
					valor_estado_devuelto = ($('f_estado_devuelto').checked) ? $('f_estado_devuelto').value : '';
					valor_estado_anulado = ($('f_estado_anulado').checked) ? $('f_estado_anulado').value : '';

			    	refrescar('abms/index.php?controlador='+$('controlador').value+'&accion=listarPrestamosPorCriterioBusqueda&estado_solicitado='+valor_estado_solicitado+'&estado_prestado='+valor_estado_prestado+'&estado_devuelto='+valor_estado_devuelto+'&estado_anulado='+valor_estado_anulado+'&solicitante='+$('solicitante').value+'&fecha_desde='+$('f_fecha_desde').value+'&fecha_hasta='+$('f_fecha_hasta').value+'', 'contenidoAjaxPrincipal');
			    }
			}

			function paginarPrestamo(pagina, sentido) {
				// Si se buscó por un expediente particular
				if( $('f_anio').value != '' )
					refrescar('abms/index.php?controlador='+$('controlador').value+'&accion=listarPrestamosPorExpediente&pagina='+pagina+'&sentido='+sentido+'&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&digito='+$('f_digito').value+'&cuerpoalcance='+$('f_cuerpoalcance').value+'&anexoalcance='+$('f_anexoalcance').value+'&cuerpoanexoalcance='+$('f_cuerpoanexoalcance').value+'&anexo='+$('f_anexo').value+'&cuerpoanexo='+$('f_cuerpoanexo').value, 'contenidoAjaxPrincipal');
				// Si se buscó por algún criterio de búsqueda
				else if( $('f_estado_solicitado').checked == 1 ||
						 $('f_estado_prestado').checked == 1 ||
						 $('f_estado_devuelto').checked == 1 ||
						 $('f_estado_anulado').checked == 1 ||
						 $('solicitante').value != '0' ||
						 $('f_fecha_hasta').value != '' )
				{
					valor_estado_solicitado = ($('f_estado_solicitado').checked) ? $('f_estado_solicitado').value : '';
					valor_estado_prestado   = ($('f_estado_prestado').checked) ? $('f_estado_prestado').value : '';
					valor_estado_devuelto   = ($('f_estado_devuelto').checked) ? $('f_estado_devuelto').value : '';
					valor_estado_anulado    = ($('f_estado_anulado').checked) ? $('f_estado_anulado').value : '';

					refrescar('abms/index.php?controlador='+$('controlador').value+'&accion=listarPrestamosPorCriterioBusqueda&pagina='+pagina+'&sentido='+sentido+'&estado_solicitado='+valor_estado_solicitado+'&estado_prestado='+valor_estado_prestado+'&estado_devuelto='+valor_estado_devuelto+'&estado_anulado='+valor_estado_anulado+'&solicitante='+$('solicitante').value+'&fecha_desde='+$('f_fecha_desde').value+'&fecha_hasta='+$('f_fecha_hasta').value, 'contenidoAjaxPrincipal');
				}
				else
					// Sino se lista el siguiente grupo de préstamos según el sentido elegido
					refrescar('abms/index.php?controlador='+$('controlador').value+'&accion=mostrarGrillaGeneral&pagina='+pagina+'&sentido='+sentido, 'contenidoAjaxPrincipal');
			}

			function limpiarCriterioBusquedaPrestamos() {
				refrescar('abms/index.php?controlador='+$('controlador').value+'&accion=mostrarGrillaGeneral', 'contenidoAjaxPrincipal');
			}

			// Al editar el número del expediente
			$('f_numero').addEvent('keydown', function(event) {
				// Si se presiona la tecla Enter y se ha ingresado un número
				if(event.key == 'Enter' && $('f_numero').value != '' )
					// Se buscan los préstamos para dicho expediente
					buscarPrestamosPorExpediente();
			});

    	    // Si hay registros
		    if ( $('cantidad').value > 0 )
		    {
	    		$('btGenerarReporte').addEvent('click', function() {
	    			// Si se buscó por un expediente particular
	    			if( $('f_anio').value != '' )
	    				$('btGenerarReporte').setProperty('href', 'abms/index.php?controlador='+$('controlador').value+'&accion=generarReporte&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&digito='+$('f_digito').value+'&cuerpoalcance='+$('f_cuerpoalcance').value+'&anexoalcance='+$('f_anexoalcance').value+'&cuerpoanexoalcance='+$('f_cuerpoanexoalcance').value+'&anexo='+$('f_anexo').value+'&cuerpoanexo='+$('f_cuerpoanexo').value);
	    			// Si se buscó por algún criterio de búsqueda
					else if( $('f_estado_solicitado').checked == 1 ||
							 $('f_estado_prestado').checked == 1 ||
							 $('f_estado_devuelto').checked == 1 ||
							 $('f_estado_anulado').checked == 1 ||
							 $('solicitante').value != '0' ||
							 $('f_fecha_hasta').value != '' )
	    			{
	    				valor_estado_solicitado = ($('f_estado_solicitado').checked) ? $('f_estado_solicitado').value : '';
						valor_estado_prestado = ($('f_estado_prestado').checked) ? $('f_estado_prestado').value : '';
						valor_estado_devuelto = ($('f_estado_devuelto').checked) ? $('f_estado_devuelto').value : '';
						valor_estado_anulado = ($('f_estado_anulado').checked) ? $('f_estado_anulado').value : '';

	    				$('btGenerarReporte').setProperty('href', 'abms/index.php?controlador='+$('controlador').value+'&accion=generarReporte&estado_solicitado='+valor_estado_solicitado+'&estado_prestado='+valor_estado_prestado+'&estado_devuelto='+valor_estado_devuelto+'&estado_anulado='+valor_estado_anulado+'&solicitante='+$('solicitante').value+'&fecha_desde='+$('f_fecha_desde').value+'&fecha_hasta='+$('f_fecha_hasta').value);
	    			} else
	    				// Sino se lista el siguiente grupo de préstamos según el sentido elegido
	    				$('btGenerarReporte').setProperty('href', 'abms/index.php?controlador='+$('controlador').value+'&accion=generarReporte');
	    		});
    		}
		</script>
    <?php
    }

    /**
     * Se listan los préstamos de un expediente determinado, perteneciente al HCD
     * @param Array $listado_prestamos, colección de instancias de Prestamo
     * @param ng_prestamos $ng_prestamos
     * @param string $mensaje
     * @param string $tipo_mensaje
     * @param Array $parametros_vista
     */
	public function listarEnSolapa($listado_prestamos = '',  ng_prestamos $ng_prestamos, $mensaje = '', $tipo_mensaje = '', $parametros_vista = '')
    {
    	// MENSAJE DEL RESULTADO DE LA OPERACION REALIZADA
    	$this->mostrarCartelResultado($mensaje, $tipo_mensaje);

    	// Cantidad de Préstamos
    	$cantidad = count($listado_prestamos);

    	// Sólo Perfiles 1 y 2 pueden Editar
    	$puede_editar = ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2) ? true : false;
    	// Sólo Perfil 1 puede Eliminar
    	$puede_eliminar = ($_SESSION['perfil2'] == 1) ? true : false;

    	// Se setea el ancho de la columna de Solicitante según el perfil actual
    	if ( $_SESSION['perfil2'] == 1 )
    		$ancho_col_solicitante = '224px';
    	elseif ( $_SESSION['perfil2'] == 2 )
    		$ancho_col_solicitante = '254px';
    	else
    		$ancho_col_solicitante = '284px';
    	?>
    	    <script>
			    $('header').setStyle('display','block');
			    $('p_menu_ocultado').setStyle('display','none');
			    $("capaFondo").setStyle('visibility','hidden');
				$("capaVentana").setStyle('visibility','hidden');
			</script>

    	    <div class="p_borde_superior"></div>

    	    <!-- BARRA DE NAVEGACION SUPERIOR -->
    	    <div id="p_barra_navegacion" class="p_barra_navegacion">
    	    	<div class="p_bnav_contenedor_3bt p_bnav_sin_borde_izquierdo">
    			    <a id="btBG" href="javascript:refrescar('consultas/index.php?controlador=consulta_gral&accion=listar_principal', 'capaVentana');" title="Consulta Parametrizada">
    				    <img id="imgBG" src="imagenes/barra/buscar_16x16.gif" width="16" height="16" />
    			    </a>
    			    <a id="btBA" title="Buscar por Antecedente" href="javascript:refrescar('consultas/index.php?controlador=por_antecedente&accion=por_antecedente', 'capaVentana');" title="B&uacute;squeda por Antecedente">
    				    <img id="imgBA" src="imagenes/barra/buscar_ant_16x16.gif" width="16" height="16" />
    			    </a>
    			    <?php
    				// SOLO PERFIL 1 Y 2 PUEDEN VERIFICAR LA DIGITALIZACION DE DOCUMENTOS DEL D.E.
    				if ( $_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2 ) {
    				?>
    					<a href="javascript:refrescar('tareas/index.php?controlador=verificar_digitalizacion&accion=verificar_digitalizacion', 'capaVentana');" title="Verificar Digitalizaci&oacute;n D.E.">
    						<img id="imgBA" src="imagenes/barra/verificar_digitalizacion_16x16.gif" width="16" height="16" />
    					</a>
    				<?php
    				}
    				?>
    		    </div>
    		    <div class="p_bnav_contenedor_3bt">
    				<?php
    				// SOLO USUARIOS DE PERFIL 1 Y 2 PUEDEN INGRESAR REGISTROS
    				if ($_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2) {
    					$prestamoParaAgregar = new Prestamo();
    					$prestamoParaAgregar->anio = $parametros_vista['anio'];
    					$prestamoParaAgregar->tipo = $parametros_vista['tipo'];
    					$prestamoParaAgregar->numero = $parametros_vista['numero'];
    					$prestamoParaAgregar->cuerpo = $parametros_vista['cuerpo'];
    					$prestamoParaAgregar->alcance = $parametros_vista['alcance'];
    					$prestamoParaAgregar->digito = 0;
    					$prestamoParaAgregar->cuerpoalcance = 0;
    					$prestamoParaAgregar->anexoalcance = 0;
    					$prestamoParaAgregar->anexo = 0;
    					$prestamoParaAgregar->cuerpoanexo = 0;

    					// propongo la fecha-hora actual como fecha de solicitud
    					$prestamoParaAgregar->fecha_solicitud = date("Y-m-d H:i:s");

    					$prestamoParaAgregar->estado = Prestamo::E_SOLICITADO;
    					$prestamoParaAgregar->id_usuario = $_SESSION['id_usuario'];
					?>
    					<a id="btAgregar" style="width:30px;" title="Agregar Registro" href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=agregar&grilla_origen=grilla_solapa&data=<?php echo $prestamoParaAgregar->Serializar(); ?>', 'contenidoAjaxPrincipal');">
    						<img id="imgAgregar" src="imagenes/barra/add_16x16.gif" width="16" height="16" />
    					</a>
    				<?php
    				} else {
    				?>
    					<a id="btAgregar" style="width:30px;" title="Agregar Registro" href="#">
    						<img id="imgAgregar" src="imagenes/barra/add_gris_16x16.gif" width="16" height="16" />
    					</a>
    				<?php
    				}
    				?>
    		    </div>
    		    <div class="p_bnav_contenedor_2bt">
    			    <a id="btGuardar" title="Aceptar los cambios realizados" href="#">
    				    <img id="imgGuardar" src="imagenes/barra/ok_gris_16x16.gif" width="16" height="16" />
    			    </a>
    			    <a id="btCancelar" title="Cancelar los cambios realizados" href="#">
    				    <img id="imgCancelar" src="imagenes/barra/error_gris_16x16.gif" width="16" height="16" />
    			    </a>
    		    </div>
    		    <div class="p_bnav_contenedor_2bt p_bnav_sin_borde_derecho">
    			    <a id="btPrint" title="Imprimir el registro seleccionado" href="#">
    				    <img id="imgPrint" src="imagenes/barra/print_16x16.gif" width="16" height="16" />
    			    </a>
    			    <?php
    				// EL perfil de acceso web NO puede imprimir la Etiqueta del expediente
    				if ( $_SESSION['perfil2'] != 4 ) {
    				?>
    					<a id="btPrintEtiq" title="Imprimir la Etiqueta del Expediente" href="#">
    						<img id="imgPrintEtiq" src="imagenes/barra/print_etiq_16x16.gif" width="16" height="16" />
    					</a>
    			    <?php
    				}
    				?>
    		    </div>
    		    <div class="p_bnav_contenedor_btSalir">
    			    <a id="btSalir" title="Volver al listado de Expedientes." href="index.php">
    				    <img src="imagenes/barra/volver.jpeg" width="17" height="17" />
    			    </a>
    		    </div>
    	    </div>

    	    <div class="p_borde_superior"></div>

    	    <!-- BUSCADOR POR EXPEDIENTE -->
    	    <div class="p_buscador">
    			<input type="hidden" id="nro_paginas" value="<?php echo $parametros_vista['nro_paginas']; ?>">
    		    <!--  AQUI SE GUARDA EL NOMBRE DEL CAMPO POR EL CUAL SE ORDENA Y SE BUSCA  -->
    		    <input type="hidden" name="campo_orden" id="campo_orden" value="<?php echo $parametros_vista['campo_orden']; ?>" />

    		    <div style="height:20px;">
    			    <form action="abms/index.php" method="post" name="formBuscador" id="formBuscador" class="p_buscador_form">

    				    <div class="p_buscador_margen_datos"></div>
    				    <div class="p_buscador_dato">
    					    <input type="text" name="f_anio" id="f_anio" value="<?php echo $parametros_vista['anio']; ?>" onKeyPress="return soloEnteros(event);" onKeyUp="javascript:respetar_anio(this);" size="2" maxlength="4" />
    				    </div>
    				    <div class="p_buscador_margen_datos"></div>
    				    <div class="p_buscador_dato">
    					    <select name="f_tipo" id="f_tipo" class="p_buscador_dato_tipo">
    							<option value="E">E</option>
    							<option value="N">N</option>
    							<option value="R">R</option>
    					    </select>
    				    </div>
    				    <div class="p_buscador_margen_datos"></div>
    				    <div class="p_buscador_dato">
    					    <input type="text" name="f_numero" id="f_numero" value="<?php echo $parametros_vista['numero']; ?>" onKeyPress="return soloEnteros(event);" size="3" maxlength="5" />
    				    </div>
    				    <div class="p_buscador_margen_datos"></div>
    				    <div class="p_buscador_dato">
    					    <input type="text" name="f_cuerpo" id="f_cuerpo" value="<?php echo $parametros_vista['cuerpo']; ?>" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" />
    				    </div>
    				    <div class="p_buscador_margen_datos"></div>
    				    <div class="p_buscador_dato">
    					    <input type="text" name="f_alcance" id="f_alcance" value="<?php echo $parametros_vista['alcance']; ?>" onKeyPress="return soloEnteros(event);" size="1" maxlength="4" />
    				    </div>

    				    <div class="p_buscador_margen_datos"></div>

    					<div class="p_boton_edicion">
    						<a title="Buscar" href="javascript:buscarPrestamos();">
    							<img src="imagenes/zoom_16x16.gif" width="16" height="16" align="top" />&nbsp;Buscar
    						</a>
    					</div>

    					<div class="p_buscador_margen_datos"></div>

    					<div class="p_boton_edicion">
    						<a title="Restablecer" href="javascript:location.href='index.php';">
    							<img src="imagenes/limpiar.png" width="16" height="16" align="top" />&nbsp;Restablecer
    						</a>
    					</div>

    				    <div style="width:15px;height:20px;float:left;"></div>

    				    <div class="p_buscador_info p_buscador_texto">
    					    <span id="cant_expedientes" style="padding:2px 0 0 0;">Cantidad Expedientes : <?php echo $_SESSION['totalExpedientes']; ?></span>
    					    <br>
    					    <span id="documento" style="padding:2px 0 0 0;"><strong>Proyecto:<span id="estado_doc" style="font-size:14px;"></span></strong></span>
    				    	<!-- 2020/05/07 XXXX, se agrega el estado de la Digitalización del expediente seleccionado -->
						    &nbsp;&nbsp;&nbsp;
						    <span id="digitalizacion" style="padding:2px 0 0 0;">
						    	<strong>Digitalizaci&oacute;n:<span id="estado_digitalizacion" style="font-size:14px;"></span></strong>
						    </span>
    				    </div>
    				    <div id="modifico_usr" class="p_usuario_afectado p_buscador_texto"></div>

    			    </form>
    		    </div>
    		</div>

    	    <div class="p_borde_superior"></div>

    	    <div class="p_solapas_titulos p_buscador_texto">
    			<div id="p_solapa_link1" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=expedientes&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&sentido=anterior', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link1').setStyle('color','#315299');" onmouseOut="$('p_solapa_link1').setStyle('color','#000');">Expedientes</div>
    		    <div id="p_solapa_link2" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=proyectos&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link2').setStyle('color','#315299');" onmouseOut="$('p_solapa_link2').setStyle('color','#000');">Proyectos</div>
    		    <div id="p_solapa_link3" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=giros&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link3').setStyle('color','#315299');" onmouseOut="$('p_solapa_link3').setStyle('color','#000');">Giros</div>
    		    <div id="p_solapa_link4" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=sanciones&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link4').setStyle('color','#315299');" onmouseOut="$('p_solapa_link4').setStyle('color','#000');">Sanciones</div>
    		    <div id="p_solapa_link5" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=estados&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link5').setStyle('color','#315299');" onmouseOut="$('p_solapa_link5').setStyle('color','#000');">Estados</div>
    		    <div id="p_solapa_link6" class="p_solapa_link" onclick="refrescar('abms/index.php?controlador=antecedentes&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link6').setStyle('color','#315299');" onmouseOut="$('p_solapa_link6').setStyle('color','#000');">Antecedentes</div>
    		    <div id="p_solapa_link7" class="p_solapa_link" style="background-color:silver" onclick="refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarEnSolapa&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');" onmouseOver="$('p_solapa_link7').setStyle('color','#315299');" onmouseOut="$('p_solapa_link7').setStyle('color','#000');">Pr&eacute;stamos</div>
    		    <div id="p_solapa_link8" class="p_solapa_link" style="color:silver;">Ruta</div>
    	    </div>

    	  	<div class="p_borde_superior_sin_linea"></div>

    	    <div id="ub_listado" class="contenedor_listado">

    			<input type="hidden" id="controlador" name="controlador" value="<?php echo $this->controlador; ?>" />
    			<input type="hidden" id="cantidad" value="<?php echo count($datos); ?>" />
    			<input type="hidden" id="pagina" value="<?php echo $parametros_vista['pagina']; ?>">
    			<input type="hidden" id="nroFila_elegida" value="">
    			<?php
    			if ( $cantidad > 0) {
    			?>
    				<div style="width:100%;height:28px;background-color:#3760A7;">
    					<?php
						if ( $puede_eliminar ) // Sólo el perfil 1 puede eliminar
							echo '<div class="orden_link_codificadoras" style="width:54px">&nbsp;</div>';
						elseif ( $puede_editar ) // Sólo los perfiles 1 y 2 pueden modificar
							echo '<div class="orden_link_codificadoras" style="width:27px">&nbsp;</div>';
						?>
						<div class="orden_link_codificadoras" style="width:<?php echo $ancho_col_solicitante; ?>;line-height:28px;">Solicitante</div>
						<div class="orden_link_codificadoras" style="width:90px;text-align:center;">Fecha<br>Solicitud</div>
						<div class="orden_link_codificadoras" style="width:90px;text-align:center;">Fecha<br>Pr&eacute;stamo</div>
						<div class="orden_link_codificadoras" style="width:90px;text-align:center;">Fecha<br>Devoluci&oacute;n</div>
						<div class="orden_link_codificadoras" style="width:90px;text-align:center;">Fecha<br>Anulado</div>
						<div class="orden_link_codificadoras" style="width:115px;text-align:center;line-height:28px;">Estado</div>
						<div class="orden_link_codificadoras" style="width:56px;text-align:center;">Libro<br>N&uacute;mero</div>
						<div class="orden_link_codificadoras" style="width:56px;text-align:center;">Libro<br>Folio</div>
						<div class="orden_link_codificadoras" style="width:310px;text-align:center;line-height:28px;">Observaciones</div>
					</div>
    				<div class="prestamo_listado">
    					<?php
    					$i = 0;
    					foreach ($listado_prestamos as $prestamo) {
    						// SE OBTIENEN LOS ESTADOS SIGUIENTES QUE PUEDE TOMAR EL PRESTAMO
    						$estados_siguientes = $ng_prestamos->ObtenerEstadosSiguientes($prestamo);
    					?>
    						<div style="clear:both;" onmouseover="javascript:this.setStyle('background-color', '#EBEFF9');" onmouseout="javascript:this.setStyle('background-color', '#ffffff');">
    							<?php
	    					    // Si tiene permiso para Editar
								if ($puede_editar) {
								?>
									<div class="prestamo_celda_listado" style="width:22px">
										<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarObservaciones&grilla_origen=grilla_solapa&parametros_serializados=<?php echo serializarColeccion($parametros_vista); ?>&data=<?php echo $prestamo->Serializar(); ?>', 'capaVentana');" title="Editar Pr&eacute;stamo" >
											&nbsp;<img src="imagenes/b_edit.png" width="14" height="14" align="center" />
										</a>
									</div>
								<?php
								}
								// Si tiene permiso para Eliminar
								if ($puede_eliminar) {
									// Si aún NO se prestó o ya se encuentra cerrado (Devuelto o Anulado)
									if ( in_array(Prestamo::E_PRESTADO, $estados_siguientes) || empty($estados_siguientes)) {
								?>
										<div class="prestamo_celda_listado" style="width:22px">
											<a href="javascript:if(confirm('¿Desea eliminar el Préstamo <?php echo $prestamo->anio.'-'.$prestamo->tipo.'-'.$prestamo->numero; ?> definitivamente?')){refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=eliminar&grilla_origen=grilla_solapa&data=<?php echo $prestamo->Serializar(); ?>&parametros_serializados=<?php echo serializarColeccion($parametros_vista); ?>', 'contenidoAjaxPrincipal');};" title="Eliminar Pr&eacute;stamo">
												&nbsp;<img src="imagenes/b_drop.png" width="14" height="14" align="center" />
											</a>
										</div>
								<?php
									} else {
								?>
										<div class="prestamo_celda_listado" style="width:22px" title="No se puede eliminar, no se encuentra cerrado el Pr&eacute;stamo (Devuelto o Anulado).">
	                                        &nbsp;<img src="imagenes/b_drop_gris.png" width="14" height="14" align="center" />
	                                    </div>
								<?php
									}
								}
								?>
    							<div class="prestamo_celda_listado" style="width:<?php echo $ancho_col_solicitante; ?>;">
    								<?php echo ($prestamo->solicitante_nombre != '') ? $prestamo->solicitante_nombre : '&nbsp;' ; ?>
    							</div>

    							<div class="prestamo_celda_listado" style="width:80px;text-align:center;">
    								<?php echo $this->verificarFecha($prestamo, Prestamo::E_SOLICITADO); ?>
    							</div>

    							<div class="prestamo_celda_listado" style="width:80px;text-align:center;">
    								<?php
    								// SI PUEDE EDITAR Y SU SIGUIENTE ESTADO ES 'PRESTADO'
    								if ( $puede_editar && in_array(Prestamo::E_PRESTADO, $estados_siguientes) ) {
    								?>
    									<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=cambiarEstado&grilla_origen=grilla_solapa&parametros_serializados=<?php echo serializarColeccion($parametros_vista); ?>&data=<?php echo $prestamo->Serializar(); ?>&estado_nuevo=<?php echo Prestamo::E_PRESTADO; ?>', 'capaVentana');" style="display: block;padding: 2px 0 2px 0;border-radius: 3px;color: #ffffff;background-color: #00375E;">
    										<img src="imagenes/b_edit.png" width="14" height="14" >&nbsp;<?php echo "Prestar"; ?>
    									</a>
    								<?php
    								} else
    									echo $this->verificarFecha($prestamo, Prestamo::E_PRESTADO);
    								?>
    							</div>

    							<div class="prestamo_celda_listado" style="width:80px;text-align:center;">
    								<?php
    								// SI PUEDE EDITAR Y SU SIGUIENTE ESTADO ES 'DEVUELTO'
    								if ( $puede_editar && in_array(Prestamo::E_DEVUELTO, $estados_siguientes) ) {
    								?>
    									<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=cambiarEstado&grilla_origen=grilla_solapa&parametros_serializados=<?php echo serializarColeccion($parametros_vista); ?>&data=<?php echo $prestamo->Serializar(); ?>&estado_nuevo=<?php echo Prestamo::E_DEVUELTO; ?>', 'capaVentana');" style="display: block;padding: 2px 0 2px 0;border-radius: 3px;color: #ffffff;background-color: #00375E;">
    										<img src="imagenes/b_edit.png" width="14" height="14" >&nbsp;<?php echo "Devolver"; ?>
    									</a>
    								<?php
    								} else
    									echo $this->verificarFecha($prestamo, Prestamo::E_DEVUELTO);
    								?>
    							</div>

    							<div class="prestamo_celda_listado" style="width:80px;text-align:center;">
    								<?php
    								// SI PUEDE EDITAR Y SU SIGUIENTE ESTADO ES 'ANULADO'
    								if ( $puede_editar && in_array(Prestamo::E_ANULADO, $estados_siguientes) ) {
    								?>
    									<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=cambiarEstado&grilla_origen=grilla_solapa&parametros_serializados=<?php echo serializarColeccion($parametros_vista); ?>&data=<?php echo $prestamo->Serializar(); ?>&estado_nuevo=<?php echo Prestamo::E_ANULADO; ?>', 'capaVentana');" style="display: block;padding: 2px 0 2px 0;border-radius: 3px;color: #ffffff;background-color: #00375E;">
    										<img src="imagenes/b_edit.png" width="14" height="14" >&nbsp;<?php echo "Anular"; ?>
    									</a>
    								<?php
    								} else
    									echo $this->verificarFecha($prestamo, Prestamo::E_ANULADO);
    								?>
    							</div>

    							<div class="prestamo_celda_listado" style="width:115px;<?php echo $this->prestamoEstadoToColor($prestamo); ?>">
    								<?php echo ($prestamo->estado != '') ? $prestamo->EstadoToString() : '&nbsp;' ; ?>
    							</div>
    							<div class="prestamo_celda_listado" style="width:50px;">
    								<?php echo ($prestamo->libro_numero != '') ? ($prestamo->libro_numero) : '&nbsp;' ; ?>
    							</div>
    							<div class="prestamo_celda_listado" style="width:50px;">
    								<?php echo ($prestamo->libro_folio != '') ? ($prestamo->libro_folio) : '&nbsp;' ; ?>
    							</div>
    							<div class="prestamo_celda_listado" style="width:442px;">
    								<?php echo ($prestamo->observaciones_prestamo != '') ? $prestamo->observaciones_prestamo : '&nbsp;' ; ?>
    							</div>
    						</div>
    					<?php
    						$i++;
    					}
    					?>
	    			</div>
    			<?php
    			}
    			?>
    	    </div>
    		<div class="p_borde_superior"></div>
    	    <div id="capa_datos_inferior">
    		    <!-- AQUI SE VISUALIZAN LOS DATOS DEL EXPEDIENTE SELECCIONADO -->
    	    </div>
    		<script>
    		    Window.onDomReady(MOOdalBox.init.bind(MOOdalBox));

    		    $('f_tipo').value = '<?php echo ($parametros_vista['tipo']) ? $parametros_vista['tipo'] : 'E'; ?>';

    		    //SE CARGAN (VISUALIZAN) LOS DATOS EN EL BUSCADOR SUPERIOR
    		    cargarBuscador($('f_anio').value, $('f_tipo').value, $('f_numero').value, $('f_cuerpo').value, $('f_alcance').value);

    		    //SE VISUALIZAN LOS DATOS(Iniciador, Categoria, Autores, Temas) DEL EXPEDIENTE SELECCIONADO
    		    pedirDatos($('f_anio').value, $('f_tipo').value, $('f_numero').value, $('f_cuerpo').value, $('f_alcance').value);

    		    // SE SETEA EL HREF DE 'btPrint' PARA GENERAR LA FICHA EN PDF
    		    $('btPrint').addEvent('click', function()
    		    {
    				$('btPrint').setProperty('href','consultas/index.php?controlador=ficha&accion=generar_ficha&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'');
    				$('btPrint').setProperty('target','_blank');
    		    });

    		    // SE SETEA EL HREF DE 'btPrintEtiq' PARA GENERAR LA ETIQUETA EN PDF
    		    $('btPrintEtiq').addEvent('click', function()
    		    {
    				$('btPrintEtiq').setProperty('href','consultas/index.php?controlador=ficha&accion=generarEtiqueta_ficha&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'');
    				$('btPrintEtiq').setProperty('target','_blank');
    		    });

    			function buscarPrestamos()
    			{
					refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'', 'contenidoAjaxPrincipal');
    			}

    			$('f_anio').addEvents({
    				click: function(){
    					se_busca = true;
    				},
    				keydown: function(event){
    					if(event.key == 'Enter')
    						buscarPrestamos();
    				}
    			});

    			$('f_numero').addEvents({
    				click: function(){
    					se_busca = true;
    				},
    				keydown: function(event){
    					if(event.key == 'Enter')
    						buscarPrestamos();
    				}
    			});

    			$('f_cuerpo').addEvents({
    				click: function(){
    					se_busca = true;
    				},
    				keydown: function(event){
    					if(event.key == 'Enter')
    						buscarPrestamos();
    				}
    			});

    			$('f_alcance').addEvents({
    				click: function(){
    					se_busca = true;
    				},
    				keydown: function(event){
    					if(event.key == 'Enter')
    						buscarPrestamos();
    				}
				});
		</script>
    <?php
    }

    /**
     * Devuelve el color de fondo y texto según el estado actual del Préstamo
     * @param Prestamo $prestamo
     * @return string $color_fondo_y_texto
     */
	public function prestamoEstadoToColor(Prestamo $prestamo)
	{
		$color_fondo_y_texto = "";

		switch ($prestamo->estado)
		{
			case Prestamo::E_SOLICITADO:
				$color_fondo_y_texto = "background-color: #FCF8E3;color: #C09853;";// AMARILLO PASTEL
				break;
			case Prestamo::E_PRESTADO:
				$color_fondo_y_texto = "background-color: #F2DEDE;color: #B94A48;";// ROJO PASTEL
				break;
			case Prestamo::E_DEVUELTO:
				$color_fondo_y_texto = "background-color: #DFF0D8;color: #468847;";// VERDE PASTEL
				break;
			case Prestamo::E_ANULADO:
				$color_fondo_y_texto = "background-color: #D9D9D9;color: #3A3A3A;";// GRIS PASTEL
				break;
		}

		return $color_fondo_y_texto;
	}

	/**
	 * Verifica la fecha para un estado determinado de un Préstamo
	 * @param Prestamo $prestamo
	 * @param string $estado
	 * @return string Devuelve la fecha en formato dd/mm/aaaa, ó la expresión "---" en caso de ser nula.
	 */
    public function verificarFecha(Prestamo $prestamo, $estado)
    {
    	if ($prestamo->ObtenerFechaSegunEstado($estado) === null)
    		return "---";
    	else
    		return $this->formatearFecha(substr($prestamo->ObtenerFechaSegunEstado($estado), 0, 10));
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
    		case Prestamo::E_PRESTADO:
    			$nombre = "Prestado";
    			break;
    		case Prestamo::E_DEVUELTO:
    			$nombre = "Devuelto";
    			break;
    		case Prestamo::E_ANULADO:
    			$nombre = "Anulado";
    			break;
    		default:
    			$nombre = "Solicitado";
    			break;
    	}

    	return $nombre;
    }

    /**
     * Formulario para editar la fecha y datos respectivos para cambiar el estado determinado de un Préstamo
     * @param Prestamo $prestamo
     * @param array $parametros_vista
     */
    public function editarCambioEstado($prestamo, $parametros_vista)
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

    		<form action="abms/index.php" method="POST" name="formCambiarEstado" id="formCambiarEstado">

		    	<input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />

		    	<input type="hidden" name="accion" id="accion" value="guardarCambioEstado" />

		    	<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />

		    	<input type="hidden" name="grilla_origen" id="grilla_origen" value="<?php echo $parametros_vista['grilla_origen']; ?>" />

		    	<input type="hidden" name="parametros_serializados" id="parametros_serializados" value="<?php echo $parametros_vista['parametros_serializados']; ?>" />

				<!-- CLAVE DEL PRESTAMO -->
				<input type="hidden" name="anio" id="anio" value="<?php echo $prestamo->anio; ?>" />
				<input type="hidden" name="tipo" id="tipo" value="<?php echo $prestamo->tipo; ?>" />
				<input type="hidden" name="numero" id="numero" value="<?php echo $prestamo->numero; ?>" />
				<input type="hidden" name="cuerpo" id="cuerpo" value="<?php echo $prestamo->cuerpo; ?>" />
				<input type="hidden" name="alcance" id="alcance" value="<?php echo $prestamo->alcance; ?>" />
				<input type="hidden" name="digito" id="digito" value="<?php echo $prestamo->digito; ?>" />
				<input type="hidden" name="cuerpoalcance" id="cuerpoalcance" value="<?php echo $prestamo->cuerpoalcance; ?>" />
				<input type="hidden" name="anexoalcance" id="anexoalcance" value="<?php echo $prestamo->anexoalcance; ?>" />
				<input type="hidden" name="cuerpoanexoalcance" id="cuerpoanexoalcance" value="<?php echo $prestamo->cuerpoanexoalcance; ?>" />
				<input type="hidden" name="anexo" id="anexo" value="<?php echo $prestamo->anexo; ?>" />
				<input type="hidden" name="cuerpoanexo" id="cuerpoanexo" value="<?php echo $prestamo->cuerpoanexo; ?>" />
				<input type="hidden" name="fecha_solicitud" id="fecha_solicitud" value="<?php echo $prestamo->fecha_solicitud; ?>" />

				<!-- ESTADO A ASIGNARLE AL PRESTAMO -->
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

				<div id="prestamo_cambio_estado_seccion_libro">
					<fieldset>
						<legend>Libro</legend>
						&nbsp;&nbsp;&nbsp;&nbsp;
						N&uacute;mero:&nbsp;
						<input type="text" name="libro_numero" id="libro_numero" value="" size="5" onKeyPress="return soloEnteros(event);" >
						&nbsp;&nbsp;&nbsp;
						Folio:&nbsp;
						<input type="text" name="libro_folio" id="libro_folio" value="" size="5" onKeyPress="return soloEnteros(event);" >
					</fieldset>
				</div>

				<div class="prestamo_cambio_estado_seccion_observaciones">
					Observaciones:<br>
					<textarea name="observaciones_prestamo" id="observaciones_prestamo"><?php echo $prestamo->observaciones_prestamo; ?></textarea>
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
			function registrarCambioEstado() {
				var mensaje = "";
				var error = false;

				if ( $('solo_fecha_estado').value == '' ) {
					error = true;
					mensaje += "Debe ingresar una Fecha.";
				}

				if ( $('solo_hora_estado').value == '' ) {
					error = true;
					mensaje += "\n Debe ingresar un Horario.";
				}

				// Si el estado a asignar es Prestado
				if( $('estado_nuevo').value == '<?php echo Prestamo::E_PRESTADO; ?>' ) {
					// Si no se han ingresado el número y el folio del libro del préstamo
					if ( $('libro_numero').value == '' && $('libro_folio').value == '' ) {
						error = true;
						mensaje += "\n Debe ingresar el N"+'\u00fa'+"mero y el Folio del Libro.";
					}
					// Si no se han ingresado valores válidos para el número o el folio del libro del préstamo
					if ( $('libro_numero').value < '1' || $('libro_folio').value < '1' ) {
						error = true;
						mensaje += "\n Debe ingresar un valor correcto para el N"+'\u00fa'+"mero o el Folio del Libro.";
					}
				}

				if ( error )
					alert(mensaje);
				else {
					// Se unen la fecha y horario del cambio de estado para su guardado en la DB
					$('fecha_estado').value = formatearConGuion($('solo_fecha_estado').value)+" "+$('solo_hora_estado').value;

					// Se envían los datos al método respectivo del controlador
					enviarForm('formCambiarEstado', 'abms', 'contenidoAjaxPrincipal');
				}
			}

			var menuDrag = new Drag.Move($('contenedoraCambioEstado'), {
				handle: $('dragger_cambio_estado')
			});

			// CALENDARIO PARA LA FECHA DEL ESTADO DEL PRESTAMO
			var calendario = new Zapatec.Calendar.setup({
				inputField: "solo_fecha_estado",
				ifFormat: "%d/%m/%Y",
				button: "img_solo_fecha_estado",
				showsTime: false
			});

			// Si el estado a asignar es Prestado
			if( $('estado_nuevo').value == '<?php echo Prestamo::E_PRESTADO; ?>' ) {
				// Se muestra la sección para editar el número y folio del Libro
				$('prestamo_cambio_estado_seccion_libro').setStyle('display', 'block');

				setfocus('libro_numero');
			} else
				// Se oculta la sección de los datos del Libro
				$('prestamo_cambio_estado_seccion_libro').setStyle('display', 'none');
		</script>
    <?php
    }

    /**
     * Formulario para editar la observación de un Préstamo
     * @param Prestamo $prestamo
     * @param array $parametros_vista
     */
    public function editarObservaciones($prestamo, $parametros_vista)
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

    		   	<input type="hidden" name="grilla_origen" id="grilla_origen" value="<?php echo $parametros_vista['grilla_origen']; ?>" />

    		   	<input type="hidden" name="parametros_serializados" id="parametros_serializados" value="<?php echo $parametros_vista['parametros_serializados']; ?>" />

    		    <!-- Estado actual del Préstamo -->
    		    <input type="hidden" name="estado" id="estado" value="<?php echo $prestamo->estado; ?>" />

    			<!-- Clave del Préstamo -->
    			<input type="hidden" name="anio" id="anio" value="<?php echo $prestamo->anio; ?>" />
    			<input type="hidden" name="tipo" id="tipo" value="<?php echo $prestamo->tipo; ?>" />
    			<input type="hidden" name="numero" id="numero" value="<?php echo $prestamo->numero; ?>" />
    			<input type="hidden" name="cuerpo" id="cuerpo" value="<?php echo $prestamo->cuerpo; ?>" />
    			<input type="hidden" name="alcance" id="alcance" value="<?php echo $prestamo->alcance; ?>" />
    			<input type="hidden" name="digito" id="digito" value="<?php echo $prestamo->digito; ?>" />
    			<input type="hidden" name="cuerpoalcance" id="cuerpoalcance" value="<?php echo $prestamo->cuerpoalcance; ?>" />
    			<input type="hidden" name="anexoalcance" id="anexoalcance" value="<?php echo $prestamo->anexoalcance; ?>" />
    			<input type="hidden" name="cuerpoanexoalcance" id="cuerpoanexoalcance" value="<?php echo $prestamo->cuerpoanexoalcance; ?>" />
    			<input type="hidden" name="anexo" id="anexo" value="<?php echo $prestamo->anexo; ?>" />
    			<input type="hidden" name="cuerpoanexo" id="cuerpoanexo" value="<?php echo $prestamo->cuerpoanexo; ?>" />
    			<input type="hidden" name="fecha_solicitud" id="fecha_solicitud" value="<?php echo $prestamo->fecha_solicitud; ?>" />

    			<div id="dragger_edicion_observaciones" class="degradado">
    				Pr&eacute;stamo <?php echo $prestamo->anio.'-'.$prestamo->tipo.'-'.$prestamo->numero; ?>
    			</div>
    			<?php
    			// Sólo el administrador puede editar el número y folio del libro
    			if ($_SESSION['perfil2'] == 1)
    			{
    			?>
    				<div id="prestamo_cambio_estado_seccion_libro">
    					<fieldset>
    						<legend>Libro</legend>
    						&nbsp;&nbsp;&nbsp;&nbsp;
    						N&uacute;mero:&nbsp;
    						<input type="text" name="libro_numero" id="libro_numero" value="<?php echo $prestamo->libro_numero; ?>" size="5" onKeyPress="return soloEnteros(event);" >
    						&nbsp;&nbsp;&nbsp;
    						Folio:&nbsp;
    						<input type="text" name="libro_folio" id="libro_folio" value="<?php echo $prestamo->libro_folio; ?>" size="5" onKeyPress="return soloEnteros(event);" >
    					</fieldset>
    				</div>
    			<?php
    			}
    			?>
    			<div class="prestamo_cambio_estado_seccion_observaciones">
    				Observaciones:<br>
    				<textarea name="observaciones_prestamo" id="observaciones_prestamo"><?php echo $prestamo->observaciones_prestamo; ?></textarea>
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
    			var mensaje = "";
    			var error = false;

    			// Sólo el Administrador puede editar el número y folio del libro
    			if ( $('perfil').value == '1' )
    			{
    				// Si el estado actual es Prestado o Devuelto
    	    		if( $('estado').value == '<?php echo Prestamo::E_PRESTADO; ?>' ||
    	    			$('estado').value == '<?php echo Prestamo::E_DEVUELTO; ?>' )
    	    		{
	    				// Si no se han ingresado el número y el folio del libro del préstamo
	    				if ( $('libro_numero').value == '' && $('libro_folio').value == '' )
	    				{
	    					error = true;
	    					mensaje += "\n Debe ingresar el N"+'\u00fa'+"mero y el Folio del Libro.";
	    				}
	    				// Si no se han ingresado valores válidos para el número o el folio del libro del préstamo
	    				if ( $('libro_numero').value < '1' || $('libro_folio').value < '1' )
	    				{
	    					error = true;
	    					mensaje += "\n Debe ingresar un valor correcto para el N"+'\u00fa'+"mero o el Folio del Libro.";
	    				}
    	    		}
    			}

    			if ( error )
    			{
    				alert(mensaje);
    			}
    			else
    			{
    				// Se envían los datos al método respectivo del controlador
    				enviarForm('formEdicionObservaciones', 'abms', 'contenidoAjaxPrincipal');
    			}
    		}

    		// Si el estado actual es Prestado, Devuelto o Anulado
    		if( $('estado').value == '<?php echo Prestamo::E_PRESTADO; ?>' ||
    			$('estado').value == '<?php echo Prestamo::E_DEVUELTO; ?>' )
    		{
    			// Se muestra la sección para editar el número y folio del Libro
    			$('prestamo_cambio_estado_seccion_libro').setStyle('display', 'block');
    		}
    		else
    		{
    			// Se oculta la sección de los datos del Libro
    			$('prestamo_cambio_estado_seccion_libro').setStyle('display', 'none');
    		}

    		var menuDrag = new Drag.Move($('contenedoraEdicionObservaciones'), {
    		   handle: $('dragger_edicion_observaciones')
    		});
    	</script>
    <?php
    }

    /**
     * Se edita un préstamo, habiendo llegado desde
     * la grilla general o de la solapa de Préstamos de un expediente determinado
     *
     * @param Prestamo $prestamo
     * @param Array $parametros_vista
     * @param Array $solicitantes
     * @param string $mensaje
     * @param string $tipo_mensaje
     */
    public function editar($prestamo, $parametros_vista, $solicitantes = null, $mensaje = '', $tipo_mensaje = '')
    {
    	// MENSAJE DEL RESULTADO DE LA OPERACION REALIZADA
    	$this->mostrarCartelResultado($mensaje, $tipo_mensaje);

    	$css_input_solo_lectura = ($prestamo) ? ' readonly style="color:#A09FA4;"' : '';
    ?>
       	<div class="p_borde_superior"></div>

        <!-- BARRA DE NAVEGACION SUPERIOR -->
        <div class="p_barra_navegacion">

        	<!-- TITULO DE LA GRILLA -->
    		<div class="prestamo_titulo_grilla">EDICI&Oacute;N DE PR&Eacute;STAMO</div>

    		<div class="p_buscador_margen_datos" style="float:right"></div>
    		<div class="p_boton_edicion" style="float:right">
    			<?php
    			// Por defecto se vuelve a la grilla general
    			$accion = "mostrarGrillaGeneral";

    			if ( $parametros_vista['grilla_origen'] == 'grilla_solapa' )
    				// Se vuelve al listado de préstamos del expediente
    				$accion = "listarEnSolapa&anio=".$prestamo->anio."&tipo=".$prestamo->tipo."&numero=".$prestamo->numero."&cuerpo=".$prestamo->cuerpo."&alcance=".$prestamo->alcance."";
    			?>
				<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=<?php echo $accion; ?>&pagina=<?php echo $parametros_vista['pagina']; ?>', 'contenidoAjaxPrincipal');" title="Cancelar los cambios realizados">
					<img src="imagenes/barra/delete_16x16.gif" width="15" height="15" />&nbsp;Cancelar
				</a>
			</div>
			<?php
        	//SOLO USUARIOS DE PERFIL 1 Y 2 PUEDEN EDITAR EL REGISTRO
        	if ( $_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2 ) {
        	?>
		    	<div class="p_buscador_margen_datos" style="float:right"></div>
		    	<div class="p_boton_edicion" style="float:right">
		    		<a href="javascript:validarPrestamo('<?php echo $this->formulario; ?>');" title="Guardar Pr&eacute;stamo">
	    				<img src="imagenes/barra/ok_16x16.gif" width="16" height="16" align="top" />&nbsp;Guardar
	    			</a>
	    		</div>
	    	<?php
        	}
        	?>
        </div>

    	<div class="p_borde_superior"></div>

    	<div class="e_formulario_edicion">

    		<div id="fade" class="overlay"></div>
    		<div id="light" class="modal"></div>

    		<form action="abms/index.php" method="post" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">

    			<input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />

    			<input type="hidden" name="accion" id="accion" value="guardar" />

    			<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />

    			<input type="hidden" name="pagina" id="pagina" value="<?php echo $parametros_vista['pagina']; ?>" />

    			<input type="hidden" name="grilla_origen" id="grilla_origen" value="<?php echo $parametros_vista['grilla_origen']; ?>" />

    			<!-- DATOS RESTANTES DEL PRESTAMO -->
    			<input type="hidden" name="solicitante_tipo" id="solicitante_tipo" value="<?php echo $prestamo->solicitante_tipo; ?>" />
    			<input type="hidden" name="solicitante_codigo" id="solicitante_codigo" value="<?php echo $prestamo->solicitante_codigo; ?>" />

    			<input type="hidden" name="estado" id="estado" value="<?php echo $parametros_vista['estado_a_editar']; ?>" />

    		<!-- CLAVE DE LA SOLICITUD -->
    			<div class="p_buscador" style="margin-bottom:5px;">
		    		<div class="p_buscador_margen_datos"></div>
		    	    <div class="p_buscador_dato p_buscador_texto">
		    		    A&ntilde;o:&nbsp;<input type="text" name="anio" id="anio" value="<?php echo ($prestamo->anio) ? $prestamo->anio : date("Y"); ?>" <?php echo $css_input_solo_lectura; ?> onKeyPress="return soloEnteros(event);" onKeyUp="javascript:respetar_anio(this);" size="2" maxlength="4" />
		    	    </div>
		    	    <div class="p_buscador_margen_datos"></div>
		    	    <div class="p_buscador_dato p_buscador_texto">
		    		    Tipo:&nbsp;
		    		    <select name="tipo" id="tipo" class="p_buscador_dato_tipo" <?php echo $css_input_solo_lectura; ?>>
		    				<option value="0">Todos</option>
		    				<option value="E">E</option>
		    				<option value="N">N</option>
		    				<option value="R">R</option>
		    				<option value="D">D</option>
		    				<option value="O">O</option>
		    		    </select>
		    		</div>
		    	    <div class="p_buscador_margen_datos"></div>
		    	    <div class="p_buscador_dato p_buscador_texto">
		    		    N&uacute;mero:&nbsp;<input type="text" name="numero" id="numero" value="<?php echo ($prestamo->numero != '') ? $prestamo->numero : ''; ?>" <?php echo $css_input_solo_lectura; ?> onKeyPress="return soloEnteros(event);" size="3" maxlength="5" />
		    	    </div>
		    		<div class="p_buscador_margen_datos"></div>
		    		<div class="p_buscador_dato p_buscador_texto">
		    		    Cuerpo:&nbsp;<input type="text" name="cuerpo" id="cuerpo" value="<?php echo ($prestamo->cuerpo != '') ? $prestamo->cuerpo : 0; ?>" <?php echo $css_input_solo_lectura; ?> onKeyPress="return soloEnteros(event);" size="1" maxlength="4" />
		    		</div>
		    	    <div class="p_buscador_margen_datos"></div>
		    	   <div class="p_buscador_dato p_buscador_texto">
		    		    Alcance:&nbsp;<input type="text" name="alcance" id="alcance" value="<?php echo ($prestamo->alcance != '') ? $prestamo->alcance : 0; ?>" <?php echo $css_input_solo_lectura; ?> onKeyPress="return soloEnteros(event);" size="1" maxlength="4" />
		    	    </div>
		    	    <div class="p_buscador_margen_datos"></div>
		    	    <div class="p_buscador_dato p_buscador_texto">
		    		    D&iacute;gito:&nbsp;<input type="text" name="digito" id="digito" value="<?php echo ($prestamo->digito != '') ? $prestamo->digito : '0'; ?>" <?php echo $css_input_solo_lectura; ?> onKeyPress="return soloEnteros(event);" size="1" maxlength="4" />
		    	    </div>
		    	    <div class="p_buscador_margen_datos"></div>
		    	    <div class="p_buscador_dato p_buscador_texto">
		    		    Cpo Alc.:&nbsp;<input type="text" name="cuerpoalcance" id="cuerpoalcance" value="<?php echo ($prestamo->cuerpoalcance != '') ? $prestamo->cuerpoalcance : 0; ?>" <?php echo $css_input_solo_lectura; ?> onKeyPress="return soloEnteros(event);" size="1" maxlength="4" />
		    	    </div>
		    	    <div class="p_buscador_margen_datos"></div>
		    	    <div class="p_buscador_dato p_buscador_texto">
		    		    An. Alc.:&nbsp;<input type="text" name="anexoalcance" id="anexoalcance" value="<?php echo ($prestamo->anexoalcance != '') ? $prestamo->anexoalcance : 0; ?>" <?php echo $css_input_solo_lectura; ?> onKeyPress="return soloEnteros(event);" size="1" maxlength="4" />
		    	    </div>
		    	    <div class="p_buscador_margen_datos"></div>
		    	    <div class="p_buscador_dato p_buscador_texto">
		    		    Cpo. An. Alc.:&nbsp;<input type="text" name="cuerpoanexoalcance" id="cuerpoanexoalcance" value="<?php echo ($prestamo->cuerpoanexoalcance != '') ? $prestamo->cuerpoanexoalcance : 0; ?>" <?php echo $css_input_solo_lectura; ?> onKeyPress="return soloEnteros(event);" size="1" maxlength="4" />
		    	    </div>
		    	    <div class="p_buscador_margen_datos"></div>
		    	    <div class="p_buscador_dato p_buscador_texto">
		    		    An.:&nbsp;<input type="text" name="anexo" id="anexo" value="<?php echo ($prestamo->anexo != '') ? $prestamo->anexo : 0; ?>" <?php echo $css_input_solo_lectura; ?> onKeyPress="return soloEnteros(event);" size="1" maxlength="4" />
		    		</div>
		    		<div class="p_buscador_margen_datos"></div>
		    	    <div class="p_buscador_dato p_buscador_texto">
		    		    Cpo. An.:&nbsp;<input type="text" name="cuerpoanexo" id="cuerpoanexo" value="<?php echo ($prestamo->cuerpoanexo != '') ? $prestamo->cuerpoanexo : 0; ?>" <?php echo $css_input_solo_lectura; ?> onKeyPress="return soloEnteros(event);" size="1" maxlength="4" />
		    	    </div>
		    	</div>

    			<div class="p_borde_superior"></div>

    			<div class="prestamo_edicion prestamo_edicion_texto">
    				<fieldset>
    					<legend>Solicitado desde el HCD</legend>

    					<!-- FECHA DE SOLICITUD HCD, EN FORMATO aaaa-mm-dd hh:mm:ss -->
    					<input type="hidden" name="fecha_solicitud" id="fecha_solicitud" value="<?php echo $prestamo->fecha_solicitud; ?>" />

    					<!-- FECHA DE SOLICITUD DESDE EL HCD -->
    					Fecha:&nbsp;&nbsp;&nbsp;<input type="text" name="solo_fecha_solicitud" id="solo_fecha_solicitud" value="<?php echo date("d/m/Y"); ?>" size="8" maxlength="10" <?php echo ($parametros_vista['estado_a_editar'] != Prestamo::E_SOLICITADO) ? 'readonly style="color:#A09FA4;"' : ''; ?> onkeyup="mascara(this, '/', patron, true);" />

    					<?php
    					// EN CASO DE ALTA, SE MUESTRA EL CALENDARIO
    					if ( $parametros_vista['estado_a_editar'] == Prestamo::E_SOLICITADO )
    					{
    					?>
    						&nbsp;<input type="image" id="img_solo_fecha_solicitud" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la Fecha" width="16" height="16">
    					<?php
    					}
    					else
    					{
    					?>
    						&nbsp;<img src="imagenes/calendario/calendario_gris.gif" alt="" width="16" height="16">
    					<?php
    					}
    					?>

    					&nbsp;&nbsp;
    					Hora:&nbsp;<input type="text" name="solo_hora_solicitud" id="solo_hora_solicitud" value="<?php echo date("H:i:s"); ?>" size="6" maxlength="8" <?php echo $css_input_solo_lectura; ?> onkeyup="mascara(this, ':', patron_hora, true);" />

    					&nbsp;&nbsp;
    					Por:&nbsp;&nbsp;
    					<?php
    					// SI SE AGREGA UNA SOLICITUD
    			    	if( $parametros_vista['estado_a_editar'] == Prestamo::E_SOLICITADO )
    			    		// SE MUESTRA EL COMBO DE SOLICITANTES
    			    		echo $this->mostrarComboSolicitantes($solicitantes);
    			    	else
    			    		// SINO, SU NOMBRE
    			    		echo $prestamo->solicitante_nombre;
    					?>
    				</fieldset>

    				<!-- ESTADO ACTUAL -->
    				<fieldset>
    					Estado:&nbsp;
    					<span style="padding:3px;<?php echo ($prestamo) ? $this->prestamoEstadoToColor($prestamo) : "background-color: #FCF8E3;color: #C09853;"; ?>">
    						<?php echo ($prestamo) ? $prestamo->EstadoToString() : "Solicitado"; ?>
    					</span>
    				</fieldset>

    				<!-- OBSERVACIONES -->
    		    	<fieldset>
    					<legend>Observaciones</legend>
    					<textarea name="observaciones_prestamo" id="observaciones_prestamo"><?php echo $prestamo->observaciones_prestamo; ?></textarea>
    				</fieldset>
    			</div>
    		</form>
    	</div>
    	<script>
			// SE MUESTRA EL SOLICITANTE
			$('tipo').value= '<?php echo ($prestamo->tipo) ? $prestamo->tipo : D; ?>';

			// SE ASIGNA EL ESTADO A EDITAR
    		var estado_a_editar = '<?php echo $parametros_vista['estado_a_editar']; ?>';

    	    // SI SE AGREGA UN PRESTAMO
        	if( estado_a_editar == '<?php echo Prestamo::E_SOLICITADO; ?>' ) {
    			// CALENDARIO PARA LA FECHA DE SOLICITUD
        		var calendario = new Zapatec.Calendar.setup({
        			inputField: "solo_fecha_solicitud",
        			ifFormat: "%d/%m/%Y",
        			button: "img_solo_fecha_solicitud",
        			showsTime: false
        		});

        		// SE MUESTRA EL SOLICITANTE
        		$('solicitante').value= '<?php echo ($prestamo->solicitante_tipo) ? $prestamo->solicitante_tipo.'-'.$prestamo->solicitante_codigo : 0; ?>';

        		// AL ELEGIR UN SOLICITANTE
        		$('solicitante').addEvent('change', function() {
        			if ($('solicitante').value != '') {
        				// SE SEPARA EL TIPO Y EL CODIGO, POR EL GUIÓN MEDIO
        				var partes_solicitante = $('solicitante').value.split('-');
            			// SE ASIGNA EL NUEVO VALOR PARA TIPO Y CODIGO
       					$('solicitante_tipo').value = partes_solicitante[0];
       					$('solicitante_codigo').value = partes_solicitante[1];
       				}
       			});
    		}

    		// SI LA FECHA DE SOLICITUD EXISTE
    		if ($('fecha_solicitud').value != '') {
    			var partes_fecha_solicitud = $('fecha_solicitud').value.split(" ");
    			$('solo_fecha_solicitud').value = formatearConBarra(partes_fecha_solicitud[0]);
    			$('solo_hora_solicitud').value = partes_fecha_solicitud[1];
    		}

    		setfocus('anio');
    	</script>
    <?php
    }

    /**
     * Muestra una ventana modal con un campo de texto,
     * a medida que se escribe va sugiriendo posibles nombres de Solicitantes,
     * al cerrar la modal se muestra el nombre elegido en el combo del formulario de edición.
     *
     * @param array $solicitantes, listado de posibles solicitantes
     */
    public function pedirNombreSolicitanteModal($solicitantes)
    {
    ?>
		<div class="autosugerido">
    		Nombre: <input type="text" id="nombre_sugerido" name="nombre_sugerido" maxlength="100" />
    		<div id="sugerencias"><ul></ul></div>
    	</div>
    	<div class="margen_modal"></div>
    	<div class="cerrar_pedirNombreModal">
    		<div class="titulo_pedirNombreModal">Buscar Solicitante.</div>
    		<div id="btCerrar_pedirNombreModal" class="btCerrar_pedirNombreModal"></div>
    	</div>
    	<script type="text/javascript">

    		ventana_modal = "si";

    		var solicitantes_a_elegir = new Array(
    			<?php
    			$cantidad = count($solicitantes);
    			for ($i=0; $i < $cantidad; $i++) {
    				$solicitanteModal = &$solicitantes[$i];

    				if ( $i == $cantidad-1 )
    					echo "'".$solicitanteModal['tipo_grp'].", ".$solicitanteModal['codigo_grp'].", ".$solicitanteModal['descripcion_grp']."'";
    				else
    					echo "'".$solicitanteModal['tipo_grp'].", ".$solicitanteModal['codigo_grp'].", ".$solicitanteModal['descripcion_grp']."',";
    			}
    			?>
    		);

    		new AutoSuggest($('nombre_sugerido'), solicitantes_a_elegir, 'solicitante');

    		setfocus('nombre_sugerido');

    		$('btCerrar_pedirNombreModal').addEvent('click', function() {
    			cerrarModalPedirNombre();
    		});
    	</script>
   	<?php
    }

    /**
     * Muestra un combo de posibles solicitantes
     * y un vínculo a una ventana modal para buscar
     * mediante un campo que sugiere nombres que concuerden con lo uno vaya escribiendo
     *
     * @param array $solicitantes
     */
    public function mostrarComboSolicitantes($solicitantes)
    {
    ?>
		<select id="solicitante" name="solicitante" class="msc_combo msc_combo_ancho" style="width:264px;">
    	    <option value="0">seleccionar</option>
    	    <?php
    	    $cant_solicitantes = count($solicitantes);
    	    for ($i=0; $i < $cant_solicitantes; $i++) {
    	    	$solicitante = &$solicitantes[$i];
    	    ?>
    	    	<option value="<?php echo $solicitante['tipo_grp'].'-'.$solicitante['codigo_grp']; ?>"><?php echo $solicitante['tipo_grp'].', '.$solicitante['codigo_grp'].', '.$solicitante['descripcion_grp']; ?></option>
    	    <?php
    	    }
    	    ?>
    	</select>
    	&nbsp;
    	<a href="javascript:modalGaby('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=pedirNombreSolicitanteModal');" title="Buscar por Nombre">
    		<img src="imagenes/zoom_16x16.gif" width="16" height="16" />
    	</a>
    <?php
    }

    /**
     * Se genera el reporte en formato PDF para guardarlo y/o imprimirlo
     * (los métodos utilizados aquí están definidos en el archivo html2pdf.class.php)
     * @param Array $listado, colección de instancias de Prestamos
     * @param Array $parametros_vista
     */
    public function generarReporte($listado = '', $parametros_vista = '')
    {
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
   						<td class="pdf_valor_celda_alineado_izquierda" style="width:90%;">Sistema de Expedientes - <b>Pr&eacute;stamos</b></td>
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
   						<th>Solicitante</th>
       	    			<th>Fecha<br>Solicitud</th>
       	    			<th>Fecha<br>Pr&eacute;stamo</th>
       	    			<th>Fecha<br>Devoluci&oacute;n</th>
       	    			<th>Fecha<br>Anulado</th>
       	    			<th>Estado</th>
       	    			<th>N&uacute;mero</th>
       	    			<th>Folio</th>
       	    			<th>Observaciones</th>
   					</tr>
   				</thead>
   				<tbody class="pdf_cuerpo_scrolleable">
   					<?php
   					foreach ($listado as $prestamo)
   					{
   					?>
   						<tr class="pdf_tabla_titulos">
   							<td class="pdf_valor_celda_centrado" style="width:150px;">
   								<?php echo $prestamo->ToStringDescription(); ?>
   							</td>
   							<td class="pdf_valor_celda_alineado_izquierda" style="width:230px;">
    							<?php echo ($prestamo->solicitante_nombre != '') ? $prestamo->solicitante_nombre : '&nbsp;' ; ?>
    						</td>
   							<td class="pdf_valor_celda_centrado" style="width:70px;">
   								<?php echo $this->verificarFecha($prestamo, Prestamo::E_SOLICITADO); ?>
   							</td>
   							<td class="pdf_valor_celda_centrado" style="width:70px;">
   								<?php echo $this->verificarFecha($prestamo, Prestamo::E_PRESTADO); ?>
   							</td>
   							<td class="pdf_valor_celda_centrado" style="width:70px;">
   								<?php echo $this->verificarFecha($prestamo, Prestamo::E_DEVUELTO); ?>
   							</td>
   							<td class="pdf_valor_celda_centrado" style="width:70px;">
   								<?php echo $this->verificarFecha($prestamo, Prestamo::E_ANULADO); ?>
   							</td>
   							<td class="pdf_valor_celda_alineado_izquierda" style="width:120px;">
   								<?php echo ($prestamo->estado != '') ? $prestamo->EstadoToString() : '&nbsp;' ; ?>
   							</td>
   							<td class="pdf_valor_celda_alineado_derecha" style="width:40px;">
   								<?php echo ($prestamo->libro_numero != '') ? ($prestamo->libro_numero) : '&nbsp;' ; ?>
   							</td>
   							<td class="pdf_valor_celda_alineado_derecha" style="width:40px;">
   								<?php echo ($prestamo->libro_folio != '') ? ($prestamo->libro_folio) : '&nbsp;' ; ?>
   							</td>
   							<td class="pdf_valor_celda_alineado_izquierda" style="width:250px;">
   								<?php echo ($prestamo->observaciones_prestamo != '') ? $prestamo->observaciones_prestamo : '&nbsp;' ;//ObtenerResumenObservacion() ?>
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
   			$html2pdf = new HTML2PDF('L','LEGAL','es');
   			$html2pdf->pdf->SetDisplayMode('fullpage');
   			$html2pdf->setDefaultFont('Arial');

   			// Se realiza la conversión HTML => PDF
   			$html2pdf->WriteHTML($content);

   			// Se envía el documento al navegador,
   			// para que se visualice la ventana para Abrir o Guardar dicho documento
   			$html2pdf->Output('listado_prestamos.pdf', 'D');
   		}
   		catch(HTML2PDF_exception $e) {
   			echo $e;
   			exit;
   		}
   	}
}
?>

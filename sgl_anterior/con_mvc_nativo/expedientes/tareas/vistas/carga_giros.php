<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaCargaGiros extends VistaBase
{
	private $controlador;
	private $formulario;

	public function __construct()
	{
	    $this->controlador = 'carga_giros';
	    $this->formulario = 'formCargaGiros';
	}
	
	public function mostrarModalCargaGiros($datos)
	{
	?>
		<script type="text/javascript">
			// SE MUESTRA LA VENTANA MODAL PARA CARGAR VARIOS GIROS
			refrescar('tareas/index.php?controlador=carga_giros&accion=cargarGiros&anio=<?php echo $datos['anio']; ?>&tipo=<?php echo $datos['tipo']; ?>&numero=<?php echo $datos['numero']; ?>&cuerpo=<?php echo $datos['cuerpo']; ?>&alcance=<?php echo $datos['alcance']; ?>', 'capaVentana');
		</script>
	<?php	
	}
	
	public function mostrarLeyendaSegunTipo($tipo)
	{
		switch($tipo) {
			case 'E':
				$leyenda = "del Expediente";
				break;
			case 'N':
				$leyenda = "de la Nota";
				break;
			case 'R':
				$leyenda = "de la Recomendaci&oacute;n";
				break;
		}
		return $leyenda;
	}

	public function cargarGiros($listadoComisiones = '', $datos = '')
	{
	?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>
		
		<div id="precarga_principal" style="display:none"></div>
		<div id="contenidoAjaxCargaGiros" class="mstcg_gral mstcg_texto">
			
			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>
			
			<form action="tareas/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">
				
				<input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
				<input type="hidden" name="accion" id="accion" value="guardar" />
				<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
				<input type="hidden" name="tcg_anio" id="tcg_anio" value="<?php echo $datos['anio']; ?>" />
				<input type="hidden" name="tcg_tipo" id="tcg_tipo" value="<?php echo $datos['tipo']; ?>" />
				<input type="hidden" name="tcg_numero" id="tcg_numero" value="<?php echo $datos['numero']; ?>" />
				<input type="hidden" name="tcg_cuerpo" id="tcg_cuerpo" value="<?php echo $datos['cuerpo']; ?>" />
				<input type="hidden" name="tcg_alcance" id="tcg_alcance" value="<?php echo $datos['alcance']; ?>" />
				
				<div id="dragger_carga_giros" class="msc_titulos degradado">Carga de Giros</div>
				<div style="height:5px;font-size:0;"></div>
				<div>
				    Carga Inicial de Giros a Comisi&oacute;n, <?php echo $this->mostrarLeyendaSegunTipo($datos['tipo']); ?>:
				    <br><br>
				    <b>
						<span id="cgc_anio"><?php echo $datos['anio']; ?></span>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<span id="cgc_tipo"><?php echo $datos['tipo']; ?></span>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<span id="cgc_numero"><?php echo $datos['numero']; ?></span>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<span id="cgc_cuerpo"><?php echo $datos['cuerpo']; ?></span>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<span id="cgc_alcance"><?php echo $datos['alcance']; ?></span>
				    </b>
				</div>
				<div style="height:7px;font-size:0;border-bottom:1px solid #838079;"></div>
				<div class="mstcg_fecha_comisiones">
					<div style="height:7px;font-size:0;"></div>
					<div class="mstcg_margen"></div>
					<div class="mstcg_fecha">
						<span>Fecha 1&deg; Giro</span>
						<input type="text" id="mstcg_fecha_desde" name="mstcg_fecha_desde" value="<?php echo date("d/m/Y"); ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="mstcg_valor_fecha" />
						<input type="image" id="mstcg_btffecha_desde" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha de entrada del giro" width="16" height="16">
					</div>
					<div class="mstcg_margen"></div>
					<div class="mstcg_comisiones">
						<span>Comisi&oacute;n</span>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<span>Observaci&oacute;n</span><br>
						<select id="tcg_comision1" name="tcg_comision1" class="msl_combo">
							<option value="0">0, TODAS</option>
							<?php
							$cant_comisiones1 = count($listadoComisiones);
							for ($c=0; $c < $cant_comisiones1; $c++)
							{
								$comision = &$listadoComisiones[$c];
							?>	
								<option value="<?php echo $comision['tipo_grp'].'-'.$comision['codigo_grp']; ?>"><?php echo $comision['codigo_grp'].', '.$comision['descripcion_grp']; ?></option>
							<?php
							}
							?>
						</select>
						&nbsp;
						<input type="text" name="tcg_observacion_comision1" id="tcg_observacion_comision1" value="" disabled>
						<div style="height:5px;font-size:0;"></div>
						<select id="tcg_comision2" name="tcg_comision2" class="msl_combo">
							<option value="0">0, TODAS</option>
							<?php
							$cant_comisiones2 = count($listadoComisiones);
							for ($c=0; $c < $cant_comisiones2; $c++)
							{
								$comision = &$listadoComisiones[$c];
							?>	
								<option value="<?php echo $comision['tipo_grp'].'-'.$comision['codigo_grp']; ?>"><?php echo $comision['codigo_grp'].', '.$comision['descripcion_grp']; ?></option>
							<?php
							}
							?>
						</select>
						&nbsp;
						<input type="text" name="tcg_observacion_comision2" id="tcg_observacion_comision2" value="" disabled>
						<div style="height:5px;font-size:0;"></div>
						<select id="tcg_comision3" name="tcg_comision3" class="msl_combo">
							<option value="0">0, TODAS</option>
							<?php
							$cant_comisiones3 = count($listadoComisiones);
							for ($c=0; $c < $cant_comisiones3; $c++)
							{
								$comision = &$listadoComisiones[$c];
							?>	
								<option value="<?php echo $comision['tipo_grp'].'-'.$comision['codigo_grp']; ?>"><?php echo $comision['codigo_grp'].', '.$comision['descripcion_grp']; ?></option>
							<?php
							}
							?>
						</select>
						&nbsp;
						<input type="text" name="tcg_observacion_comision3" id="tcg_observacion_comision3" value="" disabled>
						<div style="height:5px;font-size:0;"></div>
						<select id="tcg_comision4" name="tcg_comision4" class="msl_combo">
							<option value="0">0, TODAS</option>
							<?php
							$cant_comisiones4 = count($listadoComisiones);
							for ($c=0; $c < $cant_comisiones4; $c++)
							{
								$comision = &$listadoComisiones[$c];
							?>	
								<option value="<?php echo $comision['tipo_grp'].'-'.$comision['codigo_grp']; ?>"><?php echo $comision['codigo_grp'].', '.$comision['descripcion_grp']; ?></option>
							<?php
							}
							?>
						</select>
						&nbsp;
						<input type="text" name="tcg_observacion_comision4" id="tcg_observacion_comision4" value="" disabled>
						<div style="height:5px;font-size:0;"></div>
						<select id="tcg_comision5" name="tcg_comision5" class="msl_combo">
							<option value="0">0, TODAS</option>
							<?php
							$cant_comisiones5 = count($listadoComisiones);
							for ($c=0; $c < $cant_comisiones5; $c++)
							{
								$comision = &$listadoComisiones[$c];
							?>	
								<option value="<?php echo $comision['tipo_grp'].'-'.$comision['codigo_grp']; ?>"><?php echo $comision['codigo_grp'].', '.$comision['descripcion_grp']; ?></option>
							<?php
							}
							?>
						</select>
						&nbsp;
						<input type="text" name="tcg_observacion_comision5" id="tcg_observacion_comision5" value="" disabled>
						<div style="height:5px;font-size:0;"></div>
						<select id="tcg_comision6" name="tcg_comision6" class="msl_combo">
							<option value="0">0, TODAS</option>
							<?php
							$cant_comisiones6 = count($listadoComisiones);
							for ($c=0; $c < $cant_comisiones6; $c++)
							{
								$comision = &$listadoComisiones[$c];
							?>	
								<option value="<?php echo $comision['tipo_grp'].'-'.$comision['codigo_grp']; ?>"><?php echo $comision['codigo_grp'].', '.$comision['descripcion_grp']; ?></option>
							<?php
							}
							?>
						</select>
						&nbsp;
						<input type="text" name="tcg_observacion_comision6" id="tcg_observacion_comision6" value="" disabled>
					</div>
				</div>
				<div class="mstcg_carga_giros_margen_botones_sup"></div>
				<div style="height:25px;">
				    <div class="mstcg_carga_giros_boton degradado">
						<a id="mstcg_btCancelar" title="Cancelar y Volver" href="index.php">Cancelar</a>
				    </div>
				    <div class="mstcg_carga_giros_boton degradado">
						<a id="mstcg_btGuardar" title="Guardar Giro a comisi&oacute;n" href="#">Guardar</a>
				    </div>
				</div>
				<div class="mstcg_carga_giros_margen_botones_inf"></div>
				
			</form>
		</div>	
		<script>
			window.addEvent('domready', function()
			{
				//CALENDARIO PARA LA FECHA DESDE
				var calDesde = new Zapatec.Calendar.setup({

					inputField:"mstcg_fecha_desde",
					ifFormat:"%d/%m/%Y",
					button:"mstcg_btffecha_desde",
					showsTime:false
				});
				
				$('mstcg_btGuardar').addEvent('click', function()
				{	
					var mensaje = '';
					var error = false;
				
					if ( $('mstcg_fecha_desde').value == '' )
					{
						mensaje = "Debe ingresar la Fecha de entrada del Giro.\n";
						error = true;
					}
					
					if ( $('tcg_comision1').value == 0 && $('tcg_comision2').value == 0 && $('tcg_comision3').value == 0 && $('tcg_comision4').value == 0 && $('tcg_comision5').value == 0 && $('tcg_comision6').value == 0 )
					{
						mensaje += "Debe ingresar por lo menos una Comisi"+'\u00f3'+"n.";
						error = true;
					}
					
					if (error){
						alert(mensaje);
					}else{	
						enviarForm('formCargaGiros', 'tareas', 'contenidoAjaxPrincipal');
					}	
				});
				$('tcg_comision1').addEvent('change', function()
				{
					if ( $('tcg_comision1').value == 0 )
					{
						$('tcg_observacion_comision1').disabled = true;
					}	
					else
					{
						$('tcg_observacion_comision1').disabled = false;
					}	
				});
				$('tcg_comision2').addEvent('change', function()
				{
					if ( $('tcg_comision2').value == 0 )
					{
						$('tcg_observacion_comision2').disabled = true;
					}	
					else
					{
						$('tcg_observacion_comision2').disabled = false;
					}	
				});
				$('tcg_comision3').addEvent('change', function()
				{
					if ( $('tcg_comision3').value == 0 )
					{
						$('tcg_observacion_comision3').disabled = true;
					}	
					else
					{
						$('tcg_observacion_comision3').disabled = false;
					}	
				});
				$('tcg_comision4').addEvent('change', function()
				{
					if ( $('tcg_comision4').value == 0 )
					{
						$('tcg_observacion_comision4').disabled = true;
					}	
					else
					{
						$('tcg_observacion_comision4').disabled = false;
					}	
				});
				$('tcg_comision5').addEvent('change', function()
				{
					if ( $('tcg_comision5').value == 0 )
					{
						$('tcg_observacion_comision5').disabled = true;
					}	
					else
					{
						$('tcg_observacion_comision5').disabled = false;
					}	
				});
				$('tcg_comision6').addEvent('change', function()
				{
					if ( $('tcg_comision6').value == 0 )
					{
						$('tcg_observacion_comision6').disabled = true;
					}	
					else
					{
						$('tcg_observacion_comision6').disabled = false;
					}	
				});
				
				var menuDrag = new Drag.Move($('contenidoAjaxCargaGiros'), {
				   handle: $('dragger_carga_giros')
				});
			});
		</script>
	<?php
	}

}
?>	

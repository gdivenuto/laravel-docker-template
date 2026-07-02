<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaCredenciales extends VistaBase
{
	private $controlador;
	private $formulario;
	private $directorio_fotos;
	private $nroPagina;

	public function __construct()
	{
		$this->controlador = 'credenciales';
		$this->formulario = 'formInformes';
		$this->directorio_fotos = '/var/www/sgl/personal/fotos/';

		// Se crea una instancia del modelo de Informes
		$this->modelo = new informesModel();
	}

	/**
	 * Se muestra una ventana modal, para solicitar la información necesaria para generar el certificado de trabajo
	 *
	 * @param array $datos
	 * @param array $cargos_reconocidos
	 */
	public function pedirInfoParaCertificadoTrabajo($datos, $cargos_reconocidos = null)
	{
	?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

		<div id="precarga_principal" style="display:none"></div>

		<div id="contenedoraPedidoInfoParaCertificadoTrabajo" class="i_modal_texto">

			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>

			<form action="informes/index.php" method="POST" name="formCertificadoTrabajo" id="formCertificadoTrabajo">

			    <input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
			    <input type="hidden" name="accion" id="accion" value="generarCertificadoTrabajo" />
			    <input type="hidden" name="legajo" id="legajo" value="<?php echo $datos['p_legajo']; ?>" />
			    <input type="hidden" name="nombre" id="nombre" value="<?php echo $datos['p_nombre']; ?>" />
			    <input type="hidden" name="apellido" id="apellido" value="<?php echo $datos['p_apellido']; ?>" />

				<div id="dragger_certificado_trabajo" class="degradado">
					Certificado de trabajo predeterminado de <?php echo $datos['p_nombre'].' '.$datos['p_apellido']; ?>
				</div>
				<div id="certificado_trabajo_destinatario">
					Para ser presentado a: <input type="text" name="ct_destinatario" id="ct_destinatario" value="" />
				</div>
				<div id="certificado_trabajo_opciones">
					En car&aacute;cter de:<br>
					<input name="ct_opcion_tipo" type="radio" value="1" id="op_empleado_bloque" checked />&nbsp;Empleado de Bloque
					<input name="ct_opcion_tipo" type="radio" value="2" id="op_empleado_planta" />&nbsp;Empleado de Planta
					<input name="ct_opcion_tipo" type="radio" value="3" id="op_presidente_hcd" />&nbsp;Presidente HCD
					<input name="ct_opcion_tipo" type="radio" value="4" id="op_secretario_hcd" />&nbsp;Secretario HCD
					<input name="ct_opcion_tipo" type="radio" value="5" id="op_concejal" />&nbsp;Concejal
				</div>
				<div id="certificado_trabajo_contenedora_cargos">
					<table width="100%" >
						<thead class="e_tabla_titulos">
						  	<tr>
						  		<th nowrap class="orden_link" width="237px">Nombre del Cargo</th>
								<th nowrap class="orden_link" width="70px">Fecha de Alta</th>
								<th nowrap class="orden_link" width="70px">Fecha de Baja</th>
								<th nowrap class="orden_link" width="70px">Decreto de Baja</th>
						  	</tr>
						</thead>
						<tbody class="e_tabla_texto e_cuerpo_scrolleable">
							<?php
							$cant_cargos_reconocidos = count($cargos_reconocidos);
							for ($cr=0; $cr < $cant_cargos_reconocidos; $cr++)
							{
								$cargo_reconocido = &$cargos_reconocidos[$cr];
							?>
								<tr>
									<td class="certificado_trabajo_listado_cargos_nombre"><?php echo $cargo_reconocido['cc_nombre']; ?></td>
									<td class="certificado_trabajo_listado_cargos_fechas">
										<a href="javascript:cargarTextoParaCertificadoTrabajo('<?php echo $cargo_reconocido['c_fecha_alta']; ?>')"><?php echo $this->formatearFecha($cargo_reconocido['c_fecha_alta']); ?></a>
									</td>
									<td class="certificado_trabajo_listado_cargos_fechas"><?php echo $this->formatearFecha($cargo_reconocido['c_fecha_baja']); ?></td>
									<td class="certificado_trabajo_listado_cargos_nro_decreto_baja"><?php echo $cargo_reconocido['c_nro_decreto_baja']; ?></td>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				</div>
				<div id="certificado_trabajo_contenedora_area_texto">
					<!-- AQUÍ SE CARGA EL TEXTO SEGÚN EL TIPO DE CERTIFICADO A GENERAR -->
				</div>
				<div class="certificado_trabajo_seccion_botones">
					<div class="certificado_trabajo_margen_lateral_botones"></div>
					<div id="btGenerarPDF" class="p_boton_edicion p_boton_nuevo">
						<a href="javascript:generarCertificadoTrabajo();">
							<img src="imagenes/barra/pdf.jpg" width="15" height="15" />&nbsp;&nbsp;&nbsp;Generar
						</a>
					</div>
					<div class="certificado_trabajo_margen_medio_botones"></div>
					<div class="p_boton_edicion p_boton_nuevo">
						<a href="javascript:cerrarModalNueva();">
							<img src="imagenes/barra/error_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Cancelar
						</a>
					</div>
				</div>
			</form>
		</div>
		<script type="text/javascript">

			// Devuelve el tipo de certificado elegido
			function obtenerTipoCertificadoElegido()
			{
				var i;

			    for (i=0; i < 5; i++)
				{
			    	if (document.formCertificadoTrabajo.ct_opcion_tipo[i].checked)
			        	break;
			    }

			    return document.formCertificadoTrabajo.ct_opcion_tipo[i].value;
			}

			// Carga el texto para el certificado determinado
			function cargarTextoParaCertificadoTrabajo(fecha_alta_inicial)
			{
				var tipo_certificado_elegido;

				tipo_certificado_elegido = obtenerTipoCertificadoElegido();

				// Se muestra el botón Generar PDF
				$('btGenerarPDF').setStyle('display', 'inline');

				refrescar('informes/index.php?controlador=credenciales&accion=cargarTextoParaCertificadoTrabajo&legajo='+$('legajo').value+'&ct_destinatario='+$('ct_destinatario').value+'&ct_tipo='+tipo_certificado_elegido+'&ct_fecha_alta_inicial='+fecha_alta_inicial, 'certificado_trabajo_contenedora_area_texto');
			}

			// Se genera el certificado de trabajo
			function generarCertificadoTrabajo()
			{
				// Se envia el formulario
				$('formCertificadoTrabajo').submit();
			}

			var menuDrag = new Drag.Move($('contenedoraPedidoInfoParaCertificadoTrabajo'), {
				handle: $('dragger_certificado_trabajo')
			});

			// Se oculta el botón Generar PDF
			$('btGenerarPDF').setStyle('display', 'none');
		</script>
	<?php
	}

	/**
	 * Se genera el certificado de trabajo en formato PDF, para un EMPLEADO de BLOQUE (Planta Política)
	 *
	 * @param array $datos
	 */
	public function cargarTextoParaCertificadoEmpleadoBloque($datos)
	{
		$texto  = " CERTIFICO que";

		$texto .= ($datos['p_sexo'] == 'M') ? " el Sr. " : " la Sra. ";

		$texto .= $datos['p_nombre']." ".$datos['p_apellido'];

		$texto .= " - D.N.I. ".number_format($datos['p_nro_documento'], 0, '', '.');

		$texto .= " - Legajo N&deg; ".number_format($datos['p_legajo'], 0, '', '.')."/".$datos['digito'];

		// Si posee una fecha de baja, se expresa en pasado, sino en presente
		$texto .= ($datos['datos_ultimo_cargo']['c_fecha_baja']) ? " cumpli&oacute;" : " cumple";

		$texto .= " funciones en un Bloque Pol&iacute;tico de este Honorable Concejo Deliberante del Partido de General Pueyrredon";

		$texto .= " desde el ".$this->mostrarFechaLetras($datos['ct_fecha_alta_inicial']);

		// Si posee una fecha de baja, se muestra, sino se expresa su continuidad laboral
		$texto .= ($datos['datos_ultimo_cargo']['c_fecha_baja']) ? " al ".$this->mostrarFechaLetras($datos['datos_ultimo_cargo']['c_fecha_baja']) : " y contin&uacute;a.";

		$texto .= "\n Se extiende el presente a pedido";

		$texto .= ($datos['p_sexo'] == 'M') ? " del interesado " : " de la interesada ";

		$texto .= "para ser presentado";

		// Se muestra a quién va destinado dicho certificado, en caso de haberse definido uno, sino la leyenda especificada
		$texto .= ($datos['ct_destinatario']) ? " a ".$datos['ct_destinatario'] : " ante quien corresponda";

		$texto .= ", en la ciudad de Mar del Plata a los ".$this->mostrarDiaEnLetras(date("d"))." d&iacute;as del mes de ".$this->mostrarNombreMes(date("m"))." de ".$this->mostrarAnioEnLetras(date("Y")).".";
	?>
		<textarea id="ct_texto" name="ct_texto"><?php echo $texto; ?></textarea>
	<?php
	}

	/**
	 * Se genera el certificado de trabajo en formato PDF, para un legajo de Planta Permanente
	 *
	 * @param array $datos
	 */
	public function cargarTextoParaCertificadoEmpleadoPlanta($datos)
	{
		$texto  = " CERTIFICO que";

		$texto .= ($datos['p_sexo'] == 'M') ? " el Sr. " : " la Sra. ";

		$texto .= $datos['p_nombre']." ".$datos['p_apellido'];

		$texto .= " - D.N.I. ".number_format($datos['p_nro_documento'], 0, '', '.');

		$texto .= " - Legajo N&deg; ".number_format($datos['p_legajo'], 0, '', '.')."/".$datos['digito'];

		// Si posee una fecha de baja, se expresa en pasado, sino en presente
		$texto .= ($datos['datos_ultimo_cargo']['c_fecha_baja']) ? " cumpli&oacute;" : " cumple";

		$texto .= " funciones en ".$datos['datos_ultima_area']['area']." de este Honorable Concejo Deliberante del Partido de General Pueyrredon";

		$texto .= " desde el ".$this->mostrarFechaLetras($datos['ct_fecha_alta_inicial']);

		// Si posee una fecha de baja, se muestra, sino se expresa su continuidad laboral
		$texto .= ($datos['datos_ultimo_cargo']['c_fecha_baja']) ? " al ".$this->mostrarFechaLetras($datos['datos_ultimo_cargo']['c_fecha_baja']) : " y contin&uacute;a.";

		$texto .= "\n Se extiende el presente a pedido";

		$texto .= ($datos['p_sexo'] == 'M') ? " del interesado " : " de la interesada ";

		$texto .= "para ser presentado";

		// Se muestra a quién va destinado dicho certificado, en caso de haberse definido uno, sino la leyenda especificada
		$texto .= ($datos['ct_destinatario']) ? " a ".$datos['ct_destinatario'] : " ante quien corresponda";

		$texto .= ", en la ciudad de Mar del Plata a los ".$this->mostrarDiaEnLetras(date("d"))." d&iacute;as del mes de ".$this->mostrarNombreMes(date("m"))." de ".$this->mostrarAnioEnLetras(date("Y")).".";
	?>
		<textarea id="ct_texto" name="ct_texto"><?php echo $texto; ?></textarea>
	<?php
	}

	/**
	 * Se arma y se carga el texto para el certificado del PRESIDENTE del HCD, en el textarea
	 *
	 * @param array $datos
	 */
	public function cargarTextoParaCertificadoPresidenteHCD($datos)
	{
		$texto  = " CERTIFICO que";

		$texto .= ($datos['p_sexo'] == 'M') ? " el Sr. " : " la Sra. ";

		$texto .= $datos['p_nombre']." ".$datos['p_apellido'];

		$texto .= " - D.N.I. ".number_format($datos['p_nro_documento'], 0, '', '.');

		$texto .= " - Legajo N&deg; ".number_format($datos['p_legajo'], 0, '', '.')."/".$datos['digito'];

		// Si posee una fecha de baja, se expresa en pasado, sino en presente
		$texto .= ($datos['datos_ultimo_cargo']['c_fecha_baja']) ? " fue;" : " es";

		$texto .= " Concejal y Presidente del Honorable Concejo Deliberante del Partido de General Pueyrredon";

		$texto .= " desde el ".$this->mostrarFechaLetras($datos['ct_fecha_alta_inicial']);

		$texto .= " y por un per&iacute;odo de cuatro a&ntilde;os; efectu&aacute;ndosele los aportes jubilatorios a favor del Instituto de Previsi&oacute;n Social de la Provincia de Buenos Aires (Secci&oacute;n Municipalidades - Ley 9650).";

		$texto .= "\n Se extiende el presente a pedido";

		$texto .= ($datos['p_sexo'] == 'M') ? " del interesado " : " de la interesada ";

		$texto .= "para ser presentado";

		// Se muestra a quién va destinado dicho certificado, en caso de haberse definido uno, sino la leyenda especificada
		$texto .= ($datos['ct_destinatario']) ? " a ".$datos['ct_destinatario'] : " ante quien corresponda";

		$texto .= ", en la ciudad de Mar del Plata a los ".$this->mostrarDiaEnLetras(date("d"))." d&iacute;as del mes de ".$this->mostrarNombreMes(date("m"))." de ".$this->mostrarAnioEnLetras(date("Y")).".";
	?>
		<textarea id="ct_texto" name="ct_texto"><?php echo $texto; ?></textarea>
	<?php
	}

	/**
	 * Se arma y se carga el texto para el certificado del SECRETARIO/A del HCD, en el textarea
	 *
	 * @param array $datos
	 */
	public function cargarTextoParaCertificadoSecretarioHCD($datos)
	{
		$texto  = " CERTIFICO que";

		$texto .= ($datos['p_sexo'] == 'M') ? " el Sr. " : " la Sra. ";

		$texto .= $datos['p_nombre']." ".$datos['p_apellido'];

		$texto .= " - D.N.I. ".number_format($datos['p_nro_documento'], 0, '', '.');

		$texto .= " - Legajo N&deg; ".number_format($datos['p_legajo'], 0, '', '.')."/".$datos['digito'];

		// Si posee una fecha de baja, se expresa en pasado, sino en presente
		$texto .= ($datos['datos_ultimo_cargo']['c_fecha_baja']) ? " fue;" : " es";

		$texto .= ($datos['p_sexo'] == 'M') ? " Secretario " : " Secretaria ";

		$texto .= " del Honorable Concejo Deliberante del Partido de General Pueyrredon";

		$texto .= " desde el ".$this->mostrarFechaLetras($datos['ct_fecha_alta_inicial']);

		$texto .= " y contin&uacute;a; efectu&aacute;ndosele los aportes jubilatorios a favor del Instituto de Previsi&oacute;n Social de la Provincia de Buenos Aires (Secci&oacute;n Municipalidades - Ley 9650).";

		$texto .= "\n Se extiende el presente a pedido";

		$texto .= ($datos['p_sexo'] == 'M') ? " del interesado " : " de la interesada ";

		$texto .= "para ser presentado";

		// Se muestra a quién va destinado dicho certificado, en caso de haberse definido uno, sino la leyenda especificada
		$texto .= ($datos['ct_destinatario']) ? " a ".$datos['ct_destinatario'] : " ante quien corresponda";

		$texto .= ", en la ciudad de Mar del Plata a los ".$this->mostrarDiaEnLetras(date("d"))." d&iacute;as del mes de ".$this->mostrarNombreMes(date("m"))." de ".$this->mostrarAnioEnLetras(date("Y")).".";
	?>
		<textarea id="ct_texto" name="ct_texto"><?php echo $texto; ?></textarea>
	<?php
	}

	/**
	 * Se arma y se carga el texto para el certificado del CONCEJAL del HCD, en el textarea
	 *
	 * @param array $datos
	 */
	public function cargarTextoParaCertificadoConcejal($datos)
	{
		$texto  = " CERTIFICO que";

		$texto .= ($datos['p_sexo'] == 'M') ? " el Sr. " : " la Sra. ";

		$texto .= $datos['p_nombre']." ".$datos['p_apellido'];

		$texto .= " - D.N.I. ".number_format($datos['p_nro_documento'], 0, '', '.');

		$texto .= " - Legajo N&deg; ".number_format($datos['p_legajo'], 0, '', '.')."/".$datos['digito'];

		// Si posee una fecha de baja, se expresa en pasado, sino en presente
		$texto .= ($datos['datos_ultimo_cargo']['c_fecha_baja']) ? " fue;" : " es";

		//$texto .= ($datos['p_sexo'] == 'M') ? " el" : " la";

		$texto .= " Concejal del Honorable Concejo Deliberante del Partido de General Pueyrredon";

		$texto .= " desde el ".$this->mostrarFechaLetras($datos['ct_fecha_alta_inicial']);

		$texto .= " y contin&uacute;a; efectu&aacute;ndosele los aportes jubilatorios a favor del Instituto de Previsi&oacute;n Social de la Provincia de Buenos Aires (Secci&oacute;n Municipalidades - Ley 9650).";

		$texto .= "\n Se extiende el presente a pedido";

		$texto .= ($datos['p_sexo'] == 'M') ? " del interesado " : " de la interesada ";

		$texto .= "para ser presentado";

		// Se muestra a quién va destinado dicho certificado, en caso de haberse definido uno, sino la leyenda especificada
		$texto .= ($datos['ct_destinatario']) ? " a ".$datos['ct_destinatario'] : " ante quien corresponda";

		$texto .= ", en la ciudad de Mar del Plata a los ".$this->mostrarDiaEnLetras(date("d"))." d&iacute;as del mes de ".$this->mostrarNombreMes(date("m"))." de ".$this->mostrarAnioEnLetras(date("Y")).".";
	?>
		<textarea id="ct_texto" name="ct_texto"><?php echo $texto; ?></textarea>
	<?php
	}

	/**
	 * Se genera el certificado de trabajo respectivo, en formato PDF
	 *
	 * @param array $datos
	 */
	public function generarCertificadoTrabajo($datos)
	{
		//Logger::GetInstance()->Log("datos_en_generarCertificadoTrabajo", $datos);

		ob_start();
	?>
		<style type="text/css">
			.certificado_trabajo_general {
				width: 100%;
				border-collapse: collapse;
				border: none;
			}
			.certificado_trabajo_imagen {
				width: 100%;
			}
			.certificado_trabajo_texto p {
				font-family: Arial;
				font-size: 13px;
				text-align: justify;
				line-height: 21px;
				margin-top: 50px;
			}
			.certificado_trabajo_texto span {
				font-style: italic;
			}
		</style>
		<page backtop="20mm" backbottom="7mm" backleft="15mm" backright="15mm">
			<table class="certificado_trabajo_general">
				<tr>
					<td class="certificado_trabajo_imagen">
						<img src="../imagenes/logo_credencial.jpg" width="400" height="85" align="left">
					</td>
				</tr>
				<tr>
					<td class="certificado_trabajo_texto">
						<p><?php echo $datos['ct_texto']; ?></p>
					</td>
				</tr>
			</table>
		</page>
	<?php
		$content = ob_get_clean();

		try
		{
			// Se realiza la conversion HTML => PDF
			$html2pdf = new HTML2PDF('P','Legal','es', array(mL, mT, mR, mB));
			$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->setDefaultFont('Arial');

			// Tratamiento del código HTML
			$html2pdf->WriteHTML($content);

			// Destino donde enviar el documento
			$html2pdf->Output("certificado trabajo de ".$datos['nombre']." ".$datos['apellido'].".pdf", 'D');
		}
		catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}

	/**
	 * Se solicita información para generar la credencial de un legajo determinado
	 *
	 * @param array $datos
	 */
	public function pedirInfoParaCredencial($datos)
	{
	?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

		<div id="precarga_principal" style="display:none"></div>

		<div id="contenedoraPedirInfoParaCredencial" class="prestamo_edicion_texto">

			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>

			<form action="informes/index.php" method="POST" name="formCredencial" id="formCredencial">

			    <input type="hidden" name="legajo" id="legajo" value="<?php echo $datos['p_legajo']; ?>" />

				<div id="dragger_credencial" class="degradado">
					Credencial de <?php echo $datos['p_nombre'].' '.$datos['p_apellido']; ?>
				</div>
				<div class="gc_credencial_contenedora_info">
					<div class="gc_credencial_info_titulos">
						<?php
						// 18/07/2019
						// Si NO es Defensor del Pueblo, se permite elegir su función
						if ( $datos['nombre_cargo'] != 'Defensor del Pueblo')
							echo '<div class="gc_credencial_titulo">Funci&oacute;n</div>';
						?>
						<div class="gc_credencial_titulo">Per&iacute;odo</div>
						<div class="gc_credencial_titulo">Fecha creaci&oacute;n</div>
					</div>
					<div class="gc_credencial_info_valores">
						<?php
						// 18/07/2019
						// Si NO es Defensor del Pueblo, se permite elegir su función
						if ( $datos['nombre_cargo'] != 'Defensor del Pueblo') {
						?>
							<div class="gc_credencial_valor">
								<select id="gc_funcion" name="gc_funcion">
									<option value="Presidente">Presidente</option>
									<option value="Concejal">Concejal</option>
									<option value="Secretario">Secretario</option>
									<option value="Secretaria">Secretaria</option>
								</select>
							</div>
						<?php
						} else
							echo '<input type="hidden" id="gc_funcion" name="gc_funcion" value="'.$datos['nombre_cargo'].'" />';
						?>
						<div class="gc_credencial_valor">
							<input type="text" id="gc_periodo_anio_inicio" name="gc_periodo_anio_inicio" value="" maxlength="4" />
							&nbsp;-&nbsp;
							<input type="text" id="gc_periodo_anio_fin" name="gc_periodo_anio_fin" value="" maxlength="4" />
						</div>
						<div class="gc_credencial_valor">
							<select id="gc_fecha_creacion_mes" name="gc_fecha_creacion_mes">
								<option value="1">Enero</option>
								<option value="2">Febrero</option>
								<option value="3">Marzo</option>
								<option value="4">Abril</option>
								<option value="5">Mayo</option>
								<option value="6">Junio</option>
								<option value="7">Julio</option>
								<option value="8">Agosto</option>
								<option value="9">Septiembre</option>
								<option value="10">Octubre</option>
								<option value="11">Noviembre</option>
								<option value="12">Diciembre</option>
							</select>
							&nbsp;-&nbsp;
							<input type="text" id="gc_fecha_creacion_anio" name="gc_fecha_creacion_anio" value="" maxlength="4" />
						</div>
					</div>
				</div>

				<div class="gc_credencial_seccion_botones">
					<div class="gc_credencial_margen_lateral_botones"></div>
					<div class="p_boton_edicion p_boton_nuevo">
						<a id="btGenerarCredencial" href="#" target="_blank" >
							<img src="imagenes/barra/pdf.jpg" width="15" height="15" />&nbsp;&nbsp;&nbsp;Generar
						</a>
					</div>
					<div class="gc_credencial_margen_medio_botones"></div>
					<div class="p_boton_edicion p_boton_nuevo">
						<a href="javascript:cerrarModalNueva();">
							<img src="imagenes/barra/error_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Cancelar
						</a>
					</div>
				</div>
			</form>
		</div>
		<script type="text/javascript">

			// Se selecciona el nombre del Cargo actual
			$('gc_funcion').value = '<?php echo $datos['nombre_cargo']; ?>';

			// Inicio del período
			$('gc_periodo_anio_inicio').value = '<?php echo $datos['periodo_anio_inicio']; ?>';

			// Fin del período
			$('gc_periodo_anio_fin').value = '<?php echo $datos['periodo_anio_fin']; ?>';

			// Número del Mes, de la fecha de creación de la credencial
			$('gc_fecha_creacion_mes').value = '<?php echo $datos['nro_mes_fecha_alta']; ?>';

			// Año de la fecha de creación de la credencial
			$('gc_fecha_creacion_anio').value = '<?php echo $datos['periodo_anio_inicio']; ?>';

			// Al cliquear el botón Generar
			$('btGenerarCredencial').addEvent('click', function()
			{
				$('btGenerarCredencial').setProperty('href', 'informes/index.php?controlador=credenciales&accion=generarCredencial&gc_funcion='+$('gc_funcion').value+'&gc_periodo_anio_inicio='+$('gc_periodo_anio_inicio').value+'&gc_periodo_anio_fin='+$('gc_periodo_anio_fin').value+'&gc_fecha_creacion_mes='+$('gc_fecha_creacion_mes').value+'&gc_fecha_creacion_anio='+$('gc_fecha_creacion_anio').value+'&legajo='+$('legajo').value);
	    	});

			var menuDrag = new Drag.Move($('contenedoraPedirInfoParaCredencial'), {
			   handle: $('dragger_credencial')
			});
		</script>
	<?php
	}

	/**
	 * Se genera la credencial de un legajo determinado, en formato pdf, TAMAÑO 614x199 px
	 *
	 * @param array $datos
	 */
	public function generarCredencial($datos, $info_legajo)
	{
		ob_start();

		$foto_legajo = ($info_legajo['p_foto']) ? utf8_decode($info_legajo['p_foto']) : 'avatar.jpg';

		$nombre_credencial = 'credencial_'.$info_legajo['p_apellido'].'_'.$info_legajo['p_nombre'].'.pdf';
	?>
		<style type="text/css">
			table {
				border-collapse: collapse;
				border: none;
			}
			td {
				padding: 0;
			}

			.cred_contenedora {
				width: 614px;
				height: 199px;
				background-image: url(/var/www/sgl/personal/imagenes/credencial/fondo_nuevo.jpg);
			}

			.cred_contenedor_dorso {
				width: 306px;
				height: 199px;
			}
			.cred_margen_para_doblez {
				width: 2px;
				height: 199px;
				background-color: #fff;
			}
			.cred_contenedor_frente {
				width: 306px;
				height: 199px;
			}

			.cred_titulo_superior {
				width: 306px;
				height: 14px;
				padding-top: 5px;
				font-size: 12px;
				font-weight: bold;
				text-align: center;
			    color: #fff;
			 }

			.cred_dorso_escudo {
				height: 135px;
				text-align: center;
			}
			.cred_dorso_escudo img {
				width: 64px;
				height: 75px;
				border: 0;
			}
			.cred_dorso_titulo_inferior {
				height: 35px;
				font-size: 10px;
				font-weight: bold;
				text-align: center;
			    color: #000;
			}

			.cred_contenedor_foto_e_info {
				width: 306px;
				height: 170px;
			}

			.cred_contenedor_foto {
				width: 140px;
				height: 160px;
			}
			.cred_contenedor_info {
				width: 160px;
				height: 120px;
				vertical-align: top;
				padding-top: 15px;
			}

			.cred_foto {
				width: 140px;
				height: 110px;
				text-align: center;
				vertical-align: bottom;
			}
			.cred_foto img {
				width: 110px;
				height: 110px;
			}
			.cred_fecha_creacion {
				width: 140px;
				height: 16px;
				font-family: Arial;
				font-size: 9px;
				color: #000;
			    text-align: center;
			}

			.cred_info {
				height: 12px;
				padding-bottom: 2px;
				font-family: Arial;
				font-size: 12px;
			    color: #000;
			    text-align: left;
			}
			.cred_info_funcion {
				font-size: 25px;
				padding-top: 5px;
				padding-bottom: 3px;
			}
			.cred_info_funcion_defensor_del_pueblo {
				padding-top: 0;
				padding-bottom: 3px;
				font-weight: bold;
			}
			.cred_info_periodo {
				font-size: 12px;
				padding-bottom: 2px;
			}
			.cred_leyenda_apellido_nombre {
				height: 7px;
				font-size: 7px;
				padding-bottom: 0;
				margin-bottom: 0;
			}
			.cred_altura_valor_apellido_nombres {
				height: 0;
				padding-top: 0;
				margin-top: 0;
			}
			.cred_info_espacio_forzado_inferior {
				height: 45px;
			}
			.cred_texto_negrita {
				font-weight: bold;
			}
		</style>
		<page backleft="20mm">
			<table class="cred_contenedora">
				<tr>
			        <td class="cred_contenedor_dorso">

			        	<table>
				        	<tr>
								<td class="cred_titulo_superior">HONORABLE CONCEJO DELIBERANTE</td>
							</tr>
							<tr>
								<td class="cred_dorso_escudo">
									<br><br><img src="/var/www/sgl/personal/imagenes/credencial/escudo_dorso_64x75.png" />
			        			</td>
							</tr>
							<tr>
								<td class="cred_dorso_titulo_inferior">MUNICIPALIDAD DEL PARTIDO<br>DE GENERAL PUEYRREDON</td>
							</tr>
			        	</table>

			        </td>
			        <td class="cred_margen_para_doblez">&nbsp;</td>
			        <td class="cred_contenedor_frente">

			        	<table>
				        	<tr>
								<td class="cred_titulo_superior">HONORABLE CONCEJO DELIBERANTE</td>
							</tr>
							<tr>
								<td class="cred_contenedor_foto_e_info">

									<table>
										<tr>
											<td class="cred_contenedor_foto">

												<table>
													<tr>
														<td class="cred_foto">
															<img src="<?php echo $this->directorio_fotos.$foto_legajo; ?>" />
														</td>
													</tr>
													<tr>
														<td class="cred_fecha_creacion">
															Mar del Plata, <span class="cred_texto_negrita"><?php echo $this->nombreMes($datos['gc_fecha_creacion_mes']).' '.$datos['gc_fecha_creacion_anio']; ?></span>
														</td>
													</tr>
												</table>

											</td>
											<td class="cred_contenedor_info">

												<table>
													<tr>
														<?php
														// SI NO es Defensor del Pueblo
														if ($datos['gc_funcion'] != 'Defensor del Pueblo')
															echo '<td class="cred_info cred_info_funcion">'.strtoupper($datos['gc_funcion']).'</td>';
														else {
														?>
															<td class="cred_info">
																<span class="cred_info_funcion_defensor_del_pueblo">DEFENSOR DEL PUEBLO</span>
																<br>
																<span class="cred_leyenda_apellido_nombre">DEL PARTIDO DE GENERAL PUEYRREDON</span>
															</td>
														<?php
														}
														?>
													</tr>
													<tr>
														<td class="cred_info cred_info_periodo cred_texto_negrita">PER&Iacute;ODO: <?php echo $datos['gc_periodo_anio_inicio'].' - '.$datos['gc_periodo_anio_fin']; ?></td>
													</tr>
													<tr>
														<td class="cred_info cred_leyenda_apellido_nombre">APELLIDO/S</td>
													</tr>
													<tr>
														<td class="cred_info cred_altura_valor_apellido_nombres"><span class="cred_texto_negrita"><?php echo strtoupper($info_legajo['p_apellido']); ?></span></td>
													</tr>
													<tr>
														<td class="cred_info cred_leyenda_apellido_nombre">NOMBRE/S</td>
													</tr>
													<tr>
														<td class="cred_info cred_altura_valor_apellido_nombres"><span class="cred_texto_negrita"><?php echo strtoupper($info_legajo['p_nombre']); ?></span></td>
													</tr>
													<tr>
														<td class="cred_info">D.N.I. : <span class="cred_texto_negrita"><?php echo number_format($info_legajo['p_nro_documento'], 0, '', '.'); ?></span></td>
													</tr>
													<?php
													// SI NO es Defensor del Pueblo, se agrega un espacio forzado
													if ($datos['gc_funcion'] != 'Defensor del Pueblo')
														echo '<tr><td class="cred_info_espacio_forzado_inferior">&nbsp;<br></td></tr>';
													?>
												</table>

											</td>
										</tr>
									</table>

								</td>
							</tr>
			        	</table>

			        </td>
			    </tr>
			</table>
		</page>
		<?php
		$contenido_a_convertir = ob_get_clean();

		try
		{
			// conversion HTML => PDF
			//los métodos utilizados aquí están definidos en el archivo html2pdf.class.php
			$html2pdf = new HTML2PDF('P','A4','es');
			$html2pdf->pdf->SetDisplayMode('real');
			$html2pdf->setDefaultFont('times');

			//Tratamiento del código HTML
			$html2pdf->WriteHTML($contenido_a_convertir);

			//Destino donde enviar el documento
			$html2pdf->Output($nombre_credencial, 'D');
		}
		catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}

	/**
	 * Se solicita información para generar la credencial de un legajo determinado
	 *
	 * @param array $datos
	 */
	public function elegirConcejalesParaCredenciales($listadoConcejales)
	{
		$cant_listado = ($listadoConcejales) ? count($listadoConcejales) : 0;
	?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

		<div id="precarga_modal" style="display:none"></div>
		 <div id="contenedoraSeleccionConcejalesParaCredenciales" class="prestamo_edicion_texto">

			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>

			<form action="informes/index.php" method="POST" name="formCredencialesParaConcejales" id="formCredencialesParaConcejales">

		        <input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
			    <input type="hidden" name="accion" id="accion" value="generarCredencialesParaConcejales" />

				<div id="dragger_credenciales" class="degradado">
					Seleccione los Concejales para generar sus Credenciales
				</div>
				<div class="gcc_credencial_contenedora">
					<div class="gcc_contenedora_listado_concejales">
						<div class="gcc_listado_concejales">
							<?php
							if ( $cant_listado > 0 )
							{
							?>
								<div class="gcc_fila_concejal">
									<div class="gcc_checkbox_y_nombre checkbox_con_texto gcc_texto">
										<label>
											<input type="checkbox" name="i_concejales_todos" id="i_concejales_todos" value="" onClick="javascript:marcar_desmarcar_checkboxConcejales('i_concejales_todos', 'formCredencialesParaConcejales');" />&nbsp;Todos
										</label>
									</div>
									<div class="gcc_periodo gcc_texto">&nbsp;</div>
								</div>
								<?php
								for ($c=0; $c < $cant_listado; $c++)
								{
									$concejal = &$listadoConcejales[$c];

									// Se obtiene información del último cargo del Concejal
									$info_cargo = $this->modelo->obtenerUltimoCargo($concejal['p_legajo']);

									// Se separa la fecha de alta del Concejal
									$aux = explode("-", $info_cargo['c_fecha_alta']);

									// Se asignan los años para el período respectivo
									$anio_inicio = $aux[0];
									$anio_fin    = $anio_inicio + 4;
								?>
									<div class="gcc_fila_concejal">
										<div class="gcc_checkbox_y_nombre checkbox_con_texto gcc_texto">
											<label>
												<input type="checkbox" name="gcc_concejales[]" id="i_concejal_<?php echo $c; ?>" value="<?php echo $concejal['p_legajo']; ?>" onclick="javascript:controlarFilaConcejal(this, '<?php echo $c; ?>');" />&nbsp;<?php echo $concejal['p_apellido'].', '.$concejal['p_nombre']; ?>
											</label>
										</div>
										<div class="gcc_periodo gcc_texto">
											<input type="text" name="gcc_anios_inicio[]" id="i_anio_inicio_<?php echo $c; ?>" value="<?php echo $anio_inicio; ?>" onkeypress="javascript:return soloEnteros(event);" disabled />
											&nbsp;-&nbsp;
											<input type="text" name="gcc_anios_fin[]" id="i_anio_fin_<?php echo $c; ?>" value="<?php echo $anio_fin; ?>" onkeypress="javascript:return soloEnteros(event);" disabled />
										</div>
									</div>
								<?php
								}
							}
							?>
						</div>

						<div class="gcc_contenedora_fecha_creacion degradado">
							Fecha de creaci&oacute;n:&nbsp;
							<select id="gc_fecha_creacion_mes" name="gc_fecha_creacion_mes">
								<option value="1">Enero</option>
								<option value="2">Febrero</option>
								<option value="3">Marzo</option>
								<option value="4">Abril</option>
								<option value="5">Mayo</option>
								<option value="6">Junio</option>
								<option value="7">Julio</option>
								<option value="8">Agosto</option>
								<option value="9">Septiembre</option>
								<option value="10">Octubre</option>
								<option value="11">Noviembre</option>
								<option value="12">Diciembre</option>
							</select>
							&nbsp;-&nbsp;
							<input type="text" id="gc_fecha_creacion_anio" name="gc_fecha_creacion_anio" value="<?php echo date("Y"); ?>" maxlength="4" />
						</div>

					</div>
					<div class="gcc_contenedora_botones">
						<div class="p_boton_edicion gcc_boton">
							<a id="btGenerarCredenciales" href="javascript:generarCredencialesParaConcejales();">
								<img src="imagenes/barra/pdf.jpg" width="15" height="15" />&nbsp;&nbsp;&nbsp;Generar
							</a>
						</div>
						<div class="p_boton_edicion gcc_boton">
							<a id="i_btCerrar">
								<img src="imagenes/barra/error_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Cerrar
							</a>
						</div>
					</div>
				</div>

			</form>
		</div>
		<script type="text/javascript">

			function generarCredencialesParaConcejales()
			{
				var error = false;
				var mensaje = "";

				// En caso que no se haya seleccionado NINGÚN Concejal
				if ( !verificarCheckbox('formCredencialesParaConcejales') )
				{
					error = true;
					mensaje += "Debe seleccionar por lo menos un Concejal.";
				}

				if (error)
				{
					alert(mensaje);
				}
				else
				{
					$('formCredencialesParaConcejales').submit();
				}
			}

			$('i_btCerrar').addEvent('click', function()
			{
				$("capaFondo").setStyle('visibility','hidden');
				$("capaVentana").setStyle('visibility','hidden');
			});

			var menuDrag = new Drag.Move($('contenedoraSeleccionConcejalesParaCredenciales'), {
			   handle: $('dragger_credenciales')
			});
		</script>
	<?php
	}

	/**
	 * Se generan las credenciales en lote, para todos los Concejales, en formato pdf, TAMAÑO 614x199 px
	 *
	 * @param array $datos
	 */
	public function generarCredenciales($info)
	{
		ob_start();

		$fecha_creacion_credenciales = $this->nombreMes($info['gcc_fecha_creacion_mes']).' '.$info['gcc_fecha_creacion_anio'];

		$nombre_archivo_credenciales = "credenciales_concejales_".$this->nombreMes($info['gcc_fecha_creacion_mes'])."_".$info['gcc_fecha_creacion_anio'].".pdf";
	?>
		<style type="text/css">
			table {
				border-collapse: collapse;
				border: none;
			}
			td {
				padding: 0;
			}

			.cred_contenedora {
				width: 614px;
				height: 199px;
				background-image: url(/var/www/sgl/personal/imagenes/credencial/fondo_nuevo.jpg);
				margin-top: 10px;
			}

			.cred_contenedor_dorso {
				width: 306px;
				height: 199px;
			}
			.cred_margen_para_doblez {
				width: 2px;
				height: 199px;
				background-color: #fff;
			}
			.cred_contenedor_frente {
				width: 306px;
				height: 199px;
			}

			.cred_titulo_superior {
				width: 306px;
				height: 14px;
				padding-top: 5px;
				font-size: 12px;
				font-weight: bold;
				text-align: center;
			    color: #fff;
			}

			.cred_dorso_escudo {
				height: 135px;
				text-align: center;
			}
			.cred_dorso_escudo img {
				width: 64px;
				height: 75px;
				border: 0;
			}
			.cred_dorso_titulo_inferior {
				height: 35px;
				font-size: 10px;
				font-weight: bold;
				text-align: center;
			    color: #000;
			}

			.cred_contenedor_foto_e_info {
				width: 306px;
				height: 170px;
			}

			.cred_contenedor_foto {
				width: 140px;
				height: 160px;
			}
			.cred_contenedor_info {
				width: 160px;
				height: 160px;
			}

			.cred_foto {
				width: 140px;
				height: 110px;
				text-align: center;
				vertical-align: bottom;
			}
			.cred_foto img {
				width: 110px;
				height: 110px;
			}
			.cred_fecha_creacion {
				width: 140px;
				height: 16px;
				font-family: Arial;
				font-size: 9px;
				color: #000;
			    text-align: center;
			}

			.cred_info {
				height: 12px;
				padding-bottom: 2px;
				font-family: Arial;
				font-size: 12px;
			    color: #000;
			    text-align: left;
			}
			.cred_info_funcion {
				font-size: 25px;
				padding-top: 5px;
				padding-bottom: 3px;
			}
			.cred_info_periodo {
				font-size: 12px;
				padding-bottom: 2px;
			}
			.cred_leyenda_apellido_nombre {
				height: 7px;
				font-size: 7px;
				padding-bottom: 0;
				margin-bottom: 0;
			}
			.cred_altura_valor_apellido_nombres {
				height: 0;
				padding-top: 0;
				margin-top: 0;
			}
			.cred_info_espacio_forzado_inferior {
				height: 45px;
			}
			.cred_texto_negrita {
				font-weight: bold;
			}
		</style>
		<page backleft="20mm">
			<?php
			$cantidad_concejales = count($info['gcc_concejales']);

			// Por cada Concejal
			for ($c=0; $c < $cantidad_concejales; $c++)
			{
				// Se toma su legajo
				$legajo_concejal = &$info['gcc_concejales'][$c];

				// Se obtiene su información (apellido, nombre, dni, etc)
				$info_legajo = $this->modelo->obtenerDatosLegajo($legajo_concejal);

				// Se toma el año de inicio de su período
				$anio_inicio_concejal = &$info['gcc_anios_inicio'][$c];
				// Se toma el año de finalización de su período
				$anio_fin_concejal = &$info['gcc_anios_fin'][$c];

				// Se toma el nombre de su foto, en caso que posea
				$foto_legajo = ($info_legajo['p_foto']) ? utf8_decode($info_legajo['p_foto']) : 'avatar.jpg';
			?>
				<table class="cred_contenedora">
					<tr>
				        <td class="cred_contenedor_dorso">

				        	<table>
					        	<tr>
									<td class="cred_titulo_superior">HONORABLE CONCEJO DELIBERANTE</td>
								</tr>
								<tr>
									<td class="cred_dorso_escudo">
										<br><br><img src="/var/www/sgl/personal/imagenes/credencial/escudo_dorso_64x75.png" />
				        			</td>
								</tr>
								<tr>
									<td class="cred_dorso_titulo_inferior">MUNICIPALIDAD DEL PARTIDO<br>DE GENERAL PUEYRREDON</td>
								</tr>
				        	</table>

				        </td>
				        <td class="cred_margen_para_doblez">&nbsp;</td>
				        <td class="cred_contenedor_frente">

				        	<table>
					        	<tr>
									<td class="cred_titulo_superior">HONORABLE CONCEJO DELIBERANTE</td>
								</tr>
								<tr>
									<td class="cred_contenedor_foto_e_info">

										<table>
											<tr>
												<td class="cred_contenedor_foto">

													<table>
														<tr>
															<td class="cred_foto">
																<img src="<?php echo $this->directorio_fotos.$foto_legajo; ?>" />
															</td>
														</tr>
														<tr>
															<td class="cred_fecha_creacion">
																Mar del Plata, <span class="cred_texto_negrita"><?php echo $fecha_creacion_credenciales; ?></span>
															</td>
														</tr>
													</table>

												</td>
												<td class="cred_contenedor_info">

													<table>
														<tr>
															<td class="cred_info cred_info_funcion">CONCEJAL</td>
														</tr>
														<tr>
															<td class="cred_info cred_info_periodo cred_texto_negrita">PER&Iacute;ODO: <?php echo $anio_inicio_concejal.' - '.$anio_fin_concejal; ?></td>
														</tr>
														<tr>
															<td class="cred_info cred_leyenda_apellido_nombre">APELLIDO/S</td>
														</tr>
														<tr>
															<td class="cred_info cred_altura_valor_apellido_nombres"><span class="cred_texto_negrita"><?php echo strtoupper($info_legajo['p_apellido']); ?></span></td>
														</tr>
														<tr>
															<td class="cred_info cred_leyenda_apellido_nombre">NOMBRE/S</td>
														</tr>
														<tr>
															<td class="cred_info cred_altura_valor_apellido_nombres"><span class="cred_texto_negrita"><?php echo strtoupper($info_legajo['p_nombre']); ?></span></td>
														</tr>
														<tr>
															<td class="cred_info">D.N.I. : <span class="cred_texto_negrita"><?php echo number_format($info_legajo['p_nro_documento'], 0, '', '.'); ?></span></td>
														</tr>
														<tr>
															<td class="cred_info_espacio_forzado_inferior">&nbsp;<br></td>
														</tr>
													</table>

												</td>
											</tr>
										</table>

									</td>
								</tr>
				        	</table>

				        </td>
				    </tr>
				</table>
			<?php
			}
			?>
		</page>
		<?php
		$contenido_a_convertir = ob_get_clean();

		try
		{
			// conversion HTML => PDF
			//los métodos utilizados aquí están definidos en el archivo html2pdf.class.php
			$html2pdf = new HTML2PDF('P','A4','es');
			$html2pdf->pdf->SetDisplayMode('real');
			$html2pdf->setDefaultFont('times');

			//Tratamiento del código HTML
			$html2pdf->WriteHTML($contenido_a_convertir);

			//Destino donde enviar el documento
			$html2pdf->Output($nombre_archivo_credenciales, 'D');
		}
		catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}

	/**
	 * 31/07/2019 XXXX
	 * Se solicita información para generar la credencial de un legajo determinado
	 *
	 * @param array $datos
	 */
	public function elegirDefensoresPuebloParaCredenciales($listadoDefensoresPueblo)
	{
		$cant_listado = ($listadoDefensoresPueblo) ? count($listadoDefensoresPueblo) : 0;
	?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

		<div id="precarga_modal" style="display:none"></div>
		 <div id="contenedoraSeleccionDefensoresParaCredenciales" class="prestamo_edicion_texto">

			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>

			<form action="informes/index.php" method="POST" name="formCredencialesParaDefensores" id="formCredencialesParaDefensores">

		        <input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
			    <input type="hidden" name="accion" id="accion" value="generarCredencialesParaDefensoresPueblo" />

				<div id="dragger_credenciales" class="degradado">
					Seleccione los Defensores del Pueblo para generar sus Credenciales
				</div>
				<div class="gcc_credencial_contenedora">
					<div class="gcc_contenedora_listado_concejales">
						<div class="gcc_listado_concejales">
							<?php
							if ( $cant_listado > 0 )
							{
							?>
								<div class="gcc_fila_concejal">
									<div class="gcc_checkbox_y_nombre checkbox_con_texto gcc_texto">
										<label>
											<input type="checkbox" name="i_concejales_todos" id="i_concejales_todos" value="" onClick="javascript:marcar_desmarcar_checkboxConcejales('i_concejales_todos', 'formCredencialesParaDefensores');" />&nbsp;Todos
										</label>
									</div>
									<div class="gcc_periodo gcc_texto">&nbsp;</div>
								</div>
								<?php
								for ($c=0; $c < $cant_listado; $c++)
								{
									$defensor = &$listadoDefensoresPueblo[$c];

									// Se obtiene información del último cargo del Defensor
									$info_cargo = $this->modelo->obtenerUltimoCargo($defensor['p_legajo']);

									// Se separa la fecha de alta del Defensor
									$aux = explode("-", $info_cargo['c_fecha_alta']);

									// Se asignan los años para el período respectivo
									$anio_inicio = $aux[0];
									$anio_fin    = $anio_inicio + 5;
								?>
									<div class="gcc_fila_concejal">
										<div class="gcc_checkbox_y_nombre checkbox_con_texto gcc_texto">
											<label>
												<input type="checkbox" name="gcc_concejales[]" id="i_concejal_<?php echo $c; ?>" value="<?php echo $defensor['p_legajo']; ?>" onclick="javascript:controlarFilaConcejal(this, '<?php echo $c; ?>');" />&nbsp;<?php echo $defensor['p_apellido'].', '.$defensor['p_nombre']; ?>
											</label>
										</div>
										<div class="gcc_periodo gcc_texto">
											<input type="text" name="gcc_anios_inicio[]" id="i_anio_inicio_<?php echo $c; ?>" value="<?php echo $anio_inicio; ?>" onkeypress="javascript:return soloEnteros(event);" disabled />
											&nbsp;-&nbsp;
											<input type="text" name="gcc_anios_fin[]" id="i_anio_fin_<?php echo $c; ?>" value="<?php echo $anio_fin; ?>" onkeypress="javascript:return soloEnteros(event);" disabled />
										</div>
									</div>
								<?php
								}
							}
							?>
						</div>

						<div class="gcc_contenedora_fecha_creacion degradado">
							Fecha de creaci&oacute;n:&nbsp;
							<select id="gc_fecha_creacion_mes" name="gc_fecha_creacion_mes">
								<option value="1">Enero</option>
								<option value="2">Febrero</option>
								<option value="3">Marzo</option>
								<option value="4">Abril</option>
								<option value="5">Mayo</option>
								<option value="6">Junio</option>
								<option value="7">Julio</option>
								<option value="8">Agosto</option>
								<option value="9">Septiembre</option>
								<option value="10">Octubre</option>
								<option value="11">Noviembre</option>
								<option value="12">Diciembre</option>
							</select>
							&nbsp;-&nbsp;
							<input type="text" id="gc_fecha_creacion_anio" name="gc_fecha_creacion_anio" value="<?php echo date("Y"); ?>" maxlength="4" />
						</div>

					</div>
					<div class="gcc_contenedora_botones">
						<div class="p_boton_edicion gcc_boton">
							<a id="btGenerarCredenciales" href="javascript:generarCredencialesParaDefensores();">
								<img src="imagenes/barra/pdf.jpg" width="15" height="15" />&nbsp;&nbsp;&nbsp;Generar
							</a>
						</div>
						<div class="p_boton_edicion gcc_boton">
							<a id="i_btCerrar">
								<img src="imagenes/barra/error_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Cerrar
							</a>
						</div>
					</div>
				</div>

			</form>
		</div>
		<script type="text/javascript">

			function generarCredencialesParaDefensores()
			{
				var error = false;
				var mensaje = "";

				// En caso que no se haya seleccionado NINGÚN Defensor del Pueblo
				if ( !verificarCheckbox('formCredencialesParaDefensores') )
				{
					error = true;
					mensaje += "Debe seleccionar por lo menos un Defensor del Pueblo.";
				}

				if (error)
				{
					alert(mensaje);
				}
				else
				{
					$('formCredencialesParaDefensores').submit();
				}
			}

			$('i_btCerrar').addEvent('click', function()
			{
				$("capaFondo").setStyle('visibility','hidden');
				$("capaVentana").setStyle('visibility','hidden');
			});

			var menuDrag = new Drag.Move($('contenedoraSeleccionDefensoresParaCredenciales'), {
			   handle: $('dragger_credenciales')
			});
		</script>
	<?php
	}

	/**
	 * 31/07/2019 XXXX
	 * Se generan las credenciales en lote, para todos los Concejales, en formato pdf, TAMAÑO 614x199 px
	 *
	 * @param array $datos
	 */
	public function generarCredencialesDefensoresPueblo($info)
	{
		ob_start();

		$fecha_creacion_credenciales = $this->nombreMes($info['gcc_fecha_creacion_mes']).' '.$info['gcc_fecha_creacion_anio'];

		$nombre_archivo_credenciales = "credenciales_defensores_".$this->nombreMes($info['gcc_fecha_creacion_mes'])."_".$info['gcc_fecha_creacion_anio'].".pdf";
	?>
		<style type="text/css">
			table {
				border-collapse: collapse;
				border: none;
			}
			td {
				padding: 0;
			}
			.fondo_aqua {
				background-color: aqua;
			}
			.cred_contenedora {
				width: 614px;
				height: 199px;
				background-image: url(/var/www/sgl/personal/imagenes/credencial/fondo_nuevo.jpg);
				margin-top: 10px;
			}

			.cred_contenedor_dorso {
				width: 306px;
				height: 199px;
			}
			.cred_margen_para_doblez {
				width: 2px;
				height: 199px;
				background-color: #fff;
			}
			.cred_contenedor_frente {
				width: 306px;
				height: 199px;
			}

			.cred_titulo_superior {
				width: 306px;
				height: 14px;
				padding-top: 5px;
				font-size: 12px;
				font-weight: bold;
				text-align: center;
			    color: #fff;
			}

			.cred_dorso_escudo {
				height: 135px;
				text-align: center;
			}
			.cred_dorso_escudo img {
				width: 64px;
				height: 75px;
				border: 0;
			}
			.cred_dorso_titulo_inferior {
				height: 35px;
				font-size: 10px;
				font-weight: bold;
				text-align: center;
			    color: #000;
			}

			.cred_contenedor_foto_e_info {
				width: 306px;
				height: 170px;
			}

			.cred_contenedor_foto {
				width: 140px;
				height: 160px;
			}
			.cred_contenedor_info {
				width: 160px;
				height: 120px;
				vertical-align: top;
				padding-top: 15px;
			}

			.cred_foto {
				width: 140px;
				height: 110px;
				text-align: center;
				vertical-align: bottom;
			}
			.cred_foto img {
				width: 110px;
				height: 110px;
			}
			.cred_fecha_creacion {
				width: 140px;
				height: 16px;
				font-family: Arial;
				font-size: 9px;
				color: #000;
			    text-align: center;
			}

			.cred_info {
				height: 12px;
				padding-bottom: 2px;
				font-family: Arial;
				font-size: 12px;
			    color: #000;
			    text-align: left;
			}
			.cred_info_funcion {
				font-size: 25px;
				padding-top: 5px;
				padding-bottom: 3px;
			}
			.cred_info_funcion_defensor_del_pueblo {
				padding-top: 0;
				padding-bottom: 3px;
				font-weight: bold;
			}
			.cred_info_periodo {
				font-size: 12px;
				padding-bottom: 2px;
			}
			.cred_leyenda_apellido_nombre {
				height: 7px;
				font-size: 7px;
				padding-bottom: 0;
				margin-bottom: 0;
			}
			.cred_altura_valor_apellido_nombres {
				height: 0;
				padding-top: 0;
				margin-top: 0;
			}
			.cred_info_espacio_forzado_inferior {
				height: 45px;
			}
			.cred_texto_negrita {
				font-weight: bold;
			}
		</style>
		<page backleft="20mm">
			<?php
			$cantidad_defensores = count($info['gcc_concejales']);

			// Por cada Concejal
			for ($c=0; $c < $cantidad_defensores; $c++)
			{
				// Se toma su legajo
				$legajo_concejal = &$info['gcc_concejales'][$c];

				// Se obtiene su información (apellido, nombre, dni, etc)
				$info_legajo = $this->modelo->obtenerDatosLegajo($legajo_concejal);

				// Se toma el año de inicio de su período
				$anio_inicio_concejal = &$info['gcc_anios_inicio'][$c];
				// Se toma el año de finalización de su período
				$anio_fin_concejal = &$info['gcc_anios_fin'][$c];

				// Se toma el nombre de su foto, en caso que posea
				$foto_legajo = ($info_legajo['p_foto']) ? utf8_decode($info_legajo['p_foto']) : 'avatar.jpg';
			?>
				<table class="cred_contenedora">
					<tr>
				        <td class="cred_contenedor_dorso">

				        	<table>
					        	<tr>
									<td class="cred_titulo_superior">HONORABLE CONCEJO DELIBERANTE</td>
								</tr>
								<tr>
									<td class="cred_dorso_escudo">
										<br><br><img src="/var/www/sgl/personal/imagenes/credencial/escudo_dorso_64x75.png" />
				        			</td>
								</tr>
								<tr>
									<td class="cred_dorso_titulo_inferior">MUNICIPALIDAD DEL PARTIDO<br>DE GENERAL PUEYRREDON</td>
								</tr>
				        	</table>

				        </td>
				        <td class="cred_margen_para_doblez">&nbsp;</td>
				        <td class="cred_contenedor_frente">

				        	<table>
					        	<tr>
									<td class="cred_titulo_superior">HONORABLE CONCEJO DELIBERANTE</td>
								</tr>
								<tr>
									<td class="cred_contenedor_foto_e_info">

										<table>
											<tr>
												<td class="cred_contenedor_foto">

													<table>
														<tr>
															<td class="cred_foto">
																<img src="<?php echo $this->directorio_fotos.$foto_legajo; ?>" />
															</td>
														</tr>
														<tr>
															<td class="cred_fecha_creacion">
																Mar del Plata, <span class="cred_texto_negrita"><?php echo $fecha_creacion_credenciales; ?></span>
															</td>
														</tr>
													</table>

												</td>
												<td class="cred_contenedor_info">

													<table>
														<tr>
															<td class="cred_info">
																<span class="cred_info_funcion_defensor_del_pueblo">DEFENSOR DEL PUEBLO</span>
																<br>
																<span class="cred_leyenda_apellido_nombre">DEL PARTIDO DE GENERAL PUEYRREDON</span>
															</td>
														</tr>
														<tr>
															<td class="cred_info cred_info_periodo cred_texto_negrita">PER&Iacute;ODO: <?php echo $anio_inicio_concejal.' - '.$anio_fin_concejal; ?></td>
														</tr>
														<tr>
															<td class="cred_info cred_leyenda_apellido_nombre">APELLIDO/S</td>
														</tr>
														<tr>
															<td class="cred_info cred_altura_valor_apellido_nombres"><span class="cred_texto_negrita"><?php echo strtoupper($info_legajo['p_apellido']); ?></span></td>
														</tr>
														<tr>
															<td class="cred_info cred_leyenda_apellido_nombre">NOMBRE/S</td>
														</tr>
														<tr>
															<td class="cred_info cred_altura_valor_apellido_nombres"><span class="cred_texto_negrita"><?php echo strtoupper($info_legajo['p_nombre']); ?></span></td>
														</tr>
														<tr>
															<td class="cred_info">D.N.I. : <span class="cred_texto_negrita"><?php echo number_format($info_legajo['p_nro_documento'], 0, '', '.'); ?></span></td>
														</tr>
													</table>

												</td>
											</tr>
										</table>

									</td>
								</tr>
				        	</table>

				        </td>
				    </tr>
				</table>
			<?php
			}
			?>
		</page>
		<?php
		$contenido_a_convertir = ob_get_clean();

		try
		{
			// conversion HTML => PDF
			//los métodos utilizados aquí están definidos en el archivo html2pdf.class.php
			$html2pdf = new HTML2PDF('P','A4','es');
			$html2pdf->pdf->SetDisplayMode('real');
			$html2pdf->setDefaultFont('times');

			//Tratamiento del código HTML
			$html2pdf->WriteHTML($contenido_a_convertir);

			//Destino donde enviar el documento
			$html2pdf->Output($nombre_archivo_credenciales, 'D');
		}
		catch(HTML2PDF_exception $e) {
			echo $e;
			exit;
		}
	}

}

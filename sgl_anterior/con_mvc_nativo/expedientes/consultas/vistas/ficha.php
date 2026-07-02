<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaFicha extends VistaBase
{
    private $controlador;
    
    public function __construct()
    {
		$this->controlador = 'ficha';
    }
    
    public function array_envia($array, $nombre) 
	{
		//se serializa el vector de datos recibido
		$tmp = serialize($array);
		
		//Devuelve una cadena en la que todos los caracteres no-alfanumericos excepto -_. han sido 
		//reemplazados con un signo de porcentaje (%) seguido por dos digitos hexadecimales y los 
		//espacios son codificados como signos de suma (+)	
		$tmp = urlencode($tmp);
		
		$hora_min = explode(':', date("H:i"));
		$hora = $hora_min[0];
		$min = $hora_min[1];
		
		//se compone el nombre del txt a crear
		$archivo_para_pdf = $nombre.'_pdf_'.$_SESSION['usuario'].'_'.$_SESSION['fecha'].'_'.$hora.$min;
		//se crea el .txt y se guarda la serializacion del vector
		fputs(fopen($archivo_para_pdf.'.txt','w'),print_r($tmp, true));

		return $archivo_para_pdf;
    } 
	    
    // SE GENERA AUTOMATICAMENTE EL ENCABEZADO Y EL PIE DE CADA PAGINA DEL REPORTE
    public function encabezado_y_pie()
    {
    ?>
		<page backtop="47mm" backbottom="10mm">
			<page_header>
			<table class="pdf_gral_impresion">
				<tr>
					<td class="pdf_encab_logo">
						<img src="../imagenes/escudo_cuatro_colores.gif" width="145" height="170" border="0" align="center">
					</td>
					<td class="pdf_encab_titulo">
						Municipalidad de Gral. Pueyrredon<br><br><br>
						<strong>Honorable Concejo Deliberante</strong>
					</td>
					<td class="pdf_encab_titulo2">
						Sistema de Expedientes<br>
					</td>
				</tr>
			</table>
			</page_header>
			<page_footer>
			<table class="pdf_gral_impresion pdf_linea_superior">		
				<tr>
					<td class="pdf_pie_fecha_y_usr">
						<?php echo date("d/m/Y"); ?>&nbsp;&nbsp;<span id="reloj"></span>
					</td>
					<td class="pdf_pie_pagina_y_pc">P&aacute;gina [[page_cu]] de {nb}</td>
				</tr>
				<tr>
					<td class="pdf_pie_fecha_y_usr">
						USR. <?php echo $_SESSION['usuario']; ?>
					</td>
					<td class="pdf_pie_pagina_y_pc">PC: <?php echo gethostbyaddr($_SERVER['REMOTE_ADDR']); ?></td>
				</tr>
			</table>
			</page_footer>
		</page>
    <?php
    }
/******************************************************************************************************************
	ENCABEZADO DEL REPORTE EN FORMATO PARA IMPRIMIR
******************************************************************************************************************/	
    private function encabezado_reporte()
    {
    ?>  
		<style type="text/css">
		.imp_encabezado{
			height:122px;
			border:2px solid #000;
			font-family: Arial;
		}
		.imp_encabezado_logo{
			width:167px;
			height:122px;
			float:left;
		}
		.imp_encabezado_titulos{
			width:470px;
			height:122px;
			float:left;
		}
		.imp_encabezado_titulo1{
			width:470px;
			padding-top:20px;
			text-align:left;
			font-size:14px;
			font-weight:700;
		}
		.imp_encabezado_titulo2{
			width:470px;
			padding-top:10px;
			font-size:14px;
			text-align:right;
		}
		.imp_encabezado_titulo3{
			width:470px;
			padding-top:10px;
			text-align:left;
			font-size:17px;
			font-weight:700;
		}
		</style>
		<div class="imp_encabezado">
			<div class="imp_encabezado_logo"><img src="../imagenes/escudo_cuatro_colores.gif" width="102" height="119" align=center></div>
			<div class="imp_encabezado_titulos">
				<div class="imp_encabezado_titulo1">Municipalidad de General Pueyrred&oacute;n</div>
				<div class="imp_encabezado_titulo2">Sistema de Expedientes</div>
				<div class="imp_encabezado_titulo3">Honorable Concejo Deliberante</div>
			</div>
		</div>
    <?php
    }	
/******************************************************************************************************************
	PIE DEL REPORTE EN FORMATO PARA IMPRIMIR
******************************************************************************************************************/
    private function pie_reporte()
    {
    ?>
		<style type="text/css">
			.imp_texto {
				font-family: Arial;
				font-size: 12px;
			}
			.imp_pie{
				height:41px;
				clear:both;
				border-left:2px solid #000;
				border-right:2px solid #000;
				border-bottom:2px solid #000;
			}
			.imp_datos_izq{
				width:265px;
				height:37px;
				padding:5px 0 0 5px;
				float:left;

			}
			.imp_datos_der{
				width:265px;
				height:37px;
				padding:5px 5px 0 0;
				float:right;
				text-align:right
			}
		</style>
		<div class="imp_texto imp_pie">
			<div class="imp_datos_izq"><?php echo date("d/m/Y"); ?>&nbsp;&nbsp;<span id="reloj"></span><br>USR. <?php echo $_SESSION['usuario']; ?></div>
			<div class="imp_datos_der"><br>PC: <?php echo gethostbyaddr($_SERVER['REMOTE_ADDR']); ?></div>
		</div>
    <?php  
    }
/******************************************************************************************************************
     SE GENERA EL REPORTE DE Consulta General EN FORMATO .doc PARA PROCESAR SU CONTENIDO
******************************************************************************************************************/	
    public function generar_ficha($datos_ficha = '', $filtro = '')
    {
    	header("Content-Type: text/html; charset=UTF-8");
    ?>
		<style type="text/css">
			.imp_texto {
				font-family: Arial;
				font-size: 12px;
			}
			.imp_titulos_de_clave{
				height:25px;
				border-left:2px solid #000;
				border-right:2px solid #000;
				clear:both;
			}
			.imp_titulos_de_clave_izq{
				width:395px;
				height:20px;
				padding:5px 0 0 5px;
				float:left;
				font-size:14px;
				font-weight:700;
			}
			.imp_titulos_de_clave_der{
				width:125px;
				height:20px;
				padding:5px 5px 0 0;
				float:right;
				text-align:right;
				font-size:14px;
			}
			.imp_ficha_titulos_de_clave{
				height:25px;
				border-top:2px solid #000;
			}
			.imp_ficha_titulos_de_clave_izq{
				width:537px;
				height:20px;
				padding:5px 0 0 5px;
				float:left;
				font-size:14px;
				font-weight:700;
			}
			/************************************************************
				PARA LA FICHA DEL EXPEDIENTE
			************************************************************/
			.imp_bordes{
				border-left:2px solid #000;
				border-right:2px solid #000;
				border-bottom:2px solid #000;
			}
			.imp_ficha{
				min-height: 20px;
				padding: 2px 0 2px 0;
				font-size: 12px;
				clear: both;
			}
			.imp_ficha_nombre {
				width: 107px;
				min-height: 20px;
				padding: 2px 0 2px 0;
				padding-left: 5px;
				float: left;
				font-weight: 700;
			}
			.imp_ficha_valor{
				width: 250px;
				min-height: 20px;
				padding: 2px 0 2px 0;
				float: left;
			}
			.imp_ficha_valor_mas_largo{
				width:370px;
			}
			.imp_ficha_valor_temas_autores {
				width: 600px;
			}
			.imp_ficha_valor_caratula {
				padding: 0 5px 0 0;
				height: 20px;
				float: left;
			}
			.imp_ficha_valor_orden_proyecto{
				width:20px;
				height:20px;
				float:right;
			}
			.imp_ficha_extracto{
				padding:5px 0 5px 0;
				font-size:12px;
				clear:both;
			}
			.imp_ficha_nombre_extracto{
				width:145px;
				padding:5px 0 5px 5px;
				float:left;
			}
			.imp_ficha_valor_extracto{
				width:550px;
				padding:5px 0 5px 0;
				float:left;
			}
			.imp_ficha_fecha_estado{
				width:140px;
				height:20px;
				padding-right:10px;
				text-align:right;
				float:right;
			}
			/**************************************
				PARA LOS GIROS
			**************************************/
			.imp_ficha_giro_margen{
				width:20px;
				height:20px;
				padding-left:5px;
				float:left;
			}
			.imp_ficha_giro_comision_codigo{
				width:50px;
				height:20px;
				padding-left:5px;
				float:left;
			}
			.imp_ficha_giro_descripcion_grp{
				width:270px;
				height:20px;
				float:left;
			}
			.imp_ficha_giro_fechas{
				width:100px;
				height:20px;
				float:left;
			}
			.imp_ficha_giro_dictamen_giro{
				width:80px;
				height:20px;
				float:left;
			}
			/**************************************
				PARA LOS ANTECEDENTES
			**************************************/
			.imp_ficha_antecedente_margen{
				width:20px;
				height:20px;
				padding-left:5px;
				float:left;
			}
			.imp_ficha_antecedente_tipo{
				width:50px;
				height:20px;
				float:left;
			}
			.imp_ficha_antecedente_numero{
				width:70px;
				height:20px;
				float:left;
			}
			.imp_ficha_antecedente_anio{
				width:50px;
				height:20px;
				float:left;
			}
			.imp_ficha_antecedente_cuerpo{
				width:50px;
				height:20px;
				float:left;
			}
			.imp_ficha_antecedente_alcance{
				width:50px;
				height:20px;
				float:left;
			}
			.imp_ficha_antecedente_observaciones{
				width:570px;
				height:20px;
				float:left;
			}
			#btImprimir {
				text-align:center;
			}
		</style>
		<style media="print" type="text/css">
			#btImprimir {
				display:none;
			}
		</style>
			
		<?php $this->encabezado_reporte(); ?>
		
		<div class="imp_texto imp_titulos_de_clave">
			<div class="imp_titulos_de_clave_izq">A&ntilde;o&nbsp;&nbsp;&nbsp;T.&nbsp;&nbsp;Nro.&nbsp;&nbsp;Cpo.&nbsp;Alc.</div>
			<div class="imp_titulos_de_clave_der">Fecha Ingreso</div>
		</div>

		<div class="imp_bordes">
		
			<?php $ficha = &$datos_ficha[0]; ?>

			<div class="imp_texto imp_ficha_titulos_de_clave" >
				<div class="imp_ficha_titulos_de_clave_izq"><?php echo $ficha['anio']; ?>&nbsp;<?php echo $ficha['tipo']; ?>&nbsp;<?php echo $ficha['numero']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['cuerpo']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['alcance']; ?></div>
				<div class="imp_titulos_de_clave_der"><?php echo $this->formatearFecha($ficha['fecha_entrada_expe']); ?></div>
			</div>
			<div class="imp_texto imp_ficha">	
				<div class="imp_ficha_nombre">Car&aacute;tula:</div>
				<div class="imp_ficha_valor_caratula"><?php echo $ficha['caratula']; ?></div>
			</div>
			<div class="imp_texto imp_ficha">	
				<div class="imp_ficha_nombre">Iniciador:</div>
				<div class="imp_ficha_valor "><?php echo $ficha['iniciador']; ?></div>
			</div>
			<div class="imp_texto imp_ficha">	
				<div class="imp_ficha_nombre">Categor&iacute;a:</div>
				<div class="imp_ficha_valor"><?php echo $ficha['categoria']; ?></div>
			</div>
			<div class="imp_texto imp_ficha">	
				<div class="imp_ficha_nombre">Temas:</div>
				<div class="imp_ficha_valor_caratula">
				<?php 
				  $cantidad_temas = count($datos_ficha['temas']);
				  for ($t=0; $t < $cantidad_temas; $t++)
				  {
					$tema = &$datos_ficha['temas'][$t];
					echo $tema['descripcion_tema']; 
					if ( $t != ($cantidad_temas - 1) ) echo '<b>;</b><br> ';
				  }
				?>
				</div>
			</div>
			
			<div class="imp_texto imp_ficha">	
				<div class="imp_ficha_nombre">Autores:</div>
				<div class="imp_ficha_valor_caratula">
					<?php 
					$cantidad_autores = count($datos_ficha['autores']);
					for ($a=0; $a < $cantidad_autores; $a++)
					{
						$autor = &$datos_ficha['autores'][$a];
						echo $autor['descripcion_grp']; 
						if ( $a != ($cantidad_autores - 1) ) echo '<b>;</b><br> ';
					}
					?>
				</div>
			</div>	
			
			<?php
			$cantidad_proy = count($datos_ficha['proyectos']);
			for ($p=0; $p < $cantidad_proy; $p++)
			{
				$proyecto = &$datos_ficha['proyectos'][$p];
			?>
				  <div class="imp_texto imp_ficha">	
					  <div class="imp_ficha_nombre">Proyecto de</div>
					  <div class="imp_ficha_valor"><?php echo $proyecto['descripcion_proyecto']; ?></div>
				  </div>			
				  <div class="imp_texto imp_ficha">	
					  <div class="imp_ficha_nombre">Extracto:</div>
					  <div class="imp_ficha_valor_extracto"><?php echo $proyecto['extracto']; ?></div>
				  </div>	
			<?php  
			}
			?>

			<div class="imp_texto imp_ficha">	
				<div class="imp_ficha_nombre">Estado / Desde</div>
				<div class="imp_ficha_valor imp_ficha_valor_mas_largo">
				<?php 
					$estado = &$datos_ficha['estado'][0];
					echo $estado['nombre_estado'];
				?>
				</div>
				<div class="imp_ficha_fecha_estado"><?php echo $this->formatearFecha($estado['fecha_estado']); ?></div>
			</div>

			<?php 
			// SI SE ENCUENTRA AGREGADO A OTRO EXPEDIENTE
			if ( $ficha['agregado_anio'] != 0 )
			{
			?>
			  <div class="imp_texto imp_ficha">	
				  <div class="imp_ficha_nombre">Agregado a:</div>
				  <div class="imp_ficha_valor">
					<?php echo   $ficha['agregado_anio']."&nbsp;&nbsp;".$ficha['agregado_tipo']."&nbsp;&nbsp;&nbsp;&nbsp;".$ficha['agregado_numero']."&nbsp;&nbsp;".$ficha['agregado_cuerpo']."&nbsp;&nbsp;".$ficha['agregado_alcance']; ?>
				  </div>
			  </div>			
			<?php
			}	 

			// SI POSEE Observación
			if ( $ficha['observaciones_expe'] != '' )
			{
			?>
				<div class="imp_texto imp_ficha">	
					<div class="imp_ficha_nombre">Observaciones:</div>
					<div class="imp_ficha_valor_extracto"><?php echo $ficha['observaciones_expe']; ?></div>
				</div>
			<?php
			}	
			?>
			<?php
			// SI EXISTE ALGUN GIRO, SE MUESTRA
			if ( isset($datos_ficha['giros'][0]['comision_codigo']) )
			{
			?>
				<div class="imp_texto imp_ficha">	
					<div class="imp_ficha_nombre">Giros:</div>
				</div>
				
				<?php 
				$cantidad_giros = count($datos_ficha['giros']);
				for ($g=0; $g < $cantidad_giros; $g++)
				{
					$giro = &$datos_ficha['giros'][$g];
				?>
					<div class="imp_ficha">
						&nbsp;&nbsp;
						<?php echo $giro['comision_codigo']; ?>&nbsp;&nbsp;
						<?php echo $giro['comision']; ?>&nbsp;&nbsp;
						<?php echo ($giro['fecha_entrada_giro']) ? $this->formatearFecha($giro['fecha_entrada_giro']) : '---'; ?>&nbsp;&nbsp;
						<?php echo ($giro['fecha_salida_giro']) ? $this->formatearFecha($giro['fecha_salida_giro']) : '---'; ?>&nbsp;&nbsp;
						<?php echo $giro['dictamen_giro']; ?>
					</div>
			<?php 
				}
			}
			?>

			<?php
			// SI EXISTE ALGUN ANTECEDENTE, SE MUESTRA
			if (isset($datos_ficha['antecedentes'][0]['tipo']))
			{
			?>
				<div class="imp_texto imp_ficha">	
					<div class="imp_ficha_nombre">Antecedentes:</div>
				</div>
				<div class="imp_ficha" style="border-top:1px solid #000;font-size:11px;">
					<div class="imp_ficha_antecedente_margen">&nbsp;</div>
					<div class="imp_ficha_antecedente_numero" style="width:55px;text-align:center;border-right:1px solid #000;">N&uacute;mero</div>
					<div class="imp_ficha_antecedente_tipo" style="width:25px;text-align:center;padding-left:5px;border-right:1px solid #000">Tipo</div>
					<div class="imp_ficha_antecedente_anio" style="width:30px;text-align:center;padding-left:5px;border-right:1px solid #000">A&ntilde;o</div>
					<div class="imp_ficha_antecedente_cuerpo" style="width:25px;padding-left:5px;border-right:1px solid #000">Dig.</div>
					<div class="imp_ficha_antecedente_cuerpo" style="width:25px;padding-left:5px;border-right:1px solid #000">Cpo.</div>
					<div class="imp_ficha_antecedente_alcance" style="width:25px;padding-left:5px;border-right:1px solid #000">Alc.</div>
					<div class="imp_ficha_antecedente_cuerpo" style="padding-left:5px;border-right:1px solid #000">Cpo.Alc.</div>
					<div class="imp_ficha_antecedente_cuerpo" style="width:45px;padding-left:5px;border-right:1px solid #000">An.Alc.</div>
					<div class="imp_ficha_antecedente_alcance" style="width:75px;padding-left:5px;border-right:1px solid #000">Cpo.An.Alc.</div>
					<div class="imp_ficha_antecedente_cuerpo" style="width:25px;padding-left:5px;border-right:1px solid #000">An.</div>
					<div class="imp_ficha_antecedente_cuerpo" style="padding-left:5px;border-right:1px solid #000">Cpo.An.</div>
					<div class="imp_ficha_antecedente_observaciones" style="width:235px;text-align:center;">Observaciones</div>
				</div>
				<?php
				$cant_antecedentes = count($datos_ficha['antecedentes']);
				for ($ant=0; $ant < $cant_antecedentes; $ant++)
				{
					$antecedente = &$datos_ficha['antecedentes'][$ant];
				?>
					<div class="imp_ficha" style="border-top:1px solid #000;font-size:11px;">
						<div class="imp_ficha_antecedente_margen">&nbsp;</div>
						<div class="imp_ficha_antecedente_numero" style="width:45px;text-align:right;padding-right:10px;border-right:1px solid #000"><?php echo $antecedente['numero_a']; ?></div>
						<div class="imp_ficha_antecedente_tipo" style="width:25px;padding-left:5px;border-right:1px solid #000"><?php echo $antecedente['tipo_a']; ?></div>
						<div class="imp_ficha_antecedente_anio" style="width:30px;padding-left:5px;border-right:1px solid #000"><?php echo $antecedente['anio_a']; ?></div>
						<div class="imp_ficha_antecedente_cuerpo" style="width:25px;padding-left:5px;border-right:1px solid #000"><?php echo $antecedente['digito_a']; ?></div>
						<div class="imp_ficha_antecedente_cuerpo" style="width:25px;padding-left:5px;border-right:1px solid #000"><?php echo $antecedente['cuerpo_a']; ?></div>
						<div class="imp_ficha_antecedente_alcance" style="width:25px;padding-left:5px;border-right:1px solid #000"><?php echo $antecedente['alcance_a']; ?></div>
						<div class="imp_ficha_antecedente_cuerpo" style="padding-left:5px;border-right:1px solid #000"><?php echo $antecedente['cuerpoalcance_a']; ?></div>
						<div class="imp_ficha_antecedente_cuerpo" style="width:45px;padding-left:5px;border-right:1px solid #000"><?php echo $antecedente['anexoalcance_a']; ?></div>
						<div class="imp_ficha_antecedente_alcance" style="width:75px;padding-left:5px;border-right:1px solid #000"><?php echo $antecedente['cuerpoanexoalcance_a']; ?></div>
						<div class="imp_ficha_antecedente_cuerpo" style="width:25px;padding-left:5px;border-right:1px solid #000"><?php echo $antecedente['anexo_a']; ?></div>
						<div class="imp_ficha_antecedente_cuerpo" style="padding-left:5px;border-right:1px solid #000"><?php echo $antecedente['cuerpoanexo_a']; ?></div>
						<div class="imp_ficha_antecedente_observaciones" style="width:235px;text-align:left;padding-left:5px;"><?php echo $antecedente['observaciones_antecedentes']; ?></div>
					</div>
				<?php 
				}
			}
			?>
		</div>
		
		<?php
		echo $this->pie_reporte();
		?>
		<br>
		<div id="btImprimir">
			<input name="imprimir" type="button" class="button" value="Imprimir" onClick="window.print();">
		</div>
	<?php	
    }

    public function generarEtiqueta_ficha($etiqueta = '', $filtro = '')
    {
    	header("Content-Type: text/html; charset=UTF-8");
    ?>
		<style type="text/css">
			.etq_gral {
			  width: 387px;
			  /**/
			  min-height: 370px;
			  /**/
			  padding: 2px 0 2px 0;
			  border: 1px solid #000;
			}
			.etq_gral_texto {
			  font-family: Arial;
			  font-size: 13px;
			  color: #000;
			}
			.etq_subrayado {
				text-decoration: underline;
				font-weight: 500;
			}
			.etq_fila_exped {
			  height: 20px;
			}
			.etq_fila_autores {
				min-height: 20px;
				padding: 2px 0 2px 0;
			}
			.etq_tit_exped_izq {
			  width: 215px;
			  height: 20px;
			  float: left;
			  padding-left: 5px;
			}
			.etq_tit_exped_der {
			  width: 160px;
			  height: 20px;
			  float: right;
			  text-align: left;
			}
			.etq_margen_interno {
			  padding:5px;
			}
			.etq_dato_exped_izq {
			  width:70px;
			  height:20px;
			  float:left;
			  text-align:left;
			  padding-left:5px;
			}
			.etq_dato_autores_izq {
				width: 70px;
				min-height: 20px;
				float:left;
				text-align: left;
				padding-left: 5px;
			}
			.etq_dato_exped_der {
			  width: 280px;
			  height: 20px;
			  float: left;
			  text-align: left;
			}
			.etq_dato_autores_der {
				width: 280px;
				min-height: 20px;
				float: left;
				text-align: left;
			}
			.etq_espacio_vertical {
				height:15px;
				font-size:0;
			}
			.etq_fila_antecedente {
				height: 14px;
				padding: 2px 0 2px 0;
				border-bottom: 1px solid #000;
				font-size: 11px;
				text-align: center;
			}
			.etq_fila_observaciones {
				height: 70px;
				padding: 2px 0 2px 0;
			}
			.etq_fila_titulo_observaciones {
				width: 93px;
				min-height: 70px;
				float: left;
				padding: 2px 0 2px 7px;	
				text-decoration: underline;
				font-weight: 500;
			}	
			.etq_fila_valor_observaciones {
				width: 280px;
				min-height: 70px;
				float: left;
				padding: 2px 0 2px 0;	
			}	
			#btImprimir {
				clear:both;
				display: display;
			}
		</style>

		<style media="print" type="text/css">
			#btImprimir {
				display: none;
			}
		</style>
		<?php 
		$ficha = &$etiqueta[0]; 
		?>
		<div class="etq_gral etq_gral_texto">
			<div class="etq_fila_exped">
				<div class="etq_tit_exped_izq">
					<b class="etq_subrayado">
					<?php
					if ($ficha['tipo'] == 'E')
					{
						echo "Expediente"; 
					}
					elseif ($ficha['tipo'] == 'N')
					{ 
						echo "Nota"; 
					}
					?>
					N&deg;:
					</b>
					<strong><?php echo $ficha['numero']; ?>-<?php echo $ficha['iniciador_codigo']; ?>-<?php echo $ficha['anio']; ?></strong>
				</div>
				<div class="etq_tit_exped_der">
					<b class="etq_subrayado">Fecha Ingreso:</b>
					<strong><?php echo $this->formatearFecha($ficha['fecha_entrada_expe']); ?></strong>
				</div>
			</div>

			<div class="etq_fila_exped etq_margen_interno"><strong><?php echo $ficha['caratula']; ?></strong></div>

			<div class="etq_fila_exped">
				<div class="etq_dato_exped_izq etq_subrayado">Iniciador:</div>
				<div class="etq_dato_exped_der"><?php echo $ficha['iniciador_codigo']; ?>&nbsp;&nbsp;&nbsp;<?php echo $ficha['iniciador']; ?></div>
			</div>

			<div class="etq_fila_autores">
				<div class="etq_dato_autores_izq etq_subrayado">Autores:</div>
				<div class="etq_dato_autores_der">
				<?php
				  $cantidad_autores = count($etiqueta['autores']);
				  for ($a=0; $a < $cantidad_autores; $a++)
				  {
					$autor = &$etiqueta['autores'][$a];
					echo $autor['autor_codigo'].'&nbsp;&nbsp;&nbsp;'.$autor['descripcion_grp']; 
					if ( $a != ($cantidad_autores - 1) ) echo '<b>;</b><br>';
				  }
				?>
				</div>
			</div>

			<div class="etq_fila_exped">
				<div class="etq_dato_exped_izq etq_subrayado">Categor&iacute;a:</div>
				<div class="etq_dato_exped_der"><?php echo $ficha['id_codcategoria']; ?>&nbsp;&nbsp;&nbsp;<?php echo $ficha['categoria']; ?></div>
			</div>

			<?php
			// SI POSEE Proyectos
			$cantidad_proy = count($etiqueta['proyectos']);
			for ($p=0; $p < $cantidad_proy; $p++)
			{
			  	$proyecto = &$etiqueta['proyectos'][$p];
			?>
			  	<div class="etq_fila_exped">
			  		<div class="etq_dato_exped_izq etq_subrayado">Proyecto&nbsp;<?php echo $proyecto['orden_proyecto']; ?></div>
			  		<div class="etq_dato_exped_der"><strong><?php echo $proyecto['descripcion_proyecto']; ?></strong></div>
			  	</div>
			  	<div class="etq_margen_interno" style="text-align:justify;"><strong><?php echo $proyecto['extracto']; ?></strong></div>
			<?php  
			}
			?>

			<div class="etq_espacio_vertical"></div>
			&nbsp;&nbsp;<b class="etq_subrayado">Antecedentes:</b>
			<?php
			// SI POSEE Antecedentes
			if ( isset($etiqueta['antecedentes'][0]['tipo']) )
			{
			?>
				<div class="etq_fila_antecedente" style="border-top:1px solid #000;">
					<div style="width:43px;height:17px;float:left;border-right:1px solid #000;">N&uacute;mero</div>
					<div style="width:23px;height:17px;float:left;border-right:1px solid #000;">Tipo</div>
					<div style="width:27px;height:17px;float:left;border-right:1px solid #000;">A&ntilde;o</div>
					<div style="width:21px;height:17px;float:left;border-right:1px solid #000;">Dig.</div>
					<div style="width:23px;height:17px;float:left;border-right:1px solid #000;">Cpo.</div>
					<div style="width:23px;height:17px;float:left;border-right:1px solid #000;">Alc.</div>
					<div style="width:50px;height:17px;float:left;border-right:1px solid #000;">Cpo.Alc.</div>
					<div style="width:43px;height:17px;float:left;border-right:1px solid #000;">An.Alc.</div>
					<div style="width:60px;height:17px;float:left;border-right:1px solid #000;">Cpo.An.Alc.</div>
					<div style="width:20px;height:17px;float:left;border-right:1px solid #000;">An.</div>
					<div style="width:40px;height:17px;float:left;">Cpo.An.</div>
				</div>
			<?php	
				$cant_antecedentes = count($etiqueta['antecedentes']);
				for ($ant=0; $ant < $cant_antecedentes; $ant++)
				{
					$antecedente = &$etiqueta['antecedentes'][$ant];
				?>
					<div class="etq_fila_antecedente">
						<div style="width:43px;height:17px;float:left;border-right:1px solid #000;"><?php echo $antecedente['numero_a']; ?></div>
						<div style="width:23px;height:17px;float:left;border-right:1px solid #000;"><?php echo $antecedente['tipo_a']; ?></div>
						<div style="width:27px;height:17px;float:left;border-right:1px solid #000;"><?php echo $antecedente['anio_a']; ?></div>
						<div style="width:21px;height:17px;float:left;border-right:1px solid #000;"><?php echo $antecedente['digito_a']; ?></div>
						<div style="width:23px;height:17px;float:left;border-right:1px solid #000;"><?php echo $antecedente['cuerpo_a']; ?></div>
						<div style="width:23px;height:17px;float:left;border-right:1px solid #000;"><?php echo $antecedente['alcance_a']; ?></div>
						<div style="width:50px;height:17px;float:left;border-right:1px solid #000;"><?php echo $antecedente['cuerpoalcance_a']; ?></div>
						<div style="width:43px;height:17px;float:left;border-right:1px solid #000;"><?php echo $antecedente['anexoalcance_a']; ?></div>
						<div style="width:60px;height:17px;float:left;border-right:1px solid #000;"><?php echo $antecedente['cuerpoanexoalcance_a']; ?></div>
						<div style="width:20px;height:17px;float:left;border-right:1px solid #000;"><?php echo $antecedente['anexo_a']; ?></div>
						<div style="width:40px;height:17px;float:left;"><?php echo $antecedente['cuerpoanexo_a']; ?></div>
					</div>	
				<?php 
				}
			}
			?>
			<div class="etq_espacio_vertical"></div>
			<div class="etq_fila_observaciones">
				<div class="etq_fila_titulo_observaciones">Observaciones:</div>
				<div class="etq_fila_valor_observaciones"><?php echo $ficha['observaciones_expe']; ?></div>
			</div>

		</div>
		<div id="btImprimir"><input name="imprimir" type="button" class="button" value="Imprimir" onClick="window.print();"></div>
    <?php
    }

    public function ver_ficha_modal($datos_ficha = '', $filtro = '')
    {
    ?>
		<div class="cerrar_pedirNombreModal">
			<div id="btCerrar_pedirNombreModal" class="btCerrar_pedirNombreModal"></div>
		</div>
		<div class="imp_ficha_margen_inferior_cerrar"></div>
		<div>
			<?php $ficha = &$datos_ficha[0]; ?>
			<div class="imp_ficha_titulos_de_clave">
				<div class="imp_ficha_titulos_de_clave_izq"><?php echo $ficha['anio']; ?>&nbsp;<?php echo $ficha['tipo']; ?>&nbsp;<?php echo $ficha['numero']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['cuerpo']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['alcance']; ?></div>
				<div class="imp_titulos_de_clave_der"><?php echo $this->formatearFecha($ficha['fecha_entrada_expe']); ?></div>
			</div>
			<div class="imp_ficha">	
				<div class="imp_ficha_nombre">Car&aacute;tula:</div>
				<div class="imp_ficha_valor imp_ficha_valor_mas_largo"><?php echo $ficha['caratula']; ?></div>
			</div>
			<div class="imp_ficha">	
				<div class="imp_ficha_nombre">Iniciador:</div>
				<div class="imp_ficha_valor "><?php echo $ficha['iniciador']; ?></div>
			</div>
			<div class="imp_ficha">	
				<div class="imp_ficha_nombre">Categor&iacute;a:</div>
				<div class="imp_ficha_valor"><?php echo $ficha['categoria']; ?></div>
			</div>
			<div class="imp_ficha">	
				<div class="imp_ficha_nombre">Temas:</div>
				<div class="imp_ficha_valor imp_ficha_valor_temas_autores">
				<?php 
				$cantidad_temas = count($datos_ficha['temas']);
				for ($t=0; $t < $cantidad_temas; $t++)
				{
					$tema = &$datos_ficha['temas'][$t];
					echo $tema['descripcion_tema']; 
					if ( $t != ($cantidad_temas - 1) ) echo '<b>;</b> ';
				}
				?>
				</div>
			</div>
			<div class="imp_ficha">	
				<div class="imp_ficha_nombre">Autores:</div>
				<div class="imp_ficha_valor imp_ficha_valor_temas_autores">
				<?php 
				$cantidad_autores = count($datos_ficha['autores']);
				for ($a=0; $a < $cantidad_autores; $a++)
				{
					$autor = &$datos_ficha['autores'][$a];
					echo $autor['descripcion_grp']; 
					if ( $a != ($cantidad_autores - 1) ) echo '<b>;</b> ';
				}
				?>
				</div>
			</div>
			
			<?php
			$cantidad_proy = count($datos_ficha['proyectos']);
			for ($p=0; $p < $cantidad_proy; $p++)
			{
				$proyecto = &$datos_ficha['proyectos'][$p];
			?>
				<div class="imp_ficha">
					<div class="imp_ficha_nombre">Proyecto de:</div>
					<div class="imp_ficha_valor"><?php echo $proyecto['descripcion_proyecto']; ?></div>
				</div>
				<div class="imp_ficha_extracto"><span class="imp_ficha_negrita">Extracto:</span>&nbsp;&nbsp;<?php echo $proyecto['extracto']; ?></div>
			<?php  
			}
			?>
			
			<div class="imp_ficha">	
				<div class="imp_ficha_nombre">Estado / Desde</div>
				<div class="imp_ficha_valor imp_ficha_valor_mas_largo">
					<?php 
					$estado = &$datos_ficha['estado'][0];
					echo $estado['nombre_estado'];
					?>
				</div>
				<div class="imp_ficha_fecha_estado"><?php echo $this->formatearFecha($estado['fecha_estado']); ?></div>
			</div>
			<?php 
			// SI SE ENCUENTRA AGREGADO A OTRO EXPEDIENTE
			if ($ficha['agregado_anio'] != 0)
			{
			?>
				<div class="imp_ficha">	
					<div class="imp_ficha_nombre">Agregado a:</div>
					<div class="imp_ficha_valor">
						<?php echo   $ficha['agregado_anio']."&nbsp;&nbsp;".$ficha['agregado_tipo']."&nbsp;&nbsp;&nbsp;&nbsp;".$ficha['agregado_numero']."&nbsp;&nbsp;".$ficha['agregado_cuerpo']."&nbsp;&nbsp;".$ficha['agregado_alcance']; ?>
					</div>
				</div>			
			<?php
			} 
			// SI POSEE OBSERVACION
			if ($ficha['observaciones_expe'] != '')
			{
			?>
				<div class="imp_ficha">	
					<div class="imp_ficha_nombre">Observaciones:</div>
					<div class="imp_ficha_valor"><?php echo $ficha['observaciones_expe']; ?></div>
				</div>
			<?php
			}	
		
			//SI EXISTE ALGUN GIRO, SE MUESTRA
			if (isset($datos_ficha['giros'][0]['comision_codigo']))
			{
			?>
				<div class="imp_ficha_margen_inferior_cerrar"></div>
				<div class="imp_ficha">
					<div class="imp_ficha_nombre">Giros:</div>
				</div>
				<div class="imp_ficha">
					<div class="imp_ficha_giro_comision_codigo imp_ficha_negrita">Cod.</div>
					<div class="imp_ficha_giro_descripcion_grp imp_ficha_negrita">Comisi&oacute;n</div>
					<div class="imp_ficha_giro_fechas imp_ficha_negrita">Fecha Entrada</div>
					<div class="imp_ficha_giro_fechas imp_ficha_negrita">Fecha Salida</div>
					<div class="imp_ficha_giro_dictamen_giro imp_ficha_negrita">Dictamen</div>
				</div>
				<?php 
				$cantidad_giros = count($datos_ficha['giros']);
				for ($g=0; $g < $cantidad_giros; $g++)
				{
					$giro = &$datos_ficha['giros'][$g];
				?>
					<div class="imp_ficha">
						<div class="imp_ficha_giro_comision_codigo"><?php echo $giro['comision_codigo']; ?></div>
						<div class="imp_ficha_giro_descripcion_grp"><?php echo $giro['comision']; ?></div>
						<div class="imp_ficha_giro_fechas"><?php echo ($giro['fecha_entrada_giro']) ? $this->formatearFecha($giro['fecha_entrada_giro']) : ''; ?></div>
						<div class="imp_ficha_giro_fechas"><?php echo ($giro['fecha_salida_giro']) ? $this->formatearFecha($giro['fecha_salida_giro']) : ''; ?></div>
						<div class="imp_ficha_giro_dictamen_giro"><?php echo $giro['dictamen_giro']; ?></div>
					</div>
				<?php 
				}
			}
		
			//SI EXISTE ALGUN ANTECEDENTE, SE MUESTRA
			if (isset($datos_ficha['antecedentes'][0]['tipo']))
			{
			?>
				<div class="imp_ficha_margen_inferior_cerrar"></div>
				<div class="imp_ficha">	
					<div class="imp_ficha_nombre">Antecedentes:</div>
				</div>
				<div class="imp_ficha">
					<div class="imp_ficha_antecedente_margen">&nbsp;</div>
					<div class="imp_ficha_antecedente_tipo">Tipo</div>
					<div class="imp_ficha_antecedente_numero">N&uacute;mero</div>
					<div class="imp_ficha_antecedente_anio">A&ntilde;o</div>
					<div class="imp_ficha_antecedente_cuerpo">Cpo.</div>
					<div class="imp_ficha_antecedente_alcance">Alc.</div>
					<div class="imp_ficha_antecedente_observaciones">Observaciones</div>
				</div>
				<?php
				$cant_antecedentes = count($datos_ficha['antecedentes']);
				for ($ant=0; $ant < $cant_antecedentes; $ant++)
				{
					$antecedente = &$datos_ficha['antecedentes'][$ant];
					?>
					<div class="imp_ficha">
						<div class="imp_ficha_antecedente_margen">&nbsp;</div>
						<div class="imp_ficha_antecedente_tipo"><?php echo $antecedente['tipo_a']; ?></div>
						<div class="imp_ficha_antecedente_numero"><?php echo $antecedente['numero_a']; ?></div>
						<div class="imp_ficha_antecedente_anio"><?php echo $antecedente['anio_a']; ?></div>
						<div class="imp_ficha_antecedente_cuerpo"><?php echo $antecedente['cuerpo_a']; ?></div>
						<div class="imp_ficha_antecedente_alcance"><?php echo $antecedente['alcance_a']; ?></div>
						<div class="imp_ficha_antecedente_observaciones"><?php echo $antecedente['observaciones_antecedentes']; ?></div>
					</div>
				<?php 
				}
			}
			?>
		</div>
		<script>
			$('btCerrar_pedirNombreModal').addEvent('click', function(){
				cerrarModalPedirNombre();
			});
		</script>
	<?php
	}
}
?>

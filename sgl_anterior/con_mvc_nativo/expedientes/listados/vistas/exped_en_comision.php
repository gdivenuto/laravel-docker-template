<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaExpedEnComision extends VistaBase
{
	private $modelo_listado;
	private $controlador;
	private $formulario;
	private $nroPagina;

	public function __construct()
	{
		$this->modelo_listado = new expedEnComisionModel();
		$this->controlador = 'exped_en_comision';
		$this->formulario = 'formListados';
	}

	public function calcularFecha($dias)
	{
 	    $calculo = strtotime("$dias days");
	    return date("d/m/Y", $calculo);
	}

	public function mostrarModalComisionesActivas($listadoComisiones)
    {
    ?>
		<form id="formulario_comisiones_modal">
			<div class="listado_contenedor_comisiones_modal">
				<div class="listado_contenedor_comisiones_modal">
					<div class="listado_comisiones_modal checkbox_con_texto">
						<label><input type="checkbox" name="i_comisiones_todos" id="i_comisiones_todos" value="" onClick="javascript:marcar_desmarcar_comisiones_modal();" checked >&nbsp;Todas</label>
						<?php
						$cant_comisiones = count($listadoComisiones);
						for ($c=0; $c < $cant_comisiones; $c++)
						{
							$comision = &$listadoComisiones[$c];
						?>
							<label>
								<input type="checkbox" name="l_check_comisiones_modal[]" id="i_comision_<?php echo $comision['codigo_grp']; ?>" value="<?php echo $comision['codigo_grp']; ?>" checked >&nbsp;<?php echo $comision['tipo_grp'].', '.$comision['codigo_grp'].', '.$comision['descripcion_grp']; ?>
							</label>
						<?php
						}
						?>
					</div>
				</div>
			</div>
			<div style="height:10px;font-size:0"></div>
			<div class="cerrar_pedirNombreModal">
				<div class="titulo_pedirNombreModal">Seleccione las Comisiones que desea en la b&uacute;squeda.</div>
				<div class="listado_boton degradado">
					<a href="javascript:seguirModal();">
						<img src="data:image/gif;base64,R0lGODlhDQAQALMBAP///7+/v5rc82OkzwBUeQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAAAEALAAAAAANABAAAAQyMEgppr2iXjz0pkNnZaQQiuCpouaqVq17ZrJKxDIR4KseDIRg8OTbtIpG4GciQH4GnwgAOw==" width="14" height="14" />&nbsp;&nbsp;Siguiente
					</a>
				</div>
			</div>
		</form>
		<script type="text/javascript">

			function pasarComisionModal(id_comision_modal_origen, id_comision_modal_destino)
			{
				// SI LA COMISION ESTA MARCADA
				if ( $(id_comision_modal_origen).checked )
				{
					// SE ASIGNA EL CODIGO DE LA COMISION
					$(id_comision_modal_destino).value = $(id_comision_modal_origen).value;
				}
				else
				{
					// SE QUITA EL CODIGO DE LA COMISION PARA DESCARTARLA DEL RESULTADO
					$(id_comision_modal_destino).value = 0;
				}
			}

			function cargarComisionesModal()
			{
				<?php
				$cant_comisiones = count($listadoComisiones);
				for ($c=0; $c < $cant_comisiones; $c++)
				{
					$comision = &$listadoComisiones[$c];
				?>
					pasarComisionModal('i_comision_<?php echo $comision['codigo_grp']; ?>', 'l_comision_modal_<?php echo $comision['codigo_grp']; ?>');
				<?php
				}
				?>
			}

			function marcar_desmarcar_comisiones_modal()
			{
				// SI SE DESEA MARCAR TODO
				if ( $('i_comisiones_todos').checked )
				{
					// SE MARCAN TODAS LAS COMISIONES
					marcarTodosCheckbox('formulario_comisiones_modal');
				}
				else
				{
					// SE DESMARCAN TODAS LAS COMISIONES
					desmarcarTodosCheckbox('formulario_comisiones_modal');
				}
			}

			// Cuando se presiona en el botón "Siguiente" de la modal de Comisiones
			function seguirModal()
			{
				// Se verifica que por lo menos un checkbox esté tildado
				if ( verificarCheckbox('formulario_comisiones_modal') )
				{
					// Se cargan las comisiones que hayan sido seleccionadas
					cargarComisionesModal();

					// Se cierra la modal
					cerrarModalPedirNombre();

					// Se busca
					enviarForm('<?php echo $this->formulario; ?>', 'listados', 'contenidoAjaxResultadoListados');
				}
				else
				{
					alert("Debe seleccionar por lo menos una Comisi"+'\u00f3'+"n.");
				}
			}
		</script>
	<?php
    }
/*************************************************************************************************************************
      SE GENERA EL REPORTE DE Expedientes en Comision PARA SU IMPRESION
*************************************************************************************************************************/
    public function generar_formato_de_impresion_exped_en_comision($listado_para_pdf = '', $filtro_para_pdf = '')
	{
		header("Content-Type: text/html; charset=UTF-8");

		$modelo = new consultaGralModel();
		$modelo_exped_en_comision = new expedEnComisionModel();
		?>
		<style type="text/css">
			.imp_texto {
				font-family: Arial;
				font-size: 12px;
			}
			.imp_titulo_general {
				padding:10px 0 0 150px;
				font-size: 18px;
				font-weight:700;
				text-decoration:underline;
				text-align:left;
				border-left:2px solid #000;
				border-right:2px solid #000;
			}
			.imp_subtitulo_general {
				padding:10px 0 20px 10px;
				font-size:14px;
				font-weight:700;
				text-align:left;
				border-left:2px solid #000;
				border-right:2px solid #000;
			}
			/************************************************************/
			.imp_titulos_de_clave {
				height:25px;
				border:2px solid #000;
			}
			.imp_titulos_de_clave_izq {
				width: 395px;
				height: 20px;
				padding: 5px 0 0 5px;
				float: left;
				font-weight: 700;
			}
			.imp_titulos_de_clave_der {
				width: 245px;
				height: 20px;
				padding: 5px 5px 0 0;
				float: right;
				text-align: right;
				font-weight: 700;
			}
			/************************************************************/
			.imp_ficha_titulos_de_clave {
				height:25px;
				border-left:2px solid #000;
				border-right:2px solid #000;
				clear:both;
			}
			.imp_ficha_titulos_de_clave_izq {
				height: 20px;
				padding: 5px;
				float: left;
				font-weight: 700;
			}
			.imp_ficha_titulos_de_clave_der {
				height: 20px;
				padding: 5px 5px 0 0;
				float: right;
				text-align: right;
				font-weight: 700;
			}
			.imp_ficha_extracto {
				padding:5px 5px 10px 5px;
				text-align:left;
				border-left:2px solid #000;
				border-right:2px solid #000;
			}
			.imp_ficha_linea_informe_pendiente {
				height: 25px;
				border-left: 2px solid #000;
				border-right: 2px solid #000;
				clear: both;
				padding-left: 5px;
			}
			.linea_horizontal {
				height:1px;
				font-size:0;
				border-left:2px solid #000;
				border-right:2px solid #000;
				border-bottom:2px solid #000;
			}
			#salto_pagina_anterior {
			  page-break-before:always;
			}
			.btImprimir {
			  text-align:center;
			  padding:7px;
			}
		</style>

		<style media="print" type="text/css">
			#btImprimir {
			   display:none;
			}
		</style>

		<div id="btImprimir" class="btImprimir">
			<input name="imprimir" type="button" class="button" value="Imprimir Informe" onClick="window.print();">
		</div>

		<?php $this->encabezado_reporte(); ?>

		<div class="imp_texto imp_titulo_general">Listado de Expedientes/Notas en Comisi&oacute;n</div>
		<div class="imp_texto imp_subtitulo_general"></div>

		<?php $this->criterio_busqueda_reporte($filtro_para_pdf); ?>

		<div class="imp_texto imp_titulos_de_clave">
			<div class="imp_titulos_de_clave_izq">A&ntilde;o&nbsp;&nbsp;&nbsp;T.&nbsp;&nbsp;Nro.&nbsp;&nbsp;Cpo.&nbsp;Alc.&nbsp;Letra&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Car&aacute;tula</div>

			<div class="imp_titulos_de_clave_der"><?php if ( $listado_para_pdf[0]['dias'] ) echo "D&iacute;as en Comisi&oacute;n"; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha Ingreso</div>
		</div>

		<?php
		$cantidad_expedientes = 0;
		$cantidad_notas = 0;

		// SE RECORREN LOS EXPEDIENTES A MOSTRAR
		$cantidad = count($listado_para_pdf);
		for ($i=0; $i < $cantidad; $i++)
		{
			$ficha = &$listado_para_pdf[$i];

			if ($ficha['tipo']=='E') $cantidad_expedientes++;
			elseif ($ficha['tipo']=='N') $cantidad_notas++;
		?>
			<div class="imp_texto imp_ficha_titulos_de_clave" >
				<div class="imp_ficha_titulos_de_clave_izq" style="display: block;" ><?php echo $ficha['anio']; ?>&nbsp;<?php echo $ficha['tipo']; ?>&nbsp;<?php echo $ficha['numero']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['cuerpo']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['alcance']; ?>&nbsp;&nbsp;&nbsp;<?php echo $ficha['iniciador_codigo']; ?>&nbsp;&nbsp;&nbsp;<?php echo $ficha['caratula']; ?></div>
				<div class="imp_ficha_titulos_de_clave_der">
					<?php echo ($ficha['dias']) ? $ficha['dias']." d&iacute;as" : ''; ?>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<?php echo $this->formatearFecha($ficha['fecha_entrada_expe']); ?>
				</div>
			</div>
			<?php
			$cantidad_proy = count($ficha['proyectos']);
			for ($p=0; $p < $cantidad_proy; $p++)
			{
				$proyecto = &$ficha['proyectos'][$p];

				if ( $proyecto['extracto'] != '' && $proyecto['extracto'] != 'null' )
				{
				?>
					<div class='imp_texto imp_ficha_extracto'>
						<?php echo $this->reemplazarPorHTML($proyecto['extracto']); ?>
					</div>
				<?php
				}
			}

			//  SI NO SE FILTRÓ POR UNA COMISIÓN DETERMINADA
			if ( !$filtro_para_pdf['l_comision_tipo'] && !$filtro_para_pdf['l_comision_codigo'] )
			{
			?>
				<!-- SE MUESTRA LA DESCRIPCION DE LA COMISION -->
				<div class="imp_texto imp_ficha_linea_informe_pendiente" >
					<b>Comisi&oacute;n: </b><?php echo $ficha['descripcion_comision']; ?>&nbsp;&nbsp;&nbsp;<b>Desde el: </b><?php echo $this->formatearFecha($ficha['fecha_giro']); ?>
				</div>
			<?php
			}

			// 20/04/2015, PARA LOS VENCIDOS
			if ( $ficha['dias'] > 120 )
			{
				// 20/04/2015, SE OBTIENEN DATOS DEL ULTIMO INFORME DE UN GIRO DETERMINADO
				$datos_ultimo_informe = $modelo_exped_en_comision->obtenerUltimoInforme($ficha);

				// 20/04/2015, SI POSEE UN INFORME
				if ( $datos_ultimo_informe )
				{
					// SE MUESTRA EL DETALLE DEL INFORME PENDIENTE
				?>
					<div class="imp_texto imp_ficha_linea_informe_pendiente" >
						<b>Informe pendiente</b> desde el <?php echo $this->formatearFecha($datos_ultimo_informe['fecha_pedido_informe']); ?>
						<?php echo ($datos_ultimo_informe['detalle_informe']) ? ': '.$datos_ultimo_informe['detalle_informe'] : ''; ?>
						<?php echo ($datos_ultimo_informe['observaciones_informe']) ? ': '.$datos_ultimo_informe['observaciones_informe'] : ''; ?>
					</div>
				<?php
				}
			}
			?>
			<div class='linea_horizontal'></div>
		<?php
		}

		echo $this->contador_reporte($cantidad_expedientes, $cantidad_notas);

		echo $this->pie_reporte();
		?>
		<div id="btImprimir" class="btImprimir">
			<input name="imprimir" type="button" class="button" value="Imprimir Informe" onClick="window.print();">
		</div>
		<?php
    }
/*************************************************************************************************************************
      SE GENERA EL REPORTE DE Orden del Dia PARA SU IMPRESION
*************************************************************************************************************************/
    public function generar_formato_de_impresion_orden_del_dia_en_comision($listado_para_impresion = '', $filtro_para_impresion = '')
    {
		header("Content-Type: text/html; charset=UTF-8");
		$modelo = new consultaGralModel();
		?>
		<style type="text/css">
			.imp_texto {
				font-family: Arial;
				font-size: 12px;
			}
			.imp_titulo_general{
				padding:10px 0 0 150px;
				font-size: 18px;
				font-weight:700;
				text-decoration:underline;
				text-align:left;
				border-left:2px solid #000;
				border-right:2px solid #000;
			}
			.imp_subtitulo_general{
				padding:10px 0 20px 10px;
				font-size:14px;
				font-weight:700;
				text-align:left;
				border-left:2px solid #000;
				border-right:2px solid #000;
			}
			/************************************************************/
			.imp_titulos_de_clave{
				height:25px;
				border:2px solid #000;
			}
			.imp_titulos_de_clave_izq {
				width:395px;
				height:20px;
				padding:5px 0 0 5px;
				float:left;
				font-weight:700;
			}
			.imp_titulos_de_clave_der {
				width:105px;
				height:20px;
				padding:5px 5px 0 0;
				float:right;
				text-align:right;
			}
			/************************************************************/
			.imp_titulo_para_la_marca{
				padding:5px 0 5px 5px;
				text-align:left;
				font-size:14px;
				font-weight:700;
				color:#fff;
				background-color:#76A0CD;
				border-left:2px solid #000;
				border-right:2px solid #000;
				border-bottom:2px solid #000;
			}
			.imp_ficha_titulos_de_clave{
				height:25px;
				border-left:2px solid #000;
				border-right:2px solid #000;
			}
			.imp_ficha_titulos_de_clave_izq {
				width:537px;
				height:20px;
				padding:5px 0 0 5px;
				float:left;
				font-weight:700;
			}
			.imp_ficha_extracto{
				padding:5px 5px 10px 5px;
				text-align:left;
				border-left:2px solid #000;
				border-right:2px solid #000;
			}
			.linea_horizontal{
				height:1px;
				font-size:0;
				border-left:2px solid #000;
				border-right:2px solid #000;
				border-bottom:2px solid #000;
			}
			#salto_pagina_anterior{
			  page-break-before:always;
			}
			.btImprimir{
			  text-align:center;
			  padding:7px;
			}
		</style>

		<style media="print" type="text/css">
			#btImprimir {
			   display:none;
			}
		</style>

		<div id="btImprimir" class="btImprimir"><input name="imprimir" type="button" class="button" value="Imprimir Informe" onClick="window.print();"></div>

		<?php $this->encabezado_reporte(); ?>

		<div class="imp_texto imp_titulo_general">Listado de Orden del D&iacute;a</div>

		<div class="imp_texto imp_subtitulo_general"></div>

		<?php $this->criterio_busqueda_reporte($filtro_para_impresion); ?>

		<div class="imp_texto imp_titulos_de_clave">
			<div class="imp_titulos_de_clave_izq">A&ntilde;o&nbsp;&nbsp;&nbsp;T.&nbsp;&nbsp;Nro.&nbsp;&nbsp;Cpo.&nbsp;Alc.&nbsp;Letra&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Car&aacute;tula</div>
			<div class="imp_titulos_de_clave_der">Fecha Ingreso</div>
		</div>

		<?php
		// SE INICIALIZAN LOS CONTADORES
		$cantidad_expedientes = Array();
		$cantidad_notas = Array();

		// SE INICIALIZA UNA VARIABLE AUXILIAR PARA MOSTRAR EL NOMBRE AL CAMBIAR LA MARCA
		$marca_AUXILIAR = 99;
		// SE RECORRE EL LISTADO ORDENADO
		$cantidad = count($listado_para_impresion);
		for ($i=0; $i < $cantidad; $i++)
		{
			$ficha = &$listado_para_impresion[$i];// SE TOMA EL REGISTRO A MOSTRAR

			if ( $ficha['marca_comision'] != $marca_AUXILIAR )
			{
				$marca_AUXILIAR = $ficha['marca_comision'];
				echo "<div class='imp_texto imp_titulo_para_la_marca'>".$this->obtenerNombreMarcaComision($ficha['marca_comision'])."</div>";
			}
			// SE CONTABILIZAN LOS EXPEDIENTES Y LAS NOTAS DE LA MARCA
			if ($ficha['tipo']=='E') $cantidad_expedientes[$ficha['marca_comision']]++;
			elseif ($ficha['tipo']=='N') $cantidad_notas[$ficha['marca_comision']]++;
		?>
			<div class="imp_texto imp_ficha_titulos_de_clave">
				<div class="imp_ficha_titulos_de_clave_izq"><?php echo $ficha['anio']; ?>&nbsp;<?php echo $ficha['tipo']; ?>&nbsp;<?php echo $ficha['numero']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['cuerpo']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['alcance']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['iniciador_codigo']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['caratula']; ?></div>
				<div class="imp_titulos_de_clave_der"><?php echo $this->formatearFecha($ficha['fecha_entrada_expe']); ?></div>
			</div>
		<?php
			$cantidad_proy = count($ficha['proyectos']);
			for ($p=0; $p < $cantidad_proy; $p++)
			{
				$proyecto = &$ficha['proyectos'][$p];

				// SI TIENE EXTRACTO SE MUESTRA
				if ( $proyecto['extracto'] != '' && $proyecto['extracto'] != 'null' )
				{
		?>
					<div class='imp_texto imp_ficha_extracto'>
						<?php echo $proyecto['extracto']; ?>
					</div>
		<?php
				}
			}
		?>
			<div class='linea_horizontal'></div>
		<?php
		}

		echo $this->contador_reporte_orden_del_dia($cantidad_expedientes, $cantidad_notas);

		echo $this->pie_reporte();
		?>
		<div id="btImprimir" class="btImprimir">
			<input name="imprimir" type="button" class="button" value="Imprimir Informe" onClick="window.print();">
		</div>
    <?php
    }

    public function comparar($a,$b)
    {
		return strcmp($a["descripcion_del_iniciador"], $b["descripcion_del_iniciador"]);
    }
/*********************************************************************************************************
      CONTADOR DE EXPEDIENTES Y NOTAS DEL REPORTE DEL ORDEN DEL DIA
    EL INDICE CERO NO SE UTILIZA POR EL ECHO QUE SE CONTABILIZARON LOS QUE POSEEN MARCA,
    A PARTIR DE 1 = Para Tratar, 2 = Para Su Conocimiento, 3 = Para Archivo
**********************************************************************************************************/
    private function contador_reporte_orden_del_dia($cantidad_expedientes, $cantidad_notas)
    {
    ?>
    	<style type="text/css">
			.imp_contador{
				height:20px;
				border-left:2px solid #000;
				border-right:2px solid #000;
				font-family: Arial;
				font-size: 12px;
			}
			.imp_contador_titulo_marca{
				width:250px;
				height:14px;
				padding:0 0 0 10px;
				float:left;
				font-weight:700;
				text-align:left;
			}
			.imp_contador_valor{
				width:100px;
				height:14px;
				padding:3px 10px 3px 0;
				float:left;
				text-align:right;
			}
			.imp_contador_titulo_tipo{
				font-weight:700;
				text-decoration:underline;
			}
			.imp_contador_borde_inferior{
				border-bottom:1px solid #000;
				padding-bottom:10px;
			}
		</style>
		<div class="imp_contador">
			<div class="imp_contador_titulo_marca"></div>
			<div class="imp_contador_valor imp_contador_titulo_tipo">Expedientes</div>
			<div class="imp_contador_valor imp_contador_titulo_tipo">Notas</div>
		</div>
		<div class="imp_contador">
			<div class="imp_contador_titulo_marca">Para Tratar</div>
			<div class="imp_contador_valor"><?php echo ($cantidad_expedientes[1]) ? $cantidad_expedientes[1] : 0; ?></div>
			<div class="imp_contador_valor"><?php echo ($cantidad_notas[1]) ? $cantidad_notas[1] : 0; ?></div>
		</div>
		<div class="imp_contador">
			<div class="imp_contador_titulo_marca">Para Su Conocimiento</div>
			<div class="imp_contador_valor"><?php echo ($cantidad_expedientes[2]) ? $cantidad_expedientes[2] : 0; ?></div>
			<div class="imp_contador_valor"><?php echo ($cantidad_notas[2]) ? $cantidad_notas[2] : 0; ?></div>
		</div>
		<div class="imp_contador">
			<div class="imp_contador_titulo_marca">Para Archivo</div>
			<div class="imp_contador_valor"><?php echo ($cantidad_expedientes[3]) ? $cantidad_expedientes[3] : 0; ?></div>
			<div class="imp_contador_valor"><?php echo ($cantidad_notas[3]) ? $cantidad_notas[3] : 0; ?></div>
		</div>
		<div class="imp_contador">
			<div class="imp_contador_titulo_marca">Para Pr&oacute;rroga</div>
			<div class="imp_contador_valor"><?php echo ($cantidad_expedientes[4]) ? $cantidad_expedientes[4] : 0; ?></div>
			<div class="imp_contador_valor"><?php echo ($cantidad_notas[4]) ? $cantidad_notas[4] : 0; ?></div>
		</div>
		<div class="imp_contador imp_contador_borde_inferior">
			<div class="imp_contador_titulo_marca">Total General</div>
			<div class="imp_contador_valor"><b><?php echo ($cantidad_expedientes[1] + $cantidad_expedientes[2] + $cantidad_expedientes[3] + $cantidad_expedientes[4]); ?></b></div>
			<div class="imp_contador_valor"><b><?php echo ($cantidad_notas[1] + $cantidad_notas[2] + $cantidad_notas[3] + $cantidad_notas[4]); ?></b></div>
		</div>
    <?php
    }
/************************************************************************************************************************
	SE GENERA EL REPORTE DE Detalle de Giros PARA SU IMPRESION
**************************************************************************************************************************/
    public function generar_formato_de_impresion_detalle_de_giros($listado_para_pdf = '', $filtro_para_pdf = '')
    {
		header("Content-Type: text/html; charset=UTF-8");
		$modelo = new consultaGralModel();
    ?>
		<style type="text/css">
		.imp_texto {
			font-family: Arial;
			font-size: 12px;
		}
		.imp_titulo_general{
			padding:10px 0 0 150px;
			font-size: 18px;
			font-weight:700;
			text-decoration:underline;
			text-align:left;
			border-left:2px solid #000;
			border-right:2px solid #000;
		}
		.imp_subtitulo_general{
			padding:10px 0 20px 10px;
			font-size:14px;
			font-weight:700;
			text-align:left;
			border-left:2px solid #000;
			border-right:2px solid #000;
		}
		/************************************************************/
		.imp_titulos_de_clave{
			height:25px;
			border:2px solid #000;
			border-bottom:0;
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
		.imp_bordes {
			border-left: 2px solid #000;
			border-right: 2px solid #000;
			border-bottom: 2px solid #000;
		}
		.imp_ficha {
			height: 20px;
			clear: both;
		}
		.imp_ficha_nombre{
			width:145px;
			height:20px;
			padding-left:5px;
			float:left;
		}
		.imp_ficha_valor{
			width:270px;
			height:20px;
			float:left;
		}
		.imp_ficha_valor_mas_largo {
			width: 350px;
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
			width:500px;
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
		.imp_ficha_giro_comision_codigo{
			width: 37px;
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
			width:80px;
			height:20px;
			float:left;
		}
		.imp_ficha_giro_dictamen_giro{
			width:220px;
			height:20px;
			float:left;
		}
		.btImprimir{
			text-align:center;
			padding:7px;
		}
		</style>

		<style media="print" type="text/css">
		#btImprimir {
			display:none;
		}
		</style>

		<div id="btImprimir" class="btImprimir"><input name="imprimir" type="button" class="button" value="Imprimir Informe" onClick="window.print();"></div>

		<?php $this->encabezado_reporte(); ?>

		<div class="imp_texto imp_titulo_general">Listado de Expedientes con Detalle de Giros</div>

		<div class="imp_texto imp_subtitulo_general"></div>

		<?php $this->criterio_busqueda_reporte($filtro_para_pdf); ?>

		<div class="imp_titulos_de_clave">
			<div class="imp_texto imp_titulos_de_clave_izq">A&ntilde;o&nbsp;&nbsp;&nbsp;T.&nbsp;&nbsp;Nro.&nbsp;&nbsp;Cpo.&nbsp;Alc.</div>
			<div class="imp_texto imp_titulos_de_clave_der">Fecha Ingreso</div>
		</div>
		<div class="imp_bordes">
			<?php
			$cantidad_expedientes = 0;
			$cantidad_notas = 0;

			$cantidad = count($listado_para_pdf);
			for ($f=0; $f < $cantidad; $f++)
			{
				$ficha = &$listado_para_pdf[$f];

				if ($ficha['tipo']=='E') $cantidad_expedientes++;
				elseif ($ficha['tipo']=='N') $cantidad_notas++;
			?>
				<div class="imp_texto imp_ficha_titulos_de_clave">
					<div class="imp_ficha_titulos_de_clave_izq"><?php echo $ficha['anio']; ?>&nbsp;<?php echo $ficha['tipo']; ?>&nbsp;<?php echo $ficha['numero']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['cuerpo']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['alcance']; ?></div>
					<div class="imp_titulos_de_clave_der"><?php echo $this->formatearFecha($ficha['fecha_entrada_expe']); ?></div>
				</div>

				<div class="imp_texto imp_ficha">
					<div class="imp_ficha_nombre">Car&aacute;tula</div>
					<div class="imp_ficha_valor imp_ficha_valor_mas_largo"><?php echo $ficha['caratula']; ?></div>
				</div>
				<div class="imp_texto imp_ficha">
					<div class="imp_ficha_nombre">Iniciador / Categor&iacute;a</div>
					<div class="imp_ficha_valor_extracto"><?php echo $ficha['iniciador']; ?>&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;<?php echo $ficha['categoria']; ?></div>
					<!--<div class="imp_ficha_valor"></div>-->
				</div>
				<div class="imp_texto imp_ficha">
					<div class="imp_ficha_nombre">Temas</div>
					<div class="imp_ficha_valor">
						<?php
						  $cantidad_temas = count($ficha['temas']);
						  for ($t=0; $t < $cantidad_temas; $t++)
						  {
							$tema = &$ficha['temas'][$t];
							echo $tema['descripcion_tema'];
							if ( $t != ($cantidad_temas - 1) ) echo '<b>;</b> ';
						  }
						?>
					</div>
				</div>
				<div class="imp_texto imp_ficha">
					<div class="imp_ficha_nombre">Autores</div>
					<div class="imp_ficha_valor">
						<?php
						  $cantidad_autores = count($ficha['autores']);
						  for ($a=0; $a < $cantidad_autores; $a++)
						  {
							$autor = &$ficha['autores'][$a];
							echo $autor['descripcion_grp'];
							if ( $a != ($cantidad_autores - 1) ) echo '<b>;</b> ';
						  }
						?>
					</div>
				</div>
				<?php
				$cantidad_proy = count($ficha['proyectos']);
				for ($p=0; $p < $cantidad_proy; $p++)
				{
					$proyecto = &$ficha['proyectos'][$p];
				?>
					<div class="imp_texto imp_ficha">
						<div class="imp_ficha_nombre">Proyecto de</div>
						<div class="imp_ficha_valor"><?php echo $proyecto['descripcion_proyecto']; ?></div>
					</div>
				<?php
					// SI TIENE EXTRACTO SE MUESTRA
					if ( $proyecto['extracto'] != '' &&  $proyecto['extracto'] != 'null' )
					{
				?>
						<div class="imp_texto imp_ficha">
							<div class="imp_ficha_nombre">Extracto</div>
							<div class="imp_ficha_valor_extracto"><?php echo $proyecto['extracto']; ?></div>
						</div>
				<?php
					}
				}
				?>
				<div class="imp_texto imp_ficha">
					<div class="imp_ficha_nombre">Estado:&nbsp;</div>
					<div class="imp_ficha_valor">
					<?php
						$estado = $modelo->obtenerEstadoFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance'], $filtro_para_pdf['l_estado']);
						echo $estado[0]['nombre_estado'];
					?>
					</div>
					<div class="imp_ficha_fecha_estado"><?php echo " Desde el ".$this->formatearFecha($estado[0]['fecha_estado']); ?></div>
				</div>

				<?php
				if ( ($filtro_para_pdf['l_comision_tipo'] != '') && ($filtro_para_pdf['l_comision_codigo'] != '') )
				{
					//SE OBTIENE LA Comision DEL Expediente RESULTANTE
					$comision = $modelo->obtenerComisionFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);//, $filtro_para_pdf['l_comision_tipo'], $filtro_para_pdf['l_comision_codigo']
				?>
					<div class="imp_texto imp_ficha">
						<div class="imp_ficha_nombre">Comisi&oacute;n:&nbsp;</div>
						<div class="imp_ficha_valor" style="width:521px;"><?php echo $comision[0]['comision']; ?></div>
						<div class="imp_ficha_fecha_estado"><?php echo " Desde el ".$this->formatearFecha($comision[0]['fecha_giro']); ?></div>
						<?php
						/**
						if ( $filtro['l_tipo_listado'] == "exped_en_comision" )
						{
							$id = "lec_cant_dias_".$ficha['anio'].$ficha['tipo'].$ficha['numero'].$ficha['cuerpo'].$ficha['alcance'];
						?>
							<div id="<?php echo $id; ?>" class="lec_cantidad_dias_expe">
								<script>
									obtenerDiferenciaFechasEnDias('<?php echo $id; ?>', $('l_fecha_de_listado').value, '<?php echo $this->formatearFecha($comision[0]['fecha_giro']); ?>');
								</script>
							</div>
						<?php
						}
						/**/
						?>
					</div>
				<?php
				}
				?>

				<?php
				//Se crea una instancia del modelo
				$modeloEnComision = new expedEnComisionModel();
				//SE OBTIENEN LOS Giros DEL Expediente RESULTANTE
				$giros = $modeloEnComision->obtenerGiros($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);

				//SI EXISTE ALGUN GIRO, SE MUESTRA
				if ( isset($giros[0]['comision_codigo']) )
				{
					$cantidad_giros = count($giros);
					for ( $g=0; $g < $cantidad_giros; $g++ )
					{
						$giro = &$giros[$g];
					?>
						<div class="imp_texto imp_ficha">
							&nbsp;&nbsp;
							<?php echo $giro['comision_codigo']; ?>&nbsp;&nbsp;
							<?php echo $giro['descripcion_grp']; ?>&nbsp;&nbsp;
							<?php echo ($giro['fecha_entrada_giro']) ? $this->formatearFecha($giro['fecha_entrada_giro']) : ''; ?>&nbsp;&nbsp;
							<?php echo ($giro['fecha_salida_giro']) ? $this->formatearFecha($giro['fecha_salida_giro']) : ''; ?>&nbsp;&nbsp;
							<?php echo $giro['dictamen_giro']; ?>
						</div>
					<?php
					}
				}
			} // FIN DEL for
		?>
		</div>
		<?php
		echo $this->contador_reporte($cantidad_expedientes, $cantidad_notas);

		echo $this->pie_reporte();
		?>
		<div id="btImprimir" class="btImprimir">
			<input name="imprimir" type="button" class="button" value="Imprimir Informe" onClick="window.print();">
		</div>
		<?php
    }
/*****************************************************************************************************************
	SE GENERA EL REPORTE DE Asuntos Entrados PARA SU IMPRESION
********************************************************************************************************************/
    public function generar_formato_de_impresion_asuntos_entrados($listado_para_impresion = '', $filtro_para_impresion = '')
	{
		header("Content-Type: text/html; charset=UTF-8");
		$modelo = new consultaGralModel();
		?>
		<style type="text/css">
			.imp_texto {
				font-family: Arial;
				font-size: 12px;
			}
			.imp_titulo_general{
				padding:10px 0 0 150px;
				font-size: 18px;
				font-weight:700;
				text-decoration:underline;
				text-align:left;
				border-left:2px solid #000;
				border-right:2px solid #000;
			}
			.imp_subtitulo_general{
				padding:10px 0 20px 10px;
				font-size:14px;
				font-weight:700;
				text-align:left;
				border-left:2px solid #000;
				border-right:2px solid #000;
			}
			/************************************************************/
			.imp_descripcion_iniciador{
				padding:5px 0 5px 5px;
				text-align:left;
				font-size:14px;
				font-weight:700;
				color:#fff;
				background-color:#76A0CD;
				border:2px solid #000;
			}
			.imp_bordes {
				border-top: 2px solid #000;
				border-left: 2px solid #000;
				border-right: 2px solid #000;
			}
			.imp_ficha_iniciador_y_caratula {
				height:15px;
				padding:5px 0 5px 15px;
				text-align:left;
				font-weight:700;
			}
			.imp_ficha_extracto{
				clear:both;
				padding:5px 5px 10px 5px;
				text-align:left;
			}
			.imp_titulo_para_la_marca{
				padding:5px 0 5px 5px;
				text-align:left;
				font-size:14px;
				font-weight:700;
				color:#fff;
				background-color:#76A0CD;
				border-left:2px solid #000;
				border-right:2px solid #000;
				border-bottom:2px solid #000;
			}
			.linea_horizontal{
				height:1px;
				font-size:0;
				border-left:2px solid #000;
				border-right:2px solid #000;
				border-bottom:2px solid #000;
			}
			.btImprimir{
			  text-align:center;
			  padding:7px;
			}
		</style>

		<style media="print" type="text/css">
		#btImprimir {
		   display:none;
		}
		</style>

		<div id="btImprimir" class="btImprimir"><input name="imprimir" type="button" class="button" value="Imprimir Informe" onClick="window.print();"></div>

		<?php echo $this->encabezado_reporte(); ?>

		<div class="imp_texto imp_titulo_general">Listado de Asuntos Entrados</div>
		<div class="imp_subtitulo_general"></div>

		<?php $this->criterio_busqueda_reporte($filtro_para_impresion); ?>

		<div class='imp_bordes'>
			<?php
			// SE RECORRE EL LISTADO ORDENADO
			$cantidad = count($listado_para_impresion);
			for ( $i=0; $i < $cantidad; $i++ )
			{
				$ficha = &$listado_para_impresion[$i];// SE TOMA EL REGISTRO A MOSTRAR
			?>
				<div class="imp_texto imp_ficha_iniciador_y_caratula">
					<?php
					if ( $ficha['tipo'] == 'E' )
						echo 'Expte.';
					elseif ( $ficha['tipo'] == 'N' )
						echo 'Nota';

					echo '&nbsp;&nbsp;&nbsp;'.$ficha['numero'].'-'.$ficha['iniciador_codigo'].'-'.substr($ficha['anio'], -2).': ';

					// 05/04/2018
					// CORREGIDO POR XXXX
					// ***********************
					// Si el iniciador es un Concejal
					if ( $ficha['iniciador_codigo'] == 'CJA' ) {
						$info_autor = $this->modelo_listado->obtenerAutoresFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
						// Se muestra el Autor (el nombre del Concejal)
						echo LibreriaGeneral::reemplazarPorMayusculaAcentuada(strtoupper($info_autor[0]['descripcion_grp'])).': ';
					} else
						// sino se muestra la descripción del iniciador
						echo LibreriaGeneral::reemplazarPorMayusculaAcentuada(strtoupper($ficha['iniciador'])).': ';

					echo $ficha['caratula'].': ';
					?>
				</div>
				<?php
				$cantidad_proy = count($ficha['proyectos']);
				for ( $p=0; $p < $cantidad_proy; $p++ )
				{
					$proyecto = &$ficha['proyectos'][$p];

					// SI TIENE EXTRACTO SE MUESTRA
					if ( $proyecto['extracto'] != '' && $proyecto['extracto'] != 'null' )
					{
				?>
						<div class='imp_texto imp_ficha_extracto'>
							<?php echo $proyecto['extracto']; ?>
						</div>
				<?php
					}
				}
				?>
				<div class='linea_horizontal'></div>
			<?php
			}
			?>
		</div>
		<?php
		echo $this->pie_reporte();
		?>
		<div id="btImprimir" class="btImprimir">
			<input name="imprimir" type="button" class="button" value="Imprimir Informe" onClick="window.print();">
		</div>
		<?php
    }
/***********************************************************************************************************************
	SE GENERA EL REPORTE DE Expedientes para Expurgo PARA SU IMPRESION
************************************************************************************************************************/
    public function generar_formato_de_impresion_expurgo($listado_para_impresion = '', $filtro_para_impresion = '')
	{
		header("Content-Type: text/html; charset=UTF-8");
		$modelo = new consultaGralModel();
		?>
		<style type="text/css">
		.imp_texto {
			font-family: Arial;
			font-size: 12px;
		}
		.imp_titulo_general{
			padding:10px 0 0 150px;
			font-size: 18px;
			font-weight:700;
			text-decoration:underline;
			text-align:left;
			border-left:2px solid #000;
			border-right:2px solid #000;
		}
		.imp_subtitulo_general{
			padding:10px 0 20px 10px;
			font-size:14px;
			font-weight:700;
			text-align:left;
			border-left:2px solid #000;
			border-right:2px solid #000;
		}
		/************************************************************/
		.imp_titulos_de_clave{
			height:25px;
			border:2px solid #000;
		}
		.imp_titulos_de_clave_izq {
			width:395px;
			height:20px;
			padding:5px 0 0 5px;
			float:left;
			font-weight:700;
		}
		.imp_titulos_de_clave_der {
			width:105px;
			height:20px;
			padding:5px 5px 0 0;
			float:right;
			text-align:right;
		}
		/************************************************************/
		.imp_ficha_titulos_de_clave{
			height:25px;
			border-left:2px solid #000;
			border-right:2px solid #000;
		}
		.imp_ficha_titulos_de_clave_izq {
			width:537px;
			height:20px;
			padding:5px 0 0 5px;
			float:left;
			font-weight:700;
		}
		.imp_ficha_proyecto {
			height: 25px;
			border-left: 2px solid #000;
			border-right: 2px solid #000;
			padding:5px 0 5px 15px;
			text-align:left;
		}
		.imp_ficha_codigo_y_descripcion_proyecto{
			float:left;
			padding:5px 0 5px 15px;
			text-align:left;
			font-weight:700;
		}
		.imp_ficha_promulgado_y_sancionado{
			float:left;
			padding:5px 0 5px 0;
			text-align:left;
		}
		.imp_ficha_decretado{
			float:left;
			padding:5px 0 5px 0;
			text-align:left;
		}
		.imp_ficha_extracto{
			padding:5px 5px 10px 5px;
			text-align:left;
			border-left:2px solid #000;
			border-right:2px solid #000;
		}
		.linea_horizontal{
			height:1px;
			font-size:0;
			border-left:2px solid #000;
			border-right:2px solid #000;
			border-bottom:2px solid #000;
		}
		.btImprimir{
		  text-align:center;
		  padding:7px;
		}
		</style>

		<style media="print" type="text/css">
		#btImprimir {
		   display:none;
		}
		</style>

		<div id="btImprimir" class="btImprimir"><input name="imprimir" type="button" class="button" value="Imprimir Informe" onClick="window.print();"></div>

		<?php echo $this->encabezado_reporte(); ?>

		<div class="imp_texto imp_titulo_general">Listado de Expedientes para Expurgo</div>
		<div class="imp_texto imp_subtitulo_general"></div>

		<?php $this->criterio_busqueda_reporte($filtro_para_impresion); ?>

		<div class="imp_texto imp_titulos_de_clave">
			<div class="imp_titulos_de_clave_izq">A&ntilde;o&nbsp;&nbsp;&nbsp;T.&nbsp;&nbsp;Nro.&nbsp;&nbsp;Cpo.&nbsp;Alc.&nbsp;Letra&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Car&aacute;tula</div>
			<div class="imp_titulos_de_clave_der">Fecha Ingreso</div>
		</div>

		<?php
		// EXPEDIENTES/NOTAS
		$cantidad = count($listado_para_impresion);
		for ($f=0; $f < $cantidad; $f++)
		{
			$ficha = &$listado_para_impresion[$f];
		?>
			<div class="imp_texto imp_ficha_titulos_de_clave">
				<div class="imp_ficha_titulos_de_clave_izq"><?php echo $ficha['anio']; ?>&nbsp;<?php echo $ficha['tipo']; ?>&nbsp;<?php echo $ficha['numero']; ?>&nbsp;&nbsp;&nbsp;<?php echo $ficha['cuerpo']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['alcance']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['iniciador_codigo']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['caratula']; ?></div>
				<div class="imp_titulos_de_clave_der"><?php echo $this->formatearFecha($ficha['fecha_entrada_expe']); ?></div>
			</div>
		<?php
			// PROYECTOS
			$cantidad_proy = count($ficha['proyectos']);
			for ($p=0; $p < $cantidad_proy; $p++)
			{
				$proyecto = &$ficha['proyectos'][$p];
		?>
				<div class="imp_texto imp_ficha_proyecto">
					<?php
					// SE OBTIENEN LOS DATOS PARA Promulgados, Decretados y Sancionados
					$datos_sancion = $modelo->obtenerDatosSancion($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance'], $proyecto['orden_proyecto']);
					?>
					<?php echo $proyecto['codigo_proyecto'].'&nbsp;'.$proyecto['descripcion_proyecto']; ?>
					&nbsp;&nbsp;&nbsp;<b>Prom:</b>
					<?php echo $this->formatearFecha($datos_sancion[0]['fecha_promulga']).'&nbsp;&nbsp;&nbsp;'.$datos_sancion[0]['numero_promulga']; ?>
					&nbsp;&nbsp;&nbsp;<b>Dec:</b>
					<?php echo $datos_sancion[0]['decreto_promulga']; ?>
					&nbsp;&nbsp;&nbsp;<b>Sanc:</b>
					<?php echo $this->formatearFecha($datos_sancion[0]['fecha_sancion']).'&nbsp;'.$datos_sancion[0]['numero_sancion']; ?>
				</div>
		<?php
				// SI POSEE EXTRACTO SE MUESTRA
				if ( $proyecto['extracto'] != '' && $proyecto['extracto'] != 'null' )
				{
		?>
					<div class="imp_texto imp_ficha_extracto">
						<?php echo $proyecto['extracto']; ?>
					</div>
		<?php
				}
			}
		?>
			<div class='linea_horizontal'></div>
		<?php
		}

		echo $this->pie_reporte();
		?>
		<div id="btImprimir" class="btImprimir">
			<input name="imprimir" type="button" class="button" value="Imprimir Informe" onClick="window.print();">
		</div>
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
			}
			.imp_encabezado_logo{
				width:122px;
				height:170px;
				float:left;
			}
			.imp_encabezado_titulos{
				width:470px;
				height:122px;
				float:left;
				font-family: Arial;
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
				text-align:left;
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
			<div class="imp_encabezado_logo"><img src="../imagenes/escudo_cuatro_colores.gif" width="102" height="119" align="center"></div>
			<div class="imp_encabezado_titulos">
			<div class="imp_encabezado_titulo1">Municipalidad de General Pueyrredon</div>
			<div class="imp_encabezado_titulo2">Sistema de Expedientes</div>
			<div class="imp_encabezado_titulo3">Honorable Concejo Deliberante</div>
			</div>
		</div>
    <?php
    }
/********************************************************************************************************************
	CRITERIO DE BUSQUEDA EN FORMATO PARA IMPRIMIR
******************************************************************************************************************/
    private function criterio_busqueda_reporte($filtro_para_pdf)
    {
		$modelo = new consultaGralModel();
		?>
		<style type="text/css">
			.imp_criterios_busqueda {
				height: 20px;
				font-family: Arial;
				font-size: 12px;
				border-left: 2px solid #000;
				border-right: 2px solid #000;
			}
			.imp_criterios_nombre {
				height: 14px;
				padding: 3px 0 3px 15px;
				float: left;
				font-weight: 700;
				text-align: left;
			}
			.imp_criterios_valor {
				height: 14px;
				padding: 3px 0 3px 15px;
				float: left;
			}
			.imp_criterios_busqueda_margen_inferior {
				height: 5px;
				font-size: 0;
				border-left: 2px solid #000;
				border-right: 2px solid #000;
				clear: both;
			}
		</style>
		<?php
		//  SI SE FILTRÓ POR COMISIÓN
		if ( $filtro_para_pdf['l_comision_tipo'] && $filtro_para_pdf['l_comision_codigo'] )
		{
			$comision = $modelo->obtenerNombreComision($filtro_para_pdf['l_comision_tipo'], $filtro_para_pdf['l_comision_codigo']);
		?>
			<div class="imp_criterios_busqueda">
				<div class="imp_criterios_nombre">Comisi&oacute;n:</div>
				<div class="imp_criterios_valor"><?php echo $comision; ?></div>
			</div>
		<?php
		}

		// SI SE FILTRÓ POR ESTADO
		if ( $filtro_para_pdf['l_estado'] )
		{
			$estado = $modelo->obtenerNombreEstado($filtro_para_pdf['l_estado']);//SE OBTIENE EL NOMBRE DEL Estado
		?>
			<div class="imp_criterios_busqueda">
				<div class="imp_criterios_nombre">Estado:</div>
				<div class="imp_criterios_valor"><?php echo $estado; ?></div>
			</div>
		<?php
		}

		// SI SE FILTRÓ POR LA OBSERVACIÓN DEL ESTADO
		if ( $filtro_para_pdf['l_observacion_estado'] )
		{
		?>
			<div class="imp_criterios_busqueda">
				<div class="imp_criterios_nombre">Con palabras:</div>
				<div class="imp_criterios_valor"><?php echo $filtro_para_pdf['l_observacion_estado']; ?></div>
			</div>
		<?php
		}

		// POR FECHA DESDE - FECHA HASTA
		if ( isset($filtro_para_pdf['l_fecha_desde']) || isset($filtro_para_pdf['l_fecha_hasta']) )
		{
		?>
			<div class="imp_criterios_busqueda">
				<?php
				if (isset($filtro_para_pdf['l_fecha_hasta']))
				{
				?>
				  <div class="imp_criterios_nombre">Fecha Desde:</div>
				  <div class="imp_criterios_valor"><?php echo $this->formatearFecha($filtro_para_pdf['l_fecha_desde']); ?></div>
				<?php
				}
				if (isset($filtro_para_pdf['l_fecha_hasta']))
				{
				?>
				  <div class="imp_criterios_nombre">Fecha Hasta:</div>
				  <div class="imp_criterios_valor"><?php echo $this->formatearFecha($filtro_para_pdf['l_fecha_hasta']); ?></div>
				<?php
				}
				?>
			</div>
		<?php
		}

		// SI SE FILTRÓ POR FECHA DE COMISIÓN
		if (isset($filtro_para_pdf['l_fecha_comision']))
		{
		?>
			<div class="imp_criterios_busqueda">
				<div class="imp_criterios_nombre">Fecha Comisi&oacute;n:</div>
				<div class="imp_criterios_valor"><?php echo $this->formatearFecha($filtro_para_pdf['l_fecha_comision']); ?></div>
			</div>
		<?php
		}
		?>
		<div class="imp_criterios_busqueda_margen_inferior"></div>
    <?php
    }
/********************************************************************
      CONTADOR DE EXPEDIENTES Y NOTAS DEL REPORTE
*******************************************************************/
    private function contador_reporte($cantidad_expedientes, $cantidad_notas)
    {
    ?>
    	<style type="text/css">
			.imp_contador {
				height: 20px;
				border-left: 2px solid #000;
				border-right: 2px solid #000;
				font-family: Arial;
				font-size: 12px;
			}
			.imp_contador_titulo {
				width: 250px;
				height: 14px;
				padding: 3px 0 3px 10px;
				float: left;
				font-weight: 700;
				text-align: left;
			}
			.imp_contador_valor {
				width: 90px;
				height: 14px;
				padding: 3px 10px 3px 0;
				float: left;
				text-align: right;
			}
			.imp_contador_borde_inferior {
				border-bottom: 1px solid #000;
				padding-bottom: 10px;
			}
		</style>
		<div class="imp_contador">
			<div class="imp_contador_titulo">Cantidad de Expedientes:</div>
			<div class="imp_contador_valor"><?php echo $cantidad_expedientes; ?></div>
		</div>
		<div class="imp_contador">
			<div class="imp_contador_titulo">Cantidad de Notas:</div>
			<div class="imp_contador_valor"><?php echo $cantidad_notas; ?></div>
		</div>
		<div class="imp_contador imp_contador_borde_inferior">
			<div class="imp_contador_titulo">Cantidad Total:</div>
			<div class="imp_contador_valor"><b><?php echo ($cantidad_expedientes + $cantidad_notas); ?></b></div>
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
		.imp_pie{
			height:41px;
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
		.imp_gral_texto {
			font-family: Arial;
			font-size: 12px;
			color: #000;
		}
		</style>
		<div class="imp_pie imp_gral_texto">
			<div class="imp_datos_izq"><?php echo date("d/m/Y"); ?>&nbsp;&nbsp;<span id="reloj"></span><br>USR. <?php echo $_SESSION['usuario']; ?></div>
			<div class="imp_datos_der"><br>PC: <?php echo gethostbyaddr($_SERVER['REMOTE_ADDR']); ?></div>
		</div>
    <?php
    }
/******************************************************************************************************************
     SE REFRESCA MEDIANTE AJAX EL COMBO DE Comisiones Y Estados AL SETEAR EL CHECK DE Solo Habilitados o No
******************************************************************************************************************/
    public function comboComisiones($listadoComisiones, $comision = 0)
    {
    ?>
		<select id="c_comision" name="l_comision" class="msl_combo" style="width:207px;">
			<option value="0">0, TODAS</option>
			<?php
			$cant_comisiones = count($listadoComisiones);
			for ($c=0; $c < $cant_comisiones; $c++)
			{
				$comision = &$listadoComisiones[$c];
			?>
				<option value="<?php echo $comision['tipo_grp'].'-'.$comision['codigo_grp']; ?>"><?php echo $comision['codigo_grp'].', '.$comision['descripcion_grp']; ?></option>
			<?php
			}
			?>
		</select>
		&nbsp;
		<a id="imagen_zoom_comisiones_Listados" href="javascript:modalGaby('consultas/index.php?controlador=consulta_gral&accion=pedirNombreComisionModal&c_solo_habilitado='+$('l_solo_habilitado').value+'');" title="Buscar por Nombre de Comisi&oacute;n"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>

		<script language="Javascript" type="text/javascript">
			$('c_comision').value = <?php echo ($comision) ? "'".$comision."'" : 0; ?>;

			$('c_comision').addEvent('change', function(){
				$('c_estado').disabled = ( $('c_comision').value != 0 ) ? true : false;
			});
		</script>
    <?php
    }

    public function comboEstados($listadoEstados, $estado = 0)
    {
    ?>
		<select id="c_estado" name="l_estado" class="mslexp_combo" style="width:207px;">
			<option value="0">0, TODOS</option>
			<?php
			$cant_estados = count($listadoEstados);
			for ($e=0; $e < $cant_estados; $e++)
			{
				$estado = &$listadoEstados[$e];
			?>
				<option value="<?php echo $estado['id_codestado']; ?>"><?php echo $estado['codigo_estado'].', '.$estado['nombre_estado']; ?></option>
			<?php
			}
			?>
		</select>
		&nbsp;
		<a id="imagen_zoom_estados_Listados" href="javascript:modalGaby('abms/index.php?controlador=codestados&accion=pedirNombreModal&c_solo_habilitado='+$('l_solo_habilitado').value+'');" title="Buscar por Nombre de Estado"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>

		<script language="Javascript" type="text/javascript">
			$('c_estado').value = '<?php echo ($_SESSION['filtro_LISTADOS']['l_estado']) ? $_SESSION['filtro_LISTADOS']['l_estado'] : 0; ?>';

			$('c_estado').addEvent('change', function(){
				if ( $('c_estado').value != 0 )
				{
					$('c_comision').disabled = true;
					$('l_fecha_comision').disabled = true;
					$('msl_btffecha_comision').setStyle('visibility', 'hidden');
				}
				else
				{
					$('c_comision').disabled = false;
					$('l_fecha_comision').disabled = false;
					$('msl_btffecha_comision').setStyle('visibility', 'visible');
				}
			});
		</script>
    <?php
    }
/******************************************************************************************************************
     SE GENERA EL REPORTE DE Expedientes/Notas en Comision EN FORMATO .doc PARA PROCESAR SU CONTENIDO
******************************************************************************************************************/
    public function procesar_texto_exped_en_comision($listado_para_documento_de_texto = '', $filtro_para_texto = '')
    {
		header("Content-Type: application/msword; charset=UTF-8");
		header('Content-Disposition: inline; filename=expedientes_y_notas_en_comision.doc');

		$modelo = new consultaGralModel();
		$modelo_exped_en_comision = new expedEnComisionModel();

		//  SE COMIENZA A ARMAR EL DOCUMENTO
		echo "<html>";
		echo "\n<body>";

		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Municipalidad de General Pueyrredon</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Sistema de Expedientes</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Honorable Concejo Deliberante</p>";
		echo "\n<hr>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt;'>Listado de Expedientes/Notas en Comisi&oacute;n</p>";

		$comision = $modelo->obtenerNombreComision($filtro_para_texto['l_comision_tipo'], $filtro_para_texto['l_comision_codigo']);
		if ( $comision != '' )
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Para Tratamiento en la Comisi&oacute;n de ".$this->reemplazarPorHTML($comision)."</p>";

		$estado = $modelo->obtenerNombreEstado($filtro_para_texto['l_estado']);
		if ( $estado != '' )
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Estado: ".$this->reemplazarPorHTML($estado)."</p>";

		if (isset($filtro_para_texto['l_fecha_desde']) || isset($filtro_para_texto['l_fecha_hasta'])) {
			echo chr(9)."\n<p style='margin:0cm;margin-bottom:.0001pt'>Fecha Desde: ";
			echo (isset($filtro_para_texto['l_fecha_desde'])) ? $this->formatearFecha($filtro_para_texto['l_fecha_desde']) : '';
			echo chr(9).chr(9).chr(9)."Fecha Hasta: ";
			echo (isset($filtro_para_texto['l_fecha_hasta'])) ? $this->formatearFecha($filtro_para_texto['l_fecha_hasta']) : '';
			echo "\n</p>";
		}

		if (isset($filtro_para_texto['l_fecha_comision']) || isset($filtro_para_texto['l_fecha_comision'])) {
			echo chr(9)."\n<p style='margin:0cm;margin-bottom:.0001pt'>Fecha Comisi&oacute;n: ";
			echo (isset($filtro_para_texto['l_fecha_comision'])) ? $this->formatearFecha($filtro_para_texto['l_fecha_comision']) : '';
		}

		echo "\n<hr>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>A&ntilde;o".chr(9)."T.".chr(9)."Nro.".chr(9)."Letra".chr(9)."Car&aacute;tula</b></p>";
		echo "\n<hr>";

		// SE RECORREN LOS EXPEDIENTES A MOSTRAR
		$listado_por_marca = Array();
		$cantidad = count($listado_para_documento_de_texto);

		// EXPEDIENTES/NOTAS
		for ($i=0; $i < $cantidad; $i++)
		{
			$ficha = &$listado_para_documento_de_texto[$i];

			$dias_en_comision = "";
			if ( $ficha['dias'] > 0 )
				$dias_en_comision = "( ".$ficha['dias']." d&iacute;as en Comisi&oacute;n )";

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>".$ficha['anio']." ".$ficha['tipo']." ".$ficha['numero']." ".$ficha['iniciador_codigo']." ".$this->reemplazarPorHTML($ficha['caratula'])."</b> ".$dias_en_comision."</p>";

			// PROYECTOS
			$cantidad_proy = count($ficha['proyectos']);
			for ($p=0; $p < $cantidad_proy; $p++) {
				$proyecto = &$ficha['proyectos'][$p];

				// SI POSEE PROYECTOS
				if ( $proyecto['extracto'] != '' && $proyecto['extracto'] != 'null' )
					echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'>".$this->reemplazarPorHTML($proyecto['extracto'])."</p>";
			}

			// 21/04/2015, SI NO SE FILTRÓ POR UNA COMISIÓN DETERMINADA
			if ( !$filtro_para_texto['l_comision_tipo'] && !$filtro_para_texto['l_comision_codigo'] ) {
				$nombre_comision = ($ficha['descripcion_comision'] != '') ? $ficha['descripcion_comision'] : '---';
				$fecha_comision = ($ficha['fecha_giro'] != '') ? $this->formatearFecha($ficha['fecha_giro']) : '---';

				// Se muestra la descripción y la fecha de la Comisión
				echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'><b>Comisi&oacute;n: </b>".$nombre_comision." <b>Desde el: </b>".$fecha_comision;
			}

			// 20/04/2015, PARA LOS VENCIDOS
			if ( $ficha['dias'] > 120 ) {
				// 20/04/2015, SE OBTIENEN DATOS DEL ULTIMO INFORME DE UN GIRO DETERMINADO
				$datos_ultimo_informe = $modelo_exped_en_comision->obtenerUltimoInforme($ficha);

				// 20/04/2015, SI POSEE UN INFORME
				if ( $datos_ultimo_informe )
					// SE MUESTRA EL DETALLE DEL INFORME PENDIENTE
					echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'><b>Informe pendiente</b> desde el ".$this->formatearFecha($datos_ultimo_informe['fecha_pedido_informe']).": ".$datos_ultimo_informe['detalle_informe'].".</p>";
			}
		}

		echo "\n<hr>";

		 // PIE DEL DOCUMENTO A IMPRIMIR CON FECHA, USUARIO Y NOMBRE DE LA PC
		echo "\n<p style='margin-bottom: 0cm'p>".date("d/m/Y")."</p>";
		echo "\n<p style='margin-bottom: 0cm'p>USR. ".$_SESSION['usuario'].chr(9).chr(9).chr(9).chr(9).chr(9)."PC: ".gethostbyaddr($_SERVER['REMOTE_ADDR'])."</p>";

		echo "\n</body>";
		echo "\n</html>";
    }
/******************************************************************************************************************
     SE GENERA EL REPORTE DE Orden del dia de Comision EN FORMATO .doc PARA PROCESAR SU CONTENIDO
******************************************************************************************************************/
    public function procesar_texto_orden_del_dia($listado_para_documento_de_texto = '', $filtro_para_texto = '')
    {
		header("Content-Type: application/msword; charset=UTF-8");
		header('Content-Disposition: inline; filename=orden_del_dia.doc');

		$modelo = new consultaGralModel();
		//  SE COMIENZA A ARMAR EL DOCUMENTO
		echo "<html>";
		echo "\n<body>";

		$marca_AUXILIAR = 99;
		// SE RECORRE EL LISTADO ORDENADO
		$cantidad = count($listado_para_documento_de_texto);
		for ($i=0; $i < $cantidad; $i++)
		{
			$ficha = &$listado_para_documento_de_texto[$i];// SE TOMA EL REGISTRO A MOSTRAR

			if ( $ficha['marca_comision'] != $marca_AUXILIAR )
			{
				$marca_AUXILIAR = $ficha['marca_comision'];
				// SE MUESTRA COMO TITULO EL NOMBRE DE LA MARCA QUE LE CORRESPONDE
				echo "\n<hr>";
				echo "\n<b>".$this->obtenerNombreMarcaComision($ficha['marca_comision'])."</b>";
				echo "\n<hr>";
			}

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>".$ficha['anio']." ".$ficha['tipo']." ".$ficha['numero']." ".$ficha['iniciador_codigo']." ".$this->reemplazarPorHTML($ficha['caratula'])."</b></p>";

			$cantidad_proy = count($ficha['proyectos']);
			for ($p=0; $p < $cantidad_proy; $p++)
			{
				$proyecto = &$ficha['proyectos'][$p];

				echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'>".$this->reemplazarPorHTML($proyecto['extracto'])."</p>";
			}
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";
		}

		echo "\n</body>";
		echo "\n</html>";
    }
/******************************************************************************************************************
     SE GENERA EL REPORTE DE Detalle de Giros EN FORMATO .doc PARA PROCESAR SU CONTENIDO
******************************************************************************************************************/
    public function procesar_texto_detalle_giros($listado_para_pdf = '', $filtro_para_pdf = '')
    {
		header("Content-Type: application/msword; charset=UTF-8");
		header('Content-Disposition: inline; filename=detalle_de_giros.doc');

		$modelo = new consultaGralModel();
		$modeloEnComision = new expedEnComisionModel();
		//  SE COMIENZA A ARMAR EL DOCUMENTO .doc
		echo "<html>";
		echo "\n<body>";

		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Municipalidad de General Pueyrredon</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Sistema de Expedientes</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Honorable Concejo Deliberante</p>";
		echo "\n<hr>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Listado de Expedientes con Detalle de Giros</p>";

		$comision = $modelo->obtenerNombreComision($filtro_para_pdf['l_comision_tipo'], $filtro_para_pdf['l_comision_codigo']);
		if ( $comision != '' )
		{
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>En Comisi&oacute;n de: ".$this->reemplazarPorHTML($comision)."</p>";
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";
		}

		if ($filtro_para_pdf['l_iniciado_tipo'])
		{
			$iniciador = $modelo->obtenerIniciador($filtro_para_pdf['l_iniciado_tipo'], $filtro_para_pdf['l_iniciado_codigo']);
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Iniciado: ".$this->reemplazarPorHTML($iniciador)."</p>";
		}

		if ($filtro_para_pdf['l_autor_tipo'])
		{
			$autor = $modelo->obtenerNombreAutor($filtro_para_pdf['l_autor_tipo'], $filtro_para_pdf['l_autor_codigo']);
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Autor: ".$this->reemplazarPorHTML($autor)."</p>";
		}

		if ($filtro_para_pdf['l_categoria'])
		{
			//SE OBTIENE LA Categoria DEL CRITERIO DE BUSQUEDA
			$categoria = $modelo->obtenerNombreCategoria($filtro_para_pdf['l_categoria']);
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Categor&iacute;a: ".$this->reemplazarPorHTML($categoria)."</p>";
		}

		if ($filtro_para_pdf['l_tema'])
		{
			//SE OBTIENE EL Tema DEL CRITERIO DE BUSQUEDA
			$tema = $modelo->obtenerNombreTema($filtro_para_pdf['l_tema']);
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Tema: ".$this->reemplazarPorHTML($tema)."</p>";
		}

		if ($filtro_para_pdf['l_estado'])
		{
			//SE OBTIENE EL Estado DEL CRITERIO DE BUSQUEDA
			$estado = $modelo->obtenerNombreEstado($filtro_para_pdf['l_estado']);
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Estado: ".$this->reemplazarPorHTML($estado)."</p>";
		}

		if (isset($filtro_para_pdf['l_fecha_desde']) || isset($filtro_para_pdf['l_fecha_hasta']))
		{
			echo chr(9)."\n<p style='margin:0cm;margin-bottom:.0001pt'>Fecha Desde: ";
			echo (isset($filtro_para_pdf['l_fecha_desde'])) ? $this->formatearFecha($filtro_para_pdf['l_fecha_desde']) : '';

			echo chr(9).chr(9).chr(9)."Fecha Hasta: ";
			echo (isset($filtro_para_pdf['l_fecha_hasta'])) ? $this->formatearFecha($filtro_para_pdf['l_fecha_hasta']) : '';

			echo "\n</p>";
		}

		echo "\n<hr>";
		// TITULOS PARA LA CLAVE DEL EXPEDIENTE Y SU FECHA DE INGRESO
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>A&ntilde;o".chr(9)."T.".chr(9)."Nro.".chr(9)."Cpo.".chr(9)."Al.</b>".chr(9).chr(9).chr(9)."Fecha Ingreso</p>";

		echo "\n<hr>";

		$cantidad = count($listado_para_pdf);
		for ($f=0; $f < $cantidad; $f++)
		{
			$ficha = &$listado_para_pdf[$f];
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>".$ficha['anio'].chr(9).$ficha['tipo'].chr(9).$ficha['numero'].chr(9).$ficha['cuerpo'].chr(9).$ficha['alcance']."</b>".chr(9).chr(9).chr(9).$this->formatearFecha($ficha['fecha_entrada_expe'])."</p>";

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Car&aacute;tula: </b>".chr(9).$this->reemplazarPorHTML($ficha['caratula'])."</p>";

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Iniciador: </b>".chr(9).$this->reemplazarPorHTML($ficha['iniciador'])."</p>";

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Categor&iacute;a: </b>".chr(9).$this->reemplazarPorHTML($ficha['categoria'])."</p>";

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Tema: </b>";
			$cantidad_temas = count($ficha['temas']);
			for ($t=0; $t < $cantidad_temas; $t++)
			{
				$tema = &$ficha['temas'][$t];

				echo chr(9).chr(9).$this->reemplazarPorHTML($tema['descripcion_tema']);
				if ( $t != ($cantidad_temas - 1) ) echo '<b>;</b> ';
			}
			echo "\n</p>";

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Autor: </b>";
			$cantidad_autores = count($ficha['autores']);
			for ($a=0; $a < $cantidad_autores; $a++)
			{
				$autor = &$ficha['autores'][$a];

				echo chr(9).chr(9).$this->reemplazarPorHTML($autor['descripcion_grp']);
				if ( $a != ($cantidad_autores - 1) ) echo '<b>;</b> ';
			}
			echo "\n</p>";

			$cantidad_proy = count($ficha['proyectos']);
			for ($p=0; $p < $cantidad_proy; $p++)
			{
				$proyecto = &$ficha['proyectos'][$p];

				echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Proyecto de ".$this->reemplazarPorHTML($proyecto['descripcion_proyecto'])."</b></p>";

				echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'><b>Extracto: </b>".chr(9).$this->reemplazarPorHTML($proyecto['extracto'])."</p>";
			}

			if ($filtro_para_pdf['id_codestado'])
			{
				//SE OBTIENE EL Estado DEL Expediente
				$estado = $modeloEnComision->obtenerEstadoFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance'], $filtro_para_pdf['id_codestado']);

				echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Estado: </b>".chr(9).$this->reemplazarPorHTML($estado[0]['nombre_estado']).chr(9).chr(9).chr(9)." Desde el ".$this->formatearFecha($estado[0]['fecha_estado'])."</p>";
			}

			if ( $filtro_para_pdf['l_comision_tipo'] && $filtro_para_pdf['l_comision_codigo'] )
			{
				//SE OBTIENE LA Comision DEL Expediente
				$comision = $modeloEnComision->obtenerComisionFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);

				echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Comisi&oacute;n: </b>".chr(9).$this->reemplazarPorHTML($comision[0]['comision']).chr(9).chr(9).chr(9)." Desde el ".$this->formatearFecha($comision[0]['fecha_giro'])."</p>";
			}
			//echo "<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";

			//SE OBTIENEN LOS Giros DEL Expediente RESULTANTE
			$giros = $modeloEnComision->obtenerGiros($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);

			//SI EXISTE ALGUN GIRO, SE MUESTRA
			if (isset($giros[0]['comision_codigo']))
			{
				echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Giros: </b></p>";
				$cantidad_giros=count($giros);
				for ($g=0; $g < $cantidad_giros; $g++)
				{
					$giro = &$giros[$g];

					echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>".$giro['comision_codigo'].chr(9).$this->reemplazarPorHTML($giro['descripcion_grp']).chr(9).$this->formatearFecha($giro['fecha_entrada_giro']).chr(9).$this->formatearFecha($giro['fecha_salida_giro']).chr(9).$this->reemplazarPorHTML($giro['dictamen_giro'])."</p>";
				}
			}
			echo "\n<hr>";
		}// FIN DEL for

		// PIE DEL DOCUMENTO A IMPRIMIR CON FECHA, USUARIO Y NOMBRE DE LA PC
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>".date("d/m/Y")."</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>USR. ".$_SESSION['usuario'].chr(9).chr(9).chr(9).chr(9).chr(9)."PC: ".gethostbyaddr($_SERVER['REMOTE_ADDR'])."</p>";

		echo "\n</body>";
		echo "\n</html>";
    }
/******************************************************************************************************************
     SE GENERA EL REPORTE DE Asuntos Entrados EN FORMATO .doc PARA PROCESAR SU CONTENIDO
******************************************************************************************************************/
    public function procesar_texto_asuntos_entrados($listado_para_procesar = '', $filtro_para_pdf = '')
    {
		header("Content-Type: application/msword; charset=UTF-8");
		header('Content-Disposition: inline; filename=asuntos_entrados.doc');

		$modelo = new consultaGralModel();
		//  SE COMIENZA A ARMAR EL DOCUMENTO
		echo "<html>";
		echo "\n<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />";
		echo "\n<body>";

		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Municipalidad de General Pueyrredon</p>";
		echo chr(9).chr(9).chr(9).chr(9).chr(9).chr(9).chr(9)."\n<p style='margin:0cm;margin-bottom:.0001pt'>Sistema de Expedientes</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Honorable Concejo Deliberante</p>";
		echo "\n<hr>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Listado de Asuntos Entrados</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";
		echo chr(9)."\n<p style='margin:0cm;margin-bottom:.0001pt'>Desde Fecha: ".$this->formatearFecha($filtro_para_pdf['l_fecha_desde']).chr(9)."Hasta Fecha: ".$this->formatearFecha($filtro_para_pdf['l_fecha_hasta'])."</p>";
		echo "\n<hr>";

		// SE RECORRE EL LISTADO ORDENADO
		$cantidad = count($listado_para_procesar);
		for ($i=0; $i < $cantidad; $i++)
		{
			// SE TOMA EL REGISTRO A MOSTRAR
			$ficha = &$listado_para_procesar[$i];

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";
			if ($ficha['tipo'] == 'E')
				echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'>Expte";
			elseif ($ficha['tipo'] == 'N')
				echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'>Nota";

			echo " ".$ficha['numero']."-".$ficha['iniciador_codigo']."-".substr($ficha['anio'], -2).": ";

			// 05/04/2018
			// CORREGIDO POR XXXX
			// ***********************
			// Si el iniciador es un Concejal
			if ( $ficha['iniciador_codigo'] == 'CJA' ) {
				$info_autor = $this->modelo_listado->obtenerAutoresFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
				// Se muestra el Autor (el nombre del Concejal)
				echo LibreriaGeneral::reemplazarPorMayusculaAcentuada(strtoupper($info_autor[0]['descripcion_grp'])).': ';
			} else
				// sino se muestra la descripción del iniciador
				echo LibreriaGeneral::reemplazarPorMayusculaAcentuada(strtoupper($ficha['iniciador'])).': ';

			// Se muestra la Carátula
			echo $ficha['caratula'].": ";

			$cantidad_proy = count($ficha['proyectos']);
			for ($p=0; $p < $cantidad_proy; $p++)
			{
				$proyecto = &$ficha['proyectos'][$p];

				echo $this->reemplazarPorHTML($proyecto['extracto']);
			}
			echo "\n</p>";
		} // FIN DEL for DE LOS EXPEDIENTES DE CADA INICIADOR

		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";
		// PIE DEL DOCUMENTO A IMPRIMIR CON FECHA, USUARIO Y NOMBRE DE LA PC
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>".date("d/m/Y")."</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>USR. ".$_SESSION['usuario'].chr(9).chr(9).chr(9).chr(9).chr(9)."PC: ".gethostbyaddr($_SERVER['REMOTE_ADDR'])."</p>";

		echo "\n</body>";
		echo "\n</html>";
    }
/******************************************************************************************************************
     SE GENERA EL REPORTE DE Expedientes para Expurgo EN FORMATO .doc PARA PROCESAR SU CONTENIDO
******************************************************************************************************************/
    public function procesar_texto_expurgo($listado_para_pdf = '', $filtro_para_pdf = '')
    {
		//header("Content-Type:application/vnd.oasis.opendocument.text; charset=UTF-8");
		header("Content-Type: application/msword; charset=UTF-8");
		header('Content-Disposition: inline; filename=expediente_para_expurgo.doc');

		$modelo = new consultaGralModel();
		//  SE COMIENZA A ARMAR EL DOCUMENTO
		echo "<html>";
		echo "\n<body>";

		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Municipalidad de General Pueyrredon</p>";
		echo chr(9).chr(9).chr(9).chr(9).chr(9).chr(9).chr(9)."\n<p style='margin:0cm;margin-bottom:.0001pt'>Sistema de Expedientes</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Honorable Concejo Deliberante</p>";
		echo "\n<hr>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Listado de Expedientes/Notas</p>";

		if ($filtro_para_pdf['l_estado'])
		{
			//SE OBTIENE EL Estado DEL Expediente RESULTANTE
			$estado = $modelo->obtenerNombreEstado($filtro_para_pdf['l_estado']);
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>En Estado: ".chr(9).$this->reemplazarPorHTML($estado)."</p>";
		}
		// Rango de fechas Desde - Hasta
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Fecha Desde: ".chr(9).$this->formatearFecha($filtro_para_pdf['l_fecha_desde']).chr(9).chr(9).chr(9)."Fecha Hasta: ".chr(9).$this->formatearFecha($filtro_para_pdf['l_fecha_hasta'])."</p>";

		echo "\n<hr>";
		// TITULOS PARA LA CLAVE DEL EXPEDIENTE Y SU FECHA DE INGRESO
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>A&ntilde;o".chr(9)."T.".chr(9)."Nro.".chr(9)."Cpo.".chr(9)."Al.</b>".chr(9).chr(9).chr(9)."Fecha Ingreso</p>";

		$cantidad = count($listado_para_pdf);
		for ($f=0; $f < $cantidad; $f++)
		{
			$ficha = &$listado_para_pdf[$f];

			echo "\n<hr>";
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>".$ficha['anio'].chr(9).$ficha['tipo'].chr(9).$ficha['numero'].chr(9).$ficha['cuerpo'].chr(9).$ficha['alcance']."</b>".chr(9).chr(9).chr(9).$this->formatearFecha($ficha['fecha_entrada_expe'])."</p>";

			$cantidad_proy = count($ficha['proyectos']);
			for ($p=0; $p < $cantidad_proy; $p++)
			{
				$proyecto = &$ficha['proyectos'][$p];

				echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>".$proyecto['orden_proyecto'].chr(9).$this->reemplazarPorHTML($proyecto['descripcion_proyecto']);

					$datos_sancion = $modelo->obtenerDatosSancion($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance'], $ficha['orden_proyecto']);

				// Si posee Sanción
				//if ( $datos_sancion != '' )
				//{
					echo chr(9)."<b>Prom. :</b>".$this->formatearFecha($datos_sancion[0]['fecha_promulga']).chr(9).$datos_sancion[0]['numero_promulga'];
					echo chr(9)."<b>Dec. :</b>".$datos_sancion[0]['decreto_promulga'];
					echo chr(9)."<b>Sanc. :</b>".$this->formatearFecha($datos_sancion[0]['fecha_sancion']).chr(9).$datos_sancion[0]['numero_sancion'];
					echo "\n</p>";
				//}

				echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'>".$this->reemplazarPorHTML($proyecto['extracto'])."</p>";
			}
		}

		echo "\n<hr>";
		// PIE DEL DOCUMENTO A IMPRIMIR CON FECHA, USUARIO Y NOMBRE DE LA PC
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>".date("d/m/Y")."</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>USR. ".$_SESSION['usuario'].chr(9).chr(9).chr(9).chr(9).chr(9)."PC: ".gethostbyaddr($_SERVER['REMOTE_ADDR'])."</p>";

		echo "\n</body>";
		echo "\n</html>";
    }
/******************************************************************************************************************
     SE GENERA EL REPORTE DE Expedientes/Notas en Comision Vencidos EN FORMATO .doc PARA PROCESAR SU CONTENIDO
******************************************************************************************************************/
    public function procesar_texto_exped_en_comision_vencidos($listado_para_documento_de_texto = '', $filtro_para_texto = '')
    {
		header("Content-Type: application/msword; charset=UTF-8");
		header('Content-Disposition: inline; filename=expedientes_y_notas_en_comision_vencidos.doc');

		$modelo = new consultaGralModel();
		$modeloEnComision = new expedEnComisionModel();
		//  SE COMIENZA A ARMAR EL DOCUMENTO
		echo "<html>";
		echo "\n<body>";

		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Municipalidad de General Pueyrredon</p>";
		echo chr(9).chr(9).chr(9).chr(9).chr(9).chr(9).chr(9)."\n<p style='margin:0cm;margin-bottom:.0001pt'>Sistema de Expedientes</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Honorable Concejo Deliberante</p>";
		echo "\n<hr>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Listado de Expedientes/Notas en Comisi&oacute;n Vencidos</p>";

		$comision = $modelo->obtenerNombreComision($filtro_para_texto['l_comision_tipo'], $filtro_para_texto['l_comision_codigo']);
		if ( $comision != '' )
		{
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Para Tratamiento en la Comisi&oacute;n de ".utf8_decode($comision)."</p>";
		}
		echo "\n<hr>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>A&ntilde;o".chr(9)."T.".chr(9)."Nro.".chr(9)."Letra".chr(9)."Car&aacute;tula</b></p>";
		echo "\n<hr>";

		// SE RECORREN LOS EXPEDIENTES A MOSTRAR
		$listado_por_marca = Array();
		$cantidad = count($listado_para_documento_de_texto);

		for ($i=0; $i < $cantidad; $i++)
		{
			$ficha = &$listado_para_documento_de_texto[$i];
			if ( $ficha['dias'] > 120 )
			{
				echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>".$ficha['anio']." ".$ficha['tipo']." ".$ficha['numero']." ".$ficha['iniciador_codigo']." ".utf8_decode($ficha['caratula'])."</b></p>";
				echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";

				$cantidad_proy = count($ficha['proyectos']);
				for ($p=0; $p < $cantidad_proy; $p++)
				{
					$proyecto = &$ficha['proyectos'][$p];
					echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'>".utf8_decode($proyecto['extracto'])."</p>";
					echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";

					// SE OBTIENE EL ULTIMO GIRO
					$ultimo_giro = $modeloEnComision->obtenerComisionFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);

					echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>En Comision desde ".$this->formatearFecha($ultimo_giro[0]['fecha_giro'])."	Cantidad de d&iacute;as: ".$ficha['dias']."</p>";
					echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";
				} // FIN DEL FOR DE LOS PROYECTOS
			} // FIN DEL IF DE LOS 120 DIAS
		} // FIN DEL for DE LOS EXPEDIENTES

		echo "\n<hr>";

		 // PIE DEL DOCUMENTO A IMPRIMIR CON FECHA, USUARIO Y NOMBRE DE LA PC
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>".date("d/m/Y")."</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>USR. ".$_SESSION['usuario'].chr(9).chr(9).chr(9).chr(9).chr(9)."PC: ".gethostbyaddr($_SERVER['REMOTE_ADDR'])."</p>";

		echo "\n</body>";
		echo "\n</html>";
    }

/*******************************************************************************************************************************************/
    public function obtenerNombreMarcaComision($marca_comision)
    {
		switch ($marca_comision)
		{
			case '1':
				$nombre_marca = "Para tratar";
				break;
			case '2':
				$nombre_marca = "Para su conocimiento";
				break;
			case '3':
				$nombre_marca = "Para archivo";
				break;
			case '4':
				$nombre_marca = "Para pr&oacute;rroga";
				break;
		}
		return $nombre_marca;
    }

    public function ordenarPorCampoDeterminado ($listado, $campo, $sentido = false)
    {
		$position = array();
		$newRow = array();
		foreach ($listado as $key => $row) {
			$position[$key]  = $row[$campo];
			$newRow[$key] = $row;
		}
		if ($sentido) {
			arsort($position);
		}
		else {
			asort($position);
		}
		$returnArray = array();
		foreach ($position as $key => $pos) {
			$returnArray[] = $newRow[$key];
		}
		return $returnArray;
    }
/******************************************************************************************************************
     PARA AGRUPAR LOS VALORES DE UN ARRAY SEGÚN UN CAMPO DETERMINADO
******************************************************************************************************************/
    private function agruparArray($array,$groupkey)
    {
		if (count($array)>0)
		{
			$keys = array_keys($array[0]);
			$removekey = array_search($groupkey, $keys);
			if ($removekey===false)
				return array("Clave \"$groupkey\" no existe");
			else
				unset($keys[$removekey]);
			$groupcriteria = array();
			$return=array();

			foreach($array as $value)
			{
				$item=null;
				foreach ($keys as $key)
				{
					$item[$key] = $value[$key];
				}
				$busca = array_search($value[$groupkey], $groupcriteria);
				if ( $busca === false )
				{
					$groupcriteria[]=$value[$groupkey];
					$return[]=array($groupkey=>$value[$groupkey],'registros'=>array());
					$busca=count($return)-1;
				}
				$return[$busca]['registros'][]=$item;
			}
			return $return;
		}
		else
		{
			return array();
		}
    }
/********************************************************************************************************************
		LISTADO DE INFORMES, VENCIDOS O TODOS
*********************************************************************************************************************/
	public function listarInformes($listado = '', $listadoComisiones = '', $filtro = '')
	{
	    if ($mensaje != '') echo '<div class="abm_mensaje_resultado">'.$mensaje.'</div>';
	?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

	    <div id="precarga_modal" style="display:none"></div>
	    <div id="contenidoAjaxResultadoListados" class="msc_gral msc_texto" style="width:700px;">

			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>

			<form action="listados/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">

				<input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
				<input type="hidden" name="accion" id="accion" value="listarInformes" />
				<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
				<input type="hidden" name="l_tipo_listado" id="l_tipo_listado" value="<?php echo $filtro['l_tipo_listado']; ?>" />
				<input type="hidden" name="l_enviado" id="l_enviado" value="enviado" />
				<input type="hidden" name="l_cantidad_TOTAL" id="l_cantidad_TOTAL" value="<?php echo $filtro['l_cantidad']; ?>" />

				<div id="dragger_titulo_listado" class="msc_titulos degradado">Listado de Informes</div>
				<div style="height:10px;font-size:0;"></div>

				<div style="height:125px;">

					<div style="width:550px;height:125px;float:left;">

						<div class="msl_criterio_fechas_margen"></div>
						<div class="msl_criterio_fechas_nombres">
							<div class="msc_dato_filtro">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Desde el:</div>
						</div>
						<?php
							if ( $filtro['l_enviado'] && $listado != '' )
								$l_fecha_desde = $this->formatearFecha($_SESSION['filtro_LISTADOS']['l_fecha_desde']);
							else
								// SE CALCULA LA FECHA 10 AÑOS ANTES DEL ACTUAL
								$l_fecha_desde = '01/01/'.( date("Y")-10 );
							?>
						<div class="msl_criterio_calendario">
							<input type="text" id="l_fecha_desde" name="l_fecha_desde" value="<?php echo $l_fecha_desde; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
							<input type="image" id="msl_btffecha_desde" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Desde" width="16" height="16">
						</div>

						<div class="msl_criterio_fechas_nombres">
							<div class="msc_dato_filtro">Hasta el:</div>
						</div>
						<div class="msl_criterio_calendario">
							<input type="text" id="l_fecha_hasta" name="l_fecha_hasta" value="<?php echo($_SESSION['filtro_LISTADOS']['l_fecha_hasta']) ? $this->formatearFecha($_SESSION['filtro_LISTADOS']['l_fecha_hasta']) : date("d/m/Y") ; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
							<input type="image" id="msl_btffecha_hasta" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Hasta" width="16" height="16">
						</div>

						<div style="height:25px;clear:both">
							<div class="msl_criterio_fechas_margen"></div>
							<div class="msl_criterio_fechas_nombres">
								<div class="msc_dato_filtro">Fecha de listado:</div>
							</div>
							<div class="msl_criterio_calendario">
								<input type="text" id="l_fecha_de_listado" name="l_fecha_de_listado" value="<?php echo($_SESSION['filtro_LISTADOS']['l_fecha_de_listado']) ? $_SESSION['filtro_LISTADOS']['l_fecha_de_listado'] : date("d/m/Y"); ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" maxlength="10" class="msc_ffecha" />
								<input type="image" id="msl_bt_l_fecha_de_listado" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha de listado." width="16" height="16">
							</div>
							<div class="msl_criterio_fechas_margen_medio"></div>
							<div class="msl_criterio_fechas_nombres">
								<input type="hidden" name="l_vencidos" id="l_vencidos" value="<?php echo($filtro['l_vencidos']) ? $filtro['l_vencidos'] : 0; ?>" />
								<input type="checkbox" name="vencidos" id="vencidos" >&nbsp;S&oacute;lo Vencidos
							</div>
							<div class="msl_criterio_fechas_margen_medio"></div>
							<div class="msl_criterio_fechas_nombres">
								<input type="hidden" name="l_solo_habilitado" id="l_solo_habilitado" value="<?php echo($_SESSION['filtro_LISTADOS']['l_solo_habilitado']) ? $_SESSION['filtro_LISTADOS']['l_solo_habilitado'] : 1; ?>" />
								<input type="checkbox" name="habilitado" id="habilitado" onchange="javascript:chequear('l_solo_habilitado');refrescarCombos($('l_solo_habilitado').value);" >&nbsp;S&oacute;lo Comisiones Habilitadas
							</div>
						</div>
						<div class="msl_criterio_seccion_comisiones_y_estados">
							<div class="msl_criterio_seccion_nombres_y_combos">
								<div class="msl_criterio_margen"></div>
								<div class="msl_criterio_nombres" style="width:85px">
									<div class="msc_dato_filtro">En comisi&oacute;n de:</div>
								</div>
								<div class="msl_criterio_combos">
									<div id="msl_dato_filtro_comision" class="msc_dato_filtro">
										<select id="c_comision" name="l_comision" class="msl_combo" style="width:207px;">
											<option value="0">0, TODAS</option>
											<?php
											$cant_comisiones = count($listadoComisiones);
											for ($c=0; $c < $cant_comisiones; $c++) {
												$comision = &$listadoComisiones[$c];
											?>
												<option value="<?php echo $comision['tipo_grp'].'-'.$comision['codigo_grp']; ?>"><?php echo $comision['tipo_grp'].', '.$comision['codigo_grp'].', '.$comision['descripcion_grp']; ?></option>
											<?php
											}
											?>
										</select>
										&nbsp;
										<a id="imagen_zoom_comisiones" href="javascript:modalGaby('consultas/index.php?controlador=consulta_gral&accion=pedirNombreComisionModal&c_solo_habilitado='+$('l_solo_habilitado').value+'');" title="Buscar por Nombre de Comisi&oacute;n"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="msl_criterio_seccion_botones">
						<?php
						if ($filtro['l_enviado'] && $listado != '') {
						?>
							<div id="contenedor_btImpresion" class="msc_boton degradado">
								<a id="btImpresion" href="#" title="Formato para Impresi&oacute;n" >
								  <img src="imagenes/barra/print_16x16.gif" width="16" height="16" align="left" />&nbsp;&nbsp;&nbsp;Impresi&oacute;n
								</a>
							</div>

							<div id="contenedor_btProcesarTexto" class="msc_boton degradado">
								<a id="btProcesarTexto" href="#" title="Procesador de Texto" >
								  <img src="imagenes/iconos_office/doc.jpg" width="16" height="16" align="left" />&nbsp;&nbsp;&nbsp;Proc.Texto
								</a>
							</div>
						<?php
						}
						?>
						<div class="msc_boton degradado">
							<a href="javascript:buscar();">
								<img src="imagenes/zoom_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Buscar
							</a>
						</div>
						<div class="msc_boton degradado">
							<a href="javascript:limpiar();">
								<img src="imagenes/barra/limpiar.jpeg" width="16" height="16" />&nbsp;&nbsp;Limpiar
							</a>
						</div>
						<div class="msc_boton degradado">
							<a href="javascript:cerrarModalNueva();">
								<img src="imagenes/barra/error_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Cerrar
							</a>
						</div>
					</div><!-- FIN DE msl_criterio_seccion_botones -->
				</div>
				<?php
				if ( $filtro['l_enviado'] != '' && $listado != '' ) {
					// Si se desean mostrar sólo los Vencidos
					//if ( $filtro['l_vencidos'] == 1 )
						// Se redefine la cantidad de páginas
						//$filtro['l_nro_paginas'] = ceil(count($listado) / 5);

					$urlPrimero   = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listarInformes&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_vencidos='+$('l_vencidos').value+'&l_fecha_de_listado='+$('l_fecha_de_listado').value+'&l_comision='+$('c_comision').value+'&l_tipo_listado=informes&l_enviado=enviado&l_pagina=1', 'contenidoAjaxResultadoListados');";
					$urlAnterior  = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listarInformes&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_vencidos='+$('l_vencidos').value+'&l_fecha_de_listado='+$('l_fecha_de_listado').value+'&l_comision='+$('c_comision').value+'&l_tipo_listado=informes&l_enviado=enviado&l_pagina=".$filtro['l_pagina_ant']."', 'contenidoAjaxResultadoListados');";
					$urlSiguiente = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listarInformes&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_vencidos='+$('l_vencidos').value+'&l_fecha_de_listado='+$('l_fecha_de_listado').value+'&l_comision='+$('c_comision').value+'&l_tipo_listado=informes&l_enviado=enviado&l_pagina=".$filtro['l_pagina_sgte']."', 'contenidoAjaxResultadoListados');";
					$urlUltimo    = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listarInformes&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_vencidos='+$('l_vencidos').value+'&l_fecha_de_listado='+$('l_fecha_de_listado').value+'&l_comision='+$('c_comision').value+'&l_tipo_listado=informes&l_enviado=enviado&l_pagina=".$filtro['l_nro_paginas']."', 'contenidoAjaxResultadoListados');";
					?>

					<!-- PAGINADOR -->
					<div class="msc_paginador">
						<div class="msc_margen_paginador"></div>
						<div class="msc_flechas_paginador">
							<?php
								if ($filtro['l_pagina'] != 1) {
							?>
								<a id="btPrimero" title="Primer Registro" href="javascript:<?php echo $urlPrimero; ?>">
									<img id="imgPrimero" src="imagenes/barra/b_firstpage.png" width="14" height="14" align="center" />
								</a>
							<?php } else { ?>
								<a id="btPrimero" href="#">
									<img id="imgPrimero" src="imagenes/barra/bd_firstpage.png" width="14" height="14" align="center" />
								</a>
							<?php }
								if ($filtro['l_pagina_ant'] != 0) {
							?>
								<a id="btAnterior" title="Registro Anterior" href="javascript:<?php echo $urlAnterior; ?>">
									<img id="imgAnterior" src="imagenes/barra/b_prevpage.png" width="14" height="14" align="center" />
								</a>
							<?php } else { ?>
								<a id="btAnterior" href="#">
									<img id="imgAnterior" src="imagenes/barra/bd_prevpage.png" width="14" height="14" align="center" />
								</a>
							<?php } ?>
						</div>
						<div class="msc_detalle_paginador"><?php echo $filtro['l_pagina'].' de '.$filtro['l_nro_paginas']; ?></div>
						<div class="msc_flechas_paginador">
							<?php
							if ($filtro['l_pagina'] != $filtro['l_nro_paginas']) {
							?>
								<a id="btSiguiente" title="Registro Siguiente" href="javascript:<?php echo $urlSiguiente; ?>">
									<img id="imgSiguiente" src="imagenes/barra/b_nextpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							} else {
							?>
								<a id="btSiguiente" href="#">
									<img id="imgSiguiente" src="imagenes/barra/bd_nextpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}

							if ($filtro['l_pagina'] != $filtro['l_nro_paginas']) {
							?>
								<a id="btUltimo" title="Ultimo Registro" href="javascript:<?php echo $urlUltimo; ?>">
									<img id="imgUltimo" src="imagenes/barra/b_lastpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							} else {
							?>
								<a id="btUltimo" href="#">
									<img id="imgUltimo" src="imagenes/barra/bd_lastpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							?>
						</div>
					</div><!-- FIN DEL PAGINADOR -->
				<?php
				}
				?>

				<div class="msl_borde1">
					<div id="msl_borde2" class="msl_borde2">
						<?php
						if ( !$filtro['l_enviado'] )
						{
						?>
							<!-- FONDO MOSTRADO ANTES DE LA BUSQUEDA  -->
							<div class="msc_fondo_item">Item en Listado de Informes</div>
							<div class="msc_fondo_item">Item en Listado de Informes</div>
							<div class="msc_fondo_item">Item en Listado de Informes</div>
							<div class="msc_fondo_item">Item en Listado de Informes</div>
							<div class="msc_fondo_item">Item en Listado de Informes</div>
						<?php
						}
						else // A
						{
							if ( $listado == '' )
							{
								echo '<br><h1>Sin resultados</h1>';
							}
							else // B
							{
								//Se crea una instancia del modelo
								$modelo = new expedEnComisionModel();

								// SI SE DESEAN MOSTRAR TODOS
								if ( $filtro['l_vencidos'] != 1 )
								{
									$inicio = 0;

									$final = count($listado);
								}
								else
								{
									$inicio = ($filtro['l_pagina']-1)*5;

									$final = $filtro['l_pagina']*5;
								}

								//SE LISTAN LAS FICHAS DE LOS EXPEDIENTES
								for ($exp=$inicio; $exp < $final; $exp++)
								{
									$ficha = &$listado[$exp];

									if ( $ficha['anio'] )
									{
								?>
										<div class="mscpa_gral">
											<div style="height:19px;clear:both;">
												<div class="mscpa_expediente">

													<?php echo $ficha['anio']; ?>
													&nbsp;
													<?php echo $ficha['tipo']; ?>
													&nbsp;
													<?php echo $ficha['numero']; ?>
													&nbsp;
													<?php echo $ficha['cuerpo']; ?>
													&nbsp;
													<?php echo $ficha['alcance']; ?>

												</div>
												<div class="mscpa_titulo mscpa_tit_caratula">Car&aacute;tula:</div>
												<div class="mscpa_caratula">
													<?php echo $ficha['caratula']; ?>
												</div>
												<!-- PRIMERO EL VALOR Y LUEGO EL TITULO POR LA POSICION RIGHT -->
												<div class="mscpa_fecha_entrada"><?php echo $this->formatearFecha($ficha['fecha_entrada_expe']); ?></div>
												<div class="mscpa_titulo mscpa_tit_fecha">Fecha Expe.:</div>
											</div>

											<div style="height:19px;clear:both;">
												<div class="mscpa_titulo">Detalle:&nbsp;</div>
												<div class="mscpa_estado_comision"><?php echo $ficha['detalle_informe']; ?></div>
												<div class="lec_fecha_estado"><?php echo " <b>Pedido el</b> ".$this->formatearFecha($ficha['fecha_pedido_informe']); ?></div>
												<div class="lec_cantidad_dias_expe" ><?php echo $ficha['dias']." d&iacute;as"; ?></div>
											</div>

											<?php
											//SE OBTIENEN LOS Proyectos DEL Expediente RESULTANTE (LUEGO MOSTRAR UNA FICHA POR CADA UNO!!!)
											$proyectos = $modelo->obtenerProyectosFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);

											//SE MUESTRAN LOS Proyectos DEL Expediente RESULTANTE
											$cant_p = count($proyectos);
											for ($p=0; $p < $cant_p; $p++)
											{
												$proyecto = &$proyectos[$p];
											?>
												<div style="height:19px;clear:both;">
													<div class="mscpa_titulo">Proyecto:&nbsp;</div>
													<div class="lcg_nrotipo_proyecto"><?php echo $p+1; //CONTADOR DE PROYECTOS ?></div>
													<div class="lcg_descripcion_proyecto"><?php echo $proyecto['descripcion_proyecto']; ?></div>
												</div>
												<div style="height:40px;clear:both;">
													<div class="mscpa_titulo" style="height:37px;">Extracto:&nbsp;</div>
													<div class="mscpa_extracto"><?php echo $proyecto['extracto']; ?></div>
												</div>
											<?php
											}
											?>

											<?php
											if ( $filtro['l_comision_tipo'] != '' && $filtro['l_comision_codigo'] != '' )
											{
												//SE OBTIENE LA Comision DEL Expediente RESULTANTE
												$comision = $modelo->obtenerComisionFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
											?>
												<div style="height:19px;clear:both;">
													<div class="mscpa_titulo">Comisi&oacute;n:&nbsp;</div>
													<div class="mscpa_estado_comision"><?php echo $comision[0]['comision']; ?></div>
													<div class="lec_fecha_estado"><?php echo " <b>Desde el</b> ".$this->formatearFecha($comision[0]['fecha_giro']); ?></div>
												</div>
											<?php
											}
											?>
											<div style="height:5px;font-size:0;clear:both;"></div>
											<div style="height:19px;clear:both;">
												<div class="cyl_boton_ver_expediente_y_ficha">
													<a href="javascript:verExpediente('<?php echo $ficha['anio']; ?>', '<?php echo $ficha['tipo']; ?>', '<?php echo $ficha['numero']; ?>', '<?php echo $ficha['cuerpo']; ?>', '<?php echo $ficha['alcance']; ?>', 'informes');">Ver exped.</a>
												</div>
												<div class="cyl_boton_ver_expediente_y_ficha">
													<a href="javascript:modalGabyFicha('consultas/index.php?controlador=ficha&accion=ver_ficha_modal&anio=<?php echo $ficha['anio']; ?>&tipo=<?php echo $ficha['tipo']; ?>&numero=<?php echo $ficha['numero']; ?>&cuerpo=<?php echo $ficha['cuerpo']; ?>&alcance=<?php echo $ficha['alcance']; ?>');">Ficha</a>
												</div>
											</div>
										</div>
								<?php
									}
								} // FIN DEL for
								?>
								<script>
									var scroller = new Fx.Scroll($('msl_borde2'));
									scroller.toTop();
								</script>
							<?php
							} //FIN DEL else B
						} //FIN DEL else A
						?>
					</div><!-- FIN DE msl_borde2 -->
				</div><!-- FIN DE msl_borde1 -->

			</form>
	    </div>
	    <script>
			//CALENDARIO PARA LA FECHA DESDE
			var calDesde = new Zapatec.Calendar.setup({

				inputField:"l_fecha_desde",
				ifFormat:"%d/%m/%Y",
				button:"msl_btffecha_desde",
				showsTime:false
			});
			//CALENDARIO PARA LA FECHA HASTA
			var calHasta = new Zapatec.Calendar.setup({

				inputField:"l_fecha_hasta",
				ifFormat:"%d/%m/%Y",
				button:"msl_btffecha_hasta",
				showsTime:false
			});

			//CALENDARIO PARA LA FECHA DE LISTADO
			var calFechaDeListado = new Zapatec.Calendar.setup({

				inputField:"l_fecha_de_listado",
				ifFormat:"%d/%m/%Y",
				button:"msl_bt_l_fecha_de_listado",
				showsTime:false
			});

			function buscar()
			{
				var mensaje = '';
				var error = false;

				if ( $('l_fecha_desde').value == '' )
				{
					error = true;
					mensaje = "Debe ingresar un rango de Fecha.<br>";
				}

				if ( $('l_fecha_hasta').value == '' )
				{
					error = true;
					mensaje = "Debe ingresar un rango de Fecha.<br>";
				}

				if ( $('c_comision').disabled == false && $('c_comision').value == 0 )
				{
					error = true;
					mensaje = "Debe ingresar una Comisi"+'\u00f3'+"n.";
				}

				if ( esLaFechaMayor($('l_fecha_desde').value, $('l_fecha_hasta').value) ){
					error = true;
					mensaje = "La fecha Desde debe ser menor a la fecha Hasta en el criterio de b"+'\u00fa'+"squeda.";
				}

				if (error)
				{
					alert(mensaje);
				}
				else
				{
					enviarForm('formListados', 'listados', 'contenidoAjaxResultadoListados');
				}
			}

			function limpiar()
			{
				$('l_fecha_desde').value = '<?php echo '01/01/'.( date("Y")-10 ); ?>';
				$('l_fecha_hasta').value = '<?php echo date("d/m/Y"); ?>';
				$('l_fecha_de_listado').value = '<?php echo date("d/m/Y"); ?>';
				$('c_comision').value = 0;
			}

			$('c_comision').value = <?php echo ($_SESSION['filtro_LISTADOS']['l_comision']) ? "'".$_SESSION['filtro_LISTADOS']['l_comision']."'" : 0 ; ?>;

			<?php
			if ( $filtro['l_vencidos'] == '' )
			{
			?>
				$('vencidos').checked = false;
				$('l_vencidos').value = 0;
			<?php
			}
			elseif ( $filtro['l_vencidos'] == 0 )
			{
			?>
				$('vencidos').checked = false;
				$('l_vencidos').value = 0;
			<?php
			}
			else
			{
			?>
				$('vencidos').checked = true;
				$('l_vencidos').value = 1;
			<?php
			}
			?>

			$('vencidos').addEvent('change', function()
			{
				if ($('vencidos').checked == true)
				{
					$('l_vencidos').value = 1;
				}
				else
				{
					$('l_vencidos').value = 0;
				}
			});

			<?php
			if ( $filtro['l_enviado'] )
			{
			?>
				$('btImpresion').addEvent('click', function()
				{
				    if ( $('l_cantidad_TOTAL').value >= 5000 )
				    {
						alert("El resultado de su consulta es muy largo para imprimir.");
				    }
				    else
				    {
						$('btImpresion').setProperty('href', 'listados/index.php?controlador=exped_en_comision&accion=armar_listado_completo_listadoInformes&l_tipo_listado=informes&formato=impresion&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_fecha_de_listado='+$('l_fecha_de_listado').value+'&l_comision='+$('c_comision').value+'&l_vencidos='+$('l_vencidos').value+'');

						$('btImpresion').setProperty('target', '_blank');
						$('contenedor_btImpresion').setStyle('display', 'none');
						$('contenedor_btProcesarTexto').setStyle('display', 'none');
				    }
				});

				$('btProcesarTexto').addEvent('click', function()
				{
					if ( $('l_cantidad_TOTAL').value >= 5000 )
					{
						alert("El resultado de su consulta es muy largo para procesar su texto.");
					}
					else
					{
						$('btProcesarTexto').setProperty('href', 'listados/index.php?controlador=exped_en_comision&accion=armar_listado_completo_listadoInformes&l_tipo_listado=informes&formato=texto&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_fecha_de_listado='+$('l_fecha_de_listado').value+'&l_comision='+$('c_comision').value+'&l_vencidos='+$('l_vencidos').value+'');

						$('btProcesarTexto').setProperty('target', '_blank');
						$('contenedor_btImpresion').setStyle('display', 'none');
						$('contenedor_btProcesarTexto').setStyle('display', 'none');
					}
				});
			<?php
			}
			?>

		    var menuDrag = new Drag.Move($('contenidoAjaxResultadoListados'), {
			    handle: $('dragger_titulo_listado')
			});

			function refrescarCombos(habilitado)
			{
				refrescarCombo('listados/index.php?controlador=exped_en_comision&accion=refrescarComboComisiones&habilitado='+habilitado+'&comision=<?php echo $_SESSION['filtro_LISTADOS']['l_comision']; ?>','msl_dato_filtro_comision');
			}

			<?php
			/**/
			// 27/11/2012
			// SI SE VUELVE DE HABER VISTO EL EXPEDIENTE
			if ( $_SESSION['cerrar_modal_LISTADOS'] == 'no' )
			{
				$_SESSION['cerrar_modal_LISTADOS'] = null;
			?>
				refrescar('listados/index.php?controlador=exped_en_comision&accion=listarInformes&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_vencidos='+$('l_vencidos').value+'&l_fecha_de_listado='+$('l_fecha_de_listado').value+'&l_comision='+$('c_comision').value+'&l_tipo_listado=informes&l_enviado=enviado&l_pagina=<?php echo $_SESSION['filtro_LISTADOS']['l_pagina']; ?>', 'contenidoAjaxResultadoListados');
			<?php
			}
			/**/
			?>

			$('l_fecha_desde').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
					$('msl_bt_l_fecha_de_listado').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;
						$('msl_bt_l_fecha_de_listado').disabled = true;

						buscar();
					}
				}
			});

			$('l_fecha_hasta').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
					$('msl_bt_l_fecha_de_listado').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;
						$('msl_bt_l_fecha_de_listado').disabled = true;

						buscar();
					}
				}
			});

			$('l_fecha_de_listado').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
					$('msl_bt_l_fecha_de_listado').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;
						$('msl_bt_l_fecha_de_listado').disabled = true;

						buscar();
					}
				}
			});
		</script>
	<?php
	}
/*************************************************************************************************************************
      SE GENERA EL REPORTE DE INFORMES PARA SU IMPRESION
*************************************************************************************************************************/
    public function generar_formato_de_impresion_informes($listado_para_pdf = '', $filtro_para_pdf = '')
	{
		header("Content-Type: text/html; charset=UTF-8");
		$modelo = new consultaGralModel();
		$modeloEnComision = new expedEnComisionModel();
	?>
		<style type="text/css">
			.imp_texto {
				font-family: Arial;
				font-size: 12px;
			}
			.imp_titulo_general{
				padding:10px 0 0 150px;
				font-size: 18px;
				font-weight:700;
				text-decoration:underline;
				text-align:left;
				border-left:2px solid #000;
				border-right:2px solid #000;
			}
			.imp_subtitulo_general{
				padding:10px 0 20px 10px;
				font-size:14px;
				font-weight:700;
				text-align:left;
				border-left:2px solid #000;
				border-right:2px solid #000;
			}
			/************************************************************/
			.imp_titulos_de_clave{
				height: 25px;
				border: 2px solid #000;
			}
			.imp_titulos_de_clave_izq{
				width: 395px;
				height: 20px;
				padding: 5px 0 0 5px;
				float: left;
				font-weight: 700;
			}
			.imp_titulos_de_clave_der{
				width: 100px;
				height: 20px;
				padding: 5px 5px 0 0;
				float: right;
				text-align: right;
			}
			/************************************************************/
			.imp_ficha_titulos_de_clave{
				height:25px;
				border-left:2px solid #000;
				border-right:2px solid #000;
				clear:both;
			}
			.imp_ficha_titulos_de_clave_izq {
				width: 580px;
				height: 20px;
				padding: 5px 0 0 5px;
				float: left;
				font-weight: 700;
			}
			.imp_ficha_detalle_informe {
				width: 300px;
				height:20px;
				padding:5px 0 0 5px;
				float:left;
			}
			.imp_ficha_fecha_pedido_informe {
				width: 180px;
				height:20px;
				padding: 5px 0 0 5px;
				float: right;
			}
			.imp_ficha_extracto{
				padding:5px 5px 10px 5px;
				text-align:left;
				border-left:2px solid #000;
				border-right:2px solid #000;
			}
			.imp_ficha_datos_comision {
				width: 270px;
				height:20px;
				padding:5px 0 0 5px;
				float:left;
			}
			.imp_ficha_cantidad_dias {
				width: 175px;
				height: 20px;
				padding: 5px 0 0 5px;
				float: right;
			}
			.linea_horizontal{
				height:1px;
				font-size:0;
				border-left:2px solid #000;
				border-right:2px solid #000;
				border-bottom:2px solid #000;
			}
			.btImprimir{
			  text-align:center;
			  padding:7px;
			}
		</style>

		<style media="print" type="text/css">
			#btImprimir {
			   display:none;
			}
		</style>

		<div id="btImprimir" class="btImprimir"><input name="imprimir" type="button" class="button" value="Imprimir Informe" onClick="window.print();"></div>

		<?php $this->encabezado_reporte(); ?>

		<div class="imp_texto imp_titulo_general">Listado de Informes</div>
		<div class="imp_texto imp_subtitulo_general"></div>

		<?php $this->criterio_busqueda_reporte($filtro_para_pdf); ?>

		<div class="imp_texto imp_titulos_de_clave">
			<div class="imp_titulos_de_clave_izq">A&ntilde;o&nbsp;&nbsp;&nbsp;T.&nbsp;&nbsp;Nro.&nbsp;&nbsp;Cpo.&nbsp;Alc.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Car&aacute;tula</div>
			<div class="imp_titulos_de_clave_der"><b>Fecha Ingreso</b></div>
		</div>

		<?php
		$cantidad_expedientes = 0;
		$cantidad_notas = 0;
		// SE RECORREN LOS EXPEDIENTES A MOSTRAR
		$cantidad = count($listado_para_pdf);
		for ($i=0; $i < $cantidad; $i++)
		{
			$ficha = &$listado_para_pdf[$i];

			if ($ficha['tipo']=='E') $cantidad_expedientes++;
			elseif ($ficha['tipo']=='N') $cantidad_notas++;
		?>
			<div class="imp_texto imp_ficha_titulos_de_clave">
				<div class="imp_ficha_titulos_de_clave_izq" style="display: block;" >
					<?php echo $ficha['anio']; ?>&nbsp;
					<?php echo $ficha['tipo']; ?>&nbsp;
					<?php echo $ficha['numero']; ?>&nbsp;&nbsp;&nbsp;
					<?php echo $ficha['cuerpo']; ?>&nbsp;&nbsp;&nbsp;&nbsp;
					<?php echo $ficha['alcance']; ?>&nbsp;&nbsp;&nbsp;&nbsp;
					<?php echo $ficha['caratula']; ?>
				</div>
				<div class="imp_titulos_de_clave_der">
					<?php echo $this->formatearFecha($ficha['fecha_entrada_expe']); ?>
				</div>
			</div>

			<div class="imp_texto imp_ficha_titulos_de_clave">
				<div class="imp_ficha_detalle_informe">
					Detalle:&nbsp;<?php echo $ficha['detalle_informe']; ?>
				</div>
				<div class="imp_ficha_fecha_pedido_informe">
					<?php echo " Pedido el ".$this->formatearFecha($ficha['fecha_pedido_informe']); ?>
				</div>
			</div>

			<?php
			//SE OBTIENEN LOS Proyectos DEL Expediente RESULTANTE
			$proyectos = $modelo->obtenerProyectosFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);

			$cantidad_proy = count($proyectos);
			for ($p=0; $p < $cantidad_proy; $p++)
			{
				$proyecto = &$proyectos[$p];

				echo "<div class='imp_texto imp_ficha_extracto'>".$proyecto['extracto']."</div>";
			}
			?>

			<?php
			$ultimo_giro = $modeloEnComision->obtenerComisionFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
			?>
			<div class="imp_texto imp_ficha_titulos_de_clave">
				<div class="imp_ficha_datos_comision">
					En Comision desde <?php echo $this->formatearFecha($ultimo_giro[0]['fecha_giro']); ?>
				</div>
				<div class="imp_ficha_cantidad_dias">
					Cantidad de d&iacute;as: <?php echo $ficha['dias']; ?>
				</div>
			</div>
			<div class='linea_horizontal'></div>
		<?php
		}

		echo $this->pie_reporte();
		?>
		<div id="btImprimir" class="btImprimir">
			<input name="imprimir" type="button" class="button" value="Imprimir Informe" onClick="window.print();">
		</div>
		<?php
    }
/******************************************************************************************************************
     SE GENERA EL REPORTE DE INFORMES EN FORMATO .doc PARA PROCESAR SU CONTENIDO
******************************************************************************************************************/
    public function procesar_texto_informes($listado_para_documento_de_texto = '', $filtro_para_texto = '')
    {
		header("Content-Type: application/msword; charset=UTF-8");
		header('Content-Disposition: inline; filename=listado_informes.doc');

		$modelo = new consultaGralModel();
		$modeloEnComision = new expedEnComisionModel();
		//  SE COMIENZA A ARMAR EL DOCUMENTO
		echo "<html>";
		echo "\n<body>";

		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Municipalidad de General Pueyrredon</p>";
		echo chr(9).chr(9).chr(9).chr(9).chr(9).chr(9).chr(9)."\n<p style='margin:0cm;margin-bottom:.0001pt'>Sistema de Expedientes</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Honorable Concejo Deliberante</p>";
		echo "\n<hr>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Listado de Informes</p>";

		$comision = $modelo->obtenerNombreComision($filtro_para_texto['l_comision_tipo'], $filtro_para_texto['l_comision_codigo']);
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Para Tratamiento en la Comisi&oacute;n de ".$comision."</p>";

		if (isset($filtro_para_texto['l_fecha_desde']) || isset($filtro_para_texto['l_fecha_hasta']))
		{
			echo chr(9)."\n<p style='margin:0cm;margin-bottom:.0001pt'>Fecha Desde: ";
			echo (isset($filtro_para_texto['l_fecha_desde'])) ? $this->formatearFecha($filtro_para_texto['l_fecha_desde']) : '';
			echo chr(9).chr(9).chr(9)."Fecha Hasta: ";
			echo (isset($filtro_para_texto['l_fecha_hasta'])) ? $this->formatearFecha($filtro_para_texto['l_fecha_hasta']) : '';
			echo "\n</p>";
		}

		if (isset($filtro_para_texto['l_fecha_de_listado']) || isset($filtro_para_texto['l_fecha_de_listado']))
		{
			echo chr(9)."\n<p style='margin:0cm;margin-bottom:.0001pt'>Fecha de Listado: ".$filtro_para_texto['l_fecha_de_listado'];
		}

		echo "\n<hr>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>A&ntilde;o".chr(9)."T.".chr(9)."Nro.".chr(9)."Letra".chr(9)."Car&aacute;tula</b></p>";
		echo "\n<hr>";

		// SE RECORREN LOS EXPEDIENTES A MOSTRAR
		$cantidad = count($listado_para_documento_de_texto);
		for ($i=0; $i < $cantidad; $i++)
		{
			$ficha = &$listado_para_documento_de_texto[$i];

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>".$ficha['anio']." ".$ficha['tipo']." ".$ficha['numero']." ".$ficha['iniciador_codigo']." ".$this->reemplazarPorHTML($ficha['caratula'])."</b></p>";
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Detalle: ".$this->reemplazarPorHTML($ficha['detalle_informe'])."       Pedido el ".$this->formatearFecha($ficha['fecha_pedido_informe'])."</p>";

			//SE OBTIENEN LOS Proyectos DEL Expediente RESULTANTE
			$proyectos = $modelo->obtenerProyectosFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);

			$cantidad_proy = count($proyectos);
			for ($p=0; $p < $cantidad_proy; $p++)
			{
				$proyecto = &$proyectos[$p];
				echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'>".$this->reemplazarPorHTML($proyecto['extracto'])."</p>";
				echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";
			}

			// SE OBTIENE EL ULTIMO GIRO
			$ultimo_giro = $modeloEnComision->obtenerComisionFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>En Comision desde ".$this->formatearFecha($ultimo_giro[0]['fecha_giro'])."	Cantidad de d&iacute;as: ".$ficha['dias']."</p>";
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";

		} // FIN DEL for DE LOS EXPEDIENTES

		echo "\n<hr>";

		 // PIE DEL DOCUMENTO A IMPRIMIR CON FECHA, USUARIO Y NOMBRE DE LA PC
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>".date("d/m/Y")."</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>USR. ".$_SESSION['usuario'].chr(9).chr(9).chr(9).chr(9).chr(9)."PC: ".gethostbyaddr($_SERVER['REMOTE_ADDR'])."</p>";

		echo "\n</body>";
		echo "\n</html>";
    }
/*******************************************************************************************************************************************************/
	public function listar_expedientes_en_comision($listado = '', $listadoIniciadores = '', $listadoAutores = '', $listadoComisiones = '', $listadoCategorias = '', $listadoTemas = '', $listadoEstados = '', $listadoGiros = '', $mensaje = '', $filtro = '')
	{
	    if ( $mensaje != '' ){ echo '<div class="abm_mensaje_resultado">'.$mensaje.'</div>'; }
	?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

	    <div id="precarga_modal" style="display:none"></div>
	    <div id="contenidoAjaxResultadoListados" class="msc_gral msc_texto" style="width:700px;">

			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>

			<form action="listados/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">

				<input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
				<input type="hidden" name="accion" id="accion" value="listar" />
				<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
				<input type="hidden" name="l_enviado" id="l_enviado" value="enviado" />
				<input type="hidden" name="l_tipo_listado" id="l_tipo_listado" value="<?php echo $filtro['l_tipo_listado']; ?>" />
				<input type="hidden" name="l_cantidad_TOTAL" id="l_cantidad_TOTAL" value="<?php echo $filtro['l_cantidad']; ?>" />

				<div id="dragger_titulo_listado" class="msc_titulos degradado">Expedientes en Comisi&oacute;n</div>
				<div style="height:10px;font-size:0;"></div>

				<div style="height:135px;">
					<div style="width:550px;height:135px;float:left;">

						<div class="msl_criterio_fechas_margen"></div>
						<div class="msl_criterio_fechas_nombres">Ingresados desde el:</div>
						<?php
						if ( $filtro['l_enviado'] )
						{
							$l_fecha_desde = $this->formatearFecha($_SESSION['filtro_LISTADOS']['l_fecha_desde']);
						}
						else
						{
							// SE CALCULA LA FECHA 10 AÑOS ANTES DEL ACTUAL
							$l_fecha_desde = '01/01/'.( date("Y")-10 );
						}
						?>
						<div class="msl_criterio_calendario">
							<input type="text" id="l_fecha_desde" name="l_fecha_desde" value="<?php echo $l_fecha_desde; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
							<input type="image" id="msl_btffecha_desde" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Desde" width="16" height="16">
						</div>
						<div class="msl_criterio_fechas_nombres">Hasta el:</div>
						<div class="msl_criterio_calendario">
							<input type="text" id="l_fecha_hasta" name="l_fecha_hasta" value="<?php echo($_SESSION['filtro_LISTADOS']['l_fecha_hasta']) ? $this->formatearFecha($_SESSION['filtro_LISTADOS']['l_fecha_hasta']) : date("d/m/Y") ; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
							<input type="image" id="msl_btffecha_hasta" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Hasta" width="16" height="16">
						</div>
						<div class="msl_criterio_fechas_nombres">en Mesa de Entradas del H.C.D.</div>
						<div style="height:25px;clear:both">
							<div class="msl_criterio_fechas_margen"></div>
							<div class="msl_criterio_fechas_nombres">
								<div class="msc_dato_filtro">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ingresados en la comisi&oacute;n desde antes del:</div>
							</div>
							<div class="msl_criterio_calendario">
								<input type="text" id="l_fecha_comision" name="l_fecha_comision" value="<?php echo($_SESSION['filtro_LISTADOS']['l_fecha_comision']) ? $this->formatearFecha($_SESSION['filtro_LISTADOS']['l_fecha_comision']) : date("d/m/Y"); ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" maxlength="10" class="msc_ffecha" />
								<input type="image" id="msl_btffecha_comision" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha para la comisi&oacute;n" width="16" height="16">
							</div>
						</div>

						<div style="height:25px;clear:both">
							<div class="msl_criterio_margen"></div>
							<div class="msl_criterio_nombres">Fecha de listado:</div>
							<div class="msl_criterio_calendario">
								<input type="text" id="l_fecha_de_listado" name="l_fecha_de_listado" value="<?php echo($_SESSION['filtro_LISTADOS']['l_fecha_de_listado']) ? $_SESSION['filtro_LISTADOS']['l_fecha_de_listado'] : date("d/m/Y"); ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" maxlength="10" class="msc_ffecha" />
								<input type="image" id="msl_bt_l_fecha_de_listado" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha de listado." width="16" height="16">
							</div>
							<div class="msl_criterio_fechas_margen_medio"></div>
							<div class="msl_criterio_calendario">
								<input type="hidden" name="l_vencidos" id="l_vencidos" value="<?php echo($filtro['l_vencidos']) ? $filtro['l_vencidos'] : 0; ?>" />
								<input type="checkbox" name="vencidos" id="vencidos" >&nbsp;S&oacute;lo Vencidos
							</div>
						</div>

						<?php
						// 04/03/2013	COMISIONES SELECCIONADAS EN LA MODAL
						$cant_comisiones = count($listadoComisiones);
						for ($c=0; $c < $cant_comisiones; $c++)
						{
							$comision = &$listadoComisiones[$c];
						?>
							<input type="hidden" name="l_comisiones_modal[]" id="l_comision_modal_<?php echo $comision['codigo_grp']; ?>" value="<?php echo $comision['codigo_grp']; ?>" >
						<?php
						}
						?>
						<div class="msl_criterio_seccion_comisiones_y_estados">
							<div class="msl_criterio_seccion_nombres_y_combos">
								<div class="msl_criterio_margen"></div>
								<div class="msl_criterio_nombres">En comisi&oacute;n de:</div>
								<div id="msl_dato_filtro_comision" class="msl_criterio_combos">
									<select id="c_comision" name="l_comision" class="msl_combo" style="width:207px;">
										<option value="0">0, TODAS</option>
										<?php
										$cant_comisiones = count($listadoComisiones);
										for ($c=0; $c < $cant_comisiones; $c++)
										{
											$comision = &$listadoComisiones[$c];
										?>
											<option value="<?php echo $comision['tipo_grp'].'-'.$comision['codigo_grp']; ?>"><?php echo $comision['tipo_grp'].', '.$comision['codigo_grp'].', '.$comision['descripcion_grp']; ?></option>
										<?php
										}
										?>
									</select>
									&nbsp;
									<a id="imagen_zoom_comisiones" href="javascript:modalGaby('consultas/index.php?controlador=consulta_gral&accion=pedirNombreComisionModal&c_solo_habilitado='+$('l_solo_habilitado').value+'');" title="Buscar por Nombre de Comisi&oacute;n"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
								</div>
							</div>

							<div class="msl_criterio_seccion_nombres_y_combos">
								<div class="msl_criterio_margen"></div>
								<div class="msl_criterio_nombres">Con estado:</div>
								<div id="msl_dato_filtro_estado" class="msl_criterio_combos">
									<select id="c_estado" name="l_estado" class="msl_combo" style="width:207px;">
										<option value="0">0, TODOS</option>
										<?php
										$cant_estados = count($listadoEstados);
										for ($e=0; $e < $cant_estados; $e++)
										{
											$estado = &$listadoEstados[$e];
										?>
											<option value="<?php echo $estado['id_codestado']; ?>"><?php echo $estado['codigo_estado'].', '.$estado['nombre_estado']; ?></option>
										<?php
										}
										?>
									</select>
									&nbsp;
									<a id="imagen_zoom_estados" href="javascript:modalGaby('abms/index.php?controlador=codestados&accion=pedirNombreModal&c_solo_habilitado='+$('l_solo_habilitado').value+'');" title="Buscar por Nombre de Estado"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
								</div>
							</div>
							<div class="msl_criterio_seccion_nombres_y_combos" style="text-align:left;">
								<div class="msl_criterio_margen"></div>
								<div class="msl_criterio_nombres">S&oacute;lo Habilitados</div>
								<div class="msl_criterio_combos">
									<input type="hidden" name="l_solo_habilitado" id="l_solo_habilitado" value="<?php echo($_SESSION['filtro_LISTADOS']['l_solo_habilitado']) ? $_SESSION['filtro_LISTADOS']['l_solo_habilitado'] : 1; ?>" />
									<input type="checkbox" name="habilitado" id="habilitado" onchange="javascript:chequear('l_solo_habilitado');refrescarCombos($('l_solo_habilitado').value);" >
								</div>
							</div>
						</div>
					</div>
					<div class="msl_criterio_seccion_botones">
						<?php
						if ($filtro['l_enviado'] && $listado != '')
						{
						?>
							<div id="contenedor_btImpresion" class="msc_boton degradado">
								<a id="btImpresion" href="#" title="Formato para Impresi&oacute;n" >
								  <img src="imagenes/barra/print_16x16.gif" width="16" height="16" align="left" />&nbsp;&nbsp;&nbsp;Impresi&oacute;n
								</a>
							</div>

							<div id="contenedor_btProcesarTexto" class="msc_boton degradado">
								<a id="btProcesarTexto" href="#" title="Procesador de Texto" >
								  <img src="imagenes/iconos_office/doc.jpg" width="16" height="16" align="left" />&nbsp;&nbsp;&nbsp;Proc.Texto
								</a>
							</div>
						<?php
						}
						?>
						<div class="msc_boton degradado">
							<a href="javascript:buscar();">
								<img src="imagenes/zoom_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Buscar
							</a>
						</div>
						<div class="msc_boton degradado">
							<a href="javascript:limpiar();">
								<img src="imagenes/barra/limpiar.jpeg" width="16" height="16" />&nbsp;&nbsp;Limpiar
							</a>
						</div>
						<div class="msc_boton degradado">
							<a href="javascript:cerrarModalNueva();">
								<img src="imagenes/barra/error_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Cerrar
							</a>
						</div>
					</div><!-- FIN DE msl_criterio_seccion_botones -->
				</div>

				<?php
				if ( $filtro['l_enviado'] != '' && $listado != '' )
				{
					// SE SETEA EL PAGINADOR
					// SI SE DESEAN MOSTRAR SÓLO LOS VENCIDOS
					if ( $filtro['l_vencidos'] == 1 )
					{
						// SE DEFINE LA CANTIDAD DE PÁGINAS
						$filtro['l_nro_paginas'] = ceil(count($listado) / 5);
					}

					$urlPrimero = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_estado='+$('c_estado').value+'&l_comision='+$('c_comision').value+'&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_solo_habilitado='+$('l_solo_habilitado').value+'&l_fecha_comision='+$('l_fecha_comision').value+'&l_fecha_de_listado='+$('l_fecha_de_listado').value+'&l_vencidos='+$('l_vencidos').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=1&se_pagina=si&l_comisiones_modal=".serializarColeccion($filtro['l_comisiones_modal'])."', 'contenidoAjaxResultadoListados');";
					$urlAnterior = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_estado='+$('c_estado').value+'&l_comision='+$('c_comision').value+'&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_solo_habilitado='+$('l_solo_habilitado').value+'&l_fecha_comision='+$('l_fecha_comision').value+'&l_fecha_de_listado='+$('l_fecha_de_listado').value+'&l_vencidos='+$('l_vencidos').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=".$filtro['l_pagina_ant']."&se_pagina=si&l_comisiones_modal=".serializarColeccion($filtro['l_comisiones_modal'])."', 'contenidoAjaxResultadoListados');";
					$urlSiguiente = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_estado='+$('c_estado').value+'&l_comision='+$('c_comision').value+'&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_solo_habilitado='+$('l_solo_habilitado').value+'&l_fecha_comision='+$('l_fecha_comision').value+'&l_fecha_de_listado='+$('l_fecha_de_listado').value+'&l_vencidos='+$('l_vencidos').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=".$filtro['l_pagina_sgte']."&se_pagina=si&l_comisiones_modal=".serializarColeccion($filtro['l_comisiones_modal'])."', 'contenidoAjaxResultadoListados');";
					$urlUltimo = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_estado='+$('c_estado').value+'&l_comision='+$('c_comision').value+'&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_solo_habilitado='+$('l_solo_habilitado').value+'&l_fecha_comision='+$('l_fecha_comision').value+'&l_fecha_de_listado='+$('l_fecha_de_listado').value+'&l_vencidos='+$('l_vencidos').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=".$filtro['l_nro_paginas']."&se_pagina=si&l_comisiones_modal=".serializarColeccion($filtro['l_comisiones_modal'])."', 'contenidoAjaxResultadoListados');";
					?>
					<!-- PAGINADOR -->
					<div class="msc_paginador">
						<div class="msc_margen_paginador"></div>
						<div class="msc_flechas_paginador">
							<?php
							if ( $filtro['l_pagina'] != 1 )
							{
							?>
								<a id="btPrimero" title="Primer Registro" href="javascript:<?php echo $urlPrimero; ?>">
									<img id="imgPrimero" src="imagenes/barra/b_firstpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btPrimero" href="#">
									<img id="imgPrimero" src="imagenes/barra/bd_firstpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							if ( $filtro['l_pagina_ant'] != 0 )
							{
							?>
								<a id="btAnterior" title="Registro Anterior" href="javascript:<?php echo $urlAnterior; ?>">
									<img id="imgAnterior" src="imagenes/barra/b_prevpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btAnterior" href="#">
									<img id="imgAnterior" src="imagenes/barra/bd_prevpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							?>
						</div>
						<div class="msc_detalle_paginador"><?php echo $filtro['l_pagina'].' de '.$filtro['l_nro_paginas']; ?></div>
						<div class="msc_flechas_paginador">
							<?php
							if ( $filtro['l_pagina'] != $filtro['l_nro_paginas'] )
							{
							?>
								<a id="btSiguiente" title="Registro Siguiente" href="javascript:<?php echo $urlSiguiente; ?>">
									<img id="imgSiguiente" src="imagenes/barra/b_nextpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btSiguiente" href="#">
									<img id="imgSiguiente" src="imagenes/barra/bd_nextpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}

							if ( $filtro['l_pagina'] != $filtro['l_nro_paginas'] )
							{
							?>
								<a id="btUltimo" title="Ultimo Registro" href="javascript:<?php echo $urlUltimo; ?>">
									<img id="imgUltimo" src="imagenes/barra/b_lastpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btUltimo" href="#">
									<img id="imgUltimo" src="imagenes/barra/bd_lastpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							?>
						</div>
					</div>
					<!-- FIN DEL PAGINADOR -->
				<?php
				}
				?>

				<div class="msl_borde1">
					<div id="msl_borde2" class="msl_borde2">
						<?php
						if ( !$filtro['l_enviado'] )
						{
						?>
							<!-- FONDO MOSTRADO ANTES DE LA BUSQUEDA  -->
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
						<?php
						}
						else // A
						{
							if ( $listado == '' )
							{
								echo '<br><h1>Sin resultados</h1>';
							}
							else // B
							{
								//Se crea una instancia del modelo
								$modelo = new expedEnComisionModel();

								// SI SE DESEAN MOSTRAR SÓLO LOS VENCIDOS EN Expedientes en Comision
								if ( $filtro['l_vencidos'] == 1 )
								{
									$inicio = ($filtro['l_pagina']-1)*5;

									$final = $filtro['l_pagina']*5;
								}
								else
								{
									$inicio = 0;

									$final = count($listado);
								}

								//SE LISTAN LAS FICHAS DE LOS EXPEDIENTES
								for ($exp=$inicio; $exp < $final; $exp++)
								{
									$ficha = &$listado[$exp];

									if ( $ficha['anio'] )
									{
								?>
										<div class="mscpa_gral">
											<div style="height:19px;clear:both;">
												<div class="mscpa_expediente">

													<?php echo $ficha['anio']; ?>
													&nbsp;
													<?php echo $ficha['tipo']; ?>
													&nbsp;
													<?php echo $ficha['numero']; ?>
													&nbsp;
													<?php echo $ficha['cuerpo']; ?>
													&nbsp;
													<?php echo $ficha['alcance']; ?>

												</div>
												<div class="mscpa_titulo mscpa_tit_caratula">Car&aacute;tula:</div>
												<div class="mscpa_caratula"><?php echo $ficha['caratula']; ?></div>

												<!-- PRIMERO EL VALOR Y LUEGO EL TITULO POR LA POSICION RIGHT -->
												<div class="mscpa_fecha_entrada"><?php echo $this->formatearFecha($ficha['fecha_entrada_expe']); ?></div>
												<div class="mscpa_titulo mscpa_tit_fecha">Fecha Expe.:</div>
											</div>
											<div style="height:19px;clear:both;">
												<div class="mscpa_titulo">Tema:&nbsp;</div>
												<div class="mscpa_tema_e_iniciador_categoria_y_autor">
													<select name="temas" class="msc_combo">
														<?php
														//SE OBTIENEN LOS Temas DEL Expediente RESULTANTE
														$temas = $modelo->obtenerTemasFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
														$cant_t = count($temas);
														for ($t=0; $t < $cant_t; $t++)
														{
															$tema = &$temas[$t];
														?>
															<option value="<?php echo $t; ?>"><?php echo $tema['descripcion_tema']; ?></option>
														<?php
														}
														?>
													</select>
												</div>
												<div class="mscpa_titulo">Iniciador:&nbsp;</div>
												<div class="mscpa_tema_e_iniciador_categoria_y_autor"><?php echo $ficha['iniciador']; ?></div>
											</div>
											<div style="height:19px;clear:both;">
												<div class="mscpa_titulo">Categor&iacute;a:&nbsp;</div>
												<div class="mscpa_tema_e_iniciador_categoria_y_autor"><?php echo $ficha['categoria']; ?></div>
												<div class="mscpa_titulo">Autor:&nbsp;</div>
												<div class="mscpa_tema_e_iniciador_categoria_y_autor">
													<select name="autores" class="msc_combo">
														<?php
														//SE OBTIENEN LOS Autores DEL Expediente RESULTANTE
														$autores = $modelo->obtenerAutoresFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
														$cant_a = count($autores);
														for ($a=0; $a < $cant_a; $a++)
														{
															$autor = &$autores[$a];
														?>
															<option value="<?php echo $a; ?>"><?php echo $autor['descripcion_grp']; ?></option>
														<?php
														}
														?>
													</select>
												</div>
											</div>
											<?php
											//SE OBTIENEN LOS Proyectos DEL Expediente RESULTANTE (LUEGO MOSTRAR UNA FICHA POR CADA UNO!!!)
											$proyectos = $modelo->obtenerProyectosFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);

											//SE MUESTRAN LOS Proyectos DEL Expediente RESULTANTE
											$cant_p = count($proyectos);
											for ($p=0; $p < $cant_p; $p++)
											{
												$proyecto = &$proyectos[$p];
											?>
												<div style="height:19px;clear:both;">
													<div class="mscpa_titulo">Proyecto:&nbsp;</div>
													<div class="lcg_nrotipo_proyecto"><?php echo $p+1; //CONTADOR DE PROYECTOS ?></div>
													<div class="lcg_descripcion_proyecto"><?php echo $proyecto['descripcion_proyecto']; ?></div>
												</div>
												<div style="height:40px;clear:both;">
													<div class="mscpa_titulo" style="height:37px;">Extracto:&nbsp;</div>
													<div class="mscpa_extracto"><?php echo $proyecto['extracto']; ?></div>
												</div>
											<?php
											}
											// SE OBTIENE EL Estado DEL Expediente RESULTANTE _para_Listados
											$estado = $modelo->obtenerEstadoFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance'], $filtro['l_estado']);
											?>
											<div style="height:16px;margin-top:3px;clear:both;">
												<div class="mscpa_titulo">Estado:&nbsp;</div>
												<div class="mscpa_estado_comision"><?php echo $estado[0]['nombre_estado']; ?></div>
												<div class="lec_fecha_estado"><?php echo " <b>Desde el</b> ".$this->formatearFecha($estado[0]['fecha_estado']); ?></div>
											</div>

											<?php
											// MODIFICADO: 26/07/2012 PARA VER TODAS LAS COMISIONES

											//SE OBTIENE LA Comision DEL Expediente RESULTANTE
											$comision = $modelo->obtenerComisionFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);

											if ( $comision[0]['comision'] != '' )
											{
											?>
												<div style="height:19px;clear:both;">
													<div class="mscpa_titulo">Comisi&oacute;n:&nbsp;</div>
													<div class="mscpa_estado_comision"><?php echo $comision[0]['comision']; ?></div>
													<div class="lec_fecha_estado"><?php echo " <b>Desde el</b> ".$this->formatearFecha($comision[0]['fecha_giro']); ?></div>
													<?php
													// SE MUESTRAN LOS DIAS EN COMISION
													if ( $filtro['l_tipo_listado'] == "exped_en_comision" && $ficha['dias'] > 0 )
													{
														// COLOR VERDE
														if ( $ficha['dias'] >= 0 && $ficha['dias'] <= 105 )
														{
															$css_color = 'style="background-color: #DFF0D8;color: #468847;"';
														}
														else
														{
															// COLOR AMARILLO
															if ( $ficha['dias'] >= 106 && $ficha['dias'] <= 119 )
															{
																$css_color = 'style="background-color: #FCF8E3;color: #C09853;"';
															}
															else
															{
																// COLOR ROJO
																$css_color = 'style="background-color: #F2DEDE;color: #B94A48;"';
															}
														}
													?>
														<div class="lec_cantidad_dias_expe" <?php echo $css_color; ?> >
															<?php echo $ficha['dias']." d&iacute;as"; ?>
														</div>
													<?php
													}
													?>
												</div>
											<?php
											}
											?>
											<div style="height:5px;font-size:0;clear:both;"></div>
											<div style="height:19px;clear:both;">
												<?php
												// 20/04/2015, PARA LOS VENCIDOS
												if ( $comision[0]['orden_giro'] != '' && $ficha['dias'] > 120 )
												{
													// 20/04/2015, SE OBTIENEN DATOS DEL ULTIMO INFORME DE UN GIRO DETERMINADO
													$ficha['orden_giro'] = $comision[0]['orden_giro'];
													$datos_ultimo_informe = $modelo->obtenerUltimoInforme($ficha);

													// 20/04/2015, SI POSEE UN INFORME
													if ( $datos_ultimo_informe )
													{
													?>
														<!-- SE MUESTRA EL DETALLE DEL INFORME PENDIENTE -->
														<div class="listados_contenedora_detalle_informe_pendiente" >
															<b>Informe pendiente</b> desde el <?php echo $this->formatearFecha($datos_ultimo_informe['fecha_pedido_informe']); ?>
															<?php echo ($datos_ultimo_informe['detalle_informe']) ? ': '.$datos_ultimo_informe['detalle_informe'] : ''; ?>
															<?php echo ($datos_ultimo_informe['observaciones_informe']) ? ': '.$datos_ultimo_informe['observaciones_informe'] : ''; ?>
														</div>
													<?php
													}
												}
												?>
												<div class="cyl_boton_ver_expediente_y_ficha">
													<a href="javascript:verExpediente('<?php echo $ficha['anio']; ?>', '<?php echo $ficha['tipo']; ?>', '<?php echo $ficha['numero']; ?>', '<?php echo $ficha['cuerpo']; ?>', '<?php echo $ficha['alcance']; ?>', '<?php echo $filtro['l_tipo_listado']; ?>');">Ver exped.</a>
												</div>
												<div class="cyl_boton_ver_expediente_y_ficha">
													<a href="javascript:modalGabyFicha('consultas/index.php?controlador=ficha&accion=ver_ficha_modal&anio=<?php echo $ficha['anio']; ?>&tipo=<?php echo $ficha['tipo']; ?>&numero=<?php echo $ficha['numero']; ?>&cuerpo=<?php echo $ficha['cuerpo']; ?>&alcance=<?php echo $ficha['alcance']; ?>');">Ficha</a>
												</div>
											</div>
										</div>
								<?php
									} // FIN DEL if DE VERIFICACIÓN
								} // FIN DEL for
								?>
								<script>
								  var scroller = new Fx.Scroll($('msl_borde2'));
								  scroller.toTop();
								</script>
							<?php
							} // FIN DEL else B
						} // FIN DEL else A
						?>
					</div><!-- FIN DE msl_borde2 -->
				</div><!-- FIN DE msl_borde1 -->

			</form>
	    </div>
	    <script>

		    var menuDrag = new Drag.Move($('contenidoAjaxResultadoListados'), {
			   handle: $('dragger_titulo_listado')
			});

			//CALENDARIO PARA LA FECHA DESDE
			var calDesde = new Zapatec.Calendar.setup({

				inputField:"l_fecha_desde",
				ifFormat:"%d/%m/%Y",
				button:"msl_btffecha_desde",
				showsTime:false
			});
			//CALENDARIO PARA LA FECHA HASTA
			var calHasta = new Zapatec.Calendar.setup({

				inputField:"l_fecha_hasta",
				ifFormat:"%d/%m/%Y",
				button:"msl_btffecha_hasta",
				showsTime:false
			});

			function buscar()
			{
				var mensaje = '';
				var error = false;

				if ( $('l_fecha_desde').value == '' )
				{
					error = true;
					mensaje = "Debe ingresar un rango de Fecha.<br>";
				}
				if ( $('l_fecha_hasta').value == '' )
				{
					error = true;
					mensaje = "Debe ingresar un rango de Fecha.<br>";
				}

				/* 26/07/2012 *
				if ( $('c_comision').disabled == false && $('c_comision').value == 0 )
				{
					error = true;
					mensaje = "Debe ingresar una Comisi"+'\u00f3'+"n.";
				}
				if ( esLaFechaMayor($('l_fecha_comision').value, $('l_fecha_hasta').value) )
				{
					error = true;
					mensaje = "La fecha l"+'\u00ed'+"mite de ingreso de la Comisi"+'\u00f3'+"n no debe ser mayor a la fecha Hasta del criterio de b"+'\u00fa'+"squeda.";
				}
				/**/

				if ( esLaFechaMayor($('l_fecha_desde').value, $('l_fecha_hasta').value) )
				{
					error = true;
					mensaje = "La fecha Desde debe ser menor a la fecha Hasta en el criterio de b"+'\u00fa'+"squeda.";
				}

				if ( error )
				{
					alert(mensaje);
				}
				else
				{
					// NUEVO 01/03/2013
					// SI NO SE FILTRA POR ESTADO
					if ( $('c_estado').value == 0 )
					{
						// SI NO SE ELIGIÓ UNA COMISIÓN
						if ( $('c_comision').disabled == false && $('c_comision').value == 0 )
						{
							// SE MUESTRA UNA VENTANA MODAL DE COMISIONES ACTIVAS, PARA ELEGIR CUAL MOSTRAR EN EL RESULTADO TOTAL DEL LISTADO
							modalGaby('listados/index.php?controlador=exped_en_comision&accion=mostrarModalComisionesActivas');
						}
						else
						{
							enviarForm('formListados', 'listados', 'contenidoAjaxResultadoListados');
						}
					}
					else
					{
						enviarForm('formListados', 'listados', 'contenidoAjaxResultadoListados');
					}
				}
			}

			function limpiar()
			{
				$('l_fecha_desde').value = '<?php echo '01/01/'.( date("Y")-10 ); ?>';
				$('l_fecha_hasta').value = '<?php echo date("d/m/Y"); ?>';
				$('l_fecha_comision').value = '<?php echo date("d/m/Y"); ?>';
				$('l_fecha_de_listado').value = '<?php echo date("d/m/Y"); ?>';
				$('c_comision').value = 0;
				$('c_estado').value = 0;
				$('c_estado').disabled = false;
				$('imagen_zoom_estados').setStyle('display', 'inline');
				$('c_comision').disabled = false;
				$('imagen_zoom_comisiones').setStyle('display', 'inline');
			}

			$('c_comision').value = '<?php echo ($_SESSION['filtro_LISTADOS']['l_comision']) ? $_SESSION['filtro_LISTADOS']['l_comision'] : 0 ; ?>';

			if ( $('c_comision').value != 0 )
			{
				$('c_estado').disabled = true;
				$('imagen_zoom_estados').setStyle('display', 'none');
				$('c_estado').setStyle('width', '234px');
			}
			else
			{
				$('c_estado').disabled = false;
				$('imagen_zoom_estados').setStyle('display', 'inline');
				$('c_estado').setStyle('width', '207px');
			}

			//CALENDARIO PARA LA FECHA DE LA COMISION
			var calComision = new Zapatec.Calendar.setup({

				inputField:"l_fecha_comision",
				ifFormat:"%d/%m/%Y",
				button:"msl_btffecha_comision",
				showsTime:false
			});

			function refrescarCombos(habilitado)
			{
				refrescarCombo('listados/index.php?controlador=exped_en_comision&accion=refrescarComboComisiones&habilitado='+habilitado+'&comision=<?php echo $_SESSION['filtro_LISTADOS']['l_comision']; ?>','msl_dato_filtro_comision');
				refrescarCombo('listados/index.php?controlador=exped_en_comision&accion=refrescarComboEstados&habilitado='+habilitado+'&estado=<?php echo $_SESSION['filtro_LISTADOS']['l_estado']; ?>','msl_dato_filtro_estado');
			}

			$('c_comision').addEvent('change', function()
			{
				// SI SE ELIGIO UNA COMISION
				if ( $('c_comision').value != 0 )
				{
					// SE DESHABILITA EL COMBO DE ESTADOS
					$('c_estado').disabled = true;
					$('imagen_zoom_estados').setStyle('display', 'none');

					if ( $('l_tipo_listado').value == "exped_en_comision" )
					{
						$('vencidos').disabled = false;
					}
				}
				else
				{
					// SINO SE HABILITA EL COMBO DE ESTADOS
					$('c_estado').disabled = false;
					$('imagen_zoom_estados').setStyle('display', 'inline');

					if ( $('l_tipo_listado').value == "exped_en_comision" )
					{
						$('vencidos').disabled = true;
					}
				}
			});

			$('c_estado').addEvent('change', function()
			{
				// SI SE ELIGIO UN ESTADO
				if ( $('c_estado').value != 0 )
				{
					// SE DESHABILITAN EL COMBO DE COMISIONES Y LA FECHA ( DE INGRESADOS EN LA COMISION ANTES DEL... )
					$('c_comision').disabled = true;
					$('l_fecha_comision').disabled = true;
					$('msl_btffecha_comision').setStyle('display', 'none');//'visibility', 'hidden'
					$('imagen_zoom_comisiones').setStyle('display', 'none');

					if ( $('l_tipo_listado').value == "exped_en_comision" )
					{
						// SE DESHABILITA EL CHECKBOX DE Sólo Vencidos
						$('vencidos').disabled = true;
					}
				}
				else // SINO
				{
					// SE HABILITAN EL COMBO DE COMISIONES Y LA FECHA ( DE INGRESADOS EN LA COMISION ANTES DEL... )
					$('c_comision').disabled = false;
					$('l_fecha_comision').disabled = false;
					$('msl_btffecha_comision').setStyle('display', 'inline');//'visibility', 'visible'
					$('imagen_zoom_comisiones').setStyle('display', 'inline');

					if ( $('l_tipo_listado').value == "exped_en_comision" )
					{
						// SE HABILITA EL CHECKBOX DE Sólo Vencidos
						$('vencidos').disabled = false;
					}
				}
			});

			// SI SE ELIGIO UN ESTADO
			if ( $('c_estado').value != 0 )
			{
				// SE DESHABILITAN EL COMBO DE COMISIONES Y LA FECHA ( DE INGRESADOS EN LA COMISION ANTES DEL... )
				$('c_comision').disabled = true;
				$('l_fecha_comision').disabled = true;
				$('msl_btffecha_comision').setStyle('display', 'none');//'visibility', 'hidden'
				$('imagen_zoom_comisiones').setStyle('display', 'none');

				if ( $('l_tipo_listado').value == "exped_en_comision" )
				{
					// SE DESHABILITA EL CHECKBOX DE Sólo Vencidos
					$('vencidos').disabled = true;
				}
			}
			else
			{
				// SE HABILITAN EL COMBO DE COMISIONES Y LA FECHA ( DE INGRESADOS EN LA COMISION ANTES DEL... )
				$('c_comision').disabled = false;
				$('l_fecha_comision').disabled = false;
				$('msl_btffecha_comision').setStyle('display', 'inline');//'visibility', 'visible'
				$('imagen_zoom_comisiones').setStyle('display', 'inline');

				if ( $('l_tipo_listado').value == "exped_en_comision" )
				{
					// SE HABILITA EL CHECKBOX DE Sólo Vencidos
					$('vencidos').disabled = false;
				}
			}

			<?php
			if ( $_SESSION['filtro_LISTADOS']['l_solo_habilitado'] == '' )
			{
			?>
				$('habilitado').checked = true;
				$('l_solo_habilitado').value = 1;
			<?php
			}
			elseif ( $_SESSION['filtro_LISTADOS']['l_solo_habilitado'] == 0 )
			{
			?>
				$('habilitado').checked = false;
				$('l_solo_habilitado').value = 0;
			<?php
			}
			else
			{
			?>
				$('habilitado').checked = true;
				$('l_solo_habilitado').value = 1;
			<?php
			}
			?>

			// NUEVO 30/03/2012
			//CALENDARIO PARA LA FECHA DE LISTADO
			var calFechaDeListado = new Zapatec.Calendar.setup({

				inputField:"l_fecha_de_listado",
				ifFormat:"%d/%m/%Y",
				button:"msl_bt_l_fecha_de_listado",
				showsTime:false
			});

			<?php
			if ( $filtro['l_vencidos'] == '' )
			{
			?>
				$('vencidos').checked = false;
				$('l_vencidos').value = 0;
			<?php
			}
			elseif ( $filtro['l_vencidos'] == 0 )
			{
			?>
				$('vencidos').checked = false;
				$('l_vencidos').value = 0;
			<?php
			}
			else
			{
			?>
				$('vencidos').checked = true;
				$('l_vencidos').value = 1;
			<?php
			}
			?>

			$('vencidos').addEvent('change', function()
			{
				if ($('vencidos').checked == true)
				{
					$('l_vencidos').value = 1;
				}
				else
				{
					$('l_vencidos').value = 0;
				}
			});

			if ( $('c_estado') )
			{
				$('c_estado').value = <?php echo ($_SESSION['filtro_LISTADOS']['l_estado']) ? "'".$_SESSION['filtro_LISTADOS']['l_estado']."'" : 0 ; ?>;
			}

			<?php
			if ( $filtro['l_enviado'] )
			{
			?>
				$('btImpresion').addEvent('click', function()
				{
				    if ( $('l_cantidad_TOTAL').value >= 5000 )
				    {
					  alert("El resultado de su consulta es muy largo para imprimir.");
				    }
				    else
				    {
					    $('btImpresion').setProperty('href', 'listados/index.php?controlador=exped_en_comision&accion=armar_listado_completo&l_tipo_listado='+$('l_tipo_listado').value+'&formato=impresion&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_fecha_de_listado='+$('l_fecha_de_listado').value+'&l_estado='+$('c_estado').value+'&l_fecha_comision='+$('l_fecha_comision').value+'&l_comision='+$('c_comision').value+'&l_vencidos='+$('l_vencidos').value+'&l_comisiones_modal=<?php echo serializarColeccion($filtro['l_comisiones_modal']); ?>');

					    $('btImpresion').setProperty('target', '_blank');
					    $('contenedor_btImpresion').setStyle('display', 'none');
					    $('contenedor_btProcesarTexto').setStyle('display', 'none');
				    }
				});

				$('btProcesarTexto').addEvent('click', function()
				{
				    if ( $('l_cantidad_TOTAL').value >= 5000 )
				    {
					    alert("El resultado de su consulta es muy largo para procesar su texto.");
				    }
				    else
				    {
					    $('btProcesarTexto').setProperty('href', 'listados/index.php?controlador=exped_en_comision&accion=armar_listado_completo&l_tipo_listado='+$('l_tipo_listado').value+'&formato=texto&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_fecha_de_listado='+$('l_fecha_de_listado').value+'&l_fecha_comision='+$('l_fecha_comision').value+'&l_comision='+$('c_comision').value+'&l_estado='+$('c_estado').value+'&l_vencidos='+$('l_vencidos').value+'&l_comisiones_modal=<?php echo serializarColeccion($filtro['l_comisiones_modal']); ?>');

					    $('btProcesarTexto').setProperty('target', '_blank');
					    $('contenedor_btImpresion').setStyle('display', 'none');
					    $('contenedor_btProcesarTexto').setStyle('display', 'none');
				    }
				});
			<?php
			}

			// 27/11/2012
			// SI SE VUELVE DE HABER VISTO EL EXPEDIENTE
			if ( $_SESSION['cerrar_modal_LISTADOS'] == 'no' )
			{
				$_SESSION['cerrar_modal_LISTADOS'] = null;
			?>
				// SE REDIRECCIONA
				refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_estado='+$('c_estado').value+'&l_comision='+$('c_comision').value+'&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_solo_habilitado='+$('l_solo_habilitado').value+'&l_fecha_comision='+$('l_fecha_comision').value+'&l_fecha_de_listado='+$('l_fecha_de_listado').value+'&l_vencidos='+$('l_vencidos').value+'&l_enviado=enviado&l_tipo_listado=<?php echo $filtro['l_tipo_listado']; ?>&l_pagina=<?php echo $_SESSION['filtro_LISTADOS']['l_pagina']; ?>', 'contenidoAjaxResultadoListados');
			<?php
			}
			?>

			$('l_fecha_desde').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
					$('msl_btffecha_comision').disabled = false;
					$('msl_bt_l_fecha_de_listado').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;
						$('msl_btffecha_comision').disabled = true;
						$('msl_bt_l_fecha_de_listado').disabled = true;

						buscar();
					}
				}
			});

			$('l_fecha_hasta').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
					$('msl_btffecha_comision').disabled = false;
					$('msl_bt_l_fecha_de_listado').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;
						$('msl_btffecha_comision').disabled = true;
						$('msl_bt_l_fecha_de_listado').disabled = true;

						buscar();
					}
				}
			});

			$('l_fecha_comision').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
					$('msl_btffecha_comision').disabled = false;
					$('msl_bt_l_fecha_de_listado').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;
						$('msl_btffecha_comision').disabled = true;
						$('msl_bt_l_fecha_de_listado').disabled = true;

						buscar();
					}
				}
			});

			$('l_fecha_de_listado').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
					$('msl_btffecha_comision').disabled = false;
					$('msl_bt_l_fecha_de_listado').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;
						$('msl_btffecha_comision').disabled = true;
						$('msl_bt_l_fecha_de_listado').disabled = true;

						buscar();
					}
				}
			});
		</script>
	<?php
	}

	public function listar_detalle_giros($listado = '', $listadoIniciadores = '', $listadoAutores = '', $listadoComisiones = '', $listadoCategorias = '', $listadoTemas = '', $listadoEstados = '', $listadoGiros = '', $mensaje = '', $filtro = '')
	{
	    if ( $mensaje != '' ){ echo '<div class="abm_mensaje_resultado">'.$mensaje.'</div>'; }
	?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

	    <div id="precarga_modal" style="display:none"></div>
	    <div id="contenidoAjaxResultadoListados" class="msc_gral msc_texto" style="width:700px;">

			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>

			<form action="listados/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">

				<input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
				<input type="hidden" name="accion" id="accion" value="listar" />
				<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
				<input type="hidden" name="l_enviado" id="l_enviado" value="enviado" />
				<input type="hidden" name="l_tipo_listado" id="l_tipo_listado" value="<?php echo $filtro['l_tipo_listado']; ?>" />
				<input type="hidden" name="l_cantidad_TOTAL" id="l_cantidad_TOTAL" value="<?php echo $filtro['l_cantidad']; ?>" />

				<div id="dragger_titulo_listado" class="msc_titulos degradado">Con detalle de Giros</div>
				<div style="height:10px;font-size:0;"></div>

				<div style="height:135px;">

					<div style="width:550px;height:135px;float:left;">

						<div class="msl_criterio_fechas_margen"></div>
						<div class="msl_criterio_fechas_nombres">
							<div class="msc_dato_filtro">Ingresados desde el:</div>
						</div>
						<?php
						if ( $filtro['l_enviado'] )
						{
							$l_fecha_desde = $this->formatearFecha($_SESSION['filtro_LISTADOS']['l_fecha_desde']);
						}
						else
						{
							// SE CALCULA LA FECHA 10 AÑOS ANTES DEL ACTUAL
							$l_fecha_desde = '01/01/'.( date("Y")-10 );
						}
						?>
						<div class="msl_criterio_calendario">
							<input type="text" id="l_fecha_desde" name="l_fecha_desde" value="<?php echo $l_fecha_desde; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
							<input type="image" id="msl_btffecha_desde" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Desde" width="16" height="16">
						</div>

						<div class="msl_criterio_fechas_nombres">
							<div class="msc_dato_filtro">Hasta el:</div>
						</div>
						<div class="msl_criterio_calendario">
							<input type="text" id="l_fecha_hasta" name="l_fecha_hasta" value="<?php echo($_SESSION['filtro_LISTADOS']['l_fecha_hasta']) ? $this->formatearFecha($_SESSION['filtro_LISTADOS']['l_fecha_hasta']) : date("d/m/Y") ; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
							<input type="image" id="msl_btffecha_hasta" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Hasta" width="16" height="16">
						</div>

						<div class="msl_criterio_fechas_nombres">
							<div class="msc_dato_filtro">en Mesa de Entradas del H.C.D.</div>
						</div>

						<div style="height:25px;clear:both">
							<div class="msl_criterio_fechas_margen"></div>
							<div class="msl_criterio_fechas_nombres">
								<div class="msc_dato_filtro">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ingresados en la comisi&oacute;n desde antes del:</div>
							</div>
							<div class="msl_criterio_calendario">
								<input type="text" id="l_fecha_comision" name="l_fecha_comision" value="<?php echo($_SESSION['filtro_LISTADOS']['l_fecha_comision']) ? $this->formatearFecha($_SESSION['filtro_LISTADOS']['l_fecha_comision']) : date("d/m/Y"); ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" maxlength="10" class="msc_ffecha" />
								<input type="image" id="msl_btffecha_comision" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha para la comisi&oacute;n" width="16" height="16">
							</div>
						</div>

						<div class="msl_criterio_seccion_comisiones_y_estados">
							<div class="msl_criterio_seccion_nombres_y_combos">
								<div class="msl_criterio_margen"></div>
								<div class="msl_criterio_nombres">
									<div class="msc_dato_filtro">En comisi&oacute;n de:</div>
								</div>
								<div class="msl_criterio_combos">
									<div id="msl_dato_filtro_comision" class="msc_dato_filtro">
										&nbsp;
										<select id="c_comision" name="l_comision" class="msl_combo" style="width:195px;">
											<option value="0">0, TODAS</option>
											<?php
											$cant_comisiones = count($listadoComisiones);
											for ($c=0; $c < $cant_comisiones; $c++)
											{
												$comision = &$listadoComisiones[$c];
											?>
												<option value="<?php echo $comision['tipo_grp'].'-'.$comision['codigo_grp']; ?>"><?php echo $comision['tipo_grp'].', '.$comision['codigo_grp'].', '.$comision['descripcion_grp']; ?></option>
											<?php
											}
											?>
										</select>
										&nbsp;
										<a id="imagen_zoom_comisiones" href="javascript:modalGaby('consultas/index.php?controlador=consulta_gral&accion=pedirNombreComisionModal&c_solo_habilitado='+$('l_solo_habilitado').value+'');" title="Buscar por Nombre de Comisi&oacute;n"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
									</div>
								</div>
							</div>
							<div class="msl_criterio_seccion_nombres_y_estados">
								<div class="msl_criterio_margen"></div>
								<div class="msl_criterio_nombres">
									<div class="msc_dato_filtro">Con estado:</div>
								</div>
								<div class="msl_criterio_combos">
									<div id="msl_dato_filtro_estado" class="msc_dato_filtro">
										&nbsp;
										<select id="c_estado" name="l_estado" class="msl_combo" style="width:195px;">
											<option value="0">0, TODOS</option>
											<?php
											$cant_estados = count($listadoEstados);
											for ($e=0; $e < $cant_estados; $e++)
											{
												$estado = &$listadoEstados[$e];
											?>
												<option value="<?php echo $estado['id_codestado']; ?>"><?php echo $estado['codigo_estado'].', '.$estado['nombre_estado']; ?></option>
											<?php
											}
											?>
										</select>
										&nbsp;
										<a id="imagen_zoom_estados" href="javascript:modalGaby('abms/index.php?controlador=codestados&accion=pedirNombreModal&c_solo_habilitado='+$('l_solo_habilitado').value+'');" title="Buscar por Nombre de Estado"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
									</div>
								</div>
							</div>
							<div class="msl_criterio_seccion_nombres_y_combos" style="text-align:left;">
								<div class="msl_criterio_margen"></div>
								<div class="msl_criterio_nombres">
									<div class="msc_dato_filtro">S&oacute;lo Habilitados</div>
								</div>
								<div class="msl_criterio_combos">
									<input type="hidden" name="l_solo_habilitado" id="l_solo_habilitado" value="<?php echo($_SESSION['filtro_LISTADOS']['l_solo_habilitado']) ? $_SESSION['filtro_LISTADOS']['l_solo_habilitado'] : 1; ?>" />
									<input type="checkbox" name="habilitado" id="habilitado" onchange="javascript:chequear('l_solo_habilitado');refrescarCombos($('l_solo_habilitado').value);" >
								</div>
							</div>
						</div>
					</div>
					<div class="msl_criterio_seccion_botones">
						<?php
						if ($filtro['l_enviado'] && $listado != '')
						{
						?>
							<div id="contenedor_btImpresion" class="msc_boton degradado">
								<a id="btImpresion" href="#" title="Formato para Impresi&oacute;n" >
								  <img src="imagenes/barra/print_16x16.gif" width="16" height="16" align="left" />&nbsp;&nbsp;&nbsp;Impresi&oacute;n
								</a>
							</div>

							<div id="contenedor_btProcesarTexto" class="msc_boton degradado">
								<a id="btProcesarTexto" href="#" title="Procesador de Texto" >
								  <img src="imagenes/iconos_office/doc.jpg" width="16" height="16" align="left" />&nbsp;&nbsp;&nbsp;Proc.Texto
								</a>
							</div>
						<?php
						}
						?>
						<div class="msc_boton degradado">
							<a href="javascript:buscar();">
								<img src="imagenes/zoom_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Buscar
							</a>
						</div>
						<div class="msc_boton degradado">
							<a href="javascript:limpiar();">
								<img src="imagenes/barra/limpiar.jpeg" width="16" height="16" />&nbsp;&nbsp;&nbsp;Limpiar
							</a>
						</div>
						<div class="msc_boton degradado">
							<a href="javascript:cerrarModalNueva();">
								<img src="imagenes/barra/error_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Cerrar
							</a>
						</div>
					</div><!-- FIN DE msl_criterio_seccion_botones -->
				</div>

				<?php
				if ( $filtro['l_enviado'] != '' && $listado != '' )
				{
					$urlPrimero = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_estado='+$('c_estado').value+'&l_comision='+$('c_comision').value+'&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_solo_habilitado='+$('l_solo_habilitado').value+'&l_fecha_comision='+$('l_fecha_comision').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=1', 'contenidoAjaxResultadoListados');";
					$urlAnterior = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_estado='+$('c_estado').value+'&l_comision='+$('c_comision').value+'&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_solo_habilitado='+$('l_solo_habilitado').value+'&l_fecha_comision='+$('l_fecha_comision').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=".$filtro['l_pagina_ant']."', 'contenidoAjaxResultadoListados');";
					$urlSiguiente = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_estado='+$('c_estado').value+'&l_comision='+$('c_comision').value+'&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_solo_habilitado='+$('l_solo_habilitado').value+'&l_fecha_comision='+$('l_fecha_comision').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=".$filtro['l_pagina_sgte']."', 'contenidoAjaxResultadoListados');";
					$urlUltimo = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_estado='+$('c_estado').value+'&l_comision='+$('c_comision').value+'&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_solo_habilitado='+$('l_solo_habilitado').value+'&l_fecha_comision='+$('l_fecha_comision').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=".$filtro['l_nro_paginas']."', 'contenidoAjaxResultadoListados');";
				?>
					<!-- PAGINADOR -->
					<div class="msc_paginador">
						<div class="msc_margen_paginador"></div>
						<div class="msc_flechas_paginador">
							<?php
							if ( $filtro['l_pagina'] != 1 )
							{
							?>
								<a id="btPrimero" title="Primer Registro" href="javascript:<?php echo $urlPrimero; ?>">
									<img id="imgPrimero" src="imagenes/barra/b_firstpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btPrimero" href="#">
									<img id="imgPrimero" src="imagenes/barra/bd_firstpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							if ( $filtro['l_pagina_ant'] != 0 )
							{
							?>
								<a id="btAnterior" title="Registro Anterior" href="javascript:<?php echo $urlAnterior; ?>">
									<img id="imgAnterior" src="imagenes/barra/b_prevpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btAnterior" href="#">
									<img id="imgAnterior" src="imagenes/barra/bd_prevpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							?>
						</div>
						<div class="msc_detalle_paginador"><?php echo $filtro['l_pagina'].' de '.$filtro['l_nro_paginas']; ?></div>
						<div class="msc_flechas_paginador">
							<?php
							if ($filtro['l_pagina'] != $filtro['l_nro_paginas'])
							{
							?>
								<a id="btSiguiente" title="Registro Siguiente" href="javascript:<?php echo $urlSiguiente; ?>">
									<img id="imgSiguiente" src="imagenes/barra/b_nextpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btSiguiente" href="#">
									<img id="imgSiguiente" src="imagenes/barra/bd_nextpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}

							if ($filtro['l_pagina'] != $filtro['l_nro_paginas']){
							?>
								<a id="btUltimo" title="Ultimo Registro" href="javascript:<?php echo $urlUltimo; ?>">
									<img id="imgUltimo" src="imagenes/barra/b_lastpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btUltimo" href="#">
									<img id="imgUltimo" src="imagenes/barra/bd_lastpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							?>
						</div>
					</div>
					<!-- FIN DEL PAGINADOR -->
				<?php
				}
				?>

				<div class="msl_borde1">
					<div id="msl_borde2" class="msl_borde2">
						<?php
						if ( !$filtro['l_enviado'] )
						{
						?>
							<!-- FONDO MOSTRADO ANTES DE LA BUSQUEDA  -->
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
						<?php
						}
						else // A
						{
							if ( $listado == '' )
							{
								echo '<br><h1>Sin resultados</h1>';
							}
							else // B
							{
								//Se crea una instancia del modelo
								$modelo = new expedEnComisionModel();

								$inicio = 0;
								$final = count($listado);

								//SE LISTAN LAS FICHAS DE LOS EXPEDIENTES
								for ($exp=$inicio; $exp < $final; $exp++)
								{
									$ficha = &$listado[$exp];

									if ( $ficha['anio'] )
									{
								?>
											<div class="mscpa_gral">
												<div style="height:19px;clear:both;">
													<div class="mscpa_expediente">

														<?php echo $ficha['anio']; ?>
														&nbsp;
														<?php echo $ficha['tipo']; ?>
														&nbsp;
														<?php echo $ficha['numero']; ?>
														&nbsp;
														<?php echo $ficha['cuerpo']; ?>
														&nbsp;
														<?php echo $ficha['alcance']; ?>

													</div>
													<div class="mscpa_titulo mscpa_tit_caratula">Car&aacute;tula:</div>
													<div class="mscpa_caratula"><?php echo $ficha['caratula']; ?></div>

													<!-- PRIMERO EL VALOR Y LUEGO EL TITULO POR LA POSICION RIGHT -->
													<div class="mscpa_fecha_entrada"><?php echo $this->formatearFecha($ficha['fecha_entrada_expe']); ?></div>
													<div class="mscpa_titulo mscpa_tit_fecha">Fecha Expe.:</div>
												</div>
												<div style="height:19px;clear:both;">
													<div class="mscpa_titulo">Tema:&nbsp;</div>
													<div class="mscpa_tema_e_iniciador_categoria_y_autor">
														<select name="temas" class="msc_combo">
														<?php
														//SE OBTIENEN LOS Temas DEL Expediente RESULTANTE
														$temas = $modelo->obtenerTemasFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
														$cant_t = count($temas);
														for ($t=0; $t < $cant_t; $t++)
														{
															$tema = &$temas[$t];
														?>
															<option value="<?php echo $t; ?>"><?php echo $tema['descripcion_tema']; ?></option>
														<?php
														}
														?>
														</select>
													</div>
													<div class="mscpa_titulo">Iniciador:&nbsp;</div>
													<div class="mscpa_tema_e_iniciador_categoria_y_autor"><?php echo $ficha['iniciador']; ?></div>
												</div>
												<div style="height:19px;clear:both;">
													<div class="mscpa_titulo">Categor&iacute;a:&nbsp;</div>
													<div class="mscpa_tema_e_iniciador_categoria_y_autor"><?php echo $ficha['categoria']; ?></div>
													<div class="mscpa_titulo">Autor:&nbsp;</div>
													<div class="mscpa_tema_e_iniciador_categoria_y_autor">
														<select name="autores" class="msc_combo">
														<?php
														//SE OBTIENEN LOS Autores DEL Expediente RESULTANTE
														$autores = $modelo->obtenerAutoresFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
														$cant_a = count($autores);
														for ($a=0; $a < $cant_a; $a++)
														{
															$autor = &$autores[$a];
														?>
															<option value="<?php echo $a; ?>"><?php echo $autor['descripcion_grp']; ?></option>
														<?php
														}
														?>
														</select>
													</div>
												</div>
												<?php
												//SE OBTIENEN LOS Proyectos DEL Expediente RESULTANTE (LUEGO MOSTRAR UNA FICHA POR CADA UNO!!!)
												$proyectos = $modelo->obtenerProyectosFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);

												//SE MUESTRAN LOS Proyectos DEL Expediente RESULTANTE
												$cant_p = count($proyectos);
												for ($p=0; $p < $cant_p; $p++)
												{
													$proyecto = &$proyectos[$p];
												?>
													<div style="height:19px;clear:both;">
														<div class="mscpa_titulo">Proyecto:&nbsp;</div>
														<div class="lcg_nrotipo_proyecto"><?php echo $p+1; //CONTADOR DE PROYECTOS ?></div>
														<div class="lcg_descripcion_proyecto"><?php echo $proyecto['descripcion_proyecto']; ?></div>
													</div>
													<div style="height:40px;clear:both;">
														<div class="mscpa_titulo" style="height:37px;">Extracto:&nbsp;</div>
														<div class="mscpa_extracto"><?php echo $proyecto['extracto']; ?></div>
													</div>
												<?php
												}
												// SE OBTIENE EL Estado DEL Expediente RESULTANTE _para_Listados
												$estado = $modelo->obtenerEstadoFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance'], $filtro['l_estado']);
												?>
												<div style="height:16px;margin-top:3px;clear:both;">
													<div class="mscpa_titulo">Estado:&nbsp;</div>
													<div class="mscpa_estado_comision"><?php echo $estado[0]['nombre_estado']; ?></div>
													<div class="lec_fecha_estado"><?php echo " <b>Desde el</b> ".$this->formatearFecha($estado[0]['fecha_estado']); ?></div>
												</div>

												<?php
												/*  MODIFICADO: 26/07/2012 PARA VER TODAS LAS COMISIONES *
												if ( $filtro['l_comision_tipo'] != '' && $filtro['l_comision_codigo'] != '' )
												{
												/**/
													//SE OBTIENE LA Comision DEL Expediente RESULTANTE
													$comision = $modelo->obtenerComisionFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);

													if ( $comision[0]['comision'] != '' )
													{
												?>
														<div style="height:19px;clear:both;">
															<div class="mscpa_titulo">Comisi&oacute;n:&nbsp;</div>
															<div class="mscpa_estado_comision"><?php echo $comision[0]['comision']; ?></div>
															<div class="lec_fecha_estado"><?php echo " <b>Desde el</b> ".$this->formatearFecha($comision[0]['fecha_giro']); ?></div>
														</div>
												<?php
													}
												/**
												}
												/**/

												//Se crea una instancia del modelo
												//$modeloEnComision = new expedEnComisionModel();
												//SE OBTIENEN LOS Giros DEL Expediente RESULTANTE
												$giros = $modelo->obtenerGiros($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
												?>
												<div style="text-align:left;clear:both;">
													<?php
													//SI EXISTE ALGUN GIRO, SE MUESTRA
													if ( isset($giros[0]['comision_codigo']) )
													{
														$cantidad_giros=count($giros);
														for ($g=0; $g < $cantidad_giros; $g++)
														{
															$giro = &$giros[$g];

															echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$giro['comision_codigo'].'&nbsp;&nbsp;&nbsp;'.$giro['descripcion_grp'].'&nbsp;&nbsp;&nbsp;'.$this->formatearFecha($giro['fecha_entrada_giro']).'&nbsp;&nbsp;&nbsp;'.$this->formatearFecha($giro['fecha_salida_giro']).'&nbsp;&nbsp;&nbsp;'.$giro['dictamen_giro'].'<br>';
														}
													}
													?>
												</div>

												<div style="height:5px;font-size:0;clear:both;"></div>
												<div style="height:19px;clear:both;">
													<div class="cyl_boton_ver_expediente_y_ficha">
														<a href="javascript:verExpediente('<?php echo $ficha['anio']; ?>', '<?php echo $ficha['tipo']; ?>', '<?php echo $ficha['numero']; ?>', '<?php echo $ficha['cuerpo']; ?>', '<?php echo $ficha['alcance']; ?>', '<?php echo $filtro['l_tipo_listado']; ?>');">Ver exped.</a>
													</div>
													<div class="cyl_boton_ver_expediente_y_ficha">
														<a href="javascript:modalGabyFicha('consultas/index.php?controlador=ficha&accion=ver_ficha_modal&anio=<?php echo $ficha['anio']; ?>&tipo=<?php echo $ficha['tipo']; ?>&numero=<?php echo $ficha['numero']; ?>&cuerpo=<?php echo $ficha['cuerpo']; ?>&alcance=<?php echo $ficha['alcance']; ?>');">Ficha</a>
													</div>
												</div>
											</div>
								<?php
									} // FIN DEL if DE VERIFICACIÓN
								} // FIN DEL for
								?>
								<script>
								  var scroller = new Fx.Scroll($('msl_borde2'));
								  scroller.toTop();
								</script>
							<?php
							} // FIN DEL else B
						} // FIN DEL else A
						?>
					</div><!-- FIN DE msl_borde2 -->
				</div><!-- FIN DE msl_borde1 -->

			</form>
	    </div>
	    <script>

		    var menuDrag = new Drag.Move($('contenidoAjaxResultadoListados'), {
			   handle: $('dragger_titulo_listado')
			});

			//CALENDARIO PARA LA FECHA DESDE
			var calDesde = new Zapatec.Calendar.setup({

				inputField:"l_fecha_desde",
				ifFormat:"%d/%m/%Y",
				button:"msl_btffecha_desde",
				showsTime:false
			});
			//CALENDARIO PARA LA FECHA HASTA
			var calHasta = new Zapatec.Calendar.setup({

				inputField:"l_fecha_hasta",
				ifFormat:"%d/%m/%Y",
				button:"msl_btffecha_hasta",
				showsTime:false
			});

			function buscar()
			{
				var mensaje = '';
				var error = false;

				if ( $('l_fecha_desde').value == '' )
				{
					error = true;
					mensaje = "Debe ingresar un rango de Fecha.<br>";
				}
				if ( $('l_fecha_hasta').value == '' )
				{
					error = true;
					mensaje = "Debe ingresar un rango de Fecha.<br>";
				}

				/* 26/07/2012 *
				if ( $('c_comision').disabled == false && $('c_comision').value == 0 )
				{
					error = true;
					mensaje = "Debe ingresar una Comisi"+'\u00f3'+"n.";
				}
				/**/

				/* 26/07/2012 *
				if ( esLaFechaMayor($('l_fecha_comision').value, $('l_fecha_hasta').value) )
				{
					error = true;
					mensaje = "La fecha l"+'\u00ed'+"mite de ingreso de la Comisi"+'\u00f3'+"n no debe ser mayor a la fecha Hasta del criterio de b"+'\u00fa'+"squeda.";
				}
				/**/

				if ( esLaFechaMayor($('l_fecha_desde').value, $('l_fecha_hasta').value) )
				{
					error = true;
					mensaje = "La fecha Desde debe ser menor a la fecha Hasta en el criterio de b"+'\u00fa'+"squeda.";
				}

				if ( error )
				{
					alert(mensaje);
				}
				else
				{
					enviarForm('formListados', 'listados', 'contenidoAjaxResultadoListados');
				}
			}

			function limpiar()
			{
				$('l_fecha_desde').value = '<?php echo '01/01/'.( date("Y")-10 ); ?>';
				$('l_fecha_hasta').value = '<?php echo date("d/m/Y"); ?>';
				$('l_fecha_comision').value = '<?php echo date("d/m/Y"); ?>';
				$('c_comision').value = 0;
				$('c_estado').value = 0;
				$('c_estado').disabled = false;
				$('imagen_zoom_estados').setStyle('display', 'inline');
				$('c_comision').disabled = false;
				$('imagen_zoom_comisiones').setStyle('display', 'inline');
			}

			$('c_comision').value = <?php echo ($_SESSION['filtro_LISTADOS']['l_comision']) ? "'".$_SESSION['filtro_LISTADOS']['l_comision']."'" : 0 ; ?>;

			if ( $('c_comision').value != 0 )
			{
				$('c_estado').disabled = true;
				$('imagen_zoom_estados').setStyle('display', 'none');
			}
			else
			{
				$('c_estado').disabled = false;
				$('imagen_zoom_estados').setStyle('display', 'inline');
			}

			//CALENDARIO PARA LA FECHA DE LA COMISION
			var calComision = new Zapatec.Calendar.setup({

				inputField:"l_fecha_comision",
				ifFormat:"%d/%m/%Y",
				button:"msl_btffecha_comision",
				showsTime:false
			});

			function refrescarCombos(habilitado)
			{
				refrescarCombo('listados/index.php?controlador=exped_en_comision&accion=refrescarComboComisiones&habilitado='+habilitado+'&comision=<?php echo $_SESSION['filtro_LISTADOS']['l_comision']; ?>','msl_dato_filtro_comision');
				refrescarCombo('listados/index.php?controlador=exped_en_comision&accion=refrescarComboEstados&habilitado='+habilitado+'&estado=<?php echo $_SESSION['filtro_LISTADOS']['l_estado']; ?>','msl_dato_filtro_estado');
			}

			$('c_comision').addEvent('change', function()
			{
				// SI SE ELIGIO UNA COMISION
				if ( $('c_comision').value != 0 )
				{
					// SE DESHABILITA EL COMBO DE ESTADOS
					$('c_estado').disabled = true;
					$('imagen_zoom_estados').setStyle('display', 'none');

					if ( $('l_tipo_listado').value == "exped_en_comision" )
					{
						$('vencidos').disabled = false;
					}
				}
				else // SINO
				{
					// SE HABILITA EL COMBO DE ESTADOS
					$('c_estado').disabled = false;
					$('imagen_zoom_estados').setStyle('display', 'inline');

					if ( $('l_tipo_listado').value == "exped_en_comision" )
					{
						$('vencidos').disabled = true;
					}
				}
			});

			$('c_estado').addEvent('change', function()
			{
				// SI SE ELIGIO UN ESTADO
				if ( $('c_estado').value != 0 )
				{
					// SE DESHABILITAN EL COMBO DE COMISIONES Y LA FECHA ( DE INGRESADOS EN LA COMISION ANTES DEL... )
					$('c_comision').disabled = true;
					$('l_fecha_comision').disabled = true;
					$('msl_btffecha_comision').setStyle('display', 'none');//'visibility', 'hidden'
					$('imagen_zoom_comisiones').setStyle('display', 'none');

					if ( $('l_tipo_listado').value == "exped_en_comision" )
					{
						// SE DESHABILITA EL CHECKBOX DE Sólo Vencidos
						$('vencidos').disabled = true;
					}
				}
				else // SINO
				{
					// SE HABILITAN EL COMBO DE COMISIONES Y LA FECHA ( DE INGRESADOS EN LA COMISION ANTES DEL... )
					$('c_comision').disabled = false;
					$('l_fecha_comision').disabled = false;
					$('msl_btffecha_comision').setStyle('display', 'inline');//'visibility', 'visible'
					$('imagen_zoom_comisiones').setStyle('display', 'inline');

					if ( $('l_tipo_listado').value == "exped_en_comision" )
					{
						// SE HABILITA EL CHECKBOX DE Sólo Vencidos
						$('vencidos').disabled = false;
					}
				}
			});

			// SI SE ELIGIO UN ESTADO
			if ( $('c_estado').value != 0 )
			{
				// SE DESHABILITAN EL COMBO DE COMISIONES Y LA FECHA ( DE INGRESADOS EN LA COMISION ANTES DEL... )
				$('c_comision').disabled = true;
				$('l_fecha_comision').disabled = true;
				$('msl_btffecha_comision').setStyle('display', 'none');//'visibility', 'hidden'
				$('imagen_zoom_comisiones').setStyle('display', 'none');

				if ( $('l_tipo_listado').value == "exped_en_comision" )
				{
					// SE DESHABILITA EL CHECKBOX DE Sólo Vencidos
					$('vencidos').disabled = true;
				}
			}
			else
			{
				// SE HABILITAN EL COMBO DE COMISIONES Y LA FECHA ( DE INGRESADOS EN LA COMISION ANTES DEL... )
				$('c_comision').disabled = false;
				$('l_fecha_comision').disabled = false;
				$('msl_btffecha_comision').setStyle('display', 'inline');//'visibility', 'visible'
				$('imagen_zoom_comisiones').setStyle('display', 'inline');

				if ( $('l_tipo_listado').value == "exped_en_comision" )
				{
					// SE HABILITA EL CHECKBOX DE Sólo Vencidos
					$('vencidos').disabled = false;
				}
			}

			<?php
			if ( $_SESSION['filtro_LISTADOS']['l_solo_habilitado'] == '' )
			{
			?>
				$('habilitado').checked = true;
				$('l_solo_habilitado').value = 1;
			<?php
			}
			elseif ( $_SESSION['filtro_LISTADOS']['l_solo_habilitado'] == 0 )
			{
			?>
				$('habilitado').checked = false;
				$('l_solo_habilitado').value = 0;
			<?php
			}
			else
			{
			?>
				$('habilitado').checked = true;
				$('l_solo_habilitado').value = 1;
			<?php
			}
			?>

			if ( $('c_estado') )
			{
				$('c_estado').value = <?php echo ($_SESSION['filtro_LISTADOS']['l_estado']) ? "'".$_SESSION['filtro_LISTADOS']['l_estado']."'" : 0 ; ?>;
			}

			<?php
			if ( $filtro['l_enviado'] )
			{
			?>
				$('btImpresion').addEvent('click', function()
				{
				    if ( $('l_cantidad_TOTAL').value >= 5000 )
				    {
					  alert("El resultado de su consulta es muy largo para imprimir.");
				    }
				    else
				    {
					    $('btImpresion').setProperty('href', 'listados/index.php?controlador=exped_en_comision&accion=armar_listado_completo&l_tipo_listado='+$('l_tipo_listado').value+'&formato=impresion&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_estado='+$('c_estado').value+'&l_fecha_comision='+$('l_fecha_comision').value+'&l_comision='+$('c_comision').value+'');

					    $('btImpresion').setProperty('target', '_blank');
					    $('contenedor_btImpresion').setStyle('display', 'none');
					    $('contenedor_btProcesarTexto').setStyle('display', 'none');
				    }
				});

				$('btProcesarTexto').addEvent('click', function()
				{
				    if ( $('l_cantidad_TOTAL').value >= 5000 )
				    {
					  alert("El resultado de su consulta es muy largo para procesar su texto.");
				    }
				    else
				    {

						$('btProcesarTexto').setProperty('href', 'listados/index.php?controlador=exped_en_comision&accion=armar_listado_completo&l_tipo_listado='+$('l_tipo_listado').value+'&formato=texto&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_fecha_comision='+$('l_fecha_comision').value+'&l_comision='+$('c_comision').value+'&l_estado='+$('c_estado').value+'');

					    $('btProcesarTexto').setProperty('target', '_blank');
					    $('contenedor_btImpresion').setStyle('display', 'none');
					    $('contenedor_btProcesarTexto').setStyle('display', 'none');
				    }
				});
			<?php
			}

			/**/
			// 27/11/2012
			// SI SE VUELVE DE HABER VISTO EL EXPEDIENTE
			if ( $_SESSION['cerrar_modal_LISTADOS'] == 'no' )
			{
				$_SESSION['cerrar_modal_LISTADOS'] = null;
			?>
				refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_estado='+$('c_estado').value+'&l_comision='+$('c_comision').value+'&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_solo_habilitado='+$('l_solo_habilitado').value+'&l_fecha_comision='+$('l_fecha_comision').value+'&l_enviado=enviado&l_tipo_listado=<?php echo $filtro['l_tipo_listado']; ?>&l_pagina=<?php echo $_SESSION['filtro_LISTADOS']['l_pagina']; ?>', 'contenidoAjaxResultadoListados');
			<?php
			}
			/**/
			?>

			$('l_fecha_desde').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
					$('msl_btffecha_comision').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;
						$('msl_btffecha_comision').disabled = true;

						buscar();
					}
				}
			});

			$('l_fecha_hasta').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
					$('msl_btffecha_comision').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;
						$('msl_btffecha_comision').disabled = true;

						buscar();
					}
				}
			});

			$('l_fecha_comision').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
					$('msl_btffecha_comision').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;
						$('msl_btffecha_comision').disabled = true;

						buscar();
					}
				}
			});

		</script>
	<?php
	}

	public function listar_orden_del_dia($listado = '', $listadoIniciadores = '', $listadoAutores = '', $listadoComisiones = '', $listadoCategorias = '', $listadoTemas = '', $listadoEstados = '', $listadoGiros = '', $mensaje = '', $filtro = '')
	{
	    if ( $mensaje != '' ){ echo '<div class="abm_mensaje_resultado">'.$mensaje.'</div>'; }
	?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

	    <div id="precarga_modal" style="display:none"></div>
	    <div id="contenidoAjaxResultadoListados" class="msc_gral msc_texto" style="width:700px;">

			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>

			<form action="listados/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">

				<input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
				<input type="hidden" name="accion" id="accion" value="listar" />
				<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
				<input type="hidden" name="l_enviado" id="l_enviado" value="enviado" />
				<input type="hidden" name="l_tipo_listado" id="l_tipo_listado" value="<?php echo $filtro['l_tipo_listado']; ?>" />
				<input type="hidden" name="l_cantidad_TOTAL" id="l_cantidad_TOTAL" value="<?php echo $filtro['l_cantidad']; ?>" />

				<div id="dragger_titulo_listado" class="msc_titulos degradado">Orden del D&iacute;a</div>
				<div style="height:10px;font-size:0;"></div>

				<div style="height:135px;">
					<div style="width:550px;height:135px;float:left;">
						<div class="msl_criterio_fechas_margen"></div>
						<div class="msl_criterio_fechas_nombres">
							<div class="msc_dato_filtro">Ingresados desde el:</div>
						</div>
						<?php
						if ( $filtro['l_enviado'] )
						{
							$l_fecha_desde = $this->formatearFecha($_SESSION['filtro_LISTADOS']['l_fecha_desde']);
						}
						else
						{
							// SE CALCULA LA FECHA 10 AÑOS ANTES DEL ACTUAL
							$l_fecha_desde = '01/01/'.( date("Y")-10 );
						}
						?>
						<div class="msl_criterio_calendario">
							<input type="text" id="l_fecha_desde" name="l_fecha_desde" value="<?php echo $l_fecha_desde; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
							<input type="image" id="msl_btffecha_desde" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Desde" width="16" height="16">
						</div>

						<div class="msl_criterio_fechas_nombres">
							<div class="msc_dato_filtro">Hasta el:</div>
						</div>
						<div class="msl_criterio_calendario">
							<input type="text" id="l_fecha_hasta" name="l_fecha_hasta" value="<?php echo($_SESSION['filtro_LISTADOS']['l_fecha_hasta']) ? $this->formatearFecha($_SESSION['filtro_LISTADOS']['l_fecha_hasta']) : date("d/m/Y") ; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
							<input type="image" id="msl_btffecha_hasta" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Hasta" width="16" height="16">
						</div>

						<div class="msl_criterio_fechas_nombres">
							<div class="msc_dato_filtro">en Mesa de Entradas del H.C.D.</div>
						</div>

						<div class="msl_criterio_seccion_comisiones_y_estados">
							<div class="msl_criterio_seccion_nombres_y_combos">
								<div class="msl_criterio_margen"></div>
								<div class="msl_criterio_nombres">
									<div class="msc_dato_filtro">En comisi&oacute;n de:</div>
								</div>
								<div class="msl_criterio_combos">
									<div id="msl_dato_filtro_comision" class="msc_dato_filtro">

										<select id="c_comision" name="l_comision" class="msl_combo" style="width:207px;">
											<option value="0">0, TODAS</option>
											<?php
											$cant_comisiones = count($listadoComisiones);
											for ($c=0; $c < $cant_comisiones; $c++)
											{
												$comision = &$listadoComisiones[$c];
											?>
												<option value="<?php echo $comision['tipo_grp'].'-'.$comision['codigo_grp']; ?>"><?php echo $comision['tipo_grp'].', '.$comision['codigo_grp'].', '.$comision['descripcion_grp']; ?></option>
											<?php
											}
											?>
										</select>
										&nbsp;
										<a id="imagen_zoom_comisiones" href="javascript:modalGaby('consultas/index.php?controlador=consulta_gral&accion=pedirNombreComisionModal&c_solo_habilitado='+$('l_solo_habilitado').value+'');" title="Buscar por Nombre de Comisi&oacute;n"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
									</div>
								</div>
							</div>
							<div class="msl_criterio_seccion_nombres_y_combos" style="text-align:left;">
								<div class="msl_criterio_margen"></div>
								<div class="msl_criterio_nombres">
									<div class="msc_dato_filtro">S&oacute;lo Habilitados</div>
								</div>
								<div class="msl_criterio_combos">
									<input type="hidden" name="l_solo_habilitado" id="l_solo_habilitado" value="<?php echo($_SESSION['filtro_LISTADOS']['l_solo_habilitado']) ? $_SESSION['filtro_LISTADOS']['l_solo_habilitado'] : 1; ?>" />
									<input type="checkbox" name="habilitado" id="habilitado" onchange="javascript:chequear('l_solo_habilitado');refrescarCombos($('l_solo_habilitado').value);" >
								</div>
							</div>
						</div>
					</div>
					<div class="msl_criterio_seccion_botones">
						<?php
						if ($filtro['l_enviado'] && $listado != '')
						{
						?>
							<div id="contenedor_btImpresion" class="msc_boton degradado">
								<a id="btImpresion" href="#" title="Formato para Impresi&oacute;n" >
								  <img src="imagenes/barra/print_16x16.gif" width="16" height="16" align="left" />&nbsp;&nbsp;&nbsp;Impresi&oacute;n
								</a>
							</div>

							<div id="contenedor_btProcesarTexto" class="msc_boton degradado">
								<a id="btProcesarTexto" href="#" title="Procesador de Texto" >
								  <img src="imagenes/iconos_office/doc.jpg" width="16" height="16" align="left" />&nbsp;&nbsp;&nbsp;Proc.Texto
								</a>
							</div>
						<?php
						}
						?>
						<div class="msc_boton degradado">
							<a href="javascript:buscar();">
								<img src="imagenes/zoom_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Buscar
							</a>
						</div>
						<div class="msc_boton degradado">
							<a href="javascript:limpiar();">
								<img src="imagenes/barra/limpiar.jpeg" width="16" height="16" />&nbsp;&nbsp;&nbsp;Limpiar
							</a>
						</div>
						<div class="msc_boton degradado">
							<a href="javascript:cerrarModalNueva();">
								<img src="imagenes/barra/error_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Cerrar
							</a>
						</div>
					</div><!-- FIN DE msl_criterio_seccion_botones -->
				</div>

				<?php
				if ( $filtro['l_enviado'] != '' && $listado != '' )
				{
					$urlPrimero = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_comision='+$('c_comision').value+'&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=1', 'contenidoAjaxResultadoListados');";
					$urlAnterior = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_comision='+$('c_comision').value+'&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=".$filtro['l_pagina_ant']."', 'contenidoAjaxResultadoListados');";
					$urlSiguiente = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_comision='+$('c_comision').value+'&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=".$filtro['l_pagina_sgte']."', 'contenidoAjaxResultadoListados');";
					$urlUltimo = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_comision='+$('c_comision').value+'&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=".$filtro['l_nro_paginas']."', 'contenidoAjaxResultadoListados');";
				?>
					<!-- PAGINADOR -->
					<div class="msc_paginador">
						<div class="msc_margen_paginador"></div>
						<div class="msc_flechas_paginador">
							<?php
							if ( $filtro['l_pagina'] != 1 )
							{
							?>
								<a id="btPrimero" title="Primer Registro" href="javascript:<?php echo $urlPrimero; ?>">
									<img id="imgPrimero" src="imagenes/barra/b_firstpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btPrimero" href="#">
									<img id="imgPrimero" src="imagenes/barra/bd_firstpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							if ( $filtro['l_pagina_ant'] != 0 )
							{
							?>
								<a id="btAnterior" title="Registro Anterior" href="javascript:<?php echo $urlAnterior; ?>">
									<img id="imgAnterior" src="imagenes/barra/b_prevpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btAnterior" href="#">
									<img id="imgAnterior" src="imagenes/barra/bd_prevpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							?>
						</div>
						<div class="msc_detalle_paginador"><?php echo $filtro['l_pagina'].' de '.$filtro['l_nro_paginas']; ?></div>
						<div class="msc_flechas_paginador">
							<?php
							if ($filtro['l_pagina'] != $filtro['l_nro_paginas'])
							{
							?>
								<a id="btSiguiente" title="Registro Siguiente" href="javascript:<?php echo $urlSiguiente; ?>">
									<img id="imgSiguiente" src="imagenes/barra/b_nextpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btSiguiente" href="#">
									<img id="imgSiguiente" src="imagenes/barra/bd_nextpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}

							if ($filtro['l_pagina'] != $filtro['l_nro_paginas']){
							?>
								<a id="btUltimo" title="Ultimo Registro" href="javascript:<?php echo $urlUltimo; ?>">
									<img id="imgUltimo" src="imagenes/barra/b_lastpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btUltimo" href="#">
									<img id="imgUltimo" src="imagenes/barra/bd_lastpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							?>
						</div>
					</div>
					<!-- FIN DEL PAGINADOR -->
				<?php
				}
				?>

				<div class="msl_borde1">
					<div id="msl_borde2" class="msl_borde2">
						<?php
						if ( !$filtro['l_enviado'] )
						{
						?>
							<!-- FONDO MOSTRADO ANTES DE LA BUSQUEDA  -->
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
						<?php
						}
						else // A
						{
							if ( $listado == '' )
							{
								echo '<br><h1>Sin resultados</h1>';
							}
							else // B
							{
								//Se crea una instancia del modelo
								$modelo = new expedEnComisionModel();

								$inicio = 0;
								$final = count($listado);

								//SE LISTAN LAS FICHAS DE LOS EXPEDIENTES
								for ($exp=$inicio; $exp < $final; $exp++)
								{
									$ficha = &$listado[$exp];

									if ( $ficha['anio'] )
									{
								?>
										<div class="mscpa_gral">
											<div style="height:19px;clear:both;">
												<div class="mscpa_expediente">

													<?php echo $ficha['anio']; ?>
													&nbsp;
													<?php echo $ficha['tipo']; ?>
													&nbsp;
													<?php echo $ficha['numero']; ?>
													&nbsp;
													<?php echo $ficha['cuerpo']; ?>
													&nbsp;
													<?php echo $ficha['alcance']; ?>

												</div>
												<div class="mscpa_titulo mscpa_tit_caratula">Car&aacute;tula:</div>
												<div class="mscpa_caratula"><?php echo $ficha['caratula']; ?></div>

												<!-- PRIMERO EL VALOR Y LUEGO EL TITULO POR LA POSICION RIGHT -->
												<div class="mscpa_fecha_entrada"><?php echo $this->formatearFecha($ficha['fecha_entrada_expe']); ?></div>
												<div class="mscpa_titulo mscpa_tit_fecha">Fecha Expe.:</div>
											</div>
											<div style="height:19px;clear:both;">
												<div class="mscpa_titulo">Tema:&nbsp;</div>
												<div class="mscpa_tema_e_iniciador_categoria_y_autor">
													<select name="temas" class="msc_combo">
													<?php
													//SE OBTIENEN LOS Temas DEL Expediente RESULTANTE
													$temas = $modelo->obtenerTemasFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
													$cant_t = count($temas);
													for ($t=0; $t < $cant_t; $t++)
													{
														$tema = &$temas[$t];
													?>
														<option value="<?php echo $t; ?>"><?php echo $tema['descripcion_tema']; ?></option>
													<?php
													}
													?>
													</select>
												</div>
												<div class="mscpa_titulo">Iniciador:&nbsp;</div>
												<div class="mscpa_tema_e_iniciador_categoria_y_autor"><?php echo $ficha['iniciador']; ?></div>
											</div>
											<div style="height:19px;clear:both;">
												<div class="mscpa_titulo">Categor&iacute;a:&nbsp;</div>
												<div class="mscpa_tema_e_iniciador_categoria_y_autor"><?php echo $ficha['categoria']; ?></div>
												<div class="mscpa_titulo">Autor:&nbsp;</div>
												<div class="mscpa_tema_e_iniciador_categoria_y_autor">
													<select name="autores" class="msc_combo">
													<?php
													//SE OBTIENEN LOS Autores DEL Expediente RESULTANTE
													$autores = $modelo->obtenerAutoresFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
													$cant_a = count($autores);
													for ($a=0; $a < $cant_a; $a++)
													{
														$autor = &$autores[$a];
													?>
														<option value="<?php echo $a; ?>"><?php echo $autor['descripcion_grp']; ?></option>
													<?php
													}
													?>
													</select>
												</div>
											</div>
											<?php
											//SE OBTIENEN LOS Proyectos DEL Expediente RESULTANTE (LUEGO MOSTRAR UNA FICHA POR CADA UNO!!!)
											$proyectos = $modelo->obtenerProyectosFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);

											//SE MUESTRAN LOS Proyectos DEL Expediente RESULTANTE
											$cant_p = count($proyectos);
											for ($p=0; $p < $cant_p; $p++)
											{
												$proyecto = &$proyectos[$p];
											?>
												<div style="height:19px;clear:both;">
													<div class="mscpa_titulo">Proyecto:&nbsp;</div>
													<div class="lcg_nrotipo_proyecto"><?php echo $p+1; //CONTADOR DE PROYECTOS ?></div>
													<div class="lcg_descripcion_proyecto"><?php echo $proyecto['descripcion_proyecto']; ?></div>
												</div>
												<div style="height:40px;clear:both;">
													<div class="mscpa_titulo" style="height:37px;">Extracto:&nbsp;</div>
													<div class="mscpa_extracto"><?php echo $proyecto['extracto']; ?></div>
												</div>
											<?php
											}
											// SE OBTIENE EL Estado DEL Expediente RESULTANTE _para_Listados
											$estado = $modelo->obtenerEstadoFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance'], $filtro['l_estado']);
											?>
											<div style="height:16px;margin-top:3px;clear:both;">
												<div class="mscpa_titulo">Estado:&nbsp;</div>
												<div class="mscpa_estado_comision"><?php echo $estado[0]['nombre_estado']; ?></div>
												<div class="lec_fecha_estado"><?php echo " <b>Desde el</b> ".$this->formatearFecha($estado[0]['fecha_estado']); ?></div>
											</div>

											<?php
											/*  MODIFICADO: 26/07/2012 PARA VER TODAS LAS COMISIONES *
											if ( $filtro['l_comision_tipo'] != '' && $filtro['l_comision_codigo'] != '' )
											{
											/**/
												//SE OBTIENE LA Comision DEL Expediente RESULTANTE
												$comision = $modelo->obtenerComisionFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
											?>
												<div style="height:19px;clear:both;">
													<div class="mscpa_titulo">Comisi&oacute;n:&nbsp;</div>
													<div class="mscpa_estado_comision"><?php echo $comision[0]['comision']; ?></div>
													<div class="lec_fecha_estado"><?php echo " <b>Desde el</b> ".$this->formatearFecha($comision[0]['fecha_giro']); ?></div>
												</div>
											<?php
											/**
											}
											/**/
											?>
											<div style="height:5px;font-size:0;clear:both;"></div>
											<div style="height:19px;clear:both;">
												<div class="cyl_boton_ver_expediente_y_ficha">
													<a href="javascript:verExpediente('<?php echo $ficha['anio']; ?>', '<?php echo $ficha['tipo']; ?>', '<?php echo $ficha['numero']; ?>', '<?php echo $ficha['cuerpo']; ?>', '<?php echo $ficha['alcance']; ?>', '<?php echo $filtro['l_tipo_listado']; ?>');">Ver exped.</a>
												</div>
												<div class="cyl_boton_ver_expediente_y_ficha">
													<a href="javascript:modalGabyFicha('consultas/index.php?controlador=ficha&accion=ver_ficha_modal&anio=<?php echo $ficha['anio']; ?>&tipo=<?php echo $ficha['tipo']; ?>&numero=<?php echo $ficha['numero']; ?>&cuerpo=<?php echo $ficha['cuerpo']; ?>&alcance=<?php echo $ficha['alcance']; ?>');">Ficha</a>
												</div>
											</div>
										</div>
								<?php
									} // FIN DEL if DE VERIFICACIÓN
								} // FIN DEL for
								?>
								<script>
								  var scroller = new Fx.Scroll($('msl_borde2'));
								  scroller.toTop();
								</script>
							<?php
							} // FIN DEL else B
						} // FIN DEL else A
						?>
					</div><!-- FIN DE msl_borde2 -->
				</div><!-- FIN DE msl_borde1 -->

			</form>
	    </div>
	    <script>
			var menuDrag = new Drag.Move($('contenidoAjaxResultadoListados'), {
			   handle: $('dragger_titulo_listado')
			});

			//CALENDARIO PARA LA FECHA DESDE
			var calDesde = new Zapatec.Calendar.setup({

				inputField:"l_fecha_desde",
				ifFormat:"%d/%m/%Y",
				button:"msl_btffecha_desde",
				showsTime:false
			});
			//CALENDARIO PARA LA FECHA HASTA
			var calHasta = new Zapatec.Calendar.setup({

				inputField:"l_fecha_hasta",
				ifFormat:"%d/%m/%Y",
				button:"msl_btffecha_hasta",
				showsTime:false
			});

			function buscar()
			{
				var mensaje = '';
				var error = false;

				if ( $('l_fecha_desde').value == '' )
				{
					error = true;
					mensaje = "Debe ingresar un rango de Fecha.<br>";
				}
				if ( $('l_fecha_hasta').value == '' )
				{
					error = true;
					mensaje = "Debe ingresar un rango de Fecha.<br>";
				}

				if ( $('c_comision').disabled == false && $('c_comision').value == 0 )
				{
					error = true;
					mensaje = "Debe ingresar una Comisi"+'\u00f3'+"n.";
				}

				if ( esLaFechaMayor($('l_fecha_desde').value, $('l_fecha_hasta').value) )
				{
					error = true;
					mensaje = "La fecha Desde debe ser menor a la fecha Hasta en el criterio de b"+'\u00fa'+"squeda.";
				}

				if ( error )
				{
					alert(mensaje);
				}
				else
				{
					enviarForm('formListados', 'listados', 'contenidoAjaxResultadoListados');
				}
			}

			function limpiar()
			{
				$('l_fecha_desde').value = '<?php echo '01/01/'.( date("Y")-10 ); ?>';
				$('l_fecha_hasta').value = '<?php echo date("d/m/Y"); ?>';
				$('c_comision').value = 0;
			}

			$('c_comision').value = <?php echo ($_SESSION['filtro_LISTADOS']['l_comision']) ? "'".$_SESSION['filtro_LISTADOS']['l_comision']."'" : 0 ; ?>;

			function refrescarCombos(habilitado)
			{
				refrescarCombo('listados/index.php?controlador=exped_en_comision&accion=refrescarComboComisiones&habilitado='+habilitado+'&comision=<?php echo $_SESSION['filtro_LISTADOS']['l_comision']; ?>','msl_dato_filtro_comision');
			}

			<?php
			if ( $_SESSION['filtro_LISTADOS']['l_solo_habilitado'] == '' )
			{
			?>
				$('habilitado').checked = true;
				$('l_solo_habilitado').value = 1;
			<?php
			}
			elseif ( $_SESSION['filtro_LISTADOS']['l_solo_habilitado'] == 0 )
			{
			?>
				$('habilitado').checked = false;
				$('l_solo_habilitado').value = 0;
			<?php
			}
			else
			{
			?>
				$('habilitado').checked = true;
				$('l_solo_habilitado').value = 1;
			<?php
			}
			?>

			<?php
			if ( $filtro['l_enviado'] )
			{
			?>
				$('btImpresion').addEvent('click', function()
				{
				    if ( $('l_cantidad_TOTAL').value >= 5000 )
				    {
					  alert("El resultado de su consulta es muy largo para imprimir.");
				    }
				    else
				    {
						$('btImpresion').setProperty('href', 'listados/index.php?controlador=exped_en_comision&accion=armar_listado_completo&l_tipo_listado='+$('l_tipo_listado').value+'&formato=impresion&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_comision='+$('c_comision').value+'');

					    $('btImpresion').setProperty('target', '_blank');
					    $('contenedor_btImpresion').setStyle('display', 'none');
					    $('contenedor_btProcesarTexto').setStyle('display', 'none');
				    }
				});

				$('btProcesarTexto').addEvent('click', function()
				{
				    if ( $('l_cantidad_TOTAL').value >= 5000 )
				    {
						alert("El resultado de su consulta es muy largo para procesar su texto.");
				    }
				    else
				    {
						$('btProcesarTexto').setProperty('href', 'listados/index.php?controlador=exped_en_comision&accion=armar_listado_completo&l_tipo_listado='+$('l_tipo_listado').value+'&formato=texto&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_comision='+$('c_comision').value+'');

					    $('btProcesarTexto').setProperty('target', '_blank');
					    $('contenedor_btImpresion').setStyle('display', 'none');
					    $('contenedor_btProcesarTexto').setStyle('display', 'none');
				    }
				});
			<?php
			}

			/**/
			// 27/11/2012
			// SI SE VUELVE DE HABER VISTO EL EXPEDIENTE
			if ( $_SESSION['cerrar_modal_LISTADOS'] == 'no' )
			{
				$_SESSION['cerrar_modal_LISTADOS'] = null;
			?>
				refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_comision='+$('c_comision').value+'&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_enviado=enviado&l_tipo_listado=<?php echo $filtro['l_tipo_listado']; ?>&l_pagina=<?php echo $_SESSION['filtro_LISTADOS']['l_pagina']; ?>', 'contenidoAjaxResultadoListados');
			<?php
			}
			/**/
			?>

			$('l_fecha_desde').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;

						buscar();
					}
				}
			});

			$('l_fecha_hasta').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;

						buscar();
					}
				}
			});

		</script>
	<?php
	}

	public function listar_asuntos_entrados($listado = '', $mensaje = '', $filtro = '')
	{
	    if ( $mensaje != '' ){ echo '<div class="abm_mensaje_resultado">'.$mensaje.'</div>'; }
	?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

	    <div id="precarga_modal" style="display:none"></div>
	    <div id="contenidoAjaxResultadoListados" class="msc_gral msc_texto" style="width:700px;">

			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>

			<form action="listados/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">

				<input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
				<input type="hidden" name="accion" id="accion" value="listar" />
				<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
				<input type="hidden" name="l_enviado" id="l_enviado" value="enviado" />
				<input type="hidden" name="l_tipo_listado" id="l_tipo_listado" value="<?php echo $filtro['l_tipo_listado']; ?>" />
				<input type="hidden" name="l_cantidad_TOTAL" id="l_cantidad_TOTAL" value="<?php echo $filtro['l_cantidad']; ?>" />

				<div id="dragger_titulo_listado" class="msc_titulos degradado">Asuntos Entrados</div>
				<div style="height:10px;font-size:0;"></div>

				<div style="height:130px;">
					<div style="width:471px;height:125px;float:left;">
						<div class="msl_criterio_fechas_margen"></div>
						<div class="msl_criterio_fechas_nombres">
							<div class="msc_dato_filtro">Ingresados desde el:</div>
						</div>
						<?php
						if ( $filtro['l_enviado'] && $listado != '' )
						{
							$l_fecha_desde = $this->formatearFecha($_SESSION['filtro_LISTADOS']['l_fecha_desde']);
						}
						else
						{
							// Se calcula la fecha 30 dias antes de la actual
							$l_fecha_desde = $this->calcularFecha(-30);
						}
						?>
						<div class="msl_criterio_calendario">
							<input type="text" id="l_fecha_desde" name="l_fecha_desde" value="<?php echo $l_fecha_desde; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
							<input type="image" id="msl_btffecha_desde" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Desde" width="16" height="16">
						</div>

						<div class="msl_criterio_fechas_nombres">
							<div class="msc_dato_filtro">Hasta el:</div>
						</div>
						<div class="msl_criterio_calendario">
							<input type="text" id="l_fecha_hasta" name="l_fecha_hasta" value="<?php echo($_SESSION['filtro_LISTADOS']['l_fecha_hasta']) ? $this->formatearFecha($_SESSION['filtro_LISTADOS']['l_fecha_hasta']) : date("d/m/Y") ; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
							<input type="image" id="msl_btffecha_hasta" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Hasta" width="16" height="16">
						</div>
					</div>
					<div class="msl_criterio_seccion_botones">
						<?php
						if ( $filtro['l_enviado'] && $listado != '' )
						{
						?>
							<div id="contenedor_btImpresion" class="msc_boton degradado">
								<a id="btImpresion" href="#" title="Formato para Impresi&oacute;n" >
									<img src="imagenes/barra/print_16x16.gif" width="16" height="16" align="left" />&nbsp;&nbsp;&nbsp;Impresi&oacute;n
								</a>
							</div>

							<div id="contenedor_btProcesarTexto" class="msc_boton degradado">
								<a id="btProcesarTexto" href="#" title="Procesador de Texto" >
									<img src="imagenes/iconos_office/doc.jpg" width="16" height="16" align="left" />&nbsp;&nbsp;&nbsp;Proc.Texto
								</a>
							</div>
						<?php
						}
						?>
						<div class="msc_boton degradado">
							<a href="javascript:buscar();">
								<img src="imagenes/zoom_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Buscar
							</a>
						</div>
						<div class="msc_boton degradado">
							<a href="javascript:limpiar();">
								<img src="imagenes/barra/limpiar.jpeg" width="16" height="16" />&nbsp;&nbsp;&nbsp;Limpiar
							</a>
						</div>
						<div class="msc_boton degradado">
							<a href="javascript:cerrarModalNueva();">
								<img src="imagenes/barra/error_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Cerrar
							</a>
						</div>
					</div>
					<div style="height:7px;font-size:0;clear:both;"></div>
				</div>

				<?php
				if ( $filtro['l_enviado'] != '' && $listado != '' )
				{
					$urlPrimero = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=1', 'contenidoAjaxResultadoListados');";
					$urlAnterior = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=".$filtro['l_pagina_ant']."', 'contenidoAjaxResultadoListados');";
					$urlSiguiente = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=".$filtro['l_pagina_sgte']."', 'contenidoAjaxResultadoListados');";
					$urlUltimo = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=".$filtro['l_nro_paginas']."', 'contenidoAjaxResultadoListados');";
					?>
					<!-- PAGINADOR -->
					<div class="msc_paginador">
						<div class="msc_margen_paginador"></div>
						<div class="msc_flechas_paginador">
							<?php
							if ( $filtro['l_pagina'] != 1 )
							{
							?>
								<a id="btPrimero" title="Primer Registro" href="javascript:<?php echo $urlPrimero; ?>">
									<img id="imgPrimero" src="imagenes/barra/b_firstpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btPrimero" href="#">
									<img id="imgPrimero" src="imagenes/barra/bd_firstpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							if ( $filtro['l_pagina_ant'] != 0 )
							{
							?>
								<a id="btAnterior" title="Registro Anterior" href="javascript:<?php echo $urlAnterior; ?>">
									<img id="imgAnterior" src="imagenes/barra/b_prevpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btAnterior" href="#">
									<img id="imgAnterior" src="imagenes/barra/bd_prevpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							?>
						</div>
						<div class="msc_detalle_paginador"><?php echo $filtro['l_pagina'].' de '.$filtro['l_nro_paginas']; ?></div>
						<div class="msc_flechas_paginador">
							<?php
							if ($filtro['l_pagina'] != $filtro['l_nro_paginas'])
							{
							?>
								<a id="btSiguiente" title="Registro Siguiente" href="javascript:<?php echo $urlSiguiente; ?>">
									<img id="imgSiguiente" src="imagenes/barra/b_nextpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btSiguiente" href="#">
									<img id="imgSiguiente" src="imagenes/barra/bd_nextpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}

							if ($filtro['l_pagina'] != $filtro['l_nro_paginas']){
							?>
								<a id="btUltimo" title="Ultimo Registro" href="javascript:<?php echo $urlUltimo; ?>">
									<img id="imgUltimo" src="imagenes/barra/b_lastpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btUltimo" href="#">
									<img id="imgUltimo" src="imagenes/barra/bd_lastpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							?>
						</div>
					</div>
					<!-- FIN DEL PAGINADOR -->
				<?php
				}
				?>

				<div class="msl_borde1">
					<div id="msl_borde2" class="msl_borde2">
						<?php
						if ( !$filtro['l_enviado'] )
						{
						?>
							<!-- FONDO MOSTRADO ANTES DE LA BUSQUEDA  -->
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
						<?php
						}
						else // A
						{
							if ( $listado == '' )
							{
								echo '<br><h1>Sin resultados</h1>';
							}
							else // B
							{
								//Se crea una instancia del modelo
								$modelo = new expedEnComisionModel();

								$inicio = 0;
								$final = count($listado);

								//SE LISTAN LAS FICHAS DE LOS EXPEDIENTES
								for ($exp=$inicio; $exp < $final; $exp++)
								{
									$ficha = &$listado[$exp];

									if ( $ficha['anio'] )
									{
								?>
										<div class="mscpa_gral">
											<div style="height:19px;clear:both;">
												<div class="mscpa_expediente">

													<?php echo $ficha['anio']; ?>
													&nbsp;
													<?php echo $ficha['tipo']; ?>
													&nbsp;
													<?php echo $ficha['numero']; ?>
													&nbsp;
													<?php echo $ficha['cuerpo']; ?>
													&nbsp;
													<?php echo $ficha['alcance']; ?>

												</div>
												<div class="mscpa_titulo mscpa_tit_caratula">Car&aacute;tula:</div>
												<div class="mscpa_caratula"><?php echo $ficha['caratula']; ?></div>

												<!-- PRIMERO EL VALOR Y LUEGO EL TITULO POR LA POSICION RIGHT -->
												<div class="mscpa_fecha_entrada"><?php echo $this->formatearFecha($ficha['fecha_entrada_expe']); ?></div>
												<div class="mscpa_titulo mscpa_tit_fecha">Fecha Expe.:</div>
											</div>
											<div style="height:19px;clear:both;">
												<div class="mscpa_titulo">Tema:&nbsp;</div>
												<div class="mscpa_tema_e_iniciador_categoria_y_autor">
													<select name="temas" class="msc_combo">
													<?php
													//SE OBTIENEN LOS Temas DEL Expediente RESULTANTE
													$temas = $modelo->obtenerTemasFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
													$cant_t = count($temas);
													for ($t=0; $t < $cant_t; $t++)
													{
														$tema = &$temas[$t];
													?>
														<option value="<?php echo $t; ?>"><?php echo $tema['descripcion_tema']; ?></option>
													<?php
													}
													?>
													</select>
												</div>
												<div class="mscpa_titulo">Iniciador:&nbsp;</div>
												<div class="mscpa_tema_e_iniciador_categoria_y_autor">
													<?php
													// 05/04/2018
													// CORREGIDO POR XXXX
													// ***********************
													// Si el iniciador es un Concejal
													if ( $ficha['iniciador_codigo'] == 'CJA' ) {
														$info_autor = $this->modelo_listado->obtenerAutoresFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
														// Se muestra el Autor (el nombre del Concejal)
														echo LibreriaGeneral::reemplazarPorMayusculaAcentuada(strtoupper($info_autor[0]['descripcion_grp']));
													} else
														// sino se muestra la descripción del iniciador
														echo LibreriaGeneral::reemplazarPorMayusculaAcentuada(strtoupper($ficha['iniciador']));
													?>
												</div>
											</div>
											<div style="height:19px;clear:both;">
												<div class="mscpa_titulo">Categor&iacute;a:&nbsp;</div>
												<div class="mscpa_tema_e_iniciador_categoria_y_autor"><?php echo $ficha['categoria']; ?></div>
												<div class="mscpa_titulo">Autor:&nbsp;</div>
												<div class="mscpa_tema_e_iniciador_categoria_y_autor">
													<select name="autores" class="msc_combo">
													<?php
													//SE OBTIENEN LOS Autores DEL Expediente RESULTANTE
													$autores = $modelo->obtenerAutoresFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
													$cant_a = count($autores);
													for ($a=0; $a < $cant_a; $a++)
													{
														$autor = &$autores[$a];
													?>
														<option value="<?php echo $a; ?>"><?php echo $autor['descripcion_grp']; ?></option>
													<?php
													}
													?>
													</select>
												</div>
											</div>
											<?php
											//SE OBTIENEN LOS Proyectos DEL Expediente RESULTANTE (LUEGO MOSTRAR UNA FICHA POR CADA UNO!!!)
											$proyectos = $modelo->obtenerProyectosFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);

											//SE MUESTRAN LOS Proyectos DEL Expediente RESULTANTE
											$cant_p = count($proyectos);
											for ($p=0; $p < $cant_p; $p++)
											{
												$proyecto = &$proyectos[$p];
											?>
												<div style="height:19px;clear:both;">
													<div class="mscpa_titulo">Proyecto:&nbsp;</div>
													<div class="lcg_nrotipo_proyecto"><?php echo $p+1; //CONTADOR DE PROYECTOS ?></div>
													<div class="lcg_descripcion_proyecto"><?php echo $proyecto['descripcion_proyecto']; ?></div>
												</div>
												<div style="height:40px;clear:both;">
													<div class="mscpa_titulo" style="height:37px;">Extracto:&nbsp;</div>
													<div class="mscpa_extracto"><?php echo $proyecto['extracto']; ?></div>
												</div>
											<?php
											}
											// SE OBTIENE EL Estado DEL Expediente RESULTANTE _para_Listados
											$estado = $modelo->obtenerEstadoFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance'], $filtro['l_estado']);
											?>
											<div style="height:16px;margin-top:3px;clear:both;">
												<div class="mscpa_titulo">Estado:&nbsp;</div>
												<div class="mscpa_estado_comision"><?php echo $estado[0]['nombre_estado']; ?></div>
												<div class="lec_fecha_estado"><?php echo " <b>Desde el</b> ".$this->formatearFecha($estado[0]['fecha_estado']); ?></div>
											</div>
											<div style="height:5px;font-size:0;clear:both;"></div>
											<div style="height:19px;clear:both;">
												<div class="cyl_boton_ver_expediente_y_ficha">
													<a href="javascript:verExpediente('<?php echo $ficha['anio']; ?>', '<?php echo $ficha['tipo']; ?>', '<?php echo $ficha['numero']; ?>', '<?php echo $ficha['cuerpo']; ?>', '<?php echo $ficha['alcance']; ?>', '<?php echo $filtro['l_tipo_listado']; ?>');">Ver exped.</a>
												</div>
												<div class="cyl_boton_ver_expediente_y_ficha">
													<a href="javascript:modalGabyFicha('consultas/index.php?controlador=ficha&accion=ver_ficha_modal&anio=<?php echo $ficha['anio']; ?>&tipo=<?php echo $ficha['tipo']; ?>&numero=<?php echo $ficha['numero']; ?>&cuerpo=<?php echo $ficha['cuerpo']; ?>&alcance=<?php echo $ficha['alcance']; ?>');">Ficha</a>
												</div>
											</div>
										</div>
								<?php
									} // FIN DEL if DE VERIFICACIÓN
								} // FIN DEL for
								?>
								<script>
								  var scroller = new Fx.Scroll($('msl_borde2'));
								  scroller.toTop();
								</script>
							<?php
							} // FIN DEL else B
						} // FIN DEL else A
						?>
					</div><!-- FIN DE msl_borde2 -->
				</div><!-- FIN DE msl_borde1 -->
			</form>
	    </div>
	    <script>

		    var menuDrag = new Drag.Move($('contenidoAjaxResultadoListados'), {
			   handle: $('dragger_titulo_listado')
			});

			//CALENDARIO PARA LA FECHA DESDE
			var calDesde = new Zapatec.Calendar.setup({

				inputField:"l_fecha_desde",
				ifFormat:"%d/%m/%Y",
				button:"msl_btffecha_desde",
				showsTime:false
			});
			//CALENDARIO PARA LA FECHA HASTA
			var calHasta = new Zapatec.Calendar.setup({

				inputField:"l_fecha_hasta",
				ifFormat:"%d/%m/%Y",
				button:"msl_btffecha_hasta",
				showsTime:false
			});

			function buscar()
			{
				var mensaje = '';
				var error = false;

				if ( $('l_fecha_desde').value == '' )
				{
					error = true;
					mensaje = "Debe ingresar un rango de Fecha.<br>";
				}
				if ( $('l_fecha_hasta').value == '' )
				{
					error = true;
					mensaje = "Debe ingresar un rango de Fecha.<br>";
				}

				if ( esLaFechaMayor($('l_fecha_desde').value, $('l_fecha_hasta').value) )
				{
					error = true;
					mensaje = "La fecha Desde debe ser menor a la fecha Hasta en el criterio de b"+'\u00fa'+"squeda.";
				}

				if ( error )
				{
					alert(mensaje);
				}
				else
				{
					enviarForm('formListados', 'listados', 'contenidoAjaxResultadoListados');
				}
			}

			function limpiar()
			{
				$('l_fecha_desde').value = '<?php echo $this->calcularFecha(-30); ?>';
				$('l_fecha_hasta').value = '<?php echo date("d/m/Y"); ?>';
			}

			<?php
			if ( $filtro['l_enviado'] )
			{
			?>
				$('btImpresion').addEvent('click', function()
				{
				    if ( $('l_cantidad_TOTAL').value >= 5000 )
				    {
						alert("El resultado de su consulta es muy largo para imprimir.");
				    }
				    else
				    {
						$('btImpresion').setProperty('href', 'listados/index.php?controlador=exped_en_comision&accion=armar_listado_completo&l_tipo_listado='+$('l_tipo_listado').value+'&formato=impresion&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'');

					    $('btImpresion').setProperty('target', '_blank');
					    $('contenedor_btImpresion').setStyle('display', 'none');
					    $('contenedor_btProcesarTexto').setStyle('display', 'none');
				    }
				});

				$('btProcesarTexto').addEvent('click', function()
				{
				    if ( $('l_cantidad_TOTAL').value >= 5000 )
				    {
					  alert("El resultado de su consulta es muy largo para procesar su texto.");
				    }
				    else
				    {
						$('btProcesarTexto').setProperty('href', 'listados/index.php?controlador=exped_en_comision&accion=armar_listado_completo&l_tipo_listado='+$('l_tipo_listado').value+'&formato=texto&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'');

					    $('btProcesarTexto').setProperty('target', '_blank');
					    $('contenedor_btImpresion').setStyle('display', 'none');
					    $('contenedor_btProcesarTexto').setStyle('display', 'none');
				    }
				});
			<?php
			}

			/**/
			// 27/11/2012
			// SI SE VUELVE DE HABER VISTO EL EXPEDIENTE
			if ( $_SESSION['cerrar_modal_LISTADOS'] == 'no' )
			{
				$_SESSION['cerrar_modal_LISTADOS'] = null;
			?>
				refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_enviado=enviado&l_tipo_listado=<?php echo $filtro['l_tipo_listado']; ?>&l_pagina=<?php echo $_SESSION['filtro_LISTADOS']['l_pagina']; ?>', 'contenidoAjaxResultadoListados');
			<?php
			}
			/**/
			?>

			$('l_fecha_desde').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;

						buscar();
					}
				}
			});

			$('l_fecha_hasta').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;

						buscar();
					}
				}
			});

		</script>
	<?php
	}

	public function listar_expurgo($listado = '', $listadoEstados = '', $mensaje = '', $filtro = '')
	{
	    if ( $mensaje != '' )
	    	echo '<div class="abm_mensaje_resultado">'.$mensaje.'</div>';
	?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

	    <div id="precarga_modal" style="display:none"></div>
	    <div id="contenidoAjaxResultadoListados" class="msc_gral msc_texto" style="width:700px;">

			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>

			<form action="listados/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">

				<input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
				<input type="hidden" name="accion" id="accion" value="listar" />
				<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
				<input type="hidden" name="l_enviado" id="l_enviado" value="enviado" />
				<input type="hidden" name="l_tipo_listado" id="l_tipo_listado" value="<?php echo $filtro['l_tipo_listado']; ?>" />
				<input type="hidden" name="l_cantidad_TOTAL" id="l_cantidad_TOTAL" value="<?php echo $filtro['l_cantidad']; ?>" />

				<div id="dragger_titulo_listado" class="msc_titulos degradado">Expedientes para Expurgo</div>
				<div style="height:10px;font-size:0;"></div>
		    	<div style="height:150px;">
					<div style="width:471px;height:150px;float:left;">
						<div class="msl_criterio_fechas_margen"></div>
						<div class="msl_criterio_fechas_nombres">Ingresados desde:</div>
						<?php
						if ( $filtro['l_enviado'] )
						{
							$l_fecha_desde = $this->formatearFecha($_SESSION['filtro_LISTADOS']['l_fecha_desde']);
						}
						else
						{
							// SE CALCULA LA FECHA 10 AÑOS ANTES DEL ACTUAL
							$l_fecha_desde = '01/01/'.( date("Y")-10 );
						}
						?>
						<div class="msl_criterio_calendario">
							<input type="text" id="l_fecha_desde" name="l_fecha_desde" value="<?php echo $l_fecha_desde; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
							<input type="image" id="msl_btffecha_desde" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Desde" width="16" height="16">
						</div>

						<div class="msl_criterio_fechas_nombres">
							<div class="msc_dato_filtro">Hasta:</div>
						</div>
						<div class="msl_criterio_calendario">
							<input type="text" id="l_fecha_hasta" name="l_fecha_hasta" value="<?php echo($_SESSION['filtro_LISTADOS']['l_fecha_hasta']) ? $this->formatearFecha($_SESSION['filtro_LISTADOS']['l_fecha_hasta']) : date("d/m/Y") ; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
							<input type="image" id="msl_btffecha_hasta" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Hasta" width="16" height="16">
						</div>
						<div style="height:3px;font-size:0;clear:both;"></div>
						<div class="mslexp_contenedor_expurgo">

							<div class="msl_criterio_nombres">Con estado:</div>
							<div id="msl_dato_filtro_comision" class="msl_criterio_combos">
								<select id="c_estado" name="l_estado" class="msl_combo" style="width:207px;">
									<option value="0">Seleccione un Estado</option>
									<?php
									$cant_estados = count($listadoEstados);
									for ($e=0; $e < $cant_estados; $e++)
									{
										$estado = &$listadoEstados[$e];
									?>
										<option value="<?php echo $estado['id_codestado']; ?>"><?php echo $estado['codigo_estado'].', '.$estado['nombre_estado']; ?></option>
									<?php
									}
									?>
								</select>
								&nbsp;
								<a id="imagen_zoom_estados" href="javascript:modalGaby('abms/index.php?controlador=codestados&accion=pedirNombreModal&c_solo_habilitado='+$('l_solo_habilitado').value+'');" title="Buscar por Nombre de Estado"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
							</div>
						</div>
						<div class="mslexp_contenedor_expurgo">
							<div class="mslexp_criterio_nombre">S&oacute;lo Habilitados</div>
							<div class="mslexp_criterio_combo" style="text-align: left;">
								<input type="hidden" name="l_solo_habilitado" id="l_solo_habilitado" value="<?php echo($_SESSION['filtro_LISTADOS']['l_solo_habilitado']) ? $_SESSION['filtro_LISTADOS']['l_solo_habilitado'] : 1; ?>" />
								<input type="checkbox" name="habilitado" id="habilitado" onchange="javascript:chequear('l_solo_habilitado');refrescarCombo('listados/index.php?controlador=exped_en_comision&accion=refrescarComboEstados&habilitado='+$('l_solo_habilitado').value+'&estado='+$('c_estado').value+'','msl_dato_filtro_estado');" >
							</div>
						</div>
					</div>
					<div class="msl_criterio_seccion_botones" style="height:150px;">
						<?php
						if ( $filtro['l_enviado'] && $listado != '' )
						{
						?>
							<div id="contenedor_btImpresion" class="msc_boton degradado">
								<a id="btImpresion" href="#" title="Formato para Impresi&oacute;n" >
									<img src="imagenes/barra/print_16x16.gif" width="16" height="16" align="left" />&nbsp;&nbsp;&nbsp;Impresi&oacute;n
								</a>
							</div>

							<div id="contenedor_btProcesarTexto" class="msc_boton degradado">
								<a id="btProcesarTexto" href="#" title="Procesador de Texto" >
									<img src="imagenes/iconos_office/doc.jpg" width="16" height="16" align="left" />&nbsp;&nbsp;&nbsp;Proc.Texto
								</a>
							</div>

							<div id="contenedor_btGenerarCSV" class="msc_boton degradado">
								<a id="btGenerarCSV" href="#" title="Formato Planilla C&aacute;lculo" >
									<img src="imagenes/iconos_office/xls.jpg" width="16" height="16" align="left" />&nbsp;&nbsp;&nbsp;Planilla de Calc.
								</a>
							</div>
						<?php
						}
						?>
						<div class="msc_boton degradado">
							<a href="javascript:buscar();">
								<img src="imagenes/zoom_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Buscar
							</a>
						</div>
						<div class="msc_boton degradado">
							<a href="javascript:limpiar();">
								<img src="imagenes/barra/limpiar.jpeg" width="16" height="16" />&nbsp;&nbsp;&nbsp;Limpiar
							</a>
						</div>
						<div class="msc_boton degradado">
							<a href="javascript:cerrarModalNueva();">
								<img src="imagenes/barra/error_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Cerrar
							</a>
						</div>
					</div>
				</div>

				<?php
				if ( $filtro['l_enviado'] != '' && $listado != '' )
				{
					$urlPrimero = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_estado='+$('c_estado').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=1', 'contenidoAjaxResultadoListados');";
					$urlAnterior = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_estado='+$('c_estado').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=".$filtro['l_pagina_ant']."', 'contenidoAjaxResultadoListados');";
					$urlSiguiente = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_estado='+$('c_estado').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=".$filtro['l_pagina_sgte']."', 'contenidoAjaxResultadoListados');";
					$urlUltimo = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_estado='+$('c_estado').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=".$filtro['l_nro_paginas']."', 'contenidoAjaxResultadoListados');";
				?>
					<!-- PAGINADOR -->
					<div class="msc_paginador">
						<div class="msc_margen_paginador"></div>
						<div class="msc_flechas_paginador">
							<?php
							if ( $filtro['l_pagina'] != 1 )
							{
							?>
								<a id="btPrimero" title="Primer Registro" href="javascript:<?php echo $urlPrimero; ?>">
									<img id="imgPrimero" src="imagenes/barra/b_firstpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btPrimero" href="#">
									<img id="imgPrimero" src="imagenes/barra/bd_firstpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							if ( $filtro['l_pagina_ant'] != 0 )
							{
							?>
								<a id="btAnterior" title="Registro Anterior" href="javascript:<?php echo $urlAnterior; ?>">
									<img id="imgAnterior" src="imagenes/barra/b_prevpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btAnterior" href="#">
									<img id="imgAnterior" src="imagenes/barra/bd_prevpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							?>
						</div>
						<div class="msc_detalle_paginador"><?php echo $filtro['l_pagina'].' de '.$filtro['l_nro_paginas']; ?></div>
						<div class="msc_flechas_paginador">
							<?php
							if ($filtro['l_pagina'] != $filtro['l_nro_paginas'])
							{
							?>
								<a id="btSiguiente" title="Registro Siguiente" href="javascript:<?php echo $urlSiguiente; ?>">
									<img id="imgSiguiente" src="imagenes/barra/b_nextpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btSiguiente" href="#">
									<img id="imgSiguiente" src="imagenes/barra/bd_nextpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}

							if ($filtro['l_pagina'] != $filtro['l_nro_paginas']){
							?>
								<a id="btUltimo" title="Ultimo Registro" href="javascript:<?php echo $urlUltimo; ?>">
									<img id="imgUltimo" src="imagenes/barra/b_lastpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btUltimo" href="#">
									<img id="imgUltimo" src="imagenes/barra/bd_lastpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							?>
						</div>
					</div>
					<!-- FIN DEL PAGINADOR -->
				<?php
				}
				?>

				<div class="msl_borde1">
					<div id="msl_borde2" class="msl_borde2">
						<?php
						if ( !$filtro['l_enviado'] )
						{
						?>
							<!-- FONDO MOSTRADO ANTES DE LA BUSQUEDA  -->
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
						<?php
						}
						else // A
						{
							if ( $listado == '' )
							{
								echo '<br><h1>Sin resultados</h1>';
							}
							else // B
							{
								//Se crea una instancia del modelo
								$modelo = new expedEnComisionModel();

								$inicio = 0;
								$final = count($listado);

								//SE LISTAN LAS FICHAS DE LOS EXPEDIENTES
								for ($exp=$inicio; $exp < $final; $exp++)
								{
									$ficha = &$listado[$exp];

									if ( $ficha['anio'] )
									{
								?>
										<div class="mscpa_gral">
											<div style="height:19px;clear:both;">
												<div class="mscpa_expediente">

													<?php echo $ficha['anio']; ?>
													&nbsp;
													<?php echo $ficha['tipo']; ?>
													&nbsp;
													<?php echo $ficha['numero']; ?>
													&nbsp;
													<?php echo $ficha['cuerpo']; ?>
													&nbsp;
													<?php echo $ficha['alcance']; ?>

												</div>
												<div class="mscpa_titulo mscpa_tit_caratula">Car&aacute;tula:</div>
												<div class="mscpa_caratula"><?php echo $ficha['caratula']; ?></div>

												<!-- PRIMERO EL VALOR Y LUEGO EL TITULO POR LA POSICION RIGHT -->
												<div class="mscpa_fecha_entrada"><?php echo $this->formatearFecha($ficha['fecha_entrada_expe']); ?></div>
												<div class="mscpa_titulo mscpa_tit_fecha">Fecha Expe.:</div>
											</div>
											<div style="height:19px;clear:both;">
												<div class="mscpa_titulo">Tema:&nbsp;</div>
												<div class="mscpa_tema_e_iniciador_categoria_y_autor">
													<select name="temas" class="msc_combo">
													<?php
													//SE OBTIENEN LOS Temas DEL Expediente RESULTANTE
													$temas = $modelo->obtenerTemasFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
													$cant_t = count($temas);
													for ($t=0; $t < $cant_t; $t++)
													{
														$tema = &$temas[$t];
													?>
														<option value="<?php echo $t; ?>"><?php echo $tema['descripcion_tema']; ?></option>
													<?php
													}
													?>
													</select>
												</div>
												<div class="mscpa_titulo">Iniciador:&nbsp;</div>
												<div class="mscpa_tema_e_iniciador_categoria_y_autor"><?php echo $ficha['iniciador']; ?></div>
											</div>
											<div style="height:19px;clear:both;">
												<div class="mscpa_titulo">Categor&iacute;a:&nbsp;</div>
												<div class="mscpa_tema_e_iniciador_categoria_y_autor"><?php echo $ficha['categoria']; ?></div>
												<div class="mscpa_titulo">Autor:&nbsp;</div>
												<div class="mscpa_tema_e_iniciador_categoria_y_autor">
													<select name="autores" class="msc_combo">
													<?php
													//SE OBTIENEN LOS Autores DEL Expediente RESULTANTE
													$autores = $modelo->obtenerAutoresFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
													$cant_a = count($autores);
													for ($a=0; $a < $cant_a; $a++)
													{
														$autor = &$autores[$a];
													?>
														<option value="<?php echo $a; ?>"><?php echo $autor['descripcion_grp']; ?></option>
													<?php
													}
													?>
													</select>
												</div>
											</div>
											<?php
											//SE OBTIENEN LOS Proyectos DEL Expediente RESULTANTE (LUEGO MOSTRAR UNA FICHA POR CADA UNO!!!)
											$proyectos = $modelo->obtenerProyectosFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);

											//SE MUESTRAN LOS Proyectos DEL Expediente RESULTANTE
											$cant_p = count($proyectos);
											for ($p=0; $p < $cant_p; $p++)
											{
												$proyecto = &$proyectos[$p];
											?>
												<div style="height:19px;clear:both;">
													<div class="mscpa_titulo">Proyecto:&nbsp;</div>
													<div class="lcg_nrotipo_proyecto"><?php echo $p+1; //CONTADOR DE PROYECTOS ?></div>
													<div class="lcg_descripcion_proyecto"><?php echo $proyecto['descripcion_proyecto']; ?></div>
												</div>
												<div style="height:40px;clear:both;">
													<div class="mscpa_titulo" style="height:37px;">Extracto:&nbsp;</div>
													<div class="mscpa_extracto"><?php echo $proyecto['extracto']; ?></div>
												</div>
											<?php
											}
											// SE OBTIENE EL Estado DEL Expediente RESULTANTE _para_Listados
											$estado = $modelo->obtenerEstadoFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance'], $filtro['l_estado']);
											?>
											<div style="height:16px;margin-top:3px;clear:both;">
												<div class="mscpa_titulo">Estado:&nbsp;</div>
												<div class="mscpa_estado_comision"><?php echo $estado[0]['nombre_estado']; ?></div>
												<div class="lec_fecha_estado"><?php echo " <b>Desde el</b> ".$this->formatearFecha($estado[0]['fecha_estado']); ?></div>
											</div>
											<div style="height:5px;font-size:0;clear:both;"></div>
											<div style="height:19px;clear:both;">
												<div class="cyl_boton_ver_expediente_y_ficha">
													<a href="javascript:verExpediente('<?php echo $ficha['anio']; ?>', '<?php echo $ficha['tipo']; ?>', '<?php echo $ficha['numero']; ?>', '<?php echo $ficha['cuerpo']; ?>', '<?php echo $ficha['alcance']; ?>', '<?php echo $filtro['l_tipo_listado']; ?>');">Ver exped.</a>
												</div>
												<div class="cyl_boton_ver_expediente_y_ficha">
													<a href="javascript:modalGabyFicha('consultas/index.php?controlador=ficha&accion=ver_ficha_modal&anio=<?php echo $ficha['anio']; ?>&tipo=<?php echo $ficha['tipo']; ?>&numero=<?php echo $ficha['numero']; ?>&cuerpo=<?php echo $ficha['cuerpo']; ?>&alcance=<?php echo $ficha['alcance']; ?>');">Ficha</a>
												</div>
											</div>
										</div>
								<?php
									} // FIN DEL if DE VERIFICACIÓN
								} // FIN DEL for
								?>
								<script>
								  var scroller = new Fx.Scroll($('msl_borde2'));
								  scroller.toTop();
								</script>
							<?php
							} // FIN DEL else B
						} // FIN DEL else A
						?>
					</div><!-- FIN DE msl_borde2 -->
				</div><!-- FIN DE msl_borde1 -->
			</form>
	    </div>
	    <script>

		    var menuDrag = new Drag.Move($('contenidoAjaxResultadoListados'), {
			   handle: $('dragger_titulo_listado')
			});

			//CALENDARIO PARA LA FECHA DESDE
			var calDesde = new Zapatec.Calendar.setup({

				inputField:"l_fecha_desde",
				ifFormat:"%d/%m/%Y",
				button:"msl_btffecha_desde",
				showsTime:false
			});
			//CALENDARIO PARA LA FECHA HASTA
			var calHasta = new Zapatec.Calendar.setup({

				inputField:"l_fecha_hasta",
				ifFormat:"%d/%m/%Y",
				button:"msl_btffecha_hasta",
				showsTime:false
			});

			function buscar()
			{
				var mensaje = '';
				var error = false;

				if ( $('l_fecha_desde').value == '' )
				{
					error = true;
					mensaje = "Debe ingresar un rango de Fecha.<br>";
				}
				if ( $('l_fecha_hasta').value == '' )
				{
					error = true;
					mensaje = "Debe ingresar un rango de Fecha.<br>";
				}
				if ($('c_estado').value == 0)
				{
					error = true;
					mensaje = "Debe ingresar un Estado.";
				}

				if ( esLaFechaMayor($('l_fecha_desde').value, $('l_fecha_hasta').value) )
				{
					error = true;
					mensaje = "La fecha Desde debe ser menor a la fecha Hasta en el criterio de b"+'\u00fa'+"squeda.";
				}

				if ( error )
				{
					alert(mensaje);
				}
				else
				{
					enviarForm('formListados', 'listados', 'contenidoAjaxResultadoListados');
				}
			}

			function limpiar()
			{
				$('l_fecha_desde').value = '<?php echo '01/01/'.( date("Y")-10 ); ?>';
				$('l_fecha_hasta').value = '<?php echo date("d/m/Y"); ?>';
				$('c_estado').value = 0;
			}

			<?php
			if ( $_SESSION['filtro_LISTADOS']['l_solo_habilitado'] == '' )
			{
			?>
				$('habilitado').checked = true;
				$('l_solo_habilitado').value = 1;
			<?php
			}
			elseif ( $_SESSION['filtro_LISTADOS']['l_solo_habilitado'] == 0 )
			{
			?>
				$('habilitado').checked = false;
				$('l_solo_habilitado').value = 0;
			<?php
			}
			else
			{
			?>
				$('habilitado').checked = true;
				$('l_solo_habilitado').value = 1;
			<?php
			}
			?>

			if ( $('c_estado') )
			{
				$('c_estado').value = <?php echo ($_SESSION['filtro_LISTADOS']['l_estado']) ? "'".$_SESSION['filtro_LISTADOS']['l_estado']."'" : 0 ; ?>;
			}

			<?php
			if ( $filtro['l_enviado'] )
			{
			?>
				$('btImpresion').addEvent('click', function()
				{
				    if ( $('l_cantidad_TOTAL').value >= 5000 )
				    {
						alert("El resultado de su consulta es muy largo para imprimir.");
				    }
				    else
				    {
						$('btImpresion').setProperty('href', 'listados/index.php?controlador=exped_en_comision&accion=armar_listado_completo&l_tipo_listado='+$('l_tipo_listado').value+'&formato=impresion&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_estado='+$('c_estado').value+'');

					    $('btImpresion').setProperty('target', '_blank');
					    $('contenedor_btImpresion').setStyle('display', 'none');
					    $('contenedor_btProcesarTexto').setStyle('display', 'none');
					    $('contenedor_btGenerarCSV').setStyle('display', 'none');
				    }
				});

				$('btProcesarTexto').addEvent('click', function()
				{
				    if ( $('l_cantidad_TOTAL').value >= 5000 )
				    {
						alert("El resultado de su consulta es muy largo para procesar su texto.");
				    }
				    else
				    {
						$('btProcesarTexto').setProperty('href', 'listados/index.php?controlador=exped_en_comision&accion=armar_listado_completo&l_tipo_listado='+$('l_tipo_listado').value+'&formato=texto&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_estado='+$('c_estado').value+'');

					    $('btProcesarTexto').setProperty('target', '_blank');
					    $('contenedor_btImpresion').setStyle('display', 'none');
					    $('contenedor_btProcesarTexto').setStyle('display', 'none');
					    $('contenedor_btGenerarCSV').setStyle('display', 'none');
				    }
				});

				$('btGenerarCSV').addEvent('click', function()
				{
				    if ( $('l_cantidad_TOTAL').value >= 5000 )
				    {
						alert("El resultado de su consulta es muy largo para procesar su texto.");
				    }
				    else
				    {
						$('btGenerarCSV').setProperty('href', 'listados/index.php?controlador=exped_en_comision&accion=armar_listado_completo&l_tipo_listado='+$('l_tipo_listado').value+'&formato=csv&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_estado='+$('c_estado').value+'');

					    $('btGenerarCSV').setProperty('target', '_blank');
					    $('contenedor_btImpresion').setStyle('display', 'none');
					    $('contenedor_btProcesarTexto').setStyle('display', 'none');
					    $('contenedor_btGenerarCSV').setStyle('display', 'none');
				    }
				});
			<?php
			}

			/**/
			// 27/11/2012
			// SI SE VUELVE DE HABER VISTO EL EXPEDIENTE
			if ( $_SESSION['cerrar_modal_LISTADOS'] == 'no' )
			{
				$_SESSION['cerrar_modal_LISTADOS'] = null;
			?>
				refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_estado='+$('c_estado').value+'&l_enviado=enviado&l_tipo_listado=<?php echo $filtro['l_tipo_listado']; ?>&l_pagina=<?php echo $_SESSION['filtro_LISTADOS']['l_pagina']; ?>', 'contenidoAjaxResultadoListados');
			<?php
			}
			/**/
			?>

			$('l_fecha_desde').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;

						buscar();
					}
				}
			});

			$('l_fecha_hasta').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;

						buscar();
					}
				}
			});

		</script>
	<?php
	}

	public function listar_expedientes_en_prestamo($listado = '', $listadoEstados = '', $mensaje = '', $filtro = '')
	{
	    if ( $mensaje != '' ){ echo '<div class="abm_mensaje_resultado">'.$mensaje.'</div>'; }
	?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

	    <div id="precarga_modal" style="display:none"></div>
	    <div id="contenidoAjaxResultadoListados" class="msc_gral msc_texto" style="width:700px;">

			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>

			<form action="listados/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">

				<input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" />
				<input type="hidden" name="accion" id="accion" value="listar" />
				<input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>" />
				<input type="hidden" name="l_enviado" id="l_enviado" value="enviado" />
				<input type="hidden" name="l_tipo_listado" id="l_tipo_listado" value="<?php echo $filtro['l_tipo_listado']; ?>" />
				<input type="hidden" name="l_cantidad_TOTAL" id="l_cantidad_TOTAL" value="<?php echo $filtro['l_cantidad']; ?>" />

				<div id="dragger_titulo_listado" class="msc_titulos degradado">Expedientes en Pr&eacute;stamo</div>
				<div style="height:10px;font-size:0;"></div>
		    	<div style="height:135px;">
					<div style="width:471px;height:135px;float:left;">
						<div class="msl_criterio_fechas_margen"></div>
						<div class="msl_criterio_fechas_nombres">
							<div class="msc_dato_filtro">Ingresados desde:</div>
						</div>
						<?php
						if ( $filtro['l_enviado'] )
						{
							$l_fecha_desde = $this->formatearFecha($_SESSION['filtro_LISTADOS']['l_fecha_desde']);
						}
						else
						{
							// SE CALCULA LA FECHA 10 AÑOS ANTES DEL ACTUAL
							$l_fecha_desde = '01/01/1983';
						}
						?>
						<div class="msl_criterio_calendario">
							<input type="text" id="l_fecha_desde" name="l_fecha_desde" value="<?php echo $l_fecha_desde; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
							<input type="image" id="msl_btffecha_desde" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Desde" width="16" height="16">
						</div>

						<div class="msl_criterio_fechas_nombres">
							<div class="msc_dato_filtro">Hasta:</div>
						</div>
						<div class="msl_criterio_calendario">
							<input type="text" id="l_fecha_hasta" name="l_fecha_hasta" value="<?php echo($_SESSION['filtro_LISTADOS']['l_fecha_hasta']) ? $this->formatearFecha($_SESSION['filtro_LISTADOS']['l_fecha_hasta']) : date("d/m/Y") ; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
							<input type="image" id="msl_btffecha_hasta" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Hasta" width="16" height="16">
						</div>
						<div style="height:3px;font-size:0;clear:both;"></div>
						<div class="mslexp_contenedor_expurgo">

							<div class="msl_criterio_nombres">Con estado:</div>
							<div id="msl_dato_filtro_comision" class="msl_criterio_combos">
								<select id="c_estado" name="l_estado" class="msl_combo" style="width:207px;">
									<option value="0">Seleccione un Estado</option>
									<?php
									$cant_estados = count($listadoEstados);
									for ($e=0; $e < $cant_estados; $e++)
									{
										$estado = &$listadoEstados[$e];
									?>
										<option value="<?php echo $estado['id_codestado']; ?>"><?php echo $estado['codigo_estado'].', '.$estado['nombre_estado']; ?></option>
									<?php
									}
									?>
								</select>
								&nbsp;
								<a id="imagen_zoom_estados" href="javascript:modalGaby('abms/index.php?controlador=codestados&accion=pedirNombreModal&c_solo_habilitado='+$('l_solo_habilitado').value+'');" title="Buscar por Nombre de Estado"><img src="imagenes/zoom_16x16.gif" width="16" height="16" /></a>
							</div>
						</div>
						<div class="mslexp_contenedor_expurgo">
							<div class="mslexp_criterio_nombre">Con palabras:</div>
							<div class="mslexp_criterio_combo" style="text-align:left;">
								<input type="text" id="l_observacion_estado" name="l_observacion_estado" value="<?php echo $_SESSION['filtro_LISTADOS']['l_observacion_estado']; ?>" style="width:257px;" />
							</div>
						</div>
						<div class="mslexp_contenedor_expurgo">
							<div class="mslexp_criterio_nombre">S&oacute;lo Habilitados :</div>
							<div class="mslexp_criterio_combo" style="text-align:left;">
								<input type="hidden" name="l_solo_habilitado" id="l_solo_habilitado" value="<?php echo($_SESSION['filtro_LISTADOS']['l_solo_habilitado']) ? $_SESSION['filtro_LISTADOS']['l_solo_habilitado'] : 1; ?>" />
								<input type="checkbox" name="habilitado" id="habilitado" onchange="javascript:chequear('l_solo_habilitado');refrescarCombo('listados/index.php?controlador=exped_en_comision&accion=refrescarComboEstados&habilitado='+$('l_solo_habilitado').value+'&estado='+$('c_estado').value+'','msl_dato_filtro_estado');" >
							</div>
						</div>
					</div>
					<div class="msl_criterio_seccion_botones">
						<?php
						if ( $filtro['l_enviado'] && $listado != '' )
						{
						?>
							<div id="contenedor_btImpresion" class="msc_boton degradado">
								<a id="btImpresion" href="#" title="Formato para Impresi&oacute;n" >
									<img src="imagenes/barra/print_16x16.gif" width="16" height="16" align="left" />&nbsp;&nbsp;&nbsp;Impresi&oacute;n
								</a>
							</div>

							<div id="contenedor_btProcesarTexto" class="msc_boton degradado">
								<a id="btProcesarTexto" href="#" title="Procesador de Texto" >
									<img src="imagenes/iconos_office/doc.jpg" width="16" height="16" align="left" />&nbsp;&nbsp;&nbsp;Proc.Texto
								</a>
							</div>
						<?php
						}
						?>
						<div class="msc_boton degradado">
							<a href="javascript:buscar();">
								<img src="imagenes/zoom_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Buscar
							</a>
						</div>
						<div class="msc_boton degradado">
							<a href="javascript:limpiar();">
								<img src="imagenes/barra/limpiar.jpeg" width="16" height="16" />&nbsp;&nbsp;&nbsp;Limpiar
							</a>
						</div>
						<div class="msc_boton degradado">
							<a href="javascript:cerrarModalNueva();">
								<img src="imagenes/barra/error_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Cerrar
							</a>
						</div>
					</div>
				</div>

				<?php
				if ( $filtro['l_enviado'] != '' && $listado != '' )
				{
					$urlPrimero = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_estado='+$('c_estado').value+'&l_observacion_estado='+$('l_observacion_estado').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=1', 'contenidoAjaxResultadoListados');";
					$urlAnterior = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_estado='+$('c_estado').value+'&l_observacion_estado='+$('l_observacion_estado').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=".$filtro['l_pagina_ant']."', 'contenidoAjaxResultadoListados');";
					$urlSiguiente = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_estado='+$('c_estado').value+'&l_observacion_estado='+$('l_observacion_estado').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=".$filtro['l_pagina_sgte']."', 'contenidoAjaxResultadoListados');";
					$urlUltimo = "refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_estado='+$('c_estado').value+'&l_observacion_estado='+$('l_observacion_estado').value+'&l_enviado=enviado&l_tipo_listado=".$filtro['l_tipo_listado']."&l_pagina=".$filtro['l_nro_paginas']."', 'contenidoAjaxResultadoListados');";
				?>
					<!-- PAGINADOR -->
					<div class="msc_paginador">
						<div class="msc_margen_paginador"></div>
						<div class="msc_flechas_paginador">
							<?php
							if ( $filtro['l_pagina'] != 1 )
							{
							?>
								<a id="btPrimero" title="Primer Registro" href="javascript:<?php echo $urlPrimero; ?>">
									<img id="imgPrimero" src="imagenes/barra/b_firstpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btPrimero" href="#">
									<img id="imgPrimero" src="imagenes/barra/bd_firstpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							if ( $filtro['l_pagina_ant'] != 0 )
							{
							?>
								<a id="btAnterior" title="Registro Anterior" href="javascript:<?php echo $urlAnterior; ?>">
									<img id="imgAnterior" src="imagenes/barra/b_prevpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btAnterior" href="#">
									<img id="imgAnterior" src="imagenes/barra/bd_prevpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							?>
						</div>
						<div class="msc_detalle_paginador"><?php echo $filtro['l_pagina'].' de '.$filtro['l_nro_paginas']; ?></div>
						<div class="msc_flechas_paginador">
							<?php
							if ($filtro['l_pagina'] != $filtro['l_nro_paginas'])
							{
							?>
								<a id="btSiguiente" title="Registro Siguiente" href="javascript:<?php echo $urlSiguiente; ?>">
									<img id="imgSiguiente" src="imagenes/barra/b_nextpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btSiguiente" href="#">
									<img id="imgSiguiente" src="imagenes/barra/bd_nextpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}

							if ($filtro['l_pagina'] != $filtro['l_nro_paginas']){
							?>
								<a id="btUltimo" title="Ultimo Registro" href="javascript:<?php echo $urlUltimo; ?>">
									<img id="imgUltimo" src="imagenes/barra/b_lastpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							else
							{
							?>
								<a id="btUltimo" href="#">
									<img id="imgUltimo" src="imagenes/barra/bd_lastpage.png" width="14" height="14" align="center" />
								</a>
							<?php
							}
							?>
						</div>
					</div>
					<!-- FIN DEL PAGINADOR -->
				<?php
				}
				?>

				<div class="msl_borde1">
					<div id="msl_borde2" class="msl_borde2">
						<?php
						if ( !$filtro['l_enviado'] )
						{
						?>
							<!-- FONDO MOSTRADO ANTES DE LA BUSQUEDA  -->
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
							<div class="msc_fondo_item">Item en Listados</div>
						<?php
						}
						else // A
						{
							if ( $listado == '' )
							{
								echo '<br><h1>Sin resultados</h1>';
							}
							else // B
							{
								//Se crea una instancia del modelo
								$modelo = new expedEnComisionModel();

								$cantidad_listado = count($listado);
								//SE LISTAN LAS FICHAS DE LOS EXPEDIENTES
								for ($exp=0; $exp < $cantidad_listado; $exp++)
								{
									$ficha = &$listado[$exp];

									if ( $ficha['anio'] )
									{
								?>
										<div class="mscpa_gral">
											<div style="height:19px;clear:both;">
												<div class="mscpa_expediente">

													<?php echo $ficha['anio']; ?>
													&nbsp;
													<?php echo $ficha['tipo']; ?>
													&nbsp;
													<?php echo $ficha['numero']; ?>
													&nbsp;
													<?php echo $ficha['cuerpo']; ?>
													&nbsp;
													<?php echo $ficha['alcance']; ?>

												</div>
												<div class="mscpa_titulo mscpa_tit_caratula">Car&aacute;tula:</div>
												<div class="mscpa_caratula"><?php echo $ficha['caratula']; ?></div>

												<!-- PRIMERO EL VALOR Y LUEGO EL TITULO POR LA POSICION RIGHT -->
												<div class="mscpa_fecha_entrada"><?php echo $this->formatearFecha($ficha['fecha_entrada_expe']); ?></div>
												<div class="mscpa_titulo mscpa_tit_fecha">Fecha Expe.:</div>
											</div>
											<div style="height:19px;clear:both;">
												<div class="mscpa_titulo mscpa_titulo_prestamo">Tema:&nbsp;</div>
												<div class="mscpa_tema_e_iniciador_categoria_y_autor">
													<select name="temas" class="msc_combo">
														<?php
														//SE OBTIENEN LOS Temas DEL Expediente RESULTANTE
														$temas = $modelo->obtenerTemasFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
														$cant_t = count($temas);
														for ($t=0; $t < $cant_t; $t++)
														{
															$tema = &$temas[$t];
														?>
															<option value="<?php echo $t; ?>"><?php echo $tema['descripcion_tema']; ?></option>
														<?php
														}
														?>
													</select>
												</div>
												<div class="mscpa_titulo mscpa_titulo_prestamo">Iniciador:&nbsp;</div>
												<div class="mscpa_tema_e_iniciador_categoria_y_autor"><?php echo $ficha['iniciador']; ?></div>
											</div>
											<div style="height:19px;clear:both;">
												<div class="mscpa_titulo mscpa_titulo_prestamo">Categor&iacute;a:&nbsp;</div>
												<div class="mscpa_tema_e_iniciador_categoria_y_autor"><?php echo $ficha['categoria']; ?></div>
												<div class="mscpa_titulo mscpa_titulo_prestamo">Autor:&nbsp;</div>
												<div class="mscpa_tema_e_iniciador_categoria_y_autor">
													<select name="autores" class="msc_combo">
														<?php
														//SE OBTIENEN LOS Autores DEL Expediente RESULTANTE
														$autores = $modelo->obtenerAutoresFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);
														$cant_a = count($autores);
														for ($a=0; $a < $cant_a; $a++)
														{
															$autor = &$autores[$a];
														?>
															<option value="<?php echo $a; ?>"><?php echo $autor['descripcion_grp']; ?></option>
														<?php
														}
														?>
													</select>
												</div>
											</div>
											<?php
											//SE OBTIENEN LOS Proyectos DEL Expediente RESULTANTE (LUEGO MOSTRAR UNA FICHA POR CADA UNO!!!)
											$proyectos = $modelo->obtenerProyectosFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance']);

											//SE MUESTRAN LOS Proyectos DEL Expediente RESULTANTE
											$cant_p = count($proyectos);
											for ($p=0; $p < $cant_p; $p++)
											{
												$proyecto = &$proyectos[$p];
											?>
												<div style="height:19px;clear:both;">
													<div class="mscpa_titulo mscpa_titulo_prestamo">Proyecto:&nbsp;</div>
													<div class="lcg_nrotipo_proyecto"><?php echo $p+1; //CONTADOR DE PROYECTOS ?></div>
													<div class="lcg_descripcion_proyecto"><?php echo $proyecto['descripcion_proyecto']; ?></div>
												</div>
												<div style="height:40px;clear:both;">
													<div class="mscpa_titulo mscpa_titulo_prestamo" style="height:37px;">Extracto:&nbsp;</div>
													<div class="mscpa_extracto"><?php echo $proyecto['extracto']; ?></div>
												</div>
											<?php
											}
											// SE OBTIENE EL Estado DEL Expediente RESULTANTE _para_Listados
											$estado = $modelo->obtenerEstadoFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance'], $filtro['l_estado']);
											?>
											<div style="height:16px;margin-top:3px;clear:both;">
												<div class="mscpa_titulo mscpa_titulo_prestamo">Estado:&nbsp;</div>
												<div class="mscpa_estado_comision"><?php echo $estado[0]['nombre_estado']; ?></div>
												<div class="lec_fecha_estado"><?php echo " <b>Desde el</b> ".$this->formatearFecha($estado[0]['fecha_estado']); ?></div>
											</div>
											<div style="height:19px;clear:both;">
												<div class="mscpa_titulo mscpa_titulo_prestamo">Observaci&oacute;n:&nbsp;</div>
												<div class="mscpa_estado_comision"><?php echo $estado[0]['observaciones_estado']; ?></div>
											</div>
											<div style="height:5px;font-size:0;clear:both;"></div>
											<div style="height:19px;clear:both;">
												<div class="cyl_boton_ver_expediente_y_ficha">
													<a href="javascript:verExpediente('<?php echo $ficha['anio']; ?>', '<?php echo $ficha['tipo']; ?>', '<?php echo $ficha['numero']; ?>', '<?php echo $ficha['cuerpo']; ?>', '<?php echo $ficha['alcance']; ?>', '<?php echo $filtro['l_tipo_listado']; ?>');">Ver exped.</a>
												</div>
												<div class="cyl_boton_ver_expediente_y_ficha">
													<a href="javascript:modalGabyFicha('consultas/index.php?controlador=ficha&accion=ver_ficha_modal&anio=<?php echo $ficha['anio']; ?>&tipo=<?php echo $ficha['tipo']; ?>&numero=<?php echo $ficha['numero']; ?>&cuerpo=<?php echo $ficha['cuerpo']; ?>&alcance=<?php echo $ficha['alcance']; ?>');">Ficha</a>
												</div>
											</div>
										</div>
								<?php
									} // FIN DEL if DE VERIFICACIÓN
								} // FIN DEL for
								?>
								<script>
								  var scroller = new Fx.Scroll($('msl_borde2'));
								  scroller.toTop();
								</script>
							<?php
							} // FIN DEL else B
						} // FIN DEL else A
						?>
					</div><!-- FIN DE msl_borde2 -->
				</div><!-- FIN DE msl_borde1 -->
			</form>
	    </div>
	    <script>

		    var menuDrag = new Drag.Move($('contenidoAjaxResultadoListados'), {
			   handle: $('dragger_titulo_listado')
			});

			//CALENDARIO PARA LA FECHA DESDE
			var calDesde = new Zapatec.Calendar.setup({

				inputField:"l_fecha_desde",
				ifFormat:"%d/%m/%Y",
				button:"msl_btffecha_desde",
				showsTime:false
			});
			//CALENDARIO PARA LA FECHA HASTA
			var calHasta = new Zapatec.Calendar.setup({

				inputField:"l_fecha_hasta",
				ifFormat:"%d/%m/%Y",
				button:"msl_btffecha_hasta",
				showsTime:false
			});

			function buscar()
			{
				var mensaje = '';
				var error = false;

				if ( $('l_fecha_desde').value == '' )
				{
					error = true;
					mensaje = "Debe ingresar un rango de Fecha.<br>";
				}
				if ( $('l_fecha_hasta').value == '' )
				{
					error = true;
					mensaje = "Debe ingresar un rango de Fecha.<br>";
				}
				if ($('c_estado').value == 0)
				{
					error = true;
					mensaje = "Debe ingresar un Estado.";
				}

				if ( esLaFechaMayor($('l_fecha_desde').value, $('l_fecha_hasta').value) )
				{
					error = true;
					mensaje = "La fecha Desde debe ser menor a la fecha Hasta en el criterio de b"+'\u00fa'+"squeda.";
				}

				if ( error )
				{
					alert(mensaje);
				}
				else
				{
					enviarForm('formListados', 'listados', 'contenidoAjaxResultadoListados');
				}
			}

			function limpiar()
			{
				$('l_fecha_desde').value = '01/01/1983';
				$('l_fecha_hasta').value = '<?php echo date("d/m/Y"); ?>';
				$('c_estado').value = 0;
				$('l_observacion_estado').value = '';
			}

			<?php
			if ( $_SESSION['filtro_LISTADOS']['l_solo_habilitado'] == '' )
			{
			?>
				$('habilitado').checked = true;
				$('l_solo_habilitado').value = 1;
			<?php
			}
			elseif ( $_SESSION['filtro_LISTADOS']['l_solo_habilitado'] == 0 )
			{
			?>
				$('habilitado').checked = false;
				$('l_solo_habilitado').value = 0;
			<?php
			}
			else
			{
			?>
				$('habilitado').checked = true;
				$('l_solo_habilitado').value = 1;
			<?php
			}
			?>

			if ( $('c_estado') )
			{
				$('c_estado').value = <?php echo ($_SESSION['filtro_LISTADOS']['l_estado']) ? "'".$_SESSION['filtro_LISTADOS']['l_estado']."'" : 12 ; ?>;
			}

			<?php
			if ( $filtro['l_enviado'] )
			{
			?>
				$('btImpresion').addEvent('click', function()
				{
				    if ( $('l_cantidad_TOTAL').value >= 5000 )
				    {
						alert("El resultado de su consulta es muy largo para imprimir.");
				    }
				    else
				    {
						$('btImpresion').setProperty('href', 'listados/index.php?controlador=exped_en_comision&accion=armar_listado_completo&l_tipo_listado='+$('l_tipo_listado').value+'&formato=impresion&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_estado='+$('c_estado').value+'&l_observacion_estado='+$('l_observacion_estado').value+'');
						$('btImpresion').setProperty('target', '_blank');
					    $('contenedor_btImpresion').setStyle('display', 'none');
					    $('contenedor_btProcesarTexto').setStyle('display', 'none');
				    }
				});

				$('btProcesarTexto').addEvent('click', function()
				{
				    if ( $('l_cantidad_TOTAL').value >= 5000 )
				    {
						alert("El resultado de su consulta es muy largo para procesar su texto.");
				    }
				    else
				    {
						$('btProcesarTexto').setProperty('href', 'listados/index.php?controlador=exped_en_comision&accion=armar_listado_completo&l_tipo_listado='+$('l_tipo_listado').value+'&formato=texto&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_estado='+$('c_estado').value+'&l_observacion_estado='+$('l_observacion_estado').value+'');
						$('btProcesarTexto').setProperty('target', '_blank');
					    $('contenedor_btImpresion').setStyle('display', 'none');
					    $('contenedor_btProcesarTexto').setStyle('display', 'none');
				    }
				});
			<?php
			}

			/**/
			// 27/11/2012
			// SI SE VUELVE DE HABER VISTO EL EXPEDIENTE
			if ( $_SESSION['cerrar_modal_LISTADOS'] == 'no' )
			{
				$_SESSION['cerrar_modal_LISTADOS'] = null;
			?>
				refrescar('listados/index.php?controlador=exped_en_comision&accion=listar&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'&l_estado='+$('c_estado').value+'&l_observacion_estado='+$('l_observacion_estado').value+'&l_enviado=enviado&l_tipo_listado=<?php echo $filtro['l_tipo_listado']; ?>&l_pagina=<?php echo $_SESSION['filtro_LISTADOS']['l_pagina']; ?>', 'contenidoAjaxResultadoListados');
			<?php
			}
			/**/
			?>

			$('l_fecha_desde').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;

						buscar();
					}
				}
			});

			$('l_fecha_hasta').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;

						buscar();
					}
				}
			});

			$('l_observacion_estado').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;

						if( $('l_observacion_estado').value != '' )
						{
							buscar();
						}
					}
				}
			});

		</script>
	<?php
	}
/*************************************************************************************************************************
      SE GENERA EL REPORTE DE Expedientes en Préstamo PARA SU IMPRESION
*************************************************************************************************************************/
    public function generar_formato_de_impresion_exped_en_prestamo($listado_para_pdf = '', $filtro_para_pdf = '')
	{
		header("Content-Type: text/html; charset=UTF-8");

		$modelo = new expedEnComisionModel();
		?>
		<style type="text/css">
			.imp_titulo_general{
				padding:10px 0 0 150px;
				font-family: Arial;
				font-size: 18px;
				font-weight:700;
				text-decoration:underline;
				text-align:left;
				border-left:2px solid #000;
				border-right:2px solid #000;
			}
			.imp_subtitulo_general{
				padding:10px 0 20px 10px;
				font-size:14px;
				font-weight:700;
				text-align:left;
				border-left:2px solid #000;
				border-right:2px solid #000;
			}
			/************************************************************/
			.imp_titulos_de_clave{
				height:25px;
				border:2px solid #000;
			}
			.imp_titulos_de_clave_izq{
				width: 395px;
				height: 20px;
				padding: 5px 0 0 5px;
				float: left;
				font-family: Arial;
				font-size: 12px;
				font-weight: 700;
			}
			.imp_titulos_de_clave_der {
				width: 245px;
				height: 20px;
				padding: 5px 5px 0 0;
				float: right;
				text-align: right;
				font-family: Arial;
				font-size: 12px;
				font-weight: 700;
			}
			/************************************************************/
			.imp_ficha_titulos_de_clave{
				height:25px;
				border-left:2px solid #000;
				border-right:2px solid #000;
				clear:both;
			}
			.imp_ficha_titulos_de_clave_izq{
				height: 20px;
				padding: 5px;
				float: left;
				font-family: Arial;
				font-size: 12px;
				font-weight: 700;
			}
			.imp_ficha_titulos_de_clave_der {
				height: 20px;
				padding: 5px 5px 0 0;
				float: right;
				text-align: right;
				font-family: Arial;
				font-size: 12px;
				font-weight: 700;
			}
			.imp_ficha_extracto{
				padding:5px 5px 10px 5px;
				text-align:left;
				font-family: Arial;
				font-size:12px;
				border-left:2px solid #000;
				border-right:2px solid #000;
			}
			.linea_horizontal{
				height:1px;
				font-size:0;
				border-left:2px solid #000;
				border-right:2px solid #000;
				border-bottom:2px solid #000;
			}
			#salto_pagina_anterior{
			  page-break-before:always;
			}
			.btImprimir{
			  text-align:center;
			  padding:7px;
			}
		</style>

		<style media="print" type="text/css">
			#btImprimir {
			   display:none;
			}
		</style>

		<div id="btImprimir" class="btImprimir"><input name="imprimir" type="button" class="button" value="Imprimir Informe" onClick="window.print();"></div>

		<?php $this->encabezado_reporte(); ?>

		<div class="imp_titulo_general">Listado de Expedientes/Notas en Pr&eacute;stamo</div>
		<div class="imp_subtitulo_general"></div>

		<?php $this->criterio_busqueda_reporte($filtro_para_pdf); ?>

		<div class="imp_titulos_de_clave">
			<div class="imp_titulos_de_clave_izq">A&ntilde;o&nbsp;&nbsp;&nbsp;T.&nbsp;&nbsp;Nro.&nbsp;&nbsp;Cpo.&nbsp;Alc.&nbsp;Letra&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Car&aacute;tula</div>
			<div class="imp_titulos_de_clave_der">Fecha Ingreso</div>
		</div>

		<?php
		$cantidad_expedientes = 0;
		$cantidad_notas = 0;
		// SE RECORREN LOS EXPEDIENTES A MOSTRAR
		$cantidad = count($listado_para_pdf);
		for ($i=0; $i < $cantidad; $i++)
		{
			$ficha = &$listado_para_pdf[$i];

			if ($ficha['tipo']=='E') $cantidad_expedientes++;
			elseif ($ficha['tipo']=='N') $cantidad_notas++;
		?>
			<div class="imp_ficha_titulos_de_clave" >
				<div class="imp_ficha_titulos_de_clave_izq" style="display: block;" >
					<?php echo $ficha['anio']; ?>&nbsp;<?php echo $ficha['tipo']; ?>&nbsp;<?php echo $ficha['numero']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['cuerpo']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['alcance']; ?>&nbsp;&nbsp;&nbsp;<?php echo $ficha['iniciador_codigo']; ?>&nbsp;&nbsp;&nbsp;<?php echo $ficha['caratula']; ?>
				</div>
				<div class="imp_ficha_titulos_de_clave_der">
					<?php echo $this->formatearFecha($ficha['fecha_entrada_expe']); ?>
				</div>
			</div>
			<?php
				$cantidad_proy = count($ficha['proyectos']);
				for ($p=0; $p < $cantidad_proy; $p++)
				{
					$proyecto = &$ficha['proyectos'][$p];

					// SI TIENE EXTRACTO SE MUESTRA
					if ( $proyecto['extracto'] != '' && $proyecto['extracto'] != 'null' )
					{
			?>
						<div class='imp_ficha_extracto'>
							<?php echo $proyecto['extracto']; ?>
						</div>
			<?php
					}
				}

				// SE OBTIENEN DATOS DEL ESTADO DEL EXPEDIENTE O NOTA
				$estado = $modelo->obtenerEstadoFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance'], $filtro['l_estado']);
			?>
			<div class='imp_ficha_extracto'>
				<?php echo "Fecha: ".$this->formatearFecha($estado[0]['fecha_estado'])."&nbsp;&nbsp;&nbsp;Estado: ".$estado[0]['nombre_estado']."&nbsp;&nbsp;&nbsp;Observaci&oacute;n: ".$estado[0]['observaciones_estado']; ?>
			</div>
			<div class='linea_horizontal'></div>
		<?php
		}

		echo $this->contador_reporte($cantidad_expedientes, $cantidad_notas);

		echo $this->pie_reporte();

		echo '<div id="btImprimir" class="btImprimir"><input name="imprimir" type="button" class="button" value="Imprimir Informe" onClick="window.print();"></div>';
    }
/******************************************************************************************************************
     SE GENERA EL REPORTE DE Expedientes/Notas en Comision EN FORMATO .doc PARA PROCESAR SU CONTENIDO
******************************************************************************************************************/
    public function procesar_texto_exped_en_prestamo($listado_para_documento_de_texto = '', $filtro_para_texto = '')
    {
		header("Content-Type: application/msword; charset=UTF-8");
		header('Content-Disposition: inline; filename=expedientes_y_notas_en_prestamo.doc');

		$modelo = new expedEnComisionModel();

		//  SE COMIENZA A ARMAR EL DOCUMENTO
		echo "<html>";
		echo "\n<body>";

		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Municipalidad de General Pueyrredon</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Sistema de Expedientes</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Honorable Concejo Deliberante</p>";
		echo "\n<hr>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Listado de Expedientes/Notas en Pr&eacute;stamo</p>";

		$estado = $modelo->obtenerNombreEstado($filtro_para_texto['l_estado']);
		if ( $estado != '' )
		{
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Estado ".$this->reemplazarPorHTML($estado)."</p>";
		}
		echo "\n<hr>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>A&ntilde;o".chr(9)."T.".chr(9)."Nro.".chr(9)."Letra".chr(9)."Car&aacute;tula</b></p>";
		echo "\n<hr>";

		// SE RECORREN LOS EXPEDIENTES A MOSTRAR
		$listado_por_marca = Array();

		// EXPEDIENTES/NOTAS
		$cantidad = count($listado_para_documento_de_texto);
		for ($i=0; $i < $cantidad; $i++)
		{
			$ficha = &$listado_para_documento_de_texto[$i];

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>".$ficha['anio']." ".$ficha['tipo']." ".$ficha['numero']." ".$ficha['iniciador_codigo']." ".$this->reemplazarPorHTML($ficha['caratula'])."</b></p>";

			// PROYECTOS
			$cantidad_proy = count($ficha['proyectos']);
			for ($p=0; $p < $cantidad_proy; $p++)
			{
				$proyecto = &$ficha['proyectos'][$p];
				echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'>".$this->reemplazarPorHTML($proyecto['extracto'])."</p>";
			}

			// SE OBTIENEN DATOS DEL ESTADO DEL EXPEDIENTE O NOTA
			$estado = $modelo->obtenerEstadoFicha($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance'], $filtro['l_estado']);

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt;'>Fecha: ".$this->formatearFecha($estado[0]['fecha_estado'])." Estado: ".$this->reemplazarPorHTML($estado[0]['nombre_estado'])." Observaci&oacute;n: ".$this->reemplazarPorHTML($estado[0]['observaciones_estado'])."</p>";
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";
		}

		echo "\n<hr>";

		 // PIE DEL DOCUMENTO A IMPRIMIR CON FECHA, USUARIO Y NOMBRE DE LA PC
		echo "\n<p style='margin-bottom: 0cm'p>".date("d/m/Y")."</p>";
		echo "\n<p style='margin-bottom: 0cm'p>USR. ".$_SESSION['usuario'].chr(9).chr(9).chr(9).chr(9).chr(9)."PC: ".gethostbyaddr($_SERVER['REMOTE_ADDR'])."</p>";

		echo "\n</body>";
		echo "\n</html>";
    }

    public function listar_expedientes_sin_cargar()
	{
		// MENSAJE DEL RESULTADO DE LA OPERACION REALIZADA
		$this->mostrarCartelResultado($mensaje, $tipo_mensaje);
	?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

	    <div id="precarga_modal" style="display:none"></div>
	    <div id="contenidoAjaxResultadoListados" class="msc_gral msc_texto" style="width:550px;height:150px;">

			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>

			<form action="listados/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">

				<div id="dragger_titulo_listado" class="msc_titulos degradado">Expedientes sin documento cargado</div>

				<div style="height:120px;margin-top:10px">
					<div style="width:355px;height:120px;float:left;">
						<div class="msl_criterio_fechas_margen"></div>
						<div class="msl_criterio_fechas_nombres">
							<div class="msc_dato_filtro">Ingresados desde el:</div>
						</div>
						<div class="msl_criterio_calendario">
							<?php
							if ( $filtro['l_enviado'] )
							{
								$l_fecha_desde = $this->formatearFecha($_SESSION['filtro_LISTADOS']['l_fecha_desde']);
							}
							else
							{
								// SE CALCULA LA FECHA 1 AÑO ANTES DEL ACTUAL
								$l_fecha_desde = date("d/m").'/'.( date("Y")-1 );
							}
							?>
							<input type="text" id="l_fecha_desde" name="l_fecha_desde" value="<?php echo $l_fecha_desde; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
							<input type="image" id="msl_btffecha_desde" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Desde" width="16" height="16">
						</div>

						<div class="msl_criterio_fechas_nombres">
							<div class="msc_dato_filtro" style="padding-top:3px">Hasta el:</div>
						</div>
						<div class="msl_criterio_calendario">
							<input type="text" id="l_fecha_hasta" name="l_fecha_hasta" value="<?php echo ($_SESSION['filtro_LISTADOS']['l_fecha_hasta']) ? $this->formatearFecha($_SESSION['filtro_LISTADOS']['l_fecha_hasta']) : date("d/m/Y") ; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
							<input type="image" id="msl_btffecha_hasta" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Hasta" width="16" height="16">
						</div>
					</div>
					<div class="msl_criterio_seccion_botones" style="height:120px;" >
						<!-- BOTON PARA GENERAR PDF -->
						<div id="contenedor_btImpresion" class="msc_boton degradado">
							<a id="btImpresion" href="#" title="Generar formato para Impresi&oacute;n" >
							  <img src="imagenes/barra/print_16x16.gif" width="16" height="16" align="left" />&nbsp;&nbsp;&nbsp;Impresi&oacute;n
							</a>
						</div>
						<!-- BOTON PARA GENERAR DOCUMENTO DE TEXTO -->
						<div id="contenedor_btProcesarTexto" class="msc_boton degradado">
							<a id="btProcesarTexto" href="#" title="Generar Documento de Texto" >
							  <img src="imagenes/iconos_office/doc.jpg" width="16" height="16" align="left" />&nbsp;&nbsp;&nbsp;Documento Texto
							</a>
						</div>
						<!-- BOTON PARA CERRAR -->
						<div class="msc_boton degradado">
							<a href="javascript:cerrarModalNueva();">
								<img src="imagenes/barra/error_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Cerrar
							</a>
						</div>
					</div>
				</div>
			</form>
	    </div>
	    <script>
			// PARA ARRASTRAR LA VENTANA MODAL CON EL MOUSE
			var menuDrag = new Drag.Move($('contenidoAjaxResultadoListados'), {
			   handle: $('dragger_titulo_listado')
			});

			//CALENDARIO PARA LA FECHA DESDE
			var calDesde = new Zapatec.Calendar.setup({

				inputField:"l_fecha_desde",
				ifFormat:"%d/%m/%Y",
				button:"msl_btffecha_desde",
				showsTime:false
			});

			//CALENDARIO PARA LA FECHA HASTA
			var calHasta = new Zapatec.Calendar.setup({

				inputField:"l_fecha_hasta",
				ifFormat:"%d/%m/%Y",
				button:"msl_btffecha_hasta",
				showsTime:false
			});

			$('l_fecha_desde').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;
					}
				}
			});

			$('l_fecha_hasta').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;
					}
				}
			});

			// AL CLIQUEAR EL BOTON IMPRESION
			$('btImpresion').addEvent('click', function()
			{
				// SI CONTIENEN VALOR AMBAS FECHAS
				if ( $('l_fecha_desde').value != '' && $('l_fecha_hasta').value != '' )
				{
					// SI LA FECHA DESDE ES MAYOR A LA FECHA HASTA
					if( esLaFechaMayor($('l_fecha_desde').value, $('l_fecha_hasta').value) )
					{
						alert("La fecha Desde debe ser menor a la fecha Hasta.");
					}
					else
					{
						// SI LA DIFERENCIA ENTRE LAS FECHAS NO SUPERA LA DETERMINADA
						if ( verificarDiferenciaAnios($('l_fecha_desde').value, $('l_fecha_hasta').value, 1) )
						{
							$('btImpresion').setProperty('href', 'listados/index.php?controlador=<?php echo $this->controlador; ?>&accion=armar_listado_exped_sin_cargar&formato=impresion&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'');
							$('btImpresion').setProperty('target', '_blank');
						}
						else
						{
							$('btImpresion').setProperty('href', 'javascript:return false;');
							$('btImpresion').setProperty('target', '');

							alert("La diferencia entre las fechas no debe ser mayor a un a"+'\u00f1'+"o.");
						}
					}
				}
				else
				{
					alert("Debe ingresar la fecha Desde y/o la fecha Hasta.");
				}
			});

			// AL CLIQUEAR EL BOTON PROCESAR TEXTO
			$('btProcesarTexto').addEvent('click', function()
			{
				// SI CONTIENEN VALOR AMBAS FECHAS
				if ( $('l_fecha_desde').value != '' && $('l_fecha_hasta').value != '' )
				{
					// SI LA FECHA DESDE ES MAYOR A LA FECHA HASTA
					if( esLaFechaMayor($('l_fecha_desde').value, $('l_fecha_hasta').value) )
					{
						alert("La fecha Desde debe ser menor a la fecha Hasta.");
					}
					else
					{
						// SI LA DIFERENCIA ENTRE LAS FECHAS NO SUPERA LA DETERMINADA
						if ( verificarDiferenciaAnios($('l_fecha_desde').value, $('l_fecha_hasta').value, 1) )
						{
							$('btProcesarTexto').setProperty('href', 'listados/index.php?controlador=<?php echo $this->controlador; ?>&accion=armar_listado_exped_sin_cargar&formato=texto&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value+'');
						}
						else
						{
							$('btProcesarTexto').setProperty('href', 'javascript:return false;');

							alert("La diferencia entre las fechas no debe ser mayor a un a"+'\u00f1'+"o.");
						}
					}
				}
				else
				{
					alert("Debe ingresar la fecha Desde y/o la fecha Hasta.");
				}
			});
		</script>
	<?php
	}

	public function generar_formato_de_impresion_expedientes_sin_cargar($listado_para_pdf = '', $filtro_para_pdf = '')
    {
		header("Content-Type: text/html; charset=UTF-8");
		$modelo = new consultaGralModel();
    ?>
		<style type="text/css">
		.imp_texto {
			font-family: Arial;
			font-size: 12px;
		}
		.imp_titulo_general{
			padding:10px 0 0 150px;
			font-size: 18px;
			font-weight:700;
			text-decoration:underline;
			text-align:left;
			border-left:2px solid #000;
			border-right:2px solid #000;
		}
		.imp_subtitulo_general{
			padding:10px 0 20px 10px;
			font-size:14px;
			font-weight:700;
			text-align:left;
			border-left:2px solid #000;
			border-right:2px solid #000;
		}
		/************************************************************/
		.imp_titulos_de_clave{
			height:25px;
			border:2px solid #000;
			border-bottom:0;
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
		.imp_bordes {
			border-left: 2px solid #000;
			border-right: 2px solid #000;
			border-bottom: 2px solid #000;
		}
		.imp_ficha {
			height: 20px;
			clear: both;
		}
		.imp_ficha_nombre{
			width:80px;
			height:20px;
			padding-left:5px;
			font-weight: bold;
			float:left;
		}
		.imp_ficha_valor{
			width:800px;
			height:20px;
			float:left;
		}
		.imp_ficha_extracto{
			padding:5px 0 5px 5px;
			font-size:12px;
			clear:both;
		}
		.btImprimir{
			text-align:center;
			padding:7px;
		}
		</style>

		<style media="print" type="text/css">
		#btImprimir {
			display:none;
		}
		</style>

		<div id="btImprimir" class="btImprimir"><input name="imprimir" type="button" class="button" value="Imprimir Informe" onClick="window.print();"></div>

		<?php $this->encabezado_reporte(); ?>

		<div class="imp_texto imp_titulo_general">Listado de Expedientes sin documento cargado</div>

		<div class="imp_texto imp_subtitulo_general"></div>

		<?php $this->criterio_busqueda_reporte($filtro_para_pdf); ?>

		<div class="imp_titulos_de_clave">
			<div class="imp_texto imp_titulos_de_clave_izq">A&ntilde;o&nbsp;&nbsp;T.&nbsp;&nbsp;Nro.&nbsp;&nbsp;Cpo.&nbsp;Alc.</div>
			<div class="imp_texto imp_titulos_de_clave_der">Fecha Ingreso</div>
		</div>
		<div class="imp_bordes">
			<?php
			$cantidad = count($listado_para_pdf);
			for ($f=0; $f < $cantidad; $f++)
			{
				$ficha = &$listado_para_pdf[$f];
			?>
				<div class="imp_texto imp_ficha_titulos_de_clave">
					<div class="imp_ficha_titulos_de_clave_izq"><?php echo $ficha['anio']; ?>&nbsp;<?php echo $ficha['tipo']; ?>&nbsp;<?php echo $ficha['numero']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['cuerpo']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['alcance']; ?></div>
					<div class="imp_titulos_de_clave_der"><?php echo $this->formatearFecha($ficha['fecha_entrada_expe']); ?></div>
				</div>

				<div class="imp_texto imp_ficha">
					<div class="imp_ficha_nombre">Car&aacute;tula</div>
					<div class="imp_ficha_valor"><?php echo $ficha['caratula']; ?></div>
				</div>
				<div class="imp_texto imp_ficha">
					<div class="imp_ficha_nombre">Iniciador</div>
					<div class="imp_ficha_valor"><?php echo $ficha['iniciador']; ?></div>
				</div>
				<div class="imp_texto imp_ficha">
					<div class="imp_ficha_nombre">Categor&iacute;a</div>
					<div class="imp_ficha_valor"><?php echo $ficha['categoria']; ?></div>
				</div>
				<?php
				$cantidad_proy = count($ficha['proyectos']);
				for ($p=0; $p < $cantidad_proy; $p++)
				{
					$proyecto = &$ficha['proyectos'][$p];
				?>
					<div class="imp_texto imp_ficha">
						<div class="imp_ficha_nombre">Proyecto de</div>
						<div class="imp_ficha_valor"><?php echo $proyecto['descripcion_proyecto']; ?></div>
					</div>
				<?php
					// SI TIENE EXTRACTO SE MUESTRA
					if ( $proyecto['extracto'] != '' &&  $proyecto['extracto'] != 'null' )
					{
				?>
						<div class="imp_texto imp_ficha_extracto"><?php echo $proyecto['extracto']; ?></div>
				<?php
					}
				}
			}
		?>
		</div>
		<?php echo $this->pie_reporte(); ?>
		<div id="btImprimir" class="btImprimir">
			<input name="imprimir" type="button" class="button" value="Imprimir Informe" onClick="window.print();">
		</div>
	<?php
    }

    public function procesar_texto_expedientes_sin_cargar($listado_para_pdf = '', $filtro_para_pdf = '')
    {
		header("Content-Type: application/msword; charset=UTF-8");
		header('Content-Disposition: inline; filename=expedientes_sin_documento_cargado.doc');

		$modelo = new consultaGralModel();
		$modeloEnComision = new expedEnComisionModel();
		//  SE COMIENZA A ARMAR EL DOCUMENTO .doc
		echo "<html>";
		echo "\n<body>";

		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Municipalidad de General Pueyrredon</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Sistema de Expedientes</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Honorable Concejo Deliberante</p>";
		echo "\n<hr>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Listado de Expedientes sin documento cargado</p>";

		if (isset($filtro_para_pdf['l_fecha_desde']) || isset($filtro_para_pdf['l_fecha_hasta']))
		{
			echo chr(9)."\n<p style='margin:0cm;margin-bottom:.0001pt'>Fecha Desde: ";
			if (isset($filtro_para_pdf['l_fecha_desde']))
			{
				echo $this->formatearFecha($filtro_para_pdf['l_fecha_desde']);
			}
			else
			{
				echo '';
			}

			echo chr(9).chr(9).chr(9)."Fecha Hasta: ";
			if (isset($filtro_para_pdf['l_fecha_hasta']))
			{
				echo $this->formatearFecha($filtro_para_pdf['l_fecha_hasta']);
			}
			else
			{
				echo '';
			}
			echo "\n</p>";
		} // FIN DEL FILTRO DE FECHA

		echo "\n<hr>";
		// TITULOS PARA LA CLAVE DEL EXPEDIENTE Y SU FECHA DE INGRESO
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>A&ntilde;o".chr(9)."T.".chr(9)."Nro.".chr(9)."Cpo.".chr(9)."Al.</b>".chr(9).chr(9).chr(9)."Fecha Ingreso</p>";

		echo "\n<hr>";

		$cantidad = count($listado_para_pdf);
		for ($f=0; $f < $cantidad; $f++)
		{
			$ficha = &$listado_para_pdf[$f];
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>".$ficha['anio'].chr(9).$ficha['tipo'].chr(9).$ficha['numero'].chr(9).$ficha['cuerpo'].chr(9).$ficha['alcance']."</b>".chr(9).chr(9).chr(9).$this->formatearFecha($ficha['fecha_entrada_expe'])."</p>";

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Car&aacute;tula: </b>".chr(9).$this->reemplazarPorHTML($ficha['caratula'])."</p>";

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Iniciador: </b>".chr(9).$this->reemplazarPorHTML($ficha['iniciador'])."</p>";

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Categor&iacute;a: </b>".chr(9).$this->reemplazarPorHTML($ficha['categoria'])."</p>";

			$cantidad_proy = count($ficha['proyectos']);
			for ($p=0; $p < $cantidad_proy; $p++)
			{
				$proyecto = &$ficha['proyectos'][$p];

				echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Proyecto de ".$this->reemplazarPorHTML($proyecto['descripcion_proyecto'])."</b></p>";

				// SI TIENE EXTRACTO SE MUESTRA
				if ( $proyecto['extracto'] != '' &&  $proyecto['extracto'] != 'null' )
				{
					echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'><b>Extracto: </b>".chr(9).$this->reemplazarPorHTML($proyecto['extracto'])."</p>";
				}
			}

			echo "\n<hr>";
		}// FIN DEL for

		// PIE DEL DOCUMENTO A IMPRIMIR CON FECHA, USUARIO Y NOMBRE DE LA PC
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>".date("d/m/Y")."</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>USR. ".$_SESSION['usuario'].chr(9).chr(9).chr(9).chr(9).chr(9)."PC: ".gethostbyaddr($_SERVER['REMOTE_ADDR'])."</p>";

		echo "\n</body>";
		echo "\n</html>";
    }

    /**
     * Se genera el reporte de Expedientes para Expurgo en formato .csv para procesar su contenido
     *
     * @param array $listado_para_csv
     * @param array $filtro_para_csv
     */
    public function procesarTextoExpurgoFormatoCSV($listado_para_csv = '', $filtro_para_csv = '')
    {
    	header("Cache-Control: must-revalidate");
    	header("Pragma: must-revalidate");
    	header("Content-type: application/vnd.ms-excel");
    	header('Content-Disposition: attachment; filename=expediente_para_expurgo.csv');

    	$modelo = new consultaGralModel();

    	//  SE COMIENZA A ARMAR EL DOCUMENTO
    	echo "Municipalidad de General Pueyrredon";
    	echo "\nSistema de Expedientes";
    	echo "\nHonorable Concejo Deliberante";
    	echo "\n";
    	echo "\nListado de Expedientes/Notas";

    	if ($filtro_para_csv['l_estado'])
    	{
    		//SE OBTIENE EL Estado DEL Expediente RESULTANTE
    		$estado = $modelo->obtenerNombreEstado($filtro_para_csv['l_estado']);

    		echo "\nEn Estado: ".$estado;
    	}
    	// Rango de fechas Desde - Hasta
    	echo "\nFecha Desde: ".$this->formatearFecha($filtro_para_csv['l_fecha_desde']).",Fecha Hasta:,".$this->formatearFecha($filtro_para_csv['l_fecha_hasta']);

    	// TITULOS PARA LA CLAVE DEL EXPEDIENTE Y SU FECHA DE INGRESO
    	echo "\nAño,Tipo,Número,Cuerpo,Alcance,Fecha Ingreso,Orden Proyecto,Proyecto,Fecha Prom.,Nro. Prom.,Decreto Prom.,Fecha Sanc.,Nro. Sanc.,Extracto";

    	$cantidad = count($listado_para_csv);
    	for ($f=0; $f < $cantidad; $f++)
    	{
    		$ficha = &$listado_para_csv[$f];

    		$cantidad_proy = count($ficha['proyectos']);

        	for ($p=0; $p < $cantidad_proy; $p++)
    		{
    			$proyecto = &$ficha['proyectos'][$p];

    			$datos_sancion = $modelo->obtenerDatosSancion($ficha['anio'], $ficha['tipo'], $ficha['numero'], $ficha['cuerpo'], $ficha['alcance'], $ficha['orden_proyecto']);

    			echo "\n".$ficha['anio'].",".$ficha['tipo'].",".$ficha['numero'].",".$ficha['cuerpo'].",".$ficha['alcance'].",".$this->formatearFecha($ficha['fecha_entrada_expe']).",".$proyecto['orden_proyecto'].",".$proyecto['descripcion_proyecto'].",".$this->formatearFecha($datos_sancion[0]['fecha_promulga']).",".$datos_sancion[0]['numero_promulga'].",".$datos_sancion[0]['decreto_promulga'].",".$this->formatearFecha($datos_sancion[0]['fecha_sancion']).",".$datos_sancion[0]['numero_sancion'].",\"".str_replace('"',"",$proyecto['extracto'])."\"";
    		}
    	}
    }

    /**
	 * 2019/02/13
	 * SE OBTIENE EL LISTADO DE EXPEDIENTES SIN digitalizar, PARA GENERAR EL FORMATO DE IMPRESION Y DE DOCUMENTO DE TEXTO
	 */
    public function listar_expedientes_sin_digitalizar()
	{
		// MENSAJE DEL RESULTADO DE LA OPERACION REALIZADA
		$this->mostrarCartelResultado($mensaje, $tipo_mensaje);
	?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>

	    <div id="precarga_modal" style="display:none"></div>
	    <div id="contenidoAjaxResultadoListados" class="msc_gral msc_texto" style="width:550px;height:150px;">

			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>

			<form action="listados/index.php" method="POST" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">

				<div id="dragger_titulo_listado" class="msc_titulos degradado">Expedientes sin Digitalizar</div>

				<div style="height:120px;margin-top:10px">
					<div style="width:355px;height:120px;float:left;">
						<div class="msl_criterio_fechas_margen"></div>
						<div class="msl_criterio_fechas_nombres">
							<div class="msc_dato_filtro">Ingresados desde el:</div>
						</div>
						<div class="msl_criterio_calendario">
							<?php
							if ( $filtro['l_enviado'] )
							{
								$l_fecha_desde = $this->formatearFecha($_SESSION['filtro_LISTADOS']['l_fecha_desde']);
							}
							else
							{
								// SE CALCULA LA FECHA 1 AÑO ANTES DEL ACTUAL
								$l_fecha_desde = date("d/m").'/'.( date("Y")-1 );
							}
							?>
							<input type="text" id="l_fecha_desde" name="l_fecha_desde" value="<?php echo $l_fecha_desde; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
							<input type="image" id="msl_btffecha_desde" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Desde" width="16" height="16">
						</div>

						<div class="msl_criterio_fechas_nombres">
							<div class="msc_dato_filtro" style="padding-top:3px">Hasta el:</div>
						</div>
						<div class="msl_criterio_calendario">
							<input type="text" id="l_fecha_hasta" name="l_fecha_hasta" value="<?php echo ($_SESSION['filtro_LISTADOS']['l_fecha_hasta']) ? $this->formatearFecha($_SESSION['filtro_LISTADOS']['l_fecha_hasta']) : date("d/m/Y") ; ?>" onKeyPress="return solo_enteros_y_barra(event);" onkeyup="mascara(this,'/',patron,true)" size="8" maxlength="10" class="msc_ffecha" />
							<input type="image" id="msl_btffecha_hasta" src="imagenes/calendario/calendario.gif" alt="Calendario, presione aqu&iacute; para seleccionar la fecha Hasta" width="16" height="16">
						</div>
					</div>
					<div class="msl_criterio_seccion_botones" style="height:120px;" >
						<!-- BOTON PARA GENERAR PDF -->
						<div id="contenedor_btImpresion" class="msc_boton degradado">
							<a id="btImpresion" href="#" title="Generar formato para Impresi&oacute;n" >
							  <img src="imagenes/barra/print_16x16.gif" width="16" height="16" align="left" />&nbsp;&nbsp;&nbsp;Impresi&oacute;n
							</a>
						</div>
						<!-- BOTON PARA GENERAR DOCUMENTO DE TEXTO -->
						<div id="contenedor_btProcesarTexto" class="msc_boton degradado">
							<a id="btProcesarTexto" href="#" title="Generar Documento de Texto" >
							  <img src="imagenes/iconos_office/doc.jpg" width="16" height="16" align="left" />&nbsp;&nbsp;&nbsp;Documento Texto
							</a>
						</div>
						<!-- BOTON PARA CERRAR -->
						<div class="msc_boton degradado">
							<a href="javascript:cerrarModalNueva();">
								<img src="imagenes/barra/error_16x16.gif" width="16" height="16" />&nbsp;&nbsp;&nbsp;Cerrar
							</a>
						</div>
					</div>
				</div>
			</form>
	    </div>
	    <script>
			// PARA ARRASTRAR LA VENTANA MODAL CON EL MOUSE
			var menuDrag = new Drag.Move($('contenidoAjaxResultadoListados'), {
			   handle: $('dragger_titulo_listado')
			});

			//CALENDARIO PARA LA FECHA DESDE
			var calDesde = new Zapatec.Calendar.setup({

				inputField:"l_fecha_desde",
				ifFormat:"%d/%m/%Y",
				button:"msl_btffecha_desde",
				showsTime:false
			});

			//CALENDARIO PARA LA FECHA HASTA
			var calHasta = new Zapatec.Calendar.setup({

				inputField:"l_fecha_hasta",
				ifFormat:"%d/%m/%Y",
				button:"msl_btffecha_hasta",
				showsTime:false
			});

			$('l_fecha_desde').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;
					}
				}
			});

			$('l_fecha_hasta').addEvents({
				click: function(){
					se_busca = true;
				},
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('msl_btffecha_desde').disabled = false;
					$('msl_btffecha_hasta').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('msl_btffecha_desde').disabled = true;
						$('msl_btffecha_hasta').disabled = true;
					}
				}
			});

			// AL CLIQUEAR EL BOTON IMPRESION
			$('btImpresion').addEvent('click', function()
			{
				// SI CONTIENEN VALOR AMBAS FECHAS
				if ( $('l_fecha_desde').value != '' && $('l_fecha_hasta').value != '' )
				{
					// SI LA FECHA DESDE ES MAYOR A LA FECHA HASTA
					if( esLaFechaMayor($('l_fecha_desde').value, $('l_fecha_hasta').value) )
					{
						alert("La fecha Desde debe ser menor a la fecha Hasta.");
					}
					else
					{
						// SI LA DIFERENCIA ENTRE LAS FECHAS NO SUPERA LA DETERMINADA
						if ( verificarDiferenciaAnios($('l_fecha_desde').value, $('l_fecha_hasta').value, 1) )
						{
							$('btImpresion').setProperty('href', 'listados/index.php?controlador=<?php echo $this->controlador; ?>&accion=armar_listado_exped_sin_digitalizar&formato=impresion&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value);
							$('btImpresion').setProperty('target', '_blank');
						}
						else
						{
							$('btImpresion').setProperty('href', 'javascript:return false;');
							$('btImpresion').setProperty('target', '');

							alert("La diferencia entre las fechas no debe ser mayor a un a"+'\u00f1'+"o.");
						}
					}
				}
				else
				{
					alert("Debe ingresar la fecha Desde y/o la fecha Hasta.");
				}
			});

			// AL CLIQUEAR EL BOTON PROCESAR TEXTO
			$('btProcesarTexto').addEvent('click', function()
			{
				// SI CONTIENEN VALOR AMBAS FECHAS
				if ( $('l_fecha_desde').value != '' && $('l_fecha_hasta').value != '' )
				{
					// SI LA FECHA DESDE ES MAYOR A LA FECHA HASTA
					if( esLaFechaMayor($('l_fecha_desde').value, $('l_fecha_hasta').value) )
					{
						alert("La fecha Desde debe ser menor a la fecha Hasta.");
					}
					else
					{
						// SI LA DIFERENCIA ENTRE LAS FECHAS NO SUPERA LA DETERMINADA
						if ( verificarDiferenciaAnios($('l_fecha_desde').value, $('l_fecha_hasta').value, 1) )
						{
							$('btProcesarTexto').setProperty('href', 'listados/index.php?controlador=<?php echo $this->controlador; ?>&accion=armar_listado_exped_sin_digitalizar&formato=texto&l_fecha_desde='+$('l_fecha_desde').value+'&l_fecha_hasta='+$('l_fecha_hasta').value);
						}
						else
						{
							$('btProcesarTexto').setProperty('href', 'javascript:return false;');

							alert("La diferencia entre las fechas no debe ser mayor a un a"+'\u00f1'+"o.");
						}
					}
				}
				else
				{
					alert("Debe ingresar la fecha Desde y/o la fecha Hasta.");
				}
			});
		</script>
	<?php
	}

	/**
	 * [generar_formato_de_impresion_expedientes_sin_digitalizar description]
	 * @param  string $listado_para_pdf [description]
	 * @param  string $filtro_para_pdf  [description]
	 * @return [type]                   [description]
	 */
	public function generar_formato_de_impresion_expedientes_sin_digitalizar($listado_para_pdf = '', $filtro_para_pdf = '')
    {
		header("Content-Type: text/html; charset=UTF-8");
		$modelo = new consultaGralModel();
    ?>
		<style type="text/css">
			.imp_texto {
				font-family: Arial;
				font-size: 12px;
			}
			.imp_titulo_general{
				padding:10px 0 0 150px;
				font-size: 18px;
				font-weight:700;
				text-decoration:underline;
				text-align:left;
				border-left:2px solid #000;
				border-right:2px solid #000;
			}
			.imp_subtitulo_general{
				padding:10px 0 20px 10px;
				font-size:14px;
				font-weight:700;
				text-align:left;
				border-left:2px solid #000;
				border-right:2px solid #000;
			}
			/************************************************************/
			.imp_titulos_de_clave{
				height:25px;
				border:2px solid #000;
				border-bottom:0;
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
			.imp_bordes {
				border-left: 2px solid #000;
				border-right: 2px solid #000;
				border-bottom: 2px solid #000;
			}
			.imp_ficha {
				height: 20px;
				clear: both;
			}
			.imp_ficha_nombre{
				width:80px;
				height:20px;
				padding-left:5px;
				font-weight: bold;
				float:left;
			}
			.imp_ficha_valor{
				width:800px;
				height:20px;
				float:left;
			}
			.imp_ficha_extracto{
				padding:5px 0 5px 5px;
				font-size:12px;
				clear:both;
			}
			.btImprimir{
				text-align:center;
				padding:7px;
			}
		</style>

		<style media="print" type="text/css">
			#btImprimir {
				display:none;
			}
		</style>

		<div id="btImprimir" class="btImprimir"><input name="imprimir" type="button" class="button" value="Imprimir Informe" onClick="window.print();"></div>

		<?php $this->encabezado_reporte(); ?>

		<div class="imp_texto imp_titulo_general">Listado de Expedientes sin Digitalizar</div>

		<div class="imp_texto imp_subtitulo_general"></div>

		<?php $this->criterio_busqueda_reporte($filtro_para_pdf); ?>

		<div class="imp_titulos_de_clave">
			<div class="imp_texto imp_titulos_de_clave_izq">A&ntilde;o&nbsp;&nbsp;T.&nbsp;&nbsp;Nro.&nbsp;&nbsp;Cpo.&nbsp;Alc.</div>
			<div class="imp_texto imp_titulos_de_clave_der">Fecha Ingreso</div>
		</div>
		<div class="imp_bordes">
			<?php
			$cantidad = count($listado_para_pdf);
			for ($f=0; $f < $cantidad; $f++)
			{
				$ficha = &$listado_para_pdf[$f];
			?>
				<div class="imp_texto imp_ficha_titulos_de_clave">
					<div class="imp_ficha_titulos_de_clave_izq"><?php echo $ficha['anio']; ?>&nbsp;<?php echo $ficha['tipo']; ?>&nbsp;<?php echo $ficha['numero']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['cuerpo']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ficha['alcance']; ?></div>
					<div class="imp_titulos_de_clave_der"><?php echo $this->formatearFecha($ficha['fecha_entrada_expe']); ?></div>
				</div>

				<div class="imp_texto imp_ficha">
					<div class="imp_ficha_nombre">Car&aacute;tula</div>
					<div class="imp_ficha_valor"><?php echo $ficha['caratula']; ?></div>
				</div>
				<div class="imp_texto imp_ficha">
					<div class="imp_ficha_nombre">Iniciador</div>
					<div class="imp_ficha_valor"><?php echo $ficha['iniciador']; ?></div>
				</div>
				<div class="imp_texto imp_ficha">
					<div class="imp_ficha_nombre">Categor&iacute;a</div>
					<div class="imp_ficha_valor"><?php echo $ficha['categoria']; ?></div>
				</div>
				<?php
				$cantidad_proy = count($ficha['proyectos']);
				for ($p=0; $p < $cantidad_proy; $p++)
				{
					$proyecto = &$ficha['proyectos'][$p];
				?>
					<div class="imp_texto imp_ficha">
						<div class="imp_ficha_nombre">Proyecto de</div>
						<div class="imp_ficha_valor"><?php echo $proyecto['descripcion_proyecto']; ?></div>
					</div>
				<?php
					// SI TIENE EXTRACTO SE MUESTRA
					if ( $proyecto['extracto'] != '' &&  $proyecto['extracto'] != 'null' )
					{
				?>
						<div class="imp_texto imp_ficha_extracto"><?php echo $proyecto['extracto']; ?></div>
				<?php
					}
				}
			}
		?>
		</div>
		<?php echo $this->pie_reporte(); ?>
		<div id="btImprimir" class="btImprimir">
			<input name="imprimir" type="button" class="button" value="Imprimir Informe" onClick="window.print();">
		</div>
	<?php
    }

    /**
     * [procesar_texto_expedientes_sin_digitalizar description]
     * @param  string $listado_para_pdf [description]
     * @param  string $filtro_para_pdf  [description]
     * @return [type]                   [description]
     */
    public function procesar_texto_expedientes_sin_digitalizar($listado_para_pdf = '', $filtro_para_pdf = '')
    {
		header("Content-Type: application/msword; charset=UTF-8");
		header('Content-Disposition: inline; filename=expedientes_sin_digitalizar.doc');

		$modelo = new consultaGralModel();
		$modeloEnComision = new expedEnComisionModel();
		//  SE COMIENZA A ARMAR EL DOCUMENTO .doc
		echo "<html>";
		echo "\n<body>";

		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Municipalidad de General Pueyrredon</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Sistema de Expedientes</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Honorable Concejo Deliberante</p>";
		echo "\n<hr>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>Listado de Expedientes sin Digitalizar</p>";

		if (isset($filtro_para_pdf['l_fecha_desde']) || isset($filtro_para_pdf['l_fecha_hasta']))
		{
			echo chr(9)."\n<p style='margin:0cm;margin-bottom:.0001pt'>Fecha Desde: ";
			if (isset($filtro_para_pdf['l_fecha_desde']))
			{
				echo $this->formatearFecha($filtro_para_pdf['l_fecha_desde']);
			}
			else
			{
				echo '';
			}

			echo chr(9).chr(9).chr(9)."Fecha Hasta: ";
			if (isset($filtro_para_pdf['l_fecha_hasta']))
			{
				echo $this->formatearFecha($filtro_para_pdf['l_fecha_hasta']);
			}
			else
			{
				echo '';
			}
			echo "\n</p>";
		} // FIN DEL FILTRO DE FECHA

		echo "\n<hr>";
		// TITULOS PARA LA CLAVE DEL EXPEDIENTE Y SU FECHA DE INGRESO
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>A&ntilde;o".chr(9)."T.".chr(9)."Nro.".chr(9)."Cpo.".chr(9)."Al.</b>".chr(9).chr(9).chr(9)."Fecha Ingreso</p>";

		echo "\n<hr>";

		$cantidad = count($listado_para_pdf);
		for ($f=0; $f < $cantidad; $f++)
		{
			$ficha = &$listado_para_pdf[$f];
			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>".$ficha['anio'].chr(9).$ficha['tipo'].chr(9).$ficha['numero'].chr(9).$ficha['cuerpo'].chr(9).$ficha['alcance']."</b>".chr(9).chr(9).chr(9).$this->formatearFecha($ficha['fecha_entrada_expe'])."</p>";

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><br></p>";

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Car&aacute;tula: </b>".chr(9).$this->reemplazarPorHTML($ficha['caratula'])."</p>";

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Iniciador: </b>".chr(9).$this->reemplazarPorHTML($ficha['iniciador'])."</p>";

			echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Categor&iacute;a: </b>".chr(9).$this->reemplazarPorHTML($ficha['categoria'])."</p>";

			$cantidad_proy = count($ficha['proyectos']);
			for ($p=0; $p < $cantidad_proy; $p++)
			{
				$proyecto = &$ficha['proyectos'][$p];

				echo "\n<p style='margin:0cm;margin-bottom:.0001pt'><b>Proyecto de ".$this->reemplazarPorHTML($proyecto['descripcion_proyecto'])."</b></p>";

				// SI TIENE EXTRACTO SE MUESTRA
				if ( $proyecto['extracto'] != '' &&  $proyecto['extracto'] != 'null' )
				{
					echo "\n<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify'><b>Extracto: </b>".chr(9).$this->reemplazarPorHTML($proyecto['extracto'])."</p>";
				}
			}

			echo "\n<hr>";
		}// FIN DEL for

		// PIE DEL DOCUMENTO A IMPRIMIR CON FECHA, USUARIO Y NOMBRE DE LA PC
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>".date("d/m/Y")."</p>";
		echo "\n<p style='margin:0cm;margin-bottom:.0001pt'>USR. ".$_SESSION['usuario'].chr(9).chr(9).chr(9).chr(9).chr(9)."PC: ".gethostbyaddr($_SERVER['REMOTE_ADDR'])."</p>";

		echo "\n</body>";
		echo "\n</html>";
    }
}
?>

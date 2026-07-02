<?php
abstract class VistaBase 
{ 
	protected $modelo;
		
    //SE LE DA EL FORMATO dia/mes/anio completo
    public function formatearFecha($fecha)
    {
		if ( $fecha ) {
			if ( $fecha != '0000-00-00' ) {
				$fec_partes = explode("-",$fecha);
				$fecha_a_ver = $fec_partes[2].'/'.$fec_partes[1].'/'.$fec_partes[0];// PARA 2 CIFRAS substr($fec_partes[0], -2)
				return $fecha_a_ver;
			} else
				return '';
		} else
			return '';
    }
	
	//SE LE DA EL FORMATO dia/mes/anio completo
	public function formatearFechaLog($fecha)
	{
		if ( $fecha )
		{
			$fecha_hora_partes = explode(" ", $fecha);// SE SEPARA LA FECHA Y LA HORA DEL LOG
			$fecha_log = $fecha_hora_partes[0];//FECHA
			//$hora_log = $fecha_hora_partes[1];//HORA
			if ( $fecha != '0000-00-00' )
			{
				$fec_partes = explode("-",$fecha_log);
				$fecha_a_ver = $fec_partes[2].'/'.$fec_partes[1].'/'.$fec_partes[0]; 
				return $fecha_a_ver;
			}
			else
			{
				return '';
			}
		}
		else
		{
			return '';
		}	
	}
	
    public function cortaCadena($string, $charlimit)
    {
		if ( substr($string, $charlimit-1, 1) != '' )
		{
			$string = substr($string, 0, $charlimit);
			$array = explode(' ',$string);
			array_pop($array);
			$new_string = implode(' ',$array);
			
			return $new_string.' ...';
		}
		else
		{
			return substr($string, 0, $charlimit-1).' ...';
		}
    }
	
    public function mostrarCartelResultado($mensaje = '', $tipo_mensaje = '')
    {
    	if ( $mensaje != '' )
		{
		?> 
			<div id="abm_mensaje_resultado" class="abm_mensaje_resultado abm_fondo_mensaje<?php echo $tipo_mensaje; ?>" >
				<div class="abm_mensaje abm_imagen_mensaje<?php echo $tipo_mensaje; ?>">
					<?php echo nl2br(strip_tags($mensaje)); ?>
				</div>
				<div id="abm_mensaje_btCerrar"></div>
			</div>
			<script>
				var tipo_mensaje = <?php echo $tipo_mensaje; ?>;
				
				// Si es un mensaje satisfactorio
				if (tipo_mensaje == 1)
				{
					// No se muestra el botón Cerrar
					$('abm_mensaje_btCerrar').setStyle('display', 'none');
					
					// Se desvanece el mensaje en dos segundos
					ocultarElemento('abm_mensaje_resultado', 2000);
				}
				else// En caso de ser un mensaje de error
				{
					// Se muestra el botón Cerrar
					$('abm_mensaje_btCerrar').setStyle('display', 'block');
					
					// Al cliquear en el botón Cerrar
					$('abm_mensaje_btCerrar').addEvent('click', function()
					{
						// Se oculta el mensaje
						$('abm_mensaje_resultado').setStyle('display', 'none');
					});
				}
			</script>
		<?php
		}
	}	
    
    public function mostrarCartelResultado2($mensaje = '', $tipo_mensaje = '')
    {
		if ( $mensaje != '' )
		{
		?> 
			<div id="abm_mensaje_resultado2" class="abm_mensaje_resultado abm_fondo_mensaje<?php echo $tipo_mensaje; ?>" >
				<div class="abm_mensaje abm_imagen_mensaje<?php echo $tipo_mensaje; ?>">
					<?php echo nl2br(strip_tags($mensaje)); ?>
				</div>
				<div id="abm_mensaje_btCerrar2"></div>
			</div>
			<script>
				$('abm_mensaje_btCerrar2').addEvent('click', function()
				{
					$('abm_mensaje_resultado2').setStyle('display', 'none');
				});
			</script>
		<?php
		}
	}	
    
	/**
	 * Se define el paginador para el listado respectivo
	 * 
	 * @param string $directorio
	 * @param string $controlador
	 * @param string $accion
	 * @param array $filtro
	 * @param string $criterio_buscador
	 */
    public function mostrarPaginador($directorio, $controlador, $accion, $filtro, $criterio_buscador = '')
    {
    	if ( $filtro['nro_paginas'] > 0 )
    	{
	?>
			<div class="p_bnav_contenedor_4bt">
				<?php
				if ( $filtro['pagina'] != 1 )
				{
				?>
					<a id="btPrimero" title="Primer Registro" href="javascript:refrescar('<?php echo $directorio; ?>/index.php?controlador=<?php echo $controlador; ?>&accion=<?php echo $accion; ?>&campo_orden=<?php echo $_SESSION['ultimo_campo']; ?>&valor_buscado=<?php echo (isset($filtro['valor_buscado'])) ? $filtro['valor_buscado'] : ''; ?>&pagina=1&sentido=primero&mostrar_todos=<?php echo (isset($filtro['mostrar_todos'])) ? $filtro['mostrar_todos'] : ''; ?><?php echo $criterio_buscador; ?>', 'contenidoAjaxPrincipal');">
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
				
				if ( $filtro['pagina_ant'] != 0 )
				{
				?>
					<a id="btAnterior" title="Registro Anterior" href="javascript:refrescar('<?php echo $directorio; ?>/index.php?controlador=<?php echo $controlador; ?>&accion=<?php echo $accion; ?>&campo_orden=<?php echo $_SESSION['ultimo_campo']; ?>&valor_buscado=<?php echo (isset($filtro['valor_buscado'])) ? $filtro['valor_buscado'] : ''; ?>&pagina=<?php echo $filtro['pagina_ant']; ?>&sentido=anterior&mostrar_todos=<?php echo (isset($filtro['mostrar_todos'])) ? $filtro['mostrar_todos'] : ''; ?><?php echo $criterio_buscador; ?>', 'contenidoAjaxPrincipal');">
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
				
				echo "&nbsp;".$filtro['pagina']." de ".$filtro['nro_paginas']."&nbsp;";	
				
				if ( $filtro['pagina'] != $filtro['nro_paginas'] )
				{
				?>
					<a id="btSiguiente" title="Registro Siguiente" href="javascript:refrescar('<?php echo $directorio; ?>/index.php?controlador=<?php echo $controlador; ?>&accion=<?php echo $accion; ?>&campo_orden=<?php echo $_SESSION['ultimo_campo']; ?>&valor_buscado=<?php echo (isset($filtro['valor_buscado'])) ? $filtro['valor_buscado'] : ''; ?>&pagina=<?php echo $filtro['pagina_sgte']; ?>&sentido=siguiente&mostrar_todos=<?php echo (isset($filtro['mostrar_todos'])) ? $filtro['mostrar_todos'] : ''; ?><?php echo $criterio_buscador; ?>', 'contenidoAjaxPrincipal');">
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
					
				if ( $filtro['pagina'] != $filtro['nro_paginas'] )
				{
				?>
					<a id="btUltimo" title="Ultimo Registro" href="javascript:refrescar('<?php echo $directorio; ?>/index.php?controlador=<?php echo $controlador; ?>&accion=<?php echo $accion; ?>&campo_orden=<?php echo $_SESSION['ultimo_campo']; ?>&valor_buscado=<?php echo (isset($filtro['valor_buscado'])) ? $filtro['valor_buscado'] : ''; ?>&pagina=<?php echo $filtro['nro_paginas']; ?>&sentido=ultimo&mostrar_todos=<?php echo (isset($filtro['mostrar_todos'])) ? $filtro['mostrar_todos'] : ''; ?><?php echo $criterio_buscador; ?>', 'contenidoAjaxPrincipal');">
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
	<?php
    	}
	}
    
    public function mostrarPaginadorEnEdicion()
    {
		?>
		<div class="p_bnav_contenedor_4bt">
			<a id="btPrimero" href="#">
				<img id="imgPrimero" src="imagenes/barra/bd_firstpage.png" width="14" height="14" align="top" />
			</a>
			<a id="btAnterior" href="#">
				<img id="imgAnterior" src="imagenes/barra/bd_prevpage.png" width="14" height="14" align="top" />
			</a>
			<a id="btSiguiente" href="#">
				<img id="imgSiguiente" src="imagenes/barra/bd_nextpage.png" width="14" height="14" align="top" />
			</a>
			<a id="btUltimo" href="#">
				<img id="imgUltimo" src="imagenes/barra/bd_lastpage.png" width="14" height="14" align="top" />
			</a>
		</div>
		<?php
	}
		
    public function mostrarBarraNavegacionSuperior_Archivos($directorio, $controlador, $accion, $filtro, $mensaje = '', $tipo_mensaje = '')
    {
		?>	
		<div id="p_barra_navegacion" class="p_barra_navegacion">
		
			<div class="p_bnav_contenedor_2bt p_bnav_sin_borde_izquierdo">
				<a id="btBG" title="Consulta Gen&eacute;rica" href="#">
					<img id="imgBG" src="imagenes/barra/buscar_gris_16x16.gif" width="16" height="16" />
				</a>
				<a id="btBA" title="Buscar por Antecedente" href="#">
					<img id="imgBA" src="imagenes/barra/buscar_ant_gris_16x16.gif" width="16" height="16" />
				</a>
			</div>
			
			<div class="p_bnav_contenedor_3bt">
				<a id="btAgregar" style="width:30px;" title="Agregar Registro" href="javascript:refrescar('<?php echo $directorio; ?>/index.php?controlador=<?php echo $controlador; ?>&accion=agregar', 'contenidoAjaxPrincipal');">
					<img id="imgAgregar" src="imagenes/barra/add_16x16.gif" width="16" height="16" />
				</a>
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
					<img id="imgPrint" src="imagenes/barra/print_gris_16x16.gif" width="16" height="16" />
				</a>
				<a id="btPrintEtiq" title="Imprimir la Etiqueta del Expediente" href="#">
					<img id="imgPrintEtiq" src="imagenes/barra/print_etiq_gris_16x16.gif" width="16" height="16" />
				</a>
			</div>
			
			<div class="p_bnav_contenedor_btSalir">
				<a id="btSalir" title="Volver al listado de Expedientes." href="index.php">
					<img src="imagenes/barra/volver.jpeg" width="17" height="17" />
				</a>
			</div>
				
		</div>
		<?php	
	}	
	
	public function mostrarBarraNavegacionSuperiorEnEdicion_Archivos($url_btGuardar, $url_btCancelar, $titulo)
    {
	?>
		<div id="p_barra_navegacion" class="p_barra_navegacion">
			
			<div class="archivos_titulo_edicion">Editar <?php echo $titulo; ?>&nbsp;</div>
			<div class="p_bnav_contenedor_2bt">
			    <a id="btBG" title="Consulta Gen&eacute;rica" href="#">
					<img id="imgBG" src="imagenes/barra/buscar_gris_16x16.gif" width="16" height="16" />
			    </a>
			    <a id="btBA" title="Buscar por Antecedente" href="#">
					<img id="imgBA" src="imagenes/barra/buscar_ant_gris_16x16.gif" width="16" height="16" />
			    </a>
			</div>
				
			<div class="p_bnav_contenedor_3bt">
			    <a id="btAgregar" style="width:30px;" title="Agregar Registro" href="#">
				    <img id="imgAgregar" src="imagenes/barra/add_gris_16x16.gif" width="16" height="16" />
			    </a>
			</div>
			<div class="p_bnav_contenedor_2bt">
			    <a id="btGuardar" title="Aceptar los cambios realizados" href="<?php echo $url_btGuardar; ?>">
				    <img id="imgGuardar" src="imagenes/barra/ok_16x16.gif" width="16" height="16" />
			    </a>
			    <a id="btCancelar" title="Cancelar los cambios realizados" href="<?php echo $url_btCancelar; ?>">
				    <img id="imgCancelar" src="imagenes/barra/error_16x16.gif" width="16" height="16" />
			    </a>
			</div>
			<div class="p_bnav_contenedor_2bt">
			    <a id="btPrint" title="Imprimir el registro seleccionado" href="#">
				    <img id="imgPrint" src="imagenes/barra/print_gris_16x16.gif" width="16" height="16" />
			    </a>
			    <a id="btPrintEtiq" title="Imprimir la Etiqueta del Expediente" href="#">
				    <img id="imgPrintEtiq" src="imagenes/barra/print_etiq_gris_16x16.gif" width="16" height="16" />
			    </a>
			</div>
		</div>
	<?php
	}
	
	public function nombreMes($mes)
	{
		$meses = Array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

		return $meses[$mes];
	}
	
	public function ordenarArrayMultiDimensionalPorCampo($array_a_ordenar, $campo, $orden_inverso = false)
	{
		$posicion = array();
		$nuevaFila = array();
		
		foreach ($array_a_ordenar as $key => $row)
		{
			$posicion[$key]  = $row[$campo];
			$nuevaFila[$key] = $row;
		}
		
		if ($orden_inverso)
		{
			arsort($posicion);
		}
		else
		{
			asort($posicion);
		}
		
		$returnArray = array();
		
		foreach ($posicion as $key => $pos)
		{     
			$returnArray[] = $nuevaFila[$key];
		}
		
		return $returnArray;
	}
	
	public function reemplazarPorHTML($cadena) 
	{ 
		$cadena = str_replace("á","&aacute;",$cadena); 
		$cadena = str_replace("é","&eacute;",$cadena); 
		$cadena = str_replace("í","&iacute;",$cadena); 
		$cadena = str_replace("ó","&oacute;",$cadena); 
		$cadena = str_replace("ú","&uacute;",$cadena); 
		$cadena = str_replace("ñ","&ntilde;",$cadena); 
		$cadena = str_replace("Á","&Aacute;",$cadena); 
		$cadena = str_replace("É","&Eacute;",$cadena); 
		$cadena = str_replace("Í","&Iacute;",$cadena); 
		$cadena = str_replace("Ó","&Oacute;",$cadena); 
		$cadena = str_replace("Ú","&Uacute;",$cadena); 
		$cadena = str_replace("Ñ","&Ntilde;",$cadena);
		$cadena = str_replace("ü","&uuml;",$cadena);
		$cadena = str_replace("Ü","&Uuml;",$cadena);
		$cadena = str_replace("@","&#64;",$cadena);
		$cadena = str_replace("°","&deg;",$cadena);
		$cadena = str_replace("º","&deg;",$cadena);
		$cadena = str_replace("ª","&deg;",$cadena);
		$cadena = str_replace('"',"&#34;",$cadena);
		
		return $cadena; 
	} 
	
	public function reemplazarComillaDoble($cadena) 
	{ 
		$cadena = str_replace('"',"'",$cadena);
		
		return $cadena; 
	}
	
	public function js_encode($s)
	{
		$texto='';
		$longitud = strlen($s);
		
		for($i=0;$i<$longitud;++$i)
		{
			$num = ord($s[$i]);
			if($num<16) $texto.='\x0'.dechex($num);
			else $texto.='\x'.dechex($num);
		}
		return $texto;
	}
	
	public function obtenerNombrePerfil($perfil)
	{
		switch ($perfil)
		{
			case '1':
				return 'Administrador';
				break;
			case '2':
				return 'Supervisor';
				break;
			case '3':
				return 'Consulta';
				break;
			case '4':
				return 'Consulta Web';
				break;
		}
	}

	/**
	 * Se genera un archivo de log, utilizando la clase Logger.
	 *
	 * Ejemplo: Se guardan los datos de un listado a mostrar en un archivo de Log
	 * $this->LogVista("listado", $listado);
	 *
	 * @param string $identificador
	 * @param string|integer|array $data
	 * @param bool $incremental
	 */
	public function LogVista($identificador, $data, $incremental = true)
	{
		// Se obtiene el nombre de la clase hija, utilizando this como parámetro en el método get_class()
		$nombre_clase_hija = get_class($this);
	
		// Se obtiene un rastreo de PHP
		$backtrace = debug_backtrace();
	
		// Se toma el nombre del método invocado
		$metodo = $backtrace[1]['function'];
	
		Logger::GetInstance()->Log($nombre_clase_hija.'_'.$metodo.'_'.$identificador, $data, $incremental);
	}

	/**
	 * Devuelve el nombre del mes según su número
	 * @param integer $nro_mes
	 * @return Ambigous <string>
	 */
	public function mostrarNombreMes($nro_mes)
	{
		$meses = Array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
	
		return $meses[$nro_mes-1];
	}
	
	/**
	 * Muestra la fecha en letras
	 * @param string $fecha
	 */
	public function mostrarFechaLetras($fecha)
	{
		// Se divide la fecha por cada guión
		$partes_fecha = explode('-', $fecha);
	
		// Se establece el número del día
		$dia = ($partes_fecha[2] < 10) ? substr($partes_fecha[2], 1, 1) : $partes_fecha[2];
	
		// Devuelve la fecha en formato [nro del día] de [nombre del mes] de [nro del año]
		return $dia." de ".$this->mostrarNombreMes($partes_fecha[1])." de ".$partes_fecha[0];
	}

	public function mostrarFechaConNombreDiaCompleto($fecha)
	{
		return $this->obtenerNombreDia($fecha).' '.$this->mostrarFechaLetras($fecha);
	}
	
	public function mostrarDiaEnLetras($nro_dia = '')
	{
		$dias_en_letras = Array("uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve",
								"diez", "once", "doce", "trece", "catorce", "quince", "dieciseis", "diecisiete", "dieciocho", "diecinueve",
								"veinte", "veintiuno", "veintidos", "veintitr&eacute;s", "veinticuatro", "veinticinco", "veintiseis", "veintisiete", 
								"veintiocho", "veintinueve", "treinta", "treinta y uno");
	
		if( $nro_dia == '' )
			return '';
	
		return $dias_en_letras[$nro_dia - 1];
	}
	
	public function mostrarAnioEnLetras($nro_anio = '')
	{
		$anios_en_letras = Array();
		$anios_en_letras['2014'] = "dos mil catorce";
		$anios_en_letras['2015'] = "dos mil quince";
		$anios_en_letras['2016'] = "dos mil dieciseis";
		$anios_en_letras['2017'] = "dos mil diecisiete";
		$anios_en_letras['2018'] = "dos mil dieciocho";
		$anios_en_letras['2019'] = "dos mil diecinueve";
		$anios_en_letras['2020'] = "dos mil veinte";
		$anios_en_letras['2021'] = "dos mil veintiuno";
		$anios_en_letras['2022'] = "dos mil veintidos";
		$anios_en_letras['2023'] = "dos mil veintitres";
		$anios_en_letras['2024'] = "dos mil veinticuatro";
		$anios_en_letras['2025'] = "dos mil veinticinco";
		$anios_en_letras['2026'] = "dos mil veintiseis";
		$anios_en_letras['2027'] = "dos mil veintisiete";
		$anios_en_letras['2028'] = "dos mil veintiocho";
		$anios_en_letras['2029'] = "dos mil veintinueve";
		$anios_en_letras['2030'] = "dos mil treinta";
	
		if( $nro_anio == '' )
			return '';
	
		return $anios_en_letras[$nro_anio];
	}

	/**
	 * Devuelve el número del día que le corresponde en la semana
	 * @param  integer $anio [description]
	 * @param  integer $mes  [description]
	 * @param  integer $dia  [description]
	 * @return integer       Número del día que le corresponde en la semana
	 */
	public function obtenerNumeroDia($anio,$mes,$dia) {
	    return date("w",mktime(0, 0, 0, $mes, $dia, $anio));
	}

	/**
	 * Devuelve el nombre del día en la semana
	 * @param  string $fecha 		Fecha en formato yyyy-mm-dd
	 * @return string $nombre_dia   Nombre del día
	 */
	public function obtenerNombreDia($fecha) {
		// Nombres de días de la semana (0 = domingo, 6 = sabado)
		$nombres_dias = array("Domingo","Lunes","Martes","Mi&eacute;rcoles","Jueves","Viernes","S&aacute;bado");
		
		// Se separa la fecha por su guión
		$partes = explode('-', $fecha);
		
		$anio = $partes[0];
		$mes = $partes[1];
		$dia = $partes[2];
		
		// Se obtiene el número del día en la semana
		$numero_dia_en_semana = $this->obtenerNumeroDia($anio, $mes, $dia);
		
		// Se obtiene el nombre del día, según su número en la semana
		$nombre_dia = $nombres_dias[$numero_dia_en_semana];
		
		return $nombre_dia;
	}

	public function mostrarNombreNumeroDiaActual() {
		return $this->obtenerNombreDia(date("Y-m-d")).' '.date("d");
	}

	public function obtenerNombreMes($numero_mes) {
		// Nombres de Meses
		$nombres_meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

		// Devuelve el nombre del Mes
		return $nombres_meses[$numero_mes-1];
	}

	public function mostrarAnioConMiles($fecha) {
		return number_format(date("Y"), 0, '', '.');
	}

	public function mostrarFechaActualConLetras() {
		return $this->mostrarNombreNumeroDiaActual().' de '.$this->obtenerNombreMes(date("m")).' de '.date("Y");
	}
}
?>

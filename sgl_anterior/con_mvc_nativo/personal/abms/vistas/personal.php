<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaPersonal extends VistaBase
{
	const RUTA_DIRECTORIO_DDJJ = "/var/www/sgl/personal/";
	const RUTA_ARCHIVOS_DDJJ   = "/sgl/personal/";

	private $directorio;
	private $controlador;
    private $formulario;
    private $directorio_fotos;
    private $directorio_ddjj;
    private $directorio_legajos;

    public function __construct()
    {
		$this->directorio       = 'abms';
		$this->controlador      = 'personal';
		$this->formulario       = 'formPersonal';
		$this->directorio_fotos = 'fotos/';
		$this->directorio_ddjj  = 'ddjj/';
		$this->directorio_legajos  = 'legajos/';

		$this->modelo = new personalModel();
	}

	/**
	 * Se muestra el paginador para recorrer el listado según un criterio de búsqueda determinado
	 *
	 * @param string $directorio
	 * @param string $controlador
	 * @param string $accion
	 * @param array $filtro
	 *
	 * @see VistaBase::mostrarPaginador()
	 */
    public function mostrarPaginadorPersonal($directorio, $controlador, $accion, $filtro)
    {
		?>
		<div class="p_bnav_contenedor_4bt">
			<?php
			$criterio_buscador = '&cmb_area='.$filtro['id_area'].'&cmb_cargo='.$filtro['nomenclador'].'&cmb_concejal='.$filtro['concejal'].'&f_legajo='.$filtro['legajo'].'&f_apellido_y_nombre='.$filtro['apellido_y_nombre'].'&f_activos='.$filtro['f_activos'];

			if ($filtro['pagina'] != 1)
			{
			?>
				<a id="btPrimero" title="Primer Registro" href="javascript:refrescar('<?php echo $directorio; ?>/index.php?controlador=<?php echo $controlador; ?>&accion=<?php echo $accion; ?>&campo_orden=<?php echo $_SESSION['ultimo_campo']; ?>&pagina=1&sentido=primero<?php echo $criterio_buscador; ?>', 'contenidoAjaxPrincipal');">
					<img id="imgPrimero" src="imagenes/barra/b_firstpage.png" width="14" height="14" align="top" />
				</a>
			<?php
			}
			else
			{
			?>
				<a id="btPrimero" href="#"> <img id="imgPrimero" src="imagenes/barra/bd_firstpage.png" width="14" height="14" align="top" /></a>
			<?php
			}

			if ($filtro['pagina_ant'] != 0)
			{
			?>
				<a id="btAnterior" title="Registro Anterior" href="javascript:refrescar('<?php echo $directorio; ?>/index.php?controlador=<?php echo $controlador; ?>&accion=<?php echo $accion; ?>&campo_orden=<?php echo $_SESSION['ultimo_campo']; ?>&pagina=<?php echo $filtro['pagina_ant']; ?>&sentido=anterior<?php echo $criterio_buscador; ?>', 'contenidoAjaxPrincipal');">
					<img id="imgAnterior" src="imagenes/barra/b_prevpage.png" width="14" height="14" align="top" />
				</a>
			<?php
			}
			else
			{
			?>
				<a id="btAnterior" href="#"> <img id="imgAnterior" src="imagenes/barra/bd_prevpage.png" width="14" height="14" align="top" /></a>
			<?php
			}

			echo "&nbsp;".$filtro['pagina']." de ".$filtro['nro_paginas']."&nbsp;";

			if ( $filtro['pagina'] != $filtro['nro_paginas'] )
			{
			?>
				<a id="btSiguiente" title="Registro Siguiente" href="javascript:refrescar('<?php echo $directorio; ?>/index.php?controlador=<?php echo $controlador; ?>&accion=<?php echo $accion; ?>&campo_orden=<?php echo $_SESSION['ultimo_campo']; ?>&pagina=<?php echo $filtro['pagina_sgte']; ?>&sentido=siguiente<?php echo $criterio_buscador; ?>', 'contenidoAjaxPrincipal');">
					<img id="imgSiguiente" src="imagenes/barra/b_nextpage.png" width="14" height="14" align="top" />
				</a>
			<?php
			}
			else
			{
			?>
				<a id="btSiguiente" href="#"> <img id="imgSiguiente" src="imagenes/barra/bd_nextpage.png" width="14" height="14" align="top" /></a>
			<?php
			}

			if ($filtro['pagina'] != $filtro['nro_paginas'])
			{
			?>
				<a id="btUltimo" title="Ultimo Registro" href="javascript:refrescar('<?php echo $directorio; ?>/index.php?controlador=<?php echo $controlador; ?>&accion=<?php echo $accion; ?>&campo_orden=<?php echo $_SESSION['ultimo_campo']; ?>&pagina=<?php echo $filtro['nro_paginas']; ?>&sentido=ultimo<?php echo $criterio_buscador; ?>', 'contenidoAjaxPrincipal');">
					<img id="imgUltimo" src="imagenes/barra/b_lastpage.png" width="14" height="14" align="top" />
				</a>
			<?php
			}
			else
			{
			?>
				<a id="btUltimo" href="#"> <img id="imgUltimo" src="imagenes/barra/bd_lastpage.png" width="14" height="14" align="top" /></a>
			<?php
			}
			?>
		</div>
		<?php
	}

	/**
	 * Se listan los legajos pertenecientes a Planta Política y Planta Permanente
	 *
	 * @param array $datos
	 * @param string $mensaje
	 * @param integer $tipo_mensaje
	 * @param array $filtro
	 * @param array $listadoAreas
	 * @param array $listadoCargos
	 * @param array $listadoConcejales
	 */
    public function listar($datos, $mensaje = '', $tipo_mensaje = '', $filtro, $listadoAreas = '', $listadoCargos = '', $listadoConcejales = '')
	{
		// MENSAJE DEL RESULTADO DE LA OPERACION REALIZADA
		$this->mostrarCartelResultado($mensaje, $tipo_mensaje);

		// PARA EL BOTON Cancelar AL EDITAR, SE VUELVE AL LISTADO
		$_SESSION['p_operacion_confirmada'] = "si";
		$_SESSION['mensaje'] = null;
		$_SESSION['tipo_mensaje'] = null;
		?>
		<input type="hidden" name="nombre_archivo" id="nombre_archivo" value="<?= (isset($_SESSION['nombre_archivo'])) ? $_SESSION['nombre_archivo'] : ''; ?>" />
		<?php
		$_SESSION['nombre_archivo'] = null;
		?>
		<script>
			$("capaFondo").setStyle('visibility','hidden');
			$("capaVentana").setStyle('visibility','hidden');

			if ( $('nombre_archivo').value != '' )
			{
				if ( $('nombre_archivo').value == 'bloques' )
				{
					refrescar('informes/index.php?controlador=informes&accion=listarPersonalBloques', 'capaVentana');
				}
				else
				{
					refrescar('informes/index.php?controlador=informes&accion=listarPersonalPlantaPermanente', 'capaVentana');
				}
			}

			se_busca = false;
	    </script>

		<!-- BARRA DE NAVEGACION SUPERIOR -->
		<div id="p_barra_navegacion" class="p_barra_navegacion">
			<div class="p_titulo_listado">:. Personal del HCD</div>
			<?php
			if ($datos)
			{
				// PAGINADOR
				$this->mostrarPaginadorPersonal('abms', 'personal', 'listar', $filtro);
			}

			// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN INGRESAR PERSONAL
			if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
			{
			?>
				<div class="p_margen2_boton_edicion"></div>
				<div class="p_boton_edicion">
					<a title="Nuevo Legajo" href="javascript:refrescar('abms/index.php?controlador=personal&accion=editar', 'contenidoAjaxPrincipal');">
						<img src="imagenes/barra/add_16x16.gif" width="16" height="16" align="top" />&nbsp;Nuevo
					</a>
				</div>
			<?php
			}
			?>

			<?php
			// SI TIENE ACCESO A UN SÓLO SISTEMA SE PERMITE CERRAR LA SESION
			if ( count($_SESSION['accesos']) == 1 )
			{
			?>
				<div class="p_bnav_contenedor_btSalir">
					<a title="Salir del Sistema" href="javascript:if (confirm('Desea salir del Sistema?')){ location.href='../salir.php'; };">
						<img src="imagenes/barra/salir.jpeg" width="60" height="23" align="center" />
					</a>
				</div>
		    <?php
			}
			?>

		</div>
		<div class="p_edicion_datos_borde_superior"></div>
		<div class="ub_cont_buscador p_buscador_alto">

			<input type="hidden" id="nro_paginas"
				value="<?php echo $filtro['nro_paginas']; ?>" />
			<!-- AQUI SE GUARDA EL NOMBRE DEL CAMPO POR EL CUAL SE ORDENA -->
			<input type="hidden" name="campo_orden" id="campo_orden" value="<?php echo $filtro['campo_orden']; ?>" />

			<div class="p_buscador_titulo">Filtros:</div>
			<div class="p_buscador_combo">
				<select id="cmb_area" name="cmb_area">
					<option value="0">:: &Aacute;reas</option>
					<?php
					$cant_areas = (isset($listadoAreas)) ? count($listadoAreas) : 0;
					for ($i=0; $i < $cant_areas; $i++)
					{
						$area = &$listadoAreas[$i];
					?>
						<option value="<?php echo $area['ca_id']; ?>"><?php echo $area['ca_nombre']; ?></option>
					<?php
					}
					?>
				</select>
			</div>
			<div class="p_buscador_combo">
				<select id="cmb_cargo" name="cmb_cargo">
					<option value="0">:: Cargos</option>
					<?php
					$cant_cargos = (isset($listadoCargos)) ? count($listadoCargos) : 0;
					for ($i=0; $i < $cant_cargos; $i++)
					{
						$cargo = &$listadoCargos[$i];
					?>
						<option value="<?php echo $cargo['cc_nomenclador']; ?>"><?php echo $cargo['cc_nombre']; ?></option>
					<?php
					}
					?>
				</select>
			</div>
			<div class="p_buscador_combo">
				<select id="cmb_concejal" name="cmb_concejal">
					<option value="0">:: Concejales</option>
					<?php
					$cant_concejales = (isset($listadoConcejales)) ? count($listadoConcejales) : 0;
					for ($i=0; $i < $cant_concejales; $i++)
					{
						$concejal = &$listadoConcejales[$i];
					?>
						<option value="<?php echo $concejal['p_legajo']; ?>"><?php echo $concejal['p_apellido'].', '.$concejal['p_nombre']; ?></option>
					<?php
					}
					?>
				</select>
			</div>
			<div class="p_boton_edicion izquierda">
				<a title="Limpiar Combos" href="javascript:refrescar('abms/index.php?controlador=personal&accion=listar&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');">
					<img src="imagenes/barra/limpiar.png" width="16" height="16" align="top" />&nbsp;Limpiar
				</a>
			</div>
		</div>
		<div class="p_edicion_datos_borde_superior"></div>
		<div class="ub_cont_buscador p_buscador_alto">
			<div class="p_buscador_legajo">
				Legajo: <input type="text" name="f_legajo" id="f_legajo" value="<?php echo ($_SESSION['filtro_personal']['legajo']) ? $_SESSION['filtro_personal']['legajo'] : ''; ?>" onKeyPress="return soloEnteros(event)" />
			</div>
			<div class="p_buscador_apellido_nombre">
				Apellido y Nombres: <input type="text" name="f_apellido_y_nombre" id="f_apellido_y_nombre" value="<?php echo ($_SESSION['filtro_personal']['apellido_y_nombre']) ? $_SESSION['filtro_personal']['apellido_y_nombre'] : ''; ?>" onKeyPress="return soloEnterosLetrasComilla(event)" />
			</div>
			<div class="p_buscador_activos">
				<input type="hidden" name="f_activos" id="f_activos" value="<?php echo ($_SESSION['filtro_personal']['f_activos']) ? $_SESSION['filtro_personal']['f_activos'] : '0'; ?>" />
				S&oacute;lo Activos<input type="checkbox" name="chk_activos" id="chk_activos" <?php echo ( $_SESSION['filtro_personal']['f_activos'] == 1 ) ? "checked" : ""; ?> />
			</div>
			<div class="p_boton_edicion izquierda">
				<a title="Buscar" href="javascript:buscarLegajo();"> <img
					src="imagenes/barra/zoom_16x16.gif" width="16" height="16"
					align="top" />&nbsp;Buscar
				</a>
			</div>
			<div class="p_buscador_margen_boton"></div>
			<div class="p_boton_edicion izquierda">
				<a title="Limpiar" href="javascript:limpiarCamposBuscador();"> <img
					src="imagenes/barra/limpiar.png" width="16" height="16" align="top" />&nbsp;Limpiar
				</a>
			</div>
		</div>
		<div class="ub_listado">
			<?php
			if ($datos)
			{
				// Cantidad total de legajos
				$cantidad = (isset($datos)) ? count($datos) : 0;
			?>
				<input type="hidden" id="controlador" value="personal" />
				<input type="hidden" id="cantidad" value="<?php echo $cantidad; ?>" />
				<input type="hidden" id="pagina" value="<?php echo $filtro['pagina']; ?>" />
				<input type="hidden" id="nroFila_elegida" value="" />

				<table class="e_tabla_texto" width="100%">
					<thead class="e_tabla_titulos">
						<tr>
							<?php
							if ( $_SESSION['perfil3'] == 1 ) // Sólo el perfil 1 puede eliminar
								echo '<th class="orden_link" width="64" colspan="4">&nbsp;</th>';
							elseif ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 ) // Sólo los perfiles 1 y 2 pueden modificar
								echo '<th class="orden_link" width="48" colspan="3">&nbsp;</th>';
							?>
							<th nowrap class="orden_link">
								<a title="Ordenar por legajo" href="javascript:ordenarColumnaLegajos('p_legajo', 'personal', <?php echo $_SESSION['filtro_personal']['pagina']; ?>, <?php echo ($_SESSION['filtro_personal']['id_area']) ? $_SESSION['filtro_personal']['id_area'] : 0; ?>, <?php echo ($_SESSION['filtro_personal']['nomenclador']) ? $_SESSION['filtro_personal']['nomenclador'] : 0; ?>, <?php echo ($_SESSION['filtro_personal']['concejal']) ? $_SESSION['filtro_personal']['concejal'] : 0; ?>);">
									Legajo&nbsp;<?php if($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'p_legajo'){ echo '<img src="imagenes/s_desc.png" width="11" height="9" align="top" >'; }else{ echo '<img src="imagenes/s_asc.png" width="11" height="9" align="top" >'; } ?>
								</a>
							</th>
							<th nowrap class="orden_link">D&iacute;gito</th>
							<th nowrap class="orden_link">
								<a title="Ordenar por apellido" href="javascript:ordenarColumnaLegajos('p_apellido', 'personal', <?php echo $_SESSION['filtro_personal']['pagina']; ?>, <?php echo ($_SESSION['filtro_personal']['id_area']) ? $_SESSION['filtro_personal']['id_area'] : 0; ?>, <?php echo ($_SESSION['filtro_personal']['nomenclador']) ? $_SESSION['filtro_personal']['nomenclador'] : 0; ?>, <?php echo ($_SESSION['filtro_personal']['concejal']) ? $_SESSION['filtro_personal']['concejal'] : 0; ?>);">
									Apellido&nbsp;<?php if($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'p_apellido'){ echo '<img src="imagenes/s_desc.png" width="11" height="9" align="top" >'; }else{ echo '<img src="imagenes/s_asc.png" width="11" height="9" align="top" >'; } ?>
								</a>
							</th>
							<th nowrap class="orden_link">
								<a title="Ordenar por nombre" href="javascript:ordenarColumnaLegajos('p_nombre','personal', <?php echo $_SESSION['filtro_personal']['pagina']; ?>, <?php echo ($_SESSION['filtro_personal']['id_area']) ? $_SESSION['filtro_personal']['id_area'] : 0; ?>, <?php echo ($_SESSION['filtro_personal']['nomenclador']) ? $_SESSION['filtro_personal']['nomenclador'] : 0; ?>, <?php echo ($_SESSION['filtro_personal']['concejal']) ? $_SESSION['filtro_personal']['concejal'] : 0; ?>);">
									Nombre&nbsp;<?php if($_SESSION['ultimo_sentido'] == 'asc' && $_SESSION['ultimo_campo'] == 'p_nombre'){ echo '<img src="imagenes/s_desc.png" width="11" height="9" align="top" >'; }else{ echo '<img src="imagenes/s_asc.png" width="11" height="9" align="top" >'; } ?>
								</a>
							</th>
							<th nowrap class="orden_link">&Aacute;rea</th>
							<th nowrap class="orden_link">Cargo</th>
							<th nowrap class="orden_link">Depende de</th>
							<th nowrap class="orden_link">Secretar&iacute;a</th>
							<th nowrap class="orden_link">Tel&eacute;fono</th>
							<th nowrap class="orden_link">Celular</th>
							<th nowrap class="orden_link">Activo</th>
						</tr>
					</thead>
					<tbody id="e_cuerpo_scrolleable" class="e_cuerpo_scrolleable">
						<?php
						for ($i=0; $i < $cantidad; $i++)
						{
							$dato = &$datos[$i];

							$area = $this->modelo->obtenerNombreUltimaArea($dato['p_legajo']);

							$cargo = $this->modelo->obtenerNombreUltimoCargo($dato['p_legajo']);

							$depende_de = $this->modelo->obtenerDependeDe($dato['p_legajo']);

							$activo = $this->modelo->estaActivo($dato['p_legajo']);
						?>
							<tr id="e_fila<?php echo $i; ?>" <?php echo ($activo) ? '' : 'style="color: #A6ABAB;"'; ?> onmouseover="javascript:resaltarFila(<?php echo $i; ?>);" onmouseout="javascript:no_resaltarFila(<?php echo $i; ?>);" onclick="javascript:remarcarFila(<?php echo $i; ?>);" onDblClick="javascript:refrescar('abms/index.php?controlador=personal&accion=editar&legajo=<?php echo $dato['p_legajo']; ?>&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');">
								<a name="tr<?php echo $i; ?>" style="display: none;"></a>

								<?php
								// Sólo los perfiles 1 y 2 pueden modificar
								if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 ) {
								?>
									<td width="16">
										<a style="width: 16px; height: 16px; display: block;" title="Editar Legajo" href="javascript:refrescar('abms/index.php?controlador=personal&accion=editar&legajo=<?php echo $dato['p_legajo']; ?>&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');">
											<img src="imagenes/b_edit.png" width="14" height="14" align="top" />
										</a>
									</td>
								<?php
								}

								// Sólo el perfil 1 puede eliminar
								if ( $_SESSION['perfil3'] == 1 ) {
								?>
									<td width="16">
										<a style="width: 16px; height: 16px; display: block;" title="Eliminar Legajo" href="javascript:if (confirm('Desea dar de baja a <?php echo $dato['p_apellido']; ?>, <?php echo $dato['p_nombre']; ?>?')){ refrescar('abms/index.php?controlador=personal&accion=eliminar&legajo=<?php echo $dato['p_legajo']; ?>', 'contenidoAjaxPrincipal'); };">
											<img src="imagenes/b_drop.png" width="14" height="14" align="top" />
										</a>
									</td>
								<?php
								}

								// Sólo los perfiles 1 y 2 pueden generar Credenciales y Certificados
								if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 ) {
								?>
									<td width="16">
										<?php
										// Permite generar la Credencial sólo si el legajo está activo y es un Concejal o Secretario/a del HCD
										// 18/07/2019: se agrega la condición si es Defensor del Pueblo
										if ( $activo && ( $cargo['c_nomenclador'] == $this->modelo->obtenerIdCargoConcejal() || $cargo['c_nomenclador'] == $this->modelo->obtenerIdCargoSecretarioHCD() || $cargo['c_nomenclador'] == $this->modelo->obtenerIdCargoDefensorPueblo() || $cargo['c_nomenclador'] == $this->modelo->obtenerIdCargoDefensorPuebloCoordinador() ) ) {
										?>
											<a style="width: 16px; height: 16px; display: block;" title="Generar Credencial" href="javascript:refrescar('informes/index.php?controlador=credenciales&accion=pedirInfoParaCredencial&legajo=<?php echo $dato['p_legajo']; ?>', 'capaVentana');">
												<img src="imagenes/icono_credencial_chico.jpg" width="15" height="15" align="top" />
											</a>
										<?php
										}
										?>
									</td>
									<td width="16">
										<?php
										// Permite generar el Certificado de Trabajo sólo si el legajo está activo
										if ( $activo ) {
										?>
											<a style="width: 16px; height: 16px; display: block;" title="Generar Certificado de Trabajo" href="javascript:refrescar('informes/index.php?controlador=credenciales&accion=pedirInfoParaCertificadoTrabajo&legajo=<?php echo $dato['p_legajo']; ?>', 'capaVentana');">
												<img src="imagenes/icono_certificado_laboral.png" width="15" height="15" align="top" />
											</a>
										<?php
										}
										?>
									</td>
								<?php
								}
								?>
								<td id="p_legajo<?php echo $i; ?>" nowrap style="width: 30px; text-align: right; padding: 0 3px 0 3px;"><?php echo ($dato['p_legajo']) ? number_format($dato['p_legajo'], 0, '', '.') : '&nbsp;'; ?></td>
								<td id="p_digito<?php echo $i; ?>" nowrap style="text-align: right; padding: 0 3px 0 3px;"><?php echo ($cargo['c_digito']) ? $cargo['c_digito'] : '&nbsp;'; ?></td>
								<td id="p_apellido<?php echo $i; ?>" nowrap style="text-align: left; padding: 0 3px 0 3px;"><?php echo ($dato['p_apellido']) ? $dato['p_apellido'] : '&nbsp;'; ?></td>
								<td id="p_nombre<?php echo $i; ?>" nowrap style="text-align: left; padding: 0 3px 0 3px;"><?php echo ($dato['p_nombre']) ? $dato['p_nombre'] : '&nbsp;'; ?></td>
								<td id="p_area<?php echo $i; ?>" nowrap style="text-align: left; padding: 0 3px 0 3px;"><?php echo ($area['area']) ? $area['area'] : '&nbsp;'; ?></td>
								<td id="p_cargo<?php echo $i; ?>" nowrap style="text-align: left; padding: 0 3px 0 3px;"><?php echo ($cargo['cargo']) ? $cargo['cargo'] : '&nbsp;' ?></td>
								<td id="p_depende_de<?php echo $i; ?>" nowrap style="text-align: left; padding: 0 3px 0 3px;"><?= (isset($depende_de[0]['p_apellido'])) ? $depende_de[0]['p_apellido'].", ".$depende_de[0]['p_nombre'] : '&nbsp;'; ?></td>
								<td id="p_pertenece_secretaria_bloque<?php echo $i; ?>" nowrap style="text-align: center; padding: 0 3px 0 3px;"><?php if ( $cargo['c_pertenece_secretaria_bloque'] == '1' ){ echo 'Si'; }else{ echo 'No'; } ?></td>
								<td id="p_telefono_real<?php echo $i; ?>" nowrap style="width: 70px; text-align: right; padding: 0 3px 0 3px;"><?php echo ($dato['p_telefono_real']) ? $dato['p_telefono_real'] : '&nbsp;'; ?></td>
								<td id="p_celular_real<?php echo $i; ?>" nowrap style="width: 70px; text-align: right; padding: 0 3px 0 3px;"><?php echo ($dato['p_celular_real']) ? $dato['p_celular_real'] : '&nbsp;'; ?></td>
								<td style="text-align: center; padding: 0 3px 0 3px;"><input type="hidden" id="bandera_habilitado<?php echo $i; ?>" value="<?php echo ($activo) ? '1' : '0'; ?>"><?php echo ($activo) ? '<img src="imagenes/barra/ok_16x16.gif" width="12" height="12" align="top" />' : ''; ?></td>
							</tr>
						<?php
						}
						$posicion_en_el_listado = $i-1; // POR DEFECTO
						if ($filtro['por_teclado']=='arriba'){ $posicion_en_el_listado = $i-1; } // PARA VER LA PAGINA ANTERIOR
						if ($filtro['por_teclado']=='abajo'){ $posicion_en_el_listado = 0; } // PARA VER LA PAGINA SIGUIENTE
						?>
					</tbody>
				</table>
			<?php
			}
			else
				echo $this->mostrarCartelResultado("Sin resultados", 1);
			?>
		</div>
		<script>
			<?php
			if ($datos) {
			?>
				//SE MARCA EL ULTIMO REGISTRO DEL LISTADO
				$('e_fila<?php echo $posicion_en_el_listado; ?>').setStyle('background-color','#76A0CD');
				$('e_fila<?php echo $posicion_en_el_listado; ?>').setStyle('color','#fff');
				$('nroFila_elegida').value = <?php echo $posicion_en_el_listado; ?>;
				location.href ="#tr<?php echo $posicion_en_el_listado; ?>";
			<?php
			}
			?>

			// SE SETEA EL AREA FILTRADA
			$('cmb_area').value = '<?php echo ($_SESSION['filtro_personal']['id_area']) ? $_SESSION['filtro_personal']['id_area'] : 0; ?>';

			// SE SETEA EL CARGO FILTRADO
			$('cmb_cargo').value = '<?php echo ($_SESSION['filtro_personal']['nomenclador']) ? $_SESSION['filtro_personal']['nomenclador'] : 0; ?>';

			// SE SETEA EL CONCEJAL FILTRADO
			$('cmb_concejal').value = '<?php echo ($_SESSION['filtro_personal']['concejal']) ? $_SESSION['filtro_personal']['concejal'] : 0; ?>';

			// AL CARGARSE EL COMBO DE Areas
			$('cmb_area').addEvent('domready', function() {
				// SI HAY SELECCIONADA UN AREA Y NINGUN CARGO
				if ( $('cmb_area').value != '0' && $('cmb_cargo').value == '0' ) {
					// SE REFREZCA EL COMBO DE CARGOS, SEGUN EL TIPO DE AREA (PERMANENTE O POLITICA)
					refrescar('abms/index.php?controlador=personal&accion=refrescarComboCargos&cmb_area='+$('cmb_area').value+'', 'cmb_cargo');
				}
			});

			// AL PRODUCIRSE UN CAMBIO EN EL COMBO DE Areas
			$('cmb_area').addEvent('change', function() {
				// SE REFREZCA EL COMBO DE CARGOS, SEGUN EL TIPO DE AREA (PERMANENTE O POLITICA)
				refrescar('abms/index.php?controlador=personal&accion=refrescarComboCargos&cmb_area='+$('cmb_area').value+'', 'cmb_cargo');

				// SE FILTRA SEGUN EL CRITERIO DE BUSQUEDA
				buscarLegajo();
			});

			// AL CARGARSE EL COMBO DE Cargos
			$('cmb_cargo').addEvent('domready', function() {
				// SI HAY SELECCIONADO UN CARGO Y NINGUN CONCEJAL
				if ( $('cmb_cargo').value != '0' && $('cmb_concejal').value == '0' ) {
					// SI ES DE AREA POLITICA
					if ( $('cmb_area').value.substring(0,2) == '02' )
						// SE REFREZCA EL COMBO DE CONCEJALES
						refrescar('abms/index.php?controlador=personal&accion=refrescarComboConcejales&cmb_area='+$('cmb_area').value+'&f_activos='+$('f_activos').value, 'cmb_concejal');
					else
						// SE LIMPIA EL COMBO DE CONCEJALES
						$('cmb_concejal').value = '0';
				}
			});

			// AL PRODUCIRSE UN CAMBIO EN EL COMBO DE Cargos
			$('cmb_cargo').addEvent('change', function() {
				// SE FILTRA SEGUN EL CRITERIO DE BUSQUEDA
				buscarLegajo();
			});

			// AL PRODUCIRSE UN CAMBIO EN EL COMBO DE Concejales
			$('cmb_concejal').addEvent('change', function() {
				// SE FILTRA SEGUN EL CRITERIO DE BUSQUEDA
				buscarLegajo();
			});

			$('f_legajo').addEvents({
				click: function(){
					se_busca = true;
				},
				keydown: function(event){
					if(event.key == 'Enter')
						if( $('f_legajo').value != '' )
							buscarLegajo();
				}
			});

			$('f_apellido_y_nombre').addEvents({
				click: function(){
					se_busca = true;
				},
				keydown: function(event){
					if(event.key == 'Enter')
						if( $('f_apellido_y_nombre').value != '' )
							buscarLegajo();
				}
			});

			$('chk_activos').addEvent('change', function() {
				$('f_activos').value = ( $('chk_activos').checked == true ) ? 1 : 0;
				buscarLegajo();
			});

			// AL CLIQUEAR SOBRE Limpiar
			function limpiarCamposBuscador() {
				refrescar('abms/index.php?controlador=personal&accion=listar&cmb_area='+$('cmb_area').value+'&cmb_cargo='+$('cmb_cargo').value+'&cmb_concejal='+$('cmb_concejal').value+'&pagina=<?php echo $filtro['pagina']; ?>', 'contenidoAjaxPrincipal');
			}
		</script>
	<?php
    }

	public function comboCargos($listadoCargos)
    {
    	$cant_cargos = (isset($listadoCargos)) ? count($listadoCargos) : 0;
		?>
		<option value="0">:: Cargos</option>
		<?php
		for ($i=0; $i < $cant_cargos; $i++) {
			$cargo = &$listadoCargos[$i];
			echo '<option value="'.$cargo['cc_nomenclador'].'">'.$cargo['cc_nombre'].'</option>';
		}
		?>
		<script type="text/javascript">
			$('cmb_cargo').value = '<?php echo ($_SESSION['filtro_personal']['nomenclador']) ? $_SESSION['filtro_personal']['nomenclador'] : 0; ?>';
		</script>
		<?php
    }

	public function comboConcejales($listadoConcejales)
    {
    	$cant_concejales = (isset($listadoConcejales)) ? count($listadoConcejales) : 0;
		?>
		<option value="0">:: Concejales</option>
		<?php
		for ($i=0; $i < $cant_concejales; $i++) {
			$concejal = &$listadoConcejales[$i];
			echo '<option value="'.$concejal['p_legajo'].'">'.$concejal['p_apellido'].', '.$concejal['p_nombre'].'</option>';
		}
		?>
		<script type="text/javascript">
			$('cmb_concejal').value = '<?php echo ($_SESSION['filtro_personal']['concejal']) ? $_SESSION['filtro_personal']['concejal'] : 0; ?>';
		</script>
		<?php
    }

    /**
     * Se edita una solapa determinada, por defecto la solapa de Datos Personales
     *
     * @param integer $legajo
     * @param integer $pagina
     * @param string $mensaje
     * @param string $tipo_mensaje
     * @param string $accion_para_solapa
     */
    public function editar($legajo = 0, $pagina = 0, $mensaje = '', $tipo_mensaje = '', $accion_para_solapa = '')
    {
		// MENSAJE DEL RESULTADO DE LA OPERACION REALIZADA
		$this->mostrarCartelResultado($mensaje, $tipo_mensaje);
		?>
		<script>
			$("capaFondo").setStyle('visibility','hidden');
			$("capaVentana").setStyle('visibility','hidden');
			ocultarCapa('l_contenedor_buscador_ofuscado');
	    </script>

		<input type="hidden" id="accion_para_solapa" name="accion_para_solapa" value="<?php echo ($accion_para_solapa != '') ? $accion_para_solapa : 'editarFicha'; ?>" />

		<div id="precarga_modal" style="display: none"></div>
		<div id="contenidoAjaxEdicion">
			<!-- AQUI SE MUESTRA EL CONTENIDO PARA EDITAR -->
		</div>
		<script>
			// Si se desconoce la solapa a mostrar, por defecto muestra la solapa de Datos Personales del legajo determinado
			refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion='+$('accion_para_solapa').value+'&legajo=<?php echo $legajo; ?>&pagina=<?php echo $pagina; ?>', 'contenidoAjaxEdicion');
		</script>
		<?php
    }

    /**
     * Se simula una ventana modal para la carga de la foto, se embebe el html respectivo en un iframe, para poder utilizar jQuery
     *
     * @param array $parametros, para parametrizar el nombre de la foto y el legajo correspondiente
     */
    public function mostrarIframe($p_legajo, $p_foto)
    {
    ?>
		<script type="text/javascript">
			$("capaFondo").setStyle('visibility','visible');
			$("capaVentana").setStyle('visibility','visible');
		</script>
		<div id="precarga_modal" style="display: none"></div>
		<div id="contenidoAjaxCargaFoto" class="modal_edicion_foto_con_iframe">
			<div id="fade" class="overlay"></div>
			<div id="light" class="modal"></div>
			<iframe id="iframeParaSubirFoto" width="650" height="445" style="border:0" src="abms/upload_crop.php?legajo=<?php echo $p_legajo; ?>&nombre_foto_carnet=<?php echo $p_foto; ?>" ></iframe>
		</div>
	<?php
    }

    /**
     * Se editan los Datos Personales del legajo
     *
     * @param array $datos
     */
    public function editarFicha($datos = '', $mensaje = '', $tipo_mensaje = '')
    {
    	// MENSAJE DEL RESULTADO DE LA OPERACION REALIZADA
    	$this->mostrarCartelResultado($mensaje, $tipo_mensaje);
	?>
		<script>
			$("capaFondo").setStyle('visibility','hidden');
			$("capaVentana").setStyle('visibility','hidden');

			se_busca = false;
	    </script>

		<div class="p_buscador_legajo_solapas">
			<div class="p_buscador_legajo_solapas_titulo_e_input">
				Buscar por otro Legajo: <input type="text" name="f_legajo_solapa_datos" id="f_legajo_solapa_datos" value="" onKeyPress="return soloEnteros(event)" />
			</div>
			<div class="p_margen2_boton_edicion"></div>
			<div class="p_boton_edicion">
				<a href="javascript:verificarExistenciaLegajo();">
					<img id="p_img_titulo_cancelar_volver" src="imagenes/barra/error_16x16.gif" width="15" height="15" align="top" />&nbsp;<span id="p_titulo_cancelar_volver">Cancelar</span>
				</a>
			</div>
			<?php
			// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN INGRESAR REGISTROS
			if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 ) {
			?>
				<div class="p_boton_edicion" id="contenedora_btGuardar" style="margin-right: 20px;">
					<a id="btGuardar" title="Guardar" href="javascript:validarPersonal();" class="boton_en_edicion" tabindex="51">
						<img src="imagenes/barra/ok_16x16.gif" width="15" height="15" align="top" />&nbsp;Guardar
					</a>
				</div>
			<?php
			}
			?>
		</div>
		<!-- SOLAPAS -->
		<div class="p_solapas">
			<ul>
				<li id="p_solapa_Datos" class="actual">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarFicha&legajo='+$('f_legajo_solapa_datos').value, 'contenidoAjaxEdicion');"><span>Datos Personales</span></a>
				</li>
				<li id="p_solapa_GrupoFamiliar" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarFamilia&legajo='+$('f_legajo_solapa_datos').value, 'contenidoAjaxEdicion');"><span>Grupo Familiar</span></a>
				</li>
				<li id="p_solapa_Estudios" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarEstudios&legajo='+$('f_legajo_solapa_datos').value, 'contenidoAjaxEdicion');"><span>Estudios</span></a>
				</li>
				<li id="p_solapa_ExperienciaLaboral" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarAntecedentesLaborales&legajo='+$('f_legajo_solapa_datos').value, 'contenidoAjaxEdicion');"><span>Experiencia Laboral</span></a>
				</li>
				<li id="p_solapa_DDJJ" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarDDJJ&legajo='+$('f_legajo_solapa_datos').value, 'contenidoAjaxEdicion');"><span>Dec. Juradas</span></a>
				</li>
				<li id="p_solapa_Legajos" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarLegajos&legajo='+$('f_legajo_solapa_datos').value, 'contenidoAjaxEdicion');"><span>Legajo</span></a>
				</li>
				<li id="p_solapa_Areas" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarAreas&legajo='+$('f_legajo_solapa_datos').value, 'contenidoAjaxEdicion');"><span>&Aacute;reas</span></a>
				</li>
				<li id="p_solapa_Cargos" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarCargos&legajo='+$('f_legajo_solapa_datos').value, 'contenidoAjaxEdicion');"><span>Cargos</span></a>
				</li>
			</ul>
		</div>
		<form action="abms/index.php" method="post" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">

			<input type="hidden" name="se_sigue_editando" value="no" />
			<input type="hidden" id="directorio" name="directorio" value="<?php echo $this->directorio; ?>" />
			<input type="hidden" id="controlador" name="controlador" value="<?php echo $this->controlador; ?>" />
			<?php
			// SI ESTA DEFINIDA LA ACCION, ES PORQUE SE VOLVIO AL FORMULARIO POR UN ERROR PREVIO
			if ( isset($datos['accion']) )
				$nombre_accion = $datos['accion'];
			elseif ( isset($datos['p_legajo']) ) // SI EXISTE EL REGISTRO, SE MODIFICA
				$nombre_accion = 'modificar';
			else // SINO SE INSERTA
				$nombre_accion = 'insertar';
			?>
			<input type="hidden" id="accion" name="accion" value="<?php echo $nombre_accion; ?>" />
			<input type="hidden" id="pagina" name="pagina" value="<?php echo (isset($datos['pagina'])) ? $datos['pagina'] : ''; ?>" />
			<input type="hidden" id="p_foto" name="p_foto" value="<?php echo $datos['p_foto']; ?>" />
			<!-- Información Personal -->
			<div class="p_edicion_datos">
				<div class="p_edicion_datos_titulo_leyenda degradado">
					Informaci&oacute;n Personal&nbsp;
					<a href="#" id="toggle_info_personal" title="Ocultar">
						<img id="img_mostrar_ocultar_info_personal" src="imagenes/barra/subir.gif" width="14" height="12">
					</a>
				</div>
				<div id="panel_info_personal">
					<div class="p_edicion_ficha_parte_uno">
						<div id="contenedor_foto_carnet" class="p_edicion_ficha_foto">
							<div class="p_edicion_ficha_contenedor_foto_carnet">
								<a id="p_edicion_link_foto" href="javascript:subirFotoPorIframe();" title="Editar Foto">
									<img id="contenedora_foto_legajo" src="<?php echo $this->directorio_fotos; ?><?php echo ($datos['p_foto']) ? utf8_decode($datos['p_foto']).'?'.date("H:i:s") : 'avatar.png'; ?>" />
								</a>
							</div>
							<div id="btBorrarFotoActual" class="p_edicion_foto_actual_boton">
								<a title="Borrar foto actual" href="javascript:borrarFotoActual();" >
									&nbsp;<img src="imagenes/barra/delete_16x16.gif" width="15" height="15" align="top" />&nbsp;Borrar
								</a>
							</div>
						</div>
						<div class="p_edicion_ficha_parte_uno_datos">
							<div class="p_edicion_fila">
								<div class="p_edicion_datos_titulo p_edicion_datos_titulo_ancho80">&nbsp;&nbsp;&nbsp;&nbsp;Legajo:</div>
								<div class="p_edicion_datos_valor">
									&nbsp;<input type="text" name="p_legajo" id="p_legajo" value="<?php echo $datos['p_legajo']; ?>" style="width: 50px;" maxlength="5" onKeyPress="return soloEnteros(event)" tabindex="1" />
								</div>
							</div>
							<div class="p_edicion_fila">
								<div class="p_edicion_datos_titulo p_edicion_datos_titulo_ancho80">&nbsp;&nbsp;&nbsp;&nbsp;Apellido/s:</div>
								<div class="p_edicion_datos_valor">
									&nbsp;<input type="text" name="p_apellido" id="p_apellido" value="<?php echo $datos['p_apellido']; ?>" size="17" onKeyPress="return soloEnterosLetrasComilla(event)" tabindex="2" />
								</div>
								<div class="p_edicion_datos_titulo">&nbsp;Nombre/s:</div>
								<div class="p_edicion_datos_valor">
									<input type="text" name="p_nombre" id="p_nombre" value="<?php echo $datos['p_nombre']; ?>" style="width:178px;" onKeyPress="return soloEnterosLetrasComilla(event)" tabindex="3" />
								</div>
							</div>
							<div class="p_edicion_fila">
								<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Tipo de Doc.:</div>
								<div class="p_edicion_datos_valor">
									<input type="radio" name="p_tipo_documento" id="op_lc" value="LC" <?php echo (isset($datos['p_tipo_documento']) && $datos['p_tipo_documento'] == 'LC') ? 'checked' : ''; ?> tabindex="4" />&nbsp;LC
									<input type="radio" name="p_tipo_documento" id="op_le" value="LE" <?php echo (isset($datos['p_tipo_documento']) && $datos['p_tipo_documento'] == 'LE') ? 'checked' : ''; ?> tabindex="5" />&nbsp;LE
									<input type="radio" name="p_tipo_documento" id="op_dni" value="DNI" <?php echo (isset($datos['p_tipo_documento']) && $datos['p_tipo_documento'] == 'DNI') ? 'checked' : ''; ?> tabindex="6" checked />&nbsp;DNI
								</div>
								<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;N&deg; de Documento:</div>
								<div class="p_edicion_datos_valor">
									<input type="text" name="p_nro_documento" id="p_nro_documento" value="<?php echo $datos['p_nro_documento']; ?>" size="7" maxlength="8" onKeyPress="return soloEnteros(event);" tabindex="7" /> (sin punto)
								</div>
								<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CUIL:</div>
								<div class="p_edicion_datos_valor">
									<input type="text" name="p_cuil" id="p_cuil" value="<?= $datos['p_cuil']; ?>" size="10" maxlength="12" onKeyPress="return soloEnteros(event);" tabindex="8" /> (sin guiones)
								</div>
							</div>
							<div class="p_edicion_fila">
								<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Fecha de Ingreso a Planta Pol&iacute;tica:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
								<div class="p_edicion_datos_valor">
									<input type="text" name="p_fecha_ingreso_planta_politica" id="p_fecha_ingreso_planta_politica" value="<?php echo ($datos['p_fecha_ingreso_planta_politica']) ? $this->formatearFecha($datos['p_fecha_ingreso_planta_politica']) : ''; ?>" style="width: 80px;" maxlength="10" onkeyup="mascara(this,'/',patron,true);" tabindex="9" />
									<input type="image" id="img_p_fecha_ingreso_planta_politica" src="imagenes/calendario/calendario.gif" alt="Presione aqui para seleccionar la fecha." align="top" width="16" height="16"> (DDMMAAAA)
								</div>
							</div>
							<div class="p_edicion_fila">
								<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Fecha de Ingreso a Planta Permanente:</div>
								<div class="p_edicion_datos_valor">
									<input type="text" name="p_fecha_ingreso_planta_permanente" id="p_fecha_ingreso_planta_permanente" value="<?php echo ($datos['p_fecha_ingreso_planta_permanente']) ? $this->formatearFecha($datos['p_fecha_ingreso_planta_permanente']) : ''; ?>" style="width: 80px;" maxlength="10" onkeyup="mascara(this,'/',patron,true);" tabindex="10" />
									<input type="image" id="img_p_fecha_ingreso_planta_permanente" src="imagenes/calendario/calendario.gif" alt="Presione aqui para seleccionar la fecha." align="top" width="16" height="16"> (DDMMAAAA)
								</div>
							</div>

							<div class="p_edicion_fila">
								<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Mail para Notificaciones:</div>
								<div class="p_edicion_datos_valor">
									<input type="text" name="p_mail" id="p_mail" value="<?php echo ($datos['p_mail']) ? $datos['p_mail'] : ''; ?>" style="width:315px;" tabindex="11" />
								</div>
							</div>
						</div>
					</div>
					<div class="p_edicion_fila p_edicion_ficha_borde_superior">
						<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Sexo:</div>
						<div class="p_edicion_datos_valor">
							<input type="radio" name="p_sexo" id="op_M" value="M" <?php echo (isset($datos['p_sexo']) && $datos['p_sexo'] == 'M') ? 'checked' : ''; ?> tabindex="12" />&nbsp;Masculino
							<input type="radio" name="p_sexo" id="op_F" value="F" <?php echo (isset($datos['p_sexo']) && $datos['p_sexo'] == 'F') ? 'checked' : ''; ?> tabindex="13" />&nbsp;Femenino
						</div>
					</div>
					<div class="p_edicion_fila p_edicion_ficha_borde_inferior">
						<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Grupo Sangu&iacute;neo:</div>
						<div class="p_edicion_datos_valor">
							<select name="p_grupo_sanguineo" id="p_grupo_sanguineo"
								tabindex="14">
								<option value="0">0</option>
								<option value="A">A</option>
								<option value="B">B</option>
								<option value="AB">AB</option>
							</select>
						</div>
						<div class="p_edicion_datos_titulo">&nbsp;&nbsp;R.H.:</div>
						<div class="p_edicion_datos_valor">
							<input type="radio" name="p_factor_sanguineo" id="op_RH_positivo" value="+" <?php echo (isset($datos['p_factor_sanguineo']) && $datos['p_factor_sanguineo'] == '+') ? 'checked' : ''; ?> tabindex="15" />&nbsp;+
							<input type="radio" name="p_factor_sanguineo" id="op_RH_negativo" value="-" <?php echo (isset($datos['p_factor_sanguineo']) && $datos['p_factor_sanguineo'] == '-') ? 'checked' : ''; ?> tabindex="16" />&nbsp;-
						</div>
					</div>
					<div class="p_edicion_fila p_edicion_ficha_borde_superior">
						<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Fecha de Nacimiento:</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_fecha_nac" id="p_fecha_nac" value="<?php echo ($datos['p_fecha_nac']) ? $this->formatearFecha($datos['p_fecha_nac']) : ''; ?>" style="width: 80px;" maxlength="10" onkeyup="mascara(this,'/',patron,true);" tabindex="17" />
							<input type="image" id="img_p_fecha_nac" src="imagenes/calendario/calendario.gif" alt="Presione aqui para seleccionar la fecha." align="top" width="16" height="16" /> (DDMMAAAA)
						</div>
						<div class="p_edicion_datos_titulo">
							&nbsp;&nbsp;&nbsp;<span id="p_edad"></span>
						</div>
					</div>
					<div class="p_edicion_fila p_edicion_ficha_borde_inferior">
						<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Lugar de Nacimiento:</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_lugar_nac" id="p_lugar_nac" value="<?php echo ($datos['p_lugar_nac']) ? $datos['p_lugar_nac'] : ""; ?>" size="12" onKeyPress="return soloEnterosLetrasComilla(event)" tabindex="18" />
						</div>
						<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Provincia:</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_provincia" id="p_provincia" value="<?php echo ($datos['p_provincia']) ? $datos['p_provincia'] : ""; ?>" size="12" onKeyPress="return soloEnterosLetrasComilla(event)" tabindex="19" />
						</div>
						<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pa&iacute;s:</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_pais" id="p_pais" value="<?php echo ($datos['p_pais']) ? $datos['p_pais'] : ""; ?>" size="12" onKeyPress="return soloEnterosLetrasComilla(event)" tabindex="20" />
						</div>
					</div>
					<div class="p_edicion_fila p_edicion_ficha_borde_superior">
						<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Nacionalidad:</div>
						<div class="p_edicion_datos_valor">
							<input type="radio" name="p_nacionalidad" id="op_arg_nativo" value="Argentino Nativo" <?php echo (isset($datos['p_nacionalidad']) && $datos['p_nacionalidad'] == 'Argentino Nativo') ? 'checked' : ''; ?> tabindex="21" />&nbsp;Argentino Nativo
							<input type="radio" name="p_nacionalidad" id="op_arg_opcion" value="Argentino por Opci&oacute;n" <?php echo (isset($datos['p_nacionalidad']) && $datos['p_nacionalidad'] == 'Argentino por Opci&oacute;n') ? 'checked' : ''; ?> tabindex="22" />&nbsp;Argentino por Opci&oacute;n
							<input type="radio" name="p_nacionalidad" id="op_arg_adopcion" value="Argentino por Adopci&oacute;n" <?php echo (isset($datos['p_nacionalidad']) && $datos['p_nacionalidad'] == 'Argentino por Adopci&oacute;n') ? 'checked' : ''; ?> tabindex="23" />&nbsp;Argentino por Adopci&oacute;n
						</div>
					</div>
					<div class="p_edicion_fila">
						<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Estado Civil:</div>
						<div class="p_edicion_datos_valor">
							&nbsp;
							<input type="radio" name="p_estado_civil" id="op_estado_soltero" value="Soltero" <?php echo (isset($datos['p_estado_civil']) && $datos['p_estado_civil'] == 'Soltero') ? 'checked' : ''; ?> tabindex="24" />&nbsp;Soltero
							<input type="radio" name="p_estado_civil" id="op_estado_casado" value="Casado" <?php echo (isset($datos['p_estado_civil']) && $datos['p_estado_civil'] == 'Casado') ? 'checked' : ''; ?> tabindex="25" />&nbsp;Casado
							<input type="radio" name="p_estado_civil" id="op_estado_separado" value="Separado" <?php echo (isset($datos['p_estado_civil']) && $datos['p_estado_civil'] == 'Separado') ? 'checked' : ''; ?> tabindex="26" />&nbsp;Separado
							<input type="radio" name="p_estado_civil" id="op_estado_divorciado" value="Divorciado" <?php echo (isset($datos['p_estado_civil']) && $datos['p_estado_civil'] == 'Divorciado') ? 'checked' : ''; ?> tabindex="27" />&nbsp;Divorciado
							<input type="radio" name="p_estado_civil" id="op_estado_viudo" value="Viudo" <?php echo (isset($datos['p_estado_civil']) && $datos['p_estado_civil'] == 'Viudo') ? 'checked' : ''; ?> tabindex="28" />&nbsp;Viudo
							<input type="radio" name="p_estado_civil" id="op_estado_union_hecho" value="Unido de Hecho" <?php echo (isset($datos['p_estado_civil']) && $datos['p_estado_civil'] == 'Unido de Hecho') ? 'checked' : ''; ?> tabindex="29" />&nbsp;Unido de Hecho
						</div>
					</div>
				</div>
			</div>
			<div style="height: 5px; font-size: 0; clear: both;"></div>
			<!-- Domicilio Legal -->
			<div class="p_edicion_datos">
				<div class="p_edicion_datos_titulo_leyenda degradado">
					Domicilio Legal (el que figura en el documento)&nbsp;
					<a href="#" id="toggle_domicilio_legal" title="Mostrar">
						<img id="img_mostrar_ocultar_domicilio_legal" src="imagenes/barra/bajar.gif" width="14" height="12">
					</a>
				</div>
				<div id="panel_domicilio_legal">
					<div style="height: 5px; font-size: 0;"></div>
					<div class="p_edicion_fila">
						<div class="p_edicion_datos_titulo p_edicion_datos_titulo_ancho100">&nbsp;&nbsp;&nbsp;&nbsp;Calle:</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_calle_legal" id="p_calle_legal" value="<?php echo $datos['p_calle_legal']; ?>" tabindex="30" />
						</div>
						<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;N&uacute;mero:</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_numero_legal" id="p_numero_legal" value="<?php echo $datos['p_numero_legal']; ?>" size="4" maxlength="5" onKeyPress="return soloEnteros(event);" tabindex="31" />
						</div>
						<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Piso:</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_piso_legal" id="p_piso_legal" value="<?php echo $datos['p_piso_legal']; ?>" size="1" maxlength="2" tabindex="32" />
						</div>
						<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;Depto.:</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_depto_legal" id="p_depto_legal" value="<?php echo $datos['p_depto_legal']; ?>" size="1" maxlength="2" tabindex="33" />
						</div>
					</div>
					<div class="p_edicion_fila">
						<div class="p_edicion_datos_titulo p_edicion_datos_titulo_ancho100">&nbsp;&nbsp;&nbsp;&nbsp;Entre calles:</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_entre_calles_legal" id="p_entre_calles_legal" value="<?php echo $datos['p_entre_calles_legal']; ?>" size="64" tabindex="34" />
						</div>
					</div>
					<div class="p_edicion_fila">
						<div class="p_edicion_datos_titulo p_edicion_datos_titulo_ancho100">&nbsp;&nbsp;&nbsp;&nbsp;Zona / Barrio:</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_zona_barrio_legal" id="p_zona_barrio_legal" value="<?php echo $datos['p_zona_barrio_legal']; ?>" size="64" tabindex="35" />
						</div>
					</div>
					<div class="p_edicion_fila">
						<div class="p_edicion_datos_titulo p_edicion_datos_titulo_ancho100">&nbsp;&nbsp;&nbsp;&nbsp;Pa&iacute;s:</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_pais_legal" id="p_pais_legal" value="<?php echo ($datos['p_pais_legal']) ? $datos['p_pais_legal'] : 'Argentina'; ?>" tabindex="36" />
						</div>
						<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Provincia:&nbsp;</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_provincia_legal" id="p_provincia_legal" value="<?php echo ($datos['p_provincia_legal']) ? $datos['p_provincia_legal'] : 'Buenos Aires'; ?>" size="28" tabindex="37" />
						</div>
					</div>
					<div class="p_edicion_fila">
						<div class="p_edicion_datos_titulo p_edicion_datos_titulo_ancho100">&nbsp;&nbsp;&nbsp;&nbsp;Localidad:</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_localidad_legal" id="p_localidad_legal" value="<?php echo ($datos['p_localidad_legal']) ? $datos['p_localidad_legal'] : 'Mar del Plata'; ?>" tabindex="38" />
						</div>
						<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Tel&eacute;fono:&nbsp;&nbsp;&nbsp;</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_telefono_legal" id="p_telefono_legal" value="<?php echo $datos['p_telefono_legal']; ?>" size="28" tabindex="39" />
						</div>
					</div>
				</div>
			</div>
			<div style="height: 5px; font-size: 0; clear: both;"></div>
			<!-- Domicilio Real -->
			<div class="p_edicion_datos">
				<div class="p_edicion_datos_titulo_leyenda degradado">
					Domicilio Real (donde vive realmente)&nbsp;
					<a href="#" id="toggle_domicilio_real" title="Mostrar">
						<img id="img_mostrar_ocultar_domicilio_real" src="imagenes/barra/bajar.gif" width="14" height="12">
					</a>
				</div>
				<div id="panel_domicilio_real">
					<div style="height: 10px; font-size: 0;"></div>
					<div class="p_edicion_fila">
						<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Domicilio Real = Domicilio Legal</div>
						<div class="p_edicion_datos_valor">
							<input type="checkbox" name="chk_editar_domicilio_real" id="chk_editar_domicilio_real" checked>
						</div>
					</div>
					<div class="p_edicion_fila">
						<div class="p_edicion_datos_titulo p_edicion_datos_titulo_ancho100">&nbsp;&nbsp;&nbsp;&nbsp;Calle:</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_calle_real" id="p_calle_real" value="<?php echo $datos['p_calle_real']; ?>" tabindex="40" />
						</div>
						<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;N&uacute;mero:</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_numero_real" id="p_numero_real" value="<?php echo $datos['p_numero_real']; ?>" size="4" maxlength="5" onKeyPress="return soloEnteros(event);" tabindex="41" />
						</div>
						<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Piso:</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_piso_real" id="p_piso_real" value="<?php echo $datos['p_piso_real']; ?>" size="1" maxlength="2" tabindex="42" />
						</div>
						<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;Depto.:</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_depto_real" id="p_depto_real" value="<?php echo $datos['p_depto_real']; ?>" size="1" maxlength="2" tabindex="43" />
						</div>
					</div>
					<div class="p_edicion_fila">
						<div class="p_edicion_datos_titulo p_edicion_datos_titulo_ancho100">&nbsp;&nbsp;&nbsp;&nbsp;Entre calles:</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_entre_calles_real" id="p_entre_calles_real" value="<?php echo $datos['p_entre_calles_real']; ?>" size="64" tabindex="44" />
						</div>
					</div>
					<div class="p_edicion_fila">
						<div class="p_edicion_datos_titulo p_edicion_datos_titulo_ancho100">&nbsp;&nbsp;&nbsp;&nbsp;Zona/ Barrio:</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_zona_barrio_real" id="p_zona_barrio_real" value="<?php echo $datos['p_zona_barrio_real']; ?>" size="64" tabindex="45" />
						</div>
					</div>
					<div class="p_edicion_fila">
						<div class="p_edicion_datos_titulo p_edicion_datos_titulo_ancho100">&nbsp;&nbsp;&nbsp;&nbsp;Pa&iacute;s:</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_pais_real" id="p_pais_real" value="<?php echo ($datos['p_pais_real']) ? $datos['p_pais_real'] : 'Argentina'; ?>" tabindex="46" />
						</div>
						<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Provincia:&nbsp;</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_provincia_real" id="p_provincia_real" value="<?php echo ($datos['p_provincia_real']) ? $datos['p_provincia_real'] : 'Buenos Aires'; ?>" size="28" tabindex="47" />
						</div>
					</div>
					<div class="p_edicion_fila">
						<div class="p_edicion_datos_titulo p_edicion_datos_titulo_ancho100">&nbsp;&nbsp;&nbsp;&nbsp;Localidad:</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_localidad_real" id="p_localidad_real" value="<?php echo ($datos['p_localidad_real']) ? $datos['p_localidad_real'] : 'Mar del Plata'; ?>" tabindex="48" />
						</div>
						<div class="p_edicion_datos_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Tel&eacute;fono:&nbsp;&nbsp;&nbsp;</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_telefono_real" id="p_telefono_real" value="<?php echo $datos['p_telefono_real']; ?>" size="28" tabindex="49" />
						</div>

					</div>
					<div class="p_edicion_fila">
						<div class="p_edicion_datos_titulo p_edicion_datos_titulo_ancho100">&nbsp;&nbsp;&nbsp;&nbsp;Celular:</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_celular_real" id="p_celular_real" value="<?php echo $datos['p_celular_real']; ?>" size="9" tabindex="50" />
						</div>
						<div class="p_edicion_margen_celular_tel_mensajes"></div>
						<div class="p_edicion_datos_titulo">Tel&eacute;fono para mensajes:&nbsp;</div>
						<div class="p_edicion_datos_valor">
							<input type="text" name="p_tel_mensajes_real" id="p_tel_mensajes_real" value="<?php echo $datos['p_tel_mensajes_real']; ?>" size="16" tabindex="51" />
						</div>
					</div>
				</div>
			</div>
		</form>
		<script>
			// CALENDARIO PARA LA FECHA DE NACIMIENTO
			var cal_p_fecha_nac = new Zapatec.Calendar.setup({
				inputField:"p_fecha_nac",
				ifFormat:"%d/%m/%Y",
				button:"img_p_fecha_nac",
				showsTime:false
			});

			// CALENDARIO PARA LA FECHA DE INGRESO AL MUNICIPIO PLANTA POLITICA
			var cal_p_fecha_ingreso_planta_politica = new Zapatec.Calendar.setup({
				inputField:"p_fecha_ingreso_planta_politica",
				ifFormat:"%d/%m/%Y",
				button:"img_p_fecha_ingreso_planta_politica",
				showsTime:false
			});

			// CALENDARIO PARA LA FECHA DE INGRESO AL MUNICIPIO PLANTA PERMANENTE
			var cal_p_fecha_ingreso_planta_permanente = new Zapatec.Calendar.setup({
				inputField:"p_fecha_ingreso_planta_permanente",
				ifFormat:"%d/%m/%Y",
				button:"img_p_fecha_ingreso_planta_permanente",
				showsTime:false
			});

			$('p_solapa_Datos').setProperty('class', 'actual');

			if ( $('accion').value == "modificar" ) {
				$('p_solapa_Estudios').setProperty('class', '');
				$('p_solapa_GrupoFamiliar').setProperty('class', '');
				$('p_solapa_ExperienciaLaboral').setProperty('class', '');
				$('p_solapa_DDJJ').setProperty('class', '');
				$('p_solapa_Areas').setProperty('class', '');
				$('p_solapa_Cargos').setProperty('class', '');
				$('p_solapa_Legajos').setProperty('class', '');
			} else {
				$('p_solapa_Estudios').setStyle('display', 'none');
				$('p_solapa_GrupoFamiliar').setStyle('display', 'none');
				$('p_solapa_ExperienciaLaboral').setStyle('display', 'none');
				$('p_solapa_DDJJ').setStyle('display', 'none');
				$('p_solapa_Areas').setStyle('display', 'none');
				$('p_solapa_Cargos').setStyle('display', 'none');
				$('p_solapa_Legajos').setStyle('display', 'none');
			}


			if ( $('accion').value === 'modificar' )
			{
				$('p_fecha_nac').addEvents({
					'domready': function() {
						var edad = calcular_edad($('p_fecha_nac').value);
						if (edad != '')
							$('p_edad').setHTML("Edad: "+edad+" a"+'\u00f1'+"os");
					},
					'change': function() {
						var edad = calcular_edad($('p_fecha_nac').value);
						if (edad != '')
							$('p_edad').setHTML("Edad: "+edad+" a"+'\u00f1'+"os");
					},
					'keyup': function() {
						var edad = calcular_edad($('p_fecha_nac').value);
						if (edad != '')
							$('p_edad').setHTML("Edad: "+edad+" a"+'\u00f1'+"os");
					}
				});

				$('p_grupo_sanguineo').value = '<?= (isset($datos['p_grupo_sanguineo'])) ? $datos['p_grupo_sanguineo'] : 0; ?>';

				var op_nacionalidad = "<?php echo $datos['p_nacionalidad']; ?>";
				switch (op_nacionalidad)
				{
					case "Argentino Nativo":
						$('op_arg_nativo').checked = true;
						break;
					case "Argentino por Opci"+'\u00f3'+"n":
						$('op_arg_opcion').checked = true;
						break;
					case "Argentino por Adopci"+'\u00f3'+"n":
						$('op_arg_adopcion').checked = true;
						break;
					default:
						$('op_arg_nativo').checked = true;
				}

				var op_estado_civil = "<?php echo $datos['p_estado_civil']; ?>";
				switch (op_estado_civil)
				{
					case "Soltero":
						$('op_estado_soltero').checked = true;
						break;
					case "Casado":
						$('op_estado_casado').checked = true;
						break;
					case "Separado":
						$('op_estado_separado').checked = true;
						break;
					case "Divorciado":
						$('op_estado_divorciado').checked = true;
						break;
					case "Viudo":
						$('op_estado_viudo').checked = true;
						break;
					case "Unido de Hecho":
						$('op_estado_union_hecho').checked = true;
						break;
					default:
						$('op_estado_soltero').checked = true;
				}

				$('p_legajo').setProperty('readonly', 'readonly');

				setTimeout("$('p_apellido').select()",75);
			}
			else
			{
				$('p_legajo').setProperty('readonly', '');

				setTimeout("$('p_legajo').select()",75);
			}

			var mySlide1 = new Fx.Slide('panel_info_personal');
			$('toggle_info_personal').addEvent('click', function(e){
				e1 = new Event(e);
				mySlide1.toggle();
				e1.stop();

				if ( $('img_mostrar_ocultar_info_personal').getProperty('src') == "imagenes/barra/subir.gif" )
				{
					$('img_mostrar_ocultar_info_personal').setProperty('src', 'imagenes/barra/bajar.gif');
					$('toggle_info_personal').setProperty('title', 'Mostrar');
				}
				else
				{
					$('img_mostrar_ocultar_info_personal').setProperty('src', 'imagenes/barra/subir.gif');
					$('toggle_info_personal').setProperty('title', 'Ocultar');
				}
			});

			var mySlide2 = new Fx.Slide('panel_domicilio_legal');
			mySlide2.hide();
			$('toggle_domicilio_legal').addEvent('click', function(e){
				e2 = new Event(e);
				mySlide2.toggle();
				e2.stop();

				if ( $('img_mostrar_ocultar_domicilio_legal').getProperty('src') == "imagenes/barra/subir.gif" )
				{
					$('img_mostrar_ocultar_domicilio_legal').setProperty('src', 'imagenes/barra/bajar.gif');
					$('toggle_domicilio_legal').setProperty('title', 'Mostrar');
				}
				else
				{
					$('img_mostrar_ocultar_domicilio_legal').setProperty('src', 'imagenes/barra/subir.gif');
					$('toggle_domicilio_legal').setProperty('title', 'Ocultar');
				}
			});

			var mySlide3 = new Fx.Slide('panel_domicilio_real');
			mySlide3.hide();
			$('toggle_domicilio_real').addEvent('click', function(e){
				e3 = new Event(e);
				mySlide3.toggle();
				e3.stop();

				if ( $('img_mostrar_ocultar_domicilio_real').getProperty('src') == "imagenes/barra/subir.gif" )
				{
					$('img_mostrar_ocultar_domicilio_real').setProperty('src', 'imagenes/barra/bajar.gif');
					$('toggle_domicilio_real').setProperty('title', 'Mostrar');
				}
				else
				{
					$('img_mostrar_ocultar_domicilio_real').setProperty('src', 'imagenes/barra/subir.gif');
					$('toggle_domicilio_real').setProperty('title', 'Ocultar');
				}
			});

			// POR DEFECTO SE OCULTA EL BOTON Guardar
			$('contenedora_btGuardar').setStyle('display', 'none');

			// POR DEFECTO SE MUESTRA EL BOTON Volver
			$('p_img_titulo_cancelar_volver').setProperty('src', 'imagenes/barra/volver.jpeg');
			$('p_titulo_cancelar_volver').innerHTML = 'Volver';

			// ANTE UN CAMBIO EN LOS CAMPOS DE ENTRADA SE MODIFICA EL TITULO DEL BOTON A Cancelar
			$$("input").addEvent('change', function(){
				$('p_img_titulo_cancelar_volver').setProperty('src', 'imagenes/barra/error_16x16.gif');
				$('p_titulo_cancelar_volver').innerHTML = 'Cancelar';
			});

			// AL SITUARSE EN ALGUN CAMPO DE ENTRADA SE MUESTRA EL BOTON Guardar
			$$("input").addEvent('blur', function(){
				$('contenedora_btGuardar').setStyle('display', 'inline');
			});

			// REPITE EL VALOR EN EL CAMPO ESPECIFICADO
			function repetirValor(id_origen, id_destino) {
				$(id_destino).value = $(id_origen).value;
			}

			$('p_calle_legal').addEvent('change', function(){
				repetirValor('p_calle_legal', 'p_calle_real');
			});

			$('p_numero_legal').addEvent('change', function(){
				repetirValor('p_numero_legal', 'p_numero_real');
			});

			$('p_piso_legal').addEvent('change', function(){
				repetirValor('p_piso_legal', 'p_piso_real');
			});

			$('p_depto_legal').addEvent('change', function(){
				repetirValor('p_depto_legal', 'p_depto_real');
			});

			$('p_entre_calles_legal').addEvent('change', function(){
				repetirValor('p_entre_calles_legal', 'p_entre_calles_real');
			});

			$('p_zona_barrio_legal').addEvent('change', function(){
				repetirValor('p_zona_barrio_legal', 'p_zona_barrio_real');
			});

			$('p_pais_legal').addEvent('change', function(){
				repetirValor('p_pais_legal', 'p_pais_real');
			});

			$('p_provincia_legal').addEvent('change', function(){
				repetirValor('p_provincia_legal', 'p_provincia_real');
			});

			$('p_localidad_legal').addEvent('change', function(){
				repetirValor('p_localidad_legal', 'p_localidad_real');
			});

			$('p_telefono_legal').addEvent('change', function(){
				repetirValor('p_telefono_legal', 'p_telefono_real');
			});

			function editarDomicilioReal() {
				$('p_calle_real').setProperty('readonly', '');
				$('p_numero_real').setProperty('readonly', '');
				$('p_piso_real').setProperty('readonly', '');
				$('p_depto_real').setProperty('readonly', '');
				$('p_entre_calles_real').setProperty('readonly', '');
				$('p_zona_barrio_real').setProperty('readonly', '');
				$('p_pais_real').setProperty('readonly', '');
				$('p_provincia_real').setProperty('readonly', '');
				$('p_localidad_real').setProperty('readonly', '');
				$('p_telefono_real').setProperty('readonly', '');

				$('p_calle_real').focus();
			}

			function no_editarDomicilioReal() {
				$('p_calle_real').setProperty('readonly', 'readonly');
				$('p_numero_real').setProperty('readonly', 'readonly');
				$('p_piso_real').setProperty('readonly', 'readonly');
				$('p_depto_real').setProperty('readonly', 'readonly');
				$('p_entre_calles_real').setProperty('readonly', 'readonly');
				$('p_zona_barrio_real').setProperty('readonly', 'readonly');
				$('p_pais_real').setProperty('readonly', 'readonly');
				$('p_provincia_real').setProperty('readonly', 'readonly');
				$('p_localidad_real').setProperty('readonly', 'readonly');
				$('p_telefono_real').setProperty('readonly', 'readonly');
			}

			$('chk_editar_domicilio_real').addEvent('change', function() {
				if ( $('chk_editar_domicilio_real').checked == true )
					no_editarDomicilioReal();
				else
					editarDomicilioReal();
			});

			$('p_legajo').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_apellido').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_nombre').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_nro_documento').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_cuil').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_lugar_nac').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_provincia').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_pais').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			// 2020/05/27 XXXX
			$('p_mail').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

		//	PARA LOS CAMPOS DEL DOMICILIO LEGAL
		// ********************************************************************************
			$('p_calle_legal').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_numero_legal').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_piso_legal').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_depto_legal').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_entre_calles_legal').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_zona_barrio_legal').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_pais_legal').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_provincia_legal').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_localidad_legal').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_telefono_legal').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

		// PARA LOS CAMPOS DEL DOMICILIO REAL
		// ********************************************************************************
			$('p_calle_real').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_numero_real').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_piso_real').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_depto_real').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_entre_calles_real').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_zona_barrio_real').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_pais_real').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_provincia_real').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_localidad_real').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_telefono_real').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_celular_real').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			$('p_tel_mensajes_real').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_p_fecha_ingreso_planta_politica').disabled = false;
					$('img_p_fecha_ingreso_planta_permanente').disabled = false;
					$('img_p_fecha_nac').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter') {
						// SE DESHABILITA EL CALENDARIO
						$('img_p_fecha_ingreso_planta_politica').disabled = true;
						$('img_p_fecha_ingreso_planta_permanente').disabled = true;
						$('img_p_fecha_nac').disabled = true;
					}
				}
			});

			// Se muestra el legajo en el campo del buscador respectivo
			$('f_legajo_solapa_datos').value = '<?php echo $datos['p_legajo']; ?>';

			$('f_legajo_solapa_datos').addEvent('keydown', function(event) {
				if(event.key == 'Enter') {
					// Si hay un legajo a buscar
					if( $('f_legajo_solapa_datos').value != '' )
						refrescar('abms/index.php?controlador=personal&accion=editarFicha&legajo='+$('f_legajo_solapa_datos').value, 'contenidoAjaxEdicion');
				}
			});

			function verificarExistenciaLegajo() {

				let url = 'abms/index.php?controlador=personal&accion=verificarExistenciaLegajo&legajo='+$('f_legajo_solapa_datos').value+'&foto='+$('p_foto').value+'&cmb_area=<?php echo $_SESSION['filtro_personal']['id_area']; ?>&cmb_cargo=<?php echo $_SESSION['filtro_personal']['nomenclador']; ?>&cmb_concejal=<?php echo $_SESSION['filtro_personal']['concejal']; ?>&f_legajo=<?php echo $_SESSION['filtro_personal']['legajo']; ?>&f_apellido_y_nombre=<?php echo $_SESSION['filtro_personal']['apellido_y_nombre']; ?>&f_activos=<?php echo $_SESSION['filtro_personal']['f_activos']; ?>&pagina=<?php echo $_SESSION['filtro_personal']['pagina']; ?>';

				refrescar(url, 'contenidoAjaxPrincipal');
			}

			// Se muestra una modal para elegir una foto, recortarla y guardar dicho recorte
			function subirFotoPorIframe() {
				if ( $('p_legajo').value != '' )
					refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=mostrarIframe&legajo='+$('p_legajo').value+'&foto='+$('p_foto').value+'&accion_modal=<?php echo ($datos['p_legajo'] != '') ? 'modificar' : 'insertar'; ?>', 'capaVentana');
				else {
					alert("Debe ingresar un legajo antes de subir la foto.");
					$('p_legajo').focus();
				}
			}

			// Se muestra una modal para visualizar la foto actual y los botones "Borrar", "Subir otra" y "Cerrar"
			function editarFotoActual() {
				if ( $('p_legajo').value != '' )
					refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarFotoActual&legajo='+$('p_legajo').value+'&foto='+$('p_foto').value, 'capaVentana');
				else {
					alert("Debe ingresar un legajo antes de editar la foto.");
					$('p_legajo').focus();
				}
			}

			// Se borra la foto actual del legajo
			function borrarFotoActual() {
				if (confirm('¿Desea borrar la foto actual?'))
					refrescar('<?php echo $this->directorio; ?>/index.php?controlador=<?php echo $this->controlador; ?>&accion=borrarFotoActual&legajo='+$('p_legajo').value+'&nombre_foto='+$('p_foto').value, 'contenidoAjaxPrincipal');
			}

			// Se determina si se muestra o no el botón Borrar
			function definirBotonBorrarFotoActual() {
				// Si NO posee foto carnet
				if( $('p_foto').value == '' ) {
					// Se oculta el botón Borrar
					$('btBorrarFotoActual').setStyle('display', 'none');
					// Se setea el cursor del link para la carga de la foto
					$('p_edicion_link_foto').setStyle('cursor', 'pointer');
				} else {
					// Si posee, se muestra el botón Borrar
					$('btBorrarFotoActual').setStyle('display', 'block');
					// Se setea el cursor del link para la carga de la foto
					$('p_edicion_link_foto').setStyle('cursor', 'default');
				}
			}

			no_editarDomicilioReal();

			definirBotonBorrarFotoActual();
		</script>
	<?php
    }

    public function listarAreas($areasReconocidas = '', $info_legajo = '', $mensaje = '', $tipo_mensaje = '')
    {
		$this->mostrarCartelResultado2($mensaje, $tipo_mensaje);
    ?>
		<div class="p_buscador_legajo_solapas">
			<div class="p_buscador_legajo_solapas_titulo_e_input">
				Buscar por otro Legajo: <input type="text"
					name="f_legajo_solapa_areas" id="f_legajo_solapa_areas" value=""
					onKeyPress="return soloEnteros(event)" />
			</div>
			<div class="p_margen2_boton_edicion"></div>
			<div class="p_boton_edicion">
				<a title="Volver al listado de Personal"
					href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listar&pagina=<?php echo $_SESSION['filtro_personal']['pagina']; ?>&cmb_area=<?php echo $_SESSION['filtro_personal']['id_area']; ?>&cmb_cargo=<?php echo $_SESSION['filtro_personal']['nomenclador']; ?>&cmb_concejal=<?php echo $_SESSION['filtro_personal']['concejal']; ?>&f_legajo=<?php echo $_SESSION['filtro_personal']['legajo']; ?>&f_apellido_y_nombre=<?php echo $_SESSION['filtro_personal']['apellido_y_nombre']; ?>&f_activos=<?php echo $_SESSION['filtro_personal']['f_activos']; ?>', 'contenidoAjaxPrincipal');">
					<img src="imagenes/barra/volver.jpeg" width="17" height="17"
					align="top" />&nbsp;Volver
				</a>
			</div>

		    <?php
			// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN INGRESAR REGISTROS
			if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
			{
			?>
				<div class="p_margen2_boton_edicion"></div>
				<div class="p_boton_edicion">
					<a id="btNuevo" title="Agregar &Aacute;rea"
						href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=agregarArea&legajo=<?php echo $info_legajo['p_legajo']; ?>', 'contenidoAjaxEdicion');">
						<img src="imagenes/barra/add_16x16.gif" width="16" height="16"
						align="top" />&nbsp;Nuevo
					</a>
				</div>
			<?php
			}
			?>
		</div>

		<!-- SOLAPAS -->
		<div class="p_solapas">
			<ul>
				<li id="p_solapa_Datos">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarFicha&legajo='+$('f_legajo_solapa_areas').value, 'contenidoAjaxEdicion');"><span>Datos Personales</span></a>
				</li>
				<li id="p_solapa_GrupoFamiliar" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarFamilia&legajo='+$('f_legajo_solapa_areas').value, 'contenidoAjaxEdicion');"><span>Grupo Familiar</span></a>
				</li>
				<li id="p_solapa_Estudios" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarEstudios&legajo='+$('f_legajo_solapa_areas').value, 'contenidoAjaxEdicion');"><span>Estudios</span></a>
				</li>
				<li id="p_solapa_ExperienciaLaboral" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarAntecedentesLaborales&legajo='+$('f_legajo_solapa_areas').value, 'contenidoAjaxEdicion');"><span>Experiencia Laboral</span></a>
				</li>
				<li id="p_solapa_DDJJ" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarDDJJ&legajo='+$('f_legajo_solapa_areas').value, 'contenidoAjaxEdicion');"><span>Dec. Juradas</span></a>
				</li>
				<li id="p_solapa_Legajos" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarLegajos&legajo='+$('f_legajo_solapa_areas').value, 'contenidoAjaxEdicion');"><span>Legajo</span></a>
				</li>
				<li id="p_solapa_Areas" class="actual">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarAreas&legajo='+$('f_legajo_solapa_areas').value, 'contenidoAjaxEdicion');"><span>&Aacute;reas</span></a>
				</li>
				<li id="p_solapa_Cargos" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarCargos&legajo='+$('f_legajo_solapa_areas').value, 'contenidoAjaxEdicion');"><span>Cargos</span></a>
				</li>
			</ul>
		</div>
		<div class="p_edicion_datos">
			<div class="p_edicion_datos_titulo_leyenda degradado">
				Legajo: <?php echo number_format($info_legajo['p_legajo'], 0, '', '.').'&nbsp;&nbsp;&nbsp;'. $info_legajo['p_apellido'].', '.$info_legajo['p_nombre']; ?>
			</div>
			<div class="ac_edicion">
				<table width="100%">
					<thead class="e_tabla_titulos">
						<tr>
							<?php if ( $_SESSION['perfil3'] == 1 ) { ?>
								<th class="orden_link" width="32" colspan="2">&nbsp;</th>
							<?php } elseif ( $_SESSION['perfil3'] == 2 ) { ?>
								<th class="orden_link" width="16">&nbsp;</th>
							<?php } ?>
						    <th nowrap class="orden_link" width="237px">Nombre</th>
							<th nowrap class="orden_link" width="70px">Fecha Alta</th>
							<th nowrap class="orden_link" width="70px">Fecha Baja</th>
							<th nowrap class="orden_link">Observaciones</th>
						</tr>
					</thead>
					<tbody class="e_tabla_texto e_cuerpo_scrolleable">
						<?php
						$cantAreasReconocidas = (isset($areasReconocidas)) ? count($areasReconocidas) : 0;
						for ($ar=0; $ar < $cantAreasReconocidas; $ar++)
						{
							$area_reconocida = &$areasReconocidas[$ar];
						?>
							<tr>
								<?php
								if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 ) { ?>
									<td width="16">
										<a style="width: 16px; height: 16px; display: block;" title="Modificar &Aacute;rea" href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarArea&legajo=<?php echo $info_legajo['p_legajo']; ?>&fecha_alta=<?php echo $area_reconocida['a_fecha_alta']; ?>&id_area=<?php echo $area_reconocida['ca_id']; ?>', 'contenidoAjaxEdicion');">
											<img src="imagenes/barra/edit_16x16.gif" width="12" height="12" align="top" />
										</a>
									</td>
								<?php
								}
								if ($_SESSION['perfil3'] == 1) { ?>
									<td width="16">
										<a style="width: 16px; height: 16px; display: block;" title="Eliminar &Aacute;rea" href="javascript:if (confirm('Desea eliminar el &Aacute;rea?')){refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=eliminarArea&legajo=<?php echo $info_legajo['p_legajo']; ?>&fecha_alta=<?php echo $area_reconocida['a_fecha_alta']; ?>&id_area=<?php echo $area_reconocida['ca_id']; ?>', 'contenidoAjaxEdicion');};">
											<img src="imagenes/barra/delete_16x16.gif" width="12" height="12" align="top" />
										</a>
									</td>
								<?php } ?>
								<td nowrap style="text-align: left; padding: 0 3px 0 3px;"><?php echo $area_reconocida['ca_nombre']; ?></td>
								<td nowrap style="width: 90px; text-align: center; padding: 0 3px 0 3px;"><?php echo $this->formatearFecha($area_reconocida['a_fecha_alta']); ?></td>
								<td nowrap style="width: 90px; text-align: center; padding: 0 3px 0 3px;"><?php echo $this->formatearFecha($area_reconocida['a_fecha_baja']); ?></td>
								<td nowrap style="text-align: left; padding-left: 3px;"><?php echo $area_reconocida['a_observaciones']; ?></td>
							</tr>
						<?php
						}
						?>
					<tbody>
				</table>
			</div>
		</div>
		<script>
			<?php
			// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN VISUALIZAR LA INFORMACION PERSONAL DEL EMPLEADO
			if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 ) {
			?>
				$('p_solapa_Datos').setProperty('class', '');

				if ( $('abm_mensaje_resultado') )
				{
					$('abm_mensaje_resultado').setStyle('display', 'none');
				}
			<?php
			}
			?>

			// Se muestra el legajo en el campo del buscador respectivo
			$('f_legajo_solapa_areas').value = <?php echo ($info_legajo['p_legajo'] != '') ? $info_legajo['p_legajo'] : ''; ?>;

			$('f_legajo_solapa_areas').addEvent('keydown', function(event)
			{
				if(event.key == 'Enter')
				{
					// Si hay un legajo a buscar
					if( $('f_legajo_solapa_areas').value != '' )
					{
						refrescar('abms/index.php?controlador=personal&accion=listarAreas&legajo='+$('f_legajo_solapa_areas').value, 'contenidoAjaxEdicion');
					}
				}
			});
		</script>
	<?php
    }

    /**
     * Se edita el Area de un legajo determinado
     *
     * @param array $datos
     * @param array $filtro
     * @param array $areas_combo
     */
    public function editarArea($info_area = '', $info_legajo = '', $areas_para_combo = '')
    {
    ?>
		<div class="p_cont_botonera_edicion">
			<?php
			// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN INGRESAR REGISTROS
			if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
			{
			?>
				<div class="p_margen2_boton_edicion"></div>
				<div class="p_boton_edicion">
					<a title="Cancelar la operaci&oacute;n y volver al listado." href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarAreas&legajo=<?php echo $info_legajo['p_legajo']; ?>', 'contenidoAjaxEdicion');" class="boton_en_edicion" tabindex="5">
						<img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="top" />&nbsp;Cancelar
					</a>
				</div>
				<div class="p_margen2_boton_edicion"></div>
				<div class="p_boton_edicion">
					<a id="btGuardar" title="Guardar" href="javascript:validarArea();" class="boton_en_edicion" tabindex="4">
						<img src="imagenes/barra/ok_16x16.gif" width="16" height="16" align="top" />&nbsp;Guardar
					</a>
				</div>
			<?php
			}
			?>
		</div>
		<form action="abms/index.php" method="post" name="formAreas" id="formAreas">

			<input type="hidden" name="controlador" value="<?php echo $this->controlador; ?>" />
			<input type="hidden" name="accion" value="<?php echo ($info_area['a_legajo']) ? 'modificarArea' : 'insertarArea'; ?>" />
			<input type="hidden" name="a_legajo" value="<?php echo $info_legajo['p_legajo']; ?>" />
			<!-- Si es Concejal o no, para ingresar sus asesores al Area en caso afirmativo -->
			<input type="hidden" name="es_concejal" id="es_concejal" value="<?php echo ($info_legajo['es_concejal'] === true) ? 1 : 0; ?>" />
			<input type="hidden" name="desea_modificar_para_asesores" id="desea_modificar_para_asesores" value="" />

			<div style="height: 10px; font-size: 0;"></div>
			<div class="p_edicion_datos">
				<div class="p_edicion_datos_titulo_leyenda degradado">
					Legajo: <?php echo number_format($info_legajo['p_legajo'], 0, '', '.').'&nbsp;&nbsp;&nbsp;'. $info_legajo['p_apellido'].', '.$info_legajo['p_nombre']; ?>
				</div>
				<div class="h_edicion_fila">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;&Aacute;rea:</div>
					<div class="h_edicion_fila_valor">
						<select name="a_id_area" id="a_id_area" class="p_combos" tabindex="1">
							<option value="0">Seleccione un &Aacute;rea</option>
							<?php
							$cantAreas = (isset($areas_para_combo)) ? count($areas_para_combo) : 0;
							for ($a=0; $a < $cantAreas; $a++)
							{
								$area = &$areas_para_combo[$a];
							?>
								<option value="<?php echo $area['ca_id']; ?>"><?php echo $area['ca_nombre']; ?></option>
							<?php
							}
							?>
						</select>
					</div>
				</div>
				<div class="h_edicion_fila">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Fecha de Ingreso al &Aacute;rea:</div>
					<div class="h_edicion_fila_valor">
						<input type="text" name="a_fecha_alta" id="a_fecha_alta" value="<?php echo ($info_area['a_fecha_alta']) ? $this->formatearFecha($info_area['a_fecha_alta']) : date("d/m/Y"); ?>" style="width: 80px;" maxlength="10" onkeyup="mascara(this,'/',patron,true);" tabindex="2" />
					  	<?php
					  	if ( $info_area['a_fecha_alta'] == '' )
					  	{
					  	?>
							<input type="image" id="img_a_fecha_alta" src="imagenes/calendario/calendario.gif" alt="Presione aqu&iacute; para seleccionar la fecha." align="top" width="16" height="16">
					  	<?php
					  	}
					  	else
					  	{
					  	?>
							<img src="imagenes/calendario/calendario_gris.gif" align="top" width="16" height="16">
					  	<?php
					  	}
					  	?>
					  	(DDMMAAAA)
				  	</div>
				</div>
				<div class="h_edicion_fila">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Fecha de Egreso al &Aacute;rea:</div>
					<div class="h_edicion_fila_valor">
						<input type="text" name="a_fecha_baja" id="a_fecha_baja" value="<?php echo ($info_area['a_fecha_baja']) ? $this->formatearFecha($info_area['a_fecha_baja']) : ''; ?>" style="width: 80px;" maxlength="10" onkeyup="mascara(this,'/',patron,true);" tabindex="2" />
						<input type="image" id="img_a_fecha_baja" src="imagenes/calendario/calendario.gif" alt="Presione aqu&iacute; para seleccionar la fecha." align="top" width="16" height="16"> (DDMMAAAA)
					</div>
				</div>
				<div class="h_edicion_fila_observaciones">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Observaciones:</div>
					<div class="h_edicion_fila_valor">
						<textarea id="a_observaciones" name="a_observaciones" rows="7" cols="80" style="width: 80%" tabindex="3"><?php echo ($info_area['a_observaciones']) ? trim($info_area['a_observaciones']) : ''; ?></textarea>
					</div>
				</div>
			</div>
		</form>
		<script>
			<?php
			if ( $info_area['a_fecha_alta'] == '' )
			{
			?>
				// SE PERMITE EDITAR
				$('a_fecha_alta').setProperty('readonly', '');

				//CALENDARIO PARA LA FECHA DE ALTA AL Area
				var calAlta = new Zapatec.Calendar.setup({
					inputField:"a_fecha_alta",
					ifFormat:"%d/%m/%Y",
					button:"img_a_fecha_alta",
					showsTime:false
				});
			<?php
			}
			else
			{
			?>
				// SOLO PARA LECTURA
				$('a_fecha_alta').setProperty('readonly', 'readonly');
			<?php
			}
			?>

			//CALENDARIO PARA LA FECHA DE BAJA AL Area
			var calBaja = new Zapatec.Calendar.setup({
				inputField:"a_fecha_baja",
				ifFormat:"%d/%m/%Y",
				button:"img_a_fecha_baja",
				showsTime:false
			});

			<?php
			if ( $info_area['a_id_area'] != 0 )
			{
			?>
				$('a_id_area').value = '<?php echo $info_area['a_id_area']; ?>';
			<?php
			}
			?>

			setTimeout("$('a_id_area').focus()", 75);
		</script>

	<?php
    }

    public function listarCargos($cargosReconocidos = '', $info_legajo = '', $mensaje = '', $tipo_mensaje = '')
    {
		$this->mostrarCartelResultado2($mensaje, $tipo_mensaje);

		$modelo = new personalModel();
    ?>
		<div class="p_buscador_legajo_solapas">
			<div class="p_buscador_legajo_solapas_titulo_e_input">
				Buscar por otro Legajo: <input type="text" name="f_legajo_solapa_cargos" id="f_legajo_solapa_cargos" value="" onKeyPress="return soloEnteros(event)" />
			</div>
			<div class="p_margen2_boton_edicion"></div>
			<div class="p_boton_edicion">
				<a title="Volver al listado de Personal." href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listar&pagina=<?php echo $_SESSION['filtro_personal']['pagina']; ?>&cmb_area=<?php echo $_SESSION['filtro_personal']['id_area']; ?>&cmb_cargo=<?php echo $_SESSION['filtro_personal']['nomenclador']; ?>&cmb_concejal=<?php echo $_SESSION['filtro_personal']['concejal']; ?>&f_legajo=<?php echo $_SESSION['filtro_personal']['legajo']; ?>&f_apellido_y_nombre=<?php echo $_SESSION['filtro_personal']['apellido_y_nombre']; ?>&f_activos=<?php echo $_SESSION['filtro_personal']['f_activos']; ?>', 'contenidoAjaxPrincipal');">
					<img src="imagenes/barra/volver.jpeg" width="17" height="17" align="top" />&nbsp;Volver
				</a>
			</div>

		    <?php
			// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN INGRESAR REGISTROS
			if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
			{
			?>
				<div class="p_margen2_boton_edicion"></div>
				<div class="p_boton_edicion">
					<a id="btNuevo" title="Agregar Cargo." href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=agregarCargo&legajo=<?php echo $info_legajo['p_legajo']; ?>', 'contenidoAjaxEdicion');">
						<img src="imagenes/barra/add_16x16.gif" width="16" height="16" align="top" />&nbsp;Nuevo
					</a>
				</div>
			<?php
			}
			?>
		</div>

		<!-- SOLAPAS -->
		<div class="p_solapas">
			<ul>
				<li id="p_solapa_Datos">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarFicha&legajo='+$('f_legajo_solapa_cargos').value, 'contenidoAjaxEdicion');"><span>Datos Personales</span></a>
				</li>
				<li id="p_solapa_GrupoFamiliar" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarFamilia&legajo='+$('f_legajo_solapa_cargos').value, 'contenidoAjaxEdicion');"><span>Grupo Familiar</span></a>
				</li>
				<li id="p_solapa_Estudios" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarEstudios&legajo='+$('f_legajo_solapa_cargos').value, 'contenidoAjaxEdicion');"><span>Estudios</span></a>
				</li>
				<li id="p_solapa_ExperienciaLaboral" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarAntecedentesLaborales&legajo='+$('f_legajo_solapa_cargos').value, 'contenidoAjaxEdicion');"><span>Experiencia Laboral</span></a>
				</li>
				<li id="p_solapa_DDJJ" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarDDJJ&legajo='+$('f_legajo_solapa_cargos').value, 'contenidoAjaxEdicion');"><span>Dec. Juradas</span></a>
				</li>
				<li id="p_solapa_Legajos" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarLegajos&legajo='+$('f_legajo_solapa_cargos').value, 'contenidoAjaxEdicion');"><span>Legajo</span></a>
				</li>
				<li id="p_solapa_Areas" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarAreas&legajo='+$('f_legajo_solapa_cargos').value, 'contenidoAjaxEdicion');"><span>&Aacute;reas</span></a>
				</li>
				<li id="p_solapa_Cargos" class="actual">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarCargos&legajo='+$('f_legajo_solapa_cargos').value, 'contenidoAjaxEdicion');"><span>Cargos</span></a>
				</li>
			</ul>
		</div>

		<div class="p_edicion_datos">
			<div class="p_edicion_datos_titulo_leyenda degradado">
				Legajo: <?php echo number_format($info_legajo['p_legajo'], 0, '', '.').'&nbsp;&nbsp;&nbsp;'. $info_legajo['p_apellido'].', '.$info_legajo['p_nombre']; ?>
			</div>
			<div class="ac_edicion">

				<table width="100%">
					<thead class="e_tabla_titulos">
						<tr>
						<?php
						if ( $_SESSION['perfil3'] == 1 )
						{ //SÓLO EL PERFIL 1 PUEDE ELIMINAR
						?>
							<th class="orden_link" width="32" colspan="2">&nbsp;</th>
						<?php
						}
						elseif ( $_SESSION['perfil3'] == 2 )
						{ //SÓLO LOS PERFILES 1 Y 2 PUEDEN MODIFICAR
						?>
							<th class="orden_link" width="16">&nbsp;</th>
						<?php
						}
						?>
							<th nowrap class="orden_link" width="237px">Nombre</th>
							<th nowrap class="orden_link" width="70px">Fecha Alta</th>
							<th nowrap class="orden_link" width="70px">Decreto Alta</th>
							<th nowrap class="orden_link" width="70px">Fecha Baja</th>
							<th nowrap class="orden_link" width="70px">Decreto Baja</th>
							<th nowrap class="orden_link" width="40px">D&iacute;gito</th>
							<th nowrap class="orden_link" width="237px">Depende de</th>
							<th nowrap class="orden_link" width="70px">Secretar&iacute;a Bloque</th>
							<th nowrap class="orden_link">Observaciones</th>
							<th nowrap class="orden_link" style="background-color: #00375E;">Modificado por</th>
						</tr>
					</thead>
					<tbody class="e_tabla_texto e_cuerpo_scrolleable">
						<?php
						$cantCargosReconocidos = (isset($cargosReconocidos)) ? count($cargosReconocidos) : 0;
						for ($cr=0; $cr < $cantCargosReconocidos; $cr++)
						{
							$cargo_reconocido = &$cargosReconocidos[$cr];
						?>
							<tr>
								<?php //SOLO EL PERFIL 1 Y 2 PUEDE MODIFICAR
								if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
								{
								?>
									<td width="16">
										<a style="width: 16px; height: 16px; display: block;" title="Modificar Cargo seleccionado." href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarCargo&legajo=<?php echo $info_legajo['p_legajo']; ?>&fecha_alta=<?php echo $cargo_reconocido['c_fecha_alta']; ?>&nomenclador=<?php echo $cargo_reconocido['cc_nomenclador']; ?>', 'contenidoAjaxEdicion');">
											<img src="imagenes/barra/edit_16x16.gif" width="12" height="12" align="top" />
										</a>
									</td>
								<?php
								}

								if ($_SESSION['perfil3'] == 1)
								{ //SOLO EL PERFIL 1 PUEDE ELIMINAR
								?>
									<td width="16">
										<a style="width: 16px; height: 16px; display: block;" title="Eliminar Cargo" href="javascript:if (confirm('Desea eliminar el Cargo?')){refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=eliminarCargo&legajo=<?php echo $info_legajo['p_legajo']; ?>&fecha_alta=<?php echo $cargo_reconocido['c_fecha_alta']; ?>&nomenclador=<?php echo $cargo_reconocido['cc_nomenclador']; ?>', 'contenidoAjaxEdicion');};">
											<img src="imagenes/barra/delete_16x16.gif" width="12" height="12" align="top" />
										</a>
									</td>
								<?php
								}
								?>
								<td nowrap style="width: 217px; text-align: left; padding-left: 3px;"><?php echo $cargo_reconocido['cc_nombre']; ?></td>
								<td nowrap style="width: 70px; text-align: center; padding: 0 3px 0 3px;"><?php echo $this->formatearFecha($cargo_reconocido['c_fecha_alta']); ?></td>
								<td nowrap style="width: 70px; text-align: right; padding-right: 3px;"><?php echo $cargo_reconocido['c_nro_decreto_alta']; ?></td>
								<td nowrap style="width: 70px; text-align: center; padding: 0 3px 0 3px;"><?php echo $this->formatearFecha($cargo_reconocido['c_fecha_baja']); ?></td>
								<td nowrap style="width: 70px; text-align: right; padding-right: 3px;"><?php echo $cargo_reconocido['c_nro_decreto_baja']; ?></td>
								<td nowrap style="text-align: center; padding: 0 3px 0 3px;"><?php echo $cargo_reconocido['c_digito']; ?></td>
								<td nowrap style="width: 217px; text-align: left; padding-left: 3px;">
									<?php
									if ( $cargo_reconocido['c_depende_de'] )
									{
										$dependiente = $modelo->buscarApellidoNombre($cargo_reconocido['c_depende_de']);
										echo $dependiente['p_apellido'].', '.$dependiente['p_nombre'];
									}
									else
									{
										echo '';
									}
									?>
								</td>
								<td nowrap style="width: 70px; text-align: center;"><?php echo ($cargo_reconocido['c_pertenece_secretaria_bloque'] == '1') ? 'Si' : 'No'; ?></td>
								<td nowrap style="text-align: left; padding-left: 3px;"><?php echo $cargo_reconocido['c_observaciones']; ?></td>
								<td nowrap style="width: 80px; text-align: left; padding-left: 3px; color: #484848"><?php echo ($cargo_reconocido['c_modificado_por']) ? $modelo->obtenerNombreUsuario($cargo_reconocido['c_modificado_por']) : ''; ?></td>
							</tr>
						<?php
						}
						?>
					<tbody>
				</table>
			</div>
			<!-- FIN DE p_edicion -->
		</div>
		<script>
			<?php
			// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN VISUALIZAR LA INFORMACION PERSONAL DEL EMPLEADO
			if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
			{
			?>
				$('p_solapa_Datos').setProperty('class', '');

				if ( $('abm_mensaje_resultado') )
				{
					$('abm_mensaje_resultado').setStyle('display', 'none');
				}
			<?php
			}
			?>

			// Se muestra el legajo en el campo del buscador respectivo
			$('f_legajo_solapa_cargos').value = <?php echo ($info_legajo['p_legajo'] != '') ? $info_legajo['p_legajo'] : ''; ?>;

			$('f_legajo_solapa_cargos').addEvent('keydown', function(event)
			{
				if(event.key == 'Enter')
				{
					// Si hay un legajo a buscar
					if( $('f_legajo_solapa_cargos').value != '' )
					{
						refrescar('abms/index.php?controlador=personal&accion=listarCargos&legajo='+$('f_legajo_solapa_cargos').value, 'contenidoAjaxEdicion');
					}
				}
			});
		</script>
	<?php
    }

    public function editarCargo($info_cargo = '', $info_legajo = '', $cargos_combo = '')
    {
	?>
		<div class="p_cont_botonera_edicion">
			<?php
			// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN INGRESAR REGISTROS
			if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
			{
			?>
				<div class="p_margen2_boton_edicion"></div>

				<div class="p_boton_edicion">
					<a title="Cancelar la operaci&oacute;n y volver al listado." href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarCargos&legajo=<?php echo $info_legajo['p_legajo']; ?>', 'contenidoAjaxEdicion');" class="boton_en_edicion" tabindex="12">
						<img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="top" />&nbsp;Cancelar
					</a>
				</div>
				<div class="p_margen2_boton_edicion"></div>
				<div class="p_boton_edicion">
					<a id="btGuardar" title="Guardar" href="javascript:validarCargo();" class="boton_en_edicion" tabindex="11">
						<img src="imagenes/barra/ok_16x16.gif" width="16" height="16" align="top" />&nbsp;Guardar
					</a>
				</div>
			<?php
			}
			?>
		</div>

		<form action="abms/index.php" method="post" name="formCargos" id="formCargos">

			<input type="hidden" name="controlador" id="controlador" value="<?php echo $this->controlador; ?>" /> <input type="hidden" name="accion" value="<?php echo ($info_cargo['c_legajo']) ? 'modificarCargo' : 'insertarCargo'; ?>" />
			<input type="hidden" name="c_legajo" value="<?php echo $info_legajo['p_legajo']; ?>" />

			<div style="height: 10px; font-size: 0;"></div>
			<div class="p_edicion_datos">
				<div class="p_edicion_datos_titulo_leyenda degradado">
					Legajo: <?php echo number_format($info_legajo['p_legajo'], 0, '', '.').'&nbsp;&nbsp;&nbsp;'. $info_legajo['p_apellido'].', '.$info_legajo['p_nombre']; ?>
				</div>
				<div class="h_edicion_fila">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Cargo:</div>
					<div class="h_edicion_fila_valor">
						<select name="c_nomenclador" id="c_nomenclador" class="p_combos" tabindex="1">
							<option value="0">Seleccione un Cargo</option>
							<?php
							$cantCargos = (isset($cargos_combo)) ? count($cargos_combo) : 0;
							for ($c=0; $c < $cantCargos; $c++)
							{
								$cargo = &$cargos_combo[$c];
							?>
								<option value="<?php echo $cargo['cc_nomenclador']; ?>"><?php echo $cargo['cc_nombre']; ?></option>
							<?php
							}
							?>
						</select>
					</div>
				</div>
				<div class="h_edicion_fila">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Fecha Ingreso al Cargo:</div>
						<div class="h_edicion_fila_valor">

						<input type="text" name="c_fecha_alta" id="c_fecha_alta" value="<?php echo ($info_cargo['c_fecha_alta']) ? $this->formatearFecha($info_cargo['c_fecha_alta']) : date("d/m/Y"); ?>" style="width: 80px;" maxlength="10" onkeyup="mascara(this,'/',patron,true);" tabindex="2" />

						<?php
						// SIN FECHA DE ALTA (AL AGREGAR)
						if ( $info_cargo['c_fecha_alta'] == '' )
						{
						?>
							<input type="image" id="img_c_fecha_alta" src="imagenes/calendario/calendario.gif" alt="Presione aqui para seleccionar la fecha." align="top" width="16" height="16" />
						<?php
						}
						else
						{
						?>
							<img src="imagenes/calendario/calendario_gris.gif" align="top" width="16" height="16" />
						<?php
						}
						?>
						(DDMMAAAA)
						&nbsp;<span style="font-weight: 700;">Decreto Ingreso:</span>
						&nbsp;<input type="text" name="c_nro_decreto_alta" id="c_nro_decreto_alta" value="<?php echo $info_cargo['c_nro_decreto_alta']; ?>" style="width: 82px;" maxlength="10" tabindex="3" />
					</div>
				</div>
				<div class="h_edicion_fila">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Fecha Egreso al Cargo:</div>
					<div class="h_edicion_fila_valor">
						<input type="text" name="c_fecha_baja" id="c_fecha_baja" value="<?php echo ($info_cargo['c_fecha_baja']) ? $this->formatearFecha($info_cargo['c_fecha_baja']) : ''; ?>" style="width: 80px;" maxlength="10" onkeyup="mascara(this,'/',patron,true);" tabindex="4" />
						<input type="image" id="img_c_fecha_baja" src="imagenes/calendario/calendario.gif" alt="Presione aqui para seleccionar la fecha." align="top" width="16" height="16"> (DDMMAAAA) &nbsp;<span style="font-weight: 700;">Decreto Egreso:</span> &nbsp;
						<input type="text" name="c_nro_decreto_baja" id="c_nro_decreto_baja" value="<?php echo $info_cargo['c_nro_decreto_baja']; ?>" style="width: 82px;" maxlength="10" tabindex="5" />
					</div>
				</div>
				<div class="h_edicion_fila">
					<div class="h_edicion_fila_titulo"></div>
					<div class="h_edicion_fila_valor">
						<input type="hidden" name="c_liquidacion_pendiente" id="c_liquidacion_pendiente" value="<?php echo($info_cargo['c_liquidacion_pendiente']) ? $info_cargo['c_liquidacion_pendiente'] : 0; ?>" />
						<input type="checkbox" name="chk_liquidacion_pendiente" id="chk_liquidacion_pendiente" tabindex="6">&nbsp;Liquidaci&oacute;n pendiente
					</div>
				</div>
				<div class="h_edicion_fila">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;D&iacute;gito:</div>
					<div class="h_edicion_fila_valor">
						<input type="text" name="c_digito" id="c_digito" value="<?php echo ($info_cargo['c_digito']) ? $info_cargo['c_digito'] : $info_legajo['digito']; ?>" size="1" maxlength="2" onKeyPress="return soloEnteros(event);" tabindex="7" />
					</div>
				</div>
				<div id="campos_de_bloque">
					<div class="h_edicion_fila">
						<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Depende de:</div>
						<div class="h_edicion_fila_valor">
							<input type="text" name="c_depende_de" id="c_depende_de" value="<?php echo ($info_cargo['c_depende_de']) ? $info_cargo['c_depende_de'] : $info_legajo['depende_de']; ?>" maxlength="5" onKeyPress="return soloEnteros(event);" style="width: 50px;" tabindex="8" />
							<a id="lupaModalIniciador" href="abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarModalDependientes&c_legajo=<?php echo $info_legajo['p_legajo']; ?>" rel="moodalbox 317 350" title="Buscar Legajo">
								<img src="imagenes/barra/zoom_16x16.gif" width="16" height="16" align="top" />
							</a>
							<input type="text" name="apellido_y_nombre" id="apellido_y_nombre" value="" style="width: 200px;" disabled />
						</div>
					</div>
					<div class="h_edicion_fila">
						<div class="h_edicion_fila_titulo"></div>
						<div class="h_edicion_fila_valor">
							<input type="hidden" name="c_pertenece_secretaria_bloque" id="c_pertenece_secretaria_bloque" value="<?php echo ($info_cargo['c_pertenece_secretaria_bloque']) ? $info_cargo['c_pertenece_secretaria_bloque'] : 0; ?>" />
							<input type="checkbox" name="pertenece_secretaria_bloque" id="pertenece_secretaria_bloque" tabindex="9">&nbsp;Pertenece a Secretar&iacute;a de Bloque
						</div>
					</div>
				</div>
				<div class="h_edicion_fila_observaciones">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Observaciones:</div>
					<div class="h_edicion_fila_valor">
						<textarea id="c_observaciones" name="c_observaciones" rows="7" cols="80" style="width: 97%" tabindex="10"><?php echo ($info_cargo['c_observaciones']) ? trim($info_cargo['c_observaciones']) : ''; ?></textarea>
					</div>
				</div>
			</div>

		</form>
		<script>
			Window.onDomReady(MOOdalBox.init.bind(MOOdalBox));

			<?php
			// SIN FECHA DE ALTA (AL AGREGAR)
			if ( $info_cargo['c_fecha_alta'] == '' )
			{
			?>
				// SE PERMITE EDITAR
				$('c_fecha_alta').setProperty('readonly', '');

				//CALENDARIO PARA LA FECHA DE ALTA DEL Cargo
				var cal_c_fecha_alta = new Zapatec.Calendar.setup({
					inputField:"c_fecha_alta",
					ifFormat:"%d/%m/%Y",
					button:"img_c_fecha_alta",
					showsTime:false
				});
			<?php
			}
			else
			{
			?>
				// SOLO PARA LECTURA
				$('c_fecha_alta').setProperty('readonly', 'readonly');
			<?php
			}
			?>

			//CALENDARIO PARA LA FECHA DE ALTA AL Cargo
			var cal_c_fecha_baja = new Zapatec.Calendar.setup({
				inputField:"c_fecha_baja",
				ifFormat:"%d/%m/%Y",
				button:"img_c_fecha_baja",
				showsTime:false
			});

			$('c_fecha_baja').addEvent('change', function(){

				if( $('c_fecha_baja').value != '' )
				{
					$('chk_liquidacion_pendiente').checked = false;
					$('c_liquidacion_pendiente').value = '0';
				}
				else
				{
					$('chk_liquidacion_pendiente').checked = true;
					$('c_liquidacion_pendiente').value = '1';
				}
			});

			<?php
			if ( $info_cargo['c_liquidacion_pendiente'] == '' || $info_cargo['c_liquidacion_pendiente'] == '0' )
			{
			?>
				$('chk_liquidacion_pendiente').checked = false;
				$('c_liquidacion_pendiente').value = 0;
			<?php
			}
			else
			{
			?>
				$('chk_liquidacion_pendiente').checked = true;
				$('c_liquidacion_pendiente').value = 1;
			<?php
			}
			?>

			$('chk_liquidacion_pendiente').addEvent('change', function()
			{
				if ( $('chk_liquidacion_pendiente').checked == true )
				{
					$('c_liquidacion_pendiente').value = 1;
				}
				else
				{
					$('c_liquidacion_pendiente').value = 0;
				}
			});

			<?php
			if ( $info_cargo['c_nomenclador'] != 0 )
			{
			?>
				$('c_nomenclador').value = '<?php echo $info_cargo['c_nomenclador']; ?>';

				$('c_nomenclador').addEvent('domready', function()
				{
					// Se envia la peticion del Tipo del Area
					var cmbCargoJSON = new Json.Remote('abms/index.php?controlador='+$('controlador').value+'&accion=buscarTipoCargo&c_nomenclador='+$('c_nomenclador').value+'',
					{
						// la peticion devuelve un objeto el cual llega como parametro en el evento onComplete
						onComplete: function(objeto)
						{
							if ( objeto.tipo_cargo == 'B' )
							{
								$('campos_de_bloque').setStyle('display', 'block');
							}
							else
							{
								$('campos_de_bloque').setStyle('display', 'none');
							}
						}
					});
					cmbCargoJSON.send();
				});

			<?php
			}
			?>

			$('c_nomenclador').addEvent('change', function()
		    {
				// Se envia la peticion del Tipo del Area
				var cmbCargoJSON = new Json.Remote('abms/index.php?controlador='+$('controlador').value+'&accion=buscarTipoCargo&c_nomenclador='+$('c_nomenclador').value+'',
				{
					// la peticion devuelve un objeto el cual llega como parametro en el evento onComplete
					onComplete: function(objeto)
					{
						if ( objeto.tipo_cargo == 'B' )
						{
							$('campos_de_bloque').setStyle('display', 'block');
						}
						else
						{
							$('campos_de_bloque').setStyle('display', 'none');
						}
					}
				});
				cmbCargoJSON.send();
			});

			$('c_depende_de').addEvent('keyup', function()
			{
				if ( $('c_depende_de').value != '' && $('c_depende_de').value != $('c_legajo').value )
				{
					//Se envia la peticion de Apellido y Nombre
					var miJSON = new Json.Remote('abms/index.php?controlador='+$('controlador').value+'&accion=buscarApellidoNombre&c_depende_de='+$('c_depende_de').value+'',
					{
						//la peticion devuelve un objeto el cual llega como parametro en el evento onComplete
						onComplete: function(objeto)
						{
							if ( objeto.apellido_y_nombre != '' )
							{
								$('apellido_y_nombre').value = objeto.apellido_y_nombre;// SE MUESTRA EL apellido_y_nombre
							}
							else
							{
								$('apellido_y_nombre').value = '';
							}
						}
					});
					miJSON.send();
				}
				else
				{
					$('apellido_y_nombre').value = '';
				}
			});

			if ( $('c_depende_de').value != '' )
			{
				//Se envia la peticion de Apellido y Nombre
				var miJSON2 = new Json.Remote('abms/index.php?controlador='+$('controlador').value+'&accion=buscarApellidoNombre&c_depende_de='+$('c_depende_de').value+'',
				{
					//la peticion devuelve un objeto el cual llega como parametro en el evento onComplete
					onComplete: function(objeto)
					{
						if ( objeto.apellido_y_nombre != '' )
						{
							$('apellido_y_nombre').value = objeto.apellido_y_nombre;// SE MUESTRA EL apellido_y_nombre
						}
						else
						{
							$('apellido_y_nombre').value = '';
						}
					}
				});
				miJSON2.send();
			}
			else
			{
				$('apellido_y_nombre').value = '';
			}

			<?php
			if ( $info_cargo['c_pertenece_secretaria_bloque'] == '' )
			{
			?>
				$('pertenece_secretaria_bloque').checked = false;
				$('c_pertenece_secretaria_bloque').value = 0;
			<?php
			}
			elseif ( $info_cargo['c_pertenece_secretaria_bloque'] == 0 )
			{
			?>
				$('pertenece_secretaria_bloque').checked = false;
				$('c_pertenece_secretaria_bloque').value = 0;
			<?php
			}
			else
			{
			?>
				$('pertenece_secretaria_bloque').checked = true;
				$('c_pertenece_secretaria_bloque').value = 1;
			<?php
			}
			?>

			$('pertenece_secretaria_bloque').addEvent('change', function()
			{
				if ( $('pertenece_secretaria_bloque').checked == true )
				{
					$('c_pertenece_secretaria_bloque').value = 1;
				}
				else
				{
					$('c_pertenece_secretaria_bloque').value = 0;
				}
			});

			$('c_nro_decreto_alta').addEvents({
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					if ( $('img_c_fecha_alta') )
					{
						$('img_c_fecha_alta').disabled = false;
					}
					$('img_c_fecha_baja').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						if ( $('img_c_fecha_alta') )
						{
							$('img_c_fecha_alta').disabled = true;
						}
						$('img_c_fecha_baja').disabled = true;
					}
				}
			});

			$('c_nro_decreto_baja').addEvents({
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					if ( $('img_c_fecha_alta') )
					{
						$('img_c_fecha_alta').disabled = false;
					}
					$('img_c_fecha_baja').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						if ( $('img_c_fecha_alta') )
						{
							$('img_c_fecha_alta').disabled = true;
						}
						$('img_c_fecha_baja').disabled = true;
					}
				}
			});

			$('c_digito').addEvents({
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					if ( $('img_c_fecha_alta') )
					{
						$('img_c_fecha_alta').disabled = false;
					}
					$('img_c_fecha_baja').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						if ( $('img_c_fecha_alta') )
						{
							$('img_c_fecha_alta').disabled = true;
						}
						$('img_c_fecha_baja').disabled = true;
					}
				}
			});

			$('c_depende_de').addEvents({
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					if ( $('img_c_fecha_alta') )
					{
						$('img_c_fecha_alta').disabled = false;
					}
					$('img_c_fecha_baja').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						if ( $('img_c_fecha_alta') )
						{
							$('img_c_fecha_alta').disabled = true;
						}
						$('img_c_fecha_baja').disabled = true;
					}
				}
			});

			setTimeout("$('c_nomenclador').focus()", 75);
		</script>
	<?php
    }

    public function listarModalDependientes($datos)
    {
    ?>
		<div class="ub_listado" style="font-size: 11px;">
			<table border="0" cellpadding="0" cellspacing="0" class="e_tabla_texto">
				<thead class="e_tabla_titulos">
					<tr>
						<th class="orden_link">Legajo</th>
						<th class="orden_link">Apellido</th>
						<th class="orden_link">Nombre</th>
					</tr>
				</thead>
				<tbody class="e_cuerpo_scrolleable">
					<?php
					$n = (isset($datos)) ? count($datos) : 0;
					for ($m=0; $m < $n; $m++) {
						$dato = &$datos[$m];
					?>
						<tr id="im_fila<?php echo $m; ?>" onclick="javascript:volverModalDependiente('c_depende_de', 'apellido_y_nombre', '<?php echo $dato['p_legajo']; ?>', '<?php echo $dato['p_apellido']; ?>', '<?php echo $dato['p_nombre']; ?>');" onmouseover="javascript:$('im_fila<?php echo $m; ?>').setStyle('background-color','#DDD');" onmouseout="javascript:$('im_fila<?php echo $m; ?>').setStyle('background-color','#fff');">

							<td style="width: 50px; text-align: right; padding-right: 3px;"><?php echo $dato['p_legajo']; ?></td>
							<td style="width: 100px; text-align: left; padding-left: 3px;"><?php echo $dato['p_apellido']; ?></td>
							<td style="width: 150px; text-align: left; padding-left: 3px;"><?php echo $dato['p_nombre']; ?></td>

						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>
	<?php
    }

    public function listarEstudios($estudios = '', $info_legajo = '', $mensaje = '', $tipo_mensaje = '')
    {
    	$this->mostrarCartelResultado2($mensaje, $tipo_mensaje);
    ?>
		<div class="p_buscador_legajo_solapas">
			<div class="p_buscador_legajo_solapas_titulo_e_input">
				Buscar por otro Legajo: <input type="text" name="f_legajo_solapa_estudios" id="f_legajo_solapa_estudios" value="" onKeyPress="return soloEnteros(event)" />
			</div>
			<div class="p_margen2_boton_edicion"></div>
			<div class="p_boton_edicion">
				<a title="Volver al listado de Personal." href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listar&pagina=<?php echo $_SESSION['filtro_personal']['pagina']; ?>&cmb_area=<?php echo $_SESSION['filtro_personal']['id_area']; ?>&cmb_cargo=<?php echo $_SESSION['filtro_personal']['nomenclador']; ?>&cmb_concejal=<?php echo $_SESSION['filtro_personal']['concejal']; ?>&f_legajo=<?php echo $_SESSION['filtro_personal']['legajo']; ?>&f_apellido_y_nombre=<?php echo $_SESSION['filtro_personal']['apellido_y_nombre']; ?>&f_activos=<?php echo $_SESSION['filtro_personal']['f_activos']; ?>', 'contenidoAjaxPrincipal');">
					<img src="imagenes/barra/volver.jpeg" width="17" height="17" align="top" />&nbsp;Volver
				</a>
			</div>

		    <?php
			// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN INGRESAR REGISTROS
			if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
			{
			?>
				<div class="p_margen2_boton_edicion"></div>
				<div class="p_boton_edicion">
					<a id="btGuardar" title="Agregar Estudio." href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=agregarEstudio&legajo=<?php echo $info_legajo['p_legajo']; ?>', 'contenidoAjaxEdicion');">
						<img src="imagenes/barra/add_16x16.gif" width="16" height="16" align="top" />&nbsp;Nuevo
					</a>
				</div>
			<?php
			}
			?>
		</div>

		<!-- SOLAPAS -->
		<div class="p_solapas">
			<ul>
				<li id="p_solapa_Datos">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarFicha&legajo='+$('f_legajo_solapa_estudios').value, 'contenidoAjaxEdicion');"><span>Datos Personales</span></a>
				</li>
				<li id="p_solapa_GrupoFamiliar" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarFamilia&legajo='+$('f_legajo_solapa_estudios').value, 'contenidoAjaxEdicion');"><span>Grupo Familiar</span></a>
				</li>
				<li id="p_solapa_Estudios" class="actual">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarEstudios&legajo='+$('f_legajo_solapa_estudios').value, 'contenidoAjaxEdicion');"><span>Estudios</span></a>
				</li>
				<li id="p_solapa_ExperienciaLaboral" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarAntecedentesLaborales&legajo='+$('f_legajo_solapa_estudios').value, 'contenidoAjaxEdicion');"><span>Experiencia Laboral</span></a>
				</li>
				<li id="p_solapa_DDJJ" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarDDJJ&legajo='+$('f_legajo_solapa_estudios').value, 'contenidoAjaxEdicion');"><span>Dec. Juradas</span></a>
				</li>
				<li id="p_solapa_Legajos" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarLegajos&legajo='+$('f_legajo_solapa_estudios').value, 'contenidoAjaxEdicion');"><span>Legajo</span></a>
				</li>
				<li id="p_solapa_Areas" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarAreas&legajo='+$('f_legajo_solapa_estudios').value, 'contenidoAjaxEdicion');"><span>&Aacute;reas</span></a>
				</li>
				<li id="p_solapa_Cargos" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarCargos&legajo='+$('f_legajo_solapa_estudios').value, 'contenidoAjaxEdicion');"><span>Cargos</span></a>
				</li>
			</ul>
		</div>

		<div class="p_edicion_datos">
			<div class="p_edicion_datos_titulo_leyenda degradado">
				Legajo: <?php echo number_format($info_legajo['p_legajo'], 0, '', '.').'&nbsp;&nbsp;&nbsp;'. $info_legajo['p_apellido'].', '.$info_legajo['p_nombre']; ?>
			</div>
			<div class="ac_edicion">
				<table width="100%">
					<thead class="e_tabla_titulos">
						<tr>
						<?php
						// SÓLO EL PERFIL 1 PUEDE ELIMINAR
						if ( $_SESSION['perfil3'] == 1 )
						{
						?>
							<th class="orden_link" width="32" colspan="2">&nbsp;</th>
						<?php
						}
						// SÓLO LOS PERFILES 1 Y 2 PUEDEN MODIFICAR
						elseif ( $_SESSION['perfil3'] == 2 )
						{
						?>
							<th class="orden_link" width="16">&nbsp;</th>
						<?php
						}
						?>
						<th nowrap class="orden_link">Fecha</th>
							<th nowrap class="orden_link">T&iacute;tulo Obtenido</th>
							<th nowrap class="orden_link">Organismo que lo otorg&oacute;</th>
							<th nowrap class="orden_link">Observaciones</th>
						</tr>
					</thead>
					<tbody class="e_tabla_texto e_cuerpo_scrolleable">
						<?php
						$cantEstudios = (isset($estudios)) ? count($estudios) : 0;
						for ($h=0; $h < $cantEstudios; $h++)
						{
							$estudio = &$estudios[$h];
						?>
							<tr>
								<?php //SOLO EL PERFIL 1 Y 2 PUEDE MODIFICAR
								if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
								{
								?>
									<td width="16">
										<a style="width: 16px; height: 16px; display: block;" title="Modificar Estudio seleccionado." href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarEstudio&legajo=<?php echo $estudio['e_legajo']; ?>&fecha=<?php echo $estudio['e_fecha']; ?>', 'contenidoAjaxEdicion');">
											<img src="imagenes/barra/edit_16x16.gif" width="12" height="12" align="top" />
										</a>
									</td>
								<?php
								}
								if ($_SESSION['perfil3'] == 1){ //SOLO EL PERFIL 1 PUEDE ELIMINAR
								?>
									<td width="16">
										<a style="width: 16px; height: 16px; display: block;" itle="Eliminar Estudio seleccionado." href="javascript:if (confirm('Desea eliminar el Estudio seleccionado?')){refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=eliminarEstudio&legajo=<?php echo $estudio['e_legajo']; ?>&fecha=<?php echo $estudio['e_fecha']; ?>', 'contenidoAjaxEdicion');};">
											<img src="imagenes/barra/delete_16x16.gif" width="12" height="12" align="top" />
										</a>
									</td>
								<?php
								}
								?>
								<td nowrap style="width: 70px; text-align: center; padding: 0 3px 0 3px;"><?php echo $this->formatearFecha($estudio['e_fecha']); ?></td>
								<td nowrap style="text-align: left; padding-left: 3px;"><?php echo $estudio['e_titulo']; ?></td>
								<td nowrap style="text-align: left; padding-left: 3px;"><?php echo $estudio['e_organismo']; ?></td>
								<td nowrap style="text-align: left; padding-left: 3px;"><?php echo $estudio['e_observaciones']; ?></td>
							</tr>
						  <?php
						  }
						  ?>
					<tbody>
				</table>
			</div>
		</div>
		<script>
			<?php
			// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN VISUALIZAR LA INFORMACION PERSONAL DEL EMPLEADO
			if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
			{
			?>
				$('p_solapa_Datos').setProperty('class', '');

				if ( $('abm_mensaje_resultado') )
				{
					$('abm_mensaje_resultado').setStyle('display', 'none');
				}
			<?php
			}
			?>

			// Se muestra el legajo en el campo del buscador respectivo
			$('f_legajo_solapa_estudios').value = <?php echo ($info_legajo['p_legajo'] != '') ? $info_legajo['p_legajo'] : ''; ?>;

			$('f_legajo_solapa_estudios').addEvent('keydown', function(event)
			{
				if(event.key == 'Enter')
				{
					// Si hay un legajo a buscar
					if( $('f_legajo_solapa_estudios').value != '' )
					{
						refrescar('abms/index.php?controlador=personal&accion=listarEstudios&legajo='+$('f_legajo_solapa_estudios').value, 'contenidoAjaxEdicion');
					}
				}
			});
		</script>
	<?php
    }

    public function editarEstudio($estudio = '', $info_legajo = '')
    {
	?>
		<div class="p_cont_botonera_edicion">
			<?php
			// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN INGRESAR REGISTROS
			if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
			{
			?>
				<div class="p_margen2_boton_edicion"></div>

				<div class="p_boton_edicion">
					<a title="Cancelar la operaci&oacute;n y volver al listado." href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarEstudios&legajo=<?php echo $info_legajo['p_legajo']; ?>', 'contenidoAjaxEdicion');" class="boton_en_edicion" tabindex="12">
						<img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="top" />&nbsp;Cancelar
					</a>
				</div>
				<div class="p_margen2_boton_edicion"></div>
				<div class="p_boton_edicion">
					<a id="btGuardar" title="Guardar" href="javascript:validarEstudio();" class="boton_en_edicion" tabindex="11">
						<img src="imagenes/barra/ok_16x16.gif" width="16" height="16" align="top" />&nbsp;Guardar
					</a>
				</div>
			<?php
			}
			?>
		</div>
		<form action="abms/index.php" method="post" name="formEstudio" id="formEstudio">

			<input type="hidden" name="controlador"value="<?php echo $this->controlador; ?>" />
			<input type="hidden" name="e_legajo" value="<?php echo $info_legajo['p_legajo']; ?>" />
			<input type="hidden" name="accion" value="<?php echo ($estudio['e_legajo']) ? 'modificarEstudio' : 'insertarEstudio'; ?>" />

			<div style="height: 10px; font-size: 0;"></div>
			<div class="p_edicion_datos">
				<div class="p_edicion_datos_titulo_leyenda degradado">
					Legajo: <?php echo number_format($info_legajo['p_legajo'], 0, '', '.').'&nbsp;&nbsp;&nbsp;'. $info_legajo['p_apellido'].', '.$info_legajo['p_nombre']; ?>
				</div>
				<div class="estud_edicion_fila">
					<div class="estud_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Fecha:</div>
					<div class="estud_edicion_fila_valor">
						<input type="text" name="e_fecha" id="e_fecha" value="<?php echo $this->formatearFecha($estudio[0]['e_fecha']); ?>" style="width: 80px;" maxlength="10" onkeyup="mascara(this,'/',patron,true);" tabindex="1" />
						<?php
						if ( empty($estudio['e_fecha']) )
							echo '<input type="image" id="img_e_fecha" src="imagenes/calendario/calendario.gif" alt="Presione aqu&iacute; para seleccionar la fecha." align="top" width="16" height="16"> (DDMMAAAA)';
						?>
					</div>
				</div>
				<div class="estud_edicion_fila">
					<div class="estud_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Estudios Cursados:</div>
					<div class="estud_edicion_fila_valor">
						<input type="radio" name="e_tipo_estudio" id="op_ninguno" value="6" tabindex="2" />&nbsp;Ninguno <input type="radio" name="e_tipo_estudio" id="op_primario" value="1" tabindex="3" />&nbsp;Primario
						<input type="radio" name="e_tipo_estudio" id="op_secundario" value="2" tabindex="4" />&nbsp;Secundario <input type="radio" name="e_tipo_estudio" id="op_terciario" value="3" tabindex="5" />&nbsp;Terciario
						<input type="radio" name="e_tipo_estudio" id="op_universitario" value="4" tabindex="6" />&nbsp;Universitario <input type="radio" name="e_tipo_estudio" id="op_posgrado" value="5" tabindex="7" />&nbsp;Posgrado
					</div>
				</div>
				<div class="estud_edicion_fila">
					<div class="estud_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;T&iacute;tulo:</div>
					<div class="estud_edicion_fila_valor">
						<input type="text" name="e_titulo" id="e_titulo" value="<?php echo $estudio['e_titulo']; ?>" size="57" tabindex="8" />
					</div>
				</div>
				<div class="estud_edicion_fila">
					<div class="estud_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Organismo:</div>
					<div class="estud_edicion_fila_valor">
						<input type="text" name="e_organismo" id="e_organismo" value="<?php echo $estudio['e_organismo']; ?>" size="57" tabindex="9" />
					</div>
				</div>
				<div class="h_edicion_fila_observaciones">
					<div class="estud_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Observaciones:</div>
					<div class="estud_edicion_fila_valor">
						<textarea id="e_observaciones" name="e_observaciones" rows="7" cols="80" style="width: 80%" tabindex="10"><?php echo ($estudio['e_observaciones']) ? trim($estudio['e_observaciones']) : ''; ?></textarea>
					</div>
				</div>
			</div>
		</form>
		<script>
			<?php
			// SI SE MODIFICA EL ESTUDIO, NO SE PERMITE EDITAR LA FECHA
			if ( $estudio['e_fecha'] )
			{
			?>
				$('e_fecha').setProperty('readonly', 'readonly');
			<?php
			}
			else
			{
			?>
				$('e_fecha').setProperty('readonly', '');

				//CALENDARIO PARA LA FECHA DE INGRRESO AL EJECUTIVO
				var cal_e_fecha = new Zapatec.Calendar.setup({
					inputField:"e_fecha",
					ifFormat:"%d/%m/%Y",
					button:"img_e_fecha",
					showsTime:false
				});

				$('e_titulo').addEvents({
					keyup: function(){
						// SE HABILITA EL CALENDARIO
						$('img_e_fecha').disabled = false;
					},
					keydown: function(event){
						if(event.key == 'Enter')
						{
							// SE DESHABILITA EL CALENDARIO
							$('img_e_fecha').disabled = true;
						}
					}
				});

				$('e_organismo').addEvents({
					keyup: function(){
						// SE HABILITA EL CALENDARIO
						$('img_e_fecha').disabled = false;
					},
					keydown: function(event){
						if(event.key == 'Enter')
						{
							// SE DESHABILITA EL CALENDARIO
							$('img_e_fecha').disabled = true;
						}
					}
				});
			<?php
			}

			if ( $estudio['e_tipo_estudio'] != '' )
			{
			?>
				var op_tipo_estudio = <?php echo $estudio['e_tipo_estudio']; ?>;
				switch (op_tipo_estudio)
				{
					case 1:
						$('op_primario').checked = true;// Primario
						setTimeout("$('op_primario').focus()",75);
						break;
					case 2:
						$('op_secundario').checked = true;// Secundario
						setTimeout("$('op_secundario').focus()",75);
						break;
					case 3:
						$('op_terciario').checked = true;// Terciario
						setTimeout("$('op_terciario').focus()",75);
						break;
					case 4:
						$('op_universitario').checked = true;// Universitario
						setTimeout("$('op_universitario').focus()",75);
						break;
					case 5:
						$('op_posgrado').checked = true;// Posgrado
						setTimeout("$('op_posgrado').focus()",75);
						break;
					default:
						$('op_ninguno').checked = true;// Ninguno
						setTimeout("$('op_ninguno').focus()",75);
				}// de JS
			<?php
			}// de PHP
			else
			{
			?>
				$('op_primario').checked = true;

				setTimeout("$('e_fecha').focus()",75);
			<?php
			}
			?>
		</script>
	<?php
    }

	public function listarAntecedentesLaborales($antecedentes_laborales = '', $info_legajo = '', $mensaje = '', $tipo_mensaje = '')
	{
		$this->mostrarCartelResultado2($mensaje, $tipo_mensaje);
    ?>
		<div class="p_buscador_legajo_solapas">
			<div class="p_buscador_legajo_solapas_titulo_e_input">
				Buscar por otro Legajo:
				<input type="text" name="f_legajo_solapa_antecedente_laboral" id="f_legajo_solapa_antecedente_laboral" value="" onKeyPress="return soloEnteros(event)" />
			</div>
			<div class="p_margen2_boton_edicion"></div>
			<div class="p_boton_edicion">
				<a title="Volver al listado de Personal." href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listar&pagina=<?php echo $_SESSION['filtro_personal']['pagina']; ?>&cmb_area=<?php echo $_SESSION['filtro_personal']['id_area']; ?>&cmb_cargo=<?php echo $_SESSION['filtro_personal']['nomenclador']; ?>&cmb_concejal=<?php echo $_SESSION['filtro_personal']['concejal']; ?>&f_legajo=<?php echo $_SESSION['filtro_personal']['legajo']; ?>&f_apellido_y_nombre=<?php echo $_SESSION['filtro_personal']['apellido_y_nombre']; ?>&f_activos=<?php echo $_SESSION['filtro_personal']['f_activos']; ?>', 'contenidoAjaxPrincipal');">
					<img src="imagenes/barra/volver.jpeg" width="17" height="17" align="top" />&nbsp;Volver
				</a>
			</div>

		    <?php
			// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN INGRESAR REGISTROS
			if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
			{
			?>
				<div class="p_margen2_boton_edicion"></div>
				<div class="p_boton_edicion">
					<a id="btGuardar" title="Agregar al Grupo familiar." href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=agregarAntecedenteLaboral&legajo=<?php echo $info_legajo['p_legajo']; ?>', 'contenidoAjaxEdicion');">
						<img src="imagenes/barra/add_16x16.gif" width="16" height="16" align="top" />&nbsp;Nuevo
					</a>
				</div>
			<?php
			}
			?>
		</div>

		<!-- SOLAPAS -->
		<div class="p_solapas">
			<ul>
				<li id="p_solapa_Datos">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarFicha&legajo='+$('f_legajo_solapa_antecedente_laboral').value, 'contenidoAjaxEdicion');"><span>Datos Personales</span></a>
				</li>
				<li id="p_solapa_GrupoFamiliar" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarFamilia&legajo='+$('f_legajo_solapa_antecedente_laboral').value, 'contenidoAjaxEdicion');"><span>Grupo Familiar</span></a>
				</li>
				<li id="p_solapa_Estudios" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarEstudios&legajo='+$('f_legajo_solapa_antecedente_laboral').value, 'contenidoAjaxEdicion');"><span>Estudios</span></a>
				</li>
				<li id="p_solapa_ExperienciaLaboral" class="actual">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarAntecedentesLaborales&legajo='+$('f_legajo_solapa_antecedente_laboral').value, 'contenidoAjaxEdicion');"><span>Experiencia Laboral</span></a>
				</li>
				<li id="p_solapa_DDJJ" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarDDJJ&legajo='+$('f_legajo_solapa_antecedente_laboral').value, 'contenidoAjaxEdicion');"><span>Dec. Juradas</span></a>
				</li>
				<li id="p_solapa_Legajos" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarLegajos&legajo='+$('f_legajo_solapa_antecedente_laboral').value, 'contenidoAjaxEdicion');"><span>Legajo</span></a>
				</li>
				<li id="p_solapa_Areas" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarAreas&legajo='+$('f_legajo_solapa_antecedente_laboral').value, 'contenidoAjaxEdicion');"><span>&Aacute;reas</span></a>
				</li>
				<li id="p_solapa_Cargos" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarCargos&legajo='+$('f_legajo_solapa_antecedente_laboral').value, 'contenidoAjaxEdicion');"><span>Cargos</span></a>
				</li>
			</ul>
		</div>

		<div class="p_edicion_datos">
			<div class="p_edicion_datos_titulo_leyenda degradado">
				Legajo: <?php echo number_format($info_legajo['p_legajo'], 0, '', '.').'&nbsp;&nbsp;&nbsp;'. $info_legajo['p_apellido'].', '.$info_legajo['p_nombre']; ?>
			</div>
			<div class="f_edicion_listado_hijos">
				<table width="100%">
					<thead class="e_tabla_titulos">
						<tr>
							<?php
							if ( $_SESSION['perfil3'] == 1 )
							{ //SÓLO EL PERFIL 1 PUEDE ELIMINAR
							?>
								<th class="orden_link" width="32" colspan="2">&nbsp;</th>
							<?php
							}
							elseif ( $_SESSION['perfil3'] == 2 )
							{ //SÓLO LOS PERFILES 1 Y 2 PUEDEN MODIFICAR
							?>
								<th class="orden_link" width="16">&nbsp;</th>
							<?php
							}
							?>
							<th nowrap class="orden_link">&Aacute;mbito</th>
							<th nowrap class="orden_link">Empresa</th>
							<th nowrap class="orden_link">Cargo</th>
							<th nowrap class="orden_link">Desde</th>
							<th nowrap class="orden_link">Hasta</th>
							<th nowrap class="orden_link">Motivos del Cese</th>
							<th nowrap class="orden_link">Observaciones</th>
						</tr>
					</thead>
					<tbody class="e_tabla_texto e_cuerpo_scrolleable">
						<?php
						$cantAntecedentesLaborales = (isset($antecedentes_laborales)) ? count($antecedentes_laborales) : 0;
						for ($al=0; $al < $cantAntecedentesLaborales; $al++)
						{
						  $antecedente_laboral = &$antecedentes_laborales[$al];
						?>
						  <tr>
							<?php //SOLO EL PERFIL 1 Y 2 PUEDE MODIFICAR
							if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
							{
							?>
								<td width="16">
									<a style="width: 16px; height: 16px; display: block;" title="Modificar Trabajo seleccionado." href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarAntecedenteLaboral&legajo=<?php echo $antecedente_laboral['al_legajo']; ?>&id=<?php echo $antecedente_laboral['al_id']; ?>', 'contenidoAjaxEdicion');">
										<img src="imagenes/barra/edit_16x16.gif" width="12" height="12" align="top" />
									</a>
								</td>
							<?php
							}
							if ($_SESSION['perfil3'] == 1){ //SOLO EL PERFIL 1 PUEDE ELIMINAR
							?>
								<td width="16">
									<a style="width: 16px; height: 16px; display: block;" title="Eliminar Trabajo seleccionado." href="javascript:if (confirm('Desea eliminar el Trabajo seleccionado?')){refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=eliminarAntecedenteLaboral&legajo=<?php echo $antecedente_laboral['al_legajo']; ?>&id=<?php echo $antecedente_laboral['al_id']; ?>', 'contenidoAjaxEdicion');};">
										<img src="imagenes/barra/delete_16x16.gif" width="12" height="12" align="top" />
									</a>
								</td>
							<?php
							}
							?>
							<td nowrap style="text-align: left; padding-left: 3px;"><?php echo $antecedente_laboral['al_ambito']; ?></td>
							<td nowrap style="text-align: left; padding-left: 3px;"><?php echo $antecedente_laboral['al_empresa']; ?></td>
							<td nowrap style="text-align: left; padding-left: 3px;"><?php echo $antecedente_laboral['al_cargo']; ?></td>
							<td nowrap style="width: 70px; text-align: center; padding: 0 3px 0 3px;"><?php echo $this->formatearFecha($antecedente_laboral['al_fecha_desde']); ?></td>
							<td nowrap style="width: 70px; text-align: center; padding: 0 3px 0 3px;"><?php echo $this->formatearFecha($antecedente_laboral['al_fecha_hasta']); ?></td>
							<td nowrap style="text-align: left; padding-left: 3px;"><?php echo $antecedente_laboral['al_motivos_cese']; ?></td>
							<td nowrap style="text-align: left; padding-left: 3px;"><?php echo $antecedente_laboral['al_observaciones']; ?></td>
						</tr>
						<?php
						}
						?>
					<tbody>
				</table>
			</div>
		</div>
		<script>
			<?php
			// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN VISUALIZAR LA INFORMACION PERSONAL DEL EMPLEADO
			if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
			{
			?>
				$('p_solapa_Datos').setProperty('class', '');

				if ( $('abm_mensaje_resultado') )
				{
					$('abm_mensaje_resultado').setStyle('display', 'none');
				}
			<?php
			}
			?>

			// Se muestra el legajo en el campo del buscador respectivo
			$('f_legajo_solapa_antecedente_laboral').value = <?php echo $info_legajo['p_legajo']; ?>;

			$('f_legajo_solapa_antecedente_laboral').addEvent('keydown', function(event)
			{
				if(event.key == 'Enter')
				{
					// Si hay un legajo a buscar
					if( $('f_legajo_solapa_antecedente_laboral').value != '' )
					{
						refrescar('abms/index.php?controlador=personal&accion=listarAntecedentesLaborales&legajo='+$('f_legajo_solapa_antecedente_laboral').value, 'contenidoAjaxEdicion');
					}
				}
			});
		</script>
	<?php
    }

    public function editarAntecedenteLaboral($antecedente_laboral = '', $info_legajo = '')
    {
	?>
		<div class="p_cont_botonera_edicion">
			<?php
			// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN INGRESAR REGISTROS
			if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 ) {
			?>
				<div class="p_margen2_boton_edicion"></div>
				<div class="p_boton_edicion">
					<a title="Cancelar la operaci&oacute;n y volver al listado." href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarAntecedentesLaborales&legajo=<?php echo $info_legajo['p_legajo']; ?>', 'contenidoAjaxEdicion');" class="boton_en_edicion" tabindex="11">
						<img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="top" />&nbsp;Cancelar
					</a>
				</div>
				<div class="p_margen2_boton_edicion"></div>
				<div class="p_boton_edicion">
					<a id="btGuardar" title="Guardar" href="javascript:validarAntecedenteLaboral();" class="boton_en_edicion" tabindex="10">
						<img src="imagenes/barra/ok_16x16.gif" width="16" height="16" align="top" />&nbsp;Guardar
					</a>
				</div>
			<?php
			}
			?>
		</div>
		<form action="abms/index.php" method="post" name="formAntecedenteLaboral" id="formAntecedenteLaboral">

			<input type="hidden" name="controlador" value="<?php echo $this->controlador; ?>" />
			<input type="hidden" name="al_legajo" value="<?php echo $info_legajo['p_legajo']; ?>" />
			<input type="hidden" name="al_id" id="al_id" value="<?php echo $antecedente_laboral['al_id']; ?>" />
			<input type="hidden" name="accion" value="<?php echo ($antecedente_laboral['al_legajo']) ? 'modificarAntecedenteLaboral' : 'insertarAntecedenteLaboral'; ?>" />

			<div style="height: 10px; font-size: 0;"></div>
			<div class="p_edicion_datos">
				<div class="p_edicion_datos_titulo_leyenda degradado">
					Legajo: <?php echo number_format($info_legajo['p_legajo'], 0, '', '.').'&nbsp;&nbsp;&nbsp;'. $info_legajo['p_apellido'].', '.$info_legajo['p_nombre']; ?>
				</div>
				<div class="h_edicion_fila" id="contenedora_f_vive">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;&Aacute;mbito:</div>
					<div class="h_edicion_fila_valor">
						<input type="radio" name="al_ambito" id="op_privado" value="Privado" tabindex="1" />&nbsp;Privado <input type="radio" name="al_ambito" id="op_publico" value="P&uacute;blico" tabindex="2" />&nbsp;P&uacute;blico
						<input type="radio" name="al_ambito" id="op_propio" value="Propio" tabindex="3" />&nbsp;Propio
					</div>
				</div>
				<div class="h_edicion_fila">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Empresa:</div>
					<div class="h_edicion_fila_valor">
						<input type="text" name="al_empresa" id="al_empresa" value="<?php echo $antecedente_laboral['al_empresa']; ?>" size="53" tabindex="4" />
					</div>
				</div>
				<div class="h_edicion_fila">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Cargo:</div>
					<div class="h_edicion_fila_valor">
						<input type="text" name="al_cargo" id="al_cargo" value="<?php echo $antecedente_laboral['al_cargo']; ?>" size="53" tabindex="5" />
					</div>
				</div>

				<div class="h_edicion_fila" id="contenedora_f_fecha_inicio_convivencia">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Desde:</div>
					<div class="h_edicion_fila_valor">
						<input type="text" name="al_fecha_desde" id="al_fecha_desde" value="<?php if ($antecedente_laboral['al_fecha_desde']){ echo $this->formatearFecha($antecedente_laboral['al_fecha_desde']); } ?>" style="width: 80px;" maxlength="10" onkeyup="mascara(this,'/',patron,true);" tabindex="6" />
						<input type="image" id="img_al_fecha_desde" src="imagenes/calendario/calendario.gif" alt="Presione aqu&iacute; para seleccionar la fecha Desde." align="top" width="16" height="16">(DDMMAAAA)
					</div>
				</div>
				<div class="h_edicion_fila" id="contenedora_f_fecha_inicio_convivencia">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Hasta:</div>
					<div class="h_edicion_fila_valor">
						<input type="text" name="al_fecha_hasta" id="al_fecha_hasta" value="<?php if ($antecedente_laboral['al_fecha_hasta']){ echo $this->formatearFecha($antecedente_laboral['al_fecha_hasta']); } ?>" style="width: 80px;" maxlength="10" onkeyup="mascara(this,'/',patron,true);" tabindex="7" />
						<input type="image" id="img_al_fecha_hasta" src="imagenes/calendario/calendario.gif" alt="Presione aqu&iacute; para seleccionar la fecha Hasta." align="top" width="16" height="16">(DDMMAAAA)
					</div>
				</div>
				<div class="h_edicion_fila">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Motivos del Cese:</div>
					<div class="h_edicion_fila_valor">
						<input type="text" name="al_motivos_cese" id="al_motivos_cese" value="<?php echo $antecedente_laboral['al_motivos_cese']; ?>" size="53" tabindex="8" />
					</div>
				</div>
				<div class="h_edicion_fila_observaciones">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Observaciones:</div>
					<div class="h_edicion_fila_valor">
						<textarea id="al_observaciones" name="al_observaciones" rows="7" cols="80" style="width: 97%" tabindex="9"><?php echo ($antecedente_laboral['al_observaciones']) ? trim($antecedente_laboral['al_observaciones']) : ''; ?></textarea>
					</div>
				</div>
			</div>
		</form>
		<script>
			<?php
			if ( !empty($antecedente_laboral['al_ambito']) )
			{
			?>
				var op_ambito = "<?php echo $antecedente_laboral['al_ambito']; ?>";
				switch (op_ambito)
				{
					case "Privado":
						$('op_privado').checked = true;
						setTimeout("$('op_privado').focus()",75);
						break;
					case "P"+'\u00fa'+"blico":
						$('op_publico').checked = true;
						setTimeout("$('op_publico').focus()",75);
						break;
					case "Propio":
						$('op_propio').checked = true;
						setTimeout("$('op_propio').focus()",75);
						break;
				}
			<?php
			}
			else
			{
			?>
					$('op_privado').checked = true;
					setTimeout("$('op_privado').focus()",75);
			<?php
			}
			?>

			//CALENDARIO PARA LA FECHA DESDE
			var cal_fecha_desde = new Zapatec.Calendar.setup({
				inputField:"al_fecha_desde",
				ifFormat:"%d/%m/%Y",
				button:"img_al_fecha_desde",
				showsTime:false
			});

			//CALENDARIO PARA LA FECHA HASTA
			var cal_fecha_hasta = new Zapatec.Calendar.setup({
				inputField:"al_fecha_hasta",
				ifFormat:"%d/%m/%Y",
				button:"img_al_fecha_hasta",
				showsTime:false
			});

			$('al_empresa').addEvents({
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('img_al_fecha_desde').disabled = false;
					$('img_al_fecha_hasta').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('img_al_fecha_desde').disabled = true;
						$('img_al_fecha_hasta').disabled = true;
					}
				}
			});

			$('al_cargo').addEvents({
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('img_al_fecha_desde').disabled = false;
					$('img_al_fecha_hasta').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('img_al_fecha_desde').disabled = true;
						$('img_al_fecha_hasta').disabled = true;
					}
				}
			});

			$('al_motivos_cese').addEvents({
				keyup: function(){
					// SE HABILITAN LOS CALENDARIOS
					$('img_al_fecha_desde').disabled = false;
					$('img_al_fecha_hasta').disabled = false;
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITAN LOS CALENDARIOS
						$('img_al_fecha_desde').disabled = true;
						$('img_al_fecha_hasta').disabled = true;
					}
				}
			});
		</script>
	<?php
    }

	public function listarFamilia($familia = '', $info_legajo = '', $mensaje = '', $tipo_mensaje = '')
	{
		$this->mostrarCartelResultado2($mensaje, $tipo_mensaje);
    ?>
		<div class="p_buscador_legajo_solapas">
			<div class="p_buscador_legajo_solapas_titulo_e_input">
				Buscar por otro Legajo: <input type="text" name="f_legajo_solapa_grupo_familiar" id="f_legajo_solapa_grupo_familiar" value="" onKeyPress="return soloEnteros(event)" />
			</div>
			<div class="p_margen2_boton_edicion"></div>
			<div class="p_boton_edicion">
				<a title="Volver al listado de Personal." href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listar&pagina=<?php echo $_SESSION['filtro_personal']['pagina']; ?>&cmb_area=<?php echo $_SESSION['filtro_personal']['id_area']; ?>&cmb_cargo=<?php echo $_SESSION['filtro_personal']['nomenclador']; ?>&cmb_concejal=<?php echo $_SESSION['filtro_personal']['concejal']; ?>&f_legajo=<?php echo $_SESSION['filtro_personal']['legajo']; ?>&f_apellido_y_nombre=<?php echo $_SESSION['filtro_personal']['apellido_y_nombre']; ?>&f_activos=<?php echo $_SESSION['filtro_personal']['f_activos']; ?>', 'contenidoAjaxPrincipal');">
					<img src="imagenes/barra/volver.jpeg" width="17" height="17" align="top" />&nbsp;Volver
				</a>
			</div>

		    <?php
			// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN INGRESAR REGISTROS
			if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
			{
			?>
				<div class="p_margen2_boton_edicion"></div>
				<div class="p_boton_edicion">
					<a id="btGuardar" title="Agregar al Grupo familiar." href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=agregarFamiliar&legajo=<?php echo $info_legajo['p_legajo']; ?>', 'contenidoAjaxEdicion');">
						<img src="imagenes/barra/add_16x16.gif" width="16" height="16" align="top" />&nbsp;Nuevo
					</a>
				</div>
			<?php
			}
			?>
		</div>

		<!-- SOLAPAS -->
		<div class="p_solapas">
			<ul>
				<li id="p_solapa_Datos">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarFicha&legajo='+$('f_legajo_solapa_grupo_familiar').value, 'contenidoAjaxEdicion');"><span>Datos Personales</span></a>
				</li>
				<li id="p_solapa_GrupoFamiliar" class="actual">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarFamilia&legajo='+$('f_legajo_solapa_grupo_familiar').value, 'contenidoAjaxEdicion');"><span>Grupo Familiar</span></a>
				</li>
				<li id="p_solapa_Estudios" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarEstudios&legajo='+$('f_legajo_solapa_grupo_familiar').value, 'contenidoAjaxEdicion');"><span>Estudios</span></a>
				</li>
				<li id="p_solapa_ExperienciaLaboral" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarAntecedentesLaborales&legajo='+$('f_legajo_solapa_grupo_familiar').value, 'contenidoAjaxEdicion');"><span>Experiencia Laboral</span></a>
				</li>
				<li id="p_solapa_DDJJ" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarDDJJ&legajo='+$('f_legajo_solapa_grupo_familiar').value, 'contenidoAjaxEdicion');"><span>Dec Juradas</span></a>
				</li>
				<li id="p_solapa_Legajos" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarLegajos&legajo='+$('f_legajo_solapa_grupo_familiar').value, 'contenidoAjaxEdicion');"><span>Legajo</span></a>
				</li>
				<li id="p_solapa_Areas" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarAreas&legajo='+$('f_legajo_solapa_grupo_familiar').value, 'contenidoAjaxEdicion');"><span>&Aacute;reas</span></a>
				</li>
				<li id="p_solapa_Cargos" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarCargos&legajo='+$('f_legajo_solapa_grupo_familiar').value, 'contenidoAjaxEdicion');"><span>Cargos</span></a>
				</li>
			</ul>
		</div>

		<div class="p_edicion_datos">
			<div class="p_edicion_datos_titulo_leyenda degradado">
				Legajo: <?php echo number_format($info_legajo['p_legajo'], 0, '', '.').'&nbsp;&nbsp;&nbsp;'. $info_legajo['p_apellido'].', '.$info_legajo['p_nombre']; ?>
			</div>
			<div class="f_edicion_listado_hijos">
				<table width="100%">
					<thead class="e_tabla_titulos">
						<tr>
						<?php
						// SÓLO EL PERFIL 1 PUEDE ELIMINAR
						if ( $_SESSION['perfil3'] == 1 )
						{
						?>
							<th class="orden_link" width="32" colspan="2">&nbsp;</th>
						<?php
						}
						// SÓLO LOS PERFILES 1 Y 2 PUEDEN MODIFICAR
						elseif ( $_SESSION['perfil3'] == 2 )
						{
						?>
							<th class="orden_link" width="16">&nbsp;</th>
						<?php
						}
						?>
						<th nowrap class="orden_link">Parentesco</th>
						<th nowrap class="orden_link">Apellido</th>
						<th nowrap class="orden_link">Nombres</th>
						<th nowrap class="orden_link">Nro. Documento</th>
						<th nowrap class="orden_link">Fecha de Nacimiento</th>
						<th nowrap class="orden_link">Observaciones</th>
					</tr>
				</thead>
				<tbody class="e_tabla_texto e_cuerpo_scrolleable">
			  		  <?php
					  $cantFamiliares = (isset($familia)) ? count($familia) : 0;
					  for ($f=0; $f < $cantFamiliares; $f++)
					  {
						  $familiar = &$familia[$f];
					  ?>
						  <tr>
							<?php
							// SOLO EL PERFIL 1 Y 2 PUEDE MODIFICAR
							if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
							{
							?>
								<td width="16">
									<a style="width: 16px; height: 16px; display: block;" title="Modificar Integrante seleccionado." href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarFamiliar&legajo=<?php echo $familiar['f_legajo_emp']; ?>&id=<?php echo $familiar['f_id']; ?>', 'contenidoAjaxEdicion');">
										<img src="imagenes/barra/edit_16x16.gif" width="12" height="12" align="top" />
									</a>
								</td>
							<?php
							}
							// SOLO EL PERFIL 1 PUEDE ELIMINAR
							if ($_SESSION['perfil3'] == 1)
							{
							?>
								<td width="16">
									<a style="width: 16px; height: 16px; display: block;" title="Eliminar Integrante seleccionado." href="javascript:if (confirm('Desea eliminar el Integrante seleccionado?')){refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=eliminarFamiliar&legajo=<?php echo $familiar['f_legajo_emp']; ?>&id=<?php echo $familiar['f_id']; ?>', 'contenidoAjaxEdicion');};">
										<img src="imagenes/barra/delete_16x16.gif" width="12" height="12" align="top" />
									</a>
								</td>
							<?php
							}
							?>
							<td nowrap style="text-align: left; padding: 0 3px 0 3px;"><?php echo $familiar['f_parentesco']; ?></td>
							<td nowrap style="text-align: left; padding: 0 3px 0 3px;"><?php echo $familiar['f_apellido']; ?></td>
							<td nowrap style="text-align: left; padding: 0 3px 0 3px;"><?php echo $familiar['f_nombre']; ?></td>
							<td nowrap style="text-align: right; padding-right: 3px;"><?php echo $familiar['f_nro_documento']; ?></td>
							<td nowrap style="width: 70px; text-align: center; padding: 0 3px 0 3px;"><?php echo $this->formatearFecha($familiar['f_fecha_nac']); ?></td>
							<td nowrap style="text-align: left; padding-left: 3px;"><?php echo $familiar['f_observaciones']; ?></td>
						</tr>
					  	<?php
					  	}
					  	?>
					<tbody>
				</table>
			</div>
		</div>
		<script>
			<?php
			// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN VISUALIZAR LA INFORMACION PERSONAL DEL EMPLEADO
			if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
			{
			?>
				$('p_solapa_Datos').setProperty('class', '');

				if ( $('abm_mensaje_resultado') )
				{
					$('abm_mensaje_resultado').setStyle('display', 'none');
				}
			<?php
			}
			?>

			// Se muestra el legajo en el campo del buscador respectivo
			$('f_legajo_solapa_grupo_familiar').value = <?php echo ($info_legajo['p_legajo'] != '') ? $info_legajo['p_legajo'] : ''; ?>;

			$('f_legajo_solapa_grupo_familiar').addEvent('keydown', function(event)
			{
				if(event.key == 'Enter')
				{
					// Si hay un legajo a buscar
					if( $('f_legajo_solapa_grupo_familiar').value != '' )
					{
						refrescar('abms/index.php?controlador=personal&accion=listarFamilia&legajo='+$('f_legajo_solapa_grupo_familiar').value, 'contenidoAjaxEdicion');
					}
				}
			});
		</script>
	<?php
    }

    public function editarFamiliar($familiar = '', $info_legajo = '')
    {
	?>
		<div class="p_cont_botonera_edicion">
			<?php
			// SÓLO USUARIOS DE PERFIL 1 Y 2 PUEDEN INGRESAR REGISTROS
			if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 )
			{
			?>
				<div class="p_margen2_boton_edicion"></div>
				<div class="p_boton_edicion">
					<a title="Cancelar la operaci&oacute;n y volver al listado." href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarFamilia&legajo=<?php echo $info_legajo['p_legajo']; ?>', 'contenidoAjaxEdicion');" class="boton_en_edicion" tabindex="17">
						<img src="imagenes/barra/error_16x16.gif" width="16" height="16" align="top" />&nbsp;Cancelar
					</a>
				</div>
				<div class="p_margen2_boton_edicion"></div>
				<div class="p_boton_edicion">
					<a id="btGuardar" title="Guardar" href="javascript:validarFamiliar();" class="boton_en_edicion" tabindex="16">
						<img src="imagenes/barra/ok_16x16.gif" width="16" height="16" align="top" />&nbsp;Guardar
					</a>
				</div>
			<?php
			}
			?>
		</div>

		<form action="abms/index.php" method="post" name="formFamiliar" id="formFamiliar">

			<input type="hidden" name="controlador" value="<?php echo $this->controlador; ?>" />
			<input type="hidden" name="f_legajo_emp" value="<?php echo $info_legajo['p_legajo']; ?>" />
			<input type="hidden" name="f_id" id="f_id" value="<?php echo $familiar['f_id']; ?>" />
			<input type="hidden" name="accion" value="<?php echo ($familiar['f_legajo_emp']) ? 'modificarFamiliar' : 'insertarFamiliar'; ?>" />

			<div style="height: 10px; font-size: 0;"></div>
			<div class="p_edicion_datos">
				<div class="p_edicion_datos_titulo_leyenda degradado">
					Legajo: <?php echo number_format($info_legajo['p_legajo'], 0, '', '.').'&nbsp;&nbsp;&nbsp;'. $info_legajo['p_apellido'].', '.$info_legajo['p_nombre']; ?>
				</div>
				<div class="h_edicion_fila">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Parentesco:</div>
					<div class="h_edicion_fila_valor">
						<select name="f_parentesco" id="f_parentesco" tabindex="1">
							<option value="Padre">Padre</option>
							<option value="Madre">Madre</option>
							<option value="C&oacute;nyuge">C&oacute;nyuge</option>
							<option value="Concubino / Unido de Hecho">Concubino / Unido de Hecho</option>
							<option value="Hijo">Hijo</option>
						</select>
					</div>
				</div>
				<div class="h_edicion_fila">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Apellido:</div>
					<div class="h_edicion_fila_valor">
						<input type="text" name="f_apellido_familiar" id="f_apellido_familiar" value="<?php echo $familiar['f_apellido']; ?>" size="53" tabindex="2" />
					</div>
				</div>
				<div class="h_edicion_fila">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Nombres:</div>
					<div class="h_edicion_fila_valor">
						<input type="text" name="f_nombre_familiar" id="f_nombre_familiar" value="<?php echo $familiar['f_nombre']; ?>" size="53" tabindex="3" />
					</div>
				</div>
				<div class="h_edicion_fila" id="contenedora_f_vive">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Vive:</div>
					<div class="h_edicion_fila_valor">
						<input type="radio" name="f_vive" id="op_si" value="Si" tabindex="4" />&nbsp;Si
						<input type="radio" name="f_vive" id="op_no" value="No" tabindex="5" />&nbsp;No
					</div>
				</div>

				<div class="h_edicion_fila" id="contenedora_f_sexo">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Sexo:</div>
					<div class="h_edicion_datos_valor">
						<input type="radio" name="f_sexo" id="op_M" value="M" tabindex="6" />&nbsp;Masculino
						<input type="radio" name="f_sexo" id="op_F" value="F" tabindex="7" />&nbsp;Femenino
					</div>
				</div>

				<div class="h_edicion_fila"
					id="contenedora_f_fecha_inicio_convivencia">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Fecha inicio convivencia:</div>
					<div class="h_edicion_fila_valor">
						<input type="text" name="f_fecha_inicio_convivencia" id="f_fecha_inicio_convivencia" value="<?php echo $this->formatearFecha($familiar['f_fecha_inicio_convivencia']); ?>" style="width: 80px;" maxlength="10" onkeyup="mascara(this,'/',patron,true);" tabindex="8" />
						<input type="image" id="img_f_fecha_inicio_convivencia" src="imagenes/calendario/calendario.gif" alt="Presione aqui para seleccionar la fecha." align="top" width="16" height="16">(formato dd/mm/yyyy, sin las barras)
					</div>
				</div>

				<div class="h_edicion_fila">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Documento N&deg;:</div>
					<div class="h_edicion_fila_valor">
						<input type="text" name="f_nro_documento" id="f_nro_documento" value="<?php echo $familiar['f_nro_documento']; ?>" style="width: 80px;" maxlength="8" onKeyPress="return soloEnteros(event);" tabindex="9" />&nbsp;(sin punto)
					</div>
				</div>

				<div class="h_edicion_fila">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Fecha de Nacimiento:</div>
					<div class="h_edicion_fila_valor">
						<input type="text" name="f_fecha_nac" id="f_fecha_nac" value="<?php if ($familiar['f_fecha_nac']){ echo $this->formatearFecha($familiar['f_fecha_nac']); } ?>" style="width: 80px;" maxlength="10" onkeyup="mascara(this,'/',patron,true);" tabindex="10" />
						<input type="image" id="img_f_fecha_nac" src="imagenes/calendario/calendario.gif" alt="Presione aqu&iacute; para seleccionar la fecha." align="top" width="16" height="16">&nbsp;(DDMMAAAA)
					</div>
				</div>

				<div class="h_edicion_fila" id="contenedora_f_nacionalidad">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Nacionalidad:</div>
					<div class="h_edicion_fila_valor">
						<input type="text" name="f_nacionalidad" id="f_nacionalidad" value="<?php echo ($familiar['f_nacionalidad']) ? $familiar['f_nacionalidad'] : "Argentino"; ?>" style="width: 80px;" tabindex="11" />
					</div>
				</div>

				<div class="h_edicion_fila" id="contenedora_f_discapacitado">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Es discapacitado:</div>
					<div class="h_edicion_fila_valor">
						<input type="radio" name="f_discapacitado" id="op_disc_si" value="Si" tabindex="12" />&nbsp;Si <input type="radio" name="f_discapacitado" id="op_disc_no" value="No" tabindex="13" />&nbsp;No
					</div>
				</div>

				<div class="h_edicion_fila" id="contenedora_f_estudios">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Estudios que cursa:</div>
					<div class="h_edicion_fila_valor">
						<input type="text" name="f_estudios" id="f_estudios" value="<?php echo $familiar['f_estudios']; ?>" size="62" tabindex="14" />
					</div>
				</div>

				<div class="h_edicion_fila_observaciones">
					<div class="h_edicion_fila_titulo">&nbsp;&nbsp;&nbsp;&nbsp;Observaciones:</div>
					<div class="h_edicion_fila_valor">
						<textarea id="f_observaciones" name="f_observaciones" rows="7" cols="80" style="width: 97%" tabindex="15"><?php echo ($familiar['f_observaciones']) ? trim($familiar['f_observaciones']) : ''; ?></textarea>
					</div>
				</div>
			</div>
		</form>
		<script>
			$('f_parentesco').value = "<?php echo ($familiar['f_parentesco']) ? $familiar['f_parentesco'] : 'Padre'; ?>";

			//CALENDARIO PARA LA FECHA DE NACIMIENTO
			var cal_f_fecha_nac = new Zapatec.Calendar.setup({
				inputField:"f_fecha_nac",
				ifFormat:"%d/%m/%Y",
				button:"img_f_fecha_nac",
				showsTime:false
			});

			setearCalendarioInicioConvivencia = function()
			{
				//CALENDARIO PARA LA FECHA DE INICIO DE CONVIVENCIA
				var cal_inicio_convivencia = new Zapatec.Calendar.setup({
					inputField:"f_fecha_inicio_convivencia",
					ifFormat:"%d/%m/%Y",
					button:"img_f_fecha_inicio_convivencia",
					showsTime:false
				});
			}

			mostrar_u_ocultar_Datos = function()
			{
				var op_parentesco = $('f_parentesco').value;
				switch (op_parentesco)
				{
					case "Padre":
					case "Madre":
					case "C"+'\u00f3'+"nyuge":
						$('contenedora_f_vive').setStyle('display', 'block');
						$('contenedora_f_nacionalidad').setStyle('display', 'block');
						$('contenedora_f_sexo').setStyle('display', 'none');
						$('contenedora_f_fecha_inicio_convivencia').setStyle('display', 'none');
						$('contenedora_f_discapacitado').setStyle('display', 'none');
						$('contenedora_f_estudios').setStyle('display', 'none');
						break;
					case "Concubino / Unido de Hecho":
						$('contenedora_f_vive').setStyle('display', 'none');
						$('contenedora_f_nacionalidad').setStyle('display', 'block');
						$('contenedora_f_sexo').setStyle('display', 'none');
						$('contenedora_f_fecha_inicio_convivencia').setStyle('display', 'block');

						setearCalendarioInicioConvivencia();

						$('contenedora_f_discapacitado').setStyle('display', 'none');
						$('contenedora_f_estudios').setStyle('display', 'none');
						break;
					case "Hijo":
						$('contenedora_f_vive').setStyle('display', 'none');
						$('contenedora_f_nacionalidad').setStyle('display', 'none');
						$('contenedora_f_sexo').setStyle('display', 'block');
						$('contenedora_f_fecha_inicio_convivencia').setStyle('display', 'none');
						$('contenedora_f_discapacitado').setStyle('display', 'block');
						$('contenedora_f_estudios').setStyle('display', 'block');
						break;
				}
			};

			$('f_parentesco').addEvents({

				'domready': function()
				{
					mostrar_u_ocultar_Datos();
				},
				'change': function()
				{
					mostrar_u_ocultar_Datos();
				},
				'keyup': function()
				{
					mostrar_u_ocultar_Datos();
				}

			});

			<?php
			if ( !empty($familiar['f_vive']) )
			{
			?>
				var op_vive = "<?php echo $familiar['f_vive']; ?>";
				switch (op_vive)
				{
					case "Si":
						$('op_si').checked = true;
						break;
					case "No":
						$('op_no').checked = true;
						break;
				}
			<?php
			}

			if ( !empty($familiar['f_sexo']) )
			{
			?>
				var op_sexo = "<?php echo $familiar['f_sexo']; ?>";
				switch (op_sexo)
				{
					case "M":
						$('op_M').checked = true;
						break;
					case "F":
						$('op_F').checked = true;
						break;
				}
			<?php
			}

			if ( !empty($familiar['f_discapacitado']) )
			{
			?>
				var op_discapacitado = "<?php echo $familiar['f_discapacitado']; ?>";
				switch (op_discapacitado)
				{
					case "Si":
						$('op_disc_si').checked = true;
						break;
					case "No":
						$('op_disc_no').checked = true;
						break;
				}
			<?php
			}
			?>

			$('f_apellido_familiar').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_f_fecha_nac').disabled = false;

					if( $('img_f_fecha_inicio_convivencia') )
					{
						$('img_f_fecha_inicio_convivencia').disabled = false;
					}
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITA EL CALENDARIO
						$('img_f_fecha_nac').disabled = true;

						if( $('img_f_fecha_inicio_convivencia') )
						{
							$('img_f_fecha_inicio_convivencia').disabled = true;
						}
					}
				}
			});

			$('f_nombre_familiar').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_f_fecha_nac').disabled = false;

					if( $('img_f_fecha_inicio_convivencia') )
					{
						$('img_f_fecha_inicio_convivencia').disabled = false;
					}
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITA EL CALENDARIO
						$('img_f_fecha_nac').disabled = true;

						if( $('img_f_fecha_inicio_convivencia') )
						{
							$('img_f_fecha_inicio_convivencia').disabled = true;
						}
					}
				}
			});

			$('f_nro_documento').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_f_fecha_nac').disabled = false;

					if( $('img_f_fecha_inicio_convivencia') )
					{
						$('img_f_fecha_inicio_convivencia').disabled = false;
					}
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITA EL CALENDARIO
						$('img_f_fecha_nac').disabled = true;

						if( $('img_f_fecha_inicio_convivencia') )
						{
							$('img_f_fecha_inicio_convivencia').disabled = true;
						}
					}
				}
			});

			$('f_nacionalidad').addEvents({
				keyup: function(){
					// SE HABILITA EL CALENDARIO
					$('img_f_fecha_nac').disabled = false;

					if( $('img_f_fecha_inicio_convivencia') )
					{
						$('img_f_fecha_inicio_convivencia').disabled = false;
					}
				},
				keydown: function(event){
					if(event.key == 'Enter')
					{
						// SE DESHABILITA EL CALENDARIO
						$('img_f_fecha_nac').disabled = true;

						if( $('img_f_fecha_inicio_convivencia') )
						{
							$('img_f_fecha_inicio_convivencia').disabled = true;
						}
					}
				}
			});

			if( $('f_estudios') )
			{
				$('f_estudios').addEvents({
					keyup: function(){
						// SE HABILITA EL CALENDARIO
						$('img_f_fecha_nac').disabled = false;

						if( $('img_f_fecha_inicio_convivencia') )
						{
							$('img_f_fecha_inicio_convivencia').disabled = false;
						}
					},
					keydown: function(event){
						if(event.key == 'Enter')
						{
							// SE DESHABILITA EL CALENDARIO
							$('img_f_fecha_nac').disabled = true;

							if( $('img_f_fecha_inicio_convivencia') )
							{
								$('img_f_fecha_inicio_convivencia').disabled = true;
							}
						}
					}
				});
			}

			setTimeout("$('f_parentesco').focus()", 75);
		</script>
	<?php
    }

    /**
     * Se obtiene el legajo del nombre del archivo de DDJJ
     * @param  [string]  $archivo_ddjj Nombre del archivo de la DDJJ
     * @return [integer]               Legajo
     */
    public function obtenerLegajoDelNombreArchivoDDJJ($archivo_ddjj) {

		$partes = explode("_", $archivo_ddjj);

		return $partes[1];
    }

    /**
     * Se obtiene la fecha del nombre del archivo de DDJJ
     * @param  [string]  $archivo_ddjj Nombre del archivo de la DDJJ
     * @return [string]                Fecha en formato dd/mm/yyyy
     */
    public function obtenerFechaDelNombreArchivoDDJJ($archivo_ddjj) {

		$partes = explode("_", $archivo_ddjj);
		$anio = $partes[2];
		$mes  = $partes[3];
		$aux  = explode(".", $partes[4]);
		$dia  = $aux[0];

		return $dia.'/'.$mes.'/'.$anio;
    }

    /**
     * Se editan las DDJJ de un legajo determinado
     *
     * @param string $datos_ĺegajo
     */
    public function editarDDJJ($datos_ĺegajo = null, $mensaje = '', $tipo_mensaje = '') {
    	// MENSAJE DEL RESULTADO DE LA OPERACION REALIZADA
    	$this->mostrarCartelResultado2($mensaje, $tipo_mensaje);

    	$cant_ddjj = (isset($datos_ĺegajo['ddjj'])) ? count($datos_ĺegajo['ddjj']) : 0;
    	?>
		<script>
			$("capaFondo").setStyle('visibility','hidden');
			$("capaVentana").setStyle('visibility','hidden');

			se_busca = false;
	    </script>

		<div class="p_buscador_legajo_solapas">
			<div class="p_buscador_legajo_solapas_titulo_e_input">
				Buscar por otro Legajo: <input type="text" name="f_legajo_solapa_ddjj" id="f_legajo_solapa_ddjj" value="" onKeyPress="return soloEnteros(event)" />
			</div>
			<div class="p_margen2_boton_edicion"></div>
			<div class="p_boton_edicion">
				<a title="Volver al listado de Personal." href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listar&pagina=<?php echo $_SESSION['filtro_personal']['pagina']; ?>&cmb_area=<?php echo $_SESSION['filtro_personal']['id_area']; ?>&cmb_cargo=<?php echo $_SESSION['filtro_personal']['nomenclador']; ?>&cmb_concejal=<?php echo $_SESSION['filtro_personal']['concejal']; ?>&f_legajo=<?php echo $_SESSION['filtro_personal']['legajo']; ?>&f_apellido_y_nombre=<?php echo $_SESSION['filtro_personal']['apellido_y_nombre']; ?>&f_activos=<?php echo $_SESSION['filtro_personal']['f_activos']; ?>', 'contenidoAjaxPrincipal');">
					<img src="imagenes/barra/volver.jpeg" width="14" height="14"align="top" />&nbsp;Volver
				</a>
			</div>
		</div>
		<!-- SOLAPAS -->
		<div class="p_solapas">
			<ul>
				<li id="p_solapa_Datos">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarFicha&legajo='+$('f_legajo_solapa_ddjj').value, 'contenidoAjaxEdicion');"><span>Datos Personales</span></a>
				</li>
				<li id="p_solapa_GrupoFamiliar" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarFamilia&legajo='+$('f_legajo_solapa_ddjj').value, 'contenidoAjaxEdicion');"><span>Grupo Familiar</span></a>
				</li>
				<li id="p_solapa_Estudios">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarEstudios&legajo='+$('f_legajo_solapa_ddjj').value, 'contenidoAjaxEdicion');"><span>Estudios</span></a>
				</li>
				<li id="p_solapa_ExperienciaLaboral" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarAntecedentesLaborales&legajo='+$('f_legajo_solapa_ddjj').value, 'contenidoAjaxEdicion');"><span>Experiencia Laboral</span></a>
				</li>
				<li id="p_solapa_DDJJ" class="actual">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarDDJJ&legajo='+$('f_legajo_solapa_ddjj').value, 'contenidoAjaxEdicion');"><span>Dec. Juradas</span></a>
				</li>
				<li id="p_solapa_Legajos" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editarLegajos&legajo='+$('f_legajo_solapa_ddjj').value, 'contenidoAjaxEdicion');"><span>Legajo</span></a>
				</li>
				<li id="p_solapa_Areas" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarAreas&legajo='+$('f_legajo_solapa_ddjj').value, 'contenidoAjaxEdicion');"><span>&Aacute;reas</span></a>
				</li>
				<li id="p_solapa_Cargos" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listarCargos&legajo='+$('f_legajo_solapa_ddjj').value, 'contenidoAjaxEdicion');"><span>Cargos</span></a>
				</li>
			</ul>
		</div>

		<form action="<?php echo $this->directorio; ?>/index.php?controlador=<?php echo $this->controlador; ?>&accion=guardarDDJJ" method="post" name="formDDJJ" id="formDDJJ" enctype="multipart/form-data">

			<input type="hidden" id="directorio" name="directorio" value="<?php echo $this->directorio; ?>" />
			<input type="hidden" id="controlador" name="controlador" value="<?php echo $this->controlador; ?>" />
			<input type="hidden" id="p_legajo" name="p_legajo" value="<?php echo $datos_ĺegajo['p_legajo']; ?>" />
			<input type="hidden" id="seguir_en_solapa_ddjj" name="seguir_en_solapa_ddjj" value="si" />

			<div class="p_edicion_seccion_ddjj">
				<div class="p_edicion_datos_titulo_leyenda degradado">
		       		Legajo: <?php echo number_format($datos_ĺegajo['p_legajo'], 0, '', '.').'&nbsp;&nbsp;&nbsp;'. $datos_ĺegajo['p_apellido'].', '.$datos_ĺegajo['p_nombre']; ?>
		       	</div>
				<div class="p_edicion_seccion_datos_ddjj">
					<div class="p_edicion_seccion_datos_ddjj_titulo">Fecha de DDJJ:</div>
					<div class="p_edicion_seccion_datos_ddjj_valor">
						<input type="text" name="fecha_ddjj" id="fecha_ddjj" value="<?php echo date("d/m/Y"); ?>" style="width: 80px;" maxlength="10" onkeyup="mascara(this,'/',patron,true);" tabindex="" />
						<input type="image" id="img_fecha_ddjj" src="imagenes/calendario/calendario.gif" alt="Presione aqui para seleccionar la fecha." align="top" width="16" height="19">&nbsp;(DDMMAAAA)
					</div>
					<?php

		       		// Sólo usuarios de perfil 1 Y 2 pueden cargar DDJJ
		       		if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 ) {
		       		?>
						<div id="inputFoto"	class="input_file_personalizado div_alineado_izquierda">
							<input type="file" name="imagen_ddjj" id="imagen_ddjj" value="" size="1" class="input_file" onChange="javascript:cargarDDJJ();" />
							&nbsp;<img src="imagenes/subir.gif" width="12" height="12" align="top" />&nbsp;Cargar DDJJ
						</div>
					<?php
		       		}
		       		?>
		       	</div>
			</div>

			<!-- Listado de DDJJ por fecha del legajo determinado -->
       		<?php
       		// Si pudo abrirse el directorio de imágenes de DDJJ respectivo
       		if ( $dir_abierto = opendir('../'.$this->directorio_ddjj) ) {
       		?>
	       		<div class="p_edicion_seccion_listado_ddjj">
					<table>
						<thead class="e_tabla_titulos">
							<tr>
							<?php
							// SÓLO EL PERFIL 1 PUEDE ELIMINAR
							echo ( $_SESSION['perfil3'] == 1 ) ? '<th class="orden_link" width="16">&nbsp;</th>' : '';
							?>
							    <th nowrap class="orden_link" width="80">Fecha</th>
								<th nowrap class="orden_link" width="100">Declaraci&oacute;n Jurada</th>
							</tr>
						</thead>
						<tbody class="e_tabla_texto">
							<?php
				    		for ($i=0; $i < $cant_ddjj; $i++) {
								$ddjj = &$datos_ĺegajo['ddjj'][$i];

								$titulo_ddjj = ($i == 0) ? '<strong>Actual</strong>': 'Anterior';

								$fecha_ddjj = $this->obtenerFechaDelNombreArchivoDDJJ($ddjj);
				    		?>
								<tr>
									<?php
									// Sólo el perfil Administrador puede eliminar
									if ($_SESSION['perfil3'] == 1) {
									?>
										<td width="16">
											<a title="Eliminar DDJJ" href="javascript:if (confirm('¿Desea eliminar la DDJJ del <?php echo $fecha_ddjj; ?>?')){refrescar('<?php echo $this->directorio; ?>/index.php?controlador=<?php echo $this->controlador; ?>&accion=eliminarDDJJ&legajo=<?php echo $datos_ĺegajo['p_legajo']; ?>&nombre_imagen_ddjj=<?php echo $ddjj; ?>', 'contenidoAjaxEdicion');};">
												<img src="imagenes/barra/delete_16x16.gif" width="15" height="15" align="top" />
											</a>
										</td>
									<?php
									}
									?>
									<td nowrap class="p_edicion_seccion_listado_ddjj_anio"><?php echo $fecha_ddjj; ?></td>
									<td nowrap class="p_edicion_seccion_listado_ddjj_enlace">
										<a href="<?php echo $this->directorio_ddjj.$ddjj.'?v='.date('Ymd_His'); ?>" target="_blank" title="Ver DDJJ"><?php echo $titulo_ddjj; ?></a>
									</td>
								</tr>
							<?php
							}
							?>
						<tbody>
					</table>
				</div>
			<?php
				// Se cierra el directorio de imágenes de DDJJ
				closedir($dir_abierto);
			}
			?>
		</form>
		<script>
			var perfil_usuario = <?php echo $_SESSION['perfil3']; ?>;

     		function cargarDDJJ() {
	       	    // Si no se ha seleccionado una fecha
	       	    if ( $('fecha_ddjj').value == '' )
	       	    	alert("Debe seleccionar una fecha para la DDJJ.");
	       	    else
	       			$('formDDJJ').submit();// Se envía el formulario
	       	}

       		// Sólo usuarios de perfil 1 y 2 pueden visualizar la información personal del empleado
       		if ( perfil_usuario == 1 || perfil_usuario == 2 )
	       		$('p_solapa_Datos').setProperty('class', '');

       		// Calendario para la fecha de la DDJJ a cargar
			var cal_fecha_ddjj = new Zapatec.Calendar.setup({
				inputField: "fecha_ddjj",
				ifFormat: "%d/%m/%Y",
				button: "img_fecha_ddjj",
				showsTime:false
			});

       		// Se muestra el legajo en el campo del buscador respectivo
       		$('f_legajo_solapa_ddjj').value = $('p_legajo').value;

       		$('f_legajo_solapa_ddjj').addEvent('keydown', function(event) {
       			if(event.key == 'Enter')
       				// Si hay un legajo a buscar
       				if( $('f_legajo_solapa_ddjj').value != '' )
       					refrescar('abms/index.php?controlador=personal&accion=editarDDJJ&legajo='+$('f_legajo_solapa_ddjj').value, 'contenidoAjaxEdicion');
       		});

       		setTimeout("$('fecha_ddjj').focus()",75);
       	</script>
	<?php
    }

    // 10/05/2022 XXXX ---------------------------------------------------

    /**
     * Se editan los Legajos Digitalizados de un legajo determinado
     *
     * @param string $datos_ĺegajo
     */
    public function editarLegajos($datos_ĺegajo = null, $mensaje = '', $tipo_mensaje = '') {

    	$this->mostrarCartelResultado2($mensaje, $tipo_mensaje);

    	$cant_legajos_digitalizados = (isset($datos_ĺegajo['legajos'])) ? count($datos_ĺegajo['legajos']) : 0;
    	?>
		<script>
			$("capaFondo").setStyle('visibility','hidden');
			$("capaVentana").setStyle('visibility','hidden');

			se_busca = false;
	    </script>

		<div class="p_buscador_legajo_solapas">
			<div class="p_buscador_legajo_solapas_titulo_e_input">
				Buscar por otro Legajo: <input type="text" name="f_legajo_solapa_leg_digitalizado" id="f_legajo_solapa_leg_digitalizado" value="" onKeyPress="return soloEnteros(event)" />
			</div>
			<div class="p_margen2_boton_edicion"></div>
			<div class="p_boton_edicion">
				<a title="Volver al listado de Personal." href="javascript:refrescar('abms/index.php?controlador=<?= $this->controlador; ?>&accion=listar&pagina=<?= $_SESSION['filtro_personal']['pagina']; ?>&cmb_area=<?= $_SESSION['filtro_personal']['id_area']; ?>&cmb_cargo=<?= $_SESSION['filtro_personal']['nomenclador']; ?>&cmb_concejal=<?= $_SESSION['filtro_personal']['concejal']; ?>&f_legajo=<?= $_SESSION['filtro_personal']['legajo']; ?>&f_apellido_y_nombre=<?= $_SESSION['filtro_personal']['apellido_y_nombre']; ?>&f_activos=<?= $_SESSION['filtro_personal']['f_activos']; ?>', 'contenidoAjaxPrincipal');">
					<img src="imagenes/barra/volver.jpeg" width="14" height="14"align="top" />&nbsp;Volver
				</a>
			</div>
		</div>
		<!-- SOLAPAS -->
		<div class="p_solapas">
			<ul>
				<li id="p_solapa_Datos">
					<a href="javascript:refrescar('abms/index.php?controlador=<?= $this->controlador; ?>&accion=editarFicha&legajo='+$('f_legajo_solapa_leg_digitalizado').value, 'contenidoAjaxEdicion');"><span>Datos Personales</span></a>
				</li>
				<li id="p_solapa_GrupoFamiliar" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?= $this->controlador; ?>&accion=listarFamilia&legajo='+$('f_legajo_solapa_leg_digitalizado').value, 'contenidoAjaxEdicion');"><span>Grupo Familiar</span></a>
				</li>
				<li id="p_solapa_Estudios">
					<a href="javascript:refrescar('abms/index.php?controlador=<?= $this->controlador; ?>&accion=listarEstudios&legajo='+$('f_legajo_solapa_leg_digitalizado').value, 'contenidoAjaxEdicion');"><span>Estudios</span></a>
				</li>
				<li id="p_solapa_ExperienciaLaboral" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?= $this->controlador; ?>&accion=listarAntecedentesLaborales&legajo='+$('f_legajo_solapa_leg_digitalizado').value, 'contenidoAjaxEdicion');"><span>Experiencia Laboral</span></a>
				</li>
				<li id="p_solapa_DDJJ" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?= $this->controlador; ?>&accion=editarDDJJ&legajo='+$('f_legajo_solapa_leg_digitalizado').value, 'contenidoAjaxEdicion');"><span>Dec. Juradas</span></a>
				</li>
				<li id="p_solapa_Legajos" class="actual">
					<a href="javascript:refrescar('abms/index.php?controlador=<?= $this->controlador; ?>&accion=editarLegajos&legajo='+$('f_legajo_solapa_leg_digitalizado').value, 'contenidoAjaxEdicion');"><span>Legajo</span></a>
				</li>
				<li id="p_solapa_Areas" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?= $this->controlador; ?>&accion=listarAreas&legajo='+$('f_legajo_solapa_leg_digitalizado').value, 'contenidoAjaxEdicion');"><span>&Aacute;reas</span></a>
				</li>
				<li id="p_solapa_Cargos" class="">
					<a href="javascript:refrescar('abms/index.php?controlador=<?= $this->controlador; ?>&accion=listarCargos&legajo='+$('f_legajo_solapa_leg_digitalizado').value, 'contenidoAjaxEdicion');"><span>Cargos</span></a>
				</li>
			</ul>
		</div>

		<form action="<?= $this->directorio; ?>/index.php?controlador=<?= $this->controlador; ?>&accion=guardarLegajosDigitalizados"
			method="post" name="formLegajosDigitalizados" id="formLegajosDigitalizados" enctype="multipart/form-data">

			<input type="hidden" id="directorio" name="directorio" value="<?= $this->directorio; ?>" />
			<input type="hidden" id="controlador" name="controlador" value="<?= $this->controlador; ?>" />
			<input type="hidden" id="p_legajo" name="p_legajo" value="<?= $datos_ĺegajo['p_legajo']; ?>" />
			<input type="hidden" id="seguir_en_solapa_legajos_digitalizados" name="seguir_en_solapa_legajos_digitalizados" value="si" />

			<div class="p_edicion_seccion_ddjj">
				<div class="p_edicion_datos_titulo_leyenda degradado">
		       		Legajo: <?= number_format($datos_ĺegajo['p_legajo'], 0, '', '.').'&nbsp;&nbsp;&nbsp;'. $datos_ĺegajo['p_apellido'].', '.$datos_ĺegajo['p_nombre']; ?>
		       	</div>
				<div class="p_edicion_seccion_datos_ddjj">
					<div class="p_edicion_seccion_datos_ddjj_titulo">Fecha:</div>
					<div class="p_edicion_seccion_datos_ddjj_valor">

						<input  type="text" name="fecha_legajo_digitalizado" id="fecha_legajo_digitalizado"
								value="<?= date("d/m/Y"); ?>" style="width: 80px;" maxlength="10"
								onkeyup="mascara(this,'/',patron,true);" />

						<input  type="image" id="img_fecha_legajo_digitalizado"
								src="imagenes/calendario/calendario.gif"
								alt="Presione aqui para seleccionar la fecha."
								align="top" width="16" height="19">&nbsp;(DDMMAAAA)
					</div>
					<?php

		       		// Sólo usuarios de perfil 1 Y 2 pueden cargar
		       		if ( $_SESSION['perfil3'] == 1 || $_SESSION['perfil3'] == 2 ) {
		       		?>
						<div id="inputFoto"	class="input_file_personalizado div_alineado_izquierda">
							<input  type="file"
									name="legajo_digitalizado"
									id="legajo_digitalizado"
									value="" size="1"
									class="input_file"
									accept="application/pdf"
									onChange="javascript:cargarLegajoDigitalizado();" />
							&nbsp;
							<img src="imagenes/subir.gif" width="12" height="12" align="top" />
							&nbsp;Cargar Legajo
						</div>
						&nbsp;&nbsp;
						<a  href="<?= $this->directorio; ?>/index.php?controlador=<?= $this->controlador; ?>&accion=unificarLegajoDigitalizado&legajo=<?= $datos_ĺegajo['p_legajo']; ?>"
							class="input_file_personalizado"
							style="width: 190px;"
							title="Descargar Legajo Completo"
							target="_blank"
						>
							<img src="imagenes/barra/bajar.gif" width="12" height="12" align="top" />
							&nbsp;Descargar Legajo Completo
						</a>
					<?php
		       		}
		       		?>
		       	</div>
			</div>

			<!-- Listado de Legajos digitalizados del legajo determinado -->
       		<?php
       		// Si pudo abrirse el directorio
       		if ( $dir_abierto = opendir('../'.$this->directorio_legajos) ) {
       		?>
	       		<div class="p_edicion_seccion_listado_ddjj">
					<table>
						<thead class="e_tabla_titulos">
							<tr>
							<?php
							// SÓLO EL PERFIL 1 PUEDE ELIMINAR
							echo ( $_SESSION['perfil3'] == 1 ) ? '<th class="orden_link" width="16">&nbsp;</th>' : '';
							?>
							    <th nowrap class="orden_link" width="80">Fecha</th>
							    <th nowrap class="orden_link" width="80">Hora</th>
								<th nowrap class="orden_link" width="100">Legajos digitalizados</th>
							</tr>
						</thead>
						<tbody class="e_tabla_texto">
							<?php
				    		for ($i=0; $i < $cant_legajos_digitalizados; $i++) {

								$legajo_digitalizado = &$datos_ĺegajo['legajos'][$i];

								$fecha_legajo_digitalizado = $this->obtenerFechaDeLegajoDigitalizado($legajo_digitalizado);

								$hora_legajo_digitalizado = $this->obtenerHoraDeLegajoDigitalizado($legajo_digitalizado);
				    		?>
								<tr>
									<?php
									// Sólo el perfil Administrador puede eliminar
									if ($_SESSION['perfil3'] == 1) {
									?>
										<td width="16">
											<a  title="Eliminar Legajo digitalizado"
												href="javascript:if (confirm('¿Desea eliminar el Legajo digitalizado del <?= $fecha_legajo_digitalizado; ?>, <?=$hora_legajo_digitalizado;?>?')){refrescar('<?= $this->directorio; ?>/index.php?controlador=<?= $this->controlador; ?>&accion=eliminarLegajoDigitalizado&legajo=<?= $datos_ĺegajo['p_legajo']; ?>&nombre_legajo_digitalizado=<?= $legajo_digitalizado; ?>', 'contenidoAjaxEdicion');};">
												<img src="imagenes/barra/delete_16x16.gif" width="15" height="15" align="top" />
											</a>
										</td>
									<?php
									}
									?>
									<td nowrap class="p_edicion_seccion_listado_ddjj_anio">
										<?= $fecha_legajo_digitalizado; ?>
									</td>
									<td nowrap class="p_edicion_seccion_listado_ddjj_anio">
										<?= $hora_legajo_digitalizado; ?>
									</td>
									<td nowrap class="p_edicion_seccion_listado_ddjj_enlace">
										<a  href="<?= $this->directorio_legajos.$legajo_digitalizado.'?v='.date('Ymd_His'); ?>"
											target="_blank"
											title="Ver Legajo digitalizado">Ver</a>
									</td>
								</tr>
							<?php
							}
							?>
						<tbody>
					</table>
				</div>
			<?php
				// Se cierra el directorio de imágenes de DDJJ
				closedir($dir_abierto);
			}
			?>
		</form>
		<script>
			var perfil_usuario = <?= $_SESSION['perfil3']; ?>;

     		function cargarLegajoDigitalizado() {
	       	    // Si no se ha seleccionado una fecha
	       	    if ( $('fecha_legajo_digitalizado').value == '' )
	       	    	alert("Debe seleccionar una fecha para el Legajo digitalizado.");
	       	    else {
	       	    	let filename = $('legajo_digitalizado').value;

	       	    	let exp_reg = new RegExp('[^0-9]+'+$('p_legajo').value+'[^0-9]+');

					if (exp_reg.test(filename)) {
						// Se envía el formulario
						$('formLegajosDigitalizados').submit();
	       			} else {
	       				alert('No se puede subir el documento seleccionado ya que no contiene el número de legajo '+$('p_legajo').value+' en su nombre.');
	       			}
	       	    }
	       	}

       		// Sólo usuarios de perfil 1 y 2 pueden visualizar la información personal del empleado
       		if ( perfil_usuario == 1 || perfil_usuario == 2 )
	       		$('p_solapa_Datos').setProperty('class', '');

       		// Calendario para la fecha de la DDJJ a cargar
			var cal_fecha_legajo_digitalizado = new Zapatec.Calendar.setup({
				inputField: "fecha_legajo_digitalizado",
				ifFormat: "%d/%m/%Y",
				button: "img_fecha_legajo_digitalizado",
				showsTime:false
			});

       		// Se muestra el legajo en el campo del buscador respectivo
       		$('f_legajo_solapa_leg_digitalizado').value = $('p_legajo').value;

       		$('f_legajo_solapa_leg_digitalizado').addEvent('keydown', function(event) {
       			if(event.key == 'Enter')
       				// Si hay un legajo a buscar
       				if( $('f_legajo_solapa_leg_digitalizado').value != '' )
       					refrescar('abms/index.php?controlador=personal&accion=editarLegajos&legajo='+$('f_legajo_solapa_leg_digitalizado').value, 'contenidoAjaxEdicion');
       		});

       		setTimeout("$('fecha_legajo_digitalizado').focus()",75);
       	</script>
	<?php
    }

    /**
     * Se obtiene la fecha del nombre del archivo del Legajo digitalizado
     * @param  [string]  $legajo_digitalizado Nombre del archivo de la DDJJ
     * @return [string]                Fecha en formato dd/mm/yyyy
     */
    public function obtenerFechaDeLegajoDigitalizado($legajo_digitalizado) {

		$partes = explode("_", $legajo_digitalizado);
		$anio = $partes[1];
		$mes  = $partes[2];
		$aux  = explode(".", $partes[3]);
		$dia  = $aux[0];

		return $dia.'/'.$mes.'/'.$anio;
    }

    /**
     * Se obtiene la hora del nombre del archivo del Legajo digitalizado
     * @param  [string]  $legajo_digitalizado Nombre del archivo de la DDJJ
     * @return [string]                Hora
     */
    public function obtenerHoraDeLegajoDigitalizado($legajo_digitalizado) {

		$partes = explode("_", $legajo_digitalizado);
		$hora = $partes[4];
		$min  = $partes[5];
		$aux  = explode(".", $partes[6]);
		$seg  = $aux[0];

		return $hora.':'.$min.':'.$seg;
    }

}
?>

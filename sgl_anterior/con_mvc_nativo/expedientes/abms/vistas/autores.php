<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class VistaAutores
{
	public function listarModal($datos)
	{
	?>
		<div class="ub_listado">
			<table width="100%" class="e_tabla_texto">
		   		<thead class="e_tabla_titulos">
		  			<tr>
						<th>C&oacute;digo</th>
		  				<th>Descripci&oacute;n</th>
		  			</tr>
		  		</thead>
				<tbody class="e_cuerpo_scrolleable">
					<?php
					$n = count($datos);
					for ($m=0; $m < $n; $m++)
					{
						$dato = &$datos[$m];
					?>
						<tr id="am_fila<?php echo $m; ?>" onclick="javascript:volverModal('autor_tipo', 'autor_codigo', 'autor_descripcion', '<?php echo $dato['tipo_grp']; ?>', '<?php echo $dato['codigo_grp']; ?>', '<?php echo $dato['descripcion_grp']; ?>');" onmouseover="javascript:$('am_fila<?php echo $m; ?>').setStyle('background-color','#DDDDDD');" onmouseout="javascript:$('am_fila<?php echo $m; ?>').setStyle('background-color','#fff');"> 
							<td style="width:50px;padding-left:10px;"><?php echo $dato['codigo_grp']; ?></td>
							<td style="width:50px;padding-left:10px;"><?php echo $dato['descripcion_grp']; ?></td>
						</tr>
					<?php
					}
					?>		
				</tbody>
			</table>
		</div>
	<?php
	}
	
}
?>

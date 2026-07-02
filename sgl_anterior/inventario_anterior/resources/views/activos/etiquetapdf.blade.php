<style type="text/css">
	table {
		/* Se define el alto de la etiqueta
		de 3.5cm, considerando el alto + el borde */
		height: 132px;
		border: 2px solid silver;
	}
	#etiqueta_info {
		padding-top: 10px;
		vertical-align: top;
	}
	ul {
		list-style: none;
		margin: 0 5px;
		padding: 0;
		font-size: 12px;
	}
	img {
		width: 110px;
		height: 110px;
	}
</style>
<table>
	<tr>
		<td id="etiqueta_info">
			<ul>
				<li><strong>Origen</strong>: {{ ($activo->orden_compra == "") ? "---" : sprintf('%s %s', $activo->tipo_origen, $activo->orden_compra) }}</li>
				<li><strong>N&deg; Inv.</strong>: {{ ($activo->nro_inventario == "") ? "---" : $activo->nro_inventario }}</li>
				{{-- Si el Activo es una PC (Escritorio|Portátil|Servidor) --}}
				@if ($activo->tipo_id < 4)
					<li>{{ $activo->cpu }}</li>
					<li>{{ $activo->memoria }} - {{ $activo->hd_marca }} {{ $activo->hd_capacidad }}</li>
					<li>{{ $activo->motherboard }}</li>
					<li><strong>LAN</strong>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $activo->ethernet_mac }}</li>
					<li><strong>WLAN</strong>:&nbsp;{{ $activo->wireless_mac }}</li>
				@else
					<li><strong>Marca</strong>: {{ ($activo->marca == "") ? "---" : $activo->marca }}</li>
					<li><strong>Modelo</strong>: {{ ($activo->modelo == "") ? "---" : $activo->modelo }}</li>
				@endif
				<li><strong>N&deg; Serie</strong>: {{ ($activo->nro_serie == "") ? "---" : $activo->nro_serie }}</li>
			</ul>
		</td>
		<td>
			<?php
				// Convierto la imagen a base64 para que quede embebida en el pdf
                $img_file_content = file_get_contents(route('qr.activo', [ 'activo' => $activo]));
                $img_file_base64 = sprintf('data:image/png;base64,%s',
                    base64_encode($img_file_content)
                );
            ?>
            <img src="{{ $img_file_base64 }}" alt="QR del Activo" />
		</td>
	</tr>
</table>
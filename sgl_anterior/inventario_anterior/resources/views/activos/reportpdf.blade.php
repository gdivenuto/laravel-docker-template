<!DOCTYPE html>
<html lang="es">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        {{-- CSS --}}
        <style>
            {{-- Incrusto la hoja de estilo en el documento pdf generado --}}
            {{ file_get_contents(public_path('css/bootstrap.min.pdfreport.css')) }}
            {{ file_get_contents(public_path('css/pdf.css')) }}
        </style>
    </head>
    <body >
        <h4 class="align-middle">
            <?php
                // Convierto la imagen a base64 para que quede embebida en el pdf
                $img_file_path = public_path('img/escudo_hcd_35x40.png');
                $img_file_content = file_get_contents($img_file_path);
                $img_file_base64 = sprintf('data:image/%s;base64,%s',
                    pathinfo($img_file_path, PATHINFO_EXTENSION),
                    base64_encode($img_file_content)
                );
            ?>
            <img class="img-responsive" src="{{ $img_file_base64 }}" alt="" />
            &nbsp;&nbsp;Honorable Concejo Deliberante&nbsp;-&nbsp;LISTADO DE ACTIVOS
        </h4>
        <table class="table table-borderless table-sm pdf-tabla">
            <thead>
                <tr>
                    <th>TIPO</th>
                    <th>NOMBRE</th>
                    <th>ID</th>
                    <th class="text-nowrap">REG PAT</th>
                    <th>MARCA</th>
                    <th>MODELO</th>
                    <th>ORIGEN</th>
                    <th>RESPONSABLE</th>
                    <th>&Aacute;REA</th>
                    <th>UBICACI&Oacute;N</th>
                </tr>
            </thead>
            <tbody>
            	@foreach($activos as $a)
        			<tr>
        				<td class="text-nowrap">{{ $a->{'activo_tipos.nombre'} }}</td>
        				<td class="text-nowrap">{{ $a->{'activos.nombre_equipo'} }}</td>
        				<td>{{ $a->{'activos.id'} }}</td>
        				<td>{{ $a->{'activos.nro_inventario'} }}</td>
        				<td class="text-nowrap">{{ $a->{'activos.marca'} }}</td>
        				<td class="text-nowrap">{{ $a->{'activos.modelo'} }}</td>
        				<td class="text-nowrap">{{ ($a->{'activos.orden_compra'} == "") ? "---" : sprintf('%s %s', $a->{'activos.tipo_origen'}, $a->{'activos.orden_compra'}) }}</td>
        				<td class="text-nowrap">{{ sprintf('%s, %s', $a->{'responsables.apellido'}, $a->{'responsables.nombre'}) }}</td>
        				<td>{{ $a->{'areas.nombre'} }}</td>
        				<td>{{ $a->{'activos.ubicacion'} }}</td>
                    </tr>
                    <tr>
                        <td class="pdf-fila-borde-inferior" colspan="10">{{ $a->{'activos.observaciones'} }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>

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
            &nbsp;&nbsp;Honorable Concejo Deliberante
        </h4>
        <table class="table table-borderless table-sm pdf-tabla pdf-ficha-tabla">
            <caption>Activo N&deg; {{ $activo->id }}: {{ $activo->activo_tipo->nombre }}</caption>
            <tbody>
                <tr>
                    <td class="pdf-ficha-activo-titulo pdf-ficha-responsable">RESPONSABLE:&nbsp;</td>
                    <td><strong>{{ $activo->legajo }} | {{ $activo->responsable->nombre_completo }}</strong></td>
                </tr>
                <tr>
                    <td class="pdf-ficha-activo-titulo">ÁREA:&nbsp;</td>
                    <td>{{ ($activo->responsable) ? $activo->responsable->area->nombre : '' }}</td>
                </tr>
                <tr>
                    <td class="pdf-ficha-activo-titulo">UBICACIÓN:&nbsp;</td>
                    <td>{{ $activo->ubicacion }}</td>
                </tr>
                <tr>
                    <td class="pdf-ficha-activo-titulo">FECHA ALTA:&nbsp;</td>
                    <td>{{ date('d/m/Y', strtotime($activo->fecha_alta)) }}</td>
                </tr>
                <?php
                // Se obtienen los atributos del Activo (pares nombre_atributo:valor)
                $atributos_del_activo = $activo->getAttributes();

                // Se obtienen los atributos del Tipo del Activo (pares has_[nombre atributo]: valor (0|1) para definir si lo posee o no)
                $atributos_del_tipo_del_activo = $activo->activo_tipo->getAttributes();

                // Se obtienen las claves del array de todos los atributos del Activo
                $atributos_activo_tipo = array_keys($atributos_del_tipo_del_activo);
                
                // Por cada atributo del Activo
                foreach ($atributos_del_activo as $k => $v)
                    // Si el Tipo del activo posee a dicho atributo
                    if (in_array('has_'.$k, $atributos_activo_tipo))
                        // Si el valor del atributo del Tipo define que lo posee (valor 1 = true)
                        if ($activo->activo_tipo->getAttribute('has_'.$k))
                            // Se saltea la Ubicación, la Fecha Alta y las Observaciones por haberse mostrado previamente
                            if ($k != 'ubicacion' && $k != 'fecha_alta' && $k != 'observaciones') {
                                // A la clave se le reemplaza el guión bajo por un espacio 
                                // y el título se convierte a mayúscula
                            ?>
                                <tr>
                                    <td class='pdf-ficha-activo-titulo'>
                                        {{ strtoupper(str_replace('_', ' ', $k)) }}:&nbsp;
                                    </td>
                                    <td>{{ ($v != '') ? $v : '---' }}</td>
                                </tr>
                            <?php
                            }
                ?>
                <tr>
                    <td colspan="2" class="pdf-ficha-observaciones">&nbsp;{{ $activo->observaciones }}</td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
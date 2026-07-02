<!DOCTYPE html>
<html lang="es">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        {{-- CSS --}}
        <style>
            {{-- Incrusto la hoja de estilo en el documento pdf generado --}}
            {{ file_get_contents(public_path('css/bootstrap.min.pdfreport.css')) }}
            {{ file_get_contents(public_path('css/pdf.css')) }}

            /** Definición de márgenes **/
            @page {
                margin: 100px 50px 100px 50px;
            }

            header {
                position: fixed;
                top: -70px;
                left: 0px;
                right: 0px;
                height: 80px;
            }

            footer {
                position: fixed; 
                bottom: -70px; 
                left: 0px; 
                right: 0px;
                height: 80px; 
            }

        </style>
    </head>
    <body >
        <!-- Define header and footer blocks before your content -->
        <header>
            <table class="table table-borderless table-sm pdf-tabla">
                <thead>
                    <tr>
                        <th colspan="2"><div style="text-align: center;">INVENTARIO AL {{ date('d-m-Y') }}</div></th>
                    </tr>
                    <tr>
                        <th>SECRETAR&Iacute;A: {{ $secretaria }}</th>
                        <th><div style="text-align: right;">DEPENDENCIA: {{ $dependencia }}</div></th>
                    </tr>
                </thead>
            </table>
        </header>

        <footer>
            <table class="table table-borderless table-sm">
                <thead>
                    <tr>
                        <th width="30%"><br><br><div class="pdf-firmas">Firma del Subresponsable del &aacute;rea</div></th>
                        <th width="40%">&nbsp;</th>
                        <th width="30%"><br><br><div class="pdf-firmas">Firma del Secretario del &aacute;rea</div></th>
                    </tr>
                </thead>
            </table>
        </footer>

        <!-- Wrap the content of your PDF inside a main tag -->
        <main>
            <table class="table table-borderless table-sm pdf-tabla">
                <thead>
                    <tr>
                        <th>Nº DE INVENTARIO</th>
                        <th>ORIGEN</th>
                        <th>DESCRIPCI&Oacute;N</th>
                        <th>CARACTER&Iacute;STICAS</th>
                        <th>TITULARIDAD</th>
                        <th>ESTADO</th>
                        <th>CONDICI&Oacute;N DE USO</th>
                    </tr>
                </thead>
                <tbody>
                	@foreach($activos as $a)
            			<tr>
            				<td class="pdf-fila-borde-inferior">
                                @if (isset($a->nro_inventario) && !empty($a->nro_inventario))
                                    {{ $a->nro_inventario }}
                                @else
                                    ---
                                @endif
                            </td>
                            <td class="pdf-fila-borde-inferior">
                                @if (isset($a->orden_compra) && !empty($a->orden_compra))
                                    {{ $a->tipo_origen }} - {{ $a->orden_compra }}
                                @else
                                    ---
                                @endif
                            </td>
                            <td class="pdf-fila-borde-inferior">
                                @if (in_array($a->tipo_id, config('defaults.idActivoTipoOtros')))
                                    {{ $a->nombre_equipo }}
                                @else
                                    {{ $a->nombre }}
                                @endif
                            </td>
                            <td class="pdf-fila-borde-inferior">{{ $a->desc_dinamica }}</td>
                            <td class="pdf-fila-borde-inferior">{{ $a->titularidad }} - {{ $a->titularidad_desc }}</td>
                            <td class="pdf-fila-borde-inferior">{{ $a->estado }} - {{ $a->estado_desc }}</td>
                            <td class="pdf-fila-borde-inferior">{{ $a->condicion_uso }} - {{ $a->condicion_uso_desc }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </main>

        <script type="text/php">
            if (isset($pdf)) {
                $text = "Pág. {PAGE_NUM} de {PAGE_COUNT}";
                $size = 8;
                $font = $fontMetrics->getFont("Verdana");
                $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
                $x = ($pdf->get_width() - $width) / 2;
                $y = $pdf->get_height() - 35;
                $pdf->page_text($x, $y, $text, $font, $size);
            }
        </script>

    </body>
</html>
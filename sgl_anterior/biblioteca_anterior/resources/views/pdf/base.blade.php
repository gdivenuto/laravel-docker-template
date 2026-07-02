<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    {{-- CSS --}}
    <style>
        {{-- Incrusto la hoja de estilo en el documento pdf generado --}}
        {{-- file_get_contents(public_path('css/pdf.css')) --}}

        /** Definición de márgenes **/
        @page {
            margin: 50px 50px 100px 50px;
        }

        .page-break {
            page-break-after: always;
        }
        
        .timestamp {
            text-align: right;
            margin: 0;
            font-size: smaller;
        }

        p { 
            margin: 5;
        }
    </style>
</head>
<body>

    <table>
        <tbody>
            <tr>
                <td>
                    <?php
                        // Convierto la imagen a base64 para que quede embebida en el pdf
                        $img_file_path = public_path('img/escudo_hcd_35x40.png');
                        $img_file_content = file_get_contents($img_file_path);
                        $img_file_base64 = sprintf('data:image/%s;base64,%s',
                            pathinfo($img_file_path, PATHINFO_EXTENSION),
                            base64_encode($img_file_content)
                        );
                    ?>
                    <img src="{{ $img_file_base64 }}" alt="" />
                </td>
                <td>
                    <h2>&nbsp;&nbsp;Honorable Concejo Deliberante&nbsp;-&nbsp;{{ config('app.name', 'Biblioteca-HCD') }}</h2>
                </td>
            </tr>
        </tbody>
    </table>
    
    <h3>{{ $titulo }}</h3>
    
    @yield('content')

    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $text = "Pagina $PAGE_NUM de $PAGE_COUNT | {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}";
                $size = 8;
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
                $x = ($pdf->get_width() - $width) / 2;
                $y = $pdf->get_height() - 35;
                $pdf->text($x, $y, $text, $font, $size);
            ');
        }
    </script>

</body>
</html>

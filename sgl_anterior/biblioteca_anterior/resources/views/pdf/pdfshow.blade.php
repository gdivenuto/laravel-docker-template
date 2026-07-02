@extends('pdf.base')

@section('content')

    <table>
        <tbody>
            <tr>
                <!--
                <td>
                    <img src="data:image/png;base64,{!! base64_encode($qr) !!}"></img>
                </td>
                -->
                <td>
                    <p><strong>Tema:</strong> {{  ucfirst($normas_db) }}</p>
                    
                    <p><strong>Nro. Interno: </strong>
                        {{ (!empty($norma->nro_hcd)) ? $norma->nro_hcd : '(no posee)' }}
                    </p>
                    <p>
                        <strong>Expediente H.C.D.: </strong>
                        {{ (!empty($norma->hcd_exped)) ? $norma->hcd_exped : '(no posee)' }}
                        
                        {{-- 2023-06-12 Se retira la relación, porque ahora es un campo de 'normas'
                        @forelse ($norma->hcd_expedientes as $hcde)
                            {{ $hcde->hcd_exped }}
                        @empty
                            (no posee)
                        @endforelse
                        --}}
                        
                        @if (! empty($norma->bloque))
                            &nbsp;<strong>D&iacute;gito:</strong> {{ Str::upper($norma->bloque) }}
                        @endif
                    </p>

                    <p><strong>Acto:</strong> {{ $norma->acto_desc }} {{ $norma->nro }}</p>
                </td>
            </tr>
        </tbody>
    </table>

<!--
    <p>
        <ul>
            <li><strong>Texto:</strong> {{ $norma->url_html }}</li>
            <li><strong>PDF:</strong> {{ $norma->url_pdf }}</li>
            <li><strong>Actualizado:</strong> {{ $norma->url_actualizado_html }}</li>
            <li><strong>PDF Actualizado:</strong> {{ $norma->url_actualizado_pdf }}</li>
        </ul>
    </p>
-->

    <p><strong>Expediente D.E.:</strong> {{ (!empty($norma->exped)) ? $norma->exped : '(no posee)' }}</p>

    <p>
        <strong>Sanci&oacute;n:</strong> 
        @if (!is_null($norma->fec_sancion))
            {{ \Carbon\Carbon::parse($norma->fec_sancion)->format('d/m/Y') }}
        @else
            (sin fecha)
        @endif
    </p>

    <p>
        <strong>Promulgaci&oacute;n:</strong> 
        @if (!is_null($norma->fec_promulga))
            {{ \Carbon\Carbon::parse($norma->fec_promulga)->format('d/m/Y') }}
        @else
            (sin fecha)
        @endif
    </p>

    <p>
        <strong>Publicaci&oacute;n:</strong> 
        @if (!is_null($norma->fec_publica))
            {{ \Carbon\Carbon::parse($norma->fec_publica)->format('d/m/Y') }}
        @else
            (sin fecha)
        @endif
    </p>

    @if (!empty($norma->boletin_nro) || !empty($norma->boletin_pag))
        <p>
            <strong>Bolet&iacute;n N°:</strong> 
            {{ (!empty($norma->boletin_nro) && $norma->boletin_nro != 'null') ? $norma->boletin_nro : '(sin número)' }} 
            <strong> - Pág.:</strong> 
            {{ (!empty($norma->boletin_pag) && $norma->boletin_pag != 'null') ? $norma->boletin_pag : '(sin página)' }}
        </p>
    @endif

    @if (!empty($norma->registro_t) || !empty($norma->registro_f))
        <p>
            <strong> - Registrado</strong> 
            @if (!empty($norma->registro_t))
                <strong>Tomo</strong> {{ $norma->registro_t }}
            @endif
            @if (!empty($norma->registro_f))
                <strong>Folio</strong> {{ $norma->registro_f }}
            @endif
        </p>
    @endif

    @if (!empty($norma->abrogacion_a) || !empty($norma->abrogacion_n))
        <p>
            <strong>Abrogaci&oacute;n:</strong> {{ $norma->abrogacion_a_desc }} {{ $norma->abrogacion_n }}
<!--
            <ul>
                <li>Texto: {{ $norma->url_abrogacion_html }}</li>
                <li>PDF: {{ $norma->url_abrogacion_pdf }}</li>
            </ul>
-->
        </p>
    @endif

    @if (! empty($norma->dec_promulga))
        <p><strong>Decreto Promulgaci&oacute;n N°:</strong> {{ $norma->dec_promulga }} </p>
    @endif

    <p><strong>Contenido:</strong> {{ $norma->contenido }}</p>

    <p>
        <strong>Descriptores:</strong>
        @forelse ($norma->descriptores as $d)
            {{ $d->tag }} ;
        @empty
            - no hay descriptores definidos para esta norma -
        @endforelse
    </p>

    @if ($norma->relaciones->count() > 0)
        <ul>
            @foreach ($norma->relaciones as $r)
                <li>
                    <strong>{{ $r->relacion_desc }}:</strong> 
                    @php
                        $nr = $r->relNormaByActoNro();
                    @endphp
                    @if ($nr)
                        {{ $nr->acto_desc }} {{ $nr->nro }} {{ $r->p }}
{{--
                        @if ($nr->url_html != '')
                            <br/>
                            Texto: {{ $nr->url_html }}
                        @endif
                        @if ($nr->url_pdf != '')
                            <br/>
                            PDF: {{ $nr->url_pdf }}
                        @endif
--}}
                    @else
                        {{ $r->acto_desc }} {{ $r->n }} {{ $r->p }}
{{--
                        @if ($r->url_html != '')
                            <br/>
                            Texto: {{ $r->url_html }}
                        @endif
                        @if ($r->url_pdf != '')
                            <br/>
                            PDF: {{ $r->url_pdf }}
                        @endif
--}}
                    @endif
                </li>
            @endforeach
        </ul>
    @endif

    <p>
        <strong>Observaciones:</strong>
        <ul>
        @forelse ($norma->observaciones as $o)
            <li>{{ $o->obs }}</li>
        @empty
            <li>No hay observaciones para esta norma.</li>
        @endforelse
        </ul>
    </p>

    <p><strong>Alcance Normativo:</strong> {{ $norma->alcance_desc }} </p>

    <p><strong>Car&aacute;cter:</strong> {{ $norma->caracter_desc }} </p>

    <p><strong>Recopilación:</strong> {{ $norma->recopila_desc }} </p>

@endsection
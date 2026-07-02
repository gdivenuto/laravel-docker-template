@extends('pdf.base')

@section('content')

    <p><strong>Expediente D.E.:</strong> {{ (!empty($norma->exped)) ? $norma->exped : '(no posee)' }}</p>
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
    <p><strong>Nro. Interno:</strong> {{ (!empty($norma->nro_hcd)) ? $norma->nro_hcd : '(no posee)' }}</p>
    <p>
        <strong>Fecha de sanci&oacute;n:</strong> 
        @if (!is_null($norma->fec_sancion))
            {{ \Carbon\Carbon::parse($norma->fec_sancion)->format('d/m/Y') }}
        @else
            (sin fecha)
        @endif
    </p>
    <p>
        <strong>Fecha de promulgaci&oacute;n:</strong> 
        @if (!is_null($norma->fec_promulga))
            {{ \Carbon\Carbon::parse($norma->fec_promulga)->format('d/m/Y') }}
        @else
            (sin fecha)
        @endif
    </p>

    @if (! empty($norma->dec_promulga))
        <p><strong>Decreto de promulgaci&oacute;n N°:</strong> {{ $norma->dec_promulga }} </p>
    @endif

    <br>
    <p>
        <strong>{{ $norma->acto_desc }} {{ $norma->nro }}</strong>
    </p>

@endsection
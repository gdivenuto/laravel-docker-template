@extends('pdf.base')

@section('content')

    <p><strong>Período ({{ config('params.normaVencDayCount') }} días):</strong> Desde el <strong>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</strong> hasta el <strong>{{ \Carbon\Carbon::now()->startOfDay()->addDay(config('params.normaVencDayCount'))->format('d/m/Y') }}</strong> inclusive.</p>
    <p><strong>Cantidad de resultados:</strong> {{ $data->count() }} documento(s)</p>

	@foreach($data as $norma)
		<p style="border-top: 1px solid">
			<strong>{{ $norma->acto_desc }}</strong>&nbsp;{{ $norma->nro }}

            <strong>Vencimiento:</strong> 
            @if (!is_null($norma->fec_excluido))
                {{ \Carbon\Carbon::parse($norma->fec_excluido)->format('d/m/Y') }}
            @else
                (sin fecha)
            @endif
        </p>
        <p>            
            <strong>Sanci&oacute;n:</strong> 
            @if (!is_null($norma->fec_sancion))
                {{ \Carbon\Carbon::parse($norma->fec_sancion)->format('d/m/Y') }}
            @else
                (sin fecha)
            @endif

             <strong>Promulgaci&oacute;n:</strong> 
            @if (!is_null($norma->fec_promulga))
                {{ \Carbon\Carbon::parse($norma->fec_promulga)->format('d/m/Y') }}
            @else
                (sin fecha)
            @endif
		</p>
		<p>
			<strong>Contenido:</strong> {{ $norma->contenido }}
		</p>
		<p>
			<strong>Descriptores: </strong><span>{{ $norma->descriptores->pluck('tag')->join(', ') }}</span>
    	</p>
    @endforeach    

@endsection

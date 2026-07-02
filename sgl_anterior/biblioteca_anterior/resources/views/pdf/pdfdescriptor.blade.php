@extends('pdf.base')

@section('content')

    <p><strong>Tema:</strong> {{ ucfirst($normas_db) }}</p>
    <p>
        @if ($logic == 'or')
            <strong>Que posea alguno de los descriptores:</strong> 
        @else
            <strong>Que posea todos los descriptores:</strong> 
        @endif
        {{ join(', ', $descriptores) }}
    </p>
    <p><strong>Cantidad de resultados:</strong> {{ $data->count() }} documento(s)</p>

	@foreach($data as $norma)
		<p style="border-top: 1px solid">
			<strong>{{ $norma->acto_desc }}</strong>&nbsp;{{ $norma->nro }}
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

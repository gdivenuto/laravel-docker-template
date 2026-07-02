@extends('pdf.base')

@section('content')

    <p><strong>Tema:</strong> {{  ucfirst($normas_db) }}</p>
    <p>
        <strong>Criterio de búsqueda:</strong>
        <ul>
            @foreach ($criteria as $c)
                @switch ($c['type'])
                    @case('has_or') 
                        <li><strong>Alguno de los descriptores:</strong> {{ join(' ó ', $c['value'])}}</li>
                        @break
                    @case('has_and') 
                        <li><strong>Todos los descriptores:</strong> {{ join(' y ', $c['value'])}}</li>
                        @break
                    @case('has_not') 
                        <li><strong>Ninguno de los descriptores:</strong> {{ join(' ó ', $c['value'])}}</li>
                        @break
                    @case('begins') 
                        <li><strong>Alguno de los descriptores que comienzan con:</strong> {{ join(' ó ', $c['value'])}}</li>
                        @break
                    @case('ends') 
                        <li><strong>Alguno de los descriptores que terminan con:</strong> {{ join(' ó ', $c['value'])}}</li>
                        @break
                    @case('contains') 
                        <li><strong>Alguno de los descriptores que contienen:</strong> {{ join(' ó ', $c['value'])}}</li>
                        @break
                    @case('tipo_acto')
                        <li><strong>Tipo de acto:</strong> {{ collect($c['value'])->map(function($i, $k) {return FakeAttrib::tipoActoList()[$i]; })->join(' ó ') }}</li>
                        @break
                    @case('date_sancion') 
                        <li><strong>Fecha de sanción:</strong> desde {{ $c['value'][0] ?? '?' }} hasta {{ $c['value'][1] ?? '?' }}</li>
                        @break
                    @case('date_promulga') 
                        <li><strong>Fecha de promulgación:</strong> desde {{ $c['value'][0] ?? '?' }} hasta {{ $c['value'][1] ?? '?' }}</li>
                        @break
                    @case('date_publica') 
                        <li><strong>Fecha de publicación:</strong> desde {{ $c['value'][0] ?? '?' }} hasta {{ $c['value'][1] ?? '?' }}</li>
                        @break                    
                    @case('date_intendencia') 
                        <?php
                            $i = \App\Intendencia::find($c['value'][0]);
                            $t = sprintf("%s, %d° mandato, desde %s hasta %s", $i->intendente, $i->nro, $i->fec_desde, $i->fec_hasta ?? 'hoy');
                        ?>
                        <li><strong>Sancionado durante la Intendencia de:</strong> {{ $t }}</li>
                        @break
                    @case('full_content') 
                        <li><strong>Contenido:</strong> {{ join(' y ', $c['value'])}}</li>
                        @break
                @endswitch
            @endforeach
        </ul>
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

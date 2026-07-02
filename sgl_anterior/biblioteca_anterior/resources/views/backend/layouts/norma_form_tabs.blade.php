<?php
	$tabs = [
		['name' => 'Norma',           'route' => 'backend.normas.edit'],
		['name' => 'Descriptores',    'route' => 'backend.descriptornormas.edit'],
		['name' => 'Relaciones',      'route' => 'backend.relaciones.edit'],
		//['name' => 'Expedientes HCD', 'route' => 'backend.hcdexpedientes.edit'],
		['name' => 'Actas',           'route' => 'backend.actas.edit'],
		['name' => 'Observaciones',   'route' => 'backend.observaciones.edit'],
		//['name' => 'Procesamientos',  'route' => 'backend.procesamientos.edit'],
		['name' => 'Abstenciones',    'route' => 'backend.abstenciones.edit']
	];
?>
<ul class="nav nav-tabs nav-fill mb-3">
	@foreach ($tabs as $t)
	    <li class="nav-item">
	    	<a class="nav-link{{ (request()->route()->getName() == $t['route']) ? ' active' : '' }}{{ ($norma->id) ? '' : ' disabled' }}" href="{{ ($norma->id) ? route($t['route'], ['norma' => $norma]) : '#' }}">
	    		{{ $t['name'] }}
	    	</a>
	    </li>
	@endforeach
</ul>
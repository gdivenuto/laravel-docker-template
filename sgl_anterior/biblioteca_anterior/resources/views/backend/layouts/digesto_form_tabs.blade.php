<?php
	$tabs = [
		['name' => 'Digesto',      'route' => 'backend.digestos.edit'],
		['name' => 'Descriptores', 'route' => 'backend.descriptordigestos.edit']
	];
?>
<ul class="nav nav-tabs nav-fill mb-3">
	@foreach ($tabs as $t)
	    <li class="nav-item">
	    	<a class="nav-link{{ (request()->route()->getName() == $t['route']) ? ' active' : '' }}{{ ($digesto->id) ? '' : ' disabled' }}" href="{{ ($digesto->id) ? route($t['route'], ['digesto' => $digesto]) : '#' }}">
	    		{{ $t['name'] }}
	    	</a>
	    </li>
	@endforeach
</ul>
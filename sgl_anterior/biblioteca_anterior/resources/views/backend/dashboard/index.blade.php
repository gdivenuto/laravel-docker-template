@extends('backend.layouts.app')

@section('script')
@endsection

@section('content')
<div class="container-fluid">
	
	<div class="row">
		<div class="col-12">	
			<p class="h4">
				<i class="fa fa-home" aria-hidden="true"></i> &gt;
				<a href="{{ route('backend.dashboard.index') }}">Dashboard</a>
			</p>
		</div>
	</div>

	<div class="row mt-1">
		<div class="col-12">			
			Bienvenido, <abbr title="{{ Auth::user()->email }}">{{ Auth::user()->name }}</abbr>.<br/>
			Te encuentras en el <i>dashboard</i>; desde aqu&iacute; tendr&aacute;s un pantallazo general del estado de la base documental de la Biblioteca y podr&aacute;s acceder a las tareas administrativas m&aacute;s habituales.
		</div>
	</div>

	<div class="row mt-2">
		
		<div class="col-sm-6 mt-2">
			<div class="card h-100">
				<div class="card-body">
					<a href="{{ route('backend.normas.create') }}" class="h5 card-title">
						<i class="fa fa-arrow-right" aria-hidden="true"></i>
						Agregar
					</a>
					<p class="card-text">Agrega una <strong>nueva norma</strong> a la base de datos documental.</p>
				</div>
			</div>
		</div>

		<div class="col-sm-6 mt-2">
			<div class="card h-100">
				<div class="card-body">
					<a href="{{ route('backend.normas.index') }}" class="h5 card-title">
						<i class="fa fa-arrow-right" aria-hidden="true"></i>
						Gestionar
					</a>
					<p class="card-text">Accede a toda la base de datos documental para <strong>agregar, quitar o editar las normas</strong>.</p>
				</div>
			</div>
		</div>

		<div class="col-sm-6 mt-2">
			<div class="card h-100">
				<div class="card-body">
					<a href="{{ route('normas.search', ['normas_db' => 'todas']) }}" class="h5 card-title">
						<i class="fa fa-arrow-right" aria-hidden="true"></i>
						Buscar
					</a>
					<p class="card-text"><strong>Busca normas</strong> de la base de datos documental en base a criterios específicos.</p>
				</div>
			</div>
		</div>		

		<div class="col-sm-6 mt-2">
			<div class="card h-100">
				<div class="card-body">
					<a href="{{ route('backend.digestos.index') }}" class="h5 card-title">
						<i class="fa fa-arrow-right" aria-hidden="true"></i>
						Digestos
					</a>
					<p class="card-text">Accede a la configuración de los digestos especiales para <strong>agregar, quitar o editar digestos</strong>.</p>
				</div>
			</div>
		</div>

		<div class="col-sm-6 mt-2">
			<div class="card h-100">
				<div class="card-body">
					<a href="{{ route('backend.dashboard.publishconfirm') }}" class="h5 card-title">
						<i class="fa fa-arrow-right" aria-hidden="true"></i>
						Publicar Contenidos
					</a>
					<p class="card-text"><strong>Hacer visible en la web</strong> todo el contenido documental de car&aacute;cter p&uacute;blico.</p>
				</div>
			</div>
		</div>

		<div class="col-sm-6 mt-2">
			<div class="card h-100">
				<div class="card-body">
					<a href="{{ route('dashboard.showdbselector') }}" class="h5 card-title">
						<i class="fa fa-arrow-right" aria-hidden="true"></i>
						Previsualizar
					</a>
					<p class="card-text">Echa un vistazo a <strong>cómo se visualizará</strong> la base de datos documental de cara al usuario final.</p>
				</div>
			</div>
		</div>

	</div>

</div>
@endsection
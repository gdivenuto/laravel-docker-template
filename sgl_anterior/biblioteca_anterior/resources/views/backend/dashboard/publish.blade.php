@extends('backend.layouts.app')

@section('script')
<script src="{{ asset('js/i18n/'.\App::getLocale().'.js') }}"></script>
<script>
    // ---- Callbacks ---------------------------------------------------------

    // ------------------------------------------------------------------------
    // jQuery Document Ready Function -----------------------------------------
    // ------------------------------------------------------------------------
    $(function () {
        // Set moment locale
        moment.locale('{!! \App::getLocale() !!}');

	});
</script>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">    
            <p class="h4">
                <i class="fa fa-home" aria-hidden="true"></i> &gt;
                <a href="{{ route('backend.dashboard.index') }}">Dashboard</a> &gt;
                Publicar Contenidos
            </p>
        </div>
    </div>
    
	@if ($errors->any())
	    <div class="alert alert-danger alert-dismissible fade show" role="alert">
	        <h4 class="alert-heading">¡Atenci&oacute;n!</h4>
	        <p>No se han publicado los contenidos. Por favor verifique:</p>
	        <ul>
	            @foreach ($errors->all() as $error)
	                <li>{{ $error }}</li>
	            @endforeach
	        </ul>
	        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
	    </div>
	@else
	    @if (session('publish_status'))
	        <div class="alert alert-success alert-dismissible fade show" role="alert">
	            <p>{{ session('publish_status') }}</p>
	            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	                <span aria-hidden="true">&times;</span>
	            </button>
	        </div>
	    @endif
	@endif

	<div class="row mt-2">

		<div class="col-sm-12 mt-2">
			<div class="card h-100">
				<div class="card-body">
					<h3 class="card-title text-danger">
						<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
						ATENCI&Oacute;N
						<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
					</h3>
					<p class="card-text">Est&aacute; a punto de <strong>publicar todo el contenido documental</strong> en el sitio web y &eacute;ste será visible por los ciudadanos.</p>
					<p class="card-text">Todas las referencias a normas, decretos y sesiones, as&iacute; como tambi&eacute;n el digesto, estar&aacute;n disponibles para su consulta desde internet. Aquellos digestos específicos que hayan sido marcados como <i>p&uacute;blicos</i> tambi&eacute;n ser&aacute;n visibles.</p>
					<p class="card-text">Se le recomienda verificar que toda la informaci&oacute;n aqu&iacute; publicada est&eacute; libre de errores.</p>
					@if (Session::has('publish_code'))
						<p class="card-text">Para continuar, <strong>ingrese el código de verificación que aparece a continuación</strong> y presione el bot&oacute;n <i>Publicar Contenidos</i>.</p>
						<h1 class="card-text text-monospace">{{ join(' ', str_split(Session::get('publish_code'))) }}</h1>
					@else
						<h3 class="card-text">ERROR! No se encuentra el código de verificación.</h3>
					@endif
				</div>
			</div>
		</div>
	</div>

	<form id="dataForm" action="{{ route('backend.dashboard.publish') }}" method="POST">
		@csrf

		<div class="form-group row mt-3">
			<label for="user_publish_code" class="col-sm-2 col-form-label font-weight-bold">C&oacute;digo de Verificaci&oacute;n:</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="user_publish_code" name="user_publish_code" value="" placeholder="Ingrese el código de verificación respetando mayúsculas y minúsculas.">
			</div>
		</div>
		<div class="row my-3">
			<div class="col-sm-10 offset-sm-2">
				<button type="submit" class="btn btn-primary"><i class="fa fa-cloud-upload" aria-hidden="true"></i> Publicar Contenidos</button>
			</div>
		</div>
	</form>
</div>
@endsection
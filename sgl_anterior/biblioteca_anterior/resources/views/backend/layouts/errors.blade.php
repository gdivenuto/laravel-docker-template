@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h4 class="alert-heading">¡Atenci&oacute;n!</h4>
        <p>No se han podido salvar los cambios. Por favor verifique:</p>
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
    @if (session('save_status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <p>{{ session('save_status') }}</p>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
@endif
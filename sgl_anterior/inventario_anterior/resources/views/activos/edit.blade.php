@extends('layouts.app')

@section('script')
<script>
	// Especificaciones de tipo de activo
	var activo_tipos = @json($activo_tipos);

	// Select2 default behavior
	$.fn.select2.defaults.set('theme', 'bootstrap');
	$.fn.select2.defaults.set('language', 'es');

	// inicialización
	var flagUpdateEmpty_ubicacion = ($('#ubicacion').val() == '') || ($('#ubicacion').val() == $('#area').val());
	
	// ------------------------------------------------------------------------
	// jQuery Document Ready Function -----------------------------------------
	// ------------------------------------------------------------------------
	$(function () {
		// Set moment locale
		moment.locale('{!! \App::getLocale() !!}');

		// Formato general de Fecha y Hora
		var dateFormat = 'YYYY-MM-DD';
		var MinDate = "1980-01-01";
		var dateMin = moment(MinDate, dateFormat);
		$('#fecha_alta').datetimepicker({
			sideBySide: true,
			format: dateFormat,
			date: '{{ old('fecha_alta', $activo->fecha_alta) }}',
			minDate: dateMin
		});

		// Autoopen on focus
		/*
		$(document).on('focus', '.select2.select2-container', function (e) {
			var isOriginalEvent = e.originalEvent // don't re-open on closing focus event
			var isSingleSelect = $(this).find(".select2-selection--single").length > 0 // multi-select will pass focus to input
			if (isOriginalEvent && isSingleSelect)
				$(this).siblings('select:enabled').select2('open');
		});
		*/

		// Input setup
		$('#legajo').select2({
			placeholder: "Seleccione un responsable",
			// minimumInputLength: 3,
			closeOnSelect: true,
			ajax: {
				url: '{{ route('responsables.getautocompleteresponsablejson') }}',
				dataType: 'json',
				delay: 333,
				cache: true,
				processResults: function (data) {
					return {
						results: $.map(data, function (item) {
							return {
								id: item.legajo,
								text: sprintf('%d | %s, %s | %s', item.legajo, item.apellido, item.nombre, item.area_nombre),
								area: item.area_nombre
							}
						})
					};
				}
			}
		});

		$('#legajo').on('select2:select', function(e) {
			$('#area').val(e.params.data.area);
			if (flagUpdateEmpty_ubicacion)
				$('#ubicacion').val(e.params.data.area);
		});

		$('#marca').select2({
			placeholder: "Ingrese o seleccione una marca",
			tags: true, // permito ingreso manual
			// minimumInputLength: 3,
			closeOnSelect: true,
			ajax: {
				url: '{{ route('activos.getautocompletemarcajson') }}',
				dataType: 'json',
				delay: 333,
				cache: true,
				processResults: function (data) {
					return { results: $.map(data, function (item) { return { id: item.marca, text: item.marca } }) };
				},
			}
		});

		$('#modelo').select2({
			placeholder: "Ingrese o seleccione un modelo",
			tags: true, // permito ingreso manual
			// minimumInputLength: 3,
			closeOnSelect: true,
			ajax: {
				url: '{{ route('activos.getautocompletemodelojson') }}',
				dataType: 'json',
				delay: 333,
				cache: true,
				processResults: function (data) {
					return { results: $.map(data, function (item) { return { id: item.modelo, text: item.modelo } }) };
				},
			}
		});

		$('#sistema_operativo').select2({
			placeholder: "Ingrese o seleccione un sistema operativo",
			tags: true, // permito ingreso manual
			// minimumInputLength: 3,
			closeOnSelect: true,
			ajax: {
				url: '{{ route('activos.getautocompletesistemaoperativojson') }}',
				dataType: 'json',
				delay: 333,
				cache: true,
				processResults: function (data) {
					return { results: $.map(data, function (item) { return { id: item.sistema_operativo, text: item.sistema_operativo } }) };
				},
			}
		});

		$('#cpu').select2({
			placeholder: "Ingrese o seleccione un microprocesador",
			tags: true, // permito ingreso manual
			// minimumInputLength: 3,
			closeOnSelect: true,
			ajax: {
				url: '{{ route('activos.getautocompletecpujson') }}',
				dataType: 'json',
				delay: 333,
				cache: true,
				processResults: function (data) {
					return { results: $.map(data, function (item) { return { id: item.cpu, text: item.cpu } }) };
				},
			}
		});

		$('#motherboard').select2({
			placeholder: "Ingrese o seleccione un motherboard",
			tags: true, // permito ingreso manual
			// minimumInputLength: 3,
			closeOnSelect: true,
			ajax: {
				url: '{{ route('activos.getautocompletemotherboardjson') }}',
				dataType: 'json',
				delay: 333,
				cache: true,
				processResults: function (data) {
					return { results: $.map(data, function (item) { return { id: item.motherboard, text: item.motherboard } }) };
				},
			}
		});

		$('#hd_marca').select2({
			placeholder: "Ingrese o seleccione una marca de disco rígido",
			tags: true, // permito ingreso manual
			// minimumInputLength: 3,
			closeOnSelect: true,
			ajax: {
				url: '{{ route('activos.getautocompletehdmarcajson') }}',
				dataType: 'json',
				delay: 333,
				cache: true,
				processResults: function (data) {
					return { results: $.map(data, function (item) { return { id: item.hd_marca, text: item.hd_marca } }) };
				},
			}
		});

		$('#dvd_rw_marca').select2({
			placeholder: "Ingrese o seleccione una marca de CD/DVD",
			tags: true, // permito ingreso manual
			// minimumInputLength: 3,
			closeOnSelect: true,
			ajax: {
				url: '{{ route('activos.getautocompletedvdrwmarcajson') }}',
				dataType: 'json',
				delay: 333,
				cache: true,
				processResults: function (data) {
					return { results: $.map(data, function (item) { return { id: item.dvd_rw_marca, text: item.dvd_rw_marca } }) };
				},
			}
		});

		// Actualizar campos visibles del formulario segun tipo de activo
		$('#tipo_id').change(function() {
			var t_id = $(this).val();
			var t_result = $.grep(activo_tipos, function( item ) { return item.id == t_id; });
			var t = (t_result.length > 0) ? t_result[0] : null;
			if (t) { 
				$.each(t, function(index, value) { // 'index' es el atributo, 'value' es el valor
					var f;
					// si el atributo es "has_", busco el elemento y lo muestro/oculto segun corresponda
					if (/^has_/i.test(index)) {
						f = index.replace(/^has_/gi, ''); // quito el "has_"
						if (value)
							$('div.form-group label[for='+f+']').parent().show();
						else
							$('div.form-group label[for='+f+']').parent().hide();
					}
				});
			}
		});

		// Fuerzo actualización del campo Tipo
		$('#tipo_id').trigger('change');

		// MAC Value
		$('#ethernet_mac').on('keyup', formatMAC);
		$('#wireless_mac').on('keyup', formatMAC);

		// mostrar ayuda
		$('#showHelp').click(function(){
			$('form small.form-text').toggle();
		});

		$('#showHelp').trigger('click');

		// Al elegir si la configuración Ethernet es dinámica o no
		$('#ethernet_dinamico').change(function(){
			// Si es dinámico (= 1)
			if ( $('#ethernet_dinamico').val() == '1' ) {
				// Se deshabilita la edición de los siguientes campos
				$('#ethernet_ip').prop('disabled', true);
				$('#ethernet_mask').prop('disabled', true);
				$('#ethernet_gw').prop('disabled', true);
				$('#ethernet_dns').prop('disabled', true);

				// Se limpian los valores de los siguientes campos
				$('#ethernet_ip').val('');
				$('#ethernet_mask').val('');
				$('#ethernet_gw').val('');
				$('#ethernet_dns').val('');
			} else {
				// Se habilita la edición de los siguientes campos
				$('#ethernet_ip').prop('disabled', false);
				$('#ethernet_mask').prop('disabled', false);
				$('#ethernet_gw').prop('disabled', false);
				$('#ethernet_dns').prop('disabled', false);
			}
		});
		// Se ejecuta el evento change de '#ethernet_dinamico'
		$('#ethernet_dinamico').trigger('change');
		
		// Al elegir si la configuración Ethernet es dinámica o no
		$('#wireless_dinamico').change(function(){
			// Si es dinámico (= 1)
			if ( $('#wireless_dinamico').val() == '1' ) {
				// Se deshabilita la edición de los siguientes campos
				$('#wireless_ip').prop('disabled', true);
				$('#wireless_mask').prop('disabled', true);
				$('#wireless_gw').prop('disabled', true);
				$('#wireless_dns').prop('disabled', true);

				// Se limpian los valores de los siguientes campos
				$('#wireless_ip').val('');
				$('#wireless_mask').val('');
				$('#wireless_gw').val('');
				$('#wireless_dns').val('');
			} else {
				// Se habilita la edición de los siguientes campos
				$('#wireless_ip').prop('disabled', false);
				$('#wireless_mask').prop('disabled', false);
				$('#wireless_gw').prop('disabled', false);
				$('#wireless_dns').prop('disabled', false);
			}
		});
		// Se ejecuta el evento change de '#wireless_dinamico'
		$('#wireless_dinamico').trigger('change');

		// Codigo de verificación en los botones de guardar
		$('.btn-submit-form').click(function(e) {
			e.preventDefault();

			$.ajax({
				method: 'POST',
				url: '{{ route('activos.jsonverifybynroinventario') }}',
				headers: { 
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
				},
				contentType: 'application/json',
				data: JSON.stringify({ 
					id: {{ $activo->id ?? '-1' }}, 
					nro_inventario: $('#nro_inventario').val().trim() 
				})
			})
			.done(function( data, textStatus, jqXHR ) {
				if (data.status == 'OK') 
					$('#dataForm').submit();
				else {
					// Se agrega el contenido en el cuerpo de la modal
		            $('#modalconfirm_body_txt').html(sprintf('<h3>¡Atenci&oacute;n!</h3>%s <strong>¿Quiere guardarlo de todas maneras?</strong>', data.message));
		            
		            // Se descartan todos los manejadores de eventos asociados al evento clic del botón "Si" de la modal
		            $('#modalconfirm_btn_yes').unbind('click');

		            // Al querer imprimir el Código QR 
					$('#modalconfirm_btn_yes').click(function(e) {
						e.preventDefault();
						$('#dataForm').submit();
					});

		            // Se muestra la modal
		            $('#modalconfirm').modal('show');
				}
			})
			.fail(function( jqXHR, textStatus, errorThrown ) {
				alert(sprintf('Ha ocurrido un error al verificar el Nª de Inventario del Activo: %s', errorThrown));
			});
		});
	});
</script>
@endsection

@section('content')

	@include('layouts.modalconfirm')

<div class="container-fluid">
@if ($activo->id)
	<form id="dataForm" action="{{ route('activos.update', [ 'id' => $activo->id ]) }}" method="POST" enctype="multipart/form-data">
	@method('put')
@else
	<form id="dataForm" action="{{ route('activos.store') }}" method="POST" enctype="multipart/form-data">
@endif
		<div class="card">
	  		<div class="card-header alinear-form-control">
				@if ($activo->id)
					<h5>Activos > Editar <i>N&deg; {{ $activo->id }} - {{ $activo->habilitado }}</i></h5>
				@else
					<h5>Activos > Nuevo</h5>
				@endif

				<button type="submit" class="btn ml-5 btn-primary btn-sm btn-submit-form"><i class="fa fa-check" aria-hidden="true"></i> Guardar</button>
				<a class="btn mx-2 btn-secondary btn-sm" role="button" href="{{ route('activos.index') }}"><i class="fa fa-reply" aria-hidden="true"></i> Volver</a>
			
				<button type="button" id="showHelp" class="btn btn-secondary btn-sm">
					<i class="fa fa-question-circle" aria-hidden="true"></i>
				</button>
			</div>
			<div class="card-body">
				@include('layouts.errors')

				@csrf

				<div class="form-row">
					<div id="tipo_id_control" class="form-group col-md-3 alinear-form-control align-items-start">
						<label for="tipo_id" class="col-md-4 text-right font-weight-bold"><span class="text-info">(*)</span> Tipo</label>
						<div class="col-md-8 ml-3">
							<?php $o_tipo_id = old('tipo_id', $activo->tipo_id); ?>
							<select class="form-control custom-select-sm" id="tipo_id" name="tipo_id">
								@foreach ($activo_tipos as $at)
									<option value="{{ $at->id }}" {{ ($o_tipo_id == $at->id) ? 'selected':'' }}>{{ $at->nombre }}</option>
								@endforeach
							</select>
							<small id="tipo_idHelpBlock" class="form-text text-muted">
								Tipo de activo: determina la visibilidad del resto de los campos opcionales del activo.
							</small>
						</div>
					</div>
					<div id="nro_inventario_control" class="form-group col-md-3 alinear-form-control align-items-start">
						<label for="nro_inventario" class="col-md-4 text-right font-weight-bold">Nº Patrimonio</label>
						<div class="col-md-8">
							<input type="text" class="form-control form-control-sm" id="nro_inventario" name="nro_inventario" value="{{ old('nro_inventario', $activo->nro_inventario) }}">
							<small id="nro_inventarioHelpBlock" class="form-text text-muted">
								N&uacute;mero de inventario patrimonial.
							</small>
						</div>
					</div>
					<div id="tipo_origen_control" class="form-group col-md-3 alinear-form-control align-items-start">
						<label for="tipo_origen" class="col-md-4 text-right font-weight-bold"><span class="text-info">(*)</span> Tipo Origen</label>
						<div class="col-md-8 ml-3">
							<?php 
								$o_tipo_origen = old('tipo_origen', $activo->tipo_origen); 
								$activo_tipo_origenes = config('defaults.descripTipoOrigen');
							?>
							<select class="form-control custom-select-sm" id="tipo_origen" name="tipo_origen">
								@foreach ($activo_tipo_origenes as $k => $v)
									<option value="{{ $k }}" {{ ($o_tipo_origen == $k) ? 'selected':'' }}>{{ $v }}</option>
								@endforeach
							</select>
							<small id="tipo_origenHelpBlock" class="form-text text-muted">
								Origen del activo con respecto a su adquisici&oacute;n.
							</small>
						</div>
					</div>
					<div id="orden_compra_control" class="form-group col-md-3 alinear-form-control align-items-start">
						<label for="orden_compra" class="col-md-5 text-right font-weight-bold"><span class="text-info">(*)</span> Nº Origen</label>
						<div class="col-md-7">
							<input type="text" class="form-control form-control-sm" id="orden_compra" name="orden_compra" value="{{ old('orden_compra', $activo->orden_compra) }}">
							<small id="orden_compraHelpBlock" class="form-text text-muted">
								N&uacute;mero de Orden de Compra, Ordenanza, Decreto o Fomulario de Transferencia.
							</small>
						</div>
					</div>
				</div>

				{{-- Campos nuevos para reporte --}}
				<div class="form-row">
					<div id="titularidad_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="titularidad" class="col-md-4 text-right font-weight-bold"><span class="text-info">(*)</span> Titularidad</label>
						<div class="col-md-8 ml-3">
							<?php 
								$o_titularidad = old('titularidad', $activo->titularidad); 
								$activo_titularidades = config('defaults.descripTitularidad');
							?>
							<select class="form-control custom-select-sm" id="titularidad" name="titularidad">
								@foreach ($activo_titularidades as $k => $v)
									<option value="{{ $k }}" {{ ($o_titularidad == $k) ? 'selected':'' }}>{{ $k }} - {{ $v }}</option>
								@endforeach
							</select>
							<small id="titularidadHelpBlock" class="form-text text-muted">
								Determina qu&eacute; tipo de titularidad se maneja sobre el activo.
							</small>
						</div>
					</div>
					<div id="estado_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="estado" class="col-md-4 text-right font-weight-bold"><span class="text-info">(*)</span> Estado</label>
						<div class="col-md-8 ml-3">
							<?php 
								$o_estado = old('estado', $activo->estado); 
								$activo_estados = config('defaults.descripEstado');
							?>
							<select class="form-control custom-select-sm" id="estado" name="estado">
								@foreach ($activo_estados as $k => $v)
									<option value="{{ $k }}" {{ ($o_estado == $k) ? 'selected':'' }}>{{ $k }} - {{ $v }}</option>
								@endforeach
							</select>
							<small id="estadoHelpBlock" class="form-text text-muted">
								Determina el estado del activo con respecto a su condici&oacute;n y/o capacidades de utilizaci&oacute;n.
							</small>
						</div>
					</div>
					<div id="condicion_uso_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="condicion_uso" class="col-md-4 text-right font-weight-bold"><span class="text-info">(*)</span> Condiciones de Uso</label>
						<div class="col-md-8 ml-3">
							<?php 
								$o_condicion_uso = old('condicion_uso', $activo->condicion_uso); 
								$activo_condicion_usos = config('defaults.descripCondicionUso');
							?>
							<select class="form-control custom-select-sm" id="condicion_uso" name="condicion_uso">
								@foreach ($activo_condicion_usos as $k => $v)
									<option value="{{ $k }}" {{ ($o_condicion_uso == $k) ? 'selected':'' }}>{{ $k }} - {{ $v }}</option>
								@endforeach
							</select>
							<small id="condicion_usoHelpBlock" class="form-text text-muted">
								Determina el nivel de utilidad del activo.
							</small>
						</div>
					</div>					
				</div>

				{{-- Marca + Modelo + Nro de Serie --}}
				<div class="form-row">
					<div id="marca_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="marca" class="col-md-4 text-right font-weight-bold">Marca</label>
						<div class="col-md-8 ml-3 select2-personalizado-small">
							<select class="form-control select2-single" style="height:70%" name="marca" id="marca">
								<?php $o_val = old('marca', $activo->marca); ?>
								<option value="{{ $o_val != '' ? $o_val : config('defaults.marca') }}" selected>{{ $o_val != '' ? $o_val : config('defaults.marca') }}</option>
							</select>
							<small id="marcaHelpBlock" class="form-text text-muted">
								Marca del activo.
							</small>
						</div>
					</div>
					<div id="modelo_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="modelo" class="col-md-4 text-right font-weight-bold">Modelo</label>
						<div class="col-md-8">
							<select class="form-control select2-single" name="modelo" id="modelo">
								<?php $o_val = old('modelo', $activo->modelo); ?>
								<option value="{{ $o_val != '' ? $o_val : config('defaults.modelo') }}" selected>{{ $o_val != '' ? $o_val : config('defaults.modelo') }}</option>
							</select>
							<small id="modeloHelpBlock" class="form-text text-muted">
								Modelo del activo.
							</small>
						</div>
					</div>
					<div id="nro_serie_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="nro_serie" class="col-md-5 text-right font-weight-bold">Nº Serie</label>
						<div class="col-md-7">
							<input type="text" class="form-control form-control-sm" id="nro_serie" name="nro_serie" value="{{ old('nro_serie', $activo->nro_serie) }}">
							<small id="nro_serieHelpBlock" class="form-text text-muted">
								N&uacute;mero de serie del activo.
							</small>
						</div>
					</div>
				</div>
				{{-- Responsable + Area --}}
				<div class="form-row">
					<div id="legajo_control" class="form-group col-md-6 alinear-form-control align-items-start">
						<label for="legajo" class="col-md-3 text-right font-weight-bold"><span class="text-info">(*)</span> Responsable</label>
						<div class="col-md-9">
							<select class="form-control select2-single" name="legajo" id="legajo">
								@if ($activo->responsable)
									<option value="{{ $activo->legajo }}" selected="selected">{{ $activo->legajo }} | {{ $activo->responsable->nombre_completo }} | {{ $activo->responsable->area->nombre }}</option>
								@endif
							</select>
							<small id="legajoHelpBlock" class="form-text text-muted">
								Responsable del activo.
							</small>
						</div>
					</div>
					<div id="area_control" class="form-group col-md-6 alinear-form-control align-items-start">
						<label for="area" class="col-md-2 text-right font-weight-bold">&Aacute;rea</label>
						<div class="col-md-10">
							<input type="text" class="form-control form-control-sm" id="area" value="{{ ($activo->responsable) ? $activo->responsable->area->nombre : '' }}" disabled >
							<small id="ubicacionHelpBlock" class="form-text text-muted">
								&Aacute;rea del responsable; <i>lugar f&iacute;sico</i>.
							</small>
						</div>
					</div>
				</div>
				{{-- Ubicación + Fecha de alta --}}
				<div class="form-row">
					<div id="ubicacion_control" class="form-group col-md-6 alinear-form-control align-items-start">
						<label for="ubicacion" class="col-md-3 text-right font-weight-bold">Ubicaci&oacute;n</label>
						<div class="col-md-9">
							<input type="text" class="form-control form-control-sm" id="ubicacion" name="ubicacion" value="{{ old('ubicacion', $activo->ubicacion) }}">
							<small id="ubicacionHelpBlock" class="form-text text-muted">
								Ubicaci&oacute;n real del activo; <i>lugar f&iacute;sico</i>.
							</small>
						</div>
					</div>
					<div id="fecha_alta_control" class="form-group col-md-3 offset-md-3 alinear-form-control align-items-start">
						<label for="fecha_alta" class="col-md-6 text-right font-weight-bold"><span class="text-info">(*)</span> Fecha de Alta</label>
						<div class="col-md-6">
							<div class="input-group date" id="fecha_alta" data-target-input="nearest">
								<div class="input-group-append" data-target="#fecha_alta" data-toggle="datetimepicker">
									<div class="input-group-text"><i class="fa fa-calendar"></i></div>
								</div>
								<input type="text" class="form-control form-control-sm datetimepicker-input" name="fecha_alta" data-target="#fecha_alta" value="{{ old('fecha_alta', $activo->fecha_alta) }}"/>
							</div>
							<small id="fecha_altaHelpBlock" class="form-text text-muted">
								Fecha de alta del activo. <i>No es la fecha de carga del activo en el sistema, sino la fecha de alta efectiva del activo al patrimonio</i>.
							</small>
						</div>
					</div>
				</div>
				{{-- Nombre equipo + S.O. + Nro Serie --}}
				<div class="form-row">
					<div id="nombre_equipo_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="nombre_equipo" class="col-md-4 px-1 text-right font-weight-bold">Nombre de Equipo</label>
						<div class="col-md-8 ml-3">
							<input type="text" class="form-control form-control-sm" id="nombre_equipo" name="nombre_equipo" value="{{ old('nombre_equipo', $activo->nombre_equipo) }}">
							<small id="nombre_equipoHelpBlock" class="form-text text-muted">
								Nombre del equipo; suele ser el nombre definido para el equipo en la red (var&iacute;a seg&uacute;n hardware).
							</small>
						</div>
					</div>
					<div id="sistema_operativo_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="sistema_operativo" class="col-md-4 text-right font-weight-bold pr-0">Sistema Operativo</label>
						<div class="col-md-8">
							<select class="form-control select2-single" name="sistema_operativo" id="sistema_operativo">
								<?php $o_val = old('sistema_operativo', $activo->sistema_operativo); ?>
								<option value="{{ $o_val != '' ? $o_val : config('defaults.sistemaOperativo') }}" selected>{{ $o_val != '' ? $o_val : config('defaults.sistemaOperativo') }}</option>
							</select>
							<small id="sistema_operativoHelpBlock" class="form-text text-muted">
								Sistema Operativo instalado en el equipo.
							</small>
						</div>
					</div>
					<div id="sistema_operativo_serie_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="sistema_operativo_serie" class="col-md-4 text-right font-weight-bold pr-0 pl-0">Serial Sis. Op.</label>
						<div class="col-md-8">
							<input type="text" class="form-control form-control-sm" id="sistema_operativo_serie" name="sistema_operativo_serie" value="{{ old('sistema_operativo_serie', $activo->sistema_operativo_serie) }}">
							<small id="sistema_operativo_serieHelpBlock" class="form-text text-muted">
								N&uacute;mero de serie o <i>key</i> del Sistema Operativo instalado en el equipo, si aplica.
							</small>
						</div>
					</div>
				</div>
				{{-- Microprocesador + RAM + Motherboard + Fuente --}}
				<div class="form-row">
					<div id="cpu_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="cpu" class="col-md-4 px-1 text-right font-weight-bold">Microprocesador</label>
						<div class="col-md-8 ml-3">
							<select class="form-control select2-single" name="cpu" id="cpu">
								<?php $o_val = old('cpu', $activo->cpu); ?>
								<option value="{{ $o_val != '' ? $o_val : config('defaults.marca') }}" selected>{{ $o_val != '' ? $o_val : config('defaults.marca') }}</option>
							</select>
							<small id="cpuHelpBlock" class="form-text text-muted">
								Microprocesador o <i>CPU</i> del equipo.
							</small>
						</div>
					</div>
					<div id="memoria_control" class="form-group col-md-2 alinear-form-control align-items-start">
						<label for="memoria" class="col-md-4 text-right font-weight-bold pr-0">RAM</label>
						<div class="col-md-8 ml-3">
							<input type="text" class="form-control form-control-sm" id="memoria" name="memoria" value="{{ old('memoria', $activo->memoria) }}">
							<small id="memoriaHelpBlock" class="form-text text-muted">
								Cantidad de memoria RAM instalada en el equipo.
							</small>
						</div>
					</div>
					<div id="fuente_control" class="form-group col-md-2 alinear-form-control align-items-start">
						<label for="fuente" class="col-md-3 text-right font-weight-bold pl-0 pr-0">Fuente</label>
						<div class="col-md-8 ml-3">
							<input type="text" class="form-control form-control-sm" id="fuente" name="fuente" value="{{ old('fuente', $activo->fuente) }}">
							<small id="fuenteHelpBlock" class="form-text text-muted">
								Marca y modelo de la Fuente de Alimentaci&oacute;n eléctrica del equipo.
							</small>
						</div>
					</div>
					<div id="motherboard_control" class="form-group  col-md-4 alinear-form-control align-items-start">
						<label for="motherboard" class="col-md-4 text-right font-weight-bold pr-0" >Motherboard</label>
						<div class="col-md-8">
							<select class="form-control select2-single" name="motherboard" id="motherboard">
									<?php $o_val = old('motherboard', $activo->motherboard); ?>
									<option value="{{ $o_val != '' ? $o_val : config('defaults.marca') }}" selected>{{ $o_val != '' ? $o_val : config('defaults.marca') }}</option>
							</select>
							<small id="motherboardHelpBlock" class="form-text text-muted">
								<i>Motherboard</i> instalada en el equipo.
							</small>
						</div>
					</div>
				</div>
				{{-- Disco + Capacidad + DVD + Marca y Modelo --}}
				<div class="form-row">
					<div id="hd_marca_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="hd_marca" class="col-md-4 text-right font-weight-bold">HD</label>
						<div class="col-md-8 ml-3">
							<select class="form-control select2-single" name="hd_marca" id="hd_marca">
								<?php $o_val = old('hd_marca', $activo->hd_marca); ?>
								<option value="{{ $o_val != '' ? $o_val : config('defaults.marca') }}" selected>{{ $o_val != '' ? $o_val : config('defaults.marca') }}</option>
							</select>
							<small id="hd_marcaHelpBlock" class="form-text text-muted">
								Marca y modelo del Disco R&iacute;gido instalado en el equipo.
							</small>
						</div>
					</div>
					<div id="hd_capacidad_control" class="form-group col-md-2 alinear-form-control align-items-start">
						<label for="hd_capacidad" class="col-md-7 text-right font-weight-bold pr-0 pl-0">Capacidad HD</label>
						<div class="col-md-5 ml-3">
							<input type="text" class="form-control form-control-sm" id="hd_capacidad" name="hd_capacidad" value="{{ old('hd_capacidad', $activo->hd_capacidad) }}">
							<small id="hd_capacidadHelpBlock" class="form-text text-muted">
								Capacidad del Disco R&iacute;gido instalado en el equipo.
							</small>
						</div>
					</div>
					<div id="dvd_rw_control" class="form-group col-md-2 alinear-form-control align-items-start">
						<label for="dvd_rw" class="col-md-6 text-right font-weight-bold">DVD/CD</label>
						<div class="col-md-6">
							<select class="form-control" id="dvd_rw" name="dvd_rw">
								<option value="0" {{ ( old('dvd_rw', $activo->dvd_rw) == 0) ? 'selected':'' }}>No</option>
								<option value="1" {{ ( old('dvd_rw', $activo->dvd_rw) == 1) ? 'selected':'' }}>Si</option>
							</select>				
							<small id="dvd_rwHelpBlock" class="form-text text-muted">
								Indica si el equipo posee un lector/grabador de DVD/CD.
							</small>
						</div>
					</div>
					<div id="dvd_rw_marca_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="dvd_rw_marca" class="col-md-4 text-right font-weight-bold">Marca DVD/CD</label>
						<div class="col-md-8">
							<select class="form-control select2-single" name="dvd_rw_marca" id="dvd_rw_marca">
								<?php $o_val = old('dvd_rw_marca', $activo->dvd_rw_marca); ?>
								<option value="{{ $o_val != '' ? $o_val : config('defaults.marca') }}" selected>{{ $o_val != '' ? $o_val : config('defaults.marca') }}</option>
							</select>
							<small id="dvd_rw_marcaHelpBlock" class="form-text text-muted">
								Marca y modelo del lector/grabador de DVD/CD, si aplica.
							</small>
						</div>
					</div>
				</div>
				{{-- Ethernet dinámico --}}
				<div class="form-row">
					<div id="ethernet_dinamico_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="ethernet_dinamico" class="col-md-4 text-right font-weight-bold pr-0 pl-0">Red Ethernet Din&aacute;mico</label>
						<div class="col-md-8 ml-3">
							<select class="form-control" id="ethernet_dinamico" name="ethernet_dinamico">
								<option value="0" {{ ( old('ethernet_dinamico', $activo->ethernet_dinamico) == 0) ? 'selected':'' }}>No</option>
								<option value="1" {{ ( old('ethernet_dinamico', $activo->ethernet_dinamico) == 1) ? 'selected':'' }}>Si</option>
							</select>				
							<small id="ethernet_dinamicoHelpBlock" class="form-text text-muted">
								Indica si la configuración de red por <i>ethernet</i> es din&aacute;mica o manual.
							</small>
						</div>
					</div>
					<div id="ethernet_mac_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="ethernet_mac" class="col-md-4 text-right font-weight-bold">Ethernet MAC</label>
						<div class="col-md-8">
							<input type="text" class="form-control form-control-sm" id="ethernet_mac" name="ethernet_mac" value="{{ old('ethernet_mac', $activo->ethernet_mac) }}">
							<small id="ethernet_macHelpBlock" class="form-text text-muted">
								Direcci&oacute;n MAC de la placa de red ethernet. Tipee de forma directa los dígitos de la MAC; el campo toma formato autom&aacute;ticamente.
							</small>
						</div>
					</div>
					<div id="ethernet_ip_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="ethernet_ip" class="col-md-4 text-right font-weight-bold">Ethernet IP</label>
						<div class="col-md-8">
							<input type="text" class="form-control form-control-sm" id="ethernet_ip" name="ethernet_ip" value="{{ old('ethernet_ip', $activo->ethernet_ip) }}">
							<small id="ethernet_ipHelpBlock" class="form-text text-muted">
								Direcci&oacute;n IP de la placa de red ethernet.
							</small>
						</div>
					</div>
				</div>
				<div class="form-row">
					<div id="ethernet_mask_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="ethernet_mask" class="col-md-4 text-right font-weight-bold pr-0 pl-0">Ethernet Subnet Mask</label>
						<div class="col-md-8 ml-3">
							<input type="text" class="form-control form-control-sm" id="ethernet_mask" name="ethernet_mask" value="{{ old('ethernet_mask', $activo->ethernet_mask) }}">
							<small id="ethernet_maskHelpBlock" class="form-text text-muted">
								M&aacute;scara de subred de la placa de red ethernet.
							</small>
						</div>
					</div>
					<div id="ethernet_gw_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="ethernet_gw" class="col-md-4 text-right font-weight-bold">Ethernet Gateway</label>
						<div class="col-md-8">
							<input type="text" class="form-control form-control-sm" id="ethernet_gw" name="ethernet_gw" value="{{ old('ethernet_gw', $activo->ethernet_gw) }}">
							<small id="ethernet_gwHelpBlock" class="form-text text-muted">
								Puerta de enlace predeterminada de la placa de red ethernet.
							</small>
						</div>
					</div>
					<div id="ethernet_dns_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="ethernet_dns" class="col-md-4 text-right font-weight-bold">Ethernet DNS</label>
						<div class="col-md-8">
							<input type="text" class="form-control form-control-sm" id="ethernet_dns" name="ethernet_dns" value="{{ old('ethernet_dns', $activo->ethernet_dns) }}">
							<small id="ethernet_dnsHelpBlock" class="form-text text-muted">
								Servidores DNS configurados para la placa de red ethernet.
							</small>
						</div>
					</div>
				</div>
				{{-- Wireless dinámico --}}
				<div class="form-row">
					<div id="wireless_dinamico_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="wireless_dinamico" class="col-md-4 text-right font-weight-bold pl-0 pr-0">Red Wireless Din&aacute;mico</label>
						<div class="col-md-8 ml-3">
							<select class="form-control" id="wireless_dinamico" name="wireless_dinamico">
								<option value="0" {{ ( old('wireless_dinamico', $activo->wireless_dinamico) == 0) ? 'selected':'' }}>No</option>
								<option value="1" {{ ( old('wireless_dinamico', $activo->wireless_dinamico) == 1) ? 'selected':'' }}>Si</option>
							</select>				
							<small id="wireless_dinamicoHelpBlock" class="form-text text-muted">
								Indica si la configuración de red por <i>wireless</i> es din&aacute;mica o manual.
							</small>
						</div>
					</div>
					<div id="wireless_mac_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="wireless_mac" class="col-md-4 text-right font-weight-bold">Wireless MAC</label>
						<div class="col-md-8">
							<input type="text" class="form-control form-control-sm" id="wireless_mac" name="wireless_mac" value="{{ old('wireless_mac', $activo->wireless_mac) }}">
							<small id="wireless_macHelpBlock" class="form-text text-muted">
								Direcci&oacute;n MAC de la placa de red wireless. Tipee de forma directa los dígitos de la MAC; el campo toma formato autom&aacute;ticamente.
							</small>
						</div>
					</div>
					<div id="wireless_ip_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="wireless_ip" class="col-md-4 text-right font-weight-bold">Wireless IP</label>
						<div class="col-md-8">
							<input type="text" class="form-control form-control-sm" id="wireless_ip" name="wireless_ip" value="{{ old('wireless_ip', $activo->wireless_ip) }}">
							<small id="wireless_ipHelpBlock" class="form-text text-muted">
								Direcci&oacute;n IP de la placa de red wireless.
							</small>
						</div>
					</div>
				</div>
				<div class="form-row">
					<div id="wireless_mask_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="wireless_mask" class="col-md-4 text-right font-weight-bold pl-0 pr-0">Wireless Subnet Mask</label>
						<div class="col-md-8 ml-3">
							<input type="text" class="form-control form-control-sm" id="wireless_mask" name="wireless_mask" value="{{ old('wireless_mask', $activo->wireless_mask) }}">
							<small id="wireless_maskHelpBlock" class="form-text text-muted">
								M&aacute;scara de subred de la placa de red wireless.
							</small>
						</div>
					</div>
					<div id="wireless_gw_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="wireless_gw" class="col-md-4 text-right font-weight-bold">Wireless Gateway</label>
						<div class="col-md-8">
							<input type="text" class="form-control form-control-sm" id="wireless_gw" name="wireless_gw" value="{{ old('wireless_gw', $activo->wireless_gw) }}">
							<small id="wireless_gwHelpBlock" class="form-text text-muted">
								Puerta de enlace predeterminada de la placa de red wireless.
							</small>
						</div>
					</div>
					<div id="wireless_dns_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="wireless_dns" class="col-md-4 text-right font-weight-bold">Wireless DNS</label>
						<div class="col-md-8">
							<input type="text" class="form-control form-control-sm" id="wireless_dns" name="wireless_dns" value="{{ old('wireless_dns', $activo->wireless_dns) }}">
							<small id="wireless_dnsHelpBlock" class="form-text text-muted">
								Servidores DNS configurados para la placa de red wireless.
							</small>
						</div>
					</div>
				</div>
				{{-- Observaciones --}}
				<div class="form-row">
					<div id="observaciones_control" class="form-group col-md-12 alinear-form-control align-items-start">
						<label for="observaciones" class="col-md-1 text-right font-weight-bold px-0">Observaciones</label>
						<div class="col-md-10 ml-5">
							<textarea class="form-control" id="observaciones" name="observaciones" rows="3">{{ old('observaciones', $activo->observaciones) }}</textarea>
							<small id="observacionesHelpBlock" class="form-text text-muted">
								Observaciones con respecto al activo.
							</small>
						</div>
					</div>
				</div>
				<div class="form-row">
					{{-- Habilitado --}}
					<div id="habilitado_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="habilitado" class="col-md-4 text-right font-weight-bold">
							<span class="text-info">(*)</span> Habilitado
						</label>
						<div class="col-md-8 ml-3">
							<select class="form-control custom-select-sm" id="habilitado" name="habilitado">
								<option value="1" {{ (old('habilitado', $activo->habilitado) == '1') ? 'selected' : '' }}>
									1 -Alta
								</option>
								<option value="0" {{ (old('habilitado', $activo->habilitado) == '0') ? 'selected' : '' }}>
									0 -Baja
								</option>
							</select>
							<small id="habilitadoHelpBlock" class="form-text text-muted">
								Determina si el activo se encuentra habilitado o no.
							</small>
						</div>
					</div>
				</div>
				<div class="form-row">
					{{-- Upload del PDF --}}
					<div id="archivo_control" class="form-group col-md-4 alinear-form-control align-items-start">
						<label for="archivo" class="col-md-4 text-right font-weight-bold">
							Subir Archivo<br>(s&oacute;lo .pdf)
						</label>
						<div class="col-md-8 ml-3">
			            	<input type="file" class="form-control" id="archivo" name="archivo" accept="application/pdf">
			            </div>
			        </div>

			        @if(!empty($archivos))
				        <div class="form-group col-md-8 alinear-form-control align-items-start">
							<label for="archivo" class="col-md-3 text-right font-weight-bold">
								Archivos
							</label>
							<div class="col-md-9">
						        <ul>
						            @foreach($archivos as $archivo)
						                <li class="mb-2">
						                    <a 
												href="{{ route('activos.eliminaradjunto', ['filename' => $archivo->getFilename()]) }}"
											    class="btn btn-sm btn-danger" 
											    title="Eliminar archivo"
											>
											    <i class="fa fa-trash-o"></i>
											</a>
											&nbsp;
						                    <a  href="{{ url('activos_pdf/' . $archivo->getFilename()) }}" 
						                    	target="_blank"
						                    >
						                    	{{ substr($archivo->getFilename(), strpos($archivo->getFilename(), '__') + 2) }}
						                    </a>
						                </li>
						            @endforeach
						        </ul>
						    </div>
						</div>
				    @endif
				</div>
				
				<div class="form-group row">
					<div class="col-md-9 col-md-offset-2">
						<p>Los campos marcados con <span class="text-info">(*)</span> son obligatorios</p>
					</div>
				</div>
			</div> {{-- fin del card-body --}}
			<div class="card-footer">
				<div class="form-group row">
					<div class="col-md-10 col-md-offset-2">
						<button type="submit" class="btn btn-primary btn-sm btn-submit-form">
							<i class="fa fa-check" aria-hidden="true"></i> Guardar
						</button>
						<a class="btn btn-secondary btn-sm" role="button" href="{{ route('activos.index') }}">
							<i class="fa fa-reply" aria-hidden="true"></i> Volver
						</a>
					</div>
				</div>
			</div>
		</div> {{-- fin del card --}}
	</form>
</div>
@endsection

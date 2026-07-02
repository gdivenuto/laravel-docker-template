@extends('layouts.app')

@section('script')
<script>
	// ------------------------------------------------------------------------
	// jQuery Document Ready Function -----------------------------------------
	// ------------------------------------------------------------------------
	$(function () {	
		// Actualizar campos visibles del formulario segun tipo de activo
		$.each(@json($activo->activo_tipo), function(index, value) { // 'index' es el atributo, 'value' es el valor
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

		// Al utilizar el botón "Etiqueta"
		$('#btnVerPreviewEtiqueta').click(function() {
			// Se agrega el contenido en el cuerpo de la modal
            $('#modalconfirm_body_txt').html(verPreviewEtiqueta());
            
            // Se descartan todos los manejadores de eventos asociados al evento clic del botón "Si" de la modal
            $('#modalconfirm_btn_yes').unbind('click');

            // Al querer imprimir el Código QR 
			$('#modalconfirm_btn_yes').click(function(e) {
				e.preventDefault();
				// Se redirecciona al método "etiquetapdf" del controlador "ActivoController"
				window.location.href = '{{ route('activos.etiquetapdf', [ 'activo' => $activo]) }}';
				// Se oculta la modal
				$('#modalconfirm').modal('hide');
			});

            // Se muestra la modal
            $('#modalconfirm').modal('show');
		});
		
		function verPreviewEtiqueta() {

			var info_etiqueta  = '';

			info_etiqueta += '<div class="container">';
				info_etiqueta += '<div class="row">';
					info_etiqueta += '<div class="col-8">';
						info_etiqueta += '<ul class="m-0 p-0 list-unstyled">';
							info_etiqueta += '<li style="font-size:1.2em"><strong>Origen</strong> {{ ($activo->orden_compra == "") ? "---" : sprintf('%s %s', $activo->tipo_origen, $activo->orden_compra) }}</li>';
							info_etiqueta += '<li style="font-size:1.2em"><strong>N&deg; Inv.</strong> {{ ($activo->nro_inventario == "") ? "---" : $activo->nro_inventario }}</li>';
							// Si el Activo es una PC (Escritorio|Portátil|Servidor)
							@if ($activo->tipo_id < 4)
								info_etiqueta += '<li style="font-size:1.2em">{{ $activo->cpu }}</li>';
								info_etiqueta += '<li style="font-size:1.2em">{{ $activo->memoria }} - {{ $activo->hd_marca }} {{ $activo->hd_capacidad }}</li>';
								info_etiqueta += '<li style="font-size:1.2em">{{ $activo->motherboard }}</li>';
								info_etiqueta += '<li style="font-size:1.2em"><strong>LAN</strong> {{ $activo->ethernet_mac }}</li>';
								info_etiqueta += '<li style="font-size:1.2em"><strong>WLAN</strong> {{ $activo->wireless_mac }}</li>';
							@else
								info_etiqueta += '<li style="font-size:1.2em"><strong>Marca</strong>: {{ ($activo->marca == "") ? "---" : $activo->marca }}</li>';
								info_etiqueta += '<li style="font-size:1.2em"><strong>Modelo</strong>: {{ ($activo->modelo == "") ? "---" : $activo->modelo }}</li>';
							@endif
							info_etiqueta += '<li style="font-size:1.2em"><strong>N&deg; Serie</strong>: {{ ($activo->nro_serie == "") ? "---" : $activo->nro_serie }}</li>';
						info_etiqueta += '</ul>';
					info_etiqueta += '</div>';
					info_etiqueta += '<div class="col-4">';
						// Imagen del código QR
						info_etiqueta += '<img src="{{ route('qr.activo', [ 'activo' => $activo]) }}" class="img-fluid">';
					info_etiqueta += '</div>';
				info_etiqueta += '</div>';
			info_etiqueta += '</div>';
			// Se le pregunta al usuario si desea imprimir la etiqueta
			info_etiqueta += '<p class="text-center"><strong>¿Desea imprimir la etiqueta?</strong></p>';
			
			return info_etiqueta;
		}
	});
</script>
@endsection

@section('content')

	@include('layouts.modalconfirm')

<div class="container-fluid">

	<div class="card">
  		<div class="card-header alinear-form-control">
			<h5>Activo <i>Nº {{ $activo->id }}</i></h5>

			<a class="btn ml-5 mr-2 btn-secondary btn-sm" role="button" href="{{ route('activos.clone', [ 'id' => $activo->id ]) }}"><i class="fa fa-clone" aria-hidden="true"></i> Clonar</a>
			<a class="btn mx-2 btn-secondary btn-sm" role="button" href="{{ route('activos.fichapdf', [ 'activo' => $activo ]) }}"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Ficha PDF</a>
			<a id="btnVerPreviewEtiqueta" class="btn mx-2 btn-secondary btn-sm" role="button" href="#"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Etiqueta</a>
			<a class="btn mx-2 btn-secondary btn-sm" role="button" href="{{ route('activos.index') }}"><i class="fa fa-reply" aria-hidden="true"></i> Volver</a>
		</div>
		<div class="card-body">
			{{-- Tipo + Patrimonio + Orden Compra --}}
			<div class="form-row">
				<div id="tipo_id_control" class="form-group col-md-3 alinear-form-control align-items-start">
					<label for="tipo_id" class="col-md-4 text-right font-weight-bold">Tipo</label>
					<div class="col-md-8 ml-3">
						{{ $activo->activo_tipo->nombre }}
					</div>
				</div>
				<div id="nro_inventario_control" class="form-group col-md-3 alinear-form-control align-items-start">
					<label for="nro_inventario" class="col-md-4 text-right font-weight-bold">Nº Patrimonio</label>
					<div class="col-md-8">
						{{ $activo->nro_inventario }}
					</div>
				</div>
				<div id="tipo_origen_control" class="form-group col-md-3 alinear-form-control align-items-start">
					<label for="tipo_origen" class="col-md-5 text-right font-weight-bold">Tipo Origen</label>
					<div class="col-md-7">
						{{ $activo->tipo_origen_desc }}
					</div>
				</div>
				<div id="orden_compra_control" class="form-group col-md-3 alinear-form-control align-items-start">
					<label for="orden_compra" class="col-md-5 text-right font-weight-bold">Nº Origen</label>
					<div class="col-md-7">
						{{ $activo->orden_compra }}
					</div>
				</div>
			</div>
			{{-- Campos Reporte Inventario --}}
			<div class="form-row">
				<div id="titularidad_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="titularidad" class="col-md-4 text-right font-weight-bold">Titularidad</label>
					<div class="col-md-8 ml-3">
						{{ $activo->titularidad }} - {{ $activo->titularidad_desc }}
					</div>
				</div>
				<div id="estado_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="estado" class="col-md-4 text-right font-weight-bold">Estado</label>
					<div class="col-md-8">
						{{ $activo->estado }} - {{ $activo->estado_desc }}
					</div>
				</div>
				<div id="condicion_uso_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="condicion_uso" class="col-md-5 text-right font-weight-bold">Condici&oacute;nes de Uso</label>
					<div class="col-md-7">
						{{ $activo->condicion_uso }} - {{ $activo->condicion_uso_desc }}
					</div>
				</div>
			</div>
			{{-- Marca + Modelo + Nro de Serie --}}
			<div class="form-row">
				<div id="marca_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="marca" class="col-md-4 text-right font-weight-bold">Marca</label>
					<div class="col-md-8 ml-3">
						{{ $activo->marca }}
					</div>
				</div>
				<div id="modelo_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="modelo" class="col-md-4 text-right font-weight-bold">Modelo</label>
					<div class="col-md-8">
						{{ $activo->modelo }}
					</div>
				</div>
				<div id="nro_serie_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="nro_serie" class="col-md-5 text-right font-weight-bold">Nº Serie</label>
					<div class="col-md-7">
						{{ $activo->nro_serie }}
					</div>
				</div>
			</div>
			{{-- Responsable + Area --}}
			<div class="form-row">
				<div id="legajo_control" class="form-group col-md-6 alinear-form-control align-items-start">
					<label for="legajo" class="col-md-3 text-right font-weight-bold">Responsable</label>
					<div class="col-md-9">
						{{ $activo->legajo }} | {{ $activo->responsable->nombre_completo }} | {{ $activo->responsable->area->nombre }}
					</div>
				</div>
				<div id="area_control" class="form-group col-md-6 alinear-form-control align-items-start">
					<label for="area" class="col-md-2 text-right font-weight-bold">&Aacute;rea</label>
					<div class="col-md-10">
						{{ ($activo->responsable) ? $activo->responsable->area->nombre : '' }}
					</div>
				</div>
			</div>
			{{-- Ubicación + Fecha de alta --}}
			<div class="form-row">
				<div id="ubicacion_control" class="form-group col-md-6 alinear-form-control align-items-start">
					<label for="ubicacion" class="col-md-3 text-right font-weight-bold">Ubicaci&oacute;n</label>
					<div class="col-md-9">
						{{ $activo->ubicacion }}
					</div>
				</div>
				<div id="fecha_alta_control" class="form-group col-md-3 alinear-form-control align-items-start">
					<label for="fecha_alta" class="col-md-6 text-right font-weight-bold">Fecha de Alta</label>
					<div class="col-md-6">
						{{ $activo->fecha_alta }}
					</div>
				</div>
			</div>
			{{-- Nombre equipo + S.O. + Serial Sis. Op. --}}
			<div class="form-row">
				<div id="nombre_equipo_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="nombre_equipo" class="col-md-4 px-1 text-right font-weight-bold">Nombre de Equipo</label>
					<div class="col-md-8 ml-3">
						{{ $activo->nombre_equipo }}
					</div>
				</div>
				<div id="sistema_operativo_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="sistema_operativo" class="col-md-4 text-right font-weight-bold px-0">Sistema Operativo</label>
					<div class="col-md-8">
						{{ $activo->sistema_operativo }}
					</div>
				</div>
				<div id="sistema_operativo_serie_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="sistema_operativo_serie" class="col-md-4 text-right font-weight-bold px-0">Serial Sis. Op.</label>
					<div class="col-md-8">
						{{ $activo->sistema_operativo_serie }}
					</div>
				</div>
			</div>
			{{-- Microprocesador + RAM + Motherboard + Fuente --}}
			<div class="form-row">
				<div id="cpu_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="cpu" class="col-md-4 px-1 text-right font-weight-bold">Microprocesador</label>
					<div class="col-md-8 ml-3">
						{{ $activo->cpu }}
					</div>
				</div>
				<div id="memoria_control" class="form-group col-md-2 alinear-form-control align-items-start">
					<label for="memoria" class="col-md-4 text-right font-weight-bold pr-0">RAM</label>
					<div class="col-md-8 ml-3">
						{{ $activo->memoria }}
					</div>
				</div>
				<div id="fuente_control" class="form-group col-md-2 alinear-form-control align-items-start">
					<label for="fuente" class="col-md-3 text-right font-weight-bold pl-0 pr-0">Fuente</label>
					<div class="col-md-8 ml-3">
						{{ $activo->fuente }}
					</div>
				</div>
				<div id="motherboard_control" class="form-group  col-md-4 alinear-form-control align-items-start">
					<label for="motherboard" class="col-md-4 text-right font-weight-bold pr-0" >Motherboard</label>
					<div class="col-md-8">
						{{ $activo->motherboard }}
					</div>
				</div>
			</div>
			{{-- Disco + Capacidad + DVD + Marca y Modelo --}}
			<div class="form-row">
				<div id="hd_marca_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="hd_marca" class="col-md-4 text-right font-weight-bold">HD</label>
					<div class="col-md-8 ml-3">
						{{ $activo->hd_marca }}
					</div>
				</div>
				<div id="hd_capacidad_control" class="form-group col-md-2 alinear-form-control align-items-start">
					<label for="hd_capacidad" class="col-md-7 text-right font-weight-bold pr-0 pl-0">Capacidad HD</label>
					<div class="col-md-5 ml-3">
						{{ $activo->hd_capacidad }}
					</div>
				</div>
				<div id="dvd_rw_control" class="form-group col-md-2 alinear-form-control align-items-start">
					<label for="dvd_rw" class="col-md-6 text-right font-weight-bold">DVD/CD</label>
					<div class="col-md-6">
						{{ ($activo->dvd_rw == 0) ? 'No':'Si' }}
					</div>
				</div>
				<div id="dvd_rw_marca_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="dvd_rw_marca" class="col-md-4 text-right font-weight-bold">Marca DVD/CD</label>
					<div class="col-md-8">
						{{ $activo->dvd_rw_marca }}
					</div>
				</div>
			</div>
			{{-- Ethernet dinámico --}}
			<div class="form-row">
				<div id="ethernet_dinamico_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="ethernet_dinamico" class="col-md-4 text-right font-weight-bold pr-0 pl-0">Red Ethernet Din&aacute;mico</label>
					<div class="col-md-8 ml-3">
						{{ ($activo->ethernet_dinamico == 0) ? 'No':'Si' }}
					</div>
				</div>
				<div id="ethernet_mac_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="ethernet_mac" class="col-md-4 text-right font-weight-bold">Ethernet MAC</label>
					<div class="col-md-8">
						{{ $activo->ethernet_mac }}
					</div>
				</div>
				<div id="ethernet_ip_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="ethernet_ip" class="col-md-4 text-right font-weight-bold">Ethernet IP</label>
					<div class="col-md-8">
						{{ $activo->ethernet_ip }}
					</div>
				</div>
			</div>
			<div class="form-row">
				<div id="ethernet_mask_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="ethernet_mask" class="col-md-4 text-right font-weight-bold pr-0 pl-0">Ethernet Subnet Mask</label>
					<div class="col-md-8 ml-3">
						{{ $activo->ethernet_mask }}
					</div>
				</div>
				<div id="ethernet_gw_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="ethernet_gw" class="col-md-4 text-right font-weight-bold">Ethernet Gateway</label>
					<div class="col-md-8">
						{{ $activo->ethernet_gw }}
					</div>
				</div>
				<div id="ethernet_dns_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="ethernet_dns" class="col-md-4 text-right font-weight-bold">Ethernet DNS</label>
					<div class="col-md-8">
						{{ $activo->ethernet_dns }}
					</div>
				</div>
			</div>
			{{-- Wireless dinámico --}}
			<div class="form-row">
				<div id="wireless_dinamico_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="wireless_dinamico" class="col-md-4 text-right font-weight-bold pl-0 pr-0">Red Wireless Din&aacute;mico</label>
					<div class="col-md-8 ml-3">
						{{ ($activo->wireless_dinamico == 0) ? 'No':'Si' }}
					</div>
				</div>
				<div id="wireless_mac_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="wireless_mac" class="col-md-4 text-right font-weight-bold">Wireless MAC</label>
					<div class="col-md-8">
						{{ $activo->wireless_mac }}
					</div>
				</div>
				<div id="wireless_ip_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="wireless_ip" class="col-md-4 text-right font-weight-bold">Wireless IP</label>
					<div class="col-md-8">
						{{ $activo->wireless_ip }}
					</div>
				</div>
			</div>
			<div class="form-row">
				<div id="wireless_mask_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="wireless_mask" class="col-md-4 text-right font-weight-bold pl-0 pr-0">Wireless Subnet Mask</label>
					<div class="col-md-8 ml-3">
						{{ $activo->wireless_mask }}
					</div>
				</div>
				<div id="wireless_gw_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="wireless_gw" class="col-md-4 text-right font-weight-bold">Wireless Gateway</label>
					<div class="col-md-8">
						{{ $activo->wireless_gw }}
					</div>
				</div>
				<div id="wireless_dns_control" class="form-group col-md-4 alinear-form-control align-items-start">
					<label for="wireless_dns" class="col-md-4 text-right font-weight-bold">Wireless DNS</label>
					<div class="col-md-8">
						{{ $activo->wireless_dns }}
					</div>
				</div>
			</div>
			{{-- Observaciones --}}
			<div class="form-row">
				<div id="observaciones_control" class="form-group col-md-12 alinear-form-control align-items-start">
					<label for="observaciones" class="col-md-1 text-right font-weight-bold px-0">Observaciones</label>
					<div class="col-md-10 ml-5">
						{{ $activo->observaciones }}
					</div>
				</div>
			</div>

	        @if(!empty($archivos))
		        <div class="form-row">
					<div class="form-group col-md-12 alinear-form-control align-items-start">
						<label class="col-md-1 text-right font-weight-bold px-0">
							Archivos Actuales
						</label>
						<div class="col-md-10 ml-5">
							<ul>
					            @foreach($archivos as $archivo)
					                <li>
					                    <a href="{{ url('activos_pdf/' . $archivo->getFilename()) }}" target="_blank">
					                    	{{ substr($archivo->getFilename(), strpos($archivo->getFilename(), '__') + 2) }}
					                    </a>
					                </li>
					            @endforeach
					        </ul>
					    </div>
				    </div>
				</div>
		    @endif

		</div> {{-- fin del card-body --}}
	</div> {{-- fin del card --}}
</div>
@endsection

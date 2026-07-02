
function elegir(fila) {

	if ( $('#chk_elegido'+fila).prop('checked') ) {

		$('#anio_carga_grupal'+fila).css('color', '#000');
		$('#tipo_carga_grupal'+fila).css('color', '#000');
		$('#numero_carga_grupal'+fila).css('color', '#000');
		$('#iniciador_carga_grupal'+fila).css('color', '#000');
		$('#extracto_legible_carga_grupal'+fila).css('color', '#000');
		
		$('#anio_carga_grupal'+fila).prop('disabled', false);
		$('#tipo_carga_grupal'+fila).prop('disabled', false);
		$('#numero_carga_grupal'+fila).prop('disabled', false);
		$('#iniciador_carga_grupal'+fila).prop('disabled', false);
		$('#descripcion_iniciador_carga_grupal'+fila).prop('disabled', false);
		$('#caratula_carga_grupal'+fila).prop('disabled', false);
		$('#extracto_carga_grupal'+fila).prop('disabled', false);
	} else {
		$('#anio_carga_grupal'+fila).css('color', '#454B4F');
		$('#tipo_carga_grupal'+fila).css('color', '#454B4F');
		$('#numero_carga_grupal'+fila).css('color', '#454B4F');
		$('#iniciador_carga_grupal'+fila).css('color', '#454B4F');
		$('#extracto_legible_carga_grupal'+fila).css('color', '#454B4F');
		
		$('#anio_carga_grupal'+fila).prop('disabled', true);
		$('#tipo_carga_grupal'+fila).prop('disabled', true);
		$('#numero_carga_grupal'+fila).prop('disabled', true);
		$('#iniciador_carga_grupal'+fila).prop('disabled', true);
		$('#descripcion_iniciador_carga_grupal'+fila).prop('disabled', true);
		$('#caratula_carga_grupal'+fila).prop('disabled', true);
		$('#extracto_carga_grupal'+fila).prop('disabled', true);
	}
}

jQuery(document).ready(function() {

	$('#odcg_fecha_desde').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });

    $('#odcg_fecha_hasta').datepicker({
        locale: 'es-es',
        uiLibrary: 'bootstrap4',
        iconsLibrary: 'fontawesome',
        format: 'dd/mm/yyyy',
        size: 'small'
    });

	$('#btBuscar').click(function() {

		let mensaje = "";
		let error = false;
		
		if ( $('#odcg_fecha_desde') && $('#odcg_fecha_hasta') ) {
			if( $('#odcg_fecha_desde').val() == '' ) {
				mensaje += "Debe seleccionar la Fecha Desde.\n";
				error = true;
			}
			if( $('#odcg_fecha_hasta').val() == '' ) {
				mensaje += "Debe seleccionar la Fecha Hasta.\n";
				error = true;
			}
			
			if ( esLaFechaMayor($('#odcg_fecha_desde').val(), $('#odcg_fecha_hasta').val()) ) {
				mensaje += "La fecha Desde debe ser menor a la fecha Hasta.";
				error = true;
			}
		}
		
		if ( error ) {
			mostrarCartel(mensaje, 2);
		} else {
			$('#formEdicion').submit();
	    }
	});
	
	// Si hay expedientes que elegir
	if ( $('#cantidad_listado').val() > 0 ) {
		// Al usar el botón Cargar
		$('#btCargar').click(function() {

			// Por lo menos un chekbox debe estar tildado
			if ( !verificarCheckbox('.check_carga_grupal') )
				mostrarCartel("Debe seleccionar un Expediente o Nota.", 2);
			else {
				// Se modifica la acción para realizar la carga grupal
				$('#accion').val('cargarGrupalmente');
				// Se envía el formulario
				$('#formEdicion').submit();
			}
		});
	}
	
	$('#btVolver').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=editar&id='+$('#id_sesion').val()+'&cod_seccion='+$('#cod_seccion').val());
	});
    
});

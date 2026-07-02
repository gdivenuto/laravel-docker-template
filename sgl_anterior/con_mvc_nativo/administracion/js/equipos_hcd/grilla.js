jQuery(document).ready(function() {

    function refrezcarComboResponsables() {

        if ($('#f_cod_area').val() != '0') {
            tipo_area = $('#f_cod_area').val().substring(0, 2);
            
            if ( tipo_area == '02' ) {
                var url = $('#url_abms').val()+'?controlador='+$('#controlador').val();
                url += '&accion=refrescarComboResponsables';
                url += '&f_cod_area='+$('#f_cod_area').val();
                url += '&f_cod_responsable='+$('#f_cod_responsable').val();
                url += '&se_edita=0';
                
                // Se buscan los responsables del area determinada que se encuentren ACTIVOS en el Sistema de Personal 
                refrescar(url, '#f_cod_responsable');
            }
        }
    }
    
	function buscar() {

		var url  = $('#url_abms').val();
			url += '?controlador='+$('#controlador').val();
			url += '&accion=listar';
			url += '&f_cod_area='+$('#f_cod_area').val();
			url += '&f_cod_responsable='+$('#f_cod_responsable').val();
			url += '&f_nombre_netbios='+$('#f_nombre_netbios').val();
			url += '&f_direccion_mac='+$('#f_direccion_mac').val();

		redireccionar(url);
	}

    $('#f_cod_area').ready( function() {
        if ( $('#f_cod_area').val() != '0' ) {
            refrezcarComboResponsables();
        }
    });
    // Para buscar por Area
    $('#f_cod_area').change( function() {
        if ( $('#f_cod_area').val() != '0' ) {
            buscar();
        }
    });
    
    // Para buscar por Responsable
    $('#f_cod_responsable').change( function() {
        if ( $('#f_cod_responsable').val() != '0' ) {
            buscar();
        }
    });
    
    // Para buscar por Nombre Netbios
     $('#f_nombre_netbios').keypress( function(e) {
        if ( $('#f_nombre_netbios').val() != '' && e.which == 13 ) {
            e.preventDefault();
            buscar();
        }
    });

    // Para buscar por MAC
 	$('#f_direccion_mac').keypress( function(e) {
        if ( $('#f_direccion_mac').val() != '' && e.which == 13 ) {
            e.preventDefault();
            buscar();
        }
    });

	$('#btBuscar').click( function() {
		buscar();
	});

	$('#btLimpiar').click( function() {
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val());
	});

	$('#btNuevo').click( function() {
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=editar');
	});

    mostrarModal();

    $("#item_informatica").addClass("text-info");
});
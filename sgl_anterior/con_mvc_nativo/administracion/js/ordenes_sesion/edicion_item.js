
function setearBotonCargaGrupal() {

    // Si se ha elegido una sección
    if( $('#cod_seccion').val() != '0' ) {

        // Si la sección padre elegida es Asuntos Entrados
        if( $('#seccion_padre').val() == '20000000' ) {

            // Se muestra el botón de Carga Grupal
            $('#contenedoraCargaGrupal').css('display', 'block');
        } else {
            // Sino se oculta
            $('#contenedoraCargaGrupal').css('display', 'none');
        }
    } else {
        // Sino se oculta
        $('#contenedoraCargaGrupal').css('display', 'none');
    }
}

function validarItemOrdenDiaSesion() {

    let mensaje = '';
    let error = false;
    
    if ( $('#seccion_padre').val() == '0' ) {
        mensaje += "Debe seleccionar una Secci"+o_acentuada+"n de la cual depende.<br>";
        error = true;
    }
    
    if ( $('#cod_seccion').length > 1 && $('#cod_seccion').val() == '0' ) {
        mensaje += "Debe seleccionar una Secci"+o_acentuada+"n.<br>";
        error = true;
    }
    
    if ( $('#anio').val().trim() == '' ) {
        mensaje += "Debe ingresar un A"+enie+"o.<br>";
        error = true;
    }
    
    // Si el Tipo es "Otros", al cargar un Decreto, el campo Número es obligatorio también.
    // (se utiliza 1 en el campo Número cuando Tipo es "Otros")
    // 
    // Si no se ingresa un Número
    if ($('#numero').val().trim() == '' || $('#numero').val().trim() == 0) {
        mensaje += "Debe ingresar un N"+u_acentuada+"mero.<br>";
        error = true;
    }
    // Si el número no es válido para un Expediente
    else if ($('#tipo').val() == 'E' && $('#numero').val().trim() < 1001) {
        mensaje += "Debe ingresar un N"+u_acentuada+"mero v"+a_acentuada+"lido para el Expediente.<br>";
        error = true;
    }
    // Si el número no es válido para una Nota
    else if ($('#tipo').val() == 'N' && $('#numero').val().trim() < 1) {
        mensaje += "Debe ingresar un N"+u_acentuada+"mero v"+a_acentuada+"lido para la Nota.<br>";
        error = true;
    }
    
    // Si no se ingresa un Número
    if ($('#extracto').val().trim() == '') {
        mensaje += "Debe ingresar un Extracto.<br>";
        error = true;
    }

    // Si posee un expediente con despacho de archivo
    if ( $('#con_despacho_archivo').prop('checked') ) {
        // Si no ha ingresado el año de dicho expediente con despacho
        if ($('#anio_despacho_archivo').val().trim() == '' ) {
            mensaje += "Debe ingresar un A"+enie+"o para el exped./nota con despacho de archivo.<br>";
            error = true;
        }
        // Si no ha ingresado el número de dicho expediente con despacho
        if ($('#numero_despacho_archivo').val().trim() == '' ) {
            mensaje += "Debe ingresar un N"+u_acentuada+"mero para el exped./nota con despacho de archivo.<br>";
            error = true;
        }
    }

    if ( error ) {
        mostrarCartel(mensaje, 2);
    } else {
        // Se muestra el spinner
        $('#muestra_spinner').click();

        $('#formEdicion').submit();
    }
}

function mostrarClaveExpedConDespacho() {
    $('#anio_despacho_archivo').removeClass('d-none').addClass('d-inline-block');
    $('#tipo_despacho_archivo').removeClass('d-none').addClass('d-inline-block');
    $('#numero_despacho_archivo').removeClass('d-none').addClass('d-inline-block');
    $('#btBuscarDocElec').removeClass('d-none').addClass('d-inline-block');
}

function ocultarClaveExpedConDespacho() {
    $('#anio_despacho_archivo').removeClass('d-inline-block').addClass('d-none');
    $('#tipo_despacho_archivo').removeClass('d-inline-block').addClass('d-none');
    $('#numero_despacho_archivo').removeClass('d-inline-block').addClass('d-none');
    $('#btBuscarDocElec').removeClass('d-inline-block').addClass('d-none');
}

function asignarDespacho(orden_actuacion, detalle)
{
    let url  = $('#url_abms').val()+'?controlador='+$('#controlador').val();
        url += '&accion=asignarDespacho';
        url += '&id_item='+$('#id').val().trim();
        url += '&orden_actuacion='+orden_actuacion;
        url += '&detalle='+detalle.replace(patron_espacio_blanco_global, "%20");
    
    refrescar(url, '#cont_despachos_asignados');
}

function editarDetalleModal(orden_actuacion, detalle)
{
    $('#orden_actuacion').val(orden_actuacion);
    $('#despacho_asignado').val(detalle);
    $('#edicionDetalleModal').modal('show');
}

function eliminarDespacho(orden_actuacion)
{
    if (confirm('¿Desea eliminar el despacho?')) {

        let url  = $('#url_abms').val()+'?controlador='+$('#controlador').val();
            url += '&accion=eliminarDespacho';
            url += '&id_item='+$('#id').val().trim();
            url += '&orden_actuacion='+orden_actuacion;

        refrescar(url, '#cont_despachos_asignados');
    }
}

jQuery(document).ready(function() {
    
    // Al cargarse el combo de secciones
    $('#seccion_padre').ready(function(){

        // Si hay una sección padre elegida
        if ( $('#seccion_padre').val() != '0' ) {
            refrescar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=obtenerSubSecciones&seccion_padre='+$('#seccion_padre').val()+'&cod_seccion='+$('#cod_seccion').val(), '#cod_seccion');
        }
    });

    // Al surgir un cambio en el combo de secciones
    $('#seccion_padre').change(function(){

        // Si hay una sección padre elegida
        if ( $('#seccion_padre').val() != '0' ) {
            refrescar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=obtenerSubSecciones&seccion_padre='+$('#seccion_padre').val()+'&cod_seccion='+$('#cod_seccion').val(), '#cod_seccion');
        }
    });
    
    $('#cod_seccion').ready(function(){

        if ( $('#cod_seccion').val() != 0 ) {

            $('#ods_contenedora_resto_datos_item').css('display', 'block');
        }
        setearBotonCargaGrupal();
    });

    $('#cod_seccion').change(function(){

        if ( $('#cod_seccion').val() != 0 ) {
            
            $('#ods_contenedora_resto_datos_item').css('display', 'block');
        }
        setearBotonCargaGrupal();
    });

    $('#ods_contenedora_resto_datos_item').css('display', 'none');

    $('#tipo').ready(function(){
        if( $('#tipo').val() == '0' ){
            $('#cont_con_despacho_archivo').removeClass('d-none').addClass('d-inline-block');
            mostrarClaveExpedConDespacho();
        } else {
            $('#cont_con_despacho_archivo').removeClass('d-inline-block').addClass('d-none');
            ocultarClaveExpedConDespacho();
        }
    });
    $('#tipo').change(function(){
        if( $('#tipo').val() == '0' ){
            $('#cont_con_despacho_archivo').removeClass('d-none').addClass('d-inline-block');
            mostrarClaveExpedConDespacho();
        } else {
            $('#cont_con_despacho_archivo').removeClass('d-inline-block').addClass('d-none');
            ocultarClaveExpedConDespacho();
        }
    });

    $('#numero').blur(function(){

        // Si está definido el Número
        if ( $('#numero').val().trim() != '' &&  $('#numero').val().trim() != 0) {

            // Si es diferente el numero al registrado en la orden, y el tipo de documento es expediente o nota
            if ( ($('#numero').val().trim() != $('#numero_actual').val().trim()) && ($('#tipo').val() == 'E' || $('#tipo').val() == 'N') ) {
                // Si el número no corresponde con un Expediente
                if ($('#tipo').val() == 'E' && $('#numero').val().trim() < 1001) {
                    mostrarCartel("Debe ingresar un N"+u_acentuada+"mero v"+a_acentuada+"lido para el Expediente.", 2);
                } else if ($('#tipo').val() == 'N' && $('#numero').val().trim() < 1) {
                    // Si el número no corresponde con una Nota
                    mostrarCartel("Debe ingresar un N"+u_acentuada+"mero v"+a_acentuada+"lido para la Nota.", 2);
                } else {
                    // Se verifica si existe en el sistema y se muestran sus datos en caso afirmativo
                    refrescar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=obtenerDatosExpedienteItem&anio='+$('#anio').val()+'&tipo='+$('#tipo').val()+'&numero='+$('#numero').val()+'&id_sesion='+$('#id_sesion').val()+'&cod_seccion='+$('#cod_seccion').val(), '#ods_datos_expediente_nota');
                }
            } else {
                // Se muestra el botón Guardar
                $('#btGuardar').css('display', 'inline-block');
            }
        } else {
            // Se oculta el botón Guardar
            $('#btGuardar').css('display', 'none');
        }
    });

    // Si está definida la Sección
    if ( $('#cod_seccion').val() != '0' ) {
        // Se establece el foco en el campo número
        $('#numero').focus();
    } else {
        // Si está definida la sección padre
        if ( $('#seccion_padre').val() != '0' ) {
            // Se establece el foco en el campo número
            $('#numero').focus();
        } else {
            // Se establece el foco en el combo de la sección padre
            $('#seccion_padre').focus();
        }
    }
    
    if ( $('#accion').val() == 'insertarItem' ) {
        $('#btGuardar').css('display', 'none');
    }
    
    // En la vista previa del ítem, si la sección permite mostrar el Autor (ex Iniciador),
    // ahora se muestra sólo el Autor, es decir, el texto editado en el campo id='autor' del formulario
    $('#autor').ready(function() {
        if ( $('#autor_en_vista_previa') )
            $('#autor_en_vista_previa').html(($('#autor').val() != '') ? $('#autor').val()+': ' : '');
    });
    $('#autor').change(function() {
        if ( $('#autor_en_vista_previa') )
            $('#autor_en_vista_previa').html(($('#autor').val() != '') ? $('#autor').val()+': ' : '');
    });

    $('#btActualizarExtracto').click(function() {
        
        if ( $('#anio').val() != '' && ($('#tipo').val() == 'E' || $('#tipo').val() == 'N') && $('#numero').val().trim() != '' ) {

            let url = $('#url_abms').val()+'?controlador='+$('#controlador').val();
                url += '&accion=actualizarExtracto';
                url += '&anio='+$('#anio').val();
                url += '&tipo='+$('#tipo').val()
                url += '&numero='+$('#numero').val().trim();

            $.ajax({
                type: "POST",
                url: url,
                success: function (respuesta) {
                    $('#extracto').val(respuesta);
                }
            });
        } else {
            mostrarCartel("Debe ingresar previamente la clave (A"+enie+"o-Tipo-N"+u_acentuada+"mero) de un Expediente o Nota.", 2);
        }
    });

    $('#btCopiarIniciador').click(function() {
        
        if ( $('#anio').val() != '' && ($('#tipo').val() == 'E' || $('#tipo').val() == 'N') && $('#numero').val().trim() != '' ) {
            // Si se posee la descripción del Iniciador
            if ( $('#descripcion_iniciador').val() != '' )
                // Se asigna en el textarea de Iniciador/Autor
                $('#autor').val($('#descripcion_iniciador').val());
            else
                mostrarCartel("No se posee una descripci"+o_acentuada+"n del Iniciador.", 2);
                
        } else
            mostrarCartel("Debe ingresar previamente la clave (A"+enie+"o-Tipo-N"+u_acentuada+"mero) de un Expediente o Nota.", 2);
    });

    $('#btCopiarAutor').click(function() {
        
        if ( $('#anio').val() != '' && ($('#tipo').val() == 'E' || $('#tipo').val() == 'N') && $('#numero').val().trim() != '' ) {
            // Si se posee la descripción del Autor
            if ( $('#descripcion_autor').val() != '' )
                // Se asigna en el textarea de Iniciador/Autor
                $('#autor').val($('#descripcion_autor').val());
            else
                mostrarCartel("No se posee una descripci"+o_acentuada+"n del Autor.", 2);
                
        } else
            mostrarCartel("Debe ingresar previamente la clave (A"+enie+"o-Tipo-N"+u_acentuada+"mero) de un Expediente o Nota.", 2);
    });

    if (permite_carga_giros) {
        $('#chk_giros').change(function() {
            // Si está tildado
            if ( $('#chk_giros').prop('checked') ) {
                // Se habilita el campo 'giros'
                $('#giros').prop('disabled', false);
                $('#giros').focus();
            } else {
                // sino, se deshabilita el campo 'giros'
                $('#giros').prop('disabled', true);
            }
        });
    }

    $('#con_despacho_archivo').ready(function() {
        if ($('#con_despacho_archivo').prop('checked'))
            mostrarClaveExpedConDespacho();
        else
            ocultarClaveExpedConDespacho();
    });
    $('#con_despacho_archivo').change(function() {
        if ($('#con_despacho_archivo').prop('checked'))
            mostrarClaveExpedConDespacho();
        else
            ocultarClaveExpedConDespacho();
    });

    $('#btCargaGrupal').click(function() {
        redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=listarCargaGrupal&id_sesion='+$('#id_sesion').val()+'&cod_seccion='+$('#cod_seccion').val());
    });

    $('#btBuscarDocElec').click(function() {

        if ( $('#anio_despacho_archivo').val().trim() == '' ) {
            mostrarCartel("Debe ingresar un <b>A&ntilde;o</b> para el expediente con despacho a archivo.", 2);
        }
        else if( $('#numero_despacho_archivo').val().trim() == '' ) {
            mostrarCartel("Debe ingresar un <b>N"+u_acentuada+"mero</b> para el expediente con despacho a archivo.", 2);
        }
        else {
            // Si el número no corresponde con un Expediente
            if ($('#tipo_despacho_archivo').val() == 'E' && $('#numero_despacho_archivo').val().trim() < 1001) {
                mostrarCartel("Debe ingresar un N"+u_acentuada+"mero v"+a_acentuada+"lido para el Expediente.", 2);
            }
            else if ($('#tipo_despacho_archivo').val() == 'N' && $('#numero_despacho_archivo').val().trim() < 1) {
                // Si el número no corresponde con una Nota
                mostrarCartel("Debe ingresar un N"+u_acentuada+"mero v"+a_acentuada+"lido para la Nota.", 2);
            }
            else {
                let url  = $('#url_abms').val()+'?controlador='+$('#controlador').val();
                    url += '&accion=obtenerDocumentosExpedElec';
                    url += '&anio='+$('#anio_despacho_archivo').val().trim();
                    url += '&tipo='+$('#tipo_despacho_archivo').val();
                    url += '&numero='+$('#numero_despacho_archivo').val().trim();

                refrescar(url, '#cont_documentos_elec');
            }
        }
    });

    $('#btnActualizarDetalleDespacho').click(function () {

        let id_item = $('#id').val().trim();
        let orden_actuacion = $('#orden_actuacion').val().trim();
        let detalle = $('#despacho_asignado').val().trim().replace(patron_espacio_blanco_global, "%20");
        
        if (detalle == '')
            mostrarCartel("Debe ingresar un detalle.", 2);
        else {
            let url  = $('#url_abms').val()+'?controlador='+$('#controlador').val();
                url += '&accion=actualizarDetalleDespacho';
                url += '&id_item='+id_item;
                url += '&orden_actuacion='+orden_actuacion;
                url += '&detalle='+detalle;
            
            //alert(url);
            $('#edicionDetalleModal').modal('hide');
            refrescar(url, '#cont_despachos_asignados');
        }
    });

	$('#btGuardar').click(function(){
		validarItemOrdenDiaSesion();
	});

	$('#btCancelar').click(function(){
		redireccionar($('#url_abms').val()+'?controlador='+$('#controlador').val()+'&accion=editar&id='+$('#id_sesion').val()+'&cod_seccion='+$('#cod_seccion').val()+'&pagina='+$('#pagina').val());
	});
    
    mostrarModal();
    
    if ($('#perfil_usuario').val() == 14)
    	$("#item_informatica").addClass("text-info");
    else if ($('#perfil_usuario').val() == 10)
    	$("#item_administracion").addClass("text-info");
    else if ($('#perfil_usuario').val() == 12)
    	$("#item_comisiones").addClass("text-info");
});
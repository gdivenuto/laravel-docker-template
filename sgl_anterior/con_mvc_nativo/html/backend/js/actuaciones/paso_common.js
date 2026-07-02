//
// Libreria general JS para todos los Pasos de Actuación
//

// ---- Helpers ---------------------------------------------------------------
formSubmitOrRedirect = function (args) {
	if ($('#formPasoActuacion').length > 0)
		$('#formPasoActuacion').submit();
	else
		location.href = `${base_url}/index.php?c=actuaciones&a=siguiente&actuacion=${tipo_actuacion}`;
};

actuacionEnUltimoPaso = function() {
	return actuacion.pasos.length == (paso.id_paso + 1);
};

/**
 * Se habilitan/deshabilitan determinados botones de la UI
 * Se muestra/oculta una modal de precarga
 * @param  {boolean} habilitado Indicador para manipular la IU
 */
habilitarUI = function(habilitado) {
	if (habilitado) {
		// UI habilitada
		$('.btn-cancelar').prop('disabled', false);
		$('.btn-anterior').prop('disabled', false);
		$('.btn-siguiente').prop('disabled', false);

		setTimeout(function(e){
			$('#v_modal_espera_paso').modal('hide');
		}, parseFloat($('#v_modal_espera_paso').attr('data-delay')) * 1000);
		// al valor de data-delay (es 0.6 en el template/html) lo multiplicamos por 1000, para que espere 600 mseg
	}
	else {
		// UI deshabilitada
		$('.btn-cancelar').prop('disabled', true);
		$('.btn-anterior').prop('disabled', true);
		$('.btn-siguiente').prop('disabled', true);

		setTimeout(function(e){
			$('#v_modal_espera_paso').modal('show');
		}, parseFloat($('#v_modal_espera_paso').attr('data-open')) * 1000);
		// al valor de data-open (es 0.6 en el template/html) lo multiplicamos por 1000, para que espere 600 mseg
	}
}

// ---- Callback comun de botones ---------------------------------------------

callbackCancelar = function (e) {
	// Siempre existe una confirmacion antes de cancelar.
	showModal('Aviso', '¿Est&aacute; seguro que desea abandonar la actuación?',
	{
		btn_si: {
			class: 'btn-primary',
			action: function (e) {
				location.href = `${base_url}/index.php?c=actuaciones&a=cancelar&actuacion=${tipo_actuacion}`;
			}
		},
		btn_no: { class: 'btn-default' }
	})
};

callbackAnterior = function (e) {
	// Si el flag de advertencia esta activado, avisamos antes de retroceder.
	if (flag_advertir_anterior) {
		showModal('Aviso', '¿Est&aacute; seguro que desea volver atrás sin confirmar este paso?',
		{
			btn_si: {
				class: 'btn-primary',
				action: function (e) {
					location.href = `${base_url}/index.php?c=actuaciones&a=anterior&actuacion=${tipo_actuacion}`;
				}
			},
			btn_no: { class: 'btn-default' }
		})
	} else {
		location.href = `${base_url}/index.php?c=actuaciones&a=anterior&actuacion=${tipo_actuacion}`;
	}
};

callbackSiguiente = function (e) {
	// El boton siguiente delega su funcionamiento a la accion "confirmarPasoActual",
	// para que sea delegada en los JS de cada paso.

	// Deshabilito la UI
	habilitarUI(false);

	// Ademas de confirmar el paso, si estoy en el último paso, confirmo la actuación.
	if (! actuacionEnUltimoPaso()) {
		formSubmitOrRedirect();
	} else {
		// Peticion asíncrona
	    $.ajax({
	        method: "GET",
	        url: `${base_url}index.php?c=actuaciones&a=verificaractuacion`,
	        dataType: 'json'
	    })
	    .done(function( respuesta ) {
	    	if (respuesta.estado == 'OK') {
	    		formSubmitOrRedirect();
	    	} else {
	    		habilitarUI(true); // Habilito la UI
	    		lista_errores = $.map(respuesta.data, function(v, k) { return `<li>${v}</li>`;}).join();
	    		showModal('Advertencia', `<p>La actuación presenta una condición particular:</p><p><ul>${lista_errores}</ul></p><p>Presione <strong>Aceptar</strong> para ignorar esta advertencia y terminar la actuación o <strong>Cerrar</strong> para dejarla pendiente.</p>`, {
					btn_aceptar: { class: 'btn-default', action: formSubmitOrRedirect },
					btn_cerrar: { class: 'btn-primary' },
				});
	    	}
	    })
	    .fail(function( jqXHR, textStatus, errorThrown ) {
	    	habilitarUI(true); // Habilito la UI
	    	showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
	    });
	}
};

callbackOnSubmitFormPasoActuacion = function(e) {
	let result = confirmarPasoActual();
	
	// La manipulacion de errores se delega en la función 'manejarErrores()', si es que esta definida para el paso.
	if ((!result) && (typeof manejarErrores === "function" ))
		manejarErrores();

	return result;
};

// ---- Funciones generales ---------------------------------------------------
manejarErrores = function () {
    var msg_errores = $.map(errores, function (v) {
        return `<li>${v}</li>`;
    }).join('');

    if (errores.length > 0) {
    	habilitarUI(true); // Habilito la UI
        showModal('Error', `<p>Los datos ingresados son incorrectos:</p><ul>${msg_errores}</ul>`,
        {
            btn_cerrar: {
                class: 'btn-primary'
            }
        });
    }
};

asignarAccionBotonesWizard = function () {
	$('.btn-cancelar').click(callbackCancelar);
	$('.btn-anterior').click(callbackAnterior);
	$('.btn-siguiente').click(callbackSiguiente);
};

inicializarInterfase = function () {
    // form-control.js: Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
    fixValidatorBootstrap();

    // Se muestra el texto informativo, si aplica
    if (actuacion_texto_informativo != '') {
    	$('#actuacion_texto_informativo').html(`<h2>${actuacion_texto_informativo}</h2>`);
    	$('#actuacion_texto_informativo').css('display', 'inline-block');
    }

    // Agrego las expresiones regulares extra
    asignarValidatorExpresionesRegulares();

    // Agrego funcionalida básica de los botones del wizard
    if (typeof asignarAccionBotonesWizard === "function" ) 
    	asignarAccionBotonesWizard();
    else
    	alert('Función "asignarAccionBotonesWizard" indefinida.');

    // Habilito/Deshabilito controles segun configuración del paso
    if (typeof inicializarFormulario === "function" ) 
    	inicializarFormulario();    
    else
    	alert('Función "inicializarFormulario" indefinida.');

    // Agrego logica de validacion en el submit del formulario (si existe)
    if ($('#formPasoActuacion').length > 0) {
	   	if (typeof confirmarPasoActual === "function" )
			$('#formPasoActuacion').on('submit', callbackOnSubmitFormPasoActuacion);
		else
			alert('Función "confirmarPasoActual" indefinida.');
	}

    // Recupero los datos de la transaccion anterior, si aplica...
    if (typeof recuperarTransaccion === "function" )
    	recuperarTransaccion();
    else
    	alert('Función "recuperarTransaccion" indefinida.');
};

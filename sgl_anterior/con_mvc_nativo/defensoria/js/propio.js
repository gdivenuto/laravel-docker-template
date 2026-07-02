/****************************************************************************************
		DEFINICIONES Y FUNCIONES GENERALES JAVASCRIPT DEL SISTEMA
****************************************************************************************/
const a_acentuada = '\u00e1'; // -> á
const e_acentuada = '\u00e9'; // -> é
const i_acentuada = '\u00ed'; // -> í
const o_acentuada = '\u00f3'; // -> ó
const u_acentuada = '\u00fa'; // -> ú

const A_acentuada_mayuscula = '\u00c1'; // -> Á
const E_acentuada_mayuscula = '\u00c9'; // -> É
const I_acentuada_mayuscula = '\u00cd'; // -> Í
const O_acentuada_mayuscula = '\u00d3'; // -> Ó
const U_acentuada_mayuscula = '\u00da'; // -> Ú

const enie = '\u00f1'; 		   // -> ñ
const enie_mayuscula = '\u00d1'; // -> Ñ

const u_dieresis = '\u00FC'; 			 // -> ü
const U_dieresis_mayuscula = '\u00DC'; // -> Ü

const cedilla = '\u00E7'; 		  // -> ç
const cedilla_mayuscula = '\u00C7'; // -> Ç

const interrogacion_inicio = '\u00BF'; // -> ¿
const exclamacion_inicio = '\u00A1';   // -> ¡

const patron_espacio_blanco_global = / /g;
/****************************************************************************************/
function siExiste(id_elemento) {
	// length = 0 se interpreta como valor lógico false y 
	// length > 0 se interpreta como true
	return ( $(id_elemento).length );
}

function refrescar(enlace, capa_destino) {
 	jQuery(capa_destino).load(enlace);
}

function refrescarEnModal(enlace) {
	$('#mensaje_en_modal').load(enlace);
	$('#muestra_modal').click();
}

function refrescarContenidoEnModal(enlace) {
	$('#contenido_en_modal').load(enlace);
	$('#muestra_modal_contenido').click();
}

/**
 * Se redirecciona a una URL determinada
 * @param  string url [description]
 */
function redireccionar(url) {
	window.location = url;
}

function mostrarModal() {
	if ( $('#mensaje').val() != '' ) {
		if ($('#tipo_mensaje').val() == '2') {
			$('#mensaje_en_modal').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i>&nbsp;'+$('#mensaje').val()+'</div>');
		}
		else if ($('#tipo_mensaje').val() == '3') {
			$('#mensaje_en_modal').html('<div class="alert alert-warning"><i class="fas fa-exclamation-circle"></i>&nbsp;'+$('#mensaje').val()+'</div>');
		}
		else {
			$('#mensaje_en_modal').html('<div class="alert alert-success"><i class="fas fa-check-circle"></i>&nbsp;'+$('#mensaje').val()+'</div>');
		}
		// Se limpia
		$('#mensaje').val('');
		// Se muestra la modal
		$('#muestra_modal').click();
	}
}

function mostrarCartel(mensaje, tipo_mensaje) {
	// Si es un mensaje de error
	if (tipo_mensaje == 2)
		$('#mensaje_en_modal').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i>&nbsp;ATENCI&Oacute;N<br>'+mensaje+'</div>');
	else if (tipo_mensaje == 3)
		$('#mensaje_en_modal').html('<div class="alert alert-warning"><i class="fas fa-exclamation-circle"></i>&nbsp;ATENCI&Oacute;N<br>'+mensaje+'</div>');
	else // sino de éxito
		$('#mensaje_en_modal').html('<div class="alert alert-success"><i class="fas fa-check-circle"></i>&nbsp;OK<br>'+mensaje+'</div>');
	
	// Se muestra la modal
	$('#muestra_modal').click();
}

/**
 * Se formatea una fecha determinada para mostrar en la Vista
 * @param  string fecha formato 2017-07-10
 * @return string fecha formato 10/07/2017
 */
function formatearFechaVista(fecha) {
	if (fecha.length) {
		var aux = fecha.split('-');

		return aux[2]+'/'+aux[1]+'/'+aux[0];
	}
}

function esEmailValido(email)
{
	var s = email;
	var patron_para_mail = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
	
	if (s.length == 0 )
		return false;

	return ( patron_para_mail.test(s) );
}

function soloEnteros(e) {
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    permitidos = "0123456789";
    especiales = [8,9,13,37,39];
    tecla_especial = false;

    for (var i in especiales)
		if ( key == especiales[i] ) {
			tecla_especial = true;
			break;
        } 
   
    if (permitidos.indexOf(tecla)==-1 && !tecla_especial)
        return false;
}

function soloDecimales(e) {
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    permitidos = "0123456789.";
    especiales = [8,9,13,37,39,46]; // 46=punto
    tecla_especial = false;

    for (var i in especiales)
		if ( key == especiales[i] ) {
			tecla_especial = true;
			break;
        } 
   
    if (permitidos.indexOf(tecla)==-1 && !tecla_especial)
        return false;
}

function soloEnterosyPunto(e) {
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    permitidos = "0123456789.";
    especiales = [8,9,13,37,39,46]; // 46=punto
    tecla_especial = false;

    for (var i in especiales)
		if ( key == especiales[i] ) {
			tecla_especial = true;
			break;
        } 
   
    if (permitidos.indexOf(tecla)==-1 && !tecla_especial)
        return false;
}

function soloParaTelefono(e) {
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    permitidos = "0123456789-()";
    especiales = [8,9,13,37,39,46];
    tecla_especial = false;

    for(var i in especiales)
		if ( key == especiales[i] ) {
			tecla_especial = true;
			break;
        } 
   
    if(permitidos.indexOf(tecla)==-1 && !tecla_especial)
        return false;
}

function soloParaCUIT(e) {
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    permitidos = "0123456789-.";
    especiales = [8,9,13,37,39,46];

    tecla_especial = false;
    for(var i in especiales)
		if ( key == especiales[i] ) {
			tecla_especial = true;
			break;
        } 
   
    if(permitidos.indexOf(tecla)==-1 && !tecla_especial)
        return false;
}

/**
 * Se marcan todos los checkbox del formulario respectivo
 * @param  {string} formulario Nombre del formulario
 */
function marcarTodosCheckbox(formulario) {
    for (i=0; i< document.forms[formulario].elements.length; i++)
        if ( document.forms[formulario].elements[i].type == "checkbox" )
			document.forms[formulario].elements[i].checked = 1;
}

/**
 * Se desmarcan todos los checkbox del formulario respectivo
 * @param  {string} formulario Nombre del formulario
 */
function desmarcarTodosCheckbox(formulario) {
    for (i=0; i< document.forms[formulario].elements.length; i++)
        if ( document.forms[formulario].elements[i].type == "checkbox" )
			document.forms[formulario].elements[i].checked = 0;
}

/**
 * Se verifica si está tildado por lo menos un checkbox de un formulario determinado
 * @param  {string} checkbox_class  Nombre de la clase de los elementos de tipo checkbox
 * @return {boolean}           		true|false
 */
function verificarCheckbox(checkbox_class) {
	return ($(checkbox_class+':checked').length > 0);
}

function estanTodosVacios(class_css) {
	let todosVacios = true;
	$(class_css).each(function () {
	  	if ($(this).val() !== '') {
			todosVacios = false;
			return false;
	  	}
	});
	return todosVacios;
}
/****************************************************************************************
		DEFINICIONES Y FUNCIONES GENERALES JAVASCRIPT DEL SISTEMA
****************************************************************************************/
var a_acentuada = '\u00e1'; // -> á
var e_acentuada = '\u00e9'; // -> é
var i_acentuada = '\u00ed'; // -> í
var o_acentuada = '\u00f3'; // -> ó
var u_acentuada = '\u00fa'; // -> ú

var A_acentuada_mayuscula = '\u00c1'; // -> Á
var E_acentuada_mayuscula = '\u00c9'; // -> É
var I_acentuada_mayuscula = '\u00cd'; // -> Í
var O_acentuada_mayuscula = '\u00d3'; // -> Ó
var U_acentuada_mayuscula = '\u00da'; // -> Ú

var enie = '\u00f1'; 		   // -> ñ
var enie_mayuscula = '\u00d1'; // -> Ñ

var u_dieresis = '\u00FC'; 			 // -> ü
var U_dieresis_mayuscula = '\u00DC'; // -> Ü

var cedilla = '\u00E7'; 		  // -> ç
var cedilla_mayuscula = '\u00C7'; // -> Ç

var interrogacion_inicio = '\u00BF'; // -> ¿
var exclamacion_inicio = '\u00A1';   // -> ¡

var patron_espacio_blanco_global = / /g;

// FORMATO HORA HH:MM
var patron_hora = new Array(2,2);
/****************************************************************************************/
function siExiste(id_elemento) {
	// length = 0 se interpreta como valor lógico false y 
	// length > 0 se interpreta como true
	return ( $(id_elemento).length );
}

function siEstaDefinido(id_elemento) {
	return (typeof $(id_elemento) !== 'undefined');
}

function refrescar(enlace, capa_destino) {
 	jQuery(capa_destino).load(enlace);
}

function refrescarEnModal(enlace) {
	$('#mensaje_en_modal').load(enlace);
	$('#muestra_modal').click();
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
		if ($('#tipo_mensaje').val() == '2')
			$('#mensaje_en_modal').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i>&nbsp;'+$('#mensaje').val()+'</div>');
		else if ($('#tipo_mensaje').val() == '3')
			$('#mensaje_en_modal').html('<div class="alert alert-warning"><i class="fas fa-exclamation-circle"></i>&nbsp;'+$('#mensaje').val()+'</div>');
		else
			$('#mensaje_en_modal').html('<div class="alert alert-success"><i class="fas fa-check-circle"></i>&nbsp;'+$('#mensaje').val()+'</div>');
		
		// Se muestra la modal
		$('#muestra_modal').click();
	}
}

function mostrarDatoEnModal(dato) {
	$('#mensaje_en_modal').html('<div class="alert alert-info"><i class="far fa-file-alt"></i>&nbsp;'+dato+'</div>');
	$('#muestra_modal').click();
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

function mostrarModalConfirmacion(mensaje) {
	if ( mensaje != '' ) {
		$('#mensaje_en_modal_confirmacion').html(mensaje);
		// Se muestra la modal
		$('#muestra_modal_confirmacion').click();
	}
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

function formatearHora(elemento)
{
	// SI SE INGRESÓ UN VALOR
	if ( elemento.val() != '' ) {
		// CANTIDAD DE CARACTERES INGRESADOS
		var longitud = elemento.val().length;
	
		// SI SE INGRESARON DOS CARACTERES PARA LA HORA, SE COMPLETA
		if( longitud == 2 ) {
			elemento.val(elemento.val()+":00");
		}
		
		// SI SE INGRESÓ SOLO 1 CARÁCTER PARA LA HORA, SE COMPLETA CON CERO
		if( longitud == 1 ) {
			elemento.val("0"+elemento.val()+":00");
		}
	} else {
		elemento.val("");
	}
}

function formatearMoneda(elemento)
{
	let valor_moneda = elemento.value;
	// ej: 4.200,50
	
	let sin_coma = valor_moneda.replace(',', '.');
	// ej: 4.200.50
	//alert(sin_coma);
	
	let partes = sin_coma.split('.');
	// ej: 4 200 50
	
	let parte_decimal = partes[partes.length-1];
	// ej: 50
	//alert(parte_decimal);
	
	let parte_entera = '';
	let i;
	//alert(partes.length-1);
	for(i=0; i < partes.length-1; i++) {
		parte_entera += partes[i];
	}
	
	//alert(parte_decimal);
	
	let union = '';
	
	if (parte_entera) {
		union = parte_entera+'.'+parte_decimal;
	} else {
		union = parte_decimal;
	}
	// ej: 4200.50
	//alert(union);
	
	elemento.value = union;
}

function esEmailValido(email)
{
	let s = email;
	let patron_para_mail = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
	
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

    for (let i in especiales)
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

    for (let i in especiales)
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

    for (let i in especiales)
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

    for(let i in especiales)
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
    for(let i in especiales)
		if ( key == especiales[i] ) {
			tecla_especial = true;
			break;
        } 
   
    if(permitidos.indexOf(tecla)==-1 && !tecla_especial)
        return false;
}

// MASCARA DE ENTRADA PARA:
// FECHA (DD/MM/YYYY)
// HORA (HH:MM)
// d: valor; sep: caracter separador; pat: patron; nums
function mascara(d,sep,pat,nums)
{
    if( d.valor_anterior != d.value ) {
		val = d.value
		largo = val.length
		val = val.split(sep)
		val2 = ''
		
		for( r=0;r<val.length;r++ ) {
			val2 += val[r]	
		}
		
		if(nums) {
			for( z=0;z<val2.length;z++ ) {
				if( isNaN(val2.charAt(z)) ) {
					letra = new RegExp(val2.charAt(z),"g")
					val2 = val2.replace(letra,"")
				}
			}
		}
		
		val = ''
		val3 = new Array()
		for( s=0; s<pat.length; s++ ) {
			val3[s] = val2.substring(0,pat[s])
			val2 = val2.substr(pat[s])
		}
		
		for( q=0;q<val3.length; q++ ) {
			if(q ==0){
				val = val3[q]
			} else {
				if(val3[q] != "") {
					val += sep + val3[q]
				}
			}
		}
		d.value = val
		d.valor_anterior = val
    }
}

function esLaFechaMayor(fecha, fecha2)
{
  let xMonth=fecha.substring(3, 5);
  let xDay=fecha.substring(0, 2);
  let xYear=fecha.substring(6,10);
  let yMonth=fecha2.substring(3, 5);
  let yDay=fecha2.substring(0, 2);
  let yYear=fecha2.substring(6,10);

  if (xYear > yYear) {
      return(true)
  } else {
    if (xYear == yYear) { 
      if (xMonth > yMonth) {
	  	return(true)
      } else { 
		if (xMonth == yMonth) {
		  if (xDay > yDay)
		    return(true);
		  else
		    return(false);
		}
		else
		  return(false);
      }
    }
    else
      return(false);
  }
}

// Verifica si el Nombre Netbios posee caracteres válidos
function esNombreNetbiosValido_Anterior(cadena) {
	let patron_valido = /^([a-zA-Z0-9]{1,1}[a-zA-Z0-9\-]{0,14})$/;
	
	let patron_letras = /[a-zA-Z]+/;
	
	// Devuelve true si la cadena POSEE caracteres VALIDOS y por lo menos una letra
	return patron_valido.test(cadena) && patron_letras.test(cadena);
}

//Verifica si el Nombre Netbios posee caracteres válidos
function esNombreNetbiosValido(cadena) {
   // La cadena válida debe cumplir con el siguiente patrón:
   //     - de 1 a 15 caracteres
   //     - los caracteres válidos son a-z A-Z 0-9 y guión medio "-"
   //     - puede empezar con una letra o un número
   //     - debe contener AL MENOS una letra, en cualquier posición
   let patron_valido = /^(?=.{1,15}$)([a-zA-Z]{1,1}[a-zA-Z0-9\-]*|[0-9]{1,1}[0-9\-]*[a-zA-Z]+[a-zA-Z0-9\-]*)$/;
   
   return patron_valido.test(cadena);
}

/**
 * Se verifica si la sección de la dirección MAC posee caracteres válidos
 * 
 * @param valor_ingresado, a validar
 * @returns {Boolean}
 */
function esRangoMAC_Valido(valor_ingresado) {
	let patron_valido = /^[0-9abcdef]*$/i;
	
	// Si el valor ingresado NO posee un carácter VALIDO
	if( !patron_valido.test(valor_ingresado) ) {
		return false;
	}
	return true;
}

/**
 * Se valida que el valor ingresado sea sólo numérico, 
 * el punto sólo se utiliza para tabular entre los rangos, luego se quita
 * 
 * @param valor_ingresado, a validar
 * @returns {Boolean}
 */ 
function esRangoIP_Valido(valor_ingresado) {
	// Para validar una IP de cuatro rangos a la vez:
	// "^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$";
	
	let patron_valido = /^([0-9]\.)$/;
	//let patron_valido = /^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.)$/;
	
	if( !patron_valido.test(valor_ingresado) ) {
		return false;
	}
	return true;
}

/**
 * Verifica si el valor se encuentra en el rango [0..255]
 */
function validarRangoIP(valor_ip) {
	// Si el valor ingresado está fuera del rango [0..255]
	if( valor_ip < 0 || valor_ip > 255 ) {
		return false;
	}
	return true;
}

/**
 * Se valida el valor ingresado para el Rango de Dirección IP
 * 
 * @param e, evento del usuario al presionar la tecla
 * @param string nombre_elemento
 * @param integer nro_elemento
 * @returns {Boolean}
 */
function validarDireccionIP(e, nombre_elemento, nro_elemento) {

	let key = e.keyCode || e.which;

	let tecla = String.fromCharCode(key).toLowerCase();

	let caracteres_permitidos = "0123456789";
	//let patron_valido = /^[0-9]*$/i;

	let especiales = [8,9,13,46];

	let valor_ingresado = $(nombre_elemento+nro_elemento).val();

	let tecla_especial = false;
	for(let i in especiales) {
		if ( key == especiales[i] ) {
			tecla_especial = true;
			break;
	  	} 
	}
	
	//if( !patron_valido.test(valor_ingresado) && !tecla_especial )
  	if( caracteres_permitidos.indexOf(tecla) == -1 && !tecla_especial ) {
		return false;
	} else {
		// SI SE PULSÓ LA TECLA DEL .(punto)
		if( key == 46 ) {
			// SI EL VALOR ESTÁ EN EL RANGO [0..255]
			if( valor_ingresado >= 0 && valor_ingresado <= 255 ) {
				// SE INCREMENTA EL NUMERO
				nro_elemento++;
				// SE DA EL FOCO AL SIGUIENTE ELEMENTO DE LA DIRECCION IP
				$(nombre_elemento+nro_elemento).focus();
			} else {
				mostrarCartel("El valor ingresado debe estar dentro del rango [0..255]", 3);
				
				eliminarPuntoCadena(nombre_elemento+nro_elemento);
				
				$(nombre_elemento+nro_elemento).val('');
			}
		}
	}
}

/**
 * Elimina el punto luego de haber completado el valor de tres dígitos (o menos) en el campo de texto respectivo
 * 
 * @param id_elemento, identificador del campo de texto
 */
function eliminarPuntoCadena(id_elemento)
{
	// Si posee un valor ingresado y tiene 3 dígitos o menos
	if ( $(id_elemento).val() != '' && $(id_elemento).val().length <= 3 ) {
		// Se quita el punto ingresado
		valor_sin_punto = $(id_elemento).val().split(".").join("");
		// Se asigna el valor sin el punto
		$(id_elemento).val(valor_sin_punto);
	}
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
 * @param  string 	checkbox_class  Nombre de la clase de los elementos de tipo checkbox
 * @return boolean
 */
function verificarCheckbox(checkbox_class) {
	return ($(checkbox_class+':checked').length > 0);
}
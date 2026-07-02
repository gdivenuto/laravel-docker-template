/**
 * Definición de vocales acentuadas, letra ñ, Ñ, diéresis, y signos  de inicio de interrogación y exclamación.
 */
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

var interrogacion_inicio = '\u00BF'; // -> ¿
var exclamacion_inicio = '\u00A1';   // -> ¡

var perfil_usuario_actual;
var verificacion_ppc_hecha;

/**
 * Modificacion al prototipo de format para la clase String, la cual me permite utilizar
 * la funcion format de la clase, si es que esta no existe.
 */
if (!String.prototype.format) {
  String.prototype.format = function() {
    var args = arguments;
    return this.replace(/{(\d+)}/g, function(match, number) {
      return typeof args[number] != 'undefined'
        ? args[number]
        : match
      ;
    });
  };
}

/**
 * Permite eliminar un item de un array por su nombre
 * Ejemplo:
 * var frutas = ['manzana', 'banana', 'pera'];
 * frutas.removerItem('banana');
 * console.log(frutas); // Entrega [‘manzana’, ‘pera’]
 *
 * @param  {[type]} nombre_elemento [description]
 * @return {[type]}   [description]
 */
Array.prototype.removerItem = function (nombre_elemento) {
	for (var i = 0; i < this.length; i++) {
		if (this[i] == nombre_elemento) {
			for (var j = i; j < this.length - 1; j++) {
				this[j] = this[j + 1];
			}
			this.length = this.length - 1;
			return;
		}
	}
};

/**
 * [obtenerHorario] Devuelve el horario hh:mm:ss, en caso que lo posea la fecha
 * @param  {[type]} fecha [description]
 * @return {[type]}       horario en formato hh:mm:ss
 */
function obtenerHorario(fecha)
{
	var partes_fecha = fecha.split(" ");
	var horario = partes_fecha[1];

	return (horario != undefined) ? " "+horario : "";
}

/**
 * [formatearFechaConBarras devuelve una fecha en formato dd/mm/yyyy
 * @param  {[type]} fecha   valor de la fecha a formatear
 * @return {[type]}         fecha en formato dd/mm/yyyy
 */
function formatearFechaConBarras(fecha)
{
	// Si la fecha es nula
	if( fecha == null )
		return '';
	else {
		// Si la fecha NO es válida
		if ( !moment(fecha).isValid() )
			return '';

		// Se formatea la fecha a dd/mm/yyyy
		var fecha_con_barras = moment(fecha).format('L');

		// Se obtiene el horario hh:mm:ss, en caso que lo posea la fecha
		var horario = obtenerHorario(fecha);

		// Devuelve la fecha en formato dd/mm/yyyy HH:mm:ss (con el horario en caso que lo posea)
		return fecha_con_barras+horario;
	}
}

/**
 * [formatearFechaParaDB devuelve una fecha en formato yyyy-mm-dd
 * @param  string fecha   Valor de la fecha a formatear
 * @return string         Fecha en formato yyyy-mm-dd
 */
function formatearFechaParaDB(fecha)
{
	// Si la fecha es nula
	if( fecha == null )
		return '';
	else {
		// Si la fecha NO es válida
		if ( !moment(fecha).isValid() )
			return '';

		// Devuelve la fecha en formato yyyy-mm-dd
		return moment(fecha).format('YYYY-MM-DD');
	}
}

/**
 * Devuelve una fecha en formato yyyy-mm-dd
 * @param  {[type]} fecha [description]
 * @return {[type]}       [description]
 */
function formatearFechaConGuion(fecha)
{
	// Si la fecha es nula
	if( fecha == null || fecha == '' )
		return '';
	else {
		// Se separa el año, mes y día de la fecha
	    var partes_fecha = fecha.split("/");

	    // Se toma el año, mes y día de la fecha
	    var anio = partes_fecha[2];
	    var mes = partes_fecha[1];
	    var dia = partes_fecha[0];

	    // Devuelve la fecha en formato aaaa-mm-dd
	    return anio+"-"+mes+"-"+dia;
	}
}

/**
 * Invoca una ventana modal customizada dinámicamente.
 * @param  string titulo Titulo de la ventana modal
 * @param  string texto Texto de la ventana modal
 * @param  array botones Listado de Id de los botones a utilizar en la modal
 */
function showModal(titulo, texto, botonesDef) {
	// Se ocultan los botones (clase .btn) del contenedor de la modal
	$("#v_modal_dialog .btn").hide();
	// Se resetean los botones de la Modal: se retiran todas las clases CSS de los botones del contenedor
	// de la modal y seguidamente se le asignan las clases CSS para definirlos nuevamente
	$("#v_modal_dialog .btn").removeClass().addClass("btn btn-default");
	// Se eliminan los eventos 'click' de los botones, para eliminar su propagación
	$("#v_modal_dialog .btn").off('click');

	// Si el objeto 'botonesDef' existe, el cual es un listado de botones que deberá mostrar la modal
	if ((typeof botonesDef != 'undefined') && (botonesDef != null) ) {
		// Para cada botón
		$.each( botonesDef, function( btName, btData ) {
			// Si está definido'
			if ( $('#v_modal_dialog_'+btName).length > 0 ){
				// Se muestra dicho botón
				$('#v_modal_dialog_'+btName).show();

				// Para cada propiedad que posea
				$.each( btData, function( propiedad, valor ) {
					// Si la propiedad es su 'clase' CSS que lo define, y su valor NO es de tipo 'Normal'
					// valor puede ser: btn-primary, btn-info, btn-success, btn-warning, btn-danger o btn-link
					if ( propiedad == 'class' && valor != 'btn-default' )
						// Se reemplaza el tipo de botón utilizando la clase CSS de Bootstrap respectiva
						$('#v_modal_dialog_'+btName).removeClass('btn-default').addClass(valor);
					// Si la propiedad es la 'acción' que ejecutará dicho botón y posee funcionalidad
					if ( propiedad == 'action' && valor != '' ) {
						// El action tiene dos posibilidades: o es una funcion, o es un objeto con la dupla 'eventData' y 'fn'
						if (typeof valor === 'function')
							// Se define el evento click para dicho botón, con la funcionalidad determinada por su valor
							$('#v_modal_dialog_'+btName).click(valor);
						else
							// Si no es una funcion, entonces es un objeto con la dupla 'eventData' y 'fn'.
							// Si el boton dispone de 'eventData', esos valores se pasan como referencia al evento.
							// Posteriormente se recuperan como 'event.data.xxx'
							$('#v_modal_dialog_'+btName).click(valor.eventData, valor.fn);
					}
				});
			}
		  	else
		  		alert('No está definido el botón '+btName);// Se informa la ausencia del botón en el template
		});
	} else {
		$('#v_modal_dialog_btn_cerrar').removeClass('btn-default').addClass('btn-primary');
		$('#v_modal_dialog_btn_cerrar').show();// Se muestra por defecto el botón Cerrar
	}

	$('#v_modal_dialog_titulo').html(titulo);// Se setea el Título de la modal
	$('#v_modal_dialog_texto').html(texto);// Se setea el Texto de la modal
	$('#v_modal_dialog').modal();// Se muestra la modal
	$('#v_modal_dialog').removeClass('fade'); // quito el efecto fade para que la transicion no moleste a los ajax asincronicos
}

/**
 * Esta función generaliza la generación del comportamiento de un boton del cuadro de dialogo modal
 * el cual controla los cierres de sesion desde javascript. Se utiliza principalmente para ahorrar
 * lineas de código y centralizar la lógica de control de sesiones.
 * @param  {[type]} parametro [description]
 * @return {[type]}           [description]
 */
function modalBtnSessionHandler(parametro) {
	return {
		class: 'btn-primary',
		action: {
			eventData: { jsonData: parametro },
			fn: function (ev) {
				if (ev.data.jsonData != null) {
					// Verifico el codigo de error (para resultados de json de DataTables)
					if (typeof ev.data.jsonData.numeroError != 'undefined') {
						if (ev.data.jsonData.numeroError == 301)  { // si es un problema de cierre de sesion redirecciono al home.
							ev.preventDefault();
							$(location).attr('href','index.php?c=home&a=view');
						}
					}
					// Verifico el codigo de error (para resultados de json de framework SGL)
					else if (typeof ev.data.jsonData.estado != 'undefined') {
						if (((ev.data.jsonData.estado == 'ERROR') || (ev.data.jsonData.estado == 'WARNING')) && (ev.data.jsonData.data == 301)) {
							ev.preventDefault();
							$(location).attr('href','index.php?c=home&a=view');
						}
					}
				}
				// si no es ninguno de los casos anteriores, simplemente se hace el 'dismiss' del formulario
			}
		}
	};
}

/**
 * Permite ir al controlador respectivo, mostrando el listado de la entidad respectiva, en base al contorlador especificado
 * Utilizado en listenerBotonCancelar
 *
 * @param  string controlador Nombre del controlador respectivo
 * @param  integer anio    	  Año del expediente
 * @param  string tipo        Tipo del expediente
 * @param  integer numero     Número del expediente
 * @param  integer cuerpo     Cuerpo del expediente
 * @param  integer alcance    Alcance del expediente
 */
function irA(controlador, anio, tipo, numero, cuerpo, alcance){

	$(location).attr('href', 'index.php?c={0}&a=view&f_anio={1}&f_tipo={2}&f_numero={3}&f_cuerpo={4}&f_alcance={5}'.format(controlador, anio, tipo, numero, cuerpo, alcance));
}

/**
 * Asigna el comportamiento del click de un determinado boton a un conjunto de inputs, cuando se presiona
 * la tecla ENTER sobre alguno de ellos. Se utiliza para confirmar los parametros de búsqueda o selección al
 * presionar ENTER en un campo determinado.
 * @param  array arrayInputId  Array con los ID de los input a afectar, p.e.: ['#inputNumero', '#inputAnio']
 * @param  string buttonId     ID del botón del cual se desea ejecutar 'click' al pulsar enter.
 */
function defaultButtonInputOnEnter(arrayInputId, buttonId) {
    $.each(arrayInputId, function (index, value) {
        $(value).keypress(function(e) {
            if(e.which == 13)
                $(buttonId).click();
        })
    });
}

/**
 * Se separa el Tipo y el Código de un valor recibido desde un combobox, utilizado para Iniciadores y Autores
 * @param  string valor Valor a separar
 * @return array  clave Array que contiene los valores separados
 */
function separarTipoCodigo(valor) {
	var separador = '|';
	var partes = valor.split(separador);
	var clave = [partes[0], partes[1]];

	return clave;
}

/**
 * Elimina un elemento de una coleccion específica (debe poseer los atributos _items y _deletedItems).
 * @param  integer idElemento   Id (posición) de un elemento a eliminar.
 * @param  mixed   coleccion    Colección donde se quitará el elemento.
 * @return Elemento eliminado.
 */
function eliminarElemento(idElemento, coleccion)
{
    // Primero se verifica la existencia de los atributos _items y _deletedItems de la coleccion.
    if (!(coleccion.hasOwnProperty('_items') && coleccion.hasOwnProperty('_deletedItems'))) {
        alert('Error en eliminarElemento(): colección inválida (no posee los atributos _items y _deletedItems.)');
        return null;
    }
    eliminados = coleccion._items.splice(idElemento, 1);    // Quitamos el elemento de _items
    coleccion._deletedItems.push(eliminados[0]);            // Lo pasamos a _deletedItems
    eliminados[0].instanceState = 3;                        // 3 = IS_DELETED (lo marcamos como eliminado)
    return eliminados[0]; // retorno el elemento eliminado
}

/**
 * Elimina un elemento de una coleccion específica por referencia (debe poseer los atributos _items y _deletedItems).
 * @param  integer idElemento   Id (posición) de un elemento a eliminar.
 * @param  mixed   coleccion    Colección donde se quitará el elemento.
 * @return Elemento eliminado.
 */
function eliminarElementoPorReferencia(referencia, coleccion)
{
    // Primero se verifica la existencia de los atributos _items y _deletedItems de la coleccion.
    if (!(coleccion.hasOwnProperty('_items') && coleccion.hasOwnProperty('_deletedItems'))) {
        alert('Error en eliminarElementoPorReferencia(): colección inválida (no posee los atributos _items y _deletedItems.)');
        return null;
    }

    // Obtengo la referencia
    idElemento = coleccion._items.indexOf(referencia);

    if (idElemento !== -1 )
        return eliminarElemento(idElemento, coleccion);
    else
        return null;
}

/**
 * Se agrega un elemento a una colección determinada.
 * @param  Object elemento     Instancia de la clase a utilizar para agregar a la coleccion.
 * @param  string className    Nombre de la clase instanciada.
 * @param  mixed  coleccion    Colección donde se agrega el 'elemento'.
 * @return Elemento agregado.
 */
function agregarElemento(elemento, className, coleccion)
{
    // Primero se verifica la existencia de los atributos _items y _deletedItems de la coleccion.
    if (!(coleccion.hasOwnProperty('_items') && coleccion.hasOwnProperty('_deletedItems'))) {
        alert('Error en agregarElemento(): colección inválida (no posee los atributos _items y _deletedItems.)');
        return null;
    }
    elemento.className = className;  // Indicamos cual es la clase (para el serializador)
    elemento.instanceState = 2;      // IS_ADDED (lo marcamos como agregado)
    coleccion._items.push(elemento); // Agregamos el elemento respectivo
    return elemento;
}

/**
 * Decodifica una entidad usando un "trick and hack" para que se pueda enviar un string
 * codificado con html entities a un input en un formulario.
 * @param  {string} encodedString String codificado con html entities.
 * @return {string}               String resultante *sin* html entities.
 */
function decodeEntities(encodedString) {
	// Ver http://stackoverflow.com/questions/1147359/how-to-decode-html-entities-using-jquery/1395954#1395954
    var textArea = document.createElement('textarea');
    textArea.innerHTML = encodedString;
    return textArea.value;
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


function cantidadCheckboxElegidos(formulario) {
    let cantidad = 0;

    for (i=0; i< document.forms[formulario].elements.length; i++)
        if ( document.forms[formulario].elements[i].type == "checkbox" )
        	if ( document.forms[formulario].elements[i]+':checked' )
				cantidad++;

	return cantidad;
}

/**
 * Se selecciona y se le da el foco a un elemento determinado
 * @param  {string} elemento Id del elemento
 */
function setfocus(elemento) {
    setTimeout(function() {
		$(elemento).select();
		$(elemento).focus();
	}, 10);
}

function replaceAll(cadena, buscado, reemplazado_por) {
  	while (cadena.toString().indexOf(buscado) != -1)
      	cadena = cadena.toString().replace(buscado,reemplazado_por);
  	return cadena;
}

function cargarGiros() {
	if ( $('#f_anio').length > 0 && $('#f_anio').val() != '' ) {
		// 01/11/22 XXXX
		// Anteriormente se verificaba si el expediente ya poseía giros
		// si no poseía se redireccionaba a la carga de hasta seis comisiones a la vez
		// si poseía se redireccionaba al formulario para la carga de un giro individual
		// ------------------------------------------------------------------------------
		// $(location).attr('href','index.php?c=cargagiros&a=verificarexistencia&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}'.format(
		// 	$('#f_anio').val(),
		// 	$('#f_tipo').val(),
		// 	$('#f_numero').val(),
		// 	$('#f_cuerpo').val(),
		// 	$('#f_alcance').val()
		// ));
		// Ahora directamente se redireccionaa la actuación de carga de giros
		iniciarActuacion('expediente_cargar_giros', {
			'anio' : $('#f_anio').val(),
			'tipo' : $('#f_tipo').val(),
			'numero' : $('#f_numero').val(),
			'cuerpo' : $('#f_cuerpo').val(),
			'alcance' : $('#f_alcance').val()
		});
	} else
		$(location).attr('href','index.php?c=expedientes&a=view');
}

/**
 *  Se comparan dos fechas (Desde y Hasta), en formato dd/mm/yyyy
 *  Devuelve True si la fechaHasta es mayor a la fechaDesde
 */
function compararFechas(fechaDesde, fechaHasta) {
    var mayor = false;
    // Se separa el año, mes y día de las fechas
    var fecha_desde = fechaDesde.split("/");
    var fecha_hasta = fechaHasta.split("/");

    // Se toma el año, mes y día de la fecha 'Desde'
    anio_desde = fecha_desde[2];
    mes_desde  = fecha_desde[1];
    dia_desde  = fecha_desde[0];

    // Se toma el año, mes y día de la fecha 'Hasta'
    anio_hasta = fecha_hasta[2];
    mes_hasta  = fecha_hasta[1];
    dia_hasta  = fecha_hasta[0];

    // Si el año es el mismo, se verifica el mes
    if (anio_hasta == anio_desde) {
    	//Si el mes es el mismo, se verifica el día
        if (mes_hasta == mes_desde) {
            if (dia_hasta > dia_desde)
                mayor = true; // Se cumple que la fecha 'Hasta' es mayor
            else
                mayor = false;
        } else {
        	// Si el mes es mayor ya basta, el día puede ser menor o igual
            if (mes_hasta > mes_desde)
                mayor = true;
        }
    } else {
    	// Si el año es mayor ya basta, el mes puede ser menor o igual
        if (anio_hasta > anio_desde)
            mayor = true;
        else
        	// Si no se cumple ninguna condición la fecha 'Hasta' es menor a la fecha 'Desde'
            mayor = false;
    }
    return mayor;
}

/**
 * Para completar con tantos ceros a la izquierda de la cadena como sea necesario, en base al ancho deseado
 * Se toma el valor absoluto del número antes de completarlo
 * En caso que sea negativo, se lo precede con el signo "-"
 * @param  {[string]} numero 		 Número a complear
 * @param  {[string]} ancho_deseado  Cantidad de dígitos para determinar con cuantos ceros completar
 * @return {[string]} 				 Número completado con ceros a la izquierda
 */
function completarConCeroIzquierda(numero, ancho_deseado) {
	// Valor absoluto del número
	var numeroCompletado = Math.abs(numero);
	// Longitud del número
	var longitud         = numero.toString().length;
	// String de cero
	var zero             = "0";

    if (ancho_deseado <= longitud) {
        if (numero < 0)
             return ("-" + numeroCompletado.toString());
        else
             return numeroCompletado.toString();
    } else {
        if (numero < 0)
            return ("-" + (zero.repeat(ancho_deseado - longitud)) + numeroCompletado.toString());
        else
            return ((zero.repeat(ancho_deseado - longitud)) + numeroCompletado.toString());
    }
}

/**
 * Devuelve el año en formato corto, es decir, los últimos 2 dígitos
 * @param  {[type]} anio Año de 4 dígitos
 * @return {[type]}      Ultimos 2 dígitos del Año
 */
function obtenerAnioCorto(anio) {
	 return anio.toString().slice(-2);
}

/**
 * Devuelve el nombre codificado de un expediente en base a su clave: año, tipo y número
 * en formato AAENNNNN, donde:
 * AA     = últimos 2 dígitos del Año
 * E      = letra del Tipo de expediente (E|N|R)
 * NNNNN  = Número con ceros a la izquierda, completado para llegar a 5 dígitos
 * @param  {[type]} anio   Año del expediente
 * @param  {[type]} tipo   Tipo del expediente
 * @param  {[type]} numero Número del expediente
 * @return {[type]}        Nombre codificado en formato AAENNNNN
 */
function obtenerNombreCodificado(anio, tipo, numero) {
	var anio_digi   = obtenerAnioCorto(anio);
	var numero_digi = completarConCeroIzquierda(numero, 5);

	return anio_digi+tipo+numero_digi;
}

function seleccionarDocumento() {
	$(location).attr('href','index.php?c=cargaproyectos&a=seleccionardocumento&f_anio={0}&f_tipo={1}&f_numero={2}&f_cuerpo={3}&f_alcance={4}'.format(
		$('#f_anio').val(),
		$('#f_tipo').val(),
		$('#f_numero').val(),
		$('#f_cuerpo').val(),
		$('#f_alcance').val()
	));
}

/*  09/09/2020 XXXX
	Se reubicaron aquí para utilizarse en el resto de las solapas
	---------------------------------------------------------------*/
/**
 * Se busca el Expediente/Nota/Recomendación anterior
 */
var listenerBtnVerExpedienteAnterior = function (event) {
	// Si no se ha ingresado el Número
	if ($('#f_numero').val() === '')
		showModal('Error', 'Debe ingresar el N&uacute;mero.');
	else {
		// Peticion asíncrona
		$.ajax({
			method: "GET",
			url: "index.php",
			dataType: 'json',
			data: {
				c: "expedientes",
				a: "obtenerexpedienteanterior",
				f_anio: $('#f_anio').val(),
				f_tipo: $('#f_tipo').val(),
				f_numero: $('#f_numero').val(),
				f_cuerpo: $('#f_cuerpo').val(),
				f_alcance: $('#f_alcance').val()
			}
		}).done(function( respuesta ) {
			// muestro datos
			if (respuesta.estado == "OK") {
				if (respuesta.data != null && respuesta.data.length > 0) {
					//console.log(respuesta.data);
					// Se refrezca la solapa respectiva con el expediente/nota anterior
					irA(controlador_activo, respuesta.data[0].anio, respuesta.data[0].tipo, respuesta.data[0].numero, respuesta.data[0].cuerpo, respuesta.data[0].alcance);
				} else {
					// Se deshabilita el botón Anterior
					$('#btn_expediente_anterior').prop('disabled', true);
				}
			} else if ((respuesta.estado == "ERROR") || (respuesta.estado == "WARNING")) {
				// Se deshabilita el botón Anterior
				$('#btn_expediente_anterior').prop('disabled', true);;
			}
		}).fail(function () {
			// Se deshabilita el botón Anterior
			$('#btn_expediente_anterior').prop('disabled', true);
		});
	}
};

/**
 * Se busca el Expediente/Nota/Recomendación siguiente
 */
var listenerBtnVerExpedienteSiguiente = function (event) {
	// Si no se ha ingresado el Número
	if ($('#f_numero').val() === '')
		showModal('Error', 'Debe ingresar el N&uacute;mero.');
	else {
		// Peticion asíncrona
		$.ajax({
			method: "GET",
			url: "index.php",
			dataType: 'json',
			data: {
				c: "expedientes",
				a: "obtenersiguienteexpediente",
				f_anio: $('#f_anio').val(),
				f_tipo: $('#f_tipo').val(),
				f_numero: $('#f_numero').val(),
				f_cuerpo: $('#f_cuerpo').val(),
				f_alcance: $('#f_alcance').val()
			}
		}).done(function( respuesta ) {
			// muestro datos
			if (respuesta.estado == "OK") {
				if (respuesta.data != null && respuesta.data.length > 0) {
					//console.log(respuesta.data);
					// Se refrezca la solapa respectiva con el expediente/nota siguiente
					irA(controlador_activo, respuesta.data[0].anio, respuesta.data[0].tipo, respuesta.data[0].numero, respuesta.data[0].cuerpo, respuesta.data[0].alcance);
				} else {
					// Se deshabilita el botón Siguiente
					$('#btn_expediente_siguiente').prop('disabled', true);
				}
			} else if ((respuesta.estado == "ERROR") || (respuesta.estado == "WARNING")) {
				// Se deshabilita el botón Siguiente
				$('#btn_expediente_siguiente').prop('disabled', true);;
			}
		}).fail(function () {
			// Se deshabilita el botón Siguiente
			$('#btn_expediente_siguiente').prop('disabled', true);
		});
	}
};

(function(window){
	window.htmlentities = {
		/**
		 * Converts a string to its html characters completely.
		 *
		 * @param {String} str String with unescaped HTML characters
		 **/
		encode : function(str) {
			var buf = [];

			for (var i=str.length-1;i>=0;i--) {
				buf.unshift(['&#', str[i].charCodeAt(), ';'].join(''));
			}

			return buf.join('');
		},
		/**
		 * Converts an html characterSet into its original character.
		 *
		 * @param {String} str htmlSet entities
		 **/
		decode : function(str) {
			return str.replace(/&#(\d+);/g, function(match, dec) {
				return String.fromCharCode(dec);
			});
		}
	};
})(window);

/**
 * Se suman X Días a una fecha determinada
 * @param  {[type]} fecha En formato yyyy-mm-dd
 * @param  {[type]} dias  Número de días a agregar
 * @return {[type]}       Fecha calculada
 */
function sumarDias(fecha, dias) {

	var fecha = new Date(fecha);

	fecha.setDate(fecha.getDate() + dias);

	return fecha.getDate() + '/' + (fecha.getMonth() + 1) + '/' + fecha.getFullYear();
}

/**
 * Se limita la cantidad de caracteres de un campo determinado
 * @param  {[type]} campo_a_limitar   Campo al cual se desea limitar la cantidad de caracteres
 * @param  {[type]} limite_caracteres Número que representa el límite de caracteres
 * @return {[type]}                   [description]
 */
function limitarCantidadcaracteres(campo_a_limitar, limite_caracteres) {
    if (campo_a_limitar.value.length > limite_caracteres) {
        campo_a_limitar.value = campo_a_limitar.value.substring(0, limite_caracteres);
    }
}

/**
 * Intenta iniciar una actuación; es un wrapper que permite resumir la actuacion
 * en curso (si es que existe una).
 * @param  {[type]} tipo_actuacion [description]
 * @param  {[type]} parametros     [description]
 * @return {[type]}                [description]
 */
function iniciarActuacion(tipo_actuacion, parametros)
{
	// Peticion asíncrona
    $.ajax({
        method: "GET",
        url: `index.php?c=actuaciones&a=actuacionpendiente`,
        dataType: 'json',
        args: {
        	'tipo_actuacion': tipo_actuacion,
        	'parametros': parametros
        }
    })
    .done(function( respuesta ) {
    	if (respuesta.estado == 'OK') {
            if (respuesta.data != null) {
            	showModal('Confirmación', `<p>Existe una actuación pendiente: <strong>${respuesta.data.nombre}</strong>.</p><p>Detalle: <ul><li>${respuesta.data.texto_informativo}</li></ul></p><p>¿Desea continuar con la actuación pendiente?</p><p>Presione <strong>Si</strong> para continuar la actuación pendiente, <strong>No</strong> para descartar la actuación pendiente y comenzar una nueva, o <strong>Cancelar</strong> para salir.</p>`,
	            	{
						btn_si: {
							class: 'btn-primary',
							action: function (e) {
								$(location).attr('href', `index.php?c=actuaciones&a=retomar`);
							}
						},
						btn_no: {
							class: 'btn-default',
							action: function (e) {
					        	url_params = $.map(this.args.parametros, function(v, k) { return `${k}=${v}`;}).join('&');
				                $(location).attr('href',`index.php?c=actuaciones&a=descartar&actuacion=${this.args.tipo_actuacion}&${url_params}`);
							}.bind(this)
						},
						btn_cancelar: { class: 'btn-default' }
					});
	        } else {
	        	// Si no hay actuacion, redirecciono
	        	url_params = $.map(this.args.parametros, function(v, k) { return `${k}=${v}`;}).join('&');
                $(location).attr('href',`index.php?c=actuaciones&a=wizard&actuacion=${this.args.tipo_actuacion}&${url_params}`);
	        }
        } else // Si falló la operación en el controlador
            showModal('Error', `No se puede determinar si hay actuaciones activas. Motivo: ${respuesta.mensaje}`);
    })
    .fail(function( jqXHR, textStatus, errorThrown ) {
    	showModal('Error', 'Se ha producido un error inesperado: {0}'.format(textStatus));
    });
}

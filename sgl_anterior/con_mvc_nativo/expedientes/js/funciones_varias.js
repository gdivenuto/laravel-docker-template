/****************************************************************************************
		VARIABLES GENERALES JAVASCRIPT DEL SISTEMA
****************************************************************************************/
var tiempoInicial;//PARA LA HORA EN EL PIE

// Constantes para definir el titulo del Alert personalizado y texto del boton
var ALERT_TITLE = "SGL :: Sistema de Expedientes.";
var ALERT_BUTTON_TEXT = "Aceptar";

// PARA LOS LISTADOS
var fila_pulsar = 10;

var temas_del_expediente = new Array();
var nuevo_tema;

var autores_del_expediente = new Array();
var nuevo_autor;

// Patrón para la fecha Formato dd/mm/yyyy
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);

// Patrón para la fecha Formato yyyy-mm-dd
var patron_fecha_con_guion = new Array(4,2,2);

// Patrón para la Hora Formato hh:mm:ss
var patron_hora = new Array(2,2,2);

// PARA CONTROLAR LOS EVENTOS DE TECLADO
var ventana_modal = "no";

// VECTOR CON EL RANGO DE DIAS
var vector_rango_de_dias = new Array();

// PARA SABER SI SE ESTA UTILIZANDO EL BUSCADOR DE UN LISTADO
var se_busca = false;
/****************************************************************************************
		FUNCIONES GENERALES JAVASCRIPT DEL SISTEMA
****************************************************************************************/
function obtenerAnioActual()
{
    var fecha_actual = new Date();
    var anio = fecha_actual.getFullYear();

    return anio;
}
// PARA TRABAJAR CON AJAX
function objetoAjax()
{
    // Crea el objeto AJAX
    var xmlhttp=false;
    try {
	// Creacion del objeto AJAX para navegadores no IE
	xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
		try {
		  // Creacion del objeto AJAX para IE
		  xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
			xmlhttp = false;
		}
    }

    if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
		xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
}
/**
if (navigator.userAgent.indexOf("MSIE")>=0) navegador=0; // IE
else navegador=1; // Resto de Navegadores
/*********************************************************************************************/
function esIgualA( s )
{
    var cadena1 = this.length;
    var cadena2 = s.length;
    var n = ( cadena1 < cadena2 ? cadena1 : cadena2 );
    for ( i = 0 ; i < n ; i++ ) {
		var a = this.charCodeAt( i );
		var b = s.charCodeAt( i );
		if ( a != b )
			return( a - b );
    }
    return( cadena1 - cadena2 );
}

// Se redirecciona al login
function redireccionar() {
    location.href = "/sgl/index.php?sesion_caducada=true";
}

// Se actualiza el tiempo de la sesión
function actualizarTiempoSession() {
    var atsJSON = new Json.Remote('/sgl/librerias/actualizar_tiempo_sesion.php', {
        onComplete: function(objeto) {
            // Si NO fue actualizado el tiempo, por haber caducado la sesión
            if (objeto.actualizado === 'NO')
                // Se redirecciona al Login
                redireccionar();
        }
    });
    atsJSON.send();
}

// Se muestra el resultado de la URL en un div contenedor con AJAX
function refrescar(url, capa_destino) {
    // Antes de refrescar el contenido, se actualiza el tiempo de la sesión
    actualizarTiempoSession();

    //alert('url: '+url+'\nDestino: '+capa_destino);
    if ( capa_destino != 'capaAjaxFichaLog' ) {
        if (capa_destino == 'contenidoAjaxPrincipal')
            $('precarga_principal').setStyle('display', 'block');
        else
            if ( capa_destino != 'capaVentana' )
                $('precarga_modal').setStyle('display', 'block');
    }

    //alert('url: '+url+'\nDestino: '+capa_destino);
    $(capa_destino).setStyle('display', 'none');

    var miAjax = new Ajax(url, {
        method: 'get',
        data:'',
        evalScripts:true,
        update: $(capa_destino),
        onComplete: function(e) {
            if (capa_destino != 'capaAjaxFichaLog') {
                if (capa_destino == 'contenidoAjaxPrincipal')
                    $('precarga_principal').setStyle('display', 'none');
                else
                    if ( capa_destino != 'capaVentana' )
                        $('precarga_modal').setStyle('display', 'none');
            }
            $(capa_destino).setStyle('display', 'block');
        },
        onFailure: function(xhr) {
            redireccionar();
        }
    });
    miAjax.request();
}

//	SE MUESTRA EL RESULTADO DE LA URL EN UN DIV CONTENEDOR CON AJAX
function refrescarCombo(url, capa_destino) {
    // Antes de refrescar el contenido, se actualiza el tiempo de la sesión
    actualizarTiempoSession();

    var miAjax = new Ajax(url, {
		method: 'get',
		data:'',
		evalScripts:true,
		update: $(capa_destino),
		onFailure: function(xhr)
		{
		    redireccionar();
		}
    });
    miAjax.request();
}

//	SE ENVIA EL FORMULARIO CON AJAX
function enviarForm(form, carpeta, destino) {
    // Antes de enviar el formulario, se actualiza el tiempo de la sesión
    actualizarTiempoSession();

    if (destino == 'contenidoAjaxPrincipal')
        $('precarga_principal').setStyle('display', 'block');
    else
        $('precarga_modal').setStyle('display', 'block');

    $(destino).setStyle('display', 'none');

    var miAjax = new Ajax(carpeta+'/index.php', {
      method: 'post',
      data:$(form),
      evalScripts: true,
      update: $(destino),
      onComplete: function() {
		  if (destino == 'contenidoAjaxPrincipal')
              $('precarga_principal').setStyle('display', 'none');
          else
              $('precarga_modal').setStyle('display', 'none');

		  $(destino).setStyle('display', 'block');
      },
      onFailure: function(xhr) {
         redireccionar();
      }
    });
    miAjax.request();
}

//	SE ENVIA EL FORMULARIO CON AJAX
function enviarFormModal(form, carpeta, destino) {
    // Antes de enviar el formulario de la modal, se actualiza el tiempo de la sesión
    actualizarTiempoSession();

    var miAjax = new Ajax(carpeta+'/index.php', {
      method: 'post',
      data:$(form),
      evalScripts: true,
      update: $(destino),
      onFailure: function(xhr) {
          redireccionar();
      }
    });
    miAjax.request();
}

// 	SE RECIBE EL IDENTIFICADOR DE LA FILA
function seleccionarFila(id, anio, tipo, numero, cuerpo, alcance)
{
    //alert('Id: '+id+'\n Anio: '+anio+'\n Tipo: '+tipo+'\n Numero: '+numero+'\n Cuerpo: '+cuerpo+'\n Alcance: '+alcance);
    $(id).setStyle('background-color','#FF9900');
    $(id).setStyle('color','#080808');

    $('filaSeleccionada').value = id;
    $('anio').value = anio;
    $('tipo').value = tipo;
    $('numero').value = numero;
    $('cuerpo').value = cuerpo;
    $('alcance').value = alcance;
}

//	PARA CARGAR EN EL BUSCADOR
function cargarBuscador(anio, tipo, numero, cuerpo, alcance)
{
    $('f_anio').value = anio;
    $('f_tipo').value = tipo;
    $('f_numero').value = numero;
    $('f_cuerpo').value = cuerpo;
    $('f_alcance').value = alcance;
}

//	SE CARGAN LOS DATOS PARA EL BOTON Agregado EN LA SOLAPA Expedientes
function cargarAgregado(anio, tipo, numero, cuerpo, alcance)
{
    //alert('Anio:'+anio+'\nTipo:'+tipo+'\nNro:'+numero+'\nCuerpo:'+cuerpo+'\nAlcance:'+alcance);

    // 26/01/2012: SE AGREGO _inferior
    $('agregado_anio_inferior').value = anio;
    $('agregado_tipo_inferior').value = tipo;
    $('agregado_numero_inferior').value = numero;
    $('agregado_cuerpo_inferior').value = cuerpo;
    $('agregado_alcance_inferior').value = alcance;
}
// 26/01/2012: SE VERIFICA SI ESTA AGREGADO A UN EXPEDIENTE O UNA NOTA
function verAgregado()
{
    if ( $('agregado_tipo_inferior').value != 's/tipo' )
    {
		var url = 'abms/index.php?controlador=expedientes&accion=listar&anio='+$('agregado_anio_inferior').value+'&tipo='+$('agregado_tipo_inferior').value+'&numero='+$('agregado_numero_inferior').value+'&cuerpo='+$('agregado_cuerpo_inferior').value+'&alcance='+$('agregado_alcance_inferior').value+'&agregado=true';
		//alert(url);
		refrescar(url, 'contenidoAjaxPrincipal');
    }
    else
    {
		var mensaje = "Su Nota no se encuentra agregada a otro Expediente o Nota.";

		if ($('f_tipo').value == 'E') mensaje = "Su Expediente no se encuentra agregado a otro Expediente o Nota.";

		alert(mensaje);
    }
}

//	SE CARGAN LOS DATOS PARA EL BOTON Agregado EN LA SOLAPA Expedientes
function paraIrAntecedente(anio, tipo, numero, cuerpo, alcance)
{
    $('antecedente_anio').value    = anio;
    $('antecedente_tipo').value    = tipo;
    $('antecedente_numero').value  = numero;
    $('antecedente_cuerpo').value  = cuerpo;
    $('antecedente_alcance').value = alcance;
}

function irAntecedente()
{
    var url = 'abms/index.php?controlador=expedientes&accion=listar&anio='+$('antecedente_anio').value+'&tipo='+$('antecedente_tipo').value+'&numero='+$('antecedente_numero').value+'&cuerpo='+$('antecedente_cuerpo').value+'&alcance='+$('antecedente_alcance').value+'&sentido=anterior';

    refrescar(url, 'contenidoAjaxPrincipal');
}

//	AL MOVERSE POR LAS FILAS DEL LISTADO SE VISUALIZA LA observacion
function refrescarObservaciones(valor)
{
    $('observaciones').value = valor;
}

function refrescarEstado(codigo_estado, observaciones_estado)
{
    //alert('Codigo: '+codigo_estado+'\n Observaciones: '+observaciones_estado);
    //	SE VISUALIZAN EL CODIGO Y LA OBSERVACION DEL ESTADO DEL EXPEDIENTE
    $('estadoExped').setHTML(codigo_estado+'&nbsp;&nbsp;&nbsp;'+observaciones_estado);
}

function ordenarColumna(campo, controlador)
{
    // PARA BUSCAR LUEGO POR EL CAMPO EN LA TABLA ESPECIFICADA
    $('campo_orden').value = campo;
    // SE ORDENA POR EL CAMPO ESPECIFICADO
    refrescar('abms/index.php?controlador='+controlador+'&accion=listar&campo_orden='+campo+'', 'contenidoAjaxPrincipal');//&pagina='+$('pagina').value+'
}

function marcar(titulo)
{
    $(titulo).setStyle('color','blue');
}

//	SE REFRESCA EL LISTADO CON EL RESULTADO DEVUELTO SEGUN EL VALOR A FILTRAR EN EL CAMPO ESPECIFICADO
function buscar(campo_busqueda, valor_buscado, controlador)
{
    var url = 'abms/index.php?controlador='+controlador+'&accion=listar&campo_orden='+campo_busqueda+'&valor_buscado='+valor_buscado+'';

    refrescar(url, 'contenidoAjaxPrincipal');
}

//	SE ESTABLECE EL VALOR DE ACTIVADO O DESACTIVADO AL CHECKBOX
function chequear(nombre_campo)
{
    if ($('habilitado').checked == true){
	    $(nombre_campo).value = 1;
    }else{
	    $(nombre_campo).value = 0;
    }
}

function muestraReloj()
{
    var fechaHora = new Date();
    var horas = fechaHora.getHours();
    var minutos = fechaHora.getMinutes();
    var segundos = fechaHora.getSeconds();

    if (horas < 10) { horas = '0' + horas; }
    if (minutos < 10) { minutos = '0' + minutos; }
    if (segundos < 10) { segundos = '0' + segundos; }

    tiempoInicial = horas+':'+minutos+':'+segundos;

    document.getElementById("reloj").innerHTML = horas+':'+minutos;
}

// PERMITE INGRESAR SOLO NUMEROS
var tecla_soloEnteros = window.Event ? true : false;
function soloEnteros(evt)  // se obliga al usuario que ingrese solo numeros
{
    // NOTA: Backspace = 8, Enter = 13, '0' = 48, '9' = 57
    var key_soloEnteros = tecla_soloEnteros ? evt.which : evt.keyCode;
    return (key_soloEnteros <= 13 || (key_soloEnteros >= 48 && key_soloEnteros <= 57));
}

// PERMITE INGRESAR SOLAMENTE NUMEROS Y LA /
var tecla_para_fecha = window.Event ? true : false;
function solo_enteros_y_barra(evt)  // se obliga al usuario que ingrese solo numeros
{
    // NOTA: Backspace = 8, Enter = 13, '0' = 48, '9' = 57, Barra / = 47
    var key_para_fecha = tecla_para_fecha ? evt.which : evt.keyCode;
    return (key_para_fecha <= 13 || (key_para_fecha >= 47 && key_para_fecha <= 57));
}

// PERMITE INGRESAR SOLAMENTE NUMEROS Y LA ,(Coma)
var tecla_para_decimal = window.Event ? true : false;
function solo_enteros_y_coma(evt)  // se obliga al usuario que ingrese solo numeros
{
    // NOTA: Backspace = 8, Enter = 13, '0' = 48, '9' = 57, Coma ',' = 44
    var key_para_decimal = tecla_para_decimal ? evt.which : evt.keyCode;
    return (key_para_decimal <= 13 || (key_para_decimal >= 48 && key_para_decimal <= 57) || key_para_decimal == 44);
}

// se obliga al usuario que ingrese un anio menor o igual al actual
function respetar_anio(anio)
{
    var anio_actual = obtenerAnioActual();

    if (anio.value.length == 4) {
        if ( anio.value <= anio_actual ) // XXXX 26/07/2017 SE QUITÓ LA VALIDACIÓN: anio.value >= '1983' &&
			return true;
		else {
			alert("Ingrese un a"+'\u00f1'+"o v"+'\u00e1'+"lido");
			anio.value = '';
			anio.focus();
		}
    }
}

//Completa la hora en caso de haberse ingresado sólo la hora
function formatearHora(elemento)
{
	// Si se ingresó un valor
	if ( elemento.value != '' )
	{
		// Cantidad de caracteres ingresados
		var longitud = elemento.value.length;

		// Si se ingresaron dos caracteres para la hora, se completa
		if( longitud == 2 )
		{
			elemento.value = elemento.value+":00";
		}

		// Si se ingresó sólo 1 carácter para la hora, se completa con cero
		if( longitud == 1 )
		{
			elemento.value = "0"+elemento.value+":00";
		}
	}
	else
	{
		elemento.value = '';
	}
}

//Verifica si los valores para Hora, Minutos y Segundos son válidos
function verifica_hora(cadena)
{
	hora = cadena.value;

	if (hora == '')
	{
		return;
	}

	a = hora.charAt(0); //<= 2
	b = hora.charAt(1); //< 4
	d = hora.charAt(3); //<= 5
	f = hora.charAt(6); //<= 5

	if (f > 5)
	{
		alert("El valor que introdujo en los Segundos no corresponde\n ingrese un valor entre 00 y 59");
		return;
	}
	if (d > 5)
	{
		alert("El valor que introdujo en los Minutos no corresponde\n ingrese un valor entre 00 y 59");
		return;
	}
	if ((a == 2 && b > 3) || (a > 2))
	{
		alert("El valor que introdujo en la Hora no corresponde\n ingrese un valor entre 00 y 23");
		return;
	}
}

/***************	27/11/2012		*****************************************************/
function verExpediente(anio, tipo, numero, cuerpo, alcance, listado)
{
    //alert('anio: '+anio+'\nTipo: '+tipo+'\nNumero: '+numero+'\nCuerpo: '+cuerpo+'\nAlcance: '+alcance);

    var url = 'abms/index.php?controlador=expedientes&accion=listar&anio='+anio+'&tipo='+tipo+'&numero='+numero+'&cuerpo='+cuerpo+'&alcance='+alcance+'&sentido=anterior&listado='+listado+'&cerrar_modal=no';
    refrescar(url, 'contenidoAjaxPrincipal');
}

function cerrarModal(anio, tipo, numero, cuerpo, alcance)
{
    var url = 'abms/index.php?controlador=expedientes&accion=listar&anio='+anio+'&tipo='+tipo+'&numero='+numero+'&cuerpo='+cuerpo+'&alcance='+alcance+'&sentido=anterior&cerrar_modal=si';
    refrescar(url, 'contenidoAjaxPrincipal');
}

function cerrarModal_por_antecedente(anio, tipo, numero, cuerpo, alcance)
{
    var url = 'abms/index.php?controlador=expedientes&accion=listar&anio='+anio+'&tipo='+tipo+'&numero='+numero+'&cuerpo='+cuerpo+'&alcance='+alcance+'&sentido=anterior';
    refrescar(url, 'contenidoAjaxPrincipal');
}

function cerrarModalNueva()
{
	$("capaFondo").setStyle('visibility','hidden');
	$("capaVentana").setStyle('visibility','hidden');
}
/***************************************************************************************
	PARA LAS VENTANAS MODALES
***************************************************************************************/
function volverModal(campoOculto, campo1, campo2, oculto, codigo, descripcion)
{
	if (campoOculto != '' && oculto != '')
    {
    	// ES EL id PARA LA REFERENCIA EN LA BD
	    $(campoOculto).value = oculto;
    }
    $(campo1).value = codigo;
    $(campo2).value = descripcion;

    $('mb_close_link').onclick();// SE CIERRA LA VENTANA MODAL
}

function volverModaldiv(campoOculto, campo1, campo2, oculto, codigo, descripcion)
{
    if (campoOculto != '' && oculto != '')
    {
	    $(campoOculto).value = oculto;// ES EL id PARA LA REFERENCIA EN LA BD
    }

    $(campo1).value = codigo;

    if (campo2 == 'valor_iniciador_descripcion')
    {
		$(campo2).setHTML(descripcion);
    }
    else
    {
		$(campo2).setHTML(descripcion.substring(0, 17)+' ...');
    }

    $('mb_close_link').onclick();// SE CIERRA LA VENTANA MODAL
}

function volverModalEdit(campoOculto, campo1, campo2, oculto, codigo, descripcion)
{
    if (campoOculto != '' && oculto != ''){
	    $(campoOculto).value = oculto;//GRALMENTE. ES EL id PARA LA REFERENCIA EN LA BD
    }
    $(campo1).value = codigo;
    $(campo2).setHTML(descripcion);
    $('comision_descripcion').value = descripcion;

    $('mb_close_link').onclick();// SE CIERRA LA VENTANA MODAL
}

function volverModal_proyectos(campo1, campo2, orden, descripcion)
{
    $(campo1).value = orden;
    $(campo2).value = descripcion;

    $('mb_close_link').onclick();// SE CIERRA LA VENTANA MODAL
    setfocus('fecha_sancion');
}

function volverModal_lugares(valor_iniciador_tipo, valor_iniciador_codigo, valor_iniciador_descripcion, valor_bloque_tipo, valor_bloque_codigo)
{
    $('iniciador_tipo').value = valor_iniciador_tipo;
    $('iniciador_codigo').value = valor_iniciador_codigo;
    $('iniciador_descripcion').value = valor_iniciador_descripcion;
    //$('bloque_tipo').value = valor_bloque_tipo;
    //$('bloque_codigo').value = valor_bloque_codigo;

    $('mb_close_link').onclick();// SE CIERRA LA VENTANA MODAL
}
/******************************************************************************************
	PARA PERSONALIZAR LOS Alert (de Javascript) DEL SISTEMA
********************************************************************************************/
// over-ride the alert method only if this a newer browser.
// Older browser will see standard alerts
if (document.getElementById) {
    window.alert = function(txt) {
	    createCustomAlert(txt);
    }
}
function createCustomAlert(txt)
{
    // shortcut reference to the document object
    d = document;

    // if the modalContainer object already exists in the DOM, bail out.
    if (d.getElementById("modalContainer")) return;

    // create the modalContainer div as a child of the BODY element
    mObj = d.getElementsByTagName("body")[0].appendChild(d.createElement("div"));
    mObj.id = "modalContainer";
      // make sure its as tall as it needs to be to overlay all the content on the page
    mObj.style.height = document.documentElement.scrollHeight + "px";

    // create the DIV that will be the alert
    alertObj = mObj.appendChild(d.createElement("div"));
    alertObj.id = "alertBox";
    // MSIE doesnt treat position:fixed correctly, so this compensates for positioning the alert
    if (d.all && !window.opera) alertObj.style.top = document.documentElement.scrollTop + "px";
    // center the alert box
    alertObj.style.left = (d.documentElement.scrollWidth - alertObj.offsetWidth)/2 + "px";

    // se alinea a la izquierda el mensaje
    $('alertBox').setStyle('text-align','left');

    // create an H1 element as the title bar
    h1 = alertObj.appendChild(d.createElement("h1"));
    h1.appendChild(d.createTextNode(ALERT_TITLE));

    // create a paragraph element to contain the txt argument
    msg = alertObj.appendChild(d.createElement("p"));
    msg.innerHTML = txt;

    // create an anchor element to use as the confirmation button.
    btn = alertObj.appendChild(d.createElement("a"));
    btn.id = "closeBtn";
    btn.appendChild(d.createTextNode(ALERT_BUTTON_TEXT));
    btn.href = "#";
    // set up the onclick event to remove the alert when the anchor is clicked
    btn.onclick = function() { removeCustomAlert();return false; }
}
// removes the custom alert from the DOM
function removeCustomAlert()
{
    document.getElementsByTagName("body")[0].removeChild(document.getElementById("modalContainer"));
}
/***********************************************************************************************
	PARA NO ELEGIR UNA FECHA ANTERIOR A 01/01/1983
************************************************************************************************/
function fecha_valida(fecha)
{
    if (fecha == '')
    {
	    valida = false;
    }
    else
    {
	    // SE SEPARA EL AÑO, MES Y DÍA DE LAS FECHAS
	    var fecha_a_evaluar = fecha.split("/");
	    // SE TOMA EL AÑO, MES Y DÍA DE LA FECHA
	    var anio = fecha_a_evaluar[2];
	    var mes = fecha_a_evaluar[1];
	    var dia = fecha_a_evaluar[0];

	    if (anio == '1983'){ //SI EL AÑO ES IGUAL A '1983', SE VERIFICA EL MES
		    if (mes == '01'){ //SI EL MES ES IGUAL A '01', SE VERIFICA EL DÍA
			    if (dia >= '01'){
				    valida = true; //SE CUMPLE QUE LA FECHA ES VALIDA
			    }else{
				    valida = false;
			    }
		    }else{
			    if (mes > '01'){ //SI EL MES ES MAYOR YA BASTA, EL DÍA PUEDE SER MENOR O IGUAL
				    valida = true;
			    }
		    }
	    }else{
		    if (anio > '1983'){ //SI EL AÑO ES MAYOR YA BASTA, EL MES PUEDE SER MENOR O IGUAL
			    valida = true;
		    }else{
			    valida = false;//SI NO SE CUMPLE NINGUNA CONDICIÓN LA FECHA NO ES VALIDA
		    }
	    }
    }
    return valida;
}
/***********************************************************************************************
  PARA COMPARAR LAS FECHAS Desde Y Hasta
************************************************************************************************/
function comparar_fechas(fechaDesde, fechaHasta)
{
    var fecha_desde = fechaDesde.substr(6,4)+fechaDesde.substr(3,2)+fechaDesde.substr(0,2);

    var fecha_hasta = fechaHasta.substr(6,4)+fechaHasta.substr(3,2)+fechaHasta.substr(0,2);

    if ( fecha_hasta < fecha_desde )
    {
		return false;
	}

	return true;
}
/*****************************************************************************************
	Formatea una fecha a aaaa-mm-dd
*****************************************************************************************/
function formatearConGuion(fecha)
{
	// Se separa el año, mes y día de la fecha
    var partes_fecha = fecha.split("/");

    // Se toma el año, mes y día de la fecha
    var anio = partes_fecha[2];
    var mes = partes_fecha[1];
    var dia = partes_fecha[0];

    // Devuelve la fecha en formato aaaa-mm-dd
    return anio+"-"+mes+"-"+dia;
}
/*****************************************************************************************
Formatea una fecha a dd/mm/aaaa
*****************************************************************************************/
function formatearConBarra(fecha)
{
	// Se separa el año, mes y día de la fecha
	var partes_fecha = fecha.split("-");

	// Se toma el año, mes y día de la fecha
	var anio = partes_fecha[0];
	var mes = partes_fecha[1];
	var dia = partes_fecha[2];

	// Devuelve la fecha en formato dd/mm/aaaa
	return dia+"/"+mes+"/"+anio;
}
/*****************************************************************************************
		SE VERIFICA EL INGRESO DEL Codigo PARA LOS ABM's DE Archivos
*****************************************************************************************/
function validarCodigo(campo, form, carpeta, destino)
{
    var mensaje = '';
    var error = false;

    if ($(campo).value == '')
    {
		mensaje += "Debe ingresar un valor para el C"+'\u00f3'+"digo.";
		error = true;
    }
    if (campo != 'codigo_usuario')
    {   //SI NO SE EDITA UN USUARIO (NO POSEE FECHA 'Desde' Y FECHA 'Hasta')
		if ($('vigencia_hasta').value != '')
		{
			if (!comparar_fechas($('vigencia_desde').value, $('vigencia_hasta').value))
			{
				mensaje += '<br>La fecha Hasta no debe ser menor o igual a la fecha Desde.';
				error = true;
			}
		}
    }
    if (error)
    {
		alert(mensaje);
    }else{
		enviarForm(form, carpeta, destino);
    }
}

function validarLugar()
{
    var mensaje = '';
    var error = false;

    if ( $('tipo_grp').value == '' )
    {
		mensaje += "Debe ingresar un valor para Tipo.";
		error = true;
    }

    if ( $('codigo_grp').value == '' )
    {
		mensaje += "<br>Debe ingresar un valor para C"+'\u00f3'+"digo.";
		error = true;
    }

	if ( $('vigencia_hasta').value != '' )
	{
		if ( !comparar_fechas($('vigencia_desde').value, $('vigencia_hasta').value) )
		{
			mensaje += '<br>La fecha Hasta no debe ser menor o igual a la fecha Desde.';
			error = true;
		}
	}

    if (error)
    {
		alert(mensaje);
    }else{
		enviarForm('formLugares', 'abms', 'contenidoAjaxPrincipal');
    }
}
/*****************************************************************************************
		SE VALIDA EL INGRESO DE DATOS EN EL ABM DE Proyectos
*****************************************************************************************/
function validarProyecto(por_boton_Agregar)
{
    var mensaje = '';
    var error = false;

    if ($('anio').value == ''){
	    error = true;
    }
    if ($('numero').value == ''){
	    error = true;
    }
    if ($('orden_proyecto').value == ''){
	    error = true;
    }
    if ($('id_codproyecto').value == ''){
	    error = true;
    }
    if (por_boton_Agregar == true){
	    $('por_boton_Agregar').value = true;
    }else{
	    $('por_boton_Agregar').value = false;
    }
    if (error){
	    mensaje = "No ha ingresado alguno de los siguientes datos: A"+'\u00f1'+"o, N"+'\u00fa'+"mero, Orden y/o C"+'\u00f3'+"digo.";
	    alert(mensaje);
    }else{
	    enviarForm('formProyectos', 'abms', 'contenidoAjaxPrincipal');
    }
}
/*****************************************************************************************
		SE VALIDA EL INGRESO DE DATOS EN EL ABM DE Giros
*****************************************************************************************/
function validarGiro()
{
    var mensaje = '';
    var error = false;

    if ( $('iniciador_codigo').value == '' && $('iniciador_descripcion').value == '' )
    {
		mensaje += "Debe ingresar un valor para  C"+'\u00f3'+"digo y/o Descripci"+'\u00f3'+"n.";
	    error = true;
    }

	if ( $('fecha_salida_giro').value != '' )
	{
		if ( !comparar_fechas($('fecha_entrada_giro').value, $('fecha_salida_giro').value) )
		{
			mensaje += '<br>La Fecha de Salida no debe ser menor a la Fecha de Entrada.';
			error = true;
		}
	}

    if (error)
    {
	    alert(mensaje);
    }else{
	    enviarForm('formGiros', 'abms', 'contenidoAjaxPrincipal');
    }
}
/*****************************************************************************************
		SE VALIDA EL INGRESO DE DATOS EN EL ABM DE Sanciones
*****************************************************************************************/
function validarSancion()
{
    var mensaje = '';
    var error = false;

    if ($('orden_proyecto').value == ''){
	    error = true;
    }
    if ($('fecha_sancion').value == ''){
	    error = true;
    }
    if (error){
	    mensaje = "No ha ingresado el Orden y/o la Fecha de Sanci"+'\u00f3'+"n.";
	    alert(mensaje);
    }else{
	    enviarForm('formSanciones', 'abms', 'contenidoAjaxPrincipal');
    }
}
/*****************************************************************************************
		SE VALIDA EL INGRESO DE DATOS EN EL ABM DE Estados
*****************************************************************************************/
function validarEstado()
{
    var mensaje = '';
    var error = false;

    if ( $('fecha_estado').value == '' )
    {
	    error = true;
		mensaje += "* No ha ingresado una Fecha.";
	    /**
	    if ( esMayorAFechaActual($('fecha_estado').value) )
		{
			error = true;
			mensaje += "* La fecha debe ser menor o igual a la fecha actual.";
		}
		/**/
    }

    if ($('orden_estado').value == '')
    {
	    error = true;
	    mensaje += "<br>* No ha ingresado valor para Orden.";
    }

    if ($('id_codestado').value == '')
    {
	    error = true;
	    mensaje += "<br>* No ha ingresado un Estado.";
    }

    if (error)
    {
	    alert(mensaje);
    }
    else
    {
	    enviarForm('formEstados', 'abms', 'contenidoAjaxPrincipal');
    }
}
/*****************************************************************************************
		SE VALIDA EL INGRESO DE DATOS EN EL ABM DE Antecedentes
*****************************************************************************************/
function validarAntecedente(por_boton_Agregar)
{
    var mensaje = '';
    var error = false;

    if ($('anio_a').value == ''){
	    error = true;
    }
    if ($('numero_a').value == ''){
	    error = true;
    }
    if (por_boton_Agregar == true){
	    $('por_boton_Agregar').value = true;
    }else{
	    $('por_boton_Agregar').value = false;
    }
    if (error){
	    mensaje = "No ha ingresado el A"+'\u00f1'+"o y/o el N"+'\u00fa'+"mero.";
	    alert(mensaje);
    }else{
	    enviarForm('formAntecedentes', 'abms', 'contenidoAjaxPrincipal');
    }
}
/*****************************************************************************************
	SE VALIDA EL INGRESO DE DATOS PARA UN PRÉSTAMO DE UN ENTE EXTERNO
*****************************************************************************************/
function validarPrestamo(formulario)
{
    var mensaje = '';
	var error = false;

	// SI NO TIENE VALOR EL AÑO
	if ( $('anio').value == '' )
	{
		mensaje += "No ha ingresado un A"+'\u00f1'+"o.";
		error = true;
	}

	// SI NO TIENE VALOR EL NUMERO
	if ( $('numero').value == '' )
	{
		mensaje += "<br>No ha ingresado un N"+'\u00fa'+"mero.";
		error = true;
	}

	// SI NO TIENE VALOR EL CUERPO
	if ( $('cuerpo').value == '' )
	{
		mensaje += "<br>No ha ingresado un Cuerpo.";
		error = true;
	}

	// SI NO TIENE VALOR EL ALCANCE
	if ( $('alcance').value == '' )
	{
		mensaje += "<br>No ha ingresado un Alcance.";
		error = true;
	}

	// SI NO TIENEN VALORES LOS DATOS RESTANTES DE LA CLAVE
	if ( $('digito').value == '' || $('cuerpoalcance').value == '' || $('anexoalcance').value == '' ||
		 $('cuerpoanexoalcance').value == '' || $('anexo').value == '' || $('cuerpoanexo').value == '' )
	{
		mensaje += "<br>No ha ingresado Digito, Cuerpo Alcance, Anexo Alcance, Cuerpo Anexo Alcance, Anexo ó Cuerpo Anexo.";
		error = true;
	}

	// SI NO TIENE VALOR LA FECHA DE SOLICITUD
	if ( $('solo_fecha_solicitud').value == '' )
	{
		mensaje += "<br>No ha seleccionado una fecha de solicitud del pr"+'\u00e9'+"stamo.";
		error = true;
	}

	// SI NO TIENE VALOR LA HORA DE SOLICITUD
	if ( $('solo_hora_solicitud').value == '' )
	{
		error = true;
		mensaje += "<br>Debe ingresar un Horario.";
	}

	// SI NO SE ELIGIÓ UN SOLICITANTE
	if ($('solicitante').value == '0')
	{
		mensaje += "<br>No ha seleccionado un Solicitante.";
		error = true;
	}

	if (error)
		alert(mensaje);
	else {
		// Se une la fecha y la hora de Solicitud para armar el formato "aaaa-mm-dd hh:mm:ss"
		$('fecha_solicitud').value = formatearConGuion($('solo_fecha_solicitud').value)+" "+$('solo_hora_solicitud').value;

		enviarForm(formulario, 'abms', 'contenidoAjaxPrincipal');
	}
}
/*****************************************************************************************
		SE VALIDA EL INGRESO DE DATOS EN EL ABM DE Prestamos para Expedientes Externos
*****************************************************************************************/
function validarPrestamoExpedExterno()
{
	var mensaje = '';
	var error = false;

	if ($('anio').value == '')
	{
		mensaje = "No ha ingresado un A"+'\u00f1'+"o.";
		error = true;
	}

	if ($('numero').value == '')
	{
		mensaje = "\n No ha ingresado un N"+'\u00fa'+"mero.";
		error = true;
	}

	if ($('fecha_solicitud_hcd').value == '')
	{
		mensaje = "\n No ha seleccionado una fecha de solicitud del pr"+'\u00e9'+"stamo.";
		error = true;
	}

	if ($('solicitante').value == '0')
	{
		mensaje = "\n No ha seleccionado un Solicitante.";
		error = true;
	}

	if ($('estado').value == '0')
	{
		mensaje = "\n No ha seleccionado un estado.";
		error = true;
	}

	if (error)
		alert(mensaje);
	else
		enviarForm('formPrestamosExpedExterno', 'abms', 'contenidoAjaxPrincipal');
}
/*****************************************************************************************
		SE VALIDA EL INGRESO DE DATOS EN EL ABM DE Informes
*****************************************************************************************/
function validarInforme()
{
    var mensaje = '';
    var error = false;

	if ( $('fecha_pedido_informe').value == '' )
	{
		mensaje += 'Debe ingresar una Fecha de Pedido.';
	    error = true;
    }

	if ( $('fecha_vuelta_informe').value != '' )
	{
		if ( !comparar_fechas($('fecha_pedido_informe').value, $('fecha_vuelta_informe').value) )
		{
			mensaje += '<br>La Fecha de Vuelta no debe ser menor a la Fecha de Pedido.';
			error = true;
		}
	}

    if (error)
    {
	    alert(mensaje);
    }
    else
    {
	    enviarForm('formInformes', 'abms', 'capaVentana');
    }
}
/********************************************************************************************
		SE RESALTA LA FILA AL POSICIONARSE SOBRE ELLA
********************************************************************************************/
// 09/02/2012
// 	SE RECIBE EL IDENTIFICADOR (id) DE LA FILA
function resaltarFila(nroFila)
{
    if ( nroFila != $('nroFila_elegida').value )
    {
	    $('e_fila'+nroFila+'').setStyles({'background-color':'#EBEFF9', 'color':'#000'});//#76A0CD
    }
}
// 09/02/2012
// 	SE RECIBE EL IDENTIFICADOR (id) DE LA FILA
function no_resaltarFila(nroFila)
{
    if ( nroFila != $('nroFila_elegida').value )
    {
	    $('e_fila'+nroFila+'').setStyles({'background-color':'#fff', 'color':'#000'});
    }
}
/********************************************************************************************
		SE RESALTA LA FILA AL CLIQUEAR SOBRE ELLA
********************************************************************************************/
// 	SE RECIBE EL IDENTIFICADOR (id) DE LA FILA
function marcarFila(nroFila)
{
    $('nroFila_elegida').value = nroFila;

    //SE OBTIENEN TODAS LOS FILAS DEL LISTADO PARA DESMARCAR
    var filas_marcarFila = $$('#e_cuerpo_scrolleable tr');
    for (i = 0; i < filas_marcarFila.length; i++)
    {
		filas_marcarFila[i].style.background = '#fff';
		//filas_marcarFila[i].style.color = '#000'; // 21/06/2018
    }

    //SE MARCA LA FILA DETERMINADA
    $('e_fila'+nroFila+'').style.background = '#76A0CD';
    //$('e_fila'+nroFila+'').style.color = '#000'; //#fff 21/06/2018
}
/*******************************************************************************************
		PARA UTILIZAR LAS TECLAS Arriba Y Abajo DEL TECLADO EN LOS LISTADOS
*******************************************************************************************/
function pedirDatos(anio, tipo, numero, cuerpo, alcance)
{
    var url = 'abms/index.php?controlador=expedientes&accion=verDatosInferior&anio='+anio+'&tipo='+tipo+'&numero='+numero+'&cuerpo='+cuerpo+'&alcance='+alcance+'';

    // Donde se mostrará el resultado
    divResultado = $('capa_datos_inferior');

    var miAjax = new Ajax(url,
    {
		method: 'get',
		data:'',
		evalScripts:true,
		update: divResultado,
		onComplete: function()
		{
			// SE DEFINE EL AREA QUE SE MOVERA
			capa_desplazar = $('p_extractos');
			// SI POSEE MÁS DE UN PROYECTO EL EXPEDIENTE, SE DEFINE LA FUNCIONALIDAD DE CADA BOTÓN
			if ($('btSubir'))
			{
				// SE DEFINEN LOS MOVIMIENTOS DEL DESPLAZAMIENTO
				$('btSubir').addEvent('click', function(){
					//alert('sube');
					capa_desplazar.scrollTo(0, capa_desplazar.getSize().scroll.y - 82);
				});

				$('btBajar').addEvent('click', function(){
					capa_desplazar.scrollTo(0, capa_desplazar.getSize().scroll.y + 82);
				});
			}

			// 27/03/2012
			if ( $('h_codigo_usuario') )
			{
				// SE MUESTRA EL NOMBRE DEL USUARIO DEL EXPEDIENTE
				$('modifico_usr').setHTML('Modificado por : '+$('h_codigo_usuario').value);
			}

			// 27/03/2012
			if ( $('h_estado_doc') )
			{
				// SE MUESTRA EL ESTADO DEL DOCUMENTO .doc (PARA CARGAR, CARGADO, SIN CARGAR)
				establecerEstadoDocumento($('h_estado_doc').value);
			}

            // 2020/05/07 XXXX
            if ( $('h_estado_digitalizacion') && $('digi_completa') )
                // Se muestra el estado de la Digitalización (PARA CARGAR, CARGA PARCIAL/CARGA COMPLETA, SIN CARGAR)
                establecerEstadoDigitalizacion($('h_estado_digitalizacion').value, $('digi_completa').value);
		}
	});
    miAjax.request();
}

function establecerEstadoDocumento(estado_doc)
{
    //alert(estado_doc);
    // SE MUESTRA EL ESTADO DEL DOCUMENTO .doc
    if ( estado_doc == 1 )
    {
		$('estado_doc').setHTML(" PARA CARGAR");
		$('estado_doc').setStyle('color', 'yellow');
    }
    else if ( estado_doc == 2 )
    {
		$('estado_doc').setHTML(" CARGADO");
		$('estado_doc').setStyle('color', 'green');
    }
    else if ( estado_doc == 3 )
    {
		$('estado_doc').setHTML(" SIN CARGAR");
		$('estado_doc').setStyle('color', 'red');
    }
    else
    {
		$('estado_doc').setHTML(" SIN CARGAR");
		$('estado_doc').setStyle('color', 'red');
	}
}

/**
 * Se establece el Estado de la Digitalizacion
 * @param  {[type]} estado_digitalizacion [description]
 * @param  {[type]} digi_completa         [description]
 * @return {[type]}                       [description]
 */
function establecerEstadoDigitalizacion(estado_digitalizacion, digi_completa)
{
    // SE MUESTRA EL ESTADO DEL DOCUMENTO .doc
    if ( estado_digitalizacion == 1 )
    {
        $('estado_digitalizacion').setHTML(" PARA CARGAR");
        $('estado_digitalizacion').setStyle('color', 'yellow');
    }
    else if ( estado_digitalizacion == 2 )
    {
        if (digi_completa == 1)
            $('estado_digitalizacion').setHTML(" COMPLETA");
        else
            $('estado_digitalizacion').setHTML(" CARGADA");

        $('estado_digitalizacion').setStyle('color', 'green');
    }
    else if ( estado_digitalizacion == 3 )
    {
        $('estado_digitalizacion').setHTML(" SIN CARGAR");
        $('estado_digitalizacion').setStyle('color', 'red');
    }
    else
    {
        $('estado_digitalizacion').setHTML(" SIN CARGAR");
        $('estado_digitalizacion').setStyle('color', 'red');
    }
}
/*******************************************************************************************************
    RECIBE LAS PULSACIONES DEL TECLADO PARA EDITAR O MOSTRAR LOS DATOS DEL REGISTRO SELECCIONADO
******************************************************************************************************/
function pulsar(e)
{
	var controlador = '';
	var valor_pagina = 0;
	var evt = e ? e : event;
	var tecla = window.Event ? evt.which : evt.keyCode;
	//var tecla = (document.all) ? e.keyCode : e.which;

	if ( $('controlador') && $('controlador').value != '' )
	{
		controlador = $('controlador').value;
	}

	// SI NO SE ESTAN UTILIZANDO LAS VENTANAS MODALES, NO SE ESTÁ EDITANDO Y NO ES EL LISTADO DE PRÉSTAMOS
	if ( ventana_modal != "si" && $('nroFila_elegida') && controlador != 'prestamos' )
	{
		//alert($('nroFila_elegida').value);
		if ( $('nroFila_elegida').value != '' )
		{
			fila_pulsar = eval($('nroFila_elegida').value * 1);
			//alert(fila_pulsar);
		}
		// REFERENCIA A LA SECCION QUE SE DESEA RECORRER
		seccion_a_recorrer = $('e_cuerpo_scrolleable');

		// SE OBTIENEN TODAS LAS FILAS DE DICHA SECCION
		filas_pulsar = seccion_a_recorrer.getElementsByTagName('tr');

		// SOLO PUEDEN EDITAR AQUELLOS USUARIOS CON PERFIL 1 ó 2
		if ( $('perfil_para_expedientes').value == 1 || $('perfil_para_expedientes').value == 2 )
		{
			// SI SE PULSA LA TECLA 'ENTER', SE EDITA DICHO REGISTRO
			if ( tecla == 13 && !se_busca )
			{
				switch (controlador)
				{
					case 'expedientes':
						//alert('abms/index.php?controlador=expedientes&accion=editar&anio='+$('i_anio'+fila_pulsar).innerHTML+'&tipo='+$('i_tipo'+fila_pulsar).innerHTML+'&numero='+$('i_numero'+fila_pulsar).innerHTML+'&cuerpo='+$('i_cuerpo'+fila_pulsar).innerHTML+'&alcance='+$('i_alcance'+fila_pulsar).innerHTML+'&pagina='+$('pagina').value+'&por_btAgregarExped=false');
						refrescar('abms/index.php?controlador=expedientes&accion=editar&anio='+$('i_anio'+fila_pulsar).innerHTML+'&tipo='+$('i_tipo'+fila_pulsar).innerHTML+'&numero='+$('i_numero'+fila_pulsar).innerHTML+'&cuerpo='+$('i_cuerpo'+fila_pulsar).innerHTML+'&alcance='+$('i_alcance'+fila_pulsar).innerHTML+'&pagina='+$('pagina').value+'&por_btAgregarExped=false', 'contenidoAjaxPrincipal');
						break;
					case 'proyectos':
						refrescar('abms/index.php?controlador=proyectos&accion=editar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&orden_proyecto='+$('i_orden_proyecto'+fila_pulsar).innerHTML+'&pagina='+$('pagina').value+'', 'contenidoAjaxPrincipal');
						break;
					case 'giros':
						refrescar('abms/index.php?controlador=giros&accion=editar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&orden_giro='+$('i_orden_giro'+fila_pulsar).innerHTML+'&pagina='+$('pagina').value+'', 'contenidoAjaxPrincipal');
						break;
					case 'sanciones':
						refrescar('abms/index.php?controlador=sanciones&accion=editar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&fecha_sancion='+$('i_fecha_sancion'+fila_pulsar).innerHTML+'&orden_proyecto='+$('i_orden_proyecto'+fila_pulsar).innerHTML+'&pagina='+$('pagina').value+'', 'contenidoAjaxPrincipal');
						break;
					case 'estados':
						refrescar('abms/index.php?controlador=estados&accion=editar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&fecha_estado='+$('fecha_estado_hidden'+fila_pulsar).value+'&orden_estado='+$('i_orden_estado'+fila_pulsar).innerHTML+'&pagina='+$('pagina').value+'', 'contenidoAjaxPrincipal');
						break;
					case 'antecedentes':
						refrescar('abms/index.php?controlador=antecedentes&accion=editar&&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&anio_a='+$('i_anio_a'+fila_pulsar).innerHTML+'&tipo_a='+$('i_tipo_a'+fila_pulsar).innerHTML+'&numero_a='+$('i_numero_a'+fila_pulsar).innerHTML+'&cuerpo_a='+$('i_cuerpo_a'+fila_pulsar).innerHTML+'&alcance_a='+$('i_alcance_a'+fila_pulsar).innerHTML+'&pagina='+$('pagina').value+'', 'contenidoAjaxPrincipal');
						break;
					case 'categorias':
						refrescar('abms/index.php?controlador=categorias&accion=editar&id='+$('id_codcategoria'+fila_pulsar).value+'&pagina='+$('pagina').value+'&mostrar_todos='+$('mostrar_todos').value+'', 'contenidoAjaxPrincipal');
						break;
					case 'codestados':
						refrescar('abms/index.php?controlador=codestados&accion=editar&id='+$('id_codestado'+fila_pulsar).value+'&pagina='+$('pagina').value+'&mostrar_todos='+$('mostrar_todos').value+'', 'contenidoAjaxPrincipal');
						break;
					case 'lugares':
						refrescar('abms/index.php?controlador=lugares&accion=editar&tipo='+$('tipo_grp'+fila_pulsar).value+'&codigo='+$('codigo_grp'+fila_pulsar).value+'&pagina='+$('pagina').value+'&mostrar_todos='+$('mostrar_todos').value+'', 'contenidoAjaxPrincipal');
						break;
					case 'codproyectos':
						refrescar('abms/index.php?controlador=codproyectos&accion=editar&id='+$('id_codproyecto'+fila_pulsar).value+'&pagina='+$('pagina').value+'&mostrar_todos='+$('mostrar_todos').value+'', 'contenidoAjaxPrincipal');
						break;
					case 'codtemas':
						refrescar('abms/index.php?controlador=codtemas&accion=editar&id='+$('id_codtema'+fila_pulsar).value+'&pagina='+$('pagina').value+'&mostrar_todos='+$('mostrar_todos').value+'', 'contenidoAjaxPrincipal');
						break;
				}
			}
		}

		// SI SE PULSA LA TECLA 'ARRIBA'
		if (tecla == 38)
		{
			// SI NO ES LA PRIMER FILA
			if (fila_pulsar > 0)
			{
				num = -1;// SE RESTA LA FILA
			}
			else
			{
				// SI ES LA PRIMER FILA, SE DIRECCIONA A LA PAGINA ANTERIOR
				if ( fila_pulsar == 0 )
				{
					//opcion = $('controlador').value;
					switch (controlador)
					{
						case 'expedientes':
							refrescar('abms/index.php?controlador=expedientes&accion=listar&anio='+$('i_anio'+fila_pulsar).innerHTML+'&tipo='+$('i_tipo'+fila_pulsar).innerHTML+'&numero='+$('i_numero'+fila_pulsar).innerHTML+'&cuerpo='+$('i_cuerpo'+fila_pulsar).innerHTML+'&alcance='+$('i_alcance'+fila_pulsar).innerHTML+'&sentido=anterior&por_teclado=arriba', 'contenidoAjaxPrincipal');
							break;
						case 'categorias':
							valor_pagina = validar_limite_inicial_paginador();
							refrescar('abms/index.php?controlador=categorias&accion=listar&pagina='+valor_pagina+'&sentido=anterior', 'contenidoAjaxPrincipal');
							break;
						case 'codestados':
							valor_pagina = validar_limite_inicial_paginador();
							refrescar('abms/index.php?controlador=codestados&accion=listar&pagina='+valor_pagina+'&sentido=anterior', 'contenidoAjaxPrincipal');
							break;
						case 'lugares':
							valor_pagina = validar_limite_inicial_paginador();
							refrescar('abms/index.php?controlador=lugares&accion=listar&pagina='+valor_pagina+'&sentido=anterior', 'contenidoAjaxPrincipal');
							break;
						case 'codproyectos':
							valor_pagina = validar_limite_inicial_paginador();
							refrescar('abms/index.php?controlador=codproyectos&accion=listar&pagina='+valor_pagina+'&sentido=anterior', 'contenidoAjaxPrincipal');
							break;
						case 'codtemas':
							valor_pagina = validar_limite_inicial_paginador();
							refrescar('abms/index.php?controlador=codtemas&accion=listar&pagina='+valor_pagina+'&sentido=anterior', 'contenidoAjaxPrincipal');
							break;
						case 'proyectos':
							valor_pagina = validar_limite_inicial_paginador();
							refrescar('abms/index.php?controlador=proyectos&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&pagina='+valor_pagina+'&sentido=anterior', 'contenidoAjaxPrincipal');
							break;
						case 'giros':
							valor_pagina = validar_limite_inicial_paginador();
							refrescar('abms/index.php?controlador=giros&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&pagina='+valor_pagina+'&sentido=anterior', 'contenidoAjaxPrincipal');
							break;
						case 'sanciones':
							valor_pagina = validar_limite_inicial_paginador();
							refrescar('abms/index.php?controlador=sanciones&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&pagina='+valor_pagina+'&sentido=anterior', 'contenidoAjaxPrincipal');
							break;
						case 'estados':
							valor_pagina = validar_limite_inicial_paginador();
							refrescar('abms/index.php?controlador=estados&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&pagina='+valor_pagina+'&sentido=anterior', 'contenidoAjaxPrincipal');
							break;
						case 'antecedentes':
							valor_pagina = validar_limite_inicial_paginador();
							refrescar('abms/index.php?controlador=antecedentes&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&pagina='+valor_pagina+'&sentido=anterior', 'contenidoAjaxPrincipal');
							break;
					}
				}
			}
		}
		else
		{
			// SI SE PULSA LA TECLA 'ABAJO'
			if (tecla == 40)
			{
				//alert('SE PULSA LA TECLA ABAJO');
				// SI NO ES LA ULTIMA FILA
				if (fila_pulsar < filas_pulsar.length-1)
				{
					num = 1;// SE INCREMENTA LA FILA
				}
				else
				{
					// SI SE PULSA EN EL ULTIMO REGISTRO DE LA PAGINA, SE DIRECCIONA A LA PAGINA SIGUIENTE
					if (fila_pulsar == filas_pulsar.length-1)
					{
						switch (controlador)
						{
							case 'expedientes':
								refrescar('abms/index.php?controlador=expedientes&accion=listar&anio='+$('i_anio'+fila_pulsar).innerHTML+'&tipo='+$('i_tipo'+fila_pulsar).innerHTML+'&numero='+$('i_numero'+fila_pulsar).innerHTML+'&cuerpo='+$('i_cuerpo'+fila_pulsar).innerHTML+'&alcance='+$('i_alcance'+fila_pulsar).innerHTML+'&sentido=siguiente&por_teclado=abajo', 'contenidoAjaxPrincipal');
								break;
							case 'categorias':
								valor_pagina = validar_limite_final_paginador();
								refrescar('abms/index.php?controlador=categorias&accion=listar&pagina='+valor_pagina+'&sentido=siguiente', 'contenidoAjaxPrincipal');
								break;
							case 'codestados':
								valor_pagina = validar_limite_final_paginador();
								refrescar('abms/index.php?controlador=codestados&accion=listar&pagina='+valor_pagina+'&sentido=siguiente', 'contenidoAjaxPrincipal');
								break;
							case 'lugares':
								valor_pagina = validar_limite_final_paginador();
								refrescar('abms/index.php?controlador=lugares&accion=listar&pagina='+valor_pagina+'&sentido=siguiente', 'contenidoAjaxPrincipal');
								break;
							case 'codproyectos':
								valor_pagina = validar_limite_final_paginador();
								refrescar('abms/index.php?controlador=codproyectos&accion=listar&pagina='+valor_pagina+'&sentido=siguiente', 'contenidoAjaxPrincipal');
								break;
							case 'codtemas':
								valor_pagina = validar_limite_final_paginador();
								refrescar('abms/index.php?controlador=codtemas&accion=listar&pagina='+valor_pagina+'&sentido=siguiente', 'contenidoAjaxPrincipal');
								break;
							case 'proyectos':
								valor_pagina = validar_limite_final_paginador();
								refrescar('abms/index.php?controlador=proyectos&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&pagina='+valor_pagina+'&sentido=siguiente', 'contenidoAjaxPrincipal');
								break;
							case 'giros':
								valor_pagina = validar_limite_final_paginador();
								refrescar('abms/index.php?controlador=giros&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&pagina='+valor_pagina+'&sentido=siguiente', 'contenidoAjaxPrincipal');
								break;
							case 'sanciones':
								valor_pagina = validar_limite_final_paginador();
								refrescar('abms/index.php?controlador=sanciones&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&pagina='+valor_pagina+'&sentido=siguiente', 'contenidoAjaxPrincipal');
								break;
							case 'estados':
								valor_pagina = validar_limite_final_paginador();
								refrescar('abms/index.php?controlador=estados&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&pagina='+valor_pagina+'&sentido=siguiente', 'contenidoAjaxPrincipal');
								break;
							case 'antecedentes':
								valor_pagina = validar_limite_final_paginador();
								refrescar('abms/index.php?controlador=antecedentes&accion=listar&anio='+$('f_anio').value+'&tipo='+$('f_tipo').value+'&numero='+$('f_numero').value+'&cuerpo='+$('f_cuerpo').value+'&alcance='+$('f_alcance').value+'&pagina='+valor_pagina+'&sentido=siguiente', 'contenidoAjaxPrincipal');
								break;
						}
					}
				}
			}
			else
			{
				return;
			}
		}

		// SE DESMARCA LA FILA QUE SE ABANDONA
		filas_pulsar[fila_pulsar].style.background = '#fff';
		//filas_pulsar[fila_pulsar].style.color = '#000'; // 21/06/2018

		// SE RESTA O SE SUMA LA FILA
		fila_pulsar = eval(fila_pulsar + num);

		// SE MARCA LA FILA ACTUAL
		filas_pulsar[fila_pulsar].style.background = '#76A0CD';
		//filas_pulsar[fila_pulsar].style.color = '#000'; // 21/06/2018

		if ( $('controlador').value == 'expedientes' )
		{
			cargarBuscador($('i_anio'+fila_pulsar).innerHTML, $('i_tipo'+fila_pulsar).innerHTML, $('i_numero'+fila_pulsar).innerHTML, $('i_cuerpo'+fila_pulsar).innerHTML, $('i_alcance'+fila_pulsar).innerHTML);
			pedirDatos($('i_anio'+fila_pulsar).innerHTML, $('i_tipo'+fila_pulsar).innerHTML, $('i_numero'+fila_pulsar).innerHTML, $('i_cuerpo'+fila_pulsar).innerHTML, $('i_alcance'+fila_pulsar).innerHTML);
			cargarAgregado($('i_agregado_anio'+fila_pulsar).innerHTML, $('i_agregado_tipo'+fila_pulsar).innerHTML, $('i_agregado_numero'+fila_pulsar).innerHTML, $('i_agregado_cuerpo'+fila_pulsar).innerHTML, $('i_agregado_alcance'+fila_pulsar).innerHTML);
		}

		// SE SETEA LA FILA ELEGIDA
		$('nroFila_elegida').value = fila_pulsar;
	}
}

function validar_limite_inicial_paginador()
{
    var valor = 0;

    if ( parseInt($('pagina').value) > 1 )
    {
		valor = parseInt($('pagina').value)-parseInt(1);
    }
    else
    {
		valor = 1;
    }
    return valor;
}

function validar_limite_final_paginador()
{
    var valor = 0;

    if ( parseInt($('pagina').value) < parseInt($('nro_paginas').value)  )
    {
		valor = parseInt($('pagina').value)+parseInt(1);
    }
    else
    {
		valor = $('nro_paginas').value;
    }
    return valor;
}
/**********************************************************************************************
		PARA LA EDICION DE UN NUEVO EXPEDIENTE
**********************************************************************************************/
function habilitarLaCargaCombos()
{
    //se comento 22/02/10:$('btAgregarTema').setProperty('href', "javascript:refrescar('abms/index.php?controlador=expedientes&accion=agregarTema&anio='+$('anio').value+'&tipo='+$('tipo').value+'&numero='+$('numero').value+'&cuerpo='+$('cuerpo').value+'&alcance='+$('alcance').value+'&id_codtema='+$('id_codtema').value+'&id_usuario='+$('id_usuario').value+'', 'e_lista_temas');");
    $('btAgregarTema').setProperty('title', "Agregar el Tema al listado");
    $('imgBtAgregarTema').setProperty('src', 'imagenes/barra/add_16x16.gif');

    //se comento 03/03/10:$('btAgregarAutor').setProperty('href', "javascript:refrescar('abms/index.php?controlador=expedientes&accion=agregarAutor&anio='+$('anio').value+'&tipo='+$('tipo').value+'&numero='+$('numero').value+'&cuerpo='+$('cuerpo').value+'&alcance='+$('alcance').value+'&autor_tipo='+$('autor_tipo').value+'&autor_codigo='+$('autor_codigo').value+'&id_usuario='+$('id_usuario').value+'', 'e_lista_autores');");
    $('btAgregarAutor').setProperty('title', "Agregar el Autor al listado");
    $('imgBtAgregarAutor').setProperty('src', 'imagenes/barra/add_16x16.gif');
}
function deshabilitarLaCargaCombos()
{
    $('btAgregarTema').setProperty('href', "#");
    $('btAgregarTema').setProperty('title', "Antes debe ingresar A"+'\u00f1'+"o, Tipo, N"+'\u00fa'+"mero, Cuerpo y Alcance.");
    $('imgBtAgregarTema').setProperty('src', 'imagenes/barra/add_gris_16x16.gif');

    $('btAgregarAutor').setProperty('href', "#");
    $('btAgregarAutor').setProperty('title', "Antes debe ingresar A"+'\u00f1'+"o, Tipo, N"+'\u00fa'+"mero, Cuerpo y Alcance.");
    $('imgBtAgregarAutor').setProperty('src', 'imagenes/barra/add_gris_16x16.gif');
}
/************************************************************************************************************/
function setfocus(elemento)
{
    setTimeout(function(){
        $(elemento).focus();
        $(elemento).select();
    }, 100);
}

function obtenerValor_RadioButtonSeleccionado(radio)
{
    for (i=0;i<radio.length;i++)
        if (radio[i].checked) return radio[i].value;
}
/*******************************************************************************************************************
	METODOS PARA AGREGAR LOS TEMAS AL EXPEDIENTE
*******************************************************************************************************************/
// VERIFICA SI NO EXISTE UN TEMA PARA PODER AGREGARLO O NO
var noExiste = function(tema_noExiste)
{
    for (i=0; i< temas_del_expediente.length; i++)
    {
		if ( tema_noExiste.codigo_tema == temas_del_expediente[i].codigo_tema )
		{
			return false;
		}
    }
    return true;
}

// PARA AGREGAR UN TEMA A LA COLECCION temas_del_expediente
var agregarTema = function(nuevo_tema)
{
    if ( noExiste(nuevo_tema) )
    {
		temas_del_expediente[temas_del_expediente.length] = eval(nuevo_tema);
    }
}

// PARA VISUALIZAR LOS TEMAS
var mostrarTemas = function()
{
    var tema_pos;
    var mostrar = "";
    mostrar += "<table border='0' cellpadding='0' cellspacing='0' class='e_tabla_texto'>";
    mostrar += "<thead class='e_tabla_titulos'>";
    mostrar += "<tr>";
    mostrar += "<th class='orden_link'>&nbsp;</th>";
    mostrar += "<th class='orden_link'>C&oacute;digo</th>";
    mostrar += "<th class='orden_link'>Descripci&oacute;n</th>";
    mostrar += "</tr>";
    mostrar += "</thead>";
    mostrar += "<tbody id='e_cuerpo_scrolleable'>";

    for (tema_pos=0; tema_pos<temas_del_expediente.length; tema_pos++)
    {
		mostrar += "<tr>";
		mostrar +=   "<td style='width:2px;'>";
		mostrar +=   	"<a style='width:16px;height:16px;display:block;' href='javascript:if(confirm(\"Desea eliminar el Tema?\")){ borrar("+tema_pos+");mostrarTemas(); };'>";
		mostrar += 			"<img src='imagenes/b_drop.png' width='14' height='14' align='top' border=0 />";
		mostrar += 		"</a>";
		mostrar +=   "</td>";
		mostrar +=   "<td><input type='text' name='i_codigo_tema[]' id='lista_temas_codigo_tema"+tema_pos+"' value='"+temas_del_expediente[tema_pos].codigo_tema+"' style='width:29px;height:17px;' readonly='readonly' ></td>";
		mostrar +=   "<td><input type='text' name='i_descripcion_tema[]' value='"+temas_del_expediente[tema_pos].descripcion_tema+"' style='width:250px;height:17px;' readonly='readonly' ></td>";
		mostrar += "</tr>";
		mostrar += "<input type='hidden' name='i_id_codtema[]' value='"+temas_del_expediente[tema_pos].id_codtema+"' >";
    }
    mostrar += "</tbody>";
    mostrar += "<input type='hidden' name='contador_temas' value='"+tema_pos+"' >";
    mostrar += "</table>";

    $('e_lista_temas').setHTML(mostrar);
    $('codigo_tema').value = "";
    $('descripcion_tema').value = "";
}

var borrar = function(indice)
{
    // SE EXTRAE, DESDE EL ELEMENTO SIGUIENTE AL QUE SE DESEA QUITAR, HASTA EL FINAL DE LA COLECCION
    provisorio = temas_del_expediente.slice(indice+1);

    // SE EXTRAE DESDE EL PRIMERO HASTA EL ANTERIOR AL INDICE, PARA QUITAR EL ELEMENTO DE DICHO INDICE
    temas_del_expediente = temas_del_expediente.slice(0,indice);

    // SE CONCATENAN LAS DOS EXTRACCIONES PARA ARMAR LA COLECCION
    temas_del_expediente = temas_del_expediente.concat(provisorio);
}

var cargarArrayTemasJS = function(anio, tipo, numero, cuerpo, alcance, id_codtema, codigo_tema, descripcion_tema)
{
    // SI SE RECIBEN LOS DATOS
    if ( codigo_tema != '' && descripcion_tema != '' )
    {
		// SE DEFINE CON JSON EL TEMA A AGREGAR
		nuevo_tema = {
					  "anio":anio,
					  "tipo":tipo,
					  "numero":numero,
					  "cuerpo":cuerpo,
					  "alcance":alcance,
					  "id_codtema":id_codtema,
					  "codigo_tema":codigo_tema,
					  "descripcion_tema":descripcion_tema
					 };

		// SE AGREGA AL LISTADO DE TEMAS
		agregarTema(nuevo_tema);
    }
    // SE MUESTRA EL LISTADO DE TEMAS
    mostrarTemas();
}
/*******************************************************************************************************************
	METODOS PARA AGREGAR LOS AUTORES AL EXPEDIENTE
*******************************************************************************************************************/
// VERIFICA SI NO EXISTE UN AUTOR PARA PODER AGREGARLO O NO
var noExisteAutor = function(autor)
{
    for (i=0; i< autores_del_expediente.length; i++)
    {
		if ( autor.autor_codigo == autores_del_expediente[i].autor_codigo )
		{
			return false;
		}
    }
    return true;
}

// PARA AGREGAR UN Autor A LA COLECCION autores_del_expediente
var agregarAutor = function(nuevo_autor)
{
    if ( noExisteAutor(nuevo_autor) )
    {
	    autores_del_expediente[autores_del_expediente.length] = eval(nuevo_autor);
    }
}

// PARA VISUALIZAR LOS AUTORES
var mostrarAutores = function()
{
    var aut;
    var mostrarA = "";
    mostrarA += "<table border='0' cellpadding='0' cellspacing='0' class='e_tabla_texto'>";
    mostrarA += "<thead class='e_tabla_titulos'>";
    mostrarA += "<tr>";
    mostrarA += "<th class='orden_link'>&nbsp;</th>";
    mostrarA += "<th class='orden_link'>Grupo</th>";
    mostrarA += "<th class='orden_link'>C&oacute;digo</th>";
    mostrarA += "<th class='orden_link'>Descripci&oacute;n</th>";
    mostrarA += "</tr>";
    mostrarA += "</thead>";
    mostrarA += "<tbody id='e_cuerpo_scrolleable'>";

    for (aut=0; aut < autores_del_expediente.length; aut++)
    {
		mostrarA += "<tr>";
		mostrarA +=   "<td style='width:2px;'>";
		mostrarA += 	"<a style='width:16px;height:16px;display:block;' href='javascript:if(confirm(\"Desea eliminar el Autor?\")){ borrarAutor("+aut+");mostrarAutores(); };'>";
		mostrarA += 		"<img src='imagenes/b_drop.png' width='14' height='14' align='top' border=0 />";
		mostrarA += 	"</a>";
		mostrarA +=   "</td>";
		mostrarA +=   "<td><input type='text' name='i_autor_tipo[]' value='"+autores_del_expediente[aut].autor_tipo+"' style='width:47px;height:17px;' readonly='readonly'></td>";
		mostrarA +=   "<td><input type='text' name='i_autor_codigo[]' id='lista_autor_autor_codigo"+aut+"' value='"+autores_del_expediente[aut].autor_codigo+"' style='width:29px;height:17px;' readonly='readonly' ></td>";
		mostrarA +=   "<td><input type='text' name='i_autor_descripcion[]' value='"+autores_del_expediente[aut].autor_descripcion+"' style='width:260px;height:17px;' readonly='readonly' ></td>";
		mostrarA += "</tr>";
    }
    mostrarA += "</tbody>";
    mostrarA += "<input type='hidden' name='contador_autores' value='"+aut+"' >";
    mostrarA += "</table>";

    $('e_lista_autores').setHTML(mostrarA);
    $('autor_codigo').value = "";
    $('autor_descripcion').value = "";
}

var borrarAutor = function(indice)
{
    // SE EXTRAE, DESDE EL ELEMENTO SIGUIENTE AL QUE SE DESEA QUITAR, HASTA EL FINAL DE LA COLECCION
    provisorio = autores_del_expediente.slice(indice+1);

    // SE EXTRAE DESDE EL PRIMERO HASTA EL ANTERIOR AL INDICE, PARA QUITAR EL ELEMENTO DE DICHO INDICE
    autores_del_expediente = autores_del_expediente.slice(0,indice);

    // SE CONCATENAN LAS DOS EXTRACCIONES PARA ARMAR LA COLECCION
    autores_del_expediente = autores_del_expediente.concat(provisorio);
}

var cargarArrayAutoresJS = function(anio, tipo, numero, cuerpo, alcance, autor_tipo, autor_codigo, autor_descripcion)
{
	// Se convierte a mayúscula el Tipo de Autor
	autor_tipo = autor_tipo.toUpperCase();

	//alert(anio+'<br>'+tipo+'<br>'+numero+'<br>'+cuerpo+'<br>'+alcance+'<br>'+autor_tipo+'<br>'+autor_codigo+'<br>'+autor_descripcion);

	// SI SE RECIBEN LOS DATOS
    if (autor_codigo != '' && autor_descripcion != '')
    {
	    // SE DEFINE CON JSON EL AUTOR A AGREGAR
	    nuevo_autor = {
					   "anio":anio,
					   "tipo":tipo,
					   "numero":numero,
					   "cuerpo":cuerpo,
					   "alcance":alcance,
					   "autor_tipo":autor_tipo,
					   "autor_codigo":autor_codigo,
					   "autor_descripcion":autor_descripcion
					  };

	    // SE AGREGA AL LISTADO autores_del_expediente
	    agregarAutor(nuevo_autor);
    }
    // SE MUESTRA EL LISTADO DE AUTORES
    mostrarAutores();
}
/*****************************************************************************************
		SE VALIDA EL INGRESO DE DATOS EN EL ABM DE Expedientes
*****************************************************************************************/
function validarExpediente()
{
    var mensaje = "";
    var error = false;

    if ( $('anio').value == '' )
    {
		mensaje += "Debe ingresar un A"+'\u00f1'+"o.<br>";
		error = true;
    }

    if ( $('iniciador_codigo').value == '' )
    {
		mensaje += "Debe ingresar un Iniciador.<br>";
		error = true;
	}

    if ($('id_codcategoria').value == '')
    {
		mensaje += "Debe ingresar una Categor"+'\u00ed'+"a.<br>";
		error = true;
	}

    if ( $('fecha_entrada_expe').value == '' )
    {
		mensaje += "Debe ingresar la Fecha de Entrada.<br>";
		error = true;
	}

    if ( $('lista_temas_codigo_tema0') )
    {
		// DEBE INGRESAR POR LO MENOS UN TEMA
		if ( $('lista_temas_codigo_tema0').value == '' )
		{
			mensaje += "Debe ingresar un Tema.<br>";
			error = true;
		}
    }
    else
    {
		mensaje += "Debe ingresar un Tema.<br>";
		error = true;
	}

    if ( $('lista_autor_autor_codigo0') )
    {
		// DEBE INGRESAR POR LO MENOS UN AUTOR
		if ( $('lista_autor_autor_codigo0').value == '' )
		{
			mensaje += "Debe ingresar un Autor.<br>";
			error = true;
		}
    }
    else
    {
		mensaje += "Debe ingresar un Autor.<br>";
		error = true;
	}

    if (error)
    {
		alert(mensaje);
    }
    else
    {
		// 03/01/2012 SE VACÍAN LOS VECTORES TEMPORALES DE Temas Y Autores
		temas_del_expediente.splice(0);
		autores_del_expediente.splice(0);

		enviarForm('formExpedientes', 'abms', 'contenidoAjaxPrincipal');
    }
}
/*************************************************************************************************
	MASCARA DE ENTRADA EN LOS CAMPOS DE TEXTO PARA:
		FECHA (DD/MM/YYYY)
		HORA (HH:MM)

	Parámetros: d: valor; sep: caracter separador; pat: patron; nums
**************************************************************************************************/
function mascara(d,sep,pat,nums)
{
	if( d.valor_anterior != d.value )
	{
		val = d.value;
		largo = val.length;
		val = val.split(sep);
		val2 = '';

		for( r=0;r<val.length;r++ )
		{
			val2 += val[r];
		}

		if(nums)
		{
			for( z=0;z<val2.length;z++ )
			{
				if( isNaN(val2.charAt(z)) )
				{
					letra = new RegExp(val2.charAt(z),"g");
					val2 = val2.replace(letra,"");
				}
			}
		}

		val = '';
		val3 = new Array();
		for( s=0; s<pat.length; s++ )
		{
			val3[s] = val2.substring(0,pat[s]);
			val2 = val2.substr(pat[s]);
		}

		for( q=0;q<val3.length; q++ )
		{
			if(q ==0){
				val = val3[q];
			}
			else{
				if(val3[q] != ""){
					val += sep + val3[q];
				}
			}
		}
		d.value = val;
		d.valor_anterior = val;
	}
}
/********************************************************************
	VERIFICA SI LA FECHA1 ES MAYOR A LA FECHA2
**********************************************************************/
function esLaFechaMayor(fecha1, fecha2)
{
	var xDia = fecha1.substring(0, 2);
	var xMes = fecha1.substring(3, 5);
	var xAnio = fecha1.substring(6,10);

	var yDia = fecha2.substring(0, 2);
	var yMes = fecha2.substring(3, 5);
	var yAnio = fecha2.substring(6,10);

	if (xAnio > yAnio)
	{
	  return(true);
	}
	else
	{
		if (xAnio == yAnio)
		{
			if (xMes > yMes)
			{
				return(true);
			}
			else
			{
				if (xMes == yMes)
				{
					if (xDia > yDia)
						return(true);
					else
						return(false);
				}
				else
				{
					return(false);
				}
			}
		}
		else
		{
			return(false);
		}
	}
}

function compararFechas(fecha1, fecha2)
{
	return fecha1.getTime() - fecha2.getTime();
}
/*******************************************************************
	DEVUELVE LOS DIAS TRANSCURRIDOS ENTRE DOS FECHAS
*******************************************************************/
function obtenerDiasEntreFechas(fecha1,fecha2)
{
	var aFecha1 = fecha1.split('/');
	var aFecha2 = fecha2.split('/');

	var fFecha1 = Date.UTC(aFecha1[2], aFecha1[1]-1, aFecha1[0]);
	var fFecha2 = Date.UTC(aFecha2[2], aFecha2[1]-1, aFecha2[0]);

	var dif = fFecha2 - fFecha1;

	var dias = Math.floor(dif / (1000 * 60 * 60 * 24));

	return dias;
}
/*****************************************************************************************
	VERIFICA SI LA DIFERENCIA ENTRE DOS FECHAS NO SUPERA UNA CANTIDAD DE AÑOS DETERMINADA
******************************************************************************************/
function verificarDiferenciaAnios(fecha_desde, fecha_hasta, cantidad_anios)
{
	// SE OBTIENE LA DIFERENCIA EN DIAS
	var diferencia_dias = obtenerDiasEntreFechas(fecha_desde, fecha_hasta);

	// SI LA DIFERENCIA ENTRE ELLAS ES MAYOR A UNA CANTIDAD DETERMINADA
	if ( diferencia_dias > (cantidad_anios * 365) )
	{
		return false;
	}

	return true;
}

function bajarScrollListados(div)
{
	document.getElementById(div).scrollTop +=9999999;
}

function AutoSuggest(elem, elementos_posibles, combo)
{
	var me = this;

	this.elem = elem;
	this.elementos_posibles = elementos_posibles;
	this.elegible = new Array();
	this.textoEntrada = null;
	this.destacado = -1;
	this.div = $("sugerencias");

	var TAB = 9;
	var ESC = 27;
	var KEYUP = 38;
	var KEYDN = 40;

	elem.setAttribute("autocomplete","off");

	if(!elem.id)
	{
		var id = "sugerencias" + idContador;
		idContador++;
		elem.id = id;
	}

	elem.onload = function()
	{
		me.crearDiv();
	}

	elem.onkeydown = function(ev)
	{
		var key = me.getKeyCode(ev);
		switch(key)
		{
			case TAB:
			me.usarSugerencia();
			break;

			case ESC:
			me.ocultarDiv();
			break;

			case KEYUP:
			if (me.destacado > 0){
				me.destacado--;
			}
			me.cambiarResaltado(key);
			break;

			case KEYDN:
			if (me.destacado < (me.elegible.length - 1)){
				me.destacado++;
			}
			me.cambiarResaltado(key);
			break;

		}
	};

	elem.onkeyup = function(ev)
	{
		var key = me.getKeyCode(ev);

		switch(key)
		{
			case TAB:
			case ESC:
			case KEYUP:
			case KEYDN:
			return;
			default:

				if(this.value != me.textoEntrada && this.value.length > 0)
				{
					me.textoEntrada = this.value;

					me.getEligible();
					me.crearDiv();
					me.posicionarDiv();
					me.mostrarDiv();
				}else{
					me.ocultarDiv();
				}
		}
	};

	this.usarSugerencia = function()
	{
		if(this.destacado > -1)
		{
			this.elem.value = this.elegible[this.destacado];

			this.ocultarDiv();
			setTimeout("document.getElementById('" + this.elem.id + "').focus()",0);
		}
	};

	this.mostrarDiv = function()
	{
		this.div.style.display = 'block';
	};

	this.ocultarDiv = function()
	{
		this.div.style.display = 'none';
		this.destacado = -1;
	};

	this.cambiarResaltado = function()
	{
		var lis = this.div.getElementsByTagName('LI');
		for(i in lis)
		{
			var li = lis[i];
			if(this.destacado == i){
				li.className = "selected";
			}else{
				li.className = "";
			}
		}
	};

	this.posicionarDiv = function()
	{
		this.div.style.left = '61px';
		this.div.style.top = '40px';
		$('sugerencias').setStyle('width', '330px');
		$('sugerencias').setStyle('height', '100px');
		$('sugerencias').setStyle('font-size', '12px');
		$('sugerencias').setStyle('overflow-x', 'hidden');
		$('sugerencias').setStyle('overflow-y', 'auto');
	};

	this.crearDiv = function()
	{
		var ul = document.createElement('ul');
		for(i=0;i<this.elegible.length;i++)
		{
			var word = this.elegible[i];
			var li = document.createElement('li');
			var a = document.createElement('a');
			a.href="javascript:false";
			a.innerHTML = word;
			li.appendChild(a);

			if(me.destacado == i){
				li.className = "selected";
			}
			ul.appendChild(li);
		}

		this.div.replaceChild(ul,this.div.childNodes[0]);

		ul.onmouseover = function(ev){
			var target = me.getEventSource(ev);
			while (target.parentNode && target.tagName.toUpperCase() != 'LI'){
				target = target.parentNode;
			}
			var lis = me.div.getElementsByTagName('LI');

			for (i in lis){
				var li = lis[i];
				if(li == target){
					me.destacado = i;
					break;
				}
			}
			me.cambiarResaltado();
		};
		ul.onclick = function(ev){
			me.usarSugerencia();
			me.ocultarDiv();
			me.cancelarEvento(ev);
			if ( combo == 'c_categoria' || combo == 'c_tema' || combo == 'c_estado')
			{
				validarNombreModal(combo);
			}
			else
			{
				if ( combo == 'c_iniciado' || combo == 'c_autor' || combo == 'c_comision' || combo == 'solicitante' )
				{
					validarNombreClaveDobleModal(combo);
				}
			}
			return false;
		};
		this.div.className="suggestion_list";
		this.div.style.position = 'absolute';
	};

	this.getEligible = function()
	{
		var i_modal;
		this.elegible = new Array();
		for(i_modal=0; i_modal < this.elementos_posibles.length; i_modal++)
		{
			var sugerido = this.elementos_posibles[i_modal];
			if(sugerido.toLowerCase().indexOf(this.textoEntrada.toLowerCase()) != "-1")
			{
				this.elegible[this.elegible.length]=sugerido;
			}
		}
	};

	this.getKeyCode = function(ev)
	{
		if(ev){
			return ev.keyCode;
		}
		if(window.event){
			return window.event.keyCode;
		}
	};

	this.getEventSource = function(ev)
	{
		if(ev){
			return ev.target;
		}
		if(window.event){
			return window.event.srcElement;
		}
	};

	this.cancelarEvento = function(ev)
	{
		if(ev){
			ev.preventDefault();
			ev.stopPropagation();
		}
		if(window.event){
			window.event.returnValue = false;
		}
	}

}

function validarNombreModal(combo)
{
	if($('nombre_sugerido').value == '')
	{
		alert('Debe ingresar un nombre sugerido.');
	}
	else
	{
		// SE SEPARA EL CODIGO Y LA DESCRIPCION DE LO ELEGIDO
		var valor_elegido = $('nombre_sugerido').value.split(', ');
		codigo = valor_elegido[0];
		descripcion = valor_elegido[1];

		$(combo).value = codigo;

		ventana_modal = "no";

		// SE CIERRA LA VENTANA MODAL
		$('light').setStyle('display', 'none');
		$('fade').setStyle('display', 'none');

		if ( combo == 'c_estado' )
		{
			$('c_comision').disabled = true;
			$('imagen_zoom_comisiones').setStyle('display', 'none');
		}
	}
}

function validarNombreClaveDobleModal(combo)
{
	if($('nombre_sugerido').value == '')
	{
		alert('Debe ingresar un nombre sugerido.');
	}
	else
	{
		// SE SEPARA EL TIPO, EL CODIGO Y LA DESCRIPCION DE LO ELEGIDO
		var valor_elegido = $('nombre_sugerido').value.split(', ');
		tipo = valor_elegido[0];
		codigo = valor_elegido[1];
		descripcion = valor_elegido[2];

		$(combo).value = tipo+'-'+codigo;

		ventana_modal = "no";

		// SE CIERRA LA VENTANA MODAL
		$('light').setStyle('display', 'none');
		$('fade').setStyle('display', 'none');

		if ( combo == 'c_comision' )
		{
			$('c_estado').disabled = true;
			$('imagen_zoom_estados').setStyle('display', 'none');
		}
	}
}

function cargarModal(url, capa_destino)
{
	//alert('url: '+url+'\nDestino: '+capa_destino);
    var miAjax = new Ajax(url,
    {
		method: 'get',
		data:'',
		evalScripts:true,
		update: $(capa_destino),
		onFailure: function(xhr)
		{
		    redireccionar();
		}
    });
    miAjax.request();
}

function modalGaby(url)
{
	$('light').setStyle('display', 'block');
	$('fade').setStyle('display', 'block');
	cargarModal(url, 'light');
}

function modalGabyFicha(url)
{
	$('light').setStyle('width', '790px');
	$('light').setStyle('height', '500px');
	$('light').setStyle('top', '1%');
	$('light').setStyle('left', '1%');

	$('light').setStyle('display', 'block');
	$('fade').setStyle('display', 'block');

	cargarModal(url, 'light');
}

function cerrarModalPedirNombre()
{
	// SE SETEA EL ANCHO Y ALTO DE LA MODAL CON LOS VALORES POR DEFECTO
	$('light').setStyle('width', '400px');
	$('light').setStyle('height', '120px');
	$('light').setStyle('top', '25%');
	$('light').setStyle('left', '35%');

	// SE CIERRA LA VENTANA MODAL
	$('light').setStyle('display', 'none');
	$('fade').setStyle('display', 'none');
}

function fecha(cadena)
{
   // Separador para la introduccion de las fechas
   var separador = "/";

   // Separa por dia, mes y año
   if ( cadena.indexOf( separador ) != -1 )
   {
        var posi1 = 0;
        var posi2 = cadena.indexOf( separador, posi1 + 1 );
        var posi3 = cadena.indexOf( separador, posi2 + 1 );

        this.dia = cadena.substring( posi1, posi2 );
        this.mes = cadena.substring( posi2 + 1, posi3 );
        this.anio = cadena.substring( posi3 + 1, cadena.length );
   }
   else
   {
        this.dia = 0;
        this.mes = 0;
        this.anio = 0;
   }
}
/********************************************************************
	VERIFICA SI LA FECHA ES MAYOR O NO A LA ACTUAL
**********************************************************************/
function esMayorAFechaActual(fecha)
{
    // SE PREPARA LA FECHA ACTUAL
    var hoy = new Date();
    var mes = (hoy.getMonth()+1);
    var dia = hoy.getDate();
    var anio = hoy.getFullYear();

    if (mes < 10) { mes = ("0" + mes).slice(-2); }
    if (dia < 10) { dia = ("0" + dia).slice(-2); }

    var fecha_actual = dia+"/"+mes+"/"+anio;

	if ( esLaFechaMayor(fecha, fecha_actual) )
	{
		//alert(fecha+" es mayor a "+fecha_actual);
		return(true);
	}
	else
	{
		return(false);
	}
}

function marcarTodosCheckbox(formulario)
{
    for (i=0;i< document.forms[formulario].elements.length;i++)
    {
        if ( document.forms[formulario].elements[i].type == "checkbox" )
        {
			document.forms[formulario].elements[i].checked = 1;
		}
	}
}

function desmarcarTodosCheckbox(formulario)
{
    for (i=0;i< document.forms[formulario].elements.length;i++)
    {
        if ( document.forms[formulario].elements[i].type == "checkbox" )
        {
			document.forms[formulario].elements[i].checked = 0;
		}
	}
}

function verificarCheckbox(formulario)
{
    // SE RECORREN LOS ELEMENTOS DEL FORMULARIO
    for (i=0; i< document.forms[formulario].elements.length; i++)
    {
        // SI EL ELEMENTO ES UN CHECKBOX
        if ( document.forms[formulario].elements[i].type == "checkbox" )
        {
			// SI ESTA SELECCIONADO
			if ( document.forms[formulario].elements[i].checked )
			{
				return true;
			}
		}
	}
	return false;
}

function esIE()
{
    return esIEObsoleto() || esIE11();
}

function esIEObsoleto()
{
    return !!(navigator.userAgent.match("/MSIE/"));
}

function esIE11()
{
    return !!(
        navigator.userAgent.match("/Trident/")
        && navigator.userAgent.match("/rv:11/")
    );
}

function ocultarElemento(id, milisegundos)
{
    setTimeout(function(){
        new Fx.Styles(id).start({'opacity': ['1', '0']});
    }, milisegundos);
}

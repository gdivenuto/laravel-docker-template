/****************************************************************************************
		VARIABLES GENERALES JAVASCRIPT DEL SISTEMA 
****************************************************************************************/
var tiempoInicial;//PARA LA HORA EN EL PIE

// PARA LOS LISTADOS
var fila_pulsar = 10;

// PARA ENMASCARAR LAS FECHAS
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);

// PARA SABER SI SE ESTA UTILIZANDO EL BUSCADOR DE UN LISTADO
var se_busca = false;
/****************************************************************************************
		FUNCIONES GENERALES JAVASCRIPT DEL SISTEMA 
****************************************************************************************/
function esMailValido(email) {
    var s = email;
    var patron_para_mail = /^[A-Za-z][A-Za-z0-9_]*@[A-Za-z0-9_]+\.[A-Za-z0-9_.]+[A-za-z]$/; 
    
    if (s.length == 0 )
        return false;
    if (patron_para_mail.test(s))
        return true;
    else
        return false;
}

function esIgualA( s ) {
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
            // SI NO fue actualizado el tiempo, por haber caducado la sesión
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

    if (capa_destino != 'capaAjaxFichaLog' && capa_destino != 'capaAjaxEdicion') {
        if (capa_destino == 'contenidoAjaxPrincipal')
            $('precarga_principal').setStyle('display', 'block');
        else {
            if ( capa_destino != 'capaVentana' )
                if ($('precarga_modal'))
                    $('precarga_modal').setStyle('display', 'block');
        }
    }
    
    $(capa_destino).setStyle('display', 'none');
    
    var miAjax = new Ajax(url, {
        method: 'get',
        data:'',
        evalScripts:true,
        update: $(capa_destino),
        onFailure: function(xhr) {
            redireccionar();
        },
        onComplete: function() {
            if (capa_destino != 'capaAjaxFichaLog' && capa_destino != 'capaAjaxEdicion') {
                if (capa_destino == 'contenidoAjaxPrincipal')
                    $('precarga_principal').setStyle('display', 'none');
                else {
                    if ( capa_destino != 'capaVentana' )
                        if ($('precarga_modal'))
                            $('precarga_modal').setStyle('display', 'none');
                }
            }   
            $(capa_destino).setStyle('display', 'block');
        }
    });
    
    miAjax.request();
}

//	SE ENVIA EL FORMULARIO CON AJAX
function enviarForm(form, carpeta, destino) {
    // Antes de enviar el formulario, se actualiza el tiempo de la sesión
    actualizarTiempoSession();

    if ( destino == 'contenidoAjaxPrincipal' )
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
			if ( destino == 'contenidoAjaxPrincipal' )
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

// SE ENVIA EL FORMULARIO CON AJAX
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

function ordenarColumna(campo, controlador)
{
    // PARA BUSCAR LUEGO POR EL CAMPO EN LA TABLA ESPECIFICADA
    $('campo_orden').value = campo;
    // SE ORDENA POR EL CAMPO ESPECIFICADO
    refrescar('abms/index.php?controlador='+controlador+'&accion=listar&campo_orden='+campo+'', 'contenidoAjaxPrincipal');//&pagina='+$('pagina').value+'
}

function ordenarColumnaLegajos(campo, controlador, pagina, id_area, id_cargo, concejal)
{
    // PARA BUSCAR LUEGO POR EL CAMPO EN LA TABLA ESPECIFICADA
    $('campo_orden').value = campo;
    
    // SE ORDENA POR EL CAMPO ESPECIFICADO
    refrescar('abms/index.php?controlador='+controlador+'&accion=listar&campo_orden='+campo+'&pagina='+pagina+'&cmb_area='+id_area+'&cmb_cargo='+id_cargo+'&cmb_concejal='+concejal+'', 'contenidoAjaxPrincipal');
}

function marcar(titulo)
{
    $(titulo).setStyle('color','blue');	
}

//	SE REFRESCA EL LISTADO DE AREAS
function buscarCodArea()
{
    var url = 'abms/index.php?controlador=codareas&accion=listar&f_codigo='+$('f_codigo').value+'&f_nombre='+$('f_nombre').value+'';
    
    refrescar(url, 'contenidoAjaxPrincipal');
}

//	SE REFRESCA EL LISTADO DE CARGOS
function buscarCodCargo()
{
    var url = 'abms/index.php?controlador=codcargos&accion=listar&f_nomenclador='+$('f_nomenclador').value+'&f_nombre='+$('f_nombre').value+'';
    
    refrescar(url, 'contenidoAjaxPrincipal');
}

//	SE REFRESCA EL LISTADO CON EL RESULTADO DEVUELTO SEGUN EL VALOR A FILTRAR EN EL CAMPO ESPECIFICADO
function buscarLegajo()
{
    var url = 'abms/index.php?controlador=personal&accion=listar&cmb_area='+$('cmb_area').value+'&cmb_cargo='+$('cmb_cargo').value+'&cmb_concejal='+$('cmb_concejal').value+'&f_legajo='+$('f_legajo').value+'&f_apellido_y_nombre='+$('f_apellido_y_nombre').value+'&f_activos='+$('f_activos').value;
    
    refrescar(url, 'contenidoAjaxPrincipal');
}

//	SE ESTABLECE EL VALOR DE ACTIVADO O DESACTIVADO AL CHECKBOX
function chequear(nombre_campo)
{
    if ( $('habilitado').checked == true )
    {
	    $(nombre_campo).value = 1;
    }
    else
    {
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

function soloLetras(e)
{
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = " áéíóúabcdefghijklmnñopqrstuvwxyz";
    especiales = [8,9,13,37,39,44,46];

    tecla_especial = false;
    for(var i in especiales)
    {
		if ( key == especiales[i] )
		{
			tecla_especial = true;
			break;
        } 
    }
    
	// SI NO ES UNA LETRA O UN CARACTER PERMITIDO NO LA PERMITE UTILIZAR 
    if( letras.indexOf(tecla)==-1 && !tecla_especial )
        return false;
}

function soloEnteros(e)
{
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = "0123456789";
    especiales = [8,9,13,37,39,44,46];

    tecla_especial = false;
    for(var i in especiales)
    {
		if ( key == especiales[i] )
		{
			tecla_especial = true;
			break;
        } 
    }
    // Si no es un número entero o un carácter permitido NO la permite utilizar
    if( letras.indexOf(tecla)==-1 && !tecla_especial )
        return false;
}

function soloEnterosLetras(e)
{
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = "0123456789áéíóúabcdefghijklmnñopqrstuvwxyz";
    especiales = [8,9,13,37,39,44,46];

    tecla_especial = false;
    for(var i in especiales)
    {
		if ( key == especiales[i] )
		{
			tecla_especial = true;
			break;
        } 
    }
 
    if( letras.indexOf(tecla)==-1 && !tecla_especial )
        return false;
}

function soloEnterosLetrasComilla(e)
{
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = "0123456789áéíóúabcdefghijklmnñopqrstuvwxyz'";
    especiales = [8,9,13,32,37,39,44,46];

    tecla_especial = false;
    for(var i in especiales)
    {
        if ( key == especiales[i] )
        {
            tecla_especial = true;
            break;
        } 
    }
 
    if( letras.indexOf(tecla)==-1 && !tecla_especial )
        return false;
}

function soloEnterosLetras_y_Guion(e)
{
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = "0123456789áéíóúabcdefghijklmnñopqrstuvwxyz-";
    especiales = [8,9,13,37,39,44,46];

    tecla_especial = false;
    for(var i in especiales)
    {
		if ( key == especiales[i] )
		{
			tecla_especial = true;
			break;
        } 
    }
 
    if(letras.indexOf(tecla)==-1 && !tecla_especial)
        return false;
}

function solo_enteros_y_guion(e)
{
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = "0123456789-";
    especiales = [8,9,13,37,39,44,46];

    tecla_especial = false;
    for(var i in especiales)
    {
		if ( key == especiales[i] )
		{
			tecla_especial = true;
			break;
        } 
    }
 
    if(letras.indexOf(tecla)==-1 && !tecla_especial)
        return false;
}

function limpiaCopyPasteNumeros(id_campo)
{
    var valor = document.getElementById(id_campo).value;
    var tamanio = valor.length;
    
    for(i=0; i < tamanio; i++)
    {
		if( !isNaN(valor[i]) )
		{
			document.getElementById(id_campo).value = '';
		}
    }
}

function obtenerAnioActual() 
{  
    var fecha_actual = new Date();
    var anio = fecha_actual.getFullYear();
    
    return anio;
}

// Se obliga al usuario que ingrese un anio mayor ó igual a 1983, y menor ó igual al actual
function respetar_anio(anio)  
{ 
    var anio_actual = obtenerAnioActual();
    
    if (anio.value.length == 4)
    {
		if ( anio.value >= '1983' && anio.value <= anio_actual )
		{ 
			return true;
		}
		else
		{
			alert("Ingrese un a"+'\u00f1'+"o v"+'\u00e1'+"lido, comprendido entre 1983 y el a"+'\u00f1'+"o actual!");
			anio.value = '';
			anio.focus();
		}
    }	
} 
/***************************************************************************************
	PARA LAS VENTANAS MODALES
***************************************************************************************/	
function volverModal(campoOculto, campo1, campo2, oculto, codigo, descripcion)
{
    if (campoOculto != '' && oculto != ''){
	    $(campoOculto).value = oculto;//GRALMENTE. ES EL id PARA LA REFERENCIA EN LA BD
    }
    $(campo1).value = codigo;
    $(campo2).value = descripcion;
    
    $('mb_close_link').onclick();// SE CIERRA LA VENTANA MODAL
    mostrarMenuPpal();
}	
function volverModaldiv(campoOculto, campo1, campo2, oculto, codigo, descripcion)
{
    if ( campoOculto != '' && oculto != '' )
    {
	    $(campoOculto).value = oculto;//GRALMENTE. ES EL id PARA LA REFERENCIA EN LA BD
    }
    
    $(campo1).value = codigo;
    
    if ( campo2 == 'valor_iniciador_descripcion' )
    {
		$(campo2).setHTML(descripcion);
    }
    else
    {
		$(campo2).setHTML(descripcion.substring(0, 17)+' ...');
    }
    
    $('mb_close_link').onclick();// SE CIERRA LA VENTANA MODAL
    
    mostrarMenuPpal();
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
    mostrarMenuPpal();
}	
function volverModalDependiente(campo1, campo2, legajo, apellido, nombre)// ex volverModalDigito
{
    $(campo1).value = legajo;
    $(campo2).value = apellido+', '+nombre;
    
    $('mb_close_link').onclick();// SE CIERRA LA VENTANA MODAL
    //mostrarMenuPpal();
}
function volverModalCodArea(campo1, campo2, id, nombre)
{
    $(campo1).value = id;
    $(campo2).value = nombre;
    
    $('mb_close_link').onclick();// SE CIERRA LA VENTANA MODAL
}	
/***********************************************************************************************
	PARA NO ELEGIR UNA FECHA ANTERIOR A 01/01/1983
************************************************************************************************/
function fecha_valida(fecha)
{
    if (fecha == '')
    {
	    valida = false;
    }else{	
	    // SE SEPARA EL AÑO, MES Y DÍA DE LAS FECHAS
	    var fecha_a_evaluar = fecha.split("/"); 
	    // SE TOMA EL AÑO, MES Y DÍA DE LA FECHA 
	    anio = fecha_a_evaluar[2];
	    mes = fecha_a_evaluar[1];
	    dia = fecha_a_evaluar[0];
	    
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
/********************************************************************************************
		SE RESALTA LA FILA AL POSICIONARSE SOBRE ELLA
********************************************************************************************/
// 	SE RECIBE EL IDENTIFICADOR (id) DE LA FILA 
function resaltarFila(nroFila)
{
    if (nroFila != $('nroFila_elegida').value){
	    $('e_fila'+nroFila+'').style.background = '#C8CEDC';
    }
}
// 	SE RECIBE EL IDENTIFICADOR (id) DE LA FILA 
function no_resaltarFila(nroFila)
{
    if (nroFila != $('nroFila_elegida').value){
	    $('e_fila'+nroFila+'').style.background = '#fff';
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
    for (i = 0; i < filas_marcarFila.length; i++){
	    filas_marcarFila[i].style.background = '#fff';
	    filas_marcarFila[i].style.color = '#000';
    }	
    
    //SE MARCA LA FILA DETERMINADA
    $('e_fila'+nroFila+'').style.background = '#76A0CD';
    $('e_fila'+nroFila+'').style.color = '#fff';
}

//	SE RECIBE EL IDENTIFICADOR (id) DE LA FILA Y UNA BANDERA SI ESTA HABILITADO O NO
function remarcarFila(nroFila)
{
	$('nroFila_elegida').value = nroFila;
	
	// SE OBTIENEN TODAS LOS FILAS DEL LISTADO PARA DESMARCAR
	filas_marcarFila = $$('#e_cuerpo_scrolleable tr');
	
	// SE DESMARCAN TODAS LAS FILAS
	for (i = 0; i < filas_marcarFila.length; i++)
	{
		// SI ESTÁ HABILITADO EL REGISTRO
		if ( $('bandera_habilitado'+i).value == '1' ) 
		{
			filas_marcarFila[i].style.background = '#ffffff';
			filas_marcarFila[i].style.color = '#000000';
		}
		else
		{
			filas_marcarFila[i].style.background = '#ffffff';
			filas_marcarFila[i].style.color = '#A6ABAB';
		}
	}
	
	// SE MARCA LA FILA DETERMINADA
	$('e_fila'+nroFila+'').style.background = '#76A0CD';
	$('e_fila'+nroFila+'').style.color = '#ffffff';
}
/********************************************************************************************
		SE LIMPIA UN VALOR DE UN CARACTER DETERMINADO
********************************************************************************************/
function limpiarValor(valor, caracter)
{
	// SE SEPARA POR EL CARACTER ESPECIFICADO 
	var partes = valor.split(caracter);
	
	// SE ARMA EL VALOR
	var valor_limpio = '';
	var i;
	//alert(partes.length-1);
	for(i=0; i < partes.length; i++)
	{
		valor_limpio += partes[i];
	}
	
	return valor_limpio;
}
/*******************************************************************************************************
    RECIBE LAS PULSACIONES DEL TECLADO PARA EDITAR O MOSTRAR LOS DATOS DEL REGISTRO SELECCIONADO 
******************************************************************************************************/
function pulsar(e) 
{	
    // SI ESTA DEFINIDA LA SECCIÓN QUE SE DESEA RECORRER
	if ( $('e_cuerpo_scrolleable') )
	{
		var valor_pagina = 0;
		
		// SI ESTA DEFINIDO EL ID nroFila_elegida Y POSEE UN VALOR
		if ( $('nroFila_elegida') && $('nroFila_elegida').value != '' )
		{
			fila_pulsar = eval($('nroFila_elegida').value * 1);
		}
		// REFERENCIA A LA SECCIÓN QUE SE DESEA RECORRER
		seccion_a_recorrer = $('e_cuerpo_scrolleable');
		
		// SE OBTIENEN TODAS LAS FILAS DE DICHA SECCIÓN
		filas_pulsar = seccion_a_recorrer.getElementsByTagName('tr');
				
		// tecla ENTER, se edita el registro
		if ( e.keyCode == 13 && !se_busca )
		{
			opcion = $('controlador').value;
			switch (opcion)
			{
				case 'codareas':
					refrescar('abms/index.php?controlador=codareas&accion=editar&ca_id='+$('ca_id'+fila_pulsar).innerHTML+'&pagina='+$('pagina').value, 'contenidoAjaxPrincipal');
					break;
				case 'codcargos':
					var nomenclador = limpiarValor($('cc_nomenclador'+fila_pulsar).innerHTML, '-');
					refrescar('abms/index.php?controlador=codcargos&accion=editar&cc_nomenclador='+nomenclador+'&pagina='+$('pagina').value, 'contenidoAjaxPrincipal');
					break;
				case 'autoridades':
					refrescar('abms/index.php?controlador=autoridades&accion=editar&a_id='+$('a_id'+fila_pulsar).value+'&pagina='+$('pagina').value, 'contenidoAjaxPrincipal');
					break;
				case 'personal':
					var legajo = limpiarValor($('p_legajo'+fila_pulsar).innerHTML, '.');
					refrescar('abms/index.php?controlador=personal&accion=editar&p_legajo='+legajo+'&pagina='+$('pagina').value, 'contenidoAjaxPrincipal');
					break;
			}
		}	
		
		// SI SE PULSÓ LA TECLA 'ARRIBA'
		if (e.keyCode == 38)
		{
			// SI NO ES LA PRIMER FILA
			if (fila_pulsar > 0)
			{
				num = -1;// SE RESTA LA FILA
			}
			else
			{
				// SI ES LA PRIMER FILA, SE DIRECCIONA A LA PAGINA ANTERIOR
				if (fila_pulsar == 0)
				{
					opcion = $('controlador').value;
					switch (opcion)
					{
						case 'codareas':
							valor_pagina = validar_limite_inicial_paginador();
							refrescar('abms/index.php?controlador=codareas&accion=listar&pagina='+valor_pagina+'&sentido=anterior&f_codigo='+$('f_codigo').value+'&f_nombre='+$('f_nombre').value, 'contenidoAjaxPrincipal');
							break;
						case 'codcargos':
							valor_pagina = validar_limite_inicial_paginador();
							refrescar('abms/index.php?controlador=codcargos&accion=listar&pagina='+valor_pagina+'&sentido=anterior&f_nomenclador='+$('f_nomenclador').value+'&f_nombre='+$('f_nombre').value, 'contenidoAjaxPrincipal');
							break;
						case 'autoridades':
							valor_pagina = validar_limite_inicial_paginador();
							refrescar('abms/index.php?controlador=autoridades&accion=listar&pagina='+valor_pagina+'&sentido=anterior', 'contenidoAjaxPrincipal');
							break;
						case 'personal':
							valor_pagina = validar_limite_inicial_paginador();
							refrescar('abms/index.php?controlador=personal&accion=listar&pagina='+valor_pagina+'&sentido=anterior&cmb_area='+$('cmb_area').value+'&cmb_cargo='+$('cmb_cargo').value, 'contenidoAjaxPrincipal');
							break;
					}
				}
			}  
		}
		else
		{
			// SI SE PULSÓ LA TECLA 'ABAJO'
			if (e.keyCode == 40)
			{
				// SI NO ES LA ÚLTIMA FILA
				if (fila_pulsar < filas_pulsar.length-1)
				{
					num = 1;// SE INCREMENTA LA FILA
				}
				else
				{
					// SI SE PULSA EN EL ULTIMO REGISTRO DE LA PAGINA, SE DIRECCIONA A LA PAGINA SIGUIENTE 
					if (fila_pulsar == filas_pulsar.length-1)
					{
						opcion = $('controlador').value;
						switch (opcion)
						{
							case 'codareas':
								valor_pagina = validar_limite_final_paginador();
								refrescar('abms/index.php?controlador=codareas&accion=listar&pagina='+valor_pagina+'&sentido=siguiente&f_codigo='+$('f_codigo').value+'&f_nombre='+$('f_nombre').value, 'contenidoAjaxPrincipal');
								break;
							case 'codcargos':
								valor_pagina = validar_limite_final_paginador();
								refrescar('abms/index.php?controlador=codcargos&accion=listar&pagina='+valor_pagina+'&sentido=siguiente&f_nomenclador='+$('f_nomenclador').value+'&f_nombre='+$('f_nombre').value, 'contenidoAjaxPrincipal');
								break;
							case 'autoridades':
								valor_pagina = validar_limite_final_paginador();
								refrescar('abms/index.php?controlador=autoridades&accion=listar&pagina='+valor_pagina+'&sentido=siguiente', 'contenidoAjaxPrincipal');
								break;
							case 'personal':
								valor_pagina = validar_limite_final_paginador();
								refrescar('abms/index.php?controlador=personal&accion=listar&pagina='+valor_pagina+'&sentido=siguiente&cmb_area='+$('cmb_area').value+'&cmb_cargo='+$('cmb_cargo').value, 'contenidoAjaxPrincipal');
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
		
		// SI ESTÁ HABILITADO EL REGISTRO
		if ( $('bandera_habilitado'+fila_pulsar).value == '1' )
		{
			// SE DESMARCA LA FILA
			$('e_fila'+fila_pulsar).setStyle('background', '#ffffff');
			$('e_fila'+fila_pulsar).setStyle('color', '#000000');
		}
		else // SI ESTA DESHABILITADO
		{
			// SE DESMARCA PERO SE DEJA EN COLOR GRIS EL TEXTO
			$('e_fila'+fila_pulsar).setStyle('background', '#ffffff');
			$('e_fila'+fila_pulsar).setStyle('color', '#A6ABAB');
		}
		
		// SE RESTA O SE SUMA LA FILA
		fila_pulsar = eval(fila_pulsar + num);
		
		// SE MARCA LA FILA ACTUAL
		filas_pulsar[fila_pulsar].style.background = '#76A0CD';
		filas_pulsar[fila_pulsar].style.color = '#ffffff';
	   
		// SE SETEA LA FILA ELEGIDA
		$('nroFila_elegida').value = fila_pulsar;
	}
}

function validar_limite_inicial_paginador()
{
    var valor = 0;

    if ( parseInt($('pagina').value) > 1 )
    {
		valor = parseInt($('pagina').value) - parseInt(1);
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
		valor = parseInt($('pagina').value) + parseInt(1);
    }
    else
    {
		valor = $('nro_paginas').value;
    }
    
    return valor;
}
/************************************************************************************************************/
function obtenerValor_RadioButtonSeleccionado(radio)
{
    for (i=0; i<radio.length; i++)
    {
        if (radio[i].checked) return radio[i].value;
    }
}
/*****************************************************************************************
		SE VERIFICA EL INGRESO DEL Codigo PARA LOS ABM's DE Cargos y Areas
*****************************************************************************************/
function validarCodigo(campo, form, carpeta, destino)
{
    var mensaje = "";
    var error = false;

    if ($(campo).value == ''){
		mensaje += "* Debe ingresar un valor para el C"+'\u00f3'+"digo, gracias.";
		error = true;
    }
    
    if (error){
		alert(mensaje);
    }else{	
		enviarForm(form, carpeta, destino);
    }	
}
/*****************************************************************************************
	SE VALIDA EL INGRESO DE DATOS EN LOS ABMs
*****************************************************************************************/
function validarCodArea()
{
    var mensaje = "";
    var error = false;

	 if ( $('ca_id').value == '' )
    { 
		error = true;
		mensaje += "Debe ingresar un c"+'\u00f3'+"digo de "+'\u00c1'+"rea.\n";
	}
	
    if ( $('ca_nombre').value == '' )
    { 
		error = true;
		mensaje += "Debe ingresar un nombre de "+'\u00c1'+"rea.\n";
	}
	
	if ( $('ca_depende_de').value == $('ca_id').value )
	{
		error = true;
		mensaje += "El "+'\u00c1'+"rea del cual depende no debe ser la misma.";
	}	
	
	if ( error )
	{	
		alert(mensaje);
    }
    else
    {
		enviarForm('formCodAreas', 'abms', 'contenidoAjaxPrincipal');
    }	
}	

function validarCodCargo()
{
    var mensaje = "";
    var error = false;

	if ( $('cc_parte_nomenclador_0').value == '' || $('cc_parte_nomenclador_1').value == '' || $('cc_parte_nomenclador_2').value == '' || $('cc_parte_nomenclador_3').value == '' )
    { 
		error = true;
		mensaje += "* Debe ingresar el valor completo del nomenclador.\n";
	}

    if ( $('cc_nombre').value == '' )
    { 
		error = true;
		mensaje += "* Debe ingresar un nombre para el Cargo.\n";
	}
	
	if ( error )
	{	
		alert(mensaje);
    }
    else
    {
		enviarForm('formCodCargos', 'abms', 'contenidoAjaxPrincipal');
    }	
}	

function validarAutoridad()
{
    var mensaje = "";
    var error = false;

    if ( $('a_funcion').value == '0' )
    {
	    error = true;
	    mensaje += "Debe elegir una Funci"+'\u00f3'+"n. \n";
    }
    
    if ( $('a_legajo').value == '0' )
    {
	    error = true;
	    mensaje += "Debe elegir un Legajo. \n";
    }
    
    if ( $('a_mail').value != '' )
    {
    	// Si el mail NO es valido
		if ( esMailValido($('a_mail').value) == false ) 
		{ 		
			error = true;
			mensaje += "Debe ingresar un mail valido.\n";
			$('a_mail').focus();
		}
    }
    
    if (error)
    {
		alert(mensaje);
    }
    else
    {
		// SE ENVÍA EL FORMULARIO
    	enviarForm('formAutoridades', 'abms', 'contenidoAjaxPrincipal');
	}	
}

function validarPersonal()
{
    var mensaje = "";
    var error = false;

    if ( $('p_legajo').value == '' )
    {
	    error = true;
	    mensaje += "* No ha ingresado el Legajo.\n";
    }
    
    if ( $('p_apellido').value == '' )
    {
	    error = true;
	    mensaje += "* Debe ingresar el Apellido.\n";
    }
    
    if ( $('p_fecha_ingreso_planta_politica').value == '' && $('p_fecha_ingreso_planta_permanente').value == '' )
    {
		error = true;
	    mensaje += "* Debe ingresar una Fecha de Ingreso, para Planta Pol"+'\u00ed'+"tica o Planta Permanente.\n";
	}
    
    if ( $('p_fecha_nac').value != '' )
    {
		if ( esMayorAFechaActual($('p_fecha_nac').value) )
		{
			error = true;
			mensaje += "* La Fecha de Nacimiento debe ser menor a la fecha actual.\n";
		}	
	}
	
	if ( ( $('p_calle_legal').value == '' && $('p_numero_legal').value != '' ) || ( $('p_calle_legal').value == '' && $('p_piso_legal').value != '' ) || ( $('p_calle_legal').value == '' && $('p_depto_legal').value != '' ) || ( $('p_calle_legal').value == '' && $('p_entre_calles_legal').value != '' ) ){
		error = true;
		mensaje += "* No ha ingresado la Calle.\n";
	}
	if ( ( $('p_numero_legal').value == '' && $('p_calle_legal').value != '' ) || ( $('p_numero_legal').value == '' && $('p_piso_legal').value != '' ) || ( $('p_numero_legal').value == '' && $('p_depto_legal').value != '' ) || ( $('p_numero_legal').value == '' && $('p_entre_calles_legal').value != '' ) ){
		error = true;
		mensaje += "* No ha ingresado el N"+'\u00fa'+"mero.\n";
	}
	if ( ( $('p_calle_real').value == '' && $('p_numero_real').value != '' ) || ( $('p_calle_real').value == '' && $('p_piso_real').value != '' ) || ( $('p_calle_real').value == '' && $('p_depto_real').value != '' ) || ( $('p_calle_real').value == '' && $('p_entre_calles_real').value != '' ) ){
		error = true;
		mensaje += "* No ha ingresado la Calle.\n";
	}	
	if ( ( $('p_numero_real').value == '' && $('p_calle_real').value != '' ) || ( $('p_numero_real').value == '' && $('p_piso_real').value != '' ) || ( $('p_numero_real').value == '' && $('p_depto_real').value != '' ) || ( $('p_numero_real').value == '' && $('p_entre_calles_real').value != '' ) ){
		error = true;
		mensaje += "* No ha ingresado el N"+'\u00fa'+"mero.";
	}

    if ( $('p_mail').value != '' ) {
        // Si el mail NO es valido
        if ( esMailValido($('p_mail').value) == false ) {       
            error = true;
            mensaje += "Debe ingresar un mail v"+'\u00e1'+"lido.\n";
            $('p_mail').focus();
        }
    }
	
    if (error) {
		alert(mensaje);
    } else {
		enviarForm('formPersonal', 'abms', 'contenidoAjaxPrincipal');
	}	
}	

function validarEstudio()
{
    var mensaje = "";
    var error = false;

    if ($('e_titulo').value == ''){
	    error = true;
	    mensaje += "* No ha ingresado el Titulo.\n";
    }

    if ($('e_fecha').value == ''){
	    error = true;
	    mensaje += "* No ha ingresado la Fecha.\n";
    }
 
    if ($('e_organismo').value == ''){
	    error = true;
	    mensaje += "* No ha ingresado el Organismo.\n";
    }
    
    if (error){
		alert(mensaje);
    }else{	
		enviarForm('formEstudio', 'abms', 'contenidoAjaxEdicion');
    }	
}

/**
 * Se valida la info de la ficha web antes de enviarla
 */
function validarFichaWeb()
{
    var mensaje = "";
    var error = false;

	// Si NO se ha ingresado un mail
	if ( $('fw_mail').value == '' )
	{
		error = true;
	    mensaje += "Debe ingresar un mail.";
	    $('fw_mail').focus();
    }
	else
	{
		// Si el mail NO es valido
		if ( esMailValido($('fw_mail').value) == false ) 
		{ 		
			error = true;
			mensaje += "Debe ingresar un mail valido.\n";
			$('fw_mail').focus();
		}
	}
	
    if ( $('fw_telefono').value == '' )
    {
	    error = true;
	    mensaje += "Debe elegir un Telefono.";
	    $('fw_telefono').focus();
    }
    
    if (error)
		alert(mensaje);
    else
		enviarForm('formFichaWeb', 'abms', 'contenidoAjaxEdicion');
}	

function validarDigito()
{
    var mensaje = "";
    var error = false;
    
    if ($('d_digito').value == ''){
		error = true;
		mensaje += "* No ha ingresado el D"+'\u00ed'+"gito.\n";
    }
    if ( $('d_fecha_ingreso_ejecutivo').value != '' && $('d_fecha_egreso').value != '' ){
		if ( esLaFechaMayor($('d_fecha_ingreso_ejecutivo').value, $('d_fecha_egreso').value) ){
			error = true;
			mensaje += "* La fecha de Ingreso al Ejecutivo debe ser menor a la fecha de Egreso.\n";
		}
	}
	if ( $('d_fecha_ingreso_hcd').value != '' && $('d_fecha_egreso').value != '' ){
		if ( esLaFechaMayor($('d_fecha_ingreso_hcd').value, $('d_fecha_egreso').value) ){
			error = true;
			mensaje += "* La fecha de Ingreso al HCD debe ser menor a la fecha de Egreso.";
		}
	}
	
    if (error){
		alert(mensaje);
    }else{	
		enviarForm('formDigito', 'abms', 'contenidoAjaxEdicion');
    }	
   
}	

function validarArea()
{
    var mensaje = "";
    var error = false;

    // Si no se ha seleccionado un Area
    if ($('a_id_area').value == 0)
    {
	    error = true;
	    mensaje += "* No ha seleccionado el "+'\u00c1'+"rea.\n";
    }
    // Si no se ha elegido una fecha de alta
    if ($('a_fecha_alta').value == '')
    {
	    error = true;
	    mensaje += "* No ha ingresado la Fecha de Alta.\n";
    }
    
    if (error)
    {
		alert(mensaje);
    }
    else
    {	
    	// Si el legajo pertenece a un Concejal
    	if ( $('es_concejal').value == 1)
		{
    		// Se pregunta si se desea modificar el Bloque para sus asesores
			var respuesta = confirm("¿Desea tambi"+'\u00e9'+"n asignarle el Bloque a sus Asesores?");

			// Se asigna 1 si aceptó ó 0 si NO aceptó
			$('desea_modificar_para_asesores').value = (respuesta === true) ? 1 : 0;
		}

		// Se envia el formulario con sus datos
    	enviarForm('formAreas', 'abms', 'contenidoAjaxEdicion');
	}	
}

function validarCargo()
{
    var mensaje = '';
    var error = false;

    if ( $('c_nomenclador').value == 0 )
    {
	    error = true;
	    mensaje += "* No ha seleccionado el Cargo.\n";
    }
    
    if ( $('c_fecha_alta').value == '' )
    {
	    error = true;
	    mensaje += "* No ha ingresado la Fecha de Ingreso.\n";
    }
   
	if ( $('c_digito').value < 0 )
	{
		error = true;
		mensaje += "* No ha ingresado el D"+'\u00ed'+"gito.\n";
    }
    /**
    if ( $('c_fecha_alta').value != '' && $('c_fecha_baja').value != '' )
    {
		if ( esLaFechaMayor($('c_fecha_alta').value, $('c_fecha_baja').value) )
		{
			error = true;
			mensaje += "* La fecha de Ingreso al Cargo debe ser menor a la fecha de Egreso\n";
		}
	}
    /**/
    if (error)
    {
		alert(mensaje);
    }
    else
    {	
		enviarForm('formCargos', 'abms', 'contenidoAjaxEdicion');
    }	
}

function validarFamiliar()
{
    var mensaje = '';
    var error = false;
	
    if ( $('f_apellido_familiar').value == '' )
    {
	    error = true;
	    mensaje += "* Debe ingresar un Apellido.\n";
    }
    
	if ( estaDefinido("f_fecha_inicio_convivencia") )
	{	
		if ( $('f_parentesco').value == "Concubino / Unido de Hecho")
		{	
			if ( $('f_fecha_nac').value != "" )
			{
				if ( esLaFechaMayor($('f_fecha_nac').value, $('f_fecha_inicio_convivencia').value) )
				{
					error = true;
					mensaje = "* La fecha de nacimiento debe ser menor a la fecha de inicio de convivencia.";
				}
			}
		}
	}
	
	if ( $('f_fecha_nac').value != '' )
	{
		if ( esMayorAFechaActual($('f_fecha_nac').value) )
		{
			error = true;
			mensaje += "* La fecha de nacimiento debe ser menor a la fecha actual.";
		}	
	}
		
    if (error)
    {
		alert(mensaje);
    }
    else
    {	
		enviarForm('formFamiliar', 'abms', 'contenidoAjaxEdicion');
    }	
}

function validarAntecedenteLaboral()
{
	var mensaje = '';
    var error = false;
	
    if ( $('al_cargo').value == '' )
    {
	    error = true;
	    mensaje += "* Debe ingresar un Cargo.\n";
    }
    
    if ( $('al_fecha_desde').value == '' )
    {
		error = true;
		mensaje += "* Debe ingresar la fecha Desde.\n";
	}
	
	if ( $('al_fecha_hasta').value != '' )
	{
		if ( esLaFechaMayor($('al_fecha_desde').value, $('al_fecha_hasta').value) )
		{
			error = true;
			mensaje = "* La fecha Desde debe ser menor a la fecha Hasta.";
		}
	}
	
    if (error)
    {
		alert(mensaje);
    }
    else
    {	
		enviarForm('formAntecedenteLaboral', 'abms', 'contenidoAjaxEdicion');
    }	
}			
				
// MASCARA DE ENTRADA PARA LAS FECHAS, dd/mm/YYYY
function mascara(d,sep,pat,nums)
{
	if (d.valant != d.value){
		val = d.value
		largo = val.length
		val = val.split(sep)
		val2 = ''
		for (r=0; r<val.length; r++){
			val2 += val[r]	
		}
		if (nums){
			for (z=0; z<val2.length; z++){
				if (isNaN(val2.charAt(z))){
					letra = new RegExp(val2.charAt(z),"g")
					val2 = val2.replace(letra,"")
				}
			}
		}
		val = ''
		val3 = new Array()
		for (s=0; s<pat.length; s++){
			val3[s] = val2.substring(0,pat[s])
			val2 = val2.substr(pat[s])
		}
		for (q=0; q<val3.length; q++){
			if (q ==0){
				val = val3[q]
			}
			else{
				if (val3[q] != ""){
					val += sep + val3[q]
					}
			}
		}
		d.value = val
		d.valant = val
    }
}

function esLaFechaMayor(fecha, fecha2)
{
  //alert("esLaFechaMayor: "+fecha+"<br>"+fecha2);	
  var xMonth=fecha.substring(3, 5);
  var xDay=fecha.substring(0, 2);
  var xYear=fecha.substring(6,10);
  
  var yMonth=fecha2.substring(3, 5);
  var yDay=fecha2.substring(0, 2);
  var yYear=fecha2.substring(6,10);
  //alert("esLaFechaMayor: <br>xMonth: "+xMonth+"<br>xDay: "+xDay+"<br>xYear: "+xYear+"<br>yMonth: "+yMonth+"<br>yDay: "+yDay+"<br>yYear: "+yYear);

  if (xYear> yYear)
  {
      return(true)
  }
  else
  {
    if (xYear == yYear)
    {
      if (xMonth> yMonth)
      {
		return(true)
      }
      else
      { 
		if (xMonth == yMonth)
		{
		  if (xDay > yDay){
			//alert(xDay+"<br>"+yDay);
			return(true);
		  }else{
			return(false);
		  }	
		}
		else
		  return(false);
      }
    }
    else
      return(false);
  }
}

function comparar_fechas(fechaDesde, fechaHasta)
{
    var mayor = false;
    // SE SEPARA EL AÑO, MES Y DÍA DE LAS FECHAS
    var fecha_desde = fechaDesde.split("/"); 
    var fecha_hasta = fechaHasta.split("/");
    
    //	SE TOMA EL AÑO, MES Y DÍA DE LA FECHA 'Desde'
    anio_desde = fecha_desde[2];
    mes_desde = fecha_desde[1];
    dia_desde = fecha_desde[0];
    //alert(anio_desde+'-'+mes_desde+'-'+dia_desde);
    //	SE TOMA EL AÑO, MES Y DíA DE LA FECHA 'Hasta'
    anio_hasta = fecha_hasta[2];
    mes_hasta = fecha_hasta[1];
    dia_hasta = fecha_hasta[0];
    //alert(anio_hasta+'-'+mes_hasta+'-'+dia_hasta);
    
    if (anio_hasta == anio_desde){ //SI EL AÑO ES EL MISMO, SE VERIFICA EL MES
	    if (mes_hasta == mes_desde){ //SI EL MES ES EL MISMO, SE VERIFICA EL DÍA
		    if (dia_hasta > dia_desde){
			    mayor = true; //SE CUMPLE QUE LA FECHA 'Hasta' ES MAYOR
		    }else{
			    mayor = false;
		    }
	    }else{
		    if (mes_hasta > mes_desde){ //SI EL MES ES MAYOR YA BASTA, EL DÍA PUEDE SER MENOR O IGUAL
			    mayor = true;
		    }	
	    }
    }else{
	    if (anio_hasta > anio_desde){ //SI EL AÑO ES MAYOR YA BASTA, EL MES PUEDE SER MENOR O IGUAL
		    mayor = true;
	    }else{	
		    mayor = false;//SI NO SE CUMPLE NINGUNA CONDICIÓN LA FECHA 'Hasta' ES MENOR A LA FECHA 'Desde'
	    }	
    }	
    return mayor;
}
/* ***********************************************************************************
* SimularClick: Simular un click en un objeto.
*   idObjecte : objecte sobre el cual se aplica el evento click
* ***********************************************************************************/
function gclick(idObjete)
{
  var nouEvent = document.createEvent("MouseEvents");
  nouEvent.initMouseEvent("click", true, true, window,0, 0, 0, 0, 0, false, false, false, false, 0, null);
  var objecte = document.getElementById(idObjete);
  var canceled = !objecte.dispatchEvent(nouEvent);
}	
/***************************************************************************************
//Calcula la edad del Empleado,
//recibe la fecha como un string en formato español (dd/mm/yyyy), devuelve un entero con la edad. 
//Devuelve false en caso de que la fecha sea incorrecta o mayor que el dia actual.
****************************************************************************************/
function calcular_edad(fecha)
{
	//calculo la fecha de hoy
	hoy=new Date();
	//alert(hoy.getFullYear());

	//calculo la fecha que recibo
	//La descompongo en un array
	var array_fecha = fecha.split("/")
	//si el array no tiene tres partes, la fecha es incorrecta
	if (array_fecha.length!=3)
	   return '';//false;

	//compruebo que los ano, mes, dia son correctos
	var ano
	ano = parseInt(array_fecha[2]);
	if (isNaN(ano))
	   return '';//false

	var mes
	mes = parseInt(array_fecha[1]);
	if (isNaN(mes))
	   return '';//false;

	var dia
	dia = parseInt(array_fecha[0]);
	if (isNaN(dia))
	   return '';//false;


	//si el año de la fecha que recibo solo tiene 2 cifras hay que cambiarlo a 4
	if (ano<=99)
	   ano +=1900;

	//resto los años de las dos fechas
	edad=hoy.getFullYear()- ano - 1; //-1 porque no se sabe si ha cumplido años ya este año

	//si resto los meses y me da menor que 0 entonces no ha cumplido años. Si da mayor si ha cumplido
	if (hoy.getMonth() + 1 - mes < 0) //+ 1 porque los meses empiezan en 0
	   return edad;
	if (hoy.getMonth() + 1 - mes > 0)
	   return edad+1;

	//entonces es que eran iguales. miro los dias
	//si resto los dias y me da menor que 0 entonces no ha cumplido años. Si da mayor o igual si ha cumplido
	if (hoy.getUTCDate() - dia >= 0)
	   return edad + 1;

	return edad;
} 

function compare_dates(fecha, fecha2)
{
	var xMonth=fecha.substring(3, 5);
	var xDay=fecha.substring(0, 2);
	var xYear=fecha.substring(6,10);
	var yMonth=fecha2.substring(3, 5);
	var yDay=fecha2.substring(0, 2);
	var yYear=fecha2.substring(6,10);
	if (xYear> yYear)
	{
		return(true)
	}
	else
	{
	  if (xYear == yYear)
	  { 
		if (xMonth> yMonth)
		{
			return(true)
		}
		else
		{ 
		  if (xMonth == yMonth)
		  {
			if (xDay> yDay)
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
	
	if ( compare_dates(fecha, fecha_actual) ){	
		//alert(fecha+" es mayor a "+fecha_actual);
		return(true);
	}else{
		return(false);	
	}	
}

function estaDefinido(variable)
{ 
	return (typeof($(variable)) == "undefined")? false: true;
}

// SE OCULTA EL MENU PRINCIPAL
function ocultarMenuPpal()
{	
	$('menu_ppal').setStyle('display', 'none');
}	

// SE MUESTRA EL MENU PRINCIPAL
function mostrarMenuPpal()
{	
	//$('header').setStyles({ 'text-align':'center', 'margin':'10px auto 0' });	
	$('menu_ppal').setStyle('display', 'inline-block');
}

/**
 * Para la carga de la foto del legajo
 * Se oculta el iframe, la capa de fondo oscuro y la capa de la modal 
 */
function ocultarIframeParaSubirFoto()
{
	$("iframeParaSubirFoto").setStyle('display','none');
	$("capaFondo").setStyle('visibility','hidden');
	$("capaVentana").setStyle('visibility','hidden');
}

/**
 * Para la carga de la foto de la ficha web
 * Se oculta el iframe, la capa de fondo oscuro y la capa de la modal 
 */
function ocultarIframeParaSubirFotoFichaWeb()
{
    $("iframeParaSubirFotoFichaWeb").setStyle('display','none');
    $("capaFondo").setStyle('visibility','hidden');
    $("capaVentana").setStyle('visibility','hidden');
}

/**
 * Al abrir la ventana modal de la carga de la foto, se guarda la información previamente cargada del formulario de edición del legajo
 */
function cargarDatosEmpleadoModal()
{
	if ( $('p_apellido').value != '' )
	{
		$('p_apellido_modal').value = $('p_apellido').value;
	}
	
	if ( $('p_nombre').value != '' )
	{
		$('p_nombre_modal').value = $('p_nombre').value;
	}
	
	if ( $('op_M').checked  )
	{
		$('p_sexo_modal').value = $('op_M').value;
	}
	if ( $('op_F').checked  )
	{
		$('p_sexo_modal').value = $('op_F').value;
	}
	
	if ( $('p_grupo_sanguineo').value != '' )
	{
		$('p_grupo_sanguineo_modal').value = $('p_grupo_sanguineo').value;
	}
	
	if ( $('op_RH_positivo').checked )
	{
		$('p_factor_sanguineo_modal').value = $('op_RH_positivo').value;
	}
	if ( $('op_RH_negativo').checked )
	{
		$('p_factor_sanguineo_modal').value = $('op_RH_negativo').value;
	}
	
	if ( $('op_lc').checked )
	{
		$('p_tipo_documento_modal').value = $('op_lc').value;
	}
	if ( $('op_le').checked )
	{
		$('p_tipo_documento_modal').value = $('op_le').value;
	}
	if ( $('op_dni').checked )
	{
		$('p_tipo_documento_modal').value = $('op_dni').value;
	}
	
	if ( $('p_nro_documento').value != '' )
	{
		$('p_nro_documento_modal').value = $('p_nro_documento').value;
	}
	
	if ( $('p_fecha_ingreso_planta_politica').value != '' )
	{
		$('p_fecha_ingreso_planta_politica_modal').value = $('p_fecha_ingreso_planta_politica').value;
	}
	
	if ( $('p_fecha_ingreso_planta_permanente').value != '' )
	{
		$('p_fecha_ingreso_planta_permanente_modal').value = $('p_fecha_ingreso_planta_permanente').value;
	}
	
	if ( $('p_fecha_nac').value != '' )
	{
		$('p_fecha_nac_modal').value = $('p_fecha_nac').value;
	}
	
	if ( $('p_lugar_nac').value != '' )
	{
		$('p_lugar_nac_modal').value = $('p_lugar_nac').value;
	}
	
	if ( $('p_provincia').value != '' )
	{
		$('p_provincia_modal').value = $('p_provincia').value;
	}
	
	if ( $('p_pais').value != '' )
	{
		$('p_pais_modal').value = $('p_pais').value;
	}
	
	if ( $('op_arg_nativo').checked )
	{
		$('p_nacionalidad_modal').value = $('op_arg_nativo').value;
	}
	if ( $('op_arg_opcion').checked )
	{
		$('p_nacionalidad_modal').value = $('op_arg_opcion').value;
	}
	if ( $('op_arg_adopcion').checked )
	{
		$('p_nacionalidad_modal').value = $('op_arg_adopcion').value;
	}
	
	if ( $('op_estado_soltero').checked )
	{
		$('p_estado_civil_modal').value = $('op_estado_soltero').value;
	}
	if ( $('op_estado_casado').checked )
	{
		$('p_estado_civil_modal').value = $('op_estado_casado').value;
	}
	if ( $('op_estado_separado').checked )
	{
		$('p_estado_civil_modal').value = $('op_estado_separado').value;
	}
	if ( $('op_estado_divorciado').checked )
	{
		$('p_estado_civil_modal').value = $('op_estado_divorciado').value;
	}
	if ( $('op_estado_viudo').checked )
	{
		$('p_estado_civil_modal').value = $('op_estado_viudo').value;
	}
	if ( $('op_estado_union_hecho').checked )
	{
		$('p_estado_civil_modal').value = $('op_estado_union_hecho').value;
	}
	
	
	// PARA DOMICILIO LEGAL
	if ( $('p_calle_legal').value != '' )
	{
		$('p_calle_legal_modal').value = $('p_calle_legal').value;
	}
	
	if ( $('p_numero_legal').value != '' )
	{
		$('p_numero_legal_modal').value = $('p_numero_legal').value;
	}
	
	if ( $('p_piso_legal').value != '' )
	{
		$('p_piso_legal_modal').value = $('p_piso_legal').value;
	}
	
	if ( $('p_depto_legal').value != '' )
	{
		$('p_depto_legal_modal').value = $('p_depto_legal').value;
	}
	
	if ( $('p_entre_calles_legal').value != '' )
	{
		$('p_entre_calles_legal_modal').value = $('p_entre_calles_legal').value;
	}
	if ( $('p_zona_barrio_legal').value != '' )
	{
		$('p_zona_barrio_legal_modal').value = $('p_zona_barrio_legal').value;
	}
	
	if ( $('p_pais_legal').value != '' )
	{
		$('p_pais_legal_modal').value = $('p_pais_legal').value;
	}
	
	if ( $('p_provincia_legal').value != '' )
	{
		$('p_provincia_legal_modal').value = $('p_provincia_legal').value;
	}
	
	if ( $('p_localidad_legal').value != '' )
	{
		$('p_localidad_legal_modal').value = $('p_localidad_legal').value;
	}
	
	if ( $('p_telefono_legal').value != '' )
	{
		$('p_telefono_legal_modal').value = $('p_telefono_legal').value;
	}

	
	// PARA DOMICILIO REAL
	if ( $('p_calle_real').value != '' )
	{
		$('p_calle_real_modal').value = $('p_calle_real').value;
	}
	
	if ( $('p_numero_real').value != '' )
	{
		$('p_numero_real_modal').value = $('p_numero_real').value;
	}
	
	if ( $('p_piso_real').value != '' )
	{
		$('p_piso_real_modal').value = $('p_piso_real').value;
	}
	
	if ( $('p_depto_real').value != '' )
	{
		$('p_depto_real_modal').value = $('p_depto_real').value;
	}
	
	if ( $('p_entre_calles_real').value != '' )
	{
		$('p_entre_calles_real_modal').value = $('p_entre_calles_real').value;
	}
	
	if ( $('p_zona_barrio_real').value != '' )
	{
		$('p_zona_barrio_real_modal').value = $('p_zona_barrio_real').value;
	}
	
	if ( $('p_pais_real').value != '' )
	{
		$('p_pais_real_modal').value = $('p_pais_real').value;
	}
	
	if ( $('p_provincia_real').value != '' )
	{
		$('p_provincia_real_modal').value = $('p_provincia_real').value;
	}
	
	if ( $('p_localidad_real').value != '' )
	{
		$('p_localidad_real_modal').value = $('p_localidad_real').value;
	}
	
	if ( $('p_telefono_real').value != '' )
	{
		$('p_telefono_real_modal').value = $('p_telefono_real').value;
	}
	
	if ( $('p_celular_real').value != '' )
	{
		$('p_celular_real_modal').value = $('p_celular_real').value;
	}
	
	if ( $('p_tel_mensajes_real').value != '' )
	{
		$('p_tel_mensajes_real_modal').value = $('p_tel_mensajes_real').value;
	}
}

function AutoSuggest(elem, elementos_posibles, combo, clave)
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
			if ( clave == 'simple' )// PARA LOS COMBOS DE i_area, i_cargo, i_bloque
			{
				validarNombreModal(combo);
				
				$(combo).fireEvent("change");// 07/02/2013
			}
			else
			{
				if ( clave == 'doble' )// PARA LOS COMBOS DE i_concejal, i_retira
				{
					validarNombreClaveDobleModal(combo);
					
					$(combo).fireEvent("change");// 07/02/2013
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
		alert('Debe sugerir un nombre.');
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
	}			
}

function validarNombreClaveDobleModal(combo)
{
	if($('nombre_sugerido').value == '')
	{
		alert('Debe sugerir un nombre.');
	}
	else
	{
		// SE SEPARA EL TIPO, EL CODIGO Y LA DESCRIPCION DE LO ELEGIDO
		var valor_elegido = $('nombre_sugerido').value.split(', ');
		legajo = valor_elegido[0];
		nombre = valor_elegido[1];
		apellido = valor_elegido[2];

		$(combo).value = legajo;
		
		ventana_modal = "no";
		
		// SE CIERRA LA VENTANA MODAL
		$('light').setStyle('display', 'none');
		$('fade').setStyle('display', 'none');
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

function cerrarModalPedirNombre()
{
	// SE SETEA EL ANCHO Y ALTO DE LA MODAL CON LOS VALORES POR DEFECTO
	$('light').setStyle('width', '400px');
	$('light').setStyle('height', '120px');
	// SE CIERRA LA VENTANA MODAL
	$('light').setStyle('display', 'none');
	$('fade').setStyle('display', 'none');
}		

function definirEventoPorDefecto(objeto, evento)
{
	$(objeto).addEvent(evento, function()
	{
	});
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

function marcar_desmarcar_checkbox(id_check_todos, formulario)
{
	if ( $(id_check_todos).checked )
	{
		marcarTodosCheckbox(formulario);
	}
	else
	{
		desmarcarTodosCheckbox(formulario);
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

function mostrarCapa(id)
{
	if ( $(id) )
	{
		$(id).setStyle('visibility','visible');
	}
}

function ocultarCapa(id)
{
	if ( $(id) )
	{
		$(id).setStyle('visibility','hidden');
	}
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

function cerrarModalNueva()
{
	$("capaFondo").setStyle('visibility','hidden');
	$("capaVentana").setStyle('visibility','hidden');
}

function ocultarElemento(id, milisegundos)
{
    setTimeout(function(){    
        new Fx.Styles(id).start({'opacity': ['1', '0']});
    }, milisegundos);
}

/**
 * Se controla la habilitación o no de la fila del Concejal al utilizar su checkbox correspondiente
 * 
 * @param elemento
 * @param fila
 */
function controlarFilaConcejal(elemento, fila)
{
	// Si se elige el Concejal
	if ( elemento.checked )
	{
		// Se activan las casillas de los años del período
		$('i_anio_inicio_'+fila).setProperty("disabled","");
		$('i_anio_fin_'+fila).setProperty("disabled","");
		
		// Se pintan sus fondos de blanco
		$('i_anio_inicio_'+fila).setStyle("background-color","#fff");
		$('i_anio_fin_'+fila).setStyle("background-color","#fff");
	}
	else
	{
		// Se desactivan las casillas de los años del período
		$('i_anio_inicio_'+fila).setProperty("disabled","disabled");
		$('i_anio_fin_'+fila).setProperty("disabled","disabled");
		
		// Se pintan sus fondos de tono gris
		$('i_anio_inicio_'+fila).setStyle("background-color","#EBEBE4");
		$('i_anio_fin_'+fila).setStyle("background-color","#EBEBE4");
	}
}


function marcarTodosCheckboxConcejales(formulario)
{
    for (i=0;i< document.forms[formulario].elements.length;i++)
    {
        if ( document.forms[formulario].elements[i].type == "checkbox" )
        {
			document.forms[formulario].elements[i].checked = 1;
		}
        
        if ( document.forms[formulario].elements[i].name == "gcc_anios_inicio[]" )
        {
			document.forms[formulario].elements[i].disabled = "";
		}
        if ( document.forms[formulario].elements[i].name == "gcc_anios_fin[]" )
        {
			document.forms[formulario].elements[i].disabled = "";
		}
	}
}

function desmarcarTodosCheckboxConcejales(formulario)
{
    for (i=0;i< document.forms[formulario].elements.length;i++)
    {
        if ( document.forms[formulario].elements[i].type == "checkbox" )
        {
			document.forms[formulario].elements[i].checked = 0;
		}

        if ( document.forms[formulario].elements[i].name == "gcc_anios_inicio[]" )
        {
			document.forms[formulario].elements[i].disabled = "disabled";
		}

        if ( document.forms[formulario].elements[i].name == "gcc_anios_fin[]" )
        {
			document.forms[formulario].elements[i].disabled = "disabled";
		}
	}
}

function marcar_desmarcar_checkboxConcejales(id_check_todos, formulario)
{
	if ( $(id_check_todos).checked )
	{
		marcarTodosCheckboxConcejales(formulario);
	}
	else
	{
		desmarcarTodosCheckboxConcejales(formulario);
	}
}

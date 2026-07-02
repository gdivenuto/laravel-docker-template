/**
 * Fix para sobreescribir los defaults del validator para que sea compatible con Bootstrap 3.
 * http://stackoverflow.com/questions/18754020/bootstrap-3-with-jquery-validation-plugin
 * @return {[type]} [description]
 */
function fixValidatorBootstrap()
{
	$.validator.setDefaults({
    	highlight: function(element) {
        	$(element).closest('.form-group').addClass('has-error');
    	},
    	unhighlight: function(element) {
        	$(element).closest('.form-group').removeClass('has-error');
    	},
    	errorElement: 'span',
    	errorClass: 'help-block',
    	errorPlacement: function(error, element) {
        	if(element.parent('.input-group').length) {
            	error.insertAfter(element.parent());
        	} else {
            	error.insertAfter(element);
        	}
    	}
	});
}

/**
 * Simil funcion preg_quote en PHP
 * @param  {[type]} str       [description]
 * @param  {[type]} delimiter [description]
 * @return {[type]}           [description]
 */
function preg_quote(str, delimiter) {
  //  discuss at: http://phpjs.org/functions/preg_quote/
  // original by: booeyOH
  // improved by: Ates Goral (http://magnetiq.com)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Brett Zamir (http://brett-zamir.me)
  // bugfixed by: Onno Marsman
  //   example 1: preg_quote("$40");
  //   returns 1: '\\$40'
  //   example 2: preg_quote("*RRRING* Hello?");
  //   returns 2: '\\*RRRING\\* Hello\\?'
  //   example 3: preg_quote("\\.+*?[^]$(){}=!<>|:");
  //   returns 3: '\\\\\\.\\+\\*\\?\\[\\^\\]\\$\\(\\)\\{\\}\\=\\!\\<\\>\\|\\:'

  return String(str)
    .replace(new RegExp('[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\' + (delimiter || '') + '-]', 'g'), '\\$&');
}

/**
 * Agrega al validator de jQuery un conjunto predefinido de validaciones por 
 * expresiones regulares
 */
function asignarValidatorExpresionesRegulares() {
  
  // Se agrega una expresion regular para valores alfanuméricos
  $.validator.addMethod("regexAlphaNum", function(value, element) {
        return this.optional(element) || /^[a-z0-9]+$/i.test(value);
  }, "Solamente se permiten caracteres y n&uacute;meros.");

  // Se agrega una expresion regular para valores alfanuméricos (extendido)
  caracteres_especiales = preg_quote("áéíóúüñçÁÉÍÓÚÜÑÇ");
  $.validator.addMethod("regexAlphaNumExt", function(value, element) {
      // los caracteres especiales se escapan con doble barra
      re = new RegExp('^[a-z0-9\\s_\.-'+caracteres_especiales+']+$', 'i');
      return this.optional(element) || re.test(value);
    }, "Solamente se permiten n&uacute;meros, caracteres y algunos caracteres especiales (_ - .).");

  // Se agrega una expresion regular para la fecha 
  $.validator.addMethod("regexDate", function(value, element) {
        // Se agrega una validación por expresion regular para cubrir aquello que el 'moment' no considera.
        // return this.optional(element) || moment(value, 'D/M/YYYY').isValid();
        return this.optional(element) || ( /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/.test(value) && moment(value, 'D/M/YYYY').isValid());
  }, "Fecha inv&aacute;lida; el formato v&aacute;lido es 'd&iacute;a/mes/a&ntilde;o'");

  // Se agrega una expresion regular para la fecha y hora
  $.validator.addMethod("regexDateTime", function(value, element) {
        return this.optional(element) || (/^(\d{1,2})\/(\d{1,2})\/(\d{4}) (\d{1,2}):(\d{1,2}):(\d{1,2})$/.test(value) && moment(value, 'D/M/YYYY H:m:s').isValid());
  }, "Fecha/Hora inv&aacute;lida; el formato v&aacute;lido es 'd&iacute;a/mes/a&ntilde;o hora:minuto:segundo'");

  // Se agrega una expresion regular para la hora
  $.validator.addMethod("regexTime", function(value, element) {
        return this.optional(element) || (/^(\d{1,2}):(\d{1,2}):(\d{1,2})$/.test(value) && moment(value, 'H:m:s').isValid());
  }, "Hora inv&aacute;lida; el formato v&aacute;lido es 'hora:minuto:segundo'");

  // Se agrega una expresion regular para la validación del parametro
  $.validator.addMethod("regexParametro", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9_\.-]+$/.test(value);
  }, "Par&aacute;metro inv&aacute;lido; solamente se permiten caracteres, n&uacute;meros, gui&oacute;n bajo, gui&oacute;n medio y punto.");

  // Se agrega una expresion regular para valores alfanuméricos
  patron_nombre_usuario = preg_quote("@.!#$%&'*+-/=?^_`{|}~");
  $.validator.addMethod("regexUserName", function(value, element) {
    re = new RegExp('^[a-z0-9'+patron_nombre_usuario+']+$', 'i');
        return this.optional(element) || re.test(value);
  }, "Solamente se permiten n&uacute;meros, caracteres y algunos caracteres especiales (@.!#$%&amp;'*+-/=?^_`{|}~).");

  // Se agrega una metodo para validar el tamaño del archivo
  $.validator.addMethod('filesize', function(value, element, param) {
      // param = tamaño (en bytes) 
      // element = elemento a validar (<input>)
      // value = valor del elemento (nombre del archivo)
      return this.optional(element) || (element.files[0].size <= param) 
  });

  $.validator.addMethod("seleccionNoCero", function(value) {
        return (value != 0);
  }, "Debe seleccionar un valor de la lista de opciones.");

  $.validator.addMethod("seleccionNoVacio", function(value) {
        return (value != '');
  }, "Debe seleccionar un valor de la lista de opciones.");

  // Se agrega una expresion regular para valores numéricos
  $.validator.addMethod("regexNumerico", function(value, element) {
        return this.optional(element) || /^[0-9]+$/i.test(value);
  }, "Solamente se permiten n&uacute;meros.");
}

/**
 * Inicializa el comportamiento de los componentes DatePicker
 */
function inicializarDatePicker() {
  // Datepicker Defaults
  $.datepicker.setDefaults($.datepicker.regional['es']);
  $.datepicker.setDefaults({
      dateFormat: 'dd/mm/yy'
    , altFormat: 'yy-mm-dd'
    , changeMonth: true
    , constrainInput: false
    , beforeShow: function(elemento) { if ($(elemento).attr('readonly')) { return false; } } // Si el campo es readonly no permito su edición
    //, showButtonPanel: true
  });
}

// Toma como valor por defecto lo contenido en el atributo placeholder si el value del input es vacio.
function defaultFromPlaceholder(id) {
    if ($(id).val() == '')
        $(id).val($(id).attr('placeholder'));
}
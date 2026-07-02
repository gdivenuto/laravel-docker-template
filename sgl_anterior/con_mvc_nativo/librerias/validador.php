<?php
/***************************************************************************
		CLASE ESTATICA PARA VALIDAR PARAMETROS RECIBIDOS EN TODO EL SISTEMA
****************************************************************************/

class Validador
{
	// ************************************************************************
	// CONSTANTES DE CONFIGURACION DEL VALIDADOR ******************************
	// ************************************************************************

	// Si esta habilitado, los errores de validaciones de parametros se guardan en el 
	// log de apache. 
	public static $LOG_PHP_VALIDATION_ERROR = true; // false;
	
	// ************************************************************************
	// DEFINICION DE CONSTANTES PARA VALIDAR PARAMETROS ***********************
	// ************************************************************************
	
	// solo numeros
	public static $PATRON_NUMEROS = "/^[0-9]*$/";
	
	// solo letras (mayusculas y minúsculas), SIN caracteres acentuados
	public static $PATRON_LETRAS = "/^[a-z]*$/i";
	
	// solo letras (mayusculas y minúsculas), CON caracteres acentuados (áéíóúñÁÉÍÓÚÑ)
	public static $PATRON_LETRAS_ES = "/^[a-z\xE1\xE9\xED\xF3\xFA\xC1\xC9\xCD\xD3\xDA\xF1\xD1\]*$/i";
	
	// letras (mayusculas y minúsculas) y numeros y "/" para fechas, CON caracteres acentuados (áéíóúñÁÉÍÓÚÑ)
	public static $PATRON_SEGURO_FACIL = "/^[0-9a-z\xE1\xE9\xED\xF3\xFA\xC1\xC9\xCD\xD3\xDA\xF1\xD1\/]*$/i"; 
	
	// letras (mayusculas y minúsculas), numeros, blancos y "/" para fechas, CON caracteres acentuados (áéíóúñÁÉÍÓÚÑ)
	public static $PATRON_SEGURO_ESPACIOS = "/^[0-9a-z\xE1\xE9\xED\xF3\xFA\xC1\xC9\xCD\xD3\xDA\xF1\xD1\/\s]*$/i"; 
	
	// letras (mayusculas y minúsculas), numeros, blancos, signos de puntuación y "/" para fechas, CON caracteres acentuados (áéíóúñÁÉÍÓÚÑ)
	public static $PATRON_SEGURO_SIGNOS = "/^[0-9a-z\xE1\xE9\xED\xF3\xFA\xC1\xC9\xCD\xD3\xDA\xF1\xD1\(\)\.,;\/\s]*$/i"; 
	
	// nombre de archivo válido, donde NO SE PERMITEN los caracteres:  \ / : | < > * ? ;
	public static $PATRON_ARCHIVO = "/^[^\\/\:\|\<\>\*\?;]*$/i"; 
	
	// ************************************************************************
	// DEFINICION DE METODOS **************************************************
	// ************************************************************************
	
	public static function validarParametroDirecto($valor, $patron, $valor_defecto = '')
	{
		$aux = $valor;
		
		// SI SE DESEA VALIDAR MEDIANTE UN PATRON
		if ( trim($patron) != '' )
		{
			// SE EVALUA CON EL PATRON
			if ( preg_match($patron, $aux) )
			{
				return $aux;
			}
			else
			{
				// Si esta activada la validación, guardo en el log.
				if (Validador::$LOG_PHP_VALIDATION_ERROR)
				{
					error_log("[Validador] Fallo en validacion de parametro: $valor = $aux");					
				}
				
				// TODO: DEFINIR SI DEVOLVEMOS FALSE O LANZAMOS UNA EXCEPCION
				return false;
			}
		}	
		else
		{
			return $aux;
		}
	}

	public static function validarParametro($valor, $valor_defecto = '', $patron = '')
	{
		$aux = self::validarBasico($valor, $valor_defecto);
		
		// SI SE DESEA VALIDAR MEDIANTE UN PATRON
		if ( trim($patron) != '' )
		{
			// SE EVALUA CON EL PATRON
			if ( preg_match($patron, $aux) )
			{
				return $aux;
			}
			else
			{
				// TODO: DEFINIR SI DEVOLVEMOS FALSE O LANZAMOS UNA EXCEPCION
				return false;
			}
		}	
		else
		{
			return $aux;
		}
	}
	
	/**
	 * Se valida un determinado valor, pudiéndose utilizar un valor por defecto en caso de existir en la invocación
	 * @param string $valor
	 * @param string $valor_defecto
	 * @return mixed
	 */
	public static function validarBasico($valor, $valor_defecto = '')
	{
		$aux = (isset($_REQUEST[$valor])&&($_REQUEST[$valor]!='')) ? trim(strip_tags($_REQUEST[$valor])) : trim(strip_tags($valor_defecto));
	
		// Se reemplazan los & y " por su equivalente en HTML
		$aux = str_replace('&', '&amp;',  $aux);
		$aux = str_replace('"', '&quot;', $aux);
		
		return $aux;
	}
}
?>

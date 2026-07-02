<?php
/**
 * Clase estática para validar parámetros utilizando expresiones regulares.
 * @author XXXX
 *
 */
class RegExValidator
{
	// ************************************************************************
	// Definición de Constantes ***********************************************
	// ************************************************************************
	// Si esta habilitado, los errores de validaciones de parametros se guardan en el
	// log de apache.
	const LOG_PHP_VALIDATION_ERROR = true; // false;


	// ************************************************************************
	// Definicion de constantes para validar parámetros ***********************
	// ************************************************************************
	// solo numeros
	const PATRON_NUMEROS = "/^[0-9]+$/";

	// solo letras (mayusculas y minúsculas), SIN caracteres acentuados
	const PATRON_LETRAS = "/^[a-z]*$/i";

	// solo letras (mayusculas y minúsculas), SIN caracteres acentuados, AL MENOS 1
	const PATRON_LETRAS_NO_VACIO = "/^[a-z]+$/i";

	// solo letras (mayusculas y minúsculas) y dígitos
	const PATRON_CODIGO_ALFANUMERICO_NO_VACIO = "/^[a-z0-9]+$/i";

	// solo letras (mayusculas y minúsculas), CON caracteres acentuados (áéíóúñÁÉÍÓÚÑ)
	const PATRON_LETRAS_ES = "/^[a-z\xE1\xE9\xED\xF3\xFA\xC1\xC9\xCD\xD3\xDA\xF1\xD1\]*$/i";

	// letras (mayusculas y minúsculas) y numeros y "/" para fechas, CON caracteres acentuados (áéíóúñÁÉÍÓÚÑ)
	const PATRON_SEGURO_FACIL = "/^[0-9a-z\xE1\xE9\xED\xF3\xFA\xC1\xC9\xCD\xD3\xDA\xF1\xD1\/]*$/i";

	// letras (mayusculas y minúsculas), numeros, blancos y "/" para fechas, CON caracteres acentuados (áéíóúñÁÉÍÓÚÑ)
	const PATRON_SEGURO_ESPACIOS = "/^[0-9a-z\xE1\xE9\xED\xF3\xFA\xC1\xC9\xCD\xD3\xDA\xF1\xD1\/\s]*$/i";

	// letras (mayusculas y minúsculas), numeros, blancos, signos de puntuación y "/" para fechas, CON caracteres acentuados (áéíóúñÁÉÍÓÚÑ)
	const PATRON_SEGURO_SIGNOS = "/^[0-9a-záéíóúüñçÁÉÍÓÚÜÑÇ\xE1\xE9\xED\xF3\xFA\xC1\xC9\xCD\xD3\xDA\xF1\xD1\(\)\.,;\/\-\s]*$/i";

	// nombre de archivo válido, donde NO SE PERMITEN los caracteres:  \ / : | < > * ? ;
	const PATRON_ARCHIVO = "/^[^\\/\:\|\<\>\*\?;]*$/i";

	// letras posibles para tipo de expediente válido
	const PATRON_TIPO_EXPEDIENTE = "/^[NERDO]$/";

	// letras posibles para estado de prestamo de expediente
	const PATRON_ESTADO_PRESTAMO_EXPEDIENTE = "/^[SPDA]$/";

	// letras posibles para estado de prestamo de solicitud de expediente externo
	const PATRON_ESTADO_SOLICITUD_EXPEDIENTE_EXTERNO = "/^(SHCD|SEE|IEE|DEE|AEE)$/";

	// formato fecha y hora en string (compatible MySQL)
	const PATRON_FECHA_HORA = "/^[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2} [0-9]{2,2}:[0-9]{2,2}:[0-9]{2,2}$/i";

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Valida un parámetro; si no se cumple la expresion regular, lanza una excepción.
	 * @param string $valor Valor a validar.
	 * @param string $patron Expresión regular a utilizar para validación.
	 * @param string $nombreParametro Opcional: nombre del parámetro que se esta validando.
	 * @return bool
	 * @throws UnexpectedValueException
	 */
	public static function Validar($valor, $patron, $permitirNull = false, $nombreParametro = '')
	{
		$aux = $valor;

		// SI SE DESEA VALIDAR MEDIANTE UN PATRON
		if ( trim($patron) != '' )
		{
			if (($permitirNull) && ($valor == null))
			{
				return null;
			}
			else
			{
				// SE EVALUA CON EL PATRON
				if ( preg_match($patron, $aux) )
				{
					return $aux;
				}
				else
				{
					// Si esta activada la validación, guardo en el log.
					if (self::LOG_PHP_VALIDATION_ERROR)
					{
						error_log("[RegExValidator] Fallo en validacion de parametro: $valor = $aux");
					}

					throw new Exception("Error en validación: $nombreParametro");
				}
			}
		}
		else
		{
			return $aux;
		}
	}

	/**
	 * Valida un parámetro; si no se cumple la expresion regular, asigna un valor por defecto.
	 * @param string $valor Valor a validar.
	 * @param string $patron Expresión regular a utilizar para validación.
	 * @param string $valor_defecto Valor por defecto en caso de que falle la validación.
	 * @return unknown|boolean
	 */
	public static function ValidarConDefault($valor, $patron, $permitirNull = false, $valor_defecto = '')
	{
		try
		{
			return self::Validar($valor, $patron);
		}
		catch (Exception $e)
		{
			return $valor_defecto;
		}
	}

}

?>

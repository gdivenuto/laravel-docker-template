<?php
/**
 * Clase base para todas las clases del modelo.
 * Contiene:
 * 	- Logica de "Cast".
 * 	- Conversion a JSON y Base64
 *
 * @author XXXX, XXXX
 *
 */
abstract class ClaseBaseSGL {

	/**
	 * Convierte la instancia actual a su correspondiente representacion en JSON.
	 * @return string
	 */
	public function ToJson()
	{
		return json_encode ( $this );
	}

	/**
	 * Obtiene una cadena en JSON e intenta regenerar el estado de la instancia a partir del elemento serializado.
	 * @param string $json_data
	 * @return mixed
	 */
	public function FromJson($json_data)
	{
		$stdData = json_decode($json_data);
		return $this->Cast($stdData);
	}

	/**
	 * Serializa la instancia empaquetando con Base64 la cadena resultado.
	 * @return string
	 */
	public function Serializar()
	{
		return base64_encode($this->ToJson());
	}

	/**
	 * Deserializa la instancia desempaquetando con Base64 el JSON de entrada.
	 * @param string $stringData Cadena en Base64 la cual contiene el JSON de la instancia a deserializar.
	 * @return mixed
	 */
	public function Deserializar($stringData)
	{
		return $this->FromJson(base64_decode($stringData));
	}

	/**
	 * Toma una cadena con máscara 'Y-m-d H:i:s' y la convierte a un DateTime. Si falla, lanza una excepción.
	 * @param string $str_fecha Cadena con máscara 'Y-m-d H:i:s' a convertir a DateTime.
	 * @throws InvalidArgumentException
	 * @return DateTime Valor resultante de la conversión. Puede devolver null.
	 */
	protected function VerificarDateTimeDesdeString($str_fecha)
	{
		if (is_null($str_fecha) || empty($str_fecha))
			return null;
		else
		{
			$error_fecha = date_parse_from_format('Y-m-d H:i:s', $str_fecha);
			if (($error_fecha['error_count'] > 0) || ($error_fecha['warning_count'] > 0))
				throw new InvalidArgumentException("Conversión de fecha inválida. Fecha: ".$str_fecha);
			else
				return DateTime::createFromFormat('Y-m-d H:i:s', $str_fecha);
		}
	}

	/**
	 *
	 * @param unknown $claseDestino
	 * @param unknown $instanciaOrigen
	 * @return unknown
	 */
	protected function Cast($instanciaOrigen) {
		// Reflection para la instancia de Origen
		$origenReflection = new ReflectionObject($instanciaOrigen);
		$propiedadesOrigen = $origenReflection->getProperties();

		// Reflection para la clase destino (this)
		$destinoReflection = new ReflectionObject($this);

		foreach ( $propiedadesOrigen as $propOrigen )
		{
			$propOrigen->setAccessible(true);
			$name = $propOrigen->getName();
			$value = $propOrigen->getValue($instanciaOrigen);

			if ($destinoReflection->hasProperty($name))
			{
				$propDest = $destinoReflection->getProperty($name);
				$propDest->setAccessible(true);
				$propDest->setValue ($this, $value);
			}
			else
			{
				$this->$name = $value;
			}
		}
		return $this;
	}
}
?>

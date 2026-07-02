<?php
class Encriptador {
	// Llave de encriptación
	private $llave;
	// Algoritmo
	private $algoritmo;
	
	public function __construct($p_llave = '', $p_algoritmo = PASSWORD_DEFAULT) {
		$this->llave 	 = $p_llave;
		$this->algoritmo = $p_algoritmo;
	}
	
	/**
	 * Se encripta una cadena
	 * @param  [string] $cadena Cadena a cifrar
	 * @return [string]         Cadena cifrada
	 */
	public function encriptar($cadena) {
		return base64_encode(password_hash($this->llave.$cadena, $this->algoritmo));
	}

	/**
	 * Se desencripta una cadena
	 * @param  [string] $cadena Cadena cifrada
	 * @return [string]         Cadena descifrada
	 */
	public function desencriptar($cadena) {
		return base64_decode($cadena);
	}

	/**
	 * Se verifica si un password coincide con una cadena cifrada
	 * @param  [string] 	$password  Password a verificar
	 * @param  [string] 	$cadena    Cadena con el cifrado para verificar con el password respectivo
	 * @return [boolean]    True|False
	 */
	public function verificar($password, $cadena) {
		return password_verify($this->llave.$password, $this->desencriptar($cadena));
	}
}
?>
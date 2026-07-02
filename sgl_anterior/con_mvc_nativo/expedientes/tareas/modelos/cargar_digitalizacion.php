<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class cargarDigitalizacionModel extends ModeloBaseMySQLi
{
	private $filtroSql  = "";

	public function conectar()
	{
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(2);
	}
	
	/**
	 * Se verifica la existencia de un Expediente/Nota/Recomendacion determinado/a
	 *
	 * @param array $clave
	 * @return boolean
	 */
	public function existe($clave) {
		$conexion = $this->conectar();
	
		if ( empty($clave['numero']) ) 
			$clave['numero'] = 0;
		if ( empty($clave['cuerpo']) ) 
			$clave['cuerpo'] = 0;
		if ( empty($clave['alcance']) ) 
			$clave['alcance'] = 0;
	
		$query = "SELECT tipo
				  FROM ".$this->tabla_expedientes."
				  WHERE anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = ".$clave['numero']."
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
				 ";
	
		$resultado = $this->ejecutarQuery($query);
	
		$dato = $this->obtenerFila($resultado);
	
		$this->desconectar($conexion);

		return ( $dato['tipo'] );
	}
}
?>
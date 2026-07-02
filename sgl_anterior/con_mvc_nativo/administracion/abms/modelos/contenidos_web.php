<?php
if (!isset($_SESSION)) {
	session_start();
}

class contenidosWebModel extends ModeloBaseMySQLi {

	public function conectar() {
		return parent::conectarDB(1);
	}

	/**
	 * Se obtiene la informacion de un registro determinado por su Id
	 *
	 * @param integer $id
	 * @return array $registro
	 */
	public function obtenerRegistro($id = 0) {

		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_contenidos_web . " WHERE id = " . $id;

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

	public function validarDatos($datos) {

		$datos['titulo'] = $this->revisarValorAtributo(strip_tags($datos['titulo']));

		$datos['contenido'] = $this->revisarValorContenidoTextArea($datos['contenido']);

		return $datos;
	}

	/**
	 * [insertar description]
	 * @param  [type] $datos [description]
	 * @return [type]        [description]
	 */
	public function insertar($datos) {

		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "INSERT INTO ".$this->tabla_contenidos_web." 
						(titulo, contenido)
				   VALUES(".$datos['titulo'].", 
				   		  ".$datos['contenido'].")";
				   
		//LibreriaGeneral::registrarLog("query_insertar", $query);
		
		if ( !$this->ejecutarQuery($query) ) {
			return false;
		}
		
		$this->desconectar($conexion);
		
		// Se audita
		$this->auditarEnAdministracion(
			"ALTA", 
			$this->tabla_contenidos_web, 
			"Se ha ingresado un contenido del sitio web.");
		
		return true;	
    }

	/**
	 * Se modifica la informacion
	 *
	 * @param array $datos
	 * @return boolean
	 */
	public function modificar($datos) {

		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "UPDATE  ".$this->tabla_contenidos_web." 
				  SET titulo = ".$datos['titulo'].",
					  contenido = ".$datos['contenido']."
				  WHERE id = ".$datos['id'];

		if ( !$this->ejecutarQuery($query) ) {
			return false;
		}
		
		$this->desconectar($conexion);
		
		// Se audita
		$this->auditarEnAdministracion(
			"MODIFICA", 
			$this->tabla_contenidos_web, 
			"Se ha modificado un contenido del sitio web.");
		
		return true;
	}
}
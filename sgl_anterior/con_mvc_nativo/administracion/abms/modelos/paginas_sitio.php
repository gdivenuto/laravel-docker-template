<?php
if (!isset($_SESSION))
	session_start();

class paginasSitioModel extends ModeloBaseMySQLi {

	public function conectar() {
		return parent::conectarDB(1);
	}

	/**
	 * Se obtienen las Categorias (enlaces del menú del sitio web)
	 * @return array   $datos 	Listado
	 */
	public function obtenerCategorias() {
		
		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_categorias_sitio . " WHERE habilitado = 1 ORDER BY id";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se obtienen las Páginas de una Categoria determinada
	 * @param  integer $id_categoria 	Identificador de la categoría
	 * @return array   $datos           Listado
	 */
	public function obtenerPorCategoria($id_categoria) {
		
		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_paginas_sitio . " 
				WHERE habilitado = 1 
				AND id_categoria = ".$id_categoria."
				ORDER BY id, id_categoria";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se guarda el seteo de las páginas del sitio web
	 * @param  array 	$datos Listado de páginas con su seteo
	 * @return boolean
	 */
	public function guardar($datos) {

		$conexion = $this->conectar();
		
		// Primero se setean todas sin mantenimiento
		$query = "UPDATE ".$this->tabla_paginas_sitio." SET en_mantenimiento = 0";
		
		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {
			$cant_en_mantenimiento = (isset($datos['chk_pagina'])) ? count($datos['chk_pagina']) : 0;

			// Por cada página seteada en mantenimiento
			for ($i=0; $i < $cant_en_mantenimiento; $i++) {
				
				// Se setea en mantenimiento
				$query = "UPDATE ".$this->tabla_paginas_sitio." 
						  SET en_mantenimiento = 1 
					  	  WHERE id = ".$datos['chk_pagina'][$i];
				
				if ( !$this->ejecutarQuery($query) ) {
					return false;
				}
			}
		}

		$this->desconectar($conexion);
		
		$this->auditarEnAdministracion(
			"MODIFICA", 
			$this->tabla_paginas_sitio, 
			"Se ha modificado la configuración de mantenimiento, en las páginas del sitio");
		
		return true;	
    }
}
<?php
if (!isset($_SESSION)) {
	session_start();
}

class opendataCatalogosModel extends ModeloBaseMySQLi
{
    public function conectar()
	{
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(1);
	}

    public function listar()
	{
		$conexion = $this->conectar();
		
		$filtro = "";
		
		// PARA FILTRAR POR FECHA
		if ( $this->filtro['f_fecha'] != '' )
			$filtro .= " AND fecha_emitido = '".$this->filtro['f_fecha']."'";
		
		// PARA FILTRAR POR TITULO
		if ( $this->filtro['f_titulo'] != '' )
			$filtro .= " AND titulo LIKE '%".$this->filtro['f_titulo']."%'";
		
		// PARA FILTRAR POR CONTENIDO
		if ( $this->filtro['f_descripcion'] != '' )
			$filtro .= " AND descripcion LIKE '%".$this->filtro['f_descripcion']."%'";
		
		// para limitar el listado
		$registro_inicial = (isset($this->filtro['inicio']) && $this->filtro['inicio'] != '') ? $this->filtro['inicio'] : 0;

		$sql = "SELECT * FROM " . $this->tabla_opendata_catalogos . "
				WHERE habilitado <> 3
				" . $filtro . "
			    ORDER BY " . $_SESSION['ultimo_campo'] . " " . $_SESSION['ultimo_sentido'] . "
			    LIMIT " . $registro_inicial . ", " . $this->filtro['rango'];

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
    }
    
	public function obtenerCantidad()
	{
		$conexion = $this->conectar();
		
		$filtro = "";

		// PARA FILTRAR POR FECHA
		if ( $this->filtro['f_fecha'] != '' )
			$filtro .= " AND fecha_emitido = '".$this->filtro['f_fecha']."'";
		
		// PARA FILTRAR POR TITULO
		if ( $this->filtro['f_titulo'] != '' )
			$filtro .= " AND titulo LIKE '%".$this->filtro['f_titulo']."%'";
		
		// PARA FILTRAR POR CONTENIDO
		if ( $this->filtro['f_descripcion'] != '' )
			$filtro .= " AND descripcion LIKE '%".$this->filtro['f_descripcion']."%'";
		
		$query = "SELECT COUNT(id) AS cantidad
				  FROM ".$this->tabla_opendata_catalogos." 
				  WHERE habilitado <> 3
				  ".$filtro;
		
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		return $dato['cantidad'];
	}
	
    public function obtenerHabilitados()
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT * FROM ".$this->tabla_opendata_catalogos." WHERE habilitado = 1 ORDER BY titulo";
		
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }
    
	public function obtenerUltimoId()
	{
		return parent::obtenerUltimoCodigo($this->tabla_opendata_catalogos, 'id');
	}
	
    public function obtenerRegistro($id)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT * FROM ".$this->tabla_opendata_catalogos." WHERE id = ".$id;
			   
		$resultado = $this->ejecutarQuery($sql);
		
		$registro = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $registro;
    }

    public function existe($titulo, $fecha) 
	{    
		$conexion = $this->conectar();
		
		$query = "SELECT id 
				  FROM ".$this->tabla_opendata_catalogos." 
				  WHERE titulo = '".$titulo."'
				  AND fecha_emitido = '".$this->formatearFechaMySQL($fecha)."'";
		 		  
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		// Si existe o no
		return ( $dato['id'] != '' );
    }

	public function validarDatos($datos) {

		$datos['titulo'] = $this->revisarValorAtributo($datos['titulo']);
		$datos['descripcion'] = $this->revisarValorAtributo($datos['descripcion']);
		$datos['fecha_emitido'] = $this->revisarValorFechaAtributo($datos['fecha_emitido']);
		$datos['fecha_modificado'] = $this->revisarValorFechaAtributo($datos['fecha_modificado']);
		$datos['lenguaje'] = $this->revisarValorAtributo($datos['lenguaje']);
		$datos['licencia'] = $this->revisarValorAtributo($datos['licencia']);
		$datos['derechos'] = $this->revisarValorAtributo($datos['derechos']);
		$datos['dimension'] = $this->revisarValorAtributo($datos['dimension']);
		$datos['icono'] = $this->revisarValorAtributo($datos['icono']);

		return $datos;
	}

    //	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
    public function noLoModificoOtroUsuario()
    {
		$conexion = $this->conectar();

		$query = "SELECT id
				  FROM " . $this->tabla_opendata_catalogos . "
				  WHERE id = " . $_SESSION['id_original'] . "
				  " . $this->adaptarValorStringParaFiltro('titulo') . "
				  " . $this->adaptarValorStringParaFiltro('descripcion') . "
				  " . $this->adaptarValorStringParaFiltro('fecha_emitido') . "
				  " . $this->adaptarValorStringParaFiltro('fecha_modificado') . "
				  " . $this->adaptarValorStringParaFiltro('lenguaje') . "
				  " . $this->adaptarValorStringParaFiltro('licencia') . "
				  " . $this->adaptarValorStringParaFiltro('derechos') . "
				  " . $this->adaptarValorStringParaFiltro('dimension') . "
				  " . $this->adaptarValorStringParaFiltro('icono') . "
				  AND habilitado = " . $_SESSION['habilitado_original'];

		$resultado = $this->ejecutarQuery($query);

		$datos = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ($datos['id']);	
    }

    public function insertar($datos) {
    	
		// Se obtiene el siguiente Id
		$datos['id'] = $this->obtenerUltimoId() + 1;

		$conexion = $this->conectar();
		
		$datos = $this->validarDatos($datos);
		
		$query = "INSERT INTO " . $this->tabla_opendata_catalogos . " (id, titulo, descripcion, fecha_emitido, fecha_modificado, lenguaje, licencia, derechos, dimension, icono, habilitado)
				  VALUES( " . $datos['id'] . ",
				  		  " . $datos['titulo'] . ",
						  " . $datos['descripcion'] . ",
						  " . $datos['fecha_emitido'] . ",
						  " . $datos['fecha_modificado'] . ",
						  " . $datos['lenguaje'] . ",
				  		  " . $datos['licencia'] . ",
						  " . $datos['derechos'] . ",
						  " . $datos['dimension'] . ",
						  " . $datos['icono'] . ",
						  1
						)";
		
		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {			
			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion("ALTA", $this->tabla_opendata_catalogos, "Se ingresa el Cat&aacute;logo: ".LibreriaGeneral::eliminarComillaSimple($datos['titulo']));
		}
		return true;	
    }
	
    public function modificar($datos) {

		$conexion = $this->conectar();
		
		$datos = $this->validarDatos($datos);
		
		$query = "UPDATE " . $this->tabla_opendata_catalogos . "
				  SET titulo = " . $datos['titulo'] . ",
				  	  descripcion = " . $datos['descripcion'] . ",
				  	  fecha_emitido = " . $datos['fecha_emitido'] . ",
				  	  fecha_modificado = " . $datos['fecha_modificado'] . ",
				  	  lenguaje = " . $datos['lenguaje'] . ",
				  	  licencia = " . $datos['licencia'] . ",
				  	  derechos = " . $datos['derechos'] . ",
				  	  dimension = " . $datos['dimension'] . ",
				  	  icono = " . $datos['icono'] . "
				  WHERE id = " . $datos['id'];
				
		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {			
			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion("MODIFICA", $this->tabla_opendata_catalogos, "Se modifica el Cat&aacute;logo: ".LibreriaGeneral::eliminarComillaSimple($datos['titulo']));
		}
		return true;
    }
    
    public function eliminar($id) {

    	// Previamente se obtiene la info para auditar
    	$info = $this->obtenerRegistro($id);

		$conexion = $this->conectar();
		
		$query = "DELETE FROM ".$this->tabla_opendata_catalogos." WHERE id = ".$id;
		
		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {			
			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion("BAJA", $this->tabla_opendata_catalogos, "Se elimina el Cat&aacute;logo: ".LibreriaGeneral::eliminarComillaSimple($info['titulo']));
		}
		return true;	
    }
    
	/**
	 * Se modifica el estado Habilitado|Deshabilitado
	 *
	 * @param  integer $id
	 * @param  integer $habilitado
	 * @return boolean true|false
	 */
	public function modificarEstado($id, $habilitado) {

		$conexion = $this->conectar();

		$valor_habilitado = ($habilitado == 1) ? 0 : 1;

		$query = "UPDATE " . $this->tabla_opendata_catalogos . " SET habilitado = $valor_habilitado WHERE id = " . $id;

		if (!$this->ejecutarQuery($query)) {
			return false;
		}

		$this->desconectar($conexion);

		return true;
	}

}
?>

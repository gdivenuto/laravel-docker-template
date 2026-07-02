<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class opendataDatasetsModel extends ModeloBaseMySQLi
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
		$limite = "";
		
		// PARA FILTRAR POR FECHA
		if ( $this->filtro['f_fecha'] != '' )
		{
			$filtro .= " AND fecha_emitido = '".$this->filtro['f_fecha']."'";
		}
		
		// PARA FILTRAR POR TITULO
		if ( $this->filtro['f_titulo'] != '' )
		{
			$filtro .= " AND titulo LIKE '%".$this->filtro['f_titulo']."%'";
		}
		
		// PARA FILTRAR POR CONTENIDO
		if ( $this->filtro['f_descripcion'] != '' )
		{
			$filtro .= " AND descripcion LIKE '%".$this->filtro['f_descripcion']."%'";
		}
		
		// PARA LIMITAR EL LISTADO
		if ( $this->filtro['rango'] != 0 )
		{
			$limite = " LIMIT ".$this->filtro['inicio'].", ".$this->filtro['rango'];
		}
		
		// SI SE ORDENA POR FECHA
		if ( $_SESSION['ultimo_campo'] == 'fecha_emitido' )
		{
			// SE ORDENA TAMBIEN POR HORA
			$orden = "ORDER BY fecha_emitido ".$_SESSION['ultimo_sentido'];
		}
		elseif ( $_SESSION['ultimo_campo'] == 'titulo' )
		{
			// SINO, SE ORDENA POR TITULO
			$orden = "ORDER BY titulo ".$_SESSION['ultimo_sentido'];
		}
		
		$sql = "SELECT *, 
					(SELECT titulo FROM ".$this->tabla_opendata_catalogos." WHERE id = id_catalogo) AS nombre_catalogo, 
					(SELECT titulo FROM ".$this->tabla_opendata_publicadores." WHERE id = id_publicador) AS nombre_publicador
				FROM ".$this->tabla_opendata_datasets." WHERE habilitado <> 3 ".$filtro." ".$orden;

		// SE AGREGA EL LIMITE A LA QUERY
		$sql .= $limite;
		
		$resultado = $this->ejecutarQuery($sql);
		
		// SI DEVUELVE ALGUN REGISTRO
		if ( $resultado ) 
			// SE CREA UN VECTOR CON EL RESULTADO PAGINADO
			$datos = $this->crearVector($resultado);
		else
			return false;
		
		$this->desconectar($conexion);
		
		return $datos;
    }
    
	public function obtenerCantidad()
	{
		$conexion = $this->conectar();
		
		$filtro = "";

		// PARA FILTRAR POR FECHA
		if ( $this->filtro['f_fecha'] != '' )
		{
			$filtro .= " AND fecha_emitido = '".$this->filtro['f_fecha']."'";
		}
		
		// PARA FILTRAR POR TITULO
		if ( $this->filtro['f_titulo'] != '' )
		{
			$filtro .= " AND titulo LIKE '%".$this->filtro['f_titulo']."%'";
		}
		
		// PARA FILTRAR POR CONTENIDO
		if ( $this->filtro['f_descripcion'] != '' )
		{
			$filtro .= " AND descripcion LIKE '%".$this->filtro['f_descripcion']."%'";
		}
		
		$query = "SELECT COUNT(id) AS cantidad
				  FROM ".$this->tabla_opendata_datasets." 
				  WHERE habilitado <> 3
				  ".$filtro;
		
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		return $dato['cantidad'];
	}
	
	public function obtenerUltimoId()
	{
		return parent::obtenerUltimoCodigo($this->tabla_opendata_datasets, 'id');
	}
	
    public function obtenerRegistro($id)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT * FROM ".$this->tabla_opendata_datasets." WHERE id = ".$id;
			   
		$resultado = $this->ejecutarQuery($sql);
		
		$registro = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $registro;
    }

    public function existe($titulo, $fecha) 
	{    
		$conexion = $this->conectar();
		
		$query = "SELECT id 
				  FROM ".$this->tabla_opendata_datasets." 
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
		$datos['id_catalogo'] = $this->revisarValorAtributo($datos['id_catalogo'], 0);
		$datos['id_publicador'] = $this->revisarValorAtributo($datos['id_publicador'], 0);
		$datos['identificador'] = $this->revisarValorAtributo($datos['identificador']);
		$datos['palabras_clave'] = $this->revisarValorAtributo($datos['palabras_clave']);
		$datos['lenguaje'] = $this->revisarValorAtributo($datos['lenguaje']);
		$datos['frecuencia'] = $this->revisarValorAtributo($datos['frecuencia'], 0);
		$datos['url'] = $this->revisarValorAtributo($datos['url']);
		$datos['licencia'] = $this->revisarValorAtributo($datos['licencia']);
		$datos['fuente'] = $this->revisarValorAtributo($datos['fuente']);
		$datos['nivel_acceso'] = $this->revisarValorAtributo($datos['nivel_acceso'], 0);

		return $datos;
	}

    //	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
    public function noLoModificoOtroUsuario() {

		$conexion = $this->conectar();

		$query = "SELECT id
				  FROM " . $this->tabla_opendata_datasets . "
				  WHERE id = " . $_SESSION['id_original'] . "
				  " . $this->adaptarValorStringParaFiltro('titulo') . "
				  " . $this->adaptarValorStringParaFiltro('descripcion') . "
				  " . $this->adaptarValorStringParaFiltro('fecha_emitido') . "
				  " . $this->adaptarValorStringParaFiltro('fecha_modificado') . "
				  " . $this->adaptarValorNumericoParaFiltro('id_catalogo') . "
				  " . $this->adaptarValorNumericoParaFiltro('id_publicador') . "
				  " . $this->adaptarValorStringParaFiltro('identificador') . "
				  " . $this->adaptarValorStringParaFiltro('palabras_clave') . "
				  " . $this->adaptarValorStringParaFiltro('lenguaje') . "
				  " . $this->adaptarValorNumericoParaFiltro('frecuencia') . "
				  " . $this->adaptarValorStringParaFiltro('url') . "
				  " . $this->adaptarValorStringParaFiltro('licencia') . "
				  " . $this->adaptarValorStringParaFiltro('fuente') . "
				  " . $this->adaptarValorNumericoParaFiltro('nivel_acceso') . "
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
		
		$query = "INSERT INTO " . $this->tabla_opendata_datasets . " 
						(id, titulo, descripcion, 
						 fecha_emitido, fecha_modificado, 
						 id_catalogo, id_publicador, 
						 identificador, palabras_clave, 
						 lenguaje, frecuencia, url, licencia, 
						 fuente, nivel_acceso, habilitado)
				  VALUES( " . $datos['id'] . ",
				  		  " . $datos['titulo'] . ",
						  " . $datos['descripcion'] . ",
						  " . $datos['fecha_emitido'] . ",
						  " . $datos['fecha_modificado'] . ",
						  " . $datos['id_catalogo'] . ",
						  " . $datos['id_publicador'] . ",
						  " . $datos['identificador'] . ",
						  " . $datos['palabras_clave'] . ",
						  " . $datos['lenguaje'] . ",
						  " . $datos['frecuencia'] . ",
						  " . $datos['url'] . ",
				  		  " . $datos['licencia'] . ",
						  " . $datos['fuente'] . ",
						  " . $datos['nivel_acceso'] . ",
						  1
						)";

		//LibreriaGeneral::registrarLog("query_insertar", $query);

		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {			

			$this->desconectar($conexion);

			// Se audita
			$this->auditarEnAdministracion(
				"ALTA", 
				$this->tabla_opendata_datasets, 
				"Se ingresa el Dataset: ".LibreriaGeneral::eliminarComillaSimple($datos['titulo'])
			);
		}
		return true;	
    }
	
    public function modificar($datos) {

		$conexion = $this->conectar();
		
		$datos = $this->validarDatos($datos);
		
		$query = "UPDATE " . $this->tabla_opendata_datasets . "
				  SET titulo = " . $datos['titulo'] . ",
				  	  descripcion = " . $datos['descripcion'] . ",
				  	  fecha_emitido = " . $datos['fecha_emitido'] . ",
				  	  fecha_modificado = " . $datos['fecha_modificado'] . ",
				  	  id_catalogo = " . $datos['id_catalogo'] . ",
				  	  id_publicador = " . $datos['id_publicador'] . ",
				  	  identificador = " . $datos['identificador'] . ",
				  	  palabras_clave = " . $datos['palabras_clave'] . ",
				  	  lenguaje = " . $datos['lenguaje'] . ",
				  	  frecuencia = " . $datos['frecuencia'] . ",
				  	  url = " . $datos['url'] . ",
				  	  licencia = " . $datos['licencia'] . ",
				  	  fuente = " . $datos['fuente'] . ",
				  	  nivel_acceso = " . $datos['nivel_acceso'] . "
				  WHERE id = " . $datos['id'];
		
		//LibreriaGeneral::registrarLog("query_modificar", $query);

		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {

			$this->desconectar($conexion);

			// Se audita
			$this->auditarEnAdministracion(
				"MODIFICA", 
				$this->tabla_opendata_datasets, 
				"Se modifica el Dataset: ".LibreriaGeneral::eliminarComillaSimple($datos['titulo'])
			);
		}
		return true;
    }
    
    public function eliminar($id) {

    	// Previamente se obtiene la info para auditar
    	$info = $this->obtenerRegistro($id);

		$conexion = $this->conectar();
		
		$query = "DELETE FROM ".$this->tabla_opendata_datasets." WHERE id = ".$id;
		
		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {

			$this->desconectar($conexion);

			// Se audita
			$this->auditarEnAdministracion(
				"BAJA", 
				$this->tabla_opendata_datasets, 
				"Se elimina el Dataset: ".LibreriaGeneral::eliminarComillaSimple($info['titulo'])
			);
		}
		return true;	
    }

    /**
     * Se obtienen los Recursos de un dataset determinado
     * @param  [type] $id_dataset [description]
     * @return [type]              [description]
     */
    public function obtenerRecursos($id_dataset) {

    	$conexion = $this->conectar();

    	$query = "SELECT * FROM " . $this->tabla_opendata_recursos . " WHERE id_dataset = ".$id_dataset." ORDER BY titulo";

    	$resultado = $this->ejecutarQuery($query);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }

    /**
     * Se ingresa el Recurso del dataset
     * @param  [integer] $id_dataset     [description]
     * @param  [string] $nombre_archivo  [description]
     * @return [boolean]                 [description]
     */
    public function insertarRecurso($id_dataset, $nombre_archivo) {

    	$conexion = $this->conectar();

    	// Se toma la extensión del archivo y se convierte a minúscula
	    $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));

	    // URL para el acceso y descarga
	    $url = URL_DATASET_RECURSOS.$id_dataset."/".$nombre_archivo;

	    $tipo_medio = LibreriaGeneral::definirTipoMedio($extension);

    	$query = "INSERT INTO " . $this->tabla_opendata_recursos . " 
    					(id_dataset, titulo, fecha_emitido, url_acceso, url_descarga, tipo_medio, formato, anio)
				  VALUES( " . $id_dataset . ",
				  		 '" . $nombre_archivo . "',
						 '" . date("Y-m-d") . "',
						 '" . $url . "',
						 '" . $url . "',
						  " . $tipo_medio . ",
						 '" . $extension . "',
				  		 '" . date("Y") . "'
						)";

		//LibreriaGeneral::registrarLog("query_insertarRecurso", $query);

		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {			
			$this->desconectar($conexion);
		}
		return true;
    }

    /**
     * Se elimina un Recurso
     * @param  integer  $id
     * @param  integer  $id_dataset
     * @return boolean
     */
    public function eliminarRecurso($id, $id_dataset) {

		$conexion = $this->conectar();
		
		$query = "DELETE FROM ".$this->tabla_opendata_recursos." WHERE id = ".$id." AND id_dataset = ".$id_dataset;
		
		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {

			$this->desconectar($conexion);

			// Se audita
			$this->auditarEnAdministracion("BAJA", $this->tabla_opendata_recursos, "Se elimina el Recurso: ".$id." del Dataset ".$id_dataset);
		}
		return true;	
    }
}
?>

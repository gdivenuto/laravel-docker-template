<?php
if (!isset($_SESSION)) {
	session_start();
}

class distincionesModel extends ModeloBaseMySQLi
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
		
		// PARA FILTRAR POR DISTINCION
		if ( $this->filtro['f_distincion'] != '' && $this->filtro['f_distincion'] != '0' )
		{
			$filtro .= " AND d_tipo LIKE '".$this->filtro['f_distincion']."%'";
		}
		
		// PARA FILTRAR POR FECHA
		if ( $this->filtro['f_fecha'] != '' )
		{
			$filtro .= " AND d_fecha = '".$this->filtro['f_fecha']."'";
		}
		
		// PARA FILTRAR POR ACTO
		if ( $this->filtro['f_acto'] != '' )
		{
			$filtro .= " AND d_acto LIKE '%".$this->filtro['f_acto']."%'";
		}
		
		// PARA FILTRAR POR NUMERO DEL EXPEDIENTE DEL HCD
		if ( $this->filtro['f_expediente'] != '' )
		{
			$filtro .= " AND d_expediente LIKE '%".$this->filtro['f_expediente']."%'";
		}
		
		// PARA LIMITAR EL LISTADO
		if ( $this->filtro['rango'] != 0 )
		{
			$limite = " LIMIT ".$this->filtro['inicio'].", ".$this->filtro['rango'];
		}
		
		$sql = "SELECT *
				FROM ".$this->tabla_distinciones."
				WHERE d_habilitado <> 3
				".$filtro."
				ORDER BY ".$_SESSION['ultimo_campo']." ".$_SESSION['ultimo_sentido']."
			    " . $limite;
		
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }
	
	public function obtenerCantidad()
	{
		$conexion = $this->conectar();
		
		$filtro = "";

		// PARA FILTRAR POR DISTINCION
		if ( $this->filtro['f_distincion'] != '' && $this->filtro['f_distincion'] != '0' )
		{
			$filtro .= " AND d_tipo LIKE '".$this->filtro['f_distincion']."%'";
		}
		
		// PARA FILTRAR POR FECHA
		if ( $this->filtro['f_fecha'] != '' )
		{
			$filtro .= " AND d_fecha = '".$this->filtro['f_fecha']."'";
		}
		
		// PARA FILTRAR POR ACTO
		if ( $this->filtro['f_acto'] != '' )
		{
			$filtro .= " AND d_acto LIKE '%".$this->filtro['f_acto']."%'";
		}
		
		// PARA FILTRAR POR NUMERO DEL EXPEDIENTE DEL HCD
		if ( $this->filtro['f_expediente'] != '' )
		{
			$filtro .= " AND d_expediente LIKE '%".$this->filtro['f_expediente']."%'";
		}
		
		$query = "SELECT COUNT(d_codigo) AS cantidad
				  FROM ".$this->tabla_distinciones." 
				  WHERE d_habilitado <> 3
				  ".$filtro;
		 		  
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		return $dato['cantidad'];
	}
	
	public function obtenerUltimoId()
	{
		return parent::obtenerUltimoCodigo($this->tabla_distinciones, 'd_codigo');
	}
	
    public function obtenerRegistro($d_codigo)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT * FROM ".$this->tabla_distinciones." WHERE d_codigo = ".$d_codigo;
			   
		$resultado = $this->ejecutarQuery($sql);
		
		$registro = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $registro;
    }

    public function existe($acto) 
	{    
		$conexion = $this->conectar();
		
		$query = "SELECT d_codigo FROM ".$this->tabla_distinciones." WHERE d_acto = '".$acto."'";
		 		  
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		// Si existe o no
		return ( $dato['d_codigo'] != '' );	
    }	

    public function validarDatos($datos)
    {
		$datos['d_tipo'] = $this->revisarValorAtributo($datos['d_tipo']);
		
		$datos['d_fecha'] = $this->revisarValorFechaAtributo($datos['d_fecha']);
		
		$datos['d_acto'] = $this->revisarValorAtributo(strip_tags($datos['d_acto']));
		
		$datos['d_expediente'] = $this->revisarValorAtributo(strip_tags($datos['d_expediente']));
		
		$datos['d_contenido'] = $this->revisarValorAtributo(strip_tags($datos['d_contenido']));
		
		return $datos;
    }
	
    //	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
    public function noLoModificoOtroUsuario()
    {
		$conexion = $this->conectar();
		
		$query = "SELECT d_codigo
				  FROM ".$this->tabla_distinciones." 
				  WHERE d_codigo = ".$_SESSION['d_codigo_original']."
				  ".$this->adaptarValorStringParaFiltro('d_tipo')."
				  ".$this->adaptarValorStringParaFiltro('d_fecha')."
				  ".$this->adaptarValorStringParaFiltro('d_acto')."
				  ".$this->adaptarValorStringParaFiltro('d_expediente')."
				  ".$this->adaptarValorStringParaFiltro('d_contenido')."
				  AND d_habilitado = ".$_SESSION['d_habilitado_original']."
				 ";
		
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ( $dato['d_codigo'] );		
    }	
	
    public function insertar($datos) {

		// Se obtiene el siguiente Id
		$datos['d_codigo'] = $this->obtenerUltimoId() + 1;

		$conexion = $this->conectar();
		
		$datos = $this->validarDatos($datos);
		
		$query = "INSERT INTO ".$this->tabla_distinciones." (d_codigo, d_tipo, d_fecha, d_acto, d_expediente, d_contenido, d_habilitado)
				  VALUES('".$datos['d_codigo']."', ".$datos['d_tipo'].", ".$datos['d_fecha'].", ".$datos['d_acto'].", ".$datos['d_expediente'].", ".$datos['d_contenido'].", 1);
				 ";
		
		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {			
			$this->desconectar($conexion);
			
			// Se obtiene el nombre de la distinción
			$tipo_distincion = $this->obtenerNombreTipo(LibreriaGeneral::eliminarComillaSimple($datos['d_tipo']));
			// Observación a auditar
			$observaciones = "Se ingresa la Distinción por ".$tipo_distincion.", ".html_entity_decode(LibreriaGeneral::eliminarComillaSimple($datos['d_acto']));
			// Se audita
			$this->auditarEnAdministracion("ALTA", $this->tabla_distinciones, $observaciones);
		}
		
		return true;	
    }
	
    public function modificar($datos) {
		$conexion = $this->conectar();
		
		$datos = $this->validarDatos($datos);
		
		$query = "UPDATE ".$this->tabla_distinciones."
				  SET d_tipo = ".$datos['d_tipo'].",
					  d_fecha = ".$datos['d_fecha'].",
					  d_acto = ".$datos['d_acto'].",
					  d_expediente = ".$datos['d_expediente'].",
					  d_contenido = ".$datos['d_contenido']."
				  WHERE d_codigo = ".$datos['d_codigo'];
				
		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {			
			$this->desconectar($conexion);
			
			// Se obtiene el nombre de la distinción
			$tipo_distincion = $this->obtenerNombreTipo(LibreriaGeneral::eliminarComillaSimple($datos['d_tipo']));
			// Observación a auditar
			$observaciones = "Se modifica la Distinción por ".$tipo_distincion.", ".html_entity_decode(LibreriaGeneral::eliminarComillaSimple($datos['d_acto']));
			// Se audita
			$this->auditarEnAdministracion("MODIFICA", $this->tabla_distinciones, $observaciones);
		}
		
		return true;	
    }
    
    public function eliminar($codigo) {
    	// Previamente se obtiene la info para auditar
    	$info = $this->obtenerRegistro($codigo);

		$conexion = $this->conectar();
		
		$query = "DELETE FROM ".$this->tabla_distinciones." WHERE d_codigo = ".$codigo;
		
		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {			
			$this->desconectar($conexion);

			// Observación a auditar
			$observaciones = "Se elimina la Distinción por ".$this->obtenerNombreTipo($info['d_tipo']).", ".html_entity_decode($info['d_acto']);
			// Se audita
			$this->auditarEnAdministracion("BAJA", $this->tabla_distinciones, $observaciones);
		}
		
		return true;	
    }

	private function obtenerNombreTipo($cod_tipo) {
		switch ($cod_tipo) {
			case "CE":
				$nombre_tipo = "Ciudadano Ejemplar";
				break;
			case "CI":
				$nombre_tipo = "Ciudadano Ilustre";
				break;
			case "CM":
				$nombre_tipo = "Ciudadano Marplatense";
				break;
			case "CA":
				$nombre_tipo = "Compromiso Ambiental";
				break;
			case "CS":
				$nombre_tipo = "Compromiso Social";
				break;
			case "DI":
				$nombre_tipo = "Deportista Insigne";
				break;
			case "HD":
				$nombre_tipo = "Hijo Dilecto";
				break;
			case "MA":
				$nombre_tipo = "Mérito Académico";
				break;
			case "MC":
				$nombre_tipo = "Mérito Ciudadano";
				break;
			case "MD":
				$nombre_tipo = "Mérito Deportivo";
				break;
			case "RE":
				$nombre_tipo = "Reconocimiento";
				break;
			case "SS":
				$nombre_tipo = "Servicio Solidario";
				break;
			case "VD":
				$nombre_tipo = "Vecino Destacado";
				break;
			case "VI":
				$nombre_tipo = "Visitante Ilustre";
				break;
			case "VN":
				$nombre_tipo = "Visitante Notable";
				break;
		}
		
		return $nombre_tipo;
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

		$query = "UPDATE " . $this->tabla_distinciones . " SET d_habilitado = $valor_habilitado WHERE d_codigo = " . $id;

		if (!$this->ejecutarQuery($query)) {
			return false;
		}

		$this->desconectar($conexion);

		return true;
	}

}
?>

<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class codareasModel extends ModeloBaseMySQLi
{
	public function conectar() {
		// Se conecta según el Id del sistema
		return parent::conectarDB(3);
	}

    public function listar()
	{    
		$conexion = $this->conectar();
		
		$filtro = "";
		$limite = "";
		
		// PARA FILTRAR POR CODIGO
		if ( $this->filtro['codigo'] != '' )
			$filtro .= " AND ca_id LIKE '".$this->filtro['codigo']."%'";
		
		// PARA FILTRAR POR NOMBRE
		if ( $this->filtro['nombre'] != '' )
			$filtro .= " AND ca_nombre LIKE '%".$this->filtro['nombre']."%'";
		
		// PARA LIMITAR EL LISTADO
		if ( $this->filtro['rango'] != 0 )
			$limite = " LIMIT ".$this->filtro['inicio'].", ".$this->filtro['rango'];
		
		$sql = "SELECT * FROM ".$this->tabla_codareas."
				WHERE ca_habilitado <> 3
				".$filtro."
				ORDER BY ".$_SESSION['ultimo_campo']." ".$_SESSION['ultimo_sentido']."
				".$limite."
			   ";
		
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }
	
    public function obtenerRegistro($ca_id)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT * FROM ".$this->tabla_codareas."
				WHERE ca_id = '".$ca_id."'
			   ";
		$resultado = $this->ejecutarQuery($sql);
		
		$registro = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $registro;
    }

    public function obtenerCantidad()
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT COUNT(ca_id) AS cantidad 
				FROM ".$this->tabla_codareas."
			   ";
		
		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['cantidad'];
    }

    public function existe($id) 
	{    
		$conexion = $this->conectar();
		
		$query = "SELECT ca_id 
				  FROM ".$this->tabla_codareas." 
				  WHERE ca_id = '".$id."'
				 ";
		 		  
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		// Si existe o no
		return ( $dato['ca_id'] != '' );	
    }	

    public function validarDatos($datos)
    {
		// Nombre
		$datos['ca_nombre']     = $this->revisarValorAtributo($datos['ca_nombre']);
		
		// Tipo
		$datos['ca_tipo']       = $this->revisarValorAtributo($datos['ca_tipo']);
		
		// Depende de
		$datos['ca_depende_de'] = $this->revisarValorAtributo($datos['ca_depende_de']);
		
		// Mail
		$datos['ca_mail']       = $this->revisarValorAtributo($datos['ca_mail']);
		
		// Teléfono
		$datos['ca_telefono']   = $this->revisarValorAtributo($datos['ca_telefono']);

		return $datos;
    }
	
    //	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
    public function verificarRegistroEntero()
    {
		$conexion = $this->conectar();
		
		$filtro_nombre     = $this->adaptarValorStringParaFiltro('ca_nombre');
		
		$filtro_depende_de = $this->adaptarValorStringParaFiltro('ca_depende_de');
		
		$filtro_mail       = $this->adaptarValorStringParaFiltro('ca_mail');
		
		$filtro_telefono   = $this->adaptarValorStringParaFiltro('ca_telefono');

		$query = "SELECT ca_id
				  FROM ".$this->tabla_codareas." 
				  WHERE ca_id = '".$_SESSION['ca_id_original']."'
				  ".$filtro_nombre."
				  AND ca_tipo = '".$_SESSION['ca_tipo_original']."'
				  ".$filtro_depende_de."
				  ".$filtro_mail."
				  ".$filtro_telefono."
				  AND ca_habilitado = ".$_SESSION['ca_habilitado_original']."
				 ";
		
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ( $dato['ca_id'] );		
    }	

    public function insertar($datos)
	{	
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$datos = $this->validarDatos($datos);
		
		$query = "INSERT INTO ".$this->tabla_codareas." (ca_id, ca_nombre, ca_tipo, ca_depende_de, ca_mail, ca_telefono, ca_habilitado)
				  VALUES('".$datos['ca_id']."', ".$datos['ca_nombre'].", ".$datos['ca_tipo'].", ".$datos['ca_depende_de'].", ".$datos['ca_mail'].", ".$datos['ca_telefono'].", 1);
				 ";
		
		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{			
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION

			$this->desconectar($conexion);
		
			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaPersonalModel();
			
			$datos_log = Array();
			$datos_log['operacion']     = "ALTA";
			$datos_log['tabla']         = $this->tabla_codareas;
			$datos_log['legajo']        = 0;
			$datos_log['observaciones'] = "Se ingresa un &Aacute;rea.";
			
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		return true;	
    }
	
    public function modificar($datos)
	{	
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$datos = $this->validarDatos($datos);
		
		$query = "UPDATE ".$this->tabla_codareas."
				  SET ca_nombre = ".$datos['ca_nombre'].",
					  ca_tipo = ".$datos['ca_tipo'].",
					  ca_depende_de = ".$datos['ca_depende_de'].",
					  ca_mail = ".$datos['ca_mail'].",
					  ca_telefono = ".$datos['ca_telefono'].",
					  ca_habilitado = ".$datos['ca_habilitado']."
				  WHERE ca_id = '".$datos['ca_id']."'
				 ";
		
		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{			
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION
	
			$this->desconectar($conexion);
		
			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaPersonalModel();
			
			$datos_log = Array();
			$datos_log['operacion']     = "MODIFICA";
			$datos_log['tabla']         = $this->tabla_codareas;
			$datos_log['legajo']        = 0;
			$datos_log['observaciones'] = "Se modifica un &Aacute;rea.";
			
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		return true;	
    }
    
    public function eliminar($id)
	{	
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		// SE VERIFICA SI EL AREA ESTA ASIGNADA A UN LEGAJO 
		$queryA = "SELECT a_id_area FROM ".$this->tabla_areas." WHERE a_id_area = '".$id."'";
		$resultado = $this->ejecutarQuery($queryA, $conexion);
		
		$dato = $this->obtenerFila($resultado);
		
		// SI ESTA ASIGNADO NO SE DEBE ELIMINAR
		if ( $dato['a_id_area'] )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else // SI NO ESTA ASIGNADO A UN LEGAJO PUEDE ELIMINARSE
		{
			$query = "DELETE FROM ".$this->tabla_codareas." WHERE ca_id = '".$id."'";
			
			if ( !$this->ejecutarQuery($query) )
			{
				$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
				return false;
			}
			else
			{			
				$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION

				$this->desconectar($conexion);
		
				//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
				$modelo = new auditoriaPersonalModel();
				
				$datos_log = Array();
				$datos_log['operacion']     = "BAJA";
				$datos_log['tabla']         = $this->tabla_codareas;
				$datos_log['legajo']        = 0;
				$datos_log['observaciones'] = "Se elimina un &Aacute;rea.";
				
				//SE CARGA EN auditoria EL MOVIMIENTO
				$modelo->registrarMovimiento($datos_log);
			}
		}
		
		return true;	
    }
    
    public function listadoCombo()
	{	
		$conexion = $this->conectar();
		
		$sql = "SELECT * FROM ".$this->tabla_codareas."
				WHERE ca_habilitado = 1
				ORDER BY ca_nombre";
		
		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }	
	
    public function buscarDescripcionCodArea($ca_depende_de)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT ca_id, ca_nombre
				FROM ".$this->tabla_codareas."
				WHERE ca_id = '".$ca_depende_de."'";
		
		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }
}
?>

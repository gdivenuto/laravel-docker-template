<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class temasModel extends ModeloBaseMySQLi
{
	public function conectar()
	{
		return parent::conectarDB(2);
	}
		
	public function listadoPorExpediente()
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT T.*, CT.codigo_tema, CT.descripcion_tema
				FROM (SELECT * FROM ".$this->tabla_temas."
					  WHERE anio = ".$this->filtro['anio']."
					  AND tipo = '".$this->filtro['tipo']."'
					  AND numero = ".$this->filtro['numero']."
					  AND cuerpo = ".$this->filtro['cuerpo']."
					  AND alcance = ".$this->filtro['alcance']."
					 ) T
				LEFT JOIN ".$this->tabla_codtemas." CT ON CT.id_codtema = T.id_codtema
			   "; 
		// 03/01/2012	   
		// WHERE CT.habilitado_tema = '1'
			   
		// LEFT JOIN expedientes E ON (E.anio = T.anio AND E.tipo = T.tipo AND E.numero = T.numero AND E.cuerpo = T.cuerpo AND E.alcance = T.alcance)	   
		
		//fputs(fopen('sqlTemasPorExpediente.txt', 'w'),print_r($sql, true));  
		 
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	//	SE VERIFICA LA EXISTENCIA DE UN EXPEDIENTE DETERMINADO  
	public function existe($clave)
	{
		$conexion = $this->conectar();
		
		$query = "SELECT id_codtema
				  FROM ".$this->tabla_temas." 
				  WHERE anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = '".$clave['numero']."'
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
				  AND id_codtema = ".$clave['id_codtema']."
				 ";
		
		$resultado = $this->ejecutarQuery($query, $conexion);	
		
		$dato = $this->obtenerFila($resultado);
		
		if(!$dato['id_codtema'])
		{
			return false;
		}
		
		$this->desconectar($conexion);
		
		return true;	
	}	
	
    public function agregar($datos)
	{
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		//SE CARGA EL tema PARA DICHO EXPEDIENTE
		$query = "INSERT INTO ".$this->tabla_temas." (anio, tipo, numero, cuerpo, alcance, id_codtema, id_usuario)
				  VALUES ( ".$datos['anio'].",
						  '".$datos['tipo']."',
						   ".$datos['numero'].",
						   ".$datos['cuerpo'].",
						   ".$datos['alcance'].",
						   ".$datos['id_codtema'].",
						   ".$datos['id_usuario']."
						 ) ";
		//fputs(fopen('query_agregarTema.txt','w'),print_r($query, true));		
		
		if ( !$this->ejecutarQuery($query, $conexion) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}	
		else
		{// LA CONSULTA FUE EXITOSA
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION
		}
		
		$this->desconectar($conexion);
		
		return true;	
	}

	public function eliminar($clave)
	{
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$query = "DELETE FROM ".$this->tabla_temas."
				  WHERE anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = ".$clave['numero']."
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
				  AND id_codtema = ".$clave['id_codtema']."
				 ";
		
		if ( !$this->ejecutarQuery($query, $conexion) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}	
		else
		{	// LA CONSULTA FUE EXITOSA
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION
		}
		
		$this->desconectar($conexion);
		
		return true;	
	}
	
}
?>

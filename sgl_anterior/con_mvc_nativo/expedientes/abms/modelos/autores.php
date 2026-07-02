<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class autoresModel extends ModeloBaseMySQLi
{
	public function conectar()
	{
		return parent::conectarDB(2);
	}
			
	public function listadoTotal()
	{
		$conexion = $this->conectar();
		
		//para filtrar por anio
		$filtro = "";
		if ($this->filtro['anio'] != ''){
			$filtro = " WHERE anio = ".$this->filtro['anio']."";
		}
		//para filtrar por tipo
		if ($this->filtro['tipo'] != ''){
			$filtro .= " AND tipo = '".$this->filtro['tipo']."'";
		}
		//para filtrar por numero
		if ($this->filtro['numero'] != ''){
			$filtro .= " AND numero = ".$this->filtro['numero']."";
		}
		//para filtrar por cuerpo
		if ($this->filtro['cuerpo'] != ''){
			$filtro .= " AND cuerpo = ".$this->filtro['cuerpo']."";
		}
		//para filtrar por alcance
		if ($this->filtro['alcance'] != ''){
			$filtro .= " AND alcance = ".$this->filtro['alcance']."";
		}
		//para filtrar por tipo de autor
		if ($this->filtro['autor_tipo'] != ''){
			$filtro .= " AND autor_tipo = '".$this->filtro['autor_tipo']."'";
		}
		//para filtrar por codigo de autor
		if ($this->filtro['autor_codigo'] != ''){
			$filtro .= " AND autor_codigo = '".$this->filtro['autor_codigo']."'";
		}
				
		$sql = "SELECT * FROM ".$this->tabla_autores."
			   ".$filtro."
			   ORDER BY ".$this->filtro['campo_orden']."
			   ";
		
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function listadoPorExpediente()
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT A.*, L.descripcion_grp AS autor_descripcion 
				FROM (SELECT * FROM ".$this->tabla_autores."
					  WHERE anio = ".$this->filtro['anio']."
					  AND tipo = '".$this->filtro['tipo']."'
					  AND numero = ".$this->filtro['numero']."
					  AND cuerpo = '".$this->filtro['cuerpo']."'
					  AND alcance = ".$this->filtro['alcance']."
					  ) A
				LEFT JOIN ".$this->tabla_lugares." L ON (L.tipo_grp = A.autor_tipo AND L.codigo_grp = A.autor_codigo)
		       ";
		// 03/01/2012
		// WHERE L.habilitado_grp = '1'
		
		// LEFT JOIN expedientes E ON (E.anio = A.anio AND E.tipo = A.tipo AND E.numero = A.numero AND E.cuerpo = A.cuerpo AND E.alcance = A.alcance)	   
		
		//fputs(fopen('sqlAutoresPorExpediente.txt', 'w'),print_r($sql, true));  
		
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	//	SE VERIFICA LA EXISTENCIA DE UN EXPEDIENTE DETERMINADO  
	public function existe($clave)
	{	
		$conexion = $this->conectar();
		
		$query = "SELECT tipo
				  FROM ".$this->tabla_autores." 
				  WHERE anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = '".$clave['numero']."'
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
				  AND autor_tipo = '".$clave['autor_tipo']."'
				  AND autor_codigo = '".$clave['autor_codigo']."'
				  ";
		
		$resultado = $this->ejecutarQuery($query, $conexion);	
		
		if (!$resultado[0]->tipo){
			return false;
		}
		
		return true;	
	}	

    public function agregar($datos)
	{
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		//SE CARGA EL autor PARA DICHO EXPEDIENTE
		$query = "INSERT INTO ".$this->tabla_autores." (anio, tipo, numero, cuerpo, alcance, autor_tipo, autor_codigo, id_usuario)
				  VALUES ( ".$datos['anio'].",
						  '".$datos['tipo']."',
						   ".$datos['numero'].",
						   ".$datos['cuerpo'].",
						   ".$datos['alcance'].",
						  '".$datos['autor_tipo']."',
						  '".$datos['autor_codigo']."',
						   ".$datos['id_usuario']."
						 ) ";
		
		//fputs(fopen('query_agregarAutor.txt','w'),print_r($query,true));
		
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
	
	public function eliminar($clave)
	{	
		$conexion = $this->conectar();
		
		$query = "DELETE FROM ".$this->tabla_autores."
				  WHERE anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = '".$clave['numero']."'
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
				  AND autor_tipo = '".$clave['autor_tipo']."'
				  AND autor_codigo = '".$clave['autor_codigo']."'
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

    public function listadoCompleto()
    {
		$conexion = $this->conectar();
		
        $filtro_tipo = " WHERE L.tipo_grp = 'C'";
		if ( isset($autor_tipo) )
		{
			$filtro_tipo =  " WHERE L.tipo_grp = '".$autor_tipo."'";
		} 

		$sql = "SELECT L.* 
				FROM ".$this->tabla_lugares." L
				LEFT JOIN ".$this->tabla_autores." A 
				ON (L.tipo_grp = A.autor_tipo AND L.codigo_grp = A.autor_codigo)   
				".$filtro_tipo."                    
		       ";
		//fputs(fopen('sql_listadoCompleto.txt','w'),print_r($sql, true)); 
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		//se crea un vector asociativo para la vista
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
	}
	
    public function buscarNombreAutor($autor_tipo, $autor_codigo)
	{	
		$conexion = $this->conectar();
		
		$sql = "SELECT L.descripcion_grp AS descripcion_autor 
                FROM (SELECT * FROM ".$this->tabla_lugares."
					  WHERE tipo_grp = '".$autor_tipo."'
					  AND codigo_grp = '".$autor_codigo."'
                     ) L
                LEFT JOIN ".$this->tabla_autores." A 
                ON (L.tipo_grp = A.autor_tipo AND L.codigo_grp = A.autor_codigo)                        
		       ";
		//fputs(fopen('sql_buscarNombreAutor.txt','w'),print_r($sql, true)); 
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['descripcion_autor'];
	}
	
	public function buscarCodigoNombreAutor($autor_tipo, $autor_codigo)
	{	
		$conexion = $this->conectar();
		
		$sql = "SELECT L.codigo_grp AS codigo_autor , L.descripcion_grp AS descripcion_autor 
                FROM (SELECT * FROM ".$this->tabla_lugares."
					  WHERE tipo_grp = '".$autor_tipo."'
					  AND codigo_grp = '".$autor_codigo."'
                     ) L
                LEFT JOIN ".$this->tabla_autores." A ON (L.tipo_grp = A.autor_tipo AND L.codigo_grp = A.autor_codigo)                        
		       ";
		//fputs(fopen('sql_buscarNombreAutor.txt','w'),print_r($sql, true)); 
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
	}
	
}
?>

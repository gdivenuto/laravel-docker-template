<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class cargarProyectosModel extends ModeloBaseMySQLi
{
	private $filtroSql  = "";

	public function conectar()
	{
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(2);
	}
	
	public function listadoTotal($accion = '')
	{	
		$conexion = $this->conectar();
		
		//SE FILTRA POR anio
		if ( $this->filtro['anio'] != '' )
		{
			$this->filtroSql .= " WHERE anio = ".$this->filtro['anio']."";
		}
		
		//SE FILTRA POR tipo
		if ( $this->filtro['tipo'] != '' )
		{
			$this->filtroSql .= " AND tipo = '".$this->filtro['tipo']."'";
		}
		
		//SE FILTRA POR numero
		if ( $this->filtro['numero'] != '' )
		{
			$this->filtroSql .= " AND numero = ".$this->filtro['numero']."";
		}
		
		//SE FILTRA POR cuerpo
		if ( $this->filtro['cuerpo'] != '' )
		{
			$this->filtroSql .= " AND cuerpo = ".$this->filtro['cuerpo']."";
		}
		
		//SE FILTRA POR alcance
		if ( $this->filtro['alcance'] != '' )
		{
			$this->filtroSql .= " AND alcance = ".$this->filtro['alcance']."";
		}
		
		$limite = "";
		if ( isset($this->filtro['inicio']) && isset($this->filtro['rango']) )
		{
			$limite = "LIMIT ".$this->filtro['inicio'].", ".$this->filtro['rango']."";
		}
		
		//SI SE Edita NO SE NECESITA EL Limite EN LA QUERY
		if ( $accion == 'editar' )
		{
			$limite = "";
		}
		
		$sql = "SELECT E.*, 
					   L.descripcion_grp AS iniciador_descripcion,
					   Cat.codigo_categoria AS codigo_categoria, Cat.descripcion_categoria AS descripcion_categoria
				FROM (SELECT * FROM ".$this->tabla_expedientes."
					  ".$this->filtroSql."
					 )E
				LEFT JOIN ".$this->tabla_lugares." L ON (L.tipo_grp = E.iniciador_tipo AND L.codigo_grp = E.iniciador_codigo)
				LEFT JOIN ".$this->tabla_codcategoria." Cat ON (Cat.id_codcategoria = E.id_codcategoria)
				ORDER BY E.".$_SESSION['ultimo_campo']." ".$_SESSION['ultimo_sentido']."
			    ".$limite."
			   ";
		
		$resultado = $this->ejecutarQuery($sql);
		
		if (!$resultado)
			return false;
		else
			$datos = $this->crearVector($resultado);// SE CREA UN VECTOR CON EL RESULTADO
		
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function obtenerCantidad()
	{	
		$conexion = $this->conectar();
		
		$sql = "SELECT COUNT(anio, tipo, numero, cuerpo, alcance) AS cantidad FROM ".$this->tabla_expedientes;
		 
		$resultado = $this->ejecutarQuery($sql);
		
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['cantidad'];
	}

	/**
	 * Se verifica la existencia de un Expediente/Nota/Recomendacion determinado/a
	 *
	 * @param array $clave
	 * @return boolean
	 */
	public function existe($clave)
	{
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
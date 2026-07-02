<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class fichaModel extends ModeloBaseMySQLi
{
    public function conectar()
	{
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(2);
	}
	
	public function obtenerDatos(){
		
	    $conexion = $this->conectar();
	    
	    $sql = "SELECT E.anio, E.tipo, E.numero, E.cuerpo, E.alcance, E.caratula, E.fecha_entrada_expe, E.id_codcategoria, E.iniciador_tipo, E.iniciador_codigo, 
					   E.marca_comision, E.agregado_anio, E.agregado_tipo, E.agregado_numero, E.agregado_cuerpo, E.agregado_alcance, E.observaciones_expe,
					   (SELECT CCat.descripcion_categoria FROM ".$this->tabla_codcategoria." CCat WHERE CCat.codigo_categoria = E.id_codcategoria)AS categoria, 
					   (SELECT Ini.descripcion_grp FROM ".$this->tabla_lugares." Ini WHERE Ini.tipo_grp = iniciador_tipo AND Ini.codigo_grp = iniciador_codigo)AS iniciador
				FROM ".$this->tabla_expedientes." AS E
				WHERE E.anio = ".$this->filtro['anio']."
				AND E.tipo = '".$this->filtro['tipo']."'
				AND E.numero = ".$this->filtro['numero']."
				AND E.cuerpo = ".$this->filtro['cuerpo']."
				AND E.alcance = ".$this->filtro['alcance']."
			   ";
		
	    //fputs(fopen("query_obtenerDatos_para_ficha.txt",'w'),print_r($sql,true));
	    
	    $resultado = $this->ejecutarQuery($sql, $conexion);
	    
	    if (!$resultado) return false;
	    else $datos = $this->crearVector($resultado);// SE CREA UN VECTOR CON EL RESULTADO
	    
	    //SE OBTIENEN LOS Temas DEL Expediente 
	    $datos['temas'] = $this->obtenerTemasFicha();
	    
	    //SE OBTIENEN LOS Autores DEL Expediente 
	    $datos['autores'] = $this->obtenerAutoresFicha();
	    
	    //SE OBTIENEN LOS Giros DEL Expediente 
	    $datos['estado'] = $this->obtenerEstadoFicha();
	    
	    //SE OBTIENEN LOS Proyectos DEL Expediente 
	    $datos['proyectos'] = $this->obtenerProyectosFicha();
	    
	    //SE OBTIENEN LOS Antecedentes DEL Expediente 
	    $datos['giros'] = $this->obtenerGirosFicha();	
	    	      
	    //SE OBTIENEN LOS Giros DEL Expediente 
	    $datos['antecedentes'] = $this->obtenerAntecedentesFicha();				

	    $this->desconectar($conexion);
	    
	    return $datos;
	}
	
	public function obtenerProyectosFicha()
	{	
		$conexion = $this->conectar();
		
		$sql = "SELECT DISTINCT CP.descripcion_proyecto, P.extracto, P.orden_proyecto
				FROM (SELECT anio, tipo, numero, id_codproyecto, extracto, orden_proyecto
					  FROM ".$this->tabla_proyectos." 
					  WHERE anio = ".$this->filtro['anio']."
					  AND tipo = '".$this->filtro['tipo']."'
					  AND numero = ".$this->filtro['numero']."
					  AND cuerpo = ".$this->filtro['cuerpo']."
					  AND alcance = ".$this->filtro['alcance']."
					 )P
				LEFT JOIN ".$this->tabla_codproyectos." CP ON P.id_codproyecto = CP.id_codproyecto 
				LEFT JOIN ".$this->tabla_expedientes." E ON ( E.anio = P.anio AND E.tipo = P.tipo AND E.numero = P.numero )
		      ";
		//fputs(fopen('sql_obtenerProyectosFicha.txt','w'),print_r($sql, true));
		
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		//se crea un vector asociativo para la vista
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function obtenerGirosFicha()
	{	
	    $conexion = $this->conectar();
	    
	    $sql = "SELECT G.*, L.descripcion_grp AS comision
				FROM (SELECT * FROM ".$this->tabla_giros." 
					  WHERE anio = ".$this->filtro['anio']."
					  AND tipo = '".$this->filtro['tipo']."'
					  AND numero = ".$this->filtro['numero']."
					  AND cuerpo = ".$this->filtro['cuerpo']."
					  AND alcance = ".$this->filtro['alcance']."
				     )G
				LEFT JOIN ".$this->tabla_expedientes." E ON ( E.anio = G.anio AND E.tipo = G.tipo AND E.numero = G.numero AND E.cuerpo = G.cuerpo AND E.alcance = G.alcance)
				LEFT JOIN ".$this->tabla_lugares." L ON L.tipo_grp = G.comision_tipo AND L.codigo_grp = G.comision_codigo
			   ";
	    //fputs(fopen('sql_obtenerGirosFicha.txt','w'),print_r($sql, true));
	    
	    $resultado = $this->ejecutarQuery($sql, $conexion);
	    
	    //se crea un vector asociativo para la vista
	    $datos = $this->crearVector($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $datos;
	}
	
	public function obtenerTemasFicha()
	{	
	    $conexion = $this->conectar();
	    
	    $sql = "SELECT CT.descripcion_tema 
				FROM (SELECT id_codtema, anio, tipo, numero, cuerpo, alcance 
					  FROM ".$this->tabla_temas." 
					  WHERE anio = ".$this->filtro['anio']."
					  AND tipo = '".$this->filtro['tipo']."'
					  AND numero = ".$this->filtro['numero']."
					  AND cuerpo = ".$this->filtro['cuerpo']."
					  AND alcance = ".$this->filtro['alcance']."
					 )T
				LEFT JOIN ".$this->tabla_codtemas." CT ON CT.id_codtema = T.id_codtema
				LEFT JOIN ".$this->tabla_expedientes." E ON (E.anio = T.anio AND E.tipo = T.tipo AND E.numero = T.numero AND E.cuerpo = T.cuerpo AND E.alcance = T.alcance)
				WHERE CT.habilitado_tema = 1
		      ";
	    //fputs(fopen('sql_obtenerTemasFicha.txt','w'),print_r($sql, true));
	    
	    $resultado = $this->ejecutarQuery($sql, $conexion);
	    
	    $datos = $this->crearVector($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $datos;
	}
	
	public function obtenerAutoresFicha()
	{	
		$conexion = $this->conectar();
		
		$sql = "SELECT A.autor_codigo, L.descripcion_grp
				FROM (SELECT autor_tipo, autor_codigo, anio, tipo, numero, cuerpo, alcance 
					  FROM ".$this->tabla_autores."
					  WHERE anio = ".$this->filtro['anio']."
					  AND tipo = '".$this->filtro['tipo']."'
					  AND numero = ".$this->filtro['numero']."
					  AND cuerpo = ".$this->filtro['cuerpo']."
					  AND alcance = ".$this->filtro['alcance']."
					  )A
				LEFT JOIN ".$this->tabla_lugares." L ON (L.tipo_grp = A.autor_tipo AND L.codigo_grp = A.autor_codigo)
				LEFT JOIN ".$this->tabla_expedientes." E ON (E.anio = A.anio AND E.tipo = A.tipo AND E.numero = A.numero AND E.cuerpo = A.cuerpo AND E.alcance = A.alcance)
				WHERE L.habilitado_grp = 1
		       ";
		//fputs(fopen('sql_obtenerAutoresFicha.txt','w'),print_r($sql, true));
		 
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		//se crea un vector asociativo para la vista
		$datos = $this->crearVector($resultado);
				
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function obtenerEstadoFicha()
	{	
		$conexion = $this->conectar();
		
		$sql = "SELECT Est.id_codestado, Est.fecha_estado AS fecha_estado, CEst.nombre_estado AS nombre_estado
				FROM 
				( SELECT id_codestado, fecha_estado 
				  FROM ".$this->tabla_estados."
				  WHERE anio = ".$this->filtro['anio']."
				  AND tipo = '".$this->filtro['tipo']."'
				  AND numero = ".$this->filtro['numero']."
				  AND cuerpo = ".$this->filtro['cuerpo']."
				  AND alcance = ".$this->filtro['alcance']."
				  ORDER BY fecha_estado DESC, orden_estado DESC
				  LIMIT 0,1	    
				) Est
				LEFT JOIN ".$this->tabla_codestados." CEst
				ON CEst.id_codestado = Est.id_codestado    
		       ";
		//fputs(fopen('sql_obtenerEstadoFicha.txt','w'),print_r($sql, true));
		  
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		//se crea un vector asociativo para la vista
		$datos = $this->crearVector($resultado);
				
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function obtenerComisionFicha($anio, $tipo, $numero)
	{	
		$conexion = $this->conectar();
		
		$sql = "SELECT L.descripcion_grp AS comision, G.fecha_entrada_giro AS fecha_giro
			    FROM (SELECT * FROM ".$this->tabla_giros."
					  WHERE anio = ".$anio."
					  AND tipo = '".$tipo."'
					  AND numero = ".$numero."
					 ) G
			    LEFT JOIN ".$this->tabla_lugares." L ON (L.tipo_grp = G.comision_tipo AND L.codigo_grp = G.comision_codigo)
			    LEFT JOIN ".$this->tabla_expedientes." E ON (E.anio = G.anio AND E.tipo = G.tipo AND E.numero = G.numero AND E.cuerpo = G.cuerpo AND E.alcance = G.alcance)
				LEFT JOIN ".$this->tabla_estados." Est ON (Est.anio = E.anio AND Est.tipo = E.tipo AND Est.numero = E.numero AND Est.cuerpo = E.cuerpo AND Est.alcance = E.alcance)
			    LEFT JOIN ".$this->tabla_codestados." CEst ON CEst.id_codestado = Est.id_codestado
				WHERE (CEst.id_codestado = (SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 3) OR 
					   CEst.id_codestado = (SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 16) OR 
					   CEst.id_codestado = (SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 79)
					  )
				AND G.comision_codigo = '003'
				AND G.fecha_salida_giro IS NULL OR G.fecha_salida_giro = '0000-00-00'
				ORDER BY G.fecha_entrada_giro DESC
				
			   ";
		//fputs(fopen('sql_obtenerComisionFicha.txt','w'),print_r($sql, true));
		   
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		//se crea un vector asociativo para la vista
		$datos = $this->crearVector($resultado);
				
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function obtenerAntecedentesFicha()
	{	
	    $conexion = $this->conectar();
	    
	    $sql = "SELECT A.*
				FROM (SELECT * FROM ".$this->tabla_antecedentes." 
					  WHERE anio = ".$this->filtro['anio']."
					  AND tipo = '".$this->filtro['tipo']."'
					  AND numero = ".$this->filtro['numero']."
					  AND cuerpo = ".$this->filtro['cuerpo']."
					  AND alcance = ".$this->filtro['alcance']."
					 )A
				LEFT JOIN ".$this->tabla_expedientes." E 
				ON (E.anio = A.anio AND E.tipo = A.tipo AND E.numero = A.numero AND E.cuerpo = A.cuerpo AND E.alcance = A.alcance)	
		    ";
	    //fputs(fopen('sql_listarPorAntecedente.txt','w'),print_r($sql, true));
	    
	    $resultado = $this->ejecutarQuery($sql, $conexion);
	    
	    //se crea un vector asociativo para la vista
	    $datos = $this->crearVector($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $datos;
	}

}
?>

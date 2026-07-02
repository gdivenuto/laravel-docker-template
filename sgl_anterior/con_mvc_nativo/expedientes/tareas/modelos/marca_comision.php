<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class marcaComisionModel extends ModeloBaseMySQLi
{
	private $nombreMarca;
	
	public function conectar()
	{
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(2);
	}
		
	public function listar()
	{	
		$conexion = $this->conectar();

		$sql = "SELECT E.anio, E.tipo, E.numero, E.cuerpo, E.alcance, E.caratula, E.fecha_entrada_expe, E.id_codcategoria, E.iniciador_tipo, E.iniciador_codigo, E.marca_comision
				FROM ".$this->tabla_expedientes." AS E 
				WHERE E.fecha_entrada_expe BETWEEN '".$this->filtro['mstmc_fecha_desde']."' AND '".$this->filtro['mstmc_fecha_hasta']."'
				AND ( SELECT id_codestado FROM ".$this->tabla_estados."
					  WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance
					  ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, fecha_estado DESC, orden_estado DESC
					  LIMIT 1
					) IN ((SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 3),
						  (SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 16),
						  (SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 79)
						 )
				AND ( SELECT comision_codigo FROM ".$this->tabla_giros."
					  WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance 
					  AND fecha_entrada_giro > '0000-00-00'
					  AND (fecha_salida_giro IS NULL OR fecha_salida_giro = '0000-00-00')
					  ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, orden_giro DESC 
					  LIMIT 1
					) = '".$this->filtro['mstmc_comision_codigo']."'
				ORDER BY E.anio, E.tipo, E.numero, E.cuerpo, E.alcance
		       ";
		
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		if ( !$resultado )
		{
		    return false;
		}
		else
		{	
		    $datos = $this->crearVector($resultado);// SE CREA UN VECTOR CON EL RESULTADO
		}	
		
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function obtenerComisiones()
	{	
		$conexion = $this->conectar();
		
		$sql = "SELECT * FROM ".$this->tabla_lugares." WHERE tipo_grp = 'C' AND habilitado_grp = 1";
		 
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function marcarComisiones($comisiones_a_marcar)
	{
		$conexion = $this->conectar();
		
		//$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
			
		$query = "UPDATE ".$this->tabla_expedientes."
				  SET marca_comision = ".$comisiones_a_marcar['marca_comision']."
				  WHERE anio = ".$comisiones_a_marcar['anio']."
				  AND tipo = '".$comisiones_a_marcar['tipo']."'
				  AND numero = ".$comisiones_a_marcar['numero']."
				  AND cuerpo = ".$comisiones_a_marcar['cuerpo']."
				  AND alcance = ".$comisiones_a_marcar['alcance']."
				 ";
		
		if ( !$this->ejecutarQuery($query, $conexion) )
		{
		    //$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
		    return false;
		}	
		else
		{
		    //$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION
		    
		    //PARA REGISTRAR EN 'observaciones_log' EN LA TABLA 'auditoria'
		    switch ($comisiones_a_marcar['marca_comision'])
		    {
				case 0:
					$this->nombreMarca = "Sin marca";
					break;	
				case 1:
					$this->nombreMarca = "Para tratar";
					break;
				case 2:
					$this->nombreMarca = "Para su conocimiento";
					break;
				case 3:
					$this->nombreMarca = "Para archivo";
					break;
				case 4:
					$this->nombreMarca = "Para pr&oacute;rroga";
					break;	
		    }
		    
		    $queryLog = " INSERT INTO ".$this->tabla_auditoria_expedientes." (id_log, fecha_hora_log, id_usuario, operacion, tabla, anio_log, tipo_log, numero_log, cuerpo_log, alcance_log, fecha_log, orden_log, netusername, netpcname, observaciones_log)
						  VALUES ( null,
								  '".date("Y-m-d H:i")."',
									".$_SESSION['id_usuario'].",
								  'MARCA',
								  '".$this->tabla_expedientes."',
									".$comisiones_a_marcar['anio'].",
								  '".$comisiones_a_marcar['tipo']."',
									".$comisiones_a_marcar['numero'].",
									".$comisiones_a_marcar['cuerpo'].",
									".$comisiones_a_marcar['alcance'].",
								  '".date("Y-m-d")."',
								  0,
								  '".$_SESSION['usuario']."',
								  '".$_SESSION['netpcname']."',
								  '".$this->nombreMarca."'
								)
						";
		    
		    if ( !$this->ejecutarQuery($queryLog, $conexion) )
		    {
				return false;
		    }	
		}	
		
		$this->desconectar($conexion);
		
		return true;
	}

	public function obtenerMarca($dato)
	{	
		$conexion = $this->conectar();
		
		$sql = "SELECT marca_comision 
				FROM ".$this->tabla_expedientes."
				WHERE anio = ".$dato['anio']."
				AND tipo = '".$dato['tipo']."'
				AND numero = ".$dato['numero']."
				AND cuerpo = ".$dato['cuerpo']."
				AND alcance = ".$dato['alcance']."
		       ";
		 
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$valor_marca = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $valor_marca[0]['marca_comision'];
	}

	public function obtenerUltimoGiro($anio, $tipo, $numero, $cuerpo, $alcance) // ex obtenerFechaGiro
	{
		$conexion = $this->conectar();
		
		$query = "SELECT orden_giro, fecha_entrada_giro, fecha_salida_giro
				  FROM ".$this->tabla_giros."
				  WHERE anio = ".$anio."
				  AND tipo = '".$tipo."'
				  AND numero = ".$numero."
				  AND cuerpo = ".$cuerpo."
				  AND alcance = ".$alcance."
				  AND fecha_entrada_giro > '0000-00-00'
				  AND (fecha_salida_giro IS NULL OR fecha_salida_giro = '0000-00-00')
				  ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, orden_giro DESC 
				  LIMIT 1
				 ";	
		$resultado = $this->ejecutarQuery($query, $conexion);
		
		$registro = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $registro;		 
	}	
		
	public function limpiar()
	{
		$conexion = $this->conectar();

		$sql = "UPDATE ".$this->tabla_expedientes." AS E
				SET E.marca_comision = 0
				WHERE E.fecha_entrada_expe BETWEEN '".$this->filtro['mstmc_fecha_desde']."' AND '".$this->filtro['mstmc_fecha_hasta']."'
				AND ( SELECT id_codestado FROM ".$this->tabla_estados."
					  WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance
					  ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, fecha_estado DESC, orden_estado DESC
					  LIMIT 1
					) IN ((SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 3),
					      (SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 16),
					      (SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 79)
					     )  
				AND ( SELECT comision_codigo FROM ".$this->tabla_giros."
					  WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance 
					  AND fecha_entrada_giro > '0000-00-00'
					  AND (fecha_salida_giro IS NULL OR fecha_salida_giro = '0000-00-00')
					  ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, orden_giro DESC 
					  LIMIT 1
					) = '".$this->filtro['mstmc_comision_codigo']."'
		       ";
		
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		if ( !$resultado )
		{
		    return false;
		}
		
		$this->desconectar($conexion);
		
		return true;
	}
}
?>
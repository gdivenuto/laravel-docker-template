<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class cargarGirosModel extends ModeloBaseMySQLi
{
	public function conectar()
	{
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(2);
	}
	
	public function obtenerComisiones()
	{	
		$conexion = $this->conectar();
		
		$sql = "SELECT * FROM ".$this->tabla_lugares." WHERE tipo_grp = 'C' AND habilitado_grp = 1";
		 
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function verificarExistencia($clave)
	{	
		$conexion = $this->conectar();
		
		$sql = "SELECT tipo 
				FROM ".$this->tabla_giros." 
				WHERE anio = ".$clave['anio']."
				AND tipo = '".$clave['tipo']."'
				AND numero = ".$clave['numero']."
				AND cuerpo = ".$clave['cuerpo']."
				AND alcance = ".$clave['alcance']."
			   ";
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);

		return ( $datos[0]['tipo'] );
	}
	
	public function registrar_estado_girado_a_comision($datos, $fecha, $orden)
	{
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		//SE CARGA EL estado '3 Girado a Comision' PARA DICHO EXPEDIENTE
		$query_estado = "INSERT INTO ".$this->tabla_estados." (anio, tipo, numero, cuerpo, alcance, fecha_estado, orden_estado, id_codestado, observaciones_estado, id_usuario)
						 VALUES ( ".$datos['anio'].",
								 '".$datos['tipo']."',
								  ".$datos['numero'].",
								  ".$datos['cuerpo'].",
								  ".$datos['alcance'].",
								 '".$fecha."',
								  ".$orden.",
								  (SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 3),
								  null,
								  ".$datos['id_usuario']."
								) 
						";
		
		if ( !$this->ejecutarQuery($query_estado) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION
			
			$this->desconectar($conexion);
			
			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaExpedientesModel();
			
			$datos_log = Array();
			$datos_log['operacion_log']     = "ALTA";
			$datos_log['tabla_log']         = $this->tabla_estados;
			$datos_log['anio_log']          = $datos['anio'];
			$datos_log['tipo_log']          = $datos['tipo'];
			$datos_log['numero_log']        = $datos['numero'];
			$datos_log['cuerpo_log']        = $datos['cuerpo'];
			$datos_log['alcance_log']       = $datos['alcance'];
			$datos_log['fecha_log']         = "'".$fecha."'";
			$datos_log['orden_log']         = $orden;
			$datos_log['observaciones_log'] = "Estado: 3 AUTOMATICO";
			
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		return true;
	}
	
	public function cargarGiro($giro_a_cargar)
	{	
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$sql = "INSERT INTO ".$this->tabla_giros." (anio, tipo, numero, cuerpo, alcance, orden_giro, comision_tipo, comision_codigo, fecha_entrada_giro, fecha_salida_giro, dictamen_giro, observaciones_giro, id_usuario)
				VALUES ( ".$giro_a_cargar['anio'].",
						'".$giro_a_cargar['tipo']."',
						 ".$giro_a_cargar['numero'].",
						 ".$giro_a_cargar['cuerpo'].",
						 ".$giro_a_cargar['alcance'].",
						 ".$giro_a_cargar['orden_giro'].",
						'".$giro_a_cargar['comision_tipo']."',
						'".$giro_a_cargar['comision_codigo']."',
						 ".$giro_a_cargar['fecha_entrada_giro'].",
						 ".$giro_a_cargar['fecha_salida_giro'].",
						 ".$giro_a_cargar['dictamen_giro'].",
						 ".$giro_a_cargar['observaciones_giro'].",
						 ".$giro_a_cargar['id_usuario']."
						) 
				";
		
		if (!$this->ejecutarQuery($sql))
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION
			
			$this->desconectar($conexion);
			
			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaExpedientesModel();
			
			$datos_log = Array();
			$datos_log['operacion_log']     = "ALTA";
			$datos_log['tabla_log']         = $this->tabla_giros;
			$datos_log['anio_log']          = $giro_a_cargar['anio'];
			$datos_log['tipo_log']          = $giro_a_cargar['tipo'];
			$datos_log['numero_log']        = $giro_a_cargar['numero'];
			$datos_log['cuerpo_log']        = $giro_a_cargar['cuerpo'];
			$datos_log['alcance_log']       = $giro_a_cargar['alcance'];
			$datos_log['fecha_log']         = $giro_a_cargar['fecha_entrada_giro'];
			$datos_log['orden_log']         = $giro_a_cargar['orden_giro'];
			$datos_log['observaciones_log'] = "PASE AUTOMATICO Comisi&oacute;n: ".$giro_a_cargar['comision_tipo']." - ".$giro_a_cargar['comision_codigo'].", con fecha ".$giro_a_cargar['fecha_entrada_giro']." y Orden ".$giro_a_cargar['orden_giro'];
			
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		return true;
	}
}	
?>
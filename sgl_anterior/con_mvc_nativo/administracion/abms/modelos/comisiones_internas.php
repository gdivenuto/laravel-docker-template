<?php
if (!isset($_SESSION)) {
	session_start();
}

class comisionesInternasModel extends ModeloBaseMySQLi
{
    private $id_area_comisiones = '01030000';
	
    public function conectar() {
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(1);
	}
		
    public function listar()
	{
		$conexion = $this->conectar();
		
		$filtro = "";
		$limite = "";
		
		// PARA FILTRAR POR CODIGO
		if ( $this->filtro['f_codigo'] != '' ) {
			$filtro .= " AND ci_codigo LIKE '".$this->filtro['f_codigo']."%'";
		}
		
		// PARA FILTRAR POR NOMBRE
		if ( $this->filtro['f_nombre'] != '' ) {
			$filtro .= " AND ci_codigo = ( SELECT codigo_grp
										   FROM ".$this->tabla_lugares." 
										   WHERE descripcion_grp = '".$this->filtro['f_nombre']."'
										 )";
		}
		
		// PARA LIMITAR EL LISTADO
		if ( $this->filtro['rango'] != 0 ) {
			$limite = " LIMIT ".$this->filtro['inicio'].", ".$this->filtro['rango'];
		}
		
		$sql = "SELECT *, (SELECT descripcion_grp FROM ".$this->tabla_lugares." WHERE tipo_grp = 'C' AND codigo_grp = ci_codigo) AS ci_nombre
				FROM ".$this->tabla_comisiones_internas."
				WHERE ci_habilitado <> 3
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
		
		// PARA FILTRAR POR CODIGO
		if ( $this->filtro['f_codigo'] != '' ) {
			$filtro .= " AND ci_codigo LIKE '".$this->filtro['f_codigo']."%'";
		}
		
		// PARA FILTRAR POR NOMBRE
		if ( $this->filtro['f_nombre'] != '' ) {
			$filtro .= " AND ci_codigo = ( SELECT codigo_grp
										   FROM ".$this->tabla_lugares."
										   WHERE descripcion_grp = '".$this->filtro['f_nombre']."'
										 )";
		}
		
		$query = "SELECT COUNT(ci_codigo) AS cantidad
				  FROM ".$this->tabla_comisiones_internas."
				  WHERE ci_habilitado <> 3
				  ".$filtro;
		
		$resultado = $this->ejecutarQuery($query);
		
		$dato = $this->obtenerFila($resultado);
		
		return $dato['cantidad'];
	}
	
	public function obtenerUltimoId() {
		return parent::obtenerUltimoCodigo($this->tabla_comisiones_internas, 'ci_codigo');
	}

	public function obtenerRegistro($codigo) {

		$conexion = $this->conectar();
	
		$sql = "SELECT * FROM ".$this->tabla_comisiones_internas." WHERE ci_codigo = '".$codigo."'";
	
		$resultado = $this->ejecutarQuery($sql);
	
		$registro = $this->obtenerFila($resultado);
	
		$this->desconectar($conexion);
	
		return $registro;
	}
	
    public function existe($codigo) 
	{    
		$conexion = $this->conectar();
		
		$query = "SELECT ci_codigo FROM ".$this->tabla_comisiones_internas." WHERE ci_codigo = '".$codigo."'";
		  		    
		$resultado = $this->ejecutarQuery($query);	

		$dato = $this->obtenerFila($resultado);
		
		// Si existe o no
		return ( $dato['ci_codigo'] != '' );
    }	
	
    public function validarDatos($datos)
    {
		$datos['ci_dia'] = $this->revisarValorAtributo(strip_tags($datos['ci_dia']));
		
		$datos['ci_horario'] = $this->revisarValorAtributo(strip_tags($datos['ci_horario']));
		
		$datos['ci_relator'] = $this->revisarValorAtributo(strip_tags($datos['ci_relator']), '0');
		
		$datos['ci_incumbencias'] = $this->revisarValorAtributo(strip_tags($datos['ci_incumbencias']));
		
		return $datos;
    }
	
    //	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
    public function noLoModificoOtroUsuario() {
		
		$conexion = $this->conectar();
		
		$query = "SELECT ci_codigo
				  FROM ".$this->tabla_comisiones_internas."
				  WHERE ci_codigo = '".$_SESSION['ci_codigo_original']."'
				  ".$this->adaptarValorStringParaFiltro('ci_dia')."
				  ".$this->adaptarValorStringParaFiltro('ci_horario')."
				  AND ci_relator = ".$_SESSION['ci_relator_original']."
				  AND ci_habilitado = ".$_SESSION['ci_habilitado_original'];
		
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ( $dato['ci_codigo'] );
    }	
    
    public function insertar($datos)
	{	
		$conexion = $this->conectar();
		
		$this->iniciarTransaccion();
		
		$datos = $this->validarDatos($datos);

		// Cantidad de integrantes elegidos
		$cant_integrantes = count($datos['es_integrante']);
		
		// Se ingresa la Comisión Interna
		$query = "INSERT INTO ".$this->tabla_comisiones_internas." 
					(ci_codigo, ci_dia, ci_horario, ci_relator, ci_incumbencias, ci_habilitado)
				  VALUES(
				  	'".$datos['ci_codigo']."', 
				  	".$datos['ci_dia'].", 
				  	".$datos['ci_horario'].", 
				  	".$datos['ci_relator'].", 
				  	".$datos['ci_incumbencias'].", 
				  	'1')";
		
		if ( !$this->ejecutarQuery($query) ) {
			$this->revertirTransaccion(); // Se deshace la transacción
			return false;
		} else {
			// Por cada integrante
			for ($i=0; $i < $cant_integrantes; $i++) {
				// Legajo
				$legajo_integrante = $datos['es_integrante'][$i];

				// Si es Presidente
				if ( $legajo_integrante == $datos['cargo_presidente'])
					$cargo_en_comision = 1;
				// Si es Vicepresidente
				elseif ( $legajo_integrante == $datos['cargo_vicepresidente'])
					$cargo_en_comision = 2;
				// o solo integrante
				else
					$cargo_en_comision = 3;

				$query = "INSERT INTO ".$this->tabla_miembros_comision_interna." 
							(mci_legajo, mci_codigo_comision, mci_cargo_comision, mci_habilitado)
						  VALUES
						  	(".$legajo_integrante.", '".$datos['ci_codigo']."', ".$cargo_en_comision.", '1');";
				
				if ( !$this->ejecutarQuery($query) ) {
					$this->revertirTransaccion(); // Se deshace la transacción
					return false;
				}
			}
		}

		$this->confirmarTransaccion();

		$this->desconectar($conexion);
	
		// Se audita
		$this->auditarEnAdministracion("ALTA", $this->tabla_comisiones_internas, "Se ingresa la Comisión Interna: ".$this->obtenerNombreComision($datos['ci_codigo']));
		
		return true;	
    }
	
    public function modificar($datos)
	{	
		$conexion = $this->conectar();
		
		$this->iniciarTransaccion();
		
		$datos = $this->validarDatos($datos);
		
		// Cantidad de integrantes elegidos
		$cant_integrantes = count($datos['es_integrante']);

		// Se modifica la Comisión Interna
		$query = "UPDATE ".$this->tabla_comisiones_internas."
				  SET ci_dia = ".$datos['ci_dia'].",
					  ci_horario = ".$datos['ci_horario'].",
					  ci_relator = ".$datos['ci_relator'].",
					  ci_incumbencias = ".$datos['ci_incumbencias']."
				  WHERE ci_codigo = '".$datos['ci_codigo']."'";
 			
		if ( !$this->ejecutarQuery($query) ) {
			$this->revertirTransaccion(); // Se deshace la transacción
			return false;
		} else {
			// Previamente se eliminan todos los integrantes de la Comisión Interna
			$query = "DELETE FROM ".$this->tabla_miembros_comision_interna."
					  WHERE mci_codigo_comision = '".$datos['ci_codigo']."'";
			
			if ( !$this->ejecutarQuery($query) ) {
				$this->revertirTransaccion(); // Se deshace la transacción
				return false;
			} else {
				// Por cada integrante
				for ($i=0; $i < $cant_integrantes; $i++) {
					// Legajo
					$legajo_integrante = $datos['es_integrante'][$i];

					// Si es Presidente
					if ( $legajo_integrante == $datos['cargo_presidente'])
						$cargo_en_comision = 1;
					// Si es Vicepresidente
					elseif ( $legajo_integrante == $datos['cargo_vicepresidente'])
						$cargo_en_comision = 2;
					// o solo integrante
					else
						$cargo_en_comision = 3;

					// Se registra el Integrante respectivo en la Comisión Interna
					$query = "INSERT INTO ".$this->tabla_miembros_comision_interna." 
								(mci_legajo, mci_codigo_comision, mci_cargo_comision, mci_habilitado)
							  VALUES
							  	(".$legajo_integrante.", '".$datos['ci_codigo']."', ".$cargo_en_comision.", '1');";
					
					if ( !$this->ejecutarQuery($query) ) {
						$this->revertirTransaccion(); // Se deshace la transacción
						return false;
					}
				}
			}
		}

		$this->confirmarTransaccion();

		$this->desconectar($conexion);
		
		// Se audita
		$this->auditarEnAdministracion("MODIFICA", $this->tabla_comisiones_internas, "Se modifica la Comisión Interna: ".$this->obtenerNombreComision($datos['ci_codigo']));
		
		return true;	
    }
    
    public function eliminar($codigo) {

		$conexion = $this->conectar();
		
		// SE VERIFICA SI LA COMISION INTERNA POSEE MIEMBROS
		$query = "SELECT mci_codigo_comision FROM ".$this->tabla_miembros_comision_interna." WHERE mci_codigo_comision = '".$codigo."'";
					 
		$resultado = $this->ejecutarQuery($query);

		$verificacion = $this->obtenerFila($resultado);
		
		// SI POSEE MIEMBROS NO SE DEBE ELIMINAR
		if ( $verificacion['mci_codigo_comision'] ) {
			return false;
		}
		else // SI NO POSEE MIEMBROS PUEDE ELIMINARSE
		{
			$query = "DELETE FROM ".$this->tabla_comisiones_internas." WHERE ci_codigo = '".$codigo."'";
			
			if ( !$this->ejecutarQuery($query) ) {
				return false;
			} else {		
				$this->desconectar($conexion);
		
				// Se audita
				$this->auditarEnAdministracion("BAJA", $this->tabla_comisiones_internas, "Se elimina la Comisión Interna: ".$this->obtenerNombreComision($codigo));
			}
		}
		
		return true;	
    }
    
    /**
     * Se obtienen los relatores
     * Personal en el área de Comisiones
     * que se encuentren Activos en su cargo
     * 
     * @return [array] $datos  Listado de Personal que cumplen dicha condición
     */
    public function obtenerRelatores()
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT p_legajo, p_apellido, p_nombre
				FROM ".$this->tabla_personal."
				WHERE p_habilitado = '1'
				AND p_legajo IN ( SELECT A.a_legajo 
								  FROM ".$this->tabla_areas." AS A
								  WHERE A.a_id_area = '".$this->id_area_comisiones."'
								)
				AND p_legajo IN ( SELECT C.c_legajo 
								  FROM ".$this->tabla_cargos." AS C
								  WHERE C.c_fecha_alta = ( SELECT MAX( c_fecha_alta )
													     FROM ".$this->tabla_cargos."
													     WHERE c_legajo = C.c_legajo
													   )
								  AND C.c_fecha_baja IS NULL
								)";
		
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }
	   
    public function obtenerNombrePersonal($legajo) {

		$conexion = $this->conectar();
		
		$sql = "SELECT CONCAT(p_apellido, ', ', p_nombre) AS nombre_personal
				FROM ".$this->tabla_personal."
				WHERE p_legajo = ".$legajo;
		
		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $registro['nombre_personal'];
    }
	
    public function obtenerComisionesInternas() {

		$conexion = $this->conectar();
		
		$sql = "SELECT * FROM ".$this->tabla_lugares." WHERE tipo_grp = 'C' ORDER BY descripcion_grp";

		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }
	   
    public function obtenerNombreComision($codigo) {

		$conexion = $this->conectar();
		
		$sql = "SELECT descripcion_grp AS nombre_comision
				FROM ".$this->tabla_lugares."
				WHERE tipo_grp = 'C'
				AND codigo_grp = '".$codigo."'";
		
		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $registro['nombre_comision'];
    }
	
    /**
     * Se obtienen los Concejales activos
     * @return [array] $datos  Listado de Concejales que cumplen dicha condición
     */
    public function obtenerConcejalesActivos()
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT p_legajo, p_apellido, p_nombre
				FROM ".$this->tabla_personal." 
				WHERE p_legajo IN ( SELECT C.c_legajo
									FROM ".$this->tabla_cargos." AS C
									WHERE C.c_nomenclador = ( SELECT cc_nomenclador
															  FROM ".$this->tabla_codcargos."
															  WHERE cc_nomenclador = ".$this->id_cargo_concejal."
														    )
									AND C.c_fecha_alta = ( SELECT MAX( c_fecha_alta )
													       FROM ".$this->tabla_cargos."
													       WHERE c_legajo = C.c_legajo
													     )
								    AND C.c_fecha_baja IS NULL
								  )
				ORDER BY p_apellido, p_nombre";
		
		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }
	
    public function listarMiembrosComisionInterna($cod_comision_interna)
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT *,
					  (SELECT p_apellido FROM ".$this->tabla_personal." WHERE p_legajo = mci_legajo) AS p_apellido,
					  (SELECT p_nombre FROM ".$this->tabla_personal." WHERE p_legajo = mci_legajo) AS p_nombre,
					  (SELECT p_sexo FROM ".$this->tabla_personal." WHERE p_legajo = mci_legajo) AS p_sexo
				FROM ".$this->tabla_miembros_comision_interna."
				WHERE mci_codigo_comision = '".$cod_comision_interna."'
				AND mci_habilitado = 1
				ORDER BY mci_cargo_comision ASC, p_apellido ASC";
			   
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }

    public function esIntegrante($legajo, $codigo_comision)
	{    
		$conexion = $this->conectar();
		
		$query = "SELECT mci_legajo 
				  FROM ".$this->tabla_miembros_comision_interna." 
				  WHERE mci_legajo = ".$legajo."
				  AND mci_codigo_comision = '".$codigo_comision."'";
		  		  
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		// Si es Integrante o no
		return ( isset($dato['mci_legajo']) && $dato['mci_legajo'] != '' );
    }	


    public function esPresidente($legajo, $codigo_comision)
	{    
		$conexion = $this->conectar();
		
		$query = "SELECT mci_legajo 
				  FROM ".$this->tabla_miembros_comision_interna." 
				  WHERE mci_legajo = ".$legajo."
				  AND mci_codigo_comision = '".$codigo_comision."'
				  AND mci_cargo_comision = 1";
		
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		// Si es Presidente o no
		return ( isset($dato['mci_legajo']) && $dato['mci_legajo'] != '' );
    }


    public function esVicepresidente($legajo, $codigo_comision)
	{    
		$conexion = $this->conectar();
		
		$query = "SELECT mci_legajo 
				  FROM ".$this->tabla_miembros_comision_interna." 
				  WHERE mci_legajo = ".$legajo."
				  AND mci_codigo_comision = '".$codigo_comision."'
				  AND mci_cargo_comision = 2";
		
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		// Si es Vicepresidente o no
		return ( isset($dato['mci_legajo']) && $dato['mci_legajo'] != '' );
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

		$query = "UPDATE " . $this->tabla_comisiones_internas . " 
				  SET ci_habilitado = $valor_habilitado 
				  WHERE ci_codigo = '$id'";

		if (!$this->ejecutarQuery($query)) {
			return false;
		}

		$this->desconectar($conexion);

		return true;
	}

	/**
	 * Se setean en mantenimiento o no, las comisiones habilitadas
	 *
	 * @param  integer $mantenimiento
	 * @return boolean true|false
	 */
	public function setearMantenimiento($mantenimiento) {

		$conexion = $this->conectar();

		$valor_mantenimiento = ($mantenimiento == 1) ? 0 : 1;

		$query = "UPDATE " . $this->tabla_comisiones_internas . " SET ci_en_mantenimiento = $valor_mantenimiento";

		if (!$this->ejecutarQuery($query)) {
			return false;
		}

		$this->desconectar($conexion);

		return true;
	}

}
?>

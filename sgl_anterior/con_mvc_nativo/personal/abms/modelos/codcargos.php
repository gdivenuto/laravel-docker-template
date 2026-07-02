<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class codcargosModel extends ModeloBaseMySQLi
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

		// PARA FILTRAR POR NOMENCLADOR
		if ( $this->filtro['nomenclador'] != '' )
		{
			$filtro .= " AND cc_nomenclador LIKE '".$this->filtro['nomenclador']."%'";
		}

		// PARA FILTRAR POR NOMBRE
		if ( $this->filtro['nombre'] != '' )
		{
			$filtro .= " AND cc_nombre LIKE '%".$this->filtro['nombre']."%'";
		}

		// PARA LIMITAR EL LISTADO
		if ( $this->filtro['rango'] != 0 )
		{
			$limite = " LIMIT ".$this->filtro['inicio'].", ".$this->filtro['rango'];
		}

		$sql = "SELECT * FROM ".$this->tabla_codcargos."
				WHERE cc_habilitado <> 3
				".$filtro."
				ORDER BY ".$_SESSION['ultimo_campo']." ".$_SESSION['ultimo_sentido']."
				".$limite."
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
    }

    public function obtenerCantidad()
	{
		$conexion = $this->conectar();

		$sql = "SELECT COUNT(cc_nomenclador) AS cantidad
				FROM ".$this->tabla_codcargos."
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['cantidad'];
    }

    public function obtenerRegistro($cc_nomenclador)
    {
    	$conexion = $this->conectar();

    	$sql = "SELECT * FROM ".$this->tabla_codcargos."
				WHERE cc_nomenclador = '".$cc_nomenclador."'
			   ";

    	$resultado = $this->ejecutarQuery($sql);

    	$registro = $this->obtenerFila($resultado);

    	$this->desconectar($conexion);

    	return $registro;
    }

    public function existe($nomenclador)
	{
		$conexion = $this->conectar();

		$query = "SELECT cc_nomenclador
				  FROM ".$this->tabla_codcargos."
				  WHERE cc_nomenclador = '".$nomenclador."'
				 ";


		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		// Si existe o no
		return ( $dato['cc_nomenclador'] != '' );
    }

    public function validarDatos($datos)
    {
		// NOMBRE
		$datos['cc_nombre'] = $this->revisarValorAtributo($datos['cc_nombre']);

		// TIPO
		$datos['cc_tipo']   = $this->revisarValorAtributo($datos['cc_tipo']);

		// MÓDULO
		$datos['cc_modulo'] = $this->revisarValorAtributo($datos['cc_modulo']);

		return $datos;
    }

    //	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
    public function verificarRegistroEntero()
    {
		$filtro_nombre = $this->adaptarValorStringParaFiltro('cc_nombre');

		$filtro_modulo = $this->adaptarValorStringParaFiltro('cc_modulo');

		$conexion = $this->conectar();

		$query = "SELECT cc_nomenclador
				  FROM ".$this->tabla_codcargos."
				  WHERE cc_nomenclador = '".$_SESSION['cc_nomenclador_original']."'
				  ".$filtro_nombre."
				  AND cc_tipo = '".$_SESSION['cc_tipo_original']."'
				  AND cc_gente_a_cargo = '".$_SESSION['cc_gente_a_cargo_original']."'
				  ".$filtro_modulo."
				  AND cc_habilitado = ".$_SESSION['cc_habilitado_original']."
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ( $dato['cc_nomenclador'] );
    }

    public function insertar($datos)
	{
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		$datos = $this->validarDatos($datos);

		$query = "INSERT INTO ".$this->tabla_codcargos." (cc_nomenclador, cc_nombre, cc_tipo, cc_gente_a_cargo, cc_modulo, cc_habilitado)
				  VALUES('".$datos['cc_nomenclador']."', ".$datos['cc_nombre'].", ".$datos['cc_tipo'].", ".$datos['cc_gente_a_cargo'].", ".$datos['cc_modulo'].", 1)
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
			$datos_log['tabla']         = $this->tabla_codcargos;
			$datos_log['legajo']        = 0;
			$datos_log['observaciones'] = "Se ingresa un Cargo.";

			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}

		return true;
    }

	public function verificarUsoCargo($nomenclador)
	{
		$conexion = $this->conectar();

		// SE VERIFICA SI EL CARGO ESTA SIENDO USADO
		$query = "SELECT c_nomenclador
				  FROM ".$this->tabla_cargos."
				  WHERE c_nomenclador = '".$nomenclador."'
				  AND c_fecha_baja IS NULL
				 ";

		$resultado = $this->ejecutarQuery($query);

		$verificacion = $this->obtenerFila($resultado);

		// Si está asignado o no
		return ( $verificacion['c_nomenclador'] == '' );
	}

    public function modificar($datos)
	{
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		$datos = $this->validarDatos($datos);

		$query = "UPDATE ".$this->tabla_codcargos."
				  SET cc_nombre = ".$datos['cc_nombre'].",
					  cc_tipo = ".$datos['cc_tipo'].",
					  cc_gente_a_cargo = ".$datos['cc_gente_a_cargo'].",
					  cc_modulo = ".$datos['cc_modulo'].",
					  cc_habilitado = ".$datos['cc_habilitado']."
				  WHERE cc_nomenclador = '".$datos['cc_nomenclador']."'
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
			$datos_log['tabla']         = $this->tabla_codcargos;
			$datos_log['legajo']        = 0;
			$datos_log['observaciones'] = "Se modifica un Cargo";

			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}

		return true;
    }

    public function eliminar($nomenclador)
	{
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		// SE VERIFICA SI EL CARGO ESTA ASIGNADO A UN LEGAJO
		$queryA = "SELECT c_nomenclador
				   FROM ".$this->tabla_cargos."
				   WHERE c_nomenclador = '".$nomenclador."'
				  ";

		$resultado = $this->ejecutarQuery($queryA, $conexion);

		$dato = $this->obtenerFila($resultado);

		// SI ESTA ASIGNADO NO SE DEBE ELIMINAR
		if ( $dato['c_nomenclador'] )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else // SI NO ESTA ASIGNADO A UN LEGAJO PUEDE ELIMINARSE
		{
			$query = "DELETE FROM ".$this->tabla_codcargos."
					  WHERE cc_nomenclador = '".$nomenclador."'
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
				$datos_log['operacion']     = "BAJA";
				$datos_log['tabla']         = $this->tabla_codcargos;
				$datos_log['legajo']        = 0;
				$datos_log['observaciones'] = "Se elimina un Cargo.";

				//SE CARGA EN auditoria EL MOVIMIENTO
				$modelo->registrarMovimiento($datos_log);
			}
		}

		return true;
    }

    public function listadoCombo($legajo = 0)
	{
		$conexion = $this->conectar();

		if ( $legajo != 0 )
		{
			$sql = "SELECT * FROM ".$this->tabla_codcargos."
					WHERE cc_habilitado = 1
					AND cc_tipo IN ( SELECT ca_tipo
									 FROM ".$this->tabla_codareas."
									 WHERE ca_id IN ( SELECT a_id_area
													  FROM ".$this->tabla_areas."
													  WHERE a_legajo = ".$legajo."
												    )
								   )
					ORDER BY cc_nombre
				   ";
		}
		else
			$sql = "CALL sp_pers_listarCargos(NULL, NULL, 0, 0, 0, 'cc_nombre', 'ASC', 'no')";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
    }

    /**
     * 20/01/2020 XXXX
     *
     * Se obtienen los cargos para cargar el combo al editar
     * aquí se consideran los cargos deshabilitados también
     * @param  integer $legajo Legajo del agente
     * @return array   $datos  Listado de Cargos
     */
    public function listadoComboAlEditar($legajo = 0)
	{
		$conexion = $this->conectar();

		$sql = "SELECT * FROM ".$this->tabla_codcargos."
				WHERE cc_tipo IN ( SELECT ca_tipo
								   FROM ".$this->tabla_codareas."
								   WHERE ca_id IN ( SELECT a_id_area
												    FROM ".$this->tabla_areas."
												    WHERE a_legajo = ".$legajo."
											      )
							     )
				ORDER BY cc_nombre
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
    }
}
?>

<?php
if (!isset($_SESSION)) {
	session_start();
}

class carouselModel extends ModeloBaseMySQLi {

	public function __construct() {
		parent::__construct();
	}

	public function conectar() {
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(1);
	}

	public function listar() {
		$conexion = $this->conectar();

		$filtro = "";

		// Para filtrar por la Fecha de Creación
		if (isset($this->filtro['f_fecha']) && $this->filtro['f_fecha'] != '') {
			$filtro .= " AND fecha = '" . $this->formatearFechaMySQL($this->filtro['f_fecha']) . "'";
		}

		//para limitar el listado
		$registro_inicial = (isset($this->filtro['inicio']) && $this->filtro['inicio'] != '') ? $this->filtro['inicio'] : 0;

		$sql = "SELECT *
				FROM " . $this->tabla_carousel . "
			    WHERE habilitado <> 3
			    " . $filtro . "
			    ORDER BY habilitado DESC, prioridad DESC, id DESC
			    LIMIT " . $registro_inicial . ", " . $this->filtro['rango'];

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerCantidad() {

		$conexion = $this->conectar();

		$filtro = "";

		// Para filtrar por la Fecha de Creación
		if (isset($this->filtro['f_fecha']) && $this->filtro['f_fecha'] != '') {
			$filtro .= " AND fecha = '" . $this->formatearFechaMySQL($this->filtro['f_fecha']) . "'";
		}

		$query = "SELECT COUNT(id) AS cantidad FROM " . $this->tabla_carousel . " WHERE habilitado <> 3 " . $filtro;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		return $dato['cantidad'];
	}

	public function obtenerRegistro($id) {

		$conexion = $this->conectar();

		$query = "SELECT * FROM " . $this->tabla_carousel . " WHERE id = " . $id;

		$resultado = $this->ejecutarQuery($query);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

	//	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
	public function noLoModificoOtroUsuario() {

		$conexion = $this->conectar();

		$query = "SELECT id
				  FROM " . $this->tabla_carousel . "
				  WHERE id = " . $_SESSION['id_original'] . "
				  " . $this->adaptarValorStringParaFiltro('fecha') . "
				  " . $this->adaptarValorStringParaFiltro('recurso') . "
				  " . $this->adaptarValorStringParaFiltro('enlace') . "
				  " . $this->adaptarValorStringParaFiltro('es_actividad') . "
				  AND habilitado = " . $_SESSION['habilitado_original'];

		$resultado = $this->ejecutarQuery($query);

		$datos = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ($datos['id'] != '');
	}

	public function validarDatos($datos) {

		$datos['recurso'] = $this->revisarValorAtributo($datos['recurso']);

		$datos['enlace'] = $this->revisarValorAtributo($datos['enlace']);

		return $datos;
	}

	/**
	 * Se obtiene el ultimo Id registrado en la DB
	 *
	 * @see ModelBase::obtenerUltimoCodigo()
	 */
	public function obtenerUltimoId() {

		return parent::obtenerUltimoCodigo($this->tabla_carousel, 'id');
	}

	public function obtenerUltimaPrioridad() {

		$conexion = $this->conectar();

		$query = "SELECT MAX(prioridad) AS ultima_prioridad FROM " . $this->tabla_carousel;

		$resultado = $this->ejecutarQuery($query);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ($registro['ultima_prioridad'] != '') ? $registro['ultima_prioridad'] : 0;
	}

	public function insertar($datos) {

		$datos['prioridad'] = $this->obtenerUltimaPrioridad() + 1;

		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "INSERT INTO " . $this->tabla_carousel . "
						(fecha, recurso, enlace, es_actividad, prioridad, habilitado, editando)
				  VALUES('" . date("Y-m-d") . "',
				  		  " . $datos['recurso'] . ",
					      " . $datos['enlace'] . ",
					      1,
						  " . $datos['prioridad'] . ",
					      1,
				  		  0
					    )";
		
		//LibreriaGeneral::registrarLog("query_insertar_carousel", $query);

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {

			$this->desconectar($conexion);
			
			// Se audita
			$this->auditarEnAdministracion("MODIFICA", $this->tabla_carousel, "Se ingresa un recurso al Carousel");
		}
		return true;
	}

	public function modificar($datos) {

		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "UPDATE " . $this->tabla_carousel . "
				  SET recurso = " . $datos['recurso'] . ",
				      enlace = " . $datos['enlace'] . "
				  WHERE id = " . $datos['id'];

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {

			$this->desconectar($conexion);

			// Se audita
			$this->auditarEnAdministracion("MODIFICA", $this->tabla_carousel, "Se modifica un recurso del Carousel");
		}
		return true;
	}

	/**
	 * Se ingresa el nombre del recurso en la DB
	 * @param  [integer] $id
	 * @param  [string] $nombre_recurso
	 * @return [boolean]
	 */
	public function ingresarNombreRecurso($id, $nombre_recurso) {

		$conexion = $this->conectar();

		$query = "UPDATE " . $this->tabla_carousel . "
				  SET recurso = '" . $nombre_recurso . "'
				  WHERE id = " . $id;

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);
		}
		return true;
	}

	/**
	 * Se elimina una referencia de una foto (se setea a null el campo del registro respectivo)
	 *
	 * @param integer $id
	 * @return boolean
	 */
	public function eliminarFoto($id) {

		$conexion = $this->conectar();

		$query = "UPDATE " . $this->tabla_carousel . " SET recurso = null WHERE id = " . $id;
		
		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion("MODIFICA", $this->tabla_carousel, "Se elimina una foto del carousel.");
		}

		return true;
	}

	public function eliminar($id) {

		$conexion = $this->conectar();

		$query = "DELETE FROM " . $this->tabla_carousel . " WHERE id = " . $id;

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);

			// Se audita
			$this->auditarEnAdministracion("BAJA", $this->tabla_carousel, "Se elimina un recurso del carousel");
		}

		return true;
	}

	/**
	 * Se modifica el estado habilitado|deshabilitado del Curso
	 *
	 * @param  integer $id
	 * @param  integer $habilitado
	 * @return boolean true|false
	 */
	public function modificarEstado($id, $habilitado) {

		$conexion = $this->conectar();

		$valor_habilitado = ($habilitado == 1) ? 0 : 1;

		$query = "UPDATE " . $this->tabla_carousel . " SET habilitado = $valor_habilitado WHERE id = " . $id;

		if (!$this->ejecutarQuery($query)) {
			return false;
		}

		$this->desconectar($conexion);

		return true;
	}

	/**
	 * Se define si es un Actividad o no
	 *
	 * @param  integer $id
	 * @param  integer $es_actividad
	 * @return boolean true|false
	 */
	public function modificarEstadoEsActividad($id, $es_actividad) {

		$conexion = $this->conectar();

		$valor_es_actividad = ($es_actividad == 1) ? 0 : 1;

		$query = "UPDATE " . $this->tabla_carousel . " SET es_actividad = $valor_es_actividad WHERE id = " . $id;

		if (!$this->ejecutarQuery($query)) {
			return false;
		}

		$this->desconectar($conexion);

		return true;
	}

	/**
	 * Se audita la consulta de un registro determinado
	 * @param  [array] $registro Info del registro
	 */
	public function auditarConsultaRegistro($registro) {

		$modelo = new auditoriaModel();

		$datos_log = Array();
		$datos_log['au_operacion'] = "CONSULTA";
		$datos_log['au_tabla'] = $this->tabla_carousel;
		$datos_log['au_id_registro'] = $registro['id'];
		$datos_log['au_observaciones'] = addslashes("Se consulta un recurso del carousel");

		// SE CARGA EN auditoria EL MOVIMIENTO
		$modelo->registrarMovimiento($datos_log);
	}

	/**
	 *  Se obtiene el listado ordenado descendentemente
	 * @return array Listado
	 */
	public function obtenerListado() {

		$conexion = $this->conectar();

		$sql = "SELECT id, prioridad 
				FROM $this->tabla_carousel 
				ORDER BY habilitado DESC, prioridad DESC, id DESC";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se guarda la Prioridad de un Id determinado
	 * @param  integer $id        	Identificador
	 * @param  integer $prioridad 	Prioridad
	 * @return boolean
	 */
	public function guardarPrioridad($id, $prioridad) {

		$conexion = $this->conectar();
		
		$query = "UPDATE ".$this->tabla_carousel."
				  SET prioridad = ".$prioridad."
				  WHERE id = ".$id;
			
		if ( !$this->ejecutarQuery($query) )
			return false;
			
		$this->desconectar($conexion);
		
		return true;
	}
	

	/**
	 * Se sube su prioridad
	 *
	 * @param  integer $id
	 * @param  integer $prioridad
	 * @return boolean true|false
	 */
	public function subirPrioridad($id, $prioridad) {

		$conexion = $this->conectar();

		// Se obtiene info del registro consecutivo de mayor orden
		$query = "SELECT id, prioridad FROM $this->tabla_carousel WHERE prioridad = ($prioridad + 1)";
		
		$resultado = $this->ejecutarQuery($query);

		$siguiente = $this->obtenerFila($resultado);
		
		// Si existe el registro siguiente
		if ($siguiente['id'] != '') {
			// Se le modifica la prioridad al siguiente, definiéndole la recibida
			$query = "UPDATE $this->tabla_carousel SET prioridad = $prioridad WHERE id = " . $siguiente['id'];
			
			if (!$this->ejecutarQuery($query)) {
				return false;
			} else {
				// Se le modifica la prioridad al elegido, por la que tenía el registro siguiente
				$query = "UPDATE $this->tabla_carousel SET prioridad = " . $siguiente['prioridad'] . " WHERE id = $id";
				
				if (!$this->ejecutarQuery($query)) {
					return false;
				}
			}
		}

		$this->desconectar($conexion);

		return true;
	}

	/**
	 * Se baja su prioridad
	 *
	 * @param  integer $id
	 * @param  integer $prioridad
	 * @return boolean true|false
	 */
	public function bajarPrioridad($id, $prioridad) {

		$conexion = $this->conectar();

		// Se obtiene info del registro consecutivo de menor prioridad
		$query = "SELECT id, prioridad FROM $this->tabla_carousel WHERE prioridad = ($prioridad - 1)";
		
		$resultado = $this->ejecutarQuery($query);

		$anterior = $this->obtenerFila($resultado);
		
		// Si existe el registro anterior
		if ($anterior['id'] != '') {
			// Se le modifica la prioridad al anterior, definiéndole la recibida
			$query = "UPDATE $this->tabla_carousel SET prioridad = $prioridad WHERE id = " . $anterior['id'];
			
			if (!$this->ejecutarQuery($query)) {
				return false;
			} else {
				// Se le modifica la prioridad al elegido, por la que tenía el registro anterior
				$query = "UPDATE $this->tabla_carousel SET prioridad = " . $anterior['prioridad'] . " WHERE id = $id";
				
				if (!$this->ejecutarQuery($query)) {
					return false;
				}
			}
		}

		$this->desconectar($conexion);

		return true;
	}

}
?>

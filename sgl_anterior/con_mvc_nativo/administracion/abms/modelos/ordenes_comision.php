<?php
if (!isset($_SESSION)) {
	session_start();
}

class ordenes_comisionModel extends ModeloBaseMySQLi
{
    public function conectar() {
		// Se conecta según el Id del sistema
		return parent::conectarDB(1);
	}
	
	private function getFiltro() {

		$filtro = "";

		if ( $this->filtro['f_comision'] != '' )
			$filtro .= " AND principal LIKE '".$this->filtro['f_comision']."%'";
		
		if ( $this->filtro['f_fecha'] != '' )
			$filtro .= " AND fecha = '".$this->filtro['f_fecha']."'";
		
		return $filtro;
	}

    public function listar() {

		$conexion = $this->conectar();
		
		$limite = ($this->filtro['rango'] != 0 
			? " LIMIT ".$this->filtro['inicio'].", ".$this->filtro['rango']
			: "");
		
		$sql = "SELECT *
				FROM ".$this->tabla_od_comision."
				WHERE fecha IS NOT NULL
				".$this->getFiltro()."
				ORDER BY fecha DESC, hora DESC
				".$limite;
		
		//LibreriaGeneral::registrarLog("sql_listar_ordenes_comision", $sql);

		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }
	
	public function obtenerCantidad() {

		$conexion = $this->conectar();
		
		$query = "SELECT COUNT(id) AS cantidad
				  FROM ".$this->tabla_od_comision." 
				  WHERE fecha IS NOT NULL
				  ".$this->getFiltro();
		  		  
		//LibreriaGeneral::registrarLog("query_cantidad", $query);

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		return $dato['cantidad'];
	}
	
    public function obtenerRegistro($id) { 

		$conexion = $this->conectar();
		
		$sql = "SELECT * FROM ".$this->tabla_od_comision." WHERE id = ".$id;
		
		$resultado = $this->ejecutarQuery($sql);
		
		$registro = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $registro;
    }
	
    public function obtenerComisionesInternas() {

		$conexion = $this->conectar();
		
		$sql = "SELECT 
					codigo_grp, 
					descripcion_grp 
				FROM ".$this->tabla_lugares."
				WHERE tipo_grp = 'C'
				AND habilitado_grp = '1'
				ORDER BY descripcion_grp";
		
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
				AND codigo_grp = ".$codigo;

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $registro['nombre_comision'];
    }
	
	/**
	 * Se obtiene el ultimo Id registrado en la DB
	 *
	 * @see ModelBase::obtenerUltimoCodigo()
	 */
	public function obtenerUltimoId() {

		return parent::obtenerUltimoCodigo($this->tabla_od_comision, 'id');
	}

	/**
	 * Se verifica si existe la Orden del día de Comisión en la fecha determinada
	 * @param  string $principal 	Código de la Comisión Interna Principal
	 * @param  string $fecha        Fecha de la Orden de la Comisión elegida
	 * @return boolean
	 */
    public function existe($principal, $fecha) {

		$conexion = $this->conectar();
		
		$query = "SELECT id
				  FROM ".$this->tabla_od_comision." 
				  WHERE principal = '".$principal."'
				  AND fecha = '".$this->formatearFechaMySQL($fecha)."'";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		// Si existe o no
		return ( $dato['id'] != '' );
    }	
	
    /**
     * Se obtiene el encabezado y el pie de la comisión anterior (del mismo código)
     * @param  string $principal 	Código de la comisión Principal
     * @return array              Encabezado y pie (o null de no existir)
     */
	public function obtenerEncabezadoPie($principal)
	{
		$conexion = $this->conectar();
		
		$query = "SELECT 
					encabezado, 
					pie
				  FROM ".$this->tabla_od_comision." 
				  WHERE principal = '".$principal."'
				  AND encabezado IS NOT NULL
				  AND pie IS NOT NULL
				  ORDER BY id DESC 
				  LIMIT 1";

		//LibreriaGeneral::registrarLog("query_obtenerEncabezadoPie", $query, '.sql');
		
		$resultado = $this->ejecutarQuery($query);

		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;	
	}

    /**
     * Se obtiene el encabezado de la orden
     * @param  integer $id 	Identificador de la orden
     * @return string  $dato['encabezado']
     */
	public function obtenerEncabezado($id)
	{
		$conexion = $this->conectar();
		
		$query = "SELECT encabezado FROM ".$this->tabla_od_comision." WHERE id = ".$id;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['encabezado'];
	}

    public function modificarEncabezado($datos)
	{
		$encabezado_anterior = $this->obtenerEncabezado($datos['id']);
		
		$conexion = $this->conectar();

		$datos['encabezado'] = "'".mysqli_real_escape_string($conexion, $datos['encabezado'])."'";

		$query = "UPDATE ".$this->tabla_od_comision."
				  SET encabezado = ".$datos['encabezado']."
				  WHERE id = ".$datos['id'];

		if ( !$this->ejecutarQuery($query) )
			return false;
		
		$this->desconectar($conexion);
		
		$observaciones  = "Encabezado Anterior: ".$encabezado_anterior;
		$observaciones .= "<br>Encabezado Actual: " . $datos['encabezado'];

		$this->auditarEnAdministracion(
			"MODIFICA", 
			$this->tabla_od_comision, 
			$observaciones
		);

		return true;
    }

    /**
     * Se obtiene el pie de la orden
     * @param  integer $id 	Identificador de la orden
     * @return string  $dato['pie']
     */
	public function obtenerPie($id)
	{
		$conexion = $this->conectar();
		
		$query = "SELECT pie FROM ".$this->tabla_od_comision." WHERE id = ".$id;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['pie'];
	}

    public function modificarPie($datos)
	{
		$pie_anterior = $this->obtenerPie($datos['id']);
		
		$conexion = $this->conectar();

		$datos['pie'] = "'".mysqli_real_escape_string($conexion, $datos['pie'])."'";

		$query = "UPDATE ".$this->tabla_od_comision."
				  SET pie = ".$datos['pie']."
				  WHERE id = ".$datos['id'];

		if ( !$this->ejecutarQuery($query) )
			return false;
		
		$this->desconectar($conexion);
		
		$observaciones  = "Pie Anterior: ".$pie_anterior;
		$observaciones .= "<br>Pie Actual: " . $datos['pie'];

		$this->auditarEnAdministracion(
			"MODIFICA", 
			$this->tabla_od_comision, 
			$observaciones
		);

		return true;
    }

    public function validarDatos($datos)
    {
    	// Si se trata de una comisión conjunta (dos o más comisiones)
    	if (isset($datos['codigo_conjuntas']) && count($datos['codigo_conjuntas']) > 0)
    	{
    		// Se unen los códigos de las comisiones conjuntas, separadas por una coma
    		$datos['codigo_comision'] = "'".implode(",", $datos['codigo_conjuntas'])."'";

    		// Se registra el código de comisión de la que se recibió como principal
    		$datos['principal'] = $this->revisarValorAtributo($datos['principal']);
    	}
    	else {
    		// Si se trata de una única comisión
    		$datos['codigo_comision'] = isset($datos['codigo_comision']) 
    			? $this->revisarValorAtributo(strip_tags($datos['codigo_comision'])) 
    			: '';

    		// Siendo la única comisión, se asigna como principal
    		$datos['principal'] = $datos['codigo_comision'];
    	}

		$datos['asunto'] = $this->revisarValorAtributo($datos['asunto']);
		
		$datos['fecha'] = $this->revisarValorFechaAtributo($datos['fecha']);
		
		$datos['hora'] = $this->revisarValorAtributo($datos['hora']);
		
		$datos['encabezado'] = $this->revisarValorContenidoTextArea($datos['encabezado']);
		
		$datos['pie'] = $this->revisarValorContenidoTextArea($datos['pie']);
		
		$datos['es_conjunta'] = $this->revisarValorAtributo($datos['es_conjunta'], 0);
		
		return $datos;
    }
	
    public function insertar($datos)
    {
		// Se define el Asunto de la Orden de Comisión (siendo individual o conjunta)
		// -------------------------------------------------------------------------
		// Si es una comisión conjunta
		if (isset($datos['es_conjunta']))
		{
			// Se inicia la confección del Asunto
			$datos['asunto'] = "CONJUNTA DE ";

			// Por cada comisión
			foreach ($datos['codigo_conjuntas'] as $codigo)
				// Se agrega el nombre de la comisión mediante su código
				$datos['asunto'] .= $this->obtenerNombreComision($codigo)." y ";

			// Se elimina el último " y "
			$datos['asunto'] = substr($datos['asunto'], 0, -3);
		}
		else
			$datos['asunto'] = $this->obtenerNombreComision($datos['codigo_comision']);

		// Recién aquí se necesita la conexión para la inserción
		$conexion = $this->conectar();
		
		// Se validan los datos
		$datos = $this->validarDatos($datos);

		$query = "INSERT INTO ".$this->tabla_od_comision." 
				  	(codigo_comision, asunto, fecha, hora, encabezado, pie, es_conjunta, principal)
				  VALUES
				  	(".$datos['codigo_comision'].", 
			  		 ".$datos['asunto'].", 
			  		 ".$datos['fecha'].", 
			  		 ".$datos['hora'].",
			  		 ".$datos['encabezado'].", 
			  		 ".$datos['pie'].",
			  		 ".$datos['es_conjunta'].",
			  		 ".$datos['principal']."
			  		)";

		//LibreriaGeneral::registrarLog("query_insertar", $query);

		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {
			$this->desconectar($conexion);
		
			$observaciones  = "Se crea la Orden de Comisi&oacute;n: ";
			$observaciones .= str_replace("'","",$datos['asunto']);
			$observaciones .= " - Fecha: ".str_replace("'","",$datos['fecha']);
			$observaciones .= " y Hora: ".str_replace("'","",$datos['hora']);

			$this->auditarEnAdministracion("ALTA", $this->tabla_od_comision, $observaciones);
		}

		return true;	
    }
	
	/**
	 * Se modifica la cabecera de la Orden 
	 */
    public function modificar($datos)
    {
    	$info_anterior = $this->obtenerRegistro($datos['id']);

		$conexion = $this->conectar();
		
		$datos = $this->validarDatos($datos);
		
		$query = "UPDATE ".$this->tabla_od_comision."
				  SET asunto  = ".$datos['asunto'].",
				  	  fecha = ".$datos['fecha'].",
					  hora = ".$datos['hora']."
				  WHERE id = ".$datos['id'];
		
		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {			
			
			$this->desconectar($conexion);
		
			$observaciones  = "Se modifica la Orden de Comision:";
			$observaciones .= "\n ANTES:";
			$observaciones .= str_replace("'","",$info_anterior['asunto']);
			$observaciones .= " - Fecha: ".str_replace("'","",$info_anterior['fecha']);
			$observaciones .= " y Hora: ".str_replace("'","",$info_anterior['hora']);

			$observaciones .= "\n DESPUES:";
			$observaciones .= str_replace("'","",$datos['asunto']);
			$observaciones .= " - Fecha: ".str_replace("'","",$datos['fecha']);
			$observaciones .= " y Hora: ".str_replace("'","",$datos['hora']);

			$this->auditarEnAdministracion("MODIFICA", $this->tabla_od_comision, $observaciones);
		}
		
		return true;	
    }
    
    public function eliminar($id)
    {
    	// Se obtiene la info para auditar
    	$info = $this->obtenerRegistro($id);

		$conexion = $this->conectar();
		
		$this->iniciarTransaccion();
		
		// Primero se eliminan los items de la Orden de Comisión
		$query = "DELETE FROM ".$this->tabla_od_comision_items."
				  WHERE id_orden_comision IN 
				  	(SELECT id FROM ".$this->tabla_od_comision." WHERE id = ".$id.")";
						 
		if ( !$this->ejecutarQuery($query) ) {
			$this->revertirTransaccion();
			return false;
		} else {
			// Luego se elimina la Orden de Comisión
			$query = "DELETE FROM ".$this->tabla_od_comision." WHERE id = ".$id;
			
			if ( !$this->ejecutarQuery($query) ) {
				$this->revertirTransaccion();
				return false;
			} else {			
				$this->confirmarTransaccion();

				$this->desconectar($conexion);
				
				$observaciones  = "Se elimina la Orden de Comisi&oacute;n: ";
				$observaciones .= $info['asunto'];
				$observaciones .= " - Fecha: ".$info['fecha'];
				$observaciones .= " y Hora: ".$info['hora'];

				$this->auditarEnAdministracion("BAJA", $this->tabla_od_comision, $observaciones);
			}
		}
		
		return true;
    }
    
    /**
     * Se obtienen los Expedientes en una Comisión determinada
     * @param  string 	$comision_codigo 	Código de la Comisión
     * @param  integer  $marca_comision     Valor de la marca en comisión (puede no recibirse)
     * @return array  	$datos            	Conjunto de expedientes devueltos
     */
    public function obtenerExpedientesPorComision($comision_codigo, $marca_comision = null) {

		$conexion = $this->conectar();
		
		$filtro_por_marca = (is_null($marca_comision) 
			? " AND E.marca_comision IS NULL" 
			: " AND E.marca_comision = ".$marca_comision);

		$sql = "SELECT 
					E.anio, 
					E.tipo, 
					E.numero, 
					E.caratula, 
					E.iniciador_codigo, 
					E.marca_comision
				FROM ".$this->tabla_expedientes." AS E 
				WHERE E.fecha_entrada_expe BETWEEN '".(date('Y')-15)."-01-01' AND '".date('Y-m-d')."'
				".$filtro_por_marca."
				AND ( SELECT id_codestado 
					  FROM ".$this->tabla_estados."
					  WHERE anio = E.anio 
					  AND tipo = E.tipo 
					  AND numero = E.numero 
					  AND cuerpo = E.cuerpo 
					  AND alcance = E.alcance
					  ORDER BY 
					  	anio DESC, 
					  	tipo DESC, 
					  	numero DESC, 
					  	cuerpo DESC, 
					  	alcance DESC, 
					  	fecha_estado DESC, 
					  	orden_estado DESC
					  LIMIT 1
					) IN (
							(SELECT id_codestado FROM ".$this->tabla_codestados." WHERE id_codestado = 3),
							(SELECT id_codestado FROM ".$this->tabla_codestados." WHERE id_codestado = 16),
							(SELECT id_codestado FROM ".$this->tabla_codestados." WHERE id_codestado = 79)
						 )
				AND ( SELECT comision_codigo 
					  FROM ".$this->tabla_giros."
					  WHERE anio = E.anio 
					  AND tipo = E.tipo 
					  AND numero = E.numero 
					  AND cuerpo = E.cuerpo 
					  AND alcance = E.alcance 
					  AND fecha_entrada_giro IS NOT NULL
					  AND (fecha_salida_giro IS NULL)
					  ORDER BY 
					  	anio DESC, 
					  	tipo DESC, 
					  	numero DESC, 
					  	cuerpo DESC, 
					  	alcance DESC, 
					  	orden_giro DESC 
					  LIMIT 1
					) = '".$comision_codigo."'
				ORDER BY E.anio, E.tipo, E.numero";

		//LibreriaGeneral::registrarLog("sql_obtenerExpedientesPorComision", $sql, '.sql');

		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }
	
	/**
	 * Se obtienen los Extractos de los Proyectos de un expediente determinado
	 * @param  integer $anio   Año del expediente
	 * @param  string  $tipo   Tipo del expediente
	 * @param  integer $numero Número del expediente
	 * @return array   $datos  Extracto de Proyectos del expediente
	 */
	public function obtenerExtractosPorExpediente($anio, $tipo, $numero) {

		$conexion = $this->conectar();

		$sql = "SELECT DISTINCT P.extracto
				FROM ( SELECT * FROM " . $this->tabla_proyectos . "
			  		   WHERE anio = " . $anio . "
			  		   AND tipo = '" . $tipo . "'
			  		   AND numero = " . $numero . "
			 		 ) P
			    LEFT JOIN " . $this->tabla_codproyectos . " CP
			    	ON P.id_codproyecto = CP.id_codproyecto
			    LEFT JOIN " . $this->tabla_expedientes . " E
			    	ON ( E.anio = P.anio AND E.tipo = P.tipo AND E.numero = P.numero )";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

    public function obtenerItemsOrdenComision($id_orden_comision, $marca_comision = '') {

		$conexion = $this->conectar();
		
		$filtro = ($marca_comision != '') ? ' AND CI.marca_comision = '.$marca_comision : '';

		$sql = "SELECT CI.*, E.iniciador_codigo, E.caratula
				FROM ".$this->tabla_od_comision_items." AS CI
				LEFT JOIN " . $this->tabla_expedientes . " AS E
			    	ON ( E.anio = CI.anio AND E.tipo = CI.tipo AND E.numero = CI.numero )
				WHERE CI.id_orden_comision = ".$id_orden_comision."
				".$filtro."
				ORDER BY CI.id_orden_comision, CI.anio, CI.tipo, CI.numero";
		
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }
    
    public function obtenerIdOrdenComisionPorItem($id) {

		$conexion = $this->conectar();
		
		$sql = "SELECT id_orden_comision FROM ".$this->tabla_od_comision_items." WHERE id = ".$id;
		
		$resultado = $this->ejecutarQuery($sql);
		
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['id_orden_comision'];
    }

	public function obtenerRegistroItem($id) {

		$conexion = $this->conectar();
		
		$sql = "SELECT * FROM ".$this->tabla_od_comision_items." WHERE id = ".$id;
		
		$resultado = $this->ejecutarQuery($sql);
		
		$registro = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $registro;
    }
    
    public function validarDatosItem($datos)
    {
		$datos['anio'] = $this->revisarValorAtributo(strip_tags($datos['anio']));
		
		$datos['numero'] = $this->revisarValorAtributo(strip_tags($datos['numero']));
		
		$datos['marca_comision'] = $this->revisarValorAtributo(strip_tags($datos['marca_comision']));
		
		return $datos;
    }

    /**
     *  Se ingresa un Item
     * @param  array 	$datos Info del ítem a ingresar
     * @return boolean
     */
    public function insertarItem($datos)
	{
		$datos = $this->validarDatosItem($datos);

		$info_orden_comision = $this->obtenerRegistro($datos['id_orden_comision']);

		$conexion = $this->conectar();

		$datos['extracto'] = ($datos['extracto'] == '' || $datos['extracto'] == '0') 
			? "null" 
			: "'".mysqli_real_escape_string($conexion, $datos['extracto'])."'";

		$query = "INSERT INTO ".$this->tabla_od_comision_items." 
						(id_orden_comision, 
						 anio, 
						 tipo, 
						 numero, 
						 marca_comision, 
						 extracto
						)
				  VALUES(".$datos['id_orden_comision'].", 
						 ".$datos['anio'].", 
						'".$datos['tipo']."', 
						 ".$datos['numero'].", 
						 ".$datos['marca_comision'].", 
						 ".$datos['extracto'].")";

		//LibreriaGeneral::registrarLog("query_insertarItem", $query);

		if ( !$this->ejecutarQuery($query) )
			return false;
		
		$this->desconectar($conexion);
		
		$observaciones  = "Se ingresa el Item: ".$this->armarVistaPreviaItem($datos);
		$observaciones .= "<br>En la Orden de Comisión de " . $info_orden_comision['asunto'];
		$observaciones .= "<br>Fecha de Orden: ". $info_orden_comision['fecha'];
		$observaciones .= "<br>Hora de Orden: ". $info_orden_comision['hora'];

		$this->auditarEnAdministracion(
			"ALTA", 
			$this->tabla_od_comision_items, 
			$observaciones
		);

		return true;	
    }
	
    /**
     * Se modifica un Item
     * @param  array 	$datos  Info del Item a modificar
     * @return boolean
     */
    public function modificarItem($datos)
	{
		$info_orden_comision = $this->obtenerRegistro($datos['id_orden_comision']);
		
		$conexion = $this->conectar();

		$datos['extracto'] = ($datos['extracto'] == '' || $datos['extracto'] == '0') 
			? "null" 
			: "'".mysqli_real_escape_string($conexion, $datos['extracto'])."'";

		$query = "UPDATE ".$this->tabla_od_comision_items."
				  SET extracto = ".$datos['extracto']."
				  WHERE id = ".$datos['id'];

		if ( !$this->ejecutarQuery($query) )
			return false;
		
		$this->desconectar($conexion);
		
		$observaciones  = "Se modifica el Item: ".$this->armarVistaPreviaItem($datos);
		$observaciones .= "<br>En la Orden de Comisión de " . $info_orden_comision['asunto'];
		$observaciones .= "<br>Fecha de Orden: ". $info_orden_comision['fecha'];
		$observaciones .= "<br>Hora de Orden: ". $info_orden_comision['hora'];

		$this->auditarEnAdministracion(
			"MODIFICA", 
			$this->tabla_od_comision_items, 
			$observaciones
		);

		return true;
    }

	/**
	 * Se elimina un Item
	 * @param  integer $id Identificador del Item
	 * @return boolean
	 */
    public function eliminarItem($id) {

		$info_previa = $this->obtenerRegistroItem($id);

		$info_orden_comision = $this->obtenerRegistro($info_previa['id_orden_comision']);
		
		$conexion = $this->conectar();
		
		$query = "DELETE FROM ".$this->tabla_od_comision_items." WHERE id = ".$id;
						 
		if ( !$this->ejecutarQuery($query) )
			return false;
		else {
			$this->desconectar($conexion);

			$observaciones  = "Se elimina el Item: ".$this->armarVistaPreviaItem($info_previa);
			$observaciones .= "<br>En la Orden de Comisión de " . $info_orden_comision['asunto'];
			$observaciones .= "<br>Fecha de Orden: ". $info_orden_comision['fecha'];
			$observaciones .= "<br>Hora de Orden: ". $info_orden_comision['hora'];

			$this->auditarEnAdministracion(
				"BAJA", 
				$this->tabla_od_comision_items, 
				$observaciones
			);
		}
		
		return true;
    }
    
    /**
	 * Se arma la vista previa de un Item respectivo, utilizado al auditar
	 * @param  array 	$info_item  Información del Item
	 * @return string				Observaciones a auditar
	 */
	public function armarVistaPreviaItem($info_item) {
		// Retorna la vista previa armada
		return $info_item['anio'].' '.$info_item['tipo'].' '.$info_item['numero'].' '.str_replace("'", "", $info_item['extracto']);
	}

    /**
     * Se verifica si el expediente es un Item de una Orden de Comision
     * @param  integer 	$id_orden_comision  Identificador de la Orden de Comisión
     * @param  integer  $anio               Año del expediente
     * @param  string   $tipo               Tipo del expediente
     * @param  integer  $numero             Número del expediente
     * @return boolean
     */
    public function esItemOrdenComision($id_orden_comision, $anio, $tipo, $numero) {

		$conexion = $this->conectar();
		
		$sql = "SELECT  id
				FROM ".$this->tabla_od_comision_items."
				WHERE id_orden_comision = ".$id_orden_comision."
				AND anio = ".$anio."
				AND tipo = '".$tipo."'
				AND numero = ".$numero;

		$resultado = $this->ejecutarQuery($sql);
		
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		// Si es item o no
		return ( isset($dato['id']) && $dato['id'] != '' );
    }

	/**
	 * Se verifica si tiene Items
	 * @param  integer $id_orden_comision 	Identificador de la Orden de Comisión
	 * @return boolean
	 */
	public function tieneItems($id_orden_comision) {

		$conexion = $this->conectar();
		
		$query = "SELECT id 
				  FROM ".$this->tabla_od_comision_items." 
				  WHERE id_orden_comision = ".$id_orden_comision;
				  
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		// Si existe o no
		return ( isset($dato['id']) && $dato['id'] != '' );
	}
	
	/**
	 * Se confirma la publicación de la orden del dia de comision
	 *
	 * @param  integer $id
	 * @return boolean
	 */
	public function confirmarPublicacion($id) {

		$info_orden_comision = $this->obtenerRegistro($id);

		$conexion = $this->conectar();

		$query = "UPDATE " . $this->tabla_od_comision . " 
				  SET publicada = '1' 
				  WHERE id = " . $id;

		if (!$this->ejecutarQuery($query)) {
			return false;
		}

		$this->desconectar($conexion);

		$observaciones  = "Se publica la Orden de Comisi&oacute;n: " . $info_orden_comision['asunto'];
		$observaciones .= "<br>Fecha de Orden: ". $info_orden_comision['fecha'];
		$observaciones .= "<br>Hora de Orden: ". $info_orden_comision['hora'];

		$this->auditarEnAdministracion("PUBLICACION", $this->tabla_od_comision, $observaciones);

		return true;
	}

}

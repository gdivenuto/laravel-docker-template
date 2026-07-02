<?php
if (!isset($_SESSION)) {
	session_start();
}

class mailsPrensaModel extends ModeloBaseMySQLi
{
	public function __construct() {
		parent::__construct();
	}

    public function conectar() {
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(1);
	}
		
    public function listar()
	{
		$conexion = $this->conectar();

		$filtro = "";
		$limite = "";
		
		// PARA FILTRAR POR FECHA
		if ( $this->filtro['f_fecha'] != '' )
			$filtro .= " AND mlp_fecha = '".$this->filtro['f_fecha']."'";
		
		// PARA FILTRAR POR TITULO
		if ( $this->filtro['f_titulo'] != '' )
			$filtro .= " AND mlp_titulo LIKE '%".$this->filtro['f_titulo']."%'";
		
		// PARA LIMITAR EL LISTADO
		if ( $this->filtro['rango'] != 0 )
			$limite = " LIMIT ".$this->filtro['inicio'].", ".$this->filtro['rango'];
		
		$sql = "SELECT * FROM ".$this->tabla_mails_lista_prensa."
				WHERE mlp_fecha IS NOT NULL
				".$filtro."
				ORDER BY ".$_SESSION['ultimo_campo']." ".$_SESSION['ultimo_sentido']."	 
			   ".$limite;
		
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
			
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function obtenerCantidad()
	{
		$conexion = $this->conectar();
		
		$filtro = "";

		// PARA FILTRAR POR FECHA
		if ( $this->filtro['f_fecha'] != '' )
			$filtro .= " AND mlp_fecha = '".$this->filtro['f_fecha']."'";
		
		// PARA FILTRAR POR TITULO
		if ( $this->filtro['f_titulo'] != '' )
			$filtro .= " AND mlp_titulo LIKE '%".$this->filtro['f_titulo']."%'";
		
		$query = "SELECT COUNT(mlp_id) AS cantidad
				  FROM ".$this->tabla_mails_lista_prensa." 
				  WHERE mlp_fecha IS NOT NULL
				  ".$filtro;
		  		  
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		return $dato['cantidad'];
	}
	
    public function obtenerInfoMail($id)
	{    
		$conexion = $this->conectar();
		
		$query = "SELECT * FROM ".$this->tabla_mails_lista_prensa." WHERE mlp_id = ".$id;
		
		$resultado = $this->ejecutarQuery($query);
		
		$registro = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $registro;
    }

    public function validarDatos($datos) {

		$datos['mlp_fecha'] = $this->revisarValorFechaAtributo($datos['mlp_fecha']);
		
		$datos['mlp_titulo'] = $this->revisarValorAtributo(strip_tags($datos['mlp_titulo']));
		
		$datos['mlp_texto'] = $this->revisarValorAtributo($datos['mlp_texto']);
		
		$datos['mlp_id_gacetilla'] = $this->revisarValorAtributo($datos['mlp_id_gacetilla'], 0);
		
		return $datos;
    }
	
    public function insertar($datos) {

		$conexion = $this->conectar();
		
		$datos = $this->validarDatos($datos);
		
		$query = "INSERT INTO ".$this->tabla_mails_lista_prensa." 
						(mlp_fecha, mlp_titulo, mlp_texto, mlp_id_gacetilla)
				  VALUES(".$datos['mlp_fecha'].", 
						 ".$datos['mlp_titulo'].", 
						 ".$datos['mlp_texto'].", 
						 ".$datos['mlp_id_gacetilla'].");";
	
		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {	
			$this->desconectar($conexion);

			// Se audita
			$this->auditarEnAdministracion("ENVIO", $this->tabla_mails_lista_prensa, "Se registra el envio de un mail a la lista de Correo: ".LibreriaGeneral::eliminarComillaSimple($datos['mlp_titulo']));
		}

		return true;
	}
	
}
?>

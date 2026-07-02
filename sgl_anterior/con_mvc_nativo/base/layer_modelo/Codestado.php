<?php
/**
 * Clase Codestado
 *
 * Descripción: expe_codestados
 * Layer Datos: DBExpedientesParam
 * Layer Negocio: NGExpedientesParam
 *
 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:57
 *
 * 07/01/2022 XXXX: se retira el campo codigo_estado
 */
class Codestado extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $id_codestado;// (Primary Key) PHP: integer    MySQL: int(10) unsigned
	protected $nombre_estado;			  // PHP: string     MySQL: varchar(30) [Permite NULL]
	protected $vigencia_desde_codestado;  // PHP: string     MySQL: date [Permite NULL]
	protected $vigencia_hasta_codestado;  // PHP: string     MySQL: date [Permite NULL]
	protected $observaciones_codestado;   // PHP: string     MySQL: text [Permite NULL]
	protected $habilitado_codestado; 	  // PHP: string     MySQL: char(1)
	protected $id_usuario;				  // PHP: integer    MySQL: int(10) unsigned
	protected $tratamiento_comision; 	  // PHP: bool       MySQL: tinyint(1)

	// ************************************************************************
	// Getters & Setters ******************************************************
	// ************************************************************************
	public function getId_Codestado() { return $this->id_codestado; }
	public function setId_Codestado($value) {
		if (is_null($value))
			throw new UnexpectedValueException(sprintf("Error en %s.setId_Codestado(): no se permiten valores nulos para el atributo 'id_codestado'.", get_class($this)));
		if ( (!$this->esInteger($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setId_Codestado(): el atributo 'id_codestado' solo permite valores de tipo integer.", get_class($this)));
		$this->id_codestado = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getNombre_Estado() { return $this->nombre_estado; }
	public function setNombre_Estado($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setNombre_Estado(): el atributo 'nombre_estado' solo permite valores de tipo string.", get_class($this)));
		$this->nombre_estado = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getVigencia_Desde_Codestado() { return $this->vigencia_desde_codestado; }
	public function setVigencia_Desde_Codestado($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setVigencia_Desde_Codestado(): el atributo 'vigencia_desde_codestado' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); }
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setVigencia_Desde_Codestado(): el atributo 'vigencia_desde_codestado' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); }
		$this->vigencia_desde_codestado = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getVigencia_Hasta_Codestado() { return $this->vigencia_hasta_codestado; }
	public function setVigencia_Hasta_Codestado($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setVigencia_Hasta_Codestado(): el atributo 'vigencia_hasta_codestado' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); }
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setVigencia_Hasta_Codestado(): el atributo 'vigencia_hasta_codestado' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); }
		$this->vigencia_hasta_codestado = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getObservaciones_Codestado() { return $this->observaciones_codestado; }
	public function setObservaciones_Codestado($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setObservaciones_Codestado(): el atributo 'observaciones_codestado' solo permite valores de tipo string.", get_class($this)));
		$this->observaciones_codestado = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getHabilitado_Codestado() { return $this->habilitado_codestado; }
	public function setHabilitado_Codestado($value) {
		if (is_null($value))
			throw new UnexpectedValueException(sprintf("Error en %s.setHabilitado_Codestado(): no se permiten valores nulos para el atributo 'habilitado_codestado'.", get_class($this)));
		if ( (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setHabilitado_Codestado(): el atributo 'habilitado_codestado' solo permite valores de tipo string.", get_class($this)));
		$this->habilitado_codestado = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getId_Usuario() { return $this->id_usuario; }
	public function setId_Usuario($value) {
		if (is_null($value))
			throw new UnexpectedValueException(sprintf("Error en %s.setId_Usuario(): no se permiten valores nulos para el atributo 'id_usuario'.", get_class($this)));
		if ( (!$this->esInteger($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setId_Usuario(): el atributo 'id_usuario' solo permite valores de tipo integer.", get_class($this)));
		$this->id_usuario = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getTratamiento_Comision() { return $this->tratamiento_comision; }
	public function setTratamiento_Comision($value) {
		if (is_null($value))
			throw new UnexpectedValueException(sprintf("Error en %s.setTratamiento_Comision(): no se permiten valores nulos para el atributo 'tratamiento_comision'.", get_class($this)));
		if ( (!$this->esBoolean($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setTratamiento_Comision(): el atributo 'tratamiento_comision' solo permite valores de tipo boolean.", get_class($this)));
		$this->tratamiento_comision = $this->obtenerBoolean($value);
		$this->setInstanceState(IS_MODIFIED);
	}

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 * @param  integer (PK) id_codestado
	 * @param  string nombre_estado
	 * @param  string vigencia_desde_codestado
	 * @param  string vigencia_hasta_codestado
	 * @param  string observaciones_codestado
	 * @param  string habilitado_codestado
	 * @param  integer id_usuario
	 * @param  bool tratamiento_comision
	 */
	public function __construct(
		$pid_codestado = null,
		$pnombre_estado = null,
		$pvigencia_desde_codestado = null,
		$pvigencia_hasta_codestado = null,
		$pobservaciones_codestado = null,
		$phabilitado_codestado = '',
		$pid_usuario = 0,
		$ptratamiento_comision = true)
	{
		// Asignación de atributos.
		// Si las propiedades se acceden desde un ámbito con visibilidad directa, no
		// se disparan los métodos mágicos __get() y __set(). Por el contrario, si se
		// acceden 'desde fuera de la instancia', se disparan los métodos mágicos.
		$this->id_codestado         	= $pid_codestado;
		$this->nombre_estado        	= $pnombre_estado;
		$this->vigencia_desde_codestado = $pvigencia_desde_codestado;
		$this->vigencia_hasta_codestado = $pvigencia_hasta_codestado;
		$this->observaciones_codestado  = $pobservaciones_codestado;
		$this->habilitado_codestado 	= $phabilitado_codestado;
		$this->id_usuario           	= $pid_usuario;
		$this->tratamiento_comision 	= $ptratamiento_comision;

		// Invocación de inicialización de clase padre.
		parent::__construct();
	}
}
?>

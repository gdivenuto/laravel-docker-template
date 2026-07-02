<?php
/**
 * Clase Codproyecto
 *
 * Descripción: expe_codproyectos
 * Layer Datos: DBExpedientesParam
 * Layer Negocio: NGExpedientesParam
 *
 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:57
 *
 * 07/01/2022 XXXX: se retira el campo codigo_proyecto
 */
class Codproyecto extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $id_codproyecto        ; // (Primary Key) PHP: integer    MySQL: int(10) unsigned
	protected $descripcion_proyecto  ; // PHP: string     MySQL: varchar(30) [Permite NULL]
	protected $vigencia_desde_codproy; // PHP: string     MySQL: date [Permite NULL]
	protected $vigencia_hasta_codproy; // PHP: string     MySQL: date [Permite NULL]
	protected $habilitado_codproy	 ; // PHP: string     MySQL: char(1)
	protected $id_usuario            ; // PHP: integer    MySQL: int(10) unsigned

	// ************************************************************************
	// Getters & Setters ******************************************************
	// ************************************************************************
	public function getId_Codproyecto() { return $this->id_codproyecto; }
	public function setId_Codproyecto($value) {
		if (is_null($value))
			throw new UnexpectedValueException(sprintf("Error en %s.setId_Codproyecto(): no se permiten valores nulos para el atributo 'id_codproyecto'.", get_class($this)));
		if ( (!$this->esInteger($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setId_Codproyecto(): el atributo 'id_codproyecto' solo permite valores de tipo integer.", get_class($this)));
		$this->id_codproyecto = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getDescripcion_Proyecto() { return $this->descripcion_proyecto; }
	public function setDescripcion_Proyecto($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setDescripcion_Proyecto(): el atributo 'descripcion_proyecto' solo permite valores de tipo string.", get_class($this)));
		$this->descripcion_proyecto = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getVigencia_Desde_Codproy() { return $this->vigencia_desde_codproy; }
	public function setVigencia_Desde_Codproy($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setVigencia_Desde_Codproy(): el atributo 'vigencia_desde_codproy' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); }
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setVigencia_Desde_Codproy(): el atributo 'vigencia_desde_codproy' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); }
		$this->vigencia_desde_codproy = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getVigencia_Hasta_Codproy() { return $this->vigencia_hasta_codproy; }
	public function setVigencia_Hasta_Codproy($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setVigencia_Hasta_Codproy(): el atributo 'vigencia_hasta_codproy' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); }
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setVigencia_Hasta_Codproy(): el atributo 'vigencia_hasta_codproy' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); }
		$this->vigencia_hasta_codproy = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getHabilitado_Codproy() { return $this->habilitado_codproy; }
	public function setHabilitado_Codproy($value) {
		if (is_null($value))
			throw new UnexpectedValueException(sprintf("Error en %s.setHabilitado_Codproy(): no se permiten valores nulos para el atributo 'habilitado_codproy'.", get_class($this)));
		if ( (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setHabilitado_Codproy(): el atributo 'habilitado_codproy' solo permite valores de tipo string.", get_class($this)));
		$this->habilitado_codproy = $value;
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

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 * @param  integer (PK) id_codproyecto
	 * @param  string descripcion_proyecto
	 * @param  string vigencia_desde_codproy
	 * @param  string vigencia_hasta_codproy
	 * @param  string habilitado_codproy
	 * @param  integer id_usuario
	 */
	public function __construct(
		$pid_codproyecto = null,
		$pdescripcion_proyecto = null,
		$pvigencia_desde_codproy = null,
		$pvigencia_hasta_codproy = null,
		$phabilitado_codproy = '',
		$pid_usuario = 0)
	{
		// Asignación de atributos.
		// Si las propiedades se acceden desde un ámbito con visibilidad directa, no
		// se disparan los métodos mágicos __get() y __set(). Por el contrario, si se
		// acceden 'desde fuera de la instancia', se disparan los métodos mágicos.
		$this->id_codproyecto         = $pid_codproyecto;
		$this->descripcion_proyecto   = $pdescripcion_proyecto;
		$this->vigencia_desde_codproy = $pvigencia_desde_codproy;
		$this->vigencia_hasta_codproy = $pvigencia_hasta_codproy;
		$this->habilitado_codproy 	  = $phabilitado_codproy;
		$this->id_usuario             = $pid_usuario;

		// Invocación de inicialización de clase padre.
		parent::__construct();
	}
}
?>

<?php
/**
 * Clase Codtema
 *
 * Descripción: expe_codtemas
 * Layer Datos: DBExpedientesParam
 * Layer Negocio: NGExpedientesParam
 *
 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:05
 *
 * 07/01/2022 XXXX: se retira el campo codigo_tema
 */
class Codtema extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $id_codtema          ; // (Primary Key) PHP: integer    MySQL: int(10) unsigned
	protected $descripcion_tema    ; // PHP: string     MySQL: varchar(30) [Permite NULL]
	protected $vigencia_desde_tema ; // PHP: string     MySQL: date [Permite NULL]
	protected $vigencia_hasta_tema ; // PHP: string     MySQL: date [Permite NULL]
	protected $habilitado_tema     ; // PHP: string     MySQL: char(1)
	protected $id_usuario          ; // PHP: integer    MySQL: int(10) unsigned

	// ************************************************************************
	// Getters & Setters ******************************************************
	// ************************************************************************
	public function getId_Codtema() { return $this->id_codtema; }
	public function setId_Codtema($value) {
		if (is_null($value))
			throw new UnexpectedValueException(sprintf("Error en %s.setId_Codtema(): no se permiten valores nulos para el atributo 'id_codtema'.", get_class($this)));
		if ( (!$this->esInteger($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setId_Codtema(): el atributo 'id_codtema' solo permite valores de tipo integer.", get_class($this)));
		$this->id_codtema = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getDescripcion_Tema() { return $this->descripcion_tema; }
	public function setDescripcion_Tema($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setDescripcion_Tema(): el atributo 'descripcion_tema' solo permite valores de tipo string.", get_class($this)));
		$this->descripcion_tema = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getVigencia_Desde_Tema() { return $this->vigencia_desde_tema; }
	public function setVigencia_Desde_Tema($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setVigencia_Desde_Tema(): el atributo 'vigencia_desde_tema' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); }
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setVigencia_Desde_Tema(): el atributo 'vigencia_desde_tema' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); }
		$this->vigencia_desde_tema = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getVigencia_Hasta_Tema() { return $this->vigencia_hasta_tema; }
	public function setVigencia_Hasta_Tema($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setVigencia_Hasta_Tema(): el atributo 'vigencia_hasta_tema' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); }
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setVigencia_Hasta_Tema(): el atributo 'vigencia_hasta_tema' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); }
		$this->vigencia_hasta_tema = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getHabilitado_Tema() { return $this->habilitado_tema; }
	public function setHabilitado_Tema($value) {
		if (is_null($value))
			throw new UnexpectedValueException(sprintf("Error en %s.setHabilitado_Tema(): no se permiten valores nulos para el atributo 'habilitado_tema'.", get_class($this)));
		if ( (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setHabilitado_Tema(): el atributo 'habilitado_tema' solo permite valores de tipo string.", get_class($this)));
		$this->habilitado_tema = $value;
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
	 * @param  integer (PK) id_codtema
	 * @param  string descripcion_tema
	 * @param  string vigencia_desde_tema
	 * @param  string vigencia_hasta_tema
	 * @param  string habilitado_tema
	 * @param  integer id_usuario
	 */
	public function __construct(
		$pid_codtema = null,
		$pdescripcion_tema = null,
		$pvigencia_desde_tema = null,
		$pvigencia_hasta_tema = null,
		$phabilitado_tema = '',
		$pid_usuario = 0)
	{
		// Asignación de atributos.
		// Si las propiedades se acceden desde un ámbito con visibilidad directa, no
		// se disparan los métodos mágicos __get() y __set(). Por el contrario, si se
		// acceden 'desde fuera de la instancia', se disparan los métodos mágicos.
		$this->id_codtema           = $pid_codtema;
		$this->descripcion_tema     = $pdescripcion_tema;
		$this->vigencia_desde_tema  = $pvigencia_desde_tema;
		$this->vigencia_hasta_tema  = $pvigencia_hasta_tema;
		$this->habilitado_tema      = $phabilitado_tema;
		$this->id_usuario           = $pid_usuario;

		// Invocación de inicialización de clase padre.
		parent::__construct();
	}
}
?>

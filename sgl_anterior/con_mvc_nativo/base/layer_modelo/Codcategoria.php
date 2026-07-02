<?php
/**
 * Clase Codcategoria
 *
 * Descripción: expe_codcategoria
 * Layer Datos: DBExpedientesParam
 * Layer Negocio: NGExpedientesParam
 *
 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:49
 *
 * 07/01/2022 XXXX: se retira el campo codigo_categoria
 */
class Codcategoria extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $id_codcategoria     ; // (Primary Key) PHP: integer    MySQL: int(10) unsigned
	protected $descripcion_categoria; // PHP: string     MySQL: varchar(60) [Permite NULL]
	protected $vigencia_desde_categoria; // PHP: string     MySQL: date [Permite NULL]
	protected $vigencia_hasta_categoria; // PHP: string     MySQL: date [Permite NULL]
	protected $habilitado_categoria; // PHP: string     MySQL: char(1)
	protected $id_usuario          ; // PHP: integer    MySQL: int(10) unsigned

	// ************************************************************************
	// Getters & Setters ******************************************************
	// ************************************************************************
	public function getId_Codcategoria() { return $this->id_codcategoria; }
	public function setId_Codcategoria($value) {
		if (is_null($value))
			throw new UnexpectedValueException(sprintf("Error en %s.setId_Codcategoria(): no se permiten valores nulos para el atributo 'id_codcategoria'.", get_class($this)));
		if ( (!$this->esInteger($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setId_Codcategoria(): el atributo 'id_codcategoria' solo permite valores de tipo integer.", get_class($this)));
		$this->id_codcategoria = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getDescripcion_Categoria() { return $this->descripcion_categoria; }
	public function setDescripcion_Categoria($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setDescripcion_Categoria(): el atributo 'descripcion_categoria' solo permite valores de tipo string.", get_class($this)));
		$this->descripcion_categoria = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getVigencia_Desde_Categoria() { return $this->vigencia_desde_categoria; }
	public function setVigencia_Desde_Categoria($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setVigencia_Desde_Categoria(): el atributo 'vigencia_desde_categoria' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); }
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setVigencia_Desde_Categoria(): el atributo 'vigencia_desde_categoria' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); }
		$this->vigencia_desde_categoria = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getVigencia_Hasta_Categoria() { return $this->vigencia_hasta_categoria; }
	public function setVigencia_Hasta_Categoria($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setVigencia_Hasta_Categoria(): el atributo 'vigencia_hasta_categoria' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); }
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setVigencia_Hasta_Categoria(): el atributo 'vigencia_hasta_categoria' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); }
		$this->vigencia_hasta_categoria = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getHabilitado_Categoria() { return $this->habilitado_categoria; }
	public function setHabilitado_Categoria($value) {
		if (is_null($value))
			throw new UnexpectedValueException(sprintf("Error en %s.setHabilitado_Categoria(): no se permiten valores nulos para el atributo 'habilitado_categoria'.", get_class($this)));
		if ( (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setHabilitado_Categoria(): el atributo 'habilitado_categoria' solo permite valores de tipo string.", get_class($this)));
		$this->habilitado_categoria = $value;
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
	 * @param  integer (PK) id_codcategoria
	 * @param  string descripcion_categoria
	 * @param  string vigencia_desde_categoria
	 * @param  string vigencia_hasta_categoria
	 * @param  string habilitado_categoria
	 * @param  integer id_usuario
	 */
	public function __construct(
		$pid_codcategoria = '',
		$pdescripcion_categoria = null,
		$pvigencia_desde_categoria = null,
		$pvigencia_hasta_categoria = null,
		$phabilitado_categoria = '',
		$pid_usuario = 0)
	{
		// Asignación de atributos.
		// Si las propiedades se acceden desde un ámbito con visibilidad directa, no
		// se disparan los métodos mágicos __get() y __set(). Por el contrario, si se
		// acceden 'desde fuera de la instancia', se disparan los métodos mágicos.
		$this->id_codcategoria      = $pid_codcategoria;
		$this->descripcion_categoria = $pdescripcion_categoria;
		$this->vigencia_desde_categoria = $pvigencia_desde_categoria;
		$this->vigencia_hasta_categoria = $pvigencia_hasta_categoria;
		$this->habilitado_categoria = $phabilitado_categoria;
		$this->id_usuario           = $pid_usuario;

		// Invocación de inicialización de clase padre.
		parent::__construct();
	}
}
?>

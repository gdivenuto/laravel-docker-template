<?php
/**
 * Clase Parametro
 * 
 * Descripción: sglv2_parametros 
 * Layer Datos: DBConfiguracion
 * Layer Negocio: NGConfiguracion
 *
 * GenerateClass 0.97.5 beta @ 2016-09-20 10:56:46
 */
class Parametro extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $parametro           ; // (Primary Key) PHP: string     MySQL: char(100)
	protected $val_int             ; // PHP: integer    MySQL: int(11) [Permite NULL]
	protected $val_string          ; // PHP: string     MySQL: varchar(200) [Permite NULL]
	protected $val_datetime        ; // PHP: string     MySQL: datetime [Permite NULL]
	protected $val_text            ; // PHP: string     MySQL: text [Permite NULL]
	protected $val_double          ; // PHP: float      MySQL: double [Permite NULL]
	
	// ************************************************************************
	// Getters & Setters ******************************************************
	// ************************************************************************
	public function getParametro() { return $this->parametro; }
	public function setParametro($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setParametro(): no se permiten valores nulos para el atributo 'parametro'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setParametro(): el atributo 'parametro' solo permite valores de tipo string.", get_class($this)));
		$this->parametro = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getVal_Int() { return $this->val_int; }
	public function setVal_Int($value) { 
		if ( (!is_null($value)) && (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setVal_Int(): el atributo 'val_int' solo permite valores de tipo integer.", get_class($this)));
		$this->val_int = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getVal_String() { return $this->val_string; }
	public function setVal_String($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setVal_String(): el atributo 'val_string' solo permite valores de tipo string.", get_class($this)));
		$this->val_string = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getVal_Datetime() { return $this->val_datetime; }
	public function setVal_Datetime($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setVal_Datetime(): el atributo 'val_datetime' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setVal_Datetime(): el atributo 'val_datetime' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->val_datetime = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getVal_Text() { return $this->val_text; }
	public function setVal_Text($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setVal_Text(): el atributo 'val_text' solo permite valores de tipo string.", get_class($this)));
		$this->val_text = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getVal_Double() { return $this->val_double; }
	public function setVal_Double($value) { 
		if ( (!is_null($value)) && (!($this->esFloat($value) || $this->esInteger($value))) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setVal_Double(): el atributo 'val_double' solo permite valores de tipo float o double.", get_class($this)));
		$this->val_double = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 * @param  string (PK) parametro
	 * @param  integer val_int
	 * @param  string val_string
	 * @param  string val_datetime
	 * @param  string val_text
	 * @param  float val_double
	 */
	public function __construct(
		$pparametro = '',
		$pval_int = null,
		$pval_string = null,
		$pval_datetime = null,
		$pval_text = null,
		$pval_double = null)
	{
		// Asignación de atributos.
		// Si las propiedades se acceden desde un ámbito con visibilidad directa, no 
		// se disparan los métodos mágicos __get() y __set(). Por el contrario, si se 
		// acceden 'desde fuera de la instancia', se disparan los métodos mágicos.
		$this->parametro            = $pparametro;
		$this->val_int              = $pval_int;
		$this->val_string           = $pval_string;
		$this->val_datetime         = $pval_datetime;
		$this->val_text             = $pval_text;
		$this->val_double           = $pval_double;	
		
		// Invocación de inicialización de clase padre.
		parent::__construct();
	}
}
?>
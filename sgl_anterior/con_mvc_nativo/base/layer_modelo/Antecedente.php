<?php
/**
 * Clase Antecedente
 * 
 * Descripción: expe_antecedentes 
 * Layer Datos: DBExpedientes
 * Layer Negocio: NGExpedientes
 *
 * GenerateClass 0.97.4 beta @ 2016-09-01 13:50:59
 */
class Antecedente extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $anio                ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $tipo                ; // (Primary Key) PHP: string     MySQL: char(1)
	protected $numero              ; // (Primary Key) PHP: float      MySQL: decimal(10,0)
	protected $cuerpo              ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $alcance             ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $anio_a              ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $tipo_a              ; // (Primary Key) PHP: string     MySQL: char(1)
	protected $numero_a            ; // (Primary Key) PHP: float      MySQL: decimal(10,0)
	protected $digito_a            ; // (Primary Key) PHP: string     MySQL: char(2)
	protected $cuerpo_a            ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $alcance_a           ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $cuerpoalcance_a     ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $anexoalcance_a      ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $cuerpoanexoalcance_a; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $anexo_a             ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $cuerpoanexo_a       ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $observaciones_antecedentes; // PHP: string     MySQL: text [Permite NULL]
	protected $id_usuario          ; // PHP: integer    MySQL: int(10) unsigned
	
	// Atributos extra (no hay relación con capa de datos)
	// define si existe su directorio respectivo en 'expe-de', 
	// en caso que el expediente sea del D.E.
	// de ser así, será: /expe-de/AAAA/AAAA-NNNNNN-D
	protected $existe_directorio_expe_depto_ejecutivo;
	// ************************************************************************
	// Getters & Setters ******************************************************
	// ************************************************************************
	public function getAnio() { return $this->anio; }
	public function setAnio($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setAnio(): no se permiten valores nulos para el atributo 'anio'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setAnio(): el atributo 'anio' solo permite valores de tipo integer.", get_class($this)));
		$this->anio = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getTipo() { return $this->tipo; }
	public function setTipo($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setTipo(): no se permiten valores nulos para el atributo 'tipo'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setTipo(): el atributo 'tipo' solo permite valores de tipo string.", get_class($this)));
		$this->tipo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getNumero() { return $this->numero; }
	public function setNumero($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setNumero(): no se permiten valores nulos para el atributo 'numero'.", get_class($this)));
		if ( (!($this->esFloat($value) || $this->esInteger($value))) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setNumero(): el atributo 'numero' solo permite valores de tipo float o double.", get_class($this)));
		$this->numero = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getCuerpo() { return $this->cuerpo; }
	public function setCuerpo($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setCuerpo(): no se permiten valores nulos para el atributo 'cuerpo'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setCuerpo(): el atributo 'cuerpo' solo permite valores de tipo integer.", get_class($this)));
		$this->cuerpo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAlcance() { return $this->alcance; }
	public function setAlcance($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setAlcance(): no se permiten valores nulos para el atributo 'alcance'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setAlcance(): el atributo 'alcance' solo permite valores de tipo integer.", get_class($this)));
		$this->alcance = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAnio_A() { return $this->anio_a; }
	public function setAnio_A($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setAnio_A(): no se permiten valores nulos para el atributo 'anio_a'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setAnio_A(): el atributo 'anio_a' solo permite valores de tipo integer.", get_class($this)));
		$this->anio_a = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getTipo_A() { return $this->tipo_a; }
	public function setTipo_A($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setTipo_A(): no se permiten valores nulos para el atributo 'tipo_a'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setTipo_A(): el atributo 'tipo_a' solo permite valores de tipo string.", get_class($this)));
		$this->tipo_a = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getNumero_A() { return $this->numero_a; }
	public function setNumero_A($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setNumero_A(): no se permiten valores nulos para el atributo 'numero_a'.", get_class($this)));
		if ( (!($this->esFloat($value) || $this->esInteger($value))) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setNumero_A(): el atributo 'numero_a' solo permite valores de tipo float o double.", get_class($this)));
		$this->numero_a = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getDigito_A() { return $this->digito_a; }
	public function setDigito_A($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setDigito_A(): no se permiten valores nulos para el atributo 'digito_a'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setDigito_A(): el atributo 'digito_a' solo permite valores de tipo string.", get_class($this)));
		$this->digito_a = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getCuerpo_A() { return $this->cuerpo_a; }
	public function setCuerpo_A($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setCuerpo_A(): no se permiten valores nulos para el atributo 'cuerpo_a'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setCuerpo_A(): el atributo 'cuerpo_a' solo permite valores de tipo integer.", get_class($this)));
		$this->cuerpo_a = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAlcance_A() { return $this->alcance_a; }
	public function setAlcance_A($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setAlcance_A(): no se permiten valores nulos para el atributo 'alcance_a'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setAlcance_A(): el atributo 'alcance_a' solo permite valores de tipo integer.", get_class($this)));
		$this->alcance_a = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getCuerpoalcance_A() { return $this->cuerpoalcance_a; }
	public function setCuerpoalcance_A($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setCuerpoalcance_A(): no se permiten valores nulos para el atributo 'cuerpoalcance_a'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setCuerpoalcance_A(): el atributo 'cuerpoalcance_a' solo permite valores de tipo integer.", get_class($this)));
		$this->cuerpoalcance_a = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAnexoalcance_A() { return $this->anexoalcance_a; }
	public function setAnexoalcance_A($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setAnexoalcance_A(): no se permiten valores nulos para el atributo 'anexoalcance_a'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setAnexoalcance_A(): el atributo 'anexoalcance_a' solo permite valores de tipo integer.", get_class($this)));
		$this->anexoalcance_a = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getCuerpoanexoalcance_A() { return $this->cuerpoanexoalcance_a; }
	public function setCuerpoanexoalcance_A($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setCuerpoanexoalcance_A(): no se permiten valores nulos para el atributo 'cuerpoanexoalcance_a'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setCuerpoanexoalcance_A(): el atributo 'cuerpoanexoalcance_a' solo permite valores de tipo integer.", get_class($this)));
		$this->cuerpoanexoalcance_a = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAnexo_A() { return $this->anexo_a; }
	public function setAnexo_A($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setAnexo_A(): no se permiten valores nulos para el atributo 'anexo_a'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setAnexo_A(): el atributo 'anexo_a' solo permite valores de tipo integer.", get_class($this)));
		$this->anexo_a = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getCuerpoanexo_A() { return $this->cuerpoanexo_a; }
	public function setCuerpoanexo_A($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setCuerpoanexo_A(): no se permiten valores nulos para el atributo 'cuerpoanexo_a'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setCuerpoanexo_A(): el atributo 'cuerpoanexo_a' solo permite valores de tipo integer.", get_class($this)));
		$this->cuerpoanexo_a = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getObservaciones_Antecedentes() { return $this->observaciones_antecedentes; }
	public function setObservaciones_Antecedentes($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setObservaciones_Antecedentes(): el atributo 'observaciones_antecedentes' solo permite valores de tipo string.", get_class($this)));
		$this->observaciones_antecedentes = $value;
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

	// Atributos extra
	public function getExisteDirectorioDE() { return $this->existe_directorio_expe_depto_ejecutivo; }
	public function setExisteDirectorioDE($value) { $this->existe_directorio_expe_depto_ejecutivo = $value; }

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) anio_a
	 * @param  string (PK) tipo_a
	 * @param  float (PK) numero_a
	 * @param  string (PK) digito_a
	 * @param  integer (PK) cuerpo_a
	 * @param  integer (PK) alcance_a
	 * @param  integer (PK) cuerpoalcance_a
	 * @param  integer (PK) anexoalcance_a
	 * @param  integer (PK) cuerpoanexoalcance_a
	 * @param  integer (PK) anexo_a
	 * @param  integer (PK) cuerpoanexo_a
	 * @param  string observaciones_antecedentes
	 * @param  integer id_usuario
	 */
	public function __construct(
		$panio = 0,
		$ptipo = '',
		$pnumero = 0.0,
		$pcuerpo = 0,
		$palcance = 0,
		$panio_a = 0,
		$ptipo_a = 'E',
		$pnumero_a = 0.0,
		$pdigito_a = '',
		$pcuerpo_a = 0,
		$palcance_a = 0,
		$pcuerpoalcance_a = 0,
		$panexoalcance_a = 0,
		$pcuerpoanexoalcance_a = 0,
		$panexo_a = 0,
		$pcuerpoanexo_a = 0,
		$pobservaciones_antecedentes = null,
		$pid_usuario = 0)
	{
		// Asignación de atributos.
		// Si las propiedades se acceden desde un ámbito con visibilidad directa, no 
		// se disparan los métodos mágicos __get() y __set(). Por el contrario, si se 
		// acceden 'desde fuera de la instancia', se disparan los métodos mágicos.
		$this->anio                 = $panio;
		$this->tipo                 = $ptipo;
		$this->numero               = $pnumero;
		$this->cuerpo               = $pcuerpo;
		$this->alcance              = $palcance;
		$this->anio_a               = $panio_a;
		$this->tipo_a               = $ptipo_a;
		$this->numero_a             = $pnumero_a;
		$this->digito_a             = $pdigito_a;
		$this->cuerpo_a             = $pcuerpo_a;
		$this->alcance_a            = $palcance_a;
		$this->cuerpoalcance_a      = $pcuerpoalcance_a;
		$this->anexoalcance_a       = $panexoalcance_a;
		$this->cuerpoanexoalcance_a = $pcuerpoanexoalcance_a;
		$this->anexo_a              = $panexo_a;
		$this->cuerpoanexo_a        = $pcuerpoanexo_a;
		$this->observaciones_antecedentes = $pobservaciones_antecedentes;
		$this->id_usuario           = $pid_usuario;	
		
		// Atributos extra
		$this->existe_directorio_expe_depto_ejecutivo = null;

		// Invocación de inicialización de clase padre.
		parent::__construct();
	}
}
?>
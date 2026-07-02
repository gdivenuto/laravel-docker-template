<?php
/**
 * Clase Proyecto
 *
 * Descripción: expe_proyectos
 * Layer Datos: DBExpedientes
 * Layer Negocio: NGExpedientes
 *
 * GenerateClass 0.97.4 beta @ 2016-09-01 13:51:00
 */
class Proyecto extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $anio                ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $tipo                ; // (Primary Key) PHP: string     MySQL: char(1)
	protected $numero              ; // (Primary Key) PHP: float      MySQL: decimal(10,0)
	protected $cuerpo              ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $alcance             ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $orden_proyecto      ; // (Primary Key) PHP: float      MySQL: decimal(2,0)
	protected $id_codproyecto      ; // PHP: integer    MySQL: int(10) unsigned
	protected $extracto            ; // PHP: string     MySQL: text [Permite NULL]
	protected $observaciones_proyecto; // PHP: string     MySQL: text [Permite NULL]
	protected $id_usuario          ; // PHP: integer    MySQL: int(10) unsigned

	// Atributos de sólo lectura
	protected $ro_descripcion_proyecto; // Asociado a id_codproyecto
	protected $ro_numero_promulga; 		// Asociado a la sancion
	protected $ro_fecha_promulga;		// Asociado a la sancion
	protected $ro_decreto_promulga; 	// Asociado a la sancion
	protected $ro_fecha_veto;		 	// Asociado a la sancion

	// 05/05/2017: agregados por XXXX
	protected $ro_fecha_sancion;		// Asociado a la sancion
	protected $ro_numero_sancion;		// Asociado a la sancion

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

	public function getOrden_Proyecto() { return $this->orden_proyecto; }
	public function setOrden_Proyecto($value) {
		if (is_null($value))
			throw new UnexpectedValueException(sprintf("Error en %s.setOrden_Proyecto(): no se permiten valores nulos para el atributo 'orden_proyecto'.", get_class($this)));
		if ( (!($this->esFloat($value) || $this->esInteger($value))) )
			throw new InvalidArgumentException(sprintf("Error en %s.setOrden_Proyecto(): el atributo 'orden_proyecto' solo permite valores de tipo float o double.", get_class($this)));
		$this->orden_proyecto = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getId_Codproyecto() { return $this->id_codproyecto; }
	public function setId_Codproyecto($value) {
		if (is_null($value))
			throw new UnexpectedValueException(sprintf("Error en %s.setId_Codproyecto(): no se permiten valores nulos para el atributo 'id_codproyecto'.", get_class($this)));
		if ( (!$this->esInteger($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setId_Codproyecto(): el atributo 'id_codproyecto' solo permite valores de tipo integer.", get_class($this)));
		$this->id_codproyecto = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getExtracto() { return $this->extracto; }
	public function setExtracto($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setExtracto(): el atributo 'extracto' solo permite valores de tipo string.", get_class($this)));
		$this->extracto = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getObservaciones_Proyecto() { return $this->observaciones_proyecto; }
	public function setObservaciones_Proyecto($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setObservaciones_Proyecto(): el atributo 'observaciones_proyecto' solo permite valores de tipo string.", get_class($this)));
		$this->observaciones_proyecto = $value;
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

	// Atributos de sólo lectura
	public function getDescripcion_Proyecto() { return $this->ro_descripcion_proyecto; }
	public function setDescripcion_Proyecto($value) { /**/ }

	public function getNumero_Promulga() { return $this->ro_numero_promulga; }
	public function setNumero_Promulga($value) { /**/ }

	public function getFecha_Promulga() { return $this->ro_fecha_promulga; }
	public function setFecha_Promulga($value) { /**/ }

	public function getDecreto_Promulga() { return $this->ro_decreto_promulga; }
	public function setDecreto_Promulga($value) { /**/ }

	public function getFecha_Veto() { return $this->ro_fecha_veto; }
	public function setFecha_Veto($value) { /**/ }

	// 05/05/2017: XXXX: los agregué para el listado de Expedientes para Expurgo
	public function getFecha_Sancion() { return $this->ro_fecha_sancion; }
	public function setFecha_Sancion($value) { /**/ }

	public function getNumero_Sancion() { return $this->ro_numero_sancion; }
	public function setNumero_Sancion($value) { /**/ }
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
	 * @param  float (PK) orden_proyecto
	 * @param  integer id_codproyecto
	 * @param  string extracto
	 * @param  string observaciones_proyecto
	 * @param  integer id_usuario
	 */
	public function __construct(
		$panio = 0,
		$ptipo = '',
		$pnumero = 0.0,
		$pcuerpo = 0,
		$palcance = 0,
		$porden_proyecto = 0.0,
		$pid_codproyecto = 0,
		$pextracto = null,
		$pobservaciones_proyecto = null,
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
		$this->orden_proyecto       = $porden_proyecto;
		$this->id_codproyecto       = $pid_codproyecto;
		$this->extracto             = $pextracto;
		$this->observaciones_proyecto = $pobservaciones_proyecto;
		$this->id_usuario           = $pid_usuario;

		// Atributos de sólo lectura
		$this->ro_descripcion_proyecto = null;
		$this->ro_numero_promulga = null;
		$this->ro_fecha_promulga = null;
		$this->ro_decreto_promulga = null;
		$this->ro_fecha_veto = null;
		// 05/05/2017: agregados por XXXX
		$this->ro_fecha_sancion = null;
		$this->ro_numero_sancion = null;

		// Invocación de inicialización de clase padre.
		parent::__construct();
	}
}
?>

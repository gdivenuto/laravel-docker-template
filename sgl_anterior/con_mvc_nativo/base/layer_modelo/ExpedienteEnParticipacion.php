<?php
/**
 * Clase ExpedienteEnParticipacion
 *
 * Descripción: expe_en_participacion
 * Layer Datos: DBExpedientes
 * Layer Negocio: NGExpedientes
 *
 * Clase agregada el 2021-02-19 por XXXX
 */
class ExpedienteEnParticipacion extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $anio; 			// (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $tipo; 			// (Primary Key) PHP: string     MySQL: char(1)
	protected $numero; 			// (Primary Key) PHP: float      MySQL: decimal(10,0)
	protected $cuerpo; 			// (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $alcance; 		// (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $fecha_inicio;	// PHP: string		MySQL: date
	protected $fecha_fin;		// PHP:	string		MySQL: date
	protected $extracto;		// PHP:	string		MySQL: text [Permite NULL]
	protected $id_usuario; 		// PHP: integer     MySQL: int(10) unsigned

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

	public function getFecha_Inicio() { return $this->fecha_inicio; }
	public function setFecha_Inicio($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Inicio(): el atributo 'fecha_inicio' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); }
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Inicio(): el atributo 'fecha_inicio' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d).", get_class($this))); }
		$this->fecha_inicio = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Fin() { return $this->fecha_fin; }
	public function setFecha_Fin($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Fin(): el atributo 'fecha_fin' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); }
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Fin(): el atributo 'fecha_fin' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d).", get_class($this))); }
		$this->fecha_fin = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getExtracto() { return $this->extracto; }
	public function setExtracto($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setExtracto(): el atributo 'extracto' solo permite valores de tipo string.", get_class($this)));
		$this->extracto = $value;
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
	 * @param  integer (PK) $panio
	 * @param  string (PK) $ptipo
	 * @param  float (PK) $pnumero
	 * @param  integer (PK) $pcuerpo
	 * @param  integer (PK) $palcance
	 * @param  string $pfecha_inicio
	 * @param  string $pfecha_fin
	 * @param  string $pextracto
	 * @param  integer $pid_usuario
	 */
	public function __construct(
		$panio = 0,
		$ptipo = '',
		$pnumero = 0.0,
		$pcuerpo = 0,
		$palcance = 0,
		$pfecha_inicio = null,
		$pfecha_fin = null,
		$pextracto = null,
		$pid_usuario = 0)
	{
		// Asignación de atributos.
		// Si las propiedades se acceden desde un ámbito con visibilidad directa, no
		// se disparan los métodos mágicos __get() y __set(). Por el contrario, si se
		// acceden 'desde fuera de la instancia', se disparan los métodos mágicos.
		$this->anio         = $panio;
		$this->tipo         = $ptipo;
		$this->numero       = $pnumero;
		$this->cuerpo       = $pcuerpo;
		$this->alcance      = $palcance;
		$this->fecha_inicio = $pfecha_inicio;
		$this->fecha_fin 	= $pfecha_fin;
		$this->extracto 	= $pextracto;
		$this->id_usuario   = $pid_usuario;

		// Invocación de inicialización de clase padre.
		parent::__construct();
	}
}
?>

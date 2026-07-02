<?php
/**
 * Clase Giro
 *
 * Descripción: expe_giros
 * Layer Datos: DBExpedientes
 * Layer Negocio: NGExpedientes
 *
 * GenerateClass 0.97.4 beta @ 2016-09-01 13:51:00
 */
class Giro extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $anio                ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $tipo                ; // (Primary Key) PHP: string     MySQL: char(1)
	protected $numero              ; // (Primary Key) PHP: float      MySQL: decimal(10,0)
	protected $cuerpo              ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $alcance             ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $orden_giro          ; // (Primary Key) PHP: float      MySQL: decimal(2,0)
	protected $comision_tipo       ; // PHP: string     MySQL: varchar(3) [Permite NULL]
	protected $comision_codigo     ; // PHP: string     MySQL: varchar(10) [Permite NULL]
	protected $fecha_entrada_giro  ; // PHP: string     MySQL: date [Permite NULL]
	protected $fecha_salida_giro   ; // PHP: string     MySQL: date [Permite NULL]
	protected $dictamen_giro       ; // PHP: string     MySQL: varchar(35) [Permite NULL]
	protected $observaciones_giro  ; // PHP: string     MySQL: text [Permite NULL]
	protected $id_usuario          ; // PHP: integer    MySQL: int(10) unsigned

	// Atributo de sólo lectura
	protected $ro_descripcion_grp  ;

	// Número de días que el expediente se encuentra en una Comisión
	// este atributo es seteado y utilizado en la Grilla de Giros
	protected $cantidad_dias_en_comision;

	// 11/02/2021 XXXX
	// Para saber si se puede reordenar con otros Giros
	protected $ro_puede_reordenarse_con_giro_anterior;
	protected $ro_puede_reordenarse_con_giro_siguiente;

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

	public function getOrden_Giro() { return $this->orden_giro; }
	public function setOrden_Giro($value) {
		if (is_null($value))
			throw new UnexpectedValueException(sprintf("Error en %s.setOrden_Giro(): no se permiten valores nulos para el atributo 'orden_giro'.", get_class($this)));
		if ( (!($this->esFloat($value) || $this->esInteger($value))) )
			throw new InvalidArgumentException(sprintf("Error en %s.setOrden_Giro(): el atributo 'orden_giro' solo permite valores de tipo float o double.", get_class($this)));
		$this->orden_giro = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getComision_Tipo() { return $this->comision_tipo; }
	public function setComision_Tipo($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setComision_Tipo(): el atributo 'comision_tipo' solo permite valores de tipo string.", get_class($this)));
		$this->comision_tipo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getComision_Codigo() { return $this->comision_codigo; }
	public function setComision_Codigo($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setComision_Codigo(): el atributo 'comision_codigo' solo permite valores de tipo string.", get_class($this)));
		$this->comision_codigo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Entrada_Giro() { return $this->fecha_entrada_giro; }
	public function setFecha_Entrada_Giro($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Entrada_Giro(): el atributo 'fecha_entrada_giro' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); }
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Entrada_Giro(): el atributo 'fecha_entrada_giro' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); }
		$this->fecha_entrada_giro = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Salida_Giro() { return $this->fecha_salida_giro; }
	public function setFecha_Salida_Giro($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Salida_Giro(): el atributo 'fecha_salida_giro' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); }
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Salida_Giro(): el atributo 'fecha_salida_giro' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); }
		$this->fecha_salida_giro = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getDictamen_Giro() { return $this->dictamen_giro; }
	public function setDictamen_Giro($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setDictamen_Giro(): el atributo 'dictamen_giro' solo permite valores de tipo string.", get_class($this)));
		$this->dictamen_giro = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getObservaciones_Giro() { return $this->observaciones_giro; }
	public function setObservaciones_Giro($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setObservaciones_Giro(): el atributo 'observaciones_giro' solo permite valores de tipo string.", get_class($this)));
		$this->observaciones_giro = $value;
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

	// Atributo de sólo lectura
	public function getDescripcion_Grp() { return $this->ro_descripcion_grp; }
	public function setDescripcion_Grp($value) { /* sólo lectura */ }

	// Atributo de sólo lectura
	public function getPuede_Reordenarse_Con_Giro_Anterior() { return $this->ro_puede_reordenarse_con_giro_anterior; }
	public function setPuede_Reordenarse_Con_Giro_Anterior($value) { /* sólo lectura */ }

	// Atributo de sólo lectura
	public function getPuede_Reordenarse_Con_Giro_Siguiente() { return $this->ro_puede_reordenarse_con_giro_siguiente; }
	public function setPuede_Reordenarse_Con_Giro_Siguiente($value) { /* sólo lectura */ }

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
	 * @param  float (PK) orden_giro
	 * @param  string comision_tipo
	 * @param  string comision_codigo
	 * @param  string fecha_entrada_giro
	 * @param  string fecha_salida_giro
	 * @param  string dictamen_giro
	 * @param  string observaciones_giro
	 * @param  integer id_usuario
	 */
	public function __construct(
		$panio = 0,
		$ptipo = '',
		$pnumero = 0.0,
		$pcuerpo = 0,
		$palcance = 0,
		$porden_giro = 0.0,
		$pcomision_tipo = null,
		$pcomision_codigo = null,
		$pfecha_entrada_giro = null,
		$pfecha_salida_giro = null,
		$pdictamen_giro = null,
		$pobservaciones_giro = null,
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
		$this->orden_giro           = $porden_giro;
		$this->comision_tipo        = $pcomision_tipo;
		$this->comision_codigo      = $pcomision_codigo;
		$this->fecha_entrada_giro   = $pfecha_entrada_giro;
		$this->fecha_salida_giro    = $pfecha_salida_giro;
		$this->dictamen_giro        = $pdictamen_giro;
		$this->observaciones_giro   = $pobservaciones_giro;
		$this->id_usuario           = $pid_usuario;

		// Atributo de sólo lectura
		$this->ro_descripcion_grp = null;
		$this->cantidad_dias_en_comision = -1;
		$this->ro_puede_reordenarse_con_giro_anterior = 0;
		$this->ro_puede_reordenarse_con_giro_siguiente = 0;

		// Invocación de inicialización de clase padre.
		parent::__construct();
	}
}
?>

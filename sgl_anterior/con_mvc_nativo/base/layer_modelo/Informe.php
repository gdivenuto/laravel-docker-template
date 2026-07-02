<?php
/**
 * Clase Informe
 * 
 * Descripción: expe_informes 
 * Layer Datos: DBExpedientes|DBReportes
 * Layer Negocio: NGExpedientes|NGReportes
 *
 * GenerateClass 0.97.4 beta @ 2016-09-01 13:51:00
 */
class Informe extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $anio                 ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $tipo                 ; // (Primary Key) PHP: string     MySQL: char(1)
	protected $numero               ; // (Primary Key) PHP: float      MySQL: decimal(10,0)
	protected $cuerpo               ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $alcance              ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $orden_giro           ; // (Primary Key) PHP: float      MySQL: decimal(2,0)
	protected $orden_informe        ; // (Primary Key) PHP: float      MySQL: decimal(2,0)
	protected $fecha_pedido_informe ; // PHP: string     MySQL: date [Permite NULL]
	protected $fecha_vuelta_informe ; // PHP: string     MySQL: date [Permite NULL]
	protected $detalle_informe      ; // PHP: string     MySQL: varchar(35) [Permite NULL]
	protected $observaciones_informe; // PHP: string     MySQL: text [Permite NULL]
	protected $id_usuario           ; // PHP: integer    MySQL: int(10) unsigned

	// Atributos de sólo lectura
	protected $ro_codigo_comision; // Asociado a codigo_grp de Lugares
	protected $ro_nombre_comision; // Asociado a descripcion_grp de Lugares
	protected $ro_fecha_comision; // Asociado a fecha_entrada_giro de Giros
	protected $ro_iniciador_descripcion_grp; // Asociado a iniciador_tipo, iniciador_codigo
	protected $ro_caratula; // Asociado a caratula de Expedientes
	protected $ro_fecha_entrada_expe; // Asociado a fecha_entrada_expe de Expedientes
	protected $ro_cantidad_dias_del_informe; // Número de días que el Informe se encuentra en una Comisión. Este atributo es calculado y utilizado en el Listado de Informes
	
	// Atributos de relación 1:N
	protected $proyectos; // Colección de proyectos asociada

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

	public function getOrden_Informe() { return $this->orden_informe; }
	public function setOrden_Informe($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setOrden_Informe(): no se permiten valores nulos para el atributo 'orden_informe'.", get_class($this)));
		if ( (!($this->esFloat($value) || $this->esInteger($value))) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setOrden_Informe(): el atributo 'orden_informe' solo permite valores de tipo float o double.", get_class($this)));
		$this->orden_informe = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Pedido_Informe() { return $this->fecha_pedido_informe; }
	public function setFecha_Pedido_Informe($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Pedido_Informe(): el atributo 'fecha_pedido_informe' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Pedido_Informe(): el atributo 'fecha_pedido_informe' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_pedido_informe = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Vuelta_Informe() { return $this->fecha_vuelta_informe; }
	public function setFecha_Vuelta_Informe($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Vuelta_Informe(): el atributo 'fecha_vuelta_informe' solo permite valores de tipo string.", get_class($this)));
		if (!is_null($value))
			try { $dummy = $this->verificarDateTimeDesdeString($value); } 
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Vuelta_Informe(): el atributo 'fecha_vuelta_informe' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); } 
		$this->fecha_vuelta_informe = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getDetalle_Informe() { return $this->detalle_informe; }
	public function setDetalle_Informe($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setDetalle_Informe(): el atributo 'detalle_informe' solo permite valores de tipo string.", get_class($this)));
		$this->detalle_informe = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getObservaciones_Informe() { return $this->observaciones_informe; }
	public function setObservaciones_Informe($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setObservaciones_Informe(): el atributo 'observaciones_informe' solo permite valores de tipo string.", get_class($this)));
		$this->observaciones_informe = $value;
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
	public function getCodigo_Comision() { return $this->ro_codigo_comision; }
	public function setCodigo_Comision($value) { /* atributo de sólo lectura */ }

	public function getNombre_Comision() { return $this->ro_nombre_comision; }
	public function setNombre_Comision($value) { /* atributo de sólo lectura */ }

	public function getFecha_Comision() { return $this->ro_fecha_comision; }
	public function setFecha_Comision($value) { /* atributo de sólo lectura */ }

	public function getIniciador_Descripcion_Grp() { return $this->ro_iniciador_descripcion_grp; }
	public function setIniciador_Descripcion_Grp($value) { /* atributo de sólo lectura */ }

	public function getCaratula() { return $this->ro_caratula; }
	public function setCaratula($value) { /* atributo de sólo lectura */ }

	public function getFecha_Entrada_Expe() { return $this->ro_fecha_entrada_expe; }
	public function setFecha_Entrada_Expe($value) { /* atributo de sólo lectura */ }

	public function getCantidad_Dias_Del_Informe() { return $this->ro_cantidad_dias_del_informe; }
	public function setCantidad_Dias_Del_Informe($value) { /* atributo de sólo lectura */ }

	// Atributos de relación 1:N
	public function getProyectos() { return $this->proyectos; }
	public function setProyectos($value) { /* No puedo modificar la instancia, solo accederla //$this->proyectos = $value; */ }

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
	 * @param  float (PK) orden_informe
	 * @param  string fecha_pedido_informe
	 * @param  string fecha_vuelta_informe
	 * @param  string detalle_informe
	 * @param  string observaciones_informe
	 * @param  integer id_usuario
	 */
	public function __construct(
		$panio = 0,
		$ptipo = '',
		$pnumero = 0.0,
		$pcuerpo = 0,
		$palcance = 0,
		$porden_giro = 0.0,
		$porden_informe = 0.0,
		$pfecha_pedido_informe = null,
		$pfecha_vuelta_informe = null,
		$pdetalle_informe = null,
		$pobservaciones_informe = null,
		$pid_usuario = 0)
	{
		// Asignación de atributos.
		// Si las propiedades se acceden desde un ámbito con visibilidad directa, no 
		// se disparan los métodos mágicos __get() y __set(). Por el contrario, si se 
		// acceden 'desde fuera de la instancia', se disparan los métodos mágicos.
		$this->anio                  = $panio;
		$this->tipo                  = $ptipo;
		$this->numero                = $pnumero;
		$this->cuerpo                = $pcuerpo;
		$this->alcance               = $palcance;
		$this->orden_giro            = $porden_giro;
		$this->orden_informe         = $porden_informe;
		$this->fecha_pedido_informe  = $pfecha_pedido_informe;
		$this->fecha_vuelta_informe  = $pfecha_vuelta_informe;
		$this->detalle_informe       = $pdetalle_informe;
		$this->observaciones_informe = $pobservaciones_informe;
		$this->id_usuario            = $pid_usuario;

		// Atributos de sólo lectura
		$this->ro_codigo_comision = null;
		$this->ro_nombre_comision = null;
		$this->ro_fecha_comision = null;
		$this->ro_iniciador_descripcion_grp = null;
		$this->ro_caratula = null;
		$this->ro_fecha_entrada_expe = null;
		$this->ro_cantidad_dias_del_informe = -1;

		// Atributos de relacion 1:N
		$this->proyectos = new ColeccionClaseBase(); 
		 
		// Invocación de inicialización de clase padre.
		parent::__construct();
	}

	/**
	 * Reasigna el identificador de expediente a todas las clases contenidas en las colecciones de Informe.
	 * Esto se hace porque al crear un nuevo expediente, las colecciones asociadas pueden NO TENER seteadas 
	 * las claves primarias (referencias al expediente padre).
	 */
	public function reasignarIdEnColecciones() {
		// Actualizo los proyectos
		foreach ($this->getProyectos() as $item) {
			$item->anio = $this->anio;
			$item->tipo = $this->tipo;
			$item->numero = $this->numero;
			$item->cuerpo = $this->cuerpo;
			$item->alcance = $this->alcance;
		}
	}
}
<?php
/**
 * Clase Expediente
 *
 * Descripción: expe_expedientes
 * Layer Datos: DBExpedientes
 * Layer Negocio: NGExpedientes
 *
 * GenerateClass 0.97.4 beta @ 2016-09-01 13:16:11
 */
class Expediente extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $anio                ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $tipo                ; // (Primary Key) PHP: string     MySQL: char(1)
	protected $numero              ; // (Primary Key) PHP: float      MySQL: decimal(10,0)
	protected $cuerpo              ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $alcance             ; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $iniciador_tipo      ; // PHP: string     MySQL: varchar(3)
	protected $iniciador_codigo    ; // PHP: string     MySQL: varchar(10)
	protected $iniciador_bloque_tipo; // PHP: string     MySQL: varchar(3) [Permite NULL]
	protected $iniciador_bloque_codigo; // PHP: string     MySQL: varchar(10) [Permite NULL]
	protected $agregado_anio       ; // PHP: integer    MySQL: smallint(6) [Permite NULL]
	protected $agregado_tipo       ; // PHP: string     MySQL: char(1) [Permite NULL]
	protected $agregado_numero     ; // PHP: float      MySQL: decimal(10,0) [Permite NULL]
	protected $agregado_cuerpo     ; // PHP: integer    MySQL: smallint(6) [Permite NULL]
	protected $agregado_alcance    ; // PHP: integer    MySQL: smallint(6) [Permite NULL]
	protected $id_codcategoria     ; // PHP: integer    MySQL: int(10) unsigned
	protected $fecha_entrada_expe  ; // PHP: string     MySQL: date
	protected $caratula            ; // PHP: string     MySQL: varchar(60) [Permite NULL]
	protected $observaciones_expe  ; // PHP: string     MySQL: text [Permite NULL]
	protected $marca_comision      ; // PHP: integer    MySQL: smallint(6) [Permite NULL]
	// Agregado el 02/06/2020 por XXXX
	protected $digi_completa       ; // PHP: string     MySQL: char(1)

	protected $id_usuario          ; // PHP: integer    MySQL: int(10) unsigned

	// Atributos extra (no hay relación con capa de datos)
	protected $estado_proyecto;
	protected $url_proyecto; // Agregado el 11/03/2019 por XXXX
	protected $url_proyecto_temporal; // Agregado el 14/07/2020 por XXXX
	protected $estado_digitalizacion; // Agregado el 27/02/2019 por XXXX
	protected $url_digitalizacion; // Agregado el 08/07/2020 por XXXX

	// Atributos de solo lectura
	protected $ro_descripcion_categoria; // Asociado a id_codcategoria
	protected $ro_codigo_usuario; // Asociado a id_usuario
	protected $ro_nombre_usuario; // Asociado a id_usuario
	protected $ro_iniciador_descripcion_grp; // Asociado a iniciador_tipo, iniciador_codigo
	protected $ro_iniciador_bloque_descripcion_grp; // Asociado a iniciador_bloque_tipo, iniciador_bloque_codigo

	// Número de días que el expediente se encuentra en una Comisión
	// este atributo es seteado y utilizado en el Listado de Expedientes en Comisión y Detalle de Giros
	protected $ro_cantidad_dias_en_comision;

	// Atributos de relacion 1:N
	protected $antecedentes; // Coleccion de antecedentes asociada
	protected $autores; // Coleccion de autores asociada
	protected $estados; // Coleccion de estados asociada
	protected $giros; // Coleccion de giros asociada
	protected $proyectos; // Coleccion de proyectos asociada
	protected $sanciones; // Coleccion de sanciones asociada
	protected $temas; // Coleccion de temas asociada
	protected $informes; // Coleccion de informes asociados

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
		if ( (!($this->esFloat($value) || $this->esInteger($value)))  )
			throw new InvalidArgumentException(sprintf("Error en %s.setNumero(): el atributo 'numero' solo permite valores de tipo integer, float o double.", get_class($this)));
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

	public function getIniciador_Tipo() { return $this->iniciador_tipo; }
	public function setIniciador_Tipo($value) {
		if (is_null($value))
			throw new UnexpectedValueException(sprintf("Error en %s.setIniciador_Tipo(): no se permiten valores nulos para el atributo 'iniciador_tipo'.", get_class($this)));
		if ( (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setIniciador_Tipo(): el atributo 'iniciador_tipo' solo permite valores de tipo string.", get_class($this)));
		$this->iniciador_tipo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getIniciador_Codigo() { return $this->iniciador_codigo; }
	public function setIniciador_Codigo($value) {
		if (is_null($value))
			throw new UnexpectedValueException(sprintf("Error en %s.setIniciador_Codigo(): no se permiten valores nulos para el atributo 'iniciador_codigo'.", get_class($this)));
		if ( (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setIniciador_Codigo(): el atributo 'iniciador_codigo' solo permite valores de tipo string.", get_class($this)));
		$this->iniciador_codigo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getIniciador_Bloque_Tipo() { return $this->iniciador_bloque_tipo; }
	public function setIniciador_Bloque_Tipo($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setIniciador_Bloque_Tipo(): el atributo 'iniciador_bloque_tipo' solo permite valores de tipo string.", get_class($this)));
		$this->iniciador_bloque_tipo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getIniciador_Bloque_Codigo() { return $this->iniciador_bloque_codigo; }
	public function setIniciador_Bloque_Codigo($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setIniciador_Bloque_Codigo(): el atributo 'iniciador_bloque_codigo' solo permite valores de tipo string.", get_class($this)));
		$this->iniciador_bloque_codigo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAgregado_Anio() { return $this->agregado_anio; }
	public function setAgregado_Anio($value) {
		if ( (!is_null($value)) && (!$this->esInteger($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setAgregado_Anio(): el atributo 'agregado_anio' solo permite valores de tipo integer.", get_class($this)));
		$this->agregado_anio = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAgregado_Tipo() { return $this->agregado_tipo; }
	public function setAgregado_Tipo($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setAgregado_Tipo(): el atributo 'agregado_tipo' solo permite valores de tipo string.", get_class($this)));
		$this->agregado_tipo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAgregado_Numero() { return $this->agregado_numero; }
	public function setAgregado_Numero($value) {
		if ( (!is_null($value)) && (!$this->esFloat($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setAgregado_Numero(): el atributo 'agregado_numero' solo permite valores de tipo float o double.", get_class($this)));
		$this->agregado_numero = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAgregado_Cuerpo() { return $this->agregado_cuerpo; }
	public function setAgregado_Cuerpo($value) {
		if ( (!is_null($value)) && (!$this->esInteger($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setAgregado_Cuerpo(): el atributo 'agregado_cuerpo' solo permite valores de tipo integer.", get_class($this)));
		$this->agregado_cuerpo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAgregado_Alcance() { return $this->agregado_alcance; }
	public function setAgregado_Alcance($value) {
		if ( (!is_null($value)) && (!$this->esInteger($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setAgregado_Alcance(): el atributo 'agregado_alcance' solo permite valores de tipo integer.", get_class($this)));
		$this->agregado_alcance = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getId_Codcategoria() { return $this->id_codcategoria; }
	public function setId_Codcategoria($value) {
		if (is_null($value))
			throw new UnexpectedValueException(sprintf("Error en %s.setId_Codcategoria(): no se permiten valores nulos para el atributo 'id_codcategoria'.", get_class($this)));
		if ( (!$this->esInteger($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setId_Codcategoria(): el atributo 'id_codcategoria' solo permite valores de tipo integer.", get_class($this)));
		$this->id_codcategoria = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha_Entrada_Expe() { return $this->fecha_entrada_expe; }
	public function setFecha_Entrada_Expe($value) {
		if (is_null($value))
			throw new UnexpectedValueException(sprintf("Error en %s.setFecha_Entrada_Expe(): no se permiten valores nulos para el atributo 'fecha_entrada_expe'.", get_class($this)));
		if ( (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Entrada_Expe(): el atributo 'fecha_entrada_expe' solo permite valores de tipo string.", get_class($this)));
		try { $dummy = $this->verificarDateTimeDesdeString($value); }
			catch (Exception $e) { throw new InvalidArgumentException(sprintf("Error en %s.setFecha_Entrada_Expe(): el atributo 'fecha_entrada_expe' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d H:i:s).", get_class($this))); }
		$this->fecha_entrada_expe = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getCaratula() { return $this->caratula; }
	public function setCaratula($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setCaratula(): el atributo 'caratula' solo permite valores de tipo string.", get_class($this)));
		$this->caratula = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getObservaciones_Expe() { return $this->observaciones_expe; }
	public function setObservaciones_Expe($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setObservaciones_Expe(): el atributo 'observaciones_expe' solo permite valores de tipo string.", get_class($this)));
		$this->observaciones_expe = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getMarca_Comision() { return $this->marca_comision; }
	public function setMarca_Comision($value) {
		if ( (!is_null($value)) && (!$this->esInteger($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setMarca_Comision(): el atributo 'marca_comision' solo permite valores de tipo integer.", get_class($this)));
		$this->marca_comision = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getDigi_Completa() { return $this->digi_completa; }
	public function setDigi_Completa($value) {
		if ( (!is_null($value)) && (!is_string($value)) )
			throw new InvalidArgumentException(sprintf("Error en %s.setDigi_Completa(): el atributo 'digi_completa' solo permite valores de tipo string.", get_class($this)));
		$this->digi_completa = $value;
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
	public function getEstado_Proyecto() { return $this->estado_proyecto; }
	public function setEstado_Proyecto($value) { $this->estado_proyecto = $value; }

	// Agregado el 11/03/2019 por XXXX
	public function getUrl_Proyecto() { return $this->url_proyecto; }
	public function setUrl_Proyecto($value) { $this->url_proyecto = $value; }

	// Agregado el 14/07/2020 por XXXX
	public function getUrl_ProyectoTemporal() { return $this->url_proyecto_temporal; }
	public function setUrl_ProyectoTemporal($value) { $this->url_proyecto_temporal = $value; }

	// Agregado el 27/02/2019 por XXXX
	public function getEstado_Digitalizacion() { return $this->estado_digitalizacion; }
	public function setEstado_Digitalizacion($value) { $this->estado_digitalizacion = $value; }

	// Agregado el 08/07/2020 por XXXX
	public function getUrl_Digitalizacion() { return $this->url_digitalizacion; }
	public function setUrl_Digitalizacion($value) { $this->url_digitalizacion = $value; }

	// Atributos de solo lectura
	public function getDescripcion_Categoria() { return $this->ro_descripcion_categoria; }
	public function setDescripcion_Categoria($value) { /* atributo de solo lectura */ }

	public function getCodigo_Usuario() { return $this->ro_codigo_usuario; }
	public function setCodigo_Usuario($value) { /* atributo de solo lectura */ }

	public function getNombre_Usuario() { return $this->ro_nombre_usuario; }
	public function setNombre_Usuario($value) { /* atributo de solo lectura */ }

	public function getIniciador_Descripcion_Grp() { return $this->ro_iniciador_descripcion_grp; }
	public function setIniciador_Descripcion_Grp($value) { /* atributo de solo lectura */ }

	public function getIniciador_Bloque_Descripcion_Grp() { return $this->ro_iniciador_bloque_descripcion_grp; }
	public function setIniciador_Bloque_Descripcion_Grp($value) { /* atributo de solo lectura */ }

	public function getCantidad_Dias_En_Comision() { return $this->ro_cantidad_dias_en_comision; }
	public function setCantidad_Dias_En_Comision($value) { /* atributo de sólo lectura */ }

	// Atributos de relacion 1:N
	public function getAntecedentes() { return $this->antecedentes; }
	public function setAntecedentes($value) { /* No puedo modificar la instancia, solo accederla //$this->antecedentes = $value; */ }

	public function getAutores() { return $this->autores; }
	public function setAutores($value) { /* No puedo modificar la instancia, solo accederla //$this->autores = $value; */ }

	public function getEstados() { return $this->estados; }
	public function setEstados($value) { /* No puedo modificar la instancia, solo accederla //$this->estados = $value; */ }

	public function getGiros() { return $this->giros; }
	public function setGiros($value) { /* No puedo modificar la instancia, solo accederla //$this->giros = $value; */ }

	public function getProyectos() { return $this->proyectos; }
	public function setProyectos($value) { /* No puedo modificar la instancia, solo accederla //$this->proyectos = $value; */ }

	public function getSanciones() { return $this->sanciones; }
	public function setSanciones($value) { /* No puedo modificar la instancia, solo accederla //$this->sanciones = $value; */ }

	public function getTemas() { return $this->temas; }
	public function setTemas($value) { /* No puedo modificar la instancia, solo accederla //$this->temas = $value; */ }

	public function getInformes() { return $this->informes; }
	public function setInformes($value) { /* No puedo modificar la instancia, solo accederla //$this->informes = $value; */ }

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
	 * @param  string iniciador_tipo
	 * @param  string iniciador_codigo
	 * @param  string iniciador_bloque_tipo
	 * @param  string iniciador_bloque_codigo
	 * @param  integer agregado_anio
	 * @param  string agregado_tipo
	 * @param  float agregado_numero
	 * @param  integer agregado_cuerpo
	 * @param  integer agregado_alcance
	 * @param  integer id_codcategoria
	 * @param  string fecha_entrada_expe
	 * @param  string caratula
	 * @param  string observaciones_expe
	 * @param  integer marca_comision
	 * @param  string digi_completa
	 * @param  integer id_usuario
	 */
	public function __construct(
		$panio = 0,
		$ptipo = '',
		$pnumero = 0.0,
		$pcuerpo = 0,
		$palcance = 0,
		$piniciador_tipo = '',
		$piniciador_codigo = '',
		$piniciador_bloque_tipo = null,
		$piniciador_bloque_codigo = null,
		$pagregado_anio = null,
		$pagregado_tipo = null,
		$pagregado_numero = null,
		$pagregado_cuerpo = null,
		$pagregado_alcance = null,
		$pid_codcategoria = 0,
		$pfecha_entrada_expe = '',
		$pcaratula = null,
		$pobservaciones_expe = null,
		$pmarca_comision = null,
		$pdigi_completa = null,
		$pid_usuario = 0)
	{
		// Asignación de atributos.
		// Si las propiedades se acceden desde un ámbito con visibilidad directa, no
		// se disparan los métodos mágicos __get() y __set(). Por el contrario, si se
		// acceden 'desde fuera de la instancia', se disparan los métodos mágicos.
		$this->anio                    = $panio;
		$this->tipo                    = $ptipo;
		$this->numero                  = $pnumero;
		$this->cuerpo                  = $pcuerpo;
		$this->alcance                 = $palcance;
		$this->iniciador_tipo          = $piniciador_tipo;
		$this->iniciador_codigo        = $piniciador_codigo;
		$this->iniciador_bloque_tipo   = $piniciador_bloque_tipo;
		$this->iniciador_bloque_codigo = $piniciador_bloque_codigo;
		$this->agregado_anio           = $pagregado_anio;
		$this->agregado_tipo           = $pagregado_tipo;
		$this->agregado_numero         = $pagregado_numero;
		$this->agregado_cuerpo         = $pagregado_cuerpo;
		$this->agregado_alcance        = $pagregado_alcance;
		$this->id_codcategoria         = $pid_codcategoria;
		$this->fecha_entrada_expe      = $pfecha_entrada_expe;
		$this->caratula                = $pcaratula;
		$this->observaciones_expe      = $pobservaciones_expe;
		$this->marca_comision          = $pmarca_comision;
		$this->digi_completa           = $pdigi_completa;
		$this->id_usuario              = $pid_usuario;

		// Atributos extra
		$this->estado_proyecto       = null;
		$this->url_proyecto          = null;
		$this->url_proyecto_temporal = null;
		$this->estado_digitalizacion = null;
		$this->url_digitalizacion    = null;

		// Atributos de solo lectura
		$this->ro_descripcion_categoria            = null;
		$this->ro_codigo_usuario                   = null;
		$this->ro_nombre_usuario                   = null;
		$this->ro_iniciador_descripcion_grp        = null;
		$this->ro_iniciador_bloque_descripcion_grp = null;
		$this->ro_cantidad_dias_en_comision        = -1;

		// Atributos de relacion 1:N
		$this->antecedentes = new ColeccionClaseBase();
		$this->autores      = new ColeccionClaseBase();
		$this->estados      = new ColeccionClaseBase();
		$this->giros        = new ColeccionClaseBase();
		$this->proyectos    = new ColeccionClaseBase();
		$this->sanciones    = new ColeccionClaseBase();
		$this->temas        = new ColeccionClaseBase();
		$this->informes     = new ColeccionClaseBase();

		// Invocación de inicialización de clase padre.
		parent::__construct();
	}

	/**
	 * Reasigna el identificador de expediente a todas las clases contenidas en las colecciones de Expediente.
	 * Esto se hace porque al crear un nuevo expediente, las colecciones asociadas pueden NO TENER seteadas
	 * las claves primarias (referencias al expediente padre).
	 */
	public function reasignarIdEnColecciones() {
		// Actualizo los antecedentes
		foreach ($this->getAntecedentes() as $item)
			if ($item->anio != $this->anio || $item->tipo != $this->tipo || $item->numero != $this->numero || $item->cuerpo != $this->cuerpo || $item->alcance != $this->alcance) {
				$item->anio = $this->anio;
				$item->tipo = $this->tipo;
				$item->numero = $this->numero;
				$item->cuerpo = $this->cuerpo;
				$item->alcance = $this->alcance;
			}

		// Actualizo los autores
		foreach ($this->getAutores() as $item)
			if ($item->anio != $this->anio || $item->tipo != $this->tipo || $item->numero != $this->numero || $item->cuerpo != $this->cuerpo || $item->alcance != $this->alcance) {
				$item->anio = $this->anio;
				$item->tipo = $this->tipo;
				$item->numero = $this->numero;
				$item->cuerpo = $this->cuerpo;
				$item->alcance = $this->alcance;
			}

		// Actualizo los estados
		foreach ($this->getEstados() as $item)
			if ($item->anio != $this->anio || $item->tipo != $this->tipo || $item->numero != $this->numero || $item->cuerpo != $this->cuerpo || $item->alcance != $this->alcance) {
				$item->anio = $this->anio;
				$item->tipo = $this->tipo;
				$item->numero = $this->numero;
				$item->cuerpo = $this->cuerpo;
				$item->alcance = $this->alcance;
			}

		// Actualizo los giros
		foreach ($this->getGiros() as $item)
			if ($item->anio != $this->anio || $item->tipo != $this->tipo || $item->numero != $this->numero || $item->cuerpo != $this->cuerpo || $item->alcance != $this->alcance) {
				$item->anio = $this->anio;
				$item->tipo = $this->tipo;
				$item->numero = $this->numero;
				$item->cuerpo = $this->cuerpo;
				$item->alcance = $this->alcance;
			}

		// Actualizo los proyectos
		foreach ($this->getProyectos() as $item)
			if ($item->anio != $this->anio || $item->tipo != $this->tipo || $item->numero != $this->numero || $item->cuerpo != $this->cuerpo || $item->alcance != $this->alcance) {
				$item->anio = $this->anio;
				$item->tipo = $this->tipo;
				$item->numero = $this->numero;
				$item->cuerpo = $this->cuerpo;
				$item->alcance = $this->alcance;
			}

		// Actualizo los sanciones
		foreach ($this->getSanciones() as $item)
			if ($item->anio != $this->anio || $item->tipo != $this->tipo || $item->numero != $this->numero || $item->cuerpo != $this->cuerpo || $item->alcance != $this->alcance) {
				$item->anio = $this->anio;
				$item->tipo = $this->tipo;
				$item->numero = $this->numero;
				$item->cuerpo = $this->cuerpo;
				$item->alcance = $this->alcance;
			}

		// Actualizo los temas
		foreach ($this->getTemas() as $item)
			if ($item->anio != $this->anio || $item->tipo != $this->tipo || $item->numero != $this->numero || $item->cuerpo != $this->cuerpo || $item->alcance != $this->alcance) {
				$item->anio = $this->anio;
				$item->tipo = $this->tipo;
				$item->numero = $this->numero;
				$item->cuerpo = $this->cuerpo;
				$item->alcance = $this->alcance;
			}

		// Actualizo los informes
		foreach ($this->getInformes() as $item)
			if ($item->anio != $this->anio || $item->tipo != $this->tipo || $item->numero != $this->numero || $item->cuerpo != $this->cuerpo || $item->alcance != $this->alcance) {
				$item->anio = $this->anio;
				$item->tipo = $this->tipo;
				$item->numero = $this->numero;
				$item->cuerpo = $this->cuerpo;
				$item->alcance = $this->alcance;
			}
	}
}
?>

<?php
/**
 * Clase Participacion
 *
 * Descripción: expe_participaciones
 * Layer Datos: DBExpedientes
 * Layer Negocio: NGExpedientes
 *
 * Clase agregada el 2021-02-19 por XXXX
 */
class Participacion extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $anio; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $tipo; // (Primary Key) PHP: string     MySQL: char(1)
	protected $numero; // (Primary Key) PHP: float      MySQL: decimal(10,0)
	protected $cuerpo; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $alcance; // (Primary Key) PHP: integer    MySQL: smallint(6)
	protected $numero_participacion; // (Primary Key) PHP: integer    MySQL: decimal(2,0)

	protected $fecha; // PHP: string		MySQL: date
	protected $apellidoynombre; // PHP:	string		MySQL: varchar(200)
	protected $tipodoc; // PHP:	string		MySQL: varchar(3)
	protected $nrodoc; // PHP:	integer		MySQL: int(11)
	protected $domicilio; // PHP:	string		MySQL: varchar(200)
	protected $localidad; // PHP:	string		MySQL: varchar(200)
	protected $telefono; // PHP:	string		MySQL: varchar(50)
	protected $mail; // PHP:	string		MySQL: varchar(100)
	protected $institucion_nombre; // PHP:	string		MySQL: varchar(200)
	protected $institucion_domicilio; // PHP:	string		MySQL: varchar(200)
	protected $texto; // PHP: string      MySQL: text [Permite NULL]
	protected $documentacion; // PHP: string      MySQL: longblob [Permite NULL]

	// ************************************************************************
	// Getters & Setters ******************************************************
	// ************************************************************************
	public function getAnio() {return $this->anio;}
	public function setAnio($value) {
		if (is_null($value)) {
			throw new UnexpectedValueException(sprintf("Error en %s.setAnio(): no se permiten valores nulos para el atributo 'anio'.", get_class($this)));
		}

		if ((!$this->esInteger($value))) {
			throw new InvalidArgumentException(sprintf("Error en %s.setAnio(): el atributo 'anio' solo permite valores de tipo integer.", get_class($this)));
		}

		$this->anio = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getTipo() {return $this->tipo;}
	public function setTipo($value) {
		if (is_null($value)) {
			throw new UnexpectedValueException(sprintf("Error en %s.setTipo(): no se permiten valores nulos para el atributo 'tipo'.", get_class($this)));
		}

		if ((!is_string($value))) {
			throw new InvalidArgumentException(sprintf("Error en %s.setTipo(): el atributo 'tipo' solo permite valores de tipo string.", get_class($this)));
		}

		$this->tipo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getNumero() {return $this->numero;}
	public function setNumero($value) {
		if (is_null($value)) {
			throw new UnexpectedValueException(sprintf("Error en %s.setNumero(): no se permiten valores nulos para el atributo 'numero'.", get_class($this)));
		}

		if ((!($this->esFloat($value) || $this->esInteger($value)))) {
			throw new InvalidArgumentException(sprintf("Error en %s.setNumero(): el atributo 'numero' solo permite valores de tipo float o double.", get_class($this)));
		}

		$this->numero = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getCuerpo() {return $this->cuerpo;}
	public function setCuerpo($value) {
		if (is_null($value)) {
			throw new UnexpectedValueException(sprintf("Error en %s.setCuerpo(): no se permiten valores nulos para el atributo 'cuerpo'.", get_class($this)));
		}

		if ((!$this->esInteger($value))) {
			throw new InvalidArgumentException(sprintf("Error en %s.setCuerpo(): el atributo 'cuerpo' solo permite valores de tipo integer.", get_class($this)));
		}

		$this->cuerpo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getAlcance() {return $this->alcance;}
	public function setAlcance($value) {
		if (is_null($value)) {
			throw new UnexpectedValueException(sprintf("Error en %s.setAlcance(): no se permiten valores nulos para el atributo 'alcance'.", get_class($this)));
		}

		if ((!$this->esInteger($value))) {
			throw new InvalidArgumentException(sprintf("Error en %s.setAlcance(): el atributo 'alcance' solo permite valores de tipo integer.", get_class($this)));
		}

		$this->alcance = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getNumero_Participacion() {return $this->numero_participacion;}
	public function setNumero_Participacion($value) {
		if (is_null($value)) {
			throw new UnexpectedValueException(sprintf("Error en %s.setNumero_Participacion(): no se permiten valores nulos para el atributo 'numero_participacion'.", get_class($this)));
		}

		if ((!($this->esFloat($value) || $this->esInteger($value)))) {
			throw new InvalidArgumentException(sprintf("Error en %s.setNumero_Participacion(): el atributo 'numero_participacion' solo permite valores de tipo float o double.", get_class($this)));
		}

		$this->numero_participacion = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getFecha() {return $this->fecha;}
	public function setFecha($value) {
		if ((!is_null($value)) && (!is_string($value))) {
			throw new InvalidArgumentException(sprintf("Error en %s.setFecha(): el atributo 'fecha' solo permite valores de tipo string.", get_class($this)));
		}

		if (!is_null($value)) {
			try { $dummy = $this->verificarDateTimeDesdeString($value);} catch (Exception $e) {throw new InvalidArgumentException(sprintf("Error en %s.setFecha(): el atributo 'fecha' solo permite valores de tipo string que respeten un formato de fecha válida (Y-m-d).", get_class($this)));}
		}

		$this->fecha = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getApellidoynombre() {return $this->apellidoynombre;}
	public function setApellidoynombre($value) {
		if ((!is_null($value)) && (!is_string($value))) {
			throw new InvalidArgumentException(sprintf("Error en %s.setApellidoynombre(): el atributo 'apellidoynombre' solo permite valores de tipo string.", get_class($this)));
		}

		$this->apellidoynombre = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getTipodoc() {return $this->tipodoc;}
	public function setTipodoc($value) {
		if ((!is_null($value)) && (!is_string($value))) {
			throw new InvalidArgumentException(sprintf("Error en %s.setTipodoc(): el atributo 'tipodoc' solo permite valores de tipo string.", get_class($this)));
		}

		$this->tipodoc = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getNrodoc() {return $this->nrodoc;}
	public function setNrodoc($value) {
		if (is_null($value)) {
			throw new UnexpectedValueException(sprintf("Error en %s.setNrodoc(): no se permiten valores nulos para el atributo 'nrodoc'.", get_class($this)));
		}

		if ((!$this->esInteger($value))) {
			throw new InvalidArgumentException(sprintf("Error en %s.setNrodoc(): el atributo 'nrodoc' solo permite valores de tipo integer.", get_class($this)));
		}

		$this->nrodoc = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getDomicilio() {return $this->domicilio;}
	public function setDomicilio($value) {
		if ((!is_null($value)) && (!is_string($value))) {
			throw new InvalidArgumentException(sprintf("Error en %s.setDomicilio(): el atributo 'domicilio' solo permite valores de tipo string.", get_class($this)));
		}

		$this->domicilio = $value;
		$this->setInstanceState(IS_MODIFIED);
	}
	public function getLocalidad() {return $this->localidad;}
	public function setLocalidad($value) {
		if ((!is_null($value)) && (!is_string($value))) {
			throw new InvalidArgumentException(sprintf("Error en %s.setLocalidad(): el atributo 'localidad' solo permite valores de tipo string.", get_class($this)));
		}

		$this->localidad = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getTelefono() {return $this->telefono;}
	public function setTelefono($value) {
		if ((!is_null($value)) && (!is_string($value))) {
			throw new InvalidArgumentException(sprintf("Error en %s.setTelefono(): el atributo 'telefono' solo permite valores de tipo string.", get_class($this)));
		}

		$this->telefono = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getMail() {return $this->mail;}
	public function setMail($value) {
		if ((!is_null($value)) && (!is_string($value))) {
			throw new InvalidArgumentException(sprintf("Error en %s.setMail(): el atributo 'mail' solo permite valores de tipo string.", get_class($this)));
		}

		$this->mail = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getInstitucion_Nombre() {return $this->institucion_nombre;}
	public function setInstitucion_Nombre($value) {
		if ((!is_null($value)) && (!is_string($value))) {
			throw new InvalidArgumentException(sprintf("Error en %s.setInstitucion_Nombre(): el atributo 'institucion_nombre' solo permite valores de tipo string.", get_class($this)));
		}

		$this->institucion_nombre = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getInstitucion_Domicilio() {return $this->institucion_domicilio;}
	public function setInstitucion_Domicilio($value) {
		if ((!is_null($value)) && (!is_string($value))) {
			throw new InvalidArgumentException(sprintf("Error en %s.setInstitucion_Domicilio(): el atributo 'institucion_domicilio' solo permite valores de tipo string.", get_class($this)));
		}

		$this->institucion_domicilio = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getTexto() {return $this->texto;}
	public function setTexto($value) {
		if ((!is_null($value)) && (!is_string($value))) {
			throw new InvalidArgumentException(sprintf("Error en %s.setTexto(): el atributo 'texto' solo permite valores de tipo string.", get_class($this)));
		}

		$this->texto = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getDocumentacion() {return $this->documentacion;}
	public function setDocumentacion($value) {
		if ((!is_null($value)) && (!is_string($value))) {
			throw new InvalidArgumentException(sprintf("Error en %s.setDocumentacion(): el atributo 'documentacion' solo permite valores de tipo string.", get_class($this)));
		}

		$this->documentacion = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

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
	 * @param  integer (PK) numero_participacion
	 * @param  string $pfecha
	 * @param  string $papellidoynombre
	 * @param  string $ptipodoc
	 * @param  integer $pnrodoc
	 * @param  string $pdomicilio
	 * @param  string $plocalidad
	 * @param  string $ptelefono
	 * @param  string $pmail
	 * @param  string $pinstitucion_nombre
	 * @param  string $pinstitucion_domicilio
	 * @param  string texto
	 * @param  string documentacion
	 */
	public function __construct(
		$panio = 0,
		$ptipo = '',
		$pnumero = 0.0,
		$pcuerpo = 0,
		$palcance = 0,
		$pnumero_participacion = 0.0,
		$pfecha = null,
		$papellidoynombre = null,
		$ptipodoc = null,
		$pnrodoc = null,
		$pdomicilio = null,
		$plocalidad = null,
		$ptelefono = null,
		$pmail = null,
		$pinstitucion_nombre = null,
		$pinstitucion_domicilio = null,
		$ptexto = null,
		$pdocumentacion = null) {
		// Asignación de atributos.
		// Si las propiedades se acceden desde un ámbito con visibilidad directa, no
		// se disparan los métodos mágicos __get() y __set(). Por el contrario, si se
		// acceden 'desde fuera de la instancia', se disparan los métodos mágicos.
		$this->anio = $panio;
		$this->tipo = $ptipo;
		$this->numero = $pnumero;
		$this->cuerpo = $pcuerpo;
		$this->alcance = $palcance;
		$this->numero_participacion = $pnumero_participacion;
		$this->fecha = $pfecha;
		$this->apellidoynombre = $papellidoynombre;
		$this->tipodoc = $ptipodoc;
		$this->nrodoc = $pnrodoc;
		$this->domicilio = $pdomicilio;
		$this->localidad = $plocalidad;
		$this->telefono = $ptelefono;
		$this->mail = $pmail;
		$this->institucion_nombre = $pinstitucion_nombre;
		$this->institucion_domicilio = $pinstitucion_domicilio;
		$this->texto = $ptexto;
		$this->documentacion = $pdocumentacion;

		// Invocación de inicialización de clase padre.
		parent::__construct();
	}
}
?>

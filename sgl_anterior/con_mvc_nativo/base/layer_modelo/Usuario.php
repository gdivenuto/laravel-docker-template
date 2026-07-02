<?php
/**
 * Clase Usuario
 * 
 * Descripción: admin_usuarios 
 * Layer Datos: DBSeguridad
 * Layer Negocio: NGSeguridad
 *
 * GenerateClass 0.97.5 beta @ 2016-09-20 14:08:44
 */
class Usuario extends ClaseBase {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $id_usuario          ; // (Primary Key) PHP: integer    MySQL: int(10) unsigned
	protected $codigo_usuario      ; // PHP: string     MySQL: varchar(20)
	protected $nombre_usuario      ; // PHP: string     MySQL: varchar(30) [Permite NULL]
	protected $iniciales_usuario   ; // PHP: string     MySQL: varchar(5) [Permite NULL]
	protected $password_usuario    ; // PHP: string     MySQL: varchar(50) [Permite NULL]
	protected $habilitado_usuario  ; // PHP: string     MySQL: char(1)
	protected $confirma_giros      ; // PHP: string     MySQL: char(1)
	protected $observaciones_usuario; // PHP: string    MySQL: text [Permite NULL]
	protected $u_legajo            ; // PHP: integer    MySQL: int(11) unsigned [Permite NULL]
	protected $u_mail              ; // PHP: string     MySQL: varchar(255) unsigned [Permite NULL]

	// Atributos extra (no hay relación con capa de datos)
	protected $cert_archivo; // Archivo .p12 con el certificado empaquetado del usuario
	protected $cert_firma_holo; // Archivo con la imagen de la firma holográfica del usuario
	protected $cert_password; // Contraseña de acceso al archivo .p12 con el certificado empaquetado del usuario

	
	// ************************************************************************
	// Getters & Setters ******************************************************
	// ************************************************************************
	public function getId_Usuario() { return $this->id_usuario; }
	public function setId_Usuario($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setId_Usuario(): no se permiten valores nulos para el atributo 'id_usuario'.", get_class($this)));
		if ( (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setId_Usuario(): el atributo 'id_usuario' solo permite valores de tipo integer.", get_class($this)));
		$this->id_usuario = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getCodigo_Usuario() { return $this->codigo_usuario; }
	public function setCodigo_Usuario($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setCodigo_Usuario(): no se permiten valores nulos para el atributo 'codigo_usuario'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setCodigo_Usuario(): el atributo 'codigo_usuario' solo permite valores de tipo string.", get_class($this)));
		$this->codigo_usuario = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getNombre_Usuario() { return $this->nombre_usuario; }
	public function setNombre_Usuario($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setNombre_Usuario(): el atributo 'nombre_usuario' solo permite valores de tipo string.", get_class($this)));
		$this->nombre_usuario = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getIniciales_Usuario() { return $this->iniciales_usuario; }
	public function setIniciales_Usuario($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setIniciales_Usuario(): el atributo 'iniciales_usuario' solo permite valores de tipo string.", get_class($this)));
		$this->iniciales_usuario = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getPassword_Usuario() { return $this->password_usuario; }
	public function setPassword_Usuario($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setPassword_Usuario(): el atributo 'password_usuario' solo permite valores de tipo string.", get_class($this)));
		$this->password_usuario = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getHabilitado_Usuario() { return $this->habilitado_usuario; }
	public function setHabilitado_Usuario($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setHabilitado_Usuario(): no se permiten valores nulos para el atributo 'habilitado_usuario'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setHabilitado_Usuario(): el atributo 'habilitado_usuario' solo permite valores de tipo string.", get_class($this)));
		$this->habilitado_usuario = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getConfirma_Giros() { return $this->confirma_giros; }
	public function setConfirma_Giros($value) { 
		if (is_null($value)) 
			throw new UnexpectedValueException(sprintf("Error en %s.setConfirma_Giros(): no se permiten valores nulos para el atributo 'confirma_giros'.", get_class($this)));
		if ( (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setConfirma_Giros(): el atributo 'confirma_giros' solo permite valores de tipo string.", get_class($this)));
		$this->confirma_giros = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getObservaciones_Usuario() { return $this->observaciones_usuario; }
	public function setObservaciones_Usuario($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setObservaciones_Usuario(): el atributo 'observaciones_usuario' solo permite valores de tipo string.", get_class($this)));
		$this->observaciones_usuario = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	public function getU_Legajo() { return $this->u_legajo; }
	public function setU_Legajo($value) {
		if ( (!is_null($value)) && (!$this->esInteger($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setU_Legajo(): el atributo 'u_legajo' solo permite valores de tipo integer.", get_class($this)));
		$this->u_legajo = $value;
		$this->setInstanceState(IS_MODIFIED);
	}	

	public function getU_Mail() { return $this->u_mail; }
	public function setU_Mail($value) { 
		if ((!is_null($value)) && (!is_string($value)))
			throw new InvalidArgumentException(sprintf("Error en %s.setU_Mail(): el atributo 'u_mail' solo permite valores de tipo string.", get_class($this)));
		$this->u_mail = $value;
		$this->setInstanceState(IS_MODIFIED);
	}

	// Atributos extra
	public function getCert_Archivo() { return $this->cert_archivo; }
	public function setCert_Archivo($value) {
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setCert_Archivo(): el atributo 'cert_archivo' solo permite valores de tipo string.", get_class($this)));
		$this->cert_archivo = $value;
		//$this->setInstanceState(IS_MODIFIED);
	}

	public function getCert_Firma_Holo() { return $this->cert_firma_holo; }
	public function setCert_Firma_Holo($value) { 
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setCert_Firma_Holo(): el atributo 'cert_firma_holo' solo permite valores de tipo string.", get_class($this)));
		$this->cert_firma_holo = $value; 
		//$this->setInstanceState(IS_MODIFIED);
	}

	public function getCert_Password() { return $this->cert_password; }
	public function setCert_Password($value) {
		if ( (!is_null($value)) && (!is_string($value)) ) 
			throw new InvalidArgumentException(sprintf("Error en %s.setCert_Password(): el atributo 'cert_password' solo permite valores de tipo string.", get_class($this)));
		$this->cert_password = $value;
		//$this->setInstanceState(IS_MODIFIED);
	}

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 * @param  integer (PK) id_usuario
	 * @param  string codigo_usuario
	 * @param  string nombre_usuario
	 * @param  string iniciales_usuario
	 * @param  string password_usuario
	 * @param  string habilitado_usuario
	 * @param  string observaciones_usuario
	 */
	public function __construct(
		$pid_usuario = null,
		$pcodigo_usuario = '',
		$pnombre_usuario = null,
		$piniciales_usuario = null,
		$ppassword_usuario = null,
		$phabilitado_usuario = '',
		$pconfirma_giros = '',
		$pobservaciones_usuario = null,
		$pu_legajo = null, 
		$pu_mail = null)
	{
		// Asignación de atributos.
		// Si las propiedades se acceden desde un ámbito con visibilidad directa, no 
		// se disparan los métodos mágicos __get() y __set(). Por el contrario, si se 
		// acceden 'desde fuera de la instancia', se disparan los métodos mágicos.
		$this->id_usuario           = $pid_usuario;
		$this->codigo_usuario       = $pcodigo_usuario;
		$this->nombre_usuario       = $pnombre_usuario;
		$this->iniciales_usuario    = $piniciales_usuario;
		$this->password_usuario     = $ppassword_usuario;
		$this->habilitado_usuario   = $phabilitado_usuario;
		$this->confirma_giros       = $pconfirma_giros;
		$this->observaciones_usuario = $pobservaciones_usuario;	
		$this->u_legajo             = $pu_legajo;
		$this->u_mail               = $pu_mail;

		// Atributos extra
		$this->cert_archivo    = null;
		$this->cert_firma_holo = null;
		$this->cert_password   = null;
		
		// Invocación de inicialización de clase padre.
		parent::__construct();
	}
}
?>
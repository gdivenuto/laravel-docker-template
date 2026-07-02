<?php
/**
 *
 * @author XXXX, XXXX
 *
 */

require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/config/path_config.php');

// Clase Base para codificar y decodificar con JSon
require_once(PATH_SGL_LAYER_MODELO_PRESTAMOS.'ClaseBaseSGL.php');

class SolicitudExpedienteExterno extends ClaseBaseSGL
{
	// ************************************************************************
	// Definición de Constantes ***********************************************
	// ************************************************************************
	// Estados del cicuito de solicitudes de expediente externo.
	// Ver documentación en ./sistema_gestion_legislativa/documentacion/Modulo de Prestamos y Ubicacion/
	const E_SOLICITADO_HCD = "SHCD";		// Solicitado desde HCD
	const E_SOLICITADO_EE = "SEE";		 	// Solicitado al ente externo
	const E_INGRESADO_EE = "IEE";			// Ingresado desde el ente externo
	const E_DEVUELTO_EE = "DEE";			// Devuelto al ente externo
	const E_ANULADO_EE = "AEE";				// Solicitud anulada

	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	// Identificador de la Solicitud
	public $anio;
	public $tipo;
	public $numero;
	public $cuerpo;
	public $alcance;
	public $digito;
	public $cuerpoalcance;
	public $anexoalcance;
	public $cuerpoanexoalcance;
	public $anexo;
	public $cuerpoanexo;
	public $fecha_solicitud_hcd; // ATENCION: se guarda como un string

	// Fechas de estado relevantes (mas la fecha_solicitud, que forma parte del identificador)
	public $fecha_solicitud_ee; // ATENCION: se guarda como un string
	public $fecha_ingresado_ee; // ATENCION: se guarda como un string
	public $fecha_devuelto_ee; // ATENCION: se guarda como un string
	public $fecha_anulado_ee;  // ATENCION: se guarda como un string

	// Estado de la solicitud
	public $estado;

	// Datos extra
	public $observaciones;
	public $id_usuario;

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct()
	{
		// Datos del expediente externo de HCD
		$this->anio = 0;
		$this->tipo = 'D';
		$this->numero = 0;
		$this->cuerpo = 0;
		$this->alcance = 0;
		$this->digito = "";
		$this->cuerpoalcance = 0;
		$this->anexoalcance = 0;
		$this->cuerpoanexoalcance = 0;
		$this->anexo = 0;
		$this->cuerpoanexo = 0;

		// Para el circuito administrativo de la solicitud
		$this->fecha_solicitud_hcd = null;
		$this->fecha_solicitud_ee = null;
		$this->fecha_ingresado_ee = null;
		$this->fecha_devuelto_ee = null;
		$this->fecha_anulado_ee = null;
		$this->estado = self::E_SOLICITADO_HCD;

		// Extras
		$this->observaciones = null;
		$this->id_usuario = -1; // valor inválido a propósito
	}

	/**
	 * Obtiene la fecha_solicitud_hcd como un DateTime
	 * @throws InvalidArgumentException
	 * @return DateTime
	 */
	public function Get_fecha_solicitud_hcd_AsDateTime()
	{
		return $this->VerificarDateTimeDesdeString($this->fecha_solicitud_hcd);
	}

	/**
	 * Obtiene la fecha_solicitud_ee como un DateTime
	 * @throws InvalidArgumentException
	 * @return DateTime
	 */
	public function Get_fecha_solicitud_ee_AsDateTime()
	{
		return $this->VerificarDateTimeDesdeString($this->fecha_solicitud_ee);
	}

	/**
	 * Obtiene la fecha_ingresado_ee como un DateTime
	 * @throws InvalidArgumentException
	 * @return DateTime
	 */
	public function Get_fecha_ingresado_ee_AsDateTime()
	{
		return $this->VerificarDateTimeDesdeString($this->fecha_ingresado_ee);
	}

	/**
	 * Obtiene la fecha_devuelto_ee como un DateTime
	 * @throws InvalidArgumentException
	 * @return DateTime
	 */
	public function Get_fecha_devuelto_ee_AsDateTime()
	{
		return $this->VerificarDateTimeDesdeString($this->fecha_devuelto_ee);
	}

	/**
	 * Obtiene la fecha_anulado_ee como un DateTime
	 * @throws InvalidArgumentException
	 * @return DateTime
	 */
	public function Get_fecha_anulado_ee_AsDateTime()
	{
		return $this->VerificarDateTimeDesdeString($this->fecha_anulado_ee);
	}

	/**
	 * Asigna un valor a la fecha de solicitud del hcd a partir de un parametro DateTime
	 * @param DateTime $fecha Valor tipo DateTime para asignar a la fecha.
	 */
	public function Set_fecha_solicitud_hcd_FromDateTime(DateTime $fecha)
	{
		$this->fecha_solicitud_hcd = ($fecha === null) ? null : $fecha->format('Y-m-d H:i:s');
	}

	/**
	 * Asigna un valor a la fecha de solicitud de expediente externo a partir de un parametro DateTime
	 * @param DateTime $fecha Valor tipo DateTime para asignar a la fecha.
	 */
	public function Set_fecha_solicitud_ee_FromDateTime(DateTime $fecha)
	{
		$this->fecha_solicitud_ee = ($fecha === null) ? null : $fecha->format('Y-m-d H:i:s');
	}

	/**
	 * Asigna un valor a la fecha de ingresado a partir de un parametro DateTime
	 * @param DateTime $fecha Valor tipo DateTime para asignar a la fecha.
	 */
	public function Set_fecha_ingresado_ee_FromDateTime(DateTime $fecha)
	{
		$this->fecha_ingresado_ee = ($fecha === null) ? null : $fecha->format('Y-m-d H:i:s');
	}

	/**
	 * Asigna un valor a la fecha devuelto a partir de un parametro DateTime
	 * @param DateTime $fecha Valor tipo DateTime para asignar a la fecha.
	 */
	public function Set_fecha_devuelto_ee_FromDateTime(DateTime $fecha)
	{
		$this->fecha_devuelto_ee = ($fecha === null) ? null : $fecha->format('Y-m-d H:i:s');
	}

	/**
	 * Asigna un valor a la fecha anulado a partir de un parametro DateTime
	 * @param DateTime $fecha Valor tipo DateTime para asignar a la fecha.
	 */
	public function Set_fecha_anulado_ee_FromDateTime(DateTime $fecha)
	{
		$this->fecha_anulado_ee = ($fecha === null) ? null : $fecha->format('Y-m-d H:i:s');
	}

	/**
	 * Obtiene de un estado su correspondiente descripción.
	 * @param string $estado
	 * @return string Descripción del estado.
	 * @throws InvalidArgumentException
	 */
	public function EstadoToString()
	{
		$cadena = "";

		switch ($this->estado)
		{
			case self::E_SOLICITADO_HCD : // Solicitado por el HCD
				$cadena = "Solicitado por el HCD";
				break;

			case self::E_SOLICITADO_EE : // Solicitado al Ente Externo
				$cadena = "Solicitado al ente externo";
				break;

			case self::E_INGRESADO_EE : // Ingresado desde el Ente Externo
				$cadena = "Ingresado desde ente externo";
				break;

			case self::E_DEVUELTO_EE : // Devuelto al Ente Externo
				$cadena = "Devuelto al ente externo";
				break;

			case self::E_ANULADO_EE : // Solicitud anulada
				$cadena = "Solicitud anulada";
				break;

			default:
				throw new InvalidArgumentException("El estado es inválido. Estado: ".$this->estado);
		}
		return $cadena;
	}

	/**
	 * Dado el estado de la solicitud, devuelve la fecha asociada. Es posible que la fecha sea null.
	 * @param string $estado Estado del cual se desea obtener la fecha.
	 * @return string Fecha del estado. Puede ser null.
	 * @throws InvalidArgumentException
	 */
	public function ObtenerFechaSegunEstado($estado)
	{
		$fecha = null;

		switch ($estado)
		{
			case self::E_SOLICITADO_HCD : // Solicitado por el HCD
				$fecha = $this->fecha_solicitud_hcd;
				break;

			case self::E_SOLICITADO_EE : // Solicitado al Ente Externo
				$fecha = $this->fecha_solicitud_ee;
				break;

			case self::E_INGRESADO_EE : // Ingresado desde el Ente Externo
				$fecha = $this->fecha_ingresado_ee;
				break;

			case self::E_DEVUELTO_EE : // Devuelto al Ente Externo
				$fecha = $this->fecha_devuelto_ee;
				break;

			case self::E_ANULADO_EE : // Solicitud anulada
				$fecha = $this->fecha_anulado_ee;
				break;

			default:
				throw new InvalidArgumentException("El estado de la solicitud es inválido. Estado: ".$estado);
		}

		return $fecha;
	}

	/**
	 * Dado el estado de la solicitud, devuelve la fecha asociada. Es posible que la fecha sea null.
	 * @param string $estado Estado del cual se desea obtener la fecha.
	 * @return DateTime Fecha del estado. Puede ser null.
	 * @throws InvalidArgumentException
	 */
	public function ObtenerFechaSegunEstadoAsDateTime($estado)
	{
		$fecha = $this->ObtenerFechaSegunEstado($estado);

		return $this->VerificarDateTimeDesdeString($fecha);
	}

	/**
	 * Devuelve la fecha actual de la solicitud (la fecha del estado actual de la solicitud).
	 * @return string La fecha del estado actual de la solicitud.
	 * @throws InvalidArgumentException
	 */
	public function ObtenerFechaActual()
	{
		return $this->ObtenerFechaSegunEstado($this->estado);
	}

	/**
	 * Devuelve la fecha actual de la solicitud (la fecha del estado actual de la solicitud).
	 * @return DateTime La fecha del estado actual de la solicitud.
	 * @throws InvalidArgumentException
	 */
	public function ObtenerFechaActualAsDateTime()
	{
		return $this->ObtenerFechaSegunEstadoAsDateTime($this->estado);
	}

	/**
	 * Este método devuelve el identificador de la solicitud formateada como una cadena.
	 * @return string
	 */
	public function ToStringDescription()
	{
		return	$this->anio."-".$this->tipo."-".$this->numero." ".
				$this->cuerpo."-".$this->alcance ." ".
				$this->digito ."-".$this->cuerpoalcance."-".$this->anexoalcance."-".$this->cuerpoanexoalcance ."-".$this->anexo."-".$this->cuerpoanexo;
	}

	/**
	 * Este método devuelve una cadena con la observación recortada. Recorta la observación y anexa puntos suspensivos al final
	 * si la descripción es mayor al largo.
	 * @param number $largo Cantidad de caracteres donde cortar.
	 * @return string Observación resumida.
	 */
	public function ObtenerResumenObservacion($largo = 20)
	{
		$salida = (is_null($this->observaciones)) ? '' : $this->observaciones;
		if (strlen($salida) > $largo)
			$salida = substr($salida, 0, $largo) . "...";
		return $salida;
	}

	}
?>

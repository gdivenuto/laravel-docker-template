<?php
/**
 *
 * @author XXXX, XXXX
 *
 */

require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/config/path_config.php');

// Clase Base para codificar y decodificar con JSon
require_once(PATH_SGL_LAYER_MODELO_PRESTAMOS.'ClaseBaseSGL.php');

class Prestamo extends ClaseBaseSGL
{
	// ************************************************************************
	// Definición de Constantes ***********************************************
	// ************************************************************************
	// Estados del cicuito de prestamos.
	// Ver documentación en ./sistema_gestion_legislativa/documentacion/Modulo de Prestamos y Ubicacion/
	const E_SOLICITADO = "S";		 	// Solicitado al HCD
	const E_PRESTADO = "P";				// Prestado desde el HCD
	const E_DEVUELTO = "D";				// Devuelto al HCD
	const E_ANULADO = "A";				// Prestamo anulado

	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	// Identificador de Prestamo
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
	public $fecha_solicitud; // ATENCION: se guarda como un string

	// Fechas de estado relevantes (mas la fecha_solicitud, que forma parte del identificador)
	public $fecha_prestado; // ATENCION: se guarda como un string
	public $fecha_devuelto; // ATENCION: se guarda como un string
	public $fecha_anulado;  // ATENCION: se guarda como un string

	// Estado del préstamo
	public $estado;

	// Datos extra
	public $solicitante_tipo;
	public $solicitante_codigo;
	public $solicitante_nombre;
	public $libro_numero;
	public $libro_folio;
	public $observaciones_prestamo;
	public $id_usuario;

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct()
	{
		// Datos del expediente "nativo" de HCD
		$this->anio = 0;
		$this->tipo = 'E';
		$this->numero = 0;
		$this->cuerpo = 0;
		$this->alcance = 0;

		// Datos extendidos del expediente (expendiente del ejecutivo)
		$this->digito = "";
		$this->cuerpoalcance = 0;
		$this->anexoalcance = 0;
		$this->cuerpoanexoalcance = 0;
		$this->anexo = 0;
		$this->cuerpoanexo = 0;

		// Para el circuito administrativo del préstamo
		$this->fecha_solicitud = null;
		$this->fecha_prestado = null;
		$this->fecha_devuelto = null;
		$this->fecha_anulado = null;
		$this->estado = self::E_SOLICITADO;

		// Extras
		$this->solicitante_tipo = null;
		$this->solicitante_codigo = null;
		$this->solicitante_nombre = null;
		$this->libro_numero = null;
		$this->libro_folio = null;
		$this->observaciones_prestamo = null;
		$this->id_usuario = -1; // valor inválido a propósito
	}

	/**
	 * Obtiene la fecha_solicitud como un DateTime
	 * @throws InvalidArgumentException
	 * @return DateTime
	 */
	public function Get_fecha_solicitud_AsDateTime()
	{
		return $this->VerificarDateTimeDesdeString($this->fecha_solicitud);
	}

	/**
	 * Obtiene la fecha_prestado como un DateTime
	 * @throws InvalidArgumentException
	 * @return DateTime
	 */
	public function Get_fecha_prestado_AsDateTime()
	{
		return $this->VerificarDateTimeDesdeString($this->fecha_prestado);
	}

	/**
	 * Obtiene la fecha_devuelto como un DateTime
	 * @throws InvalidArgumentException
	 * @return DateTime
	 */
	public function Get_fecha_devuelto_AsDateTime()
	{
		return $this->VerificarDateTimeDesdeString($this->fecha_devuelto);
	}

	/**
	 * Obtiene la fecha_anulado como un DateTime
	 * @throws InvalidArgumentException
	 * @return DateTime
	 */
	public function Get_fecha_anulado_AsDateTime()
	{
		return $this->VerificarDateTimeDesdeString($this->fecha_anulado);
	}

	/**
	 * Asigna un valor a la fecha de solicitud a partir de un parametro DateTime
	 * @param DateTime $fecha Valor tipo DateTime para asignar a la fecha.
	 */
	public function Set_fecha_solicitud_FromDateTime(DateTime $fecha)
	{
		$this->fecha_solicitud = ($fecha === null) ? null : $fecha->format('Y-m-d H:i:s');
	}

	/**
	 * Asigna un valor a la fecha prestado a partir de un parametro DateTime
	 * @param DateTime $fecha Valor tipo DateTime para asignar a la fecha.
	 */
	public function Set_fecha_prestado_FromDateTime(DateTime $fecha)
	{
		$this->fecha_prestado = ($fecha === null) ? null : $fecha->format('Y-m-d H:i:s');
	}

	/**
	 * Asigna un valor a la fecha devuelto a partir de un parametro DateTime
	 * @param DateTime $fecha Valor tipo DateTime para asignar a la fecha.
	 */
	public function Set_fecha_devuelto_FromDateTime(DateTime $fecha)
	{
		$this->fecha_devuelto = ($fecha === null) ? null : $fecha->format('Y-m-d H:i:s');
	}

	/**
	 * Asigna un valor a la fecha anulado a partir de un parametro DateTime
	 * @param DateTime $fecha Valor tipo DateTime para asignar a la fecha.
	 */
	public function Set_fecha_anulado_FromDateTime(DateTime $fecha)
	{
		$this->fecha_anulado = ($fecha === null) ? null : $fecha->format('Y-m-d H:i:s');
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
			case self::E_SOLICITADO : // Solicitado al HCD
				$cadena = "Solicitado al HCD";
				break;

			case self::E_PRESTADO : // Prestado desde el HCD
				$cadena = "Prestado desde el HCD";
				break;

			case self::E_DEVUELTO : // Devuelto al HCD
				$cadena = "Devuelto al HCD";
				break;

			case self::E_ANULADO : // Prestamo anulado
				$cadena = "Préstamo anulado";
				break;

			default:
				throw new InvalidArgumentException("El estado es inválido. Estado: ".$this->estado);
		}
		return $cadena;
	}

	/**
	 * Dado el estado del préstamo, devuelve la fecha asociada. Es posible que la fecha sea null.
	 * @param string $estado Estado del cual se desea obtener la fecha.
	 * @return string Fecha del estado. Puede ser null.
	 * @throws InvalidArgumentException
	 */
	public function ObtenerFechaSegunEstado($estado)
	{
		$fecha = null;

		switch ($estado)
		{
			case self::E_SOLICITADO : // Solicitado al HCD
				$fecha = $this->fecha_solicitud;
				break;

			case self::E_PRESTADO : // Prestado desde el HCD
				$fecha = $this->fecha_prestado;
				break;

			case self::E_DEVUELTO : // Devuelto al HCD
				$fecha = $this->fecha_devuelto;
				break;

			case self::E_ANULADO : // Prestamo anulado
				$fecha = $this->fecha_anulado;
				break;

			default:
				throw new InvalidArgumentException("El estado del préstamo es inválido. Estado: ".$estado);
		}

		return $fecha;
	}

	/**
	 * Dado el estado del préstamo, devuelve la fecha asociada. Es posible que la fecha sea null.
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
	 * Devuelve la fecha actual del préstamo (la fecha del estado actual del préstamo).
	 * @return string La fecha del estado actual del préstamo.
	 * @throws InvalidArgumentException
	 */
	public function ObtenerFechaActual()
	{
		return $this->ObtenerFechaSegunEstado($this->estado);
	}

	/**
	 * Devuelve la fecha actual del préstamo (la fecha del estado actual del préstamo).
	 * @return DateTime La fecha del estado actual del préstamo.
	 * @throws InvalidArgumentException
	 */
	public function ObtenerFechaActualAsDateTime()
	{
		return $this->ObtenerFechaSegunEstadoAsDateTime($this->estado);
	}

	/**
	 * Este método devuelve el identificador del prestamo formateado como una cadena.
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
		$salida = (is_null($this->observaciones_prestamo)) ? '' : $this->observaciones_prestamo;
		if (strlen($salida) > $largo)
			$salida = substr($salida, 0, $largo) . "...";
		return $salida;
	}

}
?>

<?php
abstract class ControladorBase {
	protected $mensaje;
	protected $tipo_mensaje;
	protected $modelo;
	protected $vista_grilla;
	protected $vista_edicion;
	protected $vista_pdf;
	protected $campo_orden_por_defecto;
	protected $rango_paginacion;
	protected $tipos_imagen_permitidas;
	protected $extensiones_fotos_permitidas;
	protected $extensiones_validas;

	// Mensajes de cada operación
	protected $mensaje_ingreso_ok;
	protected $mensaje_ingreso_error;
	protected $mensaje_ingreso_previo;
	protected $mensaje_modificacion_ok;
	protected $mensaje_modificacion_error;
	protected $mensaje_modificacion_previa;
	protected $mensaje_modificacion_estado_ok;
	protected $mensaje_modificacion_estado_error;
	protected $mensaje_eliminacion_ok;
	protected $mensaje_eliminacion_error;
	protected $mensaje_modificacion_prioridad_ok;
	protected $mensaje_modificacion_prioridad_error;
	protected $mensaje_registro_utilizado;
	protected $mensaje_registro_existente;
	protected $mensaje_registro_en_edicion;

	// Perfiles para ejecutar acciones genéricas
	protected $perfiles_permitidos_para_listar;
	protected $perfiles_permitidos_para_editar;
	protected $perfiles_permitidos_para_insertar;
	protected $perfiles_permitidos_para_modificar;
	protected $perfiles_permitidos_para_eliminar;
	protected $perfiles_permitidos_para_modificarEstado;

	public function __construct() {

		$this->rango_paginacion = CANT_POR_PAGINA;

		$this->tipos_imagen_permitidas = array("image/jpeg", "image/jpg", "image/png", "image/gif");
		$this->extensiones_fotos_permitidas = array("jpeg", "jpg", "png", "gif");
		$this->extensiones_validas = array("pdf", "doc", "docx", "odt", "xls", "xlsx", "ppt", "gif", "jpg", "jpeg", "png", "gif");
		
		$this->mensaje_ingreso_ok = "Se ha grabado con &eacute;xito.";
		$this->mensaje_ingreso_error = "No se ha grabado.";
		$this->mensaje_ingreso_previo = "El registro se ha ingresado previamente. Si presiona el bot&oacute;n Guardar se modificar&aacute; el existente.";
		$this->mensaje_modificacion_ok = "Se ha modificado con &eacute;xito.";
		$this->mensaje_modificacion_error = "No se ha modificado.";
		$this->mensaje_modificacion_previa = "El registro se ha modificado previamente. Si presiona el bot&oacute;n Guardar se modificar&aacute; el existente.";
		$this->mensaje_modificacion_estado_ok = "Se ha modificado el estado del registro con &eacute;xito.";
		$this->mensaje_modificacion_estado_error = "No se ha podido modificar el estado del registro.";
		$this->mensaje_eliminacion_ok = "Se ha eliminado con &eacute;xito.";
		$this->mensaje_eliminacion_error = "No se ha eliminado.";
		$this->mensaje_modificacion_prioridad_ok = "Se ha modificado la prioridad con &eacute;xito.";
		$this->mensaje_modificacion_prioridad_error = "No se ha podido modificar la prioridad.";
		$this->mensaje_registro_utilizado = "El registro est&aacute; siendo utilizado en el sistema.";
		$this->mensaje_registro_existente = "El registro ya se encuentra registrado.";
		$this->mensaje_registro_en_edicion = "El registro se encuentra en edici&oacute;n por otro usuario.";

		// Perfiles para ejecutar acciones específicas
		$this->perfiles_permitidos_para_listar = array(1, 2, 3);
		$this->perfiles_permitidos_para_editar = array(1, 2, 3);
		$this->perfiles_permitidos_para_insertar = array(1, 2, 3);
		$this->perfiles_permitidos_para_modificar = array(1, 2, 3);
		$this->perfiles_permitidos_para_eliminar = array(1, 2, 3);
		$this->perfiles_permitidos_para_modificarEstado = array(1, 2, 3);
	}

	public function listar() {}

	public function editar() {}

	// Devuelve el formato dia/mes/anio completo
	public function formatearFecha($fecha) {
		if ($fecha) {
			if ($fecha != '0000-00-00') {
				$fec_partes = explode("-", $fecha);
				$fecha_a_ver = $fec_partes[2] . '/' . $fec_partes[1] . '/' . $fec_partes[0];

				return $fecha_a_ver;
			} else
				return '';
		} else
			return '';
	}

	/**
	 * Recibe una fecha para verificar si es válida
	 * la fecha tiene el formato dd/mm/yyyy
	 * @param string $fecha
	 * @return boolean
	 */
	public function esFechaValida($fecha) {
		if ($fecha !== null || $fecha != '') {
			$fec_partes = explode("/", $fecha);
			$mes = (isset($fec_partes[1])) ? $fec_partes[1] : '';
			$dia = (isset($fec_partes[0])) ? $fec_partes[0] : '';
			$anio = (isset($fec_partes[2])) ? $fec_partes[2] : '';

			if ($mes !== null && $mes != '') {
				if ($dia !== null && $dia != '') {
					if ($anio !== null && $anio != '')
						return checkdate($mes, $dia, $anio);
					else
						return false;
				} else
					return false;
			} else
				return false;
		} else
			return false;
	}

	/**
	 * Recibe una fecha en formato yyyy-mm-dd, para verificar si es válida
	 * @param string $fecha
	 * @return boolean
	 */
	public function esFechaValidaYYYYMMDD($fecha) {
		if ($fecha !== null || $fecha != '') {
			$fec_partes = explode("-", $fecha);

			$anio = (isset($fec_partes[0])) ? $fec_partes[0] : '';
			$mes = (isset($fec_partes[1])) ? $fec_partes[1] : '';
			$dia = (isset($fec_partes[2])) ? $fec_partes[2] : '';

			if ($mes !== null && $mes != '') {
				if ($dia !== null && $dia != '') {
					if ($anio !== null && $anio != '')
						return checkdate($mes, $dia, $anio);
					else
						return false;
				} else
					return false;
			} else
				return false;
		} else
			return false;
	}

	/**
	 * Recibe una cadena para cortar a partir de un valor el cual indica la posición limite para dicho corte
	 * @param string $cadena
	 * @param integer $limite_cadena
	 * @return string
	 */
	public function cortaCadena($cadena, $limite_cadena) {
		if (substr($cadena, $limite_cadena - 1, 1) != '') {
			$cadena = substr($cadena, '0', $limite_cadena);
			$array = explode(' ', $cadena);
			array_pop($array);
			$nueva_cadena = implode(' ', $array);

			return $nueva_cadena . ' ...';
		} else
			return substr($cadena, '0', $limite_cadena - 1) . ' ...';
	}

	public function retirarBarraInvertida($registro) {
		foreach ($registro as $key => $value)
			$registro[$key] = stripslashes($value);
		
		return $registro;
	}

	/**
	 * Se comprueba si el perfil actual posee permiso para ejecutar la acción respectiva
	 * @param  array $perfiles_a_verificar Perfiles aceptados
	 */
	public function comprobarAcceso($perfiles_a_verificar, $perfil) {
		// Si el perfil actual NO posee permiso para ejecutar la acción respectiva
		if (!in_array($perfil, $perfiles_a_verificar)) {
			$_SESSION['info_persistente']['mensaje'] = "Permiso denegado.";
			$_SESSION['info_persistente']['tipo_mensaje'] = 2;
			// Se redirecciona a la pantalla principal del sistema
			header("Location: index.php?controlador=inicio&accion=informar");
			exit();
		}
	}

	/**
	 * Se ingresa un registro determinado
	 */
	public function insertarBase()
	{
		$datos = $_REQUEST;

		if ($this->modelo->existe($datos))
			$this->listar($this->mensaje_registro_existente, 2, $datos['pagina']);
		elseif ($this->modelo->insertar($datos))
			$this->listar($this->mensaje_ingreso_ok, 1, $datos['pagina']);
		else
			$this->listar($this->mensaje_ingreso_error, 2, $datos['pagina']);
	}

	/**
	 * Se modifica un registro determinado
	 */
	public function modificarBase()
	{
		$datos = $_REQUEST;

		if ($this->modelo->noLoModificoOtroUsuario()) {
			if ($this->modelo->modificar($datos))
				$this->listar($this->mensaje_modificacion_ok, 1, $datos['pagina']);
			else
				$this->editar($datos, $this->mensaje_modificacion_error, 2);
		} else
			$this->editar($datos, $this->mensaje_modificacion_previa, 2);
	}

	/**
	 * Se elimina un registro determinado
	 */
	public function eliminarBase()
	{
		$id = LibreriaGeneral::recoge('id', 0);
		$pagina = LibreriaGeneral::recoge('pagina');
		
		if ($this->modelo->eliminar($id)) {
			$this->listar($this->mensaje_eliminacion_ok, 1, $pagina);
		} else {
			$this->listar($this->mensaje_eliminacion_error, 2, $pagina);
		}
	}

	/**
	 * Se modifica el estado Habilitado|Deshabilitado
	 */
	public function modificarEstadoBase()
	{
		$id = LibreriaGeneral::recoge('id');
		$habilitado = LibreriaGeneral::recoge('habilitado');
		$pagina = LibreriaGeneral::recoge('pagina');

		if ($this->modelo->modificarEstado($id, $habilitado))
			$this->listar($this->mensaje_modificacion_estado_ok, 1, $pagina);
		else
			$this->listar($this->mensaje_modificacion_estado_error, 2, $pagina);
	}
}
?>
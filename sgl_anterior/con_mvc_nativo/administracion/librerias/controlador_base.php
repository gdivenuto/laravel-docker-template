<?php
abstract class ControllerBase {
	protected $modelo;
	protected $vista;
	protected $vista_grilla;
	protected $vista_edicion;
	protected $campo_orden_por_defecto;
	protected $rango_paginacion;
	protected $extensiones_fotos_permitidas;
	protected $extensiones_validas;
	protected $extensiones_audio_validas;
	protected $extensiones_video_validas;
	protected $extensiones_opendata_validas;
	protected $mensaje;
	protected $tipo_mensaje;

	// Mensajes de cada operación
	protected $mensaje_ingreso_ok;
	protected $mensaje_ingreso_error;
	protected $mensaje_ingreso_previo;
	protected $mensaje_modificacion_ok;
	protected $mensaje_modificacion_error;
	protected $mensaje_modificacion_previa;
	protected $mensaje_modificacion_estado_ok;
	protected $mensaje_modificacion_estado_error;
	protected $mensaje_modificacion_estado_gratis_pago_ok;
	protected $mensaje_modificacion_estado_gratis_pago_error;
	protected $mensaje_eliminacion_ok;
	protected $mensaje_eliminacion_error;
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

		$this->rango_paginacion = 15;
		
		// Extensiones permitidas para las fotos
		$this->extensiones_fotos_permitidas = array("jpeg", "jpg", "png", "gif");
		// Extensiones válidas de archivo a subir
		$this->extensiones_validas = array("txt", "pdf", "doc", "docx", "odt", "xls", "xlsx", "ppt", "gif", "jpg", "jpeg", "png", "gif");
		// Extensiones permitidas para los audios
		$this->extensiones_audio_validas = array("mp3", "mp4", "m4a", "mpeg");
		// Extensiones permitidas para los videos
		$this->extensiones_video_validas = array("mp4", "m4a", "mov");
		// Extensiones válidas de archivo a subir
		$this->extensiones_opendata_validas = array("json", "ods", "odt", "csv", "pdf", "html", "txt");

		$this->mensaje_ingreso_ok = "Se ha grabado con &eacute;xito.";
		$this->mensaje_ingreso_error = "No se ha grabado.";
		$this->mensaje_ingreso_previo = "El registro se ha ingresado previamente. Si presiona el bot&oacute;n Guardar se modificar&aacute; el existente.";
		$this->mensaje_modificacion_ok = "Se ha modificado con &eacute;xito.";
		$this->mensaje_modificacion_error = "No se ha modificado.";
		$this->mensaje_modificacion_previa = "El registro se ha modificado previamente. Si presiona el bot&oacute;n Guardar se modificar&aacute; el existente.";
		$this->mensaje_modificacion_estado_ok = "Se ha modificado el estado del registro con &eacute;xito.";
		$this->mensaje_modificacion_estado_error = "No se ha podido modificar el estado del registro.";
		$this->mensaje_modificacion_estado_gratis_pago_ok = "Se ha modificado el estado Gratis|Pago";
		$this->mensaje_modificacion_estado_gratis_pago_error = "No se ha modificado el estado Gratis|Pago";
		$this->mensaje_eliminacion_ok = "Se ha eliminado con &eacute;xito.";
		$this->mensaje_eliminacion_error = "No se ha eliminado.";
		$this->mensaje_registro_utilizado = "El registro est&aacute; siendo utilizado en el sistema.";
		$this->mensaje_registro_existente = "El registro ya se encuentra registrado.";
		$this->mensaje_registro_en_edicion = "El registro se encuentra en edici&oacute;n por otro usuario.";

		// Perfiles para ejecutar acciones específicas
		$this->perfiles_permitidos_para_listar = array(10, 11, 12, 14, 15, 23, 24, 25, 26);
		$this->perfiles_permitidos_para_editar = array(10, 11, 12, 14, 15, 23, 24, 25, 26);
		$this->perfiles_permitidos_para_insertar = array(10, 11, 12, 14, 15, 23, 24, 25, 26);
		$this->perfiles_permitidos_para_modificar = array(10, 11, 12, 14, 15, 23, 24, 25, 26);
		$this->perfiles_permitidos_para_eliminar = array(10, 11, 12, 14, 15, 23, 24, 25, 26);
		$this->perfiles_permitidos_para_modificarEstado = array(10, 11, 12, 14, 15, 23, 24, 25, 26);
	}

	// SE LE DA EL FORMATO dia/mes/anio completo PARA ENVIAR A LA VISTA
	public function formatearFecha($fecha) {
		if ($fecha) {
			if ($fecha != '0000-00-00') {
				$fec_partes = explode("-", $fecha);
				$fecha_a_ver = $fec_partes[2] . '/' . $fec_partes[1] . '/' . $fec_partes[0]; // PARA 2 CIFRAS substr($fec_partes[0], -2)

				return $fecha_a_ver;
			} else {
				return '';
			}

		} else {
			return '';
		}
	}

	/**
	 * Convierte una fecha de formato dd/mm/aaaa a formato aaaa-mm-dd para MySQL
	 * @param string $fecha
	 * @return string|NULL fecha en formato aaaa-mm-dd para MySQL
	 */
	public function convertirFechaToMySQL($fecha, $para_fecha_hasta = false) {
		// Si la fecha no es nula ni vacía
		if ($fecha !== null && $fecha != '') {
			// Se divide la fecha por la barra
			$fec_partes = explode("/", $fecha);

			$mes = $fec_partes[1];
			$dia = $fec_partes[0];
			$anio = $fec_partes[2];

			// Fecha en formato aaaa-mm-dd para MySQL
			$fecha_en_formato_mysql = $anio . '-' . $mes . '-' . $dia;

			// Si se trata de una fecha fin de un rango, se concatena el último horario válido de un día
			if ($para_fecha_hasta) {
				return $fecha_en_formato_mysql . ' 23:59:59';
			}

			return $fecha_en_formato_mysql;
		} else {
			return null;
		}
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
					if ($anio !== null && $anio != '') {
						return checkdate($mes, $dia, $anio);
					} else {
						return false;
					}

				} else {
					return false;
				}

			} else {
				return false;
			}

		} else {
			return false;
		}

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
					if ($anio !== null && $anio != '') {
						return checkdate($mes, $dia, $anio);
					} else {
						return false;
					}

				} else {
					return false;
				}

			} else {
				return false;
			}

		} else {
			return false;
		}

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
		} else {
			return substr($cadena, '0', $limite_cadena - 1) . ' ...';
		}

	}

	public function configurarPaginacion($filtro, $modelo, $campo_orden_por_defecto, $cantidad_registros_a_mostrar) {
		// SI SE RECIBE UN MENSAJE DEL RESULTADO DE UNA OPERACION REALIZADA
		if (recoge('mensaje')) {
			$mensaje = recoge('mensaje');
		}

		// SE SETEA EL VALOR A BUSCAR
		$valor_buscado = recoge('valor_buscado');
		$filtro['valor_buscado'] = ($valor_buscado != '') ? $valor_buscado : '';

		// SE SETEA EL CAMPO POR EL CUAL ORDENAR
		$campo_orden = recoge('campo_orden');
		if ($campo_orden != '') {
			$filtro['campo_orden'] = $campo_orden;
		} else {
			//por defecto
			$filtro['campo_orden'] = $campo_orden_por_defecto;
			$_SESSION['ultimo_campo'] = '';
		}

		//DIRECCION PARA LA PAGINACION (PRIMERO, ANTERIOR, SGTE., ULTIMO)
		$filtro['sentido'] = recoge('sentido');

		if (!isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $filtro['campo_orden']) {
			// Si es la primera vez que carga la pagina
			// o se esta cambiando el campo por el que se ordena
			$_SESSION['ultimo_campo'] = $filtro['campo_orden'];
			$_SESSION['ultimo_sentido'] = 'asc';
		} else {
			// Si se hizo clic en el mismo que ya estaba ordenado antes, solo hay que cambiar el sentido:
			if ($_SESSION['ultimo_sentido'] == 'asc' && $filtro['sentido'] == '') {
				$_SESSION['ultimo_sentido'] = 'desc';
			} else {
				$_SESSION['ultimo_sentido'] = 'asc';
			}

		}

		$filtro['rango'] = $cantidad_registros_a_mostrar;
		$filtro['pagina'] = recoge('pagina'); //se obtiene el valor de la pagina

		if (!$filtro['pagina']) {
			// Al comienzo no se sabe el valor de la pagina
			$filtro['inicio'] = 0; //por lo tanto se inicia en el primer registro
			$filtro['pagina'] = 1; //con la primer pagina
		} else // sino se calcula el valor del registro inicial de la pagina deseada
		{
			// si no se utiliza el buscador
			if ($filtro['valor_buscado'] == '') {
				$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
			}
		}

		$filtro['pagina_ant'] = $filtro['pagina'] - 1; //para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1; //para la pagina posterior

		//Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
		$filtro['cantidad'] = $modelo->obtenerCantidad();

		//NUMERO TOTAL DE PAGINAS
		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		// SI SE LLEG� MEDIANTE EL TECLADO AL RECORRER EL LISTADO
		$filtro['por_teclado'] = recoge('por_teclado');

		//Se establece el filtro en el modelo
		$modelo->setFiltro($filtro);

		return $filtro;
	}

	public function retirarBarraInvertida($registro) {
		foreach ($registro as $key => $value) {
			$registro[$key] = stripslashes($value);
		}

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
	public function insertarBase() {
		
		// Se reciben los datos
		$datos = $_REQUEST;
		
		// Si ya existe el registro
		if ($this->modelo->existe($datos)) {
			$this->listar($this->mensaje_registro_existente, 2, $datos['pagina']);
		} elseif ($this->modelo->insertar($datos))
		// Se muestra la grilla
		{
			$this->listar($this->mensaje_ingreso_ok, 1, $datos['pagina']);
		} else {
			$this->listar($this->mensaje_ingreso_error, 2, $datos['pagina']);
		}
	}

	/**
	 * Se modifica un registro determinado
	 */
	public function modificarBase() {
		
		// Se reciben los datos
		$datos = $_REQUEST;

		// 	Si no ha sido modificado previamente
		if ($this->modelo->noLoModificoOtroUsuario())
		// Si se modificó
		{
			if ($this->modelo->modificar($datos)) {
				// Se desmarca para informar que ya se editó
				//$this->modelo->desmarcarEnEdicion($datos['id']);
				// Se muestra la grilla
				$this->listar($this->mensaje_modificacion_ok, 1, $datos['pagina']);
			} else
			// sino se sigue editando
			{
				$this->editar($datos, $this->mensaje_modificacion_error, 2);
			}
		} else
		// sino se sigue editando
		{
			$this->editar($datos, $this->mensaje_modificacion_previa, 2);
		}
	}

	/**
	 * Se elimina un registro determinado
	 */
	public function eliminarBase() {
		
		$id = LibreriaGeneral::recoge('id', 0);
		$pagina = LibreriaGeneral::recoge('pagina');

		// Se intenta eliminar
		if ($this->modelo->eliminar($id)) {
			$this->listar($this->mensaje_eliminacion_ok, 1, $pagina);
		} else {
			// Si surgió un error al eliminar
			$this->listar($this->mensaje_eliminacion_error, 2, $pagina);
		}

	}

	/**
	 * Se modifica el estado Habilitado|Deshabilitado
	 */
	public function modificarEstadoBase() {
		
		$id = LibreriaGeneral::recoge('id');
		$habilitado = LibreriaGeneral::recoge('habilitado');
		$pagina = LibreriaGeneral::recoge('pagina');

		// Se habilita|deshabilita
		if ($this->modelo->modificarEstado($id, $habilitado)) {
			$mensaje = $this->mensaje_modificacion_estado_ok;
			$tipo_mensaje = 1;
		} else {
			$mensaje = $this->mensaje_modificacion_estado_error;
			$tipo_mensaje = 2;
		}

		// Se vuelve a mostrar el listado
		$this->listar($mensaje, $tipo_mensaje, $pagina);
	}
}
?>

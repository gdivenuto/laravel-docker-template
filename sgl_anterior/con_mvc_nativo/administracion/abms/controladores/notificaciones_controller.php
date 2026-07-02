<?php
if (!isset($_SESSION)) {
	session_start();
}

// Incluye la libreria (y configuracion) de envio por mail mediante PHPList
require_once $_SERVER['DOCUMENT_ROOT'] . "/sgl/librerias/PhpListRESTApiClient.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/sgl/config/mail_config.php";

//Incluye el modelo que corresponde
require_once RUTA_MODELOS . "notificaciones.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "notificaciones/grilla.php";
require_once RUTA_VISTAS . "notificaciones/edicion.php";
require_once RUTA_VISTAS . "notificaciones/suscriptores.php";
require_once RUTA_VISTAS . "notificaciones/previa.php";
require_once RUTA_VISTAS . "notificaciones/pdf.php";

class notificaciones_controller extends ControllerBase {

	private $directorio_adjuntos_notificaciones;
	private $error_al_cargar_adjuntos;

	public function __construct() {
		parent::__construct();

		$this->campo_orden_por_defecto = 'n_fecha';

		// Se crea una instancia del modelo
		$this->modelo = new notificacionesModel();

		// Se crea una instancia de cada Vista
		$this->vista_grilla = new VistaNotificacionesGrilla();
		$this->vista_edicion = new VistaNotificacionesEdicion();
		$this->vista_suscriptores = new VistaNotificacionesSuscriptores();
		$this->vista_previa = new VistaNotificacionesPrevia();
		$this->vista_pdf = new VistaNotificacionesPDF();

		// Se inicializa el mensaje de resultados
		$this->mensaje = "";
	}

	public function guardarRegistroOriginal($original) {
		$_SESSION['n_id_original'] = $original['n_id'];
		$_SESSION['n_fecha_original'] = $original['n_fecha'];
		$_SESSION['n_asunto_original'] = $original['n_asunto'];
		$_SESSION['n_mensaje_original'] = $original['n_mensaje'];
		$_SESSION['n_id_grupo_destino_original'] = $original['n_id_grupo_destino'];
		$_SESSION['n_habilitada_original'] = $original['n_habilitada'];
		$_SESSION['n_enviada_original'] = $original['n_enviada'];
		$_SESSION['n_id_mail_original'] = $original['n_id_mail'];
	}

	public function listar($mensaje = '', $tipo_mensaje = '') {
		$filtro = Array();

		// Filtro por Fecha
		$f_fecha = LibreriaGeneral::recoge('f_fecha');
		$filtro['f_fecha'] = (isset($f_fecha) && $this->esFechaValida($f_fecha)) ? $this->modelo->formatearFechaMySQL($f_fecha) : '';

		// Filtro por Asunto
		$filtro['f_asunto'] = LibreriaGeneral::recoge('f_asunto');

		// Filtro por Grupo destino
		$filtro['f_grupo_destino'] = LibreriaGeneral::recoge('f_grupo_destino');

		// se establece el campo por el cual ordenar
		$campo_orden = LibreriaGeneral::recoge('campo_orden');
		if ($campo_orden != '') {
			$filtro['campo_orden'] = $campo_orden;
		} else {
			//por defecto
			$filtro['campo_orden'] = $this->campo_orden_por_defecto;
			$_SESSION['ultimo_campo'] = '';
		}

		// DIRECCION PARA LA PAGINACION (PRIMERO, ANTERIOR, SGTE., ULTIMO)
		$filtro['sentido'] = LibreriaGeneral::recoge('sentido');

		if (!isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $filtro['campo_orden']) {
			// Si es la primera vez que carga la pagina o se esta cambiando el campo por el que se ordena
			$_SESSION['ultimo_campo'] = $filtro['campo_orden'];
			$_SESSION['ultimo_sentido'] = 'asc';
		} else {
			// Si se hizo clic en el mismo que ya estaba ordenado antes, solo hay que cambiar el sentido
			$_SESSION['ultimo_sentido'] = ($_SESSION['ultimo_sentido'] == 'asc' && $filtro['sentido'] == '') ? 'desc' : 'asc';
		}

		// Cantidad de registros a mostrar
		$filtro['rango'] = $this->rango_paginacion;

		//LibreriaGeneral::registrarLog("filtro", $filtro);

		// SE ESTABLECE EL FILTRO EN EL MODELO
		$this->modelo->setFiltro($filtro);

		// Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
		$filtro['cantidad'] = $this->modelo->obtenerCantidad();

		//NUMERO TOTAL DE PAGINAS
		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		$filtro['pagina'] = LibreriaGeneral::recoge('pagina');

		// SI NO SE RECIBIÓ LA PÁGINA
		if (!$filtro['pagina']) {
			// SE ESTABLECE LA ÚLTIMA
			$filtro['pagina'] = ($filtro['nro_paginas'] > 0) ? $filtro['nro_paginas'] : 1;

			// SI LA CANTIDAD ES MENOR AL RANGO DE PAGINA
			if ($filtro['cantidad'] < $filtro['rango']) {
				$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
			} else {
				// SE MUESTRAN LOS ÚLTIMOS (VALOR DEL RANGO) REGISTROS
				$filtro['inicio'] = $filtro['cantidad'] - $filtro['rango'];
			}

		} else {
			$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
		}

		$filtro['pagina_ant'] = $filtro['pagina'] - 1; // para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1; // para la pagina posterior

		// SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
		$_SESSION['f_notificaciones'] = $filtro;

		// SE ESTABLECE EL FILTRO EN EL MODELO
		$this->modelo->setFiltro($filtro);

		// SE OBTIENE EL LISTADO
		$listado = $this->modelo->listar();

		// Se obtienen y asignan los Nombres de cada Lista a la que se envió cada Notificación
		$listado = $this->asignarleNombresDeListas($listado);

		// se muestra el listado
		$this->vista_grilla->mostrar($listado, $mensaje, $tipo_mensaje, $filtro);
	}

	/**
	 * Se obtienen y asignan los Nombres de cada Lista a la que se envió
	 * @param  array $listado Listado de Notificaciones
	 * @return array          Listado de Notificaciones CON los Nombres de cada Lista a la que se envió
	 */
	private function asignarleNombresDeListas($listado) {

		$cantidad = (isset($listado)) ? count($listado) : 0;
		// Por cada Notificación
		for ($i = 0; $i < $cantidad; $i++) {
			// Si se ha enviado a listas de distribución
			if ($listado[$i]['n_phplist_ids_destino'] != null) {
				// Se separan los IDs de las listas que posee asignadas
				$id_listas = explode(',', $listado[$i]['n_phplist_ids_destino']);

				$cantidad_id_listas = (isset($id_listas)) ? count($id_listas) : 0;
				// Por cada Lista
				for ($l = 0; $l < $cantidad_id_listas; $l++) {
					// Si NO es la lista nro. 2
					if ($id_listas[$l] != 2) {
						// Se obtiene y asigna el Nombre de dicha lista
						$listado[$i]['nombre_lista'][$l] = $this->modelo->obtenerNombreLista($id_listas[$l]);
					}
				}
				$cantidad_id_listas = 0;
			}
		}
		return $listado;
	}

	public function editar($datos_formulario = null, $mensaje = '', $tipo_mensaje = '') {

		// Si NO se viene del formulario de edición por un error
		if ($datos_formulario === null) {
			// Se recibe el Id para su edición
			$id = LibreriaGeneral::recoge('id', 0);

			// Se busca el registro en la base de datos
			$datos = $this->modelo->obtenerRegistro($id);

			// Si existe
			if ($datos['n_id']) {
				// Se audita la consulta del registro
				//$this->modelo->auditarConsultaRegistro($datos);

				// Se guarda el registro en sesion para verificar luego si no ha modificado otro usuario
				$this->guardarRegistroOriginal($datos);

				// Se marca para saber que se encuentra en edición
				//$this->modelo->marcarEnEdicion($datos['id']);

				// Se recibe si es una Fe de Erratas o no
				$datos['es_fe_erratas'] = LibreriaGeneral::recoge('es_fe_erratas');

				$datos['pagina'] = LibreriaGeneral::recoge('pagina', 1);

				$datos = $this->retirarBarraInvertida($datos);
			} else {
				// En caso de editarse un NUEVO registro
				$datos = null;
			}

		} else {
			// SI SE VIENE DEL FORMULARIO DEBIDO A UN ERROR
			$datos = $datos_formulario;
		}

		// Se obtienen las Listas de Distribución
		$listas = $this->modelo->obtenerListas();

		$this->vista_edicion->mostrar($datos, $listas, $mensaje, $tipo_mensaje);
	}

	public function seguirEditando() {
		$datos = Array();

		// Se toman los datos para seguir editando
		$datos = $_SESSION['administracion'];

		// Se elimina la información guardada en sesión
		unset($_SESSION['administracion']);

		// Se formatea la fecha recibida como yyyy-mm-dd para seguir editando en la Vista
		$datos['n_fecha'] = ($datos['n_fecha'] != '') ? LibreriaGeneral::formatearFechaConGuiones($datos['n_fecha']) : '';

		// PARA MANTENER LAS lISTAS ELEGIDAS
		// Se agrupan los IDs de las Listas Adicionales elegidas, separadas por coma, para guardarlos en el campo respectivo
		// Se les antecede el Id de la Lista 2, utilizada para el prefijo [Notificaciones HCD] del asunto de la campaña
		$datos['n_phplist_ids_destino'] = '2,' . implode(',', $datos['listas_destino']);

		// Se obtienen las Listas de Distribución
		$listas = $this->modelo->obtenerListas();

		$this->vista_edicion->mostrar($datos, $listas, $mensaje, $tipo_mensaje);
	}

	/**
	 * Se suben en temporal/ los archivos elegidos
	 */
	public function subirEnTemporal() {
		// Se recibe la info
		$datos = $_REQUEST;
		// Se reciben los archivos adjuntos
		$info_de_archivos = $_FILES['adjuntos'];

		//LibreriaGeneral::registrarLog("datos", $datos);
		//LibreriaGeneral::registrarLog("info_de_archivos", $info_de_archivos);

		// Si se recibieron los datos de la notificación con los adjuntos para cargar
		if (isset($datos) && isset($info_de_archivos['name'][0]) && $info_de_archivos['name'][0] != '') {

			// Se intenta subir cada archivo recibido
			foreach ($info_de_archivos['name'] as $f => $nombre_archivo) {

				// Se toma la extensión del archivo y se convierte a minúscula
				$extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));

				// Si su extensión no es válida
				if (!in_array($extension, $this->extensiones_validas)) {
					$this->mensaje = "La extensi&oacute;n de " . $nombre_archivo . " no es v&aacute;lida.";
					$this->tipo_mensaje = 2;
				} else {
					// Archivo del adjunto
					$archivo_a_guardar = $info_de_archivos['tmp_name'][$f];

					// Se eliminan los espacios vacíos que contenga el nombre del archivo
					// se convierte a minúsculas
					// se coloca el prefijo definido en la Vista
					$nombre_archivo_a_guardar = $datos['prefijo'] . '__' . mb_strtolower(LibreriaGeneral::reemplazarEspaciosPorGuionesBajos(LibreriaGeneral::quitarAcentos($nombre_archivo)));

					// Si no se recibió el archivo
					if ($info_de_archivos['error'][$f] == 4) {
						$this->mensaje = "No se ha subido el archivo " . $nombre_archivo;
						$this->tipo_mensaje = 2;
						continue; // Se saltea el archivo
					}

					// Si el archivo fue recibido sin errores
					if ($info_de_archivos['error'][$f] == 0) {

						// Se arma la ruta destino: directorio + nombre de archivo
						$ruta_destino_completa = RUTA_DIRECTORIO_TEMPORAL . $nombre_archivo_a_guardar;

						// Se mueve el archivo al directorio destino
						if (move_uploaded_file($archivo_a_guardar, $ruta_destino_completa)) {
							// Número de archivos subidos con éxito
							$nro_archivos_subidos++;
						}
					}
				}
			}
			// Si no surgió un error
			if ($this->tipo_mensaje != 2) {
				$this->mensaje = "Se ha realizado la carga de " . $nro_archivos_subidos . " archivo/s satisfactoriamente!";
				$this->tipo_mensaje = 1;
			}
		} else {
			$this->mensaje = "No se han recibido datos para la carga de adjuntos.";
			$this->tipo_mensaje = 2;
		}

		// Se formatean las fechas recibidas como yyyy-mm-dd para seguir editando en la Vista
		$datos['n_fecha'] = ($datos['n_fecha'] != '') ? LibreriaGeneral::formatearFechaConGuiones($datos['n_fecha']) : '';

		// Para mantener las listas elegidas
		// Se agrupan los IDs de las Listas Adicionales elegidas, separadas por coma, para guardarlos en el campo respectivo
		// Se les antecede el Id de la Lista 2, utilizada para el prefijo [Notificaciones HCD] del asunto de la campaña
		$datos['n_phplist_ids_destino'] = '2,' . implode(',', $datos['listas_destino']);

		// Se obtienen las Listas de Distribución
		$listas = $this->modelo->obtenerListas();

		$this->vista_edicion->mostrar($datos, $listas, $mensaje, $tipo_mensaje);
	}

	/**
	 * Se cancela la edicion de una notificación determinada
	 * en caso que se hayan cargado adjuntos temporales, se eliminan
	 * y se vuelve a la grilla.
	 */
	public function cancelarEdicion() {
		// Se recibe el prefijo
		$prefijo = LibreriaGeneral::recoge('prefijo', 0);

		// Se eliminan los archivos del directorio temporal
		// (en caso que posea adjuntos temporales)
		if (!$this->eliminarTemporales($prefijo)) {
			$this->listar("No se han eliminado los temporales", 2);
		}

		$this->listar();
	}

	/**
	 * Se guarda la Notificación
	 */
	public function guardar() {

		$datos = $_REQUEST; // Se recibe la info

		// Se verifica que se haya seleccionado por lo menos una Lista
		if (isset($datos['listas_destino']) && count($datos['listas_destino']) > 0) {
			// Se agrupan los IDs de las Listas Adicionales elegidas, separadas por coma, para guardarlos en el campo respectivo
			// Se les antecede el Id de la Lista 2, utilizada para el prefijo [Notificaciones HCD] del asunto de la campaña
			$datos['n_phplist_ids_destino'] = '2,' . implode(',', $datos['listas_destino']);
		} else {
			$datos['n_phplist_ids_destino'] = null;
		}

		//LibreriaGeneral::registrarLog("datos_guardar", $datos);

		// Si ya existe
		if ($datos['n_id'] != '') {
			// Se modifica en la DB
			$this->modificar($datos);
		} else {
			// Se ingresa en la DB
			$this->insertar($datos);
			// Se obtiene el Id recién registrado
			$datos['n_id'] = $this->modelo->obtenerUltimoId();
		}

		// Se copian los adjuntos temporales al directorio correspondiente (en caso que posea)
		if ($this->pasarAdjuntos($datos['prefijo'], $datos['n_id'])) {
			// Se eliminan los archivos del directorio temporal
			$this->eliminarTemporales($datos['prefijo']);
		}

		$this->listar("Se ha guardado la Notificaci&oacute;n con &eacute;xito.", 1);
	}

	/**
	 * Se pasan los adjuntos al directorio correspondiente
	 * @param  [integer] $prefijo 	Para identificar la Notificación
	 * @param  [integer] $id 		Identificador de la Notificación
	 * @return [boolean]
	 */
	public function pasarAdjuntos($prefijo, $id) {
		// Si existe el directorio
		if (is_dir(RUTA_DIRECTORIO_TEMPORAL)) {
			// Si pudo abrirse el directorio de los Adjuntos
			if ($handle = opendir(RUTA_DIRECTORIO_TEMPORAL)) {
				// Mientras encuentre un archivo
				while (false !== ($file = readdir($handle))) {
					// Si es un archivo válido
					if ($file != "." && $file != ".." && $file != "index.html") {
						// Si pertenece a la notificación respectiva
						if (LibreriaGeneral::esAdjuntoDe($prefijo, $file)) {
							// Se divide el nombre del temporal
							$aux = explode('__', $file);
							// Se agrega como prefijo el id en el nombre del adjunto
							$nombre_adjunto = LibreriaGeneral::quitarAcentos($id . '__' . $aux[1]);

							// Se intenta copiar el archivo al directorio destino
							if (!copy(RUTA_DIRECTORIO_TEMPORAL . $file, RUTA_ADJUNTOS_NOTIFICACIONES . $nombre_adjunto)) {
								return false;
							}
						}
					}
				}
				closedir($handle);

				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Se eliminan los adjuntos temporales de una notificación determinada
	 * @param  [integer] $prefijo 	Para identificar la Notificación
	 * @return [boolean]
	 */
	public function eliminarTemporales($prefijo) {
		// Si existe el directorio
		if (is_dir(RUTA_DIRECTORIO_TEMPORAL)) {
			// Si pudo abrirse el directorio de los Adjuntos
			if ($handle = opendir(RUTA_DIRECTORIO_TEMPORAL)) {

				while (false !== ($file = readdir($handle))) {
					// Si es un archivo válido
					if ($file != "." && $file != ".." && $file != "index.html") {
						// Si pertenece a la notificación respectiva
						if (LibreriaGeneral::esAdjuntoDe($prefijo, $file)) {
							// Se intenta eliminar del directorio
							if (!unlink(RUTA_DIRECTORIO_TEMPORAL . $file)) {
								return false;
							}
						}
					}
				}
				closedir($handle);

				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Se elimina un archivo adjunto a la Notificación del directorio respectivo REVISAR !!!!!!!
	 */
	public function eliminarAdjunto() {
		// Se recibe el id de la notificación
		$id_notificacion = LibreriaGeneral::recoge('id_notificacion', 0);
		// se recibe el nombre del archivo adjunto
		$nombre_adjunto = LibreriaGeneral::recoge('nombre_adjunto');
		// se reciben las listas elegidas
		$listas_destino = LibreriaGeneral::recoge('listas_destino');

		// Se obtiene la info de la Notificacion
		$datos = $this->modelo->obtenerRegistro($id_notificacion);

		// Si existe el adjunto en el directorio respectivo
		if (is_file(RUTA_ADJUNTOS_NOTIFICACIONES . $nombre_adjunto)) {
			// Si se elimina el adjunto
			if (unlink(RUTA_ADJUNTOS_NOTIFICACIONES . $nombre_adjunto)) {
				$datos['mensaje'] = "Se elimin&oacute; el adjunto " . str_replace($id_notificacion . '__', '', $nombre_adjunto) . " con &eacute;xito.";
				$datos['tipo_mensaje'] = 1;
			} else {
				$datos['mensaje'] = "No se ha eliminado el adjunto " . str_replace($id_notificacion . '__', '', $nombre_adjunto) . ".";
				$datos['tipo_mensaje'] = 2;
			}
		}

		// Para mantener las listas elegidas
		// Se agrupan los IDs de las Listas Adicionales elegidas, separadas por coma, para guardarlos en el campo respectivo
		// Se les antecede el Id de la Lista 2, utilizada para el prefijo [Notificaciones HCD] del asunto de la campaña
		$datos['n_phplist_ids_destino'] = '2,' . implode(',', $listas_destino);

		// Se obtienen las Listas de Distribución
		$listas = $this->modelo->obtenerListas();

		$this->vista_edicion->mostrar($datos, $listas, $mensaje, $tipo_mensaje);
	}

	/**
	 * Se elimina un archivo temporal
	 */
	public function eliminarTemporal() {
		// Se recibe el id de la notificación
		$datos['n_id'] = LibreriaGeneral::recoge('n_id', 0);

		// Se recibe el prefijo
		$datos['prefijo'] = LibreriaGeneral::recoge('prefijo', 0);

		// Se recibe el resto de datos de la Notificación
		$datos['n_fecha'] = $this->convertirFechaToMySQL(LibreriaGeneral::recoge('fecha'));
		$datos['n_asunto'] = LibreriaGeneral::recoge('n_asunto');
		$datos['n_id_grupo_destino'] = LibreriaGeneral::recoge('n_id_grupo_destino');
		$datos['n_mensaje'] = LibreriaGeneral::recoge('n_mensaje');
		$datos['n_id_mail'] = LibreriaGeneral::recoge('n_id_mail');
		$datos['pagina'] = LibreriaGeneral::recoge('pagina');
		// Se recibe si se trata de una Fe de Erratas o no
		$datos['es_fe_erratas'] = LibreriaGeneral::recoge('es_fe_erratas', 0);

		// se recibe el nombre del archivo adjunto
		$nombre_temporal = LibreriaGeneral::recoge('nombre_temporal');

		// Si existe el adjunto en el directorio temporal/
		if (is_file(RUTA_DIRECTORIO_TEMPORAL . $nombre_temporal)) {
			// Si se elimina el archivo temporal
			if (unlink(RUTA_DIRECTORIO_TEMPORAL . $nombre_temporal)) {
				$this->mensaje = "Se elimin&oacute; el temporal " . str_replace($datos['prefijo'] . '__', '', $nombre_temporal) . " con &eacute;xito.";
				$this->tipo_mensaje = 1;
			} else {
				$this->mensaje = "No se ha eliminado el temporal " . str_replace($datos['prefijo'] . '__', '', $nombre_temporal) . ".";
				$this->tipo_mensaje = 2;
			}
		}

		// Para mantener las listas elegidas
		// Se agrupan los IDs de las Listas Adicionales elegidas, separadas por coma, para guardarlos en el campo respectivo
		// Se les antecede el Id de la Lista 2, utilizada para el prefijo [Notificaciones HCD] del asunto de la campaña
		//$datos['n_phplist_ids_destino'] = '2,' . implode(',', $listas_destino);

		// Se obtienen las Listas de Distribución
		$listas = $this->modelo->obtenerListas();

		$this->vista_edicion->mostrar($datos, $listas, $this->mensaje, $this->tipo_mensaje);
	}

	/**
	 * Se ingresa una notificacion
	 */
	public function insertar($datos) {
		if ($this->modelo->insertar($datos)) {
			$this->mensaje = "La Notificaci&oacute;n se ingres&oacute; con &eacute;xito.";
			$this->tipo_mensaje = 1;
		} else {
			$this->mensaje = "Error al ingresar la Notificaci&oacute;n.";
			$this->tipo_mensaje = 2;
		}
	}

	/**
	 * Se modifica una notificacion
	 */
	public function modificar($datos) {
		if ($this->modelo->modificar($datos)) {
			$this->mensaje = "La Notificaci&oacute;n se modific&oacute; con &eacute;xito.";
			$this->tipo_mensaje = 1;
		} else {
			$this->mensaje = "Error al modificar la Notificaci&oacute;n.";
			$this->tipo_mensaje = 2;
		}
	}

	/**
	 * Se elimina una notificacion determinada
	 */
	public function eliminar() {
		$id = LibreriaGeneral::recoge('id');

		// Si existe el directorio
		if (is_dir(RUTA_ADJUNTOS_NOTIFICACIONES)) {
			// Si pudo abrirse el directorio de los Adjuntos
			if ($handle = opendir(RUTA_ADJUNTOS_NOTIFICACIONES)) {

				while (false !== ($file = readdir($handle))) {
					// Si es un archivo válido
					if ($file != "." && $file != ".." && $file != "index.html") {
						// Si pertenece a la notificación respectiva
						if (LibreriaGeneral::esAdjuntoDe($id, $file)) {
							// Se elimina del directorio
							unlink(RUTA_ADJUNTOS_NOTIFICACIONES . $file);
						}
					}
				}
				closedir($handle);
			}
		}

		if ($this->modelo->eliminar($id)) {
			$this->listar('La Notificaci&oacute;n se ha dado de baja con &eacute;xito.', 1);
		} else {
			$this->listar('Error al eliminar la Notificaci&oacute;n.', 2);
		}

	}

	/**
	 * Se visualiza la vista previa de la Notificación
	 * @return [type] [description]
	 */
	public function verVistaPrevia($id_notificacion = '', $mensaje = '', $tipo_mensaje = '', $p_pagina = '') {
		// Se recibe el Id de la notificación
		$id = ($id_notificacion != '') ? $id_notificacion : LibreriaGeneral::recoge('id');

		// Se busca el registro de la Notificación
		$datos = $this->modelo->obtenerRegistro($id);

		// Si se ha enviado a listas de distribución
		if ($datos['n_phplist_ids_destino'] != null) {
			// Se separan los IDs de las listas que posee asignadas
			$id_listas = explode(',', $datos['n_phplist_ids_destino']);

			$cantidad_id_listas = (isset($id_listas)) ? count($id_listas) : 0;
			// Por cada Lista
			for ($l = 0; $l < $cantidad_id_listas; $l++) {
				// Si NO es la lista nro. 2
				if ($id_listas[$l] != 2) {
					// Se obtiene y asigna el Nombre de dicha lista
					$datos['nombre_lista'][$l] = $this->modelo->obtenerNombreLista($id_listas[$l]);
				}
			}
			$cantidad_id_listas = 0;
		}

		// Se recibe el número de página actual
		$datos['pagina'] = ($p_pagina != '') ? $p_pagina : LibreriaGeneral::recoge('pagina');

		// Se visualiza la VistaPrevia de la Notificación
		$this->vista_previa->mostrar($datos);
	}

	/**
	 * Se envia la Notificacion por mail
	 */
	public function enviarNotificacionPorMail() {

		$id = LibreriaGeneral::recoge('id');

		// Se obtiene el registro
		$registro = $this->modelo->obtenerRegistro($id);

		// Si existe
		if ($registro['n_id'] != '') {
			// Se habilita para enviarla por correo
			$registro['n_enviada'] = 1;

			if ($this->enviarMail($registro)) {

				// Se genera el archivo de texto utilizado como flag para la ejecución
				// del proceso de actualización de los adjuntos de las Notificaciones
				$archivo_txt = fopen(RUTA_RAIZ.'abms/procesar_notificacion.txt', 'w');
				// Se cierra el archivo de texto
				fclose($archivo_txt);

				// Se audita el ENVIO de la Notificación
				$this->modelo->auditarEnvioNotificacion($registro['n_id'], $registro['n_asunto']);

				$this->listar("la Notificaci&oacute;n se ha enviado por correo electr&oacute;nico con &eacute;xito.", 1);
			} else {
				$this->listar("la Notificaci&oacute;n no se ha podido enviar por correo electr&oacute;nico.", 2);
			}
		} else {
			$this->listar("No se ha encontrado la Notificaci&oacute;n.", 2);
		}
	}

	/**
	 * Se arma el contenido del mensaje del Mail
	 * @param  [array] 	   $notificacion 	Información de la Notificacion
	 * @return [string]    $contenido   	Cadena de texto con el contenido HTML a ser utilizado por la API de PHP List
	 */
	public function armarContenidoMail($notificacion) {
		$contenido = '';

		// Se convierte la fecha al formato yyyy-mm-dd, en caso que se reciba con formato dd/mm/yyyy
		$fecha_notificacion = (strpos($notificacion['n_fecha'], '-') === false) ? LibreriaGeneral::formatearFechaConGuiones($notificacion['n_fecha']) : $notificacion['n_fecha'];
		// Se obtiene la fecha en formato gregoriano
		$fecha_a_mostrar = LibreriaGeneral::obtenerNombreDia($fecha_notificacion) . ' ' . LibreriaGeneral::mostrarFechaLetras($fecha_notificacion);

		// Contenedor general (CSS .vista_previa_notificacion_cuerpo)
		$contenido .= '<div style="clear:both; background:#FFF; min-height:200px; font-size:13px;">';

		// (CSS .vista_previa_notificacion_fecha)
		$contenido .= '<div style="font-size: 14px;color: #666666;padding:5px 20px;text-align: left;">' . $fecha_a_mostrar . '</div>';

		// Texto (CSS .vista_previa_notificacion_texto)
		$contenido .= '<div style="padding: 20px;font-size: 13px;color: #666666;text-align: justify;">';
		$contenido .= '<p>';
		$contenido .= ($notificacion['n_mensaje'] != '') ? nl2br($notificacion['n_mensaje']) : '';
		$contenido .= '</p>';
		$contenido .= '</div>';

		// Si posee adjuntos
		if ($dir_abierto = opendir(RUTA_ADJUNTOS_NOTIFICACIONES)) {
			// Obtengo los archivos para generar una salida ordenada.
			$archivos = array();

			while (false !== ($f = readdir($dir_abierto))) {
				// Si es un adjunto válido
				if (preg_match('/^~/', $f) !== 1 && $f != '..' && $f != '.' && $f != "index.html") {
					// Si pertenece a la Notificación respectiva
					if (LibreriaGeneral::esAdjuntoDe($notificacion['n_id'], $f)) {
						$archivos[] = $f;
					}

				}
			}
			closedir($dir_abierto);

			// Ordeno de forma "natural", case-insensitive
			natcasesort($archivos);

			// Contenedor de los adjuntos que posea
			$contenido .= '<div style="clear: both;width: 100%;margin: 10px auto;padding-top: 10px;">';

			// Genero la salida
			foreach ($archivos as $archivo_adjunto) {
				// Se divide el nombre del adjunto, separando por los dos guiones bajos
				$partes = explode('__', $archivo_adjunto);
				// Nos quedamos con el nombre para mostrarlo en el mail
				$nombre_archivo_adjunto = $partes[1];

				$contenido .= '<p><a href="' . LISTA_CORREO_URL_NOTIFICACIONES_ADJUNTOS . $archivo_adjunto . '" target="_blank" >&nbsp;';
				$contenido .= $nombre_archivo_adjunto;
				$contenido .= '</a></p>';
			}

			// fin del contenedor de los adjuntos
			$contenido .= '</div>';
		}

		// fin del contenedor general
		$contenido .= '</div>';

		return $contenido;
	}

	/**
	 * Se envia el Mail a las listas
	 * @param  [type] $notificacion [description]
	 * @return [type]               [description]
	 */
	public function enviarMail($notificacion) {

		// Si está tildada la opción de enviar por mail la Notificacion
		if ($notificacion['n_enviada'] == 1) {

			$api = new PhpListRESTApiClient(LISTA_CORREO_API_URL, LISTA_CORREO_API_USER, LISTA_CORREO_API_PASSWORD);
			try {
				if ($api->login()) {

					$fecha_envio = new DateTime();
					$fecha_envio->add(new DateInterval('PT' . LISTA_CORREO_DELAY_ENVIO . 'M')); // envio en N minutos

					// Se arma el string con todo el HTML necesario para el contenido del mail a enviar.
					// Contiene:
					// Fecha + texto + adjuntos (en caso que posea)
					$contenido_para_mail = $this->armarContenidoMail($notificacion);

					$nueva_campana = $api->messageAdd(
						$notificacion['n_asunto'], // $subject
						LISTA_CORREO_REMITENTE, // $fromfield
						LISTA_CORREO_RESPONDER_A, // $replyto
						$contenido_para_mail, // $message      ex $notificacion['n_mensaje']
						$contenido_para_mail, // $textmessage  ex $notificacion['n_mensaje']
						LISTA_CORREO_HTML_PIE_NOTIFICACIONES, // $footer
						'submitted', // $status
						LISTA_CORREO_FORMATO_ENVIO, // $sendformat
						// 2020-06-12 XXXX
						// Se utiliza otra Plantilla de Id 2, ya que posee otra firma en su pie, no se deben desuscribir
						// (definida la constante en config/mail_config.php)
						LISTA_CORREO_ID_PLANTILLA_NOTIFICACIONES, // $template
						$fecha_envio->format('Y-m-d H:i'), // $embargo
						'', // $rsstemplate
						LISTA_CORREO_ID_OWNER, // $owner
						LISTA_CORREO_FORMATEADO_HTML// $htmlformatted
					);

					// El id que genera el PHPList cuando guarda un mensaje
					$id_mensaje_phplist = $nueva_campana->id; // 275 por ejemplo

					// el id del Grupo de Distribución elegido para la notificación
					$id_grupo = $notificacion['n_id_grupo_destino'];

					// 	Se obtiene la lista de grupos de distribución
					$lista_grupos = $this->modelo->obtenerListaGruposDistribucion();
					// Se busca el grupo
					$grupo = null;
					foreach ($lista_grupos as $g) {
						if ($g['id'] == $id_grupo) {
							$grupo = $g;
						}
					}

					// Se inicializa un array para unificar los Ids de las listas a agregar a la campaña
					$id_listas_a_agregar = array();

					// Si se encontró el Grupo
					if ($grupo) {
						// Se agregan a la lista para agregar a la campaña
						$id_listas_a_agregar[] = $grupo['phplist_ids'];
					}

					// 05/01/2022 XXXX
					// Se corrigió la asignación de las listas adicionales con las listas de un grupo determinado

					// Si se eligieron Listas Adicionales
					if (isset($notificacion['n_phplist_ids_destino']) && $notificacion['n_phplist_ids_destino'] != null) {

						// Si se ha elegido un Grupo
						if ($id_listas_a_agregar[0] != '') {
							// Se agregan al final, los IDs de las listas Adicionales,
							// a los IDs de las listas de dicho Grupo
							$id_listas_a_agregar = $id_listas_a_agregar[0].','.$notificacion['n_phplist_ids_destino'];
						} else {
							// Sino, se agregan solamente los IDs de las listas Adicionales
							$id_listas_a_agregar = $notificacion['n_phplist_ids_destino'];
						}

						// Se convierte el campo a un array de IDs, quitando los repetidos
						$listas = array_unique(explode(',', $id_listas_a_agregar));
					} else {
						// Se toman sólo los IDs de las listas del Grupo respectivo
						$listas = explode(',', $id_listas_a_agregar[0]);
					}

					// Por cada Lista
					foreach ($listas as $l) {
						// Se agrega la lista al mensaje
						$api->listMessageAdd($l, $id_mensaje_phplist);
					}

					// Se toma el Id de la campaña
					$notificacion['n_id_mail'] = $id_mensaje_phplist;

					// Se guarda dicho Id de campaña en la Notificacion
					return ($this->modelo->modificar($notificacion));

				} else {
					return false;
				}
			} catch (Exception $e) {
				return false;
			}
		} else {
			return true;
		}
	}

	/**
	 * Se visualizan los Suscriptores de un mail determinado
	 * @return [type] [description]
	 */
	public function verSuscriptores() {
		// Se recibe el Id del mail de PHPList
		$id_mensaje_phplist = LibreriaGeneral::recoge('id_mail');
		// Se recibe para volver a la página donde se estaba en la grilla
		$pagina = LibreriaGeneral::recoge('pagina');

		$api = new PhpListRESTApiClient(LISTA_CORREO_API_URL, LISTA_CORREO_API_USER, LISTA_CORREO_API_PASSWORD);

		try {
			if ($api->login()) {
				// Se obtienen los Suscriptores
				$listado = $api->messageGetViewStatus($id_mensaje_phplist);
			} else {
				return false;
			}

		} catch (Exception $e) {
			return false;
		}

		// Se audita la visualización de los Suscriptores de la Notificación
		$this->modelo->auditarVerSuscriptores($id_mensaje_phplist);

		// Se visualizan los Suscriptores de la Notificación
		$this->vista_suscriptores->mostrar($listado, $pagina);
	}

	/**
	 * Se genera el PDF de la Notificación
	 */
	public function generarPdf() {
		// Se recibe el Id de la notificación
		$id = LibreriaGeneral::recoge('id');

		// Se busca el registro de la Notificación
		$datos = $this->modelo->obtenerRegistro($id);

		// Si se ha enviado a listas de distribución
		if ($datos['n_phplist_ids_destino'] != null) {
			// Se separan los IDs de las listas que posee asignadas
			$id_listas = explode(',', $datos['n_phplist_ids_destino']);

			$cantidad_id_listas = (isset($id_listas)) ? count($id_listas) : 0;
			// Por cada Lista
			for ($l = 0; $l < $cantidad_id_listas; $l++) {
				// Si NO es la lista nro. 2
				if ($id_listas[$l] != 2) {
					// Se obtiene y asigna el Nombre de dicha lista
					$datos['nombre_lista'][$l] = $this->modelo->obtenerNombreLista($id_listas[$l]);
				}
			}
			$cantidad_id_listas = 0;
		}

		// Se recibe el número de página actual
		$datos['pagina'] = LibreriaGeneral::recoge('pagina');

		// Se visualiza la VistaPrevia de la Notificación
		$this->vista_pdf->mostrar($datos);
	}

}
?>

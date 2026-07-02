<?php
if (!isset($_SESSION)) {
	session_start();
}

//Incluye el modelo que corresponde
require_once RUTA_MODELOS . "opendata_datasets.php";
require_once RUTA_MODELOS . "opendata_catalogos.php";
require_once RUTA_MODELOS . "opendata_publicadores.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "opendata_datasets/grilla.php";
require_once RUTA_VISTAS . "opendata_datasets/edicion.php";

class opendata_datasets_controller extends ControllerBase {
	public function __construct() {
		parent::__construct();

		$this->campo_orden_por_defecto = 'fecha_emitido';

		// Se crea una instancia del modelo
		$this->modelo = new opendataDatasetsModel();
		$this->modeloCatalogo = new opendataCatalogosModel();
		$this->modeloPublicador = new opendataPublicadoresModel();

		// Se crea una instancia de cada Vista
		$this->vista_grilla = new VistaOpendataDatasetsGrilla();
		$this->vista_edicion = new VistaOpendataDatasetsEdicion();

		// Se inicializa el mensaje de resultados
		$this->mensaje = "";

		$this->error_al_cargar_adjuntos = false;
	}

	public function guardarRegistroOriginal($original) {

		$_SESSION['id_original'] = $original['id'];
		$_SESSION['titulo_original'] = $original['titulo'];
		$_SESSION['descripcion_original'] = $original['descripcion'];
		$_SESSION['fecha_emitido_original'] = $original['fecha_emitido'];
		$_SESSION['fecha_modificado_original'] = $original['fecha_modificado'];
		$_SESSION['id_catalogo_original'] = $original['id_catalogo'];
		$_SESSION['id_publicador_original'] = $original['id_publicador'];
		$_SESSION['identificador_original'] = $original['identificador'];
		$_SESSION['palabras_clave_original'] = $original['palabras_clave'];
		$_SESSION['lenguaje_original'] = $original['lenguaje'];
		$_SESSION['frecuencia_original'] = $original['frecuencia'];
		$_SESSION['url_original'] = $original['url'];
		$_SESSION['licencia_original'] = $original['licencia'];
		$_SESSION['fuente_original'] = $original['fuente'];
		$_SESSION['nivel_acceso_original'] = $original['nivel_acceso'];
		$_SESSION['habilitado_original'] = $original['habilitado'];
	}

	public function listar($mensaje = '', $tipo_mensaje = '') {
		$filtro = Array();

		// FILTRO POR FECHA
		$f_fecha = LibreriaGeneral::recoge('f_fecha');
		$filtro['f_fecha'] = (isset($f_fecha) && $this->esFechaValida($f_fecha)) ? $this->modelo->formatearFechaMySQL($f_fecha) : '';

		// FILTRO POR TITULO
		$filtro['f_titulo'] = LibreriaGeneral::recoge('f_titulo');

		// FILTRO POR CONTENIDO
		$filtro['f_descripcion'] = LibreriaGeneral::recoge('f_descripcion');

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

		// Si se desconoce el valor de la pagina
		if ($filtro['pagina'] == '') {
			$filtro['inicio'] = 0; // se inicia en el primer registro
			$filtro['pagina'] = 1; // en la primer pagina
			// si no se busca
		} elseif ($filtro['f_nombre'] == '') {
			// se calcula el valor del registro inicial de la pagina deseada
			$filtro['inicio'] = ($filtro['pagina'] - 1) * $filtro['rango'];
		}

		$filtro['pagina_ant'] = $filtro['pagina'] - 1; //para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1; //para la pagina posterior

		//Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);

		// Se obtiene la cantidad total, segun el filtro, para calcular el nro. de paginas en la Vista
		$filtro['cantidad'] = $this->modelo->obtenerCantidad();

		//NUMERO TOTAL DE PAGINAS
		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		// SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
		$_SESSION['filtro_opendata_datasets'] = $filtro;

		//Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);

		// SE OBTIENE EL LISTADO
		$listado = $this->modelo->listar();

		if (empty($mensaje) && empty($tipo_mensaje)) {
			$mensaje = (isset($_SESSION['mensaje'])) ? $_SESSION['mensaje'] : '';
			$tipo_mensaje = (isset($_SESSION['tipo_mensaje'])) ? $_SESSION['tipo_mensaje'] : '';
		}

		// se muestra el listado
		$this->vista_grilla->mostrar($listado, $mensaje, $tipo_mensaje, $filtro);
	}

	public function editar($datos_formulario = null, $mensaje = '', $tipo_mensaje = '') {

		// Si NO se viene del formulario de edición por un error
		if ($datos_formulario === null) {
			// Se recibe el Id para su edición
			$id = LibreriaGeneral::recoge('id', 0);

			// Se busca el registro en la base de datos
			$datos = $this->modelo->obtenerRegistro($id);

			// Si existe
			if ($datos['id']) {
				// Se audita la consulta del registro
				//$this->modelo->auditarConsultaRegistro($datos);

				// Se guarda el registro en sesion para verificar luego si no ha modificado otro usuario
				$this->guardarRegistroOriginal($datos);

				// Se marca para saber que se encuentra en edición
				//$this->modelo->marcarEnEdicion($datos['id']);

				$datos['pagina'] = LibreriaGeneral::recoge('pagina', 1);

				$datos = $this->retirarBarraInvertida($datos);

				// Se obtienen los recursos del dataset
				$datos['recursos'] = $this->modelo->obtenerRecursos($datos['id']);
			} else {
				// En caso de editarse un NUEVO registro
				$datos = null;
			}

		} else {
			// SI SE VIENE DEL FORMULARIO DEBIDO A UN ERROR
			$datos = $datos_formulario;
		}

		// Se obtienen los catálogos y Publicadores habilitados
		$catalogos = $this->modeloCatalogo->obtenerHabilitados();
		$publicadores = $this->modeloPublicador->obtenerHabilitados();

		$this->vista_edicion->mostrar($datos, $catalogos, $publicadores, $mensaje, $tipo_mensaje);
	}

	public function seguirEditando() {

		$datos = Array();
		// Se toman los datos para seguir editando
		$datos = $_SESSION['administracion'];

		// Se elimina la información guardada en sesión
		unset($_SESSION['administracion']);

		// Se formatea la fecha recibida como yyyy-mm-dd para seguir editando en la Vista
		$datos['fecha_emitido'] = ($datos['fecha_emitido'] != '') ? LibreriaGeneral::formatearFechaConGuiones($datos['fecha_emitido']) : '';
		$datos['fecha_modificado'] = ($datos['fecha_modificado'] != '') ? LibreriaGeneral::formatearFechaConGuiones($datos['fecha_modificado']) : '';

		// Se obtienen los recursos del dataset
		$datos['recursos'] = $this->modelo->obtenerRecursos($datos['id']);

		// Se obtienen los catálogos y Publicadores habilitados
		$catalogos = $this->modeloCatalogo->obtenerHabilitados();
		$publicadores = $this->modeloPublicador->obtenerHabilitados();

		$this->vista_edicion->mostrar($datos, $catalogos, $publicadores, $mensaje, $tipo_mensaje);
	}

	/**
	 * Se suben en temporal/ los archivos elegidos
	 */
	public function subirEnTemporal() {
		// Se recibe la info
		$datos = $_REQUEST;
		// Se reciben los archivos adjuntos
		$info_de_archivos = $_FILES['adjuntos'];
		//LibreriaGeneral::registrarLog("info_de_archivos", $info_de_archivos);

		// Si se recibieron los datos del DataSet con los adjuntos para cargar
		if (isset($datos) && isset($info_de_archivos['name'][0]) && $info_de_archivos['name'][0] != '') {

			// Se intenta subir cada archivo recibido
			foreach ($info_de_archivos['name'] as $f => $nombre_archivo) {

				//LibreriaGeneral::registrarLog("size_archivos", $info_de_archivos['size'][$f]);

				// Se toma la extensión del archivo y se convierte a minúscula
				$extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));

				// Si su extensión no es válida
				if (!in_array($extension, $this->extensiones_opendata_validas)) {
					$this->mensaje = "La extensi&oacute;n de " . $nombre_archivo . " no es v&aacute;lida.";
					$this->tipo_mensaje = 2;
				} else {
					// Si su tamaño supera el permitido
					if ($info_de_archivos['size'][$f] != '' && $info_de_archivos['size'][$f] > TAMANIO_MAXIMO_FOTO) {
						$this->mensaje = "El tama&ntilde;o del archivo (" . $info_de_archivos['size'][$f] . ") <b>es mayor al permitido</b>.";
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
			}
			// Si no surgió un error
			if ($this->tipo_mensaje != 2) {
				$this->mensaje = "Se ha realizado la carga <strong>temporal</strong> de " . $nro_archivos_subidos . " archivo/s satisfactoriamente!";
				$this->tipo_mensaje = 1;
			}
		} else {
			$this->mensaje = "No se han recibido datos para la carga de recursos.";
			$this->tipo_mensaje = 2;
		}

		// Se formatean las fechas recibidas como yyyy-mm-dd para seguir editando en la Vista
		$datos['fecha_emitido'] = ($datos['fecha_emitido'] != '') ? LibreriaGeneral::formatearFechaConGuiones($datos['fecha_emitido']) : '';
		$datos['fecha_modificado'] = ($datos['fecha_modificado'] != '') ? LibreriaGeneral::formatearFechaConGuiones($datos['fecha_modificado']) : '';

		// Se obtienen los recursos del dataset
		$datos['recursos'] = $this->modelo->obtenerRecursos($datos['id']);

		// Se obtienen los catálogos y Publicadores habilitados
		$catalogos = $this->modeloCatalogo->obtenerHabilitados();
		$publicadores = $this->modeloPublicador->obtenerHabilitados();

		$this->vista_edicion->mostrar($datos, $catalogos, $publicadores, $this->mensaje, $this->tipo_mensaje);
	}

	/**
	 * Se cancela la edicion del DataSet determinado
	 * en caso que se hayan cargado recursos temporales, se eliminan
	 * y se vuelve a la grilla.
	 */
	public function cancelarEdicion() {
		// Se recibe el prefijo
		$prefijo = LibreriaGeneral::recoge('prefijo', 0);

		// En caso que posea recursos temporales, se eliminan
		if (!$this->eliminarTemporales($prefijo)) {
			$this->listar("No se han eliminado los temporales", 2);
		}

		$this->listar();
	}

	/**
	 * Se guarda el DataSet
	 */
	public function guardar() {

		$datos = $_REQUEST; // Se recibe la info

		// Si ya existe
		if ($datos['id'] != '') {
			// Se modifica en la DB
			$this->modificar($datos);
		} else {
			// Se ingresa en la DB
			$this->insertar($datos);
			// Se obtiene el Id recién registrado
			$datos['id'] = $this->modelo->obtenerUltimoId();
		}

		// Se copian los adjuntos temporales al directorio correspondiente (en caso que posea)
		if ($this->pasarAdjuntos($datos['prefijo'], $datos['id'])) {
			// Se eliminan los archivos del directorio temporal
			$this->eliminarTemporales($datos['prefijo']);
		}

		$this->mensaje = "Se ha guardado el dataset con &eacute;xito.";
		$this->tipo_mensaje = 1;

		$this->listar($this->mensaje, $this->tipo_mensaje);
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
						// Si pertenece al DataSet respectivo
						if (LibreriaGeneral::esAdjuntoDe($prefijo, $file)) {
							// Se divide el nombre del temporal
							$aux = explode('__', $file);
							// Se agrega como prefijo el id en el nombre del adjunto
							$nombre_adjunto = LibreriaGeneral::quitarAcentos($aux[1]);

							// Directorio específico del Dataset, su nombre es su Id
							$directorio_destino = RUTA_DATASET_RECURSOS . $id . "/";

							// Si no existe el directorio
							if (!is_dir($directorio_destino)) {
								$permisos = '777';
								$permisos = octdec(str_pad($permisos, 4, '0', STR_PAD_LEFT));

								@mkdir($directorio_destino, $permisos); // Se crea
								@chmod($directorio_destino, $permisos); // Se le da permisos
							}

							// Se intenta copiar el archivo al directorio destino
							if (!copy(RUTA_DIRECTORIO_TEMPORAL . $file, $directorio_destino . $nombre_adjunto)) {
								return false;
							} else {
								// Se ingresa la info del recurso en la DB
								$this->modelo->insertarRecurso($id, $nombre_adjunto);
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
	 * Se eliminan los adjuntos temporales de un DataSet determinado
	 * @param  [integer] $prefijo 	Para identificar el DataSet
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
						// Si pertenece al DataSet respectivo
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
	 * Se elimina un recurso del DataSet del directorio respectivo
	 */
	public function eliminarRecurso() {

		// Se recibe el id del Recurso
		$id = LibreriaGeneral::recoge('id', 0);

		// Se recibe el id del DataSet
		$id_dataset = LibreriaGeneral::recoge('id_dataset', 0);

		// se recibe el nombre del recurso
		$nombre_adjunto = LibreriaGeneral::recoge('nombre_adjunto');

		// Se obtiene la info del DataSet
		$datos = $this->modelo->obtenerRegistro($id_dataset);

		// Si existe el recurso en el directorio respectivo
		if (is_file(RUTA_DATASET_RECURSOS . $id_dataset . "/" . $nombre_adjunto)) {

			// Si se elimina el recurso (en la DB y físicamente)
			if ($this->modelo->eliminarRecurso($id, $id_dataset) && unlink(RUTA_DATASET_RECURSOS . $id_dataset . "/" . $nombre_adjunto)) {

				$this->mensaje = "Se elimin&oacute; el recurso " . $nombre_adjunto . " con &eacute;xito.";
				$this->tipo_mensaje = 1;
			} else {
				$this->mensaje = "No se ha eliminado el recurso " . $nombre_adjunto . ".";
				$this->tipo_mensaje = 2;
			}
		}

		// Se obtienen los recursos del dataset
		$datos['recursos'] = $this->modelo->obtenerRecursos($id_dataset);

		// Se obtienen los catálogos y Publicadores habilitados
		$catalogos = $this->modeloCatalogo->obtenerHabilitados();
		$publicadores = $this->modeloPublicador->obtenerHabilitados();

		// Se vuelve para seguir editando el DataSet
		$this->vista_edicion->mostrar($datos, $catalogos, $publicadores, $this->mensaje, $this->tipo_mensaje);
	}

	/**
	 * Se elimina un archivo temporal
	 */
	public function eliminarTemporal() {

		// Se recibe el id del DataSet
		$datos['id'] = LibreriaGeneral::recoge('id_dataset', 0);

		// Se recibe el prefijo
		$datos['prefijo'] = LibreriaGeneral::recoge('prefijo', 0);

		// Se recibe el resto de datos del DataSet
		$datos['titulo'] = LibreriaGeneral::recoge('titulo');
		$datos['descripcion'] = LibreriaGeneral::recoge('descripcion');
		$datos['id_catalogo'] = LibreriaGeneral::recoge('id_catalogo');
		$datos['id_publicador'] = LibreriaGeneral::recoge('id_publicador');
		$datos['fecha_emitido'] = LibreriaGeneral::formatearFechaConGuiones(LibreriaGeneral::recoge('fecha_emitido'));
		$datos['fecha_modificado'] = LibreriaGeneral::formatearFechaConGuiones(LibreriaGeneral::recoge('fecha_modificado'));
		$datos['identificador'] = LibreriaGeneral::recoge('identificador');
		$datos['palabras_clave'] = LibreriaGeneral::recoge('palabras_clave');
		$datos['lenguaje'] = LibreriaGeneral::recoge('lenguaje');
		$datos['frecuencia'] = LibreriaGeneral::recoge('frecuencia');
		$datos['url'] = LibreriaGeneral::recoge('url');
		$datos['licencia'] = LibreriaGeneral::recoge('licencia');
		$datos['fuente'] = LibreriaGeneral::recoge('fuente');
		$datos['nivel_acceso'] = LibreriaGeneral::recoge('nivel_acceso');
		$datos['pagina'] = LibreriaGeneral::recoge('pagina');

		// se recibe el nombre del archivo adjunto
		$nombre_temporal = LibreriaGeneral::recoge('nombre_temporal');

		// Si existe el adjunto en el directorio temporal/
		if (is_file(RUTA_DIRECTORIO_TEMPORAL . $nombre_temporal)) {
			// Si se elimina el archivo temporal
			if (unlink(RUTA_DIRECTORIO_TEMPORAL . $nombre_temporal)) {
				$this->mensaje = "Se elimin&oacute; el <strong>temporal</strong> " . str_replace($datos['prefijo'] . '__', '', $nombre_temporal) . " con &eacute;xito.";
				$this->tipo_mensaje = 1;
			} else {
				$this->mensaje = "No se ha eliminado el <strong>temporal</strong> " . str_replace($datos['prefijo'] . '__', '', $nombre_temporal) . ".";
				$this->tipo_mensaje = 2;
			}
		}

		// Se obtienen los recursos del dataset
		$datos['recursos'] = $this->modelo->obtenerRecursos($datos['id']);

		// Se obtienen los catálogos y Publicadores habilitados
		$catalogos = $this->modeloCatalogo->obtenerHabilitados();
		$publicadores = $this->modeloPublicador->obtenerHabilitados();

		// Se vuelve para seguir editando el DataSet
		$this->vista_edicion->mostrar($datos, $catalogos, $publicadores, $this->mensaje, $this->tipo_mensaje);
	}

	/**
	 * Se ingresa
	 */
	public function insertar($datos) {

		if ($this->modelo->insertar($datos)) {
			$this->mensaje = "El Dataset se ingres&oacute; con &eacute;xito.";
			$this->tipo_mensaje = 1;
		} else {
			$this->mensaje = "Error al ingresar el Dataset.";
			$this->tipo_mensaje = 2;
		}
	}

	/**
	 * Se modifica
	 */
	public function modificar($datos) {

		if ($this->modelo->modificar($datos)) {
			$this->mensaje = "El Dataset se modific&oacute; con &eacute;xito.";
			$this->tipo_mensaje = 1;
		} else {
			$this->mensaje = "Error al modificar el Dataset.";
			$this->tipo_mensaje = 2;
		}
	}

	/**
	 * Se elimina
	 */
	public function eliminar() {
		parent::eliminarBase();
	}

	/**
	 * Se modifica el estado Habilitado|Deshabilitado
	 */
	public function modificarEstado() {
		parent::modificarEstadoBase();
	}
}
?>

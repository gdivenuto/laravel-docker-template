<?php
if (!isset($_SESSION)) {
	session_start();
}

//Incluye el modelo que corresponde
require_once RUTA_MODELOS . "carousel.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "carousel/grilla.php";
require_once RUTA_VISTAS . "carousel/edicion.php";

class carousel_controller extends ControllerBase {

	// private $perfiles_permitidos_para_modificar_estado_es_actividad;
	// private $perfiles_permitidos_para_subir_prioridad;
	// private $perfiles_permitidos_para_bajar_prioridad;

	public function __construct() {
		parent::__construct();

		$this->campo_orden_por_defecto = 'prioridad';

		// Se crea una instancia del modelo
		$this->modelo = new carouselModel();

		// Se crea una instancia de la Vista
		$this->vista_grilla = new VistaCarouselGrilla();
		$this->vista_edicion = new VistaCarouselEdicion();

		// $this->perfiles_permitidos_para_modificar_estado_es_actividad = array(1, 2);
		// $this->perfiles_permitidos_para_subir_prioridad = array(1, 2);
		// $this->perfiles_permitidos_para_bajar_prioridad = array(1, 2);
	}

	public function guardarRegistroOriginal($original) {
		$_SESSION['id_original'] = $original['id'];
		$_SESSION['fecha_original'] = $original['fecha'];
		$_SESSION['recurso_original'] = $original['recurso'];
		$_SESSION['enlace_original'] = $original['enlace'];
		$_SESSION['es_actividad_original'] = $original['es_actividad'];
		$_SESSION['prioridad_original'] = $original['prioridad'];
		$_SESSION['habilitado_original'] = $original['habilitado'];
	}

	public function listar($mensaje = '', $tipo_mensaje = '', $p_pagina = '') {

		// Primero se comprueba si el perfil actual posee permiso para ejecutar la acción respectiva
		//$this->comprobarAcceso($this->perfiles_permitidos_para_listar, $_SESSION['perfil1']);

		$filtro = Array();

		$fecha = LibreriaGeneral::recoge('f_fecha');
		if (isset($fecha) && $this->esFechaValida($fecha)) {
			$filtro['f_fecha'] = $this->modelo->formatearFechaMySQL($fecha);
		}

		// Cantidad de registros a mostrar
		$filtro['rango'] = $this->rango_paginacion;

		// Se obtiene el valor de la pagina
		$filtro['pagina'] = ($p_pagina == '') ? LibreriaGeneral::recoge('pagina', 1) : $p_pagina;
		
		if (! $filtro['pagina']) {
			// al comienzo no se sabe el valor de la pagina
			$filtro['inicio'] = 0; //por lo tanto se inicia en el primer registro
			$filtro['pagina'] = 1; //con la primer pagina
		} else
		// si no se busca
		if ($filtro['valor_buscado'] == '') {
			// se calcula el valor del registro inicial de la pagina deseada
			$filtro['inicio'] = ($filtro['pagina'] - 1) * $filtro['rango'];
		}

		$filtro['pagina_ant'] = $filtro['pagina'] - 1; //para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1; //para la pagina posterior

		//Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);

		//Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
		$filtro['cantidad'] = $this->modelo->obtenerCantidad();

		//NUMERO TOTAL DE PAGINAS
		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		//Se establece nuevamente el filtro en el modelo
		$this->modelo->setFiltro($filtro);

		//Se le pide al modelo todos los items
		$datos['info'] = $this->modelo->listar();

		$this->vista_grilla->mostrar($datos, $mensaje, $tipo_mensaje, $filtro);
	}

	public function editar($datos_formulario = null, $mensaje = '', $tipo_mensaje = '') {

		// Primero se comprueba si el perfil actual posee permiso para ejecutar la acción respectiva
		//$this->comprobarAcceso($this->perfiles_permitidos_para_editar, $_SESSION['perfil1']);

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

				$datos['pagina'] = LibreriaGeneral::recoge('pagina', 1);

				$datos = $this->retirarBarraInvertida($datos);
			} else {
				// En caso de editarse un NUEVO registro
				$datos = null;
			}
		} else {
			// Si se viene del formulario debido a un error
			$datos_formulario['fecha'] = ($datos_formulario['fecha'] != '') ? $this->modelo->formatearFechaMySQL($datos_formulario['fecha']) : '';

			// SI SE VIENE DEL FORMULARIO DEBIDO A UN ERROR
			$datos = $datos_formulario;
		}

		// SE MUESTRA EL FORMULARIO DE EDICION
		$this->vista_edicion->mostrar($datos, $mensaje, $tipo_mensaje);
	}

	/**
	 * Se sube en temporal/ la foto elegida
	 */
	public function subirEnTemporal() {

		// Se recibe la info
		$datos = $_REQUEST;

		// Se reciben el archivo de la foto
		$info_foto = $_FILES['foto'];

		// Si no se recibió el archivo
		if ($info_foto['error'] == 4) {
			$this->mensaje = "No se ha recibido el archivo de la foto.";
			$this->tipo_mensaje = 2;
		} else {
			// Si se recibieron los datos del registro, con la foto a cargar sin errores
			if (isset($datos) && isset($info_foto['name']) && $info_foto['name'] != '' && $info_foto['error'] == 0) {

				$nombre_archivo = $info_foto['name'];

				// Se toma la extensión del archivo y se convierte a minúscula
				$extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));

				// Si su extensión no es válida
				if (!in_array($extension, $this->extensiones_fotos_permitidas)) {
					$this->mensaje = "La extensi&oacute;n de la foto no es v&aacute;lida.";
					$this->tipo_mensaje = 2;
				} else {
					// Archivo
					$archivo_a_guardar = $info_foto['tmp_name'];

					// Se eliminan los espacios vacíos que contenga el nombre del archivo
					// se convierte a minúsculas
					// se coloca el prefijo definido en la Vista
					$nombre_archivo_a_guardar = $datos['prefijo'] . '__' . mb_strtolower(LibreriaGeneral::reemplazarEspaciosPorGuionesBajos(LibreriaGeneral::quitarAcentos($nombre_archivo)));

					// Se obtiene información del archivo recibido
					$info_imagen = @getimagesize($archivo_a_guardar);
					
					// Si el archivo es una imagen
					if ( $info_imagen ) {
						
						// Se crea una nueva
						if ( $info_imagen[2]==1 )
							$img = @imagecreatefromgif($archivo_a_guardar);

						if ( $info_imagen[2]==2 )
							$img = @imagecreatefromjpeg($archivo_a_guardar);

						if ( $info_imagen[2]==3 )
							$img = @imagecreatefrompng($archivo_a_guardar);
						
						$ancho_original = $info_imagen[0];// Ancho de la imagen recibida
						$alto_original = $info_imagen[1];// Alto de la imagen recibida
						
						// Si el ancho es menor al alto (si la foto es vertical)
						if ( $ancho_original < $alto_original ) {
							$this->mensaje = "La foto no es v&aacute;lida para el Carousel, su ancho es menor a su alto.";
							$this->tipo_mensaje = 2;
						} else {
							// Se fija el ancho de la imagen
							$ancho_final = ANCHO_IMAGEN_CAROUSEL;
							// Se calcula la proporción del alto de la imagen
							$alto_final = ($ancho_final / $ancho_original) * $alto_original;
												
							// Crea una imagen que representa una imagen en negro del tamaño especificado
							$imagen_redimensionada = imagecreatetruecolor($ancho_final, $alto_final);
							
							// Copia y cambia el tamaño de parte de la imagen redimensionándola
							imagecopyresampled($imagen_redimensionada, $img, 0, 0, 0, 0, $ancho_final, $alto_final, $info_imagen[0], $info_imagen[1]);

							// Se arma la ruta destino: directorio + nombre de archivo
							$ruta_destino_completa = RUTA_DIRECTORIO_TEMPORAL . $nombre_archivo_a_guardar;

							if ( $info_imagen[2]==1 )
								@imagegif($imagen_redimensionada, $ruta_destino_completa);

							if ( $info_imagen[2]==2 )
								@imagejpeg($imagen_redimensionada, $ruta_destino_completa);

							if ( $info_imagen[2]==3 )
								@imagepng($imagen_redimensionada, $ruta_destino_completa);

							imagedestroy($imagen_redimensionada);

							$this->mensaje = "Se ha realizado la carga <strong>temporal</strong> de la foto satisfactoriamente, debe <strong>Guardar</strong> para finalizar con la edici&oacute;n.";
							$this->tipo_mensaje = 1;
						}
					}
				}
			} else {
				$this->mensaje = "No se ha recibido una foto.";
				$this->tipo_mensaje = 2;
			}
		}

		// Se formatea la fechas recibida como yyyy-mm-dd para seguir editando en la Vista
		$datos['fecha'] = ($datos['fecha'] != '') ? LibreriaGeneral::formatearFechaConGuiones($datos['fecha']) : '';

		$this->vista_edicion->mostrar($datos, $this->mensaje, $this->tipo_mensaje);
	}

	/**
	 * Se cancela la edicion del registro determinado
	 * en caso que se hayan cargado recursos temporales, se eliminan
	 * y se vuelve a la grilla.
	 */
	public function cancelarEdicion() {
		// Se recibe el prefijo
		$prefijo = LibreriaGeneral::recoge('prefijo', 0);

		// En caso que posea recursos temporales, se eliminan
		if (!$this->eliminarTemporales($prefijo)) {
			$this->listar("No se ha eliminado la foto temporal", 2);
		}

		$this->listar();
	}

	/**
	 * Se guarda el registro
	 */
	public function guardar() {

		$datos = $_REQUEST; // Se recibe la info

		// Si ya existe
		if ($datos['id'] != '') {

			// Se modifica en la DB
			$this->modificar($datos);

			// Se recibe la info del video a subir
			$info_video = $_FILES['video'];

			// Si se recibió el video para cargar
			if (isset($info_video['name'][0]) && $info_video['name'][0] != '') {
				// Se carga el video (en el directorio y su nombre en la DB)
				$this->cargarVideo($datos, $info_video);
			}
		} else {
			// Se ingresa en la DB
			$this->insertar($datos);
			// Se obtiene el Id recién registrado
			$datos['id'] = $this->modelo->obtenerUltimoId();
		}

		// Se copia la foto temporal al directorio correspondiente (en caso que posea)
		if ($this->moverFotoTemporal($datos['prefijo'], $datos['id'])) {
			// Se elimina la foto temporal
			$this->eliminarTemporales($datos['prefijo']);
		}

		// Se vuelve a la grilla
		$this->listar($this->mensaje, $this->tipo_mensaje);
	}

	/**
	 * Se pasan los adjuntos al directorio correspondiente
	 * @param  [integer] $prefijo 	Para identificar la Notificación
	 * @param  [integer] $id 		Identificador de la Notificación
	 * @return [boolean]
	 */
	public function moverFotoTemporal($prefijo, $id) {

		// Si existe el directorio
		if (is_dir(RUTA_DIRECTORIO_TEMPORAL)) {
			// Si pudo abrirse el directorio de los Adjuntos
			if ($handle = opendir(RUTA_DIRECTORIO_TEMPORAL)) {
				// Mientras encuentre un archivo
				while (false !== ($file = readdir($handle))) {
					// Si es un archivo válido
					if ($file != "." && $file != ".." && $file != "index.html") {
						// Si pertenece al producto respectivo
						if (LibreriaGeneral::esAdjuntoDe($prefijo, $file)) {
							// Se toma la extensión del archivo y se convierte a minúscula
							$extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

							// Directorio final de la foto, su nombre es el Id del producto
							$ruta_final_foto = RUTA_RECURSOS_CAROUSEL . $id . "_foto." . $extension;

							// Se intenta copiar el archivo al directorio destino
							if (!copy(RUTA_DIRECTORIO_TEMPORAL . $file, $ruta_final_foto)) {
								return false;
							} else {
								// Se ingresa el nombre de la foto en la DB
								$this->modelo->ingresarNombreRecurso($id, $id . "_foto." . $extension);
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
	 * Se eliminan la foto temporal de un producto determinado
	 * @param  [integer] $prefijo 	Para identificar el producto
	 * @return [boolean]
	 */
	public function eliminarTemporales($prefijo) {

		// Si existe el directorio
		if (is_dir(RUTA_DIRECTORIO_TEMPORAL)) {
			// Si pudo abrirse el directorio temporal
			if ($handle = opendir(RUTA_DIRECTORIO_TEMPORAL)) {

				while (false !== ($file = readdir($handle))) {
					// Si es un archivo válido
					if ($file != "." && $file != ".." && $file != "index.html") {
						// Se intenta eliminar del directorio temporal
						if (!unlink(RUTA_DIRECTORIO_TEMPORAL . $file)) {
							return false;
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
	 * Se elimina una foto temporal
	 */
	public function eliminarTemporal() {

		// se recibe el nombre del archivo temporal
		$nombre_temporal = LibreriaGeneral::recoge('nombre_temporal');

		// Si existe el temporal en el directorio temporal/
		if (is_file(RUTA_DIRECTORIO_TEMPORAL . $nombre_temporal)) {
			// Si se elimina el archivo temporal
			if (unlink(RUTA_DIRECTORIO_TEMPORAL . $nombre_temporal)) {
				$this->mensaje = "Se elimin&oacute; la foto <strong>temporal</strong> con &eacute;xito.";
				$this->tipo_mensaje = 1;
			} else {
				$this->mensaje = "No se ha eliminado la foto <strong>temporal</strong>.";
				$this->tipo_mensaje = 2;
			}
		}

		$datos['prefijo'] = ''; // Se limpia

		// Se recibe el resto de datos del producto
		$datos['id'] = LibreriaGeneral::recoge('id', 0);
		//$datos['fecha'] = LibreriaGeneral::recoge('fecha');
		$datos['recurso'] = LibreriaGeneral::recoge('recurso');
		$datos['enlace'] = LibreriaGeneral::recoge('enlace');
		$datos['pagina'] = LibreriaGeneral::recoge('pagina');

		// Se formatean las fechas recibidas como yyyy-mm-dd para seguir editando en la Vista
		//$datos['fecha'] = ($datos['fecha'] != '') ? LibreriaGeneral::formatearFechaConGuiones($datos['fecha']) : '';

		// Se redirecciona al formulario de edición
		$this->vista_edicion->mostrar($datos, $this->mensaje, $this->tipo_mensaje);
	}

	/**
	 * Se elimina la foto del directorio respectivo
	 */
	public function eliminarFoto() {

		// Se recibe el id
		$id = LibreriaGeneral::recoge('id', 0);

		// Se obtiene la info
		$info = $this->modelo->obtenerRegistro($id);

		// Si existe la foto en el directorio respectivo
		if (is_file(RUTA_RECURSOS_CAROUSEL . $info['recurso'])) {

			// Si se elimina la foto (en la DB y físicamente)
			if ($this->modelo->eliminarFoto($id) && unlink(RUTA_RECURSOS_CAROUSEL . $info['recurso'])) {

				$mensaje = "Se elimin&oacute; la foto con &eacute;xito.";
				$tipo_mensaje = 1;
			} else {
				$mensaje = "No se ha eliminado la foto.";
				$tipo_mensaje = 2;
			}
		}

		// Se obtiene la info nuevamente
		$datos = $this->modelo->obtenerRegistro($id);

		// Se vuelve para seguir editando
		$this->vista_edicion->mostrar($datos, $mensaje, $tipo_mensaje);
	}

	/**
	 * Se ingresa
	 */
	public function insertar($datos) {

		if ($this->modelo->insertar($datos)) {
			$this->mensaje = "El registro se ingres&oacute; con &eacute;xito.";
			$this->tipo_mensaje = 1;
		} else {
			$this->mensaje = "Error al ingresar el registro.";
			$this->tipo_mensaje = 2;
		}
	}

	/**
	 * Se modifica un registro determinado
	 */
	public function modificar($datos) {

		if ($this->modelo->modificar($datos)) {
			$this->mensaje = "El registro se modific&oacute; con &eacute;xito.";
			$this->tipo_mensaje = 1;
		} else {
			$this->mensaje = "Error al modificar el registro.";
			$this->tipo_mensaje = 2;
		}
	}

	/**
	 * Se carga la foto Principal
	 * @param  [type] $datos        [description]
	 * @param  [type] $info_foto [description]
	 */
	private function cargarFoto($datos, $info_foto) {

		// Se divide el nombre del archivo por cada punto
		$auxiliar = explode(".", $info_foto["name"]);
		// Se toma la última parte del nombre (su extensión)
		$extension_archivo = end($auxiliar);

		// Si el tipo de archivo es png, jpg ó jpeg y su extensión es válida
		if ((($info_foto["type"] == 'image/png') ||
			($info_foto["type"] == 'image/gif') ||
			($info_foto["type"] == 'image/jpg') ||
			($info_foto["type"] == 'image/jpeg')
		) && in_array($extension_archivo, $this->extensiones_fotos_permitidas)
		) {
			// Si su tamaño supera el permitido
			if ($info_foto["size"] != '' && $info_foto["size"] > TAMANIO_MAXIMO_FOTO) {
				$this->mensaje = "El tama&ntilde;o del archivo (" . $info_foto["size"] . ") <b>es mayor al permitido</b>.";
				$this->tipo_mensaje = 2;
			} else {
				// Si surgió un error al intentar cargar el archivo
				if ($info_foto["error"] > 0) {
					$this->mensaje = "Error al cargar la foto: " . $info_foto["error"];
					$this->tipo_mensaje = 2;
				} else {
					$nombre_archivo = $datos['id'] . '_carousel.' . $extension_archivo;

					$ruta_origen = $info_foto['tmp_name'];

					$ruta_destino = RUTA_RECURSOS_CAROUSEL . $nombre_archivo;

					if (!move_uploaded_file($ruta_origen, $ruta_destino)) {
						$this->mensaje = "Error al subir el archivo.";
						$this->tipo_mensaje = 2;
					} else {
						// devuelve el nombre del archivo, para guardar en la DB
						return $nombre_archivo;
					}
				}
			}
		} else {
			$this->mensaje = "El archivo <b>no</b> es una imagen.";
			$this->tipo_mensaje = 2;
		}
	}

	/**
	 * Se carga el video en el directorio respectivo
	 * @param array $datos_recibidos
	 * @param array $info_video
	 */
	public function cargarVideo($datos_recibidos, $info_video) {

		// Primero se comprueba si el perfil actual posee permiso para ejecutar la acción respectiva
		//$this->comprobarAcceso($this->perfiles_permitidos_para_cargar_video, $_SESSION['perfil1']);

		// Directorio donde se almacenan los videos
		$directorio_destino = RUTA_RECURSOS_CAROUSEL;

		// Si se reciben los datos
		if (isset($datos_recibidos)) {

			// Archivo temporal del video a guardar
			$video_a_guardar = $info_video['tmp_name'];

			// Se eliminan los espacios vacíos que contenga el nombre del video
			$nombre_video = LibreriaGeneral::eliminarEspacios($info_video['name']);

			// Se toma la extensión del video y se convierte a minúscula
			$extension = strtolower(pathinfo($nombre_video, PATHINFO_EXTENSION));

			// Se agrega como prefijo el Id del Contenido, al nombre del video, y se convierte a minúsculas
			$nombre_video_a_guardar = strtolower($datos_recibidos['id'] . "_video." . $extension);

			// Si no se recibió el video
			if ($info_video['error'][$f] == 4) {
				$this->mensaje = "No se ha subido el video.";
				$this->tipo_mensaje = 2;
			}

			// Si el video fue recibido sin errores
			if ($info_video['error'][$f] == 0) {
				// Si el tamaño del video supera el límite determinado
				if ($info_video['size'][$f] > TAMANIO_MAXIMO_VIDEO) {
					$this->mensaje = "El video supera el tama&ntilde;o m&aacute;ximo permitido!";
					$this->tipo_mensaje = 2;
				}
				// Si su extensión no es válida
				elseif (!in_array($extension, $this->extensiones_video_validas)) {
					$this->mensaje = "La extensi&oacute;n del video no es v&aacute;lida";
					$this->tipo_mensaje = 2;
				} else {
					// Si no existe el directorio
					if (!is_dir($directorio_destino)) {
						$permisos = '777';
						$permisos = octdec(str_pad($permisos, 4, '0', STR_PAD_LEFT));

						mkdir($directorio_destino, $permisos); // Se crea
						chmod($directorio_destino, $permisos); // Se le da permisos
					}

					// Se arma la ruta destino: directorio + nombre de video
					$ruta_destino_completa = $directorio_destino . $nombre_video_a_guardar;

					// Se mueve el video al directorio destino
					if (move_uploaded_file($video_a_guardar, $ruta_destino_completa)) {

						// Si realmente se cargó el video en el directorio respectivo
						if (LibreriaGeneral::existeArchivo($ruta_destino_completa)) {

							// Se intenta registrar el nombre del video en la DB
							if (!$this->modelo->ingresarNombreRecurso($datos_recibidos['id'], $nombre_video_a_guardar)) {
								$this->mensaje = "Error al ingresar el video " . $nombre_video;
								$this->tipo_mensaje = 2;
							}
						}
					} else {
						$this->mensaje = "No se ha cargado el video.";
						$this->tipo_mensaje = 2;
					}
				}
			}

			// Si no surgió un error
			if ($this->tipo_mensaje != 2) {
				$this->mensaje = "Se ha realizado la carga del video satisfactoriamente!";
				$this->tipo_mensaje = 1;
			}
		} else {
			$this->mensaje = "No se han recibido datos para la carga.";
			$this->tipo_mensaje = 2;
		}
	}

	/**
	 * Se elimina un registro determinado
	 */
	public function eliminar() {

		// Primero se comprueba si el perfil actual posee permiso para ejecutar la acción respectiva
		//$this->comprobarAcceso($this->perfiles_permitidos_para_eliminar, $_SESSION['perfil1']);

		$id = LibreriaGeneral::recoge('id', 0);
		$pagina = LibreriaGeneral::recoge('pagina');

		// Se obtiene la info
		$info = $this->modelo->obtenerRegistro($id);

		// Si existe el recurso
		if (is_file(RUTA_RECURSOS_CAROUSEL . $info['recurso'])) {
			// Se elimina
			unlink(RUTA_RECURSOS_CAROUSEL . $info['recurso']);
		}

		// Si se ha eliminado
		if ($this->modelo->eliminar($id)) {

			// Se regenera la prioridad
			$this->regenerarPrioridad();

			$this->listar($this->mensaje_eliminacion_ok, 1, $pagina);
		} else {
			$this->listar($this->mensaje_eliminacion_error, 2, $pagina);
		}
	}

	/**
	 * Se modifica el estado Habilitado|Deshabilitado
	 */
	public function modificarEstado() {
		
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

		// Se regenera la prioridad
		$this->regenerarPrioridad();

		// Se vuelve a mostrar el listado
		$this->listar($mensaje, $tipo_mensaje, $pagina);
	}

	/**
	 * Se define si es una Actividad o no
	 */
	public function modificarEstadoEsActividad() {

		// Primero se comprueba si el perfil actual posee permiso para ejecutar la acción respectiva
		//$this->comprobarAcceso($this->perfiles_permitidos_para_modificar_estado_es_actividad, $_SESSION['perfil1']);

		$id = LibreriaGeneral::recoge('id');
		$es_actividad = LibreriaGeneral::recoge('es_actividad');
		$pagina = LibreriaGeneral::recoge('pagina');

		// Se define si es es_actividad o no
		if ($this->modelo->modificarEstadoEsActividad($id, $es_actividad)) {
			$mensaje = $this->mensaje_modificacion_estado_ok;
			$tipo_mensaje = 1;
		} else {
			$mensaje = $this->mensaje_modificacion_estado_error;
			$tipo_mensaje = 2;
		}

		// Se vuelve a mostrar el listado
		$this->listar($mensaje, $tipo_mensaje, $pagina);
	}

	/**
	 * Se genera la Prioridad
	 * @return boolean
	 */
	public function regenerarPrioridad() {

		// Se inicializa con la última prioridad registrada más uno, para disminuir correctamente sin perder su valor
		$prioridad = $this->modelo->obtenerUltimaPrioridad() + 1;

		// Se obtiene el listado ordenado descendentemente
		$datos = $this->modelo->obtenerListado();

		$cantidad_datos = (isset($datos)) ? count($datos) : 0;

		for ($i = 0; $i < $cantidad_datos; $i++) {
			$dato = &$datos[$i];

			$prioridad_a_guardar = --$prioridad;

			if (!$this->modelo->guardarPrioridad($dato['id'], $prioridad_a_guardar)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Se sube su prioridad
	 */
	public function subirPrioridad() {
		// Primero se comprueba si el perfil actual posee permiso para ejecutar la acción respectiva
		//$this->comprobarAcceso($this->perfiles_permitidos_para_subir_prioridad, $_SESSION['perfil1']);

		$id = LibreriaGeneral::recoge('id', 0);
		$prioridad = LibreriaGeneral::recoge('prioridad', 0);
		$pagina = LibreriaGeneral::recoge('pagina');

		// Se sube de prioridad
		if ($this->modelo->subirPrioridad($id, $prioridad)) {
			// Se vuelve a mostrar el listado
			$this->listar($this->mensaje_modificacion_prioridad_ok, 1, $pagina);
		} else {
			// Se vuelve a mostrar el listado
			$this->listar($this->mensaje_modificacion_prioridad_error, 2, $pagina);
		}
	}

	/**
	 * Se baja su prioridad
	 */
	public function bajarPrioridad() {
		// Primero se comprueba si el perfil actual posee permiso para ejecutar la acción respectiva
		//$this->comprobarAcceso($this->perfiles_permitidos_para_bajar_prioridad, $_SESSION['perfil1']);

		$id = LibreriaGeneral::recoge('id', 0);
		$prioridad = LibreriaGeneral::recoge('prioridad', 0);
		$pagina = LibreriaGeneral::recoge('pagina');

		// Se baja de prioridad
		if ($this->modelo->bajarPrioridad($id, $prioridad)) {
			// Se vuelve a mostrar el listado
			$this->listar($this->mensaje_modificacion_prioridad_ok, 1, $pagina);
		} else {
			// Se vuelve a mostrar el listado
			$this->listar($this->mensaje_modificacion_prioridad_error, 2, $pagina);
		}
	}
}

<?php
if (!isset($_SESSION)) {
	session_start();
}

// Incluye la libreria (y configuracion) de envio por mail mediante PHPList
require_once $_SERVER['DOCUMENT_ROOT'] . "/sgl/librerias/PhpListRESTApiClient.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/sgl/config/mail_config.php";

//Incluye el modelo que corresponde
require_once RUTA_MODELOS . "gacetillas.php";
require_once RUTA_MODELOS . "mails_prensa.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "gacetillas/grilla.php";
require_once RUTA_VISTAS . "gacetillas/edicion.php";
require_once RUTA_VISTAS . "gacetillas/suscriptores.php";
require_once RUTA_VISTAS . "gacetillas/previa.php";

class gacetillas_controller extends ControllerBase {

	protected $vista_suscriptores;
	protected $vista_previa;
	
	private $error_al_cargar_fotos_restantes;

	public function __construct() {
		parent::__construct();

		$this->campo_orden_por_defecto = 'g_fecha';

		// Se crea una instancia del modelo
		$this->modelo = new gacetillasModel();
		$this->modeloMailsPrensa = new mailsPrensaModel();

		// Se crea una instancia de cada Vista
		$this->vista_grilla = new VistaGacetillaGrilla();
		$this->vista_edicion = new VistaGacetillaEdicion();
		$this->vista_suscriptores = new VistaGacetillaSuscriptores();
		$this->vista_previa = new VistaGacetillaPrevia();
	}

	public function guardarRegistroOriginal($original) {

		$_SESSION['g_codigo_original'] = $original['g_codigo'];
		$_SESSION['g_fecha_original'] = $original['g_fecha'];
		$_SESSION['g_titulo_original'] = $original['g_titulo'];
		$_SESSION['g_texto_original'] = $original['g_texto'];
		$_SESSION['g_foto_original'] = $original['g_foto'];
		$_SESSION['g_tipo_original'] = $original['g_tipo'];
		$_SESSION['g_acto_original'] = $original['g_acto'];
		$_SESSION['g_habilitada_original'] = $original['g_habilitada'];
		$_SESSION['g_enviar_por_mail_original'] = $original['g_enviar_por_mail'];
		$_SESSION['g_id_mail_original'] = $original['g_id_mail'];
	}

	/**
	 * Se listan los registros
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 * @param  string $p_pagina     [description]
	 * @return [type]               [description]
	 */
	public function listar($mensaje = '', $tipo_mensaje = '') {

		$filtro = Array();

		// Si se recibe la marca para limpiar, se limpia el filtro en la sesión, sino se mantienen
		$_SESSION['f_gacetillas'] = (LibreriaGeneral::recoge('limpiar') == 'si') ? '' : $_SESSION['f_gacetillas'];

		// Filtro por Fecha
		$f_fecha = LibreriaGeneral::recoge('f_fecha');
		$filtro['f_fecha'] = (isset($f_fecha) && $this->esFechaValida($f_fecha)) ? $this->modelo->formatearFechaMySQL($f_fecha) : '';

		// Filtro por Título
		$filtro['f_titulo'] = LibreriaGeneral::recoge('f_titulo', (isset($_SESSION['f_gacetillas']['f_titulo']) ? $_SESSION['f_gacetillas']['f_titulo'] : ''));

		// Se filtra por Proveedor
		$filtro['f_tipo'] = LibreriaGeneral::recoge('f_tipo', (isset($_SESSION['f_gacetillas']['f_tipo']) ? $_SESSION['f_gacetillas']['f_tipo'] : 0));

		// Se filtra por Rubro
		$filtro['f_acto'] = LibreriaGeneral::recoge('f_acto', (isset($_SESSION['f_gacetillas']['f_acto']) ? $_SESSION['f_gacetillas']['f_acto'] : 0));

		$_SESSION['f_gacetillas'] = $filtro;

		// SE SETEA EL CAMPO POR EL CUAL SE ORDENA
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
			// Si es la primera vez que carga la pagina
			// o se esta cambiando el campo por el que se ordena
			$_SESSION['ultimo_campo'] = $filtro['campo_orden'];
			$_SESSION['ultimo_sentido'] = 'asc';
		} else {
			// Si se hizo clic en el mismo que ya estaba ordenado antes, solo hay que cambiar el sentido
			$_SESSION['ultimo_sentido'] = ($_SESSION['ultimo_sentido'] == 'asc' && $filtro['sentido'] == '') ? 'desc' : 'asc';
		}

		// Cantidad de registros a mostrar
		$filtro['rango'] = $this->rango_paginacion;

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
			} else
			// SE MUESTRAN LOS ÚLTIMOS (VALOR DEL RANGO) REGISTROS
			{
				$filtro['inicio'] = $filtro['cantidad'] - $filtro['rango'];
			}

		} else {
			$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
		}

		$filtro['pagina_ant'] = $filtro['pagina'] - 1; // para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1; // para la pagina posterior

		// Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);

		$datos = $this->modelo->listar();

		$this->vista_grilla->mostrar($datos, $mensaje, $tipo_mensaje, $filtro);
	}

	/**
	 * Se edita un registro de un Id determinado
	 */
	public function editar($datos_formulario = null, $mensaje = '', $tipo_mensaje = '') {

		// Si se viene del listado
		if ($datos_formulario === null) {
			$codigo = LibreriaGeneral::recoge('id', 0);

			// Se obtienen los datos
			$datos = $this->modelo->obtenerRegistro($codigo);

			// Si existe
			if ($datos['g_codigo']) {

				// SE GUARDA EL REGISTRO EN SESION PARA VERIFICAR LUEGO SI LO HA MODIFICADO OTRO USUARIO
				$this->guardarRegistroOriginal($datos);

				$datos['pagina'] = LibreriaGeneral::recoge('pagina');

				$datos = $this->retirarBarraInvertida($datos);

				// Se obtienen las fotos de la galería de la Gacetilla respectiva
				$datos['fotos'] = $this->modelo->obtenerFotos($datos['g_codigo']);
			} else {
				$datos = null;
			}
		} else {
			// Si se viene del formulario debido a un error
			$datos_formulario['g_fecha'] = ($datos_formulario['g_fecha'] != '') ? $this->modelo->formatearFechaMySQL($datos_formulario['g_fecha']) : '';

			$datos = $datos_formulario;

			// Se obtienen las fotos de la galería de la Gacetilla respectiva
			$datos['fotos'] = $this->modelo->obtenerFotos($datos['g_codigo']);
		}

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

		// Si se recibieron los datos del registro con el archivo a cargar
		if (isset($datos) && isset($info_foto['name']) && $info_foto['name'] != '') {

			$nombre_archivo = $info_foto['name'];

			// Se toma la extensión del archivo y se convierte a minúscula
			$extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));

			// Si su extensión no es válida
			if (!in_array($extension, $this->extensiones_fotos_permitidas)) {
				$this->mensaje = "La extensi&oacute;n de " . $nombre_archivo . " no es v&aacute;lida.";
				$this->tipo_mensaje = 2;
			} else {
				// Archivo
				$archivo_a_guardar = $info_foto['tmp_name'];

				// Se obtiene información del archivo recibido
				$info_imagen = @getimagesize($archivo_a_guardar);
				
				// Si el archivo es una imagen
				if ( $info_imagen ) {
					$ancho_original = $info_imagen[0];// Ancho de la imagen recibida
					$alto_original = $info_imagen[1];// Alto de la imagen recibida
					
					// Si el ancho es menor al alto (si la foto es vertical)
					if ( $ancho_original < $alto_original ) {
						$this->mensaje = "La foto no es v&aacute;lida para la Gacetilla, su ancho es menor a su alto.";
						$this->tipo_mensaje = 2;
					} else {
						// Se eliminan los espacios vacíos que contenga el nombre del archivo
						// se convierte a minúsculas
						// se coloca el prefijo definido en la Vista
						$nombre_archivo_a_guardar = $datos['prefijo'] . '__' . mb_strtolower(LibreriaGeneral::reemplazarEspaciosPorGuionesBajos(LibreriaGeneral::quitarAcentos($nombre_archivo)));

						// Si no se recibió el archivo
						if ($info_foto['error'] == 4) {
							$this->mensaje = "No se ha subido el archivo " . $nombre_archivo;
							$this->tipo_mensaje = 2;
						}

						// Si el archivo fue recibido sin errores
						if ($info_foto['error'] == 0) {

							// Se arma la ruta destino: directorio + nombre de archivo
							$ruta_destino_completa = RUTA_DIRECTORIO_TEMPORAL . $nombre_archivo_a_guardar;

							// Se mueve el archivo al directorio destino
							move_uploaded_file($archivo_a_guardar, $ruta_destino_completa);
						}
					}
				} else {
					$this->mensaje = "El archivo subido no es una imagen.";
					$this->tipo_mensaje = 2;
				}
			}

			// Si no surgió un error
			if ($this->tipo_mensaje != 2) {
				$this->mensaje = "Se ha realizado la carga <strong>temporal</strong> del archivo satisfactoriamente. Para finalizar la edici&oacute;n utilice el bot&oacute;n <strong>Guardar</strong>.";
				$this->tipo_mensaje = 1;
			}
		} else {
			$this->mensaje = "No se ha recibido un archivo.";
			$this->tipo_mensaje = 2;
		}

		// Se formatean las fechas recibidas como yyyy-mm-dd para seguir editando en la Vista
		$datos['g_fecha'] = ($datos['g_fecha'] != '') ? LibreriaGeneral::formatearFechaConGuiones($datos['g_fecha']) : '';

		// Se obtienen las fotos de la galería de la Gacetilla respectiva
		$datos['fotos'] = ($datos['g_codigo'] != '') ? $this->modelo->obtenerFotos($datos['g_codigo']) : null;

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
		if ($datos['g_codigo'] != '') {

			// Se reciben las fotos restantes
			$info_fotos = $_FILES['fotos'];
			
			// Si se recibieron fotos para cargar
			if ( isset($info_fotos['name'][0]) && $info_fotos['name'][0] != '' ) {
				// Se cargan las fotos restantes
				$this->cargarImagenes($datos, $info_fotos);
			}

			// Se modifica en la DB
			$this->modificar($datos);

			// Se vuelve a la grilla
			$this->listar("Se ha guardado la Gacetilla con &eacute;xito.", 1);
		} else {
			// Se ingresa en la DB
			$this->insertar($datos);
			// Se obtiene el Id recién registrado
			$datos['g_codigo'] = $this->modelo->obtenerUltimoId();

			// Se fuerza la url para que se mantenga en la edición
			header('Location: '.URL_ABMS.'?controlador=gacetillas&accion=editar&id='.$datos['g_codigo']);
			exit;
		}
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
							$ruta_final_foto = RUTA_FOTOS_GACETILLAS . $id . "." . $extension;

							// Se intenta copiar el archivo al directorio destino
							if (!copy(RUTA_DIRECTORIO_TEMPORAL . $file, $ruta_final_foto)) {
								return false;
							} else {
								// Se ingresa el nombre de la foto en la DB
								$this->modelo->ingresarNombreFoto($id, $id . "." . $extension);
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
		$datos['g_codigo'] = LibreriaGeneral::recoge('g_codigo', 0);
		$datos['g_fecha'] = LibreriaGeneral::recoge('g_fecha');
		$datos['g_titulo'] = LibreriaGeneral::recoge('g_titulo');
		$datos['g_tipo'] = LibreriaGeneral::recoge('g_tipo');
		$datos['g_acto'] = LibreriaGeneral::recoge('g_acto');
		$datos['g_foto'] = LibreriaGeneral::recoge('g_foto');
		$datos['g_texto'] = LibreriaGeneral::recoge('g_texto');
		$datos['g_enviar_por_mail'] = LibreriaGeneral::recoge('g_enviar_por_mail');

		$datos['pagina'] = LibreriaGeneral::recoge('pagina');

		// Se formatean las fechas recibidas como yyyy-mm-dd para seguir editando en la Vista
		$datos['g_fecha'] = ($datos['g_fecha'] != '') ? LibreriaGeneral::formatearFechaConGuiones($datos['g_fecha']) : '';

		// Se redirecciona al formulario de edición
		$this->vista_edicion->mostrar($datos, $this->mensaje, $this->tipo_mensaje);
	}

	/**
	 * Se elimina la foto del directorio respectivo
	 */
	public function eliminarFoto() {

		// Se recibe el id
		$id = LibreriaGeneral::recoge('id', 0);

		// se obtiene el nombre de la foto
		$nombre_foto = $this->modelo->obtenerNombreFoto($id);

		// Si existe la foto en el directorio respectivo
		if (is_file(RUTA_FOTOS_GACETILLAS . $nombre_foto)) {

			// Si se elimina la foto (en la DB y físicamente)
			if ($this->modelo->eliminarFoto($id) && unlink(RUTA_FOTOS_GACETILLAS . $nombre_foto)) {

				$mensaje = "Se elimin&oacute; la foto " . $nombre_foto . " con &eacute;xito.";
				$tipo_mensaje = 1;
			} else {
				$mensaje = "No se ha eliminado la foto " . $nombre_foto . ".";
				$tipo_mensaje = 2;
			}
		}

		// Se fuerza la url para que se mantenga en la edición
		header('Location: '.URL_ABMS.'?controlador=gacetillas&accion=editar&id='.$id);
		exit;
	}

	/**
	 * Se elimina una foto secundaria y en la DB mediante su id
	 */
	public function eliminarFotoSecundaria() {

		// Se recibe el id de la foto secundaria
		$id_imagen = LibreriaGeneral::recoge('id_imagen');
		// Se recibe el id de la Gacetilla
		$id_gacetilla = LibreriaGeneral::recoge('id_gacetilla');

		// Se obtienen los datos de la foto secundaria
		$registro = $this->modelo->obtenerDatosImagen($id_imagen);

		// Si existe la foto en el directorio
		if (is_file(RUTA_FOTOS_GACETILLAS . $registro['fsg_nombre_foto'])) {
			// Si se elimina la foto del directorio
			if (unlink(RUTA_FOTOS_GACETILLAS . $registro['fsg_nombre_foto'])) {
				// Si se elimina el nombre de la foto secundaria en la DB
				if ($this->modelo->eliminarFotoSecundaria($id_imagen)) {
					$mensaje = "Se elimin&oacute; la imagen con &eacute;xito.";
					$tipo_mensaje = 1;
				} else {
					$mensaje = "No se ha eliminado la imagen.";
					$tipo_mensaje = 2;
				}
			}
		}
		
		// Se fuerza la url para que se mantenga en la edición
		header('Location: '.URL_ABMS.'?controlador=gacetillas&accion=editar&id='.$id_gacetilla);
		exit;
	}

	/**
	 * Se cargan las imágenes en el directorio respectivo
	 * @param array $datos_recibidos
	 * @param array $info_de_archivos
	 */
	private function cargarImagenes($datos_recibidos, $info_de_archivos)
	{
		// Directorio donde se almacenan las fotos restantes de las gacetillas
		$directorio_destino = RUTA_FOTOS_GACETILLAS;

		// Cantidad de archivos subidos con éxito
		$nro_archivos_subidos = 0;

		// Si se reciben los datos
		if( isset($datos_recibidos) )
		{
			// Se intenta subir cada archivo recibido
			foreach ($info_de_archivos['name'] as $f => $nombre_archivo)
			{
				// Archivo de la imagen
				$archivo_a_guardar = $info_de_archivos['tmp_name'][$f];
				
				// Se eliminan los espacios vacíos que contenga el nombre del archivo
				$nombre_archivo = LibreriaGeneral::eliminarEspacios($nombre_archivo);
				
				// Se toma la extensión del archivo y se convierte a minúscula
				$extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
				
				// Se conforma el nombre de la imagen a guardar con: 
				// código de la Gacetilla + número entero aleatorio + "." + extensión
				$nombre_archivo_a_guardar = $datos_recibidos['g_codigo']."_".rand().".".$extension;
				
				// Si no se recibió el archivo
				if ($info_de_archivos['error'][$f] == 4)
				{
					$this->mensaje = "No se ha subido el archivo ".$nombre_archivo;
					$this->tipo_mensaje = 2;
					$this->error_al_cargar_fotos_restantes = true;
					continue; // Se saltea el archivo
				}

				// Si el archivo fue recibido sin errores
				if ($info_de_archivos['error'][$f] == 0) {
					// Si el tamaño del archivo supera el límite determinado
					if ($info_de_archivos['size'][$f] > TAMANIO_MAXIMO_FOTO) {
						$this->mensaje = $nombre_archivo." supera el tama&ntilde;o m&aacute;ximo permitido!";
						$this->tipo_mensaje = 2;
						$this->error_al_cargar_fotos_restantes = true;
						continue; // Se saltea el archivo
					}
					// Si su extensión no es válida
					elseif( !in_array($extension, $this->extensiones_fotos_permitidas) )
					{
						$this->mensaje = "La extensi&oacute;n de ".$nombre_archivo." no es v&aacute;lida";
						$this->tipo_mensaje = 2;
						$this->error_al_cargar_fotos_restantes = true;
						continue; // Se saltea el archivo
					}
					else
					{
						// Se obtienen datos específicos de la imagen
						$datos_archivo_a_guardar = getimagesize($archivo_a_guardar);

						// Si el archivo realmente es una imagen
						if ( $datos_archivo_a_guardar ) {
							// Si no existe el directorio
							if ( !is_dir($directorio_destino) ) {
								$permisos = '777';
								$permisos = octdec(str_pad($permisos, 4, '0', STR_PAD_LEFT));

								mkdir($directorio_destino, $permisos); // Se crea
								chmod($directorio_destino, $permisos); // Se le da permisos
							}
						}

						// Se arma la ruta destino: directorio + nombre de archivo
						$ruta_destino_completa = $directorio_destino.$nombre_archivo_a_guardar;
						//LibreriaGeneral::registrarLog("ruta_destino_completa", $ruta_destino_completa);
						
						// Se mueve el archivo al directorio destino
						if( move_uploaded_file($archivo_a_guardar, $ruta_destino_completa) )
						{
							// Número de archivos subidos con éxito
							$nro_archivos_subidos++;

							// Si NO se ingresó la imagen en la DB
							if ( ! $this->modelo->ingresarNombreImagenEnDB($datos_recibidos['g_codigo'], $nombre_archivo_a_guardar))
							{
								$this->mensaje = "Error al ingresar la imagen ".$nombre_archivo;
								$this->tipo_mensaje = 2;
								$this->error_al_cargar_fotos_restantes = true;
								continue; // Se saltea el archivo
							}
						}
					}
				}
			}
			// Si no surgió un error
			if ( $this->tipo_mensaje != 2 )
			{
				$this->mensaje = "Se ha realizado la carga de ".$nro_archivos_subidos." archivo/s satisfactoriamente!";
				$this->tipo_mensaje = 1;
				$this->error_al_cargar_fotos_restantes = false;
			}
		}
		else
		{
			$this->mensaje = "No se han recibido datos para la carga de la/s imagen/es.";
			$this->tipo_mensaje = 2;
			$this->error_al_cargar_fotos_restantes = true;
		}
	}

	/**
	 * Se ingresa
	 */
	public function insertar($datos) {

		if ($this->modelo->insertar($datos)) {
			$this->mensaje = "La gacetilla se ingres&oacute; con &eacute;xito.";
			$this->tipo_mensaje = 1;
		} else {
			$this->mensaje = "Error al ingresar la gacetilla.";
			$this->tipo_mensaje = 2;
		}
	}

	/**
	 * Se modifica
	 */
	public function modificar($datos) {

		if ($this->modelo->modificar($datos)) {
			$this->mensaje = "La gacetilla se modific&oacute; con &eacute;xito.";
			$this->tipo_mensaje = 1;
		} else {
			$this->mensaje = "Error al modificar la gacetilla.";
			$this->tipo_mensaje = 2;
		}
	}

	/**
	 * Se elimina un registro determinado
	 */
	public function eliminar() {
		
		$id = LibreriaGeneral::recoge('id', 0);
		$pagina = LibreriaGeneral::recoge('pagina');

		// Se obtiene el nombre de la foto a eliminar
		$nombre_foto = $this->modelo->obtenerNombreFoto($id);

		// Si existe la foto
		if (is_file(URL_FOTOS_GACETILLAS . $nombre_foto)) {
			// Se elimina del directorio respectivo
			unlink(URL_FOTOS_GACETILLAS . $nombre_foto);
		}

		// Se deberían eliminar las fotos secundarias de existir
		// .....

		if ($this->modelo->eliminar($id)) {
			$this->listar($this->mensaje_eliminacion_ok, 1);
		} else {
			$this->listar($this->mensaje_eliminacion_error, 2);
		}
	}

	/**
	 * Se modifica el estado Habilitado|Deshabilitado
	 */
	public function modificarEstado() {
		parent::modificarEstadoBase();
	}

	/**
	 * Se visualiza la vista previa
	 */
	public function verVistaPrevia() {

		// Se recibe el Id
		$id = LibreriaGeneral::recoge('id', 0);

		// Se busca el registro
		$datos = $this->modelo->obtenerRegistro($id);

		// Se obtienen las fotos de la Gacetilla
		$datos['fotos'] = $this->modelo->obtenerFotos($id);

		// Se recibe el número de página actual
		$datos['pagina'] = LibreriaGeneral::recoge('pagina');
		
		// Se visualiza la Vista Previa
		$this->vista_previa->mostrar($datos);
	}

	/* Se genera el archivo de texto utilizado como flag para la ejecución
	 * del proceso de actualización de las fotos de las gacetillas.
	*/
	public function generarArchivoTexto() {
		
		// SE ABRE EL ARCHIVO DE TEXTO PARA SOBREESCRIBIRLO, SI NO EXISTE SE CREA
		$archivo_txt = fopen(RUTA_RAIZ.'abms/procesargace.txt', 'w');

		if ($archivo_txt) {
			$this->mensaje = "La Gacetilla se ha enviado por correo electr&oacute;nico con &eacute;xito.";
			$this->tipo_mensaje = 1;
		} else {
			$this->mensaje = "La Gacetilla no se ha podido enviar por correo electr&oacute;nico.";
			$this->tipo_mensaje = 2;
		}

		// SE CIERRA EL ARCHIVO DE TEXTO
		fclose($archivo_txt);

		// SE MUESTRA EL LISTADO DE GACETILLAS Y UN MENSAJE CON EL RESULTADO DE LA OPERACION
		$this->listar($this->mensaje, $this->tipo_mensaje);
	}

	/**
	 * Se envia la Gacetilla por mail
	 */
	public function enviarGacetillaPorMail() {
		$codigo = LibreriaGeneral::recoge('codigo');
		// Se obtiene el registro
		$registro = $this->modelo->obtenerRegistro($codigo);

		// Si existe
		if ($registro['g_codigo'] != '') {
			// Se habilita para enviarla por correo,
			// puede pasar que no se haya habilitado en la edición y se desee enviar desde la Vista previa.
			$registro['g_enviar_por_mail'] = 1;

			if ($this->enviarMail($registro)) {

				// Se genera el archivo de texto utilizado como flag para la ejecución
				// del proceso de actualización de las fotos de las gacetillas
				$archivo_txt = fopen(RUTA_RAIZ.'abms/procesargace.txt', 'w');
				// Se cierra el archivo de texto
				fclose($archivo_txt);

				// Se audita el ENVIO de la Gacetilla
				$this->modelo->auditarEnvioGacetilla($registro['g_codigo'], $registro['g_titulo']);

				// Se muestra la grilla de gacetillas
				$this->listar("La Gacetilla se ha enviado por correo electr&oacute;nico con &eacute;xito.", 1);
			} else {
				$this->listar("La Gacetilla no se ha podido enviar por correo electr&oacute;nico.", 2);
			}

		} else {
			$this->listar("No se ha encontrado la Gacetilla.", 2);
		}

	}

	/**
	 * Se arma el contenido del mensaje del Mail
	 * @param  [array] 	   $gacetilla 	Información de la Gacetilla
	 * @return [string]    $contenido   Cadena de texto con el contenido HTML a ser utilizado por la API de PHP List
	 */
	public function armarContenidoMail($gacetilla) {
		$contenido = '';
		// Se obtienen las fotos restantes de la Gacetilla respectiva
		$fotos_restantes = $this->modelo->obtenerFotos($gacetilla['g_codigo']);

		// Cantidad de fotos restantes
		$cant_imagenes_restantes = count($fotos_restantes);

		// Se convierte la fecha al formato yyyy-mm-dd, en caso que se reciba con formato dd/mm/yyyy
		$fecha_gacetilla = (strpos($gacetilla['g_fecha'], '-') === false) ? LibreriaGeneral::formatearFechaConGuiones($gacetilla['g_fecha']) : $gacetilla['g_fecha'];
		// Se obtiene la fecha en formato gregoriano
		$fecha_a_mostrar = LibreriaGeneral::obtenerNombreDia($fecha_gacetilla) . ' ' . LibreriaGeneral::mostrarFechaLetras($fecha_gacetilla);

		// Contenedor general (CSS .vista_previa_gacetilla_cuerpo)
		$contenido .= '<div style="clear:both; background:#FFF; min-height:200px; font-size:13px;">';

		// (CSS .vista_previa_gacetilla_fecha)
		$contenido .= '<div style="font-size: 14px;color: #666666;padding:5px 20px;text-align: left;">' . $fecha_a_mostrar . '</div>';

		// Si posee foto principal
		if (isset($gacetilla['g_foto']) && $gacetilla['g_foto'] != '') {
			// foto existente
			$img_ppal = $gacetilla['g_foto'];

			// Se agrega
			$contenido .= '<div><img src="' . LISTA_CORREO_IMG_SIN_RESIZE_URL . $img_ppal . '" width="100%" /></div>';
			//$contenido .= '<div><img src="'.sprintf(LISTA_CORREO_IMG_URL, 800, $img_ppal).'" width="100%" /></div>';
		}

		// Texto (CSS .vista_previa_gacetilla_texto)
		$contenido .= '<div style="padding: 20px;font-size: 13px;color: #666666;text-align: justify;"><p>';
		$contenido .= ($gacetilla['g_texto'] != '') ? nl2br($gacetilla['g_texto']) : '';
		$contenido .= '</p></div>';

		// Si posee fotos restantes
		if ($cant_imagenes_restantes > 0) {
			// Contenedor de las fotos restantes (CSS .vista_previa_gacetilla_contenedor_fotos_secundarias)
			$contenido .= '<div style="clear: both;width: 100%;margin: 10px auto;padding-top: 10px;">';
			// Por cada foto restante
			for ($i = 0; $i < $cant_imagenes_restantes; $i++) {
				$info_imagen = &$fotos_restantes[$i];

				// Contenedor de la foto (CSS .vista_previa_gacetilla_contenedor_foto_secundaria, IMPORTANTE: EL min-height ES MENOR AQUÍ)
				$contenido .= '<div style="width: 30%;min-height:118px;float: left;text-align:center;margin-left: 20px;margin-bottom: 5px;padding-top:5px;background-color: #F5F5F5;border: 1px solid #E5E5E5;">';

				// La foto secundaria
				$contenido .= '<img src="' . LISTA_CORREO_IMG_SECUNDARIAS_URL . $info_imagen['fsg_nombre_foto'] . '" width="97%" />';

				// fin del contenedor de la foto
				$contenido .= '</div>';
			}
			// fin del contenedor de las fotos restantes
			$contenido .= '</div>';
		}

		// fin del contenedor general
		$contenido .= '</div>';

		return $contenido;
	}

	public function enviarMail($gacetilla) {
		// Si está tildada la opción de enviar por mail la gacetilla
		if ($gacetilla['g_enviar_por_mail'] == 1) {

			$api = new PhpListRESTApiClient(LISTA_CORREO_API_URL, LISTA_CORREO_API_USER, LISTA_CORREO_API_PASSWORD);

			try {
				if ($api->login()) {

					$fecha_envio = new DateTime();
					$fecha_envio->add(new DateInterval('PT' . LISTA_CORREO_DELAY_ENVIO . 'M')); // envio en N minutos

					// Se arma el string con todo el HTML necesario para el contenido del mail a enviar.
					// Contiene:
					// Fecha + foto principal + texto + fotos restantes (éstas últimas en caso que posea la gacetilla)
					$contenido_para_mail = $this->armarContenidoMail($gacetilla);

					$nueva_campana = $api->messageAdd(
						$gacetilla['g_titulo'], // $subject
						LISTA_CORREO_REMITENTE, // $fromfield
						LISTA_CORREO_RESPONDER_A, // $replyto
						$contenido_para_mail, // $message      ex $gacetilla['g_texto']
						$contenido_para_mail, // $textmessage  ex $gacetilla['g_texto']
						LISTA_CORREO_HTML_PIE, // $footer
						'submitted', // $status
						LISTA_CORREO_FORMATO_ENVIO, // $sendformat
						LISTA_CORREO_ID_PLANTILLA, // $template
						$fecha_envio->format('Y-m-d H:i'), // $embargo
						'', // $rsstemplate
						LISTA_CORREO_ID_OWNER, // $owner
						LISTA_CORREO_FORMATEADO_HTML// $htmlformatted
					);

					$api->listMessageAdd(LISTA_CORREO_ID_LISTA_DISTRIBUCION, $nueva_campana->id);

					// Se toma el Id de la campaña
					$gacetilla['g_id_mail'] = $nueva_campana->id;

					// Se guarda dicho Id de campaña en la gacetilla
					if ($this->modelo->modificar($gacetilla)) {

						// Se registra la info del mail de la Gacetilla también en la tabla de los Mails de la lista de Prensa
						$info_mail['mlp_fecha'] = $fecha_envio->format('Y-m-d');
						$info_mail['mlp_titulo'] = $gacetilla['g_titulo'];
						$info_mail['mlp_texto'] = $gacetilla['g_texto'];
						$info_mail['mlp_id_gacetilla'] = $gacetilla['g_codigo'];

						return $this->modeloMailsPrensa->insertar($info_mail);
					} else {
						return false;
					}

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

}
?>
<?php
if (!isset($_SESSION)) {
	session_start();
}

//Incluye el modelo que corresponde
require_once RUTA_MODELOS . "equipos_hcd.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "equipos_hcd/grilla.php";
require_once RUTA_VISTAS . "equipos_hcd/edicion.php";
require_once RUTA_VISTAS . "equipos_hcd/combo.php";

class equipos_hcd_controller extends ControllerBase {
	const RUTA_TEMPORAL = "/var/www/sgl/administracion/abms/";
	const RUTA_DIRECTORIO_DHCP3 = "/var/www/sgl/administracion/dhcp3/";
	const NOMBRE_ARCHIVO_CONFIGURACION = 'dhcpd-data.conf';
	const NOMBRE_ARCHIVO_TEXTO = 'procesar.txt';
	const USUARIO = 'expe';
	const PASSWORD = '123456';

	private $id_conexion;
	private $resultado_login;

	public function __construct() {
		parent::__construct();

		$this->campo_orden_por_defecto = 'nombre_netbios';

		// Se crea una instancia del modelo
		$this->modelo = new equiposHcdModel();

		// Se crea una instancia de la Vista
		$this->vista_grilla = new VistaEquiposHcdGrilla();
		$this->vista_edicion = new VistaEquiposHcdEdicion();
		$this->vista_combo = new VistaEquiposHcdCombo();

		// Se inicializa el mensaje de resultados
		$this->mensaje = "";
	}

	public function guardarRegistroOriginal($original) {
		$_SESSION['id_original'] = $original['id'];
		$_SESSION['nombre_netbios_original'] = $original['nombre_netbios'];
		$_SESSION['direccion_mac_original'] = $original['direccion_mac'];
		$_SESSION['ip_original'] = $original['ip'];
		$_SESSION['nameserver_original'] = $original['nameserver'];
		$_SESSION['wins_original'] = $original['wins'];
		$_SESSION['gateway_original'] = $original['gateway'];
		$_SESSION['nro_inventario_original'] = $original['nro_inventario'];
		$_SESSION['fecha_alta_original'] = $original['fecha_alta'];
		$_SESSION['fecha_caducidad_original'] = $original['fecha_caducidad'];
		$_SESSION['comentario_original'] = $original['comentario'];
		$_SESSION['cod_area_original'] = $original['cod_area'];
		$_SESSION['cod_responsable_original'] = $original['cod_responsable'];
		$_SESSION['observaciones_original'] = $original['observaciones'];
		$_SESSION['habilitado_original'] = $original['habilitado'];
	}

	/**
	 * Se resuelve la lógica para obtener el listado
	 * en base a un criterio de búsqueda determinado
	 *
	 * @param string $mensaje
	 * @param string $tipo_mensaje
	 */
	public function listar($mensaje = '', $tipo_mensaje = '') {
		$filtro = Array();

		// Filtro por Area
		$filtro['f_cod_area'] = LibreriaGeneral::recoge('f_cod_area', 0);

		// Filtro por Responsable
		$filtro['f_cod_responsable'] = LibreriaGeneral::recoge('f_cod_responsable', 0);

		// Filtro por Nombre Netbios
		$filtro['f_nombre_netbios'] = LibreriaGeneral::recoge('f_nombre_netbios');

		// Filtro por Dirección MAC
		$filtro['f_direccion_mac'] = LibreriaGeneral::recoge('f_direccion_mac');

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

		// SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
		$_SESSION['filtro_equipos_hcd'] = $filtro;

		// SE ESTABLECE EL FILTRO EN EL MODELO
		$this->modelo->setFiltro($filtro);

		// SE OBTIENE EL LISTADO
		$datos['listado'] = $this->modelo->listar();

		// SE OBTIENEN LAS AREAS REGISTRADAS
		$datos['areas'] = $this->modelo->obtenerAreasRegistradas();

		// SE OBTIENEN LOS RESPONSABLES REGISTRADOS
		$datos['responsables'] = $this->modelo->obtenerResponsablesPorArea();

		// se muestra el listado
		$this->vista_grilla->mostrar($datos, $mensaje, $tipo_mensaje, $filtro);
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
				//$this->modelo->marcarEnEdicion($datos['comp_id']);

				$datos['pagina'] = LibreriaGeneral::recoge('pagina', 1);

				$datos = $this->retirarBarraInvertida($datos);
			} else {
				// En caso de editarse un NUEVO registro
				$datos = null;
			}

		} else {
			// Si se viene del formulario debido a un error
			$datos = $datos_formulario;
		}

		// Se obtienen las áreas del HCD
		$areas = $this->modelo->obtenerAreasActivas();

		// Se obtienen los responsables registrados
		$responsables = $this->modelo->obtenerResponsablesPorArea();
		
		$this->vista_edicion->mostrar($datos, $areas, $responsables, $mensaje, $tipo_mensaje);
	}

	public function refrescarComboResponsables() {

		$cod_area = LibreriaGeneral::recoge('cod_area', 0);
		$cod_responsable = LibreriaGeneral::recoge('cod_responsable', 0);
		
		// Si se invoca desde la edición se recibe en false, para que no tome en cuenta que ya estén registrados
		$se_edita = LibreriaGeneral::recoge('se_edita', 0);

		// Se obtienen los responsables registrados
		$listado_responsables = $this->modelo->obtenerResponsablesPorArea($cod_area, $se_edita);
		
		$this->vista_combo->mostrar($listado_responsables, $cod_responsable);
	}

	public function insertar() {
		$datos = $_REQUEST;

		// SE ARMA LA MAC Y LAS IPs RECIBIDAS DE LA VISTA
		$datos = $this->armarMAC_IPs($datos);

		// PARA QUE DOS USUARIOS NO INGRESEN LA MISMA DIRECCION DE MAC
		if (!$this->modelo->existeMAC($datos['direccion_mac'])) {
			if (!$this->modelo->existeNetBios($datos['nombre_netbios'])) {
				if ($this->modelo->insertar($datos)) {
					$this->listar("El equipo " . $datos['nombre_netbios'] . " se ingres&oacute; con &eacute;xito.", 1);
				} else {
					$this->listar("Error al ingresar el equipo " . $datos['nombre_netbios'], 2);
				}
			} else {
				$this->listar("Se ha registrado previamente un equipo con nombre " . $datos['nombre_netbios'], 2);
			}
		} else {
			$this->listar("Se ha registrado previamente la MAC " . $datos['direccion_mac'], 2);
		}
	}

	public function modificar() {
		$datos = $_REQUEST;

		// SE ARMA LA MAC Y LAS IPs RECIBIDAS DE LA VISTA
		$datos = $this->armarMAC_IPs($datos);

		// Se verifica si el registro no ha sido modificado previamente
		if ($this->modelo->noLoModificoOtroUsuario()) {
			if ($this->modelo->modificar($datos)) {
				$this->listar("El equipo " . $datos['nombre_netbios'] . " se modific&oacute; con &eacute;xito.", 1);
			} else {
				$this->listar("Error al modificar el equipo " . $datos['nombre_netbios'], 2);
			}
		} else {
			$this->listar("El equipo " . $datos['nombre_netbios'] . " se ha modificado previamente.", 2);
		}
	}

	public function eliminar() {
		$id = LibreriaGeneral::recoge('id', 0);

		if ($this->modelo->eliminar($id)) {
			$this->listar("El equipo se elimin&oacute; con &eacute;xito.", 1);
		} else {
			$this->listar("No es posible eliminar el equipo.", 2);
		}
	}

	/**
	 * Se modifica el estado Habilitado|Deshabilitado
	 */
	public function modificarEstado() {
		parent::modificarEstadoBase();
	}

	/**
	 * Se verifica la existencia del nombre netbios
	 */
	public function estaDisponibleNombreNetbios() {
		$nombre_netbios = LibreriaGeneral::recoge('nombre_netbios');

		// Se obtiene la información del Articulo en caso de existir
		echo $this->modelo->estaDisponibleNombreNetbios($nombre_netbios);
	}

	public function armarMAC_IPs($datos) {

		// SE ARMA LA MAC RECIBIDA DE LA VISTA
		if ($datos['parte_direccion_mac_0'] != '') {
			$parte_direccion_mac_0 = $datos['parte_direccion_mac_0'];
			$parte_direccion_mac_1 = $datos['parte_direccion_mac_1'];
			$parte_direccion_mac_2 = $datos['parte_direccion_mac_2'];
			$parte_direccion_mac_3 = $datos['parte_direccion_mac_3'];
			$parte_direccion_mac_4 = $datos['parte_direccion_mac_4'];
			$parte_direccion_mac_5 = $datos['parte_direccion_mac_5'];

			$datos['direccion_mac'] = $parte_direccion_mac_0 . '-' . $parte_direccion_mac_1 . '-' . $parte_direccion_mac_2 . '-' . $parte_direccion_mac_3 . '-' . $parte_direccion_mac_4 . '-' . $parte_direccion_mac_5;
		}

		// SE ARMA LA IP RECIBIDA DE LA VISTA
		if ($datos['parte_ip_0'] != '') {
			$parte_ip_0 = $datos['parte_ip_0'];
			$parte_ip_1 = $datos['parte_ip_1'];
			$parte_ip_2 = $datos['parte_ip_2'];
			$parte_ip_3 = $datos['parte_ip_3'];

			$datos['ip'] = $parte_ip_0 . '.' . $parte_ip_1 . '.' . $parte_ip_2 . '.' . $parte_ip_3;
		}

		// SE ARMA LA WINS RECIBIDA DE LA VISTA
		if ($datos['parte_wins_0'] != '') {
			$parte_wins_0 = $datos['parte_wins_0'];
			$parte_wins_1 = $datos['parte_wins_1'];
			$parte_wins_2 = $datos['parte_wins_2'];
			$parte_wins_3 = $datos['parte_wins_3'];

			$datos['wins'] = $parte_wins_0 . '.' . $parte_wins_1 . '.' . $parte_wins_2 . '.' . $parte_wins_3;
		}

		// SE ARMA LA PUERTA DE ENLACE RECIBIDA DE LA VISTA
		if ($datos['parte_gateway_0'] != '') {
			$parte_gateway_0 = $datos['parte_gateway_0'];
			$parte_gateway_1 = $datos['parte_gateway_1'];
			$parte_gateway_2 = $datos['parte_gateway_2'];
			$parte_gateway_3 = $datos['parte_gateway_3'];

			$datos['gateway'] = $parte_gateway_0 . '.' . $parte_gateway_1 . '.' . $parte_gateway_2 . '.' . $parte_gateway_3;
		}

		return $datos;
	}

	public function generarArchivoConfiguracion() {
		// NOMBRE DEL ARCHIVO DE CONFIGURACION A GENERAR O SOBREESCRIBIR
		$nombre_archivo_configuracion = self::NOMBRE_ARCHIVO_CONFIGURACION;

		// NOMBRE DEL ARCHIVO DE TEXTO
		$nombre_archivo_texto = self::NOMBRE_ARCHIVO_TEXTO;

		// SE ABRE EL ARCHIVO DE CONFIGURACION PARA SOBREESCRIBIRLO, SI NO EXISTE SE CREA
		$archivo = fopen($nombre_archivo_configuracion, 'w');

		// PRIMERA PARTE (COMENTARIOS #) DEL ARCHIVO DE CONFIGURACION
		fwrite($archivo, "# Generado por SGL @ " . date("Y-m-d H:i:s") . PHP_EOL);
		fwrite($archivo, "#" . PHP_EOL);
		fwrite($archivo, "# Formato de registro:" . PHP_EOL);
		fwrite($archivo, "# Zona:Nombre netbios:MAC Address:IP:Nameserver:WINS:Gateway:Numero de Inventario:Fecha que caduca:Comentario" . PHP_EOL);
		fwrite($archivo, "# * Para ingresar varios nameservers solo debe separarlos con ','" . PHP_EOL);
		fwrite($archivo, "# * Formato de fecha AAAA-MM-DD" . PHP_EOL . PHP_EOL);

		// Se obtiene el listado de equipos registrados en la red del HCD
		$listado = $this->modelo->obtenerInfoEquiposRedHCD();

		$cantidad_datos = count($listado);
		// Por cada equipo
		for ($i = 0; $i < $cantidad_datos; $i++) {
			$datos_pc = &$listado[$i];

			// SE ESCRIBE LA LINEA EN EL ARCHIVO .conf, EN CASO DE ERROR, SALE DEL CICLO
			if (!fwrite($archivo, $datos_pc['host_data'] . PHP_EOL)) {
				$mensaje = "No se ha podido generar el archivo de configuraci&oacute;n.";
				$tipo_mensaje = 2;
				break;
			} else {
				$mensaje = "El archivo de configuraci&oacute;n se gener&oacute; con &eacute;xito.";
				$tipo_mensaje = 1;
			}
		}

		// SI EL ARCHIVO DE CONFIGURACIÓN SE GENERÓ CON ÉXITO
		if ($tipo_mensaje == '1') {
			// SE ABRE EL ARCHIVO DE TEXTO PARA SOBREESCRIBIRLO, SI NO EXISTE SE CREA
			$archivo_txt = fopen($nombre_archivo_texto, 'w');

			// SE CIERRA EL ARCHIVO DE TEXTO
			fclose($archivo_txt);
		}

		// SE CIERRA EL ARCHIVO DE CONFIGURACIÓN
		fclose($archivo);

		// SI EL ARCHIVO DE CONFIGURACIÓN SE GENERÓ CON ÉXITO
		if ($tipo_mensaje == '1') {
			// RUTA TEMPORAL DONDE ESTÁN EL ARCHIVO DE CONFIGURACION Y EL ARCHIVO DE TEXTO PARA CARGAR (/sgl/administracion/abms/)
			$directorio_desde = self::RUTA_TEMPORAL;

			// RUTA DESTINO DONDE SE CARGARÁN LOS ARCHIVOS (/var/www/sgl/administracion/dhcp3/)
			$directorio_destino = self::RUTA_DIRECTORIO_DHCP3;

			// SE ABRE EL DIRECTORIO TEMPORAL PARA TOMAR LOS ARCHIVOS
			$dir = opendir($directorio_desde);

			// SE ESTABLECE UNA CONEXION FTP
			$this->id_conexion = ftp_connect('localhost');

			// SE ESTABLECE EL INICIO DE SESION FTP CON USUARIO Y PASSWORD
			$this->resultado_login = ftp_login($this->id_conexion, self::USUARIO, self::PASSWORD);

			// Si surge un error al conectarse o autenticarse en el Servidor FTP
			if ((!$this->id_conexion) || (!$this->resultado_login)) {
				$mensaje = "Error al intentar conectarse o autenticarse en el Servidor FTP.";
				$tipo_mensaje = 2;
			} else {
				// SE CAMBIA AL DIRECTORIO DONDE SE QUIEREN SUBIR LOS ARCHIVOS, ( /var/www/sgl/administracion/dhcp3/ )
				if (ftp_chdir($this->id_conexion, $directorio_destino)) {
					$dir_actual = ftp_pwd($this->id_conexion);

					// SE ELIMINA EL ARCHIVO DE TEXTO SI EXISTE EN /dhcp3
					ftp_delete($this->id_conexion, $dir_actual . "/" . $nombre_archivo_texto);

					// SE ELIMINA EL ARCHIVO DE CONFIGURACION SI EXISTE EN /dhcp3
					ftp_delete($this->id_conexion, $dir_actual . "/" . $nombre_archivo_configuracion);

					// SE CARGA EL ARCHIVO DE CONFIGURACION
					if (ftp_put($this->id_conexion, $dir_actual . "/" . $nombre_archivo_configuracion, $directorio_desde . $nombre_archivo_configuracion, FTP_BINARY)) {
						$mensaje = "Se ha cargado el archivo satisfactoriamente!";
						$tipo_mensaje = 1;

						// SE CARGA EL ARCHIVO DE TEXTO
						if (ftp_put($this->id_conexion, $dir_actual . "/" . $nombre_archivo_texto, $directorio_desde . $nombre_archivo_texto, FTP_BINARY)) {
							$mensaje = "Se ha cargado el archivo satisfactoriamente!";
							$tipo_mensaje = 1;
						} else {
							$mensaje = "La transferencia del archivo ha fallado!";
							$tipo_mensaje = 2;
						}
					} else {
						$mensaje = "La transferencia del archivo ha fallado!";
						$tipo_mensaje = 2;
					}
				} else {
					$mensaje = "El cambio al directorio destino ha fallado!";
					$tipo_mensaje = 2;
				}
			}

			//SE CIERRA LA SECUENCIA FTP
			ftp_close($this->id_conexion);

			// SE CIERRA EL DIRECTORIO
			closedir($dir);
		}

		// SE MUESTRA EL LISTADO DE PCs Y UN MENSAJE CON EL RESULTADO DE LA OPERACION
		$this->listar($mensaje, $tipo_mensaje);
	}

	// UTILIZADO UNA SOLA VEZ
	// public function importarDatosPC() {
	// 	// NOMBRE DEL ARCHIVO A IMPORTAR SU CONTENIDO A LA BASE DE DATOS
	// 	$nombre_archivo = 'configuracion_pc_red.conf';

	// 	$datos_pc = Array();

	// 	// SE ABRE EL ARCHIVO PARA LECTURA
	// 	$archivo = fopen($nombre_archivo, 'r');

	// 	// SE ALMACENA SU CONTENIDO EN UNA VARIABLE PARA TRABAJAR CON ÉL
	// 	$contenido = fread($archivo, filesize($nombre_archivo));

	// 	// SE CIERRA EL ARCHIVO
	// 	fclose($archivo);

	// 	// SE TOMAN LAS LINEAS DEL CONTENIDO POR CADA SALTO DE LINEA ENCONTRADO
	// 	// \n = símbolo de fin de línea
	// 	$lineas = explode("\n", $contenido);

	// 	// POR CADA LINEA DEL CONTENIDO
	// 	foreach ($lineas as $linea) {
	// 		// SE TOMA EL PRIMER CARACTER DE LA LINEA
	// 		$primer_caracter = substr($linea, 0, 1);

	// 		// SI EL PRIMER CARACTER NO ES NI # NI UN ESPACIO VACIO
	// 		if ($primer_caracter != '#' && $primer_caracter != '') {
	// 			// SE DIVIDE LA LINEA EN CADA OCURRENCIA DE ':'
	// 			$linea_dividida = explode(":", $linea);

	// 			$offset = 0;
	// 			$datos_pc['zona'] = $linea_dividida[$offset];
	// 			$datos_pc['nombre_netbios'] = $linea_dividida[++$offset];
	// 			$datos_pc['direccion_mac'] = $linea_dividida[++$offset];
	// 			$datos_pc['ip'] = $linea_dividida[++$offset];
	// 			$datos_pc['nameserver'] = $linea_dividida[++$offset];
	// 			$datos_pc['wins'] = $linea_dividida[++$offset];
	// 			$datos_pc['gateway'] = $linea_dividida[++$offset];
	// 			$datos_pc['nro_inventario'] = $linea_dividida[++$offset];
	// 			$datos_pc['fecha_alta'] = date("Y-m-d");
	// 			$datos_pc['fecha_caducidad'] = $linea_dividida[++$offset];
	// 			$datos_pc['comentario'] = $linea_dividida[++$offset];
	// 			$datos_pc['cod_area'] = 'null';
	// 			$datos_pc['cod_responsable'] = 'null';

	// 			// SE GRABAN LOS DATOS DE LA PC EN LA BASE DE DATOS, EN CASO DE ERROR, SALE DEL CICLO
	// 			if (!$this->modelo->insertar($datos_pc)) {
	// 				$mensaje = 'La importaci&oacute;n a la base de datos no se ha podido completar.';
	// 				$tipo_mensaje = 2;
	// 				break;
	// 			} else {
	// 				$mensaje = 'La importaci&oacute;n a la base de datos se realiz&oacute; con &eacute;xito.';
	// 				$tipo_mensaje = 1;
	// 			}
	// 		}
	// 	}

	// 	// SE MUESTRA EL LISTADO DE PCs Y UN MENSAJE CON EL RESULTADO DE LA OPERACION
	// 	$this->listar($mensaje, $tipo_mensaje);
	// }

}
?>

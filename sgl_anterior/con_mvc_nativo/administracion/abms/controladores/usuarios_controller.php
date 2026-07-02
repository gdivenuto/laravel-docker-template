<?php
if (!isset($_SESSION)) {
	session_start();
}

//Incluye el modelo que corresponde
require_once RUTA_MODELOS . "usuarios.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "usuarios/grilla.php";
require_once RUTA_VISTAS . "usuarios/edicion.php";

class usuarios_controller extends ControllerBase {

	private $perfiles_permitidos_para_ingresar_a_biblioteca;
	private $perfiles_permitidos_para_ingresar_a_inventario;

	public function __construct() {

		parent::__construct();

		$this->campo_orden_por_defecto = 'codigo_usuario';

		// Se crea una instancia del modelo
		$this->modelo = new usuariosModel();

		// Se crea una instancia de cada Vista
		$this->vista_grilla = new VistaUsuariosGrilla();
		$this->vista_edicion = new VistaUsuariosEdicion();

		// Se inicializa el mensaje de resultados
		$this->mensaje = "";

		$this->perfiles_permitidos_para_ingresar_a_biblioteca = array(1);
		$this->perfiles_permitidos_para_ingresar_a_inventario = array(1);
	}

	public function guardarRegistroOriginal($original)
	{
		$_SESSION['id_usuario_original'] = $original['id_usuario'];
		$_SESSION['codigo_usuario_original'] = $original['codigo_usuario'];
		$_SESSION['nombre_usuario_original'] = $original['nombre_usuario'];
		$_SESSION['iniciales_usuario_original'] = $original['iniciales_usuario'];
		$_SESSION['password_usuario_original'] = $original['password_usuario'];
		$_SESSION['u_legajo_original'] = $original['u_legajo'];
		$_SESSION['u_mail_original'] = $original['u_mail'];
		$_SESSION['habilitado_usuario_original'] = $original['habilitado_usuario'];
		$_SESSION['confirma_giros_original'] = $original['confirma_giros'];
		$_SESSION['observaciones_usuario_original'] = $original['observaciones_usuario'];
	}

	public function listar($mensaje = '', $tipo_mensaje = '') {
		$filtro = Array();

		// se establece el campo por el cual ordenar
		$campo_orden = LibreriaGeneral::recoge('campo_orden');
		if ($campo_orden != '') {
			$filtro['campo_orden'] = $campo_orden;
		} else {
			//por defecto
			$filtro['campo_orden'] = $this->campo_orden_por_defecto;
			$_SESSION['ultimo_campo'] = '';
		}

		// se establece el valor a buscar en el modelo
		$filtro['valor_buscado'] = LibreriaGeneral::recoge('valor_buscado');

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
		$_SESSION['filtro_usuarios'] = $filtro;

		// SE ESTABLECE EL FILTRO EN EL MODELO
		$this->modelo->setFiltro($filtro);

		// Se obtiene el listado
		$datos = $this->modelo->listar();

		// Se muestra la grilla
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
			if (isset($datos['id_usuario'])) {
				// Se audita la consulta del registro
				//$this->modelo->auditarConsultaRegistro($datos);

				// Se guarda el registro en sesion para verificar luego si no ha modificado otro usuario
				$this->guardarRegistroOriginal($datos);

				// Se marca para saber que se encuentra en edición
				//$this->modelo->marcarEnEdicion($datos['comp_id']);

				$datos['pagina'] = LibreriaGeneral::recoge('pagina', 1);

				$datos = $this->retirarBarraInvertida($datos);

				$datos['perfiles'] = $this->obtenerPerfiles($id);
			} else {
				// En caso de editarse un NUEVO registro
				$datos = null;
			}
		} else {
			// Si se viene del formulario debido a un error
			$datos = $datos_formulario;
		}

		$this->vista_edicion->mostrar($datos, $mensaje, $tipo_mensaje);
	}

	public function insertar()
	{
		$datos = $_REQUEST;
		
		if ($this->modelo->insertar($datos))
			$this->listar("El Usuario {$datos['codigo_usuario']} se ingres&oacute; con &eacute;xito.", 1);
		else
			$this->listar("Error al ingresar el Usuario {$datos['codigo_usuario']}.", 2);
	}

	public function modificar()
	{
		$datos = $_REQUEST;

		// Se verifica si el registro no ha sido modificado previamente
		if ($this->modelo->noLoModificoOtroUsuario())
		{
			if ($this->modelo->modificar($datos))
				$this->listar("El Usuario " . $datos['codigo_usuario'] . " se modific&oacute; con &eacute;xito.", 1);
			else
				$this->listar("Error al modificar el Usuario " . $datos['codigo_usuario'] . ".", 2);
		} else
			$this->listar("El Usuario " . $datos['codigo_usuario'] . " se ha modificado previamente.", 2);
	}

	public function eliminar()
	{
		$id = LibreriaGeneral::recoge('id', 0);

		if ($this->modelo->eliminar($id))
			$this->listar("El Usuario se elimin&oacute; con &eacute;xito.", 1);
		else
			$this->listar("No es posible eliminar el Usuario, posee informaci&oacute;n histórica a mantener.", 2);
	}

	/**
	 * Se obtienen los perfiles de un usuario, determinado por su Id
	 * de aquellos sistemas que se encuentren habilitados
	 * @param  [integer] $id_usuario Identificador del Usuario
	 * @return [array]               Perfiles del Usuario respectivo
	 */
	public function obtenerPerfiles($id_usuario) {
		$datos_perfiles = $this->modelo->obtenerPerfiles($id_usuario);
		$cantidad = count($datos_perfiles);

		for ($i = 0; $i < $cantidad; $i++) {
			$dato = &$datos_perfiles[$i];

			// 1- SISTEMA DE ADMINISTRACION
			if ($dato['id_sistema'] == 1) {
				// Se asigna el valor del perfil correspondiente a dicho sistema, en caso que posea
				$perfiles['perfil_admin'] = $dato['perfil'];
			}

			// 2- SISTEMA DE EXPEDIENTES
			if ($dato['id_sistema'] == 2) {
				// Se asigna el valor del perfil correspondiente a dicho sistema, en caso que posea
				$perfiles['perfil_exped'] = $dato['perfil'];
			}

			// 3- SISTEMA DE PERSONAL
			if ($dato['id_sistema'] == 3) {
				// Se asigna el valor del perfil correspondiente a dicho sistema, en caso que posea
				$perfiles['perfil_personal'] = $dato['perfil'];
			}

			// 4- SISTEMA DE BIBLIOTECA
			if ($dato['id_sistema'] == 4) {
				// Se asigna el valor del perfil correspondiente a dicho sistema, en caso que posea
				$perfiles['perfil_biblioteca'] = $dato['perfil'];
			}

			// 5- SISTEMA DE INVENTARIO
			if ($dato['id_sistema'] == 5) {
				// Se asigna el valor del perfil correspondiente a dicho sistema, en caso que posea
				$perfiles['perfil_inventario'] = $dato['perfil'];
			}
			
			// 6- SISTEMA DE DEFENSORIA
			if ($dato['id_sistema'] == 6) {
				// Se asigna el valor del perfil correspondiente a dicho sistema, en caso que posea
				$perfiles['perfil_defensoria'] = $dato['perfil'];
			}
		}

		return (isset($perfiles)) ? $perfiles : null;
	}

	/**
	 * Se modifica el estado Habilitado|Deshabilitado
	 */
	public function modificarEstado() {
		parent::modificarEstadoBase();
	}

	/**
	 * Se verifica la existencia del nombre de usuario
	 */
	public function estaDisponibleNombreUsuario()
	{
		$codigo_usuario = LibreriaGeneral::recoge('codigo_usuario');

		// Se obtiene el nombre de usuario en caso de existir
		echo $this->modelo->estaDisponibleNombreUsuario($codigo_usuario);
	}

	/**
	 * Se verifica la existencia del legajo
	 */
	public function existeLegajo()
	{
		$u_legajo = LibreriaGeneral::recoge('u_legajo');

		// Se obtiene la información del Legajo en caso de existir
		$info = $this->modelo->existeLegajo($u_legajo);

		header('Content-type: application/json; charset=utf-8');
		
		echo json_encode($info);
	}

	/**
	 * Obtención del token (server side).
	 * 
	 * @param  [string] $api_url      URL del API endpoint.
	 * @param  [string] $email        Usuario de aplicación que solicitará el token de login remoto.
	 * @param  [string] $password     Password de aplicación que solicitará el token de login remoto.
	 * @param  [string] $remote_email Usuario al cual diferir la autenticación
	 * @param  [string] $remote_name  Nombre descriptivo del usuario al cual diferir la autenticación.
	 * @param  [array]  $extra_data   Array 'diccionario' con parámetros extra.
	 * @return [array]  Si 'status' != 'OK', hay un error (ver 'message'). Resultados en 'data'.
	 */
	private function getRemoteLoginToken($api_url, $user, $password, $remote_email, $remote_name, $extra_data = array())
	{
		// Parametros
		$payload_data = array(
			'email' => $user,
			'password' => $password,
			'remote_email' => $remote_email,
			'remote_name' => $remote_name
		);
		$payload = json_encode(array_merge($payload_data, $extra_data));

		// curl handler config
		$ch = curl_init($api_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Accept: application/json'
		));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

		// Obtener respuesta
		$curl_result = curl_exec($ch);
		$curl_errno = curl_errno($ch);
		$curl_error = curl_error($ch);
		$curl_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		// Error control
		if ($curl_errno > 0) {
			return array('status' => 'ERROR', 'message' => "cURL Error ($curl_errno): $curl_error", 'data' => null);
		}

		// Return response
		$data = json_decode($curl_result, true);

		return (!is_null($data))
			? $data
			: array('status' => 'ERROR', 'message' => 'No se puede decodificar respuesta.', 'data' => null);
	}

	public function ingresarAlSistemaDeBiblioteca() {

		// Primero se comprueba si el perfil actual posee permiso para ejecutar la acción respectiva
		$this->comprobarAcceso($this->perfiles_permitidos_para_ingresar_a_biblioteca, $_SESSION['perfil4']);

		// Obtengo el token de redirección
		$token_data = $this->getRemoteLoginToken(
			API_BIBLIOTECA_URL,
			API_BIBLIOTECA_USER,
			API_BIBLIOTECA_PASSWORD,
			$_SESSION['usuario'].'@concejomdp.gov.ar', //--> Usuario logueado en el SGL
			$_SESSION['nombre_usuario'],
			array(
				'remote_user_id' => $_SESSION['id_usuario'],
				'remote_system_id' => 4, // id del sistema de Biblioteca
				'remote_profile_id' => $_SESSION['perfil4']
			)
		);
		
		// Muestro un link
		if ($token_data['status'] == 'OK') {
			$login_url = $token_data['data']['url'];
			header('Location: '.$login_url);
			exit;
		} else {
			echo sprintf('<p>No se pudo generar el token de acceso: %s</p>', $token_data['message']);
		}
	}

	public function ingresarAlSistemaDeInventario() {

		// Primero se comprueba si el perfil actual posee permiso para ejecutar la acción respectiva
		$this->comprobarAcceso($this->perfiles_permitidos_para_ingresar_a_inventario, $_SESSION['perfil5']);

		// Obtengo el token de redirección
		$token_data = $this->getRemoteLoginToken(
			API_INVENTARIO_URL,
			API_INVENTARIO_USER,
			API_INVENTARIO_PASSWORD,
			$_SESSION['usuario'].'@concejomdp.gov.ar', //--> Usuario logueado en el SGL
			$_SESSION['nombre_usuario'],
			array(
				'remote_user_id' => $_SESSION['id_usuario'],
				'remote_system_id' => 5, // Id del sistema de Inventario
				'remote_profile_id' => $_SESSION['perfil5']
			)
		);
		
		// Muestro un link
		if ($token_data['status'] == 'OK') {
			$login_url = $token_data['data']['url'];
			header('Location: '.$login_url);
			exit;
		} else {
			echo sprintf('<p>No se pudo generar el token de acceso: %s</p>', $token_data['message']);
		}
	}

	/**
	 * Se busca el legajo por el nombre de usuario
	 */
	public function buscarLegajoPoNombreUsuario()
	{
		$nombre_usuario = LibreriaGeneral::recoge('nombre_usuario');

		$partes = explode(' ', $nombre_usuario);
		$nombre = isset($partes[0]) ? $partes[0] : '';
		$apellido = isset($partes[1]) ? $partes[1] : '';

		echo $this->modelo->buscarLegajoPoNombreUsuario($nombre, $apellido);
	}

}
?>

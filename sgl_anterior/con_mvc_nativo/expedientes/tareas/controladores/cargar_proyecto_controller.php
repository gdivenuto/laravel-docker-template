<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//Incluye el modelo que corresponde
require 'modelos/carga_proyectos.php';

//Incluye la vista que corresponde
require 'vistas/cargar_proyecto.php';
require '../abms/vistas/expedientes.php';
require '../abms/vistas/proyectos.php';

class cargar_proyecto_controller extends ControllerBase
{
	const RUTA_ARCHIVOS   = "/sgl/expedientes/proyectos/";
	const RUTA_DIRECTORIO = "/var/www/sgl/expedientes/proyectos/";
	const USUARIO         = 'expe';
	const PASSWORD        = '123456';

	// Tamaño máximo permitido 40MB ( 1024 * 1024 * 40 )
	const TAMANIO_MAXIMO_DOCUMENTO = 41943040;
	// Para el mensaje al usuario
	const MSG_TAMANIO_MAXIMO_DOCUMENTO = "40MB";

	private $id_conexion;
	private $resultado_login;
	private $clave_expediente = Array();
	private $existe_dir;

	public function listar()
	{
	    $clave = Array( 'anio' => Validador::validarParametro('anio'),
						'tipo' => Validador::validarParametro('tipo'),
						'numero' => Validador::validarParametro('numero'),
						'cuerpo' => Validador::validarParametro('cuerpo'),
						'alcance' => Validador::validarParametro('alcance')
					  );

	 	$modelo = new cargarProyectosModel();

	    $vista = new VistaCargarProyecto();

	    // Se verifica que exista el expediente/nota con dicha clave
	    if ( $modelo->existe($clave) )
	    {
		    if ( $_SESSION['perfil2'] == 3 ) // PARA LOS BLOQUES (PERFIL 3)
		    {
				$vista->consultar_carga_bloque($clave);
		    }
		    elseif ( $_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2 ) // PARA NIVEL ADMINISTRATIVO (PERFILES 1 ó 2)
		    {
				$directorio_proyectos_para_cargar = self::RUTA_DIRECTORIO."/temporal/";

				// Se muestra el contenido del directorio respectivo
				$vista->mostrar_contenido($directorio_proyectos_para_cargar, $clave);
		    }
		}
	    else
	    {
	    	if ( $clave['tipo'] == 'E')
	    	{
	    		$mensaje = "EXPEDIENTE NO ENCONTRADO EN EL SISTEMA";
	    	}
	    	elseif ( $clave['tipo'] == 'N')
	    	{
	    		$mensaje = "NOTA NO ENCONTRADA EN EL SISTEMA";
	    	}
	    	else
	    	{
	    		$mensaje = "RECOMENDACION NO ENCONTRADA EN EL SISTEMA";
	    	}

	    	// Se vuelve al listado principal de expedientes, informando al usuario del documento no encontrado en el sistema
	    	$vista->volverListadoPrincipal($clave, $mensaje);
	    }
	}

	// 21/05/2015, XXXX, QUITÉ EL PARÁMETRO
	public function armar_nombre_documento()
	{
	    $this->clave_expediente['pftp_anio'] = Validador::validarParametro('pftp_anio');
	    $this->clave_expediente['pftp_tipo'] = Validador::validarParametro('pftp_tipo');
	    $this->clave_expediente['pftp_numero'] = Validador::validarParametro('pftp_numero');

	    // SE ARMA EL NOMBRE DEL DOCUMENTO .doc PARA EL RESPECTIVO EXPEDIENTE
	    $anio_corto = substr($this->clave_expediente['pftp_anio'], -2);
	    $tipo = $this->clave_expediente['pftp_tipo'];
	    $aux_numero = 100000+$this->clave_expediente['pftp_numero'];
	    $numero = substr($aux_numero, -5);

	    return  $anio_corto.$tipo.$numero;
	}

	// PARA EL USUARIO DE BLOQUES
	public function upload_bloque()
	{
	    $nombre_documento = $this->armar_nombre_documento();

	    // SI SE VUELVE DE LA PREGUNTA AL USUARIO POR LA SOBREESCRITURA DEL DOCUMENTO
	    if ( Validador::validarParametro('se_vuelve') == 'si' )
	    {
		    $se_vuelve = Validador::validarParametro('pftp_se_vuelve');
	    }
	    else
	    {
		    $se_vuelve = 'no';
	    }

	    $vista = new VistaCargarProyecto();
	    // SE VERIFICA SI EXISTE EL DOCUMENTO AATNNNNN.doc
	    if ( ($se_vuelve == 'no') && ( is_file(self::RUTA_DIRECTORIO."temporal/".$nombre_documento.".doc")) )
	    {
		    $vista->preguntar_usuario_bloque($_REQUEST);// SE PREGUNTA SI SE DESEA SOBREESCRIBIR
	    }
	    else
	    {
		    // SE MUESTRA EL FORMULARIO PARA CARGAR EL PROYECTO .doc
		    $vista->upload_bloque($_REQUEST);
	    }
	}

	/**
	 * Para el perfil de Bloques, cuando cargan en temporal/ el documento del proyecto
	 */
	public function procesar_upload_bloque()
	{
		$vista = new VistaCargarProyecto();

		// Si NO se ha recibido el documento
	    if ( $_FILES['proyecto_subido']['error'] == 4 ) {
			// Retorna al listado principal de expedientes con un mensaje determinado al usuario
		    $vista->retornar("No se ha recibido el documento.", 2, $_POST);
	    }
	    // Si el documento recibido excede la directiva upload_max_filesize de php.ini
	    elseif ( $_FILES['proyecto_subido']['error'] == 1 ) {
			// Retorna al listado principal de expedientes con un mensaje determinado al usuario
		    $vista->retornar("El documento supera el tama&ntilde;o m&aacute;ximo permitido de ".self::MSG_TAMANIO_MAXIMO_DOCUMENTO, 2, $_POST);
	    }
	    // Si el documento recibido excede la directiva MAX_FILE_SIZE especificada en el formulario HTML
	    elseif ( $_FILES['proyecto_subido']['error'] == 2 ) {
			// Retorna al listado principal de expedientes con un mensaje determinado al usuario
		    $vista->retornar("El documento supera el tama&ntilde;o m&aacute;ximo permitido de ".self::MSG_TAMANIO_MAXIMO_DOCUMENTO, 2, $_POST);
	    }
	    // Si el documento fue parcialmente subido
	    elseif ( $_FILES['proyecto_subido']['error'] == 3 ) {
			// Retorna al listado principal de expedientes con un mensaje determinado al usuario
		    $vista->retornar("El documento fue parcialmente subido", 2, $_POST);
	    }
	    // Una extensión de PHP detuvo la subida de ficheros
	    elseif ( $_FILES['proyecto_subido']['error'] == 8 ) {
			// Retorna al listado principal de expedientes con un mensaje determinado al usuario
		    $vista->retornar("Una extensión de PHP detuvo la subida de ficheros", 2, $_POST);
	    }
	    // Si el documento fue recibido sin errores
	    elseif ( $_FILES['proyecto_subido']['error'] == 0 ) {
	    	// Si el tamaño del documento supera el límite determinado
	    	if ( $_FILES['proyecto_subido']['size'] > self::TAMANIO_MAXIMO_DOCUMENTO ) {
	    		// Retorna al listado principal de expedientes con un mensaje determinado al usuario
	    		$vista->retornar("El documento supera el tama&ntilde;o m&aacute;ximo permitido de ".self::MSG_TAMANIO_MAXIMO_DOCUMENTO, 2, $_POST);
	    	} else {
	    		// Si el documento fue subido
				if ( is_uploaded_file($_FILES['proyecto_subido']['tmp_name']) ) {
					// Se extrae su extensión
					$extension = strtolower(end(explode('.', $_FILES['proyecto_subido']['name'])));

					// Se verifica si se ha subido un archivo con extensión .doc, .docx U .odt
					if ( strtolower($extension) == 'doc' || strtolower($extension) == 'docx' || strtolower($extension) == 'odt') {

						$directorio_destino_por_bloque = self::RUTA_DIRECTORIO."temporal/";

						// Se establece una conexión FTP
						$this->id_conexion = ftp_connect('localhost');

						// Se establece el inicio de sesión FTP con usuario y password
						$this->resultado_login = ftp_login($this->id_conexion, self::USUARIO, self::PASSWORD);

						// Se chequea la conexión FTP
						if ( ( !$this->id_conexion ) || ( !$this->resultado_login ) ) {
							// Retorna al listado principal de expedientes con un mensaje determinado al usuario
						    $vista->retornar("Error al intentar conectarse o autentificarse en el Servidor FTP.", 2, $_POST);
						} else {
						    // Se arma el nombre del directorio para el expediente respectivo
						    $nombre_documento = $this->armar_nombre_documento($_POST);

							// Se cambia al directorio donde se quiere subir el archivo
							// ( /var/www/sgl/expedientes/proyectos/temporal/ )
							ftp_chdir($this->id_conexion, $directorio_destino_por_bloque);

							$dir_actual = ftp_pwd($this->id_conexion);

							// Documento a cargar
							$archivo_local = $_FILES['proyecto_subido']['tmp_name'];
							// Ruta final donde se cargará el documento
							$ruta_archivo_remoto = $dir_actual."/".$nombre_documento.".doc";

							// Se carga el documento
							if ( ftp_put($this->id_conexion, $ruta_archivo_remoto, $archivo_local, FTP_BINARY) ) {
								// Se cierra la conexión FTP
								ftp_close($this->id_conexion);

								$texto_segun_tipo = ($_POST['pftp_tipo'] == 'E') ? "el Expediente " : "la Nota ";

							    // Retorna al listado principal de expedientes con un mensaje determinado al usuario
							    $vista->retornar("Se ha cargado el documento satisfactoriamente para ".$texto_segun_tipo.$_POST['pftp_anio']." ".$_POST['pftp_tipo']." ".$_POST['pftp_numero'], 1, $_POST);
							} else {
								// Se cierra la conexión FTP
								ftp_close($this->id_conexion);

							    // Retorna al listado principal de expedientes con un mensaje determinado al usuario
							    $vista->retornar("La transferencia del documento ha fallado!", 2, $_POST);
							}
						}
					} else {
						// Retorna al listado principal de expedientes con un mensaje determinado al usuario
					    $vista->retornar("La extensi&oacute;n del documento no es correcta. Se permiten extensiones .doc, .docx u .odt solamente.", 2, $_POST);
					}
				} else {
					// Retorna al listado principal de expedientes con un mensaje determinado al usuario
				    $vista->retornar("No se ha cargado el documento: ".$_FILES['proyecto_subido']['tmp_name'], 2, $_POST);
				}
	    	}
	    }
	}

	// PARA EL USUARIO DE PERFIL ADMINISTRADOR O SUPERVISOR
	public function pasar_proyectos()
	{
		$modelo = new cargarProyectosModel();
	    $originales_existentes = Array();
	    $posicion_existentes = 0;

		$clave_expediente = Array('anio' => $_POST['f_anio'],
								  'tipo' => $_POST['f_tipo'],
								  'numero' => $_POST['f_numero'],
								  'cuerpo' => $_POST['f_cuerpo'],
								  'alcance' => $_POST['f_alcance']
								 );

	    // POR CADA ARCHIVO RECIBIDO
	    foreach ($_POST as $valor)
	    {
			// SE EXTRAE SU EXTENSION
			$extension = strtolower(end(explode('.', $valor)));

			// SE TRABAJA CON LOS .doc SOLAMENTE
			if ( $extension == "doc" )
			{
				// DOCUMENTO A PASAR POR FTP
				$documento = $valor;

				// SE SEPARA EL NOMBRE Y LA EXTENSION DEL DOCUMENTO
				$division = explode('.',$documento);
				// SE TOMA EL NOMBRE DEL DOCUMENTO (EL AATNNNNN)
				$nombre = $division[0];

				// SE TOMAN LOS DOS PRIMEROS DIGITOS QUE CORRESPONDEN A LOS DOS ULTIMOS DIGITOS DEL AÑO
				$anio_corto = substr($nombre, 0, 2);

				if ( $anio_corto >= 83 && $anio_corto <= 99 )
					$anio = "19".$anio_corto;
				else
					$anio = "20".$anio_corto;

				// RUTA TEMPORAL DONDE ESTÁN LOS PROYECTOS PARA CARGAR
				$directorio_desde = self::RUTA_DIRECTORIO."temporal/";

				// RUTA DESTINO DONDE SE CARGARÁ EL PROYECTO
				$directorio_destino = self::RUTA_DIRECTORIO.$anio."/".$nombre."/";

				// SE VERIFICA SI EXISTE original.doc, RETORNA SU NOMBRE EN CASO AFIRMATIVO (PUEDE ESTAR EN MINÚSCULA O EN MAYÚSCULA)
				$nombre_original_existente = $this->existeOriginal($directorio_destino);

				// SI EXISTE original.doc
				if ( $nombre_original_existente != '' )
				{
					// SE ALMACENAN LOS DATOS DEL original.doc EXISTENTE PARA LA POSTERIOR PREGUNTA AL USUARIO DE SOBREESCRITURA O RENOMBRADO
					$originales_existentes[$posicion_existentes]['directorio_desde'] = $directorio_desde;
					$originales_existentes[$posicion_existentes]['directorio_destino'] = $directorio_destino;
					$originales_existentes[$posicion_existentes]['documento'] = $documento;// DOCUMENTO TOMADO DE /temporal
					$originales_existentes[$posicion_existentes]['nombre_original_existente'] = $nombre_original_existente;
					$originales_existentes[$posicion_existentes]['anio'] = $anio;
					$originales_existentes[$posicion_existentes]['nombre_directorio'] = $nombre;// NOMBRE DEL DIRECTORIO DEL original.doc EXISTENTE
					$originales_existentes[$posicion_existentes]['cargado'] = 'no';

					$posicion_existentes++;
				}
				else // SI NO EXISTE
				{
					// SE ABRE EL DIRECTORIO temporal PARA TOMAR EL DOCUMENTO
					$dir = opendir($directorio_desde);

					// SE ESTABLECE UNA CONEXION FTP
					$this->id_conexion = ftp_connect('localhost');

					// SE ESTABLECE EL INICIO DE SESION FTP CON USUARIO Y PASSWORD
					$this->resultado_login = ftp_login($this->id_conexion, self::USUARIO, self::PASSWORD);

					// SE CHEQUEA LA CONEXION
					if ( ( !$this->id_conexion ) || ( !$this->resultado_login ) ) {
						$mensaje = "Error al intentar conectarse o autentificarse en el Servidor FTP.";
						$tipo_mensaje = 2;

					} else {
						// SE CAMBIA AL DIRECTORIO DONDE SE QUIERE SUBIR EL ARCHIVO
						// ( /var/www/sgl/expedientes/proyectos/AAAA/AATNNNNN/ )
						ftp_chdir($this->id_conexion, $directorio_destino);

						$dir_actual = ftp_pwd($this->id_conexion);

						/****************************************************************************************************************************/
						/*	IMPORTANTE: EL DIRECTORIO DESTINO AATNNNNN SE GENERA AUTOMÁTICAMENTE MEDIANTE UN SCRIPT EN EL SERVIDOR DE PRODUCCIÓN	*/
						/****************************************************************************************************************************/

						// SI EXISTE EL DIRECTORIO DONDE SE GUARDARÁ EL DOCUMENTO
						if ( is_dir($directorio_destino) ) {

							$archivo_remoto = $dir_actual."/original.doc";
							$archivo_local = $directorio_desde.$documento;

							// SE CARGA EL ARCHIVO COMO original.doc (EN MINUSCULA)
							if ( ftp_put($this->id_conexion, $archivo_remoto, $archivo_local, FTP_BINARY) ) {

								$mensaje = "Se ha cargado el documento satisfactoriamente como original.doc";
								$tipo_mensaje = 1;

								// SE ELIMINA EL DOCUMENTO DEL DIRECTORIO /temporal
								ftp_delete($this->id_conexion, $directorio_desde.$documento);

								// Se obtiene la clave del expediente/nota a partir del nombre codificado
								$clave = $this->obtenerClaveDeNombre($nombre);

								// Se registra la carga del Proyecto en Auditoría
								$this->auditarCargaProyecto($clave, "PROYECTO CARGADO", "Se carga el documento original.doc");
							} else {
								$mensaje = "La transferencia del documento ha fallado, el directorio ".$nombre." puede que no exista.";
								$tipo_mensaje = 2;
							}
						} else {
							$mensaje = "El directorio ".$nombre." no existe.";
							$tipo_mensaje = 2;
						}
					}

					// SE CIERRA LA SECUENCIA FTP
					ftp_close($this->id_conexion);

					// SE CIERRA EL DIRECTORIO
					closedir($dir);
				}
			}
	    }

	    // SI HAY originales EXISTENTES SE PREGUNTA AL USUARIO POR CADA UNO DE ELLOS SI DESEA SOBREESCRIBIR O RENOMBRAR
	    if ( $originales_existentes[0]['nombre_original_existente'] != '' )
	    {
			$_SESSION['originales_existentes'] = $originales_existentes;
			$_SESSION['numero_cargados'] = 0;

			// SE CONSULTA AL USUARIO QUE HACER CON LOS originales EXISTENTES
			$this->consultarUsuario($clave_expediente);
		}
		else
		{
			$_SESSION['originales_existentes'] = null;
			$_SESSION['mensaje']               = $mensaje;
			$_SESSION['tipo_mensaje']          = $tipo_mensaje;

			// 03/04/2019 XXXX
			// Vuelve en el lugar de la grilla del expediente que se estaba visualizando (el del buscador)
			$this->volverAlInicio($clave_expediente);
		}
	}

	/**
	 * Para volver al expediente que se estaba visualizando (el del buscador)
	 */
	public function volverAlInicio($clave_expediente) {
		// URL para volver al expediente que se estaba visualizando (el del buscador)
		$url = "index.php?anio=".$clave_expediente['anio']."&tipo=".$clave_expediente['tipo']."&numero=".$clave_expediente['numero']."&cuerpo=".$clave_expediente['cuerpo']."&alcance=".$clave_expediente['alcance']."&sentido=anterior";
		?>
		<script type="text/javascript">
			location.href = '<?php echo $url; ?>';
		</script>
		<?php
	}

	public function consultarUsuario($clave_expediente = null) {

		if ($clave_expediente == null) {
			$clave_expediente = Array('anio' => Validador::validarParametro('f_anio'),
									  'tipo' => Validador::validarParametro('f_tipo'),
									  'numero' => Validador::validarParametro('f_numero'),
									  'cuerpo' => Validador::validarParametro('f_cuerpo'),
									  'alcance' => Validador::validarParametro('f_alcance')
									 );
		}

		$cantidad = count($_SESSION['originales_existentes']);

		// SI QUEDA UN EXISTENTE PARA CARGAR
		if ( $_SESSION['numero_cargados'] < $cantidad ) {
			$posicion = $_SESSION['numero_cargados'];

			// SI NO ESTA CARGADO
			if ( $_SESSION['originales_existentes'][$posicion]['cargado'] == 'no' ) {
				// SE MARCA COMO CARGADO EN LA SESION
				$_SESSION['originales_existentes'][$posicion]['cargado'] = 'si';
				$_SESSION['numero_cargados']++;

				// SE PREGUNTA SI DESEA SOBREESCRIBIRLO O RENOMBRARLO
				$vista = new VistaCargarProyecto();
				$vista->preguntar_por_original_doc($_SESSION['originales_existentes'][$posicion]['directorio_desde'], $_SESSION['originales_existentes'][$posicion]['directorio_destino'], $_SESSION['originales_existentes'][$posicion]['documento'], $_SESSION['originales_existentes'][$posicion]['nombre_original_existente'], $_SESSION['originales_existentes'][$posicion]['anio'], $_SESSION['originales_existentes'][$posicion]['nombre_directorio']);
			}
		} else
			$this->volverAlInicio($clave_expediente);
	}

	// EN CASO DE EXISTIR RETORNA EL NOMBRE (PUEDE ESTAR EN MINÚSCULA O EN MAYÚSCULA)
	public function existeOriginal($directorio_destino)
	{
		$existe = false;
		$nombre_del_original = '';

		// SE ABRE EL DIRECTORIO DONDE SE CARGARA EL PROYECTO (original.doc)
		if ( $dir_abierto = @opendir($directorio_destino) )
		{
			// MIENTRAS NO SE HAYA TERMINADO DE RECORRER EL DIRECTORIO Y NO SE HAYA ENCONTRADO original.doc
			while ( ( false !== ( $file = readdir($dir_abierto) ) && !$existe ) )
			{
				if ( $file != '..' && $file != '.' )
				{
					// SI SE ENCUENTRA original.doc (SE LO BUSCA EN MINUSCULA)
					if ( "original.doc" == strtolower($file) )
					{
						$existe = true;
						$nombre_del_original = $file;// SE TOMA EL NOMBRE PORQUE PUEDE ESTAR EN MINUSCULA O MAYUSCULA
					}
				}
			}
			// SE CIERRA EL DIRECTORIO
			closedir($dir_abierto);
		}

		return $nombre_del_original;
	}

	public function renombrarOriginalAnterior()
	{
		$modelo = new cargarProyectosModel();

		$directorio_desde = Validador::validarParametro('directorio_desde');
		$directorio_destino = Validador::validarParametro('directorio_destino');
		$documento = Validador::validarParametro('documento');
		$nombre_original_existente = Validador::validarParametro('nombre_original_existente');
		// Para la auditoría de la sobreescritura del documento
		$clave_a_separar = Validador::validarParametro('clave_a_separar');

		$clave_expediente = Array('anio' => Validador::validarParametro('f_anio'),
								  'tipo' => Validador::validarParametro('f_tipo'),
								  'numero' => Validador::validarParametro('f_numero'),
								  'cuerpo' => Validador::validarParametro('f_cuerpo'),
								  'alcance' => Validador::validarParametro('f_alcance')
								 );

		// SE ABRE EL DIRECTORIO temporal PARA TOMAR EL DOCUMENTO
		$dir = opendir($directorio_desde);

		// SE ESTABLECE UNA CONEXION FTP
		$this->id_conexion = ftp_connect('localhost');

		// SE ESTABLECE EL INICIO DE SESION FTP CON USUARIO Y PASSWORD
		$this->resultado_login = ftp_login($this->id_conexion, self::USUARIO, self::PASSWORD);

		// SE CHEQUEA LA CONEXION
		if ( ( !$this->id_conexion ) || ( !$this->resultado_login ) )
		{
			$mensaje = "Error al intentar conectarse o autentificarse en el Servidor FTP.";
			$tipo_mensaje = 2;
		}
		else
		{
			// SE CAMBIA AL DIRECTORIO DONDE SE QUIERE SUBIR EL ARCHIVO, ( /var/www/sgl/expedientes/proyectos/AAAA/AATNNNNN/ )
			ftp_chdir($this->id_conexion, $directorio_destino);

			$dir_actual = ftp_pwd($this->id_conexion);

			// RUTA CON EL NOMBRE ANTERIOR
			$ruta_nombre_anterior = $dir_actual."/".$nombre_original_existente;

			// RUTA CON EL NOMBRE NUEVO PARA ASIGNARSELO AL ORIGINAL ANTERIOR
			$ruta_nombre_nuevo = $dir_actual."/original_anterior_".date("d_m_Y_H_i_s").".doc";

			// SE RENOMBRA original.doc COMO 'original_anterior_anio_mes_dia_hora_min_seg.doc' PARA NO PERDERLO
			if ( ftp_rename($this->id_conexion, $ruta_nombre_anterior, $ruta_nombre_nuevo) )
			{
				// SE CARGA EL ARCHIVO COMO original.doc (EN MINUSCULA)
				if ( ftp_put($this->id_conexion, $dir_actual."/original.doc", $directorio_desde.$documento, FTP_BINARY) )
				{
					$mensaje = "Se ha cargado el documento satisfactoriamente como original.doc!";
					$tipo_mensaje = 1;

					// SE ELIMINA EL DOCUMENTO DEL DIRECTORIO /temporal
					ftp_delete($this->id_conexion, $directorio_desde.$documento);

					// Se divide el nombre y la extensión del documento
					$division = explode('.',$documento);
					// Se toma el nombre del documento (el AATNNNNN)
					$nombre = $division[0];

					// Se obtiene la clave del expediente/nota a partir del nombre codificado
					$clave = $this->obtenerClaveDeNombre($nombre);

					// Se registra el renombrado del documento en Auditoría
					$this->auditarCargaProyecto($clave, "PROYECTO AGREGADO", "Se agrega un documento a un proyecto que ya se encuentra cargado, se renombra el anterior para mantenerlo.");
				}
				else
				{
					$mensaje = "La transferencia del documento ha fallado!";
					$tipo_mensaje = 2;
				}
			}
			else
			{
				$mensaje = "Hubo un problema al renombrar el original.doc existente.";
				$tipo_mensaje = 2;
			}
		}

		// SE CIERRA LA SECUENCIA FTP
		ftp_close($this->id_conexion);

		// SE CIERRA EL DIRECTORIO
		closedir($dir);

		$_SESSION['mensaje'] = $mensaje;
		$_SESSION['tipo_mensaje'] = $tipo_mensaje;

		// SE VUELVE A CONSULTAR AL USUARIO
		$this->consultarUsuario($clave_expediente);
	}

	/**
	 * Se sobreescribe el documento del proyecto, previamente cargado
	 * @return [type] [description]
	 */
	public function sobreescribirOriginal()
	{
		$modelo = new cargarProyectosModel();

		$directorio_desde          = Validador::validarParametro('directorio_desde');
		$directorio_destino        = Validador::validarParametro('directorio_destino');
		$documento                 = Validador::validarParametro('documento');
		$nombre_original_existente = Validador::validarParametro('nombre_original_existente');

		// Para la auditoría de la sobreescritura del documento
		$clave_a_separar = Validador::validarParametro('clave_a_separar');

		$clave_expediente = Array('anio' => Validador::validarParametro('f_anio'),
								  'tipo' => Validador::validarParametro('f_tipo'),
								  'numero' => Validador::validarParametro('f_numero'),
								  'cuerpo' => Validador::validarParametro('f_cuerpo'),
								  'alcance' => Validador::validarParametro('f_alcance')
								 );

		// SE ABRE EL DIRECTORIO temporal PARA TOMAR EL DOCUMENTO
		$dir = opendir($directorio_desde);

		// SE ESTABLECE UNA CONEXION FTP
		$this->id_conexion = ftp_connect('localhost');

		// SE ESTABLECE EL INICIO DE SESION FTP CON USUARIO Y PASSWORD
		$this->resultado_login = ftp_login($this->id_conexion, self::USUARIO, self::PASSWORD);

		// SE CHEQUEA LA CONEXION
		if ( ( !$this->id_conexion ) || ( !$this->resultado_login ) )
		{
			$mensaje = "Error al intentar conectarse o autentificarse en el Servidor FTP.";
			$tipo_mensaje = 2;
		}
		else
		{
			// SE CAMBIA AL DIRECTORIO DONDE SE QUIERE SUBIR EL ARCHIVO, ( /var/www/sgl/expedientes/proyectos/AAAA/AATNNNNN/ )
			ftp_chdir($this->id_conexion, $directorio_destino);

			$dir_actual = ftp_pwd($this->id_conexion);

			// SE ELIMINA EL original EXISTENTE PARA CARGAR EL NUEVO
			ftp_delete($this->id_conexion, $dir_actual."/".$nombre_original_existente);

			// SE CARGA EL ARCHIVO COMO original.doc (EN MINUSCULA)
			if ( ftp_put($this->id_conexion, $dir_actual."/original.doc", $directorio_desde.$documento, FTP_BINARY) )
			{
				$mensaje = "Se ha cargado el documento satisfactoriamente como original.doc!";
				$tipo_mensaje = 1;

				// SE ELIMINA EL DOCUMENTO DEL DIRECTORIO /temporal
				ftp_delete($this->id_conexion, $directorio_desde.$documento);

				// Se divide el nombre y la extensión del documento
				$division = explode('.',$documento);
				// Se toma el nombre del documento (el AATNNNNN)
				$nombre = $division[0];

				// Se obtiene la clave del expediente/nota a partir del nombre codificado
				$clave = $this->obtenerClaveDeNombre($nombre);

				// Se registra el reemplazo del Proyecto en Auditoría
				$this->auditarCargaProyecto($clave, "PROYECTO REEMPLAZADO", "Se reemplaza el documento original.doc");
			}
			else
			{
				$mensaje = "La transferencia del documento ha fallado!";
				$tipo_mensaje = 2;
			}
		}

		//SE CIERRA LA SECUENCIA FTP
		ftp_close($this->id_conexion);

		// SE CIERRA EL DIRECTORIO
		closedir($dir);

		$_SESSION['mensaje'] = $mensaje;
		$_SESSION['tipo_mensaje'] = $tipo_mensaje;

		// SE VUELVE A CONSULTAR AL USUARIO
		$this->consultarUsuario($clave_expediente);
	}

	public function mostrar_contenido_dir()
	{
	    $anio = Validador::validarParametro('pftp_anio');
	    // SE ARMA EL NOMBRE DEL DIRECTORIO PARA EL EXPEDIENTE RESPECTIVO
	    $nombre_directorio = $this->armar_nombre_documento($_REQUEST);

	    $directorio_proyectos = self::RUTA_DIRECTORIO.$anio."/".$nombre_directorio."/";
	    $archivo_proyectos = self::RUTA_ARCHIVOS.$anio."/".$nombre_directorio."/";

	    $vistaProyecto = new VistaProyectos();
	    $vistaProyecto->mostrar_contenido_dir($directorio_proyectos, $archivo_proyectos, $_REQUEST);
	}

	/**
	 * Se audita la carga de un Proyecto
	 * @param  array 	$clave_expediente
	 * @param  string 	$observaciones
	 */
	public function auditarCargaProyecto($clave_expediente, $operacion, $observaciones) {

		$modelo = new auditoriaExpedientesModel();

		$datos_log = Array();
		$datos_log['operacion_log']     = $operacion;
		$datos_log['tabla_log']         = "";
		$datos_log['anio_log']          = $clave_expediente['anio'];
		$datos_log['tipo_log']          = $clave_expediente['tipo'];
		$datos_log['numero_log']        = $clave_expediente['numero'];
		$datos_log['cuerpo_log']        = $clave_expediente['cuerpo'];
		$datos_log['alcance_log']       = $clave_expediente['alcance'];
		$datos_log['fecha_log']         = "null";
		$datos_log['orden_log']         = "null";
		$datos_log['observaciones_log'] = $observaciones;

		// Se audita la carga de los Proyectos
		$modelo->registrarMovimiento($datos_log);
	}

}
?>

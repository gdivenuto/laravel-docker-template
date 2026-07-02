<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//Incluye el modelo que corresponde
require 'modelos/cargar_digitalizacion.php';

//Incluye la vista que corresponde
require 'vistas/cargar_digitalizacion.php';
require '../abms/vistas/expedientes.php';
require '../abms/vistas/proyectos.php';

class cargar_digitalizacion_controller extends ControllerBase
{
	const RUTA_ARCHIVOS   = "/sgl/expedientes/proyectos/";
	const RUTA_DIRECTORIO = "/var/www/sgl/expedientes/proyectos/";
	const USUARIO         = 'expe';
	const PASSWORD        = '123456';

	// NO UTILIZADO, POR EL MOMENTO NO HAY LÍMITE DE TAMAÑO
	// Tamaño máximo permitido de 20MB ( 2MB = 1024*1024*2*10 )
	//const TAMANIO_MAXIMO_DOCUMENTO = 20971520;

	private $id_conexion;
	private $resultado_login;
	private $clave_expediente = Array();
	private $existe_dir;

	/**
	 * Primero se verifica que exista el Expediente o la Nota
	 * Luego, se muestra el contenido del directorio "digital/" para que el usuario elija las digitalizaciones a cargar
	 */
	public function listar() {

	    $clave = Array( 'anio' => Validador::validarParametro('anio'),
						'tipo' => Validador::validarParametro('tipo'),
						'numero' => Validador::validarParametro('numero'),
						'cuerpo' => Validador::validarParametro('cuerpo'),
						'alcance' => Validador::validarParametro('alcance')
					  );

	 	$modelo = new cargarDigitalizacionModel();

	    $vista = new VistaCargarDigitalizacion();

	    // Se verifica que exista el expediente/nota con dicha clave
	    if ( $modelo->existe($clave) ) {
		 //    // PARA LOS BLOQUES (PERFIL 3)
		 //    if ( $_SESSION['perfil2'] == 3 )
			// 	$vista->consultar_carga_bloque($clave);
			// // PARA NIVEL ADMINISTRATIVO (PERFILES 1 ó 2)
		 //    elseif ( $_SESSION['perfil2'] == 1 || $_SESSION['perfil2'] == 2 ) {
				$directorio_proyectos_para_cargar = self::RUTA_DIRECTORIO."digital/";
				// Se muestra el contenido del directorio respectivo
				$vista->mostrar_contenido($directorio_proyectos_para_cargar, $clave);
		    //}
		} else {
	    	if ( $clave['tipo'] == 'E')
	    		$mensaje = "EXPEDIENTE NO ENCONTRADO EN EL SISTEMA";
	    	elseif ( $clave['tipo'] == 'N')
	    		$mensaje = "NOTA NO ENCONTRADA EN EL SISTEMA";
	    	else
	    		$mensaje = "RECOMENDACION NO ENCONTRADA EN EL SISTEMA";

	    	// Se vuelve al listado principal de expedientes, informando al usuario del documento no encontrado en el sistema
	    	$vista->volverListadoPrincipal($clave, $mensaje);
	    }
	}

	/**
	 * Se arma el nombre de la digitalización con el formato AATNNNNN
	 * @return [string] Nombre
	 */
	public function armar_nombre_documento() {

		$this->clave_expediente['pftp_anio']   = Validador::validarParametro('pftp_anio');
		$this->clave_expediente['pftp_tipo']   = Validador::validarParametro('pftp_tipo');
		$this->clave_expediente['pftp_numero'] = Validador::validarParametro('pftp_numero');

	    // SE ARMA EL NOMBRE DEL DOCUMENTO .pdf PARA EL RESPECTIVO EXPEDIENTE
		$anio_corto = substr($this->clave_expediente['pftp_anio'], -2);
		$tipo       = $this->clave_expediente['pftp_tipo'];
		$aux_numero = 100000+$this->clave_expediente['pftp_numero'];
		$numero     = substr($aux_numero, -5);

	    return  $anio_corto.$tipo.$numero;
	}

	/**
	 * 03/04/2019 XXXX, ahora permite reemplazar la digitalización al consultarle al usuario previamente
	 *
	 * Se finaliza la carga de la digitalización
	 * @return [type] [description]
	 */
	public function pasar_digitalizaciones() {

		$modelo                      = new cargarDigitalizacionModel();
		$digitalizaciones_existentes = Array();
		$posicion_existentes         = 0;

		$clave_expediente = Array('anio' => Validador::validarParametro('f_anio'),
								  'tipo' => Validador::validarParametro('f_tipo'),
								  'numero' => Validador::validarParametro('f_numero'),
								  'cuerpo' => Validador::validarParametro('f_cuerpo'),
								  'alcance' => Validador::validarParametro('f_alcance')
								 );

	    // POR CADA ARCHIVO RECIBIDO
	    foreach ($_POST as $valor) {
			// SE EXTRAE SU EXTENSION
			$extension = strtolower(end(explode('.', $valor)));
			// SE TRABAJA CON LOS .pdf SOLAMENTE
			if ( $extension === "pdf" ) {
				// DOCUMENTO A PASAR POR FTP
				$documento = $valor;

				// SE SEPARA EL NOMBRE Y LA EXTENSION DEL DOCUMENTO
				$division = explode('.',$documento);

				// 13/09/2019 XXXX se convierte a mayúscula el nombre del documento
				// para evitar el AAtNNNNN
				// SE TOMA EL NOMBRE DEL DOCUMENTO (EL AAENNNNN)
				$nombre = mb_strtoupper($division[0]);

				// 21/05/2019
				// Se toma la parte del nombre correspondiente a la codificación AAENNNNN.
				// Esto se realiza porque ahora al digitalizar un expediente
				// pueden agregar la letra "a" ó "A" en el nombre codificado.
				// Tener sólo el nombre codificado permite utilizarlo para el directorio respectivo:
				// "proyectos/AAAA/AAENNNNN/"
				$nombre_limpio = substr($nombre, 0, 8);

				// SE TOMAN LOS DOS PRIMEROS DIGITOS QUE CORRESPONDEN A LOS DOS ULTIMOS DIGITOS DEL AÑO
				$anio_corto = substr($nombre, 0, 2);
				// Se completa el año
				$anio = ( $anio_corto >= 83 && $anio_corto <= 99 ) ? "19".$anio_corto : "20".$anio_corto;

				// RUTA TEMPORAL DONDE ESTÁN LOS PROYECTOS PARA CARGAR
				$directorio_desde = self::RUTA_DIRECTORIO."digital/";

				// RUTA DESTINO DONDE SE CARGARÁ EL PROYECTO
				$directorio_destino = self::RUTA_DIRECTORIO.$anio."/".$nombre_limpio."/";

				// Se verifica si ya existe el documento AAENNNNN.pdf
				if ( file_exists($directorio_destino.$nombre_limpio.".pdf") ) {
					// SE ALMACENAN LOS DATOS DEL pdf EXISTENTE PARA LA POSTERIOR PREGUNTA AL USUARIO DE SOBREESCRITURA
					$digitalizaciones_existentes[$posicion_existentes]['directorio_desde']   = $directorio_desde;
					$digitalizaciones_existentes[$posicion_existentes]['directorio_destino'] = $directorio_destino;
					$digitalizaciones_existentes[$posicion_existentes]['documento']          = $documento;// DOCUMENTO TOMADO DE /digital
					$digitalizaciones_existentes[$posicion_existentes]['anio']               = $anio;
					$digitalizaciones_existentes[$posicion_existentes]['nombre_directorio']  = $nombre_limpio;// NOMBRE DEL DIRECTORIO (de formato AAENNNNN)
					$digitalizaciones_existentes[$posicion_existentes]['cargado']            = 'no';

					$posicion_existentes++;
				}
				else // SI NO EXISTE, se carga el pdf normalmente
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
						// SE CAMBIA AL DIRECTORIO DONDE SE QUIERE SUBIR EL ARCHIVO, ( /var/www/sgl/expedientes/proyectos/AAAA/AATNNNNN/ )
						ftp_chdir($this->id_conexion, $directorio_destino);

						$dir_actual = ftp_pwd($this->id_conexion);

						/****************************************************************************************************************************/
						/*	IMPORTANTE: EL DIRECTORIO DESTINO AATNNNNN SE GENERA AUTOMÁTICAMENTE MEDIANTE UN SCRIPT EN EL SERVIDOR DE PRODUCCIÓN	*/
						/****************************************************************************************************************************/

						// SI EXISTE EL DIRECTORIO DONDE SE GUARDARÁ EL DOCUMENTO
						if ( is_dir($directorio_destino) ) {
							// SE CARGA la digitalización, con la nomenclatura AATNNNNN.pdf
							if ( ftp_put($this->id_conexion, $dir_actual."/".$nombre_limpio.".pdf", $directorio_desde.$documento, FTP_BINARY) ) {

								$mensaje = "Se ha cargado la digitalizaci&oacute;n";
								$tipo_mensaje = 1;

								// SE ELIMINA EL DOCUMENTO DEL DIRECTORIO /digital
								ftp_delete($this->id_conexion, $directorio_desde.$documento);

								// Se obtiene la clave del expediente/nota a partir del nombre codificado
								$clave = $this->obtenerClaveDeNombre($nombre_limpio);

								// Se audita la carga de la digitalización
								$this->auditarCargaDigitalizaciones($clave, "DIGITALIZACION CARGADA", "Se carga la digitalización ".$nombre_limpio.".pdf");
							} else {
								$mensaje = "La transferencia de la digitalizaci&oacute;n ha fallado, el directorio ".$nombre_limpio." puede que no exista.";
								$tipo_mensaje = 2;
							}
						} else {
							$mensaje = "El directorio ".$nombre_limpio." no existe.";
							$tipo_mensaje = 2;
						}
					}

					//SE CIERRA LA SECUENCIA FTP
					ftp_close($this->id_conexion);

					// SE CIERRA EL DIRECTORIO
					closedir($dir);
				}
			}
	    }

	    // SI HAY digitalizaciones EXISTENTES SE PREGUNTA AL USUARIO POR CADA UNO DE ELLAS SI DESEA REEMPLAZARLAS
	    if ( $digitalizaciones_existentes[0]['nombre_directorio'] != '' ) {
		 	$_SESSION['digitalizaciones_existentes'] = $digitalizaciones_existentes;
		 	$_SESSION['numero_cargados'] = 0;

		 	// SE CONSULTA AL USUARIO QUE HACER CON LAS digitalizaciones EXISTENTES
		 	$this->consultarUsuario($clave_expediente);
		} else {
			$_SESSION['digitalizaciones_existentes'] = null;
			$_SESSION['mensaje']                     = $mensaje;
			$_SESSION['tipo_mensaje']                = $tipo_mensaje;

			// Vuelve en el lugar de la grilla del expediente que se estaba visualizando (el del buscador)
			$this->volverAlInicio($clave_expediente);
		}
	}

	/**
	 * Se le pregunta al usuario si desea Reemplazar o Agregar las digitalizaciones
	 * en caso que una digitalización posea al final de su nombre la letra "a" o "A"
	 * se agrega directamente a la existente.
	 * @param  [array] $clave_expediente Clave del expediente para volver a la grilla, luego de la operación.
	 */
	public function consultarUsuario($clave_expediente) {

		$cantidad = count($_SESSION['digitalizaciones_existentes']);

		// Si queda un existente para cargar
		if ( $_SESSION['numero_cargados'] < $cantidad ) {
			$posicion = $_SESSION['numero_cargados'];

			// Si no está cargado
			if ( $_SESSION['digitalizaciones_existentes'][$posicion]['cargado'] == 'no' ) {

				// Se marca como cargado en la sesión
				$_SESSION['digitalizaciones_existentes'][$posicion]['cargado'] = 'si';
				$_SESSION['numero_cargados']++;

				$directorio_desde   = $_SESSION['digitalizaciones_existentes'][$posicion]['directorio_desde'];
				$directorio_destino = $_SESSION['digitalizaciones_existentes'][$posicion]['directorio_destino'];
				$documento          = $_SESSION['digitalizaciones_existentes'][$posicion]['documento'];
				$anio               = $_SESSION['digitalizaciones_existentes'][$posicion]['anio'];
				$nombre 			= $_SESSION['digitalizaciones_existentes'][$posicion]['nombre_directorio'];

				// Se separa la extensión del nombre del documento
				// $documento puede ser: AAENNNNNa.pdf, AAENNNNNA.pdf ó AAENNNNN.pdf
				$division = explode('.',$documento);
				// Se toma solamente el nombre del documento
				$solo_nombre_documento = $division[0];

				// Se verifica si el nombre posee la letra "a" o "A" al final
				if ( (substr($solo_nombre_documento, -1) === 'a') || (substr($solo_nombre_documento, -1) === 'A') ) {

					// Se arma la información para agregar directamente a la digitalización existente
					$info_para_agregar['clave_expediente']   = $clave_expediente;
					$info_para_agregar['directorio_desde']   = $directorio_desde;
					$info_para_agregar['directorio_destino'] = $directorio_destino;
					$info_para_agregar['documento']          = $documento;
					$info_para_agregar['anio']               = $anio;
					$info_para_agregar['tipo']               = substr($nombre, 2, 1);
					$info_para_agregar['numero']             = (int)substr($nombre, 3, 5);
					$info_para_agregar['nombre_codificado']  = $nombre;

					// Se agrega directamente sin preguntarle al usuario
					$this->agregar($info_para_agregar);

				} else {
					// Se le pregunta al usaurio si desea reemplazarlo o agregarlo a la digitalización existente
					$vista = new VistaCargarDigitalizacion();
					$vista->preguntar_por_digitalizacion(
						$clave_expediente,
						$directorio_desde,
						$directorio_destino,
						$documento,
						$anio,
						$nombre
					);
				}
			}
		} else
			// Vuelve en el lugar de la grilla del expediente que se estaba visualizando (el del buscador)
			$this->volverAlInicio($clave_expediente);
	}

	/**
	 * Se reemplaza la digitalización (se elimina la existente y se carga la nueva)
	 */
	public function reemplazar() {

		$clave_expediente = Array('anio' => Validador::validarParametro('anio'),
								  'tipo' => Validador::validarParametro('tipo'),
								  'numero' => Validador::validarParametro('numero'),
								  'cuerpo' => 0,
								  'alcance' => 0
								 );

		$directorio_desde   = Validador::validarParametro('directorio_desde');
		$directorio_destino = Validador::validarParametro('directorio_destino');
		$documento          = Validador::validarParametro('documento');
		$nombre_codificado  = Validador::validarParametro('nombre_codificado');

		// Se establece una conexión FTP
		$this->id_conexion = ftp_connect('localhost');

		// Se establece el inicio de sesión FTP con usuario y password
		$this->resultado_login = ftp_login($this->id_conexion, self::USUARIO, self::PASSWORD);

		// Se chequea la conexión
		if ( ( !$this->id_conexion ) || ( !$this->resultado_login ) ) {
			$mensaje = "Error al intentar conectarse o autentificarse en el Servidor FTP.";
			$tipo_mensaje = 2;
		} else {
			// Se elimina la digitalización existente para cargar la nueva
			if (file_exists($directorio_destino."/".$documento))
				ftp_delete($this->id_conexion, $directorio_destino."/".$documento);
				//unlink($directorio_destino."/".$documento);

			// Se carga la digitalización (con la extensión en minúscula)
			if ( ftp_put($this->id_conexion, $directorio_destino."/".$nombre_codificado.".pdf", $directorio_desde.$documento, FTP_BINARY) ) {
				$mensaje = "Se ha reemplazado la digitalizaci&oacute;n satisfactoriamente!";
				$tipo_mensaje = 1;

				// Se elimina la digitalización del directorio "proyectos/digital/" del expediente
				if (file_exists($directorio_desde."/".$documento))
					ftp_delete($this->id_conexion, $directorio_desde."/".$documento);
					//unlink($directorio_desde."/".$documento);

				// Se obtiene la clave del expediente/nota a partir del nombre codificado
				$clave = $this->obtenerClaveDeNombre($nombre_codificado);

				// Se audita la sobreescritura de la digitalización
				$this->auditarCargaDigitalizaciones($clave, "DIGITALIZACION REEMPLAZADA", "Se reemplaza la digitalización ".$documento);
			} else {
				$mensaje = "La transferencia de la digitalizaci&oacute;n ha fallado!";
				$tipo_mensaje = 2;
			}
		}

		// Se cierra la conexión FTP
		ftp_close($this->id_conexion);

		$_SESSION['mensaje']      = $mensaje;
		$_SESSION['tipo_mensaje'] = $tipo_mensaje;

		// SE VUELVE A CONSULTAR AL USUARIO
		$this->consultarUsuario($clave_expediente);
	}

	/**
	 * Se agrega la digitalización a la existente (se unen los documentos .pdf)
	 * 20/01/2020 XXXX
	 * Se utilizan directamente las rutas de las digitalizaciones,
	 * no hace falta descargar la existente.
	 */
	public function agregar($info_para_agregar = null) {

		// Si se le preguntó al usuario
		// se recibe la información desde la Vista
		if ( is_null($info_para_agregar) ) {
			$clave_expediente = Array('anio' => Validador::validarParametro('anio'),
									  'tipo' => Validador::validarParametro('tipo'),
									  'numero' => Validador::validarParametro('numero'),
									  'cuerpo' => 0,
									  'alcance' => 0
									 );

			$directorio_desde   = Validador::validarParametro('directorio_desde');
			$directorio_destino = Validador::validarParametro('directorio_destino');
			$documento          = Validador::validarParametro('documento');
			$nombre_codificado  = Validador::validarParametro('nombre_codificado');
		}
		else // Sino, se utiliza el parámetro recibido desde el mismo Controlador
		{
			$clave_expediente   = $info_para_agregar['clave_expediente'];
			$directorio_desde   = $info_para_agregar['directorio_desde'];
			$directorio_destino = $info_para_agregar['directorio_destino'];
			$documento          = $info_para_agregar['documento'];
			$nombre_codificado  = $info_para_agregar['nombre_codificado'];
		}

		// Ruta de la digitalización EXISTENTE (proyectos/AAAA/AAENNNNN/AAENNNNN.PDF)
		$ruta_digitalizacion_existente = $directorio_destino."/".$nombre_codificado.'.pdf';

		// Ruta de la digitalización A AGREGAR (proyectos/digital/AAENNNNN.PDF)
		$ruta_digitalizacion_a_agregar = $directorio_desde.$documento;

		// Se unen las digitalizaciones
		$union_pdfs = $this->unirDigitalizaciones($ruta_digitalizacion_existente, $ruta_digitalizacion_a_agregar, '/tmp/'.$nombre_codificado);

		// Si se ha generado la unión de las digitalizaciones
		if (! is_null($union_pdfs)) {

			// Se establece una conexión FTP
			$this->id_conexion = ftp_connect('localhost');

			// Se establece el inicio de sesión FTP con Usuario y Password
			$this->resultado_login = ftp_login($this->id_conexion, self::USUARIO, self::PASSWORD);

			// Se chequea la conexión
			if ( ( !$this->id_conexion ) || ( !$this->resultado_login ) ) {
				$mensaje = "Error al intentar conectarse o autentificarse en el Servidor FTP.";
				$tipo_mensaje = 2;
			} else {
				// Se elimina la digitalización existente para cargar la unión de ambas digitalizaciones
				if (file_exists($ruta_digitalizacion_existente))
					ftp_delete($this->id_conexion, $ruta_digitalizacion_existente);
					//unlink($ruta_digitalizacion_existente);

				// Se carga la digitalización (con la extensión en minúscula)
				if ( ftp_put($this->id_conexion, $ruta_digitalizacion_existente, $union_pdfs, FTP_BINARY) ) {

					// Se elimina la digitalización que fue agregada
					// del directorio "proyectos/digital/" del expediente respectivo
					if (file_exists($ruta_digitalizacion_a_agregar))
						unlink($ruta_digitalizacion_a_agregar);

					// Se elimina la unión en "proyectos/digital/"
					// una vez cargada en el directorio final del expediente
					if (file_exists($union_pdfs))
						unlink($union_pdfs);

					$mensaje = "Se ha agregado la digitalizaci&oacute;n satisfactoriamente a la existente!";
					$tipo_mensaje = 1;

					// Se obtiene la clave del expediente/nota a partir del nombre codificado
					$clave = $this->obtenerClaveDeNombre($nombre_codificado);

					// Se audita la unión de las digitalizaciones
					$this->auditarCargaDigitalizaciones($clave, "DIGITALIZACION AGREGADA", "Se agrega una digitalización a la ".$documento);
				} else {
					$mensaje = "La uni&oacute;n de las digitalizaciones ha fallado!";
					$tipo_mensaje = 2;
				}
			}
		} else {
			$mensaje = "La uni&oacute;n de las digitalizaciones ha fallado!";
			$tipo_mensaje = 2;
		}

		// Se cierra la conexión FTP
		ftp_close($this->id_conexion);

		$_SESSION['mensaje']      = $mensaje;
		$_SESSION['tipo_mensaje'] = $tipo_mensaje;

		// SE VUELVE A CONSULTAR AL USUARIO
		$this->consultarUsuario($clave_expediente);
	}

	/**
	 * 20/05/2019 XXXX
	 * Se unen las digitalizaciones
	 * @param  [string] $digi_existente        Digitalización existente
	 * @param  [string] $digi_a_agregar        Digitalización a agregar a la existente
	 * @param  [string] $nombre_digitalizacion Nombre codificado, en formato AAENNNNN
	 * @return [string]                        Nombre del archivo de la unificación, o null
	 */
	public function unirDigitalizaciones($digi_existente, $digi_a_agregar, $nombre_digitalizacion) {
		// La digitalizacion resultante se debe llamar igual
		$nombre_salida = $nombre_digitalizacion.'_final.pdf';

		// Se define el comando para unir las digitalizaciones, utilizando el comando gs (ghostscript)
		$comando = "gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=".$nombre_salida." ".$digi_existente." ".$digi_a_agregar;

		// Se ejecuta el comando
		$resultado = shell_exec($comando);

		// Devuelve el nombre del archivo con la unión de las digitalizaciones, o null
		return (is_file($nombre_salida)) ? $nombre_salida : null;
	}

	/**
	 * Para volver al expediente que se estaba visualizando (el del buscador)
	 */
	public function volverAlInicio($clave_expediente) {
		// URL para volver al expediente que se estaba visualizando (el del buscador)
		$url = "index.php?anio=".$clave_expediente['anio']."&tipo=".$clave_expediente['tipo']."&numero=".$clave_expediente['numero']."&cuerpo=".$clave_expediente['cuerpo']."&alcance=".$clave_expediente['alcance']."&sentido=anterior";
		?>
		<script type="text/javascript">location.href = '<?php echo $url; ?>';</script>
		<?php
	}

	/**
	 * Se audita la carga de las digitalizaciones
	 * @param  array 	$clave_expediente
	 * @param  string 	$observaciones
	 */
	public function auditarCargaDigitalizaciones($clave_expediente, $operacion, $observaciones) {

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

		// Se audita la carga de las digitalizaciones
		$modelo->registrarMovimiento($datos_log);
	}

	/**
	 * NO UTILIZADO, POR EL MOMENTO SE UBICAN EN "proyectos/digital/" POR FTP
	 *
	 * Se verifica si ya se encuentra la digitalización
	 * De ser así se le pregunta al usuario si desea reemplazarlo
	 * Sino se le muestra el formulario para su carga
	 * @return [type] [description]
	 *
	public function upload_bloque() {

	    $vista = new VistaCargarDigitalizacion();
	    $nombre_documento = $this->armar_nombre_documento();

	    // SI SE VUELVE DE LA PREGUNTA AL USUARIO POR LA SOBREESCRITURA DEL DOCUMENTO
	    if ( Validador::validarParametro('se_vuelve') == 'si' )
		    $se_vuelve = Validador::validarParametro('pftp_se_vuelve');
	    else
		    $se_vuelve = 'no';

	    // SE VERIFICA SI EXISTE EL DOCUMENTO AATNNNN.pdf
	    if ( ($se_vuelve == 'no') && ( is_file(self::RUTA_DIRECTORIO."digital/".$nombre_documento.".pdf")) )
	    	// SE PREGUNTA SI SE DESEA SOBREESCRIBIR
		    $vista->preguntar_usuario_bloque($_REQUEST);
	    else
		    // SE MUESTRA EL FORMULARIO PARA CARGAR EL PROYECTO .pdf
		    $vista->upload_bloque($_REQUEST);
	}
	/**/

	/**
	 * NO UTILIZADO, POR EL MOMENTO SE UBICAN EN "proyectos/digital/" POR FTP
	 *
	 * Se inicia la carga de la digitalización, guardando el PDF en el directorio "/digital"
	 * @return [type] [description]
	 *
	public function procesar_upload_bloque() {

	    $vista = new VistaCargarDigitalizacion();

	    // SE ARMA EL NOMBRE DEL DIRECTORIO PARA EL EXPEDIENTE RESPECTIVO
	    $nombre_documento = $this->armar_nombre_documento($_POST);

		// Si NO se ha recibido el documento
	    if ( $_FILES['proyecto_subido']['error'] == 4 ) {
			$mensaje = "No se ha subido el documento.";
			$tipo_mensaje = 2;
	    }

	    // Si el documento fue recibido sin errores
	    if ( $_FILES['proyecto_subido']['error'] == 0 ) {
			if ( is_uploaded_file($_FILES['proyecto_subido']['tmp_name']) ) {
				// SE EXTRAE SU EXTENSION
				$extension = strtolower(end(explode('.', $_FILES['proyecto_subido']['name'])));

				// SE VERIFICA SI SE HA SUBIDO UN ARCHIVO CON EXTENSION .pdf
				if ( $extension == 'pdf' ) {
					$directorio_destino_por_bloque = self::RUTA_DIRECTORIO."digital/";

					// SE ESTABLECE UNA CONEXION FTP
					$this->id_conexion = ftp_connect('localhost');

					// SE ESTABLECE EL INICIO DE SESION FTP CON USUARIO Y PASSWORD
					$this->resultado_login = ftp_login($this->id_conexion, self::USUARIO, self::PASSWORD);

					// SE CHEQUEA LA CONEXION FTP
					if ( ( !$this->id_conexion ) || ( !$this->resultado_login ) ) {
						$mensaje = "Error al intentar conectarse o autentificarse en el Servidor FTP.";
						$tipo_mensaje = 2;
					} else {
						// SE CAMBIA AL DIRECTORIO DONDE SE QUIERE SUBIR EL ARCHIVO, ( /var/www/sgl/expedientes/proyectos/digital/ )
						ftp_chdir($this->id_conexion, $directorio_destino_por_bloque);

						$dir_actual = ftp_pwd($this->id_conexion);

						//$ruta_archivo_local = $directorio_carga_por_bloque.$nombre_documento.".pdf";
						$archivo_local = $_FILES['proyecto_subido']['tmp_name'];
						$ruta_archivo_remoto = $dir_actual."/".$nombre_documento.".pdf";

						// SE CARGA EL ARCHIVO
						if ( ftp_put($this->id_conexion, $ruta_archivo_remoto, $archivo_local, FTP_BINARY) ) {
							$mensaje = "Se ha cargado la digitalizaci&oacute;n satisfactoriamente!";
							$tipo_mensaje = 1;
						} else {
							$mensaje = "La transferencia de la digitalizaci&oacute;n ha fallado!";
							$tipo_mensaje = 2;
						}

						//SE CIERRA LA SECUENCIA FTP
						ftp_close($this->id_conexion);
					}

					$texto_segun_tipo = ($_POST['pftp_tipo'] == 'E') ? "el Expediente " : "la Nota ";

					$mensaje = "Se ha cargado la digitalizaci&oacute;n satisfactoriamente.";
					$tipo_mensaje = 1;
				} else {
					$mensaje = "La extensi&oacute;n de la digitalizaci&oacute;n no es correcta. Solamente se permite .pdf";
					$tipo_mensaje = 2;
				}
			} else {
				$mensaje = "No se ha cargado la digitalizaci&oacute;n";
				$tipo_mensaje = 2;
			}
	    }

	    // Retorna al listado principal de expedientes con un mensaje determinado al usuario
	    $vista->retornar($mensaje, $tipo_mensaje, $_POST);
	}
	/**/

	/**
	 * NO UTILIZADO, POR EL MOMENTO SE UBICAN EN "proyectos/digital/" POR FTP
	 *
	 * En caso de existir retorna el nombre (puede estar en minúscula o en mayúscula)
	 * @param  [type] $directorio_destino [description]
	 * @return [type]                     [description]
	 *
	public function existeOriginal($directorio_destino) {

		$existe = false;
		$nombre_del_original = '';

		// SE ABRE EL DIRECTORIO DONDE SE CARGARA la Digitalización (original.pdf)
		if ( $dir_abierto = @opendir($directorio_destino) ) {
			// MIENTRAS NO SE HAYA TERMINADO DE RECORRER EL DIRECTORIO Y NO SE HAYA ENCONTRADO original.pdf
			while ( ( false !== ( $file = readdir($dir_abierto) ) && !$existe ) ) {
				if ( $file != '..' && $file != '.' ) {
					// SI SE ENCUENTRA original.pdf (SE LO BUSCA EN MINUSCULA)
					if ( "original.pdf" == strtolower($file) ) {
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
	/**/

	/**
	 * NO UTILIZADO, POR EL MOMENTO SE UBICAN EN "proyectos/digital/" POR FTP
	 *
	 * Se renombra el archivo como original_anterior_[fecha en formato d_m_Y_H_i_s]
	 * @return [type] [description]
	 *
	public function renombrarOriginalAnterior() {

		$modelo = new cargarDigitalizacionModel();

		$directorio_desde          = Validador::validarParametro('directorio_desde');
		$directorio_destino        = Validador::validarParametro('directorio_destino');
		$documento                 = Validador::validarParametro('documento');
		$nombre_original_existente = Validador::validarParametro('nombre_original_existente');
		// Para la auditoría de la sobreescritura del documento
		$clave_a_separar           = Validador::validarParametro('clave_a_separar');

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
			// SE CAMBIA AL DIRECTORIO DONDE SE QUIERE SUBIR EL ARCHIVO, ( /var/www/sgl/expedientes/proyectos/AAAA/AATNNNNN/ )
			ftp_chdir($this->id_conexion, $directorio_destino);

			$dir_actual = ftp_pwd($this->id_conexion);

			// RUTA CON EL NOMBRE ANTERIOR
			$ruta_nombre_anterior = $dir_actual."/".$nombre_original_existente;

			// RUTA CON EL NOMBRE NUEVO PARA ASIGNARSELO AL ORIGINAL ANTERIOR
			$ruta_nombre_nuevo = $dir_actual."/original_anterior_".date("d_m_Y_H_i_s").".pdf";

			// SE RENOMBRA original.pdf COMO 'original_anterior_anio_mes_dia_hora_min_seg.pdf' PARA NO PERDERLO
			if ( ftp_rename($this->id_conexion, $ruta_nombre_anterior, $ruta_nombre_nuevo) ) {
				// SE CARGA EL ARCHIVO COMO original.pdf (EN MINUSCULA)
				if ( ftp_put($this->id_conexion, $dir_actual."/original.pdf", $directorio_desde.$documento, FTP_BINARY) ) {
					$mensaje = "Se ha cargado la digitalizaci&oacute;n satisfactoriamente!";
					$tipo_mensaje = 1;

					// SE ELIMINA EL DOCUMENTO DEL DIRECTORIO /temporal
					ftp_delete($this->id_conexion, $directorio_desde.$documento);

					// ****** Se registra el renombrado del documento en Auditoría
					//$modelo->auditarCargaDocumento($clave_a_separar, "Se renombra el documento que ya existe para mantenerlo.");
				} else {
					$mensaje = "La transferencia de la digitalizaci&oacute;n ha fallado!";
					$tipo_mensaje = 2;
				}
			} else {
				$mensaje = "Hubo un problema al renombrar el original.pdf existente.";
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
		$this->consultarUsuario();
	}
	/**/
}
?>

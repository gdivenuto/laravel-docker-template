<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//Incluye la vista que corresponde
require 'vistas/cargar_documentos_depto_ejecutivo.php';

class cargar_documentos_depto_ejecutivo_controller extends ControllerBase
{
	// Ruta del directorio de Proyectos donde se cargarán los documentos del D.E.
	const RUTA_DIRECTORIO_PROYECTOS = "/var/www/sgl/expedientes/proyectos/";

	// Para mostrar el contenido del directorio "expe-de"
	const RUTA_DIRECTORIO_EXPE_DE   = "/var/www/sgl/expedientes/expe-de/";

	// Para la transferencia por FTP
	const USUARIO  = 'expe';
	const PASSWORD = '123456';

	private $extensiones_no_permitidas = array(".exe", ".pif", ".inf");
	private $id_conexion;
	private $resultado_login;

	public function listar() {

	    // CLAVE DEL FILTRO DE BUSQUEDA
	    $f_criterio_busqueda = Array();
		$f_criterio_busqueda['criterio_anio']    = Validador::validarParametro('anio');
		$f_criterio_busqueda['criterio_tipo']    = Validador::validarParametro('tipo');
		$f_criterio_busqueda['criterio_numero']  = Validador::validarParametro('numero');
		$f_criterio_busqueda['criterio_cuerpo']  = Validador::validarParametro('cuerpo');
		$f_criterio_busqueda['criterio_alcance'] = Validador::validarParametro('alcance');

	    // DIRECTORIO DONDE SE COPIARÁN LOS DOCUMENTOS ( FORMATO AATNNNNN, DENTRO DE proyectos/ )
	    $directorio_destino = $this->armar_nombre_directorio_destino($f_criterio_busqueda['criterio_anio'], $f_criterio_busqueda['criterio_tipo'], $f_criterio_busqueda['criterio_numero']);

	    // DATOS DEL ANTECEDENTE
		$anio_a       = Validador::validarParametro('anio_a');
		$numero_a     = Validador::validarParametro('numero_a');
		$aux_numero_a = 1000000+$numero_a;
		$numero_a     = substr($aux_numero_a, -6);
		$digito_a     = Validador::validarParametro('digito_a');

	    // DIRECTORIO DONDE SE TOMAN LOS DOCUMENTOS DEL EJECUTIVO
	    $ruta_documentos_ejecutivo_para_cargar = self::RUTA_DIRECTORIO_EXPE_DE.$anio_a."/".$anio_a."-".$numero_a."-".$digito_a;

	    $vista = new VistaCargarDocumentosDeptoEjecutivo();
	    $vista->mostrar_contenido($ruta_documentos_ejecutivo_para_cargar, $directorio_destino, $f_criterio_busqueda);
	}

	public function armar_nombre_directorio_destino($anio, $tipo, $numero) {

	    // SE ARMA EL NOMBRE DEL DIRECTORIO ( FORMATO AATNNNNN, DENTRO DE sgl/expedientes/proyectos/ )
		$anio_corto = substr($anio, -2);
		$tipo       = $tipo;
		$aux_numero = 100000+$numero;
		$numero     = substr($aux_numero, -5);

	    return $anio_corto.$tipo.$numero;
	}

	/**
	 * Modificado el 19/02/2019 por XXXX
	 */
	public function pasar_documentos_ejecutivo() {

		// RUTA DONDE ESTÁN LOS DOCUMENTOS PARA CARGAR
	    $ruta_documentos_ejecutivo_para_cargar = Validador::validarParametro('ruta_documentos_ejecutivo_para_cargar');

	    // Clave del Expediente
		$criterio_anio    = Validador::validarParametro('criterio_anio');
		$criterio_tipo    = Validador::validarParametro('criterio_tipo');
		$criterio_numero  = Validador::validarParametro('criterio_numero');
		$criterio_cuerpo  = Validador::validarParametro('criterio_cuerpo');
		$criterio_alcance = Validador::validarParametro('criterio_alcance');

	    // Directorio donde se copiarán los documentos (formato AATNNNNN, en ../proyectos/AAAA/)
	    $directorio_destino = Validador::validarParametro('directorio_destino');

	    $documentos_existentes = array();
	    $posicion_existentes = 0;

	    // Por cada documento elegido para pasar
	    foreach ($_POST['documento'] as $valor) {
			// Si es un documento y su extensión está permitida
			if ( is_file($ruta_documentos_ejecutivo_para_cargar.'/'.$valor)
				&&
				( !in_array(strtolower(substr($valor, -4)), $this->extensiones_no_permitidas) )
			   ) {
				// Documento a cargar en /proyectos/AAAA/AATNNNNN
				$documento = $valor;

				// Ruta del directorio de PROYECTOS donde se cargarán los documentos del D.E.
				//	/var/www/sgl/expedientes/proyectos/AAAA/AAENNNNN/
				$ruta_destino = self::RUTA_DIRECTORIO_PROYECTOS.$criterio_anio."/".$directorio_destino."/";

				// Se verifica si ya existe el documento
				if ( file_exists($ruta_destino.$documento) ) {

					// Se toma la información de los documentos existentes, para preguntarle al usuario si desea sobreescribirlos
					$documentos_existentes[$posicion_existentes]['directorio_desde']   = $ruta_documentos_ejecutivo_para_cargar;
					$documentos_existentes[$posicion_existentes]['directorio_destino'] = $directorio_destino;
					$documentos_existentes[$posicion_existentes]['documento']          = $documento;
					$documentos_existentes[$posicion_existentes]['cargado']            = 'no';

					$posicion_existentes++;
				} else {
					// SE ABRE EL DIRECTORIO DEL EXPEDIENTE DEL EJECUTIVO PARA TOMAR LOS DOCUMENTOS
					$dir = opendir($ruta_documentos_ejecutivo_para_cargar);

					// SE ESTABLECE UNA CONEXION FTP
					$this->id_conexion = ftp_connect('localhost');

					// SE ESTABLECE EL INICIO DE SESION FTP CON USUARIO Y PASSWORD
					$this->resultado_login = ftp_login($this->id_conexion, self::USUARIO, self::PASSWORD);

					// SE CHEQUEA LA CONEXION
					if ( ( !$this->id_conexion ) || ( !$this->resultado_login ) ) {
						$mensaje = "Error al intentar conectarse o autentificarse en el Servidor FTP.";
						$tipo_mensaje = 2;
					} else {
						// Se cambia al directorio de /proyectos, donde se desean cargar los documentos
						// "/sgl/expedientes/proyectos/AAAA/AATNNNNN/"
						ftp_chdir($this->id_conexion, $ruta_destino);

						// Se obtiene el nombre del directorio actual
						// "/home/expe"
						$dir_actual = ftp_pwd($this->id_conexion);

						// 	"/proyectos/AAAA/AAENNNNN"
						$archivo_remoto = $dir_actual."/".$documento;

						// 	"/expe-de/AAAA/AAAA-NNNNN-D/"
						$archivo_local = $ruta_documentos_ejecutivo_para_cargar."/".$documento;

						// Se carga el archivo
						if ( ftp_put($this->id_conexion, $archivo_remoto, $archivo_local, FTP_BINARY) ) {
							$mensaje = "Se han cargado satisfactoriamente los documentos!";
							$tipo_mensaje = 1;
						} else {
							$mensaje = "La transferencia de los documentos ha fallado!";
							$tipo_mensaje = 2;
						}

						// SE CIERRA LA SECUENCIA FTP
						ftp_close($this->id_conexion);
					}
					// SE CIERRA EL DIRECTORIO
					closedir($dir);
				}
			}
	    }

	    // Si hay documentos existentes
	    if ( $documentos_existentes[0]['documento'] != '' ) {

	    	// Clave del expediente
			$_SESSION['criterio_anio']         = $criterio_anio;
			$_SESSION['criterio_tipo']         = $criterio_tipo;
			$_SESSION['criterio_numero']       = $criterio_numero;
			$_SESSION['criterio_cuerpo']       = $criterio_cuerpo;
			$_SESSION['criterio_alcance']      = $criterio_alcance;
			// Conjunto de documentos existentes
			$_SESSION['documentos_existentes'] = $documentos_existentes;
			// Se inicializa para determinar luego los que se van sobreescribiendo
			$_SESSION['numero_cargados']       = 0;

			// Se le pregunta al usuario si desea sobreescribirlos
			$this->consultarUsuario();

		} else {
			$_SESSION['documentos_existentes'] = null;
			$_SESSION['mensaje']               = $mensaje;
			$_SESSION['tipo_mensaje']          = $tipo_mensaje;
		?>
			<script type="text/javascript">
				// Se vuelve a la solapa de Antecedentes
				refrescar('abms/index.php?controlador=antecedentes&accion=listar&anio=<?php echo $criterio_anio; ?>&tipo=<?php echo $criterio_tipo; ?>&numero=<?php echo $criterio_numero; ?>&cuerpo=<?php echo $criterio_cuerpo; ?>&alcance=<?php echo $criterio_alcance; ?>&mensaje=<?php echo $mensaje; ?>&tipo_mensaje=<?php echo $tipo_mensaje; ?>', 'contenidoAjaxPrincipal');
			</script>
		<?php
		}
	}

	/**
	 * Agregado el 19/02/2019 por XXXX
	 * Se le pregunta al usuario si desea sobreescribir el documento existente
	 */
	public function consultarUsuario() {

		$cantidad = count($_SESSION['documentos_existentes']);

		// SI QUEDA UN EXISTENTE PARA CARGAR
		if ( $_SESSION['numero_cargados'] < $cantidad ) {

			$posicion = $_SESSION['numero_cargados'];

			// SI NO ESTA CARGADO
			if ( $_SESSION['documentos_existentes'][$posicion]['cargado'] == 'no' ) {

				// SE MARCA COMO CARGADO EN LA SESION
				$_SESSION['documentos_existentes'][$posicion]['cargado'] = 'si';
				$_SESSION['numero_cargados']++;

				$vista = new VistaCargarDocumentosDeptoEjecutivo();

				// Se le pregunta al usuario si desea sobreescribir el documento existente
				$vista->preguntarPorSobreescritura(
					$_SESSION['documentos_existentes'][$posicion]['directorio_desde'],
					$_SESSION['documentos_existentes'][$posicion]['directorio_destino'],
					$_SESSION['documentos_existentes'][$posicion]['documento'],
					$_SESSION['criterio_anio']
				);
			}
		} else {
		?>
			<script type="text/javascript">
				// Se vuelve a la solapa de Antecedentes
				refrescar('abms/index.php?controlador=antecedentes&accion=listar&anio=<?php echo $_SESSION['criterio_anio']; ?>&tipo=<?php echo $_SESSION['criterio_tipo']; ?>&numero=<?php echo $_SESSION['criterio_numero']; ?>&cuerpo=<?php echo $_SESSION['criterio_cuerpo']; ?>&alcance=<?php echo $_SESSION['criterio_alcance']; ?>&mensaje=<?php echo $_SESSION['mensaje']; ?>&tipo_mensaje=<?php echo $_SESSION['tipo_mensaje']; ?>', 'contenidoAjaxPrincipal');
			</script>
		<?php
		}
	}

	/**
	 * Agregado el 19/02/2019 por XXXX
	 * Se sobreescribe un documento
	 */
	public function sobreescribirDocumento()
	{
		$directorio_desde   = Validador::validarParametro('directorio_desde');   // /var/www/sgl/expedientes/expe-de/AAAA/AAAA-NNNNNN-D
		$directorio_destino = Validador::validarParametro('directorio_destino'); // AAENNNNN
		$documento          = Validador::validarParametro('documento');  		 // Nombre del documento
		$anio               = Validador::validarParametro('anio'); 			     // AAAA

		// directorio_desde   EJEMPLO = /var/www/sgl/expedientes/expe-de/2018/2018-001122-1
		// directorio_destino EJEMPLO = /sgl/expedientes/proyectos//18E02180/

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
			// "/sgl/expedientes/proyectos/AAAA/AATNNNNN/"
			$directorio_destino_proyectos = "/var/www/sgl/expedientes/proyectos/".$anio."/".$directorio_destino."/";

			ftp_chdir($this->id_conexion, $directorio_destino_proyectos);

			$dir_actual = ftp_pwd($this->id_conexion);

			// SE ELIMINA EL documento EXISTENTE PARA CARGAR EL NUEVO
			ftp_delete($this->id_conexion, $dir_actual."/".$documento);

			// 	"/proyectos/AAAA/AAENNNNN"
			$archivo_remoto = $dir_actual."/".$documento;

			// 	"/expe-de/AAAA/AAAA-NNNNN-D/"
			$archivo_local = $directorio_desde."/".$documento;

			// Se carga el documento nuevamente
			if ( ftp_put($this->id_conexion, $archivo_remoto, $archivo_local, FTP_BINARY) ) {

				$mensaje = "Se ha cargado el documento satisfactoriamente!";
				$tipo_mensaje = 1;
			} else {
				$mensaje = "La transferencia del documento ha fallado!";
				$tipo_mensaje = 2;
			}
		}

		//SE CIERRA LA SECUENCIA FTP
		ftp_close($this->id_conexion);

		// SE CIERRA EL DIRECTORIO
		closedir($dir);

		$_SESSION['mensaje']      = $mensaje;
		$_SESSION['tipo_mensaje'] = $tipo_mensaje;

		// SE VUELVE A CONSULTAR AL USUARIO
		$this->consultarUsuario();
	}
}
?>

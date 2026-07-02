<?php
if (!isset($_SESSION))
	session_start();

require_once RUTA_MODELOS . "Movimiento.php";

require_once RUTA_VISTAS . "movimiento/grilla.php";
require_once RUTA_VISTAS . "movimiento/edicion.php";

class MovimientoController extends ControladorBase
{
    public function __construct()
    {
    	parent::__construct();

    	$this->campo_orden_por_defecto = 'numero';

		$this->modelo = new MovimientoModel();

		$this->vista_grilla = new VistaMovimientoGrilla();
		$this->vista_edicion = new VistaMovimientoEdicion();
	}
	
	/**
	 * Se listan los movimientos de un expediente determinado por su número
	 * 
	 * @param  string $numero
	 * @param  string $mensaje
	 * @param  string $tipo_mensaje
	 * @return html   
	 */
	public function listar($numero = '', $mensaje = '', $tipo_mensaje = ''): void {

		$numero = ($numero == '' 
			? LibreriaGeneral::recoge('numero', 0) 
			: $numero);

		$listado = $this->modelo->listar($numero);
		
		$this->vista_grilla->mostrar($numero, $listado, $mensaje, $tipo_mensaje);
	}

	public function editar($mensaje = '', $tipo_mensaje = ''): void {
		
		$numero = LibreriaGeneral::recoge('numero', 0);
				
		$this->vista_edicion->mostrar($numero, $mensaje, $tipo_mensaje);
	}

	public function guardar() {

		$datos = $_REQUEST;
		$info_documento = $_FILES['documento'];
		
		if ( isset($info_documento['name'][0]) && $info_documento['name'][0] != '' )
			$this->cargarDocumento($datos['numero'], $info_documento);

		header('Location: '.URL_ABMS.'?controlador=movimiento&accion=listar&numero='.$datos['numero']);
		exit;
	}

	private function cargarDocumento($numero, $info_documento)
	{
		$archivo_a_guardar = $info_documento['tmp_name'];
		
		$nombre_archivo = LibreriaGeneral::eliminarEspacios($info_documento['name']);
		
		if ($info_documento['error'] == 4) {
			$_SESSION['mensaje'] = "No se ha subido el archivo ".$nombre_archivo;
			$_SESSION['tipo_mensaje'] = 2;
		}

		if ($info_documento['error'] == 0) {
			$extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
		
			if ($info_documento['size'] > TAMANIO_MAXIMO_ARCHIVO) {
				$_SESSION['mensaje'] = $nombre_archivo." supera el tama&ntilde;o m&aacute;ximo permitido!";
				$_SESSION['tipo_mensaje'] = 2;
			}
			elseif( ! in_array($extension, ['pdf', 'docx','doc']) ) {
				$_SESSION['mensaje'] = "La extensi&oacute;n de ".$nombre_archivo." no es v&aacute;lida";
				$_SESSION['tipo_mensaje'] = 2;
			}
			else {
				$nombre_archivo_final = $numero.'_'.$nombre_archivo;
				$ruta_documento = RUTA_DOCUMENTOS_MOVIMIENTOS.$nombre_archivo_final;

				if (move_uploaded_file($archivo_a_guardar, $ruta_documento)) {

					if ($this->modelo->insertar($numero, $nombre_archivo_final)) {
						$_SESSION['mensaje'] = "Se ha realizado la carga satisfactoriamente!";
						$_SESSION['tipo_mensaje'] = 1;
					} else {
						if (is_file($ruta_documento))
							unlink($ruta_documento);

						$_SESSION['mensaje'] = "No se ha registrado el documento correctamente!";
						$_SESSION['tipo_mensaje'] = 2;
					}
				}
			}
		} else {
			$_SESSION['mensaje'] = "No se ha recibido el documento correctamente!";
			$_SESSION['tipo_mensaje'] = 2;
		}
	}

	/**
	 * Se elimina un registro determinado
	 */
	public function eliminar() {

		$id = LibreriaGeneral::recoge('id', 0);
		
		$registro = $this->modelo->obtenerRegistro($id);

		if ( isset($registro['documento']) ) {
			
			if (is_file(RUTA_DOCUMENTOS_MOVIMIENTOS . $registro['documento']))
				unlink(RUTA_DOCUMENTOS_MOVIMIENTOS . $registro['documento']);

			if ($this->modelo->eliminar($id))
				$this->listar($registro['numero'], $this->mensaje_eliminacion_ok, 1);
			else
				$this->listar($registro['numero'], $this->mensaje_eliminacion_error, 2);
		}
	}
}
?>
<?php
if (!isset($_SESSION))
	session_start();

require_once RUTA_MODELOS . "Nota.php";

require_once RUTA_VISTAS . "nota/grilla.php";
require_once RUTA_VISTAS . "nota/edicion.php";

class NotaController extends ControladorBase
{
    public function __construct()
    {
    	parent::__construct();

    	$this->campo_orden_por_defecto = 'numero';

		$this->modelo = new NotaModel();

		$this->vista_grilla = new VistaNotaGrilla();
		$this->vista_edicion = new VistaNotaEdicion();
	}
	
	/**
	 * Se listan las notas
	 * 
	 * @param  string $mensaje
	 * @param  string $tipo_mensaje
	 * @param  string $p_pagina 
	 * @return html   
	 */
	public function listar($mensaje = '', $tipo_mensaje = '', $p_pagina = ''): void {

		$filtro = Array();

		$filtro['f_numero'] = LibreriaGeneral::recoge('f_numero');

		$f_fecha_desde = LibreriaGeneral::recoge('f_fecha_desde');
		$filtro['f_fecha_desde'] = ( isset($f_fecha_desde) && $this->esFechaValida($f_fecha_desde) ) 
			? $this->modelo->formatearFechaMySQL($f_fecha_desde) : '';
		
		$f_fecha_hasta = LibreriaGeneral::recoge('f_fecha_hasta');
		$filtro['f_fecha_hasta'] = ( isset($f_fecha_hasta) && $this->esFechaValida($f_fecha_hasta) ) 
			? $this->modelo->formatearFechaMySQL($f_fecha_hasta) : '';
		
		$filtro['pagina'] = ($p_pagina == '') ? LibreriaGeneral::recoge('pagina', 1) : $p_pagina;

		$campo_orden = LibreriaGeneral::recoge('campo_orden');
		if ($campo_orden != '') {
			$filtro['campo_orden'] = $campo_orden;
		} else {
			$filtro['campo_orden'] = $this->campo_orden_por_defecto;
			$_SESSION['ultimo_campo'] = '';
		}

		$filtro['sentido'] = LibreriaGeneral::recoge('sentido');

		if (!isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $filtro['campo_orden']) {
			$_SESSION['ultimo_campo'] = $filtro['campo_orden'];
			$_SESSION['ultimo_sentido'] = 'asc';
		} else {
			$_SESSION['ultimo_sentido'] = ($_SESSION['ultimo_sentido'] == 'asc' && $filtro['sentido'] == '') ? 'desc' : 'asc';
		}

		$filtro['rango'] = $this->rango_paginacion;

		if ($filtro['pagina'] == '') {
			$filtro['inicio'] = 0;
			$filtro['pagina'] = 1;
		} else {
			$filtro['inicio'] = ($filtro['pagina'] - 1) * $filtro['rango'];
		}

		$filtro['pagina_ant'] = $filtro['pagina'] - 1;
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;

		$this->modelo->setFiltro($filtro);

		$filtro['cantidad'] = $this->modelo->obtenerCantidad();

		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		$this->modelo->setFiltro($filtro);

		$listado = $this->modelo->listar();
		
		$this->vista_grilla->mostrar($listado, $mensaje, $tipo_mensaje, $filtro);
	}

	public function editar($mensaje = '', $tipo_mensaje = ''): void {
		$pagina = LibreriaGeneral::recoge('pagina', 1);
		$this->vista_edicion->mostrar($mensaje, $tipo_mensaje, $pagina);
	}

	public function guardar() {

		$datos = $_REQUEST;
		$info_documento = $_FILES['documento'];
		
		if ( isset($info_documento['name'][0]) && $info_documento['name'][0] != '' ) {
			$numero = $this->modelo->obtenerUltimoId() + 1;
			$this->cargarDocumento($numero, $info_documento);
		}

		$this->listar($_SESSION['mensaje'], $_SESSION['tipo_mensaje'], $datos['pagina']);
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
				$ruta_documento = RUTA_DOCUMENTOS_NOTAS.$nombre_archivo_final;

				if (move_uploaded_file($archivo_a_guardar, $ruta_documento)) {

					if ($this->modelo->insertar($nombre_archivo_final)) {
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

		$numero = LibreriaGeneral::recoge('numero', 0);
		
		$registro = $this->modelo->obtenerRegistro($numero);

		if ( isset($registro['documento']) ) {

			if (is_file(RUTA_DOCUMENTOS_NOTAS . $registro['documento']))
				unlink(RUTA_DOCUMENTOS_NOTAS . $registro['documento']);

			if ($this->modelo->eliminar($numero))
				$this->listar($this->mensaje_eliminacion_ok, 1);
			else
				$this->listar($this->mensaje_eliminacion_error, 2);
		}
	}
}
?>
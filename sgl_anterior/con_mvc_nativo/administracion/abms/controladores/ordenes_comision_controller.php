<?php
if (!isset($_SESSION))
	session_start();

require_once RUTA_MODELOS . "ordenes_comision.php";

require_once RUTA_VISTAS . "ordenes_comision/grilla.php";
require_once RUTA_VISTAS . "ordenes_comision/edicion.php";
require_once RUTA_VISTAS . "ordenes_comision/edicion_item.php";
require_once RUTA_VISTAS . "ordenes_comision/agregar_item.php";
require_once RUTA_VISTAS . "ordenes_comision/formato_impresion.php";
require_once RUTA_VISTAS . "ordenes_comision/edicion_encabezado.php";
require_once RUTA_VISTAS . "ordenes_comision/edicion_pie.php";
require_once RUTA_VISTAS . "ordenes_comision/edicion_cabecera.php";

class ordenes_comision_controller extends ControllerBase
{
	protected $vista_agregar_item;
	protected $vista_edicion_item;
	protected $vista_formato_impresion;
	protected $vista_edicion_encabezado;
	protected $vista_edicion_pie;
	protected $vista_edicion_cabecera;

	public function __construct()
	{
		parent::__construct();
	
		$this->campo_orden_por_defecto = 'fecha';

		$this->modelo = new ordenes_comisionModel();
	
		$this->vista_grilla = new VistaOrdenComisionGrilla();
		$this->vista_edicion = new VistaOrdenComisionEdicion();
		$this->vista_agregar_item = new VistaOrdenComisionAgregarItem();
		$this->vista_edicion_item = new VistaOrdenComisionEdicionItem();
		$this->vista_formato_impresion = new VistaFormatoImpresion();
		$this->vista_edicion_encabezado = new VistaOrdenComisionEdicionEncabezado();
		$this->vista_edicion_pie = new VistaOrdenComisionEdicionPie();
		$this->vista_edicion_cabecera = new VistaOrdenComisionEdicionCabecera();

		$this->mensaje = "";
	}
	
	public function guardarRegistroOriginal($original)
    {
		$_SESSION['id_original'] = $original['id'];
		$_SESSION['codigo_comision_original'] = $original['codigo_comision'];
		$_SESSION['asunto_original'] = $original['asunto'];
		$_SESSION['fecha_original'] = $original['fecha'];
		$_SESSION['hora_original'] = $original['hora'];
		$_SESSION['encabezado_original'] = $original['encabezado'];
		$_SESSION['pie_original'] = $original['pie'];
		$_SESSION['es_conjunta_original'] = $original['es_conjunta'];
		$_SESSION['principal_original'] = $original['principal'];
		$_SESSION['publicada_original'] = $original['publicada'];
    }
	
	/**
	 * Se listan las comisiones
	 * @param  string $mensaje      	Texto informativo
	 * @param  string $tipo_mensaje 	Tipo del mensaje
	 * @return html               		Vista del listado
	 */
    public function listar($mensaje = '', $tipo_mensaje = '')
	{
		$filtro = Array();
		
		$id_editado = LibreriaGeneral::recoge('id_editado', 0);
		if ($id_editado != 0) {
			$this->modelo->desmarcarEnEdicion($id_editado);
		}

		$filtro['pagina'] = LibreriaGeneral::recoge('pagina');

		$filtro['f_comision'] = LibreriaGeneral::recoge('f_comision');
		
		$f_fecha = LibreriaGeneral::recoge('f_fecha');
		if ( isset($f_fecha) && $this->esFechaValida($f_fecha) )
			$filtro['f_fecha'] = $this->modelo->formatearFechaMySQL($f_fecha);
		else
			$filtro['f_fecha'] = '';
		
		$campo_orden = LibreriaGeneral::recoge('campo_orden');
		if ( !empty($campo_orden) )
			$filtro['campo_orden'] = $campo_orden;
		else {
			$filtro['campo_orden'] = $this->campo_orden_por_defecto;
			$_SESSION['ultimo_campo'] = '';
		}
			
		$filtro['sentido'] = LibreriaGeneral::recoge('sentido');

		if (!isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $filtro['campo_orden']) {
			$_SESSION['ultimo_campo'] = $filtro['campo_orden'];
			$_SESSION['ultimo_sentido'] = 'asc';
		} else {
			$_SESSION['ultimo_sentido'] = ($_SESSION['ultimo_sentido'] == 'asc' && $filtro['sentido'] == '') 
				? 'desc' 
				: 'asc';
		}

		$filtro['rango'] = $this->rango_paginacion;

		$this->modelo->setFiltro($filtro);

		$filtro['cantidad'] = $this->modelo->obtenerCantidad();

		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		if ($filtro['pagina'] == '' || $filtro['pagina'] == 0) {
			$filtro['inicio'] = 0;
			$filtro['pagina'] = 1;
		} else {
			$filtro['inicio'] = ($filtro['pagina'] - 1) * $filtro['rango'];
		}

		$filtro['pagina_ant'] = $filtro['pagina'] - 1;
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;

		$this->modelo->setFiltro($filtro);
			
		$datos['info'] = $this->modelo->listar();
		
		$datos['comisiones_internas'] = $this->modelo->obtenerComisionesInternas();
		
		$this->vista_grilla->mostrar($datos, $mensaje, $tipo_mensaje, $filtro);
    }

    /**
     * Se edita una comisión
     * @param  integer $id           	Identificador de la comisión (0 por defecto al crearse una)
     * @param  string  $mensaje      	Texto informativo
     * @param  string  $tipo_mensaje 	Tipo del mensaje
     * @return html    					Vista de la edición
     */
    public function editar($id = 0, $mensaje = '', $tipo_mensaje = '')
	{
		$id = ( $id != 0 ) ? $id : LibreriaGeneral::recoge('id', 0);
		
		$filtro['pagina'] = LibreriaGeneral::recoge('pagina');
		
		$datos = ($id != 0) ? $this->modelo->obtenerRegistro($id) : null;
		
		$datos['comisiones_internas'] = $this->modelo->obtenerComisionesInternas();

		if (empty($mensaje) && empty($tipo_mensaje)) {
			$mensaje = (isset($_SESSION['mensaje'])) ? $_SESSION['mensaje'] : '';
			$tipo_mensaje = (isset($_SESSION['tipo_mensaje'])) ? $_SESSION['tipo_mensaje'] : '';

			$_SESSION['mensaje'] = $_SESSION['tipo_mensaje'] = ''; 
 		}

		$this->vista_edicion->mostrar($datos, $filtro, $mensaje, $tipo_mensaje);
    }
	
	/**
	 * Se ingresa una comisión
	 * @return html 	Vista de la edición
	 */
    public function insertar() {
        
		$datos = $_REQUEST;
		
		// Si ya existe la Orden para esa Comisión y fecha
		if ( $this->modelo->existe($datos['codigo_comision'], $datos['fecha']) )
		{
			$this->editar(
				$this->modelo->obtenerUltimoId(), 
				"La Orden del D&iacute;a de Comisi&oacute;n ya se ha creado en dicha fecha.", 
				3);
		} else {
			$codigo = ($datos['es_conjunta']) ? $datos['principal'] : $datos['codigo_comision'];

			// Se obtiene el encabezado y el pie de la Comisión anterior (si existe una del mismo tipo)
			$info_comision_anterior = $this->modelo->obtenerEncabezadoPie($codigo);
			
			if (isset($info_comision_anterior) && count($info_comision_anterior) > 0) {
				$datos['encabezado'] = $info_comision_anterior[0]['encabezado'];
				$datos['pie'] = $info_comision_anterior[0]['pie'];
			}

			// Si se crea la Orden
			if ($this->modelo->insertar($datos)) {
				
				// Se obtiene el último Id generado
				$id = $this->modelo->obtenerUltimoId();

				// Se vuelve a obtener el registro de la Comisión (individual o conjunta) 
				// para utilizar los datos generados durante la inserción
				$datos = $this->modelo->obtenerRegistro($id);
				
				// Una vez creada la orden de Comisión, se precargan los ítems 
				// (los expedientes con su marca respectiva), la marca 4 no existe
				$this->precargarOrdenComisionPorMarca($id, $datos['principal'], 1);
				$this->precargarOrdenComisionPorMarca($id, $datos['principal'], 2);
				$this->precargarOrdenComisionPorMarca($id, $datos['principal'], 3);
				$this->precargarOrdenComisionPorMarca($id, $datos['principal'], 5);
				
				$this->editar($id, "Se cre&oacute; con &eacute;xito la Orden del D&iacute;a de Comisi&oacute;n.", 1);
			} else {
				$this->editar(
					$this->modelo->obtenerUltimoId(),
				 	"Error al crear la Orden del D&iacute;a de Comisi&oacute;n.", 
				 	2
				 );
			}
		}
    }

    /**
     * Se agregan los ítems en la Orden de Comisión (los expedientes en su marca respectiva)
     * @param  integer 	$id_orden_comision Identificador de la Orden de Comisión
     * @param  string 	$codigo_comision   Código de la Comisión
     * @param  integer 	$marca_comision    Valor que representa la marca respectiva en la Comisión
     */
    private function precargarOrdenComisionPorMarca($id_orden_comision, $codigo_comision, $marca_comision) {

    	// Se obtienen los Expedientes de una Comisión y marca determinada
		$expedientes = $this->modelo->obtenerExpedientesPorComision($codigo_comision, $marca_comision);
		
		$cant_expedientes = (isset($expedientes)) ? count($expedientes) : 0;

		// Por cada expediente de la Marca respectiva
		for ($i=0; $i < $cant_expedientes; $i++) {
			$expe = &$expedientes[$i];

			$datos['id_orden_comision'] = $id_orden_comision;
			$datos['anio'] = $expe['anio'];
			$datos['tipo'] = $expe['tipo'];
			$datos['numero'] = $expe['numero'];
			$datos['marca_comision'] = $expe['marca_comision'];
			$datos['extracto'] = '';

			// Primero se obtienen los Extractos de los proyectos del Expediente
			$extractos = $this->modelo->obtenerExtractosPorExpediente($expe['anio'], $expe['tipo'], $expe['numero']);
			$cant_extractos = (isset($extractos)) ? count($extractos) : 0;
			
			// Por cada Extracto
			for ($e=0; $e < $cant_extractos; $e++)
				// Se va agregando un extracto con el otro (de poseer más de uno)
				$datos['extracto'] .= $extractos[$e]['extracto'];

			$this->modelo->insertarItem($datos);
		}
    }

	/**
	 * Se modifica una comisión
	 * @return html 	Vista de la edición
	 */
    public function modificar() {

		$datos = $_REQUEST;

		if ($this->modelo->modificar($datos))
			$this->editar($datos['id'], "Se modific&oacute; con &eacute;xito la Orden del D&iacute;a de Comisi&oacute;n", 1);
		else
			$this->editar($datos['id'], "Error al modificar la Orden del D&iacute;a de Comisi&oacute;n", 2);
	}

	/**
	 * Se elimina una comisión
	 * @return html 	Vista del listado
	 */
	public function eliminar() {

		$id = LibreriaGeneral::recoge('id');

		if ($this->modelo->eliminar($id))
			$this->listar("Se elimin&oacute; con &eacute;xito la Orden de Comisi&oacute;n.", 1);
		else
			$this->listar("No se ha eliminado la Orden de Comisi&oacute;n, debe poseer un &Iacute;tem.", 2);
	}
    
    public function agregarItem() {

		$id_orden_comision = LibreriaGeneral::recoge('id', 0);
		$marca = LibreriaGeneral::recoge('marca', 0);

		$info_orden = $this->modelo->obtenerRegistro($id_orden_comision);
		
		$expe = $this->modelo->obtenerExpedientesPorComision($info_orden['codigo_comision'], $marca);

		$cant_expe = (isset($expe)) ? count($expe) : 0;

		$expe_a_utilizar = null;
		for ($i=0; $i < $cant_expe; $i++) {
			// Se verifica si el expediente NO es item de la Orden de Comisión
			if ( ! $this->modelo->esItemOrdenComision($id_orden_comision, $expe[$i]['anio'], $expe[$i]['tipo'], $expe[$i]['numero'])) {
				$expe_a_utilizar[] =  $expe[$i];
			}
		}
		
		$this->vista_agregar_item->mostrar($info_orden, $marca, $expe_a_utilizar);
    }

    public function guardarItems() {
    	
		$datos = $_REQUEST;
		
		$cant_expe_a_agregar = (isset($datos['a_agregar'])) ? count($datos['a_agregar']) : 0;
		// Por cada expediente a agregar
		for ($i=0; $i < $cant_expe_a_agregar; $i++) {

			$expe = explode("___", $datos['a_agregar'][$i]);
			
			$datos['anio'] = $expe[0];
			$datos['tipo'] = $expe[1];
			$datos['numero'] = $expe[2];

			$datos['extracto'] = '';

			// Primero se obtienen los Extractos de los proyectos del Expediente
			$extractos = $this->modelo->obtenerExtractosPorExpediente($datos['anio'], $datos['tipo'], $datos['numero']);
			$cant_extractos = (isset($extractos)) ? count($extractos) : 0;
			
			// Por cada Extracto
			for ($e=0; $e < $cant_extractos; $e++)
				// Se va agregando un extracto con el otro (de poseer más de uno)
				$datos['extracto'] .= $extractos[$e]['extracto'];

			// Si no se inserta el item
			if ( ! $this->modelo->insertarItem($datos))
				$this->editar(
					$datos['id_orden_comision'], 
					"Error al agregar los &iacute;tems a la Orden del D&iacute;a de Comisi&oacute;n", 
					2);
		}
		// Se vuelve a la edición de la Orden de la Comisión
		$this->editar(
			$datos['id_orden_comision'], 
			"Se agregaron con &eacute;xito los &iacute;tems a la Orden del D&iacute;a de Comisi&oacute;n", 
			1);
    }

    public function editarItem() {

		$id = LibreriaGeneral::recoge('id', 0);

		$registro = $this->modelo->obtenerRegistroItem($id);

		$this->vista_edicion_item->mostrar($registro);
    }

    /**
     * Se modifica un Item
     * @return html 	Vista de la edición
     */
    public function modificarItem() {

		$datos = $_REQUEST;
		
		if ($this->modelo->modificarItem($datos))
			$this->editar($datos['id_orden_comision'], "Se modific&oacute; con &eacute;xito el &iacute;tem de la Orden del D&iacute;a de Comisi&oacute;n", 1);
		else
			$this->editar($datos['id_orden_comision'], "Error al modificar el &iacute;tem de la Orden del D&iacute;a de Comisi&oacute;n", 2);
	}
    
    /**
     * Se elimina un Item
     * @return html 	Vista de la edición
     */
    public function eliminarItem() {

		$id = LibreriaGeneral::recoge('id', 0);

		$id_orden_comision = $this->modelo->obtenerIdOrdenComisionPorItem($id);

		if ($this->modelo->eliminarItem($id))
			$this->editar($id_orden_comision, "Se elimin&oacute; con &eacute;xito el &iacute;tem de la Orden del D&iacute;a de Comisi&oacute;n", 1);
		else
			$this->editar($id_orden_comision, "No se ha eliminado el &iacute;tem de la Orden del D&iacute;a de Comisi&oacute;n", 2);
    }

    public function editarEncabezado() {

		$id = LibreriaGeneral::recoge('id', 0);

		$encabezado = $this->modelo->obtenerEncabezado($id);

		$this->vista_edicion_encabezado->mostrar($id, $encabezado);
    }

    public function modificarEncabezado() {

		$datos = $_REQUEST;
		
		if ($this->modelo->modificarEncabezado($datos))
			$this->editar($datos['id'], "Se modific&oacute; con &eacute;xito el encabezado de la Orden del D&iacute;a de Comisi&oacute;n", 1);
		else
			$this->editar($datos['id'], "Error al modificar el encabezado de la Orden del D&iacute;a de Comisi&oacute;n", 2);
	}
    
    public function editarPie() {

		$id = LibreriaGeneral::recoge('id', 0);

		$pie = $this->modelo->obtenerPie($id);

		$this->vista_edicion_pie->mostrar($id, $pie);
    }

    public function modificarPie() {

		$datos = $_REQUEST;
		
		if ($this->modelo->modificarPie($datos))
			$this->editar($datos['id'], "Se modific&oacute; con &eacute;xito el pie de la Orden del D&iacute;a de Comisi&oacute;n", 1);
		else
			$this->editar($datos['id'], "Error al modificar el pie de la Orden del D&iacute;a de Comisi&oacute;n", 2);
	}
    
    public function editarCabecera() {

		$id = LibreriaGeneral::recoge('id', 0);

		$datos = $this->modelo->obtenerRegistro($id);

		$this->vista_edicion_cabecera->mostrar($datos);
    }

    public function modificarCabecera() {

		$datos = $_REQUEST;
		
		if ($this->modelo->modificar($datos))
			$this->editar($datos['id'], "Se modific&oacute; con &eacute;xito la cabecera de la Orden del D&iacute;a de Comisi&oacute;n", 1);
		else
			$this->editar($datos['id'], "Error al modificar la cabecera de la Orden del D&iacute;a de Comisi&oacute;n", 2);
	}
    
	/**
	 * Se genera el PDF de la Orden del Día de Comisión
	 */
	public function crearFormatoPdf() {

		$id = LibreriaGeneral::recoge('id', 0);

		$datos = $this->modelo->obtenerRegistro($id);
		
		// Opciones evaluadas --------------------------

		// html2pdf en backend (NO centra el pie del documento)
		//$this->vista_formato_impresion->mostrarPDF($datos);

		// htmltopdf en JS
		// (genera una imagen, NO sirve para documentos largos)
		//$this->vista_formato_impresion->mostrarHTMLImpresion($datos);

		// Con wkhtmltopdf (NO se usa en Producción por desactualización del server :/)
		//$this->vista_formato_impresion->convertirHtmlToPdf($datos);

		// Enviar a impresión => Guardar como PDF (con el nombre de la comisión)
		$this->vista_formato_impresion->mostrarHTMLParaImpresion($datos);

	}

	/**
	 * Se genera el HTML de la Orden del Día de Comisión
	 * @return string	$nombre_archivo 	Nombre del archivo HTML de la Orden del día de la Comision
	 */
	public function crearFormatoHTML() {

		$id = LibreriaGeneral::recoge('id', 0);

		$datos = $this->modelo->obtenerRegistro($id);
		
		$this->vista_formato_impresion->mostrarHTML($datos);
	}
	
	/**
	 * Se confirma la publicación de la Orden del Día de Sesión en el sitio web
	 */
	public function confirmarPublicacion() {
		
		$id = LibreriaGeneral::recoge('id');
		$pagina = LibreriaGeneral::recoge('pagina');

		if ($this->modelo->confirmarPublicacion($id)) {

			// Se genera el archivo de texto, utilizado como flag para la ejecución
			// del proceso de actualización de las Ordenes del Día de Comisión
			// --- Nota: se genera el mismo archivo utilizado para Gacetillas, 
			// --- porque se encuentra en el mismo script .sh de actualización.
			$archivo_txt = fopen(RUTA_RAIZ.'abms/procesargace.txt', 'w');
			fclose($archivo_txt);

			// Se genera y guarda el html, de la orden del día, en el directorio a sincronizar con la web
			$datos = $this->modelo->obtenerRegistro($id);
			$this->vista_formato_impresion->generarHtml($datos);

			$this->listar("Orden del D&iacute;a de Comisi&oacute;n publicada", 1, $pagina);
		} else {
			$this->listar("No se ha podido publicar la Orden del D&iacute;a de Comisi&oacute;n", 2, $pagina);
		}
	}

	/**
	 * Se carga el documento pdf de la orden del día de comisión firmada
	 */
	private function cargarDocumento($id, $info_archivo)
	{
		if (isset($id) && isset($info_archivo['name']) && $info_archivo['name'] != '') {

			$archivo_a_guardar = $info_archivo['tmp_name'];
			
			$nombre_archivo = LibreriaGeneral::eliminarEspacios($info_archivo['name']);

			if ($info_archivo['error'] == 4) {
				$_SESSION['mensaje'] = "No se ha subido el archivo ".$nombre_archivo;
				$_SESSION['tipo_mensaje'] = 2;
			}

			if ($info_archivo['error'] == 0) {
				$extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
			
				if ($info_archivo['size'] > TAMANIO_MAXIMO_FOTO) {
					$_SESSION['mensaje'] = $nombre_archivo." supera el tama&ntilde;o m&aacute;ximo permitido.";
					$_SESSION['tipo_mensaje'] = 2;
				}
				elseif( ! in_array($extension, ['pdf']) ) {
					$_SESSION['mensaje'] = "La extensi&oacute;n de ".$nombre_archivo." no es v&aacute;lida.";
					$_SESSION['tipo_mensaje'] = 2;
				}
				else {
					move_uploaded_file($archivo_a_guardar, RUTA_ORDENES_COMISION_FIRMADOS . $id . '.pdf');
				}
			}

			if ( $this->tipo_mensaje != 2 ) {
				$_SESSION['mensaje'] = "Se ha realizado la carga satisfactoriamente.";
				$_SESSION['tipo_mensaje'] = 1;
			}
		} else {
			$_SESSION['mensaje'] = "No se ha recibido el documento a cargar.";
			$_SESSION['tipo_mensaje'] = 2;
		}
	}

	public function upload() {

		$id = LibreriaGeneral::recoge('id');
		$pagina = LibreriaGeneral::recoge('pagina');
		$info_documento = $_FILES['documento'];
		
		if ( isset($info_documento['name'][0]) && $info_documento['name'][0] != '' )
			$this->cargarDocumento($id, $info_documento);

		header('Location: '.URL_ABMS.'?controlador=ordenes_comision&accion=editar&id='.$id.'&pagina='.$pagina);
		exit;
	}

	public function eliminarDocumentoFirmado() {

		$id = LibreriaGeneral::recoge('id');
		$pagina = LibreriaGeneral::recoge('pagina');
		$documento = LibreriaGeneral::recoge('documento');

		if (is_file(RUTA_ORDENES_COMISION_FIRMADOS . $documento)) {
			unlink(RUTA_ORDENES_COMISION_FIRMADOS . $documento);

			$_SESSION['mensaje'] = "Se ha eliminado el documento satisfactoriamente.";
			$_SESSION['tipo_mensaje'] = 1;
		} else {
			$_SESSION['mensaje'] = "No se ha eliminado el documento.";
			$_SESSION['tipo_mensaje'] = 2;
		}

		header('Location: '.URL_ABMS.'?controlador=ordenes_comision&accion=editar&id='.$id.'&pagina='.$pagina);
		exit;
	}
}

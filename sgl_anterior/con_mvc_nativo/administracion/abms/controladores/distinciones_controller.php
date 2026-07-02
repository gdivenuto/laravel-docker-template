<?php
if (!isset($_SESSION)) {
	session_start();
}

//Incluye el modelo que corresponde
require_once RUTA_MODELOS . "distinciones.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "distinciones/grilla.php";
require_once RUTA_VISTAS . "distinciones/edicion.php";

class distinciones_controller extends ControllerBase {
	
	public function __construct() {

		parent::__construct();

		$this->campo_orden_por_defecto = 'd_codigo';

		// Se crea una instancia del modelo
		$this->modelo = new distincionesModel();
	
		// Se crea una instancia de la Vista
		$this->vista_grilla = new VistaDistincionesGrilla();
		$this->vista_edicion = new VistaDistincionesEdicion();
	
		// Se inicializa el mensaje de resultados
		$this->mensaje = "";
	}
	
	public function guardarRegistroOriginal($original) {

		$_SESSION['d_codigo_original'] = $original['d_codigo'];
		$_SESSION['d_tipo_original'] = $original['d_tipo'];
		$_SESSION['d_fecha_original'] = $original['d_fecha'];
		$_SESSION['d_acto_original'] = $original['d_acto'];
		$_SESSION['d_expediente_original'] = $original['d_expediente'];
		$_SESSION['d_contenido_original'] = $original['d_contenido'];
		$_SESSION['d_habilitado_original'] = $original['d_habilitado'];
    }
	
    public function listar($mensaje = '', $tipo_mensaje = '')
	{
		$filtro = Array();
		
		// FILTRO POR FECHA
		$f_fecha = LibreriaGeneral::recoge('f_fecha');
		$filtro['f_fecha'] = ( isset($f_fecha) && $this->esFechaValida($f_fecha) ) ? $this->modelo->formatearFechaMySQL($f_fecha) : '';
		
		// FILTRO POR DISTINCION
		$filtro['f_distincion'] = LibreriaGeneral::recoge('f_distincion');
		
		// FILTRO POR ACTO
		$filtro['f_acto'] = LibreriaGeneral::recoge('f_acto');
		
		// FILTRO POR NUMERO DE EXPEDIENTE DEL HCD
		$filtro['f_expediente'] = LibreriaGeneral::recoge('f_expediente');
		
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
			} else {
				// SE MUESTRAN LOS ÚLTIMOS (VALOR DEL RANGO) REGISTROS
				$filtro['inicio'] = $filtro['cantidad'] - $filtro['rango'];
			}
		} else {
			$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
		}

		$filtro['pagina_ant'] = $filtro['pagina'] - 1; // para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1; // para la pagina posterior

		// SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
		$_SESSION['filtro_distinciones'] = $filtro;
			
		//Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);
				
		// SE OBTIENE EL LISTADO
		$listado = $this->modelo->listar();
		
		//se muestra el listado
		$this->vista_grilla->mostrar($listado, $mensaje, $tipo_mensaje, $filtro);
    }

    public function editar($datos_formulario = null, $mensaje = '', $tipo_mensaje = '') {

		// Si NO se viene del formulario de edición por un error
		if ($datos_formulario === null) {
			// Se recibe el Id para su edición
			$id = LibreriaGeneral::recoge('id', 0);

			// Se busca el registro en la base de datos
			$datos = $this->modelo->obtenerRegistro($id);

			// Si existe
			if ($datos['d_codigo']) {
				// Se audita la consulta del registro
				//$this->modelo->auditarConsultaRegistro($datos);

				// Se guarda el registro en sesion para verificar luego si no ha modificado otro usuario
				$this->guardarRegistroOriginal($datos);

				// Se marca para saber que se encuentra en edición
				//$this->modelo->marcarEnEdicion($datos['d_codigo']);

				$datos['pagina'] = LibreriaGeneral::recoge('pagina', 1);

				$datos = $this->retirarBarraInvertida($datos);
			} else {
				// En caso de editarse un NUEVO registro
				$datos = null;
			}

		} else {
			// SI SE VIENE DEL FORMULARIO DEBIDO A UN ERROR
			$datos = $datos_formulario;
		}

		$this->vista_edicion->mostrar($datos, $mensaje, $tipo_mensaje);
	}
    
    public function insertar()
	{    
		$datos = $_REQUEST;
		
		// SI NO EXISTE, PARA QUE DOS USUARIOS NO INGRESEN EL MISMO REGISTRO
		if ( $this->modelo->existe($datos['d_acto']) ) {	
			$this->listar("La Distinci&oacute;n se ha ingresado previamente", 2);
		} elseif ($this->modelo->insertar($datos)) {
				$this->listar("Se agreg&oacute; con &eacute;xito la Distinci&oacute;n", 1);
		} else {
			$this->listar("Error al agregar la Distinci&oacute;n", 2);
		}
    }

	/**
	 * Se modifica un registro determinado
	 */
	public function modificar() {
		parent::modificarBase();
	}

	/**
	 * Se elimina un registro determinado
	 */
	public function eliminar() {
		parent::eliminarBase();
	}

	/**
	 * Se modifica el estado Habilitado|Deshabilitado
	 */
	public function modificarEstado() {
		parent::modificarEstadoBase();
	}

	public function obtenerCodigoTipo($nombre_tipo) {

		switch ($nombre_tipo) {

			case "Ciudadano Ejemplar":
				$cod_tipo = "CE";
				break;
			case "Ciudadano Ilustre":
				$cod_tipo = "CI";
				break;
			case "Ciudadano Marplatense":
				$cod_tipo = "CM";
				break;
			case "Compromiso Ambiental":
				$cod_tipo = "CA";
				break;
			case "Compromiso Social":
				$cod_tipo = "CS";
				break;
			case "Deportista Insigne":
				$cod_tipo = "DI";
				break;
			case "Hijo Dilecto":
				$cod_tipo = "HD";
				break;
			case "M&eacute;rito Acad&eacute;mico":
				$cod_tipo = "MA";
				break;
			case "M&eacute;rito Ciudadano":
				$cod_tipo = "MC";
				break;
			case "M&eacute;rito Deportivo":
				$cod_tipo = "MD";
				break;
			case "Reconocimiento":
				$cod_tipo = "RE";
				break;
			case "Servicio Solidario":
				$cod_tipo = "SS";
				break;
			case "Vecino Destacado":
				$cod_tipo = "VD";
				break;
			case "Visitante Ilustre":
				$cod_tipo = "VI";
				break;
			case "Visitante Notable":
				$cod_tipo = "VN";
				break;
		}
		
		return $cod_tipo;
	}
	
	// SE UTILIZA UNA SOLA VEZ
	// PARA IMPORTAR EL CONTENIDO DE UN ARCHIVO .txt A LA BASE DE DATOS
	// public function importarDatosDistinciones()
	// {
	// 	$datos_distincion = Array();

	// 	// NOMBRE DEL ARCHIVO A IMPORTAR SU CONTENIDO EN LA BASE DE DATOS
	// 	$nombre_archivo = LibreriaGeneral::recoge('nombre_archivo');

	// 	// SE ABRE EL ARCHIVO PARA LECTURA
	// 	$archivo = fopen($nombre_archivo,'r');

	// 	// SE ALMACENA SU CONTENIDO EN UNA VARIABLE PARA TRABAJAR CON ÉL
	// 	$contenido = fread($archivo, filesize($nombre_archivo));

	// 	// SE CIERRA EL ARCHIVO
	// 	fclose($archivo);

	// 	// SE TOMAN LAS LINEAS DEL CONTENIDO POR CADA SALTO DE LINEA ENCONTRADO
	// 	// \n = símbolo de fin de línea
	// 	$lineas = explode("\n",$contenido);

	// 	// dato: CONTADOR PARA DETERMINAR EL DATO QUE LE CORRESPONDE
	// 	// 1: d_tipo
	// 	// 2: d_fecha
	// 	// 3: d_acto
	// 	// 4: d_expediente
	// 	// 5: d_contenido
		
	// 	$dato = 1;
		
	// 	// POR CADA LINEA DEL CONTENIDO
	// 	foreach ($lineas as $linea)
	// 	{
	// 		// SE TOMA EL PRIMER CARACTER DE LA LINEA
	// 		$primer_caracter = substr($linea, 0, 1);
			
	// 		// SI NO ES UN ESPACIO VACIO
	// 		if ( $primer_caracter != '' )
	// 		{
	// 			// SI ES UN # (LA LINEA DE TIPO DE DISTINCION COMIENZA CON #, EJEMPLO: #M&eacute;rito Ciudadano)
	// 			if ( $primer_caracter == '#' )
	// 			{
	// 				// SE TOMA EL TEXTO DEL TIPO, DESPUES DEL #
	// 				$datos_distincion['d_tipo'] = substr($linea, 1);
					
	// 				// SE ASIGNA EL CODIGO DEL TIPO DE DISTINCIÓN SEGUN SU NOMBRE
	// 				$datos_distincion['d_tipo'] = $this->obtenerCodigoTipo($datos_distincion['d_tipo']);
					
	// 				// PARA SEGUIR ALMACENANDO EL TIPO DE DISTINCION HASTA QUE CAMBIE SU VALOR
	// 				$d_tipo_auxiliar = $datos_distincion['d_tipo'];
					
	// 				$dato = 2;// d_fecha
	// 			}
	// 			else
	// 			{
	// 				switch($dato)
	// 				{
	// 					case '2':
	// 						// PRIMERO SE DEFINE EL CODIGO
	// 						$datos_distincion['d_codigo'] = $this->modelo->obtenerUltimoCodigo() + 1;
							
	// 						// SI NO CAMBIÓ EL TIPO, SE UTILIZA EL MISMO
	// 						if ( !$datos_distincion['d_tipo'] )
	// 						{
	// 							$datos_distincion['d_tipo'] = $d_tipo_auxiliar;
	// 						}
							
	// 						// LUEGO SE ASIGNA LA FECHA
	// 						$datos_distincion['d_fecha'] = $linea;
							
	// 						// SE SIGUE CON EL SIGUIENTE DATO: d_acto
	// 						$dato = 3;
	// 						break;
	// 					case '3':
	// 						// SE ASIGNA EL ACTO
	// 						$datos_distincion['d_acto'] = $linea;
							
	// 						// SE SIGUE CON EL SIGUIENTE DATO: d_expediente
	// 						$dato = 4;
	// 						break;
	// 					case '4':
	// 						// SE ASIGNA EL EXPEDIENTE
	// 						$datos_distincion['d_expediente'] = $linea;
							
	// 						// SE SIGUE CON EL SIGUIENTE DATO: d_contenido
	// 						$dato = 5;
	// 						break;
	// 					case '5':
	// 						// SE ASIGNA EL CONTENIDO
	// 						$datos_distincion['d_contenido'] = $linea;
							
	// 						//fputs(fopen("datos_distincion.txt", 'w'), print_r($datos_distincion, true));
								
	// 						// SE GRABAN LOS DATOS DE LA DISTINCION EN LA BASE DE DATOS, EN CASO DE ERROR, SALE DEL CICLO
	// 						if ( !$this->modelo->insertar($datos_distincion) )
	// 						{
	// 							$tipo_mensaje = 2;
	// 							break;
	// 						}
	// 						else
	// 						{
	// 							$tipo_mensaje = 1;
								
	// 							// UNA VEZ GUARDADO EL REGISTRO, SE LIMPIA EL VECTOR DE DATOS
	// 							$datos_distincion = null;
								
	// 							// SE VUELVE A ASIGNAR EN 2 (d_fecha) LA BANDERA, PARA UN REGISTRO NUEVO (SI HAY)
	// 							$dato = 2;
	// 						}
							
	// 						break;
	// 				}
	// 			}
	// 		}
	// 	}

	// 	if ( $tipo_mensaje == 2 )
	// 	{
	// 		$this->mensaje = 'La importaci&oacute;n a la base de datos no se ha podido completar.';
	// 	}
	// 	else
	// 	{
	// 		$this->mensaje = 'La importaci&oacute;n a la base de datos se realiz&oacute; con &eacute;xito.';
	// 	}
		
	// 	// SE MUESTRA EL LISTADO DE DISTINCIONES Y UN MENSAJE CON EL RESULTADO DE LA OPERACION
	// 	$this->listar($this->mensaje, $this->tipo_mensaje);
	// }
	
}
?>

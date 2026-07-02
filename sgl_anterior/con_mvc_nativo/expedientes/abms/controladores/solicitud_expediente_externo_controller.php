<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

// Configuracion de rutas
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/config/path_config.php');

// Layer de negocio
require_once(PATH_SGL_LAYER_NEGOCIO_PRESTAMOS.'ng_prestamos.php');

//Incluye la vista que corresponde
require 'vistas/solicitud_expediente_externo.php';

class solicitud_expediente_externo_controller extends ControllerBase
{
	// contendrá una instancia de la capa de negocio del circuito de prestamos y solicitudes al D.E.
	public $ng_prestamos;
	public $rango_paginador;
	
	public function __construct()
	{
		// almacena una instancia de la capa de negocio del circuito de prestamos
		$this->ng_prestamos = new ng_prestamos();
		// por defecto la página muestra 11 registros
		$this->rango_paginador = 9;
	}

	/**
	 * Guarda una instancia de SolicitudExpedienteExterno en formato Json en la Sesión
	 * @param SolicitudExpedienteExterno $solicitud_ee
	 */
	public function guardarRegistroOriginal(SolicitudExpedienteExterno $solicitud_ee)
	{
		$_SESSION['solicitud_ee_original'] = $solicitud_ee->ToJson();
	}

	/**
	 * Obtiene una cadena en JSON e intenta regenerar el estado de la instancia a partir del elemento serializado.
	 * @return SolicitudExpedienteExterno|NULL
	 */
	public function obtenerRegistroOriginal()
	{
		// Si existe la Solicitud Original en la Sesión, y no está vacía
		if (array_key_exists('solicitud_ee_original', $_SESSION) && (!empty($_SESSION['solicitud_ee_original'])))
		{
			// Se instancia una nueva solicitud
			$resultado = new SolicitudExpedienteExterno();
				
			// Obtiene una cadena en JSON e intenta regenerar el estado de la instancia a partir del elemento serializado.
			$resultado->FromJson($_SESSION['solicitud_ee_original']);
				
			return $resultado;
		}
		else
		{
			return null;
		}
	}

	/**
	 *  Devuelve una colección de parámetros, los cuales son enviados desde la Vista
	 * @return array $parametros_vista, Colección de parámetros para ser utilizados en la Vista
	 */
	public function armarConjuntoParametrosSolicitudes($campo_orden_por_defecto)
	{
		$parametros_vista['mensaje'] = $this->parametrosObtenerONull('mensaje');
		$parametros_vista['tipo_mensaje'] = $this->parametrosObtenerONull('tipo_mensaje');
	
		// Dirección para la paginación (Primero, Anterior, Siguiente, Último)
		$parametros_vista['sentido'] = $this->parametrosObtenerONull('sentido');
	
		//se establece el campo por el cual ordenar
		$campo_orden = $this->parametrosObtenerONull('campo_orden');
	
		if ( !is_null($campo_orden) && !empty($campo_orden) )
		{
			$parametros_vista['campo_orden'] = $campo_orden;
		}
		else
		{
			//por defecto
			$parametros_vista['campo_orden'] = $campo_orden_por_defecto;
			$_SESSION['ultimo_campo'] = '';
		}
	
		if ( !isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $parametros_vista['campo_orden'] )
		{
			// Si es la primera vez que carga la pagina
			// o se esta cambiando el campo por el que se ordena
			$_SESSION['ultimo_campo'] = $parametros_vista['campo_orden'];
			$_SESSION['ultimo_sentido'] = ORDEN_ASCENDENTE;
		}
		else
		{
			// Si se hizo clic en el mismo que ya estaba ordenado antes se cambia el sentido
			if ($_SESSION['ultimo_sentido'] == ORDEN_ASCENDENTE)
			{
				$_SESSION['ultimo_sentido'] = ORDEN_DESCENDENTE;
			}
			else
			{
				$_SESSION['ultimo_sentido'] = ORDEN_ASCENDENTE;
			}
		}
	
		// cantidad de registros a mostrar
		$parametros_vista['rango'] = $this->rango_paginador;

		// Primero se obtiene la cantidad total de solicitudes
		$parametros_vista['cantidad'] = $this->ng_prestamos->ObtenerSolicitudesExpedientesExternosCantidadResultados(
				null, null, null, null, null,
				null, null, null, null, null, null,
				null, null, null);
			
		// Número total de páginas
		$parametros_vista['nro_paginas'] = ceil($parametros_vista['cantidad'] / $parametros_vista['rango']);
	
		// Se obtiene el valor de la pagina
		$parametros_vista['pagina'] = $this->parametrosObtenerONull('pagina');
	
		// SI NO SE RECIBIÓ LA PÁGINA
		if ( !$parametros_vista['pagina'] )
		{
			// SE ESTABLECE LA ÚLTIMA
			$parametros_vista['pagina'] = ( $parametros_vista['nro_paginas'] > 0 ) ? $parametros_vista['nro_paginas'] : 1;
	
			// SI LA CANTIDAD ES MENOR AL RANGO DE PAGINA
			if ( $parametros_vista['cantidad'] < $parametros_vista['rango'] )
			{
				$parametros_vista['inicio'] = ($parametros_vista['pagina'] * $parametros_vista['rango']) - $parametros_vista['rango'];
			}
			else
			{
				// SE MUESTRAN LOS ÚLTIMOS (VALOR DEL RANGO) REGISTROS
				$parametros_vista['inicio'] = $parametros_vista['cantidad'] - $parametros_vista['rango'];
			}
		}
		else
		{
			$parametros_vista['inicio'] = ($parametros_vista['pagina'] * $parametros_vista['rango']) - $parametros_vista['rango'];
		}
		$parametros_vista['pagina_ant'] = $parametros_vista['pagina'] - 1;	// Página anterior
		$parametros_vista['pagina_sgte'] = $parametros_vista['pagina'] + 1;	// Página posterior
	
		return $parametros_vista;
	}
	
	/**
	 * Lista las Solicitudes
	 * @param string $mensaje
	 * @param string $tipo_mensaje
	 */
	public function listar($mensaje = '', $tipo_mensaje = '')
	{
		// Se comienza a armar una colección de parámetros, los cuales son enviados desde la interfaz de usuario
		$parametros_vista = $this->armarConjuntoParametrosSolicitudes('fecha_solicitud_hcd');
	
		// Si se recibe un mensaje y su tipo internamente en el controlador
		if ($mensaje != '' && $tipo_mensaje != '')
		{
			$parametros_vista['mensaje'] = $mensaje;
			$parametros_vista['tipo_mensaje'] = $tipo_mensaje;
		}
	
		// Luego se obtienen las solicitudes
		$solicitudes = $this->ng_prestamos->ObtenerSolicitudesExpedientesExternos( 
														null, null, null, null, null,
														null, null, null, null, null, null,
														null, null, null,
														array($parametros_vista['campo_orden']),
														$_SESSION['ultimo_sentido'],
														$parametros_vista['rango'],
														$parametros_vista['inicio']);
	
		// Se crea una instancia de la vista
		$vista = new VistaSolicitudExpedienteExterno();
			
		// Se muestra el listado de Solicitudes
		$vista->listar($solicitudes, $this->ng_prestamos, $parametros_vista);
	}

	/**
	 * Si se desean listar las Solicitudes de un expediente determinado
	 * @param string $mensaje
	 * @param string $tipo_mensaje
	 */
	public function listarPorExpediente($mensaje = '', $tipo_mensaje = '')
	{
		// Se comienza a armar una colección de parámetros, los cuales son enviados desde la interfaz de usuario
		$parametros_vista = $this->armarConjuntoParametros('fecha_solicitud_hcd');
	
		// Se verifica la recepción del Año, Tipo y Número
		if ($this->parametrosVerificarParametros(array('anio','tipo','numero')))
		{
			// Se asignan los datos parametrizados en un array auxiliar
			$parametros_vista_aux = $this->parametros;
				
			// Se agregan datos a la colección de parámetros para la Vista
			$parametros_vista = array_merge($parametros_vista, $parametros_vista_aux);

			// Primero se obtiene la cantidad total de préstamos de un expediente determinado
			$parametros_vista['cantidad'] = $this->ng_prestamos->ObtenerSolicitudesExpedientesExternosCantidadResultados(
					$this->parametrosObtenerONull('anio'),
					$this->parametrosObtenerONull('tipo'),
					$this->parametrosObtenerONull('numero'),
					( $this->parametros['cuerpo'] ) ? $this->parametros['cuerpo'] : null,
					( $this->parametros['alcance'] ) ? $this->parametros['alcance'] : null,
					( $this->parametros['digito'] ) ? $this->parametros['digito'] : null,
					( $this->parametros['cuerpoalcance'] ) ? $this->parametros['cuerpoalcance'] : null,
					( $this->parametros['anexoalcance'] ) ? $this->parametros['anexoalcance'] : null,
					( $this->parametros['cuerpoanexoalcance'] ) ? $this->parametros['cuerpoanexoalcance'] : null,
					( $this->parametros['anexo'] ) ? $this->parametros['anexo'] : null,
					( $this->parametros['cuerpoanexo'] ) ? $this->parametros['cuerpoanexo'] : null,
					null, null, null);
	
			// Luego se obtienen las solicitudes de un expediente determinado
			$solicitudes = $this->ng_prestamos->ObtenerSolicitudesExpedientesExternos(
						   $this->parametrosObtenerONull('anio'),
						   $this->parametrosObtenerONull('tipo'),
						   $this->parametrosObtenerONull('numero'),
						   ( $this->parametros['cuerpo'] ) ? $this->parametros['cuerpo'] : null,
						   ( $this->parametros['alcance'] ) ? $this->parametros['alcance'] : null,
						   ( $this->parametros['digito'] ) ? $this->parametros['digito'] : null,
						   ( $this->parametros['cuerpoalcance'] ) ? $this->parametros['cuerpoalcance'] : null,
						   ( $this->parametros['anexoalcance'] ) ? $this->parametros['anexoalcance'] : null,
						   ( $this->parametros['cuerpoanexoalcance'] ) ? $this->parametros['cuerpoanexoalcance'] : null,
						   ( $this->parametros['anexo'] ) ? $this->parametros['anexo'] : null,
						   ( $this->parametros['cuerpoanexo'] ) ? $this->parametros['cuerpoanexo'] : null,
						   null, null, null,
						   array($parametros_vista['campo_orden']),
						   $_SESSION['ultimo_sentido'],
						   $parametros_vista['rango'],
						   $parametros_vista['inicio']);
				
		}
	
		// Número total de páginas
		$parametros_vista['nro_paginas'] = ceil($parametros_vista['cantidad'] / $parametros_vista['rango']);
	
		// Se crea una instancia de la vista
		$vista = new VistaSolicitudExpedienteExterno();
			
		// Se muestra el listado de las Solicitudes
		$vista->listar($solicitudes, $this->ng_prestamos, $parametros_vista);
	}

	/**
	 * Si se desean listar las Solicitudes según un criterio de búsqueda determinado
	 * @param string $mensaje
	 * @param string $tipo_mensaje
	 */
	public function listarPorCriterioBusqueda($mensaje = '', $tipo_mensaje = '')
	{
		// Se comienza a armar una colección de parámetros, los cuales son enviados desde la interfaz de usuario
		$parametros_vista = $this->armarConjuntoParametros('fecha_solicitud_hcd');
	
		// Se asignan los datos parametrizados en un array auxiliar
		$parametros_vista_aux = $this->parametros;
	
		// Se agregan datos a la colección de parámetros para la Vista
		$parametros_vista = array_merge($parametros_vista, $parametros_vista_aux);
	
		$conjuntoEstados = Array();
		// Para estado Solicitado al HCD
		if ( !is_null($parametros_vista['estado_solicitado_hcd']) && $parametros_vista['estado_solicitado_hcd'] != '')
		{
			$conjuntoEstados[]= $parametros_vista['estado_solicitado_hcd'];
		}
		// Para estado Solicitado al Ente Externo
		if ( !is_null($parametros_vista['estado_solicitado_ee']) && $parametros_vista['estado_solicitado_ee'] != '')
		{
			$conjuntoEstados[]= $parametros_vista['estado_solicitado_ee'];
		}
		// Para estado Ingresado del Ente Externo
		if ( !is_null($parametros_vista['estado_ingresado_ee']) && $parametros_vista['estado_ingresado_ee'] != '')
		{
			$conjuntoEstados[]= $parametros_vista['estado_ingresado_ee'];
		}
		// Para estado Devuelto al Ente Externo
		if ( !is_null($parametros_vista['estado_devuelto_ee']) && $parametros_vista['estado_devuelto_ee'] != '')
		{
			$conjuntoEstados[] = $parametros_vista['estado_devuelto_ee'];
		}
		// Para estado Anulado
		if ( !is_null($parametros_vista['estado_anulado_ee']) && $parametros_vista['estado_anulado_ee'] != '')
		{
			$conjuntoEstados[]= $parametros_vista['estado_anulado_ee'];
		}
	
		// Primero se obtiene la cantidad total de Solicitudes que cumplen con dicho criterio
		$parametros_vista['cantidad'] = $this->ng_prestamos->ObtenerSolicitudesExpedientesExternosCantidadResultados(
				null, null, null, null, null, null, null, null, null, null, null,
				( $parametros_vista['fecha_desde'] ) ? $this->convertirFechaToMySQL($parametros_vista['fecha_desde']) : null,
				( $parametros_vista['fecha_hasta'] ) ? $this->convertirFechaToMySQL($parametros_vista['fecha_hasta'], true) : null,
				( count($conjuntoEstados) > 0 ) ? $conjuntoEstados : null );
	
		// Luego se obtienen las Solicitudes según el criterio de búsqueda utilizado
		$solicitudes = $this->ng_prestamos->ObtenerSolicitudesExpedientesExternos(
				null, null, null, null, null, null, null, null, null, null, null,
				( $parametros_vista['fecha_desde'] ) ? $this->convertirFechaToMySQL($parametros_vista['fecha_desde']) : null,
				( $parametros_vista['fecha_hasta'] ) ? $this->convertirFechaToMySQL($parametros_vista['fecha_hasta'], true) : null,
				( count($conjuntoEstados) > 0 ) ? $conjuntoEstados : null,
				array($parametros_vista['campo_orden']),
				$_SESSION['ultimo_sentido'],
				$parametros_vista['rango'],
				$parametros_vista['inicio']);
	
		// Número total de páginas
		$parametros_vista['nro_paginas'] = ceil($parametros_vista['cantidad'] / $parametros_vista['rango']);
	
		// Se crea una instancia de la vista
		$vista = new VistaSolicitudExpedienteExterno();
			
		// Se muestra el listado de las Solicitudes obtenidos según el criterio de búsqueda
		$vista->listar($solicitudes, $this->ng_prestamos, $parametros_vista);
	}

	/**
	 * Este método nos envía al formulario de edición de cambio de estado
	 */
	public function cambiarEstado()
	{
		// Verifico que existan todos los parametros obligatorios
		if ($this->parametrosVerificarParametros(array('data','estado_nuevo','parametros_serializados')))
		{
			// Deserializo el parámetro (me invocaron desde la vista).
			$solicitud_ee = new SolicitudExpedienteExterno();
			$solicitud_ee->Deserializar($this->parametrosObtener('data'));
	
			// Parámetros para la vista (estado_nuevo)
			$parametros_vista = $this->parametrosObtenerColeccion(array('estado_nuevo'));
				
			$parametros_vista['parametros_serializados'] = $this->parametrosObtener('parametros_serializados');
				
			//Se crea una instancia de la "vista"
			$vista = new VistaSolicitudExpedienteExterno();
	
			//se muestra el formulario de edicion de cambio de estado
			$vista->editarCambioEstado($solicitud_ee, $parametros_vista);
		}
		else
		{
			$parametros_vista['mensaje'] = "ERROR: en cambiarEstado, falta el par&aacute;metro necesario";
			$parametros_vista['tipo_mensaje'] = 2;
				
			// Se vuelve al listado respectivo
			$this->mostrarListadoGeneralSegunCriterioUtilizado($parametros_vista);
		}
	}

	/**
	 * Este método se ejecuta desde el formulario de edición de Cambio de Estado
	 */
	public function guardarCambioEstado()
	{
		// Verifico que existan todos los parametros MINIMOS obligatorios
		if ($this->parametrosVerificarParametros(array('anio','tipo','numero','cuerpo','alcance','digito',
				'cuerpoalcance','anexoalcance','cuerpoanexoalcance','anexo','cuerpoanexo','fecha_solicitud_hcd',
				'estado_nuevo','fecha_estado','observaciones','parametros_serializados')))
		{
			// Parametros del cambio de estado
			$nuevo_estado = $this->parametrosObtener('estado_nuevo');
			$fecha_estado = $this->parametrosObtener('fecha_estado');
			
			// Recargo la Solicitud (solamente le asigno los campos clave, el resto se recarga solo).
			$solicitud_ee = new SolicitudExpedienteExterno();
			$solicitud_ee->anio = $this->parametrosObtener('anio');
			$solicitud_ee->tipo = $this->parametrosObtener('tipo');
			$solicitud_ee->numero = $this->parametrosObtener('numero');
			$solicitud_ee->cuerpo = $this->parametrosObtener('cuerpo');
			$solicitud_ee->alcance = $this->parametrosObtener('alcance');
			$solicitud_ee->digito = $this->parametrosObtener('digito');
			$solicitud_ee->cuerpoalcance = $this->parametrosObtener('cuerpoalcance');
			$solicitud_ee->anexoalcance = $this->parametrosObtener('anexoalcance');
			$solicitud_ee->cuerpoanexoalcance = $this->parametrosObtener('cuerpoanexoalcance');
			$solicitud_ee->anexo = $this->parametrosObtener('anexo');
			$solicitud_ee->cuerpoanexo = $this->parametrosObtener('cuerpoanexo');
			$solicitud_ee->fecha_solicitud_hcd = $this->parametrosObtener('fecha_solicitud_hcd');
	
			// recargo el resto de los atributos
			$solicitud_ee = $this->ng_prestamos->RecargarSolicitudExpedienteExterno($solicitud_ee);
				
			// También le cambio las observaciones
			$solicitud_ee->observaciones = $this->parametrosObtener('observaciones');
			
			// Trato de hacer el cambio de estado y guardar
			try
			{
				$solicitud_ee = $this->ng_prestamos->CambiarEstadoExpedienteExterno($solicitud_ee, $nuevo_estado, $fecha_estado);
					
				$this->ng_prestamos->GuardarSolicitudExpedienteExterno($solicitud_ee);
					
				$mensaje = "Solicitud $solicitud_ee->anio-$solicitud_ee->tipo-$solicitud_ee->numero actualizada con &eacute;xito.";
				$tipo_mensaje = 1;
			}
			catch (Exception $e)
			{
				$mensaje = "ERROR: ".$e->getMessage();
				$tipo_mensaje = 2;
			}
		}
		else
		{
			$mensaje = "ERROR: en guardarCambioEstado, falta el par&aacute;metro necesario";
			$tipo_mensaje = 2;
		}

		// Se asigna el sentido descendente, para que al volver se liste de forma Ascendente
		$_SESSION['ultimo_sentido'] = ORDEN_DESCENDENTE;
		
		// Se obtienen los parámetros serializados en la Vista
		$parametros_serializados = $this->parametrosObtener('parametros_serializados');
	
		// Se deserializan los parametros para seguir utilizando en la Vista
		$parametros_vista = deserializarColeccion($parametros_serializados);
	
		$parametros_vista['mensaje'] = $mensaje;
		$parametros_vista['tipo_mensaje'] = $tipo_mensaje;
		
		// Se asignan los parámetros de la vista en el controlador de préstamos
		$this->parametrosAsignarColeccion($parametros_vista);
	
		// Se vuelve al listado respectivo
		$this->mostrarListadoGeneralSegunCriterioUtilizado($parametros_vista);
	}

	/**
	 * Se muestra el listado general, según el criterio utilizado
	 * antes de guardar el cambio de estado,
	 * de haberse utilizado uno, sino se muestran todos las Solicitudes
	 *
	 * @param array $parametros_vista, criterio utilizado
	 */
	public function mostrarListadoGeneralSegunCriterioUtilizado($parametros_vista)
	{
		// Si se filtró por expediente
		if ( $parametros_vista['anio'] != '' )
		{
			$this->listarPorExpediente($parametros_vista['mensaje'], $parametros_vista['tipo_mensaje']);
		}
		// Si se filtró por estado y/o rango de fechas
		else if( $parametros_vista['estado_solicitado_hcd'] != '' ||
				 $parametros_vista['estado_solicitado_ee'] != '' ||
				 $parametros_vista['estado_ingresado_ee'] != '' ||
				 $parametros_vista['estado_devuelto_ee'] != '' ||
				 $parametros_vista['estado_anulado_ee'] != '' ||
				 $parametros_vista['fecha_hasta'] != '' )
		{
			$this->listarPorCriterioBusqueda($parametros_vista['mensaje'], $parametros_vista['tipo_mensaje']);
		}
		else // sino, se listan todos
		{
			// Se muestran todos las Solicitudes
			$this->listar($parametros_vista['mensaje'], $parametros_vista['tipo_mensaje']);
		}
	}
	
	/**
	 * Se genera el reporte en formato PDF para guardarlo y/o imprimirlo
	 */
	public function generarReporte()
	{
		// Se comienza a armar una colección de parámetros, los cuales son enviados desde la interfaz de usuario
		$parametros_vista = $this->armarConjuntoParametrosSolicitudes('fecha_solicitud_hcd');
	
		// Se asignan los datos parametrizados en un array auxiliar
		$parametros_vista_aux = $this->parametros;
	
		// Se agregan datos a la colección de parámetros para la Vista
		$parametros_vista = array_merge($parametros_vista, $parametros_vista_aux);
	
		$conjuntoEstados = Array();
		// Para estado Solicitado al HCD
		if ( !is_null($parametros_vista['estado_solicitado_hcd']) && $parametros_vista['estado_solicitado_hcd'] != '')
		{
			$conjuntoEstados[]= $parametros_vista['estado_solicitado_hcd'];
		}
		// Para estado Solicitado al Ente Externo
		if ( !is_null($parametros_vista['estado_solicitado_ee']) && $parametros_vista['estado_solicitado_ee'] != '')
		{
			$conjuntoEstados[]= $parametros_vista['estado_solicitado_ee'];
		}
		// Para estado Ingresado del Ente Externo
		if ( !is_null($parametros_vista['estado_ingresado_ee']) && $parametros_vista['estado_ingresado_ee'] != '')
		{
			$conjuntoEstados[]= $parametros_vista['estado_ingresado_ee'];
		}
		// Para estado Devuelto al Ente Externo
		if ( !is_null($parametros_vista['estado_devuelto_ee']) && $parametros_vista['estado_devuelto_ee'] != '')
		{
			$conjuntoEstados[] = $parametros_vista['estado_devuelto_ee'];
		}
		// Para estado Anulado
		if ( !is_null($parametros_vista['estado_anulado_ee']) && $parametros_vista['estado_anulado_ee'] != '')
		{
			$conjuntoEstados[]= $parametros_vista['estado_anulado_ee'];
		}
			
		// Luego se obtienen las solicitudes
		$solicitudes = $this->ng_prestamos->ObtenerSolicitudesExpedientesExternos(
				$this->parametrosObtenerONull('anio'),
				$this->parametrosObtenerONull('tipo'),
				$this->parametrosObtenerONull('numero'),
				( $this->parametros['cuerpo'] ) ? $this->parametros['cuerpo'] : null,
				( $this->parametros['alcance'] ) ? $this->parametros['alcance'] : null,
				( $this->parametros['digito'] ) ? $this->parametros['digito'] : null,
				( $this->parametros['cuerpoalcance'] ) ? $this->parametros['cuerpoalcance'] : null,
				( $this->parametros['anexoalcance'] ) ? $this->parametros['anexoalcance'] : null,
				( $this->parametros['cuerpoanexoalcance'] ) ? $this->parametros['cuerpoanexoalcance'] : null,
				( $this->parametros['anexo'] ) ? $this->parametros['anexo'] : null,
				( $this->parametros['cuerpoanexo'] ) ? $this->parametros['cuerpoanexo'] : null,
				( $parametros_vista['fecha_desde'] ) ? $this->convertirFechaToMySQL($parametros_vista['fecha_desde']) : null,
				( $parametros_vista['fecha_hasta'] ) ? $this->convertirFechaToMySQL($parametros_vista['fecha_hasta'], true) : null,
				( count($conjuntoEstados) > 0 ) ? $conjuntoEstados : null,
				array($parametros_vista['campo_orden']),
				$_SESSION['ultimo_sentido'],
				null,
				null);

		// Se crea una instancia de la vista
		$vista = new VistaSolicitudExpedienteExterno();
			
		// Se muestra el listado de las Solicitudes
		$vista->generarReporte($solicitudes, $parametros_vista);
	}

	/**
	 * Se elimina una Solicitud (de forma lógica)
	 */
	public function eliminar()
	{
		// Verifico que existan todos los parametros obligatorios
		if ( $this->parametrosVerificarParametros(array('data')) )
		{
			// Deserializo el parametro (me invocaron desde la vista).
			$solicitud_ee = new SolicitudExpedienteExterno();
			$solicitud_ee->Deserializar($this->parametrosObtener('data'));
				
			try
			{
				// Se elimina la Solicitud de forma lógica
				$this->ng_prestamos->EliminarSolicitud($solicitud_ee);
	
				$mensaje = "La Solicitud $solicitud_ee->anio-$solicitud_ee->tipo-$solicitud_ee->numero se elimin&oacute; con &eacute;xito.";
				$tipo_mensaje = 1;
			}
			catch (Exception $e)
			{
				$mensaje = $e->getMessage();
				$tipo_mensaje = 2;
			}
		}
		else
		{
			$mensaje = "ERROR: en eliminar, falta el par&aacute;metro necesario";
			$tipo_mensaje = 2;
		}

		// Se asigna el sentido descendente, para que al volver se liste de forma Ascendente
		$_SESSION['ultimo_sentido'] = ORDEN_DESCENDENTE;
		
		// Se obtienen los parámetros serializados en la Vista
		$parametros_serializados = $this->parametrosObtener('parametros_serializados');
		
		// Se deserializan los parametros para seguir utilizando en la Vista
		$parametros_vista = deserializarColeccion($parametros_serializados);
		
		$parametros_vista['mensaje'] = $mensaje;
		$parametros_vista['tipo_mensaje'] = $tipo_mensaje;
		
		// Se asignan los parámetros de la vista en el controlador de préstamos
		$this->parametrosAsignarColeccion($parametros_vista);
		
		// Se vuelve al listado respectivo
		$this->mostrarListadoGeneralSegunCriterioUtilizado($parametros_vista);
	}
	
	/**
	 * Se muestra el formulario para editar la observación de una Solicitud
	 */
	public function editarObservaciones()
	{
		// Verifico que existan todos los parámetros obligatorios
		if ( $this->parametrosVerificarParametros(array('data', 'parametros_serializados')) )
		{
			// Deserializo el parámetro (me invocaron desde la vista).
			$solicitud_ee = new SolicitudExpedienteExterno();
			$solicitud_ee->Deserializar($this->parametrosObtener('data'));
	
			$parametros_vista['parametros_serializados'] = $this->parametrosObtener('parametros_serializados');
	
			//Se crea una instancia de VistaSolicitudExpedienteExterno
			$vista = new VistaSolicitudExpedienteExterno();
	
			//se muestra el formulario de edicion de cambio de estado
			$vista->editarObservaciones($solicitud_ee, $parametros_vista);
		}
		else
		{
			$parametros_vista['mensaje'] = "ERROR: en editarObservaciones, falta el par&aacute;metro necesario";
			$parametros_vista['tipo_mensaje'] = 2;
	
			// Se vuelve al listado respectivo
			$this->mostrarListadoGeneralSegunCriterioUtilizado($parametros_vista);
		}
	}

	/**
	 * Se guarda la modificación de la observación de la Solicitud
	 */
	public function guardarObservaciones()
	{
		// Se verifica que existan todos los parametros MINIMOS obligatorios
		if ($this->parametrosVerificarParametros(array('anio','tipo','numero','cuerpo','alcance','digito',
				'cuerpoalcance','anexoalcance','cuerpoanexoalcance','anexo','cuerpoanexo','fecha_solicitud_hcd',
				'estado','observaciones','parametros_serializados')))
		{
			// Se crea una instancia de Prestamo
			$solicitud_ee = new SolicitudExpedienteExterno();
				
			$solicitud_ee->estado = $this->parametrosObtener('estado');
				
			$solicitud_ee->anio = $this->parametrosObtener('anio');
			$solicitud_ee->tipo = $this->parametrosObtener('tipo');
			$solicitud_ee->numero = $this->parametrosObtener('numero');
			$solicitud_ee->cuerpo = $this->parametrosObtener('cuerpo');
			$solicitud_ee->alcance = $this->parametrosObtener('alcance');
			$solicitud_ee->digito = $this->parametrosObtener('digito');
			$solicitud_ee->cuerpoalcance = $this->parametrosObtener('cuerpoalcance');
			$solicitud_ee->anexoalcance = $this->parametrosObtener('anexoalcance');
			$solicitud_ee->cuerpoanexoalcance = $this->parametrosObtener('cuerpoanexoalcance');
			$solicitud_ee->anexo = $this->parametrosObtener('anexo');
			$solicitud_ee->cuerpoanexo = $this->parametrosObtener('cuerpoanexo');
			$solicitud_ee->fecha_solicitud_hcd = $this->parametrosObtener('fecha_solicitud_hcd');
	
			// recargo el resto de los atributos
			$solicitud_ee = $this->ng_prestamos->RecargarSolicitudExpedienteExterno($solicitud_ee);
				
			// También le cambio la observación
			$solicitud_ee->observaciones = $this->parametrosObtener('observaciones');
			
			// Se guardan las modificaciones en el Préstamo
			try
			{
				$this->ng_prestamos->GuardarSolicitudExpedienteExterno($solicitud_ee);
					
				$mensaje = "Solicitud $solicitud_ee->anio-$solicitud_ee->tipo-$solicitud_ee->numero modificada con &eacute;xito.";
				$tipo_mensaje = 1;
			}
			catch (Exception $e)
			{
				$mensaje = "ERROR: ".$e->getMessage();
				$tipo_mensaje = 2;
			}
		}
		else
		{
			$mensaje = "ERROR: en guardarObservaciones, falta el par&aacute;metro necesario";
			$tipo_mensaje = 2;
		}
	
		// Se asigna el sentido descendente, para que al volver se liste de forma Ascendente
		$_SESSION['ultimo_sentido'] = ORDEN_DESCENDENTE;
	
		// Se obtienen los parámetros serializados en la Vista
		$parametros_serializados = $this->parametrosObtener('parametros_serializados');
	
		// Se deserializan los parametros para seguir utilizando en la Vista
		$parametros_vista = deserializarColeccion($parametros_serializados);
	
		$parametros_vista['mensaje'] = $mensaje;
		$parametros_vista['tipo_mensaje'] = $tipo_mensaje;
	
		// Se asignan los parámetros de la vista en el controlador de préstamos
		$this->parametrosAsignarColeccion($parametros_vista);
	
		// Se vuelve al listado respectivo
		$this->mostrarListadoGeneralSegunCriterioUtilizado($parametros_vista);
	}
}
?>
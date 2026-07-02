<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

// Configuracion de rutas
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/config/path_config.php');

// Layer de negocio
require_once(PATH_SGL_LAYER_NEGOCIO_PRESTAMOS.'ng_prestamos.php');

//Incluye el modelo que corresponde
require 'modelos/prestamos.php';

//Incluye la vista que corresponde
require 'vistas/prestamos.php';

class prestamos_controller extends ControllerBase
{
	// contendrá una instancia de la capa de negocio del circuito de prestamos
	public $ng_prestamos;
	public $rango_paginador;
	
	public function __construct()
	{
		// almacena una instancia de la capa de negocio del circuito de prestamos
		$this->ng_prestamos = new ng_prestamos();
		// por defecto la página muestra 11 registros
		$this->rango_paginador = 11;
	}
	
	/**
	 * Guarda una instancia de Prestamo en formato Json en la Sesión
	 * @param Prestamo $prestamo
	 */
	public function guardarRegistroOriginal(Prestamo $prestamo)
	{
		$_SESSION['prestamo_original'] = $prestamo->ToJson();
	}
	
	/**
	 * Obtiene una cadena en JSON e intenta regenerar el estado de la instancia a partir del elemento serializado.
	 * @return Prestamo|NULL
	 */
	public function obtenerRegistroOriginal()
	{
		// Si existe el Préstamo Original en la Sesión, y no está vacío
		if (array_key_exists('prestamo_original', $_SESSION) && (!empty($_SESSION['prestamo_original'])))
		{
			// Se instancia un nuevo préstamo
			$resultado = new Prestamo();
			
			// Obtiene una cadena en JSON e intenta regenerar el estado de la instancia a partir del elemento serializado.
			$resultado->FromJson($_SESSION['prestamo_original']);
			
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
	public function armarConjuntoParametrosPrestamos($campo_orden_por_defecto)
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
			$_SESSION['ultimo_sentido'] = ($_SESSION['ultimo_sentido'] == ORDEN_ASCENDENTE) ? ORDEN_DESCENDENTE : ORDEN_ASCENDENTE;
		}
		
		// cantidad de registros a mostrar
		$parametros_vista['rango'] = $this->rango_paginador;
		
		// Primero se obtiene la cantidad total de préstamos
		$parametros_vista['cantidad'] = $this->ng_prestamos->ObtenerPrestamosCantidadResultados(
				null, null, null, null, null,
				null, null, null, null, null, null,
				null, null, null, null, null);
		
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
	 * Si se desean listar TODOS los Préstamos
	 * @param string $mensaje
	 * @param string $tipo_mensaje
	 */
	public function mostrarGrillaGeneral($mensaje = '', $tipo_mensaje = '')
	{
		// Se comienza a armar una colección de parámetros, los cuales son enviados desde la interfaz de usuario
		$parametros_vista = $this->armarConjuntoParametrosPrestamos('fecha_solicitud');
		
		// Si se recibe un mensaje y su tipo internamente en el controlador 
		if ($mensaje != '' && $tipo_mensaje != '')
		{
			$parametros_vista['mensaje'] = $mensaje;
			$parametros_vista['tipo_mensaje'] = $tipo_mensaje;
		}
		
		// Luego se obtienen los préstamos
		$prestamos = $this->ng_prestamos->ObtenerPrestamos( null, null, null, null, null,
				null, null, null, null, null, null,
				null, null, null, null, null,
				array($parametros_vista['campo_orden']),
				$_SESSION['ultimo_sentido'],
				$parametros_vista['rango'],
				$parametros_vista['inicio']);
		
		// Se obtienen los posibles solicitantes
		$modelo = new prestamosModel();
		$solicitantes = $modelo->obtenerSolicitantes();
		
		// Se crea una instancia de la vista
		$vista = new VistaPrestamos();
			
		// Se muestra el listado de todos los préstamos (del HCD y de Entes Externos)
		$vista->mostrarGrillaGeneral($prestamos, $this->ng_prestamos, $solicitantes, $parametros_vista);
	}
	
	/**
	 * Si se desean listar los Préstamos de un expediente determinado
	 * @param string $mensaje
	 * @param string $tipo_mensaje
	 */
	public function listarPrestamosPorExpediente($mensaje = '', $tipo_mensaje = '')
	{
		// Se comienza a armar una colección de parámetros, los cuales son enviados desde la interfaz de usuario
		$parametros_vista = $this->armarConjuntoParametros('fecha_solicitud');
		
		// Se verifica la recepción del Año, Tipo y Número
		if ($this->parametrosVerificarParametros(array('anio','tipo','numero')))
		{
			// Se asignan los datos parametrizados en un array auxiliar
			$parametros_vista_aux = $this->parametros;
			
			// Se agregan datos a la colección de parámetros para la Vista
			$parametros_vista = array_merge($parametros_vista, $parametros_vista_aux);
			
			// Primero se obtiene la cantidad total de préstamos de un expediente determinado
			$parametros_vista['cantidad'] = $this->ng_prestamos->ObtenerPrestamosCantidadResultados(
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
														null, null, null, null, null);

			// Luego se obtienen los préstamos de un expediente determinado
			$prestamos = $this->ng_prestamos->ObtenerPrestamos(
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
														null, null, null, null, null,
														array($parametros_vista['campo_orden']),
														$_SESSION['ultimo_sentido'],
														$parametros_vista['rango'],
														$parametros_vista['inicio']);
			
		}
	
		// Número total de páginas
		$parametros_vista['nro_paginas'] = ceil($parametros_vista['cantidad'] / $parametros_vista['rango']);
	
		// Se obtienen los posibles solicitantes
		$modelo = new prestamosModel();
		$solicitantes = $modelo->obtenerSolicitantes();
		
		// Se crea una instancia de la vista
		$vista = new VistaPrestamos();
			
		// Se muestra el listado de todos los préstamos (del HCD y de Entes Externos)
		$vista->mostrarGrillaGeneral($prestamos, $this->ng_prestamos, $solicitantes, $parametros_vista);
	}
	
	/**
	 * Si se desean listar los Préstamos según un criterio de búsqueda determinado
	 * @param string $mensaje
	 * @param string $tipo_mensaje
	 */
	public function listarPrestamosPorCriterioBusqueda($mensaje = '', $tipo_mensaje = '')
	{
		// Se comienza a armar una colección de parámetros, los cuales son enviados desde la interfaz de usuario
		$parametros_vista = $this->armarConjuntoParametros('fecha_solicitud');
		
		// Se asignan los datos parametrizados en un array auxiliar
		$parametros_vista_aux = $this->parametros;

		// Se agregan datos a la colección de parámetros para la Vista
		$parametros_vista = array_merge($parametros_vista, $parametros_vista_aux);
				
		$solicitante = $this->parametrosObtenerONull('solicitante');
		// Si se recibió un Solicitante
		if ( !is_null($solicitante) && $solicitante != '0' )
		{
			// Se separa por el guión medio
			$partes_solicitante = explode('-', $solicitante);
		}
		// Se asignan el Tipo y el Código del solicitante
		$parametros_vista['solicitante_tipo'] = ($partes_solicitante[0]) ? $partes_solicitante[0] : null;
		$parametros_vista['solicitante_codigo'] = ($partes_solicitante[1]) ? $partes_solicitante[1] : null;
		
		$conjuntoEstados = Array();
		// Para estado Solicitado
		if ( !is_null($parametros_vista['estado_solicitado']) && $parametros_vista['estado_solicitado'] != '')
		{
			$conjuntoEstados[]= $parametros_vista['estado_solicitado'];
		}
		// Para estado Prestado
		if ( !is_null($parametros_vista['estado_prestado']) && $parametros_vista['estado_prestado'] != '')
		{
			$conjuntoEstados[]= $parametros_vista['estado_prestado'];
		}
		// Para estado Devuelto
		if ( !is_null($parametros_vista['estado_devuelto']) && $parametros_vista['estado_devuelto'] != '')
		{
			$conjuntoEstados[] = $parametros_vista['estado_devuelto'];
		}
		// Para estado Anulado
		if ( !is_null($parametros_vista['estado_anulado']) && $parametros_vista['estado_anulado'] != '')
		{
			$conjuntoEstados[]= $parametros_vista['estado_anulado'];
		}
		
		// Primero se obtiene la cantidad total de préstamos que cumplen con dicho criterio
		$parametros_vista['cantidad'] = $this->ng_prestamos->ObtenerPrestamosCantidadResultados(
						null, null, null, null, null, null, null, null, null, null, null,
						( $parametros_vista['fecha_desde'] ) ? $this->convertirFechaToMySQL($parametros_vista['fecha_desde']) : null,
						( $parametros_vista['fecha_hasta'] ) ? $this->convertirFechaToMySQL($parametros_vista['fecha_hasta'], true) : null,
						$parametros_vista['solicitante_tipo'],
						$parametros_vista['solicitante_codigo'],
						( count($conjuntoEstados) > 0 ) ? $conjuntoEstados : null );
	
		// Luego se obtienen los préstamos según el criterio de búsqueda utilizado
		$prestamos = $this->ng_prestamos->ObtenerPrestamos(
						null, null, null, null, null, null, null, null, null, null, null,
						( $parametros_vista['fecha_desde'] ) ? $this->convertirFechaToMySQL($parametros_vista['fecha_desde']) : null,
						( $parametros_vista['fecha_hasta'] ) ? $this->convertirFechaToMySQL($parametros_vista['fecha_hasta'], true) : null,
						$parametros_vista['solicitante_tipo'],
						$parametros_vista['solicitante_codigo'],
						( count($conjuntoEstados) > 0 ) ? $conjuntoEstados : null,
						array($parametros_vista['campo_orden']),
						$_SESSION['ultimo_sentido'],
						$parametros_vista['rango'],
						$parametros_vista['inicio']);

		// Número total de páginas
		$parametros_vista['nro_paginas'] = ceil($parametros_vista['cantidad'] / $parametros_vista['rango']);
		
		// Se obtienen los posibles solicitantes
		$modelo = new prestamosModel();
		$solicitantes = $modelo->obtenerSolicitantes();
		
		// Se crea una instancia de la vista
		$vista = new VistaPrestamos();
			
		// Se muestra el listado de los préstamos obtenidos según el criterio de búsqueda
		$vista->mostrarGrillaGeneral($prestamos, $this->ng_prestamos, $solicitantes, $parametros_vista);
	}
	
	/**
	 * Se listan los préstamos de un expediente determinado
	 * @param string $mensaje
	 * @param string $tipo_mensaje
	 */
	public function listarEnSolapa($mensaje = '', $tipo_mensaje = '')
	{			
		// Si se reciben los datos correspondientes a la clave del expediente del HCD
		if ($this->parametrosVerificarParametros(array('anio','tipo','numero','cuerpo','alcance')))
		{
			$parametros_vista = $this->parametrosObtenerColeccion(array('anio','tipo','numero','cuerpo','alcance'));
			
			// DIRECCIÓN PARA LA PAGINACIÓN (PRIMERO, ANTERIOR, SIGUIENTE, ÚLTIMO)
			$parametros_vista['sentido'] = 'anterior';

			// Si se recibe un mensaje y su tipo internamente en el controlador
			if ($mensaje != '' && $tipo_mensaje != '')
			{
				$parametros_vista['mensaje'] = $mensaje;
				$parametros_vista['tipo_mensaje'] = $tipo_mensaje;
			}
			else
			{
				$mensaje = $this->parametrosObtenerONull('mensaje');
				$tipo_mensaje = $this->parametrosObtenerONull('tipo_mensaje');
			}
			
			//se establece el campo por el cual ordenar
			$campo_orden = $this->parametrosObtenerONull('campo_orden');
			if ( !empty($campo_orden) )
			{
				$parametros_vista['campo_orden'] = $campo_orden;
			}
			else
			{
				//por defecto
				$parametros_vista['campo_orden'] = 'fecha_solicitud';
				$_SESSION['ultimo_campo'] = '';
			}
			
			// Si es la primera vez que carga la pagina o se esta cambiando el campo por el que se ordena
			if ( !isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $parametros_vista['campo_orden'] ) 
			{
				$_SESSION['ultimo_campo'] = $parametros_vista['campo_orden'];
				$_SESSION['ultimo_sentido'] = ORDEN_ASCENDENTE;
			} 
			else 
			{
				// Si se hizo clic en el mismo que ya estaba ordenado antes
				// Solo hay que cambiar el sentido:
				if ($_SESSION['ultimo_sentido'] == ORDEN_ASCENDENTE)
				{
					$_SESSION['ultimo_sentido'] = ORDEN_DESCENDENTE;
				}
				else
				{
					$_SESSION['ultimo_sentido'] = ORDEN_ASCENDENTE;
				}
			}
			
			$parametros_vista['rango'] = $this->rango_paginador;//cantidad de registros a mostrar
			
			$parametros_vista['pagina'] = $this->parametrosObtenerONull('pagina');	//se obtiene el valor de la pagina
			
			if ( !$parametros_vista['pagina'] )
			{	//si no se sabe el valor de la pagina
				$parametros_vista['inicio'] = 0;	//se inicia en el primer registro
				$parametros_vista['pagina'] = 1;	//con la primer pagina 
			}
			else
			{	//si se conoce se calcula el valor del registro inicial de dicha pagina 
				$parametros_vista['inicio'] = ($parametros_vista['pagina'] * $parametros_vista['rango']) - $parametros_vista['rango'];
			} 
			$parametros_vista['pagina_ant'] = $parametros_vista['pagina'] - 1;		//para la pagina anterior
			$parametros_vista['pagina_sgte'] = $parametros_vista['pagina'] + 1;		//para la pagina posterior
	
			// Filtramos para todos los prestamos del expediente seleccionado, utilizando los filtros definidos
			$prestamos = $this->ng_prestamos->ObtenerPrestamos(
												$this->parametrosObtener('anio'), 
												$this->parametrosObtener('tipo'), 
												$this->parametrosObtener('numero'), 
												$this->parametrosObtener('cuerpo'), 
												$this->parametrosObtener('alcance'),
												null, null, null, null, null, null, null, null, null, null, null, 
												array($parametros_vista['campo_orden']), ORDEN_ASCENDENTE);
			
			//Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
			$parametros_vista['cantidad'] = count($prestamos); 
			
			//NUMERO TOTAL DE PAGINAS 
			$parametros_vista['nro_paginas'] = ceil($parametros_vista['cantidad'] / $parametros_vista['rango']);
			
			//Se crea una instancia de la vista
			$vista = new VistaPrestamos();
			
			// Se muestra el listado
			$vista->listarEnSolapa($prestamos, $this->ng_prestamos, $mensaje, $tipo_mensaje, $parametros_vista);
		}
	}

	/**
	 * Se muestra el formulario de edición para un NUEVO préstamo
	 * en caso de venir desde la solapa de préstamos de un expediente determinado
	 * se reciben los datos de dicho expediente
	 */
	public function agregar($mensaje = '', $tipo_mensaje = '')
	{
		// Se arma un paquete de parámetros para la vista
		$parametros_vista = array();
		
		// Para saber de qué listado se viene
		$parametros_vista['grilla_origen'] = $this->parametrosObtener('grilla_origen');
		
		// Se ingresa el préstamo con estado Solicitado
		$parametros_vista['estado_a_editar'] = Prestamo::E_SOLICITADO;
		
		// Si se viene del listado de la solapa de préstamos de un expediente determinado
		if ( $parametros_vista['grilla_origen'] == "grilla_solapa" )
		{
			// Se verifica que se reciban los datos de la clave del expediente
			if ( $this->parametrosVerificarParametros(array('data')) )
			{
				// Se crea una instancia de Préstamo
				$prestamo = new Prestamo();
				// Se deserializan los datos para cargar la instancia de Prestamo
				$prestamo->Deserializar($this->parametrosObtener('data'));
			
				$parametros_vista['anio'] = $prestamo->anio;
				$parametros_vista['tipo'] = $prestamo->tipo;
				$parametros_vista['numero'] = $prestamo->numero;
				$parametros_vista['cuerpo'] = $prestamo->cuerpo;
				$parametros_vista['alcance'] = $prestamo->alcance;
			}
			else
			{
				$mensaje = "ERROR: falta el par&aacute;metro data";
				$tipo_mensaje = 2;
	
				// Vuelve al listado/grilla original
				$this->volverSegunGrillaOrigen($parametros_vista['grilla_origen'], $mensaje, $tipo_mensaje);
			}
		}
		else
		{
			// si se viene de la grilla general, no tenemos aún datos de un expediente, se ingresan en la edición
			$prestamo = null;
		}
		
		// Se crea una instancia del modelo
		$modelo = new prestamosModel();
		// Se obtienen los posibles solicitantes
		$solicitantes = $modelo->obtenerSolicitantes();

		//Se crea una instancia de la vista de préstamos
		$vista = new VistaPrestamos();
		// Se muestra el formulario de Edición
		$vista->editar($prestamo, $parametros_vista, $solicitantes, $mensaje, $tipo_mensaje);
	}
	
	/**
	 * Se genera la solicitud en el Ente Externo
	 */
	public function generarSolicitudEE()
	{
		// Verifico que existan todos los párametros obligatorios
		if ($this->parametrosVerificarParametros(array('data','pagina')))
		{
			// Deserializo el parametro (me invocaron desde la vista).
			$prestamo = new Prestamo();
			$prestamo->Deserializar($this->parametrosObtener('data'));
		
			// Parámetros para la vista (valor de la página)
			$parametros_vista = $this->parametrosObtener('pagina');
		
			// Recargo el préstamo, por las dudas
			$prestamo = $this->ng_prestamos->RecargarPrestamo($prestamo);
			
			// Se vuelve a verificar si requiere la generación de una solicitud
			if ( $this->ng_prestamos->RequiereSolicitudExpedienteExterno($prestamo) )
			{
				try
				{
					// Se obtiene un objeto de Solicitud de Expediente Externo
					$solicitud = $this->ng_prestamos->ObtenerInstanciaSolicitudExpedienteExterno($prestamo);
					
					// Se guardan los datos de la Solicitud del expediente externo
					$solicitud = $this->ng_prestamos->GuardarSolicitudExpedienteExterno($solicitud);
					
					$mensaje = "Se ha generado la solicitud satisfactoriamente.";
					$tipo_mensaje = 1;
				}
				catch (Exception $e)
				{
					$mensaje = "Error al generar la solicitud, ".$e->getMessage();
					$tipo_mensaje = 2;
				}
			}
			else
			{
				$mensaje = "Ya no es necesario generar la solicitud.";
				$tipo_mensaje = 1;
			}
			
			// Se vuelve a mostrar la grilla general
			$this->mostrarGrillaGeneral($mensaje, $tipo_mensaje);
		}
	}
	
	/**
	 * Se recibe la clave de un expediente perteneciente a un Ente Externo
	 * para la edición de su Préstamo
	 */
	public function editar($mensaje = '', $tipo_mensaje = '')
	{
		// Verifico que existan todos los párametros obligatorios
		if ($this->parametrosVerificarParametros(array('data','pagina')))
		{
			// Parámetros para la vista (valor de la página)
			$parametros_vista = $this->parametrosObtener('pagina');
				
			// Deserializo el parametro (me invocaron desde la vista).
			$prestamo = new Prestamo();
			$prestamo->Deserializar($this->parametrosObtener('data'));
	
			// Recargo el préstamo, por las dudas
			$prestamo = $this->ng_prestamos->RecargarPrestamo($prestamo);
	
			// Se crea una instancia de prestamosModel
			$modelo = new prestamosModel();
			// Se obtienen los posibles Solicitantes
			$solicitantes = $modelo->obtenerSolicitantes();
				
			// Se crea una instancia de VistaPrestamos
			$vista = new VistaPrestamos();
				
			// Verifico, por las dudas que exista el préstamo que se quiere editar
			if ( !is_null($prestamo) )
			{
				// Se guarda en Sesión el objeto Préstamo, previo a su edición, para verificar luego con la D.B.
				$this->guardarRegistroOriginal($prestamo);
	
				// Se muestra el formulario de edición
				$vista->editar($prestamo, $parametros_vista, $solicitantes, $mensaje, $tipo_mensaje);
			}
		}
		else
		{
			// TODO: error... falta un parámetro importante
			echo $mensaje."\n ERROR: en editar, falta el parámetro necesario";
		}
	}
	
	/**
	 * Verifica si dos instancias de Prestamo son iguales o no
	 * @param Prestamo $prestamoA
	 * @param Prestamo $prestamoB
	 * @return boolean
	 */
	public function SonPrestamosIguales(Prestamo $prestamoA, Prestamo $prestamoB)
	{
		// OJO!!! $prestamoA o $prestamoB pueden ser null.
		return ($prestamoA == $prestamoB);
	}
		
	/**
	 * Este método nos envía al formulario de edición de cambio de estado
	 */
	public function cambiarEstado()
	{
		// Verifico que existan todos los parametros obligatorios
		if ($this->parametrosVerificarParametros(array('data','estado_nuevo','grilla_origen','parametros_serializados')))
		{
			// Deserializo el parametro (me invocaron desde la vista).
			$prestamo = new Prestamo();
			$prestamo->Deserializar($this->parametrosObtener('data'));
				
			// Parametros para la vista (estado_nuevo)
			$parametros_vista = $this->parametrosObtenerColeccion(array('estado_nuevo'));
			
			$parametros_vista['grilla_origen'] = $this->parametrosObtener('grilla_origen');
			$parametros_vista['parametros_serializados'] = $this->parametrosObtener('parametros_serializados');
			
			//Se crea una instancia de la "vista"
			$vista = new VistaPrestamos();
		
			//se muestra el formulario de edicion de cambio de estado
			$vista->editarCambioEstado($prestamo, $parametros_vista);
		}
		else
		{
			$parametros_vista['mensaje'] = "ERROR: en cambiarEstado, falta el par&aacute;metro necesario";
			$parametros_vista['tipo_mensaje'] = 2;
			
			// Se vuelve al listado respectivo
			$this->volverAlCambiarEstadoSegunGrillaOrigen($parametros_vista);
		}
	}

	/**
	 * Este método se ejecuta desde el formulario de edición de Cambio de Estado
	 */
	public function guardarCambioEstado()
	{
		// Verifico que existan todos los parametros MINIMOS obligatorios
		if ($this->parametrosVerificarParametros(array('anio','tipo','numero','cuerpo','alcance','digito',
				'cuerpoalcance','anexoalcance','cuerpoanexoalcance','anexo','cuerpoanexo','fecha_solicitud',
				'estado_nuevo','fecha_estado','observaciones_prestamo','grilla_origen','parametros_serializados')))
		{
			// Parametros del cambio de estado
			$nuevo_estado = $this->parametrosObtener('estado_nuevo');
			$fecha_estado = $this->parametrosObtener('fecha_estado');
			
			// Dependiendo del estado que se edito, son los campos extra que necesito
			if (($nuevo_estado == Prestamo::E_PRESTADO) && !$this->parametrosVerificarParametros(array('libro_numero', 'libro_folio')))
			{
				$this->parametrosAsignar('mensaje', "ERROR: en cambiarEstado, falta el parametro necesario (libro_numero y libro_folio)");
				$this->parametrosAsignar('tipo_mensaje', 2);
			}
			else
			{
				// Recargo el prestamo (solamente le asigno los campos clave, el resto se recarga solo).
				$prestamo = new Prestamo();
				
				$prestamo->anio = $this->parametrosObtener('anio');
				$prestamo->tipo = $this->parametrosObtener('tipo');
				$prestamo->numero = $this->parametrosObtener('numero');
				$prestamo->cuerpo = $this->parametrosObtener('cuerpo');
				$prestamo->alcance = $this->parametrosObtener('alcance');
				$prestamo->digito = $this->parametrosObtener('digito');
				$prestamo->cuerpoalcance = $this->parametrosObtener('cuerpoalcance');
				$prestamo->anexoalcance = $this->parametrosObtener('anexoalcance');
				$prestamo->cuerpoanexoalcance = $this->parametrosObtener('cuerpoanexoalcance');
				$prestamo->anexo = $this->parametrosObtener('anexo');
				$prestamo->cuerpoanexo = $this->parametrosObtener('cuerpoanexo');
				$prestamo->fecha_solicitud = $this->parametrosObtener('fecha_solicitud');
				
				$prestamo = $this->ng_prestamos->RecargarPrestamo($prestamo); // recargo el resto de los atributos
					
				// También le cambio las observaciones
				$prestamo->observaciones_prestamo = $this->parametrosObtener('observaciones_prestamo');
				
				// Si es prestado, tengo que asignarle el libro_numero y libro_folio
				if ($nuevo_estado == Prestamo::E_PRESTADO)
				{
					$prestamo->libro_numero = $this->parametrosObtener('libro_numero');
					$prestamo->libro_folio = $this->parametrosObtener('libro_folio');
				}
								
				// Trato de hacer el cambio de estado y guardar
				try
				{
					$prestamo = $this->ng_prestamos->CambiarEstadoPrestamo($prestamo, $nuevo_estado, $fecha_estado);
					
					$this->ng_prestamos->GuardarPrestamo($prestamo);
					
					$mensaje = "Pr&eacute;stamo $prestamo->anio-$prestamo->tipo-$prestamo->numero actualizado con &eacute;xito.";
					$tipo_mensaje = 1;
				}
				catch (Exception $e)
				{
					$mensaje = "[ERROR] ".$e->getMessage();
					$tipo_mensaje = 2;
				}
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

		// Para saber a qué grilla volver
		$parametros_vista['grilla_origen'] = $this->parametrosObtener('grilla_origen');
		
		$parametros_vista['mensaje'] = $mensaje;
		$parametros_vista['tipo_mensaje'] = $tipo_mensaje;

		// Se asignan los parámetros de la vista en el controlador de préstamos
		$this->parametrosAsignarColeccion($parametros_vista);
		
		// Se vuelve al listado respectivo
		$this->volverAlCambiarEstadoSegunGrillaOrigen($parametros_vista);
	}
	
	/**
	 * Se vuelve al listado respectivo, según la grilla origen 
	 * donde se cambió el estado del préstamo
	 * @param array $parametros_vista
	 */
	public function volverAlCambiarEstadoSegunGrillaOrigen($parametros_vista)
	{
		// Si se viene de la grilla general
		if ( $parametros_vista['grilla_origen'] == "grilla_general" )
		{
			$parametros_vista['sentido'] = '';
			
			$this->mostrarListadoGeneralSegunCriterioUtilizado($parametros_vista);
		}
		else
		{
			// si se viene de la solapa de Préstamos de un expediente determinado
			$this->listarEnSolapa($parametros_vista['mensaje'], $parametros_vista['tipo_mensaje']);
		}
	}
	
	/**
	 * Se muestra el listado general, según el criterio utilizado
	 * antes de guardar el cambio de estado,
	 * de haberse utilizado uno, sino se muestran todos los préstamos
	 * 
	 * @param array $parametros_vista, criterio utilizado
	 */
	public function mostrarListadoGeneralSegunCriterioUtilizado($parametros_vista)
	{
		// Si se filtró por expediente
		if ( $parametros_vista['anio'] != '' )
		{
			$this->listarPrestamosPorExpediente($parametros_vista['mensaje'], $parametros_vista['tipo_mensaje']);
		}
		// Si se filtró por estado, solicitante y/o rango de fechas
		else if( $parametros_vista['estado_solicitado'] != '' ||
				 $parametros_vista['estado_prestado'] != '' ||
				 $parametros_vista['estado_devuelto'] != '' ||
				 $parametros_vista['estado_anulado'] != '' ||
				 $parametros_vista['solicitante'] != '' ||
				 $parametros_vista['fecha_hasta'] != '' )
		{
			$this->listarPrestamosPorCriterioBusqueda($parametros_vista['mensaje'], $parametros_vista['tipo_mensaje']);
		}
		else // sino, se listan todos
		{
			// Se muestran todos los préstamos
			$this->mostrarGrillaGeneral($parametros_vista['mensaje'], $parametros_vista['tipo_mensaje']);
		}
	}
	
	/**
	 * Se guarda un préstamo desde la grilla general,
	 * si es de un expediente externo ( tipo = D ),
	 * se obtiene un objeto de Solicitud con dicho préstamo
	 * y luego se guarda la solicitud al Ente Externo,
	 * con la fecha de solicitud del préstamo
	 * (fecha_solicitud_hcd = fecha_solicitud)
	 */
	public function guardar()
	{
		// Para saber de qué grilla se vino
		$grilla_origen = $this->parametrosObtener('grilla_origen');
		
		// Se verifica que no falte ningún parámetro obligatorio
		if ($this->parametrosVerificarParametros(array('anio','tipo','numero','cuerpo','alcance','digito',
				'cuerpoalcance','anexoalcance','cuerpoanexoalcance','anexo','cuerpoanexo','fecha_solicitud')))
		{
			// Del campo solicitante, se separa el Tipo y el Código, por el guión medio
			$partes_solicitante = explode('-', $this->parametrosObtenerONull('solicitante'));
				
			// Creamos una instancia de préstamo a partir de los parametros que hay cargados en el controlador.
			$prestamo = $this->mapearPrestamo($partes_solicitante);
			
			try
			{
				// Verifico si no me modificaron el préstamo mientras lo estaba editando
				$prestamo_actual = $this->ng_prestamos->RecargarPrestamo($prestamo);
	
				// Si se agrega el préstamo
				if ($prestamo_actual == null)
				{
					// Se guardan los datos del préstamo
					$prestamo = $this->ng_prestamos->GuardarPrestamo($prestamo);
					
					// Se verifica si requiere un expediente externo
					if ($this->ng_prestamos->RequiereSolicitudExpedienteExterno($prestamo))
					{
						// Se obtiene un objeto de Solicitud de Expediente Externo
						$solicitud = $this->ng_prestamos->ObtenerInstanciaSolicitudExpedienteExterno($prestamo);
							
						// Se guardan los datos de la Solicitud del expediente externo
						$solicitud = $this->ng_prestamos->GuardarSolicitudExpedienteExterno($solicitud);
					}
					
					$mensaje = "El pr&eacute;stamo $prestamo->anio-$prestamo->tipo-$prestamo->numero se guard&oacute; con &eacute;xito.";
					$tipo_mensaje = 1;
				}
				else // Estoy editando
				{
					$prestamo_original = $this->obtenerRegistroOriginal();
	
					// OJO!!! $prestamo_original puede ser null (si no esta en sesion).
					if ($this->SonPrestamosIguales($prestamo_actual, $prestamo_original))
					{
						// Se guardan los datos del préstamo
						$prestamo = $this->ng_prestamos->GuardarPrestamo($prestamo);
						
						$mensaje = "El pr&eacute;stamo $prestamo->anio-$prestamo->tipo-$prestamo->numero se guard&oacute; con &eacute;xito.";
						$tipo_mensaje = 1;
					}
					else
					{
						$mensaje = "No se puede guardar el pr&eacute;stamo $prestamo->anio-$prestamo->tipo-$prestamo->numero, por haber sido editado desde otra terminal.";
						$tipo_mensaje = 2;
					}
				}
				
				// Vuelve al listado/grilla original 
				$this->volverSegunGrillaOrigen($grilla_origen, $mensaje, $tipo_mensaje);
			}
			catch (Exception $e)
			{
				$mensaje = $e->getMessage();
				$tipo_mensaje = 2;
	
				// Simulo una llamada desde la interfase, por eso tengo que serializar el prestamo.
				$this->parametrosAsignar('data', $prestamo->Serializar());
				$this->parametrosAsignar('estado_a_editar', $prestamo->estado);
				$this->parametrosAsignar('pagina', 0);
	
				// Se sigue mostrando la edición del préstamo, con el mensaje de error.
				$this->agregar($mensaje, $tipo_mensaje);
			}
		}
		else
		{
			$mensaje = "ERROR: en guardar, faltan par&aacute;metros";
			$tipo_mensaje = 2;

			// Vuelve al listado/grilla original
			$this->volverSegunGrillaOrigen($grilla_origen, $mensaje, $tipo_mensaje);
		}
	}

	/**
	 * Vuelve al listado/grilla original 
	 * @param string $grilla_origen
	 * @param string $mensaje
	 * @param int $tipo_mensaje
	 */
	public function volverSegunGrillaOrigen($grilla_origen, $mensaje, $tipo_mensaje)
	{
		// Si se llegó desde la solapa de préstamos de un expediente determinado
		if( $grilla_origen == 'grilla_solapa')
		{
			// Se muestra el listado de préstamos de dicho expediente
			$this->listarEnSolapa($mensaje, $tipo_mensaje);
		}
		else
		{
			// Se muestra la grilla general de préstamos
			$this->mostrarGrillaGeneral($mensaje, $tipo_mensaje);
		}
	}
	
	/**
	 * Se crea una instancia de préstamo a partir de los parámetros que hay cargados en el controlador.
	 * @param Array $partes_solicitante
	 * @return Prestamo $prestamo
	 */
	public function mapearPrestamo($partes_solicitante)
	{
		$prestamo = new Prestamo();
	
		$prestamo->anio = $this->parametrosObtener('anio');
		$prestamo->tipo = $this->parametrosObtener('tipo');
		$prestamo->numero = $this->parametrosObtener('numero');
		$prestamo->cuerpo = $this->parametrosObtener('cuerpo');
		$prestamo->alcance = $this->parametrosObtener('alcance');
		$prestamo->digito = $this->parametrosObtener('digito');
		$prestamo->cuerpoalcance = $this->parametrosObtener('cuerpoalcance');
		$prestamo->anexoalcance = $this->parametrosObtener('anexoalcance');
		$prestamo->cuerpoanexoalcance = $this->parametrosObtener('cuerpoanexoalcance');
		$prestamo->anexo = $this->parametrosObtener('anexo');
		$prestamo->cuerpoanexo = $this->parametrosObtener('cuerpoanexo');
		$prestamo->fecha_solicitud = $this->parametrosObtener('fecha_solicitud');
	
		$prestamo->fecha_prestado = $this->parametrosObtenerONull('fecha_prestado');
		$prestamo->fecha_devuelto = $this->parametrosObtenerONull('fecha_devuelto');
		$prestamo->fecha_anulado = $this->parametrosObtenerONull('fecha_anulado');
		$prestamo->estado = $this->parametrosObtenerONull('estado');
	
		$prestamo->solicitante_tipo = $partes_solicitante[0];
		$prestamo->solicitante_codigo = $partes_solicitante[1];
	
		$prestamo->libro_numero = $this->parametrosObtenerONull('libro_numero');
		$prestamo->libro_folio = $this->parametrosObtenerONull('libro_folio');
		$prestamo->observaciones_prestamo = $this->parametrosObtenerONull('observaciones_prestamo');
		$prestamo->id_usuario = $this->parametrosObtenerONull('id_usuario');
	
		return $prestamo;
	}
	 
	/**
	 * Se muestra la ventana modal para seleccionar el solicitante por nombre
	 */
	public function pedirNombreSolicitanteModal()
	{
		$modelo = new prestamosModel();
		$solicitantes = $modelo->obtenerSolicitantes();
		 
		$vista = new VistaPrestamos();
		$vista->pedirNombreSolicitanteModal($solicitantes);
	}

	/**
	 * Se genera el reporte en formato PDF para guardarlo y/o imprimirlo
	 */
	public function generarReporte()
	{
		// Se comienza a armar una colección de parámetros, los cuales son enviados desde la interfaz de usuario
		$parametros_vista = $this->armarConjuntoParametrosPrestamos('fecha_solicitud');
		
		// Se asignan los datos parametrizados en un array auxiliar
		$parametros_vista_aux = $this->parametros;
	
		// Se agregan datos a la colección de parámetros para la Vista
		$parametros_vista = array_merge($parametros_vista, $parametros_vista_aux);

		$solicitante = $this->parametrosObtenerONull('solicitante');
		// Si se recibió un Solicitante
		if ( !is_null($solicitante) && $solicitante != '0' )
		{
			// Se separa por el guión medio
			$partes_solicitante = explode('-', $solicitante);
		}
		// Se asignan el Tipo y el Código del solicitante
		$parametros_vista['solicitante_tipo'] = ($partes_solicitante[0]) ? $partes_solicitante[0] : null;
		$parametros_vista['solicitante_codigo'] = ($partes_solicitante[1]) ? $partes_solicitante[1] : null;
		
		$conjuntoEstados = Array();
		// Para estado Solicitado
		if ( !is_null($parametros_vista['estado_solicitado']) && $parametros_vista['estado_solicitado'] != '')
		{
			$conjuntoEstados[]= $parametros_vista['estado_solicitado'];
		}
		// Para estado Prestado
		if ( !is_null($parametros_vista['estado_prestado']) && $parametros_vista['estado_prestado'] != '')
		{
			$conjuntoEstados[]= $parametros_vista['estado_prestado'];
		}
		// Para estado Devuelto
		if ( !is_null($parametros_vista['estado_devuelto']) && $parametros_vista['estado_devuelto'] != '')
		{
			$conjuntoEstados[] = $parametros_vista['estado_devuelto'];
		}
		// Para estado Anulado
		if ( !is_null($parametros_vista['estado_anulado']) && $parametros_vista['estado_anulado'] != '')
		{
			$conjuntoEstados[]= $parametros_vista['estado_anulado'];
		}
	
		// Se obtienen los préstamos
		$prestamos = $this->ng_prestamos->ObtenerPrestamos(
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
						 $parametros_vista['solicitante_tipo'],
						 $parametros_vista['solicitante_codigo'],
						 ( count($conjuntoEstados) > 0 ) ? $conjuntoEstados : null,
						 array($parametros_vista['campo_orden']),
						 $_SESSION['ultimo_sentido'],
						 null,
						 null);
	
		// Se crea una instancia de la vista
		$vista = new VistaPrestamos();
			
		// Se muestra el listado de las Solicitudes
		$vista->generarReporte($prestamos, $parametros_vista);
	}
	
	/**
	 * Se elimina un Préstamo (de forma lógica)
	 */
	public function eliminar()
	{
		// Verifico que existan todos los parametros obligatorios
		if ( $this->parametrosVerificarParametros(array('data')) )
		{
			// Deserializo el parametro (me invocaron desde la vista).
			$prestamo = new Prestamo();
			$prestamo->Deserializar($this->parametrosObtener('data'));
			
			try
			{
				// Se elimina el Préstamo de forma lógica
				$this->ng_prestamos->EliminarPrestamo($prestamo);
	
				$mensaje = "El pr&eacute;stamo $prestamo->anio-$prestamo->tipo-$prestamo->numero se elimin&oacute; con &eacute;xito.";
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
		
		// Para saber a qué grilla volver
		$parametros_vista['grilla_origen'] = $this->parametrosObtener('grilla_origen');
		
		$parametros_vista['mensaje'] = $mensaje;
		$parametros_vista['tipo_mensaje'] = $tipo_mensaje;
		
		// Se asignan los parámetros de la vista en el controlador de préstamos
		$this->parametrosAsignarColeccion($parametros_vista);
		
		// Se vuelve al listado respectivo
		$this->volverAlCambiarEstadoSegunGrillaOrigen($parametros_vista);
	}

	/**
	 * Se muestra el formulario para editar la observación de un Préstamo
	 */
	public function editarObservaciones()
	{
		// Verifico que existan todos los parametros obligatorios
		if ( $this->parametrosVerificarParametros(array('data', 'grilla_origen', 'parametros_serializados')) )
		{
			// Deserializo el parametro (me invocaron desde la vista).
			$prestamo = new Prestamo();
			$prestamo->Deserializar($this->parametrosObtener('data'));
				
			$parametros_vista['grilla_origen'] = $this->parametrosObtener('grilla_origen');
			$parametros_vista['parametros_serializados'] = $this->parametrosObtener('parametros_serializados');
				
			//Se crea una instancia de la "vista"
			$vista = new VistaPrestamos();
				
			//se muestra el formulario de edicion de cambio de estado
			$vista->editarObservaciones($prestamo, $parametros_vista);
		}
		else
		{
			$parametros_vista['mensaje'] = "ERROR: en editarObservaciones, falta el par&aacute;metro necesario";
			$parametros_vista['tipo_mensaje'] = 2;
				
			// Se vuelve al listado respectivo
			$this->volverAlCambiarEstadoSegunGrillaOrigen($parametros_vista);
		}
	}
	
	/**
	 * Se guardan las modificaciones de observaciones, y datos del libro (en caso de existir) del Préstamo
	 */
	public function guardarObservaciones()
	{
		// Se verifica que existan todos los parametros MINIMOS obligatorios
		if ($this->parametrosVerificarParametros(array('anio','tipo','numero','cuerpo','alcance','digito',
				'cuerpoalcance','anexoalcance','cuerpoanexoalcance','anexo','cuerpoanexo','fecha_solicitud',
				'estado','observaciones_prestamo','grilla_origen','parametros_serializados')))
		{
			// Se crea una instancia de Prestamo
			$prestamo = new Prestamo();
			
			$prestamo->estado = $this->parametrosObtener('estado');
			
			// Si el estado actual es Prestado o Devuelto, se utilizan los datos del libro (número y folio)
			if ( ($prestamo->estado == Prestamo::E_PRESTADO || 
				  $prestamo->estado == Prestamo::E_DEVUELTO) && 
				  !$this->parametrosVerificarParametros(array('libro_numero', 'libro_folio')) )
			{
				$this->parametrosAsignar('mensaje', "ERROR: en guardarObservaciones, falta el parametro necesario (libro_numero y libro_folio)");
				$this->parametrosAsignar('tipo_mensaje', 2);
			}
			else
			{
				$prestamo->anio = $this->parametrosObtener('anio');
				$prestamo->tipo = $this->parametrosObtener('tipo');
				$prestamo->numero = $this->parametrosObtener('numero');
				$prestamo->cuerpo = $this->parametrosObtener('cuerpo');
				$prestamo->alcance = $this->parametrosObtener('alcance');
				$prestamo->digito = $this->parametrosObtener('digito');
				$prestamo->cuerpoalcance = $this->parametrosObtener('cuerpoalcance');
				$prestamo->anexoalcance = $this->parametrosObtener('anexoalcance');
				$prestamo->cuerpoanexoalcance = $this->parametrosObtener('cuerpoanexoalcance');
				$prestamo->anexo = $this->parametrosObtener('anexo');
				$prestamo->cuerpoanexo = $this->parametrosObtener('cuerpoanexo');
				$prestamo->fecha_solicitud = $this->parametrosObtener('fecha_solicitud');
	
				// Se recarga el resto de los atributos
				$prestamo = $this->ng_prestamos->RecargarPrestamo($prestamo);
					
				// Se toma la modificación en las observaciones
				$prestamo->observaciones_prestamo = $this->parametrosObtener('observaciones_prestamo');
	
				// Si el estado actual es Prestado o Devuelto
				if ($prestamo->estado == Prestamo::E_PRESTADO || $prestamo->estado == Prestamo::E_DEVUELTO)
				{
					// Se toma la modificación del libro_numero y libro_folio
					$prestamo->libro_numero = $this->parametrosObtener('libro_numero');
					$prestamo->libro_folio = $this->parametrosObtener('libro_folio');
				}
	
				// Se guardan las modificaciones en el Préstamo
				try
				{
					$this->ng_prestamos->GuardarPrestamo($prestamo);
						
					$mensaje = "Pr&eacute;stamo $prestamo->anio-$prestamo->tipo-$prestamo->numero modificado con &eacute;xito.";
					$tipo_mensaje = 1;
				}
				catch (Exception $e)
				{
					$mensaje = "ERROR: ".$e->getMessage();
					$tipo_mensaje = 2;
				}
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
	
		// Para saber a qué grilla volver
		$parametros_vista['grilla_origen'] = $this->parametrosObtener('grilla_origen');
	
		$parametros_vista['mensaje'] = $mensaje;
		$parametros_vista['tipo_mensaje'] = $tipo_mensaje;
	
		// Se asignan los parámetros de la vista en el controlador de préstamos
		$this->parametrosAsignarColeccion($parametros_vista);
	
		// Se vuelve al listado respectivo
		$this->volverAlCambiarEstadoSegunGrillaOrigen($parametros_vista);
	}
	
}
?>

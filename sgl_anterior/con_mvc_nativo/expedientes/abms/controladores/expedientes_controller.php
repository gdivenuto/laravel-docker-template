<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//Incluye el modelo que corresponde
require 'modelos/expedientes.php';
require 'modelos/autores.php';
require 'modelos/codtemas.php';
require 'modelos/proyectos.php';
require 'modelos/codproyectos.php';
require 'modelos/categorias.php';
require 'modelos/lugares.php';

//Incluye la vista que corresponde
require 'vistas/expedientes.php';
require 'vistas/proyectos.php';

class expedientes_controller extends ControllerBase
{
	const RUTA_DIRECTORIO_PROYECTOS = "/var/www/sgl/expedientes/proyectos/";
	//const USUARIO = 'expe';
	//const PASSWORD = '123456';

	private $filtro = Array();
	private $clave = Array();
	private $mensaje;
	private $tipo_mensaje;

	public function __construct()
	{
		parent::__construct();

		// Se crea una instancia de cada modelo
		$this->modelo           = new expedientesModel();
		$this->modeloLugares    = new lugaresModel();
		$this->modeloCategorias = new categoriasModel();
		$this->modeloCodTemas   = new codtemasModel();
		$this->modeloTemas 		= new temasModel();
	    $this->modeloAutores 	= new autoresModel();

		// Se crea una instancia de la Vista
		$this->vista = new VistaExpedientes();

		// Se inicializa el mensaje de resultados
		$this->mensaje = "";
	}

	public function mostrarMensajeError($mensaje = '')
	{
		$this->listar('', $mensaje, 2);
	}

	public function guardarRegistroOriginal($original)
	{
		$_SESSION['anio_original']                    = $original[0]['anio'];
		$_SESSION['tipo_original']                    = $original[0]['tipo'];
		$_SESSION['numero_original']                  = $original[0]['numero'];
		$_SESSION['cuerpo_original']                  = $original[0]['cuerpo'];
		$_SESSION['alcance_original']                 = $original[0]['alcance'];
		$_SESSION['iniciador_tipo_original']          = $original[0]['iniciador_tipo'];
		$_SESSION['iniciador_codigo_original']        = $original[0]['iniciador_codigo'];
		$_SESSION['iniciador_bloque_tipo_original']   = $original[0]['iniciador_bloque_tipo'];
		$_SESSION['iniciador_bloque_codigo_original'] = $original[0]['iniciador_bloque_codigo'];
		$_SESSION['agregado_anio_original']           = $original[0]['agregado_anio'];
		$_SESSION['agregado_tipo_original']           = $original[0]['agregado_tipo'];
		$_SESSION['agregado_numero_original']         = $original[0]['agregado_numero'];
		$_SESSION['agregado_cuerpo_original']         = $original[0]['agregado_cuerpo'];
		$_SESSION['agregado_alcance_original']        = $original[0]['agregado_alcance'];
		$_SESSION['id_codcategoria_original']         = $original[0]['id_codcategoria'];
		$_SESSION['fecha_entrada_expe_original']      = $original[0]['fecha_entrada_expe'];
		$_SESSION['caratula_original']                = $original[0]['caratula'];
		$_SESSION['observaciones_expe_original']      = $original[0]['observaciones_expe'];
		$_SESSION['marca_comision_original']          = $original[0]['marca_comision'];
		$_SESSION['digi_completa_original']           = $original[0]['digi_completa'];
		$_SESSION['id_usuario_original']              = $original[0]['id_usuario'];
	}

	public function listar($datos = '', $mensaje = '', $tipo_mensaje = '')
	{
	    $_SESSION['cargado_previamente'] = false;

	    if ( isset($datos['anio']) && $datos['anio'] != '' )
	    {
			$this->filtro['anio'] = $datos['anio'];
			$this->filtro['tipo'] = $datos['tipo'];
			$this->filtro['numero'] = $datos['numero'];
			$this->filtro['cuerpo'] = $datos['cuerpo'];
			$this->filtro['alcance'] = $datos['alcance'];

			// DIRECCIÓN PARA LA PAGINACIÓN (PRIMERO, ANTERIOR, SGTE., ÚLTIMO)
			$this->filtro['sentido'] = 'anterior';
	    }
	    else
	    {
			$this->filtro['anio'] = Validador::validarParametro('anio');
			$this->filtro['tipo'] = Validador::validarParametro('tipo');
			$this->filtro['numero'] = Validador::validarParametro('numero');
			$this->filtro['cuerpo'] = Validador::validarParametro('cuerpo');
			$this->filtro['alcance'] = Validador::validarParametro('alcance');

			// DIRECCIÓN PARA LA PAGINACIÓN (PRIMERO, ANTERIOR, SGTE., ÚLTIMO)
			$this->filtro['sentido'] = Validador::validarParametro('sentido');

			// SI SE LLEGÓ MEDIANTE EL TECLADO AL RECORRER EL LISTADO
			$this->filtro['por_teclado'] = Validador::validarParametro('por_teclado');
			$this->filtro['agregado'] = Validador::validarParametro('agregado');

			// SI SE LLEGÓ MEDIANTE EL botón Buscar
			$this->filtro['por_boton_buscar'] = Validador::validarParametro('por_boton_buscar');
	    }

	    //SE OBTIENE LA CANTIDAD TOTAL DE EXPEDIENTES
	    $_SESSION['totalExpedientes'] = $this->modelo->obtenerCantidad();

	    //SE ESTABLECE EL FILTRO EN EL MODELO
	    $this->modelo->setFiltro($this->filtro);

	    //Se le pide al modelo todos los items
	    $listado = $this->modelo->listadoTotal();

	    $cant_expedientes = count($listado);
	    // Por cada expediente
	    for ($i=0; $i < $cant_expedientes; $i++) {
	    	// Se obtiene el estado de existencia de su proyecto 'original.doc'
	    	$listado[$i]['estado_doc'] = $this->verificarEstadoDoc($listado[$i]);

	    	// Se obtiene el estado de existencia de su Digitalización
	    	$listado[$i]['estado_digitalizacion'] = $this->verificarEstadoDigitalizacion($listado[$i]);
	    }

	    if ( $mensaje == '' && $tipo_mensaje == '' ) {
			// SI SE LLEGA DE Tareas (Carga de Proyectos, Carga de Giros)
			$mensaje = Validador::validarParametro('mensaje');
			$tipo_mensaje = Validador::validarParametro('tipo_mensaje');
	    }

		// 17/05/2012	SI NO SE HA ELIMINADO UN EXPEDIENTE O UNA NOTA
		if ( ! isset($datos['eliminado']) ) {
			// 10/01/2012
			if ( $this->filtro['tipo'] ) {
				if ( !$this->modelo->existe($this->filtro) ) {
					if ( $this->filtro['tipo'] == 'E')
						$mensaje = "EXPEDIENTE NO ENCONTRADO EN EL SISTEMA";
					elseif ( $this->filtro['tipo'] == 'N')
						$mensaje = "NOTA NO ENCONTRADA EN EL SISTEMA";
					else
						$mensaje = "RECOMENDACION NO ENCONTRADA EN EL SISTEMA";

					$tipo_mensaje = 2;
				}
			}
		}

	    $this->vista->listar($listado, $mensaje, $tipo_mensaje, $this->filtro);
	}

	// SE OBTIENE EL LISTADO CODIFICADO EN NOTACION DE OBJETOS (JSON)
	public function obtenerListadoCodificado($datos = '')
	{
	    if ($datos['anio'] != '')
	    {
			$this->filtro['anio']    = $datos['anio'];
			$this->filtro['tipo']    = $datos['tipo'];
			$this->filtro['numero']  = $datos['numero'];
			$this->filtro['cuerpo']  = $datos['cuerpo'];
			$this->filtro['alcance'] = $datos['alcance'];
	    }
	    else
	    {
			$this->filtro['anio']    = Validador::validarParametro('anio');
			$this->filtro['tipo']    = Validador::validarParametro('tipo');
			$this->filtro['numero']  = Validador::validarParametro('numero');
			$this->filtro['cuerpo']  = Validador::validarParametro('cuerpo');
			$this->filtro['alcance'] = Validador::validarParametro('alcance');
		}

	    //SE OBTIENE LA CANTIDAD TOTAL DE EXPEDIENTES
	    $_SESSION['totalExpedientes'] = $this->modelo->obtenerCantidad();

	    //SE ESTABLECE EL FILTRO EN EL MODELO
	    $this->modelo->setFiltro($this->filtro);

	    //Se le pide al modelo todos los items
	    $listado = $this->modelo->listadoTotal();

		// SE CODIFICA EL LISTADO (ARRAY) A NOTACION DE OBJETOS (JSON)
		$listado_json = json_encode($listado);

		return $listado_json;
	}

	// DEVUELVE EL LISTADO EN UN ARRAY, EN BASE A UN JSON
	public function obtenerListadoDeJSON($listado_json)
	{
		// SE DECODIFICA EL JSON GENERANDO EL LISTADO (ARRAY)
		$listado = json_decode($listado_json);

		return $listado;
	}

	public function obtenerRegistroExpediente()
	{
	    $_SESSION['cargado_previamente'] = false;

	    $clave_expediente = Array('anio' => Validador::validarParametro('anio'),
								  'tipo' => Validador::validarParametro('tipo'),
								  'numero' => Validador::validarParametro('numero'),
								  'cuerpo' => Validador::validarParametro('cuerpo'),
								  'alcance' => Validador::validarParametro('alcance')
								 );

	    //SE OBTIENE LA CANTIDAD TOTAL DE EXPEDIENTES
	    $_SESSION['totalExpedientes'] = $this->modelo->obtenerCantidad();

	    //Se le pide al modelo todos los items
	    $datos_expediente = $this->modelo->obtenerRegistroExpediente($clave_expediente);

	    $this->vista->listar($datos_expediente);
	}

	public function editar($clave = null, $mensaje = '')
	{
		$this->filtro['anio']    = ( $clave['anio'] != '' ) ? $clave['anio'] : Validador::validarParametro('anio');

		$this->filtro['tipo']    = ( $clave['tipo'] != '' ) ? $clave['tipo'] : Validador::validarParametro('tipo');

		$this->filtro['numero']  = ( $clave['numero'] != '' ) ? $clave['numero'] : Validador::validarParametro('numero');

		$this->filtro['cuerpo']  = ( $clave['cuerpo'] != '' ) ? $clave['cuerpo'] : Validador::validarParametro('cuerpo');

		$this->filtro['alcance'] = ( $clave['alcance'] != '' ) ? $clave['alcance'] : Validador::validarParametro('alcance');

	    $this->filtro['sentido'] = 'para_edicion';

	    if ( Validador::validarParametro('por_btAgregarExped') == 'false' )
			$_SESSION['campos_habilitados'] = true;

	    //Se establece el filtro en el modelo
	    $this->modelo->setFiltro($this->filtro);

	    //Se le pide al modelo todos los items
	    $listado = $this->modelo->listadoTotal('editar');

	    // SE GUARDA EL REGISTRO EN SESION PARA VERIFICAR
	    // LUEGO SI LO HA MODIFICADO OTRO USUARIO
	    $this->guardarRegistroOriginal($listado);

	    // 21/05/2013 SÓLO SI POSEE NÚMERO EL EXPEDIENTE O LA NOTA
	    if ( $this->filtro['numero'] != '' ) {
			//Se obtienen los Temas para dicho Expediente
			$listadoTemas = $this->obtenerTemasPropios($filtro);

			//Se obtienen los Autores para dicho Expediente
			$listadoAutores = $this->obtenerAutoresPropios($filtro);
		}

		$lista_completa_lugares    = $this->modeloLugares->listadoModal();

		$lista_completa_categorias = $this->modeloCategorias->listadoModal();

		$lista_completa_temas      = $this->modeloCodTemas->listadoModal();

		// 2020/05/07 XXXX
		// Se obtiene el estado de existencia de su Digitalización
	    $listado[0]['estado_digitalizacion'] = $this->verificarEstadoDigitalizacion($listado[0]);

	    //se muestra el registro a editar, con los listados de sus Temas y sus Autores
	    //(AL EDITAR UNO EXISTENTE)
	    $this->vista->editar($listado, $listadoAutores, $listadoTemas, $mensaje, $lista_completa_lugares, $lista_completa_categorias, $lista_completa_temas, $this->filtro);
	}

	public function agregar()
	{
	    $_SESSION['campos_habilitados'] = false;

		// SE MUESTRA EL FORM DE EDICION
		$this->vista->editar();
	}

	public function habilitarRestoDatos()
	{
	    $_SESSION['campos_habilitados'] = true;

	    $clave = Array(
						'anio' => Validador::validarParametro('anio'),
						'tipo' => Validador::validarParametro('tipo'),
						'numero' => Validador::validarParametro('numero'),
						'cuerpo' => Validador::validarParametro('cuerpo'),
						'alcance' => Validador::validarParametro('alcance')
					  );

	    // SE REDIRECCIONA AL EDIT DE EXPEDIENTES PARA SEGUIR EDITÁNDOLO
	    $this->editar($clave);
	}

	public function insertar()
	{
	    $datos = $_REQUEST;//SE RECIBEN LOS DATOS

	    $clave_a_mostrar = $datos['anio'].'-'.$datos['tipo'].'-'.$datos['numero'].'-'.$datos['cuerpo'].'-'.$datos['alcance'];

	    if ( $datos['tipo'] == 'E' )
			$nombre_tipo = 'Expediente';

	    if ( $datos['tipo'] == 'N' )
			$nombre_tipo = 'Nota';

	    if ( $datos['tipo'] == 'R' )
			$nombre_tipo = 'Recomendacion';

	    // SE FORMATEA LA FECHA DE ENTRADA COMO yyyy-mm-dd:
	    if ( isset($datos['fecha_entrada_expe']) && $this->esFechaValida($datos['fecha_entrada_expe']) )
			$datos['fecha_entrada_expe'] = $this->modelo->formatearFechaMySQL($datos['fecha_entrada_expe']);

	    // SI AÚN NO POSEE EL NÚMERO:
	    if ( $datos['numero'] === '' )
		{
			// SE GENERA EL NUEVO NÚMERO:
			$datos['numero'] = $this->modelo->setearNumeroSgte($datos['anio'], $datos['tipo']);

			$clave_a_mostrar = $datos['anio'].'-'.$datos['tipo'].'-'.$datos['numero'].'-'.$datos['cuerpo'].'-'.$datos['alcance'];

			// SI SE INSERTA EL Expediente
			if ( $this->modelo->insertar($datos) )
			{
				$mensaje = 'Su '.$nombre_tipo.' '.$clave_a_mostrar.' se ha ingresado con éxito.';
				$tipo_mensaje = 1;

				// SE MUESTRA EL LISTADO DE EXPEDIENTES
				$this->listar($datos, $mensaje, $tipo_mensaje);
			}
			else
			{
				$mensaje = 'Su '.$nombre_tipo.' '.$clave_a_mostrar.' no se ha ingresado.';
				$tipo_mensaje = 2;
				$_SESSION['campos_habilitados'] = false;

				$this->vista->editar($datos, null, null, $mensaje, null, null, null, null, $tipo_mensaje);
			}
	    }
	    else // SI POSEE EL NÚMERO:
	    {
			// SE VERIFICA SI EXISTE LA CLAVE SIN Cuerpo Y Alcance (VALOR CERO), PARA NO INGRESAR EL MISMO REGISTRO
			if ( $this->modelo->verificar_sin_cpo_y_alc($datos) )
			{
				// SE VERIFICA QUE NO EXISTA CON LOS VALORES DE Cuerpo Y Alcance INGRESADOS
				if ( !$this->modelo->verificar_con_clave_completa($datos) )
				{
					// SI SE INGRESA EL Expediente / Nota
					if ( $this->modelo->insertar($datos) )
					{
						$mensaje = 'Su '.$nombre_tipo.' '.$clave_a_mostrar.' se ha ingresado con éxito.';
						$tipo_mensaje = 1;

						// SE MUESTRA EL LISTADO DE EXPEDIENTES
						$this->listar($datos, $mensaje, $tipo_mensaje);
					}
					else
					{
						$mensaje = 'Su '.$nombre_tipo.' '.$clave_a_mostrar.' no se ha ingresado.';
						$tipo_mensaje = 2;
						$_SESSION['campos_habilitados'] = false;

						$this->vista->editar($datos, null, null, $mensaje, null, null, null, null, $tipo_mensaje);
					}
				}
				else
				{
					// SI EXISTE, SE INFORMA AL USUARIO PARA QUE INGRESE LA CLAVE NUEVAMENTE:
					$mensaje = $nombre_tipo.' con '.$clave_a_mostrar.' ya existe, ingrese una nueva clave, gracias.';
					$tipo_mensaje = 2;
					$_SESSION['campos_habilitados'] = false;

					$this->vista->editar($datos, null, null, $mensaje, null, null, null, null, $tipo_mensaje);
				}
			}
			else
			{
				// SI NO EXISTE, SE GUARDA:

				// SI SE INGRESA SATISFACTORIAMENTE EL Expediente
				if ($this->modelo->insertar($datos))
				{
					$mensaje = 'Su '.$nombre_tipo.' '.$clave_a_mostrar.' se ha ingresado con éxito.';
					$tipo_mensaje = 1;

					$this->listar($datos, $mensaje);// SE MUESTRA EL LISTADO DE EXPEDIENTES
				}
				else
				{
					$mensaje = 'Su '.$nombre_tipo.' '.$clave_a_mostrar.' no se ha ingresado.';
					$tipo_mensaje = 2;
					$_SESSION['campos_habilitados'] = false;

					$this->vista->editar($datos, null, null, $mensaje, null, null, null, null, $tipo_mensaje);
				}
			}
	    }
	}

	public function modificar()
	{
	    $datos = $_REQUEST;//SE RECIBEN LOS DATOS

	    $clave_a_mostrar = $datos['anio'].'-'.$datos['tipo'].'-'.$datos['numero'].'-'.$datos['cuerpo'].'-'.$datos['alcance'];

	    if ($datos['tipo'] == 'E')
			$nombre_tipo = 'Expediente';

	    if ($datos['tipo'] == 'N')
			$nombre_tipo = 'Nota';

	    if ($datos['tipo'] == 'R')
			$nombre_tipo = 'Recomendacion';

	    // SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO PREVIAMENTE
	    if ($this->modelo->verificarRegistroEntero())
	    {
			if ( isset($datos['fecha_entrada_expe']) && $this->esFechaValida($datos['fecha_entrada_expe']) )
			{
				$datos['fecha_entrada_expe'] = $this->modelo->formatearFechaMySQL($datos['fecha_entrada_expe']);
			}

			// SE MODIFICA EL EXPEDIENTE
			if ($this->modelo->modificar($datos))
			{
				$mensaje = 'Su '.$nombre_tipo.' '.$clave_a_mostrar.' se ha modificado con éxito.';
				$tipo_mensaje = 1;
			}
			else
			{
				$mensaje = 'Su '.$nombre_tipo.' '.$clave_a_mostrar.' no se ha modificado.';
				$tipo_mensaje = 2;
			}
	    }
	    else
	    {
			$mensaje = 'Su '.$nombre_tipo.' '.$clave_a_mostrar.' se ha modificado previamente.';
			$tipo_mensaje = 2;
		}

		$this->listar($datos, $mensaje, $tipo_mensaje);
	}

	public function eliminar()
	{
		$this->clave['anio']    = Validador::validarParametro('anio');
		$this->clave['tipo']    = Validador::validarParametro('tipo');
		$this->clave['numero']  = Validador::validarParametro('numero');
		$this->clave['cuerpo']  = Validador::validarParametro('cuerpo');
		$this->clave['alcance'] = Validador::validarParametro('alcance');

	    $this->clave['fecha_entrada_expe'] = Validador::validarParametro('fecha_entrada_expe');//PARA REGISTRAR EN auditoria

	    $clave_a_mostrar = $this->clave['anio'].'-'.$this->clave['tipo'].'-'.$this->clave['numero'].'-'.$this->clave['cuerpo'].'-'.$this->clave['alcance'];

	    if ($this->clave['tipo'] == 'E')
			$nombre_tipo = 'Expediente';

	    if ($this->clave['tipo'] == 'N')
			$nombre_tipo = 'Nota';

	    if ($this->clave['tipo'] == 'R')
			$nombre_tipo = 'Recomendacion';

	    if ( !$this->modelo->verificarParentesco($this->clave) )
	    {
			//SI NO POSEE RELACIÓN CON OTROS EXPEDIENTES
			if ($this->modelo->eliminar($this->clave)) {//SE LO ELIMINA

				$mensaje = 'Su '.$nombre_tipo.' '.$clave_a_mostrar.' se ha eliminado con éxito.';
				$tipo_mensaje = 1;
				$_SESSION['agregado_previamente'] = false;
			}else{
				$mensaje = 'Su '.$nombre_tipo.' '.$clave_a_mostrar.' no se ha eliminado.';
				$tipo_mensaje = 2;
			}
	    }
	    else
	    {
			$mensaje = 'Su '.$nombre_tipo.' '.$clave_a_mostrar.' no puede eliminarse, está relacionado/a con otro Expediente o Nota.';
			$tipo_mensaje = 2;
	    }
	    // 17/05/2012	BANDERA PARA EL METODO $this->listar()
	    $this->clave['eliminado'] = true;

	    $this->listar($this->clave, $mensaje, $tipo_mensaje);
	}

	public function obtenerTemasPropios($filtro)
	{
	    //Se establece el filtro en el modelo
	    $this->modeloTemas->setFiltro($this->filtro);

	    //Se le pide al modelo todos los items
	    $listado = $this->modeloTemas->listadoPorExpediente();

	    return $listado;
	}

	public function obtenerAutoresPropios($filtro)
	{
	    //Se establece el filtro en el modelo de Autores
	    $this->modeloAutores->setFiltro($this->filtro);

	    //Se obtienen los Autores para dicho Expediente
	    $listado = $this->modeloAutores->listadoPorExpediente();

	    return $listado;
	}

	public function eliminarTema()
	{
		$this->clave['anio']       = Validador::validarParametro('anio');
		$this->clave['tipo']       = Validador::validarParametro('tipo');
		$this->clave['numero']     = Validador::validarParametro('numero');
		$this->clave['cuerpo']     = Validador::validarParametro('cuerpo');
		$this->clave['alcance']    = Validador::validarParametro('alcance');
		$this->clave['id_codtema'] = Validador::validarParametro('id_codtema');

	    if ($this->modeloTemas->eliminar($this->clave)){
			$mensaje = 'El Tema se elimin&oacute; con &eacute;xito';
	    }else{
			$mensaje = 'Error al eliminar el Tema';
			error($mensaje); // NUEVO
	    }
	    $this->editar();//$mensaje
	}

	public function eliminarAutor()
	{
		$this->clave['anio']         = Validador::validarParametro('anio');
		$this->clave['tipo']         = Validador::validarParametro('tipo');
		$this->clave['numero']       = Validador::validarParametro('numero');
		$this->clave['cuerpo']       = Validador::validarParametro('cuerpo');
		$this->clave['alcance']      = Validador::validarParametro('alcance');
		$this->clave['autor_tipo']   = Validador::validarParametro('autor_tipo');
		$this->clave['autor_codigo'] = Validador::validarParametro('autor_codigo');

	    if ($this->modeloAutores->eliminar($this->clave)){
			$mensaje = 'El Autor se elimin&oacute; con &eacute;xito';
	    }else{
			$mensaje = 'Error al eliminar el Autor';
			error($mensaje); // NUEVO
	    }
	    $this->editar();//$mensaje
	}

	public function verDatosInferior()
	{
		$this->filtro['anio']    = Validador::validarParametro('anio');
		$this->filtro['tipo']    = Validador::validarParametro('tipo');
		$this->filtro['numero']  = Validador::validarParametro('numero');
		$this->filtro['cuerpo']  = Validador::validarParametro('cuerpo');
		$this->filtro['alcance'] = Validador::validarParametro('alcance');

	    // Se establece el filtro en el modelo
	    $this->modelo->setFiltro($this->filtro);

	    // SE OBTIENEN ALGUNOS DATOS DEL EXPEDIENTE
	    $listado = $this->modelo->obtenerDatosExped();

	    // SE OBTIENE EL Estado
	    $estado = $this->modelo->obtenerEstado();

	    /* NUEVO 30/09/2011 *
	    1 = PARA CARGAR
	    2 = CARGADO
	    3 = SIN CARGAR
	    /**/
	    // SE VERIFICA EL ESTADO DEL PROYECTO (ARCHIVO original.doc)
	    $estado_doc = $this->verificarEstadoDoc($this->filtro);

	    // Se obtienen los Autores para dicho Expediente
	    $listadoAutores = $this->obtenerAutoresPropios($this->filtro);

	    // Se obtienen los Temas para dicho Expediente
	    $listadoTemas = $this->obtenerTemasPropios($this->filtro);

	    // Se obtienen los Proyectos para dicho Expediente
	    $listadoProyectos = $this->modelo->obtenerProyectosExped();

	    $datos_expediente = Array('caratula' => $listado[0]['caratula'],
								  'iniciador_codigo' => $listado[0]['codigo_grp'],
								  'iniciador_descripcion' => $listado[0]['descripcion_grp'],
								  'id_codcategoria' => $listado[0]['id_codcategoria'],
								  'descripcion_categoria' => $listado[0]['descripcion_categoria'],
								  'estado_doc' => $estado_doc,
								  'codigo_estado' => $estado[0]['codigo_estado'],
								  'nombre_estado' => $estado[0]['nombre_estado'],
								  'codigo_usuario' => $listado[0]['codigo_usuario'],
								  'autores' => $listadoAutores,
								  'temas' => $listadoTemas,
								  'proyectos' => $listadoProyectos,
								  'observaciones_expe' => $listado[0]['observaciones_expe']
								 );

		// 2020/05/07 XXXX
		// Se obtiene el estado de existencia de su Digitalización
	    $datos_expediente['estado_digitalizacion'] = $this->verificarEstadoDigitalizacion($listado[0]);
	    // Se setea si la digitalización está completa o no
	    $datos_expediente['digi_completa'] = $listado[0]['digi_completa'];

	    // SE OBTIENEN LOS id DE LOS ESTADOS 3, 16 y 79 (PUEDEN SER DIFERENTES AL codigo_estado)
	    $id_estadoA = $this->modelo->obtenerIdCodEstadoSegunCodigo(3);
	    $id_estadoB = $this->modelo->obtenerIdCodEstadoSegunCodigo(16);
	    $id_estadoC = $this->modelo->obtenerIdCodEstadoSegunCodigo(79);

	    // SI EL Estado ES 3, 16 ó 79 SE OBTIENE LA Comision
	    if ( ($estado[0]['codigo_estado'] == $id_estadoA ) OR ($estado[0]['codigo_estado'] == $id_estadoB ) OR ($estado[0]['codigo_estado'] == $id_estadoC ) )
	    {
			$comision = $this->modelo->obtenerComision($listado);

			$datos_expediente['codigo_grp']      = $comision[0]['codigo_grp'];
			$datos_expediente['descripcion_grp'] = $comision[0]['descripcion_grp'];
	    }

	    //SE MUESTRAN LOS DATOS DEL EXPEDIENTE
	    $this->vista->mostrarDatosExped($datos_expediente);
	}

    // XXXX: Realizado el 15/11/2011
    // XXXX: Optimizado el 19/01/2017
    // XXXX: Optimizado el 08/02/2017 con el uso de la función 'scandir' de PHP
    // XXXX: proyecto CARGADO se considera que posea por lo menos un documento de cualquier extensión permitida
    // y siempre y cuando NO sea su Digitalización (AAENNNNN.pdf)
    public function verificarEstadoDoc($expediente)
    {
		$anio_corto = substr($expediente['anio'], -2);
		$tipo       = $expediente['tipo'];
		$aux_numero = 100000+$expediente['numero'];
		$numero     = substr($aux_numero, -5);

		$nombre_codificado = $anio_corto.$tipo.$numero;

		$directorio_remoto = self::RUTA_DIRECTORIO_PROYECTOS.$expediente['anio']."/".$nombre_codificado."/";

		$estado_doc = 3;// Estado 'SIN CARGAR' por defecto

		// Primero se verifica si se encuentra el documento en el directorio '/temporal'
		if ( file_exists("../proyectos/temporal/".$nombre_codificado.".doc") ||
			 file_exists("../proyectos/temporal/".$nombre_codificado.".docx") ||
			 file_exists("../proyectos/temporal/".$nombre_codificado.".odt")
		   ) {
		    $estado_doc = 1;// Estado 'PARA CARGAR'
		}

		// Sino se verifica si existe el directorio remoto determinado por la clave del expediente respectivo
		else if (is_dir($directorio_remoto)) {
		    // Se 'escanea' dicho directorio, se obtiene un array de los archivos que contiene
		    $archivos = @scandir($directorio_remoto);

		    // A partir de la fecha,  2020/05/07 (XXXX)
		    // Si posee por lo menos un archivo, ya se considera que el proyecto está Cargado
		    // y que NO sea el AAENNNNN.pdf (su Digitalización)
		    if ( count($archivos) > 2 ) {
		    	// Se verifica
		    	foreach ($archivos as $a) {
		    		// Si NO es el AAENNNNN.pdf (su Digitalización)
		    		if ( $a != '.' && $a != '..' && $a != $nombre_codificado.'.pdf') {
						// Estado 'CARGADO'
		        		$estado_doc = 2;
		        		// Se sale del foreach
		        		break;
		        	}
		    	}
		    }
		}

		return $estado_doc;
	}

    // 2020/05/07 XXXX
    // Se verifica el estado de la Digitalización de un expediente determinado
    public function verificarEstadoDigitalizacion($expediente)
    {
		$anio_corto = substr($expediente['anio'], -2);
		$tipo       = $expediente['tipo'];
		$aux_numero = 100000+$expediente['numero'];
		$numero     = substr($aux_numero, -5);

		$nombre_codificado = $anio_corto.$tipo.$numero;

		$directorio_remoto = self::RUTA_DIRECTORIO_PROYECTOS.$expediente['anio']."/".$nombre_codificado."/";

		$estado_digitalizacion = 3;// Estado 'SIN CARGAR' por defecto

		// Primero se verifica si se encuentra el documento en el directorio '/digital'
		if ( file_exists("../proyectos/digital/".$nombre_codificado.".pdf") )
		    $estado_digitalizacion = 1;// Estado 'PARA CARGAR'

		// Se verifica si existe el archivo AATNNNNN.pdf
		if ( file_exists($directorio_remoto.$nombre_codificado.".pdf") )
		    $estado_digitalizacion = 2;// Estado 'CARGADO'

		return $estado_digitalizacion;
	}

    public function agregarTema()
	{
		$datos['anio']       = Validador::validarParametro('anio');
		$datos['tipo']       = Validador::validarParametro('tipo');
		$datos['numero']     = Validador::validarParametro('numero');
		$datos['cuerpo']     = Validador::validarParametro('cuerpo');
		$datos['alcance']    = Validador::validarParametro('alcance');
		$datos['id_codtema'] = Validador::validarParametro('id_codtema');
		$datos['id_usuario'] = Validador::validarParametro('id_usuario');

		if ( !$this->modeloTemas->agregar($datos) )
			$mensaje = 'Ya se ha agregado el Tema con c&oacute;digo '.$datos['id_codtema'];

		//Se establece el filtro en el modelo
		$this->modeloTemas->setFiltro($datos);
		//Se le pide al modelo todos los items
		$listado = $this->modeloTemas->listadoPorExpediente();

		$this->vista->listarTemas($listado, $mensaje);
    }

    public function listarTemas()
	{
		$datos = Array();
		$datos['anio']    = Validador::validarParametro('anio');
		$datos['tipo']    = Validador::validarParametro('tipo');
		$datos['numero']  = Validador::validarParametro('numero');
		$datos['cuerpo']  = Validador::validarParametro('cuerpo');
		$datos['alcance'] = Validador::validarParametro('alcance');

		//Se establece el filtro en el modelo
		$this->modeloTemas->setFiltro($datos);
		//Se le pide al modelo todos los items
		$listado = $this->modeloTemas->listadoPorExpediente();

		$this->vista->listarTemas($listado);
    }

    public function agregarAutor()
	{
		$datos['anio']         = Validador::validarParametro('anio');
		$datos['tipo']         = Validador::validarParametro('tipo');
		$datos['numero']       = Validador::validarParametro('numero');
		$datos['cuerpo']       = Validador::validarParametro('cuerpo');
		$datos['alcance']      = Validador::validarParametro('alcance');
		$datos['autor_tipo']   = Validador::validarParametro('autor_tipo');
		$datos['autor_codigo'] = Validador::validarParametro('autor_codigo');
		$datos['id_usuario']   = Validador::validarParametro('id_usuario');

		if ( !$this->modeloAutores->agregar($datos) )
		{
			$mensaje = 'Ya se ha agregado el Autor '.$datos['autor_codigo'];
		}
		//Se establece el filtro en el modelo
		$this->modeloAutores->setFiltro($datos);
		//Se le pide al modelo todos los items
		$listado = $this->modeloAutores->listadoPorExpediente();

		$this->vista->listarAutores($listado, $mensaje);
    }

    public function listarAutores()
	{
		$datos['anio'] = Validador::validarParametro('anio');
		$datos['tipo'] = Validador::validarParametro('tipo');
		$datos['numero'] = Validador::validarParametro('numero');
		$datos['cuerpo'] = Validador::validarParametro('cuerpo');
		$datos['alcance'] = Validador::validarParametro('alcance');

		//Se establece el filtro en el modelo
		$this->modeloAutores->setFiltro($datos);
		//Se le pide al modelo todos los items
		$listado = $this->modeloAutores->listadoPorExpediente();

		$this->vista->listarAutores($listado);
    }

    public function buscarNombreIniciador()
	{
		$iniciador_tipo   = Validador::validarParametro('iniciador_tipo');
		$iniciador_codigo = Validador::validarParametro('iniciador_codigo');
		$para_giro        = Validador::validarParametro('para_giro');

		$descripcion = $this->modeloLugares->buscarNombreIniciador($iniciador_tipo, $iniciador_codigo, $para_giro);

		if ( strlen($descripcion) > 21 )
			echo "{'descripcion': '".cortar_string($descripcion, 19)."'}"; //substr($descripcion, 0, 19) ...
		else
			echo "{'descripcion': '".$descripcion."'}";
    }

    public function buscarCodigoNombreIniciador()
	{
		$iniciador_tipo   = Validador::validarParametro('iniciador_tipo');
		$iniciador_codigo = Validador::validarParametro('iniciador_codigo');
		$para_giro        = Validador::validarParametro('para_giro');

		$iniciador = $this->modeloLugares->buscarCodigoNombreIniciador($iniciador_tipo, $iniciador_codigo, $para_giro);

		if (strlen($iniciador[0]['descripcion_grp']) > 21)
		{
			echo "{'codigo':'".$iniciador[0]['codigo_grp']."', 'descripcion': '".cortar_string($iniciador[0]['descripcion_grp'], 19)."'}"; //substr($iniciador[0]['descripcion_grp'], 0, 19) ...
		}
		else
		{
			echo "{'codigo':'".$iniciador[0]['codigo_grp']."', 'descripcion': '".$iniciador[0]['descripcion_grp']."'}";
		}
    }

    public function buscarNombreAutor()
	{
		$autor_tipo   = Validador::validarParametro('autor_tipo');
		$autor_codigo = Validador::validarParametro('autor_codigo');

		$descripcion = $this->modeloAutores->buscarNombreAutor($autor_tipo, $autor_codigo);

		if (strlen($descripcion) > 21){
			echo "{'descripcion': '".cortar_string($descripcion, 19)."'}"; //substr($descripcion, 0, 19) ...
		}else{
			echo "{'descripcion': '".$descripcion."'}";
		}
    }

    public function buscarCodigoNombreAutor()
	{
		$autor_tipo = Validador::validarParametro('autor_tipo');
		$autor_codigo = Validador::validarParametro('autor_codigo');

		$autor = $this->modeloAutores->buscarCodigoNombreAutor($autor_tipo, $autor_codigo);

		if ( strlen($autor[0]['descripcion_autor']) > 21 ) {
			echo "{'codigo':'".$autor[0]['codigo_autor']."', 'descripcion': '".cortar_string($autor[0]['descripcion_autor'], 19)."'}"; //substr($autor[0]['descripcion_autor'], 0, 19) ...
		} else {
			echo "{'codigo':'".$autor[0]['codigo_autor']."', 'descripcion': '".$autor[0]['descripcion_autor']."'}";
		}
    }

    public function setearNumeroSgte()
    {
		$anio = Validador::validarParametro('anio');
		$tipo = Validador::validarParametro('tipo');

		$numero = $this->modelo->setearNumeroSgte($anio, $tipo);

		echo "{'numero': '".$numero."'}";
    }

    public function agregarAutores()
    {
		$datos = $_REQUEST;//SE RECIBEN LOS DATOS

		$this->vista->agregarAutores($datos);
    }
}
?>

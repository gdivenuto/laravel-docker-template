<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//Incluye el modelo que corresponde
require 'modelos/codareas.php';

//Incluye la vista que corresponde
require 'vistas/codareas.php';

class codareas_controller extends ControllerBase
{
	private $rango_paginador;
	private $mensaje;
	private $tipo_mensaje;

	public function __construct()
	{
		parent::__construct();
	
		$this->rango_paginador = 18;
		 
		// Se crea una instancia del modelo
		$this->modelo = new codareasModel();
	
		// Se crea una instancia de la Vista
		$this->vista = new VistaAreas();
	
		// Se inicializa el mensaje de resultados
		$this->mensaje = "";
	}
	
	public function guardarRegistroOriginal($original)
    {
		$_SESSION['ca_id_original']         = $original['ca_id'];
		$_SESSION['ca_nombre_original']     = $original['ca_nombre'];
		$_SESSION['ca_tipo_original']       = $original['ca_tipo'];
		$_SESSION['ca_depende_de_original'] = $original['ca_depende_de'];
		$_SESSION['ca_mail_original']       = $original['ca_mail'];
		$_SESSION['ca_telefono_original']   = $original['ca_telefono'];
		$_SESSION['ca_habilitado_original'] = $original['ca_habilitado'];
    }
	
    public function listar($mensaje = '', $tipo_mensaje = '')
	{
		$filtro = Array();
				
		// FILTRO POR CODIGO
		$f_codigo = Validador::validarParametro('f_codigo');
		$filtro['codigo'] = ( $f_codigo != '' ) ? $f_codigo : '';
		
		// FILTRO POR NOMBRE
		$f_nombre = Validador::validarParametro('f_nombre');
		$filtro['nombre'] = ( $f_nombre != '' ) ? $f_nombre : '';
		
		// SE SETEA EL CAMPO POR EL CUAL SE ORDENA
		$campo_orden = Validador::validarParametro('campo_orden');
		if ( !empty($campo_orden) )
		{
			$filtro['campo_orden'] = $campo_orden;
		}
		else
		{
			//por defecto
			$filtro['campo_orden'] = 'ca_id';
			$_SESSION['ultimo_campo'] = '';
		}
		
		//DIRECCION PARA LA PAGINACION (PRIMERO, ANTERIOR, SGTE., ULTIMO)
		$filtro['sentido'] = Validador::validarParametro('sentido');
		
		if ( !isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $filtro['campo_orden'] )
		{
			// Si es la primera vez que carga la pagina
			// o se esta cambiando el campo por el que se ordena
			$_SESSION['ultimo_campo'] = $filtro['campo_orden'];
			$_SESSION['ultimo_sentido'] = 'asc';
		}
		else
		{
			// Si se hizo clic en el mismo que ya estaba ordenado antes, solo hay que cambiar el sentido:
			$_SESSION['ultimo_sentido'] = ( $_SESSION['ultimo_sentido'] == 'asc' && $filtro['sentido'] == '' ) ? 'desc' : 'asc';
		}
		
		$filtro['rango'] = $this->rango_paginador;	
		$filtro['pagina'] = Validador::validarParametro('pagina');	//se obtiene el valor de la pagina
		
		if ( !$filtro['pagina'] )
		{	//al comienzo no se sabe el valor de la pagina
			$filtro['inicio'] = 0;	//por lo tanto se inicia en el primer registro
			$filtro['pagina'] = 1;	//con la primer pagina 
		}
		else
		{	//sino se calcula el valor del registro inicial de la pagina deseada
			if ( $filtro['valor_buscado'] == '' )
			{	//si no se busca
				$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
			}
		} 
		$filtro['pagina_ant'] = $filtro['pagina'] - 1;		//para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;		//para la pagina posterior

		// Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
		$filtro['cantidad'] = $this->modelo->obtenerCantidad();
		
		// NUMERO TOTAL DE PAGINAS 
		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		// SI SE LLEGÓ MEDIANTE EL TECLADO AL RECORRER EL LISTADO
		$filtro['por_teclado'] = Validador::validarParametro('por_teclado');

		// SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
		$_SESSION['filtro_codarea'] = $filtro;
			
		// Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);
				
		// SE OBTIENE EL LISTADO
		$listado = $this->modelo->listar();
		
		// Se muestra el listado
		$this->vista->listar($listado, $mensaje, $tipo_mensaje, $filtro);
    }

    public function editar()
	{    
		$ca_id = Validador::validarParametro('ca_id');
		$filtro['pagina'] = Validador::validarParametro('pagina');
		$filtro['mostrar_todos'] = Validador::validarParametro('mostrar_todos');
				
		//Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);
			
		// SE OBTIENE EL REGISTRO
		$datos = $this->modelo->obtenerRegistro($ca_id);
		
		// SE GUARDA EL REGISTRO EN SESION PARA VERIFICAR LUEGO SI LO HA MODIFICADO OTRO USUARIO
		$this->guardarRegistroOriginal($datos);

		$this->vista->editar($datos, $filtro);
    }
	
    public function agregar()
	{    
		$this->vista->editar(null);
    }

    public function insertar()
	{    
		$datos = $_REQUEST;

		// SI NO EXISTE, PARA QUE DOS USUARIOS NO INGRESEN EL MISMO REGISTRO
		if ( $this->modelo->existe($datos['ca_id']) )
			$this->listar("El &Aacute;rea ".$datos['ca_id']." se ha ingresado previamente.", 2);	
		else {
			if ($this->modelo->insertar($datos))
				$this->listar("El &Aacute;rea ".$datos['ca_id']." se agregó con éxito.", 1);
			else
				$this->listar("Error al agregar el &Aacute;rea ".$datos['ca_id'], 2);
		}
		
    }

    public function modificar()
	{	
		$datos = $_REQUEST;
		
		// SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO PREVIAMENTE
		if ( $this->modelo->verificarRegistroEntero() ) {
			if ( $this->modelo->modificar($datos) )
				$this->listar("El &Aacute;rea se modificó con éxito.", 1);
			else
				$this->listar("Error al modificar el &Aacute;rea.", 2);
		}
		else
			$this->listar("El registro se ha modificado previamente.", 2);
    }
    
    public function eliminar()
	{    
		$ca_id = Validador::validarParametro('ca_id');
		
		if ($this->modelo->eliminar($ca_id))
			$this->listar("El &Aacute;rea se elimin&oacute; con &eacute;xito.", 1);
		else
			$this->listar("El &Aacute;rea no se ha eliminado, debe estar asignada a un Legajo.", 2);
    }
    
    public function listarModal()
	{
		$listado = $this->modelo->listadoCombo();
		
		$this->vista->listarModal($listado);
    }
    
    public function buscarDescripcionCodArea()
	{
		$ca_depende_de = Validador::validarParametro('ca_depende_de');

		$area_que_depende = $this->modelo->buscarDescripcionCodArea($ca_depende_de);
		
        echo "{'id_area':'".$area_que_depende['ca_id']."', 'descripcion_area':'".$area_que_depende['ca_nombre']."'}";
    }
}
?>
<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//Incluye el modelo que corresponde
require 'modelos/codcargos.php';

//Incluye la vista que corresponde
require 'vistas/codcargos.php';

class codcargos_controller extends ControllerBase
{
	private $rango_paginador;
	private $mensaje;
	private $tipo_mensaje;
	
	public function __construct()
	{
		parent::__construct();
	
		$this->rango_paginador = 18;
			
		// Se crea una instancia del modelo
		$this->modelo = new codcargosModel();
	
		// Se crea una instancia de la Vista
		$this->vista = new VistaCargos();
	
		// Se inicializa el mensaje de resultados
		$this->mensaje = "";
	}
	
	public function guardarRegistroOriginal($original)
    {
		$_SESSION['cc_nomenclador_original'] = $original['cc_nomenclador'];
		$_SESSION['cc_nombre_original'] = $original['cc_nombre'];
		$_SESSION['cc_tipo_original'] = $original['cc_tipo'];
		$_SESSION['cc_gente_a_cargo_original'] = $original['cc_gente_a_cargo'];
		$_SESSION['cc_modulo_original'] = $original['cc_modulo'];
		$_SESSION['cc_habilitado_original'] = $original['cc_habilitado'];
    }
	
    public function listar($mensaje = '', $tipo_mensaje = '')
    {
		$filtro = Array();
				
		// FILTRO POR NOMENCLADOR
		$f_nomenclador = Validador::validarParametro('f_nomenclador');
		if ( $f_nomenclador != '' )
		{
			$filtro['nomenclador'] = $f_nomenclador;
		}
		else
		{
			$filtro['nomenclador'] = '';
		}
		
		// FILTRO POR NOMBRE
		$f_nombre = Validador::validarParametro('f_nombre');
		if ( $f_nombre != '' )
		{
			$filtro['nombre'] = $f_nombre;
		}
		else
		{
			$filtro['nombre'] = '';
		}
		
		// SE SETEA EL CAMPO POR EL CUAL SE ORDENA
		$campo_orden = Validador::validarParametro('campo_orden');
		if ( !empty($campo_orden) )
		{
			$filtro['campo_orden'] = $campo_orden;
		}
		else
		{
			//por defecto
			$filtro['campo_orden'] = 'cc_nomenclador';
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
			if ( $_SESSION['ultimo_sentido'] == 'asc' && $filtro['sentido'] == '' )
			{
				$_SESSION['ultimo_sentido'] = 'desc';
			}
			else
			{
				$_SESSION['ultimo_sentido'] = 'asc';
			}
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

		//Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
		$filtro['cantidad'] = $this->modelo->obtenerCantidad();
		
		//NUMERO TOTAL DE PAGINAS 
		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		// SI SE LLEGÓ MEDIANTE EL TECLADO AL RECORRER EL LISTADO
		$filtro['por_teclado'] = Validador::validarParametro('por_teclado');	

		// SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
		$_SESSION['filtro_codcargo'] = $filtro;
		//fputs(fopen("session_filtro_codcargo.txt", 'w'), print_r($_SESSION['filtro_codcargo'], true));
				
		// Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);
					
		// SE OBTIENE EL LISTADO
		$listado = $this->modelo->listar();
		
		//se muestra el listado
		$this->vista->listar($listado, $mensaje, $tipo_mensaje, $filtro);
    }

    public function editar()
	{    
		$cc_nomenclador = Validador::validarParametro('cc_nomenclador');
		$filtro['pagina'] = Validador::validarParametro('pagina');
		$filtro['mostrar_todos'] = Validador::validarParametro('mostrar_todos');
				
		// Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);
			
		// SE OBTIENE EL REGISTRO
		$datos = $this->modelo->obtenerRegistro($cc_nomenclador);

		// SE GUARDA EL REGISTRO EN SESION PARA VERIFICAR LUEGO SI LO HA MODIFICADO OTRO USUARIO
		$this->guardarRegistroOriginal($datos);

		$this->vista->editar($datos, $filtro);
    }
	
    public function agregar()
	{    
		$this->vista->editar(null);
    }

	private function completarParteNomenclador($parte_nomenclador)
	{
		if ( $parte_nomenclador < 10 )
		{
			$parte_nomenclador = substr(($parte_nomenclador+100), -2);
		}
		
		return $parte_nomenclador;
	}
	
    public function insertar()
	{
		$post = $_REQUEST;
		
		// SE ARMA EL NUMERO DEL NOMENCLADOR RECIBIDO EN LA VISTA
		$cc_parte_nomenclador_0 = $this->completarParteNomenclador($post['cc_parte_nomenclador_0']);
		$cc_parte_nomenclador_1 = $this->completarParteNomenclador($post['cc_parte_nomenclador_1']);
		$cc_parte_nomenclador_2 = $this->completarParteNomenclador($post['cc_parte_nomenclador_2']);
		$cc_parte_nomenclador_3 = $this->completarParteNomenclador($post['cc_parte_nomenclador_3']);
	
		$post['cc_nomenclador'] = $cc_parte_nomenclador_0.$cc_parte_nomenclador_1.$cc_parte_nomenclador_2.$cc_parte_nomenclador_3;
		//fputs(fopen('post_cc_nomenclador.txt','w'),print_r($post['cc_nomenclador'], true));
		
		//PARA QUE DOS USUARIOS NO INGRESEN EL MISMO REGISTRO
		if ( $this->modelo->existe($post['cc_nomenclador']) )
		{ 	
			$mensaje = 'El registro se ha ingresado previamente.';
			$tipo_mensaje = 2;
		}
		else
		{
			if ($this->modelo->insertar($post))
			{
				$mensaje = 'El Cargo se agreg&oacute; con &eacute;xito.';
				$tipo_mensaje = 1;
			}
			else
			{
				$mensaje = 'Error al agregar el Cargo.';
				$tipo_mensaje = 2;
			}
		}
		
		$this->listar($mensaje, $tipo_mensaje);
    }

    public function modificar()
	{	
		$post = $_REQUEST;
		//fputs(fopen('post_recibido_modificarCargo_CONTROLLER.txt','w'),print_r($post, true));
		
		// SE ARMA EL NUMERO DEL NOMENCLADOR RECIBIDO EN LA VISTA
		$cc_parte_nomenclador_0 = $this->completarParteNomenclador($post['cc_parte_nomenclador_0']);
		$cc_parte_nomenclador_1 = $this->completarParteNomenclador($post['cc_parte_nomenclador_1']);
		$cc_parte_nomenclador_2 = $this->completarParteNomenclador($post['cc_parte_nomenclador_2']);
		$cc_parte_nomenclador_3 = $this->completarParteNomenclador($post['cc_parte_nomenclador_3']);
	
		$post['cc_nomenclador'] = $cc_parte_nomenclador_0.$cc_parte_nomenclador_1.$cc_parte_nomenclador_2.$cc_parte_nomenclador_3;
		//fputs(fopen('post_cc_nomenclador.txt','w'),print_r($post['cc_nomenclador'], true));
		
		// SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO PREVIAMENTE
		if ( $this->modelo->verificarRegistroEntero() )
		{
			/* 13/08/2012 *
			// SI SE DESEA DESHABILITAR EL CARGO
			if ( $post['cc_habilitado'] == 0 )
			{		
				// SE VERIFICA SI ESTÁ SIENDO UTILIZADO
				if ( $this->modelo->verificarUsoCargo($post['cc_nomenclador']) )
				{
					$modificable = false;
				}
				else
				{
					$modificable = true;
				}
			}
			else // ESTA HABILITADO
			{
				$modificable = true;
			}
			
			if ( $modificable )
			{
			/**/ 
				if ( $this->modelo->modificar($post) )
				{
					$mensaje = 'El Cargo se modific&oacute; con &eacute;xito.';
					$tipo_mensaje = 1;
				}
				else
				{
					$mensaje = 'Error al modificar el Cargo.';
					$tipo_mensaje = 2;
				}
			/**
			}
			else
			{
				$mensaje = 'El Cargo está siendo utilizado.';
				$tipo_mensaje = 2;
			}
			/**/ 
			
			$this->listar($mensaje, $tipo_mensaje);
		}
		else
		{
			$mensaje = 'El registro se ha modificado previamente.';
			$tipo_mensaje = 2;
			$this->listar($mensaje, $tipo_mensaje);
		}
    }
    
    public function eliminar()
	{    
		$cc_nomenclador = Validador::validarParametro('cc_nomenclador');
		
		if ( $this->modelo->eliminar($cc_nomenclador) )
		{
			$mensaje = 'El Cargo se elimin&oacute; con &eacute;xito.';
			$tipo_mensaje = 1;
		}
		else
		{
			$mensaje = 'El Cargo no se ha eliminado, debe estar asignado a un Legajo.';
			$tipo_mensaje = 2;
		}
		
		$this->listar($mensaje, $tipo_mensaje);
    }
    
    public function unirNumeroNomenclador($nomenclador)
    {
		// 01234567
		// 99999999
		$nomenclador[0] = substr($nomenclador, 0, 2);
		$nomenclador[1] = substr($nomenclador, 2, 2);
		$nomenclador[2] = substr($nomenclador, 4, 2);
		$nomenclador[3] = substr($nomenclador, 6, 2);
		
		return $nomenclador[0].'-'.$nomenclador[1].'-'.$nomenclador[2].'-'.$nomenclador[3];
	}
		
}
?>

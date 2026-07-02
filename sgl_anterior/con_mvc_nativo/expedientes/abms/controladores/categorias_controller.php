<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//Incluye el modelo que corresponde
require 'modelos/categorias.php';

//Incluye la vista que corresponde
require 'vistas/categorias.php';

class categorias_controller extends ControllerBase
{
	private $filtro = Array();
	private $eliminado = false;
	
	public function guardarRegistroOriginal($original)
	{
		$_SESSION['id_codcategoria_original'] = $original[0]['id_codcategoria'];
		$_SESSION['codigo_categoria_original'] = $original[0]['codigo_categoria'];
		$_SESSION['descripcion_categoria_original'] = $original[0]['descripcion_categoria'];
		$_SESSION['vigencia_desde_categoria_original'] = $original[0]['vigencia_desde_categoria'];
		$_SESSION['vigencia_hasta_categoria_original'] = $original[0]['vigencia_hasta_categoria'];
		$_SESSION['habilitado_categoria_original'] = $original[0]['habilitado_categoria'];
		$_SESSION['id_usuario_original'] = $original[0]['id_usuario'];
		//fputs(fopen('SESSION_Categorias.txt','w'),print_r($_SESSION, true));
	}
	
	public function listar($mensaje = '', $tipo_mensaje = '', $id = '', $orden = '', $pagina = '')
	{
		//Se crea una instancia del modelo
		$modelo = new categoriasModel();
		
		//se establece el valor a buscar en el modelo
		$valor_buscado = Validador::validarParametro('valor_buscado');
		if ( !empty($valor_buscado) )
		{
		    $this->filtro['valor_buscado'] = $valor_buscado;
		}
		else
		{
		    $this->filtro['valor_buscado'] = '';
		}
		//se establece el campo por el cual ordenar
		$campo_orden = Validador::validarParametro('campo_orden');
		if ( !empty($campo_orden) )
		{
		    $this->filtro['campo_orden'] = $campo_orden;
		}
		else
		{
		    //por defecto
		    $this->filtro['campo_orden'] = 'codigo_categoria';
		    $_SESSION['ultimo_campo'] = '';
		}
		
		if ($this->eliminado)
		{
		    $this->filtro['id'] = 0;
		}
		else
		{	
		    $this->filtro['id'] = Validador::validarParametro('id');
		    $this->filtro['boton'] = Validador::validarParametro('boton');
		}	
		
		$this->filtro['sentido'] = Validador::validarParametro('sentido');//DIRECCIÓN PARA LA PAGINACIÓN (PRIMERO, ANTERIOR, SGTE., ÚLTIMO)
		$this->filtro['por_teclado'] = Validador::validarParametro('por_teclado');// SI SE RECORRE POR TECLADO
		
		if ( !isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $this->filtro['campo_orden'] )
		{
		    // Si es la primera vez que carga la pagina
		    // o se esta cambiando el campo por el que se ordena
		    $_SESSION['ultimo_campo'] = $this->filtro['campo_orden'];
		    $_SESSION['ultimo_sentido'] = 'asc';
		}
		else
		{
		    // Si se hizo clic en el mismo que ya estaba ordenado antes
		    // Solo hay que cambiar el sentido:
		    if ($_SESSION['ultimo_sentido'] == 'asc' && $this->filtro['sentido'] == ''){
				$_SESSION['ultimo_sentido'] = 'desc';
		    }
		    else
		    {
				$_SESSION['ultimo_sentido'] = 'asc';
		    }
		}
		if ($orden != ''){
		    $_SESSION['ultimo_sentido'] = $orden;// 'Ascendente'
		}

		//se obtiene el valor de la pagina
		if ( $pagina != '' )
		{
		    $this->filtro['pagina'] = $pagina;	
		}
		elseif (Validador::validarParametro('pagina'))
		{
		    $this->filtro['pagina'] = Validador::validarParametro('pagina');
		}
		else
		{
		    $this->filtro['pagina'] = '';
		}
		
		$this->filtro['rango'] = 20;	//cantidad de registros a mostrar
		
		if ( $this->filtro['pagina'] == '' )
		{	//si no se sabe el valor de la pagina
		    $this->filtro['inicio'] = 0;	//se inicia en el primer registro
		    $this->filtro['pagina'] = 1;	//con la primer pagina 
		}
		else
		{	//si se conoce se calcula el valor del registro inicial de dicha pagina 
		    $this->filtro['inicio'] = ($this->filtro['pagina'] * $this->filtro['rango']) - $this->filtro['rango'];
		}
		$this->filtro['pagina_ant'] = $this->filtro['pagina'] - 1;  //para la pagina anterior
		$this->filtro['pagina_sgte'] = $this->filtro['pagina'] + 1; //para la pagina posterior
		
		//Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
		$this->filtro['cantidad'] = $modelo->obtenerCantidad();
		//NUMERO TOTAL DE PAGINAS 
		$this->filtro['nro_paginas'] = ceil($this->filtro['cantidad'] / $this->filtro['rango']);
		
		// 05/01/2012
		$this->filtro['mostrar_todos'] = Validador::validarParametro('mostrar_todos', 'si');
		
		//Se establece el filtro en el modelo
		$modelo->setFiltro($this->filtro);
		//Se le pide al modelo todos los items
		$listado = $modelo->listadoTotal();

		if ($id)
		{
		    $this->filtro['id'] = $id;
		}

		$vista = new VistaCategorias();
		$vista->listar($listado, $mensaje, $tipo_mensaje, $this->filtro);
	}
	 
	public function editar()
	{	
	    $id = Validador::validarParametro('id');
	    // 09/01/2012: SE AGREGO EL FILTRO PARA ACUMULAR PARAMETROS (pagina Y mostrar_todos)
	    $this->filtro['pagina'] = Validador::validarParametro('pagina');
	    $this->filtro['mostrar_todos'] = Validador::validarParametro('mostrar_todos');
			   
	    $modeloCategoria = new categoriasModel();
	    // SE OBTIENE EL REGISTRO
	    $listado = $modeloCategoria->obtenerRegistro($id);
	    
	    // SE GUARDA EL REGISTRO EN SESION PARA VERIFICAR LUEGO SI LO HA MODIFICADO OTRO USUARIO
	    $this->guardarRegistroOriginal($listado);

	    $vistaCategoria = new VistaCategorias();
	    $vistaCategoria->editar($listado, $this->filtro);
	}
	
	public function agregar()
	{
		$vistaCategoria = new VistaCategorias();
		$vistaCategoria->editar(null);
	}
	
	public function insertar()
	{	
		$post = $_REQUEST;
			
		//Se crea una instancia del modelo
		$modeloCategoria = new categoriasModel();
		
		if ( !$modeloCategoria->existe($post['codigo_categoria']) )
		{	//PARA QUE DOS USUARIOS NO INGRESEN EL MISMO REGISTRO
		
		    if ( empty($post['descripcion_categoria']) )
		    {
				$post['descripcion_categoria'] = null;
		    }
		    
		    if ( !empty($post['vigencia_desde_categoria']) && $this->esFechaValida($post['vigencia_desde_categoria']) )
		    {
				$post['vigencia_desde_categoria'] = $modeloCategoria->formatearFechaMySQL($post['vigencia_desde_categoria']);
		    }
		    else
		    {
				$post['vigencia_desde_categoria'] = null;
		    }
		    
		    if ( !empty($post['vigencia_hasta_categoria']) && $this->esFechaValida($post['vigencia_hasta_categoria']) )
		    {
				$post['vigencia_hasta_categoria'] = $modeloCategoria->formatearFechaMySQL($post['vigencia_hasta_categoria']);
		    }
		    else
		    {
				$post['vigencia_hasta_categoria'] = null;
		    }
		    
		    //fputs(fopen('post_insertarCategoriasC.txt','w'),print_r($post, true));
			    
		    if ($modeloCategoria->insertar($post))
		    {
				$mensaje = 'La Categoría se agregó con éxito.';
				$tipo_mensaje = 1;
		    }
		    else
		    {
				$mensaje = 'Error al agregar la Categoría.';
				$tipo_mensaje = 2;
		    }
		    $this->listar($mensaje, $tipo_mensaje);
		}
		else
		{
		    $mensaje = 'El registro se ha ingresado previamente.';
		    $tipo_mensaje = 2;
		    $this->listar($mensaje, $tipo_mensaje);
		}
		
	}
	
	public function modificar()
	{	
	    $post = $_REQUEST;
	    		
	    //Se crea una instancia del modelo
	    $modeloCategoria = new categoriasModel();
	    
	    // 	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO PREVIAMENTE
	    if ($modeloCategoria->verificarRegistroEntero())
		{    
			if (empty($post['descripcion_categoria'])){
				$post['descripcion_categoria'] = null;
			}
			
			if (!empty($post['vigencia_desde_categoria']) && $this->esFechaValida($post['vigencia_desde_categoria'])){
				$post['vigencia_desde_categoria'] = $modeloCategoria->formatearFechaMySQL($post['vigencia_desde_categoria']);
			}else{
				$post['vigencia_desde_categoria'] = null;
			}
		
			if ( !empty($post['vigencia_hasta_categoria']) && $this->esFechaValida($post['vigencia_hasta_categoria']) )
			{
				$post['vigencia_hasta_categoria'] = $modeloCategoria->formatearFechaMySQL($post['vigencia_hasta_categoria']);
			}
			else
			{
				$post['vigencia_hasta_categoria'] = null;
			}
			//fputs(fopen('postCategorias_al_modificar.txt','w'), print_r($post, true));
			
			if ( $modeloCategoria->modificar($post) )
			{
				$mensaje = 'La Categoría se modificó con éxito.';
				$tipo_mensaje = 1;
			}
			else
			{
				$mensaje = 'Error al modificar la Categoría.<br>';
				$tipo_mensaje = 2;
			}
			$this->listar($mensaje, $tipo_mensaje, $post['id_codcategoria'], 'ASC', $post['pagina']);
	    }
	    else
	    {
			$mensaje = 'El registro se ha modificado previamente.';
			$tipo_mensaje = 2;
			$this->listar($mensaje, $tipo_mensaje, $post['id_codcategoria'], 'ASC', $post['pagina']);
	    }
	}
	
	public function eliminar()
	{
	    $id = Validador::validarParametro('id');
	   
	    $modeloCategoria = new categoriasModel();
	    
	    if ( $modeloCategoria->eliminar($id) )
	    {
			$mensaje = 'La Categoría se eliminó con éxito.';
			$this->eliminado = true;
			$tipo_mensaje = 1;
	    }
	    else
	    {
			$mensaje = 'Error al eliminar la Categoría.';
			$tipo_mensaje = 2;
	    }
	    $this->listar($mensaje, $tipo_mensaje);
	}
	
	public function listarModal()
	{		
	    //Se crea una instancia del modelo
	    $modelo = new categoriasModel();
	    
	    //Se le pide al modelo todos los items
	    $listado = $modelo->listadoModal();
	    
	    //Se crea una instancia de la vista
	    $vista = new VistaCategorias();
	    
	    //se muestra la Ventana Modal
	    $vista->listarModal($listado);
	}
	
	public function pedirNombreModal()
	{
	    $c_solo_habilitado = Validador::validarParametro('c_solo_habilitado');
		
	    $modelo = new categoriasModel();
	    $listado = $modelo->listadoModal($c_solo_habilitado);
	    
	    $vista = new VistaCategorias();
	    $vista->pedirNombreModal($listado);
	}
		
}
?>

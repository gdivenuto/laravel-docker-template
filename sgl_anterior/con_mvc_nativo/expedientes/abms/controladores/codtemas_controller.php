<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//require '../../librerias/control_duracion_sesion.php';
//Incluye el modelo que corresponde
require 'modelos/codtemas.php';

//Incluye la vista que corresponde
require 'vistas/codtemas.php';

class codtemas_controller extends ControllerBase
{
	private $filtro = Array();
	private $eliminado = false;
	
	public function guardarRegistroOriginal($original)
	{
		$_SESSION['id_codtema_original'] = $original[0]['id_codtema'];
		$_SESSION['codigo_tema_original'] = $original[0]['codigo_tema'];
		$_SESSION['descripcion_tema_original'] = $original[0]['descripcion_tema'];
		$_SESSION['vigencia_desde_tema_original'] = $original[0]['vigencia_desde_tema'];
		$_SESSION['vigencia_hasta_tema_original'] = $original[0]['vigencia_hasta_tema'];
		$_SESSION['habilitado_tema_original'] = $original[0]['habilitado_tema'];
		$_SESSION['id_usuario_original'] = $original[0]['id_usuario'];
	
	}
	
	public function listar($mensaje = '', $tipo_mensaje = '', $id = '', $orden = '', $pagina = '')
	{
		//Se crea una instancia del modelo
		$modeloCodTema = new codtemasModel();
		
		//se establece el valor a buscar en el modelo
		if ($_GET['valor_buscado'] != ''){
		    $this->filtro['valor_buscado'] = Validador::validarParametro('valor_buscado');
		}else{
		    $this->filtro['valor_buscado'] = '';
		}
		//se establece el campo por el cual ordenar
		if ($_GET['campo_orden'] != ''){
		    $this->filtro['campo_orden'] = Validador::validarParametro('campo_orden');
		}else{
		    //por defecto
		    $this->filtro['campo_orden'] = 'codigo_tema';
		    $_SESSION['ultimo_campo'] = '';
		}

		if ($this->eliminado){
		    $this->filtro['id'] = 0;
		}else{	
		    $this->filtro['id'] = Validador::validarParametro('id');
		    $this->filtro['boton'] = Validador::validarParametro('boton');
		}	
		$this->filtro['sentido'] = Validador::validarParametro('sentido');//DIRECCIÓN PARA LA PAGINACIÓN (PRIMERO, ANTERIOR, SGTE., ÚLTIMO)
		
		
		if (!isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $this->filtro['campo_orden']) {
			// Si es la primera vez que carga la pagina
			// o se esta cambiando el campo por el que se ordena
			$_SESSION['ultimo_campo'] = $this->filtro['campo_orden'];
			$_SESSION['ultimo_sentido'] = 'asc';
		} else {
			// Si se hizo clic en el mismo que ya estaba ordenado antes
			// Solo hay que cambiar el sentido:
			if ($_SESSION['ultimo_sentido'] == 'asc' && $this->filtro['sentido'] == '') {
				$_SESSION['ultimo_sentido'] = 'desc';
			} else {
				$_SESSION['ultimo_sentido'] = 'asc';
			}
		}
		if ($orden != ''){
		    $_SESSION['ultimo_sentido'] = $orden;// 'Ascendente'
		}
		//se obtiene el valor de la pagina
		if ($pagina != ''){
		    $this->filtro['pagina'] = $pagina;	
		}elseif (Validador::validarParametro('pagina')){
		    $this->filtro['pagina'] = Validador::validarParametro('pagina');
		}else{
		    $this->filtro['pagina'] = '';
		}
		$this->filtro['rango'] = 21;	//cantidad de registros a mostrar
		if ($this->filtro['pagina'] == ''){	//si no se sabe el valor de la pagina
			$this->filtro['inicio'] = 0;	//se inicia en el primer registro
			$this->filtro['pagina'] = 1;	//con la primer pagina 
		}else{	//si se conoce se calcula el valor del registro inicial de dicha pagina 
			$this->filtro['inicio'] = ($this->filtro['pagina'] * $this->filtro['rango']) - $this->filtro['rango'];
		} 
		$this->filtro['pagina_ant'] = $this->filtro['pagina'] - 1;		//para la pagina anterior
		$this->filtro['pagina_sgte'] = $this->filtro['pagina'] + 1;		//para la pagina posterior
		//Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
		$this->filtro['cantidad'] = $modeloCodTema->obtenerCantidad();
		//NUMERO TOTAL DE PAGINAS (DE 12 expedientes CADA UNA)
		$this->filtro['nro_paginas'] = ceil($this->filtro['cantidad'] / $this->filtro['rango']);
		
		// 05/01/2012
		$this->filtro['mostrar_todos'] = Validador::validarParametro('mostrar_todos', 'si');
				
		//Se establece el filtro en el modelo
		$modeloCodTema->setFiltro($this->filtro);
		//Se le pide al modelo todos los items
		$listado = $modeloCodTema->listadoTotal();
		
		if ($id){
		    $this->filtro['id'] = $id;
		}
 		//Creamos una instancia de la "vista"
		$vistaCodTema = new VistaCodTemas();
		//se muestra el listado
		$vistaCodTema->listar($listado, $mensaje, $tipo_mensaje, $this->filtro);
	}
	 
	public function editar()
	{	
	    $id = Validador::validarParametro('id');
	    // 09/01/2012: SE AGREGO EL FILTRO PARA ACUMULAR PARAMETROS (pagina Y mostrar_todos)
	    $this->filtro['pagina'] = Validador::validarParametro('pagina');
	    $this->filtro['mostrar_todos'] = Validador::validarParametro('mostrar_todos');
			   
	    $modeloCodTema = new codtemasModel();
	   
	    // SE OBTIENE EL REGISTRO
	    $listado = $modeloCodTema->obtenerRegistro($id);
	    
	    // SE GUARDA EL REGISTRO EN SESION PARA VERIFICAR LUEGO SI LO HA MODIFICADO OTRO USUARIO
	    $this->guardarRegistroOriginal($listado);
	  
	    $vistaCodTema = new VistaCodTemas();
	    $vistaCodTema->editar($listado, $this->filtro);
	}
	
	public function agregar()
	{
		$vistaCodTema = new VistaCodTemas();
		$vistaCodTema->editar(null);
	}
	
	public function insertar()
	{
		$post = $_REQUEST;
		
		//Se crea una instancia del modelo
		$modeloCodTema = new codtemasModel();
			
		if ( !$modeloCodTema->existe($post['codigo_tema']) )
		{	//PARA QUE DOS USUARIOS NO INGRESEN EL MISMO REGISTRO
		
			if (empty($post['descripcion_tema'])){
				$post['descripcion_tema'] = null;
			}
			
			if (!empty($post['vigencia_desde_tema']) && $this->esFechaValida($post['vigencia_desde_tema'])){
				$post['vigencia_desde_tema'] = $modeloCodTema->formatearFechaMySQL($post['vigencia_desde_tema']);
			}else{
				$post['vigencia_desde_tema'] = null;
			}
			
			if (!empty($post['vigencia_hasta_tema']) && $this->esFechaValida($post['vigencia_hasta_tema'])){
				$post['vigencia_hasta_tema'] = $modeloCodTema->formatearFechaMySQL($post['vigencia_hasta_tema']);
			}else{
				$post['vigencia_hasta_tema'] = null;
			}
			
			if ($modeloCodTema->insertar($post)){
				$mensaje = 'El Tema se agregó con éxito.';
				$tipo_mensaje = 1;
			}else{
				$mensaje = 'Error al agregar el Tema';
				$tipo_mensaje = 2;
			}
			$this->listar($mensaje, $tipo_mensaje);
		}
		else
		{
			$mensaje = 'El registro se ha ingresado previamente';
			$tipo_mensaje = 2;
			$this->listar($mensaje, $tipo_mensaje);
		}	
	}
	
	public function modificar()
	{
		$post = $_REQUEST;
		
		//Se crea una instancia del modelo
		$modeloCodTema = new codtemasModel();
		
		// 	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO PREVIAMENTE
		if ($modeloCodTema->verificarRegistroEntero()){
		
		    if (empty($post['descripcion_tema'])){
			    $post['descripcion_tema'] = null;
		    }
		    
		    if (!empty($post['vigencia_desde_tema']) && $this->esFechaValida($post['vigencia_desde_tema'])){
			    $post['vigencia_desde_tema'] = $modeloCodTema->formatearFechaMySQL($post['vigencia_desde_tema']);
		    }else{
			    $post['vigencia_desde_tema'] = null;
		    }
		    
		    if (!empty($post['vigencia_hasta_tema']) && $this->esFechaValida($post['vigencia_hasta_tema'])){
			    $post['vigencia_hasta_tema'] = $modeloCodTema->formatearFechaMySQL($post['vigencia_hasta_tema']);
		    }else{
			    $post['vigencia_hasta_tema'] = null;
		    }
		    
		    if ($modeloCodTema->modificar($post)){
			    $mensaje = 'El Tema se modificó con éxito.';
			    $tipo_mensaje = 1;
		    }else{
			    $mensaje = 'Error al modificar el Tema.';
			    $tipo_mensaje = 2;
		    }
		    $this->listar($mensaje, $tipo_mensaje, $post['id_codtema'], 'asc', $post['pagina']);
		}else{
		    $mensaje = 'El registro se ha modificado previamente.';
		    $tipo_mensaje = 2;
		    $this->listar($mensaje, $tipo_mensaje, $post['id_codtema'], 'asc', $post['pagina']);
		}
	}
	
	public function eliminar()
	{
		$id = Validador::validarParametro('id');
		
		//Se crea una instancia del modelo
		$modeloCodTema = new codtemasModel();
 		
		if ($modeloCodTema->eliminar($id)){
		    $mensaje = 'El Tema se eliminó con éxito.';
		    $tipo_mensaje = 1;
		    $this->eliminado = true;
		}else{
		    $mensaje = 'No puede eliminar el Tema. Posee registros asociados con algún Expediente o Nota.';
		    $tipo_mensaje = 2;
		}
		
		$this->listar($mensaje, $tipo_mensaje);
	}
	
	public function listarModal()
	{
	    $filtro = Array();
				    
	    //Se crea una instancia del modelo
	    $modelo = new codtemasModel();
	    //Se establece el filtro en el modelo
	    $modelo->setFiltro($filtro);
	    //Se le pide al modelo todos los items
	    $listado = $modelo->listadoModal();
	    
	    //Creamos una instancia de la "vista"
	    $vista = new VistaCodTemas();
	    //se muestra la Ventana Modal
	    $vista->listarModal($listado);
	}
	
	public function pedirNombreModal()
	{
	    $c_solo_habilitado = Validador::validarParametro('c_solo_habilitado');
		
	    $modelo = new codtemasModel();
	    $listado = $modelo->listadoModal($c_solo_habilitado);
	    
	    $vista = new VistaCodTemas();
	    $vista->pedirNombreModal($listado);
	}
	
}
?>

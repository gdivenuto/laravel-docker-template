<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//require '../../librerias/control_duracion_sesion.php';
//Incluye el modelo que corresponde
require 'modelos/codproyectos.php';

//Incluye la vista que corresponde
require 'vistas/codproyectos.php';

class codproyectos_controller extends ControllerBase
{
	private $filtro = Array();
	private $eliminado = false;
	
	public function guardarRegistroOriginal($original)
	{
		$_SESSION['id_codproyecto_original'] = $original[0]['id_codproyecto'];
		$_SESSION['codigo_proyecto_original'] = $original[0]['codigo_proyecto'];
		$_SESSION['descripcion_proyecto_original'] = $original[0]['descripcion_proyecto'];
		$_SESSION['vigencia_desde_codproy_original'] = $original[0]['vigencia_desde_codproy'];
		$_SESSION['vigencia_hasta_codproy_original'] = $original[0]['vigencia_hasta_codproy'];
		$_SESSION['habilitado_codproy_original'] = $original[0]['habilitado_codproy'];
		$_SESSION['id_usuario_original'] = $original[0]['id_usuario'];
		//fputs(fopen('SESSIONRegistroOriginalCategoriasC.txt','w'),print_r($_SESSION,true));
	}
	
	public function listar($mensaje = '', $tipo_mensaje = '', $id = '', $orden = '', $pagina = '')
	{
		//Se crea una instancia del modelo
		$modeloCodProyecto = new codproyectosModel();
		
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
			$this->filtro['campo_orden'] = 'id_codproyecto';
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
							
		if (!$this->filtro['pagina']){	//si no se sabe el valor de la pagina
			$this->filtro['inicio'] = 0;	//se inicia en el primer registro
			$this->filtro['pagina'] = 1;	//con la primer pagina 
		}else{	//si se conoce se calcula el valor del registro inicial de dicha pagina 
			$this->filtro['inicio'] = ($this->filtro['pagina'] * $this->filtro['rango']) - $this->filtro['rango'];
		} 
		$this->filtro['pagina_ant'] = $this->filtro['pagina'] - 1;		//para la pagina anterior
		$this->filtro['pagina_sgte'] = $this->filtro['pagina'] + 1;		//para la pagina posterior
		
		//Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
		$this->filtro['cantidad'] = $modeloCodProyecto->obtenerCantidad();
		
		//NUMERO TOTAL DE PAGINAS 
		$this->filtro['nro_paginas'] = ceil($this->filtro['cantidad'] / $this->filtro['rango']);
		
		// 05/01/2012
		$this->filtro['mostrar_todos'] = Validador::validarParametro('mostrar_todos', 'si');
			
		//Se establece el filtro en el modelo
		$modeloCodProyecto->setFiltro($this->filtro);
 		//Se le pide al modelo todos los items
		$listado = $modeloCodProyecto->listadoTotal();
		
		if ($id){
		    $this->filtro['id'] = $id;
		}
 		//Creamos una instancia de la "vista"
		$vistaCodProyecto = new VistaCodProyectos();
		//se muestra el listado
		$vistaCodProyecto->listar($listado, $mensaje, $tipo_mensaje, $this->filtro);
	}
	 
	public function editar()
	{	
		$id = Validador::validarParametro('id');
		// 09/01/2012: SE AGREGO EL FILTRO PARA ACUMULAR PARAMETROS (pagina Y mostrar_todos)
	    $this->filtro['pagina'] = Validador::validarParametro('pagina');
	    $this->filtro['mostrar_todos'] = Validador::validarParametro('mostrar_todos');
			   		
		$modeloCodProyecto = new codproyectosModel();
		
		// SE OBTIENE EL REGISTRO
		$listado = $modeloCodProyecto->obtenerRegistro($id);
		
		// SE GUARDA EL REGISTRO EN SESION PARA VERIFICAR LUEGO SI LO HA MODIFICADO OTRO USUARIO
		$this->guardarRegistroOriginal($listado);

		$vistaCodProyecto = new VistaCodProyectos();
		$vistaCodProyecto->editar($listado, $this->filtro);
	}
	
	public function agregar()
	{
		$vistaCodProyecto = new VistaCodProyectos();
		$vistaCodProyecto->editar(null);
	}
	
	public function insertar()
	{
		$post = $_REQUEST;
		
		//Se crea una instancia del modelo
		$modeloCodProyecto = new codproyectosModel();
		
		if (!$modeloCodProyecto->existe($post['codigo_proyecto'])){ //PARA QUE DOS USUARIOS NO INGRESEN EL MISMO REGISTRO
		
			if (empty($post['descripcion_proyecto'])){
				$post['descripcion_proyecto'] = null;
			}
			
			if (!empty($post['vigencia_desde_codproy']) && $this->esFechaValida($post['vigencia_desde_codproy'])){
				$post['vigencia_desde_codproy'] = $modeloCodProyecto->formatearFechaMySQL($post['vigencia_desde_codproy']);
			}else{
				$post['vigencia_desde_codproy'] = null;
			}
			
			if (!empty($post['vigencia_hasta_codproy']) && $this->esFechaValida($post['vigencia_hasta_codproy'])){
				$post['vigencia_hasta_codproy'] = $modeloCodProyecto->formatearFechaMySQL($post['vigencia_hasta_codproy']);
			}else{
				$post['vigencia_hasta_codproy'] = null;
			}
		
			if ($modeloCodProyecto->insertar($post)){
			    $mensaje = 'El Código de Proyecto se agregó con éxito.';
			    $tipo_mensaje = 1;
			}else{
			    $mensaje = 'Error al agregar el Código de Proyecto.';
			    $tipo_mensaje = 2;
			}
			$this->listar($mensaje, $tipo_mensaje);
		}else{
			$mensaje = 'El registro se ha ingresado previamente.';
			$tipo_mensaje = 2;
			$this->listar($mensaje, $tipo_mensaje);
		}	
	}
	
	public function modificar()
	{
		$post = $_REQUEST;
		
		//Se crea una instancia del modelo
		$modeloCodProyecto = new codproyectosModel();
		
		// 	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO PREVIAMENTE
		if ($modeloCodProyecto->verificarRegistroEntero()){
			
			if (empty($post['descripcion_proyecto'])){
				$post['descripcion_proyecto'] = null;
			}
			
			if (!empty($post['vigencia_desde_codproy']) && $this->esFechaValida($post['vigencia_desde_codproy'])){
				$post['vigencia_desde_codproy'] = $modeloCodProyecto->formatearFechaMySQL($post['vigencia_desde_codproy']);
			}else{
				$post['vigencia_desde_codproy'] = null;
			}
			
			if (!empty($post['vigencia_hasta_codproy']) && $this->esFechaValida($post['vigencia_hasta_codproy'])){
				$post['vigencia_hasta_codproy'] = $modeloCodProyecto->formatearFechaMySQL($post['vigencia_hasta_codproy']);
			}else{
				$post['vigencia_hasta_codproy'] = null;
			}
				
			if ($modeloCodProyecto->modificar($post)){
				$mensaje = 'El Código de Proyecto se modificó con éxito.';
				$tipo_mensaje = 1;
			}else{
				$mensaje = 'Error al modificar el Código de Proyecto.';
				$tipo_mensaje = 2;
			}
			
			$this->listar($mensaje, $tipo_mensaje, $post['id_codproyecto'], 'ASC', $post['pagina']);
		}else{
			$mensaje = 'El registro se ha modificado previamente.';
			$tipo_mensaje = 2;      
			$this->listar($mensaje, $tipo_mensaje, $post['id_codproyecto'], 'ASC', $post['pagina']);
		}	
	}
	
	public function eliminar()
	{
		$id = Validador::validarParametro('id');
		
		//Se crea una instancia del modelo
		$modeloCodProyecto = new codproyectosModel();
 		
		if ($modeloCodProyecto->eliminar($id)){
			$mensaje = 'El Código de Proyecto se eliminó con éxito.';
			$tipo_mensaje = 1;
			$this->eliminado = true;
		}else{
			$mensaje = 'No es posible eliminar el Código de Proyecto. Posee registros asociados con algún Expediente o Nota.';
			$tipo_mensaje = 2;
		}
		
		$this->listar($mensaje, $tipo_mensaje);
	}
		
}
?>

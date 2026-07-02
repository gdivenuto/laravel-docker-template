<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//require '../../librerias/control_duracion_sesion.php';
//Incluye el modelo que corresponde
require 'modelos/codestados.php';

//Incluye la vista que corresponde
require 'vistas/codestados.php';

class codestados_controller extends ControllerBase
{
	private $filtro = Array();
	private $eliminado = false;
	
	public function guardarRegistroOriginal($original){
	
		$_SESSION['id_codestado_original'] = $original[0]['id_codestado'];
		$_SESSION['codigo_estado_original'] = $original[0]['codigo_estado'];
		$_SESSION['nombre_estado_original'] = $original[0]['nombre_estado'];
		$_SESSION['vigencia_desde_codestado_original'] = $original[0]['vigencia_desde_codestado'];
		$_SESSION['vigencia_hasta_codestado_original'] = $original[0]['vigencia_hasta_codestado'];
		$_SESSION['observaciones_codestado_original'] = $original[0]['observaciones_codestado'];
		$_SESSION['habilitado_codestado_original'] = $original[0]['habilitado_codestado'];
		$_SESSION['id_usuario_original'] = $original[0]['id_usuario'];
	
		//fputs(fopen('SESSIONRegistroOriginalCategoriasC.txt','w'),print_r($_SESSION,true));
	}
	
	public function listar($mensaje = '', $tipo_mensaje = '', $id = '', $orden = '', $pagina = '')
	{		
		//Se crea una instancia del modelo
		$modelo = new codestadosModel();
		
		//se establece el valor a buscar en el modelo
		$valor_buscado = Validador::validarParametro('valor_buscado');
		if ( !empty($valor_buscado) ){
		    $this->filtro['valor_buscado'] = $valor_buscado;
		}else{
		    $this->filtro['valor_buscado'] = '';
		}
		//se establece el campo por el cual ordenar
		$campo_orden = Validador::validarParametro('campo_orden');
		if (!empty($campo_orden)){
		    $this->filtro['campo_orden'] = $campo_orden;
		}else{
		    //por defecto
		    $this->filtro['campo_orden'] = 'codigo_estado';
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
		
		//se obtiene el valor de la pagina
		if ($pagina != ''){
		    $this->filtro['pagina'] = $pagina;	
		}elseif (Validador::validarParametro('pagina')){
		    $this->filtro['pagina'] = Validador::validarParametro('pagina');
		}else{
		    $this->filtro['pagina'] = '';
		}
		$this->filtro['rango'] = 15;	//cantidad de registros a mostrar
		if (!$this->filtro['pagina']){	//si no se sabe el valor de la pagina
			$this->filtro['inicio'] = 0;	//se inicia en el primer registro
			$this->filtro['pagina'] = 1;	//con la primer pagina 
		}else{	//si se conoce se calcula el valor del registro inicial de dicha pagina 
			$this->filtro['inicio'] = ($this->filtro['pagina'] * $this->filtro['rango']) - $this->filtro['rango'];
		} 
		$this->filtro['pagina_ant'] = $this->filtro['pagina'] - 1;		//para la pagina anterior
		$this->filtro['pagina_sgte'] = $this->filtro['pagina'] + 1;		//para la pagina posterior
		
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
		
		if ($id){
		    $this->filtro['id'] = $id;
		}
 		//Creamos una instancia de la "vista"
		$vistaCodestado = new VistaCodEstados();
		//se muestra el listado
		$vistaCodestado->listar($listado, $mensaje, $tipo_mensaje, $this->filtro);
	}
	 
	public function editar()
	{	
	    $id = Validador::validarParametro('id');
	    // 09/01/2012: SE AGREGO EL FILTRO PARA ACUMULAR PARAMETROS (pagina Y mostrar_todos)
	    $this->filtro['pagina'] = Validador::validarParametro('pagina');
	    $this->filtro['mostrar_todos'] = Validador::validarParametro('mostrar_todos');
		
	    $modelo = new codestadosModel();
	    
	    // SE OBTIENE EL REGISTRO
	    $listado = $modelo->obtenerRegistro($id);
	    
	    // SE GUARDA EL REGISTRO EN SESION PARA VERIFICAR LUEGO SI LO HA MODIFICADO OTRO USUARIO
	    $this->guardarRegistroOriginal($listado);

	    $vistaCodestado = new VistaCodEstados();
	    $vistaCodestado->editar($listado, $this->filtro);
	}
	
	public function agregar()
	{
		$vistaCodestado = new VistaCodEstados();
	    $vistaCodestado->editar(null);
	}
	
	public function insertar(){
	
	    $post = $_REQUEST;
	    
	    //Se crea una instancia del modelo
	    $modelo = new codestadosModel();
	    
	    if (!$modelo->existe($post['codigo_estado'])){//PARA QUE DOS USUARIOS NO INGRESEN EL MISMO REGISTRO
		    
		if (empty($post['nombre_estado'])){
			$post['nombre_estado'] = null;
		}
		
		if (isset($post['vigencia_desde_codestado']) && $this->esFechaValida($post['vigencia_desde_codestado'])){
			$post['vigencia_desde_codestado'] = $modelo->formatearFechaMySQL($post['vigencia_desde_codestado']);
		}
		if (isset($post['vigencia_hasta_codestado']) && $this->esFechaValida($post['vigencia_hasta_codestado'])){
			$post['vigencia_hasta_codestado'] = $modelo->formatearFechaMySQL($post['vigencia_hasta_codestado']);
		}
		
		if (empty($post['observaciones_codestado'])){
			$post['observaciones_codestado'] = null;
		}
						
		if ($modelo->insertar($post)){
			$mensaje = 'El estado se agregó con éxito';
			$tipo_mensaje = 1;
		}else{
			$mensaje = 'Error al agregar el estado';
			$tipo_mensaje = 2;
		}
		$this->listar($mensaje, $tipo_mensaje);
	    }else{
		$mensaje = 'El registro se ha ingresado previamente';
		$tipo_mensaje = 2;
		$this->listar($mensaje, $tipo_mensaje);
	    }	
	}
	
	public function modificar(){
	
	    $post = $_REQUEST;
	    //fputs(fopen('postEstados.txt','w'), print_r($post, true));	
	    //Se crea una instancia del modelo
	    $modelo = new codestadosModel();
	    
	    // 	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO PREVIAMENTE
	    if ($modelo->verificarRegistroEntero()){
		    
		if (empty($post['nombre_estado'])){
		    $post['nombre_estado'] = null;
		}
		
		if (!empty($post['vigencia_desde_codestado']) && $this->esFechaValida($post['vigencia_desde_codestado'])){
		    $post['vigencia_desde_codestado'] = $modelo->formatearFechaMySQL($post['vigencia_desde_codestado']);
		}else{
		    $post['vigencia_desde_codestado'] = null;
		}
		
		if (!empty($post['vigencia_hasta_codestado']) && $this->esFechaValida($post['vigencia_hasta_codestado'])){
		    $post['vigencia_hasta_codestado'] = $modelo->formatearFechaMySQL($post['vigencia_hasta_codestado']);
		}else{
		    $post['vigencia_hasta_codestado'] = null;
		}
		
		if (empty($post['observaciones_codestado'])){
		    $post['observaciones_codestado'] = null;
		}
		
		if ($modelo->modificar($post)){
		    $mensaje = 'El estado se modificó con éxito';
		    $tipo_mensaje = 1;
		}else{
		    $mensaje = 'Error al modificar el estado';
		    $tipo_mensaje = 2;
		}
		$this->listar($mensaje, $tipo_mensaje, $post['id_codestado'], 'asc', $post['pagina']);
	    }else{
		$mensaje = 'El registro se ha modificado previamente';
		$tipo_mensaje = 2;
		$this->listar($mensaje, $tipo_mensaje, $post['id_codestado'], 'asc', $post['pagina']);
	    }	
	}
	
	public function eliminar(){

	    $id = htmlentities($_GET['id'], ENT_QUOTES);
	    
	    //Se crea una instancia del modelo
	    $modelo = new codestadosModel();
	    
	    if ($modelo->eliminar($id)){
		$mensaje = 'El estado se eliminó con éxito';
		$tipo_mensaje = 1;
		$this->eliminado = true;
	    }else{
		$mensaje = 'No puede eliminar el Código de Estado. Posee registros asociados con algún Expediente o Nota.';
		$tipo_mensaje = 2;
	    }
	    $this->listar($mensaje, $tipo_mensaje);
	}
	
	public function listarModal(){
				
		//Se crea una instancia del modelo
		$modelo = new codestadosModel();
		//Se le pide al modelo todos los items
		$listado = $modelo->listadoModal();
		
		//fputs(fopen('ListadoModalCodEstadosC.txt','w'),print_r($listado,true));
		
 		//Creamos una instancia de la "vista"
		$vista = new VistaCodEstados();
		//se muestra la Ventana Modal
		$vista->listarModal($listado);
	}
	
	public function pedirNombreModal()
	{
		$c_solo_habilitado = Validador::validarParametro('c_solo_habilitado');
		
	    $modelo = new codestadosModel();
	    $listado = $modelo->listadoModal($c_solo_habilitado);
	    
	    $vista = new VistaCodEstados();
	    $vista->pedirNombreModal($listado);
	}
	
}
?>

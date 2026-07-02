<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

// Incluye el modelo que corresponde
require 'modelos/estados.php';
require 'modelos/codestados.php';

// Incluye la vista que corresponde
require 'vistas/estados.php';

class estados_controller extends ControllerBase
{
	public function guardarRegistroOriginal($original)
	{
		$_SESSION['anio_original'] = $original[0]['anio'];
		$_SESSION['tipo_original'] = $original[0]['tipo'];
		$_SESSION['numero_original'] = $original[0]['numero'];
		$_SESSION['cuerpo_original'] = $original[0]['cuerpo'];
		$_SESSION['alcance_original'] = $original[0]['alcance'];
		$_SESSION['fecha_estado_original'] = $original[0]['fecha_estado'];
		$_SESSION['orden_estado_original'] = $original[0]['orden_estado'];
		$_SESSION['id_codestado_original'] = $original[0]['id_codestado'];
		$_SESSION['observaciones_estado_original'] = $original[0]['observaciones_estado'];
		$_SESSION['id_usuario_original'] = $original[0]['id_usuario'];
	}
	
	public function listar($mensaje = '', $tipo_mensaje = '', $clave = '')
	{	
		$modelo = new estadosModel();
		
		$filtro = Array();
		
		if ( !empty($clave) )
		{	
			$filtro['anio'] = $clave['anio'];
			$filtro['tipo'] = $clave['tipo'];
			$filtro['numero'] = $clave['numero'];
			$filtro['cuerpo'] = $clave['cuerpo'];
			$filtro['alcance'] = $clave['alcance'];
			$filtro['sentido'] = 'anterior';// DIRECCIÓN PARA LA PAGINACIÓN (PRIMERO, ANTERIOR, SGTE., ÚLTIMO)
		}
		else
		{
			$filtro['anio'] = Validador::validarParametro('anio');
			$filtro['tipo'] = Validador::validarParametro('tipo');
			$filtro['numero'] = Validador::validarParametro('numero');
			$filtro['cuerpo'] = Validador::validarParametro('cuerpo');
			$filtro['alcance'] = Validador::validarParametro('alcance');
			$filtro['sentido'] = Validador::validarParametro('sentido');// DIRECCIÓN PARA LA PAGINACIÓN (PRIMERO, ANTERIOR, SGTE., ÚLTIMO)
			$filtro['por_teclado'] = Validador::validarParametro('por_teclado');// SI SE LLEGÓ MEDIANTE EL TECLADO AL RECORRER EL LISTADO
		}
		
		//se establece el campo por el cual ordenar
		if (Validador::validarParametro('campo_orden') != '' )
		{
			$filtro['campo_orden'] = Validador::validarParametro('campo_orden');
		}
		else
		{
			$filtro['campo_orden'] = 'anio';//por defecto
			$_SESSION['ultimo_campo'] = '';
		}
		
		$filtro['rango'] = 10;	//cantidad de registros a mostrar
		$filtro['pagina'] = Validador::validarParametro('pagina');	//se obtiene el valor de la pagina
					
		if (!$filtro['pagina'])
		{
			//si no se sabe el valor de la pagina
			$filtro['inicio'] = 0;	//se inicia en el primer registro
			$filtro['pagina'] = 1;	//con la primer pagina 
		}
		else
		{
			//si se conoce se calcula el valor del registro inicial de dicha pagina 
			$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
		} 
		$filtro['pagina_ant'] = $filtro['pagina'] - 1;		//para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;		//para la pagina posterior

		//Se establece el filtro en el modelo
		$modelo->setFiltro($filtro);			
		//Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
		$filtro['cantidad'] = $modelo->obtenerCantidad();
		
		//NUMERO TOTAL DE PAGINAS 
		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);
		
		//Se establece el filtro en el modelo
		$modelo->setFiltro($filtro);
		
 		//Se le pide al modelo todos los items
		$listado = $modelo->listadoTotal();
		
 		//Se crea una instancia de la vista
		$vista = new VistaEstados();
		//se muestra el listado
		$vista->listar($listado, $mensaje, $tipo_mensaje, $filtro);
	}
	
	public function editar($clave = '')
	{	
		$filtro = Array();
		if ( !empty($clave) )
		{
			$filtro['anio'] = $clave['anio'];
			$filtro['tipo'] = $clave['tipo'];
			$filtro['numero'] = $clave['numero'];
			$filtro['cuerpo'] = $clave['cuerpo'];
			$filtro['alcance'] = $clave['alcance'];
			$filtro['fecha_estado'] = $clave['fecha_estado'];
			$filtro['orden_estado'] = $clave['orden_estado'];
		}
		else
		{
			// CUANDO SE LLEGA DEL LAPIZ DEL LISTADO O CON EL DOBLE CLICK
			$filtro['anio'] = Validador::validarParametro('anio');
			$filtro['tipo'] = Validador::validarParametro('tipo');
			$filtro['numero'] = Validador::validarParametro('numero');
			$filtro['cuerpo'] = Validador::validarParametro('cuerpo');
			$filtro['alcance'] = Validador::validarParametro('alcance');
			$filtro['fecha_estado'] = Validador::validarParametro('fecha_estado');
			$filtro['orden_estado'] = Validador::validarParametro('orden_estado');
		}	
		
		//se establece el campo por el cual ordenar
		if ($_GET['campo_orden'] != ''){
			$filtro['campo_orden'] = Validador::validarParametro('campo_orden');
		}else{
			//por defecto
			$filtro['campo_orden'] = 'anio';
			$_SESSION['ultimo_campo'] = '';
		}
		
		$filtro['rango'] = 10;	//cantidad de registros a mostrar
		
		$filtro['pagina'] = Validador::validarParametro('pagina');	//se obtiene el valor de la pagina
					
		if (!$filtro['pagina']){	//si no se sabe el valor de la pagina
			$filtro['inicio'] = 0;	//se inicia en el primer registro
			$filtro['pagina'] = 1;	//con la primer pagina 
		}else{	//si se conoce se calcula el valor del registro inicial de dicha pagina 
			$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
		} 
		$filtro['pagina_ant'] = $filtro['pagina'] - 1;		//para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;		//para la pagina posterior
						
		//fputs(fopen('filtroEditarEstadosC.txt', 'w'),print_r($filtro, true));
		
		$modeloCodEstados = new codestadosModel();
		$listadoCodEstados = $modeloCodEstados->listadoModal();
		//fputs(fopen('listadoCodEstados_editar_estados_controller.txt','w'), print_r($listadoCodEstados, true));
		
		$modelo = new estadosModel();
		$modelo->setFiltro($filtro);
		$listado = $modelo->listadoTotal();
		
		// SE GUARDA EL REGISTRO EN SESION PARA VERIFICAR LUEGO SI LO HA MODIFICADO OTRO USUARIO
		$this->guardarRegistroOriginal($listado);
		
		$vista = new VistaEstados();
		$vista->editar($listado, $filtro, $listadoCodEstados);
	}
	
	public function agregar()
	{			
		$filtro = Array();
		$filtro['anio'] = Validador::validarParametro('anio');
		$filtro['tipo'] = Validador::validarParametro('tipo');
		$filtro['numero'] = Validador::validarParametro('numero');
		$filtro['cuerpo'] = Validador::validarParametro('cuerpo');
		$filtro['alcance'] = Validador::validarParametro('alcance');
		
		//se establece el campo por el cual ordenar
		if ( Validador::validarParametro('campo_orden') != '' ) 
		{
			$filtro['campo_orden'] = Validador::validarParametro('campo_orden');
		}else{
			//por defecto
			$filtro['campo_orden'] = 'anio';
		}
		$filtro['rango'] = 10;	//cantidad de registros a mostrar
		
		$filtro['pagina'] = 1;
		
		//se calcula el valor del registro inicial de dicha pagina 
		$filtro['inicio'] = 0;
		
		$filtro['pagina_ant'] = $filtro['pagina'] - 1;		//para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;		//para la pagina posterior
		
		$modeloCodEstados = new codestadosModel();
		$listadoCodEstados = $modeloCodEstados->listadoModal();
		
		$modelo = new estadosModel();
		//SE OBTIENE EL ULTIMO ORDEN INGRESADO
		$filtro['ultimoOrden'] = $modelo->obtenerUltimoOrden($filtro);
		
		$vista = new VistaEstados();
		$vista->editar(null, $filtro, $listadoCodEstados);
	}
	
	public function insertar()
	{
		$post = $_REQUEST;
	
		if ( $post['numero'] == '' ){ $post['numero'] = 0; }
		if ( $post['cuerpo'] == '' ){ $post['cuerpo'] = 0; }
		if ( $post['alcance'] == '' ){ $post['alcance'] = 0; }
		
		// Se crea una instancia del modelo
		$modelo = new estadosModel();
		
		// PARA QUE DOS USUARIOS NO INGRESEN EL MISMO REGISTRO
		if ( !$modelo->existe($post) )
		{
			if ( isset($post['fecha_estado']) && $this->esFechaValida($post['fecha_estado']) )
			{
				$post['fecha_estado'] = $modelo->formatearFechaMySQL($post['fecha_estado']);
			}
			
			//fputs(fopen('postInsertarEstadoC.txt','w'),print_r($post, true));
			
			if ( $modelo->insertar($post) )
			{
				$mensaje = 'El Estado se agregó con éxito.';
				$tipo_mensaje = 1;
			}
			else
			{
				$mensaje = 'Error al agregar el Estado.';
				$tipo_mensaje = 2;
			}
			
			$this->listar($mensaje, $tipo_mensaje, $post);
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
		$modelo = new estadosModel();
		
		// 	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO PREVIAMENTE
		if ( $modelo->verificarRegistroEntero() )
		{
			if ( isset($post['fecha_estado']) && $this->esFechaValida($post['fecha_estado']) )
			{
				$post['fecha_estado'] = $modelo->formatearFechaMySQL($post['fecha_estado']);
			}
			
			if ($modelo->modificar($post) )
			{
				$mensaje = 'El Estado se modificó con éxito.';
				$tipo_mensaje = 1;
			}
			else
			{
				$mensaje = 'Error al modificar el Estado.';
				$tipo_mensaje = 2;
			}
			$this->listar($mensaje, $tipo_mensaje, $post);
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
		$clave = Array();
		$clave['anio'] = Validador::validarParametro('anio');
		$clave['tipo'] = Validador::validarParametro('tipo');
		$clave['numero'] = Validador::validarParametro('numero');
		$clave['cuerpo'] = Validador::validarParametro('cuerpo');
		$clave['alcance'] = Validador::validarParametro('alcance');
		$clave['fecha_estado'] = Validador::validarParametro('fecha_estado');
		$clave['orden_estado'] = Validador::validarParametro('orden_estado');
		
		//Se crea una instancia del modelo
		$modelo = new estadosModel();
 		
		if ( $modelo->eliminar($clave) )
		{
			$mensaje = 'El Estado se eliminó con éxito.';
			$tipo_mensaje = 1;
		}
		else
		{
			$mensaje = 'Error al eliminar el Estado.';
			$tipo_mensaje = 2;
		}
		$this->listar($mensaje, $tipo_mensaje, $clave);
	}
	
}
?>

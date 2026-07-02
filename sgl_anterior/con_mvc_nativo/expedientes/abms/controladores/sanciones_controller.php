<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

// Incluye el modelo que corresponde
require 'modelos/sanciones.php';
require 'modelos/proyectos.php';
require 'modelos/estados.php';

// Incluye la vista que corresponde
require 'vistas/sanciones.php';

class sanciones_controller extends ControllerBase
{
	public function guardarRegistroOriginal($original)
	{
		$_SESSION['anio_original'] = $original[0]['anio'];
		$_SESSION['tipo_original'] = $original[0]['tipo'];
		$_SESSION['numero_original'] = $original[0]['numero'];
		$_SESSION['cuerpo_original'] = $original[0]['cuerpo'];
		$_SESSION['alcance_original'] = $original[0]['alcance'];
		$_SESSION['orden_proyecto_original'] = $original[0]['orden_proyecto'];
		$_SESSION['fecha_sancion_original'] = $original[0]['fecha_sancion'];
		$_SESSION['numero_sancion_original'] = $original[0]['numero_sancion'];
		$_SESSION['fecha_promulga_original'] = $original[0]['fecha_promulga'];
		$_SESSION['numero_promulga_original'] = $original[0]['numero_promulga'];
		$_SESSION['decreto_promulga_original'] = $original[0]['decreto_promulga'];
		$_SESSION['fecha_veto_original'] = $original[0]['fecha_veto'];
		$_SESSION['decreto_veto_original'] = $original[0]['decreto_veto'];
		$_SESSION['decreto_presidencia_original'] = $original[0]['decreto_presidencia'];
		$_SESSION['fecha_remision_de_comunicacion_original'] = $original[0]['fecha_remision_de_comunicacion'];
		$_SESSION['fecha_1er_vto_comunicacion_original'] = $original[0]['fecha_1er_vto_comunicacion'];
		$_SESSION['fecha_2do_vto_comunicacion_original'] = $original[0]['fecha_2do_vto_comunicacion'];
		$_SESSION['fecha_rta_comunicacion_original'] = $original[0]['fecha_rta_comunicacion'];
		$_SESSION['observaciones_sancion_original'] = $original[0]['observaciones_sancion'];
		$_SESSION['id_usuario_original'] = $original[0]['id_usuario'];
	}
	
	public function listar($mensaje = '', $tipo_mensaje = '', $clave = '')
	{	
		//Se crea una instancia del modelo
		$modelo = new sancionesModel();
		
		$filtro = Array();
		
		if ( !empty($clave ))
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
		//fputs(fopen('filtro_Sanciones.txt', 'w'), print_r($filtro, true));
		
		//se establece el campo por el cual ordenar
		if (!empty($_GET['campo_orden'])){
			$filtro['campo_orden'] = htmlentities($_GET['campo_orden'], ENT_QUOTES);
		}
		else{
			$filtro['campo_orden'] = 'anio';//por defecto
			$_SESSION['ultimo_campo'] = '';
		}
		
		if (!isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $filtro['campo_orden']) {
			// Si es la primera vez que carga la pagina
			// o se esta cambiando el campo por el que se ordena
			$_SESSION['ultimo_campo'] = $filtro['campo_orden'];
			$_SESSION['ultimo_sentido'] = 'asc';
		} else {
			// Si se hizo clic en el mismo que ya estaba ordenado antes
			// Solo hay que cambiar el sentido:
			if ($_SESSION['ultimo_sentido'] == 'asc') {
				$_SESSION['ultimo_sentido'] = 'desc';
			} else {
				$_SESSION['ultimo_sentido'] = 'asc';
			}
		}
		
		$filtro['rango'] = 10;	//cantidad de registros a mostrar
		$filtro['pagina'] = htmlentities($_GET['pagina'], ENT_QUOTES);	//se obtiene el valor de la pagina
					
		if (!$filtro['pagina']){	//si no se sabe el valor de la pagina
			$filtro['inicio'] = 0;	//se inicia en el primer registro
			$filtro['pagina'] = 1;	//con la primer pagina 
		}else{	//si se conoce se calcula el valor del registro inicial de dicha pagina 
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
		$vista = new VistaSanciones();
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
			$filtro['orden_proyecto'] = $clave['orden_proyecto'];
			$filtro['fecha_sancion'] = $clave['fecha_sancion'];
		}
		else
		{
			$filtro['anio'] = htmlentities($_GET['anio'], ENT_QUOTES);
			$filtro['tipo'] = htmlentities($_GET['tipo'], ENT_QUOTES);
			$filtro['numero'] = htmlentities($_GET['numero'], ENT_QUOTES);
			$filtro['cuerpo'] = htmlentities($_GET['cuerpo'], ENT_QUOTES);
			$filtro['alcance'] = htmlentities($_GET['alcance'], ENT_QUOTES);
			$filtro['orden_proyecto'] = htmlentities($_GET['orden_proyecto'], ENT_QUOTES);
			$filtro['fecha_sancion'] = htmlentities($_GET['fecha_sancion'], ENT_QUOTES);
		}	
				
		$filtro['rango'] = 10;	//cantidad de registros a mostrar
		
		$filtro['pagina'] = htmlentities($_GET['pagina'], ENT_QUOTES);	//se obtiene el valor de la pagina
		
		//si no se sabe el valor de la pagina	
		if (!$filtro['pagina'])
		{
			$filtro['inicio'] = 0;	//se inicia en el primer registro
			$filtro['pagina'] = 1;	//con la primer pagina 
		}
		else
		{	//si se conoce se calcula el valor del registro inicial de dicha pagina 
			$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
		} 
		$filtro['pagina_ant'] = $filtro['pagina'] - 1;		//para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;		//para la pagina posterior
		
		//se establece el campo por el cual ordenar
		if ($_GET['campo_orden'] != '')
		{
			$filtro['campo_orden'] = htmlentities($_GET['campo_orden'], ENT_QUOTES);
		}
		else
		{
			//por defecto
			$filtro['campo_orden'] = 'anio';
			$_SESSION['ultimo_campo'] = '';
		}
		
		if (!isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $filtro['campo_orden'])
		{
			// Si es la primera vez que carga la pagina
			// o se esta cambiando el campo por el que se ordena
			$_SESSION['ultimo_campo'] = $filtro['campo_orden'];
			$_SESSION['ultimo_sentido'] = 'asc';
		}
		else
		{
			// Si se hizo clic en el mismo que ya estaba ordenado antes
			// Solo hay que cambiar el sentido:
			if ($_SESSION['ultimo_sentido'] == 'asc')
			{
				$_SESSION['ultimo_sentido'] = 'desc';
			}
			else
			{
				$_SESSION['ultimo_sentido'] = 'asc';
			}
		}
		
		//fputs(fopen('filtroEditarEstadosC.txt','w'),print_r($filtro, true));
		
		//Se crea una instancia del modelo
		$modelo = new sancionesModel();
		//Se establece el filtro en el modelo
		$modelo->setFiltro($filtro);
		//Se le pide al modelo todos los items
		$listado = $modelo->listadoTotal();
		
		// SE GUARDA EL REGISTRO EN SESION PARA VERIFICAR 
		// LUEGO SI LO HA MODIFICADO OTRO USUARIO
		$this->guardarRegistroOriginal($listado);
		
		//Se crea una instancia de la "vista"
		$vista = new VistaSanciones();
		$vista->editar($listado, $filtro);
	}
	
	public function agregar()
	{			
		$filtro = Array();
		$filtro['anio'] = htmlentities($_GET['anio'], ENT_QUOTES);
		$filtro['tipo'] = htmlentities($_GET['tipo'], ENT_QUOTES);
		$filtro['numero'] = htmlentities($_GET['numero'], ENT_QUOTES);
		$filtro['cuerpo'] = htmlentities($_GET['cuerpo'], ENT_QUOTES);
		$filtro['alcance'] = htmlentities($_GET['alcance'], ENT_QUOTES);
		
		//se establece el campo por el cual ordenar
		if ($_GET['campo_orden'] != ''){
			$filtro['campo_orden'] = htmlentities($_GET['campo_orden'], ENT_QUOTES);
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
				
		//Se crea una instancia del modelo
		$modelo = new sancionesModel();
		//SE OBTIENE EL ULTIMO ORDEN INGRESADO
		$filtro['ultimoOrden'] = $modelo->obtenerUltimoOrden($filtro);
		
		//Se crea una instancia de la "vista"
		$vista = new VistaSanciones();
		//se muestra el listado
		$vista->editar(null, $filtro);
	}
	
	public function insertar()
	{
		$post = $_REQUEST;
		
		if ( $post['numero'] == '' ){ $post['numero'] = 0; }
		if ( $post['cuerpo'] == '' ){ $post['cuerpo'] = 0; }
		if ( $post['alcance'] == '' ){ $post['alcance'] = 0; }
		
		//Se crea una instancia del modelo
		$modelo = new sancionesModel();
		
		$clave = Array( 'anio' => $post['anio'],
						'tipo' => $post['tipo'],
						'numero' => $post['numero'],
						'cuerpo' => $post['cuerpo'],
						'alcance' => $post['alcance']
					  );
					  
		// PARA QUE DOS USUARIOS NO INGRESEN EL MISMO REGISTRO		
		if (!$modelo->existe($clave))
		{
			if (isset($post['fecha_sancion']) && $this->esFechaValida($post['fecha_sancion']))
			{
				$post['fecha_sancion'] = $modelo->formatearFechaMySQL($post['fecha_sancion']);
			}
			
			if ($modelo->insertar($post))
			{
				$mensaje = 'La Sanción se agreg&oacute; con &eacute;xito.';
				$tipo_mensaje = 1;
			}
			else
			{
				$mensaje = 'Error al agregar la Sanci&oacute;n.';
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
		$modelo = new sancionesModel();
		
		// 	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO PREVIAMENTE
		if ( $modelo->verificarRegistroEntero() )
		{
			if (isset($post['fecha_sancion']) && $this->esFechaValida($post['fecha_sancion']))
			{
				$post['fecha_sancion'] = $modelo->formatearFechaMySQL($post['fecha_sancion']);
			}
			
			if ($modelo->modificar($post))
			{
				$mensaje = 'La Sanción se modific&oacute; con &eacute;xito.';
				$tipo_mensaje = 1;
			}
			else
			{
				$mensaje = 'Error al modificar la Sanci&oacute;n.';
				$tipo_mensaje = 2;
			}
		}
		else
		{
			$mensaje = 'El registro se ha modificado previamente.';
			$tipo_mensaje = 2;
		}
		
		$this->listar($mensaje, $tipo_mensaje, $post);
	}
	
	public function eliminar()
	{	
		$clave = Array();
		
		$clave['anio'] = Validador::validarParametro('anio');
		$clave['tipo'] = Validador::validarParametro('tipo');
		$clave['numero'] = Validador::validarParametro('numero');
		$clave['cuerpo'] = Validador::validarParametro('cuerpo');
		$clave['alcance'] = Validador::validarParametro('alcance');
		$clave['orden_proyecto'] = Validador::validarParametro('orden_proyecto');
		$clave['fecha_sancion'] = Validador::validarParametro('fecha_sancion');
		
		//fputs(fopen('sansiones_eliminar_aca_llego.txt','w'),print_r($clave,true));
		//Se crea una instancia del modelo
		$modelo = new sancionesModel();
 		
		if ( $modelo->eliminar($clave) )
		{
			$mensaje = 'La Sanci&oacute;n se elimin&oacute; con &eacute;xito.';
			$tipo_mensaje = 1;
		}
		else
		{
			$mensaje = 'Error al eliminar la Sanci&oacute;n.';
			$tipo_mensaje = 2;
		}
		
		$this->listar($mensaje, $tipo_mensaje, $clave);
	}

}
?>

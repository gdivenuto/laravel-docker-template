<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//Incluye el modelo que corresponde
require 'modelos/lugares.php';
//Incluye la vista que corresponde
require 'vistas/lugares.php';

class lugares_controller extends ControllerBase
{
    public function guardarRegistroOriginal($original)
    {
	    $_SESSION['tipo_grp_original'] = $original[0]['tipo_grp'];
	    $_SESSION['codigo_grp_original'] = $original[0]['codigo_grp'];
	    $_SESSION['descripcion_grp_original'] = $original[0]['descripcion_grp'];
	    $_SESSION['abreviatura_grp_original'] = $original[0]['abreviatura_grp'];
	    $_SESSION['bloque_tipo_original'] = $original[0]['bloque_tipo'];
	    $_SESSION['bloque_codigo_original'] = $original[0]['bloque_codigo'];
	    $_SESSION['observaciones_grp_original'] = $original[0]['observaciones_grp'];
	    $_SESSION['vigente_Desde_grp_original'] = $original[0]['vigente_Desde_grp'];
	    $_SESSION['vigente_Hasta_grp_original'] = $original[0]['vigente_Hasta_grp'];
	    $_SESSION['habilitado_grp_original'] = $original[0]['habilitado_grp'];
    }
    
    public function listar($mensaje = '', $tipo_mensaje = '', $datos = '')
	{
	    $modeloLugares = new lugaresModel();
	    
	    $filtro = Array();
	    
	    //se establece el valor a buscar en el modelo
		$valor_buscado = Validador::validarParametro('valor_buscado');
		if ( !empty($valor_buscado) )
		{
		    $filtro['valor_buscado'] = $valor_buscado;
		}
		else
		{
		    $filtro['valor_buscado'] = '';
		}
		
	    //se establece el campo por el cual ordenar
		$campo_orden = Validador::validarParametro('campo_orden');
		if ( !empty($campo_orden) )
		{
		    $filtro['campo_orden'] = $campo_orden;
		}
		else
		{
		    //por defecto
		    $filtro['campo_orden'] = 'tipo_grp';
		    $_SESSION['ultimo_campo'] = '';
		}
	    
	    $filtro['sentido'] = Validador::validarParametro('sentido');//DIRECCIÓN PARA LA PAGINACIÓN (PRIMERO, ANTERIOR, SGTE., ÚLTIMO)

	    if ( !isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $filtro['campo_orden'] )
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
			if ( $_SESSION['ultimo_sentido'] == 'asc' && $filtro['sentido'] == '' )
			{
				$_SESSION['ultimo_sentido'] = 'desc';
			}
			else
			{
				$_SESSION['ultimo_sentido'] = 'asc';
			}
	    }
	    
	    //se obtiene el valor de la pagina
	    if ( $pagina != '' )
	    {
			$filtro['pagina'] = $pagina;	
	    }
	    elseif (Validador::validarParametro('pagina'))
	    {
			$filtro['pagina'] = Validador::validarParametro('pagina');
	    }
	    else
	    {
			$filtro['pagina'] = '';
	    }
	    
	    $filtro['rango'] = 15;	//cantidad de registros a mostrar
	    
	    if ( $filtro['pagina'] == '' )
	    {	//si no se sabe el valor de la pagina
			$filtro['inicio'] = 0;	//se inicia en el primer registro
			$filtro['pagina'] = 1;	//con la primer pagina 
	    }
	    else
	    {	//si se conoce se calcula el valor del registro inicial de dicha pagina 
			$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
	    }
	    $filtro['pagina_ant'] = $filtro['pagina'] - 1;		//para la pagina anterior
	    $filtro['pagina_sgte'] = $filtro['pagina'] + 1;		//para la pagina posterior
	    
	    //Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
	    $filtro['cantidad'] = $modeloLugares->obtenerCantidad();
	    
	    //NUMERO TOTAL DE PAGINAS (DE 12 expedientes CADA UNA)
	    $filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);
		
		// 05/01/2012
		$filtro['mostrar_todos'] = Validador::validarParametro('mostrar_todos', 'si');
		
	    //Se establece el filtro en el modelo
	    $modeloLugares->setFiltro($filtro);
	    //Se le pide al modelo todos los items
	    $listado = $modeloLugares->listar();

	    if ( $datos['tipo_grp'] && $datos['codigo_grp'] )
	    {
			$filtro['tipo_grp'] = $datos['tipo_grp'];
			$filtro['codigo_grp'] = $datos['codigo_grp'];
	    }
	    else
	    {
			if ( $_GET['tipo_grp'] && $_GET['codigo_grp'] )
			{
				$filtro['tipo_grp'] = $_GET['tipo_grp'];
				$filtro['codigo_grp'] = $_GET['codigo_grp'];
			}
	    }
	    
	    $vistaLugares = new VistaLugares();
	    $vistaLugares->listar($listado, $mensaje, $tipo_mensaje, $filtro);
    }
      
    public function editar()
	{    
	    $tipo = Validador::validarParametro('tipo');
	    $codigo = Validador::validarParametro('codigo');
	    // 09/01/2012: SE AGREGO EL FILTRO PARA ACUMULAR PARAMETROS (pagina Y mostrar_todos)
	    $this->filtro['pagina'] = Validador::validarParametro('pagina');
	    $this->filtro['mostrar_todos'] = Validador::validarParametro('mostrar_todos');
			   
	    $modeloLugares = new lugaresModel();
	    
	    // SE OBTIENE EL REGISTRO
	    $listado = $modeloLugares->obtenerRegistro($tipo, $codigo);
	    //fputs(fopen('listado_editar_lugaresC.txt','w'),print_r($listado,true));
	    
	    // SE GUARDA EL REGISTRO EN SESION PARA VERIFICAR LUEGO SI LO HA MODIFICADO OTRO USUARIO
	    $this->guardarRegistroOriginal($listado);
	  
	    $vistaLugares = new VistaLugares();
	    $vistaLugares->editar($listado, $this->filtro);
    }
    
    public function agregar()
    {
	    $vistaLugares = new VistaLugares();
	    $vistaLugares->editar(null);
    }
    
    public function insertar()
    {
		$datos = $_REQUEST;
		//fputs(fopen('datosLugaresAntesC.txt','w'),print_r($datos,true));
		
		//Se crea una instancia del modelo
		$modeloLugares = new lugaresModel();
		
		//SE VERIFICA POR SU CLAVE, PARA QUE DOS USUARIOS NO INGRESEN EL MISMO REGISTRO 
		if ( !$modeloLugares->existe($datos['tipo_grp'], $datos['codigo_grp']) )
		{
			if ( $datos['descripcion_grp'] == '' )
			{
				$datos['descripcion_grp'] = null;
			}
						
			if ( $datos['observaciones_grp'] == '' )
			{
				$datos['observaciones_grp'] = null;
			}
							
			if ( $datos['vigente_Desde_grp'] != '' && $this->esFechaValida($datos['vigente_Desde_grp']) )
			{
				$datos['vigente_Desde_grp'] = $modeloLugares->formatearFechaMySQL($datos['vigente_Desde_grp']);
			}
			else
			{
				$datos['vigente_Desde_grp'] = null;
			}
			
			if ( $datos['vigente_Hasta_grp'] != '' && $this->esFechaValida($datos['vigente_Hasta_grp']) )
			{
				$datos['vigente_Hasta_grp'] = $modeloLugares->formatearFechaMySQL($datos['vigente_Hasta_grp']);
			}
			else
			{
				$datos['vigente_Hasta_grp'] = null;
			}
			
			// SI SE INGRESARON VALORES PARA bloque_tipo Y bloque_codigo
			if ( $datos['bloque_tipo'] != '' && $datos['bloque_codigo'] != '' )
			{
				// SE VERIFICA LA EXISTENCIA DE bloque_tipo Y bloque_codigo EN LA BD ANTES DE GUARDAR
				if ( !$modeloLugares->existe($datos['bloque_tipo'], $datos['bloque_codigo']) )
				{
					$mensaje = "El Lugar '".$datos['bloque_tipo']."-".$datos['bloque_codigo']."' no existe.";
					$tipo_mensaje = 2;  
				}
			}
			else
			{	
				$datos['bloque_tipo'] = null;
				$datos['bloque_codigo'] = null;	
			}
			
			//fputs(fopen('datosLugaresDespuesC.txt','w'),print_r($datos,true));
			
			// SI NO HA SURGIDO UN ERROR
			if ( $tipo_mensaje != 2 )
			{
				if ( $modeloLugares->insertar($datos) )
				{
					$mensaje = 'El Lugar se agregó con éxito.';
					$tipo_mensaje = 1;  
				}
				else
				{
					$mensaje = 'Error al agregar el Lugar.';
					$tipo_mensaje = 2;
				}
			}
		}
		else
		{
			$mensaje = 'El Lugar se ha ingresado previamente';
			$tipo_mensaje = 2;
		}
		
		$this->listar($mensaje, $tipo_mensaje, $datos);
    }
    
    public function modificar()
    {
		$datos = $_REQUEST;
		//fputs(fopen('datos_modificar_lugaresC.txt','w'),print_r($datos,true));
		
		$modeloLugares = new lugaresModel();
		
		// 	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO PREVIAMENTE
		if ( $modeloLugares->verificarRegistroEntero() )
		{
			if ( $post['descripcion_grp'] == '' )
			{
				$post['descripcion_grp'] = null;
			}
			
			if ( $datos['observaciones_grp'] == '' )
			{
				$datos['observaciones_grp'] = null;
			}
			
			if ( $datos['vigente_Desde_grp'] != '' && $this->esFechaValida($datos['vigente_Desde_grp']) )
			{
				$datos['vigente_Desde_grp'] = $modeloLugares->formatearFechaMySQL($datos['vigente_Desde_grp']);
			}
			else
			{
				$datos['vigente_Desde_grp'] = null;
			}
			
			if ( $datos['vigente_Hasta_grp'] != '' && $this->esFechaValida($datos['vigente_Hasta_grp']) )
			{
				$datos['vigente_Hasta_grp'] = $modeloLugares->formatearFechaMySQL($datos['vigente_Hasta_grp']);
			}
			else
			{
				$datos['vigente_Hasta_grp'] = null;
			}
			
			// SI SE INGRESARON VALORES PARA bloque_tipo Y bloque_codigo
			if ( $datos['bloque_tipo'] != '' && $datos['bloque_codigo'] != '' )
			{
				// SE VERIFICA LA EXISTENCIA DE bloque_tipo Y bloque_codigo EN LA BD ANTES DE GUARDAR
				if ( !$modeloLugares->existe($datos['bloque_tipo'], $datos['bloque_codigo']) )
				{
					$mensaje = 'El bloque_tipo o el bloque_codigo no existen.';
					$tipo_mensaje = 2;
				}
			}
			else
			{
				$datos['bloque_tipo'] = null;
				$datos['bloque_codigo'] = null;
			}
			
			// SI NO HA SURGIDO UN ERROR
			if ( $tipo_mensaje != 2 )
			{
				if ( $modeloLugares->modificar($datos) )
				{
					$mensaje = 'El Lugar se modificó con éxito.';
					$tipo_mensaje = 1;
				}
				else
				{
					$mensaje = 'Error al modificar el Lugar.';
					$tipo_mensaje = 2;
				}
			}
		}
		else
		{
			$mensaje = 'El Lugar se ha modificado previamente';
			$tipo_mensaje = 2;
		}
		
		$this->listar($mensaje, $tipo_mensaje, $datos);
    }
    
    public function eliminar()
    {
		$tipo = Validador::validarParametro('tipo');
		$codigo = Validador::validarParametro('codigo');
		
		$modeloLugares = new lugaresModel();
		
		if ( $modeloLugares->eliminar($tipo, $codigo) )
		{
			$mensaje = 'El Lugar se eliminó con éxito.';
			$tipo_mensaje = 1;
		}
		else
		{
			$mensaje = 'No puede eliminarse el Lugar, se encuentra relacionado con otro Lugar.';
			$tipo_mensaje = 2;	  
		}
		
		$this->listar($mensaje, $tipo_mensaje);
    }
    
    public function listarModal()
	{				
		$solo_comision = Validador::validarParametro('solo_comision');
		//Se crea una instancia del modelo
		$modelo = new lugaresModel();
		//Se le pide al modelo todos los items
		$listado = $modelo->listadoModal($solo_comision);
		
		$se_edita = Validador::validarParametro('se_edita');

		$vista = new VistaLugares();
		$vista->listarModal($listado, $se_edita);
    }
    
    public function listarModalIniciador()
	{				
		$iniciador_tipo = Validador::validarParametro('iniciador_tipo');
		
		//Se crea una instancia del modelo
		$modelo = new lugaresModel();
		
		//Se le pide al modelo todos los items
		$listado = $modelo->listadoModalIniciador($iniciador_tipo);
		//fputs(fopen('listado_listarModalIniciador.txt', 'w'), print_r($listado, true));
		
		$vista = new VistaLugares();
		$vista->listarModal($listado);
    }
	
}
?>

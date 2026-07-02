<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//Incluye el modelo que corresponde
require 'modelos/proyectos.php';
require 'modelos/codproyectos.php';
require 'modelos/antecedentes.php';

//Incluye la vista que corresponde
require 'vistas/proyectos.php';
require 'vistas/antecedentes.php';

class proyectos_controller extends ControllerBase
{
	public function guardarRegistroOriginal($original)
	{
		$_SESSION['anio_original'] = $original[0]['anio'];
		$_SESSION['tipo_original'] = $original[0]['tipo'];
		$_SESSION['numero_original'] = $original[0]['numero'];
		$_SESSION['cuerpo_original'] = $original[0]['cuerpo'];
		$_SESSION['alcance_original'] = $original[0]['alcance'];
		$_SESSION['orden_proyecto_original'] = $original[0]['orden_proyecto'];
		$_SESSION['id_codproyecto_original'] = $original[0]['id_codproyecto'];
		$_SESSION['extracto_original'] = $original[0]['extracto'];
		$_SESSION['observaciones_proyecto_original'] = $original[0]['observaciones_proyecto'];
		$_SESSION['id_usuario_original'] = $original[0]['id_usuario'];
	
		//fputs(fopen('SESSIONRegistroOriginalExpedC.txt','w'),print_r($_SESSION,true));
	}
	
	public function listar($mensaje = '', $tipo_mensaje = '', $clave = '')
	{		
		//fputs(fopen('clave_proyecto_listar_C.txt','w'),print_r($clave,true));
		
		//Se crea una instancia del modelo
		$modeloProyecto = new proyectosModel();
		
		$filtro = Array();
		
		if (!empty($clave))
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
							
		$filtro['rango'] = 8;	//cantidad de registros a mostrar
		
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
		$modeloProyecto->setFiltro($filtro);
			
		//Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
		$filtro['cantidad'] = $modeloProyecto->obtenerCantidad();
		
		//NUMERO TOTAL DE PAGINAS
		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);
		
		//fputs(fopen('filtro_proyecto_listar_C.txt','w'),print_r($filtro,true));	
		
		//Se establece el filtro en el modelo
		$modeloProyecto->setFiltro($filtro);
		
 		//Se le pide al modelo todos los items
		$listado = $modeloProyecto->listadoTotal();
		//fputs(fopen('listado_en_listar_proyectos_controller.txt','w'),print_r($listado,true));	
		
 		//Se crea una instancia de la vista
		$vistaProyecto = new VistaProyectos();
		//se muestra el listado
		$vistaProyecto->listar($listado, $mensaje, $tipo_mensaje, $filtro);
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
		}
		else
		{
			$filtro['anio'] = Validador::validarParametro('anio');
			$filtro['tipo'] = Validador::validarParametro('tipo');
			$filtro['numero'] = Validador::validarParametro('numero');
			$filtro['cuerpo'] = Validador::validarParametro('cuerpo');
			$filtro['alcance'] = Validador::validarParametro('alcance');
			$filtro['orden_proyecto'] = Validador::validarParametro('orden_proyecto');
		}
				
		$filtro['rango'] = 8;	//cantidad de registros a mostrar
		$filtro['pagina'] = Validador::validarParametro('pagina');	//se obtiene el valor de la pagina
					
		if ( !$filtro['pagina'] )
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
				
		//fputs(fopen('MOSTRAR_filtroProyectosEditar.txt','w'),print_r($filtro,true));	
		
		//Se crea una instancia del modelo
		$modeloProyecto = new proyectosModel();
		
		//Se establece el filtro en el modelo
		$modeloProyecto->setFiltro($filtro);
		
		//Se le pide al modelo todos los items
		$listado = $modeloProyecto->listadoTotal();
		
		// SE GUARDA EL REGISTRO EN SESION PARA VERIFICAR 
		// LUEGO SI LO HA MODIFICADO OTRO USUARIO
		$this->guardarRegistroOriginal($listado);
				
		//Se crea una instancia del modelo de Codigos de Proyectos para el combo
		$modeloCodProyecto = new codproyectosModel();
		
		//Se le pide al modelo todos los items para el combo de Codigos
		$codigos_proy = $modeloCodProyecto->getCodigos();
		
		//Se le pide al modelo todos los proyectos Relacionados (del mismo Expediente)
		$proyRelacionados = $modeloProyecto->listadoRelacionados();
		
		//Se crea una instancia de la vista
		$vistaProyecto = new VistaProyectos();
		//se muestra el listado
		$vistaProyecto->editar($listado, $proyRelacionados, $codigos_proy, $filtro);
	}
	
	public function agregar()
	{
		$filtro = Array();
		$filtro['anio'] = Validador::validarParametro('anio');
		$filtro['tipo'] = Validador::validarParametro('tipo');
		$filtro['numero'] = Validador::validarParametro('numero');
		$filtro['cuerpo'] = Validador::validarParametro('cuerpo');
		$filtro['alcance'] = Validador::validarParametro('alcance');
		
		$filtro['ingreso_individual'] = Validador::validarParametro('ingreso_individual');
		
		if ($_GET['por_wizard'] == true)
		{
			$_SESSION['por_wizard'] = true;
		}
		else
		{
			$_SESSION['por_wizard'] = false;
		}

		$filtro['rango'] = 8;	//cantidad de registros a mostrar
		$filtro['pagina'] = 1;	
		//se calcula el valor del registro inicial de dicha pagina 
		$filtro['inicio'] = 0;
		$filtro['pagina_ant'] = $filtro['pagina'] - 1;		//para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;		//para la pagina posterior
		
		//Se crea una instancia del modelo
		$modeloProyecto = new proyectosModel();
		
		//Se establece el filtro en el modelo
		$modeloProyecto->setFiltro($filtro);
		
		//SE OBTIENE EL ULTIMO ORDEN INGRESADO
		$filtro['ultimoOrden'] = $modeloProyecto->obtenerUltimoOrden($filtro);
		
		//Se le pide al modelo todos los proyectos Relacionados (del mismo Expediente)
		$proyRelacionados = $modeloProyecto->listadoTotal();
		
		//Se crea una instancia del modelo de Codigos de Proyectos
		$modeloCodProyecto = new codproyectosModel();
		
		//Se le pide al modelo todos los items para el combo de Codigos
		$codigos_proy = $modeloCodProyecto->getCodigos();
		
		//Se crea una instancia de la "vista"
		$vistaProyecto = new VistaProyectos();
		//se muestra el formulario de Edicion
		$vistaProyecto->editar(null, $proyRelacionados, $codigos_proy, $filtro);
	}
	
	public function insertar()
	{
		$datos = $_REQUEST;
		
		if ( $datos['numero'] == '' )
		{
			$datos['numero'] = 0;
		}
		if ( $datos['cuerpo'] == '' )
		{
			$datos['cuerpo'] = 0;
		}
		if ( $datos['alcance'] == '' )
		{
			$datos['alcance'] = 0;
		}
		if ( $datos['extracto'] == '' )
		{
			$datos['extracto'] = null;
		}
		if ( $datos['observaciones_proyecto'] == '' )
		{
			$datos['observaciones_proyecto'] = null;
		}
		//fputs(fopen('datos_InsertarProyectoC.txt','w'),print_r($datos,true));
		
		// Se crea una instancia del modelo
		$modeloProyecto = new proyectosModel();
		
		// PARA QUE DOS USUARIOS NO INGRESEN EL MISMO REGISTRO
		if ( !$modeloProyecto->existe($datos) )
		{
			if ( $modeloProyecto->insertar($datos) )
			{
				//SI SE ESTA EDITANDO CON EL WIZARD Y NO SE LLEGO POR EL BOTON Agregar
				if ( Validador::validarParametro('por_boton_Agregar') == 'false' )
				{  
					//Se crea una instancia del modelo
					$modeloAntecedentes = new antecedentesModel();
					//Se establece el filtro en el modelo
					$modeloAntecedentes->setFiltro($datos);
					//Se le pide al modelo los antecedentes Relacionados
					$antecedentes = $modeloAntecedentes->listadoRelacionados();
					
					//Aviso de ingreso satisfactorio del Proyecto
					$mensaje = 'El Proyecto se agregó con éxito.';
					$tipo_mensaje = 1;
					// SE REDIRECCIONA AL EDIT DE ANTECEDENTES
					//Se crea una instancia de la "vista"
					$vistaAntecedente = new VistaAntecedentes();
					//se muestra el listado
					$vistaAntecedente->editar(null, $antecedentes, $datos, $mensaje);
				}
				else
				{
					$mensaje = 'El Proyecto se agregó con éxito.';
					$tipo_mensaje = 1;
					$this->listar($mensaje, $tipo_mensaje, $datos);// SE VUELVE AL LISTADO DE PROYECTOS DEL EXPEDIENTE EN CURSO
				}
			}
			else
			{
				$mensaje = 'Error al agregar el Proyecto.';
				$tipo_mensaje = 2;
				// SE REDIRECCIONA AL EDIT DE PROYECTOS
				$this->listar($mensaje, $tipo_mensaje, $datos);
			}
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
		$datos = $_REQUEST;
		
		//Se crea una instancia del modelo
		$modeloProyecto = new proyectosModel();
		
		// 	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO PREVIAMENTE
		if ( $modeloProyecto->verificarRegistroEntero() )
		{
			if ( $datos['numero'] == '' )
			{
				$datos['numero'] = 0;
			}
			if ( $datos['cuerpo'] == '' )
			{
				$datos['cuerpo'] = 0;
			}
			if ( $datos['alcance'] == '' )
			{
				$datos['alcance'] = 0;
			}
			if ( $datos['extracto'] == '' )
			{
				$datos['extracto'] = null;
			}
			if ( $datos['observaciones_proyecto'] == '' )
			{
				$datos['observaciones_proyecto'] = null;
			}
		
			//fputs(fopen('datos_modificar_proyectos_C.txt','w'),print_r($datos,true));
		
			if ( $modeloProyecto->modificar($datos) )
			{
				$mensaje = 'El Proyecto se modificó con éxito.';
				$tipo_mensaje = 1;
			}
			else
			{
				$mensaje = 'Error al modificar el Proyecto.';
				$tipo_mensaje = 2;
			}
		}
		else
		{
			$mensaje = 'El registro se ha modificado previamente.';
			$tipo_mensaje = 2;
		}
		
		$this->listar($mensaje, $tipo_mensaje, $datos);// PARA VOLVER A LOS PROYECTOS DEL EXPEDIENTE/NOTA EN CURSO
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
		
		$esRelacionado = Validador::validarParametro('esRelacionado');
		
		//Se crea una instancia del modelo
		$modeloProyecto = new proyectosModel();
 		
		if ( $modeloProyecto->eliminar($clave) )
		{
			$mensaje = 'El Proyecto se elimin&oacute; con &eacute;xito.';
			$tipo_mensaje = 1;
			
			if ( isset($esRelacionado) && $esRelacionado )
			{
				// SI SE ELIMINO UN PROYECTO RELACIONADO
				$this->editar($mensaje, $tipo_mensaje);
			}
			else
			{
				// SI SE ELIMINO UN PROYECTO 
				$this->listar($mensaje, $tipo_mensaje, $clave);// SE VUELVE AL LISTADO DE PROYECTOS DEL EXPEDIENTE EN CURSO
			}	
		}
		else
		{
			$mensaje = 'Error al eliminar el Proyecto.';
			$tipo_mensaje = 2;
			
			$this->listar($mensaje, $tipo_mensaje, $clave);
		}
	}

	public function listarModal()
	{
		$clave = Array();
		$clave['anio'] = Validador::validarParametro('anio');
		$clave['tipo'] = Validador::validarParametro('tipo');
		$clave['numero'] = Validador::validarParametro('numero');
		$clave['cuerpo'] = Validador::validarParametro('cuerpo');
		$clave['alcance'] = Validador::validarParametro('alcance');
					
		//Se crea una instancia del modelo
		$modelo = new proyectosModel();
		//Se le pide al modelo todos los items
		$listado = $modelo->listadoModal($clave);
		
 		//Creamos una instancia de la "vista"
		$vista = new VistaProyectos();
		//se muestra la Ventana Modal
		$vista->listarModal($listado);
	}

}
?>

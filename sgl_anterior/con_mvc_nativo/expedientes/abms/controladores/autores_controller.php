<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//Incluye el modelo que corresponde
require 'modelos/autores.php';
require 'modelos/lugares.php';

//Incluye la vista que corresponde
require 'vistas/autores.php';
require 'vistas/expedientes.php';

class autores_controller extends ControllerBase
{
	public function listar($mensaje = '')
	{
		//Se crea una instancia del modelo
		$modelo = new autoresModel();
		
		$filtro = Array();
		
		//se establece el valor a buscar en el modelo
		if ($_GET['valor_buscado'] != ''){
			$filtro['valor_buscado'] = htmlentities($_GET['valor_buscado'], ENT_QUOTES);
		}else{
			$filtro['valor_buscado'] = '';
		}
		
		//se establece el campo por el cual ordenar
		if ($_GET['campo_orden'] != ''){
			$filtro['campo_orden'] = htmlentities($_GET['campo_orden'], ENT_QUOTES);
		}else{
			//por defecto
			$filtro['campo_orden'] = 'codigo_categoria';
		}
				
		//Se establece el filtro en el modelo
		$modelo->setFiltro($filtro);
 		//Se le pide al modelo todos los items
		$listado = $modelo->listadoTotal();
		
 		//Creamos una instancia de la "vista"
		$vista = new VistaAutores();
		//se muestra el listado
		$vista->listar($listado, $mensaje, $filtro);
	}
		
	public function eliminar()
	{		
		$clave = Array();
		$clave['anio'] = htmlentities($_GET['anio'], ENT_QUOTES);
		$clave['tipo'] = htmlentities($_GET['tipo'], ENT_QUOTES);
		$clave['numero'] = htmlentities($_GET['numero'], ENT_QUOTES);
		$clave['cuerpo'] = htmlentities($_GET['cuerpo'], ENT_QUOTES);
		$clave['alcance'] = htmlentities($_GET['alcance'], ENT_QUOTES);
		$clave['autor_tipo'] = htmlentities($_GET['autor_tipo'], ENT_QUOTES);
		$clave['autor_codigo'] = htmlentities($_GET['autor_codigo'], ENT_QUOTES);
		
		//Se crea una instancia del modelo
		$modelo = new autoresModel();
		
		if ($modelo->eliminar($clave)){
			$mensaje = 'El autor se elimin&oacute; con &eacute;xito';
		}else{
			$mensaje = 'Error al eliminar el autor';
		}
		
		$this->listar($mensaje);
	}
	
	public function listarModal()
	{
		$autor_tipo = Validador::validarParametro('autor_tipo');
			
		//Se crea una instancia del modelo de Lugares
		$modelo = new lugaresModel();
		
		//Se le pide al modelo todos los items
		$listado = $modelo->listadoModal($autor_tipo);
		
 		// Se crea una instancia de la "vista"
		$vista = new VistaAutores();
		
		//se muestra la Ventana Modal
		$vista->listarModal($listado);
	}
	
}
?>

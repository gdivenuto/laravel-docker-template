<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//Incluye el modelo que corresponde
require 'modelos/ficha.php';

//Incluye la vista que corresponde
require 'vistas/ficha.php';

class ficha_controller extends ControllerBase
{
	public function array_recibe($url_array) 
	{
	    //Devuelve una cadena con las barras invertidas eliminadas (\' se convierte en '), 
	    //las barras invertidas dobles se convierten en sencillas
	    $tmp = stripslashes($url_array);
	    
	    //Decodifica cualquier cifrado tipo %## en la cadena dada. Se devuelve la cadena decodificada
	    $tmp = urldecode($tmp);
	    
	    //toma una variable sencilla seriada y la convierte de vuelta a su valor PHP
	    $tmp = unserialize($tmp);

	    return $tmp;
	} 
	
	//SE RECIBE LA CLAVE DEL EXPEDIENTE PARA GENERAR LA FICHA
	public function generar_ficha()
	{
	    //Se crea una instancia del modelo
	    $modelo = new fichaModel();
			    
	    $filtro = Array();
	    $filtro['anio'] = Validador::validarParametro('anio', 0, Validador::$PATRON_NUMEROS);
	    $filtro['tipo'] = Validador::validarParametro('tipo', 'E', Validador::$PATRON_LETRAS);
	    $filtro['numero'] = Validador::validarParametro('numero', 0, Validador::$PATRON_NUMEROS);
	    $filtro['cuerpo'] = Validador::validarParametro('cuerpo', 0, Validador::$PATRON_NUMEROS);
	    $filtro['alcance'] = Validador::validarParametro('alcance', 0, Validador::$PATRON_NUMEROS);
	    
	    $modelo->setFiltro($filtro);
	    
	    // SE OBTIENEN LOS DATOS EN EL MODELO PARA LA FICHA DEL EXPEDIENTE
	    $ficha = $modelo->obtenerDatos();

	    //fputs(fopen('datosFichaC.txt','w'), print_r($ficha, true));	
	    
	    $vista = new VistaFicha();
	    $vista->generar_ficha($ficha, $filtro);
	}
		
	//SE RECIBE LA CLAVE DEL EXPEDIENTE PARA GENERAR LA ETIQUETA
	public function generarEtiqueta_ficha()
	{
	    //Se crea una instancia del modelo
	    $modelo = new fichaModel();
	        
	    $filtro = Array();
	    $filtro['anio'] = Validador::validarParametro('anio', 0, Validador::$PATRON_NUMEROS);
	    $filtro['tipo'] = Validador::validarParametro('tipo', 'E', Validador::$PATRON_LETRAS);
	    $filtro['numero'] = Validador::validarParametro('numero', 0, Validador::$PATRON_NUMEROS);
	    $filtro['cuerpo'] = Validador::validarParametro('cuerpo', 0, Validador::$PATRON_NUMEROS);
	    $filtro['alcance'] = Validador::validarParametro('alcance', 0, Validador::$PATRON_NUMEROS);
	    
	    $modelo->setFiltro($filtro);
	    
	    //Se realiza la busqueda en el modelo
	    $etiqueta = $modelo->obtenerDatos();
	    //fputs(fopen('datos_EtiquetaC.txt','w'),print_r($etiqueta,true));	
	    
	    $vista = new VistaFicha();
	    $vista->generarEtiqueta_ficha($etiqueta, $filtro);
	}

	public function ver_ficha_modal()
	{
		//Se crea una instancia del modelo
		$modelo = new fichaModel();
			
		$filtro = Array();
		$filtro['anio'] = Validador::validarParametro('anio', 0, Validador::$PATRON_NUMEROS);
	    $filtro['tipo'] = Validador::validarParametro('tipo', 'E', Validador::$PATRON_LETRAS);
	    $filtro['numero'] = Validador::validarParametro('numero', 0, Validador::$PATRON_NUMEROS);
	    $filtro['cuerpo'] = Validador::validarParametro('cuerpo', 0, Validador::$PATRON_NUMEROS);
	    $filtro['alcance'] = Validador::validarParametro('alcance', 0, Validador::$PATRON_NUMEROS);
		
		$modelo->setFiltro($filtro);
		
		// SE OBTIENEN LOS DATOS EN EL MODELO PARA LA FICHA DEL EXPEDIENTE
		$ficha = $modelo->obtenerDatos();
		//fputs(fopen('datosFichaC.txt','w'), print_r($ficha, true));	
		
		$vista = new VistaFicha();
		$vista->ver_ficha_modal($ficha, $filtro);
	}
}
?>

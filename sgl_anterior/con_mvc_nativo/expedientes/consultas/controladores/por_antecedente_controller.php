<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//Incluye el modelo que corresponde
require 'modelos/por_antecedente.php';
require 'modelos/consulta_gral.php';

//Incluye la vista que corresponde
require 'vistas/por_antecedente.php';

class por_antecedente_controller extends ControllerBase
{
    private $filtro = Array();
    
    public function por_antecedente($mensaje = '')
	{
		$this->filtro['bpa_enviado'] = Validador::validarParametro('bpa_enviado');
		if ($this->filtro['bpa_enviado'] == 'enviado')
		{
			$this->filtro['bpa_numero'] = Validador::validarParametro('bpa_numero');
			$this->filtro['bpa_anio'] = Validador::validarParametro('bpa_anio');

			//Se crea una instancia del modelo
			$modelo = new porAntecedenteModel();
			//Se establece el filtro en el modelo
			$modelo->setFiltro($this->filtro);
			//Se realiza la busqueda en el modelo
			$listado = $modelo->listar();
		}
		
		//Creamos una instancia de la "vista"
		$vista = new VistaPorAntecedente();
		//se muestra el listado
		$vista->por_antecedente($listado, $mensaje, $this->filtro);
    }	
	
}
?>

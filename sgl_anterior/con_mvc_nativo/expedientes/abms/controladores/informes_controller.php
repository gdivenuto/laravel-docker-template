<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//require '../../librerias/control_duracion_sesion.php';
//Incluye el modelo que corresponde
require 'modelos/informes.php';
//Incluye la vista que corresponde
require 'vistas/informes.php';

class informes_controller extends ControllerBase
{
	private $clave = Array();
	
	public function guardarRegistroOriginal($original)
	{
		$_SESSION['anio_original'] = $original[0]['anio'];
		$_SESSION['tipo_original'] = $original[0]['tipo'];
		$_SESSION['numero_original'] = $original[0]['numero'];
		$_SESSION['cuerpo_original'] = $original[0]['cuerpo'];
		$_SESSION['alcance_original'] = $original[0]['alcance'];
		$_SESSION['orden_giro_original'] = $original[0]['orden_giro'];
		$_SESSION['orden_informe_original'] = $original[0]['orden_informe'];
		$_SESSION['fecha_pedido_informe_original'] = $original[0]['fecha_pedido_informe'];
		$_SESSION['fecha_vuelta_informe_original'] = $original[0]['fecha_vuelta_informe'];
		$_SESSION['detalle_informe_original'] = $original[0]['detalle_informe'];
		$_SESSION['observaciones_informe_original'] = $original[0]['observaciones_informe'];
		$_SESSION['id_usuario_original'] = $original[0]['id_usuario'];
		//fputs(fopen('SESSIONRegistroOriginalInformeC.txt','w'),print_r($_SESSION,true));
	}
		
	public function listar($clave = '', $mensaje = '', $tipo_mensaje = '')
	{
	    if ( !empty($clave) )
	    {
			$this->clave['anio'] = $clave['anio'];
			$this->clave['tipo'] = $clave['tipo'];
			$this->clave['numero'] = $clave['numero'];
			$this->clave['cuerpo'] = $clave['cuerpo'];
			$this->clave['alcance'] = $clave['alcance'];
			$this->clave['orden_giro'] = $clave['orden_giro'];
			$this->clave['giro_cerrado'] = 'no';
			$this->clave['comision_descripcion'] = $clave['comision_descripcion'];
		}
		else
		{
			$this->clave['anio'] = Validador::validarParametro('anio');
			$this->clave['tipo'] = Validador::validarParametro('tipo');
			$this->clave['numero'] = Validador::validarParametro('numero');
			$this->clave['cuerpo'] = Validador::validarParametro('cuerpo');
			$this->clave['alcance'] = Validador::validarParametro('alcance');
			$this->clave['orden_giro'] = Validador::validarParametro('orden_giro');
			$this->clave['giro_cerrado'] = Validador::validarParametro('giro_cerrado');
			$this->clave['orden_informe'] = Validador::validarParametro('orden_informe');
			$this->clave['comision_descripcion'] = Validador::validarParametro('comision_descripcion');
		}
		
	    $modelo = new informesModel();
		$informes = $modelo->listar($this->clave);
	    
	    $vista = new VistaInformes();
	    $vista->listar($informes, $this->clave, $mensaje, $tipo_mensaje);
	}
	
	public function agregar()
	{			
		$filtro = Array();
		$filtro['anio'] = Validador::validarParametro('anio');
		$filtro['tipo'] = Validador::validarParametro('tipo');
		$filtro['numero'] = Validador::validarParametro('numero');
		$filtro['cuerpo'] = Validador::validarParametro('cuerpo');
		$filtro['alcance'] = Validador::validarParametro('alcance');
		$filtro['orden_giro'] = Validador::validarParametro('orden_giro');
		$filtro['giro_cerrado'] = 'no';
		$filtro['comision_descripcion'] = Validador::validarParametro('comision_descripcion');
		
		//Se crea una instancia del modelo
		$modelo = new informesModel();
		//SE OBTIENE EL SIGUIENTE ORDEN A INGRESAR
		$filtro['ultimoOrden'] = $modelo->obtenerUltimoOrden($filtro);
		
		//Se crea una instancia de la "vista"
		$vista = new VistaInformes();
		//se muestra el formulario
		$vista->editar(null, $filtro);
	}
	
	public function editar()
	{			
		$filtro = Array();
		$filtro['anio'] = Validador::validarParametro('anio');
		$filtro['tipo'] = Validador::validarParametro('tipo');
		$filtro['numero'] = Validador::validarParametro('numero');
		$filtro['cuerpo'] = Validador::validarParametro('cuerpo');
		$filtro['alcance'] = Validador::validarParametro('alcance');
		$filtro['orden_giro'] = Validador::validarParametro('orden_giro');
		$filtro['orden_informe'] = Validador::validarParametro('orden_informe');
		$filtro['giro_cerrado'] = 'no';
		$filtro['comision_descripcion'] = Validador::validarParametro('comision_descripcion');
		
		// Se crea una instancia del modelo
		$modelo = new informesModel();
		$informe = $modelo->listar($filtro);
		
		// SE GUARDA EL REGISTRO EN SESION PARA VERIFICAR 
		// LUEGO SI LO HA MODIFICADO OTRO USUARIO
		$this->guardarRegistroOriginal($informe);
		
		// Se crea una instancia de la "vista"
		$vista = new VistaInformes();
		// SE MUESTRA EL FORMULARIO
		$vista->editar($informe, $filtro);
	}
	
	public function insertar()
	{
		$datos = $_REQUEST;
		
		$this->clave['anio'] = Validador::validarParametro('anio');
		$this->clave['tipo'] = Validador::validarParametro('tipo');
		$this->clave['numero'] = Validador::validarParametro('numero');
		$this->clave['cuerpo'] = Validador::validarParametro('cuerpo');
		$this->clave['alcance'] = Validador::validarParametro('alcance');
		$this->clave['orden_giro'] = Validador::validarParametro('orden_giro');
		$this->clave['giro_cerrado'] = 'no';
		$this->clave['comision_descripcion'] = Validador::validarParametro('comision_descripcion');
							  
		//Se crea una instancia del modelo
		$modelo = new informesModel();	
			
		//PARA QUE DOS USUARIOS NO INGRESEN EL MISMO REGISTRO	
		if ( !$modelo->existe($this->clave) )
		{
			if ( $modelo->insertar($datos) )
			{
				$mensaje = 'El Informe se agregó con éxito.';
				$tipo_mensaje = 1;
			}
			else
			{
				$mensaje = 'Error al agregar el Informe.';
				$tipo_mensaje = 2;
			}
		}
		else
		{
			$mensaje = 'El registro se ha ingresado previamente.';
			$tipo_mensaje = 2;
		}
		$this->listar($this->clave, $mensaje, $tipo_mensaje);	
	}
	
	public function modificar()
	{	
		$datos = $_REQUEST;
		//fputs(fopen('datos_Modificar_Informes.txt','w'),print_r($datos, true));
		
		//Se crea una instancia del modelo
		$modelo = new informesModel();
		
		// 	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO PREVIAMENTE
		if ( $modelo->verificarRegistroEntero() )
		{	
			if ( $modelo->modificar($datos) )
			{
				$mensaje = 'El Informe se modificó con éxito.';
				$tipo_mensaje = 1;
			}
			else
			{
				$mensaje = 'Error al modificar el Informe.';
				$tipo_mensaje = 2;
			}
		}
		else
		{
			$mensaje = 'El registro se ha modificado previamente.';
			$tipo_mensaje = 2;
		}
		$this->listar($datos, $mensaje, $tipo_mensaje);
	}
	
	public function eliminar()
	{	
		$this->clave['anio'] = Validador::validarParametro('anio');
		$this->clave['tipo'] = Validador::validarParametro('tipo');
		$this->clave['numero'] = Validador::validarParametro('numero');
		$this->clave['cuerpo'] = Validador::validarParametro('cuerpo');
		$this->clave['alcance'] = Validador::validarParametro('alcance');
		$this->clave['orden_giro'] = Validador::validarParametro('orden_giro');
		$this->clave['orden_informe'] = Validador::validarParametro('orden_informe');
		$this->clave['comision_descripcion'] = Validador::validarParametro('comision_descripcion');
				
		//Se crea una instancia del modelo de Informes
		$modelo = new informesModel();
 		
		if ( $modelo->eliminar($this->clave) )
		{
			$mensaje = 'El Informe se eliminó con éxito.';
			$tipo_mensaje = 1;
		}
		else
		{
			$mensaje = 'Error al eliminar el Informe.';
			$tipo_mensaje = 2;
		}
		$this->clave['orden_informe'] = '';
		$this->clave['giro_cerrado'] = 'no';
		//fputs(fopen('clave_eliminar_Informe.txt','w'),print_r($this->clave, true));
		$this->listar($this->clave, $mensaje, $tipo_mensaje);
	}
	
    public function buscarInformes()
	{
		$clave['anio'] = Validador::validarParametro('anio');
		$clave['tipo'] = Validador::validarParametro('tipo');
		$clave['numero'] = Validador::validarParametro('numero');
		$clave['cuerpo'] = Validador::validarParametro('cuerpo');
		$clave['alcance'] = Validador::validarParametro('alcance');
		$clave['orden_giro'] = Validador::validarParametro('orden_giro');
		
		//fputs(fopen('clave_buscarInformes_controller.txt','w'),print_r($clave, true));
		
		//Se crea una instancia del modelo de Informes
		$modelo = new informesModel();
		// SE OBTIENEN TODOS LOS INFORMES DEL GIRO
		$informes = $modelo->listar($clave);
		//fputs(fopen('informes_buscarInformes_controller.txt','w'),print_r($informes, true));
		
		echo json_encode($informes, JSON_FORCE_OBJECT);// FUERZA A DEVOLVER UN OBJETO
    } 

}
?>

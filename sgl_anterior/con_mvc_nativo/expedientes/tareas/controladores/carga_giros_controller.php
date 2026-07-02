<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//Incluye el modelo que corresponde
require 'modelos/carga_giros.php';
require '../abms/modelos/giros.php';
require '../abms/modelos/estados.php';

//Incluye la vista que corresponde
require 'vistas/carga_giros.php';
require '../abms/vistas/giros.php';

class carga_giros_controller extends ControllerBase
{
	private $giro_a_cargar = Array();
	
	public function esFechaValida($fecha)
	{
		if ($fecha != null){
	    
		    $fec_partes = explode("/",$fecha);
		    $mes   = $fec_partes[1];
		    $dia   = $fec_partes[0];
		    $anio  = $fec_partes[2];

		    return checkdate( $mes, $dia, $anio );
	    }else{
		    return false;
	    }	
	}
	
	public function verificarExistencia()
	{
	    $datos['anio'] = Validador::validarParametro('anio');
	    $datos['tipo'] = Validador::validarParametro('tipo');	
	    $datos['numero'] = Validador::validarParametro('numero');
	    $datos['cuerpo'] = Validador::validarParametro('cuerpo');
	    $datos['alcance'] = Validador::validarParametro('alcance');
	    
	    //Se crea una instancia del modelo
	    $modelo = new cargarGirosModel();
	    
	    if ( $modelo->verificarExistencia($datos) )
	    {
		    //SE AGREGA UN Giro NORMALMENTE
		    $this->agregar($datos);
		}
	    else
	    {	
			// SE MUESTRA LA VENTANA MODAL PARA CARGAR VARIOS GIROS
			$vista = new VistaCargaGiros();
			$vista->mostrarModalCargaGiros($datos);
	    }
	}
	
	public function agregar($datos)
	{
		$datos['campo_orden'] = 'anio';
	    $datos['rango'] = 12;	
	    $datos['pagina'] = 1;
	    $datos['inicio'] = 0;
		    
	    //Se crea una instancia del modelo
	    $modelo = new girosModel();
	    //SE OBTIENE EL ULTIMO ORDEN INGRESADO
	    $datos['ultimoOrden'] = $modelo->obtenerUltimoOrden($datos);
	    
	    //Se crea una instancia de la "vista"
	    $vista = new VistaGiros();
	    //se muestra el listado
	    $vista->editar(null, $datos);
	}
	
	public function cargarGiros()
	{
	    $datos['anio'] = Validador::validarParametro('anio');
	    $datos['tipo'] = Validador::validarParametro('tipo');	
	    $datos['numero'] = Validador::validarParametro('numero');
	    $datos['cuerpo'] = Validador::validarParametro('cuerpo');
	    $datos['alcance'] = Validador::validarParametro('alcance');
	    
	    $modelo = new cargarGirosModel();
	    
	    // SE OBTIENEN LAS COMISIONES
	    $comisiones = $modelo->obtenerComisiones();
	    
	    $vista = new VistaCargaGiros();
	    $vista->cargarGiros($comisiones, $datos);
	}
	
	public function guardar()
	{
	    //Se crea una instancia del modelo
	    $modelo = new cargarGirosModel();
	    
	    //echo '<pre>';print_r($_REQUEST);echo '</pre>';
	    
	    //PARA CARGAR HASTA 6 GIROS A LA VEZ
	    $orden = 0;
	    for ($i=0; $i<6; $i++)
	    {
			// SI SE SELECCIONÓ UNA COMISIÓN, VALOR DISTINTO DE CERO
	    	if ( Validador::validarParametro('tcg_comision'.($i+1)) != '0' )
	    	{ 
				$this->giro_a_cargar['anio'] = Validador::validarParametro('tcg_anio');
				$this->giro_a_cargar['tipo'] = Validador::validarParametro('tcg_tipo');
				$this->giro_a_cargar['numero'] = Validador::validarParametro('tcg_numero');
				$this->giro_a_cargar['cuerpo'] = Validador::validarParametro('tcg_cuerpo');
				$this->giro_a_cargar['alcance'] = Validador::validarParametro('tcg_alcance');
				$orden = $orden+1; // SE INCREMENTA EL orden PARA CARGAR EL GIRO
				$this->giro_a_cargar['orden_giro'] = $orden;
				
				// SE TOMA EL tipo Y EL codigo DE LA COMISION
				$comision = explode("-", Validador::validarParametro('tcg_comision'.($i+1)));
				$this->giro_a_cargar['comision_tipo'] = $comision[0];
				$this->giro_a_cargar['comision_codigo'] = $comision[1];
				
				if ($orden == 1)
				{ 
					// SI ES EL PRIMER GIRO, SE CARGA CON FECHA DE ENTRADA
					$this->giro_a_cargar['fecha_entrada_giro'] = "'".$modelo->formatearFechaMySQL(Validador::validarParametro('mstcg_fecha_desde'))."'";
				}
				else
				{
					$this->giro_a_cargar['fecha_entrada_giro'] = 'null';
				}
				
				$this->giro_a_cargar['fecha_salida_giro'] = 'null';
				$this->giro_a_cargar['dictamen_giro'] = 'null';
				
				if ( Validador::validarParametro('tcg_observacion_comision'.($i+1)) == '' )
				{
					$this->giro_a_cargar['observaciones_giro'] = 'null';
				}
				else
				{
					$this->giro_a_cargar['observaciones_giro'] = "'".Validador::validarParametro('tcg_observacion_comision'.($i+1))."'";
				}	
				
				$this->giro_a_cargar['id_usuario'] = Validador::validarParametro('id_usuario');

				//fputs(fopen('giro_a_cargarC_'.$i.'.txt','w'),print_r($this->giro_a_cargar, true));
				
				if ( $modelo->cargarGiro($this->giro_a_cargar) )
				{
					$error = 'false';
					$mensaje = 'Los Giros se han cargado satisfactoriamente.';
					$tipo_mensaje = 1;
				}
				else
				{
					$error = 'true';
					$mensaje = 'Ha ocurrido un error al intentar cargar los Giros.';
					$tipo_mensaje = 2;  
				}	
			}
	    }
	    
	    // SI NO SURGIO UN ERROR AL CARGAR LOS GIROS
	    if ($error == 'false')
	    {
			// SE OBTIENE EL ULTIMO ORDEN DEL Estado PARA EL EXPEDIENTE DETERMINADO
			$modeloEstados = new estadosModel();
			$ordenEstado = $modeloEstados->obtenerUltimoOrden($this->giro_a_cargar);
			$nuevo_orden_estado = $ordenEstado + 1;
			// SE FORMATEA LA FECHA PARA EL ESTADO
			$fecha_a_guardar = $modelo->formatearFechaMySQL(Validador::validarParametro('mstcg_fecha_desde'));
			
			// SE REGISTRA EL ESTADO 3 (GIRADO A COMISION) PARA EL EXPEDIENTE
			$modelo->registrar_estado_girado_a_comision($this->giro_a_cargar, $fecha_a_guardar, $nuevo_orden_estado);
	    }
	    
	    //Se crea una instancia del modelo
	    $modeloGiros = new girosModel();
	    
	    $this->giro_a_cargar['inicio'] = 0;
	    $this->giro_a_cargar['rango'] = 12;
	    
	    //Se establece el filtro en el modelo
	    $modeloGiros->setFiltro($this->giro_a_cargar);
	    
	    //Se le pide al modelo todos los items
	    $listado = $modeloGiros->listar(1);// EL 1 PARA QUE LISTE TODOS LOS GIROS DEL EXPEDIENTE
	    //fputs(fopen('listado_guardar_giros.txt', 'w'), print_r($listado, true));
	    
	    //Se crea una instancia de la vista
	    $vista = new VistaGiros();
	    //se muestra el listado
	    $vista->listar($listado, $mensaje, $tipo_mensaje, $this->giro_a_cargar);
	}
}
?>

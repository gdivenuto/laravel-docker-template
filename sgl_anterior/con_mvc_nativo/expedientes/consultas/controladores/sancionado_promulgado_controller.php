<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//Incluye el modelo que corresponde
require 'modelos/consulta_gral.php';
require 'modelos/sancionado_promulgado.php';

//Incluye la vista que corresponde
require 'vistas/sancionado_promulgado.php';

class sancionado_promulgado_controller extends ControllerBase
{
    private $listadoIniciadores = null;
    private $listadoAutores = null;
    private $listadoCategorias = null;
    private $listadoTemas = null;
    
    public function esFechaValida($fecha)
	{    
		if($fecha != null){
		
			$fec_partes = explode("/",$fecha);
			$mes   = $fec_partes[1];
			$dia   = $fec_partes[0];
			$anio  = $fec_partes[2];

			return checkdate( $mes, $dia, $anio );
		}else{
			return false;
		}	
    }
	
    public function cargarCombos()
    {
		//Se crea una instancia del modelo ConsultaGral para cargar los combos
		$modeloConsultaGral = new consultaGralModel();
		
		//Se le pide al modelo todos los autores
		$this->listadoIniciadores = $modeloConsultaGral->obtenerIniciadores();
		//fputs(fopen('listadoIniciadores.txt','w'),print_r($listadoIniciadores, true));
		
		//Se le pide al modelo todos los autores
		$this->listadoAutores = $modeloConsultaGral->obtenerAutores();
		//fputs(fopen('listadoAutores.txt','w'),print_r($listadoAutores, true));
		
		//Se le pide al modelo todos las categorias
		$this->listadoCategorias = $modeloConsultaGral->obtenerCategorias();
		//fputs(fopen('listadoCategorias.txt','w'),print_r($listadoCategorias, true));
		
		//Se le pide al modelo todos los temas
		$this->listadoTemas = $modeloConsultaGral->obtenerTemas();
		//fputs(fopen('listadoTemas.txt','w'),print_r($listadoTemas, true));
    }
	
    public function listar($mensaje = '')
    {
		// Se cargan los combos para la busqueda en la vista
		$this->cargarCombos();
		//Se crea una instancia del modelo de Sanciones
		$modelo = new sancionadoPromulgadoModel();

	/**********************************************************************************************		
			SE ARMA EL FILTRO PARA LA VISTA
	**********************************************************************************************/		
		$filtro = Array();
		$filtro['c_enviado'] = Validador::validarParametro('c_enviado');
		
		//PARA VOLVER A MOSTRAR LOS DATOS DE LA BUSQUEDA EN LOS COMBOS DE LA VISTA
		$filtro['c_iniciado'] = Validador::validarParametro('c_iniciado');
		$filtro['c_autor'] = Validador::validarParametro('c_autor');
		$filtro['c_categoria'] = Validador::validarParametro('c_categoria');	//SE FILTRA POR Categoria
		$filtro['c_tema'] = Validador::validarParametro('c_tema');		//SE FILTRA POR Tema
		$filtro['c_opcionsp'] = Validador::validarParametro('c_opcionsp');	//SE FILTRA POR Sancionado O Promulgado
		$filtro['c_rango'] = 5;				//cantidad de registros a mostrar
		$filtro['c_pagina'] = Validador::validarParametro('c_pagina');	//se obtiene el valor de la pagina
		
		// PARA MOSTRAR LOS HABILITADOS O NO EN LOS COMBOS DEL BUSCADOR
		$filtro['c_solo_habilitado'] = Validador::validarParametro('c_solo_habilitado');
						
		//SE FILTRA POR Iniciador
		$iniciado = explode("-", Validador::validarParametro('c_iniciado'));
		$filtro['c_iniciado_tipo'] = $iniciado[0];
		$filtro['c_iniciado_codigo'] = $iniciado[1];
		
		//SE FILTRA POR Autor
		$autor = explode("-", Validador::validarParametro('c_autor'));
		$filtro['c_autor_tipo'] = $autor[0];
		$filtro['c_autor_codigo'] = $autor[1];
		
		//SE FILTRA POR Fecha desde Y Fecha hasta
		if ($this->esFechaValida(Validador::validarParametro('c_fecha_desde'))){
			$filtro['c_fecha_desde'] = $modelo->formatearFechaMySQL(Validador::validarParametro('c_fecha_desde'));
		}
		if ($this->esFechaValida(Validador::validarParametro('c_fecha_hasta'))){
			$filtro['c_fecha_hasta'] = $modelo->formatearFechaMySQL(Validador::validarParametro('c_fecha_hasta'));
		}
				
		if (!$filtro['c_pagina']){		//si no se sabe el valor de la pagina
			$filtro['c_inicio'] = 0;	//se inicia en el primer registro
			$filtro['c_pagina'] = 1;	//con la primer pagina 
		}else{	
			//si se conoce se calcula el valor del registro inicial de dicha pagina 
			$filtro['c_inicio'] = ($filtro['c_pagina'] * $filtro['c_rango']) - $filtro['c_rango'];
		} 
		$filtro['c_pagina_ant'] = $filtro['c_pagina'] - 1;	//para la pagina anterior
		$filtro['c_pagina_sgte'] = $filtro['c_pagina'] + 1;	//para la pagina posterior
			
	/*************************************************************************************************/
		if ($filtro['c_enviado'] == 'enviado')
		{	
			// 04/01/2012: SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
			$_SESSION['filtro_consulta_sancionado_promulgado'] = $filtro;
			//fputs(fopen('SESSION_Consulta_sancionado_promulgado.txt','w'),print_r($_SESSION['filtro_consulta_sancionado_promulgado'], true));
			
			// SE ESTABLECE EL FILTRO PARA LA QUERY DEL LISTADO
			$modelo->setFiltro($filtro);
			// SE REALIZA LA BUSQUEDA EN EL MODELO
			$listado = $modelo->listar();
			// SE GUARDA LA CANTIDAD TOTAL DE EXPEDIENTES DEVUELTOS(SIN TOMAR EN CUENTA EL LIMIT)
			$filtro['c_cantidad'] = $_SESSION['total'];
			
			$filtro['c_nro_paginas'] = 1;
			if ($filtro['c_cantidad'] > 5){
				//NUMERO TOTAL DE PAGINAS (DE 5 expedientes CADA UNA)
				$filtro['c_nro_paginas'] = ceil($filtro['c_cantidad'] / $filtro['c_rango']);
			}
		}

		$vista = new VistaSancionadoPromulgado();
		//se muestra el listado
		$vista->listar_sancionado_promulgado($listado, $this->listadoIniciadores, $this->listadoAutores, $this->listadoCategorias, $this->listadoTemas, $mensaje, $filtro);
    }	

    //SE OBTIENE EL LISTADO COMPLETO PARA GENERAR EL FORMATO DE IMPRESION Y EL DOCUMENTO DE TEXTO
    public function armar_listado_completo()
    {
		$modelo = new sancionadoPromulgadoModel();

		// SE ARMA EL FILTRO PARA LA VISTA
		$filtro = Array();

		// PARA UTILIZAR LA VISTA CORRESPONDIENTE, EL FORMATO DE IMPRESION O EL DOCUMENTO DE TEXTO
		$formato = Validador::validarParametro('formato');

		//SE FILTRA POR Iniciador
		$iniciado = explode("-", Validador::validarParametro('c_iniciado'));
		$filtro['c_iniciado_tipo'] = $iniciado[0];
		$filtro['c_iniciado_codigo'] = $iniciado[1];
		
		//SE FILTRA POR Autor
		$autor = explode("-", Validador::validarParametro('c_autor'));
		$filtro['c_autor_tipo'] = $autor[0];
		$filtro['c_autor_codigo'] = $autor[1];
		
		//SE FILTRA POR Fecha desde Y Fecha hasta
		if ($this->esFechaValida(Validador::validarParametro('c_fecha_desde'))){
			$filtro['c_fecha_desde'] = $modelo->formatearFechaMySQL(Validador::validarParametro('c_fecha_desde'));
		}
		if ($this->esFechaValida(Validador::validarParametro('c_fecha_hasta'))){
			$filtro['c_fecha_hasta'] = $modelo->formatearFechaMySQL(Validador::validarParametro('c_fecha_hasta'));
		}
		
		$filtro['c_categoria'] = Validador::validarParametro('c_categoria', 0);	//SE FILTRA POR Categoria
		$filtro['c_tema'] = Validador::validarParametro('c_tema', 0);		//SE FILTRA POR Tema
		$filtro['c_opcionsp'] = Validador::validarParametro('c_opcionsp');	//SE FILTRA POR Sancionado O Promulgado
		//fputs(fopen('filtro_armar_listado_completo_Controller.txt','w'),print_r($filtro, true));

		// SE ESTABLECE EL FILTRO PARA LA QUERY DEL MODELO
		$modelo->setFiltro($filtro);
		// SE OBTIENE EL LISTADO COMPLETO EN EL MODELO
		$listado = $modelo->armar_listado_para_reporte();
		//fputs(fopen('listado_armar_listado_completo_Controller.txt', 'w'),print_r($listado, true));

		$vista = new VistaSancionadoPromulgado();
		if ($formato == "impresion"){
			$vista->generar_formato_para_impresion($listado, $filtro);
		}elseif ($formato == "texto"){
			$vista->procesar_texto($listado, $filtro);
		}
    }

    //SE DESSERIALIZA UN ARRAY RECIBIDO CON LA INFORMACION (SE LO CONVIERTE A SU VALOR PHP) 
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
	
    public function refrescarComboIniciadores()
    {
		$habilitado = Validador::validarParametro('habilitado');
		$iniciador = Validador::validarParametro('iniciador');
		
		//Se crea una instancia del modelo
		$modelo = new consultaGralModel();
		$listado = $modelo->obtenerIniciadores($habilitado);
		
		//Se crea una instancia de la "vista"
		$vista = new VistaSancionadoPromulgado();
		$vista->comboIniciadores($listado, $iniciador);
    }
    
    public function refrescarComboAutores()
    {
		$habilitado = Validador::validarParametro('habilitado');
		$autor = Validador::validarParametro('autor');
		
		//Se crea una instancia del modelo
		$modelo = new consultaGralModel();
		$listado = $modelo->obtenerAutores($habilitado);
		
		//Se crea una instancia de la "vista"
		$vista = new VistaSancionadoPromulgado();
		$vista->comboAutores($listado, $autor);
    }
    /**
    public function refrescarComboComisiones()
    {
		$habilitado = Validador::validarParametro('habilitado');
		$comision = Validador::validarParametro('comision');
		
		//Se crea una instancia del modelo
		$modelo = new consultaGralModel();
		$listado = $modelo->obtenerComisiones($habilitado);
		
		//Se crea una instancia de la "vista"
		$vista = new VistaConsultaGral();
		$vista->comboComisiones($listado, $comision);
    }
    
    public function refrescarComboCategorias()
    {
		$habilitado = Validador::validarParametro('habilitado');
		$categoria = Validador::validarParametro('categoria');
		
		//Se crea una instancia del modelo
		$modelo = new consultaGralModel();
		$listado = $modelo->obtenerCategorias($habilitado);
		
		//Se crea una instancia de la "vista"
		$vista = new VistaConsultaGral();
		$vista->comboCategorias($listado, $categoria);
    }
    
    public function refrescarComboTemas()
    {
		$habilitado = Validador::validarParametro('habilitado');
		$tema = Validador::validarParametro('tema');
		
		//Se crea una instancia del modelo
		$modelo = new consultaGralModel();
		$listado = $modelo->obtenerTemas($habilitado);
		
		//Se crea una instancia de la "vista"
		$vista = new VistaConsultaGral();
		$vista->comboTemas($listado, $tema);
    }
    
    public function refrescarComboEstados()
    {
		$habilitado = Validador::validarParametro('habilitado');
		$estado = Validador::validarParametro('estado');
		
		//Se crea una instancia del modelo
		$modelo = new consultaGralModel();
		$listado = $modelo->obtenerEstados($habilitado);
		
		//Se crea una instancia de la "vista"
		$vista = new VistaConsultaGral();
		$vista->comboEstados($listado, $estado);
    }
    /**/
}
?>

<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//Incluye el modelo que corresponde
require 'modelos/consulta_gral.php';

//Incluye la vista que corresponde
require 'vistas/consulta_gral.php';

class consulta_gral_controller extends ControllerBase
{
    private $listadoIniciadores = null;
    private $listadoAutores = null;
    private $listadoComisiones = null;
    private $listadoCategorias = null;
    private $listadoTemas = null;
    private $listadoEstados = null;
    
    public function cargarCombos()
    {
		//Se crea una instancia del modelo ConsultaGral para cargar los combos
		$modelo = new consultaGralModel();
		
		//Se le pide al modelo todos los iniciadores
		$this->listadoIniciadores = $modelo->obtenerIniciadores();
		//fputs(fopen('listadoIniciadores.txt','w'),print_r($listadoIniciadores, true));
		
		//Se le pide al modelo todos los autores
		$this->listadoAutores = $modelo->obtenerAutores();
		//fputs(fopen('listadoAutores.txt','w'),print_r($listadoAutores, true));
		
		//Se le pide al modelo todas las comisiones
		$this->listadoComisiones = $modelo->obtenerComisiones();
		//fputs(fopen('listadoComisiones.txt','w'),print_r($listadoComisiones, true));
		
		//Se le pide al modelo todos las categorias
		$this->listadoCategorias = $modelo->obtenerCategorias();
		//fputs(fopen('listadoCategorias.txt','w'),print_r($listadoCategorias, true));
		
		//Se le pide al modelo todos los temas
		$this->listadoTemas = $modelo->obtenerTemas();
		//fputs(fopen('listadoTemas.txt','w'),print_r($listadoTemas, true));
		
		//Se le pide al modelo todos los estados
		$this->listadoEstados = $modelo->obtenerEstados();
		//fputs(fopen('listadoEstados.txt','w'),print_r($listadoEstados, true));
    }
	
    public function listar_principal($mensaje = '')
    {
		// Se cargan los combos para la busqueda en la vista
		$this->cargarCombos();
		//Se crea una instancia del modelo ConsultaGral para armar el filtro
		$modelo = new consultaGralModel();
	/***************************************************************************************************
		SE ARMA EL FILTRO PARA LA VISTA
	***************************************************************************************************/				
		$filtro = Array();
		
		$filtro['c_enviado'] = Validador::validarParametro('c_enviado');
		$filtro['c_boton_presionado'] = Validador::validarParametro('c_boton_presionado');
		
		// PARA VOLVER A MOSTRAR LOS DATOS DE LA BUSQUEDA EN LOS COMBOS DE LA VISTA
		$filtro['c_iniciado'] = Validador::validarParametro('c_iniciado');
		$filtro['c_autor'] = Validador::validarParametro('c_autor');
		$filtro['c_comision'] = Validador::validarParametro('c_comision');
		$filtro['c_categoria'] = Validador::validarParametro('c_categoria');// SE FILTRA POR Categoria
		$filtro['c_tema'] = Validador::validarParametro('c_tema');			// SE FILTRA POR Tema
		$filtro['c_estado'] = Validador::validarParametro('c_estado');		// SE FILTRA POR Estado
		$filtro['c_palabra'] = Validador::validarParametro('c_palabra');	// SE FILTRA POR Palabra ingresada(extracto del proyecto)
		
		// PARA MOSTRAR LOS HABILITADOS O NO EN LOS COMBOS DEL BUSCADOR
		$filtro['c_solo_habilitado'] = Validador::validarParametro('c_solo_habilitado');
		
		// 24/04/2015, SI SE MUESTRAN SOLO LOS EXPEDIENTES SIN PROYECTO CARGADO
		$filtro['c_sin_proyecto_cargado'] = Validador::validarParametro('c_sin_proyecto_cargado');
			
		// SE FILTRA POR Iniciador SE SEPARA EN TIPO Y CODIGO
		$iniciado = explode("-", Validador::validarParametro('c_iniciado'));
		$filtro['c_iniciado_tipo'] = $iniciado[0];
		$filtro['c_iniciado_codigo'] = $iniciado[1];
		
		// SE FILTRA POR Autor SE SEPARA EN TIPO Y CODIGO
		$autor = explode("-", Validador::validarParametro('c_autor'));
		$filtro['c_autor_tipo'] = $autor[0];
		$filtro['c_autor_codigo'] = $autor[1];
		
		//SE FILTRA POR Comision SE SEPARA EN TIPO Y CODIGO
		$comision = explode("-", Validador::validarParametro('c_comision'));
		$filtro['c_comision_tipo'] = $comision[0];
		$filtro['c_comision_codigo'] = $comision[1];
		
		// SE FILTRA POR Fecha desde Y Fecha hasta
		if ($this->esFechaValida(Validador::validarParametro('c_fecha_desde'))){
			$filtro['c_fecha_desde'] = $modelo->formatearFechaMySQL(Validador::validarParametro('c_fecha_desde'));
		}
		if ($this->esFechaValida(Validador::validarParametro('c_fecha_hasta'))){
			$filtro['c_fecha_hasta'] = $modelo->formatearFechaMySQL(Validador::validarParametro('c_fecha_hasta'));
		}
			
		$filtro['c_rango'] = 5;	//cantidad de registros a mostrar
		$filtro['c_pagina'] = Validador::validarParametro('c_pagina');	//se obtiene el valor de la pagina
					
		if ($filtro['c_pagina'] == '')
		{	//si no se sabe el valor de la pagina
			$filtro['c_inicio'] = 0;	//se inicia en el primer registro
			$filtro['c_pagina'] = 1;	//con la primer pagina 
		}
		else
		{	//si se conoce se calcula el valor del registro inicial de dicha pagina 
			$filtro['c_inicio'] = ($filtro['c_pagina'] * $filtro['c_rango']) - $filtro['c_rango'];
		} 
		$filtro['c_pagina_ant'] = $filtro['c_pagina'] - 1;		//para la pagina anterior
		$filtro['c_pagina_sgte'] = $filtro['c_pagina'] + 1;		//para la pagina posterior
		
		//fputs(fopen('filtroConsultaGralC.txt','w'),print_r($filtro, true));
		
	/***************************************************************************************************/			

		if ($filtro['c_enviado'] == 'enviado')
		{
			// 04/01/2012: SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
			$_SESSION['filtro_cta_gral'] = $filtro;
			//fputs(fopen("SESSION_ConsultaGralC.txt",'w'),print_r($_SESSION['filtro_cta_gral'], true));
			
			// SE ESTABLECE EL FILTRO PARA LA QUERY DEL LISTADO
			$modelo->setFiltro($filtro);
			// SE REALIZA LA BUSQUEDA EN EL MODELO
			$listado = $modelo->listar();
			//fputs(fopen('listado_Consulta_General.txt', 'w'),print_r($listado, true));
			
			
			/**
			// 27/04/2015, SI SE DESEAN FILTRAR SOLO AQUELLOS DOCUMENTOS QUE ESTEN SIN CARGAR
			if ( $filtro['c_sin_proyecto_cargado'] == 1 )
			{
				$cantidad = count($listado);
				$pos = 0;
				for ($i=0; $i < $cantidad; $i++)
				{
					// SE VERIFICA EL ESTADO DEL PROYECTO (ARCHIVO original.doc) EN EL EXPEDIENTE
					//1 = PARA CARGAR, 2 = CARGADO, 3 = SIN CARGAR
					$estado_doc = $this->verificarEstadoDoc($listado[$i]);
					//fputs(fopen("estado_doc_Consulta_General_controller.txt", 'w'),print_r($estado_doc, true));
					
					// SI ESTA SIN CARGAR EL DOCUMENTO
					if ( $estado_doc == 3 )
					{
						$listado_sin_cargar[$pos] = $listado[$i];
						$pos++;
					}
				}
				
				// SI SE ENCONTRO POR LO MENOS UN DOCUMENTO SIN CARGAR
				if ( $listado_sin_cargar[0]['anio'] != '' )
				{
					// SE REDEFINE EL LISTADO DE RESULTADOS
					$listado = $listado_sin_cargar;
					// SE REDEFINE LA CANTIDAD DEL RESULTADO
					$filtro['c_cantidad'] = $_SESSION['total'];//count($listado)
				}
				else
				{
					// SI NO SE ENCONTRO NINGUNO SIN CARGAR, NO HAY NADA QUE MOSTRAR
					$listado = null;
					
					$filtro['c_cantidad'] = 0;
				}
				
				fputs(fopen("listado_sin_cargar.txt",'w'), print_r($listado, true));
			}
			else
			{
				// SE GUARDA LA CANTIDAD TOTAL DE EXPEDIENTES DEVUELTOS(SIN TOMAR EN CUENTA EL LIMIT)
				// DICHO VALOR SE OBTUVO EN EL Modelo
				$filtro['c_cantidad'] = $_SESSION['total'];
			}
			/**/
			
			// SE GUARDA LA CANTIDAD TOTAL DE EXPEDIENTES DEVUELTOS(SIN TOMAR EN CUENTA EL LIMIT)
			// DICHO VALOR SE OBTUVO EN EL Modelo
			$filtro['c_cantidad'] = $_SESSION['total'];
			
			$filtro['c_nro_paginas'] = 1;
			if ($filtro['c_cantidad'] > 5)
			{
				// NUMERO TOTAL DE PAGINAS (DE 5 expedientes CADA UNA)
				$filtro['c_nro_paginas'] = ceil($filtro['c_cantidad'] / $filtro['c_rango']);
			}
			//fputs(fopen('filtro_exped_consulta_general.txt', 'w'),print_r($filtro, true));
		}	

		$vista = new VistaConsultaGral();
		//se muestra el listado
		$vista->listar_principal($listado, $this->listadoIniciadores, $this->listadoAutores, $this->listadoComisiones, $this->listadoCategorias, $this->listadoTemas, $this->listadoEstados, $mensaje, $filtro);
    }	
	
    /**
     * SE OBTIENE EL LISTADO COMPLETO PARA GENERAR EL FORMATO DE IMPRESION Y EL DOCUMENTO DE TEXTO
     */
    public function armar_listado_completo()
	{
		$modelo = new consultaGralModel();

		// SE ARMA EL FILTRO PARA LA VISTA
		$filtro = Array();

		// PARA UTILIZAR LA VISTA CORRESPONDIENTE, EL FORMATO DE IMPRESION O EL DOCUMENTO DE TEXTO
		$formato = Validador::validarParametro('formato');

		// SE FILTRA POR Iniciador
		$iniciado = explode("-", Validador::validarParametro('c_iniciado'));
		$filtro['c_iniciado_tipo'] = $iniciado[0];
		$filtro['c_iniciado_codigo'] = $iniciado[1];
		
		// SE FILTRA POR Autor
		$autor = explode("-", Validador::validarParametro('c_autor'));
		$filtro['c_autor_tipo'] = $autor[0];
		$filtro['c_autor_codigo'] = $autor[1];
		
		// SE FILTRA POR Comision
		$comision = explode("-", Validador::validarParametro('c_comision'));
		$filtro['c_comision_tipo'] = $comision[0];
		$filtro['c_comision_codigo'] = $comision[1];

		// SE FILTRA POR Fecha desde Y Fecha hasta
		if ($this->esFechaValida(Validador::validarParametro('c_fecha_desde'))){
			$filtro['c_fecha_desde'] = $modelo->formatearFechaMySQL(Validador::validarParametro('c_fecha_desde'));
		}
		if ($this->esFechaValida(Validador::validarParametro('c_fecha_hasta'))){
			$filtro['c_fecha_hasta'] = $modelo->formatearFechaMySQL(Validador::validarParametro('c_fecha_hasta'));
		}
		
		$filtro['c_categoria'] = Validador::validarParametro('c_categoria', 0);	// SE FILTRA POR Categoria
		$filtro['c_tema'] = Validador::validarParametro('c_tema', 0);			// SE FILTRA POR Tema
		$filtro['c_estado'] = Validador::validarParametro('c_estado', 0);		// SE FILTRA POR Estado

		// SE FILTRA POR Palabra (Carátula o Extracto del proyecto)
		$palabra = Validador::validarParametro('c_palabra');
		if (!empty($palabra)){
			$filtro['c_palabra'] = $palabra;
		}	
		
		// SE ESTABLECE EL FILTRO PARA LA QUERY DEL MODELO
		$modelo->setFiltro($filtro);
		
		// SE OBTIENE EL LISTADO COMPLETO EN EL MODELO
		$listado = $modelo->armar_listado_para_reporte();// armar_listado_completo
		
		$vista = new VistaConsultaGral();
		
		if ( $formato == "impresion" )
		{
			$vista->generar_formato_para_impresion($listado, $filtro);
		}
		elseif ( $formato == "texto" )
		{
			$vista->procesar_texto($listado, $filtro);
		}
		elseif ( $formato == "csv" )
		{
			$vista->generarCSV_ConsultaGeneral($listado, $filtro);
		}
    }

    /**
     *  SE DESERIALIZA UN ARRAY RECIBIDO CON LA INFORMACION (SE LO CONVIERTE A SU VALOR PHP)
     *  
     * @param unknown $url_array
     * @return mixed
     */
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
		$vista = new VistaConsultaGral();
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
		$vista = new VistaConsultaGral();
		$vista->comboAutores($listado, $autor);
    }
   
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
    
	public function buscarPorNombreModal()
	{	
	    $modal_descripcion_tema = Validador::validarParametro('modal_descripcion_tema');
	    
		$modeloCodTema = new codtemasModel();
		$listadoTemas = $modelo->buscarPorNombreModal($modal_descripcion_tema);
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
    
	public function pedirNombreIniciadorModal()
	{
	    $c_solo_habilitado = Validador::validarParametro('c_solo_habilitado');
		
	    $modelo = new consultaGralModel();
		$this->listadoIniciadores = $modelo->obtenerIniciadores($c_solo_habilitado);
	    
	    $vista = new VistaConsultaGral();
	    $vista->pedirNombreIniciadorModal($this->listadoIniciadores);
	}
	
	public function pedirNombreAutorModal()
	{
	    $c_solo_habilitado = Validador::validarParametro('c_solo_habilitado');
		
	    $modelo = new consultaGralModel();
		$this->listadoAutores = $modelo->obtenerAutores($c_solo_habilitado);
	    
	    $vista = new VistaConsultaGral();
	    $vista->pedirNombreAutorModal($this->listadoAutores);
	}
	
	public function pedirNombreComisionModal()
	{
	    $c_solo_habilitado = Validador::validarParametro('c_solo_habilitado');
		
	    $modelo = new consultaGralModel();
		$this->listadoComisiones = $modelo->obtenerComisiones($c_solo_habilitado);
	    
	    $vista = new VistaConsultaGral();
	    $vista->pedirNombreComisionModal($this->listadoComisiones);
	}
	
}
?>

<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//require '../../librerias/control_duracion_sesion.php';
//Incluye el modelo que corresponde
require 'modelos/antecedentes.php';
require 'modelos/expedientes.php';

//Incluye la vista que corresponde
require 'vistas/antecedentes.php';
require 'vistas/expedientes.php';

class antecedentes_controller extends ControllerBase
{
	public function esIgual($datos)
	{
		//fputs(fopen('datos_esIgualAntecedentesC.txt','w'),print_r($datos,true));
		if ($datos['anio'] != $datos['anio_a']){
			return false;
		}
		if ($datos['tipo'] != $datos['tipo_a']){
			return false;
		}
		if ($datos['numero'] != $datos['numero_a']){
			return false;
		}
		if ($datos['cuerpo'] != $datos['cuerpo_a']){
			return false;
		}
		if ($datos['alcance'] != $datos['alcance_a']){
			return false;
		}
		return true;
	}
	
	public function guardarRegistroOriginal($original)
	{
		$_SESSION['anio_original'] = $original[0]['anio'];
		$_SESSION['tipo_original'] = $original[0]['tipo'];
		$_SESSION['numero_original'] = $original[0]['numero'];
		$_SESSION['cuerpo_original'] = $original[0]['cuerpo'];
		$_SESSION['alcance_original'] = $original[0]['alcance'];
		$_SESSION['anio_a_original'] = $original[0]['anio_a'];
		$_SESSION['tipo_a_original'] = $original[0]['tipo_a'];
		$_SESSION['numero_a_original'] = $original[0]['numero_a'];
		$_SESSION['digito_a_original'] = $original[0]['digito_a'];
		$_SESSION['cuerpo_a_original'] = $original[0]['cuerpo_a'];
		$_SESSION['alcance_a_original'] = $original[0]['alcance_a'];
		$_SESSION['cuerpoalcance_a_original'] = $original[0]['cuerpoalcance_a'];
		$_SESSION['anexoalcance_a_original'] = $original[0]['anexoalcance_a'];
		$_SESSION['cuerpoanexoalcance_a_original'] = $original[0]['cuerpoanexoalcance_a'];
		$_SESSION['anexo_a_original'] = $original[0]['anexo_a'];
		$_SESSION['cuerpoanexo_a_original'] = $original[0]['cuerpoanexo_a'];
		$_SESSION['observaciones_antecedentes_original'] = $original[0]['observaciones_antecedentes'];
		$_SESSION['id_usuario_original'] = $original[0]['id_usuario'];
	
		//fputs(fopen('SESSION_RegistroOriginalAntecedenteC.txt','w'),print_r($_SESSION,true));
	}
	
	public function listar($mensaje = '', $tipo_mensaje = '', $clave = '')
	{			
		//Se crea una instancia del modelo
		$modelo = new antecedentesModel();
		
		$filtro = Array();
		if ( !empty($clave) )
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
			
			// 11/07/2012
			$mensaje = Validador::validarParametro('mensaje');
			$tipo_mensaje = Validador::validarParametro('tipo_mensaje');
		}
		//se establece el campo por el cual ordenar
		$campo_orden = Validador::validarParametro('campo_orden');
		if ( !empty($campo_orden) )
		{
			$filtro['campo_orden'] = $campo_orden;
		}else{
			//por defecto
			$filtro['campo_orden'] = 'anio';
			$_SESSION['ultimo_campo'] = '';
		}
		
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
			if ($_SESSION['ultimo_sentido'] == 'asc') {
				$_SESSION['ultimo_sentido'] = 'desc';
			} else {
				$_SESSION['ultimo_sentido'] = 'asc';
			}
		}
		
		$filtro['rango'] = 10;	//cantidad de registros a mostrar
		$filtro['pagina'] = Validador::validarParametro('pagina');	//se obtiene el valor de la pagina
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
		
		//fputs(fopen('filtroAntecedentesListarC.txt','w'),print_r($filtro,true));	
		
		//Se establece el filtro en el modelo
		$modelo->setFiltro($filtro);
 		//Se le pide al modelo todos los items
		$listado = $modelo->listado();
		//fputs(fopen('MOSTRAR_listadoAntecedentes.txt','w'),print_r($listado,true));
		
 		//Se crea una instancia de la vista
		$vista = new VistaAntecedentes();
		//se muestra el listado
		$vista->listar($listado, $mensaje, $tipo_mensaje, $filtro);
	}
		
	public function editar($clave = '')
	{	
		//Se crea una instancia del modelo
		$modelo = new antecedentesModel();
		
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
			$filtro['anio_a'] = Validador::validarParametro('anio_a');
			$filtro['tipo_a'] = Validador::validarParametro('tipo_a');
			$filtro['numero_a'] = Validador::validarParametro('numero_a');
			$filtro['cuerpo_a'] = Validador::validarParametro('cuerpo_a');
			$filtro['alcance_a'] = Validador::validarParametro('alcance_a');
		}
		
		//se establece el campo por el cual ordenar
		$campo_orden = Validador::validarParametro('campo_orden');
		if (!empty($campo_orden)){
			$filtro['campo_orden'] = $campo_orden;
		}else{
			//por defecto
			$filtro['campo_orden'] = 'anio';
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
		$filtro['rango'] = 12;	//cantidad de registros a mostrar
		$filtro['pagina'] = Validador::validarParametro('pagina');	//se obtiene el valor de la pagina
		if (!$filtro['pagina']){	//si no se sabe el valor de la pagina
			$filtro['inicio'] = 0;	//se inicia en el primer registro
			$filtro['pagina'] = 1;	//con la primer pagina 
		}else{	//si se conoce se calcula el valor del registro inicial de dicha pagina 
			$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
		} 
		$filtro['pagina_ant'] = $filtro['pagina'] - 1;		//para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;		//para la pagina posterior
		//Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
		$filtro['cantidad'] = $modelo->obtenerCantidad();
		//NUMERO TOTAL DE PAGINAS 
		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);
		
		//fputs(fopen('MOSTRAR_filtroAntecedentesEditarC.txt','w'),print_r($filtro,true));	
		
		//Se establece el filtro en el modelo
		$modelo->setFiltro($filtro);
 		//Se le pide al modelo todos los items
		$listado = $modelo->listado();
		//fputs(fopen('listado_antecedentes.txt','w'),print_r($listado,true));
		
		// SE GUARDA EL REGISTRO EN SESION PARA VERIFICAR 
		// LUEGO SI LO HA MODIFICADO OTRO USUARIO
		$this->guardarRegistroOriginal($listado);
		//Se le pide al modelo todos los Antecedentes relacionados
		$listadoRelacionados = $modelo->listadoRelacionados();
		
		//Se crea una instancia de la "vista"
		$vistaAntecedente = new VistaAntecedentes();
		//se muestra el listado
		$vistaAntecedente->editar($listado, $listadoRelacionados, $filtro);
	}
	
	public function agregar()
	{	
		$filtro = Array();
		$filtro['anio'] = Validador::validarParametro('anio');
		$filtro['tipo'] = Validador::validarParametro('tipo');
		$filtro['numero'] = Validador::validarParametro('numero');
		$filtro['cuerpo'] = Validador::validarParametro('cuerpo');
		$filtro['alcance'] = Validador::validarParametro('alcance');
		$filtro['por_boton_Agregar'] = Validador::validarParametro('por_boton_Agregar');
		//fputs(fopen('filtroAgregarAntecedente.txt','w'),print_r($filtro,true));
		
		//SI SE LLEGA DEL BOTON Agregar O DEL ICONO DE EDICION DEL LISTADO
		if ($filtro['por_boton_Agregar']){
			$_SESSION['por_boton_Agregar'] = true;
		}else{
			$_SESSION['por_boton_Agregar'] = false;
		}		
				
		//Se crea una instancia del modelo
		$modelo = new antecedentesModel();
		//Se establece el filtro en el modelo
		$modelo->setFiltro($filtro);
		//Se le pide al modelo los antecedentes Relacionados
		$antecedentes = $modelo->listadoRelacionados();
		
		//Se crea una instancia de la "vista"
		$vista = new VistaAntecedentes();
		//se muestra el formulario de Edicion
		$vista->editar(null, $antecedentes, $filtro);
	}
	
	public function validarDatos($datos)
	{	
		if (empty($datos['numero'])){
			$datos['numero'] = 0;
		}
		if (empty($datos['cuerpo'])){
			$datos['cuerpo'] = 0;
		}
		if (empty($datos['alcance'])){
			$datos['alcance'] = 0;
		}
		if (empty($datos['anio_a'])){
			$datos['anio_a'] = 0;
		}
		if (empty($datos['tipo_a'])){
			$datos['tipo_a'] = 0;
		}
		if (empty($datos['numero_a'])){
			$datos['numero_a'] = 0;
		}
		if ( $datos['digito_a'] == '' )
		{
			$datos['digito_a'] = '0';
		}
		if (empty($datos['cuerpo_a'])){
			$datos['cuerpo_a'] = 0;
		}
		if (empty($datos['alcance_a'])){
			$datos['alcance_a'] = 0;
		}
		if (empty($datos['cuerpoalcance_a'])){
			$datos['cuerpoalcance_a'] = 0;
		}
		if (empty($datos['anexoalcance_a'])){
			$datos['anexoalcance_a'] = 0;
		}
		if (empty($datos['cuerpoanexoalcance_a'])){
			$datos['cuerpoanexoalcance_a'] = 0;
		}
		if (empty($datos['anexo_a'])){
			$datos['anexo_a'] = 0;
		}
		if (empty($datos['cuerpoanexo_a'])){
			$datos['cuerpoanexo_a'] = 0;
		}
		if (empty($datos['observaciones_antecedentes'])){
			$datos['observaciones_antecedentes'] = null;
		}else{
			$datos['observaciones_antecedentes'] = trim($datos['observaciones_antecedentes']);
		}
		return $datos;
	}
	
	public function insertar()
	{
		$datos = $this->validarDatos($_REQUEST);
		
		//Se crea una instancia del modelo
		$modeloAntecedente = new antecedentesModel();
		
		// SI ES EXPEDIENTE O NOTA SE VERIFICA SU EXISTENCIA
		if ( $datos['tipo_a'] != 'D')
		{
		    // TIENE QUE EXISTIR EL EXPEDIENTE O NOTA PARA CARGARLO COMO ANTECEDENTE
		    if ( $modeloAntecedente->existe($datos) )
		    {	
				//PARA QUE DOS USUARIOS NO INGRESEN EL MISMO REGISTRO
				if (!$this->esIgual($datos))// SI ES DISTINTO AL EXPEDIENTE EN CURSO
				{
					if ( $modeloAntecedente->insertar($datos) )
					{
						//SI SE ESTA EDITANDO CON EL WIZARD Y NO SE LLEGO POR EL BOTON Agregar
						if ( $datos['por_boton_Agregar'] == 'false' )
						{
							//Se crea una instancia del modelo de Expedientes
							$modeloExpediente = new expedientesModel();
							//Se establece el filtro en el modelo
							$modeloExpediente->setFiltro($datos);
							//Se le pide al modelo todos los items
							$listado = $modeloExpediente->listadoTotal();
							
							//Aviso de ingreso satisfactorio del Expediente
							$mensaje = 'El antecedente se agregó con éxito.';
							$tipo_mensaje = 1;
							//Se crea una instancia de la vista
							$vistaExpediente = new VistaExpedientes();
							//se muestra el listado
							$vistaExpediente->listar($listado, $mensaje, $tipo_mensaje, $datos);
						}
						else
						{
							$mensaje = 'El antecedente se agregó con éxito.';
							$tipo_mensaje = 1;
							$this->listar($mensaje, $tipo_mensaje, $datos);// SE VUELVE AL LISTADO DE ANTECEDENTES DEL EXPEDIENTE EN CURSO
						}
					}
					else
					{
						$mensaje = 'Error al agregar el antecedente.';
						$tipo_mensaje = 2;
						$this->listar($mensaje, $tipo_mensaje, $datos);
					}
				}
				else
				{
					// MODIFICADO 01/12/2011
					if ( $datos['tipo_a'] == 'E' )
					{
						$mensaje = "EL Expediente tomado como Antecedente debe ser distinto al Expediente en curso.";
					}
					elseif ( $datos['tipo_a'] == 'N' )
					{
						$mensaje = "La Nota tomada como Antecedente debe ser distinta a la Nota en curso.";
					}
					$tipo_mensaje = 2;
					
					$this->listar($mensaje, $tipo_mensaje, $datos);// PARA VOLVER A LOS ANTECEDENTES DEL EXPEDIENTE EN CURSO
				}
		    }
		    else
		    {
				if ( $datos['tipo_a'] == 'E' )
				{
					$mensaje = 'El Expediente no existe para cargarlo como antecedente.';
				}
				elseif ( $datos['tipo_a'] == 'N' )
				{
					$mensaje = 'La Nota no existe para cargarla como antecedente.';
				}
				$tipo_mensaje = 2;
				$this->listar($mensaje, $tipo_mensaje, $datos);
		    }
		}
		else // SI ES DEL DEPARTAMENTO EJECUTIVO
		{
		    if ( !$this->esIgual($datos) )// SI ES DISTINTO AL EXPEDIENTE EN CURSO
		    {
				if ( $modeloAntecedente->insertar($datos) )
				{
					$mensaje = 'El antecedente se agregó con éxito.';
					$tipo_mensaje = 1;
				}
				else
				{
					$mensaje = 'Error al agregar el antecedente.';
					$tipo_mensaje = 2;
				}
		    }
		    else
		    {
				$mensaje = "EL Expediente tomado como Antecedente debe ser distinto al Expediente en curso.";
				$tipo_mensaje = 2;
		    }
		    $this->listar($mensaje, $tipo_mensaje, $datos);
		}
	}

	// MODIFICADO 01/12/2011
	public function modificar()
	{
		//Se crea una instancia del modelo
		$modeloAntecedente = new antecedentesModel();
		
		$datos = $this->validarDatos($_REQUEST);
	
		if ( $datos['tipo_a'] == 'E' )
		{
			$mensaje_segun_tipo_por_igualdad = "EL Expediente tomado como Antecedente debe ser distinto al Expediente en curso.";
			$mensaje_segun_tipo_por_existencia = "El Expediente tomado como Antecedente no existe.";
		}
		elseif ( $datos['tipo_a'] == 'N' )
		{
			$mensaje_segun_tipo_por_igualdad = "La Nota tomada como Antecedente debe ser distinta a la Nota en curso.";
			$mensaje_segun_tipo_por_existencia = "La Nota tomada como Antecedente no existe.";
		}
		
		// SI ES DEL EJECUTIVO NO SE VERIFICA LA EXISTENCIA
		if ( $datos['tipo_a'] == 'D' )
		{
			if ( !$this->esIgual($datos) )// SI ES DISTINTO AL EXPEDIENTE EN CURSO
			{
				if ( $modeloAntecedente->modificar($datos) )
				{
					$mensaje = 'El Antecedente se modificó con éxito.';
					$tipo_mensaje = 1;
				}
				else
				{
					$mensaje = 'Error al modificar el Antecedente, pudo haber sido modificado previamente.';
					$tipo_mensaje = 2;
				}
			}
			else
			{
				$mensaje = "EL Expediente tomado como Antecedente debe ser distinto al Expediente en curso.";
				$tipo_mensaje = 2;
			}
		}
		else // SINO SE SIGUE VERIFICANDO
		{
			if ($modeloAntecedente->existe($datos))// SE VERIFICA SI EL EXPEDIENTE TOMADO COMO ANTECEDENTE EXISTE
			{
				if ( !$this->esIgual($datos) )// SI ES DISTINTO AL EXPEDIENTE EN CURSO
				{
					if ($modeloAntecedente->modificar($datos))
					{
						$mensaje = 'El Antecedente se modificó con éxito.';
						$tipo_mensaje = 1;
					}
					else
					{
						$mensaje = 'Error al modificar el Antecedente, pudo haber sido modificado previamente.';
						$tipo_mensaje = 2;
					}
				}
				else
				{
					$mensaje = $mensaje_segun_tipo_por_igualdad;
					$tipo_mensaje = 2;
				}	
			}
			else
			{
				$mensaje = $mensaje_segun_tipo_por_existencia;
				$tipo_mensaje = 2;
			}
		}
		
		$this->listar($mensaje, $tipo_mensaje, $datos);
	}
	
	public function eliminar()
	{
		$clave = Array();
		$clave['anio'] = Validador::validarParametro('anio');
		$clave['tipo'] = Validador::validarParametro('tipo');
		$clave['numero'] = Validador::validarParametro('numero');
		$clave['cuerpo'] = Validador::validarParametro('cuerpo');
		$clave['alcance'] = Validador::validarParametro('alcance');
		$clave['anio_a'] = Validador::validarParametro('anio_a');
		$clave['tipo_a'] = Validador::validarParametro('tipo_a');
		$clave['numero_a'] = Validador::validarParametro('numero_a');
		$clave['digito_a'] = Validador::validarParametro('digito_a');
		$clave['cuerpo_a'] = Validador::validarParametro('cuerpo_a');
		$clave['alcance_a'] = Validador::validarParametro('alcance_a');
		$clave['cuerpoalcance_a'] = Validador::validarParametro('cuerpoalcance_a');
		$clave['anexoalcance_a'] = Validador::validarParametro('anexoalcance_a');
		$clave['cuerpoanexoalcance_a'] = Validador::validarParametro('cuerpoanexoalcance_a');
		$clave['anexo_a'] = Validador::validarParametro('anexo_a');
		$clave['cuerpoanexo_a'] = Validador::validarParametro('cuerpoanexo_a');
		
		//fputs(fopen('claveEliminarAntecedente.txt','w'),print_r($clave,true));
		//Se crea una instancia del modelo
		$modeloAntecedente = new antecedentesModel();
 		
		if ( $modeloAntecedente->eliminar($clave) )
		{
			$mensaje = 'El Antecedente se eliminó con éxito.';
			$tipo_mensaje = 1;
		}
		else
		{
			$mensaje = 'Error al eliminar el Antecedente.';
			$tipo_mensaje = 2;
		}
		
		$this->listar($mensaje, $tipo_mensaje, $clave);
	}

}
?>

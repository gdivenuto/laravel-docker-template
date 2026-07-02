<?php
if (!isset($_SESSION)) {
	session_start();
}

abstract class ControllerBase
{
    /**
     * Coleccion de parametros de llamada al controlador.
     * @var array
     */
	protected $parametros;
	protected $ruta_proyectos;
	protected $modelo;
	protected $vista;

    public function __construct()
    {
    	// Inicializo la coleccion de parametros
    	$this->parametros     = array();
    	$this->ruta_proyectos = "/var/www/sgl/expedientes/proyectos/";
    }

    /**
     * Se muestra un mensaje de Permiso Denegado
     */
    public function informarPermisoDenegado()
    {
    ?>
    	<div class="contenedor_mensaje_permiso_denegado">Permiso denegado!</div>
    <?php
    }

    /**
     * Se genera un archivo de log, utilizando la clase Logger.
	 *
     * Ejemplo: Se guarda el array $filtro_busqueda en un archivo de Log
	 * $this->Log("filtro_busqueda", $filtro_busqueda);
	 *
     * @param string $identificador
     * @param string|integer|array $data
     * @param bool $incremental
     */
	public function LogControlador($identificador, $data, $incremental = true)
    {
    	// Se obtiene el nombre de la clase hija, utilizando this como parámetro en el método get_class()
    	$nombre_clase_hija = get_class($this);

    	// Se obtiene un rastreo de PHP
    	$backtrace = debug_backtrace();

    	// Se toma el nombre del método invocado
    	$metodo = $backtrace[1]['function'];

    	Logger::GetInstance()->Log($nombre_clase_hija.'_'.$metodo.'_'.$identificador, $data, $incremental);
    }

    /**
     * Asigna un valor a un parametro del controlador, normalizando los nombres de los parámetros a minúsculas.
     * Si el parametro no existe, lo agrega.
     * @param string $nombre_parametro Parametro a modificar
     * @param mixed $valor Valor a guardar.
     */
	public function parametrosAsignar($nombre_parametro, $valor)
	{
		$this->parametros[strtolower(trim($nombre_parametro))] = $valor;
	}

	/**
	 * Asigna una coleccion de parametros a los parametros del controlador, normalizando
	 * los nombres de los parámetros a minúsculas.
	 * @param array $parametros
	 */
	public function parametrosAsignarColeccion($parametros)
	{
		if (is_array($parametros))
			foreach ($parametros as $key => $value)
				$this->parametrosAsignar($key, $value);
	}

	/**
	 * Obtiene un parametro de la lista de parametros del controlador.
	 * Si el parametro no existe, lanza una excepcion.
	 * @param string $nombre_parametro Nombre del parametro, normalizado a minúsculas.
	 * @throws Exception
	 */
	protected function parametrosObtener($nombre_parametro)
	{
		// Normalizo el nombre del parametro
		$nombre_parametro = strtolower(trim($nombre_parametro));

		// Debe existir en el array de parametros o lanzo una excepcion
		if (array_key_exists($nombre_parametro, $this->parametros))
		{
			return $this->parametros[$nombre_parametro];
		}
		else
		{
			throw new Exception("No se encuentra el parámetro ".$nombre_parametro);
		}
	}

	/**
	 * Obtiene un subconjunto de parametros de la coleccion de parametros del controlador.
	 * Si un parametro no se encuentra, lanza una excepcion.
	 * @param array $parametros_a_obtener Nombre de los parametros a obtener
	 * @return multitype:NULL
	 */
	protected function parametrosObtenerColeccion($parametros_a_obtener)
	{
		$resultado = array();

		if (is_array($parametros_a_obtener))
			foreach ($parametros_a_obtener as $value)
				$resultado[$value] = $this->parametrosObtener($value);

		return $resultado;
	}

	/**
	 * Obtiene un subconjunto de parametros de la coleccion de parametros del controlador.
	 * Si un parametro no se encuentra, lo devuelve como null.
	 * @param array $parametros_a_obtener Nombre de los parametros a obtener
	 * @return array:NULL
	 */
	protected function parametrosObtenerColeccionONull($parametros_a_obtener)
	{
		$resultado = array();

		if (is_array($parametros_a_obtener))
			foreach ($parametros_a_obtener as $value)
				$resultado[$value] = $this->parametrosObtenerONull($value);

			return $resultado;
	}

	/**
	 * Obtiene un parametro de la lista de parametros del controlador.
	 * Si el parametro no existe, devuelve NULL.
	 * @param string $nombre_parametro
	 * @return array:NULL
	 */
	protected function parametrosObtenerONull($nombre_parametro)
	{
		try
		{
			return $this->parametrosObtener($nombre_parametro);
		}
		catch (Exception $e)
		{
			return null;
		}
	}

	/**
	 * Obtiene un parametro de la lista de parametros del controlador.
	 * Si el parametro no existe, devuelve NULL.
	 * @param string $nombre_parametro
	 * @return array:NULL
	 */
	protected function parametrosObtenerONullSiVacio($nombre_parametro)
	{
		$resultado = $this->parametrosObtenerONull($nombre_parametro);
		return ($resultado == '') ? null : $resultado;
	}

	/**
	 * Valida todos los parametros del controlador.
	 */
	protected function parametrosValidar()
	{
		return true;
	}

	/**
	 * Verifica la existencia de un conjunto de parametros. Devuelve true si se encuentran todos,
	 * false en caso de que al menos uno no exista.
	 * @param array $parametros_a_verificar Arreglo con los nombres de parametros a verificar.
	 * @return boolean
	 */
	protected function parametrosVerificarParametros($parametros_a_verificar)
	{
		if (is_array($parametros_a_verificar))
		{
			foreach ($parametros_a_verificar as $key => $value)
				if ($this->parametrosObtenerONull($value) === null)
					return false;

			return true;
		}
		else
			return false;
	}

	/**
	 * Recibe una fecha para verificar si es válida
	 * la fecha tiene el formato dd/mm/yyyy
	 * @param string $fecha
	 * @return boolean
	 */
    public function esFechaValida($fecha)
	{
		if ( $fecha !== null || $fecha != '' )
		{
			$fec_partes = explode("/",$fecha);
			$mes   = (isset($fec_partes[1])) ? $fec_partes[1] : '';
			$dia   = (isset($fec_partes[0])) ? $fec_partes[0] : '';
			$anio  = (isset($fec_partes[2])) ? $fec_partes[2] : '';

			if ( $mes !== null && $mes != '' )
			{
				if ( $dia !== null && $dia != '' )
				{
					if ( $anio !== null && $anio != '' )
					{
						return checkdate( $mes, $dia, $anio );
					}
					else
					{
						return false;
					}
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
    }

    /**
     * Recibe una cadena para cortar a partir de un valor el cual indica la posición limite para dicho corte
     * @param string $cadena
     * @param integer $limite_cadena
     * @return string
     */
	public function cortaCadena($cadena, $limite_cadena)
	{
	    if ( substr($cadena,$limite_cadena-1,1) != '' )
	    {
			$cadena = substr($cadena,'0',$limite_cadena);
			$array = explode(' ',$cadena);
			array_pop($array);
			$nueva_cadena = implode(' ',$array);

			return $nueva_cadena.' ...';
	    }
	    else{
			return substr($cadena,'0',$limite_cadena-1).' ...';
	    }
	}

    public function configurarPaginacion($filtro, $modelo, $campo_orden_por_defecto, $cantidad_registros_a_mostrar)
    {
		// SI SE RECIBE UN MENSAJE DEL RESULTADO DE UNA OPERACION REALIZADA
		if ( Validador::validarParametro('mensaje') )
		{
			$mensaje = Validador::validarParametro('mensaje');
		}
		// SE SETEA EL VALOR A BUSCAR
		$valor_buscado = Validador::validarParametro('valor_buscado');
		if ( !empty($valor_buscado ) )
		{
			$filtro['valor_buscado'] = $valor_buscado;
		}
		else
		{
			$filtro['valor_buscado'] = '';
		}

		// SE SETEA EL CAMPO POR EL CUAL ORDENAR
		$campo_orden = Validador::validarParametro('campo_orden');
		if ( !empty($campo_orden) )
		{
			$filtro['campo_orden'] = $campo_orden;
		}
		else
		{
			//por defecto
			$filtro['campo_orden'] = $campo_orden_por_defecto;
			$_SESSION['ultimo_campo'] = '';
		}

		//DIRECCION PARA LA PAGINACION (PRIMERO, ANTERIOR, SGTE., ULTIMO)
		$filtro['sentido'] = Validador::validarParametro('sentido');

		if ( !isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $filtro['campo_orden'] )
		{
			// Si es la primera vez que carga la pagina
			// o se esta cambiando el campo por el que se ordena
			$_SESSION['ultimo_campo'] = $filtro['campo_orden'];
			$_SESSION['ultimo_sentido'] = 'asc';
		}
		else
		{
			// Si se hizo clic en el mismo que ya estaba ordenado antes, solo hay que cambiar el sentido:
			if ( $_SESSION['ultimo_sentido'] == 'asc' && $filtro['sentido'] == '' )
			{
				$_SESSION['ultimo_sentido'] = 'desc';
			}
			else
			{
				$_SESSION['ultimo_sentido'] = 'asc';
			}
		}

		$filtro['rango'] = $cantidad_registros_a_mostrar;
		$filtro['pagina'] = Validador::validarParametro('pagina');	//se obtiene el valor de la pagina

		if ( !$filtro['pagina'] )
		{	//al comienzo no se sabe el valor de la pagina
			$filtro['inicio'] = 0;	//por lo tanto se inicia en el primer registro
			$filtro['pagina'] = 1;	//con la primer pagina
		}
		else
		{	//sino se calcula el valor del registro inicial de la pagina deseada
			if ($filtro['valor_buscado'] == ''){	//si no se busca
				$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
			}
		}
		$filtro['pagina_ant'] = $filtro['pagina'] - 1;		//para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;		//para la pagina posterior

		//Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
		$filtro['cantidad'] = $modelo->obtenerCantidad();

		//NUMERO TOTAL DE PAGINAS
		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		// SI SE LLEGÓ MEDIANTE EL TECLADO AL RECORRER EL LISTADO
		$filtro['por_teclado'] = Validador::validarParametro('por_teclado');

		//Se establece el filtro en el modelo
		$modelo->setFiltro($filtro);

		return $filtro;
	}

	/**
	 * Convierte una fecha de formato dd/mm/aaaa a formato aaaa-mm-dd para MySQL
	 * @param string $fecha
	 * @return string|NULL fecha en formato aaaa-mm-dd para MySQL
	 */
	public function convertirFechaToMySQL($fecha, $para_fecha_hasta = false)
	{
		// Si la fecha no es nula ni vacía
		if ( $fecha !== null && $fecha != '' )
		{
			// Se divide la fecha por la barra
			$fec_partes = explode("/",$fecha);

			$mes   = $fec_partes[1];
			$dia   = $fec_partes[0];
			$anio  = $fec_partes[2];

			// Fecha en formato aaaa-mm-dd para MySQL
			$fecha_en_formato_mysql = $anio.'-'.$mes.'-'.$dia;

			// Si se trata de una fecha fin de un rango, se concatena el último horario válido de un día
			if ($para_fecha_hasta)
				return $fecha_en_formato_mysql.' 23:59:59';

			return $fecha_en_formato_mysql;
		}
		else
		{
			return null;
		}
	}

	/**
	 *  Devuelve una colección de parámetros, los cuales son enviados desde la Vista
	 * @return array $parametros_vista, Colección de parámetros para ser utilizados en la Vista
	 */
	public function armarConjuntoParametros($campo_orden_por_defecto)
	{
		$parametros_vista['mensaje'] = $this->parametrosObtenerONull('mensaje');
		$parametros_vista['tipo_mensaje'] = $this->parametrosObtenerONull('tipo_mensaje');

		// Dirección para la paginación (Primero, Anterior, Siguiente, Último)
		$parametros_vista['sentido'] = $this->parametrosObtenerONull('sentido');

		//se establece el campo por el cual ordenar
		$campo_orden = $this->parametrosObtenerONull('campo_orden');

		if ( !is_null($campo_orden) && !empty($campo_orden) )
		{
			$parametros_vista['campo_orden'] = $campo_orden;
		}
		else
		{
			//por defecto
			$parametros_vista['campo_orden'] = $campo_orden_por_defecto;
			$_SESSION['ultimo_campo'] = '';
		}

		if ( !isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $parametros_vista['campo_orden'] )
		{
			// Si es la primera vez que carga la pagina
			// o se esta cambiando el campo por el que se ordena
			$_SESSION['ultimo_campo'] = $parametros_vista['campo_orden'];
			$_SESSION['ultimo_sentido'] = ORDEN_ASCENDENTE;
		}
		else
		{
			// Si se hizo clic en el mismo que ya estaba ordenado antes se cambia el sentido
			if ($_SESSION['ultimo_sentido'] == ORDEN_ASCENDENTE)
			{
				$_SESSION['ultimo_sentido'] = ORDEN_DESCENDENTE;
			}
			else
			{
				$_SESSION['ultimo_sentido'] = ORDEN_ASCENDENTE;
			}
		}

		$parametros_vista['rango'] = $this->rango_paginador;	//cantidad de registros a mostrar
		$parametros_vista['pagina'] = $this->parametrosObtenerONull('pagina');	//se obtiene el valor de la pagina

		// Si no se sabe el valor de la pagina
		if ( !$parametros_vista['pagina'] )
		{
			$parametros_vista['inicio'] = 0;	//se inicia en el primer registro
			$parametros_vista['pagina'] = 1;	//con la primer pagina
		}
		else
		{
			// Si se conoce se calcula el valor del registro inicial de dicha pagina
			$parametros_vista['inicio'] = ($parametros_vista['pagina'] * $parametros_vista['rango']) - $parametros_vista['rango'];
		}

		$parametros_vista['pagina_ant'] = $parametros_vista['pagina'] - 1;	// Para la pagina anterior
		$parametros_vista['pagina_sgte'] = $parametros_vista['pagina'] + 1;	// Para la pagina posterior

		return $parametros_vista;
	}

	/**
	 * 2018/10/16 XXXX
	 * Se reemplaza la minúscula acentuada por la Mayúscula acentuada
	 * @param  [string] $cadena 	Cadena a convertir sus acentos.
	 * @return [string] $cadena    	Cadena convertida.
	 */
	public function reemplazarPorMayusculaAcentuada($cadena) {
		$cadena = str_replace('á','Á',$cadena);
		$cadena = str_replace('é','É',$cadena);
		$cadena = str_replace('í','Í',$cadena);
		$cadena = str_replace('ó','Ó',$cadena);
		$cadena = str_replace('ú','Ú',$cadena);

		return $cadena;
	}

    /**
     * 14/02/2019
     * Se verifica el estado de digitalización de un expediente determinado
     * @param  [array] 		$expediente 				Información de un Expediente determinado
     * @return [integer]    $estado_digitalizacion      Valor numérico (1=PARA CARGAR, 2=CARGADO, 3=SIN CARGAR)
     */
	public function verificarEstadoDigitalizacion($expediente) {

		$anio_corto = substr($expediente['anio'], -2);
		$tipo       = $expediente['tipo'];
		$aux_numero = 100000+$expediente['numero'];
		$numero     = substr($aux_numero, -5);

		$nombre_codificado = $anio_corto.$tipo.$numero;

		$directorio_remoto = $this->ruta_proyectos.$expediente['anio']."/".$nombre_codificado."/";

		$estado_digitalizacion = 3;// Estado 'SIN CARGAR' por defecto

		// Primero se verifica si se encuentra la digitalización en el directorio '/digital'
		if ( file_exists("../proyectos/digital/".$nombre_codificado.".pdf") ||
			 file_exists("../proyectos/digital/".$nombre_codificado.".PDF") )
		    $estado_digitalizacion = 1;// Estado 'PARA CARGAR'
		// Sino se verifica si existe el directorio remoto determinado por la clave del expediente respectivo
		else if (is_dir($directorio_remoto)) {
		    // Se 'escanea' dicho directorio, se obtiene un array de los archivos que contiene
		    $archivos = @scandir($directorio_remoto);

		    // Si posee por lo menos un archivo
		    if ( count($archivos) > 2 ) {
		    	//  Se verifica que posea la digitalización
		    	if (in_array($nombre_codificado.".pdf", $archivos))
		    		// Estado 'CARGADO'
		        	$estado_digitalizacion = 2;
		    }
		}

		return $estado_digitalizacion;
    }

    /**
     * Se obtiene la clave del expediente/nota a partir del nombre codificado
     * @param  [string] $nombre_codificado 	Nombre en formato AATNNNNN
     * @return [array]  $clave              Array con la clave
     */
    public function obtenerClaveDeNombre($nombre_codificado)
    {
    	// Se retiran las 'A' y 'a'
    	$nombre_codificado = str_replace('A', '', $nombre_codificado);
		$nombre_codificado = str_replace('a', '', $nombre_codificado);

		// Se toman los dos primeros caracteres correspondientes al Año
		$anio_corto = substr($nombre_codificado, 0, 2);
		// Se completa el año
		$anio = ($anio_corto < 83) ? '20'.$anio_corto : '19'.$anio_corto;

		// Se extrae el Tipo del nombre codificado
		$tipo = substr($nombre_codificado, 2, 1);

		// Se toman los últimos cinco caracteres correspondientes al Número
		// y se retiran los ceros de la izquierda (se convierte a entero la cadena)
		$numero = intval(substr($nombre_codificado, 3, 5));

		$clave = Array(
			'anio' => $anio,
		  	'tipo' => $tipo,
		  	'numero' => $numero,
		  	'cuerpo' => 0,
		  	'alcance' => 0
		);

		return $clave;
    }
}
?>

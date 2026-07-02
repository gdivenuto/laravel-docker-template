<?php
// 07/05/2015: CLASE ESTATICA PARA VALIDAR PARAMETROS RECIBIDOS EN TODO EL SISTEMA
require_once("validador.php");

/***************************************************************************
		LIBRERIA GENERAL DE FUNCIONES PHP PARA LA APLICACION
****************************************************************************/
// MODIFICADA EL 07/05/2015
function recoge($valor, $valor_defecto = '')
{
	return Validador::validarParametro($valor, $valor_defecto);
}

// Para recortar un texto
function cortar_string($string, $charlimit)
{
    if (substr($string,$charlimit-1,1) != ' ')
    {
		$string = substr($string,'0',$charlimit);
		$array = explode(' ',$string);
		array_pop($array);
		$new_string = implode(' ',$array);
		return $new_string.' ...';
    }
    else
    {
		return substr($string,'0',$charlimit-1).' ...';
    }
} 

// Para convertir los saltos de linea y las tabulaciones en su respectiva etiqueta u operador html
function convertir_salto_linea($textohtml) 
{
    $textohtml=str_replace("\n","<br>",$textohtml);
    $textohtml=str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;",$textohtml);

    return $textohtml;
} 

// SE REGRESA EL PASSWORD A SU ESTADO ORIGINAL
function desencriptar($cadena, $name)
{
    $suma = 0;
    $newtexto = "";

	for ($x=0; $x < strlen($_SESSION["token_".$name]); $x++)
	{
		$suma += ord($_SESSION["token_".$name][$x]);
	}

	$semilla = "$suma";
	$suma = 0;
  
	for ($z=0; $z < strlen($semilla); $z++)
	{
	   $suma += $semilla[$z];
	}

	$semilla = $suma;

	for ($y=0; $y < strlen($cadena); $y++)
	{
		if (ord($cadena[$y]) - $semilla > 31)
		{
			$suma = (ord($cadena[$y]) - $semilla);
		   
		}else{
			$suma = 126 - (31 - (ord($cadena[$y]) - $semilla));
		}
		
		$newtexto .= chr($suma);
	}

    return $newtexto;
}

function obtenerDatosArchivoRecibido($path) 
{
	// Vaciamos la caché de lectura de disco
	clearstatcache();
	
	// Comprobamos si el fichero existe
	$datos["exists"] = is_file($path);
	// Comprobamos si el fichero es escribible
	$datos["writable"] = is_writable($path);
	// Leemos los permisos del fichero
	$datos["chmod"] = ( $datos["exists"] ? substr(sprintf("%o", fileperms($path)), -4) : FALSE );
	// Extraemos la extensión, un sólo paso
	$datos["ext"] = substr(strrchr($path, "."),1);
	// Primer paso de lectura de ruta
	$datos["path"] = array_shift(explode(".".$datos["ext"],$path));
	// Primer paso de lectura de nombre
	$datos["name"] = array_pop(explode("/",$datos["path"]));
	// Ajustamos nombre a FALSE si está vacio
	$datos["name"] = ($datos["name"] ? $datos["name"] : FALSE);
	// Ajustamos la ruta a FALSE si está vacia
	$datos["path"] = ($datos["exists"] ? ($datos["name"] ? realpath(array_shift(explode($datos["name"],$datos["path"]))) : realpath(array_shift(explode($datos["ext"],$datos["path"])))) : ($datos["name"] ? array_shift(explode($datos["name"],$datos["path"])) : ($datos["ext"] ? array_shift(explode($datos["ext"],$datos["path"])) : rtrim($datos["path"],"/")))) ;
	// Ajustamos el nombre a FALSE si está vacio o a su valor en caso contrario
	$datos["filename"] = (($datos["name"] OR $datos["ext"]) ? $datos["name"].($datos["ext"] ? "." : "").$datos["ext"] : FALSE);
	
	// Devolvemos los resultados
	return $datos;
}

function armarArbol($dir, $tab=0)
{
	$directorios_excluidos = Array("imagenes", "docs", "documentos", "documentacion", "proyectos", "generar_pdf", "backups", "sgl_imagenes", "fonts", "expe-de");
	$directorio = dir($dir);
    if (!$tab) echo "<pre>";
    
    if ( !in_array(basename($directorio->path), $directorios_excluidos) )
    {
		echo "\n".str_pad("",($tab*3)," ",STR_PAD_LEFT)."<img src='imagenes/directorio.jpg' width='14' height='14' align='top' >&nbsp;<strong>".basename($directorio->path)."</strong>";
		
		while ( $df = $directorio->read() )
		{
			if ($df=="." || $df=="..")continue;
			
			if (is_file($directorio->path.$df))
			{
				echo "\n".str_pad("",($tab*3)," ")."  ".basename($df);
			}
			else
			{
				armarArbol($directorio->path.$df."/",$tab+1);
			}
		}
	}
    
    $directorio->close();
    
 	if (!$tab) echo "\n</pre>";
}

function eliminarAcentos($cadena)
{
	$a_buscar = "ÀÁÂÄÅàáâäÒÓÔÖòóôöÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ";
	$reemplazo = "AAAAAaaaaOOOOooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
	
	return utf8_encode(strtr(utf8_decode($cadena), utf8_decode($a_buscar), $reemplazo));
}

function reemplazarPorMayusculaAcentuada($cadena) 
{ 
	$cadena = str_replace('á','Á',$cadena);
	$cadena = str_replace('é','É',$cadena);
	$cadena = str_replace('í','Í',$cadena);
	$cadena = str_replace('ó','Ó',$cadena);
	$cadena = str_replace('ú','Ú',$cadena);
	
	return $cadena; 
}

/**
 * Serializa una colección de datos
 * @param Array $coleccion
 * @return string, devuelve los datos serializados en una cadena
 */
function serializarColeccion($coleccion)
{
	return base64_encode(json_encode($coleccion));
}

/**
 * Deserializa una cadena de datos
 * @param string $cadena_serializada
 * @return mixed
 */
function deserializarColeccion($cadena_serializada)
{
	return json_decode(base64_decode($cadena_serializada), true);
}

/**
 * Se guarda en un archivo txt el contenido de un elemento determinado
 * @param string $nombre_archivo
 * @param mixed $elemento_a_verificar
 */
function guardarEnTxt($nombre_archivo, $elemento_a_verificar)
{
	fputs(fopen($nombre_archivo.".txt", 'w'), print_r($elemento_a_verificar, true));
}

/**
 * Se eliminan los espacios vacíos en cualquier posición de la cadena
 * @param string $cadena
 * @return string cadena, sin espacios vacíos
 */
function eliminarEspacios($cadena)
{
	$cadena = str_replace(' ','',$cadena);
	return $cadena;
}

/**
 * Se elimina un directorio determinado,
 * previamente se elimina su contenido recursivamente
 * @param string $directorio
 */
function eliminarDirectorio($directorio)
{
	// Si se puede abrir el directorio respectivo
	if ( $dir_abierto = @opendir($directorio) )
	{
		// Mientras encuentre un archivo
		while ( false !== ( $archivo = readdir($dir_abierto) ) )
		{
			// Se descartan . y ..
			if ( $archivo != '..' && $archivo != '.' )
			{
				// Se elimina el archivo
				if ( !@unlink($directorio.'/'.$archivo) )
				{
					$this->eliminarDirectorio($directorio.'/'.$archivo);
				}
			}
		}
		// Se cierra el directorio
		closedir($dir_abierto);

		// Se elimina el directorio, el cual ya se encuentra vacío
		@rmdir($directorio);
	}
}

function restarDiasFechaActual($cantidad) {
	$fecha = date('Y-m-j');
	
	$fecha_anterior = strtotime ('-'.$cantidad.' day', strtotime($fecha));
	$fecha_anterior = date('Y-m-j', $fecha_anterior);

	return $fecha_anterior;
}
?>

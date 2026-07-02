<?php
/**
 * Clase base para la implementación del patrón strategy en las acciones de una vista.
 * Define un comportamiento por defecto para una vista base. 
 */
class BaseViewAction {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $vista; 	//!< Vista que invoca la acción (Contexto de ejecución)

	// ************************************************************************
	// Definición de Métodos que requieren implementación *********************
	// ************************************************************************

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct(BaseView $view)
	{
		$this->vista = $view;
	}

	/**
	 * Método que renderiza el código html de la sección <head>...</head> para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarHtmlHeader()
	{
		echo "\n<!-- ".get_class($this).".generarHtmlHeader() -->\n";
		require($this->vista->baseTemplatePath.'base_head.php');
	}

	/**
	 * Método que renderiza el código html dentro de la sección <head>...</head> correspondiente a
	 * la inclusión o definición de código JavaScript, para la vista actual dentro del ámbito de la acción.
	 * Se utiliza para especificar únicamente lo correpondiente a JavaScript, sin necesidad de reescribir 
	 * todo el código del método generarHtmlHeader().
	 */
	public function generarHtmlHeaderJS()
	{
		echo "\n<!-- ".get_class($this).".generarHtmlHeaderJS() -->\n";
		echo '<script type="text/javascript" src="'.$this->vista->baseUrl.'js/base.js"></script>'."\n";
	}

	/**
	 * Método que renderiza el código html dentro de la sección <head>...</head> correspondiente a
	 * la inclusión o definición de código CSS, para la vista actual dentro del ámbito de la acción.
	 * Se utiliza para especificar únicamente lo correpondiente a CSS, sin necesidad de reescribir 
	 * todo el código del método generarHtmlHeader().
	 */
	public function generarHtmlHeaderCSS()
	{
		// Debido a que se utiliza una versión custom de bootstrap (+sass) para los estilos,
		// ya no es necesario incluír *POR DEFECTO* nuestra hoja de estilo. Si se diera el caso
		// de que algun ViewAction necesita su estilo, solo tiene que heredar y pisar este comportamiento.
		// 
		// echo "\n<!-- ".get_class($this).".generarHtmlHeaderCSS() -->\n";
		echo '<link rel="stylesheet" type="text/css" href="'.$this->vista->baseUrl.'css/estilo.css" />'."\n";
	}

	/**
	 * Método que renderiza el código html del menú principal para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarMenu()
	{
		echo "\n<!-- ".get_class($this).".generarMenu() -->\n";
		require($this->vista->baseTemplatePath.'base_menu.php');
	}

	/**
	 * Método que renderiza el código html de la cabecera de página para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarCabecera()
	{
		echo "\n<!-- ".get_class($this).".generarCabecera() -->\n";
		require($this->vista->baseTemplatePath.'base_cabecera.php');
	}

	/**
	 * Método que renderiza el código html del cuerpo de la página para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarCuerpo()
	{
		echo "\n<!-- ".get_class($this).".generarCuerpo() -->\n";
		require($this->vista->baseTemplatePath.'base_cuerpo.php');
	}

	/**
	 * Método que renderiza el código html del pie de la página para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarPie()
	{
		echo "\n<!-- ".get_class($this).".generarPie() -->\n";
		require($this->vista->baseTemplatePath.'base_pie.php');
	}

	/**
	 * Método que renderiza el código html del cuadro de diálogo modal de la página para la vista actual
	 * dentro del ámbito de la acción.
	 */
	public function generarModalDialog()
	{
		echo "\n<!-- ".get_class($this).".generarModalDialog() -->\n";
		require($this->vista->baseTemplatePath.'base_modal_dialog.php');
	}

	/**
	 * Método que renderiza la plantilla general, la cual incluye las llamadas al resto de los
	 * métodos de renderizado de la accion actual.
	 * @param  string $plantilla Nombre del archivo de la plantilla base.
	 */
	public function entregar($plantilla = 'base.php')
	{
		require($this->vista->baseTemplatePath.$plantilla);
	}

	/**
	 * Alias del método entregar()
	 * @param  string $plantilla Nombre del archivo de la plantilla base.
	 */
	public function incluirPlantilla($plantilla = 'base.php')
	{
		$this->entregar($plantilla);
	}

	/**
	 * Renderiza la lista de opciones en base a una colección de instancias de una clase determinada. Preselecciona
	 * la primer opcion cuyo p_atributo_valor sea igual (y del mismo tipo de dato) al elemento de la colección consultada.
	 * @param  array $coleccion            Colección de instancias de una clase determinada
	 * @param  mixed $atributo_valor       Atributo de la clase a utilizar como valor en el combo. Si es un array, utiliza un "value" combinado.
	 * @param  mixed $atributo_descripcion Atributo de la clase a utilizar como descripción en el combo. Si es un array, utiliza un "description" combinado.
	 * @param  mixed $opcion_actual        Opción actual, puede ser nula. Si es un array, utiliza un "default" combinado.
	 */
	protected function renderOptionList(array $p_coleccion, $p_atributo_valor, $p_atributo_descripcion, $p_opcion_actual = null) {
		// Se inicializa la lista de opciones vacia
		$lista_opciones = '';
		$separador_value = '|';
		$separador_desc = ' - ';

		// Los parametros de atributo_valor, atributo_descripcion y opcion_actual
		// pueden ser array o variables "comunes". Para mayor simplicidad, los considero siempre
		// arrays, haciendo las conversiones necesarias.
		$atrValor = (is_array($p_atributo_valor)) ? $p_atributo_valor : array($p_atributo_valor);
		$atrDescrip = (is_array($p_atributo_descripcion)) ? $p_atributo_descripcion : array($p_atributo_descripcion);
		$opActual = (is_array($p_opcion_actual)) ? $p_opcion_actual : array($p_opcion_actual);

		// Armo el conjunto de valores del 'default value' (selected)
		$defaultData = implode($separador_value, $opActual);
		
		// Si la coleccion es nula, no renderizo nada
		if (!is_null($p_coleccion)) {
			$existe_selected = false; // para evitar que existan 'dos option selected'
			
			// Se recorre la colección de instancias
			foreach ($p_coleccion as $instancia) {
				$className = get_class($instancia);

				// verifico que las propiedades existan (tanto de 'value' como 'description')
				$existen_propiedades = true;
				foreach ($atrValor as $a) 
					$existen_propiedades = $existen_propiedades && property_exists($className, $a);
				foreach ($atrDescrip as $a) 
					$existen_propiedades = $existen_propiedades && property_exists($className, $a);

				if ($existen_propiedades) {
					// Armo el conjunto de valores del 'value'
					$valueDataArray = array();
					foreach ($atrValor as $a)
						$valueDataArray[] = $instancia->{$a};
					$valueData = implode($separador_value, $valueDataArray);

					// Armo el conjunto de valores del 'description'
					$descDataArray = array();
					foreach ($atrDescrip as $a)
						$descDataArray[] = $instancia->{$a};
					$descData = implode($separador_desc, $descDataArray);

					// Verifico el valor 'selected'
					$selectedFlag = (($valueData == $defaultData) && !$existe_selected) ? ' selected ' : ' ';

					// genero el tag option
					$lista_opciones .= sprintf("\t<option%svalue=\"%s\">%s</option>\n",	$selectedFlag, $valueData, $descData);
				}
				else {
					$lista_opciones = "\t<option selected>Ha ocurrido un error al generar las opciones.</option>\n";
					break;
				}

				$existe_selected = ($valueData == $defaultData) || $existe_selected;
			}
		}
		
		// Devuelve la lista de opciones para el combo
		echo $lista_opciones;
	}

	/**
	 * Convierte una instancia de ColeccionClaseBase a la definición de un array en JavaScript.
	 * @param  ColeccionClaseBase $coleccion      Instancia de ColeccionClaseBase a convertir.
	 * @param  string             $nombreJSArray         Nombre del array en JavaScript.
	 * @param  array             $arrayExclusion Array de atributos a excluir durante la conversión de los elementos de la colección.
	 * @return string             Definición de un array en JavaScript.
	 */
	public function coleccionAJavaScriptArray(ColeccionClaseBase $coleccion, $nombreJSArray, $arrayExclusion = null) {
	    // Verifico parámetros
	    if ($nombreJSArray === '')
	    	throw new InvalidArgumentException(sprintf("Error en %s.coleccionAJavaScriptArray(): se debe especificar un valor para 'nombreJSArray'.", get_class($this)));

		// Si no hay ignorados, debo crear el array vacio
		if (is_null($arrayExclusion))
			$arrayExclusion = array();

		// Transformo la colección
	    $jsResult = array();
	    foreach ($coleccion as $item) {
	        $jsFila = array();
	        $propiedades = $item->obtenerPropiedades($item);

	        foreach ($propiedades as $key => $value) 
	        	if (!in_array($key, $arrayExclusion))
	        		$jsFila[] = sprintf("'%s':'%s'", $key, $value);

	       	$jsResult[] = sprintf('{%s}', implode(',', $jsFila)); 
	    }

		return sprintf('var %s = [%s];', $nombreJSArray, implode(',', $jsResult));
	}
}
?>
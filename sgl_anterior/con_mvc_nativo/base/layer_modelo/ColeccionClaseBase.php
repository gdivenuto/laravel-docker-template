<?php
/**
 * Colección específica para elementos de tipo ClaseBase. 
 * Implementa la funcionalidad de control de estado de instancias, utilizado posteriormente
 * en la capa de negocio.
 */
class ColeccionClaseBase implements ArrayAccess, Countable, Iterator {

    // ************************************************************************
    // Definición de Atributos ************************************************
    // ************************************************************************

    private $_items;
    private $_deletedItems;
    private $_position;

    // ************************************************************************
    // Definición de Métodos **************************************************
    // ************************************************************************
    /**
     * Constructor de clase
     * @param array $pItems Array de inicialización. Todos sus elementos deben ser de tipo ClaseBase.
     * @param boolean $pEstabilizar Estabiliza la lista despues de inicializar la colección.
     */
    public function __construct($pItems = array(), $pEstabilizar = true) {
        // verificación de tipo de elemento
        foreach ($pItems as $i)
            if (! $i instanceof ClaseBase)
                throw new Exception(sprintf("Error en %s: No se permite agregar elementos que no sean de tipo ClaseBase o alguno de sus derivados.", get_class($this)));

        // inicialización
        $this->_items = $pItems;
        $this->_deletedItems = array();
    	$this->_position = 0;

        // estabilizo la lista
        if ($pEstabilizar)
            $this->estabilizar();
    }

    /**
     * 'Estabiliza' todos los elementos de la colección, es decir, hace que todas las instancias se vuelvan estables (IS_STABLE).
     */
    public function estabilizar() {
        foreach ($this->_items as $i) 
            $i->setInstanceState(IS_STABLE);
    }

    /**
     * Inicializa la colección a partir de un array.
     * @param  array   $pItems       Array con el cual se desea inicializar la colección.
     * @param  boolean $pLimpiarExistentes Elimina los elementos existentes antes de completar la colección.
     * @param  boolean $pEstabilizar Indica si se debe marcar los elementos de la colección como estables (IS_STABLE) después del llenado.
     */
    public function fillFromArray($pItems = array(), $pLimpiarExistentes = false, $pEstabilizar = true) {
        if ($pLimpiarExistentes)
            $this->_items = array();

        foreach ($pItems as $indice => $instancia) 
            if (! $instancia instanceof ClaseBase)
                throw new Exception(sprintf("Error en %s: No se permite agregar elementos que no sean de tipo ClaseBase o alguno de sus derivados.", get_class($this)));

        $this->_items = $pItems;

        // estabilizo la lista
        if ($pEstabilizar)
            $this->estabilizar(); 
    }
    
    /**
     * Se obtiene el primer elemento de un conjunto de instancias de tipo ClaseBase
     * @return [ClaseBase] Instancia determinada
     */
    public function obtenerPrimero() {
        return (count($this->_items) == 0) ? null : $this->_items[0];
    }

    /**
     * Se obtiene el último elemento de un conjunto de instancias de tipo ClaseBase
     * @return [ClaseBase] Instancia determinada
     */
    public function obtenerUltimo() {
        return (count($this->_items) == 0) ? null : $this->_items[count($this->_items)-1];
    }

    /** 
     * Devuelve el arreglo de elementos eliminados.
     * @return array Arreglo de elementos eliminados.
     */
    public function getDeletedItems() {
        return $this->_deletedItems;
    }

    // ************************************************************************
    // Definición de Métodos de interface ArrayAccess *************************
    // ************************************************************************ 
    /**
     * Agrega un elemento a la colección, o sobreescribe uno existente. Soporta índices numéricos o cadenas (diccionario).
     * Es el equivalente de hacer un $miArreglo[] = new Expediente(); o $miArreglo[8] = new Expediente();
     * @param  mixed $pIndex Indice de la colección, el cual puede ser numérico o cadena. Si no se especifica, agrega un elemento.
     * @param  ClaseBase $pInstanciaClaseBase Instancia a agregar.
     */
    public function offsetSet($pIndex, $pInstanciaClaseBase) {
        // Solo permito instancias de tipo ClaseBase.
        if (! $pInstanciaClaseBase instanceof ClaseBase)
            throw new Exception(sprintf("Error en %s: No se permite agregar elementos que no sean de tipo ClaseBase o alguno de sus derivados.", get_class($this)));
        
        // Agrego el elemento a la colección
        if (is_null($pIndex)) 
            $this->_items[] = $pInstanciaClaseBase;
        else
            $this->_items[$pIndex] = $pInstanciaClaseBase;

        // Marco la instancia como agregada.
        $pInstanciaClaseBase->setInstanceState(IS_ADDED);
    }

    /**
     * Determina si un elemento determinado existe en base a su índice.
     * @param  mixed $pIndex Indice de la colección, el cual puede ser numérico o cadena.
     * @return bool         TRUE si existe un elemento en la posición indicada, FALSE en caso contrario.
     */
    public function offsetExists($pIndex) {
        return isset($this->_items[$pIndex]);
    }

    /** 
     * Elimina un elemento de la colección, moviéndolo posteriormente a la lista de elementos borrados.
     * @param  mixed $pIndex Indice de la colección, el cual puede ser numérico o cadena.
     */
    public function offsetUnset($pIndex) {
        // Antes de eliminar el elemento, lo muevo a la coleccion de "eliminados", y lo marco como tal.
        $instancia = $this->_items[$pIndex];
        $instancia->setInstanceState(IS_DELETED);
        $this->_deletedItems[] = $instancia;

        // Quito el elemento de la colección
        unset($this->_items[$pIndex]);
    }

    /**
     * Obtengo el elemento en la posición dada.
     * @param  mixed $pIndex Indice de la colección, el cual puede ser numérico o cadena.
     * @return ClaseBase        Instancia a obtener, o nulo en caso de que no exista.
     */
    public function offsetGet($pIndex) {
        return isset($this->_items[$pIndex]) ? $this->_items[$pIndex] : null;
    }

    // ************************************************************************
    // Definición de Métodos de interface Countable ***************************
    // ************************************************************************ 
    public function count() {
    	return count($this->_items);
    }

    // ************************************************************************
    // Definición de Métodos de interface Iterator ****************************
    // ************************************************************************ 
	public function rewind() {
		return reset($this->_items);
	}
	
	public function current() {
		return current($this->_items);
	}

	public function key() {
		return key($this->_items);
	}
	
	public function next() {
		return next($this->_items);
	}
	
	public function valid() {
		return key($this->_items) !== null;
	}

}
?>
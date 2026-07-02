/**
 * Genera el código HTML de una grilla (tabla) dinamicamente.
 * @param  {string} identificador Id de la tabla.
 * @param  {Array} columnas      Coleccion de nombres de columnas.
 * @return {string}               Codigo HTML con la tabla generada.
 */
function generarGrillaHtml(identificador, columnas, ocultarEncabezado = false)
{
	htmlId = identificador.replace('#', '');
    htmldata = '<table class="table" id="'+htmlId+'">';
    
    if (!ocultarEncabezado) {
        htmldata += '<thead class="color-fondo-menu">';
        for (i = 0; i < columnas.length; i++)
            htmldata += '<th class="text-left">' + columnas[i] + '</th>';
        htmldata += '</thead>';
    }

	htmldata += '<tbody></tbody></table>';

	return htmldata;
}
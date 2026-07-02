<?php
if (!isset($_SESSION)) {
    session_start();
}

//Incluye el modelo que corresponde
require_once RUTA_MODELOS . "ficha_web.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "ficha_web/grilla.php";
require_once RUTA_VISTAS . "ficha_web/edicion.php";

// Se incluye el modelo del sistema de Personal
require_once RUTA_SGL.'personal/abms/modelos/personal.php';

class ficha_web_controller extends ControllerBase
{
    public function __construct()
    {
        parent::__construct();

        $this->campo_orden_por_defecto = 'p_apellido';

        // Se crea una instancia de cada modelo
        $this->modelo_personal = new personalModel();
        $this->modelo = new fichaWebModel();
        
        // Se crea una instancia de la Vista
        $this->vista_grilla = new VistaFichaWebGrilla();
        $this->vista_edicion = new VistaFichaWebEdicion();

        // Se inicializa el mensaje de resultados
        $this->mensaje = "";
    }

    public function guardarOriginal($original)
    {
        $_SESSION['fw_legajo_original']               = $original['fw_legajo'];
        $_SESSION['fw_funcion_original']              = $original['fw_funcion'];
        $_SESSION['fw_es_presidente_bloque_original'] = $original['fw_es_presidente_bloque'];
        $_SESSION['fw_anio_inicio_original']          = $original['fw_anio_inicio'];
        $_SESSION['fw_anio_fin_original']             = $original['fw_anio_fin'];
        $_SESSION['fw_foto_original']                 = $original['fw_foto'];
        $_SESSION['fw_profesion_original']            = $original['fw_profesion'];
        $_SESSION['fw_mail_original']                 = $original['fw_mail'];
        $_SESSION['fw_telefono_original']             = $original['fw_telefono'];
        $_SESSION['fw_facebook_original']             = $original['fw_facebook'];
        $_SESSION['fw_instagram_original']            = $original['fw_instagram'];
        $_SESSION['fw_twitter_original']              = $original['fw_twitter'];
        $_SESSION['fw_sitio_web_original']            = $original['fw_sitio_web'];
        $_SESSION['fw_autor_codigo_original']         = $original['fw_autor_codigo'];
    }

    /**
     * Se muestra el listado de Concejales para editar su ficha web
     */
    public function listar($mensaje = '', $tipo_mensaje = '', $bg_pagina = '')
    {
        $filtro = Array();
        
        // Filtro por Legajo
        $filtro['f_legajo'] = LibreriaGeneral::recoge('f_legajo', 0);

        // Filtro por Apellido ó Nombre
        $filtro['f_apellido_y_nombre'] = LibreriaGeneral::recoge('f_apellido_y_nombre');
        
        // Para listar sólo los Activos o todos
        $filtro['f_activos'] = LibreriaGeneral::recoge('f_activos', 1);
        
        $filtro['pagina'] = ($bg_pagina != '') ? $bg_pagina : LibreriaGeneral::recoge('pagina');

        // se establece el campo por el cual ordenar
        $campo_orden = LibreriaGeneral::recoge('campo_orden');
        if ($campo_orden != '') {
            $filtro['campo_orden'] = $campo_orden;
        } else {
            //por defecto
            $filtro['campo_orden'] = $this->campo_orden_por_defecto;
            $_SESSION['ultimo_campo'] = '';
        }
        
        // DIRECCION PARA LA PAGINACION (PRIMERO, ANTERIOR, SIGUIENTE, ULTIMO)
        $filtro['sentido'] = LibreriaGeneral::recoge('sentido');
        
        // ORDEN ASCENDENTE O DESCENDENTE (DESDE EL PAGINADOR)
        $filtro['sentido_orden'] = LibreriaGeneral::recoge('sentido_orden');
        
        if (!isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $filtro['campo_orden']) {
            // Si es la primera vez que carga la pagina o se esta cambiando el campo por el que se ordena
            $_SESSION['ultimo_campo'] = $filtro['campo_orden'];
            $_SESSION['ultimo_sentido'] = 'asc';
        } else {
            // Si se hizo clic en el mismo que ya estaba ordenado antes, solo hay que cambiar el sentido
            $_SESSION['ultimo_sentido'] = ($_SESSION['ultimo_sentido'] == 'asc' && $filtro['sentido'] == '') ? 'desc' : 'asc';
        }
        
        // Cantidad de registros a mostrar
        $filtro['rango'] = $this->rango_paginacion;

        // SE ESTABLECE EL FILTRO EN EL MODELO
        $this->modelo->setFiltro($filtro);

        // Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
        $filtro['cantidad'] = $this->modelo->obtenerCantidad();

        //NUMERO TOTAL DE PAGINAS
        $filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

        // SI NO SE RECIBIÓ LA PÁGINA
        // if (!$filtro['pagina']) {
        //     // Se establece la primera
        //     $filtro['pagina'] = 1;//($filtro['nro_paginas'] > 0) ? $filtro['nro_paginas'] : 1;

        //     // SI LA CANTIDAD ES MENOR AL RANGO DE PAGINA
        //     if ($filtro['cantidad'] < $filtro['rango']) {
        //         $filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
        //     } else {
        //         // SE MUESTRAN LOS ÚLTIMOS (VALOR DEL RANGO) REGISTROS
        //         $filtro['inicio'] = $filtro['cantidad'] - $filtro['rango'];
        //     }
        // } else {
        //     $filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
        // }

        // Si se desconoce el valor de la pagina
        if ($filtro['pagina'] == '') {
            $filtro['inicio'] = 0; // se inicia en el primer registro
            $filtro['pagina'] = 1; // en la primer pagina
            // si no se busca
        } elseif ($filtro['f_nombre'] == '') {
            // se calcula el valor del registro inicial de la pagina deseada
            $filtro['inicio'] = ($filtro['pagina'] - 1) * $filtro['rango'];
        }

        $filtro['pagina_ant'] = $filtro['pagina'] - 1; // para la pagina anterior
        $filtro['pagina_sgte'] = $filtro['pagina'] + 1; // para la pagina posterior

        // SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
        $_SESSION['f_ficha_web'] = $filtro;
            
        // Se establece el filtro en el modelo
        $this->modelo->setFiltro($filtro);
                
        // SE OBTIENE EL LISTADO
        $listado = $this->modelo->listar();
        
        //se muestra el listado
        $this->vista_grilla->mostrar($listado, $mensaje, $tipo_mensaje, $filtro);
    }
    
    /**
     * Se muestra un formulario para editar la ficha web de un legajo determinado
     */
    public function editar($legajo = '', $pmensaje = '', $ptipo_mensaje = '')
    {
        $legajo = ( $legajo != '' ) ? $legajo : LibreriaGeneral::recoge('legajo', 0);
        
        // Se obtienen los datos del Legajo respectivo
        $datos = $this->modelo_personal->obtenerRegistro($legajo);

        // Se obtiene el último área del legajo respectivo
        $area = $this->modelo_personal->obtenerNombreUltimaArea($legajo);
                 
        // Se obtiene la información de la Ficha Web del legajo respectivo
        $info = $this->modelo->obtenerRegistro($legajo);

        // Se guarda el registro en sesión para verificar luego si lo ha modificado otro usuario
        $this->guardarOriginal($info);
    
        $info['fw_legajo'] = ($info['fw_legajo']) ? $info['fw_legajo'] : $legajo;
        $info['apellido']  = $datos['p_apellido'];
        $info['nombre']    = $datos['p_nombre'];
        $info['bloque']    = ($area != '') ? $area['area'] : '';
        $info['pagina']    = LibreriaGeneral::recoge('pagina');

        // Se obtienen los Autores activos
        $info['autores'] = $this->modelo->obtenerAutores();

        $this->vista_edicion->mostrar($info, $pmensaje, $ptipo_mensaje);
    }
    
    /**
     * Se sube en temporal/ la foto elegida
     */
    public function subirEnTemporal() {

        // Se recibe la info
        $datos = $_REQUEST;
        
        // Se reciben el archivo de la foto
        $info_foto = $_FILES['foto'];

        // Si se recibieron los datos del registro con el archivo a cargar
        if (isset($datos) && isset($info_foto['name']) && $info_foto['name'] != '') {

            $nombre_archivo = $info_foto['name'];

            // Se toma la extensión del archivo y se convierte a minúscula
            $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));

            // Si su extensión no es válida
            if (!in_array($extension, $this->extensiones_fotos_permitidas)) {
                $this->mensaje = "La extensi&oacute;n de " . $nombre_archivo . " no es v&aacute;lida.";
                $this->tipo_mensaje = 2;
            } else {
                // Archivo
                $archivo_a_guardar = $info_foto['tmp_name'];

                // Se eliminan los espacios vacíos que contenga el nombre del archivo
                // se convierte a minúsculas
                // se coloca el prefijo definido en la Vista
                $nombre_archivo_a_guardar = $datos['prefijo'] . '__' . mb_strtolower(LibreriaGeneral::reemplazarEspaciosPorGuionesBajos(LibreriaGeneral::quitarAcentos($nombre_archivo)));

                // Si no se recibió el archivo
                if ($info_foto['error'] == 4) {
                    $this->mensaje = "No se ha subido el archivo " . $nombre_archivo;
                    $this->tipo_mensaje = 2;
                }

                // Si el archivo fue recibido sin errores
                if ($info_foto['error'] == 0) {

                    // Se arma la ruta destino: directorio + nombre de archivo
                    $ruta_destino_completa = RUTA_DIRECTORIO_TEMPORAL . $nombre_archivo_a_guardar;

                    // Se mueve el archivo al directorio destino
                    if (move_uploaded_file($archivo_a_guardar, $ruta_destino_completa)) {
                        // Número de archivos subidos con éxito
                        $nro_archivos_subidos++;
                    }
                }
            }

            // Si no surgió un error
            if ($this->tipo_mensaje != 2) {
                $this->mensaje = "Se ha realizado la carga <strong>temporal</strong> del archivo satisfactoriamente!";
                $this->tipo_mensaje = 1;
            }
        } else {
            $this->mensaje = "No se ha recibido un archivo.";
            $this->tipo_mensaje = 2;
        }

        // Se obtienen los Autores activos
        $datos['autores'] = $this->modelo->obtenerAutores();
        
        $this->vista_edicion->mostrar($datos, $this->mensaje, $this->tipo_mensaje);
    }

    /**
     * Se cancela la edicion
     * en caso que se hayan cargado recursos temporales, se eliminan
     * y se vuelve a la grilla.
     */
    public function cancelarEdicion() {
        // Se recibe el prefijo
        $prefijo = LibreriaGeneral::recoge('prefijo', 0);

        // En caso que posea recursos temporales, se eliminan
        if (!$this->eliminarTemporales($prefijo)) {
            $this->listar("No se ha eliminado la foto temporal", 2);
        }

        $this->listar();
    }

    /**
     * Se guarda
     */
    public function guardar() {

        $datos = $_REQUEST; // Se recibe la info
        
        // Se guarda en la DB
        if ($this->modelo->guardar($datos)) {
            $this->mensaje = "La ficha se guard&oacute; con &eacute;xito.";
            $this->tipo_mensaje = 1;
        } else {
            $this->mensaje = "Error al guardar la ficha.";
            $this->tipo_mensaje = 2;
        }
        
        $this->listar($this->mensaje, $this->tipo_mensaje, $datos['pagina']);
    }

    /**
     * Se pasan los adjuntos al directorio correspondiente
     * @param  [integer] $prefijo   Para identificar el temporal del registro
     * @param  [integer] $id        Identificador del registro
     * @return [boolean]
     */
    public function moverFotoTemporal($prefijo, $id) {

        // Si existe el directorio
        if (is_dir(RUTA_DIRECTORIO_TEMPORAL)) {
            // Si pudo abrirse el directorio de los Adjuntos
            if ($handle = opendir(RUTA_DIRECTORIO_TEMPORAL)) {
                // Mientras encuentre un archivo
                while (false !== ($file = readdir($handle))) {
                    // Si es un archivo válido
                    if ($file != "." && $file != ".." && $file != "index.html") {
                        // Si pertenece al registro respectivo
                        if (LibreriaGeneral::esAdjuntoDe($prefijo, $file)) {
                            // Se toma la extensión del archivo y se convierte a minúscula
                            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                            // Directorio final de la foto, su nombre es el Id del registro
                            $ruta_final_foto = RUTA_FOTOS_FICHAS_AUTORIDADES . $id . "." . $extension;

                            // Se intenta copiar el archivo al directorio destino
                            if (!copy(RUTA_DIRECTORIO_TEMPORAL . $file, $ruta_final_foto)) {
                                return false;
                            } else {
                                // Se ingresa el nombre de la foto en la DB
                                $this->modelo->ingresarNombreFoto($id, $id . "." . $extension);
                            }
                        }
                    }
                }
                closedir($handle);

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Se eliminan la foto temporal de un registro determinado
     * @param  [integer] $prefijo   Para identificar el registro
     * @return [boolean]
     */
    public function eliminarTemporales($prefijo) {

        // Si existe el directorio
        if (is_dir(RUTA_DIRECTORIO_TEMPORAL)) {
            // Si pudo abrirse el directorio temporal
            if ($handle = opendir(RUTA_DIRECTORIO_TEMPORAL)) {

                while (false !== ($file = readdir($handle))) {
                    // Si es un archivo válido
                    if ($file != "." && $file != ".." && $file != "index.html") {
                        // Se intenta eliminar del directorio temporal
                        if (!unlink(RUTA_DIRECTORIO_TEMPORAL . $file)) {
                            return false;
                        }
                    }
                }
                closedir($handle);

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Se elimina una foto temporal
     */
    public function eliminarTemporal() {

        // se recibe el nombre del archivo temporal
        $nombre_temporal = LibreriaGeneral::recoge('nombre_temporal');

        // Si existe el temporal en el directorio temporal/
        if (is_file(RUTA_DIRECTORIO_TEMPORAL . $nombre_temporal)) {
            // Si se elimina el archivo temporal
            if (unlink(RUTA_DIRECTORIO_TEMPORAL . $nombre_temporal)) {
                $this->mensaje = "Se elimin&oacute; la foto <strong>temporal</strong> con &eacute;xito.";
                $this->tipo_mensaje = 1;
            } else {
                $this->mensaje = "No se ha eliminado la foto <strong>temporal</strong>.";
                $this->tipo_mensaje = 2;
            }
        }

        $datos['prefijo'] = ''; // Se limpia

        // Se recibe el resto de datos del registro
        $datos['fw_legajo'] = LibreriaGeneral::recoge('fw_legajo', 0);
        $datos['fw_funcion'] = LibreriaGeneral::recoge('fw_funcion');
        $datos['fw_es_presidente_bloque'] = LibreriaGeneral::recoge('fw_es_presidente_bloque');
        $datos['fw_anio_inicio'] = LibreriaGeneral::recoge('fw_anio_inicio');
        $datos['fw_anio_fin'] = LibreriaGeneral::recoge('fw_anio_fin');
        $datos['fw_foto'] = LibreriaGeneral::recoge('fw_foto');
        $datos['fw_profesion'] = LibreriaGeneral::recoge('fw_profesion');
        $datos['fw_mail'] = LibreriaGeneral::recoge('fw_mail');
        $datos['fw_telefono'] = LibreriaGeneral::recoge('fw_telefono');
        $datos['fw_facebook'] = LibreriaGeneral::recoge('fw_facebook');
        $datos['fw_instagram'] = LibreriaGeneral::recoge('fw_instagram');
        $datos['fw_twitter'] = LibreriaGeneral::recoge('fw_twitter');
        $datos['fw_sitio_web'] = LibreriaGeneral::recoge('fw_sitio_web');
        $datos['fw_autor_codigo'] = LibreriaGeneral::recoge('fw_autor_codigo');

        $datos['pagina'] = LibreriaGeneral::recoge('pagina');
        
        // Se obtienen los Autores activos
        $datos['autores'] = $this->modelo->obtenerAutores();
        
        // Se redirecciona al formulario de edición
        $this->vista_edicion->mostrar($datos, $this->mensaje, $this->tipo_mensaje);
    }

    /**
     * Se elimina la foto del directorio respectivo
     */
    public function eliminarFoto() {

        // Se recibe el id
        $id = LibreriaGeneral::recoge('id', 0);

        // se obtiene el nombre de la foto
        $nombre_foto = $this->modelo->obtenerNombreFoto($id);

        // Si existe la foto en el directorio respectivo
        if (is_file(RUTA_FOTOS_FICHAS_AUTORIDADES . $nombre_foto)) {

            // Si se elimina la foto (en la DB y físicamente)
            if ($this->modelo->eliminarFoto($id) && unlink(RUTA_FOTOS_FICHAS_AUTORIDADES . $nombre_foto)) {

                $mensaje = "Se elimin&oacute; la foto " . $nombre_foto . " con &eacute;xito.";
                $tipo_mensaje = 1;
            } else {
                $mensaje = "No se ha eliminado la foto " . $nombre_foto . ".";
                $tipo_mensaje = 2;
            }
        }

        // Se obtiene la info
        $datos = $this->modelo->obtenerRegistro($id);
        
        // Se obtienen los Autores activos
        $datos['autores'] = $this->modelo->obtenerAutores();
        
        // Se vuelve para seguir editando
        $this->vista_edicion->mostrar($datos, $mensaje, $tipo_mensaje);
    }

}
?>
<?php
/***********************************************************************************
    Archivo de configuracion. 
/***********************************************************************************/
// Un método estático normalmente se lo llama sin crear un objeto de dicha clase 
// Nombre de la Clase :: Método Estático
$config = Config::singleton();

// Directorio de los Controladores
$config->set('controllersFolder', 'controladores/');
// Directorio de los Modelos
$config->set('modelsFolder', 'modelos/');
// Directorio de las Vistas
$config->set('viewsFolder', 'vistas/');
?>

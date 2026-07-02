<?php 
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/config/config.php');

// Ruteo la petición al subsitio de backend
ControllerRouter::route($_REQUEST, $_FILES, PATH_KRAKEN_HTML_BACKEND_CONTROLLER_PREFIX);
?>
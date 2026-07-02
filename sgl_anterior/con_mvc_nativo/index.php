<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/config/config.php');

// Redirección al backend
header('Location: '.URL_KRAKEN_HTML_BACKEND.'index.php');
exit(); // termino la ejecución del script
?>
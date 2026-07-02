<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

session_unset();//se eliminan las variables de sesion
session_destroy();//se elimina la sesion
header("location:index.php");//se vuelve al login
?>

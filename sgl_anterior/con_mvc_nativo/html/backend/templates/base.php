<?php
/**
 * Este script esta diseñado para ser incluído como plantilla desde BaseViewAction o alguno de sus 
 * descendientes.
 * A continuación se detallan los métodos de generación de la plantilla (implementados en 
 * BaseViewAction o alguno de sus descendientes.
 *
 * 	$this->generarHtmlHeader();
 * 	$this->generarHtmlHeaderCSS();
 * 	$this->generarHtmlHeaderJS();
 * 	$this->generarMenu();
 * 	$this->generarCabecera();
 * 	$this->generarCuerpo();
 * 	$this->generarPie();
 */
?><!DOCTYPE html>
<html lang="es">
<head>
<?php 
	// Genero la cabecera base
	$this->generarHtmlHeader();

	// Incluyo las hojas de estilo
	$this->generarHtmlHeaderCSS();

	// Incluyo los scripts JS
	$this->generarHtmlHeaderJS();
?>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>

<body>

<?php 
	// Incluyo el menú de navegación.
	$this->generarMenu();
?>
	
	<div class="container-fluid">

<?php
	// Incluyo la cabecera
	$this->generarCabecera();

	// Incluyo el cuerpo de la vista
	$this->generarCuerpo();
?>
	
	</div><!-- /.container -->

<?php
	// Incluyo el pie de pagina
	$this->generarPie();
?>

</body>
</html>
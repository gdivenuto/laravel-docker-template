<?php
/**
 * Este script esta diseñado para ser incluído como plantilla desde BaseViewActionReport o alguno de sus
 * descendientes.
 * A continuación se detallan los métodos de generación de la plantilla (implementados en
 * BaseViewActionReport o alguno de sus descendientes.
 *
 * 	$this->generarHtmlHeader();
 * 	$this->generarHtmlHeaderCSS();
 * 	$this->generarHtmlHeaderJS();
 * 	$this->generarMenu();			--> No utilizado
 * 	$this->generarCabecera();
 * 	$this->generarCriterioBusqueda();
 * 	$this->generarCuerpo();
 * 	$this->generarPie();            --> No utilizado
 */
?>
<!DOCTYPE html>
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
		<style type="text/css">
			#btImprimir{
				text-align: center;
				padding: 7px;
			}
			#btImprimir input {
				background-color: #2da4c6;
				color: #fff;
				font-family: Arial;
				border: none;
				border-radius: 3px;
				padding: 5px 15px;
				cursor: pointer;
			}
		</style>
		<style media="print" type="text/css">
			#btImprimir {
			   display: none;
			}
		</style>
	</head>
	<body>
		<div id="btImprimir">
			<input name="imprimir" type="button" class="button" value="Imprimir Informe" onClick="window.print();">
		</div>	
<?php
// Incluyo la cabecera
$this->generarCabecera();

// Incluyo el criterio de búsqueda utilizado
$this->generarCriterioBusqueda();

// Incluyo el cuerpo de la vista
$this->generarCuerpo();
?>
	</body>
</html>
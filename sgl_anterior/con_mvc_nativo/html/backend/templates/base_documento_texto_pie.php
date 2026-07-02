<?php
// Se toma la información del Usuario actual
$info_usuario = $this->vista->data['usuario'];
echo "\n<hr>";
 // PIE DEL DOCUMENTO A IMPRIMIR CON FECHA, USUARIO Y NOMBRE DE LA PC 
echo "\n<p style='font-size:14px' >";
echo "Fecha: ".date("d/m/Y");
echo " | ".date("H:i")." Hs.";
echo " | Usuario: ".$info_usuario->codigo_usuario;
echo " | PC: ".gethostbyaddr($_SERVER['REMOTE_ADDR']);
echo "</p>";
echo "\n</body>";
echo "\n</html>";
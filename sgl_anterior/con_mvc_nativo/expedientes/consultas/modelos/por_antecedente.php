<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class porAntecedenteModel extends ModeloBaseMySQLi
{
	public function conectar()
	{
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(2);
	}
		
	public function listar()
	{
	    $conexion = $this->conectar();
	    
	    $filtro_por_anio = "";
	    if ( $this->filtro['bpa_anio'] != '' )
	    {
			$filtro_por_anio = "AND anio_a = ".$this->filtro['bpa_anio'];
		}	
	    
	    $sql = " SELECT E.anio, E.tipo, E.numero, E.cuerpo, E.alcance, E.caratula, E.fecha_entrada_expe, E.id_codcategoria, E.iniciador_tipo, E.iniciador_codigo, E.marca_comision,
						(SELECT CCat.descripcion_categoria FROM ".$this->tabla_codcategoria." CCat WHERE CCat.codigo_categoria = E.id_codcategoria)AS categoria, 
						(SELECT Ini.descripcion_grp FROM ".$this->tabla_lugares." Ini WHERE Ini.tipo_grp = iniciador_tipo AND Ini.codigo_grp = iniciador_codigo)AS iniciador
				 FROM ".$this->tabla_expedientes." AS E
				 WHERE (E.anio, E.tipo, E.numero, E.cuerpo, E.alcance) IN ( SELECT anio, tipo, numero, cuerpo, alcance
																			FROM ".$this->tabla_antecedentes."
																			WHERE numero_a = ".$this->filtro['bpa_numero']."
																			".$filtro_por_anio."
																		  )
				 ORDER BY E.anio ASC, E.tipo ASC, E.numero ASC, E.cuerpo ASC, E.alcance ASC
			   ";
	    //fputs(fopen('sqlPorAntecedente.txt','w'),print_r($sql,true));
	    
	    $resultado = $this->ejecutarQuery($sql, $conexion);
	    
	    //se crea un vector asociativo para la vista
	    $datos = $this->crearVector($resultado);

	    $this->desconectar($conexion);
	    
	    return $datos;
	}
}
?>

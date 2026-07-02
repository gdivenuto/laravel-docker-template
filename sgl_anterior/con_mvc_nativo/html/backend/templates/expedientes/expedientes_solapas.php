<?php
/**
 * Este script esta diseñado para ser incluído desde las páginas que requieran las Solapas respectivas.
 *
 * Se le agrega class="active" a la solapa, determinada por su Id,
 * utilizando el método setearComportamientoSolapas() en expedientes-busquedasimple-common.js
 */
?>
<!-- Solapas -->
<ul class="nav nav-tabs nav-justified">
    <li id="solapa_expedientes"><a class="solapa" data-controlador="expedientes" href="">Expedientes</a></li>
    <li id="solapa_proyectos"><a class="solapa" data-controlador="proyectos" href="">Proyectos</a></li>
    <li id="solapa_expedientes_elec"><a class="solapa" data-controlador="expedienteselec" href="">Exp. Electr&oacute;nico</a></li>
    <li id="solapa_participaciones"><a class="solapa" data-controlador="participaciones" href="">Participaciones</a></li>
    <li id="solapa_giros"><a class="solapa" data-controlador="giros" href="">Giros</a></li>
    <li id="solapa_sanciones"><a class="solapa" data-controlador="sanciones" href="">Sanciones</a></li>
    <li id="solapa_estados"><a class="solapa" data-controlador="estados" href="">Estados</a></li>
    <li id="solapa_antecedentes"><a class="solapa" data-controlador="antecedentes" href="">Antecedentes</a></li>
    <li id="solapa_prestamos"><a class="solapa" data-controlador="prestamos" href="">Pr&eacute;stamos</a></li>
</ul>
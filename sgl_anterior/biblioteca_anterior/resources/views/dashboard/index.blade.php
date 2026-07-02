@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="jumbotron">
                <!--
                    <h1 class="display-4">Biblioteca</h1>
                    <hr class="my-4">
                -->
                <p>El DIGESTO es la compilación de la normativa municipal con el fin de poner orden para dejar en claro la que está en vigor, utilizando una base de datos para identificarlas, describirlas y rescatarlas.</p>
                <p>La Base de Datos de Normas Municipales Digesto, responde al estudio de la normativa municipal que realizara el Dr. Rodolfo A. Rozas, responsable de la Oficina de Digesto desde el año de 1988 hasta abril de 1994, y está integrada por las ordenanzas de carácter general y permanente hasta esa fecha, y a partir de ese momento se han cargado en la Base de Datos todas las normas que se han ido sancionando e incluye las vigentes de carácter particular y transitorio, anteriores a 1994.</p>
                <p>El análisis que realiza el Departamento Referencia Legislativa y Digesto es desde un punto de vista formal, teniendo en cuenta las relaciones expresas entre normas, no así las tácitas. Siempre tenga en cuenta el usuario el principio jurídico Lex posterior derogat prior (Ley posterior deroga a la anterior)</p>
                <p>En el caso de los textos completos, se han incluido los de carácter general y permanente, y a partir de la Ordenanza 10.000, todas las sancionadas por este H. Cuerpo.</p>
                <p>La Base de Datos está en permanente actualización.</p>
                <p>Se recomienda para facilitar la búsqueda consultar la ayuda.</p>
                <p>Las normas contenidas en la base de datos de normas municipales, son aquellas que se publican en el Boletín Municipal, Boletín Oficial de la Provincia y Boletín Oficial de la Nación. Debido a ello se deja aclarado que la información contenida en esta página es de carácter informativo, debiéndose tomar por auténticos los textos publicados en los Boletines Oficiales que correspondan conforme al decreto nº659/1947 del PEN, decreto nº383/1954 de la Provincia de Buenos Aires, y Ordenanza Municipal 1230 del año 1959, y a partir del año 2009 la Ordenanza 19176.</p>
                <p>Se solicita que en caso de utilización de la información contenida en el presente sitio en páginas o sitios webs de carácter comercial, sea citada la fuente de origen de estos datos.</p>
                <a class="btn btn-primary btn-lg" href="{{ route('dashboard.showdbselector') }}" role="button">Acceso a la Base de Datos</a>
            </div>
        </div>
    </div>
</div>
@endsection
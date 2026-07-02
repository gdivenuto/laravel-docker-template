<?php

/**
 * Archivo de configuración customizado para tomar determinados defaults
 * en la aplicación.
 *
 * XXXX @ 2019-05-07
 * XXXX @ 2021-03-02 > Agregado de 'allowedImportSourceIP'
 */
return [
    /*
    |--------------------------------------------------------------------------
    | allowedImportSourceIP
    |--------------------------------------------------------------------------
    |
    | Este whitelist define el conjunto de IPs desde las cuales se podrán
    | utilizar las API de importación de datos.
    |
    */
    'allowedImportSourceIP' => [
        '127.0.0.1',    // localhost
        '172.16.0.2',   // hcd02.concejomdp.gov.ar
        '172.16.0.200'  // test.concejomdp.gov.ar ; inventario.concejomdp.gov.ar
    ],


    /*
    |--------------------------------------------------------------------------
    | activoGrupoID
    |--------------------------------------------------------------------------
    |
    | Este valor define el ID de Grupoo por defecto para los filtros de activos.
    |
    */
    'activoGrupoID' => 2, // ADMINISTRACION

    /*
    |--------------------------------------------------------------------------
    | activoTipoID
    |--------------------------------------------------------------------------
    |
    | Este valor define el ID de ActivoTipo por defecto para los formularios de
    | alta/modificación de activos.
    |
    */
    'activoTipoID' => 1, // PC ESCRITORIO

    /*
    |--------------------------------------------------------------------------
    | marca
    |--------------------------------------------------------------------------
    |
    | Este valor define la marca por defecto para los formularios de alta y/o
    | modificación de activos. Se puede utilizar para la marca de otros
    | dispositivos.
    |
    */
    'marca' => '',

    /*
    |--------------------------------------------------------------------------
    | modelo
    |--------------------------------------------------------------------------
    |
    | Este valor define el modelo por defecto para los formularios de alta y/o
    | modificación de activos.
    |
    */
    'modelo' => '',

   /*
    |--------------------------------------------------------------------------
    | sistemaOperativo
    |--------------------------------------------------------------------------
    |
    | Este valor define el sistema operativo por defecto para los formularios
    | de alta y/o modificación de activos.
    |
    */
    'sistemaOperativo' => 'WINDOWS 10 PRO (64 BITS)',

    /*
    |--------------------------------------------------------------------------
    | tableResultPerPage
    |--------------------------------------------------------------------------
    |
    | Este valor define la cantidad de resultados por defecto que se muestran
    | en las tablas de resultado de la interfase.
    |
    */
    'tableResultPerPage' => 25,

    /*
    |--------------------------------------------------------------------------
    | secretaria
    |--------------------------------------------------------------------------
    |
    | Este valor define la secretaría por defecto en el reporte de toma de
    | inventario.
    |
    */
    'secretaria' => 'Honorable Concejo Deliberante 7-0-0-00-00-0-0-0',

    /*
    |--------------------------------------------------------------------------
    | dependencia
    |--------------------------------------------------------------------------
    |
    | Este valor define la dependencia por defecto en el reporte de toma de
    | inventario.
    |
    */
    'dependencia' => 'Honorable Concejo Deliberante',

    /*
    |--------------------------------------------------------------------------
    | tipo_origen
    |--------------------------------------------------------------------------
    |
    | Este valor define el tipo de origen por defecto para los formularios de
    | alta/modificación de activos.
    |
    */
    'tipo_origen' => 'OC', // Orden de Compra

    /*
    |--------------------------------------------------------------------------
    | descripTipoOrigen
    |--------------------------------------------------------------------------
    |
    | Este array define los tipos de origen predefinidos para un activo, junto
    | con sus descripciones.
    |
    */
    'descripTipoOrigen' => [
        'OC' => 'Orden de Compra',
        'OR' => 'Ordenanza',
        'DE' => 'Decreto',
        'FT' => 'Formulario de Transferencia'
    ],

    /*
    |--------------------------------------------------------------------------
    | titularidad
    |--------------------------------------------------------------------------
    |
    | Este valor define la titularidad por defecto para los formularios de
    | alta/modificación de activos.
    |
    */
    'titularidad' => '1.1', // Propios

    /*
    |--------------------------------------------------------------------------
    | descripTitularidad
    |--------------------------------------------------------------------------
    |
    | Este array define las titularidades predefinidas para un activo, junto
    | con sus descripciones.
    |
    */
    'descripTitularidad' => [
        '1.1' => 'Propios - Adquiridos',
        '1.2' => 'Propios - Donados',
        '1.3' => 'Propios - Traslados internos o externos',
        '2.1' => 'De Terceros - Comodato',
        '2.2' => 'De Terceros - Alquilado',
        '2.3' => 'De Terceros - Leasing'
    ],

    /*
    |--------------------------------------------------------------------------
    | estado
    |--------------------------------------------------------------------------
    |
    | Este valor define el estado por defecto para los formularios de
    | alta/modificación de activos.
    |
    */
    'estado' => '1', // Muy Bueno

    /*
    |--------------------------------------------------------------------------
    | descripEstado
    |--------------------------------------------------------------------------
    |
    | Este array define los estados predefinidos para un activo, junto con sus
    | descripciones.
    |
    */
    'descripEstado' => [
        '1' => 'Muy Bueno',
        '2' => 'Bueno',
        '3' => 'Regular',
        '4' => 'Malo'
    ],

    /*
    |--------------------------------------------------------------------------
    | condicion_uso
    |--------------------------------------------------------------------------
    |
    | Este valor define la condicion de uso por defecto para los formularios de
    | alta/modificación de activos.
    |
    */
    'condicion_uso' => '1', // Activo

    /*
    |--------------------------------------------------------------------------
    | descripCondicionUso
    |--------------------------------------------------------------------------
    |
    | Este array define las condiciones de uso predefinidas para un activo,
    | junto con sus descripciones.
    |
    */
    'descripCondicionUso' => [
        '1' => 'Activo',
        '2' => 'Desuso',
        '3' => 'Rezago'
    ],

    /*
    |--------------------------------------------------------------------------
    | idActivoTipoOtros
    |--------------------------------------------------------------------------
    |
    | Este valor define cuando un activo pertenece al tipo "OTROS". Lo que hace
    | es cambiar el comportamiento de volcado de datos (reportes, fichas,
    | etiquetas, etc) cuando un activo es de este tipo.
    |
    | Update: se define como array, para poder tener diferentes tipos de "OTROS".
    |
    */
    'idActivoTipoOtros' => [14, 26], // "OTROS"

    /*
    |--------------------------------------------------------------------------
    | habilitado
    |--------------------------------------------------------------------------
    |
    | Este valor define el alta del activo por defecto, para los formularios de
    | alta/modificación de activos.
    |
    */
    'habilitado' => '1', // Alta del activo

    /*
    |--------------------------------------------------------------------------
    | descripHabilitado
    |--------------------------------------------------------------------------
    |
    | Este array define la habilitación predefinida para un activo,
    | junto con sus descripciones.
    |
    */
    'descripHabilitado' => [
        '1' => 'Alta',
        '0' => 'Baja'
    ],

];

<?php

/**
 * Archivo de configuración customizado para tomar determinados defaults
 * en la aplicación.
 *
 * XXXX @ 2020-09-07
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Enable Application Backend
    |--------------------------------------------------------------------------
    |
    | This enables/disables the application backend.
    |
    */

    'backend_enabled' => (bool) env('APP_BACKEND_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | revProxySrcIp
    |--------------------------------------------------------------------------
    |
    | Define la dirección IP origen del proxy reverso.
    |
    */
    'revProxySrcIp' => env('REV_PROXY_SRC_IP', '127.0.0.1'),

    /*
    |--------------------------------------------------------------------------
    | hcdBaseURL
    |--------------------------------------------------------------------------
    |
    | Define el url base del sitio web del Concejo Deliberante.
    |
    */
    'hcdBaseUrl' => env('HCD_BASE_URL', 'http://www.concejomdp.gov.ar'),

    /*
    |--------------------------------------------------------------------------
    | docBaseUrl
    |--------------------------------------------------------------------------
    |
    | Define el url base para obtener los archivos originales de las normas.
    |
    */
    'docBaseUrl' => env('DOC_BASE_URL', 'http://www.concejomdp.gov.ar/biblioteca/'),

    /*
    |--------------------------------------------------------------------------
    | forceNoCacheDoc
    |--------------------------------------------------------------------------
    |
    | Determina si se debe agregar una variable para evitar los cache durante
    | la generación de los links para archivos originales.
    |
    */
    'forceNoCacheDoc' => true,

    /*
    |--------------------------------------------------------------------------
    | maxBackendDescriptorJson
    |--------------------------------------------------------------------------
    |
    | Determina la cantidad de resultados máxima correspondiente a la API json
    | de consulta de descriptores de backend para mejora de performance.
    | (BackendDescriptorNormasController::getBackendDescriptorJson)
    |
    */
    'maxBackendDescriptorJson' => 10,

    /*
    |--------------------------------------------------------------------------
    | charCountBackendDescriptorJson
    |--------------------------------------------------------------------------
    |
    | Determina la cantidad de caracteres mínimos requeridos para en la API json
    | de consulta de descriptores de backend para mejora de performance.
    | (BackendDescriptorNormasController::getBackendDescriptorJson)
    |
    */
    'charCountBackendDescriptorJson' => 3,

    /*
    |--------------------------------------------------------------------------
    | resultsPerPage
    |--------------------------------------------------------------------------
    |
    | Define la cantidad de resultados por página del paginador.
    |
    */
    'resultsPerPage' => 10,

    /*
    |--------------------------------------------------------------------------
    | normaVencDayCount
    |--------------------------------------------------------------------------
    |
    | Define la cantidad de días a considerar para los reportes de normas a
    | vencer.
    |
    */
    'normaVencDayCount' => 90,

    /*
    |--------------------------------------------------------------------------
    | inTestMode
    |--------------------------------------------------------------------------
    |
    | Indica si la aplicación esta corriendo como versión de prueba; esto
    | hace, entre otras cosas, que se le muestre al usuario un mensaje de
    | advertencia en todas las vistas.
    | Se recuerda que al hacer un cambio, se debe refrescar la configuración
    | ejecutando 'php artisan config:cache'.
    |
    */
    'inTestMode' => env('APP_DEMO', true),
];

<?php

// ----------------------------------------------------------------------------
// ATENCION: el orden de las rutas IMPORTA!!!
// ----------------------------------------------------------------------------

// ----------------------------------------------------------------------------
// Extra
// ----------------------------------------------------------------------------
Auth::routes();

// ----------------------------------------------------------------------------
// Index
// ----------------------------------------------------------------------------
Route::get('/', 'IndexController@index')
	->name('index');

// ----------------------------------------------------------------------------
// Dashboard
// ----------------------------------------------------------------------------
Route::get('/dashboard', 'DashboardController@index')
	->name('dashboard');

// ----------------------------------------------------------------------------
// Activos
// ----------------------------------------------------------------------------
Route::get('/activos', 'ActivoController@index')
	->name('activos.index');

Route::get('/activos/create', 'ActivoController@create')
	->name('activos.create');

Route::get('/activos/reportes/exportpdf','ActivoController@exportPDF')
	->name('activos.exportpdf');

Route::get('/activos/reportes/tomainventario','ActivoController@tomaInventario')
	->name('activos.tomainventario');

Route::get('/activos/{activo}/edit', 'ActivoController@edit')
	->name('activos.edit');

Route::get('/activos/{activo_origen}/clone', 'ActivoController@clone')
	->name('activos.clone');

Route::get('/activos/{activo}/delete', 'ActivoController@destroy')
	->name('activos.delete');

Route::get('/activos/{activo}/print', 'ActivoController@show')
	->name('activos.showprint');

Route::get('/activos/{activo}', 'ActivoController@show')
	->name('activos.show');

Route::post('/activos', 'ActivoController@store')
	->name('activos.store');

Route::put('/activos/{activo}', 'ActivoController@update')
	->name('activos.update');

Route::get('/activos/{activo}/fichapdf', 'ActivoController@fichaPDF')
	->name('activos.fichapdf');

Route::get('/activos/{activo}/etiquetapdf', 'ActivoController@etiquetaPdf')
 	->name('activos.etiquetapdf');

Route::get('/activos/eliminaradjunto/{filename}', 'ActivoController@deleteFile')
	->name('activos.eliminaradjunto');

// JSON responses -------------------------------------------------------------
Route::get('/activos/getdata/json', 'ActivoController@getDatatablesJson')
	->name('activos.getdatatablesjson');

Route::get('/activos/getdata/search/marca/json', 'ActivoController@getAutocompleteMarcaJson')
	->name('activos.getautocompletemarcajson');

Route::get('/activos/getdata/search/modelo/json', 'ActivoController@getAutocompleteModeloJson')
	->name('activos.getautocompletemodelojson');

Route::get('/activos/getdata/search/orden_compra/json', 'ActivoController@getAutocompleteOrdenCompraJson')
	->name('activos.getautocompleteordencomprajson');

Route::get('/activos/getdata/search/sistema_operativo/json', 'ActivoController@getAutocompleteSistemaOperativoJson')
	->name('activos.getautocompletesistemaoperativojson');

Route::get('/activos/getdata/search/cpu/json', 'ActivoController@getAutocompleteCpuJson')
	->name('activos.getautocompletecpujson');

Route::get('/activos/getdata/search/motherboard/json', 'ActivoController@getAutocompleteMotherboardJson')
	->name('activos.getautocompletemotherboardjson');

Route::get('/activos/getdata/search/hd_marca/json', 'ActivoController@getAutocompleteHdMarcaJson')
	->name('activos.getautocompletehdmarcajson');

Route::get('/activos/getdata/search/dvd_rw_marca/json', 'ActivoController@getAutocompleteDvdRwMarcaJson')
	->name('activos.getautocompletedvdrwmarcajson');

Route::get('/activos/getdata/search/getbyid/json/{id}', 'ActivoController@jsonGetById')
 	->name('activos.jsongetbyid');

Route::post('/activos/getdata/search/verifybynroinventario/json', 'ActivoController@jsonVerifyByNroInventario')
 	->name('activos.jsonverifybynroinventario');

// ----------------------------------------------------------------------------
// Grupos
// ----------------------------------------------------------------------------
Route::get('/grupos/getdata/search/tipos/json/{grupo_id}', 'GrupoController@jsonGetTiposById')
 	->name('grupos.jsongettiposbyid')
 	->where('grupo_id', '^(all|[0-9]+)$');

// JSON responses -------------------------------------------------------------

// ----------------------------------------------------------------------------
// Responsables
// ----------------------------------------------------------------------------

// JSON responses -------------------------------------------------------------
Route::get('/responsables/getdata/search/responsable/json', 'ResponsableController@getAutocompleteResponsableJson')
	->name('responsables.getautocompleteresponsablejson');

Route::get('/responsables/getdata/search/responsablefiltro/json', 'ResponsableController@getAutocompleteResponsableFiltroJson')
	->name('responsables.getautocompleteresponsablefiltrojson');

// ----------------------------------------------------------------------------
// Areas
// ----------------------------------------------------------------------------

// JSON responses -------------------------------------------------------------
Route::get('/areas/getdata/search/area/json', 'AreaController@getAutocompleteAreaJson')
	->name('areas.getautocompleteareajson');

// ----------------------------------------------------------------------------
// QR
// ----------------------------------------------------------------------------

Route::get('/qr/activos/{activo}', 'QRController@generateActivoQR')
	->name('qr.activo');

// ----------------------------------------------------------------------------
// ImportData
// ----------------------------------------------------------------------------

// JSON responses -------------------------------------------------------------
Route::get('/importdata/sgl', 'ImportDataController@importSGLData')
	->name('importdata.importsgldata');

// ----------------------------------------------------------------------------
// Remote Token Login
// ----------------------------------------------------------------------------

Route::get('/remote/login/{email}/{token}', 'Auth\RemoteLoginController@remoteLogin')
	->name('remotelogin.remotelogin');

// JSON responses -------------------------------------------------------------
Route::post('/remote/token/generate', 'Auth\RemoteLoginController@generateRemoteToken')
	->name('remotelogin.generateremotetoken');


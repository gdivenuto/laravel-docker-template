<?php

use Illuminate\Support\Facades\Route;

// ----------------------------------------------------------------------------
// ATENCION: el orden de las rutas IMPORTA!!!
// ----------------------------------------------------------------------------

// ----------------------------------------------------------------------------
// Auth Enabled only if application allows it.
// ----------------------------------------------------------------------------
if (config('params.backend_enabled')) {

	// ---- Default Auth routes -----------------------------------------------
	Auth::routes();

	// ---- Remote Token Login ------------------------------------------------
	Route::get('/remote/login/{email}/{token}', 'Auth\RemoteLoginController@remoteLogin')
		->name('remotelogin.remotelogin');

	// ---- JSON responses
	Route::post('/remote/token/generate', 'Auth\RemoteLoginController@generateRemoteToken')
		->name('remotelogin.generateremotetoken');
}

// ****************************************************************************
// FRONTEND
// ****************************************************************************

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

Route::get('/dashboard/dbselector', 'DashboardController@showDBSelector')
	->name('dashboard.showdbselector');

// ----------------------------------------------------------------------------
// Normas
// ----------------------------------------------------------------------------
Route::get('/normas/search/simple/{normas_db}/{descriptor_id?}', 'NormaController@searchSimple')
	->name('normas.searchsimple')
	->where([
		'normas_db' => '^[a-zA-Z0-9 ]{1,100}$', // '^(digesto|normas|sesiones|decretos|todas)+$',
		'descriptor_id' => '^[0-9-]+$'
	]);

Route::get('/normas/search/simple/{normas_db}/tag/{descriptor_logic}/{tag_str}', 'NormaController@searchSimpleTag')
	->name('normas.searchsimpletag')
	->where([
		'normas_db' => '^[a-zA-Z0-9 ]{1,100}$', // '^(digesto|normas|sesiones|decretos|todas)+$',
		'descriptor_logic' => '^(or|and)$',
		'tag_str' => '^[a-zA-Z0-9ñÑáéíóúüÁÉÍÓÚÜ |/\.,:;"&º-]{1,150}$'
	]);

Route::get('/normas/search/{normas_db}', 'NormaController@search')
	->middleware('browser.preventhistoryback')
	->where(['normas_db' => '^[a-zA-Z0-9 ]{1,100}$'])
	->name('normas.search');

// Route::get('/normas/search/content', 'NormaController@searchByContent')
// 	->middleware('browser.preventhistoryback')
// 	->name('normas.searchcontent');

Route::get('/normas/search/keyword/{normas_db}', 'NormaController@searchByKeyword')
	->middleware('browser.preventhistoryback')
	->where(['normas_db' => '^[a-zA-Z0-9 ]{1,100}$'])
	->name('normas.searchkeyword');

Route::post('/normas/search/keyword', 'NormaController@searchByKeywordRedirect')
	->name('normas.searchkeywordredirect');

Route::get('/normas/expired', 'NormaController@normasVenc')
	->middleware('browser.preventhistoryback')
	->name('normas.normasvenc');

Route::get('/normas/show/{normas_db?}/acto/{acto}/nro/{nro}/{force_digesto_id?}', 'NormaController@showByActoNumero')
	->name('normas.showbyactonumero')
	->where([
		'normas_db' => '^[a-zA-Z0-9 ]{1,100}$',
		'acto' => \App\Helpers\FakeAttrib::tipoActoRegEx(),
		'nro' => '^[0-9-]+$',
		'force_digesto_id' => '^[0-9]{1,4}$'
	]);

Route::get('/normas/show/{normas_db?}/{norma}', 'NormaController@show')
	->name('normas.show')
	->where([
		'normas_db' => '^[a-zA-Z0-9 ]{1,100}$',
		'norma' => '^[0-9]+$'
	]);

// JSON responses -------------------------------------------------------------

Route::get('/normas/getdata/clearsession/json/{search_type}', 'NormaController@clearSessionSearchJson')
	->name('normas.clearsessionsearchjson')
	->where(['search_type' => '^[a-z_]+$']);

Route::get('/normas/getdata/norma/json/{norma_id}', 'NormaController@getNormaJson')
	->name('normas.getnormajson')
	->where(['norma_id' => '^[0-9]+$']);

Route::get('/normas/getdata/normabyactonumero/json/{normas_db?}/acto/{acto}/nro/{nro}', 'NormaController@getByActoNumeroJson')
	->name('normas.getbyactonumerojson')
	->where([
		'normas_db' => '^[a-zA-Z0-9 ]{1,100}$',
		'acto' => \App\Helpers\FakeAttrib::tipoActoRegEx(),
		'nro' => '^[0-9-]+$'
	]);

Route::get('/normas/getdata/search/descriptor/json/{normas_db}/{descriptor_logic}/{descriptor_id_list}', 'NormaController@getNormasByDescriptorJson')
	->name('normas.getnormasbydescriptorjson')
	->where([
		'normas_db' => '^[a-zA-Z0-9 ]{1,100}$',
		'descriptor_logic' => '^(or|and)$',
		'descriptor_id_list' => '^(\d+(,\d+)*)?$' // enteros separados por coma, cadena vacia inválida
	]);

Route::post('/normas/getdata/advsearch/descriptor/json/{normas_db}', 'NormaController@getNormasAdvSearchJson')
	->where(['normas_db' => '^[a-zA-Z0-9 ]{1,100}$'])
	->name('normas.getnormasadvsearchjson');

// Route::post('/normas/getdata/content/fulltext/json', 'NormaController@getNormasContentSearchJson')
// 	->name('normas.getnormascontentsearchjson');

Route::post('/normas/getdata/keyword/json/{normas_db}', 'NormaController@getNormasKeywordSearchJson')
	->where(['normas_db' => '^[a-zA-Z0-9 ]{1,100}$'])
	->name('normas.getnormaskeywordsearchjson');

Route::get('/normas/getdata/expired/json', 'NormaController@getNormasAVencerJson')
	->name('normas.getnormasavencerjson');

// PDF output -----------------------------------------------------------------

Route::get('/normas/pdf/descriptor/{normas_db}/{descriptor_logic}/{descriptor_id_list}', 'NormaController@getNormasByDescriptorPdf')
	->where([
		'normas_db' => '^[a-zA-Z0-9 ]{1,100}$',
		'descriptor_logic' => '^(or|and)$',
		'descriptor_id_list' => '^(\d+(,\d+)*)?$' // enteros separados por coma, cadena vacia inválida
	])
	->name('normas.getnormasbydescriptorpdf');

Route::get('/normas/pdf/advsearch/{normas_db}', 'NormaController@getNormasAdvPdf')
	->where(['normas_db' => '^[a-zA-Z0-9 ]{1,100}$'])
	->name('normas.getnormasadvpdf');

Route::get('/normas/pdf/keyword/{normas_db}', 'NormaController@getNormasKeywordPdf')
	->where(['normas_db' => '^[a-zA-Z0-9 ]{1,100}$'])
	->name('normas.getnormaskeywordpdf');

Route::get('/normas/pdf/expired', 'NormaController@getNormasAVencerPdf')
	->name('normas.getnormasavencerpdf');

Route::get('/normas/pdf/show/{normas_db?}/{norma}', 'NormaController@showPdf')
	->name('normas.showpdf')
	->where([
		'normas_db' => '^[a-zA-Z0-9 ]{1,100}$',
		'norma' => '^[0-9]+$'
	]);

// Ruta para la generación de un PDF con la norma en formato TRANSP(winisis).
Route::get('/normas/pdf/transporte/{normas_db?}/{norma}', 'NormaController@showTransporte')
	->name('normas.showtransporte')
	->where([
		'normas_db' => '^[a-zA-Z0-9 ]{1,100}$',
		'norma' => '^[0-9]+$'
	]);

// ----------------------------------------------------------------------------
// Digesto Output
// ----------------------------------------------------------------------------

// JSON responses -------------------------------------------------------------
Route::get('/digesto/getdata/astext/json', 'NormaController@generateDigestoTextFileJson')
	->name('digesto.generatedigestotextfilejson');

// ----------------------------------------------------------------------------
// Descriptores
// ----------------------------------------------------------------------------

// JSON responses -------------------------------------------------------------
Route::get('/descriptores/getdata/search/tag/json', 'DescriptorController@getAutocompleteDescriptorJson')
	->name('descriptores.getautocompletedescriptorjson');

Route::post('/descriptores/getdata/search/id/json', 'DescriptorController@getDescriptorByIdJson')
	->name('descriptores.getdescriptorbyidjson');

// ----------------------------------------------------------------------------
// QR
// ----------------------------------------------------------------------------

Route::get('/qr/normas/{normas_db}/{norma}', 'QRController@generateNormaUrlQR')
	->where([
		'normas_db' => '^[a-zA-Z0-9 ]{1,100}$',
		'norma' => '^[0-9]+$'
	])
	->name('qr.norma');

Route::get('/qr/normas/search/simple/{normas_db}/tag/{descriptor_logic}/{tag_str}', 'QRController@generateSearchSimpleTagQR')
	->where([
		'normas_db' => '^[a-zA-Z0-9 ]{1,100}$', // '^(digesto|normas|sesiones|decretos|todas)+$',
		'descriptor_logic' => '^(or|and)$',
		'tag_str' => '^[a-zA-Z0-9ñÑáéíóúüÁÉÍÓÚÜ |/\.,:;"&º-]{1,150}$'
	])
	->name('qr.normasearchsimpletag');

// ****************************************************************************
// BACKEND
// 
// Nota: las rutas del backend se habilitan solamente si el módulo de backend
// está habilitado.
// ****************************************************************************
if (config('params.backend_enabled')) {
	// ------------------------------------------------------------------------
	// Backend: Dashboard
	// ------------------------------------------------------------------------

	Route::get('/backend/dashboard', 'BackendDashboardController@index')
		->name('backend.dashboard.index');

	Route::get('/backend/dashboard/publish', 'BackendDashboardController@publishConfirm')
		->name('backend.dashboard.publishconfirm');

	Route::post('/backend/dashboard/publish', 'BackendDashboardController@publish')
		->name('backend.dashboard.publish');

	// ------------------------------------------------------------------------
	// Backend: Normas
	// ------------------------------------------------------------------------

	Route::get('/backend/normas', 'BackendNormaController@index')
		->name('backend.normas.index');

	Route::get('/backend/normas/create', 'BackendNormaController@create')
		->name('backend.normas.create');

	Route::get('/backend/normas/{norma}/edit', 'BackendNormaController@edit')
		->name('backend.normas.edit');

	Route::get('/backend/normas/{norma}/clonar', 'BackendNormaController@clonar')
		->name('backend.normas.clonar');

	Route::get('/backend/normas/{norma}', 'BackendNormaController@show')
		->name('backend.normas.show');

	Route::post('/backend/normas', 'BackendNormaController@store')
		->name('backend.normas.store');

	Route::put('/backend/normas/{norma}', 'BackendNormaController@update')
		->name('backend.normas.update');

	// Datatables API / JSON responses ----------------------------------------
	Route::get('/backend/normas/dt/get', 'BackendNormaController@dtGetNormasJson')
		->name('backend.normas.dtgetnormasjson');

	Route::get('/backend/normas/dt/getactos', 'BackendNormaController@dtGetActosJson')
		->name('backend.normas.dtgetactosjson');
	
	// ------------------------------------------------------------------------
	// Backend: Descriptores x Norma
	// ------------------------------------------------------------------------
	
	Route::get('/backend/normas/{norma}/edit/descriptores', 'BackendDescriptorNormasController@edit')
		->name('backend.descriptornormas.edit');

	Route::put('/backend/normas/{norma}/edit/descriptores', 'BackendDescriptorNormasController@update')
		->name('backend.descriptornormas.update');

	// JSON responses ---------------------------------------------------------
	Route::get('/backend/descriptores/search/tag/json', 'BackendDescriptorNormasController@getBackendDescriptorJson')
		->name('backend.descriptornormas.getdescriptoresjson');

	Route::post('/backend/descriptores/add/json', 'BackendDescriptorNormasController@addBackendDescriptorJson')
		->name('backend.descriptornormas.adddescriptorjson');

	// ------------------------------------------------------------------------
	// Backend: HCD Expedientes
	// ------------------------------------------------------------------------
	Route::get('/backend/normas/{norma}/edit/hcd_expedientes', 'BackendHcdExpedientesController@edit')
		->name('backend.hcdexpedientes.edit');

	Route::put('/backend/normas/{norma}/edit/hcd_expedientes', 'BackendHcdExpedientesController@update')
		->name('backend.hcdexpedientes.update');

	// ------------------------------------------------------------------------
	// Backend: Actas
	// ------------------------------------------------------------------------
	Route::get('/backend/normas/{norma}/edit/actas', 'BackendActasController@edit')
		->name('backend.actas.edit');

	Route::put('/backend/normas/{norma}/edit/actas', 'BackendActasController@update')
		->name('backend.actas.update');

	// ------------------------------------------------------------------------
	// Backend: Observaciones
	// ------------------------------------------------------------------------
	Route::get('/backend/normas/{norma}/edit/observaciones', 'BackendObservacionesController@edit')
		->name('backend.observaciones.edit');

	Route::put('/backend/normas/{norma}/edit/observaciones', 'BackendObservacionesController@update')
		->name('backend.observaciones.update');

	// ------------------------------------------------------------------------
	// Backend: Procesamientos
	// ------------------------------------------------------------------------
	Route::get('/backend/normas/{norma}/edit/procesamientos', 'BackendProcesamientosController@edit')
		->name('backend.procesamientos.edit');

	Route::put('/backend/normas/{norma}/edit/procesamientos', 'BackendProcesamientosController@update')
		->name('backend.procesamientos.update');	

	// ------------------------------------------------------------------------
	// Backend: Abstenciones
	// ------------------------------------------------------------------------
	Route::get('/backend/normas/{norma}/edit/abstenciones', 'BackendAbstencionesController@edit')
		->name('backend.abstenciones.edit');

	Route::put('/backend/normas/{norma}/edit/abstenciones', 'BackendAbstencionesController@update')
		->name('backend.abstenciones.update');	

	// ------------------------------------------------------------------------
	// Backend: Relaciones
	// ------------------------------------------------------------------------
	Route::get('/backend/normas/{norma}/edit/relaciones', 'BackendRelacionesController@edit')
		->name('backend.relaciones.edit');

	Route::put('/backend/normas/{norma}/edit/relaciones', 'BackendRelacionesController@update')
		->name('backend.relaciones.update');

	// ------------------------------------------------------------------------
	// Backend: Digestos
	// ------------------------------------------------------------------------

	Route::get('/backend/digestos', 'BackendDigestoController@index')
		->name('backend.digestos.index');

	Route::get('/backend/digestos/create', 'BackendDigestoController@create')
		->name('backend.digestos.create');

	Route::get('/backend/digestos/{digesto}/edit', 'BackendDigestoController@edit')
		->name('backend.digestos.edit');

	Route::post('/backend/digestos', 'BackendDigestoController@store')
		->name('backend.digestos.store');

	Route::put('/backend/digestos/{digesto}', 'BackendDigestoController@update')
		->name('backend.digestos.update');

	// Datatables API / JSON responses ----------------------------------------
	Route::get('/backend/digestos/dt/get', 'BackendDigestoController@dtGetDigestosJson')
		->name('backend.digestos.dtgetdigestosjson');

	// ------------------------------------------------------------------------
	// Backend: Descriptores x Digesto
	// ------------------------------------------------------------------------
	
	Route::get('/backend/digestos/{digesto}/edit/descriptores', 'BackendDescriptorDigestosController@edit')
		->name('backend.descriptordigestos.edit');

	Route::put('/backend/digestos/{digesto}/edit/descriptores', 'BackendDescriptorDigestosController@update')
		->name('backend.descriptordigestos.update');

	// ------------------------------------------------------------------------
	// Backend: Intendencias
	// ------------------------------------------------------------------------

	Route::get('/backend/intendencias', 'BackendIntendenciasController@index')
		->name('backend.intendencias.index');

	Route::get('/backend/intendencias/create', 'BackendIntendenciasController@create')
		->name('backend.intendencias.create');

	Route::get('/backend/intendencias/{intendencia}/edit', 'BackendIntendenciasController@edit')
		->name('backend.intendencias.edit');

	Route::post('/backend/intendencias', 'BackendIntendenciasController@store')
		->name('backend.intendencias.store');

	Route::put('/backend/intendencias/{intendencia}', 'BackendIntendenciasController@update')
		->name('backend.intendencias.update');

	Route::get('/backend/intendencias/{intendencia}', 'BackendIntendenciasController@destroy')
		->name('backend.intendencias.destroy');

	// Datatables API / JSON responses ----------------------------------------
	Route::get('/backend/intendencias/dt/get', 'BackendIntendenciasController@dtGetIntendenciasJson')
		->name('backend.intendencias.dtgetintendenciasjson');

	// ------------------------------------------------------------------------
	// Backend: Descriptores
	// ------------------------------------------------------------------------

	Route::get('/backend/descriptores', 'BackendDescriptoresController@index')
		->name('backend.descriptores.index');

	Route::get('/backend/descriptores/create', 'BackendDescriptoresController@create')
		->name('backend.descriptores.create');

	Route::get('/backend/descriptores/choise', 'BackendDescriptoresController@choise')
		->name('backend.descriptores.choise');

	Route::get('/backend/descriptores/buscar', 'BackendDescriptoresController@buscarDescriptores')
		->name('backend.descriptores.buscar');

	Route::post('/backend/descriptores/replace', 'BackendDescriptoresController@replace')
		->name('backend.descriptores.replace');

	Route::get('/backend/descriptores/{descriptor}/edit', 'BackendDescriptoresController@edit')
		->name('backend.descriptores.edit');

	Route::post('/backend/descriptores', 'BackendDescriptoresController@store')
		->name('backend.descriptores.store');

	Route::put('/backend/descriptores/{descriptor}', 'BackendDescriptoresController@update')
		->name('backend.descriptores.update');

	Route::get('/backend/descriptores/{descriptor}', 'BackendDescriptoresController@destroy')
		->name('backend.descriptores.destroy');


	// Datatables API / JSON responses ----------------------------------------
	Route::get('/backend/descriptores/dt/get', 'BackendDescriptoresController@dtGetDescriptoresJson')
		->name('backend.descriptores.dtgetdescriptoresjson');

	// ------------------------------------------------------------------------
	// Backend: Auditoría
	// ------------------------------------------------------------------------

	Route::get('/backend/audit', 'BackendAuditController@index')
		->name('backend.audit.index');

	// Datatables API / JSON responses ----------------------------------------
	Route::get('/backend/audit/dt/get', 'BackendAuditController@dtGetAuditJson')
		->name('backend.audit.dtgetauditjson');

}
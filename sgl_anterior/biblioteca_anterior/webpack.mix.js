const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

// Genero un único archivo .js con todas las librerias
mix.js(
	[
		'resources/js/app.js',
		'resources/js/extra.js',
		//'resources/js/custHelper.js',
	], 'public/js/app.js')

// sass -> css
	.sass('resources/sass/app.scss', 'public/css')

// fix para el error 404 de popper
	.js('node_modules/popper.js/dist/popper.js', 'public/js')
	.sourceMaps();

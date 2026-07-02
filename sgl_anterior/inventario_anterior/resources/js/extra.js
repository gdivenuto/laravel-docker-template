/**
 * Conjunto de 'requires' para librerias extras
 *
 * Se agregan en ./src/webpack.mix.js
 */

// moment
window.moment = require('moment'); // 'Moment' requires injection this way, using 'window.moment...'

// sprintf-js
require('sprintf-js');

// datatables
require('datatables.net');
require('datatables.net-bs4');
require('datatables.net-responsive');
require('datatables.net-responsive-bs4');
require('datatables.net-buttons');
require('datatables.net-buttons-bs4');
//require('datatables.net-buttons/js/buttons.colVis.js')(); // Column visibility
//require('datatables.net-buttons/js/buttons.html5.js')();  // HTML 5 file export
//require('datatables.net-buttons/js/buttons.flash.js')();  // Flash file export
//require('datatables.net-buttons/js/buttons.print.js')();  // Print view button
//require('datatables.net-select');
//require('datatables.net-select-bs'); // Commented out because fails the versioning; only include the css in app.scss

// TempusDominus DateTimePicker
require('tempusdominus-core');
require('tempusdominus-bootstrap-4');

// select2
require('select2');
//require('select2-theme-bootstrap4'); // Commented out because fails the versioning; only include the css in app.scss

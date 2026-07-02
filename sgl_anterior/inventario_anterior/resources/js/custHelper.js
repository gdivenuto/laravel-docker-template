/**
 * Set of custom 'helper functions' for the app.
 *
 * Added in webpack.mix.js
 *
 * By Kaleb
 */

!function() {
    'use strict'

	/**
	 * Converts a UTC date to a relative date using the local() time.
	 * REQUIRES: 'moment' library.
	 * @param  string dateParam UTC DateTime in 'YYYY-MM-DD HH:mm:ss' format.
	 * @return string           Relative day.
	 */
	function utcDateFormat (dateParam) 
	{
	    var from_date_utc = moment(dateParam);
	    var current_utc = moment().utc().format('YYYY-MM-DD HH:mm:ss');
	    var difference = from_date_utc.diff(current_utc, 'days');
	    if (difference < -2)
	        return moment.utc(dateParam).local().format('YYYY-MM-DD HH:mm:ss');
	    else
	        return from_date_utc.from(current_utc); 
	}

	/**
	 * Trims a string by length.
	 * @param  {string} str String to trim
	 * @param  {integer} len Lenght to trim at.
	 * @return {string}     result
	 */
	function trimString(str, len) 
	{
        return (str.length > len) ? str.substr(0, len+1) + '&hellip;' : str;		
	}

	/**
	 * Renders a small button bar
	 * @param  {array} options Each element represents a button's html representation.
	 * @return {string}        Button bar html.
	 */
	function renderSmallButtonBar(options)
	{
		var output = '<div class="btn-group" role="group" aria-label="Acciones">';
		$.each(options, function(index, value) {
			output += value;
		});
		output += '</div>';
		return output;
	}

	/**
	 * Renders a progress bar
	 * @param  {int} min        Minimum value
	 * @param  {int} current    Current value
	 * @param  {int} max        Maximum value
	 * @param  {string} text       Caption text
	 * @param  {string} extraClass Extra class for the progress bar
	 * @return {string}            Progress bar html.
	 */
	function renderProgressBar(min, current, max, text, extraClass)
	{
        var perc = Math.floor((current / (max - min)) * 100);
        var status = ( current > Math.floor(max / 2)) ? 'progress-bar-success' : 'progress-bar-danger';
        return sprintf('<div class="progress"><div class="progress-bar %s %s" role="progressbar" aria-valuenow="%d" aria-valuemin="%d" aria-valuemax="%d" style="width: %d%%"><span>%s</span></div></div>',
        	status, extraClass, current, min, max, perc, text);
	}

	/**
	 * Wrapper to call JSON controllers actions (ajax)
	 * @param  {array} options 
	 */
	function ajaxControllerAction(options) 
	{
		options = options || {};
		var p_url = options.url || '';
		var p_done = options.done || function (data) {};
		var p_fail = options.fail || function (data) { alert('Error on ajax call.'); };

		$.ajax({
			url: p_url,
			contentType: 'application/json',
            dataType: 'json'
		})
		.done(p_done)
		.fail(p_fail);
	}

	/**
	 * Decodes HTML Entities in a string
	 * @param  {[type]} str [description]
	 * @return {[type]}     [description]
	 */
	function htmlEntitiesDecode(str)
	{
		return $('<textarea />').html(str).text();
	}

	/**
	 * Formats a text into a MAC Address
	 * @param  {[type]} e [description]
	 * @return {[type]}   [description]
	 */
	function formatMAC(e) {
	    var r = /([a-f0-9]{2})([a-f0-9]{2})/i,
	        str = e.target.value.replace(/[^a-f0-9]/ig, "");

	    while (r.test(str)) {
	        str = str.replace(r, '$1' + ':' + '$2');
	    }

	    e.target.value = str.slice(0, 17);
	};

	/**
     * export to either browser or node.js
     */
    /* eslint-disable quote-props */
    if (typeof exports !== 'undefined') {
        exports['ajaxControllerAction'] = ajaxControllerAction;
        exports['utcDateFormat'] = utcDateFormat;
        exports['trimString'] = trimString;
        exports['renderSmallButtonBar'] = renderSmallButtonBar;
        exports['renderProgressBar'] = renderProgressBar;
        exports['htmlEntitiesDecode'] = htmlEntitiesDecode;
        exports['formatMAC'] = formatMAC;
    }
    if (typeof window !== 'undefined') {
        window['ajaxControllerAction'] = ajaxControllerAction;
        window['utcDateFormat'] = utcDateFormat;
        window['trimString'] = trimString;
        window['renderSmallButtonBar'] = renderSmallButtonBar;
        window['renderProgressBar'] = renderProgressBar;
        window['htmlEntitiesDecode'] = htmlEntitiesDecode;
        window['formatMAC'] = formatMAC;

        if (typeof define === 'function' && define['amd']) {
            define(function() {
                return {
                    'ajaxControllerAction': ajaxControllerAction,
                    'utcDateFormat': utcDateFormat,
                    'trimString': trimString,
                    'renderSmallButtonBar': renderSmallButtonBar,
                    'renderProgressBar': renderProgressBar,
                    'htmlEntitiesDecode': htmlEntitiesDecode,
                    'formatMAC': formatMAC
                }
            })
        }
    }
    /* eslint-enable quote-props */
}()
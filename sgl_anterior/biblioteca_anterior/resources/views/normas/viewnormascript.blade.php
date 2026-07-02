// Delegates (view norma) -------------------------------------------------
var disableInvalidLinks = function () {
    $('a.link-norma-doc').each(function () {
        var link_obj = $(this);
        var doc_url = link_obj.attr('href');
        $.ajax({
            method: 'HEAD',
            url: doc_url,
            // custom attribute
            link_instance: link_obj
        })
        .fail(function(jqXHR, textStatus, errorThrown ) {
            var contents = this.link_instance.contents();
            contents.unwrap();
            contents.wrap('<del></del>');
        });
    });
};

var getNorma = function (norma_id) {
	$.ajax({
        method: 'GET',
        url: `{{ route('normas.getnormajson', [ 'norma_id' => '_norma-id_' ]) }}`.replace('_norma-id_', norma_id),
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        contentType: 'application/json'
    })
    .done(function(response, textStatus, jqXHR) {
        if (response.status == 'OK') {
        	var norma = response.data;
        	$('#nro_hcd').html(norma.nro_hcd ?? '(no posee)');
        	
        	$('#hcd_expedientes').empty();
        	if (norma.hcd_expedientes.length > 0)
        		$.each(norma.hcd_expedientes, function (i, v) { $('#hcd_expedientes').append(v.hcd_exped + '&nbsp;'); });
        	else
				$('#hcd_expedientes').html('(no posee)');

			if (norma.bloque != '')
				$('#bloque').html(`<strong class="text-uppercase">D&iacute;gito:</strong> ${norma.bloque}`);
			else
				$('#bloque').empty();

			$('#acto_desc').html(norma.acto_desc);
			$('#nro').html(norma.nro);

			$('#url_html').attr('href', norma.url_html);
			$('#url_pdf').attr('href', norma.url_pdf);
			$('#url_actualizado_html').attr('href', norma.url_actualizado_html);
			$('#url_actualizado_pdf').attr('href', norma.url_actualizado_pdf);

			$('#exped').html((norma.exped != '') ? norma.exped : '(no posee)');

			$('#fec_sancion').html((norma.fec_sancion) ? norma.fec_sancion : '(sin fecha)');
			$('#fec_promulga').html((norma.fec_promulga) ? norma.fec_promulga : '(sin fecha)');
			$('#fec_publica').html((norma.fec_publica) ? norma.fec_publica : '(sin fecha)');

        	disableInvalidLinks();

        	$('#modalViewNorma').modal();
    	}
    })
    .fail(function(jqXHR, textStatus, errorThrown ) {
        console.log('error');
    });
};
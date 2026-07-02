var disablePaginator = function () {
    $('li.paginator-first').addClass('disabled');
    $('li.paginator-prev').addClass('disabled');
    $('li.paginator-next').addClass('disabled');
    $('li.paginator-last').addClass('disabled');
};

var setupPaginator = function () {
    $('nav.paginator').hide();

    $('a.paginator-link').unbind('click');
    $('a.paginator-link').click(function (e) {
        e.preventDefault();
        doSearch($(this).data('dstpage'));
    });
};

var renderPaginator = function (data) {
    if (data.last_page == 1) $('nav.paginator').hide();
    else $('nav.paginator').show();

    $('a.paginator-text').html(`Página ${data.current_page} de ${data.last_page}`);
    
    if (data.current_page == 1) 
        $('li.paginator-first,li.paginator-prev').addClass('disabled');
    else {
        $('li.paginator-first a').attr('href', data.first_page_url);
        $('li.paginator-first a').data('dstpage', 1);
        $('li.paginator-prev a').attr('href', data.prev_page_url);
        $('li.paginator-prev a').data('dstpage', data.current_page - 1);
        $('li.paginator-prev,li.paginator-first').removeClass('disabled');
    };

    if (data.current_page == data.last_page)
        $('li.paginator-next,li.paginator-last').addClass('disabled');
    else {
        $('li.paginator-next a').attr('href', data.next_page_url);
        $('li.paginator-next a').data('dstpage', data.current_page + 1);
        $('li.paginator-last a').attr('href', data.last_page_url);
        $('li.paginator-last a').data('dstpage', data.last_page);        
        $('li.paginator-next,li.paginator-last').removeClass('disabled');
    };
};

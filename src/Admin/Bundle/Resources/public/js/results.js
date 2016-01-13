

$(document).ready(function () {

    var body = $('body'),
        orderBy = 'last DESC',
        searchTimeout = null,
        searchRequest = null;

    function loadContent (data) {
        var admin = jQuery('#results'),
            content = $(data);
        admin.find('table.results').each(function (i) {
            $(this).find('> tbody > tr').remove();
            content.filter('#results').find('table.results').eq(i).find('> tbody > tr').appendTo($(this).find('> tbody'));
            $(this).find('> thead > tr > th').each(function (j) {
                $(this).find('label:first-child > *:not(select):not(input)').remove();
                content.filter('#results').find('table.results > thead > tr > th').eq(j).find('label:first-child > *:not(select):not(input)').prependTo($(this).find('label:first-child'));
            });
        });
        admin.find('.page-total').text(content.find('.page-total').text());
    }

    function getData()
    {
        var admin = jQuery('#results');
        var result = {
            order: orderBy,
            search: admin.find('input[name="search"]').val().trim(),
            page: admin.find('input[name="page"]').val().trim(),
            group: admin.find('select[name="group"]').val().trim(),
            last: admin.find('select[name="last"]').val().trim(),
            completed: admin.find('select[name="completed"]').val().trim(),
        };

        //for(var i = 1; i <= 17; i++) {
        //    result['lesson' + i] = admin.find('select[name="lesson' + i + '"]').val().trim();
        //}

        return result;
    }

    function loadResults() {
        if(searchRequest != null)
            searchRequest.abort();
        if(searchTimeout != null)
            clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function () {
            searchRequest = $.ajax({
                url: window.callbackPaths['results_callback'],
                type: 'GET',
                dataType: 'text',
                data: getData(),
                success: loadContent
            });
        }, 100);
    }


    body.on('keyup change', '#results input[name="search"], #results input[name="page"]', function () {
        if(searchTimeout != null)
            clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadResults, 1000);
    });

    body.on('change', '#results table.results > thead > tr > th:not(:last-child) > label > select, #results table.results > thead > tr > th:not(:last-child) > label > input', function () {
        var that = $(this);

        if(that.val() == '_ascending' || that.val() == '_descending')
        {
            orderBy = that.attr('name') + (that.val() == '_ascending' ? ' ASC' : ' DESC');
            that.val(that.data('last') || that.find('option').first().attr('value'));
        }
        else if(that.val().trim() != '')
        {
            that.parents('th').removeClass('unfiltered').addClass('filtered');
            that.data('last', that.val());
        }
        else
        {
            that.parents('th').removeClass('filtered').addClass('unfiltered');
            that.data('last', that.val());
        }

        loadResults();
    });

});

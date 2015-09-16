

$(document).ready(function () {

    var body = $('body'),
        orderBy = 'last DESC',
        searchTimeout = null,
        searchRequest = null;

    function loadContent (data) {
        var admin = jQuery('#results'),
            content = $(data);
        admin.find('table.results > tbody > tr').remove();
        content.find('table.results > tbody > tr').appendTo(admin.find('table.results > tbody'));
        admin.find('table.results > thead > tr > th').each(function (i) {
            $(this).find('label:first-child > *:not(select):not(input)').remove();
            content.find('.pane-content th').eq(i).find('label:first-child > *:not(select):not(input)').prependTo($(this).find('label:first-child'));
        });
        admin.find('#page-total').text(content.find('#page-total').text());
    }

    function getData()
    {
        var admin = jQuery('#results');
        var result = {
            order: orderBy,
            search: admin.find('input[name="search"]').val().trim(),
            page: admin.find('input[name="page"]').val().trim(),
            role: admin.find('select[name="role"]').val().trim(),
            group: admin.find('select[name="group"]').val().trim(),
            last: admin.find('select[name="last"]').val().trim(),
            completed: admin.find('select[name="completed"]').val().trim(),
            paid: admin.find('select[name="hasPaid"]').val().trim()
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


    body.on('keyup', '#results input[name="search"], #results input[name="page"]', function () {
        if(searchTimeout != null)
            clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadResults, 1000);
    });

    body.on('click', '#results .paginate a', function (evt) {
        evt.preventDefault();
        var admin = $('#results'),
            page = this.search.match(/([0-9]*|last|prev|next|first)$/i)[0],
            current = parseInt(admin.find('input[name="page"]').val());
        if(page == 'first')
            page = 1;
        if(page == 'next')
            page = current + 1;
        if(page == 'prev')
            page = current - 1;
        if(page == 'last')
            page = parseInt(admin.find('#page-total').text());
        admin.find('input[name="page"]').val(page);
        loadResults();
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

    body.on('click', '#results table.results > tbody > tr.read-only', function () {
        var row = $(this),
            userId = (/user-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1];
        if(row.is('.selected')) {
            row.removeClass('selected');
        }
        else {
            row.addClass('selected');
            if(row.next().is('.loading') && !row.next().is('.processing')) {
                row.next().addClass('processing');
                $.ajax({
                    url: window.callbackPaths['results_user'],
                    type: 'GET',
                    dataType: 'text',
                    data: {
                        userId: userId
                    },
                    success: function (response) {
                        var cell = row.next().find('> td'),
                            content = $(response);
                        cell.find('*').remove();
                        content.appendTo(cell);
                        row.next().removeClass('loading processing');
                    },
                    error: function () {
                        row.next().removeClass('processing')
                    }
                });
            }
        }
    });

    body.on('click', '#results table.results table tr:nth-child(odd)', function () {
        $(this).toggleClass('selected');
    });

    body.on('change', '#results input[name="search"], #results input[name="page"]', loadResults);

});

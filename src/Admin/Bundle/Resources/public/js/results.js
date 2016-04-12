

$(document).ready(function () {

    var body = $('body'),
        orderBy = 'last DESC',
        searchTimeout = null,
        searchRequest = null;

    var lastSelected = null;
    var selectViable = false;
    document.onselectstart = function () {
        if(key.shift && selectViable) {
            return false;
        }
    };
    body.on('mousedown', '.results [class*="-row"], table.results > tbody > tr', function (evt) {
        // cancel select toggle if target of click is also interactable
        if($(evt.target).is('select, input, a, textarea, button, label.checkbox, label.radio, label.checkbox *, label.radio *, button *, .selectize-control, .selectize-control *')) {
            return;
        }

        selectViable = false;
        var results = $(this).parents('.results');
        var type = (/(.*)-row/i).exec($(this).attr('class'))[1];
        var state = !$(this).find('input[name="selected"]').prop('checked');
        // clear selection unless shift is pressed
        var range = $(this);
        if (!key.shift) {
            results.find('.selected').not($(this)).removeClass('selected').find('> *:last-child input[name="selected"]')
                .prop('checked', false);
        }
        else {
            // check if range is viable
            if(lastSelected != null && lastSelected.is('.' + type + '-row')) {
                if(lastSelected.index() < $(this).index()) {
                    range = $.merge(range, lastSelected.nextUntil($(this)));
                }
                else {
                    range = $.merge(range, $(this).nextUntil(lastSelected));
                }
                selectViable = true;
            }
        }
        if (state) {
            range.addClass('selected').find('> *:last-child input[name="selected"]')
                .prop('checked', state);
            range.trigger('selected');
        }
        else {
            range.removeClass('selected').find('> *:last-child input[name="selected"]')
                .prop('checked', state);
        }

        // if we just did a select, reset the last select so it takes two more clicks to do another range
        if (selectViable) {
            lastSelected = null;
        }
        else {
            lastSelected = $(this);
        }
    });

    body.on('click', '.results.expandable > [class*="-row"]:nth-of-type(odd), .results.expandable > tbody > tr:nth-child(odd)', function () {
        var row = $(this);
        if(row.is('.selected')) {
            row.removeClass('selected');
        }
        else {
            row.addClass('selected');
        }
    });

    function resetHeader() {
        var command = $('.results.collapsible:visible').first();
        if (command.length == 0) {
            return;
        }
        command.each(function () {
            var command = $(this);
            var selected = $('[class*="-row"]:visible.selected').filter(function () {
                return isElementInViewport($(this));
            });

            if (selected.length == 0) {
                if ($(this).is('[class*="-row"]:visible') && isElementInViewport($(this))) {
                    selected = $(this);
                }
                else {
                    selected = command.find('[class*="-row"]:visible').filter(function () {
                        return isElementInViewport($(this));
                    });
                }
            }

            if(selected.length == 0) {
                command.attr('class', command.attr('class').replace(/showing-(.*?)(\s+|$)/i, ''));
                command.addClass('empty');
            }
            else {
                command.removeClass('empty');
                var table = (/(.*)-row/i).exec(selected.attr('class'))[1];
                table = 'showing-' + table;
                if(!command.is('.' + table)) {
                    command.attr('class', command.attr('class').replace(/showing-(.*?)(\s+|$)/i, ''));
                    command.addClass(table);
                }
            }
        });
    }

    body.on('show', '.panel-pane', function () {
        if (!$(this).is('.results-loaded')) {
            $(this).addClass('results-loaded');
            $(this).find('.results header .search .checkbox').draggable();
        }
        resetHeader();
    });

    function getData()
    {
        var admin = $('.results:visible');
        var result = {
            order: orderBy,
            tables: admin.find('.class-names input:checked').map(function () { return $(this).val(); }).toArray(),
            search: (admin.find('input[name="search"]').val() || '').trim()
        };

        admin.find('input[name="page"]').each(function () {
            var table = $(this).parents('.paginate > .paginate').parent().attr('class').replace('paginate', '').trim();
            result['page-' + table] = $(this).val();
        });

        admin.find('header .input input, header .input select').each(function () {
            result[$(this).attr('name')] = $(this).val();
        });

        return result;
    }
    window.getData = getData;

    body.on('click', '.results .class-names .checkbox a', function (evt) {
        evt.preventDefault();
        var command = $('.results:visible');
        var heading = $('[name="' + this.hash.substr(1) + '"]');
        var topPlusPane = DASHBOARD_MARGINS.padding.top + command.find('.pane-top').outerHeight(true) - heading.outerHeight();
        heading.scrollintoview({padding: {
            top: topPlusPane,
            right: 0,
            bottom: $(window).height() - DASHBOARD_MARGINS.padding.top + command.find('.pane-top').height() - heading.outerHeight(),
            left: 0
        }});
        command.find('[class*="-row"].' + this.hash.substr(1) + '-row').first().trigger('mouseover');
    });

    // collapse section feature
    body.on('change', '.results .class-names .checkbox input', function () {
        var command = $('.results:visible');
        var table = $(this).val();
        var heading = command.find('> h2.' + table);
        if($(this).is(':checked')) {
            heading.removeClass('collapsed').addClass('expanded');
        }
        else {
            heading.removeClass('expanded').addClass('collapsed');
        }
        if($(this).is('[disabled]')) {
            heading.hide();
        }
        else {
            heading.show();
        }
        if (command.is('.showing-' + table) && (heading.is('.collapsed') || !heading.is(':visible')) || command.is('.empty')) {
            resetHeader();
        }
    });

    var radioCounter = 5000;

    body.on('click', '.results a[href^="#add-"]:not([href^="#add-new-"])', function (evt) {
        evt.preventDefault();
        var results = $(this).parents('.results');
        var table = $(this).attr('href').substring(5);
        var newRow = results.find('.' + table + '-row.template, .' + table + '-row.template + .expandable.template').clone();
        newRow.each(function () {
            var that = $(this);
            that.attr('class', that.attr('class').replace(new RegExp(table + '-id-[0-9]*(\\s|$)', 'ig'), table + '-id- '));
            that.removeClass('template removed read-only historic').addClass('edit empty');
            that.find('select, textarea, input[type="text"]').val('').trigger('change');
            that.find('> *').each(function () {
                var radio = $(this).find('input[type="radio"]').first();
                if (radio.length > 0) {
                    $(this).find('input[type="radio"]').attr('name', radio.attr('name').split('-')[0] + '-' + radioCounter++).trigger('change');
                }
            });
            that.find('input[type="checkbox"]').prop('checked', false).trigger('change');
        });
        newRow.insertBefore(results.find('.' + table + '-row').first());
    });

    body.on('click', '[class*="-row"] a[href^="#remove-"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('[class*="-row"]');
        if($(this).is('[href^="#remove-confirm-"]')) {
            row.addClass('removed');
        }
        else {
            row.addClass('remove-confirm');
        }
    });

    body.on('click', '[class*="-row"] a[href^="#edit-"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('[class*="-row"]');
        row.removeClass('read-only').addClass('edit');
    });

    body.on('click', '[class*="-row"] a[href="#cancel-edit"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('[class*="-row"]');
        row.removeClass('edit remove-confirm').addClass('read-only');
    });

    function loadContent (data, tables) {
        var admin = $(this).closest('.results'),
            content = $(data).filter('.results');
        if(!tables) {
            tables = $.unique(admin.find('[class*="-row"].template').map(function () {
                return (/(.*)-row/i).exec($(this).attr('class'))[1];
            }).toArray());
        }
        for(var t = 0; t < tables.length; t++) {
            var table = tables[t];
            (function (table) {
                // leave edit rows alone
                admin.find('> .' + table + '-row:not(.edit):not(.template), > .' + table + '-row:not(.edit):not(.template) + .expandable').remove();
                var keepRows = admin.find('> .' + table + '-row').map(function () {
                    var rowId = (new RegExp(table + '-id-([0-9]*)(\\s|$)', 'i')).exec($(this).attr('class'))[1];
                    return '.' + table + '-id-' + rowId;
                }).toArray().join(',');
                content.find('> .' + table + '-row:not(.template), > .' + table + '-row:not(.template) + .expandable').not(keepRows).insertBefore(admin.find('.' + table + '-row.template').first());
                admin.find('.paginate.' + table + ' .page-total').text(content.find('.paginate.' + table + ' .page-total').text());
            })(table);
        }
        resetHeader();
        admin.trigger('resulted');
    }
    // make available to save functions that always lead back to index
    window.loadContent = loadContent;

    function loadResults() {
        if(searchRequest != null)
            searchRequest.abort();
        if(searchTimeout != null)
            clearTimeout(searchTimeout);
        var that = $(this);

        searchTimeout = setTimeout(function () {
            searchRequest = $.ajax({
                url: Routing.generate('command_callback'),
                type: 'GET',
                dataType: 'text',
                data: getData(),
                success: function (data) {
                    loadContent.apply(that, [data]);
                }
            });
        }, 100);
    }
    window.loadResults = loadResults;

    body.on('mouseover click', '.results [class*="-row"]', resetHeader);

    body.on('change', '.paginate input', function () {
        var results = $(this).parents('.results');
        var paginate = $(this).closest('.paginate');
        results.find('.class-names a[href="#' + (/showing-(.*?)(\s|$)/i).exec(results.attr('class'))[1].trim() + '"]').trigger('click');
    });

    body.on('click', '.results a[href^="#search-"]', function (evt) {
        var admin = $(this).parents('.results');
        evt.preventDefault();
        var search = this.hash.substring(8);
        if (search.indexOf(':') > -1) {
            admin.find('header input, header select').each(function () {
                var subSearch = (new RegExp($(this).attr('name') + ':(.*?)(\s|$)', 'i')).exec(search);
                if (subSearch) {
                    search = search.replace(subSearch[0], '');
                    $(this).val(subSearch[1]).trigger('change');
                }
            });
        }
        else {

        }
        $(this).parents('.results').find('.search input[name="search"]').val(search).trigger('change');
    });

    body.on('click change', '.paginate a', function (evt) {
        evt.preventDefault();
        var results = $(this).parents('.results'),
            paginate = $(this).closest('.paginate'),
            page = this.hash.match(/([0-9]*|last|prev|next|first)$/i)[0],
            current = parseInt(paginate.find('input[name="page"]').val()),
            last = parseInt(paginate.find('.page-total').text());
        if(page == 'first')
            page = 1;
        if(page == 'next')
            page = current + 1;
        if(page == 'prev')
            page = current - 1;
        if(page == 'last')
            page = last;
        if(page > last)
            page = last;
        if(page < 1)
            page = 1;
        paginate.find('input[name="page"]').val(page).trigger('change');
    });

    body.on('submit', '.results header form', function (evt) {
        evt.preventDefault();

        loadResults.apply($(this).parents('.results'));
    });

    body.on('change', '.results header .input > select, .results header .input > input', function () {
        var that = $(this);
        var admin = $('.results:visible');
        var paginate = that.closest('.paginate');

        if(that.val() == '_ascending' || that.val() == '_descending')
        {
            orderBy = that.attr('name') + (that.val() == '_ascending' ? ' ASC' : ' DESC');
            that.val(that.data('last') || that.find('option').first().attr('value'));
        }
        else if(that.val().trim() != '') {
            that.parent().removeClass('unfiltered').addClass('filtered');
            that.data('last', that.val());
        }
        else
        {
            that.parent().removeClass('filtered').addClass('unfiltered');
            that.data('last', that.val());
        }

        var disabled = [];
        admin.find('header .filtered').each(function () {
            var header = $(this).parents('header > *');
            admin.find('.class-names .checkbox input').each(function () {
                if (!header.is('.search') && !header.is('.paginate') && !header.is('.' + $(this).val())) {
                    disabled = $.merge($(disabled), $(this));
                }
            });
        });
        admin.find('.class-names .checkbox input').each(function () {
            if(!$(this).is('[disabled]')) {
                $(this).data('last', $(this).prop('checked'));
            }
            if($(this).is(disabled)) {
                $(this).attr('disabled', 'disabled').prop('checked', false);
            }
            else {
                $(this).removeAttr('disabled').prop('checked', $(this).data('last'))
            }
            $(this).trigger('change');
        });

        loadResults.apply(admin);
    });

});

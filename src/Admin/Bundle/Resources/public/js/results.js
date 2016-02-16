

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
    body.on('mousedown', '.results [class*="-row"], table.results > tbody > tr', function () {
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
        var command = $('.results:visible');
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

        command.attr('class', command.attr('class').replace(/showing-(.*?)(\s+|$)/i, ''));
        if(selected.length == 0) {
            command.addClass('empty');
        }
        else {
            command.removeClass('empty');
            var table = (/(.*)-row/i).exec(selected.attr('class'))[1];
            table = 'showing-' + table;
            if(!command.is('.' + table)) {
                command.addClass(table);
            }
        }
    }

    body.on('show', '.panel-pane', function () {
        if (!$(this).is('.results-loaded')) {
            $(this).addClass('results-loaded');
            $(this).find('header .search .checkbox').draggable();
        }
        resetHeader();
    });

    function getData()
    {
        var admin = $('.results:visible');
        var result = {
            order: orderBy,
            tables: admin.find('.class-names input:checked').map(function () { return $(this).val(); }).toArray(),
            search: admin.find('input[name="search"]').val().trim()
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

    var callbackTimeout = null;

    body.on('change', '.results .users input', function () {

    });

    function setupUsers()
    {
        var row = $(this).parents('[class*="-row"]'),
            that = row.find('.users input:not(.selectized)'),
            field = that.parents('label');
        that.selectize({
            persist:false,
            delimiter: ' ',
            searchField: ['text', 'value', '1', '0'],
            plugins: ['continue_editing', 'restore_on_backspace2'],
            maxItems: 1,
            dropdownParent:'body',
            options: [that.val()],
            render: {
                option: function(item) {
                    var desc = '<div class="entity-search">'
                        + '<span class="title">'
                        + '<span class="name"><i class="icon source"></i>' + item.text + '</span>'
                        + '<span class="by">' + (typeof item[0] != 'undefined' ? item[0] : '') + '</span>'
                        + '</span>';
                    if (field.data('users').indexOf(item.value) > -1) {
                        desc += '<a href="#subtract-entity">Remove</a>';
                    }
                    else {
                        desc += '<a href="#insert-entity">Add</a>';
                    }
                    if (field.data('owner') != item.value) {
                        desc += '<a href="#set-owner">Set owner</a>';
                    }
                    return desc + '</div>';
                }
            },
            load: function(query, callback) {
                if (query.length < 1) {
                    callback();
                    return;
                }
                if(callbackTimeout) {
                    clearTimeout(callbackTimeout);
                }
                callbackTimeout = setTimeout(function () {
                    $.ajax({
                        url: window.callbackPaths['command_callback'],
                        type: 'GET',
                        dataType:'text',
                        data: {
                            tables: ['ss_user'],
                            search: query
                        },
                        error: function() {
                            callback();
                        },
                        success: function(content) {
                            var results = $(content).find('.ss_user-row').map(function () {
                                var rowId = (/ss_user-id-([0-9]*)/i).exec($(this).attr('class'))[1];
                                return {
                                    value: rowId,
                                    text: $(this).find('.first input').val() + ' ' + $(this).find('.last input').val(),
                                    0: $(this).find('.email input').val()
                                }}).toArray();
                            callback(results);
                        }
                    });
                }, 100);
            }
        });
    }

    function setupFields() {
        setupUsers.apply(this);
    }

    var radioCounter = 5000;

    body.on('click', '.results a[href^="#add-"]', function (evt) {
        evt.preventDefault();
        var results = $(this).parents('.results');
        var table = $(this).attr('href').substring(5);
        var newRow = results.find('.' + table + '-row').first().clone().insertBefore(results.find('.' + table + '-row').first());
        newRow.attr('class', newRow.attr('class').replace(new RegExp(table + '-id-[0-9]*(\\s|$)', 'ig'), table + '-id- '));
        newRow.removeClass('template removed read-only historic').addClass('edit');
        newRow.find('select, textarea, input[type="text"]').val('').trigger('change');
        newRow.find('> *').each(function () {
            var radio = $(this).find('input[type="radio"]').first();
            if (radio.length > 0) {
                $(this).find('input[type="radio"]').attr('name', radio.attr('name').split('-')[0] + '-' + radioCounter++).trigger('change');
            }
        });
        newRow.find('input[type="checkbox"]').prop('checked', false).trigger('change');
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
        setupFields.apply(this);
    });

    body.on('click', '[class*="-row"] a[href="#cancel-edit"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('[class*="-row"]');
        row.removeClass('edit remove-confirm').addClass('read-only');
    });

    function loadContent (data) {
        var admin = jQuery('.results:visible'),
            content = $(data).filter('.results');
        admin.find('.class-names .checkbox input:checked').each(function () {
            var table = $(this).val();
            // leave edit rows alone
            admin.find('> .' + table + '-row:not(.edit)').remove();
            var keepRows = admin.find('> .' + table + '-row').map(function () {
                var rowId = (new RegExp(table + '-id-([0-9]*)(\\s|$)', 'i')).exec($(this).attr('class'))[1];
                return '.' + table + '-id-' + rowId;
            }).toArray().join(',');
            content.find('> .' + table + '-row').not(keepRows).insertAfter(admin.find('> h2.' + table));
            admin.find('.paginate.' + table + ' .page-total').text(content.find('.paginate.' + table + ' .page-total').text());
        });
        resetHeader();
        admin.trigger('resulted');
    }
    // make available to save functions that always lead back to index
    window.loadContent = loadContent;

    body.on('change', '.results input[name="group"]', function () {
        if ($(this).prop('checked')) {
            $(this).parents('[class*="-row"] > *').find('input[name="group"]').not(this).prop('checked', false);
        }
    });

    function loadResults() {
        if(searchRequest != null)
            searchRequest.abort();
        if(searchTimeout != null)
            clearTimeout(searchTimeout);

        searchTimeout = setTimeout(function () {
            searchRequest = $.ajax({
                url: window.callbackPaths['command_callback'],
                type: 'GET',
                dataType: 'text',
                data: getData(),
                success: loadContent
            });
        }, 100);
    }

    body.on('mouseover click', '.results [class*="-row"]', resetHeader);

    // hide any visible modals when panel changes
    body.on('hide', '.panel-pane', function () {
        body.find('.modal:visible').modal('hide');
        body.find('.ui-datepicker').hide();
    });

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

        loadResults();
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

        loadResults();
    });

});



Selectize.define('restore_on_backspace2', function(options) {
    var self = this;

    options.text = options.text || function(option) {
        return option[this.settings.labelField];
    };

    this.onKeyDown = (function() {
        var original = self.onKeyDown;
        return function(e) {
            var index, option;
            index = this.caretPos - 1;

            if (e.keyCode === 8 && this.$control_input.val() === '' && !this.$activeItems.length) {
                if (index >= 0 && index < this.items.length) {
                    option = this.options[this.items[index]];
                    // prevent from deleting google
                    if (this.deleteSelection(e)) {
                        this.setTextboxValue(option[this.settings.valueField]);
                        this.refreshOptions(true);
                    }
                    e.preventDefault();
                    return;
                }
            }
            return original.apply(this, arguments);
        };
    })();
});

Selectize.define('continue_editing', function(options) {
    var self = this;

    options.text = options.text || function(option) {
        return option[this.settings.labelField];
    };

    this.onFocus = (function() {
        var original = self.onFocus;

        return function(e) {
            original.apply(this, arguments);

            var index = this.caretPos - 1;
            if (index >= 0 && index < this.items.length) {
                var option = this.options[this.items[index]];
                var currentValue = options.text.apply(this, [option]);
                if (this.deleteSelection({keyCode: 8})) {
                    // only remove item if it is made up and not from the server
                    if(typeof option.alt == 'undefined')
                        this.removeItem(currentValue);
                    this.setTextboxValue(option[this.settings.valueField]);
                    this.refreshOptions(true);
                }
            }
        };
    })();

    this.onBlur = (function() {
        var original = self.onBlur;

        return function(e) {
            var v = this.$control_input.val();
            original.apply(this, arguments);
            if(v.trim() != '') {
                var option = this.options[v] || { value: v, text: v };
                this.addOption(option);
                this.setValue(option.value);
            }
        };
    })();
});

var callbackTimeout;

function setupSelectize()
{
    var that = $(this),
        row = that.parents('tr');
    that.selectize({
        persist:false,
        delimiter: ' ',
        searchField: ['text', 'value', '1', '0'],
        plugins: ['continue_editing', 'restore_on_backspace2'],
        maxItems: 1,
        options: [that.val()],
        render: {
            option: function(item) {
                return '<div>' +
                '<span class="title">' +
                '<span class="name"><i class="icon source"></i>' + item.text + '</span>' +
                '<span class="by">' + (typeof item[0] != 'undefined' ? item[0] : '') + '</span>' +
                '</span>' +
                '<span class="description">' + (typeof item[1] != 'undefined' ? item[1] : '') + '</span>' +
                '</div>';
            }
        },
        load: function(query, callback) {
            if(that.parents('th').length > 0) callback(window.entities);
            if (query.length < 1) callback();
            if(callbackTimeout)
                clearTimeout(callbackTimeout);
            callbackTimeout = setTimeout(function () {
                $.ajax({
                    url: window.callbackPaths['emails_search'],
                    dataType:'json',
                    data: {
                        alt: row.find('.selectized').not(that).map(function () {return $(this).attr('name');}).toArray().join(','),
                        field: that.attr('name'),
                        q: query
                    },
                    error: function() {
                        callback();
                    },
                    success: function(res) {
                        callback(res.slice(0, 100));
                    }
                });
            }, 500);
        }
    });
}

$(document).ready(function () {

    var body = $('body');
    var orderBy = 'created DESC',
        searchTimeout = null,
        searchRequest = null;

    function getData() {
        var admin = jQuery('#emails');
        return {
            order: orderBy,
            search: admin.find('input[name="search"]').val().trim(),
            page: admin.find('input[name="page"]').val().trim()
        };
    }

    function loadContent (data) {
        var admin = jQuery('#emails'),
            content = $(data);
        admin.find('.history > tbody > tr').remove();
        content.find('.history > tbody > tr').appendTo(admin.find('.history > tbody'));
        admin.find('.history > thead > tr > th').each(function (i) {
            $(this).find('label:first-child > *:not(select):not(input)').remove();
            content.find('.pane-content th').eq(i).find('label:first-child > *:not(select):not(input)').prependTo($(this).find('label:first-child'));
        });
        admin.find('#page-total').text(content.find('#page-total').text());
        // update template list and filter out any zeros
        admin.find('#history select[name="template"]').replaceWith(content.find('#history select[name="template"]'));
        if(admin.find('input[name="search"]').val().trim() != '') {
            admin.find('#history select[name="template"] optgroup option').each(function () {
                if (!$(this).text().match(/\([0-9]*\)/i) || $(this).text().indexOf('(0)') > -1) {
                    $(this).remove();
                }
            });
            admin.find('#history select[name="template"] optgroup:not(:has(*))').remove();
        }
    }

    function loadResults() {
        if(searchRequest != null)
            searchRequest.abort();
        if(searchTimeout != null)
            clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function () {
            searchRequest = $.ajax({
                url: window.callbackPaths['emails_callback'],
                type: 'GET',
                dataType: 'text',
                data: getData(),
                success: loadContent
            });
        }, 100);
    }

    body.on('keyup', '#emails input[name="search"], #emails input[name="page"]', function () {
        if(searchTimeout != null)
            clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadResults, 1000);
    });

    key('⌘+v, ctrl+v, command+v', function()
    {
        var email = $('#send-email');
        if (email.is(':visible')) {

            // get the clipboard text
            var that = $(this),
                text = $('<textarea></textarea>')
                    .css('opacity', '0')
                    .css('height', 1)
                    .css('width', 1).appendTo(email).focus();
            setTimeout(function () {
                var clipText = text.val(), i;
                text.remove();
                that.focus();

                // split into rows
                var clipRows = clipText.split(/\n/ig);

                // split rows into columns
                for (i=0; i<clipRows.length; i++) {
                    clipRows[i] = clipRows[i].split(/\t|\s\s\s\s+/ig);
                }

                // write out in a table
                for (i=0; i<clipRows.length; i++) {
                    if(clipRows[i].length == 0 || clipRows[i][0].length == 0 || clipRows[i].indexOf('email') > -1 ||
                        clipRows[i].indexOf('e-mail') > -1 || clipRows[i].indexOf('E-mail') > -1)
                        continue;
                    email.find('a[href="#add-line"]').trigger('click');
                    var newRow = email.find('.variables > tbody > tr').last();
                    for (var j=0; j<clipRows[i].length; j++) {
                        if (clipRows[i][j].length == 0) {
                            newRow.find('> td:eq(' + j + ') input').val('');
                        }
                        else {
                            newRow.find('> td:eq(' + j + ') input').val(clipRows[i][j]);
                        }
                    }
                }
            }, 100);
        }
    });

    body.on('change', '#send-email .variables > tbody > tr > td > label > input, #send-email .variables > thead > tr > th > label > input', function () {
        if($(this).parents('th').length > 0) {
            var i = $(this).parents('th').index();
            for(var k in window.entities) {
                if(window.entities.hasOwnProperty(k) && window.entities[k].value == this.selectize.getValue()) {
                    var cell = $(this).parents('.variables').find('> tbody > tr > td:eq(' + i + ')');
                    cell.find('input').attr('placeholder', window.entities[k]['0'].substr(1));
                    cell.find('> label > input').attr('name', window.entities[k].value);
                    break;
                }
            }
        }
        else {
            if(typeof this.selectize.options[this.selectize.getValue()] != 'undefined' &&
                typeof this.selectize.options[this.selectize.getValue()].alt != 'undefined') {
                var option = this.selectize.options[this.selectize.getValue()];
                for(var j in option.alt) {
                    if(option.alt.hasOwnProperty(j)) {
                        var that = $(this).parents('tr').find('input[name="' + j + '"]');
                        that.val(option.alt[j]);
                        that[0].selectize.addOption({text: option.alt[j], value: option.alt[j]});
                        that[0].selectize.setValue(option.alt[j])
                    }
                }
            }
        }
    });

    body.on('click', '#emails .paginate a', function (evt) {
        evt.preventDefault();
        var admin = $('#emails'),
            page = this.hash.match(/([0-9]*|last|prev|next|first)$/i)[0],
            current = parseInt(admin.find('input[name="page"]').val()),
            last = parseInt(admin.find('#page-total').text());
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
        admin.find('input[name="page"]').val(page);
        loadResults();
    });

    body.on('click', '#emails a[href="#send-email"]', function () {
        var emails = $('#emails'),
            template = $(this).parents('tr').find('td:nth-child(1)').text();
        if(template != '') {
            emails.find('.nav li a[href="#send-email"]')
                .parents('ul').find('li')
                .removeClass('active').last()
                .addClass('active');
            if(template != emails.find('select[name="template"]').val()) {
                emails.find('select[name="template"]').val(template).trigger('change');
            }
        }
    });

    body.on('click', '#send-email a[href="#remove-line"]', function (evt) {
        evt.preventDefault();
        if($(this).parents('tr').siblings().length > 0)
            $(this).parents('tr').remove();
        else
            $(this).parents('tr').find('input').val('');
    });

    body.on('click', '#send-email a[href="#remove-field"]', function (evt) {
        evt.preventDefault();
        var index = $(this).parents('th').index();
        $(this).parents('.variables').find('thead > tr > th:eq(' + index + '), tbody > tr > td:eq(' + index + ')').remove();
    });

    body.on('click', '#send-email a[href="#add-field"]', function (evt) {
        evt.preventDefault();
        var email = $('#send-email');
        email.find('.variables > thead > tr > th:first-child, .variables > tbody > tr > td:first-child').each(function () {
            var newCell = $(this).clone().insertBefore($(this).parents('tr').find('td:last-child, th:last-child'));
            newCell.find('.selectized').removeClass('selectized');
            newCell.find('.selectize-control').remove();
            if(newCell.is('th')) {
                newCell.html('<label class="input"><input type="text" /></label><a href="#remove-field"></a>');
            }
            newCell.find('input').each(function () {
                $(this).val('');
                setupSelectize.apply(this);
            });
        });
    });

    body.on('change', '#send-email select[name="template"]', function () {
        var email = $('#send-email');
        if($(this).val() != '')
            email.find('.preview').replaceWith($('<iframe class="preview" src="' + window.callbackPaths['emails_template'] + '/' + $(this).val() + '" height="400" width="100%" frameborder="0"></iframe>'));
    });

    body.on('click', '#send-email a[href="#add-line"]', function (evt) {
        evt.preventDefault();
        var email = $('#send-email'),
            newRow = email.find('.variables > tbody > tr').first().clone().appendTo(email.find('.variables > tbody'));
        newRow.find('.selectized').removeClass('selectized');
        newRow.find('.selectize-control').remove();
        newRow.find('input').each(function () {
            $(this).val('');
            setupSelectize.apply(this);
        });

    });

    body.on('click', '#send-email a[href="#markdown"], #send-email a[href="#editor1"]', function (evt) {
        evt.preventDefault();
    });

    body.on('show', '#emails', function () {
        if($(this).is('.loaded'))
            return;
        $(this).addClass('loaded');
        var cur = -1, prv = -1;
        var hideTimeout = null;
        var emails = $('#emails');
        emails.find('.history th:nth-child(1) .input + div')
            .datepicker({
                //numberOfMonths: 3,
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,

                beforeShowDay: function ( date ) {
                    return [true, ( (date.getTime() >= Math.min(prv, cur) && date.getTime() <= Math.max(prv, cur)) ? 'date-range-selected' : '')];
                },

                onSelect: function ( dateText, inst ) {
                    prv = cur;
                    cur = (new Date(inst.selectedYear, inst.selectedMonth, inst.selectedDay)).getTime();
                    if ( prv == -1 || prv == cur ) {
                        prv = cur;
                        $(this).prev().find('input').val( dateText );
                    } else {
                        var d1 = $.datepicker.formatDate( 'mm/dd/y', new Date(Math.min(prv,cur)), {} ),
                            d2 = $.datepicker.formatDate( 'mm/dd/y', new Date(Math.max(prv,cur)), {} );
                        $(this).prev().find('input').val( d1+' - '+d2 );
                    }
                },

                onChangeMonthYear: function ( year, month, inst ) {
                    //prv = cur = -1;
                },

                onAfterUpdate: function () {
                    var that = $(this);
                    $('<a href="#everything">All</a>')
                        .insertBefore($(this).find('.ui-datepicker-header'))
                        .on('click', function (evt) {
                            evt.preventDefault();
                            cur = -1;
                            prv = -1;
                            that.prev().find('input').val('').trigger('change');
                            that.hide();
                        });
                    $('<a href="#asc">Ascending (Then-Now)</a>')
                        .insertBefore($(this).find('.ui-datepicker-header'))
                        .on('click', function (evt) {
                            evt.preventDefault();
                            orderBy = that.prev().find('input').attr('name') + ' ASC';
                            loadResults();
                            that.hide();
                        });
                    $('<a href="#desc">Descending (Now-Then)</a>')
                        .insertBefore($(this).find('.ui-datepicker-header'))
                        .on('click', function (evt) {
                            evt.preventDefault();
                            orderBy = that.prev().find('input').attr('name') + ' DESC';
                            loadResults();
                            that.hide();
                        });
                    $('<a href="#yesterday">Since Yesterday</a>')
                        .insertBefore($(this).find('.ui-datepicker-header'))
                        .on('click', function (evt) {
                            evt.preventDefault();
                            var yesterday = new Date();
                            yesterday.setHours(0, 0, 0, 0);
                            yesterday.setTime(yesterday.getTime() - 86400000);
                            prv = yesterday.getTime();
                            cur = (new Date()).getTime();
                            var d1 = $.datepicker.formatDate( 'mm/dd/y', new Date(Math.min(prv,cur)), {}),
                                d2 = $.datepicker.formatDate( 'mm/dd/y', new Date(Math.max(prv,cur)), {} );
                            that.prev().find('input').val( d1+' - '+d2 ).trigger('change');
                            that.hide();
                        });
                    $('<a href="#week">This Week</a>')
                        .insertBefore($(this).find('.ui-datepicker-header'))
                        .on('click', function (evt) {
                            evt.preventDefault();
                            var lastSunday = new Date();
                            lastSunday.setHours(0, 0, 0, 0);
                            lastSunday.setDate(lastSunday.getDate() - lastSunday.getDay());
                            prv = lastSunday.getTime();
                            cur = (new Date()).getTime();
                            var d1 = $.datepicker.formatDate( 'mm/dd/y', new Date(Math.min(prv,cur)), {}),
                                d2 = $.datepicker.formatDate( 'mm/dd/y', new Date(Math.max(prv,cur)), {} );
                            that.prev().find('input').val( d1+' - '+d2 ).trigger('change');
                            that.hide();
                        });
                    $('<a href="#month">This Month</a>')
                        .insertBefore($(this).find('.ui-datepicker-header'))
                        .on('click', function (evt) {
                            evt.preventDefault();
                            var theFirst = new Date();
                            theFirst.setHours(0, 0, 0, 0);
                            theFirst.setDate(1);
                            prv = theFirst.getTime();
                            cur = (new Date()).getTime();
                            var d1 = $.datepicker.formatDate( 'mm/dd/y', new Date(Math.min(prv,cur)), {}),
                                d2 = $.datepicker.formatDate( 'mm/dd/y', new Date(Math.max(prv,cur)), {} );
                            that.prev().find('input').val( d1+' - '+d2 ).trigger('change');
                            that.hide();
                        });
                    $('<a href="#year">This Year</a>')
                        .insertBefore($(this).find('.ui-datepicker-header'))
                        .on('click', function (evt) {
                            evt.preventDefault();
                            var foty = new Date();
                            foty.setHours(0, 0, 0, 0);
                            foty.setMonth(0, 1);
                            prv = foty.getTime();
                            cur = (new Date()).getTime();
                            var d1 = $.datepicker.formatDate( 'mm/dd/y', new Date(Math.min(prv,cur)), {}),
                                d2 = $.datepicker.formatDate( 'mm/dd/y', new Date(Math.max(prv,cur)), {} );
                            that.prev().find('input').val( d1+' - '+d2 ).trigger('change');
                            that.hide();
                        });

                    $('<button type="button" class="ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all" data-handler="hide" data-event="click">Done</button>')
                        .appendTo($(this).find('.ui-datepicker-buttonpane'))
                        .on('click', function () {
                            that.prev().find('input').trigger('change');
                            that.hide();
                        });
                }
            })
            .each(function (i) {
                $(this).position({
                    my: 'left top',
                    at: 'left bottom',
                    of: emails.find('th:nth-child(1) input').eq(i)
                });
            })
            .on('mousedown', function () {
                setTimeout(function () {
                    if(hideTimeout)
                        clearTimeout(hideTimeout);
                }, 100);
            })
            .on('blur', function () {
                $(this).hide();
            })
            .hide();

        body.on('mousedown', '#emails *', function (evt) {
            if(this == evt.target &&
                $(evt.target).parents('.ui-datepicker').length == 0 &&
                $(evt.target).parents('th:nth-child(1)').length == 0) {
                hideTimeout = setTimeout(function () { emails.find('.ui-datepicker').parent().hide(); }, 500);
            }
        });

        body.on('blur', '#emails th:nth-child(1) input', function () {
            var that = $(this).parent().next();
            hideTimeout = setTimeout(function () { that.hide(); }, 500);
        });

        body.on('focus', '#emails th:nth-child(1) input', function () {
            setTimeout(function () {
                if(hideTimeout)
                    clearTimeout(hideTimeout);
            }, 100);
            var v = this.value,
                d;

            try {
                if ( v.indexOf(' - ') > -1 ) {
                    d = v.split(' - ');

                    prv = $.datepicker.parseDate( 'mm/dd/y', d[0] ).getTime();
                    cur = $.datepicker.parseDate( 'mm/dd/y', d[1] ).getTime();

                } else if ( v.length > 0 ) {
                    prv = cur = $.datepicker.parseDate( 'mm/dd/y', v ).getTime();
                }
            } catch ( e ) {
                cur = prv = -1;
            }

            if ( cur > -1 )
                $(this).parent().next().datepicker('setDate', new Date(cur));

            $(this).parent().next().datepicker('refresh').show();
        });
    });


    body.on('shown.bs.modal', '#send-confirm', function () {
        var count = 0;
        $('#send-email').find('.variables > tbody > tr').each(function () {
            if($(this).find('td:nth-child(1) > label > input').val().trim().length > 0)
                count++;
        });
        $('#send-confirm').find('.count').text(count);
    });

    body.on('click', '#send-confirm a[href="#confirm-send"]', function () {
        var email = $('#send-email');
        $.ajax({
            url: window.callbackPaths['emails_send'] + '/' + email.find('select[name="template"]').val(),
            dataType: 'text',
            type: 'POST',
            data: {
                subject: email.find('input[name="subject"]').val().trim(),
                template: email.find('iframe.preview')[0].contentWindow.CKEDITOR.instances.editor1.getData(),
                variables: email.find('.variables > tbody > tr').map(function () {
                    var line = {};
                    $(this).find('> td > label > input').each(function () { line[$(this).attr('name')] = $(this).val().trim(); });
                    return line;
                }).toArray(),
                confirm: true
            },
            success: function (response) {
                // clear list

            }
        });
    });

    body.on('click', '#send-email a[href="#save-template"]', function (evt) {
        evt.preventDefault();
        var email = $('#send-email');
        $.ajax({
            url: window.callbackPaths['emails_save'],
            dataType: 'text',
            type: 'POST',
            data: {
                template: email.find('iframe.preview')[0].contentWindow.CKEDITOR.instances.editor1.getData(),
                name: email.find('input[name="template-name"]').val().trim()
            },
            success: function (response) {
                // clear list

            }
        });
    });

    body.find('#send-email .variables input').each(setupSelectize);

    body.on('change', '#emails table.history > thead > tr > th:not(:last-child) > label > select, #emails table.history > thead > tr > th:not(:last-child) > label > input', function () {
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
$(document).ready(function () {

    var body = $('body'),
        orderBy = 'lastVisit DESC',
        searchTimeout = null,
        searchRequest = null;

    body.on('click', '#command a[href^="/emails"]', function () {
        var that = $(this);
        body.one('show', '#emails', function () {
            $('#emails').find('input[name="search"]').val(that[0].hash).trigger('keyup');
        });
    });

    key('⌘+c, ctrl+c, command+c', function()
    {
        var command = $('#command');
        if (command.is(':visible')) {

            // get the clipboard text
            var text = $('<textarea></textarea>')
                    .css('position', 'fixed')
                    .css('top', 0)
                    .css('left', -10000)
                    .css('opacity', '0')
                    .css('height', 1)
                    .css('width', 1).appendTo(command).focus();
            // generate rows in current view
            var currentView =
                command.find('#users > form header > *:visible:not(:last-child)').map(function (i) {
                    if(i == 0) {
                        return 'Visited';
                    }
                    if(i == 4) {
                        return 'Sign up';
                    }
                    if(i == 3) {
                        return "First name\tLast name\tEmail";
                    }
                    return $(this).find('> label > select option:first-child').text();
                }).toArray().join("\t") + "\r\n" +
                command.find('#users .user-row').map(function () {
                return $(this).find('> *:visible:not(:last-child)').map(function (i) {
                    if(i == 1 || i == 2) {
                        return $(this).find(':checked ~ span').map(function () {
                            return $(this).text();
                        }).toArray().join(', ');
                    }
                    if(i == 3) {
                        return $(this).find('input').map(function () {return $(this).val();}).toArray().join("\t");
                    }
                    return $(this).text();
                }).toArray().join("\t");
            }).toArray().join("\r\n");
            text.val(currentView);
            text.selectRange(0, currentView.length);

            setTimeout(function () {
                text.remove();
            }, 100);
        }
    });

    body.on('change focus blur mousedown mouseup keydown keyup', '#command .group-row input, #command .group-row select', function () {
        var tab = $('#command');
        var row = $(this).parents('.group-row');
        if(row.find('input[name="groupName"]').val().trim() == '' && row.find('textarea[name="description"]').val().trim() == '' && row.find('input[name="roles"]:checked').length == 0) {
            row.removeClass('invalid').addClass('valid empty');
        }
        else if((row.find('textarea[name="description"]').val().trim() != '' || row.find('input[name="roles"]:checked').length != 0) && row.find('input[name="groupName"]').val().trim() == '') {
            row.removeClass('valid empty').addClass('invalid');
        }
        else {
            row.removeClass('invalid empty').addClass('valid');
        }
        if(tab.find('.group-row.invalid, .group-row.empty').length > 0) {
            tab.find('#groups .pane-top .highlighted-link').removeClass('valid').addClass('invalid');
        }
        else {
            tab.find('#groups .pane-top .highlighted-link').removeClass('invalid').addClass('valid');
        }
    });

    body.on('click', '#command a[href*="_switch_user"]', function () {
        if(searchRequest != null)
            searchRequest.abort();
    });

    body.on('click', '#command a[href="#new-user"]', function (evt) {
        evt.preventDefault();
        var that = $(this);
        var admin = $('#command'),
            dialog = $('#users').find('.pane-top'),
            data = {
                first: dialog.find('.first-name input').val().trim(),
                last: dialog.find('.last-name input').val().trim(),
                email: dialog.find('.email input').val().trim(),
                pass: dialog.find('.password input').val()
            };
        if(dialog.find('.highlighted-link').is('.invalid'))
            return;
        loadingAnimation(that);
        dialog.find('.highlighted-link').removeClass('valid').addClass('invalid');
        $.ajax({
            url: Routing.generate('add_user'),
            type: 'POST',
            dataType: 'text',
            data: data,
            success: function (response) {
                that.find('.squiggle').stop().remove();
                // reset dialog fields for next entry
                dialog.find('.first-name input').val('');
                dialog.find('.last-name input').val('');
                dialog.find('.email input').val('');
                dialog.find('.password input').val('');

                // show that filtering is showing users with this last name
                admin.find('th').each(function (i) {
                    if(i == 3) {
                        $(this).find('select').val(data.last.substr(0, 1).toUpperCase() + '%');
                        $(this).removeClass('unfiltered').addClass('filtered');
                    }
                    else {
                        $(this).removeClass('filtered').addClass('unfiltered');
                    }
                });

                loadContent.apply(admin.find('.results'), [response]);
            },
            error: function () {
                that.find('.squiggle').stop().remove();
            }
        });
    });

    body.on('click', '.results [value="#save-user"]', function (evt) {
        evt.preventDefault();
        var data = {};
        var row = $(this).parents('.ss_user-row');
        data['users'] = [];
        row.removeClass('edit').addClass('read-only');
        data['users'][data['users'].length] = {
            groups : row.find('input[name="groups"]:checked').map(function () {return $(this).val();}).toArray().join(','),
            roles : row.find('input[name="roles"]:checked').map(function () {return $(this).val();}).toArray().join(','),
            firstName : row.find('input[name="first"]').val().trim(),
            lastName : row.find('input[name="last"]').val().trim(),
            email : row.find('input[name="email"]').val().trim(),
            userId : (/user-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1]
        };
        $.ajax({
            url: Routing.generate('save_user'),
            type: 'POST',
            dataType: 'text',
            data: data,
            success: loadContent
        });
    });

    body.on('show', '#command', function () {
        if($(this).is('.loaded'))
            return;
        $(this).addClass('loaded');
        var admin = $('#command'),
            pickers = admin.find('th:nth-child(1) .input + div, th:nth-child(6) .input + div');

        var cur = -1, prv = -1;
        var hideTimeout = null;
        pickers
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
                    of: admin.find('th:nth-child(1) input, th:nth-child(6) input').eq(i)
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

        $(window).resize(function () {
            pickers.each(function (i) {
                $(this).position({
                    my: 'left top',
                    at: 'left bottom',
                    of: admin.find('th:nth-child(1) input, th:nth-child(6) input').eq(i)
                });
            });
        });

        body.on('mousedown', '#command *', function (evt) {
            if(this == evt.target &&
                $(evt.target).parents('.ui-datepicker').length == 0 &&
                $(evt.target).parents('th:nth-child(1), th:nth-child(6)').length == 0) {
                hideTimeout = setTimeout(function () { admin.find('.ui-datepicker').parent().hide(); }, 500);
            }
        });

        body.on('blur', '#command th:nth-child(1) input, #command th:nth-child(6) input', function () {
            var that = $(this).parent().next();
            hideTimeout = setTimeout(function () { that.hide(); }, 500);
        });

        body.on('focus', '#command th:nth-child(1) input, #command th:nth-child(6) input', function () {
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

    body.on('click', '#command a[href="#confirm-cancel-user"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('.user-row'),
            userId = (/user-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1];
        $('#cancel-user-name').text(row.find('input[name="first-name"]').first().val());
        $('#confirm-cancel-user').data('userId', userId);
    });

    body.on('click', '#command a[href="#confirm-password-reset"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('.user-row'),
            userId = (/user-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1];
        $('#reset-user-name').text(row.find('input[name="first-name"]').first().val());
        $('#confirm-password-reset').data('userId', userId);
    });

    body.on('click', '#confirm-cancel-user a[href="#cancel-user"]', function () {
        var data = getDataRequest.apply($(this).parents('.results'));
        data.userId = $('#confirm-cancel-user').data('userId');
        $.ajax({
            url: Routing.generate('cancel_user'),
            type: 'POST',
            dataType: 'text',
            data: data,
            success: loadContent
        });
    });

    body.on('click', '#confirm-password-reset a[href="#reset-password"]', function () {
        var data = getDataRequest.apply($(this).parents('.results'));
        data.userId = $('#confirm-password-reset').data('userId');
        $.ajax({
            url: Routing.generate('reset_user'),
            type: 'POST',
            dataType: 'text',
            data: data,
            success: loadContent
        });
    });

});
$(document).ready(function () {

    var body = $('body'),
        processing = false,
        timeLine, timelineInterval;

    body.on('click', 'a[href^="#search-"]', function (evt) {
        evt.preventDefault();
        $('#activity').find('.search input').val($(this).attr('href').substring(8));
        timeLine.itemsData.clear();
    });

    body.on('show', '#activity', function () {
        timelineInterval = setInterval(updateTimeline, 500);
        if($(this).is('.loaded'))
            return;
        $(this).addClass('loaded');
        for(var i = 0; i < window.initialDates.length; i++)
        {
            window.initialDates[i].start = new Date(window.initialDates[i].start);
        }
        timeLine = new vis.Timeline($('#visit-timeline')[0], window.initialDates, {
            width: '100%',
            animateZoom: false,
            animate: false,
            showCurrentTime: true,
            showCustomTime: true,
            orientation: 'top',
            showMajorLabels: true,
            showNavigation: true,
            padding: 1,
            margin: {item: 2},
            zoomMax: 18 * 60 * 60 * 1000
        });

        timeLine.setWindow(new Date((new Date).getTime() - 60 * 60 * 5 * 1000), new Date((new Date).getTime() + 60 * 60 * 1000));

        var admin = $('#activity'),
            pickers = admin.find('.range .input + div');

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
                    of: admin.find('input[name="range"]')
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
            pickers.each(function () {
                $(this).position({
                    my: 'left top',
                    at: 'left bottom',
                    of: admin.find('input[name="range"]')
                });
            });
        });

        body.on('mousedown', '#activity *', function (evt) {
            if(this == evt.target &&
                $(evt.target).parents('.range').length == 0) {
                pickers.parent().find('input').blur();
                hideTimeout = setTimeout(function () { admin.find('.ui-datepicker').parent().hide(); }, 500);
            }
        });

        body.on('blur', '.range input', function () {
            var that = $(this).parent().next();
            hideTimeout = setTimeout(function () { that.hide(); }, 500);
        });

        body.on('focus', '.range input', function () {
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

    function updateTimeline() {
        if(processing || timeLine == null) {
            return;
        }
        var start = Math.floor(timeLine.range.start / 1000),
            end = Math.floor(timeLine.range.end / 1000);
        processing = true;
        $.ajax({
            url: Routing.generate('activity'),
            type: 'POST',
            dataType: 'json',
            data: {
                search: $('#activity').find('input[name="search"]').val().trim(),
                start: start,
                end: end,
                not: $('div[data-id]').map(function () {return $(this).attr('data-id');}).toArray().join(',')
            },
            success: function (data) {
                //var ids = timeLine.itemsData._data.map(function (x) {return x.className;});
                for(var i = 0; i < data.length; i++)
                {
                    //if(ids.indexOf(data[i].className) == -1)
                    //{
                    try {
                        data[i].start = new Date(data[i].start);
                        timeLine.itemsData.add(data[i]);
                    }
                    catch (e){
                        // ignore already added errors
                    }
                    //}
                }
                processing = false;
            },
            error: function () {
                processing = false;
            }
        })
    }

    body.on('hide', '#activity', function () {
        clearTimeout(timelineInterval);
    });

    body.on('mouseover', '.vis.timeline .item.box', function () {
        $(this).addClass('related-hover');
        var session = (/session-id-([a-z0-9]*)(\s|$)/ig).exec($(this).attr('class'))[0].trim();
        if(session.length > 'session-id-'.length)
            $('.' + session).addClass('related-session');
        var user = (/user-id-([a-z0-9]*)(\s|$)/ig).exec($(this).attr('class'))[0].trim();
        if(user.length > 'user-id-'.length)
            $('.' + user).addClass('related-user');
    });

    body.on('mouseout', '.vis.timeline .item.box', function () {
        $('.related-session, .related-user, .related-hover').removeClass('related-session related-user related-hover');
    });

    body.on('keyup', '#activity input[name="search"]', function () {
        timeLine.itemsData.clear();
    });

    body.on('change', '#activity input[name="search"]', function () {
        timeLine.itemsData.clear();
    });

    body.on('change', '#activity input[name="range"]', function () {
        var v = this.value,
            d, cur, prv;

        try {
            if ( v.indexOf(' - ') > -1 ) {
                d = v.split(' - ');

                prv = $.datepicker.parseDate( 'mm/dd/y', d[0] );
                prv.setHours(0,0,0,0);
                prv = prv.getTime();
                cur = $.datepicker.parseDate( 'mm/dd/y', d[1] );
                cur.setHours(0,0,0,0);
                cur = cur.getTime();

            } else if ( v.length > 0 ) {
                prv = $.datepicker.parseDate( 'mm/dd/y', v );
                prv.setHours(0,0,0,0);
                prv = cur = prv.getTime();
            }
        } catch ( e ) {
            cur = prv = -1;
        }
        var d1 = new Date(Math.min(prv,cur) -  60 * 60 * 1000),
            d2 = new Date(Math.max(prv,cur) + 60 * 60 * 24 * 1000);
        timeLine.setWindow(d1, d2, {animate:false});
    });

});


$(document).ready(function () {

    // TODO: remove old unused tabs
    var body = $('body');

    body.on('click', 'a[href*="/plan/download"]', function () {
        body.removeClass('download-plan');
    });

    function activateMenu(path, noPush) {
        var that = $(this);
        var i = window.callbackUri.indexOf(path),
            panel = $('#' + window.callbackKeys[i] + '.panel-pane'),
            panelIds = body.find('.panel-pane').map(function () {return $(this).attr('id');}).toArray(),
            item = body.find('.main-menu a[href^="' + window.callbackUri[window.callbackKeys.indexOf(window.callbackKeys[i].replace(/-step[0-9]+/g, ''))] + '"]').first();

        // activate the menu
        body.find('.main-menu .active').removeClass('active');

        // do not push when menu is activated from back or forward buttons
        if(!noPush) {
            // create a mock link to get the browser to parse pathname, query, and hash
            var a = document.createElement('a');
            a.href = path;
            visits[visits.length] = {path: a.pathname, query: a.search, hash: a.hash, time: (new Date()).toJSON()};
            window.history.pushState(window.callbackKeys[i], "", path);
        }
        // expand menu groups
        if(item.length > 0) {
            if(item.parents('ul.collapse').length != 0 &&
                item.parents('ul.collapse')[0] != body.find('.main-menu ul.collapse.in')[0])
                body.find('.main-menu ul.collapse.in').removeClass('in');
            item.addClass('active').parents('ul.collapse').addClass('in').css('height', '');
        }
        if(that.is('a')) {
            item = item.add(that);
        }

        // download the panel
        if(panel.length == 0) {
            item.each(function (i, obj) { loadingAnimation($(obj)); });
            if(window.sincluding.length > 0) {
                setTimeout(function () {
                    activateMenu.apply(that, [path, true]);
                }, 1000);
                return;
            }
            setTimeout(function () {window.sincluding[window.sincluding.length] = window.callbackPaths[window.callbackKeys[i]];}, 15);
            $.ajax({
                url: window.callbackPaths[window.callbackKeys[i]],
                type: 'GET',
                dataType: 'text',
                success: function (tab) {
                    var content = $(tab),
                        panes = content.filter('.panel-pane'),
                        styles = ssMergeStyles(content),
                        scripts = ssMergeScripts(content);
                    content = content.not(styles).not(scripts);

                    // don't ever add panes that are already on the page, this is to help with debugging, but should never really happen
                    if (panelIds.length > 0)
                        panes = panes.not('#' + panelIds.join(', #'));

                    if (panes.length > 0) {
                        content.filter('[id]').each(function () {
                            var id = $(this).attr('id');
                            if($('#' + id).length > 0)
                                content = content.not('#' + id);
                        });
                        panes.hide().insertBefore(body.find('.footer'));
                        content.not(panes).insertBefore(body.find('.footer'));
                        var newPane = content.filter('#' + window.callbackKeys[i]);
                        if (newPane.length == 0) {
                            newPane = content.filter('.panel-pane').first();
                        }
                        item.find('.squiggle').stop().remove();
                        activatePanel(newPane);
                    }
                },
                error:function () {
                    item.find('.squiggle').stop().remove();
                }
            });
        }
        // collapse menus and show panel if it is not already visible
        else if(!panel.is(':visible')) {
            item.find('.squiggle').stop().remove();
            activatePanel(panel);
        }
    }
    window.activateMenu = activateMenu;

    function activatePanel(panel)
    {
        collapseMenu.apply(this);
        // animate panels
        var triggerShow = setInterval(function () {
            if(window.sincluding.length == 0) {
                var panels = body.find('.panel-pane:visible').fadeOut(75);
                // poll for panel visibility and fire events
                var triggerHide = setInterval(function () {
                    if(panels.is(':visible'))
                        return;
                    panels.trigger('hide');
                    panel.fadeIn(75);
                    setTimeout(function () {
                        panel.scrollintoview(DASHBOARD_MARGINS).trigger('show')
                    }, 75);
                    clearInterval(triggerHide);
                }, 50);
                clearInterval(triggerShow);
            }
        }, 50);
    }

    body.on('click', 'a[href*="/redirect/facebook"], a[href*="/redirect/google"]', function () {
        loadingAnimation($(this));
    });

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

    // hide any visible modals when panel changes
    body.on('hide', '.panel-pane', function () {
        body.find('.modal:visible').modal('hide');
        body.find('.ui-datepicker').hide();
    });

    body.on('click', '.paginate a', function (evt) {
        evt.preventDefault();
        var admin = $(this).parents('.paginate'),
            page = this.hash.match(/([0-9]*|last|prev|next|first)$/i)[0],
            current = parseInt(admin.find('input[name="page"]').val()),
            last = parseInt(admin.find('.page-total').text());
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
        admin.find('input[name="page"]').val(page).trigger('change');
    });



    function expandMenu(evt)
    {
        var parent = $(this).parents('#left-panel, #right-panel');
        if($(this).is('[href="#collapse"]'))
            return collapseMenu();
        if($(this).is('[href="#expand"]'))
            evt.preventDefault();
        if(parent.length > 0 && parent.width() < 150) {
            // record this special case where its not a link, everything else is recorded automatically
            visits[visits.length] = {path: window.location.pathname, query: window.location.search, hash: '#expand', time:(new Date()).toJSON()};
            // cancel navigation is we are uncollapsing instead
            evt.preventDefault();
            body.find('#left-panel, #right-panel').not(parent).removeClass('expanded').addClass('collapsed');
            // re-render visible panels
            body.find('.panel-pane:visible').redraw();
            var top = -$(window).scrollTop();
            if(parent.is('#left-panel'))
                body.removeClass('right-menu').addClass('left-menu');
            else
                body.removeClass('left-menu').addClass('right-menu');
            parent.removeClass('collapsed').addClass('expanded');
            body.find('.panel-pane:visible').css('top', top);
            $(window).scrollTop(0);
            return false;
        }
        return true;
    }

    function collapseMenu(evt)
    {
        if($(this).is('[href="#collapse"]') || $(this).is('[href="#expand"]'))
            evt.preventDefault();
        if(body.is('.left-menu') || body.is('.right-menu')) {
            // collapse menus
            body.removeClass('right-menu left-menu');
            var top = body.find('.panel-pane:visible').css('top');
            body.find('.panel-pane:visible').css('top', '');
            body.find('#left-panel, #right-panel').removeClass('expanded').addClass('collapsed');
            $(window).scrollTop(-parseInt(top));
            return false;
        }
        return true;
    }

    body.on('show', '#home', function () {
        // TODO: add mobile check here?
        if (typeof navigator != 'undefined' &&
            ((navigator.userAgent.toLowerCase().indexOf("iphone") > -1 &&
            navigator.userAgent.toLowerCase().indexOf("ipad") == -1) ||
            navigator.userAgent.toLowerCase().indexOf("android") > -1)) {
            // show empty
            $('#bookmark').modal({show:true});
        }
    });

    // remove it so it never comes up more than once
    body.on('hidden.bs.modal', '#bookmark', function () {
        $(this).remove();
    });

    body.on('click', ':not(#left-panel):not(#right-panel):not(#left-panel *):not(#right-panel *)', collapseMenu);
    body.on('click', '#left-panel a[href="#collapse"], #right-panel a[href="#collapse"]', collapseMenu);

    function handleLink(evt) {
        var that = $(this),
            el = that[0],
            path = $(this).attr('href'),
            callback = window.callbackUri.indexOf(path);
        if(!expandMenu.apply(this, [evt]))
            return;
        if($(this).is('.invalid a.more'))
        {
            evt.preventDefault();
            evt.stopPropagation();
            return;
        }

        // the path is not a callback so just return normally
        if(typeof window.history == 'undefined' || typeof window.history.pushState == 'undefined' ||
            // check if there is a tab with the selected url
            callback == -1) {
            visits[visits.length] = {path: el.pathname, query: el.search, hash: el.hash, time:(new Date()).toJSON()};
            collapseMenu.apply(this, [evt]);
        }
        // if the path clicked is a callback, use callback to load the new tab
        else
        {
            evt.preventDefault();
            if(window.callbackKeys[callback] == '_welcome') {
                path = window.callbackUri[window.callbackKeys.indexOf('home')];
            }
            activateMenu.apply(this, [path]);
        }
    }

    // capture all callback links
    body.filter('.dashboard-home').on('click', 'button[value]', function () {
        
    });
    body.filter('.dashboard-home').on('click dblclick dragstart', 'a[href]:not(.accordion-toggle)', handleLink);

    window.onpopstate = function(e){
        if(window.callbackKeys.indexOf(e.state) > -1) {
            activateMenu(window.callbackUri[window.callbackKeys.indexOf(e.state)], true);
        }
        else if (window.callbackUri.indexOf(window.location.pathname) > -1) {
            activateMenu(window.location.pathname, true);
        }
    };

    window.onpushstate = function(e){
        if(window.callbackKeys.indexOf(e.state) > -1) {
            activateMenu(window.callbackUri[window.callbackKeys.indexOf(e.state)], true);
        }
        else if (window.callbackUri.indexOf(window.location.pathname) > -1) {
            activateMenu(window.location.pathname, true);
        }
    };

    // -------------- Player --------------- //
    window.musicIndex = 0;
    if(typeof $.fn.jPlayer == 'function') {
        var jp = jQuery('#jquery_jplayer');
        window.musicIndex = Math.floor(Math.random() * window.musicLinks.length);
        jp.jPlayer({
            swfPath: window.callbackPaths['_welcome'] + 'bundles/studysauce/js',
            solution: 'html,flash',
            supplied: 'm4a,mp3,oga',
            preload: 'metadata',
            volume: 0.8,
            muted: false,
            cssSelectorAncestor: '#' + $('#checkin:visible,#home:visible').attr('id'),
            cssSelector: {
                play: '.minplayer-default-play',
                pause: '.minplayer-default-pause'
            },
            ready: function() {
                var index = ++window.musicIndex % window.musicLinks.length;
                $(this).jPlayer( "setMedia", {
                    mp3: window.musicLinks[index],
                    m4a: window.musicLinks[index].substr(0, window.musicLinks[index].length - 4) + '.mp4',
                    oga: window.musicLinks[index].substr(0, window.musicLinks[index].length - 4) + '.ogg'
                });
            }
        });

        jp.bind($.jPlayer.event.ended, function () {
            if(window.musicIndex == -1) {
                window.musicIndex = Math.floor(Math.random() * window.musicLinks.length);
                return;
            }
            var index = ++window.musicIndex % window.musicLinks.length;
            jp.jPlayer("setMedia", {
                mp3: window.musicLinks[index],
                m4a: window.musicLinks[index].substr(0, window.musicLinks[index].length - 4) + '.mp4',
                oga: window.musicLinks[index].substr(0, window.musicLinks[index].length - 4) + '.ogg'
            });
            $(this).jPlayer("play");
        });
    }
    // -------------- END Player --------------- //

    $(window).unload(function () {
        if(typeof checkedInBtn != 'undefined' && body.find(checkedInBtn).length == 0 &&
            window.visits.length > 0)
        {
            $.ajax({url: window.callbackPaths['_visit'] + '?close'});
        }
    });

    var visiting = false;
    setInterval(function () {
        if(visiting)
            return;
        if(visits.length > 0) {
            visiting = true;
            $.ajax({
                url: window.callbackPaths['_visit'] + '?sync',
                type: 'GET',
                data: {},
                success: function () {
                    visiting = false;
                },
                error: function () {
                    visiting = false;
                }
            });
        }
    }, 10000);

});

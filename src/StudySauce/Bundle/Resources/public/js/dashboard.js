
$(document).ready(function () {

    // handles all navigation while on dashboard
    // TODO: remove old unused tabs
    var body = $('body');

    function handleLink(evt) {

        var that = $(this),
            el = that[0],
            path = $(this).attr('href'),
            routes = Routing.match(path) || Routing.match(this.pathname);

        if (!expandMenu.apply(this, [evt]))
            return false;

        if ($(this).is('.invalid a.more') ||
            // do nothing because we are already on the page
            window.location.pathname == this.pathname
        ) {
            evt.preventDefault();
            evt.stopPropagation();
            return false;
        }

        // the path is not a callback so just return normally
        if (typeof window.history == 'undefined' || typeof window.history.pushState == 'undefined'
                // check if there is a tab with the selected url
            || typeof routes[0] == 'undefined' || typeof routes[0].route.requirements._format == 'undefined'
            || routes[0].route.requirements['_format'].indexOf('tab') == -1) {

            visits[visits.length] = {path: el.pathname, query: el.search, hash: el.hash, time: (new Date()).toJSON()};
            collapseMenu.apply(this, [evt]);

            return true;
        }
        // if the path clicked is a callback, use callback to load the new tab
        else {
            evt.preventDefault();
            // allow other click responders to finish processing before doing a page change

            setTimeout(function () {
                if (routes[0].name == '_welcome') {
                    path = Routing.generate(routes[0].name);
                }
                var message = new $.Event('beforeunload');
                $(window).trigger(message);
                if(typeof message.result !== 'undefined') {
                    body.off('click.confirm_navigation').one('click.confirm_navigation', '#general-dialog a[href="#submit"]', function () {
                        activateMenu.apply(that[0], [path]);
                    });

                    $('#general-dialog').modal('hide').modal({show: true, backdrop: true})
                        .find('.modal-body').html(message.result)
                }
                else {
                    activateMenu.apply(that[0], [path]);
                }
            }, 50);
            return false;
        }
    }
    window.handleLink = handleLink;

    // capture all callback links
    body.filter('.dashboard-home').on('click', 'button[value]', function () {

    });
    body.filter('.dashboard-home').on('click dblclick dragstart', 'a[href]:not(.accordion-toggle)', handleLink);

    function loadPanel(path, noPush, activatePanel) {
        var that = $(this);
        var routes = Routing.match(path) || Routing.match(this.pathname),
            subKey = routes[0].name.split('_')[0],
            subPath = Routing.generate(subKey),
            key = subKey,
            requirements = routes[0].route.requirements;

        // add route parameter to tab id if loading a specific page like /packs/2 or /adviser/1
        for (var r in requirements) {
            if (requirements.hasOwnProperty(r) && r != '_format') {
                if (typeof routes[0].params[r] == 'undefined' && !isNaN(parseInt(requirements[r]))) {
                    key += '-' + r + requirements[r];
                }
                else {
                    key += '-' + r + routes[0].params[r];
                }
            }
        }

        var panel = $('#' + key + '.panel-pane'),
            panelIds = body.find('.panel-pane').map(function () {
                return $(this).attr('id');
            }).toArray(),
            item = body.find('.main-menu a[href$="' + subPath + '"]').first();

        // activate the menu
        body.find('.main-menu .active').removeClass('active');

        // do not push when menu is activated from back or forward buttons
        if (!noPush) {
            // create a mock link to get the browser to parse pathname, query, and hash
            var a = document.createElement('a');
            a.href = path;
            visits[visits.length] = {path: a.pathname, query: a.search, hash: a.hash, time: (new Date()).toJSON()};
            window.history.pushState(key, "", path);
        }
        // expand menu groups
        if (item.length > 0) {
            if (item.parents('ul.collapse').length != 0 &&
                item.parents('ul.collapse')[0] != body.find('.main-menu ul.collapse.in')[0])
                body.find('.main-menu ul.collapse.in').removeClass('in');
            item.addClass('active').parents('ul.collapse').addClass('in').css('height', '');
            body.find('#welcome-message .main-menu a').each(function () {
                var parts = $(this).attr('href').split('/');
                parts[parts.length-1] = subPath.substr(1);
                $(this).attr('href', parts.join('/'));
            });
            var host;
            if(!(host = body.find('#welcome-message .main-menu a[href*="' + window.location.hostname +  '"]')).is('.active')) {
                host.addClass('active');
            }
        }
        if (that.is('a')) {
            item = item.add(that);
        }

        var loadTabUrl = Routing.generate(routes[0].name, $.extend(routes[0].params, {_format: 'tab'}));
        // download the panel
        if (panel.length == 0) {
            item.each(function (i, obj) {
                loadingAnimation($(obj));
            });
            if (window.sincluding.length > 0) {
                setTimeout(function () {
                    activateMenu.apply(that, [path, true]);
                }, 1000);
                return;
            }
            window.sincluding[window.sincluding.length] = path;
            $.ajax({
                url: loadTabUrl,
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
                            if ($('#' + id).length > 0)
                                content = content.not('#' + id);
                        });
                        panes.hide().insertBefore(body.find('.footer'));
                        content.not(panes).insertBefore(body.find('.footer'));
                        var newPane = content.filter('#' + key);
                        if (newPane.length == 0) {
                            newPane = content.filter('.panel-pane').first();
                        }
                        item.find('.squiggle').stop().remove();
                        var triggerShow = setInterval(function () {
                                if (window.sincluding.length == 0) {
                                    clearInterval(triggerShow);
                                    activatePanel(newPane);
                                }
                        }, 50);

                    }
                },
                error: function () {
                    item.find('.squiggle').stop().remove();
                }
            });
        }
        // collapse menus and show panel if it is not already visible
        else if (!panel.is(':visible')) {
            item.find('.squiggle').stop().remove();
            activatePanel(panel);
        }
    }
    window.loadPanel = loadPanel;

    function activateMenu(path, noPush) {
        loadPanel.apply(this, [path, noPush, activatePanel]);
    }
    window.activateMenu = activateMenu;

    body.on('click', 'a[href*="/redirect/facebook"], a[href*="/redirect/google"]', function () {
        loadingAnimation($(this));
    });

    function expandMenu(evt) {
        var parent = $(this).closest('#left-panel, #right-panel');
        if($(this).is('a[href="#right-panel"]')) {
            parent = $('#right-panel');
        }
        if ($(this).is('[href="#collapse"]'))
            return collapseMenu();
        if ($(this).is('[href="#expand"]'))
            evt.preventDefault();
        if (parent.length > 0 && parent.width() < 150) {
            // record this special case where its not a link, everything else is recorded automatically
            visits[visits.length] = {
                path: window.location.pathname,
                query: window.location.search,
                hash: '#expand',
                time: (new Date()).toJSON()
            };
            // cancel navigation is we are uncollapsing instead
            evt.preventDefault();
            body.find('#left-panel, #right-panel').not(parent).removeClass('expanded').addClass('collapsed');
            // re-render visible panels
            body.find('.panel-pane:visible').redraw();
            var top = -$(window).scrollTop();
            if (parent.is('#left-panel'))
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

    function collapseMenu(evt) {
        if ($(this).is('[href="#collapse"]') || $(this).is('[href="#expand"]') || $(this).is('[href="#right-panel"]'))
            evt.preventDefault();
        if (body.is('.left-menu') || body.is('.right-menu')) {
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
            $('#bookmark').modal({show: true});
        }
    });

    // remove it so it never comes up more than once
    body.on('hidden.bs.modal', '#bookmark', function () {
        $(this).remove();
    });

    body.on('click', ':not(#left-panel):not(#right-panel):not(#left-panel *):not(#right-panel *):not([href="#right-panel"])', collapseMenu);
    body.on('click', '#left-panel a[href="#collapse"], #right-panel a[href="#collapse"]', collapseMenu);

    window.onpopstate = function (e) {
        var routes = Routing.match(e.state);
        if (typeof routes[0] == 'undefined') {
            routes = Routing.match(window.location.pathname);
        }
        if (typeof routes[0] != 'undefined') {
            activateMenu(Routing.generate(routes[0].name, $.extend({_format: 'tab'}, routes[0].params)), true);
        }
    };

    window.onpushstate = function (e) {
        var routes = Routing.match(e.state) || Routing.match(window.location.pathname);
        if (typeof routes[0] == 'undefined') {
            routes = Routing.match(window.location.pathname);
        }
        if (typeof routes[0] != 'undefined') {
            activateMenu(Routing.generate(routes[0].name, $.extend({_format: 'tab'}, routes[0].params)), true);
        }
    };

    $(window).unload(function () {
        if (typeof checkedInBtn != 'undefined' && body.find(checkedInBtn).length == 0 &&
            window.visits.length > 0) {
            $.ajax({url: Routing.generate('_visit') + '?close'});
        }
    });

    var visiting = false;
    setInterval(function () {
        if (visiting)
            return;
        if (visits.length > 0) {
            visiting = true;
            $.ajax({
                url: Routing.generate('_visit') + '?sync',
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

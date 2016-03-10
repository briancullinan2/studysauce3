

$(document).ready(function () {

    // TODO: remove old unused tabs
    var body = $('body');

    body.on('click', 'a[href*="/plan/download"]', function () {
        body.removeClass('download-plan');
    });

    function activateMenu(path, noPush) {
        var that = $(this);
        var routes = Routing.match(path),
            subKey = routes[0].name,
            subPath = Routing.generate(subKey),
            key = Routing.match(subPath)[0].name;
        // add route parameter to tab id if loading a specific page like /packs/2 or /adviser/1
        for(var r in routes[0].route.requirements) {
            if (routes[0].route.requirements.hasOwnProperty(r) && r != '_format' && routes[0].route.requirements[r] == '[0-9]*' || routes[0].route.requirements[r] == '[0-9]+') {
                key += '-' + r + routes[0].params[r];
            }
        }
        var panel = $('#' + key + '.panel-pane'),
            panelIds = body.find('.panel-pane').map(function () {return $(this).attr('id');}).toArray(),
            item = body.find('.main-menu a[href^="' + subPath + '"]').first();

        // activate the menu
        body.find('.main-menu .active').removeClass('active');

        // do not push when menu is activated from back or forward buttons
        if(!noPush) {
            // create a mock link to get the browser to parse pathname, query, and hash
            var a = document.createElement('a');
            a.href = path;
            visits[visits.length] = {path: a.pathname, query: a.search, hash: a.hash, time: (new Date()).toJSON()};
            window.history.pushState(key, "", path);
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
            setTimeout(function () {window.sincluding[window.sincluding.length] = path;}, 15);
            $.ajax({
                url: Routing.generate(subKey, $.extend({_format: 'tab'}, routes[0].params)),
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
                        var newPane = content.filter('#' + key);
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
            routes = Routing.match(path);
        if(!expandMenu.apply(this, [evt]))
            return;
        if($(this).is('.invalid a.more'))
        {
            evt.preventDefault();
            evt.stopPropagation();
            return;
        }

        // the path is not a callback so just return normally
        if(typeof window.history == 'undefined' || typeof window.history.pushState == 'undefined'
            // check if there is a tab with the selected url
            || typeof routes[0] == 'undefined' || typeof routes[0].route.requirements._format == 'undefined'
            || routes[0].route.requirements['_format'].indexOf('tab') == -1) {
            visits[visits.length] = {path: el.pathname, query: el.search, hash: el.hash, time:(new Date()).toJSON()};
            collapseMenu.apply(this, [evt]);
        }
        // if the path clicked is a callback, use callback to load the new tab
        else
        {
            evt.preventDefault();
            if(routes[0].name == '_welcome') {
                path = Routing.generate(routes[0].name);
            }
            activateMenu.apply(this, [path]);
        }
    }

    // capture all callback links
    body.filter('.dashboard-home').on('click', 'button[value]', function () {
        
    });
    body.filter('.dashboard-home').on('click dblclick dragstart', 'a[href]:not(.accordion-toggle)', handleLink);

    window.onpopstate = function(e){
        var routes = Routing.match(e.state);
        if (typeof routes[0] == 'undefined') {
            routes = Routing.match(window.location.pathname);
        }
        if (typeof routes[0] != 'undefined') {
            activateMenu(Routing.generate(routes[0].name, $.extend({_format: 'tab'}, routes[0].params)), true);
        }
    };

    window.onpushstate = function(e){
        var routes = Routing.match(e.state) || Routing.match(window.location.pathname);
        if (typeof routes[0] == 'undefined') {
            routes = Routing.match(window.location.pathname);
        }
        if (typeof routes[0] != 'undefined') {
            activateMenu(Routing.generate(routes[0].name, $.extend({_format: 'tab'}, routes[0].params)), true);
        }
    };

    $(window).unload(function () {
        if(typeof checkedInBtn != 'undefined' && body.find(checkedInBtn).length == 0 &&
            window.visits.length > 0)
        {
            $.ajax({url: Routing.generate('_visit') + '?close'});
        }
    });

    var visiting = false;
    setInterval(function () {
        if(visiting)
            return;
        if(visits.length > 0) {
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

    body.on('click', 'a[data-target="#upload-file"], a[href="#upload-file"]', function () {
        var dialog = $('#upload-file');

        if(dialog.find('.plupload').is('.init'))
            return;
        var upload = new plupload.Uploader({
            chunk_size: '5MB',
            runtimes : 'html5,flash,silverlight,html4',
            drop_element : 'upload-file',
            browse_button : 'file-upload-select', // you can pass in id...
            container: dialog.find('.plupload')[0], // ... or DOM Element itself
            url : Routing.generate('file_create'),
            unique_names: true,
            max_files: 0,
            multipart: false,
            multiple_queues: true,
            urlstream_upload: false,
            filters : {
                max_file_size : '1gb',
                mime_types: [
                    {
//                        title : "Video files",
//                        extensions : "mov,avi,mpg,mpeg,wmv,mp4,webm,flv,m4v,mkv,ogv,ogg,rm,rmvb,m4v"
                        title : "Image files",
                        extensions : "jpg,jpeg,gif,png,bmp,tiff"
                    }
                ]
            },
            flash_swf_url : Routing.generate('_welcome') + 'bundles/studysauce/js/plupload/js/Moxie.swf',
            silverlight_xap_url : Routing.generate('_welcome') + 'bundles/studysauce/js/plupload/js/Moxie.xap',
            init: {
                PostInit: function(up) {
                    dialog.find('.plupload').addClass('init');
                    dialog.find('#file-upload-select').on('click', function () {
                        up.splice();
                    });
                },
                FilesAdded: function(up) {
                    up.start();
                },
                UploadProgress: function(up) {
                    var squiggle;
                    if((squiggle = dialog.find('.squiggle')).length == 0)
                        squiggle = $('<small class="squiggle">&nbsp;</small>').appendTo(dialog.find('.plup-filelist'));
                    squiggle.stop().animate({width: up.total.percent + '%'}, 1000, 'swing');
                },
                FileUploaded: function(up, file, response) {
                    var data = JSON.parse(response.response);
                    dialog.find('input[type="hidden"]').val(data.fid);
                    dialog.find('.plup-filelist .squiggle').stop().remove();
                    dialog.find('.plupload img').attr('src', data.src);
                },
                Error: function(up, err) {
                }
            }
        });

        setTimeout(function () {upload.init();}, 200);

    });


});

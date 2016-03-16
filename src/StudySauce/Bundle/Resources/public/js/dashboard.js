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
        for (var r in routes[0].route.requirements) {
            if (routes[0].route.requirements.hasOwnProperty(r) && r != '_format' && routes[0].route.requirements[r] == '[0-9]*' || routes[0].route.requirements[r] == '[0-9]+') {
                key += '-' + r + routes[0].params[r];
            }
        }
        var panel = $('#' + key + '.panel-pane'),
            panelIds = body.find('.panel-pane').map(function () {
                return $(this).attr('id');
            }).toArray(),
            item = body.find('.main-menu a[href^="' + subPath + '"]').first();

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
        }
        if (that.is('a')) {
            item = item.add(that);
        }

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
            setTimeout(function () {
                window.sincluding[window.sincluding.length] = path;
            }, 15);
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
                        activatePanel(newPane);
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

    window.activateMenu = activateMenu;

    function activatePanel(panel) {
        collapseMenu.apply(this);
        // animate panels
        var triggerShow = setInterval(function () {
            if (window.sincluding.length == 0) {
                var panels = body.find('.panel-pane:visible').fadeOut(75);
                // poll for panel visibility and fire events
                var triggerHide = setInterval(function () {
                    if (panels.is(':visible'))
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

    function expandMenu(evt) {
        var parent = $(this).parents('#left-panel, #right-panel');
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
        if ($(this).is('[href="#collapse"]') || $(this).is('[href="#expand"]'))
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

    body.on('click', ':not(#left-panel):not(#right-panel):not(#left-panel *):not(#right-panel *)', collapseMenu);
    body.on('click', '#left-panel a[href="#collapse"], #right-panel a[href="#collapse"]', collapseMenu);

    function handleLink(evt) {
        var that = $(this),
            el = that[0],
            path = $(this).attr('href'),
            routes = Routing.match(path);
        if (!expandMenu.apply(this, [evt]))
            return;
        if ($(this).is('.invalid a.more')) {
            evt.preventDefault();
            evt.stopPropagation();
            return;
        }

        // the path is not a callback so just return normally
        if (typeof window.history == 'undefined' || typeof window.history.pushState == 'undefined'
                // check if there is a tab with the selected url
            || typeof routes[0] == 'undefined' || typeof routes[0].route.requirements._format == 'undefined'
            || routes[0].route.requirements['_format'].indexOf('tab') == -1) {
            visits[visits.length] = {path: el.pathname, query: el.search, hash: el.hash, time: (new Date()).toJSON()};
            collapseMenu.apply(this, [evt]);
        }
        // if the path clicked is a callback, use callback to load the new tab
        else {
            evt.preventDefault();
            if (routes[0].name == '_welcome') {
                path = Routing.generate(routes[0].name);
            }
            activateMenu.apply(this, [path]);
        }
    }

    // capture all callback links
    body.filter('.dashboard-home').on('click', 'button[value]', function () {

    });
    body.filter('.dashboard-home').on('click dblclick dragstart', 'a[href]:not(.accordion-toggle)', handleLink);

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

    body.on('hidden.bs.modal', '#upload-file', function () {
        var dialog = $('#upload-file');
        dialog.find('.file').remove();
    });

    body.on('dragover', '#upload-file', function () {
        $(this).addClass('dragging');
    });

    body.on('click', 'a[data-target="#upload-file"], a[href="#upload-file"]', function () {
        var dialog = $('#upload-file');

        if (dialog.find('.plupload').is('.init'))
            return;
        var upload = new plupload.Uploader({
            chunk_size: '5MB',
            runtimes: 'html5,flash,silverlight,html4',
            drop_element: 'upload-file',
            dragdrop: true,
            browse_button: 'file-upload-select', // you can pass in id...
            container: 'upload-file', // ... or DOM Element itself
            url: Routing.generate('file_create'),
            unique_names: true,
            max_files: 0,
            multipart: false,
            multiple_queues: true,
            urlstream_upload: false,
            filters: {
                max_file_size: '1gb',
                mime_types: [
                    {
//                        title : "Video files",
//                        extensions : "mov,avi,mpg,mpeg,wmv,mp4,webm,flv,m4v,mkv,ogv,ogg,rm,rmvb,m4v"
                        title: "Image files",
                        extensions: "jpg,jpeg,gif,png,bmp,tiff"
                    }
                ]
            },
            flash_swf_url: Routing.generate('_welcome') + 'bundles/studysauce/js/plupload/js/Moxie.swf',
            silverlight_xap_url: Routing.generate('_welcome') + 'bundles/studysauce/js/plupload/js/Moxie.xap',
            init: {
                PostInit: function (up) {
                    dialog.find('.plupload').addClass('init');
                    dialog.find('#file-upload-select').on('click', function () {
                        up.splice();
                    });
                },
                FilesAdded: function (up, files) {
                    plupload.each(files, function (file) {
                        $('<div id="' + file.id + '" class="file">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>').appendTo(dialog.find('.plupload'));
                    });
                    up.start();
                },
                UploadProgress: function (up, file) {
                    var squiggle;
                    if ((squiggle = dialog.find('.squiggle')).length == 0)
                        squiggle = $('<small class="squiggle">&nbsp;</small>').appendTo(dialog.find('.plup-filelist'));
                    squiggle.stop().animate({width: up.total.percent + '%'}, 500, 'swing');
                    var subsquiggle;
                    if ((subsquiggle = dialog.find('#' + file.id).find('b').html('<span>' + file.percent + '%</span>').find('.squiggle')).length == 0) {
                        subsquiggle = $('<small class="squiggle">&nbsp;</small>').appendTo(dialog.find('#' + file.id));
                    }
                    subsquiggle.stop().animate({width: file.percent + '%'}, 500, 'swing');
                },
                FileUploaded: function (up, file, response) {
                    var data = JSON.parse(response.response);
                    dialog.find('input[type="hidden"]').val(data.fid);
                    dialog.find('.plup-filelist .squiggle').stop().remove();
                    dialog.find('#' + file.id).find('.squiggle').stop().remove();
                    dialog.find('.plupload img').attr('src', data.src);
                },
                Error: function (up, err) {
                }
            }
        });

        setTimeout(function () {
            upload.init();
        }, 200);

    });

    $(document.body).bind("dragover", function () {
        $(this).addClass('dragging');
    });
    $(document.body).bind("dragleave", function () {
        $(this).removeClass('dragging');
    });
    $(document.body).bind("drop", function () {
        $(this).removeClass('dragging').addClass('dropped');
    });

    // entity search
    function setupFields() {
        var that = body.find('input[type="text"][data-tables]:not(.selectized)');
        that.each(function () {
            var field = $(this);
            $(this).selectize({
                persist: false,
                delimiter: ' ',
                searchField: ['text', 'value', '0'],
                maxItems: 1,
                dropdownParent: null,
                options: [],
                render: {
                    option: function (item) {
                        var desc = '<span class="title">'
                            + '<span class="name"><i class="icon source"></i>' + item.text + '</span>'
                            + '<span class="by">' + (typeof item[0] != 'undefined' ? item[0] : '') + '</span>'
                            + '</span>';
                        var buttons = 1;
                        if (field.data('entities').indexOf(item.table + '-' + item.value) > -1) {
                            desc += '<a href="#subtract-entity" title="Remove"></a>';
                        }
                        else {
                            desc += '<a href="#insert-entity" title="Add"></a>';
                        }
                        return '<div class="entity-search buttons-' + buttons + '">' + desc + '</div>';
                    }
                },
                load: function (query, callback) {
                    if (query.length < 1) {
                        callback();
                        return;
                    }
                    var tables = field.data('tables');
                    var tableNames = [];
                    for (var t in tables) {
                        if (tables.hasOwnProperty(t)) {
                            tableNames[tableNames.length] = t;
                        }
                    }
                    $.ajax({
                        url: Routing.generate('command_callback'),
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            tables: tableNames,
                            search: query
                        },
                        error: function () {
                            callback();
                        },
                        success: function (content) {
                            var results = [];
                            for (var t = 0; t < tableNames.length; t++) {
                                var table = tableNames[t];
                                if (content.hasOwnProperty(table)) {
                                    (function (table) {
                                        results = $.merge(results, content[table].map(function (e) {
                                            return {
                                                table: table,
                                                value: e.id,
                                                text: e[tables[table][0]] + ' ' + e[tables[table][1]],
                                                0: e[tables[table][2]]
                                            }
                                        }));
                                    })(table);
                                }
                            }
                            callback(results);
                        }
                    });
                }
            });
            var existing = field.data('options');
            for (var i in existing) {
                if (existing.hasOwnProperty(i)) {
                    $(this)[0].selectize.addOption(existing[i]);
                }
            }
        });
    }

    body.on('click', '[class*="-row"] a[href^="#edit-"]', setupFields);
    body.on('shown.bs.modal', setupFields);
    body.on('show', '.panel-pane', setupFields);

    // collection control for entity search
    body.on('change', '.entity-search input.selectized', function () {
        if ($(this).val().trim() == '') {
            return;
        }
        var id = parseInt($(this).val());
        var item = $(this)[0].selectize.options[id];
        createEntityRow.apply($(this).parents('label'), [item, $(this).data('entities').indexOf(item.table + '-' + item.value) > -1]);
        $(this).val('');
        this.selectize.setValue('');
        this.selectize.renderCache = {};
    });

    body.on('click', 'a[href="#insert-entity"], a[href="#subtract-entity"]', function (evt) {
        evt.preventDefault();
        var field = $(this).parents('.entity-search').find('label.input');
        var id = parseInt($(this).parents('label').find('input').val());
        createEntityRow.apply(field, [field.find('input')[0].selectize.options[id], $(this).is('[href="#subtract-entity"]')]);
        field.find('input')[0].selectize.renderCache = {};
    });

    body.on('click', 'label:has(input[data-users][data-groups]) ~ a[href="#users-groups"]', function () {
        var field = $(this).siblings('label:has(input[data-users][data-groups])').find('input[data-users][data-groups]');
        var dialog = $('#users-groups');
        var userField = dialog.find('input[name="users"]'),
            groupField = dialog.find('input[name="groups"]');
        userField.data('entities', field.data('entities'));
        groupField.data('entities', field.data('entities'));
        dialog.one('shown.bs.modal', function () {
            var users = field.data('users');
            var groups = field.data('groups');
            userField.data('options', users);
            groupField.data('options', groups);

            // remove existing rows
            dialog.find('.checkbox:not(.template)').remove();

            // create these rows
            for (var u in users) {
                if (users.hasOwnProperty(u)) {
                    createEntityRow.apply(userField.parents('label'), [users[u]]);
                    if (typeof userField[0].selectize != 'undefined') {
                        userField[0].selectize.addOption(users[u]);
                    }
                }
            }
            for (var g in groups) {
                if (groups.hasOwnProperty(g)) {
                    createEntityRow.apply(groupField.parents('label'), [groups[g]]);
                    if (typeof groupField[0].selectize != 'undefined') {
                        groupField[0].selectize.addOption(groups[g]);
                    }
                }
            }
        });
        var row = $(this).parents('.pack-row');
        body.one('click.users_groups', 'a[href="#submit-entities"]', function () {
            // copy users and groups back to field
            field.data('groups', dialog.find('.groups .checkbox:not(.template)').map(function () {
                return $.extend({remove: $(this).find('a[href="#subtract-entity"]').length == 0}, groupField[0].selectize.options[$(this).find('input').val()]);
            }).toArray());

            field.data('users', dialog.find('.users .checkbox:not(.template)').map(function () {
                return $.extend({remove: $(this).find('a[href="#subtract-entity"]').length == 0}, userField[0].selectize.options[$(this).find('input').val()]);
            }).toArray());
        });
    });

    body.on('hidden.bs.modal', '#users_groups', function () {
        setTimeout(function () {
            body.off('click.users_groups');
        }, 100);
    });

    function createEntityRow(option, remove) {
        var field = $(this),
            input = field.find('input[data-entities]'),
            i = field.siblings('.checkbox:not(.template)').length,
            existing = field.siblings('.checkbox').find('input[value="' + option.value + '"]'),
            newRow;
        if (existing.length > 0) {
            i = (/\[([0-9]*)]/i).exec(existing.attr('name'))[1];
            existing.parents('label').replaceWith(newRow = field.siblings('.template').clone());
        }
        else {
            newRow = field.siblings('.template').clone().insertBefore(field.siblings('label.checkbox').first());
        }

        newRow.find('span').text(option.text);
        newRow.find('input').attr('name', input.attr('name') + '[' + i + '][id]').val(option.value);

        if (remove) {
            // remove entity
            newRow.addClass('buttons-1');
            newRow.find('[href="#subtract-entity"]').remove();
            $('<input type="hidden" name="' + newRow.find('input').attr('name').replace('[id]', '[remove]') + '" value="true" />').insertAfter(newRow.find('input'));
            input.data('entities', input.data('entities').filter(function (e) {return e != option.table + '-' + option.value;}));
        }
        else {
            // add entity
            newRow.addClass('buttons-1');
            newRow.find('[href="#insert-entity"]').remove();
            input.data('entities', $.merge(input.data('entities'), [option.table + '-' + option.value]));
        }
        newRow.removeClass('template');
        return newRow;
    }

    window.createEntityRow = createEntityRow;

});

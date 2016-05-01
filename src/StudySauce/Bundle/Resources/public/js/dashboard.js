
Selectize.define( 'clear_selection', function ( options ) {
    var self = this;

    if ((empty = self.$input.find('option[value=""]')).length > 0) {
        self.plugins.settings.dropdown_header = {
            title: empty.text()
        };

        this.require('dropdown_header');

        self.setup = (function () {
            var original = self.setup;

            return function () {
                original.apply(this, arguments);
                this.$dropdown.on('mousedown', '.selectize-dropdown-header', function (e) {
                    self.setValue('');
                    self.close();
                    self.blur();

                    return false;
                });
            }
        })();
    }
});

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
                    if(typeof option[0] == 'undefined') {
                        this.removeItem(currentValue);
                    }
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

$(document).ready(function () {

    // TODO: remove old unused tabs
    var body = $('body');

    body.on('click', 'a[href*="/plan/download"]', function () {
        body.removeClass('download-plan');
    });

    function activateMenu(path, noPush) {
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
                url: Routing.generate(routes[0].name, $.extend({_format: 'tab'}, routes[0].params)),
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
            routes = Routing.match(path) || Routing.match(this.pathname);
        if (!expandMenu.apply(this, [evt]))
            return false;
        if ($(this).is('.invalid a.more')) {
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
        setTimeout(function () {
            body.off('click.upload');
            dialog.find('.plupload img').attr('src', defaultImage).removeClass('add').load(function () {
                centerize.apply($(this));
            });
            dialog.find('.file').remove();
        }, 100);
    });

    body.on('dragover', '#upload-file', function () {
        $(this).addClass('dragging');
    });

    body.on('click', 'a[data-target="#upload-file"], a[href="#upload-file"]', function () {
        var dialog = $('#upload-file');

        // update field next to upload link
        var row = $(this).parents('[class*="-row"]');
        body.one('click.upload', 'a[href="#submit-upload"]', function () {
            var url = dialog.find('img').attr('src');
            // TODO user some sort of data binding api to update this part
            row.addClass('changed').find('input[name="upload"]').val(url).trigger('change').siblings('img').attr('src', url).removeClass('default').load(function () {
                if($(this).is('.centerized')) {
                    centerize.apply(this);
                }
            });
        });

        if (dialog.find('.plupload').is('.init'))
            return;
        defaultImage = dialog.find('.plupload img.default').attr('src');
        var upload = new plupload.Uploader({
            chunk_size: '5MB',
            runtimes: 'html5,flash,silverlight,html4',
            drop_element: 'upload-file',
            dragdrop: true,
            browse_button: 'file-upload-select', // you can pass in id...
            container: plupload[0], // ... or DOM Element itself
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
                        title: "Image files",
                        extensions: "jpg,jpeg,gif,png,bmp,tiff"
                    },
                    {
                        title: "Audio files",
                        extensions: "mp3,ogg,m4a,mp4"
                    },
                    {
                        title : "Video files",
                        extensions : "mov,avi,mpg,mpeg,wmv,mp4,webm,flv,m4v,mkv,ogv,ogg,rm,rmvb,m4v"
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
                        $('<div id="' + file.id + '" class="file">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>').appendTo(dialog.find('.plup-filelist'));
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
                    dialog.find('.plupload img').attr('src', data.src).removeClass('default').load(function () {
                        centerize.apply($(this));
                    });
                },
                Error: function (up, err) {
                }
            }
        });

        setTimeout(function () {
            upload.init();
        }, 200);

    });
    var defaultImage;

    // hide any visible modals when panel changes
    body.on('hide', '.panel-pane', function () {
        body.find('.modal:visible').modal('hide');
        body.find('.ui-datepicker:not(.ui-datepicker-inline)').hide();
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

    // -------------- Player --------------- //
    //window.musicIndex = 0;
    if(typeof $.fn.jPlayer == 'function') {
        var jp = jQuery('#jquery_jplayer');
        //window.musicIndex = Math.floor(Math.random() * window.musicLinks.length);
        jp.jPlayer({
            swfPath: Routing.generate('_welcome') + 'bundles/studysauce/js',
            solution: 'html,flash',
            supplied: 'm4a,mp3,oga',
            preload: 'metadata',
            volume: 0.8,
            muted: false,
            cssSelectorAncestor: '.preview-play:visible',
            cssSelector: {
                play: '.play',
                pause: '.pause'
            },
            ready: function() {

            }
        });

        /*
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
        */
    }
    // -------------- END Player --------------- //

    // entity search
    function setupFields() {
        /*
        var plain = body.find('select:not(.selectized):not([data-tables])');
        plain.each(function () {
            var field = $(this);
            if(field.parents('.template,.read-only').length > 0) {
                return;
            }
            field.selectize({
                preload: 'focus',
                plugins: {
                    'clear_selection': {}
                }
            });
        });
        */

        var that = body.find('input[type="text"][data-tables]:not(.selectized)');
        that.each(function () {
            var field = $(this);
            if(field.parents('.template').length > 0) {
                return;
            }
            var options = [];
            var tables = field.data('tables');
            for (var i in tables) {
                if (tables.hasOwnProperty(i)) {
                    options = $.merge(options, field.data(i) || []);
                }
            }

            field.data('oldValue', field.val()).selectize({
                persist: false,
                delimiter: ' ',
                searchField: ['text', 'value', '0'],
                maxItems: 20,
                dropdownParent: null,
                closeAfterSelect: true,
                options: options,
                plugins: {
                    'clear_selection': {}
                },
                onItemAdd: function (value) {
                    handleSelectize.apply(field[0], [value, field[0].selectize.options[value], false]);
                },
                onItemRemove: function (value) {
                    handleSelectize.apply(field[0], [value, field[0].selectize.options[value], true]);
                },
                render: {
                    option: function (item) {
                        var desc = '<span class="entity-title">'
                            + '<span class="entity-name"><i class="icon source"></i>' + item.text + '</span>'
                            + '<span class="entity-by">' + (typeof item[0] != 'undefined' ? item[0] : '') + '</span>'
                            + '</span>';
                        var buttons = 1,
                            entities;
                        if((entities = field.data('entities')) != null) {
                            if (entities.indexOf(item.value) > -1)
                            {
                                desc += '<a href="#subtract-entity" title="Remove">&nbsp;</a>';
                            }
                        else
                            {
                                desc += '<a href="#insert-entity" title="Add">&nbsp;</a>';
                            }
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
                    $.ajax({
                        url: Routing.generate('command_callback'),
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            tables: tables,
                            search: query
                        },
                        error: function () {
                            callback();
                        },
                        success: function (content) {
                            var results = [];
                            for (var t in tables) {
                                if (!tables.hasOwnProperty(t)) {
                                    continue;
                                }
                                var table = isNaN(parseInt(t)) ? t : tables[t];
                                if (typeof content.results[table] != 'undefined') {
                                    (function (table) {
                                        results = $.merge(results, content.results[table].map(function (e) {
                                            return {
                                                table: table,
                                                value: table + '-' + e.id,
                                                text: e[tables[table][0]] + (typeof e[tables[table][1]] != 'undefined' ? (' ' + e[tables[table][1]]) : ''),
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
            });//.ready(function () {
            //    field[0].selectize.setValue(options);
            //});
        });
    }
    window.setupFields = setupFields;

    body.on('click', '[class*="-row"] a[href^="#edit-"]', setupFields);
    body.on('shown.bs.modal', setupFields);
    body.on('show', '.panel-pane', setupFields);
    body.on('resulted', '.results', setupFields);

    var isSettingSelectize = false;

    function handleSelectize (value, item, remove) {
        var entityField = $(this);
        if(entityField.data('confirm') === false) {
            return;
        }
        if(isSettingSelectize) {
            return;
        }
        isSettingSelectize = true;
        var existing = (entityField.data('entities') || []);
        var isDialog = entityField.parents('#add-entity').length > 0;
        var isTemplate = entityField.is('label:has(~ .checkbox.template) input.selectized[data-tables][data-entities]');
        var obj = $.extend({remove: remove}, item);

        if(isTemplate) {
            obj.remove = existing.indexOf(value) > -1;
            createEntityRow.apply(entityField.parents('label'), [obj, obj.remove]);
            this.selectize.setValue('', true);
        }
        else {
            var oldValue = entityField.data('oldValue').split(' ');
            this.selectize.setValue(oldValue, true);
        }
        this.selectize.renderCache = {};
        entityField.blur();

        if (isDialog) {
            var dialog = $('#add-entity');
            updateRows(entityField, value, obj);
            dialog.find('input[data-entities]').data('entities', entityField.data('entities')); // copy to other fields in dialog for syncronicity
            body.off('click.modify_entities').one('click.modify_entities', 'a[href="#submit-entities"]', function () {
                var toField = dialog.prop('field');
                var oldEntities = toField.data('entities');
                var newEntities = entityField.data('entities');
                var tables = toField.data('tables');

                // get entities differences
                var addItems = newEntities.filter(function (e) {return oldEntities.indexOf(e) == -1});
                var removeItems = oldEntities.filter(function (e) {return newEntities.indexOf(e) == -1});

                // show confirmation dialog
                var message = (addItems.length > 0 ? (' add ' + addItems.map(function (e) {
                        return dialog.find('input[name="' + e.split('-')[0] + '"]')[0].selectize.options[e].text;}).join(', ')) : '')
                    + (addItems.length > 0 && removeItems.length > 0 ? ' and ' : '')
                    + (removeItems.length > 0 ? (' remove ' + removeItems.map(function (e) {
                        return dialog.find('input[name="' + e.split('-')[0] + '"]')[0].selectize.options[e].text;}).join(', ')) : '');

                // confirmation dialog
                body.off('click.modify_entities_confirm').one('click.modify_entities_confirm', '#general-dialog a[href="#submit"]', function () {
                    // filter out the removed and add the new to the field value
                    var newValue = $.merge(toField.val().split(' ').filter(function (e) {return removeItems.indexOf(e) == -1;}), addItems);
                    toField.data('entities', newEntities.slice(0));
                    var allOptions = [];
                    for(var tableName in tables) {
                        if (tables.hasOwnProperty(tableName)) {
                            var options = dialog.find('input[name="' + tableName + '"]').data(tableName);
                            toField.data(tableName, options);
                            allOptions = $.merge(allOptions, options);
                        }
                    }
                    // copy values from dialog back to field after confirmation
                    toField[0].selectize.clearOptions();
                    toField[0].selectize.addOption(allOptions);
                    toField[0].selectize.renderCache = {};
                    confirmEntitySelect.apply(toField, [newValue]);
                });

                $('#general-dialog').modal({show: true, backdrop: true})
                    .find('.modal-body').html('<p>Are you sure you want to ' + message + '?');
            });
        }
        else {
            var message = (obj.remove ? 'remove ' : 'add ') + obj.text;

            // confirmation dialog
            body.off('click.modify_entities_confirm').one('click.modify_entities_confirm', '#general-dialog a[href="#submit"]', function () {
                updateRows(entityField, value, obj);
                // oldValue actually contains updates values
                var oldValue = entityField.data('oldValue').split(' ');
                confirmEntitySelect.apply(entityField, [oldValue]);
            });

            $('#general-dialog').modal({show: true, backdrop: true})
                .find('.modal-body').html('<p>Are you sure you want to ' + message + '?');
        }
        isSettingSelectize = false;
    }

    function confirmEntitySelect(newValue) {
        if(isSettingSelectize) {
            return;
        }
        isSettingSelectize = true;
        var field = $(this);
        field[0].selectize.setValue(newValue, true);
        var tables = field.data('tables');
        var updates = {};
        for(var table in tables) {
            if(tables.hasOwnProperty(table)) {
                updates[table] = field.data(table).map(function (g) {
                    return {id: g.value.substr(8), remove: g['remove']};
                });
            }
        }

        standardSave.apply(field, [updates]);
        isSettingSelectize = false;
    }

    function standardSave(data) {
        var field = $(this);
        var tab = field.closest('.results');
        data = $.extend(data, {requestKey: getDataRequest.apply(tab).requestKey});
        var actionItem = field.closest('[action], [data-action]');
        var saveUrl = actionItem.data('action') || actionItem.attr('action');

        if(typeof saveUrl == 'undefined') {
            throw 'Save action not found!';
        }

        // TODO: loading animation from CTA or activating field?

        $.ajax({
            url: saveUrl,
            type: 'POST',
            dataType: 'text',
            data: data,
            success: function (data) {
                tab.find('.squiggle').stop().remove();
                // copy rows and select
                loadContent.apply(tab, [data]);
            },
            error: function () {
                tab.find('.squiggle').stop().remove();
            }
        });
    }
    window.standardSave = standardSave;

    body.on('change', '.header input[name="search"]', function () {
        var that = $(this);
        var value = that.val();
        var table = value.split('-')[0];
        var id = parseInt(value.split('-')[1]);
        that[0].selectize.setValue('');
        that.blur();
        if(table == 'ss_user') {
            window.activateMenu(Routing.generate('home_user', {user: id}));
        }
        else if(table == 'ss_group') {
            window.activateMenu(Routing.generate('groups_edit', {group: id}));
        }
        else if(table == 'pack') {
            window.activateMenu(Routing.generate('packs_edit', {pack: id}));
        }
    });

    function updateRows (toField, value, item) {
        isSettingSelectize = true;
        var tableName = value.split('-')[0];
        var existing = (toField.data('entities') || []);
        var existingEntities = toField.data(tableName) || [];
        var oldValue = toField.val().split(' ');
        var entity;
        if((entity = existingEntities.filter(function (e) {return e.value == item.value})).length > 0) {
            entity[0].remove = item.remove;
        }
        else {
            existingEntities[existingEntities.length] = item;
        }
        if (item.remove) {
            existing = existing.filter(function (i) {return i != item.value});
            oldValue = oldValue.filter(function (i) {return i != item.value});
        }
        else {
            if(existing.indexOf(item.value) == -1) {
                existing[existing.length] = item.value;
            }
            if(oldValue.indexOf(item.value) == -1) {
                oldValue[oldValue.length] = item.value;
            }
        }
        toField.data('oldValue', oldValue.join(' '));
        toField.data('entities', existing);
        toField.data(tableName, existingEntities);
        isSettingSelectize = false;
    }

    body.on('click', 'a[href="#insert-entity"], a[href="#subtract-entity"]', function (evt) {
        evt.preventDefault();
        var field = $(this).parents('.entity-search').find('input.selectized[data-tables]');
        var check = $(this).parents('label').find('input[type="checkbox"]');
        var id = check.attr('name').split('[')[0] + '-' + parseInt(check.val());
        field[0].selectize.setValue(id);
        //TODO: update field data
    });

    body.on('click', '*:has(input[data-entities]) ~ a[href="#add-entity"]', function () {
        var field = $(this).siblings().find('input[data-entities]');
        var dialog = $('#add-entity').prop('field', field);
        // TODO create fields
        var tables = field.data('tables');

        dialog.find('.tab-pane.active, li').removeClass('active');
        dialog.find('li').hide();
        for(var tableName in tables) {
            if(tables.hasOwnProperty(tableName)) {

                var entityField = dialog.find('input[name="' + tableName + '"][type="text"]');
                if(entityField.length == 0) {
                    var newTemplate = dialog.find('.tab-pane.template').clone()
                        .attr('id', 'add-entity-' + tableName).insertBefore(dialog.find('.tab-pane.template'));
                    newTemplate.removeClass('template active');
                    var title = tableName.replace('ss_', '').substr(0, 1).toUpperCase() + tableName.replace('ss_', '').substr(1);
                    entityField = newTemplate.find('input').attr('name', tableName).attr('placeholder', 'Search for ' + title);
                    newTemplate.find('header label').text('Current ' + title + 's');
                    dialog.find('li.template').clone().appendTo(dialog.find('ul')).removeClass('template active')
                        .find('a').attr('href', '#add-entity-' + tableName).data('target', '#add-entity-' + tableName).text(title);
                }
                dialog.find('a[href="#add-entity-' + tableName + '"]').parent().show();
            }
        }

        dialog.one('shown.bs.modal', function () {
            var first = null;
            for(var tableName in tables) {
                if (tables.hasOwnProperty(tableName)) {
                    if(first == null) {
                        first = tableName;
                    }
                    var entityField = dialog.find('input[name="' + tableName + '"]');
                    var entities = field.data(tableName);
                    var tmpTables = {};
                    tmpTables[tableName] = tables[tableName];
                    entityField.data('tables', tmpTables);
                    entityField.data('oldValue', '');
                    entityField.data('entities', field.data('entities').slice(0));
                    entityField.data(tableName, entities);
                    //entityField.

                    // remove existing rows
                    dialog.find('.checkbox:not(.template)').remove();

                    if(entityField.is('.selectized')) {
                        entityField.val('');
                        entityField[0].selectize.setValue('');
                        entityField[0].selectize.renderCache = {};
                        entityField[0].selectize.clearOptions();
                        entityField[0].selectize.addOption(entities);
                    }
                }
            }

            setTimeout(function () {
                dialog.find('a[href="#add-entity-' + first + '"]').trigger('click');
            }, 50);
        });
    });

    body.on('click', '#add-entity [href^="#add-entity-"]', function () {
        var input = $('#add-entity').find($(this).attr('href')).find('.selectize-input input');
        input.trigger('click');
        setTimeout(function () {
            input.focus();
        }, 50);
    });

    body.on('hidden.bs.modal', '#general-dialog', function () {
        $(this).find('.modal-body').html('<p>put message here</p>');
    });

    body.on('change', '#pack-publish input[name="schedule"]', function () {
        var dialog = $('#pack-publish');
        if(dialog.find('input[name="schedule"]').datetimepicker('getValue') <= new Date()) {
            dialog.find('input[value="now"]').prop('checked', true);
        }
        else {
            dialog.find('input[value="later"]').prop('checked', true);
        }
    });

    body.on('change', '#pack-publish input[name="date"]', function () {
        var dialog = $('#pack-publish'),
            input = dialog.find('input[name="schedule"]');
        if(dialog.find('input[value="now"]').is(':checked')) {
            input.datetimepicker('setOptions', {value: new Date()})
        }
    });

    function showPublishDialog(packId, packName, publish) {
        var field = $(this);
        var dialog = $('#pack-publish').modal({show: true, backdrop: true});

        var allowTimes = [];
        for(var xh=0;xh<=23;xh++){for(var xm=0;xm<60;xm+=30){
            allowTimes[allowTimes.length] = ("0"+xh).slice(-2)+':'+("0"+xm).slice(-2);
        }}
        dialog.find('input[name="schedule"]').datetimepicker({
            format: 'd.m.Y H:i',
            inline: true,
            minDate: 0,
            roundTime: 'ceil'
        //    allowTimes: allowTimes
        }).addClass('dateTimePicker');

        // set up previous publish settings
        if(publish) {
            applyFields.apply(dialog, [publish]);
        }
        var date = new Date(dialog.find('input[name="schedule"]').datetimepicker('getValue'));
        date.setHours(date.getHours() + Math.ceil(date.getMinutes()/60));
        date.setMinutes(0);
        dialog.find('input[name="schedule"]').datetimepicker('setOptions', {value: date});
        dialog.find('input[name="schedule"]').trigger('change');

        body.one('click.publish', '#pack-publish a[href="#submit-publish"]', function () {

            var publish = gatherFields.apply(dialog, [['schedule', 'email', 'alert'], false]);

            // show confirmation dialog
            $('#general-dialog').modal({show: true, backdrop: true})
                .find('.modal-body').html('<p>Are you sure you want to publish ' + packName + '?');

            body.one('click.publish_confirm', '#general-dialog a[href="#submit"]', function () {
                field.data('publish', publish);
                standardSave.apply(field, [{packId: packId.replace('pack-', ''), publish: publish}]);
            });
        });
    }
    window.showPublishDialog = showPublishDialog;

    body.on('hidden.bs.modal', '#general-dialog', function () {
        setTimeout(function () {
            body.off('click.modify_entities_confirm');
            body.off('click.publish_confirm');
            body.off('click.confirm_action');
            body.off('click.confirm_navigation');
        }, 100);
    });

    body.on('hidden.bs.modal', '#add-entity', function () {
        setTimeout(function () {
            body.off('click.modify_entities');
        }, 100);
    });

    function createEntityRow(option, remove) {
        var field = $(this),
            input = field.find('input[data-entities]'),
            i = field.siblings('.checkbox:not(.template)').length,
            id = option.value.substr(input.attr('name').length + 1),
            existing = field.siblings('.checkbox').find('input[name^="' + input.attr('name') + '["][value="' + id + '"]'),
            newRow;
        if (existing.length > 0) {
            i = (/\[([0-9]*)]/i).exec(existing.attr('name'))[1];
            existing.parents('label').replaceWith(newRow = field.siblings('.template').clone());
        }
        else {
            newRow = field.siblings('.template').clone().insertBefore(field.siblings('label.checkbox').first());
        }

        newRow.find('span').text(option.text);
        newRow.find('input').attr('name', input.attr('name') + '[' + i + '][id]').val(id);

        if (remove) {
            // remove entity
            newRow.addClass('buttons-1');
            newRow.find('[href="#subtract-entity"]').remove();
            $('<input type="hidden" name="' + newRow.find('input').attr('name').replace('[id]', '[remove]') + '" value="true" />').insertAfter(newRow.find('input'));
        }
        else {
            // add entity
            newRow.addClass('buttons-1');
            newRow.find('[href="#insert-entity"]').remove();
        }
        newRow.removeClass('template');
        return newRow;
    }

    // TODO: insert publish dialog here

    body.on('hidden.bs.modal', '#pack-publish', function () {
        setTimeout(function () {
            body.off('click.publish');
        }, 100);
    });

});

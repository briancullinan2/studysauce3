
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
                this.setValue(option[this.settings.valueField]);
            }
        };
    })();
});


$(document).ready(function () {
    // handles all entity searching functions

    var body = $('body');


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

        var that = body.find('input[type="text"][data-tables]:not(.selectized):not(.selectizing)');
        that.addClass('selectizing').each(function () {
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
            var fields = getAllFields(tables);
            field.data('oldValue', field.val()).selectize({
                persist: false,
                delimiter: ' ',
                valueField: '_tableValue',
                searchConjunction: 'or',
                searchField: fields,
                maxItems: 20,
                dropdownParent: null,
                closeAfterSelect: true,
                options: options,
                hideSelected: false,
                plugins: {
                    'clear_selection': {}
                },
                onItemAdd: function (value) {
                    handleSelectize.apply(field[0], [value, field[0].selectize.options[value], false]);
                },
                onItemRemove: function (value) {
                    handleSelectize.apply(field[0], [value, field[0].selectize.options[value], true]);
                },
                onOptionAdd: function () {
                    adjustBackdrop();
                },
                onOptionRemove: function () {
                    adjustBackdrop();
                },
                onLoad: function () {
                    adjustBackdrop();
                },
                onType: function () {
                    adjustBackdrop();
                },
                onBlur: function () {
                    adjustBackdrop();
                },
                onFocus: function () {
                    adjustBackdrop();
                },
                render: {
                    option: function (item) {
                        var tmpTables = {};
                        tmpTables[item['table']] = tables[item['table']];
                        return window.views.render('cell_collectionRow', {context: $('<div/>'), entity: item, tables: tmpTables});
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
                                    results = $.merge(results, content.results[table]);
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
        var isInline = entityField.parents('.entity-search').find('header').length > 0;
        var obj = $.extend({removed: remove}, item);

        if(isInline) {
            obj.removed = existing.indexOf(value) > -1;
            entityField[0].selectize.setValue('', true);
        }
        else {
            var oldValue = entityField.data('oldValue').split(' ');
            entityField[0].selectize.setValue(oldValue, true);
        }
        entityField[0].selectize.renderCache = {};
        entityField.blur();

        if (isDialog) {
            var dialog = $('#add-entity');
            window.views.render.apply(entityField.parents('.entity-search').parent(), ['cell-collection', {tables: $.extend({}, entityField.data('tables')), entities: [obj], entityIds: entityField.data('entities').slice(0)}]);
            adjustBackdrop();
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
                        var field = dialog.find('input[name="' + e.split('-')[0] + '"]');
                        var option = field[0].selectize.options[e];
                        return option[tables[option['table']][0]] + ' ' + option[tables[option['table']][1]];}).join(', ')) : '')
                    + (addItems.length > 0 && removeItems.length > 0 ? ' and ' : '')
                    + (removeItems.length > 0 ? (' remove ' + removeItems.map(function (e) {
                        var field = dialog.find('input[name="' + e.split('-')[0] + '"]');
                        var option = field[0].selectize.options[e];
                        return option[tables[option['table']][0]] + ' ' + option[tables[option['table']][1]];}).join(', ')) : '');

                // confirmation dialog
                body.off('click.modify_entities_confirm').one('click.modify_entities_confirm', '#general-dialog a[href="#submit"]', function () {
                    // filter out the removed and add the new to the field value
                    var newValue = $.merge(toField.val().split(' ').filter(function (e) {return removeItems.indexOf(e) == -1;}), addItems);
                    toField.data('entities', newEntities.slice(0));
                    var allOptions = [];
                    for(var tableName in tables) {
                        if (tables.hasOwnProperty(tableName)) {
                            var options = dialog.find('input[name="' + tableName + '"]').data(tableName);
                            toField.data(tableName, options.slice(0));
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
            var tables = entityField.data('tables');
            var message = (obj.removed ? 'remove ' : 'add ') + obj[tables[obj['table']][0]] + ' ' + obj[tables[obj['table']][1]];

            // confirmation dialog
            body.off('click.modify_entities_confirm').one('click.modify_entities_confirm', '#general-dialog a[href="#submit"]', function () {
                window.views.render.apply(entityField.parents('.entity-search').parent(), ['cell-collection', {tables: $.extend({}, entityField.data('tables')), entities: [obj], entityIds: entityField.data('entities').slice(0)}]);
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
                (function (table) {
                    updates[table] = field.data(table).map(function (g) {
                        return {id: g[field[0].selectize.settings.valueField].substr(table.length + 1), remove: g['removed']};
                    });
                })(table);
            }
        }

        standardSave.apply(field, [updates]);
        isSettingSelectize = false;
    }

    body.on('click', 'a[href="#insert-entity"], a[href="#subtract-entity"]', function (evt) {
        evt.preventDefault();
        var field = $(this).parents('.entity-search').find('input.selectized[data-tables]');
        var check = $(this).parents('label').find('input[type="checkbox"]');
        var id = check.attr('name').split('[')[0] + '-' + parseInt(check.val());
        var item = field[0].selectize.options[id];
        handleSelectize.apply(field[0], [id, item, true]);
        //item.removed = typeof item.removed == 'undefined' ? true : !item.removed;
        //window.views.render.apply(field.parents('.entity-search').parent(), ['cell-collection', {tables: $.extend({}, field.data('tables')), entities: [item], entityIds: field.data('entities').slice(0)}]);
    });

    body.on('click', '*:has(input[data-entities]) ~ a[href="#add-entity"], form *:has(input[data-entities]) ~ * a[href="#add-entity"]', function () {
        var field = $(this).siblings().find('input[data-entities]');
        if(field.length == 0) {
            field = $(this).parents('form').find('input[data-entities]');
        }
        // TODO create fields
        var tables = $.extend({}, field.data('tables'));
        var dialogStr = window.views.render.apply(body, ['add_entity', {tables: tables, entities: field.data('ss_user').slice(0), entityIds: field.data('entities').slice(0)}]);
        if ($('#add-entity').length == 0) {
            $(dialogStr).appendTo(body);
        }
    });

    body.on('click', 'a[data-target="#create-entity"]', function () {
        var tableNames = $(this).attr('href').split('-').slice(1);
        var tmpTables = {};
        tmpTables[tableNames[0]] = AdminController.__vars.defaultMiniTables[tableNames[0]];
        var dialogStr = window.views.render.apply(body, ['create_entity', {tables: tmpTables, entities: [], entityIds: []}]);
        if ($('#create-entity').length == 0) {
            $(dialogStr).appendTo(body);
        }
    });

    body.on('click', '#add-entity [href^="#add-entity-"]', function () {
        var input = $('#add-entity').find($(this).attr('href')).find('.selectize-input input');
        input.trigger('click');
        setTimeout(function () {
            input.focus();
        }, 50);
    });

    body.on('hidden.bs.modal', '#add-entity', function () {
        setTimeout(function () {
            body.off('click.modify_entities');
        }, 100);
    });
});
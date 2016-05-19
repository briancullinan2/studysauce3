
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
        body.find('input[data-confirm], select:has(option[data-confirm])').each(function () {
            $(this).data('oldValue', $(this).val());
        });

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
            var fields = getAllFieldNames(tables);
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
                    return true;
                },
                onItemRemove: function (value) {
                    handleSelectize.apply(field[0], [value, field[0].selectize.options[value], true]);
                    return true;
                },
                onDropdownClose: function(dropdown) {
                    $(dropdown).prev().find('input').blur();
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
                        var newItem = $.extend({}, item);
                        if (field.parents('#add-entity').length > 0) {
                            newItem = $.extend(newItem, {removed: (field.data('entities') || []).indexOf(item._tableValue) == -1});
                        }
                        return window.views.render('cell_collectionRow', {
                            context: $('<div/>'),
                            entity: newItem,
                            tables: tmpTables
                        });
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
            });
        });
    }
    window.setupFields = setupFields;

    body.on('click', '[class*="-row"] a[href^="#edit-"]', setupFields);
    body.on('shown.bs.modal', setupFields);
    body.on('show', '.panel-pane', setupFields);
    body.on('resulted', '.results', setupFields);

    var isSettingSelectize = false;

    function handleSelectize (value, item, removed) {
        var entityField = $(this);
        if(entityField.data('confirm') === false) { // probably means this handling logic is implemented elsewhere
            return;
        }
        // don't update again when the value changes from this method
        if(isSettingSelectize) {
            return;
        }
        isSettingSelectize = true;

        // do a few extra things to help list in dialog stay open after clicking
        item.removed = removed;
        var dialog;
        var existing = (entityField.data('entities') || []);

        var obj = $.extend({}, item);
        obj.removed = existing.indexOf(value) > -1;
        entityField[0].selectize.setValue('', true);
        // TODO: item._tableValue = '' would make it disappear from the list, then reappear could take place when checkbox is unchecked
        // reset drop down field
        setTimeout(function () {
            entityField[0].selectize.renderCache = [];
            entityField[0].selectize.$control_input.blur();
            entityField[0].selectize.$control_input.trigger('click');
        }, 50);

        if ((dialog = entityField.parents('#add-entity')).length > 0) {
            window.views.render.apply(entityField.parents('.entity-search').parent(), ['cell-collection', {
                tables: $.extend({}, entityField.data('tables')),
                entities: [obj],
                entityIds: entityField.data('entities').slice(0),
                removedEntities: obj.removed ? [obj] : []
            }]);
            // synchronize with other fields in the dialog for consistency
            dialog.find('.input input.selectized').not(entityField).data('entities', entityField.data('entities'));

            // TODO: update confirmation message

            adjustBackdrop();
        }
        else {
            // TODO: set confirmation message
            var oldEntities = $.merge([], existing);
            var current = entityField[0].selectize.getValue().split(' ');
            var oldValue = entityField.data('oldValue'),
                newEntities = null, newValue, valI;

            if((valI = existing.indexOf(value)) > -1 && removed) {
                newEntities = $.merge([], existing);
                newEntities.splice(valI, 1);
                newValue = current.filter(function (i) {return i != value;});
            } else if (valI == -1 && !removed) {
                newEntities = $.merge([value], existing);
                newValue = current.indexOf(value) == -1 ? $.merge([value], current) : current;
            }
            entityField[0].selectize.setValue(oldValue, true);

            if(newEntities == null) {
                isSettingSelectize = false;
                return;
            }

            showEntityDialog.apply(entityField, [entityField, oldEntities, newEntities]);

            body.off('click.modify_entities_confirm').one('click.modify_entities_confirm', '#general-dialog a[href="#submit"]', function () {
                isSettingSelectize = true;
                window.views.render.apply(entityField.parents('.entity-search').parent(), ['cell-collection', {
                    tables: $.extend({}, entityField.data('tables')),
                    entities: [obj],
                    entityIds: newEntities.slice(0),
                    removedEntities: obj.removed ? [obj] : []
                }]);
                entityField[0].selectize.setValue(newValue, true);
                var tables = entityField.data('tables'),
                // filter out the removed and add the new to the field value
                // TODO: fix this for inline version var newValue = $.merge(toField.val().split(' ').filter(function (e) {return removeItems.indexOf(e) == -1;}), addItems);
                    updates = {};
                for(var tableName in tables) {
                    if (tables.hasOwnProperty(tableName)) {
                        var options = entityField.data(tableName);
                        for(var o = 0; o < options.length; o++) {
                            var g = options[o];
                            assignSubKey(updates, decodeURIComponent(entityField.attr('name') || tableName) + '[' + o + ']', {
                                id: g['_tableValue'].substr(tableName.length + 1),
                                remove: g['removed']
                            });
                        }
                        entityField.data(tableName, options.slice(0));
                    }
                }
                standardSave.apply(entityField, [updates]);
                isSettingSelectize = false;
            });
        }

        isSettingSelectize = false;
    }

    body.on('click', 'a[href="#insert-entity"], a[href="#subtract-entity"]', function (evt) {
        evt.preventDefault();
        var field = $(this).parents('.entity-search').find('input.selectized[data-tables]');
        var check = $(this).parents('label').find('input[type="checkbox"]');
        var id = check.attr('name').split('[')[0] + '-' + parseInt(check.val());
        var item = field[0].selectize.options[id];
        //var existing = field.data('entities');
        //field.data('entities', existing.indexOf(item._tableValue) > -1
        //    ? existing.filter(function (e) {return e != item._tableValue;})
        //    : $.merge([item._tableValue] , existing));
        item.removed = $(this).is('[href="#subtract-entity"]');
        handleSelectize.apply(field[0], [id, item, $(this).is('[href="#subtract-entity"]')]);
    });

    function copyToDialog(dialogName) {
        var field = $(this);
        var settings = {
            tables: $.extend({}, field.data('tables') || {}),
            entityIds:  (field.data('entities') || []).slice(0),
            confirm: field.data('confirm') || true, // dialog uses this to determine if a confirm should be displayed at the end, as opposed to confirming every field change
            entities: [] // TODO: include default entities
        };
        for(var t in settings.tables) {
            if(settings.tables.hasOwnProperty(t)) {
                settings.entities = $.merge(settings.entities, (field.data(t) || []).slice(0));
            }
        }

        var dialogStr = window.views.render.apply(body, [dialogName, settings]);
        var dialog;
        if ((dialog = $('#' + dialogName)).length == 0) {
            dialog = $(dialogStr).appendTo(body);
        }
        dialog.prop('field', field);
        adjustBackdrop();
    }

    // TODO: activate this from a data-confirm data-modal reference
    body.on('click', '#add-entity a[href="#submit-entities"]', function () {
        var dialog = $('#add-entity');
        // create a confirmation message
        var fromField = dialog.find('input.selectized');
        var toField = dialog.prop('field');
        var oldEntities = toField.data('entities');
        var newEntities = dialog.find('input.selectized').first().data('entities');

        showEntityDialog.apply(fromField, [toField, oldEntities, newEntities]);
        // confirmation dialog
        body.off('click.modify_entities_confirm').one('click.modify_entities_confirm', '#general-dialog a[href="#submit"]', function () {
            copyFromDialog.apply(toField);
        });

    });

    function showEntityDialog(toField, oldEntities, newEntities) {
        var tables = toField.data('tables');
        var fromField = $(this).filter('input.selectized');

        // get entities differences
        var addItems = newEntities.filter(function (e) {return oldEntities.indexOf(e) == -1});
        var removeItems = oldEntities.filter(function (e) {return newEntities.indexOf(e) == -1});

        // show confirmation dialog
        var message = (addItems.length > 0 ? (' add ' + addItems.map(function (e) {
                var option = fromField.filter('[data-' + e.split('-')[0] + ']')[0].selectize.options[e];
                return option[tables[option['table']][0]] + ' ' + option[tables[option['table']][1]];}).join(', ')) : '')
            + (addItems.length > 0 && removeItems.length > 0 ? ' and ' : '')
            + (removeItems.length > 0 ? (' remove ' + removeItems.map(function (e) {
                var option = fromField.filter('[data-' + e.split('-')[0] + ']')[0].selectize.options[e];
                return option[tables[option['table']][0]] + ' ' + option[tables[option['table']][1]];}).join(', ')) : '');

        // TODO: on pack entities show publish-confirm dialog
        if(typeof tables['pack'] != 'undefined') {
            // TODO: add additional messaging concerning subgroups
            var rows = toField.parents('.panel-pane').find('.ss_group-row:visible'); // TODO: not() same id as this page?
            message += ' ' + (addItems.length > 0 ? 'to' : '')
                + (addItems.length > 0 && removeItems.length ? '/' : '')
                + (removeItems.length > 0 ? 'from' : '')
                + ' ALL ' + (rows.length - 1) + ' subgroups (affects ' + rows.map(function () {
                    var result = parseInt($(this).find('.title label:nth-child(2) span').text());
                    return isNaN(result) ? 0 : result;}).toArray().reduce(function (a, b) {return a + b;}) + ' users) ';
            //if(addItems.length > 0) {
            //    // TODO: if adding show publish dialog instead
            //    showPublishDialog.apply(toField, [addItems[0], packName, null]);
            //}
        }

        $('#general-dialog').modal({show: true, backdrop: true})
            .find('.modal-body').html('<p>Are you sure you want to ' + message.trim() + '?');
    }

    function resetFieldToData() {
        var toField = $(this);
        var searchFields = getAllFieldNames(toField.data('tables')).slice(0, 3);
        if(toField.is('.selectized')) {
            toField[0].selectize.setValue('');
            toField[0].selectize.renderCache = [];
            toField[0].selectize.clearOptions();
            toField[0].selectize.settings.searchField = searchFields;
            toField[0].selectize.addOption(getAllOptions.apply(toField));
        }
    }

    body.on('show.bs.modal', '#add-entity', function () {
        var dialog = $('#add-entity');

        setTimeout(function () {

            // update all selectize fields to match options in data
            dialog.find('li:visible a').each(function () {
                var field = dialog.find($(this).attr('href')).find('input.selectized');
                resetFieldToData.apply(field);
            });

            // focus on the visible selectize control to activate dropdown menu
            var visible = dialog.find('.tab-pane:visible .selectize-control input');
            visible.trigger('click');
            visible.focus();
        }, 100);

    });

    function getAllOptions() {
        var toField = $(this);
        var tables = toField.data('tables');
        // filter out the removed and add the new to the field value
        var allOptions = [];
        for(var tableName in tables) {
            if (tables.hasOwnProperty(tableName)) {
                var options = toField.data(tableName);
                allOptions = $.merge(allOptions, options);
            }
        }

        return allOptions;
    }

    function copyFromDialog () {
        var dialog = $('#add-entity'),
            toField = $(this),
            tables = toField.data('tables'),
        // filter out the removed and add the new to the field value
        // TODO: fix this for inline version var newValue = $.merge(toField.val().split(' ').filter(function (e) {return removeItems.indexOf(e) == -1;}), addItems);
            updates = {};
        for(var tableName in tables) {
            if (tables.hasOwnProperty(tableName)) {
                var fromField = dialog.find('input[name="' + tableName + '"]');
                var options = fromField.data(tableName);
                for(var o = 0; o < options.length; o++) {
                    var g = options[o];
                    assignSubKey(updates, decodeURIComponent(toField.attr('name') || tableName) + '[' + o + ']', {
                        id: g['_tableValue'].substr(tableName.length + 1),
                        remove: g['removed']
                    });
                }
                toField.data(tableName, options.slice(0));
            }
        }
        // copy values from dialog back to field after confirmation
        toField.data('entities', dialog.find('input.selectized').data('entities').slice(0));
        // TODO: fix this for inline version toField[0].selectize.setValue(newValue, true);

        // reset toField
        resetFieldToData.apply(toField);
        standardSave.apply(toField, [updates]);
    }

    body.on('click', '[data-target="#create-entity"], [data-target="#add-entity"]', function () {
        var field, dialog;
        if((dialog = $(this).parents('#create-entity')).length > 0) {
            field = dialog.prop('field');
        }
        else if ($(this).is('[data-tables][data-entities]')) {
            field = $(this);
        }
        else {
            field = $(this).parents('.entity-search').find('input[data-tables]');
        }
        copyToDialog.apply(field, [$(this).data('target').substr(1)]);
    });

    body.on('hidden.bs.modal', '#add-entity', function () {
        setTimeout(function () {
            body.off('click.modify_entities');
        }, 100);
    });
});
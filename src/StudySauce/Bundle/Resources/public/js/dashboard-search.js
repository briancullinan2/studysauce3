
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
            window.views.render.apply(entityField.parents('.entity-search'), ['cell-collection', {entities: [obj]}]);
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
                (function (table) {
                    updates[table] = field.data(table).map(function (g) {
                        return {id: g.value.substr(table.length + 1), remove: g['remove']};
                    });
                })(table);
            }
        }

        standardSave.apply(field, [updates]);
        isSettingSelectize = false;
    }

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

    body.on('click', '*:has(input[data-entities]) ~ a[href="#add-entity"], form *:has(input[data-entities]) ~ * a[href="#add-entity"]', function () {
        var field = $(this).siblings().find('input[data-entities]');
        // TODO create fields
        var tables = field.data('tables');
        window.views.render.apply(body, ['add-entity', {tables: tables, entities: field.data('entities').splice(0)}]);
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
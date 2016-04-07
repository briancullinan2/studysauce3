
$(document).ready(function () {

    var body = $('body'),
        autoSaveTimeout = 0;

    function groupsFunc(evt) {
        var tab = $(this).parents('.results:visible');
        var groupRow = tab.find('.ss_group-row.edit');
        //if(groupRow.find('.name input').val().trim() == '') {
        //    tab.find('.highlighted-link').removeClass('valid').addClass('invalid');
        //}
        //else {
        //    tab.find('.highlighted-link').removeClass('invalid').addClass('valid');
        //}

        // do not autosave from selectize because the input underneath will change
        if(typeof evt != 'undefined' && $(evt.target).parents('.selectize-input').length > 0) {
            return;
        }

        // save at most every 2 seconds, don't autosave from admin lists
        //if (autoSaveTimeout === null && $('.panel-pane[id^="groups-"]:visible').length > 0) {
        //    autoSaveTimeout = setTimeout(function () {
        //        autoSave.apply(tab);
        //    }, 2000);
        //}

    }

    body.on('click', '[id^="groups-"] .pack-row [href="#remove-confirm-pack"]', function (evt) {
        evt.preventDefault();
        var tab = $(this).parents('.results');
        var row = $(this).parents('.pack-row');
        var group = tab.find('.ss_group-row.edit:not(.template)');
        var id = ((/pack-id-([0-9]*)(\s|$)/ig).exec(row.attr('class')) || [])[1];
        var groupId = ((/ss_group-id-([0-9]*)(\s|$)/ig).exec(group.attr('class')) || [])[1];
        body.one('click.remove', '#confirm-remove a[href="#remove-confirm"]', function () {
            row.addClass('removed');
            $.ajax({
                url: Routing.generate('save_group'),
                type: 'POST',
                dataType: 'text',
                data: {
                    groupId: groupId,
                    packId: id != null ? id : null,
                    groups: [{id: groupId, remove: true}]
                },
                success: function (data) {
                    // copy rows and select
                    var selected = (/pack-id-([0-9]+)(\s|$)/ig).exec(tab.find('> .pack-row.selected').attr('class') || '');
                    loadContent.apply(tab, [data, ['pack']]);
                    if (selected) {
                        tab.find('> .pack-row.pack-id-' + selected[1]).addClass('selected');
                    }
                }
            });
        });
        row.removeClass('removed');
    });

    body.on('click', '[id^="groups"] .ss_group-row [href="#remove-confirm-group"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('.ss_group-row');
        var groupId = ((/ss_group-id-([0-9]*)(\s|$)/ig).exec(row.attr('class')) || [])[1];
        body.one('click.remove', '#confirm-remove a[href="#remove-confirm"]', function () {
            row.addClass('removed');
            $.ajax({
                url: Routing.generate('save_group'),
                type: 'POST',
                dataType: 'text',
                data: {
                    groupId: groupId,
                    remove: true
                },
                success: function (data) {
                    // copy rows and redirect
                    var groups;
                    if ((groups = $('#groups')).length > 0) {
                        loadContent.apply(groups.find('.results'), [data, ['ss_group']]);
                    }
                    activateMenu(Routing.generate('groups'));
                }
            });
        });
        row.removeClass('removed');
    });

    body.on('hidden.bs.modal', '#confirm-remove', function () {
        setTimeout(function () {
            body.off('click.remove');
        }, 100);
    });

    body.on('click', '[id^="groups"] a[href="#add-new-ss_group"]', function (evt) {
        evt.preventDefault();
        window.activateMenu(Routing.generate('groups_new'));
    });

    function autoSave(close) {
        autoSaveTimeout = null;
        var tab = $(this);
        var row = tab.find('.ss_group-row.edit:not(.template)');
        //if(tab.find('.highlighted-link').is('.invalid'))
        //    return;
        loadingAnimation(tab.find('a[href="#save-ss_group"]'));
        //tab.find('.highlighted-link').removeClass('valid').addClass('invalid');

        $.ajax({
            url: Routing.generate('save_group'),
            type: 'POST',
            dataType: close ? 'text' : 'json',
            data: {
                logo: row.find('.id img:not(.default)').attr('src'),
                groupName: row.find('input[name="name"]').val().trim(),
                roles: row.find('input[name="roles"]:checked').map(function () {return $(this).val();}).toArray().join(','),
                groupId: ((/ss_group-id-([0-9]*)(\s|$)/ig).exec(row.attr('class')) || [])[1],
                parent: row.find('select[name="parent"]').val()
            },
            success: function (data) {
                tab.find('.squiggle').stop().remove();
                if (close) {

                }
                else {
                    // copy new ids to new rows
                    for(var i = 0; i < data.ss_group.length; i++) {
                        if(row.filter('.ss_group-id-' + data.ss_group[i].id).length == 0) {
                            row.filter('.edit.ss_group-id-:not(.template)').first().removeClass('ss_group-id-').addClass('ss_group-id-' + data.ss_group[i].id);
                            // set pack id on panel
                            tab.closest('.panel-pane').attr('id', 'groups-group' + data.ss_group[i].id);
                            window.activateMenu(Routing.generate('groups_edit', {group: data.ss_group[i].id}));
                        }
                    }

                }
            },
            error: function () {
                tab.find('.squiggle').stop().remove();
            }
        });
    }

    body.on('show', '.panel-pane[id^="groups-"]', function () {
        groupsFunc.apply($(this).find('.ss_group-row'));
        autoSaveTimeout = null;
    });

    body.on('click', '[id^="groups-"] a[href="#save-ss_group"], [id^="groups-"] [value="#save-ss_group"]', function (evt) {
        evt.preventDefault();

        if (autoSaveTimeout != null) {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = null;
        }
        autoSave.apply($(this).parents('.results:visible'), [true]);

    });

    body.on('change keyup keydown', '[id^="groups-"] .ss_group-row input, [id^="groups-"] .ss_group-row select, [id^="groups-"] .ss_group-row textarea', groupsFunc);

    body.on('click', '.group-list .ss_group-row', function (evt) {
        if($(evt.target).is('a[href="#edit-group"]') || !$(evt.target).is('a, .ss_group-row > .packs')
            && $(evt.target).parents('.ss_group-row > .packs').length == 0)
        {
            var results = $(this).parents('.results');
            var row = $(this).closest('.ss_group-row');
            var groupId = (/ss_group-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1];
            window.activateMenu(Routing.generate('groups_edit', {group: groupId}));
            row.removeClass('edit').addClass('read-only');
        }
    });

    body.on('click', '[id^="groups-"] .ss_group-row.edit a[href^="#cancel-"], [id^="groups-"] .ss_group-row ~ .highlighted-link a[href^="#cancel"]', function (evt) {
        evt.preventDefault();
        window.activateMenu(Routing.generate('groups'));
    });

    body.on('click', '[id^="groups-"] .ss_group-row a[href="#upload-image"]', function () {
        var row = $(this).parents('.ss_group-row');
        body.one('click.upload', 'a[href="#submit-upload"]', function () {
            row.find('.id img').attr('src', $('#upload-file').find('img').attr('src')).removeClass('default');
            groupsFunc.apply(row);
        });
    });

    body.on('change', '[id^="groups-"] .ss_group-row .packs input.selectized', function (evt) {
        var dialog = $('#pack-publish').modal({show: true, backdrop: true});
        var tab = $(this).parents('.results'),
            groupId = (/ss_group-id-([0-9]+)(\s|$)/ig).exec($(this).parents('.ss_group-row').attr('class'))[1],
            label = $(this).parents('label');


        if ($(this).val().trim() == '') {
            return;
        }
        var id = $(this).val(),
            packName = this.selectize.options[id].text;
        $(this).val('');
        this.selectize.setValue('');
        this.selectize.renderCache = {};


        dialog.find('input[name="publish-date"]').datetimepicker({
            format: 'd.m.Y H:i',
            inline: true,
            minDate: 0
        });
        dialog.one('click.publish', 'a[href="#submit-publish"]', function () {


            var publish = {
                schedule: dialog.find('input[name="publish-schedule"]:checked').val() == 'now' ? 'now' : dialog.find('input[name="publish-date"]').datetimepicker('getValue'),
                email: dialog.find('input[name="publish-email"]:checked').val() != null,
                alert: dialog.find('input[name="publish-alert"]:checked').val() != null
            };

            // show confirmation dialog
            $('#general-dialog').modal({show: true, backdrop: true})
                .find('.modal-body').html('<p>Are you sure you want to publish ' + packName + '?');

            body.one('click.publish_confirm', '#general-dialog a[href="#submit"]', function () {

                // save packs
                loadingAnimation(tab.find('a[href="#save-pack"]'));

                $.ajax({
                    url: Routing.generate('save_group'),
                    type: 'POST',
                    dataType: 'text',
                    data: {
                        groupId: groupId,
                        packId: id != null ? id.substr(5) : null,
                        groups: [{id: groupId, remove: false}],
                        publish: publish
                    },
                    success: function (data) {
                        tab.find('.squiggle').stop().remove();
                        // copy rows and select
                        var selected = (/pack-id-([0-9]+)(\s|$)/ig).exec(tab.find('> .pack-row.selected').attr('class') || '');
                        loadContent.apply(tab, [data, ['pack']]);
                        if (selected) {
                            tab.find('> .pack-row.pack-id-' + selected[1]).addClass('selected');
                        }
                    },
                    error: function () {
                        tab.find('.squiggle').stop().remove();
                    }
                });
            });
        });
    });

    body.on('click', '[id^="groups-"] a[href="#edit-ss_group"]', function (evt) {
        evt.preventDefault();
        $(this).parents('.results').find('.ss_group-row').removeClass('read-only').addClass('edit');
        window.setupFields();
    });

    body.on('click', '[id^="groups-"] a[href="#edit-pack"]', function () {
        var results = $(this).parents('.results');
        var row = $(this).parents('.pack-row');
        var packId = (/pack-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1];
        window.activateMenu(Routing.generate('packs_edit', {pack: packId}));
        row.removeClass('edit').addClass('read-only');
    });

    body.on('hidden.bs.modal', '#general-dialog', function () {
        setTimeout(function () {
            body.off('click.modify_entities_confirm');
            body.off('click.publish_confirm');
        }, 100);
    });

    body.on('hidden.bs.modal', '#pack-publish', function () {
        setTimeout(function () {
            $(this).off('click.publish');
        }, 100);
    });

    body.on('click', '[id^="groups-"] *:has(input[data-ss_user]) ~ a[href="#add-entity"]', function () {
        var tab = $(this).parents('.results'),
            row = $(this).parents('.pack-row'),
            rowId = (/pack-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1],
            groupId = (/ss_group-id-([0-9]+)(\s|$)/ig).exec(tab.find('.ss_group-row.edit').first().attr('class'))[1],
            field = $(this).siblings('*:has(input[data-ss_user])').find('input[data-ss_user]');

        body.one('click.modify_entities', 'a[href="#submit-entities"]', function () {
            body.one('click.modify_entities_confirm', '#general-dialog a[href="#submit"]', function () {
                // TODO: confirmation dialog
                $.ajax({
                    url: Routing.generate('save_group'),
                    type: 'POST',
                    dataType: 'text',
                    data: {
                        groupId: groupId,
                        packId: rowId != null ? rowId : null,
                        users: field.data('ss_user').map(function (g) {
                            return {id: g['value'].substr(8), remove: g['remove']};
                        })
                    },
                    success: function (data) {
                        tab.find('.squiggle').stop().remove();
                        // copy rows and select
                        var selected = (/pack-id-([0-9]+)(\s|$)/ig).exec(tab.find('> .pack-row.selected').attr('class') || '');
                        loadContent.apply(tab, [data, ['pack']]);
                        if (selected) {
                            tab.find('> .pack-row.pack-id-' + selected[1]).addClass('selected');
                        }
                    },
                    error: function () {
                        tab.find('.squiggle').stop().remove();
                    }
                });
            });
        });
    });

});
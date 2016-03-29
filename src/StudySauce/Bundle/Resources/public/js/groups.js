
$(document).ready(function () {

    var body = $('body'),
        autoSaveTimeout = null;

    function groupsFunc() {
        var tab = $(this).parents('.results:visible');
        var groupRow = tab.find('.ss_group-row.edit');
        if(groupRow.find('.name input').val().trim() == '') {
            tab.find('.highlighted-link').removeClass('valid').addClass('invalid');
        }
        else {
            tab.find('.highlighted-link').removeClass('invalid').addClass('valid');
        }

        // save at most every 2 seconds, don't autosave from admin lists
        if (autoSaveTimeout === null && $('.panel-pane[id^="groups-"]:visible').length > 0) {
            autoSaveTimeout = setTimeout(function () {
                autoSave.apply(tab);
            }, 2000);
        }

    }

    body.on('click', '[id^="groups-"] .ss_group-row .packs a[href="/packs"]', function () {
        body.one('show', '#packs', function () {
            $(this).find('a[href="#add-pack"]').first().trigger('click');
        });
    });

    function autoSave(close) {
        autoSaveTimeout = null;
        var tab = $(this);
        var row = tab.find('.ss_group-row.edit:not(.template)');
        if(tab.find('.highlighted-link').is('.invalid'))
            return;
        loadingAnimation(tab.find('a[href="#save-ss_group"]'));
        tab.find('.highlighted-link').removeClass('valid').addClass('invalid');

        $.ajax({
            url: Routing.generate('save_group'),
            type: 'POST',
            dataType: close ? 'text' : 'json',
            data: {
                groupName: row.find('input[name="name"]').val().trim(),
                description: row.find('textarea[name="description"]').val().trim(),
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

    body.on('click', '.group-list .ss_group-row a[href="#edit-group"]', function () {
        var results = $(this).parents('.results');
        var row = $(this).parents('.ss_group-row');
        var groupId = (/ss_group-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1];
        window.activateMenu(Routing.generate('groups_edit', {group: groupId}));
        row.removeClass('edit').addClass('read-only');
    });

    body.on('click', '[id^="groups-"] .ss_group-row.edit a[href^="#cancel-"], [id^="groups-"] .ss_group-row ~ .highlighted-link a[href^="#cancel"]', function (evt) {
        evt.preventDefault();
        var results = $(this).parents('.results'),
            row = $(this).parents('.ss_group-row');
        results.find('.search .input').removeClass('read-only');
        row.removeClass('read-only').addClass('edit');
        window.activateMenu(Routing.generate('groups'));
    });

    body.on('click', '[id^="groups-"] .ss_group-row a[href="#upload-image"]', function () {
        var row = $(this).parents('.ss_group-row');
        body.one('click.upload', 'a[href="#submit-upload"]', function () {
            row.find('.id img').attr('src', $('#upload-file').find('img').attr('src')).removeClass('.default');
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
        body.one('click.publish', '#pack-publish a[href="#submit-publish"]', function () {


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
                tab.find('.highlighted-link').removeClass('valid').addClass('invalid');
                loadingAnimation(tab.find('a[href="#save-pack"]'));

                $.ajax({
                    url: Routing.generate('save_group'),
                    type: 'POST',
                    dataType: 'text',
                    data: {
                        groupId: groupId,
                        packId: id != null ? id : null,
                        groups: [{id: groupId, remove: false}],
                        publish: publish
                    },
                    success: function (data) {
                        // copy rows and select
                        var content = $(data).filter('.results'),
                            selected = (/pack-id-([0-9]+)(\s|$)/ig).exec(tab.find('> .pack-row.selected').attr('class') || '');
                        tab.find('> .pack-row:not(.template), > .pack-row:not(.template) + .expandable').remove();
                        var keepRows = tab.find('> .pack-row').map(function () {
                            var rowId = (new RegExp('pack-id-([0-9]*)(\\s|$)', 'i')).exec($(this).attr('class'))[1];
                            return '.pack-id-' + rowId;
                        }).toArray().join(',');
                        content.find('> .pack-row:not(.template), > .pack-row:not(.template) + .expandable').not(keepRows).insertBefore(tab.find('.pack-row.template').first());
                        tab.find('.paginate.pack .page-total').text(content.find('.paginate.pack .page-total').text());
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
            body.off('click.publish');
        }, 100);
    });

    body.on('click', '[id^="groups-"] label:has(input[data-ss_user]) ~ a[href="#add-entity"]', function () {
        var tab = $(this).parents('.results'),
            row = $(this).parents('.expandable').prev('.pack-row'),
            rowId = (/pack-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1],
            groupId = (/ss_group-id-([0-9]+)(\s|$)/ig).exec(tab.find('.ss_group-row.edit').first().attr('class'))[1],
            field = $(this).siblings('label:has(input[data-ss_user])').find('input[data-ss_user]');
        body.one('click.modify_entities', 'a[href="#submit-entities"]', function () {
            // TODO: confirmation dialog
            $.ajax({
                url: Routing.generate('save_group'),
                type: 'POST',
                dataType: 'text',
                data: {
                    groupId: groupId,
                    packId: rowId != null ? rowId : null,
                    users: field.data('ss_user').map(function (g) {return {id: g['value'], remove: g['remove']};})
                },
                success: function (data) {
                    // copy rows and select
                    var content = $(data).filter('.results'),
                        selected = (/pack-id-([0-9]+)(\s|$)/ig).exec(tab.find('> .pack-row.selected').attr('class') || '');
                    tab.find('> .pack-row:not(.template), > .pack-row:not(.template) + .expandable').remove();
                    var keepRows = tab.find('> .pack-row').map(function () {
                        var rowId = (new RegExp('pack-id-([0-9]*)(\\s|$)', 'i')).exec($(this).attr('class'))[1];
                        return '.pack-id-' + rowId;
                    }).toArray().join(',');
                    content.find('> .pack-row:not(.template), > .pack-row:not(.template) + .expandable').not(keepRows).insertBefore(tab.find('.pack-row.template').first());
                    tab.find('.paginate.pack .page-total').text(content.find('.paginate.pack .page-total').text());
                    if(selected) {
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
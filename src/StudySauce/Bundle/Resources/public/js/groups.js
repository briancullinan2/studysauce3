
$(document).ready(function () {

    var body = $('body'),
        autoSaveTimeout = 0;

    function groupsFunc(evt) {
        var tab = $(this).parents('.results:visible');
        var groupRow = tab.find('.ss_group-row');
        if(groupRow.length > 0 && groupRow.find('.name input').val().trim() == '') {
            tab.find('.highlighted-link a[href^="#save-"]').attr('disabled', 'disabled');
        }
        else {
            tab.find('.highlighted-link a[href^="#save-"]').removeAttr('disabled');
        }

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

    body.on('click', '[id^="groups"] a[href="#add-new-ss_group"]', function (evt) {
        evt.preventDefault();
        if($(this).parents('.panel-pane').is('[id^="groups-"]')) {
            var row = $(this).parents('.results').find('.ss_group-row:not(.template)');
            var groupId = ((/ss_group-id-([0-9]*)(\s|$)/ig).exec(row.attr('class')) || [])[1];
            body.one('show', '#groups-group0', function () {
                $(this).find('.ss_group-row:not(.template) .parent select').val(groupId);
                if(row.find('.id img:not(.default)').length > 0) {
                    $(this).find('.ss_group-row:not(.template) .id img').attr('src', row.find('.id img').attr('src')).removeClass('default');
                }
            });
        }
        window.activateMenu(Routing.generate('groups_new'));
    });

    body.on('click', '[id^="groups-"] a[href^="/packs/0"]', function () {
        var row = $(this).parents('.results').find('.ss_group-row:not(.template)');
        var groupId = ((/ss_group-id-([0-9]*)(\s|$)/ig).exec(row.attr('class')) || [])[1];
        body.one('show', '#packs-pack0', function () {
            $(this).find('.pack-row:not(.template) .groups label > input').val('ss_group-' + groupId);
            if($(this).find('.pack-row .groups label > input.selectized').length > 0) {
                var userCount = 0;
                row.parents('pack-row').each(function () {
                    userCount += parseInt($(this).find('.count label:first-of-type span').text());
                });
                var option = {
                    remove:false,
                    table: 'ss_group',
                    value: 'ss_group-' + groupId,
                    text: row.find('.name input').val(),
                    0: '(' + userCount + ' users)'
                };
                $(this).find('.pack-row .groups label > input.selectized')[0].selectize.addOption(option);
                $(this).find('.pack-row .groups label > input.selectized').data('entities', ['ss_group-' + groupId]).data('ss_group', [option])[0].selectize.setValue('ss_group-' + groupId);
            }
            if(row.find('.id img:not(.default)').length > 0) {
                $(this).find('.pack-row:not(.template) .id img').attr('src', row.find('.id img').attr('src')).removeClass('default');
            }
        });
    });

    function autoSave(close) {
        autoSaveTimeout = null;
        var tab = $(this);
        var row = tab.find('.ss_group-row.valid:not(.template)');
        var groupId = ((/ss_group-id-([0-9]*)(\s|$)/ig).exec(row.attr('class')) || [])[1];
        if(tab.find('.highlighted-link a[href^="#save-"]').is('[disabled]'))
            return;
        loadingAnimation(tab.find('a[href^="#save-"]'));
        tab.find('.highlighted-link a[href^="#save-"]').attr('disabled', 'disabled');

        $.ajax({
            url: Routing.generate('save_group'),
            type: 'POST',
            dataType: close ? 'text' : 'json',
            data: {
                logo: row.find('.id img:not(.default)').attr('src'),
                groupName: row.find('input[name="name"]').val().trim(),
                invite: row.find('.invite input').val().trim(),
                roles: row.find('input[name="roles"]:checked').map(function () {return $(this).val();}).toArray().join(','),
                groupId: groupId,
                parent: row.find('select[name="parent"]').val(),
                requestKey: getDataRequest.apply(tab).requestKey
            },
            success: function (data) {
                tab.find('.squiggle').stop().remove();

                // rename tab if working with a new group
                // TODO: generalize this with some sort of data attribute or class, same as in packs
                if (tab.closest('.panel-pane').is('#groups-group0')) {
                    var id = close
                        ? (/ss_group-id-([0-9]+)(\s|$)/i).exec($(data).find('.ss_group-row:not(.template)').first().attr('class'))[1]
                        : data.ss_group[0].id;
                    tab.closest('.panel-pane').attr('id', 'groups-group' + id);
                    window.activateMenu(Routing.generate('groups_edit', {group: id}));
                }

                if(close) {
                }

                //tab.find('.ss_group-row .invites input').data('entities', data.ss_group[0].invites[0].code);
                loadContent.apply(tab, [data]);
            },
            error: function () {
                tab.find('.squiggle').stop().remove();
            }
        });
    }

    // TODO: refresh all intermediate group panels also
    var shouldRefresh = false;
    body.on('resulted', '[id^="groups-"] .results', function () {
        shouldRefresh = true;
        var loaded = body.find('#groups');
        var id = $(this).find('.ss_group-row .parent select').val();
        if($(this).parents('.panel-pane').attr('id') != 'groups-group' + id) {
            loaded = loaded.add(body.find('#' + 'groups-group' + id));
        }
        loaded.off('show.resulted').on('show.resulted', function () {
            loadResults.apply($(this).find('.results'));
        });
    });

    body.on('resulted', '[id^="packs-"] .results', function () {
        var groups = $(this).find('.pack-row .groups input[data-entities]').data('entities');
        var loaded = body.find('#groups');
        for(var g in groups) {
            if(groups.hasOwnProperty(g) && groups[g].substr(0, 9) == 'ss_group-') {
                loaded = loaded.add(body.find('#' + 'groups-group' + groups[g].substr(9)));
            }
        }
        loaded.off('show.resulted').on('show.resulted', function () {
            loadResults.apply($(this).find('.results'));
        });
    });

    body.on('show', '.panel-pane[id^="groups-"]', function () {
        autoSaveTimeout = 0;
        groupsFunc.apply($(this).find('.ss_group-row:not(.template)'));
        autoSaveTimeout = null;
    });

    body.on('click', '[id^="groups-"] a[href="#save-ss_group"], [id^="groups-"] [value="#save-ss_group"]', function (evt) {
        evt.preventDefault();
        var tab = $(this).parents('.results:visible');
        if (autoSaveTimeout != null) {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = null;
        }
        tab.find('[class*="-row"].edit').removeClass('edit remove-confirm').addClass('read-only');
        autoSave.apply(tab, [true]);
    });

    body.on('change keyup keydown', '[id^="groups-"] .ss_group-row input, [id^="groups-"] .ss_group-row select, [id^="groups-"] .ss_group-row textarea', groupsFunc);

    body.on('click', '.group-list .ss_group-row', function (evt) {
        if(!$(evt.target).is('a, .ss_group-row > .packList') && $(evt.target).parents('.ss_group-row > .packList').length == 0)
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
        $(this).parents('.results').find('.ss_group-row').removeClass('edit').addClass('read-only');
        window.activateMenu(Routing.generate('groups'));
    });

    body.on('click', '[id^="groups-"] .ss_group-row a[href="#upload-image"]', function () {
        var row = $(this).parents('.ss_group-row');
        body.one('click.upload', 'a[href="#submit-upload"]', function () {
            row.find('.id img').attr('src', $('#upload-file').find('img').attr('src')).removeClass('default');
            groupsFunc.apply(row);
        });
    });

    var isSettingSelectize = false;
    body.on('change', '[id^="groups-"] .highlighted-link .packs input.selectized', function () {
        if(isSettingSelectize) {
            return;
        }
        isSettingSelectize = true;
        var that = $(this),
            tab = that.parents('.results'),
            groupId = (/ss_group-id-([0-9]+)(\s|$)/ig).exec(tab.find('.ss_group-row:not(.template)').attr('class'))[1],
            id = that.val(),
            packName = that[0].selectize.options[id].text;

        showPublishDialog(packName, null)(function (publish) {
            that[0].selectize.setValue('', true);
            isSettingSelectize = false;

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
                    publish: publish,
                    requestKey: getDataRequest.apply(tab).requestKey
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

    body.on('change', '[id^="groups-"] .pack-row .members input.selectized', function () {
        var field = $(this),
            tab = field.parents('.results'),
            row = field.parents('.pack-row'),
            rowId = (/pack-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1],
            groupId = (/ss_group-id-([0-9]+)(\s|$)/ig).exec(tab.find('.ss_group-row:not(.template)').first().attr('class'))[1];

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
                        return {id: g.value.substr(8), remove: g['remove']};
                    }),
                    requestKey: getDataRequest.apply(tab).requestKey
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
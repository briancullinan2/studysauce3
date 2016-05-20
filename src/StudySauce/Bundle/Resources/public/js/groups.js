
$(document).ready(function () {

    var body = $('body');

    // TODO: move this to template system and use validation
    function groupsFunc() {
        var tab = getTab.apply(this);
        var groupRow = $(this).closest('.ss_group-row').add($(this).find('.ss_group-row'));
        if(groupRow.length > 0 && groupRow.find('.name input').val().trim() == '') {
            groupRow.removeClass('valid empty').addClass('invalid');
            tab.find('.highlighted-link a[href^="#save-"]').attr('disabled', 'disabled');
        }
        else {
            groupRow.removeClass('invalid empty').addClass('valid');
            tab.find('.highlighted-link a[href^="#save-"]').removeAttr('disabled');
        }

        // save at most every 2 seconds, don't autosave from admin lists
        //if (autoSaveTimeout === null && $('.panel-pane[id^="groups-"]:visible').length > 0) {
        //    autoSaveTimeout = setTimeout(function () {
        //        autoSave.apply(tab);
        //    }, 2000);
        //}
    }

    body.on('validate', '[id^="groups-"]', groupsFunc);

    // TODO: do this through some sort of view template default option in the URL
    body.on('click', '[id^="groups-"] a[href^="/groups/0"]', function () {
        var row = $(this).parents('.panel-pane').find('.group-edit .ss_group-row:not(.template)');
        var groupId = getTabId.apply(row);
        body.one('show', '#groups-group0', function () {
            $(this).find('.ss_group-row:not(.template) .parent select').val(groupId);
            if(row.find('.id img:not(.default)').length > 0) {
                $(this).find('.ss_group-row:not(.template) .id img').attr('src', row.find('.id img').attr('src')).removeClass('default');
            }
        });
    });

    // TODO: use template system to update values
    body.on('click', '[id^="groups-"] a[href^="/packs/0"]', function () {
        var row = $(this).parents('.results').find('.ss_group-row:not(.template)');
        var groupId = getTabId.apply(this);
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
                var groupsField = $(this).find('.pack-row .groups label > input.selectized');
                groupsField.data('confirm', false);
                groupsField[0].selectize.addOption(option);
                groupsField.data('entities', ['ss_group-' + groupId]).data('ss_group', [option]);
                groupsField[0].selectize.setValue('ss_group-' + groupId);
                groupsField.data('confirm', true);
            }
            if(row.find('.id img:not(.default)').length > 0) {
                $(this).find('.pack-row:not(.template) .id img').attr('src', row.find('.id img').attr('src')).removeClass('default');
            }
        });
    });

    // TODO: refresh all intermediate group panels also
    body.on('resulted', '[id^="groups-"] .results', function (evt) {
        var results = $(this);
        var tab = results.closest('.panel-pane');
        if (tab.is('#groups-group0')) {
            window.views.render.apply(tab, ['groups', {entity: evt['results']['results']['ss_group'][0]}]);
            loadResults.apply(tab.find('.results').not(results));
            var id = getTabId.apply(results);
            window.activateMenu(Routing.generate('groups_edit', {group: id}));
        }

        var loaded = body.find('#groups');
        var parentId = tab.find('.ss_group-row .parent select').val();
        if(tab.attr('id') != 'groups-group' + parentId) {
            loaded = loaded.add(body.find('#' + 'groups-group' + parentId));
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

    // TODO: generalize some of this and move to results?

    var isSettingSelectize = false;
    body.on('change', '.list-packs .highlighted-link.pack input.selectized', function () {
        if(isSettingSelectize) {
            return;
        }
        isSettingSelectize = true;
        var that = $(this),
            id = that.val(),
            packName = that[0].selectize.options[id].text;

        that[0].selectize.setValue('', true);

        showPublishDialog.apply(that, [id, packName, null]);

        isSettingSelectize = false;
    });


});
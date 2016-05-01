
$(document).ready(function () {

    var body = $('body'),
        autoSaveTimeout = 0;

    function groupsFunc(evt) {
        var tab = $(this).closest('.results:visible');
        var groupRow = tab.find('.ss_group-row').addClass('changed');
        if(groupRow.length > 0 && groupRow.find('.name input').val().trim() == '') {
            groupRow.removeClass('valid empty').addClass('invalid');
            tab.find('.highlighted-link a[href^="#save-"]').attr('disabled', 'disabled');
        }
        else {
            groupRow.removeClass('invalid empty').addClass('valid');
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

    body.on('click', '[id^="groups-"] a[href^="/groups/0"]', function (evt) {
        var row = $(this).parents('.panel-pane').find('.group-edit .ss_group-row:not(.template)');
        var groupId = getTabId.apply(row);
        body.one('show', '#groups-group0', function () {
            $(this).find('.ss_group-row:not(.template) .parent select').val(groupId);
            if(row.find('.id img:not(.default)').length > 0) {
                $(this).find('.ss_group-row:not(.template) .id img').attr('src', row.find('.id img').attr('src')).removeClass('default');
            }
        });
    });

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

    function autoSave(close) {
        autoSaveTimeout = null;
        var tab = $(this);
        var row = tab.find('.ss_group-row.valid:not(.template)');
        var groupId = getTabId.apply(tab);
        if(tab.find('.highlighted-link a[href^="#save-"]').is('[disabled]'))
            return;
        loadingAnimation(tab.find('a[href^="#save-"]'));
        tab.find('.highlighted-link a[href^="#save-"]').attr('disabled', 'disabled');

        var groupData = $.extend({groupId: groupId, requestKey: getDataRequest.apply(tab).requestKey},
            gatherFields.apply(row, [['upload', 'name', 'invite', 'parent']]));

        $.ajax({
            url: Routing.generate('save_group'),
            type: 'POST',
            dataType: 'text',
            data: groupData,
            success: function (data) {
                tab.find('.squiggle').stop().remove();

                loadContent.apply(tab, [data]);

                // rename tab if working with a new group
                // TODO: generalize this with some sort of data attribute or class, same as in packs
                if (tab.closest('.panel-pane').is('#groups-group0')) {
                    var id = getTabId.apply(tab);
                    tab.closest('.panel-pane').attr('id', 'groups-group' + id);
                    window.activateMenu(Routing.generate('groups_edit', {group: id}));
                    // TODO: make this a part of some sort of WPF style property updating notification
                    // update id in results
                    var results = tab.closest('.panel-pane').find('.group-list .results').data('request');
                    results['parent-ss_group-id'] = id;
                    results['requestKey'] = null;
                }
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
        groupsFunc.apply($(this).find('.group-edit .ss_group-row:not(.template)'));
        autoSaveTimeout = null;
    });

    // TODO: generalize some of this and move to results?
    body.on('click', '[id^="groups-"] a[href="#save-ss_group"], [id^="groups-"] [value="#save-ss_group"]', function (evt) {
        evt.preventDefault();
        var tab = $(this).parents('.results:visible');
        if (autoSaveTimeout != null) {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = null;
        }
        tab.find('[class*="-row"].edit').removeClass('edit remove-confirm').addClass('read-only');
        groupsFunc.apply(tab);
        autoSave.apply(tab, [true]);
    });

    body.on('change keyup keydown', '.group-edit .ss_group-row input, .group-edit .ss_group-row select, .group-edit .ss_group-row textarea', groupsFunc);

    var isSettingSelectize = false;
    body.on('change', '[id^="groups-"] .highlighted-link .packs input.selectized', function () {
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

    $(window).on('beforeunload', function (evt) {
        //if($('.panel-pane[id^="packs-"] .pack-edit .pack-row.changed:not(.template)').length > 0)
        if($('.panel-pane[id^="groups-"]:visible').find('.group-edit .ss_group-row.edit.changed:not(.template):not(.removed)').length > 0) {
            evt.preventDefault();
            return "You have unsaved changes!  Please don't go!";
        }
    });

    body.on('hide', '.panel-pane[id^="groups-"]', function () {
        var row = $(this).find('.results [class*="-row"].edit');
        row.removeClass('edit remove-confirm').addClass('read-only');
    });


});

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
            if(row.find('[class*="id"] img:not(.default)').length > 0) {
                var src = row.find('[class*="id"] img').attr('src');
                $(this).find('.ss_group-row:not(.template) [class*="id"] input').val(src);
                $(this).find('.ss_group-row:not(.template) [class*="id"] img')
                    .attr('src', src).removeClass('default');
            }
        });
    });

    // TODO: use template system to update values
    body.on('click', '[id^="groups-"] [data-target="#create-entity"]', function () {
        var results = getTab.apply(this);
        var request = results.data('request');
        var row = results.find('.ss_group-row:not(.template)');
        var groupId = getTabId.apply(this);
        var group = gatherFields.apply(row, [getAllFieldNames({ss_group: request['tables']['ss_group']})]);
        body.one('click.create_new', '#create-entity a[href^="/packs/0"]', function () {
            body.one('show', '#packs-pack0', function () {
                // TODO: add a sub groups to UI
                // render new group row and add to group list behind save
                var toTemplate = $(this).find('.group-list .results');
                var toRequest = toTemplate.data('request');
                group['table'] = 'ss_group';
                group['id'] = groupId;
                var newGroupRow = window.views.render.apply(toTemplate, ['row', {
                    entity: applyEntityObj(group),
                    table: 'ss_group',
                    tables: toRequest['tables'],
                    results: {allGroups: toTemplate.data('allGroups')},
                    request: toRequest}]);
                $(newGroupRow).insertAfter(toTemplate.find('header.pack'));
                // add group to path
                getTab.apply(this).closest('form').attr('action', Routing.generate('packs_create', {groups: {id: groupId}}));
                if(row.find('[class*="id"] img:not(.default)').length > 0) {
                    var src = row.find('[class*="id"] img').attr('src');
                    $(this).find('.pack-row:not(.template) [class*="id"] input').val(src);
                    $(this).find('.pack-row:not(.template) [class*="id"] img')
                        .attr('src', src).removeClass('default');
                }
            });
        });
    });

    // TODO: refresh all intermediate group panels also
    body.on('resulted.saved', '[id^="groups-"] .results', function (evt) {
        var results = $(this);
        var tab = results.closest('.panel-pane');
        if (tab.is('#groups-group0')) {
            window.views.render.apply(tab, ['groups', {entity: evt['results']['results']['ss_group'][0]}]);
            results.data('request', $.extend(results.data('request'), {requestKey: evt['results'].requestKey})); // replace key because template clears it
            var id = getTabId.apply(results);
            window.activateMenu(Routing.generate('groups_edit', {group: id}));
            loadResults.apply(tab.find('.results').not(results));
        }
        var loaded = body.find('[id^="groups"]:not(#groups-group0)');
        // refresh parent group incase of name change
        //var parentId = tab.find('.ss_group-row .parent select').val();
        //if(tab.attr('id') != 'groups-group' + parentId) {
        //    loaded = loaded.add(body.find('#' + 'groups-group' + parentId));
        //}
        loaded.off('show.resulted').one('show.resulted', function () {
            loadResults.apply($(this).find('.results'));
        });
    });

    body.on('resulted.saved', '[id^="packs-"] .results', function () {
        var loaded = body.find('[id^="groups"]:not(#groups-group0)');
        loaded.off('show.resulted').one('show.resulted', function () {
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
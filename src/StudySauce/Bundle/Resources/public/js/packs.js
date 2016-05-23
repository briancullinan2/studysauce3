
$(document).ready(function () {

    var body = $('body');

    key('⌘+v, ctrl+v, command+v', function () {
        var tab = $('[id*="packs-"]:visible');
        if(tab.find('input:focus, select:focus, textarea:focus').parents('.results [class*="-row"]').is('.empty')) {
            // get the clipboard text
            var text = $('<textarea></textarea>')
                .css('position', 'fixed')
                .css('top', 0)
                .css('left', -10000)
                .css('opacity', '0')
                .css('height', 1)
                .css('width', 1).appendTo(tab).focus();

            setTimeout(function () {
                var clipText = text.val();
                text.remove();
                rowImport.apply(tab, [clipText]);
            }, 100);
        }
    });

    function rowImport(clipText) {
        var results = $(this).find('.card-list .results'),
            request = results.data('request'),
            last = null;

        // split into rows
        var clipRows = Papa.parse(clipText).data;

        var newRows = $([]);
        // write out in a table
        for (var i=0; i<clipRows.length; i++) {
            // skip partial rows
            if(clipRows[i].length < 2)
                continue;

            var newRow = {};
            // set card type
            newRow['responseType'] = '';
            if(clipRows[i].length > 2) {
                if (clipRows[i][0].match(/multiple/ig) != null) {
                    newRow['responseType'] = 'mc'
                }
                else if (clipRows[i][0].match(/false/ig) != null) {
                    newRow['responseType'] = 'tf'
                }
                else if (clipRows[i][0].match(/blank|short/ig) != null) {
                    newRow['responseType'] = 'sa';
                }
            }

            // set correct answers
            newRow['answers'] = clipRows[i].splice(3).filter(function (x) {return x.trim() != '';}).join("\n");

            // merge this with row template and just pass a model containing type, answers, correct, content
            if(clipRows[i].length == 2) {
                newRow['content'] = clipRows[i][0];
                newRow['correct'] = clipRows[i][1];
            }
            else {
                if(newRow['responseType'] == 'tf') {
                    newRow['correct'] = (clipRows[i][2].match(/true|false/i) || [''])[0].toLowerCase() == true ? 'true' : 'false';
                }
                else {
                    newRow['correct'] = clipRows[i][2];
                }
                newRow['content'] = clipRows[i][1];
            }

            newRow['table'] = 'card';
            var rowHtml = $(window.views.render('row-card', {card: applyEntityObj(newRow), table: 'card', tableId: 'card', tables: request.tables, request: request}))
                .removeClass('read-only').addClass('edit');

            // list under currently focused row
            if(last != null) {
                last = rowHtml.insertAfter(last.last());
            }
            else {
                last = rowHtml.insertAfter(results.find('> header.card'));
            }
            newRows = newRows.add(last);

        }

        // remove empties
        results.find('.card-row.empty, .card-row.empty + .expandable:not([class*="-row"])').remove();

        resizeTextAreas.apply(newRows);
        newRows.addClass('changed');
        results.trigger('validate');
        if(results.find('.card-row:visible').length == 0) {
            for(var n = 0; n < 5; n++) {
                addResultRow.apply(results, ['card']);
            }
        }
    }

    function resizeTextAreas() {
        var row = $(this).closest('.card-row:not(.template):not(.removed)');
        row.each(function () {
            var row = $(this);
            var that = row.find('textarea:visible');
            that.each(function () {
                var that = $(this);
                that.css('height', '');
                //if (row.is('.edit')) {
                    that.height(that[0].scrollHeight - 4);
                    row.find('.correct').addClass('editing');
                //}
                //else {
                //    row.find('.correct').removeClass('editing');
                //    if (row.find('.correct .radios :checked').length > 0) {
                //        row.find('.correct textarea, .correct .radios').scrollTop(row.find('.correct .radios input').index(row.find('.correct .radios :checked')) * 22 - 2);
                //    }
                //}
            });
        });
    }
    body.on('change keyup keypress keydown focus blur', '[id^="packs-"] .card-row textarea', resizeTextAreas);

    body.on('change keyup', '[id^="packs-"] .card-row .type-mc.correct textarea, [id^="packs-"] .card-row .correct .radios input', function (evt) {
        var row = $(this).parents('.card-row');
        var that = row.find('.type-mc.correct textarea');

        // get current line
        var origName = $(evt.target).is('.correct .radios input') ? $(evt.target).attr('name') : row.find('.correct .radios input').attr('name');
        var orig = $(evt.target).is('.correct .radios input') ? $(evt.target).val() : row.find('.correct .radios input:checked').val();
        var line = row.find('.correct .radios input').index(row.find('.correct .radios input').filter(function () {return $(this).val() == orig;}));
        var existing = row.find('.correct .radios label');
        var answers = that.val().split(/\n/ig) || [];
        var newItems = [];
        var orderChanged = false;
        for(var i in answers) {
            if(!answers.hasOwnProperty(i))
                continue;
            var newItem;
            if ((newItem = $(existing.find('input').filter(function () {
                    return $(this).val() == answers[i];}).map(function () {
                    return $(this).parents('label')[0];})).not(newItems).first()).length == 0) {
                newItem = $('<label class="radio"><input type="radio" name="' + origName + '" value="' + answers[i] + '"><i></i><span>' + answers[i] + '</span></label>')
                    .appendTo(row.find('.correct .radios'));
            }
            else {
                if (i != newItem.parent().index(newItem)) {
                    orderChanged = true;
                    newItem.detach().appendTo(row.find('.correct .radios'));
                }
            }
            newItems = $.merge(newItems, [newItem[0]]);
        }
        if (existing.not(newItems).length > 0) {
            existing.not(newItems).remove();
        }
        var newVal = row.find('.correct .radios input').filter(function () {return $(this).val() == orig;});
        if(newVal.length == 0 || !orderChanged) {
            newVal = row.find('.correct .radios input').eq(line);
        }
        newVal.prop('checked', true);
    });

    body.on('click', '[id^="packs-"] .preview-play .play', function () {
        var player = $('#jquery_jplayer');
        player.jPlayer("setMedia", {
            mp3: $(this).attr('href')
        });
        player.jPlayer("play");
    });

    // TODO: merge with row-card.html.php or cell-id-card.html.php
    function setTypeClass() {
        var row = $(this).closest('.card-row');
        var results = row.parents('.results');
        var request = results.data('request');
        var data = gatherFields.apply(row, [getAllFieldNames(request.tables['card'])]);
        data['table'] = 'card';
        data['id'] = getRowId.apply(row);
        window.views.render.apply(results, ['row-card', {card: applyEntityObj(data), request: request, tables: request.tables, table: 'card'}])
    }

    body.on('change', '[id*="packs-"] .card-row select[name="responseType"]', setTypeClass);

    var autoSaveTimeout = 0;

    function updatePreview() {
        if($(this).length == 0) {
            return;
        }
        var row = $(this).add($(this).next('.expandable'));
        var results = row.parents('.results');
        var request = results.data('request');
        var data = gatherFields.apply(row, [getAllFieldNames(request.tables['card'])]);
        window.views.render.apply(row, ['cell_preview_card', {card: applyEntityObj($.extend({table: 'card'}, data))}]);
        centerize.apply(row.find('.centerized:visible'));
        $('#jquery_jplayer').jPlayer('option', 'cssSelectorAncestor', '.preview-play:visible');
    }

    body.on('selected', '[id^="packs-"] .card-row', updatePreview);

    body.on('click', '[id^="packs-"] .card-row [href="#remove-confirm-card"]', packsFunc);

    // TODO: generalize this

    body.on('validate', '[id^="packs-"]', packsFunc);

    // TODO: merge this with template then run changes through template on server and check each cell for invalid class to validate before saving to database
    function packsFunc() {

        var tab = getTab.apply(this);
        var packRows = tab.find('.pack-row:not(.template),.pack-row:not(.valid):not(.template)'); // <-- this is critical for autosave to work
        var cardRows = $(this).closest('.card-row:not(.removed)').add($(this).find('.card-row:not(.removed)'));

        for(var c  = 0; c < cardRows.length; c++) {
            var row = $(cardRows[c]);
            var data = gatherFields.apply(row, [['responseType', 'content', 'answers', 'correct']]);

            if(data.content == '' && (typeof data.correct == 'undefined' || data.correct == '') && (typeof data.answers == 'undefined' || data.answers == '')) {
                row.removeClass('invalid').addClass('empty valid');
            }
            else if (data.content == '' || data.correct == '' || data.answers == '') {
                row.removeClass('valid empty').addClass('invalid');
                if (data.content == '') {
                    row.find('.content').addClass('invalid');
                }
                else {
                    row.find('.content').removeClass('invalid');
                }
                if (data.answers == '') {
                    row.find('.answers').addClass('invalid');
                }
                else {
                    row.find('.answers').removeClass('invalid');
                }
                if (data.correct == '') {
                    row.find('.correct').addClass('invalid');
                }
                else {
                    row.find('.correct').removeClass('invalid');
                }
            }
            else {
                row.removeClass('invalid empty').addClass('valid');
            }

            // update line number
            var rowIndex = '' + (cardRows.index(row) + 1);
            if(row.find('.input.type > span').text() != rowIndex) {
                row.find('.input.type > span').text(rowIndex);
            }
        }
        for(var p = 0; p < packRows.length; p++) {
            var row2 = $(packRows[p]);
            if(row2.find('.name input').val().trim() != '') {
                row2.removeClass('invalid empty').addClass('valid').find('.name').removeClass('invalid');
            }
            else {
                row2.removeClass('valid empty').addClass('invalid').find('.name').addClass('invalid');
            }
        }

        var hasError = false;
        if(tab.find('.card-row.invalid:not(.empty):not(.removed):not(.template)').length > 0) {
            tab.addClass('has-card-error');
            hasError = true;
        }
        else {
            tab.removeClass('has-card-error')
        }

        if(tab.find('.pack-row.valid:not(.empty):not(.removed):not(.template)').length > 0) {
            tab.removeClass('has-pack-error').find('.highlighted-link a[href^="#save-"]').removeAttr('disabled');
        }
        else {
            hasError = true;
            tab.addClass('has-pack-error').find('.highlighted-link a[href^="#save-"]').attr('disabled', 'disabled');
        }

        if(hasError) {
            tab.addClass('has-error').find('.pack-row:not(.template):not(.removed) .status').addClass('read-only');
        }
        else {
            tab.removeClass('has-error').find('.pack-row:not(.template):not(.removed) .status').removeClass('read-only');
        }

        if(cardRows.length > 0) {
            updatePreview.apply(cardRows.filter('.selected').first());
        }
        // save at most every 2 seconds, don't autosave from admin lists
        if (autoSaveTimeout === null && $('.panel-pane[id^="packs-"]:visible').length > 0) {
            autoSaveTimeout = setTimeout(function () {
                standardSave.apply(tab, [{}]);
                autoSaveTimeout = null;
            }, 2000);
        }
    }

    body.on('resulted.saved', '[id^="packs-"] .results', function (evt) {
        var results = $(this);
        var tab = results.closest('.panel-pane');
        autoSaveTimeout = null;
        if (tab.is('#packs-pack0') && typeof evt['results']['results']['pack'][0] != 'undefined') {
            window.views.render.apply(tab, ['packs', {entity: evt['results']['results']['pack'][0]}]);
            results.data('request', $.extend({requestKey: evt['results'].requestKey}, results.data('request')));
            var id = getTabId.apply(results);
            window.activateMenu(Routing.generate('packs_edit', {pack: id}));
        }
        loadResults.apply(tab.find('.results').not(results));
        var loaded = body.find('[id^="groups"]:not(#groups-group0)');
        loaded.off('show.resulted').one('show.resulted', function () {
            loadResults.apply($(this).find('.results'));
        });
    });

    // TODO: shouldn't need to do this anymore
    function setupPackEditor() {
        autoSaveTimeout = 0;
        var tab = $(this).closest('.panel-pane');
        var cardRows = tab.find('.card-row:not(.removed):not(.template)');

        setTimeout(function () {
            for(var i = 0; i < cardRows.length; i++) {
                var row = $(cardRows[i]);
                if (row.find('.correct .radios :checked').length > 0) {
                    row.find('.correct textarea, .correct .radios').scrollTop(row.find('.correct .radios input').index(row.find('.correct .radios :checked')) * 22 - 2);
                }

                // update line number
                var rowIndex = '' + (i + 1);
                if(row.find('.input.type > span').text() != rowIndex) {
                    row.find('.input.type > span').text(rowIndex);
                }
            }
        }, 50);

        var select = tab.find('.pack-row:not(.template) .status select');
        select.data('oldValue', select.val());
        resizeTextAreas.apply(tab.find('.card-row:not(.template):not(.removed)'));
        tab.find('.card-row:not(.template):not(.removed)').first().filter(':not(.selected)').trigger('mousedown');

        autoSaveTimeout = null;
    }

    body.on('click', '.panel-pane[id^="packs-"] a[href="#edit-pack"]', setupPackEditor);
    body.on('show', '.panel-pane[id^="packs-"]', setupPackEditor);

    // TODO: generalize and move this to dashboard using some sort of property binding API, data-target, data-toggle for selects toggles class of closest matching target?
    // TODO: data-toggle="modal" on option brings up dialog for confirmation

    body.on('change change.confirm', '[id^="packs-"] .status select', function (evt) {
        var row = $(this).parents('.pack-row');
        var select = $(this);

        if($(this).val() == 'GROUP' && evt.namespace == 'confirm') {
            select.val(select.data('oldValue'));
            evt.preventDefault();
            showPublishDialog.apply(select, [getRowId.apply(row), row.find('.name input').val(), select.data('publish')]);
        }
        else {
            select.data('oldValue', select.val());
        }

        var pack = {};
        window.views.render.apply(row.find('> .status'), ['cell_status_pack', {pack: pack}]);
    });

});


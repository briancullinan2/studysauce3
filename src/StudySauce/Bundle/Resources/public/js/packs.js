
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
        var tab = $(this),
            last = null;

        // split into rows
        var clipRows = Papa.parse(clipText).data;

        // write out in a table
        for (var i=0; i<clipRows.length; i++) {
            // skip partial rows
            if(clipRows[i].length < 2)
                continue;

            var newRow = tab.find('.card-row.empty:not(.template):not(.removed):not(.changed)').first();
            if(newRow.length == 0) {
                // TODO: use template system instead
                tab.find('[href="#add-card"]').first().trigger('click');
                newRow = tab.find('.card-row.empty:not(.template):not(.removed):not(.changed)').first();
            }

            // list under currently focused row
            if(last != null) {
                last = newRow.addClass('changed').add(newRow.find('+ .expandable')).detach().insertAfter(last.last());
            }
            else {
                last = newRow.addClass('changed').add(newRow.find('+ .expandable'));
            }

            // set card type
            if(clipRows[i].length > 2) {
                (function (i) {
                    if (newRow.find('.type select option').filter(function () {
                            return $(this).attr('value') == clipRows[i][0];
                        }).length > 0) {
                        newRow.find('.type select').val(clipRows[i][0]).trigger('change');
                    }
                    else if (clipRows[i][0].match(/multiple/ig) != null) {
                        newRow.find('.type select').val('mc').trigger('change');
                    }
                    else if (clipRows[i][0].match(/false/ig) != null) {
                        newRow.find('.type select').val('tf').trigger('change');
                    }
                    else if (clipRows[i][0].match(/blank|short/ig) != null) {
                        newRow.find('.type select').val('sa exactly').trigger('change');
                    }
                    else {
                        newRow.find('.type select').val('').trigger('change');
                    }
                })(i);
            }
            else {
                newRow.find('.type select').val('').trigger('change');
            }

            setTypeClass.apply(newRow);

            // set correct answers
            newRow.find('.correct.type-mc textarea').val(clipRows[i].splice(3).filter(function (x) {return x.trim() != '';}).join("\n")).trigger('change');

            // TODO: merge this with row template and just pass a model containing type, answers, correct, content
            if(clipRows[i].length == 2) {
                newRow.find('.content textarea').val(clipRows[i][0]).trigger('change');
                newRow.find('.correct textarea').val(clipRows[i][1]).trigger('change');
            }
            else {
                var tf = (clipRows[i][2].match(/true|false/i) || [''])[0].toLowerCase();
                newRow.find('.correct.type-tf input').filter(tf == 'true'
                    ? '[value="true"]'
                    : (tf == 'false'
                    ? '[value="false"]'
                    : ':not(input)')).prop('checked', true);
                (function (i) {
                    newRow.find('.correct.type-mc .radios input').filter(function () {
                        return $(this).val() == clipRows[i][2];
                    }).prop('checked', true).trigger('change');
                })(i);
                newRow.find('.correct .correct:not(.type-mc) textarea').val(clipRows[i][2]);
                newRow.find('.content textarea').val(clipRows[i][1]).trigger('change');
            }

            var isUrl, content;
            if((isUrl = (/https:\/\/.*?(\s|\\n|$)/i).exec(content = newRow.find('.content textarea').val())) !== null) {
                newRow.find('.content textarea').val(content.replace(isUrl[0], '').trim().replace(/^\\n|\\n$/i, '').trim()).trigger('change');
                newRow.find('input[name="upload"]').val(isUrl[0].trim().replace(/^\\n|\\n$/i, '').trim());
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
        var data = gatherFields.apply(row, [['type']]);

        if(!row.is('.type-' + data.type)) {
            row.attr('class', row.attr('class').replace(/\s*type-.*?(\s|$)/ig, ' '));
            if (data.type != '' && data.type != null) {
                row.addClass('type-' + data.type);
            }
        }
    }

    body.on('change', '[id*="packs-"] .card-row select[name="type"]', setTypeClass);

    var autoSaveTimeout = 0;

    function updatePreview() {
        if($(this).length == 0) {
            return;
        }
        var row = $(this).add($(this).next('.expandable'));
        window.views.render.apply(row, ['cell_preview_card', []]);
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
        var cardRows = tab.find('.card-row:not(.removed):not(.template)');

        for(var c  = 0; c < cardRows.length; c++) {
            var row = $(cardRows[c]);
            var data = gatherFields.apply(row, [['type', 'content', 'answers', 'correct']]);

            if(data.content == '' && data.correct == '' && (typeof data.answers == 'undefined' || data.answers == '')) {
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

    body.on('resulted', '[id^="packs-"] .results', function () {
        var tab = $(this);
        autoSaveTimeout = null;
        if (tab.closest('.panel-pane').is('#packs-pack0')) {
            var id = getTabId.apply(tab);
            tab.closest('.panel-pane').attr('id', 'packs-pack' + id);
            window.activateMenu(Routing.generate('packs_edit', {pack: id}));
        }
        var loaded = body.find('#groups');
        loaded.off('show.resulted').on('show.resulted', function () {
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

    body.on('change confirm', '[id^="packs-"] .status select', function (evt) {
        var row = $(this).parents('.pack-row');
        var select = $(this);

        if($(this).val() == 'GROUP' && evt.type != 'confirm') {
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



$(document).ready(function () {

    var body = $('body');

    function getTab() {
        return $(this).closest('.panel-pane').find('.pack-edit .results, .card-list .results');
    }

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

            // set correct answers
            newRow.find('.correct.type-mc textarea').val(clipRows[i].splice(3).filter(function (x) {return x.trim() != '';}).join("\n")).trigger('change');

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

        packsFunc.apply(tab.find('.card-row:not(.template):not(.removed)'));
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

    function updatePreview() {
        var row = $(this);
        if(row.length == 0) {
            return;
        }
        var template = row.find('select[name="type"]').val() == ''
            ? row.find('+ .expandable .preview-card:not([class*="type-"])')
            : row.find('+ .expandable .preview-card.type-' + row.find('select[name="type"]').val().split(' ')[0]);
        var newTemplate = row.find('select[name="type"]').val() == ''
            ? row.find('~ .card-row + .expandable.template .preview-card:not([class*="type-"])')
            : row.find('~ .card-row + .expandable.template .preview-card.type-' + row.find('select[name="type"]').val().split(' ')[0]);
        // switch templates if needed
        if (newTemplate.length != template.length) {
            row.find('+ .expandable .preview-card').remove();
            template = newTemplate.clone().appendTo(row.find('+ .expandable .preview'))
        }

        // replace with image
        var url;
        if((url = row.find('input[name="upload"]').val().trim()) != '') {
            template.find('img').attr('src', url).load(function () {
                centerize.apply($(this));
            });
            template.filter(':not(.preview-answer)').find('.preview-content').replaceWith($('<img src="' + url + '" />').load(function () {
                centerize.apply($(this));
            }));
            template.filter('.preview-answer').find('.preview-prompt .preview-content').replaceWith($('<img src="' + url + '" />').load(function () {
                centerize.apply($(this));
            }));
            if(row.find('.content textarea').val().trim() != '') {
                template.find('[type="text"]').val(row.find('.content textarea').val());
            }
            else {
                template.find('[type="text"]').val('Type your answer');
            }
        }
        else {
            template.find('[type="text"]').val('Type your answer');
        }

        // insert content and multiple choice answers
        var content = row.find('.content textarea:visible').val() || '';
        template.find('.preview-content div').text(content.replace(/\\n/ig, "\n"));
        var answers = row.find('.correct.type-mc:visible textarea').val();
        if (answers != null) {
            answers = answers.split("\n");
            template.find('.preview-response div').each(function () {
                $(this).text(answers[$(this).parent().parent().find('.preview-response').index($(this).parent())]);
            });
        }
        var answer = row.find('.input.correct:visible textarea, .input.correct:visible select, .radio.correct:visible input[type="radio"]:checked, .radios:visible input[type="radio"]:checked').val() || '';
        template.filter('.preview-answer').find('.preview-inner .preview-content div').text(answer.replace(/\\n/ig, "\n"));

        $('#jquery_jplayer').jPlayer('option', 'cssSelectorAncestor', '.preview-play:visible');
        // center some preview fields
        centerize.apply(row.find(' + .expandable .preview-content div, + .expandable .preview-response div, + .expandable img, .pack-icon img'));
    }

    $(window).on('beforeunload', function (evt) {
        if($('.panel-pane[id^="packs-"]:visible').find('.pack-edit .pack-row.edit.changed:not(.template):not(.removed), .card-list .card-row.edit.changed:not(.template):not(.removed)').length > 0) {
            evt.preventDefault();
            return "You have unsaved changes!  Please don't go!";
        }
    });

    body.on('hide', '.panel-pane[id^="packs-"]', function () {
        var row = $(this).find('.results [class*="-row"].edit');
        row.removeClass('edit remove-confirm').addClass('read-only');
    });

    body.on('click', '[id^="packs-"] .preview-play .play', function () {
        var player = $('#jquery_jplayer');
        player.jPlayer("setMedia", {
            mp3: $(this).attr('href')
        });
        player.jPlayer("play");
    });

    function packsFunc (evt) {
        var tab = getTab.apply(this);
        var packRows = $(this).closest('.pack-row').filter(':not(.template):not(.removed)');
        var cardRows = $(this).closest('.card-row').filter(':not(.template):not(.removed)');

        for(var c  = 0; c < cardRows.length; c++) {
            var row = $(cardRows[c]);
            var data = gatherFields.apply(row, [['type']]);

            if(!row.is('.type-' + data.type)) {
                row.attr('class', row.attr('class').replace(/\s*type-.*?(\s|$)/ig, ' '));
                if (data.type != '' && data.type != null) {
                    row.addClass('type-' + data.type);
                }
            }

        }

        // do not autosave from selectize because the input underneath will change
        if(typeof evt != 'undefined') {
            if($(evt.target).parents('.selectize-input').length > 0) {
                return;
            }
            packRows.not('.changed').addClass('changed');
            cardRows.not('.changed').addClass('changed');
        }

        if(validationTimeout != null) {
            clearTimeout(validationTimeout);
        }
        validationTimeout = setTimeout(function () {
            validateChanged.apply(tab);
        }, 100);
    }

    function validateChanged() {

        var tab = getTab.apply(this);
        var packRows = tab.find('.pack-row.changed:not(.template),.pack-row:not(.valid):not(.template)'); // <-- this is critical for autosave to work
        var cardRows = tab.find('.card-row.changed:not(.removed):not(.template)');

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
                autoSave.apply(tab, [false] /* do not close and return to /packs from edits */);
            }, 2000);
        }
    }

    var autoSaveTimeout = 0;
    var validationTimeout = null;

    body.on('selected', '[id^="packs-"] .card-row', function () {
        updatePreview.apply($(this));
    });

    body.on('click', '[id^="packs-"] .card-row [href="#remove-confirm-card"]', packsFunc);

    body.on('click', '[id^="packs-"] a[href="#save-pack"], [id^="packs-"] [value="#save-pack"]', function (evt) {
        evt.preventDefault();
        var tab = getTab.apply(this);
        if (autoSaveTimeout != null) {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = null;
        }
        tab.find('.card-row.empty:not(.template)').each(function () {
            var that = jQuery(this);
            if(that.find('.content textarea').val().trim() == '') {
                that.add(that.next('.expandable')).removeClass('selected').addClass('removed');
            }
        });
        tab.find('[class*="-row"].edit').removeClass('edit remove-confirm').addClass('read-only');
        resizeTextAreas.apply(getTab.apply(this).find('.pack-row,.card-row'));
        validateChanged.apply(tab);
    });

    var isLoading = false;

    // TODO: generalize this
    function autoSave(close) {
        autoSaveTimeout = null;
        var tab = getTab.apply(this);
        var packRows = tab.find('.pack-row.valid:not(.template)');
        var cardRows = tab.find('.card-row.valid.changed:not(.template),.card-row.removed:not(.template)');
        if (packRows.length == 0) {
            return;
        }
        if(tab.find('.highlighted-link a[href^="#save-"]').is('[disabled]') || isLoading) {
            // select incorrect row handled by #goto-error
            return;
        }

        isLoading = true;
        loadingAnimation(tab.find('a[href="#save-pack"]'));

        // get the parsed list of cards
        var cards = [];
        cardRows.each(function () {
            var rowId = getRowId.apply($(this));
            if($(this).is('.removed') || $(this).is('.empty')) {
                cards[cards.length] = {
                    id: rowId,
                    remove: true
                };
            }
            else {
                cards[cards.length] = $.extend({id: rowId}, gatherFields.apply($(this), [['type', 'upload', 'content', 'answers', 'correct']]));
            }
        });
        cardRows.removeClass('changed');

        var packData = $.extend({cards: cards},
            gatherFields.apply(packRows, [['upload', 'status', 'title', 'keyboard']]));
        packRows.removeClass('changed');
        standardSave.apply(tab.find('a[href="#save-pack"]'), [packData]);
    }

    var shouldRefresh = false;
    body.on('resulted', '[id^="packs-"] .results', function () {
        isLoading = false;
        if (tab.closest('.panel-pane').is('#packs-pack0')) {
            var id = getTabId.apply(tab);
            tab.closest('.panel-pane').attr('id', 'packs-pack' + id);
            window.activateMenu(Routing.generate('packs_edit', {pack: id}));
        }
        shouldRefresh = true;
    });

    function setupPackEditor() {
        autoSaveTimeout = 0;
        var tab = $(this).closest('.panel-pane');
        validateChanged.apply(tab.find('.pack-row:not(.template)'));
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

    body.on('show', '#packs', function () {
        if (shouldRefresh) {
            loadResults.apply($(this).find('.results'));
        }
    });

    // TODO: generalize and move this to dashboard using some sort of property binding API, data-target, data-toggle for selects toggles class of closest matching target?

    body.on('change', '[id^="packs-"] .status select', function (evt) {
        var row = $(this).parents('.pack-row');
        var select = $(this);

        if($(this).val() == 'GROUP') {
            select.val(select.data('oldValue'));

            body.one('click.publish_confirm', '#general-dialog a[href="#submit"]', function () {
                setTimeout(function () {
                    var publish = select.data('publish');
                    select.find('option[value="GROUP"]').text(publish.schedule <= new Date()
                        ? 'Published'
                        : 'Pending (' + (publish.schedule.getMonth() + 1) + '/' + publish.schedule.getDay() + '/' + publish.schedule.getYear() + ' '
                    + publish.schedule.getHours() + ':00)');
                    row.find('.status > div').attr('class', publish.schedule <= new Date() ? 'group' : 'group pending');
                    select.val('GROUP');
                    // saves automatically
                }, 50);
            });

            showPublishDialog.apply(select, [getRowId.apply(row), row.find('.name input').val(), select.data('publish')]);
        }
        else {
            select.data('oldValue', select.val());
        }

        var status = select.val().toLowerCase();
        if(status == '') {
            row.find('.status > div').attr('class', '');
        }
        else if(!row.find('.status > div').is('.' + status)) {
            row.find('.status > div').attr('class', status);
        }
    });

    body.on('change keyup', '[id^="packs-"] .card-row input, [id^="packs-"] .card-row select, [id^="packs-"] .card-row textarea', packsFunc);
    body.on('change keyup', '[id^="packs-"] .pack-row input, [id^="packs-"] .pack-row select, [id^="packs-"] .pack-row textarea', packsFunc);

});
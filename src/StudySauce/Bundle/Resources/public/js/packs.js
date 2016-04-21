
$(document).ready(function () {

    var body = $('body'),
        radioCounter = 5000;

    key('⌘+v, ctrl+v, command+v', function () {
        var tab = $('[id^="packs-"] .results:visible');
        if(tab.is(':visible') && tab.find('input:focus, select:focus, textarea:focus').parents('.results [class*="-row"]').is('.empty')) {
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
        var clipRows = CSVToArray(clipText, "\t");

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
            newRow.find('.correct.radio input').attr('name', 'correct-' + radioCounter++);
            newRow.find('.correct textarea').val(clipRows[i].splice(3).filter(function (x) {return x.trim() != '';}).join("\n")).trigger('change');

            if(clipRows[i].length == 2) {
                newRow.find('.content input').val(clipRows[i][0]);
                newRow.find('.correct input').val(clipRows[i][1]);
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
                newRow.find('.correct.type-sa input, .input.correct:not([class*="type-"]) input').val(clipRows[i][2].replace("\n", '\\n'));
                newRow.find('.content input').val(clipRows[i][1]);
            }

            var isUrl, content;
            if((isUrl = (/https:\/\/.*?(\s|\\n|$)/i).exec(content = newRow.find('.content input').val())) !== null) {
                newRow.find('.content input').val(content.replace(isUrl[0], '').trim().replace(/^\\n|\\n$/i, '').trim());
                newRow.find('input[name="url"]').val(isUrl[0].trim().replace(/^\\n|\\n$/i, '').trim());
            }
        }

        packsFunc.apply(tab.find('.card-row:not(.template):not(.removed)'));
    }

    body.on('focus blur', '[id^="packs-"] .card-row .correct textarea, [id^="packs-"] .card-row .correct .radios input', function (evt) {
        var row = $(this).parents('.card-row');
        var that = row.find('.correct textarea');
        that.css('height', '');
        if (row.find('.correct :focus, .correct .radios :hover').length > 0) {
            that.height(that[0].scrollHeight - 4);
            row.find('.correct').addClass('editing');
        }
        else {
            row.find('.correct').removeClass('editing');
            if (row.find('.correct .radios :checked').length > 0) {
                row.find('.correct textarea, .correct .radios').scrollTop(row.find('.correct .radios input').index(row.find('.correct .radios :checked')) * 22 - 2);
            }
        }
    });

    body.on('change keyup', '[id^="packs-"] .card-row .correct textarea, [id^="packs-"] .card-row .correct .radios input', function (evt) {
        var row = $(this).parents('.card-row');
        var that = row.find('.correct textarea');

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
            (function (i) {
                if ((newItem = $(existing.find('input').filter(function () {return $(this).val() == answers[i];})
                        .map(function () {return $(this).parents('label')[0];})).not(newItems).first()).length == 0) {
                    newItem = $('<label class="radio"><input type="radio" name="' + origName + '" value="' + answers[i] + '"><i></i><span>' + answers[i] + '</span></label>')
                        .appendTo(row.find('.correct .radios'));
                }
                else {
                    if (i != newItem.index()) {
                        orderChanged = true;
                        newItem.detach().appendTo(row.find('.correct .radios'));
                    }
                }
                newItems = $.merge(newItems, [newItem[0]]);
            })(i);
        }
        if (existing.not(newItems).length > 0) {
            existing.not(newItems).remove();
        }
        var newVal = row.find('.correct .radios input').filter(function () {return $(this).val() == orig;});
        if(newVal.length == 0 || !orderChanged) {
            newVal = row.find('.correct .radios input').eq(line);
        }
        newVal.prop('checked', true);
        updatePreview.apply(row);
    });

    function updatePreview() {
        var row = $(this);
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
        if((url = row.find('input[name="url"]').val().trim()) != '') {
            template.find('img').attr('src', url).load(function () {
                centerize.apply($(this));
            });
            template.filter(':not(.preview-answer)').find('.preview-content').replaceWith($('<img src="' + url + '" />').load(function () {
                centerize.apply($(this));
            }));
            template.filter('.preview-answer').find('.preview-prompt .preview-content').replaceWith($('<img src="' + url + '" />').load(function () {
                centerize.apply($(this));
            }));
            if(row.find('.content input').val().trim() != '') {
                template.find('[type="text"]').val(row.find('.content input').val());
            }
            else {
                template.find('[type="text"]').val('Type your answer');
            }
        }
        else {
            template.find('[type="text"]').val('Type your answer');
        }

        // insert content and multiple choice answers
        template.find('.preview-content').text(row.find('.content input:visible').val());
        var answers = row.find('.correct.type-mc:visible textarea').val();
        if (answers != null) {
            answers = answers.split("\n");
            template.find('.preview-response').each(function () {
                $(this).text(answers[$(this).parent().find('.preview-response').index($(this))]);
            });
        }
        template.filter('.preview-answer').find('.preview-inner .preview-content').text(row.find('.input.correct:visible input:not([type="radio"]), .input.correct:visible select, .radio.correct:visible input[type="radio"]:checked, .radios:visible input[type="radio"]:checked').val());

        $('#jquery_jplayer').jPlayer('option', 'cssSelectorAncestor', '.preview-play:visible');
        // center some preview fields
        centerize.apply(row.find(' + .expandable .preview-content, + .expandable .preview-response, + .expandable img, .pack-icon img'));
    }

    body.on('click', '[id^="packs-"] .preview-play .play', function () {
        var player = $('#jquery_jplayer');
        player.jPlayer("setMedia", {
            mp3: $(this).attr('href')
        });
        player.jPlayer("play");
    });

    function packsFunc (evt) {
        var tab = $(this).closest('.results:visible');
        var packRows = $(this).closest('.pack-row').filter(':not(.template):not(.removed)');
        var cardRows = $(this).closest('.card-row').filter(':not(.template):not(.removed)');

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

        var tab = $(this).closest('.results:visible');
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

            if(!row.is('.type-' + data.type)) {
                row.attr('class', row.attr('class').replace(/\s*type-.*?(\s|$)/ig, ' '));
                if (data.type != '' && data.type != null) {
                    row.addClass('type-' + data.type);
                }
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
            tab.addClass('has-error');
        }
        else {
            tab.removeClass('has-error');
        }

        if(cardRows.length > 0) {
            updatePreview.apply(cardRows);
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

    body.on('click', '#packs a[href="#add-new-pack"]', function (evt) {
        evt.preventDefault();
        window.activateMenu(Routing.generate('packs_new'));
    });

    body.on('click', '#packs .pack-row', function (evt) {
        if(!$(evt.target).is('a, .pack-row > .packList') && $(evt.target).parents('.pack-row > .packList').length == 0)
        {
            var results = $(this).parents('.results');
            var row = $(this).closest('.pack-row');
            var packId = (/pack-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1];
            window.activateMenu(Routing.generate('packs_edit', {pack: packId}));
            row.removeClass('edit').addClass('read-only');
        }
    });

    body.on('click', '[id^="packs-"] .pack-row.edit a[href^="#cancel-"], [id^="packs-"] .pack-row ~ .highlighted-link a[href^="#cancel"]', function (evt) {
        evt.preventDefault();
        $(this).parents('.results').find('.pack-row,.card-row').removeClass('edit').addClass('read-only');
        window.activateMenu(Routing.generate('packs'));
    });

    body.on('click', '[id^="packs-"] .card-row [href="#remove-confirm-card"]', packsFunc);

    body.on('click', '[id^="packs-"] a[href="#save-pack"], [id^="packs-"] [value="#save-pack"]', function (evt) {
        evt.preventDefault();
        var tab = $(this).parents('.results:visible');
        if (autoSaveTimeout != null) {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = null;
        }
        tab.find('.card-row.empty:not(.template)').each(function () {
            var that = jQuery(this);
            if(that.find('.content input').val().trim() == '') {
                that.add(that.next('.expandable')).removeClass('selected').addClass('removed');
            }
        });
        tab.find('[class*="-row"].edit').removeClass('edit remove-confirm').addClass('read-only');
        autoSaveTimeout = 0;
        validateChanged.apply(tab);
        autoSave.apply(tab, [true]);
        autoSaveTimeout = null;
    });

    var isLoading = false;

    function autoSave(close) {
        autoSaveTimeout = null;
        var tab = $(this).closest('.results:visible');
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
        var packId = (/pack-id-([0-9]+)(\s|$)/i).exec(packRows.attr('class'));
        var cards = [];
        cardRows.each(function () {
            var rowId = (/card-id-([0-9]+)(\s|$)/i).exec($(this).attr('class'));
            if($(this).is('.removed') || $(this).is('.empty')) {
                cards[cards.length] = {
                    id: rowId != null ? rowId[1] : null,
                    remove: true
                };
            }
            else if($(this).is('.invalid')) {
                return true;
            }
            else {
                cards[cards.length] = {
                    id: rowId != null ? rowId[1] : null,
                    type:     $(this).find('.type select').val(),
                    content:  ($(this).find('input[name="url"]').val() != '' ? ($(this).find('input[name="url"]').val() + "\\n") : '') + $(this).find('.input.content:visible input').val(),
                    answers:  $(this).find('.correct.type-mc:visible textarea').val(),
                    correct:  $(this).find('.input.correct:visible input:not([type="radio"]), .input.correct:visible select, .radio.correct:visible input[type="radio"]:checked, .radios:visible input[type="radio"]:checked').val()
                };
            }
        });
        cardRows.removeClass('changed');

        $.ajax({
            url: Routing.generate('packs_create'),
            type: 'POST',
            dataType: close ? 'text' : 'json',
            data: {
                id: packId != null ? packId[1] : null,
                logo: packRows.find('.id img:not(.default)').attr('src'),
                cards: cards,
                groups: packRows.find('.groups input').data('ss_group').map(function (g) {return {id: g['value'].substring(9), remove: g['remove']};}),
                users: packRows.find('.groups input').data('ss_user').map(function (g) {return {id: g['value'].substring(8), remove: g['remove']};}),
                status: packRows.find('.status select').val(),
                title: packRows.find('.name input').val(),
                keyboard: packRows.find('select[name="keyboard"]').val(),
                publish: packRows.find('.status select').data('publish'),
                requestKey: getDataRequest.apply(tab).requestKey
            },
            success: function (data) {
                tab.find('.squiggle').stop().remove();

                // rename tab if working with a new pack
                // TODO: generalize this with some sort of data attribute or class, same as in groups
                if (tab.closest('.panel-pane').is('#packs-pack0')) {
                    var id = close
                        ? (/pack-id-([0-9]+)(\s|$)/i).exec($(data).find('.pack-row:not(.template)').first().attr('class'))[1]
                        : data.pack[0].id;
                    tab.closest('.panel-pane').attr('id', 'packs-pack' + id);
                    window.activateMenu(Routing.generate('packs_edit', {pack: id}));
                }

                // make fields read-only
                if(close) {
                }

                loadContent.apply(tab, [data]);

                // if done in the background, don't refresh the tab with loadContent
                isLoading = false;
            },
            error: function () {
                isLoading = false;
                tab.find('.squiggle').stop().remove();
            }
        });
    }

    var shouldRefresh = false;
    body.on('resulted', '[id^="packs-"] .results', function () {
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

        autoSaveTimeout = null;
        var select = tab.find('.pack-row:not(.template) .status select');
        select.data('oldValue', select.val());
    }

    body.on('click', '.panel-pane[id^="packs-"] a[href="#edit-pack"]', setupPackEditor);
    body.on('show', '.panel-pane[id^="packs-"]', setupPackEditor);

    body.on('show', '#packs', function () {
        if (shouldRefresh) {
            loadResults.apply($(this).find('.results'));
        }
    });

    body.on('change keyup', '[id^="packs-"] .card-row input, [id^="packs-"] .card-row select, [id^="packs-"] .card-row textarea', packsFunc);
    body.on('change keyup', '[id^="packs-"] .pack-row input, [id^="packs-"] .pack-row select, [id^="packs-"] .pack-row textarea', packsFunc);

    body.on('change', '[id^="packs-"] .pack-row .status select', function () {
        var row = $(this).parents('.pack-row');
        var status = row.find('.status select').val().toLowerCase();
        if(status == '') {
            row.find('.status > div').attr('class', '');
        }
        else if(!row.find('.status > div').is('.' + status)) {
            row.find('.status > div').attr('class', status);
        }
    });

    // TODO: generalize and move this to dashboard, just like users-groups dialog
    body.on('click', '[id^="packs-"] .card-row a[href="#upload-image"]', function () {
        var row = $(this).parents('.card-row');
        body.one('click.upload', 'a[href="#submit-upload"]', function () {
            row.find('input[name="url"]').val($('#upload-file').find('img').attr('src'));
            packsFunc.apply(row.addClass('changed'));
        });
    });

    body.on('click', '[id^="packs-"] .pack-row a[href="#upload-image"]', function () {
        var row = $(this).parents('.pack-row');
        body.one('click.upload', 'a[href="#submit-upload"]', function () {
            row.find('.id img').attr('src', $('#upload-file').find('img').attr('src')).removeClass('default');
            packsFunc.apply(row.addClass('changed'));
        });
    });

    body.on('change', '#pack-publish input[name="schedule"]', function () {
        var dialog = $('#pack-publish');
        if(dialog.find('input[name="schedule"]').datetimepicker('getValue') <= new Date()) {
            dialog.find('input[value="now"]').prop('checked', true);
        }
        else {
            dialog.find('input[value="later"]').prop('checked', true);
        }
    });

    body.on('change', '#pack-publish input[name="date"]', function () {
        var dialog = $('#pack-publish'),
            input = dialog.find('input[name="schedule"]');
        if(dialog.find('input[value="now"]').is(':checked')) {
            input.datetimepicker('setOptions', {value: new Date()})
        }
    });

    body.on('change', '[id^="packs-"] .status select', function () {
        var that = $(this);
        if($(this).val() == 'GROUP') {
            var row = that.parents('.pack-row');

            body.one('hidden.bs.modal', '#pack-publish', function () {
                that.val(that.data('oldValue'));
                if(that.val() != 'GROUP') {
                    that.trigger('change');
                }
            });
            showPublishDialog.apply(this, [row.find('.name input').val(), that.data('publish')])(function (publish) {
                row.find('.status select option[value="GROUP"]').text(publish.schedule <= new Date() ? 'Published' : 'Pending (' + (publish.schedule.getMonth() + 1) + '/' + publish.schedule.getDay() + '/' + publish.schedule.getYear() + ')');
                row.find('.status select').data('publish', publish).val('GROUP');
                row.find('.status > div').attr('class', publish.schedule <= new Date() ? 'group' : 'group pending');
                row.parents('.results').find('a[href="#save-pack"]').first().trigger('click');
            });
        }
        else {
            that.data('oldValue', that.val());
        }
    });

    body.on('click', '[id^="packs-"] *:has(input[data-ss_user][data-ss_group]) ~ a[href="#add-entity"]', function () {
        var row = $(this).parents('.pack-row');
        body.one('click.modify_entities', 'a[href="#submit-entities"]', function () {
            setTimeout(function () {
                packsFunc.apply(row.addClass('changed'));
            }, 100);
        });
    });

});
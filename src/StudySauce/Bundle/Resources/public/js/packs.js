
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
            last = tab.find('.card-row.empty').first();
        last = last.add(last.find('+ .expandable'));

        // split into rows
        var clipRows = CSVToArray(clipText, "\t");

        // write out in a table
        for (var i=0; i<clipRows.length; i++) {
            // skip partial rows
            if(clipRows[i].length < 2)
                continue;

            tab.find('[href="#add-card"]').first().trigger('click');
            var newRow = tab.find('.card-row.empty').first();
            last = newRow.add(newRow.find('+ .expandable')).detach().insertAfter(last.last());

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
                    }).prop('checked', true).trigger('blur');
                })(i);
                newRow.find('.correct.type-sa input, .input.correct:not([class*="type-"]) input').val(clipRows[i][2].replace("\n", '\\n'));
                newRow.find('.content input').val(clipRows[i][1]);
            }
        }

        // remove empties
        tab.find('.card-row.empty:not(.template)').each(function () {
            var that = jQuery(this);
            if(that.find('.content input').val().trim() == '') {
                that.add(that.next('.expandable')).remove();
            }
        });

        packsFunc.apply(tab.find('.card-row'));
    }

    body.on('focus mousedown keydown change keyup blur', '[id^="packs-"] .card-row .correct textarea, [id^="packs-"] .card-row .correct .radios input', function (evt) {
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
                row.find('.correct textarea, .correct .radios').scrollTop(row.find('.correct .radios input').index(row.find('.correct .radios :checked')) * 24 - 2);
            }
        }

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
            template.find('img').attr('src', url);
            template.filter(':not(.preview-answer)').find('.preview-content').replaceWith('<img src="' + url + '" />');
            template.filter('.preview-answer').find('.preview-prompt .preview-content').replaceWith('<img src="' + url + '" />');
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
        var tab = $(this).parents('.results:visible');
        var packRows = $(this).closest('.pack-row').filter(':not(.template):not(.removed)');
        var cardRows = $(this).closest('.card-row').filter(':not(.template):not(.removed)');
        cardRows.each(function () {
            var row = $(this);
            if(row.find('.content input').val().trim() == '' && (
                    row.find('.type select').val() != 'mc' || row.find('.correct.type-mc textarea').val().trim() == ''
                ) && row.find('.correct input').val().trim() == '') {
                row.removeClass('invalid').addClass('empty valid');
            }
            else if (row.find('.type select').val() != 'mc' || row.find('.correct.type-mc textarea').val().trim() != '') {
                row.removeClass('invalid empty').addClass('valid');
            }
            else {
                row.removeClass('valid empty').addClass('invalid');
            }
            var type = row.find('.type select').val();
            if(!row.is('.type-' + type)) {
                row.attr('class', row.attr('class').replace(/\s*type-.*?(\s|$)/ig, ' '));
                if (type != '' && type != null) {
                    row.addClass('type-' + type);
                }
            }

            if (row.find('.correct .radios :checked').length > 0) {
                row.find('.correct textarea, .correct .radios').scrollTop(row.find('.correct .radios input').index(row.find('.correct .radios :checked')) * 24 - 2);
            }

            if(previewTimeout != null) {
                clearTimeout(previewTimeout);
            }
            previewTimeout = setTimeout(function () {
                updatePreview.apply(row);
            }, 100);
        });
        packRows.each(function () {
            var row = $(this);
            if(row.find('.name input').val().trim() != '') {
                row.removeClass('invalid empty').addClass('valid');
            }
            else {
                row.removeClass('valid empty').addClass('invalid');
            }
        });

        if(tab.find('.pack-row.read-only').length > 0 || tab.find('.card-row.invalid:not(.removed)').length == 0 && (
            tab.find('.card-row.valid:not(.empty)').length > 0 || tab.find('.card-row.removed').length > 0) &&
            tab.find('.pack-row.valid:not(.empty)').length > 0) {
            tab.find('.highlighted-link a[href^="#save-"]').removeAttr('disabled');
        }
        else {
            tab.find('.highlighted-link a[href^="#save-"]').attr('disabled', 'disabled');
        }

        // do not autosave from selectize because the input underneath will change
        if(typeof evt != 'undefined' && $(evt.target).parents('.selectize-input').length > 0) {
            return;
        }

        // save at most every 2 seconds, don't autosave from admin lists
        if (autoSaveTimeout === null && $('.panel-pane[id^="packs-"]:visible').length > 0) {
            autoSaveTimeout = setTimeout(function () {
                autoSave.apply(packRows.add(cardRows), [false] /* do not close and return to /packs from edits */);
            }, 2000);
        }
    }

    var autoSaveTimeout = 0,
    previewTimeout = null;

    body.on('selected', '[id^="packs-"] .card-row', function () {
        updatePreview.apply($(this));
    });

    body.on('click', '#packs a[href="#add-new-pack"]', function (evt) {
        evt.preventDefault();
        window.activateMenu(Routing.generate('packs_new'));
    });

    body.on('click', '#packs .pack-row', function (evt) {
        if($(evt.target).is('a[href="#edit-pack"]') || !$(evt.target).is('a, .pack-row > .packList')
            && $(evt.target).parents('.pack-row > .packList').length == 0 && !$(evt.target).is('a[href^="#remove-"]'))
        {
            var results = $(this).parents('.results');
            var row = $(this).closest('.pack-row');
            var packId = (/pack-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1];
            window.activateMenu(Routing.generate('packs_edit', {pack: packId}));
            row.removeClass('edit').addClass('read-only');
        }
    });

    body.on('click', '[id^="packs-"] a[href="#edit-pack"]', function (evt) {
        evt.preventDefault();
        var tab = $(this).parents('.results');
        tab.find('.pack-row,.card-row').removeClass('read-only').addClass('edit');
        window.setupFields();
        autoSaveTimeout = 0;
        packsFunc.apply(tab.find('.pack-row').add(tab.find('.card-row')));
        autoSaveTimeout = null;
    });

    body.on('click', '[id^="packs-"] .pack-row.edit a[href^="#cancel-"], [id^="packs-"] .pack-row ~ .highlighted-link a[href^="#cancel"]', function (evt) {
        evt.preventDefault();
        $(this).parents('.results').find('.pack-row,.card-row').removeClass('edit').addClass('read-only');
        window.activateMenu(Routing.generate('packs'));
    });

    body.on('click', '[id^="packs"] .pack-row [href="#remove-confirm-pack"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('.pack-row');
        var tab = row.parents('.results');
        var rowId = (/pack-id-([0-9]+)(\s|$)/i).exec(row.attr('class'))[1];
        body.one('click.remove', '#confirm-remove a[href="#remove-confirm"]', function () {
            row.addClass('removed');
            $.ajax({
                url: Routing.generate('packs_remove'),
                type: 'POST',
                dataType: 'text',
                data: {
                    id: rowId
                },
                success: function () {
                    // reload packs if the tab is already loaded and switch to it
                    tab.trigger('resulted');
                    window.activateMenu(Routing.generate('packs'));
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

    body.on('click', '[id^="packs-"] a[href="#save-pack"], [id^="packs-"] [value="#save-pack"]', function (evt) {
        evt.preventDefault();

        if (autoSaveTimeout != null) {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = null;
        }
        autoSave.apply($(this).parents('.results:visible').find('.pack-row,.card-row'), [true]);
    });

    function autoSave(close) {
        autoSaveTimeout = null;
        var tab = $(this).parents('.results:visible');
        var packRows = $(this).closest('.pack-row').filter(':not(.template):not(.removed)');
        var cardRows = $(this).closest('.card-row').filter(':not(.template)');
        if (packRows.length == 0) {
            packRows = tab.find('.pack-row.edit:not(.template)');
        }
        if (packRows.length == 0) {
            return;
        }
        if(tab.find('.highlighted-link a[href^="#save-"]').is('[disabled]')) {
            // TODO: select incorrect row
            return;
        }

        tab.find('.highlighted-link a[href^="#save-"]').attr('disabled', 'disabled');
        loadingAnimation(tab.find('a[href="#save-pack"]'));

        // get the parsed list of cards
        var packId = (/pack-id-([0-9]+)(\s|$)/i).exec(packRows.attr('class'));
        var cards = [];
        cardRows.each(function () {
            var rowId = (/card-id-([0-9]+)(\s|$)/i).exec($(this).attr('class'));
            if($(this).is('.removed')) {
                cards[cards.length] = {
                    id: rowId != null ? rowId[1] : null,
                    remove: true
                };
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
                publish: packRows.find('.status select').data('publish')
            },
            success: function (data) {
                tab.find('.squiggle').stop().remove();

                // rename tab if working with a new pack
                if (tab.closest('.panel-pane').is('#packs-pack0')) {
                    tab.closest('.panel-pane').attr('id', 'packs-pack' + data.pack[0].id);
                    if (!close) {
                        window.activateMenu(Routing.generate('packs_edit', {pack: data.pack[0].id}));
                    }
                }

                if (close) {
                    loadContent.apply(tab, [data]);
                    packsFunc.apply(packRows.add(cardRows));
                    //var newId = (/pack-id-([0-9]*)(\s|$)/i).exec(tab.find('.pack-row:visible').first().attr('class'))[1];
                    //tab.find('.results .search input[name="search"]').val('pack-id:' + newId); // we dont need to trigger a change because this should be what we got back from create request
                    //tab.find('.search .input').removeClass('read-only');
                    window.activateMenu(Routing.generate('packs'));
                }
                else {
                    // copy new ids to new rows
                    for(var i = 0; i < data.pack.length; i++) {
                        if(packRows.filter('.pack-id-' + data.pack[i].id).length == 0) {
                            packRows.filter('.edit.pack-id-:not(.template)').first().removeClass('pack-id-').addClass('pack-id-' + data.pack[i].id);
                            // set pack id on panel
                        }
                    }
                    for(var j = 0; j < data.card.length; j++) {
                        if(cardRows.filter('.card-id-' + data.card[j].id).length == 0) {
                            cardRows.filter('.edit.card-id-:not(.template)').first().removeClass('card-id-').addClass('card-id-' + data.card[j].id);
                        }
                    }
                    tab.trigger('resulted');
                }

                // if done in the background, don't refresh the tab with loadContent
            },
            error: function () {
                tab.find('.squiggle').stop().remove();
            }
        });
    }

    var shouldRefresh = false;
    body.on('resulted', '[id^="packs-"] .results', function () {
        shouldRefresh = true;
    });

    body.on('show', '.panel-pane[id^="packs-"]', function () {
        autoSaveTimeout = 0;
        packsFunc.apply($(this).find('.pack-row').add($(this).find('.card-row')));
        autoSaveTimeout = null;
    });

    body.on('show', '#packs', function () {
        if (shouldRefresh) {
            loadResults.apply($(this).find('.results'));
        }
    });

    body.on('change keyup keydown', '[id^="packs-"] .card-row input, [id^="packs-"] .card-row select, [id^="packs-"] .card-row textarea', packsFunc);
    body.on('change keyup keydown', '[id^="packs-"] .pack-row input, [id^="packs-"] .pack-row select, [id^="packs-"] .pack-row textarea', packsFunc);

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
            packsFunc.apply(row);
        });
    });

    body.on('click', '[id^="packs-"] .pack-row a[href="#upload-image"]', function () {
        var row = $(this).parents('.pack-row');
        body.one('click.upload', 'a[href="#submit-upload"]', function () {
            row.find('.id img').attr('src', $('#upload-file').find('img').attr('src')).removeClass('default');
            packsFunc.apply(row);
        });
    });

    body.on('click', '[id^="packs-"] a[data-target="#pack-publish"], [id^="packs-"] a[href="#pack-publish"]', function () {
        var dialog = $('#pack-publish');
        var row = $(this).parents('.pack-row');

        dialog.find('input[name="publish-date"]').datetimepicker({
            format: 'd.m.Y H:i',
            inline: true,
            minDate: 0
        });

        // set up previous publish settings
        var publish = row.find('.status select').data('publish');
        if (typeof publish.schedule != 'undefined') {
            if(new Date(publish.schedule) < new Date()) {
                dialog.find('input[name="publish-schedule"][value="now"]').prop('checked', true);
            }
            else {
                dialog.find('input[name="publish-schedule"][value="later"]').prop('checked', true);
                dialog.find('input[name="publish-date"]').datetimepicker('setOptions', {value: new Date(publish.schedule)});
            }
        }
        else {
            dialog.find('input[name="publish-schedule"][value="now"]').prop('checked', true);
        }

        if (typeof publish.email != 'undefined') {
            dialog.find('input[name="publish-email"][value="' + publish.email + '"]').prop('checked', true);
        }
        else {
            dialog.find('input[name="publish-email"][value="true"]').prop('checked', true);
        }

        if (typeof publish.alert != 'undefined') {
            dialog.find('input[name="publish-alert"][value="' + publish.alert + '"]').prop('checked', true);
        }
        else {
            dialog.find('input[name="publish-alert"][value="true"]').prop('checked', true);
        }

        body.one('click.publish', '#pack-publish a[href="#submit-publish"]', function () {
            var entityField = row.find('.groups input.selectized');
            var newValue = entityField.val().split(' ');
            var groupStr = '';
            for(var v in newValue) {
                if (newValue.hasOwnProperty(v) && typeof entityField[0].selectize.options[newValue[v]] != 'undefined') {
                    var obj = entityField[0].selectize.options[newValue[v]];
                    groupStr += (groupStr != '' ? '<br />' : '') + obj.text;
                }
            }

            var publish = {
                schedule: dialog.find('input[name="publish-schedule"]:checked').val() == 'now' ? new Date() : dialog.find('input[name="publish-date"]').datetimepicker('getValue'),
                email: dialog.find('input[name="publish-email"]:checked').val() != null,
                alert: dialog.find('input[name="publish-alert"]:checked').val() != null
            };

            // show confirmation dialog
            $('#general-dialog').modal({show: true, backdrop: true})
                .find('.modal-body').html('<p>Are you sure you want to publish to ' + groupStr + '?');

            body.one('click.publish_confirm', '#general-dialog a[href="#submit"]', function () {
                row.find('.status select option[value="GROUP"]').text(publish.schedule <= new Date() ? 'Published' : 'Pending (' + (publish.schedule.getMonth() + 1) + '/' + publish.schedule.getDay() + '/' + publish.schedule.getYear() + ')');
                row.find('.status select').data('publish', publish).val('GROUP');
                row.find('.status > div').attr('class', publish.schedule <= new Date() ? 'group' : 'group pending');
                packsFunc.apply(row);
            });
        });
    });

    body.on('hidden.bs.modal', '#general-dialog', function () {
        setTimeout(function () {
            body.off('click.modify_entities_confirm');
            body.off('click.publish_confirm');
        }, 100);
    });

    body.on('click', '[id^="packs-"] *:has(input[data-ss_user][data-ss_group]) ~ a[href="#add-entity"]', function () {
        var row = $(this).parents('.pack-row');
        body.one('click.modify_entities', 'a[href="#submit-entities"]', function () {
            setTimeout(function () {
                packsFunc.apply(row);
            }, 100);
        });
    });

    body.on('hidden.bs.modal', '#pack-publish', function () {
        setTimeout(function () {
            body.off('click.publish');
        }, 100);
    });

});
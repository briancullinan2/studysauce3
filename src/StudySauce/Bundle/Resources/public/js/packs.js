
$(document).ready(function () {

    var body = $('body'),
        radioCounter = 5000;

    key('⌘+v, ctrl+v, command+v', function () {
        var tab = $('[id^="packs"] .results:visible');
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
                rowImport(clipText);
            }, 100);
        }
    });

    function rowImport(clipText) {
        var tab = $('.results:visible'),
            last = tab.find('.card-row.empty').first();

        // split into rows
        var clipRows = clipText.split(/\n/ig);

        // split rows into columns
        for (var i=0; i<clipRows.length; i++) {
            clipRows[i] = clipRows[i].split(/\t|\s\s\s\s/ig);
        }

        // write out in a table
        for (i=0; i<clipRows.length; i++) {
            // skip partial rows
            if(clipRows[i].length < 2)
                continue;

            tab.find('[href="#add-card"]').first().trigger('click');
            var newRow = tab.find('.card-row.empty').first().detach().insertAfter(last);
            last = newRow;
            newRow.attr('class', newRow.attr('class').replace(/card-id-[0-9]*(\s|$)/ig, ''));
            if(newRow.find('.type select option[value="' + clipRows[i][0] + '"]').length > 0) {
                newRow.find('.type select').val(clipRows[i][0]).trigger('change');
            }
            else if(clipRows[i][0].match(/multiple/ig) != null) {
                newRow.find('.type select').val('mc').trigger('change');
            }
            else if(clipRows[i][0].match(/false/ig) != null) {
                newRow.find('.type select').val('tf').trigger('change');
            }
            else if(clipRows[i][0].match(/blank|short/ig) != null) {
                newRow.find('.type select').val('sa').trigger('change');
            }
            else {
                newRow.find('.type select').val('').trigger('change');
            }

            newRow.find('.correct.radio input').attr('name', 'correct-' + radioCounter++);
            newRow.find('.correct textarea').val(clipRows[i].splice(3).filter(function (x) {return x.trim() != '';}).join("\n")).trigger('change');

            if(clipRows[i].length == 2) {
                newRow.find('.content input').val(clipRows[i][0]);
                newRow.find('.correct input').val(clipRows[i][1]);
            }
            else {
                newRow.find('.correct.type-tf input').filter(clipRows[i][2].match(/t/i) ? '[value="true"]' : (clipRows[i][2]
                    .match(/f/i) ? '[value="false"]' : ':not(input)')).prop('checked', true);
                newRow.find('.correct.type-mc select, .correct.type-sa input, .input.correct:not([class*="type-"]) input').val(clipRows[i][2]);
                newRow.find('.content input').val(clipRows[i][1]);
            }
        }

        // remove empties
        tab.find('.card-row.empty').each(function () {
            var that = jQuery(this);
            if(that.find('.content input').val().trim() == '' && tab.find('.card-row').length > 1) {
                that.remove();
            }
        });

        packsFunc();
    }

    body.on('focus mousedown keydown change keyup blur', '.card-row .correct textarea, .card-row .correct .radios input', function (evt) {
        var row = $(this).parents('.card-row');
        var that = row.find('.correct textarea');
        var rowId = (/card-id-([0-9]*)(\s|$)/i).exec(row.attr('class'))[1];
        that.css('height', '');
        if (row.find('.correct :focus, .correct .radios :hover').length > 0) {
            that.height(that[0].scrollHeight - 4);
            row.find('.correct').addClass('editing');
        }
        else {
            row.find('.correct').removeClass('editing');
            if (row.find('.correct .radios :checked').length > 0) {
                row.find('.correct textarea, .correct .radios').scrollTop(row.find('.correct .radios :checked').parents('label').position().top - 2);
            }
        }

        // get current line
        var orig = $(evt.target).is('.correct .radios input') ? $(evt.target).val() : row.find('.correct .radios input:checked').val();
        var line = row.find('.correct .radios input').index(row.find('.correct .radios input[value="' + orig + '"]'));
        var existing = row.find('.correct .radios label');
        var answers = that.val().split(/\n/ig) || [];
        var newItems = [];
        var orderChanged = false;
        for(var i in answers) {
            if(!answers.hasOwnProperty(i))
                continue;
            var newItem;
            if ((newItem = $(existing.find('[value="' + answers[i] + '"]').map(function () {return $(this).parents('label')[0];})).not(newItems).first()).length == 0) {
                newItem = $('<label class="radio"><input type="radio" name="correct-mc-' + rowId + '" value="' + answers[i] + '"><i></i><span>' + answers[i] + '</span></label>')
                    .appendTo(row.find('.correct .radios'));
            }
            else {
                if (i != newItem.index()) {
                    orderChanged = true;
                    newItem.detach().appendTo(row.find('.correct .radios'));
                }
            }
            newItems = $.merge(newItems, [newItem[0]]);
        }
        if (existing.not(newItems).length > 0) {
            existing.not(newItems).remove();
        }
        var newVal = row.find('.correct .radios input[value="' + orig + '"]');
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
        }

        // insert content and multiple choice answers
        template.find('[type="text"]').val(row.find('.content input').val());
        template.find('.preview-content').text(row.find('.content input:visible').val());
        var answers = row.find('.correct.type-mc:visible textarea').val();
        if (answers != null) {
            answers = answers.split("\n");
            template.find('.preview-response').each(function () {
                $(this).text(answers[$(this).parent().find('.preview-response').index($(this))]);
            });
        }
        template.filter('.preview-answer').find('.preview-inner .preview-content').text(row.find('.input.correct:visible input:not([type="radio"]), .input.correct:visible select, .radio.correct:visible input[type="radio"]:checked, .radios:visible input[type="radio"]:checked').val());

        // center some preview fields
        centerize.apply(row.find(' + .expandable .preview-content, + .expandable .preview-response, + .expandable .preview-inner img'));
    }

    function centerize() {
        var text = $('<textarea></textarea>')
            .css('padding', 0)
            .css('margin', 0)
            .css('position', 'fixed')
            .css('top', 0)
            .css('left', -10000)
            .css('opacity', '0')
            .css('height', 1)
            .css('border', 0)
            .css('width', 1).appendTo('body');

        $(this).each(function () {
            text.val($(this).text());
            text.width($(this).outerWidth() + 18);
            text.css('font-size', $(this).css('font-size'));
            var height = text[0].scrollHeight;
            $(this).css('padding-top', '');
            if (height < $(this).height()) {
                $(this).css('padding-top', (($(this).height() - height) / 2) + 'px')
            }
        });

        text.remove();
    }

    function packsFunc () {
        var tab = $('.results:visible');
        var rows = $(this).closest('.card-row:not(.removed)');
        if (rows.length == 0) {
            rows = tab.find('.card-row:not(.removed):not(.template)');
        }
        rows.each(function () {
            var row = $(this);
            if(row.find('.content input').val().trim() == '' && (
                    row.find('.type select').val() != 'mc' || row.find('.correct.type-mc textarea').val().trim() == ''
                )) {
                row.removeClass('invalid').addClass('empty valid');
            }
            else if (row.find('.content input').val().trim() != '' && (
                    row.find('.type select').val() != 'mc' || row.find('.correct.type-mc textarea').val().trim() != ''
                )) {
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
                row.find('.correct textarea, .correct .radios').scrollTop(row.find('.correct .radios :checked').parents('label').position().top - 2);
            }

            updatePreview.apply(row);
        });
        if(tab.find('.card-row.invalid:not(.removed)').length == 0 && (
            tab.find('.card-row.valid:not(.empty)').length > 0 || tab.find('.card-row.removed').length > 0)) {
            tab.find('.highlighted-link').removeClass('invalid').addClass('valid');
        }
        else {
            tab.find('.highlighted-link').removeClass('valid').addClass('invalid');
        }
    }

    body.on('selected', '.card-row', function () {
        centerize.apply($(this).find(' + .expandable .preview-content, + .expandable .preview-response'))
    });

    body.on('click', '.results a[href="#add-pack"]', function () {
        var results = $(this).parents('.results');
        var row = $(this).parents('.pack-row');
        var search = 'pack-id:0';
        results.find('.search .input').addClass('read-only');
        results.find('.search input[name="search"]').val(search).trigger('change');
        results.one('resulted', function () {
            for(var i = 0; i < 5; i++) {
                results.find('a[href="#add-card"]').first().trigger('click');
            }
        });
    });

    body.on('click', '.pack-row a[href="#edit-pack"]', function () {
        var results = $(this).parents('.results');
        var row = $(this).parents('.pack-row');
        var packId = (/pack-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1];
        window.activateMenu(Routing.generate('packs_edit', {pack: packId}));
        row.removeClass('edit').addClass('read-only');
    });

    body.on('resulted', '.results', function () {
        var results = $(this);
        var pack = results.find('.pack-row.edit');
        if (pack.length > 0) {
            results.find('.card-row').removeClass('read-only').addClass('edit');
        }
        packsFunc();
    });

    body.on('click', '.pack-row.edit a[href^="#cancel-"], .results .pack-row ~ .highlighted-link a[href^="#cancel"]', function (evt) {
        evt.preventDefault();
        var results = $(this).parents('.results'),
            row = $(this).parents('.pack-row');
        results.find('.search .input').removeClass('read-only');
        row.removeClass('read-only').addClass('edit');
        window.activateMenu(Routing.generate('packs'));
    });

    body.on('click', '.results [href="#remove-confirm-pack"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('.pack-row');
        var rowId = (/pack-id-([0-9]+)(\s|$)/i).exec(row.attr('class'))[1];
        $.ajax({
            url: Routing.generate('packs_remove'),
            type: 'POST',
            dataType: 'text',
            data: {
                id: rowId
            },
            success: loadContent
        });
    });

    body.on('click', '.results a[href="#save-pack"], .results [value="#save-pack"]', function (evt) {
        evt.preventDefault();

        autoSave.apply(this);

    });

    function autoSave(close) {
        var tab = $('.results:visible');
        var row = $(this).parents('.pack-row');
        var packId = (/pack-id-([0-9]+)(\s|$)/i).exec(row.attr('class'));
        if (row.length == 0) {
            row = tab.find('.pack-row:not(.empty):visible').first();
        }
        if(tab.find('.highlighted-link').is('.invalid')) {
            // TODO: select incorrect row
            return;
        }

        tab.find('.highlighted-link').removeClass('valid').addClass('invalid');
        loadingAnimation($(this));

        // get the parsed list of cards
        var cards = [];
        tab.find('.card-row.valid:not(.empty):not(.template)').each(function () {
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
            dataType: 'text',
            data: {
                id: packId != null ? packId[1] : null,
                logo: row.find('.id img:not(.default)').attr('src'),
                cards: cards,
                groups: row.find('.groups input').data('groups'),
                users: row.find('.groups input').data('users'),
                status: row.find('.status select').val(),
                title: row.find('.name input').val(),
                keyboard: row.find('select[name="keyboard"]').val()
            },
            success: function (data) {
                tab.find('.squiggle').stop().remove();
                if (close) {
                    row.removeClass('edit').addClass('read-only');
                    tab.find('.card-row.valid').removeClass('edit').addClass('read-only');
                    window.loadContent(data);
                    var newId = (/pack-id-([0-9]*)(\s|$)/i).exec(tab.find('.results .pack-row:visible').first().attr('class'))[1];
                    tab.find('.results .search input[name="search"]').val('pack-id:' + newId); // we dont need to trigger a change because this should be what we got back from create request
                    tab.find('.search .input').removeClass('read-only');
                    window.activateMenu(Routing.generate('packs'));
                }
                // if done in the background, don't refresh the tab with loadContent
            },
            error: function () {
                tab.find('.squiggle').stop().remove();
            }
        });
    }

    body.on('show', '[id^="packs"]', function () {
        packsFunc();
    });

    body.on('change keyup keydown', '.card-row input, .card-row select, .card-row textarea', packsFunc);

    // TODO: generalize and move this to dashboard, just like users-groups dialog
    body.on('click', '.card-row a[href="#upload-image"]', function () {
        var row = $(this).parents('.card-row');
        body.one('click.upload', 'a[href="#submit-upload"]', function () {
            row.find('input[name="url"]').val($('#upload-file').find('img').attr('src'));
            updatePreview.apply(row);
        });
    });

    body.on('click', '.pack-row a[href="#upload-image"]', function () {
        var row = $(this).parents('.pack-row');
        body.one('click.upload', 'a[href="#submit-upload"]', function () {
            row.find('.id img').attr('src', $('#upload-file').find('img').attr('src')).removeClass('.default');
        });
    });

    body.on('hidden.bs.modal', '#upload-file', function () {
        setTimeout(function () {
            body.off('click.upload');
        }, 100);
    });

    body.on('click', 'a[data-target="#pack-publish"], a[href="#pack-publish"]', function () {
        var dialog = $('#pack-publish');

        dialog.find('#publish-date').datepicker({
            showOtherMonths: true,
            altField: 'input[name="publish-date"]',
            altFormat: "DD, d MM, yy",
            selectOtherMonths: true,
            minDate: body.is('.adviser') ? null : 0,
            autoPopUp:'focus',
            changeMonth: true,
            changeYear: true,
            closeAtTop: false,
            dateFormat: 'mm/dd/yy',
            defaultDate:'0y',
            firstDay:0,
            fromTo:false,
            speed:'immediate',
            yearRange: '-3:+3'
        }).on('focus', function () {
            setTimeout(function () {
                $('#ui-datepicker-div').scrollintoview(DASHBOARD_MARGINS);
            }, 50);
        });
    });

});
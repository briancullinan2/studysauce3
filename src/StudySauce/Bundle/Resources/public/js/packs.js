
$(document).ready(function () {

    var body = $('body'),
        radioCounter = 5;

    key('⌘+v, ctrl+v, command+v', function () {
        var tab = $('#packs');
        if(tab.is(':visible')) {
            // get the clipboard text
            var text = $('<textarea></textarea>')
                .css('position', 'fixed')
                .css('top', 0)
                .css('left', -10000)
                .css('opacity', '0')
                .css('height', 1)
                .css('width', 1).appendTo(tab).focus();

            setTimeout(function () {
                var clipText = text.val(), i;
                text.remove();
                rowImport(clipText);
            }, 100);
        }
    });

    function setFontSize() {
        var words = $(this).find('.inner').text().split(/\s+/ig),
            size = 12;
        $(this).find('.inner').css('font-size', size);
        var origLines = Math.ceil($(this).find('.inner').height() / (size * 1.2)),
            numberOfLines = 0;
        do {
            size++;
            $(this).find('.inner').css('font-size', size);
            numberOfLines = Math.ceil($(this).find('.inner').height() / (size * 1.2));
        } while(size < 32 && $(this).find('.inner').height() < $(this).height() - (size * 1.2)
            && $(this).find('.inner').height() < $(this).width()
            && (numberOfLines < Math.floor(words.length / numberOfLines)
                || numberOfLines <= origLines || words.length / numberOfLines / 4 > $(this).width() / $(this).height()));
        $(this).find('.inner').css('font-size', size - 1);
        if($(this).find('.inner').height() < $(this).height()) {
            $(this).find('.inner').css('margin-top', ($(this).height() - $(this).find('.inner').height()) / 2);
        }
        else {
            $(this).find('.inner').css('margin-top', 0);
        }
    }

    function rowImport(clipText) {
        var tab = $('#packs'),
            last = tab.find('.card-row').last();

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
            var newRow = last.clone().insertAfter(tab.find('.card-row').last());
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
                newRow.find('.type select').val('');
            }

            newRow.find('.correct.radio input').attr('name', 'correct-' + radioCounter++);
            newRow.find('.answers textarea').val(clipRows[i].splice(4).filter(function (x) {return x.trim() != '';}).join("\n")).trigger('change');
            newRow.find('.correct.type-tf input').filter(clipRows[i][3].match(/t/i) ? '[value="true"]' : (clipRows[i][3]
                .match(/f/i) ? '[value="false"]' : ':not(input)')).prop('checked', true);
            newRow.find('.correct.type-mc select, .answers.type-sa input, .correct:not([class*="type-"]) input').val(clipRows[i][3]).trigger('change');

            if(clipRows[i].length == 2) {
                newRow.find('.content input').val(clipRows[i][0]);
                newRow.find('.response input').val(clipRows[i][1]);
            }
            else {
                newRow.find('.content input').val(clipRows[i][1]);
                newRow.find('.response input').val(clipRows[i][2]);
            }
        }

        // remove empties
        tab.find('.card-row.empty').each(function () {
            var that = jQuery(this);
            if(that.find('.content input').val().trim() == '' &&
                that.find('.response input').val().trim() == '' &&
                tab.find('.card-row').length > 1) {
                that.remove();
            }
        });

        packsFunc();
    }

    body.on('focus mousedown keydown change keyup', '#packs .answers textarea', function () {
        $(this).css('height', '');
        $(this).height($(this)[0].scrollHeight - 4);
        var row = $(this).parents('.card-row');
        // get current line
        var orig = row.find('.correct.type-mc select').val();
        var line = row.find('.correct.type-mc option[value="' + orig + '"]').index();
        row.find('.correct.type-mc option').remove();
        var answers = $(this).val().split(/\n/ig) || [];
        for(var i in answers) {
            if(!answers.hasOwnProperty(i))
                continue;
            $('<option value="' + answers[i] + '">' + answers[i] + '</option>').appendTo(row.find('.correct.type-mc select'));
        }
        var newVal = row.find('.correct.type-mc option[value="' + orig + '"]');
        if(newVal.length == 0) {
            newVal = row.find('.corrent.type-mc option').eq(line);
        }
        row.find('.correct.type-mc select').val(newVal.attr('value'));
    });

    body.on('change', '#packs .type select', function () {
        var row = $(this).parents('.card-row');
        row.attr('class', row.attr('class').replace(/type-.*?(\s|$)/ig, ''));
        if($(this).val() != '' && $(this).val() != null) {
            row.addClass('type-' + $(this).val());
        }
        if($(this).val() == 'mc') {
            row.find('.type-mc textarea').trigger('change');
        }
    });

    body.on('mousedown click focus', '#packs .type select', function () {
        $(this).find('option').each(function () {
            if($(this).attr('data-text') != null) {
                $(this).text($(this).attr('data-text'));
            }
        });
    });

    body.on('change blur', '#packs .type select', function () {
        $(this).find('option').each(function () {
            if($(this).attr('value') != '') {
                $(this).text($(this).attr('value').toLocaleUpperCase());
            }
        });
    });

    function packsFunc () {
        var tab = $('#packs');
        tab.find('.card-row').each(function () {
            var row = $(this);
            if(row.find('.content input').val().trim() == '' &&
                row.find('.response input').val().trim() == '' && (
                    row.find('.type select').val() != 'mc' || row.find('.answers.type-mc textarea').val().trim() == ''
                )) {
                row.removeClass('invalid').addClass('empty valid');
            }
            else if (row.find('.content input').val().trim() != '' && (
                    row.find('.type select').val() != 'mc' || row.find('.answers.type-mc textarea').val().trim() != ''
                )) {
                row.removeClass('invalid empty').addClass('valid');
            }
            else {
                row.removeClass('valid empty').addClass('invalid');
            }
        });
        if(tab.find('.card-row.invalid').length == 0 && tab.find('.card-row.valid:not(.empty)').length > 0) {
            tab.find('.highlighted-link').removeClass('invalid').addClass('valid');
        }
        else {
            tab.find('.highlighted-link').removeClass('valid').addClass('invalid');
        }
    }

    body.on('change keyup keydown', '#packs .card-row input, #packs .card-row select, #packs .card-row textarea', packsFunc);

    body.on('click', '#packs a[href="#create-new"]', function (evt) {
        evt.preventDefault();

        var tab = $('#packs');
        if(tab.find('.highlighted-link').is('.invalid')) {
            // TODO: select incorrect row
            return;
        }

        tab.find('.highlighted-link').removeClass('valid').addClass('invalid');
        loadingAnimation($(this));

        // get the parsed list of cards
        var cards = [];
        tab.find('.card-row').each(function () {
            var rowId = (/card-id-([0-9]*)(\s|$)/i).exec($(this).attr('class'));
            if($(this).is('removed')) {
                cards[cards.length] = {
                    id: rowId != null ? rowId[0] : null,
                    remove: true
                };
            }
            else {
                cards[cards.length] = {
                    id: rowId != null ? rowId[0] : null,
                    type: $(this).find('.type select').val(),
                    content: $(this).find('.content:visible input').val(),
                    response: $(this).find('.response:visible input').val(),
                    answers: $(this).find('.answers textarea').val(),
                    correct: $(this).find('.correct:visible input:not([type="radio"]), .correct:visible select, .correct:visible input[type="radio"]:checked').val()
                };
            }
        });

        $.ajax({
            url: window.callbackPaths['packs_create'],
            type: 'POST',
            dataType: 'text',
            data: {
                cards: cards,
                title: tab.find('.title input').val(),
                creator: tab.find('.creator input').val()
            },
            success: function (data) {
                tab.find('.squiggle').stop().remove();
                tab.find('table.results').each(function (i) {
                    $(this).replaceWith($(data).filter('#packs').find('table.results').eq(i));
                });

                // reset card entry form
                tab.find('.title input').val('');
                var remove = tab.find('.card-row').detach();
                for(var i = 0; i < 5; i++) {
                    var newRow = remove.last().clone().insertAfter(tab.find('.card-row').last());
                    newRow.attr('class', newRow.attr('class').replace(/card-id-[0-9]*(\s|$)/ig, ''));
                    newRow.find('.type select, .answers textarea, .correct.type-mc select, .answers.type-sa input, .content input, .response input, .correct input[type="text"]').val('').trigger('change');
                    newRow.find('.correct.radio input').attr('name', 'correct-' + radioCounter++);
                    newRow.find('.correct.type-tf input').prop('checked', false);
                }
                remove.remove();
            },
            error: function () {
                tab.find('.squiggle').stop().remove();
            }
        })
    });

});

$(document).ready(function () {

    var body = $('body');

    key('⌘+v, ctrl+v, command+v', function () {
        var importTab = $('#packs');
        if(importTab.is(':visible')) {
            importTab.find('textarea').focus();
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

    function rowImport(clipText, append, count) {
        var importTab = $('#packs');

        // split into rows
        var clipRows = clipText.split(/\n/ig);

        // split rows into columns
        for (var i=0; i<clipRows.length; i++) {
            clipRows[i] = clipRows[i].split(/\t|\s\s\s\s/ig);
        }

        // write out in a table
        if(typeof count == 'undefined') {
            count = 0;
        }
        var total = (importTab.find('textarea').val().match(/\n/g) || []).length + 1;
        for (i=0; i<clipRows.length; i++) {
            // skip partial rows
            if(clipRows[i].length < 2)
                continue;

            count++;
            if(clipRows[i].length >= 4 && clipRows[i][3] == 'multiple') {
                var newMultipleBoxes = importTab.find('.templates > *').not('.card:not(.type-multiple)').clone().appendTo(append);
                newMultipleBoxes.filter('.card').first().find('.inner').html(clipRows[i][0].replace(/\s*\n\s*|(\\r)*\\n(\\r)*/g, "<br />"));
                if(clipRows[i].length >= 3) {
                    newMultipleBoxes.filter('.card').first().find('select').val(clipRows[i][2]);
                }
                newMultipleBoxes.find('.preview-count').text(count + ' of ' + total);
                newMultipleBoxes.find('.preview-title').text(importTab.find('.title input').val().trim());
                var rows = clipRows[i][1].split(',');
                newMultipleBoxes.filter('.card').eq(1).find('.inner').html(rows.map(function (x) {return '<div class="response">' + x.replace(/\s*\n\s*|(\\r)*\\n(\\r)*/g, "<br />") + '</div>';}).join(''));
                newMultipleBoxes.filter('.card').last().find('select').append($(rows.map(function (x) {return '<option>' + x + '</option>';}).join('')));
                if(clipRows[i].length >= 5) {
                    newMultipleBoxes.filter('.card').last().find('select').val(clipRows[i][4]);
                    newMultipleBoxes.filter('.card').last().find('.inner').html(clipRows[i][4].replace(/\s*\n\s*|(\\r)*\\n(\\r)*/g, "<br />"));
                }
            }
            else {
                var newBoxes = importTab.find('.templates > *').not('.card.type-multiple').clone().appendTo(append);
                if(clipRows[i].length >= 3) {
                    newBoxes.filter('.card').first().find('select').val(clipRows[i][2]);
                }
                newBoxes.filter('.card').first().find('.inner').html(clipRows[i][0].replace(/\s*\n\s*|(\\r)*\\n(\\r)*/g, "<br />"));
                newBoxes.find('.preview-count').text(count + ' of ' + total);
                newBoxes.find('.preview-title').text(importTab.find('.title input').val().trim());
                newBoxes.filter('.card').last().find('.inner').html(clipRows[i][1].replace(/\s*\n\s*|(\\r)*\\n(\\r)*/g, "<br />"));
            }
        }

        // remove empties
        if(importTab.find('.import-row.edit.valid').not('fieldset .import-row').length > 1)
        {
            importTab.find('.import-row.edit').each(function () {
                var that = jQuery(this);
                if(that.find('.first-name input').val().trim() == '' &&
                    that.find('.last-name input').val().trim() == '' &&
                    that.find('.email input').val().trim() == '')
                {
                    that.remove();
                }
            });
        }
    }

    body.on('focus mousedown keydown change keyup', '#packs .response textarea', function () {
        $(this).css('height', '');
        $(this).height($(this)[0].scrollHeight - 4);
    });

    body.on('change', '#packs .type select', function () {
        var row = $(this).parents('.card-row');
        row.attr('class', row.attr('class').replace(/type-.*?(\s|$)/ig, ''));
        if($(this).val() != '') {
            row.addClass('type-' + $(this).val());
        }
    });

    body.on('click', '#packs a[href="#create-new"]', function (evt) {
        evt.preventDefault();

        var tab = $('#packs');
        if(tab.find('.highlighted-link').is('.invalid')) {
            if(tab.find('.title input').val().trim() == '') {
                tab.find('.title input').focus();
            }
            else {
                tab.find('textarea').focus();
            }
            return;
        }

        tab.find('.highlighted-link').removeClass('valid').addClass('invalid');
        loadingAnimation(tab.find('.highlighted-link a'));

        // get the parsed list of cards
        var cards = [], cardsDiv;
        rowImport(tab.find('textarea').val(), cardsDiv = $('<div>'));
        cardsDiv.find('.card.question').each(function () {
            cards[cards.length] = {
                content: $(this).find('.inner').text(),
                response: $(this).next('.response').text()
            };
        });

        $.ajax({
            url: window.callbackPaths['packs_create'],
            type: 'POST',
            dataType: 'text',
            data: {
                cards: cards,
                title: tab.find('.title input').val()
            },
            success: function (data) {
                tab.find('.squiggle').stop().remove();
                tab.find('table.results').each(function (i) {
                    $(this).replaceWith($(data).filter('#packs').find('table.results').eq(i));
                });
            },
            error: function () {
                tab.find('.squiggle').stop().remove();
            }
        })
    });

});
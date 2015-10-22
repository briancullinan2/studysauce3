
$(document).ready(function () {

    var body = $('body');

    key('⌘+v, ctrl+v, command+v', function () {
        var importTab = $('#packs');
        if(importTab.is(':visible')) {
            importTab.find('textarea').focus();
        }
    });

    body.on('click', '#packs table.results > tbody > tr:nth-child(odd)', function () {
        var row = $(this);
        if(row.is('.selected')) {
            row.removeClass('selected');
        }
        else {
            row.addClass('selected');
        }
    });

    var previewTimeout = null;
    function previewImport() {
        var importTab = $('#packs');
        if(previewTimeout != null)
            clearTimeout(previewTimeout);
        previewTimeout = setTimeout(function () {
            // select the first couple rows or limit to 1000 characters
            var entry = importTab.find('textarea').val();
            var first1000 = /(.*?\n){4}|[\s\S]{0,500}/i;
            var match = first1000.exec(entry)[0];

            // get the lines around where the cursor is
            var start = importTab.find('textarea')[0].selectionStart;
            if(typeof start == 'number') {
                var lastLine = (/.*?$/ig).exec(entry.substr(0, start))[0];
                match = first1000.exec(entry.substr(start - lastLine.length, entry.length))[0];
            }

            var preview = importTab.find('fieldset');
            preview.find('.question, .response').remove();
            var count = (entry.substr(0, start).match(/\n/g) || []).length;
            rowImport(match, preview, count);
            if(importTab.find('.title input').val() != '' && preview.find('.question').length > 0) {
                importTab.find('.highlighted-link').removeClass('invalid').addClass('valid');
            }
            else {
                importTab.find('.highlighted-link').removeClass('valid').addClass('invalid');
            }
        }, 1000);
    }

    function setFontSize() {
        var words = $(this).find('.inner').text().split(/\s+/ig),
            size = 12;
        $(this).css('font-size', size);
        var origLines = Math.ceil($(this).find('.inner').height() / (size * 1.2)),
            numberOfLines = 0;
        do {
            size++;
            $(this).css('font-size', size);
            numberOfLines = Math.ceil($(this).find('.inner').height() / (size * 1.2));
        } while(size < 32 && $(this).find('.inner').height() < $(this).height() - (size * 1.2)
            && $(this).find('.inner').height() < $(this).width()
            && (numberOfLines < Math.floor(words.length / numberOfLines)
                || numberOfLines == origLines || words.length / numberOfLines / 2 > $(this).width() / $(this).height()));
        $(this).css('font-size', size - 1);
    }

    function rowImport(clipText, append, count) {
        var importTab = $('#packs');

        // split into rows
        var clipRows = clipText.split(/\n/ig);

        // split rows into columns
        for (var i=0; i<clipRows.length; i++) {
            clipRows[i] = clipRows[i].split(/\t|\s\s\s\s+/ig);
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
            var newBoxes = $('<div class="question"><div class="inner">' + clipRows[i][0].replace(/\s*\n\s*|(\\r)*\\n(\\r)*/g, "<br />") + '</div><div class="preview-count">' + count + ' of ' + total + '</div><div class="preview-title">' + importTab.find('.title input').val().trim() + '</div><div class="answer">Get answer</div></div><div class="response"><div class="inner">' + clipRows[i][1].replace(/\s*\n\s*|(\\r)*\\n(\\r)*/g, "<br />") + '</div><div class="preview-count">' + count + ' of ' + total + '</div><div class="preview-title">' + importTab.find('.title input').val().trim() + '</div><div class="wrong">Wrong</div><div class="correct">Correct</div></div>').appendTo(append);
            setFontSize.apply(newBoxes.filter('.question'));
            setFontSize.apply(newBoxes.filter('.response'));
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

    body.on('click', '#packs a[href="#create-new"]', function (evt) {
        evt.preventDefault();

        var tab = $('#packs');
        if(tab.find('.highlighted-link').is('invalid')) {
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
        cardsDiv.find('.question').each(function () {
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
                var content = $(data).find('table.results');
                if(content.length > 0) {
                    tab.find('table.results').replaceWith(content);
                }
            },
            error: function () {
                tab.find('.squiggle').stop().remove();
            }
        })
    });

    body.on('change keydown keyup', '#packs .title input', previewImport);
    body.on('mousedown mouseup change focus blur keydown keyup', '#packs textarea', previewImport);

});
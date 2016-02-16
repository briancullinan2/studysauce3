
$(document).ready(function () {

    var body = $('body'),
        radioCounter = 5000;

    key('⌘+v, ctrl+v, command+v', function () {
        var tab = $('#packs');
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
            newRow.find('.answers textarea').val(clipRows[i].splice(3).filter(function (x) {return x.trim() != '';}).join("\n")).trigger('change');

            if(clipRows[i].length == 2) {
                newRow.find('.content input').val(clipRows[i][0]);
                newRow.find('.response input').val(clipRows[i][1]);
            }
            else {
                newRow.find('.correct.type-tf input').filter(clipRows[i][2].match(/t/i) ? '[value="true"]' : (clipRows[i][2]
                    .match(/f/i) ? '[value="false"]' : ':not(input)')).prop('checked', true);
                newRow.find('.correct.type-mc select, .answers.type-sa input, .input.correct:not([class*="type-"]) input').val(clipRows[i][2]);
                newRow.find('.content input').val(clipRows[i][1]);
                newRow.find('.response input').val(clipRows[i][7]);
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

    body.on('focus mousedown keydown change keyup blur', '#packs .answers textarea', function () {
        $(this).css('height', '');
        if ($(this).is(':focus')) {
            $(this).height($(this)[0].scrollHeight - 4);
        }
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
            else {
                $(this).text('Type');
            }
        });
    });

    function packsFunc () {
        var tab = $('#packs');
        var rows = $(this).closest('.card-row:not(.removed)');
        if (rows.length == 0) {
            rows = tab.find('.card-row:not(.removed)');
        }
        rows.each(function () {
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
            var type = row.find('.type select').val();
            if(!row.is('.type-' + type)) {
                row.attr('class', row.attr('class').replace(/\s*type-.*?(\s|$)/ig, ' '));
                if (type != '' && type != null) {
                    row.addClass('type-' + type);
                }
            }
        });
        if(tab.find('.card-row.invalid:not(.removed)').length == 0 && (
            tab.find('.card-row.valid:not(.empty)').length > 0 || tab.find('.card-row.removed').length > 0)) {
            tab.find('.highlighted-link').removeClass('invalid').addClass('valid');
        }
        else {
            tab.find('.highlighted-link').removeClass('valid').addClass('invalid');
        }
    }

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
        var search = 'pack-id:' + packId;
        results.find('.search .input').addClass('read-only');
        results.find('.search input[name="search"]').val(search).trigger('change');
    });

    body.on('resulted', '.results', function () {
        var results = $(this);
        var pack = results.find('.pack-row.edit');
        if (pack.length > 0) {
            results.find('.card-row').removeClass('read-only').addClass('edit');
        }
        packsFunc();
    });

    body.on('click', '.pack-row.edit a[href^="#cancel-"]', function () {
        var results = $(this).parents('.results');
        results.find('.search .input').removeClass('read-only');
        results.find('.card-row').removeClass('edit').addClass('read-only');
        results.find('.search input[name="search"]').val('').trigger('change');
    });

    body.on('change keyup keydown', '#packs .card-row input, #packs .card-row select, #packs .card-row textarea', packsFunc);

    body.on('click', '.results [href="#remove-confirm-pack"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('.pack-row');
        var rowId = (/pack-id-([0-9]+)(\s|$)/i).exec(row.attr('class'))[1];
        $.ajax({
            url: window.callbackPaths['packs_remove'],
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

        var tab = $('#packs');
        var row = $(this).parents('.pack-row');
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
        tab.find('.card-row.valid:not(.empty)').each(function () {
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
                    content:  $(this).find('.input.content:visible input').val(),
                    response: $(this).find('.input.response:visible input').val(),
                    answers:  $(this).find('.input.answers:visible textarea, .input.answers:visible input').val(),
                    correct:  $(this).find('.input.correct:visible input:not([type="radio"]), .input.correct:visible select, .radio.correct:visible input[type="radio"]:checked').val()
                };
            }
        });

        $.ajax({
            url: window.callbackPaths['packs_create'],
            type: 'POST',
            dataType: 'text',
            data: {
                id: tab.find('.results .search input[name="search"]').val().split(':')[1],
                cards: cards,
                group: row.find('.groups input[name="group"]:checked').val(),
                groups: row.find('.groups input[name="groups"]:checked').map(function () {return $(this).val();}).toArray(),
                status: row.find('.status select').val(),
                title: row.find('.name input').val()
            },
            success: function (data) {
                tab.find('.squiggle').stop().remove();
                row.removeClass('edit').addClass('read-only');
                tab.find('.card-row.valid').removeClass('edit').addClass('read-only');
                window.loadContent(data);
                var newId = (/pack-id-([0-9]*)(\s|$)/i).exec(tab.find('.results .pack-row:visible').first().attr('class'))[1];
                tab.find('.results .search input[name="search"]').val('pack-id:' + newId); // we dont need to trigger a change because this should be what we got back from create request
                tab.find('.search .input').removeClass('read-only');
            },
            error: function () {
                tab.find('.squiggle').stop().remove();
            }
        })
    });

});
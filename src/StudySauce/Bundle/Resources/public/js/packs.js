
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
                var imported = false;
                var isObj = false;
                if(clipText[0] == '{' && clipText[clipText.length-1] == '}') {
                    try {
                        var obj = JSON.parse(clipText);
                        if(typeof obj.title != 'undefined' && typeof obj.cards != 'undefined') {
                            isObj = true;
                            jsonImport.apply(tab, [obj]);
                            imported = true;
                        }
                    }
                    catch (e) {
                        if(isObj) {}
                        throw e;
                    }
                }
                if(!imported) {
                    rowImport.apply(tab, [clipText]);
                }
            }, 100);
        }
    });

    key('⌘+c, ctrl+c, command+c', function () {
        var tab = $('[id*="packs-"]:visible');
        if (tab.length > 0) {

            // get the clipboard text
            var text = $('<textarea></textarea>')
                .css('position', 'fixed')
                .css('top', 0)
                .css('left', -10000)
                .css('opacity', '0')
                .css('height', 1)
                .css('width', 1).appendTo(tab).focus();

            var pack = tab.find('.pack-edit .results').data('results')['pack'][0];
            pack.cards = tab.find('.card-list .results').data('results')['card'];
            var currentView = JSON.stringify(pack);
            text.val(currentView);
            text.selectRange(0, currentView.length);

            setTimeout(function () {
                text.remove();
            }, 100);
        }
    });

    key('enter, return', function () {
        var tab = $('[id*="cards-card"]:not(.hiding):visible, [id*="cards-answer"]:not(.hiding):visible');
        if(tab.find('.type-sa input').length > 0) {
            tab.find('.type-sa a[href="#done"]').trigger('click');
        }
        if(tab.find('.card-row .preview-card:not([class*="type-"]), .card-row .preview-card.preview-answer[class*="type-"]').length > 0) {
            tab.find('.card-row .preview-card:not([class*="type-"]), .card-row .preview-card.preview-answer[class*="type-"]').trigger('click');
        }
    });

    function jsonImport(json) {
        var results = $(this).find('.card-list .results'),
            request = results.data('request'),
            last = null;

        var newRows = $([]);
        for(var c in json['cards']) {
            if(!json['cards'].hasOwnProperty(c)) {
                return;
            }
            (function (card) {
                setTimeout(function () {
                    delete card.id;
                    var rowHtml = $(window.views.render('row-card', {
                        card: applyEntityObj(card),
                        table: 'card',
                        tableId: 'card',
                        tables: request.tables,
                        request: request,
                        results: []
                    }))
                        .removeClass('read-only').addClass('edit');

                    // list under currently focused row
                    if (last != null) {
                        last = rowHtml.insertAfter(last.last());
                    }
                    else {
                        last = rowHtml.insertAfter(results.find('> header.card'));
                    }
                    resizeTextAreas.apply(last);
                    last.addClass('changed');
                    last.trigger('validate');
                    newRows = newRows.add(last);
                }, 20);
            })(json['cards'][c]);
        }

        var packResults = $(this).find('.pack-edit .results');
        var packRequest = packResults.data('request');
        delete json.id;
        var rowHtml = $(window.views.render('row-pack', {
            pack: applyEntityObj(json),
            table: 'pack',
            tableId: 'pack',
            tables: packRequest.tables,
            request: packRequest,
            results: []
        }))
            .removeClass('read-only').addClass('edit');
        packResults.find('.pack-row').replaceWith(rowHtml);
        rowHtml.addClass('changed');

        // remove empties
        results.find('.card-row.empty:not(.removed), .card-row.empty:not(.removed) + .expandable:not([class*="-row"])').remove();

        if(results.find('.card-row:not(.removed)').length == 0) {
            for(var n = 0; n < 5; n++) {
                addResultRow.apply(results, ['card']);
            }
        }
    }

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
            if (clipRows[i].length < 2)
                continue;

            (function (clipRow) {
                setTimeout(function () {
                    var newRow = {};
                    // set card type
                    newRow['responseType'] = '';
                    if (clipRow.length > 2) {
                        if (clipRow[0].match(/multiple/ig) != null) {
                            newRow['responseType'] = 'mc'
                        }
                        else if (clipRow[0].match(/false/ig) != null) {
                            newRow['responseType'] = 'tf'
                        }
                        else if (clipRow[0].match(/blank|short/ig) != null) {
                            newRow['responseType'] = 'sa';
                        }
                    }

                    // set correct answers
                    newRow['answers'] = clipRow.splice(3).filter(function (x) {
                        return x.trim() != '';
                    });

                    // merge this with row template and just pass a model containing type, answers, correct, content
                    if (clipRow.length == 2) {
                        newRow['content'] = clipRow[0];
                        newRow['correct'] = clipRow[1];
                    }
                    else {
                        if (newRow['responseType'] == 'tf') {
                            newRow['correct'] = (clipRow[2].match(/true|false/i) || [''])[0].toLowerCase() == 'true'
                                ? 'true'
                                : ((clipRow[2].match(/true|false/i) || [''])[0].toLowerCase() == 'false' ? 'false' : '');
                        }
                        else {
                            newRow['correct'] = clipRow[2];
                        }
                        newRow['content'] = clipRow[1];
                    }

                    newRow['table'] = 'card';
                    var rowHtml = $(window.views.render('row-card', {
                        card: applyEntityObj(newRow),
                        table: 'card',
                        tableId: 'card',
                        tables: request.tables,
                        request: request
                    }))
                        .removeClass('read-only').addClass('edit');

                    // list under currently focused row
                    if (last != null) {
                        last = rowHtml.insertAfter(last.last());
                    }
                    else {
                        last = rowHtml.insertAfter(results.find('> header.card'));
                    }
                    resizeTextAreas.apply(last);
                    last.addClass('changed');
                    last.trigger('validate');
                    newRows = newRows.add(last);
                }, 20);
            })(clipRows[i]);
        }

        // remove empties
        results.find('.card-row.empty:not(.removed), .card-row.empty:not(.removed) + .expandable:not([class*="-row"])').remove();

        if(results.find('.card-row:not(.removed)').length == 0) {
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

    body.on('click', '[id^="packs-"] .preview-play .play, [id^="cards"] .preview-play .play', function (evt) {
        evt.preventDefault();
        var player = $('#jquery_jplayer');
        player.jPlayer("stop");
        if(player.data('jPlayer').status.src != $(this).attr('href')) {
            try {
                player.jPlayer("setMedia", {
                    mp3: $(this).attr('href')
                });
                player.jPlayer("play");
            }
            catch (e) {
                // silently fail
            }
        }
        else {
            player.jPlayer("play");
        }
    });

    // TODO: merge with row-card.html.php or cell-id-card.html.php
    function setTypeClass() {
        var that = $(this);
        setTimeout(function () {
            var row = that.closest('.card-row');
            var results = row.parents('.results');
            var request = results.data('request');
            var data = gatherFields.apply(row, [getAllFieldNames(request.tables['card'])]);
            data['table'] = 'card';
            data['id'] = getRowId.apply(row);
            window.views.render.apply(row, ['row-card', {card: applyEntityObj(data), request: request, tables: request.tables, table: 'card'}])
        }, 13);
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

    body.on('validate', '[id^="packs-"] [class*="-row"]', packsFunc);

    // TODO: merge this with template then run changes through template on server and check each cell for invalid class to validate before saving to database
    function packsFunc() {

        var tab = getTab.apply(this);
        var packRows = tab.find('.pack-row').first(); // <-- this is critical for autosave to work
        var cardRows = $(this).closest('.card-row:not(.removed)');

        for(var c  = 0; c < cardRows.length; c++) {
            var row = $(cardRows[c]);
            var data = gatherFields.apply(row, [['responseType', 'content', 'answers', 'correct']]);

            if(data.content == '' && (typeof data.correct == 'undefined' || data.correct == '') && (typeof data.answers == 'undefined' || data.answers == '')) {
                row.removeClass('invalid').addClass('empty valid');
            }
            else {
                if (data.content == '') {
                    row.find('.content').addClass('invalid');
                }
                else {
                    row.find('.content').removeClass('invalid');
                }
                if (data.correct == '' || (data.responseType == 'mc' && data.answers == '')) {
                    row.find('.correct').addClass('invalid');
                }
                else {
                    row.find('.correct').removeClass('invalid');
                }

                if (data.content == '' || data.correct == '' || (data.responseType == 'mc' && data.answers == '')) {
                    row.removeClass('valid empty').addClass('invalid');
                }
                else {
                    row.removeClass('invalid empty').addClass('valid');
                }
            }

            // update line number
            var rowIndex = '' + (row.parents('.results').find('.card-row:not(.removed)').index(row) + 1);
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
                resultsSave.apply(tab, [{}]);
                autoSaveTimeout = null;
            }, 2000);
        }
    }

    body.on('resulted.saved', '[id^="packs-"] .results', function (evt) {
        var results = $(this);
        var tab = results.closest('.panel-pane');
        autoSaveTimeout = null;
        if (tab.is('#packs-pack0') && typeof evt['results'] && typeof evt['results']['results']['pack'][0] != 'undefined') {
            window.views.render.apply(tab, ['packs', {entity: evt['results']['results']['pack'][0]}]);
            results.data('request', $.extend(results.data('request'), {requestKey: evt['results'].requestKey})); // replace key because template clears it
            var id = getTabId.apply(results);
            window.activateMenu(Routing.generate('packs_edit', {pack: id}));
            // save cards next!
            resultsSave.apply(tab.find('.card-list .results'), [{}]);
            loadResults.apply(tab.find('.group-list .results'));
        }
        var loaded = body.find('#packs'); // only top level packs page because packs cannot affect other pack pages
        loaded.off('show.resulted').one('show.resulted', function () {
            loadResults.apply($(this).find('.results'));
        });
    });

    body.on('resulted.saved', '[id^="groups-"] .results', function () {
        var loaded = body.find('[id^="packs"]:not(#packs-pack0)');
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

    body.on('click', '[id^="cards-card"] .card-row .preview-card:not([class*="type-"])', function (evt) {
        if($(evt.target).is('a, a *, .preview-play, preview-play *')) {
            return;
        }
        // go to answer without submitting for flash card
        var id = getRowId.apply($(this).parents('.card-row'));
        activateMenu(Routing.generate('cards_answers', {answer: id}));
    });

    // turn off study mode when not on a card
    body.on('show', '.panel-pane', function () {
        if(!$(this).is('[id^="cards"]')) {
            body.removeClass('study-mode');
        }
    });

    body.on('showing', '[id^="cards-pack"]', function () {
        body.addClass('study-mode');

        var tab = $(this);
        var results = tab.find('.results');
        var request = results.data('request');

        // call recalculate on template
        var footer = tab.find('.results .cardResult');
        var user = window.views.__globalVars.app.getUser();
        footer.find('*').remove();
        var up = $.extend(user.getUserPack(applyEntityObj({table: 'pack', id: request['pack-id']})), {user: user});
        window.views.render('cell-cardResult-user_pack', {
            context: footer,
            request: request,
            user_pack: up,
            results: {user_pack: [up]}
        });

    });

    body.on('loaded', '[id^="cards-card"], [id^="cards-answer"]', function () {
        var tab = $(this);
        var results = tab.find('.results');
        var request = results.data('request');
        // change the request to only load single user_pack
        tab.addClass('loaded');
        delete request['requestKey'];
        request['tables']['ss_user'] = ['id'];
        delete request['skipRetention'];
        tab.find('.results').data('request', request);
    });

    // turn on study mode to hide extra menus
    body.on('showing', '[id^="cards-card"], [id^="cards-answer"]', function () {
        body.addClass('study-mode');

        var tab = $(this);
        var results = tab.find('.results');
        var request = results.data('request');

        // update the card count at the bottom
        var footer = tab.find('.preview-footer');
        var user = window.views.__globalVars.app.getUser();
        var cardId = getRowId.apply(tab.find('.card-row'));
        footer.find('.preview-count').remove();
        window.views.render('cell-cardFooter-card', {
            context: footer,
            request: request,
            card: applyEntityObj({table: 'card', id: cardId}),
            results: {user_pack: [$.extend(user.getUserPack(applyEntityObj({table: 'pack', id: request['pack-id']})), {user: user})]}
        });

        // remove read-only from the last time it was saved
        tab.find('.card-row').removeClass('read-only');

        // default focus on short answer cards
        if(tab.find('.type-sa').length > 0) {
            tab.find('.type-sa input').val('').focus();
        }

        // load the next card in the sequence
        if(tab.closest('.panel-pane').find('.card-row').length > 0) {
            var packId = Cookies.get('retention_shuffle') == 'true' ? null : tab.closest('.panel-pane').data('card').pack.id;
            loadOneExtra.apply(this, [packId, getRowId.apply(tab.find('.card-row'))]);
        }

    });

    body.on('show', '[id^="cards-card"], [id^="cards-answer"]', function () {
        var tab = $(this);

        // setup the play button
        $('#jquery_jplayer').jPlayer('option', 'cssSelectorAncestor', '.preview-play:visible');
        centerize.apply(tab.find('.preview-play a'));
        if(tab.find('.preview-card:not(.preview-answer) .preview-play').length > 0) {
            tab.find('.preview-card:not(.preview-answer) .preview-play .play').first().trigger('click');
        }

    });

    body.on('show', '#home', function () {
        Cookies.set('retention', moment(new Date()).formatPHP('r'), { expires: 7 });
        body.addClass('clear-header');
        loadResults.apply($(this).find('.results'));
        if (document.cancelFullScreen) {
            document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
        }
    });

    body.on('hiding', '#home', function () {
        body.removeClass('clear-header');
    });

    function goFullscreen() {
        if ((document.fullScreenElement && document.fullScreenElement !== null) ||
            (!document.mozFullScreen && !document.webkitIsFullScreen)) {
            if (document.documentElement.requestFullScreen) {
                document.documentElement.requestFullScreen();
            } else if (document.documentElement.mozRequestFullScreen) {
                document.documentElement.mozRequestFullScreen();
            } else if (document.documentElement.webkitRequestFullScreen) {
                document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
            }
        }
    }

    body.on('click', '[id^="packs"] .pack-row a[href^="/cards"]', function () {
        var rowId = getRowId.apply($(this).parents('.pack-row'));
        Cookies.set('retention_' + rowId, moment(new Date()).formatPHP('r'), { expires: 7 });
        Cookies.set('retention_summary', 'true');
    });

    body.on('click', '[id^="home"] .user-shuffle a[href^="/cards"]', function () {
        Cookies.set('retention_summary', 'false');
        Cookies.set('retention_shuffle', $(this).is('header a') ? 'true' : 'false');
    });

    body.on('click', '[id^="cards-pack"] a[href^="/cards"]', function () {
        var tab = $(this);
        var results = tab.find('.results');
        var request = results.data('request');
        // if retention_summary
        if(Cookies.get('retention_summary') == 'true') {
            Cookies.set('retention_' + request['pack-id'], moment(new Date()).formatPHP('r'), { expires: 7 });
        }
        else {
            Cookies.set('retention', moment(new Date()).formatPHP('r'), { expires: 7 });
        }
    });

    body.on('click', '[id^="cards"] .card-row .preview-answer:not([class*="type-"]) [href="#wrong"], [id^="cards"] .card-row .preview-answer:not([class*="type-"]) [href="#right"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('.card-row');
        if(row.is('.read-only')) {
            return;
        }
        row.addClass('read-only');

        var id = getRowId.apply($(this).parents('.card-row'));
        var correct = $(this).is('[href="#right"]');
        var packId = $(this).parents('.panel-pane').data('card').pack.id;
        var response = {
            pack: packId,
            correct : correct,
            card : id,
            created : moment(new Date()).formatPHP('r'),
            answer: ''
        };

        var user = window.views.__globalVars.app.getUser();
        var ups = user.getUserPacks().toArray();
        for(var up in ups) {
            if(ups.hasOwnProperty(up) && ups[up].getPack().getId() == packId) {
                ups[up].retention[id][2] = !correct;
                ups[up].retention[id][3] = response.created;
            }
        }
        user.userPacks = ups;
        jQuery('.header').data('user', user);

        body.one('hiding', '[id^="cards"]', function () {
            $(this).stop().hide();
        });
        body.one('showing', '.panel-pane', function () {
            $(this).stop().css({left: 0});
            doFlash(correct);
        });
        pickNextCard(packId, id);

        $.ajax({
            url: Routing.generate('responses', {user:$('.header').data('user').id}),
            type: 'POST',
            dataType: 'json',
            data: response,
            success: function (data) {

            },
            error: function () {
            }
        });
    });

    function getRandomCard(retention, prefix, previousId) {
        retention.sort();
        var i = retention.indexOf(parseInt(previousId));
        if(i > -1) {
            retention.splice(i, 1);
        }
        i = retention.indexOf(previousId);
        if(i > -1) {
            retention.splice(i, 1);
        }
        console.log(retention.join(','));
        var seed = prefix + previousId;
        console.log(seed);
        var rng = new Math.seedrandom(seed);
        return retention[Math.floor(rng() * retention.length)];
    }

    function getRetention(packId, cardId) {
        var isSummary = Cookies.get('retention_summary') == 'true';
        var retentionDate = isSummary ? Cookies.get('retention_' + packId) : Cookies.get('retention');
        var retention = [];
        var user = window.views.__globalVars.app.getUser();
        var userPacks = user.getUserPacks().toArray();
        for(var up in userPacks) {
            /** @var UserPack $up */
            if(!userPacks.hasOwnProperty(up) || userPacks[up].getRemoved() || userPacks[up].getPack().getStatus() == 'DELETED'
                || (userPacks[up].getPack().getStatus() == 'UNPUBLISHED' && userPacks[up].getPack().getOwnerId() != user.getId())) {
                continue;
            }
            if(isSummary && userPacks[up].pack.id != packId) {
                continue;
            }
            var pack = userPacks[up].getRetention();
            for(var id in pack) {
                if(!pack.hasOwnProperty(id)) {
                    continue;
                }
                var r = pack[id];
                if(['', id].join('') == ['', cardId].join('')) {
                    continue;
                }
                if(r[3] != null && new Date(r[3]) > new Date(retentionDate)) {

                }
                else if (isSummary || r[2]) {
                    retention[retention.length] = id;
                }
            }
        }
        return retention;
    }

    function loadOneExtra(packId, newId) {
        // pick card based on last time hitting home page, same retention rules as in the app
        var retentionDate;
        if(Cookies.get('retention_summary') == 'true') {
            retentionDate = Cookies.get('retention_' + packId);
            if(retentionDate == null) {
                Cookies.set('retention_' + packId, retentionDate = moment(new Date()).formatPHP('r'), { expires: 7 });
            }
        }
        else {
            retentionDate = Cookies.get('retention');
            if(retentionDate == null) {
                Cookies.set('retention', retentionDate = moment(new Date()).formatPHP('r'), { expires: 7 });
            }
        }
        var retention = getRetention(packId, newId);

        // pick card based on last time hitting home page, same retention rules as in the app
        var nextId = getRandomCard(retention, retentionDate, newId);
        var tab = $('#cards-card' + nextId);
        if(typeof nextId != 'undefined' && tab.length == 0) {
            loadPanel.apply(this, [Routing.generate('cards', {card: nextId}), true, function () {}]);
        }
    }

    function pickNextCard(packId, cardId) {
        var retentionDate;
        if(Cookies.get('retention_summary') == 'true') {
            retentionDate = Cookies.get('retention_' + packId);
            if(retentionDate == null) {
                Cookies.set('retention_' + packId, retentionDate = moment(new Date()).formatPHP('r'), { expires: 7 });
            }
        }
        else {
            retentionDate = Cookies.get('retention');
            if(retentionDate == null) {
                Cookies.set('retention', retentionDate = moment(new Date()).formatPHP('r'), { expires: 7 });
            }
        }
        var retention = getRetention(packId, cardId);

        // TODO: created results tab without callback
        var newId = getRandomCard(retention, retentionDate, cardId);
        if(retention.length > 0) {
            activateMenu(Routing.generate('cards', {card: newId}));
        }
        // go to results page
        else {
            activateMenu(Routing.generate('cards_result', {pack: packId}));
        }

    }

    function setupProgress() {
        var progress = $(this).find('.preview-progress:visible:not(.setup-progress)');
        if(progress.length > 0) {
            progress.addClass('setup-progress');
            progress.data('progress', new ProgressBar.Circle(progress[0], {
                strokeWidth: 12,
                easing: 'linear',
                duration: 250,
                color: '#09B',
                trailColor: '#BBB',
                trailWidth: 12,
                svgStyle: null
            }));
        }
    }

    body.on('show', '[id^="cards"]', setupProgress);

    body.on('resulted.refresh', '[id^="cards"] .results', function () {
        $('#jquery_jplayer').jPlayer('option', 'cssSelectorAncestor', '.preview-play:visible');
        var that = $(this).closest('.panel-pane');
        setupProgress.apply(that);
        if($(this).is('[id^="cards-card"], [id^="cards-answer"]')) {
            var packId = Cookies.get('retention_shuffle') == 'true' ? null : that.data('card').pack.id;
            loadOneExtra.apply(this, [packId, getRowId.apply($(this).find('.card-row'))]);
        }
    });

    var jPlayer = $('#jquery_jplayer');
    jPlayer.bind($.jPlayer.event.timeupdate, function (evt) {
        var player = $('#jquery_jplayer').data('jPlayer');
        var progress = $('.preview-progress:visible').data('progress');
        if(typeof progress != 'undefined') {
            progress.stop();
            progress.animate(player.status.currentTime / player.status.duration);
        }
    });

    function doFlash(correct) {
        if(correct) {
            $('<div class="flash-right"><span>&#x2713;︎</span></div>').appendTo(body).animate({opacity: 0}, 1000, function () {
                $(this).remove();
            });
        }
        else {
            $('<div class="flash-wrong"><span>&#x2717;</span></div>').appendTo(body).animate({opacity: 0}, 1000, function () {
                $(this).remove();
            });
        }
    }

    body.on('click', '[id^="cards-answer"] .card-row .preview-card.preview-answer[class*="type-"]', function (evt) {
        if($(evt.target).is('a, a *, .preview-play, preview-play *')) {
            return;
        }

        var packId = Cookies.get('retention_shuffle') == 'true' ? null : $(this).parents('.panel-pane').data('card').pack.id;
        var id = getRowId.apply($(this).parents('.card-row'));
        pickNextCard(packId, id);
    });

    body.on('click', '[id^="cards"] .card-row .type-mc .preview-response, [id^="cards"] .card-row .type-tf a[href="#true"], [id^="cards"] .card-row .type-tf a[href="#false"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('.card-row');
        if(row.is('.read-only')) {
            return;
        }
        row.addClass('read-only');

        var id = getRowId.apply($(this).parents('.card-row'));
        var correct = $(this).is('.correct');
        var packId = $(this).parents('.panel-pane').data('card').pack.id;
        var response = {
            pack: packId,
            correct : correct,
            card : id,
            created : moment(new Date()).formatPHP('r'),
            answer: ((/answer-id-([0-9]*)/).exec($(this).attr('class')) || [])[1]
        };

        var user = window.views.__globalVars.app.getUser();
        var ups = user.getUserPacks().toArray();
        for(var up in ups) {
            if(ups.hasOwnProperty(up) && ups[up].getPack().getId() == packId) {
                ups[up].retention[id][2] = !correct;
                ups[up].retention[id][3] = response.created;
            }
        }
        user.userPacks = ups;
        jQuery('.header').data('user', user);

        // do transition
        body.one('hiding', '[id^="cards"]', function () {
            $(this).stop().hide();
        });
        body.one('showing', '.panel-pane', function () {
            $(this).stop().css({left: 0});
            doFlash(correct);
        });
        if(!correct) {
            activateMenu(Routing.generate('cards_answers', {answer: id}));
        }
        else {
            pickNextCard(packId, id);
        }

        // save response
        $.ajax({
            url: Routing.generate('responses', {user:$('.header').data('user').id}),
            type: 'POST',
            dataType: 'json',
            data: response,
            success: function (data) {

            },
            error: function () {
            }
        });
    });

    body.on('click', '[id^="cards"] .card-row .type-sa a[href="#done"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('.card-row');
        if(row.is('.read-only')) {
            return;
        }
        row.addClass('read-only');

        var id = getRowId.apply($(this).parents('.card-row'));
        var input = $(this).parents('.card-row').find('input');

        // check answer
        var correct = (new RegExp(input.data('correct'), 'i')).exec(input.val()) != null;
        var packId = $(this).parents('.panel-pane').data('card').pack.id;
        var response = {
            pack: packId,
            correct : correct,
            card : id,
            created : moment(new Date()).formatPHP('r'),
            value : input.val(),
            answer: ((/answer-id-([0-9]*)/).exec($(this).attr('class')) || [])[1]
        };

        var user = window.views.__globalVars.app.getUser();
        var ups = user.getUserPacks().toArray();
        for(var up in ups) {
            if(ups.hasOwnProperty(up) && ups[up].getPack().getId() == packId) {
                ups[up].retention[id][2] = !correct;
                ups[up].retention[id][3] = response.created;
            }
        }
        user.userPacks = ups;
        jQuery('.header').data('user', user);


        // do transition
        body.one('hiding', '[id^="cards"]', function () {
            $(this).stop().hide();
        });
        body.one('showing', '.panel-pane', function () {
            $(this).stop().css({left: 0});
            doFlash(correct);
        });
        if(!correct) {
            activateMenu(Routing.generate('cards_answers', {answer: id}));
        }
        else {
            pickNextCard(packId, id);
        }

        // save response
        $.ajax({
            url: Routing.generate('responses', {user: $('.header').data('user').id}),
            type: 'POST',
            dataType: 'json',
            data: response,
            success: function (data) {
                // TODO: update global retention
            },
            error: function () {
            }
        });
    });
});


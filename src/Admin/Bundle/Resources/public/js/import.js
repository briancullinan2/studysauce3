
jQuery(document).ready(function() {

    var body = $('body');
    var ctrlDown = false;
    var ctrlKey = 17, vKey = 86, cKey = 67;

    function importFunc() {
        var importTab = $('#import'),
            row = $(this).closest('.import-row');
        row.each(function () {
            var that = jQuery(this),
                isValid = true;

            if(that.find('.first-name input').val().trim() == '' ||
                that.find('.last-name input').val().trim() == '' ||
                that.find('.email input').val().trim() == '' ||
                !(/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}\b/i).test(that.find('.email input').val()))
                isValid = false;

            if(isValid)
                that.removeClass('invalid').addClass('valid');
            else
                that.removeClass('valid').addClass('invalid');
        });
        if(row.is('fieldset .import-row')) {
            if (importTab.find('.import-row.edit.valid').length == 0)
                importTab.find('.highlighted-link:not(.form-actions)').removeClass('valid').addClass('invalid');
            else
                importTab.find('.highlighted-link:not(.form-actions)').removeClass('invalid').addClass('valid');
        }
        else {
            if (importTab.find('.import-row.edit.valid').length == 0)
                importTab.find('.form-actions').removeClass('valid').addClass('invalid');
            else
                importTab.find('.form-actions').removeClass('invalid').addClass('valid');
        }
    }

    body.on('click', '#import a[href*="/register"]', function (evt) {
        evt.preventDefault();
    });

    body.on('change', '#import .first-name input, #import .last-name input, #import .email input', importFunc);
    body.on('keyup', '#import .first-name input, #import .last-name input, #import .email input', importFunc);

    $(document).keydown(function(e)
    {
        if (e.keyCode == ctrlKey) ctrlDown = true;
    }).keyup(function(e)
    {
        if (e.keyCode == ctrlKey) ctrlDown = false;
    });

    body.on('keydown', '#import', function(e)
    {
        var importTab = $('#import');
        if (ctrlDown && (e.keyCode == vKey || e.keyCode == cKey)) {

            // get the clipboard text
            importTab.find('textarea').focus();
        }
    });

    function rowImport(clipText, append)
    {
        var importTab = $('#import');

        // split into rows
        var clipRows = clipText.split(/\n/ig);

        // split rows into columns
        for (var i=0; i<clipRows.length; i++) {
            clipRows[i] = clipRows[i].split(/\t|\s\s\s\s+/ig);
        }

        // write out in a table
        for (i=0; i<clipRows.length; i++) {
            if(clipRows[i].length == 0 || clipRows[i][0].length == 0 || clipRows[i].indexOf('email') > -1 ||
                clipRows[i].indexOf('e-mail') > -1 || clipRows[i].indexOf('E-mail') > -1)
                continue;
            var addUser = importTab.find('.import-row').last(),
                newRow = addUser.clone().attr('id', '').addClass('edit');
            if(append != null)
                newRow.appendTo(append);
            else
                newRow.insertBefore(addUser);
            for (var j=0; j<clipRows[i].length; j++) {
                if (clipRows[i][j].length == 0) {
                    newRow.find('input, select').eq(j).val('');
                }
                else {
                    var option = newRow.find('option:contains("' + clipRows[i][j] + '")');
                    if(j == 3 && option.length > 0) {
                        newRow.find('input, select').eq(j).val(option.attr('value'));
                    }
                    else {
                        newRow.find('input, select').eq(j).val(clipRows[i][j]);
                    }
                }
            }
            importFunc.apply(newRow);
            importTab.addClass('edit-user-only');

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

    var previewTimeout = null;
    function previewImport() {
        var importTab = $('#import');
        if(previewTimeout != null)
            clearTimeout(previewTimeout);
        previewTimeout = setTimeout(function () {
            // select the first couple rows or limit to 1000 characters
            var first1000 = /[\s\S]{0,1000}/i;
            var match = first1000.exec(importTab.find('textarea').val());
            var preview = importTab.find('fieldset');
            preview.find('.import-row').remove();
            rowImport(match[0], preview);
        }, 1000);
    }

    body.on('mousedown', '#import textarea', previewImport);
    body.on('mouseup', '#import textarea', previewImport);
    body.on('change', '#import textarea', previewImport);
    body.on('focus', '#import textarea', previewImport);
    body.on('blur', '#import textarea', previewImport);
    body.on('keydown', '#import textarea', previewImport);
    body.on('keyup', '#import textarea', previewImport);

    body.on('click', '#import a[href="#import-group"]', function (evt) {
        evt.preventDefault();
        var importTab = $('#import');
        importTab.find('fieldset').find('.import-row').remove();
        rowImport(importTab.find('textarea').val());
        importTab.find('textarea').val('');
    });

    body.on('click', '#import a[href="#add-user"]', function (evt) {
        evt.preventDefault();
        var importTab = $('#import'),
            newUser = importTab.find('.import-row').first().clone()
            .removeClass('read-only historic').addClass('edit').insertBefore(importTab.find('.form-actions').first());
        newUser.find('.first-name input, .last-name input, .email input').val('');
        importFunc.apply(newUser);
    });

    body.on('click', '#import a[href="#save-group"]', function (evt) {
        evt.preventDefault();
        var importTab = $('#import'),
            users = [],
            rows = importTab.find('.import-row.edit.valid').not('fieldset .row');
        if(importTab.find('.form-actions').is('.invalid')) {
            return;
        }
        importTab.find('.form-actions').removeClass('valid').addClass('invalid');
        rows.each(function () {
            var that = jQuery(this);
            var newInvite = {
                first: that.find('.first-name input').val(),
                last: that.find('.last-name input').val(),
                email: that.find('.email input').val(),
            };
            if(that.find('.adviser select').length > 0) {
                newInvite.adviser = that.find('.adviser select').val()
            }
            users[users.length] = newInvite;
        });
        jQuery.ajax({
            url: window.callbackPaths['import_save'],
            type: 'POST',
            dataType: 'text',
            data: {
                users: users
            },
            success: function (data)
            {
                var content = $(data);
                importTab.find('.import-row').remove();
                content.find('.import-row').insertBefore(importTab.find('.form-actions'));
            }
        });
    });
});
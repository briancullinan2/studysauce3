jQuery(document).ready(function() {

    var body = $('body');

    function getHash()
    {
        var account = $(this);
        var data = gatherFields.apply(account, [['first', 'last', 'email', 'pass', 'csrf_token', 'new-password']]);
        var hash = '';
        for(var h in data) {
            if(data.hasOwnProperty(h)) {
                hash += data[h];
            }
        }
        return hash;
    }

    function accountFunc(evt) {
        var account = $(this);
        var results = account.closest('.results');
        var fields = ['first', 'last', 'email', 'password', 'csrf_token'];
        if(account.find('.new-password:visible').length > 0) {
            fields = $.merge(fields, ['new-password', 'confirm-password']);
        }
        var data = gatherFields.apply(account, [fields]);
        standardValidation.apply(account, [data]);
        account.find('.invite-row.edit').each(function () {
            var account = $(this);
            var data = gatherFields.apply(account, [['_code', 'childFirst', 'childLast', 'parent', 'year']]);
            if($(evt.target).is('.invite-row select')) {
                var newInvite = window.views.render('AdminBundle:Admin:cell-idSingleCoupon-invite.html.php', {'context' : account, 'results' : results.data('results'), 'request' : results.data('request')});
                account.find('.idSingleCoupon > *').remove();
                account.find('.idSingleCoupon').append(newInvite);
            }
            standardValidation.apply(account, [data]);
        });

    }

    body.on('show', '#account', function () {
        $(this).data('state', getHash());
    });

    body.on('validate', '[id^="account"] .results', accountFunc);
    body.on('change keyup keydown', '#account input, #account select, #account textarea', standardChangeHandler);

    body.on('click', '#account a[href="#edit-account"]', function (evt) {
        var account = jQuery('#account');
        evt.preventDefault();
        account.find('.ss_user-row').removeClass('read-only edit-pass').addClass('edit');
        accountFunc.apply(account.find('.results'));
    });

    body.on('click', '#account a[href="#edit-password"]', function (evt) {
        var account = jQuery('#account');
        evt.preventDefault();
        account.find('.ss_user-row').addClass('edit-pass');
        accountFunc.apply(account.find('.results'));
    });

    body.on('click', '#account a[href="#cancel-edit"]', function (evt) {
        var account = jQuery('#account');
        account.find('.ss_user-row').removeClass('edit-pass');
    });

    body.on('click', '#cancel-confirm a[href="#cancel-account"]', function (evt) {
        var account = jQuery('#account'),
            cancel = $('#cancel-confirm');
        evt.preventDefault();
        if(cancel.is('invalid'))
            return;
        cancel.removeClass('valid').addClass('invalid');
        jQuery.ajax({
            url: Routing.generate('cancel_payment'),
            type: 'POST',
            dataType: 'json',
            data: {
                cancel: true,
                csrf_token: account.find('input[name="csrf_token"]').val()
            },
            success: function () {
                // redirected and logged out automatically by server
                $('#cancel-confirm').modal('hide');
                account.find('.type label').replaceWith('<label><span>Account type</span>Free</label>');
            },
            error: function () {
                cancel.removeClass('invalid').addClass('valid');
            }
        });
    });

    body.on('click', '#account .social-login a[href*="#remove-"]', function (evt) {
        evt.preventDefault();
        var account = $('#account');
        jQuery.ajax({
            url: Routing.generate('remove_social'),
            type: 'POST',
            dataType: 'text',
            data: {
                remove: $(this).attr('href').substr(8),
                csrf_token: account.find('input[name="csrf_token"]').val()
            },
            success: function (data) {
                var response = $(data);
                account.find('input[name="csrf_token"]').val(response.find('input[name="csrf_token"]').val());
                account.find('.social-login').replaceWith(response.find('.social-login'));
            }
        });
    });

    function submitAccount(evt)
    {
        evt.preventDefault();
        var account = $(this).parents('.panel-pane');
        var hash = getHash();
        var data = gatherFields.apply($(this), [['first', 'last', 'email', 'pass', 'csrf_token', 'new-password', '_remember_me', 'confirm-password', '_code', 'childFirst', 'childLast']]);

        if(account.find('.highlighted-link').is('.invalid')) {
            account.addClass('invalid has-error');
        }
        else {
            account.removeClass('invalid has-error');
        }

        // cancel if invalid
        var saveButton = account.find('.highlighted-link [href^="#save-"], .highlighted-link [value^="#save-"]').first();
        if (saveButton.is('.read-only > *, [disabled], .invalid, .invalid > *')) {
            gotoError.apply(this);
            return;
        }

        standardSave.apply($(this), [data, function () {
            account.data('state', hash);
            if(typeof data.error != 'undefined') {
                account.find('.form-actions').prepend($('<span class="error">' + data.error + '</span>'));
            }
            account.find('.ss_user-row, .invite-row').removeClass('edit edit-pass').addClass('read-only');
            account.find('.pass input, .new-password input, .confirm-password input').val('');
        }]);
    }

    body.on('submit', '#account form', submitAccount);

});
jQuery(document).ready(function() {

    var body = $('body');

    function getHash()
    {
        var account = jQuery('#account');
        return account.find('.first-name input').val().trim() + account.find('.last-name input').val().trim() +
            account.find('.email input').val().trim() + account.find('.password input').val() +
            account.find('.new-password input').val();
    }

    function accountFunc() {
        var account = jQuery('#account');
        var valid = true;

        if (account.find('.password input').val() == '') {
            account.addClass('password-required');
        }
        else {
            account.removeClass('password-required');
        }
        if(account.find('.new-password').css('visibility') != 'hidden' && account.find('.new-password input').val().trim() == '') {
            account.addClass('new-password-required');
        }
        else {
            account.removeClass('new-password-required');
        }
        if(account.find('.new-password').css('visibility') != 'hidden' &&
            account.find('.new-password input').val() != account.find('.confirm-password input').val()) {
            account.addClass('confirm-required');
        }
        else {
            account.removeClass('confirm-required');
        }
        if(account.find('.first-name input').val() == '') {
            account.addClass('first-required');
        }
        else {
            account.removeClass('first-required');
        }
        if(account.find('.last-name input').val() == '') {
            account.addClass('last-required');
        }
        else {
            account.removeClass('last-required');
        }
        if(account.find('.email input').val().trim() == '' ||
            !(/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}\b/i).test(account.find('.email input').val())) {
            account.addClass('email-required');
        }
        else {
            account.removeClass('email-required');
        }

        if (getHash() == account.data('state') || account.is('.password-required') || account.is('.new-password-required') ||
            account.is('.first-required') || account.is('.last-required') || account.is('.email-required') || account.is('.confirm-required')) {
            account.find('.form-actions').removeClass('valid').addClass('invalid');
        }
        else {
            account.removeClass('invalid-only').find('.form-actions').removeClass('invalid').addClass('valid');
            account.find('.form-actions .error').remove();
        }
    }

    body.on('show', '#account', function () {
        $(this).data('state', getHash());
        accountFunc();
    });

    body.on('change keyup keydown', '#account input', accountFunc);

    body.on('click', '#account a[href="#edit-account"]', function (evt) {
        var account = jQuery('#account');
        evt.preventDefault();
        account.find('.account-info').removeClass('read-only').addClass('edit');
        account.find('.password').css('visibility', 'visible');
        account.find('.new-password, .confirm-password').css('visibility', 'hidden');
        account.find('.edit-icons').toggle();
        account.find('[value="#save-account"]').show();
        account.find('.social-login').hide();
        accountFunc();
    });

    body.on('click', '#account a[href="#edit-password"]', function (evt) {
        var account = jQuery('#account');
        evt.preventDefault();
        account.find('.password, .new-password, .confirm-password').css('visibility', 'visible');
        account.find('.edit-icons').toggle();
        account.find('[value="#save-account"]').show();
        account.find('.social-login').hide();
        accountFunc();
    });

    body.on('click', '#cancel-confirm a[href="#cancel-account"]', function (evt) {
        var account = jQuery('#account'),
            cancel = $('#cancel-confirm');
        evt.preventDefault();
        if(cancel.is('invalid'))
            return;
        cancel.removeClass('valid').addClass('invalid');
        jQuery.ajax({
            url: window.callbackPaths['cancel_payment'],
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
            url: window.callbackPaths['remove_social'],
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
        var account = jQuery('#account');
        if(account.find('.form-actions').is('.invalid')) {
            if (account.is('.password-required') || account.is('.new-password-required') || account.is('.first-required') ||
                account.is('.last-required') || account.is('.email-required') || account.is('.confirm-required')) {
                account.addClass('invalid-only');
                if(account.is('.password-required')) {
                    account.find('.password input').focus();
                }
                else if(account.is('.new-password-required')) {
                    account.find('.new-password input').focus();
                }
                else if(account.is('.confirm-required')) {
                    account.find('.confirm-password input').focus();
                }
                else if(account.is('.first-required')) {
                    account.find('.first-name input').focus();
                }
                else if(account.is('.last-required')) {
                    account.find('.last-name input').focus();
                }
                else if(account.is('.email-required')) {
                    account.find('.email input').focus();
                }
            }
            return;
        }
        account.find('.form-actions').removeClass('valid').addClass('invalid');
        loadingAnimation($(this).find('[value="#save-account"]'));
        var hash = getHash();

        jQuery.ajax({
            url:window.callbackPaths['account_update'],
            type: 'POST',
            dataType: 'json',
            data: {
                first: account.find('.first-name input').val(),
                last: account.find('.last-name input').val(),
                email: account.find('.email input').val(),
                pass: account.find('.password input').val(),
                newPass: account.find('.new-password input').val(),
                csrf_token: account.find('input[name="csrf_token"]').val()
            },
            success: function (data) {
                account.find('.squiggle').stop().remove();
                account.find('input[name="csrf_token"]').val(data.csrf_token);
                account.data('state', hash);
                if(typeof data.error != 'undefined') {
                    account.find('.form-actions').prepend($('<span class="error">' + data.error + '</span>'));
                }
                account.find('.password input, .new-password input, .confirm-password input').val('');
                account.find('.edit-icons').toggle();
                account.find('[value="#save-account"]').hide();
                account.find('.account-info').removeClass('edit').addClass('read-only');
                account.find('.password, .new-password, .confirm-password').css('visibility', 'hidden');
                account.find('.social-login').show();
            },
            error: function () {
                account.find('.squiggle').stop().remove();
            }
        });
    }

    body.on('submit', '#account form', submitAccount);

});
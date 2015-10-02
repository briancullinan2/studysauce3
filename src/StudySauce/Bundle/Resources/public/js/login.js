jQuery(document).ready(function() {

    var body = $('body');

    function accountFunc() {
        var account = $(this).closest('#login');

        if (account.find('.email input').val().trim() == '' ||
            !(/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}\b/i).test(account.find('.email input').val())) {
            account.addClass('email-required');
        }
        else {
            account.removeClass('email-required');
        }
        if (account.find('.password input').val().trim() == '') {
            account.addClass('password-required');
        }
        else {
            account.removeClass('password-required');
        }

        if (account.is('.email-required') || account.is('.password-required')) {
            account.find('.form-actions').removeClass('valid').addClass('invalid');
        }
        else {
            account.removeClass('invalid-only').find('.form-actions').removeClass('invalid').addClass('valid');
            account.find('.form-actions .error').remove();
        }
    }

    body.on('show', '#login', function () {
        accountFunc.apply(this);
    });
    body.on('change keyup keydown', '#login .email input, #login .password input', accountFunc);

    function submitLogin(evt)
    {
        evt.preventDefault();
        var account = $(this).closest('#login');
        if(account.find('.form-actions').is('.invalid')) {
            account.addClass('invalid-only');
            if(account.is('.email-required')) {
                account.find('.email input').focus();
            }
            else if(account.is('.password-required')) {
                account.find('.password input').focus();
            }
            return;
        }

        account.find('.form-actions .error').remove();
        account.find('.form-actions').removeClass('valid').addClass('invalid');
        loadingAnimation(account.find('[value="#user-login"]'));

        jQuery.ajax({
            url:window.callbackPaths['account_auth'],
            type: 'POST',
            dataType: 'json',
            data: {
                _remember_me: 'on',
                email: account.find('.email input').val(),
                pass: account.find('.password input').val(),
                csrf_token: account.find('input[name="csrf_token"]').val()
            },
            success: function (data) {
                account.find('.squiggle').stop().remove();
                account.find('input[name="csrf_token"]').val(data.csrf_token);
                if(typeof data.redirect != 'undefined' && (/\/login/i).test(data.redirect))
                {
                    account.find('.form-actions').prepend($('<span class="error">Invalid password</span>'));
                }
                account.find('.password input').val('');
            },
            error: function (data) {
                account.find('.squiggle').stop().remove();
                account.find('input[name="csrf_token"]').val(data.csrf_token);
            }
        });
    }
    body.on('submit', '#login form', submitLogin);

});
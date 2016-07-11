jQuery(document).ready(function() {

    var body = $('body');

    function getHash()
    {
        var account = jQuery('#register');
        return account.find('.first-name input').val().trim() + account.find('.last-name input').val().trim() +
                account.find('.email input').val().trim() + account.find('.password input').val().trim();
    }

    function accountFunc() {
        var account = jQuery('#register');
        var valid = true;
        if (account.find('.first-name input').val() == '') {
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
        if(account.find('.password input').val() == '') {
            account.addClass('password-required');
        }
        else {
            account.removeClass('password-required');
        }

        if (getHash() == account.data('state') || account.is('.first-required') || account.is('.last-required') ||
            account.is('.email-required') || account.is('.password-required'))
            account.find('.form-actions').removeClass('valid').addClass('invalid');
        else {
            account.removeClass('invalid-only').find('.form-actions').removeClass('invalid').addClass('valid');
            account.find('.form-actions .error').remove();
        }
    }
    body.on('show', '#register', function () {
        if($(this).data('state') == null)
            $(this).data('state', getHash());
        accountFunc();
    });

    body.on('change', '[id^="register"] .hasChild input, #register_child select', function () {
        var tab = $(this).parents('.panel-pane');
        Cookies.set('hasChild', $(this).is(':checked') ? 'true' : 'false');
        window.views.render.apply(tab, [tab.attr('id'), {context: tab}]);
    });

    body.on('change keyup keydown', '#register .first-name input, #register .last-name input, #register .email input, #register .password input', accountFunc);

    body.on('click', '#register a[href="#sign-in-with-email"]', function (evt) {
        var account = jQuery('#register');
        evt.preventDefault();
        $(this).remove();
        account.find('form').show();
        accountFunc();
    });

    body.on('submit', '#register form', function (evt) {
        var account = jQuery('#register');
        evt.preventDefault();
        var hash = getHash();
        var data = gatherFields.apply($(this), [['first', 'last', 'email', 'password', 'csrf_token', '_code', '_remember_me', 'hasChild', 'childFirst', 'childLast']]);
        standardSave.apply($(this), [data, function () {
            account.data('state', hash);
        }]);
        $(this).find('.form-actions').removeClass('invalid').addClass('valid');
    });

    function resetFunc() {
        var reset = $('#reset');
        if(reset.find('.email input').val().trim() != '' &&
            (reset.find('.password input:visible').length == 0 || (reset.find('.password input').val().trim() != '' &&
            reset.find('.password input').val() == reset.find('.confirm-password input').val())))
            reset.find('.form-actions').removeClass('invalid').addClass('valid');
        else
            reset.find('.form-actions').removeClass('valid').addClass('invalid');
    }

    body.on('show', '#reset', resetFunc);
    body.on('keyup', '#reset input', resetFunc);
    body.on('change', '#reset input', resetFunc);
    function submitReset(evt)
    {
        evt.preventDefault();
        var account = jQuery('#reset');
        if(account.find('.form-actions').is('.invalid'))
            return;

        account.find('.form-actions .error').remove();
        account.find('.form-actions').removeClass('valid').addClass('invalid');
        loadingAnimation($(this).find('[value="#reset-password"]'));
        jQuery.ajax({
            url: Routing.generate('reset'),
            type: 'POST',
            dataType: 'json',
            data: {
                email: account.find('.email input').val().trim(),
                token: account.find('input[name="token"]').val(),
                newPass: account.find('.password input').val(),
                csrf_token: account.find('input[name="csrf_token"]').val()
            },
            success: function (data) {
                account.find('.squiggle').stop().remove();
                account.find('input[name="csrf_token"]').val(data.csrf_token);
                if(typeof data.error != 'undefined' && data.error != null) {
                    account.find('.form-actions').prepend($('<span class="error">' + data.error + '</span>'));
                }
                else if (account.find('input[name="token"]').val() == '') {
                    account.addClass('reset-sent');
                }
            },
            error: function () {
                account.find('.squiggle').stop().remove();
            }
        });
    }
    body.on('submit', '#reset form', submitReset);

});
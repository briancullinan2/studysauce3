jQuery(document).ready(function() {

    var body = $('body');

    function getHash()
    {
        var account = $(this);
        var data = gatherFields.apply(account, [['first', 'last', 'email', 'password', 'csrf_token', '_code', '_remember_me', 'hasChild', 'childFirst', 'childLast']]);
        var hash = '';
        for(var h in data) {
            if(data.hasOwnProperty(h)) {
                hash += data[h];
            }
        }
        return hash;
    }

    body.on('show', '[id^="register"]', function () {
        if($(this).data('state') == null)
            $(this).data('state', getHash.apply(this));
    });

    function accountFunc(evt) {
        var account = $(this).closest('.panel-pane');
        var data = gatherFields.apply(account, [['first', 'last', 'email', 'password', '_code', 'childFirst', 'childLast', 'parent', 'year']]);
        if($(evt.target).is('.hasChild input, #register_child select')) {
            Cookies.set('hasChild', account.find('.hasChild input').is(':checked') ? 'true' : 'false');
            window.views.render.apply(account, [account.attr('id'), {context: account}]);
        }
        standardValidation.apply(account, [data]);
    }

    body.on('validate', '[id^="register"]', accountFunc);
    body.on('change keyup keydown', '[id^="register"] input, [id^="register"] select, [id^="register"] textarea', standardChangeHandler);

    body.on('click', '#register a[href="#sign-in-with-email"]', function (evt) {
        var account = jQuery('#register');
        evt.preventDefault();
        $(this).remove();
        account.find('form').show();
        accountFunc.apply(this);
    });

    body.on('submit', '[id^="register"] form', function (evt) {
        var account = $(this).parents('.panel-pane');
        evt.preventDefault();
        var hash = getHash.apply(this);
        var data = gatherFields.apply($(this), [['first', 'last', 'email', 'password', 'csrf_token', '_code', '_remember_me', 'hasChild', 'childFirst', 'childLast']]);
        account.trigger('validate');
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
            account.find('.childFirst input, .childLast input, .parent select, .year select, ._code select').val('');
        }]);
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
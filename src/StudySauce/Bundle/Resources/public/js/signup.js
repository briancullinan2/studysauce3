
jQuery(document).ready(function($) {
    var body = $('body');


    function signupFunc()
    {
        var signup = $('#signup');
        if(signup.find('#billing-pane .first-name input').val().trim() == '') {
            signup.addClass('first-required');
        }
        else {
            signup.removeClass('first-required');
        }
        if(signup.find('#billing-pane .email input').val().trim() == ''
            || !(/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}\b/i)
                .test(signup.find('#billing-pane .email input').val())) {
            signup.addClass('email-required');
        }
        else {
            signup.removeClass('email-required');
        }
        if(signup.find('input[name="street1"]').val().trim() == '') {
            signup.addClass('street-required');
        }
        else {
            signup.removeClass('street-required');
        }
        if(signup.find('.city input').val().trim() == '') {
            signup.addClass('city-required');
        }
        else {
            signup.removeClass('city-required');
        }
        if(signup.find('.zip input').val().trim() == '') {
            signup.addClass('zip-required');
        }
        else {
            signup.removeClass('zip-required');
        }
        if(signup.find('select[name="state"]').val().trim() == '') {
            signup.addClass('state-required');
        }
        else {
            signup.removeClass('state-required');
        }
        if(signup.find('select[name="country"]').val().trim() == '') {
            signup.addClass('country-required');
        }
        else {
            signup.removeClass('country-required');
        }
        if(signup.find('#payment-pane').is(':visible')) {
            if (signup.find('input[name="cc-number"]').val().trim() == '') {
                signup.addClass('cc-number-required');
            }
            else {
                signup.removeClass('cc-number-required');
            }
            if (signup.find('select[name="cc-month"]').val().trim() == '') {
                signup.addClass('cc-month-required');
            }
            else {
                signup.removeClass('cc-month-required');
            }
            if (signup.find('select[name="cc-year"]').val().trim() == '') {
                signup.addClass('cc-year-required');
            }
            else {
                signup.removeClass('cc-year-required');
            }
            if (signup.find('input[name="cc-ccv"]').val().trim() == '') {
                signup.addClass('cc-ccv-required');
            }
            else {
                signup.removeClass('cc-ccv-required');
            }
        }
        else {
            signup.removeClass('cc-number-required');
            signup.removeClass('cc-month-required');
            signup.removeClass('cc-year-required');
            signup.removeClass('cc-ccv-required');
        }
        if(signup.find('input[name="organization"]').val().trim() == '') {
            signup.addClass('organization-required');
        }
        else {
            signup.removeClass('organization-required');
        }
        if(signup.find('input[name="phone"]').val().trim() == '') {
            signup.addClass('phone-required');
        }
        else {
            signup.removeClass('phone-required');
        }
        if(signup.find('input[name="phone"]').val().trim() == '') {
            signup.addClass('phone-required');
        }
        else {
            signup.removeClass('phone-required');
        }
        if(signup.find('input[name="students"]').val().trim() == ''
            || isNaN(parseInt(signup.find('input[name="students"]').val()))
            || parseInt(signup.find('input[name="students"]').val()) <= 0) {
            signup.addClass('students-required');
        }
        else {
            signup.removeClass('students-required');
        }
        if(signup.find('.payment select').val().trim() == '') {
            signup.addClass('payment-required');
        }
        else {
            signup.removeClass('payment-required');
        }

        if(signup.is('.first-required') || signup.is('.email-required')
            || signup.is('.street-required') || signup.is('.city-required') || signup.is('.zip-required')
            || signup.is('.state-required') || signup.is('.country-required') || signup.is('.cc-number-required')
            || signup.is('.cc-month-required') || signup.is('.cc-year-required') || signup.is('.cc-ccv-required')
            || signup.is('.payment-required'))
            signup.find('.form-actions').removeClass('valid').addClass('invalid');
        else {
            signup.removeClass('invalid-only').find('.form-actions').removeClass('invalid').addClass('valid');
            signup.find('.form-actions .error').remove();
        }

        /*
        if(signup.find('.payment select').val() == 'Credit card') {
            signup.find('#payment-pane').css('display', 'inline-block');
        }
        else {
            signup.find('#payment-pane').hide();
        }
        */
    }

    body.on('show', '#signup', signupFunc);
    body.on('change', '#signup input, #signup select', signupFunc);
    body.on('keyup', '#signup input[type="text"]', signupFunc);

    body.on('click', '#signup a[href="#business-order"]', function (evt) {
        var signup = $('#signup');
        evt.preventDefault();
        if(!signup.find('.form-actions').is('.valid')) {
            signup.addClass('invalid-only');
            if(signup.is('.organization-required')) {
                signup.find('#billing-pane .organization input').focus();
            }
            else if(signup.is('.street-required')) {
                signup.find('#billing-pane .street1 input').focus();
            }
            else if(signup.is('.city-required')) {
                signup.find('#billing-pane .city input').focus();
            }
            else if(signup.is('.zip-required')) {
                signup.find('#billing-pane .zip input').focus();
            }
            else if(signup.is('select[name="state"]')) {
                signup.find('#billing-pane select[name="state"]').focus();
            }
            else if(signup.is('.country-required')) {
                signup.find('#billing-pane select[name="country"]').focus();
            }
            else if(signup.is('.first-required')) {
                signup.find('#billing-pane .first-name input').focus();
            }
            else if(signup.is('.title-required')) {
                signup.find('#billing-pane .title input').focus();
            }
            else if(signup.is('.email-required')) {
                signup.find('#billing-pane .email input').focus();
            }
            else if(signup.is('.phone-required')) {
                signup.find('#billing-pane .phone input').focus();
            }
            else if(signup.is('.students-required')) {
                signup.find('#billing-pane .students input').focus();
            }
            else if(signup.is('.payment-required')) {
                signup.find('#billing-pane .payment select').focus();
            }
            return;
        }

        signup.find('.form-actions').removeClass('valid').addClass('invalid');
        loadingAnimation(signup.find('[value="#business-order"]'));

        $.ajax({
            url: window.callbackPaths['signup_save'],
            type: 'POST',
            dataType: 'json',
            data: {
                organization: signup.find('#billing-pane .organization input').val().trim(),
                phone: signup.find('#billing-pane .phone input').val().trim(),
                title: signup.find('#billing-pane .title input').val().trim(),
                first: signup.find('#billing-pane .first-name input').val().trim(),
                email: signup.find('#billing-pane .email input').val().trim(),
                street1: signup.find('input[name="street1"]').val().trim(),
                street2: signup.find('input[name="street2"]').val().trim(),
                city: signup.find('.city input').val().trim(),
                zip: signup.find('.zip input').val().trim(),
                state: signup.find('select[name="state"]').val().trim(),
                country: signup.find('select[name="country"]').val().trim(),
                students: signup.find('input[name="students"]').val().trim(),
                payment: signup.find('.payment select').val() /* signup.find('form').is(':visible') ? {
                    number: signup.find('input[name="cc-number"]').val().trim(),
                    month: signup.find('select[name="cc-month"]').val().trim(),
                    year: signup.find('select[name="cc-year"]').val().trim(),
                    ccv: signup.find('input[name="cc-ccv"]').val().trim()
                } : null */
            },
            success: function (data) {
                signup.find('.squiggle').stop().remove();
                // this should redirect anyways
                if(typeof data.error != 'undefined') {
                    signup.find('.form-actions').prepend($('<span class="error">' + data.error + '</span>'));
                }
            },
            error: function () {
                signup.find('.squiggle').stop().remove();
            }
        });
    });

});
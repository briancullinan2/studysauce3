
jQuery(document).ready(function($) {
    var body = $('body');

    body.on('click', '[id^="store"] button, [id^="store"] a[href="#remove-coupon"]', function () {
        var cart = (Cookies.get('cart') || '').split(',');
        if(cart[0] == '') {
            cart.splice(0);
        }
        if($(this).is('a[href="#remove-coupon"]')) {
            var removeI = cart.indexOf($(this).data('value'));
            if(removeI > -1) {
                cart.splice(removeI);
            }
        }
        else {
            cart[cart.length] = $(this).val();
        }
        Cookies.set('cart', cart.join(','));
        var row = $(this).parents('.coupon-row');
        var results = $(this).parents('.results');
        var request = results.data('request');
        var resultsObj = results.data('results');
        window.views.render.apply(results, ['results', {tables: request.tables, request: request, results: resultsObj, context: results}]);
    });

    body.on('showing', '[id^="store"]', function () {
        var results = $(this).find('.results');
        var request = results.data('request');
        var resultsObj = results.data('results');
        if(resultsObj) {
            window.views.render.apply(results, ['results', {
                tables: request.tables,
                request: request,
                results: resultsObj,
                context: results
            }]);
        }
    });

    function checkoutFunc()
    {
        var checkout = $('#checkout');
        var valid = false,
            valid2 = false;
        if(checkout.find('#billing-pane .first-name input').val().trim() == '') {
            checkout.addClass('first-required');
        }
        else {
            checkout.removeClass('first-required');
        }
        if(checkout.find('#billing-pane .last-name input').val().trim() == '') {
            checkout.addClass('last-required');
        }
        else {
            checkout.removeClass('last-required');
        }
        if(checkout.find('#billing-pane .email input').val().trim() == '' ||
            !(/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}\b/i).test(checkout.find('#billing-pane .email input').val())) {
            checkout.addClass('email-required');
        }
        else {
            checkout.removeClass('email-required');
        }
        if(checkout.find('input[name="street1"]').val().trim() == '') {
            checkout.addClass('street-required');
        }
        else {
            checkout.removeClass('street-required');
        }
        if(checkout.find('.city input').val().trim() == '') {
            checkout.addClass('city-required');
        }
        else {
            checkout.removeClass('city-required');
        }
        if(checkout.find('.zip input').val().trim() == '') {
            checkout.addClass('zip-required');
        }
        else {
            checkout.removeClass('zip-required');
        }
        if(checkout.find('select[name="state"]').val().trim() == '') {
            checkout.addClass('state-required');
        }
        else {
            checkout.removeClass('state-required');
        }
        if(checkout.find('select[name="country"]').val().trim() == '') {
            checkout.addClass('country-required');
        }
        else {
            checkout.removeClass('country-required');
        }
        if(checkout.find('input[name="cc-number"]').val().trim() == '') {
            checkout.addClass('cc-number-required');
        }
        else {
            checkout.removeClass('cc-number-required');
        }
        if(checkout.find('select[name="cc-month"]').val().trim() == '') {
            checkout.addClass('cc-month-required');
        }
        else {
            checkout.removeClass('cc-month-required');
        }
        if(checkout.find('select[name="cc-year"]').val().trim() == '') {
            checkout.addClass('cc-year-required');
        }
        else {
            checkout.removeClass('cc-year-required');
        }
        if(checkout.find('input[name="cc-ccv"]').val().trim() == '') {
            checkout.addClass('cc-ccv-required');
        }
        else {
            checkout.removeClass('cc-ccv-required');
        }
        if(checkout.find('input[name="password"]:visible').length > 0 && checkout.find('input[name="password"]:visible').val().trim() == '') {
            checkout.addClass('password-required');
        }
        else {
            checkout.removeClass('password-required');
        }

        if(checkout.is('.reoccurs-required, .first-required, .last-required, .email-required, .street-required, ' +
            '.city-required, .zip-required, .state-required, .country-required, .cc-number-required, ' +
            '.cc-month-required, .cc-year-required, .cc-ccv-required, .password-required, .gift-first-required' +
            '.gift-last-required, .gift-email-required'))
            checkout.find('.form-actions').removeClass('valid').addClass('invalid');
        else {
            checkout.removeClass('invalid-only').find('.form-actions').removeClass('invalid').addClass('valid');
            checkout.find('.form-actions .error').remove();
        }
    }

    body.on('show', '#checkout', checkoutFunc);
    body.on('change', '#checkout input, #checkout select', checkoutFunc);
    body.on('keyup', '#checkout input[type="text"], #checkout input[type="password"]', checkoutFunc);

    body.on('click', '#checkout a[href="#show-coupon"]', function (evt) {
        var checkout = $('#checkout');
        evt.preventDefault();
        $(this).hide();
        checkout.find('#coupon-pane')
            .css('display', 'inline-block')
            .css('visibility', 'visible')
            .animate({opacity:1,height: 100});
    });

    body.on('click', '#checkout a[href="#show-gift"]', function (evt) {
        var checkout = $('#checkout');
        evt.preventDefault();
        $(this).hide();
        checkout.find('#gift-pane')
            .css('display', 'inline-block')
            .css('visibility', 'visible')
            .animate({opacity:1,height: 166});
    });

    body.on('click', '#checkout a[href="#coupon-apply"]', function (evt) {
        var checkout = $('#checkout');
        evt.preventDefault();
        checkout.find('.form-actions .error').remove();
        $.ajax({
            url: Routing.generate('checkout_coupon'),
            type: 'POST',
            dataType: 'text',
            data: {
                coupon: checkout.find('.coupon-code input').val().trim()
            },
            success: function (data) {
                var content = $(data);
                if (typeof data.error != 'undefined') {
                    checkout.find('.form-actions').prepend($('<span class="error">' + data.error + '</span>'));
                }
                else {
                    // set new line item
                    checkout.find('.product-option').replaceWith(content.find('.product-option'));

                    // update coupon pane
                    checkout.find('#coupon-pane').html(content.find('#coupon-pane').html());
                }
            }
        });
    });

    body.on('click', '#checkout a[href="#coupon-remove"]', function (evt) {
        var checkout = $('#checkout');
        evt.preventDefault();
        checkout.find('.form-actions .error').remove();
        $.ajax({
            url: Routing.generate('checkout_coupon'),
            type: 'POST',
            dataType: 'text',
            data: {
                remove: true
            },
            success: function (data) {
                var content = $(data);
                if (typeof data.error != 'undefined') {
                    checkout.find('.form-actions').prepend($('<span class="error">' + data.error + '</span>'));
                }
                else {
                    // set new line item
                    checkout.find('.product-option').replaceWith(content.find('.product-option'));

                    // update coupon pane
                    checkout.find('#coupon-pane').html(content.find('#coupon-pane').html());
                }
            }
        });
    });

    body.on('click', '#checkout a[href="#submit-order"]', function (evt) {
        var checkout = $('#checkout');
        evt.preventDefault();
        if(!checkout.find('.form-actions').is('.valid')) {
            checkout.addClass('invalid-only');
            if(checkout.is('.first-required')) {
                checkout.find('#billing-pane .first-name input').focus();
            }
            else if(checkout.is('.last-required')) {
                checkout.find('#billing-pane .last-name input').focus();
            }
            else if(checkout.is('.email-required')) {
                checkout.find('#billing-pane .email-name input').focus();
            }
            else if(checkout.is('.password-required')) {
                checkout.find('#billing-pane input[name="password"]').focus();
            }
            else if(checkout.is('.street-required')) {
                checkout.find('#billing-pane input[name="street1"]').focus();
            }
            else if(checkout.is('.city-required')) {
                checkout.find('#billing-pane .city input').focus();
            }
            else if(checkout.is('.zip-required')) {
                checkout.find('#billing-pane .zip input').focus();
            }
            else if(checkout.is('.state-required')) {
                checkout.find('#billing-pane select[name="state"]').focus();
            }
            else if(checkout.is('.country-required')) {
                checkout.find('#billing-pane select[name="country"]').focus();
            }
            else if(checkout.is('.reoccurs-required')) {
                checkout.find('input[name="reoccurs"]:checked').first().focus();
            }
            else if(checkout.is('.cc-number-required')) {
                checkout.find('#payment-pane input[name="cc-number"]').focus();
            }
            else if(checkout.is('.cc-month-required')) {
                checkout.find('#payment-pane select[name="cc-month"]').focus();
            }
            else if(checkout.is('.cc-year-required')) {
                checkout.find('#payment-pane select[name="cc-year"]').focus();
            }
            else if(checkout.is('.cc-ccv-required')) {
                checkout.find('#payment-pane input[name="cc-ccv"]').focus();
            }
            else if(checkout.is('.gift-first-required')) {
                checkout.find('#gift-pane .first-name input').focus();
            }
            else if(checkout.is('.gift-last-required')) {
                checkout.find('#gift-pane .last-name input').focus();
            }
            else if(checkout.is('.gift-email-required')) {
                checkout.find('#gift-pane .email input').focus();
            }
            return;
        }

        checkout.find('.form-actions').removeClass('valid').addClass('invalid');
        loadingAnimation(checkout.find('a[href="#submit-order"]'));

        var data = gatherFields.apply(checkout, [['first', 'last']]);
        debugger;

        $.ajax({
            url: Routing.generate('checkout_pay'),
            type: 'POST',
            dataType: 'json',
            data: {
                reoccurs: checkout.find('input[name="reoccurs"]:checked').val().trim(),
                first: checkout.find('#billing-pane .first-name input').val().trim(),
                last: checkout.find('#billing-pane .last-name input').val().trim(),
                email: checkout.find('#billing-pane .email input').val().trim(),
                pass: checkout.find('input[name="password"]:visible').length == 0 ? null : checkout.find('input[name="password"]:visible').val(),
                street1: checkout.find('input[name="street1"]').val().trim(),
                street2: checkout.find('input[name="street2"]').val().trim(),
                city: checkout.find('.city input').val().trim(),
                zip: checkout.find('.zip input').val().trim(),
                state: checkout.find('select[name="state"]').val().trim(),
                country: checkout.find('select[name="country"]').val().trim(),
                number: checkout.find('input[name="cc-number"]').val().trim(),
                month: checkout.find('select[name="cc-month"]').val().trim(),
                year: checkout.find('select[name="cc-year"]').val().trim(),
                ccv: checkout.find('input[name="cc-ccv"]').val().trim(),
                invite: checkout.find('#gift-pane').is(':visible') ? {
                    first: checkout.find('#gift-pane .first-name input').val().trim(),
                    last: checkout.find('#gift-pane .last-name input').val().trim(),
                    email: checkout.find('#gift-pane .email input').val().trim()
                } : null
            },
            success: function (data) {
                checkout.find('.squiggle').stop().remove();
                // this should redirect anyways
                if(typeof data.error != 'undefined') {
                    checkout.find('.form-actions').prepend($('<span class="error">' + data.error + '</span>'));
                }
            },
            error: function () {
                checkout.find('.squiggle').stop().remove();
            }
        });
    });

});
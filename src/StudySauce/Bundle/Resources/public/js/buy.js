
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


    function checkoutFunc() {
        var account = $(this);
        var valid = true;
        var data = gatherFields.apply(account, [['first', 'last', 'password', 'street1', 'city', 'zip', 'state', 'country', 'number', 'month', 'year', 'ccv']]);
        for(var d in data) {
            if(data.hasOwnProperty(d)) {
                if(data[d] == '') {
                    account.find('label.' + d).addClass('invalid');
                }
                else {
                    account.find('label.' + d).removeClass('invalid');
                }
                if(d == 'email') {
                    if(!(/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}\b/i).test(account.find('.email input').val())) {
                        account.find('label.' + d).addClass('invalid');
                    }
                }
            }
        }

        if (account.find('label.input.invalid').length > 0)
            account.find('.form-actions').removeClass('valid').addClass('invalid');
        else {
            account.removeClass('invalid-only').find('.form-actions').removeClass('invalid').addClass('valid');
            account.find('.form-actions .error').remove();
        }
    }

    body.on('validate', '[id^="checkout"]', checkoutFunc);
    body.on('change keyup keydown', '[id^="checkout"] input, [id^="checkout"] select, [id^="checkout"] textarea', standardChangeHandler);

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

    body.on('submit', '#checkout form', function (evt) {
        var account = $(this).parents('.panel-pane');
        evt.preventDefault();
        var data = gatherFields.apply(account, [['first', 'last', 'password', 'csrf_token', 'street1', 'street2', 'city', 'zip', 'state', 'country', 'number', 'month', 'year', 'ccv']]);
        account.trigger('validate');
        if(account.find('.highlighted-link').is('.invalid')) {
            account.addClass('invalid has-error');
        }
        else {
            account.removeClass('invalid has-error');
        }
        gotoError.apply(this);
        standardSave.apply(this, [data, function () {

        }]);
    });

});
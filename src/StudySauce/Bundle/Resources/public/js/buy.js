
jQuery(document).ready(function($) {
    var body = $('body');

    body.on('click', '[id^="store"]:not(#store_cart) button, [id^="store"] a[href="#remove-coupon"]', function () {
        var cart = (Cookies.get('cart') || '').split(',');
        if(cart[0] == '') {
            cart.splice(0);
        }
        if($(this).is('a[href="#remove-coupon"]')) {
            var removeI = cart.indexOf($(this).parents('.coupon-row').find('[name="coupon"]').val());
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

    function cartFunc() {
        var account = $(this);
        var valid = true;
        account.find('.coupon-row').each(function () {
            var data = gatherFields.apply(account, [['child']]);
            if(data.child == '') {
                valid = false;
                $(this).addClass('invalid');
            }
            else {
                $(this).removeClass('invalid');
            }
        });
        if(!valid) {
            account.find('.form-actions').removeClass('valid').addClass('invalid');
        }
        else {
            account.removeClass('invalid-only').find('.form-actions').removeClass('invalid').addClass('valid');
        }
    }

    body.on('validate', '[id^="store_cart"]', cartFunc);
    body.on('change keyup keydown', '[id^="store_cart"] input, [id^="store_cart"] select, [id^="store_cart"] textarea', standardChangeHandler);
    body.on('submit', '#store_cart form', function (evt) {
        var account = $(this).parents('.panel-pane');
        evt.preventDefault();
        account.trigger('validate');
        if(account.find('.highlighted-link').is('.invalid')) {
            account.addClass('invalid has-error');
        }
        else {
            account.removeClass('invalid has-error');
        }
        var data = {coupon: '', child: {}};
        account.find('.coupon-row').each(function () {
            var couponData = gatherFields.apply(account, [['child', 'coupon']]);
            data.coupon += (data.coupon != '' ? ',' : '') + couponData.coupon;
            data.child[couponData.child] = couponData.coupon;
        });
        gotoError.apply(this);
        standardSave.apply(this, [data, function () {

        }]);
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
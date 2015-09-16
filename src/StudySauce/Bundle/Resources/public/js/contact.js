$(document).ready(function () {

    // TODO: bring back chat
    var body = $('body');

    function validateContact()
    {
        var contact = $(this).closest('#contact-support');
        if(contact.find('.name input').val().trim() == '') {
            contact.addClass('name-required');
        }
        else {
            contact.removeClass('name-required');
        }
        if(contact.find('.email input').val().trim() == '' ||
            !(/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}\b/i).test(contact.find('.email input').val())) {
            contact.addClass('email-required');
        }
        else {
            contact.removeClass('email-required');
        }
        if(contact.find('.message textarea').val().trim() == '') {
            contact.addClass('message-required');
        }
        else {
            contact.removeClass('message-required');
        }

        if(contact.is('.name-required') || contact.is('.email-required') || contact.is('.message-required')) {
            contact.find('.highlighted-link').removeClass('valid').addClass('invalid');
        }
        else {
            contact.removeClass('invalid-only').find('.highlighted-link').removeClass('invalid').addClass('valid');
        }
    }

    body.on('show.bs.modal', '#contact-support', validateContact);
    body.on('keyup', '#contact-support input, #contact-support textarea', validateContact);
    body.on('change', '#contact-support input, #contact-support textarea', validateContact);

    body.on('submit', '#contact-support form', function (evt) {
        evt.preventDefault();
        var contact = $('#contact-support');
        if(contact.find('.highlighted-link').is('.invalid')) {
            contact.addClass('invalid-only');
            if(contact.is('.name-required')) {
                contact.find('.name input').focus();
            }
            if(contact.is('.email-required')) {
                contact.find('.email input').focus();
            }
            if(contact.is('.message-required')) {
                contact.find('.message textarea').focus();
            }
            return;
        }
        contact.removeClass('valid').addClass('invalid');

        jQuery.ajax({
            url: window.callbackPaths['contact_send'],
            type: 'POST',
            dataType: 'json',
            data: {
                name: contact.find('.name input').val(),
                email: contact.find('.email input').val(),
                message: contact.find('.message textarea').val()
            },
            success: function () {
                contact.find('.message textarea').val('');
                contact.modal('hide');
            },
            error: function () {
                contact.removeClass('invalid').addClass('valid');
            }
        });
    });

    function validateDemo()
    {
        var contact = $(this).closest('#schedule-demo');
        if(contact.find('.first-name input').val().trim() == '') {
            contact.addClass('first-required');
        }
        else {
            contact.removeClass('first-required');
        }
        if(contact.find('.last-name input').val().trim() == '') {
            contact.addClass('last-required');
        }
        else {
            contact.removeClass('last-required');
        }
        if(contact.find('.email input').val().trim() == '' ||
            !(/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}\b/i).test(contact.find('.email input').val())) {
            contact.addClass('email-required');
        }
        else {
            contact.removeClass('email-required');
        }
        if(contact.find('.company input').val().trim() == '') {
            contact.addClass('company-required');
        }
        else {
            contact.removeClass('company-required');
        }
        if(contact.find('.phone input').val().trim() == '') {
            contact.addClass('phone-required');
        }
        else {
            contact.removeClass('phone-required');
        }

        if(contact.is('.first-required') || contact.is('.last-required') || contact.is('.email-required') || contact.is('.company-required') || contact.is('.phone-required')) {
            contact.find('.highlighted-link').removeClass('valid').addClass('invalid');
        }
        else {
            contact.removeClass('invalid-only').find('.highlighted-link').removeClass('invalid').addClass('valid');
        }
    }

    body.on('show.bs.modal', '#schedule-demo', validateDemo);
    body.on('keyup', '#schedule-demo input, #schedule-demo textarea', validateDemo);
    body.on('change', '#schedule-demo input, #schedule-demo textarea', validateDemo);

    body.on('submit', '#schedule-demo form', function (evt) {
        evt.preventDefault();
        var contact = $('#schedule-demo');
        if(contact.find('.highlighted-link').is('.invalid')) {
            contact.addClass('invalid-only');
            if(contact.is('.first-required')) {
                contact.find('.first-name input').focus();
            }
            else if(contact.is('.last-required')) {
                contact.find('.last-name input').focus();
            }
            else if(contact.is('.company-required')) {
                contact.find('.company input').focus();
            }
            else if(contact.is('.email-required')) {
                contact.find('.email input').focus();
            }
            else if(contact.is('.phone-required')) {
                contact.find('.phone input').focus();
            }
            return;
        }
        contact.find('.highlighted-link').removeClass('valid').addClass('invalid');

        jQuery.ajax({
            url: window.callbackPaths['contact_send'],
            type: 'POST',
            dataType: 'json',
            data: {
                name: contact.find('.first-name input').val() + ' ' + contact.find('.last-name input').val(),
                email: contact.find('.email input').val(),
                message: 'First: ' + contact.find('.first-name input').val() + "\r\n" +
                         'Last: ' + contact.find('.last-name input').val() + "\r\n" +
                         'Company: ' + contact.find('.company input').val() + "\r\n" +
                         'Phone: ' + contact.find('.phone input').val()
            },
            success: function () {
                contact.find('.message textarea').val('');
                contact.modal('hide');
            },
            error: function () {
                contact.removeClass('invalid').addClass('valid');
            }
        });
    });

    body.on('submit', '#bill-parents form', function (evt) {
        var contact = $('#bill-parents');
        evt.preventDefault();
        if(contact.find('.highlighted-link').is('.invalid')) {
            contact.addClass('invalid-only');
            if(contact.is('.first-required')) {
                contact.find('.first-name input').focus();
            }
            else if(contact.is('.last-required')) {
                contact.find('.last-name input').focus();
            }
            else if(contact.is('.email-required')) {
                contact.find('.email input').focus();
            }
            else if(contact.is('.your-first-required')) {
                contact.find('.your-first input').focus();
            }
            else if(contact.is('.your-last-required')) {
                contact.find('.your-last input').focus();
            }
            else if(contact.is('.your-email-required')) {
                contact.find('.your-email input').focus();
            }
            return;
        }
        loadingAnimation(contact.find('[value="#submit-contact"]'));
        contact.removeClass('valid').addClass('invalid');
        var data = {
            first: contact.find('.first-name input').val(),
            last: contact.find('.last-name input').val(),
            email: contact.find('.email input').val()
        };
        if(contact.find('.your-first input').length > 0) {
            data['yourFirst'] = contact.find('.your-first input').val().trim();
            data['yourLast'] = contact.find('.your-last input').val().trim();
            data['yourEmail'] = contact.find('.your-email input').val().trim();
        }
        jQuery.ajax({
            url: window.callbackPaths['contact_parents'],
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function () {
                contact.find('.squiggle').stop().remove();
                contact.find('.first-name input, .last-name input, .email input, ' +
                            '.your-first input, .your-last input, .your-email input').val('');
                contact.modal('hide');
                $('#bill-parents-confirm').modal({show:true});
            },
            error: function () {
                contact.find('.squiggle').stop().remove();
            }
        });
    });

    function validateInvite()
    {
        var invite = $(this).closest('#student-invite, #bill-parents');
        var valid = true;
        if(invite.find('.first-name input').val().trim() == '') {
            invite.addClass('first-required');
        }
        else {
            invite.removeClass('first-required');
        }
        if(invite.find('.last-name input').val().trim() == '') {
            invite.addClass('last-required');
        }
        else {
            invite.removeClass('last-required');
        }
        if(invite.find('.email input').val().trim() == '' ||
            !(/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}\b/i).test(invite.find('.email input').val())) {
            invite.addClass('email-required');
        }
        else {
            invite.removeClass('email-required');
        }
        if(invite.find('.your-first').length > 0) {
            if(invite.find('.your-first input').val().trim() == '') {
                invite.addClass('your-first-required');
            }
            else {
                invite.removeClass('your-first-required');
            }
            if(invite.find('.your-last input').val().trim() == '') {
                invite.addClass('your-last-required');
            }
            else {
                invite.removeClass('your-last-required');
            }
            if(invite.find('.your-email input').val().trim() == '' ||
                !(/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}\b/i).test(invite.find('.your-email input').val())) {
                invite.addClass('your-email-required');
            }
            else {
                invite.removeClass('your-email-required');
            }
        }
        if(invite.is('.first-required') || invite.is('.last-required') || invite.is('.email-required') ||
            invite.is('.your-first-required') || invite.is('.your-last-required') || invite.is('.your-email-required'))
            invite.find('.highlighted-link').removeClass('valid').addClass('invalid');
        else
            invite.removeClass('invalid-only').find('.highlighted-link').removeClass('invalid').addClass('valid');
    }

    body.on('show.bs.modal', '#student-invite, #bill-parents', validateInvite);
    body.on('keyup', '#student-invite input, #bill-parents input', validateInvite);
    body.on('change', '#student-invite input, #bill-parents input', validateInvite);

    body.on('submit', '#student-invite form', function (evt) {
        evt.preventDefault();
        var contact = $('#student-invite');
        if(contact.find('.highlighted-link').is('.invalid')) {
            contact.addClass('invalid-only');
            if(contact.is('.first-required')) {
                contact.find('.first-name input').focus();
            }
            else if(contact.is('.last-required')) {
                contact.find('.last-name input').focus();
            }
            else if(contact.is('.email-required')) {
                contact.find('.email input').focus();
            }
            else if(contact.is('.your-first-required')) {
                contact.find('.your-first input').focus();
            }
            else if(contact.is('.your-last-required')) {
                contact.find('.your-last input').focus();
            }
            else if(contact.is('.your-email-required')) {
                contact.find('.your-email input').focus();
            }
            return;
        }
        loadingAnimation(contact.find('[value="#submit-contact"]'));
        contact.find('.highlighted-link').removeClass('valid').addClass('invalid');
        var data = {
            first: contact.find('.first-name input').val(),
            last: contact.find('.last-name input').val(),
            email: contact.find('.email input').val()
        };
        if(contact.find('.your-first input').length > 0) {
            data['yourFirst'] = contact.find('.your-first input').val().trim();
            data['yourLast'] = contact.find('.your-last input').val().trim();
            data['yourEmail'] = contact.find('.your-email input').val().trim();
        }
        jQuery.ajax({
            url: window.callbackPaths['contact_students'],
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function () {
                contact.find('.squiggle').stop().remove();
                contact.find('.first-name input, .last-name input, .email input, ' +
                            '.your-first input, .your-last input, .your-email input').val('');
                contact.modal('hide');
                $('#student-invite-confirm').modal({show:true});
            },
            error: function () {
                contact.find('.squiggle').stop().remove();
            }
        });
    });
});
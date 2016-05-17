
$(document).ready(function () {

    // handle shared publishing function
    var body = $('body');


    body.on('change', '#pack-publish input[name="schedule"]', function () {
        var dialog = $('#pack-publish');
        if (dialog.find('input[name="schedule"]').datetimepicker('getValue') <= new Date()) {
            dialog.find('input[value="now"]').prop('checked', true);
        }
        else {
            dialog.find('input[value="later"]').prop('checked', true);
        }
    });

    body.on('change', '#pack-publish input[name="date"]', function () {
        var dialog = $('#pack-publish'),
            input = dialog.find('input[name="schedule"]');
        if (dialog.find('input[value="now"]').is(':checked')) {
            input.datetimepicker('setOptions', {value: new Date()})
        }
    });

    function showPublishDialog(packId, packName, publish) {
        var field = $(this);
        if ((dialog = $('#pack-publish')).modal({show: true, backdrop: true}).length == 0) {
            dialog = $(window.views.render.apply(body, ['pack-publish', {}])).appendTo(body);
        }

        var allowTimes = [];
        for (var xh = 0; xh <= 23; xh++) {
            for (var xm = 0; xm < 60; xm += 30) {
                allowTimes[allowTimes.length] = ("0" + xh).slice(-2) + ':' + ("0" + xm).slice(-2);
            }
        }
        dialog.find('input[name="schedule"]').datetimepicker({
            format: 'd.m.Y H:i',
            inline: true,
            minDate: 0,
            roundTime: 'ceil'
            //    allowTimes: allowTimes
        }).addClass('dateTimePicker');

        // set up previous publish settings
        if (publish) {
            applyFields.apply(dialog, [publish]);
        }
        var date = new Date(dialog.find('input[name="schedule"]').datetimepicker('getValue'));
        date.setHours(date.getHours() + Math.ceil(date.getMinutes() / 60));
        date.setMinutes(0);
        dialog.find('input[name="schedule"]').datetimepicker('setOptions', {value: date});
        dialog.find('input[name="schedule"]').trigger('change');

        body.one('click.publish', '#pack-publish a[href="#submit-publish"]', function () {

            var publish = gatherFields.apply(dialog, [['schedule', 'email', 'alert'], false]);

            // show confirmation dialog
            $('#general-dialog').modal({show: true, backdrop: true})
                .find('.modal-body').html('<p>Are you sure you want to publish ' + packName + '?');

            body.one('click.publish_confirm', '#general-dialog a[href="#submit"]', function () {
                field.data('publish', publish);
                field.filter('select').val('GROUP').trigger('confirm');
                standardSave.apply(field, [{packId: packId.replace('pack-', ''), publish: publish}]);
            });
        });
    }

    body.on('hidden.bs.modal', '#pack-publish', function () {
        setTimeout(function () {
            body.off('click.publish');
        }, 100);
    });

    window.showPublishDialog = showPublishDialog;

});
$(document).ready(function () {

    var body = $('body');

    body.on('keyup change', '#userlist [name="search"]', function () {
        var userlist = $('#userlist');
        if($(this).val().trim() == '') {
            userlist.find('tr').show();
        }
        else {
            userlist.find('tbody tr').each(function () {
                if($(this).find('> td').text().toLowerCase().indexOf(userlist.find('[name="search"]').val().trim().toLowerCase()) == -1) {
                    $(this).hide();
                }
                else {
                    $(this).show();
                }
            });
        }
    });

    function sortSelect(a, b) {
        if(a == 'Student' || a == 'Status' || a == 'Adviser' || a == 'School' || a == 'Date' || a == 'Completion')
            return -1;
        if(b == 'Student' || b == 'Status' || b == 'Adviser' || b == 'School' || b == 'Date' || a == 'Completion')
            return 1;
        if(a == 'Ascending (A-Z)' && b == 'Descending (Z-A)')
            return -1;
        if(b == 'Ascending (A-Z)' && a == 'Descending (Z-A)')
            return 1;
        if(a == 'Ascending (A-Z)' || a == 'Descending (Z-A)')
            return -1;
        if(b == 'Ascending (A-Z)' || b == 'Descending (Z-A)')
            return 1;

        if (a.toUpperCase() > b.toUpperCase())
            return 1;
        if (a.toUpperCase() < b.toUpperCase())
            return -1;
        // a must be equal to b
        return 0;
    }

    body.on('show', '#userlist', function () {
        var userlist = jQuery('#userlist');
        if($(this).data('state') == null) {
            $(this).data('state', '');

            var status = ['Status', 'Ascending', 'Descending', 'Red', 'Yellow', 'Green'];
            userlist.find('th:nth-child(1)').html('<select><option>' + status.join("</option><option>") + '</option></select>');

            var dates = ['Date', 'Ascending (A-Z)', 'Descending (Z-A)'];
            userlist.find('td:nth-child(2)').each(function () {
                if(dates.indexOf(jQuery(this).text().trim()) == -1)
                    dates[dates.length] = jQuery(this).text().trim();
            });
            userlist.find('th:nth-child(2)').html('<select><option>' + dates.join("</option><option>") + '</option></select>');

            var students = ['Student', 'Ascending (A-Z)', 'Descending (Z-A)'];
            userlist.find('td:nth-child(3)').each(function () {
                if(students.indexOf(jQuery(this).text().trim()) == -1)
                    students[students.length] = jQuery(this).text().trim();
            });
            students.sort(sortSelect);
            userlist.find('th:nth-child(3)').html('<select><option>' + students.join("</option><option>") + '</option></select>');

            var completion = ['Completion', 'Ascending (A-Z)', 'Descending (Z-A)'];
            userlist.find('td:nth-child(4)').each(function () {
                if(completion.indexOf(jQuery(this).text().trim()) == -1)
                    completion[completion.length] = jQuery(this).text().trim();
            });
            completion.sort(sortSelect);
            userlist.find('th:nth-child(4)').html('<select><option>' + completion.join("</option><option>") + '</option></select>');

            var schools = ['School', 'Ascending (A-Z)', 'Descending (Z-A)'];
            userlist.find('td:nth-child(5)').each(function () {
                if(schools.indexOf(jQuery(this).text().trim()) == -1)
                    schools[schools.length] = jQuery(this).text().trim();
            });
            schools.sort(sortSelect);
            userlist.find('th:nth-child(5)').html('<select><option>' + schools.join("</option><option>") + '</option></select>');

            var advisers = ['Adviser', 'Ascending (A-Z)', 'Descending (Z-A)'];
            userlist.find('.master td:nth-child(6)').each(function () {
                if(advisers.indexOf(jQuery(this).text().trim()) == -1)
                    advisers[advisers.length] = jQuery(this).text().trim();
            });
            advisers.sort(sortSelect);
            userlist.find('.master th:nth-child(6)').html('<select><option>' + advisers.join("</option><option>") + '</option></select>');

        }
        $('#partner-advice-1').modal({show:true});
    });

    body.on('hidden.bs.modal', '#partner-advice-1', function () {
        $(this).remove();
    });

    body.on('click', '#userlist a[href="#change-status"]', function (evt) {
        var userlist = jQuery('#userlist');
        evt.preventDefault();
        evt.stopPropagation();
        var row = jQuery(this).parents('tr'),
            selectStatus = jQuery('#select-status');
        if(selectStatus.is(':visible'))
        {
            selectStatus.hide();
            return;
        }
        selectStatus.css('top', row.position().top).off().on('click', 'a', function (evt) {
            evt.preventDefault();
            var status = jQuery(this).attr('href').substring(1),
                userId = (/user-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'));
            row.removeClass('status_green status_yellow status_red');
            row.addClass('status_' + status);
            jQuery.ajax({
                url: Routing.generate('userlist_status'),
                type: 'POST',
                dataType: 'json',
                data: {
                    userId: userId[1],
                    status: status
                },
                success: function () {
                    userlist.find('tr.user-id-' + userId[1]).removeClass('status_green status_yellow status_red')
                        .addClass('status_' + status);
                }
            });
        }).show();
    });

    body.on('click', function () {
        jQuery('#select-status').hide();
    });

    body.on('change', '#userlist select', function () {
        var userlist = jQuery('#userlist');
        jQuery('tr').show();
        userlist.find('select').each(function () {
            if(jQuery(this).val() == 'Ascending (A-Z)' || jQuery(this).val() == 'Descending (Z-A)' ||
                jQuery(this).val() == 'Ascending' || jQuery(this).val() == 'Descending')
            {
                var ascending = jQuery(this).val() == 'Ascending (A-Z)' || jQuery(this).val() == 'Ascending',
                    i = jQuery(this).parents('th').index() + 1;
                userlist.find('tbody tr').detach().sort(function (a, b) {
                    var aTxt = jQuery(a).find('td:nth-child(' + i + ')').text().toUpperCase(),
                        bTxt = jQuery(b).find('td:nth-child(' + i + ')').text().toUpperCase();
                    if(i == 1)
                    {
                        if(jQuery(a).is('.status_red'))
                            aTxt = 1;
                        if(jQuery(b).is('.status_red'))
                            bTxt = 1;
                        if(jQuery(a).is('.status_yellow'))
                            aTxt = 2;
                        if(jQuery(b).is('.status_yellow'))
                            bTxt = 2;
                        if(jQuery(a).is('.status_green'))
                            aTxt = 3;
                        if(jQuery(b).is('.status_green'))
                            bTxt = 3;
                    }
                    if(i == 2)
                    {
                        aTxt = jQuery(a).find('td:nth-child(' + i + ')').attr('data-timestamp');
                        bTxt = jQuery(b).find('td:nth-child(' + i + ')').attr('data-timestamp');
                    }
                    if (aTxt > bTxt)
                        return (ascending ? 1 : -1);
                    if (aTxt < bTxt)
                        return (ascending ? -1 : 1);
                    // a must be equal to b
                    return 0;
                }).appendTo(userlist.find('tbody'));
                jQuery(this).val(jQuery(this).data('last') || jQuery(this).find('option').first().text());
            }
            else if(jQuery(this).val() != 'Status' &&
                jQuery(this).val() != 'Date' &&
                jQuery(this).val() != 'Student' &&
                jQuery(this).val() != 'Completion' &&
                jQuery(this).val() != 'School' &&
                jQuery(this).val() != 'Adviser' &&
                jQuery(this).val() != 'Deadlines' &&
                jQuery(this).val() != 'Notes' &&
                jQuery(this).val() != 'Grades' &&
                jQuery(this).val() != 'Schedule')
            {
                jQuery(this).parent().removeClass('unfiltered').addClass('filtered');
                jQuery(this).data('last', jQuery(this).val());
                var j = jQuery(this).parents('th').index() + 1,
                    filter = jQuery(this).val();
                // if we are changing the status, select rows by class name
                if(j == 1) {
                    userlist.find('tbody tr').hide();
                    userlist.find('tr.status_' + filter.toLowerCase()).show();
                }
                else {
                    userlist.find('td:nth-child(' + j + ')').each(function () {
                        if(jQuery(this).text().trim().indexOf(filter) == -1 &&
                            (jQuery(this).attr('data-value') == null || jQuery(this).attr('data-value').indexOf(filter) == -1))
                            jQuery(this).parents('tr').hide();
                    });
                }
            }
            else
            {
                jQuery(this).parent().removeClass('filtered').addClass('unfiltered');
                jQuery(this).data('last', jQuery(this).val());
            }
        });
    });

});
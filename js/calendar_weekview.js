
$(function() {
    $(document).on('mouseenter', ".calendar_hour", function() {
        var id = $(this).attr('id').split('_');
        $(".ui-selected").each(function() {
            $(this).removeClass('ui-selected');
        });
        $("#weekview_table").selectable({
            filter: "td[id^='" + id[0] + "']",
            stop: function(event, ui) {
                /* Get the title of the first td that has ui-selected class (selected class)*/
                var from_date = $('.ui-selected')[0].title;
                /* Get the id of the last td that has ui-selected class (selected class)*/
                var to_date_id = $('.ui-selected')[($('.ui-selected').length) - 1].id;

                var to_date = $('.ui-selected')[($('.ui-selected').length) - 1].title;
                var tonext = (eval(id[1]) + 1800);

                var fromdate_details = from_date.split(' ');
                var to_date_id_details = to_date_id.split('_');

                /* Get the title of the next element after last td that has ui-selected class (selected class)*/
                var to_date_title = $('#' + id[0] + '_' + (eval(to_date_id_details[1]) + 1800)).attr('title');
                var todate_details = to_date_title.split(' ');

                var from_hours_details = fromdate_details[1].split(':');
                var to_hours_details = todate_details[1].split(':');

                $('#pickDate_from').val(fromdate_details[0]);
                $('#pickDate_to').val(todate_details[0]);

                $('#fromHour').val(from_hours_details[0]);
                $('#fromMinutes').val(from_hours_details[1]);

                $('#toHour').val(to_hours_details[0]);
                $('#toMinutes').val(to_hours_details[1]);

                $('#popup_weekview_createentry').dialog('open');
            }
        });
    });


    $(document).on('mouseover', ".calendar_hourevent", function() {
        var depth = 30;
        $(this).draggable({
            grid: [170, 20],
            stack: ".calendar_hourevent", //#week_days_container
            containment: "#weekview_table",
            zIndex: 200,
            scroll: true,
            start: function(event, ui, originalPositionTop) {
                $(this).data('originalLeft', parseInt(ui.helper.css('left'))); /* Get the original left on start & store it in arbitrary data store */
                $(this).data('originalTop', parseInt(ui.helper.css('top'))); 	/* Get the original top  on start & store it in arbitrary data store */
                var id = ui.helper.attr('id').split('_');
                $(this).data('toTime', $('#toTime_' + id[1]).text().split(':'));
                $(this).data('fromTime', $('#fromTime_' + id[1]).text().split(':'));
            },
            drag: function(event, ui) {
                var id = ui.helper.attr('id').split('_');
                var timeFields = new Array('toTime', 'fromTime');
                for (var i = 0; i < 2; i++) {
                    currentTime = new Date();
                    currentTime.setHours($(this).data(timeFields[i])[0], $(this).data(timeFields[i])[1])
                    currentTime.setTime(currentTime.getTime() + ((ui.position.top - $(this).data('originalTop')) / 20) * (depth * 60 * 1000));
                    $('#' + timeFields[i] + '_' + id[1]).text(currentTime.getHours() + ':' + currentTime.getMinutes());
                }
            },
            stop: function(event, ui) {
                var id = ui.helper.attr('id').split('_');
                /* Calculate Left Positions */
                var destleft = (ui.position.left) / 170;
                var originalleft = $(this).data('originalLeft') / 170;

                var topdiff = ((ui.position.top - $(this).data('originalTop')) / 20) * (depth * 60);
                var leftdiff = (parseFloat(destleft.toPrecision(2) - originalleft.toPrecision(2)) * 86400);

                var diff = topdiff + leftdiff;

                var originalTop = $(this).data('originalTop');
                var originalLeft = $(this).data('originalLeft');

                $.post("index.php?module=calendar/weekviewoperations&action=update_time", {todiff: diff, fromdiff: diff, id: id[1]}, function(returnedData) {
                    if (returnedData == '0') {
                        $('#leave_' + id[1]).animate({top: originalTop, left: originalLeft}, 'slow');
                    }
                });

            }
        });
    });

    $(document).on('mouseover', ".calendar_hourevent", function() {
        var depth = 30;
        $(this).resizable({
            minHeight: 20,
            maxWidth: 170,
            grid: 20,
            handles: 's',
            ghost: true,
            //autoHide: true,
            containment: "#weekview_table",
            start: function(event, ui) {
                var id = ui.element.attr('id').split('_');
                $(this).data('originalTime', $('#toTime_' + id[1]).text().split(':'));
            },
            resize: function(event, ui) {
                var id = ui.element.attr('id').split('_');
                currentToTime = new Date();
                currentToTime.setHours($(this).data('originalTime')[0], $(this).data('originalTime')[1]);
                var diff = (((ui.size.height - ui.originalSize.height) / 20) * (depth * 60) * 1000);
                currentToTime.setTime(currentToTime.getTime() + diff);
                $('#toTime_' + id[1]).text(currentToTime.getHours() + ':' + currentToTime.getMinutes());
            },
            stop: function(event, ui) {
                var id = ui.element.attr('id').split('_');
                var diff = ((ui.size.height - ui.originalSize.height) / 20) * (depth * 60);
                var originalTime = $(this).data('originalTime');
                $.post("index.php?module=calendar/weekviewoperations&action=update_time", {todiff: diff, id: id[1]}, function(returnedData) {
                    if (returnedData == '0') {
                        $('#leave_' + id[1]).animate({height: ui.originalSize.height}, 'fast');
                        $('#toTime_' + id[1]).text(originalTime[0] + ':' + originalTime[1]);
                    }
                });
            }
        });
    });

    $(document).on('change', "#customer_1_autocomplete", function() {
        $.post("index.php?module=calendar/weekviewoperations&action=suggest_customervisits", {uid: $('#popup_weekview_createentry').find('#uid').val(), cid: $('#popup_weekview_createentry').find('#customer_1_id').val()}, function(returnedData) {
            $("#suggestions_Results").show();
            $("#suggestions_Results").html(returnedData);
        });
    });

    $(document).on('click', ".calendar_hourevent", function(event) {
        if ($(event.target).is("div")) {
            var id = $(this).attr("id").split("_");

            /* Call the function sharedPopUp(); this will call the original popUp() function */
            sharedFunctions.sharedPopUp('calendar/weekviewoperations', 'popup_calendar_custvisitsdetails', id[1]);
        }
    });

    $(document).on('click', "img[id^='deleltevisiticon_']", function() {
        var id = $(this).attr("id").split('_');
        $.post("index.php?module=calendar/weekviewoperations&action=delete_visit", {lid: id[1]})
    });
});

function drawbox(content) {
    // Loop through each element (td) that has class "ui-selected" and remove the class fo this td
    $(".ui-selected").each(function() {
        $(this).removeClass('ui-selected');
    });

    $("#week_days_container").prepend(content);
    $('#popup_weekview_createentry').dialog('close');
}
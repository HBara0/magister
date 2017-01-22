<script>
    $(function() {
        $(document).on("click", "button[id^='subscribebutton']", function() {
            var id = $(this).attr("id").split("_");
            $.ajax({
                type: 'post',
                url: "{$core->settings['rootdir']}/index.php?module=events/eventslist&id=" + id[1] + "&action=events_" + id[2],
                data: "id=" + id[1],
                beforeSend: function() {
                    loadgif($("#subscribedive_" + id[1]));

                },
                success: function(returnedData) {
                    $("#subscribedive_" + id[1]).html(returnedData);
                }
            })
        });

    });
</script>
<div class="modal-dialog" >
    <div class="modal-content">
        <div class="modal-header ">
            <h4 class="modal-title" >{$lang->addcalendarevent}</h4>
        </div>
        <div class="modal-body">
            <form  action="#" method="post" id="perform_portal/calendar_Form" name="perform_portal/calendar_Form">
                <label>{$lang->daterange}</label>
                <div class="input-daterange input-group datepicker">
                    <input type="text" class="input-sm form-control" value="{$event[fromdateoutput]}" name="event[fromDate]" />
                    <span class="input-group-addon">to</span>
                    <input type="text" class="input-sm form-control" value="{$event[todateoutput]}" name="event[toDate]" />
                </div>
                <div class="input-group bootstrap-timepicker timepicker">
                    <input data-provide="timepicker" name="event[fromTime]" value="{$event[fromtimeoutput]}" type="text" class="form-control input-small">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                    <input data-provide="timepicker" name="event[toTime]" value="{$event[totimeoutput]}" type="text" class="form-control input-small">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <input type="submit" value="{$lang->savecaps}" id="perform_portal/calendar_Button" class="button"/>
            <div id="perform_portal/calendar_Results"></div>
        </div>
    </div>
</div>
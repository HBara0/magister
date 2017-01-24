<div id='calendar'></div>
<div class="modal fade" id="calendar_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        iniCalendar($('#calendar'));

        function iniCalendar(obj) {
            $(obj).fullCalendar({
                selectable: true,
                select: function(start, end) {
                    $.ajax({
                        type: 'post',
                        url: rootdir + "?module=portal/calendar&action=get_createevent",
                        data: "start=" + start + "&end=" + end,
                        beforeSend: function() {
                            loadgif($("#calendar_modal").find('.modal-body'));
                            $("#calendar_modal").modal('show');
                        },
                        success: function(returnedData) {
                            $("#calendar_modal").html(returnedData);
                            initialize();
                        }

                    });

                },
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                editable: false,
                eventLimit: true,
                // events: reservedData
                events: {
                    type: 'POST',
                    url: '{$core->settings['rootdir']}/index.php?module=portal/calendar&action=fetchevents',
                    error: function() {
                        $('#script-warning').show();
                    }

                },
                loading: function(bool) {
                    $('#loading').toggle(bool);
                },
                eventClick: function(event, jsEvent, view) {
                    $.ajax({
                        type: 'post',
                        url: rootdir + "?module=portal/calendar&action=get_editevent",
                        data: "id=" + event.id + "&type=" + event.type,
                        beforeSend: function() {
                            loadgif($("#calendar_modal").find('.modal-body'));
                            $("#calendar_modal").modal('show');
                        },
                        success: function(returnedData) {
                            $("#calendar_modal").html(returnedData);
                            initialize();
                        }

                    });
                }
            });
            setInterval(function() {
                $(obj).fullCalendar('refetchEvents')
            }, 150000);
        }
    });

</script>
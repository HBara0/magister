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
                        url: '{$core->settings['rootdir']}/index.php?module=portal/calendar&action=get_editevent',
                        data: "id=" + event.id + "&type=" + event.type,
                        beforeSend: function() {
                            $("body").append("<div id='modal-loading'><span  style='display:block; width:100px; height: 100%; margin: 0 auto;'><img  src='./images/loader.gif'/></span></div>");
                            $("#modal-loading").dialog({
                                height: 150, modal: true, closeOnEscape: false, title: 'Loading...', resizable: false, minHeight: 0,
                            });
                        },
                        complete: function() {
                            $("#modal-loading").dialog("close").remove();
                        },
                        success: function(returnedData) {
                            $(".workspace_container").append(returnedData);
                            $("div[id^='popup_']").dialog({
                                bgiframe: true,
                                closeOnEscape: true,
                                modal: true,
                                width: 600,
                                minWidth: 600,
                                maxWidth: 800,
                                zIndex: 1000,
                                close: function() {
                                    $(this).find("form").each(function() {
                                        this.reset();
                                    });
                                    $(this).find("span[id$='_Validation']").empty();
                                    $(this).find("span[id$='_Results']").empty();
                                    $(this).remove();
                                }
                            });
                            /* Make the parent dialog overflow as visible to completely display the  customer inline search results */
                            $(".ui-dialog,div[id^='popup_']").css("overflow", "visible");
                            $("input[id='hide_popupBox']").click(function() {
                                $("#popupBox").hide("fast");
                            });
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
<div style="display:none">
    <a  id="updatemktintldtls_{$mibdid}_{$core->input['module']}_loadpopupbyid" rel="mktdetail_{$mibdid}"><img src="{$core->settings[rootdir]}/images/icons/update.png"/></a>
</div>
<div id='calendar'></div>

<script>
    $(document).ready(function() {
        //   var reservations ={$reserved_data};
        //    var reservedData = [];
        //    for(var data in reservations) {
        //        reservedData.push(reservations[data]);
        //   }
        iniCalendar($('#calendar'));

        function iniCalendar(obj) {
            $(obj).fullCalendar({
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
                    url: '{$core->settings['rootdir']}/index.php?module=facilitymgmt/facilitiesschedule&action=fetchevents',
                    error: function() {
                        $('#script-warning').show();
                    }

                },
                loading: function(bool) {
                    $('#loading').toggle(bool);
                },
                dayClick: function(date, jsEvent, view) {

                    $.ajax({
                        type: 'post',
                        url: rootdir + "?module=facilitymgmt/facilitiesschedule&action=get_creatreservation",
                        data: "date=" + date.format(),
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

                },
                eventClick: function(event, jsEvent, view) {

                    $.ajax({
                        type: 'post',
                        url: rootdir + "?module=facilitymgmt/facilitiesschedule&action=get_editreservation",
                        data: "id=" + event.id,
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
            }, 60000);
        }
    });

</script>
<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->calendar}</title>
        {$headerinc}
        <link href="./css/calendar.min.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript">
            $(function() {
                $(".showpopup[id^='createeventtask_']").live("click", function() {
                    var id = $(this).attr("id").split("_");
                    $("#popup_createeventtask input[id^='altpickDate_']").val(id[1]);
                    $('#popup_createeventtask').dialog('open');
                });

                $('.redactor_air').css('z-index', ($('.ui-dialog').css('z-index') + 1));
                //get content of the popoup dialog and manipulate them.
                //var popupdata=$('.ui-dialog');
                //alert(popupdata.contents().find('#event_type').val());

                $('select[id=event_type]').live('change', function() {
                    var eventtype = $(this).val();
                    if (eventtype != 0 && (eventtype == 4)) {
                        $('#visitypefields').show("slide", { direction: "left" }, 500);
                    }
                    else{
                      $('#visitypefields').hide("slide", { direction: "left" }, 500);
                    }
                });

            });
        </script>
    </head>
    <body>
        {$header}
    <tr>
        <td colspan="3" class="contentContainer">
            <div style="width: 5%; float:right; text-align:right;"><a href="index.php?module=calendar/preferences"><img src="./images/icons/options.gif" border='0' alt="{$lang->calendarpreferences}"/></a></div>
            <table width="100%" cellspacing="0" cellpadding="0" class="calendar">
                <tr>
                    <td colspan="2"><a href="index.php?module=calendar/home{$prevlink_querystring}"><h3 style="margin-bottom: 1px;"> &laquo;</h3></a></td>
                    <td colspan="4" align="center"><h3 style="margin-bottom: 0px;">{$calendar_title}</h3></td>
                    <td colspan="3" style="text-align:right;"><a href="index.php?module=calendar/home{$nextlink_querystring}"><h3 style="margin-bottom: 1px;"> &raquo;</h3></a></td>
                </tr>
                {$calendar}
            </table>
        </td>
    </tr>
    {$footer}
    {$addeventtask_popup}
</body>
</html>
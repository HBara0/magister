<link href="./css/calendar.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
    $(function() {
        $(document).on("click", ".showpopup[id^='createeventtask_']", function() {
            var id = $(this).attr("id").split("_");
            $("#popup_createeventtask input[id^='altpickDate_']").val(id[1]);
            $('#popup_createeventtask').dialog('open');
        });

        $('.redactor_air').css('z-index', ($('.ui-dialog').css('z-index') + 1));

        $(document).on('change', 'select[id=event_type]', function() {
            var types = [{$etypemorefields}];
            if(jQuery.inArray(parseInt($(this).val()), types) > -1) {
                $('#visittypefields').slideDown();
            }
            else {
                $('#visittypefields').slideUp();
            }
        });

    });
</script>
<div class="container">
    <div style="width:10%; float:right; text-align:right;"><button onclick="goToURL('index.php?module=calendar/tasksboard')">{$lang->tasksboard}</button> <a href="index.php?module=calendar/preferences"><img src="./images/icons/options.gif" border='0' alt="{$lang->calendarpreferences}"/></a></div>
    <table width="100%" cellspacing="0" cellpadding="0" class="calendar">
        <tr>
            <td colspan="2"><a href="index.php?module=calendar/home{$prevlink_querystring}"><h3 style="margin-bottom: 1px;"> &laquo;</h1></a></td>
            <td colspan="4" align="center"><h3 style="margin-bottom: 0px;">{$calendar_title}</h1></td>
            <td colspan="3" style="text-align:right;"><a href="index.php?module=calendar/home{$nextlink_querystring}"><h3 style="margin-bottom: 1px;"> &raquo;</h1></a></td>
        </tr>
        {$calendar}
    </table>
    {$addeventtask_popup}
</div>

<div id="popup_reservefacility" title="{$lang->reservefacility}">
    <script>
        $(function() {
            $(document).on("change", "#status", function() {
                var val = $(this).val();
                $('[data-purpose]').each(function(i, obj) {
                    var id = $(this).attr('data-purpose').split("_");
                    if(val == id[1]) {
                        $(obj).show();
                    }
                    else {
                        $(obj).hide();
                        $(obj).removeAttr("selected");
                    }
                });
            });
        }
        );
    </script>
    <form id="perform_facilitymgmt/facilitiesschedule_Form" name="perform_facilitymgmt/facilitiesschedule_Form" action="#" method="post">
        <input type="hidden" name="action" value="perform_createreservation" />
        <table width="100%">
            <tr>
                <td>{$lang->from}*</td>
                <td>
                    <input type="text" tabindex="2" id="pickDate_from"  autocomplete="off" tabindex="1" data-alternativename="dateFrom"  name="reserve[fromDate]" value="{$reservation['fromDate_output']}" />
                    <input id="altpickTime_from" data-alternativename="timeFrom" type="time" tabindex="3" name="reserve[fromTime]" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="08:00" value="{$reservation[fromTime_output]}" required="required">
                </td>
            </tr>
            <tr>
                <td>{$lang->to}*</td>
                <td>
                    <input type="text" tabindex="2" id="pickDate_to"  autocomplete="off" tabindex="1" data-alternativename="dateTo"  name="reserve[toDate]" value="{$reservation['toDate_output']}" />
                    <input id="altpickTime_to" data-alternativename="timeTo" type="time" tabindex="3" name="reserve[toTime]" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="08:00" value="{$reservation[toTime_output]}" required="required">

                </td>
            </tr>
            <tr>
                <td>{$lang->facility}*</td>
                <td>
                    {$facilityreserve}
                </td>
            </tr>
            <tr>
                <td>{$lang->status}</td>
                <td>
                    {$statuslist}
                </td>
            </tr>
            <tr>
                <td>{$lang->purpose}</td>
                <td>
                    <select id="purposes" name="reserve[purpose]">
                        {$purposeoptions}
                    </select>
                </td>
            </tr>

        </table>
        <div align="center"><input type='button' id='perform_facilitymgmt/facilitiesschedule_Button' value='{$lang->savecaps}' class='button'/></div>
    </form>
    <div id="perform_facilitymgmt/facilitiesschedule_Results"></div>
</div>
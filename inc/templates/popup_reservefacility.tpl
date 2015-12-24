<div id="popup_reservefacility" title="{$lang->reservefacility}">
    <form id="perform_facilitymgmt/facilitiesschedule_Form" name="perform_facilitymgmt/facilitiesschedule_Form" action="#" method="post">
        <input type="hidden" name="action" value="perform_createreservation" />
        <table width="100%">
            <tr>
                <td>{$lang->facility}*</td>
                <td>
                    <input type="text"   id="basicfacilities{$extraid}_autocomplete" data-autocompletefilters="loacationLat,loacationLong{$extra_inputids}" autocomplete="false" tabindex="1" value="{$facilityname}"/>
                    <input type='hidden' id='basicfacilities{$extraid}_id'  name="reserve[fmfid]" value="{$facilityid}"/>
                    <input type='hidden' id='basicfacilities{$extraid}_id_output' name="" value="" disabled/>
                    <input type="hidden" id="loacationLat"  value="" name="loacationLat"/>
                    <input type="hidden" id="loacationLong"  value="" name="loacationLong"/>
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

                            if(navigator.geolocation) {
                                navigator.geolocation.getCurrentPosition(fillvalue);
                            }
                        }
                        );
                        function fillvalue(position) {
                            $('input[id="loacationLat"]').val(position.coords.latitude);
                            $('input[id="loacationLong"]').val(position.coords.longitude);

                        }
                    </script>
                </td>
            </tr>
            <tr>
                <td>{$lang->from}*</td>
                <td>
                    <input type="text" tabindex="2" id="pickDate_from"  autocomplete="off" tabindex="1" data-alternativename="reserveFrom"  name="reserve[fromDate]" value="{$reservation['fromDate_output']}" />
                    <input id="altpickTime_from" data-alternativename="timeFrom" type="time" tabindex="3" name="reserve[fromTime]" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="08:00" value="{$reservation[fromTime_output]}" required="required">
                </td>
            </tr>
            <tr>
                <td>{$lang->to}*</td>
                <td>
                    <input type="text" tabindex="2" id="pickDate_to"  autocomplete="off" tabindex="1" data-alternativename="reserveto"  name="reserve[toDate]" value="{$reservation['toDate_output']}" />
                    <input id="altpickTime_to" data-alternativename="timeto" type="time" tabindex="3" name="reserve[toTime]" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="08:00" value="{$reservation[toTime_output]}" required="required">

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
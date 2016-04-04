<div>
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
    <div {$display_form}>
        <form id="perform_facilitymgmt/facilitiesschedule_Form" name="perform_facilitymgmt/facilitiesschedule_Form" action="#" method="post">
            <input type="hidden" name="action" value="perform_createreservation" />
            <input type="hidden" name="reserve[fmrid]" value="{$id}" />


            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">{$lang->from}*</div>
                <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
                    <input type="text" tabindex="2" id="pickDate_from"  autocomplete="off" tabindex="1" data-alternativename="dateFrom"  name="reserve[fromDate]" value="{$reservation['fromDate_output']}" />
                </div>
                <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
                    <input style="width:95%" id="altpickTime_from" data-alternativename="timeFrom" type="time" tabindex="3" name="reserve[fromTime]" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="08:00" value="{$reservation[fromTime_output]}" required="required">
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">{$lang->to}*</div>
                <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
                    <input type="text" tabindex="2" id="pickDate_to"  autocomplete="off" tabindex="1" data-alternativename="dateTo"  name="reserve[toDate]" value="{$reservation['toDate_output']}" />
                </div>
                <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
                    <input style="width:95%" id="altpickTime_to" data-alternativename="timeTo" type="time" tabindex="3" name="reserve[toTime]" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="08:00" value="{$reservation[toTime_output]}" required="required">
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">{$lang->facility}*</div>
                <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                    {$facilityreserve}
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">{$lang->status}</div>
                <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                    {$statuslist}
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">{$lang->purpose}</div>
                <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                    <select id="purposes" name="reserve[purpose]" style="width:150px;">
                        {$purposeoptions}
                    </select>
                </div>
            </div>
            <div class="row" style="float:right">
                <input type='button' id='perform_facilitymgmt/facilitiesschedule_Button' value='{$lang->savecaps}' class='button'/>
            </div>
        </form>
        <div id="perform_facilitymgmt/facilitiesschedule_Results" style="margin-top:3px;"></div>
    </div>
    <div {$display_infobox}>
        <table class="datatable">
            <tr>
                <td>{$lang->facility}:</td>
                <td>{$facilityname}</td>
            </tr>
            <tr>
                <td>{$lang->from}:</td>
                <td>{$reservation['fromDate_output']}</td>
            </tr>
            <tr>
                <td>{$lang->to}:</td>
                <td>{$reservation['toDate_output']}</td>
            </tr>
            <tr>
                <td>{$lang->reservedby}:</td>
                <td>{$reservedby}</td>
            </tr>
            <tr {$show_status}>
                <td>{$lang->status}:</td>
                <td>{$reservation['status_output']}</td>
            </tr>
            <tr {$show_purpose}>
                <td>{$lang->purpose}:</td>
                <td>{$reservation['purpose_output']}</td>
            </tr>

        </table>
    </div>
</div>
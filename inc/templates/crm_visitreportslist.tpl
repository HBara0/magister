<script language="javascript">
    $(function() {

        $(document).on("click", "a[id^='unlockuserreport_']", function() {
            var id = $(this).attr('id').split('_');
            $("input[id='checkbox_" + id[1] + "']").prop('checked', true);
            $('#moderationtools option[value="lockunlock"]').prop('selected', true);
            $('#moderationtools').trigger("change");
        });
        $('#moderationtools').change(function() {
            if(sharedFunctions.checkSession() == false) {
                return;
            }

            if($(this).val().length > 0) {
                var formData = $("form[id='moderation_crm/listvisitreports_Form']").serialize();
                var url = "index.php?module=crm/listvisitreports&action=do_lockunlock_listvisitreports";

                sharedFunctions.requestAjax("post", url, formData, "moderation_crm/listvisitreports_Results", "moderation_crm/listvisitreports_Results");
            }
        });
    });
</script>
<h1>{$lang->listofvisitreports}</h1>
<form action="#" method="post" id="moderation_crm/listvisitreports_Form" name="moderation_crm/listvisitreports_Form" style="margin-bottom: 0px;">
                <table class="datatable_basic table table-bordered row-border hover order-column" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>&nbsp;</th>
                            <th>{$lang->customername} </th>
                            <th>{$lang->prepareby}</th>
                            <th>{$lang->calltype}</th>
                            <th>{$lang->dateofvisit}</th>
                <th {$displaydraft}>{$lang->isdraft}</th>
                <th>&nbsp;</th>
                            <th>&nbsp;</th>
            </tr>
        </thead>
                    <tfoot>
                        <tr>
                            <th>&nbsp;</th>
                            <th>{$lang->customername}</th>
                            <th>{$lang->prepareby}</th>
                            <th>{$lang->calltype}</th>
                            <th>{$lang->dateofvisit}</th>
                            <th {$displaydraft}>{$lang->isdraft}</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                        </tr>
                    </tfoot>
        <tbody>
            {$reportslist}
        </tbody>
        <tfoot>
            {$buttons_row}
        </tfoot>
    </table>
</form>
<div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}'  class="smalltext"/></form></div>

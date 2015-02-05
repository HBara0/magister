<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->aro}</title>
        {$headerinc}
        <script type="text/javascript">
            $(function() {

                $('input[id$=_approver]').live('change', function() {
                    var id = $(this).attr('id').split("_");
                    if($(this).val() != 'user') {
                        $("div[id^='user_" + id[1] + "']").hide();
                    }
                    $("div[id^='" + $(this).val() + "_" + id[1] + "']").effect("highlight", {color: "#D6EAAC"}, 1500).find("input").first().focus().val("");

                });

            });
        </script>
    </head>
    <body>
    <tr>
        {$header}
        {$menu}
        <td class="contentContainer" colspan="2">
            <h1>{$lang->manageapprovalchainspolicies} </h1>
            <form name="perform_aro/manageapprovalchainspolicies_Form" id="perform_aro/manageapprovalchainspolicies_Form"  action="#" method="post">
                <input type="hidden" id="wpid" name="chainpolicy[aapcid]" value="{$chainpolicy[aapcid]}">
                <table class="datatable"  style="width:100%;">
                    <tr><td>{$lang->affiliate} </td>
                        <td> {$affiliate_list}</td>
                    </tr>
                    <tr ><td>{$lang->effromdate} </td>
                        <td> <input type="text" id="pickDate_from"  autocomplete="off" tabindex="2" value="{$chainpolicy[effectiveFrom_output]}" required="required" /> </td>               </td>

                        <td> <input type="hidden" name="chainpolicy[effectiveFrom]" id="altpickDate_from" value="{$chainpolicy[effectiveFrom_formatted]}"/></td>
                    </tr>
                    <tr><td>{$lang->effromdate}  </td>
                        <td> <input type="text" id="pickDate_to" autocomplete="off" tabindex="2" value="{$chainpolicy[effectiveTo_output]}" required="required" />  </td>
                        <td> <input type="hidden" name="chainpolicy[effectiveTo]" id="altpickDate_to" value="{$chainpolicy[effectiveTo_formatted]}"/></td>

                    </tr>
                    <tr><td>{$lang->purchasetype}  </td>
                        <td>{$purchasetypelist} </td>
                    </tr>

                </table>
                <table class=" datatable-striped">
                    <tbody id="approvers_tbody" style="width:100%;">
                        <tr class="thead"><Td colspan="3">Approval Chain</Td></tr>
                                {$aro_manageapprovalchainspolicies_approversrows}
                    </tbody>


                </table>
                <table>
                    <tr><td valign="top">

                            <input name="numrows_approvers{$rowid}" type="hidden" id="approvers_numrows" value="{$rowid}">
                            <img src="./images/add.gif" id="addmore_approvers" alt="{$lang->add}">
                        </td>
                    </tr>
                    <tr>
                </table>
                <input type="submit" id="perform_aro/manageapprovalchainspolicies_Button" value="Save" class="button"/>
            </form>
            <div id="perform_aro/manageapprovalchainspolicies_Results"></div>
        </td>
    </tr>
</body>
</html>
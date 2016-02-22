<script type="text/javascript">
    $(function() {
        $(document).on("change", 'input[id$=_approver]', function() {
            var id = $(this).attr('id').split("_");
            if($(this).val() != 'user') {
                $("div[id^='user_" + id[1] + "']").hide();
            } else {
                $("div[id^='user_" + id[1] + "']").effect("highlight", {color: "#D6EAAC"}, 1500).find("input").first().focus().val("");
            }
        });
        $(document).on('click', "img[id^='deletesection_']", function() {
            var id = $(this).attr('id').split("_");
            $('tr[id="' + id[1] + '"]').remove();
        });
    });
</script>
<h1>{$lang->manageapprovalchainspolicies} </h1>
<form name="perform_aro/manageapprovalchainspolicies_Form" id="perform_aro/manageapprovalchainspolicies_Form"  action="#" method="post">
    <input type="hidden" id="wpid" name="chainpolicy[aapcid]" value="{$chainpolicy[aapcid]}">
    <table class="datatable"  style="width:100%;">
        <tr><td>{$lang->affiliate} </td>
            <td> {$affiliate_list}</td>
        </tr>
        <tr>
            <td>{$lang->country}<span style="font-weight:bold"> ({$lang->countrypolicy})</span></td>
            <td>
                <input id="countries_1_autocomplete" autocomplete="off" type="text" value="{$chainpolicy[country]}" style="width:150px;">
                <input id="countries_1_id" name="chainpolicy[coid]"  value="{$chainpolicy[coid]}" type="hidden">
                <div id="searchQuickResults_1" class="searchQuickResults" style="display: none;"></div>

            </td>
        </tr>
        <tr ><td>{$lang->effromdate} </td>
            <td><input type="text" id="pickDate_from"  autocomplete="off" tabindex="2" value="{$chainpolicy[effectiveFrom_output]}" required="required"/>
                <input type="hidden" name="chainpolicy[effectiveFrom]" id="altpickDate_from" value="{$chainpolicy[effectiveFrom_formatted]}"/></td>
        </tr>
        <tr><td>{$lang->eftodate}</td>
            <td><input type="text" id="pickDate_to" autocomplete="off" tabindex="2" value="{$chainpolicy[effectiveTo_output]}" required="required" />
                <input type="hidden" name="chainpolicy[effectiveTo]" id="altpickDate_to" value="{$chainpolicy[effectiveTo_formatted]}"/></td>

        </tr>
        <tr><td>{$lang->purchasetype}  </td>
            <td>{$purchasetypelist} </td>
        </tr>
        {$audittrail}
        <tr>
            <td colspan="2" class="subtitle">
                {$lang->informmore}
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input name="chainpolicy[informCoordinators]" id="chainpolicy_informCoordinators" type="checkbox" value="1" checked="checked" disabled="disabled"> {$lang->inform} {$lang->coordinators}
            </td>
        </tr>
        <tr>
            <td colspan="2" class="altrow2">
                <input name="chainpolicy[informGlobalCFO]" id="chainpolicy_informGlobalCFO" type="checkbox" value="1" {$checked[informGlobalCFO]}> {$lang->inform} {$lang->globalcfo}
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input name="chainpolicy[informGlobalPurchaseMgr]" id="chainpolicy_informGlobalPurchaseMgr" type="checkbox" value="1" {$checked[informGlobalPurchaseMgr]}> {$lang->inform} {$lang->globalpurchasemgr}
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input name="chainpolicy[informGlobalCommercials]" id="chainpolicy_informGlobalCommercials" type="checkbox" value="1" {$checked[informGlobalCommercials]}> {$lang->inform} {$lang->intermediarycommercials}
            </td>
        </tr>
        <tr><td style="vertical-align:top">{$lang->selectemployee}</td>
            <td style="vertical-align: top;">{$chainpolicy[informInternalUsers_output]}</td>
        </tr>
        <tr>
            <td style="vertical-align:top"></td>
            <td style="vertical-align: top;padding-left:10px;">
                <input type='text' id='user_0_informed_autocomplete' value="{$chainpolicy[username]}"/>
                <input type='hidden' id='user_0_informed_id' name='chainpolicy[informInternalUsers][]' value="{$user->uid}" />
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <table>
                    <tbody id="informedemployees_tbody">
                        {$informemployees_rows}
                    </tbody>
                    <tr><td valign="top">
                            <input name="numrows_informmoreemployees{$inform_rowid}" type="hidden" id="numrows_informedemployees" value="{$inform_rowid}">
                            <img src="./images/add.gif" id="ajaxaddmore_aro/manageapprovalchainspolicies_informedemployees" alt="{$lang->add}">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="vertical-align:top">{$lang->informbymail}</td>
            <td>
                <textarea name="chainpolicy[informExternalUsers]" id="description" cols="40" rows="5" placeholder="Enter emails seperated by comma">{$chainpolicy[informExternalUsers_output]}</textarea>
            </td>
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
                <input name="numrows_approvers{$rowid}" type="hidden" id="numrows_approvers" value="{$rowid}">
                <img src="./images/add.gif" id="ajaxaddmore_aro/manageapprovalchainspolicies_approvers" alt="{$lang->add}">
            </td>
        </tr>
    </table>
    <input type="submit" id="perform_aro/manageapprovalchainspolicies_Button" value="Save" class="button" style="{$display[save]}"/>
    <a class="button" href="{$core->settings['rootdir']}/index.php?module=aro/manageapprovalchainspolicies&id={$chainpolicy[aapcid]}&referrer=clone" value="Clone" target='_blank' style="{$display['clone']};padding-top:5px;color:white">
        Clone</a>
</form>
<div id="perform_aro/manageapprovalchainspolicies_Results"></div>

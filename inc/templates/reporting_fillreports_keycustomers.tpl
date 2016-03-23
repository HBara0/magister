<script src="{$core->settings[rootdir]}/js/fillreport.js" type="text/javascript"></script>
<h1>{$lang->keycustomers}<div style="font-style:italic; font-size:12px; color:#888;">Q{$report_meta[quarter]} {$report_meta[year]} / {$report_meta[supplier]} - {$report_meta[affiliate]}</div></h1>
{$excludekeycust_notifymessage}
<form id="save_keycustomers_reporting/fillreport_Form" name="save_keycustomers_reporting/fillreport_Form" action="index.php?module=reporting/fillreport&amp;stage=marketreport" method="post">
    <input type="hidden" id="rid" name="rid" value="{$core->input[rid]}">
    <input type="hidden" id="identifier" name="identifier" value="{$core->input[identifier]}">
    <input type="hidden" id="numrows" name="numrows" value="{$customerscount}">
    <table width="100%">
        <tbody id="keycustomers_tbody">
            {$customersrows}
        </tbody>
        <tfoot>
            <tr>
                <td valign="top">{$addmore_customers}</td>
                <td align="right"><input type="button" value="{$lang->prevcaps}" class="button" onClick="goToURL('index.php?module=reporting/fillreport&amp;stage=productsactivity&amp;identifier={$core->input[identifier]}');"/> <input type="button" id="save_keycustomers_reporting/fillreport_Button" value="{$lang->savecaps}" class="button"/> <input type="submit" value="{$lang->nextcaps}" onClick='$("form:first").unbind("submit").trigger("submit");' class="button"/>{$exludestage}
                </td>
            </tr>
            <tr>
                <td colspan="2"><div id="save_keycustomers_reporting/fillreport_Results"></div></td>
            </tr>
        </tfoot>
    </table>
</form>
{$addcustomer_popup}

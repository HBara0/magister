<script src="{$core->settings[rootdir]}/js/fillreport.js" type="text/javascript"></script>
<style type="text/css">
    .ui-tabs-nav li {
        width:110px;
    }

    .ui-icon,.ui-icon-close {
        cursor: pointer;
    }
</style>
<h1>{$lang->selectareport}</h1>
<form method="post" name="fillreport" action="index.php?module=reporting/fillreport&amp;stage=productsactivity">
    <table width="100%">
        <tr>
            <td width="50%" style="padding: 15px; border-right: 1px dashed #E2EFDC;"><div style="text-align:left; float:right; font-weight: bold;">{$lang->affiliate}</div></td>
            <td align="left" style="padding: 15px;">{$affiliates_list}</td>
        </tr>
        <tr id="suppliers_row">
            <td width="50%"  style="padding: 15px; border-right: 1px dashed #E2EFDC;"><div style="text-align:left; float:right; font-weight: bold;">{$lang->supplier}</div></td>
            <td align="left" style="padding: 15px;"><select id="spid" name="spid" tabindex="2"></select>&nbsp;<span id="supplierslist_Loading"></span></td>
        </tr>
        <tr id="quarters_row">
            <td width="50%" style="padding: 15px; border-right: 1px dashed #E2EFDC;"><div style="text-align:left; float:right; font-weight: bold;">{$lang->quarter}</div></td>
            <td align="left" style="padding: 15px;"><select id="quarter" name="quarter" tabindex="3"></select>&nbsp;<span id="quarters_Loading"></span> | <select id="year" name="year" tabindex="4"></select>&nbsp;<span id="years_Loading"></span></td>
        </tr>
        <tr><td colspan="2" align="center"><hr /></td></tr>
        <tr style="display:none;" id="buttons_row">
            <td colspan="2" align="center">
                <input type="submit" value="{$lang->begincaps}" class="button">
                {$transfill_checkbox}
            </td>
        </tr>
    </table>
</form>

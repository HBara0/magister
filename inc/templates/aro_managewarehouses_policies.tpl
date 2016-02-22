<h1>{$lang->managewarehousepolicies}</h1>
<form name="perform_aro/managewarehousepolicies_Form" id="perform_aro/managewarehousepolicies_Form"  action="#" method="post">
    <input type="hidden" id="wpid" name="warehousepolicy[awpid]" value="{$warehouse[awpid]}">
    <table class="datatable"  style="width:50%;">
        <tr>
            <td colspan="2">
                <div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px; text-align:center; font-weight:bold;">
                    If the Warehouse is missing, please click here
                    <a href="index.php?module=contents/createwarehouses" target="_blank"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a> to create it.
                </div>
            </td>
        </tr>
        <tr><td>{$lang->warehouse} </td>
            <td> {$warehouse_list} </td>
        </tr>
        <tr ><td>{$lang->effromdate} </td>
            <td> <input type="text" id="pickDate_from" autocomplete="off" tabindex="2" value="{$warehouse[effectiveFrom_output]}" required="required" style="width:50%"/> </td>
            <td> <input type="hidden" name="warehousepolicy[effectiveFrom]" id="altpickDate_from" value="{$warehouse[effectiveFrom_formatted]}"/></td>
        </tr>
        <tr><td>{$lang->eftodate}  </td>
            <td> <input type="text" id="pickDate_to" autocomplete="off" tabindex="2" value="{$warehouse[effectiveTo_output]}" required="required" style="width:50%"/></td>
            <td> <input type="hidden" name="warehousepolicy[effectiveTo]" id="altpickDate_to" value="{$warehouse[effectiveTo_formatted]}"/></td>
        </tr>
        <tr><td>{$lang->rate}</td>
            <td><input type="number"  step="any" name="warehousepolicy[rate]" value="{$warehouse[rate]}" style="width:50%"/></td>
        </tr>
        <tr><td>{$lang->currency}</td>
            <td>{$currencies_list}</td>
        </tr>
        <tr><td>{$lang->reateuom}</td>
            <td>{$reateuom}</td>
        </tr>
        <tr><td>{$lang->dateperiod}</td>
            <td><input type="number"  step="any" name="warehousepolicy[datePeriod]"  value="{$warehouse[datePeriod]}" style="width:50%"/></td>
        </tr>
        {$audittrail}
    </table>
    <input type="submit" id="perform_aro/managewarehousepolicies_Button" value="Save" class="button"/>
</form>
<div id="perform_aro/managewarehousepolicies_Results"></div>

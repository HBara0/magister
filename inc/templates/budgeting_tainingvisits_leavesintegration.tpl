<form name="trainingvisitsleaves" id="trainingvisitsleaves"  action="index.php?module=budgeting/trainingvisits&source=import&affid={$core->input[financialbudget][affid]}&year={$core->input[financialbudget][year]}" method="post">
    <div class="ui-state-highlight ui-corner-all datatable" style="padding-left: 5px; height:260px;  margin-bottom:10px; overflow: auto;">
        <p><h2 style="font-size: 19px;  font-weight: bolder; color:red">Warnings</h2><strong>{$lang->warningimport}:</strong> </p>
        <p class="subtitle">Fill based on existing leaves</p>
        <table class="datatable" border="0" cellpadding="1" cellspacing="2" width="100%">
            <thead>
            <th width="1%"></th>
            <th width="20%">{$lang->employee}</th>
            <th width="30%">{$lang->date}</th>
            <th width="30%">{$lang->reason}</th>
            <th width="30%">{$lang->totalleavecost}</th>
            </thead>
            <tbody>
                {$budgeting_tainingvisitleaves_rows}
                <tr><td><input type="submit" disabled="disabled"  id="trainingvisitsleaves_Button" value="{$lang->import}"  class="button"/></td></tr>
            </tbody>


        </table>
    </div>
</form>
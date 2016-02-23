<div style="display:inline-block; width:50%;"><h1>{$lang->workshiftmanagement}</h1></div><div style="float: right; display:inline-block; text-align:right; width:30%; font-weight:bold; border-bottom: #E6E8E8 solid 1px;"><a href="#" id="showpopup_addworkshift" class="showpopup"><img src="images/addnew.png" border="0" alt="{$lang->createworkshift}" /> {$lang->createworkshift}</a></div>
<form name='change_hr/employeeslist_Form' id="change_hr/employeeslist_Form" method="post">
    <input type="hidden" id="action" name="action" value="do_setworkshifts" />
    <table class="datatable">
        <tr><th>{$lang->workshift}</th><th>{$lang->fromdate}</th><th>{$lang->todate}</th></tr>
        <tbody id="shifts_tbody">
            {$shift_row}
        </tbody>
        <tr><td colspan="3"><img src="images/add.gif" id="addmore_shifts" alt="{$lang->add}"></td></tr>
        <tr><td colspan="3" class="subtitle">{$lang->employeeslist}</td></tr>
        <tr><td colspan="3">{$users_list}</td></tr>
        <tr>
            <td colspan="3"><input type='button' class='button' value='{$lang->savecaps}' id='change_hr/employeeslist_Button' /></td>
        </tr>
    </table>
</form>
<div id="change_hr/employeeslist_Results"></div>
{$addworkshift_popup}

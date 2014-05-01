<div id="popup_addworkshift" title="{$lang->createworkshift}">
    <form name='add_hr/employeeslist_Form' id="add_hr/employeeslist_Form" method="post" >
        <input type="hidden" id="action" name="action" value="do_addworkshift" />
        <table width="100%">
            <tr>
                <td>{$lang->ondutytime}</td>
                <td>{$ondutyhour_selectlist} {$ondutymins_selectlist}</td>
                <td>{$lang->offdutytime}</td>
                <td>{$offdutyhour_selectlist} {$offdutymins_selectlist}</td>
            </tr>
            <tr>
                <td>{$lang->days}</td>
                <td colspan="3">{$weekdays_checkbox}</td>
            </tr>
        </table>
        <hr />
        <input type='button' class='button' value='{$lang->savecaps}' id='add_hr/employeeslist_Button' />
    </form>
    <div id="add_hr/employeeslist_Results" ></div>
</div>
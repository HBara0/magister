<div class="container">
    <h1>{$lang->addadditionalbalance}</h1>
    <form name='add_attendance/addadditionaldays_Form' id="add_attendance/addadditionaldays_Form" method="post">
        <input type="hidden" id="action" name="action" value="do_addadditionaldays" />
        <table width="100%">
            <tr>
                <td style="width:25%;">{$lang->employeename}</td>
                <td style="width:25%;">{$lang->additionaldays}</td>
                <td style="width:25%;">{$lang->date}</td>
                <td style="width:25%;">{$lang->justification}</td>
            </tr>
            <tr>
                <td> <select id="AttendanceAddDays[uid]" name="AttendanceAddDays[uid][]" size="5" tabindex="1" multiple="multiple" required='required' style="width:160px;">{$users_list}</select> </td>
                <td style="vertical-align:top"><input type="text" id='AttendanceAddDays[numDays]' name="AttendanceAddDays[numDays]" required='required' size="5" accept="numeric" tabindex="2"/></td>
                <td style="vertical-align:top"><input type='text' id='pickDate_date' autocomplete='off' tabindex="3" required='required'/><br />
                    <input type='hidden' name='AttendanceAddDays[date]' id='altpickDate_date' />
                    <input type='checkbox' name='AttendanceAddDays[correspondToDate]' id='AttendanceAddDays[correspondToDate]' value="1" checked="checked">{$lang->correspondtoperiod}</td>
                <td style="vertical-align:top"><input type="text" id='AttendanceAddDays[remark]' name="AttendanceAddDays[remark]" size="40" tabindex="4" required='required' /></td>
            </tr>

            <tr>
                <td colspan="4"><hr/><input type='submit' class='button' value='{$lang->savecaps}' id='add_attendance/addadditionaldays_Button' /></td>
            </tr>
        </table>
    </form>
    <div id="add_attendance/addadditionaldays_Results"></div>
</div>
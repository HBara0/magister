<div id="popup_addattendancerecord" title="{$lang->addattendancerecord}">
    <form name='change_attendance/attendancerecords_Form' id="change_attendance/attendancerecords_Form" method="post">
        <input type="hidden"  name="action" value="do_addattendancerecord" />
        <table>
            <tr>
                <td>
                    {$lang->username}
                </td>
                <td>
                    {$usernamelist}
                </td>
            </tr>
            <tr>
                <td>
                    {$lang->operation}
                </td>
                <td>
                    {$typeslist}
                </td>
            </tr>
            <tr>
                <td>
                    {$lang->time}
                </td>
                <td> <div>
                        <input type="text" id="pickDate_record" autocomplete="off" tabindex="2" value="{$time['date']}" required='required' />
                        <input type="hidden" name="time[date]" id="altpickDate_record" value="{$time[date]}"/>
                        <input type="time" value="{$time[hours]}" name="time[time]" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="{$current_date[hours]}:{$current_date[minutes]}" required="required">
                    </div>
                </td>
            </tr>
        </table>
        <input type='button' class='button' value='{$lang->savecaps}' id='change_attendance/attendancerecords_Button' />
    </form>
    <div id="change_attendance/attendancerecords_Results" ></div>
</div>
<div id="popup_updateattrecords" title="{$lang->editattendancerecord}">
    <form name='change_attendance/attendancerecords_Form' id="change_attendance/attendancerecords_Form" method="post">
        <input type="hidden"  name="action" value="do_editattendancerecord" />
        <input type="hidden" name="record[aarid]" value="{$record->aarid}" />
        <div>
            <span>{$user->displayName}</span>
            <br><br>
            {$lang->operation} : <span style="width:150px">{$type}</span>
            <input type="hidden" name="record[uid]"  value="{$user->uid}"/>
            <input type="hidden" name="record[aarid]"  value="{$record->aarid}"/>
        </div>
        <hr>
        <div {$show_lastupdated}>
            {$lang->lastupdatedtime} :  <span>{$lastupdated_time}</span><br>
        </div>
        </hr>
        <div>
            <input type="text" id="pickDate_record" autocomplete="off" tabindex="2" value="{$time['date']}" required='required' />
            <input type="hidden" name="time[date]" id="altpickDate_record" value="{$time[date]}"/>
            <input type="time" value="{$time[hours]}" name="time[time]" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="{$current_date[hours]}:{$current_date[minutes]}" required="required">
        </div>
        <input type='button' class='button' value='{$lang->savecaps}' id='change_attendance/attendancerecords_Button' />
    </form>
    <div id="change_attendance/attendancerecords_Results" ></div>
</div>
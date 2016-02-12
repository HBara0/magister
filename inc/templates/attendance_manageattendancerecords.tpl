<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->manageattendancerecords}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->manageattendancerecords}</h1>
            <form action="#" method="post" id="perform_attendance/manageattendancerecords_Form" name="perform_attendance/manageattendancerecords_Form" style="margin-bottom: 0px;">
                <table class="table-hover" width="100%">
                    <tr>
                        <td colspan="4">
                            <div style="width:100%; height:150px; overflow:auto; vertical-align:top; margin-bottom: 10px;">
                                <table class="datatable" width="100%">
                                    <thead>
                                        <tr>
                                            <th width="100%"><input type="checkbox" id='users_checkall'><input class='inlinefilterfield' type='text' tabindex="2"  placeholder="{$lang->search} {$lang->users}" style="display:inline-block;width:70%;margin-left:5px;"/></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {$employeelist}
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {$lang->fromhour}
                        </td>
                        <td>
                            <input id="altpickTime_from" data-alternativename="timeFrom" type="time" tabindex="3" name="records[fromTime]" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="08:00"  required="required">
                        </td>
                        <td>
                            {$lang->tohourr}
                        </td>
                        <td>
                            <input id="altpickTime_to" data-alternativename="timeTo" type="time" tabindex="3" name="records[toTime]" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="08:00"  required="required">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {$lang->fromdate}
                        </td>
                        <td>
                            <input type="text" id="pickDate_from" autocomplete="off" tabindex="1" value="{$leave[fromDate_output]}" required="required"/>
                            <input type="hidden" name="records[fromDate]" id="altpickDate_from" value="{$leave[fromDate_formatted]}" /></td>
                        </td>
                        <td>
                            {$lang->todate}
                        </td>
                        <td>
                            <input type="text" id="pickDate_to" autocomplete="off" tabindex="1"  required="required"/>
                            <input type="hidden" name="records[toDate]" id="altpickDate_to" value="" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {$lang->newstatus}
                        </td>
                        <td>
                            {$newstatus_list}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type='submit' id='perform_attendance/manageattendancerecords_Button' value='{$lang->submit}' class='button'/>
                        </td>
                    </tr>
                </table>
            </form>
            <div id="perform_attendance/manageattendancerecords_Results" ></div>
        </td>
    </tr>
    {$footer}
</body>
</html>
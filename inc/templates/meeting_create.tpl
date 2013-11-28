<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->employeeslist}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->create}</h3>
            <form name="perform_meetings/create_Form" action="#" method="post" id="perform_meetings/create_Form" >
                <input type="hidden" value="do_{$action}meeting" name="action" id="action" />
                <input type="hidden" value="{$core->input[mtid]}" name="mtid"  />
                <table  cellpadding="1" cellspacing="1" width="100%" >
                    <tr>
                        <td>{$lang->title}</td>
                        <td><input type="text" tabindex="1" name="meeting[title]" size="40" required="required" value="{$meeting[title]}"/></td>
                    </tr>
                    <tr>
                        <td>{$lang->fromdate}</td>
                        <td><input type="text" tabindex="2" id="pickDate_from" autocomplete="off" tabindex="1"  name="meeting[fromDate]" value="{$meeting[fromDate_output]}" required="required"/><input type="hidden" name="meeting[altfromDate]" id="altpickDate_from" value="{$meeting[fromDate]}" /> <input type="time" tabindex="3" name="meeting[fromTime]" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="08:00" value="{$meeting[fromTime_output]}" required="required"></td>
                    </tr>
                    <tr>
                        <td>{$lang->todate}</td>
                        <td><input type="text" tabindex="4" id="pickDate_to" autocomplete="off" tabindex="1" name="meeting[toDate]"  value="{$meeting[toDate_output]}" required="required"/><input type="hidden" name="meeting[alttoDate]" id="altpickDate_to" value="{$meeting[toDate]}" /> <input type="time" name="meeting[toTime]" tabindex="5" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="17:00" value="{$meeting[toTime_output]}" required="required"></td>
                    </tr>

                    <tr>
                        <td>{$lang->description}</td>
                        <td><textarea class="texteditormin" tabindex="6" name="meeting[description]" cols="20" rows="5">{$meeting[description]}</textarea> </td>
                    </tr>
                    <tr>
                        <td>{$lang->location}</td>
                        <td><input type="text" name="meeting[location]" size="50" tabindex="7" value="{$meeting[location]}"/></td>
                    </tr>
                    <tr>
                        <td>{$lang->ispublic}</td>
                        <td><input type="checkbox" name="meeting[isPublic]"   tabindex="8" value="1" {$checkboxmeeting[isPublic]}/></td>
                    </tr>
                    <tr><td class="thead" colspan="3">{$lang->associations}<a title="{$lang->associations}" href="#associationssection" onClick="$('#associationssection').fadeToggle();">...</a></td></tr>
                    {$createmeeting_associations}
                    <tr>
                        <td colspan="2">
                            <hr /><input type="submit" class="button" value="{$lang->savecaps}" id="perform_meetings/create_Button" /> <input type="reset" class="button" value="{$lang->reset}"/>
                            <div style="display:table-cell;"id="perform_meetings/create_Results"></div>
                        </td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
</body>
</html>
<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$pagetitle}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$pagetitle}</h3>
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
                        <td><input type="checkbox" name="meeting[isPublic]" tabindex="8" value="1"{$checked_checkboxes[isPublic]}/></td>
                    </tr>
                    <tr><td class="thead" colspan="3">{$lang->associations}<a title="{$lang->associations}" href="#associationssection" onClick="$('#associationssection').fadeToggle();">...</a></td></tr>
                    {$createmeeting_associations}
                    <tr><td class="thead" colspan="3">{$lang->attendees}</td>  </tr>
                </table>
                <table width="100%">
                    <tr> <th>{$lang->notify_user}<input type="checkbox" id='notify_user' name='meeting[attendees][notifyuser]' value="1"  title="{$lang->notify_user}"></th>
                        <th>{$lang->notifyrep}<input type="checkbox"{$checked} id='notify_representative' name='meeting[attendees][notifyrep]' value="1"  title="{$lang->notifyrep}"></th> </tr>
                    <tr>
                        <td><div style="display: inline-block;">{$createmeeting_userattendees}</div> </td> 
                        <td>  <div style="display: inline-block;"> {$createmeeting_repattendees}</div></td> 
                    </tr>
                    <tr>  <td><img src="images/add.gif" id="addmore_attendees" alt="Add"></td>  <td><img src="images/add.gif" id="addmore_rep" alt="Add" title="{$lang->addmorerows}"></td></tr>

                    <tr>
                        <td colspan="2">
                            <hr /><input type="submit" class="button" value="{$lang->savecaps}" id="perform_meetings/create_Button" /> <input type="reset" class="button" value="{$lang->reset}"/>
                            <div id="perform_meetings/create_Results"></div>
                        </td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
</body>
</html>
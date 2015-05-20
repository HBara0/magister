<tr class="altrow">
    <td style="vertical-align: top;">
        <input type="hidden" value

               <div style="display: inline-block;">
            <table border="0" width="50%" cellspacing="1" cellpadding="1">
                <tbody id="actionsusers{$arowid}_{$userrowid}_tbody">
                    {$actions_users}
                </tbody>
                <tr><td>
                        <input name="numrows_actionsusers{$arowid}" type="hidden" id="numrows_actionsusers_{$reprowid}" value="{$$userrowid}">
                        <img src="./images/add.gif" id="ajaxaddmore_meetings/minutesmeeting_actionsusers_{$userrowid}" alt="{$lang->add}">
                    </td>
                </tr>
            </table>
        </div>
    </td>
    <td style="vertical-align: top;">
        <div style="display: inline-block;">
            <table border="0" width="50%" cellspacing="1" cellpadding="1">
                <tbody id="actionsrepresentatives{$arowid}_{$reprowid}_tbody">
                    {$actions_representatives}
                </tbody>
                <tr><td>
                        <input name="numrows_actionsrepresentatives{$arowid}" type="hidden" id="numrows_actionsrepresentatives_{$reprowid}" value="{$reprowid}">
                        <img src="./images/add.gif" id="ajaxaddmore_meetings/minutesmeeting_actionsrepresentatives_{$reprowid}" alt="{$lang->add}">
                    </td>
                </tr>
            </table>
        </div>
    </td>
    <td>
    </td>
</tr>
<tr>
    <td>
        <input type="hidden" name="mof['actions'][{$arowid}][momid]" value="{}"/>
        <input type="checkbox" name="mof['actions'][{$arowid}][isTask]" {$simple_check} value="1"/>{$lang->addasatask}
    </td>
    <td>
        {$lang->when}
        <input type="text" id="pickDate_from" autocomplete="off"/>
        <input type="hidden" name="fromDate" id="altpickDate_from" name="mof['actions'][{$arowid}][when]"/>
    </td>
    <td>
        {$lang->what}
        <textarea name="mof['actions'][{$arowid}][what]" id="" cols="25" rows="5" style="vertical-align: top" >{}</textarea>
    </td>
</tr>
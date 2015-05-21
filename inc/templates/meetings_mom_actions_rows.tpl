<tr class="altrow">
    <td colspan="2">
        {$lang->what}<br/>
        <textarea name="mof[actions][{$arowid}][what]" id="" cols="70" rows="5" style="vertical-align: top" {$disabled}>{$actions_data[what]}</textarea>
    </td>
    <td>
        {$lang->when}<br/>
        <input type="text" id="pickDate_{$arowid}_from" autocomplete="off" value="{$actions_data[date_otput]}" {$disabled}/>
        <input type="hidden" id="altpickDate_{$arowid}_from" name="mof[actions][{$arowid}][date]" value="{$actions_data[date_otput]}"/>
    </td>
</tr>
<tr>
    <td style="vertical-align: top;width:20%;">
        <input type="hidden" value="{$checksum[actions]}" name="mof[actions][{$arowid}][inputChecksum]"/>

        <div style="display: inline-block;width:100%">
            <table border="0" cellspacing="1" cellpadding="1" width="100%">
                <thead>
                    <tr>
                        <td>{$lang->employee}
                            <input type="hidden" value="{$checksum[users]}" name="mof[actions][{$arowid}][users][{$userrowid}][inputChecksum]"/>
                        </td>
                    </tr>
                </thead>
                <tbody id="actionsusers_{$arowid}_tbody">
                    {$actions_users}
                </tbody>
                <tr {$display}>
                    <td>
                        <input type="hidden" name="ajaxaddmoredata[arowid]" id="ajaxaddmoredata_arowid" value="{$arowid}"/>
                        <input name="numrows_actionsusers" type="hidden" id="numrows_actionsusers_{$arowid}" value="{$userrowid}">
                        <img src="./images/add.gif" id="ajaxaddmore_meetings/minutesmeeting_actionsusers_{$arowid}" alt="{$lang->add}">
                    </td>
                </tr>
            </table>
        </div>
    </td>
    <td style="vertical-align: top;width:20%;">
        <div style="display: inline-block;width:100%">
            <table border="0" cellspacing="1" cellpadding="1" width="100%">
                <thead>
                    <tr>
                        <td>{$lang->representative}
                            <input type="hidden" value="{$checksum[representatives]}" name="mof[actions][{$arowid}][representatives][{$reprowid}][inputChecksum]"/>
                        </td>
                    </tr>
                </thead>
                <tbody id="actionsrepresentatives_{$arowid}_tbody">
                    {$actions_representatives}
                </tbody>
                <tr {$display}>
                    <td>
                        <input type="hidden" name="ajaxaddmoredata[arowid]" id="ajaxaddmoredata_arowid" value="{$arowid}"/>
                        <input name="numrows_actionsrepresentatives" type="hidden" id="numrows_actionsrepresentatives_{$arowid}" value="{$reprowid}">
                        <img src="./images/add.gif" id="ajaxaddmore_meetings/minutesmeeting_actionsrepresentatives_{$arowid}" alt="{$lang->add}">
                    </td>
                </tr>
            </table>
        </div>
    </td>
    <td style="width:30%;">
        <input type="hidden" name="mof[actions][{$arowid}][momid]" value="{}"/>
        <input type="checkbox" name="mof[actions][{$arowid}][isTask]" {$simple_check} value="1" {$checked} {$disabled}/>{$lang->addasatask}
    </td>
</tr>

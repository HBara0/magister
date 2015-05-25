<tr id="{$userrowid}" width="100%">
    <td>
        <input type='text' id='user_{$arowid}{$userrowid}_autocomplete' value="{$assignee_data[username]}" autocomplete='off' style="width:70%" {$disabled}/>
        <input type='hidden' id='user_{$arowid}{$userrowid}_id' name='mof[actions][{$arowid}][users][{$userrowid}][uid]' value="{$assignee_data[uid]}" />
        <div id='searchQuickResults_user_{$arowid}{$userrowid}' class='searchQuickResults' style='display:none;'></div>
        <input type="hidden" value="{$checksum[users]}" name="mof[actions][{$arowid}][users][{$userrowid}][inputChecksum]"/>
        <input type="hidden" value="{$arowid}{$userrowid}"/>

    </td>
</tr>
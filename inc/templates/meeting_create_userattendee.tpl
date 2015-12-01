<tr id="{$rowid}" class="{$altrow}">
    <td>{$lang->employee} <input type="hidden" name="meeting[attendees][uid][{$rowid}][matid]" value="{$matid}"/></td>
    <td>
        <input type='text'id='user_{$rowid}_QSearch' value="{$meeting[attendees][$matid][name]}" autocomplete='off' size='40px'/>
        <input type='hidden' id='user_{$rowid}_id' name='meeting[attendees][uid][{$rowid}][id]' value="{$meeting[attendees][$matid][uid]}" />
        <div id='searchQuickResults_user_{$rowid}' class='searchQuickResults' style='display:none;'></div>
        <!--<input type='text'id='user_{$rowid}_autocomplete' value="{$meeting[attendees][$matid][name]}" autocomplete='off' size='40px'/>
        <input type='hidden' id='user_{$rowid}_id' name='meeting[attendees][uid][{$rowid}][id]' value="{$meeting[attendees][$matid][uid]}" />
        <div id='searchQuickResults_user_{$rowid}' class='searchQuickResults' style='display:none;'></div> -->
    </td>
</tr>
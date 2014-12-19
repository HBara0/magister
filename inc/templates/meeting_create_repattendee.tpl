<tr id="{$reprowid}" class="{$altrow}">
    <td>{$lang->representative} <input type="hidden" name="meeting[attendees][rpid][{$reprowid}][matid]" value="{$matid}"/></td>
    <td><input type='text'id='representative_{$reprowid}_autocomplete' value="{$meeting[attendees][$matid][name]}" autocomplete='off' size='40px'/>
        <input type='hidden' id='representative_{$reprowid}_id' name='meeting[attendees][rpid][{$reprowid}][id]' value="{$meeting[attendees][$matid][id]}" />
        <a href='#representative_{$reprowid}_id' id='addnew_meetings/create_representative'><img src='images/addnew.png' border='0' alt='{$lang->add}'/></a>
        <div id='searchQuickResults_representative_{$reprowid}' class='searchQuickResults' style='display:none;'></div>
    </td>
</tr>
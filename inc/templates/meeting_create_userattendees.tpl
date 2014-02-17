 
<div style="display:block;"> 
    <table  border="0" width="50%" cellspacing="1" cellpadding="1">
        <tbody id="attendees_tbody">
            <tr id="1" class="{$altrow}">
        <input type="hidden"   name="meeting[attendees][{$rowid}][matid]" value="{$matid}"/>
        <td>{$lang->employee}  </td>
        <td>
            <input type='text'id='user_{$rowid}_QSearch' value="{$meeting[attendees][user]}"  autocomplete='off' size='40px' placeholder="select user "/>
            <input type='hidden' id='user_{$rowid}_id' name='meeting[attendees][{$rowid}][uid]' value="{$meeting[attendees][uid]}" />
            <div id='searchQuickResults_user_{$rowid}' class='searchQuickResults' style='display:none;'></div> </select>
        </td>

        </tbody>  
    </table>  
</div>

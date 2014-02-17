<div style="display:block;">
    <table  border="0" width="50%" cellspacing="1" cellpadding="1">
        <tbody id="rep_tbody">
            <tr id="1" class="{$altrow}">
        <input type="hidden" name="meeting[attendees][{$reprowid}][matid]" value="{$matid}"/>
                <td>{$lang->representative}</td>
                <td><input type='text'id='representative_{$reprowid}_QSearch' value="{$meeting[attendees][rep]}" autocomplete='off' size='40px' placeholder="select representative "/>
                    <input type='hidden' id='representative_{$reprowid}_id' name='meeting[attendees][{$reprowid}][repid]' value="{$meeting[attendees][repid]}" />
                    <div id='searchQuickResults_representative_{$reprowid}' class='searchQuickResults' style='display:none;'></div>
                </td>
                
        </tbody>
    </table>
</div>

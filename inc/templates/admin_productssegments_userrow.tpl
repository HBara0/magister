<tr id='{$users_counter}'>   
    <td width="10%"></td>
    <td>
        <input type='text' id='user_{$users_counter}_QSearch' autocomplete='off' size='30px'/>
        <input type='hidden' id='user_{$users_counter}_id' name='coordinators[{$users_counter}][uid]' value="{$user[uid]}"/>
        <div id='searchQuickResults_user_{$users_counter}' class='searchQuickResults' style='display:none;'></div>
    </td>
    <td> <td><input type="text" id="pickDate_{$users_counter}_from" autocomplete="off" tabindex="1" value=""/><input type="hidden" name="coordinators[{$users_counter}][fromDate]" id="altpickDate_{$users_counter}_from" value="" /></td>
</tr>


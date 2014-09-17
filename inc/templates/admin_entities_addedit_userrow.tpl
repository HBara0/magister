<tr id='{$users_counter}'>
    <td>
        <input type='text' id='user_{$users_counter}_autocomplete' autocomplete='off' size='40px' value="{$val[username]}"/><input type='hidden' id='user_{$users_counter}_id' name='users[{$users_counter}][uid]' value="{$val[uid]}"/><div id='searchQuickResults_user_{$users_counter}' class='searchQuickResults' style='display:none;'></div>
    </td>
    <td>
        {$affiliates_list_userssection}
    </td>
    <td>
        <input type='checkbox' name='users[{$users_counter}][isValidator]' id='user_{$users_counter}_validator'{$validator_checked[$val[uid]]}>
    </td>
</tr>
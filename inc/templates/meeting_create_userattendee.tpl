<tr id="{$rowid}" class="{$altrow}">
    <td>{$lang->employees} <input type="hidden" name="meeting[attendees][uid][matids]" value="{$matids}"/></td>
    <td>
        <input type="text" id="tokeninput_user_input" name="meeting[attendees][uid][ids]" />
        {$jquery_tokeninput}
    </td>
</tr>
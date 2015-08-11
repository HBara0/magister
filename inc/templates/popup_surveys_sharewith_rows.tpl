<tr class='{$rowclass}'>
    <td width="1%"><input name="sharesurvey[]" type="checkbox"{$checked} value="{$user[uid]}"></td>
    <td><a href='./users.php?action=profile&uid={$user[uid]}' target='_blank'>{$user[displayName]}</a></td>
    <td>{$user[mainAffiliate]}</td>
    <td>{$user[position]}</td>
<tr>
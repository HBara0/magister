<tr class='{$rowclass}'>
    <td width="1%"><input name="invitations[$invitationsgroup][{$user[uid]}]" type="checkbox"{$checked} value="{$user[uid]}" {$display[invitationcheckbox]}></td>
    <td><a href='./users.php?action=profile&uid={$user[uid]}' target='_blank'>{$user[displayName]}</a></td>
    <td>{$user[mainaffiliate]}</td>
    <td>{$userpositions}</td>
    <td>{$usersegments}</td>
<tr>
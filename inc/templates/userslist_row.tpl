<tr class='{$class}'>
    <td>{$user[name]}</td>
    <td><a href='./users.php?action=profile&uid={$user[uid]}' target='_blank'>{$user[displayName]}</a></td>
    <td>{$user[mainaffiliate]}</td>
    <td>{$userpositions}</td>
    <td>{$user[reportsToName]}</td>
    <td style="text-align:right; padding: 0px;">{$skypelink} <a href="mailto:{$user[email]}"><img src='./images/icons/send.gif' alt='{$user[email]}' border='0' /></a> <a style="cursor:pointer;" href="{$core->settings['rootdir']}/users.php?action=downloadvcard&uid={$user[uid]}"><img src="./images/editprofile.gif" title="{$lang->downloadcontact}"/></a></td>
</tr>
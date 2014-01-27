<tr>
    <td style="width: 40%"><a href="index.php?module=meetings/viewmeeting&referrer=list&mtid={$meeting[mtid]}" target="_blank">{$meeting[title]}</a></td>
    <td style="width: 10%">{$meeting[fromDate_output]}</td>
    <td style="width: 40%">{$meeting[location]}</td>
    <td style="width: 10%"><a href="{$DOMAIN}users.php?action=profile&uid={$meeting[createdBy]}" target="_blank">{$meeting[businessMgr]}</a></td>
</tr>
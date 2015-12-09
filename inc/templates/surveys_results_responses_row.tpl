<tr>
    <td><a href="index.php?module=surveys/viewresponse&identifier={$response[identifier]}" target="_blank">{$response[identifier]}</a></td>
    <td><a href="users.php?action=profile&uid={$response[uid]}" target="_blank">{$response[respondant]}</a></td>
    <td>{$response[time_output]}</td>
    {$passedcolumns}
</tr>
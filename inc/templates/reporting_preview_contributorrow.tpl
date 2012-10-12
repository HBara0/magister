<tr>
    <td style='font-weight:bold; width:15%; text-align:left;'>{$lang->reportpreparedby_text} </td>
    <td class='namescell' style='width:30%;'>{$employee[firstName]} {$employee[lastName]}</td>
    <td style='padding-right: 5px; text-align:right; width:15%;'>{$lang->email_text} </td>
    <td class='namescell' style='width:30%;'><a href='mailto:{$employee[email]}'>{$employee[email]}</a></td>
</tr>
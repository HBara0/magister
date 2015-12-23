<tr class="{$rowclass}" id="leave_{$leave[lid]}">
    <td>{$leave[employeename]}</td>
    <td>{$leave['requestTime_output']}</td>
    <td>{$leave[fromDate_output]}</td>
    <td>{$leave[toDate_output]}</td>
    <td><a href=index.php?module=attendance/viewleave&id={$leave[lid]}>{$leave['type_output']}</a></td>
    <td>&nbsp;{$approve_link}</td>
    <td>{$edit_link}&nbsp;{$revoke_link}&nbsp;{$tm_link}</td>
</tr>
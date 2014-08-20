<tr class="{$rowclass}" id="leave_{$leave[lid]}">
    <td><a href=index.php?module=attendance/viewleave&id={$leave[lid]}>{$leave[employeename]}</a></td>
    <td>{$leave['requestTime_output']}</td>
    <td>{$leave[fromDate_output]}</td>
    <td>{$leave[toDate_output]}</td>
    <td>{$leave['type_output']}</td>
    <td>&nbsp;{$approve_link}</td>
    <td>{$edit_link}&nbsp;{$revoke_link}</td>
</tr>
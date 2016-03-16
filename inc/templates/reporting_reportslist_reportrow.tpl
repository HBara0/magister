<tr class="{$rowclass}">
    <td>{$checkbox[$report[rid]]}</td>
    <td>{$report[affiliatename]} {$filters[affid][$report[affid]]}</td>
    <td>{$report[suppliername]} {$filters[spid][$report[spid]]}</td>
    <td>{$report[quarter]} {$filters[quarter][$report[quarter]]}</td>
    <td>{$report[year]} {$filters[year][$report[year]]}</td>
    <td><a href="#status_{$report[rid]}" id="status_{$report[rid]}" rel="{$report[rid]}">{$report[status]}</a></td>
    <td>{$rep_tools}</td>
</tr>
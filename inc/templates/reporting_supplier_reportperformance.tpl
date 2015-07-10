<tr >
    <td colspan="2" style="background-color:#d0f6aa">
        {$lang->supplier}: <a href="index.php?module=reporting/performance&year={$report_data[year]}&quarter={$report_data[quarter]}&spid={$report[spid]}&excludecharts=1">{$report[supplier]}</a>
    </td>
</tr>
<tr>
    <td>{$lang->avgmkrrating}</td>
    <td>
        <div class="ratebar" style="display:inline-block; background-color:#FFF;">
            <div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-readonly="true" data-rateit-value=" {$avgrating[supplier]}"></div>
        </div>
        <strong> {$avgrating[supplier]}</strong>
    </td>
</tr>
<tr>
    <td style="vertical-align: top">
        {$lang->daystocompletion}:
        <ul style="list-style-type:none">
            <li>From Quarter Start</li><li>From report Creation</li>
        </ul>
    </td>
    <td>{$report[status_output]}
        {$report[daysfromqstart]}<br/>{$report[daysfromreportcreation]}
    </td>
</tr>

<tr>
    <td style="vertical-align: top">
        {$lang->daystoimportdata}:
        <ul style="list-style-type:none">
            <li>From Quarter Start</li><li>From report Creation</li>
        </ul>
    </td>
    <td>{$report[daystoimportfromqstart]}<br/> {$report[daystoimportfromcreation]}

    </td>
</tr>
{$mkr_rating}


<tr>
    <td class="thead" colspan="2">
        {$lang->affiliate}: <a href="index.php?module=reporting/performance&year={$report_data[year]}&quarter={$report_data[quarter]}&affid={$affiliate->affid}">{$affiliate->get_displayname()}</a>
    </td>
</tr>
<tr>
    <td>{$lang->avgmkrrating}</td>
    <td>
        <div class="ratebar" style="display:inline-block; background-color:#FFF;">
            <div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-readonly="true" data-rateit-value="{$avgmkrrating[$affiliate->get_displayname()]}"></div>
        </div>
        <strong> {$avgmkrrating[$affiliate->get_displayname()]}</strong>
    </td>
</tr>
<tr>
    <td>{$lang->avg} {$lang->daystocompletion} - {$lang->frombeginingofquarter}</td>
    <td>  {$avgperaff[daysfromqstart][$affiliate->get_displayname()]}</td>
</tr>
<tr>
    <td>
        {$lang->avg} {$lang->daystocompletion} - {$lang->fromcreationdate}
    </td>
    <td>
        {$avgperaff[daysfromreportcreation][$affiliate->get_displayname()]}
    </td>
</tr>
<tr>
    <td>
        {$lang->avg} {$lang->daystoimportdata} - {$lang->frombeginingofquarter}
    </td>
    <td>
        {$avgperaff[daystoimportfromqstart][$affiliate->get_displayname()]}
    </td>
</tr>
<tr>
    <td>{$lang->avg} {$lang->daystoimportdata} - {$lang->fromcreationdate}</td>
    <td>   {$avgperaff[daystoimportfromcreation][$affiliate->get_displayname()]}</td>
</tr>

{$supplier_reportperformance}

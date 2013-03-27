<table class="reportbox">
    <tr><td colspan="10" class="thead">{$lang->progressionyearsby} {$lang->$aggregate_type} - <em>{$lang->$category} ({$categories_uom[$category]})</em></td></tr>
    <tr>
        <td width="270px" class="columnhead">&nbsp;</td>
        <td colspan="3" align="center" class="columnhead" width="230px" >{$report_years[before_2years]}</td>
        <td colspan="3" align="center" class="columnhead" width="230px" >{$report_years[before_1year]}</td>
        <td colspan="3" align="center" class="columnhead" width="230px" >{$report_years[current_year]}</td>
    </tr>
    {$reporting_report_newtotaloverviewbox_row[$aggregate_type][$category]}
<tr>
</table>
<br />
<br />
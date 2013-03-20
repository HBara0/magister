<table width="100%" cellpadding="0" cellspacing="0" class="reportbox">
    <tr><td colspan="10" width="130" class="thead">{$lang->progressionyearsby} {$lang->$aggregate_type} - <em>{$lang->$category} ({$categories_uom[$category]})</em></td></tr>
    <tr>
        <td class="columnhead">&nbsp;</td>
        <td colspan="3" align="center" class="columnhead">{$report_years[before_2years]}</td>
        <td colspan="3" align="center" class="columnhead">{$report_years[before_1year]}</td>
        <td colspan="3" align="center" class="columnhead">{$report_years[current_year]}</td>
    </tr>
    {$reporting_report_newtotaloverviewbox_row[$aggregate_type]}
</table>
<br />   
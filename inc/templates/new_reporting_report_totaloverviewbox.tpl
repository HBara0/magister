<table class="reportbox">
    <tr><td colspan="10" class="thead">{$lang->progressionyearsby} {$lang->$aggregate_type} - <em>{$lang->$category} ({$categories_uom[$category]})</em></td></tr>
    <tr>
        <td class="totalbox_itemnamecell">&nbsp;</td>
        <td colspan="3" align="center" class="totalsbox_columnhead">{$report_years[before_2years]}</td>
        <td colspan="3" align="center" class="totalsbox_columnhead">{$report_years[before_1year]}</td>
        <td colspan="3" align="center" class="totalsbox_columnhead">{$report_years[current_year]}</td>
    </tr>
    {$reporting_report_newtotaloverviewbox_row[$aggregate_type][$category]}
  <tr>
      <td colspan="10" class="columnhead" style="height:2px;"></td>
  </tr>
    <tr>
  <td class="totalbox_itemnamecell" style="font-weight:bold;">{$lang->total}</td>
    <td class="totalbox_totalcell">{$progression_totals[data][$report_years[before_2years]]}</td>
    <td class='totalsbox_yearsep{$newtotaloverviewbox_row_percclass[$report_years[before_2years]]}'>&lsaquo;</td>
    <td class='{$newtotaloverviewbox_row_percclass[$report_years[before_2years]]}'>{$progression_totals[perc][$report_years[before_2years]]}%</td>
    <td class='totalsbox_yearsep{$newtotaloverviewbox_row_percclass[$report_years[before_2years]]}'>&rsaquo;</td>
    <td class="totalbox_totalcell">{$progression_totals[data][$report_years[before_1year]]}</td>
    <td class='totalsbox_yearsep{$newtotaloverviewbox_row_percclass[$report_years[before_1year]]}'>&lsaquo;</td>
    <td class='{$newtotaloverviewbox_row_percclass[$report_years[before_1year]]}'>{$progression_totals[perc][$report_years[before_1year]]}%</td>
    <td class='totalsbox_yearsep{$newtotaloverviewbox_row_percclass[$report_years[before_1year]]}'>&rsaquo;</td>
    <td class="totalbox_totalcell">{$progression_totals[data][$report_years[current_year]]}</td>
</tr>
</table>
<br />
<br />
<page>
    <table width="100%" cellpadding="0" cellspacing="0" class="reportbox">
<tr><td colspan="14" width="50px"class="thead">{$lang->quarterscomparison} - $category</td></tr>
  <tr>
    <td  width="185px" rowspan="2" class="columnhead">&nbsp;</td>
 
    <td colspan="4" align="center" class="columnhead">{$report_years[before_2years]}</td>
    <td colspan="4" align="center" class="columnhead altrow">{$report_years[before_1year]}</td>
    <td colspan="4" align="center" class="altrow2 columnhead">{$report_years[current_year]}</td>
  </tr>
  <tr>
        <td width="60px" class="columnsubhead">{$lang->q1}</td>
        <td width="60px" class="columnsubhead">{$lang->q2}</td>
        <td width="60px" class="columnsubhead">{$lang->q3}</td>
        <td width="60px" class="columnsubhead">{$lang->q4}</td>
        <td width="60px" class="columnsubhead altrow">{$lang->q1}</td>
        <td width="60px" class="columnsubhead altrow">{$lang->q2}</td>
        <td width="60px" class="columnsubhead altrow">{$lang->q3}</td>
        <td width="60px" class="columnsubhead altrow">{$lang->q4}</td>
        <td width="60px" class="columnsubhead altrow2">{$lang->q1}</td>
        <td width="60px" class="columnsubhead altrow2">{$lang->q2}</td>
        <td width="60px" class="columnsubhead altrow2">{$lang->q3}</td>
        <td width="60px" class="columnsubhead altrow2">{$lang->q4}</td>
  </tr>
  {$reporting_report_newoverviewbox_row[$aggregate_type][$category]}
</table>
<br />
</page>
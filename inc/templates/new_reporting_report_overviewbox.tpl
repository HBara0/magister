<page>
<table width="100%" border="1" cellpadding="0" cellspacing="0">
<tr><td colspan="14" width="50px"class="thead">{$lang->quarterscomparison}</td></tr>
  <tr>
    <td  width="185px"rowspan="2">&nbsp;</td>
 
    <td  colspan="4" align="center">{$report_years[before_2years]}</td>
    <td  colspan="4" align="center">{$report_years[before_1year]}</td>
    <td colspan="4" align="center">{$report_years[current_year]}</td>
  </tr>
  <tr>
      <td width="60px">{$lang->q1}</td>
        <td width="60px">{$lang->q2}</td>
        <td width="60px">{$lang->q3}</td>
        <td width="60px">{$lang->q4}</td>
        <td width="60px">{$lang->q1}</td>
        <td width="60px">{$lang->q2}</td>
        <td width="60px">{$lang->q3}</td>
        <td width="60px">{$lang->q4}</td>
        <td width="60px">{$lang->q1}</td>
        <td width="60px">{$lang->q2}</td>
        <td width="60px">{$lang->q3}</td>
        <td width="60px">{$lang->q4}</td>
  </tr>
  {$reporting_report_newoverviewbox_row[$aggregate_type][$category]}
</table>


</page>
<page>
<br />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td rowspan="{$cdetails_rowspan}" class="altrow2" valign="top" style="border-right: 1px solid #CCCCCC; width:50%;">
        <h2>{$lang->customerdetails}</h2>
		<span style="font-weight:bold">{$visitreport[customerdetails][companyName]}</span>
     	<p style="font-style:italic;">
			{$visitreport[customerdetails][addressDetails]}
        </p>
        <p>{$phone_details}</p>
       </td>
      <td style="border-right: 1px solid #91b64f; padding:5px;" class="altrow">{$lang->dateofvisit}</td>
      <td style="padding:5px;">{$visitreport[formatteddate]}</td>
    </tr>
    <tr>
      <td style="border-right: 1px solid #91b64f; padding:5px;" class="altrow">{$lang->calltype}</td>
      <td style="padding:5px;">{$visitreport[type]}</td>
    </tr>
  <tr>
    <td width="22%" style="border-right: 1px solid #91b64f; border-bottom: 1px dashed #91b64f; padding:5px;" class="altrow">{$lang->callpurpose}</td>
    <td width="28%" style="border-bottom: 1px dashed #91b64f; padding:5px;">{$visitreport[purpose]}</td>
  </tr>
  <tr>
    <td style="border-right: 1px solid #91b64f; padding:5px;">{$lang->productlinediscussed}</td>
    <td style="padding:5px;">{$reportproductlines}</td>
  </tr>
  <tr>
    <td style="border-right: 1px solid #91b64f; padding:5px;">{$lang->suppliername}</td>
    <td style="padding:5px;">{$reportsuppliers}</td>
  </tr>
  {$accompaniedbyrow}
  <tr>
    <td  style="border-top: 1px dashed #91b64f; text-align:center; padding:5px;"><strong>{$lang->supplystatus}</strong><br />
    {$visitreport[supplyStatus]}</td>
    <td  style="border-top: 1px dashed #91b64f; text-align:center; padding:5px;"><strong>{$lang->availabilityissues}</strong><br />
    {$visitreport[availabilityIssues]}</td>
  </tr>
  <tr>
  	<td class="altrow2" style="border-right: 1px solid #CCCCCC; padding:5px; width:50%;"><hr /><strong>{$lang->contactperson}:</strong> {$visitreport[contactperson]}</td>
    <td colspan="2" style="text-align:center;"><hr /><strong>{$lang->ourcurrentmktshare}</strong><br />
    {$visitreport[currentMktShare]}</td>
  </tr>
</table>
<br />
{$visitdetails}<br />
{$competitioncomments}
<p>
<span style="font-weight:bold;">{$lang->prepareby}:</span> {$visitreport[user]}
</p>
</page>
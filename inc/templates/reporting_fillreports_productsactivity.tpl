<html>
<head>
<title>{$core->settings[systemtitle]} | {$lang->productactivitydetails} - Q{$core->input[quarter]} {$core->input[year]} / {$core->input[supplier]} - {$core->input[affiliate]}</title>
{$headerinc}
<script src="{$core->settings[rootdir]}/js/fillreport.js" type="text/javascript"></script>
</head>
<body>
{$header}
<tr>
{$menu}
<td class="contentContainer">
<h3>{$lang->productactivitydetails}<div style="font-style:italic; font-size:12px; color:#888;">Q{$core->input[quarter]} {$core->input[year]} / {$core->input[supplier]} - {$core->input[affiliate]}</div></h3>
<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;"><p><strong>Important: Do not fill anything in this section until you are informed that data has been imported by the IT. Also, please do not finalize the report.</strong><br /><strong>Updates:</strong> <span class="red_text">It is now possible to use currencies other than USD; OCOS will convert them for you.</span><br /><strong>Notice:</strong> Only input <em>purchases</em> amounts and quantities relative to the report quarter, not up-to values.</p></div>
<form id="save_productsactivity_reporting/fillreport_Form" name="save_productsactivity_reporting/fillreport_Form" action="index.php?module=reporting/fillreport&amp;stage=keycustomers" method="post">
<input type="hidden" id="rid" name="rid" value="{$rid}">
<input type="hidden" id="identifier" name="identifier" value="{$identifier}">
<input type="hidden" id="numrows" name="numrows" value="{$productscount}">
<input type="hidden" id="baseCurrency" name="baseCurrency" value="{$core->input[baseCurrency]}">
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-left: 8px;">
    <thead>
        <tr>
            <td width="28%" rowspan="2" valign="top" align="left"><strong>{$lang->product}</strong></td>
			<td width="12%" rowspan="2" valign="top" align="center" class="altrow2 border_right"><strong>{$lang->soldquantity}</strong><br /><span class="smalltext"><em>{$lang->mt}</em></span></td>
            <td width="12%" rowspan="2" valign="top" align="center"><strong>{$lang->turnover}<br />
            </strong><span class="smalltext"><em>({$lang->purchaseamount})</em><strong><br />
            </strong><em>.K Currency (i.e. 1000=1k)</em></span></td>
            <td width="10%" rowspan="2" valign="top" align="center">&nbsp;</td>
            <td width="10%" rowspan="2" valign="top" align="center"><strong>{$lang->purchasedqty}<br /></strong><span class="smalltext"><em>{$lang->mt}</em></span></td>
            <td width="14%" rowspan="2" valign="top" align="center"><strong>{$lang->salestype}</strong></div></td>
            <td colspan="2" valign="top" align="center"><strong>{$lang->yearforecasts}/{$core->input[baseCurrency]}</strong></td>
        </tr>
        <tr>
            <td width="11%" valign="top" align="center"><span class="smalltext"><em><strong>{$lang->purchaseamount}</strong></em></span></td>
            <td width="9%" valign="top" align="center"><span class="smalltext"><em><strong>{$lang->purchaseqty}</strong></em></span></td>
        </tr>
    </thead>
    <tbody id="productsactivity_tbody">
         {$productsrows}
    </tbody>
    <tfoot>
      <tr>
      <td colspan="8">
      	<div style="float:left; width: 50%;">
        <img src="images/add.gif" id="addmore_productsactivity_product" alt="{$lang->add}"> 
        <div id="save_productsactivity_reporting/fillreport_Results"></div>
        </div>
      	<div style="margin-top: 6px; float:right; width:40%; text-align: right">
	<input type="button" value="{$lang->prevcaps}" class="button" onClick="javascript:history.go(-1);"/> <input type="submit" id="save_productsactivity_reporting/fillreport_Button" value="{$lang->savecaps}" class="button"/> <input type="button" value="{$lang->nextcaps}" id="showpopup_fillreportsconfirmnosave" class="button showpopup" />{$exludestage}
        </div>
       </td>
      </tr>
      </tfoot>
</table>
<div id="popup_fillreportsconfirmnosave" title="{$lang->addnewproduct}">
	<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;"><p>If you made changes, or input data, it is recommended to save; if you are only viewing, then click next.</p></div>

    Are you sure you want to continue without saving?<br />
    <hr />
    <input type="submit" onClick="$('input[id^=\'save_\'][id$=\'_Button\']').trigger('click'); $('#popup_fillreportsconfirmnosave').dialog('close');" value="{$lang->savecaps}" class="button" /> <input type="submit" onClick='$("form:first").unbind("submit").trigger("submit");' value="{$lang->nextcaps}" class="button" />
</div>
</form>
</td>
  </tr>
{$footer}
{$addproduct_popup}
</body>
</html>
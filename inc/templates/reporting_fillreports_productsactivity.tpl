<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;"><p><strong>Important: Fields in this stage, except the forecasts, are no longer editable. If you have any consideration about the numbers, please contact the auditor of the report.</strong></div>
<form id="save_productsactivity_reporting/fillreport_Form" name="save_productsactivity_reporting/fillreport_Form" action="index.php?module=reporting/fillreport&amp;stage=marketreport" method="post">
    <input type="hidden" id="rid" name="rid" value="{$rid}">
    <input type="hidden" id="transfill" name="transfill" value="{$transfill}">
    <input type="hidden" id="identifier" name="identifier" value="{$identifier}">
    <input type="hidden" id="numrows" name="numrows" value="{$productscount}">
    <input type="hidden" id="baseCurrency" name="baseCurrency" value="{$core->input[baseCurrency]}">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-left: 8px;">
        <thead>
            <tr>
                <td width="28%" rowspan="2" valign="top" align="left"><strong>{$lang->product}</strong></td>
                <td width="12%" rowspan="2" valign="top" align="center" class="yellowbackground border_right"><strong>{$lang->soldquantity}</strong><br /><span class="smalltext"><em>{$lang->mt}</em></span></td>
                <td width="12%" rowspan="2" valign="top" align="center" class='altrow2'><strong>{$lang->turnover}<br />
                    </strong><span class="smalltext"><em>({$lang->purchaseamount})</em><strong><br />
                        </strong><em>.K Currency (i.e. 1000=1k)</em></span></td>
                <td width="10%" rowspan="2" valign="top" align="center" class='altrow2'>&nbsp;</td>
                <td width="10%" rowspan="2" valign="top" align="center" class='altrow2'><strong>{$lang->purchasedqty}<br /></strong><span class="smalltext"><em>{$lang->mt}</em></span></td>
                <td width="14%" rowspan="2" valign="top" align="center"><strong>{$lang->salestype}</strong></div></td>
                <td colspan="2" valign="top" align="center"><strong>{$lang->yearforecasts}/{$core->input[baseCurrency]}</strong></td>
            </tr>
            <tr>
                <td width="11%" valign="top" align="center"><span class="smalltext"><em><strong>{$lang->purchaseamount}<em>.K Currency (i.e. 1000=1k)</em></span></strong></em></span></td>
                <td width="9%" valign="top" align="center"><span class="smalltext"><em><strong>{$lang->purchaseqty}<br /><em>{$lang->mt}</em></strong></em></span></td>
            </tr>
        </thead>
        <tbody id="productsactivity_tbody">
            {$productsrows}
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8">
                    <div style="float:left; width: 50%;">
                        <img src="./images/add.gif" style="cursor: pointer" id="ajaxaddmore_reporting/fillreport_productsactivity" alt="{$lang->add}">{$lang->addproductactivity}
                        <input name="numrows_productsactivity" type="hidden" id="numrows_productsactivity" value="{$rowid}">
                        <input type="hidden" name="ajaxaddmoredata[basecurrency]" id="ajaxaddmoredata_productsactivity" value="{$core->input['baseCurrency']}"/>
                        <input type="hidden" name="ajaxaddmoredata[quarter]" id="ajaxaddmoredata_productsactivity" value="{$qreport->quarter}"/>
                        <input type="hidden" name="ajaxaddmoredata[isauditor]" id="ajaxaddmoredata_productsactivity" value="{$core->input['auditor']}"/>

                    </div>
                    <div style="margin-top: 6px; float:right; width:40%; text-align: right">
                       <!-- <input type="button" value="{$lang->prevcaps}" class="button" onClick="javascript:history.go(-1);"/>-->
                        <input type="hidden" id="previewed_value" name="previewed_productsactiviy">
                        <input type="submit" id="save_productsactivity_reporting/fillreport_Button" value="{$lang->savecaps}" class="button"/>
                        <input type="button" value="{$lang->preview}" id="previewed_button" class="button"/> <br/>
                       <!-- <input type="button" value="{$lang->nextcaps}" id="showpopup_fillreportsconfirmnosave" class="button showpopup" />
                        -->
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
  <!--  <div id="popup_fillreportsconfirmnosave" title="{$lang->addnewproduct}">
        <div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;"><p>If you made changes, or input data, it is recommended to save; if you are only viewing, then click next.</p></div>

        Are you sure you want to continue without saving?<br />
        <hr />
        <input type="submit" onClick="$('input[id^=\'save_\'][id$=\'_Button\']').trigger('click');
                $('#popup_fillreportsconfirmnosave').dialog('close');" value="{$lang->savecaps}" class="button" /> <input type="submit" onClick='$("form:first").unbind("submit").trigger("submit");' value="{$lang->nextcaps}" class="button" />
    </div>
    -->
</form>

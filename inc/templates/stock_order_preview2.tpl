<h1>{$lang->preview}</h1>
<h1>{$lang->internalform}-{$lang->stockorder}-{$affiliate}</h1>
<table cellpadding="0" cellspacing="0" width="990px;">
    <tr>
        <td style="border-bottom: 1px solid #F2F2F2;width:50%;" width="50%">
            <table width="50%" style="text-align:left;">
                <tr>
                    <th>{$lang->nborder}</th><td>{$core->input[orderNumbers]}</td>
                </tr>
                <th>{$lang->type}</th><td>{$type}</td>
    </tr>
</table>
</td>
<td style="border-bottom: 1px solid #F2F2F2; width:50%;" width="50%">
    <table width="50%" style="text-align:left;">
        <tr><th>{$lang->date}</th><td>{$date}</td>
        </tr>
    </table>
</td>
</tr>
<tr>
    <td style="border-bottom: 1px solid #F2F2F2;width:50%;">
        <table width="50%" style="text-align:left;">
            <tr>
                <th>{$lang->currency}</th>
                <td>{$currency}</td>
                <th>{$lang->fxto}</th><td>{$core->input[fxUSD]}</td>
            </tr>
            <tr>
                <th>{$lang->ordervalue}</th><td>{$ordervalue}</td>
                <th>{$lang->palets}</th><td>{$core->input[paletsNum]}</td>
            </tr>
        </table>
    </td>
    <td style="border-bottom: 1px solid #F2F2F2; width:50%;" width="50%">
        <table width="70%" style="text-align:left;">
            <tr>
                <th>{$lang->netmarginusd}</th><td>{$total_net_margin_usd}</td>
                <th>{$lang->nbitems}</th><td>{$nbitems}</td>
            </tr>
            <tr>
                <th>{$lang->netmargin}</th><td>{$total_net_margin}</td>
                <th>{$lang->netweight}</th><td>{$net_weight}</td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <th colspan="14" style="text-align:center;" class="thead">{$lang->supplier}</th>
</tr>
<tr>
    <td colspan="2" style="border-bottom: 1px solid #F2F2F2;">
        <table style="text-align:left" width="60%">
            <tr>
                <td colspan="4" class="tdtop"></td>
            </tr>
            <tr>
                <td style="border-left:1px solid #F2F2F2; padding-left:20px;border-right:1px solid #F2F2F2;" colspan="4"> <h1>{$supplier}</h1></td>
            </tr>
            <tr>
                <th class="tdleft">{$lang->incoterms}</th>
                <td colspan="3" class="tdright">{$incoterms}&nbsp;({$incotermslocation})</td>
            </tr>
            <tr>
                <th class="tdleft">{$lang->paymentterms}</th>
                <td>{$core->input[supplierPaymentTermsDays]}</td>
                <th>{$lang->daysfrom}</th>
                <td class="tdright">{$supplierpaymenttermsfrom}</td>
            </tr>
            <tr>
                <th class="tdleft">{$lang->ets}</th><td>{$expectedshipingdate}</td>
                <th>{$lang->approx}</th>
                <td class="tdright">{$core->input[daysToDeliver]}</td>
            </tr>
            <tr>
                <td class="tdbottom" colspan="4"></td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <th colspan="2" style="text-align:center;" class="thead">{$lang->customer}</th>
</tr>
<tr>
    <td style="border-bottom: 1px solid #F2F2F2;" valign="top" colspan="2">
        <table style="text-align:left" width="60%">
            <tr>
                <td class="tdtop" colspan="4"></td>
            </tr>
            <tr class="subtitle">
                <th colspan="2" class="tdleft">{$lang->customer}</th><th colspan="2" class="tdright">{$lang->paymentterms}</th>
            </tr>
            {$customer_row}
            <tr>
                <th style="padding-top:30px;" class="tdleft">{$lang->custpayments}</th>
                <td style="padding-top:30px;">{$customerpaymentdate}</td>
                <th style="padding-top:30px;">{$lang->supplierpayment}</th>
                <td style="padding-top:30px;" class="tdright">{$supplierpaymentdate}</td>
            </tr>
            <tr>
                <td class="tdbottom" colspan="4"></td>
            </tr>
        </table>
    </td>
</tr>
<tr>
<tr>
    <th colspan="2" style="text-align:center;" class="thead">{$lang->product}</th>
</tr>
<td style="border-bottom: 1px solid #F2F2F2;" valign="top" colspan="2">
    <table style="text-align:left;vertical-align:top;" width="100%">
        <tr>
            <td class="tdtop" colspan="14"></td>
        </tr>
        <tr style="vertical-align:top;">
            <th style="font-size:11px;" class="tdleft">{$lang->product}</th><th style="font-size:11px">{$lang->packingtype}</th><th style="font-size:11px">{$lang->packing}</th><th style="font-size:11px;text-align:left;">{$lang->qty}</th><th style="font-size:11px">{$lang->daysinstock}</th>
            <th style="font-size:11px">{$lang->clearingfees}</th><th style="font-size:11px">{$lang->lcfees}</th><th style="font-size:11px">{$lang->buyingprice}</th><th style="font-size:11px">{$lang->sellingprice}</th><th style="font-size:11px">{$lang->total}</th><th style="font-size:11px">{$lang->costprice}</th><th style="font-size:11px">{$lang->grossmargin}</th><th style="font-size:11px">{$lang->netmarginusd}</th><th style="font-size:11px" class="tdright">{$lang->netmargin}</th>
        </tr>
        {$product_row}
        <tr>
            <td class="tdbottom" colspan="14"></td>
        </tr>
    </table>
</td>
</tr>
<tr>
    <th  colspan="2" style="text-align:center;" class="thead">{$lang->stocksupervision}</th>
</tr>
<tr>
    <td style="border-bottom: 1px solid #F2F2F2;" valign="top" colspan="2">
        <table style="text-align:left; width:70%;">
            <tr>
                <td class="tdtop" colspan="5"></td>
            </tr>
            <tr><td colspan="5" class="subtitle" style="text-align:center;border-left:1px solid #F2F2F2;border-right:1px solid #F2F2F2;">{$lang->actualpurchase}</td></tr>
            <tr>
                <th class="tdleft">{$lang->qty}</th><th>{$lang->total}</th><th>{$lang->etsofstockentry}</th><th>{$lang->shelflife}</th><th class='tdright'>{$lang->etsofsale}</th>
            </tr>
            <tr>
                {$actualpurchase}
            </tr>
            <tr>
                <td class="tdbottom" colspan="5"></td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <th  colspan="2"  style="text-align:center;" class="thead">{$lang->approvals}</th>
</tr>
<tr>
    <td valign="top" colspan="2">
        <table width="70%" style="text-align:left;">
            <tr>
                <td class="tdtop" colspan="4"></td>
            </tr>
            <tr>
                <th class="subtitle" style="border-left:1px solid #F2F2F2;">{$lang->submittedby}</th><th colspan="3" class="subtitle" style="margin-bottom:5px;border-right:1px solid #F2F2F2;">{$lang->purchasecommitteeapproval}</th>
            </tr>
            <tr>
                <th class="tdleft">{$lang->businessmanager}</th><th>{$lang->generalmanager}</th><th>{$lang->regionalmanager}</th><th class="tdright">{$lang->financemanager}</th>
            </tr>
            <tr>
                <td class="tdleft">{$users[$core->input[submittedBy]]}</td><td>{$users[$core->input[generalManager]]}</td><td>{$users[$core->input[regionalManager]]}</td><td class="tdright">{$users[$core->input[financeManager]]}</td>
            </tr>

            <tr>
                <td class="tdbottom" colspan="4"></td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td colspan="6">

        <form id="add_stock/stockorder_Form"  name="add_stock/stockorder_Form" method="post" >

            <input type="hidden" name="identifier" value="{$identifier}">
            <div align="center"><input type="button" value="{$lang->prev}" class="button" onClick="goToURL('index.php?module=stock/stockorder&identifier={$identifier}');"> <input type="button" id="add_stock/stockorder_Button" name="add_stock/stockorder_Button" value="{$lang->save}" class="button"></div>
        </form>

        <div id="add_stock/stockorder_Results"></div>
    </td>
</tr>
</table>
</form>

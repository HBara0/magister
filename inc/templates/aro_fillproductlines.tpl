<a class="header " href="#"><h2>{$lang->fillproductlines}</h2></a>
<div>
    <p>
    <table width="100%">
        <thead>
            <tr style="vertical-align: top;">
                <td width="" class="border_right" rowspan="2" valign="top" align="center">{$lang->product}</td>
                <td width="" class="border_right" rowspan="2" valign="top" align="center">{$lang->packaging}</td>
                <td width="" class="border_right" rowspan="2" valign="top" align="center">{$lang->quantity}</td>
                <td width="" class="border_right" rowspan="2" valign="top" align="center">{$lang->uom}</td>
                <td width="" class="border_right" rowspan="2" valign="top" align="center">{$lang->daysinstock}</td>
                <td width="" class="border_right" rowspan="2" valign="top" align="center">{$lang->qtypotentiallysold}</td>
                <td width="" class="border_right" rowspan="2" valign="top" align="center">{$lang->qtypotentiallysold}(%)</td>
                <td width="" class="border_right" rowspan="2" valign="top" align="center">{$lang->intialprice}</td>
                <td width="" class="border_right" rowspan="2" valign="top" align="center">{$lang->affbuyingprice}<a href="#" title="{$lang->affbuyingpricetooltip}"><img src="./images/icons/question.gif"/></a></td>
                <td width="" class="border_right" rowspan="2" valign="top" align="center">{$lang->totalbuyingvalue}<a href="#" title="{$lang->totalbuyingvaluetooltip}"><img src="./images/icons/question.gif"/></a></td>
                <td width="" class="border_right" rowspan="2" valign="top" align="center">{$lang->costprice}</td>
                <td width="" class="border_right" rowspan="2" valign="top" align="center">{$lang->costpriceatriskratio}</td>
                <td width="" class="border_right" rowspan="2" valign="top" align="center">{$lang->sellingprice}</td>
                <td width="" class="border_right" rowspan="2" valign="top" align="center">{$lang->sellingpriceatriskratio}</td>
                <td width="" class="border_right" rowspan="2" valign="top" align="center">{$lang->netmarginaff}</td>
                <td width="" class="border_right" rowspan="2" valign="top" align="center">{$lang->netmarginintermed}</td>
                <td width="" class="border_right" rowspan="2" valign="top" align="center">{$lang->netmarginperc}</td>
        </thead>

        <tbody id="productline_{$plrowid}_tbody" style="width:100%;">
            {$aroproductlines_rows}
        </tbody>
        <tfoot>
            <tr><td valign="top">
                    <input name="numrows_productlines{$plrowid}" type="hidden" id="numrows_productline_{$plrowid}" value="{$plrowid}">
                <!--    <input type="hidden" name="ajaxaddmoredata[affid]" id="ajaxaddmoredata_affid" value="{$budget_data[affid]}"/> -->
                    <img src="./images/add.gif" id="ajaxaddmore_aro/managearodouments_productline_{$plrowid}" alt="{$lang->add}">
                </td>
            </tr>
        </tfoot>

    </table>
</p>
</div>



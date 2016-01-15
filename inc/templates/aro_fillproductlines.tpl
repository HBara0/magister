<a class="header" href="#" id="pldiv"><h2 id="aro_productlines">{$lang->fillproductlines}</h2></a>
<div>
    <p>
    <table width="100%">
        <thead>
            <tr style="vertical-align: top;{$bold}">
                <td class="border_right" rowspan="2" valign="top" align="center">{$lang->product}</td>
                <td class="border_right" rowspan="2" valign="top" align="center">{$lang->packaging}</td>
                <td class="border_right" rowspan="2" valign="top" align="center">{$lang->quantity}</td>
                <td class="border_right" rowspan="2" valign="top" align="center">{$lang->uom}</td>
                <td class="border_right" rowspan="2" valign="top" align="center">{$lang->daysinstock}
                    <input type="hidden" id="productline_daysInStock_disabled" value="1"/></td>
                <td class="border_right" rowspan="2" valign="top" align="center">{$lang->qtypotentiallysold}
                    <input type="hidden" id="productline_qtyPotentiallySold_disabled" value="1"/></td>
                <td class="border_right" {$colspan[qtypotentiallysold]} rowspan="2" valign="top" align="center">{$lang->qtypotentiallysold}(%)</td>
                <td class="border_right" rowspan="2" valign="top" align="center">{$lang->intialprice}</td>
                <td class="border_right" rowspan="2" valign="top" align="center">{$lang->fees}</td>
                <td class="border_right" rowspan="2" valign="top" align="center">{$lang->affbuyingprice}<a class="hidden-print" href="#" title="{$lang->affbuyingpricetooltip}"><img src="./images/icons/question.gif"/></a></td>
                <td class="border_right" rowspan="2" valign="top" align="center">{$lang->totalbuyingvalue}</td>
                <td class="border_right" rowspan="2" valign="top" align="center">{$lang->costprice}<a href="#" title="{$lang->costpricetooltip}" class="hidden-print"><img src="./images/icons/question.gif"/></a></td>
                <td class="border_right" rowspan="2" valign="top" align="center">{$lang->costpriceatriskratio}</td>
                <td class="border_right" rowspan="2" valign="top" align="center">{$lang->sellingprice}</td>
                <td class="border_right" rowspan="2" valign="top" align="center">{$lang->sellingpriceatriskratio}</td>
                <td class="border_right" rowspan="2" valign="top" align="center">{$lang->netmargin}</td>
                <td class="border_right" colspan="2" rowspan="2" valign="top" align="center">{$lang->netmarginperc}</td>
            </tr>
        </thead>

        <tbody id="productline_{$plrowid}_tbody" style="width:100%;" class="{$datatable}">
            {$aroproductlines_rows}
        </tbody>
        <tfoot>
            <tr><td valign="top" {$display_addmoreproductlines}>
                    <input name="numrows_productline{$plrowid}" type="hidden" id="numrows_productline_{$plrowid}" value="{$plrowid}">
                    <img src="./images/add.gif" id="ajaxaddmore_aro/managearodouments_productline_{$plrowid}" alt="{$lang->add}"><small>{$lang->addmoreproductlines}</small>
                </td>
            </tr>
        </tfoot>

    </table>
</p>
</div>



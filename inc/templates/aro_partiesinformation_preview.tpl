<a class="header" href="#" id="pldiv"><h2 id="aro_partiesinformation">{$lang->partiesinformation}</h2></a>
<div>
    {$interm_vendor}
    <table>
        <tbody width="100%;" class="datatable">
            <tr><td class="subtitle border_right" colspan="6">{$lang->shipmentparameters}</td></tr>
                {$partiesinfo_shipmentparameters}
            <tr><td class="subtitle  border_right" colspan="6">{$lang->fees}</td></tr>
                {$partiesinfo_fees}
            <tr><td colspan="2"></td>
                <td class="subtitle border_right">{$lang->intermediary}</td>
                <td class="subtitle border_right">{$lang->vendor}</td>
            </tr>

            <tr class="altrow">
                <td colspan="2">{$lang->estdateofpayment}</td>
                <td class="border_right">{$partiesinfo[intermedEstDateOfPayment_formatted]}</td>
                <td class="border_right">{$partiesinfo[vendorEstDateOfPayment_formatted]} </td>
                <td class="border_right" colspan="2" style="padding-left:10px;font-weight:bold"> {$lang->diffbetweenpaymentdays}</td>
                <td class="border_right" id='cashcycle' style='color:red'>{$partiesinfo[diffbtwpaymentdates]} </td>
            </tr>
            <tr class="altrow" style="{$previewdisplay[promiseofpayment]}">
                <td class="border_right" colspan="2">{$lang->acceptablepaymentterm}</td>
                <td class="border_right">{$aropartiesinfo_obj->ptAcceptableMargin}</td>
                <td class="border_right"></td>
            </tr>
            <tr class="altrow" style="{$previewdisplay[promiseofpayment]}">
                <td class="border_right" colspan="2">{$lang->promiseofpayment}  <a href="#" title="{$lang->promiseofpaymentdesc}"><img src="./images/icons/question.gif" ></a></td>
                <td class="border_right">{$partiesinfo[promiseOfPayment_formatted]} </td>
                <td class="border_right"></td>
            </tr>
        </tbody>
    </table>
</div>



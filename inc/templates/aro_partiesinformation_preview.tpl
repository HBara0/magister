<a class="header" href="#" id="pldiv"><h2 id="aro_partiesinformation">{$lang->partiesinformation}</h2></a>
<div>
    {$interm_vendor}
    <table>
        <tbody width="100%;" class="datatable">
            <tr><td class="subtitle" colspan="6">{$lang->shipmentparameters}</td></tr>
                {$partiesinfo_shipmentparameters}
            <tr><td class="subtitle" colspan="6">{$lang->fees}</td></tr>
                {$partiesinfo_fees}
            <tr><td colspan="2"></td>
                <td class="subtitle">{$lang->intermediary}</td>
                <td class="subtitle">{$lang->vendor}</td>
            </tr>

            <tr class="altrow">
                <td colspan="2">{$lang->estdateofpayment}</td>
                <td>{$partiesinfo[intermedEstDateOfPayment_formatted]}</td>
                <td>{$partiesinfo[vendorEstDateOfPayment_formatted]} </td>
                <td colspan="2" style="padding-left:10px;font-weight:bold"> {$lang->diffbetweenpaymentdays}</td>
                <td>{$partiesinfo[diffbtwpaymentdates]} </td>
            </tr>
            <tr class="altrow" style="{$previewdisplay[promiseofpayment]}">
                <td colspan="2">{$lang->acceptablepaymentterm}</td>
                <td>{$aropartiesinfo_obj->ptAcceptableMargin}</td>
                <td></td>
            </tr>
            <tr class="altrow" style="{$previewdisplay[promiseofpayment]}">
                <td colspan="2">{$lang->promiseofpayment}  <a href="#" title="{$lang->promiseofpaymentdesc}"><img src="./images/icons/question.gif" ></a></td>
                <td>{$partiesinfo[promiseOfPayment_formatted]} </td>
                <td></td>

            </tr>
        </tbody>
    </table>
</div>



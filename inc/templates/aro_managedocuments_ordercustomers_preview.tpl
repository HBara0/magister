<a class="header " href="#"><h2>Order Customers</h2></a>
<div>
    <p>
    <table>
        <thead>
            <tr style="vertical-align: top;">
                <td class="border_right" rowspan="2" valign="top" align="center" style="font-weight:bold;">{$lang->customer}</td>
                <td class="border_right" rowspan="2" valign="top" align="center" style="font-weight:bold;">{$lang->paymentterms}</td>
                <td class="border_right" rowspan="2" valign="top" align="center" style="font-weight:bold;width:210px;">{$lang->paymenttermsdesc}</td>
                <td class="border_right" rowspan="2" valign="top" align="center" style="font-weight:bold;width:200px;">{$lang->paymenttermbasedate}</td>
        </thead>
        <tbody id="newcustomer_{$rowid}_tbody" style="width:100%;">
            {$aro_managedocuments_ordercustomers_rows}
        </tbody>
        <tfoot>
            <tr class="altrow2">
                {$unspecified_customer_row}

                <td>{$lang->unspecifiedcustomers}</td>
                <td>{$altpayment_term}</td>
                <td> {$customeroder[paymentTermDesc]}</td>
                <td> {$customeroder[paymenttermbasedate_output]}</td>
            </tr>
            <tr><td colspan="2">{$lang->estimatedduedate}</td> <td>{$avgeliduedate}</td></tr>
        </tfoot>
    </table>
</p>
</div>



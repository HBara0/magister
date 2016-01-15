<a class="header " href="#"><h2>Order Customers</h2></a>
<div>
    <p>
    <table class="datatable">
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
            {$unspecified_customer_row}
            <tr>
                <td class="border_right" colspan="4"><span style="font-weight:bold;">{$lang->estimatedduedate}</span>
                    <span class="border_right" style="padding-left:10px;">{$avgeliduedate}</span></td></tr>
        </tfoot>
    </table>
</p>
</div>



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
            <tr><td valign="top">
                    <input name="numrows_newcustomer" type="hidden" id="numrows_newcustomer_{$rowid}" value="{$rowid}">
                    <img src="./images/add.gif" id="ajaxaddmore_aro/managearodouments_newcustomer_{$rowid}" alt="{$lang->add}"><small>{$lang->addmorecustomers}</small>
                </td></tr>
            <tr class="altrow2">
                {$unspecified_customer_row}
            </tr>
            <tr><td colspan="2">{$lang->estimatedduedate}</td> <td><input type="hidden" name ="cpurchasetype" id="cpurchasetype"/><input type="text"   name="avgeliduedate" id="avgeliduedate" value="{$avgeliduedate}" class="automaticallyfilled-noneditable"/> </td></tr>
        </tfoot>
    </table>
</p>
</div>


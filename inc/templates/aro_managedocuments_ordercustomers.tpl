<a class="header " href="#"><h2>Order Customers</h2></a>
<div>
    <p>
    <table>
        <tbody id="newcustomer_{$rowid}_tbody" style="width:100%;">
            {$aro_managedocuments_ordercustomers_rows}
        </tbody>
        <tfoot>
            <tr><td valign="top">
                    <input name="numrows_newcustomer" type="hidden" id="numrows_newcustomer_{$rowid}" value="{$rowid}">
                    <img src="./images/add.gif" id="ajaxaddmore_aro/managearodouments_newcustomer_{$rowid}" alt="{$lang->add}">
                </td></tr>
            <tr class="altrow2">
                {$unspecified_customer_row}
            </tr>
            <tr><td colspan="3">{$lang->estimatedduedate}</td> <td><input type="hidden" name ="cpurchasetype" id="cpurchasetype"/><input type="text"   name="avgeliduedate" id="avgeliduedate"/> </td></tr>
        </tfoot>
    </table>
</p>
</div>



<a class="header " href="#"><h2>Order Customers</h2></a>
<div>
    <p>
    <table width="100%">
        <tbody id="newcustomer_{$rowid}_tbody" style="width:100%;">
            {$aro_managedocuments_ordercustomers_rows}
        </tbody>
        <tfoot>
            <tr><td valign="top">

                    <input name="numrows_newcustomer" type="hidden" id="numrows_newcustomer_{$rowid}" value="{$rowid}">
                    <img src="./images/add.gif" id="ajaxaddmore_aro/managearodouments_newcustomer_{$rowid}" alt="{$lang->add}">
                </td></tr>
            <tr class="altrow2">
                <td><input type="checkbox" value="unsepcifiedCustomer" name="customeroder[altcorder][altcid]"/></td>
                <td>Unspecified customers. </td>
                <td>Payment Terms  </td>
                <td>{$altpayment_term}</td>
                <td>Payment Terms Description </td>
                <td> <input type="text" name="customeroder[altcorder][paymentTermDesc]"/></td>

                <td>Payment Term Base Date</td>
                <td> <input type="text" id="pickDate_to_{$rowid}_altcid" autocomplete="off" tabindex="2" value="{$customeroder[paymenttermbasedate_output]}" required="required" />  </td>
                <td> <input type="hidden" name="customeroder[altcorder][paymentTermBaseDate]" id="altpickDate_to_{$rowid}_altcid" value="{$customeroder[paymenttermbasedate_formatted]}"/></td>

            </tr>
            <Tr><Td colspan="3">Estimated local invoices due date if any </Td> <Td><input type="hidden" name ="cpurchasetype" id="cpurchasetype"/><input type="text"   name="avgeliduedate" id="avgeliduedate"/> </Td></tr>
        </tfoot>
    </table>
</p>
</div>



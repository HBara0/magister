<a class="header " href="#"><h2>Order Customers content</h2></a>
<div>
    <p>
    <table width="100%">
        <thead><th>Order Customers</th></thead>

        <tbody id="newcustomer_tbody" style="width:100%;">
            <tr id="{$newcustomer_rowid}">

                <td> customer</td>
                <td><input type='text' id='customer_noexception_{$newcustomer_rowid}_autocomplete' name="customeroder[$newcustomer_rowid][customerName]"  value="{$customeroder[customerName]}" autocomplete='off' {$required}/>
                    <input type='text' size='3' id='customer_noexception_{$newcustomer_rowid}_id_output' disabled='disabled' value="{$customeroder[cid]}" style="display:none;"/>
                    <input type='hidden' value="{$customeroder[cid]}" id='customer_noexception_{$newcustomer_rowid}_id' name='customeroder[$newcustomer_rowid][cid]' /> </td>
                <td>Payment Terms  </td>
                <td>{$payment_term}</td>
                <td> <input type="text" name="customeroder[paymentTermDesc]"/></td>
                <td> </td>
                <td>Payment Term Base Date</td>
                <td> <input type="text" id="pickDate_to" autocomplete="off" tabindex="2" value="{$customeroder[paymenttermbasedate_output]}" required="required" />  </td>
                <td> <input type="hidden" name="customeroder[paymentTermBasedate]" id="altpickDate_to" value="{$customeroder[paymenttermbasedate_formatted]}"/></td>

            </tr>
        </tbody>
        <tfoot>
            <tr><td valign="top">
                    <input name="numrows_newcustomer" type="hidden" id="numrows_newcustomer" value="{$newcustomer_rowid}">

                    <img src="./images/add.gif" id="addmore_newcustomer" alt="{$lang->add}">
                </td></tr>
            <tr>
        </tfoot>
    </table>
</p>
</div>



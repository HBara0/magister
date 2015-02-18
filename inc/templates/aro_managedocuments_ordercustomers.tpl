<a class="header " href="#"><h2>Order Customers content</h2></a>
<div>
    <p>
    <table>
        <thead><th>Order Customers</th></thead>

        <tbody id="newcustomer_{$rowid}_tbody">
            {$aro_managedocuments_ordercustomers_rows}
        </tbody>
        <tfoot>
            <tr><td valign="top">

                    <input name="numrows_newcustomer" type="hidden" id="numrows_newcustomer_{$rowid}" value="{$rowid}">
                    <img src="./images/add.gif" id="ajaxaddmore_aro/managearodouments_newcustomer_{$rowid}" alt="{$lang->add}">
                </td></tr>
            <tr>
        </tfoot>
    </table>
</p>
</div>



<h4>{$warehouse->get_displayname()}</h4>
<table border=1 width="100%" class="datatable">
    <tr class="thead"><th>Item</th><th>Qty</th><th>Unit Cost</th><th>Total Cost</th></tr>
            {$itemlines}
    <tr><th>Total</th><th>{$total['value']}</th><th>{$total['qty']}</th><th>-</th></tr>
</table>
<br />

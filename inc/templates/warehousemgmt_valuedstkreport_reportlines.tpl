<h4>{$warehouse->get_displayname()}</h4>
<table border=1 width="100%">
    <tr><th>Item</th><th>Qty</th><th>Total Cost</th><th>Unit Cost</th></tr>
            {$itemlines}
    <tr><th>Total</th><th>{$total['qty']}</th><th>{$total['value']}</th><th>-</th></tr>
</table>
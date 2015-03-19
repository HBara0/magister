<tr>
<h4 class="header">{$lang->anticipatedamount}</h4>
<td>
    <div style = "display:inline-block;padding:5px;width:20%;">{$lang->neededamount}</div>
    <input type="number" name="segment[{$sequence}][tmpfid][amount]" value="{$amount_value}">
    <div style = "display:inline-block;padding:5px;width:20%;">{$lang->currency}</div>
    {$currencies_listf}
</td>
</tr>
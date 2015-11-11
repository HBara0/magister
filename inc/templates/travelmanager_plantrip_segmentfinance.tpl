<tr id="{$frowid}">
    <td style="width:50%">
        <input type="hidden" name="segment[{$sequence}][tmpfid][$frowid][inputChecksum]" value="{$finance_checksum}">
        <div style = "display:inline-block;padding:5px;width:35%;">{$lang->amountneededinadvance}</div>
        <input type="number"  name="segment[{$sequence}][tmpfid][$frowid][amount]" id="segment_{$sequence}_tmpfid_{$frowid}_amount" value="{$amount_value}">
        <div style = "display:inline-block;padding:5px;width:10%;">{$lang->currency}</div>
        {$currencies_listf}
    </td>
    <td id="segment_{$sequence}_tmpfid_{$frowid}_results" style="color:red;width:45%;"></td>
</tr>
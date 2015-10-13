<tr id="{$frowid}">
    <td>
        <input type="hidden" name="segment[{$sequence}][tmpfid][$frowid][inputChecksum]" value="{$finance_checksum}">
        <div style = "display:inline-block;padding:5px;width:20%;">{$lang->amountneededinadvance}</div>
        <input type="number"  name="segment[{$sequence}][tmpfid][$frowid][amount]" value="{$amount_value}">
        <div style = "display:inline-block;padding:5px;width:20%;">{$lang->currency}</div>
        {$currencies_listf}
    </td>
</tr>
<tr id="{$frowid}">
    <td>
        <input type="hidden" name="segment[{$sequence}][tmpfid][$frowid][inputChecksum]" value="{$finance_checksum}">
        <div style = "display:inline-block;padding:5px;width:20%;">{$lang->neededamount}</div>
        <input type="number" tabindex="8" name="segment[{$sequence}][tmpfid][$frowid][amount]" value="{$amount_value}">
        <div style = "display:inline-block;padding:5px;width:20%;">{$lang->currency}</div>
        {$currencies_listf}
    </td>
</tr>
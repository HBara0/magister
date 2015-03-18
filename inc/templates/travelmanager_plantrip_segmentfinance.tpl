<tr>
    <td>
        <div style = "display:inline-block;padding:5px;width:20%;">{$lang->neededamount}</div>
        <input type="number" name="segment[{$sequence}][tmpfid][amount]" value="">
    </td
</tr>
<tr>
    <td>
        <div style = "display:inline-block;padding:5px;width:20%;">{$lang->currency}</div>
        {$currencies_listf}
    </td>
</tr>

<tr><td>
        <div style = "display:inline-block;padding:5px;width:20%;">{$lang->paidby}</div>
        <select name="segment[{$sequence}][tmpfid][paidBy]" tabindex="5">
            {$paidby_list}</select>
    </td></tr>
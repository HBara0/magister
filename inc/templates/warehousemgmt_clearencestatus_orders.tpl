<tr class="thead">
    <td style="width:15%;">{$lang->ordernum}</td>
    <td>{$lang->supplier}</td>
    <td>{$lang->supplierlocation}</td>
    <td>{$lang->etd}</td>
    <td>{$lang->eta}</td>
    <td>{$lang->atd}</td>
    <td>{$lang->ata}</td>
    <td>{$lang->ca}</td>
    <td>{$lang->supdocrecptdate}</td>
    <td>{$lang->cadocdelvdate}</td>
    <td>{$lang->delvorderreldate}</td>
    <td>{$lang->etc}</td>
</tr>
<tr class="altrow">
    <td>{$order[documentno]}</td>
    <td>{$order[supplier]}</td>
    <td>{$order[bpaddress]}</td>
    <td>{$order[etd]}</td>
    <td>{$order[eta]}</td>
    <td>{$order[atd]}</td>
    <td>{$order[ata]}</td>
    <td>{$ca}</td>
    <td>{$order[supdocrecptdate]}</td>
    <td>{$order[cadocdelvdate]}</td>
    <td>{$order[delvorderreldate]}</td>
    <td>{$order[etc]}</td>
</tr>
<tr>
    <td colspan="7">
        <table class="datatable" style="width:100%;">
            {$orderlines_output}
        </table>
    </td>
</tr>


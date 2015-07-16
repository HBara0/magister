<tr class="thead">
    <td style="width:15%;">{$lang->ordernum}</td>
    <td>{$lang->orderdate}</td>
    <td>{$lang->custorderref}</td>
    <td>{$lang->customer}</td>
    <td>{$lang->fromwarehouse}</td>
    <td>{$lang->deliveryfrom}</td>
    <td>{$lang->deliverylocation}</td>
</tr>
<tr class="altrow">
    <td>{$order[documentno]}</td>
    <td>{$order[DateOrdered_output]}</td>
    <td>{$order[poreference]}</td>
    <td>{$order[customer]}</td>
    <td>{$order[warehouse]}</td>
    <td>{$order[deliveryfrom_output]}</td>
    <td>{$order[deliverylocation_output]}</td>
</tr>
<tr>
    <td colspan="7">
        <table class="datatable" style="width:100%;">
            {$orderlines_output}
        </table>
    </td>
</tr>


<table class="datatable">
    <thead style="color:black;" class="thead">
        <tr>
            <td>{$lang->orderdate}</td>
            <td>{$lang->documentnumber}</td>
            <td>{$lang->customer}</td>
            <td>{$lang->custorderref}</td>
            <td>{$lang->custcountry}</td>
            <td>{$lang->salesrep}</td>
            <td>{$lang->currency}</td>
            <td>{$lang->totalgrossamt}</td>
            <td>{$lang->totalnetamt}</td>
            <td>{$lang->paymentterms}</td>
            <td>{$lang->incoterms}</td>
            <td>{$lang->incotermsdesc}</td>
            <td>{$lang->updatedon}</td>
        </tr>
    </thead>
    <tbody style="color:black;">
        <tr>
            <td class="border_right">{$order[DateOrdered_output]}</td>
            <td class="border_right">{$order[documentno]}</td>
            <td class="border_right">{$order[customer]}</td>
            <td class="border_right">{$order[poreference]}</td>
            <td class="border_right">{$order[customercountry]}</td>
            <td class="border_right">{$order[salesrep_output]}</td>
            <td class="border_right">{$order[currency]}</td>
            <td class="border_right">{$order[grandtotal]}</td>
            <td class="border_right">{$order[totallines]}</td>
            <td class="border_right">{$order[paymentterm]}</td>
            <td class="border_right">{$order[incoterms]}</td>
            <td class="border_right">{$order[em_ork_incotermsdesc]}</td>
            <td>{$order[updated_output]}</td>
        </tr>
    </tbody>
</table>
<table>
    {$statusinfo_output}
</table>
<table style="color:black;" class="datatable">
    <thead>
        <tr style="width:70%;font-weight: bold" class="altrow">
            <td style="width:10%;">{$lang->product}</td>
            <td style="width:10%;">{$lang->qty}</td>
            <td style="width:10%;">{$lang->uom}</td>
            <td style="width:10%;">{$lang->packaging}</td>
            <td style="width:10%;">{$lang->price}</td>
            <td style="width:10%;">{$lang->linegrossamt}</td>
            <td style="width:10%;">{$lang->linenetamt}</td>
        </tr>
    </thead>
    <tbody>
        {$lines_output}
    </tbody>
</table>
<table style="color:black;display:{$display};">
    <tr class="subtitle" style="width:100%;">
        <td style="width:15%">
            {$lang->attachmenttitle}
        </td>
        <td style="width:10%">
            {$lang->description}
        </td>
        <td>
            {$lang->downloadlink}
        </td>
    </tr>
    <tbody>
        {$attachments_ouput}
    </tbody>
</table>
<br /><br />
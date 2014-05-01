<table class="sectionbox datatable" width="100%">
    <tr>
        <td style="font-weight: bold;">{$lang->nborder}</td><td style="font-weight:bold;">{$order[number_output]}</td>
        <td style="font-weight: bold;">{$lang->date}</td><td>{$order[date_output]}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">{$lang->type}</td><td>{$order[type_output]}</td>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">{$lang->currency}</td><td>{$order[currency_output]}</td>
        <td style="font-weight: bold;">{$lang->fxtocurrency}</td><td>{$order[fxUSD]}</td>
    </tr>
</table>
<br />
<table class="sectionbox datatable" width="100%">
    <tr><td class="thead" colspan="4">{$lang->orderoverview}</td></tr>
    <tr>
        <td style="width:20%; font-weight: bold;">{$lang->numberofitems}</td><td style="width:30%;">{$order[numberOfItems]}</td>
        <td style="width:20%; font-weight: bold;">{$lang->warehouseunitsize}</td><td style="width:30%;">{$order[warehouseUnitSize]} {$order[warehouseUnit_output]}</td>

    </tr>
    <tr>
        <td style="width:20%; font-weight: bold;">{$lang->ordervalue}</td><td>{$order[totalPurchaseValue]}</td>
        <td style="font-weight: bold;">{$lang->netmargincurrency}</td><td>{$order[totalNetMargin]}</td>
    </tr>
    <tr>
        <td style="width:20%; font-weight: bold;">{$lang->netmarginperc}</td><td>{$order[totalNetMarginPerc]}</td>
        <td style="font-weight: bold;">{$lang->netweight}</td><td>{$order[totalNetWeight]}</td>
    </tr>
    <tr>
        <td colspan="4" class="altrow2" style="font-style:italic;">{$lang->risk}: {$order[risk]}, {$lang->warehousecharges}: {$order[warehouseCharges]}, {$lang->bankinterest}: {$order[bankInterest]}</td>
    </tr>
</table>
<br />
<table class="sectionbox datatable" width="100%">
    <tr>
        <td class="altrow" style="font-weight:bold;">{$lang->customerspayment}</td><td class="altrow" style="font-weight:bold;">{$order[customersPaymentDate_output]}</td><td class="altrow" style="font-weight:bold;">{$lang->supplierpayment}</td><td class="altrow" style="font-weight:bold;">{$order[supplierPaymentDate_output]}</td>
    </tr>
</table>
{$supplier_section}
{$customers_section}
{$products_section}
<tr class="trowtools {$rowclass}">
    <td width="19%">{$policy->affid}</td>
    <td width="19%">{$country_output}</td>
    <td width="19%">{$policy->purchaseType}</td>
    <td width="15%">{$policy->effectiveFrom}</td>
    <td width="15%">{$policy->effectiveTo }</td>
    <td width="8%">{$policy->isactveicon}</td>
    <td width="5%" id="edit_{$policy->apid}_tools">
        <div style="display: none;">
            {$row_tools}
        </div>
    </td>
</tr>
<tr id='{$supp_counter}'>
    <td>
        <input type='text' id='supplier_{$supp_counter}_autocomplete' autocomplete='off' size='40px' value="{$val[name]}"/><input type='hidden' id='supplier_{$supp_counter}_id' name='supplier[{$supp_counter}][eid]' value="{$val[eid]}"/><div id='searchQuickResults_supplier_{$supp_counter}' class='searchQuickResults' style='display:none;'></div>
    </td>
    <td>
        {$affiliates_list_supplierssection}
    </td>
    <td>
        <input type='checkbox' name='supplier[{$supp_counter}][isValidator]' id='supplier_{$supp_counter}_validator'{$validator_checked[$val[eid]]}>
    </td>
</tr>
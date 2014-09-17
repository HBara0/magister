<tr id="{$supplierrownumber}">
    <td>
        <input type='text' id='supplier_{$supplierrownumber}_autocomplete' value="{$visitreport_values[suppliername][$k]}" autocomplete="off"/><input type="text" size="3" id="supplier_{$supplierrownumber}_id_output" value="{$visitreport_values[spid][$k]}" disabled/><input type='hidden' id='supplier_{$supplierrownumber}_id' name='spid[]' value="{$visitreport_values[spid][$k]}" /><div id='searchQuickResults_supplier_{$supplierrownumber}' class='searchQuickResults' style='display:none;'></div>
    </td>
</tr>
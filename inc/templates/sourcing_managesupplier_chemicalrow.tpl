<tr id='{$chemicalp_rowid}'>
    <td><input type='text' value="{$supplier[chemicalsubstances][$key][name]}" id='chemicalproducts_{$chemicalp_rowid}_autocomplete' autocomplete='off' size='40px' placeholder="select chemical products"/>
        <input type='hidden' id='chemicalproducts_{$chemicalp_rowid}_id' name='supplier[chemicalproducts][{$chemicalp_rowid}][csid]' value="{$supplier[chemicalsubstances][$key][csid]}"/>
        <div id="searchQuickResults_chemicalproducts_{$chemicalp_rowid}" class="searchQuickResults" style="display:none;"></div>
        <a href='#createchemical_{$chemicalp_rowid}_id' id='addnew_sourcing/managesupplier_chemical'><img src='images/addnew.png' border='0' alt='{$lang->add}' /></a></td>


    <td><select name="supplier[chemicalproducts][{$chemicalp_rowid}][supplyType]">
            <option value="p"{$selecteditems[supplyType][$key][p]}>{$lang->producer}</option>
            <option value="t"{$selecteditems[supplyType][$key][t]}>{$lang->trader}</option>
        </select></td>
</tr>
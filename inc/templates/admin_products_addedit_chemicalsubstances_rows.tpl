<tr id={$chemicalp_rowid}> <td colspan="2">{$lang->chemicalsubstances}</td><td> <input type="text" value="{$product[chemicalsubstances][$key][name]}" id="chemicalproducts_{$chemicalp_rowid}_QSearch" autocomplete="off" size="40px" placeholder="{$lang->selectchemical}"/>
  <input type="hidden" id="chemicalproducts_{$chemicalp_rowid}_id" name="chemsubstances[{$chemicalp_rowid}][csid]"  value="{$product[chemicalsubstances][$key][csid]}"/>
        <div id="searchQuickResults_chemicalproducts_{$chemicalp_rowid}" class="searchQuickResults" style="display:none;"></div> </td>
    <td><a class="showpopup" id="showpopup_createchemical"><img src="../images/addnew.png" border="0" alt="{$lang->add}"/></a> </td>
</tr>
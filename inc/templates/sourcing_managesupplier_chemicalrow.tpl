<tr id='{$chemicalp_rowid}'>
	<td><input type='text' value="{$supplier[chemicalproducts][name]}" id='chemicalproducts_{$chemicalp_rowid}_QSearch' autocomplete='off' size='40px'/>
		<input type='hidden' id='chemicalproducts_{$chemicalp_rowid}_id' name='supplier[chemicalproducts][{$chemicalp_rowid}][csid]' value="{$supplier[chemicalproducts][csid]}"/>
		<div id="searchQuickResults_chemicalproducts_{$chemicalp_rowid}" class="searchQuickResults" style="display:none;"></div>
		<a href='#createchemical_{$chemicalp_rowid}_id' id='addnew_sourcing/managesupplier_chemical'><img src='images/addnew.png' border='0' alt='{$lang->add}' /></a></td>
	<td><select name="supplier[chemicalproducts][{$chemicalp_rowid}][supplyType]">
			<option value="p"{$selecteditems[$chemicalp_rowid][supplyType][p]} selected>{$lang->producer}</option>
			<option value="t"{$selecteditems[$chemicalp_rowid][supplyType][t]}>{$lang->trader}</option>
		</select></td>
</tr>

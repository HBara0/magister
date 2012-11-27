<tr id='{$chemicalp_rowid}'>
	<td>
		<input type='text' value="{$chemicalproduct[name]} - {$chemicalproduct[casNum]}" id='chemicalproducts_{$chemicalp_rowid}_QSearch' autocomplete='off' size='40px'/>
		<input type='hidden' id='chemicalproducts_{$chemicalp_rowid}_id' name='supplier[chemicalproducts][csid][]' value="{$chemicalproduct[csid]}"/>
		<div id="searchQuickResults_chemicalproducts_{$chemicalp_rowid}" class="searchQuickResults" style="display:none;"></div>
	</td>
</tr>
<tr id='{$chemicalp_rowid}'>
	<td>
		<input type='text' value="{$supplier[chemicalproducts][name]}" id='chemicalproducts_{$chemicalp_rowid}_QSearch' autocomplete='off' size='40px'/>
		<input type='hidden' id='chemicalproducts_{$chemicalp_rowid}_id' name='supplier[chemicalproducts][{$chemicalp_rowid}][csid]' value="{$supplier[chemicalproducts][csid]}"/>
		<div id="searchQuickResults_chemicalproducts_{$chemicalp_rowid}" class="searchQuickResults" style="display:none;"></div>
	</td>
    <td><a href='#chemical_{$chemicalp_rowid}_id' id='addnew_sourcing/managesupplier_chemical'><img src='images/addnew.png' border='0' alt='{$lang->add}'></a>
    </a></td>
    <td>						<select name="supplier[chemicalproducts][{$chemicalp_rowid}][supplyType]" size="2">
							<option value="t"{$selecteditems[type][t]}>{$lang->trader}</option>
							<option value="p"{$selecteditems[type][p]} selected>{$lang->producer}</option>
						</select></td>
</tr>


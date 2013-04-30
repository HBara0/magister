<tr id='{$genericproduct_rowid}'>
	<td>
            {$generic_product_list} 
      </td>
	<td><select name="supplier[genericproducts][{$genericproduct_rowid}][supplyType]">
			<option value="p"{$selecteditems[supplyType][$key][p]}>{$lang->producer}</option>
			<option value="t"{$selecteditems[supplyType][$key][t]}>{$lang->trader}</option>
		</select></td>
</tr>
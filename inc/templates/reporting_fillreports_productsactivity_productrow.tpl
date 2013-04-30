<tr id="{$rowid}">
  <td style="padding: 2px; border-bottom: dashed 1px #CCCCCC;" align="left">{$paid_field}
    <input type='text' name="productactivity[$rowid][productname]" id="product_{$rowid}_QSearch" value="{$productactivity[productname]}" autocomplete='off'/>
    <input type='text' size='2' style="width:25px;" name='product_{$rowid}_id_output' id='product_{$rowid}_id_output' disabled='disabled' value="{$productactivity[pid]}"/>
    <input type='hidden' value='{$productactivity[pid]}' id='product_{$rowid}_id' name='productactivity[$rowid][pid]' />
    <a href="#" id="showpopup_addproduct" class="showpopup"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a>
    <div id='searchQuickResults_{$rowid}' class='searchQuickResults' style='display:none;'></div></td>
  <td style="border-bottom: dashed 1px #CCCCCC;" align="center" class="altrow2 border_right"><input name="productactivity[$rowid][soldQty]" type="text" id="soldQty_{$rowid}" size="10" accept="numeric" value="{$productactivity[soldQty]}" autocomplete='off'/></td>
	<td  style="border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left"><input name="productactivity[$rowid][turnOver]" type="text" id="turnOver_{$rowid}" size="10" accept="numeric" value="{$productactivity[turnOver]}" autocomplete='off'/></td>
 	<td style="border-bottom: dashed 1px #CCCCCC;" align="center">{$currencyfx_selectlist}</td>
  <td style="border-bottom: dashed 1px #CCCCCC;" align="center"><input name="productactivity[$rowid][quantity]" type="text" id="quantity_{$rowid}" size="10" accept="numeric"  value="{$productactivity[quantity]}" autocomplete='off'/></td>
  <td style="border-bottom: dashed 1px #CCCCCC;" align="center">{$saletype_selectlist}</td>
  <td style="border-bottom: dashed 1px #CCCCCC;" align="center"><input name="productactivity[$rowid][salesForecast]" type="text" id="salesForecast_{$rowid}" size="10" accept="numeric" value="{$productactivity[salesForecast]}" autocomplete='off'/></td>
  <td style="border-bottom: dashed 1px #CCCCCC;" align="center"><input name="productactivity[$rowid][quantityForecast]" type="number" id="quantityForecast_{$rowid}" size="10"  accept="numeric" value="{$productactivity[quantityForecast]}" autocomplete='off'/></td>
</tr>
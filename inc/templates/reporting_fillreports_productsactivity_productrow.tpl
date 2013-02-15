<tr id="{$rowid}">
  <td style="padding: 2px; border-bottom: dashed 1px #CCCCCC;" align="left">{$paid_field}
    <input type='text' name="product_{$rowid}_QSearch" id="product_{$rowid}_QSearch" value="{$productactivity[$i][productname]}" autocomplete='off'/>
    <input type='text' size='2' style="width:25px;" name='product_{$rowid}_id_output' id='product_{$rowid}_id_output' disabled='disabled' value="{$productactivity[$i][pid]}"/>
    <input type='hidden' value='{$productactivity[$i][pid]}' id='product_{$rowid}_id' name='pid_{$rowid}' />
    <a href="#" id="showpopup_addproduct" class="showpopup"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a>
    <div id='searchQuickResults_{$rowid}' class='searchQuickResults' style='display:none;'></div></td>
  <td  style="border-bottom: dashed 1px #CCCCCC;" align="center"><input name="turnOver_{$rowid}" type="text" id="turnOver_{$rowid}" size="10" accept="numeric" value="{$productactivity[$i][turnOver]}" autocomplete='off'/>  </td>
 <td> {$currencyfx_selectlist}</td>
  <td style="border-bottom: dashed 1px #CCCCCC;" align="center"><input name="quantity_{$rowid}" type="text" id="quantity_{$rowid}" size="10" accept="numeric"  value="{$productactivity[$i][quantity]}" autocomplete='off'/></td>
  <td style="border-bottom: dashed 1px #CCCCCC;" align="center"><input name="soldQty_{$rowid}" type="text" id="soldQty_{$rowid}" size="10" accept="numeric" value="{$productactivity[$i][soldQty]}" autocomplete='off'/></td>
  <td style="border-bottom: dashed 1px #CCCCCC;" align="center">{$saletype_selectlist}</td>
  <td style="border-bottom: dashed 1px #CCCCCC;" align="center"><input name="salesForecast_{$rowid}" type="text" id="salesForecast_{$rowid}" size="10" accept="numeric" value="{$productactivity[$i][salesForecast]}" autocomplete='off'/></td>
  <td style="border-bottom: dashed 1px #CCCCCC;" align="center"><input name="quantityForecast_{$rowid}" type="number" id="quantityForecast_{$rowid}" size="10"  accept="numeric" value="{$productactivity[$i][quantityForecast]}" autocomplete='off'/></td>
</tr>

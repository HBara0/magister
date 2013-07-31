<tr id="{$rowid}">
    <td style="width: 40%; border-right: 1px solid #E8E8E8; border-bottom: 1px dashed #E8E8E8;" valign="top" class="altrow2">{$lang->productline}<br />
    {$generics_list}
    {$lang->chemicalsubstance}<br />
    <input type='text' value="{$report_data[overallstatus][$rowid][chemsubstance]}" id='chemicalproducts_{$rowid}_QSearch' autocomplete='off' size='40px'/>
    <input type='hidden' id='chemicalproducts_{$rowid}_id' name='overallstatus[$rowid][csid]' value="{$report_data[overallstatus][$rowid][csid]}"/>
    <div id="searchQuickResults_chemicalproducts_{$rowid}" class="searchQuickResults" style="display:none;"></div>
                
    </td>
    <td valign="top" style="border-bottom: 1px dashed #E8E8E8;"><textarea rows="3" cols="50" name="overallstatus[$rowid][status]" id="status_{$rowid}">{$report_data[overallstatus][$rowid][status]}</textarea></td>
</tr>
<tr id="{$rowid}">
    <td>
        <input type="checkbox" name="forecastline[$rowid][todelete]" value="1" style="width:25px;" title="{$lang->deletecheckboxnote}"/>
    </td>
    <td>
        <input type="hidden" name="forecastline[$rowid][gpflid]" value="{$forecastline[gpflid]}"/>
        <input type="hidden" name="forecastline[$rowid][inputChecksum]" value="{$forecastline[inputChecksum]}"/>
        <input type="hidden" name="forecastline[$rowid][gpfid]" value="{$forecastline[gpfid]}"/>
        <input type='text' name="forecastline[$rowid][pid]" id="product_noexception_{$rowid}_autocomplete" value="{$forecastline[productName]}" autocomplete='off' {$required}/>
        <input type='text' size='2' style="width:35px;display:none;" name='product_{$rowid}_id_output' id='product_noexception_{$rowid}_id_output' disabled='disabled' value="{$forecastline[pid]}"/>
        <input type='hidden' value='{$forecastline[pid]}' id='product_noexception_{$rowid}_id' name='forecastline[$rowid][pid]' />
        <div id='searchQuickResults_product_{$rowid}' class='searchQuickResults' style='display:none;'></div>
        {$segments_selectlist}
    </td>
    <td>{$saletype_selectlist}</td>
    <td> <input style="width:100px;" name="forecastline[$rowid][month1]" type="number" step="any" id="forecastline_{$rowid}_month_1" value="{$forecastline[month1]}" {$readonly[month1]}/></td>
    <td> <input style="width:100px;" name="forecastline[$rowid][month2]" type="number" step="any" id="forecastline_{$rowid}_month_2" value="{$forecastline[month2]}" {$readonly[month2]}/></td>
    <td> <input style="width:100px;" name="forecastline[$rowid][month3]" type="number" step="any" id="forecastline_{$rowid}_month_3" value="{$forecastline[month3]}" {$readonly[month3]}/></td>
    <td> <input style="width:100px;" name="forecastline[$rowid][month4]" type="number" step="any" id="forecastline_{$rowid}_month_4" value="{$forecastline[month4]}" {$readonly[month4]}/></td>
    <td> <input style="width:100px;" name="forecastline[$rowid][month5]" type="number" step="any" id="forecastline_{$rowid}_month_5" value="{$forecastline[month5]}" {$readonly[month5]}/></td>
    <td> <input style="width:100px;" name="forecastline[$rowid][month6]" type="number" step="any" id="forecastline_{$rowid}_month_6" value="{$forecastline[month6]}" {$readonly[month6]}/></td>
    <td> <input style="width:100px;" name="forecastline[$rowid][month7]" type="number" step="any" id="forecastline_{$rowid}_month_7" value="{$forecastline[month7]}" {$readonly[month7]}/></td>
    <td> <input style="width:100px;" name="forecastline[$rowid][month8]" type="number" step="any" id="forecastline_{$rowid}_month_8" value="{$forecastline[month8]}" {$readonly[month8]}/></td>
    <td> <input style="width:100px;" name="forecastline[$rowid][month9]" type="number" step="any" id="forecastline_{$rowid}_month_9" value="{$forecastline[month9]}" {$readonly[month9]}/></td>
    <td> <input style="width:100px;" name="forecastline[$rowid][month10]" type="number" step="any" id="forecastline_{$rowid}_month_10" value="{$forecastline[month10]}" {$readonly[month10]}/></td>
    <td> <input style="width:100px;" name="forecastline[$rowid][month11]" type="number" step="any" id="forecastline_{$rowid}_month_11" value="{$forecastline[month11]}" {$readonly[month11]}/></td>
    <td> <input style="width:100px;" name="forecastline[$rowid][month12]" type="number" step="any" id="forecastline_{$rowid}_month_12" value="{$forecastline[month12]}" {$readonly[month12]}/></td>
    <td> <span style="width:100px;font-weight:bold" id="total_{$rowid}">{$forecastline[quantity]}</span></td>
</tr>


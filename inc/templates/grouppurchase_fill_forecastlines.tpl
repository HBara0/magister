<tr id="{$rowid}">

    <td>
        <input type="hidden" name="forecastline[{$currentyear}][$rowid][gpflid]" value="{$forecastline[gpflid1]}"/>
        <input type="hidden" name="forecastline[{$nextyear}][$rowid][gpflid]" value="{$forecastline[gpflid2]}"/>
        <input type="hidden" name="forecastline[{$currentyear}][$rowid][inputChecksum]" value="{$forecastline[inputChecksum1]}"/>
        <input type="hidden" name="forecastline[{$nextyear}][$rowid][inputChecksum]" value="{$forecastline[inputChecksum2]}"/>
        <input type="hidden" name="forecastline[{$currentyear}][$rowid][gpfid]" value="{$forecastline[gpfid1]}"/>
        <input type="hidden" name="forecastline[{$nextyear}][$rowid][gpfid]" value="{$forecastline[gpfid2]}"/>
        <input type='text' name="forecastline[$rowid][pid]" id="product_noexception_{$rowid}_autocomplete" value="{$forecastline[productName]}" autocomplete='off' {$required}/>
        <input type='text' size='2' style="width:35px;display:none;" name='product_{$rowid}_id_output' id='product_noexception_{$rowid}_id_output' disabled='disabled' value="{$forecastline[pid]}"/>
        <input type='hidden' value='{$forecastline[pid]}' id='product_noexception_{$rowid}_id' name='forecastline[{$currentyear}][$rowid][pid]' />
        <input type='hidden' value='{$forecastline[pid]}' id="forecastline_nextyear_{$rowid}_pid" name='forecastline[{$nextyear}][$rowid][pid]' />
        <div id='searchQuickResults_product_{$rowid}' class='searchQuickResults' style='display:none;'></div>
        <input type="hidden" value="{$forecastline['psid']}" name="forecastline[{$nextyear}][{$rowid}][psid]" id="forecastline_nextyear_{$rowid}_psid"/>
        {$segments_selectlist}
    </td>
    <td>{$saletype_selectlist}<input type="hidden" value="{$defaultselected}" name="forecastline[{$nextyear}][{$rowid}][saleType]" id="forecastline_nextyear_{$rowid}_saleType"/></td>
    <td> <input style="width:60px;" name="forecastline[{$year1}][$rowid][{$month1}]" type="number" step="any" id="forecastline_{$rowid}_month_1" value="{$forecastline[$month1]}" {$readonly[month1]} required="required"/></td>
    <td> <input style="width:60px;" name="forecastline[{$year2}][$rowid][{$month2}]" type="number" step="any" id="forecastline_{$rowid}_month_2" value="{$forecastline[$month2]}" {$readonly[month2]} required="required"/></td>
    <td> <input style="width:60px;" name="forecastline[{$year3}][$rowid][{$month3}]" type="number" step="any" id="forecastline_{$rowid}_month_3" value="{$forecastline[$month3]}" {$readonly[month3]} required="required"/></td>
    <td> <input style="width:60px;" name="forecastline[{$year4}][$rowid][{$month4}]" type="number" step="any" id="forecastline_{$rowid}_month_4" value="{$forecastline[$month4]}" {$readonly[month4]} required="required"/></td>
    <td> <input style="width:60px;" name="forecastline[{$year5}][$rowid][{$month5}]" type="number" step="any" id="forecastline_{$rowid}_month_5" value="{$forecastline[$month5]}" {$readonly[month5]} required="required"/></td>
    <td> <input style="width:60px;" name="forecastline[{$year6}][$rowid][{$month6}]" type="number" step="any" id="forecastline_{$rowid}_month_6" value="{$forecastline[$month6]}" {$readonly[month6]} required="required"/></td>
    <td> <input style="width:60px;" name="forecastline[{$year7}][$rowid][{$month7}]" type="number" step="any" id="forecastline_{$rowid}_month_7" value="{$forecastline[$month7]}" {$readonly[month7]} required="required"/></td>
    <td> <input style="width:60px;" name="forecastline[{$year8}][$rowid][{$month8}]" type="number" step="any" id="forecastline_{$rowid}_month_8" value="{$forecastline[$month8]}" {$readonly[month8]} required="required"/></td>
    <td> <input style="width:60px;" name="forecastline[{$year9}][$rowid][{$month9}]" type="number" step="any" id="forecastline_{$rowid}_month_9" value="{$forecastline[$month9]}" {$readonly[month9]} required="required"/></td>
    <td> <input style="width:60px;" name="forecastline[{$year10}][$rowid][{$month10}]" type="number" step="any" id="forecastline_{$rowid}_month_10" value="{$forecastline[$month10]}" {$readonly[month10]} required="required"/></td>
    <td> <input style="width:60px;" name="forecastline[{$year11}][$rowid][{$month11}]" type="number" step="any" id="forecastline_{$rowid}_month_11" value="{$forecastline[$month11]}" {$readonly[month11]} required="required"/></td>
    <td> <input style="width:60px;" name="forecastline[{$year12}][$rowid][{$month12}]" type="number" step="any" id="forecastline_{$rowid}_month_12" value="{$forecastline[$month12]}" {$readonly[month12]} required="required"/></td>
    <td> <span style="width:100px;font-weight:bold" id="total_{$rowid}">{$forecastline[quantity]}</span></td>

</tr>


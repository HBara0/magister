<tr id="{$rowid}">
    <td><input type='text' value="{$budgetrainingvisit[event]}" autocomplete='off'   name="budgetrainingvisit[local][$rowid][event]"/></td>
    <td><input type='hidden' id='supplier_noexception_{$rowid}_id' value="{$budgetrainingvisit[company]}" name='budgetrainingvisit[local][$rowid][company]' autocomplete='off' />
        <input type='text' value="{$budgetrainingvisit[company]}" name='budgetrainingvisit[local][$rowid][company]'  name="" value="{$budgetrainingvisit[companyoutput]}" id='supplier_noexception_{$rowid}_autocomplete'/>
        <input type="hidden" name="budgetrainingvisit[local][$rowid][classification] " value="local"/>
        <input type="hidden" name="budgetrainingvisit[local][$rowid][btvid] " value="{$budgetvisit->btvid}"/>
        <input type="hidden" name="budgetrainingvisit[local][$rowid][inputChecksum] " value="{$budgetrainingvisit[inputChecksum]}"/>
        <input type="hidden" name="budgetrainingvisit[local][$rowid][lid] " value="{$budgetrainingvisit[$populated_leave->lid][$type][lid]}{$budgetrainingvisit[lid]}"/>
    </td>

    <td><input type="text"  id="pickDate_local{$rowid}" autocomplete="off" tabindex="1" value="{$budgetrainingvisit[date_output]} {$budgetrainingvisit[$populated_leave->lid][$type][date_output]} " required="required"/>
        <input type="hidden" name="budgetrainingvisit[local][$rowid][date]" id="altpickDate_local{$rowid}" value="{$budgetrainingvisit[Date_formatted]}{$budgetrainingvisit[$populated_leave->lid][$type][Date_formatted]}" />
    </td>
    <td><textarea  name="budgetrainingvisit[local][$rowid][purpose]"  cols="40" required="required">{$budgetrainingvisit[purpose]}{$budgetrainingvisit[$populated_leave->lid][$type][purpose]}</textarea></td>
    <td><input type='number' id="costaff_{$rowid}_local" step="only"  name="budgetrainingvisit[local][$rowid][costAffiliate]" value="{$budgetrainingvisit[costAffiliate]}{$budgetrainingvisit[$populated_leave->lid][$type][costAffiliate]}" autocomplete='off' {$required}/></td>
</tr>
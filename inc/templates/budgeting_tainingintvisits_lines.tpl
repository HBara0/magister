<tr id="{$rowid}" >
    <td><input type='text'id='event{$rowid}' name="budgetrainingvisit[international][$rowid][event]" value="{$budgetrainingvisit[event]}" autocomplete='off' {$required}/>
        <input type="hidden" name="budgetrainingvisit[international][$rowid][inputChecksum] " value="{$budgetrainingvisit[inputChecksum]}"/>
        <input type="hidden" name="budgetrainingvisit[international][$rowid][classification] " value="International"/>
        <input type="hidden" name="budgetrainingvisit[international][$rowid][btvid] " value="{$budgetvisit->btvid}"/>
    </td>
    <td> <select name="budgetrainingvisit[international][$rowid][bm]"> {$business_managers_list}</select></td>
    <td><input type="text"  id="pickDate_intvisit{$rowid}" autocomplete="off" tabindex="1" value="{$budgetrainingvisit[date_output]} {$budgetrainingvisit[$populated_leave->lid][$type][date_output]}" required="required"/>
        <input type="hidden" name="budgetrainingvisit[international][$rowid][date]" id="altpickDate_intvisit{$rowid}" value="{$budgetrainingvisit[Date_formatted]} {$budgetrainingvisit[$populated_leave->lid][$type][Date_formatted]}" />
    </td>
    <td>
        <textarea  name="budgetrainingvisit[international][$rowid][purpose]"  cols="40" required="required">{$budgetrainingvisit[purpose]}{$budgetrainingvisit[$populated_leave->lid][$type][purpose]}</textarea>
    <td><input type='number'  step="only" id="cost_plancost_{$rowid}_int"   name="budgetrainingvisit[international][$rowid][planCost]" value="{$budgetrainingvisit[planCost]}{$budgetrainingvisit[$populated_leave->lid][$type][planCost]}" autocomplete='off'  required="required"/></td>
    <td><input type='number'  step="only"  id="cost_othercost_{$rowid}_int"  name="budgetrainingvisit[international][$rowid][otherCosts]" value="{$budgetrainingvisit[otherCosts]}{$budgetrainingvisit[$populated_leave->lid][$type][otherCosts]}" autocomplete='off' required="required" /></td>

    <td>  <span id="subtotal_{$rowid}_int" style=" float: right;font-weight:bold;">{$totalintamount}{$budgetrainingvisit[$populated_leave->lid][$type][totalexpenses]} </span>  </td>

</tr>

<tr>
    <td><input type='text' id='representative_{$arowid}{$reprowid}_autocomplete' value="{$assignee_data[repname]}" autocomplete='off' style="width:70%" {$disabled}/>
        <input type='hidden' id='representative_{$arowid}{$reprowid}_id' name='mof[actions][{$arowid}][representatives][{$reprowid}][repid]' value="{$assignee_data[repid]}" />
        <div id='searchQuickResults_representative_{$arowid}{$reprowid}' class='searchQuickResults' style='display:none;'></div>
        <input type="hidden" value="[$arowid}{$reprowid}"/>

    </td>
</tr>
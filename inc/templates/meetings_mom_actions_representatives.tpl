<tr>
    <td><input type='text' id='supprepresentative_{$arowid}{$reprowid}_autocomplete' value="{$assignee_data[repname]}" autocomplete='off' style="width:70%" {$disabled}/>
        <input type='hidden' id='supprepresentative_{$arowid}{$reprowid}_id' name='mof[actions][{$arowid}][representatives][{$reprowid}][repid]' value="{$assignee_data[repid]}" />
        <input type="hidden" id="supprepresentative_{$arowid}{$reprowid}_spid" name="spid" value='{$spid}'/>
        <div id='searchQuickResults_supprepresentative_{$arowid}{$reprowid}' class='searchQuickResults' style='display:none;'></div>
        <input type="hidden" value="{$checksum[representatives]}" name="mof[actions][{$arowid}][representatives][{$reprowid}][inputChecksum]"/>
        <input type="hidden" value="[$arowid}{$reprowid}"/>

    </td>
</tr>
<tr id="{$rowid}">
    <Td>
        <div style="display: block; width: 100%;">
            <div  style="display: inline-block; ; width:30%;"> <label>sequence</label><input type="number"  min="1" max="12" name="chainpolicy[approverchain][{$rowid}][sequence]"/></div>
            <div  style="display: inline-block; ; width:60%;">
                <label>select approver</label>
                <div  style="display:block; ">
                    {$list}
                </div>
            </div>
            <div id="user_{$rowid}_approveruser" style="float:right;display:none;">
                <label>select employee</label>
                <input type='text' id='user_{$rowid}_autocomplete' value="{$chainpolicy[contactPersonName]}"/>
                <input type='hidden' size="3" disabled="disabled" id='user_{$rowid}_id_output' name='contactPerson' value="{$chainpolicy[contactPerson]}" />
                <input type='hidden' id='user_{$rowid}_id' name='chainpolicy[approverchain][{$rowid}][uid]' value="{$chainpolicy[contactPerson]}" />
            </div>
        </div>


    </td>
</tr>
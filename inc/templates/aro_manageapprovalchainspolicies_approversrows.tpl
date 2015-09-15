<tr id="{$rowid}">
    <Td>
        <div style="display: block; width: 100%; height:150px; overflow:auto;">
            <div  style="display: inline-block; ; width:20%; vertical-align:top;"> <label>sequence</label><input type="number"  min="1" max="12" value="{$approverdata[sequence]}" name="chainpolicy[approverchain][{$rowid}][sequence]"/></div>
            <div  style="display: inline-block; ; width:65%;">
                <label> <h4>select approver</h4></label>
                <div  style="display:block; ">
                    {$list}
                </div>
            </div>
            <div id="user_{$rowid}_approveruser" style=" margin-left:240px; {$display[$rowid][uid]}">
                <label>{$lang->selectemployee}</label>
                <input type='text' id='user_{$rowid}_autocomplete' value="{$chainpolicy[username]}"/>
                <input type='hidden' size="3" disabled="disabled" id='user_{$rowid}_id_output' name='contactPerson' value="{$user->uid}" />
                <input type='hidden' id='user_{$rowid}_id' name='chainpolicy[approverchain][{$rowid}][uid]' value="{$user->uid}" />
            </div>
        </div>
    </td>
</tr>
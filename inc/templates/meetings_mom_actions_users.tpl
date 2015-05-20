<tr id="{$userrowid}" class="{$altrow}">
    <td>{$lang->employee}</td>
    <td>
        <input type='text'id='user_{$userrowid}_autocomplete' value="" autocomplete='off'/>
        <input type='hidden' id='user_{$userrowid}_id' name='mof[actions][{$arowid}][users][{$userrowid}][uid]' value="" />
        <div id='searchQuickResults_user_{$userrowid}' class='searchQuickResults' style='display:none;'></div>
    </td>
</tr>
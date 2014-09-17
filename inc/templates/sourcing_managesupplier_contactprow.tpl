<tr id='{$contactp_rowid}' style="vertical-align:top;">
    <td>
        <input type='text' id='representative_{$contactp_rowid}_autocomplete' autocomplete='off' size='40px' value="{$contactperson[name]}"/>
        <input type='hidden' id='representative_{$contactp_rowid}_id' name='supplier[representative][{$contactp_rowid}][id]' value="{$contactperson[rpid]}"/></td>
    <td>
        <a href='#representative_{$contactp_rowid}_id' id='addnew_sourcing/managesupplier_representative'><img src='images/addnew.png' border='0' alt='{$lang->add}' /></a>
        <div id='searchQuickResults_{$contactp_rowid}' class='searchQuickResults' style='display:none;'></div>
    </td>
    <td>{$lang->repnotes}</td><td><textarea name="supplier[representative][{$contactp_rowid}][notes]" title="{$lang->contactdesc}"cols="40" rows="3">{$contactperson[notes]}</textarea></td>
</tr>
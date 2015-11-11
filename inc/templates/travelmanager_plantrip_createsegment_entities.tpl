<tr id="{$entrowid}">
    <td {$display_external} data-purposes="external_{$sequence}">{$lang->businesspartners}*</td>
    <td {$display_external} data-purposes="external_{$sequence}"><input type="text"  id="allentities_{$afent_checksum}_cache_autocomplete" autocomplete="off" tabindex="1" value="{$entityname}"/>
        <input type='hidden' id='allentities_{$afent_checksum}_cache_id'  name="segment[{$sequence}][assign][eid][{$afent_checksum}]" value="{$entityid}"/>
        <input type='hidden' id='allentities_{$afent_checksum}_cache_id_output' name="segment[{$sequence}][assign][eid][{$afent_checksum}]" value="{$entityid}" disabled/>
    </td>
    <td><a href="index.php?module=contents/addentities&amp;type=supplier&amp;referrer=budgeting" target="_blank"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a>
    </td>
</tr>
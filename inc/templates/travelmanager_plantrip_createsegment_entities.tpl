<tr id="{$entrowid}">
    <td {$display_external} data-purposes="external_{$sequence}">{$lang->businesspartners}*</td>
    <td {$display_external} data-purposes="external_{$sequence}"><input type="text"  id="allentities_{$afent_checksum}_cache_autocomplete" autocomplete="off" tabindex="1" value="{$entityname}" required="required"/>
        <input type='hidden' id='allentities_{$afent_checksum}_cache_id'  name="segment[{$sequence}][assign][eid][{$afent_checksum}]" value="{$entityid}"/>
        <input type='hidden' id='allentities_{$afent_checksum}_cache_id_output' name="segment[{$sequence}][assign][eid][{$afent_checksum}]" value="{$entityid}" disabled/>
    </td>
</tr>
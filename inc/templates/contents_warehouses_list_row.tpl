<tr  class="trowtools">
    <td width="15%">{$affiliate->get_displayname()}</td>
    <td width="15%">{$warehouse->name}</td>
    <td width="15%">{$city->get_displayname()}</td>
    <td width="15%">{$country->get_displayname()}</td>
    <td width="15">{$warehouse->isactveicon}</td>
   <!-- <td width="15%">{$edit_link}&nbsp;{$delete_link}</td>-->

    <td id="edit_{$warehouse->wid}_tools" width="15%">
        <div style="display: none;">
            {$edit_link}
            {$delete_link}
        </div>
    </td>
</tr>
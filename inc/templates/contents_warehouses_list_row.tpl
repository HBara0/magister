<tr  class="trowtools">
    <td width="22.5%">{$affiliate->get_displayname()}</td>
    <td width="22.5%">{$warehouse->name}</td>
    <td width="22.5%">{$city->get_displayname()}</td>
    <td width="22.5%">{$country->get_displayname()}</td>
    <td width="7%">{$warehouse->isactveicon}</td>
   <!-- <td width="15%">{$edit_link}&nbsp;{$delete_link}</td>-->

    <td id="edit_{$warehouse->wid}_tools" width="3%">
        <div style="display: none;">
            {$edit_link}
            {$delete_link}
        </div>
    </td>
</tr>
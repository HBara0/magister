<tr  class="trowtools">
    <td width="22%">{$affiliate->get_displayname()}</td>
    <td width="22%">{$warehouse->name}</td>
    <td width="22%">{$city->get_displayname()}</td>
    <td width="22%">{$country->get_displayname()}</td>
    <td width="7%">{$warehouse->isactveicon}</td>
    <td id="edit_{$warehouse->wid}_tools" width="5%">
        <div style="display: none;">
            {$edit_link}
            {$delete_link}
        </div>
    </td>
</tr>
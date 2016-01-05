<Tr class="trowtools {$rowclass}">
    <td>{$affobj->get_displayname()}</td>
    <td>{$approvers->effectiveFrom}</td>
    <td>{$approvers->effectiveTo}</td>
    <td>{$purchasetype_obj->get_displayname()}</td>
    <td id="edit_{$approvers->aapcid}_tools">
        <div style="display: none;">
            {$row_tools}
        </div>
    </td>
</tr>
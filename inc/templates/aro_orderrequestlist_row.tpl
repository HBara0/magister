<tr {$style} class="{$rowclass}">
    <td width="15%">{$icons[pending]} {$icons[rejected]}{$documentrequest->affid}</td>
    <td width="15%">{$documentrequest->orderType}</td>
    <td width="15%">{$documentrequest->orderReference}</td>
    <td width="15%">{$documentrequest->currency}</td>
    <td width="18%">{$documentrequest->businessmanager_output}</td>
    <td width="12%">{$documentrequest->createdOn}</td>
    <td width="5%" id="edit_{$documentrequest->aorid}_tools">
        <div style="display: none;">
            {$row_tools}
        </div>
    </td>
</tr>
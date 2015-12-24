<tr {$style} class="{$rowclass}">
    <td width="18%">{$icons[rejected]}{$documentrequest->affid}</td>
    <td width="18%">{$documentrequest->orderType}</td>
    <td width="18%">{$documentrequest->orderReference}</td>
    <td width="18%">{$documentrequest->currency}</td>
    <td width="18%">{$documentrequest->createdOn}</td>
    <td width="5%" id="edit_{$documentrequest->apid}_tools">
        <div style="display: none;">
            {$row_tools}
        </div>
    </td>
</tr>
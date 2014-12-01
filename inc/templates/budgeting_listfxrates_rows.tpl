<tr>
    <td>{$affiliate->get_displayname()}</td>
    <td>{$fxrate->year}</td>
    <td>{$fromcurrency->name}<span> (<small><strong>{$fromcurrency->alphaCode}</strong></small>)</span></td>
    <td>{$tocurrency->name}<span> (<small><strong>{$tocurrency->alphaCode}</strong></small>)</span</td>
    <td>{$fxrate->rate}</td>
    <td>{$fxrate->rateCategory}</td>
    <td id="delete_{$fxrate->bfxid}_tools">
        <div style="display:block;">
            {$row_tools}
        </div>
    </td>
</tr>
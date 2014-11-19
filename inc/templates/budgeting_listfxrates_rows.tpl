<Tr>
    <td>{$aff_obj->get_displayname()}</td>
    <td>{$fxrate->year}</td>
    <td>{$fromcur_obj->get()[name]}<span> <small><strong>{$fromcur_obj->get()[alphaCode]}</strong></small></span></td>
    <td>{$tocur_obj->get()[name]}<span>  <small><strong>{$tocur_obj->get()[alphaCode]}</strong></small></span</td>
    <td>{$fxrate->rate}</td>
    <td id="delete_{$fxrate->bfxid}_tools">
        <div style="display:block;">
            {$row_tools}
        </div>
    </td>

</tr>
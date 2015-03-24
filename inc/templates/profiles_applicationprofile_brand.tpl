
<tr>
    <td colspan="3">
        <h3>{$lang->brands}</h3>
        <table width="100%" cellpadding="0" cellspacing="0">
            <tbody>
                <tr id="1">
                    <td colspan="3">
                        <div style="width:100%; max-height:200px; overflow:auto; display:inline-block; vertical-align:top;">
                            <table class="datatable" width="100%">
                                <thead>
                                    <tr class="altrow2">
                                        <th><input class='inlinefilterfield' type='text' style="width: 95%" placeholder="{$lang->brands}"/></th>
                                        <th><input class='inlinefilterfield' type='text' style="width: 95%" placeholder="{$lang->cusomter}"/></th>
                                        <th><input class='inlinefilterfield' type='text' style="width: 95%" placeholder="{$lang->type}"/></th>
                                        <th><input class='inlinefilterfield' type='text' style="width: 95%" placeholder="{$lang->country}"/></th>
                                    </tr>
                                </thead>
                                {$brandlist}
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot><tr><td colspan="4"> <small>({$itemscount[brands]} {$lang->items})</small></td></tr></tfoot>
        </table>
    </td>
</tr>
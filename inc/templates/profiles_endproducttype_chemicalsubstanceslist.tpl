<tr>
    <td colspan="2">
        <h3>{$lang->chemicalsubstances}</h3>
        <table width="100%" cellpadding="0" cellspacing="0">
            <tbody>
                <tr id="1">
                    <td colspan="2">
                        <div style="width:100%; height:200px; overflow:auto; display:inline-block; vertical-align:top;">
                            <table class="datatable" width="100%">
                                <thead>
                                    <tr class="altrow2">
                                        <th><input class='inlinefilterfield' type='text' style="width: 95%" placeholder="{$lang->chemicalsubstances}"/></th>
                                    </tr>
                                </thead>
                                {$chemicalsubstances_rows}
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot><tr><td colspan="4"> <small>({$itemscount['chemicalsubstances']} {$lang->items})</small></td></tr></tfoot>

        </table>
    </td>
</tr>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-left: 8px;">
    <tbody id="expenses{$rowid}_tbody">
        <tr id="{$rowid}"><td>
                <div style="display:inline-block;">{$lang->exptype}</div>

                <div style="display:inline-block;"><select id="segment_expensestype_{$sequence}_{$rowid}" name='segment[{$sequence}][expenses][{$rowid}][tmetid]'   {$onchange_actions}>{$expenses_options}</select></div>
                <div style="display:block;padding:5px">

                    <div style="display:none;" id="Other_{$sequence}_{$rowid}">
                        <div style="display:inline-block;">{$lang->other} <input name="segment[{$sequence}][expenses][{$rowid}][description]" type="text" value="{$expensestype[otherdesc]}"> </div>
                    </div>
                </div>
                <div style="display: inline-block; width:60%;">{$expenses_details}</div>
                <div style="display:none; padding: 8px;" id="anotheraff_{$sequence}_{$rowid}">
                    <span>Another Affiliate </span>
                    <input id="affiliate_{$sequence}_{$rowid}_cache_autocomplete" autocomplete="off" tabindex="8" value=""  type="text">
                    <input id="affiliate_{$sequence}_{$rowid}_cache_id" name="segment[{$sequence}][expenses][{$rowid}][paidBy]"  type="hidden">
                </div>
            </td></tr> </tbody>
</table>

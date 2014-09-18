<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-left: 8px;">
    <tbody id="expenses{$rowid}_tbody">
        <tr id="{$rowid}"><td>
                <div style="display:inline-block;">{$lang->exptype}</div>

                <div style="display:inline-block;"><select name='segment[{$sequence}][expenses][{$rowid}][tmetid]'>{$expenses_options}</select></div>
                <div style="display:block;padding:5px">

                    <div style="display:inline-block;"  class=" subtitle">{$lang->other} <input type="checkbox" id="show_otherexpenses_{$sequence}_{$rowid}" value="1" name="segment[{$sequence}][expenses][{$rowid}][other]"/></div>

                    <div style="display:none;" id="otherexpenses_{$sequence}_{$rowid}">
                        <div style="display:inline-block;">{$lang->what}   <input name="segment[{$sequence}][expenses][{$rowid}][otherdesc]" type="text" value="{$expensestype[otherdesc]}"> </div>
                        <div style="display:inline-block;">{$lang->howmuch} <input name="segment[{$sequence}][expenses][{$rowid}][otheramt]" type="text"   accept="numeric"  value="{$expensestype[otheramt]}"></div>
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

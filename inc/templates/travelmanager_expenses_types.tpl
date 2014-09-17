<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-left: 8px;">
    <tbody id="expenses{$rowid}_tbody">
        <tr id="{$rowid}"><td>
                <input name="sequence" type="hidden" id="sequence" value="{$sequence}">
                <div style="display:inline-block;">{$lang->exptype}</div>

                <div style="display:inline-block;"><select name='segment[{$sequence}][{$rowid}][tmetid]'>{$expenses_options}</select></div>
                <div style="display:block;padding:5px">

                    <div style="display:inline-block;"  class=" subtitle">{$lang->other} <input type="checkbox" id="show_otherexpenses" value="1" name="segment[{$sequence}][{$rowid}][other]"/></div>

                    <div style="display:none;" id="otherexpenses">
                        <div style="display:inline-block;">{$lang->what}   <input name="segment[{$sequence}][{$rowid}][otherdesc]" type="text" value="{$expensestype[otherdesc]}"> </div>
                        <div style="display:inline-block;">{$lang->howmuch} <input name="segment[{$sequence}][{$rowid}][otheramt]" type="text"   accept="numeric"  value="{$expensestype[otheramt]}"></div>
                    </div>
                </div>
                <div style="display: inline-block; width:60%;">{$expenses_details}</div>
            </td></tr> </tbody>
</table>

<tr class="{$altrow}"  style="border:1px gainsboro solid;" id="{$sequence}_{$rowid}"><td>
        <div style="display:inline-block;padding:5px;width:20%;">{$lang->exptype} </div>
        <div style="display:inline-block;width:70%;"><select id="segment_expensestype_{$sequence}_{$rowid}" tabindex="6" data-reqparent="children-expenses_{$sequence}_{$rowid}_expamount-currency_{$sequence}_{$rowid}_list" name='segment[{$sequence}][expenses][{$rowid}][tmetid]'   {$onchange_actions}>{$expenses_options}</select></div>
        <div style="display:block;padding:5px">
            <input type="hidden" value="{$expensestype[$segid][$rowid]['tmeid']}" name="segment[{$sequence}][expenses][{$rowid}][tmeid]"/>
            <div style="{$display_exp}" id="Other_{$sequence}_{$rowid}">

                <div>
                    <div style="display:inline-block;padding:5px;width:20%;">{$lang->other}</div>
                    <div style="display:inline-block;width:70%;">
                        <input  tabindex="6"  name="segment[{$sequence}][expenses][{$rowid}][description]" type="text" value="{$expensestype[$segid][$rowid][otherdesc]}">
                    </div>

                    <div> <div style="display:inline-block;padding:5px;width:20%;">{$lang->comments}</div>
                        <div style="display:inline-block;width:70%;"><textarea name="segment[{$sequence}][expenses][{$rowid}][comments]" cols="30" rows="5" >{$expensestype[$segid][$rowid][comments]}</textarea>
                        </div>
                    </div>

                </div>
            </div>
            <div>
                {$expenses_details}
            </div>
            <div style="{$expensestype[$sequence][$rowid][display]}  padding: 5px;" id="anotheraff_{$sequence}_{$rowid}" class="border_bottom border_left border_right border_top" >
                <div style="display:inline-block;width:20%;">Another Affiliate </div>
                <div style="display:inline-block;width:70%;"><input id="affiliate_{$sequence}_{$rowid}_cache_expenses_autocomplete" autocomplete="off" tabindex="8" value="{$expensestype[$segid][$rowid][affiliate]}"  type="text"></div>
                <input id="affiliate_{$sequence}_{$rowid}_cache_expenses_id" name="segment[{$sequence}][expenses][{$rowid}][paidById]" value="{$expensestype[$segid][$rowid][affid]}"type="hidden">
            </div>
    </td></tr>




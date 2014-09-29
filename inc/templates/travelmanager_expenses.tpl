<div id="expensescontainer" style="display:block;vertical-align: top; padding:5px;" class="border_bottom border_left border_right border_top">
    <div style="display:inline-block; vertical-align: top;"> {$lang->expectedamt} </div>

    <div style="display:inline-block; text-align:left;   vertical-align: top;">
        <input tabindex="" accept="numeric"   value="{$expensestype[$segid][$rowid][expectedAmt]}" id="expenses_expectedAmt"  size="20" name="segment[{$sequence}][expenses][{$rowid}][expectedAmt]" type="text"{$expenses_output_requiredattr}/>
    </div>
    <div style="display:inline-block; text-align:left; padding:5px;vertical-align: top;">
        <span style="display:inline-block;">{$lang->currecy}</span>
        <select name="segment[{$sequence}][expenses][{$rowid}][currency]"><option value="USD">USD</option></select>{$expenses_output_required}

    </div>

</div>
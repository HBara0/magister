<div id="expensescontainer" style="display:block;vertical-align: top; padding:5px;" class="border_bottom border_left border_right border_top">
    <div style="display:inline-block; vertical-align: top;width:20%;"> {$lang->expectedamt} </div>

    <div style="display:inline-block; text-align:left;   vertical-align: top;width:70%;" id="expenses_amtcurr_{$sequence}_{$rowid}">
        <input tabindex="6" accept="numeric"   value="{$expensestype[$segid][$rowid][expectedAmt]}" id="expenses_{$sequence}_{$rowid}_expamount" size="20" name="segment[{$sequence}][expenses][{$rowid}][expectedAmt]" type="text"{$expenses_output_requiredattr}/>
        {$currencies_list}

    </div>
    <div id="fandb_warning_{$sequence}_{$rowid}"></div>
    <div style="display:inline-block; text-align:left; padding:5px;vertical-align: top;">


    </div>

</div>
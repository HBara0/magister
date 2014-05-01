<div id="attendancecontainer_{$expensestype[alteid]}" style="display:inline-block; width:45%; vertical-align: top; padding:5px;" class="border_bottom border_left">
    <div style="display:inline-block;width:30%; vertical-align: top;">{$expensestype[title]}</div>
    <div style="display:inline-block; text-align:left; width:40%; vertical-align: top;"><input tabindex="" accept="numeric" size="7" value="{$expensestype[expectedAmt]}" id="expenses_{$expensestype[name]}[{$expensestype[alteid]}]" name="leaveexpenses[{$expensestype[alteid]}][expectedAmt]" type="text"{$expenses_output_requiredattr}/>
        <select name="leaveexpenses[{$expensestype[alteid]}][currency]"><option value="USD">USD</option></select>{$expenses_output_required}</div>
        {$expenses_output_comments_field} 
</div>

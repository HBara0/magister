<div id="attendancecontainer_{$expensestype[alteid]}" style="display:inline-block; width:45%;">
    <div style="display:inline-block;width:25%;">{$expensestype[title]}</div>

    <div style="display:inline-block; padding:5px; text-align:left; width:30%;"> <input tabindex="" accept="numeric" size="7" value="{$leaveexpences[$leaveexpences[alteid]][expectedAmt]}" id="expenses_{$expensestype[title]}[{$expensestype[alteid]}]" name="leaveexpenses[{$expensestype[alteid]}][expectedAmt]"  type="text" {$expenses_output_requiredattr}/> <select name="leaveexpenses[{$expensestype[alteid]}][currency]"><option value="USD">USD</option></select>{$expenses_output_required}</div>

</div>

<div style="horizontal-align: middle; font-weight: bold;border-bottom: 1px dashed #666;font-size: 14px;padding:5px; background-color: #92D050 ; ">Summary of Expenses</div>
{$expenses_details}
<div><div style="display:inline-block;padding:5px;width:85%;">Hotel Accommodation</div><div style="width:10%; display:inline-block;padding:5px;text-align:right;">$ {$expenses[accomodation]}</div>
</div>
<div>
    <div style="display:inline-block;padding:5px;width:18%;"><b>Additional Expenses</b></div>
    <div> {$additional_expenses_details}</div>
    <div style="display:inline-block;padding:5px;width:85%;">Additional Expenses total</div><div style="width:10%; display:inline-block;padding:5px;text-align:right;">$ {$expenses[additional]}</div>
</div>

<div>
    <div style="width:85%;font-weight:bold;display:inline-block;padding:5px;">Total</div><div style="display:inline-block;width:10%;font-weight:bold;padding:5px;text-align:right;">$ {$expenses_total}</div>
</div>

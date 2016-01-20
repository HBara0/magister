<div style="horizontal-align: middle; font-weight: bold;border-bottom: 1px dashed #666;font-size: 14px;padding:5px; background-color: #92D050 ; ">{$lang->summaryofexpenses}</div>
<div style="display: block;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: greenyellow">{$expenses_details}</div>
<div style="display:block;padding:5px 0px 5px 0px;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: greenyellow"><div style="display:inline-block;width:85%;">{$lang->hotelaccommodation}</div><div style="width:10%; display:inline-block;text-align:right;">$ {$expenses[accomodation]}</div>
</div>
<!--<div style="display:block;padding:5px 0px 5px 0px;border-bottom: 1px;border-bottom-style: solid;border-bottom-color: greenyellow">
    <div style="display:inline-block;width:85%;">{$lang->subtotal}</div><div style="width:10%; display:inline-block;text-align:right;font-weight:bold;"> {$expenses_subtotal}</div>
</div>-->
<div> {$additional_expenses_details}</div>

<div style="display:block;padding:5px 0px 5px 0px;border-top: 1px;border-top-style: solid;border-top-color: greenyellow;background-color: #92D050">
    <div style="width:85%;font-weight:bold;display:inline-block;">Total</div><div style="display:inline-block;width:10%;font-weight:bold;text-align:right;"> {$expenses_total}</div>
</div>
{$amount_payedinadv}

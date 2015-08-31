<h1>Valued Stock Report<br /><small>As of {$date_info[year]}-{$date_info[mon]}-{$date_info[mday]}</small></h1>
<h2>{$affiliateobj->get_displayname()}</h2>
<h2>Affiliate Currency :{$currencyname}</h2>
{$reportlines}
<h2>Grand Total:<br />
    Qty: {$grandtotal['qty']}<br />
    Value: {$grandtotal['value']}
</h2>
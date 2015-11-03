<h1>QR Reporting Inconsistency</h1><br>
Inconsistency Submitted By :{$user->get_displayname()}<br>
Report Details: {$affiliate->get_displayname()}   {$supplier->get_displayname()}/Q{$quarter}{$year}<br><br>
Comment:<div>{$comment}</div><br>
<div style="width:100%">
    <table>
        <thead>
        <th style="width:35%">Product</th>
        <th style="width:8%">Sold Quantity</th>
        <th style="width:8%">Turnover</th>
        <th style="width:10%">Currency</th>
        <th style="width:15%">Sale Type</th>
        <th style="width:8%">Forecast Purchase Amount</th>
        <th style="width:8%">Forecast Purchase Qty</th>
        </thead>
        <tbody><tr style="text-align: center;vertical-align: middle">
                <td style="width:35%;">{$productactivity_obj->get_product()->get_displayname()}</td>
                <td style="width:8%">{$productactivity_obj->soldQty}</td>
                <td style="width:8%">{$productactivity_obj->turnOver}</td>
                <td style="width:10%">{$selectedcur}</td>
                <td style="width:15%">{$productactivity_obj->saleType}</td>
                <td style="width:8%">{$productactivity_obj->salesForecast}</td>
                <td style="width:8%">{$productactivity_obj->quantityForecast}</td>
            </tr>
        </tbody>';
    </table>
</div>
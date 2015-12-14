<div><h2>ARO Details</h2>
    <div style="margin-bottom:20px;">
        <table style="width:100%">
            <tr><td style="font-weight: bold;width:40%">{$lang->orderpurchasetype}</td><td>{$data[purchasetype_output]}</td></tr>
           <!-- <tr><td style="font-weight: bold;width:40%">{$lang->destinationcountry}</td><td></td></tr>-->
            <tr><td style="font-weight: bold;width:40%">{$lang->purchasingcompany}</td><td>{$data[affiliate_output]}</td></tr>
            <tr><td style="font-weight: bold;width:40%">{$lang->intermediary}{$lang->affiliate} (if Any)</td><td>{$data[intermed_aff_output]}</td></tr>
            <tr><td style="font-weight: bold;width:40%">{$lang->purchasecurrency}</td><td>{$data[currency]}</td></tr>
        </table>
    </div>

    <div style="margin-bottom:20px;">
        <table style="width:100%">
            {$data[products_output]}
        </table>
    </div>

    <div style="margin-bottom:20px;">
        <table style="width:100%">
            <tr><td style="font-weight: bold;width:40%">{$lang->estdateofpayment}</td><td>{$data[vendorEstDateOfPayment_formatted]}</td></tr>
            <tr><td style="font-weight: bold;width:40%">{$lang->estdateofpayment}</td><td>{$data[intermedEstDateOfPayment_formatted]}</td></tr>
            <tr><td style="font-weight: bold;width:40%">{$lang->promiseofpayment}</td><td>{$data[promiseOfPayment_formatted]}</td></tr>
        </table>
    </div>

    <div style="margin-bottom:20px;">
        <table style="width:100%">
            <tr><td style="font-weight: bold;width:40%">{$lang->invvalusdsupplier}</td><td>{$data[invoiceValueFromSupplier]}</td></tr>
            <tr><td style="font-weight: bold;width:40%">{$lang->invvalintermedaffcustomer}</td><td>{$data[invoiceValueAffiliate]}</td></tr>
            <tr><td style="font-weight: bold;width:40%">{$lang->netmarginintermedusd}</td><td>{$data[netmarginIntermed]} | {$data[netmarginIntermedPerc]}</td></tr>
            <tr><td style="font-weight: bold;width:40%">{$lang->invvaluecustomer}</td><td>{$data[invoiceValueCustomer]}</td></tr>
            <tr><td style="font-weight: bold;width:40%">{$lang->netmarginaff}</td><td>{$data[netmarginLocal]} | {$data[netmarginLocalPerc]}</td></tr>
            <tr><td style="font-weight: bold;width:40%">{$lang->totalnetmarginofallorkilaent}</td><td>{$data[globalNetmargin]}</td></tr>
        </table>
    </div>

</div>



<div><h2>ARO Details</h2>
    <div style="margin-bottom:20px;">
        <table style="width:100%;">
            <tr><td style="font-weight: bold;width:40%;background-color:#92D050;">{$lang->orderpurchasetype}</td><td style="background-color: #F1F1F1;">{$data[purchasetype_output]}</td></tr>
           <!-- <tr><td style="font-weight: bold;width:40%">{$lang->destinationcountry}</td><td></td></tr>-->
            <tr><td style="font-weight: bold;width:40%;background-color:#92D050;">{$lang->purchasingcompany}</td><td style="background-color: #F1F1F1;">{$data[affiliate_output]}</td></tr>
            <tr><td style="font-weight: bold;width:40%;background-color:#92D050;">{$lang->intermediary}{$lang->affiliate} (if Any)</td><td style="background-color: #F1F1F1;">{$data[intermed_aff_output]}</td></tr>
            <tr><td style="font-weight: bold;width:40%;background-color:#92D050;">{$lang->purchasecurrency}</td><td style="background-color: #F1F1F1;">{$data[currency]}</td></tr>
        </table>
    </div>

    <div style="margin-bottom:20px;">
        <table style="width:100%">
            {$data[products_output]}
        </table>
    </div>

    <div style="margin-bottom:20px;">
        <table style="width:100%">
            <tr><td style="font-weight: bold;width:40%;background-color:#92D050;">{$lang->quantity}</td><td style="background-color: #F1F1F1;">{$data[totalQuantityUom]}</td></tr>
            <tr><td style="font-weight: bold;width:40%;background-color:#92D050;">{$lang->estdateofpayment} {$lang->vendor}</td><td style="background-color: #F1F1F1;">{$data[vendorEstDateOfPayment_formatted]}</td></tr>
            <tr><td style="font-weight: bold;width:40%;background-color:#92D050;">{$lang->estdateofpayment} {$lang->intermediary}</td><td style="background-color: #F1F1F1;">{$data[intermedEstDateOfPayment_formatted]}</td></tr>
            <tr><td style="font-weight: bold;width:40%;background-color:#92D050;">{$lang->promiseofpayment}</td><td style="background-color: #F1F1F1;">{$data[promiseOfPayment_formatted]}</td></tr>
        </table>
    </div>

    <div style="margin-bottom:20px;">
        <table style="width:100%">
            <tr><td style="font-weight: bold;width:40%;background-color:#92D050;">{$lang->invvalusdsupplier}</td><td style="background-color: #F1F1F1;">{$data[invoiceValueFromSupplier]}</td></tr>
            <tr><td style="font-weight: bold;width:40%;background-color:#92D050;">{$lang->invvalintermedaffcustomer}</td><td style="background-color: #F1F1F1;">{$data[invoiceValueAffiliate]}</td></tr>
            <tr><td style="font-weight: bold;width:40%;background-color:#92D050;">{$lang->netmarginintermedusd}</td><td style="background-color: #F1F1F1;">{$data[netmarginIntermed]} | {$data[netmarginIntermedPerc]}</td></tr>
            <tr><td style="font-weight: bold;width:40%;background-color:#92D050;">{$lang->invvaluecustomer}</td><td style="background-color: #F1F1F1;">{$data[invoiceValueCustomer]}</td></tr>
            <tr><td style="font-weight: bold;width:40%;background-color:#92D050;">{$lang->netmarginaff}</td><td style="background-color: #F1F1F1;">{$data[netmarginLocal]} | {$data[netmarginLocalPerc]}</td></tr>
            <tr><td style="font-weight: bold;width:40%;background-color:#92D050;">{$lang->totalnetmarginofallorkilaent}</td><td style="background-color: #F1F1F1;">{$data[globalNetmargin]}</td></tr>
        </table>
    </div>

</div>



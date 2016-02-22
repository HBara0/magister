<a class="header" href="#"><h2 id="aro_orderidentification">{$lang->orderidentification}</h2></a>
<div>
    <p>
    <table class="datatable">
        <tbody style="width:100%">
            <tr>
                <td class="border_right" style="font-weight: bold;width:16%;">{$lang->affiliate}</td>
                <td class="border_right" style="width:16%">{$localaff->get_displayname()}</td>
                <td class="border_right" style="font-weight: bold;width:16%;">{$lang->orderpurchasetype}</td>
                <td class="border_right" style="width:16%">{$purchasetype->get_displayname()}</td>
                <td class="border_right" style="font-weight: bold;width:16%;">{$lang->country}</td>
                <td class="border_right" style="width:16%">{$localaff->get_displayname()}</td>
            </tr>
            <tr class="altrow">
                <td class="border_right" style="font-weight: bold;width:16%;">{$lang->orderreference}</td>
                <td class="border_right" style="width:16%">{$aroorderrequest->orderReference}</td>
                <td class="border_right" style="font-weight: bold;width:16%;">{$lang->buyingcurr}</td>
                <td class="border_right" style="width:16%">{$arorequest[currency]}</td>
                <td class="border_right" style="font-weight: bold;width:16%;">{$lang->usdexchangerate}</td>
                <td class="border_right" style="width:16%">{$aroorderrequest->exchangeRateToUSD} </td>
            </tr>
            <tr>
                <td class="border_right" style="font-weight: bold;width:16%;">{$lang->inspectiontype}</td>
                <td class="border_right" style="width:16%">{$aroorderrequest->inspectionType}</td>
                <td class="border_right" style="font-weight: bold;width:16%;">{$lang->bmanager}</td>
                <td class="border_right" style="width:16%">{$aroorderrequest->aroBusinessManager_output}</td>
                {$arocustomer_output}
            </tr>
        </tbody>
    </table>
</p>
</div>



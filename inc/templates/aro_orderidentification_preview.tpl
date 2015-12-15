<a class="header" href="#"><h2 id="aro_orderidentification">{$lang->orderidentification}</h2></a>
<div>
    <p>
    <table>
        <tbody style="width:100%">
            <tr>
                <td style="font-weight: bold;width:16%;border-left:4px solid #F2F2F2;">{$lang->affiliate}</td>
                <td style="width:16%">{$localaff->get_displayname()}</td>
                <td style="font-weight: bold;width:16%;border-left: 4px solid #F2F2F2;">{$lang->orderpurchasetype}</td>
                <td style="width:16%">{$purchasetype->get_displayname()}</td>
                {$arocustomer_output}
            </tr>
            <tr>
                <td style="font-weight: bold;width:16%;border-left:4px solid #F2F2F2;">{$lang->orderreference}</td>
                <td style="width:16%">{$aroorderrequest->orderReference}</td>
                <td style="font-weight: bold;width:16%;border-left: 4px solid #F2F2F2;">{$lang->buyingcurr}</td>
                <td style="width:16%">{$arorequest[currency]}</td>
                <td style="font-weight: bold;width:16%;border-left:4px solid #F2F2F2;">{$lang->usdexchangerate}</td>
                <td style="width:16%">{$aroorderrequest->exchangeRateToUSD} </td>
            </tr>
            <tr>
                <td style="font-weight: bold;width:16%;border-left:4px solid #F2F2F2;;">{$lang->inspectiontype}</td>
                <td style="width:16%">{$aroorderrequest->inspectionType}</td>
                <td style="font-weight: bold;width:16%;border-left:4px solid #F2F2F2;">{$lang->bmanager}</td>
                <td style="width:16%">{$aroorderrequest->aroBusinessManager_output}</td>
            </tr>
        </tbody>
    </table>
</p>
</div>


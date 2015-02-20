<a class="header" href="#"><h2>Order Identification</h2></a>
<div>
    <p>
    <table>
        <tbody>
            <tr>
                <td>Affiliate</td>
                <td>{$affiliate_list}</td>
                <td>order Type</td>
                <td>{$purchasetypelist}</td>
                <td>Buying Currency</td>
                <td>{$currencies_list}</td>
            </tr>
            <tr>
                <td>Order Reference</td>
                <td><input type="text"   readonly="readonly" name="orderReference" id="orderreference"/></td>
                <td>Exchange rate to usd</td>
                <td><input type="number"  step="any" name="exchangeRateToUSD" id="exchangeRateToUSD"/></td>
            </tr>

            <tr>
                <td>Inspection Type</td>
                <td>{$inspectionlist}</td>
                <td></td>
                <td></td>
            </tr>

        </tbody>
    </table>
</p>
</div>



<h1>{$lang->priceproduct}</h1>
<form name='perform_grouppurchase/pricing_Form' id="perform_grouppurchase/pricing_Form" method="post">
    <table width="100%">
        <thead>
            <tr>
                <td colspan="6" class="ui-state-highlight ui-corner-all" style="padding: 5px;"><strong>{$lang->supplier}</strong> <input type='text' id='supplier_1_autocomplete' /><input type="text" size="3" id="supplier_1_id_output" disabled/><input type='hidden' id='supplier_1_id' name='spid'  /><div id='searchQuickResults_1' class='searchQuickResults' style='display:none;'></div>
                </td>
            </tr>
            <tr>
                <th style="width:24%;">{$lang->product}</th>
                <th style="width:19%;">{$lang->incoterm}</th>
                <th style="width:19%;">{$lang->price}</th>
                <th style="width:19%;">{$lang->unit}</th>
                <th style="width:19%;">{$lang->validthrough}</th>
                <th style="width:19%;">{$lang->remarks}</th>
            </tr>
        </thead>
        <tr>
            <td><input name="affiliate[1]" value="0" type="hidden"> <input type='text' id="product_1_autocomplete" name="product_name" autocomplete='off'/><input type='hidden' size='2' id='product_1_id_output' disabled='disabled' /><input type='hidden' id='product_1_id' name='pid' /><div id='searchQuickResults_product_1' class='searchQuickResults' style='display:none;'></div></td>
            <td align="center">{$pricingmethods_mainlist}</td>
            <td align="center"><input type='text' name="price[1]" accept="numeric" id="price[1]" size="10"/></td>
            <td align="center">{$units_mainlist}</td>
            <td align="center"><input type='text' id='pickDate_valid' autocomplete='off' /> <input type='hidden' name='validThrough[1]' id='altpickDate_valid' /></td>
            <td align="center"><input type='text' name="remark[1]"  id="remark[1]" size="30"/></td>
        </tr>
        <tr>
            <td class="subtitle" colspan="6"><hr />{$lang->priceperaffiliate}</td>
        </tr>
        <tr>
            <th>{$lang->affiliate}</th>
            <th>{$lang->incoterm}</th>
            <th>{$lang->price}</th>
            <th>{$lang->unit}</th>
            <th>{$lang->validthrough}</th>
            <th>{$lang->remarks}</th>
        </tr>
        <tbody id="prices_tbody">
            <tr id='2'>
                <td>{$affiliates_list}</td>
                <td align="center">{$pricingmethods_list}</td>
                <td align="center"><input type='text' name="price[2]" accept="numeric" id="price[2]" size="10"/></td>
                <td align="center">{$units_list}</td>
                <td align="center">
                    <input type='text' id='pickDate_valid2' autocomplete='off' />
                    <input type='hidden' name='validThrough[2]' id='altpickDate_valid2' /></td>
                <td align="center"><input type='text' name="remark[2]" id="remark[2]" size="30"/></td>
            </tr>
        </tbody>
        <tr>
            <td><img src="images/add.gif" id="addmore_prices" alt="{$lang->add}"> </td>
            <td colspan="5">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="6" class="subtitle"><hr />{$lang->comments}</td>
        </tr>
        <tr>
            <td colspan="6"><textarea cols="30" rows="5" id="notes" name="notes"></textarea></td>
        </tr>
        <tr>
            <td><input type="button" id="perform_grouppurchase/pricing_Button" value="{$lang->price}" class="button"/></td>
            <td colspan="5">&nbsp;</td>
        </tr>
    </table>
</form>
<div id="perform_grouppurchase/pricing_Results"></div>
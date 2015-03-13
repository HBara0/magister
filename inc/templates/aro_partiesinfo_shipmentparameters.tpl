<tr>
    <td>{$lang->estdateofshipment}</td>
    <td>
        <input type="text" id="pickDate_estDateOfShipment" autocomplete="off" tabindex="2" value="{$partiesinfo[estDateOfShipment_formatted]}" required="required" style="width:150px;"/>
        <input type="hidden" name="partiesinfo[estDateOfShipment]" id="altpickDate_estDateOfShipment" value="{$partiesinfo[estDateOfShipment_output]}"/>
    </td>
    <td>{$lang->transittime}</td>
    <td><input type="number" step="any" name="partiesinfo[transitTime]" id="partiesinfo_transitTime" value="{$aropartiesinfo_obj->transitTime}" required='required'></td>
    <td>{$lang->estclearancetime}</td>
    <td><input type="number" step="any" name="partiesinfo[clearanceTime]" id="partiesinfo_clearanceTime" value="{$aropartiesinfo_obj->clearanceTime}" required='required'></td>
</tr>
<tr>
    <td>{$lang->countryofshipment}</td>
    <td>{$countryofshipment_list}</td>
    <td>{$lang->countryoforigin}</td>
    <td>{$countryoforigin_list}</td>
</tr>

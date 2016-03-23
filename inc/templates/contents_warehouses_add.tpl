<h1>{$lang->managewarehouse}</h1>
<form action="#" method="post" id="perform_contents/createwarehouses_Form" name="perform_contents/createwarehouses_Form">
    <table width="100%" border="0" cellpadding="0" cellspacing="1">
        <tr>
            <td>{$lang->affiliate}</td><td colspan="2">{$affiliates_list}
                <input type="hidden" value="{$warehouse->wid}" name="warehouse[wid]" /></td>
        </tr>
        <tr>
            <td>{$lang->name}</td><td><input type="text" id="warehouse_name" name="warehouse[name]" value="{$warehouse->name}" required="required"/></td>
        </tr>
        <tr><td><h3 style="margin-bottom:10px;">{$lang->address}</h3></td></tr>
        <tr>
            <td>{$lang->addressline1}</td>
            <td colspan="2">
                <input type="text" id="warehouse_addressLine1" name="warehouse[addressLine1]" value="{$warehouse->addressLine1}"/>
            </td>
        </tr>
        <tr>
            <td>{$lang->addressline2}</td>
            <td colsapn="2">
                <input type="text" id="warehouse_addressLine2" name="warehouse[addressLine2]" value="{$warehouse->addressLine2}"/>
            </td>
        </tr>

        <tr>
            <td>{$lang->postcode}</td>
            <td colspan="2"><input type="text" id="warehouse_postalCode" name="warehouse[postalCode]" accept="numeric" value="{$warehouse->postalCode}"/></td>
        </tr>
        <tr>
            <td>{$lang->city}</td>
            <td colspan="2"><input type="text" autocomplete="off" tabindex=""  id="cities_{$sequence}_cache_autocomplete"    value="{$warehouse->city}" required="required"/>
                <input type='hidden' id='cities_{$sequence}_cache_id'   name="warehouse[ciid]" value="{$warehouse->ciid}"/>
            </td>
        </tr>
        <tr>
            <td>{$lang->geolocation}</td>
            <td colspan="2">
                <input type="text" name="warehouse[geoLocation]" id="warehouse_geoLocation" placeholder="33.892516 35.510929" pattern="(\-?\d+(\.\d+)?) \s*(\-?\d+(\.\d+)?)" value="{$warehouse->longitude} {$warehouse->latitude}"/>
                <span class="smalltext">({$lang->longlattidue})</span></td>
        </tr>
        <tr>
            <td>{$lang->idob}</td>
            <td colspan="2"><input type="text" id="warehouse_integrationOBId" name="warehouse[integrationOBId]"  value="{$warehouse->integrationOBId}"/></td>
        </tr>

        <tr>   <td colspan="3" align="left">
                <fieldset class="altrow2" style="width:25%;">
                    <input name="warehouse[isActive]" id="warehouse_isActive" type="checkbox" value="1" {$checked[isactive]}> {$lang->isactive}</td>
                </fieldset>
        </tr>

        <tr>
            <td colspan="3" align="left">
                <input type="submit" value="{$lang->save}" id="perform_contents/createwarehouses_Button" class="button"/> <input type="reset" value="{$lang->reset}" class="button"/>
                <div id="perform_contents/createwarehouses_Results"></div>
            </td>
        </tr>
    </table>
</form>
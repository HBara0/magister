<h1>{$lang->createlocations}</h1>

<form action="#" method="post" id="perform_contents/createlocations_Form" name="perform_contents/createlocations_Form">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td><strong>{$lang->entity}</strong></td>
            <td colspan="2"><input type="text"  autocomplete="off" id="allentities_1_cache_autocomplete"/><input type='hidden' id='allentities_1_cache_id' name='entitylocation[eid]' value="{$entitylocation[eid]}"/></td>
        </tr>
        <tr>
            <td><strong>{$lang->locationtype}</strong></td><td colspan="2">{$locationstypes_list}</td>
        </tr>
        <tr>
            <td><strong>{$lang->country}</strong></td><td colspan="2">{$countries_list}</td>
        </tr>
        <tr>
            <td>{$lang->city}</td>
            <td> <input type="text" required="required"  autocomplete="off" id="cities_1_cache_autocomplete"/><input type='hidden' id='cities_1_cache_id' name='entitylocation[ciid]'  value="{$entitylocation[ciid]}"/></td>
        </tr>
        <tr>
            <td>{$lang->address}</td>
            <td colspan="2"><input type="text" id="addressLine1" name="entitylocation[addressLine1]" /><br /><input type="text" id="addressLine2" name="entitylocation[addressLine2]" /></td>
        </tr>
        <tr>
            <td>{$lang->buildingname}</td><td colspan="2"><input type="text" id="building" name="entitylocation[building]" /> <input type="text" id="floor" name="entitylocation[floor]" size='3' maxlength="3" /></td>
        </tr>
        <tr>
            <td>{$lang->postcode}</td><td colspan="2"><input type="text" id="postCode" name="entitylocation[postCode]" accept="numeric" /></td>
        </tr>
        <tr>
            <td>{$lang->pobox}</td><td colspan="2"><input type="text" id="poBox" name="entitylocation[poBox]" accept="numeric" /></td>
        </tr>
        <tr>
            <td>{$lang->telephone}</td>
            <td colspan="2">+ <input type="text" id="telephone_intcode" name="entitylocation[telephone_intcode]" size="3" maxlength="3" accept="numeric" />
                <input type="text" id="telephone_areacode" name="entitylocation[telephone_areacode]" size='4' maxlength="4" accept="numeric" />
                <input type="text" id="telephone_number" name="entitylocation[telephone_number]" accept="numeric"  /><br />
        </tr>
        <tr>
            <td>{$lang->geolocation}</td><td colspan="2"><input type="text" name="entitylocation[geoLocation]" id="geoLocation" placeholder="33.892516 35.510929" pattern="(\-?\d+(\.\d+)?) \s*(\-?\d+(\.\d+)?)"/> <span class="smalltext">({$lang->longlattidue})</span></td>
        </tr>
        <tr>
            <td colspan="3" align="left">
                <hr />
                <input type="submit" class="button" value="{$lang->add}" id="perform_contents/createlocations_Button" /> <input  class="button" type="reset" value="{$lang->reset}"/>
                <div id="perform_contents/createlocations_Results"></div>
            </td>
        </tr>
    </table>
</form>
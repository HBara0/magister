<div id="popup_addhotel" title="{$lang->addotherhotel}"  style="height: 200px;">
    <form action="#" method="post" id="add_otherhotel_{$core->input[module]}_Form" name="add_otherhotel_{$core->input[module]}_Form">
        <input type="hidden" name="action" value="do_add_otherhotel" />
        <table cellpadding='0' cellspacing='0' width='100%'>
            <tr>
            <input type='hidden' value='{$sequence}' name="sequence">
            <td width="40%"><strong>{$lang->hotel}*</strong></td><td><input type='text' required="required"   name="otherhotel[name]"  tabindex="100"/></td>
            </tr>
            <tr>
                <td><strong>{$lang->city}*</strong></td>

                <Td><input type="text" autocomplete="off" tabindex="100"  id="cities_cache_{$sequence}_autocomplete" value="{$segmentobj_destcityname}" required="required"/></Td>
            <input type='hidden' id='cities_cache_{$sequence}_id'   name="otherhotel[city]" value="{$destcityid}"/>
            </td>

            </tr>
            <tr>
                <td>
                    <input type='hidden' id='countries_{$sequence}_cache_id'   name="otherhotel[country]" value="{$segdescity_obj_coid}"/>
                </td>

            </tr>
            <tr><td>   <div style="display:inline-block;padding:5px;width:15%;">{$lang->address}*</div></td><td><div style="display:inline-block;padding:5px;width:20%;"><input tabindex="100" name="otherhotel[addressLine1]" type="textarea" required="required">{$selectedhotel[$segid][$approved_hotels[tmhid]][address]}</div>
                </td></tr>
            <tr>
                <td>
                    <div style="display:inline-block;padding:5px;width:15%;">{$lang->phone}*</div>
                </td>
                <td>
                    {$countriescodes_list}
                  <!--  <input type="text" tabindex="100" id="telephone_areacode" name="telephone_areacode" size='4' maxlength="4" accept="numeric" value="{$telephone[1][areacode]}"/>-->
                    <br/>
                    <input type="text" tabindex="100" id="telephone_number" name="telephone_number" accept="numeric" value="{$telephone[1][number]}" required="required"/><br />
                </td>
            </tr>
            <tr>
                <td>{$lang->averagepriceinusd}</td>
                <td><input tabindex="100" type="number" name="otherhotel[avgPrice]"><input type="hidden" name="otherhotel[currency]" value="840"></td>
            </tr>
            <tr>
                <td>{$lang->website}</td>
                <td><input tabindex="100" type="text" accept="" name="otherhotel[website]"></td>
            </tr>
            <tr>
                <td>{$lang->stars}</td>
                <td>{$ratingselectlist}</td>
            </tr>
            <tr>
                <td>{$lang->contactperson}</td>
                <td><input tabindex="100" type="text" name="otherhotel[contactPerson]"></td>
            </tr>
            <tr>
                <td>{$lang->contactemail}</td>
                <td><input tabindex="100" type="email" name="otherhotel[contactEmail]"></td>
            </tr>
            <tr>
                <td>{$lang->distancefromoffice}</td>
                <td><input tabindex="100" type="text" name="otherhotel[distance]"></td>
            </tr>
            <tr>
                <td>{$lang->negotiatedcontract}</td>
                <td><input tabindex="100" type="checkbox" name="otherhotel[isContracted]" value="1"></td>
            </tr>
            <tr>
                <td colspan="2" align="left">
                    <hr />
                    <input type='submit' id='add_otherhotel_{$core->input[module]}_Button' value='{$lang->savecaps}' class='button'/>
                    <div id="add_otherhotel_{$core->input[module]}_Results"></div>
                </td>
            </tr>
        </table>
    </form>
</div>
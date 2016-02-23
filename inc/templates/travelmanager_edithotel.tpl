<script src="{$core->settings[rootdir]}/js/jquery.rateit.min.js" type="text/javascript"></script>
<link href="{$core->settings[rootdir]}/css/rateit.min.css" rel="stylesheet" type="text/css">
<script>
    $(function() {
    {$header_ratingjs}

    });
</script>
<h1>{$lang->edithotel}</h1>
{$approve}
<form name="perform_travelmanager/edithotel_Form" id="perform_travelmanager/edithotel_Form" action="#" method="post">
    <input type="hidden" name="hotel[tmhid]" value='{$hotel['tmhid']}'>
    <table>
        <tr>
            <td>{$lang->iscontracted}</td><td>{$contractedchekbox}</td><td>{$lang->isapproved}</td><td>{$approvcheckbox}</td>
            <td>{$lang->stars}</td><td>{$criteriaandstars}</td>
        </tr>
        <tr>
            <td>{$lang->hotelname}</td><td><input type="text" name="hotel[name]" value="{$hotel['name']}"></td>
            <td>{$lang->city}</td>
            <td><input type="text" id="cities_1_autocomplete" autocomplete="false" tabindex="1" value="{$cityname}" required="required"/>
                <input type='hidden' id='cities_1_id'  name="hotel[city]" value="{$hotel['city']}"/>
                <input type='hidden' id='cities_1_id_output' name="hotel[city]" value="{$hotel['city']}" disabled/>
            </td>
            <td>{$lang->country}</td>
            <td><input type="text" id="countries_1_autocomplete" autocomplete="false" tabindex="1" value="{$countryname}" required="required"/>
                <input type='hidden' id='countries_1_id'  name="hotel[country]" value="{$hotel['country']}"/>
                <input type='hidden' id='countries_1_id_output' name="hotel[country]" value="{$hotel['country']}" disabled/>
            </td>
        </tr>
        <tr>
            <td>{$lang->addressline1}</td><td><textarea name='hotel[addressLine1]'>{$hotel['addressLine1']}</textarea></td>
            <td>{$lang->addressline2}</td><td><textarea name='hotel[addressLine2]'>{$hotel['addressLine2']}</textarea></td>
        </tr>
        <tr>
            <td>{$lang->postcode}</td><td><input type="text" name='hotel[postCode]' value="{$hotel['postCode']}"></td>
            <td>{$lang->pobox}</td><td><input type="text" name='hotel[poBox]' value="{$hotel['poBox']}"></td>
        </tr>
        <tr>
            <td>{$lang->phone}</td>
            <td>
                {$countriescodes_list}
                <input type="text" tabindex="100" id="telephone_areacode" name="hotel[telephone_areacode]" size='4' maxlength="4" accept="numeric" value="{$telephone_areacode}"/>
                <br/>
                <input type="text" tabindex="100" id="telephone_number" name="hotel[telephone_number]" accept="numeric" value="{$telephone_number}"/><br />
            </td>
            <td>{$lang->website}</td><td><input type="text" name='hotel[website]' value="{$hotel['website']}"></td>
            <td>{$lang->mainemail}</td><td><input type="email" name='hotel[mainEmail]' value="{$hotel['mainEmail']}"></td>
        </tr>
        <tr>
            <td>{$lang->fax}</td><td><input type='number' name='hotel[fax]' value="{$hotel['fax']}"></td>
            <td>{$lang->contactperson}</td><td><input type='text' name='hotel[contactPerson] value="{$hotel['contactPerson']}"'></td>
            <td>{$lang->contactemail}</td><td><input type="email" name='hotel[contactEmail]' value="{$hotel['contactEmail']}"></td>
        </tr>
        <tr>
            <td>{$lang->averageprice}</td><td><input type="number" name="hotel[avgPrice]" value="{$hotel['avgPrice']}"></td>
            <td>{$lang->currency}</td><td>{$currency_list}</td>
            <td>{$lang->distancefromoffice}</td><td><input type="text" name="hotel[distance]" value='{$hotel['distance']}'></td>
        </tr>
        <tr>
            <td>{$lang->mgmtreview}</td><td colspan="3"><textarea name='hotel[mgmtReview]' class="redactor_editor">{$hotel['mgmtReview']}</textarea></td>
        </tr>
        <tr>
            <td>
                <input type='submit' style="cursor: pointer" class='button' value="{$lang->savecaps}" id='perform_travelmanager/edithotel_Button'>
            </td>
        </tr>
    </table>
</form>
<div id="perform_travelmanager/edithotel_Results"></div>
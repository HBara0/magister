<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->requestleave}</title>
        {$headerinc}
        <script src="{$core->settings[rootdir]}/js/jquery.rateit.min.js" type="text/javascript"></script>
        <link href="{$core->settings[rootdir]}/css/rateit.min.css" rel="stylesheet" type="text/css">
        <script>
            $(function() {
            {$header_ratingjs}

            });
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer" colspan="2">
            <h1>{$lang->edithotel}</h1>
            {$approve}
            <form name="perform_travelmanager/edithotel_Form" id="perform_travelmanager/edithotel_Form" action="#" method="post">
                <input type="hidden" name="hotel[tmhid]" value='{$hotel['tmhid']}'>
                <div class="form-inline form-group">
                    <div class="checkbox" id="entype">
                        <label>
                            {$contractedchekbox}{$lang->iscontracted}
                        </label>
                        &nbsp;
                        <label>
                            {$approvcheckbox}{$lang->isapproved}
                        </label>
                    </div>
                </div>
                <div class="form-group form-inline">
                    <label for="hotelname">{$lang->hotelname}</label><input class="form-control" type="text"id="hotelname" name="hotel[name]" value="{$hotel['name']}">
                    <label for="cities_1_autocomplete">{$lang->city}</label>
                    <input  class="form-control" type="text" id="cities_1_autocomplete" autocomplete="false" tabindex="1" value="{$cityname}" required="required"/>
                    <input type='hidden' id='cities_1_id'  name="hotel[city]" value="{$hotel['city']}"/>
                    <input type='hidden' id='cities_1_id_output' name="hotel[city]" value="{$hotel['city']}" disabled/>

                    <label for="countries_1_autocomplete">{$lang->country}</label>
                    <input  class="form-control" type="text" id="countries_1_autocomplete" autocomplete="false" tabindex="1" value="{$countryname}" required="required"/>
                    <input type='hidden' id='countries_1_id'  name="hotel[country]" value="{$hotel['country']}"/>
                    <input type='hidden' id='countries_1_id_output' name="hotel[country]" value="{$hotel['country']}" disabled/>
                </div>
            </div>
            <div class="form-group ">
                <label for="addressline1">{$lang->addressline1}</label><textarea id="addressline1" class="form-control" name='hotel[addressLine1]'>{$hotel['addressLine1']}</textarea>
                <label for="addressline2">{$lang->addressline2}</label><textarea id="addressline2" class="form-control" name='hotel[addressLine2]'>{$hotel['addressLine2']}</textarea>
            </div>
            <div class="form-group ">
                <label for="postcode">{$lang->postcode}</label><input id="postcode" class="form-control"  type="text" name='hotel[postCode]' value="{$hotel['postCode']}">
                <label for="pobox">{$lang->pobox}</label><input id="pobox" class="form-control"  type="text" name='hotel[poBox]' value="{$hotel['poBox']}">
            </div>
             <div class="form-group ">
                <label>{$lang->phone}</label>
                {$countriescodes_list}
                <input type="text" tabindex="100" id="telephone_areacode" name="hotel[telephone_areacode]" size='4' maxlength="4" accept="numeric" value="{$telephone_areacode}"/>
                <input type="text" tabindex="100" id="telephone_number" name="hotel[telephone_number]" accept="numeric" value="{$telephone_number}"/>
            </div>
            <div class="form-group ">
                <label for="website">{$lang->website}</label><input id="website" class="form-control"  type="text" name='hotel[website]' value="{$hotel['website']}">
                <label for="mainEmail">{$lang->mainEmail}</label><input id="mainEmail" class="form-control"  type="email" name='hotel[mainEmail]' value="{$hotel['mainEmail']}">
            </div>
            <div class="form-group ">
                <label for="fax">{$lang->fax}</label><input id="fax" class="form-control"  type="text" name='hotel[fax]' value="{$hotel['fax']}">
                <label for="contactPerson">{$lang->contactperson}</label><input id="contactPerson" class="form-control"  type="text" name='hotel[contactPerson]' value="{$hotel['contactPerson']}">
                <label for="contactEmail">{$lang->contactemail}</label><input id="contactEmail" class="form-control"  type="email" name='hotel[contactEmail]' value="{$hotel['contactEmail']}">
            </div>
           <div class="form-group ">
                <label for="avgPrice">{$lang->averageprice}</label><input id="avgPrice" class="form-control"  type="number" name='hotel[avgPrice]' value="{$hotel['avgPrice']}">
                <label for="currency">{$lang->currency}</label>{$currency_list}
                <label for="distance">{$lang->distancefromoffice}</label><input id="distance" class="form-control"  type="email" name='hotel[distance]' value="{$hotel['distance']}">
            </div>
              <div class="form-group ">
                <label for="mgmtReview">{$lang->mgmtreview}</label><textarea id="mgmtReview" class="form-control" name='hotel[mgmtReview]'>{$hotel['mgmtReview']}</textarea>
            </div>
            <input type='submit' style="cursor: pointer" class='button' value="{$lang->savecaps}" id='perform_travelmanager/edithotel_Button'>
        </form>
        <div id="perform_travelmanager/edithotel_Results"></div>
    </td>
</tr>
{$footer}
</body>
</html>
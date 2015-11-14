<div>
    <table>
        <tr>  <td>{$lang->origincity}*</td>
            <td><input type="text"  disabled="disabled" autocomplete="false" tabindex="1"  id="cities_{$sequence}_cache_autocomplete"    value="{$segment[$sequence][origincity][name]}" required="required"/>
                <input type='hidden' id='cities_{$sequence}_cache_id'   name="segment[{$sequence}][originCity]" value="{$segment[$sequence][origincity][ciid]}"/>
            </td>

            <td style="text-align:left;">{$lang->destinationcity}*</td>
            <td><input type="text" {$disabled}  id="destinationcity_{$sequence}_cache_autocomplete" autocomplete="false" tabindex="1" value="{$segment[$sequence][destinationcity][name]}" required="required"/>
                <input type="hidden" id="coid"  value="{$segment[countryleave]}" name="coid"/>
                <input type='hidden' id='destinationcity_{$sequence}_cache_id'  name="segment[{$sequence}][destinationCity]" value="{$segment[$sequence][destinationcity][ciid]}"/>
                <input type='hidden' id='destinationcity_{$sequence}_cache_id_output' name="segment[{$sequence}][destinationCity]" value="{$segment[$sequence][destinationcity][ciid]}" disabled/>

            </td>
            <td rowspan="2"> <p id="pickDateto_warning_{$sequence}" style="vertical-align: top;color:red;width:175px;margin-left:10px;"></p></td>
        </tr>
        <tr>
            <td width="18%">{$lang->fromdate}*</td>
            <td><input type="text" id="pickDate_from_{$sequence}"  disabled="disabled"   autocomplete="false" tabindex="1" value="{$segment[$sequence][fromDate_output]}" required="required"/>
                <input type="hidden" name="segment[{$sequence}][fromDate]" id="altpickDate_from_{$sequence}"  value="{$segment[$sequence][fromDate_formatted]}" />
            </td>


            <td>{$lang->todate}*</td>
            <td><input type="text" id="pickDate_to_{$sequence}"    autocomplete="false" tabindex="1" value="{$segment[$sequence][toDate_output]}" required="required" />
                <input type="hidden" name="segment[{$sequence}][toDate]" id="altpickDate_to_{$sequence}" value="{$segment[$sequence][toDate_formatted]}"/>
                <input type="hidden" name="leaveDate" id="leaveDate_to_{$sequence}" value="{$leave[$sequence][toDate]}"/></td>
               <!-- <td> <span id="numdays_{$sequence}">{$segment[$sequence][numberdays]}</span></td>-->
        </tr>

        <tr id="purposes_row_{$sequence}">
            <td colspan="2" class="subtitle">{$lang->internalpurposes}*</td><td colspan="2" class="subtitle">{$lang->externalpurposes}*</td>
        </tr>
        <tr><td colspan="2">{$internalpurposes_checks}</td><td colspan="2">{$extpurposes_checks}</td></tr>

        <tr style="width:100%;">
            <td colspan="2" style="vertical-align: top;width:20%;">
                <div style="display: inline-block;width:100%">
                    <table border="0" cellspacing="1" cellpadding="1" width="100%">
                        <tbody id="affiliate_{$sequence}_tbody">
                            {$affiliates_output}
                        </tbody>
                        <tr>
                            <td data-purposes="internal_{$sequence}" {$display_internal} >
                                <input name="numrows_affiliate" type="hidden" id="numrows_affiliate_{$affrowid}" value="{$affrowid}">
                                <img src="./images/add.gif" id="ajaxaddmore_travelmanager/plantrip_affiliate_{$sequence}" alt="{$lang->add}">
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="vertical-align: top;width:20%;">
                <div style="display: inline-block;width:100%">
                    <input type="hidden" id="event" value="1">
                    <input type="hidden" id="eventpurposeid" >
                    <table border="0" cellspacing="1" cellpadding="1" width="100%" {$addmore_display}>
                        <tr  id="events_{$sequence}_trow">

                        </tr>
                        <tbody id="entities_{$sequence}_tbody">
                            {$entities}
                        </tbody>
                        <tr>
                            <td data-purposes="external_{$sequence}" {$display_external} >
                                <input type="hidden" name="ajaxaddmoredata[ltpid]" id="ajaxaddmoredata_ltpid_{$sequence}" value="{$ltpid}"/>
                                <input name="numrows_entities" type="hidden" id="numrows_entities_{$entityrowid}" value="{$entityrowid}">
                                <img src="./images/add.gif" id="ajaxaddmore_travelmanager/plantrip_entities_{$sequence}" alt="{$lang->add}">
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
        <tr><td id="considerleisure_{$sequence}">{$lang->considerleisuretourism}</td><td><input type="checkbox" name="segment[{$sequence}][isNoneBusiness]" value="1" {$checked['isNoneBusiness']}/></td></tr>
        <tr><td id="segreason_{$sequence}">{$lang->reason}*</td><td><textarea name="segment[{$sequence}][reason]"  cols="30" rows="3" required="required">{$segment[$sequence][reason_output]}</textarea><br/></td></tr>
    </table>
    <div id="content_detailsloader_{$sequence}"></div>
    <div id="content_details_{$sequence}">
        {$plansegmentscontent_output}
    </div>
</div>
<div style="vertical-align: top;" id="segment_city_{$sequence}">
    <div id="segment_city_loader_{$sequence}"></div>
    {$cityprofile_output}
    {$citybriefings_output}
</div>
{$seg2helptour}


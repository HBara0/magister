<div style="display:inline-block;width:60%;">
    <table>
        <tr>
        <input type="hidden" value="{$leaveid}" id="lid" name="segment[lid]"/>
        <td width="18%">{$lang->fromdate}</td>
        <td><input type="text" id="pickDate_from_{$sequence}" disabled="disabled" autocomplete="off" tabindex="1" value="{$segment[$sequence][fromDate_output]}" required="required"/>
            <input type="hidden" name="segment[{$sequence}][fromDate]" id="altpickDate_from_{$sequence}" value="{$segment[$sequence][fromDate_formatted]}" /></td>

        <td>{$lang->todate}</td>
        <td><input type="text" id="pickDate_to_{$sequence}"    autocomplete="off" tabindex="2" value="{$segment[$sequence][toDate_output]}" required="required" />
            <input type="hidden" name="segment[{$sequence}][toDate]" id="altpickDate_to_{$sequence}" value="{$segment[$sequence][toDate_formatted]}"/></td>
        <input type="hidden" name="leaveDate" id="leaveDate_to_{$sequence}" value="{$leave[$sequence][toDate]}"/></td>
        <td> <span id="numdays_{$sequence}">{$segment[$sequence][numberdays]}</span></td>
        </tr>

        <tr>  <td>{$lang->origincity}</td>
            <Td><input type="text"  disabled="disabled" autocomplete="off" tabindex="1"  id="cities_{$sequence}_cache_autocomplete"    value="{$segment[$sequence][origincity][name]}" required="required"/></Td>
            <td>
                <input type='hidden' id='cities_{$sequence}_cache_id'   name="segment[{$sequence}][originCity]" value="{$segment[$sequence][origincity][ciid]}"/>
            </td>

            <td>{$lang->destinationcity}</td>
            <td><input type="text" {$disabled}  id="destinationcity_{$sequence}_cache_autocomplete" autocomplete="off" tabindex="1" value="{$segment[$sequence][destinationcity][name]}" required="required"/>
                <input type="hidden" id="coid"  value="{$segment[countryleave]}" name="coid"/>
                <input type='hidden' id='destinationcity_{$sequence}_cache_id'  name="segment[{$sequence}][destinationCity]" value="{$segment[$sequence][destinationcity][ciid]}"/>

            </td>
            <td></td>
        </tr>
    </table>
    <div id="content_detailsloader_{$sequence}"></div>
    <div id="content_details_{$sequence}">
        {$plansegmentscontent_output}
    </div>
</div>
<div style="display:inline-block;width:25%; vertical-align: top;" id="segment_city_{$sequence}">
    <div id="segment_city_loader_{$sequence}"></div>

    {$cityprofile_output}

    {$citybriefings_output}

</div>
<div style="display:inline-block;width:25%; vertical-align: top;" id="segment_transportcity_{$sequence}">
    {$transsegments_output}
</div>

<div style="display:block;width:25%; vertical-align: top;" id="segment_transportcity_{$sequence}">
    {$hotelssegments_output}
</div>


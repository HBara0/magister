<div style="box-shadow: 0px 2px 1px rgba(0, 0, 0, 0.1), 0px 1px 1px rgba(0, 0, 0, 0.1); border: 1px rgba(0, 0, 0, 0.1) solid;display: block;margin-bottom: 16px; padding:4px;width:100%;">
    <div style=" display:inline-block; width:20%;">
        <input type="hidden" value="{$flight[flightdetails]}" name="segment[{$sequence}][tmtcid][{$category[inputChecksum]}][{$flight[flightid]}][transpDetails]"/>
        {$flightnumber_checkbox}
        <input type="hidden" name="segment[{$sequence}][tmtcid][{$category[inputChecksum]}][{$flight[flightid]}][fare]" value="{$flight[pricing]}" />
        <span class="subtitle"><strong>{$flight[pricing]}USD</strong></span>
        <div style="padding:6px;" class='smalltext'><em>{$cheapest}</em></div>
        <div style="padding:4px;" class='smalltext'><em>{$flight[triptype]}</em></div>
    </div>
    <div style="display:inline-block; width:75%; vertical-align: top;">
        {$flights_records_roundtripsegments_details}
    </div>
</div>

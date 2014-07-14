<div style="box-shadow: 0px 2px 1px rgba(0, 0, 0, 0.1), 0px 1px 1px rgba(0, 0, 0, 0.1); border: 1px  rgba(0, 0, 0, 0.1) solid;display: block;margin: 8px; padding:4px;width:100%;">
    <div style=" display:inline-block; width:30%;">
        <input type="hidden" value="{$flight[flightdetails]}" name="segment[{$sequence}][tmtcid][{$transpcatid}][flightDetails]"/>

        <input type="checkbox" name="segment[{$sequence}][tmtcid][{$transpcatid}][flightNumber]" value="{$flight[flightnumber]}"/>
        <span class="subtitle"><strong> $ {$flight[pricing]}</strong></span>
        <div style="padding:4px;"><em> {$flight[triptype]}</em></div>
    </div>

    <div style="display:inline-block; width:45%; vertical-align: top;">
        {$flights_records_roundtripsegments_details}
    </div>

</div>

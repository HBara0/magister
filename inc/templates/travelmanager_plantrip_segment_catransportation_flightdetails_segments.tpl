<div style="box-shadow: 0px 2px 1px rgba(0, 0, 0, 0.1), 0px 1px 1px rgba(0, 0, 0, 0.1); border: 1px  rgba(0, 0, 0, 0.1) solid;display: block;margin: 8px; padding:7px;width:70%;">

    <div style=" display:inline-block;padding:4px; vertical-align: top;" class="green_text"><input type="checkbox" name="segment[{$sequence}][tmtcid][flightnumber]" value="{$flight[flightnumber]}"/>
        <input type="hidden" value="{$flight[flightdetails]}" name="segment[{$sequence}][tmtcid][flightDetails]"/>
    </div>
    <div style="display:inline-block;padding:4px; vertical-align: top;" class="green_text"><strong> $ {$flight[pricing]}</strong></div>

    <div style=" display:inline-block; padding:4px; vertical-align: top;"> {$flight[departuretime]} - {$flight[arrivaltime]} <br/> <span class="smalltext"> {$flight[carrier]} </span><span class="smalltext">{$flight[flightnumber]}</span></div>
    <div style=" display:inline-block;padding:4px; vertical-align: top;">{$flight[duration]}</div>
    <div style=" display:inline-block;padding:4px; vertical-align: top;" class="smalltext">{$flight[triptype]}</div>


</div>

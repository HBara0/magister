<div style="display:block;" >
    <div class="subtitle">Possible Transportations</div>
    <div class="ui-state-highlight ui-corner-all" style="padding: 6px; font-weight: bold;"><a href="{$transpmode_apimaplink}" target="_blank">Visualize Tranpostation Possibilities on Map</a></div>
    {$transsegments_output}
</div>
<div style="display:block;" id="segment_hotels_{$sequence}">
    <div class="subtitle">Approved Hotels</div>
    {$hotelssegments_output}
</div>


<div style="display:block; width: 100%;" id="segment_expenses_{$sequence}">
    <input name="sequence" type="hidden" id="sequence" value="{$sequence}">
    <div class="subtitle" style="padding:8px;width:40%;">{$lang->addexp}</div>

    {$segments_expenses_output}

    <span> <img src="./images/add.gif"  id="ajaxaddmore_travelmanager/plantrip_expenses_{$sequence}"  alt="{$lang->add}">
        <input name="numrows_expenses{$sequence}" type="text" id="numrows_expenses{$sequence}" value="{$rowid}">

    </span>

</div>

<div style="width:70%; display: inline-block; vertical-align: top;"><span class="subtitle">{$transportation->transpType}</span>
    <div style="font-size: 10px; font-style: italic;">
        {$transportation->vehicleNumber} {$transportation->isRoundTrip_output} {$transportation->get_traspclass()->get_displayname()}<br />
        {$lang->seatingdescription}: {$transportation->seatingDescription}<br />
        {$lang->stopdescription}: {$transportation->stopDescription}
    </div>
</div>
<div style="width:25%;display:inline-block; font-size:14px; font-weight:bold;text-align:right;vertical-align:top;">{$fare}<br/><small style="font-weight:normal;"> [paid by : {$paidby}]</small> </div>
{$connectionduration}
{$flight_details}
{$warnings[transpclass]}
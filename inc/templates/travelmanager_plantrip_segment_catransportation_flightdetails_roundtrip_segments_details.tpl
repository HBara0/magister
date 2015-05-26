<div style="margin-bottom:5px;">
    <div>
        <div style="display: inline-block; width: 75%; padding:2px; font-weight: bold;"><span title='{$flight[departuredate]} {$flight[departuretime]} {$flight[departuretimezone]}'>{$flight[departuredate]} {$flight[departuretime]}</span> - <span title='{$flight[arrivaldate]} {$flight[arrivaltime]} {$flight[arrivaltimezone]}'>{$flight[arrivaltime]}</span></div><div style="display: inline-block;">{$flight[duration]}</div>
    </div>
    <div style="padding:2px;">
        <div>{$flight[origin]} - {$flight[destination]}</div>
        <div style="vertical-align: middle;"><div style="display: inline-block;"><img src='https://www.gstatic.com/flights/airline_logos/70px/{$segment->flight->carrier}.png' style="vertical-align: middle; "width="20" height="20" alt=""></div><div class="smalltext" style='color: #CCC; display: inline-block; line-height: 20px;'>&nbsp;{$flight[carrier]} {$flight[flightnumber]} | {$lang->class} {$flight[cabin]}</div></div>
    </div>
    {$connectionduration}
</div>
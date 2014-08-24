<div style="margin-bottom:5px;">
    <div>
        <div style="display: inline-block; width: 75%; padding:2px; font-weight: bold;"><span title='{$flight[departuredate]} {$flight[departuretime]}'>{$flight[departuredate]} {$flight[departuretime]}</span> - <span title='{$flight[arrivaldate]} {$flight[arrivaltime]}'>{$flight[arrivaltime]}</span></div><div style="display: inline-block;">{$flight[duration]}</div>
    </div>
    <div style="padding:2px;">
        <div>{$flight[origin]} - {$flight[destination]}</div>
        <div style="vertical-align: middle;"><div style="display: inline-block;"><img src='#' style="vertical-align: middle; "width="20" height="20"></div><div class="smalltext" style='color: #CCC; display: inline-block; line-height: 20px;'> · {$flight[carrier]} {$flight[flightnumber]}</div></div>
    </div>
    {$connectionduration}
</div>
<!--https://www.gstatic.com/flights/airline_logos/70px/{$segment->flight->carrier}.png-->
<h1>{$lang->hotelslist}</h1>
<form name="perform_travelmanager/hotelslist_Form" id="perform_travelmanager/hotelslist_Form" action="#" method="post">
    <table class="datatable" width="100%">
        <thead>
            <tr>
                <th style="width:25%;">{$lang->name} </th>
                <th style="width:20%;">{$lang->city}</th>
                <th style="width:20%;">{$lang->country}</th>
                <th style="width:15%;">{$lang->isapproved}</th>
                <th style="width:20%;">{$lang->avgprice}</th>
            </tr>
            <tr>
                {$filters_row}
            </tr>
        </thead>
        <tbody>
            {$hotel_rows}
        </tbody>
    </table>
</form>
<div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>

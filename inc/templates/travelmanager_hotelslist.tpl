<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->hotelslist}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->hotelslist}</h1>
            <form name="perform_travelmanager/hotelslist_Form" id="perform_travelmanager/hotelslist_Form" action="#" method="post">
                <table class="datatable" width="100%">
                    <thead>
                        <tr>
                            <th style="width:25%;">{$lang->name} </th>
                            <th style="width:20%;">{$lang->city}</th>
                            <th style="width:20%;">{$lang->country}</th>
                            <th style="width:15%;">{$lang->isapproved}<a href="{$sort_url}&amp;sortby=isApproved&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=isApproved&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
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
        </td>
    </tr>
    {$footer}
</body>
</html>
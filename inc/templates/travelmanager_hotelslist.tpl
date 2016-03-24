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
                <div style="float: right;{$hidecreate}"><a href="{$core->settings['rootdir']}/index.php?module=travelmanager/edithotel" target="_blank" class="btn btn-success"><label style="color: white"><strong>{$lang->createhotel}</strong></label></a></div>
                <table class="datatable_basic  table table-bordered row-border hover order-column" data-skipfilter="true" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th >{$lang->name} </th>
                            <th >{$lang->city}</th>
                            <th >{$lang->country}</th>
                            <th >{$lang->phone}</th>
                            <th >{$lang->address}</th>
                            <th >{$lang->avgprice}</th>
                            <th >{$lang->isapproved}</th>
                            <th ></th>

                        </tr>
                    </thead>
                    <thead>
                        <tr>{$filters_row}</tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th >{$lang->name} </th>
                            <th >{$lang->city}</th>
                            <th >{$lang->country}</th>
                            <th >{$lang->phone}</th>
                            <th >{$lang->address}</th>
                            <th >{$lang->avgprice}</th>
                            <th >{$lang->isapproved}</th>
                            <th ></th>
                        </tr>
                    </tfoot>
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
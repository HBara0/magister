<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->warehouseslist}</title>
        {$headerinc}
    </head>
    <body>
        {$header}

    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->warehouseslist}</h3>
            <form action='index.php?module=cms/eventlist' method="post">
                <table class="datatable" width="100%">
                    <thead>
                        <tr>
                            <th width="20%">{$lang->affiliate}</th>
                            <th width="20%">{$lang->name}</th>
                            <th width="20%">{$lang->city}</th>
                            <th width="20%">{$lang->country}</th>
                            <th width="20"></th>

                        </tr>
                    </thead>
                </table>
                <table class="datatable" width="100%">

                    <tbody>
                        {$warehouse_rows}
                    </tbody>
                </table>
            </form>
            <div style="width:40%; float:left; margin-top:20px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
                </form></div>

        </td>
    </tr>

    {$footer}
</body>

</html>
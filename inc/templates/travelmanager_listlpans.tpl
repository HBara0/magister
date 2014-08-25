<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->listplans}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->listplans}</h3>
            <table width="100%" class="datatable">
                <thead>
                    <tr>
                        <th width="30%">{$lang->plan}</th>
                        <th width="30%">{$lang->employee}</th>
                        <th width="40%">{$lang->createdon}</th>
                        <th width="1%">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    {$plan_rows}
                </tbody>
            </table>
            <div style="width:40%; float:left; margin-top:20px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>
        </td>
    </tr>
    {$footer}
</body>
</html>
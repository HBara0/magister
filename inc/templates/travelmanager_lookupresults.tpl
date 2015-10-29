<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->lookupresults}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->lookupresults}</h1>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td valign="top" style="width:50%; padding:10px;"><div class="subtitle">{$lang->flightslist}</div></td>
                    <td valign="top" style="width:50%; padding:10px;"><div class="subtitle">{$lang->hotelslist}</div></td>
                </tr>
                <tr>
                    <td>{$flight_row}</td>
                    <td>{$lang->nomatchfound}</td>
                </tr>
            </table>
        </td>
    </tr>
    {$footer}
</body>
</html>
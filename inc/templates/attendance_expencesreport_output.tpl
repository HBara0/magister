<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->fillmontlyreport}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->mireport}</h3>
            <table width="100%" class="datatable">
                <tr>
                    <th>{$lang->dimensions}</th>
                        {$dimension_head}
                </tr>
                {$parsed_dimension} 
            </table>
        </td>
    </tr>
    {$footer}
</body>
</html>
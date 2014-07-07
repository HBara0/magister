<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->expensesreport}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->mireport}</h1>
            <table width="100%" class="datatable">
                <tr class="thead">
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
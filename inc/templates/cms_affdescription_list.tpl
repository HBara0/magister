<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->affdesclist}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->affdesclist}</h3>
            <table class="datatable" width="100%">
                <thead>
                    <tr>
                        <th>{$lang->affiliate}</th>
                        <th>{$lang->description}</th>
                    </tr>
                    {$filters_row}
                </thead>

                <tbody>
                    {$cms_affdesc_rows}
                </tbody>
            </table>
        </td>
    </tr>

    {$footer}
</body>
</html>
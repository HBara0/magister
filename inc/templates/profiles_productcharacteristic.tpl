<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$characteristic['title']}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$characteristic['title']}</h1>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                {$values_list}
            </table>
        </td>
    </tr>
    {$footer}
</body>
</html>
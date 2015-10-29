<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$page[title]}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            {$stockreportpage[content]}
            {$stockpermonthofsale_output}
        </td>
    </tr>
</body>
</html>
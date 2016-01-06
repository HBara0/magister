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
            {$page[content]}
            {$page_ouptput}
        </td>
    </tr>
</body>
</html>
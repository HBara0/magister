<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$reporttitle}</title>
        {$headerinc}
        <link href="report.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <div align="center">{$reports}</div>
            <div align="right" style="margin-top: 20px;">{$tools}</div>
        </td>
    </tr>
    {$footer}
</body>
</html>
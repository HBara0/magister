<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$page[title]}</title>
        {$headerinc}
        <script type="text/javascript" src="{$core->settings[rootdir]}/js/tableExport.min.js"></script>
        <script type="text/javascript" src="{$core->settings[rootdir]}/js/jquery.base64.min.js"></script>
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
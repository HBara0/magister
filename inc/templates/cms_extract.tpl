<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->events}</title>
        {$headerinc}
        <script>
            $(function () {
                $('button[id^="extract_"]').live('click', function () {
                    var id = $(this).attr('id');
                    sharedFunctions.requestAjax("post", "index.php?module=cms/extractpages&action=" + id, '', 'results', 'results', 'html', true);
                });
            });
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->extractdatatools}</h3>
            <div style="display: inline-block"><button id="extract_segments" class="button">{$lang->extractsegmentscontent}</button></div>
            <div style="display: inline-block"><button id="extract_cmspages" class="button">{$lang->extractpagescontents}</button></div>
            <br>
            <div id="results"></div>
        </td>
    </tr>

    {$footer}
</body>

</html>
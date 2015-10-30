<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->events}</title>
        {$headerinc}
        <script>
            $(function () {
                $(document).on('click', 'button[id^="show_"]', function () {
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
            <div style="display: inline-block">
                <div style="display: inline-block"><a href="{$core->rootdir['settings']}index.php?module=cms/extractpages&extract=segments"><button class="button">{$lang->extractsegmentscontent}</button></a></div>
                <div style="display: inline-block"><a href="{$core->rootdir['settings']}index.php?module=cms/extractpages&extract=pages"><button class="button">{$lang->extractpagescontents}</button></a></div>
                <br>
            </div>
            <hr style="visibility:hidden;" />
            <div style="display: inline-block;">
                <div style="display: inline-block"><button id="show_segments" class="button">{$lang->showsegmentscontent}</button></div>
                <div style="display: inline-block"><button id="show_cmspages" class="button">{$lang->showpagescontents}</button></div>
            </div>
            <div id="results"></div>
        </td>
    </tr>

    {$footer}
</body>

</html>
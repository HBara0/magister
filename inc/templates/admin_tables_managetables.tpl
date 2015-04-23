<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->managetable}</title>
        {$headerinc}
        <script>
            $(function () {
                $('input[id="restricted"]').bind('keypress', function (event) {
                    var regex = new RegExp("^[a-zA-Z0-9_]+$");
                    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                    if(!regex.test(key)) {
                        event.preventDefault();
                        return false;
                    }
                });
                $('button[id="save_createclass"]').live('click', function () {
                    $('input[id^="perform_"]').click();
                    var stid = $('input[id="stid"]').val();
                    setTimeout((sharedFunctions.requestAjax("post", "index.php?module=tablesdefinition/managetables&action=createclass", "&stid=" + stid, true)), 2000);
                });
            });
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer" colspan="2">
            <h1>{$page_title}</h1>
            <a href='{$core->settings['rootdir']}/manage/index.php?module=tablesdefinition/managetables&type=showtabledata&d$@1á={$core->input['d$@1á']}'><button style="float: right" id='showtablcols' class="button">{$lang->gettablecolumns}</button></a>
                {$table_main}
        </td>
    </tr>
    {$footer}
</html>
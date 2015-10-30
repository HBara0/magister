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
                $(document).on('click', 'button[id="save_createclass"]', function () {
                    $('input[id^="perform_"]').click();
                    var classdef = '';
                    var classfunc = '';
                    var overwrite = '';
                    var stid = $('input[id="stid"]').val();
                    if($('input[id="classdef"]').is(':checked')) {
                        var classdef = "&classdef=1";
                    }
                    ;
                    if($('input[id="classfunc"]').is(':checked')) {
                        var classfunc = "&classfunc=1";
                    }
                    ;
                    if($('input[id="overwrite"]').is(':checked')) {
                        var overwrite = "&overwrite=1";
                    }
                    ;
                    setTimeout((sharedFunctions.requestAjax("post", "index.php?module=managesystem/managetables&action=createclass", "&stid=" + stid + classdef + classfunc + overwrite, true)), 2000);
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
            <a href='{$core->settings['rootdir']}/manage/index.php?module=managesystem/managetables&type=showtabledata&d$@1á={$core->input['d$@1á']}'><button style="float: right" id='showtablcols' class="button">{$lang->gettablecolumns}</button></a>
                {$table_main}
        </td>
    </tr>
    {$footer}
    {$filexists}
</html>
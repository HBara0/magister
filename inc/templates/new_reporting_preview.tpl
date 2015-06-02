<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$report[title]}</title>
        {$headerinc}

        <link href="{$core->settings[rootdir]}/css/rateit.min.css" rel="stylesheet" type="text/css">
        <link href="./css/report.css" rel="stylesheet" type="text/css" />

        <script src="{$core->settings[rootdir]}/js/jquery.rateit.min.js" type="text/javascript"></script>
        <script src="{$core->settings[rootdir]}/js/fillreport.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $(window).scroll(function() {
                    if($(this).scrollTop() > $('#tableofcontent').offset().top) {
                        $('.scrollup').fadeIn();
                    } else {
                        $('.scrollup').fadeOut();
                    }
                });

                $('.scrollup').click(function() {
                    $("html, body").animate({scrollTop: $('#tableofcontent').offset().top}, 300);
                    return false;
                });
            });
        </script>
    </head>

    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <form id="save_report_reporting/fillreport_Button" name="save_report_reporting/fillreport_Button" action="#" method="post">
                <input type="hidden" name="reportdata" value="{$reportrawdata}">
            </form>
            <div align="center">
                {$reports}
            </div>
            <div align="center">{$reportingeditsummary}</div>
            <div align="right">{$tools}</div>
            <span><a href="#tableofcontent" class="scrollup" title="{$lang->clicktoscroll}"></a></span>
        </td>
    </tr>
    {$footer}
</body>
</html>
<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$report[quartername]}</title>
        {$headerinc}
        <link href="./css/report.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript">
            $(document).ready(function() {
                $(window).scroll(function() {
                    if ($(this).scrollTop() > $('#tableofcontent').offset().top) {
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
        {$reports}
        <span><a href="#tableofcontent" class="scrollup" title="{$lang->clicktoscroll}"></a></span>
    </body>
</html>
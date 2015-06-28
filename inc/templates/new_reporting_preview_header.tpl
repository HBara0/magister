{$headerinc}
<link href="{$core->settings[rootdir]}/css/rateit.min.css" rel="stylesheet" type="text/css">
<link href="./css/report.css" rel="stylesheet" type="text/css" />

<script src="{$core->settings[rootdir]}/js/jquery.rateit.min.js" type="text/javascript"></script>
<script src="{$core->settings[rootdir]}/js/fillreport.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $(window).scroll(function () {
            if($(this).scrollTop() > $('#tableofcontent').offset().top) {
                $('.scrollup').fadeIn();
            } else {
                $('.scrollup').fadeOut();
            }
        });

        $('.scrollup').click(function () {
            $("html, body").animate({scrollTop: $('#tableofcontent').offset().top}, 300);
            return false;
        });
    {$header_ratingjs}
    });
</script>
<form id="save_report_reporting/fillreport_Button" name="save_report_reporting/fillreport_Button" action="#" method="post">
    <input type="hidden" name="reportdata" value="{$reportrawdata}">
    <input type="hidden" id="transfill" name="transfill" value="{$transfill}">

</form>
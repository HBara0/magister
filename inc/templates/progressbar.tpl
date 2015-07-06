<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="/resources/demos/style.css">
<script>
    $(function () {
        $("#progressbar").progressbar({
            value: {$value}
        });
        $("#progressbar").find(".ui-progressbar-value").css({
            "background": '#006600'
        });
        $("#progresslabel").text('tesssst');
    });
</script>
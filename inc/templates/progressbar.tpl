
<script>
    $(function () {
        $("#progressbar{$task_barid}").progressbar({
            value: {$value}
        });
        $("#progressbar{$task_barid}").find(".ui-progressbar-value").css({
            "background": '#006600'
        });
    });
</script>
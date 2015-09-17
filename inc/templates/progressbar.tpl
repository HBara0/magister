<script>
    $(function () {
        $("#progressbar{$task_barid}").progressbar({value: {$value}});
        $("#progressbar{$task_barid}").find(".ui-progressbar-value").css({"background": '#91b64f'});
        $("#caption{$task_barid}").html('<b>{$value}%</b>').css({'font-size': '9px;','color': '#666'});
    });
</script>
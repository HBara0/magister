<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->viewplan}</title>
        {$headerinc}
        <script type="text/javascript">

            $(function () {
                $('input[id="confirm_finalize"]').live('click', function () {
                    $('input[type="submit"][id^="perform_travelmanager/viewplan_Button"]').attr("disabled", !this.checked);
                });
                $('button[id="closepage"]').live('click', function () {
                    window.close();
                });
            });

        </script>
    </head>

    <body>
        {$header}
    <tr>
        {$menu}
        {$content}

    </tr>
    {$footer}
</body>
</html>
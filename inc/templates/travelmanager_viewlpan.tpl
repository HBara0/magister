<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->viewplan}</title>
        {$headerinc}
        <script type="text/javascript">

            $(function () {
                $(document).on('click', 'input[id="confirm_finalize"]', function () {
                    $('input[type="submit"][id^="perform_travelmanager/viewplan_Button"]').attr("disabled", !this.checked);
                });
                $(document).on('click', 'button[id="closepage"]', function () {
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
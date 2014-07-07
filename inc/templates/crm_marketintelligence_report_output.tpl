<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->fillmontlyreport}</title>
        {$headerinc}
        <style>
            .marketreport_dimension {
                background-color: red;

            }
        </style>
        <script>
            $(function() {

                $('tr[id^="dimension_"]').mouseover(function() {
                    //     $(this).toggleClass('mainmenuitem_hover')
                });

            });

        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->mireport}</h1>
            <table width="100%" class="datatable">
                <tr class="thead">
                    <th>{$lang->dimension}</th>
                        {$dimension_head}
                </tr>
                {$parsed_dimension}
            </table>
        </td>
    </tr>
    {$footer}
</body>

</html>


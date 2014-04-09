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
                    $(this).toggleClass('mainmenuitem_hover') 
                });

            });

        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->mireport}</h3>
            <table width="100%" class="datatable">
                <tr>
                    {$dimension_head}
                </tr>
                {$parsed_dimension} 

            </table>

        </td>
    </tr>        
</body>

</html>


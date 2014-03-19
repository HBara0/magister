<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->fillmontlyreport}</title>
        {$headerinc}
        <script>
            $(function() {

                 $('tr[id^="dimension_"]').mouseover(function() {
                   // $(this).toggleClass('mainmenuitem_hover') 
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
                <th>dimension</th>
                    {$dimension_head}
                    {$parsed_dimension} 


            </table>

        </td>
    </tr>        
</body>

</html>


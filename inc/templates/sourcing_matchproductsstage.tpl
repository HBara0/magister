<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->matchintegrationdata}</title>
        {$headerinc}
    </head>
    <body>
        {$header}  


    <tr>
        <td class="menuContainer"></td>
        <td class="contentContainer">
            <h3>{$matchinproducts}</h3>



            <form name="perform_sourcing/matchproduct_Form" action="index.php?module=sourcing/matchproducts" method="post"  id="perform_sourcing/matchproduct_Form">

                <div align="center">
                    <table width="100%" class="datatable">
                        <thead>
                        <th width="40%"> <h3>Matchitem</h3>{$lang->matchitem}</th><th>&nbsp;</th><th width="40%"><h3>Matchwith</h3>{$lang->matchwith}</th>
                 
                        </thead>
                        <tbody>
                            {$matchproduct}
                        </tbody>
                    </table>

                </div>
                <input class="rounded" type="hidden" value="do_match" name="action" id="action" />
                <input type="submit" class="button" value="import" id="perform_sourcing/matchproducts_Button" />

            </form>
        </td>


    </tr>

</body>
</html>

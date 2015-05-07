<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$page_title}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$page_title_header}<small><br />{$customername}</small></h1>
                    {$clone_button}
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                {$endproducts_list}
                {$chemsubstance_list}
                {$products_list}
                {$ingredients_list}
            </table>
        </td>
    </tr>
    {$footer}
    {$pop_clone}
</body>
</html>
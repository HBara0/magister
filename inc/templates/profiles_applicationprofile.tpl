<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$application['title']}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$application['title']}<small><br />{$application['prodsegtitle']}</small></h1>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    {$application_funtionalproperties_list}
                </tr>
                <tr>
                    {$application_products_list}
                </tr>
                <tr>
                    {$application_chemicalsubst_list}
                </tr>
                <tr>
                    {$application_endproducttype_list}
                </tr>
                <tr>
                    {$application_supplier_list}
                </tr>
                <tr>{$application_brand_list}</tr>
            </table>
        </td>
    </tr>
    {$footer}
</body>
</html>
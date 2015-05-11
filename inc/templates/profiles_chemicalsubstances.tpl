<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$chemsub['name']}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$chemsub['name']}</h1>
            <small>
                <h2>{$lang->casnum}: {$chemsub['casNum']}</h2>
                <h2>{$lang->synonym}: </h2>  {$chemsub['synonyms']}

            </small>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                {$endproducts_list}
                {$products_list}
                {$funcprop_list}
                {$app_list }
                {$supplier_list}
                {$customer_list}
                {$possible_supp_list}


            </table>
        </td>
    </tr>
    {$footer}
</body>
</html>
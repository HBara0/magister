<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$product['name']}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$product['name']}</h1>
            <small>
                <h2>{$lang->supplier}: {$supplier_name}</h2>
                <h2>{$lang->defaultfunct}: {$defaultfunc}</h2>
                <h2>{$lang->description}: {$product['description']}</h2>

            </small>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                {$funcprop_list}
                {$chemsub_list}
                {$brand_list}
                {$customer_list}
            </table>
        </td>
    </tr>
    {$footer}
</body>
</html>
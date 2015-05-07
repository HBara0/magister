<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$page_title}</title>
        {$headerinc}
        <script>
            $(function () {
                $('#brands_1_id_output').live('change', function () {
                    if($('#brands_1_id_output').val() > 0) {
                        $('input[id="customer_1_autocomplete"]').attr('disabled', 'disabled');
                        $('input[id="customer_1_id"]').val('0');
                        $('input[id="customer_1_autocomplete"]').val('');
                    }
                    else {
                        $('input[id="customer_1_autocomplete"]').removeAttr('disabled');
                    }
                });
                $('input[id ="brands_1_autocomplete"]').live('change', function () {
                    if($('#brands_1_id_output').val() > 0) {
                        $('input[id="customer_1_autocomplete"]').attr('disabled', 'disabled');
                        $('input[id="customer_1_id"]').val('0');
                        $('input[id="customer_1_autocomplete"]').val('');
                    }
                    else {
                        $('input[id="customer_1_autocomplete"]').removeAttr('disabled');
                    }
                });
            });
        </script>
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
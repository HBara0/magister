<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->customerslist}</title>
        {$headerinc}
        <script type="text/javascript" src="{$core->settings[rootdir]}/js/tableExport.min.js"></script>
        <script type="text/javascript" src="{$core->settings[rootdir]}/js/jquery.base64.min.js"></script>

    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->customerslist}</h1>
            <table class="datatable_basic table table-bordered row-border hover order-column" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>{$lang->companyname}</th>
                        <th>{$lang->affiliate}</th>
                        <th>{$lang->segment}</th>
                        <th>{$lang->entitytype}</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>{$lang->companyname}</th>
                        <th>{$lang->affiliate}</th>
                        <th>{$lang->segment}</th>
                        <th>{$lang->entitytype}</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
                <tbody>
                    {$customers_list}
                </tbody>
            </table>
            <div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>
            <div style=" float:right;">{$toolgenerate}</div>
        </td>
    </tr>
    {$footer}
</body>
</html>
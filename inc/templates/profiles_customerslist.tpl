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
            <form method='post' action='$_SERVER[REQUEST_URI]'>
                <table id="tabletoexport" class="datatable">
                    <thead>
                        <tr>
                            <th style="width:32%;">{$lang->companyname} <a href="{$sort_url}&amp;sortby=customername&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=customername&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
                            <th style="width:32%;">{$lang->affiliate}</th>
                            <th style="width:30%;">{$lang->segment}</th>
                            <th>{$lang->entitytype}</th>
                            <th>&nbsp;</th>
                        </tr>
                        {$filters_row}
                    </thead>
                    <tbody>
                        {$customers_list}

                    </tbody>
                </table>
            </form>
            <div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>
            <div style=" float:right;">{$toolgenerate}</div>
        </td>
    </tr>
    {$footer}
</body>
</html>
<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->supplierslist}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->supplierslist}</h3>
            <form method='post' action='$_SERVER[REQUEST_URI]'>
                <table class="datatable" width="100%">
                    <thead>
                        <tr>
                            <th style="width:30%;">{$lang->companyname} <a href="{$sort_url}&amp;sortby=entityname&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=entityname&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
                            <th style="width:35%;">{$lang->affiliate}</th>
                            <th style="width:35%;">{$lang->segment}</th>
                        </tr>
                        {$filters_row}
                    </thead>
                </table>
            </form>
            <table class="datatable" width="100%">
                <thead><tr class="dummytrow"><th style="width:30%;"></th><th style="width:35%;"></th><th style="width:35%;"></th></tr></thead>
                <tbody>
                    {$suppliers_list}
                </tbody>
            </table>
            <div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>
        </td>
    </tr>
    {$footer}
</body>
</html>
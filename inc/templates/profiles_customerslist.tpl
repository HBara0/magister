<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->customerslist}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->customerslist}</h3>
            <table class="datatable">
                <thead>
                    <tr>
                        <th style="width:32%;">{$lang->companyname} <a href="{$sort_url}&amp;sortby=customername&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=customername&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
                        <th style="width:32%;">{$lang->affiliate}</th>
                        <th style="width:33%;">{$lang->segment}</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    {$customers_list}
                </tbody>
            </table>
            <div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>
            <div style="width:40%; display:inline-block; text-align:right; float:right" align="center">
                <form method='post' action='$_SERVER[REQUEST_URI]'>
                    <input type="hidden" name="filterby" id="filterby" value='name'/><input type="text" name="filtervalue" id="filtervalue"> <input type="submit" class="button" value="{$lang->filter}">
                </form>
            </div>
        </td>
    </tr>
    {$footer}
</body>
</html>
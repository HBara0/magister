<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->affiliateslist}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <div style="width:40%; display:inline-block;"><h1>{$lang->affiliateslist}</h1></div><div style="float:right; display:inline-block; width:40%; text-align:right;"><br /><a href="index.php?module=profiles/affiliateslist&view={$switchview[link]}"><img src="./images/icons/{$switchview[icon]}" alt="{$lang->changeview}" border="0"></a></div>
            <table class="datatable" style="display:{$datatable_display};">
                <thead>
                    <tr>
                        <th style="width:25%;">{$lang->name} 
                            <a href="{$sort_url}&amp;sortby=name&amp;order=ASC">
                                <img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/>
                            </a>
                            <a href="{$sort_url}&amp;sortby=name&amp;order=DESC">
                                <img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/>
                            </a>
                        </th>
                        <th style="width:25%;">{$lang->territory}</th>
                        <th style="width:25%;">{$lang->gm}</th>
                        <th style="width:25%;">{$lang->supervisor}</th>
                    </tr>
                </thead>
                <tbody>
                    {$affiliates_list}
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"><div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div></td>
                    </tr>
                </tfoot>
            </table>
            <div align="center">{$map_view}</div>
        </td>
    </tr>
    {$footer}
</body>
</html>
<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->brandslist}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->brandslist}Brands List</h1>
            <form method='post' action='$_SERVER[REQUEST_URI]'>
                <table class="datatable" style="display:{$datatable_display};">
                    <thead>
                        <tr>
                            <th style="width:25%;">{$lang->brand}
                                <a href="{$sort_url}&amp;sortby=brandname&amp;order=ASC">
                                    <img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/>
                                </a>
                                <a href="{$sort_url}&amp;sortby=brandname&amp;order=DESC">
                                    <img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/>
                                </a>
                            </th>
                            <th style="width:25%;">{$lang->customers}</th>
                            <th style="width:25%;">{$lang->country}</th>
                            <th style="width:25%;">{$lang->endproduct}</th>
                        </tr>
                        {$filters_row}
                    </thead>
                    <tbody>
                        {$brands_list}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4"><div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div></td>
                        </tr>
                    </tfoot>
                </table>
            </form>
        </td>
    </tr>
    {$footer}
</body>
</html>
<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->meetings}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->meetings}</h1>
            <table class="datatable" width="100%">
                <thead>
                    <tr>
                    <tr>
                        <th style="width: 20%;">{$lang->title} <a href="{$sort_url}&amp;sortby=title&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=title&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                        <th style="width: 30%;"> </th>
                        <th style="width: 30%;">{$lang->description}</th>
                        <th style="width: 10%;">{$lang->fromdate} <a href="{$sort_url}&amp;sortby=fromDate&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=fromDate&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                        <th style="width: 10%;">{$lang->todate} <a href="{$sort_url}&amp;sortby=toDate&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=toDate&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                        <th style="width: 20%;">{$lang->location} <a href="{$sort_url}&amp;sortby=location&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=location&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                        <th style="width: 10%;">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    {$meeting_list_row}
                </tbody>
            </table>
            <div style="width:40%; float:left; margin-top:0px;">
                <form method='post' action='$_SERVER[REQUEST_URI]'>
                    {$lang->perlist}:
                    <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
                </form>
            </div>
        </td>
    </tr>
</body>
</html>
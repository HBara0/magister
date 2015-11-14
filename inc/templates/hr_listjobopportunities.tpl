<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->listjobopportunities}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->listjobopportunities}</h1>
            <table class="datatable" width="100%">
                <thead>
                    <tr>
                        <th width="30%">{$lang->title} <a href="{$sort_url}&amp;sortby=title&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=title&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                        <th width="15%">{$lang->ispublished}<a href="{$sort_url}&amp;sortby=isPublished&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=isPublished&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                        <th width="15%">{$lang->applicants}<a href="{$sort_url}&amp;sortby=applicants&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=applicants&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th></th>
                        <th width="15%">{$lang->views}<a href="{$sort_url}&amp;sortby=views&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=views&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th></th>
                        <th width="15%">{$lang->createdon}<a href="{$sort_url}&amp;sortby=createdOn&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=createdOn&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th></th>
                    </tr>
                    {$filters_row}
                </thead>
                <tbody>
                    {$hr_listjobopportunities_rows}
                </tbody>
            </table>
        </td>
    </tr>
    {$footer}
</body>
</html>
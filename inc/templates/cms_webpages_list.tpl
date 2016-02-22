<h1>{$lang->listwebpages}</h1>
<form method='post' action='$_SERVER[REQUEST_URI]'>
    <table class="datatable" width="100%">
        <thead>
            <tr>
                <th width="20%">{$lang->title} <a href="{$sort_url}&amp;sortby=title&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=title&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                <th>{$lang->version}<a href="{$sort_url}&amp;sortby=version&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=version&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                <th>{$lang->published}</th>
                <th>{$lang->lang}</th>
                <th>{$lang->author}<a href="{$sort_url}&amp;sortby=creator&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=creator&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                <th>{$lang->date} <a href="{$sort_url}&amp;sortby=dateCreated&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=dateCreated&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                <th>{$lang->hits}<a href="{$sort_url}&amp;sortby=hits&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=hits&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
            </tr>
            {$filters_row}
        </thead>
    </table>
</form>
<table class="datatable" width="100%">
    <tbody>
        {$cms_pages_list_rows}
    </tbody>
</table>
<div style="width:40%; float:left; margin-top:20px;" class="smalltext">
    <form method='post' action='$_SERVER[REQUEST_URI]'>
        {$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
    </form>
</div>
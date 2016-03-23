<h1>{$lang->sharedfileslist}</h1>
<table class="datatable">
    <thead>
        <tr>
            <th style="width:2%;"></th>
            <th style="width:20%;">{$lang->title}<a href="{$sort_url}&amp;sortby=titlename&amp;order=ASC"><img src="{$core->settings[rootdir]}/images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=titlename&amp;order=DESC"><img src="{$core->settings[rootdir]}/images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
            <th style="width:30%;">{$lang->description}</th>
            <th style="width:15%;">{$lang->date}<a href="{$sort_url}&amp;sortby=timeLine&amp;order=ASC"><img src="{$core->settings[rootdir]}/images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=timeLine&amp;order=DESC"><img src="{$core->settings[rootdir]}/images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
            <th style="width:15%;">{$categories_list}</th>
            <th style="width:8%;">{$lang->size}<a href="{$sort_url}&amp;sortby=size&amp;order=ASC"><img src="{$core->settings[rootdir]}/images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=size&amp;order=DESC"><img src="{$core->settings[rootdir]}/images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
            <th style="width:6%;"><a href="{$change_view_url}"><img src="./images/icons/{$change_view_icon}" alt="{$lang->changeview}" border="0"/></a></th>
        </tr>
    </thead>
    <tbody>
        {$files_list}
    </tbody>
</table>
<div><form method='post' action='$_SERVER[REQUEST_URI]'>
        {$lang->perlist} <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>

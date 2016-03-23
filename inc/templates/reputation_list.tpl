<h1>{$lang->reputationslist}</h1>
<table class="datatable" width="100%">
    <thead>
        <tr>
            <th style="width:50%;">{$lang->title} <a href="{$sort_url}&amp;sortby=title&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=title&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
            <th style="width:45%;">{$lang->description}</th>
            <th style="width:5%;">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        {$reputations_list}
    </tbody>
</table>
<div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>

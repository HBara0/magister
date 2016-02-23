<h1>{$lang->listchemcialsrequests}</h1>
<form  action='$_SERVER[REQUEST_URI]' method="post">
    <table class="datatable">
        <thead>
            <tr>
                <th style="width: 10%;">{$lang->user} <a href="{$sort_url}&amp;sortby=displayName&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=displayName&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                <th style="width: 15%;">{$lang->chemicalname} <a href="{$sort_url}&amp;sortby=chemicalname&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=chemicalname&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                <th style="width: 25%;">{$lang->requestdescription}</th>
                <th style="width: 15%;">{$lang->application} <a href="{$sort_url}&amp;sortby=application&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=application&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                <th style="width: 15%;">{$lang->origin}</th>
                <th style="width: 15%;">{$lang->time} <a href="{$sort_url}&amp;sortby=timeRequested&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=timeRequested&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                <th style="width: 5%;">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            {$chemcialsrequests_rows}
        </tbody>

    </table>
</form>
<div style="width:40%; float:left; margin-top:0px;">
    <form method='post' action='$_SERVER[REQUEST_URI]'>
        {$lang->perlist}:
        <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
    </form>
</div>
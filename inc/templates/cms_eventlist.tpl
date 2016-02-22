<h3>{$lang->eventlist}</h3>
<form action='index.php?module=cms/eventlist' method="post">
    <table class="datatable" width="100%">
        <thead>
            <tr>
                <th width="30%">{$lang->eventtitle} <a href="{$sort_url}&amp;sortby=title&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=title&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                <th>{$lang->location}</th>
                <th>{$lang->fromdate}<a href="{$sort_url}&amp;sortby=fromDate&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=fromDate&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                <th>{$lang->todate}<a href="{$sort_url}&amp;sortby=toDate&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=toDate&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                <th width="2%">{$lang->published}</th>
            </tr>
            {$filters_row}
        </thead>
    </table>
    <table class="datatable" width="100%">

        <tbody>
            {$cms_events_list_rows}
        </tbody>
    </table>
</form>
<div style="width:40%; float:left; margin-top:20px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
    </form></div>
<div class="container">
    <h1>{$lang->listassetusers}</h1>
    <form action='$_SERVER[REQUEST_URI]' method="post">
        <table class="datatable">
            <thead>
                <tr>
                    <th style="width:20%">{$lang->assignedusers}</th>
                    <th style="width:25%;">{$lang->assets} <a href="{$sort_url}&amp;sortby=asid&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=asid&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                    <th style="width:25%;">{$lang->fromdate} <a href="{$sort_url}&amp;sortby=fromDate&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=fromDate&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                    <th style="width:25%;">{$lang->todate} <a href="{$sort_url}&amp;sortby=toDate&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=toDate&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                    <th>&nbsp;</th>
                </tr>
                {$filters_row}
            </thead>
            <tbody>
                {$assignee_list}
            </tbody>

        </table>
    </form>
    <div style="width:40%; float:left; margin-top:0px;">
        <form method='post' action='$_SERVER[REQUEST_URI]'>
            {$lang->perlist}:
            <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
        </form>
    </div>
</div>
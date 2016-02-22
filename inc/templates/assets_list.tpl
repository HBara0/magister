<h1>{$lang->listasset}</h1>
<form action='$_SERVER[REQUEST_URI]' method="post">
    <table class="datatable">
        <thead>
            <tr>
                <th style="width:15%">{$lang->tag} <a href="{$sort_url}&amp;sortby=tag&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=tag&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                <th style="width:25%">{$lang->title} <a href="{$sort_url}&amp;sortby=title&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=title&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                <th style="width:15%;">{$lang->affiliate}</th>
                <th style="width:10%;">{$lang->type} <a href="{$sort_url}&amp;sortby=title&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=title&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                <th style="width:15%;">{$lang->status} <a href="{$sort_url}&amp;sortby=status&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=status&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a> </th>
                <th style="width:15%;">{$lang->createdon} <a href="{$sort_url}&amp;sortby=createdon&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=createdon&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a> </th>
                <th>&nbsp;</th>
            </tr>
            {$filters_row}
        </thead>
        <tbody>
            {$assets_listrow}
        </tbody>

    </table>
</form>
<div style="width:40%; float:left; margin-top:0px;">
    <form method='post' action='$_SERVER[REQUEST_URI]'>
        {$lang->perlist}:
        <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
    </form>
</div>
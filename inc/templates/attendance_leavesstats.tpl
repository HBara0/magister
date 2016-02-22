<div class="container">
    <h1>{$lang->leavesstats}</h1>
    <div style="display:inline-block; width: 40%;">{$lang->leavetype}: {$types_list}</div><div style="display:inline-block; width: 40%; float:right; text-align:right;"><a href="index.php?module=attendance/addadditionaldays"><img src="images/addnew.png" border='0' alt="{$lang->additionaldays}"> {$lang->addadditionalbalance}</a></div>
    <table class="datatable">
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th style="width:11%">{$lang->period}</th>
                <th style="width:10%;">{$lang->entitledfor} <a href="{$sort_url}&amp;sortby=entitledfor&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=entitledfor&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                <th style="width:14%;">{$lang->balanceprevyear} <a href="{$sort_url}&amp;sortby=remainprevyear&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=remainprevyear&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                <th style="width:11%;">{$lang->cantake} <a href="{$sort_url}&amp;sortby=cantake&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=cantake&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                <th style="width:9%;">{$lang->daystaken} <a href="{$sort_url}&amp;sortby=daystaken&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=daystaken&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                <th style="width:8%;">{$lang->balance}</th>
                <th style="width:11%;">{$lang->additionaldays} <a href="{$sort_url}&amp;sortby=additionaldays&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=additionaldays&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                <th style="width:8%;">{$lang->finalbalance}</th>
            </tr>
        </thead>
        <tbody>
            {$statslist}
        </tbody>
    </table>
</div>
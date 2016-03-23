<h1>{$lang->budgetfxlist}</h1>
{$create_tool}
<form action='index.php?module=budgeting/listfxrates' method="post">
    <table class="datatable" width="100%">
        <thead>
            <tr>
                <th style="width:15%;">{$lang->affiliate} <a href="{$sort_url}&amp;sortby=affid&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=affid&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
                <th style="width:5%;">{$lang->year}</th>
                <th style="width:20%;">{$lang->fromcurr}</th>
                <th style="width:20%;">{$lang->tocurr}</th>
                <th style="width:10%;">{$lang->rate}</th>
                <th style="width:20%;">{$lang->category}</th>
            </tr>
            <tr>
                {$filters_row}
            </tr>
        </thead>
        <tbody class="datatable datacell">
            {$budgetfxratess_list}
        </tbody>
</form>
</table>
{$popupcreaterate}
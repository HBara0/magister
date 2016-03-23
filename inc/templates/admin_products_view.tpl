<h1>{$lang->listavailableproducts}</h1>
<form method='post' action='$_SERVER[REQUEST_URI]'>
    <table class="datatable">
        <thead>
            <tr>
                <th>{$lang->id}</th><th>{$lang->name} <a href="{$sort_url}&amp;sortby=name&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=name&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th><th>{$lang->generic}  <a href="{$sort_url}&amp;sortby=generic&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=generic&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th><th>{$lang->segment} <a href="{$sort_url}&amp;sortby=segment&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=segment&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th><th>{$lang->supplier} <a href="{$sort_url}&amp;sortby=supplier&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=supplier&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th><th>&nbsp;</th>
            </tr>
            <tr>
                {$filters_row}
            </tr>
        </thead>
        <tbody>
            {$products_list}
        </tbody>
    </table>
</form>
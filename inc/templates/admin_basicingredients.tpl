<h1>{$lang->managebasicingredients}</h1>
<table class="datatable">
    <div style="float:right;" class="subtitle"> <a href="#" id="showpopup_createbasicingredient" class="showpopup"><img src="{$core->settings[rootdir]}/images/addnew.png" border="0">{$lang->createbasicingredient}</a></div>
    <thead>
        <tr>
            <th>{$lang->title} <a href="{$sort_url}&amp;sortby=title&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=title&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
            <th>{$lang->description}</th>
        </tr>
        <tr>
            {$filters_row}
        </tr>
    </thead>

    <tbody>
        {$basicingredients_list}
    </tbody>
</table>
{$popup_createbasicingredient}
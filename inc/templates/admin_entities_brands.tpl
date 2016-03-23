<h1>{$lang->managebrands}</h1>
<table class="datatable">
    <div style="float:right;" class="subtitle"> <a href="#" id="showpopup_createbrand" class="showpopup"><img src="{$core->settings[rootdir]}/images/addnew.png" border="0">{$lang->createbrand}</a></div>
    <thead>
        <tr>
            <th>{$lang->name} <a href="{$sort_url}&amp;sortby=name&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=name&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
            <th>{$lang->entity}</th>
        </tr>
    </thead>
    <tbody>
        {$entitybrands_list}
    </tbody>
    <tr>
        <td>
            <div style="width:40%; float:left; margin-top:0px;">
                <form method='post' action='$_SERVER[REQUEST_URI]'>
                    {$lang->perlist}:
                    <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
                </form>
            </div>
        </td>
    </tr>
</table>

{$popup_createbrand}

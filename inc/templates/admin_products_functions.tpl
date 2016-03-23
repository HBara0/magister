<h1>{$lang->listavailablefunctions}</h1>
<div style="float:right;" class="subtitle"><a href="#" id="showpopup_cretefunction" class="showpopup"><img src="{$core->settings[rootdir]}/images/addnew.png" border="0">{$lang->create}</a></div>
<table class="datatable">
    <thead>
        <tr>
            <th>#</th>
            <th>{$lang->name} <a href="{$sort_url}&amp;sortby=name&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=name&amp;order=DESC"><img src="../images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
            <th>{$lang->title} <a href="{$sort_url}&amp;sortby=title&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=title&amp;order=DESC"><img src="../images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
            <th>{$lang->appsegment}</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        {$productsapplicationsfunctions_list}
    </tbody>
    <tr>
        <td colspan="3">
            <div style="width:40%; float:left; margin-top:0px;">
                <form method='post' action='$_SERVER[REQUEST_URI]'>
                    {$lang->perlist}:
                    <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
                </form>
            </div>
        </td>
    </tr>
</table>
</form>
{$popup_createfunction}
{$popup_applicationdescription}
{$popup_deleteappfunc}

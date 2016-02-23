<h1>{$lang->listofavailablefiles}</h1>
<table class="datatable">
    <thead>
        <tr>
            <th>{$lang->title}<a href="{$sort_url}&amp;sortby=title&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=title&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
            <th>{$lang->description}<a href="{$sort_url}&amp;sortby=description&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=description&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
            <th>{$lang->category}<a href="{$sort_url}&amp;sortby=category&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=category&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
            <th>{$lang->relatedto}<a href="{$sort_url}&amp;sortby=referenceId&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=reference&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        {$files_list}
    </tbody>
</table>
<form method='post' action='$_SERVER[REQUEST_URI]'> {$lang->perlist}<input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form>
<div><hr></div>
<iframe id='uploadFrame' name='uploadFrame' src='#' style="display:none; margin:0px;"></iframe>
<h1>{$lang->addnewfile}</h1>
<form  action="index.php?module=entities/manageentitiesfiles&amp;action=do_uploadfile" method="post" enctype="multipart/form-data" target="uploadFrame">
    {$addedit_form}
    <tr>
        <td>&nbsp;</td>
        <td colspan="3"><input type="submit" class='button' value="{$lang->savecaps}" onClick="$('#upload_Result').show()"><br />
            <div id="upload_Result" style="display:none;"><img src="{$core->settings[rootdir]}/images/loading.gif" /> {$lang->uploadinprogress}</div>
        </td>
    </tr>
</table>
</form>
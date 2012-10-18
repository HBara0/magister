<div id="popup_updateentitiesfiles" title="{$lang->updatefile}">
<iframe id='updateUploadFrame' name='updateUploadFrame' src='#' style="display:none;" ></iframe>
<form  action="index.php?module=entities/manageentitiesfiles&amp;action=do_update" method="post" enctype="multipart/form-data" target="updateUploadFrame" >
    <input type="hidden" id="fid" name="fid" value="{$core->input[id]}" />
    {$lang->selectfile}: <input type="file" id="updatefile" name="updatefile" /><br />
    <strong>{$lang->changes}</strong><br />
    <textarea name="changes" id="changes" cols="40" rows="5" ></textarea>
    <hr />
    <input type='submit' class='button' value='{$lang->savecaps}' onClick="$('#updateUpload_Result').show()"  />
</form>
<div id="updateUpload_Result" style="display:none;"><img src="{$core->settings[rootdir]}/images/loading.gif" /> {$lang->uploadinprogress}</div>
</div>
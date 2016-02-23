<link href='{$core->settings[rootdir]}/css/jqueryuitheme/jquery-ui-1.7.2.custom.css' rel='stylesheet' type='text/css' />

<iframe id='uploadFrame' name='uploadFrame' src='#' style="display:none;"></iframe>
<h1>{$lang->shareafile}</h1>
<form  name='upload_Form' id="upload_Form" action="index.php?module=filesharing/uploadfile&amp;action=do_uploadfile" method="post" enctype="multipart/form-data" target="uploadFrame">
    <table>
        <tr>
            <td>{$lang->selectfile}</td>
            <td colspan="3"><input type="file" id="uploadfile[]" name="uploadfile[]" multiple="true" required="required" ></td>

        </tr>
        <tr>
            <td>{$lang->title}</td>
            <td colspan="3"><input name='title' id='title' type="text" maxlength="220" autocomplete='off' required="required"/></td>
        </tr>
        <tr>
            <td>{$lang->category}</td>
            <td>{$categories_list}</td>
            <td>{$lang->folder}</td>
            <td>{$folders_list} <a href="#" id="showpopup_createfolder" class ="showpopup"><img src="images/addnew.png" alt="{$lang->createfolder}" border='0' /></a></td>
        </tr>
        <tr>
            <td>{$lang->description}</td>
            <td colspan="3"><textarea name="description" id="description" cols="30" rows="10" ></textarea></td>
        </tr>
        <tr>
            <td>{$lang->preventaccess}</td>
            <td>{$employees_preventaccess_list}<br /><span class="smalltext" style="color:#666666;">{$lang->preventaccessnote}</span></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan="3"><input type="submit" class='button' value="{$lang->share}" onClick="$('#upload_Result').show()">
                <br />
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan="3"><div id="upload_Result" style="display:none;"><img src="{$core->settings[rootdir]}/images/loading.gif" /> {$lang->uploadinprogress}</div></td>
        </tr>
    </table>
</form>
<div id="popup_createfolder" title="{$lang->createfolder}" style="display:none;">
    <form name='perform_filesharing/fileslist_Form' id="perform_filesharing/fileslist_Form" method="post">
        <input type="hidden" id="action" name="action" value="createfolder" />
        <table>
            <tr>
                <td>{$lang->title}</td><td><input name='name' id='name' type="text" maxlength="220" autocomplete='off' required="required"/></td>

            </tr>
            <tr>
                <td>{$lang->parentfolder}</td><td>{$folders_list}</td>
            </tr>
            <tr>
                <td>{$lang->description}</td>
                <td><textarea name="description" id="description" cols="30" rows="10" ></textarea></td>
            </tr>
            <tr>
                <th>{$lang->preventreading}</th>
                <th>{$lang->preventwriting}</th>
            </tr>
            <tr>
                <td><input type="checkbox" value="1" id="noReadPermissionsLater" name="noReadPermissionsLater" checked="checked">{$lang->preventallnewusers}<br />{$employees_preventread_list}</td>
                <td align="center"><input type="checkbox" value="1" id="noWritePermissionsLater" name="noWritePermissionsLater" checked="checked">{$lang->preventallnewusers}<br />{$employees_preventwrite_list}</td>
            </tr>
            <tr>
                <td>&nbsp;</td><td><input type='submit' class='button' value='{$lang->savecaps}' id='perform_filesharing/fileslist_Button' /></td>
            </tr>
        </table>

    </form>
    <hr />
    <div id="perform_filesharing/fileslist_Results" ></div>
</div>

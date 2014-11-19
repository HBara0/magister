<div id="popup_deletefilebox" title="{$lang->suredeletefile}">
    <form name='change_filesharing/fileslist_Form' id="change_filesharing/fileslist_Form" method="post" >
        <input type="hidden" id="action" name="action" value="do_deletefile" />
        <input type="hidden" id="fid" name="fid" value="{$core->input[id]}" />
        <table width="100%">
            <tr>
                <td>{$lang->suredeletefile} "{$file[title]}"?</td>
            </tr>
            <tr>
                <td><hr /> <input type='button' id='change_filesharing/fileslist_Button' value='{$lang->yes}' class='button'/></td>
            </tr>
        </table>
    </form>
    <div id="change_filesharing/fileslist_Results" ></div>
</div>
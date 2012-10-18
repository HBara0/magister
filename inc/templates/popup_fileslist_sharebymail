<div id="popup_sharebymail" title="{$lang->sharebymail}">
	{$lang->youaretoshare} $file[name] <i class="smalltext">($file[size])</i>
    <form name='change_filesharing/fileslist_Form' id="change_filesharing/fileslist_Form" method="post">
        <input type="hidden" id="action" name="action" value="do_sendbymail" />
        <input type="hidden" id="id" name="id" value="{$core->input[id]}" />
        <input type="hidden" id="attachment" name="attachment" value="{$attachment_path}" />
        <input type="hidden" id="title" name="title" value="{$file[title]}" />
        <input type="hidden" id="sendingobject" name="sendingobject" value="file" />
        {$lang->affiliates}<br />
        {$affiliates_list}<br /><a href="#customizemessage" onClick="$('#customizemessage').show();" class="smalltext"><img src="images/edit.gif" border="0" alt="{$lang->customizemessage}">{$lang->customizemessage}</a><br />
        <div id="customizemessage" style="display:none;">
        	<hr />
            <strong>{$lang->subject}:</strong> <input type="text" size="50" maxlength="70" name="subject" id="subject" value="{$lang->filesharing_sharesubject}"/><br />
            <strong>{$lang->message}:</strong><br />
            <textarea cols="50" rows="10" name="message" id="message">{$lang->filesharing_sharemessage}</textarea>
        </div>
        <br />
        <input type='button' class='button' value='{$lang->sharebymail}' id='change_filesharing/fileslist_Button' />
    </form>
    <hr />
    <div id="change_filesharing/fileslist_Results" ></div>
</div>
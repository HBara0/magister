    <div id='popup_changeprofilepic' title="{$lang->changeprofilepic}">
    	 <iframe id='uploadFrame' name='uploadFrame' src='#' style="display:none; margin:0px;"></iframe>
		<p>
    	 <form action="users.php?action=admin_do_changeprofilepic" method="post" enctype="multipart/form-data" target="uploadFrame">
  
         	 {$lang->selectprofilepicture} 
                 <input type="file" id="uploadfile" name="uploadfile"><br />
                 <input type="hidden" value="{$profile[uid]}" name="profile[uid]" />
             <div style="font-style:italic; margin: 5px;">{$lang->onlyfiletypesallowed}</div>
          	<input type="submit" class='button' value="{$lang->savecaps}" onClick="$('#upload_Result').show();">
         </form>
        </p>
         <div id="upload_Result" style="display:none;"><img src="{$core->settings[rootdir]}/images/loading.gif" /> {$lang->uploadinprogress}</div>
    </div>
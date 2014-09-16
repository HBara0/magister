<hr />
<div class="subtitle">{$lang->createrechange}</div>
<div>
    <form id='add_development/viewrequirement_Form' name='add_development/viewrequirement_Form' action='#' method='post'>
        <input type="hidden" value='{$requirement[drid]}' name="drid">
        <input type="hidden" value='createreqchange' name="action">
        <strong>{$lang->title}</strong><br />
        <input type="text" name='title' size="100"><br />
        <strong>{$lang->description}</strong><br />
        <textarea cols="50" rows='10' name="description"></textarea><br />
        {$lang->requestedby} <input type='text' id='user_1_autocomplete' value="{$user[requestedBy_name]}"/><input type="text" size="3" id="user_1_id_output" value="{$user[requestedBy]}" disabled/><input type='hidden' id='user_1_id' name='requestedBy' value="{$user[requestedBy]}" /><div id='searchQuickResults_1' class='searchQuickResults' style='display:none;'></div> <input type="text" id="pickDate_dateRequested" autocomplete="off" tabindex="2" value="{$user[dateRequested_output]}" /><input type="hidden" name="dateRequested" id="altpickDate_dateRequested" value="{$user[dateRequested_formatted]}"/> 
        <br />
        <!--{$lang->approvedby} <input type='text' id='user_noexception_2_autocomplete' value="{$user[approvedBy_name]}"/><input type="text" size="3" id="user_noexception_2_id_output" value="{$user[approvedBy]}" disabled/><input type='hidden' id='user_noexception_2_id' name='approvedBy' value="{$user[approvedBy]}" /><div id='searchQuickResults_2_noexception' class='searchQuickResults' style='display:none;'></div>!-->
        <hr />
        <input type='button' id='add_development/viewrequirement_Button' value='{$lang->savecaps}' class='button'/>
        <div id='add_development/viewrequirement_Results'></div>
    </form>
</div>
<div id="popup_deleterepresentative" title="{$lang->deleterepresentative}">
    <form name='perform_profiles/representativeslist_Form' id="perform_profiles/representativeslist_Form" method="post">
        <input type="hidden" id="action" name="action" value="do_delete" />
        <input type="hidden" id="rpid" name="rpid" value="{$rpid}" />
        {$deletemessage}
        <hr />
        <input type='button' id='perform_profiles/representativeslist_Button' value='{$lang->yes}' class='button'/>
    </form>
    <div id="perform_profiles/representativeslist_Results" ></div>
</div>
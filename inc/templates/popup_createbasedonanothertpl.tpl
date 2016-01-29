<div id="popup_createbasedonanothertpl" title="{$lang->createbasedonanother}">
    <form action="#" method="post" id="perform_surveys/createsurveytemplate_Form" name="perform_products/functions_Form">
        <input type="hidden" name="action" value="createbasedonanother" />
        <input type="hidden" name="safid" value="{$safid}" />

        <div>
            <h2>{$lang->template}</h2>
        </div>
        {$surveytemplates_list}
        <input type='button' id='test' value='{$lang->loadquestions}' class='button'/>

    </form>
</div>
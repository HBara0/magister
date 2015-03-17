<div id="popup_applicationdescription"  title="{$lang->description}">
    <form action="#" method="post" id="perform_products/functions_Form" name="perform_products/functions_Form">
        <input type="hidden" name="action" value="save_descr" />
        <input type='hidden' name='segfuncapp' value='{$safid}'/>
        <textarea  rows="5" cols="60" class="redactor_editor" name="segapdescription">{$segapdescriptions}</textarea>
        <hr />
        <input type='button' id='perform_products/functions_Button' value='{$lang->savecaps}' class='button'/>
        <div id="perform_products/functions_Results"></div>
    </form>
</div>